<?php

declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';

/*
 * Game lobby in the Travian Legends look: the game world of this server as a card (play / world details) and
 * "join a new game world", which opens the registration (then tribe, sector and character name in the game).
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
$ageDays = max(0, intdiv(time() - $world['start'], 86400));
$mapText = $world['mapSize'] <= 50 ? 'Small' : ($world['mapSize'] <= 150 ? 'Medium' : 'Large');
$mapText .= ' (' . ($world['mapSize'] * 2 + 1) . '×' . ($world['mapSize'] * 2 + 1) . ')';
$img = '/gpack/legends655/mainPage/img_ltr/';
$tribes = ['roman', 'teuton', 'gaul', 'egyptian', 'hun'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>OpenVillage 4.6</title>
    <meta name="description" content="A local-first preservation server for a T4.6-style strategy game ruleset.">
    <link rel="stylesheet" href="/lobby.css?v=1">
    <style>
        .world .art { background-image: url('<?= escape($img) ?>themes/default/background/bgResources.jpg'); }
        .dialog .art { background-image: url('<?= escape($img) ?>themes/default/background/bgResources.jpg'); }
    </style>
</head>
<body>
<header class="bar">
    <a class="logo" href="/" aria-label="OpenVillage">
        <svg viewBox="0 0 40 52" aria-hidden="true">
            <path class="emblem" d="M5 6h30v24c0 10-8 16-15 20C13 46 5 40 5 30z"/>
            <circle class="coin" cx="20" cy="24" r="10"/><path class="mark" d="M15.5 18.5h9v2.4h-3.3v9.6h-2.4v-9.6h-3.3z"/>
        </svg>
    </a>
    <div class="account"><span class="dot<?= $ready ? '' : ' waiting' ?>"></span><?= $ready ? 'Local world ready' : 'Preparing the world…' ?></div>
</header>

<main>
    <h1>Your game worlds</h1>
    <article class="world" tabindex="0" data-open="worldDialog">
        <div class="art"></div>
        <div class="figures">
            <?php foreach ($tribes as $tribe): ?>
                <img src="<?= escape($img) ?>views/activation/preview/tribe/<?= $tribe ?>.png" alt="">
            <?php endforeach; ?>
        </div>
        <div class="caption">
            <span class="sub">OpenVillage</span>
            <h2>Local world</h2>
            <span class="sub">Speed <?= escape($speedText) ?></span>
        </div>
        <div class="age"><?= $ageDays ?> <?= $ageDays === 1 ? 'day' : 'days' ?>
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <a class="play" href="/game/login.php"><span>▶</span> Play now</a>
        <button class="round info" type="button" data-open="worldDialog" aria-label="Game world details">i</button>
    </article>

    <button class="join" type="button" data-open="registerDialog" <?= $ready ? '' : 'disabled' ?>>
        <span class="plus">+</span> Join a new game world
    </button>

    <footer>
        <a href="/docs/">Rules and operations</a> · <a href="/terms.html">Terms</a> · <a href="/privacy.html">Privacy</a> · <a href="/health.php">Health</a>
        <p>Independent preservation project. Not affiliated with or endorsed by Travian Games.</p>
    </footer>
</main>

<div class="overlay" id="worldDialog" hidden>
    <section class="dialog" role="dialog" aria-modal="true" aria-labelledby="worldTitle">
        <div class="art">
            <h2 id="worldTitle">Local world</h2>
            <button class="close" type="button" data-close aria-label="Close">✕</button>
            <div class="figures">
                <?php foreach ($tribes as $tribe): ?>
                    <img src="<?= escape($img) ?>views/activation/preview/tribe/<?= $tribe ?>.png" alt="">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="stats">
            <div><i class="calendar"></i><span>Age:<b><?= $ageDays ?> <?= $ageDays === 1 ? 'day' : 'days' ?></b></span></div>
            <div><i class="boot"></i><span>Speed:<b><?= escape($speedText) ?></b></span></div>
            <div><i class="players"></i><span>Players:<b><?= $world['players'] ?></b></span></div>
            <div><i class="mapIcon"></i><span>Map size:<b><?= escape($mapText) ?></b></span></div>
        </div>
        <div class="buttons">
            <button class="green" type="button" data-open="registerDialog" <?= $ready ? '' : 'disabled' ?>>Join the game world</button>
            <a class="link" href="/game/login.php">I already play here – log in</a>
        </div>
    </section>
</div>

<div class="overlay" id="registerDialog" <?= $error === '' ? 'hidden' : '' ?>>
    <section class="dialog" role="dialog" aria-modal="true" aria-labelledby="registerTitle">
        <div class="head">
            <h2 id="registerTitle">Join the game world</h2>
            <button class="close" type="button" data-close aria-label="Close">✕</button>
        </div>
        <form method="post" action="/register.php" class="form">
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
            <p class="hint">Next you choose your tribe, the starting sector and the name of your character.</p>
            <div class="buttons"><button class="green" type="submit" <?= $ready ? '' : 'disabled' ?>>Continue</button></div>
        </form>
    </section>
</div>

<script>
    (function () {
        function open(id) {
            document.querySelectorAll('.overlay').forEach(function (o) { o.hidden = o.id !== id; });
            var field = document.querySelector('#' + id + ' input:not([type=hidden])');
            if (field && window.matchMedia('(pointer: fine)').matches) field.focus();
        }
        document.addEventListener('click', function (event) {
            var opener = event.target.closest('[data-open]');
            if (opener && !event.target.closest('a.play')) {
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
            if (event.key === 'Enter' && event.target.matches('.world')) open('worldDialog');
        });
    })();
</script>
</body>
</html>
