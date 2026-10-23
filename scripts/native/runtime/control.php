<?php
/*
 * Local control endpoint for process-less runtimes (the iOS app; also usable natively).
 * Served by scripts/native/router.php at /__openvillage/<action>, loopback only, and – when the
 * OV_CONTROL_TOKEN environment variable is set – only with a matching "X-OV-Token" header.
 *
 *   GET  tick    run every due automation job once (replaces the forked Automation.php daemons)
 *   POST reset   drop and recreate the game, global and cache databases from the schema files
 *   POST setup   create the world on an empty database (= docker/mariadb/init.sh + install.php)
 *   POST sql     run SQL (form fields: query, db=game|global) and return the result sets as JSON
 *   GET  status  installed flag, counts, versions
 *   POST flush   clear the cache table used by the Redis replacement
 */

$ovAction = $GLOBALS['ovControlAction'] ?? '';
$ovRoot = dirname(__DIR__, 3);

function ov_json(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR);
}

function ov_env(string $name, string $default = ''): string
{
    $v = getenv($name);
    return $v === false || $v === '' ? $default : $v;
}

function ov_pdo(?string $database = null): PDO
{
    $dsn = sprintf('mysql:host=%s;charset=utf8mb4', ov_env('GAME_DB_HOST', 'localhost'));
    if ($database !== null) {
        $dsn .= ';dbname=' . $database;
    }
    return new PDO($dsn, ov_env('GAME_DB_USER', 'openvillage'), ov_env('GAME_DB_PASSWORD', 'local-game-password'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

// ---- access control -------------------------------------------------------------------------
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, ['127.0.0.1', '::1'], true)) {
    ov_json(['error' => 'forbidden'], 403);
    return;
}
$token = getenv('OV_CONTROL_TOKEN');
if ($token !== false && $token !== '' && !hash_equals($token, (string)($_SERVER['HTTP_X_OV_TOKEN'] ?? ''))) {
    ov_json(['error' => 'bad token'], 403);
    return;
}
set_time_limit(0);
ignore_user_abort(true);

switch ($ovAction) {
    // ------------------------------------------------------------------------------------------
    case 'reset':
        $started = microtime(true);
        $schemas = [
            ov_env('GLOBAL_DB_NAME', 'openvillage_global') => $ovRoot . '/data/main.sql',
            ov_env('GAME_DB_NAME', 'openvillage_game') => $ovRoot . '/main_script/include/schema/T4.4.sql',
            ov_env('OV_KV_DB', 'openvillage_cache') => null,
        ];
        mysqli_report(MYSQLI_REPORT_OFF);
        $m = @new mysqli(ov_env('GAME_DB_HOST', 'localhost'), ov_env('GAME_DB_USER', 'openvillage'), ov_env('GAME_DB_PASSWORD', 'local-game-password'));
        if ($m->connect_errno) {
            ov_json(['ok' => false, 'error' => $m->connect_error], 500);
            return;
        }
        $m->set_charset('utf8mb4');
        $log = [];
        foreach ($schemas as $database => $file) {
            if (!preg_match('/^[A-Za-z0-9_]+$/', $database)) {
                ov_json(['ok' => false, 'error' => "bad database name $database"], 500);
                return;
            }
            $ok = $m->query("DROP DATABASE IF EXISTS `$database`")
                && $m->query("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")
                && $m->select_db($database);
            if ($ok && $file !== null) {
                $sql = (string)file_get_contents($file);
                if ($m->multi_query($sql)) {
                    do {
                        if ($r = $m->store_result()) {
                            $r->free();
                        }
                    } while ($m->more_results() && $m->next_result());
                }
                $ok = $m->errno === 0;
            }
            if (!$ok) {
                ov_json(['ok' => false, 'error' => "$database: " . $m->error, 'log' => $log], 500);
                return;
            }
            $log[] = "$database recreated";
        }
        foreach (glob(dirname(__DIR__, 3) . '/main_script/copyable/include/runtime/*.{json,log}', GLOB_BRACE) ?: [] as $f) {
            @unlink($f);
        }
        ov_json(['ok' => true, 'log' => $log, 'ms' => (int)((microtime(true) - $started) * 1000)]);
        return;

    // ------------------------------------------------------------------------------------------
    case 'setup':
        $started = microtime(true);
        $game = ov_env('GAME_DB_NAME', 'openvillage_game');
        $global = ov_env('GLOBAL_DB_NAME', 'openvillage_global');
        $mapSize = max(10, (int)ov_env('GAME_MAP_SIZE', '25'));
        $speed = max(1, (int)ov_env('GAME_SPEED', '10'));
        $log = [];
        try {
            $pdo = ov_pdo($game);
            if ((int)$pdo->query('SELECT COUNT(*) FROM config')->fetchColumn() === 0) {
                $pdo->exec("INSERT INTO config (startTime, map_size, worldUniqueId, installed, loginInfoTitle, loginInfoHTML, message)
                            VALUES (UNIX_TIMESTAMP(), $mapSize, 1, 0, '', '', '')");
                ov_pdo($global)->exec("UPDATE gameServers SET speed=$speed, startTime=UNIX_TIMESTAMP() WHERE id=1");
                $log[] = 'config row created';
            }
            // The rest is install.php (which only runs from the CLI) executed in-process.
            define('IS_INSTALLER', true);
            require $ovRoot . '/main_script/copyable/include/env.php';
            require $ovRoot . '/main_script/include/bootstrap.php';
            require $ovRoot . '/main_script/copyable/include/migrate.php';
            ob_start();
            run_schema_migrations();
            run_global_schema_migrations();
            $log[] = trim((string)ob_get_clean());
            mt_srand(make_seed());
            $installed = (bool)\Core\Config::getInstance()->dynamic->installed;
            if (!$installed) {
                $password = ov_env('INITIAL_ADMIN_PASSWORD');
                if ($password === '') {
                    throw new RuntimeException('INITIAL_ADMIN_PASSWORD is required');
                }
                ini_set('memory_limit', '-1');
                $installer = new \Model\InstallerModel();
                $installer->mapToArray();
                $installer->createRestOfTheMap();
                $installer->pushMapToDB();
                $installer->finalize($password);
                \Core\Database\DB::getInstance()->query('UPDATE odata SET lasttrain=' . \Core\Config::getProperty('game', 'start_time'));
                $log[] = 'world installed';
            } else {
                $log[] = 'already installed';
            }
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            ov_json(['ok' => true, 'installed' => true, 'log' => $log, 'ms' => (int)((microtime(true) - $started) * 1000)]);
        } catch (Throwable $e) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            ov_json(['ok' => false, 'error' => get_class($e) . ': ' . $e->getMessage(), 'at' => basename($e->getFile()) . ':' . $e->getLine(), 'log' => $log], 500);
        }
        return;

    // ------------------------------------------------------------------------------------------
    case 'tick':
        $started = microtime(true);
        require __DIR__ . '/TickJob.php';                       // must precede the autoloader
        require $ovRoot . '/main_script/copyable/include/env.php';
        require $ovRoot . '/main_script/include/bootstrap.php';
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        session_write_close();

        $stateFile = RUNTIME_PATH . '/tick-state.json';
        @mkdir(RUNTIME_PATH, 0775, true);
        $fh = fopen($stateFile, 'c+');
        if (!$fh || !flock($fh, LOCK_EX | LOCK_NB)) {
            ov_json(['ok' => true, 'busy' => true]);              // a previous tick is still running
            return;
        }
        $state = json_decode((string)stream_get_contents($fh), true) ?: [];
        $save = static function () use (&$state, $fh): void {
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, json_encode($state));
            fflush($fh);
        };
        $skip = array_filter(array_map('trim', explode(',', ov_env('OV_TICK_SKIP'))));

        \Core\Jobs\Launcher::lunchJobs();                         // registers the job tree (TickJob)
        $db = \Core\Database\DB::getInstance();
        $config = \Core\Config::getInstance();
        $errorHandler = \Core\ErrorHandler::getInstance();
        $now = microtime(true);
        $ran = [];
        $errors = [];
        $run = static function (string $label, callable $fn) use (&$ran, &$errors, $errorHandler): void {
            $t = microtime(true);
            try {
                $fn();
                $ran[] = $label . ' ' . round((microtime(true) - $t) * 1000) . 'ms';
            } catch (Throwable $e) {
                $errors[] = $label . ': ' . $e->getMessage();
                $errorHandler->handleExceptions($e);
            }
        };

        foreach (\Core\Jobs\Job::$daemons as $i => $daemon) {
            $key = $i . ':' . $daemon->name;
            $interval = max(0, (float)$daemon->interval);
            if ($interval > 0 && $now - ($state[$key] ?? 0) < $interval) {
                continue;
            }
            $state[$key] = $now;
            $save();
            mt_srand(make_seed());
            $config->dynamic = (object)$db->query('SELECT * FROM config LIMIT 1')->fetch_assoc();
            if ($config->dynamic->needsRestart == 1) {
                $db->query('UPDATE config SET needsRestart=0');
            }
            $exclude = $daemon->name === 'postService' && $config->dynamic->postServiceDone == 0;
            if ($config->dynamic->finishStatusSet && !$exclude) {
                continue;
            }
            if (!($config->dynamic->automationState || $exclude)) {
                continue;
            }
            if (!$db->checkConnection()) {
                continue;
            }
            $GLOBALS['prgName'] = $daemon->name;
            $children = $daemon->children();
            if ($children === null) {
                if (!in_array($daemon->name, $skip, true)) {
                    $run($daemon->name, $daemon->callback);
                }
                continue;
            }
            foreach ($children as $j => $child) {
                if (!$child instanceof \Core\Jobs\Job || in_array($child->name, $skip, true)) {
                    continue;
                }
                $childKey = $key . '/' . $j . ':' . $child->name;
                if (!isset($state[$childKey])) {
                    $state[$childKey] = $now;                    // like Job::$lastReload: first run after one interval
                    continue;
                }
                if ($now - $state[$childKey] < (float)$child->interval) {
                    continue;
                }
                $state[$childKey] = $now;
                $save();
                $GLOBALS['prgName'] = $child->name;
                $run($child->name, $child->callback);
            }
        }
        $save();
        flock($fh, LOCK_UN);
        fclose($fh);
        ov_json(['ok' => true, 'ran' => $ran, 'errors' => $errors, 'ms' => (int)((microtime(true) - $started) * 1000)]);
        return;

    // ------------------------------------------------------------------------------------------
    case 'sql':
        $query = (string)($_POST['query'] ?? '');
        $database = ($_POST['db'] ?? 'game') === 'global' ? ov_env('GLOBAL_DB_NAME', 'openvillage_global') : ov_env('GAME_DB_NAME', 'openvillage_game');
        if (trim($query) === '') {
            ov_json(['ok' => false, 'error' => 'empty query'], 400);
            return;
        }
        mysqli_report(MYSQLI_REPORT_OFF);
        $started = microtime(true);
        $m = @new mysqli(ov_env('GAME_DB_HOST', 'localhost'), ov_env('GAME_DB_USER', 'openvillage'), ov_env('GAME_DB_PASSWORD', 'local-game-password'), $database);
        if ($m->connect_errno) {
            ov_json(['ok' => false, 'error' => $m->connect_error], 500);
            return;
        }
        $m->set_charset('utf8mb4');
        $sets = [];
        $affected = 0;
        $error = null;
        if ($m->multi_query($query)) {
            do {
                if ($res = $m->store_result()) {
                    $columns = array_map(fn ($f) => $f->name, $res->fetch_fields());
                    $rows = [];
                    while (($row = $res->fetch_row()) && count($rows) < 5000) {
                        $rows[] = array_map(fn ($v) => $v === null ? 'NULL' : (string)$v, $row);
                    }
                    $sets[] = ['columns' => $columns, 'rows' => $rows, 'total' => $res->num_rows];
                    $res->free();
                } elseif ($m->affected_rows > 0) {
                    $affected += $m->affected_rows;
                }
            } while ($m->more_results() && $m->next_result());
        }
        if ($m->errno) {
            $error = $m->error;
        }
        ov_json(['ok' => $error === null, 'error' => $error, 'sets' => $sets, 'affected' => $affected,
                 'ms' => (int)((microtime(true) - $started) * 1000)], $error === null ? 200 : 400);
        return;

    // ------------------------------------------------------------------------------------------
    case 'status':
        try {
            $pdo = ov_pdo(ov_env('GAME_DB_NAME', 'openvillage_game'));
            $installed = (int)($pdo->query('SELECT installed FROM config LIMIT 1')->fetchColumn() ?: 0);
            $players = $installed ? (int)$pdo->query('SELECT COUNT(*) FROM users WHERE id > 2')->fetchColumn() : 0;
            ov_json([
                'ok' => true,
                'installed' => $installed === 1,
                'players' => $players,
                'php' => PHP_VERSION,
                'mariadb' => (string)$pdo->query('SELECT VERSION()')->fetchColumn(),
                'time' => time(),
            ]);
        } catch (Throwable $e) {
            ov_json(['ok' => false, 'installed' => false, 'error' => $e->getMessage()], 503);
        }
        return;

    // ------------------------------------------------------------------------------------------
    case 'flush':
        try {
            $redis = new Redis();
            $redis->connect(ov_env('REDIS_HOST', '127.0.0.1'), (int)ov_env('REDIS_PORT', '6379'));
            ov_json(['ok' => (bool)$redis->flushAll()]);
        } catch (Throwable $e) {
            ov_json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
        return;

    default:
        ov_json(['error' => 'unknown action'], 404);
}
