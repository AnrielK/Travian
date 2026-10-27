<?php
/*
 * Right sidebar of the Travian Legends look (included by rightsideinfor.php when Skin::isLegends()):
 * active village (four building shortcuts + "more"), village list with expansion slots and village groups,
 * admin box and the task overview with the tribe's mentor – markup of Travian Legends 655, styled by legends_ltr.css.
 */
$e = [LegendsUi::class, 'e'];
$t = [LegendsUi::class, 't'];
$uid = (int)$session->uid;

// building shortcuts of the active village
$slotOf = [];
for ($i = 19; $i <= 40; $i++) {
    $gid = (int)($village->resarray['f' . $i . 't'] ?? 0);
    if ($gid > 0 && !isset($slotOf[$gid])) $slotOf[$gid] = $i;
}
$shortcut = function (string $name, int $gid, string $svg) use ($slotOf, $session, $lang, $e): string {
    $has = isset($slotOf[$gid]);
    $href = $has && $session->plus ? 'build.php?id=' . $slotOf[$gid] : '#';
    $onclick = !$has ? 'return false;' : ($session->plus ? '' : "LegendsPlus.open('directLinks');return false;");
    $title = (string)($lang['buildings'][$gid] ?? $name);
    return '<a class="layoutButton buttonFramed withIcon round ' . $name . ' gold' . ($has ? '' : ' disabled') . '" href="' . $e($href) . '" title="' . $e($title) . '"'
        . ($onclick !== '' ? ' onclick="' . $onclick . '"' : '') . '>'
        . LegendsUi::svg($svg) . '</a>';
};
$moreBuildings = [16, 15, 22, 12, 24, 25, 26, 27, 28, 29, 30, 37, 44];

// expansion slots (same formula as the original box)
$vcount = safe_count($session->vvillages);
$mode = CP;
$slots = 1;
while (isset(${'cp' . $mode}[$slots]) && ${'cp' . $mode}[$slots] < $session->cp) $slots++;
$slots--;
$nextCp = ${'cp' . $mode}[$slots + 1] ?? 0;
$cpPercent = max(0, min(100, safe_div($session->cp, $nextCp) * 100));

