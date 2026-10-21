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
$background = $speed > 1 ? 'defaultSpeed' : 'default';
$tribeIcons = '<div class="tribes">';
foreach (['roman' => 'Romans', 'teuton' => 'Teutons', 'gaul' => 'Gauls', 'egyptian' => 'Egyptians', 'hun' => 'Huns'] as $tribe => $label) {
    $tribeIcons .= '<i class="tribe ' . $tribe . '" title="' . $label . '"></i>';
}
$tribeIcons .= '</div>';
$title = '<div class="title"><div class="avatarName">%s</div><div class="gameworldName">Local world</div><div class="subtitle">OpenVillage 4.6</div></div>';
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
<html lang="en" style="--readingDirection: ltr; --readingDirectionFrom: left; --readingDirectionTo: right; --scaleXFactor: 1;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>OpenVillage 4.6 – Lobby</title>
    <link href="/legends/lobby.css" rel="stylesheet">
    <style>
        /* OpenVillage additions: own logo, view switching, small forms in the dialogs */
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
    </style>
</head>
<body class="<?= $view ?>">
<div id="root">
    <header>
        <div class="headerContainerStart">
            <nav class="pageMenu">
                <a class="openClosePageMenu closed" data-toggle-menu><?= $svg('menu', true) ?></a>
                <div class="menu" hidden>
                    <a href="/"><span>Your gameworlds</span><?= $svg('chevron') ?></a>
                    <a href="/?join=1"><span>Join new gameworld</span><?= $svg('chevron') ?></a>
                    <a data-open="login"><span>Login</span><?= $svg('chevron') ?></a>
                    <a href="/docs/"><span>Rules and operations</span><?= $svg('chevron') ?></a>
                </div>
            </nav>
            <?php if ($view === 'join'): ?>
                <button type="button" class="secondary withText" onclick="location.href='/'"><div><?= $svg('chevron') ?><span>Back to your gameworlds</span></div></button>
            <?php else: ?>
                <button type="button" class="framed green withText" onclick="location.href='/?join=1'"><div>Join new gameworld</div></button>
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
                <a class="openCloseAccountMenu closed" data-open="login"><span class="dot<?= $ready ? '' : ' waiting' ?>"></span><span>Local world</span><?= $svg('chevron', false, 'closed') ?></a>
            </nav>
        </div>
    </header>

    <main id="accountAvatars">
        <h1>Your gameworlds</h1>
        <section class="yourGameworlds">
            <div class="gameworld owner">
                <div class="background <?= $background ?>"></div>
                <div class="content">
                    <?= sprintf($title, 'OpenVillage') ?>
                    <div class="lastActivity"><span><?= escape($ageShort) ?></span><?= $svg('clock', true) ?></div>
                    <button type="button" class="minimal green playNow withText" onclick="location.href='/game/dorf1.php'"><div><?= $svg('play', true) ?><span>Play now</span></div></button>
                    <div class="actions">
                        <button type="button" class="framed round blue duals withIcon" data-open="register" title="Another account"><?= $svg('duals', true) ?></button>
                        <button type="button" class="framed round green showGameworldDetails withIcon" data-open="details" title="Gameworld details"><?= $svg('info', true) ?></button>
                    </div>
                </div>
            </div>
            <div class="joinNewGameworld" onclick="location.href='/?join=1'"><?= $svg('plus') ?><span>Join new gameworld</span></div>
        </section>
    </main>

    <main id="newGameworlds">
        <h1>Join new gameworld</h1>
        <section class="results">
            <div class="gameworld new" data-open="details">
                <div class="background <?= $background ?>"></div>
                <div class="content">
                    <?= sprintf($title, '') ?>
                    <div class="serverAge" title="Server age in days"><i></i><span><?= $ageDays ?></span></div>
                    <div class="speed" title="Server speed"><i></i><span>×<?= escape($speedNumber) ?></span></div>
                    <?= $tribeIcons ?>
                </div>
                <div class="recommended"><?= $svg('recommended') ?><span>Recommended</span></div>
            </div>
        </section>
    </main>

    <footer>
        <nav>
            <a href="/">Lobby</a>
            <a href="/docs/">Rules and operations</a>
            <a href="/terms.html">Terms</a>
            <a href="/privacy.html">Privacy</a>
            <a href="/health.php">Health</a>
        </nav>
        <div class="copyright">OpenVillage preservation project · Not affiliated with or endorsed by Travian Games.</div>
    </footer>
