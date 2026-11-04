<?php
/*
 * Gold shop of the Travian Legends look as the payment wizard dialog of Legends 692.2 (gold.php?dialog=1, opened by
 * js/Game/Legends/goldShop.js): the markup of #paymentWizard, styled by legends_ltr.css. "Buy gold": the purchase
 * steps, the six packages in a row and "Next"; "Advantages": Gold Club and Travian PLUS, the production bonuses below.
 */

use resources\View\LegendsUi as UI;

$h = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
$number = static fn(int $n) => number_format($n, 0, ',', number_thousands_separator());
$shield = '<svg viewBox="0 0 19.58 20"><path class="shield" d="M19.58 3.79S16.73 3 13.77 1.85C12.37 1.32 11 0 9.79 0s-2.7 1.35-4.15 1.91C2.74 3 0 3.79 0 3.79S-.38 15.42 9.79 20C20 15.42 19.58 3.79 19.58 3.79z"/>'
    . '<path class="check" d="M15.37 6.18a.26.26 0 00-.25-.13 1.08 1.08 0 00-.58.22 5.55 5.55 0 00-.78.65l-3.45 3.42a6.57 6.57 0 01-.53.48.73.73 0 01-.3.18c-.13 0-1.74-2.51-2.1-2.51a1.42 1.42 0 00-1 .48 1.45 1.45 0 00-.46 1 13.56 13.56 0 001.25 3.06 1.57 1.57 0 00.63.63 1.66 1.66 0 00.81.31 2.33 2.33 0 001.06-.62 20.4 20.4 0 001.82-1.73l3.35-3.54.06-.06a1.76 1.76 0 00.58-1.23v-.15a1 1 0 00-.11-.46z"/></svg>';
$arrow = '<svg class="advantageBonusArrow premiumFeature" viewBox="0 0 160 180"><path class="border" d="M158.78 106.45 85.17 2.72C84.06 1.01 82.14 0 80.03 0S76 1.01 74.79 2.72L1.17 106.45c-1.41 1.91-1.51 4.53-.5 6.64 1.11 2.11 3.23 3.42 5.65 3.42h31.76v57.15c0 3.52 2.82 6.34 6.35 6.34h70.89c3.53 0 6.35-2.82 6.35-6.34v-57.15h31.97c2.42 0 4.54-1.31 5.65-3.42 1.11-2.11.91-4.63-.5-6.64Z"/>'
    . '<path class="inner" d="M108.45 167.41h-57v-63.89H20.52l59.44-85.03 59.02 85.1-30.53-.05v63.87z"/></svg>';
$help = '<svg viewBox="0 0 20 20"><path d="M9 15h2V9H9v6zm1-8c.3 0 .5-.1.7-.3s.3-.4.3-.7-.1-.5-.3-.7c-.2-.2-.4-.3-.7-.3s-.5.1-.7.3c-.2.2-.3.4-.3.7s.1.5.3.7.4.3.7.3zm0 13C4.5 20 0 15.5 0 10S4.5 0 10 0s10 4.5 10 10-4.5 10-10 10zm0-2c4.4 0 8-3.6 8-8s-3.6-8-8-8-8 3.6-8 8 3.6 8 8 8z"/></svg>';
$checkbox = '<svg viewBox="0 0 18 18" class="checkbox" aria-hidden="true" focusable="false"><path class="box" d="M16 0H2a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V2a2 2 0 00-2-2z"/>'
    . '<path class="checkmark" d="M15.28 4.28l-8.27 8.27-4.3-4.26h0"/></svg>';
$videoArrow = str_replace('premiumFeature', 'videoFeature', $arrow);
$videoIcon = '<svg viewBox="0 0 190 220.5" class="videoIcon"><rect width="173.7" height="118.6" x="14.3" y="99.7" fill="#3969c4" stroke="#000" stroke-width="4" rx="11.5"/>'
    . '<rect width="152.3" height="96.3" x="25" y="111" fill="#72a7f2" stroke="#1b1464" stroke-width="4" rx="5.8"/>'
    . '<path fill="#fff" stroke="#000" stroke-width="7" d="M79.3 184.1v-52.5c0-6 5.2-10 9.8-7.5l48.9 26.3c5.4 2.9 5.4 12.1 0 15l-48.9 26.3c-4.6 2.5-9.8-1.5-9.8-7.6Z"/>'
    . '<path fill="#efefef" stroke="#000" stroke-width="3" d="M5.6 74.4 149.3 2.2c3.9-1.9 8.9.3 11.1 4.7l11.2 22.2c2.2 4.4.8 9.5-3.1 11.4L24.7 112.4c-3.9 1.9-8.9-.3-11.1-4.7L2.5 85.8c-2.2-4.4-.8-9.5 3.1-11.4Z"/></svg>';
