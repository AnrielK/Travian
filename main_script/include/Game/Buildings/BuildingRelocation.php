<?php

namespace Game\Buildings;

use Core\Database\DB;

/**
 * Relocating buildings inside the village centre (Main Building → "Relocate buildings").
 *
 * The player rearranges the building slots on the village map and submits the result as a placement
 * "target slot => source slot". The buildings (type + level) move with their queued work: construction
 * and demolition tasks follow the building to its new slot, so nothing in progress is lost.
 *
 * Only the free building slots 19–38 take part. The rally point (39) and the wall (40) have fixed places,
 * and a Wonder of the World village keeps its layout (the wonder spans several slots).
 */
final class BuildingRelocation
{
    public const FIRST_SLOT = 19;
    public const LAST_SLOT = 38;

    public static function isMovableSlot(int $slot): bool
    {
        return $slot >= self::FIRST_SLOT && $slot <= self::LAST_SLOT;
    }

    /**
     * Validates a submitted placement and drops the slots that stay where they are.
     *
     * @param mixed $raw target slot => source slot
     * @return array<int, int>|null the moved slots (target => source), null when the placement is invalid
     */
    public static function normalizePlacement($raw): ?array
    {
        if (!is_array($raw) || count($raw) > self::LAST_SLOT - self::FIRST_SLOT + 1) {
            return null;
        }
        $placement = [];
        foreach ($raw as $target => $source) {
            if (!is_int($target) && !(is_string($target) && ctype_digit($target))) {
                return null;
            }
            if (!is_int($source) && !(is_string($source) && ctype_digit($source))) {
                return null;
            }
            $target = (int)$target;
            $source = (int)$source;
            if (!self::isMovableSlot($target) || !self::isMovableSlot($source)) {
                return null;
            }
            if ($target !== $source) {
                $placement[$target] = $source;
            }
        }
        // Every slot that gives its building away has to receive one (possibly an empty plot) in return,
        // i.e. the moved slots form a permutation; otherwise buildings would be duplicated or lost.
        $targets = array_keys($placement);
        $sources = array_values($placement);
        if (count(array_unique($sources)) !== count($sources)) {
            return null;
        }
        sort($targets);
        sort($sources);
        if ($targets !== $sources) {
            return null;
        }
        ksort($placement);

        return $placement;
    }

    /**
     * Moves the buildings of a village as described by a normalized placement.
     *
     * Locks in the same order as the automation worker (owner → task rows → fdata), so a building task that
     * completes at the same moment either runs before the move or sees the task already on its new slot.
     *
     * @param array<int, int> $placement target slot => source slot, as returned by normalizePlacement()
     */
    public static function apply(int $kid, array $placement): bool
    {
        $placement = self::normalizePlacement($placement);
        if ($placement === null || $kid <= 0) {
            return false;
        }
        if (!$placement) {
            return true;
        }
        $db = DB::getInstance();
        if (!$db->begin_transaction()) {
            return false;
        }
        try {
            $village = $db->query("SELECT owner, isWW FROM vdata WHERE kid=$kid");
            if (!$village || !$village->num_rows) {
                $db->rollback();
                return false;
            }
            $village = $village->fetch_assoc();
            if ((int)$village['isWW'] === 1) {
                $db->rollback();
                return false;
            }
            $owner = (int)$village['owner'];
            if (!$db->query("SELECT id FROM users WHERE id=$owner FOR UPDATE")) {
                throw new \RuntimeException('Unable to lock the village owner.');
            }
            if (!$db->query("SELECT id FROM building_upgrade WHERE kid=$kid FOR UPDATE")
                || !$db->query("SELECT id FROM demolition WHERE kid=$kid FOR UPDATE")) {
                throw new \RuntimeException('Unable to lock the village tasks.');
            }
            $columns = [];
            foreach ($placement as $source) {
                $columns[] = "f{$source}";
                $columns[] = "f{$source}t";
            }
            $fields = $db->query("SELECT " . implode(', ', $columns) . " FROM fdata WHERE kid=$kid FOR UPDATE");
            if (!$fields || !$fields->num_rows) {
                $db->rollback();
                return false;
            }
            $fields = $fields->fetch_assoc();

            $set = [];
            $case = [];
            foreach ($placement as $target => $source) {
                $set[] = "f{$target}=" . (int)$fields["f{$source}"];
                $set[] = "f{$target}t=" . (int)$fields["f{$source}t"];
                $case[] = "WHEN $source THEN $target";
            }
            $sourceList = implode(',', array_values($placement));
            $caseSql = 'CASE building_field ' . implode(' ', $case) . ' END';

            if (!$db->query("UPDATE fdata SET " . implode(', ', $set) . " WHERE kid=$kid")
                || !$db->query("UPDATE building_upgrade SET building_field=$caseSql WHERE kid=$kid AND building_field IN ($sourceList)")
                || !$db->query("UPDATE demolition SET building_field=$caseSql WHERE kid=$kid AND building_field IN ($sourceList)")) {
                throw new \RuntimeException('Unable to move the buildings.');
            }
            if (!$db->commit()) {
                throw new \RuntimeException('Unable to commit the building relocation.');
            }
        } catch (\Throwable $e) {
            $db->rollback();
            logError('Building relocation failed for village %s: %s', [$kid, $e->getMessage()]);
            return false;
        }

        return true;
    }
}
