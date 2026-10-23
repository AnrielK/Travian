<?php
/*
 * OpenVillage for XAMPP (Windows / Linux): starts the game with the PHP and MariaDB (MySQL) of XAMPP.
 * Loaded by launcher.php once it has checked the PHP version (this file needs PHP 8.2).
 *
 * XAMPP's Apache is not used: the game runs in PHP's built-in web server with scripts/native/router.php
 * (behind xampp/router.php), Redis is replaced by a MariaDB table (scripts/native/runtime/redis-polyfill.php)
 * and the automation runs through /__openvillage/tick once a second, from this script – the same runtime as
 * the iOS app. The first start opens the installation wizard (xampp/installer/), which writes config.local.php.
 */

declare(strict_types=1);

const OV_EXTENSIONS = ['mysqli', 'pdo_mysql', 'mbstring', 'gd', 'iconv', 'ctype'];

$root = dirname(__DIR__);
$isWindows = PHP_OS_FAMILY === 'Windows';
$options = getopt('', ['port:', 'no-browser']);
$runtime = __DIR__ . '/runtime';
$configFile = __DIR__ . '/config.local.php';

function say(string $line): void
{
    fwrite(STDOUT, $line . PHP_EOL);
}

function fail(string $line): never
{
    fwrite(STDERR, 'CHYBA: ' . $line . PHP_EOL);
    exit(1);
}

function port_open(int $port): bool
{
    $socket = @fsockopen('127.0.0.1', $port, $errno, $error, 1);
    if ($socket === false) {
        return false;
    }
    fclose($socket);
    return true;
}

say('OpenVillage pro XAMPP');
say('PHP ' . PHP_VERSION . ' (' . PHP_BINARY . ')');

// ---- php.ini for the game server (on top of XAMPP's php.ini) -------------------------------------
foreach (['', '/sessions', '/logs'] as $dir) {
    if (!is_dir($runtime . $dir) && !mkdir($runtime . $dir, 0775, true)) {
        fail("Nelze vytvořit složku $runtime$dir");
    }
}
$ini = [
    'display_errors=0',
    'log_errors=1',
    'error_reporting=' . E_ALL,
    'error_log=' . $runtime . '/logs/php-error.log',
    'memory_limit=512M',
    'max_execution_time=300',
    'session.save_path=' . $runtime . '/sessions',
    'session.use_strict_mode=1',
    'session.cookie_httponly=1',
    'session.cookie_samesite=Lax',
    'expose_php=0',
    'auto_prepend_file=' . $root . '/scripts/native/runtime/redis-polyfill.php',
];
$extensionDir = (string)ini_get('extension_dir');
$missing = [];
foreach (OV_EXTENSIONS as $extension) {
    if (extension_loaded($extension)) {
        continue;
    }
    // Enabled in XAMPP's php.ini by default; switched on here when the user has commented it out.
    $file = $extensionDir . DIRECTORY_SEPARATOR . ($isWindows ? "php_$extension.dll" : "$extension.so");
    if (is_file($file)) {
        $ini[] = 'extension=' . $extension;
    } else {
        $missing[] = $extension;
    }
}
if ($missing) {
    fail('Chybí rozšíření PHP: ' . implode(', ', $missing) . '. Povol je v php.ini XAMPPu (' . php_ini_loaded_file() . ').');
}

// ---- MariaDB / MySQL of XAMPP --------------------------------------------------------------------
if (!port_open(3306)) {
    $xampp = dirname(PHP_BINARY, 2);                         // C:\xampp\php\php.exe
    $mysqld = $xampp . '\mysql\bin\mysqld.exe';
    if ($isWindows && is_file($mysqld)) {
        say('Spouštím MySQL z XAMPPu…');
        // Like xampp\mysql_start.bat, in its own minimized window, so it outlives this one.
        pclose(popen('start "MySQL" /MIN /D "' . $xampp . '" "' . $mysqld . '" --defaults-file=mysql\bin\my.ini --standalone', 'r'));
        for ($i = 0; $i < 30 && !port_open(3306); $i++) {
            sleep(1);
        }
    }
    if (!port_open(3306)) {
        say('POZOR: MySQL/MariaDB neběží na portu 3306. Spusť ho v XAMPP Control Panelu (MySQL → Start)'
            . ($isWindows ? '' : ' nebo příkazem: sudo /opt/lampp/lampp startmysql') . '.');
    }
}

