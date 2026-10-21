<?php
/*
 * Page layout for graphic pack 29c89d54 (Travian Legends look: top bar with hero, round navigation,
 * stock bar under the navigation, framed sidebars, full-width village views). Selected by Layout.php
 * when is_t46_layout(); every id the game scripts use (l1…l4, lbar1…lbar4, stockBar*, servertime, …)
 * is kept from Layout.php.
 */

use Core\Config;

$t46Tribes = [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 6 => 'egyptian', 7 => 'hun'];
$t46Tribe = $t46Tribes[$vars['t46Race'] ?? 1] ?? 'roman';
$t46V2 = is_hero_v2(); // Travian Legends 655 packs: round header buttons, logo with the server time below
$t46VillageView = in_array($vars['contentCssClass'], ['village1', 'village2'], true);
$t46Indicator = static function ($count) {
    return $count ? '<div class="indicator">' . ($count > 99 ? '+99' : (int)$count) . '</div>' : '';
};
?>
<?php require __DIR__ . "/head.php"; ?>
<body class="v39 webkit chrome <?=get_locale(); ?> <?=Config::getProperty("settings", "global_css_class"); ?> <?=$vars['contentCssClass']; ?> <?=$vars['colorBlind'] ? 'colorBlind' : ''; ?> <?=$vars['bodyCssClass']; ?> <?=(getDirection() == 'RTL' ? 'rtl' : 'ltr'); ?> season-<?=detect_season(); ?> buildingsV3 t46Layout t46-<?=$t46Tribe; ?><?=$t46V2 ? ' legendsV2' : ''; ?>" data-theme="default">
<div id="reactDialogWrapper"></div>
<div id="background" class="<?=$t46Tribe; ?>">
    <div id="bodyWrapper">
        <img style="filter:chroma();" src="img/x.gif" id="msfilter" alt=""/>
        <div id="topBar">
            <?php if ($t46V2): ?>
                <a id="logo" class="legendsLogo" href="<?=$vars['showNavBar'] ? 'dorf1.php' : '/'; ?>" title="OpenVillage 4.6">
                    <svg viewBox="0 0 172 40" aria-label="OpenVillage">
                        <path class="emblem" d="M4 3h18v18c0 8-5 13-9 16-4-3-9-8-9-16z"/>
                        <circle class="coin" cx="13" cy="17" r="6.5"/><path class="coinMark" d="M10 13.5h6v1.6h-2.2v6.4h-1.6v-6.4H10z"/>
                        <text class="title" x="30" y="23">OPENVILLAGE</text>
                        <text class="subtitle" x="31" y="35">L E G E N D S</text>
                    </svg>
                </a>
            <?php else: ?>
                <a id="logo" href="<?=$vars['showNavBar'] ? 'dorf1.php' : '/'; ?>" title="OpenVillage 4.6"><span>OpenVillage</span></a>
            <?php endif; ?>
            <div id="header">
                <?php if ($vars['showNavBar']): ?>
                    <div id="navigation">
                        <a class="village resourceView" href="dorf1.php" accesskey="1"
                           title="<?=T("inGame", "Navigation.Resources"); ?>||"></a>
                        <a class="village buildingView" href="dorf2.php" accesskey="2"
                           title="<?=T("inGame", "Navigation.Buildings"); ?>||"></a>
                        <a class="map" href="karte.php" accesskey="3"
                           title="<?=T("inGame", "Navigation.Map"); ?>||"></a>
                        <a class="statistics" href="statistiken.php" accesskey="4"
                           title="<?=T("inGame", "Navigation.Statistics"); ?>||"></a>
                        <a class="reports" href="reports.php" accesskey="5"
                           title="<?=T("inGame", "Navigation.Reports"); ?>||<?=T("inGame", "Navigation.newReports"); ?>: <?=$vars['newReportsCount']; ?>"><?=$t46Indicator($vars['newReportsCount']); ?></a>
                        <a class="messages" href="messages.php" accesskey="6"
                           title="<?=T("inGame", "Navigation.Messages"); ?>||<?=T("inGame", "Navigation.newMessages"); ?>: <?=$vars['newMessagesCount']; ?>"><?=$t46Indicator($vars['newMessagesCount']); ?></a>
                        <a class="dailyQuests" href="dailyquests.php" accesskey="7" title="<?=T("DailyQuest", "Daily Quests"); ?>"></a>
                    </div>
                    <?php if (isset($vars['stockBar']) && $vars['showStockbar']): ?>
                        <?php require __DIR__ . '/stockBarT46.php'; ?>
                    <?php endif; ?>
                    <a class="shop" href="gold.php" title="<?=T("inGame", "Navigation.Buy gold"); ?>"></a>
                    <a class="currency" href="gold.php">
                        <i class="goldCoin_medium" title="<?=T("inGame", "gold"); ?>"></i>
                        <div class="ajaxReplaceableGoldAmount"><?=getCustom("serverIsFreeGold") ? '<b>' . T("Global", "Unlimited") . '</b>' : $vars['goldCount']; ?></div>
                        <i class="silverCoin_medium" title="<?=T("inGame", "silver"); ?>"></i>
                        <div class="ajaxReplaceableSilverAmount"><?=$vars['silverCount']; ?></div>
                    </a>
                <?php endif; ?>
            </div>
            <?php if ($t46V2 && ($vars['showNavBar'] || $vars['headerBar'])): ?>
                <nav id="outOfGame" class="<?=getDirection(); ?>">
                    <?php if ($vars['showNavBar']): ?>
                        <a class="layoutButton buttonFramed withIcon round profile" href="spieler.php"
                           title="<?=T("inGame", "Profile.Profile"); ?>||<?=T("inGame", "Profile.edit profile description"); ?>">
                            <svg class="profile" viewBox="0 0 16 16"><g class="outline"><path d="M8 1.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7zM1.5 15c0-3.3 2.9-5.2 6.5-5.2s6.5 1.9 6.5 5.2z"/></g><g class="icon"><path d="M8 1.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7zM1.5 15c0-3.3 2.9-5.2 6.5-5.2s6.5 1.9 6.5 5.2z"/></g></svg>
                        </a>
                        <?php if (!$vars['isSitter']): ?>
                            <a class="layoutButton buttonFramed withIcon round options" href="options.php"
                               title="<?=T("inGame", "Options.Options"); ?>||<?=T("inGame", "Options.edit account settings"); ?>">
                        <?php else: ?>
                            <a class="layoutButton buttonFramed withIcon round options disabled"
                               title="<?=T("inGame", "Options.Options"); ?>||<?=htmlspecialchars('<span class="warning">' . T("inGame", "Options.you may not edit settings of another account") . '</span>'); ?>">
                        <?php endif; ?>
                            <svg class="options" viewBox="0 0 16 16"><g class="outline"><path d="M6.8 1h2.4l.4 2 1.4.6 1.7-1.2 1.7 1.7-1.2 1.7.6 1.4 2 .4v2.4l-2 .4-.6 1.4 1.2 1.7-1.7 1.7-1.7-1.2-1.4.6-.4 2H6.8l-.4-2-1.4-.6-1.7 1.2-1.7-1.7 1.2-1.7-.6-1.4-2-.4V6.8l2-.4.6-1.4-1.2-1.7 1.7-1.7 1.7 1.2 1.4-.6zM8 5.6a2.4 2.4 0 1 0 0 4.8 2.4 2.4 0 0 0 0-4.8z"/></g><g class="icon"><path d="M6.8 1h2.4l.4 2 1.4.6 1.7-1.2 1.7 1.7-1.2 1.7.6 1.4 2 .4v2.4l-2 .4-.6 1.4 1.2 1.7-1.7 1.7-1.7-1.2-1.4.6-.4 2H6.8l-.4-2-1.4-.6-1.7 1.2-1.7-1.7 1.2-1.7-.6-1.4-2-.4V6.8l2-.4.6-1.4-1.2-1.7 1.7-1.7 1.7 1.2 1.4-.6zM8 5.6a2.4 2.4 0 1 0 0 4.8 2.4 2.4 0 0 0 0-4.8z"/></g></svg>
                        </a>
                        <a class="layoutButton buttonFramed withIcon round help" href="help.php"
                           title="<?=T("inGame", "Help.Help"); ?>||<?=T("inGame", "Help.Manuals, Answers and Support"); ?>">
                            <svg class="help" viewBox="0 0 16 16"><g class="outline"><path d="M4.6 5.4C4.6 3.3 6.1 2 8.1 2s3.4 1.2 3.4 3c0 1.5-.9 2.2-1.9 2.9-.8.5-1 .9-1 1.8v.6H6.7v-.8c0-1.4.5-2.1 1.5-2.8.8-.5 1.1-.9 1.1-1.5 0-.7-.5-1.2-1.3-1.2-.9 0-1.4.6-1.4 1.4zM6.6 11.6h2.2V14H6.6z"/></g><g class="icon"><path d="M4.6 5.4C4.6 3.3 6.1 2 8.1 2s3.4 1.2 3.4 3c0 1.5-.9 2.2-1.9 2.9-.8.5-1 .9-1 1.8v.6H6.7v-.8c0-1.4.5-2.1 1.5-2.8.8-.5 1.1-.9 1.1-1.5 0-.7-.5-1.2-1.3-1.2-.9 0-1.4.6-1.4 1.4zM6.6 11.6h2.2V14H6.6z"/></g></svg>
                        </a>
                    <?php endif; ?>
                    <a class="layoutButton buttonFramed withIcon round logout" href="logout.php"
                       title="<?=T("inGame", "Logout.Logout"); ?>||<?=T("inGame", "Logout.Log out from the game"); ?>">
                        <svg class="logout" viewBox="0 0 16 16"><g class="outline"><path d="M3.2 1.5 8 6.3l4.8-4.8 1.7 1.7L9.7 8l4.8 4.8-1.7 1.7L8 9.7l-4.8 4.8-1.7-1.7L6.3 8 1.5 3.2z"/></g><g class="icon"><path d="M3.2 1.5 8 6.3l4.8-4.8 1.7 1.7L9.7 8l4.8 4.8-1.7 1.7L8 9.7l-4.8 4.8-1.7-1.7L6.3 8 1.5 3.2z"/></g></svg>
                    </a>
                </nav>
                <script type="text/javascript">
                    jQuery('#outOfGame a.logout').click(function () {
                        var windows = Travian.WindowManager.getWindows();
                        for (var i = 0; i < windows.length; i++) {
                            Travian.WindowManager.unregister(windows[i]);
                        }
                    });
                </script>
            <?php elseif ($vars['showNavBar'] || $vars['headerBar']): ?>
                <ul id="outOfGame" class="<?=getDirection(); ?>">
                    <?php if ($vars['showNavBar']): ?>
                        <li class="profile">
                            <a href="spieler.php" title="<?=T("inGame", "Profile.Profile"); ?>||<?=T("inGame", "Profile.edit profile description"); ?>">
                                <svg class="profile" viewBox="0 0 16 16"><path class="outline" d="M8 1.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7zM1.5 15c0-3.3 2.9-5.2 6.5-5.2s6.5 1.9 6.5 5.2z"/></svg>
                            </a>
                        </li>
                        <li class="options">
                            <?php if (!$vars['isSitter']): ?>
                                <a href="options.php" title="<?=T("inGame", "Options.Options"); ?>||<?=T("inGame", "Options.edit account settings"); ?>">
                                    <svg class="options" viewBox="0 0 16 16"><path class="outline" d="M6.8 1h2.4l.4 2 1.4.6 1.7-1.2 1.7 1.7-1.2 1.7.6 1.4 2 .4v2.4l-2 .4-.6 1.4 1.2 1.7-1.7 1.7-1.7-1.2-1.4.6-.4 2H6.8l-.4-2-1.4-.6-1.7 1.2-1.7-1.7 1.2-1.7-.6-1.4-2-.4V6.8l2-.4.6-1.4-1.2-1.7 1.7-1.7 1.7 1.2 1.4-.6zM8 5.6a2.4 2.4 0 1 0 0 4.8 2.4 2.4 0 0 0 0-4.8z"/></svg>
                                </a>
                            <?php else: ?>
                                <div class="a disabled" title="<?=T("inGame", "Options.Options"); ?>||<?=htmlspecialchars('<span class="warning">' . T("inGame", "Options.you may not edit settings of another account") . '</span>'); ?>">
                                    <svg class="options" viewBox="0 0 16 16"><circle class="outline" cx="8" cy="8" r="6"/></svg>
                                </div>
                            <?php endif; ?>
                        </li>
                        <li class="forum">
                            <a target="_blank" href="<?=getForumUrl(); ?>" title="<?=T("inGame", "Forum.Forum"); ?>||<?=T("inGame", "Forum.Meet other players on our external forum"); ?>">
                                <svg class="discord" viewBox="0 0 16 16"><path class="outline" d="M1.5 2.5h13v8.5H7l-3.5 3v-3h-2z"/></svg>
                            </a>
                        </li>
                        <li class="help">
                            <a href="help.php" title="<?=T("inGame", "Help.Help"); ?>||<?=T("inGame", "Help.Manuals, Answers and Support"); ?>">
                                <svg class="answers" viewBox="0 0 16 16"><path class="outline" d="M4.6 5.4C4.6 3.3 6.1 2 8.1 2s3.4 1.2 3.4 3c0 1.5-.9 2.2-1.9 2.9-.8.5-1 .9-1 1.8v.6H6.7v-.8c0-1.4.5-2.1 1.5-2.8.8-.5 1.1-.9 1.1-1.5 0-.7-.5-1.2-1.3-1.2-.9 0-1.4.6-1.4 1.4zM6.6 11.6h2.2V14H6.6z"/></svg>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="logout<?=$vars['showNavBar'] ? '' : ' logoutOnly'; ?>">
                        <a href="logout.php" title="<?=T("inGame", "Logout.Logout"); ?>||<?=T("inGame", "Logout.Log out from the game"); ?>">
                            <svg class="logout" viewBox="0 0 16 16"><path class="outline" d="M3.2 1.5 8 6.3l4.8-4.8 1.7 1.7L9.7 8l4.8 4.8-1.7 1.7L8 9.7l-4.8 4.8-1.7-1.7L6.3 8 1.5 3.2z"/></svg>
                        </a>
                    </li>
                </ul>
                <script type="text/javascript">
                    jQuery('#outOfGame li.logout a').click(function () {
                        var windows = Travian.WindowManager.getWindows();
                        for (var i = 0; i < windows.length; i++) {
                            Travian.WindowManager.unregister(windows[i]);
                        }
                    });
                </script>
            <?php endif; ?>
        </div>
        <?=$vars['topBarHero'] ?? ''; ?>
        <div id="center">
            <?php if ($vars['headerBar']): ?>
                <div id="servertime" class="stime">
                    <span><?=T("inGame", "serverTime"); ?>:</span>
                    <span><?=appendTimer($vars['dateTime'] ?? time(), 1); ?>&nbsp;(UTC&nbsp;<?=date('P'); ?>)</span>
                </div>
            <?php endif; ?>
            <div id="sidebarBeforeContent" class="sidebar beforeContent">
                <?=$vars['sidebarBeforeContent']; ?>
            </div>
            <?php if ($t46VillageView): ?>
                <div id="contentOuterContainer">
                    <div id="content" class="<?=$vars['contentCssClass']; ?>">
                        <?=$vars['content']; ?>
                        <div class="clear"></div>
                    </div>
                </div>
            <?php else: ?>
                <div id="contentOuterContainer" class="contentPage">
                    <div class="contentTitle">
                        <?php if ($vars['showCloseButton']): ?>
                            <a id="closeContentButton" class="contentTitleButton buttonFramed withIcon rectangle green"
                               href="<?=$vars['bodyCssClass'] == 'perspectiveResources' ? 'dorf1.php' : 'dorf2.php'; ?>"
                               title="<?=T("Global", "General.closeWindow"); ?>">
                                <svg viewBox="0 0 20 20"><g class="outline"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"/></g><g class="icon"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"/></g></svg>
                            </a>
                        <?php endif; ?>
                        <?php if ($vars['answerId']): ?>
                            <a id="answersButton" class="contentTitleButton buttonFramed withIcon rectangle green"
                               href="<?=getAnswersUrl(); ?>aid=<?=$vars['answerId']; ?>#go2answer" target="_blank"
                               title="<?=T("Global", "FAQ"); ?>"></a>
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
            <?php endif; ?>
            <div id="sidebarAfterContent" class="sidebar afterContent">
                <?=$vars['sidebarAfterContent']; ?>
            </div>
        </div>
        <div id="footer">
            <!--email_off-->
            <div id="pageLinks">
                <a href="/">Home</a>
                <a href="/docs/">Rules and operations</a>
                <a href="/terms.html">Terms</a>
                <a href="/privacy.html">Privacy</a>
                <?php if ($t46V2): ?><a href="<?=getForumUrl(); ?>" target="_blank"><?=T("inGame", "Forum.Forum"); ?></a><?php endif; ?>
            </div>
            <p class="copyright" style="direction:ltr;">OpenVillage preservation project · Not affiliated with Travian Games.</p>
            <!--/email_off-->
        </div>
    </div>
    <div id="ce"></div>
