<?php
/*
 * "Your tasks" (TasksCtrl) – markup of the Travian Legends task list, styled by the #tasks rules of 29c89d54, and of
 * the task details (.taskDetailview, &t=<task>). Travian Legends 655: a click on a task opens its details, "Collect"
 * takes the reward without leaving the page with the Legends animation (js/Game/Legends/tasks.js).
 */
use resources\View\LegendsUi;

$h = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES);
$base = 'tasks.php?s=' . $vars['scope'];
$v2 = is_hero_v2(); // Travian Legends 655: iconValueBox rewards, Legends progress bar and buttons, divider
// Legends writes numbers below 10 000 without a separator (1680, 12 500)
$num = static fn($n) => abs((int)$n) < 10000 ? (string)(int)$n : number_format((int)$n, 0, ',', number_thousands_separator());
$valueBox = static fn(string $icon, $value, string $title) => '<div class="iconValueBoxWrapper" title="' . $h($title) . '"><div class="iconValueBox ' . $icon . '"><i class="' . $icon . '"></i><span>' . ($v2 ? $num($value) : number_format((int)$value, 0, ',', number_thousands_separator())) . '</span></div></div>';
$label = static function (array $task, string $prefix = '') use ($h, $num) {
    if ($task['type'] === 'level') {
        return $h(T('Buildings', 'level')) . ' <span>' . $task['value'] . '</span>/<span>' . $task['goal'] . '</span>';
    }
    if ($task['type'] === 'fields') {
        return '<span>' . $task['value'] . '</span>/<span>' . $task['goal'] . '</span> ' . $h(sprintf(T('Tasks', 'toLevel', 'to level %d'), $task['level']));
    }
    // The first span is the prefix of the task details ("Population 197/250"); the overview leaves it empty.
    return '<span>' . $h($prefix) . ($prefix !== '' ? ' ' : '') . '</span><span>' . $num($task['value']) . '</span>/<span>' . $num($task['goal']) . '</span>';
};
$collectedText = T('Tasks', 'collected', 'Collected');
$collectUrl = static function (string $key, array $extra = []) use ($base, $vars) {
    $query = '&collect=' . rawurlencode($key) . '&c=' . rawurlencode($vars['checker']);
    foreach ($extra as $k => $v) {
        $query .= '&' . $k . '=' . rawurlencode((string)$v);
    }
    return $base . $query;
};
/** Progress cell (Legends 655): bar while open, "Collect" when reached, the greyed "Collected" once collected. */
$progressV2 = static function (array $task, string $url, string $prefix = '') use ($h, $label, $collectedText) {
    if ($task['state'] === 'collected') {
        return LegendsUi::button($h($collectedText) . '<i class="collectedCheckmark"></i>',
            ['class' => 'collect collected preventAnimation', 'disabled' => true, 'appearance' => 'grey', 'data' => ['text-collected' => $collectedText]]);
    }
    if ($task['state'] === 'open' || $task['state'] === 'future') { // future = a later step of the details: its progress only
        return '<div class="progressBar"><div class="bar"><div class="filling" style="width:' . round(min(1, $task['ratio']) * 100) . '%"></div><div class="decoration"></div></div>'
            . '<div class="label">' . $label($task, $prefix) . '</div></div>';
    }
    return LegendsUi::button($h(T('Tasks', 'collect', 'Collect')) . '<i class="collectedCheckmark"></i>', ['class' => 'collect',
        'data' => ['collect' => $url, 'text-collected' => $collectedText]]);
};
$rewardsV2 = static fn(array $task) => $valueBox('allResources', $task['rewardResources'], T('Tasks', 'rewardResourcesHero', T('Tasks', 'rewardResources', 'Resources of each type')))
    . $valueBox('xp', $task['rewardExperience'], T('Tasks', 'rewardExperience', 'Hero experience'));
