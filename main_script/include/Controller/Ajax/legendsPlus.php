<?php

namespace Controller\Ajax;

use Core\Config;
use Core\Session;
use Game\GoldHelper;
use function getCustom;

/**
 * Travian PLUS dialog of the Travian Legends 655 look (js/Game/Legends/plus.js).
 * POST action=info: gold, price, duration, whether PLUS is active; action=activate: books (or extends) PLUS like the
 * classic dialog does (ajax.php?cmd=premiumFeature, featureKey plus → GoldHelper::buyPlus()).
 */
class legendsPlus extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid()) {
            return $this->error('Not available.');
        }
        $session = Session::getInstance();
        $config = Config::getInstance();
        $cost = (int)$config->gold->plusGold;
        $seconds = (int)$config->gold->plusAccountDurationSeconds;
        $days = $seconds >= 86400;
        $data = [
            'gold' => getCustom('serverIsFreeGold') ? T('Global', 'Unlimited') : (int)$session->getAvailableGold(),
            'cost' => $cost,
            'active' => $session->hasPlus(),
            'unit' => $days ? 'days' : 'hours',
            'duration' => round($seconds / ($days ? 86400 : 3600), 1),
            'canSpend' => $session->checkSitterPermission(Session::SITTER_CAN_BUY_OR_SPEND_GOLD),
        ];
        if (($_POST['action'] ?? '') === 'activate') {
            if (!$data['canSpend']) {
                return $this->error(T('inGame', 'sitterNoPermForGold'), 403);
            }
            $till = $session->plusTill();
            if ($session->getAvailableGold() >= $cost) {
                (new GoldHelper())->buyPlus();
            }
            $data['activated'] = $session->plusTill() > $till;
            if ($data['activated']) {
                $data['active'] = true;
            } else {
                $data['error'] = 'notEnoughGold';
            }
        }
        $this->response['data'] = $data;
        return true;
    }
}
