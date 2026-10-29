<?php
/* Onboarding guide of the Travian Legends look (resources/View/LegendsTutorial.php, js/Game/Legends/tutorial.js). */
$session = \Core\Session::getInstance();
if ($session->isSitter()) {
    return;
}
$tutState = \resources\View\LegendsTutorial::state((int)$session->getPlayerId());
if ($tutState === null) {
    return;
}
$tutTexts = T('LegendsTutorial', '.');
?>
<script type="text/javascript">window.LegendsTutorialConfig = <?=json_encode(['state' => $tutState, 'T' => $tutTexts], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS); ?>;</script>
<script type="text/javascript" src="js/Game/Legends/tutorial.js?v=<?=ov_asset_version(PUBLIC_PATH . 'js/Game/Legends/tutorial.js'); ?>"></script>
