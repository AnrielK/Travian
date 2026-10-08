<?php
/* "Advantages" tab of the gold shop in the Travian Legends look (included from goldShop/main.php, graphic packs with heroV2). */

use resources\View\LegendsUi as UI;

$h = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
$adv = $vars['advantages'];
$canSpend = $vars['canSpendGold'];
$duration = static function (int $seconds): string {
    if ($seconds >= 86400 && $seconds % 86400 === 0) {
        $days = intdiv($seconds, 86400);
        return sprintf($days === 1 ? T('Tasks', 'oneDay', '%d day') : T('Tasks', 'nDays', '%d days'), $days);
    }
    $hours = max(1, (int)round($seconds / 3600));
    return sprintf($hours === 1 ? T('Tasks', 'oneHour', '%d hour') : T('Tasks', 'nHours', '%d hours'), $hours);
};
$book = static fn(string $key) => "new Travian.Game.WayOfPayment('" . $key . "', 'goldShop'); return false;";
$toggle = static fn(string $key) => "Travian.ajax({data: {cmd: 'premiumFeature', featureKey: '" . $key . "', toggleAutoprolong: 1, context: 'goldShop'},"
    . " onSuccess: function () { window.location.reload(); }}); this.disabled = true;";
$info = static fn(string $text) => '<span class="advInfo" title="' . $h(strip_tags(str_replace('</p>', ' ', $text))) . '">'
    . '<svg viewBox="0 0 20 20"><circle cx="10" cy="10" r="8.5"/><path d="M10 8.6v5.6M10 5.6v.3"/></svg></span>';
$remaining = static fn(int $till) => '<span class="advCountdown" data-end="' . $till . '"></span>';
$button = static function (string $label, array $f) use ($book, $canSpend): string {
    return UI::button($label, ['gold' => $f['gold'], 'class' => 'advButton', 'disabled' => !$canSpend, 'onclick' => $book($f['key'])]);
};
$boostTitles = [
    'Wood' => T('Tasks', 'increaseProductionWood', 'Increase lumber production'),
    'Clay' => T('Tasks', 'increaseProductionClay', 'Increase clay production'),
    'Iron' => T('Tasks', 'increaseProductionIron', 'Increase iron production'),
    'Crop' => T('Tasks', 'increaseProductionCrop', 'Increase crop production'),
];
$boostImages = ['Wood' => 'lumber', 'Clay' => 'clay', 'Iron' => 'iron', 'Crop' => 'crop'];
$boostDesc = ['Wood' => '+25WoodDesc', 'Clay' => '+25ClayDesc', 'Iron' => '+25IronDesc', 'Crop' => '+25CropDesc'];
?>
<div class="ovAdvantages">
    <div class="advRow advPremium">
        <?php $f = $adv['goldclub']; ?>
        <div class="advCard advWide goldclub<?=$f['active'] ? ' active' : ''; ?>">
            <?=$info(T('PaymentWizard', 'goldClubDesc')); ?>
            <div class="advImage"></div>
            <div class="advText">
                <h3><?=$h(T('PaymentWizard', 'GoldClub')); ?></h3>
                <p><?=$h(T('Tasks', 'goldClubShort', 'More efficiency through automated merchants, farm lists and further advantages.')); ?></p>
            </div>
            <div class="advFooter">
                <div class="advStatus"><?=$h(T('PaymentWizard', 'Bonus duration')); ?>: <b><?=$h(T('PaymentWizard', 'ToTheEndOfTheGame')); ?></b></div>
                <?php if ($f['active']): ?>
                    <div class="advActive"><?=UI::svg('check', 'check'); ?><?=$h(T('Tasks', 'activated', 'Activated')); ?></div>
                <?php else: ?>
                    <?=$button(T('inGame', 'plus.activate'), $f); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php $f = $adv['plus']; ?>
        <div class="advCard advWide plus<?=$f['active'] ? ' active' : ''; ?>">
            <?=$info(T('PaymentWizard', 'PlusDesc')); ?>
            <div class="advImage"></div>
            <div class="advText">
                <h3><?=$h(T('Tasks', 'travianPlus', 'Travian Plus')); ?></h3>
                <p><?=$h(T('Tasks', 'plusShort', 'Optimise your empire with a larger building queue, attack warnings and further options.')); ?></p>
            </div>
            <div class="advFooter">
                <div class="advStatus">
                    <?php if ($f['active']): ?>
                        <label class="advAutoExtend"><input type="checkbox"<?=$f['autoExtend'] ? ' checked' : ''; ?><?=$canSpend ? '' : ' disabled'; ?>
                                onchange="<?=$h($toggle($f['key'])); ?>"> <?=$h(T('PaymentWizard', 'Extend automatically')); ?></label>
                        <div><?=$h(T('Tasks', 'stillActive', 'Still active for:')); ?> <b><?=$remaining($f['till']); ?></b></div>
                    <?php else: ?>
                        <?=$h(T('PaymentWizard', 'Bonus duration')); ?>: <b><?=$h($duration($f['duration'])); ?></b>
                    <?php endif; ?>
                </div>
                <?=$button(T('inGame', $f['active'] ? 'plus.extend' : 'plus.activate'), $f); ?>
            </div>
        </div>
    </div>
    <div class="advRow advBoosts">
        <?php foreach ($adv['boosts'] as $f): ?>
            <div class="advCard advBoost <?=$h(strtolower($f['name'])); ?><?=$f['active'] ? ' active' : ''; ?>">
                <?=$info(T('PaymentWizard', $boostDesc[$f['name']])); ?>
                <div class="advImage <?=$boostImages[$f['name']]; ?>"></div>
                <h4><?=$h($boostTitles[$f['name']]); ?></h4>
                <div class="advBonus"><svg class="advArrow" viewBox="0 0 16 18"><path d="M8 1 15 9h-4v8H5V9H1z"/></svg>
                    <b>+25%</b> <?=$h(sprintf(T('Tasks', 'forDuration', 'for %s'), $duration($f['duration']))); ?></div>
                <?=$button(T('inGame', $f['active'] ? 'plus.extend' : 'plus.activate'), $f); ?>
                <?php if ($f['active']): ?>
                    <div class="advStatus"><?=$h(T('Tasks', 'stillActive', 'Still active for:')); ?> <b><?=$remaining($f['till']); ?></b></div>
                    <label class="advAutoExtend"><input type="checkbox"<?=$f['autoExtend'] ? ' checked' : ''; ?><?=$canSpend ? '' : ' disabled'; ?>
                            onchange="<?=$h($toggle($f['key'])); ?>"> <?=$h(T('PaymentWizard', 'Extend automatically')); ?></label>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script type="text/javascript">
    (function () {
        // Remaining time of active advantages: "6d 23:52:22", reloads the page when one runs out.
        var items = document.querySelectorAll('.ovAdvantages .advCountdown');
        var pad = function (n) { return (n < 10 ? '0' : '') + n; };
        var tick = function () {
            var now = Math.floor(Date.now() / 1000), expired = false;
            for (var i = 0; i < items.length; i++) {
                var left = Math.max(0, parseInt(items[i].getAttribute('data-end'), 10) - now);
                expired = expired || left === 0;
                var d = Math.floor(left / 86400), h = Math.floor(left % 86400 / 3600), m = Math.floor(left % 3600 / 60), s = left % 60;
                items[i].textContent = (d > 0 ? d + 'd ' : '') + pad(h) + ':' + pad(m) + ':' + pad(s);
            }
            if (expired) { window.location.reload(); return; }
            setTimeout(tick, 1000);
        };
        if (items.length) { tick(); }
    })();
</script>
