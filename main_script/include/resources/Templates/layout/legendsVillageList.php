<?php
/*
 * Travian Legends 655 (is_hero_v2()): village list with village groups ("Skupiny vesnic (1/20)") in the box of the
 * village list (layout/sidebarBoxVillageList.php). Groups: resources/View/LegendsVillageGroups.php,
 * editor: js/Game/Legends/villageGroups.js (ajax.php?cmd=villageGroups). Port of the travian-T4.6 branch.
 */
use resources\View\LegendsUi;
use resources\View\LegendsVillageGroups;

$session = \Core\Session::getInstance();
$uid = (int)$session->getPlayerId();
$rows = $vars['villageRows'] ?? [];
$structure = LegendsVillageGroups::structure($uid, array_keys($rows));
$groupCount = count(LegendsVillageGroups::groups($uid));
$canEdit = !$session->isSitter();
$e = static fn($s) => LegendsUi::e($s);
$t = static fn($k, $d) => T('VillageGroups', $k, $d);
$handle = LegendsVillageGroups::handle();
$attackSvg = '<svg viewBox="0 0 20 19.06" class="attack"><path d="M16.22 13.08l2.14-2.14-1.37-1.36-1.63 1.63-2.15-2.15 4.74-4.73L19.06 0l-4.32 1.11L10 5.85 5.26 1.11.94 0l1.11 4.33 4.74 4.73-2.15 2.15-1.63-1.63-1.37 1.36 2.14 2.14L0 16.86l2.21 2.2 3.78-3.77 2.13 2.13 1.37-1.37-1.63-1.63L10 12.28l2.14 2.14-1.63 1.63 1.37 1.37 2.14-2.13 3.77 3.77 2.21-2.2-3.78-3.78z"/></svg>';
$villageEntry = static function (array $v) use ($e, $t, $handle, $attackSvg, $canEdit): string {
    $title = $v['name'] . ($v['attacks'] ? ' – ' . $t('attacks', 'Incoming attacks') . ': ' . $v['attacks'] : '');
    return '<div class="listEntry village' . ($v['active'] ? ' active' : '') . ($v['attacks'] ? ' attack' : '') . '" data-kid="' . (int)$v['kid'] . '" title="' . $e($title) . '">'
        . ($canEdit ? '<span class="dragAndDrop" title="' . $e($t('dragVillage', 'Drag the village into a group')) . '">' . $handle . '</span>' : '')
        . '<a class="iconAndNameWrapper' . ($v['active'] ? ' active' : '') . '" href="' . $e($v['href']) . '">' . $attackSvg . '<span class="name">' . $e($v['name']) . '</span></a>'
        . '<span class="coordinatesGrid"><span class="coordinates coordinatesWrapper coordinatesAligned coordinates' . strtolower(getDirection()) . '">'
        . '<span class="coordinateX">(' . (int)$v['x'] . '</span><span class="coordinatePipe">|</span><span class="coordinateY">' . (int)$v['y'] . ')</span>'
        . '</span></span></div>';
};
?>
<div class="villageList" data-max="<?=LegendsVillageGroups::MAX; ?>">
    <div class="groupsHeader">
        <span><?=$e($t('groups', 'Village groups')); ?> (<span class="groupCount"><?=$groupCount; ?></span>/<?=LegendsVillageGroups::MAX; ?>)</span>
        <span></span>
        <?php if ($canEdit): ?>
        <button type="button" class="secondaryIconButton addGroup" title="<?=$e($t('add', 'New village group')); ?>"
                onclick="LegendsVillageGroups.addGroup();"<?=$groupCount >= LegendsVillageGroups::MAX ? ' disabled' : ''; ?>>
            <svg viewBox="0 0 20 16"><path d="M1 2.2C1 1.5 1.5 1 2.2 1h5l2 2.2h8.6c.7 0 1.2.5 1.2 1.2v9.4c0 .7-.5 1.2-1.2 1.2H2.2C1.5 15 1 14.5 1 13.8zM10 6.3v5.4M7.3 9h5.4"/></svg>
        </button>
        <?php endif; ?>
    </div>
    <?php foreach ($structure as $entry):
        if ($entry['type'] === 'group'):
            $g = $entry['group']; ?>
    <div class="groupWrapper<?=$g['collapsed'] ? ' collapsed' : ''; ?>" data-gid="<?=$g['gid']; ?>">
        <div class="listEntry group <?=$g['color']; ?>" data-gid="<?=$g['gid']; ?>" data-color="<?=$g['color']; ?>">
            <?php if ($canEdit): ?><span class="dragAndDrop" title="<?=$e($t('dragGroup', 'Drag to move the group')); ?>"><?=$handle; ?></span><?php endif; ?>
            <span class="name"><?=$e($g['name']); ?></span>
            <div class="actions">
                <?php if ($canEdit): ?>
                <button type="button" class="secondaryIconButton edit" title="<?=$e($t('edit', 'Edit group')); ?>" onclick="LegendsVillageGroups.editGroup(this);">
                    <svg viewBox="0 0 15.59 20"><path d="M15.59 18.55V20H0v-1.45zM11.52 0L1.05 13.47.92 17.4l3.65-1.21L15 2.73zm.25 7L8.16 4.33 9 3.2l3.6 2.62z"></path></svg>
                </button>
                <?php endif; ?>
                <button type="button" class="secondaryIconButton expand" title="<?=$e($t('expand', 'Show / hide villages')); ?>" onclick="LegendsVillageGroups.toggleGroup(this);">
                    <svg viewBox="0 0 12 7" class="arrowExpand<?=$g['collapsed'] ? '' : ' expanded'; ?>"><path d="M1 6l5-5 5 5" fill="none"></path></svg>
                </button>
            </div>
        </div>
        <div class="groupContainer"><div class="dropContainer" data-gid="<?=$g['gid']; ?>"><?php
            foreach ($entry['villages'] as $k) {
                if (isset($rows[$k])) {
                    echo $villageEntry($rows[$k]);
                }
            } ?></div></div>
    </div>
        <?php elseif (isset($rows[$entry['kid']])): ?>
    <div class="dropContainer topLevel" data-gid="0"><?=$villageEntry($rows[$entry['kid']]); ?></div>
        <?php endif;
    endforeach; ?>
</div>
<?php if ($canEdit): ?>
<script type="text/javascript">window.LegendsVillageGroupsConfig = <?=json_encode([
    'T' => [
        'newGroup' => $t('newGroup', 'New group'),
        'del' => $t('delete', 'Delete group'),
        'delConfirm' => $t('deleteConfirm', 'Delete this group? Your villages will not be affected.'),
        'cancel' => $t('cancel', 'Cancel'),
        'delete' => $t('deleteButton', 'Delete'),
        'color' => $t('color', 'Colour'),
        'max' => $t('max', 'You can have at most 20 village groups.'),
    ],
    'colors' => LegendsVillageGroups::COLORS,
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS); ?>;</script>
<script type="text/javascript" src="js/Game/Legends/dialog.js?v=1"></script>
<script type="text/javascript" src="js/Game/Legends/villageGroups.js?v=1"></script>
<?php endif; ?>
