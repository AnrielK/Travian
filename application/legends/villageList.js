/*
 * Village list of the Travian Legends look (application/views/Legends/sidebarRight.php): village groups (add, edit
 * name and colour, delete, collapse, drag villages and groups to a new place) and the four quick link slots with their
 * dialog. Config: window.VillageListConfig; dialogs: LegendsDialog (application/legends/legendsDialogs.js).
 */
var VillageListUi = (function () {
    var C = window.VillageListConfig || {T: {}, colors: [], links: []};
    var T = C.T;

    function post(data) {
        var body = Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&');
        return fetch('ajax.php?cmd=villageGroups', {method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: body})
            .then(function (r) { return r.json(); }).catch(function () { return null; });
    }
    function box() { return document.getElementById('sidebarBoxVillageList'); }
    function list() { var b = box(); return b && b.querySelector('.villageList'); }

    /* ---------------- groups ---------------- */
    function addGroup() {
        var l = list();
        if (l && l.querySelectorAll('.groupWrapper').length >= +l.getAttribute('data-max')) { LegendsDialog.alert(T.max); return; }
        post({action: 'add', name: T.newGroup}).then(function () { location.reload(); });
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

    /* ---------------- drag & drop of villages and groups ---------------- */
    function structure() {
        var items = [];
        Array.prototype.forEach.call(list().children, function (el) {
            if (el.classList.contains('groupWrapper')) {
                items.push({g: +el.getAttribute('data-gid'), v: Array.prototype.map.call(el.querySelectorAll('.listEntry.village'), function (v) { return +v.getAttribute('data-wref'); })});
            } else if (el.classList.contains('topLevel')) {
                var v = el.querySelector('.listEntry.village');
                if (v) items.push({v: +v.getAttribute('data-wref')});
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
        if (!b) return;
        var drag = null, line = null;
        b.addEventListener('pointerdown', function (ev) {
            var h = ev.target.closest('.dragAndDrop');
            if (!h || !list().contains(h)) return;
            var entry = h.closest('.listEntry');
            if (entry.classList.contains('editMode')) return;
            var kind = entry.classList.contains('group') ? 'group' : 'village';
            var moving = kind === 'group' ? entry.closest('.groupWrapper') : entry;
            ev.preventDefault();
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
            drag = {entry: entry, moving: moving, kind: kind, ghost: ghost, dx: ev.clientX - r.left, dy: ev.clientY - r.top, target: null};
            try { h.setPointerCapture(ev.pointerId); } catch (e) {}
        });
        b.addEventListener('pointermove', function (ev) {
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
            if (!drag) return;
            var d = drag;
            drag = null;
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

    /* ---------------- quick links ---------------- */
    var qlDialog = null, dragLink = null;
    function slots() { return Array.prototype.slice.call(box().querySelectorAll('.quickLinkSlot')); }
    function linkHtml(key) {
        for (var i = 0; i < C.links.length; i++) if (C.links[i].key === key) return C.links[i].html;
        return '';
    }
    function saveSlots() {
        var keys = slots().map(function (s) { var a = s.querySelector('.layoutButton'); return a ? a.getAttribute('data-key') : ''; });
        post({action: 'quickLinks', links: JSON.stringify(keys)});
        if (qlDialog) qlDialog.querySelectorAll('.quickLinkItem').forEach(function (it) {
            it.classList.toggle('used', keys.indexOf(it.getAttribute('data-key')) !== -1);
        });
    }
    function setSlot(slot, key) {
        var old = slot.querySelector('.layoutButton');
        if (old) old.remove();
        if (key) {
            slots().forEach(function (s) {   // a link sits in one slot only
                var a = s.querySelector('.layoutButton');
                if (a && a.getAttribute('data-key') === key) a.remove();
            });
            slot.insertAdjacentHTML('beforeend', linkHtml(key));
        }
    }
    function editing() { return box().classList.contains('editQuickLinks'); }
    function toggleQuickLinks(force) {
        var b = box(), on = typeof force === 'boolean' ? force : !editing();
        b.classList.toggle('editQuickLinks', on);
        b.closest('.sidebar').classList.toggle('editQuickLinks', on);
        slots().forEach(function (s) { s.classList.toggle('editMode', on); });
        b.querySelector('.quickLinks .settings').classList.toggle('editMode', on);
        var ov = b.querySelector('.editQuickLinksOverlay');
        if (on && !ov) {
            ov = document.createElement('div');
            ov.className = 'editQuickLinksOverlay';
            ov.addEventListener('click', function () { toggleQuickLinks(false); });
            b.insertBefore(ov, b.firstChild);
        } else if (!on && ov) {
            ov.remove();
        }
        if (on) openQuickLinksDialog(); else if (qlDialog) { var d = qlDialog; qlDialog = null; LegendsDialog.close(d); }
    }
    function openQuickLinksDialog() {
        var html = '<p class="quickLinksText">' + T.qlText + '</p><div class="quickLinksWrapper">'
            + C.links.map(function (l) {
                return '<div class="quickLinkItem" data-key="' + l.key + '">' + l.html
                    + '<div class="quickLinkTitle">' + l.title + '</div><div class="quickLinkSubtitle">' + l.sub + '</div></div>';
            }).join('') + '</div>';
        qlDialog = LegendsDialog.open({cls: 'basic quickLinksDialog', heading: T.qlTitle, html: html, modal: false,
            onClose: function () { if (qlDialog) { qlDialog = null; toggleQuickLinks(false); } }});
        saveSlots.call(null);
    }
    function initQuickLinks() {
        var b = box();
        if (!b) return;
        // edit mode: links do not navigate; a tap on a dialog link fills the first empty slot, a tap on a slot empties it
        document.addEventListener('click', function (ev) {
            if (!editing()) return;
            var a = ev.target.closest('.quickLink');
            if (!a) return;
            ev.preventDefault();
            ev.stopPropagation();
            if (dragLink && dragLink.moved) return;
            var inSlot = a.closest('.quickLinkSlot');
            if (inSlot) { a.remove(); saveSlots(); return; }
            var free = slots().filter(function (s) { return !s.querySelector('.layoutButton'); })[0];
            if (free) { setSlot(free, a.getAttribute('data-key')); saveSlots(); }
        }, true);
        document.addEventListener('pointerdown', function (ev) {
            if (!editing()) return;
            var a = ev.target.closest('.quickLink');
            if (!a) return;
            ev.preventDefault();
            var r = a.getBoundingClientRect();
            var ghost = a.cloneNode(true);
            ghost.classList.add('quickLinkDragObject');
            ghost.style.left = r.left + 'px';
            ghost.style.top = r.top + 'px';
            document.body.appendChild(ghost);
            dragLink = {a: a, key: a.getAttribute('data-key'), from: a.closest('.quickLinkSlot'), ghost: ghost,
                dx: ev.clientX - r.left, dy: ev.clientY - r.top, x: ev.clientX, y: ev.clientY, moved: false, over: null};
            if (dragLink.from) dragLink.from.classList.add('highlightDragSource');
        }, true);
        document.addEventListener('pointermove', function (ev) {
            if (!dragLink) return;
            if (Math.abs(ev.clientX - dragLink.x) + Math.abs(ev.clientY - dragLink.y) > 6) dragLink.moved = true;
            dragLink.ghost.style.left = (ev.clientX - dragLink.dx) + 'px';
            dragLink.ghost.style.top = (ev.clientY - dragLink.dy) + 'px';
            dragLink.ghost.style.display = 'none';
            var el = document.elementFromPoint(ev.clientX, ev.clientY);
            dragLink.ghost.style.display = '';
            var over = el && el.closest('#sidebarBoxVillageList .quickLinkSlot');
            if (dragLink.over !== over) {
                if (dragLink.over) dragLink.over.classList.remove('highlightDropTarget');
                if (over) over.classList.add('highlightDropTarget');
                dragLink.over = over;
            }
        }, true);
        function end() {
            if (!dragLink) return;
            var d = dragLink;
            d.ghost.remove();
            if (d.from) d.from.classList.remove('highlightDragSource');
            if (d.over) d.over.classList.remove('highlightDropTarget');
            if (!d.moved) { setTimeout(function () { dragLink = null; }, 0); return; }
            if (d.over && d.over !== d.from) {
                var other = d.over.querySelector('.layoutButton');
                var otherKey = other ? other.getAttribute('data-key') : '';
                setSlot(d.over, d.key);
                if (d.from) setSlot(d.from, otherKey);   // swap between slots
            } else if (!d.over && d.from) {
                d.a.remove();   // dragged out of the slots: empty the slot
            }
            saveSlots();
            setTimeout(function () { dragLink = null; }, 0);
        }
        document.addEventListener('pointerup', end, true);
        document.addEventListener('pointercancel', end, true);
    }

    /* ---------------- village name (edited in place) ---------------- */
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

    function init() { initDrag(); initQuickLinks(); initRename(); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
    return {addGroup: addGroup, toggleGroup: toggleGroup, editGroup: editGroup, toggleQuickLinks: toggleQuickLinks};
})();
