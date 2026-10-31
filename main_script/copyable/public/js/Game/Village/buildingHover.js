/*
 * Village centre (dorf2.php): the Legends hover of a building – a white veil over the building (20 %, like the
 * highlight shape of Legends) – wherever the pointer is on it, not only on its level circle. The browser's :hover
 * on the outlines is unreliable (Safari, outlines lying on top of each other, the pointer layer of the rearrange
 * mode) and our outlines miss parts of the pictures, so the building under the pointer is found by the pixels of its
 * picture (and its outline) and gets .ovHover; openvillage.css applies the veil (SVG filter #ovHoverVeil).
 *
 * The wall picture also holds the bridges at the gates, so the wall is not veiled as a whole: a copy of its
 * pictures, cut to the ellipse of the wall ring, is veiled instead.
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

    // white veil: every colour 20 % towards white, transparency kept (as the Legends highlight shape)
    if (!document.getElementById('ovHoverVeil')) {
        var holder = document.createElement('div');
        holder.style.cssText = 'position:absolute;width:0;height:0;overflow:hidden';
        holder.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" focusable="false" aria-hidden="true">'
            + '<filter id="ovHoverVeil" color-interpolation-filters="sRGB"><feColorMatrix type="matrix" values="'
            + '0.8 0 0 0 0.2  0 0.8 0 0 0.2  0 0 0.8 0 0.2  0 0 0 1 0"/></filter></svg>';
        document.body.appendChild(holder);
    }

    // ------------------------------------------------------------------ pixels of the building pictures

    var pictures = {};   // url -> {canvas context, width, height} once loaded, null while loading

    function picture(url) {
        if (url in pictures) {
            return pictures[url];
        }
        pictures[url] = null;
        var image = new Image();
        image.onload = function () {
            try {
                var canvas = document.createElement('canvas');
                canvas.width = image.naturalWidth;
                canvas.height = image.naturalHeight;
                var context = canvas.getContext('2d', {willReadFrequently: true});
                context.drawImage(image, 0, 0);
                context.getImageData(0, 0, 1, 1);   // throws for pictures from another origin
                pictures[url] = {context: context, width: canvas.width, height: canvas.height};
            } catch (ignored) {
                pictures[url] = false;
            }
        };
        image.onerror = function () {
            pictures[url] = false;
        };
        image.src = url;
        return null;
    }

    // Is the point on a visible pixel of the building picture (an <img> showing its picture as background)?
    function onPicture(img, x, y) {
        if (!img) {
            return false;
        }
        var match = /url\(["']?([^"')]+)["']?\)/.exec(window.getComputedStyle(img).backgroundImage || '');
        if (!match) {
            return false;
        }
        var data = picture(match[1]);
        if (!data) {
            return false;
        }
        var rect = img.getBoundingClientRect();
        if (x < rect.left || y < rect.top || x >= rect.right || y >= rect.bottom || !img.offsetWidth) {
            return false;
        }
        var factor = rect.width / img.offsetWidth;
        var px = Math.floor((x - rect.left) / factor);
        var py = Math.floor((y - rect.top) / factor);
        if (px >= data.width || py >= data.height) {
            return false;
        }
        return data.context.getImageData(px, py, 1, 1).data[3] > 40;
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
            if (onPicture(slot.querySelector('img.building'), x, y) || inShape(slot.querySelector('svg.buildingShape'), x, y)) {
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

    // load the pictures up front, so the first hover already knows them
    Array.prototype.forEach.call(map.querySelectorAll('.buildingSlot[data-aid]:not(.g0) img.building'), function (img) {
        onPicture(img, -1, -1);
    });

    // veiled copies of the wall pictures, cut to the wall ring (see openvillage.css .wallGlow)
    // (walls with the original Legends outline are veiled through their highlight shape instead)
    Array.prototype.forEach.call(map.querySelectorAll('.buildingSlot.a40:not(.g0) img.wall'), function (img) {
        if (img.parentNode.querySelector('svg.buildingShape .highlightShape')) {
            return;
        }
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
