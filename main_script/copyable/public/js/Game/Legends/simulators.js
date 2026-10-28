/*
 * Simulators of the rally point in the Travian Legends 655 look (resources/Templates/rallypoint/legendsSimulators.php):
 *  - combat simulator: attacker ("You" by default), defender and any number of reinforcements, tribe portraits, troops,
 *    smithy levels, hero, wall / residence / stonemason and population; "Simulate" lets the game's own battle
 *    simulator fight (ajax.php?cmd=legendsCombat → Controller\RallyPoint\Simulator) and shows the result tables.
 *  - travel simulator: duration and distance between two coordinates like Game\SpeedCalculator (unit speed × the
 *    world's movement speed, artefact, hero items, tournament square for the part after 20 fields).
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
        var fav = favorite();
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
    // favourite simulator: saved on the server like the favourite tabs (ajax.php?cmd=tabFavorite), browser storage as fallback
    function favorite() {
        return D.favor === 'travel' ? 'travel' : 'combat';
    }
    function post(cmd, data) {
        data.ajaxToken = window.ajaxToken || '';
        var body = Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&');
        return fetch('ajax.php?cmd=' + cmd, {method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: body})
            .then(function (r) { return r.json(); });
    }
    root.querySelector('[data-sim-favor]').addEventListener('click', function (ev) {
        ev.preventDefault();
        var name = root.querySelector('[data-sim-tab].active').getAttribute('data-sim-tab');
        D.favor = name;
        // stored with the player's favourite tabs (Session favorTabs "rallySimulators": 0 combat, 1 travel)
        post('tabFavorite', {name: 'rallySimulators', number: name === 'travel' ? 1 : 0}).catch(function () {});
        show(name);
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
    var TROOPS_ICON = '<svg class="troopsIcon" viewBox="0 0 14 16"><path d="M1 1h12v10l-6 4-6-4z" fill="#b8261f" stroke="#6e130e" stroke-width="1"/><path d="M1 3.5h12" stroke="#f2c14e" stroke-width="1.2"/></svg>';
    var SMITHY_ICON = '<svg class="smithyIcon" viewBox="0 0 18 14"><path d="M1 2h10c0 2 1.6 3.2 6 3.4V7.4c-2.6 0-4 .6-4.6 1.8V10H14v2.5H5V10h1.6v-.8C5.8 7.8 4 6.8 1 6.4z" fill="#8d8d8d" stroke="#555" stroke-width=".8"/></svg>';
    function options(values, suffix) {
        return values.map(function (v) { return '<option value="' + v + '">' + v + suffix + '</option>'; }).join('');
    }
    function troopsTable(role, tribe, side) {
        var icons = '<tr class="troopIcons"><th></th>', counts = '<tr class="count"><th title="' + esc(T.troops) + '">' + TROOPS_ICON + '</th>',
            ups = '<tr class="upgrades"><th title="' + esc(T.upgrades) + '">' + SMITHY_ICON + '</th>';
        var animals = tribe === 4 || tribe === 5;
        for (var i = 1; i <= 10; i++) {
            var id = unitId(tribe, i);
            icons += '<td><i class="unit u' + id + '" title="' + esc(D.names[id] || '') + '" data-unit="' + i + '"></i></td>';
            counts += '<td><input type="text" class="text unit' + i + '" name="' + role + 'U' + i + '" value="0" inputmode="numeric"></td>';
            if (i <= 8) ups += animals ? '<td></td>' : '<td><input type="text" class="text unit' + i + '" name="' + role + 'A' + i + '" value="" maxlength="2" inputmode="numeric"></td>';
        }
        ups += '<td colspan="2"></td>';
        icons += '<td class="hero' + (animals ? ' hidden' : '') + '"><i class="unit uhero" title="' + esc(T.hero) + '"></i></td></tr>';
        counts += '<td class="hero' + (animals ? ' hidden' : '') + '"><input type="checkbox" name="' + role + 'Hero"></td></tr>';
        ups += '<td></td></tr>';
        return '<div class="troops"><table>' + icons + counts + ups + '</table></div>';
    }
    /* hero like the Legends simulator: health, fighting strength points (→ strength), bonus points (→ %), horse, equipment */
    function heroRow(role, side) {
        var off = side === 'attacker';
        return '<div class="heroAttributes hidden" data-hero="' + role + '"><div class="stats">'
            + '<span class="heroTitle">' + esc(T.hero) + '</span>'
            + '<label title="' + esc(T.heroHealth) + '"><i class="hero_health"></i><input type="text" name="' + role + 'HeroHealth" value="100"></label>'
            + '<label title="' + esc(T.heroStrength) + '"><i class="hero_strength"></i><input type="text" name="' + role + 'HeroPower" value="0"></label>'
            + '<span class="calculated heroStrength">0</span>'
            + '<label title="' + esc(off ? T.heroOff : T.heroDef) + '"><i class="' + (off ? 'hero_offBonus' : 'hero_defBonus') + '"></i><input type="text" name="' + role + 'HeroBonus" value="0"></label>'
            + '<span class="calculated heroBonus">0,0%</span>'
            + (off ? '<label title="' + esc(T.heroHorse) + '"><i class="hero_horse"></i><input type="checkbox" name="' + role + 'HeroHorse"></label>' : '')
            + '<input type="hidden" name="' + role + 'HeroItems" value="0">'
            + '</div><div class="equipment"><span class="title">' + esc(T.equipment) + '</span>'
            + '<span class="slot" title="' + esc(T.rightHand) + '"><i class="hero_handR"></i></span><span class="slot" title="' + esc(T.torso) + '"><i class="hero_torso"></i></span>'
            + '</div></div>';
    }
    /* the game's hero strength (Controller\RallyPoint\Simulator::hero_str4): 100 + points × 100 (Romans) or × 80, + weapon */
    function heroStrength(role) {
        var roleEl = rolesEl.querySelector('.role[data-role="' + role + '"]');
        var tribe = roleEl ? +roleEl.getAttribute('data-tribe') : D.tribe;
        return 100 + Math.min(100, num((field(role + 'HeroPower') || {}).value || 0)) * (tribe === 1 ? 100 : 80) + num((field(role + 'HeroItems') || {}).value || 0);
    }
    function updateHero(role) {
        var row = rolesEl.querySelector('.heroAttributes[data-hero="' + role + '"]'), h = field(role + 'Hero');
        if (!row) return;
        row.classList.toggle('hidden', !(h && h.checked));
        row.querySelector('.heroStrength').textContent = heroStrength(role);
        row.querySelector('.heroBonus').textContent = (Math.min(100, num(field(role + 'HeroBonus').value)) * 0.2).toFixed(1).replace('.', ',') + '%';
    }
    function villageSelect(role) {
        return '<div class="villageSelection"><select name="' + role + 'Village">'
            + D.villages.map(function (v) { return '<option value="' + v.id + '">' + esc(v.name) + '</option>'; }).join('')
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
            + button(CLEAR_ICON, 'clear', T.clear) + '</div>'
            + tribeSelection(side === 'attacker' ? 'attack' : 'defence', side === 'attacker' ? ATTACK_TRIBES : (you ? ATTACK_TRIBES : DEFENCE_TRIBES), tribe)
            + '<div class="content">';
        var spy = '<label title="' + esc(T.spyArtefact) + '"><i class="artefact_spy"></i><select name="' + role + 'Spy">' + options([0, 2, 3, 4, 5], '×') + '</select></label>';
        if (side === 'attacker') {
            html += '<div class="movement"><label><input type="radio" name="attType" value="normal"> ' + esc(T.attack) + '</label>'
                + '<label><input type="radio" name="attType" value="raid" checked> ' + esc(T.raid) + '</label></div>'
                + '<div class="bonus"><div class="buildings">'
                + '<label title="' + esc(T.population) + '"><i class="simPopulationIcon"></i><input type="text" name="attPop" value="' + (village(D.villages[0] && D.villages[0].id) || {pop: 1}).pop + '" maxlength="6"></label>'
                + '<label title="' + esc(T.catapultTarget) + '"><i class="simCatapultIcon"></i><input type="text" name="attCatapult" value="0" maxlength="2"></label>'
                + '</div><div class="artefacts"></div></div>';
        } else {
            html += '<div class="bonus"><div class="buildings">'
                + '<label title="' + esc(T.wall) + '"><i class="simWallIcon"></i><input type="text" name="defWall" value="0" maxlength="2"></label>'
                + '<label title="' + esc(T.residence) + '"><i class="simResidenceIcon"></i><input type="text" name="defResidence" value="0" maxlength="2"></label>'
                + '<label title="' + esc(T.stonemason) + '"><i class="simStonemasonIcon"></i><input type="text" name="defStonemason" value="0" maxlength="2"></label>'
                + '<label title="' + esc(T.population) + '"><i class="simPopulationIcon"></i><input type="text" name="defPop" value="1" maxlength="6"></label>'
                + '</div><div class="artefacts">'
                + '<label title="' + esc(T.architectArtefact) + '"><i class="artefact_buildingMaster"></i><select name="defArchitect">' + options([0, 2, 3, 4, 5], '×') + '</select></label>'
                + spy + '</div></div>';
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
        updateHero('att');
        updateHero('def');
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
            setVal(role + 'U' + i, v.units[i - 1] || 0);
            if (i <= 8) setVal(role + 'A' + i, (role === 'att' ? v.att[i - 1] : v.def[i - 1]) || '');
        }
        var hero = field(role + 'Hero');
        if (hero) hero.checked = (v.units[10] || 0) > 0;
        if (D.hero) {
            setVal(role + 'HeroHealth', D.hero.health);
            setVal(role + 'HeroPower', D.hero.power);
            setVal(role + 'HeroItems', D.hero.itempower);
            setVal(role + 'HeroBonus', role === 'att' ? D.hero.offBonus : D.hero.defBonus);
        }
        if (role === 'def') { setVal('defWall', v.wall || 0); setVal('defResidence', v.residence || 0); setVal('defStonemason', v.stonemason || 0); }
        setVal(role + 'Pop', v.pop || 1);
        updateHero(role);
    }
    function clearRole(roleEl) {
        Array.prototype.forEach.call(roleEl.querySelectorAll('input[type=text]'), function (el) {
            el.value = /U\d+$|Wall|Residence|Stonemason|HeroPower|HeroBonus/.test(el.name) ? '0' : (/HeroHealth/.test(el.name) ? '100' : '');
        });
        Array.prototype.forEach.call(roleEl.querySelectorAll('input[type=checkbox]'), function (el) { el.checked = false; });
        updateHero(roleEl.getAttribute('data-role'));
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
            updateHero(side === 'attacker' ? 'att' : 'def');
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
        var m = /^(att|def)Hero/.exec(ev.target.name);
        if (m) updateHero(m[1]);
    });
    rolesEl.addEventListener('input', function (ev) {
        var m = /^(att|def)Hero/.exec(ev.target.name);
        if (m) updateHero(m[1]);
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

    /* battle: fought by the game's simulator on the server */
    function groupOf(roleEl) {
        var role = roleEl.getAttribute('data-role'), tribe = +roleEl.getAttribute('data-tribe');
        var g = {role: role, tribe: tribe, units: [], hero: false};
        for (var i = 1; i <= 10; i++) {
            var lvlEl = field(role + 'A' + i);
            g.units.push({id: unitId(tribe, i), n: num((field(role + 'U' + i) || {}).value || 0), level: Math.min(20, lvlEl ? num(lvlEl.value) : 0)});
        }
        var h = field(role + 'Hero');
        g.hero = !!(h && h.checked) && num((field(role + 'HeroHealth') || {value: 100}).value) > 0 && tribe !== 4 && tribe !== 5;
        var horse = field(role + 'HeroHorse');
        g.heroData = {on: g.hero, power: num((field(role + 'HeroPower') || {}).value || 0), items: num((field(role + 'HeroItems') || {}).value || 0),
            bonus: Math.min(100, num((field(role + 'HeroBonus') || {}).value || 0)), horse: !!(horse && horse.checked)};
        return g;
    }
    var simulating = false;
    function simulate() {
        if (simulating) return;
        var attEl = rolesEl.querySelector('.role.attacker'), defEl = rolesEl.querySelector('.role.defender');
        var att = groupOf(attEl);
        var defs = [groupOf(defEl)].concat(Array.prototype.map.call(rolesEl.querySelectorAll('.role.reinforcement'), groupOf));
        var raid = (rolesEl.querySelector('[name=attType]:checked') || {}).value !== 'normal';
        var pack = function (g) {
            return {tribe: g.tribe, units: g.units.map(function (u) { return u.n; }), levels: g.units.map(function (u) { return u.level; }), hero: g.heroData};
        };
        var battle = {
            raid: raid,
            attacker: Object.assign(pack(att), {pop: num((field('attPop') || {}).value || 1)}),
            defenders: defs.map(pack),
            pop: num((field('defPop') || {}).value || 1),
            wall: Math.min(20, num(field('defWall').value)),
            residence: Math.min(20, num(field('defResidence').value)),
            stonemason: Math.min(20, num((field('defStonemason') || {}).value || 0)),
            scout: num((field('defSpy') || {}).value || 0),
            durability: num((field('defArchitect') || {}).value || 0),
            catapultLevel: Math.min(20, num((field('attCatapult') || {}).value || 0))
        };
        var button = cs.querySelector('button.simulate');
        simulating = true;
        button.disabled = true;
        post('legendsCombat', {battle: JSON.stringify(battle)}).then(function (j) {
            var d = j && j.response && !j.response.error ? j.response.data : null;
            if (!d) return;
            var attLoss = +d.losses[0] || 0, defLoss = +d.losses[1] || 0;
            var dead = d.deadHeroes || {off: 0, def: []};
            var attSum = att.units.reduce(function (a, u) { return a + u.n; }, 0) + (att.hero ? 1 : 0);
            var attWins = attSum > 0 && attLoss < 1 && (raid ? attLoss <= defLoss : defLoss >= 0.9999);
            showResults(att, defs, attLoss, defLoss, attWins, raid, dead);
        }).catch(function () {}).then(function () { simulating = false; button.disabled = false; });
    }
    function resultTable(g, loss, heroDead) {
        var head = '<tr><th></th>', rows = '<tr class="troopsCount"><th>' + esc(T.troops) + '</th>', losses = '<tr class="troopsLoss"><th>' + esc(T.losses) + '</th>', bounty = 0;
        g.units.forEach(function (u) {
            var lost = Math.round(u.n * loss);
            head += '<th><i class="unit u' + u.id + '" title="' + esc(D.names[u.id] || '') + '"></i></th>';
            rows += '<td class="' + (u.n ? '' : 'none') + '">' + fmt(u.n) + '</td>';
            losses += '<td class="' + (lost ? '' : 'none') + '">' + fmt(lost) + '</td>';
            bounty += (u.n - lost) * ((D.units[u.id] || {}).cap || 0);
        });
        if (g.tribe !== 4 && g.tribe !== 5) {
            var heroLost = g.hero && (heroDead === undefined ? loss >= 0.9999 : !!heroDead) ? 1 : 0;
            head += '<th class="uhero"><i class="unit uhero"></i></th>';
            rows += '<td class="uhero">' + (g.hero ? 1 : 0) + '</td>';
            losses += '<td class="uhero' + (heroLost ? '' : ' none') + '">' + heroLost + '</td>';
        }
        return {html: '<table>' + head + '</tr>' + rows + '</tr>' + losses + '</tr></table>', bounty: bounty};
    }
    function showResults(att, defs, attLoss, defLoss, attWins, raid, dead) {
        var res = cs.querySelector('.results');
        dead = dead || {off: 0, def: []};
        var a = resultTable(att, attLoss, dead.off);
        var html = '<div class="separator long"><span class="title">' + esc(T.result) + '</span></div>'
            + '<div class="result attack"><div class="header"><i class="tribeIcon smallTribe' + att.tribe + '"></i>'
            + D.svg.svgAttack.replace('class="svgAttack"', 'class="roleIcon svgAttack"')
            + '<h2>' + esc(T.attacker) + '</h2><div class="outcome"><div class="outcomeIcons">' + (attWins ? D.svg.svgAttack : D.svg.svgAttackLost) + (attLoss >= 0.9999 ? D.svg.svgSkull : '') + '</div></div></div>'
            + '<div class="content">' + a.html
            + (raid && attWins ? '<table><tr class="additionalInfo"><td><i class="carry"></i>' + esc(T.bounty) + ': <span>' + fmt(a.bounty) + '</span></td></tr></table>' : '')
            + '</div></div>';
        defs.forEach(function (g, i) {
            var r = resultTable(g, defLoss, (dead.def || [])[i]);
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
    // "Simulovat" sits at the bottom of the form, in the free white space under the troops (no floating bar)
    function updateSticky() {}

    /* ------------------------------------------------------------------ travel simulator */
    var tr = root.querySelector('.travelSimulator');
    function tf(name) { return tr.querySelector('[name="' + name + '"]'); }
    var calcButton = tr.querySelector('button.calculate');
    function coordsValid() {
        return ['sourceX', 'sourceY', 'targetX', 'targetY'].every(function (n) { return coord(n) !== null; });
    }
    function updateCalc() { calcButton.disabled = !coordsValid(); calcButton.classList.toggle('disabled', calcButton.disabled); }
    function coord(name) {
        var v = String(tf(name).value).trim().replace(/[\u2212\u2013]/g, '-');
        if (!/^-?\d+$/.test(v)) return null;
        v = parseInt(v, 10);
        return Math.abs(v) <= D.mapSize ? v : null;
    }
    /* Formulas::getDistance: the map wraps around */
    function delta(a, b) {
        var size = 2 * D.mapSize + 1;
        return ((a - b + size + D.mapSize) % size + size) % size - D.mapSize;
    }
    function distance(x1, y1, x2, y2) { return Math.sqrt(Math.pow(delta(x2, x1), 2) + Math.pow(delta(y2, y1), 2)); }
    function duration(sec) {
        sec = Math.round(sec);
        var h = Math.floor(sec / 3600), m = Math.floor(sec % 3600 / 60), s = sec % 60;
        return h + ':' + (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }
    /* Game\SpeedCalculator::calc */
    function calculate() {
        var sx = coord('sourceX'), sy = coord('sourceY'), tx = coord('targetX'), ty = coord('targetY');
        var sv = tr.querySelector('.sourceCoordinatesValidation'), tv = tr.querySelector('.targetCoordinatesValidation');
        sv.hidden = sx !== null && sy !== null; sv.textContent = T.invalidCoords;
        tv.hidden = tx !== null && ty !== null; tv.textContent = T.invalidCoords;
        if (!sv.hidden || !tv.hidden) return;
        var dist = distance(sx, sy, tx, ty);
        var speed = Math.max(1, (num(tf('speed').value) || 1) * D.movementSpeed);
        speed *= 1 + num(tf('item').value) / 100;                                   // map: own villages
        if (dist > D.bootsMinDistance) speed *= 1 + num(tf('boots').value) / 100;    // spurs
        speed *= parseFloat(tf('artefact').value) || 1;
        var arena = num(tf('arena').value), sec;
        if (arena > 0 && dist > 20) sec = (20 / speed + (dist - 20) / (speed * (10 + arena) * 10 / 100)) * 3600;
        else sec = dist / speed * 3600;
        var loading = tr.querySelector('.loadingIndicator');
        loading.hidden = false;
        setTimeout(function () {
            loading.hidden = true;
            tr.querySelector('.duration span').textContent = duration(sec);
            tr.querySelector('.distance span').textContent = (Math.round(dist * 10) / 10).toString().replace('.', ',') + ' ' + T.fields;
            tr.querySelector('.duration').classList.remove('outdated');
            tr.querySelector('.distance').classList.remove('outdated');
        }, 250);
    }
    tr.querySelector('button.calculate').addEventListener('click', calculate);
    tr.querySelector('button.switchSourceAndTarget').addEventListener('click', function () {
        ['X', 'Y'].forEach(function (c) { var a = tf('source' + c), b = tf('target' + c), v = a.value; a.value = b.value; b.value = v; });
        updateCalc();
    });
    tr.addEventListener('input', function () {
        updateCalc();
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
    updateCalc();
    render();
    var start = (location.hash || '').replace('#', '');
    show(start === 'travel' || start === 'combat' ? start : favorite());
})();
