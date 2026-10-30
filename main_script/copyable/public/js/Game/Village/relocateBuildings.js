/*
 * Main Building → "Relocate buildings" (dorf2.php?relocate=1).
 *
 * Turns the village map into an editor: pick a building (tap it or drag it) and drop it on another building
 * site. An occupied site swaps both buildings, an empty one simply takes the building. The Main Building, the
 * rally point and the wall keep their places (tapping them says so). Nothing is saved until the player
 * confirms; the form then posts the layout as placement[target slot] = source slot
 * (see Game\Buildings\BuildingRelocation).
 */
(function () {
    'use strict';

    var root = document.getElementById('relocateBuildings');
    var wrapper = document.querySelector('.villageMapWrapper.relocateMode');
    var map = wrapper && wrapper.querySelector('#villageContent, #village_map');
    if (!root || !map) {
        return;
    }
    document.body.classList.add('relocatingBuildings');
    // The editor chrome lives at the top of the page: the map sits inside stacking contexts of the layout, so a
    // backdrop there could not cover the sidebars. The backdrop leaves an elliptic window over the village instead.
    document.body.appendChild(root);

    var RALLY_POINT_SLOT = 39;
    var DRAG_THRESHOLD = 6;
    var firstSlot = parseInt(root.getAttribute('data-first-slot'), 10);
    var lastSlot = parseInt(root.getAttribute('data-last-slot'), 10);
    var fixedGids = (root.getAttribute('data-fixed-gids') || '').split(',').filter(Boolean);
    var texts = {
        hint: root.querySelector('.relocateSubtitle').textContent,
        select: root.getAttribute('data-text-select') || '%s',
        fixed: root.getAttribute('data-text-fixed') || '',
        moveFirst: root.getAttribute('data-text-move-first') || ''
    };
    var backdrop = root.querySelector('.relocateBackdrop');
    var subtitle = root.querySelector('.relocateSubtitle');
    var saveButton = root.querySelector('.relocateSave');
    var placementBox = root.querySelector('.relocatePlacement');

    var buildings = {};   // original slot -> building element
    var occupant = {};    // current slot -> original slot of the building standing there
    var marks = {};       // slot -> site marker (ellipse under the building)
    var hits = {};        // slot -> transparent pointer target (also for the fixed rally point)
    var slots = [];       // sites that take part in the relocation
    var hitSlots = [];    // every site with a pointer target
    var changed = 0;

    function slotClass(element, from, to) {
        element.classList.remove('a' + from);
        element.classList.add('a' + to);
    }

    function create(slot, className) {
        var element = document.createElement('div');
        element.className = 'buildingSlot a' + slot + ' ' + className;
        element.setAttribute('data-slot', String(slot));
        map.appendChild(element);
        return element;
    }

    function isEmpty(slot) {
        return buildings[occupant[slot]].getAttribute('data-gid') === '0';
    }

    function isFixed(slot) {
        return slots.indexOf(slot) < 0;
    }

    for (var slot = firstSlot; slot <= RALLY_POINT_SLOT; ++slot) {
        var building = map.querySelector('.buildingSlot[data-aid="' + slot + '"]');
        if (!building) {
            continue;
        }
        buildings[slot] = building;
        occupant[slot] = slot;
        hitSlots.push(slot);
        hits[slot] = create(slot, 'relocateHit');
        var fixed = slot > lastSlot || fixedGids.indexOf(building.getAttribute('data-gid')) >= 0;
        if (fixed) {
            building.classList.add('relocateFixed');
            continue;
        }
        slots.push(slot);
        building.classList.add('relocatable');
        marks[slot] = create(slot, 'relocateMark');
        marks[slot].appendChild(document.createElement('span'));
    }
    if (!slots.length) {
        return;
    }

    var selected = null;   // slot whose building is picked up
    var hovered = null;    // site under the pointer while something is picked up
    var drag = null;

    // ------------------------------------------------------------------ messages

    var tip = document.createElement('div');
    tip.className = 'relocateTip';
    root.appendChild(tip);
    var tipTimer = null;

    function showTip(text, x, y, above) {
        if (!text) {
            return;
        }
        tip.textContent = text;
        tip.classList.toggle('above', !!above);
        tip.style.left = Math.round(x) + 'px';
        tip.style.top = Math.round(y) + 'px';
        tip.classList.add('shown');
        window.clearTimeout(tipTimer);
        tipTimer = window.setTimeout(function () {
            tip.classList.remove('shown');
        }, 2600);
    }

    function hideTip() {
        window.clearTimeout(tipTimer);
        tip.classList.remove('shown');
    }

    function tipAtSlot(text, slot) {
        var rect = hits[slot].getBoundingClientRect();
        showTip(text, rect.left + rect.width / 2, rect.top + rect.height * 0.7);
    }

    function updateSubtitle() {
        subtitle.textContent = '';
        if (selected === null) {
            subtitle.textContent = texts.hint;
            return;
        }
        var element = buildings[occupant[selected]];
        // "Now choose a new position for %s (level %s)" with the building name highlighted
        var parts = texts.select.split('%s');
        subtitle.appendChild(document.createTextNode(parts[0] || ''));
        var name = document.createElement('span');
        name.className = 'relocateBuildingName';
        name.textContent = element.getAttribute('data-name') || '';
        subtitle.appendChild(name);
        subtitle.appendChild(document.createTextNode((parts[1] || '') + (element.getAttribute('data-level') || '') + (parts.slice(2).join('%s'))));
    }

    // ------------------------------------------------------------------ state

    function render() {
        map.classList.toggle('relocateSelecting', selected !== null);
        slots.forEach(function (slot) {
            var element = buildings[occupant[slot]];
            element.classList.toggle('relocateSelected', slot === selected);
            element.classList.toggle('relocateEmpty', isEmpty(slot));
            marks[slot].classList.toggle('origin', slot === selected);
            marks[slot].classList.toggle('target', selected !== null && slot === hovered && slot !== selected);
            marks[slot].classList.toggle('free', isEmpty(slot));
        });
        updateSubtitle();
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
        saveButton.classList.toggle('disabled', changed === 0);
        saveButton.setAttribute('aria-disabled', changed === 0 ? 'true' : 'false');
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
        slotClass(buildings[moving], from, to);
        slotClass(buildings[staying], to, from);
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
            tipAtSlot(texts.fixed, slot);
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

    function endDrag(event, cancelled) {
        var state = drag;
        drag = null;
        if (!state) {
            return;
        }
        if (!state.moved) {
            if (!cancelled) {
                tap(state.slot);
            }
        } else {
            var element = buildings[occupant[state.slot]];
            element.style.transform = '';
            element.classList.remove('relocateDragging');
            var target = cancelled ? null : slotAt(event.clientX, event.clientY, true);
            if (target !== null && target !== state.slot) {
                swap(state.slot, target);
            }
            selected = null;
        }
        hovered = null;
        render();
    }

    map.addEventListener('pointerdown', function (event) {
        if (event.button > 0 || !event.target.classList || !event.target.classList.contains('relocateHit')) {
            return;
        }
        // the hit boxes of neighbouring sites overlap, so the nearest site wins rather than the topmost box
        var slot = slotAt(event.clientX, event.clientY, false);
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

    map.addEventListener('pointermove', function (event) {
        if (!drag) {
            if (selected !== null) {
                var over = slotAt(event.clientX, event.clientY, true);
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
                // a fixed building does not come along; the tap message explains why on release
                return;
            }
            drag.moved = true;
            selected = drag.slot;
            hideTip();
            buildings[occupant[drag.slot]].classList.add('relocateDragging');
        }
        var factor = scale();
        buildings[occupant[drag.slot]].style.transform = 'translate(' + (dx / factor) + 'px, ' + (dy / factor) + 'px)';
        hovered = slotAt(event.clientX, event.clientY, true);
        render();
    });

    map.addEventListener('pointerup', function (event) {
        endDrag(event, false);
    });
    map.addEventListener('pointercancel', function (event) {
        endDrag(event, true);
    });

    // Building links, level circles and tooltips stay quiet while the map is being edited; the wall says it
    // stays where it is.
    map.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        var wall = event.target.closest && event.target.closest('.buildingSlot.a40');
        if (wall) {
            showTip(texts.fixed, event.clientX, event.clientY + 12);
        }
    }, true);
    map.addEventListener('dragstart', function (event) {
        event.preventDefault();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }
        if (selected !== null) {
            selected = null;
            hovered = null;
            render();
        } else {
            window.location.href = root.querySelector('.relocateClose').getAttribute('href');
        }
    });

    var submitting = false;
    saveButton.addEventListener('click', function (event) {
        if (!changed || submitting) {
            event.preventDefault();
            if (!changed) {
                var rect = saveButton.getBoundingClientRect();
                // the button sits at the bottom of the screen, so the message goes above it
                showTip(texts.moveFirst, rect.left + rect.width / 2, rect.top - 8, true);
            }
            return;
        }
        submitting = true;
    });

    // ------------------------------------------------------------------ backdrop

    // Light the village itself: an ellipse around all building sites (works for every look of the map).
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
        backdrop.style.background = 'radial-gradient(ellipse ' + rx + 'px ' + ry + 'px at ' + cx + 'px ' + cy + 'px, '
            + 'rgba(0, 0, 0, 0.1) 0, rgba(0, 0, 0, 0.16) 78%, rgba(0, 0, 0, 0.92) 100%)';
    }
    var placing = false;
    function schedulePlaceBackdrop() {
        if (!placing) {
            placing = true;
            window.requestAnimationFrame(function () {
                placing = false;
                placeBackdrop();
            });
        }
    }
    window.addEventListener('resize', schedulePlaceBackdrop);
    window.addEventListener('scroll', function () {
        schedulePlaceBackdrop();
        hideTip();
    }, true);
    window.addEventListener('load', schedulePlaceBackdrop);

    // Outside the village nothing reacts while relocating (the backdrop lets the pointer through to the map).
    ['click', 'pointerdown', 'mousedown', 'touchstart'].forEach(function (type) {
        document.addEventListener(type, function (event) {
            if (!map.contains(event.target) && !root.contains(event.target)) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, {capture: true, passive: false});
    });

    render();
    updateForm();
    placeBackdrop();
})();
