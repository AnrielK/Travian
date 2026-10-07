<?php
/* Gold & Plus page (GoldShopCtrl) in the Travian Legends look. */
$h = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
?>
<div id="goldShopT46">
    <div class="goldBalance"><i class="goldCoin_medium"></i><?=number_format((int)$vars['gold']); ?></div>
    <div class="contentNavi subNavi">
        <div class="container <?=$vars['tab'] === 'gold' ? 'active' : 'normal'; ?>"><div class="content"><a class="tabItem" href="gold.php"><?=$h(T('PaymentWizard', 'Buy gold')); ?></a></div></div>
        <div class="container normal"><div class="content"><a class="tabItem" href="voucher.php"><?=$h(T('PaymentWizard', 'Vouchers')); ?></a></div></div>
        <div class="container <?=$vars['tab'] === 'pros' ? 'active' : 'normal'; ?>"><div class="content"><a class="tabItem" href="gold.php?t=pros"><?=$h(T('PaymentWizard', 'Advantages')); ?></a></div></div>
    </div>

    <?php if ($vars['tab'] === 'gold'): ?>
        <?php if ($vars['credited'] !== null): ?>
            <div class="shopNotice"><?=$h(sprintf(T('Tasks', 'goldCredited', '%d gold has been credited to your account.'), $vars['credited'])); ?></div>
        <?php endif; ?>
        <?php if ($vars['offline']): ?>
            <div class="shopNotice offline"><?=$h(T('Tasks', 'offlineGold', 'Offline world: no payment is set up, the packages are credited for free.')); ?></div>
        <?php endif; ?>
        <?php if (is_hero_v2()): ?>
            <?php /* Travian Legends 655 package cards (payment shop markup): bonus, original amount, ribbons, then "Next". */ ?>
            <div class="dialog paymentShopV5 ovGoldShop">
                <div id="paymentWizard" class="buyGold">
                    <div class="shopWrapper selectPackage">
                        <div class="purchaseStepWrapper">
                            <div class="packages">
                                <?php foreach ($vars['packages'] as $p): ?>
                                    <?php $bonus = $p['index'] === 0 ? 22 : 20; $original = (int)round($p['gold'] / (1 + $bonus / 100)); ?>
                                    <input type="radio" name="goldPackage" id="goldPackage<?=$p['index']; ?>" value="<?=$p['index']; ?>"<?=$p['bestSeller'] ? ' checked' : ''; ?>>
                                    <label class="package promo<?=$p['bestSeller'] || $p['bestValue'] ? ' bestPackage' : ''; ?>" for="goldPackage<?=$p['index']; ?>">
                                        <?php if ($p['bestSeller']): ?><div class="best bestSeller"><div class="text"><?=$h(T('Tasks', 'bestSeller', 'Best seller')); ?></div></div><?php endif; ?>
                                        <?php if ($p['bestValue']): ?><div class="best bestValue"><div class="text"><?=$h(T('Tasks', 'bestValue', 'Best value')); ?></div></div><?php endif; ?>
                                        <div class="discount">+<?=$bonus; ?>%</div>
                                        <div class="originalAmount"><?=\resources\View\LegendsUi::goldCoin('goldCoin'); ?><?=number_format($original); ?></div>
                                        <div class="productAmount"><?=\resources\View\LegendsUi::goldCoin('goldCoin'); ?><span><?=number_format($p['gold']); ?></span></div>
                                        <div class="packageImage size<?=$p['index']; ?>"><img src="<?=get_gpack_cdn_mainPage_url(); ?>img_ltr/legacy/views/shop/package3.png" alt=""></div>
                                        <div class="productPrice"><?=$p['price'] !== '' ? $h($p['price']) : $h(T('Tasks', 'free', 'Free')); ?></div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="paymentWrapper">
                            <div class="choosePayment"><?=$h(T('Tasks', 'choosePayment', 'Choose a payment method')); ?></div>
                            <?=\resources\View\LegendsUi::button($h(T('Tasks', 'next', 'Next')), ['class' => 'goldShopNext',
                                'onclick' => $vars['offline']
                                    ? "var p=document.querySelector('input[name=goldPackage]:checked'); if(p){window.location.href='gold.php?package='+p.value+'&c=" . rawurlencode($vars['checker']) . "'; this.disabled=true;}"
                                    : "jQuery(window).trigger('startPaymentWizard', {}); return false;"]); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
        <div class="packages">
                <?php foreach ($vars['packages'] as $p): ?>
                    <a class="package<?=$p['bestSeller'] || $p['bestValue'] ? ' bestPackage' : ''; ?>"
                       href="<?=$vars['offline'] ? $h('gold.php?package=' . $p['index'] . '&c=' . rawurlencode($vars['checker'])) : '#'; ?>"
                        <?=$vars['offline'] ? '' : 'onclick="jQuery(window).trigger(\'startPaymentWizard\', {}); return false;"'; ?>>
                        <?php if ($p['bestSeller']): ?><div class="ribbon bestSeller"><?=$h(T('Tasks', 'bestSeller', 'Best seller')); ?></div><?php endif; ?>
                        <?php if ($p['bestValue']): ?><div class="ribbon bestValue"><?=$h(T('Tasks', 'bestValue', 'Best value')); ?></div><?php endif; ?>
                        <div class="amount"><i class="goldCoin_medium"></i><?=number_format($p['gold']); ?></div>
                        <div class="packageImage size<?=$p['index']; ?>"></div>
                        <div class="price"><?=$p['price'] !== '' ? $h($p['price']) : $h(T('Tasks', 'free', 'Free')); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="features">
            <?php foreach ($vars['features'] as $f): ?>
                <div class="feature <?=$h($f['class']); ?><?=$f['active'] ? ' active' : ''; ?>">
                    <div class="featureImage"></div>
                    <div class="featureTitle"><?=$h($f['title']); ?></div>
                    <div class="featureButton"><?=$f['button']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