?>
<div id="tasks" data-checker="<?=$h($vars['checker']); ?>">
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

    <?php if (isset($vars['detail'])):
        $d = $vars['detail']; ?>
        <div class="taskDetailview" data-task="<?=$h($d['key']); ?>">
            <div class="taskDescription">
                <div class="imageFrame"><div class="taskImage description"><img src="img/x.gif" class="<?=$h($d['image']); ?>" alt=""></div></div>
                <div class="description">
                    <div class="title"><?=$h($d['title']); ?></div>
                    <div class="text"><?=$h($d['description']); ?></div>
                </div>
            </div>
            <?php foreach ($vars['steps'] as $step): ?>
                <div class="task level <?=$h(explode(' ', $d['image'])[0]); ?> <?=$step['state']; ?>" data-task="<?=$h($d['key']); ?>">
                    <div class="taskImage level"><img src="img/x.gif" class="<?=$h($d['image']); ?>" alt=""></div>
                    <div class="title"><?=$h($step['title']); ?></div>
                    <div class="rewards"><?=$rewardsV2($step); ?></div>
                    <div class="progress"><?=$progressV2($step, $collectUrl($d['key'], ['t' => $d['key'], 'dp' => $vars['detailPage']]), $d['type'] === 'count' ? $d['title'] : ''); ?></div>
                </div>
            <?php endforeach; ?>
            <?=$vars['detailPages'] > 1 ? LegendsUi::pagination($vars['detailPage'], $vars['detailPages'], $base . '&t=' . rawurlencode($d['key']) . '&dp=') : '<div class="divider"></div>'; ?>
            <?php $bonusAfterDetail = true; ?>
    <?php else: ?>
    <?php foreach ($vars['tasks'] as $task): ?>
        <div class="task group <?=$h($task['key']); ?> <?=$h(explode(' ', $task['image'])[0]); ?> <?=$task['state']; ?>"<?php if ($v2): ?> data-task="<?=$h($task['key']); ?>" data-href="<?=$h($base . '&t=' . rawurlencode($task['key'])); ?>"<?php endif; ?>>
            <div class="taskImage group"><img src="img/x.gif" class="<?=$h($task['image']); ?>" alt=""></div>
            <div class="titleDecoration"></div>
            <div class="title"><?=$h($task['title']); ?></div>
            <div class="rewards">
                <?php if ($task['state'] !== 'collected' && $v2): ?>
                    <?=$rewardsV2($task); ?>
                <?php elseif ($task['state'] !== 'collected'): ?>
                    <div class="reward resources" title="<?=$h(T('Tasks', 'rewardResources', 'Resources of each type')); ?>"><i class="resources_large"></i><?=number_format($task['rewardResources'], 0, ',', number_thousands_separator()); ?></div>
                    <div class="reward experience" title="<?=$h(T('Tasks', 'rewardExperience', 'Hero experience')); ?>"><i class="heroExperience_medium"></i><?=number_format($task['rewardExperience'], 0, ',', number_thousands_separator()); ?></div>
                <?php endif; ?>
            </div>
            <div class="progress">
                <?php if ($task['state'] === 'collected'): ?>
                    <div class="allDone"><?=$h(T('Tasks', 'allDone', 'All steps completed')); ?></div>
                <?php elseif ($v2): ?>
                    <?=$progressV2($task, $collectUrl($task['key'], ['p' => $vars['page']])); ?>
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
        <?=$vars['pages'] > 1 ? LegendsUi::pagination($vars['page'], $vars['pages'], $base . '&p=') : '<div class="divider"></div>'; ?>
    <?php elseif ($vars['pages'] > 1): ?>
        <div class="taskPager">
            <a class="arrow previous<?=$vars['page'] <= 1 ? ' disabled' : ''; ?>" href="<?=$h($base . '&p=' . max(1, $vars['page'] - 1)); ?>"></a>
            <?php for ($p = 1; $p <= $vars['pages']; ++$p): ?>
                <a class="dot<?=$p === $vars['page'] ? ' active' : ''; ?>" href="<?=$h($base . '&p=' . $p); ?>"><span><?=$p; ?></span></a>
            <?php endfor; ?>
            <a class="arrow next<?=$vars['page'] >= $vars['pages'] ? ' disabled' : ''; ?>" href="<?=$h($base . '&p=' . min($vars['pages'], $vars['page'] + 1)); ?>"></a>
        </div>
    <?php endif; ?>
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
    <?php if (!empty($bonusAfterDetail)): ?>
            <div class="buttonWrapper">
                <?=LegendsUi::button($h(T('Tasks', 'backToOverview', 'Back to overview')), ['class' => 'backToOverview', 'onclick' => "window.location.href='" . $base . "'"]); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if ($v2): ?><script type="text/javascript" src="js/Game/Legends/tasks.js?v=<?=ov_asset_version(PUBLIC_PATH . 'js/Game/Legends/tasks.js'); ?>"></script><?php endif; ?>
