<?php
/* Stock bar of the Travian Legends layout (included by LayoutT46.php; same data and ids as Layout.php). */
$sb = $vars['stockBar'];
$small = getDisplay("smallResourcesFontSize") ? ' style="font-size: 9px;"' : '';
$resourceButton = static function ($i, $icon) use ($sb, $small) {
    $n = $i + 1;
    $alert = ($i === 3 && $sb['production'][3] < 0) ? ' alert' : '';
    ?>
    <a id="stockBarResource<?=$n; ?>" class="stockBarButton" href="production.php?t=<?=$n; ?>"
       title="<?=htmlspecialchars($sb['titles'][$i]); ?>">
        <i class="<?=$icon; ?>"></i>
        <?php if ($sb['productionBoost'][$i]): ?>
            <svg class="productionBoost" viewBox="0 0 11 11"><path d="M5.5 0 11 6H7.5v5h-4V6H0z"/></svg>
        <?php endif; ?>
        <span id="l<?=$n; ?>" class="value<?=$alert; ?>"<?=$small; ?>><?=$sb['storageString'][$i]; ?></span>
        <div class="barBox">
            <div id="lbar<?=$n; ?>" class="bar stock<?=$sb['storageClass'][$i]; ?>" style="width:<?=$sb['percents'][$i]; ?>%;"></div>
        </div>
    </a>
    <?php
};
?>
<div id="stockBar">
    <div class="warehouse">
        <div id="stockBarWarehouseWrapper" class="capacity" title="<?=T("Buildings", "10.title"); ?>">
            <i class="warehouse_medium"></i>
            <div class="value" id="stockBarWarehouse"<?=$small; ?>><?=number_format_x($sb['maxstore'], 9 * 1e6); ?></div>
        </div>
        <?php $resourceButton(0, 'lumber_small'); ?>
        <?php $resourceButton(1, 'clay_small'); ?>
        <?php $resourceButton(2, 'iron_small'); ?>
    </div>
    <div class="granary">
        <div id="stockBarGranaryWrapper" class="capacity" title="<?=T("Buildings", "11.title"); ?>">
            <i class="granary_medium"></i>
            <div class="value" id="stockBarGranary"<?=$small; ?>><?=number_format_x($sb['maxcrop'], 9 * 1e6); ?></div>
        </div>
        <?php $resourceButton(3, 'crop_small'); ?>
        <a id="stockBarFreeCropWrapper" class="stockBarButton" href="production.php?t=5"
           title="<?=htmlspecialchars($sb['titles'][4]); ?>">
            <i class="freeCrop_small"></i>
            <span id="stockBarFreeCrop" class="value"<?=$small; ?>><?=$sb['production'][4]; ?></span>
        </a>
    </div>
</div>
<script type="text/javascript">
    var resources = {
        production: {"l1": <?=$sb['production'][0]; ?>, "l2": <?=$sb['production'][1]; ?>, "l3": <?=$sb['production'][2]; ?>, "l4": <?=$sb['production'][3]; ?>, "l5": <?=$sb['production'][4]; ?>},
        storage: {"l1": <?=$sb['storage'][0]; ?>, "l2": <?=$sb['storage'][1]; ?>, "l3": <?=$sb['storage'][2]; ?>, "l4": <?=$sb['storage'][3]; ?>},
        maxStorage: {"l1": <?=$sb['maxstore']; ?>, "l2": <?=$sb['maxstore']; ?>, "l3": <?=$sb['maxstore']; ?>, "l4": <?=$sb['maxcrop']; ?>}
    };
</script>
