<?php

namespace Controller;

use Core\Config;
use Core\Database\DB;
use Core\Session;
use Model\DailyQuestModel;
use resources\View\GameView;
use resources\View\PHPBatchView;

/**
 * Daily quests page of the Travian Legends style (graphic pack 29c89d54), opened from the "Q" button of the
 * top navigation. Uses the game's daily quest data (DailyQuestModel); other packs keep the classic dialog.
 */
class DailyQuestsCtrl extends GameCtrl
{
    /** quest id => [points per step, steps] – same table as DailyQuestModel::$quest_data. */
    private const QUESTS = [
        1 => [5, 1], 2 => [3, 3], 3 => [3, 3], 4 => [5, 1], 5 => [2, 3], 6 => [4, 3],
        7 => [5, 3], 8 => [3, 3], 9 => [3, 3], 10 => [5, 3], 11 => [2, 3],
    ];

    public function __construct()
    {
        parent::__construct();
        if (!is_t46_layout()) {
            $this->redirect('dorf1.php');
            return;
        }
        $session = Session::getInstance();
        $uid = (int)$session->getPlayerId();
        $model = new DailyQuestModel();
        $quest = $model->getQuest($uid);
        if (!$quest) {
            $model->addDailyQuestRow($uid);
            $quest = $model->getQuest($uid);
        }

        if (isset($_GET['collect'], $_GET['c']) && $_GET['c'] === $session->getChecker()) {
            $session->changeChecker();
            $points = $model->calcTotalPoints($quest);
            $model->setReward($uid);
            foreach ([1, 2, 3, 4] as $n) {
                if ($points >= $n * 25 && !$quest['reward' . $n . 'Done']) {
                    $model->collectReward($uid, $session->getKid(), $n);
                }
            }
            $this->redirect('dailyquests.php');
            return;
        }

        $points = $model->calcTotalPoints($quest);
        $quests = [];
        foreach (self::QUESTS as $id => [$reward, $steps]) {
            $done = min($steps, (int)$quest['qst' . $id]);
            $quests[] = [
                'id' => $id,
                'name' => sprintf(T('DailyQuest', 'questData.' . $id . '.name'), DailyQuestModel::getAllianceContributionNeededForQuest()),
                'reward' => $reward,
                'steps' => $steps,
                'done' => $done,
                'points' => $done * $reward,
                'total' => $steps * $reward,
            ];
        }
        $milestones = [];
        foreach ([1, 2, 3, 4] as $n) {
            $milestones[$n] = [
                'points' => $n * 25,
                'reached' => $points >= $n * 25,
                'done' => (bool)$quest['reward' . $n . 'Done'],
                'description' => T('DailyQuest', 'reward_' . ($n * 25) . '%_desc'),
            ];
        }
        $lastReset = (int)DB::getInstance()->fetchScalar('SELECT lastDailyQuestReset FROM config');
        $nextReset = $lastReset > 0 ? $lastReset + (int)getGame('dailyQuestInterval') : 0;

        $this->view = new GameView();
        $this->view->vars['titleInHeader'] = '';
        $this->view->vars['bodyCssClass'] = 'perspectiveResources';
        $this->view->vars['contentCssClass'] = 'dailyQuestsT46';
        $this->view->vars['showCloseButton'] = TRUE;
        $this->view->vars['content'] = PHPBatchView::render('dailyQuestT46/main', [
            'points' => $points,
            'quests' => $quests,
            'milestones' => $milestones,
            'nextReset' => $nextReset,
            'checker' => $session->getChecker(),
        ]);
    }
}