// Content of the help tooltip of a card (shown by js/Game/Legends/goldShop.js on hover), as the Legends advantages tooltips.
$tooltip = static function (string $cls, string $title, string $intro, array $rows) use ($h): string {
    $html = '<template class="bonusTooltip"><div class="tooltipHelpContainer advantage ' . $cls . '"><h3>' . $h($title) . '</h3>' . $intro;
    foreach ($rows as $row) {
        $html .= '<div class="helpText"><span class="bonusIcon ' . ($row['icon'] ?? '') . '">' . ($row['svg'] ?? '') . '</span>'
            . '<strong class="title">' . $row['title'] . '</strong><span class="desc">' . $row['desc'] . '</span></div>';
    }
    return $html . '</div></template>';
};
$features = static function (string $base, array $map) use ($h): array {
    $rows = [];
    foreach ($map as $key => $icon) {
        $rows[] = ['icon' => $icon, 'title' => $h(T($base, 'features.' . $key . '.title')), 'desc' => $h(T($base, 'features.' . $key . '.text'))];
    }
    return $rows;
};
$tab = $vars['tab'];
$steps = ['selectPackage' => T('Tasks', 'stepSelectPackage', 'Select package'), 'selectPaymentMethod' => T('Tasks', 'stepPaymentMethod', 'Payment method'),
    'insertBillingInformation' => T('Tasks', 'stepBilling', 'Billing information'), 'confirmed' => T('Tasks', 'stepConfirmed', 'Confirmed')];
