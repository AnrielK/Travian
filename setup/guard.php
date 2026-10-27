<?php
/*
 * Security: the installer drops and recreates every table and rewrites application/config.php.
 * It used to stay reachable after the installation, so anybody could wipe the game.
 *
 * The installer is now locked when
 *   - setup/install.lock exists (created automatically at the end of the installation), or
 *   - the database already contains registered players (servers installed before this change).
 * To reinstall on purpose, delete setup/install.lock and empty the database.
 */
if (!function_exists('setup_is_locked')) {
    function setup_is_locked()
    {
        if (is_file(__DIR__ . '/install.lock')) {
            return true;
        }
        // credentials of the installer (application/db_local.php); older installs kept them in config.php
        $c = array();
        $local = __DIR__ . '/../application/db_local.php';
        if (is_file($local)) {
            $d = include $local;
            if (is_array($d)) {
                $c = array('SQL_SERVER' => ($d['host'] ?? '') . (!empty($d['port']) ? ';port=' . $d['port'] : ''),
                    'SQL_USER' => $d['user'] ?? '', 'SQL_PASS' => $d['pass'] ?? '', 'SQL_DB' => $d['name'] ?? '');
            }
        }
        if (!$c) {
            $src = @file_get_contents(__DIR__ . '/../application/config.php');
            if ($src === false) {
                return false;
            }
            foreach (array('SQL_SERVER', 'SQL_USER', 'SQL_PASS', 'SQL_DB') as $k) {
                if (!preg_match('/define\(\s*["\']' . $k . '["\']\s*,\s*["\']([^"\']*)["\']\s*\)/', $src, $m)) {
                    return false; // not configured yet
                }
                $c[$k] = $m[1];
            }
        }
        try {
            $pdo = new PDO('mysql:host=' . $c['SQL_SERVER'] . ';dbname=' . $c['SQL_DB'], $c['SQL_USER'], $c['SQL_PASS'],
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3));
            $n = (int)$pdo->query('SELECT COUNT(*) FROM users WHERE id > 6')->fetchColumn();
            return $n > 0;
        } catch (Throwable $e) {
            return false; // no database / no tables yet -> installation allowed
        }
    }
}

if (setup_is_locked()) {
    http_response_code(403);
    exit('The game is already installed. The installer is locked (delete setup/install.lock and empty the database to reinstall).');
}
