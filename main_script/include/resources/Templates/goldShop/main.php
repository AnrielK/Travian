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