?>
<div id="paymentWizard" class="<?=$tab === 'pros' ? 'advantages' : 'buyGold'; ?>">
    <div class="accountBalance"><div class="inlineIcon"><?=UI::goldCoin('goldCoin'); ?><span class="value"><?=$number((int)$vars['gold']); ?></span></div></div>
    <div class="header"><div>
        <div class="contentNavi subNavi tabFavorWrapper"><div class="scrollingContainer">
            <div class="content favor"><a class="tabItem<?=$tab === 'gold' ? ' active' : ''; ?>" href="gold.php" data-shop-tab="gold"><?=$h(T('PaymentWizard', 'Buy gold')); ?></a></div>
            <div class="content favor"><a class="tabItem<?=$tab === 'pros' ? ' active' : ''; ?>" href="gold.php?t=pros" data-shop-tab="pros"><?=$h(T('PaymentWizard', 'Advantages')); ?></a></div>
        </div></div>
        <div class="navigationSpacer"></div>
    </div></div>
    <div id="paymentWizardContent" class="contentWrapper">
        <?php if ($tab === 'gold'): ?>
            <div class="contentV2 shopWrapper selectPackage">
                <h1 class="titleInHeader"><?=$h(T('Tasks', 'goldShopTitle', 'Gold & Plus')); ?></h1>
                <div class="buyProgress">
                    <?php $first = true; foreach ($steps as $key => $label): ?>
                        <?php if (!$first): ?><div class="divider"></div><?php endif; ?>
                        <div class="inlineIcon step <?=$key; ?> <?=$first || $vars['credited'] !== null ? 'done' : 'locked'; ?>"><?=$shield; ?><span class="value"><?=$h($label); ?></span></div>
                    <?php $first = false; endforeach; ?>
                </div>
                <div class="purchaseStepWrapper">
                    <div class="packages">
                        <?php foreach ($vars['packages'] as $p): ?>
                            <input id="package<?=$p['index'] + 1; ?>" type="radio" value="<?=$p['index']; ?>" name="package"<?=$p['bestSeller'] ? ' checked' : ''; ?>>
                            <label for="package<?=$p['index'] + 1; ?>" class="package<?=$p['bestSeller'] || $p['bestValue'] ? ' bestPackage' : ''; ?>">
                                <?php if ($p['bestSeller']): ?><div class="best bestSeller"><div class="text"><?=$h(T('Tasks', 'bestSeller', 'Best seller')); ?></div></div><?php endif; ?>
                                <?php if ($p['bestValue']): ?><div class="best bestValue"><div class="text"><?=$h(T('Tasks', 'bestValue', 'Best value')); ?></div></div><?php endif; ?>
                                <div class="inlineIcon productAmount"><?=UI::goldCoin('goldCoin'); ?><span class="value"><?=$number($p['gold']); ?></span></div>
                                <div class="packageImage"><img alt="" src="<?=get_gpack_cdn_mainPage_url(); ?>img_ltr/legacy/views/shop/package<?=min(6, $p['index'] + 1); ?>.png"></div>
                                <div class="productPrice"><?=$p['price'] !== '' ? $h($p['price']) : $h(T('Tasks', 'free', 'Free')); ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="paymentWrapper">
                    <div class="paymentMethods"></div>
                    <?php if ($vars['credited'] !== null): ?>
                        <span class="choosePayment credited"><?=$h(sprintf(T('Tasks', 'goldCredited', '%d gold has been credited to your account.'), $vars['credited'])); ?></span>
                    <?php else: ?>
                        <span class="choosePayment"><?=$h($vars['offline'] ? T('Tasks', 'offlineGold', 'Offline world: no payment is set up, the packages are credited for free.')
                            : T('Tasks', 'choosePayment', 'Choose a payment method')); ?></span>
                    <?php endif; ?>
                    <?=UI::button($h(T('Tasks', 'next', 'Next')), ['class' => 'buyButton', 'id' => 'continueBuy', 'type' => 'button',
                        'data' => ['offline' => $vars['offline'] ? '1' : '0', 'checker' => $vars['checker']]]); ?>
                </div>
            </div>
        <?php else: ?>
            <?php $adv = $vars['advantages']; $canSpend = $vars['canSpendGold']; ?>
            <?php foreach (['goldClub' => $adv['goldclub'], 'travianPlus' => $adv['plus']] as $cls => $f): ?>
                <div class="advantagesBonusBox <?=$cls; ?><?=$f['active'] ? ' active' : ''; ?>">
                    <div class="titleDecoration"></div>
                    <div class="bonusIcon"></div>
                    <div class="bonusTitle">
                        <h3><?=$h($cls === 'goldClub' ? T('PaymentWizard', 'GoldClub') : T('Tasks', 'travianPlus', 'Travian Plus')); ?></h3>
                        <div><?=$h($cls === 'goldClub' ? T('Tasks', 'goldClubShort', 'More efficiency through automated merchants, farm lists and further advantages.')
                            : T('Tasks', 'plusShort', 'Optimise your empire with a larger building queue, attack warnings and further options.')); ?></div>
                    </div>
                    <div class="bonusButton">
                        <?php if ($cls === 'goldClub' && $f['active']): ?>
                            <div class="advActive"><?=UI::svg('check', 'check'); ?><?=$h(T('Tasks', 'activated', 'Activated')); ?></div>
                        <?php else: ?>
                            <?=UI::button($h(T('inGame', $f['active'] ? 'plus.extend' : 'plus.activate')), ['gold' => $f['gold'], 'class' => 'prosButton ' . $cls,
                                'type' => 'button', 'disabled' => !$canSpend, 'data' => ['feature' => $f['key']]]); ?>
                        <?php endif; ?>
                    </div>
                    <div class="bonusDuration">
                        <?php if ($cls === 'travianPlus' && $f['active']): ?>
                            <div class="formV2 autoProlong"><label class="checkbox"><input type="checkbox" name="autoProlong" data-feature="<?=$h($f['key']); ?>"
                                    <?=$f['autoExtend'] ? ' checked' : ''; ?><?=$canSpend ? '' : ' disabled'; ?>><?=$checkbox; ?><div class="label"><?=$h(T('PaymentWizard', 'Extend automatically')); ?></div></label></div>
                            <span class="timerLabel"><?=$h(T('Tasks', 'stillActive', 'Still active for:')); ?></span>
                            <?=appendTimer($f['till'] - time()); ?>
                        <?php else: ?>
                            <span><?=$h(T('PaymentWizard', 'Bonus duration')); ?>: <strong><?=$h($cls === 'goldClub' ? T('PaymentWizard', 'ToTheEndOfTheGame') : UI::durationText($f['duration'])); ?></strong></span>
                        <?php endif; ?>
                    </div>
                    <div class="tooltipHelpIcon bonusExplanation"><?=$help; ?><?=$cls === 'goldClub'
                        ? $tooltip('goldclub', T('PaymentWizard', 'GoldClub'), '', $features('LegendsGoldClub', ['troopEscape' => 'capitalEvasion',
                            'raidList' => 'farmLists', 'tradeThreeTimes' => 'merchantsRun3', 'tradeRoute' => 'automaticTrade', 'cropFinder' => 'cropFinder',
                            'messageArchive' => 'messageArchive']))
                        : $tooltip('plus', T('Tasks', 'travianPlus', 'Travian Plus'), '', $features('LegendsPlus', ['attackWarning' => 'attackWarning',
                            'buildingQueue' => 'buildingQueue', 'linkList' => 'linkList', 'villageStatistics' => 'villageOverview', 'fullScreen' => 'largerMap',
                            'directLinks' => 'directLinks', 'tradeMulti' => 'merchantsRunTwice'])); ?></div>
                </div>
            <?php endforeach; ?>
            <?php foreach ($adv['boosts'] as $f): ?>
                <?php $res = ['Wood' => 'lumber', 'Clay' => 'clay', 'Iron' => 'iron', 'Crop' => 'crop'][$f['name']]; ?>
                <div class="advantagesBonusBox <?=$res; ?>ProductionBonus<?=$f['active'] ? ' active' : ''; ?>">
                    <div class="titleDecoration"></div>
                    <div class="bonusIcon"></div>
                    <div class="bonusTitle"><h3><?=$h(T('Tasks', 'increaseProduction' . $f['name'], 'Increase production')); ?></h3></div>
                    <div class="bonusInfo bonusText"><?=$arrow; ?><span><strong>+25%</strong> <?=sprintf($h(T('Tasks', 'forDuration', 'for %s')), '<strong>' . $h(UI::durationText($f['duration'], true)) . '</strong>'); ?></span></div>
                    <div class="bonusButton">
                        <?=UI::button($h(T('inGame', $f['active'] ? 'plus.extend' : 'plus.activate')), ['gold' => $f['gold'], 'class' => 'prosButton ' . $res . 'ProductionBonus',
                            'type' => 'button', 'disabled' => !$canSpend, 'data' => ['feature' => $f['key']]]); ?>
                    </div>
                    <?php if ($f['active']): ?>
                        <div class="bonusDuration"><span class="timerLabel"><?=$h(T('Tasks', 'stillActive', 'Still active for:')); ?></span> <?=appendTimer($f['till'] - time()); ?></div>
                    <?php endif; ?>
                    <?php /* Legends also offers +15% for 8 hours for watching a video; OpenVillage shows no ads, so the button is disabled. */ ?>
                    <?php $for = static fn(int $seconds) => sprintf($h(T('Tasks', 'forDuration', 'for %s')), '<strong>' . $h(UI::durationText($seconds, true)) . '</strong>'); ?>
                    <div class="bonusVideo">
                        <span class="separator"><?=$h(T('Tasks', 'or', 'or')); ?></span>
                        <span class="bonusText"><?=$videoArrow; ?><span><strong>+15%</strong> <?=$for(8 * 3600); ?></span></span>
                        <button class="textButtonV2 buttonFramed withTextAndIcon rectangle withText purple disabled" type="button" disabled
                                title="<?=$h(T('Tasks', 'videoUnavailable', 'No videos are available.')); ?>"><div><span><?=$h(T('inGame', 'plus.activate')); ?></span><?=$videoIcon; ?></div></button>
                    </div>
                    <div class="tooltipHelpIcon bonusExplanation"><?=$help; ?><?=$tooltip('productionBoost ' . $res . 'ProductionBonus', T('Tasks', 'increaseProduction' . $f['name'], 'Increase production'),
                        '<span>' . $h(T('Tasks', 'boostIntro', 'Applies to all your villages, on the base production and all other bonuses.')) . '</span>', [
                            ['svg' => $arrow, 'title' => '+25% ' . $for($f['duration']), 'desc' => $h(T('Tasks', 'boostGoldDesc', 'Activate it for gold; with automatic extension the bonus lasts.'))],
                            ['svg' => $videoArrow, 'title' => '+15% ' . $for(8 * 3600), 'desc' => $h(T('Tasks', 'boostVideoDesc', 'Or watch a short video for a smaller bonus, once a day per resource.'))
                                . '<br><strong>' . $h(T('Tasks', 'videoUnavailable', 'No videos are available.')) . '</strong>'],
                        ]); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
