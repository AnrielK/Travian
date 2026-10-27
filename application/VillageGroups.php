<?php
/**
 * Village groups of the Travian Legends village list ("Skupiny vesnic (1/20)").
 *
 * A player keeps up to MAX groups (name + highlight colour, collapsed or not); a village belongs to at most one
 * group, villages without a group are listed under the groups. Tables are created on first use (SQLite and MySQL),
 * so existing worlds need no migration:
 *   ov_village_groups        uid, gid, name, color, sort, collapsed
 *   ov_village_group_members uid, wref, gid
 */
final class VillageGroups
{
    const MAX = 20;
    const COLORS = ['grey', 'yellow', 'green', 'turquoise', 'blue', 'violet', 'magenta', 'red', 'orange'];

    private static $ready = false;

    private static function db(): ?PDO
    {
        $pdo = function_exists('travian_pdo') ? travian_pdo() : null;
        return $pdo instanceof PDO ? $pdo : null;
    }

    public static function ensureTables(): bool
    {
        if (self::$ready) return true;
        if (!($db = self::db())) return false;
        try {
            $db->exec('CREATE TABLE IF NOT EXISTS ov_village_groups (
                uid INT NOT NULL,
                gid INT NOT NULL,
                name VARCHAR(40) NOT NULL DEFAULT \'\',
                color VARCHAR(12) NOT NULL DEFAULT \'grey\',
                sort INT NOT NULL DEFAULT 0,
                collapsed INT NOT NULL DEFAULT 0,
                PRIMARY KEY (uid, gid)
            )');
            $db->exec('CREATE TABLE IF NOT EXISTS ov_village_group_members (
                uid INT NOT NULL,
                wref INT NOT NULL,
                gid INT NOT NULL,
                PRIMARY KEY (uid, wref)
            )');
        } catch (Throwable $e) {
            error_log('village groups: ' . $e->getMessage());
            return false;
        }
        return self::$ready = true;
    }

    private static function run(string $sql, array $args = [])
    {
        if (!self::ensureTables()) return false;
        try {
            $st = self::db()->prepare($sql);
            $st->execute($args);
            return $st;
        } catch (Throwable $e) {
            error_log('village groups: ' . $e->getMessage());
            return false;
        }
    }

    /** Groups of a player in their order: gid => [gid, name, color, collapsed]. */
    public static function groups(int $uid): array
    {
        $out = [];
        $st = self::run('SELECT gid, name, color, collapsed FROM ov_village_groups WHERE uid = ? ORDER BY sort, gid', [$uid]);
        if ($st) {
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $out[(int)$r['gid']] = ['gid' => (int)$r['gid'], 'name' => (string)$r['name'],
                    'color' => in_array($r['color'], self::COLORS, true) ? $r['color'] : 'grey', 'collapsed' => (bool)$r['collapsed']];
            }
        }
        return $out;
    }

    /** wref => gid of the player's grouped villages. */
    public static function members(int $uid): array
    {
        $out = [];
        $st = self::run('SELECT wref, gid FROM ov_village_group_members WHERE uid = ?', [$uid]);
        if ($st) {
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) $out[(int)$r['wref']] = (int)$r['gid'];
        }
        return $out;
    }

    /** Creates a group, returns its id (null when the player has MAX groups already). */
    public static function add(int $uid, string $name): ?int
    {
        $groups = self::groups($uid);
        if (count($groups) >= self::MAX) return null;
        $gid = $groups ? max(array_keys($groups)) + 1 : 1;
        $name = self::cleanName($name);
        if ($name === '') $name = defined('VG_NEW_GROUP') ? VG_NEW_GROUP : 'New group';
        $ok = self::run('INSERT INTO ov_village_groups (uid, gid, name, color, sort, collapsed) VALUES (?, ?, ?, ?, ?, 0)',
            [$uid, $gid, $name, 'grey', $gid]);
        return $ok ? $gid : null;
    }

    public static function rename(int $uid, int $gid, string $name): void
    {
        $name = self::cleanName($name);
        if ($name !== '') self::run('UPDATE ov_village_groups SET name = ? WHERE uid = ? AND gid = ?', [$name, $uid, $gid]);
    }

    public static function color(int $uid, int $gid, string $color): void
    {
        if (in_array($color, self::COLORS, true)) self::run('UPDATE ov_village_groups SET color = ? WHERE uid = ? AND gid = ?', [$color, $uid, $gid]);
    }

    public static function collapse(int $uid, int $gid, bool $collapsed): void
    {
        self::run('UPDATE ov_village_groups SET collapsed = ? WHERE uid = ? AND gid = ?', [$collapsed ? 1 : 0, $uid, $gid]);
    }

    /** Deletes a group; its villages go back to the ungrouped list. */
    public static function delete(int $uid, int $gid): void
    {
        self::run('DELETE FROM ov_village_group_members WHERE uid = ? AND gid = ?', [$uid, $gid]);
        self::run('DELETE FROM ov_village_groups WHERE uid = ? AND gid = ?', [$uid, $gid]);
    }

    /** Puts one of the player's villages into a group (gid 0 = no group). */
    public static function assign(int $uid, int $wref, int $gid, array $ownVillages): void
    {
        if (!in_array($wref, array_map('intval', $ownVillages), true)) return;
        self::run('DELETE FROM ov_village_group_members WHERE uid = ? AND wref = ?', [$uid, $wref]);
        if ($gid > 0 && isset(self::groups($uid)[$gid])) {
            self::run('INSERT INTO ov_village_group_members (uid, wref, gid) VALUES (?, ?, ?)', [$uid, $wref, $gid]);
        }
    }

    private static function cleanName(string $name): string
    {
        $name = trim(preg_replace('/\s+/u', ' ', strip_tags($name)));
        return function_exists('mb_substr') ? mb_substr($name, 0, 40) : substr($name, 0, 40);
    }

    /** ajax.php?cmd=villageGroups: action add|rename|color|collapse|delete|assign; answers the new state as JSON. */
    public static function ajax(int $uid, array $ownVillages, array $post): array
    {
        $gid = (int)($post['gid'] ?? 0);
        switch ($post['action'] ?? '') {
            case 'add':      $gid = (int)self::add($uid, (string)($post['name'] ?? '')); break;
            case 'rename':   self::rename($uid, $gid, (string)($post['name'] ?? '')); break;
            case 'color':    self::color($uid, $gid, (string)($post['color'] ?? '')); break;
            case 'collapse': self::collapse($uid, $gid, !empty($post['collapsed'])); break;
            case 'delete':   self::delete($uid, $gid); break;
            case 'assign':   self::assign($uid, (int)($post['wref'] ?? 0), $gid, $ownVillages); break;
        }
        return ['gid' => $gid, 'groups' => array_values(self::groups($uid)), 'members' => self::members($uid)];
    }
}
