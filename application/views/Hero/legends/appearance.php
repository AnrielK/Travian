<?php
/*
 * Hero appearance editor of the Travian Legends hero in the Legends 655 look (hero/v2/appearanceLegends.php of the
 * main branch). The browser composes previews and option thumbnails from the parts; saving posts the look to
 * hero_legends.php, the server composes the hero images (HeroLegends).
 */
$e = [LegendsUi::class, 'e'];
$t = [LegendsUi::class, 't'];
$labels = [
    'jaw' => $t('HEROL_JAW', 'Face'), 'eyes' => $t('HEROL_EYES', 'Eyes'), 'brows' => $t('HEROL_BROWS', 'Eyebrows'),
    'nose' => $t('HEROL_NOSE', 'Nose'), 'mouth' => $t('HEROL_MOUTH', 'Mouth'), 'ears' => $t('HEROL_EARS', 'Ears'),
    'hair' => $t('HEROL_HAIR', 'Hair'), 'beard' => $t('HEROL_BEARD', 'Beard'),
    'scar' => $t('HEROL_SCAR', 'Scar'), 'tattoo' => $t('HEROL_TATTOO', 'Tattoo'),
    'skinColor' => $t('HEROL_SKIN', 'Skin colour'), 'eyeColor' => $t('HEROL_EYECOLOR', 'Eye colour'),
    'hairColor' => $t('HEROL_HAIRCOLOR', 'Hair colour'),
];
$appearance = HeroLegends::get((int)$session->uid);
$config = [
    'appearance' => $appearance,
    'tribe' => $appearance['tribeColor'] ?? 'roman',
    'options' => HeroLegends::options(),
    'categories' => HeroLegends::CATEGORIES,
    'layers' => HeroLegends::LAYERS,
    'equipment' => (object)[],
    'armPoses' => (object)[],
    'indexUrl' => HeroLegends::PACK_DIR . 'index.json',
    'partsUrl' => HeroLegends::partsUrl(),
    'labels' => $labels,
];
$genderIcon = LegendsUi::icon('0 0 21 21', '<path class="female" d="M21 .8v4.4a.8.8 0 0 1-.8.8h-.4a.8.8 0 0 1-.8-.8V3.42l-4 4a5.51 5.51 0 0 1-5.71 8.5 5.37 5.37 0 0 1-1.22-.43A6.46 6.46 0 0 0 10.2 14a3.36 3.36 0 0 0 1-.05A3.5 3.5 0 0 0 9.76 7.1a3.53 3.53 0 0 0-2.1 1.35 3.38 3.38 0 0 0-.59 1.36 3.41 3.41 0 0 0 0 1.49 2.44 2.44 0 0 1-1.07.58 2.620 2.62 0 0 1-.79 0l-.06-.21A5.52 5.52 0 0 1 9.37 5.1c.23 0 .46-.08.69-.1h.44a5.51 5.51 0 0 1 3.11 1l4-4H15.8a.8.8 0 0 1-.8-.8V.8a.8.8 0 0 1 .8-.8h4.4a.8.8 0 0 1 .8.8Z"/><path class="male" d="M10.82 8.11a2.62 2.62 0 0 0-.79 0 2.46 2.46 0 0 0-1.13.59v.11a3.51 3.51 0 0 1-2.77 4.12 3.41 3.41 0 0 1-1.35 0 2.79 2.79 0 0 1-.39-.1A3.5 3.5 0 0 1 5.8 6a6.55 6.55 0 0 1 2.09-1.45A5.58 5.58 0 0 0 5.41 4 5.57 5.57 0 0 0 0 9.22a5.49 5.49 0 0 0 4.5 5.68V17H3.3a.8.8 0 0 0-.8.8v.4a.8.8 0 0 0 .8.8h1.2v1.2a.8.8 0 0 0 .8.8h.4a.8.8 0 0 0 .8-.8V19h1.2a.8.8 0 0 0 .8-.8v-.4a.8.8 0 0 0-.8-.8H6.5v-2.1A5.53 5.53 0 0 0 11 9.5a5.41 5.41 0 0 0-.18-1.39Z"/>');
$diceIcon = LegendsUi::icon('0 0 20 20', '<path d="M3 2h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1Zm2.5 2.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm9 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM10 8.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm-4.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm9 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>');
$zoomIcon = '<svg viewBox="0 0 21.77 22" class="zoomOut"><g class="outline"><path d="M19.09 2.48a8.820 8.82 0 1 0 .19 12.46 8.81 8.81 0 0 0-.19-12.46Zm-10.66 11a6.51 6.51 0 1 1 9.2-.14 6.51 6.51 0 0 1-9.2.14Zm-2.88 2.29.57.55a1.58 1.58 0 0 1 0 2.22l-2.9 3a1.55 1.55 0 0 1-2.21 0L.48 21a1.55 1.55 0 0 1 0-2.21l2.9-3a1.56 1.56 0 0 1 2.17-.02Z"/><path class="horizontal" d="M15.62 9.8h-5.33a.85.85 0 0 1-.85-.8v-.33a.85.85 0 0 1 .85-.85h5.33a.85.85 0 0 1 .85.85V9a.85.85 0 0 1-.85.8Z"/><path class="vertical" d="M14 6.15v5.32a.86.86 0 0 1-.85.86h-.29a.86.86 0 0 1-.85-.86V6.15a.85.85 0 0 1 .85-.85h.29a.85.85 0 0 1 .85.85Z"/></g><g class="icon"><path d="M19.09 2.48a8.82 8.82 0 1 0 .19 12.46 8.81 8.81 0 0 0-.19-12.46Zm-10.66 11a6.51 6.51 0 1 1 9.2-.14 6.51 6.51 0 0 1-9.2.14Zm-2.88 2.29.57.55a1.58 1.58 0 0 1 0 2.22l-2.9 3a1.55 1.55 0 0 1-2.21 0L.48 21a1.55 1.55 0 0 1 0-2.21l2.9-3a1.56 1.56 0 0 1 2.17-.02Z"/><path class="horizontal" d="M15.62 9.8h-5.33a.85.85 0 0 1-.85-.8v-.33a.85.85 0 0 1 .85-.85h5.33a.85.85 0 0 1 .85.85V9a.85.85 0 0 1-.85.8Z"/><path class="vertical" d="M14 6.15v5.32a.86.86 0 0 1-.85.86h-.29a.86.86 0 0 1-.85-.86V6.15a.85.85 0 0 1 .85-.85h.29a.85.85 0 0 1 .85.85Z"/></g></svg>';
?>
<div class="contentV2">
    <div class="appearanceWrapper heroLegendsEditor" id="heroLegendsEditor" data-config="<?=$e(json_encode($config)); ?>">
        <div class="categories">
            <?php foreach (HeroLegends::CATEGORIES as $category => $unused): ?>
                <div class="category <?=$category; ?>" data-category="<?=$category; ?>" title="<?=$e($labels[$category]); ?>"><i></i></div>
            <?php endforeach; ?>
        </div>
        <div class="selectionWrapper">
            <div class="pages"></div>
            <div class="previewWrapper">
                <div class="preview zoomedIn"><canvas class="heroPreviewCanvas" width="254" height="334"></canvas></div>
                <div class="actions">
                    <?=LegendsUi::button($genderIcon, ['icon' => true, 'class' => 'toggleGender', 'title' => $t('HEROL_GENDER', 'Gender')]); ?>
                    <?=LegendsUi::button($diceIcon, ['icon' => true, 'class' => 'shuffleConfig', 'title' => $t('HEROL_RANDOM', 'Random')]); ?>
                    <?=LegendsUi::button($zoomIcon, ['icon' => true, 'class' => 'toggleZoom', 'title' => $t('HEROL_ZOOM', 'Whole body')]); ?>
                </div>
            </div>
            <div class="colorsWrapper" style="display:none">
                <div class="colorPalette"></div>
                <div class="colors"></div>
            </div>
        </div>
        <div class="buttonWrapper">
            <?=LegendsUi::button($e($t('HEROL_BUY', 'Buy selected options')), ['appearance' => 'gold', 'class' => 'buyOptions', 'disabledClass' => true,
                'title' => $t('HEROL_FREE', 'All options of this world are free.')]); ?>
            <?=LegendsUi::button(LegendsUi::icon('0 0 16.02 16', '<path d="M2.35 2.35A8 8 0 1 1 .27 10h2.08A6 6 0 1 0 8 2a5.93 5.930 0 0 0-4.22 1.78L7 7H0V0Z"/>'),
                ['icon' => true, 'class' => 'resetChanges', 'disabledClass' => true, 'title' => $t('HEROL_RESET', 'Reset')]); ?>
            <?=LegendsUi::button($t('HEROL_SAVE', 'Save'), ['class' => 'saveChanges', 'disabledClass' => true]); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
