<?php
/*
 * "Your tasks" of the Travian Legends look (task overview of the mentor box) – port of TasksCtrl of the main branch.
 * Only with the legends655 look; the original T4.6 look keeps its quest system.
 */
include("application/Account.php");
if (!Skin::isLegends()) {
    header('Location: dorf1.php');
    exit;
}
$perPage = 7;
$scope = ($_GET['s'] ?? '') === 'general' ? 'general' : 'village';
$model = new LegendsTasks();
if (isset($_GET['collect'], $_GET['c']) && is_string($_GET['collect'])) {
    if ($_GET['c'] === $session->checker) {
        $session->changeChecker();
        $model->collect($scope, $_GET['collect']);
    }
    header('Location: tasks.php?s=' . $scope . (isset($_GET['p']) ? '&p=' . (int)$_GET['p'] : ''));
    exit;
}
$all = $model->getTasks($scope);
$pages = max(1, (int)ceil(count($all) / $perPage));
$page = max(1, min($pages, (int)($_GET['p'] ?? 1)));
$heroLevel = LegendsTasks::heroLevel();
$vars = [
    'scope' => $scope,
    'villageName' => $village->vname,
    'tasks' => array_slice($all, ($page - 1) * $perPage, $perPage),
    'page' => $page,
    'pages' => $pages,
    'checker' => $session->checker,
    'heroLevel' => $heroLevel,
    'bonus' => LegendsTasks::bonusPercent($heroLevel),
    'nextBonus' => LegendsTasks::bonusPercent($heroLevel + 1),
];
ob_start();
include("application/views/Legends/tasks.php");
$pageBody = ob_get_clean();
$pageTitle = LegendsUi::t('TASKS_TITLE', 'Your tasks');
$pageContentClass = 'tasks';
include("application/views/Legends/page.php");
