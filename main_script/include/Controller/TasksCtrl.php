<?php

namespace Controller;

use Core\Session;
use Model\TaskListModel;
use resources\View\GameView;
use resources\View\PHPBatchView;

/**
 * "Your tasks" page of the Travian Legends style (graphic pack 29c89d54). Other graphic packs keep the
 * classic quest system only, so the page sends them back to the village.
 */
class TasksCtrl extends GameCtrl
{
    private const PER_PAGE = 7;

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
            if ($_GET['c'] === $session->getChecker()) {
                $session->changeChecker();
                $model->collect($scope, $_GET['collect']);
            }
            $this->redirect('tasks.php?s=' . $scope . (isset($_GET['p']) ? '&p=' . (int)$_GET['p'] : ''));
            return;
        }

        $tasks = $model->getTasks($scope);
        $pages = max(1, (int)ceil(count($tasks) / self::PER_PAGE));
        $page = max(1, min($pages, (int)($_GET['p'] ?? 1)));
        $heroLevel = $model->getHeroLevel();

        $this->view = new GameView();
        $this->view->vars['titleInHeader'] = T('Tasks', 'title', 'Your tasks');
        $this->view->vars['bodyCssClass'] = 'perspectiveResources';
        $this->view->vars['contentCssClass'] = 'tasks';
        $this->view->vars['content'] = PHPBatchView::render('tasks/main', [
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
