<?php
/*
 * Statistics → General ("Všeobecné") in the Travian Legends look: the markup of the Legends client (#statisticsV2,
 * styled by legends_ltr.css of the graphic pack) rendered on the server, with SVG charts from
 * resources\View\StatisticsCharts and a small script for the tabs, the data switches and collapsing the sections.
 * Data: Model\StatisticsGeneralModel ($vars['data']).
 */

use Core\Helper\TimezoneHelper;
use resources\View\StatisticsCharts as Chart;

$d = $vars['data'];
$e = [Chart::class, 'e'];
$t = static function (string $key, string $default) {
    return T('StatisticsV2', $key, $default);
};
$cs = get_locale() === 'cs-CZ';
$fmt = static function ($x) {
    return number_format_x((int)round($x));
};
/** 686 800 → "686,8k" ("686,8tis." in Czech), 2 700 000 → "2,7M" ("2,7mil."). */
$short = static function ($x) use ($t, $cs, $fmt) {
    $sign = $x < 0 ? '−' : '';
    $a = abs($x);
    if ($a >= 1e6) {
        return $sign . number_format($a / 1e6, 1, $cs ? ',' : '.', '') . $t('millionShort', 'M');
    }
    if ($a >= 1e3) {
        return $sign . number_format($a / 1e3, 1, $cs ? ',' : '.', '') . $t('thousandShort', 'k');
    }
    return $sign . $fmt($a);
};
$pct = static function ($x) use ($cs) {
    return number_format($x, 1, $cs ? ',' : '.', '') . '%';
};
$rank = static function ($r) use ($fmt) {
    return $r ? $fmt($r) : '-';
};
/** Image of the graphic pack, or of the Travian Legends pack when this look has none (pie chart labels). */
$img = static function (string $path) {
    $dir = get_gpack_mainPage_dir();
    if (is_file($dir . $path)) {
        return get_gpack_cdn_mainPage_url() . $path;
    }
    return preg_replace('~[^/]+/mainPage/$~', 'legends655/mainPage/', get_gpack_cdn_mainPage_url()) . $path;
};
$tribeNames = [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 6 => 'egyptian', 7 => 'hun', 8 => 'spartan'];
$tribeColors = [1 => '#96323d', 2 => '#428291', 3 => '#de7e1b', 6 => '#3e559a', 7 => '#674876', 8 => '#4E4545'];
$worseColor = '#608cbc';
$betterColor = '#ea866e';
$dayTip = static function ($day, $text) use ($t) {
    return $t('day', 'Day') . ' ' . $day . ': ' . $text;
};

/** "Game world: rank" and "Alliance: rank" with their icons. */
$serverAllianceRank = static function (string $class, $server, $alliance, bool $hasAlliance, string $serverTitle = '', string $allianceTitle = '') use ($t, $e, $rank) {
    $title = static function ($s) use ($e) {
        return $s !== '' ? ' title="' . $e($s) . '"' : '';
    };
    return '<div class="ServerAllianceRank">'
        . '<div class="typeWrapper"' . $title($serverTitle) . '><div class="buttonWrapper"><div>' . $e($t('server', 'Game world:')) . '</div><i class="gameworld_large"></i></div>'
        . '<div class="rankDisplay ' . $class . '">' . $rank($server) . '</div></div>'
        . '<div class="typeWrapper' . ($hasAlliance ? '' : ' disabled') . '"' . $title($hasAlliance ? $allianceTitle : $t('youAreNotInAlly', 'You are not in an alliance')) . '>'
        . '<div class="buttonWrapper"><div>' . $e($t('alliance', 'Alliance')) . '</div><i class="alliance_large"></i></div>'
        . '<div class="rankDisplay ' . $class . '">' . ($hasAlliance ? $rank($alliance) : '-') . '</div></div>'
        . '</div>';
};
/** Pie of the entries worse and better than the player, with the two counters left and right of it. */
$comparison = static function (string $chartClass, string $title, int $worse, int $better, string $icon, string $iconTitle, string $worseTip = '', string $betterTip = '') use ($t, $e, $worseColor, $betterColor) {
    $tip = static function ($s) use ($e) {
        return $s !== '' ? ' title="' . $e($s) . '"' : '';
    };
    return '<div class="' . $chartClass . '"><div class="chartTitle">' . $e($title) . '</div><div>'
        . '<div class="worse centeredText"' . $tip($worseTip) . '><p class="inlineIcon" title="' . $e($iconTitle) . '"><i class="' . $icon . '"></i><span>' . $worse . '</span></p><p>' . $e($t('worse', 'worse')) . '</p></div>'
        . '<div class="better centeredText"' . $tip($betterTip) . '><p class="inlineIcon" title="' . $e($iconTitle) . '"><i class="' . $icon . '"></i><span>' . $better . '</span></p><p>' . $e($t('better', 'better')) . '</p></div>'
        . '</div>'
        . Chart::pie([
            ['value' => $worse, 'color' => $worseColor, 'title' => $t('worse', 'worse') . ': ' . $worse],
            ['value' => $better, 'color' => $betterColor, 'title' => $t('better', 'better') . ': ' . $better],
        ], '')
        . '</div>';
};
$legendTabs = static function (array $tabs) use ($e) {
    $out = '<div class="legendTabs">';
    $first = true;
    foreach ($tabs as $mode => [$icon, $label]) {
        $out .= '<div class="tab inlineIcon' . ($first ? ' active' : '') . '" data-mode="' . $mode . '"><i class="' . $icon . '"></i><span>' . $e($label) . '</span></div>';
        $first = false;
    }
    return $out . '</div>';
};
$modeView = static function (string $mode, string $html, bool $first) {
    return '<div data-mode-view="' . $mode . '"' . ($first ? '' : ' style="display:none"') . '>' . $html . '</div>';
};
$switch = '<div class="openedClosedSwitch switchOpened"></div>';
$modes = ['perDay' => ['clock_medium', $t('perDay', 'Per day')], 'soFar' => ['resources_medium', $t('soFar', 'So far')]];

