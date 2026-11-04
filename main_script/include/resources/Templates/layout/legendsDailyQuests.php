<?php
/* Travian Legends 655 (is_hero_v2()): the daily quests open in the Legends dialog (js/Game/Legends/dailyQuests.js, after
   js/Game/Legends/dialog.js of layout/legendsGoldClub.php). */
?>
<script type="text/javascript">window.LegendsDailyQuestsTexts = <?=json_encode(['close' => T('Global', 'General.close', 'Close')], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS); ?>;</script>
<script type="text/javascript" src="js/Game/Legends/dailyQuests.js?v=<?=ov_asset_version(PUBLIC_PATH . 'js/Game/Legends/dailyQuests.js'); ?>"></script>
