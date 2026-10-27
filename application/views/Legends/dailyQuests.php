<?php
/* Daily quests in the Travian Legends 655 markup (main branch: resources/Templates/dailyQuestT46/legends.php). */
$e = [LegendsUi::class, 'e'];
$t = [LegendsUi::class, 't'];
$points = (int)$vars['points'];
$readyToCollect = false;
foreach ($vars['milestones'] as $m) {
    $readyToCollect = $readyToCollect || ($m['reached'] && !$m['done']);
}
$reset = defined('questday23') ? trim(questday23, " ()") : '';
?>
<div id="dailyQuests" class="contentV2">
    <div class="screen active">
        <div>
            <h1><?php echo $e($t('DQ_TITLE', 'Collect daily rewards')); ?></h1>
            <div class="resetInfo"><?php echo $e($reset); ?></div>
            <div class="dailyProgress">
                <div class="content">
                    <div class="pointsSource"><div class="inner"></div></div>
                    <?php foreach ($vars['milestones'] as $m) { ?>
                        <div class="reward" title="<?php echo $e(strip_tags($m['description'])); ?>">
                            <?php echo LegendsUi::svg('rewardFrame', 'below'); ?>
                            <div class="rewardImage reward<?php echo $m['points']; ?> <?php echo $m['done'] ? 'completed' : ($m['reached'] ? 'achieved' : 'locked'); ?>"></div>
                            <?php if ($m['done']) echo LegendsUi::svg('check', 'check'); ?>
                            <?php echo LegendsUi::svg('rewardFrame', 'above'); ?>
                            <div class="points"><?php echo $m['points']; ?></div>
                        </div>
                    <?php } ?>
                    <div class="achievedPoints">
                        <?php echo LegendsUi::svg('laurel', 'laurel'); ?>
                        <?php echo LegendsUi::svg('rewardFrame', 'above'); ?>
                        <div class="achieved"><?php echo $points; ?></div>
                        <div class="points"><?php echo $e($t('DQ_POINTS', 'Points')); ?></div>
                    </div>
                </div>
                <div class="backgroundBox">
                    <?php for ($i = 0; $i < 4; $i++) echo LegendsUi::progressBar(['color' => 'green', 'primary' => max(0, min(25, $points - 25 * $i)) * 4]); ?>
                </div>
            </div>
            <div class="quests">
                <?php foreach ($vars['quests'] as $q) { ?>
                    <div class="dailyQuest<?php echo $q['done'] >= $q['steps'] ? ' completed' : ''; ?>">
                        <div class="titleDecoration"></div>
                        <div class="title"><?php echo $e($q['name']); ?></div>
                        <div class="taskTrack withSummary">
                            <?php for ($i = 0; $i < $q['steps']; ++$i) { ?>
                                <div class="pill<?php echo $i < $q['done'] ? ' completed' : ''; ?>"><?php echo LegendsUi::svg('laurelIcon', 'laurelIcon'); ?><span><?php echo (int)$q['reward']; ?></span></div>
                            <?php } ?>
                        </div>
                        <div class="questAchievedPoints<?php echo $q['done'] >= $q['steps'] ? ' completed' : ''; ?>">
                            <?php echo LegendsUi::svg('laurel', 'laurel'); ?>
                            <div class="points"><?php echo (int)$q['points']; ?>/<?php echo (int)$q['total']; ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="actionWrapper">
                <?php echo LegendsUi::button($e($t('DQ_COLLECT', 'Collect rewards')), ['class' => 'collectRewards', 'disabled' => !$readyToCollect,
                    'onclick' => "window.location.href='dailyquests.php?collect=1&c=" . rawurlencode($vars['checker']) . "'; this.disabled = true;"]); ?>
            </div>
        </div>
    </div>
</div>
