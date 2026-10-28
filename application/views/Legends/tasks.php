<?php
/*
 * "Your tasks" – markup of the Travian Legends task list (main branch: resources/Templates/tasks/main.php, Legends 655)
 * and of the task details (.taskDetailview). Clicking a task opens its details; "Vyzvednout" collects the reward with
 * the Legends animation (application/legends/tasks.js).
 */
$e = [LegendsUi::class, 'e'];
$t = [LegendsUi::class, 't'];
$base = 'tasks.php?s=' . $vars['scope'];
$num = function ($n) { $n = (int)$n; return abs($n) < 10000 ? (string)$n : number_format($n, 0, ',', ' '); };   // Legends: 1680, 12 500
$valueBox = function (string $icon, $value, string $title) use ($e, $num) {
    return '<div class="iconValueBoxWrapper" title="' . $e($title) . '"><div class="iconValueBox ' . $icon . '"><i class="' . $icon . '"></i><span>' . $num($value) . '</span></div></div>';
};
$label = function (array $task, string $prefix = '') use ($e, $t, $num) {
    if ($task['type'] === 'level') {
        return $e($t('TASKS_LEVEL', 'Level')) . ' <span>' . $task['value'] . '</span>/<span>' . $task['goal'] . '</span>';
    }
    if ($task['type'] === 'fields') {
        return '<span>' . $task['value'] . '</span>/<span>' . $task['goal'] . '</span> ' . $e(sprintf($t('TASKS_TO_LEVEL', 'to level %d'), $task['level']));
    }
    // the first span is the prefix of the task details ("Obyvatelé 197/250"); the overview hides it
    return '<span>' . $e($prefix) . ($prefix !== '' ? ' ' : '') . '</span><span>' . $num($task['value']) . '</span>/<span>' . $num($task['goal']) . '</span>';
};
$collectedText = $t('TASKS_COLLECTED', 'Collected');
/** Progress cell: bar while open, "Vyzvednout" when reached, the greyed "Vyzvednuto" once collected. */
$progress = function (array $task, string $collectUrl, string $prefix = '') use ($e, $t, $label, $collectedText) {
    if ($task['state'] === 'collected') {
        return LegendsUi::button($e($collectedText) . '<i class="collectedCheckmark"></i>',
            ['class' => 'collect collected preventAnimation', 'disabled' => true, 'appearance' => 'grey', 'data' => ['text-collected' => $collectedText]]);
    }
    if ($task['state'] === 'open' || $task['state'] === 'future') {   // future = a later step: its progress only
        return '<div class="progressBar"><div class="bar"><div class="filling" style="width:' . round(min(1, $task['ratio']) * 100) . '%"></div><div class="decoration"></div></div>'
            . '<div class="label">' . $label($task, $prefix) . '</div></div>';
    }
    return LegendsUi::button($e($t('TASKS_COLLECT', 'Collect')) . '<i class="collectedCheckmark"></i>', ['class' => 'collect',
        'data' => ['collect' => $collectUrl, 'text-collected' => $collectedText]]);
};
$rewards = function (array $task) use ($valueBox, $t) {
    return $valueBox('allResources', $task['rewardResources'], $t('TASKS_REWARD_RESOURCES_HERO', 'Resources of each type – into the hero\'s inventory'))
        . $valueBox('xp', $task['rewardExperience'], $t('TASKS_REWARD_XP', 'Hero experience'));
};
$rewardBonus = function () use ($vars, $e, $t) {
    return '<div class="rewardBonus"><div class="heroLevel"><div class="level">' . (int)$vars['heroLevel'] . '</div>'
        . '<div class="description">' . $e($t('TASKS_HERO_LEVEL', 'Hero level')) . '</div></div>'
        . '<div class="rewardInfo"><h4>' . $e(sprintf($t('TASKS_BONUS', 'Bonus rewards = %d%%'), $vars['bonus'])) . '</h4>'
        . '<div>' . $e($t('TASKS_BONUS_TEXT', 'Your hero gives you bonus rewards depending on his level.')) . '</div></div>'
        . '<div class="nextLevel"><div class="level">' . ((int)$vars['heroLevel'] + 1) . '</div>'
        . '<div class="description">' . $e(sprintf($t('TASKS_NEXT_LEVEL', 'Next level = %d%%'), $vars['nextBonus'])) . '</div></div></div>';
};
$collectUrl = function (string $key, array $extra = []) use ($base, $vars) {
    $q = '&collect=' . rawurlencode($key) . '&c=' . rawurlencode($vars['checker']);
    foreach ($extra as $k => $v) $q .= '&' . $k . '=' . rawurlencode((string)$v);
    return $base . $q;
};
?>
<div id="tasks" data-checker="<?php echo $e($vars['checker']); ?>">
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

    <?php if (isset($vars['detail'])) {
        $d = $vars['detail'];
        $detailBase = $base . '&t=' . rawurlencode($d['key']);
        ?>
        <div class="taskDetailview" data-task="<?php echo $e($d['key']); ?>">
            <div class="taskDescription">
                <div class="imageFrame"><div class="taskImage description"><img src="img/x.gif" class="<?php echo $e($d['image']); ?>" alt=""></div></div>
                <div class="description">
                    <div class="title"><?php echo $e($d['title']); ?></div>
                    <div class="text"><?php echo $e($d['description']); ?></div>
                </div>
            </div>
            <?php foreach ($vars['steps'] as $step) {
                $state = $step['state'];
                ?>
                <div class="task level <?php echo $e(explode(' ', $d['image'])[0]); ?> <?php echo $state; ?>" data-task="<?php echo $e($d['key']); ?>">
                    <div class="taskImage level"><img src="img/x.gif" class="<?php echo $e($d['image']); ?>" alt=""></div>
                    <div class="title"><?php echo $e($step['title']); ?></div>
                    <div class="rewards"><?php echo $rewards($step); ?></div>
                    <div class="progress"><?php echo $progress($step, $collectUrl($d['key'], ['t' => $d['key'], 'dp' => $vars['detailPage']]), $d['title']); ?></div>
                </div>
            <?php } ?>
            <?php echo $vars['detailPages'] > 1 ? LegendsUi::pagination($vars['detailPage'], $vars['detailPages'], $detailBase . '&dp=') : '<div class="divider"></div>'; ?>
            <?php echo $rewardBonus(); ?>
            <div class="buttonWrapper">
                <?php echo LegendsUi::button($e($t('TASKS_BACK', 'Back to overview')), ['class' => 'backToOverview', 'onclick' => "window.location.href='" . $base . "'"]); ?>
            </div>
        </div>
    <?php } else { ?>
        <?php foreach ($vars['tasks'] as $task) { ?>
            <div class="task group <?php echo $e($task['key']); ?> <?php echo $e(explode(' ', $task['image'])[0]); ?> <?php echo $task['state']; ?>"
                 data-task="<?php echo $e($task['key']); ?>" data-href="<?php echo $e($base . '&t=' . rawurlencode($task['key'])); ?>">
                <div class="taskImage group"><img src="img/x.gif" class="<?php echo $e($task['image']); ?>" alt=""></div>
                <div class="titleDecoration"></div>
                <div class="title"><?php echo $e($task['title']); ?></div>
                <div class="rewards"><?php echo $rewards($task); ?></div>
                <div class="progress"><?php echo $progress($task, $collectUrl($task['key'], ['p' => $vars['page']])); ?></div>
            </div>
        <?php } ?>

        <?php echo $vars['pages'] > 1 ? LegendsUi::pagination($vars['page'], $vars['pages'], $base . '&p=') : '<div class="divider"></div>'; ?>

        <?php echo $rewardBonus(); ?>
    <?php } ?>
</div>
<script type="text/javascript" src="application/legends/tasks.js?v=<?php echo Skin::VERSION; ?>"></script>