</div>
<script type="text/javascript">
    var T4_feature_flags = <?=json_encode([
        'vacationMode' => true, 'territory' => false, 'heroitems' => true, 'allianceBonus' => true,
        'boostedStart' => false, 'pushingProtectionAlways' => false, 'tribesEgyptiansAndHuns' => false,
        'hideFoolsArtifacts' => false, 'welcomeScreen' => false,
    ]); ?>;
</script>
<script type="text/javascript">
    // Legends: a cost the village cannot pay yet (lumber, clay, iron, crop) is shown in a red box, updated while stock grows.
    (function () {
        var mark = function () {
            if (typeof resources === 'undefined' || !resources.storage) {
                return;
            }
            var items = document.querySelectorAll('#content .resourceWrapper .resource, #content .resourceWrapper .resources');
            for (var i = 0; i < items.length; i++) {
                var icon = items[i].querySelector('i'), value = items[i].querySelector('.value');
                var match = icon && value && /(?:^|\s)r([1-4])(?:Big)?(?:\s|$)/.exec(icon.className);
                if (!match) {
                    continue;
                }
                var cost = parseInt(value.textContent.replace(/[^0-9]/g, ''), 10);
                var stock = Math.floor(resources.storage['l' + match[1]] || 0);
                items[i].classList.toggle('notEnough', !isNaN(cost) && cost > stock);
            }
        };
        jQuery(function () { mark(); setInterval(mark, 2000); });
    })();
    // Legends: tooltips sit centred under the hovered object or button (not next to the mouse pointer, which on
    // touch screens is not where the finger is), above it when there is no room below.
    (function () {
        var place = function () {
            var tip = Travian.Tip;
            if (!tip || !tip.element || tip.displayState !== 'show' || !tip.elementCurrent || !tip.elementCurrent[0]) {
                return;
            }
            var anchor = tip.elementCurrent[0].getBoundingClientRect();
            if (!anchor.width && !anchor.height) {
                return;
            }
            var box = tip.element[0], gap = 6, pad = 8, hidden = box.style.display === 'none';
            if (hidden) { box.style.visibility = 'hidden'; box.style.display = 'block'; }
            var width = box.offsetWidth, height = box.offsetHeight;
            if (hidden) { box.style.display = 'none'; box.style.visibility = ''; }
            var left = anchor.left + anchor.width / 2 - width / 2;
            left = Math.max(pad, Math.min(left, document.documentElement.clientWidth - width - pad));
            var top = anchor.bottom + gap;
            if (top + height > window.innerHeight - pad && anchor.top - gap - height > pad) {
                top = anchor.top - gap - height;
            }
            tip.element.css({left: left + window.pageXOffset, top: top + window.pageYOffset});
        };
        jQuery(function () {
            if (!window.Travian || !Travian.Tip || typeof Travian.Tip.show !== 'function') {
                return;
            }
            var show = Travian.Tip.show;
            Travian.Tip.show = function () {
                var result = show.apply(this, arguments);
                place();
                setTimeout(place, 150);
                return result;
            };
            // runs after the built-in mousemove handler, which would move the tip next to the pointer again
            jQuery('body').on('mousemove', place);
            window.addEventListener('scroll', place, {passive: true});
        });
    })();
