<?php
/*
 * Hero auctions in the Travian Legends look (Controller/HeroAuctionV2.php of the main branch) with the T4.6 auction
 * data and actions (table auction; bids, sales and the gold -> silver exchange are handled by hero_auction.php and
 * application/views/Auction/actions.php). Needs $session, $database, $Travian, $generator, $Afinished.
 */
$t = [LegendsUi::class, 't'];
$e = [LegendsUi::class, 'e'];
$hlAction = in_array($_GET['action'] ?? 'buy', ['buy', 'sell', 'bids'], true) ? ($_GET['action'] ?? 'buy') : 'buy';
$hlDone = (int)($Afinished[0]['finished'] ?? 0);
$hlCan = $hlDone >= 10;
if ($hlCan) include("application/views/Auction/actions.php");
$hlFilters = ['all' => '', 'helmet' => '=1', 'body' => '=2', 'leftHand' => '=3', 'rightHand' => '=4', 'shoes' => '=5', 'horse' => '=6', 'bag' => '>=7'];
$hlFilter = isset($_GET['slot'], $hlFilters[$_GET['slot']]) ? $_GET['slot'] : 'all';
$hlWhere = $hlFilters[$hlFilter] !== '' ? ' AND btype' . $hlFilters[$hlFilter] : '';
$hlPage = max(1, (int)($_GET['page'] ?? 1));
$hlItem = static function (int $btype, int $type) use ($Travian): array {
    $d = $Travian->getItemData($btype, $type);
    $name = !empty($d['name']) ? $d['name'] : (defined('ITEM' . $type) ? constant('ITEM' . $type) : '');
    $text = !empty($d['title']) ? $d['title'] : (defined('IEFF' . $type) ? constant('IEFF' . $type) : '');
    return [$btype >= 7 && $btype <= 9 ? $btype + 105 : (int)($d['item'] ?? $type), $name, $text];
};
$hlCell = static function (array $row) use ($hlItem): string {
    [$item, $name, $text] = $hlItem((int)$row['btype'], (int)$row['type']);
    return '<td class="heroItemImage heroV2">' . LegendsUi::heroItem(['btype' => (int)$row['btype'], 'type' => $item, 'num' => (int)$row['num']], ['title' => $name . '||' . $text]) . '</td>';
};
$hlSilver = static function ($value, string $extra = ''): string {
    return '<span class="valueWrapper">' . LegendsUi::silverCoin() . '<span>' . number_format((float)$value) . '</span>' . $extra . '</span>';
};
$hlTh = static function (array $columns) use ($e): string {
    $html = '<thead><tr>';
    $first = true;
    foreach ($columns as $class => $label) {
        $item = $first && $class === 'item';
        $html .= '<th class="item ' . ($item ? 'itemInformation' : $class) . '"' . ($item ? ' colspan="2"' : '') . '><div class="cellWrapper"><span>' . $e($label) . '</span></div></th>';
        $first = false;
    }
    return $html . '</tr></thead>';
};
$hlEmpty = static function (int $colspan) use ($t): string {
    return '<tr class="empty"><td colspan="' . $colspan . '"><em>' . LegendsUi::e($t('HEROL_NO_ITEMS', 'No items available at the moment')) . '</em></td></tr>';
};
$hlBidder = static function (array $row) use ($database, $session, $t): string {
    $bidder = (int)$row['uid'];
    if (!$bidder) return '';
    if ($bidder === (int)$session->uid) return '<span class="highestBidderBadge self">' . LegendsUi::e($t('HEROL_YOU', 'You')) . '</span>';
    return '<span class="highestBidderBadge none">' . LegendsUi::e($database->getUserField($bidder, 'username', 0)) . '</span>';
};
$hlRow = static function (array $row, string $action) use ($hlItem, $hlCell, $hlSilver, $hlBidder, $hlCan, $session, $generator, $t, $hlPage, $hlFilter): string {
    $mine = (int)$row['uid'] === (int)$session->uid;
    $price = php7_is_zero($row['newsilver']) ? (int)$row['silver'] : (int)$row['newsilver'];
    $running = (int)$row['finish'] === 0 && $row['time'] > time();
    $min = $price + 1;
    $notAllowed = !$hlCan || !$running || (int)$row['owner'] === (int)$session->uid;
    [, $name] = $hlItem((int)$row['btype'], (int)$row['type']);
    $form = '<form class="formV2 bidInput" method="post" action="hero_auction.php?action=' . $action . '&amp;slot=' . $hlFilter . '">'
        . '<input type="hidden" name="a" value="' . (int)$row['id'] . '"><input type="hidden" name="action" value="' . $action . '">'
        . '<input type="hidden" name="page" value="' . $hlPage . '"><input type="hidden" name="z" value="1ce"><input type="hidden" name="silver" value="' . (int)$row['silver'] . '">'
        . '<label class="input amount"><input type="text" name="maxBid" inputmode="numeric" autocomplete="off" placeholder="' . $min . '" value="' . ($notAllowed ? '-' : $min) . '"' . ($notAllowed ? ' disabled' : '') . '></label>'
        . LegendsUi::button($t('HEROL_BID', 'Bid'), ['type' => 'submit', 'disabledClass' => $notAllowed, 'disabled' => $notAllowed]) . '</form>';
    return '<tr class="' . ($running ? ($mine ? 'highlighted' : 'default') : 'finished') . '">' . $hlCell($row)
        . '<td class="itemInformation"><span><strong>' . LegendsUi::e($name) . '</strong>' . ((int)$row['num'] > 1 ? ' (x' . (int)$row['num'] . ')' : '') . '</span><br>' . $hlSilver($price, $hlBidder($row)) . '</td>'
        . '<td class="bidCount">' . sprintf($t('HEROL_X_BIDS', '%s bids'), (int)$row['bids']) . '</td>'
        . '<td class="finishedAt">' . ($running ? '<span class="timer" counting="down" value="' . ($row['time'] - time()) . '">' . $generator->getTimeFormat($row['time'] - time()) . '</span>' : LegendsUi::e($t('HEROL_DONE', 'Done'))) . '</td>'
        . '<td class="maximumBid">' . $form . '</td></tr>';
};
$hlExchange = '<div class="exchangeWidget"' . ($hlCan ? '' : ' title="' . $e(($lang['Errors']['Error02'] ?? 'Auctions locked')) . '"') . '>'
    . '<button type="button" class="exchangeGold" data-exchange="GoldToSilver"' . ($hlCan ? '' : ' disabled') . '>' . LegendsUi::goldCoin() . '<span>' . number_format((float)$session->gold) . '</span></button>'
    . LegendsUi::button(LegendsUi::exchangeIcon(), ['icon' => true, 'round' => true, 'class' => 'openExchangeOffice', 'appearance' => 'green', 'disabled' => !$hlCan, 'data' => ['exchange' => 'open']])
    . '<button type="button" class="exchangeSilver" data-exchange="SilverToGold"' . ($hlCan ? '' : ' disabled') . '>' . LegendsUi::silverCoin() . '<span>' . number_format((float)$session->silver) . '</span></button></div>';
