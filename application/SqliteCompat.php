<?php
/**
 * SQLite backend for the game (used by the iOS build, optional elsewhere).
 *
 * The game was written for MySQL/MariaDB in non-strict mode. This class opens an SQLite
 * database and translates each query so that it behaves like it did on MySQL:
 *
 *  - string literals: MySQL backslash escapes and "double quoted strings" -> SQLite '...'
 *  - a / b            -> real division (MySQL never truncates; SQLite does for integers)
 *  - INSERT IGNORE    -> INSERT OR IGNORE,   FOR UPDATE / LOCK IN SHARE MODE removed
 *  - SET ... / SHOW … -> no-op,  START TRANSACTION -> BEGIN,  TRUNCATE -> DELETE FROM
 *  - INSERT with 0 / '' / NULL for an AUTO_INCREMENT column -> new id (MySQL behaviour)
 *  - '' written to a numeric column -> 0 (MySQL non-strict behaviour)
 *  - MySQL functions IF, FIND_IN_SET, POW, SQRT, RAND, FLOOR, CEIL, UNIX_TIMESTAMP, NOW,
 *    CONCAT, GREATEST, LEAST, FROM_UNIXTIME, MD5 … registered as SQLite functions
 */
class SqliteCompat
{
    /** @var array<string, array{cols: string[], types: string[], autoinc: ?string}> */
    private static $tables = [];

