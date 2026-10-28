<?php
LegendsUi::helpStep('#heroV2Inventory .equipmentSlots', 'after', 'HELP_HERO_EQUIPMENT_T', 'Equipment', 'HELP_HERO_EQUIPMENT', 'Helmet, body armour, boots, both hands, horse and bag. Click an item in the inventory to equip it, click an equipped item to take it off.');
LegendsUi::helpStep('#heroV2Inventory .inventoryFilters .heroItemSlotFilter', 'above', 'HELP_HERO_FILTERS_T', 'Filters', 'HELP_HERO_FILTERS', 'Show only the items that fit the chosen equipment slot.');
LegendsUi::helpStep('#heroV2Inventory .heroItems', 'above', 'HELP_HERO_INVENTORY_T', 'Inventory', 'HELP_HERO_INVENTORY', 'All items your hero carries. Click a consumable such as an ointment or a scroll to use it.');
LegendsUi::helpStep('#heroV2Inventory .tradeItems', 'above', 'HELP_HERO_TRADE_T', 'Trade', 'HELP_HERO_TRADE', 'Opens the auctions where you can sell items you do not need and buy better ones for silver.');
/*
 * Hero inventory in the Travian Legends 655 look – port of hero/v2/inventory.php + inventoryScript.php of the main
 * branch on the data and actions of this branch:
 *  - hover: item tooltip; click: context menu Use / Equip, Info, Sort (equipped items: Take off, Info),
 *  - Use / Equip: the T4.6 inventory action (form #HeroInventory → application/Inventory.php), with an amount dialog
 *    for stacks; Take off: hero_inventory.php?inventory&<slot>,
 *  - resource items (task rewards): the "Move resources" dialog (ajax.php?cmd=heroResources),
 *  - Sort and drag & drop: bag places (ajax.php?cmd=heroBagPlace, application/HeroBag.php), drop on an equipment
 *    slot = equip.
 * Needs $gi, $gender, $dis, $deadTitle, $Travian, $database, $session, $heroF, $village (hero_inventory.php).
 */
