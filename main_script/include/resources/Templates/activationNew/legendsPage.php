<?php
/*
 * Travian Legends activation (graphic packs with heroV2): choose the tribe, the starting sector, confirm with the
 * character name. A page of its own (not the 1280 px game layout), so it fits a phone like the Legends one.
 * Steps post to the same activate.php pages as the classic activation (vid / sector / name).
 */
$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pack = get_gpack_cdn_mainPage_url();
$img = $pack . 'img_ltr/views/activation/';
$bg = get_gpack_cdn_mainPage_url() . 'img_ltr/themes/default/background/bgBuildings.jpg';
$files = [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 6 => 'egyptian', 7 => 'hun', 8 => 'spartan', 9 => 'viking'];
$step = $vars['step'];
// Legends order: Gauls (recommended) first
$order = array_values(array_intersect([3, 1, 2, 6, 7, 8, 9], $vars['tribes']));
$tribe = (int)$vars['tribe'];
$sector = in_array($vars['sector'], ['nw', 'no', 'sw', 'so'], true) ? $vars['sector']
    : ($vars['sector'] === 'ne' ? 'no' : ($vars['sector'] === 'se' ? 'so' : 'sw'));
$sectors = ['nw' => 'North - West', 'no' => 'North - East', 'sw' => 'South - West', 'so' => 'South - East'];
$recommendedSector = 'sw';
$titles = [
    'tribe' => T('ActivationNew', 'Select your tribe'),
    'sector' => T('ActivationNew', 'selectStartingSector', 'Select starting sector'),
    'confirm' => T('ActivationNew', 'confirmChoice', 'Confirm your choice'),
    'login' => T('Login', 'Login'),
];
$feather = '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M17.5 2.5c-6 .5-10 4.5-11.5 9.5l-3.5 5.5 1 .5 3-4.5c4 0 9-3 11-11z" fill="#c9b79a" stroke="#6b5a45" stroke-width="1"/><path d="M15 5L4 16" stroke="#6b5a45" stroke-width="1"/></svg>';
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>OpenVillage – <?=$e($titles[$step]); ?></title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body { font: 14px/1.4 Verdana, Arial, sans-serif; color: #3f3326; background: #4f6531 url('<?=$e($bg); ?>') center top / cover fixed;
            display: flex; flex-direction: column; min-height: 100vh; }
        main { flex: 1; padding: 16px 12px 24px; display: flex; justify-content: center; align-items: flex-start; }
        @font-face { font-family: "Noto Serif"; font-weight: 700; src: url('<?=$e($pack); ?>fonts/noto-serif-latin-ext-700-normal.woff2') format("woff2");
            unicode-range: U+0100-024F, U+1E00-1EFF; }
        @font-face { font-family: "Noto Serif"; font-weight: 700; src: url('<?=$e($pack); ?>fonts/noto-serif-latin-700-normal.woff2') format("woff2"); }
        /* game window: the frame and header of the Legends content windows (hud/content) */
        .frame { position: relative; width: 100%; max-width: 620px; padding: 68px 11px 11px; filter: drop-shadow(0 6px 12px rgba(0, 0, 0, .45)); }
        .frame:before { content: ""; position: absolute; inset: 19px 0 auto; height: 48px; background: url('<?=$e($pack); ?>img_ltr/hud/content/header.jpg') repeat-x; }
        .frame:after { content: ""; position: absolute; inset: 0; z-index: 1; pointer-events: none; border-style: solid; border-width: 68px 51px 11px 51px;
            border-image: url('<?=$e($pack); ?>img_ltr/hud/content/frame.png') 68 51 11 51 repeat; }
        .window { position: relative; z-index: 2; background: #f4efe4 url('<?=$e($img); ?>activateBackground.png') center bottom / 100% auto no-repeat;
            min-height: 620px; display: flex; flex-direction: column; padding-bottom: 190px; }
        .title { position: absolute; z-index: 2; top: 24px; left: 46px; right: 46px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            font: 700 24px/26px "Noto Serif", Georgia, serif; color: #f2f2f2;
            text-shadow: #b68363 1px 0 0, #b68363 1px 1px 0, #b68363 0 1px 0, #5e463a -1px 1px 0, #5e463a -1px 0 0, #5e463a -1px -1px 0, #5e463a 0 -1px 0, #5e463a 1px -1px 0; }
        .logoTop { display: none; position: absolute; top: 10px; left: 16px; z-index: 3; }
        .logoTop svg { width: 190px; height: 44px; }
        .body { padding: 14px 18px 0; flex: 1; display: flex; flex-direction: column; }
        .lead { font-weight: 700; margin: 0 0 18px; }
        .error { background: #f6d9d3; border: 1px solid #c4544a; color: #8a2219; padding: 8px 10px; border-radius: 3px; margin-bottom: 14px; }
        .actions { margin-top: auto; padding: 28px 0 0; text-align: center; }
        .green { appearance: none; position: relative; min-width: 240px; padding: 9px 26px; cursor: pointer; border-radius: 5px;
            font: 700 17px/1.2 Arial, Helvetica, sans-serif; color: #f2f2f2; text-shadow: rgba(0, 0, 0, .5) 1px 1px 1px;
            background: linear-gradient(#7cb029, #587c1e); border: 1px solid #283308;
            box-shadow: #4b6f19 2px 0 1px -1px inset, #4b6f19 -2px 0 1px -1px inset, #b5e251 0 3px 1px -1px inset, rgba(44, 75, 12, .6) 0 -3px 1px -1px inset; }
        .goldFrame { display: inline-block; padding: 3px; border-radius: 7px; border: 1px solid #34220d;
            background: linear-gradient(180deg, #cba467 5%, #f3e2ae 13%, #efbf7b 32%, #aa8050 48%, #835e35 72%, #ad8a54 93%, #d7b672 100%); }
        .green:active { filter: brightness(.92); }
        .links { margin-top: 14px; display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
        .links a { color: #5c8a2b; font-weight: 700; text-decoration: none; }
        input[type=radio] { position: absolute; opacity: 0; pointer-events: none; }

        /* step 1: tribe */
        .tribes { display: flex; align-items: center; justify-content: center; gap: 6px; }
        .tribes .orn { flex: 0 0 auto; width: 34px; height: 39px; background: url('<?=$e($img); ?>ornamentStart.png') right center / contain no-repeat; }
        .tribes .orn.end { background-image: url('<?=$e($img); ?>ornamentEnd.png'); background-position: left center; }
        .tribes label { flex: 0 1 80px; aspect-ratio: 4 / 5; border: 2px solid #b39a6b; border-radius: 3px; cursor: pointer; position: relative;
            background: #64503f center / cover no-repeat; box-shadow: 0 0 0 1px #5d4a36; }
        <?php foreach ($files as $id => $file): ?>
        .tribes label.t<?=$id; ?> { background-image: url('<?=$e($img); ?>selection/tribe/<?=$file; ?>.jpg'); }
        #t<?=$id; ?>:checked ~ .tribes label.t<?=$id; ?> { border-color: #6aa13b; box-shadow: 0 0 0 1px #2c4a14, 0 0 8px rgba(106, 161, 59, .9); }
        #t<?=$id; ?>:checked ~ .tribes label.t<?=$id; ?>:after { content: ""; position: absolute; left: 50%; bottom: -22px; margin-left: -12px;
            border: 12px solid transparent; border-bottom: 14px solid #6aa13b; border-top: 0; }
        #t<?=$id; ?>:checked ~ .descriptions .d<?=$id; ?> { display: block; }
        <?php endforeach; ?>
        .descriptions { margin-top: 24px; }
        .description { display: none; position: relative; min-height: 240px; border: 1px solid #cbb898; border-radius: 3px; padding: 22px 46% 18px 22px;
            background: linear-gradient(110deg, #f2e8d8 40%, #e9d4b8); box-shadow: 0 2px 6px rgba(80, 60, 30, .35); overflow: hidden; }
        .description h2 { margin: 0 0 18px; font: 700 30px/1 Arial, sans-serif; color: #61332b; text-transform: uppercase; }
        .description ul { list-style: none; margin: 0; padding: 0; }
        .description li { display: flex; gap: 10px; align-items: center; margin: 0 0 10px; font-size: 13.5px; }
        .description li svg { flex: 0 0 18px; width: 18px; height: 18px; }
        .description .figure { position: absolute; right: 0; bottom: 0; width: 46%; height: 100%; background: right bottom / contain no-repeat; }
        <?php foreach ($files as $id => $file): ?>
        .description.d<?=$id; ?> .figure { background-image: url('<?=$e($img); ?>preview/tribe/<?=$file; ?>.png'); }
        <?php endforeach; ?>
        .description .ribbon { position: absolute; top: -4px; right: 22%; width: 159px; height: 65px; padding: 8px 14px 22px;
            background: url('<?=$e($img); ?>selection/tribe/ribbon.png') center / 100% 100% no-repeat; color: #fff; font: 700 11px/1.15 Arial, sans-serif;
            text-align: center; text-transform: uppercase; display: grid; align-items: center; z-index: 2; }

        /* step 2: sector */
        .mapWrap { width: min(422px, 100%); margin: 6px auto 0; aspect-ratio: 422 / 396; position: relative;
            background: url('<?=$e($img); ?>selection/sector/map-background.png') center / 100% 100% no-repeat; }
        .map { position: absolute; inset: 7.3% 7.6%; border: 2px solid rgba(255, 255, 255, .45);
            background: url('<?=$e($img); ?>selection/sector/map.png') center / 100% 100% no-repeat; display: grid; grid-template: 1fr 1fr / 1fr 1fr; }
        .map label { position: relative; cursor: pointer; display: flex; flex-direction: column; justify-content: flex-start; align-items: center;
            padding-top: 8%; font: 700 15px Arial, sans-serif; color: #5b4632; text-transform: uppercase; background: rgba(90, 70, 45, .14); }
        .map label.s-sw, .map label.s-so { justify-content: flex-end; padding: 0 0 8%; }
        .map label .banner { width: 52px; height: 54px; background: center / contain no-repeat; display: none; margin: 6px 0; }
        .map label .recommended { display: inline-block; background: #d27b2c; color: #fff; font-size: 12px; padding: 2px 6px; margin-bottom: 6px; }
        .map label.s-nw, .map label.s-sw { border-right: 1px solid rgba(90, 70, 45, .35); }
        .map label.s-nw, .map label.s-no { border-bottom: 1px solid rgba(90, 70, 45, .35); }
        <?php foreach (array_keys($sectors) as $s): ?>
        #s<?=$s; ?>:checked ~ .mapWrap label.s-<?=$s; ?> { background: rgba(255, 250, 235, .45); }
        #s<?=$s; ?>:checked ~ .mapWrap label.s-<?=$s; ?> .banner { display: block; }
        <?php endforeach; ?>
        .map label.s-nw, .map label.s-no { order: 0; }
        .map label .banner { background-image: url('<?=$e($img); ?>selection/sector/banner-<?=$files[$tribe] ?? 'gaul'; ?>.png'); }

        /* step 3: confirm */
        .cards { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .card { position: relative; aspect-ratio: 220 / 300; border: 1px solid #b9a483; border-radius: 3px; background: #efe4cf center bottom / cover no-repeat;
            box-shadow: 0 3px 8px rgba(80, 60, 30, .35); text-align: center; overflow: hidden; }
        .card h3 { margin: 10px 0 0; font: 700 26px/1 Arial, sans-serif; color: #61332b; text-transform: uppercase; position: relative; z-index: 1; }
        .card a { position: absolute; left: 0; right: 0; bottom: 10px; color: #6a9c2e; font-weight: 700; text-decoration: none; z-index: 1; }
        .card.tribe { background-size: 82% auto; }
        .card.sector { background-size: 100% auto; background-position: center 55%; }
        .field { margin-top: 22px; }
        .field label { display: block; font-weight: 700; margin-bottom: 6px; }
        .field input { width: 100%; font: 15px Verdana, Arial, sans-serif; padding: 10px 12px; border: 1px solid #9b8d79; border-radius: 2px; background: #fff; }
        .info { display: flex; gap: 10px; align-items: center; margin-top: 16px; padding: 9px 12px; background: #d9e0ec; border: 1px solid #6f8fc7; border-radius: 3px; color: #2c3b55; }
        .info i { flex: 0 0 20px; height: 20px; border: 1.5px solid #3e68b6; border-radius: 50%; color: #3e68b6; font: 700 13px/17px Georgia, serif; text-align: center; font-style: normal; }

        footer { background: linear-gradient(#a99a83, #94846c); border-top: 2px solid #cdbfa5; padding: 10px 16px; display: flex; align-items: center; gap: 10px; }
        footer a { display: flex; align-items: center; gap: 8px; text-decoration: none; color: #fff; }
        footer svg { width: 172px; height: 40px; }
        .logoTop .emblem, footer .emblem { fill: #b5302b; stroke: #f2f2f2; stroke-width: 1.4; }
        .logoTop .coin, footer .coin { fill: #e5b75b; stroke: #6d4a1d; }
        .logoTop .coinMark, footer .coinMark { fill: #6d4a1d; }
        .logoTop .t1, footer .t1 { fill: #fff; font: 700 16.5px Georgia, serif; }
        .logoTop .t2, footer .t2 { fill: #fff; font: 600 7.5px Arial, sans-serif; letter-spacing: 1.2px; }

        @media (min-width: 760px) {
            .logoTop { display: block; }
            footer { display: none; }
            main { padding-top: 70px; }
        }
        @media (max-width: 520px) {
            main { padding: 10px 6px 16px; }
            .title { font-size: 21px; left: 40px; }
            .body { padding: 12px 12px 0; }
            .window { min-height: 0; padding-bottom: 140px; }
            .tribes { gap: 4px; }
            .tribes .orn { width: 20px; }
            .description { padding: 18px 40% 14px 14px; min-height: 220px; }
            .description h2 { font-size: 24px; }
            .description li { font-size: 12.5px; }
            .description .figure { width: 42%; }
            .card h3 { font-size: 20px; }
            .green { min-width: 220px; font-size: 17px; }
        }
    </style>
</head>
<body>
<a class="logoTop" href="/" title="OpenVillage"><svg viewBox="0 0 172 40" aria-label="OpenVillage">
            <path class="emblem" d="M4 3h18v18c0 8-5 13-9 16-4-3-9-8-9-16z"/>
            <circle class="coin" cx="13" cy="17" r="6.5"/><path class="coinMark" d="M10 13.5h6v1.6h-2.2v6.4h-1.6v-6.4H10z"/>
            <text class="t1" x="30" y="23">OPENVILLAGE</text>
            <text class="t2" x="31" y="35">L E G E N D S</text>
        </svg></a>
<main>
    <div class="frame">
        <h1 class="title"><?=$e($titles[$step]); ?></h1>
        <div class="window">
            <div class="body">
                <?php if ($vars['error'] !== ''): ?>
                    <div class="error"><?=$e($vars['error']); ?></div>
                <?php endif; ?>

                <?php if ($step === 'tribe'): ?>
                    <form method="post" action="activate.php?page=vid" class="step">
                        <p class="lead"><?=$e(T('ActivationNew', 'vidSelectDescription')); ?></p>
                        <?php foreach ($order as $id): ?>
                            <input type="radio" name="vid" value="<?=$id; ?>" id="t<?=$id; ?>"<?=($vars['tribeChosen'] ? $id === $tribe : $id === 3) ? ' checked' : ''; ?>>
                        <?php endforeach; ?>
                        <div class="tribes">
                            <span class="orn"></span>
                            <?php foreach ($order as $id): ?>
                                <label for="t<?=$id; ?>" class="t<?=$id; ?>" title="<?=$e(T('Global', 'races.' . $id)); ?>"></label>
                            <?php endforeach; ?>
                            <span class="orn end"></span>
                        </div>
                        <div class="descriptions">
                            <?php foreach ($order as $id): ?>
                                <div class="description d<?=$id; ?>">
                                    <?php if ($id === 3): ?>
                                        <div class="ribbon"><?=$e(T('ActivationNew', 'recommendedForNewPlayers')); ?></div>
                                    <?php endif; ?>
                                    <h2><?=$e(T('Global', 'races.' . $id)); ?></h2>
                                    <ul>
                                        <?php foreach ((array)T('ActivationNew', 'race' . $id . '_attributes') as $text): ?>
                                            <li><?=$feather; ?><span><?=$e($text); ?></span></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="figure"></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="actions"><span class="goldFrame"><button type="submit" class="green"><?=$e(T('ActivationNew', 'Confirm')); ?></button></span></div>
                    </form>

                <?php elseif ($step === 'sector'): ?>
                    <form method="post" action="activate.php?page=confirmation" class="step">
                        <p class="lead"><?=$e(T('ActivationNew', 'sectorSelectDescription')); ?></p>
                        <?php foreach (array_keys($sectors) as $s): ?>
                            <input type="radio" name="sector" value="<?=$s; ?>" id="s<?=$s; ?>"<?=$s === $recommendedSector ? ' checked' : ''; ?>>
                        <?php endforeach; ?>
                        <div class="mapWrap">
                            <div class="map">
                                <?php foreach ($sectors as $s => $label): ?>
                                    <label for="s<?=$s; ?>" class="s-<?=$s; ?>">
                                        <?php if ($s === 'sw' || $s === 'so'): ?><span class="banner"></span><?php endif; ?>
                                        <?php if ($s === $recommendedSector): ?><span class="recommended"><?=$e(T('ActivationNew', 'RECOMMENDED')); ?></span><?php endif; ?>
                                        <span><?=$e(T('ActivationNew', $label)); ?></span>
                                        <?php if ($s === 'nw' || $s === 'no'): ?><span class="banner"></span><?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="actions"><span class="goldFrame"><button type="submit" class="green"><?=$e(T('ActivationNew', 'Confirm')); ?></button></span></div>
                    </form>

                <?php elseif ($step === 'login'): ?>
                    <form method="post" action="login.php" class="step login">
                        <p class="lead"><?=$e($vars['welcome'] ?? ''); ?></p>
                        <div class="field">
                            <label for="loginName"><?=$e(T('Login', 'accountNameOrEmailAddress', 'Account name or e-mail address')); ?></label>
                            <input id="loginName" name="name" value="<?=$e($vars['name']); ?>" required autocomplete="username">
                        </div>
                        <div class="field">
                            <label for="loginPassword"><?=$e(T('Login', 'pass')); ?></label>
                            <input id="loginPassword" type="password" name="password" required autocomplete="current-password">
                        </div>
                        <input type="hidden" name="s1" value="Login"><input type="hidden" name="w" value="">
                        <div class="links">
                            <a href="login.php?forgotPassword=1"><?=$e(trim(T('Login', 'PasswordForgotten?'))); ?></a>
                            <a href="/"><?=$e(T('Login', 'registerNow', 'Join the game world')); ?></a>
                        </div>
                        <div class="actions"><span class="goldFrame"><button type="submit" class="green"><?=$e(T('Login', 'Login')); ?></button></span></div>
                    </form>
                    <script>document.querySelector('input[name=w]').value = screen.width + ':' + screen.height;</script>

                <?php else: ?>
                    <form method="post" action="activate.php?page=dorf" class="step">
                        <p class="lead"><?=$e(T('ActivationNew', 'confirmDescription', 'Confirm your choice, choose the name of your character and start your adventure.')); ?></p>
                        <div class="cards">
                            <div class="card tribe" style="background-image:url('<?=$e($img); ?>summary/tribe/<?=$files[$tribe] ?? 'gaul'; ?>.png')">
                                <h3><?=$e(T('Global', 'races.' . $tribe)); ?></h3>
                                <a href="activate.php?page=vid"><?=$e(T('ActivationNew', 'change', 'Change')); ?></a>
                            </div>
                            <div class="card sector" style="background-image:url('<?=$e($img); ?>summary/sector/<?=$sector; ?>.png')">
                                <h3><?=$e(T('ActivationNew', $sectors[$sector])); ?></h3>
                                <a href="activate.php?page=sector"><?=$e(T('ActivationNew', 'change', 'Change')); ?></a>
                            </div>
                        </div>
                        <input type="hidden" name="sector" value="<?=$e($sector); ?>">
                        <div class="field">
                            <label for="characterName"><?=$e(T('ActivationNew', 'characterName', 'Enter the name of your character:')); ?></label>
                            <input id="characterName" name="name" value="<?=$e($vars['name']); ?>" minlength="3" maxlength="15"
                                   pattern="[A-Za-z0-9 _\-]+" required autocomplete="off">
                        </div>
                        <div class="info"><i>i</i><span><?=$e(T('ActivationNew', 'characterNameInfo', 'This is the name of your character on the game world.')); ?></span></div>
                        <div class="actions"><span class="goldFrame"><button type="submit" class="green"><?=$e(T('ActivationNew', 'letsPlay', "Let's play")); ?></button></span></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<footer>
    <a href="/" title="OpenVillage">
        <svg viewBox="0 0 172 40" aria-label="OpenVillage">
            <path class="emblem" d="M4 3h18v18c0 8-5 13-9 16-4-3-9-8-9-16z"/>
            <circle class="coin" cx="13" cy="17" r="6.5"/><path class="coinMark" d="M10 13.5h6v1.6h-2.2v6.4h-1.6v-6.4H10z"/>
            <text class="t1" x="30" y="23">OPENVILLAGE</text>
            <text class="t2" x="31" y="35">L E G E N D S</text>
        </svg>
    </a>
</footer>
</body>
</html>
