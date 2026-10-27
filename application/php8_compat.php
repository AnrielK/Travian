<?php
/**
 * PHP 8 compatibility helpers.
 *
 * The game was written for PHP 5/7, where many built-in functions only raised a
 * warning (and carried on) when they received null, false or a scalar instead of
 * an array. PHP 8 throws a TypeError in those cases and the whole page dies.
 * These helpers reproduce the old PHP 7 results without the warnings.
 *
 * Loaded from application/config.php (and from the few scripts that run without it).
 */

if (defined('PHP8_COMPAT_LOADED')) {
    return;
}
define('PHP8_COMPAT_LOADED', true);

// PHP 8: deprecation notices were never emitted by PHP 7; keep them out of the pages
// even with an old config.php (which only hides warnings and notices).
error_reporting(error_reporting() & ~E_DEPRECATED);

// PHP 8.1+: mysqli throws exceptions by default. The code checks return values instead.
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

if (!function_exists('safe_count')) {
    /**
     * count() with PHP 7 semantics: null => 0, any other non-countable value => 1.
     * (PHP 8 throws "count(): Argument #1 must be of type Countable|array".)
     */
    function safe_count($value, $mode = COUNT_NORMAL)
    {
        if (is_array($value) || $value instanceof Countable) {
            return count($value, $mode);
        }
        return $value === null ? 0 : 1;
    }
}

if (!function_exists('safe_num')) {
    /**
     * Numeric value of a loosely typed input with PHP 7 semantics.
     * "" / null / "abc" => 0, "12abc" => 12, "1.5" => 1.5.
     * (PHP 8 throws "Unsupported operand types: string + int" for non-numeric strings.)
     */
    function safe_num($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        if (is_bool($value) || $value === null) {
            return (int)$value;
        }
        if (is_array($value)) {
            return 0;
        }
        if (is_object($value) && !method_exists($value, '__toString')) {
            return $value; // not a number-like value, leave it alone
        }
        $value = trim((string)$value);
        if (is_numeric($value)) {
            return $value + 0;
        }
        if (preg_match('/^[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?/', $value, $m)) {
            return $m[0] + 0;
        }
        return 0;
    }
}

if (!function_exists('filter_string_polyfill')) {
    /**
     * Replacement for the removed FILTER_SANITIZE_STRING filter (deprecated in PHP 8.1):
     * strips tags and encodes quotes, exactly like the old filter did.
     */
    function filter_string_polyfill($value)
    {
        if ($value === null || $value === false) {
            return $value;
        }
        $str = preg_replace('/\x00|<[^>]*>?/', '', (string)$value);
        return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
    }
}

if (!function_exists('safe_constant')) {
    /**
     * constant() with PHP 7 semantics: returns null for an undefined constant.
     * (PHP 8 throws Error "Undefined constant".)
     */
    function safe_constant($name)
    {
        return defined($name) ? constant($name) : null;
    }
}

if (!function_exists('filter_string_array_polyfill')) {
    /**
     * Replacement for filter_string_array_polyfill($data).
     */
    function filter_string_array_polyfill($data)
    {
        if (!is_array($data)) {
            return filter_string_polyfill($data);
        }
        foreach ($data as $k => $v) {
            $data[$k] = is_array($v) ? filter_string_array_polyfill($v) : filter_string_polyfill($v);
        }
        return $data;
    }
}

if (!function_exists('php7_is_zero')) {
    /**
     * PHP 7 result of ($value == 0).
     * PHP 8 changed string/number comparison: "" == 0 and "abc" == 0 are now false.
     */
    function php7_is_zero($value)
    {
        if (is_string($value) && !is_numeric($value)) {
            return safe_num($value) == 0;
        }
        return $value == 0;
    }
}

if (!function_exists('php7_is_blank')) {
    /**
     * PHP 7 result of ($value == '').
     * PHP 8 changed string/number comparison: 0 == '' is now false.
     */
    function php7_is_blank($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value == 0;
        }
        return $value == '';
    }
}

