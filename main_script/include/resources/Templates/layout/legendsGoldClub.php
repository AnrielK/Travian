<?php
/* Travian Legends 655 (is_hero_v2()): the Gold Club dialog (js/Game/Legends/goldClub.js) and the Travian PLUS dialog
   (js/Game/Legends/plus.js) in the Legends look replace the classic Travian.Game.GoldclubDialog / PlusDialog on every page. */
$gcTexts = T('LegendsGoldClub', '.');
$gcTexts['goldCoin'] = \resources\View\LegendsUi::goldCoin('goldCoin');
$gcTexts['close'] = T('Global', 'General.close', 'Close');
$plusTexts = T('LegendsPlus', '.');
$plusTexts['goldCoin'] = $gcTexts['goldCoin'];
$plusTexts['close'] = $gcTexts['close'];
?>
<script type="text/javascript">window.LegendsGoldClubTexts = <?=json_encode($gcTexts, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS); ?>;
window.LegendsPlusTexts = <?=json_encode($plusTexts, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS); ?>;</script>
<script type="text/javascript" src="js/Game/Legends/dialog.js?v=1"></script>
<script type="text/javascript" src="js/Game/Legends/goldClub.js?v=1"></script>
<script type="text/javascript" src="js/Game/Legends/plus.js?v=1"></script>
