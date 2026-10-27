<?php
/**
 * Minimal mysqli replacement for the SQLite build (the iOS PHP has no mysqli extension).
 *
 * The add-on pages (envanter, gods, flags, banksystem, statistics, …) use procedural mysqli_*
 * calls through baglanti.php. Here they run on the game's SQLite connection (travian_pdo()),
 * every statement translated by SqliteCompat. Results are buffered like mysqli_store_result.
 */

if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
    define('MYSQLI_NUM', 2);
    define('MYSQLI_BOTH', 3);
    define('MYSQLI_REPORT_OFF', 0);
    define('MYSQLI_REPORT_ERROR', 1);
    define('MYSQLI_REPORT_STRICT', 2);
}

class mysqli_result
{
    public $num_rows = 0;
    public $field_count = 0;
    private $rows;
    private $pos = 0;

    public function __construct(array $rows, $fieldCount)
    {
        $this->rows = $rows;
        $this->num_rows = count($rows);
        $this->field_count = $fieldCount;
    }

    public function fetch_assoc()
    {
        return $this->pos < $this->num_rows ? $this->rows[$this->pos++] : null;
    }

    public function fetch_row()
    {
        $r = $this->fetch_assoc();
        return $r === null ? null : array_values($r);
    }

    public function fetch_array($mode = MYSQLI_BOTH)
    {
        $r = $this->fetch_assoc();
        if ($r === null) return null;
        if ($mode == MYSQLI_ASSOC) return $r;
        $num = array_values($r);
        if ($mode == MYSQLI_NUM) return $num;
        $out = [];
        $i = 0;
        foreach ($r as $k => $v) { $out[$i++] = $v; $out[$k] = $v; }
        return $out;
    }

    public function fetch_all($mode = MYSQLI_NUM)
    {
        $all = [];
        while (($r = $this->fetch_array($mode)) !== null) $all[] = $r;
        return $all;
    }

    public function data_seek($offset)
    {
        if ($offset < 0 || $offset >= $this->num_rows) return false;
        $this->pos = (int)$offset;
        return true;
    }

    public function free() {}
    public function close() {}
}

class mysqli
{
    public $error = '';
    public $errno = 0;
    public $connect_error = null;
    public $connect_errno = 0;
    public $insert_id = 0;
    public $affected_rows = 0;
    private $pdo;

    public function __construct($host = null, $user = null, $pass = null, $db = null, $port = null, $socket = null)
    {
        $this->pdo = travian_pdo();
        if (!$this->pdo) {
            $this->connect_error = 'SQLite database not available';
            $this->connect_errno = 2002;
        }
    }

    public function query($sql, $resultMode = 0)
    {
        $this->error = ''; $this->errno = 0;
        if (!$this->pdo) return false;
        try {
            $stmt = $this->pdo->query($sql);
        } catch (PDOException $e) {
            $this->error = $e->getMessage(); $this->errno = 1064;
            if (function_exists('travian_log_sql_error')) travian_log_sql_error($e->getMessage(), $sql);
            return false;
        }
        if ($stmt->columnCount() > 0) {
            return new mysqli_result($stmt->fetchAll(PDO::FETCH_ASSOC), $stmt->columnCount());
        }
        $this->affected_rows = $stmt->rowCount();
        $this->insert_id = (int)$this->pdo->lastInsertId();
        return true;
    }

    public function multi_query($sql)
    {
        foreach (preg_split('/;\s*\n/', $sql) as $q) {
            if (trim($q) !== '') $this->query($q);
        }
        return true;
    }

    public function real_escape_string($s) { return sql_esc($s); }
    public function escape_string($s) { return sql_esc($s); }
    public function set_charset($charset) { return true; }
    public function select_db($db) { return true; }
    public function close() { return true; }
    public function ping() { return (bool)$this->pdo; }
    public function more_results() { return false; }
    public function next_result() { return false; }
    public function store_result() { return false; }
}

function mysqli_report($flags) { return true; }
function mysqli_connect($host = null, $user = null, $pass = null, $db = null, $port = null, $socket = null)
{
    $m = new mysqli($host, $user, $pass, $db);
    return $m->connect_error ? false : $m;
}
function mysqli_connect_error() { return travian_pdo() ? null : 'SQLite database not available'; }
function mysqli_connect_errno() { return travian_pdo() ? 0 : 2002; }
function mysqli_query($link, $sql, $mode = 0) { return $link instanceof mysqli ? $link->query($sql) : false; }
function mysqli_error($link) { return $link instanceof mysqli ? $link->error : ''; }
function mysqli_errno($link) { return $link instanceof mysqli ? $link->errno : 0; }
function mysqli_close($link) { return true; }
function mysqli_set_charset($link, $charset) { return true; }
function mysqli_select_db($link, $db) { return true; }
function mysqli_real_escape_string($link, $s) { return sql_esc($s); }
function mysqli_escape_string($link, $s) { return sql_esc($s); }
function mysqli_insert_id($link) { return $link instanceof mysqli ? $link->insert_id : 0; }
function mysqli_affected_rows($link) { return $link instanceof mysqli ? $link->affected_rows : -1; }
function mysqli_fetch_assoc($r) { return $r instanceof mysqli_result ? $r->fetch_assoc() : null; }
function mysqli_fetch_array($r, $mode = MYSQLI_BOTH) { return $r instanceof mysqli_result ? $r->fetch_array($mode) : null; }
function mysqli_fetch_row($r) { return $r instanceof mysqli_result ? $r->fetch_row() : null; }
function mysqli_fetch_all($r, $mode = MYSQLI_NUM) { return $r instanceof mysqli_result ? $r->fetch_all($mode) : []; }
function mysqli_num_rows($r) { return $r instanceof mysqli_result ? $r->num_rows : 0; }
function mysqli_num_fields($r) { return $r instanceof mysqli_result ? $r->field_count : 0; }
function mysqli_data_seek($r, $o) { return $r instanceof mysqli_result ? $r->data_seek($o) : false; }
function mysqli_free_result($r) { return null; }
function mysqli_result($r, $row = 0, $field = 0)
{
    if (!$r instanceof mysqli_result || !$r->data_seek($row)) return false;
    $a = $r->fetch_array(MYSQLI_BOTH);
    return $a[$field] ?? false;
}
