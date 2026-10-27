<?php
/**
 * Database settings for the local / iOS build (loaded by config.php and baglanti.php).
 *
 * Values come from environment variables, so the same files work everywhere:
 *   TRAVIAN_DB_DRIVER    mysql (default) | sqlite (iOS app)
 *   TRAVIAN_SQLITE_PATH  SQLite database file (sqlite driver)
 *   TRAVIAN_DB_HOST / _PORT / _USER / _PASS / _NAME   MySQL / MariaDB
 * Without environment variables the MySQL credentials come from application/db_local.php, which the installer
 * (setup/) writes – config.php itself is never rewritten.
 *   TRAVIAN_SINGLE_ACCOUNT_PER_IP=1   restore the live-server rule "one account per IP"
 */
if (defined('SQL_DRIVER')) {
    return;
}
// Credentials written by the installer (setup/) into application/db_local.php – environment variables win.
$travianDbLocal = is_file(__DIR__ . '/db_local.php') ? (include __DIR__ . '/db_local.php') : [];
if (!is_array($travianDbLocal)) $travianDbLocal = [];
define("SQL_SERVER", getenv("TRAVIAN_DB_HOST") ?: ($travianDbLocal['host'] ?? "127.0.0.1"));
define("SQL_PORT",   getenv("TRAVIAN_DB_PORT") ?: ($travianDbLocal['port'] ?? "3306"));
define("SQL_USER",   getenv("TRAVIAN_DB_USER") ?: ($travianDbLocal['user'] ?? "travian"));
define("SQL_PASS",   getenv("TRAVIAN_DB_PASS") !== false ? getenv("TRAVIAN_DB_PASS") : ($travianDbLocal['pass'] ?? "travian"));
define("SQL_DB",     getenv("TRAVIAN_DB_NAME") ?: ($travianDbLocal['name'] ?? "travian46"));
unset($travianDbLocal);
define("SQL_DRIVER",  getenv("TRAVIAN_DB_DRIVER") ?: "mysql");
define("SQLITE_PATH", getenv("TRAVIAN_SQLITE_PATH") ?: dirname(__DIR__) . "/local/travian.sqlite");
// Local testing: several accounts from 127.0.0.1 (the live server allowed one account per IP)
define("LOCAL_MULTI_ACCOUNTS", getenv("TRAVIAN_SINGLE_ACCOUNT_PER_IP") ? false : true);

if (SQL_DRIVER === 'sqlite') {
    require_once __DIR__ . '/SqliteCompat.php';
    if (!function_exists('mysqli_query')) {
        require_once __DIR__ . '/sqlite_mysqli.php';   // mysqli_* used by the add-on pages
    }
}

/**
 * The game's PDO connection (one per request; SQLite needs a single writer connection).
 * Returns false when the database is unreachable (like the old config.php did).
 */
function travian_pdo()
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    try {
        if (SQL_DRIVER === 'sqlite') {
            $pdo = SqliteCompat::connect(SQLITE_PATH);
        } else {
            $pdo = new PDO('mysql:host=' . SQL_SERVER . ';port=' . SQL_PORT . ';dbname=' . SQL_DB . ';charset=utf8', SQL_USER, SQL_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8, sql_mode=''",
            ]);
        }
    } catch (PDOException $e) {
        error_log('DB connection failed: ' . $e->getMessage());
        $pdo = false;
    }
    return $pdo;
}

/** One line per SQL error into local/logs/sql_errors.log (local build). */
function travian_log_sql_error($message, $query)
{
    $dir = dirname(__DIR__) . '/local/logs';
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    $where = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'cli';
    @file_put_contents($dir . '/sql_errors.log', date('c') . ' ' . $where . ' | ' . $message . ' | ' . preg_replace('/\s+/', ' ', (string)$query) . "\n", FILE_APPEND);
}
