<?php
/*
 * One building of the "Construct new building" window in the Travian Legends look (graphic packs with heroV2):
 * name and description with the building picture on the right, then the framed box with the costs (missing
 * resources highlighted, "enough resources at …" with the exchange button), the benefits of level 1 and the
 * buttons: build / master builder on the left, 25 % faster build or the Travian PLUS hint on the right.
 */
$itemId = (int)$vars['itemId'];
$contract = $vars['contractLink'];
$soon = !empty($vars['soon']);
$noResources = !$soon && !empty($contract['noResources']);
$missing = $vars['missing'] ?? [];
$section2 = !$soon && (!empty($contract['fast']) || !empty($contract['plus']));
$title = T("Buildings", $itemId . '.title');
?>
<div id="contract_building<?=$itemId; ?>" class="buildingWrapper ovLegendsBuild<?=$soon ? ' soon' : ''; ?>">
    <div class="build_desc">
        <h2><?=$vars['count'] > 1 ? $vars['count'] . '. ' : ''; ?><?=$title; ?></h2>
        <?=T("Buildings", $itemId . '.desc'); ?>
    </div>
    <a href="#" onclick="return Travian.Game.iPopup(<?=$itemId; ?>,4);" class="build_logo_link">
        <img class="build_logo building big g<?=$itemId; ?> <?=htmlspecialchars($vars['tribe']); ?>" src="img/x.gif" alt="<?=$title; ?>">
    </a>
    <div class="upgradeBuilding">
        <div class="contractWrapper">
            <div class="inlineIconList resourceWrapper charges">
                <?php for ($r = 0; $r < 4; $r++): ?>
                    <div class="inlineIcon resource transfer<?=!empty($missing[$r]) ? ' fillUp' : ''; ?>"><i class="r<?=$r + 1; ?>Big"></i><span class="value value"><?=number_format_x($vars['cost'][$r]); ?></span></div>
                <?php endfor; ?>
                <div class="inlineIcon resource"><i class="cropConsumptionBig"></i><span class="value value"><?=$vars['freeCrop']; ?></span></div>
            </div>
            <div class="upgradeBlocked">
                <?php if ($noResources): ?>
                    <?=$contract['main']; ?>
                    <?=$vars['npcButton']; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="buildingBenefits">
            <div class="wrapper">
                <?php foreach ($vars['benefits'] as [$icon, $value]): ?>
                    <div class="unit"><i class="<?=$icon; ?>"></i><span class="value"><?=$value; ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($soon): ?>
            <div class="upgradeButtonsContainer"><?=$contract; ?></div>
        <?php else: ?>
            <div class="upgradeButtonsContainer<?=$section2 ? ' section2Enabled' : ''; ?>">
                <div class="section1">
                    <?php if (!$noResources && $contract['main'] !== null): ?>
                        <?=$contract['main']; ?>
                    <?php endif; ?>
                    <?=$contract['master']; ?>
                    <?php if ($contract['main'] !== null || $contract['master'] !== null): ?>
                        <div class="inlineIcon duration"><div class="iconWrapper"><i class="clock_medium"></i></div><span class="value"><?=$vars['timeInString']; ?></span></div>
                    <?php endif; ?>
                </div>
                <?php if ($section2): ?>
                    <div class="section2">
                        <?php if (!empty($contract['fast'])): ?>
                            <?=$contract['fast']; ?>
                            <div class="inlineIcon duration fast"><div class="iconWrapper"><i class="clock_medium"></i><?=\resources\View\LegendsUi::svg('speedUp', 'speedUp'); ?></div><span class="value"><?=$vars['fastTimeInString']; ?></span></div>
                        <?php else: ?>
                            <div class="plusAdvertising"><?=$contract['plus']; ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="clear"></div>
            </div>
        <?php endif; ?>
    </div>
</div>
