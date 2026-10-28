<?php

namespace Controller\Ajax;

use Core\Database\DB;
use resources\View\LegendsQuickLinks;

/**
 * Travian Legends 655 quick links (resources/View/LegendsQuickLinks.php): stores one set of slots.
 * POST: links = JSON list of link keys ('' = empty slot), kid = 0 for the village list, else a village of the player.
 */
class quickLinks extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid() || !is_hero_v2()) {
            return $this->error('Not available.');
        }
        $uid = (int)$this->session->getPlayerId();
        $kid = (int)($_POST['kid'] ?? 0);
        if ($kid > 0 && !DB::getInstance()->fetchScalar('SELECT 1 FROM vdata WHERE kid = ' . $kid . ' AND owner = ' . $uid)) {
            return $this->error('Invalid village.', 403);
        }
        $keys = json_decode((string)($_POST['links'] ?? '[]'), true);
        if (!is_array($keys)) {
            return $this->error('Invalid links.');
        }
        $this->response['data']['links'] = LegendsQuickLinks::save($uid, $kid, array_values($keys));
        return true;
    }
}
