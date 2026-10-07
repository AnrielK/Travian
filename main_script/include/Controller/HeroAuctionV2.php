<?php

namespace Controller;

use Controller\Ajax\heroAuction;
use Core\Config;
use Core\Database\DB;
use Core\Helper\TimezoneHelper;
use Core\Session;
use Game\Hero\HeroItems;
use Model\AuctionModel;
use resources\View\LegendsUi as UI;

/**
 * Auctions in the Travian Legends 655 look (graphic packs with 'heroV2'): buy (grouped by item type or as a
 * list), sell (bag + own auctions) and the overview (own bids, sales, account statement). Bidding, selling
 * and cancelling still go through HeroAuctionCtrl (same request parameters as the classic pages).
 */
class HeroAuctionV2
{
    private const PAGE_SIZE = 10;

    /** Slot filter => item categories (items.btype). */
    private const FILTER_BTYPES = [
        'all' => [], 'leftHand' => [3], 'rightHand' => [4], 'helmet' => [1], 'body' => [2], 'shoes' => [5], 'horse' => [6],
        'bag' => [7, 8, 9, 10, 11, 12, 13, 14, 15],
    ];

    private $session;
    private $db;
    private $items;
    private $canTakePart;
    private $checker;

    public function __construct(bool $canTakePart, string $checker)
    {
        $this->session = Session::getInstance();
        $this->db = DB::getInstance();
        $this->items = new HeroItems();
        $this->canTakePart = $canTakePart;
        $this->checker = $checker;
    }

    public static function contentClass(string $action): string
    {
        return 'auctions ' . ['buy' => 'auctionsBuy', 'sell' => 'auctionsSell', 'bids' => 'auctionsOverview', 'accounting' => 'auctionsOverview'][$action];
    }

    public function tabs(string $action): string
    {
        $tabs = ['buy' => T('HeroV2', 'auctionsBuy', 'Buy'), 'sell' => T('HeroV2', 'auctionsSell', 'Sell'),
            'bids' => T('HeroV2', 'auctionsOverview', 'Auction overview')];
        $active = $action === 'accounting' ? 'bids' : $action;
        $html = '<div class="contentNavi subNavi">';
        foreach ($tabs as $key => $label) {
            $html .= '<div class="container ' . ($key === $active ? 'active' : 'normal') . '"><div class="content">'
                . '<a class="tabItem" href="hero.php?t=4&amp;action=' . $key . '">' . UI::e($label) . '</a></div></div>';
        }
        return $html . '</div>';
    }

    private function filterKey(): string
    {
        $filter = $_GET['slot'] ?? 'all';
        return isset(self::FILTER_BTYPES[$filter]) ? $filter : 'all';
    }

    private function filterSql(string $filter, string $column = 'btype'): string
    {
        $btypes = self::FILTER_BTYPES[$filter];
        return $btypes ? ' AND ' . $column . ' IN(' . implode(',', $btypes) . ')' : '';
    }

    private function locked(): string
    {
        if ($this->canTakePart) {
            return '';
        }
        $done = min(10, (int)$this->session->getSuccessAdventuresCount());
        return '<div class="auctionsLocked"><div class="lockedIcon"><i class="lock"></i></div>'
            . '<h3>' . UI::e(T('Auction', 'auctionsLocked', 'Auctions locked')) . '</h3>'
            . '<div class="barWrapper"><div class="bar" style="width:' . ($done * 10) . '%"></div></div>'
            . '<div class="barSubText"><div>' . sprintf(T('HeroV2', 'finishAdventures', 'Finish %s <a href="%s">adventures</a> to unlock the auctions and the exchange office!'), 10, 'hero.php?t=3') . '</div>'
            . '<div class="inputRatio"><span class="nominator">' . $done . '</span><span class="separator">/</span><span class="denominator">10</span></div></div></div>';
    }

