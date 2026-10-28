<?php
/*
 * Rally point → Simulátory of the Travian Legends look (build.php?id=39&t=3): the combat simulator ("Bitevní simulátor")
 * and the travel simulator ("Cestovní simulátor") with the markup of Travian Legends (#rallyPointSimulators,
 * #combatSimulator, .travelSimulator – styled by legends_ltr.css). Both run in the browser
 * (application/legends/simulators.js); this file hands them the world's data: units, own villages with their troops,
 * smithy levels, walls and the speed settings of the world.
 */
$se = [LegendsUi::class, 'e'];
$st = [LegendsUi::class, 't'];

$simUnits = (function () {
    include __DIR__ . '/../../Data/unitdata.php';
    $out = [];
    for ($i = 1; $i <= 70; $i++) {
        if (!isset(${'u' . $i})) continue;
        $u = ${'u' . $i};
        $out[$i] = ['atk' => (int)$u['atk'], 'di' => (int)$u['di'], 'dc' => (int)$u['dc'], 'pop' => (int)$u['pop'],
            'speed' => (int)$u['speed'], 'cap' => (int)$u['cap']];
    }
    return $out;
})();
$simNames = [];
for ($simI = 1; $simI <= 70; $simI++) {
    $simNames[$simI] = (string)$technology->getUnitName($simI);
}
$simTsLevels = (function () {
    include __DIR__ . '/../../Data/buidata.php';
    $out = [0 => 100];
    foreach ($bid14 as $level => $row) $out[(int)$level] = (int)$row['attri'];
    return $out;
})();

/** Level of the first slot holding one of $gids in a village's fdata row. */
$simLevel = function (array $simF, array $gids) {
    for ($i = 19; $i <= 40; $i++) {
        if (in_array((int)($simF['f' . $i . 't'] ?? 0), $gids, true)) return (int)($simF['f' . $i] ?? 0);
    }
    return 0;
};
$simVillages = [];
foreach ((array)$database->getProfileVillages((int)$session->uid) as $simVi) {
    $simWref = (int)$simVi['wref'];
    $simU = (array)$database->getUnit($simWref);
    $simAb = (array)$database->getABTech($simWref);
    $simF = (array)$database->getResourceLevel($simWref);
    $simVillages[] = [
        'id' => $simWref,
        'name' => (string)$simVi['name'],
        'x' => (int)$simVi['vx'],
        'y' => (int)$simVi['vy'],
        'pop' => (int)$simVi['pop'],
        'active' => $simWref === (int)$village->wid,
        'units' => array_map(function ($i) use ($simU) { return (int)($simU['u' . $i] ?? 0); }, range(1, 11)),
        'att' => array_map(function ($i) use ($simAb) { return (int)($simAb['a' . $i] ?? 0); }, range(1, 8)),
        'def' => array_map(function ($i) use ($simAb) { return (int)($simAb['b' . $i] ?? 0); }, range(1, 8)),
        'wall' => $simLevel($simF, [31, 32, 33, 42, 43]),
        'residence' => $simLevel($simF, [25, 26]),
        'ts' => $simLevel($simF, [14]),
    ];
}
usort($simVillages, function ($a, $b) { return [$b['active'], $a['name']] <=> [$a['active'], $b['name']]; });

