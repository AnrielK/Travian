<?php
/*
 * Router of the XAMPP runtime (started by xampp/launcher.php):
 *   - loads config.local.php (written by the installation wizard) into the environment the game reads,
 *   - serves the wizard at /install/ until the world is installed (and every other page redirects there),
 *   - then hands each request to scripts/native/router.php.
 */

declare(strict_types=1);

$ovConfigFile = __DIR__ . '/config.local.php';
$ovConfig = is_file($ovConfigFile) ? (include $ovConfigFile) : null;
$ovInstalled = is_array($ovConfig) && !empty($ovConfig['installed']);
$ovPath = rawurldecode((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));

if (is_array($ovConfig)) {
    $ovBase = 'http://127.0.0.1:' . (int)($_SERVER['SERVER_PORT'] ?? 8080);
    $ovEnv = $ovConfig['env'] + [
        'APP_URL' => $ovBase,
        'GAME_WORLD_URL' => $ovBase . '/game/',
        'GAME_WORLD_ID' => 'local',
        'OV_KV_DB' => 'openvillage_cache',               // redis-polyfill.php: the cache lives in MariaDB
        'OV_TICK_SKIP' => 'mayExitJobs:backup',          // no forked workers, no mysqldump
        'OV_NATIVE' => '1',
        'OV_XAMPP' => '1',
    ];
    $ovEnv['GLOBAL_DB_HOST'] = $ovEnv['GAME_DB_HOST'];
    $ovEnv['GLOBAL_DB_USER'] = $ovEnv['GAME_DB_USER'];
    $ovEnv['GLOBAL_DB_PASSWORD'] = $ovEnv['GAME_DB_PASSWORD'];
    foreach ($ovEnv as $ovName => $ovValue) {
        putenv($ovName . '=' . $ovValue);
    }
    if (!empty($ovEnv['APP_TIMEZONE'])) {
        date_default_timezone_set($ovEnv['APP_TIMEZONE']);
    }
}

if ($ovPath === '/install' || str_starts_with($ovPath, '/install/')) {
    if ($ovInstalled) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Not Found\n";
        return true;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Cache-Control: no-store');
    require __DIR__ . '/installer/' . ($ovPath === '/install/api' ? 'api.php' : 'index.php');
    return true;
}
if (!$ovInstalled && !str_starts_with($ovPath, '/__openvillage/')) {
    header('Location: /install/', true, 302);
    return true;
}
return require dirname(__DIR__) . '/scripts/native/router.php';
