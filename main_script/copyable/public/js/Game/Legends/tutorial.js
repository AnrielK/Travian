/*
 * Onboarding guide of the Travian Legends look for new players (layout/legendsTutorial.php, resources/View/LegendsTutorial.php):
 * the page is dimmed, the element of the step stays lit and a dark speech bubble explains it. Steps that ask for a click
 * let only the lit element be clicked; the others continue with the arrows or OK. The step reached is stored on the server
 * (ajax.php?cmd=legendsTutorial), so the guide continues on the next page and after a reload.
 */
(function () {
    var C = window.LegendsTutorialConfig;
    if (!C || !C.step) return;
    var T = C.T || {};
    var path = location.pathname;
    var page = /dorf1\.php$/.test(path) ? 'dorf1' : /dorf2\.php$/.test(path) ? 'dorf2' : /build\.php$/.test(path) ? 'build' : /hero\.php$/.test(path) ? 'hero' : 'other';
    var resourceBuild = page === 'build' && document.body.classList.contains('perspectiveResources') && document.getElementById('build');

    // page: where the step is shown; target: the lit element (first one found); next: the step after it;
    // click: the step continues when the lit element is clicked (the click itself does what it always does)
    var STEPS = {
        1: {pages: ['dorf1'], target: ['#resourceFieldContainer'], text: 'fields', click: true, next: 2},
        2: {pages: ['buildRes'], target: ['#build #build_value', '#build .description'], text: 'production', next: 3},
        3: {pages: ['buildRes'], target: ['#build .inlineIconList.resourceWrapper', '#build .resourceWrapper'], text: 'costs', prev: 2, next: 4},
        4: {pages: ['buildRes'], target: ['#build .upgradeButtonsContainer button.build', '#build .upgradeButtonsContainer'], text: 'upgrade', prev: 3, click: true, next: 5},
        5: {pages: ['dorf1'], target: ['#resourceFieldContainer'], text: 'queue', click: true, next: 6},
        6: {pages: ['buildRes'], target: ['#build .upgradeButtonsContainer'], text: 'second', ok: true, next: 7},
        7: {pages: ['dorf1', 'dorf2', 'build', 'other'], target: ['#heroImageButton'], text: 'hero', next: 8},
        8: {pages: ['dorf1', 'dorf2', 'build', 'other'], target: ['#topBarHero a.layoutButton.adventure'], text: 'adventure', prev: 7, click: true, next: 9},
        9: {pages: ['dorf1'], target: ['.villageInfobox.movements', '#resourceFieldContainer'], text: 'meanwhile', ok: true, next: 10},
        10: {pages: ['dorf1', 'dorf2'], target: ['#sidebarBoxQuestmaster'], text: 'tasks', ok: true, next: 0}
    };
    var step = C.step, els = null, timer = null;

    function save(n) {
        step = n;
        var body = 'step=' + n + '&ajaxToken=' + encodeURIComponent(window.ajaxToken || '');
        try {
            fetch('ajax.php?cmd=legendsTutorial', {method: 'POST', credentials: 'same-origin', keepalive: true,
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: body});
        } catch (e) {}
    }
    function find(list) {
        for (var i = 0; i < list.length; i++) {
            var el = document.querySelector(list[i]);
            if (el && el.getBoundingClientRect().width > 0) return el;
        }
        return null;
    }
    function onPage(s) {
        return s.pages.some(function (p) { return p === page || (p === 'buildRes' && resourceBuild); });
    }
    function clear() {
        if (els) els.root.remove();
        els = null;
        clearInterval(timer);
        document.body.classList.remove('legendsTutorialActive');
    }
    function go(n) {
        clear();
        save(n);
        if (n) show();
    }
    function show() {
        var s = STEPS[step];
        if (!s || !onPage(s)) return;
        var target = find(s.target);
        if (!target) return;
        var root = document.createElement('div');
        root.className = 'legendsTutorial';
        var parts = ['top', 'bottom', 'left', 'right'].map(function (k) {
            var d = document.createElement('div');
            d.className = 'legendsTutorialShade ' + k;
            root.appendChild(d);
            return d;
        });
        var lid = document.createElement('div'); // over the lit element when it is not to be clicked
        lid.className = 'legendsTutorialLid';
        if (!s.click) root.appendChild(lid);
        var ring = document.createElement('div');
        ring.className = 'legendsTutorialRing';
        root.appendChild(ring);
        var bubble = document.createElement('div');
        bubble.className = 'legendsTutorialBubble';
        var nav = '';
        if (s.prev) nav += '<button type="button" class="prev" aria-label="&lt;"><svg viewBox="0 0 8 14"><path d="M7 1 1 7l6 6"/></svg></button>';
        if (s.next && !s.click && !s.ok) nav += '<button type="button" class="next" aria-label="&gt;"><svg viewBox="0 0 8 14"><path d="M1 1l6 6-6 6"/></svg></button>';
        if (s.ok) nav += '<button type="button" class="ok">' + (T.ok || 'OK') + '</button>';
        bubble.innerHTML = '<div class="text">' + (T[s.text] || '') + '</div>'
            + (nav ? '<div class="nav">' + nav + '</div>' : '')
            + '<a href="#" class="skip">' + (T.skip || 'Skip') + '</a><span class="arrow"></span>';
        root.appendChild(bubble);
        document.body.appendChild(root);
        document.body.classList.add('legendsTutorialActive');
        els = {root: root, parts: parts, lid: lid, ring: ring, bubble: bubble, target: target, step: s};
        bubble.addEventListener('click', function (ev) {
            var b = ev.target.closest('button, a.skip');
            if (!b) return;
            ev.preventDefault();
            if (b.classList.contains('skip')) go(0);
            else if (b.classList.contains('prev')) go(s.prev);
            else go(s.next);
        });
        place();
        timer = setInterval(place, 250); // follows the page (layout changes, the iOS app zooming)
    }
    function place() {
        if (!els) return;
        if (!document.body.contains(els.target)) { clear(); show(); return; }
        var r = els.target.getBoundingClientRect(), pad = 4;
        var x0 = Math.max(0, r.left - pad), y0 = Math.max(0, r.top - pad), x1 = r.right + pad, y1 = r.bottom + pad;
        var vw = window.innerWidth, vh = window.innerHeight;
        var set = function (el, l, t, w, h) { el.style.left = l + 'px'; el.style.top = t + 'px'; el.style.width = Math.max(0, w) + 'px'; el.style.height = Math.max(0, h) + 'px'; };
        set(els.parts[0], 0, 0, vw, y0);
        set(els.parts[1], 0, y1, vw, vh - y1);
        set(els.parts[2], 0, y0, x0, y1 - y0);
        set(els.parts[3], x1, y0, vw - x1, y1 - y0);
        set(els.lid, x0, y0, x1 - x0, y1 - y0);
        set(els.ring, x0, y0, x1 - x0, y1 - y0);
        var b = els.bubble, bw = b.offsetWidth, bh = b.offsetHeight, gap = 14;
        var cx = (r.left + r.right) / 2;
        var above = r.top - bh - gap > 8 || r.bottom + bh + gap > vh;
        var top = above ? r.top - bh - gap : r.bottom + gap;
        if (r.height > vh * .5) top = Math.min(Math.max(8, r.top + 40), vh - bh - 8); // a big element: the bubble inside it
        var left = Math.min(Math.max(8, cx - bw / 2), vw - bw - 8);
        b.style.left = left + 'px';
        b.style.top = Math.max(8, top) + 'px';
        b.classList.toggle('below', !above);
        b.querySelector('.arrow').style.left = Math.min(Math.max(14, cx - left), bw - 14) + 'px';
    }
    // a click on the lit element of a click step continues the guide on the next page
    document.addEventListener('click', function (ev) {
        if (!els || !els.step.click) return;
        var s = els.step, area = s.clickTarget ? document.querySelector(s.clickTarget) : els.target;
        if (area && area.contains(ev.target)) { save(s.next); clear(); }
    }, true);
    window.addEventListener('resize', place);
    window.addEventListener('scroll', place, {passive: true});
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { setTimeout(show, 300); });
    else setTimeout(show, 300);
})();
