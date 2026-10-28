<?php

namespace resources\View;

use Core\Database\DB;

/**
 * Village groups of the Travian Legends 655 village list ("Skupiny vesnic (1/20)"), port of the travian-T4.6 branch.
 *
 * The list is one ordered sequence of top-level entries – groups (with their villages) and villages without a group –
 * which the player rearranges by dragging (js/Game/Legends/villageGroups.js, ajax.php?cmd=villageGroups). A new group
 * goes to the end of the list. Up to MAX groups, each with a name, a highlight colour and a collapsed flag.
 * Tables are created on first use:
 *   ov_village_groups        uid, gid, name, color, sort, collapsed
 *   ov_village_group_members uid, kid, gid (0 = top level), sort
 */
class LegendsVillageGroups
{
    public const MAX = 20;
    public const COLORS = ['grey', 'yellow', 'green', 'turquoise', 'blue', 'violet', 'magenta', 'red', 'orange'];

    private static bool $ready = false;

    public static function ensureTables(): void
    {
        if (self::$ready) {
            return;
        }
        $db = DB::getInstance();
        $db->query('CREATE TABLE IF NOT EXISTS `ov_village_groups` (
            `uid` INT UNSIGNED NOT NULL,
            `gid` INT UNSIGNED NOT NULL,
            `name` VARCHAR(40) NOT NULL DEFAULT \'\',
            `color` VARCHAR(12) NOT NULL DEFAULT \'grey\',
            `sort` INT NOT NULL DEFAULT 0,
            `collapsed` TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`uid`, `gid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $db->query('CREATE TABLE IF NOT EXISTS `ov_village_group_members` (
            `uid` INT UNSIGNED NOT NULL,
            `kid` INT UNSIGNED NOT NULL,
            `gid` INT UNSIGNED NOT NULL,
            `sort` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`uid`, `kid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        self::$ready = true;
    }

    private static function rows(string $sql, array $args = []): array
    {
        self::ensureTables();
        $st = DB::getInstance()->run($sql, $args);
        if (!$st) {
            return [];
        }
        $res = $st->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    private static function exec(string $sql, array $args = []): bool
    {
        self::ensureTables();
        return DB::getInstance()->run($sql, $args) !== false;
    }

    /** The player's villages (kid) in the order of the game's village list. */
    public static function ownVillages(int $uid): array
    {
        $res = DB::getInstance()->query('SELECT kid FROM vdata WHERE owner = ' . $uid . ' ORDER BY name');
        $out = [];
        while ($res && ($row = $res->fetch_row())) {
            $out[] = (int)$row[0];
        }
        return $out;
    }

    /** Groups of a player: gid => [gid, name, color, collapsed, sort]. */
    public static function groups(int $uid): array
    {
        $out = [];
        foreach (self::rows('SELECT gid, name, color, collapsed, sort FROM ov_village_groups WHERE uid = ? ORDER BY sort, gid', [$uid]) as $r) {
            $out[(int)$r['gid']] = ['gid' => (int)$r['gid'], 'name' => (string)$r['name'], 'sort' => (int)$r['sort'],
                'color' => in_array($r['color'], self::COLORS, true) ? $r['color'] : 'grey', 'collapsed' => (bool)$r['collapsed']];
        }
        return $out;
    }

    /** kid => [gid, sort] of the villages with a stored place. */
    private static function places(int $uid): array
    {
        $out = [];
        foreach (self::rows('SELECT kid, gid, sort FROM ov_village_group_members WHERE uid = ?', [$uid]) as $r) {
            $out[(int)$r['kid']] = [(int)$r['gid'], (int)$r['sort']];
        }
        return $out;
    }

    /**
     * The list as the player arranged it: [['type' => 'group', 'group' => [...], 'villages' => [kid, …]] |
     * ['type' => 'village', 'kid' => kid], …]. $villages = the player's kids in the game's order; villages without a
     * stored place (new ones) come last.
     */
    public static function structure(int $uid, array $villages): array
    {
        $groups = self::groups($uid);
        $places = self::places($uid);
        $top = [];
        foreach ($groups as $g) {
            $top[] = [$g['sort'], 0, 'group', $g['gid']];
        }
        $inGroup = [];
        foreach (array_values($villages) as $i => $kid) {
            $kid = (int)$kid;
            [$gid, $sort] = $places[$kid] ?? [0, 1000000 + $i];
            if ($gid && isset($groups[$gid])) {
                $inGroup[$gid][] = [$sort, $i, $kid];
            } else {
                $top[] = [$gid ? 1000000 + $i : $sort, 1, 'village', $kid];
            }
        }
        usort($top, static fn($a, $b) => [$a[0], $a[1], $a[3]] <=> [$b[0], $b[1], $b[3]]);
        $out = [];
        foreach ($top as $t) {
            if ($t[2] === 'group') {
                $members = $inGroup[$t[3]] ?? [];
                usort($members, static fn($a, $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);
                $out[] = ['type' => 'group', 'group' => $groups[$t[3]], 'villages' => array_column($members, 2)];
            } else {
                $out[] = ['type' => 'village', 'kid' => $t[3]];
            }
        }
        return $out;
    }

    /**
     * Stores an arrangement sent by the page: [['g' => gid, 'v' => [kid, …]] | ['v' => kid], …]. Unknown groups and
     * foreign villages are skipped; groups and villages missing from it keep their relative order at the end.
     */
    public static function saveOrder(int $uid, array $items, array $ownVillages): void
    {
        $own = array_flip(array_map('intval', $ownVillages));
        $groups = self::groups($uid);
        $sort = 0;
        $seenV = $seenG = $rows = $gsort = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (isset($item['g'])) {
                $gid = (int)$item['g'];
                if (!isset($groups[$gid]) || isset($seenG[$gid])) {
                    continue;
                }
                $seenG[$gid] = true;
                $gsort[$gid] = ++$sort;
                $i = 0;
                foreach ((array)($item['v'] ?? []) as $kid) {
                    $kid = (int)$kid;
                    if (!isset($own[$kid]) || isset($seenV[$kid])) {
                        continue;
                    }
                    $seenV[$kid] = true;
                    $rows[$kid] = [$gid, ++$i];
                }
            } elseif (isset($item['v']) && !is_array($item['v'])) {
                $kid = (int)$item['v'];
                if (!isset($own[$kid]) || isset($seenV[$kid])) {
                    continue;
                }
                $seenV[$kid] = true;
                $rows[$kid] = [0, ++$sort];
            }
        }
        foreach (self::structure($uid, $ownVillages) as $entry) {
            if ($entry['type'] === 'group' && !isset($seenG[$entry['group']['gid']])) {
                $gsort[$entry['group']['gid']] = ++$sort;
                foreach ($entry['villages'] as $k) {
                    if (!isset($seenV[$k])) {
                        $seenV[$k] = true;
                        $rows[$k] = [$entry['group']['gid'], ++$sort];
                    }
                }
            } elseif ($entry['type'] === 'village' && !isset($seenV[$entry['kid']])) {
                $seenV[$entry['kid']] = true;
                $rows[$entry['kid']] = [0, ++$sort];
            }
        }
        $db = DB::getInstance();
        $db->begin_transaction();
        try {
            foreach ($gsort as $gid => $s) {
                self::exec('UPDATE ov_village_groups SET sort = ? WHERE uid = ? AND gid = ?', [$s, $uid, $gid]);
            }
            self::exec('DELETE FROM ov_village_group_members WHERE uid = ?', [$uid]);
            foreach ($rows as $kid => [$gid, $s]) {
                self::exec('INSERT INTO ov_village_group_members (uid, kid, gid, sort) VALUES (?, ?, ?, ?)', [$uid, $kid, $gid, $s]);
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }

    private static function items(int $uid, array $ownVillages, ?int $without = null): array
    {
        $items = [];
        foreach (self::structure($uid, $ownVillages) as $entry) {
            if ($entry['type'] === 'group' && $entry['group']['gid'] === $without) {
                foreach ($entry['villages'] as $k) {
                    $items[] = ['v' => $k];   // the villages of a deleted group stay at its place
                }
            } elseif ($entry['type'] === 'group') {
                $items[] = ['g' => $entry['group']['gid'], 'v' => $entry['villages']];
            } else {
                $items[] = ['v' => $entry['kid']];
            }
        }
        return $items;
    }

    /** Creates a group at the end of the list; returns its id (null when the player has MAX groups already). */
    public static function add(int $uid, string $name, array $ownVillages): ?int
    {
        $groups = self::groups($uid);
        if (count($groups) >= self::MAX) {
            return null;
        }
        self::saveOrder($uid, self::items($uid, $ownVillages), $ownVillages);   // fix the order: the new group is last
        $gid = $groups ? max(array_keys($groups)) + 1 : 1;
        $name = self::cleanName($name);
        if ($name === '') {
            $name = T('VillageGroups', 'newGroup', 'New group');
        }
        $ok = self::exec('INSERT INTO ov_village_groups (uid, gid, name, color, sort, collapsed) VALUES (?, ?, ?, ?, ?, 0)',
            [$uid, $gid, $name, 'grey', 100000 + $gid]);
        return $ok ? $gid : null;
    }

    public static function rename(int $uid, int $gid, string $name): void
    {
        $name = self::cleanName($name);
        if ($name !== '') {
            self::exec('UPDATE ov_village_groups SET name = ? WHERE uid = ? AND gid = ?', [$name, $uid, $gid]);
        }
    }

    public static function color(int $uid, int $gid, string $color): void
    {
        if (in_array($color, self::COLORS, true)) {
            self::exec('UPDATE ov_village_groups SET color = ? WHERE uid = ? AND gid = ?', [$color, $uid, $gid]);
        }
    }

    public static function collapse(int $uid, int $gid, bool $collapsed): void
    {
        self::exec('UPDATE ov_village_groups SET collapsed = ? WHERE uid = ? AND gid = ?', [$collapsed ? 1 : 0, $uid, $gid]);
    }

    /** Deletes a group; its villages stay in the list at the place of the group. */
    public static function delete(int $uid, int $gid, array $ownVillages): void
    {
        $items = self::items($uid, $ownVillages, $gid);
        self::exec('DELETE FROM ov_village_groups WHERE uid = ? AND gid = ?', [$uid, $gid]);
        self::saveOrder($uid, $items, $ownVillages);
    }

    private static function cleanName(string $name): string
    {
        $name = trim((string)preg_replace('/\s+/u', ' ', strip_tags($name)));
        return mb_substr($name, 0, 40);
    }

    /** SVG of the six-dot drag handle. */
    public static function handle(): string
    {
        return '<svg class="handle" viewBox="0 0 8 13"><circle cx="1.5" cy="1.5" r="1.5"/><circle cx="6.5" cy="1.5" r="1.5"/>'
            . '<circle cx="1.5" cy="6.5" r="1.5"/><circle cx="6.5" cy="6.5" r="1.5"/><circle cx="1.5" cy="11.5" r="1.5"/>'
            . '<circle cx="6.5" cy="11.5" r="1.5"/></svg>';
    }
}
