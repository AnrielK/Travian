<?php
/*
 * Rally point → Simulators of the Travian Legends 655 look (build.php?id=39&tt=3, RallyPointCntrl): the combat
 * simulator and the travel simulator with the markup of Travian Legends (#rallyPointSimulators, #combatSimulator,
 * .travelSimulator – styled by legends_ltr.css), driven by js/Game/Legends/simulators.js. The battle is fought by the
 * game's own simulator (ajax.php?cmd=legendsCombat → Controller\RallyPoint\Simulator), the travel time follows
 * Game\SpeedCalculator (unit speed × movement speed of the world, tournament square after 20 fields, hero items).
 * This file hands the page the world's data: units, own villages with troops and smithy levels, walls, the hero.
 */

use Core\Config;
use Core\Database\DB;
use Core\Session;
use Core\Village;
use Game\Formulas;
use resources\View\LegendsUi;

$se = [LegendsUi::class, 'e'];
$st = static fn(string $key, string $default) => T('LegendsSim', $key, $default);
$session = Session::getInstance();
$db = DB::getInstance();
$uid = (int)$session->getPlayerId();
$ownTribe = (int)$session->getRace();

$simUnits = [];
$simNames = [];
foreach ([1, 2, 3, 4, 5, 6, 7] as $simTribe) {
    for ($i = 1; $i <= 10; ++$i) {
        $id = nrToUnitId($i, $simTribe);
        $row = Formulas::$data['units'][$simTribe - 1][$i - 1] ?? null;
        if (!$row) {
            continue;
        }
        $simUnits[$id] = ['speed' => (int)$row['speed'], 'cap' => (int)$row['cap']];
        $simNames[$id] = T('Troops', $id . '.title');
    }
}

/** Level of the first building slot holding one of $gids in a village's fdata row. */
$simLevel = static function (array $f, array $gids) {
    for ($i = 19; $i <= 40; ++$i) {
        if (in_array((int)($f['f' . $i . 't'] ?? 0), $gids, true)) {
            return (int)($f['f' . $i] ?? 0);
        }
    }
    return 0;
};
$simVillages = [];
$activeKid = (int)Village::getInstance()->getKid();
$result = $db->query('SELECT kid, name, pop FROM vdata WHERE owner = ' . $uid . ' ORDER BY name');
while ($result && ($row = $result->fetch_assoc())) {
    $kid = (int)$row['kid'];
    $units = $db->query('SELECT * FROM units WHERE kid = ' . $kid);
    $units = $units ? (array)$units->fetch_assoc() : [];
    $smithy = $db->query('SELECT * FROM smithy WHERE kid = ' . $kid);
    $smithy = $smithy ? (array)$smithy->fetch_assoc() : [];
    $fields = $db->query('SELECT * FROM fdata WHERE kid = ' . $kid);
    $fields = $fields ? (array)$fields->fetch_assoc() : [];
    $xy = Formulas::kid2xy($kid);
    $levels = array_map(static fn($i) => (int)($smithy['u' . $i] ?? 0), range(1, 8));
    $simVillages[] = [
        'id' => $kid,
        'name' => (string)$row['name'],
        'x' => (int)$xy['x'],
        'y' => (int)$xy['y'],
        'pop' => (int)$row['pop'],
        'active' => $kid === $activeKid,
        'units' => array_map(static fn($i) => (int)($units['u' . $i] ?? 0), range(1, 11)),
        'att' => $levels,
        'def' => $levels,
        'wall' => $simLevel($fields, [31, 32, 33, 42, 43]),
        'residence' => $simLevel($fields, [25, 26]),
        'stonemason' => $simLevel($fields, [34]),
    ];
}
usort($simVillages, static fn($a, $b) => [$b['active'], $a['name']] <=> [$a['active'], $b['name']]);

