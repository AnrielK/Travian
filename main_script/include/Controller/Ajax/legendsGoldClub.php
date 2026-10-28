<?php

namespace Controller\Ajax;

use Core\Config;
use Core\Database\DB;
use Core\Session;
use Game\GoldHelper;
use Model\DailyQuestModel;
use function getCustom;

/**
 * Gold Club ("Bonifácův klub") dialog of the Travian Legends 655 look (js/Game/Legends/goldClub.js).
 * POST action=info: gold, price, whether the club is active; action=activate: books it for the round like the
 * classic dialog does (ajax.php?cmd=premiumFeature, featureKey goldclub).
 */
class legendsGoldClub extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid()) {
            return $this->error('Not available.');
        }
        $session = Session::getInstance();
        $cost = (int)Config::getInstance()->gold->goldClubGold;
        $data = [
            'gold' => getCustom('serverIsFreeGold') ? T('Global', 'Unlimited') : (int)$session->getAvailableGold(),
            'cost' => $cost,
            'active' => $session->hasGoldClub(),
            'canSpend' => $session->checkSitterPermission(Session::SITTER_CAN_BUY_OR_SPEND_GOLD),
        ];
        if (($_POST['action'] ?? '') === 'activate' && !$data['active']) {
            if (!$data['canSpend']) {
                return $this->error(T('inGame', 'sitterNoPermForGold'), 403);
            }
            if (GoldHelper::decreaseGold($session->getPlayerId(), $cost)) {
                DB::getInstance()->query('UPDATE users SET goldclub=1 WHERE id=' . (int)$session->getPlayerId());
                (new DailyQuestModel())->setQuestAsCompleted($session->getPlayerId(), 5);
                $data['activated'] = true;
                $data['active'] = true;
            } else {
                $data['error'] = 'notEnoughGold';
            }
        }
        $this->response['data'] = $data;
        return true;
    }
}
