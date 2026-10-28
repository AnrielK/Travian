/*
 * Quick links of the Travian Legends 655 look (resources/View/LegendsQuickLinks.php):
 *   'list'    = the four slots in the header of the village list (one set for all villages),
 *   'village' = the five slots of the active village box (one set per village).
 * The "…" button starts the editor: the page is dimmed, the edited box stays above it and the dialog lists all
 * links. A tap on a link of the dialog fills the first empty slot, a tap on a slot empties it; links can also be
 * dragged into a slot, between slots (swap) or out of the slots. Every change is stored (ajax.php?cmd=quickLinks).
 * Dialog: window.LegendsDialog (js/Game/Legends/dialog.js).
 */
var LegendsQuickLinks = (function () {
    var C = window.LegendsQuickLinksConfig || {T: {}, links: []};
    var T = C.T;
    var SCOPES = {
        list: {box: function () { return document.getElementById('sidebarBoxVillagelist'); }, title: function () { return T.listTitle; }, text: function () { return T.listText; }},
        village: {box: function () { return document.getElementById('sidebarBoxActiveVillage'); }, title: function () { return T.villageTitle; }, text: function () { return T.villageText; }}
    };
    var dialog = null, drag = null, scope = null; // scope = the set being edited

    function post(data) {
        data.ajaxToken = window.ajaxToken || '';
        var body = Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&');
        return fetch('ajax.php?cmd=quickLinks', {method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: body})
            .then(function (r) { return r.json(); }).catch(function () { return null; });
    }
    function wrapper(name) { var b = SCOPES[name || scope || 'list'].box(); return b && b.querySelector('.buttonsWrapper.quickLinks'); }
    function slots(name) { var w = wrapper(name); return w ? Array.prototype.slice.call(w.querySelectorAll('.quickLinkSlot')) : []; }
    function linkHtml(key) {
        for (var i = 0; i < C.links.length; i++) if (C.links[i].key === key) return C.links[i].html;
        return '';
    }
    function save() {
        var keys = slots().map(function (s) { var a = s.querySelector('.layoutButton'); return a ? a.getAttribute('data-key') : ''; });
        var data = {links: JSON.stringify(keys), kid: scope === 'village' ? (wrapper().getAttribute('data-kid') || 0) : 0};
        post(data);
        markUsed(keys);
    }
    function markUsed(keys) {
        if (!dialog) return;
        Array.prototype.forEach.call(dialog.querySelectorAll('.quickLinkItem'), function (it) {
            it.classList.toggle('used', keys.indexOf(it.getAttribute('data-key')) !== -1);
        });
    }
    function setSlot(slot, key) {
        var old = slot.querySelector('.layoutButton');
        if (old) old.parentNode.removeChild(old);
        if (!key) return;
        slots().forEach(function (s) { // a link sits in one slot only
            var a = s.querySelector('.layoutButton');
            if (a && a.getAttribute('data-key') === key) a.parentNode.removeChild(a);
        });
        slot.insertAdjacentHTML('beforeend', linkHtml(key));
    }
    function editing() { return scope !== null; }

    function toggle(force, name) {
        name = name || 'list';
        var on = typeof force === 'boolean' ? force : !(editing() && scope === name);
        if (on && editing() && scope !== name) toggle(false, scope); // one set at a time
        if (!on && !editing()) return;
        if (!on) name = scope;
        var b = SCOPES[name].box();
        if (!b) return;
        b.classList.toggle('editQuickLinks', on);
        var sidebar = b.closest('.sidebar') || b.parentNode;
        sidebar.classList.toggle('editQuickLinks', on);
        document.body.classList.toggle('legendsQuickLinksEditing', on);
        slots(name).forEach(function (s) { s.classList.toggle('editMode', on); });
        var settings = wrapper(name).parentNode.querySelector('.settings');
        if (settings) settings.classList.toggle('editMode', on);
        var ov = document.getElementById('legendsQuickLinksOverlay');
        if (on && !ov) {
            // dims the whole page; the edited box and the dialog stay above it
            ov = document.createElement('div');
            ov.id = 'legendsQuickLinksOverlay';
            ov.addEventListener('click', function () { toggle(false); });
            (document.getElementById('background') || document.body).appendChild(ov);
        } else if (!on && ov) {
            ov.parentNode.removeChild(ov);
        }
        scope = on ? name : null;
        if (on) {
            openDialog();
        } else if (dialog) {
            var d = dialog;
            dialog = null;
            LegendsDialog.close(d);
        }
    }
    function openDialog() {
        var sc = SCOPES[scope];
        var indicator = C.ring || '';
        var html = '<p class="quickLinksText">' + sc.text() + '</p><div class="quickLinksWrapper">'
            + C.links.map(function (l) {
                return '<div class="quickLinkItem" data-key="' + l.key + '"><div class="quickLinkIcon">' + indicator + l.html + '</div>'
                    + '<div class="quickLinkTitle">' + l.title + '</div><div class="quickLinkSubtitle">' + l.sub + '</div></div>';
            }).join('') + '</div>';
        dialog = LegendsDialog.open({cls: 'basic quickLinksDialog', heading: sc.title(), html: html, modal: false, closeText: T.close,
            onClose: function () { if (dialog) { dialog = null; toggle(false); } }});
        dialog.classList.add('quickLinksOverlay');
        place();
        markUsed(slots().map(function (s) { var a = s.querySelector('.layoutButton'); return a ? a.getAttribute('data-key') : ''; }));
    }

    // as in Legends the dialog stands left of the edited box (centred when there is no room for it)
    function place() {
        if (!dialog || !scope) return;
        var w = dialog.querySelector('.dialogWrapper'), b = SCOPES[scope].box();
        if (!w || !b) return;
        w.style.position = ''; w.style.left = ''; w.style.top = '';
        var r = b.getBoundingClientRect(), dw = w.offsetWidth, dh = w.offsetHeight;
        var left = r.left - dw - 14;
        if (left < 8) { dialog.classList.remove('besideBox'); return; }
        dialog.classList.add('besideBox');
        w.style.position = 'fixed';
        w.style.left = left + 'px';
        w.style.top = Math.max(8, Math.min(r.top - 20, window.innerHeight - dh - 8)) + 'px';
    }
    window.addEventListener('resize', place);
    window.addEventListener('scroll', place, {passive: true});
    function dialogItem(key) { return dialog ? dialog.querySelector('.quickLinkItem[data-key="' + key + '"]') : null; }

    function init() {
        // edit mode: links do not navigate; a tap on a dialog link fills the first empty slot, a tap on a slot empties it
        document.addEventListener('click', function (ev) {
            if (!editing()) return;
            var a = ev.target.closest('.quickLink');
            if (!a) return;
            ev.preventDefault();
            ev.stopPropagation();
            if (drag && drag.moved) return;
            var inSlot = a.closest('.quickLinkSlot');
            if (inSlot) {
                if (slots().indexOf(inSlot) !== -1) { a.parentNode.removeChild(a); save(); }
                return;
            }
            var free = slots().filter(function (s) { return !s.querySelector('.layoutButton'); })[0];
            if (free) { setSlot(free, a.getAttribute('data-key')); save(); }
        }, true);
        document.addEventListener('pointerdown', function (ev) {
            if (!editing()) return;
            var a = ev.target.closest('.quickLink');
            if (!a) return;
            var from = a.closest('.quickLinkSlot');
            if (from && slots().indexOf(from) === -1) return; // the other set is not being edited
            ev.preventDefault();
            var r = a.getBoundingClientRect();
            var ghost = a.cloneNode(true);
            ghost.classList.add('quickLinkDragObject');
            ghost.style.left = r.left + 'px';
            ghost.style.top = r.top + 'px';
            document.body.appendChild(ghost);
            drag = {a: a, key: a.getAttribute('data-key'), from: from, ghost: ghost,
                dx: ev.clientX - r.left, dy: ev.clientY - r.top, x: ev.clientX, y: ev.clientY, moved: false, over: null};
            if (from) from.classList.add('highlightDragSource');
            else if (a.closest('.quickLinkItem')) a.closest('.quickLinkItem').classList.add('highlightDragSource');
        }, true);
        document.addEventListener('pointermove', function (ev) {
            if (!drag) return;
            if (Math.abs(ev.clientX - drag.x) + Math.abs(ev.clientY - drag.y) > 6) drag.moved = true;
            drag.ghost.style.left = (ev.clientX - drag.dx) + 'px';
            drag.ghost.style.top = (ev.clientY - drag.dy) + 'px';
            drag.ghost.style.display = 'none';
            var el = document.elementFromPoint(ev.clientX, ev.clientY);
            drag.ghost.style.display = '';
            var over = el && el.closest('.quickLinkSlot');
            if (over && slots().indexOf(over) === -1) over = null;
            // a link lifted from a slot can go back to its place in the dialog: that place is the cyan circle
            // (shown from the start of the drag); it counts only when the link is dropped onto it
            if (drag.from && !drag.home) {
                drag.home = dialogItem(drag.key);
                if (drag.home) drag.home.classList.add('highlightDropTarget');
            }
            drag.back = null;
            if (drag.home && !over) {
                var hi = drag.home.querySelector('.quickLinkIcon') || drag.home, hr = hi.getBoundingClientRect();
                if (ev.clientX >= hr.left - 6 && ev.clientX <= hr.right + 6 && ev.clientY >= hr.top - 6 && ev.clientY <= hr.bottom + 6) drag.back = drag.home;
            }
            if (drag.over !== over) {
                if (drag.over) drag.over.classList.remove('highlightDropTarget');
                if (over) over.classList.add('highlightDropTarget');
                drag.over = over;
            }
        }, true);
        // the dragged copy flies to a place, then disappears
        function fly(ghost, rect, cb) {
            if (!rect) { ghost.remove(); cb(); return; }
            ghost.style.transition = 'left .18s ease-out, top .18s ease-out';
            ghost.style.left = rect.left + 'px';
            ghost.style.top = rect.top + 'px';
            var done = false;
            function fin() { if (done) return; done = true; ghost.remove(); cb(); }
            ghost.addEventListener('transitionend', fin);
            setTimeout(fin, 260);
        }
        function buttonRect(container) {
            var b = container && (container.querySelector('.layoutButton') || container.querySelector('.slot') || container);
            if (!b) return null;
            var r = b.getBoundingClientRect(), g = drag.ghost.getBoundingClientRect();
            return {left: r.left + (r.width - g.width) / 2, top: r.top + (r.height - g.height) / 2};
        }
        function end(ev) {
            if (!drag) return;
            var d = drag;
            function clean() {
                if (d.from) d.from.classList.remove('highlightDragSource');
                if (d.over) d.over.classList.remove('highlightDropTarget');
                if (d.home) d.home.classList.remove('highlightDropTarget');
                if (dialog) dialog.querySelectorAll('.quickLinkItem.highlightDragSource').forEach(function (i) { i.classList.remove('highlightDragSource'); });
                setTimeout(function () { drag = null; }, 0);
            }
            if (!d.moved) { d.ghost.remove(); clean(); return; }
            var cancelled = ev && ev.type === 'pointercancel';
            if (!cancelled && d.over && d.over !== d.from) {
                // dropped onto a cyan slot: the link snaps into it (a link already there swaps places)
                fly(d.ghost, buttonRect(d.over), function () {
                    var other = d.over.querySelector('.layoutButton');
                    var otherKey = other ? other.getAttribute('data-key') : '';
                    setSlot(d.over, d.key);
                    if (d.from) setSlot(d.from, otherKey);
                    clean();
                    save();
                });
            } else if (!cancelled && d.from && d.back) {
                // dropped onto its cyan place in the dialog: the slot is emptied
                fly(d.ghost, buttonRect(d.back), function () {
                    if (d.a.parentNode) d.a.parentNode.removeChild(d.a);
                    clean();
                    save();
                });
            } else {
                // anywhere else: back to where it was lifted, nothing changes
                fly(d.ghost, buttonRect(d.from || d.a.closest('.quickLinkIcon')), clean);
            }
        }
        document.addEventListener('pointerup', end, true);
        document.addEventListener('pointercancel', end, true);
        document.addEventListener('keydown', function (ev) { if (ev.key === 'Escape' && editing()) toggle(false); });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
    return {toggle: toggle};
})();
