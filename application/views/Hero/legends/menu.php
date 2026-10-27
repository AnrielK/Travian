<?php
/*
 * Hero window tabs in the Travian Legends look (like hero/menus.php of the main branch): Inventory, Attributes,
 * Appearance. Adventures and auctions are their own windows (top bar buttons), so they get no hero tabs.
 * $hlTab: 'inventory' | 'attributes' | 'appearance'
 */
$hlTabs = [
    ['hero_inventory.php', LegendsUi::t('HEROL_TAB_INVENTORY', 'Inventory'), 'inventory', 1],
    ['hero_inventory.php?view=attributes', defined('herohero0') ? herohero0 : 'Attributes', 'attributes', 5],
    ['hero.php', defined('herohero1') ? herohero1 : 'Appearance', 'appearance', 2],
];
?>
<div class="contentNavi subNavi">
    <?php foreach ($hlTabs as [$hlHref, $hlLabel, $hlKey, $hlFavor]) { ?>
        <div class="container <?php echo $hlTab === $hlKey ? 'active' : 'normal'; ?>">
            <div class="content favor favorKey<?php echo $hlFavor; ?>"><a href="<?php echo $hlHref; ?>" class="tabItem"><?php echo $hlLabel; ?></a></div>
        </div>
    <?php } ?>
</div>
<script type="text/javascript">document.getElementById('content').className = 'heroV2 heroV2<?php echo ucfirst($hlTab); ?> heroView-<?php echo $hlTab; ?>';</script>
