<?php
/*
 * Database connections used by the newer add-on pages (envanter, gods, flags, carkodul, ...).
 *
 * PHP 8 changes:
 *  - "<?" short open tag replaced by "<?php" (with short_open_tag=Off the whole file,
 *    including the password, used to be sent to the browser as plain text),
 *  - mysqli_query() was called without a connection (fatal ArgumentCountError in PHP 8),
 *  - credentials are now taken from application/config.php instead of being duplicated here.
 */
require_once __DIR__ . '/application/php8_compat.php';

if (!function_exists('baglanti_config')) {
    function baglanti_config()
    {
        if (defined('SQL_SERVER')) {
            return array(SQL_SERVER, SQL_USER, SQL_PASS, SQL_DB);
        }
        // read the SQL_* defines without executing config.php (it also connects and sends headers)
        $cfg = array('SQL_SERVER' => 'localhost', 'SQL_USER' => 'root', 'SQL_PASS' => '', 'SQL_DB' => 'travian');
        $src = @file_get_contents(__DIR__ . '/application/config.php');
        if ($src !== false) {
            foreach ($cfg as $k => $v) {
                if (preg_match('/define\(\s*["\']' . $k . '["\']\s*,\s*["\']([^"\']*)["\']\s*\)/', $src, $m)) {
                    $cfg[$k] = $m[1];
                }
            }
        }
        return array($cfg['SQL_SERVER'], $cfg['SQL_USER'], $cfg['SQL_PASS'], $cfg['SQL_DB']);
    }
}

list($DBHOST, $DBUSER, $DBPASS, $DBNAME) = baglanti_config();

if (!isset($baglanDB) || !($baglanDB instanceof PDO)) {
    try {
        $baglanDB = new PDO("mysql:host=$DBHOST;dbname=$DBNAME", $DBUSER, $DBPASS,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
    } catch (PDOException $e) {
        // do not leak connection details to visitors
        error_log('baglanti.php: DB connection failed: ' . $e->getMessage());
        die("Veritabanı Bağlantısı Başarısız");
    }
}

// one mysqli connection is enough; the old code opened three identical ones
$connection = mysqli_connect($DBHOST, $DBUSER, $DBPASS, $DBNAME);
if ($connection) {
    mysqli_set_charset($connection, 'utf8');
}
$connection2 = $connection;
$connection3 = $connection;
$host = $host2 = $host3 = $DBHOST;
$db_name = $db_name2 = $db_name3 = $DBNAME;
$db_user = $db_user2 = $db_user3 = $DBUSER;
$db_password = $db_password2 = $db_password3 = $DBPASS;
