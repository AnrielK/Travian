<?php
// Graphic packs. The game's look comes from the "mainPage" pack (CSS, sprites, layout images);
// GAME_GPACK picks it per world (iOS app: Nastavení světa → Grafika; native runtime: GAME_GPACK=…).
// Packs whose folder is missing are skipped, so a trimmed distribution keeps working.
$styles = [
    // 29c89d54 = Travian Legends look: its own CSS with the page layout of layout/LayoutT46.php (is_t46_layout()),
    // plus a17a8f72's styles for the T4.4 widgets/pages; files it lacks are served from a17a8f72 (web server fallback).
    '29c89d54' => ['hash' => '29c89d54', 'name' => 'Travian T4.6', 'isNew' => true, 'layout' => 'legends'],
    // legends655 = Travian Legends gpack 655.3 (scripts/gpack/import-legends-gpack.py), same Legends layout;
    // 'heroV2' = its stylesheet styles the new hero window (inventory / attributes / appearance) and auctions.
    'legends655' => ['hash' => 'legends655', 'name' => 'Travian Legends 655.3 (výchozí)', 'isNew' => true, 'layout' => 'legends', 'heroV2' => true],
    'a17a8f72' => ['hash' => 'a17a8f72', 'name' => 'OpenVillage 4.6', 'isNew' => true],
    '68597666' => ['hash' => '68597666', 'name' => 'Travian T4.4 Seasonized', 'isNew' => false],
    'd11cb434' => ['hash' => 'd11cb434', 'name' => 'Travian T4.4 Seasonized v2', 'isNew' => false],
    'TravianOld' => ['hash' => 'TravianOld', 'name' => 'Travian T4.4 Classic', 'isNew' => false],
];
$styles = array_filter($styles, static fn ($p, $hash) => is_file(__DIR__ . "/$hash/mainPage/lang/en-US/compact.css"),
    ARRAY_FILTER_USE_BOTH);
// Default look: legends655 (the iOS app ships only this pack); older packs stay selectable where present.
$default = isset($styles['legends655']) ? 'legends655' : (isset($styles['29c89d54']) ? '29c89d54' : 'a17a8f72');
$wanted = getenv('GAME_GPACK');
$mainPage = ($wanted !== false && isset($styles[$wanted])) ? $wanted : $default;
return [
    'default' => $default,
    'mainPage' => $mainPage,
    'path' => __DIR__,
    'list' => [
        $default => ['hash' => $default, 'name' => 'OpenVillage 4.6', 'isNew' => true],
    ],
    'styles' => $styles,
];
