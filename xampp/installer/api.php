<?php
/*
 * Back end of the installation wizard (xampp/installer/index.php), served at /install/api by xampp/router.php
 * until the world is installed. Loopback only.
 *
 *   GET  ?action=check     requirements
 *   POST action=testdb     connect to MariaDB/MySQL with the given administrator account
 *   POST action=configure  create the game's database user and databases, write config.local.php
 *   POST action=finish     mark the world installed (after /__openvillage/reset and /__openvillage/setup)
 *
 * Creating the world itself is done by the browser through the control endpoint
 * (scripts/native/runtime/control.php), with the token returned by "configure".
 */

declare(strict_types=1);

const OV_DATABASES = ['openvillage_game', 'openvillage_global', 'openvillage_cache'];
const OV_DB_USER = 'openvillage';

function ov_reply(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function ov_random(int $length): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
    $out = '';
    for ($i = 0; $i < $length; $i++) {
        $out .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    return $out;
}

/** Administrator connection from the form fields; throws mysqli_sql_exception. */
function ov_admin_connection(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $host = trim((string)($_POST['db_host'] ?? '127.0.0.1')) ?: '127.0.0.1';
    // Port 3306 only: the game's connection settings have no port (docker/native use the default too).
    $db = new mysqli($host, (string)($_POST['db_user'] ?? 'root'), (string)($_POST['db_password'] ?? ''), '', 3306);
    $db->set_charset('utf8mb4');
    return $db;
}

/** The game's database user with all rights on openvillage_* databases. */
function ov_create_game_account(mysqli $db, string $password): void
{
    foreach (['localhost', '127.0.0.1', '::1'] as $from) {
        $account = "'" . OV_DB_USER . "'@'$from'";
        $db->query("CREATE USER IF NOT EXISTS $account IDENTIFIED BY '$password'");
        $db->query("ALTER USER $account IDENTIFIED BY '$password'");
        $db->query("GRANT ALL PRIVILEGES ON `openvillage\\_%`.* TO $account");
    }
    $db->query('FLUSH PRIVILEGES');
}

/** Errors of privilege tables left by an older server version ("Incorrect file format 'tables_priv'", ...). */
function ov_is_grant_table_error(mysqli_sql_exception $e): bool
{
    return (bool)preg_match('/Incorrect (file format|information in file)|(tables|columns|procs|proxies|global)_priv|Column count of mysql\\.|'
        . 'needs to be upgraded|Cannot load from mysql\\./i', $e->getMessage());
}

/**
 * Repairs the privilege tables named in $error (all of them when none is named) with REPAIR TABLE, rebuilding
 * their index from the table definition (USE_FRM) when a plain repair fails – what an "Incorrect file format"
 * left by another server version needs. Returns one line per table for the wizard's log.
 */
function ov_repair_grant_tables(mysqli $db, string $error): array
{
    $tables = preg_match("/'(?:\\.\\/mysql\\/)?(\\w+?)(?:\\.frm)?'/", $error, $m) ? [$m[1]]
        : ['global_priv', 'user', 'db', 'tables_priv', 'columns_priv', 'procs_priv', 'proxies_priv', 'roles_mapping'];
    $log = [];
    foreach ($tables as $table) {
        if (!preg_match('/^\\w+$/', $table)) {
            continue;
        }
        foreach (['', ' USE_FRM'] as $mode) {
            try {
                $db->query("REPAIR TABLE mysql.`$table`$mode");
                $check = $db->query("CHECK TABLE mysql.`$table`")->fetch_all(MYSQLI_ASSOC);
                $status = (string)end($check)['Msg_text'];
            } catch (mysqli_sql_exception $e) {
                $status = $e->getMessage();
            }
            if ($status === 'OK') {
                break;
            }
        }
        $log[] = "mysql.$table: $status";
    }
    return $log;
}

function ov_existing_databases(mysqli $db): array
{
    $found = [];
    foreach (OV_DATABASES as $name) {
        $result = $db->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$name'");
        $tables = (int)$result->fetch_row()[0];
        if ($db->query("SHOW DATABASES LIKE '" . str_replace('_', '\\_', $name) . "'")->num_rows > 0) {
            $found[$name] = $tables;
        }
    }
    return $found;
}

function ov_write_config(string $file, array $config): void
{
    $php = "<?php\n// OpenVillage for XAMPP – written by the installation wizard. Delete this file to install again.\nreturn "
        . var_export($config, true) . ";\n";
    $temp = $file . '.' . ov_random(6) . '.tmp';
    if (file_put_contents($temp, $php, LOCK_EX) === false || !rename($temp, $file)) {
        @unlink($temp);
        throw new RuntimeException("Nelze zapsat $file");
    }
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($file, true);
    }
}

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    ov_reply(['ok' => false, 'error' => 'Instalace je dostupná jen z tohoto počítače.'], 403);
    return;
}