// incoming attacks (Plus) and the village link that keeps the current page
$villmasR = safe_implode(',', $session->villages);
$attacks = [];
if ($session->plus) {
    foreach ((array)$database->getMovement(88, $villmasR, 1) as $mv) {
        $attacks[$mv['to']] = ($attacks[$mv['to']] ?? 0) + 1;
    }
}
$query = explode('&', (string)($_SERVER['QUERY_STRING'] ?? ''));
if (isset($_GET['newdid']) || isset($_GET['id'])) unset($query[0]);
if (isset($_GET['newdid']) && isset($_GET['id'])) unset($query[1]);
$query = safe_implode('&', $query);
$isBuild = ($_SERVER['PHP_SELF'] ?? '') === '/build.php';
$villageLink = function (array $vil) use ($query, $isBuild, $village) {
    global $fff;
    $id = $_GET['id'] ?? null;
    $link = '?newdid=' . $vil['wref'] . ($id !== null ? '&id=' . $id : '') . ($query ? '&' . $query : '');
    if ($isBuild && (int)$id > 18) {
        $buildvil = $fff[$vil['wref']] ?? [];
        $newId = $id;
        $exist = 0;
        for ($b = 19; $b <= 40; $b++) {
            if (($buildvil['f' . $b . 't'] ?? null) == $village->resarray['f' . $id . 't'] && ($buildvil['f' . $b] ?? 0) > 0) {
                $newId = $b;
                $exist = 1;
            }
        }
        if ($newId != $id) $link = '?newdid=' . $vil['wref'] . '&id=' . $newId . ($query ? '&' . $query : '');
        elseif (!$exist) $link = '?newdid=' . $vil['wref'] . '&id=' . $newId;
    }
    return ($_SERVER['PHP_SELF'] ?? '') . $link;
};
$coord = function ($n): string {
    $n = (int)$n;
    return $n < 0 ? '&#x2212;' . abs($n) : (string)$n;
};
$handle = '<svg viewBox="0 0 6 16" class="handle"><circle cx="1" cy="2" r="1"/><circle cx="5" cy="2" r="1"/><circle cx="1" cy="6" r="1"/><circle cx="5" cy="6" r="1"/><circle cx="1" cy="10" r="1"/><circle cx="5" cy="10" r="1"/><circle cx="1" cy="14" r="1"/><circle cx="5" cy="14" r="1"/></svg>';
$attackSvg = '<svg viewBox="0 0 20 19.06" class="attack"><path d="M16.22 13.08l2.14-2.14-1.37-1.36-1.63 1.63-2.15-2.15 4.74-4.73L19.06 0l-4.32 1.11L10 5.85 5.26 1.11.94 0l1.11 4.33 4.74 4.73-2.15 2.15-1.63-1.63-1.37 1.36 2.14 2.14L0 16.86l2.21 2.2 3.78-3.77 2.13 2.13 1.37-1.37-1.63-1.63L10 12.28l2.14 2.14-1.63 1.63 1.37 1.37 2.14-2.13 3.77 3.77 2.21-2.2-3.78-3.78z"></path></svg>';
$villageEntry = function (array $vil) use ($village, $attacks, $villageLink, $coord, $handle, $attackSvg, $e, $t): string {
    $attack = !empty($attacks[$vil['wref']]);
    $title = $vil['name'] . ($attack ? ' – ' . $t('VG_ATTACKS', 'Incoming attacks') . ': ' . $attacks[$vil['wref']] : '');
    return '<div class="listEntry village' . ($vil['wref'] == $village->wid ? ' active' : '') . ($attack ? ' attack' : '') . '" data-wref="' . (int)$vil['wref'] . '" title="' . $e($title) . '">'
        . '<span class="dragAndDrop" title="' . $e($t('VG_DRAG', 'Drag the village into a group')) . '">' . $handle . '</span>'
        . '<a class="iconAndNameWrapper" href="' . $e($villageLink($vil)) . '">' . $attackSvg . '<span class="name">' . $e($vil['name']) . '</span></a>'
        . '<span class="coordinatesGrid"><span class="coordinates coordinatesWrapper coordinatesAligned coordinatesltr">'
        . '<span class="coordinateX">(' . $coord($vil['vx']) . '</span><span class="coordinatePipe">|</span><span class="coordinateY">' . $coord($vil['vy']) . ')</span>'
        . '</span></span></div>';
};
$villagesByWref = [];
foreach ($session->vvillages as $vil) $villagesByWref[(int)$vil['wref']] = $vil;
$listStructure = VillageGroups::structure($uid, array_keys($villagesByWref));
$groupCount = count(VillageGroups::groups($uid));
$groupHandle = str_replace('class="handle"', 'class="handle"', $handle);
$quickLinks = LegendsQuickLinks::all($village->resarray);
$quickSlots = VillageGroups::quickLinks($uid);
$slotIndicator = '<svg class="editSlotIndicator" viewBox="0 0 44 44"><circle cx="22" cy="22" r="21" fill="none" stroke-width="1.5" stroke-dasharray="4 3"/></svg>';
$plusSmall = '<svg viewBox="0 0 18 18"><path d="M16 10h-6v6H8v-6H2V8h6V2h2v6h6z"/></svg>';
$taskModel = new LegendsTasks();
$claimable = $taskModel->hasCollectable();
?>
<div id="sidebarAfterContent" class="sidebar afterContent">
    <div class="sidebarBoxWrapper">
        <div id="sidebarBoxActiveVillage" class="sidebarBox">
            <div class="header">
                <div class="buttonsWrapper">
                    <?php echo $shortcut('market', 17, 'sbMarket') . $shortcut('barracks', 19, 'sbBarracks') . $shortcut('stable', 20, 'sbStable') . $shortcut('workshop', 21, 'sbWorkshop'); ?>
                    <button type="button" class="layoutButton buttonFramed withIcon round more gold small" title="<?php echo $e($t('SB_MORE_BUILDINGS', 'More buildings')); ?>"
                            onclick="var m=document.getElementById('activeVillageMore');m.hidden=!m.hidden;"><?php echo LegendsUi::svg('sbMore'); ?></button>
                </div>
                <div id="activeVillageMore" class="sidebarPopup" hidden>
                    <?php
                    $any = false;
                    foreach ($moreBuildings as $gid) {
                        if (!isset($slotOf[$gid])) continue;
                        $any = true;
                        echo '<a href="build.php?id=' . $slotOf[$gid] . '">' . $e($lang['buildings'][$gid] ?? $gid) . '</a>';
                    }
                    if (!$any) echo '<span>' . $e($t('SB_NO_BUILDINGS', 'No further buildings yet.')) . '</span>';
                    ?>
                </div>
            </div>
            <div class="content">
                <div class="playerName"><?php echo $e($session->username); ?></div>
                <div id="villageName" class="editable">
                    <input type="text" class="villageInput" maxlength="30" value="<?php echo $e($village->vname); ?>" data-did="<?php echo (int)$village->wid; ?>"
                           title="<?php echo $e(defined('dorf1_villageNameBox_16') ? dorf1_villageNameBox_16 : 'Rename village'); ?>">
                    <svg viewBox="0 0 15.59 20" class="rename"><path d="M15.59 18.55V20H0v-1.45zM11.52 0L1.05 13.47.92 17.4l3.65-1.21L15 2.73zm.25 7L8.16 4.33 9 3.2l3.6 2.62z"></path></svg>
                </div>
                <div class="population"><?php echo POPULATION; ?>: <span><?php echo (int)$village->pop; ?></span></div>
                <div class="loyalty <?php echo $village->loyalty < 30 ? 'low' : ($village->loyalty < 90 ? 'medium' : 'high'); ?>"><?php echo LOYALTY; ?>: <span><?php echo (int)$village->loyalty; ?>%</span></div>
            </div>
        </div>

        <div id="sidebarBoxVillageList" class="sidebarBox">
            <div class="header">
                <div class="buttonsWrapper quickLinks">
                    <?php foreach ($quickSlots as $i => $key) { ?>
                        <div class="quickLinkSlot" data-slot="<?php echo $i; ?>"><?php echo $slotIndicator; ?><div class="slot"><?php echo $plusSmall; ?></div><?php
                            if ($key !== '') echo LegendsQuickLinks::button($key, $quickLinks[$key], (bool)$session->plus);
                        ?></div>
                    <?php } ?>
                    <span class="spacer"></span>
                    <a class="layoutButton buttonFramed withIcon round villageStatistics green" href="dorf3.php" title="<?php echo $e($t('SB_OVERVIEW', 'Village overview')); ?>"><?php echo LegendsUi::svg('sbOverview'); ?></a>
                    <button type="button" class="layoutButton buttonFramed withIcon round settings gold" title="<?php echo $e($t('QL_EDIT', 'Edit quick links')); ?>"
                            onclick="VillageListUi.toggleQuickLinks();"><?php echo LegendsUi::svg('sbMore'); ?><svg class="closeIcon" viewBox="0 0 20 20"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"/></svg></button>
                </div>
            </div>
            <div class="content">
                <div class="expansionSlotInfo" title="<?php echo $e(dorf1_villageNameBox2_3 . ': ' . $vcount . '/' . $slots . ' – ' . dorf1_villageNameBox2_5 . ' ' . (int)$session->cp . '/' . $nextCp); ?>">
                    <div class="boxTitle"><?php echo dorf1_villageNameBox2_3; ?> <span class="slots"><?php echo $vcount . '/' . $slots; ?></span></div>
                    <div class="barWrapper"><div class="bar" style="width:<?php echo round($cpPercent, 1); ?>%"></div></div>
                </div>
                <div class="villageList" data-max="<?php echo VillageGroups::MAX; ?>">
                    <div class="groupsHeader">
                        <span><?php echo $e($t('VG_GROUPS', 'Village groups')); ?> (<span class="groupCount"><?php echo $groupCount; ?></span>/<?php echo VillageGroups::MAX; ?>)</span>
                        <span></span>
                        <button type="button" class="secondaryIconButton addGroup" title="<?php echo $e($t('VG_ADD', 'New village group')); ?>"
                                onclick="VillageListUi.addGroup();"<?php echo $groupCount >= VillageGroups::MAX ? ' disabled' : ''; ?>>
                            <svg viewBox="0 0 20 16"><path d="M1 2.2C1 1.5 1.5 1 2.2 1h5l2 2.2h8.6c.7 0 1.2.5 1.2 1.2v9.4c0 .7-.5 1.2-1.2 1.2H2.2C1.5 15 1 14.5 1 13.8zM10 6.3v5.4M7.3 9h5.4"/></svg>
                        </button>
                    </div>
                    <?php foreach ($listStructure as $entry) {
                        if ($entry['type'] === 'group') {
                            $g = $entry['group']; ?>
                        <div class="groupWrapper<?php echo $g['collapsed'] ? ' collapsed' : ''; ?>" data-gid="<?php echo $g['gid']; ?>">
                            <div class="listEntry group <?php echo $g['color']; ?>" data-gid="<?php echo $g['gid']; ?>" data-color="<?php echo $g['color']; ?>">
                                <span class="dragAndDrop" title="<?php echo $e($t('VG_DRAG_GROUP', 'Drag to move the group')); ?>"><?php echo $handle; ?></span>
                                <span class="name"><?php echo $e($g['name']); ?></span>
                                <div class="actions">
                                    <button type="button" class="secondaryIconButton edit" title="<?php echo $e($t('VG_EDIT', 'Edit group')); ?>" onclick="VillageListUi.editGroup(this);">
                                        <svg viewBox="0 0 15.59 20"><path d="M15.59 18.55V20H0v-1.45zM11.52 0L1.05 13.47.92 17.4l3.65-1.21L15 2.73zm.25 7L8.16 4.33 9 3.2l3.6 2.62z"></path></svg>
                                    </button>
                                    <button type="button" class="secondaryIconButton expand" title="<?php echo $e($t('VG_EXPAND', 'Show / hide villages')); ?>" onclick="VillageListUi.toggleGroup(this);">
                                        <svg viewBox="0 0 12 7" class="arrowExpand<?php echo $g['collapsed'] ? '' : ' expanded'; ?>"><path d="M1 6l5-5 5 5" fill="none"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="groupContainer"><div class="dropContainer" data-gid="<?php echo $g['gid']; ?>"><?php
                                foreach ($entry['villages'] as $w) if (isset($villagesByWref[$w])) echo $villageEntry($villagesByWref[$w]);
                            ?></div></div>
                        </div>
                    <?php } elseif (isset($villagesByWref[$entry['wref']])) { ?>
                        <div class="dropContainer topLevel" data-gid="0"><?php echo $villageEntry($villagesByWref[$entry['wref']]); ?></div>
                    <?php }
                    } ?>
                </div>
            </div>
        </div>

        <?php if ($session->access == 9) { ?>
        <div id="sidebarBoxAdmin" class="sidebarBox">
            <div class="header"></div>
            <div class="content">
                <div class="boxTitle"><?php echo $e(defined('SB_ADMIN') ? SB_ADMIN : 'Admin'); ?></div>
                <p><?php echo $lang['Admin']['Panel01']; ?></p>
                <?php echo LegendsUi::button($e(strip_tags($lang['Admin']['Panel02'])), ['onclick' => "window.location.href='panel';"]); ?>
            </div>
        </div>
        <?php } ?>

        <div id="sidebarBoxQuestmaster" class="sidebarBox">
            <div class="header">
                <a id="questmasterButton" class="vid_<?php echo (int)$session->tribe; ?><?php echo $claimable ? ' claimable' : ''; ?>" href="tasks.php" title="<?php echo $e($t('TASKS_OVERVIEW', 'Task overview')); ?>">
                    <div class="mentor"></div>
                    <?php if ($claimable) { ?><div class="bigSpeechBubble newQuestSpeechBubble"><img src="img/x.gif" alt=""></div><?php } ?>
                </a>
            </div>
            <div class="content">
                <a class="progressiveTasksTitle" href="tasks.php"><div class="boxTitle"><?php echo $e($t('TASKS_OVERVIEW', 'Task overview')); ?></div></a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var VillageListConfig = <?php echo json_encode([
    'T' => [
        'name' => $t('VG_NAME_PROMPT', 'Name of the group:'),
        'newGroup' => $t('VG_NEW_GROUP', 'New group'),
        'del' => $t('VG_DELETE', 'Delete group'),
        'delConfirm' => $t('VG_DELETE_CONFIRM', 'Delete this group? Your villages will not be affected.'),
        'cancel' => $t('VG_CANCEL', 'Cancel'),
        'delete' => $t('VG_DELETE_BUTTON', 'Delete'),
        'color' => $t('VG_COLOR', 'Colour'),
        'max' => $t('VG_MAX', 'You can have at most 20 village groups.'),
        'qlTitle' => $t('QL_TITLE', 'Available quick links for the village list'),
        'qlText' => $t('QL_TEXT', 'Drag up to 4 buttons into the slots of the village list to customise your quick links. The quick links are the same for all villages, but state and tooltip follow the active village.'),
        'qlRemove' => $t('QL_REMOVE', 'Tap a slot to empty it.'),
    ],
    'colors' => VillageGroups::COLORS,
    'links' => array_map(function ($k) use ($quickLinks, $session) {
        return ['key' => $k, 'title' => $quickLinks[$k]['title'], 'sub' => $quickLinks[$k]['sub'],
            'html' => LegendsQuickLinks::button($k, $quickLinks[$k], (bool)$session->plus)];
    }, array_keys($quickLinks)),
], JSON_UNESCAPED_UNICODE); ?>;
</script>
<script type="text/javascript" src="application/legends/villageList.js?v=<?php echo Skin::VERSION; ?>"></script>
