<?php
/*
 * "Your tasks" of the Travian Legends look (task overview of the mentor box) – port of TasksCtrl of the main branch.
 * Only with the legends655 look; the original T4.6 look keeps its quest system.
 *   tasks.php?s=village|general            overview (7 tasks a page, &p=)
 *   tasks.php?s=…&t=<task>                 details of one task: description and its steps (3 a page, &dp=)
 *   tasks.php?s=…&collect=<task>&c=<checker>[&ajax=1]
 *                                          collects the reached step; with ajax=1 the answer is JSON for the
 *                                          collect animation (application/legends/tasks.js), else a redirect back
 */
include("application/Account.php");
if (!Skin::isLegends()) {
    header('Location: dorf1.php');
    exit;
}
$perPage = 7;
$detailPerPage = 3;
$scope = ($_GET['s'] ?? '') === 'general' ? 'general' : 'village';
$model = new LegendsTasks();
if (isset($_GET['collect'], $_GET['c']) && is_string($_GET['collect'])) {
    $reward = null;
    if ($_GET['c'] === $session->checker) {
        $session->changeChecker();
        $reward = $model->collect($scope, $_GET['collect']);
    }
    if (!empty($_GET['ajax'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $reward !== null, 'reward' => $reward, 'checker' => $session->checker]);
        exit;
    }
    $back = 'tasks.php?s=' . $scope . (isset($_GET['p']) ? '&p=' . (int)$_GET['p'] : '');
    if (isset($_GET['t']) && is_string($_GET['t'])) $back .= '&t=' . rawurlencode($_GET['t']) . (isset($_GET['dp']) ? '&dp=' . (int)$_GET['dp'] : '');
    header('Location: ' . $back);
    exit;
}
$heroLevel = LegendsTasks::heroLevel();
$vars = [
    'scope' => $scope,
    'villageName' => $village->vname,
    'checker' => $session->checker,
    'heroLevel' => $heroLevel,
    'bonus' => LegendsTasks::bonusPercent($heroLevel),
    'nextBonus' => LegendsTasks::bonusPercent($heroLevel + 1),
    'page' => max(1, (int)($_GET['p'] ?? 1)),
];
$detail = (isset($_GET['t']) && is_string($_GET['t'])) ? $model->details($scope, $_GET['t']) : null;
if ($detail !== null) {
    $pages = max(1, (int)ceil(count($detail['history']) / $detailPerPage));
    $vars['detail'] = $detail;
    $vars['detailPages'] = $pages;
    $vars['detailPage'] = max(1, min($pages, (int)($_GET['dp'] ?? 1)));
    $vars['steps'] = array_slice($detail['history'], ($vars['detailPage'] - 1) * $detailPerPage, $detailPerPage);
} else {
    $all = $model->getTasks($scope);
    $vars['pages'] = max(1, (int)ceil(count($all) / $perPage));
    $vars['page'] = min($vars['pages'], $vars['page']);
    $vars['tasks'] = array_slice($all, ($vars['page'] - 1) * $perPage, $perPage);
}
ob_start();
include("application/views/Legends/tasks.php");
$pageBody = ob_get_clean();
$pageTitle = LegendsUi::t('TASKS_TITLE', 'Your tasks');
$pageContentClass = 'tasks';
$pageInfo = LegendsUi::t('TASKS_INFO', '');
include("application/views/Legends/page.php");
