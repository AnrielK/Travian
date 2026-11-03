<?php
/* Adventure list in the Travian Legends look (HeroAdventureCtrl, graphic packs with heroV2). */

use resources\View\LegendsUi as UI;

$e = [UI::class, 'e'];
$canStart = $vars['status'] === 100 && !$vars['noRallyPoint'];
$fields = static fn(float $d) => number_format($d, 2, ',', ' ');
// "00:23:56" as in Legends 692.2
$duration = static fn(string $time) => preg_replace('/^(\d):/', '0$1:', $time);
$videoIcon = '<svg viewBox="0 0 190 220.5" class="videoIcon"><rect width="173.7" height="118.6" x="14.3" y="99.7" fill="#3969c4" stroke="#000" stroke-width="4" rx="11.5"/>'
    . '<rect width="152.3" height="96.3" x="25" y="111" fill="#72a7f2" stroke="#1b1464" stroke-width="4" rx="5.8"/>'
    . '<path fill="#fff" stroke="#000" stroke-width="7" d="M79.3 184.1v-52.5c0-6 5.2-10 9.8-7.5l48.9 26.3c5.4 2.9 5.4 12.1 0 15l-48.9 26.3c-4.6 2.5-9.8-1.5-9.8-7.6Z"/>'
    . '<path fill="#efefef" stroke="#000" stroke-width="3" d="M5.6 74.4 149.3 2.2c3.9-1.9 8.9.3 11.1 4.7l11.2 22.2c2.2 4.4.8 9.5-3.1 11.4L24.7 112.4c-3.9 1.9-8.9-.3-11.1-4.7L2.5 85.8c-2.2-4.4-.8-9.5 3.1-11.4Z"/></svg>';
$forward = '<svg viewBox="0 0 205 140"><path d="m112.2 2.6 84.7 52.7c10.7 6.7 10.8 22.3.1 29l-84.7 53.1c-11.4 7.1-26.2-1-26.3-14.5L86 17.1c0-13.4 14.8-21.6 26.2-14.5Z"/>'
    . '<path fill="#b954e3" d="m106 13.9 84.5 52.5c2.5 1.6 2.5 5.3 0 6.9l-84.4 52.9c-2.7 1.7-6.2-.2-6.2-3.4l-.1-105.5c0-3.2 3.5-5.1 6.2-3.4Z"/>'
    . '<path d="m26.2 2.6 84.7 52.7c10.7 6.7 10.8 22.3.1 29l-84.7 53.1C14.9 144.5.1 136.3.1 122.9L0 17.1C0 3.7 14.8-4.5 26.2 2.6Z"/>'
    . '<path fill="#b954e3" d="m20 13.9 84.5 52.5c2.5 1.6 2.5 5.3 0 6.9l-84.4 52.9c-2.7 1.7-6.2-.2-6.2-3.4L13.8 17.3c0-3.2 3.5-5.1 6.2-3.4Z"/></svg>';
$arrow = '<svg class="advantageBonusArrow videoFeature" viewBox="0 0 160 180"><path class="border" d="M158.78 106.45 85.17 2.72C84.06 1.01 82.14 0 80.03 0S76 1.01 74.79 2.72L1.17 106.45c-1.41 1.91-1.51 4.53-.5 6.64 1.11 2.11 3.23 3.42 5.65 3.42h31.76v57.15c0 3.52 2.82 6.34 6.35 6.34h70.89c3.53 0 6.35-2.82 6.35-6.34v-57.15h31.97c2.42 0 4.54-1.31 5.65-3.42 1.11-2.11.91-4.63-.5-6.64Z"/>'
    . '<path class="inner" d="M108.45 167.41h-57v-63.89H20.52l59.44-85.03 59.02 85.1-30.53-.05v63.87z"/></svg>';
