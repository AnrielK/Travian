<?php
/* Daily quests in the Travian Legends 655 markup (graphic packs with 'heroV2'); same data as dailyQuestT46/main.php. */

use Core\Helper\TimezoneHelper;
use resources\View\LegendsUi as UI;

$h = [UI::class, 'e'];
$points = (int)$vars['points'];
$readyToCollect = false;
foreach ($vars['milestones'] as $m) {
    $readyToCollect = $readyToCollect || ($m['reached'] && !$m['done']);
}
?>
<div id="dailyQuests" class="contentV2">
    <div class="screen active">
        <div>
            <h1><?=$h(T('DailyQuest', 'Collect daily rewards')); ?></h1>
            <div><?php if ($vars['nextReset'] > 0): ?><?=$h(sprintf(T('Tasks', 'dailyReset', 'Next reset: %s. Collect your rewards before then!'),
                    TimezoneHelper::autoDateString($vars['nextReset'], true))); ?><?php endif; ?></div>
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
                        <div class="points"><?=$h(T('DailyQuest', 'points')); ?></div>
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
                    <div class="dailyQuest<?=$q['done'] >= $q['steps'] ? ' completed' : ''; ?>">
                        <div class="titleDecoration"></div>
                        <div class="title"><?=$h($q['name']); ?></div>
                        <div class="taskTrack withSummary">
                            <?php for ($i = 0; $i < $q['steps']; ++$i): ?>
                                <div class="pill<?=$i < $q['done'] ? ' completed' : ''; ?>"><?=UI::svg('laurelIcon', 'laurelIcon'); ?><span><?=(int)$q['reward']; ?></span></div>
                            <?php endfor; ?>
                        </div>
                        <div class="questAchievedPoints<?=$q['done'] >= $q['steps'] ? ' completed' : ''; ?>">
                            <?=UI::svg('laurel', 'laurel'); ?>
                            <div class="points"><?=(int)$q['points']; ?>/<?=(int)$q['total']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="actionWrapper">
                <?=UI::button($h(T('Tasks', 'collectRewards', 'Collect rewards')), ['class' => 'collectRewards', 'disabled' => !$readyToCollect,
                    'onclick' => "window.location.href='dailyquests.php?collect=1&c=" . rawurlencode($vars['checker']) . "'; this.disabled = true;"]); ?>
            </div>
        </div>
    </div>
</div>
