<?php
/**
 * Village list of the Travian Legends look: village groups ("Skupiny vesnic (1/20)") and the quick link slots.
 *
 * The list is one ordered sequence of top-level entries – groups (with their villages) and villages without a group –
 * which the player rearranges by dragging. A new group goes to the end of the list. Up to MAX groups, each with a
 * name, a highlight colour and a collapsed flag. Tables are created on first use (SQLite and MySQL):
 *   ov_village_groups        uid, gid, name, color, sort, collapsed
 *   ov_village_group_members uid, wref, gid (0 = top level), sort
 *   ov_user_prefs            uid, name, value (quick links of the village list)
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
                sort INT NOT NULL DEFAULT 0,
                PRIMARY KEY (uid, wref)
            )');
            $db->exec('CREATE TABLE IF NOT EXISTS ov_user_prefs (
                uid INT NOT NULL,
                name VARCHAR(40) NOT NULL,
                value TEXT,
                PRIMARY KEY (uid, name)
            )');
        } catch (Throwable $e) {
            error_log('village groups: ' . $e->getMessage());
            return false;
        }
        // tables of the first version had no sort column for villages
        try {
            $db->query('SELECT sort FROM ov_village_group_members LIMIT 1');
        } catch (Throwable $e) {
            try { $db->exec('ALTER TABLE ov_village_group_members ADD COLUMN sort INT NOT NULL DEFAULT 0'); } catch (Throwable $e2) {}
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

    /** Groups of a player: gid => [gid, name, color, collapsed, sort]. */
    public static function groups(int $uid): array
    {
        $out = [];
        $st = self::run('SELECT gid, name, color, collapsed, sort FROM ov_village_groups WHERE uid = ? ORDER BY sort, gid', [$uid]);
        if ($st) {
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $out[(int)$r['gid']] = ['gid' => (int)$r['gid'], 'name' => (string)$r['name'], 'sort' => (int)$r['sort'],
                    'color' => in_array($r['color'], self::COLORS, true) ? $r['color'] : 'grey', 'collapsed' => (bool)$r['collapsed']];
            }
        }
        return $out;
    }

    /** wref => [gid, sort] of the villages with a stored place. */
    private static function places(int $uid): array
    {
        $out = [];
        $st = self::run('SELECT wref, gid, sort FROM ov_village_group_members WHERE uid = ?', [$uid]);
        if ($st) {
            foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) $out[(int)$r['wref']] = [(int)$r['gid'], (int)$r['sort']];
        }
        return $out;
    }

    /**
     * The list as the player arranged it: [['type' => 'group', 'group' => [...], 'villages' => [wref, …]] |
     * ['type' => 'village', 'wref' => wref], …]. $villages = the player's wrefs in the game's order; villages without a
     * stored place (new ones) come last.
     */
    public static function structure(int $uid, array $villages): array
    {
        $groups = self::groups($uid);
        $places = self::places($uid);
        $top = [];
        foreach ($groups as $g) $top[] = [$g['sort'], 0, 'group', $g['gid']];
        $inGroup = [];
        foreach (array_values($villages) as $i => $wref) {
            $wref = (int)$wref;
            [$gid, $sort] = $places[$wref] ?? [0, 1000000 + $i];
            if ($gid && isset($groups[$gid])) $inGroup[$gid][] = [$sort, $i, $wref];
            else $top[] = [$gid ? 1000000 + $i : $sort, 1, 'village', $wref];
        }
        usort($top, function ($a, $b) { return [$a[0], $a[1], $a[3]] <=> [$b[0], $b[1], $b[3]]; });
        $out = [];
        foreach ($top as $t) {
            if ($t[2] === 'group') {
                $members = $inGroup[$t[3]] ?? [];
                usort($members, function ($a, $b) { return [$a[0], $a[1]] <=> [$b[0], $b[1]]; });
                $out[] = ['type' => 'group', 'group' => $groups[$t[3]], 'villages' => array_column($members, 2)];
            } else {
                $out[] = ['type' => 'village', 'wref' => $t[3]];
            }
        }
        return $out;
    }

    /**
     * Stores an arrangement sent by the page: [['g' => gid, 'v' => [wref, …]] | ['v' => wref], …]. Unknown groups and
     * foreign villages are skipped; groups and villages missing from it keep their relative order at the end.
     */
    public static function saveOrder(int $uid, array $items, array $ownVillages): void
    {
        $own = array_flip(array_map('intval', $ownVillages));
        $groups = self::groups($uid);
        $sort = 0;
        $seenV = [];
        $seenG = [];
        $rows = [];      // wref => [gid, sort]
        $gsort = [];     // gid => sort
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            if (isset($item['g'])) {
                $gid = (int)$item['g'];
                if (!isset($groups[$gid]) || isset($seenG[$gid])) continue;
                $seenG[$gid] = true;
                $gsort[$gid] = ++$sort;
                $i = 0;
                foreach ((array)($item['v'] ?? []) as $wref) {
                    $wref = (int)$wref;
                    if (!isset($own[$wref]) || isset($seenV[$wref])) continue;
                    $seenV[$wref] = true;
                    $rows[$wref] = [$gid, ++$i];
                }
            } elseif (isset($item['v']) && !is_array($item['v'])) {
                $wref = (int)$item['v'];
                if (!isset($own[$wref]) || isset($seenV[$wref])) continue;
                $seenV[$wref] = true;
                $rows[$wref] = [0, ++$sort];
            }
        }
        foreach (self::structure($uid, $ownVillages) as $entry) {
            if ($entry['type'] === 'group' && !isset($seenG[$entry['group']['gid']])) {
                $gsort[$entry['group']['gid']] = ++$sort;
                foreach ($entry['villages'] as $w) if (!isset($seenV[$w])) { $seenV[$w] = true; $rows[$w] = [$entry['group']['gid'], ++$sort]; }
            } elseif ($entry['type'] === 'village' && !isset($seenV[$entry['wref']])) {
                $seenV[$entry['wref']] = true;
                $rows[$entry['wref']] = [0, ++$sort];
            }
        }
        $db = self::db();
        if (!$db || !self::ensureTables()) return;
        try {
            $db->beginTransaction();
            $st = $db->prepare('UPDATE ov_village_groups SET sort = ? WHERE uid = ? AND gid = ?');
            foreach ($gsort as $gid => $s) $st->execute([$s, $uid, $gid]);
            $db->prepare('DELETE FROM ov_village_group_members WHERE uid = ?')->execute([$uid]);
            $st = $db->prepare('INSERT INTO ov_village_group_members (uid, wref, gid, sort) VALUES (?, ?, ?, ?)');
            foreach ($rows as $wref => [$gid, $s]) $st->execute([$uid, $wref, $gid, $s]);
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('village groups: ' . $e->getMessage());
        }
    }

    /** Creates a group at the end of the list; returns its id (null when the player has MAX groups already). */
    public static function add(int $uid, string $name, array $ownVillages): ?int
    {
        $groups = self::groups($uid);
        if (count($groups) >= self::MAX) return null;
        // fix the current arrangement first, so the new group really is the last entry
        $items = [];
        foreach (self::structure($uid, $ownVillages) as $entry) {
            $items[] = $entry['type'] === 'group' ? ['g' => $entry['group']['gid'], 'v' => $entry['villages']] : ['v' => $entry['wref']];
        }
        self::saveOrder($uid, $items, $ownVillages);
        $gid = $groups ? max(array_keys($groups)) + 1 : 1;
        $name = self::cleanName($name);
        if ($name === '') $name = defined('VG_NEW_GROUP') ? VG_NEW_GROUP : 'New group';
        $ok = self::run('INSERT INTO ov_village_groups (uid, gid, name, color, sort, collapsed) VALUES (?, ?, ?, ?, ?, 0)',
            [$uid, $gid, $name, 'grey', 100000 + $gid]);
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

    /** Deletes a group; its villages stay in the list at the place of the group. */
    public static function delete(int $uid, int $gid, array $ownVillages): void
    {
        $items = [];
        foreach (self::structure($uid, $ownVillages) as $entry) {
            if ($entry['type'] === 'group' && $entry['group']['gid'] === $gid) {
                foreach ($entry['villages'] as $w) $items[] = ['v' => $w];
            } elseif ($entry['type'] === 'group') {
                $items[] = ['g' => $entry['group']['gid'], 'v' => $entry['villages']];
            } else {
                $items[] = ['v' => $entry['wref']];
            }
        }
        self::run('DELETE FROM ov_village_groups WHERE uid = ? AND gid = ?', [$uid, $gid]);
        self::saveOrder($uid, $items, $ownVillages);
    }

    private static function cleanName(string $name): string
    {
        $name = trim(preg_replace('/\s+/u', ' ', strip_tags($name)));
        return function_exists('mb_substr') ? mb_substr($name, 0, 40) : substr($name, 0, 40);
    }

    /* ---- quick links of the village list (up to 4, see LegendsQuickLinks) ---- */

    public static function pref(int $uid, string $name): ?string
    {
        $st = self::run('SELECT value FROM ov_user_prefs WHERE uid = ? AND name = ?', [$uid, $name]);
        $v = $st ? $st->fetchColumn() : false;
        return $v === false ? null : (string)$v;
    }

    public static function setPref(int $uid, string $name, string $value): void
    {
        self::run('DELETE FROM ov_user_prefs WHERE uid = ? AND name = ?', [$uid, $name]);
        self::run('INSERT INTO ov_user_prefs (uid, name, value) VALUES (?, ?, ?)', [$uid, $name, $value]);
    }

    /** Keys of the four slots ('' = empty slot). */
    public static function quickLinks(int $uid): array
    {
        $v = self::pref($uid, 'quickLinks');
        $keys = $v === null ? LegendsQuickLinks::DEFAULT_SLOTS : (json_decode($v, true) ?: []);
        $out = [];
        for ($i = 0; $i < 4; $i++) {
            $k = (string)($keys[$i] ?? '');
            $out[] = isset(LegendsQuickLinks::LINKS[$k]) ? $k : '';
        }
        return $out;
    }

    public static function setQuickLinks(int $uid, array $keys): void
    {
        $out = [];
        for ($i = 0; $i < 4; $i++) {
            $k = (string)($keys[$i] ?? '');
            $out[] = isset(LegendsQuickLinks::LINKS[$k]) && !in_array($k, $out, true) ? $k : '';
        }
        self::setPref($uid, 'quickLinks', json_encode($out));
    }

    /** ajax.php?cmd=villageGroups: action add|rename|color|collapse|delete|order|quickLinks. */
    public static function ajax(int $uid, array $ownVillages, array $post): array
    {
        $gid = (int)($post['gid'] ?? 0);
        switch ($post['action'] ?? '') {
            case 'add':        $gid = (int)self::add($uid, (string)($post['name'] ?? ''), $ownVillages); break;
            case 'rename':     self::rename($uid, $gid, (string)($post['name'] ?? '')); break;
            case 'color':      self::color($uid, $gid, (string)($post['color'] ?? '')); break;
            case 'collapse':   self::collapse($uid, $gid, !empty($post['collapsed'])); break;
            case 'delete':     self::delete($uid, $gid, $ownVillages); break;
            case 'order':      self::saveOrder($uid, (array)json_decode((string)($post['items'] ?? '[]'), true), $ownVillages); break;
            case 'quickLinks': self::setQuickLinks($uid, (array)json_decode((string)($post['links'] ?? '[]'), true)); break;
        }
        return ['gid' => $gid, 'groups' => array_values(self::groups($uid)), 'quickLinks' => self::quickLinks($uid)];
    }
}
