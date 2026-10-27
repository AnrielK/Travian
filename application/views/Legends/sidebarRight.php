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
    $href = !$has ? '#' : ($session->plus ? 'build.php?id=' . $slotOf[$gid] : 'plus.php?id=3');
    $title = (string)($lang['buildings'][$gid] ?? $name);
    return '<a class="layoutButton buttonFramed withIcon round ' . $name . ' gold' . ($has ? '' : ' disabled') . '" href="' . $e($href) . '" title="' . $e($title) . '">'
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
$groups = VillageGroups::groups($uid);
$members = VillageGroups::members($uid);
$byGroup = [];
$ungrouped = [];
foreach ($session->vvillages as $vil) {
    $g = $members[(int)$vil['wref']] ?? 0;
    if ($g && isset($groups[$g])) $byGroup[$g][] = $vil; else $ungrouped[] = $vil;
}
$icon = function (string $viewBox, string $path, string $class = ''): string {
    return '<svg viewBox="' . $viewBox . '"' . ($class ? ' class="' . $class . '"' : '') . '><g class="outline"><path d="' . $path . '"></path></g><g class="icon"><path d="' . $path . '"></path></g></svg>';
};
$cultureIcon = $icon('0 0 20 20', 'M10 3.2a2.1 2.1 0 1 1 0 4.2 2.1 2.1 0 0 1 0-4.2zM4.4 3.6C2.3 5.4 1.3 8.2 2 11c.7 2.8 2.9 4.9 5.6 5.6l.4-1.5C5.8 14.6 4.1 13 3.5 10.7 3 8.4 3.8 6.1 5.5 4.7zm11.2 0-1.1 1.1c1.7 1.4 2.5 3.7 2 6-.6 2.3-2.3 3.9-4.5 4.4l.4 1.5c2.7-.7 4.9-2.8 5.6-5.6.7-2.8-.3-5.6-2.4-7.4zM6.9 8.5 9 10.6V18h2v-7.4l2.1-2.1-1.4-1.4L10 8.8 8.3 7.1z');
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
                <div class="buttonsWrapper villageListButtons">
                    <a class="layoutButton buttonFramed withIcon round troops green" href="dorf3.php?s=5" title="<?php echo $e($t('SB_TROOPS', 'Troops in your villages')); ?>"><?php echo str_replace('class="attack"', 'class="troops"', $icon('0 0 20 19.06', 'M16.22 13.08l2.14-2.14-1.37-1.36-1.63 1.63-2.15-2.15 4.74-4.73L19.06 0l-4.32 1.11L10 5.85 5.26 1.11.94 0l1.11 4.33 4.74 4.73-2.15 2.15-1.63-1.63-1.37 1.36 2.14 2.14L0 16.86l2.21 2.2 3.78-3.77 2.13 2.13 1.37-1.37-1.63-1.63L10 12.28l2.14 2.14-1.63 1.63 1.37 1.37 2.14-2.13 3.77 3.77 2.21-2.2-3.78-3.78z')); ?></a>
                    <a class="layoutButton buttonFramed withIcon round culture green" href="dorf3.php?s=4" title="<?php echo $e($t('SB_CULTURE', 'Culture points')); ?>"><?php echo $cultureIcon; ?></a>
                    <span class="spacer"></span>
                    <a class="layoutButton buttonFramed withIcon round overview green" href="dorf3.php" title="<?php echo $e($t('SB_OVERVIEW', 'Village overview')); ?>"><?php echo LegendsUi::svg('sbOverview'); ?></a>
                    <button type="button" class="layoutButton buttonFramed withIcon round more gold small" title="<?php echo $e($t('SB_MORE', 'More')); ?>"
                            onclick="var m=document.getElementById('villageListMore');m.hidden=!m.hidden;"><?php echo LegendsUi::svg('sbMore'); ?></button>
                </div>
                <div id="villageListMore" class="sidebarPopup" hidden>
                    <a href="dorf3.php?s=2"><?php echo $e($t('SB_RESOURCES', 'Resources')); ?></a>
                    <a href="dorf3.php?s=3"><?php echo $e($t('SB_WAREHOUSE', 'Warehouse')); ?></a>
                    <a href="#" onclick="VillageGroupsUi.add();return false;"><?php echo $e($t('VG_ADD', 'New village group')); ?></a>
                </div>
            </div>
            <div class="content">
                <div class="expansionSlotInfo" title="<?php echo $e(dorf1_villageNameBox2_3 . ': ' . $vcount . '/' . $slots . ' – ' . dorf1_villageNameBox2_5 . ' ' . (int)$session->cp . '/' . $nextCp); ?>">
                    <div class="boxTitle"><?php echo dorf1_villageNameBox2_3; ?> <span class="slots"><?php echo $vcount . '/' . $slots; ?></span></div>
                    <div class="barWrapper"><div class="bar" style="width:<?php echo round($cpPercent, 1); ?>%"></div></div>
                </div>
                <div class="villageList" data-max="<?php echo VillageGroups::MAX; ?>">
                    <div class="groupsHeader">
                        <span><?php echo $e($t('VG_GROUPS', 'Village groups')); ?> (<?php echo count($groups); ?>/<?php echo VillageGroups::MAX; ?>)</span>
                        <span></span>
                        <button type="button" class="secondaryIconButton addGroup" title="<?php echo $e($t('VG_ADD', 'New village group')); ?>"
                                onclick="VillageGroupsUi.add();"<?php echo count($groups) >= VillageGroups::MAX ? ' disabled' : ''; ?>>
                            <svg viewBox="0 0 20 16"><path d="M0 2.2C0 1 1 0 2.2 0h5.3l2 2.2h8.3C19 2.2 20 3.2 20 4.4v9.4c0 1.2-1 2.2-2.2 2.2H2.2C1 16 0 15 0 13.8zm9.1 4.4v2.3H6.8v1.8h2.3V13h1.8v-2.3h2.3V8.9h-2.3V6.6z"/></svg>
                        </button>
                    </div>
                    <?php foreach ($groups as $g) { ?>
                        <div class="groupWrapper<?php echo $g['collapsed'] ? ' collapsed' : ''; ?>" data-gid="<?php echo $g['gid']; ?>">
                            <div class="listEntry group <?php echo $g['color']; ?>" data-gid="<?php echo $g['gid']; ?>" data-color="<?php echo $g['color']; ?>">
                                <span class="name"><?php echo $e($g['name']); ?></span>
                                <div class="actions">
                                    <button type="button" class="secondaryIconButton edit" title="<?php echo $e($t('VG_EDIT', 'Edit group')); ?>" onclick="VillageGroupsUi.edit(this);">
                                        <svg viewBox="0 0 15.59 20"><path d="M15.59 18.55V20H0v-1.45zM11.52 0L1.05 13.47.92 17.4l3.65-1.21L15 2.73zm.25 7L8.16 4.33 9 3.2l3.6 2.62z"></path></svg>
                                    </button>
                                    <button type="button" class="secondaryIconButton expand" title="<?php echo $e($t('VG_EXPAND', 'Show / hide villages')); ?>" onclick="VillageGroupsUi.toggle(this);">
                                        <svg viewBox="0 0 12 7" class="arrowExpand<?php echo $g['collapsed'] ? '' : ' expanded'; ?>"><path d="M1 1l5 5 5-5" fill="none"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="groupContainer"><div class="dropContainer" data-gid="<?php echo $g['gid']; ?>"><?php
                                foreach ($byGroup[$g['gid']] ?? [] as $vil) echo $villageEntry($vil);
                            ?></div></div>
                        </div>
                    <?php } ?>
                    <div class="dropContainer ungrouped" data-gid="0"><?php
                        foreach ($ungrouped as $vil) echo $villageEntry($vil);
                    ?></div>
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
var VillageGroupsUi = (function () {
    var T = <?php echo json_encode([
        'name' => $t('VG_NAME_PROMPT', 'Name of the group:'),
        'newGroup' => $t('VG_NEW_GROUP', 'New group'),
        'del' => $t('VG_DELETE', 'Delete group'),
        'delConfirm' => $t('VG_DELETE_CONFIRM', 'Delete this group? Its villages stay in the list.'),
        'color' => $t('VG_COLOR', 'Colour'),
        'save' => $t('VG_SAVE', 'Save'),
        'max' => $t('VG_MAX', 'You can have at most 20 village groups.'),
    ], JSON_UNESCAPED_UNICODE); ?>;
    var COLORS = <?php echo json_encode(VillageGroups::COLORS); ?>;
    function post(data, reload) {
        var body = Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&');
        return fetch('ajax.php?cmd=villageGroups', {method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: body})
            .then(function (r) { return r.json(); })
            .then(function (j) { if (reload !== false) location.reload(); return j; })
            .catch(function () { if (reload !== false) location.reload(); });
    }
    function add() {
        var list = document.querySelector('#sidebarBoxVillageList .villageList');
        if (list && list.querySelectorAll('.groupWrapper').length >= +list.getAttribute('data-max')) { alert(T.max); return; }
        var name = prompt(T.name, T.newGroup);
        if (name !== null) post({action: 'add', name: name});
    }
    function toggle(btn) {
        var w = btn.closest('.groupWrapper');
        var collapsed = w.classList.toggle('collapsed');
        btn.querySelector('svg').classList.toggle('expanded', !collapsed);
        post({action: 'collapse', gid: w.getAttribute('data-gid'), collapsed: collapsed ? 1 : 0}, false);
    }
    function edit(btn) {
        var row = btn.closest('.listEntry.group');
        if (row.classList.contains('editMode')) return;
        var gid = row.getAttribute('data-gid'), color = row.getAttribute('data-color');
        var name = row.querySelector('.name').textContent.trim();
        row.classList.add('editMode');
        row.innerHTML = '<div class="editModeOverlay"></div><input type="text" class="name" maxlength="40">'
            + '<div class="colorSelection"><div class="color ' + color + '" title="' + T.color + '"><div class="selectedIndicator"></div></div></div>'
            + '<div class="actions"><button type="button" class="secondaryIconButton delete" title="' + T.del + '">'
            + '<svg viewBox="0 0 14 16"><path d="M1 3h12v1.5H1zM5 0h4v1.5h4V3H1V1.5h4zM2.5 5h9l-.8 11H3.3zM5 7v7h1.2V7zm2.8 0v7H9V7z"/></svg></button></div>';
        var input = row.querySelector('input.name');
        input.value = name;
        input.focus();
        input.select();
        var swatch = row.querySelector('.color');
        swatch.addEventListener('click', function () {
            swatch.classList.remove(color);
            color = COLORS[(COLORS.indexOf(color) + 1) % COLORS.length];
            swatch.classList.add(color);
            row.className = row.className.replace(/\b(grey|yellow|green|turquoise|blue|violet|magenta|red|orange)\b/, color);
        });
        function save() {
            post({action: 'color', gid: gid, color: color}, false).then(function () {
                post({action: 'rename', gid: gid, name: input.value});
            });
        }
        input.addEventListener('keydown', function (ev) {
            if (ev.key === 'Enter') save();
            if (ev.key === 'Escape') location.reload();
        });
        row.querySelector('.editModeOverlay').addEventListener('click', save);
        row.querySelector('.delete').addEventListener('click', function () {
            if (confirm(T.delConfirm)) post({action: 'delete', gid: gid});
        });
    }
    // drag & drop of villages into groups (pointer events: mouse and touch)
    function initDrag() {
        var box = document.getElementById('sidebarBoxVillageList');
        if (!box) return;
        var drag = null;
        function target(x, y) {
            var el = document.elementFromPoint(x, y);
            if (!el) return null;
            var w = el.closest('#sidebarBoxVillageList .groupWrapper');
            if (w) return w;
            return el.closest('#sidebarBoxVillageList .dropContainer.ungrouped');
        }
        box.addEventListener('pointerdown', function (ev) {
            var h = ev.target.closest('.dragAndDrop');
            if (!h) return;
            var entry = h.closest('.listEntry.village');
            ev.preventDefault();
            var r = entry.getBoundingClientRect();
            var ghost = document.createElement('div');
            ghost.className = 'dragObject';
            ghost.appendChild(entry.cloneNode(true));
            ghost.style.left = r.left + 'px';
            ghost.style.top = r.top + 'px';
            ghost.style.width = r.width + 'px';
            box.querySelector('.villageList').appendChild(ghost);
            entry.classList.add('dragged');
            drag = {entry: entry, ghost: ghost, dx: ev.clientX - r.left, dy: ev.clientY - r.top, over: null};
            try { h.setPointerCapture(ev.pointerId); } catch (e) {}
        });
        box.addEventListener('pointermove', function (ev) {
            if (!drag) return;
            drag.ghost.style.left = (ev.clientX - drag.dx) + 'px';
            drag.ghost.style.top = (ev.clientY - drag.dy) + 'px';
            drag.ghost.style.display = 'none';
            var over = target(ev.clientX, ev.clientY);
            drag.ghost.style.display = '';
            if (drag.over !== over) {
                if (drag.over) drag.over.classList.remove('highlightDropSection');
                if (over) over.classList.add('highlightDropSection');
                drag.over = over;
            }
        });
        function end(ev) {
            if (!drag) return;
            var d = drag;
            drag = null;
            d.ghost.remove();
            d.entry.classList.remove('dragged');
            if (d.over) d.over.classList.remove('highlightDropSection');
            if (!d.over || ev.type === 'pointercancel') return;
            var gid = d.over.getAttribute('data-gid') || '0';
            var current = d.entry.closest('[data-gid]');
            if (current && current.getAttribute('data-gid') === gid) return;
            post({action: 'assign', gid: gid, wref: d.entry.getAttribute('data-wref')});
        }
        box.addEventListener('pointerup', end);
        box.addEventListener('pointercancel', end);
    }
    // village name: edited in the box (Legends), saved by ajax.php?cmd=changeVillageName
    function initRename() {
        var input = document.querySelector('#sidebarBoxActiveVillage .villageInput');
        if (!input) return;
        var old = input.value;
        function save() {
            var v = input.value.trim();
            if (!v || v === old) { input.value = old; return; }
            var body = 'name=' + encodeURIComponent(v) + '&did=' + encodeURIComponent(input.getAttribute('data-did'));
            fetch('ajax.php?cmd=changeVillageName', {method: 'POST', credentials: 'same-origin',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: body}).then(function () { location.reload(); });
        }
        input.addEventListener('keydown', function (ev) {
            if (ev.key === 'Enter') { ev.preventDefault(); input.blur(); }
            if (ev.key === 'Escape') { input.value = old; input.blur(); }
        });
        input.addEventListener('blur', save);
    }
    function init() { initDrag(); initRename(); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
    return {add: add, toggle: toggle, edit: edit};
})();
</script>
