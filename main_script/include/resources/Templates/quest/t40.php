<?php
/** Content of the Travian 4.0 task dialog (Controller\Ajax\questT40, Model\QuestT40). */

use Core\Session;
use Model\QuestT40;

/** @var QuestT40 $quest */
$quest = $vars['quest'];
$task = (int)$vars['task'];
$text = static fn(string $key, ...$args) => $args ? vsprintf(T('QuestT40', $key), $args) : T('QuestT40', $key);
$link = static fn(string $label, string $action, array $data = []) => '<a href="#" class="arrow" data-t40-action="' . $action . '"'
    . implode('', array_map(static fn($k, $v) => ' data-' . $k . '="' . (int)$v . '"', array_keys($data), $data)) . '>' . $label . '</a>';
$race = Session::getInstance()->getRace();
$unitName = T('Troops', nrToUnitId(1, $race) . '.title');

$reward = static function (int $task) use ($quest, $text) {
    $items = [];
    foreach ($quest->rewardOf($task) as $type => $amount) {
        switch ($type) {
            case 'res':
                foreach ($amount as $i => $value) {
                    $items[] = '<span class="resources"><i class="r' . ($i + 1) . '"></i>' . number_format_x($value) . '</span>';
                }
                break;
            case 'plus':
                $items[] = $text('rewardPlus' . $amount);
                break;
            case 'gold':
                $items[] = '<span class="resources"><img src="img/x.gif" class="gold" alt="">' . $amount . '</span>';
                break;
            case 'silver':
                $items[] = '<span class="resources"><img src="img/x.gif" class="silver" alt="">' . $amount . '</span>';
                break;
            case 'finish':
                $items[] = $text($amount === 1 ? 'rewardFinishWoodcutter' : 'rewardFinishBuildings');
                break;
        }
    }
    return $items ? '<div class="rew"><p class="ta_aw">' . $text('reward') . '</p>' . implode(' ', $items) . '</div>' : '';
};

// %s values of the task texts
$args = [];
if ($task === 10) {
    $neighbour = $quest->neighbour();
    $args = ['<b>' . $neighbour['name'] . '</b>'];
} elseif ($task === 18) {
    $args = [QuestT40::barracksLumber()];
} elseif ($task === 26) {
    $args = [$unitName];
} elseif ($task === 28) {
    $args = [QuestT40::inviteGold()];
}
?>
<div class="questT40" data-task="<?=$task; ?>">
<?php if ($task === QuestT40::WELCOME): ?>
    <h4><?=$text('0.title'); ?></h4>
    <div class="spoken">&bdquo;<?=$text('0.desc'); ?>&ldquo;</div>
    <div class="links">
        <?=$link($text('0.start'), 'start', ['tasks' => 1]); ?>
        <?=$link($text('0.look'), 'close'); ?>
    </div>
    <div class="links"><?=$link($text('0.none'), 'start', ['tasks' => 0]); ?></div>
    <div id="qstbg" class="intro<?=[1 => 'Romans', 2 => 'Teutons', 3 => 'Gauls'][$race] ?? ''; ?>"></div>
<?php elseif ($task === QuestT40::OPTIONAL): ?>
    <h4><?=$text('29.title'); ?></h4>
    <ul class="optional">
        <?php for ($i = 30; $i <= 40; ++$i): ?>
            <li><?php if ($quest->isOptionalCollected($i)): ?>
                    <span class="done"><?=$text($i . '.title'); ?></span>
                <?php else: ?>
                    <?=$link($text($i . '.title'), 'view', ['task' => $i]); ?>
                    <?php if ($quest->isComplete($i)): ?><span class="ready"><?=$text('ready'); ?></span><?php endif; ?>
                <?php endif; ?></li>
        <?php endfor; ?>
    </ul>
<?php elseif ($task >= 30): ?>
    <?php $complete = $quest->isComplete($task) && !$quest->isOptionalCollected($task); ?>
    <h4><?=$text($task . '.title'); ?></h4>
    <div class="spoken">&bdquo;<?=$text($task . ($complete ? '.done' : '.desc')); ?>&ldquo;</div>
    <?php if (!$complete): ?>
        <div class="rew"><p class="ta_aw"><?=$text('order'); ?></p><?=$text($task . '.order'); ?><?php
            if ($task === 33): ?> <?=$text('33.count', $quest->adventuresDone()); ?><?php endif; ?></div>
    <?php endif; ?>
    <?=$reward($task); ?>
    <div class="links">
        <?=$link($text('back'), 'view', ['task' => 0]); ?>
        <?php if ($complete): ?><?=$link($text('complete'), 'collect', ['task' => $task]); ?><?php endif; ?>
    </div>
    <div id="qstbg" class="<?=QuestT40::image($task); ?>"></div>
<?php elseif ($task === 21): ?>
    <h4><?=$text('21.title'); ?></h4>
    <div class="spoken">&bdquo;<?=$text('21.desc'); ?>&ldquo;</div>
    <div class="choice">
        <button type="button" class="green" data-t40-action="choose" data-economy="1"><?=$text('economy'); ?></button>
        <button type="button" class="green" data-t40-action="choose" data-economy="0"><?=$text('military'); ?></button>
    </div>
    <div id="qstbg" class="<?=QuestT40::image(21); ?>"></div>
<?php else: ?>
    <?php $complete = $quest->isComplete($task); ?>
    <h4><?=$text($task . '.title'); ?></h4>
    <?php if ($complete): ?>
        <div class="spoken">&bdquo;<?=$task === 11 ? $text('11.done', $text('11.res' . $quest->heroProductionType())) : $text($task . '.done', ...$args); ?>&ldquo;</div>
        <?=$reward($task); ?>
        <div class="links"><?=$link($text('continue'), 'collect'); ?></div>
    <?php else: ?>
        <div class="spoken">&bdquo;<?=$text($task . '.desc', ...$args); ?>&ldquo;</div>
        <div class="rew"><p class="ta_aw"><?=$text('order'); ?></p><?=$text($task . '.order', ...$args); ?></div>
        <?php if ($task === 10): ?>
            <div class="answer coordinates">
                <label>X: <input class="text" name="x" maxlength="4" autocomplete="off"></label>
                <label>Y: <input class="text" name="y" maxlength="4" autocomplete="off"></label>
                <button type="button" class="green" data-t40-action="answer"><?=$text('complete'); ?></button>
            </div>
        <?php elseif (in_array($task, [6, 18, 20, 28], true)): ?>
            <div class="answer">
                <input class="text" name="value" maxlength="8" autocomplete="off">
                <button type="button" class="green" data-t40-action="answer"><?=$text('complete'); ?></button>
            </div>
        <?php endif; ?>
        <?php if ($vars['wrong']): ?><p class="wrong"><?=$text('wrong'); ?></p><?php endif; ?>
    <?php endif; ?>
    <div id="qstbg" class="<?=QuestT40::image($task); ?>"></div>
<?php endif; ?>
</div>