$hlSlots = LegendsUi::EQUIPMENT;
$hlItem = static function (int $btype, int $type) use ($Travian): array {
    $d = $Travian->getItemData($btype, $type);
    $name = !empty($d['name']) ? $d['name'] : (defined('ITEM' . $type) ? constant('ITEM' . $type) : '');
    $text = !empty($d['title']) ? $d['title'] : (defined('IEFF' . $type) ? constant('IEFF' . $type) : '');
    $item = $btype >= 7 && $btype <= 9 ? $btype + 105 : (int)($d['item'] ?? $type);
    return [$item, $name, $text];
};
/** Tooltip / Info data of an item (Legends heroItemTooltip): name, category, effect lines. */
$hlInfo = static function (array $row, int $shown) use ($hlItem): string {
    $btype = (int)$row['btype'];
    [$item, $name, $text] = $hlItem($btype, (int)$row['type']);
    $stats = [];
    foreach (preg_split('#<br\s*/?>|\|\|#i', (string)$text) as $line) {
        $line = trim(html_entity_decode(strip_tags($line), ENT_QUOTES, 'UTF-8'));
        if ($line !== '') $stats[] = ['t' => $line];
    }
    if ($btype === HeroBag::RESOURCE_BTYPE && $stats) $stats = [['t' => trim(strip_tags($name)), 'd' => $stats[0]['t']]];
    return json_encode([
        'name' => trim(html_entity_decode(strip_tags((string)$name), ENT_QUOTES, 'UTF-8')) . ($shown > 1 ? ' (' . $shown . ')' : ''),
        'sub' => $btype >= 7 ? LegendsUi::t('HEROL_CONSUMABLE', 'Consumable items') : LegendsUi::slotName(LegendsUi::itemSlot($btype)),
        'tier' => LegendsUi::itemTier($btype, $item),
        'stats' => $stats,
    ], JSON_UNESCAPED_UNICODE);
};
$hlRender = static function (array $row, array $attributes, bool $withAmount = true) use ($hlItem): string {
    [$item, , ] = $hlItem((int)$row['btype'], (int)$row['type']);
    return LegendsUi::heroItem(['btype' => (int)$row['btype'], 'type' => $item, 'num' => (int)$row['num'], 'id' => (int)$row['id']],
        ['withAmount' => $withAmount, 'attributes' => $attributes]);
};
$hlStatus = $heroF->getHeroStatus();
$hlDead = $hlStatus != 100;
/** T4.6: equipment, bandages, scrolls, ointments and the bucket need the hero at home and alive. */
$hlNeedsHero = static function (int $btype): bool { return $btype <= 10 || $btype == 11 || $btype == 13; };
// equipped items
$hlEquipped = [];
foreach ($hlSlots as $hlSlot) {
    if (!empty($gi[$hlSlot]) && !php7_is_zero($gi[$hlSlot])) {
        $hlRow = $database->getItemData($gi[$hlSlot]);
        if ($hlRow) $hlEquipped[$hlSlot] = $hlRow;
    }
}
// bag: the same items as the classic inventory, on their places
$hlBag = HeroBag::items((int)$session->uid);
$hlPlaces = HeroBag::places((int)$session->uid, $hlBag);
$hlByPlace = [];
foreach ($hlBag as $hlRow) $hlByPlace[$hlPlaces[(int)$hlRow['id']]] = $hlRow;
$hlSize = max(16, (int)ceil((max(array_merge([0], array_keys($hlByPlace))) + 1) / 8) * 8);
$hlHorse = isset($hlEquipped['horse']) ? $hlItem(6, (int)$hlEquipped['horse']['type'])[0] : null;
$hlBody = HeroLegends::active((int)$session->uid) ? HeroLegends::imageUrl((int)$session->uid, 'body_inventory') : $database->heroBody($session->uid);
$hlResources = [];
foreach (HeroBag::RESOURCE_TYPES as $hlR => $hlType) $hlResources[$hlR] = $hlType;
?>
<div class="contentV2">
    <div class="inventoryPageWrapper" id="heroV2Inventory" data-dead="<?php echo $hlDead ? 1 : 0; ?>"
         data-village="<?php echo LegendsUi::e($village->vname); ?>">
        <div class="currentHeroEquipment<?php echo $hlStatus == 101 ? ' isDead' : ''; ?>">
            <div class="equipmentSlots">
                <?php foreach ($hlSlots as $hlSlot) {
                    if (isset($hlEquipped[$hlSlot])) {
                        $hlRow = $hlEquipped[$hlSlot];
                        echo $hlRender($hlRow, ['data-equipped' => $hlSlot, 'draggable' => 'true', 'data-info' => $hlInfo($hlRow, $hlSlot === 'bag' ? (int)$hlRow['type'] : 1)], $hlSlot === 'bag');
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
                for ($hlPlace = 1; $hlPlace <= $hlSize; $hlPlace++) {
                    if (!isset($hlByPlace[$hlPlace])) {
                        echo LegendsUi::emptySlot('', ['attributes' => ['data-place' => $hlPlace, 'data-drop' => 'inventory_' . $hlPlace]]);
                        continue;
                    }
                    $hlRow = $hlByPlace[$hlPlace];
                    $hlBtype = (int)$hlRow['btype'];
                    $hlNum = (int)$hlRow['num'];
                    if ($hlBtype >= 7 && $hlBtype <= 9) $hlRow['num'] = $hlNum - (int)$hlRow['type'];   // bandages / cages: num - those in the bag slot
                    echo $hlRender($hlRow, [
                        'data-place' => $hlPlace, 'data-drop' => 'inventory_' . $hlPlace, 'draggable' => 'true',
                        'data-instant' => $hlBtype >= 7 ? 1 : 0, 'data-usable-if-dead' => $hlNeedsHero($hlBtype) ? 0 : 1,
                        'data-info' => $hlInfo($hlRow, (int)$hlRow['num']),
                    ]);
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.LegendsInventoryConfig = <?php echo json_encode([
        'resourceBtype' => HeroBag::RESOURCE_BTYPE,
        'resourceTypes' => $hlResources,
        'text' => [
            'amount' => LegendsUi::t('HEROL_USE_AMOUNT', 'How many do you want to use?'),
            'notMoveableDead' => LegendsUi::t('HEROL_NOT_USABLE', 'Your hero must be alive and at home to use this item.'),
            'use' => LegendsUi::t('HEROL_USE', 'Use'),
            'equip' => LegendsUi::t('HEROL_EQUIP', 'Equip'),
            'info' => LegendsUi::t('HEROL_INFO', 'Info'),
            'sort' => LegendsUi::t('HEROL_SORT', 'Sort'),
            'unequip' => LegendsUi::t('HEROL_UNEQUIP', 'Take off'),
            'moveResources' => LegendsUi::t('HEROL_MOVE_RESOURCES', 'Move resources'),
            'moveResourcesText' => LegendsUi::t('HEROL_MOVE_RESOURCES_TEXT', 'Move resources to the warehouse of your currently active village: %s'),
            'moveMaximum' => LegendsUi::t('HEROL_MOVE_MAX', 'Move maximum'),
            'move' => LegendsUi::t('HEROL_MOVE', 'Move'),
            'fillUp' => LegendsUi::t('HEROL_FILL_UP', 'Fill up'),
            'resources' => [1 => LegendsUi::t('HRES_WOOD', 'Lumber'), 2 => LegendsUi::t('HRES_CLAY', 'Clay'), 3 => LegendsUi::t('HRES_IRON', 'Iron'), 4 => LegendsUi::t('HRES_CROP', 'Crop')],
            'amounts' => [1 => LegendsUi::t('HEROL_AMOUNT_1', 'Amount of lumber'), 2 => LegendsUi::t('HEROL_AMOUNT_2', 'Amount of clay'),
                3 => LegendsUi::t('HEROL_AMOUNT_3', 'Amount of iron'), 4 => LegendsUi::t('HEROL_AMOUNT_4', 'Amount of crop')],
            'close' => LegendsUi::t('UI_CLOSE', 'Close'),
            'ok' => LegendsUi::t('HEROL_OK', 'OK'),
            'cancel' => LegendsUi::t('VG_CANCEL', 'Cancel'),
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS); ?>;
</script>
<script type="text/javascript" src="application/legends/heroInventory.js?v=<?php echo Skin::VERSION; ?>"></script>