$root = dirname(__DIR__, 2);
$configFile = dirname(__DIR__) . '/config.local.php';
$action = (string)($_POST['action'] ?? $_GET['action'] ?? '');

try {
    switch ($action) {
        case 'check':
            $checks = [];
            $add = static function (string $name, bool $ok, string $detail, bool $required = true) use (&$checks): void {
                $checks[] = ['name' => $name, 'ok' => $ok, 'detail' => $detail, 'required' => $required];
            };
            $add('PHP 8.2 nebo novější', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION);
            foreach (['mysqli', 'pdo_mysql', 'mbstring', 'gd', 'iconv', 'ctype', 'json'] as $extension) {
                $add("Rozšíření PHP $extension", extension_loaded($extension), extension_loaded($extension) ? 'načteno' : 'chybí – povol ho v php.ini');
            }
            foreach ([dirname(__DIR__), $root . '/main_script/copyable/include'] as $dir) {
                $add('Zápis do ' . str_replace($root . DIRECTORY_SEPARATOR, '', $dir), is_writable($dir), is_writable($dir) ? 'ano' : 'nelze zapisovat');
            }
            $socket = @fsockopen('127.0.0.1', 3306, $errno, $error, 1);
            $add('MariaDB / MySQL na portu 3306', $socket !== false,
                $socket !== false ? 'běží' : 'neběží – spusť MySQL v XAMPP Control Panelu', false);
            if ($socket !== false) {
                fclose($socket);
            }
            $add('Paměť pro PHP', true, (string)ini_get('memory_limit'), false);
            $styles = (require $root . '/sections/gpack/gpack.php')['styles'];
            ov_reply([
                'ok' => true,
                'checks' => $checks,
                'passed' => !array_filter($checks, static fn ($c) => $c['required'] && !$c['ok']),
                'gpacks' => array_map(static fn ($s) => ['hash' => $s['hash'], 'name' => $s['name']], array_values($styles)),
                'timezone' => date_default_timezone_get(),
            ]);
            return;

        case 'testdb':
            $db = ov_admin_connection();
            $version = (string)$db->query('SELECT VERSION()')->fetch_row()[0];
            ov_reply(['ok' => true, 'version' => $version, 'existing' => ov_existing_databases($db)]);
            return;

        case 'configure':
            $speed = max(1, min(1000, (int)($_POST['speed'] ?? 10)));
            $mapSize = max(25, min(400, (int)($_POST['map_size'] ?? 50)));
            $roundDays = max(1, min(365, (int)($_POST['round_days'] ?? 35)));
            $heroSpeed = max(1, min(10, (float)($_POST['hero_speed'] ?? 1)));
            $title = trim((string)($_POST['title'] ?? '')) ?: 'OpenVillage 4.6';
            $serverName = trim((string)($_POST['server_name'] ?? '')) ?: 'Local World';
            $adminPassword = (string)($_POST['admin_password'] ?? '');
            $timezone = (string)($_POST['timezone'] ?? 'UTC');
            $gpack = (string)($_POST['gpack'] ?? '');
            if (strlen($adminPassword) < 6) {
                ov_reply(['ok' => false, 'error' => 'Heslo správce musí mít aspoň 6 znaků.'], 400);
                return;
            }
            if (!in_array($timezone, timezone_identifiers_list(), true)) {
                $timezone = 'UTC';
            }
            $styles = (require $root . '/sections/gpack/gpack.php')['styles'];
            if (!isset($styles[$gpack])) {
                $gpack = '';
            }

            $db = ov_admin_connection();
            $existing = array_filter(ov_existing_databases($db));
            if ($existing && ($_POST['overwrite'] ?? '') !== '1') {
                ov_reply(['ok' => false, 'error' => 'Databáze hry už existují: ' . implode(', ', array_keys($existing))
                    . '. Potvrď jejich přepsání.', 'existing' => $existing], 409);
                return;
            }
            // The game's own account, limited to its databases (the control endpoint drops and recreates them).
            $password = ov_random(32);
            $gameUser = OV_DB_USER;
            $notes = [];
            $host = trim((string)($_POST['db_host'] ?? '127.0.0.1')) ?: '127.0.0.1';
            try {
                ov_create_game_account($db, $password);
            } catch (mysqli_sql_exception $e) {
                if (!ov_is_grant_table_error($e)) {
                    throw $e;
                }
                // System tables of another MariaDB/MySQL version (e.g. a data folder kept from an older XAMPP).
                $notes[] = 'Systémové tabulky MariaDB jsou poškozené nebo z jiné verze (' . $e->getMessage() . '), opravuji je…';
                foreach (ov_repair_grant_tables($db, $e->getMessage()) as $line) {
                    $notes[] = '  ' . $line;
                }
                try {
                    ov_create_game_account($db, $password);
                    $notes[] = 'Systémové tabulky opraveny.';
                } catch (mysqli_sql_exception $e) {
                    if (!ov_is_grant_table_error($e)) {
                        throw $e;
                    }
                    if ((string)($_POST['db_password'] ?? '') === '') {
                        // The game's settings treat an empty password as unset, so a root without one cannot stand in.
                        ov_reply(['ok' => false, 'notes' => $notes, 'error' => 'Systémové tabulky MariaDB v XAMPPu jsou poškozené'
                            . ' nebo z jiné verze a automatická oprava se nepovedla (' . $e->getMessage() . ').' . "\n"
                            . 'Oprava: v XAMPP Control Panelu otevři Shell, spusť „mysql_upgrade -u root --force", pak MySQL zastav'
                            . ' a znovu spusť (Stop / Start) a instalaci zopakuj.'], 500);
                        return;
                    }
                    $gameUser = (string)$_POST['db_user'];
                    $password = (string)$_POST['db_password'];
                    $notes[] = 'Uživatele openvillage nešlo vytvořit, hra použije zadaný účet správce.';
                }
            }
            foreach (OV_DATABASES as $name) {
                $db->query("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }

            $env = [
                'GAME_DB_HOST' => $host,
                'GAME_DB_NAME' => 'openvillage_game',
                'GLOBAL_DB_NAME' => 'openvillage_global',
                'GAME_DB_USER' => $gameUser,
                'GAME_DB_PASSWORD' => $password,
                'APP_SECRET' => ov_random(48),
                'APP_TIMEZONE' => $timezone,
                'OV_CONTROL_TOKEN' => ov_random(32),
                'INITIAL_ADMIN_PASSWORD' => $adminPassword,
                'GAME_SPEED' => (string)$speed,
                'GAME_MAP_SIZE' => (string)$mapSize,
                'GAME_ROUND_LENGTH_DAYS' => (string)$roundDays,
                'GAME_HERO_SPEED' => (string)$heroSpeed,
                'GAME_TITLE' => $title,
                'GAME_SERVER_NAME' => $serverName,
            ];
            if ($gpack !== '') {
                $env['GAME_GPACK'] = $gpack;
            }
            ov_write_config($configFile, ['installed' => false, 'env' => $env]);
            ov_reply(['ok' => true, 'token' => $env['OV_CONTROL_TOKEN'], 'notes' => $notes]);
            return;

        case 'finish':
            $config = is_file($configFile) ? (include $configFile) : null;
            if (!is_array($config) || !hash_equals($config['env']['OV_CONTROL_TOKEN'], (string)($_POST['token'] ?? ''))) {
                ov_reply(['ok' => false, 'error' => 'Neplatný požadavek.'], 403);
                return;
            }
            $config['installed'] = true;
            $config['installedAt'] = date('c');
            ov_write_config($configFile, $config);
            ov_reply(['ok' => true]);
            return;

        default:
            ov_reply(['ok' => false, 'error' => 'Neznámá akce.'], 404);
    }
} catch (mysqli_sql_exception $e) {
    ov_reply(['ok' => false, 'error' => 'Databáze: ' . $e->getMessage()], 400);
} catch (Throwable $e) {
    ov_reply(['ok' => false, 'error' => $e->getMessage()], 500);
}
