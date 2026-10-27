<?php
LegendsUi::helpStep('#heroV2Inventory .equipmentSlots', 'after', 'HELP_HERO_EQUIPMENT_T', 'Equipment', 'HELP_HERO_EQUIPMENT', 'Helmet, body armour, boots, both hands, horse and bag. Click an item in the inventory to equip it, click an equipped item to take it off.');
LegendsUi::helpStep('#heroV2Inventory .inventoryFilters .heroItemSlotFilter', 'above', 'HELP_HERO_FILTERS_T', 'Filters', 'HELP_HERO_FILTERS', 'Show only the items that fit the chosen equipment slot.');
LegendsUi::helpStep('#heroV2Inventory .heroItems', 'above', 'HELP_HERO_INVENTORY_T', 'Inventory', 'HELP_HERO_INVENTORY', 'All items your hero carries. Click a consumable such as an ointment or a scroll to use it.');
LegendsUi::helpStep('#heroV2Inventory .tradeItems', 'above', 'HELP_HERO_TRADE_T', 'Trade', 'HELP_HERO_TRADE', 'Opens the auctions where you can sell items you do not need and buy better ones for silver.');
/*
 * Hero inventory in the Travian Legends 655 look (Templates/hero/v2/inventory.php of the main branch) with the data
 * and actions of the T4.6 inventory: clicking a bag item uses the classic Travian.Game.Hero.Inventory (form
 * #HeroInventory, elements item_<id>), clicking an equipped item takes it off (hero_inventory.php?inventory&<slot>).
 * Needs $gi, $gender, $dis, $deadTitle, $Travian, $database, $session, $heroF (hero_inventory.php).
 */
