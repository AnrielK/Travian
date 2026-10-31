/*
 * Main Building → "Relocate buildings" (dorf2.php?mode=rearrange), modelled on the Travian Legends rearrange mode.
 *
 * The markup is the Legends one (.rearrangeBuildings with .villageMask, instructions step1 / step2 / dragAndDrop,
 * #villageContent.rearrangeMode with .movable / .selected / .dragged / .dropTarget slots and .selectTarget /
 * .dragMode on the map), so the Legends gpack (legends_ltr.css) styles it as in the original; looks without those
 * styles get the fallback of openvillage.css (.relocateFallback).
 *
 * Pick a building (tap it or drag it) and drop it on another building site: an occupied site swaps both buildings,
 * an empty one takes the building. The Main Building, the rally point and the wall keep their places. Nothing is
 * saved until the player confirms; the form then posts placement[target slot] = source slot
 * (see Game\Buildings\BuildingRelocation).
 */
(function () {
    'use strict';

    var root = document.getElementById('relocateBuildings');
    var map = document.querySelector('#villageContent.rearrangeMode, #village_map.rearrangeMode');
    if (!root || !map) {
        return;
    }

    var RALLY_POINT_SLOT = 39;
    var DRAG_THRESHOLD = 6;
    var data = function (name) {
        return root.getAttribute('data-' + name) || '';
    };
    var firstSlot = parseInt(data('first-slot'), 10);
    var lastSlot = parseInt(data('last-slot'), 10);
    var fixedGids = data('fixed-gids').split(',').filter(Boolean);
    var container = root.querySelector('.container');
    var steps = {
        step1: root.querySelector('.step.step1'),
        step2: root.querySelector('.step.step2'),
        dragAndDrop: root.querySelector('.step.dragAndDrop')
    };
    var templates = {step2: steps.step2.innerHTML, dragAndDrop: steps.dragAndDrop.innerHTML};
    var confirmButton = root.querySelector('button.confirm');
    var closeLink = root.querySelector('.close');
    var form = root.querySelector('form.relocateForm');
    var placementBox = root.querySelector('.relocatePlacement');

    document.body.classList.add('rearrangeBuildings');
    // Without the Legends styles the editor uses the fallback look: chrome fixed on top of the page and an
    // elliptic window over the village in place of the Legends village mask.
    var mask = root.querySelector('.villageMask');
    var fallback = window.getComputedStyle(mask).backgroundImage === 'none';
    if (fallback) {
        root.classList.add('relocateFallback');
    }
    // The map sits inside stacking contexts of the layout, so the overlay goes to the top of the page to cover the
    // sidebars and the top bar as well.
    document.body.appendChild(root);
    window.requestAnimationFrame(function () {
        root.classList.add('show');
    });

    // ------------------------------------------------------------------ slots

    var buildings = {};   // original slot -> building element
    var occupant = {};    // current slot -> original slot of the building standing there
    var marks = {};       // slot -> orange plot shown under a building that is being dragged away
    var hits = {};        // slot -> transparent pointer target (also for the fixed buildings)
    var slots = [];       // sites that take part in the relocation
    var hitSlots = [];    // every site with a pointer target
    var changed = 0;

    function create(slot, className) {
        var element = document.createElement('div');
        element.className = 'buildingSlot a' + slot + ' ' + className;
        element.setAttribute('data-slot', String(slot));
        map.appendChild(element);
        return element;
    }

    // The plot a dragged building leaves behind is drawn with the shape of an empty building site, so it has the
    // same size as the highlighted drop target.
    var emptyShape = map.querySelector('.buildingSlot.g0 svg.buildingShape.iso');

    for (var slot = firstSlot; slot <= RALLY_POINT_SLOT; ++slot) {
        var building = map.querySelector('.buildingSlot[data-aid="' + slot + '"]');
        if (!building) {
            continue;
        }
        buildings[slot] = building;
        occupant[slot] = slot;
        hitSlots.push(slot);
        hits[slot] = create(slot, 'relocateHit');
        if (slot > lastSlot || fixedGids.indexOf(building.getAttribute('data-gid')) >= 0) {
            hits[slot].classList.add('fixed');
            continue;
        }
        slots.push(slot);
        building.classList.add('movable');
        marks[slot] = create(slot, 'relocateMark');
        marks[slot].appendChild(emptyShape ? emptyShape.cloneNode(true) : document.createElement('span'));
    }
    if (!slots.length) {
        return;
    }

    var dragContainer = document.createElement('div');
    dragContainer.className = 'dragContainer buildingSlot';
    map.appendChild(dragContainer);

    function isEmpty(slot) {
        return buildings[occupant[slot]].getAttribute('data-gid') === '0';
    }

    function isFixed(slot) {
        return slots.indexOf(slot) < 0;
    }

    function shape(slot) {
        return buildings[occupant[slot]].querySelector('svg.buildingShape');
    }

    // ------------------------------------------------------------------ messages

    var tip = document.createElement('div');
    tip.className = 'relocateTip';
    document.body.appendChild(tip);
    var tipTimer = null;

    function showTip(text, x, y, above) {
        if (!text) {
            return;
        }
        tip.innerHTML = '<span class="notice"></span>';
        tip.firstChild.textContent = text;
        tip.classList.toggle('above', !!above);
        tip.style.left = Math.round(x) + 'px';
        tip.style.top = Math.round(y) + 'px';
        tip.classList.add('shown');
        window.clearTimeout(tipTimer);
        tipTimer = window.setTimeout(hideTip, 2600);
    }

    function hideTip() {
        window.clearTimeout(tipTimer);
        tip.classList.remove('shown');
    }

    function tipAtSlot(text, slot) {
        var rect = hits[slot].getBoundingClientRect();
        showTip(text, rect.left + rect.width / 2, rect.top + rect.height * 0.7);
    }

    function escapeHtml(text) {
        var span = document.createElement('span');
        span.textContent = text;
        return span.innerHTML;
    }

    function fill(template, slot) {
        var element = buildings[occupant[slot]];
        return template
            .split('[NAME]').join('<strong>' + escapeHtml(element.getAttribute('data-name') || '') + '</strong>')
            .split('[LEVEL]').join(escapeHtml(element.getAttribute('data-level') || ''));
    }

    function showStep(name, slot) {
        if (name !== 'step1') {
            steps[name].innerHTML = fill(templates[name], slot);
        }
        Object.keys(steps).forEach(function (key) {
            steps[key].classList.toggle('active', key === name);
        });
    }

    // ------------------------------------------------------------------ state

    var selected = null;   // slot whose building is picked up
    var hovered = null;    // site under the pointer while something is picked up
    var pointed = null;    // building under the pointer while nothing is picked up ('wall' for the wall)
    var drag = null;
    var walls = map.querySelectorAll('.buildingSlot.a40');

    function render() {
        var dragging = !!(drag && drag.moved);
        map.classList.toggle('selectTarget', selected !== null && !dragging);
        map.classList.toggle('dragMode', dragging);
        hitSlots.forEach(function (slot) {
            var element = buildings[occupant[slot]];
            var target = selected !== null && slot === hovered && slot !== selected && !isFixed(slot);
            element.classList.toggle('selected', slot === selected);
            element.classList.toggle('dragged', dragging && slot === selected);
            element.classList.toggle('dropTarget', target);
            var svg = shape(slot);
            if (svg) {
                svg.classList.toggle('hover', target);
            }
            if (marks[slot]) {
                marks[slot].classList.toggle('origin', dragging && slot === selected);
            }
            // hover glow of the village view (openvillage-t46.css), which the pointer layer would swallow
            element.classList.toggle('relocateHover', selected === null && slot === pointed);
        });
        Array.prototype.forEach.call(walls, function (wall) {
            wall.classList.toggle('relocateHover', selected === null && pointed === 'wall');
        });
        if (selected === null) {
            showStep('step1');
        } else {
            showStep(dragging ? 'dragAndDrop' : 'step2', selected);
        }
    }

    function updateForm() {
        var inputs = [];
        slots.forEach(function (slot) {
            if (occupant[slot] !== slot) {
                inputs.push('<input type="hidden" name="placement[' + slot + ']" value="' + occupant[slot] + '"/>');
            }
        });
        placementBox.innerHTML = inputs.join('');
        changed = inputs.length;
        confirmButton.classList.toggle('disabled', changed === 0);
        if (changed) {
            hideTip();
        }
    }

    function swap(from, to) {
        if (from === to || isFixed(from) || isFixed(to) || (isEmpty(from) && isEmpty(to))) {
            return;
        }
        var moving = occupant[from];
        var staying = occupant[to];
        buildings[moving].classList.remove('a' + from);
        buildings[moving].classList.add('a' + to);
        buildings[staying].classList.remove('a' + to);
        buildings[staying].classList.add('a' + from);
        occupant[to] = moving;
        occupant[from] = staying;
        updateForm();
    }

    function scale() {
        var rect = map.getBoundingClientRect();
        return map.offsetWidth ? rect.width / map.offsetWidth : 1;
    }

    // The site nearest to the pointer (sites overlap a little, so the plain hit box is not precise enough).
    function slotAt(x, y, movableOnly) {
        var best = null;
        var bestDistance = Infinity;
        (movableOnly ? slots : hitSlots).forEach(function (slot) {
            var rect = hits[slot].getBoundingClientRect();
            var dx = x - (rect.left + rect.width / 2);
            var dy = y - (rect.top + rect.height * 0.6);
            var distance = dx * dx + dy * dy;
            var reach = Math.max(rect.width, rect.height) * 0.75;
            if (distance < bestDistance && distance <= reach * reach) {
                best = slot;
                bestDistance = distance;
            }
        });
        return best;
    }

    // ------------------------------------------------------------------ pointer

    function tap(slot) {
        if (isFixed(slot)) {
            tipAtSlot(data('text-fixed'), slot);
            return;
        }
        hideTip();
        if (selected === null) {
            selected = isEmpty(slot) ? null : slot;
        } else if (slot === selected) {
            selected = null;
        } else {
            swap(selected, slot);
            selected = null;
        }
    }

    function startDrag(slot) {
        var source = buildings[occupant[slot]];
        dragContainer.className = 'dragContainer buildingSlot enabled a' + slot + ' ' + Array.prototype.filter.call(source.classList, function (name) {
            return /^g\d+$/.test(name);
        }).join(' ');
        dragContainer.innerHTML = '';
        // the Legends styles pin the drag container to the corner (the original script moves it), so it starts
        // on the building's own site
        dragContainer.style.left = source.offsetLeft + 'px';
        dragContainer.style.top = source.offsetTop + 'px';
        ['img', 'svg.buildingShape'].forEach(function (selector) {
            var part = source.querySelector(selector);
            if (part) {
                dragContainer.appendChild(part.cloneNode(true));
            }
        });
    }

    function stopDrag() {
        dragContainer.className = 'dragContainer buildingSlot';
        dragContainer.innerHTML = '';
        dragContainer.style.transform = '';
    }

    function endDrag(event, cancelled) {
        var state = drag;
        if (!state) {
            return;
        }
        if (!state.moved) {
            drag = null;
            if (!cancelled) {
                tap(state.slot);
            }
        } else {
            var target = cancelled ? null : targetAt(event.clientX, event.clientY);
            drag = null;
            stopDrag();
            if (target !== null && target !== state.slot) {
                swap(state.slot, target);
            }
            selected = null;
        }
        hovered = null;
        render();
    }

    map.addEventListener('pointerdown', function (event) {
        if (event.button > 0) {
            return;
        }
        var slot = buildingAt(event.clientX, event.clientY);
        if (slot === 'wall') {
            return;   // the click handler explains that the wall stays
        }
        if (slot === null) {
            if (!event.target.classList || !event.target.classList.contains('relocateHit')) {
                return;
            }
            slot = slotAt(event.clientX, event.clientY, false);
        }
        if (slot === null) {
            return;
        }
        event.preventDefault();
        drag = {slot: slot, x: event.clientX, y: event.clientY, moved: false};
        try {
            map.setPointerCapture(event.pointerId);
        } catch (ignored) {
        }
    });

    // Is the point inside the outline of a building (its click / hover shapes, current season only)?
    function inShape(svg, x, y) {
        if (!svg) {
            return false;
        }
        var paths = svg.querySelectorAll('path');
        for (var i = 0; i < paths.length; ++i) {
            var path = paths[i];
            if (!path.isPointInFill || !path.getScreenCTM || path.closest('g') && window.getComputedStyle(path.closest('g')).display === 'none') {
                continue;
            }
            var matrix = path.getScreenCTM();
            if (!matrix) {
                continue;
            }
            var point = new DOMPoint(x, y).matrixTransform(matrix.inverse());
            if (path.isPointInFill(point)) {
                return true;
            }
        }
        return false;
    }

    // The building under the pointer by its outline; the wall when the pointer is on the wall.
    function buildingAt(x, y) {
        for (var i = hitSlots.length - 1; i >= 0; --i) {
            var slot = hitSlots[i];
            if (!isEmpty(slot) && inShape(shape(slot), x, y)) {
                return slot;
            }
        }
        for (var w = 0; w < walls.length; ++w) {
            if (inShape(walls[w].querySelector('svg.buildingShape'), x, y)) {
                return 'wall';
            }
        }
        return null;
    }

    // Where a picked building would go: the building under the pointer by its outline (none over the fixed ones and
    // the wall), otherwise the nearest building site.
    function targetAt(x, y) {
        var building = buildingAt(x, y);
        if (building !== null) {
            return building === 'wall' || isFixed(building) ? null : building;
        }
        return slotAt(x, y, true);
    }

    function pointAt(event) {
        var slot = buildingAt(event.clientX, event.clientY);
        if (slot !== pointed) {
            pointed = slot;
            render();
        }
    }

    map.addEventListener('pointerleave', function () {
        if (pointed !== null) {
            pointed = null;
            render();
        }
    });

    map.addEventListener('pointermove', function (event) {
        if (!drag) {
            if (selected === null) {
                pointAt(event);
            } else {
                var over = targetAt(event.clientX, event.clientY);
                if (over !== hovered) {
                    hovered = over;
                    render();
                }
            }
            return;
        }
        var dx = event.clientX - drag.x;
        var dy = event.clientY - drag.y;
        if (!drag.moved) {
            if (Math.abs(dx) < DRAG_THRESHOLD && Math.abs(dy) < DRAG_THRESHOLD) {
                return;
            }
            if (isFixed(drag.slot) || isEmpty(drag.slot)) {
                return;   // a fixed building stays; the tap message explains why on release
            }
            drag.moved = true;
            selected = drag.slot;
            hideTip();
            startDrag(drag.slot);
        }
        var factor = scale();
        dragContainer.style.transform = 'translate(' + (dx / factor) + 'px, ' + (dy / factor) + 'px)';
        hovered = targetAt(event.clientX, event.clientY);
        render();
    });

    map.addEventListener('pointerup', function (event) {
        endDrag(event, false);
    });
    map.addEventListener('pointercancel', function (event) {
        endDrag(event, true);
    });

    // Building links, level circles and tooltips stay quiet while rearranging; the wall says it stays in place.
    map.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (!drag && buildingAt(event.clientX, event.clientY) === 'wall') {
            showTip(data('text-fixed'), event.clientX, event.clientY + 12);
        }
    }, true);
    map.addEventListener('dragstart', function (event) {
        event.preventDefault();
    });

    // ------------------------------------------------------------------ confirm / abort

    var leaving = false;
    window.addEventListener('beforeunload', function (event) {
        if (changed && !leaving) {
            event.preventDefault();
            event.returnValue = data('text-unload');
            return event.returnValue;
        }
    });

    function leave(url) {
        leaving = true;
        window.location.href = url;
    }

    function button(text, cls) {
        return '<button type="button" class="textButtonV1 ' + cls + '" version="textButtonV1">' + escapeHtml(text) + '</button>';
    }

    var CLOSE_ICON = '<svg viewBox="0 0 20 20"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"></path></svg>';
    var openDialog = null;

    // The Legends rearrange dialogs: a plain white box, "cancel rearrangement?" at the top of the screen, the
    // confirmation over the confirm button. place(dialog) positions it once it is in the page.
    function dialog(cls, title, text, buttons, closable, place, onOpen) {
        closeDialog();
        var overlay = document.createElement('div');
        overlay.className = 'relocateDialogOverlay';
        if (fallback) {
            overlay.classList.add('plain');
            overlay.innerHTML = '<div class="relocateDialog ' + cls + '">'
                + (closable ? '<a class="relocateDialogClose" href="#" title="' + escapeHtml(data('text-cancel')) + '">' + CLOSE_ICON + '</a>' : '')
                + '<h2>' + escapeHtml(title) + '</h2><p>' + text + '</p><div class="relocateDialogButtons">' + buttons + '</div></div>';
        } else {
            // the Legends markup of these dialogs (dialogV1), styled by legends_ltr.css
            var legends = cls === 'proceedDialog'
                ? {context: 'rearrangeBuildingsConfirm', dialog: 'rearrangeBuildingsConfirmDialog', buttons: 'rearrangeBuildingsButtons'}
                : {context: 'rearrangeBuildingsAbort', dialog: 'rearrangeBuildingsAbortDialog', buttons: 'decisionButtons'};
            overlay.innerHTML = '<div class="dialogWrapper dialogV1 relocateDialog ' + cls + '" data-context="' + legends.context + '">'
                + '<div class="dialog ' + legends.dialog + '"><div class="dialogContainer"><div class="dialogContents">'
                + '<form action="#" onsubmit="return false;"><div class="dialogDragBar"></div>'
                + (closable ? '<div class="dialogCancelButton smallIconButton cancel relocateDialogClose" title="' + escapeHtml(data('text-cancel')) + '"></div>' : '')
                + '<div class="content" id="dialogContent"><h1>' + escapeHtml(title) + '</h1><p>' + text + '</p>'
                + '<div class="' + legends.buttons + '">' + buttons + '</div></div>'
                + '</form></div></div></div></div>';
        }
        document.body.appendChild(overlay);
        var box = overlay.firstChild;
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                closeDialog();
            }
        });
        var close = box.querySelector('.relocateDialogClose');
        if (close) {
            close.addEventListener('click', function (event) {
                event.preventDefault();
                closeDialog();
            });
        }
        box.querySelector('.cancelAction').addEventListener('click', closeDialog);
        place(box);
        openDialog = overlay;
        onOpen(box);
        window.requestAnimationFrame(function () {
            overlay.classList.add('shown');
        });
    }

    function closeDialog() {
        if (openDialog && openDialog.parentNode) {
            openDialog.parentNode.removeChild(openDialog);
        }
        openDialog = null;
    }

    function atTop(box) {
        box.style.top = '12px';
    }

    function overConfirmButton(box) {
        var rect = confirmButton.getBoundingClientRect();
        var height = box.offsetHeight;
        var top = rect.top + rect.height / 2 - height / 2;
        box.style.top = Math.max(12, Math.min(window.innerHeight - height - 12, top)) + 'px';
    }

    function proceed() {
        var village = '<strong>' + escapeHtml(data('village-name')) + '</strong>';
        var text = escapeHtml(data('text-proceed')).split('[VILLAGE]').join(village);
        dialog('proceedDialog', data('text-title'), text,
            button(data('text-title'), 'gold proceedAction') + button(data('text-cancel'), 'grey cancelAction'),
            true, overConfirmButton,
            function (box) {
                box.querySelector('.proceedAction').addEventListener('click', submit);
            });
    }

    function submit() {
        leaving = true;
        closeDialog();
        form.submit();
    }

    function abort() {
        var url = closeLink.getAttribute('href');
        if (!changed) {
            leave(url);
            return;
        }
        dialog('abortDialog', data('text-abort-title'), escapeHtml(data('text-abort')),
            button(data('text-abort-confirm'), 'green abortAction') + button(data('text-cancel'), 'grey cancelAction'),
            false, atTop,
            function (box) {
                box.querySelector('.abortAction').addEventListener('click', function () {
                    leave(url);
                });
            });
    }

    closeLink.addEventListener('click', function (event) {
        event.preventDefault();
        abort();
    });

    function moveFirstTip() {
        var rect = confirmButton.getBoundingClientRect();
        showTip(data('text-move-first'), rect.left + rect.width / 2, rect.top - 8, true);
    }

    confirmButton.addEventListener('mouseenter', function () {
        if (!changed) {
            moveFirstTip();
        }
    });
    confirmButton.addEventListener('mouseleave', function () {
        if (!changed) {
            hideTip();
        }
    });
    confirmButton.addEventListener('click', function (event) {
        event.preventDefault();
        if (!changed) {
            moveFirstTip();
            return;
        }
        proceed();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }
        if (openDialog) {
            closeDialog();
            return;
        }
        if (selected !== null) {
            selected = null;
            hovered = null;
            render();
        } else {
            abort();
        }
    });

    // ------------------------------------------------------------------ village mask

    // Legends village mask (villageMask.png, 4029 × 1509): black at 85 % with an elliptic window (centre
    // 2015 / 433.5). As in Legends the picture is centred on the village map (1280 × 720) and starts at its top, so
    // the window sits a little below the middle of the walls; a shadow continues the black beyond the picture.
    var MASK_CENTER_X = 2014.5;
    var MASK_CENTER_Y = 433.5;
    var VILLAGE_CENTER_X = 640;
    var VILLAGE_CENTER_Y = 433.5;

    function placeMask() {
        var page = document.documentElement;
        root.style.height = Math.max(page.scrollHeight, document.body.scrollHeight, window.innerHeight) + 'px';
        // the page can be wider than <body> (phones show the whole desktop layout)
        root.style.width = Math.max(page.scrollWidth, document.body.scrollWidth, window.innerWidth) + 'px';
        if (fallback) {
            placeBackdrop();
            return;
        }
        var rect = map.getBoundingClientRect();
        var factor = scale();
        var left = rect.left + window.pageXOffset + (VILLAGE_CENTER_X - MASK_CENTER_X) * factor;
        var top = rect.top + window.pageYOffset + (VILLAGE_CENTER_Y - MASK_CENTER_Y) * factor;
        mask.style.transform = 'translate(' + left + 'px, ' + top + 'px) scale(' + factor + ')';
        container.style.top = Math.max(0, rect.top + window.pageYOffset) + 'px';
    }

    function placeBackdrop() {
        var box = null;
        hitSlots.forEach(function (slot) {
            var rect = hits[slot].getBoundingClientRect();
            if (!rect.width) {
                return;
            }
            box = box ? {
                left: Math.min(box.left, rect.left),
                top: Math.min(box.top, rect.top),
                right: Math.max(box.right, rect.right),
                bottom: Math.max(box.bottom, rect.bottom)
            } : {left: rect.left, top: rect.top, right: rect.right, bottom: rect.bottom};
        });
        if (!box) {
            return;
        }
        var cx = (box.left + box.right) / 2;
        var cy = (box.top + box.bottom) / 2;
        var rx = (box.right - box.left) / 2 * 1.16;
        var ry = (box.bottom - box.top) / 2 * 1.24;
        mask.style.background = 'radial-gradient(ellipse ' + rx + 'px ' + ry + 'px at ' + cx + 'px ' + cy + 'px, '
            + 'rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.16) 78%, rgba(0, 0, 0, 0.92) 100%)';
    }
    var placing = false;
    var schedule = function () {
        if (!placing) {
            placing = true;
            window.requestAnimationFrame(function () {
                placing = false;
                placeMask();
            });
        }
    };
    window.addEventListener('resize', schedule);
    window.addEventListener('scroll', schedule, true);
    window.addEventListener('load', schedule);
    placeMask();
    window.addEventListener('scroll', hideTip, true);

    // Outside the village nothing reacts while rearranging (dialogs aside).
    ['click', 'pointerdown', 'mousedown', 'touchstart'].forEach(function (type) {
        document.addEventListener(type, function (event) {
            var target = event.target;
            if (map.contains(target) || container.contains(target)
                || (target.closest && target.closest('.relocateDialogOverlay'))) {
                return;
            }
            event.preventDefault();
            event.stopPropagation();
        }, {capture: true, passive: false});
    });

    render();
    updateForm();
})();
