<?php
/*
 * Drop-in replacement for the phpredis `Redis` class, used where no Redis server exists (the iOS app).
 * Loaded through auto_prepend_file; does nothing when the real extension is present.
 *
 * Data lives in a MariaDB table (database OV_KV_DB, default "openvillage_cache") reached through the
 * same unix socket as the game. Implements the subset of the API the game uses: set (NX/EX/PX), setex,
 * setnx, get, exists, del, keys, incr, expire, ttl, eval (INCR+EXPIRE limiter script), ping, flushAll,
 * plus OPT_PREFIX. Semantics follow phpredis, including keys() returning prefixed names.
 */

if (!class_exists('Redis', false)) {
    final class RedisException extends RuntimeException
    {
    }

    final class Redis
    {
        public const OPT_SERIALIZER = 1;
        public const OPT_PREFIX = 2;
        public const OPT_READ_TIMEOUT = 3;
        public const SERIALIZER_NONE = 0;

        private static ?PDO $pdo = null;
        private string $prefix = '';

        private static function db(): PDO
        {
            if (self::$pdo instanceof PDO) {
                return self::$pdo;
            }
            $env = static function (string $name, string $default): string {
                $v = getenv($name);
                return $v === false || $v === '' ? $default : $v;
            };
            $database = $env('OV_KV_DB', 'openvillage_cache');
            if (!preg_match('/^[A-Za-z0-9_]+$/', $database)) {
                throw new RedisException('Invalid OV_KV_DB');
            }
            $pdo = new PDO(
                sprintf('mysql:host=%s;charset=utf8mb4', $env('GAME_DB_HOST', 'localhost')),
                $env('GAME_DB_USER', 'openvillage'),
                getenv('GAME_DB_PASSWORD') !== false ? getenv('GAME_DB_PASSWORD') : 'local-game-password',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
            $pdo->exec("USE `$database`");
            $pdo->exec('CREATE TABLE IF NOT EXISTS kv (
                k VARBINARY(512) NOT NULL PRIMARY KEY,
                v LONGBLOB NOT NULL,
                exp BIGINT NULL,
                KEY exp (exp)
            ) ENGINE=InnoDB');
            if (mt_rand(1, 200) === 1) {
                $pdo->exec('DELETE FROM kv WHERE exp IS NOT NULL AND exp <= UNIX_TIMESTAMP()');
            }
            return self::$pdo = $pdo;
        }

        private function k(string $key): string
        {
            return $this->prefix . $key;
        }

        private const LIVE = '(exp IS NULL OR exp > UNIX_TIMESTAMP())';

        // ---- connection -------------------------------------------------------------------
        public function connect($host = '', $port = 6379, $timeout = 0.0, ...$rest): bool
        {
            try {
                self::db();
            } catch (PDOException $e) {
                throw new RedisException('Cache database unavailable: ' . $e->getMessage(), 0, $e);
            }
            return true;
        }
        public function pconnect(...$args): bool { return $this->connect(); }
        public function open(...$args): bool { return $this->connect(); }
        public function auth($credentials): bool { return true; }
        public function select($db): bool { return true; }
        public function close(): bool { return true; }
        public function isConnected(): bool { return self::$pdo instanceof PDO; }

        public function ping($message = null)
        {
            self::db()->query('SELECT 1');
            return $message === null ? true : $message;
        }

        public function setOption($option, $value): bool
        {
            if ($option === self::OPT_PREFIX) {
                $this->prefix = (string)$value;
            }
            return true;
        }

        public function getOption($option)
        {
            return $option === self::OPT_PREFIX ? $this->prefix : null;
        }

        // ---- strings ----------------------------------------------------------------------
        /** @param array|int|null $options  ['nx'|'xx', 'ex' => s, 'px' => ms] or a TTL in seconds */
        public function set($key, $value, $options = null)
        {
            $nx = $xx = false;
            $ttl = null;
            if (is_int($options) || (is_string($options) && ctype_digit($options))) {
                $ttl = (int)$options;
            } elseif (is_array($options)) {
                foreach ($options as $k => $v) {
                    if (is_int($k)) {
                        $flag = strtolower((string)$v);
                        $nx = $nx || $flag === 'nx';
                        $xx = $xx || $flag === 'xx';
                    } elseif (strtolower($k) === 'ex') {
                        $ttl = max(1, (int)$v);
                    } elseif (strtolower($k) === 'px') {
                        $ttl = max(1, (int)ceil($v / 1000));
                    }
                }
            }
            $db = self::db();
            $key = $this->k((string)$key);
            $value = (string)$value;
            $exp = $ttl === null ? null : time() + $ttl;
            if ($nx) {
                // insert, or take over an expired row
                $st = $db->prepare('INSERT INTO kv (k, v, exp) VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        v   = IF(exp IS NOT NULL AND exp <= UNIX_TIMESTAMP(), VALUES(v), v),
                        exp = IF(exp IS NOT NULL AND exp <= UNIX_TIMESTAMP(), VALUES(exp), exp)');
                $st->execute([$key, $value, $exp]);
                return $st->rowCount() > 0;
            }
            if ($xx) {
                $st = $db->prepare('UPDATE kv SET v = ?, exp = ? WHERE k = ? AND ' . self::LIVE);
                $st->execute([$value, $exp, $key]);
                return $st->rowCount() > 0 || $this->exists(substr($key, strlen($this->prefix))) > 0;
            }
            $db->prepare('REPLACE INTO kv (k, v, exp) VALUES (?, ?, ?)')->execute([$key, $value, $exp]);
            return true;
        }

        public function setex($key, $ttl, $value): bool { return $this->set($key, $value, ['ex' => (int)$ttl]); }
        public function psetex($key, $ttlMs, $value): bool { return $this->set($key, $value, ['px' => (int)$ttlMs]); }
        public function setnx($key, $value): bool { return $this->set($key, $value, ['nx']); }

        public function get($key)
        {
            $st = self::db()->prepare('SELECT v FROM kv WHERE k = ? AND ' . self::LIVE);
            $st->execute([$this->k((string)$key)]);
            $v = $st->fetchColumn();
            return $v === false ? false : (string)$v;
        }

        public function mget(array $keys): array
        {
            return array_map(fn ($k) => $this->get($k), $keys);
        }

        /** @return int number of existing keys */
        public function exists($key, ...$more): int
        {
            $keys = is_array($key) ? $key : array_merge([$key], $more);
            if (!$keys) {
                return 0;
            }
            $keys = array_map(fn ($k) => $this->k((string)$k), $keys);
            $st = self::db()->prepare('SELECT COUNT(*) FROM kv WHERE k IN (' . implode(',', array_fill(0, count($keys), '?')) . ') AND ' . self::LIVE);
            $st->execute(array_values($keys));
            return (int)$st->fetchColumn();
        }

        public function del($key, ...$more): int
        {
            $keys = is_array($key) ? $key : array_merge([$key], $more);
            $keys = array_values(array_filter($keys, fn ($k) => $k !== null && $k !== ''));
            if (!$keys) {
                return 0;
            }
            $keys = array_map(fn ($k) => $this->k((string)$k), $keys);
            $st = self::db()->prepare('DELETE FROM kv WHERE k IN (' . implode(',', array_fill(0, count($keys), '?')) . ')');
            $st->execute($keys);
            return $st->rowCount();
        }
        public function unlink($key, ...$more): int { return $this->del($key, ...$more); }

        public function keys($pattern): array
        {
            $like = strtr($this->k((string)$pattern), ['\\' => '\\\\', '%' => '\\%', '_' => '\\_', '*' => '%', '?' => '_']);
            $st = self::db()->prepare('SELECT k FROM kv WHERE k LIKE ? AND ' . self::LIVE);
            $st->execute([$like]);
            return array_map('strval', $st->fetchAll(PDO::FETCH_COLUMN)); // phpredis returns prefixed names
        }

        public function incrBy($key, $by): int
        {
            $db = self::db();
            $key = $this->k((string)$key);
            $db->prepare('INSERT INTO kv (k, v, exp) VALUES (?, ?, NULL)
                ON DUPLICATE KEY UPDATE
                    v   = IF(exp IS NOT NULL AND exp <= UNIX_TIMESTAMP(), VALUES(v), CAST(CAST(v AS SIGNED) + ? AS CHAR)),
                    exp = IF(exp IS NOT NULL AND exp <= UNIX_TIMESTAMP(), NULL, exp)')
                ->execute([$key, (string)(int)$by, (int)$by]);
            $st = $db->prepare('SELECT v FROM kv WHERE k = ?');
            $st->execute([$key]);
            return (int)$st->fetchColumn();
        }
        public function incr($key, $by = 1): int { return $this->incrBy($key, $by); }
        public function decr($key, $by = 1): int { return $this->incrBy($key, -$by); }

        public function expire($key, $ttl): bool
        {
            $st = self::db()->prepare('UPDATE kv SET exp = UNIX_TIMESTAMP() + ? WHERE k = ? AND ' . self::LIVE);
            $st->execute([(int)$ttl, $this->k((string)$key)]);
            return $st->rowCount() > 0;
        }

        public function ttl($key): int
        {
            $st = self::db()->prepare('SELECT exp FROM kv WHERE k = ? AND ' . self::LIVE);
            $st->execute([$this->k((string)$key)]);
            $row = $st->fetch(PDO::FETCH_NUM);
            if ($row === false) {
                return -2;
            }
            return $row[0] === null ? -1 : max(0, (int)$row[0] - time());
        }

        /** Supports the rate-limiter script pattern: INCR KEYS[1] and EXPIRE it on first use. */
        public function eval($script, $args = [], $numKeys = 0)
        {
            if (preg_match("/redis\\.call\\('INCR',\\s*KEYS\\[1\\]\\)/i", $script) && $numKeys >= 1) {
                $db = self::db();
                $db->beginTransaction();
                try {
                    $n = $this->incrBy($args[0], 1);
                    if ($n === 1 && preg_match("/redis\\.call\\('EXPIRE',\\s*KEYS\\[1\\],\\s*(\\d+)\\)/i", $script, $m)) {
                        $this->expire($args[0], (int)$m[1]);
                    }
                    $db->commit();
                    return $n;
                } catch (Throwable $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
            throw new RedisException('Lua scripts are not supported by the local Redis replacement.');
        }

        public function flushAll($async = null): bool
        {
            self::db()->exec('DELETE FROM kv');
            return true;
        }
        public function flushDb($async = null): bool { return $this->flushAll(); }

        public function dbSize(): int
        {
            return (int)self::db()->query('SELECT COUNT(*) FROM kv WHERE ' . self::LIVE)->fetchColumn();
        }
    }
}
