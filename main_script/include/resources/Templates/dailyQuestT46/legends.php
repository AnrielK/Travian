<?php
/* Daily quests in the Travian Legends 655 markup (graphic packs with 'heroV2'); same data as dailyQuestT46/main.php.
   Links are data-href: js/Game/Legends/dailyQuests.js follows them in the dialog, or on this page. */

use Core\Helper\TimezoneHelper;
use resources\View\LegendsUi as UI;

$h = [UI::class, 'e'];
$points = (int)$vars['points'];
$readyToCollect = false;
foreach ($vars['milestones'] as $m) {
    $readyToCollect = $readyToCollect || ($m['reached'] && !$m['done']);
}
/** Steps of a quest (one pill per step) and its points, in the list and in the details. */
$track = static function (array $q): string {
    $html = '<div class="taskTrack withSummary">';
    for ($i = 0; $i < $q['steps']; ++$i) {
        $html .= '<div class="pill' . ($i < $q['done'] ? ' completed' : '') . '">' . UI::svg('laurelIcon', 'laurelIcon') . '<span>' . (int)$q['reward'] . '</span></div>';
    }
    return $html . '</div><div class="questAchievedPoints' . ($q['done'] >= $q['steps'] ? ' completed' : '') . '">'
        . UI::svg('laurel', 'laurel') . '<div class="points">' . (int)$q['points'] . '/' . (int)$q['total'] . '</div></div>';
};
$detail = $vars['detail'];
?>
<div id="dailyQuests" class="contentV2">
    <?php if ($detail): ?>
    <div class="screen active">
        <div id="dailyQuestsDetailsScreen" class="DailyQuest_<?=sprintf('%02d', $detail['id']); ?>">
            <?=UI::button('<svg viewBox="0 0 10.2 16.5"><path d="m.1 8.1-.1.2.1.2L7 15.9c.7.7 1.9.8 2.6.1.7-.7.8-1.8.1-2.5L5 8.3l4.7-5.2c.7-.7.7-1.9-.1-2.6-.7-.7-1.9-.7-2.6.1l-.1.1L.1 8.1z"/></svg><span>'
                . $h(T('HeroV2', 'back', 'Back')) . '</span>', ['secondary' => true, 'class' => 'backButton withTextAndIcon',
                'data' => ['href' => 'dailyquests.php']]); ?>
            <h1><?=$h($detail['name']); ?></h1>
            <div class="description"><?=$h($detail['description']); ?></div>
            <?php foreach ($detail['information'] as $information): ?>
                <div class="additionalInformation"><?=$h($information); ?></div>
            <?php endforeach; ?>
            <div class="dailyQuest"><?=$track($detail); ?></div>
        </div>
    </div>
    <?php else: ?>
    <div class="screen active">
        <div>
            <h1><?=$h(T('DailyQuest', 'legendsTitle', T('DailyQuest', 'Collect daily rewards'))); ?></h1>
            <div><?php if ($vars['nextReset'] > 0): ?><?=$h(($resetText = T('Tasks', 'dailyResetLegends', '')) !== ''
                    ? sprintf($resetText,TimezoneHelper::date(TimezoneHelper::getTimeFormat(false), $vars['nextReset']))
                    : sprintf(T('Tasks', 'dailyReset', 'Next reset: %s. Collect your rewards before then!'), TimezoneHelper::autoDateString($vars['nextReset'], true))); ?><?php endif; ?></div>
            <div class="dailyProgress">
                <div class="content">
                    <div class="pointsSource"><div class="inner"></div></div>
                    <?php foreach ($vars['milestones'] as $m): ?>
                        <div class="reward" title="<?=$h(strip_tags($m['description'])); ?>">
                            <?=UI::svg('rewardFrame', 'below'); ?>
                            <div class="rewardImage reward<?=$m['points']; ?> <?=$m['done'] ? 'completed' : ($m['reached'] ? 'achieved' : 'locked'); ?>"></div>
                            <?php if ($m['done']): ?><?=UI::svg('check', 'check'); ?><?php endif; ?>
                            <?=UI::svg('rewardFrame', 'above'); ?>
                            <div class="points"><?=$m['points']; ?></div>
                        </div>
                    <?php endforeach; ?>
                    <div class="achievedPoints">
                        <?=UI::svg('laurel', 'laurel'); ?>
                        <?=UI::svg('rewardFrame', 'above'); ?>
                        <div class="achieved"><?=$points; ?></div>
                        <div class="points"><?=$h(T('DailyQuest', 'pointsLegends', T('DailyQuest', 'points'))); ?></div>
                    </div>
                </div>
                <div class="backgroundBox">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <?=UI::progressBar(['color' => 'green', 'primary' => max(0, min(25, $points - 25 * $i)) * 4]); ?>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="quests">
                <?php foreach ($vars['quests'] as $q): ?>
                    <div class="dailyQuest<?=$q['done'] >= $q['steps'] ? ' completed' : ''; ?>" data-href="dailyquests.php?q=<?=(int)$q['id']; ?>">
                        <div class="titleDecoration"></div>
                        <div class="title"><?=$h($q['name']); ?></div>
                        <?=$track($q); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="actionWrapper">
                <?=UI::button($h(T('Tasks', 'collectRewards', 'Collect rewards')), ['class' => 'collectRewards', 'disabled' => !$readyToCollect,
                    'data' => ['href' => 'dailyquests.php?collect=1&c=' . rawurlencode($vars['checker'])]]); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