$hero = $db->query('SELECT health, power, offBonus, defBonus FROM hero WHERE uid = ' . $uid);
$hero = $hero ? (array)$hero->fetch_assoc() : [];
$heroItems = (array)Config::getProperty('heroConfig', 'heroItemsSettings');
$simData = [
    'favor' => (int)$session->getFavoriteTab('rallySimulators') === 1 ? 'travel' : 'combat',
    'tribe' => $ownTribe,
    'hero' => ['health' => (int)round((float)($hero['health'] ?? 100)), 'power' => (int)($hero['power'] ?? 0), 'itempower' => 0,
        'offBonus' => (int)($hero['offBonus'] ?? 0), 'defBonus' => (int)($hero['defBonus'] ?? 0)],
    'units' => $simUnits,
    'names' => $simNames,
    'villages' => $simVillages,
    'mapSize' => (int)MAP_SIZE,
    'movementSpeed' => max(1, (float)Config::getProperty('game', 'movement_speed_increase')),
    'bootsMinDistance' => 20 * (float)($heroItems['heroMinDistanceRate'] ?? 1),
    'bootsRate' => (float)($heroItems['heroDistanceSpeedRate'] ?? 1),
    'mapRate' => (float)($heroItems['heroIncreaseSpeedOwnRate'] ?? 1),
    'svg' => [],
    'texts' => [],
];
foreach (['svgAttack', 'svgDefend', 'svgAttackLost', 'svgDefendLost', 'svgSkull'] as $simSvg) {
    $simFile = get_gpack_mainPage_dir() . 'img_ltr/legacy/views/combatSimulator/svg/' . $simSvg . '.svg';
    $simData['svg'][$simSvg] = is_file($simFile) ? trim((string)file_get_contents($simFile)) : '';
}
foreach ([
    'you' => ['SIM_YOU', 'You'], 'attacker' => ['SIM_ATTACKER', 'Attacker'], 'defender' => ['SIM_DEFENDER', 'Defender'],
    'reinforcement' => ['SIM_REINFORCEMENT', 'Reinforcement'], 'addReinforcement' => ['SIM_ADD_REINFORCEMENT', 'Add reinforcement'],
    'attack' => ['SIM_ATTACK_NORMAL', 'attack'], 'raid' => ['SIM_ATTACK_RAID', 'Raid'], 'clear' => ['SIM_CLEAR', 'Clear'],
    'delete' => ['SIM_DELETE', 'Remove'], 'reload' => ['SIM_RELOAD', 'Load the village\'s troops'],
    'population' => ['SIM_POPULATION', 'Population'], 'upgrades' => ['SIM_UPGRADES', 'Smithy level'],
    'heroOff' => ['SIM_HERO_OFF', 'Hero offence bonus (%)'], 'heroDef' => ['SIM_HERO_DEF', 'Hero defence bonus (%)'],
    'heroStrength' => ['SIM_HERO_STRENGTH', 'Hero fighting strength'], 'wall' => ['SIM_WALL', 'Wall level'],
    'residence' => ['SIM_RESIDENCE', 'Residence / palace level'],
    'result' => ['SIM_RESULT', 'Result'], 'troops' => ['SIM_UNITS', 'Troops'], 'losses' => ['SIM_LOSSES', 'Losses'],
    'survivors' => ['SIM_SURVIVORS', 'Survivors'], 'playDefender' => ['SIM_PLAY_DEFENDER', 'Play as defender'],
    'playAttacker' => ['SIM_PLAY_ATTACKER', 'Play as attacker'], 'fields' => ['SIM_FIELDS', 'fields'],
    'invalidCoords' => ['SIM_INVALID_COORDS', 'Enter valid coordinates.'], 'hero' => ['SIM_HERO', 'Hero'],
    'bounty' => ['SIM_BOUNTY', 'Carrying capacity'], 'heroHealth' => ['SIM_HERO_HEALTH', 'Health'],
    'heroHorse' => ['SIM_HERO_HORSE', 'Hero on a horse'], 'equipment' => ['SIM_EQUIPMENT', 'Equipment'],
    'rightHand' => ['SIM_RIGHT_HAND', 'Right hand'], 'torso' => ['SIM_TORSO', 'Body'],
    'spyArtefact' => ['SIM_SPY_ARTEFACT', 'Artefact of the scouts'], 'architectArtefact' => ['SIM_ARCHITECT_ARTEFACT', 'Artefact of the architects'],
    'stonemason' => ['SIM_STONEMASON', 'Stonemason level'], 'catapultTarget' => ['SIM_CATAPULT_TARGET', 'Catapult target level'],
] as $simKey => [$simConst, $simDefault]) {
    $simData['texts'][$simKey] = $st($simConst, $simDefault);
}
$simStar = '<svg viewBox="0 0 20 19" class="star"><path d="M10 0l3.09 6.26L20 7.27l-5 4.87 1.18 6.88L10 15.77l-6.18 3.25L5 12.14 0 7.27l6.91-1.01L10 0z"></path></svg>';
$simSwap = '<svg viewBox="0 0 20 16"><path d="M14 0l5 4-5 4V5H2V3h12zM6 8v3h12v2H6v3l-5-4z"/></svg>';
$simNone = $st('SIM_NONE', 'None');
$simSelect = static function (string $name, string $label, array $options) use ($se) {
    $html = '<label class="select"><select name="' . $se($name) . '">';
    foreach ($options as $value => $text) {
        $html .= '<option value="' . $se($value) . '">' . $se($text) . '</option>';
    }
    return $html . '</select><span class="label pinned">' . $se($label) . '</span></label>';
};
$simSpeeds = [];
foreach ($simUnits as $simUnit) {
    if ($simUnit['speed'] > 0) {
        $simSpeeds[$simUnit['speed']] = (string)$simUnit['speed'];
    }
}
ksort($simSpeeds);
$simArena = [0 => $st('SIM_ARENA_NONE', 'No tournament square')];
for ($i = 1; $i <= 20; ++$i) {
    $simArena[$i] = $st('SIM_LEVEL', 'Level') . ' ' . $i . ' (+' . (Formulas::TournamentSqValue($i) - 100) . ' %)';
}
$bootsRate = $simData['bootsRate'];
$mapRate = $simData['mapRate'];
$pct = static fn($v) => '+' . rtrim(rtrim(number_format($v, 1, ',', ''), '0'), ',') . ' %';
?>
<div id="rallyPointSimulators" class="legendsSimulators">
    <div class="subTabs">
        <div class="subTabs_tabs">
            <a href="#combat" class="active" data-sim-tab="combat"><?=$se($st('SIM_BATTLE', 'Combat simulator')); ?></a>
            <a href="#travel" data-sim-tab="travel"><?=$se($st('SIM_TRAVEL', 'Travel simulator')); ?></a>
            <a href="#" class="favor" data-sim-favor="1" title="<?=$se($st('SIM_FAVOR', 'Set as favourite tab')); ?>"><?=$simStar; ?></a>
        </div>
    </div>

    <div id="combatSimulator" data-sim-pane="combat">
        <p class="description"><?=$se($st('SIM_BATTLE_TEXT', '')); ?></p>
        <div class="switchRoleWrapper">
            <button type="button" class="switch defender" data-sim-switch="1"><?=$se($st('SIM_PLAY_DEFENDER', 'Play as defender')); ?></button>
        </div>
        <div class="roles"></div>
        <div id="stickyPin">
            <div id="stickyWrapper">
                <?=LegendsUi::button($se($st('SIM_SIMULATE', 'Simulate')), ['class' => 'simulate']); ?>
            </div>
        </div>
        <div class="results" hidden></div>
    </div>

    <div class="travelSimulator formV2" data-sim-pane="travel" hidden>
        <p class="description"><?=$se($st('SIM_TRAVEL_TEXT', '')); ?></p>
        <div class="coordinatesWrapper">
            <span class="name"><?=$se($st('SIM_START', 'Start')); ?></span>
            <label class="input coordinateX"><input type="text" name="sourceX" placeholder="X" inputmode="numeric"></label>
            <label class="input coordinateY"><input type="text" name="sourceY" placeholder="Y" inputmode="numeric"></label>
            <?=LegendsUi::button($simSwap, ['icon' => true, 'class' => 'switchSourceAndTarget', 'title' => $st('SIM_SWITCH', 'Switch start and target')]); ?>
            <span class="name"><?=$se($st('SIM_TARGET', 'Target')); ?></span>
            <label class="input coordinateX"><input type="text" name="targetX" placeholder="X" inputmode="numeric"></label>
            <label class="input coordinateY"><input type="text" name="targetY" placeholder="Y" inputmode="numeric"></label>
            <span class="sourceCoordinatesValidation" hidden></span>
            <span class="targetCoordinatesValidation" hidden></span>
        </div>
        <?php
        echo $simSelect('speed', $st('SIM_SLOWEST', 'Speed of the slowest unit'), $simSpeeds);
        echo $simSelect('arena', $st('SIM_ARENA', 'Tournament square level'), $simArena);
        echo $simSelect('artefact', $st('SIM_ARTEFACT', 'Artefact effect (faster troops)'), ['1' => $simNone, '1.5' => '×1,5', '2' => '×2', '3' => '×3']);
        echo $simSelect('boots', $st('SIM_BOOTS', 'Hero\'s boots'), ['0' => $simNone, (string)(25 * $bootsRate) => $st('SIM_BOOTS_SPURS', 'Spurs') . ' (' . $pct(25 * $bootsRate) . ')',
            (string)(50 * $bootsRate) => $st('SIM_BOOTS_SPURS', 'Spurs') . ' (' . $pct(50 * $bootsRate) . ')', (string)(75 * $bootsRate) => $st('SIM_BOOTS_SPURS', 'Spurs') . ' (' . $pct(75 * $bootsRate) . ')']);
        echo $simSelect('item', $st('SIM_ITEM', 'Hero\'s items'), ['0' => $simNone, (string)(30 * $mapRate) => $st('SIM_ITEM_MAP', 'Map') . ' (' . $pct(30 * $mapRate) . ')',
            (string)(40 * $mapRate) => $st('SIM_ITEM_MAP', 'Map') . ' (' . $pct(40 * $mapRate) . ')', (string)(50 * $mapRate) => $st('SIM_ITEM_MAP', 'Map') . ' (' . $pct(50 * $mapRate) . ')']);
        ?>
        <div class="calculationWrapper">
            <?=LegendsUi::button($se($st('SIM_CALCULATE', 'Calculate')), ['class' => 'calculate', 'disabled' => true]); ?>
            <div class="loadingIndicator" hidden><div class="spinner"></div></div>
            <div class="duration"><?=$se($st('SIM_DURATION', 'Duration')); ?>: <span>-</span></div>
            <div class="distance"><?=$se($st('SIM_DISTANCE', 'Distance')); ?>: <span>-</span></div>
        </div>
    </div>
</div>
<script type="text/javascript">window.LegendsSimData = <?=json_encode($simData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;</script>
<script type="text/javascript" src="js/Game/Legends/simulators.js?v=<?=ov_asset_version(PUBLIC_PATH . 'js/Game/Legends/simulators.js'); ?>"></script>
