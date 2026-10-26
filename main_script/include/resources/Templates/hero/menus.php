<?php if (is_t46_layout()): ?>
    <?php
    /* Travian Legends (29c89d54): the hero window has Inventory / Attributes / Appearance; adventures and
       auctions are their own windows (opened from the hero buttons in the top bar), so they get no hero tabs. */
    $t46View = $vars['selectedTab'] == 1 && (($_GET['view'] ?? '') === 'attributes') ? 'attributes' : ($vars['selectedTab'] == 1 ? 'inventory' : '');
    // Favourite keys: 1 inventory, 5 attributes (opened as t=1&view=attributes), 2 appearance.
    $t46Tabs = [
        ['hero.php?t=1', T("HeroGlobal", "Inventory", T("HeroInventory", "inventory", "Inventory")), $t46View === 'inventory', 1],
        ['hero.php?t=1&amp;view=attributes', T("HeroGlobal", "Attributes"), $t46View === 'attributes', 5],
        ['hero.php?t=2', T("HeroGlobal", "Appear"), $vars['selectedTab'] == 2, 2],
    ];
    $t46FavourKey = 0;
    foreach ($t46Tabs as $t46Tab) {
        if ($t46Tab[2]) $t46FavourKey = $t46Tab[3];
    }
    ?>
    <?php if ($vars['selectedTab'] <= 2): ?>
        <?php if ($t46FavourKey): ?><?=\resources\View\LegendsUi::favorButton('hero', $t46FavourKey); ?><?php endif; ?>
        <div class="contentNavi subNavi">
            <?php $t46Favourite = (int)\Core\Session::getInstance()->getFavoriteTab("hero"); ?>
            <?php foreach ($t46Tabs as [$href, $label, $active, $key]): ?>
                <div class="container <?=$active ? 'active' : 'normal'; ?>">
                    <div class="content favor favorKey<?=$key; ?><?=$t46Favourite === $key ? ' favorActive' : ''; ?>"><a href="<?=$href; ?>" class="tabItem"><?=$label; ?></a></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($t46View !== ''): ?>
            <script type="text/javascript">jQuery('#content').addClass('heroView-<?=$t46View; ?>');</script>
        <?php endif; ?>
    <?php endif; ?>
    <?php return; ?>
<?php endif; ?>
<a id="tabFavorButton" class="contentTitleButton"
   onclick="
           Travian.ajax(
           {
           data:
           {
           cmd: 'tabFavorite',
           name: 'hero',
           number: '<?=$vars['selectedTab'];?>'
           },
           onSuccess: function(data)
           {
           if (data.success)
           {
           jQuery('.favor').removeClass('favorActive');
           jQuery('.favor.favorKey<?=$vars['selectedTab'];?>').addClass('favorActive');
           }
           }
           });
           return false;
           "
   title="<?=$vars['favorText'];?>"
>&nbsp;</a>
<?php $favorTabId = \Core\Session::getInstance()->getFavoriteTab("hero");?>
<?php /* attributesTab and adventuresTab: the tutorial frames these tabs in red */ ?>
<div class="contentNavi subNavi ">
    <div title="" class="container attributesTab <?=$vars['selectedTab'] == 1 ? "active" : "normal";?>">
        <div class="background-start">&nbsp;</div>
        <div class="background-end">&nbsp;</div>
        <div class="content favor <?=$favorTabId == 1 ? "favorActive" : '';?> favorKey1">

            <a href="hero.php?t=1" class="tabItem"><?=T("HeroGlobal", "Attributes");?> <img src="img/x.gif" class="favorIcon" alt="<?=T("Global", "This tab is set as favourite");?>"/></a>
        </div>
    </div>
    <div title="" class="container appearanceTab <?=$vars['selectedTab'] == 2 ? "active" : "normal";?>">
        <div class="background-start">&nbsp;</div>
        <div class="background-end">&nbsp;</div>
        <div class="content favor <?=$favorTabId == 2 ? "favorActive" : '';?> favorKey2">

            <a href="hero.php?t=2" class="tabItem"><?=T("HeroGlobal", "Appear");?> <img src="img/x.gif" class="favorIcon" alt="<?=T("Global", "This tab is set as favourite");?>"/></a>
        </div>
    </div>
    <div title="" class="container adventuresTab <?=$vars['selectedTab'] == 3 ? "active" : "normal";?>">
        <div class="background-start">&nbsp;</div>
        <div class="background-end">&nbsp;</div>
        <div class="content favor <?=$favorTabId == 3 ? "favorActive" : '';?> favorKey3">

            <a href="hero.php?t=3" class="tabItem"><?=T("HeroGlobal", "Adventure");?> <img src="img/x.gif" class="favorIcon" alt="<?=T("Global", "This tab is set as favourite");?>"/></a>
        </div>
    </div>
    <div title="" class="container auctionTab <?=$vars['selectedTab'] == 4 ? "active" : "normal";?>">
        <div class="background-start">&nbsp;</div>
        <div class="background-end">&nbsp;</div>
        <div class="content favor <?=$favorTabId == 4 ? "favorActive" : '';?> favorKey4">
            <a href="hero.php?t=4" class="tabItem"><?=T("HeroGlobal", "Auctions");?> <img src="img/x.gif" class="favorIcon" alt="<?=T("Global", "This tab is set as favourite");?>"/></a>
        </div>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>