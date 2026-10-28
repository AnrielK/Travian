<?php

namespace Controller\Ajax;

use resources\View\LegendsVillageGroups;

/**
 * Village groups of the Travian Legends 655 village list (resources/View/LegendsVillageGroups.php,
 * js/Game/Legends/villageGroups.js). POST action = add | rename | color | collapse | delete | order.
 */
class villageGroups extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid() || !is_hero_v2()) {
            return $this->error('Not available.');
        }
        if ($this->session->isSitter()) {
            return $this->error('Not available for sitters.', 403);
        }
        $uid = (int)$this->session->getPlayerId();
        $own = LegendsVillageGroups::ownVillages($uid);
        $gid = (int)($_POST['gid'] ?? 0);
        switch ($_POST['action'] ?? '') {
            case 'add':
                $gid = (int)LegendsVillageGroups::add($uid, (string)($_POST['name'] ?? ''), $own);
                if (!$gid) {
                    return $this->error(T('VillageGroups', 'max', 'You can have at most 20 village groups.'));
                }
                break;
            case 'rename':
                LegendsVillageGroups::rename($uid, $gid, (string)($_POST['name'] ?? ''));
                break;
            case 'color':
                LegendsVillageGroups::color($uid, $gid, (string)($_POST['color'] ?? ''));
                break;
            case 'collapse':
                LegendsVillageGroups::collapse($uid, $gid, !empty($_POST['collapsed']));
                break;
            case 'delete':
                LegendsVillageGroups::delete($uid, $gid, $own);
                break;
            case 'order':
                $items = json_decode((string)($_POST['items'] ?? '[]'), true);
                LegendsVillageGroups::saveOrder($uid, is_array($items) ? $items : [], $own);
                break;
            default:
                return $this->error('Unknown action.');
        }
        $this->response['data'] = ['gid' => $gid, 'groups' => array_values(LegendsVillageGroups::groups($uid))];
        return true;
    }
}
