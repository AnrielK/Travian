<?php
/*
 * Activation (tribe, starting sector, character name) with the markup of the Travian Legends activation page,
 * styled by the Legends stylesheet of the graphic pack (css/legends_ltr.css, .activateWithGameLobbyAccount).
 * The three steps are switched in the page; the form posts tribe, sector and name to activate.php?page=legends.
 */
$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pack = get_gpack_cdn_mainPage_url();
$order = array_values(array_intersect([3, 1, 2, 6, 7, 8, 9], $vars['tribes']));
$tribe = in_array((int)$vars['tribe'], $order, true) ? (int)$vars['tribe'] : 3;
$sector = in_array($vars['sector'], ['nw', 'no', 'sw', 'so'], true) ? $vars['sector'] : 'sw';
$sectors = ['nw' => 'North - West', 'no' => 'North - East', 'sw' => 'South - West', 'so' => 'South - East'];
$step = $vars['step'] === 'confirm' ? 'confirm' : 'selectTribe';
$titles = [
    'selectTribe' => T('ActivationNew', 'Select your tribe'),
    'selectSector' => T('ActivationNew', 'selectStartingSector', 'Select starting sector'),
    'confirm' => T('ActivationNew', 'confirmChoice', 'Confirm your choice'),
];
$change = T('ActivationNew', 'change', 'Change');
$button = static fn(string $id, string $text, string $type = 'button') =>
    '<button type="' . $type . '" value="' . $e($text) . '" id="' . $id . '" class="textButtonV2 green buttonFramed withText rectangle"><div>' . $e($text) . '</div></button>';
$logo = '<svg viewBox="0 0 172.7 60" aria-label="OpenVillage"><path class="ovEmblem" d="M6 6h30v26c0 11-8 17-15 21C14 49 6 43 6 32z"/>'
    . '<circle class="ovCoin" cx="21" cy="26" r="10"/><path class="ovMark" d="M16.5 20.5h9v2.4h-3.3v9.6h-2.4v-9.6h-3.3z"/>'
    . '<text class="ovTitle" x="44" y="31">OPENVILLAGE</text><text class="ovSub" x="45" y="45">L E G E N D S</text></svg>';
