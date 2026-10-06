<?php
/*
 * Resource view for the Travian Legends layout (graphic pack 29c89d54): field image + level badges
 * positioned by the pack's CSS (.level.buildingSlotN), clickable field outlines, info boxes on the right.
 * Same data as dorf1/main.php (Dorf1Ctrl).
 */
use Core\Session;

$fastUp = Session::getInstance()->fastUpgradeActive() ? '&fastUP=1' : '';
// Badge positions of the pack (top/left of each 25px level badge) – the click outline is an ellipse around it.
$t46Badges = [1 => [13, 171], [14, 261], [26, 329], [52, 113], [65, 226], [72, 283], [70, 368], [103, 53], [103, 134],
    [104, 324], [104, 412], [164, 61], [154, 134], [190, 270], [159, 392], [244, 165], [249, 256], [226, 346]];
$tribe = (int)($vars['t46Race'] ?? 1);
?>
<div id="resourceFieldContainer" class="resourceField<?=(int)$vars['fieldType']; ?> tribe<?=in_array($tribe, [1, 2, 3, 6, 7], true) ? $tribe : 1; ?>">
    <?php foreach ($vars['maps'] as $i => $map): $index = $i + 1;
        $area = $vars['areas'][$i]; ?>
        <a href="build.php?id=<?=$index; ?><?=$fastUp; ?>"
           class="level <?=$vars['showColored'] ? 'colorLayer ' : ''; ?><?=$map['color']; ?> gid<?=$map['item_id']; ?> level<?=$map['level']; ?> buildingSlot<?=$index; ?> aid<?=$index; ?><?=$map['upgradeState'] ? ' underConstruction' : ''; ?>"
           title="<?=htmlspecialchars($area['title']); ?>">
            <?php if ($vars['showColored']): ?>
                <div class="labelLayer"><?=$map['level'] > 0 ? (int)$map['level'] : ''; ?></div>
            <?php else: ?>
                <span><?=$map['level'] > 0 ? (int)$map['level'] : ''; ?></span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
    <a class="villageCenter" href="dorf2.php" title="<?=T("inGame", "Navigation.Buildings"); ?>"></a>
    <svg class="resourceField" width="473" height="304" viewBox="0 0 473 304">
        <?php foreach ($t46Badges as $index => [$top, $left]): $cx = $left + 12; $cy = $top + 14; ?>
            <path class="buildingSlot<?=$index; ?>" d="M<?=$cx - 36; ?> <?=$cy; ?>a36 24 0 1 0 72 0a36 24 0 1 0 -72 0z"
                  onclick="window.location.href='build.php?id=<?=$index; ?><?=$fastUp; ?>'"><title><?=htmlspecialchars($vars['areas'][$index - 1]['title'] ?? ''); ?></title></path>
        <?php endforeach; ?>
    </svg>
    <svg class="villageCenter" width="473" height="304" viewBox="0 0 473 304">
        <path d="M196 152a42 30 0 1 0 84 0a42 30 0 1 0 -84 0z" onclick="window.location.href='dorf2.php'"><title><?=T("inGame", "Navigation.Buildings"); ?></title></path>
    </svg>
</div>
<div class="villageInfoWrapper">
    <?=$vars['onLoadBuildings']; ?>
    <div class="villageInfobox movements">
        <?=$vars['movements']; ?>
    </div>
    <div class="villageInfobox production">
        <table id="production" cellpadding="1" cellspacing="1">
            <thead>
            <tr>
                <th colspan="4"><?=T("Dorf1", "production.production per hour"); ?>:</th>
            </tr>
            </thead>
            <tbody>
            <?php for ($r = 0; $r < 4; ++$r): ?>
                <tr>
                    <td class="ico">
                        <div>
                            <?php if ($vars['productionBoost'][$r]): ?>
                                <img src="img/x.gif" class="productionBoost" alt="<?=T("inGame", "productionBoost.woodProductionBoost"); ?>">
                            <?php endif; ?>
                            <i class="r<?=$r + 1; ?>"></i>
                        </div>
                    </td>
                    <td class="res"><?=T("Dorf1", "production.resources." . ($r + 1)); ?>:</td>
                    <td class="num"><?=number_format_x($vars['production'][$r], 1e12); ?></td>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
        <?=$vars['goldProductionBoostButton']; ?>
    </div>
    <div class="villageInfobox units">
        <table id="troops" cellpadding="1" cellspacing="1">
            <thead>
            <tr>
                <th colspan="3"><?=T("Dorf1", "units"); ?>:</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($vars['unitsSummary'] <= 0): ?>
                <tr>
                    <td class="noTroops"><?=T("Dorf1", "none"); ?>.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($vars['units'] as $key => $num): ?>
                    <tr>
                        <td class="ico">
                            <a href="build.php?gid=16&tt=1#td">
                                <img class="unit u<?=$key == 98 ? 'hero' : $key; ?>" src="img/x.gif"
                                     alt="<?=T("Troops", ($key == 98 ? 'hero' : $key) . '.title'); ?>">
                            </a>
                        </td>
                        <td class="num"><?=number_format_x($num, 1e12); ?></td>
                        <td class="un"><?=T("Troops", ($key == 98 ? 'hero' : $key) . '.title'); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($vars['heroOnly']): ?>
                    <tr>
                        <td class="noTroops"><?=T("Dorf1", "none"); ?>.</td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
