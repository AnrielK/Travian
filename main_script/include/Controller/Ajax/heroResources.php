<?php

namespace Controller\Ajax;

use Core\Database\DB;
use Core\Village;
use Game\Hero\HeroItems;

/**
 * Legends hero inventory: moves resources from the hero's resource items (lumber/clay/iron/crop carts, btype 16)
 * into the warehouse and granary of the active village, at most up to their free capacity.
 * POST: r1..r4 amounts (or max=1 to fill up), c = checker.
 */
class heroResources extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid() || !HeroItems::resourceItemsEnabled()) {
            return $this->error('Not available.');
        }
        if (($_POST['c'] ?? '') !== $this->session->getChecker()) {
            return $this->error('Invalid request.', 403);
        }
        if ($this->session->isSitter() && !$this->session->checkSitterPermission(\Core\Session::SITTER_CAN_BUY_OR_SPEND_GOLD)) {
            return $this->error('Sitters cannot use hero items.', 403);
        }
        $uid = (int)$this->session->getPlayerId();
        $db = DB::getInstance();
        $village = Village::getInstance();
        $owned = self::ownedResources($uid);
        $current = [1 => $village->getCurrentResources(0), 2 => $village->getCurrentResources(1),
            3 => $village->getCurrentResources(2), 4 => $village->getCurrentResources(3)];
        $capacity = [1 => $village->get('maxstore'), 2 => $village->get('maxstore'), 3 => $village->get('maxstore'), 4 => $village->get('maxcrop')];
        $moved = [0, 0, 0, 0];
        foreach (HeroItems::RESOURCE_TYPES as $r => $type) {
            $free = max(0, (int)floor($capacity[$r] - $current[$r]));
            $wanted = !empty($_POST['max']) ? $owned[$r] : max(0, (int)($_POST['r' . $r] ?? 0));
            $amount = min($wanted, $owned[$r], $free);
            if ($amount <= 0) {
                continue;
            }
            // Take the amount from the stack (only when it is still there), then delete emptied stacks.
            $stmt = $db->run('UPDATE items SET num = num - ? WHERE uid = ? AND btype = ? AND type = ? AND proc = 0 AND num >= ? LIMIT 1',
                [$amount, $uid, HeroItems::RESOURCE_BTYPE, $type, $amount]);
            if (!$stmt || $stmt->affected_rows !== 1) {
                continue;
            }
            $moved[$r - 1] = $amount;
        }
        $db->run('DELETE FROM items WHERE uid = ? AND btype = ? AND num = 0', [$uid, HeroItems::RESOURCE_BTYPE]);
        if (array_sum($moved) > 0) {
            $village->modifyResources($moved, 1);
        }
        $this->response['data']['moved'] = $moved;
        $this->response['data']['owned'] = self::ownedResources($uid);
        return true;
    }

    /** Resource items in the hero's bag: [1 => lumber, ..., 4 => crop]. */
    public static function ownedResources(int $uid): array
    {
        $owned = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        $stmt = DB::getInstance()->run('SELECT type, SUM(num) AS n FROM items WHERE uid = ? AND btype = ? AND proc = 0 GROUP BY type',
            [$uid, HeroItems::RESOURCE_BTYPE]);
        $rows = $stmt ? $stmt->get_result() : false;
        while ($rows && ($row = $rows->fetch_assoc())) {
            $r = array_search((int)$row['type'], HeroItems::RESOURCE_TYPES, true);
            if ($r !== false) {
                $owned[$r] = (int)$row['n'];
            }
        }
        return $owned;
    }
}