// Legends offers a shorter or a harder next adventure for watching a video; OpenVillage shows no ads, so the buttons are disabled.
$videoBox = static function (string $key, string $svg) use ($e, $videoIcon): string {
    return '<div class="videoFeatureBonusBox ' . $key . ' watchReady"><div class="videoFeatureBonusIcon ' . $key . ' bonus"><i class="bonusIcon"></i>' . $svg . '</div>'
        . '<h4 class="descriptionTitle">' . $e(T('HeroV2', $key . 'Title', $key)) . '</h4>'
        . '<div class="featureDescription">' . T('HeroV2', $key . 'Desc', '') . '</div>'
        . '<div class="bonusStatus watchReady"><div class="watchVideo"><button class="textButtonV2 buttonFramed withTextAndIcon rectangle withText purple disabled" type="button" disabled'
        . ' title="' . $e(T('Tasks', 'videoUnavailable', 'No videos are available.')) . '"><div><span>' . $e(T('HeroV2', 'watchVideo', 'Watch video')) . '</span>' . $videoIcon . '</div></button></div></div></div>';
};
?>
<div id="heroAdventure" class="contentV2">
    <div class="heroState">
        <span class="heroStatusMessage"><img src="img/x.gif" class="heroStatus<?=(int)$vars['status']; ?>" alt=""></span>
        <div><?=$vars['noRallyPoint'] ? '<span class="noRallyPointInHomeVillage">' . T('HeroAdventure', 'No rallyPoint') . '</span>' : $vars['statusMessage']; ?></div>
    </div>
    <?=$videoBox('adventureDuration', $forward); ?>
    <?=$videoBox('adventureDifficulty', $arrow); ?>
    <table class="adventureList borderGap">
        <thead>
        <tr>
            <th class="place"><?=$e(T('HeroV2', 'adventurePlace', 'Place')); ?></th>
            <th class="distance"><?=$e(T('HeroV2', 'adventureDistance', 'Distance')); ?></th>
            <th class="duration"><?=$e(T('HeroAdventure', 'moveTime')); ?></th>
            <th class="difficulty"><?=$e(ucfirst(T('HeroAdventure', 'difficulty'))); ?></th>
            <th class="button"></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$vars['rows']): ?>
            <tr><td colspan="5" class="noAdventures"><?=$e(T('HeroAdventure', 'no adventures')); ?></td></tr>
        <?php endif; ?>
        <?php foreach ($vars['rows'] as $row): ?>
            <tr id="adventure<?=$row['kid']; ?>">
                <td class="place"><img src="img/x.gif" class="<?=$e($row['place']); ?>" alt=""></td>
                <td class="distance"><?=$e(sprintf(T('HeroV2', 'adventureFields', '%s fields'), $fields($row['distance']))); ?></td>
                <td class="duration"><div class="cellWrapper"><div class="iconWrapper"><i class="clock_medium"></i></div><div class="duration"><?=$e($duration($row['duration'])); ?></div></div></td>
                <td class="difficulty">
                    <div class="iconWrapper" title="<?=$e(T('HeroAdventure', $row['hard'] ? 'hard' : 'normal') . ' · ' . T('HeroAdventure', 'timeLeft') . ': ' . secondsToString($row['timeLeft'])); ?>">
                        <i class="<?=$row['hard'] ? 'difficulty_hard' : 'difficulty_normal'; ?>"></i>
                    </div>
                </td>
                <td class="button">
                    <?php if ($row['onTheWay']): ?>
                        <span class="onTheWay"><?=$e(T('HeroAdventure', 'Hero on his way')); ?></span>
                    <?php else: ?>
                        <form method="post" action="start_adventure.php">
                            <input type="hidden" name="send" value="1">
                            <input type="hidden" name="kid" value="<?=$row['kid']; ?>">
                            <input type="hidden" name="from" value="list">
                            <input type="hidden" name="a" value="1">
                            <?=UI::button($e(T('HeroV2', 'explore', 'Explore')), ['type' => 'submit', 'disabled' => !$canStart]); ?>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (!empty($vars['extraButton'])): ?>
        <div class="finishNow"><?=$vars['extraButton']; ?></div>
    <?php endif; ?>
</div>
