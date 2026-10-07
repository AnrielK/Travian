<?php
/*
 * Hero attributes in the Travian Legends 655 look (heroV2Attributes): health / experience / speed, hero
 * production, attribute points with +/- buttons, equipment benefits and the home village with the hide switch.
 * Changes go through the heroSetAttributes ajax command.
 */

use resources\View\LegendsUi as UI;

$e = [UI::class, 'e'];
$h = $vars['hero'];
$free = (int)$h['points'];
$productionIcons = [0 => 'resources_medium', 1 => 'lumber_small', 2 => 'clay_small', 3 => 'iron_small', 4 => 'crop_small'];
$productionTiny = [0 => 'resources_small', 1 => 'lumber_tiny', 2 => 'clay_tiny', 3 => 'iron_tiny', 4 => 'crop_tiny'];
$production = [0 => $h['allResourcesProduct']] + array_fill(1, 4, $h['eachResourceProduct']);
$attributeIcons = ['power' => 'attributeStrength_medium', 'offBonus' => 'attributeOffBonus_medium',
    'defBonus' => 'attributeDefBonus_medium', 'productionPoints' => 'attributeRessourceBonus_medium'];
$attributes = [
    'power' => ['name' => T('HeroInventory', 'fightingStrength'), 'used' => (int)$h['power'], 'title' => $vars['HeroFightingStrengthTitle'],
        'value' => number_format_x($h['totalPower'])],
    'offBonus' => ['name' => T('HeroInventory', 'offBonus'), 'used' => (int)$h['offBonus'], 'title' => $vars['HeroOffBonusTitle'],
        'value' => number_format(round($h['offBonus'] * 0.2, 1), 1) . '%'],
    'defBonus' => ['name' => T('HeroInventory', 'defBonus'), 'used' => (int)$h['defBonus'], 'title' => $vars['DefBonusTitle'],
        'value' => number_format(round($h['defBonus'] * 0.2, 1), 1) . '%'],
    'productionPoints' => ['name' => T('HeroInventory', 'productBonus'), 'used' => (int)$h['production'], 'title' => $vars['heroProductBonusTitle'],
        'value' => (int)$h['production']],
];
$arrowDouble = '<svg class="arrowDouble" viewBox="0 0 10 7.75"><path d="M0 0h3l3 3.88-3 3.87H0l3-3.87"/><path d="M4 0h3l3 3.88-3 3.87H4l3-3.87"/></svg>';
$health = (int)ceil($h['regenerating'] ? $h['regeneratedHealth'] : $h['health']);
?>
<div class="contentV2" id="heroV2Attributes"
     data-resource="<?=(int)$h['productionType']; ?>" data-behaviour="<?=$h['hide'] ? 'hide' : 'fight'; ?>"
     data-free="<?=$free; ?>" data-fs-per-point="<?=(int)$h['fsperpoint']; ?>" data-item-fs="<?=(int)$h['itemfs']; ?>"
     data-production-rate="<?=$e(json_encode(['all' => $h['allPerPoint'], 'each' => $h['eachPerPoint']])); ?>">
    <?php if ($h['status'] == '101'): ?>
        <div class="attributeBox heroDead">
            <div class="status"><i class="statusDead_medium"></i>
                <div class="title"><?=$h['heroStatusMessage']; ?></div>
            </div>
            <div class="description"><?=$vars['regenerate']['heroReviveDesc']; ?></div>
            <div class="title"><?=T('HeroInventory', 'ResourcesRequiredToReviveHero'); ?></div>
            <form method="post" action="hero.php?t=1&amp;view=attributes" class="reviveWrapper">
                <input type="hidden" name="a" value="1">
                <div class="reviveWithResources charges">
                    <div class="inlineIconList resourceWrapper">
                        <?php foreach ($vars['regenerate']['costs'] as $i => $cost): ?>
                            <div class="inlineIcon resource"><i class="r<?=$i + 1; ?>"></i><span class="value"><?=number_format_x($cost); ?></span></div>
                        <?php endforeach; ?>
                    </div>
                    <div><div class="inlineIcon duration"><i class="clock_medium"></i><span class="value"><?=secondsToString($vars['regenerate']['neededTime'], true); ?></span></div></div>
                </div>
                <?php if ($vars['regenerate']['showButton']): ?>
                    <?=UI::button(T('HeroInventory', 'Regenerate'), ['type' => 'submit', 'name' => 'heroRegeneration', 'class' => 'reviveWithResources']); ?>
                <?php else: ?>
                    <div class="reviveWithResources chargesErrorMessage"><?=$vars['regenerate']['npc']; ?> <?=$vars['regenerate']['none']; ?></div>
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>

    <div class="<?=$h['regenerating'] ? 'heroRevive ' : ''; ?>attributeBox">
        <?php if ($h['regenerating']): ?>
            <div class="status"><i class="statusRevive_medium"></i>
                <div class="title"><?=$h['heroStatusMessage']; ?></div>
            </div>
        <?php endif; ?>
        <div class="stats">
            <div class="name" title="<?=$e($vars['HealthTitle']); ?>"><i class="attributeHealth_medium"></i><span><?=T('HeroInventory', 'health'); ?></span></div>
            <?=UI::progressBar(['color' => $h['regenerating'] ? 'orange' : 'green', 'primary' => $health, 'title' => $vars['HealthTitle']]); ?>
            <div class="value" title="<?=$e($vars['HealthTitle']); ?>"><?=$health; ?>%</div>
            <div class="name" title="<?=$e($vars['ExperienceTitle']); ?>"><i class="attributeExperience_medium"></i><span><?=T('HeroInventory', 'experience'); ?></span></div>
            <?=UI::progressBar(['color' => 'blue', 'primary' => $h['reachedExperience'], 'title' => $vars['ExperienceTitle']]); ?>
            <div class="value" title="<?=$e($vars['ExperienceTitle']); ?>"><?=number_format_x($h['exp']); ?></div>
            <div class="name" title="<?=$e($vars['SpeedTitle']); ?>"><i class="attributeSpeed_medium"></i><span><?=T('HeroInventory', 'speed'); ?></span></div>
            <div class="value speedValue" title="<?=$e($vars['SpeedTitle']); ?>"><?=$arrowDouble; ?><span><strong><?=$h['speed']; ?></strong> <?=T('HeroV2', 'fieldsPerHour', 'fields per hour'); ?></span></div>
        </div>
    </div>

    <div class="attributeBox">
        <div class="heroProduction">
            <div class="productionTotal" title="<?=$e($vars['production_tooltip_title']); ?>">
                <strong><?=T('HeroV2', 'heroProduction', 'Hero production'); ?></strong>
                <div class="productionItem"><span class="value">+ <?=(int)$h['baseCrop']; ?></span><i class="crop_tiny"></i></div>
                <div class="productionItem"><span class="value">+ <?=number_format_x($production[(int)$h['productionType']]); ?></span><i class="<?=$productionTiny[(int)$h['productionType']]; ?>"></i></div>
            </div>
            <div class="changeProduction">
                <?php foreach ($productionIcons as $type => $icon): ?>
                    <div class="resource <?=$icon; ?>">
                        <?=UI::button('<i class="' . $icon . '"></i>', ['icon' => true, 'toggle' => true, 'activeNotClickable' => true,
                            'active' => (int)$h['productionType'] === $type, 'class' => 'pickProductionType', 'data' => ['resource' => $type]]); ?>
                        <span class="value" data-production="<?=$type; ?>"><?=number_format_x($production[$type]); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="attributeBox">
        <div class="heroAttributes">
            <div class="attributes formV2<?=$free > 0 ? ' withSliders levelUp' : ''; ?>">
                <div class="title"><?=T('HeroV2', 'attributes', T('HeroGlobal', 'Attributes')); ?></div>
                <div class="points_descr"><?=T('HeroV2', 'pointsAvailable', T('HeroInventory', 'availablePoints')); ?></div>
                <div class="pointsAvailable" id="heroV2FreePoints"><?=$free; ?></div>
                <?php foreach ($attributes as $key => $attribute): ?>
                    <div class="name" title="<?=$e($attribute['title']); ?>"><i class="<?=$attributeIcons[$key]; ?>"></i><span><?=$e($attribute['name']); ?></span></div>
                    <?=UI::progressBar(['color' => 'blue', 'primary' => $attribute['used'], 'secondary' => 0, 'title' => $attribute['title'],
                        'buttons' => $free > 0 && $attribute['used'] < 100 ? $key : null, 'plusDisabled' => $free === 0, 'minusDisabled' => true]); ?>
                    <div class="inputRatio pointsRatio isLtr">
                        <span class="nominator"><label class="points"><input type="text" inputmode="numeric" name="<?=$key; ?>" autocomplete="off"
                                  value="<?=$attribute['used']; ?>" data-used="<?=$attribute['used']; ?>"<?=$free === 0 || $attribute['used'] >= 100 ? ' disabled' : ''; ?>></label></span>
                        <span class="separator">/</span>
                        <span class="denominator"><div class="value" title="<?=$e($attribute['title']); ?>" data-value="<?=$key; ?>"><?=$attribute['value']; ?></div></span>
                    </div>
                <?php endforeach; ?>
                <?=UI::button(T('HeroInventory', 'save'), ['id' => 'savePoints', 'disabled' => true]); ?>
            </div>
        </div>
    </div>

    <?php if ($vars['equipmentBenefits']): ?>
        <div class="attributeBox">
            <div class="heroEquipmentBenefits">
                <strong><?=T('HeroV2', 'equipmentBenefits', 'Benefits of hero equipment'); ?></strong>
                <ul>
                    <?php foreach ($vars['equipmentBenefits'] as $benefit): ?>
                        <li><span class="listIcon"></span><span class="benefit"><?=$benefit; ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="attributeBox">
        <div class="heroState"><i class="<?=$h['status'] == 100 ? 'statusHome_medium' : 'statusRunning_medium'; ?>"></i>
            <div><?=$h['heroStatusMessage']; ?></div>
        </div>
        <?=UI::toggleSwitch('attackBehaviour', (bool)$h['hide'], T('HeroInventory', 'HeroHideDesc'), 'heroHideSwitch'); ?>
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

        function save(data) {
            var payload = Object.assign({cmd: 'heroSetAttributes', resource: root.dataset.resource, attackBehaviour: root.dataset.behaviour}, data);
            Travian.ajax({data: payload, onSuccess: function () { window.location.reload(); }});
        }

        function format(key, points) {
            if (key === 'power') return (100 + points * fsPerPoint + itemFs).toLocaleString();
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
                        value.textContent = Math.round(total * (type === 0 ? rate.all : rate.each)).toLocaleString();
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
                save({});
            }
        });
        root.querySelectorAll('.points input').forEach(function (input) {
            input.addEventListener('change', function () { set(input.name, parseInt(input.value, 10) || 0); });
            input.addEventListener('wheel', function (event) {
                if (input.disabled) return;
                event.preventDefault();
                set(input.name, (parseInt(input.value, 10) || 0) + (event.deltaY > 0 ? -1 : 1));
            }, {passive: false});
        });
        saveButton.addEventListener('click', function () {
            if (!saveButton.disabled) save({attributes: added});
        });
        var hide = root.querySelector('input[name="attackBehaviour"]');
        hide.addEventListener('change', function () {
            root.dataset.behaviour = hide.checked ? 'hide' : 'fight';
            save({});
        });
        render();
    })();
</script>
