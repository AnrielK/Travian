<?php
/* Installation wizard of the XAMPP runtime, served at /install/ by xampp/router.php (back end: api.php). */
header('Content-Type: text/html; charset=utf-8');
?><!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Instalace OpenVillage</title>
<style>
:root{--bg:#efe6cf;--card:#fffaf0;--ink:#3b2f1e;--muted:#7a6a50;--line:#d9c9a3;--green:#5b8a2e;--green-dark:#46701f;--red:#b23a2a;--amber:#b7791f}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.5 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif}
main{max-width:760px;margin:0 auto;padding:24px 16px 48px}
h1{margin:0 0 4px;font-size:26px}
.sub{color:var(--muted);margin:0 0 20px}
ol.steps{display:flex;gap:6px;list-style:none;padding:0;margin:0 0 20px;counter-reset:s;flex-wrap:wrap}
ol.steps li{flex:1 1 0;min-width:110px;padding:8px 10px;border-radius:8px;background:#e3d6b6;color:var(--muted);font-size:13px;counter-increment:s}
ol.steps li::before{content:counter(s) ". "}
ol.steps li.active{background:var(--green);color:#fff}
ol.steps li.done{background:#c9dcae;color:var(--ink)}
section{display:none;background:var(--card);border:1px solid var(--line);border-radius:12px;padding:20px}
section.active{display:block}
h2{margin:0 0 12px;font-size:19px}
table{width:100%;border-collapse:collapse}
td{padding:6px 4px;border-bottom:1px solid var(--line);vertical-align:top}
td.state{width:28px;font-weight:bold}
.ok{color:var(--green)}.bad{color:var(--red)}.warn{color:var(--amber)}
label{display:block;margin:10px 0 4px;font-weight:600}
.hint{font-weight:normal;color:var(--muted);font-size:13px}
input,select{width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:6px;font:inherit;background:#fff;color:var(--ink)}
.row{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}
@media (max-width:560px){.row{grid-template-columns:1fr}}
.check{display:flex;gap:8px;align-items:flex-start;margin-top:12px;font-weight:normal}
.check input{width:auto;margin-top:4px}
.actions{display:flex;justify-content:space-between;gap:8px;margin-top:20px}
button{padding:9px 18px;border:0;border-radius:6px;background:var(--green);color:#fff;font:inherit;font-weight:600;cursor:pointer}
button:hover{background:var(--green-dark)}
button.secondary{background:#cfc0a0;color:var(--ink)}
button:disabled{opacity:.5;cursor:default}
.msg{margin-top:12px;padding:10px 12px;border-radius:6px;display:none;white-space:pre-wrap}
.msg.show{display:block}
.msg.error{background:#f6dcd6;color:var(--red)}
.msg.info{background:#e4eed6;color:var(--green-dark)}
.bar{height:14px;background:#e3d6b6;border-radius:7px;overflow:hidden;margin:8px 0 12px}
.bar div{height:100%;width:0;background:var(--green);transition:width .4s}
pre{background:#2b241a;color:#e8dcc0;padding:12px;border-radius:8px;max-height:260px;overflow:auto;font-size:13px;white-space:pre-wrap;margin:0}
dl{display:grid;grid-template-columns:max-content 1fr;gap:6px 16px;margin:0}
dt{color:var(--muted)}dd{margin:0;font-weight:600;word-break:break-all}
</style>
</head>
<body>
<main>
<h1>Instalace OpenVillage</h1>
<p class="sub">Průvodce připraví databázi XAMPPu a vytvoří herní svět.</p>
<ol class="steps">
  <li data-step="1">Požadavky</li>
  <li data-step="2">Databáze</li>
  <li data-step="3">Svět</li>
  <li data-step="4">Instalace</li>
  <li data-step="5">Hotovo</li>
</ol>

<section data-step="1">
  <h2>Kontrola požadavků</h2>
  <table id="checks"><tr><td>Kontroluji…</td></tr></table>
  <div class="msg" id="msg1"></div>
  <div class="actions"><button class="secondary" id="recheck">Zkontrolovat znovu</button><button id="next1" disabled>Pokračovat</button></div>
</section>

<section data-step="2">
  <h2>Databáze (MariaDB / MySQL z XAMPPu)</h2>
  <p class="hint">Zadej účet správce databáze. V XAMPPu je to výchozí <b>root</b> bez hesla. Průvodce pod ním založí
    vlastního uživatele <b>openvillage</b> a databáze <b>openvillage_game</b>, <b>openvillage_global</b> a <b>openvillage_cache</b>.</p>
  <div class="row">
    <div><label for="db_host">Server</label><input id="db_host" value="127.0.0.1"></div>
    <div><label for="db_user">Uživatel</label><input id="db_user" value="root" autocomplete="username"></div>
  </div>
  <label for="db_password">Heslo <span class="hint">(v XAMPPu prázdné)</span></label>
  <input id="db_password" type="password" autocomplete="current-password">
  <label class="check" id="overwriteRow" hidden><input type="checkbox" id="overwrite">
    <span>Databáze hry už existují. Přepsat je (stávající svět se smaže).</span></label>
  <div class="msg" id="msg2"></div>
  <div class="actions"><button class="secondary" data-back="1">Zpět</button><button id="next2">Otestovat a pokračovat</button></div>
</section>

<section data-step="3">
  <h2>Nastavení světa</h2>
  <div class="row">
    <div><label for="title">Název hry</label><input id="title" value="OpenVillage 4.6"></div>
    <div><label for="server_name">Název světa</label><input id="server_name" value="Local World"></div>
  </div>
  <div class="row">
    <div><label for="speed">Rychlost</label>
      <select id="speed"><option value="1">1×</option><option value="3">3×</option><option value="5">5×</option>
        <option value="10" selected>10×</option><option value="50">50×</option><option value="100">100×</option></select></div>
    <div><label for="map_size">Velikost mapy <span class="hint">(větší mapa = delší instalace)</span></label>
      <select id="map_size"><option value="25">25 × 25</option><option value="50" selected>50 × 50</option>
        <option value="100">100 × 100</option><option value="200">200 × 200</option></select></div>
  </div>
  <div class="row">
    <div><label for="round_days">Délka kola (dny)</label><input id="round_days" type="number" min="1" max="365" value="35"></div>
    <div><label for="hero_speed">Rychlost hrdiny</label>
      <select id="hero_speed"><option value="1" selected>1×</option><option value="2">2×</option><option value="3">3×</option><option value="5">5×</option></select></div>
  </div>
  <div class="row">
    <div><label for="gpack">Vzhled <span class="hint">(u světa už nejde změnit)</span></label><select id="gpack"></select></div>
    <div><label for="timezone">Časové pásmo</label><select id="timezone"></select></div>
  </div>
  <label for="tribes">Národy <span class="hint">(u světa už nejde změnit)</span></label>
  <select id="tribes"><option value="all" selected>Všechny národy vzhledu (i Egypťané a Hunové)</option>
    <option value="classic">Jen tři původní národy (Římané, Germáni, Galové)</option></select>
  <label for="admin_password">Heslo správce <span class="hint">(účty Multihunter a Support, aspoň 6 znaků)</span></label>
  <input id="admin_password" type="password" autocomplete="new-password">
  <div class="msg" id="msg3"></div>
  <div class="actions"><button class="secondary" data-back="2">Zpět</button><button id="next3">Instalovat</button></div>
</section>

<section data-step="4">
  <h2>Instalace</h2>
  <div class="bar"><div id="bar"></div></div>
  <pre id="log"></pre>
  <div class="msg" id="msg4"></div>
  <div class="actions"><button class="secondary" data-back="3" id="back4" hidden>Zpět</button><span></span></div>
</section>

<section data-step="5">
  <h2>Hotovo</h2>
  <p>Svět je vytvořený a automatika běží, dokud je otevřené okno spouštěče (start.bat / start.sh).</p>
  <dl>
    <dt>Adresa hry</dt><dd id="gameUrl"></dd>
    <dt>Správce</dt><dd>Multihunter (heslo, které jsi zadal)</dd>
    <dt>Nastavení</dt><dd>xampp/config.local.php</dd>
  </dl>
  <p class="hint">Novou instalaci spustíš smazáním souboru xampp/config.local.php a restartem spouštěče.</p>
  <div class="actions"><span></span><button id="play">Přejít do hry</button></div>
</section>
</main>
<script>
(function () {
  'use strict';
  var $ = function (id) { return document.getElementById(id); };
  var token = '';

  function show(step) {
    document.querySelectorAll('section').forEach(function (s) { s.classList.toggle('active', s.dataset.step == step); });
    document.querySelectorAll('ol.steps li').forEach(function (li) {
      li.classList.toggle('active', li.dataset.step == step);
      li.classList.toggle('done', li.dataset.step < step);
    });
    window.scrollTo(0, 0);
  }
  function message(id, text, kind) {
    var m = $(id);
    m.textContent = text || '';
    m.className = 'msg' + (text ? ' show ' + (kind || 'error') : '');
  }
  function api(action, fields) {
    var options = {method: 'GET'};
    var url = '/install/api?action=' + action;
    if (fields) {
      var body = new URLSearchParams(fields);
      body.set('action', action);
      options = {method: 'POST', body: body};
      url = '/install/api';
    }
    return fetch(url, options).then(function (r) {
      return r.json().catch(function () { return {ok: false, error: 'Neplatná odpověď serveru (' + r.status + ')'}; });
    });
  }
  function control(action) {
    return fetch('/__openvillage/' + action, {method: 'POST', headers: {'X-OV-Token': token}}).then(function (r) {
      return r.json().catch(function () { return {ok: false, error: 'Neplatná odpověď serveru (' + r.status + ')'}; });
    });
  }
  function dbFields() {
    return {db_host: $('db_host').value, db_user: $('db_user').value, db_password: $('db_password').value};
  }

  // 1. requirements
  function check() {
    $('next1').disabled = true;
    api('check').then(function (r) {
      var rows = '';
      (r.checks || []).forEach(function (c) {
        var mark = c.ok ? '<span class="ok">✓</span>' : (c.required ? '<span class="bad">✗</span>' : '<span class="warn">!</span>');
        rows += '<tr><td class="state">' + mark + '</td><td>' + c.name + '</td><td>' + c.detail + '</td></tr>';
      });
      $('checks').innerHTML = rows;
      $('next1').disabled = !r.passed;
      message('msg1', r.passed ? '' : 'Některé povinné požadavky nejsou splněné.');
      var gpack = $('gpack');
      gpack.innerHTML = '';
      (r.gpacks || []).forEach(function (g) {
        gpack.add(new Option(g.name + (g.tribes ? ' – ' + g.tribes + (g.tribes < 5 ? ' národy' : ' národů') : ''), g.hash));
      });
      var zones = (Intl.supportedValuesOf ? Intl.supportedValuesOf('timeZone') : []).concat(['UTC']);
      var local = Intl.DateTimeFormat().resolvedOptions().timeZone || r.timezone || 'UTC';
      var tz = $('timezone');
      tz.innerHTML = '';
      zones.filter(function (z, i) { return zones.indexOf(z) === i; }).forEach(function (z) { tz.add(new Option(z, z, false, z === local)); });
    }).catch(function (e) { message('msg1', 'Server neodpovídá: ' + e.message); });
  }
  $('recheck').onclick = check;
  $('next1').onclick = function () { show(2); };

  // 2. database
  $('next2').onclick = function () {
    message('msg2', '');
    $('next2').disabled = true;
    api('testdb', dbFields()).then(function (r) {
      $('next2').disabled = false;
      if (!r.ok) { message('msg2', r.error); return; }
      var used = Object.keys(r.existing || {}).filter(function (k) { return r.existing[k] > 0; });
      if (used.length && !$('overwrite').checked) {
        $('overwriteRow').hidden = false;
        message('msg2', 'Připojeno (' + r.version + '). Databáze ' + used.join(', ') + ' už obsahují data. Pro pokračování potvrď přepsání.');
        return;
      }
      show(3);
    }).catch(function (e) { $('next2').disabled = false; message('msg2', e.message); });
  };

  // 3. world settings + 4. installation
  var log = function (line) { $('log').textContent += line + '\n'; $('log').scrollTop = 1e9; };
  var progress = function (p) { $('bar').style.width = p + '%'; };
  $('next3').onclick = function () {
    message('msg3', '');
    if ($('admin_password').value.length < 6) { message('msg3', 'Heslo správce musí mít aspoň 6 znaků.'); return; }
    var fields = dbFields();
    ['title', 'server_name', 'speed', 'map_size', 'round_days', 'hero_speed', 'gpack', 'tribes', 'timezone', 'admin_password']
      .forEach(function (k) { fields[k] = $(k).value; });
    fields.overwrite = $('overwrite').checked ? '1' : '0';
    show(4);
    $('log').textContent = '';
    $('back4').hidden = true;
    message('msg4', '');
    progress(5);
    var fail = function (text) { message('msg4', text); log('CHYBA: ' + text); $('back4').hidden = false; throw new Error(text); };
    log('Zakládám uživatele a databáze…');
    api('configure', fields).then(function (r) {
      (r.notes || []).forEach(function (l) { log('  ' + l); });
      if (!r.ok) fail(r.error);
      token = r.token;
      progress(20);
      log('Nahrávám schéma databází…');
      return control('reset');
    }).then(function (r) {
      if (!r.ok) fail(r.error);
      (r.log || []).forEach(function (l) { log('  ' + l); });
      progress(40);
      log('Vytvářím mapu a svět (může trvat i několik minut)…');
      var p = 40, timer = setInterval(function () { if (p < 90) progress(++p); }, 1500);
      return control('setup').finally(function () { clearInterval(timer); });
    }).then(function (r) {
      if (!r.ok) fail(r.error + (r.at ? ' (' + r.at + ')' : ''));
      (r.log || []).forEach(function (l) { if (l) log('  ' + l); });
      progress(95);
      log('Dokončuji…');
      return api('finish', {token: token});
    }).then(function (r) {
      if (!r.ok) fail(r.error);
      progress(100);
      log('Hotovo.');
      $('gameUrl').textContent = location.origin + '/';
      setTimeout(function () { show(5); }, 600);
    }).catch(function (e) {
      if ($('back4').hidden) { message('msg4', e.message); $('back4').hidden = false; }
    });
  };

  document.querySelectorAll('[data-back]').forEach(function (b) { b.onclick = function () { show(b.dataset.back); }; });
  $('play').onclick = function () { location.href = '/'; };
  show(1);
  check();
})();
</script>
</body>
</html>
