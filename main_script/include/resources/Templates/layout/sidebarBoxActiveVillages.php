<?php
// Travian Legends 655: five quick link slots per village instead of the four building buttons (LegendsQuickLinks)
$qlV2 = is_hero_v2();
if ($qlV2) {
    $qlLinks = \resources\View\LegendsQuickLinks::all();
    $qlKid = (int)\Core\Village::getInstance()->getKid();
    $qlSlots = \resources\View\LegendsQuickLinks::slots((int)\Core\Session::getInstance()->getPlayerId(), $qlKid);
}
?>
<div id="sidebarBoxActiveVillage" class="sidebarBox   ">
    <div class="sidebarBoxBaseBox">
        <div class="baseBox baseBoxTop">
            <div class="baseBox baseBoxBottom">
                <div class="baseBox baseBoxCenter"></div>
            </div>
        </div>
    </div>
    <div class="sidebarBoxInnerBox">
        <div class="innerBox header ">
            <?php if ($qlV2): ?>
            <div class="buttonsWrapper quickLinks villageQuickLinks" data-kid="<?=$qlKid; ?>">
                <?=\resources\View\LegendsQuickLinks::slotsHtml($qlSlots, $qlLinks) . \resources\View\LegendsQuickLinks::settingsButton('village'); ?>
            </div>
            <?php else: ?>
            <div class="buttonsWrapper">
                <?=$vars['workshop']; ?>
                <?php if ($vars['showWorkshopToolTip']): ?>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery('#<?=$vars['workshopButtonId'];?>').one('mouseenter', function (event) {
                                Travian.Game.Layout.loadLayoutButtonTitle(event.delegateTarget, 'activeVillage', 'workshopWhite');
                            });
                        });

                        jQuery('#<?=$vars['workshopButtonId'];?>').click(function (event) {
                            jQuery(window).trigger('buttonClicked', [this, {
                                "type": "green",
                                "onclick": "return false;",
                                "loadTitle": true,
                                "boxId": "activeVillage",
                                "disabled": false,
                                "speechBubble": "",
                                "class": "",
                                "id": "<?=$vars['workshopButtonId'];?>",
                                "redirectUrl": "build.php?id=<?=$vars['workshopBuildingFieldId'];?>",
                                "redirectUrlExternal": ""
                            }]);
                        });
                    </script>
                <?php endif; ?>
                <?=$vars['stable']; ?>
                <?php if ($vars['showStableToolTip']): ?>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery('#<?=$vars['stableButtonId'];?>').one('mouseenter', function (event) {
                                Travian.Game.Layout.loadLayoutButtonTitle(event.delegateTarget, 'activeVillage', 'stableWhite');
                            });
                        });

                        jQuery('#<?=$vars['stableButtonId'];?>').click(function (event) {
                            jQuery(window).trigger('buttonClicked', [this, {
                                "type": "green",
                                "onclick": "return false;",
                                "loadTitle": true,
                                "boxId": "activeVillage",
                                "disabled": false,
                                "speechBubble": "",
                                "class": "",
                                "id": "<?=$vars['stableButtonId'];?>",
                                "redirectUrl": "build.php?id=<?=$vars['stableBuildingFieldId'];?>",
                                "redirectUrlExternal": ""
                            }]);
                        });
                    </script>
                <?php endif; ?>
                <?=$vars['barracks']; ?>
                <?php if ($vars['showBarracksToolTip']): ?>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery('#<?=$vars['barracksButtonId'];?>').one('mouseenter', function (event) {
                                Travian.Game.Layout.loadLayoutButtonTitle(event.delegateTarget, 'activeVillage', 'barracksWhite');
                            });
                        });
                        jQuery('#<?=$vars['barracksButtonId'];?>').click(function (event) {
                            jQuery(window).trigger('buttonClicked', [this, {
                                "type": "green",
                                "onclick": "return false;",
                                "loadTitle": true,
                                "boxId": "activeVillage",
                                "disabled": false,
                                "speechBubble": "",
                                "class": "",
                                "id": "<?=$vars['barracksButtonId'];?>",
                                "redirectUrl": "build.php?id=<?=$vars['barracksBuildingFieldId'];?>",
                                "redirectUrlExternal": ""
                            }]);
                        });
                    </script>
                <?php endif; ?>
                <?=$vars['marketplace']; ?>
                <?php if ($vars['showMarketplaceToolTip']): ?>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery('#<?=$vars['marketplaceButtonId'];?>').one('mouseenter', function (event) {
                                Travian.Game.Layout.loadLayoutButtonTitle(event.delegateTarget, 'activeVillage', 'marketWhite');
                            });
                        });
                        jQuery('#<?=$vars['marketplaceButtonId'];?>').click(function (event) {
                            jQuery(window).trigger('buttonClicked', [this, {
                                "type": "green",
                                "onclick": "return false;",
                                "loadTitle": true,
                                "boxId": "activeVillage",
                                "disabled": false,
                                "speechBubble": "",
                                "class": "",
                                "id": "<?=$vars['marketplaceButtonId'];?>",
                                "redirectUrl": "build.php?id=<?=$vars['marketplaceBuildingFieldId'];?>",
                                "redirectUrlExternal": ""
                            }]);
                        });
                    </script>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="clear"></div>
            <?php if (is_t46_layout()): ?>
                <div class="playerName"><?=htmlspecialchars((string)($vars['playerName'] ?? '')); ?></div>
            <?php endif; ?>
            <div id="villageNameField"<?=$qlV2 && !\Core\Session::getInstance()->isSitter() ? ' data-did="' . (int)\Core\Village::getInstance()->getKid() . '"' : ''; ?>
                 class="boxTitle"><?=filter_var($vars['villageName'], FILTER_SANITIZE_STRING); ?></div>
        </div>
        <div class="innerBox content">
            <?php if (is_t46_layout()): ?>
                <div class="population"><?=T("Statistics", "population"); ?>: <?=(int)($vars['population'] ?? 0); ?></div>
            <?php endif; ?>
            <div
                    class="loyalty <?=$vars['loyalty'] > 100 ? "high" : (($vars['loyalty'] >= 50 || $vars['loyalty'] == 100) ? "medium" : "low"); ?>">
                <?=T("inGame", "loyalty"); ?>:
                <span>‎&#x202d;&#x202d;<?=round($vars['loyalty']); ?>&#x202c;&#37;&#x202c;‎</span>
            </div>
        </div>
        <div class="innerBox footer">
            <?=$vars['edit']; ?>
        </div>
    </div>
</div>
<?php if ($qlV2): ?>
<script type="text/javascript">window.LegendsQuickLinksConfig = <?=\resources\View\LegendsQuickLinks::config($qlLinks); ?>;</script>
<script type="text/javascript" src="js/Game/Legends/dialog.js?v=1"></script>
<script type="text/javascript" src="js/Game/Legends/quickLinks.js?v=1"></script>
<script type="text/javascript" src="js/Game/Legends/villageRename.js?v=1"></script>
<?php endif; ?>