(function () {
    var root = document.getElementById('heroLegendsEditor');
    if (!root) return;
    var cfg = JSON.parse(root.dataset.config);
    var saved = Object.assign({}, cfg.appearance), current = Object.assign({}, cfg.appearance);
    var category = 'jaw', page = 0, zoomed = true, index = null, images = {};
    var pagesEl = root.querySelector('.pages'), preview = root.querySelector('.heroPreviewCanvas');
    var colorsWrapper = root.querySelector('.colorsWrapper');
    var COLORS = ['skinColor', 'eyeColor', 'hairColor'];

    function optionsOf(gender) { return cfg.options[gender || current.gender]; }

    function normalize(c) {
        var o = optionsOf(c.gender), r = {gender: c.gender};
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
            // Equipped items (same rules as HeroLegends::layerKeys): armour replaces the tribe clothes.
            if (cat === 'equipment' || (layer === 'body' && cfg.equipment.body)) {
                var part = cfg.equipment[layer] && g + '/equipment/' + cfg.equipment[layer];
                if (part && index.parts[part]) { keys.push(part); return; }
                if (cat === 'equipment') return;
            }
            var v = cat === null ? '' : c[cat];
            if (cat !== null && (v === null || v === undefined)) return;
            var s = color ? c[color] : null, key;
            if (layer === 'base') key = g + '/skinColor/' + s + '/base/base-' + s;
            else if (layer === 'leftArm' || layer === 'rightArm') key = g + '/skinColor/' + s + '/' + layer + '/' + layer + (cfg.armPoses[layer] || 'Default') + '-' + s;
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

    /** Head square on the hero canvas (follows the jaw part, like the server's HeroLegends::headBox). */
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
        root.querySelectorAll('.category').forEach(function (el) {
            var list = o.categories[el.dataset.category] || [];
            el.classList.toggle('disabled', list.length === 0);
            el.classList.toggle('selected', el.dataset.category === category);
        });
        var list = o.categories[category] || [];
        var perPage = list.length > 15 ? 12 : 15, pages = Math.max(1, Math.ceil(list.length / perPage));
        page = Math.min(page, pages - 1);
        pagesEl.innerHTML = '';
        var wrapper = document.createElement('div');
        wrapper.className = 'page page' + page + ' active';
        var options = document.createElement('div');
        options.className = 'optionsWrapper';
        list.slice(page * perPage, (page + 1) * perPage).forEach(function (value) {
            var el = document.createElement('div'), name = 'option' + (value === null ? 'Empty' : value);
            el.className = 'option ' + name + (current[category] === value ? ' selected' : '');
            el.dataset.option = value === null ? '' : value;
            var thumb = document.createElement('div');
            thumb.className = 'optionPreview ' + current.gender + ' ' + category + ' ' + name;
            var canvas = document.createElement('canvas');
            canvas.width = 84; canvas.height = 84;
            thumb.appendChild(canvas);
            el.appendChild(thumb);
            if (current[category] === value) el.insertAdjacentHTML('beforeend', '<div class="selectedIndicator"></div>');
            options.appendChild(el);
            var look = Object.assign({}, current); look[category] = value;
            draw(canvas, look, headBox(look, category === 'hair' || category === 'beard' ? 340 : 260));
        });
        wrapper.appendChild(options);
        pagesEl.appendChild(wrapper);
        if (pages > 1) {
            var nav = '<div class="pagination"><button type="button" class="back' + (page === 0 ? ' disabled' : '') + '" data-page="' + (page - 1) + '"></button>';
            for (var i = 0; i < pages; i++) nav += '<button type="button" class="page' + (i === page ? ' selected' : '') + '" data-page="' + i + '"></button><div class="pageIndex">' + (i + 1) + '</div>';
            pagesEl.insertAdjacentHTML('beforeend', nav + '<button type="button" class="forward' + (page === pages - 1 ? ' disabled' : '') + '" data-page="' + (page + 1) + '"></button></div>');
        }
        var colorGroup = cfg.categories[category][1];
        colorsWrapper.style.display = colorGroup ? '' : 'none';
        if (colorGroup) {
            colorsWrapper.querySelector('.colorPalette').textContent = cfg.labels[colorGroup];
            var colors = colorsWrapper.querySelector('.colors');
            colors.className = 'colors ' + colorGroup;
            colors.innerHTML = o.colors[colorGroup].map(function (c) {
                return '<div class="color ' + c + (current[colorGroup] === c ? ' selected' : '') + '" data-color="' + c + '" data-group="' + colorGroup + '">'
                    + '<div class="colorPreview ' + c + '"></div>' + (current[colorGroup] === c ? '<div class="selectedIndicator"></div>' : '') + '</div>';
            }).join('');
        }
        var gender = root.querySelector('.toggleGender');
        gender.classList.remove('male', 'female');
        gender.classList.add(current.gender);
        root.querySelector('.preview').className = 'preview ' + current.gender + (zoomed ? ' zoomedIn' : ' zoomedOut');
        var changed = dirty();
        root.querySelector('.resetChanges').classList.toggle('disabled', !changed);
        root.querySelector('.saveChanges').classList.toggle('disabled', !changed);
        drawPreview();
    }

    function random(list) { return list[Math.floor(Math.random() * list.length)]; }

    root.addEventListener('click', function (event) {
        var t = event.target;
        var cat = t.closest('.category');
        if (cat && !cat.classList.contains('disabled')) { category = cat.dataset.category; page = 0; render(); return; }
        var pageButton = t.closest('.pagination button');
        if (pageButton && !pageButton.classList.contains('disabled')) { page = parseInt(pageButton.dataset.page, 10); render(); return; }
        var option = t.closest('.option');
        if (option) { current[category] = option.dataset.option === '' ? null : parseInt(option.dataset.option, 10); render(); return; }
        var color = t.closest('.color');
        if (color) { current[color.dataset.group] = color.dataset.color; render(); return; }
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
            fetch('hero_legends.php', {method: 'POST', body: body, credentials: 'same-origin'})
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
