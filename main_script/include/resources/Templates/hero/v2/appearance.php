<?php
/*
 * Hero appearance in the Travian Legends 655 look (heroV2Appearance): category buttons, a page of options,
 * the face preview with gender / random buttons and the hair colours. The face parts are the game's own
 * (img/hero/<gender>/…); preview and saving go through the heroEditor ajax command.
 */

use resources\View\LegendsUi as UI;

$e = [UI::class, 'e'];
// Legends category => [face attribute, thumbnail folder, thumbnail prefix]
$categories = [
    'jaw' => ['headProfile', 'face', 'face'],
    'eyes' => ['eyes', 'eye', 'eye'],
    'brows' => ['eyebrow', 'eyebrow', 'eyebrow'],
    'nose' => ['nose', 'nose', 'nose'],
    'mouth' => ['mouth', 'mouth', 'mouth'],
    'ears' => ['ears', 'ear', 'ear'],
    'hair' => ['hairStyle', 'hair', 'hair'],
    'beard' => ['beard', 'beard', 'beard'],
    'scar' => [null, null, null],
    'tattoo' => [null, null, null],
];
$labels = [
    'jaw' => T('HeroFace', 'headProfile'), 'eyes' => T('HeroFace', 'eyes'), 'brows' => T('HeroFace', 'eyebrow'),
    'nose' => T('HeroFace', 'nose'), 'mouth' => T('HeroFace', 'mouth'), 'ears' => T('HeroFace', 'ears'),
    'hair' => T('HeroFace', 'hairStyle'), 'beard' => T('HeroFace', 'beard'),
    'scar' => T('HeroV2', 'scar', 'Scar'), 'tattoo' => T('HeroV2', 'tattoo', 'Tattoo'),
];
$hairColors = ['black', 'brightbrown', 'brown', 'blonde', 'red'];
$config = [
    'faceData' => $vars['faceData'],
    'attributes' => $vars['attributes'],
    'categories' => $categories,
    'hairColors' => $hairColors,
    'base' => 'img/hero/',
];
?>
<div class="contentV2">
    <div class="appearanceWrapper" id="heroV2Appearance" data-config="<?=$e(json_encode($config)); ?>">
        <div class="categories">
            <?php foreach ($categories as $category => [$attribute]): ?>
                <div class="category <?=$category; ?><?=$category === 'jaw' ? ' selected' : ''; ?><?=$attribute === null ? ' disabled' : ''; ?>"
                     data-category="<?=$category; ?>" title="<?=$e($labels[$category]); ?>"><i></i></div>
            <?php endforeach; ?>
        </div>
        <div class="selectionWrapper">
            <div class="page page0 active"><div class="optionsWrapper"></div></div>
            <div class="previewWrapper">
                <div class="preview <?=$e($vars['attributes']['gender']); ?> zoomedIn"><div class="heroFaceLayers"><?=$vars['heroImage']; ?></div></div>
                <div class="actions">
                    <?=UI::button(UI::icon('0 0 21 21', '<path class="female" d="M21 .8v4.4a.8.8 0 0 1-.8.8h-.4a.8.8 0 0 1-.8-.8V3.42l-4 4a5.51 5.51 0 0 1-5.71 8.5 5.37 5.37 0 0 1-1.22-.43A6.46 6.46 0 0 0 10.2 14a3.36 3.36 0 0 0 1-.05A3.5 3.5 0 0 0 9.76 7.1a3.53 3.53 0 0 0-2.1 1.35 3.38 3.38 0 0 0-.59 1.36 3.41 3.41 0 0 0 0 1.49 2.44 2.44 0 0 1-1.07.58 2.62 2.62 0 0 1-.79 0l-.06-.21A5.52 5.52 0 0 1 9.37 5.1c.23 0 .46-.08.69-.1h.44a5.51 5.51 0 0 1 3.11 1l4-4H15.8a.8.8 0 0 1-.8-.8V.8a.8.8 0 0 1 .8-.8h4.4a.8.8 0 0 1 .8.8Z"/><path class="male" d="M10.82 8.11a2.62 2.62 0 0 0-.79 0 2.46 2.46 0 0 0-1.13.59v.11a3.51 3.51 0 0 1-2.77 4.12 3.41 3.41 0 0 1-1.35 0 2.79 2.79 0 0 1-.39-.1A3.5 3.5 0 0 1 5.8 6a6.55 6.55 0 0 1 2.09-1.45A5.58 5.58 0 0 0 5.41 4 5.57 5.57 0 0 0 0 9.22a5.49 5.49 0 0 0 4.5 5.68V17H3.3a.8.8 0 0 0-.8.8v.4a.8.8 0 0 0 .8.8h1.2v1.2a.8.8 0 0 0 .8.8h.4a.8.8 0 0 0 .8-.8V19h1.2a.8.8 0 0 0 .8-.8v-.4a.8.8 0 0 0-.8-.8H6.5v-2.1A5.53 5.53 0 0 0 11 9.5a5.41 5.41 0 0 0-.18-1.39Z"/>'),
                        ['icon' => true, 'class' => 'toggleGender ' . $vars['attributes']['gender'], 'title' => T('HeroFace', 'Gender')]); ?>
                    <?=UI::button(UI::icon('0 0 20 20', '<path d="M3 2h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1Zm2.5 2.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm9 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM10 8.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm-4.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Zm9 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>'),
                        ['icon' => true, 'class' => 'shuffleConfig', 'title' => T('HeroFace', 'random', T('HeroV2', 'random', 'Random'))]); ?>
                </div>
            </div>
            <div class="colorsWrapper" style="display:none">
                <div class="colorPalette"><?=$e(T('HeroFace', 'hairColor')); ?></div>
                <div class="colors hairColor">
                    <?php foreach ($hairColors as $i => $color): ?>
                        <div class="color <?=$color; ?>" data-color="<?=$i; ?>"><div class="colorPreview <?=$color; ?>"></div></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="buttonWrapper">
            <?=UI::button($e(T('HeroV2', 'buyOptions', 'Buy selected options')), ['appearance' => 'gold', 'class' => 'buyOptions', 'disabledClass' => true,
                'title' => T('HeroV2', 'noBuyOptions', 'All options of this world are free.')]); ?>
            <?=UI::button(UI::icon('0 0 20 20', '<path d="M10 3a7 7 0 1 1-6.6 9.3l1.9-.6A5 5 0 1 0 6.5 6.4L8.6 8.5H2.5V2.4l2.6 2.6A7 7 0 0 1 10 3Z"/>'),
                ['icon' => true, 'class' => 'resetChanges', 'disabledClass' => true, 'title' => T('HeroV2', 'resetChanges', 'Reset changes')]); ?>
            <?=UI::button(T('HeroInventory', 'save'), ['class' => 'saveChanges', 'disabledClass' => true]); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function () {
        var root = document.getElementById('heroV2Appearance');
        if (!root) return;
        var config = JSON.parse(root.dataset.config);
        var saved = Object.assign({}, config.attributes);
        var current = Object.assign({}, config.attributes);
        var category = 'jaw';
        var options = root.querySelector('.optionsWrapper');
        var preview = root.querySelector('.preview');
        var layers = root.querySelector('.heroFaceLayers');
        var colors = root.querySelector('.colorsWrapper');
        var resetButton = root.querySelector('.resetChanges'), saveButton = root.querySelector('.saveChanges');

        function rangeOf(attribute, gender) {
            return config.faceData[gender || current.gender][attribute];
        }

        function dirty() {
            return Object.keys(current).some(function (key) { return String(current[key]) !== String(saved[key]); });
        }

        function update() {
            var changed = dirty();
            [resetButton, saveButton].forEach(function (b) { b.classList.toggle('disabled', !changed); });
            root.querySelectorAll('.category').forEach(function (c) {
                var info = config.categories[c.dataset.category], range = info[0] ? rangeOf(info[0]) : null;
                c.classList.toggle('disabled', !range || range.end <= 0);
                c.classList.toggle('selected', c.dataset.category === category);
            });
            preview.className = 'preview ' + current.gender + ' zoomedIn';
            root.querySelector('.toggleGender').classList.remove('male', 'female');
            root.querySelector('.toggleGender').classList.add(current.gender);
            renderOptions();
        }

        function renderOptions() {
            var info = config.categories[category], attribute = info[0];
            options.innerHTML = '';
            if (!attribute) return;
            var range = rangeOf(attribute), ids = [];
            for (var id = range.start; id <= range.end; id++) ids.push(id);
            if (range.none > 0) ids.push(range.none);
            ids.forEach(function (id) {
                var option = document.createElement('div');
                var name = id === range.none ? 'None' : String(id - range.start);
                option.className = 'option option' + (id - range.start) + (String(current[attribute]) === String(id) ? ' selected' : '');
                option.dataset.option = id;
                option.innerHTML = '<div class="optionPreview ' + current.gender + ' ' + category + '"><img alt="" src="' + config.base
                    + current.gender + '/thumb/head/' + info[1] + '/' + info[2] + name + '.png"></div>'
                    + (String(current[attribute]) === String(id) ? '<div class="selectedIndicator"></div>' : '');
                options.appendChild(option);
            });
            var hair = ['hair', 'beard', 'brows'].indexOf(category) >= 0;
            colors.style.display = hair ? '' : 'none';
            var hairColor = parseInt(current.hairColor, 10) - rangeOf('hairColor').start;
            colors.querySelectorAll('.color').forEach(function (c) {
                var selected = parseInt(c.dataset.color, 10) === hairColor;
                c.classList.toggle('selected', selected);
                var indicator = c.querySelector('.selectedIndicator');
                if (selected && !indicator) c.insertAdjacentHTML('beforeend', '<div class="selectedIndicator"></div>');
                if (!selected && indicator) indicator.remove();
            });
        }

        function request(action, attribs, done) {
            root.classList.add('isLoading');
            Travian.ajax({
                data: {cmd: 'heroEditor', action: action, attribs: attribs},
                onSuccess: function (data) {
                    root.classList.remove('isLoading');
                    if (data && data.html) layers.innerHTML = data.html;
                    if (done) done(data || {});
                },
                onFailure: function () { root.classList.remove('isLoading'); }
            });
        }

        function show() {
            request('show', current);
            update();
        }

        root.addEventListener('click', function (event) {
            var c = event.target.closest('.category');
            if (c && !c.classList.contains('disabled')) {
                category = c.dataset.category;
                update();
                return;
            }
            var option = event.target.closest('.option');
            if (option) {
                current[config.categories[category][0]] = parseInt(option.dataset.option, 10);
                show();
                return;
            }
            var color = event.target.closest('.color');
            if (color) {
                current.hairColor = rangeOf('hairColor').start + parseInt(color.dataset.color, 10);
                show();
                return;
            }
            if (event.target.closest('.toggleGender')) {
                request('gender', {gender: current.gender === 'male' ? 'female' : 'male'}, function (data) {
                    if (data.attributes) current = Object.assign({uid: current.uid}, data.attributes);
                    if (category === 'beard' && current.gender === 'female') category = 'jaw';
                    update();
                });
                return;
            }
            if (event.target.closest('.shuffleConfig')) {
                request('random', {gender: current.gender}, function (data) {
                    if (data.attributes) current = Object.assign({uid: current.uid}, data.attributes);
                    update();
                });
                return;
            }
            if (event.target.closest('.resetChanges') && dirty()) {
                current = Object.assign({}, saved);
                show();
                return;
            }
            if (event.target.closest('.saveChanges') && dirty()) {
                request('save', current, function () { window.location.reload(); });
            }
        });
        update();
    })();
</script>
