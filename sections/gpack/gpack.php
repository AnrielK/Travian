<?php
// Graphic packs. The game's look comes from the "mainPage" pack (CSS, sprites, layout images);
// GAME_GPACK picks it per world (iOS app: Nastavení světa → Grafika; native runtime: GAME_GPACK=…).
// Packs whose folder is missing are skipped, so a trimmed distribution keeps working.
$styles = [
    // 29c89d54 = Travian Legends look: its own CSS with the page layout of layout/LayoutT46.php (is_t46_layout()),
    // plus a17a8f72's styles for the T4.4 widgets/pages; files it lacks are served from a17a8f72 (web server fallback).
    '29c89d54' => ['hash' => '29c89d54', 'name' => 'Travian T4.6 (výchozí)', 'isNew' => true],
    'a17a8f72' => ['hash' => 'a17a8f72', 'name' => 'OpenVillage 4.6', 'isNew' => true],
    '68597666' => ['hash' => '68597666', 'name' => 'Travian T4.4 Seasonized', 'isNew' => false],
    'd11cb434' => ['hash' => 'd11cb434', 'name' => 'Travian T4.4 Seasonized v2', 'isNew' => false],
    'TravianOld' => ['hash' => 'TravianOld', 'name' => 'Travian T4.4 Classic', 'isNew' => false],
];
$styles = array_filter($styles, static fn ($p, $hash) => is_file(__DIR__ . "/$hash/mainPage/lang/en-US/compact.css"),
    ARRAY_FILTER_USE_BOTH);
$wanted = getenv('GAME_GPACK');
$mainPage = ($wanted !== false && isset($styles[$wanted])) ? $wanted
    : (isset($styles['29c89d54']) ? '29c89d54' : 'a17a8f72');
return [
    'default' => '29c89d54',
    'mainPage' => $mainPage,
    'list' => [
        '29c89d54' => ['hash' => '29c89d54', 'name' => 'OpenVillage 4.6', 'isNew' => true],
    ],
    'styles' => $styles,
];
