<?php
// PHP 8 compatibility: constants referenced but never defined in the original code.
// PHP 7 treated an undefined constant as its own name (with a warning); PHP 8 throws, so we define them here.
$__compatNumeric = ["TB_PREFIX"=>"", "xQUEST"=>1, "NATARS_MAX"=>22, "MINPROTECTION"=>0, "COSTRES"=>100, "DBSERVIDORES"=>SQL_DB, "COSTCP"=>100, "HOWCP"=>500];
foreach ($__compatNumeric as $k=>$v) { if (!defined($k)) define($k, $v); }
$__compatNames = [
  "RES_MOREINFO",
  "RES_PRODUCTION",
  "RES_FULLIN",
  "DBSERVIDORES",
  "qArrayW",
  "qArrayE",
  "qArrayB",
  "R4",
  "R3",
  "R2",
  "R1",
  "KARG",
  "IEFF32s",
  "sell",
  "bids",
  "XTRAVIAN",
  "WW",
  "Villages",
  "STARTNOW",
  "SELL_RES",
  "SELL_CP",
  "SAME_NAME",
  "RU",
  "RES_CROPBALANCETITLE",
  "RES_CROPBALANCE",
  "PD_LUMBER",
  "PD_IRON",
  "PD_CROP",
  "PD_CLAY",
  "NAME_NO_EXIST",
  "NAME_EMPTY",
  "LOGIN_NO_JAVASCRIPT",
  "HERO_HERODEADORNOTHERE",
  "BL_CAPITAL",
  "ALREADY_IN_ALLY",
  "ALREADY_INVITED",
];
foreach ($__compatNames as $k) { if (!defined($k)) define($k, $k); }
unset($__compatNumeric, $__compatNames);

// PHP 8: count()/sizeof() throw on non-countables. PHP 7 returned 0 for null and 1 for scalars.
if (!function_exists('compat_count')) {
    function compat_count($value, $mode = COUNT_NORMAL) {
        if (is_array($value) || $value instanceof Countable) return count($value, $mode);
        return $value === null ? 0 : 1;
    }
}

