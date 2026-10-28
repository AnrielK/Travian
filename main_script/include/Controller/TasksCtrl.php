<?php

namespace Controller;

use Core\Session;
use Model\TaskListModel;
use resources\View\GameView;
use resources\View\PHPBatchView;

/**
 * "Your tasks" page of the Travian Legends style (graphic pack 29c89d54). Other graphic packs keep the
 * classic quest system only, so the page sends them back to the village.
 *   tasks.php?s=village|general                 overview (7 tasks a page, &p=)
 *   tasks.php?s=…&t=<task>                      details of one task: description and all its steps (3 a page, &dp=;
 *                                               without &dp the page holding the current step)
 *   tasks.php?s=…&collect=<task>&c=<checker>    collects the reached step; with &ajax=1 the answer is JSON for the
 *                                               collect animation (js/Game/Legends/tasks.js), else a redirect back
 */
class TasksCtrl extends GameCtrl
{
    private const PER_PAGE = 7;
    private const DETAIL_PER_PAGE = 3;

    public function __construct()
    {
        parent::__construct();
        if (!is_t46_layout()) {
            $this->redirect('dorf1.php');
            return;
        }
        $session = Session::getInstance();
        $scope = ($_GET['s'] ?? '') === 'general' ? 'general' : 'village';
        $model = new TaskListModel();

        if (isset($_GET['collect'], $_GET['c']) && is_string($_GET['collect'])) {
            $reward = null;
            if ($_GET['c'] === $session->getChecker()) {
                $session->changeChecker();
                $reward = $model->collect($scope, $_GET['collect']);
            }
            if (!empty($_GET['ajax'])) {
                header('Content-Type: application/json; charset=utf-8');
                header('Cache-Control: no-store');
                echo json_encode(['ok' => $reward !== null, 'reward' => $reward, 'checker' => $session->getChecker()]);
                exit;
            }
            $back = 'tasks.php?s=' . $scope . (isset($_GET['p']) ? '&p=' . (int)$_GET['p'] : '');
            if (isset($_GET['t']) && is_string($_GET['t'])) {
                $back .= '&t=' . rawurlencode($_GET['t']) . (isset($_GET['dp']) ? '&dp=' . (int)$_GET['dp'] : '');
            }
            $this->redirect($back);
            return;
        }

        $detail = isset($_GET['t']) && is_string($_GET['t']) ? $model->details($scope, $_GET['t']) : null;
        $tasks = $detail === null ? $model->getTasks($scope) : [];
        $pages = max(1, (int)ceil(count($tasks) / self::PER_PAGE));
        $page = max(1, min($pages, (int)($_GET['p'] ?? 1)));
        $detailVars = [];
        if ($detail !== null) {
            $detailPages = max(1, (int)ceil(count($detail['history']) / self::DETAIL_PER_PAGE));
            $detailPage = max(1, min($detailPages, (int)($_GET['dp'] ?? (intdiv((int)$detail['currentIndex'], self::DETAIL_PER_PAGE) + 1))));
            $detailVars = [
                'detail' => $detail,
                'detailPage' => $detailPage,
                'detailPages' => $detailPages,
                'steps' => array_slice($detail['history'], ($detailPage - 1) * self::DETAIL_PER_PAGE, self::DETAIL_PER_PAGE),
            ];
        }
        $heroLevel = $model->getHeroLevel();

        $this->view = new GameView();
        $this->view->vars['titleInHeader'] = T('Tasks', 'title', 'Your tasks');
        $this->view->vars['bodyCssClass'] = 'perspectiveResources';
        $this->view->vars['contentCssClass'] = 'tasks';
        $this->view->vars['content'] = PHPBatchView::render('tasks/main', $detailVars + [
            'scope' => $scope,
            'villageName' => $session->village->getName(),
            'tasks' => array_slice($tasks, ($page - 1) * self::PER_PAGE, self::PER_PAGE),
            'page' => $page,
            'pages' => $pages,
            'checker' => $session->getChecker(),
            'heroLevel' => $heroLevel,
            'bonus' => $model->getBonusPercent($heroLevel),
            'nextBonus' => $model->getBonusPercent($heroLevel + 1),
        ]);
    }
}