$simHero = (array)$database->getHero((int)$session->uid);
$simData = [
    'favor' => Skin::favorite('rallySimulators'),
    'tribe' => (int)$session->tribe,
    'hero' => ['health' => (int)round((float)($simHero['health'] ?? 100)), 'power' => (int)($simHero['power'] ?? 0),
        'itempower' => (int)($simHero['itempower'] ?? 0), 'offBonus' => (int)($simHero['offBonus'] ?? 0), 'defBonus' => (int)($simHero['defBonus'] ?? 0)],
    'units' => $simUnits,
    'names' => $simNames,
    'villages' => $simVillages,
    'tsLevels' => $simTsLevels,
    'tsThreshold' => defined('TS_THRESHOLD') ? (int)TS_THRESHOLD : 20,
    'troopSpeed' => defined('INCREASE_SPEED') ? max(1, (float)INCREASE_SPEED) : 1,
    'worldMax' => defined('WORLD_MAX') ? (int)WORLD_MAX : 100,
    'svg' => [],
    'texts' => [],
];
foreach (['svgAttack', 'svgDefend', 'svgAttackLost', 'svgDefendLost', 'svgSkull'] as $simSvg) {
    $simFile = APP_MAIN_PATH . '/gpack/legends655/mainPage/img_ltr/legacy/views/combatSimulator/svg/' . $simSvg . '.svg';
    $simData['svg'][$simSvg] = is_file($simFile) ? trim(file_get_contents($simFile)) : '';
}
foreach ([
    'you' => ['SIM_YOU', 'You'], 'attacker' => ['SIM_ATTACKER', 'Attacker'], 'defender' => ['SIM_DEFENDER', 'Defender'],
    'reinforcement' => ['SIM_REINFORCEMENT', 'Reinforcement'], 'addReinforcement' => ['SIM_ADD_REINFORCEMENT', 'Add reinforcement'],
    'attack' => ['SIM_ATTACK_NORMAL', 'attack'], 'raid' => ['SIM_ATTACK_RAID', 'Raid'], 'clear' => ['SIM_CLEAR', 'Clear'],
    'delete' => ['SIM_DELETE', 'Remove'], 'reload' => ['SIM_RELOAD', 'Load the village\'s troops'],
    'population' => ['SIM_POPULATION', 'Population'], 'upgrades' => ['SIM_UPGRADES', 'Smithy level'],
    'heroOff' => ['SIM_HERO_OFF', 'Hero offence bonus (%)'], 'heroDef' => ['SIM_HERO_DEF', 'Hero defence bonus (%)'],
    'heroStrength' => ['SIM_HERO_STRENGTH', 'Hero fighting strength'], 'wall' => ['SIM_WALL', 'Wall level'],
    'residence' => ['SIM_RESIDENCE', 'Residence / palace level'], 'allyBonus' => ['SIM_ALLY_BONUS', 'Alliance bonus'],
    'result' => ['SIM_RESULT', 'Result'], 'troops' => ['SIM_UNITS', 'Troops'], 'losses' => ['SIM_LOSSES', 'Losses'],
    'survivors' => ['SIM_SURVIVORS', 'Survivors'], 'playDefender' => ['SIM_PLAY_DEFENDER', 'Play as defender'],
    'playAttacker' => ['SIM_PLAY_ATTACKER', 'Play as attacker'], 'fields' => ['SIM_FIELDS', 'fields'],
    'invalidCoords' => ['SIM_INVALID_COORDS', 'Enter valid coordinates.'], 'hero' => ['SIM_HERO', 'Hero'],
    'bounty' => ['SIM_BOUNTY', 'Carrying capacity'],
    'celebration' => ['SIM_CELEBRATION', 'Great celebration'], 'heroHealth' => ['SIM_HERO_HEALTH', 'Health'],
    'heroHorse' => ['SIM_HERO_HORSE', 'Hero on a horse'], 'equipment' => ['SIM_EQUIPMENT', 'Equipment'],
    'rightHand' => ['SIM_RIGHT_HAND', 'Right hand'], 'torso' => ['SIM_TORSO', 'Body'],
    'spyArtefact' => ['SIM_SPY_ARTEFACT', 'Artefact of the scouts'], 'architectArtefact' => ['SIM_ARCHITECT_ARTEFACT', 'Artefact of the architects'],
    'stonemason' => ['SIM_STONEMASON', 'Stonemason level'],
] as $simKey => [$simConst, $simDefault]) {
    $simData['texts'][$simKey] = $st($simConst, $simDefault);
}
$simTribes = [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 4 => 'nature', 5 => 'natar', 6 => 'egyptian', 7 => 'hun'];
$simStar = '<svg viewBox="0 0 20 19" class="star"><path d="M10 0l3.09 6.26L20 7.27l-5 4.87 1.18 6.88L10 15.77l-6.18 3.25L5 12.14 0 7.27l6.91-1.01L10 0z"></path></svg>';
$simSwap = '<svg viewBox="0 0 20 16"><path d="M14 0l5 4-5 4V5H2V3h12zM6 8v3h12v2H6v3l-5-4z"/></svg>';
$simNone = $se($st('SIM_NONE', 'None'));
$simSelect = function (string $name, string $label, array $options, string $extra = '') use ($se) {
    $html = '<label class="select' . ($extra ? ' ' . $extra : '') . '"><select name="' . $se($name) . '">';
    foreach ($options as $value => $text) $html .= '<option value="' . $se($value) . '">' . $se($text) . '</option>';
    return $html . '</select><span class="label pinned">' . $se($label) . '</span></label>';
};
$simSpeeds = [];
foreach ($simUnits as $simId => $simUnit) {
    if ($simUnit['speed'] > 0) $simSpeeds[$simUnit['speed']] = $simUnit['speed'];
}
ksort($simSpeeds);
$simOwnSpeed = (int)($simUnits[((int)$session->tribe - 1) * 10 + 1]['speed'] ?? 6);
$simSpeedOptions = [];
foreach ($simSpeeds as $simS) $simSpeedOptions[$simS] = (string)$simS;
$simArena = [0 => $st('SIM_ARENA_NONE', 'No tournament square')];
for ($simI = 1; $simI <= 20; $simI++) $simArena[$simI] = $st('TASKS_LEVEL', 'Level') . ' ' . $simI . ' (+' . ($simTsLevels[$simI] - 100) . ' %)';
$simCurrent = $simVillages[0] ?? ['x' => 0, 'y' => 0];
?>
<div id="rallyPointSimulators" class="legendsSimulators">
    <div class="subTabs">
        <div class="subTabs_tabs">
            <a href="#combat" class="active" data-sim-tab="combat"><?php echo $se($st('SIM_BATTLE', 'Combat simulator')); ?></a>
            <a href="#travel" data-sim-tab="travel"><?php echo $se($st('SIM_TRAVEL', 'Travel simulator')); ?></a>
            <a href="#" class="favor" data-sim-favor="1" title="<?php echo $se($st('SIM_FAVOR', 'Set as favourite tab')); ?>"><?php echo $simStar; ?></a>
        </div>
    </div>

    <div id="combatSimulator" data-sim-pane="combat">
        <p class="description"><?php echo $se($st('SIM_BATTLE_TEXT', '')); ?></p>
        <div class="switchRoleWrapper">
            <button type="button" class="switch defender" data-sim-switch="1"><?php echo $se($st('SIM_PLAY_DEFENDER', 'Play as defender')); ?></button>
        </div>
        <div class="roles"></div>
        <div id="stickyPin">
            <div id="stickyWrapper">
                <?php echo LegendsUi::button($se($st('SIM_SIMULATE', 'Simulate')), ['class' => 'simulate']); ?>
            </div>
        </div>
        <div class="results" hidden></div>
    </div>

    <div class="travelSimulator formV2" data-sim-pane="travel" hidden>
        <p class="description"><?php echo $se($st('SIM_TRAVEL_TEXT', '')); ?></p>
        <div class="coordinatesWrapper">
            <span class="name"><?php echo $se($st('SIM_START', 'Start')); ?></span>
            <label class="input coordinateX"><input type="text" name="sourceX" placeholder="X" inputmode="numeric"></label>
            <label class="input coordinateY"><input type="text" name="sourceY" placeholder="Y" inputmode="numeric"></label>
            <?php echo LegendsUi::button($simSwap, ['icon' => true, 'class' => 'switchSourceAndTarget', 'title' => $st('SIM_SWITCH', 'Switch start and target')]); ?>
            <span class="name"><?php echo $se($st('SIM_TARGET', 'Target')); ?></span>
            <label class="input coordinateX"><input type="text" name="targetX" placeholder="X" inputmode="numeric"></label>
            <label class="input coordinateY"><input type="text" name="targetY" placeholder="Y" inputmode="numeric"></label>
            <span class="sourceCoordinatesValidation" hidden></span>
            <span class="targetCoordinatesValidation" hidden></span>
        </div>
        <?php
        echo $simSelect('speed', $st('SIM_SLOWEST', 'Speed of the slowest unit'), $simSpeedOptions);
        echo $simSelect('arena', $st('SIM_ARENA', 'Tournament square level'), $simArena);
        echo $simSelect('artefact', $st('SIM_ARTEFACT', 'Artefact effect (faster troops)'), ['1' => $simNone, '1.5' => '×1,5', '2' => '×2', '3' => '×3']);
        echo $simSelect('boots', $st('SIM_BOOTS', 'Hero\'s boots'), ['0' => $simNone, '25' => $st('SIM_BOOTS_1', 'Spurs (+25 %)'), '50' => $st('SIM_BOOTS_2', 'Spurs (+50 %)'), '75' => $st('SIM_BOOTS_3', 'Spurs (+75 %)')]);
        echo $simSelect('item', $st('SIM_ITEM', 'Hero\'s items'), ['0' => $simNone, '30' => $st('SIM_ITEM_1', 'Map (+30 %)'), '40' => $st('SIM_ITEM_2', 'Map (+40 %)'), '50' => $st('SIM_ITEM_3', 'Map (+50 %)')]);
        echo $simSelect('horse', $st('SIM_HORSE', 'Hero\'s horse'), ['0' => $simNone, '14' => $st('SIM_HORSE_1', 'Gelding (14 fields/h)'), '17' => $st('SIM_HORSE_2', 'Thoroughbred (17 fields/h)'), '20' => $st('SIM_HORSE_3', 'Warhorse (20 fields/h)')]);
        echo $simSelect('skills', $st('SIM_HERO_SKILLS', 'Hero skills'), ['0' => $simNone, '5' => '+5 %', '10' => '+10 %', '15' => '+15 %', '20' => '+20 %']);
        ?>
        <div class="calculationWrapper">
            <?php echo LegendsUi::button($se($st('SIM_CALCULATE', 'Calculate')), ['class' => 'calculate', 'disabled' => true]); ?>
            <div class="loadingIndicator" hidden><div class="spinner"></div></div>
            <div class="duration"><?php echo $se($st('SIM_DURATION', 'Duration')); ?>: <span>-</span></div>
            <div class="distance"><?php echo $se($st('SIM_DISTANCE', 'Distance')); ?>: <span>-</span></div>
        </div>
    </div>
</div>
<script type="text/javascript">window.LegendsSimData = <?php echo json_encode($simData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>; window.LegendsSimData.defaultSpeed = 0;</script>
<script type="text/javascript" src="application/legends/simulators.js?v=<?php echo Skin::VERSION; ?>"></script>
