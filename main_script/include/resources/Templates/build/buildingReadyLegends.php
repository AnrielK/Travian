<?php
/*
 * Upgrade page of an existing building / resource field in the Travian Legends look (graphic packs with heroV2):
 * illustrated header with the building values, collapsible description, then the upgrade box with the costs,
 * the benefits of the next level and the upgrade buttons. Same contract data as build/buildingReadyWrapper.php.
 */

use Core\Config;

$itemId = (int)$vars['itemId'];
$fields = $itemId >= 1 && $itemId <= 4;
$cp = $vars['culturePointsAndPopulation'];
$next = $vars['nextLevelCpPop'];
$noResources = !empty($vars['contractLink']['noResources']);
$number = static fn($v) => number_format_x($v);
// Benefits row: [icon class, current value, delta of the next level, formatter]
$percent = static fn($v) => $v . '%';
$benefits = [
    ['culturePoints_medium', $cp['cp'], $next['cp'], $number],
    ['population_medium', $cp['pop'], $next['pop'], $number],
];
foreach ([
    'constructionTime' => ['constructionTime_medium', $percent],
    'infantryBonusTime' => ['infantryBonusTime_medium', $percent],
    'cavalryBonusTime' => ['cavalryBonusTime_medium', $percent],
    'siegeTime' => ['siegeTime_small', $percent],
    'troopSpeed' => ['troopSpeed_medium', $percent],
    'merchantCap' => ['merchantCap_small', $number],
    'maxTraps' => ['maxTraps_medium', $number],
    'warehouseCap' => ['warehouseCap_medium', $number],
    'granaryCap' => ['granaryCap_medium', $number],
    'crannyCap' => ['crannyCap_small', $number],
] as $key => [$icon, $format]) {
    if (isset($cp[$key]) && $cp[$key] !== null) {
        $benefits[] = [$icon, $cp[$key], $next[$key] ?? 0, $format];
    }
}
$signed = static function ($delta, callable $format): string {
    $delta = (float)$delta;
    return ($delta < 0 ? '−' : '+') . $format(abs($delta));
};
?>
<?php if (Config::getInstance()->extraSettings->upgradeToMaxLevel->enabled || in_array($itemId, [10, 11, 38, 39], true)): ?>
    <script type="text/javascript">
        function getGidX() {
            return {additionalData: {gid: <?=(int)$vars['building_field']; ?>}};
        }
    </script>
<?php endif; ?>
<div class="upgradeHeader<?=$fields ? ' levelSpecificInfo' : ''; ?>">
    <?php if ($vars['showValuesTable']): ?>
        <table cellpadding="1" cellspacing="1" id="build_value">
            <?=$vars['valueTable']; ?>
        </table>
    <?php endif; ?>
    <div class="buildingIllustration g<?=$itemId; ?><?=$fields ? '' : ' ' . htmlspecialchars($vars['tribe']); ?>"></div>
</div>
<div class="buildingDescription" id="ovBuildingDescription">
    <div class="headline">
        <button type="button" class="openedClosedSwitch switchOpened" aria-expanded="true"></button>
        <h4><?=T('Buildings', 'description', 'Description'); ?></h4>
    </div>
    <div class="description"><?=T("Buildings", $itemId . '.desc'); ?></div>
