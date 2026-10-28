/*
 * "Your tasks" (tasks.php, TasksCtrl) in the Travian Legends 655 look:
 *  - a click on a task opens its details;
 *  - "Collect" collects the reward without leaving the page, with the Legends animation: the button turns into the
 *    spinning checkmark and then into the greyed "Collected" (CSS: #tasks .task .progress button.collected), the
 *    resource reward splits into lumber / clay / iron / crop which burst out of the row and fly to the hero (they go
 *    to the hero's inventory), like the experience; then the list shows the next step of the task.
 */
(function () {
    var root = document.getElementById('tasks');
    if (!root) return;
    var RES = ['resource_lumber', 'resource_clay', 'resource_iron', 'resource_crop'];
    var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function pageRect(el) {
        var r = el.getBoundingClientRect();
        return {x: r.left + window.scrollX, y: r.top + window.scrollY, w: r.width, h: r.height};
    }
    function entity(cls, amount) {
        var d = document.createElement('div');
        d.className = 'entity iconValueBox ' + cls;
        d.innerHTML = '<i class="' + cls + '"></i><span>' + amount + '</span>';
        return d;
    }
    function heroTarget() {
        return document.getElementById('heroImageButton') || document.querySelector('#topBarHero, .heroImageButton');
    }
    function pulseHero() {
        var h = heroTarget();
        if (h && h.animate) h.animate([{filter: 'brightness(1.5)'}, {filter: 'none'}], {duration: 400, easing: 'ease-out'});
    }

    /* reward boxes burst out of the row and fly to their target */
    function fly(row, reward) {
        var all = row.querySelector('.iconValueBox.allResources'), xp = row.querySelector('.iconValueBox.xp');
        if (!all || reduced || !document.body.animate) return;
        all.classList.add('animated');
        var layer = document.createElement('div');
        layer.className = 'animationObject animated legendsRewardFlight';
        layer.style.left = '0px';
        layer.style.top = '0px';
        document.body.appendChild(layer);
        var start = pageRect(all), xpStart = xp ? pageRect(xp) : start;
        // the resources go to the hero's inventory (like Legends), so everything flies to the hero
        var items = RES.map(function (cls) { return {el: entity(cls, reward.resources), from: start, to: heroTarget(), done: pulseHero}; });
        if (reward.experience > 0) items.push({el: entity('xp', reward.experience), from: xpStart, to: heroTarget(), done: pulseHero});
        // burst: the boxes spread around the reward before they fly away (like the four resources in Legends)
        var spread = [[-62, -38], [16, -46], [-58, 30], [22, 34], [10, -40]];
        items.forEach(function (it, i) {
            var el = it.el;
            layer.appendChild(el);
            el.style.left = it.from.x + 'px';
            el.style.top = it.from.y + 'px';
            var dx = spread[i][0], dy = spread[i][1];
            var target = it.to ? pageRect(it.to) : {x: it.from.x, y: it.from.y - 200, w: 0, h: 0};
            var tx = target.x + target.w / 2 - it.from.x - el.offsetWidth / 2, ty = target.y + target.h / 2 - it.from.y - el.offsetHeight / 2;
            var delay = 180 + i * 70;
            var anim = el.animate([
                {transform: 'translate(0, 0) scale(.5)', opacity: 0, offset: 0},
                {transform: 'translate(' + dx + 'px,' + dy + 'px) scale(1.05)', opacity: 1, offset: .22},
                {transform: 'translate(' + dx * 1.08 + 'px,' + dy * 1.08 + 'px) scale(1)', opacity: 1, offset: .45},
                {transform: 'translate(' + tx + 'px,' + ty + 'px) scale(.7)', opacity: 1, offset: .95},
                {transform: 'translate(' + tx + 'px,' + ty + 'px) scale(.4)', opacity: 0, offset: 1}
            ], {duration: 1500, delay: delay, easing: 'cubic-bezier(.45,.05,.55,.95)', fill: 'both'});
            var landed = false;
            setTimeout(function () { landed = true; it.done(); }, delay + 1420);
            anim.onfinish = function () { if (!landed) it.done(); if (el.parentNode) el.parentNode.removeChild(el); };
        });
        setTimeout(function () { if (layer.parentNode) layer.parentNode.removeChild(layer); }, 3200);
    }

    /* after the animation the list (or the details) shows the task's next step */
    function refresh() {
        return fetch(location.href, {credentials: 'same-origin'}).then(function (r) { return r.text(); }).then(function (html) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var fresh = doc.getElementById('tasks');
            if (!fresh) { location.reload(); return; }
            root.innerHTML = fresh.innerHTML;
            root.setAttribute('data-checker', fresh.getAttribute('data-checker') || '');
            // the mentor's exclamation bubble follows the tasks that still wait to be collected
            var bubble = document.querySelector('#sidebarBoxQuestmaster .speechBubbleContainer, #questmasterButton .speechBubbleContainer');
            var freshBubble = doc.querySelector('#sidebarBoxQuestmaster .speechBubbleContainer, #questmasterButton .speechBubbleContainer');
            if (bubble && !freshBubble) bubble.style.display = 'none';
        }).catch(function () { location.reload(); });
    }

    function collect(button) {
        var url = button.getAttribute('data-collect');
        var row = button.closest('.task');
        if (!url || button.disabled) return;
        button.disabled = true;
        root.classList.add('collecting');
        fetch(url + '&ajax=1', {credentials: 'same-origin', headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(function (r) { return r.json(); })
            .then(function (j) {
                if (!j || !j.ok || !j.reward) { location.reload(); return; }
                // other "Vyzvednout" buttons carry the old checker – give them the new one
                Array.prototype.forEach.call(root.querySelectorAll('button[data-collect]'), function (b) {
                    b.setAttribute('data-collect', b.getAttribute('data-collect').replace(/([?&]c=)[^&]*/, '$1' + encodeURIComponent(j.checker)));
                });
                button.classList.add('collected');
                row.classList.add('collecting');
                fly(row, j.reward);
                Array.prototype.forEach.call(row.querySelectorAll('.rewards .iconValueBox'), function (b) {
                    setTimeout(function () { b.classList.add('disabled'); }, 450);
                });
                setTimeout(function () {
                    refresh().then(function () { root.classList.remove('collecting'); });
                }, 2500);
            })
            .catch(function () { location.reload(); });
    }

    root.addEventListener('click', function (ev) {
        var button = ev.target.closest('button[data-collect]');
        if (button) {
            ev.preventDefault();
            ev.stopPropagation();
            collect(button);
            return;
        }
        if (ev.target.closest('button, a')) return;
        var task = ev.target.closest('.task.group[data-href]');
        if (task && !root.classList.contains('collecting')) location.href = task.getAttribute('data-href');
    });
})();
