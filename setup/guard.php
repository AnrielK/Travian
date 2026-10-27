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
        $cfg = __DIR__ . '/../application/config.php';
        $src = @file_get_contents($cfg);
        if ($src === false) {
            return false;
        }
        $c = array();
        foreach (array('SQL_SERVER', 'SQL_USER', 'SQL_PASS', 'SQL_DB') as $k) {
            if (!preg_match('/define\(\s*["\']' . $k . '["\']\s*,\s*["\']([^"\']*)["\']\s*\)/', $src, $m)) {
                return false; // template / not configured yet
            }
            $c[$k] = $m[1];
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