    public static function connect($path)
    {
        $pdo = new SqliteCompatPDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 15,
            // no STRINGIFY_FETCHES: like MySQL native prepared statements (DB.php), integers come back as int
        ]);
        $pdo->exec('PRAGMA journal_mode = WAL');
        $pdo->exec('PRAGMA synchronous = NORMAL');
        $pdo->exec('PRAGMA busy_timeout = 15000');
        $pdo->exec('PRAGMA case_sensitive_like = OFF');
        self::registerFunctions($pdo);
        return $pdo;
    }

    // ------------------------------------------------------------------ functions

    private static function truthy($v)
    {
        if ($v === null) return false;
        if (is_int($v) || is_float($v)) return $v != 0;
        return (float)$v != 0;   // MySQL: '' and non-numeric strings are 0 = false
    }

    private static function registerFunctions(PDO $pdo)
    {
        $det = defined('PDO::SQLITE_DETERMINISTIC') ? PDO::SQLITE_DETERMINISTIC : 0;
        $num = function ($v) { return $v === null ? null : (is_int($v) || is_float($v) ? $v : (float)$v); };
        $f = [
            'IF'            => [function ($c, $a, $b) { return self::truthy($c) ? $a : $b; }, 3, $det],
            'FIND_IN_SET'   => [function ($needle, $list) {
                                    if ($needle === null || $list === null) return null;
                                    $i = array_search((string)$needle, explode(',', (string)$list), true);
                                    return $i === false ? 0 : $i + 1; }, 2, $det],
            'POW'           => [function ($a, $b) use ($num) { return ($a === null || $b === null) ? null : pow($num($a), $num($b)); }, 2, $det],
            'POWER'         => [function ($a, $b) use ($num) { return ($a === null || $b === null) ? null : pow($num($a), $num($b)); }, 2, $det],
            'SQRT'          => [function ($a) use ($num) { return ($a === null || $num($a) < 0) ? null : sqrt($num($a)); }, 1, $det],
            'FLOOR'         => [function ($a) use ($num) { return $a === null ? null : (int)floor($num($a)); }, 1, $det],
            'CEIL'          => [function ($a) use ($num) { return $a === null ? null : (int)ceil($num($a)); }, 1, $det],
            'CEILING'       => [function ($a) use ($num) { return $a === null ? null : (int)ceil($num($a)); }, 1, $det],
            'MOD'           => [function ($a, $b) use ($num) { return ($a === null || !$b) ? null : fmod($num($a), $num($b)); }, 2, $det],
            'LOG'           => [function ($a) use ($num) { return ($a === null || $num($a) <= 0) ? null : log($num($a)); }, 1, $det],
            'RAND'          => [function () { return mt_rand() / mt_getrandmax(); }, -1, 0],
            'UNIX_TIMESTAMP'=> [function ($d = null) { return $d === null ? time() : (int)strtotime((string)$d); }, -1, 0],
            'NOW'           => [function () { return date('Y-m-d H:i:s'); }, 0, 0],
            'CURDATE'       => [function () { return date('Y-m-d'); }, 0, 0],
            'FROM_UNIXTIME' => [function ($t, $fmt = null) { return $t === null ? null : date('Y-m-d H:i:s', (int)$t); }, -1, $det],
            'CONCAT'        => [function (...$a) { foreach ($a as $x) if ($x === null) return null; return implode('', $a); }, -1, $det],
            'CONCAT_WS'     => [function ($sep, ...$a) { return implode((string)$sep, array_filter($a, function ($x) { return $x !== null; })); }, -1, $det],
            'GREATEST'      => [function (...$a) { foreach ($a as $x) if ($x === null) return null; return max($a); }, -1, $det],
            'LEAST'         => [function (...$a) { foreach ($a as $x) if ($x === null) return null; return min($a); }, -1, $det],
            'MD5'           => [function ($s) { return $s === null ? null : md5((string)$s); }, 1, $det],
            'LCASE'         => [function ($s) { return $s === null ? null : mb_strtolower((string)$s); }, 1, $det],
            'UCASE'         => [function ($s) { return $s === null ? null : mb_strtoupper((string)$s); }, 1, $det],
            'CHAR_LENGTH'   => [function ($s) { return $s === null ? null : mb_strlen((string)$s); }, 1, $det],
        ];
        foreach ($f as $name => [$fn, $argc, $flags]) {
            $pdo->sqliteCreateFunction($name, $fn, $argc, $flags);
        }
    }

    // ------------------------------------------------------------------ lexer

    /**
     * Splits SQL into tokens: ['code', text] ['str', value] ['ident', name] ['param', text] ['raw', text]
     */
    private static function lex($sql)
    {
        $tokens = [];
        $code = '';
        $n = strlen($sql);
        $flush = function () use (&$code, &$tokens) { if ($code !== '') { $tokens[] = ['code', $code]; $code = ''; } };
        for ($i = 0; $i < $n; $i++) {
            $c = $sql[$i];
            if ($c === "'" || $c === '"') {
                $flush();
                $q = $c; $val = '';
                for ($i++; $i < $n; $i++) {
                    $d = $sql[$i];
                    if ($d === '\\' && $i + 1 < $n) {
                        $e = $sql[++$i];
                        $map = ['n' => "\n", 'r' => "\r", 't' => "\t", '0' => "\0", 'Z' => "\x1a", 'b' => "\x08"];
                        if ($e === '%' || $e === '_') $val .= '\\' . $e;      // MySQL keeps these for LIKE
                        else $val .= $map[$e] ?? $e;
                    } elseif ($d === $q) {
                        if ($i + 1 < $n && $sql[$i + 1] === $q) { $val .= $q; $i++; }
                        else break;
                    } else {
                        $val .= $d;
                    }
                }
                $tokens[] = ['str', $val];
            } elseif ($c === '`') {
                $flush();
                $end = strpos($sql, '`', $i + 1);
                if ($end === false) $end = $n;
                $tokens[] = ['ident', substr($sql, $i + 1, $end - $i - 1)];
                $i = $end;
            } elseif ($c === '#' || ($c === '-' && substr($sql, $i, 3) === '-- ')) {
                $end = strpos($sql, "\n", $i);
                $i = $end === false ? $n : $end;
                $code .= ' ';
            } elseif ($c === '/' && $i + 1 < $n && $sql[$i + 1] === '*') {
                $end = strpos($sql, '*/', $i + 2);
                $i = $end === false ? $n : $end + 1;
                $code .= ' ';
            } elseif ($c === ':' && $i + 1 < $n && (ctype_alpha($sql[$i + 1]) || $sql[$i + 1] === '_') && ($i === 0 || $sql[$i - 1] !== ':')) {
                $flush();
                $j = $i + 1;
                while ($j < $n && (ctype_alnum($sql[$j]) || $sql[$j] === '_')) $j++;
                $tokens[] = ['param', substr($sql, $i, $j - $i)];
                $i = $j - 1;
            } elseif ($c === '?') {
                $flush();
                $tokens[] = ['param', '?'];
            } else {
                $code .= $c;
            }
        }
        $flush();
        return $tokens;
    }

    private static function emit(array $tokens)
    {
        $out = '';
        foreach ($tokens as [$type, $text]) {
            switch ($type) {
                case 'str':   $out .= "'" . str_replace("'", "''", $text) . "'"; break;
                case 'ident': $out .= '"' . str_replace('"', '""', $text) . '"'; break;
                default:      $out .= $text;
            }
        }
        return $out;
    }

    // ------------------------------------------------------------------ schema info

    private static function table(PDO $pdo, $name)
    {
        $key = strtolower($name);
        if (!array_key_exists($key, self::$tables)) {
            $info = null;
            $rows = $pdo->query('PRAGMA table_info("' . str_replace('"', '""', $name) . '")')->fetchAll(PDO::FETCH_ASSOC);
            if ($rows) {
                $sqlRow = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name=" . $pdo->quote($name))->fetchColumn();
                $info = ['cols' => [], 'types' => [], 'autoinc' => null];
                foreach ($rows as $r) {
                    $info['cols'][] = $r['name'];
                    $info['types'][strtolower($r['name'])] = strtoupper($r['type']);
                    if ($r['pk'] && strtoupper($r['type']) === 'INTEGER' && stripos((string)$sqlRow, 'AUTOINCREMENT') !== false
                        && preg_match('/"' . preg_quote($r['name'], '/') . '"\s+INTEGER\s+PRIMARY\s+KEY\s+AUTOINCREMENT/i', (string)$sqlRow)) {
                        $info['autoinc'] = $r['name'];
                    }
                }
            }
            self::$tables[$key] = $info;
        }
        return self::$tables[$key];
    }

    // ------------------------------------------------------------------ translation

    /**
     * Returns the SQLite version of $sql, or null when the statement is a MySQL-only no-op.
     * $params (named PDO parameters) may be modified.
     */
    public static function translate(PDO $pdo, $sql, &$params)
    {
        $trim = ltrim($sql);
        if (preg_match('/^(SET|SHOW|LOCK|UNLOCK|OPTIMIZE|ANALYZE|FLUSH|USE)\b/i', $trim)) return null;

        $tokens = self::lex($sql);

        // Rewrites inside plain SQL code (never inside strings / identifiers / comments).
        foreach ($tokens as $k => [$type, $text]) {
            if ($type !== 'code') continue;
            $text = preg_replace('/\bFOR\s+UPDATE\b/i', '', $text);
            $text = preg_replace('/\bLOCK\s+IN\s+SHARE\s+MODE\b/i', '', $text);
            $text = preg_replace('/\b(?:USE|FORCE|IGNORE)\s+(?:INDEX|KEY)\s*\([^)]*\)/i', '', $text);
            $text = preg_replace('/\b(?:SQL_CACHE|SQL_NO_CACHE|HIGH_PRIORITY|LOW_PRIORITY|DELAYED|SQL_SMALL_RESULT|SQL_BIG_RESULT)\b/i', '', $text);
            $text = preg_replace('/^(\s*)INSERT\s+IGNORE\b/i', '$1INSERT OR IGNORE', $text);
            $text = preg_replace('/^(\s*)START\s+TRANSACTION\b/i', '$1BEGIN', $text);
            $text = preg_replace('/^(\s*)TRUNCATE\s+(TABLE\s+)?/i', '$1DELETE FROM ', $text);
            $text = str_replace('/', '*1.0/', $text);
            // MySQL logical operators (|| is OR unless PIPES_AS_CONCAT is set).
            $text = str_replace(['&&', '||'], [' AND ', ' OR '], $text);
            // MySQL accepts keywords after a dot (movement.from, m.to); SQLite needs them quoted.
            $text = preg_replace('/(?<![\w."])([A-Za-z_]\w*)\.([A-Za-z_]\w*)\b/', '$1."$2"', $text);
            $tokens[$k][1] = $text;
        }

        // Skeleton: SQL code with quoted identifiers; every literal/parameter replaced by a marker.
        $skel = '';
        foreach ($tokens as $k => [$type, $text]) {
            if ($type === 'str' || $type === 'param') $skel .= "\x01" . $k . "\x02";
            elseif ($type === 'ident') $skel .= '"' . str_replace('"', '""', $text) . '"';
            else $skel .= $text;
        }

        // MySQL "INSERT INTO t SET a = 1, b = 2" -> "INSERT INTO t (a, b) VALUES (1, 2)"
        if (preg_match('/^(\s*(?:INSERT(?:\s+OR\s+IGNORE)?|REPLACE)\s+INTO\s+"?\w+"?)\s+SET\s+(.*)$/is', $skel, $m)) {
            $cols = []; $vals = [];
            foreach (self::splitTop($m[2]) as $assign) {
                if (!preg_match('/^\s*"?(\w+)"?\s*=(.*)$/s', $assign, $a)) { $cols = null; break; }
                $cols[] = '"' . $a[1] . '"'; $vals[] = trim($a[2]);
            }
            if ($cols) $skel = $m[1] . ' (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $vals) . ')';
        }

        if (preg_match('/^(\s*(?:INSERT(?:\s+OR\s+IGNORE)?|REPLACE)\s+INTO\s+"?(\w+)"?\s*(?:\(([^)]*)\))?\s*VALUES\s*)(.*)$/is', $skel, $m)) {
            $skel = $m[1] . self::fixInsert($pdo, $tokens, $params, $m[2], $m[3] ?? '', $m[4]);
        } elseif (preg_match('/^(\s*UPDATE\s+"?(\w+)"?\s+SET\s+)(.*?)((?:\s+WHERE\s+.*)?)$/is', $skel, $m)) {
            $skel = $m[1] . self::fixUpdate($pdo, $tokens, $params, $m[2], $m[3]) . $m[4];
        }

        // Expand markers back into SQLite literals / parameters.
        return preg_replace_callback('/\x01(\d+)\x02/', function ($mm) use ($tokens) {
            return self::emit([$tokens[(int)$mm[1]]]);
        }, $skel);
    }

    /** Splits "a, (b, c), d" at top-level commas. */
    private static function splitTop($s)
    {
        $parts = []; $depth = 0; $cur = '';
        $len = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $c = $s[$i];
            if ($c === '(') $depth++;
            elseif ($c === ')') $depth--;
            if ($c === ',' && $depth === 0) { $parts[] = $cur; $cur = ''; continue; }
            $cur .= $c;
        }
        $parts[] = $cur;
        return $parts;
    }

    /**
     * Literal value of a VALUES / SET element: a string or bound parameter marker, a bare 0 / NULL / number.
     * Returns false when the element is an expression (left untouched).
     */
    private static function elementValue(array $tokens, $params, $element)
    {
        $t = trim($element);
        if (preg_match('/^\x01(\d+)\x02$/', $t, $mm)) {
            [$type, $text] = $tokens[(int)$mm[1]];
            if ($type === 'str') return $text;
            if ($text === '?') return false;
            $name = substr($text, 1);
            if (is_array($params)) {
                if (array_key_exists($name, $params)) return $params[$name];
                if (array_key_exists(':' . $name, $params)) return $params[':' . $name];
            }
            return false;
        }
        if (strcasecmp($t, 'NULL') === 0) return null;
        if (preg_match('/^-?\d+(\.\d+)?$/', $t)) return $t;
        return false;
    }

    /** Replace an element; a dropped named parameter is removed from $params so PDO does not complain. */
    private static function replaceElement(array $tokens, &$params, $element, $raw)
    {
        if (preg_match('/^\s*\x01(\d+)\x02\s*$/', $element, $mm) && $tokens[(int)$mm[1]][0] === 'param' && is_array($params)) {
            $name = substr($tokens[(int)$mm[1]][1], 1);
            unset($params[$name], $params[':' . $name]);
        }
        preg_match('/^(\s*).*?(\s*)$/s', $element, $ws);
        return $ws[1] . $raw . $ws[2];
    }

    private static function isNumericType($type)
    {
        return in_array($type, ['INTEGER', 'REAL', 'NUMERIC'], true);
    }

    private static function fixInsert(PDO $pdo, array $tokens, &$params, $table, $colList, $valuesPart)
    {
        $info = self::table($pdo, $table);
        if (!$info) return $valuesPart;
        $cols = trim($colList) === '' ? $info['cols'] : array_map(function ($c) { return trim($c, " \t\n\r\"`"); }, explode(',', $colList));

        // VALUES (..), (..) [tail]
        $out = ''; $depth = 0; $start = null; $len = strlen($valuesPart); $i = 0;
        for (; $i < $len; $i++) {
            $c = $valuesPart[$i];
            if ($c === '(') {
                if ($depth === 0) { $start = $i + 1; $out .= '('; }
                $depth++;
                if ($depth === 1) continue;
            } elseif ($c === ')') {
                $depth--;
                if ($depth === 0 && $start !== null) {
                    $elements = self::splitTop(substr($valuesPart, $start, $i - $start));
                    foreach ($elements as $pos => $element) {
                        if (!isset($cols[$pos])) continue;
                        $col = $cols[$pos];
                        $value = self::elementValue($tokens, $params, $element);
                        if ($value === false) continue;
                        if ($info['autoinc'] !== null && strcasecmp($col, $info['autoinc']) === 0) {
                            if ($value === null || $value === '' || (is_numeric($value) && (float)$value == 0)) {
                                $elements[$pos] = self::replaceElement($tokens, $params, $element, 'NULL');
                            }
                        } elseif ($value === '' && self::isNumericType($info['types'][strtolower($col)] ?? '')) {
                            $elements[$pos] = self::replaceElement($tokens, $params, $element, '0');
                        }
                    }
                    $out .= implode(',', $elements) . ')';
                    $start = null;
                    continue;
                }
            } elseif ($depth === 0 && !ctype_space($c) && $c !== ',') {
                break;   // tail such as ON DUPLICATE KEY …
            }
            if ($depth === 0) $out .= $c;
        }
        return $out . substr($valuesPart, $i);
    }

    private static function fixUpdate(PDO $pdo, array $tokens, &$params, $table, $setPart)
    {
        $info = self::table($pdo, $table);
        if (!$info) return $setPart;
        $parts = self::splitTop($setPart);
        foreach ($parts as $k => $assign) {
            if (!preg_match('/^(\s*(?:\w+\.)?"?(\w+)"?\s*=)(.*)$/s', $assign, $m)) continue;
            if (!self::isNumericType($info['types'][strtolower($m[2])] ?? '')) continue;
            if (self::elementValue($tokens, $params, $m[3]) === '') {
                $parts[$k] = $m[1] . self::replaceElement($tokens, $params, $m[3], '0');
            }
        }
        return implode(',', $parts);
    }
}

/**
 * PDO for code that talks to PDO directly ($baglanDB): every statement is translated like DB.php does.
 * DB.php translates itself (it knows the bound parameters) and uses prepareRaw().
 */
class SqliteCompatPDO extends PDO
{
    private function tr($sql)
    {
        $params = [];
        $t = SqliteCompat::translate($this, $sql, $params);
        return $t === null ? 'SELECT 1 WHERE 0' : $t;
    }

    #[\ReturnTypeWillChange]
    public function prepare($query, $options = [])
    {
        return parent::prepare($this->tr($query), $options);
    }

    public function prepareRaw($query)
    {
        return parent::prepare($query);
    }

    #[\ReturnTypeWillChange]
    public function query($query, $fetchMode = null, ...$fetchModeArgs)
    {
        $query = $this->tr($query);
        return $fetchMode === null ? parent::query($query) : parent::query($query, $fetchMode, ...$fetchModeArgs);
    }

    #[\ReturnTypeWillChange]
    public function exec($statement)
    {
        return parent::exec($this->tr($statement));
    }
}
