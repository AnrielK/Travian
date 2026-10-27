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

// Local / iOS build: settings from environment variables (MySQL or SQLite), shared PDO connection.
require_once __DIR__ . '/application/db_settings.php';
$DBHOST = SQL_SERVER; $DBUSER = SQL_USER; $DBPASS = SQL_PASS; $DBNAME = SQL_DB;

if (!isset($baglanDB) || !($baglanDB instanceof PDO)) {
    $baglanDB = travian_pdo();
    if (!$baglanDB) {
        die("Veritabanı Bağlantısı Başarısız");   // do not leak connection details to visitors
    }
}

// one mysqli connection is enough; the old code opened three identical ones
$connection = mysqli_connect($DBHOST, $DBUSER, $DBPASS, $DBNAME, (int)SQL_PORT);
if ($connection) {
    mysqli_set_charset($connection, 'utf8');
}
$connection2 = $connection;
$connection3 = $connection;
$host = $host2 = $host3 = $DBHOST;
$db_name = $db_name2 = $db_name3 = $DBNAME;
$db_user = $db_user2 = $db_user3 = $DBUSER;
$db_password = $db_password2 = $db_password3 = $DBPASS;