    private function exchangeWidget(): string
    {
        $disabled = $this->canTakePart ? '' : ' disabled';
        $title = $this->canTakePart ? '' : ' title="' . UI::e(T('Auction', 'auctionsLocked', 'Auctions locked') . '||' . T('Auction', '10AdventuresError')) . '"';
        return '<div class="exchangeWidget"' . $title . '>'
            . '<button type="button" class="exchangeGold" data-exchange="GoldToSilver"' . $disabled . '>' . UI::goldCoin() . '<span>' . number_format_x($this->session->getAvailableGold()) . '</span></button>'
            . UI::button(UI::exchangeIcon(), ['icon' => true, 'round' => true, 'class' => 'openExchangeOffice', 'appearance' => 'green',
                'disabled' => !$this->canTakePart, 'title' => T('Auction', 'silverExchange'), 'data' => ['exchange' => 'open']])
            . '<button type="button" class="exchangeSilver" data-exchange="SilverToGold"' . $disabled . '>' . UI::silverCoin() . '<span>' . number_format_x($this->session->getAvailableSilver()) . '</span></button>'
            . '</div>';
    }

    private function goldenWrapper(string $filter, string $filterHref): string
    {
        return '<div class="goldenWrapper heroV2">' . UI::slotFilter($filter, $filterHref) . $this->exchangeWidget() . '</div>';
    }

    /** Category text below the item name, e.g. "Level 2 / Helmet". */
    private function itemMeta(int $btype, int $type): string
    {
        if ($btype >= 7) {
            return T('HeroV2', 'consumables', 'Consumables');
        }
        $tier = (int)substr(UI::itemTier($btype, $type), 4);
        return sprintf(T('HeroV2', 'itemLevel', 'Level %s'), $tier) . ' / ' . UI::slotName(UI::itemSlot($btype));
    }

    private function itemName(int $btype, int $type): string
    {
        return $this->items->getHeroItemProperties($btype, $type)['name'];
    }

    private function silverValue($value): string
    {
        return '<span class="valueWrapper">' . UI::silverCoin() . '<span>' . number_format_x($value) . '</span></span>';
    }

    private function th(array $columns): string
    {
        $html = '<thead><tr>';
        $first = true;
        foreach ($columns as $class => $label) {
            // The first column covers the item picture and the item information.
            $html .= '<th class="' . $class . '"' . ($first && $class === 'item' ? ' colspan="2"' : '') . '>' . UI::e($label) . '</th>';
            $first = false;
        }
        return $html . '</tr></thead>';
    }

    private function emptyRow(int $colspan, string $text): string
    {
        return '<tr class="empty"><td colspan="' . $colspan . '">' . UI::e($text) . '</td></tr>';
    }

    private function heroItemCell(array $row): string
    {
        return '<td class="heroItemImage heroV2">' . UI::heroItem(['btype' => $row['btype'], 'type' => $row['type'], 'num' => $row['num']],
                ['title' => UI::itemTitle((int)$row['btype'], (int)$row['type'])]) . '</td>';
    }

    // ------------------------------------------------------------------ buy

    public function buy(): string
    {
        $uid = (int)$this->session->getPlayerId();
        $filter = $this->filterKey();
        $grouped = ($_GET['grouped'] ?? '1') !== '0';
        $type = isset($_GET['type']) && preg_match('/^(\d+)\.(\d+)$/', $_GET['type'], $m) ? [(int)$m[1], (int)$m[2]] : null;
        $page = max(1, (int)($_REQUEST['page'] ?? 1));
        $base = 'hero.php?t=4&action=buy&grouped=' . ($grouped ? 1 : 0);

        $html = $this->locked();
        $html .= '<div class="groupAndControls">'
            . UI::toggleSwitch('buyView', $grouped, UI::e(T('HeroV2', 'grouped', 'Group')), 'buyViewSwitch')
            . '<div class="listControl"><div class="controlSection reload">'
            . UI::button('<svg viewBox="0 0 20 20"><path d="M10 3a7 7 0 1 1-6.6 9.3l1.9-.6A5 5 0 1 0 6.5 6.4L8.6 8.5H2.5V2.4l2.6 2.6A7 7 0 0 1 10 3Z"/></svg>',
                ['icon' => true, 'secondary' => true, 'toggle' => true, 'appearance' => 'brown', 'class' => 'reload', 'title' => T('HeroV2', 'reload', 'Reload'),
                    'onclick' => 'window.location.reload()'])
            . '</div></div></div>';
        $html .= $this->goldenWrapper($filter, $base . ($type ? '&type=' . $type[0] . '.' . $type[1] : '') . '&slot=');

        $where = 'finish=0 AND time>' . time() . ' AND uid!=' . $uid . $this->filterSql($filter);
        if ($grouped && !$type) {
            $html .= $this->buyGroups($where, $page, $base . '&slot=' . $filter);
        } else {
            if ($type) {
                $where .= ' AND btype=' . $type[0] . ' AND type=' . $type[1];
                $html .= '<div class="itemTypeDetailContainer"><a class="backButton" href="' . UI::e($base . '&slot=' . $filter) . '">'
                    . '<svg viewBox="0 0 9 16"><path d="M8 1 1 8l7 7" fill="none" stroke="currentColor" stroke-width="2"/></svg></a>'
                    . '<strong>' . UI::e($this->itemName($type[0], $type[1])) . '</strong></div>';
            }
            $html .= $this->buyList($where, $page, $base . ($type ? '&type=' . $type[0] . '.' . $type[1] : '') . '&slot=' . $filter);
        }
        return $html;
    }