// Legacy mysql_* API (removed in PHP 7) used by a few admin pages – thin mysqli / SQLite shim.
if (!function_exists('mysql_query') && SQL_DRIVER === 'sqlite') {
    final class CompatMysqlResult {
        public $rows; public $pos = 0;
        function __construct(array $rows) { $this->rows = $rows; }
        function next() { return $this->pos < count($this->rows) ? $this->rows[$this->pos++] : false; }
    }
    function __compat_sqlite_pdo() {
        static $pdo = null;
        if ($pdo === null) { require_once __DIR__.'/SqliteCompat.php'; $pdo = SqliteCompat::connect(SQLITE_PATH); }
        return $pdo;
    }
    function mysql_query($q, $link = null) {
        $pdo = __compat_sqlite_pdo(); $params = array();
        try {
            $sql = SqliteCompat::translate($pdo, $q, $params);
            if ($sql === null) return true;
            $st = $pdo->query($sql);
            if (stripos(ltrim($sql), 'SELECT') === 0) return new CompatMysqlResult($st->fetchAll(PDO::FETCH_ASSOC));
            $GLOBALS['__compat_affected'] = $st->rowCount();
            return true;
        } catch (Throwable $e) { $GLOBALS['__compat_error'] = $e->getMessage(); return false; }
    }
    function mysql_fetch_assoc($r) { return $r instanceof CompatMysqlResult ? $r->next() : false; }
    function mysql_fetch_array($r, $t = 3) {
        if (!($r instanceof CompatMysqlResult) || !($row = $r->next())) return false;
        return $t === 1 ? $row : array_merge($row, array_values($row));
    }
    function mysql_fetch_row($r) { $row = mysql_fetch_assoc($r); return $row ? array_values($row) : false; }
    function mysql_num_rows($r) { return $r instanceof CompatMysqlResult ? count($r->rows) : 0; }
    function mysql_result($r, $row = 0, $field = 0) {
        if (!($r instanceof CompatMysqlResult) || !isset($r->rows[$row])) return false;
        $a = $r->rows[$row]; $v = array_values($a);
        return is_int($field) ? ($v[$field] ?? false) : ($a[$field] ?? false);
    }
    function mysql_real_escape_string($s, $link = null) { return substr(__compat_sqlite_pdo()->quote((string)$s), 1, -1); }
    function mysql_escape_string($s) { return mysql_real_escape_string($s); }
    function mysql_error($link = null) { return $GLOBALS['__compat_error'] ?? ''; }
    function mysql_insert_id($link = null) { return __compat_sqlite_pdo()->lastInsertId(); }
    function mysql_affected_rows($link = null) { return $GLOBALS['__compat_affected'] ?? 0; }
}
if (!function_exists('mysql_query')) {
    function __compat_mysql_link() {
        static $link = null;
        if ($link === null) {
            $link = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASS, SQL_DB, (int)SQL_PORT);
            mysqli_set_charset($link, 'utf8');
            mysqli_report(MYSQLI_REPORT_OFF);
        }
        return $link;
    }
    function mysql_query($q, $link = null) { return mysqli_query($link ?: __compat_mysql_link(), $q); }
    function mysql_fetch_assoc($r) { return $r ? mysqli_fetch_assoc($r) : false; }
    function mysql_fetch_array($r, $t = MYSQLI_BOTH) { return $r ? mysqli_fetch_array($r, $t) : false; }
    function mysql_fetch_row($r) { return $r ? mysqli_fetch_row($r) : false; }
    function mysql_num_rows($r) { return $r ? mysqli_num_rows($r) : 0; }
    function mysql_result($r, $row = 0, $field = 0) {
        if (!$r || !mysqli_data_seek($r, $row)) return false;
        $a = mysqli_fetch_array($r, MYSQLI_BOTH);
        return $a[$field] ?? false;
    }
    function mysql_real_escape_string($s, $link = null) { return mysqli_real_escape_string($link ?: __compat_mysql_link(), (string)$s); }
    function mysql_escape_string($s) { return mysql_real_escape_string($s); }
    function mysql_error($link = null) { return mysqli_error($link ?: __compat_mysql_link()); }
    function mysql_insert_id($link = null) { return mysqli_insert_id($link ?: __compat_mysql_link()); }
    function mysql_affected_rows($link = null) { return mysqli_affected_rows($link ?: __compat_mysql_link()); }
}

// PHP 8: GD functions throw on `false`. Map tile generators load many sprites; a missing sprite
// used to be silently skipped in PHP 7. Return a transparent 60x60 image instead and log the path.
if (!function_exists('compat_imagecreatefrom')) {
    function compat_imagecreatefrom($path, $kind = null) {
        static $cache = [];
        $key = is_string($path) ? $path : '';
        if (isset($cache[$key])) return $cache[$key];
        $img = false;
        if (is_string($path) && !is_file($path) && strpos($path, 'img/') === 0) {
            $alt = APP_MAIN_PATH.'/gpack/travian_default/'.$path; // some sprites only exist in the graphics pack
            if (is_file($alt)) $path = $alt;
        }
        if (is_string($path) && is_file($path)) {
            $kind = $kind ?: strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($kind === 'png') $img = @imagecreatefrompng($path);
            elseif ($kind === 'gif') $img = @imagecreatefromgif($path);
            elseif ($kind === 'jpg' || $kind === 'jpeg') $img = @imagecreatefromjpeg($path);
        }
        if (!$img) {
            error_log('[compat] missing map sprite: '.(is_string($path) ? $path : gettype($path)));
            $img = imagecreatetruecolor(60, 60);
            imagesavealpha($img, true);
            imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
        }
        return $cache[$key] = $img;
    }
}
