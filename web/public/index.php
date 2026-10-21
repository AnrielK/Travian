<?php

declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';

/*
 * Game lobby in the Travian Legends look: "your game worlds" (the world of this server, play now / details),
 * "join a new game world" (world list -> world details -> registration, then tribe, sector and character name
 * in the game) and the login, in dialogs with the frame and header of the game windows.
 */
$error = isset($_GET['error']) ? trim((string)$_GET['error']) : '';
$ready = false;
$world = ['start' => time(), 'mapSize' => (int)env_value('GAME_MAP_SIZE', '100'), 'players' => 0];
try {
    $database = game_database();
    $config = $database->query('SELECT installed, startTime, map_size FROM config LIMIT 1')->fetch();
    if ($config) {
        $ready = (int)$config['installed'] === 1;
        $world['start'] = (int)$config['startTime'];
        $world['mapSize'] = (int)$config['map_size'];
    }
    $world['players'] = (int)$database->query('SELECT COUNT(*) FROM users WHERE id > 2')->fetchColumn();
} catch (Throwable $exception) {
    $ready = false;
}
$speed = (float)env_value('GAME_SPEED', '1');
$speedText = '×' . rtrim(rtrim(number_format($speed, 2, '.', ''), '0'), '.');
$age = max(0, time() - $world['start']);
$ageDays = intdiv($age, 86400);
$ageShort = $age < 3600 ? '<1h' : ($age < 86400 ? intdiv($age, 3600) . 'h' : ($ageDays > 1 ? '>' . $ageDays . 'd' : '>24h'));
$mapText = $world['mapSize'] <= 50 ? 'Small' : ($world['mapSize'] <= 150 ? 'Medium' : 'Large');
$pack = '/gpack/legends655/mainPage/';
$img = $pack . 'img_ltr/';
$tribes = ['roman', 'teuton', 'gaul', 'egyptian', 'hun'];
$figures = static function () use ($img, $tribes): string {
    $html = '<div class="figures">';
    foreach ($tribes as $tribe) {
        $html .= '<img src="' . escape($img) . 'views/activation/preview/tribe/' . $tribe . '.png" alt="">';
    }
    return $html . '</div>';
};
$view = isset($_GET['join']) ? 'join' : 'worlds';
// Original Legends lobby look when its graphics were fetched (scripts/gpack/fetch-lobby-assets.py).
if (is_file(__DIR__ . '/legends/lobby.css') && !isset($_GET['classic'])) {
    require __DIR__ . '/lobby-legends.php';
    return;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>OpenVillage 4.6 – Lobby</title>
    <meta name="description" content="A local-first preservation server for a T4.6-style strategy game ruleset.">
    <link rel="stylesheet" href="/lobby.css?v=2">
    <style>
        @font-face { font-family: "Noto Serif"; font-weight: 700; src: url('<?= escape($pack) ?>fonts/noto-serif-latin-700-normal.woff2') format("woff2"); }
        body { background-image: linear-gradient(rgba(38, 58, 78, .78), rgba(20, 24, 28, .92) 70%), url('<?= escape($img) ?>themes/default/background/bgBuildings.jpg'); }
        .card .art, .worldDialog .art { background-image: url('<?= escape($img) ?>themes/default/background/bgResources.jpg'); }
        .window:before { background-image: url('<?= escape($img) ?>hud/content/header.jpg'); }
        .window:after { border-image-source: url('<?= escape($img) ?>hud/content/frame.png'); }
        .window .content { background-image: url('<?= escape($img) ?>views/activation/activateBackground.png'); }
    </style>
</head>
<body class="<?= $view ?>">
<header class="bar">
    <button class="menu" type="button" data-open="menuDialog" aria-label="Menu"><span></span><span></span><span></span></button>
    <a class="logo" href="/" aria-label="OpenVillage lobby">
        <svg viewBox="0 0 40 52" aria-hidden="true">
            <path class="emblem" d="M5 6h30v24c0 10-8 16-15 20C13 46 5 40 5 30z"/>
            <circle class="coin" cx="20" cy="24" r="10"/><path class="mark" d="M15.5 18.5h9v2.4h-3.3v9.6h-2.4v-9.6h-3.3z"/>
        </svg>
        <span class="name">OpenVillage<small>Lobby</small></span>
    </a>
    <button class="account" type="button" data-open="loginDialog"><span class="dot<?= $ready ? '' : ' waiting' ?>"></span><span class="label">Local world</span>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9l7 7 7-7"/></svg></button>
</header>

<main>
    <section class="worlds">
        <h1>Your game worlds</h1>
        <div class="grid">
            <article class="card" data-open="worldDialog" tabindex="0">
                <div class="art"></div>
                <?= $figures() ?>
                <div class="caption">
                    <span class="player">OpenVillage</span>
                    <h2>Local world</h2>
                    <span class="sub">Speed <?= escape($speedText) ?></span>
                </div>
                <div class="age"><?= escape($ageShort) ?>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <a class="play" href="/game/dorf1.php"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 2l14 8-14 8z"/></svg>Play now</a>
                <div class="roundButtons">
                    <button class="round blue" type="button" data-open="registerDialog" aria-label="Another account">
                        <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="12" cy="11" r="5"/><path d="M3 26c0-6 4-9 9-9s9 3 9 9z"/><circle cx="22" cy="13" r="4"/><path d="M19 26c0-4 2-7 6-7s5 3 5 7z"/><path class="plus" d="M24 3v8M20 7h8"/></svg>
                    </button>
                    <button class="round info" type="button" data-open="worldDialog" aria-label="Game world details">
                        <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="13"/><path class="i" d="M16 14v9M16 9v1"/></svg>
                    </button>
                </div>
            </article>
            <a class="joinCard" href="/?join=1"><span class="plus"></span><span>Join a new game world</span></a>
        </div>
    </section>

    <section class="join">
        <h1>Join a new game world <a class="back" href="/" aria-label="Back">✕</a></h1>
        <div class="grid">
            <article class="card new" data-open="worldDialog" tabindex="0">
                <div class="art"></div>
                <?= $figures() ?>
                <div class="ribbon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 10h4v11H2zM8 21h9c1 0 2-1 2-2l2-7c0-1-1-2-2-2h-6l1-5c0-1-1-3-3-3L8 10z"/></svg>Recommended</div>
                <div class="caption"><h2>Local world</h2><span class="sub">OpenVillage 4.6</span></div>
                <div class="stat days"><i class="calendar"></i><?= $ageDays ?></div>
                <div class="stat speed"><i class="boot"></i><?= escape($speedText) ?></div>
            </article>
        </div>
    </section>

    <footer>
        <a href="/docs/">Rules and operations</a> · <a href="/terms.html">Terms</a> · <a href="/privacy.html">Privacy</a> · <a href="/health.php">Health</a>
        <p>Independent preservation project. Not affiliated with or endorsed by Travian Games.</p>
    </footer>
</main>

<div class="overlay" id="worldDialog" hidden>
    <section class="worldDialog" role="dialog" aria-modal="true" aria-labelledby="worldTitle">
        <div class="art">
            <h2 id="worldTitle">Local world</h2>
            <button class="close" type="button" data-close aria-label="Close"><svg viewBox="0 0 16 16"><path d="M3 3l10 10M13 3L3 13"/></svg></button>
            <?= $figures() ?>
        </div>
        <div class="stats">
            <div><i class="calendar"></i><span>Age:<b><?= $ageDays ?> <?= $ageDays === 1 ? 'day' : 'days' ?></b></span></div>
            <div><i class="boot"></i><span>Speed:<b><?= escape($speedText) ?></b></span></div>
            <div><i class="players"></i><span>Players:<b><?= $world['players'] ?></b></span></div>
            <div><i class="mapIcon"></i><span>Map size:<b><?= escape($mapText) ?></b></span></div>
        </div>
        <div class="buttons">
            <span class="goldFrame"><button class="green" type="button" data-open="registerDialog" <?= $ready ? '' : 'disabled' ?>>Join the game world</button></span>
            <button class="link" type="button" data-open="loginDialog">I already play here – log in</button>
        </div>
    </section>
</div>

<div class="overlay" id="registerDialog" <?= $error === '' ? 'hidden' : '' ?>>
    <section class="window" role="dialog" aria-modal="true" aria-labelledby="registerTitle">
        <h2 class="title" id="registerTitle">Join the game world</h2>
        <button class="close" type="button" data-close aria-label="Close"><svg viewBox="0 0 16 16"><path d="M3 3l10 10M13 3L3 13"/></svg></button>
        <form class="content" method="post" action="/register.php">
            <?php if ($error !== ''): ?>
                <p class="error"><?= escape($error) ?></p>
            <?php endif; ?>
            <input type="hidden" name="csrf" value="<?= escape(csrf_token()) ?>">
            <label>Player name
                <input name="username" minlength="3" maxlength="15" pattern="[A-Za-z0-9 _\-]+" required autocomplete="username">
            </label>
            <label>Email
                <input type="email" name="email" maxlength="90" required autocomplete="email">
            </label>
            <label>Password
                <input type="password" name="password" minlength="10" maxlength="128" required autocomplete="new-password">
            </label>
            <label class="check">
                <input type="checkbox" name="terms" value="1" required>
                <span>I accept the <a href="/terms.html">terms</a> and <a href="/privacy.html">privacy notice</a>.</span>
            </label>
            <p class="hint"><i>i</i>Next you choose your tribe, the starting sector and the name of your character.</p>
            <div class="buttons">
                <span class="goldFrame"><button class="green" type="submit" <?= $ready ? '' : 'disabled' ?>>Continue</button></span>
                <button class="link" type="button" data-open="loginDialog">I already have an account – log in</button>
            </div>
        </form>
    </section>
</div>

<div class="overlay" id="loginDialog" hidden>
    <section class="window" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
        <h2 class="title" id="loginTitle">Login</h2>
        <button class="close" type="button" data-close aria-label="Close"><svg viewBox="0 0 16 16"><path d="M3 3l10 10M13 3L3 13"/></svg></button>
        <form class="content" method="post" action="/game/login.php">
            <label>Account name or e-mail address
                <input name="name" required autocomplete="username">
            </label>
            <label>Password
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <input type="hidden" name="s1" value="Login"><input type="hidden" name="w" value="">
            <p class="hint"><i>i</i>Log in with the account you created on this game world.</p>
            <div class="buttons">
                <span class="goldFrame"><button class="green" type="submit">Login</button></span>
                <a class="link" href="/game/login.php?forgotPassword=1">Password forgotten?</a>
                <button class="link" type="button" data-open="registerDialog">No account yet – join the game world</button>
            </div>
        </form>
    </section>
</div>

<div class="overlay" id="menuDialog" hidden>
    <nav class="sideMenu" aria-label="Menu">
        <button class="close" type="button" data-close aria-label="Close"><svg viewBox="0 0 16 16"><path d="M3 3l10 10M13 3L3 13"/></svg></button>
        <a href="/">Your game worlds</a>
        <a href="/?join=1">Join a new game world</a>
        <button type="button" data-open="loginDialog">Login</button>
        <a href="/docs/">Rules and operations</a>
        <a href="/terms.html">Terms</a>
        <a href="/privacy.html">Privacy</a>
    </nav>
</div>

<script>
    (function () {
        document.querySelectorAll('input[name=w]').forEach(function (input) { input.value = screen.width + ':' + screen.height; });
        function open(id) {
            document.querySelectorAll('.overlay').forEach(function (o) { o.hidden = o.id !== id; });
            var field = document.querySelector('#' + id + ' input:not([type=hidden])');
            if (field && window.matchMedia('(pointer: fine)').matches) field.focus();
        }
        document.addEventListener('click', function (event) {
            if (event.target.closest('a.play')) return;
            var opener = event.target.closest('[data-open]');
            if (opener) {
                event.preventDefault();
                open(opener.getAttribute('data-open'));
                return;
            }
            if (event.target.closest('[data-close]') || event.target.classList.contains('overlay')) {
                event.target.closest('.overlay').hidden = true;
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') document.querySelectorAll('.overlay').forEach(function (o) { o.hidden = true; });
            if (event.key === 'Enter' && event.target.matches('.card')) open('worldDialog');
        });
    })();
</script>
</body>
</html>
