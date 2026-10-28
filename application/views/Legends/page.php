<?php
/*
 * Page frame of the Legends-only pages (tasks.php, dailyquests.php): the same layout as the other game pages
 * (top bar, hero, sidebars, window with close button) around $pageBody.
 *   $pageTitle        title in the window header ('' = none)
 *   $pageContentClass class of #content
 *   $pageBody         HTML of the window
 *   $pageInfo         optional text of the "i" title button
 */
include __DIR__ . '/../html.php';
?>
<body class="v35 webkit <?= $database->bodyClass($_SERVER['HTTP_USER_AGENT'] ?? ''); ?> en-US <?php echo LegendsUi::e($pageContentClass); ?> perspectiveResources <?php echo DIRECTION; ?> buildingsV3">
<div id="background" class="showWall <?= get_tribe() ?>">
    <div id="headerBar"></div>
    <div id="topBar">
        <?php
        include __DIR__ . '/../topheader.php';
        include __DIR__ . '/../toolbar.php';
        ?>
    </div>
    <div id="topBarHeroWrapper">
        <?php include __DIR__ . '/../hero.php'; ?>
    </div>
    <div id="bodyWrapper">
        <div id="center">
            <?php include __DIR__ . '/../sideinfo.php'; ?>
            <div id="contentOuterContainer" class="size1">
                <?php include __DIR__ . '/../res.php'; ?>
                <div class="contentTitle">
                    <?php if (!empty($pageInfo)) { // "i" title button (Legends knowledge base) with the page's explanation ?>
                    <a id="knowledgeBaseButton" class="contentTitleButton buttonFramed withIcon rectangle green" href="#" title="<?php echo LegendsUi::e(LegendsUi::t('UI_INFO', 'Information')); ?>"
                       onclick="LegendsDialog.alert(<?php echo LegendsUi::e(json_encode($pageInfo, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS)); ?>);return false;">&nbsp;</a>
                    <?php } ?>
                    <a id="closeContentButton" class="contentTitleButton buttonFramed green withIcon rectangle" href="dorf1.php" title="<?php echo LegendsUi::e(LegendsUi::t('UI_CLOSE', 'Close')); ?>">
                        <svg viewBox="0 0 20 20"><g class="outline"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"></path></g><g class="icon"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"></path></g></svg>
                    </a>
                </div>
                <div class="contentContainer">
                    <div id="content" class="<?php echo LegendsUi::e($pageContentClass); ?>">
                        <?php if ($pageTitle !== '') { ?><h1 class="titleInHeader"><?php echo LegendsUi::e($pageTitle); ?></h1><?php } ?>
                        <?php echo $pageBody; ?>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="contentFooter">&nbsp;</div>
            </div>
            <?php include __DIR__ . '/../rightsideinfor.php'; ?>
            <div class="clear"></div>
        </div>
        <?php include __DIR__ . '/../header.php'; ?>
    </div>
    <div id="ce"></div>
</div>
</body>
</html>
