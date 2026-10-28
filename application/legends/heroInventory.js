/*
 * Hero inventory of the Travian Legends look (application/views/Hero/legends/inventory.php) – port of
 * hero/v2/inventoryScript.php of the main branch:
 *  - hover: item tooltip under the item; click: context menu Use / Equip, Info, Sort (equipped items: Take off, Info),
 *  - Use on a resource item: "Move resources" dialog (ajax.php?cmd=heroResources), on other items: equip / use
 *    through the T4.6 inventory form, stacks with an amount dialog,
 *  - Info: the item details on a dimmed page,
 *  - Sort: the item follows the pointer and is put down on the clicked bag place (ajax.php?cmd=heroBagPlace),
 *  - drag & drop between bag places and equipment slots.
 * Config and texts: window.LegendsInventoryConfig.
 */
    (function () {
        var root = document.getElementById('heroV2Inventory');
        if (!root) return;
        var C = window.LegendsInventoryConfig || {text: {}, resourceTypes: {}, resourceBtype: 16};
        var text = C.text;
        var resourceTypes = C.resourceTypes;
        var busy = false;
        var content = document.getElementById('content') || document.body;

        /*
         * Actions of this branch: drid = 'inventory_0' → take off (hero_inventory.php?inventory&<slot>),
         * 'inventory_<n>' → put the bag item on place n (ajax heroBagPlace), else use / equip through the T4.6 inventory
         * form (#HeroInventory → application/Inventory.php).
         */
        function itemById(id) { return root.querySelector('.heroItem[data-id="' + id + '"]'); }
        function send(id, drid, amount) {
            if (busy) return;
            var item = itemById(id);
            if (drid === 'inventory_0') {
                busy = true;
                window.location.href = 'hero_inventory.php?inventory&' + encodeURIComponent(item ? item.dataset.equipped : '');
                return;
            }
            var place = /^inventory_(\d+)$/.exec(drid || '');
            if (place) {
                if (item && item.dataset.equipped) { send(id, 'inventory_0', 1); return; }
                busy = true;
                root.classList.add('loading');
                fetch('ajax.php?cmd=heroBagPlace', {method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(id) + '&place=' + encodeURIComponent(place[1])})
                    .then(function () { window.location.reload(); }, function () { window.location.reload(); });
                return;
            }
            var form = document.getElementById('HeroInventory');
            if (!form || !item) return;
            busy = true;
            root.classList.add('loading');
            form.id.value = id;
            form.amount.value = amount || 1;
            form.btype.value = item.dataset.btype;
            form.type.value = item.dataset.type;
            form.submit();
        }
        function post(cmd, data) {
            return fetch('ajax.php?cmd=' + cmd, {method: 'POST', credentials: 'same-origin', headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&')});
        }
        /* amount of a stack to use: a small Legends dialog (window.prompt is not shown in every web view) */
        function askAmount(max, done) {
            if (!window.LegendsDialog) { var a = window.prompt(text.amount, String(max)); if (a !== null) done(a); return; }
            var d = LegendsDialog.open({cls: 'basic legendsConfirm', basic: true, closable: false,
                html: '<p class="confirmText">' + esc(text.amount) + '</p><p class="confirmText"><input type="number" class="ovAmount" min="1" max="' + max + '" value="' + max + '" style="width:90px;text-align:center"></p>',
                buttons: '<button type="button" class="textButtonV2 buttonFramed rectangle withText grey cancelAction"><div>' + esc(text.cancel) + '</div></button>'
                    + '<button type="button" class="textButtonV2 buttonFramed rectangle withText green okAction"><div>' + esc(text.ok) + '</div></button>'});
            var input = d.querySelector('.ovAmount');
            setTimeout(function () { input.focus(); input.select(); }, 50);
            d.querySelector('.cancelAction').addEventListener('click', function () { LegendsDialog.close(d); });
            d.querySelector('.okAction').addEventListener('click', function () { var v = input.value; LegendsDialog.close(d); done(v); });
        }

        function el(tag, className, html) {
            var node = document.createElement(tag);
            if (className) node.className = className;
            if (html !== undefined) node.innerHTML = html;
            return node;
        }

        function esc(value) {
            var node = document.createElement('span');
            node.textContent = value == null ? '' : String(value);
            return node.innerHTML;
        }

        function infoOf(item) {
            try { return JSON.parse(item.dataset.info || '{}'); } catch (e) { return {}; }
        }

        function isResource(item) {
            return item && parseInt(item.dataset.btype, 10) === C.resourceBtype;
        }

        // ---- tooltip -------------------------------------------------------------------------------------------
        function tooltipHtml(info) {
            var html = '<div class="title">' + esc(info.name) + '</div>' + '<div class="subTitle">' + esc(info.sub) + '</div>'
                + '<div class="separator"></div>';
            (info.stats || []).forEach(function (stat) {
                html += '<div class="stat">' + esc(stat.t) + (stat.d ? '<div class="details">' + esc(stat.d) + '</div>' : '') + '</div>';
            });
            return html;
        }

        var tooltip = null;
        function hideTooltip() {
            if (tooltip) { tooltip.remove(); tooltip = null; }
        }
        function showTooltip(item) {
            hideTooltip();
            if (!item || item.classList.contains('empty') || document.querySelector('.ovInventoryMenu')) return;
            var info = infoOf(item);
            if (!info.name) return;
            tooltip = el('div', 'heroItemTooltipV1 heroItemTooltip ' + (info.tier || 'consumable'), tooltipHtml(info));
            content.appendChild(tooltip);
            var rect = item.getBoundingClientRect(), width = tooltip.offsetWidth, height = tooltip.offsetHeight;
            var left = Math.max(8, Math.min(rect.left + rect.width / 2 - width / 2, document.documentElement.clientWidth - width - 8));
            var top = rect.bottom + 6;
            if (top + height > window.innerHeight - 8) top = Math.max(8, rect.top - 6 - height);
            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';
        }
        root.addEventListener('mouseover', function (event) {
            var item = event.target.closest('.heroItem');
            if (item && !item.contains(event.relatedTarget)) showTooltip(item);
        });
        root.addEventListener('mouseout', function (event) {
            var item = event.target.closest('.heroItem');
            if (item && !item.contains(event.relatedTarget)) hideTooltip();
        });
        window.addEventListener('scroll', hideTooltip, {passive: true});

        // ---- use -----------------------------------------------------------------------------------------------
        function activate(item) {
            if (!item || item.classList.contains('empty')) return;
            var id = item.dataset.id;
            if (item.dataset.equipped) {
                send(id, 'inventory_0', 1);
                return;
            }
            if (root.dataset.dead === '1' && item.dataset.usableIfDead !== '1') {
                if (window.LegendsDialog) LegendsDialog.alert(esc(text.notMoveableDead)); else alert(text.notMoveableDead);
                return;
            }
            if (isResource(item)) {
                openTransfer(item);
                return;
            }
            var count = item.querySelector('.count');
            var max = count ? parseInt(count.textContent, 10) || 1 : 1;
            if (item.dataset.instant === '1' && max > 1) {
                askAmount(max, function (answer) { send(id, item.dataset.slot, Math.max(1, Math.min(max, parseInt(answer, 10) || 0))); });
            } else {
                send(id, item.dataset.slot, max);
            }
        }

        // ---- context menu --------------------------------------------------------------------------------------
        var icons = {
            use: '<svg viewBox="0 0 28 28"><path d="M4 9h15V5l6 6-6 6v-4H4zM24 19H9v4l-6-6 6-6v4h15z"/></svg>',
            info: '<svg viewBox="0 0 28 28"><path d="M8 4h9l5 5v15H8zM10 6v16h10V10h-4V6zM12 13h6v1.6h-6zM12 16.5h6v1.6h-6zM12 20h4v1.6h-4z"/></svg>',
            sort: '<svg viewBox="0 0 28 28"><path d="M14 2l4 4h-3v6h6V9l4 5-4 5v-3h-6v6h3l-4 4-4-4h3v-6H7v3l-4-5 4-5v3h6V6h-3z"/></svg>',
            unequip: '<svg viewBox="0 0 28 28"><path d="M6 8l2-2 6 6 6-6 2 2-6 6 6 6-2 2-6-6-6 6-2-2 6-6z"/></svg>'
        };
        function closeMenu() {
            var wrapper = document.querySelector('.ovInventoryMenu');
            if (wrapper) wrapper.remove();
            root.querySelectorAll('.heroItem.menuOpen').forEach(function (i) { i.classList.remove('menuOpen'); });
        }
        function openMenu(item) {
            closeMenu();
            hideTooltip();
            var entries = [];
            if (item.dataset.equipped) {
                entries.push(['unequip', text.unequip, function () { activate(item); }, false]);
            } else {
                var blocked = root.dataset.dead === '1' && item.dataset.usableIfDead !== '1';
                entries.push(['use', parseInt(item.dataset.btype, 10) <= 6 ? text.equip : text.use, function () { activate(item); }, blocked]);
            }
            entries.push(['info', text.info, function () { showInfo(item); }, false]);
            if (!item.dataset.equipped) entries.push(['sort', text.sort, function () { startSort(item); }, false]);

            var wrapper = el('div', 'contextMenuWrapper ovInventoryMenu');
            var menu = el('div', 'contextMenu defaultAppearance');
            entries.forEach(function (entry) {
                var button = el('button', 'entry ' + entry[0], icons[entry[0]] + '<span class="content">' + esc(entry[1]) + '</span>');
                button.type = 'button';
                button.disabled = entry[3];
                button.addEventListener('click', function (event) {
                    event.stopPropagation();
                    closeMenu();
                    entry[2]();
                });
                menu.appendChild(button);
            });
            wrapper.appendChild(menu);
            wrapper.addEventListener('click', closeMenu);
            content.appendChild(wrapper);
            item.classList.add('menuOpen');
            var rect = item.getBoundingClientRect();
            var left = rect.right - rect.width * 0.15, top = rect.top - menu.offsetHeight / 2 + rect.height / 2;
            if (left + menu.offsetWidth > document.documentElement.clientWidth - 8) left = rect.left - menu.offsetWidth + rect.width * 0.15;
            menu.style.left = Math.max(8, left) + 'px';
            menu.style.top = Math.max(8, Math.min(top, window.innerHeight - menu.offsetHeight - 8)) + 'px';
        }

        // ---- info ----------------------------------------------------------------------------------------------
        function overlay(className) {
            var layer = el('div', 'ovInventoryOverlay ' + className);
            layer.addEventListener('click', function (event) { if (event.target === layer) layer.remove(); });
            document.addEventListener('keydown', function onKey(event) {
                if (event.key === 'Escape') { layer.remove(); document.removeEventListener('keydown', onKey); }
            });
            document.body.appendChild(layer);
            return layer;
        }
        function showInfo(item) {
            var info = infoOf(item);
            var layer = overlay('itemInfo');
            var box = el('div', 'heroV2 mobileHeroItemTooltip');
            box.appendChild(el('div', 'heroItemTooltipV1 heroItemTooltip ' + (info.tier || 'consumable'), tooltipHtml(info)));
            layer.appendChild(box);
            var close = el('button', 'ovOverlayClose', '<svg viewBox="0 0 20 20"><path d="M3 5.5 5.5 3 10 7.5 14.5 3 17 5.5 12.5 10l4.5 4.5-2.5 2.5L10 12.5 5.5 17 3 14.5 7.5 10z"/></svg>');
            close.type = 'button';
            close.title = text.close;
            close.addEventListener('click', function () { layer.remove(); });
            layer.appendChild(close);
        }

        // ---- sort ----------------------------------------------------------------------------------------------
        // Legends sorting: the page is dimmed, the item is lifted out of its place (an empty light place stays)
        // and follows the finger / mouse; it is put down on the bag place under it when released or tapped.
        var sorting = null;
        function placeAt(x, y) {
            var places = root.querySelectorAll('.heroItems .heroItem[data-drop^="inventory_"]');
            for (var i = 0; i < places.length; i++) {
                var r = places[i].getBoundingClientRect();
                if (x >= r.left && x <= r.right && y >= r.top && y <= r.bottom) return places[i];
            }
            return null;
        }
        function stopSort() {
            if (!sorting) return;
            sorting.ghost.remove();
            sorting.dim.remove();
            sorting.item.classList.remove('sortSource');
            root.querySelectorAll('.sortTarget').forEach(function (t) { t.classList.remove('sortTarget'); });
            root.classList.remove('sortMode');
            document.documentElement.classList.remove('ovSorting');
            document.removeEventListener('pointermove', sorting.move, true);
            document.removeEventListener('pointerup', sorting.up, true);
            document.removeEventListener('pointerdown', sorting.down, true);
            document.removeEventListener('touchmove', sorting.block, {passive: false, capture: true});
            sorting = null;
        }
        function dropAt(x, y) {
            if (!sorting) return;
            var target = placeAt(x, y), source = sorting.item;
            stopSort();
            if (target && target !== source) send(source.dataset.id, target.dataset.drop, 1);
        }
        function startSort(item) {
            stopSort();
            var rect = item.getBoundingClientRect();
            var dim = el('div', 'ovSortDim');
            document.body.appendChild(dim);
            var ghost = item.cloneNode(true);
            ghost.classList.add('ovSortGhost');
            ghost.classList.remove('menuOpen');
            ghost.style.width = rect.width + 'px';
            ghost.style.height = rect.height + 'px';
            document.body.appendChild(ghost);
            item.classList.add('sortSource');
            root.classList.add('sortMode');
            document.documentElement.classList.add('ovSorting');
            // the lifted item sits a little below-right of the pointer so the finger does not cover it
            var offset = 0.35 * rect.width;
            function follow(x, y) {
                ghost.style.left = (x + offset) + 'px';
                ghost.style.top = (y + offset) + 'px';
                root.querySelectorAll('.sortTarget').forEach(function (t) { t.classList.remove('sortTarget'); });
                var target = placeAt(x, y);
                if (target && target !== item) target.classList.add('sortTarget');
            }
            follow(rect.left + rect.width / 2, rect.top + rect.height / 2);
            var pressed = false, moved = false, start = null;
            var down = function (event) {
                pressed = true; moved = false; start = {x: event.clientX, y: event.clientY};
                follow(event.clientX, event.clientY);
                event.preventDefault();
            };
            var move = function (event) {
                if (start && Math.abs(event.clientX - start.x) + Math.abs(event.clientY - start.y) > 6) moved = true;
                if (pressed || event.pointerType === 'mouse') follow(event.clientX, event.clientY);
            };
            var up = function (event) {
                if (!pressed) return;
                pressed = false;
                event.preventDefault();
                event.stopPropagation();
                // released over the bag (after dragging, or a tap on a place): put the item down there
                if (placeAt(event.clientX, event.clientY) || !moved) dropAt(event.clientX, event.clientY);
            };
            var block = function (event) { event.preventDefault(); };
            document.addEventListener('pointerdown', down, true);
            document.addEventListener('pointermove', move, true);
            document.addEventListener('pointerup', up, true);
            document.addEventListener('touchmove', block, {passive: false, capture: true});
            sorting = {item: item, ghost: ghost, dim: dim, move: move, up: up, down: down, block: block};
        }

        // ---- move resources dialog -----------------------------------------------------------------------------
        function owned() {
            var result = {1: 0, 2: 0, 3: 0, 4: 0};
            root.querySelectorAll('.heroItems .heroItem[data-btype="' + C.resourceBtype + '"]').forEach(function (item) {
                for (var r in resourceTypes) {
                    if (parseInt(item.dataset.type, 10) === resourceTypes[r]) {
                        var count = item.querySelector('.count');
                        result[r] += count ? parseInt(count.textContent, 10) || 0 : 0;
                    }
                }
            });
            return result;
        }
        function village() {
            var res = window.resources || {storage: {}, maxStorage: {}};
            var data = {};
            for (var r = 1; r <= 4; r++) {
                data[r] = {stock: Math.floor(res.storage['l' + r] || 0), max: Math.floor(res.maxStorage['l' + r] || 0)};
            }
            return data;
        }
        function format(n) {
            return Math.round(n).toLocaleString('cs-CZ').replace(/ /g, ' ');
        }
        function openTransfer(item) {
            hideTooltip();
            var have = owned(), stock = village(), selected = item ? (Object.keys(resourceTypes).find(function (r) {
                return resourceTypes[r] === parseInt(item.dataset.type, 10);
            }) || '1') : '1';
            var layer = overlay('transfer');
            var dialog = el('div', 'ovDialog resourceTransferDialog');
            var html = '<div class="ovDialogHeader"><h3>' + esc(text.moveResources) + '</h3>'
                + '<button type="button" class="ovDialogClose buttonFramed withIcon rectangle green" title="' + esc(text.close) + '">'
                + '<svg viewBox="0 0 16 16"><path class="outline" d="M3.2 1.5 8 6.3l4.8-4.8 1.7 1.7L9.7 8l4.8 4.8-1.7 1.7L8 9.7l-4.8 4.8-1.7-1.7L6.3 8 1.5 3.2z"/></svg></button></div>'
                + '<div class="ovDialogBody contentV2"><p class="intro">' + esc(text.moveResourcesText).replace('%s', '<b>' + esc(root.dataset.village) + '</b>') + '</p>';
            for (var r = 1; r <= 4; r++) {
                var free = Math.max(0, stock[r].max - stock[r].stock), limit = Math.min(have[r], free);
                html += '<div class="resourceRow" data-r="' + r + '" data-limit="' + limit + '">'
                    + '<div class="resourceRowHeader"><span>' + esc(text.resources[r]) + '</span>'
                    + '<span class="limit"><span class="from">' + format(stock[r].stock) + '</span> → <span class="to">' + format(stock[r].stock) + '</span></span></div>'
                    + '<div class="resourceRowBody">'
                    + '<div class="heroItem consumable bag heroItemV1 notClickable"><div class="item item' + resourceTypes[r] + '"></div><div class="count">' + have[r] + '</div></div>'
                    + '<div class="resourceInput">'
                    + '<label class="ovInput"><span class="legend">' + esc(text.amounts[r]) + '</span><input type="number" min="0" max="' + limit + '" value="0" inputmode="numeric"></label>'
                    + '<button type="button" class="fillup buttonFramed withIcon rectangle green" title="' + esc(text.fillUp) + '"' + (limit ? '' : ' disabled') + '>'
                    + '<svg viewBox="0 0 17 17"><path d="M8.5 1 16 9.5h-4.5V16h-6V9.5H1z"/></svg></button>'
                    + '<input type="range" class="ovRange" min="0" max="' + limit + '" value="0"' + (limit ? '' : ' disabled') + '>'
                    + '</div></div></div>';
            }
            html += '<div class="actionButton">'
                + '<button type="button" class="textButtonV2 buttonFramed rectangle withText green moveMax"><div>' + esc(text.moveMaximum) + '</div></button>'
                + '<button type="button" class="textButtonV2 buttonFramed rectangle withText green moveSelected" disabled><div>' + esc(text.move) + '</div></button>'
                + '</div></div>';
            dialog.innerHTML = html;
            layer.appendChild(dialog);
            dialog.querySelector('.ovDialogClose').addEventListener('click', function () { layer.remove(); });

            var rows = dialog.querySelectorAll('.resourceRow');
            function update() {
                var any = false;
                rows.forEach(function (row) {
                    var r = row.dataset.r, limit = parseInt(row.dataset.limit, 10);
                    var number = row.querySelector('input[type=number]'), range = row.querySelector('.ovRange');
                    var value = Math.max(0, Math.min(limit, parseInt(number.value, 10) || 0));
                    range.value = value;
                    range.style.setProperty('--fill', limit ? (value / limit * 100) + '%' : '0%');
                    row.querySelector('.to').textContent = format(stock[r].stock + value);
                    row.querySelector('.limit').classList.toggle('reached', stock[r].stock + value >= stock[r].max);
                    any = any || value > 0;
                });
                dialog.querySelector('.moveSelected').disabled = !any;
            }
            rows.forEach(function (row) {
                var number = row.querySelector('input[type=number]'), range = row.querySelector('.ovRange');
                number.addEventListener('input', update);
                number.addEventListener('blur', function () {
                    number.value = Math.max(0, Math.min(parseInt(row.dataset.limit, 10), parseInt(number.value, 10) || 0));
                    update();
                });
                range.addEventListener('input', function () { number.value = range.value; update(); });
                row.querySelector('.fillup').addEventListener('click', function () { number.value = row.dataset.limit; update(); });
            });
            var selectedInput = dialog.querySelector('.resourceRow[data-r="' + selected + '"] input[type=number]');
            if (selectedInput) setTimeout(function () { selectedInput.focus(); selectedInput.select(); }, 50);

            function submit(data) {
                if (busy) return;
                busy = true;
                dialog.classList.add('loading');
                post('heroResources', data).then(function () { window.location.reload(); }, function () { window.location.reload(); });
            }
            dialog.querySelector('.moveMax').addEventListener('click', function () { submit({max: 1}); });
            dialog.querySelector('.moveSelected').addEventListener('click', function () {
                var data = {};
                rows.forEach(function (row) { data['r' + row.dataset.r] = parseInt(row.querySelector('input[type=number]').value, 10) || 0; });
                submit(data);
            });
            update();
        }

        // ---- clicks, filters, drag & drop ----------------------------------------------------------------------
        root.addEventListener('click', function (event) {
            var filter = event.target.closest('.filterSlot');
            if (filter) {
                root.querySelectorAll('.filterSlot').forEach(function (f) { f.classList.toggle('active', f === filter); });
                var items = root.querySelector('.heroItems');
                items.className = items.className.replace(/filter_\w+/, 'filter_' + filter.dataset.key);
                var key = filter.dataset.key;
                items.querySelectorAll('.heroItem').forEach(function (i) {
                    var slot = i.getAttribute('data-slot');
                    i.style.display = key === 'all' || !slot || slot === key || i.classList.contains('empty') ? '' : 'none';
                });
                return;
            }
            var item = event.target.closest('.heroItem');
            if (sorting) return; // handled by the pointer events of the sort mode
            if (item && !item.classList.contains('empty')) openMenu(item);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') { stopSort(); closeMenu(); }
        });

        var dragged = null;
        root.addEventListener('dragstart', function (event) {
            hideTooltip();
            closeMenu();
            dragged = event.target.closest('.heroItem');
            if (!dragged) return;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', dragged.dataset.id || '');
            dragged.classList.add('dragged');
        });
        root.addEventListener('dragend', function () {
            if (dragged) dragged.classList.remove('dragged');
            root.querySelectorAll('.dropTarget').forEach(function (t) { t.classList.remove('dropTarget'); });
        });
        root.addEventListener('dragover', function (event) {
            var target = event.target.closest('[data-drop],[data-equipped]');
            if (!dragged || !target || target === dragged) return;
            event.preventDefault();
            root.querySelectorAll('.dropTarget').forEach(function (t) { if (t !== target) t.classList.remove('dropTarget'); });
            target.classList.add('dropTarget');
        });
        root.addEventListener('drop', function (event) {
            var target = event.target.closest('[data-drop],[data-equipped]');
            if (!dragged || !target || target === dragged) return;
            event.preventDefault();
            var drop = target.dataset.drop || target.dataset.equipped;
            if (drop.indexOf('inventory_') === 0) {
                send(dragged.dataset.id, drop, 1);
            } else if (!dragged.dataset.equipped) {
                activate(dragged);
            }
        });
    })();