$hlGolden = static function (bool $withFilter) use ($hlExchange, $hlFilter, $hlAction): string {
    return '<div class="goldenWrapper heroV2">' . ($withFilter ? LegendsUi::slotFilter($hlFilter, 'hero_auction.php?action=' . $hlAction . '&slot=') : '') . $hlExchange . '</div>';
};
?>
<h1 class="titleInHeader"><?php echo herohero3; ?></h1>
<script type="text/javascript">document.getElementById('content').className = 'auctions <?php echo ['buy' => 'auctionsBuy', 'sell' => 'auctionsSell', 'bids' => 'auctionsOverview'][$hlAction]; ?>';</script>
<div class="contentNavi subNavi">
    <?php foreach (['buy' => $t('HEROL_AUCTION_BUY', defined('HEROAC30') ? HEROAC30 : 'Buy'), 'sell' => $t('HEROL_AUCTION_SELL', defined('HEROAC31') ? HEROAC31 : 'Sell'), 'bids' => $t('HEROL_AUCTION_OVERVIEW', 'Auction overview')] as $hlKey => $hlLabel) { ?>
        <div class="container <?php echo $hlKey === $hlAction ? 'active' : 'normal'; ?>"><div class="content favor"><a class="tabItem" href="hero_auction.php?action=<?php echo $hlKey; ?>"><?php echo $e($hlLabel); ?></a></div></div>
    <?php } ?>