// ---- web server ------------------------------------------------------------------------------------
$port = (int)($options['port'] ?? 8080);
for ($tries = 0; $tries < 20; $tries++, $port++) {
    $probe = @stream_socket_server("tcp://127.0.0.1:$port");
    if ($probe !== false) {
        fclose($probe);
        break;
    }
}
$command = [PHP_BINARY];
foreach ($ini as $setting) {
    array_push($command, '-d', $setting);
}
array_push($command, '-S', "127.0.0.1:$port", '-t', $root . '/web/public', __DIR__ . '/router.php');
$env = getenv();
if (!$isWindows) {
    $env['PHP_CLI_SERVER_WORKERS'] = '4';                    // not supported by the Windows build
}
$serverLog = $runtime . '/logs/server.log';
$server = proc_open($command, [0 => ['pipe', 'r'], 1 => ['file', $serverLog, 'a'], 2 => ['file', $serverLog, 'a']],
    $pipes, $root, $env);
if (!is_resource($server)) {
    fail('Nepodařilo se spustit webový server PHP.');
}
for ($i = 0; $i < 50 && !port_open($port); $i++) {
    usleep(200000);
}
if (!port_open($port)) {
    fail("Webový server se nespustil, viz $serverLog");
}
// Ctrl+C reaches the server too (same console); a plain kill of this script should not leave it running.
$stopServer = static function () use ($server): void {
    if (proc_get_status($server)['running']) {
        proc_terminate($server);
    }
};
register_shutdown_function($stopServer);
if (function_exists('pcntl_async_signals')) {
    pcntl_async_signals(true);
    foreach ([SIGINT, SIGTERM, SIGHUP] as $signal) {
        pcntl_signal($signal, static fn () => exit(0));
    }
}
$url = "http://127.0.0.1:$port/";
say("Hra běží na $url  (ukončení: Ctrl+C nebo zavřením tohoto okna)");

if (!isset($options['no-browser'])) {
    if ($isWindows) {
        pclose(popen('start "" "' . $url . '"', 'r'));
    } elseif (PHP_OS_FAMILY === 'Darwin') {
        exec('open ' . escapeshellarg($url) . ' >/dev/null 2>&1 &');
    } elseif (trim((string)shell_exec('command -v xdg-open 2>/dev/null')) !== '') {
        exec('xdg-open ' . escapeshellarg($url) . ' >/dev/null 2>&1 &');
    }
}

// ---- automation: one tick a second once the world is installed ------------------------------------
$automationLog = $runtime . '/logs/automation.log';
$announced = false;
while (true) {
    $status = proc_get_status($server);
    if (!$status['running']) {
        fail("Webový server skončil, viz $serverLog");
    }
    $config = is_file($configFile) ? (include $configFile) : null;
    if (is_array($config) && !empty($config['installed'])) {
        if (!$announced) {
            say('Svět je nainstalovaný, automatika běží.');
            $announced = true;
        }
        $context = stream_context_create(['http' => [
            'method' => 'GET',
            'header' => 'X-OV-Token: ' . $config['env']['OV_CONTROL_TOKEN'] . "\r\n",
            'timeout' => 300,
            'ignore_errors' => true,
        ]]);
        $response = @file_get_contents("http://127.0.0.1:$port/__openvillage/tick", false, $context);
        $result = is_string($response) ? json_decode($response, true) : null;
        if (!is_array($result) || !empty($result['errors'])) {
            $line = date('Y-m-d H:i:s') . ' ' . (is_array($result) ? implode('; ', (array)$result['errors']) : 'tick failed: ' . substr((string)$response, 0, 300));
            file_put_contents($automationLog, $line . PHP_EOL, FILE_APPEND);
        }
    }
    sleep(1);
}
