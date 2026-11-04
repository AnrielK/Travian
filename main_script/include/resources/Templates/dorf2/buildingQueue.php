<div class="boxes buildingList">
    <div class="boxes-tl"></div>
    <div class="boxes-tr"></div>
    <div class="boxes-tc"></div>
    <div class="boxes-ml"></div>
    <div class="boxes-mr"></div>
    <div class="boxes-mc"></div>
    <div class="boxes-bl"></div>
    <div class="boxes-br"></div>
    <div class="boxes-bc"></div>
    <div class="boxes-contents cf">
        <?php if($vars['normal'] > 0):?>
            <?php if (is_hero_v2()): ?><h5><?=T("Buildings", "underConstruction", T("Buildings", "Buildings"));?>:</h5><?php else: ?><?=T("Buildings", "Buildings");?>:<?php endif; ?>
            <?=$vars['finishNowButton'];?>
        <?php endif;?>
        <?=$vars['buildings'];?>
    </div>
</div>
<script type="text/javascript">
    var bld =<?=$vars['buildsJson'];?>;
</script>