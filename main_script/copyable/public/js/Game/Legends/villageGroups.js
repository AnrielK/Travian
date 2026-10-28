/*
 * Village groups of the Travian Legends 655 village list (layout/legendsVillageList.php), port of the travian-T4.6
 * branch: add a group (it goes to the end of the list), edit its name and colour, delete it (its villages stay),
 * collapse it, and drag villages and groups to a new place (a village dropped on a group goes into it).
 * Config: window.LegendsVillageGroupsConfig; server: ajax.php?cmd=villageGroups; dialogs: window.LegendsDialog.
 */
var LegendsVillageGroups = (function () {
    var C = window.LegendsVillageGroupsConfig || {T: {}, colors: []};
    var T = C.T;

    function post(data) {
        data.ajaxToken = window.ajaxToken || '';
        var body = Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&');
        return fetch('ajax.php?cmd=villageGroups', {method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: body})
            .then(function (r) { return r.json(); })
            .then(function (j) { return j && j.response ? j.response : null; })
            .catch(function () { return null; });
    }
    function box() { return document.getElementById('sidebarBoxVillagelist'); }
    function list() { var b = box(); return b && b.querySelector('.villageList'); }

    /* ---------------- groups ---------------- */
    function addGroup() {
        var l = list();
        if (l && l.querySelectorAll('.groupWrapper').length >= +l.getAttribute('data-max')) { LegendsDialog.alert(T.max); return; }
        post({action: 'add', name: T.newGroup}).then(function (r) {
            if (r && r.error) { LegendsDialog.alert(r.errorMsg || T.max); return; }
            location.reload();
        });
    }
    function toggleGroup(btn) {
        var w = btn.closest('.groupWrapper');
        var collapsed = w.classList.toggle('collapsed');
        btn.querySelector('svg').classList.toggle('expanded', !collapsed);
        post({action: 'collapse', gid: w.getAttribute('data-gid'), collapsed: collapsed ? 1 : 0});
    }
    function editGroup(btn) {
        var row = btn.closest('.listEntry.group');
        if (row.classList.contains('editMode')) return;
        var gid = row.getAttribute('data-gid'), color = row.getAttribute('data-color');
        var name = row.querySelector('.name').textContent.trim();
        var original = row.innerHTML;
        row.classList.add('editMode');
        var swatches = C.colors.map(function (c) {
            return '<div class="color ' + c + '" data-color="' + c + '"><div class="selectedIndicator"></div></div>';
        }).join('');
        row.innerHTML = '<div class="editModeOverlay"></div><input type="text" class="name" maxlength="40">'
            + '<div class="colorSelection"><div class="color current ' + color + '" title="' + T.color + '"></div>'
            + '<div class="availableColors" hidden>' + swatches + '</div></div>'
            + '<div class="actions"><button type="button" class="secondaryIconButton delete" title="' + T.del + '">'
            + '<svg viewBox="0 0 14 16"><path d="M1 3h12v1.5H1zM5 0h4v1.5h4V3H1V1.5h4zM2.5 5h9l-.8 11H3.3zM5 7v7h1.2V7zm2.8 0v7H9V7z"/></svg></button></div>';
        var input = row.querySelector('input.name');
        input.value = name;
        input.focus();
        input.select();
        var current = row.querySelector('.color.current'), palette = row.querySelector('.availableColors');
        function mark() {
            palette.querySelectorAll('.color').forEach(function (s) { s.classList.toggle('selected', s.getAttribute('data-color') === color); });
        }
        mark();
        current.addEventListener('click', function () { palette.hidden = !palette.hidden; });
        palette.addEventListener('click', function (ev) {
            var s = ev.target.closest('.color');
            if (!s) return;
            current.classList.remove(color);
            row.classList.remove(color);
            color = s.getAttribute('data-color');
            current.classList.add(color);
            row.classList.add(color);
            mark();
            palette.hidden = true;
            input.focus();
        });
        var done = false;
        function save() {
            if (done) return;
            done = true;
            var v = input.value.trim() || name;
            post({action: 'color', gid: gid, color: color}).then(function () {
                return post({action: 'rename', gid: gid, name: v});
            }).then(function () {
                row.classList.remove('editMode');
                row.setAttribute('data-color', color);
                row.innerHTML = original;
                row.querySelector('.name').textContent = v;
            });
        }
        input.addEventListener('keydown', function (ev) {
            if (ev.key === 'Enter') { ev.preventDefault(); save(); }
            if (ev.key === 'Escape') { done = true; location.reload(); }
        });
        row.querySelector('.editModeOverlay').addEventListener('click', save);
        row.querySelector('.delete').addEventListener('click', function () {
            done = true;
            LegendsDialog.confirm(T.delConfirm, T.cancel, T['delete'], function (ok) {
                if (ok) post({action: 'delete', gid: gid}).then(function () { location.reload(); });
                else { done = false; input.focus(); }
            });
        });
    }

    /* ---------------- selection: a tap on a group row shows its handle and buttons (as on the phone in Legends) ---------------- */
    function initSelection() {
        document.addEventListener('click', function (ev) {
            var l = list();
            if (!l) return;
            var row = ev.target.closest('.listEntry.group');
            if (row && !l.contains(row)) row = null;
            if (row && (row.classList.contains('editMode') || ev.target.closest('button, input, .colorSelection'))) return;
            l.querySelectorAll('.listEntry.group.selected').forEach(function (r) { if (r !== row) r.classList.remove('selected'); });
            if (row) row.classList.toggle('selected');
        });
    }

    /* ---------------- drag & drop of villages and groups ---------------- */
    function structure() {
        var items = [];
        Array.prototype.forEach.call(list().children, function (el) {
            if (el.classList.contains('groupWrapper')) {
                items.push({g: +el.getAttribute('data-gid'), v: Array.prototype.map.call(el.querySelectorAll('.listEntry.village'), function (v) { return +v.getAttribute('data-kid'); })});
            } else if (el.classList.contains('topLevel')) {
                var v = el.querySelector('.listEntry.village');
                if (v) items.push({v: +v.getAttribute('data-kid')});
            }
        });
        return items;
    }
    // where the dragged entry would go: {parent, before, line: [x, y, width]} or null
    function dropTarget(x, y, kind) {
        var l = list(), lr = l.getBoundingClientRect();
        if (x < lr.left - 30 || x > lr.right + 30 || y < lr.top - 10 || y > lr.bottom + 20) return null;
        var top = Array.prototype.filter.call(l.children, function (el) { return el.classList.contains('groupWrapper') || el.classList.contains('topLevel'); });
        if (kind === 'village') {
            // inside a group: over its title row (first place) or over / below its villages
            for (var i = 0; i < top.length; i++) {
                var w = top[i];
                if (!w.classList.contains('groupWrapper')) continue;
                var r = w.getBoundingClientRect();
                if (y < r.top || y > r.bottom) continue;
                var head = w.querySelector('.listEntry.group').getBoundingClientRect();
                var dc = w.querySelector('.dropContainer');
                if (y <= head.top + head.height / 2) break;   // upper half of the title row: before the group (top level)
                var vs = w.classList.contains('collapsed') ? [] : Array.prototype.slice.call(dc.querySelectorAll('.listEntry.village'));
                for (var j = 0; j < vs.length; j++) {
                    var vr = vs[j].getBoundingClientRect();
                    if (y < vr.top + vr.height / 2) return {parent: dc, before: vs[j], line: [vr.left, vr.top, vr.width]};
                }
                var last = vs.length ? vs[vs.length - 1].getBoundingClientRect() : head;
                return {parent: dc, before: null, line: [head.left, last.bottom, head.width]};
            }
        }
        for (var k = 0; k < top.length; k++) {
            var tr = top[k].getBoundingClientRect();
            if (y < tr.top + Math.min(tr.height, 22) / 2) return {parent: l, before: top[k], line: [tr.left, tr.top, tr.width]};
        }
        var lastTop = top.length ? top[top.length - 1].getBoundingClientRect() : lr;
        return {parent: l, before: null, line: [lastTop.left, lastTop.bottom, lastTop.width]};
    }
    function initDrag() {
        var b = box();
        if (!b || !list()) return;
        var drag = null, line = null;
        var press = null, suppressClick = false;
        function start(entry, h, ev) {
            var kind = entry.classList.contains('group') ? 'group' : 'village';
            var moving = kind === 'group' ? entry.closest('.groupWrapper') : entry;
            var r = entry.getBoundingClientRect();
            var ghost = document.createElement('div');
            ghost.className = 'dragObject';
            ghost.appendChild(entry.cloneNode(true));
            ghost.style.left = r.left + 'px';
            ghost.style.top = r.top + 'px';
            ghost.style.width = r.width + 'px';
            list().appendChild(ghost);
            line = document.createElement('div');
            line.className = 'villageListDropLine';
            document.body.appendChild(line);
            entry.classList.add('dragged');
            list().classList.add('dragging');
            drag = {entry: entry, moving: moving, kind: kind, ghost: ghost, dx: ev.clientX - r.left, dy: ev.clientY - r.top, target: null};
            try { h.setPointerCapture(ev.pointerId); } catch (e) {}
        }
        b.addEventListener('pointerdown', function (ev) {
            var l = list();
            var h = ev.target.closest('.dragAndDrop');
            var entry = (h || ev.target).closest('.listEntry');
            if (!entry || !l.contains(entry) || entry.classList.contains('editMode') || !entry.querySelector('.dragAndDrop')) return;
            if (h) { ev.preventDefault(); start(entry, h, ev); return; }
            // a long press on a village (phone / tablet) picks it up like its handle
            if (ev.pointerType === 'mouse' || !entry.classList.contains('village')) return;
            var x = ev.clientX, y = ev.clientY, id = ev.pointerId;
            press = {x: x, y: y, timer: setTimeout(function () {
                press = null;
                suppressClick = true;
                if (navigator.vibrate) { try { navigator.vibrate(15); } catch (e) {} }
                start(entry, entry.querySelector('.dragAndDrop'), {clientX: x, clientY: y, pointerId: id});
            }, 450)};
        });
        // no page scroll while a village is dragged on a touch screen
        b.addEventListener('touchmove', function (ev) { if (drag) ev.preventDefault(); }, {passive: false});
        b.addEventListener('click', function (ev) {
            if (suppressClick) { suppressClick = false; ev.preventDefault(); ev.stopPropagation(); }
        }, true);
        b.addEventListener('contextmenu', function (ev) { if (drag || press) ev.preventDefault(); });
        b.addEventListener('pointermove', function (ev) {
            if (press && Math.abs(ev.clientX - press.x) + Math.abs(ev.clientY - press.y) > 8) { clearTimeout(press.timer); press = null; }
            if (!drag) return;
            drag.ghost.style.left = (ev.clientX - drag.dx) + 'px';
            drag.ghost.style.top = (ev.clientY - drag.dy) + 'px';
            var t = dropTarget(ev.clientX, ev.clientY, drag.kind);
            drag.target = t;
            if (t) {
                line.style.display = 'block';
                line.style.left = (t.line[0] + window.scrollX) + 'px';
                line.style.top = (t.line[1] + window.scrollY - 1) + 'px';
                line.style.width = t.line[2] + 'px';
            } else {
                line.style.display = 'none';
            }
        });
        function end(ev) {
            if (press) { clearTimeout(press.timer); press = null; }
            if (suppressClick) setTimeout(function () { suppressClick = false; }, 400);
            if (!drag) return;
            var d = drag;
            drag = null;
            list().classList.remove('dragging');
            d.ghost.remove();
            line.remove();
            d.entry.classList.remove('dragged');
            var t = d.target;
            if (!t || ev.type === 'pointercancel') return;
            if (t.before === d.moving || (d.kind === 'village' && t.before === d.moving.parentNode)) return;
            if (d.kind === 'group') {
                list().insertBefore(d.moving, t.before);
            } else if (t.parent === list()) {
                var oldTop = d.moving.parentNode.classList.contains('topLevel') ? d.moving.parentNode : null;
                var holder = oldTop || document.createElement('div');
                holder.className = 'dropContainer topLevel';
                holder.setAttribute('data-gid', '0');
                holder.appendChild(d.moving);
                list().insertBefore(holder, t.before);
            } else {
                var prev = d.moving.parentNode;
                t.parent.insertBefore(d.moving, t.before);
                if (prev.classList.contains('topLevel') && !prev.children.length) prev.remove();
            }
            post({action: 'order', items: JSON.stringify(structure())});
        }
        b.addEventListener('pointerup', end);
        b.addEventListener('pointercancel', end);
    }

    function init() { initDrag(); initSelection(); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
    return {addGroup: addGroup, toggleGroup: toggleGroup, editGroup: editGroup};
})();