?><!DOCTYPE html>
<html id="mainLayout" lang="en" style="--readingDirection: ltr; --readingDirectionFrom: left; --readingDirectionTo: right; --scaleXFactor: 1;">
<head>
    <title>OpenVillage – <?=$e($titles[$step]); ?></title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <script>
        // The Legends mobile layout is 620 px wide: phones in portrait show it scaled to the screen width.
        (function () {
            var meta = document.querySelector('meta[name=viewport]');
            function fit() {
                var portrait = window.matchMedia('(orientation: portrait)').matches;
                var narrow = Math.min(screen.width, screen.height) < 620;
                meta.setAttribute('content', portrait && narrow ? 'width=620' : 'width=device-width, initial-scale=1');
            }
            fit();
            window.addEventListener('orientationchange', function () { setTimeout(fit, 100); });
        })();
    </script>
    <meta name="theme-color" content="#F4EFE4"/>
    <link href="<?=$e($pack); ?>css/legends_ltr.css" rel="stylesheet" type="text/css"/>
    <style>
        body.activate { background: #4f6531 url('<?=$e($pack); ?>img_ltr/themes/default/background/bgBuildings.jpg') center top / cover fixed; }
        #logo svg .ovEmblem { fill: #b5302b; stroke: #f2f2f2; stroke-width: 1.4; }
        #logo svg .ovCoin { fill: #e5b75b; stroke: #6d4a1d; }
        #logo svg .ovMark { fill: #6d4a1d; }
        #logo svg text { fill: #f2f2f2; stroke: none; }
        #logo svg .ovTitle { font: 700 16px "Noto Serif", Georgia, serif; }
        #logo svg .ovSub { font: 600 9px Arial, sans-serif; }
        #content.activate .ovError { margin: 0 0 10px; padding: 8px 10px; border: 1px solid #c4544a; background: #f6d9d3; color: #8a2219; border-radius: 3px; font-weight: 700; }
    </style>
</head>
<body class="activate ltr mobileOptimized" data-theme="default" data-village-perspective="perspectiveBuildings">
<div id="background">
    <div id="topBar">
        <a id="logo" href="/" title="OpenVillage"><?=$logo; ?></a>
        <div id="header">
            <input type="checkbox" id="mobileMenuState"/>
            <div id="navigation"><label class="mobileMenuButton" for="mobileMenuState"></label></div>
            <nav id="mobileMenu" class="mobileMenuActivate">
                <ul>
                    <li class="lobbyItem"><a class="lobby" href="/">LOBBY</a></li>
                    <li class="loginItem"><a class="login" href="login.php">LOGIN</a></li>
                    <li><a class="support" href="/docs/">RULES</a></li>
                </ul>
                <p class="copyright">OpenVillage preservation project</p>
            </nav>
        </div>
    </div>

    <div id="center">
        <div id="sidebarBeforeContent" class="sidebar beforeContent"><div class="sidebarBoxWrapper"></div></div>
        <div id="contentOuterContainer" class=" contentPage">
            <div class="contentTitle buttonCount0"></div>
            <div class="contentContainer">
                <div id="content" class="activate">
                    <div class="dynamicTitles">
                        <?php foreach ($titles as $class => $title): ?>
                            <div class="<?=$class; ?><?=$class === $step ? ' active' : ''; ?>"><h1 class="titleInHeader"><?=$e($title); ?></h1></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mobileCover"></div>
                    <div class="activationWrapper activateWithGameLobbyAccount <?=$step; ?>">
                        <form class="formV2" method="post" id="activate" action="activate.php?page=legends">
                            <div class="steps">
                                <div class="stepDescription selectTribe"><?=$e(T('ActivationNew', 'vidSelectDescription')); ?></div>
                                <div class="selection selectTribe">
                                    <div class="ornamentStart"></div>
                                    <?php foreach ($order as $i => $id): ?>
                                        <input type="radio" id="tribe_<?=$id; ?>" name="tribe" value="<?=$id; ?>" class="option<?=$i; ?>"<?=$id === $tribe ? ' checked' : ''; ?>/>
                                        <label for="tribe_<?=$id; ?>" class="tribe<?=$id; ?>" title="<?=$e(T('Global', 'races.' . $id)); ?>"></label>
                                    <?php endforeach; ?>
                                    <div class="ornamentEnd"></div>
                                    <div class="description tribeDescription">
                                        <?php foreach ($order as $i => $id): ?>
                                            <div class="optionDescription option<?=$i; ?> tribe<?=$id; ?>">
                                                <?php if ($id === 3): ?>
                                                    <div class="recommended"><span class="text"><?=$e(T('ActivationNew', 'recommendedForNewPlayers')); ?></span></div>
                                                <?php endif; ?>
                                                <h2><?=$e(T('Global', 'races.' . $id)); ?></h2>
                                                <ul>
                                                    <?php foreach ((array)T('ActivationNew', 'race' . $id . '_attributes') as $text): ?>
                                                        <li><?=$e($text); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div id="selectionIndicator"></div>
                                </div>
                                <div class="buttonWrapper selectTribe"><?=$button('selectTribe', T('ActivationNew', 'Confirm')); ?></div>

                                <div class="stepDescription selectSector"><?=$e(T('ActivationNew', 'sectorSelectDescription')); ?></div>
                                <div class="selectSector mapWrapper">
                                    <div id="activationMapContainer">
                                        <div id="map" class="tribe<?=$tribe; ?>">
                                            <?php foreach ($sectors as $s => $label): ?>
                                                <input type="radio" name="sector" value="<?=$s; ?>" id="sector_<?=$s; ?>"<?=$s === $sector ? ' checked="checked"' : ''; ?>/>
                                                <label for="sector_<?=$s; ?>"<?=$s === 'sw' ? ' data-text="' . $e(T('ActivationNew', 'RECOMMENDED')) . '"' : ''; ?>><?=$e(T('ActivationNew', $label)); ?></label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="buttonWrapper selectSector"><?=$button('selectSector', T('ActivationNew', 'Confirm')); ?></div>

                                <div class="stepDescription confirm"><?=$e(T('ActivationNew', 'confirmDescription', 'Confirm your choice, choose the name of your character and start your adventure.')); ?></div>
                                <div class="selection confirm">
                                    <?php foreach ($order as $id): ?>
                                        <div class="confirmTribe tribe<?=$id; ?><?=$id === $tribe ? ' selected' : ''; ?>">
                                            <h2><?=$e(T('Global', 'races.' . $id)); ?></h2><a class="change backToTribe"><?=$e($change); ?></a>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php foreach ($sectors as $s => $label): ?>
                                        <div class="confirmSector <?=$s; ?><?=$s === $sector ? ' selected' : ''; ?>">
                                            <h2><?=$e(T('ActivationNew', $label)); ?></h2><a class="change backToSector"><?=$e($change); ?></a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="avatarName confirm">
                                    <?php if ($vars['error'] !== ''): ?><p class="ovError"><?=$e($vars['error']); ?></p><?php endif; ?>
                                    <label class="input fixed">
                                        <input type="text" name="name" maxlength="15" minlength="3" pattern="[A-Za-z0-9 _\-]+" required
                                               placeholder="<?=$e(T('ActivationNew', 'characterNamePlaceholder', 'Character name')); ?>" value="<?=$e($vars['name']); ?>">
                                        <div class="label"><?=$e(T('ActivationNew', 'characterName', 'Enter the name of your character:')); ?></div>
                                    </label>
                                    <div class="avatarInfo">
                                        <svg viewBox="0 0 20 20"><path d="M9 15h2V9H9v6zm1-8c.3 0 .5-.1.7-.3s.3-.4.3-.7-.1-.5-.3-.7c-.2-.2-.4-.3-.7-.3s-.5.1-.7.3c-.2.2-.3.4-.3.7s.1.5.3.7.4.3.7.3zm0 13C4.5 20 0 15.5 0 10S4.5 0 10 0s10 4.5 10 10-4.5 10-10 10zm0-2c4.4 0 8-3.6 8-8s-3.6-8-8-8-8 3.6-8 8 3.6 8 8 8z"></path></svg>
                                        <span><?=$e(T('ActivationNew', 'characterNameInfo', 'This is the name of your character on the game world.')); ?></span>
                                    </div>
                                </div>
                                <div class="buttonWrapper confirm"><?=$button('confirm', T('ActivationNew', 'letsPlay', "Let's play"), 'submit'); ?></div>
                            </div>
                            <input type="hidden" name="w" value="">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="sidebarAfterContent" class="sidebar afterContent"><div class="sidebarBoxWrapper"></div></div>
    </div>

    <div id="footer">
        <div id="pageLinks">
            <a href="/">Lobby</a>
            <a href="/docs/">Rules and operations</a>
            <a href="/terms.html">Terms</a>
            <a href="/privacy.html">Privacy</a>
            <div class="clear"></div>
        </div>
        <p class="copyright">OpenVillage preservation project · Not affiliated with Travian Games.</p>
    </div>
</div>
<script>
    (function () {
        var wrapper = document.querySelector('.activationWrapper');
        var form = document.getElementById('activate');
        var steps = ['selectTribe', 'selectSector', 'confirm'];
        form.elements.w.value = screen.width + ':' + screen.height;
        function checked(name) { var i = form.querySelector('input[name=' + name + ']:checked'); return i ? i.value : null; }
        function show(step) {
            steps.forEach(function (s) { wrapper.classList.toggle(s, s === step); });
            document.querySelectorAll('.dynamicTitles > div').forEach(function (d) { d.classList.toggle('active', d.classList.contains(step)); });
            var tribe = checked('tribe'), sector = checked('sector');
            document.getElementById('map').className = 'tribe' + tribe;
            document.querySelectorAll('.confirmTribe').forEach(function (d) { d.classList.toggle('selected', d.classList.contains('tribe' + tribe)); });
            document.querySelectorAll('.confirmSector').forEach(function (d) { d.classList.toggle('selected', d.classList.contains(sector)); });
            document.title = 'OpenVillage – ' + document.querySelector('.dynamicTitles > div.' + step + ' h1').textContent;
        }
        function indicator() {
            var label = form.querySelector('input[name=tribe]:checked + label'), box = document.getElementById('selectionIndicator');
            if (label && box) box.style.left = (label.offsetLeft + label.offsetWidth / 2) + 'px';
        }
        document.getElementById('selectTribe').addEventListener('click', function () { show('selectSector'); });
        document.getElementById('selectSector').addEventListener('click', function () { show('confirm'); });
        document.querySelectorAll('.backToTribe').forEach(function (a) { a.addEventListener('click', function () { show('selectTribe'); }); });
        document.querySelectorAll('.backToSector').forEach(function (a) { a.addEventListener('click', function () { show('selectSector'); }); });
        form.querySelectorAll('input[name=tribe]').forEach(function (i) { i.addEventListener('change', indicator); });
        window.addEventListener('resize', indicator);
        indicator();
        show('<?=$step; ?>');
    })();
</script>
</body>
</html>
