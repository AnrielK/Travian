<?php
/*
 * Several game worlds side by side (native router: native runtime, XAMPP, iOS app).
 *
 * World 1 is the world of the installation: database GAME_DB_NAME, settings from the environment, pages at /game/.
 * Every further world is a row of gameServers in the global database (id = the world's worldUniqueId) with its
 * own game database (gameDb), look (gpack), speed, map size, round length and hero speed; its pages are at
 * /game<id>/ (/game2/, /game3/, ...). Before a request of such a world runs, ov_apply_world() puts its settings
 * into the environment the game reads (connection.php, gpack.php, HeroHelper) and puts them back afterwards.
 * The global database (accounts across worlds, gameServers) and the cache server are shared; sessions and cache
 * keys are already separated by worldUniqueId.
 */

declare(strict_types=1);

const OV_WORLD_ENV = ['GAME_DB_NAME', 'GAME_SPEED', 'GAME_ROUND_LENGTH_DAYS', 'GAME_MAP_SIZE', 'GAME_HERO_SPEED',
    'GAME_GPACK', 'GAME_SERVER_NAME', 'GAME_WORLD_ID', 'GAME_WORLD_URL', 'OV_WORLD'];

function ov_worlds_env(string $name, string $default = ''): string
{
    $value = getenv($name);
    return $value === false || $value === '' ? $default : $value;
}

