<?php
/**
 * Hero bag of the Travian Legends look (application/views/Hero/legends/inventory.php), port of the bag handling of
 * the main branch (Controller/Ajax/heroInventory.php "change place", heroResources.php):
 *  - places: every bag item has a place 1..n; this branch's heroitems table has no place column, so the places are kept
 *    per player in ov_user_prefs ("heroBagPlaces", JSON id => place). New items take the first free place.
 *  - move(): puts an item on another place (swapping with the item there), like sorting in Legends.
 *  - moveResources(): resource items (task rewards, btype 16) into the warehouse / granary of a village.
 */
final class HeroBag
{
    const RESOURCE_BTYPE = 16;
    /** resource index 1..4 (lumber, clay, iron, crop) => item type (picture) */
    const RESOURCE_TYPES = [1 => 145, 2 => 146, 3 => 147, 4 => 148];
    const PREF = 'heroBagPlaces';

    /** Items in the bag (not equipped), as the inventory lists them. */
    public static function items(int $uid): array
    {
        global $database;
        $rows = $database->query("SELECT * FROM heroitems WHERE (proc = 0 or (btype > 6 and btype <10 and num>type)) AND uid = " . $uid);
        return is_array($rows) ? $rows : [];
    }

    private static function load(int $uid): array
    {
        $v = VillageGroups::pref($uid, self::PREF);
        $a = $v ? json_decode($v, true) : [];
        $out = [];
        foreach ((array)$a as $id => $place) $out[(int)$id] = (int)$place;
        return $out;
    }

    private static function save(int $uid, array $places): void
    {
        VillageGroups::setPref($uid, self::PREF, json_encode($places));
    }

    /** id => place for the given bag items (places of items that are gone are dropped, new items get free places). */
    public static function places(int $uid, array $items): array
    {
        $saved = self::load($uid);
        $out = [];
        $used = [];
        foreach ($items as $item) {
            $id = (int)$item['id'];
            if (isset($saved[$id]) && $saved[$id] > 0 && !isset($used[$saved[$id]])) {
                $out[$id] = $saved[$id];
                $used[$saved[$id]] = true;
            }
        }
        $next = 1;
        foreach ($items as $item) {
            $id = (int)$item['id'];
            if (isset($out[$id])) continue;
            while (isset($used[$next])) $next++;
            $out[$id] = $next;
            $used[$next] = true;
        }
        if ($out != $saved) self::save($uid, $out);
        return $out;
    }

    /** Puts bag item $id on $place (1..200); an item already there takes the old place. */
    public static function move(int $uid, int $id, int $place): bool
    {
        if ($place < 1 || $place > 200) return false;
        $items = self::items($uid);
        $places = self::places($uid, $items);
        if (!isset($places[$id])) return false;
        $from = $places[$id];
        foreach ($places as $other => $p) {
            if ($p === $place && $other !== $id) $places[$other] = $from;
        }
        $places[$id] = $place;
        self::save($uid, $places);
        return true;
    }

    /**
     * Moves resources from the hero's resource items into village $wid: $amounts = [1 => lumber, 2 => clay, 3 => iron,
     * 4 => crop], null = as much as fits. Never more than the items hold or the warehouse / granary takes.
     * Returns the moved amounts.
     */
    public static function moveResources(int $uid, int $wid, ?array $amounts): array
    {
        global $database;
        $v = $database->query('SELECT wood, clay, iron, crop, maxstore, maxcrop FROM vdata WHERE wref = ' . $wid . ' AND owner = ' . $uid);
        $moved = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        if (!is_array($v) || !isset($v[0])) return $moved;
        $v = $v[0];
        $stock = [1 => (float)$v['wood'], 2 => (float)$v['clay'], 3 => (float)$v['iron'], 4 => (float)$v['crop']];
        foreach (self::RESOURCE_TYPES as $r => $type) {
            $rows = $database->query('SELECT id, num FROM heroitems WHERE uid = ' . $uid . ' AND btype = ' . self::RESOURCE_BTYPE . ' AND type = ' . $type . ' AND proc = 0');
            if (!is_array($rows) || !$rows) continue;
            $have = array_sum(array_map(function ($row) { return (int)$row['num']; }, $rows));
            $cap = $r === 4 ? (float)$v['maxcrop'] : (float)$v['maxstore'];
            $free = max(0, (int)floor($cap - $stock[$r]));
            $want = $amounts === null ? $have : max(0, (int)($amounts[$r] ?? 0));
            $left = min($want, $have, $free);
            $moved[$r] = $left;
            foreach ($rows as $row) {
                if ($left <= 0) break;
                $take = min($left, (int)$row['num']);
                if ($take >= (int)$row['num']) $database->editProcItem((int)$row['id'], 1, $uid);
                else $database->editHeroNum((int)$row['id'], $take, 0);
                $left -= $take;
            }
        }
        if (array_sum($moved) > 0) $database->modifyResource($wid, $moved[1], $moved[2], $moved[3], $moved[4], 1);
        return $moved;
    }
}
