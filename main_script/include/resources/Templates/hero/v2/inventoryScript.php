<?php
/*
 * Behaviour of the Legends hero inventory (hero/v2/inventory.php):
 *  - hover: item tooltip (heroItemTooltipV1) under the item,
 *  - click: context menu Use / Info / Sort (equipped items: Take off / Info),
 *  - Use on a resource item (lumber/clay/iron/crop cart): "Move resources" dialog (heroResources command),
 *    on other items: equip / use through the heroInventory command,
 *  - Info: the item details on a dimmed page,
 *  - Sort: the item follows the pointer and is put down on the clicked bag place,
 *  - drag & drop between bag places and equipment slots.
 */
use resources\View\LegendsUi as UI;

$resourceTypes = \Game\Hero\HeroItems::RESOURCE_TYPES;
?>
<script type="text/javascript">
    (function () {
        var root = document.getElementById('heroV2Inventory');
        if (!root) return;
        var text = <?=json_encode([
            'amount' => T('HeroInventory', 'useDialogDescription'),
            'notMoveable' => T('HeroInventory', 'notMoveableText'),
            'notMoveableDead' => T('HeroInventory', 'notMoveableTextDead'),
            'use' => T('HeroV2', 'use', 'Use'),
            'info' => T('HeroV2', 'info', 'Info'),
            'sort' => T('HeroV2', 'sort', 'Sort'),
            'unequip' => T('HeroV2', 'unequip', 'Take off'),
            'moveResources' => T('HeroV2', 'moveResources', 'Move resources'),
            'moveResourcesText' => T('HeroV2', 'moveResourcesText', 'Move resources to the warehouse of your currently active village: %s'),
            'moveMaximum' => T('HeroV2', 'moveMaximum', 'Move maximum'),
            'move' => T('HeroV2', 'move', 'Move'),
            'fillUp' => T('HeroV2', 'moveMax', 'Fill up'),
            'resources' => [1 => T('Dorf1', 'production.resources.1'), 2 => T('Dorf1', 'production.resources.2'), 3 => T('Dorf1', 'production.resources.3'), 4 => T('Dorf1', 'production.resources.4')],
            'amounts' => [1 => T('HeroV2', 'moveAmount1'), 2 => T('HeroV2', 'moveAmount2'), 3 => T('HeroV2', 'moveAmount3'), 4 => T('HeroV2', 'moveAmount4')],
            'close' => T('Global', 'General.closeWindow'),
        ]); ?>;
        var resourceTypes = <?=json_encode($resourceTypes); ?>;
        var busy = false;
        var content = document.getElementById('content') || document.body;

        function send(id, drid, amount) {
            if (busy) return;
            busy = true;
            root.classList.add('loading');
            Travian.ajax({
                data: {cmd: 'heroInventory', id: id, drid: drid, amount: amount || 1, a: root.dataset.kid, c: root.dataset.checker},
                onSuccess: function () { window.location.reload(); },
                onFailure: function () { window.location.reload(); }
            });
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
            return item && parseInt(item.dataset.btype, 10) === <?=\Game\Hero\HeroItems::RESOURCE_BTYPE; ?>;
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
                alert(text.notMoveableDead);
                return;
            }
            if (isResource(item)) {
                openTransfer(item);
                return;
            }
            var amount = 1, count = item.querySelector('.count');
            var max = count ? parseInt(count.textContent, 10) || 1 : 1;
            if (item.dataset.instant === '1' && max > 1) {
                var answer = window.prompt(text.amount, String(max));
                if (answer === null) return;
                amount = Math.max(1, Math.min(max, parseInt(answer, 10) || 0));
            } else {
                amount = max;
            }
            send(id, item.dataset.slot, amount);
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
                entries.push(['use', text.use, function () { activate(item); }, blocked]);
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
        var sorting = null;
        function stopSort() {
            if (!sorting) return;
            sorting.ghost.remove();
            sorting.item.classList.remove('sortSource');
            root.classList.remove('sortMode');
            document.removeEventListener('pointermove', sorting.move);
            sorting = null;
        }
        function startSort(item) {
            stopSort();
            var rect = item.getBoundingClientRect();
            var ghost = item.cloneNode(true);
            ghost.classList.add('ovSortGhost');
            ghost.style.width = rect.width + 'px';
            ghost.style.height = rect.height + 'px';
            ghost.style.left = rect.left + rect.width * 0.5 + 'px';
            ghost.style.top = rect.top + rect.height * 0.5 + 'px';
            content.appendChild(ghost);
            item.classList.add('sortSource');
            root.classList.add('sortMode');
            var move = function (event) {
                ghost.style.left = event.clientX + 'px';
                ghost.style.top = event.clientY + 'px';
            };
            document.addEventListener('pointermove', move);
            sorting = {item: item, ghost: ghost, move: move};
        }

        // ---- move resources dialog -----------------------------------------------------------------------------
        function owned() {
            var result = {1: 0, 2: 0, 3: 0, 4: 0};
            root.querySelectorAll('.heroItems .heroItem[data-btype="<?=\Game\Hero\HeroItems::RESOURCE_BTYPE; ?>"]').forEach(function (item) {
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
                data.cmd = 'heroResources';
                data.c = root.dataset.checker;
                Travian.ajax({
                    data: data,
                    onSuccess: function () { window.location.reload(); },
                    onFailure: function () { window.location.reload(); }
                });
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
                return;
            }
            var item = event.target.closest('.heroItem');
            if (sorting) {
                var place = item && item.dataset.drop && item.dataset.drop.indexOf('inventory_') === 0 ? item.dataset.drop : null;
                var source = sorting.item;
                stopSort();
                if (place && item !== source) send(source.dataset.id, place, 1);
                return;
            }
            if (item && !item.classList.contains('empty')) openMenu(item);
        });
        document.addEventListener('click', function (event) {
            if (sorting && !root.contains(event.target)) stopSort();
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
</script>
