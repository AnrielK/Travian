<?php
LegendsUi::helpStep('#heroV2Attributes .stats', 'below', 'HELP_HERO_STATS_T', 'Hero status', 'HELP_HERO_STATS', 'Health, experience and speed of your hero. Health regenerates every day; if it drops to zero the hero dies and has to be revived. Experience from battles and adventures raises the hero level.');
LegendsUi::helpStep('#heroV2Attributes .heroProduction', 'below', 'HELP_HERO_PRODUCTION_T', 'Hero production', 'HELP_HERO_PRODUCTION', 'Choose which resource your hero produces in his home village. "All resources" gives a smaller amount of every resource.');
LegendsUi::helpStep('#heroV2Attributes .heroAttributes', 'above', 'HELP_HERO_ATTRIBUTES_T', 'Attribute points', 'HELP_HERO_ATTRIBUTES', 'Every level gives you new points. Spend them on fighting strength, offence and defence bonus or resource production and confirm them with Save.');
/*
 * Hero attributes in the Travian Legends 655 look (Templates/hero/v2/attributes.php of the main branch) with the T4.6
 * hero data: health / experience / speed, hero production, attribute points with +/- buttons, the hide switch.
 * Saving goes through the classic ajax command heroSetAttributes (ajax.php).
 * Needs $session, $heroF, $hero_levels, $hero_t, $village, $database, $lang (hero_inventory.php).
 */