</div>
<div id="auctions">
    <?php if (!empty($bidError)) { ?><p class="error"><?php echo $e($bidError); ?></p><?php } ?>
    <?php if (!$hlCan) { ?>
        <div class="auctionsLocked"><div class="lockedIcon"><i class="lock"></i></div>
            <h3><?php echo $e($t('HEROL_AUCTIONS_LOCKED', 'Auctions locked')); ?></h3>
            <div class="barWrapper"><div class="bar" style="width:<?php echo min(100, $hlDone * 10); ?>%"></div></div>
            <div class="barSubText"><div><?php echo sprintf($t('HEROL_FINISH_ADVENTURES', 'Finish %s <a href="%s">adventures</a> to unlock the auctions and the exchange office!'), 10, 'hero_adventure.php'); ?></div>
                <div class="inputRatio"><span class="nominator"><?php echo min(10, $hlDone); ?></span><span class="separator">/</span><span class="denominator">10</span></div></div>
        </div>
    <?php } ?>
    <form class="exchangeOffice formV2" method="post" action="hero_auction.php?action=<?php echo $hlAction; ?>" style="display:none">
        <h4><?php echo $e(defined('exchange0') ? exchange0 : 'Silver converter'); ?></h4>
        <div class="exchangeLine"><?php echo LegendsUi::goldCoin('goldCoin'); ?>
            <label class="input"><input type="text" name="gold" inputmode="numeric" value="1"></label> × 100 = <?php echo LegendsUi::silverCoin('silverCoin'); ?>
            <?php echo LegendsUi::button($e(defined('exchange1') ? exchange1 : 'Exchange'), ['type' => 'submit']); ?></div>
    </form>
    <?php if ($hlAction === 'buy') {
        echo $hlGolden(true);
        $hlTotal = $database->query("SELECT COUNT(*) AS c FROM auction WHERE finish = 0 AND owner != '" . sql_esc($session->uid) . "'" . $hlWhere);
        $hlTotal = (int)($hlTotal[0]['c'] ?? 0);
        $hlPages = max(1, (int)ceil($hlTotal / 20));
        $hlPage = min($hlPage, $hlPages);
        $hlRows = $database->query("SELECT * FROM auction WHERE finish = 0 AND owner != '" . sql_esc($session->uid) . "'" . $hlWhere . " ORDER BY time ASC LIMIT " . (($hlPage - 1) * 20) . ", 20");
        echo '<div class="auctionListWrapper"><table class="itemType detailed">'
            . $hlTh(['item' => $t('HEROL_ITEM', 'Item'), 'bidCount' => $t('HEROL_BIDS', 'Bids'), 'finishedAt' => $t('HEROL_END', 'End'), 'maximumBid' => $t('HEROL_MAX_BID', 'Maximum bid')]) . '<tbody>';
        if (!$hlTotal) echo $hlEmpty(5);
        foreach ((array)$hlRows as $hlR) echo $hlRow($hlR, 'buy');
        echo '</tbody></table></div>' . LegendsUi::pagination($hlPage, $hlPages, 'hero_auction.php?action=buy&slot=' . $hlFilter . '&page=');
    } elseif ($hlAction === 'sell') {
        $hlItems = $database->query("SELECT * FROM heroitems WHERE proc = 0 AND uid = '" . sql_esc($session->uid) . "'");
        $hlItems = is_array($hlItems) ? $hlItems : [];
        echo '<div class="heroV2 sell"><div class="sellItems">' . $hlGolden(false)
            . '<div class="description"><h3>' . $e($t('HEROL_SELL_ITEMS', 'Sell items')) . '</h3><div class="tooltipHelpText">'
            . $e($t('HEROL_SELL_DESCR', 'Click on an item in your inventory to start an auction. Other players can bid on it.')) . '</div></div>'
            . '<div class="inventoryWrapper heroV2"><div class="heroItems filter_all">';
        $hlCount = 0;
        foreach ($hlItems as $hlR) {
            [$hlI, $hlName, $hlText] = $hlItem((int)$hlR['btype'], (int)$hlR['type']);
            $hlAmount = (int)$hlR['btype'] >= 7 && (int)$hlR['btype'] <= 9 ? (int)$hlR['num'] - (int)$hlR['type'] : (int)$hlR['num'];
            echo LegendsUi::heroItem(['btype' => (int)$hlR['btype'], 'type' => $hlI, 'num' => $hlAmount], ['title' => $hlName . '||' . $hlText, 'attributes' => [
                'data-id' => (int)$hlR['id'], 'data-name' => $hlName, 'data-amount' => $hlAmount]]);
            $hlCount++;
        }
        for ($i = $hlCount; $i < max(24, (int)ceil($hlCount / 8) * 8); $i++) echo LegendsUi::emptySlot('');
        echo '</div></div></div>';
        echo '<form class="sellItemForm" method="post" action="hero_auction.php?action=sell" style="display:none"><input type="hidden" name="a" value="e45"><input type="hidden" name="id" value="">'
            . '<div class="sellItemDetails"><div class="sellItemPreview"></div><div><strong class="sellItemName"></strong>'
            . '<div class="sellAmount"><label>' . $e($t('HEROL_AMOUNT', 'Amount')) . ' <input type="text" name="amount" value="1" inputmode="numeric" style="width:50px"></label></div></div>'
            . LegendsUi::button($t('HEROL_START_AUCTION', 'Start auction'), ['type' => 'submit', 'disabled' => !$hlCan, 'disabledClass' => !$hlCan])
            . LegendsUi::button($t('HEROL_CANCEL', 'Cancel'), ['class' => 'cancelSell', 'appearance' => 'grey']) . '</div></form>';
        echo '<h3 class="yourAuctions">' . $e($t('HEROL_YOUR_AUCTIONS', 'Your auctions')) . '</h3>';
        echo '<div class="ongoingSellListWrapper"><table class="itemType detailed">' . $hlTh(['item' => $t('HEROL_ITEM', 'Item'), 'bidCount' => $t('HEROL_BIDS', 'Bids'), 'status' => $t('HEROL_STATUS', 'Status')]) . '<tbody>';
        $hlMine = $database->query("SELECT * FROM auction WHERE finish = 0 AND owner = '" . sql_esc($session->uid) . "' ORDER BY time ASC");
        if (!$hlMine) echo $hlEmpty(4);
        foreach ((array)$hlMine as $hlR) {
            [, $hlName] = $hlItem((int)$hlR['btype'], (int)$hlR['type']);
            $hlPrice = php7_is_zero($hlR['newsilver']) ? $hlR['silver'] : $hlR['newsilver'];
            $hlCancel = (int)$hlR['bids'] === 0 ? ' <a class="cancelAuction" href="hero_auction.php?action=sell&amp;abort=' . (int)$hlR['id'] . '">' . $e($t('HEROL_CANCEL', 'Cancel')) . '</a>' : '';
            echo '<tr class="default">' . $hlCell($hlR) . '<td class="itemInformation"><span><strong>' . $e($hlName) . '</strong> (x' . (int)$hlR['num'] . ')</span><br>' . $hlSilver($hlPrice, $hlBidder($hlR)) . '</td>'
                . '<td class="bidCount">' . sprintf($t('HEROL_X_BIDS', '%s bids'), (int)$hlR['bids']) . '</td>'
                . '<td class="status"><span class="timer" counting="down" value="' . ($hlR['time'] - time()) . '">' . $generator->getTimeFormat($hlR['time'] - time()) . '</span>' . $hlCancel . '</td></tr>';
        }
        echo '</tbody></table></div></div>';
    } else {
        echo $hlGolden(true) . '<div class="auctionsOverviewContainer">';
        echo '<div class="listHeadline"><h4>' . $e($t('HEROL_YOUR_BIDS', 'Your bids')) . '</h4><span>' . $e($t('HEROL_YOUR_BIDS_DESCR', 'All your bids in active and finished auctions.')) . '</span></div>';
        echo '<div class="bidListWrapper"><table class="itemType detailed">' . $hlTh(['item' => $t('HEROL_ITEM', 'Item'), 'bidCount' => $t('HEROL_BIDS', 'Bids'), 'finishedAt' => $t('HEROL_END', 'End'), 'maximumBid' => $t('HEROL_MAX_BID', 'Maximum bid')]) . '<tbody>';
        $hlBids = $database->query("SELECT * FROM auction WHERE uid = '" . sql_esc($session->uid) . "'" . $hlWhere . " ORDER BY finish ASC, time DESC LIMIT 50");
        if (!$hlBids) echo $hlEmpty(5);
        foreach ((array)$hlBids as $hlR) echo $hlRow($hlR, 'bids');
        echo '</tbody></table></div>';
        echo '<div class="listHeadline"><h4>' . $e($t('HEROL_SOLD', 'Sold items')) . '</h4></div>';
        echo '<div class="soldListWrapper"><table class="itemType detailed">' . $hlTh(['item' => $t('HEROL_ITEM', 'Item'), 'bidCount' => $t('HEROL_BIDS', 'Bids'), 'payout' => $t('HEROL_PAYOUT', 'Payout')]) . '<tbody>';
        $hlSold = $database->query("SELECT * FROM auction WHERE finish = 1 AND owner = '" . sql_esc($session->uid) . "' AND bids > 0" . $hlWhere . " ORDER BY time DESC LIMIT 20");
        if (!$hlSold) echo $hlEmpty(4);
        foreach ((array)$hlSold as $hlR) {
            [, $hlName] = $hlItem((int)$hlR['btype'], (int)$hlR['type']);
            $hlPrice = php7_is_zero($hlR['newsilver']) ? $hlR['silver'] : $hlR['newsilver'];
            echo '<tr class="finished">' . $hlCell($hlR) . '<td class="itemInformation"><span><strong>' . $e($hlName) . '</strong> (x' . (int)$hlR['num'] . ')</span></td>'
                . '<td class="bidCount">' . sprintf($t('HEROL_X_BIDS', '%s bids'), (int)$hlR['bids']) . '</td><td class="payout">' . $hlSilver($hlPrice) . '</td></tr>';
        }
        echo '</tbody></table></div></div>';
    } ?>
</div>
<script type="text/javascript">
(function () {
    var root = document.getElementById('auctions');
    if (!root) return;
    var office = root.querySelector('.exchangeOffice');
    root.querySelectorAll('[data-exchange]').forEach(function (b) {
        b.addEventListener('click', function () { if (!b.disabled && office) office.style.display = office.style.display === 'none' ? '' : 'none'; });
    });
    var form = root.querySelector('.sellItemForm');
    if (form) {
        root.querySelectorAll('.sell .heroItems .heroItem[data-id]').forEach(function (item) {
            item.style.cursor = 'pointer';
            item.addEventListener('click', function () {
                form.querySelector('input[name=id]').value = item.getAttribute('data-id');
                form.querySelector('input[name=amount]').value = item.getAttribute('data-amount');
                form.querySelector('.sellItemName').textContent = item.getAttribute('data-name');
                var preview = form.querySelector('.sellItemPreview');
                preview.innerHTML = '';
                preview.appendChild(item.cloneNode(true));
                form.style.display = '';
            });
        });
        form.querySelector('.cancelSell').addEventListener('click', function () { form.style.display = 'none'; });
    }
})();
</script>