if (!function_exists('safe_div')) {
    /**
     * Division with PHP 7 semantics: dividing by zero returned INF / -INF / NAN with a warning,
     * PHP 8 throws DivisionByZeroError. Non-numeric strings are treated as numbers like PHP 7 did.
     */
    function safe_div($a, $b)
    {
        if (!is_int($a) && !is_float($a)) {
            $a = safe_num($a);
        }
        if (!is_int($b) && !is_float($b)) {
            $b = safe_num($b);
        }
        if ($b == 0) {
            return $a > 0 ? INF : ($a < 0 ? -INF : NAN);
        }
        return $a / $b;
    }
}

if (!function_exists('php7_ts')) {
    /**
     * Timestamp argument for date()/gmdate() with PHP 7 semantics.
     * PHP 8 treats date($format, null) as "now"; PHP 7 converted null to 0 (1970-01-01).
     * Floats and numeric strings are truncated to int as before (no PHP 8.1 deprecation).
     */
    function php7_ts($value)
    {
        if ($value === null || $value === false || $value === '') {
            return 0;
        }
        $n = safe_num($value);
        if (is_float($n) && !is_finite($n)) {
            return 0;
        }
        return (int)$n;
    }
}

if (!function_exists('safe_max')) {
    /**
     * max()/min() with PHP 7 semantics for the single-argument form:
     * an empty array (or a non-array) returned false with a warning, PHP 8 throws ValueError/TypeError.
     */
    function safe_max(...$args)
    {
        if (count($args) === 1) {
            if (!is_array($args[0]) || count($args[0]) === 0) {
                return false;
            }
            return max($args[0]);
        }
        return max(...$args);
    }

    function safe_min(...$args)
    {
        if (count($args) === 1) {
            if (!is_array($args[0]) || count($args[0]) === 0) {
                return false;
            }
            return min($args[0]);
        }
        return min(...$args);
    }
}

if (!function_exists('safe_array_rand')) {
    /**
     * array_rand() with PHP 7 semantics: empty array / too many requested keys returned null.
     */
    function safe_array_rand($array, $num = 1)
    {
        if (!is_array($array) || count($array) === 0 || $num < 1 || $num > count($array)) {
            return null;
        }
        return array_rand($array, $num);
    }
}

if (!function_exists('safe_strpos')) {
    /**
     * strpos() with PHP 7 semantics: an offset outside the string returned false,
     * PHP 8 throws ValueError.
     */
    function safe_strpos($haystack, $needle, $offset = 0)
    {
        $haystack = (string)$haystack;
        if ($offset > strlen($haystack) || -$offset > strlen($haystack)) {
            return false;
        }
        return strpos($haystack, (string)$needle, $offset);
    }
}

if (!function_exists('safe_implode')) {
    /**
     * implode() with PHP 7 semantics: a non-array argument returned null with a warning
     * (PHP 8 throws TypeError). Also accepts the legacy (array, glue) argument order.
     */
    function safe_implode($glue, $pieces = null)
    {
        if ($pieces === null && func_num_args() === 1) {
            return is_array($glue) ? implode($glue) : null;
        }
        if (is_array($glue) && !is_array($pieces)) {
            [$glue, $pieces] = [$pieces, $glue];
        }
        if (!is_array($pieces)) {
            return null;
        }
        return implode((string)$glue, $pieces);
    }
}

if (!function_exists('safe_in_array')) {
    /**
     * in_array() / array_sum() / array_push() with PHP 7 semantics: a non-array argument
     * produced a warning and returned null instead of throwing a TypeError.
     */
    function safe_in_array($needle, $haystack, $strict = false)
    {
        return is_array($haystack) ? in_array($needle, $haystack, $strict) : null;
    }

    function safe_array_sum($array)
    {
        return is_array($array) ? array_sum($array) : null;
    }

    function safe_array_push(&$array, ...$values)
    {
        if (!is_array($array)) {
            return null;
        }
        return array_push($array, ...$values);
    }
}