// ------------------------------------------------------------------------------------------- world progression
$sp = $d['serverProgression'];
$stepTitle = static function (array $s) use ($t) {
    $date = static function ($time, $hour = false) {
        return $time ? TimezoneHelper::autoDate($time, $hour) : '-';
    };
    $defaults = [
        'start_achieved' => 'Start date of the server',
        'start_locked' => 'Start date of the server',
        'secondVillage_locked' => 'Nobody has founded a second village yet',
        'secondVillage_achieved' => 'First player to found a second village on this server: [VALUE] ([DATETIME])',
        'artifacts_locked' => 'Artefacts will appear on [VALUE]',
        'artifacts_unlocked' => 'Artefacts appeared on [VALUE]',
        'artifacts_achieved' => 'The first artefact was conquered by [VALUE] ([DATETIME])',
        'constructionPlans_locked' => 'Construction plans will appear on [VALUE]',
        'constructionPlans_unlocked' => 'Construction plans appeared on [VALUE]',
        'constructionPlans_achieved' => 'First player to obtain a construction plan: [VALUE] ([DATETIME])',
        'firstWWLevelOne_locked' => 'Who will build the first level of a Wonder of the World?',
        'firstWWLevelOne_achieved' => 'The first level of a Wonder of the World was built by [VALUE] ([DATETIME])',
        'serverEnd_locked' => 'Maximum duration of this server',
        'serverEnd_achieved' => 'The server ended on [VALUE]',
    ];
    $key = $s['id'] . '_' . $s['state'];
    $value = $s['player'] ?? $date($s['release'] ?? ($s['time'] ?? 0));
    return strtr($t('step_' . $key, $defaults[$key] ?? ''), ['[VALUE]' => $value, '[DATETIME]' => $date($s['time'] ?? 0, true)]);
};
$stepLabel = static function (array $s) {
    if ($s['state'] === 'achieved' && isset($s['player'])) {
        return $s['player'];
    }
    if (in_array($s['id'], ['start', 'serverEnd'])) {
        return TimezoneHelper::autoDate($s['time']);
    }
    if (!empty($s['release'])) {
        return TimezoneHelper::autoDate($s['release']);
    }
    return '-';
};
?>
<div id="statisticsV2" class="contentV2">
<div id="serverProgression">
    <h4><i class="serverProgression"></i> <?=$e($t('serverProgression', 'Game world progression')); ?></h4>
    <div id="serverProgressionTimeLine">
        <div id="timeLine">
            <div class="serverNumbers">
                <div class="counter serverTime" title="<?=$e($t('daysSinceServerStart', 'Days since the server started')); ?>"><i class="serverTime"></i><span><?=(int)$sp['ageInDays']; ?></span></div>
                <div class="counter activatedPlayers" title="<?=$e($t('activatedPlayers', 'Number of players on this server')); ?>"><i class="player_large"></i><span><?=(int)$sp['activatedPlayers']; ?></span></div>
            </div>
            <div class="timeLine itemCount<?=count($sp['steps']); ?> unlocked<?=(int)$sp['reached']; ?>">
                <?php foreach ($sp['steps'] as $s): ?>
                    <div class="step <?=$e($s['id']); ?> <?=$e($s['state']); ?>" title="<?=$e($stepTitle($s)); ?>">
                        <i class="<?=$e($s['id']); ?> tribe<?=(int)($s['state'] === 'achieved' ? ($s['tribe'] ?? 0) : 0); ?>"></i>
                        <div class="description"><?=$e($stepLabel($s)); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div id="serverProgressionTribeDistribution">
        <div class="chartWithLegend">
            <div class="legendContainer">
                <div class="simpleLegend">
                    <table class="transparent">
                        <thead><tr><td><i class="distribution"></i></td><td colspan="2"><?=$e($t('currentTribeDistribution', 'Current tribe distribution')); ?></td></tr></thead>
                        <tbody>
                        <?php foreach ($sp['tribeDistribution'] as $tribe => $value): ?>
                            <tr>
                                <td><i class="tribe<?=$tribe; ?>_medium"></i></td>
                                <td><?=$e(T('Global', 'races.' . $tribe)); ?></td>
                                <td><span class="v<?=$tribe; ?>"><?=$pct($value); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="chartContainer">
                <?php
                $slices = [];
                foreach ($sp['tribeDistribution'] as $tribe => $value) {
                    $slices[] = [
                        'value' => $value, 'color' => $tribeColors[$tribe] ?? '#999', 'label' => $pct($value),
                        'title' => T('Global', 'races.' . $tribe) . ': ' . $pct($value), 'class' => 'nation nation' . $tribe,
                        'icon' => $img('img_ltr/global/tribes/' . ($tribeNames[$tribe] ?? 'roman') . '_medium.png'),
                    ];
                }
                echo Chart::pie($slices, 'side');
                ?>
            </div>
        </div>
    </div>
