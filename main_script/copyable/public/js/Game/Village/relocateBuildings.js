/*
 * Main Building → "Relocate buildings" (dorf2.php?relocate=1).
 *
 * Turns the village map into an editor: pick a building (tap it or drag it) and drop it on another building
 * site. An occupied site swaps both buildings, an empty one simply takes the building. Nothing is saved until
 * the player confirms; the form then posts the layout as placement[target slot] = source slot
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
    var backdrop = root.querySelector('.relocateBackdrop');

    var firstSlot = parseInt(root.getAttribute('data-first-slot'), 10);
    var lastSlot = parseInt(root.getAttribute('data-last-slot'), 10);
    var saveButton = root.querySelector('.relocateSave');
    var placementBox = root.querySelector('.relocatePlacement');
    var DRAG_THRESHOLD = 6;

    var buildings = {};   // original slot -> building element
    var occupant = {};    // current slot -> original slot of the building standing there
    var marks = {};       // slot -> site marker (ellipse under the building)
    var hits = {};        // slot -> transparent pointer target
    var slots = [];

    function slotClass(element, from, to) {
        element.classList.remove('a' + from);
        element.classList.add('a' + to);
    }

    function isEmpty(slot) {
        return buildings[occupant[slot]].getAttribute('data-gid') === '0';
    }

    function create(slot, className) {
        var element = document.createElement('div');
        element.className = 'buildingSlot a' + slot + ' ' + className;
        element.setAttribute('data-slot', String(slot));
        map.appendChild(element);
        return element;
    }

    for (var slot = firstSlot; slot <= lastSlot; ++slot) {
        var building = map.querySelector('.buildingSlot[data-aid="' + slot + '"]');
        if (!building) {
            continue;
        }
        slots.push(slot);
        buildings[slot] = building;
        occupant[slot] = slot;
        building.classList.add('relocatable');
        marks[slot] = create(slot, 'relocateMark');
        marks[slot].appendChild(document.createElement('span'));
        hits[slot] = create(slot, 'relocateHit');
    }
    if (!slots.length) {
        return;
    }

    var selected = null;   // slot whose building is picked up
    var hovered = null;    // slot under the pointer while something is picked up
    var drag = null;

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
    }

    function updateForm() {
        var inputs = [];
        slots.forEach(function (slot) {
            if (occupant[slot] !== slot) {
                inputs.push('<input type="hidden" name="placement[' + slot + ']" value="' + occupant[slot] + '"/>');
            }
        });
        placementBox.innerHTML = inputs.join('');
        saveButton.disabled = inputs.length === 0;
        saveButton.classList.toggle('disabled', inputs.length === 0);
    }

    function swap(from, to) {
        if (from === to || (isEmpty(from) && isEmpty(to))) {
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
    function slotAt(x, y) {
        var best = null;
        var bestDistance = Infinity;
        slots.forEach(function (slot) {
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

    function endDrag(event, cancelled) {
        var state = drag;
        drag = null;
        if (!state) {
            return;
        }
        var element = buildings[occupant[state.slot]];
        element.style.transform = '';
        element.classList.remove('relocateDragging');
        if (!state.moved) {
            // a tap: pick up, put down on another site, or drop the selection
            if (selected === null) {
                selected = isEmpty(state.slot) ? null : state.slot;
            } else if (state.slot === selected) {
                selected = null;
            } else {
                swap(selected, state.slot);
                selected = null;
            }
        } else {
            var target = cancelled ? null : slotAt(event.clientX, event.clientY);
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
        var slot = slotAt(event.clientX, event.clientY);
        if (slot === null) {
            return;
        }
        event.preventDefault();
        drag = {slot: slot, x: event.clientX, y: event.clientY, moved: false, pointer: event.pointerId};
        if (map.setPointerCapture) {
            try {
                map.setPointerCapture(event.pointerId);
            } catch (ignored) {
            }
        }
    });

    map.addEventListener('pointermove', function (event) {
        if (!drag) {
            if (selected !== null) {
                var over = slotAt(event.clientX, event.clientY);
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
            if (isEmpty(drag.slot)) {
                drag = null;
                return;
            }
            drag.moved = true;
            selected = drag.slot;
            buildings[occupant[drag.slot]].classList.add('relocateDragging');
        }
        var factor = scale();
        buildings[occupant[drag.slot]].style.transform = 'translate(' + (dx / factor) + 'px, ' + (dy / factor) + 'px)';
        var target = slotAt(event.clientX, event.clientY);
        if (target !== hovered) {
            hovered = target;
        }
        render();
    });

    map.addEventListener('pointerup', function (event) {
        endDrag(event, false);
    });
    map.addEventListener('pointercancel', function (event) {
        endDrag(event, true);
    });

    // Building links, level circles and tooltips stay quiet while the map is being edited.
    map.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
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

    root.querySelector('form').addEventListener('submit', function () {
        saveButton.disabled = true;
    });

    // Light the village itself: an ellipse around all building sites (works for every look of the map).
    function placeBackdrop() {
        var box = null;
        map.querySelectorAll('.buildingSlot.relocateMark, .buildingSlot.a39').forEach(function (element) {
            var rect = element.getBoundingClientRect();
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
        var rx = (box.right - box.left) / 2 * 1.32;
        var ry = (box.bottom - box.top) / 2 * 1.38;
        backdrop.style.background = 'radial-gradient(ellipse ' + rx + 'px ' + ry + 'px at ' + cx + 'px ' + cy + 'px, '
            + 'rgba(12, 14, 8, 0) 0, rgba(12, 14, 8, 0) 82%, rgba(12, 14, 8, 0.72) 100%)';
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
    window.addEventListener('scroll', schedulePlaceBackdrop, true);
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
