<?php
/*
 * Daily quests of the Travian Legends look ("Q" button in the top navigation) – port of DailyQuestsCtrl of the main
 * branch on the T4.6 daily quest data (DailyQuests.php). Only with the legends655 look; the original look keeps
 * the classic dialog.
 */
include("application/Account.php");
if (!Skin::isLegends()) {
    header('Location: dorf1.php');
    exit;
}
$uid = (int)$session->uid;
if (isset($_GET['collect'], $_GET['c'])) {
    if ($_GET['c'] === $session->checker) {
        $session->changeChecker();
        foreach ([1, 2, 3, 4] as $n) DailyQuests::collect($uid, $n, (int)$village->wid);
    }
    header('Location: dailyquests.php');
    exit;
}
$row = DailyQuests::row($uid);
$points = DailyQuests::points($row);
$vars = [
    'points' => $points,
    'quests' => DailyQuests::quests($row),
    'milestones' => DailyQuests::milestones($row, $points),
    'checker' => $session->checker,
];
ob_start();
include("application/views/Legends/dailyQuests.php");
$pageBody = ob_get_clean();
$pageTitle = '';
$pageContentClass = 'dailyQuestsT46';
include("application/views/Legends/page.php");