</script>
<script type="text/javascript">
    // Dialogs centred on the page: when only part of the page is visible (phone in portrait, where the
    // 1280 px layout is wider than the screen), centre them in the visible part instead of the whole page.
    (function () {
        if (!window.Travian || !Travian.Dialog || typeof Travian.Dialog.Dialog !== 'function') {
            return;
        }
        var Dialog = Travian.Dialog.Dialog;
        var Patched = function () {
            Dialog.apply(this, arguments);
            var calculate = this.calculatePosition;
            if (typeof calculate !== 'function') {
                return;
            }
            this.calculatePosition = function () {
                var position = calculate.apply(this, arguments);
                var view = window.visualViewport, body = jQuery('body');
                var relative = jQuery(this.options.relativeTo);
                if (!view || view.width >= body.width() - 2 || (relative.length && !relative.is(body))) {
                    return position;
                }
                var width = this.wrapper.outerWidth(), height = this.wrapper.outerHeight();
                this.wrapper.css({position: 'absolute'});
                return {
                    left: Math.max(view.pageLeft + 5, view.pageLeft + (view.width - width) / 2),
                    top: Math.max(view.pageTop + 10, view.pageTop + (view.height - height) / 2)
                };
            };
        };
        Patched.prototype = Dialog.prototype;
        Object.keys(Dialog).forEach(function (key) { Patched[key] = Dialog[key]; });
        Travian.Dialog.Dialog = Patched;
    })();
</script>
</body>
</html>
<!---- This page was generated in <?=round(1000 * (microtime(true) - $GLOBALS['start_time']), 2); ?> ms ---->
