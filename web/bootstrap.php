<?php

declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure',
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');
session_start();
// Further worlds (gameServers rows with their own database); not part of every distribution.
if (is_file(dirname(__DIR__) . '/scripts/native/runtime/worlds.php')) {
    require_once dirname(__DIR__) . '/scripts/native/runtime/worlds.php';
}
if (!function_exists('ov_extra_worlds')) {
    function ov_extra_worlds(): array
    {
        return [];
    }

    function ov_world_path(int $id): string
    {
        return '/game/';
    }
}

function env_value(string $name, string $default = ''): string
{
    $value = getenv($name);
    return $value === false || $value === '' ? $default : $value;
}

/** Database of world $world (1 = the installation's world; further worlds see scripts/native/runtime/worlds.php). */
function game_database(int $world = 1): PDO
{
    static $databases = [];
    if (isset($databases[$world])) {
        return $databases[$world];
    }
    $name = $world === 1 ? env_value('GAME_DB_NAME', 'openvillage_game') : (string)(ov_extra_worlds()[$world]['gameDb'] ?? '');
    if ($name === '') {
        throw new RuntimeException("unknown world $world");
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        env_value('GAME_DB_HOST', 'database'),
        $name
    );
    $database = new PDO(
        $dsn,
        env_value('GAME_DB_USER', 'openvillage'),
        getenv('GAME_DB_PASSWORD') !== false ? getenv('GAME_DB_PASSWORD') : 'local-game-password',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    return $databases[$world] = $database;
}

/**
 * The worlds shown in the lobby: id, path (/game/, /game2/, ...), name, look (gpack and its name), tribes, speed,
 * start, map size, players, whether the world is installed (ready) and its own registration page (registerUrl).
 */
function lobby_worlds(): array
{
    $gpacks = require dirname(__DIR__) . '/sections/gpack/gpack.php';
    $worlds = [1 => ['name' => env_value('GAME_SERVER_NAME', 'Local World'), 'speed' => (float)env_value('GAME_SPEED', '1'),
        'gpack' => '', 'mapSize' => (int)env_value('GAME_MAP_SIZE', '100')]];
    foreach (ov_extra_worlds() as $id => $row) {
        $worlds[$id] = ['name' => (string)$row['name'], 'speed' => (float)$row['speed'], 'gpack' => (string)$row['gpack'],
            'mapSize' => (int)$row['mapSize']];
    }
    $tribeNames = [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 6 => 'egyptian', 7 => 'hun', 8 => 'spartan'];
    foreach ($worlds as $id => &$world) {
        $world += ['id' => $id, 'path' => ov_world_path($id), 'ready' => false, 'start' => time(), 'players' => 0];
        try {
            $database = game_database($id);
            $config = $database->query('SELECT * FROM config LIMIT 1')->fetch();
            if ($config) {
                $world['ready'] = (int)$config['installed'] === 1;
                $world['start'] = (int)$config['startTime'];
                $world['mapSize'] = (int)$config['map_size'];
                $world['gpack'] = (string)($config['gpack'] ?? '') ?: $world['gpack'];
            }
            $world['players'] = (int)$database->query('SELECT COUNT(*) FROM users WHERE id > 2')->fetchColumn();
        } catch (Throwable $exception) {
            $world['ready'] = false;
        }
        // The world's look (config.gpack, chosen when the world was created) and the tribes it offers.
        $look = $gpacks['styles'][$world['gpack']] ?? ($id === 1 ? $gpacks['styles'][$gpacks['mainPage']] ?? [] : []);
        $world['lookName'] = preg_replace('/\s*\(.*\)$/', '', (string)($look['name'] ?? 'OpenVillage 4.6'));
        $world['tribes'] = array_values(array_intersect_key($tribeNames, array_flip($look['tribes'] ?? [1, 2, 3, 6, 7])));
        // The look's own registration page (Travian 4.0) instead of the lobby's dialog.
        $world['registerUrl'] = empty($look['ownRegistration']) ? '' : $world['path'] . 'register.php';
    }
    unset($world);
    return $worlds;
}

function csrf_token(): string
{
    if (empty($_SESSION['launcher_csrf'])) {
        $_SESSION['launcher_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['launcher_csrf'];
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
