<?php
// security: this script deletes the whole database. It used to run for anybody who opened the URL.
// It can now only be started from the command line:  php dropdb.php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Forbidden'); }
require_once __DIR__ . '/baglanti.php';
$mysqli = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME); // credentials from application/config.php
$mysqli->query('SET foreign_key_checks = 0');
if ($result = $mysqli->query("SHOW TABLES")) {
    while ($row = $result->fetch_array(MYSQLI_NUM)) {
        $mysqli->query('DROP TABLE IF EXISTS ' . $row[0]);
    }
}

$mysqli->query('SET foreign_key_checks = 1');
$mysqli->close();
