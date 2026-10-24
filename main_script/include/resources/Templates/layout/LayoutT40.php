<?php
/* Page frame of the Travian 4.0 graphic pack (gpack.php 'layout' => 't40', included by Layout.php): the 4.0
 * header with the round navigation, the resource line, the village sign on the left and the hero, player,
 * alliance and village list signs on the right, the quest master's picture and the info sign on the left tree, and
 * on the tribe and start choice (no navigation bar) the wooden menu on the left
 * (styles: <pack>/mainPage/lang/openvillage-t40.css). The pages inside it are the same as in the other layouts. */

use Core\Config;

$t40 = $vars['t40'] ?? [];
$hero = $t40['hero'] ?? null;
$stock = $vars['stockBar'] ?? null;
?>
<?php require __DIR__ . "/head.php"; ?>
<body class="v35 webkit chrome <?=get_locale();?> <?= Config::getProperty("settings", "global_css_class"); ?> <?=$vars['contentCssClass']; ?> <?= $vars['colorBlind'] ? 'colorBlind' : ''; ?> <?=$vars['bodyCssClass']; ?> <?= (getDirection() == 'RTL' ? 'rtl' : 'ltr'); ?> season-<?= detect_season(); ?> buildingsV1 layoutT40">
<div id="reactDialogWrapper"></div>
<div id="wrapper">
    <img id="staticElements" src="img/x.gif" alt=""/>
    <div class="bodyWrapper">
        <img style="filter:chroma();" src="img/x.gif" id="msfilter" alt=""/>
        <?php if ($vars['headerBar']): ?>
            <a id="logout" href="logout.php" title="<?=T("inGame", "Logout.Logout"); ?>||<?=T("inGame",
                "Logout.Log out from the game"); ?>"></a>
        <?php endif; ?>
        <div id="header">
            <div id="mtop">
                <a id="logo" href="dorf1.php" title="OpenVillage"></a>
                <?php if ($vars['showNavBar']): ?>
                    <ul id="navigation">
                        <li class="resources">
                            <a class="<?=$vars['bodyCssClass'] == 'perspectiveResources' ? 'active' : ''; ?>"
                               href="dorf1.php" accesskey="1"
                               title="<?=T("inGame", "Navigation.Resources"); ?>||"></a>
                        </li>
                        <li class="village">
                            <a class="<?=$vars['bodyCssClass'] == 'perspectiveBuildings' ? 'active' : ''; ?>"
                               href="dorf2.php" accesskey="2"
                               title="<?=T("inGame", "Navigation.Buildings"); ?>||"></a>
                        </li>
                        <li class="map">
                            <a href="karte.php" accesskey="3" title="<?=T("inGame", "Navigation.Map"); ?>||"></a>
                        </li>
                        <li class="stats">
                            <a href="statistiken.php" accesskey="4"
                               title="<?=T("inGame", "Navigation.Statistics"); ?>||"></a>
                        </li>
                        <li class="reports">
                            <a href="reports.php" accesskey="5"
                               title="<?=T("inGame", "Navigation.Reports"); ?>||<?=T("inGame",
                                   "Navigation.newReports"); ?>: <?=$vars['newReportsCount']; ?>"></a>
                            <?php if ($vars['newReportsCount']): ?>
                                <div class="bubble">
                                    <div class="bubble-background-l"></div>
                                    <div class="bubble-background-r"></div>
                                    <div class="bubble-content"><?=$vars['newReportsCount'] > 99 ? '+99' : $vars['newReportsCount']; ?></div>
                                </div>
                            <?php endif; ?>
                        </li>
                        <li class="messages">
                            <a href="messages.php" accesskey="6"
                               title="<?=T("inGame", "Navigation.Messages"); ?>||<?=T("inGame",
                                   "Navigation.newMessages"); ?>: <?=$vars['newMessagesCount']; ?>"></a>
                            <?php if ($vars['newMessagesCount']): ?>
                                <div class="bubble">
                                    <div class="bubble-background-l"></div>
                                    <div class="bubble-background-r"></div>
                                    <div class="bubble-content"><?=$vars['newMessagesCount'] > 99 ? '+99' : $vars['newMessagesCount']; ?></div>
                                </div>
                            <?php endif; ?>
                        </li>
                    </ul>
                    <div id="plusLink">
                        <div id="plus">
                            <a class="plusBtn" href="#"
                               onclick="jQuery(window).trigger('startPaymentWizard', {}); this.blur(); return false;"
                               title="<?=T("inGame", "Navigation.Buy gold"); ?>"><span
                                        class="plusBtn-l">Plus</span><span class="plusBtn-r"></span></a>
                        </div>
                        <div id="gs">
                            <p class="gold">
                                <a href="#" title="<?=T("inGame", "gold"); ?>"
                                   onclick="jQuery(window).trigger('startPaymentWizard', {data:{activeTab: 'pros'}}); return false;"><img
                                            src="img/x.gif" class="gold" alt="<?=T("inGame", "gold"); ?>"/><br/><span
                                            class="ajaxReplaceableGoldAmount"><?php if (getCustom("serverIsFreeGold")): ?>&infin;<?php else: ?><?=$vars['goldCount']; ?><?php endif; ?></span></a>
                            </p>
                            <p class="silver">
                                <a href="#" title="<?=T("inGame", "silver"); ?>"
                                   onclick="jQuery(window).trigger('startPaymentWizard', {data:{activeTab: 'pros'}}); return false;"><img
                                            src="img/x.gif" class="silver" alt="<?=T("inGame", "silver"); ?>"/><br/><span
                                            class="ajaxReplaceableSilverAmount"><?=$vars['silverCount']; ?></span></a>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="clear"></div>
            </div>
        </div>
        <div id="mid">
            <?php if (!$vars['showNavBar']): ?>
                <div id="side_navi">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <?php if ($vars['contentCssClass'] == 'activate'): ?>
                            <li><a href="/docs/">Rules</a></li>
                            <li><a href="logout.php"><?=T("inGame", "Logout.Logout"); ?></a></li>
                        <?php else: ?>
                            <li class="<?=$vars['contentCssClass'] == 'login' ? 'active' : ''; ?>"><a href="login.php">Login</a></li>
                            <li><a href="/?join=1">Register</a></li>
                            <li><a href="/docs/">Rules</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <a id="ingameManual" href="<?=$vars['showNavBar'] ? 'help.php' : '/docs/'; ?>" title="<?=T("inGame", "Help.Help"); ?>"><img
                        class="question" src="img/x.gif" alt=""/></a>
            <?php if (isset($t40['villageName'])): ?>
                <div id="villageName">
                    <div class="clickable" onclick="window.location.href='dorf3.php';"
                         title="<?=htmlspecialchars($t40['villageName']); ?>">
                        <?=$t40['villageName']; ?><br/>
                        <span class="loyalty <?=$t40['loyalty'] > 100 ? 'high' : ($t40['loyalty'] >= 50 ? 'medium' : 'low'); ?>"><?=T("inGame",
                                "loyalty"); ?>: <?=round($t40['loyalty']); ?>%</span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($t40['questmaster'])): ?>
                <div class="questMaster">
                    <button type="button"
                            class="master nation<?=in_array($t40['hero']['race'] ?? 1, [1, 2, 3]) ? $t40['hero']['race'] : 1; ?>"
                            onclick="jQuery('#questmasterButton').click(); return false;"
                            title="<?=T("Quest", "helperAndTasks"); ?>"></button>
                </div>
                <?php /* the T4.4 quest master box, hidden: its script opens the quest dialog */ ?>
                <div id="t40Questmaster"><?=$t40['questmaster']; ?></div>
            <?php endif; ?>
            <?php if (!empty($t40['infoCount'])): ?>
                <div id="sideInfoCountdown">
                    <div class="head"></div>
                    <div class="content">
                        <ul><?=$t40['infoItems']; ?></ul>
                        <?php if ($t40['infoCount'] > 1): ?>
                            <div class="pager"><a class="back">&lsaquo;</a> <span class="page">1</span>/<?=$t40['infoCount']; ?>
                                <a class="next">&rsaquo;</a></div>
                        <?php endif; ?>
                    </div>
                </div>
                <script type="text/javascript">
                    jQuery(function () {
                        var sign = jQuery('#sideInfoCountdown'), items = sign.find('.content > ul > li'), shown = 0;
                        function show(i) {
                            shown = (i + items.length) % items.length;
                            items.removeClass('shown').eq(shown).addClass('shown');
                            sign.find('.pager .page').text(shown + 1);
                        }
                        sign.find('.pager .back').click(function () { show(shown - 1); });
                        sign.find('.pager .next').click(function () { show(shown + 1); });
                        show(0);
                        Travian.Game.Layout.setupInfoboxItemsDeletionWithMessage('<?=T("inGame",
                            "Delete this message permanently?");?>', '<?=T("inGame", "Confirm");?>');
                    });
                </script>
            <?php endif; ?>
            <?php if ($stock && $vars['showStockbar']): ?>
                <ul id="res">
                    <?php foreach ([0, 1, 2, 3] as $i): $max = $i == 3 ? $stock['maxcrop'] : $stock['maxstore']; ?>
                        <li class="r<?=$i + 1; ?>" title="<?=htmlspecialchars($stock['titles'][$i]); ?>">
                            <p>
                                <a href="production.php?t=<?=$i + 1; ?>"><img src="img/x.gif" alt=""/></a>
                                <span class="value<?=$i == 3 && $stock['production'][3] < 0 ? ' alert' : ''; ?>"><span
                                            id="l<?=$i + 1; ?>"><?=$stock['storageString'][$i]; ?></span>/<?=number_format_x($max,
                                        9 * 1e6); ?></span>
                            </p>
                            <div class="bar-bg">
                                <div id="lbar<?=$i + 1; ?>" class="bar stock<?=$stock['storageClass'][$i]; ?>"
                                     style="width:<?=$stock['percents'][$i]; ?>%;"></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <li class="r5" title="<?=htmlspecialchars($stock['titles'][4]); ?>">
                        <p>
                            <a href="production.php?t=5"><img src="img/x.gif" alt=""/></a>
                            <span class="value"><?=number_format_x($stock['cropUsage'][0]); ?>/<?=number_format_x($stock['cropUsage'][1]); ?></span>
                        </p>
                    </li>
                </ul>
                <script type="text/javascript">
                    var resources = {};
                    resources.production = {
                        "l1": <?=$stock['production'][0];?>,
                        "l2": <?=$stock['production'][1];?>,
                        "l3": <?=$stock['production'][2];?>,
                        "l4": <?=$stock['production'][3];?>,
                        "l5": <?=$stock['production'][4];?>
                    };
                    resources.storage = {
                        "l1": <?=$stock['storage'][0];?>,
                        "l2": <?=$stock['storage'][1];?>,
                        "l3": <?=$stock['storage'][2];?>,
                        "l4": <?=$stock['storage'][3];?>
                    };
                    resources.maxStorage = {
                        "l1": <?=$stock['maxstore'];?>,
                        "l2": <?=$stock['maxstore'];?>,
                        "l3": <?=$stock['maxstore'];?>,
                        "l4": <?=$stock['maxcrop'];?>
                    };
                </script>
            <?php endif; ?>
            <div id="contentOuterContainer">
                <div class="contentTitle">
                    <?php if ($vars['showCloseButton']): ?>
                        <a id="closeContentButton" class="contentTitleButton"
                           href="<?=$vars['bodyCssClass'] == 'perspectiveResources' ? 'dorf1.php' : 'dorf2.php'; ?>"
                           title="<?=T("Global", "General.closeWindow"); ?>">&nbsp;</a>
                    <?php endif; ?>
                    <?php if ($vars['answerId']): ?>
                        <a id="answersButton" class="contentTitleButton"
                           href="<?=getAnswersUrl(); ?>aid=<?=$vars['answerId']; ?>#go2answer" target="_blank"
                           title="<?=T("Global", "FAQ"); ?>">&nbsp;</a>
                    <?php endif; ?>
                </div>
                <div class="contentContainer">
                    <div id="content" class="<?=$vars['contentCssClass']; ?>">
                        <?php
                        if ($vars['titleInHeader']) {
                            echo '<h1 class="titleInHeader">' . $vars['titleInHeader'] . '</h1>';
                        }
                        echo $vars['content'];
                        ?>
                        <div class="clear"></div>
                    </div>
                    <div class="clear">&nbsp;</div>
                </div>
                <div class="contentFooter"></div>
            </div>
            <?php if ($hero): ?>
                <div id="side_info">
                    <div class="sideInfoHero">
                        <img class="heroImage"
                             src="hero_head.php?uid=<?=$hero['uid']; ?>&amp;size=sideinfo&amp;<?=$hero['heroImageHash']; ?>"
                             alt=""/>
                        <a class="heroImageBorder" href="hero.php<?=$hero['hasNewPoints'] ? '?flagAttributesBoxOpen' : ''; ?>"
                           title="<?=T("HeroGlobal", "HeroOverview"); ?>||<?=htmlspecialchars($hero['longStatus']); ?>"></a>
                        <a class="adventures" href="hero.php?t=3"
                           title="<?=T("Auction", "Adventure"); ?>"></a>
                        <a class="auctions" href="hero.php?t=4" title="<?=T("HeroGlobal", "Auctions"); ?>"></a>
                        <?php if ($hero['adventureWhiteButton']['adventureCount'] ?? 0): ?>
                            <div class="bubble adventureCount">
                                <div class="bubble-background-l"></div>
                                <div class="bubble-background-r"></div>
                                <div class="bubble-content"><?=$hero['adventureWhiteButton']['adventureCount']; ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="sideInfoPlayer">
                        <img src="img/x.gif" class="nationBig nationBig<?=$hero['race']; ?>"
                             alt="<?=T("Global", "races." . $hero['race']); ?>"/>
                        <a class="signLink" href="spieler.php" title="<?=T("Profile", "Player Profile"); ?>"><span
                                    class="wrap"><?=$hero['playerName']; ?></span></a>
                    </div>
                    <div class="sideInfoAlly">
                        <img src="img/x.gif" class="crest" alt=""/>
                        <a class="signLink" href="<?=$t40['allianceId'] ? 'allianz.php' : 'build.php?gid=18'; ?>"><span
                                    class="wrap"><?=$t40['allianceId'] ? $t40['allianceName'] : T("embassyWhite", "no alliance"); ?></span></a>
                    </div>
                    <div id="villageList" class="listing">
                        <div class="head"><a href="dorf3.php"><?=T("inGame", "villages"); ?>:</a></div>
                        <div class="list">
                            <ul>
                                <?php foreach ($t40['villages'] ?? [] as $v): ?>
                                    <li class="entry"
                                        title="<?=htmlspecialchars($v['name'] . ' (' . $v['x'] . '|' . $v['y'] . ')'); ?>">
                                        <a href="<?=$v['href']; ?>" class="<?=$v['active'] ? 'active' : ''; ?>"><?=$v['name']; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="foot"></div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="clear"></div>
        </div>
        <div id="footer">
            <div id="mfoot">
                <div>
                    <a href="/">Home</a><a href="/docs/">Rules and operations</a><a href="/terms.html">Terms</a><a
                            href="/privacy.html">Privacy</a>
                </div>
                <p class="copyright">OpenVillage preservation project · Not affiliated with Travian Games.</p>
            </div>
        </div>
        <?php if ($vars['headerBar']): ?>
            <div id="servertime" class="stime">
                <div class="content-background-l">&nbsp;</div>
                <div class="content-background-r">&nbsp;</div>
                <div class="content day"><?=T("inGame", "serverTime"); ?>:&nbsp;<?=appendTimer($vars['dateTime'] ?? time(), 1); ?></div>
            </div>
        <?php endif; ?>
    </div>
    <div id="ce"></div>
</div>
<script type="text/javascript">
    var T4_feature_flags = <?=json_encode([
        'vacationMode' => true, 'territory' => false, 'heroitems' => true, 'allianceBonus' => true,
        'boostedStart' => false, 'pushingProtectionAlways' => false, 'tribesEgyptiansAndHuns' => false,
        'hideFoolsArtifacts' => false, 'welcomeScreen' => false,
    ]);?>
</script>
</body>
</html>
<!---- This page was generated in <?= round(1000 * (microtime(true) - $GLOBALS['start_time']), 2); ?> ms ---->