    private function buyGroups(string $where, int $page, string $url): string
    {
        $total = (int)$this->db->fetchScalar("SELECT COUNT(*) FROM (SELECT 1 FROM auction WHERE $where GROUP BY btype, type) g");
        $pages = max(1, (int)ceil($total / self::PAGE_SIZE));
        $page = min($page, $pages);
        $result = $this->db->query("SELECT btype, type, COUNT(*) auctions, MIN(time) nextFinish,
                SUBSTRING_INDEX(GROUP_CONCAT(silver ORDER BY time, id), ',', 1) nextPrice,
                SUBSTRING_INDEX(GROUP_CONCAT(num ORDER BY time, id), ',', 1) num
            FROM auction WHERE $where GROUP BY btype, type ORDER BY btype, type
            LIMIT " . (($page - 1) * self::PAGE_SIZE) . ', ' . self::PAGE_SIZE);
        $html = '<div class="itemTypeListWrapper"><table class="itemType detailed entriesClickable">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'auctionsCount' => T('HeroV2', 'runningAuctions', 'Running auctions'),
                'nextFinishAt' => T('HeroV2', 'nextFinish', 'Next finish'), 'nextPrice' => T('HeroV2', 'nextPrice', 'Next price')])
            . '<tbody>';
        if (!$total) {
            $html .= $this->emptyRow(5, T('Auction', 'noAuction'));
        }
        while ($row = $result->fetch_assoc()) {
            $href = 'hero.php?t=4&action=buy&grouped=1&slot=' . $this->filterKey() . '&type=' . $row['btype'] . '.' . $row['type'];
            $html .= '<tr class="default" data-href="' . UI::e($href) . '">' . $this->heroItemCell($row)
                . '<td class="itemInformation"><span><strong>' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '</strong></span><br>'
                . '<span class="itemMeta">' . UI::e($this->itemMeta((int)$row['btype'], (int)$row['type'])) . '</span></td>'
                . '<td class="auctionsCount">' . sprintf(T('HeroV2', 'xAuctions', '%s auctions'), (int)$row['auctions']) . '</td>'
                . '<td class="nextFinishAt">' . appendTimer($row['nextFinish'] - time()) . '</td>'
                . '<td class="nextPrice">' . $this->silverValue($row['nextPrice']) . '</td></tr>';
        }
        return $html . '</tbody></table></div>' . UI::pagination($page, $pages, $url . '&page=');
    }

    private function buyList(string $where, int $page, string $url): string
    {
        $uid = (int)$this->session->getPlayerId();
        $total = (int)$this->db->fetchScalar("SELECT COUNT(*) FROM auction WHERE $where");
        $pages = max(1, (int)ceil($total / self::PAGE_SIZE));
        $page = min($page, $pages);
        $result = $this->db->query("SELECT * FROM auction WHERE $where ORDER BY time, id LIMIT " . (($page - 1) * self::PAGE_SIZE) . ', ' . self::PAGE_SIZE);
        $html = '<div class="itemTypeListWrapper"><table class="itemType detailed">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'bidCount' => T('HeroV2', 'bidsHeader', 'Bids'),
                'finishedAt' => T('HeroV2', 'timeLeft', 'Time left'), 'maximumBid' => T('HeroV2', 'maximumBid', 'Maximum bid')])
            . '<tbody>';
        if (!$total) {
            $html .= $this->emptyRow(5, T('Auction', 'noAuction'));
        }
        while ($row = $result->fetch_assoc()) {
            $html .= $this->auctionRow($row, $page, $url, (int)$row['activeUid'] === $uid ? 'highlighted' : 'default', 'buy', $row['secure_id']);
        }
        return $html . '</tbody></table></div>' . UI::pagination($page, $pages, $url . '&page=');
    }

    private function bidderBadge(array $row): string
    {
        $uid = (int)$this->session->getPlayerId();
        if (!(int)$row['activeUid']) {
            return '';
        }
        if ((int)$row['activeUid'] === $uid) {
            return '<span class="highestBidderBadge self">' . UI::e(T('HeroV2', 'highestBidderYou', 'You')) . '</span>';
        }
        return '';
    }

    /** One auction with the bid form (Legends bidInput). */
    private function auctionRow(array $row, int $page, string $url, string $state, string $action, string $identifier): string
    {
        $uid = (int)$this->session->getPlayerId();
        $mine = (int)$row['activeUid'] === $uid;
        $min = (int)$row['silver'] + ((int)$row['activeUid'] && !$mine ? 1 : 0);
        $current = $mine ? (int)$row['maxSilver'] : 0;
        $notAllowed = !$this->canTakePart;
        $name = UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . ((int)$row['btype'] >= 7 ? ' (x' . (int)$row['num'] . ')' : '');
        $form = '<form class="formV2 bidInput" method="post" action="' . UI::e($url . '&page=' . $page) . '"'
            . ($notAllowed ? ' title="' . UI::e(T('Auction', 'auctionsLocked', 'Auctions locked') . '||' . T('Auction', '10AdventuresError')) . '"' : '') . '>'
            . '<input type="hidden" name="a" value="' . UI::e($identifier) . '"><input type="hidden" name="z" value="' . UI::e($this->checker) . '">'
            . '<input type="hidden" name="action" value="' . $action . '"><input type="hidden" name="page" value="' . $page . '">'
            . '<label class="input amount"><input type="text" name="maxBid" inputmode="numeric" autocomplete="off" placeholder="' . $min . '"'
            . ' value="' . ($notAllowed ? '-' : ($current ?: '')) . '"' . ($notAllowed ? ' disabled' : '') . '></label>'
            . UI::button(T('HeroV2', 'buttonBid', T('Auction', 'doBid')), ['type' => 'submit', 'disabledClass' => $notAllowed, 'disabled' => $notAllowed])
            . '</form>';
        return '<tr class="' . $state . '">' . $this->heroItemCell($row)
            . '<td class="itemInformation"><span><strong>' . $name . '</strong></span><br>'
            . '<span class="valueWrapper">' . UI::silverCoin() . '<span>' . number_format_x($row['silver']) . '</span>' . $this->bidderBadge($row) . '</span></td>'
            . '<td class="bidCount">' . (int)$row['bids'] . ' ' . UI::e(T('Auction', 'bids')) . '</td>'
            . '<td class="finishedAt">' . ($row['time'] > time() && !$row['finish'] ? appendTimer($row['time'] - time()) : UI::e(T('Auction', 'finished'))) . '</td>'
            . '<td class="maximumBid">' . $form . '</td></tr>';
    }

    // ------------------------------------------------------------------ sell

    public function sell(int $maxAuctions): string
    {
        $uid = (int)$this->session->getPlayerId();
        $m = new AuctionModel();
        $html = $this->locked();
        $html .= '<div class="sell">';
        $html .= $this->goldenWrapper('all', '') ;
        $html .= '<h4>' . UI::e(T('HeroV2', 'sellItems', 'Sell items')) . '</h4>'
            . '<p>' . sprintf(T('HeroV2', 'sellDescription', 'Click on an item in your inventory to start an auction. Other players can bid on it. An auction lasts up to %s hours.'),
                round(Config::getInstance()->auction->auctionTime / 3600, 1)) . '</p>';
        $items = [];
        $result = $this->db->query("SELECT * FROM items WHERE uid=$uid AND proc=0 ORDER BY placeId, id");
        while ($row = $result->fetch_assoc()) {
            $items[(int)$row['placeId']] = $row;
        }
        $size = max(16, (int)ceil(max(array_keys($items) ?: [0]) / 8) * 8);
        $html .= '<div class="inventoryWrapper heroV2"><div class="heroItems filter_all">';
        for ($place = 1; $place <= $size; $place++) {
            if (!isset($items[$place])) {
                $html .= UI::emptySlot('');
                continue;
            }
            $row = $items[$place];
            $packages = heroAuction::getPackagesForItemTypeId($row['type']);
            $horse = (int)$row['btype'] === 6 && (int)$row['type'] === AuctionModel::FIRST_HORSE_TYPE;
            $html .= UI::heroItem($row, ['title' => UI::itemTitle((int)$row['btype'], (int)$row['type']), 'attributes' => [
                'data-name' => $this->itemName((int)$row['btype'], (int)$row['type']),
                'data-amount' => (int)$row['num'],
                'data-packages' => json_encode(array_values(array_filter($packages, static fn($p) => $p <= $row['num']))),
                'data-horse' => $horse && $m->canExchangeFirstHorseForSilver($uid, (int)$row['id']) ? 1 : 0,
            ]]);
        }
        $html .= '</div></div>';
        $html .= '<form class="sellItemForm" method="post" action="hero.php?t=4&amp;action=sell" style="display:none">'
            . '<input type="hidden" name="a" value="' . UI::e($this->checker) . '"><input type="hidden" name="id" value="">'
            . '<div class="sellItemDetails"><div class="sellItemPreview"></div><div><strong class="sellItemName"></strong>'
            . '<div class="sellAmount"><label>' . UI::e(T('HeroV2', 'amount', 'Amount')) . ' <select name="amount"></select></label></div>'
            . '<div class="sellHorse" style="display:none"><label><input type="checkbox" name="sellHorse" value="1"> '
            . sprintf(T('HeroV2', 'sellHorseForSilver', 'Exchange this horse for %s silver'), AuctionModel::FIRST_HORSE_SILVER) . '</label></div></div>'
            . UI::button(T('HeroV2', 'startAuction', 'Start auction'), ['type' => 'submit', 'disabled' => !$this->canTakePart, 'disabledClass' => !$this->canTakePart])
            . UI::button(T('HeroInventory', 'buttonCancel'), ['class' => 'cancelSell', 'appearance' => 'grey']) . '</div></form>';
        if ($m->getMyRunningAuctionsCount($uid) >= $maxAuctions) {
            $html .= '<p class="error">' . sprintf(T('Auction', 'You can only have x auctions at a time'), $maxAuctions) . '</p>';
        }

        $html .= '<h4>' . UI::e(T('HeroV2', 'yourAuctions', 'Your auctions')) . '</h4>';
        $html .= '<div class="soldListWrapper"><table class="itemType detailed">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'startedAt' => T('HeroV2', 'startedAt', 'Ends'), 'status' => T('HeroV2', 'status', 'Status')])
            . '<tbody>';
        $rows = 0;
        $running = $m->getMyRunningAuctions($uid);
        while ($row = $running->fetch_assoc()) {
            $rows++;
            $cancel = (int)$row['bids'] === 0
                ? ' <a class="cancelAuction" href="hero.php?t=4&amp;action=sell&amp;abort=' . (int)$row['id'] . '">' . UI::e(T('HeroV2', 'cancel', 'Cancel')) . '</a>' : '';
            $html .= '<tr class="default">' . $this->heroItemCell($row)
                . '<td class="itemInformation"><span><strong>' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '</strong> (x' . (int)$row['num'] . ')</span></td>'
                . '<td class="finishedAt">' . appendTimer($row['time'] - time()) . '</td>'
                . '<td class="status">' . UI::e(T('Auction', 'running')) . $cancel . '</td></tr>';
        }
        $finished = $m->getMyFinishedAuctions($uid, 1, 10);
        while ($row = $finished->fetch_assoc()) {
            $rows++;
            $status = $row['cancel'] ? T('Auction', 'cancelled') : ((int)$row['bids'] ? number_format_x($row['silver']) : T('Auction', 'finished'));
            $html .= '<tr class="finished">' . $this->heroItemCell($row)
                . '<td class="itemInformation"><span><strong>' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '</strong> (x' . (int)$row['num'] . ')</span></td>'
                . '<td class="finishedAt">' . UI::e(TimezoneHelper::autoDate($row['time'])) . '</td>'
                . '<td class="status">' . ((int)$row['bids'] && !$row['cancel'] ? $this->silverValue($row['silver']) : UI::e($status)) . '</td></tr>';
        }
        if (!$rows) {
            $html .= $this->emptyRow(4, T('HeroV2', 'noItems', 'No items available at the moment'));
        }
        return $html . '</tbody></table></div></div>';
    }

    // ------------------------------------------------------------------ overview

    public function overview(): string
    {
        $uid = (int)$this->session->getPlayerId();
        $m = new AuctionModel();
        $filter = $this->filterKey();
        $url = 'hero.php?t=4&action=bids&slot=' . $filter;
        $html = $this->locked() . $this->goldenWrapper($filter, 'hero.php?t=4&action=bids&slot=');

        $html .= '<h4>' . UI::e(T('HeroV2', 'yourBids', 'Your bids')) . '</h4><div class="itemTypeListWrapper"><table class="itemType detailed">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'bidCount' => T('HeroV2', 'bidsHeader', 'Bids'),
                'finishedAt' => T('HeroV2', 'timeLeft', 'Time left'), 'maximumBid' => T('HeroV2', 'maximumBid', 'Maximum bid')]) . '<tbody>';
        $rows = 0;
        $running = $this->db->query("SELECT bids.outbid, auction.* FROM bids JOIN auction ON auction.id=bids.auctionId
            WHERE bids.uid=$uid AND auction.finish=0" . $this->filterSql($filter, 'auction.btype') . " ORDER BY auction.time");
        while ($row = $running->fetch_assoc()) {
            $rows++;
            $html .= $this->auctionRow($row, 1, $url, (int)$row['activeUid'] === $uid ? 'highlighted' : 'default', 'bids', (string)$row['id']);
        }
        if (!$rows) {
            $html .= $this->emptyRow(5, T('Auction', 'noAuction'));
        }
        $html .= '</tbody></table></div>';

        $html .= '<h4>' . UI::e(T('Auction', 'Finished auctions')) . '</h4><div class="itemTypeListWrapper"><table class="itemType detailed">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'bidCount' => T('HeroV2', 'bidsHeader', 'Bids'), 'price' => T('HeroV2', 'price', 'Price'),
                'status' => T('HeroV2', 'status', 'Status')]) . '<tbody>';
        $finished = $m->getMyFinishedBids($uid, 0, 1, 10);
        $rows = 0;
        while ($row = $finished->fetch_assoc()) {
            if (self::FILTER_BTYPES[$filter] && !in_array((int)$row['btype'], self::FILTER_BTYPES[$filter], true)) {
                continue;
            }
            $rows++;
            $won = !$row['outbid'];
            $html .= '<tr class="finished">' . $this->heroItemCell($row)
                . '<td class="itemInformation"><span><strong>' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '</strong> (x' . (int)$row['num'] . ')</span></td>'
                . '<td class="bidCount">' . (int)$row['bids'] . '</td><td class="price">' . $this->silverValue($row['silver']) . '</td>'
                . '<td class="status"><span class="highestBidderBadge ' . ($won ? 'self' : 'none') . '">' . UI::e(T('Auction', $won ? 'won' : 'outbid')) . '</span></td></tr>';
        }
        if (!$rows) {
            $html .= $this->emptyRow(5, T('Auction', 'noAuction'));
        }
        $html .= '</tbody></table></div>';

        $html .= '<h4>' . UI::e(T('Auction', 'balanceSince')) . '</h4><div class="itemTypeListWrapper"><table class="itemType compact accounting">'
            . $this->th(['date' => T('Auction', 'date'), 'cause' => T('Auction', 'cause'), 'payout' => T('Auction', 'reserve'), 'balance' => T('Auction', 'balance')])
            . '<tbody>';
        $booking = $m->getMyBookings($uid);
        if (!$booking->num_rows) {
            $html .= $this->emptyRow(4, T('Auction', 'noBooking'));
        }
        while ($row = $booking->fetch_assoc()) {
            if ($row['cause'] === '1') {
                $cause = T('Auction', 'Adventure');
            } elseif ($row['cause'] === AuctionModel::BOOKING_CAUSE_QUEST_REWARD) {
                $cause = T('Auction', 'Quest reward');
            } else {
                $parts = explode(',', $row['cause']);
                $cause = sprintf(T('Auction', $parts[0] == 1 ? 'sell x items of y' : 'buy x items of y'), $parts[3] ?? '', $this->itemName((int)($parts[1] ?? 0), (int)($parts[2] ?? 0)));
            }
            $html .= '<tr class="default"><td class="date">' . UI::e(TimezoneHelper::autoDate($row['time'])) . '</td><td class="cause">' . UI::e($cause) . '</td>'
                . '<td class="payout">' . UI::e($row['reserve']) . '</td><td class="balance">' . UI::e($row['balance']) . '</td></tr>';
        }
        return $html . '</tbody></table></div>';
    }

    /** Client behaviour: grouped switch, clickable groups, sell panel, exchange office toggle. */
    public static function script(): string
    {
        return <<<'HTML'
<script type="text/javascript">
(function () {
    var root = document.getElementById('auctions');
    if (!root) return;
    root.addEventListener('click', function (event) {
        var row = event.target.closest('tr[data-href]');
        if (row && !event.target.closest('a,button,input,form')) { window.location.href = row.dataset.href; return; }
        var exchange = event.target.closest('[data-exchange]');
        if (exchange && !exchange.disabled) {
            var office = document.getElementById('auctionsExchangeOffice');
            if (office) office.style.display = office.style.display === 'none' ? '' : 'none';
            return;
        }
        var form = root.querySelector('.sellItemForm');
        if (event.target.closest('.cancelSell')) { form.style.display = 'none'; return; }
        var item = event.target.closest('.sell .heroItems .heroItem:not(.empty)');
        if (item && form) {
            form.style.display = '';
            form.querySelector('input[name=id]').value = item.dataset.id;
            form.querySelector('.sellItemName').textContent = item.dataset.name;
            form.querySelector('.sellItemPreview').innerHTML = item.outerHTML;
            form.querySelector('.sellItemPreview').classList.add('heroV2');
            var select = form.querySelector('select[name=amount]');
            var packages = JSON.parse(item.dataset.packages || '[]');
            select.innerHTML = packages.map(function (p) { return '<option value="' + p + '">' + p + '</option>'; }).join('');
            form.querySelector('.sellAmount').style.display = packages.length ? '' : 'none';
            form.querySelector('.sellHorse').style.display = item.dataset.horse === '1' ? '' : 'none';
            form.querySelector('input[name=sellHorse]').checked = false;
            form.scrollIntoView({block: 'nearest'});
        }
    });
    var filter = root.querySelector('.sell .heroItemSlotFilter');
    if (filter) {
        filter.querySelectorAll('a.filterSlot').forEach(function (a) { a.removeAttribute('href'); });
        filter.addEventListener('click', function (event) {
            var slot = event.target.closest('.filterSlot');
            if (!slot) return;
            event.preventDefault();
            filter.querySelectorAll('.filterSlot').forEach(function (f) { f.classList.toggle('active', f === slot); });
            var items = root.querySelector('.sell .heroItems');
            items.className = items.className.replace(/filter_\w+/, 'filter_' + slot.dataset.key);
        });
    }
    var grouped = root.querySelector('.buyViewSwitch input');
    if (grouped) grouped.addEventListener('change', function () {
        window.location.href = 'hero.php?t=4&action=buy&grouped=' + (grouped.checked ? 1 : 0);
    });
})();
</script>
HTML;
    }
}
