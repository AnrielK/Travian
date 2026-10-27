<?php
/*
 * Helpers of the installer (process.php):
 *  - the database credentials go to application/db_local.php (read by application/db_settings.php);
 *    application/config.php is never rewritten, so the game code around it (looks, Legends, …) stays intact,
 *  - every table of the game is dropped before the tables are created again, so the installation can be repeated
 *    (the old list missed tables, the second run stopped at "table already exists" and left half a database),
 *  - SQL errors stop the installation with a message instead of being skipped silently.
 */

function setup_sql_text()
{
    return file_get_contents(__DIR__ . "/data/sql.sql") . "\n" . file_get_contents(__DIR__ . "/data/missing_tables.sql");
}

/** Every table the installer creates plus the tables the game adds on its own later. */
function setup_tables($sql)
{
    preg_match_all('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([A-Za-z0-9_]+)`?/i', $sql, $m);
    $tables = array_merge($m[1], array('ov_village_groups', 'ov_village_group_members', 'ov_user_prefs', 'ov_task_progress', 'hero_legends',
        'a2b', 'odemeler', 'paketler', 'abdata', 'achiev', 'activate', 'adventure', 'alidata', 'ali_invite', 'ali_log', 'ali_permission',
        'antimult', 'artefacts', 'attacks', 'auction', 'banlist', 'bdata', 'buygold', 'confs', 'critical_log', 'demolition', 'diplomacy',
        'enforcement', 'farmlist', 'fdata', 'hero', 'heroface', 'heroinventory', 'heroitems', 'links', 'log', 'map_control', 'market',
        'mdata', 'medal', 'movement', 'ndata', 'newproc', 'news', 'odata', 'online', 'palevo', 'password', 'prisoners', 'queue',
        'raidlist', 'referals', 'research', 'roullet', 'route', 'sitters', 'spravka', 'tdata', 'training', 'units', 'users', 'vdata',
        'wdata', 'config', 'codes', 'payments', 'autorenewals', 'quests', 'deleted', 'ignore', 'plusaddons', 'storage', 'support', 'servers'));
    $out = array();
    foreach ($tables as $t) {
        $out[strtolower($t)] = $t;   // Windows MySQL/MariaDB: table names are case-insensitive
    }
    return array_values($out);
}

/** Runs the SQL script statement by statement result; returns '' or the first error. */
function setup_run_sql($mysqli, $sql)
{
    $n = 1;
    if (!$mysqli->multi_query($sql)) {
        return 'SQL #1: ' . $mysqli->error;
    }
    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
        if (!$mysqli->more_results()) {
            break;
        }
        $n++;
        if (!$mysqli->next_result()) {
            return 'SQL #' . $n . ': ' . $mysqli->error;
        }
    } while (true);
    return $mysqli->errno ? 'SQL #' . $n . ': ' . $mysqli->error : '';
}

/** Drops all tables of the game (also ones of an older, half finished installation). */
function setup_drop_tables($mysqli, $sql)
{
    $mysqli->query('SET FOREIGN_KEY_CHECKS = 0');
    foreach (setup_tables($sql) as $t) {
        $mysqli->query('DROP TABLE IF EXISTS `' . $mysqli->real_escape_string($t) . '`');
    }
    $mysqli->query('SET FOREIGN_KEY_CHECKS = 1');
}

/** Writes application/db_local.php; returns false when the folder is not writable. */
function setup_write_db_local($host, $user, $pass, $name)
{
    $port = '3306';
    if (preg_match('/^(.+):(\d+)$/', $host, $m)) {
        $host = $m[1];
        $port = $m[2];
    }
    $data = array('host' => $host, 'port' => $port, 'user' => $user, 'pass' => $pass, 'name' => $name);
    $php = "<?php\n// Database of this game server, written by the installer (setup/). Environment variables TRAVIAN_DB_* take precedence.\nreturn "
        . var_export($data, true) . ";\n";
    return @file_put_contents(__DIR__ . '/../application/db_local.php', $php) !== false;
}

/** Error page of the installer with a way back to the form. */
function setup_fail($message)
{
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Install error</title></head><body style="font:14px Arial,sans-serif;margin:40px">'
        . '<h2 style="color:#b00">Installation failed</h2><p>' . htmlspecialchars($message) . '</p>'
        . '<p><a href="index.php?s=1">Back to the settings</a></p></body></html>';
    exit;
}
