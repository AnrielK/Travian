/*
 * Onboarding guide of the Travian Legends look for new players (layout/legendsTutorial.php, resources/View/LegendsTutorial.php),
 * as in the original: a dark speech bubble with a golden frame and an arrow at an element of the page; nothing is dimmed
 * and the page stays usable. The guide has threads that continue on the page they belong to (F resource fields,
 * B new buildings, H hero and adventures, Q tasks, M the adventure on its way). A step continues with the chevrons, OK,
 * or by clicking the element it points at (the click itself does what it always does). The step reached is stored on
 * the server (ajax.php?cmd=legendsTutorial).
 */
(function () {
    var C = window.LegendsTutorialConfig;
    if (!C || !C.state) return;
    var T = C.T || {};
    var state = C.state;
    var path = location.pathname;
    var build = document.getElementById('build');
    var page = /dorf1\.php$/.test(path) ? 'dorf1' : /dorf2\.php$/.test(path) ? 'dorf2'
        : /build\.php$/.test(path) && build && /\bgid[1-4]\b/.test(build.className) ? 'buildRes'
        : /build\.php$/.test(path) && build && /\bgid0\b/.test(build.className) ? 'buildNew'
        : /hero\.php$/.test(path) && document.querySelector('table.adventureList') ? 'adventures' : 'other';

    var FIELD = '#resourceFieldContainer a.level';
    var UPGRADE = '#build .upgradeButtonsContainer .section1 button';
    var SLOT = '#villageContent .buildingSlot.g0:not(.legendsWall)';
    var HERO = '#heroImageButton';
    // per thread: steps (1-based). pages: where it is shown; target: the element pointed at; side: where the bubble
    // stands; click: clicking it (or these elements) continues; group: steps that share one progress bar
    var THREADS = {
        F: [
            {pages: ['dorf1'], target: '#resourceFieldContainer a.level0, ' + FIELD, side: 'above', text: 'fields', click: FIELD},
            {pages: ['buildRes'], target: '#build #build_value', side: 'right', text: 'production', group: [2, 4]},
            {pages: ['buildRes'], target: '#build .upgradeBuilding .resourceWrapper', side: 'left', text: 'costs', group: [2, 4]},
            {pages: ['buildRes'], target: UPGRADE, side: 'above', text: 'upgrade', group: [2, 4], click: UPGRADE},
            {pages: ['dorf1'], target: '.buildingList', side: 'below', text: 'queue', click: FIELD},
            {pages: ['buildRes'], target: UPGRADE, side: 'above', text: 'second', ok: true}
        ],
        B: [
            {pages: ['dorf2'], target: SLOT, side: 'above', text: 'slots', click: SLOT},
            {pages: ['buildNew'], target: '#build .buildingWrapper .build_desc', side: 'above', text: 'description', group: [2, 4]},
            {pages: ['buildNew'], target: '#build .contentNavi', side: 'above', text: 'tabs', group: [2, 4]},
            {pages: ['buildNew'], target: '#build .contentNavi', side: 'above', text: 'soon', group: [2, 4], ok: true}
        ],
        H: [
            {pages: ['dorf1', 'dorf2'], target: HERO, side: 'below', text: 'hero', group: [1, 2]},
            {pages: ['dorf1', 'dorf2'], target: HERO, side: 'below', text: 'adventure', group: [1, 2], click: HERO, go: 'hero.php?t=3'},
            {pages: ['adventures'], target: 'table.adventureList th.duration', side: 'above', text: 'duration', group: [3, 5]},
            {pages: ['adventures'], target: 'table.adventureList th.difficulty', side: 'above', text: 'difficulty', group: [3, 5]},
            {pages: ['adventures'], target: 'table.adventureList td.button button', side: 'above', text: 'explore', group: [3, 5], click: 'table.adventureList td.button button'}
        ],
        Q: [
            {pages: ['dorf1', 'dorf2'], target: '#sidebarBoxQuestmaster', side: 'above', text: 'tasks', ok: true, after: 'H'}
        ],
        M: [
            {pages: ['dorf1'], target: '.villageInfobox.movements', side: 'below', text: 'meanwhile', ok: true, after: 'H',
                when: function () { return !!document.querySelector('.villageInfobox.movements table, .villageInfobox.movements tr'); }}
        ]
    };
    var ORDER = {dorf1: ['F', 'H', 'M', 'Q'], dorf2: ['B', 'H', 'Q'], buildRes: ['F'], buildNew: ['B'], adventures: ['H']};

    var cur = null; // {thread, n, s, el, bubble, arrow}

    function save(thread, n) {
        state[thread] = n;
        var body = 'thread=' + thread + '&step=' + n + '&ajaxToken=' + encodeURIComponent(window.ajaxToken || '');
        try {
            fetch('ajax.php?cmd=legendsTutorial', {method: 'POST', credentials: 'same-origin', keepalive: true,
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: body});
        } catch (e) {}
    }
    function visible(el) {
        var r = el.getBoundingClientRect();
        return r.width > 0 && r.height > 0 && getComputedStyle(el).visibility !== 'hidden';
    }
    function find(sel) {
        var list = document.querySelectorAll(sel);
        for (var i = 0; i < list.length; i++) if (visible(list[i])) return list[i];
        return null;
    }
    function onPage(s) { return s.pages.indexOf(page) >= 0; }
    // the step of a thread for this page: the current one, else the last earlier step shown here (the thread
    // continues from there), else the next one when the current step's click has already led here
    function stepFor(thread) {
        var steps = THREADS[thread], n = state[thread];
        if (n < 0) return 0;
        if (n === 0) n = 1;
        var s = steps[n - 1];
        if (!s || (s.after && state[s.after] >= 0) || (s.when && !s.when())) return 0;
        if (onPage(s)) return n;
        for (var i = n - 1; i >= 1; i--) if (onPage(steps[i - 1]) && (i === 1 || steps[i - 2].click || steps[i - 2].ok)) return i;
        if (s.click && steps[n] && onPage(steps[n])) return n + 1;
        return 0;
    }
    function clear() {
        if (cur) cur.bubble.remove();
        cur = null;
    }
    function next(n) {
        var t = cur.thread, steps = THREADS[t];
        clear();
        save(t, n > steps.length ? -1 : n);
        if (n <= steps.length && onPage(steps[n - 1])) show(t, n);
        else showAny();
    }
    function chevron(cls, d) { return '<svg class="' + cls + '" viewBox="0 0 12 17"><path d="' + d + '"/></svg>'; }
    function show(thread, n) {
        var s = THREADS[thread][n - 1], el = find(s.target);
        if (!el) return false;
        var b = document.createElement('div');
        b.className = 'legendsGuide';
        var html = '';
        if (s.group) {
            html += '<div class="progress">';
            for (var i = s.group[0]; i <= s.group[1]; i++) html += '<div' + (i === n ? ' class="active"' : '') + '></div>';
            html += '</div>';
        }
        html += '<div class="helpText">' + (T[s.text] || '') + '</div>';
        var back = s.group && n > s.group[0], fwd = s.group && n < s.group[1] && !s.click && !s.ok;
        if (back || fwd || s.ok) {
            html += '<nav>' + (back ? chevron('chevronBack', 'M10 2L3 8.5l7 6.5') : '') + (fwd ? chevron('chevronForward', 'M2 2l7 6.5L2 15') : '')
                + (s.ok ? '<button type="button" class="ok">' + (T.ok || 'OK') + '</button>' : '') + '</nav>';
        }
        html += '<svg class="pointingArrow"><path class="fill"/><path class="line"/></svg>';
        b.innerHTML = html;
        document.body.appendChild(b);
        cur = {thread: thread, n: n, s: s, el: el, bubble: b};
        b.addEventListener('click', function (ev) {
            var x = ev.target.closest('svg.chevronBack, svg.chevronForward, button.ok');
            if (!x) return;
            ev.preventDefault();
            next(x.classList.contains('chevronBack') ? n - 1 : n + 1);
        });
        place();
        return true;
    }
    function showAny() {
        var list = ORDER[page] || [];
        for (var i = 0; i < list.length; i++) {
            var n = stepFor(list[i]);
            if (n && show(list[i], n)) return;
        }
    }
    var ARROW = 26, ARROW_W = 36; // depth and width of the arrow
    function place() {
        if (!cur) return;
        if (!document.body.contains(cur.el) || !visible(cur.el)) { var t = cur.thread, n = cur.n; clear(); if (!show(t, n)) showAny(); return; }
        var b = cur.bubble, r = cur.el.getBoundingClientRect();
        var vw = document.documentElement.clientWidth, vh = window.innerHeight;
        b.style.width = Math.min(350, vw - 16) + 'px';
        var bw = b.offsetWidth, bh = b.offsetHeight, gap = ARROW + 2;
        var room = {above: r.top - bh - gap, below: vh - r.bottom - bh - gap, left: r.left - bw - gap, right: vw - r.right - bw - gap};
        var side = cur.s.side;
        var flip = {above: 'below', below: 'above', left: 'right', right: 'left'};
        if (room[side] < 4 && room[flip[side]] > room[side]) side = flip[side];
        if ((side === 'left' || side === 'right') && room[side] < 4) side = room.above > room.below ? 'above' : 'below';
        // a big element (the resource fields, the build menu): point at its upper part, the bubble inside the view
        var cx = (r.left + r.right) / 2, cy = (r.top + Math.min(r.bottom, r.top + 120)) / 2;
        var left, top;
        if (side === 'above' || side === 'below') {
            left = Math.min(Math.max(8, cx - bw / 2), vw - bw - 8);
            top = side === 'above' ? r.top - bh - gap : r.bottom + gap;
        } else {
            top = Math.min(Math.max(8, cy - bh / 2), Math.max(8, vh - bh - 8));
            left = side === 'left' ? r.left - bw - gap : r.right + gap;
        }
        var sx = window.pageXOffset, sy = window.pageYOffset;
        b.style.left = (left + sx) + 'px';
        b.style.top = (top + sy) + 'px';
        b.className = 'legendsGuide ' + side;
        var a = b.querySelector('svg.pointingArrow'), w = ARROW_W, d = ARROW, fill, line, ax, ay, vbw, vbh;
        if (side === 'above' || side === 'below') {
            vbw = w; vbh = d;
            ax = Math.min(Math.max(14, cx - left - w / 2), bw - w - 14);
            ay = side === 'above' ? bh - 1 : -d + 1;
            line = side === 'above' ? 'M0 0L' + w / 2 + ' ' + d + 'L' + w + ' 0' : 'M0 ' + d + 'L' + w / 2 + ' 0L' + w + ' ' + d;
        } else {
            vbw = d; vbh = w;
            ay = Math.min(Math.max(14, cy - top - w / 2), bh - w - 14);
            ax = side === 'left' ? bw - 1 : -d + 1;
            line = side === 'left' ? 'M0 0L' + d + ' ' + w / 2 + 'L0 ' + w : 'M' + d + ' 0L0 ' + w / 2 + 'L' + d + ' ' + w;
        }
        fill = line + 'Z';
        a.setAttribute('viewBox', '0 0 ' + vbw + ' ' + vbh);
        a.setAttribute('width', vbw); a.setAttribute('height', vbh);
        a.style.left = ax + 'px'; a.style.top = ay + 'px';
        a.querySelector('.fill').setAttribute('d', fill);
        a.querySelector('.line').setAttribute('d', line);
    }
    // a click on the element of a click step continues the thread (on the next page)
    document.addEventListener('click', function (ev) {
        if (!cur || !cur.s.click || !ev.target.closest(cur.s.click)) return;
        var t = cur.thread, n = cur.n, s = cur.s;
        save(t, n + 1 > THREADS[t].length ? -1 : n + 1);
        clear();
        if (s.go) { ev.preventDefault(); ev.stopPropagation(); location.href = s.go; }
    }, true);
    window.addEventListener('resize', place);
    setInterval(place, 300); // follows the page (layout changes, the iOS app zooming)
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { setTimeout(showAny, 300); });
    else setTimeout(showAny, 300);
})();