</div>

<div id="overlay" data-modal="details" hidden>
    <div id="gameworldDetails" class="modal">
        <?= $close ?>
        <div class="gameworld details">
            <div class="background <?= $background ?>"></div>
            <div class="content"><?= sprintf($title, '') ?><?= $tribeIcons ?></div>
        </div>
        <section class="details hasAction">
            <div class="serverAge"><i></i><span>Age:<strong><?= $ageDays ?> <?= $ageDays === 1 ? 'day' : 'days' ?></strong></span></div>
            <div class="speed"><i></i><span>Speed:<strong>×<?= escape($speedNumber) ?></strong></span></div>
            <div class="playerCount"><i></i><span>Players:<strong><?= (int)$world['players'] ?></strong></span></div>
            <div class="map"><i></i><span>Map size:<strong><?= escape($mapText) ?></strong></span></div>
        </section>
        <section class="action">
            <form onsubmit="return false">
                <button type="button" class="framed green withText" data-open="register" <?= $ready ? '' : 'disabled' ?>><div>Join gameworld</div></button>
            </form>
        </section>
    </div>
</div>

<div id="overlay" data-modal="register" <?= $error === '' ? 'hidden' : '' ?>>
    <div id="registration" class="modal">
        <?= $close ?>
        <h1>Join gameworld</h1>
        <form method="post" action="/register.php">
            <?php if ($error !== ''): ?><p class="error"><?= escape($error) ?></p><?php endif; ?>
            <input type="hidden" name="csrf" value="<?= escape(csrf_token()) ?>">
            <label class="text"><input name="username" minlength="3" maxlength="15" pattern="[A-Za-z0-9 _\-]+" required autocomplete="username"><div class="label">Player name</div></label>
            <label class="text"><input type="email" name="email" maxlength="90" required autocomplete="email"><div class="label">Email</div></label>
            <label class="text"><input type="password" name="password" minlength="10" maxlength="128" required autocomplete="new-password"><div class="label">Password</div></label>
            <label class="check"><input type="checkbox" name="terms" value="1" required><span>I accept the <a href="/terms.html">terms</a> and <a href="/privacy.html">privacy notice</a>.</span></label>
            <p class="hint">Next you choose your tribe, the starting sector and the name of your character.</p>
            <div class="links"><a data-open="login">I already have an account – log in</a></div>
            <button type="submit" class="framed green withText" <?= $ready ? '' : 'disabled' ?>><div>Continue</div></button>
        </form>
    </div>
</div>

<div id="overlay" data-modal="login" hidden>
    <div id="login" class="modal">
        <?= $close ?>
        <h1>Login</h1>
        <form method="post" action="/game/login.php">
            <label class="text"><input name="name" required autocomplete="username"><div class="label">Account name or e-mail address</div></label>
            <label class="text"><input type="password" name="password" required autocomplete="current-password"><div class="label">Password</div></label>
            <input type="hidden" name="s1" value="Login"><input type="hidden" name="w" value="">
            <div class="links"><a href="/game/login.php?forgotPassword=1">Password forgotten?</a><a data-open="register">Join gameworld</a></div>
            <button type="submit" class="framed green withText"><div>Login</div></button>
        </form>
    </div>
</div>

<script>
    (function () {
        document.querySelectorAll('input[name=w]').forEach(function (i) { i.value = screen.width + ':' + screen.height; });
        function open(name) {
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
            if (opener) { event.preventDefault(); event.stopPropagation(); open(opener.getAttribute('data-open')); return; }
            if (event.target.closest('[data-close]') || event.target.hasAttribute('data-modal')) {
                event.target.closest('[data-modal]').hidden = true;
            }
        });
        document.addEventListener('keyup', function (event) {
            if (event.key === 'Escape') document.querySelectorAll('[data-modal]').forEach(function (o) { o.hidden = true; });
        });
    })();
</script>
</body>
</html>
