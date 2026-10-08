<?php
/* Adventure list in the Travian Legends look (HeroAdventureCtrl, graphic packs with heroV2). */

use resources\View\LegendsUi as UI;

$e = [UI::class, 'e'];
$canStart = $vars['status'] === 100 && !$vars['noRallyPoint'];
$fields = static fn(float $d) => number_format($d, 2, ',', ' ');
?>
<div class="contentV2">
    <div class="heroState">
        <span class="heroStatusMessage"><img src="img/x.gif" class="heroStatus<?=(int)$vars['status']; ?>" alt=""></span>
        <div><?=$vars['noRallyPoint'] ? '<span class="noRallyPointInHomeVillage">' . T('HeroAdventure', 'No rallyPoint') . '</span>' : $vars['statusMessage']; ?></div>
    </div>
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
                <td class="duration"><div class="cellWrapper"><i class="clock_medium"></i><span class="duration"><?=$e($row['duration']); ?></span></div></td>
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
