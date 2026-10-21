<?php
/*
 * Login of the game world in the look of the Travian Legends lobby (stylesheet /legends/lobby.css, fetched by
 * scripts/gpack/fetch-lobby-assets.py): lobby header with the logo, the login form in a lobby dialog.
 */
$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
// the lobby stylesheet uses white-space: pre-line, whitespace between tags would become empty lines
ob_start(static fn(string $html): string => (string)preg_replace('~>\s+<~', '><', $html));
?><!doctype html>
<html lang="en" style="--readingDirection: ltr; --readingDirectionFrom: left; --readingDirectionTo: right; --scaleXFactor: 1;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>OpenVillage – <?=$e(T('Login', 'Login')); ?></title>
    <link href="/legends/lobby.css" rel="stylesheet">
    <style>
        header svg.logo { height: 60px; width: 174px; cursor: pointer; }
        header svg.logo path { display: inline; }
        header svg.logo .emblem { fill: #b5302b; stroke: #e9e1cf; stroke-width: 1.4; }
        header svg.logo .coin { fill: #c9a254; stroke: #6d4a1d; }
        header svg.logo .mark { fill: #6d4a1d; }
        header svg.logo text { fill: #fff9eb; }
        header svg.logo .t1 { font: 700 21px "Noto Serif", Georgia, serif; }
        header svg.logo .t2 { font: 400 11px "Noto Sans", Arial, sans-serif; letter-spacing: .25em; }
        #lobbyLogin { display: grid; justify-items: center; padding-block-start: 40px; }
        #lobbyLogin .modal { box-shadow: 0 0 50px #0009; width: min(450px, 100vw - 36px); box-sizing: border-box; }
        #lobbyLogin label.text, #lobbyLogin label.text input { width: 100%; box-sizing: border-box; }
        #lobbyLogin form { display: grid; }
        #lobbyLogin p.welcome { margin-block-end: 15px; }
        #lobbyLogin .error { color: var(--negativeColor); font-weight: 700; margin-block-end: 15px; }
        #lobbyLogin .links { display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-block: 0 20px; }
        #lobbyLogin .links a { color: #5c8a2b; font-weight: 700; text-decoration: none; }
        #lobbyLogin form > button { justify-self: center; }
    </style>
</head>
<body>
<div id="root">
    <header class="logoOnly">
        <svg class="logo" viewBox="0 0 173.48 60" onclick="location.href='/'" aria-label="OpenVillage">
            <path class="emblem" d="M6 6h30v26c0 11-8 17-15 21C14 49 6 43 6 32z"/>
            <circle class="coin" cx="21" cy="26" r="10"/><path class="mark" d="M16.5 20.5h9v2.4h-3.3v9.6h-2.4v-9.6h-3.3z"/>
            <text class="t1" x="44" y="31">OPENVILLAGE</text>
            <text class="t2" x="46" y="47">LOBBY</text>
        </svg>
    </header>
    <main id="lobbyLogin">
        <div class="modal">
            <h1><?=$e(T('Login', 'Login')); ?></h1>
            <form method="post" action="login.php">
                <p class="welcome"><?=$e($vars['welcome'] ?? ''); ?></p>
                <?php if (($vars['error'] ?? '') !== ''): ?><p class="error"><?=$e($vars['error']); ?></p><?php endif; ?>
                <label class="text"><input name="name" value="<?=$e($vars['name'] ?? ''); ?>" required autocomplete="username"><div class="label"><?=$e(T('Login', 'accountNameOrEmailAddress')); ?></div></label>
                <label class="text"><input type="password" name="password" required autocomplete="current-password"><div class="label"><?=$e(T('Login', 'pass')); ?></div></label>
                <input type="hidden" name="s1" value="Login"><input type="hidden" name="w" value="">
                <div class="links">
                    <a href="login.php?forgotPassword=1"><?=$e(trim(T('Login', 'PasswordForgotten?'))); ?></a>
                    <a href="/?join=1">Join gameworld</a>
                </div>
                <button type="submit" class="framed green withText"><div><?=$e(T('Login', 'Login')); ?></div></button>
            </form>
        </div>
    </main>
    <footer>
        <nav><a href="/">Lobby</a><a href="/docs/">Rules and operations</a><a href="/terms.html">Terms</a><a href="/privacy.html">Privacy</a></nav>
        <div class="copyright">OpenVillage preservation project · Not affiliated with or endorsed by Travian Games.</div>
    </footer>
</div>
<script>document.querySelector('input[name=w]').value = screen.width + ':' + screen.height;</script>
</body>
</html>
<?php ob_end_flush(); ?>
