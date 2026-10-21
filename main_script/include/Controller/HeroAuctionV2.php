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

    /** Favourite tab numbers stored under the "heroAuction" favourite key. */
    public const FAVOURITE_TABS = [0 => 'buy', 1 => 'sell', 2 => 'bids'];

    public static function contentClass(string $action): string
    {
        return 'auctions ' . ['buy' => 'auctionsBuy', 'sell' => 'auctionsSell', 'bids' => 'auctionsOverview', 'accounting' => 'auctionsOverview'][$action];
    }

    public function tabs(string $action): string
    {
        $tabs = ['buy' => T('HeroV2', 'auctionsBuy', 'Buy'), 'sell' => T('HeroV2', 'auctionsSell', 'Sell'),
            'bids' => T('HeroV2', 'auctionsOverview', 'Auction overview')];
        $active = $action === 'accounting' ? 'bids' : $action;
        $keys = array_flip(self::FAVOURITE_TABS);
        $favourite = (int)\Core\Session::getInstance()->getFavoriteTab('heroAuction');
        $html = UI::favorButton('heroAuction', $keys[$active]) . '<div class="contentNavi subNavi">';
        foreach ($tabs as $key => $label) {
            $html .= '<div class="container ' . ($key === $active ? 'active' : 'normal') . '">'
                . '<div class="content favor favorKey' . $keys[$key] . ($favourite === $keys[$key] ? ' favorActive' : '') . '">'
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

    private function goldenWrapper(string $filter, ?string $filterHref, string $start = ''): string
    {
        return '<div class="goldenWrapper heroV2">' . $start . UI::slotFilter($filter, $filterHref) . $this->exchangeWidget() . '</div>';
    }

    private const ICON_SEARCH = '<svg viewBox="0 0 21.77 22"><path d="M19.09 2.48a8.82 8.82 0 1 0 .19 12.46 8.81 8.81 0 0 0-.19-12.46Zm-10.66 11a6.51 6.51 0 1 1 9.2-.14 6.51 6.51 0 0 1-9.2.14Zm-2.88 2.29.57.55a1.58 1.58 0 0 1 0 2.22l-2.9 3a1.55 1.55 0 0 1-2.21 0L.48 21a1.55 1.55 0 0 1 0-2.21l2.9-3a1.56 1.56 0 0 1 2.17-.02Z"/></svg>';
    private const ICON_BACK = '<svg viewBox="0 0 10.2 16.5"><path d="m.1 8.1-.1.2.1.2L7 15.9c.7.7 1.9.8 2.6.1.7-.7.8-1.8.1-2.5L5 8.3l4.7-5.2c.7-.7.7-1.9-.1-2.6-.7-.7-1.9-.7-2.6.1l-.1.1L.1 8.1z"/></svg>';
    private const ICON_DETAILED = '<svg viewBox="0 0 14 8"><path d="M1 8a1 1 0 0 1-1-1V5.8a1 1 0 0 1 1-1h1.2a1 1 0 0 1 1 1V7a1 1 0 0 1-1 1H1Zm4.8 0a1 1 0 0 1-1-1V5.8a1 1 0 0 1 1-1H13a1 1 0 0 1 1 1V7a1 1 0 0 1-1 1H5.8ZM1 3.2a1 1 0 0 1-1-1V1a1 1 0 0 1 1-1h1.2a1 1 0 0 1 1 1v1.2a1 1 0 0 1-1 1H1Zm4.8 0a1 1 0 0 1-1-1V1a1 1 0 0 1 1-1H13a1 1 0 0 1 1 1v1.2a1 1 0 0 1-1 1H5.8Z"/></svg>';
    private const ICON_COMPACT = '<svg viewBox="0 0 14 10"><path d="M3.556 2.333V.556h10.445v1.777H3.556Zm0 3.556V4.111h10.445V5.89H3.556Zm0 3.556V7.667h10.445v1.778H3.556ZM.89 2.333a.89.89 0 1 1 0-1.777.89.89 0 0 1 0 1.777Zm0 3.556a.89.89 0 1 1 0-1.778.89.89 0 0 1 0 1.778Zm0 3.556a.89.89 0 1 1 0-1.778.89.89 0 0 1 0 1.778Z"/></svg>';
    private const ICON_RELOAD = '<svg viewBox="0 0 14 14"><path d="M7 14c-1.954 0-3.61-.678-4.966-2.034C.678 10.609 0 8.954 0 7c0-1.954.678-3.61 2.034-4.966C3.391.678 5.046 0 7 0c1.006 0 1.969.208 2.887.623a6.67 6.67 0 0 1 2.363 1.783V0H14v6.125H7.875v-1.75h3.675A5.107 5.107 0 0 0 9.636 2.45 5.187 5.187 0 0 0 7 1.75c-1.458 0-2.698.51-3.719 1.531C2.261 4.302 1.75 5.541 1.75 7c0 1.458.51 2.698 1.531 3.719C4.302 11.739 5.541 12.25 7 12.25a5.13 5.13 0 0 0 3.04-.963 5.082 5.082 0 0 0 1.904-2.537h1.837a6.909 6.909 0 0 1-2.494 3.784C10.034 13.511 8.604 14 7 14Z"/></svg>';
    private const ICON_SORT = '<svg viewBox="0 0 10 5" class="sortingArrow inactive"><path d="M0 5l5-5 5 5z"/></svg>';

    /** List layout (detailed / compact) and reload buttons above a list. */
    private function listControl(): string
    {
        return '<div class="listControl"><div class="controlSection listLayout">'
            . UI::button(self::ICON_DETAILED, ['icon' => true, 'secondary' => true, 'toggle' => true, 'active' => true, 'appearance' => 'brown',
                'class' => 'listLayout_detailed', 'title' => T('HeroV2', 'detailedList', 'detailed list'), 'data' => ['layout' => 'detailed']])
            . UI::button(self::ICON_COMPACT, ['icon' => true, 'secondary' => true, 'toggle' => true, 'appearance' => 'brown',
                'class' => 'listLayout_compact', 'title' => T('HeroV2', 'compactList', 'compact list'), 'data' => ['layout' => 'compact']])
            . '</div><div class="controlSection reload">'
            . UI::button(self::ICON_RELOAD, ['icon' => true, 'secondary' => true, 'toggle' => true, 'appearance' => 'brown', 'class' => 'reload',
                'title' => T('HeroV2', 'reload', 'Reload'), 'onclick' => 'window.location.reload()'])
            . '</div></div>';
    }

    /** Category text below the item name: slot name and the quality badge (Rise of Governors), or "Consumables". */
    private function itemMeta(int $btype, int $type): string
    {
        if ($btype >= 7) {
            return UI::e(T('HeroV2', 'consumables', 'Consumables'));
        }
        $quality = (int)substr(UI::itemTier($btype, $type), 4);
        return UI::e(UI::slotName(UI::itemSlot($btype))) . ($quality ? UI::rarityQuality($quality, false) : '');
    }

    private function itemName(int $btype, int $type): string
    {
        return $this->items->getHeroItemProperties($btype, $type)['name'];
    }

    private function silverValue($value, string $extra = ''): string
    {
        return '<span class="valueWrapper">' . UI::silverCoin() . '<span>' . number_format_x($value) . '</span>' . $extra . '</span>';
    }

    /** Table head; $columns: class => label, a leading "!" in the label marks a column that cannot be sorted. */
    private function th(array $columns): string
    {
        $html = '<thead><tr>';
        $first = true;
        foreach ($columns as $class => $label) {
            $sortable = $label[0] !== '!';
            $label = ltrim($label, '!');
            // The first column covers the item picture and the item information.
            $item = $first && $class === 'item';
            $html .= '<th class="item ' . ($item ? 'itemInformation' : $class) . ($sortable ? ' sortable' : '') . '"' . ($item ? ' colspan="2"' : '') . '>'
                . '<div class="cellWrapper"><span>' . UI::e($label) . '</span>' . ($sortable ? self::ICON_SORT : '') . '</div></th>';
            $first = false;
        }
        return $html . '</tr></thead>';
    }

    private function emptyRow(int $colspan, string $text): string
    {
        return '<tr class="empty"><td colspan="' . $colspan . '"><em>' . UI::e($text) . '</em></td></tr>';
    }

    private function heroItemCell(array $row, bool $neutral = false): string
    {
        $html = UI::heroItem(['btype' => $row['btype'], 'type' => $row['type'], 'num' => $row['num']],
            ['title' => UI::itemTitle((int)$row['btype'], (int)$row['type'])]);
        if ($neutral) {
            // Item types of the grouped list are shown without rarity colour, like in Rise of Governors.
            $html = self::neutral($html);
        }
        return '<td class="heroItemImage heroV2">' . $html . '</td>';
    }

    /** Item without rarity colour (item types in lists): neutral background, quality numeral only. */
    private static function neutral(string $html): string
    {
        return preg_replace(['/\bheroItem common\b/', '/(class="item item\d+) common"/', '~<svg class="rarityIndicator common"[^>]*>.*?</svg>~'],
            ['heroItem neutral', '$1 neutral"', ''], $html, 1);
    }

    /** "Name (alliance)" of a player for the bidder badge. */
    private function playerLabel(int $uid): string
    {
        static $cache = [];
        if (!$uid) {
            return '';
        }
        if (!isset($cache[$uid])) {
            $row = $this->db->query("SELECT u.name, a.tag FROM users u LEFT JOIN alidata a ON a.id=u.aid WHERE u.id=$uid")->fetch_assoc();
            $cache[$uid] = $row ? $row['name'] . ($row['tag'] ? ' (' . $row['tag'] . ')' : '') : '';
        }
        return $cache[$uid];
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
        $where = 'finish=0 AND time>' . time() . ' AND uid!=' . $uid . $this->filterSql($filter);

        $html = $this->locked();
        if ($type) {
            // Item type page: back button, rarity filter, price overview with the 24 hour chart, then the auctions.
            $back = '<a class="textButtonV2 buttonSecondary backButton withTextAndIcon rectangle withText green" href="' . UI::e($base . '&slot=' . $filter) . '">'
                . '<div>' . self::ICON_BACK . '<span>' . UI::e(T('HeroV2', 'back', 'Back')) . '</span></div></a>';
            $html .= '<div class="goldenWrapper heroV2">' . $back . $this->exchangeWidget() . '</div>';
            $html .= $this->rarityFilter();
            $html .= $this->typeDetails($type[0], $type[1]);
            $html .= '<div class="listHeadline"><div class="tooltipHelpText">' . UI::e(T('HeroV2', 'bidHint', 'Bid on items with a set minimum price.')) . '</div></div>'
                . $this->listControl();
            $where .= ' AND btype=' . $type[0] . ' AND type=' . $type[1];
            return $html . $this->buyList($where, $page, $base . '&type=' . $type[0] . '.' . $type[1] . '&slot=' . $filter) . '</div>';
        }
        $html .= '<div class="groupAndControls">'
            . UI::toggleSwitch('buyView', $grouped, UI::e(T('HeroV2', 'grouped', 'Group')), 'buyViewSwitch')
            . $this->listControl() . '</div>';
        $search = '<div class="itemTypeSearch formV2 heroV2 closed"><button type="button" class="openSearch" title="' . UI::e(T('HeroV2', 'searchItem', 'Search item')) . '">'
            . self::ICON_SEARCH . '</button><label class="input"><input type="text" name="itemSearch" autocomplete="off" placeholder="'
            . UI::e(T('HeroV2', 'searchItem', 'Search item')) . '"></label></div>';
        // Like in Rise of Governors only the chosen slot is shown; a click on it slides the other slots out.
        $html .= str_replace('heroItemSlotFilter filterSlotsRow', 'heroItemSlotFilter filterSlotsRow collapsible collapsed', $this->goldenWrapper($filter, $base . '&slot=', $search));
        if ($grouped) {
            return $html . $this->buyGroups($where, $page, $base . '&slot=' . $filter);
        }
        return $html . $this->buyList($where, $page, $base . '&slot=' . $filter);
    }

    private function rarityFilter(): string
    {
        $all = '<svg class="allRarities" viewBox="0 0 19 19"><rect class="uncommon" width="8" height="8" x="10.5" y=".5" rx="1" ry="1"/><rect class="common" width="8" height="8" x=".5" y=".5" rx="1" ry="1"/>'
            . '<rect class="epic" width="8" height="8" x="10.5" y="10.5" rx="1" ry="1"/><rect class="rare" width="8" height="8" x=".5" y="10.5" rx="1" ry="1"/></svg>';
        $shapes = [
            'common' => '<circle class="common" cx="9" cy="9" r="9"/>',
            'uncommon' => '<path class="uncommon" d="M6.7 1.33c1.02-1.77 3.58-1.77 4.61 0l6.34 10.97c1.02 1.77-.25 3.98-2.3 3.98H2.66c-2.05 0-3.32-2.21-2.3-3.98L6.7 1.33Z"/>',
            'rare' => '<path class="rare" d="M7.09.79a2.71 2.71 0 0 1 3.82 0l6.3 6.3a2.71 2.71 0 0 1 0 3.82l-6.3 6.3a2.71 2.71 0 0 1-3.82 0l-6.3-6.3a2.71 2.71 0 0 1 0-3.82l6.3-6.3Z"/>',
            'epic' => '<path class="epic" d="M7.45.82a2.64 2.64 0 0 1 3.12 0l6.34 4.61c.93.68 1.32 1.87.97 2.96l-2.42 7.47a2.654 2.654 0 0 1-2.52 1.83H5.1c-1.15 0-2.17-.73-2.52-1.83L.13 8.4c-.36-1.09.03-2.3.97-2.96L7.45.82Z"/>',
        ];
        $labels = ['common' => T('HeroV2', 'rarityCommon', 'Common'), 'uncommon' => T('HeroV2', 'rarityUncommon', 'Uncommon'),
            'rare' => T('HeroV2', 'rarityRare', 'Rare'), 'epic' => T('HeroV2', 'rarityEpic', 'Epic')];
        $html = '<div class="goldenWrapper heroV2"><span class="itemRarityLabel">' . UI::e(T('HeroV2', 'itemRarity', 'Item rarity')) . '</span><div class="heroItemRarityFilter">'
            . '<div class="filterRarity all active" data-key="all" title="' . UI::e(T('HeroV2', 'rarityAll', 'All')) . '">' . $all . '</div>';
        foreach ($shapes as $key => $shape) {
            $html .= '<div class="filterRarity ' . $key . '" data-key="' . $key . '" title="' . UI::e($labels[$key]) . '"><div class="rarityQuality">'
                . '<svg class="rarityIndicator ' . $key . '" viewBox="0 0 18 18">' . $shape . '</svg></div></div>';
        }
        return $html . '</div></div>';
    }

    /** Picture, name, lowest/average/highest selling price and the sales of the last 24 hours of one item type. */
    private function typeDetails(int $btype, int $type): string
    {
        $where = "finish=1 AND cancel=0 AND bids>0 AND btype=$btype AND type=$type";
        $prices = $this->db->query("SELECT MIN(silver/GREATEST(num,1)) low, AVG(silver/GREATEST(num,1)) avg, MAX(silver/GREATEST(num,1)) high FROM auction WHERE $where")->fetch_assoc();
        $item = UI::heroItem(['btype' => $btype, 'type' => $type, 'num' => 0], ['title' => UI::itemTitle($btype, $type), 'withAmount' => false]);
        $item = self::neutral($item);
        $dot = '<div class="rarityQuality"><svg class="rarityIndicator common" viewBox="0 0 18 18"><circle class="common" cx="9" cy="9" r="9"/></svg></div>';
        $rows = '';
        foreach (['low' => T('HeroV2', 'lowestPrice', 'Lowest selling price'), 'avg' => T('HeroV2', 'averagePrice', 'Average selling price'),
                     'high' => T('HeroV2', 'highestPrice', 'Highest selling price')] as $key => $label) {
            $value = $prices && $prices[$key] !== null ? $this->silverValue((int)round($prices[$key])) : '<span class="valueWrapper">-</span>';
            $rows .= '<tr><td class="label"><div class="valueWrapper labelRarity common">' . $dot . UI::e($label) . '</div></td><td class="value">' . $value . '</td></tr>';
        }
        return '<div class="itemTypeDetailContainer"><div class="itemTypeDetailedInformation">'
            . '<div class="itemImage heroV2">' . $item . '</div>'
            . '<div class="itemDetails"><h4 class="itemTitle">' . UI::e($this->itemName($btype, $type)) . '</h4><span class="itemMeta">' . $this->itemMeta($btype, $type) . '</span></div>'
            . '<div class="priceTable"><table class="borderGap"><tbody>' . $rows . '</tbody></table></div>'
            . $this->priceChart($where) . '</div>';
    }

    /** Area chart of the average price (left axis) and the number of sales (right axis) per hour of the last 24 hours. */
    private function priceChart(string $where): string
    {
        $now = time();
        $sales = array_fill(0, 24, 0);
        $sum = array_fill(0, 24, 0.0);
        $result = $this->db->query("SELECT time, silver/GREATEST(num,1) price FROM auction WHERE $where AND time>" . ($now - 86400) . " AND time<=$now");
        while ($row = $result->fetch_assoc()) {
            $bucket = min(23, max(0, 23 - intdiv($now - (int)$row['time'], 3600)));
            $sales[$bucket]++;
            $sum[$bucket] += (float)$row['price'];
        }
        $avg = [];
        foreach ($sales as $i => $n) {
            $avg[$i] = $n ? $sum[$i] / $n : 0;
        }
        $nice = static function (float $max): int {
            if ($max <= 0) {
                return 10;
            }
            $step = 10 ** floor(log10($max));
            return (int)(ceil($max / $step) * $step);
        };
        $maxSales = max(2, $nice(max($sales)));
        $maxPrice = $nice(max($avg));
        [$x0, $x1, $h] = [40, 260, 121.67];
        $points = static function (array $values, float $max) use ($x0, $x1, $h): array {
            $out = [];
            foreach ($values as $i => $v) {
                $out[] = round($x0 + ($x1 - $x0) * $i / 23, 2) . ',' . round($h - ($max > 0 ? $v / $max * $h : 0), 2);
            }
            return $out;
        };
        $area = static function (array $points, string $color) use ($x0, $x1, $h): string {
            $line = 'M' . implode('L', $points);
            return '<path fill="' . $color . '" fill-opacity=".3" stroke="none" d="' . $line . "L$x1,{$h}L$x0,{$h}Z" . '"/>'
                . '<path fill="none" stroke="' . $color . '" d="' . $line . '"/>';
        };
        $grid = '';
        foreach ([0, $h] as $y) {
            $grid .= '<line stroke-dasharray="3 3" stroke="#939393" x1="' . $x0 . '" y1="' . $y . '" x2="' . $x1 . '" y2="' . $y . '"/>';
        }
        foreach ([$x0, ($x0 + $x1) / 2, $x1] as $x) {
            $grid .= '<line stroke-dasharray="3 3" stroke="#939393" x1="' . $x . '" y1="0" x2="' . $x . '" y2="' . $h . '"/>';
        }
        $text = static fn($x, $y, $anchor, $color, $value) => '<text font-size="10" x="' . $x . '" y="' . $y . '" text-anchor="' . $anchor . '" fill="' . $color . '">' . UI::e($value) . '</text>';
        $axes = '<path stroke="#5E463A" stroke-width="2" fill="none" d="M' . $x0 . ',0V' . $h . 'H' . $x1 . 'V0"/>'
            . $text($x0, $h + 13, 'middle', '#5E463A', T('HeroV2', 'hoursAgo23', '23 h'))
            . $text(($x0 + $x1) / 2, $h + 13, 'middle', '#5E463A', T('HeroV2', 'hoursAgo12', '12 h'))
            . $text($x1, $h + 13, 'middle', '#5E463A', T('HeroV2', 'now', 'Now'))
            . $text($x1 + 10, $h + 3, 'start', '#D9A90B', '0') . $text($x1 + 10, $h / 2 + 3, 'start', '#D9A90B', (string)($maxSales / 2)) . $text($x1 + 10, 8, 'start', '#D9A90B', (string)$maxSales)
            . $text($x0 - 10, $h + 3, 'end', '#B43B35', '0') . $text($x0 - 10, $h / 2 + 3, 'end', '#B43B35', (string)($maxPrice / 2)) . $text($x0 - 10, 8, 'end', '#B43B35', (string)$maxPrice);
        $dot = '<div class="rarityQuality"><svg class="rarityIndicator common" viewBox="0 0 18 18"><circle class="common" cx="9" cy="9" r="9"/></svg></div>';
        return '<div class="priceChart"><div class="title">' . UI::e(T('HeroV2', 'priceChartTitle', 'Average price and sales during the last 24 hours')) . '</div>'
            . '<svg class="recharts-surface ovPriceChart" viewBox="0 0 300 140" preserveAspectRatio="xMidYMid meet">' . $grid . $area($points($sales, $maxSales), '#D9A90B')
            . $area($points($avg, $maxPrice), '#B43B35') . $axes . '</svg>'
            . '<div class="recharts-legend-wrapper"><ul class="recharts-default-legend">'
            . '<li class="recharts-legend-item"><svg width="14" height="14" viewBox="0 0 32 32"><line stroke-width="4" stroke="#B43B35" x1="0" y1="16" x2="32" y2="16"/></svg>'
            . '<span style="color:#B43B35"><div class="labelRarity common">' . $dot . UI::e(T('HeroV2', 'averagePriceShort', 'Average price')) . '</div></span></li>'
            . '<li class="recharts-legend-item"><svg width="14" height="14" viewBox="0 0 32 32"><line stroke-width="4" stroke="#D9A90B" x1="0" y1="16" x2="32" y2="16"/></svg>'
            . '<span style="color:#D9A90B"><div class="labelRarity common">' . $dot . UI::e(T('HeroV2', 'sales', 'Sales')) . '</div></span></li></ul></div></div>';
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
            $html .= $this->emptyRow(5, T('HeroV2', 'noItems', 'No items available at the moment'));
        }
        while ($row = $result->fetch_assoc()) {
            $name = $this->itemName((int)$row['btype'], (int)$row['type']);
            $href = 'hero.php?t=4&action=buy&grouped=1&slot=' . $this->filterKey() . '&type=' . $row['btype'] . '.' . $row['type'];
            $html .= '<tr class="default" data-href="' . UI::e($href) . '" data-name="' . UI::e(mb_strtolower($name)) . '">' . $this->heroItemCell($row, true)
                . '<td class="itemInformation" data-sort="' . UI::e($name) . '"><span><strong>' . UI::e($name) . '</strong></span><br>'
                . '<span class="itemMeta">' . $this->itemMeta((int)$row['btype'], (int)$row['type']) . '</span></td>'
                . '<td class="auctionsCount" data-sort="' . (int)$row['auctions'] . '">' . sprintf(T('HeroV2', 'xAuctions', '%s auctions'), (int)$row['auctions']) . '</td>'
                . '<td class="nextFinishAt" data-sort="' . (int)$row['nextFinish'] . '">' . appendTimer($row['nextFinish'] - time()) . '</td>'
                . '<td class="nextPrice" data-sort="' . (int)$row['nextPrice'] . '">' . $this->silverValue($row['nextPrice']) . '</td></tr>';
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
        $html = '<div class="auctionListWrapper"><table class="itemType detailed">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'bidCount' => T('HeroV2', 'bidsColumn', 'bids'),
                'finishedAt' => T('HeroV2', 'endsColumn', 'End'), 'maximumBid' => '!' . T('HeroV2', 'maximumBid', 'Maximum bid')])
            . '<tbody>';
        if (!$total) {
            $html .= $this->emptyRow(5, T('HeroV2', 'noItems', 'No items available at the moment'));
        }
        while ($row = $result->fetch_assoc()) {
            $html .= $this->auctionRow($row, $page, $url, (int)$row['activeUid'] === $uid ? 'highlighted' : 'default', 'buy', $row['secure_id']);
        }
        return $html . '</tbody></table></div>' . UI::pagination($page, $pages, $url . '&page=');
    }

    /** Highest bidder under the price: "You" (green) or the name and alliance of the other bidder. */
    private function bidderBadge(array $row): string
    {
        $uid = (int)$this->session->getPlayerId();
        $bidder = (int)$row['activeUid'];
        if (!$bidder) {
            return '';
        }
        if ($bidder === $uid) {
            return '<span class="highestBidderBadge self">' . UI::e(T('HeroV2', 'highestBidderYou', 'You')) . '</span>';
        }
        $label = $this->playerLabel($bidder);
        return $label === '' ? '' : '<span class="highestBidderBadge none">' . UI::e($label) . '</span>';
    }

    /** One auction with the bid form (Legends bidInput). */
    private function auctionRow(array $row, int $page, string $url, string $state, string $action, string $identifier): string
    {
        $uid = (int)$this->session->getPlayerId();
        $mine = (int)$row['activeUid'] === $uid;
        $running = $row['time'] > time() && !$row['finish'];
        $min = (int)$row['silver'] + ((int)$row['activeUid'] && !$mine ? 1 : 0);
        $current = $mine ? (int)$row['maxSilver'] : $min;
        $notAllowed = !$this->canTakePart || !$running;
        $name = UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . ((int)$row['btype'] >= 7 ? ' (x' . (int)$row['num'] . ')' : '');
        $form = '<form class="formV2 bidInput" method="post" action="' . UI::e($url . '&page=' . $page) . '"'
            . (!$this->canTakePart ? ' title="' . UI::e(T('Auction', 'auctionsLocked', 'Auctions locked') . '||' . T('Auction', '10AdventuresError')) . '"' : '') . '>'
            . '<input type="hidden" name="a" value="' . UI::e($identifier) . '"><input type="hidden" name="z" value="' . UI::e($this->checker) . '">'
            . '<input type="hidden" name="action" value="' . $action . '"><input type="hidden" name="page" value="' . $page . '">'
            . '<label class="input amount"><input type="text" name="maxBid" inputmode="numeric" autocomplete="off" placeholder="' . $min . '"'
            . ' value="' . ($notAllowed ? '-' : $current) . '"' . ($notAllowed ? ' disabled' : '') . '></label>'
            . UI::button(T('HeroV2', 'buttonBid', T('Auction', 'doBid')), ['type' => 'submit', 'disabledClass' => $notAllowed, 'disabled' => $notAllowed])
            . '</form>';
        $bids = (int)$row['bids'];
        return '<tr class="' . ($running ? $state : 'finished') . '" data-name="' . UI::e(mb_strtolower($this->itemName((int)$row['btype'], (int)$row['type']))) . '">' . $this->heroItemCell($row)
            . '<td class="itemInformation" data-sort="' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '"><span><strong>' . $name . '</strong></span><br>'
            . $this->silverValue($row['silver'], $this->bidderBadge($row)) . '</td>'
            . '<td class="bidCount" data-sort="' . $bids . '">' . sprintf(T('HeroV2', 'xBids', '%s bids'), $bids) . '</td>'
            . '<td class="finishedAt" data-sort="' . (int)$row['time'] . '">' . ($running ? appendTimer($row['time'] - time()) : UI::e(T('HeroV2', 'done', 'Done'))) . '</td>'
            . '<td class="maximumBid">' . $form . '</td></tr>';
    }

    // ------------------------------------------------------------------ sell

    public function sell(int $maxAuctions): string
    {
        $uid = (int)$this->session->getPlayerId();
        $m = new AuctionModel();
        $hours = round(Config::getInstance()->auction->auctionTime / 3600, 1);
        $html = $this->locked();
        $html .= '<div class="heroV2 sell"><div class="sellItems">';
        $html .= $this->goldenWrapper('all', null);
        $html .= '<div class="description"><h3>' . UI::e(T('HeroV2', 'sellItems', 'Sell items')) . '</h3>'
            . '<div class="tooltipHelpText">' . sprintf(UI::e(T('HeroV2', 'sellDescription', 'Click on an item in your inventory to start an auction. Other players can bid on it. An auction lasts up to %s hours.')), $hours) . '</div></div>';
        $items = [];
        $result = $this->db->query("SELECT * FROM items WHERE uid=$uid AND proc=0 AND btype<>" . \Game\Hero\HeroItems::RESOURCE_BTYPE . " ORDER BY placeId, id");
        while ($row = $result->fetch_assoc()) {
            $items[(int)$row['placeId']] = $row;
        }
        $size = max(24, (int)ceil(max(array_keys($items) ?: [0]) / 8) * 8);
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
        $html .= '</div></div></div>';
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

        $html .= '<h3 class="yourAuctions">' . UI::e(T('HeroV2', 'yourAuctions', 'Your auctions')) . '</h3>' . $this->listControl();
        $html .= '<div class="ongoingSellListWrapper"><table class="itemType detailed">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'startedAt' => T('HeroV2', 'startedAtColumn', 'Started at'), 'status' => '!' . T('HeroV2', 'statusColumn', 'Status')])
            . '<tbody>';
        $rows = 0;
        $length = (int)Config::getInstance()->auction->auctionTime;
        $running = $m->getMyRunningAuctions($uid);
        while ($row = $running->fetch_assoc()) {
            $rows++;
            $cancel = (int)$row['bids'] === 0
                ? ' <a class="cancelAuction" href="hero.php?t=4&amp;action=sell&amp;abort=' . (int)$row['id'] . '">' . UI::e(T('HeroV2', 'cancel', 'Cancel')) . '</a>' : '';
            $html .= '<tr class="default">' . $this->heroItemCell($row)
                . '<td class="itemInformation" data-sort="' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '"><span><strong>' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '</strong> (x' . (int)$row['num'] . ')</span><br>'
                . $this->silverValue($row['silver'], $this->bidderBadge($row)) . '</td>'
                . '<td class="startedAt" data-sort="' . ((int)$row['time'] - $length) . '">' . UI::e(TimezoneHelper::autoDate((int)$row['time'] - $length)) . '</td>'
                . '<td class="status">' . appendTimer($row['time'] - time()) . $cancel . '</td></tr>';
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
        $all = isset($_GET['all']);
        $html = $this->locked() . $this->goldenWrapper($filter, 'hero.php?t=4&action=bids&slot=');
        $html .= '<div class="auctionsOverviewContainer">';

        // Own bids: running ones first, then the finished ones (won or outbid).
        $html .= '<div class="listHeadline"><h4>' . UI::e(T('HeroV2', 'yourBids', 'Your bids')) . '</h4><span>'
            . UI::e(T('HeroV2', 'yourBidsDescription', 'All your bids in active and finished auctions.')) . '</span></div>' . $this->listControl();
        $html .= '<div class="bidListWrapper"><table class="itemType detailed">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'bidCount' => T('HeroV2', 'bidsColumn', 'bids'),
                'finishedAt' => T('HeroV2', 'endsColumn', 'End'), 'maximumBid' => '!' . T('HeroV2', 'maximumBid', 'Maximum bid')]) . '<tbody>';
        $limit = $all ? 100 : 5;
        $result = $this->db->query("SELECT * FROM auction WHERE id IN(SELECT auctionId FROM bids WHERE uid=$uid)" . $this->filterSql($filter)
            . " ORDER BY finish, time DESC LIMIT " . ($limit + 1));
        $rows = 0;
        while ($row = $result->fetch_assoc()) {
            if (++$rows > $limit) {
                break;
            }
            $html .= $this->auctionRow($row, 1, $url, (int)$row['activeUid'] === $uid ? 'highlighted' : 'default', 'bids', (string)$row['id']);
        }
        if (!$rows) {
            $html .= $this->emptyRow(5, T('HeroV2', 'noItems', 'No items available at the moment'));
        }
        $html .= '</tbody></table></div>';
        if ($rows > $limit) {
            $html .= '<div class="showMore"><a href="' . UI::e($url . '&all=1') . '">' . UI::e(T('HeroV2', 'showMore', 'Show more')) . '</a></div>';
        }

        // Own items that were sold.
        $html .= '<div class="listHeadline"><h4>' . UI::e(T('HeroV2', 'soldItems', 'Sold items')) . '</h4></div>' . $this->listControl();
        $html .= '<div class="soldListWrapper"><table class="itemType detailed">'
            . $this->th(['item' => T('HeroV2', 'item', 'Item'), 'bidCount' => T('HeroV2', 'bidsColumn', 'bids'),
                'finishedAt' => T('HeroV2', 'endsColumn', 'End'), 'payout' => T('HeroV2', 'payout', 'Payout')]) . '<tbody>';
        $sold = $this->db->query("SELECT * FROM auction WHERE uid=$uid AND finish=1 AND cancel=0 AND bids>0" . $this->filterSql($filter) . " ORDER BY time DESC LIMIT 20");
        $rows = 0;
        while ($row = $sold->fetch_assoc()) {
            $rows++;
            $html .= '<tr class="finished">' . $this->heroItemCell($row)
                . '<td class="itemInformation" data-sort="' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '"><span><strong>' . UI::e($this->itemName((int)$row['btype'], (int)$row['type'])) . '</strong> (x' . (int)$row['num'] . ')</span><br>'
                . $this->silverValue($row['silver'], $this->bidderBadge($row)) . '</td>'
                . '<td class="bidCount" data-sort="' . (int)$row['bids'] . '">' . sprintf(T('HeroV2', 'xBids', '%s bids'), (int)$row['bids']) . '</td>'
                . '<td class="finishedAt" data-sort="' . (int)$row['time'] . '">' . UI::e(TimezoneHelper::autoDate($row['time'])) . '</td>'
                . '<td class="payout" data-sort="' . (int)$row['silver'] . '">' . $this->silverValue($row['silver']) . '</td></tr>';
        }
        if (!$rows) {
            $html .= $this->emptyRow(5, T('HeroV2', 'noItems', 'No items available at the moment'));
        }
        $html .= '</tbody></table></div>';

        $html .= '<div class="listHeadline"><h4>' . UI::e(T('Auction', 'balanceSince')) . '</h4></div><div class="itemTypeListWrapper"><table class="itemType compact accounting">'
            . $this->th(['date' => '!' . T('Auction', 'date'), 'cause' => '!' . T('Auction', 'cause'), 'payout' => '!' . T('Auction', 'reserve'), 'balance' => '!' . T('Auction', 'balance')])
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
        return $html . '</tbody></table></div></div>';
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
    // Sortable columns (click the head; arrow up = ascending), list layout (detailed / compact, remembered).
    root.querySelectorAll('table.itemType th.sortable').forEach(function (th) {
        th.addEventListener('click', function () {
            var table = th.closest('table'), body = table.tBodies[0];
            var index = Array.prototype.indexOf.call(th.parentNode.children, th);
            var column = index === 0 ? 1 : index + 1; // the first head spans the picture and the information cell
            var ascending = !th.classList.contains('ascending');
            th.parentNode.querySelectorAll('th').forEach(function (h) {
                h.classList.remove('ascending', 'descending');
                var arrow = h.querySelector('.sortingArrow'); if (arrow) arrow.classList.add('inactive');
            });
            th.classList.add(ascending ? 'ascending' : 'descending');
            var arrow = th.querySelector('.sortingArrow');
            if (arrow) { arrow.classList.remove('inactive'); arrow.classList.toggle('down', !ascending); arrow.classList.toggle('up', ascending); }
            var rows = Array.prototype.filter.call(body.rows, function (r) { return r.cells.length > column; });
            rows.sort(function (a, b) {
                var x = a.cells[column].dataset.sort || a.cells[column].textContent, y = b.cells[column].dataset.sort || b.cells[column].textContent;
                var nx = parseFloat(x), ny = parseFloat(y);
                var c = !isNaN(nx) && !isNaN(ny) && String(nx) === String(x).trim() ? nx - ny : String(x).localeCompare(String(y));
                return ascending ? c : -c;
            });
            rows.forEach(function (r) { body.appendChild(r); });
        });
    });
    function layout(name) {
        root.querySelectorAll('table.itemType:not(.accounting)').forEach(function (t) { t.classList.toggle('detailed', name === 'detailed'); t.classList.toggle('compact', name === 'compact'); });
        root.querySelectorAll('[data-layout]').forEach(function (b) { b.classList.toggle('active', b.dataset.layout === name); });
    }
    root.querySelectorAll('[data-layout]').forEach(function (b) {
        b.addEventListener('click', function () { layout(b.dataset.layout); try { localStorage.setItem('ovAuctionLayout', b.dataset.layout); } catch (e) {} });
    });
    try { var saved = localStorage.getItem('ovAuctionLayout'); if (saved) layout(saved); } catch (e) {}
    // Item search: the field slides open over the slot filter and filters the list by name.
    var search = root.querySelector('.itemTypeSearch');
    if (search) {
        var field = search.querySelector('input');
        search.querySelector('.openSearch').addEventListener('click', function () {
            var open = search.classList.contains('closed');
            search.classList.toggle('closed', !open); search.classList.toggle('opened', open);
            if (open) field.focus(); else { field.value = ''; field.dispatchEvent(new Event('input')); }
        });
        field.addEventListener('input', function () {
            var q = field.value.trim().toLowerCase();
            root.querySelectorAll('table.itemType tbody tr[data-name]').forEach(function (r) { r.style.display = !q || r.dataset.name.indexOf(q) !== -1 ? '' : 'none'; });
        });
        field.addEventListener('keydown', function (event) { if (event.key === 'Escape') search.querySelector('.openSearch').click(); });
    }
    // Rarity filter of an item type (all items of this world are common).
    var rarity = root.querySelector('.heroItemRarityFilter');
    if (rarity) rarity.addEventListener('click', function (event) {
        var f = event.target.closest('.filterRarity'); if (!f) return;
        rarity.querySelectorAll('.filterRarity').forEach(function (x) { x.classList.toggle('active', x === f); });
        var key = f.dataset.key;
        root.querySelectorAll('.auctionListWrapper tbody tr[data-name]').forEach(function (r) {
            var item = r.querySelector('.heroItem');
            r.style.display = key === 'all' || (item && item.classList.contains(key)) ? '' : 'none';
        });
    });
    var slots = root.querySelector('.filterSlotsRow.collapsible');
    if (slots) slots.addEventListener('click', function (event) {
        var slot = event.target.closest('.filterSlot');
        if (slot && slots.classList.contains('collapsed')) { event.preventDefault(); slots.classList.remove('collapsed'); }
    });
    var grouped = root.querySelector('.buyViewSwitch input');
    if (grouped) grouped.addEventListener('change', function () {
        window.location.href = 'hero.php?t=4&action=buy&grouped=' + (grouped.checked ? 1 : 0);
    });
})();
</script>
HTML;
    }
}
