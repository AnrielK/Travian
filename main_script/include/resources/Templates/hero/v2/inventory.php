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
$heroItems = new \Game\Hero\HeroItems();
// Tooltip / Info data of an item: name, category and its effects (Legends heroItemTooltip).
$info = static function (array $item) use ($heroItems): string {
    $btype = (int)$item['btype'];
    $properties = $heroItems->getHeroItemProperties($btype, (int)$item['type']);
    $stats = [];
    foreach ((array)$properties['attributes'] as $line) {
        $line = trim(strip_tags(str_replace(['<br>', '<br />'], ' ', (string)$line)));
        if ($line === '') {
            continue;
        }
        // "Lumber: move lumber to ..." -> title "Lumber" with the explanation as details.
        if ($btype === \Game\Hero\HeroItems::RESOURCE_BTYPE && strpos($line, ': ') !== false) {
            [$title, $details] = explode(': ', $line, 2);
            $stats[] = ['t' => $title, 'd' => ucfirst($details)];
        } else {
            $stats[] = ['t' => $line];
        }
    }
    return json_encode([
        'name' => trim(strip_tags((string)$properties['name'])),
        'sub' => $btype >= 7 ? T('HeroV2', 'consumable', 'Consumable items') : UI::slotName(UI::itemSlot($btype)),
        'tier' => UI::itemTier($btype, (int)$item['type']),
        'stats' => $stats,
    ]);
};
$village = \Core\Village::getInstance();
?>
<div class="contentV2">
    <div class="inventoryPageWrapper" id="heroV2Inventory"
         data-kid="<?=(int)$vars['kid']; ?>" data-checker="<?=$e($vars['checker']); ?>"
         data-dead="<?=$vars['dead'] ? 1 : 0; ?>" data-in-village="<?=$vars['inVillage'] ? 1 : 0; ?>"
         data-village="<?=$e($village->getName()); ?>">
        <div class="currentHeroEquipment<?=$vars['dead'] ? ' isDead' : ''; ?>">
            <div class="equipmentSlots">
                <?php foreach (UI::EQUIPMENT as $slot): ?>
                    <?php if (isset($equipped[$slot])): $item = $equipped[$slot]; ?>
                        <?=UI::heroItem($item, [
                            'attributes' => ['data-equipped' => $slot, 'draggable' => 'true', 'data-info' => $info($item)],
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
                            'attributes' => ['data-place' => $place, 'data-drop' => 'inventory_' . $place, 'draggable' => 'true',
                                'data-instant' => $item['instant'] ? 1 : 0, 'data-usable-if-dead' => $item['isUsableIfDead'] ? 1 : 0,
                                'data-info' => $info($item)],
                        ]); ?>
                    <?php else: ?>
                        <?=UI::emptySlot('', ['attributes' => ['data-place' => $place, 'data-drop' => 'inventory_' . $place]]); ?>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/inventoryScript.php'; ?>