</div>

<?php $pr = $d['populationRank']; ?>
<div id="populationRank" class="expanded statisticsSection" data-section="populationRank">
    <?=$switch; ?>
    <h4><i class="populationRank"></i> <?=$e($t('populationRank', 'Population rank')); ?></h4>
    <div class="chartWithLegend">
        <div class="legendContainer">
            <div class="simpleLegend">
                <?=$serverAllianceRank('Population', $pr['serverRank'], $pr['allianceRank'], $pr['hasAlliance'],
                    strtr($t('yourPopulationRank', 'Your rank in the population ranking of this server: [RANK]'), ['[RANK]' => $rank($pr['serverRank'])]),
                    strtr($t('yourPopulationRankInAlly', 'Your rank in the population ranking of your alliance: [RANK]'), ['[RANK]' => $rank($pr['allianceRank'])])); ?>
            </div>
        </div>
        <div class="chartContainer">
            <?php
            $points = [];
            foreach ($pr['history'] as $day => $r) {
                $points[$day] = $r['server'];
            }
            echo Chart::area($points, [
                'yMin' => 1,
                'format' => $fmt,
                'tooltip' => static function ($day, $v) use ($dayTip, $t, $fmt) {
                    return $dayTip($day, $t('rank', 'Rank') . ' ' . $fmt($v));
                },
            ]);
            ?>
        </div>
    </div>
</div>