if (!function_exists('php8_define_fallbacks')) {
    /**
     * Constants that the code references but that were never defined anywhere.
     * PHP 7 silently used the constant name as a string; PHP 8 throws "Undefined constant".
     * Called from DB.php (i.e. right after config.php has defined the SQL_* settings),
     * so an existing config.php from a PHP 7 installation keeps working unchanged.
     */
    function php8_define_fallbacks()
    {
        $fallbacks = array(
            'TB_PREFIX'     => '',
            'DBSERVIDORES'  => defined('SQL_DB') ? SQL_DB : '',
            'SELL_RES'      => false, // sell resources for gold (Plus page)
            'SELL_CP'       => false, // sell culture points for gold (Plus page)
            'COSTRES'       => 10,
            'COSTCP'        => 20,
            'HOWCP'         => 2500,
            'MINPROTECTION' => 0,
            'NATARS_MAX'    => 22,
            'WW'            => true,
            'xQUEST'        => 1,
            'GP_LOCATION2'  => 'img/', // cropfinder icons (img/x.gif)
        );
        foreach ($fallbacks as $name => $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
    }
}

if (!function_exists('safe_imagecopyresized')) {
    /**
     * GD functions with PHP 7 semantics: when an image file is missing, imagecreatefrom*()
     * returns false. PHP 7 only warned and skipped that piece; PHP 8 throws a TypeError and the
     * whole picture (map tile, hero portrait) is lost. These wrappers skip the piece like PHP 7.
     */
    function php8_is_gd($img)
    {
        return $img instanceof GdImage || is_resource($img);
    }

    function safe_imagecopyresized($dst, $src, $dx, $dy, $sx, $sy, $dw, $dh, $sw, $sh)
    {
        if (!php8_is_gd($dst) || !php8_is_gd($src)) {
            return false;
        }
        return imagecopyresized($dst, $src, (int)$dx, (int)$dy, (int)$sx, (int)$sy, (int)$dw, (int)$dh, (int)$sw, (int)$sh);
    }

    function safe_imagecopy($dst, $src, $dx, $dy, $sx, $sy, $sw, $sh)
    {
        if (!php8_is_gd($dst) || !php8_is_gd($src)) {
            return false;
        }
        return imagecopy($dst, $src, (int)$dx, (int)$dy, (int)$sx, (int)$sy, (int)$sw, (int)$sh);
    }

    function safe_imagesx($img)
    {
        return php8_is_gd($img) ? imagesx($img) : false;
    }

    function safe_imagesy($img)
    {
        return php8_is_gd($img) ? imagesy($img) : false;
    }

    function safe_imagedestroy($img)
    {
        return php8_is_gd($img) ? imagedestroy($img) : false;
    }
}

if (!function_exists('safe_imagecreatefrompng')) {
    /**
     * imagecreatefrom*() with PHP 7 semantics: an empty path returned false with a warning,
     * PHP 8 throws ValueError "Path cannot be empty".
     */
    function safe_imagecreatefrompng($file)
    {
        return ((string)$file === '' || !is_file($file)) ? false : imagecreatefrompng($file);
    }

    function safe_imagecreatefromgif($file)
    {
        return ((string)$file === '' || !is_file($file)) ? false : imagecreatefromgif($file);
    }

    function safe_imagecreatefromjpeg($file)
    {
        return ((string)$file === '' || !is_file($file)) ? false : imagecreatefromjpeg($file);
    }
}

if (!function_exists('safe_mysqli_fetch_array')) {
    /**
     * mysqli_fetch_*() / mysqli_num_rows() with PHP 7 semantics: a failed query (false)
     * produced a warning and null; PHP 8 throws a TypeError.
     */
    function safe_mysqli_fetch_array($result, $mode = MYSQLI_BOTH)
    {
        return $result instanceof mysqli_result ? mysqli_fetch_array($result, $mode) : null;
    }

    function safe_mysqli_fetch_assoc($result)
    {
        return $result instanceof mysqli_result ? mysqli_fetch_assoc($result) : null;
    }

    function safe_mysqli_num_rows($result)
    {
        return $result instanceof mysqli_result ? mysqli_num_rows($result) : null;
    }
}

/* ------------------------------------------------------------------------------------------
 * SQL injection protection for the legacy concatenated queries.
 * The code builds SQL by gluing request values into strings. Every such value is now passed
 * through one of these helpers at the place where the SQL text is assembled:
 *   '... = '" . sql_esc($x) . "'"   value inside quotes  -> escaped like mysqli_real_escape_string
 *   "... id = " . sql_num($x)          numeric position     -> only a number can get through
 * ------------------------------------------------------------------------------------------ */
if (!function_exists('sql_esc')) {
    /**
     * Escape a value for use inside a quoted SQL string literal ('...' or "...").
     * Same rules as mysql_real_escape_string(); safe for the utf8/utf8mb4 connections used here.
     * null/false become '' exactly like plain string interpolation did before.
     */
    function sql_esc($value)
    {
        if (is_array($value)) {
            return '';
        }
        if (is_bool($value)) {
            $value = $value ? '1' : '';
        } elseif (is_object($value) && !method_exists($value, '__toString')) {
            return '';
        }
        if (function_exists('__sqlsafe_hook') && strpos((string)$value, "\\'") !== false) {
            __sqlsafe_hook('sql_esc', $value); // test harness only: value looks already escaped
        }
        return strtr((string)$value, array(
            "\\" => "\\\\", "\0" => "\\0", "\n" => "\\n", "\r" => "\\r",
            "'" => "\\'", '"' => '\\"', "\x1a" => "\\Z",
        ));
    }
}

if (!function_exists('sql_num')) {
    /**
     * Value for a numeric position in SQL (WHERE id = ..., LIMIT ..., SET x = x + ...).
     * Integers and floats pass through, numeric strings are converted, anything else
     * (e.g. "1 OR 1=1", "", null) becomes 0, so no SQL can be injected.
     */
    function sql_num($value)
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            return is_finite($value) ? $value : 0;
        }
        if (is_bool($value) || $value === null) {
            return (int)$value;
        }
        if (is_string($value)) {
            $v = trim($value);
            if (is_numeric($v)) {
                return $v + 0;
            }
        }
        if (function_exists('__sqlsafe_hook') && $value !== '') {
            __sqlsafe_hook('sql_num', $value); // test harness only
        }
        return 0;
    }
}