$hlSlots = ['helmet', 'body', 'shoes', 'leftHand', 'rightHand', 'horse', 'bag'];
$hlItem = static function (int $btype, int $type) use ($Travian): array {
    $d = $Travian->getItemData($btype, $type);
    $name = !empty($d['name']) ? $d['name'] : (defined('ITEM' . $type) ? constant('ITEM' . $type) : '');
    $text = !empty($d['title']) ? $d['title'] : (defined('IEFF' . $type) ? constant('IEFF' . $type) : '');
    $item = $btype >= 7 && $btype <= 9 ? $btype + 105 : (int)($d['item'] ?? $type);
    return [$item, $name, $text];
};
$hlRender = static function (array $row, string $attributes, string $title, bool $withAmount = true) use ($hlItem): string {
    [$item, , ] = $hlItem((int)$row['btype'], (int)$row['type']);
    $html = LegendsUi::heroItem(['btype' => (int)$row['btype'], 'type' => $item, 'num' => (int)$row['num']], [
        'title' => $title, 'withAmount' => $withAmount,
    ]);
    return preg_replace('/^<div /', '<div ' . $attributes . ' ', $html, 1);
};
// equipped items
$hlEquipped = [];
foreach ($hlSlots as $hlSlot) {
    if (!empty($gi[$hlSlot]) && !php7_is_zero($gi[$hlSlot])) {
        $hlRow = $database->getItemData($gi[$hlSlot]);
        if ($hlRow) $hlEquipped[$hlSlot] = $hlRow;
    }
}
// bag (the same items as the classic inventory)
$hlBag = $database->query("SELECT * FROM heroitems WHERE (proc = 0 or (btype > 6 and btype <10 and num>type)) AND uid = '" . sql_esc($session->uid) . "'");
$hlBag = is_array($hlBag) ? $hlBag : [];
$hlSize = max(16, (int)ceil((count($hlBag) + 1) / 8) * 8);
$hlStatus = $heroF->getHeroStatus();
$hlHorse = isset($hlEquipped['horse']) ? $hlItem(6, (int)$hlEquipped['horse']['type'])[0] : null;
$hlBody = HeroLegends::active((int)$session->uid) ? HeroLegends::imageUrl((int)$session->uid, 'body_inventory') : $database->heroBody($session->uid);
?>
<div class="contentV2">
    <div class="inventoryPageWrapper" id="heroV2Inventory">
        <div class="currentHeroEquipment<?php echo $hlStatus == 101 ? ' isDead' : ''; ?>">
            <div class="equipmentSlots">
                <?php foreach ($hlSlots as $hlSlot) {
                    if (isset($hlEquipped[$hlSlot])) {
                        $hlRow = $hlEquipped[$hlSlot];
                        [, $hlName, $hlText] = $hlItem((int)$hlRow['btype'], (int)$hlRow['type']);
                        $hlHtml = $hlRender($hlRow, 'id="item_' . (int)$hlRow['id'] . '" data-equipped="' . $hlSlot . '"', $hlName . '||' . $deadTitle . $hlText, $hlSlot === 'bag');
                        echo $dis ? $hlHtml : '<a href="?inventory&amp;' . $hlSlot . '" class="heroItemLink">' . $hlHtml . '</a>';
                    } else {
                        echo LegendsUi::emptySlot($hlSlot, ['title' => LegendsUi::slotName($hlSlot), 'attributes' => ['data-drop' => $hlSlot]]);
                    }
                } ?>
            </div>
            <div class="hero <?php echo $gender; ?><?php echo $hlHorse ? ' horse horse' . (int)$hlHorse : ''; ?>">
                <img src="<?php echo $hlBody; ?>" class="heroBodyImage heroBodyImage-<?php echo strtoupper(DIRECTION); ?>" alt="">
            </div>
        </div>
        <div class="inventoryWrapper">
            <div class="inventoryFilters"><?php echo LegendsUi::slotFilter('all'); ?></div>
            <a class="tradeItems lightColor" href="hero_auction.php?action=sell">
                <svg viewBox="0 0 20 20"><path class="greenInner" fill="#699E32" d="M3 4h9l5 6-5 6H3l5-6z"/><path fill="none" stroke="#34220D" stroke-width="1.2" d="M3 4h9l5 6-5 6H3l5-6z"/></svg>
                <?php echo LegendsUi::t('HEROL_TRADE', 'Trade items'); ?>
            </a>
            <div class="heroItems filter_all">
                <?php
                $hlPlace = 1;
                foreach ($hlBag as $hlRow) {
                    $hlBtype = (int)$hlRow['btype'];
                    [, $hlName, $hlText] = $hlItem($hlBtype, (int)$hlRow['type']);
                    $hlNum = (int)$hlRow['num'];
                    if ($hlBtype >= 7 && $hlBtype <= 9) {
                        $hlRow['num'] = $hlNum - (int)$hlRow['type'];   // bandages / cages: the classic inventory shows num - type
                        $hlTitle = '(' . $hlNum . ') ' . $hlName . '||' . ($hlBtype <= 10 ? $deadTitle : '') . ($hlNum * SPEED / 10) . $hlText;
                    } else {
                        $hlTitle = ($hlNum > 1 ? '(' . $hlNum . ') ' : '') . $hlName . '||' . (($hlBtype <= 10 || $hlBtype == 11 || $hlBtype == 13) ? $deadTitle : '') . $hlText;
                    }
                    echo $hlRender($hlRow, 'id="item_' . (int)$hlRow['id'] . '" data-place="' . $hlPlace . '"', $hlTitle);
                    $hlPlace++;
                }
                for (; $hlPlace <= $hlSize; $hlPlace++) {
                    echo LegendsUi::emptySlot('', ['attributes' => ['data-place' => $hlPlace]]);
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function () {
        // slot filter of the bag (client side, like the main branch)
        var root = document.getElementById('heroV2Inventory');
        if (!root) return;
        var items = root.querySelector('.heroItems');
        root.querySelectorAll('.inventoryFilters .filterSlot').forEach(function (filter) {
            filter.addEventListener('click', function () {
                root.querySelectorAll('.inventoryFilters .filterSlot').forEach(function (f) { f.classList.remove('active'); });
                filter.classList.add('active');
                var key = filter.getAttribute('data-key');
                items.className = 'heroItems filter_' + key;
                items.querySelectorAll('.heroItem').forEach(function (item) {
                    var slot = item.getAttribute('data-slot');
                    item.style.display = key === 'all' || !slot || slot === key || item.classList.contains('empty') ? '' : 'none';
                });
            });
        });
    })();
</script>