<?php
$rr = $d['resourceRank'];
$base = max(1, $rr['positive']['sum'], $rr['negative']['sum']);
$w = static function ($x) use ($base) {
    return round($x / $base * 100, 4) . '%';
};
$resIcons = [
    'r1' => ['lumber_small', 26, 19, '#f37800', T('inGame', 'resources.r1', 'Lumber')],
    'r2' => ['clay_small', 26, 18, '#f09e32', T('inGame', 'resources.r2', 'Clay')],
    'r3' => ['iron_small', 26, 18, '#b1cacf', T('inGame', 'resources.r3', 'Iron')],
    'r4' => ['crop_small', 26, 19, '#ffde00', T('inGame', 'resources.r4', 'Crop')],
];
$hasRaid = $rr['positive']['raid'] > 0 || $rr['negative']['raid'] > 0;
$hasTrade = $rr['positive']['trade'] > 0 || $rr['negative']['trade'] > 0;
?>
<div id="resourceRank" class="statisticsSection" data-section="resourceRank">
    <?=$switch; ?>
    <h4><i class="resourceRank"></i> <?=$e($t('resourceRank', 'Resource rank')); ?></h4>
    <div>
        <div class="resourceBarChartContainer">
            <div class="resourceBarChart animated">
                <div class="barContainer">
                    <div class="positive animated"><span class="raid" style="width: <?=$w($rr['positive']['raid']); ?>;"></span><span class="trade" style="width: <?=$w($rr['positive']['trade']); ?>;"></span><span class="production" style="width: <?=$w($rr['positive']['production']); ?>;"></span></div>
                    <div class="negative"><span class="trade" style="width: <?=$w($rr['negative']['trade']); ?>;"></span><span class="raid" style="width: <?=$w($rr['negative']['raid']); ?>;"></span></div>
                </div>
                <div class="balance">
                    <div class="positive"><?=$e($short($rr['positive']['sum'])); ?></div>
                    <div class="negative"><?=$e($short(-$rr['negative']['sum'])); ?></div>
                    <div class="total <?=$rr['balance'] < 0 ? 'negative' : 'positive'; ?>"><?=$e($short($rr['balance'])); ?></div>
                </div>
            </div>
            <table class="resourceBarChartLegend transparent">
                <tbody>
                <?php foreach ([
                    ['positive raid', $t('bountyWon', 'Bounty won:'), $rr['positive']['raid']],
                    ['positive trade', $t('incomingTradingVolume', 'Incoming trading volume:'), $rr['positive']['trade']],
                    ['positive production', $t('production', 'Production') . ':', $rr['positive']['production']],
                    ['negative raid', $t('bountyLost', 'Stolen resources:'), -$rr['negative']['raid']],
                    ['negative trade', $t('outgoingTradingVolume', 'Outgoing trading volume:'), -$rr['negative']['trade']],
                ] as [$class, $label, $value]): ?>
                    <tr class="<?=$class; ?>">
                        <td><?=$e($label); ?></td>
                        <td class="value" title="<?=$e($fmt($value)); ?>"><?=$e($short($value)); ?></td>
                        <td class="indicators"><div class="cellInner"><span class="resourceIndicator"></span></div></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="chartWithLegend statsModeScope">
            <div class="legendContainer">
                <?=$legendTabs($modes); ?>
                <div class="simpleLegend">
                    <?php $first = true;
                    foreach (['perDay', 'soFar'] as $mode) {
                        echo $modeView($mode, $serverAllianceRank('Production', $rr['rank'][$mode]['server'], $rr['rank'][$mode]['alliance'], $rr['hasAlliance'],
                            strtr($t('yourProductionRank', 'Your rank in the production ranking of this server: [RANK]'), ['[RANK]' => $rank($rr['rank'][$mode]['server'])]),
                            strtr($t('yourProductionRankInAlly', 'Your rank in the production ranking of your alliance: [RANK]'), ['[RANK]' => $rank($rr['rank'][$mode]['alliance'])])), $first);
                        $first = false;
                    } ?>
                </div>
            </div>
            <div class="chartContainer statsSwitchScope">
                <div class="dataSwitchButtons">
                    <a href="#" class="iconButtonV2<?=$hasRaid ? '' : ' disabled'; ?>" data-switch="raid" title="<?=$e($t('bounty', 'Bounty')); ?>"><i class="bounty_small"></i></a>
                    <a href="#" class="iconButtonV2<?=$hasTrade ? '' : ' disabled'; ?>" data-switch="trade" title="<?=$e($t('trading', 'Trading')); ?>"><i class="trade_small"></i></a>
                    <a href="#" class="iconButtonV2 active" data-switch="production" title="<?=$e($t('production', 'Production')); ?>"><i class="resources_small"></i></a>
                </div>
                <div data-switch-view="raid" class="raidChart" style="display:none">
                    <div class="chartTitle"><?=$e($t('bounty', 'Bounty')); ?></div>
                    <?=Chart::bars($rr['raidHistory'], ['inColor' => '#eda932', 'outColor' => '#c35742', 'format' => $short,
                        'inTitle' => $t('bountyWon', 'Bounty won:'), 'outTitle' => $t('bountyLost', 'Stolen resources:')]); ?>
                </div>
                <div data-switch-view="trade" class="tradeChart" style="display:none">
                    <div class="chartTitle"><?=$e($t('tradingVolume', 'Trading volume')); ?></div>
                    <?=Chart::bars($rr['tradeHistory'], ['inColor' => '#f4c97e', 'outColor' => '#d48879', 'format' => $short,
                        'inTitle' => $t('incomingTradingVolume', 'Incoming trading volume:'), 'outTitle' => $t('outgoingTradingVolume', 'Outgoing trading volume:')]); ?>
                </div>
                <div data-switch-view="production">
                    <div class="chartTitle"><?=$e($t('production', 'Production')); ?></div>
                    <?php $first = true;
                    foreach (['perDay', 'soFar'] as $mode) {
                        $slices = [];
                        foreach ($resIcons as $r => [$icon, $iw, $ih, $color, $name]) {
                            $value = $rr['production'][$mode][$r];
                            $slices[] = ['value' => $value, 'color' => $color, 'label' => $short($value), 'title' => $name . ': ' . $fmt($value),
                                'icon' => $img('img_ltr/global/resources/' . $icon . '.png'), 'iconW' => $iw, 'iconH' => $ih];
                        }
                        echo $modeView($mode, Chart::pie($slices, 'stack'), $first);
                        $first = false;
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$ms = $d['militaryStrength'];
$villages = $ms['villages'];
$def = $ms['defence'];
?>
<div id="militaryStrengthRank" class="statisticsSection" data-section="militaryStrengthRank">
    <?=$switch; ?>
    <h4><i class="militaryStrengthRank"></i> <?=$e($t('militaryStrengthRank', 'Military strength rank')); ?></h4>
    <div>
        <div class="chartWithLegend statsModeScope statsVillageScope">
            <div class="legendContainer">
                <?=$legendTabs(['offence' => ['offence_medium', $t('offensive', 'Offence')], 'defence' => ['defence_medium', $t('defensive', 'Defence')]]); ?>
                <div class="simpleLegend">
                    <div data-mode-view="offence">
                        <?php foreach ($villages as $i => $v): ?>
                            <div data-village-view="<?=$v['kid']; ?>"<?=$i ? ' style="display:none"' : ''; ?>>
                                <?=$serverAllianceRank('Offensive', $v['offRank']['server'], $v['offRank']['alliance'], $ms['hasAlliance'],
                                    strtr($t('yourOffensiveRank', 'Rank of village [VILLAGE] in the offensive strength ranking of this server: [RANK]'), ['[VILLAGE]' => $v['name'], '[RANK]' => $rank($v['offRank']['server'])]),
                                    strtr($t('yourOffensiveRankInAlly', 'Rank of village [VILLAGE] in the offensive strength ranking of your alliance: [RANK]'), ['[VILLAGE]' => $v['name'], '[RANK]' => $rank($v['offRank']['alliance'])])); ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if (!$villages): ?>
                            <?=$serverAllianceRank('Offensive', 0, 0, $ms['hasAlliance']); ?>
                        <?php endif; ?>
                        <table class="villageList transparent">
                            <thead><tr><th></th><th><i class="village_medium"></i></th><th><i class="offence_medium"></i></th></tr></thead>
                            <tbody>
                            <?php foreach ($villages as $i => $v): ?>
                                <tr>
                                    <td class="rank"><a class="iconButtonV2<?=$i ? '' : ' active'; ?>" href="#" data-village="<?=$v['kid']; ?>"><span><?=$i + 1; ?></span></a></td>
                                    <td class="name" title="<?=$e($v['name']); ?>"><?=$e($v['name']); ?></td>
                                    <td class="strength"><span><?=$fmt($v['off']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div data-mode-view="defence" style="display:none">
                        <?=$serverAllianceRank('Defensive', $def['rank']['server'], $def['rank']['alliance'], $ms['hasAlliance'],
                            strtr($t('yourDefensiveRank', 'Your rank in the defensive strength ranking of this server: [RANK]'), ['[RANK]' => $rank($def['rank']['server'])]),
                            strtr($t('yourDefensiveRankInAlly', 'Your rank in the defensive strength ranking of your alliance: [RANK]'), ['[RANK]' => $rank($def['rank']['alliance'])])); ?>
                        <table class="villageList transparent">
                            <thead><tr><th></th><th><i class="village_medium"></i></th><th><i class="defence_medium"></i></th></tr></thead>
                            <tbody>
                            <?php foreach ($villages as $i => $v): ?>
                                <tr>
                                    <td class="rank"><span class="iconButtonV2 static"><span><?=$i + 1; ?></span></span></td>
                                    <td class="name" title="<?=$e($v['name']); ?>"><?=$e($v['name']); ?></td>
                                    <td class="strength"><span><?=$fmt($v['def']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="chartContainer">
                <div data-mode-view="offence">
                    <?php foreach ($villages as $i => $v):
                        $better = $v['offRank']['server'] - 1;
                        $worse = max(0, $ms['totalVillages'] - $v['offRank']['server']); ?>
                        <div data-village-view="<?=$v['kid']; ?>"<?=$i ? ' style="display:none"' : ''; ?>>
                            <?=$comparison('strengthRankChart', $t('offensiveCompare', 'Offensive strength comparison'), $worse, $better, 'village_medium', T('Statistics', 'tabs.villages'),
                                strtr($t('numOfVillagesWorseThanVillage', 'Number of villages ranked below village [VILLAGE]'), ['[VILLAGE]' => $v['name']]),
                                strtr($t('numOfVillagesBetterThanVillage', 'Number of villages ranked above village [VILLAGE]'), ['[VILLAGE]' => $v['name']])); ?>
                            <div class="chartDetails">
                                <div>
                                    <div class="strength"><div class="inlineIcon"><i class="offence_medium"></i><span><?=$fmt($v['off']); ?></span></div></div>
                                    <div class="name"><div class="inlineIcon"><i class="village_medium"></i><span><?=$e($v['name']); ?></span></div></div>
                                    <?=$e($t('offensiveCompareDescription', 'Offensive strength in the selected village')); ?>
                                    <div class="rankDisplay"><?=$rank($v['offRank']['server']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div data-mode-view="defence" style="display:none">
                    <?php $better = $def['rank']['server'] - 1;
                    $worse = max(0, $ms['totalPlayers'] - $def['rank']['server']); ?>
                    <?=$comparison('strengthRankChart', $t('defensiveCompare', 'Defensive strength comparison'), $worse, $better, 'player_medium', T('Statistics', 'tabs.players'),
                        $t('numOfVillagesWorse', 'Number of players with a weaker defence than yours'),
                        $t('numOfVillagesBetter', 'Number of players with a stronger defence than yours')); ?>
                    <div class="chartDetails">
                        <div>
                            <div class="strength"><div class="inlineIcon"><i class="defence_medium"></i><span><?=$fmt($def['total']); ?></span></div></div>
                            <?=$e($t('defensiveCompareDescription', 'sum of the average defence against infantry and cavalry in all your villages')); ?>
                            <div class="defenceSplit">
                                <span title="<?=$e($t('defenceAgainstInfantry', 'Defence against infantry')); ?>"><i class="defenceInfantry"></i><?=$e($t('defenceAgainstInfantry', 'Defence against infantry')); ?>: <b><?=$fmt($def['infantry']); ?></b></span>
                                <span title="<?=$e($t('defenceAgainstCavalry', 'Defence against cavalry')); ?>"><i class="defenceCavalry"></i><?=$e($t('defenceAgainstCavalry', 'Defence against cavalry')); ?>: <b><?=$fmt($def['cavalry']); ?></b></span>
                            </div>
                            <div class="rankDisplay"><?=$rank($def['rank']['server']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$cp = $d['culturePointsRank'];
$cpModes = ['perDay' => ['clock_medium', $t('perDay', 'Per day')], 'soFar' => ['culturePoints_medium', $t('soFar', 'So far')]];
$cpRankKeys = [
    'perDay' => ['CPperDay', $t('yourCPperDayRank', 'Your rank in the culture point production ranking of this server: [RANK]'), $t('yourCPperDayRankInAlly', 'Your rank in the culture point production ranking of your alliance: [RANK]'), $t('cpProduction', 'Culture point production')],
    'soFar' => ['CPsoFar', $t('yourCPsoFarRank', 'Your rank by culture points produced so far on this server: [RANK]'), $t('yourCPsoFarRankInAlly', 'Your rank by culture points produced so far in your alliance: [RANK]'), $t('cpProductionSoFar', 'Culture points produced so far')],
];
$cpSources = [
    'buildings' => ['#ffd667', $t('cpBuildings', 'Buildings')],
    'celebrations' => ['#dd2e45', $t('cpCelebrations', 'Celebrations')],
    'items' => ['#31a1ed', $t('cpItems', 'Items')],
    'rewards' => ['#a9d18e', $t('cpRewards', 'Rewards')],
    'allianceBonus' => ['#8ea3d1', $t('cpAllianceBonus', 'Alliance bonus')],
];
?>
<div id="culturePointsRank" class="statisticsSection statsSwitchScope" data-section="culturePointsRank">
    <div class="culturePointsHeader">
        <?=$switch; ?>
        <h4><i class="culturePointsRank"></i> <?=$e($t('culturePointsRank', 'Culture points rank')); ?></h4>
        <div class="dataSwitchButtons">
            <a href="#" class="iconButtonV2 active" data-switch="rank" title="<?=$e($t('cpRank', 'Rank')); ?>"><i class="culturePoints_small"></i></a>
            <a href="#" class="iconButtonV2" data-switch="distribution" title="<?=$e($t('cpDistribution', 'Distribution')); ?>"><i class="distribution"></i></a>
            <a href="#" class="iconButtonV2" data-switch="progression" title="<?=$e($t('cpProgression', 'Progression')); ?>"><i class="progression"></i></a>
        </div>
    </div>
    <div class="chartWithLegend statsModeScope" id="cpRankComparison" data-switch-view="rank">
        <div class="legendContainer">
            <?=$legendTabs($cpModes); ?>
            <div class="simpleLegend">
                <?php $first = true;
                foreach (['perDay', 'soFar'] as $mode) {
                    [$class, $tipServer, $tipAlly, $label] = $cpRankKeys[$mode];
                    $r = $cp[$mode]['rank'];
                    echo $modeView($mode, $serverAllianceRank($class, $r['server'], $r['alliance'], $cp['hasAlliance'],
                            strtr($tipServer, ['[RANK]' => $rank($r['server'])]), strtr($tipAlly, ['[RANK]' => $rank($r['alliance'])]))
                        . '<div class="cpProduction">' . $e($label) . ': <span>' . $fmt($cp[$mode]['value']) . '</span></div>', $first);
                    $first = false;
                } ?>
            </div>
        </div>
        <div class="chartContainer">
            <?php $first = true;
            foreach (['perDay', 'soFar'] as $mode) {
                $r = $cp[$mode]['rank']['server'];
                echo $modeView($mode, '<div class="rankDisplay">' . $rank($r) . '</div>'
                    . $comparison('culturePointsRankChart', $t('cpRankComparison', 'Rank comparison by culture points'), max(0, $cp['totalPlayers'] - $r), max(0, $r - 1),
                        'player_medium', T('Statistics', 'tabs.players')), $first);
                $first = false;
            } ?>
        </div>
    </div>
    <div class="chartWithLegend statsModeScope" id="cpDistributionChart" data-switch-view="distribution" style="display:none">
        <div class="legendContainer">
            <?=$legendTabs($cpModes); ?>
            <div class="simpleLegend">
                <?php $first = true;
                foreach (['perDay', 'soFar'] as $mode) {
                    $rows = '';
                    $sum = 0;
                    foreach ($cpSources as $key => [$color, $label]) {
                        $value = (int)($cp['distribution'][$mode][$key] ?? 0);
                        $sum += $value;
                        $rows .= '<tr><td>' . $e($label) . '</td><td><span class="' . $key . '">' . $fmt($value) . '</span></td></tr>';
                    }
                    $rows .= '<tr><td>' . $e($t('total', 'Total')) . ':</td><td><span>' . $fmt($sum) . '</span></td></tr>';
                    echo $modeView($mode, '<table class="transparent"><tbody>' . $rows . '</tbody></table>', $first);
                    $first = false;
                } ?>
            </div>
        </div>
        <div class="chartContainer">
            <div class="chartTitle"><?=$e($t('cpRankDistribution', 'Culture point distribution')); ?></div>
            <?php $first = true;
            foreach (['perDay', 'soFar'] as $mode) {
                $slices = [];
                foreach ($cpSources as $key => [$color, $label]) {
                    $value = (int)($cp['distribution'][$mode][$key] ?? 0);
                    $slices[] = ['value' => $value, 'color' => $color, 'title' => $label . ': ' . $fmt($value)];
                }
                echo $modeView($mode, Chart::pie($slices, ''), $first);
                $first = false;
            } ?>
        </div>
    </div>
    <div class="chartWithLegend statsModeScope" id="cpProgressionChart" data-switch-view="progression" style="display:none">
        <div class="legendContainer">
            <?=$legendTabs($cpModes); ?>
            <div class="simpleLegend">
                <div class="chartTitle"><?=$e($t('cpRankProgression', 'Culture point progression')); ?></div>
                <?php $first = true;
                foreach (['perDay', 'soFar'] as $mode) {
                    echo $modeView($mode, Chart::area($cp['progression'][$mode], [
                        'width' => 520, 'height' => 228, 'fill' => '#ffd667', 'stroke' => '#c79a1e', 'format' => $short,
                        'tooltip' => static function ($day, $v) use ($dayTip, $fmt) {
                            return $dayTip($day, $fmt($v));
                        },
                    ]), $first);
                    $first = false;
                } ?>
            </div>
        </div>
    </div>
</div>
</div>
<style>
    #statisticsV2 .statisticsSection.collapsed > :not(.openedClosedSwitch):not(h4):not(.culturePointsHeader),
    #statisticsV2 .statisticsSection.collapsed .culturePointsHeader .dataSwitchButtons { display: none !important; }
    #statisticsV2 .statisticsSection.collapsed h4 { margin-bottom: 30px; }
    #statisticsV2 .openedClosedSwitch { cursor: pointer; }
    #statisticsV2 .ServerAllianceRank .typeWrapper.disabled .rankDisplay { opacity: .5; }
    #statisticsV2 .legendTabs .tab span { margin-inline-start: 4px; }
    #statisticsV2 .legendTabs .tab { white-space: nowrap; }
    #statisticsV2 .chartWithLegend .chartContainer { position: relative; }
    #statisticsV2 #cpDistributionChart .chartContainer .chartTitle { top: 0; left: 0; }
    #statisticsV2 #cpDistributionChart .chartContainer .recharts-wrapper { top: 30px; margin-bottom: 30px; }
    #statisticsV2 .iconButtonV2.disabled { pointer-events: none; opacity: .5; }
    #statisticsV2 .iconButtonV2.static { cursor: default; }
    #statisticsV2 .statisticsDots circle:hover { fill-opacity: 1; }
    #statisticsV2 .raidChart, #statisticsV2 .tradeChart { position: relative; }
    #statisticsV2 .raidChart .recharts-wrapper, #statisticsV2 .tradeChart .recharts-wrapper { margin-top: 8px; }
    #statisticsV2 .recharts-wrapper .recharts-cartesian-axis text { font-size: 11px; }
    #statisticsV2 #militaryStrengthRank .chartDetails .defenceSplit { margin: 10px 0; display: flex; flex-direction: column; gap: 4px; }
    #statisticsV2 #cpProgressionChart { grid-template-columns: 100%; }
    #statisticsV2 #cpProgressionChart .legendTabs { width: 233px; }
    #statisticsV2 #cpProgressionChart .simpleLegend { min-height: 0; }
    #statisticsV2 #cpProgressionChart .simpleLegend .chartTitle { text-align: right; padding: 0 10px; }
    #statisticsV2 #cpDistributionChart .simpleLegend table { width: 100%; }
</style>
<script type="text/javascript">
    (function () {
        var root = document.getElementById('statisticsV2');
        if (!root) {
            return;
        }
        var show = function (scope, attr, value) {
            Array.prototype.forEach.call(scope.querySelectorAll('[' + attr + ']'), function (el) {
                // only the views of this scope, not those of a nested scope of the same kind
                el.style.display = el.getAttribute(attr) === value ? '' : 'none';
            });
        };
        var closest = function (el, cls) {
            while (el && el !== root) {
                if (el.classList && el.classList.contains(cls)) {
                    return el;
                }
                el = el.parentNode;
            }
            return null;
        };
        root.addEventListener('click', function (event) {
            var target = event.target;
            var tab = target.closest ? target.closest('.legendTabs .tab') : null;
            if (tab && root.contains(tab)) {
                var scope = closest(tab, 'statsModeScope');
                Array.prototype.forEach.call(tab.parentNode.children, function (t) {
                    t.classList.toggle('active', t === tab);
                });
                show(scope, 'data-mode-view', tab.getAttribute('data-mode'));
                return;
            }
            var button = target.closest ? target.closest('a.iconButtonV2') : null;
            if (button && root.contains(button)) {
                event.preventDefault();
                if (button.classList.contains('disabled')) {
                    return;
                }
                if (button.hasAttribute('data-switch')) {
                    var switchScope = closest(button, 'statsSwitchScope');
                    Array.prototype.forEach.call(button.parentNode.children, function (b) {
                        b.classList.toggle('active', b === button);
                    });
                    show(switchScope, 'data-switch-view', button.getAttribute('data-switch'));
                } else if (button.hasAttribute('data-village')) {
                    var villageScope = closest(button, 'statsVillageScope');
                    Array.prototype.forEach.call(villageScope.querySelectorAll('a[data-village]'), function (b) {
                        b.classList.toggle('active', b === button);
                    });
                    show(villageScope, 'data-village-view', button.getAttribute('data-village'));
                }
                return;
            }
            var toggle = target.closest ? target.closest('.openedClosedSwitch') : null;
            if (toggle && root.contains(toggle)) {
                var section = closest(toggle, 'statisticsSection');
                var collapsed = !section.classList.contains('collapsed');
                section.classList.toggle('collapsed', collapsed);
                section.classList.toggle('expanded', !collapsed);
                toggle.classList.toggle('switchOpened', !collapsed);
                toggle.classList.toggle('switchClosed', collapsed);
                try {
                    window.localStorage.setItem('statisticsSection' + section.getAttribute('data-section'), collapsed ? 'collapsed' : 'expanded');
                } catch (e) {
                }
            }
        });
        Array.prototype.forEach.call(root.querySelectorAll('.statisticsSection'), function (section) {
            var state = null;
            try {
                state = window.localStorage.getItem('statisticsSection' + section.getAttribute('data-section'));
            } catch (e) {
            }
            if (state === 'collapsed') {
                section.classList.add('collapsed');
                section.classList.remove('expanded');
                var toggle = section.querySelector('.openedClosedSwitch');
                toggle.classList.remove('switchOpened');
                toggle.classList.add('switchClosed');
            }
        });
    })();
</script>
