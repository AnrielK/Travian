<?php
/* "Your tasks" – markup of the Travian Legends task list (main branch: resources/Templates/tasks/main.php, Legends 655). */
$e = [LegendsUi::class, 'e'];
$t = [LegendsUi::class, 't'];
$base = 'tasks.php?s=' . $vars['scope'];
$num = function ($n) { return number_format((int)$n, 0, ',', ' '); };
$valueBox = function (string $icon, $value, string $title) use ($e, $num) {
    return '<div class="iconValueBoxWrapper" title="' . $e($title) . '"><div class="iconValueBox ' . $icon . '"><i class="' . $icon . '"></i><span>' . $num($value) . '</span></div></div>';
};
$label = function (array $task) use ($e, $t, $num) {
    if ($task['type'] === 'level') {
        return $e($t('TASKS_LEVEL', 'Level')) . ' <span>' . $task['value'] . '</span>/<span>' . $task['goal'] . '</span>';
    }
    if ($task['type'] === 'fields') {
        return '<span>' . $task['value'] . '</span>/<span>' . $task['goal'] . '</span> ' . $e(sprintf($t('TASKS_TO_LEVEL', 'to level %d'), $task['level']));
    }
    // the pack hides the first span for population / culture point tasks (Legends puts a prefix there)
    return '<span></span><span>' . $num($task['value']) . '</span>/<span>' . $num($task['goal']) . '</span>';
};
?>
<div id="tasks">
    <div class="contentNavi subNavi">
        <div class="container <?php echo $vars['scope'] === 'village' ? 'active' : 'normal'; ?>">
            <div class="background-start">&nbsp;</div><div class="background-end">&nbsp;</div>
            <div class="content"><a class="tabItem" href="tasks.php?s=village"><?php echo $e(sprintf($t('TASKS_VILLAGE', 'Tasks for village %s'), $vars['villageName'])); ?></a></div>
        </div>
        <div class="container <?php echo $vars['scope'] === 'general' ? 'active' : 'normal'; ?>">
            <div class="background-start">&nbsp;</div><div class="background-end">&nbsp;</div>
            <div class="content"><a class="tabItem" href="tasks.php?s=general"><?php echo $e($t('TASKS_GENERAL', 'General tasks')); ?></a></div>
        </div>
    </div>

    <?php foreach ($vars['tasks'] as $task) { ?>
        <div class="task group <?php echo $e($task['key']); ?> <?php echo $e(explode(' ', $task['image'])[0]); ?> <?php echo $task['state']; ?>">
            <div class="taskImage group"><img src="img/x.gif" class="<?php echo $e($task['image']); ?>" alt=""></div>
            <div class="titleDecoration"></div>
            <div class="title"><?php echo $e($task['title']); ?></div>
            <div class="rewards">
                <?php if ($task['state'] !== 'collected') {
                    echo $valueBox('allResources', $task['rewardResources'], $t('TASKS_REWARD_RESOURCES', 'Resources of each type'));
                    echo $valueBox('xp', $task['rewardExperience'], $t('TASKS_REWARD_XP', 'Hero experience'));
                } ?>
            </div>
            <div class="progress">
                <?php if ($task['state'] === 'collected') { ?>
                    <div class="allDone"><?php echo $e($t('TASKS_ALL_DONE', 'All steps completed')); ?></div>
                <?php } elseif ($task['state'] === 'open') { ?>
                    <div class="progressBar">
                        <div class="bar">
                            <div class="filling" style="width:<?php echo round(min(1, $task['ratio']) * 100); ?>%"></div>
                            <div class="decoration"></div>
                        </div>
                        <div class="label"><?php echo $label($task); ?></div>
                    </div>
                <?php } else {
                    echo LegendsUi::button($e($t('TASKS_COLLECT', 'Collect')) . '<i class="collectedCheckmark"></i>', ['class' => 'collect',
                        'onclick' => "window.location.href='" . $base . '&p=' . $vars['page'] . '&collect=' . rawurlencode($task['key']) . '&c=' . rawurlencode($vars['checker']) . "'; this.disabled = true;"]);
                } ?>
            </div>
        </div>
    <?php } ?>

    <?php echo $vars['pages'] > 1 ? LegendsUi::pagination($vars['page'], $vars['pages'], $base . '&p=') : '<div class="divider"></div>'; ?>

    <div class="rewardBonus">
        <div class="heroLevel">
            <div class="level"><?php echo (int)$vars['heroLevel']; ?></div>
            <div class="description"><?php echo $e($t('TASKS_HERO_LEVEL', 'Hero level')); ?></div>
        </div>
        <div class="rewardInfo">
            <h4><?php echo $e(sprintf($t('TASKS_BONUS', 'Bonus rewards = %d%%'), $vars['bonus'])); ?></h4>
            <div><?php echo $e($t('TASKS_BONUS_TEXT', 'Your hero gives you bonus rewards depending on his level.')); ?></div>
        </div>
        <div class="nextLevel">
            <div class="level"><?php echo (int)$vars['heroLevel'] + 1; ?></div>
            <div class="description"><?php echo $e(sprintf($t('TASKS_NEXT_LEVEL', 'Next level = %d%%'), $vars['nextBonus'])); ?></div>
        </div>
    </div>
</div>
