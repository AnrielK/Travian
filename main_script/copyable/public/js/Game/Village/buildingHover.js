/*
 * Village centre (dorf2.php): the hover glow of a building wherever the pointer is on it, not only on its level
 * circle. The browser's :hover on the building outlines is unreliable (Safari, outlines lying on top of each
 * other, the pointer layer of the rearrange mode), so the building under the pointer is found by its outline
 * (isPointInFill) and gets .ovHover; openvillage.css draws the glow.
 *
 * The wall picture also holds the bridges at the gates, so the wall does not glow as a whole: a copy of its pictures,
 * cut to the ellipse of the wall ring, lights up instead.
 */
(function () {
    'use strict';

    var map = document.querySelector('#villageContent, #village_map');
    if (!map) {
        return;
    }

    function visible(element) {
        var group = element.closest('g');
        return !group || window.getComputedStyle(group).display !== 'none';
    }

    function inShape(svg, x, y) {
        if (!svg) {
            return false;
        }
        var paths = svg.querySelectorAll('path');
        for (var i = 0; i < paths.length; ++i) {
            var path = paths[i];
            if (!path.isPointInFill || !visible(path)) {
                continue;
            }
            var matrix = path.getScreenCTM();
            if (matrix && path.isPointInFill(new DOMPoint(x, y).matrixTransform(matrix.inverse()))) {
                return true;
            }
        }
        return false;
    }

    function walls() {
        return map.querySelectorAll('.buildingSlot.a40');
    }

    // The building under the point: the front-most building whose outline contains it, else the wall.
    function buildingAt(x, y) {
        var best = null;
        var bestZ = -Infinity;
        var slots = map.querySelectorAll('.buildingSlot[data-aid]');
        for (var i = 0; i < slots.length; ++i) {
            var slot = slots[i];
            if (slot.getAttribute('data-gid') === '0') {
                continue;
            }
            if (inShape(slot.querySelector('svg.buildingShape'), x, y)) {
                var z = parseInt(window.getComputedStyle(slot).zIndex, 10) || 0;
                if (z >= bestZ) {
                    best = slot;
                    bestZ = z;
                }
            }
        }
        if (best) {
            return best;
        }
        var wallSlots = walls();
        for (var w = 0; w < wallSlots.length; ++w) {
            if (!wallSlots[w].classList.contains('g0') && inShape(wallSlots[w].querySelector('svg.buildingShape'), x, y)) {
                return 'wall';
            }
        }
        return null;
    }

    // glow copies of the wall pictures, cut to the wall ring (see openvillage.css .wallGlow)
    Array.prototype.forEach.call(map.querySelectorAll('.buildingSlot.a40:not(.g0) img.wall'), function (img) {
        var glow = img.cloneNode(false);
        glow.classList.add('wallGlow');
        glow.removeAttribute('title');
        glow.style.left = img.offsetLeft + 'px';
        glow.style.top = img.offsetTop + 'px';
        img.parentNode.insertBefore(glow, img.nextSibling);
    });

    var current = null;

    function set(target) {
        if (target === current) {
            return;
        }
        if (current === 'wall') {
            Array.prototype.forEach.call(walls(), function (wall) {
                wall.classList.remove('ovHover');
            });
        } else if (current) {
            current.classList.remove('ovHover');
        }
        current = target;
        if (current === 'wall') {
            Array.prototype.forEach.call(walls(), function (wall) {
                wall.classList.add('ovHover');
            });
        } else if (current) {
            current.classList.add('ovHover');
        }
    }

    map.addEventListener('pointermove', function (event) {
        if (event.pointerType === 'touch' || map.classList.contains('selectTarget') || map.classList.contains('dragMode')) {
            set(null);
            return;
        }
        var target = null;
        var level = event.target.closest && event.target.closest('.buildingSlot .level');
        if (level) {
            var owner = level.closest('.buildingSlot');
            target = owner.classList.contains('a40') ? 'wall' : owner;
        } else {
            target = buildingAt(event.clientX, event.clientY);
        }
        set(target);
        map.classList.toggle('ovPointer', target !== null);
    });
    map.addEventListener('pointerleave', function () {
        set(null);
        map.classList.remove('ovPointer');
    });

    window.OpenVillageBuildingHover = {buildingAt: buildingAt, inShape: inShape, clear: function () {
        set(null);
    }};
})();