if (!function_exists('sql_id')) {
    /**
     * Whitelist an SQL identifier (column / table name) built from a variable.
     */
    function sql_id($name, array $allowed = null)
    {
        $name = (string)$name;
        if ($allowed !== null) {
            return in_array($name, $allowed, true) ? $name : '__invalid_identifier__';
        }
        // an invalid name becomes a column that does not exist: the query fails, nothing is injected
        return preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) ? $name : '__invalid_identifier__';
    }
}

if (!function_exists('sql_in_ints')) {
    /**
     * Comma separated list of integers for "IN (...)"; accepts an array or a "1,2,3" string.
     */
    function sql_in_ints($list)
    {
        if (!is_array($list)) {
            $list = explode(',', (string)$list);
        }
        $out = array();
        foreach ($list as $v) {
            $v = trim((string)$v, " '\"");
            if ($v !== '' && is_numeric($v)) {
                $out[] = $v + 0;
            }
        }
        return $out ? implode(',', $out) : '0';
    }
}

if (!function_exists('sql_frag')) {
    /**
     * Marks an SQL fragment that the code has already validated against a strict pattern
     * (e.g. "2 or size = 3"). Returns it unchanged; it exists so the intent is visible.
     */
    function sql_frag($fragment)
    {
        return (string)$fragment;
    }
}

if (!function_exists('password_md5_equals')) {
    /**
     * Constant-time comparison of a stored md5 password hash with a computed one.
     * The old code used ==, where two different hashes of the form "0e123..." compare as equal
     * numbers ("magic hashes"), so a wrong password could be accepted.
     */
    function password_md5_equals($stored, $computed)
    {
        if (!is_string($stored) || $stored === '' || !is_string($computed)) {
            return false;
        }
        return hash_equals($stored, $computed);
    }
}
