<?php
/*
 * Lobby with the stylesheet, pictures and icons of the Travian Legends lobby (web/public/legends/, fetched by
 * scripts/gpack/fetch-lobby-assets.py, not part of the repository). Included by index.php, which prepares
 * the world data. Markup follows the Legends lobby components so its stylesheet applies unchanged.
 *
 * @var string $error
 * @var bool $ready
 * @var array $world
 * @var float $speed
 * @var int $ageDays
 * @var string $ageShort
 * @var string $mapText
 * @var array $worlds every world of this server (lobby_worlds() + title, speedText, ageDays, ageShort)
 * @var string $gamePath URL path of the chosen world's pages
 */
$icons = json_decode((string)@file_get_contents(__DIR__ . '/legends/icons.json'), true) ?: [];
$svg = static function (string $name, bool $outline = false, string $class = '') use ($icons): string {
    if (!isset($icons[$name])) {
        return '';
    }
    $paths = '';
    foreach ($icons[$name]['paths'] as [$pathClass, $d]) {
        $paths .= '<path' . ($pathClass !== '' ? ' class="' . escape($pathClass) . '"' : '') . ' d="' . escape($d) . '"/>';
    }
    $inner = $outline ? '<g class="outline">' . $paths . '</g><g class="icon">' . $paths . '</g>' : $paths;
    return '<svg' . ($class !== '' ? ' class="' . escape($class) . '"' : '') . ' viewBox="' . escape($icons[$name]['viewBox']) . '">' . $inner . '</svg>';
};
$speedNumber = rtrim(rtrim(number_format($speed, 2, '.', ''), '0'), '.');
$tribeIconsOf = static function (array $of): string {
    $html = '<div class="tribes">';
    foreach (['roman' => 'Romans', 'teuton' => 'Teutons', 'gaul' => 'Gauls', 'egyptian' => 'Egyptians', 'hun' => 'Huns'] as $tribe => $label) {
        if (in_array($tribe, $of['tribes'], true)) {
            $html .= '<i class="tribe ' . $tribe . '" title="' . escape(lt($label)) . '"></i>';
        }
    }
    return $html . '</div>';
};
$tribeIcons = $tribeIconsOf($world);
$titleOf = static function (array $of, string $avatar): string {
    return '<div class="title"><div class="avatarName">' . escape($avatar) . '</div><div class="gameworldName">' . escape($of['title'])
        . '</div><div class="subtitle">' . escape($of['lookName']) . '</div></div>';
};
$backgroundOf = static fn (array $of): string => $of['speed'] > 1 ? 'defaultSpeed' : 'default';
$close = '<button type="button" class="framed green closeButton withIcon" data-close>' . $svg('close', true) . '</button>';
$view = isset($_GET['join']) ? 'join' : 'worlds';
// The Legends stylesheet sets white-space: pre-line, so whitespace between tags would become empty lines
// (the lobby is rendered by React without any); scripts and styles are left as they are.
ob_start(static function (string $html): string {
    $parts = preg_split('~(<(?:script|style)\b.*?</(?:script|style)>)~s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($parts as $i => $part) {
        if ($i % 2 === 0) {
            $parts[$i] = preg_replace('~>\s+<~', '><', $part);
        }
    }
    return implode('', $parts);
});
?>
<!doctype html>
<html lang="<?= lobby_language() ?>" style="--readingDirection: ltr; --readingDirectionFrom: left; --readingDirectionTo: right; --scaleXFactor: 1;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>OpenVillage 4.6 – Lobby</title>
    <link href="/legends/lobby.css" rel="stylesheet">
    <style>
        /* OpenVillage additions: own logo, view switching, small forms in the dialogs */
        /* "Create a new game world" tile on the "Join new gameworld" view: the same framed tile as
           "Join new gameworld" on "Your gameworlds" (lobby.css styles that one under .yourGameworlds only) */
        #newGameworlds .results .joinNewGameworld { cursor: pointer; font-size: 18px; font-weight: 700; height: 150px; max-width: var(--cardMaxWidth); min-width: var(--cardMinWidth); position: relative; width: 100%; box-sizing: border-box; --fontColor: #fffdf8; color: var(--fontColor); align-content: center; align-items: center; backdrop-filter: blur(3.5px); -webkit-backdrop-filter: blur(3.5px); background-image: linear-gradient(180deg, #2c3031d9, #1a1e1fd9 100%); border: 1px solid #34220d; border-radius: 7px; box-shadow: var(--dropShadow, calc(3px * var(--scaleXFactor)) 3px 5px #00000059); column-gap: 30px; display: grid; grid-auto-flow: column; justify-content: center; outline: 1px solid #34220d00; transition: transform .15s ease-out, box-shadow .15s ease-out, border .15s ease-out, outline .15s ease-out; }
        #newGameworlds .results .joinNewGameworld > * { filter: drop-shadow(0 0 2px rgba(27, 26, 26, .8)); }
        #newGameworlds .results .joinNewGameworld svg { height: 2em; width: 2em; fill: currentColor; }
        #newGameworlds .results .joinNewGameworld:hover:not(:active) { --dropShadow: calc(4px * var(--scaleXFactor)) 4px 7px rgba(0, 0, 0, .4); --fontColor: var(--linkHoverColor); outline: 1px solid var(--linkHoverColor); }
        #newGameworlds .results .joinNewGameworld:active { --dropShadow: 0 0 5px 1px rgba(0, 0, 0, .5); --fontColor: var(--linkActiveColor); outline: 1px solid var(--linkActiveColor); }
        header svg.logo { height: 60px; width: 174px; cursor: pointer; }
        header svg.logo .emblem { fill: #b5302b; stroke: #e9e1cf; stroke-width: 1.4; }
        header svg.logo .coin { fill: #c9a254; stroke: #6d4a1d; }
        header svg.logo .mark { fill: #6d4a1d; }
        header svg.logo text { fill: #fff9eb; }
        header svg.logo path { display: inline; }
        @media screen and (max-width: 880px) and (orientation: portrait) { header svg.logo { width: 45px; } header svg.logo text { display: none; } }
        header svg.logo .t1 { font: 700 21px "Noto Serif", Georgia, serif; letter-spacing: .02em; }
        header svg.logo .t2 { font: 400 11px "Noto Sans", Arial, sans-serif; letter-spacing: .25em; }
        body.worlds #newGameworlds, body.join #accountAvatars { display: none; }
        [hidden] { display: none !important; }
        .modal form { display: grid; }
        .modal form p.hint { margin-block: 0 15px; font-size: .95em; }
        .modal form .error { color: var(--negativeColor); font-weight: 700; margin-block-end: 15px; }
        .modal label.check { display: flex; gap: 8px; align-items: flex-start; pointer-events: auto; padding-block-end: 15px; }
        .modal label.check input { margin-top: 2px; width: 18px; height: 18px; }
        .modal label.check a, .modal .links a { color: #5c8a2b; font-weight: 700; text-decoration: none; }
        .modal .links { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-block: 0 15px; }
        .modal form > button { justify-self: center; }
        @media screen and (max-width: 640px) and (orientation: portrait) {
            header .accountMenu .openCloseAccountMenu { max-width: calc(100vw - 150px); }
            header .accountMenu .openCloseAccountMenu span:not(.dot) { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        }
        header .accountMenu .openCloseAccountMenu .dot { width: 10px; height: 10px; border-radius: 50%; background: #91c44a; box-shadow: 0 0 6px #91c44a; }
        header .accountMenu .openCloseAccountMenu .dot.waiting { background: #d8a53d; box-shadow: 0 0 6px #d8a53d; }
        /* footer links under the line without background (also in portrait), separated like on wide screens */
        footer nav { display: flex !important; flex-wrap: wrap; justify-content: center; gap: 6px 0 !important; width: auto !important; }
        footer nav a { background: none !important; border-radius: 0 !important; padding: 0 10px !important; }
        footer nav a:not(:first-of-type) { border-inline-start: 1px solid var(--fontColor) !important; }
        .modal { max-width: calc(100vw - 36px) !important; box-sizing: border-box; }
        /* world picture of the details dialog: as wide as the dialog, cut off instead of overflowing the frame */
        #gameworldDetails .gameworld.details { width: auto; max-width: 100%; overflow: hidden; }
        #gameworldDetails .gameworld.details .background, #gameworldDetails .gameworld.details .background:before, #gameworldDetails .gameworld.details .background:after { background-size: cover; }
    </style>
</head>
<body class="<?= $view ?>">
<div id="root">
    <header>
        <div class="headerContainerStart">
            <nav class="pageMenu">
                <a class="openClosePageMenu closed" data-toggle-menu><?= $svg('menu', true) ?></a>
                <div class="menu" hidden>
                    <a href="/"><span><?= lt('Your gameworlds') ?></span><?= $svg('chevron') ?></a>
                    <a href="/?join=1"><span><?= lt('Join new gameworld') ?></span><?= $svg('chevron') ?></a>
                    <a href="/worlds.php"><span><?= lt('Game worlds') ?></span><?= $svg('chevron') ?></a>
                    <a data-open="login"><span><?= lt('Login') ?></span><?= $svg('chevron') ?></a>
                    <a href="/docs/"><span><?= lt('Rules and operations') ?></span><?= $svg('chevron') ?></a>
                    <a href="?lang=<?= lobby_language() === 'cs' ? 'en' : 'cz' ?>"><span><?= lobby_language() === 'cs' ? 'English' : 'Čeština' ?></span><?= $svg('chevron') ?></a>
                </div>
            </nav>
            <?php if ($view === 'join'): ?>
                <button type="button" class="secondary withText" onclick="location.href='/'"><div><?= $svg('chevron') ?><span><?= lt('Back to your gameworlds') ?></span></div></button>
            <?php else: ?>
                <button type="button" class="framed green withText" onclick="location.href='/?join=1'"><div><?= lt('Join new gameworld') ?></div></button>
            <?php endif; ?>
        </div>
        <svg class="logo" viewBox="0 0 173.48 60" preserveAspectRatio="xMinYMid slice" onclick="location.href='/'" aria-label="OpenVillage">
            <path class="emblem" d="M6 6h30v26c0 11-8 17-15 21C14 49 6 43 6 32z"/>
            <circle class="coin" cx="21" cy="26" r="10"/><path class="mark" d="M16.5 20.5h9v2.4h-3.3v9.6h-2.4v-9.6h-3.3z"/>
            <text class="t1" x="44" y="31">OPENVILLAGE</text>
            <text class="t2" x="46" y="47">LOBBY</text>
        </svg>
        <div class="headerContainerEnd">
            <nav class="accountMenu">
                <a class="openCloseAccountMenu closed" data-open="login"><span class="dot<?= $ready ? '' : ' waiting' ?>"></span><span><?= escape($world['title']) ?></span><?= $svg('chevron', false, 'closed') ?></a>
            </nav>
        </div>
    </header>

    <main id="accountAvatars">
        <h1><?= lt('Your gameworlds') ?></h1>
        <section class="yourGameworlds">
            <?php foreach ($worlds as $w): ?>
            <div class="gameworld owner" data-world="<?= $w['id'] ?>">
                <div class="background <?= $backgroundOf($w) ?>"></div>
                <div class="content">
                    <?= $titleOf($w, 'OpenVillage') ?>
                    <div class="lastActivity"><span><?= escape($w['ageShort']) ?></span><?= $svg('clock', true) ?></div>
                    <button type="button" class="minimal green playNow withText" onclick="location.href='<?= escape($w['path']) ?>dorf1.php'"><div><?= $svg('play', true) ?><span><?= lt('Play now') ?></span></div></button>
                    <div class="actions">
                        <button type="button" class="framed round blue duals withIcon" data-open="register" title="<?= escape(lt('Another account')) ?>"><?= $svg('duals', true) ?></button>
                        <button type="button" class="framed round green showGameworldDetails withIcon" data-open="details" title="<?= escape(lt('Gameworld details')) ?>"><?= $svg('info', true) ?></button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="joinNewGameworld" onclick="location.href='/?join=1'"><?= $svg('plus') ?><span><?= lt('Join new gameworld') ?></span></div>
        </section>
    </main>

    <main id="newGameworlds">
        <h1><?= lt('Join new gameworld') ?></h1>
        <section class="results">
            <?php foreach ($worlds as $w): ?>
            <div class="gameworld new" data-open="details" data-world="<?= $w['id'] ?>">
                <div class="background <?= $backgroundOf($w) ?>"></div>
                <div class="content">
                    <?= $titleOf($w, '') ?>
                    <div class="serverAge" title="<?= escape(lt('Server age in days')) ?>"><i></i><span><?= $w['ageDays'] ?></span></div>
                    <div class="speed" title="<?= escape(lt('Server speed')) ?>"><i></i><span><?= escape($w['speedText']) ?></span></div>
                    <?= $tribeIconsOf($w) ?>
                </div>
                <?php if ($w['id'] === max(array_keys($worlds))): ?>
                <div class="recommended"><?= $svg('recommended') ?><span><?= lt('Recommended') ?></span></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <div class="joinNewGameworld" onclick="location.href='/worlds.php'"><?= $svg('plus') ?><span><?= lt('Create a new game world') ?></span></div>
        </section>
    </main>

    <footer>
        <nav>
            <a href="/"><?= lt('Lobby') ?></a>
            <a href="/docs/"><?= lt('Rules and operations') ?></a>
            <a href="/terms.html"><?= lt('Terms') ?></a>
            <a href="/privacy.html"><?= lt('Privacy') ?></a>
            <a href="/health.php"><?= lt('Health') ?></a>
        </nav>
        <div class="copyright"><?= lt('OpenVillage preservation project · Not affiliated with or endorsed by Travian Games.') ?></div>
    </footer>
</div>

<div id="overlay" data-modal="details" hidden>
    <div id="gameworldDetails" class="modal">
        <?= $close ?>
        <div class="gameworld details">
            <div class="background <?= $backgroundOf($world) ?>"></div>
            <div class="content"><?= $titleOf($world, '') ?><?= $tribeIcons ?></div>
        </div>
        <section class="details hasAction">
            <div class="serverAge"><i></i><span><?= lt('Age:') ?><strong><?= $ageDays ?> <?= lt($ageDays === 1 ? 'day' : 'days') ?></strong></span></div>
            <div class="speed"><i></i><span><?= lt('Speed:') ?><strong>×<?= escape($speedNumber) ?></strong></span></div>
            <div class="playerCount"><i></i><span><?= lt('Players:') ?><strong><?= (int)$world['players'] ?></strong></span></div>
            <div class="map"><i></i><span><?= lt('Map size:') ?><strong><?= escape(lt($mapText)) ?></strong></span></div>
        </section>
        <section class="action">
            <form onsubmit="return false">
                <button type="button" class="framed green withText" data-open="register" <?= $ready ? '' : 'disabled' ?>><div><?= lt('Join gameworld') ?></div></button>
            </form>
        </section>
    </div>
</div>

<div id="overlay" data-modal="register" <?= $error === '' ? 'hidden' : '' ?>>
    <div id="registration" class="modal">
        <?= $close ?>
        <h1><?= lt('Join gameworld') ?></h1>
        <form method="post" action="/register.php">
            <input type="hidden" name="world" value="<?= $world['id'] ?>">
            <?php if ($error !== ''): ?><p class="error"><?= escape(lt($error)) ?></p><?php endif; ?>
            <input type="hidden" name="csrf" value="<?= escape(csrf_token()) ?>">
            <label class="text"><input name="username" minlength="3" maxlength="15" pattern="[A-Za-z0-9 _\-]+" required autocomplete="username"><div class="label"><?= lt('Player name') ?></div></label>
            <label class="text"><input type="email" name="email" maxlength="90" required autocomplete="email"><div class="label"><?= lt('Email') ?></div></label>
            <label class="text"><input type="password" name="password" minlength="10" maxlength="128" required autocomplete="new-password"><div class="label"><?= lt('Password') ?></div></label>
            <label class="check"><input type="checkbox" name="terms" value="1" required><span><?= lt('I accept the <a href="/terms.html">terms</a> and <a href="/privacy.html">privacy notice</a>.') ?></span></label>
            <p class="hint"><?= lt('Next you choose your tribe, the starting sector and the name of your character.') ?></p>
            <div class="links"><a data-open="login"><?= lt('I already have an account – log in') ?></a></div>
            <button type="submit" class="framed green withText" <?= $ready ? '' : 'disabled' ?>><div><?= lt('Continue') ?></div></button>
        </form>
    </div>
</div>

<div id="overlay" data-modal="login" hidden>
    <div id="login" class="modal">
        <?= $close ?>
        <h1><?= lt('Login') ?></h1>
        <form method="post" action="<?= escape($gamePath) ?>login.php">
            <label class="text"><input name="name" required autocomplete="username"><div class="label"><?= lt('Account name or e-mail address') ?></div></label>
            <label class="text"><input type="password" name="password" required autocomplete="current-password"><div class="label"><?= lt('Password') ?></div></label>
            <input type="hidden" name="s1" value="Login"><input type="hidden" name="w" value="">
            <div class="links"><a href="<?= escape($gamePath) ?>login.php?forgotPassword=1"><?= lt('Password forgotten?') ?></a><a data-open="register"><?= lt('Join gameworld') ?></a></div>
            <button type="submit" class="framed green withText"><div><?= lt('Login') ?></div></button>
        </form>
    </div>
</div>

<script>
    (function () {
        document.querySelectorAll('input[name=w]').forEach(function (i) { i.value = screen.width + ':' + screen.height; });
        var registerUrl = <?= json_encode($world['registerUrl']) ?>;   // the look's own registration page (Travian 4.0)
        function open(name) {
            if (name === 'register' && registerUrl) { location.href = registerUrl; return; }
            document.querySelectorAll('[data-modal]').forEach(function (o) { o.hidden = o.getAttribute('data-modal') !== name; });
        }
        document.addEventListener('click', function (event) {
            var toggle = event.target.closest('[data-toggle-menu]');
            if (toggle) {
                var menu = toggle.parentNode.querySelector('.menu');
                menu.hidden = !menu.hidden;
                toggle.classList.toggle('opened', !menu.hidden);
                toggle.classList.toggle('closed', menu.hidden);
                return;
            }
            var opener = event.target.closest('[data-open]');
            var other = event.target.closest('[data-world]');
            if (opener && other && other.getAttribute('data-world') !== '<?= $world['id'] ?>') {
                // another world: reload the lobby with its dialogs
                event.preventDefault();
                location.href = '/?<?= $view === 'join' ? 'join=1&' : '' ?>world=' + other.getAttribute('data-world') + '&open=' + opener.getAttribute('data-open');
                return;
            }
            if (opener) { event.preventDefault(); event.stopPropagation(); open(opener.getAttribute('data-open')); return; }
            if (event.target.closest('[data-close]') || event.target.hasAttribute('data-modal')) {
                event.target.closest('[data-modal]').hidden = true;
            }
        });
        var wanted = new URLSearchParams(location.search).get('open');
        if (wanted === 'worldDialog') wanted = 'details';
        if (wanted && document.querySelector('[data-modal="' + wanted.replace(/[^A-Za-z]/g, '') + '"]')) open(wanted);
        document.addEventListener('keyup', function (event) {
            if (event.key === 'Escape') document.querySelectorAll('[data-modal]').forEach(function (o) { o.hidden = true; });
        });
    })();
</script>
</body>
</html>
