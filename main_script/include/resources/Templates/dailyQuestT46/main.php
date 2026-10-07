<?php
/* Daily quests (DailyQuestsCtrl) in the Travian Legends look. */
use Core\Helper\TimezoneHelper;

$h = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
$readyToCollect = false;
foreach ($vars['milestones'] as $m) {
    $readyToCollect = $readyToCollect || ($m['reached'] && !$m['done']);
}
$progress = min(100, $vars['points']);
?>
<div id="dailyQuestsT46">
    <h2 class="dqTitle"><?=$h(T('DailyQuest', 'Collect daily rewards')); ?></h2>
    <?php if ($vars['nextReset'] > 0): ?>
        <div class="dqReset"><?=$h(sprintf(T('Tasks', 'dailyReset', 'Next reset: %s. Collect your rewards before then!'),
                TimezoneHelper::autoDateString($vars['nextReset'], true))); ?></div>
    <?php endif; ?>

    <div class="dqRewardBar">
        <div class="dqTrack"><div class="dqTrackFill" style="width:<?=$progress; ?>%"></div></div>
        <div class="dqStart"></div>
        <?php foreach ($vars['milestones'] as $n => $m): ?>
            <div class="dqMilestone reward<?=$m['points']; ?><?=$m['reached'] ? ' reached' : ''; ?><?=$m['done'] ? ' done' : ''; ?>"
                 title="<?=$h(strip_tags($m['description'])); ?>" style="left:calc(30px + (100% - 150px) * <?=$m['points'] / 100; ?> - 30px)">
                <div class="dqChest"></div>
                <div class="dqMilestonePoints"><?=$m['points']; ?></div>
            </div>
        <?php endforeach; ?>
        <div class="dqPoints"><span><?=(int)$vars['points']; ?></span><small><?=$h(T('DailyQuest', 'points')); ?></small></div>
    </div>

    <div class="dqList">
        <?php foreach ($vars['quests'] as $q): ?>
            <div class="dqQuest<?=$q['done'] >= $q['steps'] ? ' completed' : ''; ?>">
                <div class="dqName"><?=$h($q['name']); ?></div>
                <div class="dqSteps steps<?=$q['steps']; ?>">
                    <?php for ($i = 0; $i < $q['steps']; ++$i): ?>
                        <div class="dqStep<?=$i < $q['done'] ? ' done' : ''; ?>"><i class="laurel"></i><?=$q['reward']; ?></div>
                    <?php endfor; ?>
                </div>
                <div class="dqWreath"><?=$q['points']; ?>/<?=$q['total']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="dqCollect">
        <button type="button" class="buttonFramed green rectangle withText<?=$readyToCollect ? '' : ' disabled'; ?>"
            <?=$readyToCollect ? '' : 'disabled'; ?>
                onclick="window.location.href='dailyquests.php?collect=1&amp;c=<?=$h(rawurlencode($vars['checker'])); ?>'; this.disabled = true;">
            <div><?=$h(T('Tasks', 'collectRewards', 'Collect rewards')); ?></div>
        </button>
    </div>
</div>
