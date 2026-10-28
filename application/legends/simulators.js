/*
 * Simulátory of the rally point in the Travian Legends look (application/views/Simulator/legends.php):
 *  - combat simulator: attacker ("Ty" by default), defender and any number of reinforcements, tribe portraits, troops,
 *    smithy levels, hero, wall / residence and population; "Simulovat" computes the battle with the Travian formulas
 *    (infantry / cavalry split, smithy upgrades 1.007^level, wall bonus per tribe, morale, loss exponent from the
 *    number of troops, raid vs. normal attack) and shows the result tables under the form.
 *  - travel simulator: duration and distance between two coordinates with the world's troop speed, tournament square
 *    and the speed effects.
 * Data and texts: window.LegendsSimData.
 */
(function () {
    var D = window.LegendsSimData;
    if (!D) return;
    var T = D.texts;
    var root = document.getElementById('rallyPointSimulators');
    if (!root) return;
    var CAVALRY = [4, 5, 6, 15, 16, 23, 24, 25, 26, 45, 46, 54, 55, 56, 63, 64, 65, 66];
    var WALL = {1: 1.030, 2: 1.020, 3: 1.025, 4: 1.0, 5: 1.030, 6: 1.025, 7: 1.015};
    var ATTACK_TRIBES = [1, 2, 3, 6, 7];
    var DEFENCE_TRIBES = [1, 2, 3, 6, 7, 4, 5];
    var CLEAR_ICON = '<svg viewBox="0 0 20 20"><path d="M12.3 1.6l6.1 6.1-9.8 9.8H3.4L1 15.1a1.6 1.6 0 010-2.3L12.3 1.6zM5.2 9.9l-2.3 2.3 3.3 3.3h1.6l1.1-1.1-3.7-4.5zM10 18h9v2h-9z"></path></svg>';
    var RELOAD_ICON = '<svg viewBox="0 0 20 20"><path d="M10 3a7 7 0 016.6 4.6L19 6v6h-6l2.5-2.5A5 5 0 105 13H3a7 7 0 017-10z"></path></svg>';
    var DELETE_ICON = '<svg viewBox="0 0 20 20"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"></path></svg>';
    var PLUS_ICON = '<svg viewBox="0 0 36 36"><circle cx="18" cy="18" r="17"></circle><line x1="18" y1="9" x2="18" y2="27"></line><line x1="9" y1="18" x2="27" y2="18"></line></svg>';

    function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'}[c]; }); }
    function num(v) { v = parseInt(String(v).replace(/[^0-9-]/g, ''), 10); return isNaN(v) || v < 0 ? 0 : v; }
    function fmt(n) { return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, ' '); }
    function unitId(tribe, i) { return (tribe - 1) * 10 + i; }
    function button(content, cls, title) {
        return '<button type="button" class="iconButtonV2 ' + cls + '"' + (title ? ' title="' + esc(title) + '"' : '') + '>' + content + '</button>';
    }
    function village(id) {
        for (var i = 0; i < D.villages.length; i++) if (String(D.villages[i].id) === String(id)) return D.villages[i];
        return D.villages[0];
    }

    /* ------------------------------------------------------------------ sub tabs + favourite */
    var tabs = root.querySelectorAll('[data-sim-tab]');
    function show(name) {
        Array.prototype.forEach.call(tabs, function (a) { a.classList.toggle('active', a.getAttribute('data-sim-tab') === name); });
        Array.prototype.forEach.call(root.querySelectorAll('[data-sim-pane]'), function (p) { p.hidden = p.getAttribute('data-sim-pane') !== name; });
        var fav = null;
        try { fav = localStorage.getItem('legendsSimFavor'); } catch (e) {}
        Array.prototype.forEach.call(tabs, function (a) {
            var old = a.querySelector('svg.star');
            if (old) old.parentNode.removeChild(old);
            if (a.getAttribute('data-sim-tab') === (fav || 'combat')) a.insertAdjacentHTML('beforeend', root.querySelector('[data-sim-favor] svg').outerHTML);
        });
        updateSticky();
    }
    Array.prototype.forEach.call(tabs, function (a) {
        a.addEventListener('click', function (ev) { ev.preventDefault(); show(a.getAttribute('data-sim-tab')); history.replaceState(null, '', '#' + a.getAttribute('data-sim-tab')); });
    });
    root.querySelector('[data-sim-favor]').addEventListener('click', function (ev) {
        ev.preventDefault();
        var active = root.querySelector('[data-sim-tab].active');
        try { localStorage.setItem('legendsSimFavor', active.getAttribute('data-sim-tab')); } catch (e) {}
        show(active.getAttribute('data-sim-tab'));
    });

    /* ------------------------------------------------------------------ combat simulator */
    var cs = document.getElementById('combatSimulator');
    var rolesEl = cs.querySelector('.roles');
    var youDefend = false;
    var reinforcements = [];   // tribes

    function tribeSelection(kind, tribes, active) {
        var html = '<div class="tribeSelection ' + kind + '"><div class="tribeWrapper">';
        tribes.forEach(function (tr) {
            var big = kind !== 'reinforcement';
            html += '<div class="' + (tr === active ? 'active' : 'inactive') + '" data-tribe="' + tr + '" title="' + esc(D.names[unitId(tr, 1)] ? '' : '') + '">'
                + '<i class="' + (big ? 'bigTribe' : 'smallTribe') + tr + (big && tr !== active ? ' inactive' : '') + '"></i>' + (big ? '' : PLUS_ICON) + '</div>';
        });
        return html + '</div></div>';
    }
    function troopsTable(role, tribe, side) {
        var icons = '<tr class="troopIcons"><th></th>', counts = '<tr class="count"><th><i class="troopsIcon"></i></th>', ups = '<tr class="upgrades"><th><i class="smithyIcon" title="' + esc(T.upgrades) + '"></i></th>';
        for (var i = 1; i <= 10; i++) {
            var id = unitId(tribe, i);
            icons += '<td><i class="unit u' + id + '" title="' + esc(D.names[id] || '') + '" data-unit="' + i + '"></i></td>';
            counts += '<td><input type="text" class="text unit' + i + '" name="' + role + 'U' + i + '" value="" inputmode="numeric"></td>';
            ups += i <= 8 && tribe !== 4 && tribe !== 5
                ? '<td><input type="text" class="text unit' + i + '" name="' + role + 'A' + i + '" value="" maxlength="2" inputmode="numeric"></td>' : '<td></td>';
        }
        var hero = tribe !== 4 && tribe !== 5;
        icons += '<td class="hero' + (hero ? '' : ' hidden') + '"><i class="unit uhero" title="' + esc(T.hero) + '"></i></td></tr>';
        counts += '<td class="hero' + (hero ? '' : ' hidden') + '"><input type="checkbox" name="' + role + 'Hero"></td></tr>';
        ups += '<td></td></tr>';
        return '<div class="troops"><table>' + icons + counts + ups + '</table></div>';
    }
    function heroRow(role, side) {
        return '<div class="heroAttributes"><div class="stats">'
            + '<label title="' + esc(T.heroStrength) + '"><i class="hero_strength"></i><input type="text" name="' + role + 'HeroStrength" value="0"></label>'
            + '<label title="' + esc(side === 'attacker' ? T.heroOff : T.heroDef) + '"><i class="' + (side === 'attacker' ? 'hero_offBonus' : 'hero_defBonus') + '"></i><input type="text" name="' + role + 'HeroBonus" value="0"> %</label>'
            + '</div></div>';
    }
    function villageSelect(role) {
        return '<div class="villageSelection"><select name="' + role + 'Village">'
            + D.villages.map(function (v) { return '<option value="' + v.id + '">' + esc(v.name) + ' (' + v.x + '|' + v.y + ')</option>'; }).join('')
            + '</select>' + button(RELOAD_ICON, 'reload', T.reload) + '</div>';
    }
    function roleHtml(side, tribe, you) {
        var role = side === 'attacker' ? 'att' : 'def';
        var icon = side === 'attacker' ? D.svg.svgAttack : D.svg.svgDefend;
        icon = icon.replace('<svg ', '<svg class="roleIcon ' + (side === 'attacker' ? 'svgAttack' : 'svgDefend') + '" ').replace(/class="svg(Attack|Defend)"\s*/, '');
        var html = '<div class="role ' + side + '" data-role="' + role + '" data-tribe="' + tribe + '">'
            + '<div class="header' + (you ? ' you' : '') + '">' + icon
            + '<h2>' + esc(you ? T.you : (side === 'attacker' ? T.attacker : T.defender)) + '</h2>'
            + (you ? villageSelect(role) : '')
            + '<div class="rolePopulation" title="' + esc(T.population) + '"><i class="population_medium"></i><input type="text" name="' + role + 'Pop" value=""></div>'
            + button(CLEAR_ICON, 'clear', T.clear) + '</div>'
            + tribeSelection(side === 'attacker' ? 'attack' : 'defence', side === 'attacker' ? ATTACK_TRIBES : (you ? ATTACK_TRIBES : DEFENCE_TRIBES), tribe)
            + '<div class="content">';
        if (side === 'attacker') {
            html += '<div class="movement"><label><input type="radio" name="attType" value="normal"> ' + esc(T.attack) + '</label>'
                + '<label><input type="radio" name="attType" value="raid" checked> ' + esc(T.raid) + '</label></div>'
                + '<div class="bonus"><div class="buildings"></div><div class="artefacts">'
                + '<label title="' + esc(T.allyBonus) + '"><i class="allyBonus"></i><select name="attAlly">' + [0, 1, 2, 3, 4, 5].map(function (p) { return '<option value="' + p + '">' + p + '%</option>'; }).join('') + '</select></label>'
                + '</div></div>';
        } else {
            html += '<div class="bonus"><div class="buildings">'
                + '<label title="' + esc(T.wall) + '"><i class="simWallIcon"></i><input type="text" name="defWall" value="0" maxlength="2"></label>'
                + '<label title="' + esc(T.residence) + '"><i class="simResidenceIcon"></i><input type="text" name="defResidence" value="0" maxlength="2"></label>'
                + '</div><div class="artefacts">'
                + '<label title="' + esc(T.allyBonus) + '"><i class="allyBonus"></i><select name="defAlly">' + [0, 1, 2, 3, 4, 5].map(function (p) { return '<option value="' + p + '">' + p + '%</option>'; }).join('') + '</select></label>'
                + '</div></div>';
        }
        html += troopsTable(role, tribe, side) + heroRow(role, side) + '</div></div>';
        return html;
    }
    function reinforcementHtml(index, tribe) {
        var role = 'rein' + index;
        return '<div class="role reinforcement" data-role="' + role + '" data-tribe="' + tribe + '" data-index="' + index + '">'
            + '<div class="header"><i class="smallTribe' + tribe + '"></i><h3>' + esc(T.reinforcement) + '</h3>'
            + button(DELETE_ICON, 'delete', T['delete']) + button(CLEAR_ICON, 'clear', T.clear) + '</div>'
            + '<div class="content">' + troopsTable(role, tribe, 'defender') + '</div></div>';
    }
    function render() {
        var keep = collect();
        var attTribe = youDefend ? (keep.attTribe || 1) : D.tribe;
        var defTribe = youDefend ? D.tribe : (keep.defTribe || D.tribe);
        rolesEl.innerHTML = roleHtml('attacker', attTribe, !youDefend)
            + roleHtml('defender', defTribe, youDefend)
            + reinforcements.map(function (tr, i) { return reinforcementHtml(i, tr); }).join('')
            + tribeSelection('reinforcement', DEFENCE_TRIBES.filter(function (t) { return t !== 4 && t !== 5; }), 0).replace('tribeSelection reinforcement', 'tribeSelection reinforcement roleReinforcement" title="' + esc(T.addReinforcement));
        restore(keep);
        if (!keep.loaded) loadVillage(youDefend ? 'def' : 'att', D.villages[0] && D.villages[0].id);
    }
    function collect() {
        var out = {values: {}, loaded: !!rolesEl.querySelector('input')};
        Array.prototype.forEach.call(rolesEl.querySelectorAll('input, select'), function (el) {
            out.values[el.name] = el.type === 'checkbox' || el.type === 'radio' ? (el.checked ? el.value || 'on' : null) : el.value;
        });
        var a = rolesEl.querySelector('.role.attacker'), d = rolesEl.querySelector('.role.defender');
        out.attTribe = a ? +a.getAttribute('data-tribe') : 0;
        out.defTribe = d ? +d.getAttribute('data-tribe') : 0;
        return out;
    }
    function restore(keep) {
        Array.prototype.forEach.call(rolesEl.querySelectorAll('input, select'), function (el) {
            if (!(el.name in keep.values)) return;
            var v = keep.values[el.name];
            if (el.type === 'radio') { if (v !== null) el.checked = el.value === v; }
            else if (el.type === 'checkbox') el.checked = v !== null;
            else el.value = v;
        });
    }
    function field(name) { return rolesEl.querySelector('[name="' + name + '"]'); }
    function setVal(name, v) { var el = field(name); if (el) el.value = v; }
    function loadVillage(role, id) {
        var v = village(id);
        if (!v) return;
        var sel = field(role + 'Village');
        if (sel) sel.value = v.id;
        for (var i = 1; i <= 10; i++) {
            setVal(role + 'U' + i, v.units[i - 1] || '');
            if (i <= 8) setVal(role + 'A' + i, (role === 'att' ? v.att[i - 1] : v.def[i - 1]) || '');
        }
        var hero = field(role + 'Hero');
        if (hero) hero.checked = (v.units[10] || 0) > 0;
        setVal(role + 'Pop', v.pop || '');
        if (role === 'def') { setVal('defWall', v.wall || 0); setVal('defResidence', v.residence || 0); }
    }
    function clearRole(roleEl) {
        Array.prototype.forEach.call(roleEl.querySelectorAll('input[type=text]'), function (el) { el.value = el.name.indexOf('Hero') !== -1 || /Wall|Residence/.test(el.name) ? '0' : ''; });
        Array.prototype.forEach.call(roleEl.querySelectorAll('input[type=checkbox]'), function (el) { el.checked = false; });
    }

    rolesEl.addEventListener('click', function (ev) {
        var t = ev.target, roleEl = t.closest('.role'), btn = t.closest('button'), tribeEl = t.closest('.tribeWrapper > div');
        if (tribeEl) {
            var tribe = +tribeEl.getAttribute('data-tribe');
            var sel = tribeEl.closest('.tribeSelection');
            if (sel.classList.contains('reinforcement')) {
                reinforcements.push(tribe);
                render();
                return;
            }
            var r = sel.parentNode;   // .role
            if (+r.getAttribute('data-tribe') === tribe || r.querySelector('.header.you')) return;   // "Ty" keeps the own tribe
            r.setAttribute('data-tribe', tribe);
            var keep = collect();
            var side = r.classList.contains('attacker') ? 'attacker' : 'defender';
            var tmp = document.createElement('div');
            tmp.innerHTML = roleHtml(side, tribe, false);
            r.parentNode.replaceChild(tmp.firstChild, r);
            restore(keep);
            return;
        }
        if (btn && roleEl) {
            if (btn.classList.contains('clear')) clearRole(roleEl);
            else if (btn.classList.contains('reload')) loadVillage(roleEl.getAttribute('data-role'), field(roleEl.getAttribute('data-role') + 'Village').value);
            else if (btn.classList.contains('delete')) { reinforcements.splice(+roleEl.getAttribute('data-index'), 1); render(); }
            return;
        }
        if (t.matches('.header.you ~ .content .troopIcons i.unit')) {
            var role = roleEl.getAttribute('data-role');
            var v = village(field(role + 'Village').value), i = +t.getAttribute('data-unit');
            var input = field(role + 'U' + i);
            input.value = num(input.value) >= (v.units[i - 1] || 0) ? '' : (v.units[i - 1] || '');
        }
    });
    rolesEl.addEventListener('change', function (ev) {
        if (/Village$/.test(ev.target.name)) loadVillage(ev.target.name.replace('Village', ''), ev.target.value);
    });
    cs.querySelector('[data-sim-switch]').addEventListener('click', function () {
        youDefend = !youDefend;
        var b = this;
        b.classList.toggle('defender', !youDefend);
        b.classList.toggle('attacker', youDefend);
        b.textContent = youDefend ? T.playAttacker : T.playDefender;
        rolesEl.innerHTML = '';
        render();
        cs.querySelector('.results').hidden = true;
    });

    /* battle */
    function upgraded(value, pop, level) {
        return level > 0 ? value + (value + 300 * pop / 7) * (Math.pow(1.007, level) - 1) : value;
    }
    function groupOf(roleEl) {
        var role = roleEl.getAttribute('data-role'), tribe = +roleEl.getAttribute('data-tribe');
        var g = {role: role, tribe: tribe, units: [], hero: false};
        for (var i = 1; i <= 10; i++) {
            var lvlEl = field(role + 'A' + i);
            g.units.push({id: unitId(tribe, i), n: num((field(role + 'U' + i) || {}).value || 0), level: Math.min(20, lvlEl ? num(lvlEl.value) : 0)});
        }
        var h = field(role + 'Hero');
        g.hero = !!(h && h.checked);
        g.heroStrength = num((field(role + 'HeroStrength') || {}).value || 0);
        g.heroBonus = num((field(role + 'HeroBonus') || {}).value || 0);
        return g;
    }
    function simulate() {
        var attEl = rolesEl.querySelector('.role.attacker'), defEl = rolesEl.querySelector('.role.defender');
        var att = groupOf(attEl);
        var defs = [groupOf(defEl)].concat(Array.prototype.map.call(rolesEl.querySelectorAll('.role.reinforcement'), groupOf));
        var raid = (rolesEl.querySelector('[name=attType]:checked') || {}).value !== 'normal';
        var attAlly = 1 + num(field('attAlly').value) / 100, defAlly = 1 + num(field('defAlly').value) / 100;
        var inf = 0, cav = 0, attCount = 0;
        att.units.forEach(function (u) {
            var d = D.units[u.id];
            if (!d || !u.n) return;
            var a = upgraded(d.atk, d.pop, u.level) * u.n;
            if (CAVALRY.indexOf(u.id) !== -1) cav += a; else inf += a;
            attCount += u.n;
        });
        if (att.hero) { inf += att.heroStrength; attCount += 1; }
        var attMul = (1 + (att.hero ? att.heroBonus : 0) / 100) * attAlly;
        inf *= attMul; cav *= attMul;
        var A = inf + cav;
        var di = 0, dc = 0, defCount = 0;
        defs.forEach(function (g) {
            g.units.forEach(function (u) {
                var d = D.units[u.id];
                if (!d || !u.n) return;
                di += upgraded(d.di, d.pop, u.level) * u.n;
                dc += upgraded(d.dc, d.pop, u.level) * u.n;
                defCount += u.n;
            });
            if (g.hero) { di += g.heroStrength; dc += g.heroStrength; defCount += 1; }
        });
        var main = defs[0];
        var defMul = (1 + (main.hero ? main.heroBonus : 0) / 100) * defAlly;
        var wall = Math.min(20, num(field('defWall').value)), residence = Math.min(20, num(field('defResidence').value));
        var Dv = A > 0 ? (di * inf + dc * cav) / A : di;
        Dv = (Dv * defMul + 10 + 2 * residence * residence) * Math.pow(WALL[main.tribe] || 1, wall);
        // morale: a bigger attacker loses effectiveness against a smaller defender
        var pa = num(field('attPop').value), pd = num(field('defPop').value);
        if (pa > 0 && pd > 0 && pa > pd && main.tribe !== 4 && main.tribe !== 5) A *= Math.max(2 / 3, Math.pow(pd / pa, 0.2));
        var N = attCount + defCount;
        var K = N > 0 ? Math.min(1.5, Math.max(1.2578, 2 * (1.8592 - Math.pow(N, 0.015)))) : 1.5;
        var attWins = A > Dv;
        var x = attWins ? Math.pow(Dv / A, K) : (A > 0 ? Math.pow(A / Dv, K) : 0);
        var attLoss, defLoss;
        if (raid) {
            var winner = x / (1 + x), loser = 1 / (1 + x);
            attLoss = attWins ? winner : loser;
            defLoss = attWins ? loser : winner;
        } else {
            attLoss = attWins ? x : 1;
            defLoss = attWins ? 1 : x;
        }
        if (A <= 0) { attLoss = 0; defLoss = 0; }
        showResults(att, defs, attLoss, defLoss, attWins && A > 0, raid);
    }
    function resultTable(g, loss) {
        var head = '<tr><th></th>', rows = '<tr class="troopsCount"><th>' + esc(T.troops) + '</th>', losses = '<tr class="troopsLoss"><th>' + esc(T.losses) + '</th>', bounty = 0;
        g.units.forEach(function (u) {
            var lost = Math.round(u.n * loss);
            head += '<th><i class="unit u' + u.id + '" title="' + esc(D.names[u.id] || '') + '"></i></th>';
            rows += '<td class="' + (u.n ? '' : 'none') + '">' + fmt(u.n) + '</td>';
            losses += '<td class="' + (lost ? '' : 'none') + '">' + fmt(lost) + '</td>';
            bounty += (u.n - lost) * ((D.units[u.id] || {}).cap || 0);
        });
        if (g.tribe !== 4 && g.tribe !== 5) {
            var heroLost = g.hero && loss >= 0.9999 ? 1 : 0;
            head += '<th class="uhero"><i class="unit uhero"></i></th>';
            rows += '<td class="uhero">' + (g.hero ? 1 : 0) + '</td>';
            losses += '<td class="uhero' + (heroLost ? '' : ' none') + '">' + heroLost + '</td>';
        }
        return {html: '<table>' + head + '</tr>' + rows + '</tr>' + losses + '</tr></table>', bounty: bounty};
    }
    function showResults(att, defs, attLoss, defLoss, attWins, raid) {
        var res = cs.querySelector('.results');
        var a = resultTable(att, attLoss);
        var html = '<div class="separator long"><span class="title">' + esc(T.result) + '</span></div>'
            + '<div class="result attack"><div class="header"><i class="tribeIcon smallTribe' + att.tribe + '"></i>'
            + D.svg.svgAttack.replace('class="svgAttack"', 'class="roleIcon svgAttack"')
            + '<h2>' + esc(T.attacker) + '</h2><div class="outcome"><div class="outcomeIcons">' + (attWins ? D.svg.svgAttack : D.svg.svgAttackLost) + (attLoss >= 0.9999 ? D.svg.svgSkull : '') + '</div></div></div>'
            + '<div class="content">' + a.html
            + (raid && attWins ? '<table><tr class="additionalInfo"><td><i class="carry"></i>' + esc(T.bounty) + ': <span>' + fmt(a.bounty) + '</span></td></tr></table>' : '')
            + '</div></div>';
        defs.forEach(function (g, i) {
            var r = resultTable(g, defLoss);
            if (i === 0) {
                html += '<div class="result defence"><div class="header"><i class="tribeIcon smallTribe' + g.tribe + '"></i>'
                    + D.svg.svgDefend.replace('class="svgDefend"', 'class="roleIcon svgDefend"')
                    + '<h2>' + esc(T.defender) + '</h2><div class="outcome"><div class="outcomeIcons">' + (attWins ? D.svg.svgDefendLost : D.svg.svgDefend) + (defLoss >= 0.9999 ? D.svg.svgSkull : '') + '</div></div></div>'
                    + '<div class="content">' + r.html + '</div></div>';
            } else {
                html += '<div class="result reinforcement"><div class="header"><i class="tribeIcon smallTribe' + g.tribe + '"></i><h2>' + esc(T.reinforcement) + '</h2></div>'
                    + '<div class="content">' + r.html + '</div></div>';
            }
        });
        res.innerHTML = html;
        res.hidden = false;
        res.scrollIntoView({behavior: 'smooth', block: 'start'});
        updateSticky();
    }
    cs.querySelector('button.simulate').addEventListener('click', simulate);

    /* the Simulovat bar sticks to the bottom of the window while the form is longer than the window */
    var pin = document.getElementById('stickyPin'), wrap = document.getElementById('stickyWrapper');
    function updateSticky() {
        if (!pin || !wrap) return;
        if (cs.hidden) { wrap.classList.remove('floating'); return; }
        var r = pin.getBoundingClientRect();
        var floating = r.bottom > window.innerHeight && rolesEl.getBoundingClientRect().top < window.innerHeight - 60;
        wrap.classList.toggle('floating', floating);
        if (floating) wrap.style.left = (pin.getBoundingClientRect().left + 6 - 6) + 'px'; else wrap.style.left = '';
    }
    window.addEventListener('scroll', updateSticky, {passive: true});
    window.addEventListener('resize', updateSticky);

    /* ------------------------------------------------------------------ travel simulator */
    var tr = root.querySelector('.travelSimulator');
    function tf(name) { return tr.querySelector('[name="' + name + '"]'); }
    var speedSel = tf('speed');
    if (speedSel && D.defaultSpeed) speedSel.value = String(D.defaultSpeed);
    if (D.villages[0]) tf('arena').value = String(D.villages[0].ts || 0);
    function coord(name) {
        var v = String(tf(name).value).trim().replace(/[−–]/g, '-');
        if (!/^-?\d+$/.test(v)) return null;
        v = parseInt(v, 10);
        return Math.abs(v) <= D.worldMax ? v : null;
    }
    function distance(x1, y1, x2, y2) {
        var size = 2 * D.worldMax + 1;
        var dx = Math.abs(x1 - x2), dy = Math.abs(y1 - y2);
        if (dx > D.worldMax) dx = size - dx;
        if (dy > D.worldMax) dy = size - dy;
        return Math.sqrt(dx * dx + dy * dy);
    }
    function duration(sec) {
        sec = Math.round(sec);
        var h = Math.floor(sec / 3600), m = Math.floor(sec % 3600 / 60), s = sec % 60;
        return h + ':' + (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }
    function calculate() {
        var sx = coord('sourceX'), sy = coord('sourceY'), tx = coord('targetX'), ty = coord('targetY');
        var sv = tr.querySelector('.sourceCoordinatesValidation'), tv = tr.querySelector('.targetCoordinatesValidation');
        sv.hidden = sx !== null && sy !== null; sv.textContent = T.invalidCoords;
        tv.hidden = tx !== null && ty !== null; tv.textContent = T.invalidCoords;
        if (!sv.hidden || !tv.hidden) return;
        var dist = distance(sx, sy, tx, ty);
        var speed = num(tf('speed').value) || 1;
        var horse = num(tf('horse').value);
        if (horse) speed = Math.min(speed, horse);
        speed *= parseFloat(tf('artefact').value) || 1;
        speed *= 1 + num(tf('skills').value) / 100;
        if (tf('item').value) speed *= 1 + num(tf('itemSize').value) / 100;
        // tournament square (and the hero's spurs) from TS_THRESHOLD fields on – the same rule as the game's movements
        // (Database::procDistanceTime: the bonus applies to the whole journey once it is that long)
        var ts = (D.tsLevels[num(tf('arena').value)] || 100) / 100 - 1;
        if (tf('boots').value) ts += num(tf('bootsSize').value) / 100;
        var hours = dist / (dist >= D.tsThreshold ? speed * (1 + ts) : speed);
        var loading = tr.querySelector('.loadingIndicator');
        loading.hidden = false;
        setTimeout(function () {
            loading.hidden = true;
            tr.querySelector('.duration span').textContent = duration(hours * 3600 / D.troopSpeed);
            tr.querySelector('.distance span').textContent = (Math.round(dist * 10) / 10).toString().replace('.', ',') + ' ' + T.fields;
            tr.querySelector('.duration').classList.remove('outdated');
            tr.querySelector('.distance').classList.remove('outdated');
        }, 250);
    }
    tr.querySelector('button.calculate').addEventListener('click', calculate);
    tr.querySelector('button.switchSourceAndTarget').addEventListener('click', function () {
        ['X', 'Y'].forEach(function (c) { var a = tf('source' + c), b = tf('target' + c), v = a.value; a.value = b.value; b.value = v; });
    });
    tr.addEventListener('input', function () {
        tr.querySelector('.duration').classList.add('outdated');
        tr.querySelector('.distance').classList.add('outdated');
    });
    tr.addEventListener('change', function () {
        tr.querySelector('.duration').classList.add('outdated');
        tr.querySelector('.distance').classList.add('outdated');
    });
    // pinned labels of the form fields (Legends formV2): select labels always sit on the frame
    Array.prototype.forEach.call(tr.querySelectorAll('label.select'), function (l) { l.classList.add('fixedLabel'); });

    /* ------------------------------------------------------------------ start */
    render();
    var fav = null;
    try { fav = localStorage.getItem('legendsSimFavor'); } catch (e) {}
    var start = (location.hash || '').replace('#', '');
    show(start === 'travel' || start === 'combat' ? start : (fav || 'combat'));
})();
