<?php
/*
 * Hero inventory in the Travian Legends 655 look (heroV2Inventory): equipment slots, the hero picture with
 * the horse, slot filters and the bag. Clicking (or dragging) an item equips / uses / takes it off through
 * the heroInventory ajax command of the classic inventory.
 */

use resources\View\LegendsUi as UI;

$e = [UI::class, 'e'];
$equipped = [];
$bag = [];
foreach ($vars['items'] as $item) {
    $slot = $item['proc'] ? ($item['btype'] >= 7 ? 'bag' : UI::itemSlot((int)$item['btype'])) : null;
    if ($slot !== null && (int)($vars['inventory'][$slot] ?? 0) === (int)$item['id']) {
        $equipped[$slot] = $item;
    } elseif (!$item['proc']) {
        $bag[(int)$item['placeId']] = $item;
    }
}
$horse = $equipped['horse']['type'] ?? null;
?>
<div class="contentV2">
    <div class="inventoryPageWrapper" id="heroV2Inventory"
         data-kid="<?=(int)$vars['kid']; ?>" data-checker="<?=$e($vars['checker']); ?>"
         data-dead="<?=$vars['dead'] ? 1 : 0; ?>" data-in-village="<?=$vars['inVillage'] ? 1 : 0; ?>">
        <div class="currentHeroEquipment<?=$vars['dead'] ? ' isDead' : ''; ?>">
            <div class="equipmentSlots">
                <?php foreach (UI::EQUIPMENT as $slot): ?>
                    <?php if (isset($equipped[$slot])): $item = $equipped[$slot]; ?>
                        <?=UI::heroItem($item, [
                            'title' => UI::itemTitle((int)$item['btype'], (int)$item['type']),
                            'attributes' => ['data-equipped' => $slot, 'draggable' => 'true'],
                        ]); ?>
                    <?php else: ?>
                        <?=UI::emptySlot($slot, [
                            'title' => UI::slotName($slot),
                            'attributes' => ['data-drop' => $slot],
                        ]); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="hero <?=$e($vars['gender']); ?><?=$horse ? ' horse horse' . (int)$horse : ''; ?>">
                <img src="hero_body.php?uid=<?=(int)$vars['uid']; ?>&amp;size=inventory&amp;nohorse=1&amp;<?=$e($vars['heroBodyHash']); ?>"
                     class="heroBodyImage heroBodyImage-<?=getDirection(); ?>" alt="<?=$e(T('HeroInventory', 'Body')); ?>">
            </div>
        </div>
        <div class="inventoryWrapper">
            <div class="inventoryFilters"><?=UI::slotFilter('all'); ?></div>
            <a class="tradeItems lightColor" href="hero.php?t=4&amp;action=sell">
                <svg viewBox="0 0 20 20"><path class="greenInner" fill="#699E32" d="M3 4h9l5 6-5 6H3l5-6z"/><path fill="none" stroke="#34220D" stroke-width="1.2" d="M3 4h9l5 6-5 6H3l5-6z"/></svg>
                <?=$e(T('HeroV2', 'tradeItems', 'Trade items')); ?>
            </a>
            <div class="heroItems filter_all">
                <?php for ($place = 1; $place <= $vars['inventorySize']; $place++): ?>
                    <?php if (isset($bag[$place])): $item = $bag[$place]; ?>
                        <?=UI::heroItem($item, [
                            'title' => UI::itemTitle((int)$item['btype'], (int)$item['type']),
                            'attributes' => ['data-place' => $place, 'data-drop' => 'inventory_' . $place, 'draggable' => 'true',
                                'data-instant' => $item['instant'] ? 1 : 0, 'data-usable-if-dead' => $item['isUsableIfDead'] ? 1 : 0],
                        ]); ?>
                    <?php else: ?>
                        <?=UI::emptySlot('', ['attributes' => ['data-place' => $place, 'data-drop' => 'inventory_' . $place]]); ?>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function () {
        var root = document.getElementById('heroV2Inventory');
        if (!root) return;
        var text = <?=json_encode([
            'amount' => T('HeroInventory', 'useDialogDescription'),
            'notMoveable' => T('HeroInventory', 'notMoveableText'),
            'notMoveableDead' => T('HeroInventory', 'notMoveableTextDead'),
        ]); ?>;
        var busy = false;

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

        root.addEventListener('click', function (event) {
            var filter = event.target.closest('.filterSlot');
            if (filter) {
                root.querySelectorAll('.filterSlot').forEach(function (f) { f.classList.toggle('active', f === filter); });
                var items = root.querySelector('.heroItems');
                items.className = items.className.replace(/filter_\w+/, 'filter_' + filter.dataset.key);
                return;
            }
            activate(event.target.closest('.heroItem'));
        });

        var dragged = null;
        root.addEventListener('dragstart', function (event) {
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