$h = $session->heroD;
$hlTp = $session->tribe == 1 ? 100 : 80;
$hlFree = max(0, (int)$h['level'] * 4 - ((int)$h['power'] + (int)$h['offBonus'] + (int)$h['defBonus'] + (int)$h['product']));
$hlResource = 4;
for ($i = 0; $i <= 4; $i++) if (!php7_is_zero($h['r' . $i])) { $hlResource = $i; break; }
$hlAll = 3 * SPEED;            // per production point, all resources
$hlEach = 10 * SPEED;          // per production point, one resource
$hlProduction = [0 => $hlAll * (int)$h['product']] + array_fill(1, 4, $hlEach * (int)$h['product']);
$hlIcons = [0 => 'resources_medium', 1 => 'lumber_small', 2 => 'clay_small', 3 => 'iron_small', 4 => 'crop_small'];
$hlTiny = [0 => 'resources_small', 1 => 'lumber_tiny', 2 => 'clay_tiny', 3 => 'iron_tiny', 4 => 'crop_tiny'];
if ($h['level'] != 100) {
    $hlExp = round(100 * safe_div(($hero_levels[$h['level']] - $h['experience']), ($hero_levels[$h['level']] - $hero_levels[$h['level'] + 1])), 1);
    $hlExp = max(0, min(100, $hlExp));
    $hlExpTitle = HEROI33 . '||' . HEROI31 . ' ' . ($hero_levels[$h['level'] + 1] - $h['experience']) . ' ' . HEROI32 . ' ' . ($h['level'] + 1) . '.';
} else {
    $hlExp = 100;
    $hlExpTitle = HEROI33;
}
$hlStatus = $heroF->getHeroStatus();
$hlPower = 100 + $hlTp * (int)$h['power'] + (int)$h['itempower'];
$hlAttributes = [
    'power' => ['name' => HEROI11, 'icon' => 'attributeStrength_medium', 'used' => (int)$h['power'], 'title' => HEROI8 . '||' . HEROI7, 'value' => number_format($hlPower)],
    'offBonus' => ['name' => HEROI14, 'icon' => 'attributeOffBonus_medium', 'used' => (int)$h['offBonus'], 'title' => HEROI14 . '||' . HEROI13, 'value' => number_format((int)$h['offBonus'] * 0.2, 1) . '%'],
    'defBonus' => ['name' => HEROI16, 'icon' => 'attributeDefBonus_medium', 'used' => (int)$h['defBonus'], 'title' => HEROI16 . '||' . HEROI15, 'value' => number_format((int)$h['defBonus'] * 0.2, 1) . '%'],
    'productionPoints' => ['name' => HEROI19, 'icon' => 'attributeRessourceBonus_medium', 'used' => (int)$h['product'], 'title' => HEROI19 . '||' . HEROI17, 'value' => (int)$h['product']],
];
$hlArrow = '<svg class="arrowDouble" viewBox="0 0 10 7.75"><path d="M0 0h3l3 3.88-3 3.87H0l3-3.87"/><path d="M4 0h3l3 3.88-3 3.87H4l3-3.87"/></svg>';
$hlHealth = (int)round($h['health']);
?>
<div class="contentV2" id="heroV2Attributes" data-resource="<?php echo $hlResource; ?>" data-behaviour="<?php echo php7_is_zero($h['hide']) ? 'fight' : 'hide'; ?>"
     data-free="<?php echo $hlFree; ?>" data-fs-per-point="<?php echo $hlTp; ?>" data-item-fs="<?php echo (int)$h['itempower']; ?>"
     data-production-rate="<?php echo htmlspecialchars(json_encode(['all' => $hlAll, 'each' => $hlEach])); ?>">
    <?php if ($hlStatus == 101) { ?>
        <div class="attributeBox heroDead">
            <div class="status"><i class="statusDead_medium"></i>
                <div class="title"><?php echo $heroF->printHeroSt(); ?></div>
            </div>
            <div class="title"><?php echo $lang['Hero']['Revive04']; ?></div>
            <div class="reviveWrapper">
                <div class="reviveWithResources charges">
                    <div class="inlineIconList resourceWrapper">
                        <?php foreach (['wood', 'clay', 'iron', 'crop'] as $i => $r) { ?>
                            <div class="inlineIcon resource"><i class="r<?php echo $i + 1; ?>"></i><span class="value"><?php echo number_format($hero_t[$h['level']][$r]); ?></span></div>
                        <?php } ?>
                    </div>
                    <div><div class="inlineIcon duration"><i class="clock_medium"></i><span class="value"><?php echo gmdate('H:i:s', (int)($hero_t[$h['level']]['time'] / SPEED * 1.5)); ?></span></div></div>
                </div>
                <?php if (!$h['revivetime']) {
                    echo LegendsUi::button(defined('HEROI28') ? HEROI28 : 'Revive', ['href' => 'hero_inventory.php?revive=1', 'class' => 'reviveWithResources']);
                } ?>
            </div>
        </div>
    <?php } ?>

    <div class="attributeBox">
        <div class="stats">
            <div class="name" title="<?php echo htmlspecialchars(HEROI25 . '||' . HEROI44); ?>"><i class="attributeHealth_medium"></i><span><?php echo rtrim(HEROI25, ':'); ?></span></div>
            <?php echo LegendsUi::progressBar(['color' => 'green', 'primary' => $hlHealth, 'title' => HEROI25 . '||' . HEROI44]); ?>
            <div class="value"><?php echo $hlHealth; ?>%</div>
            <div class="name" title="<?php echo htmlspecialchars($hlExpTitle); ?>"><i class="attributeExperience_medium"></i><span><?php echo rtrim(HEROI33, ':'); ?></span></div>
            <?php echo LegendsUi::progressBar(['color' => 'blue', 'primary' => $hlExp, 'title' => $hlExpTitle]); ?>
            <div class="value"><?php echo number_format((int)$h['experience']); ?></div>
            <div class="name" title="<?php echo htmlspecialchars(HEROI37 . '||' . HEROI36); ?>"><i class="attributeSpeed_medium"></i><span><?php echo rtrim(HEROI37, ':'); ?></span></div>
            <div class="value speedValue"><?php echo $hlArrow; ?><span><strong><?php echo $h['speed'] * INCREASE_SPEED; ?></strong> <?php echo HEROI38; ?></span></div>
        </div>
    </div>

    <div class="attributeBox">
        <div class="heroProduction">
            <div class="productionTotal" title="<?php echo htmlspecialchars(HEROI43 . '||' . HEROI47); ?>">
                <strong><?php echo rtrim(HEROI43, ':'); ?></strong>
                <div class="productionItem"><span class="value">+ 6</span><i class="crop_tiny"></i></div>
                <div class="productionItem"><span class="value">+ <?php echo number_format($hlProduction[$hlResource]); ?></span><i class="<?php echo $hlTiny[$hlResource]; ?>"></i></div>
            </div>
            <div class="changeProduction">
                <?php foreach ($hlIcons as $hlType => $hlIcon) { ?>
                    <div class="resource <?php echo $hlIcon; ?>">
                        <?php echo LegendsUi::button('<i class="' . $hlIcon . '"></i>', ['icon' => true, 'toggle' => true, 'activeNotClickable' => true,
                            'active' => $hlResource === $hlType, 'class' => 'pickProductionType', 'data' => ['resource' => $hlType]]); ?>
                        <span class="value" data-production="<?php echo $hlType; ?>"><?php echo number_format($hlProduction[$hlType]); ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="attributeBox">
        <div class="heroAttributes">
            <div class="attributes formV2<?php echo $hlFree > 0 ? ' withSliders levelUp' : ''; ?>">
                <div class="title"><?php echo HEROI10; ?></div>
                <div class="points_descr"><?php echo LegendsUi::t('HEROL_POINTS', 'Points available'); ?></div>
                <div class="pointsAvailable" id="heroV2FreePoints"><?php echo $hlFree; ?></div>
                <?php foreach ($hlAttributes as $hlKey => $hlA) { ?>
                    <div class="name" title="<?php echo htmlspecialchars($hlA['title']); ?>"><i class="<?php echo $hlA['icon']; ?>"></i><span><?php echo $hlA['name']; ?></span></div>
                    <?php echo LegendsUi::progressBar(['color' => 'blue', 'primary' => $hlA['used'], 'secondary' => 0, 'title' => $hlA['title'],
                        'buttons' => $hlFree > 0 && $hlA['used'] < 100 ? $hlKey : null, 'plusDisabled' => $hlFree === 0, 'minusDisabled' => true]); ?>
                    <div class="inputRatio pointsRatio isLtr">
                        <span class="nominator"><label class="points"><input type="text" inputmode="numeric" name="<?php echo $hlKey; ?>" autocomplete="off"
                                  value="<?php echo $hlA['used']; ?>" data-used="<?php echo $hlA['used']; ?>"<?php echo $hlFree === 0 || $hlA['used'] >= 100 ? ' disabled' : ''; ?>></label></span>
                        <span class="separator">/</span>
                        <span class="denominator"><div class="value" data-value="<?php echo $hlKey; ?>"><?php echo $hlA['value']; ?></div></span>
                    </div>
                <?php } ?>
                <?php echo LegendsUi::button(LegendsUi::t('HEROL_SAVE', 'Save'), ['id' => 'savePoints', 'disabled' => true, 'disabledClass' => true]); ?>
            </div>
        </div>
    </div>

    <div class="attributeBox">
        <div class="heroState"><i class="<?php echo $hlStatus == 100 ? 'statusHome_medium' : 'statusRunning_medium'; ?>"></i>
            <div><?php echo $heroF->printHeroSt(); ?></div>
        </div>
        <?php echo LegendsUi::toggleSwitch('attackBehaviour', !php7_is_zero($h['hide']), HEROI40, 'heroHideSwitch'); ?>
    </div>
</div>
<script type="text/javascript">
    (function () {
        var root = document.getElementById('heroV2Attributes');
        if (!root) return;
        var free = parseInt(root.dataset.free, 10) || 0;
        var rate = JSON.parse(root.dataset.productionRate);
        var fsPerPoint = parseInt(root.dataset.fsPerPoint, 10) || 0, itemFs = parseInt(root.dataset.itemFs, 10) || 0;
        var added = {power: 0, offBonus: 0, defBonus: 0, productionPoints: 0};
        var saveButton = document.getElementById('savePoints');

        function save(points) {
            var body = new URLSearchParams();
            body.append('resource', root.dataset.resource);
            body.append('attackBehaviour', root.dataset.behaviour);
            Object.keys(added).forEach(function (k) { body.append('attributes[' + k + ']', points ? added[k] : 0); });
            fetch('ajax.php?cmd=heroSetAttributes', {method: 'POST', body: body, credentials: 'same-origin'})
                .then(function () { window.location.reload(); });
        }
        function format(key, points) {
            if (key === 'power') return (100 + points * fsPerPoint + itemFs).toLocaleString('en-US');
            if (key === 'offBonus' || key === 'defBonus') return (Math.round(points * 0.2 * 10) / 10).toFixed(1) + '%';
            return String(points);
        }
        function render() {
            var spent = 0;
            Object.keys(added).forEach(function (key) { spent += added[key]; });
            document.getElementById('heroV2FreePoints').textContent = free - spent;
            Object.keys(added).forEach(function (key) {
                var input = root.querySelector('input[name="' + key + '"]');
                if (!input) return;
                var used = parseInt(input.dataset.used, 10), total = used + added[key];
                input.value = total;
                var row = input.closest('.inputRatio').previousElementSibling;
                var secondary = row.querySelector('.filling.secondary');
                if (secondary) secondary.style.width = added[key] + '%';
                var plus = row.querySelector('button.plus'), minus = row.querySelector('button.minus');
                if (plus) plus.disabled = spent >= free || total >= 100;
                if (minus) minus.disabled = added[key] === 0;
                root.querySelector('[data-value="' + key + '"]').textContent = format(key, total);
                if (key === 'productionPoints') {
                    root.querySelectorAll('[data-production]').forEach(function (value) {
                        var type = parseInt(value.dataset.production, 10);
                        value.textContent = Math.round(total * (type === 0 ? rate.all : rate.each)).toLocaleString('en-US');
                    });
                }
            });
            saveButton.disabled = spent === 0;
            saveButton.classList.toggle('disabled', spent === 0);
        }
        function set(key, total) {
            var input = root.querySelector('input[name="' + key + '"]');
            var used = parseInt(input.dataset.used, 10), spentElsewhere = 0;
            Object.keys(added).forEach(function (k) { if (k !== key) spentElsewhere += added[k]; });
            total = Math.max(used, Math.min(100, used + free - spentElsewhere, total));
            added[key] = total - used;
            render();
        }
        root.addEventListener('click', function (event) {
            var step = event.target.closest('button[data-attribute]');
            if (step) {
                var key = step.dataset.attribute;
                set(key, parseInt(root.querySelector('input[name="' + key + '"]').value, 10) + parseInt(step.dataset.step, 10));
                return;
            }
            var pick = event.target.closest('button.pickProductionType');
            if (pick && !pick.classList.contains('active')) {
                root.dataset.resource = pick.dataset.resource;
                save(false);
            }
        });
        root.querySelectorAll('.points input').forEach(function (input) {
            input.addEventListener('change', function () { set(input.name, parseInt(input.value, 10) || 0); });
        });
        saveButton.addEventListener('click', function () { if (!saveButton.disabled) save(true); });
        var hide = root.querySelector('input[name="attackBehaviour"]');
        hide.addEventListener('change', function () {
            root.dataset.behaviour = hide.checked ? 'hide' : 'fight';
            save(false);
        });
        render();
    })();
</script>
