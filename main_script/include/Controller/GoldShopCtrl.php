<?php

namespace Controller;

use Core\Config;
use Core\Database\DB;
use Core\Database\GlobalDB;
use Core\Session;
use Game\GoldHelper;
use Model\AutoExtendModel;
use resources\View\GameView;
use resources\View\PHPBatchView;

/**
 * Gold & Plus page of the Travian Legends style (graphic pack 29c89d54), opened from the "T" coin.
 *
 * When the world has an active payment configuration with gold products, the packages come from there and
 * "Continue" opens the game's own payment wizard. In an offline world (no payment configured – the normal
 * case for the local / iOS runtime) the packages are credited directly as gift gold, so gold features can
 * be tried out; nothing is charged and no payment data is asked for.
 */
class GoldShopCtrl extends GameCtrl
{
    private const OFFLINE_PACKAGES = [44, 137, 336, 816, 2178, 4500];

    public function __construct()
    {
        parent::__construct();
        if (!is_t46_layout()) {
            $this->redirect('dorf1.php');
            return;
        }
        $session = Session::getInstance();
        $tab = ($_GET['t'] ?? '') === 'pros' ? 'pros' : 'gold';
        $products = $this->onlineProducts();
        $offline = $products === [];

        if ($offline && isset($_GET['package'], $_GET['c']) && $_GET['c'] === $session->getChecker()) {
            $session->changeChecker();
            $index = (int)$_GET['package'];
            if (isset(self::OFFLINE_PACKAGES[$index]) && $session->checkSitterPermission(Session::SITTER_CAN_BUY_OR_SPEND_GOLD)) {
                DB::getInstance()->run('UPDATE users SET gift_gold = gift_gold + ? WHERE id = ?',
                    [self::OFFLINE_PACKAGES[$index], $session->getPlayerId()]);
            }
            $this->redirect('gold.php?credited=' . $index);
            return;
        }

        $packages = [];
        if ($offline) {
            foreach (self::OFFLINE_PACKAGES as $i => $gold) {
                $packages[] = ['index' => $i, 'gold' => $gold, 'price' => '', 'bestSeller' => $i === 3, 'bestValue' => $i === 5];
            }
        } else {
            foreach ($products as $i => $p) {
                $packages[] = ['index' => $i, 'gold' => (int)$p['goldProductGold'],
                    'price' => rtrim(rtrim(number_format((float)$p['goldProductPrice'], 2, ',', ' '), '0'), ',') . ' ' . $p['goldProductMoneyUnit'],
                    'bestSeller' => (bool)$p['isBestSeller'], 'bestValue' => (bool)$p['isBestValue']];
            }
        }

        $helper = new GoldHelper();
        $config = Config::getInstance();
        $features = [];
        $advantages = [];
        if ($tab === 'pros' && is_hero_v2()) {
            // Travian Legends "Advantages" cards: gold club and Plus on top, the four production bonuses below.
            $autoExtend = new AutoExtendModel();
            $uid = $session->getPlayerId();
            $advantages['goldclub'] = ['key' => 'goldclub', 'gold' => (int)$config->gold->goldClubGold,
                'active' => $session->hasGoldClub()];
            $advantages['plus'] = ['key' => 'plus', 'gold' => (int)$config->gold->plusGold, 'active' => $session->hasPlus(),
                'till' => (int)$session->plusTill(), 'autoExtend' => $autoExtend->hasAutoExtend($uid, 1),
                'duration' => (int)$config->gold->plusAccountDurationSeconds];
            $advantages['boosts'] = [];
            foreach ([1 => 'Wood', 2 => 'Clay', 3 => 'Iron', 4 => 'Crop'] as $r => $name) {
                $advantages['boosts'][] = ['key' => 'productionboost' . $name, 'resource' => $r, 'name' => $name,
                    'gold' => (int)$config->gold->productionBoostGold, 'active' => $session->hasProductionBoost($r),
                    'till' => (int)$session->productionBoostTill($r), 'autoExtend' => $autoExtend->hasAutoExtend($uid, $r + 1),
                    'duration' => (int)$config->gold->productionBoostDurationSeconds];
            }
        } elseif ($tab === 'pros') {
            $features[] = ['class' => 'plus', 'title' => T('PaymentWizard', 'Plus'), 'button' => $helper->getPlusButton(FALSE, TRUE),
                'gold' => $config->gold->plusGold, 'active' => $session->hasPlus()];
            foreach ([1 => 'Wood', 2 => 'Clay', 3 => 'Iron', 4 => 'Crop'] as $r => $name) {
                $features[] = ['class' => 'productionBoost' . $name, 'title' => '+25% ' . T('Dorf1', 'production.resources.' . $r),
                    'button' => $helper->getProductionBoostButton($r, FALSE, TRUE), 'gold' => $config->gold->productionBoostGold,
                    'active' => $session->hasProductionBoost($r)];
            }
            $features[] = ['class' => 'goldclub', 'title' => T('PaymentWizard', 'GoldClub'), 'button' => $helper->getGoldClubButton(),
                'gold' => $config->gold->goldClubGold, 'active' => $session->hasGoldClub()];
        }

        $this->view = new GameView();
        $this->view->vars['titleInHeader'] = T('Tasks', 'goldShopTitle', 'Gold & Plus');
        $this->view->vars['bodyCssClass'] = 'perspectiveResources';
        $this->view->vars['contentCssClass'] = 'goldShopT46';
        $this->view->vars['answerId'] = 0; // Legends shop header: gold balance and close button only.
        $this->view->vars['content'] = PHPBatchView::render('goldShop/main', [
            'tab' => $tab,
            'offline' => $offline,
            'packages' => $packages,
            'features' => $features,
            'advantages' => $advantages,
            'canSpendGold' => $session->checkSitterPermission(Session::SITTER_CAN_BUY_OR_SPEND_GOLD),
            'gold' => $session->getAvailableGold(),
            'credited' => isset($_GET['credited']) ? (self::OFFLINE_PACKAGES[(int)$_GET['credited']] ?? null) : null,
            'checker' => $session->getChecker(),
        ]);
    }

    /** Gold products of an active payment configuration (first location), or [] for an offline world. */
    private function onlineProducts(): array
    {
        try {
            $db = GlobalDB::getInstance();
            if (!$db->fetchScalar('SELECT active FROM paymentConfig')) {
                return [];
            }
            $location = (int)$db->fetchScalar('SELECT id FROM locations ORDER BY id LIMIT 1');
            $result = $db->query("SELECT * FROM goldProducts WHERE goldProductLocation=$location AND isActive>0 ORDER BY goldProductGold");
            $rows = [];
            while ($result && ($row = $result->fetch_assoc())) {
                $rows[] = $row;
            }
            return $rows;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
