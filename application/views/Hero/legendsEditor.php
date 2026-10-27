<?php
/*
 * Appearance editor of the Travian Legends hero (application/HeroLegends.php), ported from the main branch
 * (hero/v2/appearanceLegends.php). The browser draws the preview and the option thumbnails from the parts on
 * canvases; "Save" posts the look to hero_legends.php, the server composes the hero images.
 * Self-contained styles, so it works with both looks of the game.
 */
$hlUid = (int)$session->uid;
$hlT = static function ($const, $fallback) { return defined($const) ? constant($const) : $fallback; };
$hlLabels = [
    'jaw' => $hlT('HEROL_JAW', 'Face'), 'eyes' => $hlT('HEROL_EYES', 'Eyes'), 'brows' => $hlT('HEROL_BROWS', 'Eyebrows'),
    'nose' => $hlT('HEROL_NOSE', 'Nose'), 'mouth' => $hlT('HEROL_MOUTH', 'Mouth'), 'ears' => $hlT('HEROL_EARS', 'Ears'),
    'hair' => $hlT('HEROL_HAIR', 'Hair'), 'beard' => $hlT('HEROL_BEARD', 'Beard'),
    'scar' => $hlT('HEROL_SCAR', 'Scar'), 'tattoo' => $hlT('HEROL_TATTOO', 'Tattoo'),
    'skinColor' => $hlT('HEROL_SKIN', 'Skin colour'), 'eyeColor' => $hlT('HEROL_EYECOLOR', 'Eye colour'),
    'hairColor' => $hlT('HEROL_HAIRCOLOR', 'Hair colour'),
];
$hlAppearance = HeroLegends::get($hlUid);
$hlConfig = [
    'appearance' => $hlAppearance,
    'tribe' => $hlAppearance['tribeColor'] ?? 'roman',
    'options' => HeroLegends::options(),
    'categories' => HeroLegends::CATEGORIES,
    'layers' => HeroLegends::LAYERS,
    'indexUrl' => HeroLegends::PACK_DIR . 'index.json',
    'partsUrl' => HeroLegends::partsUrl(),
    'saveUrl' => 'hero_legends.php',
    'labels' => $hlLabels,
];
?>
<style>
    .hlEditor { display: grid; grid-template-columns: 220px minmax(0, 1fr); gap: 12px; margin: 8px 0 12px; font-family: Arial, Helvetica, Verdana, sans-serif; }
    .hlEditor.isLoading { opacity: .5; pointer-events: none; }
    .hlEditor .hlPreview { position: relative; width: 220px; }
    .hlEditor .hlPreview canvas { display: block; width: 220px; height: 289px; border-radius: 6px; border: 1px solid #b9a98c;
        background: radial-gradient(ellipse at 50% 35%, #f6efe1 0%, #e3d6bd 70%, #cdbb9a 100%); }
    .hlEditor .hlActions { display: flex; gap: 6px; margin-top: 6px; }
    .hlEditor .hlActions button, .hlEditor .hlButtons button { cursor: pointer; border: 1px solid #5c4134; border-radius: 4px; padding: 5px 10px;
        font: bold 12px Arial, sans-serif; color: #fff; background: linear-gradient(#8eb341, #5d8a1e); box-shadow: 0 1px 2px rgba(0, 0, 0, .35); }
    .hlEditor .hlActions button { flex: 1; color: #3f2c22; background: linear-gradient(#f3ead8, #d8c8a8); padding: 5px 4px; }
    .hlEditor .hlActions button.active { background: linear-gradient(#e1cfa8, #c4ad80); }
    .hlEditor button.disabled { opacity: .45; cursor: default; }
    .hlEditor .hlCategories { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
    .hlEditor .hlCategory { cursor: pointer; padding: 4px 8px; border: 1px solid #c8b896; border-radius: 12px; background: #f7f1e3; color: #5c4134; font-size: 11px; font-weight: bold; }
    .hlEditor .hlCategory.selected { background: #71a32f; border-color: #4d7a17; color: #fff; }
    .hlEditor .hlCategory.disabled { opacity: .35; cursor: default; }
    .hlEditor .hlOptions { display: grid; grid-template-columns: repeat(auto-fill, 64px); gap: 6px; min-height: 210px; align-content: start; }
    .hlEditor .hlOption { position: relative; cursor: pointer; width: 64px; height: 64px; border: 2px solid #d6c7a7; border-radius: 6px; background: #efe5d0; overflow: hidden; }
    .hlEditor .hlOption canvas { width: 64px; height: 64px; display: block; }
    .hlEditor .hlOption.selected { border-color: #71a32f; box-shadow: 0 0 0 2px rgba(113, 163, 47, .35); }
    .hlEditor .hlOption.empty:after { content: "∅"; position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #a8987a; }
    .hlEditor .hlPages { display: flex; gap: 4px; margin-top: 6px; }
    .hlEditor .hlPages button { cursor: pointer; min-width: 24px; border: 1px solid #c8b896; border-radius: 4px; background: #f7f1e3; color: #5c4134; }
    .hlEditor .hlPages button.selected { background: #71a32f; color: #fff; }
    .hlEditor .hlColorsTitle { margin: 10px 0 4px; font-weight: bold; color: #5c4134; font-size: 12px; }
    .hlEditor .hlColors { display: flex; flex-wrap: wrap; gap: 5px; }
    .hlEditor .hlColor { cursor: pointer; width: 22px; height: 22px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #9d8b6a; }
    .hlEditor .hlColor.selected { box-shadow: 0 0 0 2px #71a32f; }
    .hlEditor .hlButtons { display: flex; gap: 8px; justify-content: flex-end; margin-top: 12px; }
    .hlEditor .hlButtons .resetChanges { color: #3f2c22; background: linear-gradient(#f3ead8, #d8c8a8); }
</style>
<div class="hlEditor" id="heroLegendsEditor" data-config="<?php echo htmlspecialchars(json_encode($hlConfig), ENT_QUOTES); ?>">
    <div class="hlPreview">
        <canvas class="hlPreviewCanvas" width="508" height="668"></canvas>
        <div class="hlActions">
            <button type="button" class="toggleGender"><?php echo htmlspecialchars($hlT('HEROL_GENDER', 'Gender')); ?></button>
            <button type="button" class="shuffleConfig"><?php echo htmlspecialchars($hlT('HEROL_RANDOM', 'Random')); ?></button>
            <button type="button" class="toggleZoom"><?php echo htmlspecialchars($hlT('HEROL_ZOOM', 'Whole body')); ?></button>
        </div>
    </div>
    <div class="hlSelection">
        <div class="hlCategories">
            <?php foreach (HeroLegends::CATEGORIES as $category => $unused) { ?>
                <div class="hlCategory" data-category="<?php echo $category; ?>"><?php echo htmlspecialchars($hlLabels[$category]); ?></div>
            <?php } ?>
        </div>
        <div class="hlOptions"></div>
        <div class="hlPages"></div>
        <div class="hlColorsWrapper">
            <div class="hlColorsTitle"></div>
            <div class="hlColors"></div>
        </div>
        <div class="hlButtons">
            <button type="button" class="resetChanges disabled"><?php echo htmlspecialchars($hlT('HEROL_RESET', 'Reset')); ?></button>
            <button type="button" class="saveChanges disabled"><?php echo htmlspecialchars($hlT('HEROL_SAVE', 'Save')); ?></button>
        </div>
    </div>
</div>
<script type="text/javascript">
(function () {
    var root = document.getElementById('heroLegendsEditor');
    if (!root) return;
    var cfg = JSON.parse(root.getAttribute('data-config'));
    var saved = Object.assign({}, cfg.appearance), current = Object.assign({}, cfg.appearance);
    var category = 'jaw', page = 0, zoomed = true, index = null, images = {};
    var optionsEl = root.querySelector('.hlOptions'), pagesEl = root.querySelector('.hlPages'), preview = root.querySelector('.hlPreviewCanvas');
    var COLORS = ['skinColor', 'eyeColor', 'hairColor'];
    // swatches for the colour names of the parts
    var SWATCH = {
        skin1: '#f3d2b5', skin2: '#d9a982', skin3: '#a8714a', skin4: '#6b4329',
        black: '#1e1a18', blind: '#d8dde0', blue: '#3c74c6', brown: '#6b4226', brownblue: '#5c6a7a', gold: '#d6a531',
        green: '#4f8f3a', greenblue: '#3f8a86', grey: '#8d8d8d', red: '#b5352d', silver: '#c3c7cc', yellow: '#e2c541',
        blonde: '#e3c27a', blondepink: '#e9b4a8', blondered: '#d8925a', brightbrown: '#9a6a3f', redblack: '#5a2522',
        signalred: '#e0261c', violet: '#7a4b9a', white: '#f1efe9'
    };

    function optionsOf(gender) { return cfg.options[gender || current.gender]; }
    function normalize(c) {
        var o = optionsOf(c.gender), r = {gender: c.gender, tribeColor: cfg.tribe};
        COLORS.forEach(function (k) { var l = o.colors[k]; r[k] = l.indexOf(c[k]) >= 0 ? c[k] : l[0]; });
        Object.keys(o.categories).forEach(function (k) {
            var l = o.categories[k], v = c[k] === undefined ? null : c[k];
            r[k] = l.indexOf(v) >= 0 ? v : (l.length ? l[0] : null);
        });
        return r;
    }
    function layerKeys(c) {
        var keys = [], g = c.gender;
        Object.keys(cfg.layers).forEach(function (layer) {
            var cat = cfg.layers[layer][0], color = cfg.layers[layer][1];
            var v = cat === null ? '' : c[cat];
            if (cat !== null && (v === null || v === undefined)) return;
            var s = color ? c[color] : null, key;
            if (layer === 'base') key = g + '/skinColor/' + s + '/base/base-' + s;
            else if (layer === 'leftArm' || layer === 'rightArm') key = g + '/skinColor/' + s + '/' + layer + '/' + layer + 'Default-' + s;
            else if (layer === 'body') key = g + '/tribeColor/' + cfg.tribe + '/body/body-' + cfg.tribe;
            else if (layer === 'tattoo') key = g + '/tattoo/tattoo' + v;
            else key = g + '/' + color + '/' + s + '/' + layer + '/' + layer + v + '-' + s;
            if (index.parts[key]) keys.push(key);
        });
        return keys;
    }
    function image(key) {
        if (!images[key]) {
            images[key] = new Promise(function (resolve) {
                var img = new Image();
                img.onload = function () { resolve(img); };
                img.onerror = function () { resolve(null); };
                img.src = cfg.partsUrl + key + '.png';
            });
        }
        return images[key];
    }
    function headBox(c, size) {
        var jaw = null;
        layerKeys(c).forEach(function (k) { if (k.indexOf('/jaw/') >= 0) jaw = index.parts[k]; });
        jaw = jaw || [387, 226, 122, 106];
        return [jaw[0] + jaw[2] / 2 - size / 2, jaw[1] + jaw[3] - size * 0.78, size, size];
    }
    function draw(canvas, c, box) {
        var keys = layerKeys(c);
        return Promise.all(keys.map(image)).then(function (imgs) {
            var ctx = canvas.getContext('2d');
            var scale = Math.min(canvas.width / box[2], canvas.height / box[3]);
            var ox = (canvas.width - box[2] * scale) / 2, oy = (canvas.height - box[3] * scale) / 2;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            imgs.forEach(function (img, i) {
                if (!img) return;
                var p = index.parts[keys[i]];
                ctx.drawImage(img, ox + (p[0] - box[0]) * scale, oy + (p[1] - box[1]) * scale, img.width * scale, img.height * scale);
            });
        });
    }
    function drawPreview() {
        var box = zoomed ? headBox(current, 360) : [0, 0, index.size[0], index.size[1]];
        if (zoomed) box[3] = box[2] * preview.height / preview.width;
        return draw(preview, current, box);
    }
    function dirty() {
        return Object.keys(current).some(function (k) { return current[k] !== saved[k]; });
    }
    function render() {
        var o = optionsOf();
        root.querySelectorAll('.hlCategory').forEach(function (el) {
            var list = o.categories[el.getAttribute('data-category')] || [];
            el.classList.toggle('disabled', list.length === 0);
            el.classList.toggle('selected', el.getAttribute('data-category') === category);
        });
        var list = o.categories[category] || [], perPage = 12, pages = Math.max(1, Math.ceil(list.length / perPage));
        page = Math.min(page, pages - 1);
        optionsEl.innerHTML = '';
        list.slice(page * perPage, (page + 1) * perPage).forEach(function (value) {
            var el = document.createElement('div');
            el.className = 'hlOption' + (current[category] === value ? ' selected' : '') + (value === null ? ' empty' : '');
            el.setAttribute('data-option', value === null ? '' : value);
            optionsEl.appendChild(el);
            if (value === null) return;
            var canvas = document.createElement('canvas');
            canvas.width = 144; canvas.height = 144;
            el.appendChild(canvas);
            var look = Object.assign({}, current); look[category] = value;
            draw(canvas, look, headBox(look, category === 'hair' || category === 'beard' ? 340 : 260));
        });
        pagesEl.innerHTML = '';
        if (pages > 1) for (var i = 0; i < pages; i++) {
            pagesEl.insertAdjacentHTML('beforeend', '<button type="button" data-page="' + i + '"' + (i === page ? ' class="selected"' : '') + '>' + (i + 1) + '</button>');
        }
        var group = cfg.categories[category][1], wrap = root.querySelector('.hlColorsWrapper');
        wrap.style.display = group ? '' : 'none';
        if (group) {
            root.querySelector('.hlColorsTitle').textContent = cfg.labels[group];
            root.querySelector('.hlColors').innerHTML = o.colors[group].map(function (c) {
                return '<div class="hlColor' + (current[group] === c ? ' selected' : '') + '" title="' + c + '" data-color="' + c + '" data-group="' + group
                    + '" style="background:' + (SWATCH[c] || '#ccc') + '"></div>';
            }).join('');
        }
        root.querySelector('.toggleZoom').classList.toggle('active', !zoomed);
        var changed = dirty();
        root.querySelector('.resetChanges').classList.toggle('disabled', !changed);
        root.querySelector('.saveChanges').classList.toggle('disabled', !changed);
        drawPreview();
    }
    function random(list) { return list[Math.floor(Math.random() * list.length)]; }

    root.addEventListener('click', function (event) {
        var t = event.target;
        var cat = t.closest('.hlCategory');
        if (cat && !cat.classList.contains('disabled')) { category = cat.getAttribute('data-category'); page = 0; render(); return; }
        var pageButton = t.closest('.hlPages button');
        if (pageButton) { page = parseInt(pageButton.getAttribute('data-page'), 10); render(); return; }
        var option = t.closest('.hlOption');
        if (option) { var v = option.getAttribute('data-option'); current[category] = v === '' ? null : parseInt(v, 10); render(); return; }
        var color = t.closest('.hlColor');
        if (color) { current[color.getAttribute('data-group')] = color.getAttribute('data-color'); render(); return; }
        if (t.closest('.toggleGender')) {
            current = normalize(Object.assign({}, current, {gender: current.gender === 'male' ? 'female' : 'male'}));
            if (!optionsOf().categories[category].length) category = 'jaw';
            render(); return;
        }
        if (t.closest('.shuffleConfig')) {
            var o = optionsOf(), look = {gender: current.gender};
            COLORS.forEach(function (k) { look[k] = random(o.colors[k]); });
            Object.keys(o.categories).forEach(function (k) {
                var l = o.categories[k].filter(function (v) { return v !== null; });
                look[k] = l.length ? random(l) : null;
                if ((k === 'scar' || k === 'tattoo') && Math.random() < 0.7) look[k] = null;
            });
            current = normalize(look); render(); return;
        }
        if (t.closest('.toggleZoom')) { zoomed = !zoomed; render(); return; }
        if (t.closest('.resetChanges') && dirty()) { current = Object.assign({}, saved); render(); return; }
        if (t.closest('.saveChanges') && dirty()) {
            root.classList.add('isLoading');
            var body = new URLSearchParams();
            body.append('cmd', 'save');
            body.append('config', JSON.stringify(current));
            fetch(cfg.saveUrl, {method: 'POST', body: body, credentials: 'same-origin'})
                .then(function (r) { return r.json(); })
                .then(function (r) { if (r && r.saved) window.location.reload(); else root.classList.remove('isLoading'); })
                .catch(function () { root.classList.remove('isLoading'); });
        }
    });

    root.classList.add('isLoading');
    fetch(cfg.indexUrl).then(function (r) { return r.json(); }).then(function (data) {
        index = data;
        root.classList.remove('isLoading');
        render();
    });
})();
</script>
