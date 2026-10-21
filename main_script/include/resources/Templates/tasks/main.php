<?php
/* "Your tasks" (TasksCtrl) – markup of the Travian Legends task list, styled by the #tasks rules of 29c89d54. */
$h = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
$base = 'tasks.php?s=' . $vars['scope'];
$v2 = is_hero_v2(); // Travian Legends 655: iconValueBox rewards, Legends progress bar and buttons, divider
$valueBox = static fn(string $icon, $value, string $title) => '<div class="iconValueBoxWrapper" title="' . $h($title) . '"><div class="iconValueBox ' . $icon . '"><i class="' . $icon . '"></i><span>' . number_format((int)$value, 0, ',', number_thousands_separator()) . '</span></div></div>';
$label = static function (array $task) use ($h) {
    if ($task['type'] === 'level') {
        return $h(T('Buildings', 'level')) . ' <span>' . $task['value'] . '</span>/<span>' . $task['goal'] . '</span>';
    }
    if ($task['type'] === 'fields') {
        return '<span>' . $task['value'] . '</span>/<span>' . $task['goal'] . '</span> ' . $h(sprintf(T('Tasks', 'toLevel', 'to level %d'), $task['level']));
    }
    // The pack hides the first span for population / culture point tasks (Legends puts a prefix there).
    return '<span></span><span>' . number_format($task['value'], 0, ',', number_thousands_separator()) . '</span>/<span>' . number_format($task['goal'], 0, ',', number_thousands_separator()) . '</span>';
};
?>
<div id="tasks">
    <div class="contentNavi subNavi">
        <div class="container<?=$vars['scope'] === 'village' ? ' active' : ''; ?>">
            <div class="content">
                <a class="tabItem" href="tasks.php?s=village"><?=$h(sprintf(T('Tasks', 'villageTasks', 'Tasks for village %s'), $vars['villageName'])); ?></a>
            </div>
        </div>
        <div class="container<?=$vars['scope'] === 'general' ? ' active' : ''; ?>">
            <div class="content">
                <a class="tabItem" href="tasks.php?s=general"><?=$h(T('Tasks', 'generalTasks', 'General tasks')); ?></a>
            </div>
        </div>
    </div>

    <?php foreach ($vars['tasks'] as $task): ?>
        <div class="task group <?=$h($task['key']); ?> <?=$h(explode(' ', $task['image'])[0]); ?> <?=$task['state']; ?>">
            <div class="taskImage group"><img src="img/x.gif" class="<?=$h($task['image']); ?>" alt=""></div>
            <div class="titleDecoration"></div>
            <div class="title"><?=$h($task['title']); ?></div>
            <div class="rewards">
                <?php if ($task['state'] !== 'collected' && $v2): ?>
                    <?=$valueBox('allResources', $task['rewardResources'], T('Tasks', 'rewardResources', 'Resources of each type')); ?>
                    <?=$valueBox('xp', $task['rewardExperience'], T('Tasks', 'rewardExperience', 'Hero experience')); ?>
                <?php elseif ($task['state'] !== 'collected'): ?>
                    <div class="reward resources" title="<?=$h(T('Tasks', 'rewardResources', 'Resources of each type')); ?>"><i class="resources_large"></i><?=number_format($task['rewardResources'], 0, ',', number_thousands_separator()); ?></div>
                    <div class="reward experience" title="<?=$h(T('Tasks', 'rewardExperience', 'Hero experience')); ?>"><i class="heroExperience_medium"></i><?=number_format($task['rewardExperience'], 0, ',', number_thousands_separator()); ?></div>
                <?php endif; ?>
            </div>
            <div class="progress">
                <?php if ($task['state'] === 'collected'): ?>
                    <div class="allDone"><?=$h(T('Tasks', 'allDone', 'All steps completed')); ?></div>
                <?php elseif ($v2): ?>
                    <?php if ($task['state'] === 'open'): ?>
                        <div class="progressBar">
                            <div class="bar">
                                <div class="filling" style="width:<?=round(min(1, $task['ratio']) * 100); ?>%"></div>
                                <div class="decoration"></div>
                            </div>
                            <div class="label"><?=$label($task); ?></div>
                        </div>
                    <?php else: ?>
                        <?=\resources\View\LegendsUi::button($h(T('Tasks', 'collect', 'Collect')) . '<i class="collectedCheckmark"></i>', ['class' => 'collect',
                            'onclick' => "window.location.href='" . $base . '&p=' . $vars['page'] . '&collect=' . rawurlencode($task['key']) . '&c=' . rawurlencode($vars['checker']) . "'; this.disabled = true;"]); ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="progressBar">
                        <div class="bar">
                            <div class="filling" style="width:<?=round(min(1, $task['ratio']) * 100); ?>%"></div>
                            <div class="decoration"></div>
                        </div>
                        <div class="label"><?=$label($task); ?></div>
                    </div>
                    <button type="button" class="buttonFramed green rectangle withText collect"
                            onclick="window.location.href='<?=$h($base . '&p=' . $vars['page'] . '&collect=' . rawurlencode($task['key']) . '&c=' . rawurlencode($vars['checker'])); ?>'; this.disabled = true;">
                        <div><?=$h(T('Tasks', 'collect', 'Collect')); ?></div></button>
                <?php endif; ?>
                <?php if (!$v2): ?><div class="stepCounter"><?=min($task['step'] + 1, $task['steps']); ?>/<?=$task['steps']; ?></div><?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($v2): ?>
        <?=$vars['pages'] > 1 ? \resources\View\LegendsUi::pagination($vars['page'], $vars['pages'], $base . '&p=') : '<div class="divider"></div>'; ?>
    <?php elseif ($vars['pages'] > 1): ?>
        <div class="taskPager">
            <a class="arrow previous<?=$vars['page'] <= 1 ? ' disabled' : ''; ?>" href="<?=$h($base . '&p=' . max(1, $vars['page'] - 1)); ?>"></a>
            <?php for ($p = 1; $p <= $vars['pages']; ++$p): ?>
                <a class="dot<?=$p === $vars['page'] ? ' active' : ''; ?>" href="<?=$h($base . '&p=' . $p); ?>"><span><?=$p; ?></span></a>
            <?php endfor; ?>
            <a class="arrow next<?=$vars['page'] >= $vars['pages'] ? ' disabled' : ''; ?>" href="<?=$h($base . '&p=' . min($vars['pages'], $vars['page'] + 1)); ?>"></a>
        </div>
    <?php endif; ?>

    <div class="rewardBonus">
        <div class="heroLevel">
            <div class="level"><?=(int)$vars['heroLevel']; ?></div>
            <div class="description"><?=$h(T('Tasks', 'heroLevel', 'Hero level')); ?></div>
        </div>
        <div class="rewardInfo">
            <h4><?=$h(sprintf(T('Tasks', 'bonusRewards', 'Bonus rewards = %d%%'), $vars['bonus'])); ?></h4>
            <div><?=$h(T('Tasks', 'bonusDescription', 'Your hero gives you bonus rewards depending on his level.')); ?></div>
        </div>
        <div class="nextLevel">
            <div class="level"><?=(int)$vars['heroLevel'] + 1; ?></div>
            <div class="description"><?=$h(sprintf(T('Tasks', 'nextLevel', 'Next level = %d%%'), $vars['nextBonus'])); ?></div>
        </div>
    </div>
</div>
