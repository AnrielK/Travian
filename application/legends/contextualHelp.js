/* Taken over from the main branch (resources/Templates/layout/contextualHelp.php): Legends contextual help tour. */
(function (steps, closeLabel) {
    'use strict';
    var button = document.getElementById('contextualHelpButton');
    if (!button || !steps.length) return;
    var root = null, bubble = null, index = 0;
    var arrowSvg = '<svg class="pointingArrow" viewBox="0 0 40 40"><path d="M40 1L2 20l38 19z"/></svg>';
    var chevron = function (cls, d) {
        return '<svg class="' + cls + '" viewBox="0 0 12 17" tabindex="0"><path d="' + d + '"/></svg>';
    };
    function visibleTarget(selector) {
        var list = document.querySelectorAll(selector);
        for (var i = 0; i < list.length; i++) {
            var r = list[i].getBoundingClientRect();
            if (r.width > 0 && r.height > 0) return list[i];
        }
        return null;
    }
    function close() {
        if (!root) return;
        root.parentNode.removeChild(root);
        root = bubble = null;
        button.classList.remove('active');
        document.removeEventListener('keydown', onKey, true);
        document.removeEventListener('mousedown', onOutside, true);
        window.removeEventListener('resize', place);
    }
    function onKey(e) {
        if (e.key === 'Escape') { close(); e.preventDefault(); }
        else if (e.key === 'ArrowRight') { go(index + 1); e.preventDefault(); }
        else if (e.key === 'ArrowLeft') { go(index - 1); e.preventDefault(); }
    }
    function onOutside(e) {
        if (bubble && !bubble.contains(e.target) && e.target !== button && !button.contains(e.target)) close();
    }
    function go(i) {
        if (i < 0 || i >= steps.length) return;
        index = i;
        render();
    }
    function render() {
        var step = steps[index];
        var progress = '';
        for (var i = 0; i < steps.length; i++) progress += '<div' + (i <= index ? ' class="active"' : '') + '></div>';
        var last = index === steps.length - 1;
        bubble.className = 'contextualHelp ' + step.placement;
        bubble.innerHTML = '<div class="arrowContainer">' + arrowSvg + '</div>'
            + '<div class="helpBody"><div class="progress">' + progress + '</div>'
            + (step.title ? '<div class="helpTitle"></div>' : '') + '<div class="helpText"></div>'
            + '<nav>' + chevron('chevronBack' + (index === 0 ? ' disabled' : ''), 'M10 2L3 8.5l7 6.5')
            + (last ? '<button type="button" class="textButtonV1 closeHelp"></button>'
                    : chevron('chevronForward', 'M2 2l7 6.5L2 15'))
            + '</nav></div>';
        if (step.title) bubble.querySelector('.helpTitle').textContent = step.title;
        bubble.querySelector('.helpText').textContent = step.text;
        var back = bubble.querySelector('.chevronBack'), forward = bubble.querySelector('.chevronForward'),
            closeButton = bubble.querySelector('.closeHelp');
        back.addEventListener('click', function () { go(index - 1); });
        if (forward) forward.addEventListener('click', function () { go(index + 1); });
        if (closeButton) {
            closeButton.textContent = closeLabel;
            closeButton.addEventListener('click', close);
        }
        var target = visibleTarget(step.selector);
        if (target && target.scrollIntoView) {
            var r = target.getBoundingClientRect();
            if (r.top < 0 || r.bottom > window.innerHeight) target.scrollIntoView({block: 'center', behavior: 'smooth'});
        }
        place();
    }
    function place() {
        if (!bubble) return;
        var step = steps[index], target = visibleTarget(step.selector);
        var doc = document.documentElement, sx = window.pageXOffset, sy = window.pageYOffset;
        var t = target ? target.getBoundingClientRect() : {left: doc.clientWidth / 2, right: doc.clientWidth / 2, top: 120, bottom: 120, width: 0, height: 0};
        var placement = step.placement, w, h, left, top;
        var fits = function (p) {
            bubble.className = 'contextualHelp ' + p;
            w = bubble.offsetWidth; h = bubble.offsetHeight;
            if (p === 'after') return t.right + w <= doc.clientWidth;
            if (p === 'before') return t.left - w >= 0;
            if (p === 'above') return t.top + sy - h >= 0;
            return true;
        };
        if (!fits(placement)) placement = fits('below') ? 'below' : 'above';
        fits(placement);
        if (placement === 'after' || placement === 'before') {
            left = placement === 'after' ? t.right : t.left - w;
            top = t.top + t.height / 2 - h / 2;
        } else {
            left = t.left + t.width / 2 - w / 2;
            top = placement === 'below' ? t.bottom : t.top - h;
        }
        left = Math.max(4, Math.min(left, doc.clientWidth - w - 4));
        top = Math.max(4, top);
        bubble.style.left = (left + sx) + 'px';
        bubble.style.top = (top + sy) + 'px';
        var arrow = bubble.querySelector('svg.pointingArrow');
        if (placement === 'after' || placement === 'before') {
            arrow.style.left = '';
            arrow.style.top = Math.max(0, Math.min(h - 54, t.top + t.height / 2 - top - 27)) + 'px';
        } else {
            arrow.style.top = '';
            arrow.style.left = Math.max(0, Math.min(w - 54, t.left + t.width / 2 - left - 27)) + 'px';
        }
    }
    function open() {
        root = document.createElement('div');
        root.id = 'contextualHelp';
        bubble = document.createElement('div');
        root.appendChild(bubble);
        document.body.insertBefore(root, document.body.firstChild);
        button.classList.add('active');
        document.addEventListener('keydown', onKey, true);
        document.addEventListener('mousedown', onOutside, true);
        window.addEventListener('resize', place);
        go(0);
    }
    button.addEventListener('click', function (e) {
        e.preventDefault();
        if (root) close(); else open();
    });
})