/** Connection to the database server with the game's account (no database selected). */
function ov_worlds_pdo(?string $database = null): PDO
{
    $dsn = sprintf('mysql:host=%s;charset=utf8mb4', ov_worlds_env('GAME_DB_HOST', 'localhost'));
    if ($database !== null) {
        $dsn .= ';dbname=' . $database;
    }
    $password = getenv('GAME_DB_PASSWORD');
    return new PDO($dsn, ov_worlds_env('GAME_DB_USER', 'openvillage'), $password === false ? 'local-game-password' : $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function ov_worlds_global(): PDO
{
    static $pdo;
    return $pdo ??= ov_worlds_pdo(ov_worlds_env('GLOBAL_DB_NAME', 'openvillage_global'));
}

/** URL path of a world's pages: /game/ for world 1, /game<id>/ for the others. */
function ov_world_path(int $id): string
{
    return $id === 1 ? '/game/' : '/game' . $id . '/';
}

/** The further worlds (gameServers rows with their own database), by id. Empty while there are none. */
function ov_extra_worlds(): array
{
    try {
        $rows = ov_worlds_global()->query("SELECT * FROM gameServers WHERE id > 1 ORDER BY id")->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
    $worlds = [];
    foreach ($rows as $row) {
        if (($row['gameDb'] ?? '') !== '') {
            $worlds[(int)$row['id']] = $row;
        }
    }
    return $worlds;
}

/** Settings of world $id as environment variables; null for an unknown world. World 1 = the environment as it is. */
function ov_world_env(int $id): ?array
{
    if ($id === 1) {
        $env = [];
        foreach (OV_WORLD_ENV as $name) {
            $env[$name] = getenv($name) === false ? null : getenv($name);
        }
        return $env;
    }
    $row = ov_extra_worlds()[$id] ?? null;
    if ($row === null) {
        return null;
    }
    $base = rtrim(ov_worlds_env('APP_URL', 'http://127.0.0.1:8080'), '/');
    return [
        'GAME_DB_NAME' => (string)$row['gameDb'],
        'GAME_SPEED' => (string)$row['speed'],
        'GAME_ROUND_LENGTH_DAYS' => (string)$row['roundLength'],
        'GAME_MAP_SIZE' => (string)$row['mapSize'],
        'GAME_HERO_SPEED' => (string)max(1, (int)$row['heroSpeed']),
        'GAME_GPACK' => (string)$row['gpack'],
        'GAME_SERVER_NAME' => (string)$row['name'],
        'GAME_WORLD_ID' => (string)$row['worldId'],
        'GAME_WORLD_URL' => $base . ov_world_path($id),
        'OV_WORLD' => (string)$id,
    ];
}

/**
 * Puts world $id's settings into the environment for the rest of this request and restores the previous values
 * when it ends (the built-in server's workers and the iOS app serve the next request from the same process).
 * Returns false for an unknown world.
 */
function ov_apply_world(int $id): bool
{
    if ($id === 1) {
        return true;
    }
    $env = ov_world_env($id);
    if ($env === null) {
        return false;
    }
    $previous = [];
    foreach ($env as $name => $value) {
        $previous[$name] = getenv($name);
        putenv($name . '=' . $value);
    }
    register_shutdown_function(static function () use ($previous): void {
        foreach ($previous as $name => $value) {
            putenv($value === false ? $name : $name . '=' . $value);
        }
    });
    return true;
}

/** Runs an SQL file (a schema dump) in $database. */
function ov_worlds_import(string $database, string $file): void
{
    $password = getenv('GAME_DB_PASSWORD');
    mysqli_report(MYSQLI_REPORT_OFF);
    $m = @new mysqli(ov_worlds_env('GAME_DB_HOST', 'localhost'), ov_worlds_env('GAME_DB_USER', 'openvillage'),
        $password === false ? 'local-game-password' : $password, $database);
    if ($m->connect_errno) {
        throw new RuntimeException($m->connect_error);
    }
    $m->set_charset('utf8mb4');
    if ($m->multi_query((string)file_get_contents($file))) {
        do {
            if ($result = $m->store_result()) {
                $result->free();
            }
        } while ($m->more_results() && $m->next_result());
    }
    if ($m->errno) {
        throw new RuntimeException("$database: " . $m->error);
    }
    $m->close();
}

/**
 * Creates the game database of a new world and registers it in gameServers; the world itself (map, Natars,
 * administrators) is then installed by ov_install_world() in a request running with the new world's settings.
 * $settings: name, gpack, speed, mapSize, roundDays, heroSpeed. Returns the new world's id.
 */
function ov_register_world(array $settings): int
{
    $root = dirname(__DIR__, 3);
    require_once $root . '/main_script/copyable/include/migrate.php';
    run_global_schema_migrations();                                     // gameServers.gameDb, gpack, ...
    $global = ov_worlds_global();
    $id = max(1, (int)$global->query('SELECT MAX(id) FROM gameServers')->fetchColumn()) + 1;
    $database = ov_worlds_env('GAME_DB_NAME', 'openvillage_game') . '_' . $id;
    $server = ov_worlds_pdo();
    $server->exec("DROP DATABASE IF EXISTS `$database`");             // left over by a failed attempt
    $server->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    ov_worlds_import($database, $root . '/main_script/include/schema/T4.4.sql');
    $game = ov_worlds_pdo($database);
    $speed = max(1, (int)$settings['speed']);
    $mapSize = max(10, (int)$settings['mapSize']);
    $game->exec("INSERT INTO config (startTime, map_size, worldUniqueId, installed, loginInfoTitle, loginInfoHTML, message)
                 VALUES (UNIX_TIMESTAMP(), $mapSize, $id, 0, '', '', '')");
    $base = rtrim(ov_worlds_env('APP_URL', 'http://127.0.0.1:8080'), '/');
    $insert = $global->prepare('INSERT INTO gameServers (id, worldId, speed, name, version, gameWorldUrl, startTime, roundLength,
        activation, configFileLocation, gameDb, gpack, mapSize, heroSpeed)
        VALUES (?, ?, ?, ?, 4, ?, UNIX_TIMESTAMP(), ?, 0, ?, ?, ?, ?, ?)');
    $insert->execute([$id, 'world' . $id, $speed, (string)$settings['name'], $base . ov_world_path($id),
        max(1, (int)$settings['roundDays']), $root . '/main_script/copyable/include/connection.php', $database,
        (string)$settings['gpack'], $mapSize, max(1, (int)$settings['heroSpeed'])]);
    @mkdir($root . '/main_script/copyable/include/runtime/world-' . $id, 0775, true);
    return $id;
}

/**
 * Installs the world of the current environment on its prepared database (install.php run in-process):
 * migrations, map, Natars, Multihunter and Support with $password. Loads the game, so once per request.
 */
function ov_install_world(string $password): array
{
    $root = dirname(__DIR__, 3);
    $log = [];
    if (!defined('IS_INSTALLER')) {
        define('IS_INSTALLER', true);
    }
    require_once $root . '/main_script/copyable/include/env.php';
    require_once $root . '/main_script/include/bootstrap.php';
    require_once $root . '/main_script/copyable/include/migrate.php';
    ob_start();
    run_schema_migrations();
    run_global_schema_migrations();
    $log[] = trim((string)ob_get_clean());
    mt_srand(make_seed());
    if ((bool)\Core\Config::getInstance()->dynamic->installed) {
        $log[] = 'already installed';
        return $log;
    }
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
    return $log;
}

/**
 * Drops a further world: its database and its runtime folder. Its gameServers row stays (without gameDb, hidden),
 * so the id is never given to another world: sessions and cache keys of the old world carry that id.
 */
function ov_delete_world(int $id): void
{
    $row = ov_extra_worlds()[$id] ?? null;
    if ($id <= 1 || $row === null || !preg_match('/^[A-Za-z0-9_]+$/', (string)$row['gameDb'])) {
        throw new InvalidArgumentException('unknown world');
    }
    ov_worlds_pdo()->exec('DROP DATABASE IF EXISTS `' . $row['gameDb'] . '`');
    ov_worlds_global()->prepare("UPDATE gameServers SET gameDb = '', hidden = 1, finished = 1, registerClosed = 1 WHERE id = ?")
        ->execute([$id]);
    $runtime = dirname(__DIR__, 3) . '/main_script/copyable/include/runtime/world-' . $id;
    foreach (glob($runtime . '/*') ?: [] as $file) {
        is_file($file) && @unlink($file);
    }
    @rmdir($runtime);
}