</div>
<?php if (!$vars['showCosts']): ?>
    <div class="upgradeBuilding completed">
        <div class="completedMessage"><span class="errorMessage"><?=$vars['contractLink']['main']; ?></span></div>
        <div class="buildingBenefits completed">
            <div class="wrapper">
                <?php foreach ($benefits as [$icon, $value, $delta, $format]): ?>
                    <div class="unit"><i class="<?=$icon; ?>"></i><span class="value"><?=$format($value); ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php
    $plusAdvertising = !empty($vars['contractLink']['plus']) && !empty($vars['contractLink']['master']);
    $fast = !empty($vars['contractLink']['fast']) && !$plusAdvertising ? $vars['contractLink']['fast'] : null;
    $section2 = $plusAdvertising || $fast !== null || !empty($vars['contractLink']['extraModuleButton']);
    ?>
    <div class="upgradeBuilding">
        <div id="contract" class="contractWrapper">
            <div class="inlineIconList resourceWrapper">
                <?php for ($r = 0; $r < 4; $r++): ?>
                    <div class="inlineIcon resource<?=!empty($vars['missing'][$r]) ? ' fillUp' : ''; ?>"><i class="r<?=$r + 1; ?>Big"></i><span class="value value"><?=number_format_x($vars['cost'][$r]); ?></span></div>
                <?php endfor; ?>
                <div class="inlineIcon resource"><i class="cropConsumptionBig"></i><span class="value value"><?=$vars['freeCrop']; ?></span></div>
            </div>
            <?php if ($noResources): ?>
                <div class="upgradeBlocked">
                    <div class="errorMessage">
                        <?=$vars['contractLink']['main']; ?>
                        <span class="hide"><?=appendTimer(\Core\Village::getInstance()->calcWhenResourcesAreAvailable($vars['cost'])); ?></span>
                    </div>
                    <?=$vars['npcButton']; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="buildingBenefits">
            <div class="wrapper">
                <?php foreach ($benefits as [$icon, $value, $delta, $format]): ?>
                    <div class="unit">
                        <i class="<?=$icon; ?>"></i>
                        <span class="value"><?=$format($value); ?></span>
                        <span class="delta">(&#8237;<?=$signed($delta, $format); ?>&#8236;)</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="upgradeButtonsContainer<?=$section2 ? ' section2Enabled' : ''; ?>">
            <div class="section1">
                <?php if (!$noResources && $vars['contractLink']['main'] !== null): ?>
                    <?=$vars['contractLink']['main']; ?>
                <?php endif; ?>
                <?=$vars['contractLink']['master']; ?>
                <div class="inlineIcon duration"><i class="clock_medium"></i><span class="value"><?=$vars['timeInString']; ?></span>
                    <?php if (!empty($vars['contractLink']['waitLoop'])): ?>
                        <span class="waitLoop">(<?=T("Buildings", "waitLoop"); ?>)</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($section2): ?>
                <div class="section2">
                    <?php if ($plusAdvertising): ?>
                        <div class="plusAdvertising"><?=str_replace(['<br />', '<br>', '<br/>'], '', $vars['contractLink']['plus']); ?></div>
                    <?php endif; ?>
                    <?php if ($fast !== null): ?>
                        <?=$fast; ?>
                        <div class="inlineIcon duration fast"><div class="iconWrapper"><i class="clock_medium"></i><?=\resources\View\LegendsUi::svg('speedUp', 'speedUp'); ?></div><span class="value"><?=$vars['fastTimeInString']; ?></span></div>
                    <?php endif; ?>
                    <?=str_replace(['<br />', '<br>', '<br/>'], '', $vars['contractLink']['extraModuleButton']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<script type="text/javascript">
    (function () {
        // Description open / closed, remembered per browser (like Legends).
        var box = document.getElementById('ovBuildingDescription');
        if (!box) return;
        var toggle = box.querySelector('.openedClosedSwitch'), key = 'ovBuildDescriptionCollapsed';
        function set(collapsed) {
            box.classList.toggle('collapsed', collapsed);
            toggle.classList.toggle('switchOpened', !collapsed);
            toggle.classList.toggle('switchClosed', collapsed);
            toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        }
        try { set(window.localStorage.getItem(key) === '1'); } catch (e) {}
        box.querySelector('.headline').addEventListener('click', function () {
            var collapsed = !box.classList.contains('collapsed');
            set(collapsed);
            try { window.localStorage.setItem(key, collapsed ? '1' : '0'); } catch (e) {}
        });
    })();
</script>
<div class="clear"></div>
