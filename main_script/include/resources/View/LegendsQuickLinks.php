<?php

namespace resources\View;

use Core\Database\DB;
use Core\Session;
use Core\Village;

/**
 * Quick links of the Travian Legends 655 look (is_hero_v2()): up to four round buttons in the header of the village
 * list ("Available quick links for the village list", the same for all villages) and up to five in the box of the
 * active village (chosen per village), both chosen in a dialog (js/Game/Legends/quickLinks.js, ajax.php?cmd=quickLinks).
 * State and tooltip follow the active village (a link is greyed when the building is missing there). They are a Travian
 * PLUS feature like the classic direct links: without PLUS a click opens the PLUS dialog.
 *
 * Stored in ov_quick_links (uid, kid = 0 for the village list, else the village), created on first use.
 */
class LegendsQuickLinks
{
    public const TABLE = 'ov_quick_links';
    public const LIST_SLOTS = 4;
    public const VILLAGE_SLOTS = 5;
    /** As in Travian Legends a new account starts without quick links (only the fixed overview button of the list). */
    public const DEFAULT_LIST = ['', '', '', ''];
    public const DEFAULT_VILLAGE = ['', '', '', '', ''];

    /**
     * key => [building ids, subtitle key, url (%d = building slot, null = the building), icon, title key, gold]
     * in the order of the Travian Legends dialog; gold = a Gold Club feature (golden button like in Legends).
     */
    public const LINKS = [
        'smithy'        => [[13], 'manage', null, 'smithy', 'smithy', false],
        'rallyOverview' => [[16], 'overview', 'build.php?id=%d&tt=1', 'banner', 'rallyPoint', false],
        'rallySend'     => [[16], 'sendTroops', 'build.php?id=%d&tt=2', 'swords', 'rallyPoint', false],
        'rallyFarmlist' => [[16], 'farmList', 'build.php?id=%d&tt=99', 'scroll', 'rallyPoint', true],
        'marketSend'    => [[17], 'sendResources', 'build.php?id=%d&t=5', 'market', 'marketplace', false],
        'marketRoutes'  => [[17], 'tradeRoutes', 'build.php?id=%d&t=0', 'wheel', 'marketplace', true],
        'marketBuy'     => [[17], 'buy', 'build.php?id=%d&t=1', 'scales', 'marketplace', false],
        'barracks'      => [[19], 'manage', null, 'barracks', 'barracks', false],
        'greatBarracks' => [[29], 'manage', null, 'barracks', 'greatBarracks', false],
        'stable'        => [[20], 'manage', null, 'stable', 'stable', false],
        'greatStable'   => [[30], 'manage', null, 'stable', 'greatStable', false],
        'workshop'      => [[21], 'manage', null, 'workshop', 'workshop', false],
        'townHall'      => [[24], 'celebrations', null, 'celebration', 'townHall', false],
        'heroMansion'   => [[37], 'manage', null, 'tree', 'heroMansion', false],
        'hospital'      => [[46], 'manage', null, 'bandage', 'hospital', false],
        'residence'     => [[25, 26, 44], 'train', null, 'sack', 'residence', false],
    ];

    private const ICONS = [
        'smithy' => ['0 0 20 20', 'M1 6h12c0 2.3 1.8 3.6 6 3.8V12c-3 0-4.6.8-5.3 2.3V15H15v2.5H5V15h1.8v-.7C6 12.6 4 11.4 1 11zM13.5 1.2l1.4 1.4-2.6 2.6-1.4-1.4z'],
        'banner' => ['0 0 20 20', 'M2.5 1.5h15v2h-2v14.2L10 13.9l-5.5 3.8V3.5h-2zm5 4v5.3L10 9.2l2.5 1.6V5.5z'],
        'scroll' => ['0 0 20 20', 'M5.5 1.5h9.8a3 3 0 0 1 3 3v1.8h-3.6v9.6a2.6 2.6 0 0 1-2.6 2.6H3.8a2.6 2.6 0 0 1-2.6-2.6v-1.6h9.6V4.5a1.2 1.2 0 0 0-1.2-1.2H5.5zM5 6h4v1.6H5zm0 3h4v1.6H5z'],
        'wheel' => ['0 0 20 20', 'M10 1a9 9 0 1 1 0 18 9 9 0 0 1 0-18zm0 2.3a6.7 6.7 0 1 0 0 13.4 6.7 6.7 0 0 0 0-13.4zm-.9.9h1.8v4.1l2.9-2.9 1.3 1.3-2.9 2.9h4.1v1.8h-4.1l2.9 2.9-1.3 1.3-2.9-2.9v4.1H9.1v-4.1l-2.9 2.9-1.3-1.3 2.9-2.9H3.7V9.1h4.1L4.9 6.2l1.3-1.3 2.9 2.9z'],
        'scales' => ['0 0 20 20', 'M9 2h2v2l5 1.4V7h-.5L18 13a3.5 3.5 0 0 1-7 0l2.5-6h-1.3L11 6.6V16h3v2H6v-2h3V6.6L7.8 7H6.5L9 13a3.5 3.5 0 0 1-7 0l2.5-6H4V5.4L9 4z'],
        'celebration' => ['0 0 20 20', 'M7 2h11v12a3 3 0 1 1-2-2.8V6H9v10a3 3 0 1 1-2-2.8z'],
        'tree' => ['0 0 20 20', 'M9.6 1l4.6 5.6h-2.3l3.4 4.8h-2.8l3 4.1h-5.2V19H8.1v-3.5H2.9l3-4.1H3.1l3.4-4.8H4.2zM15.5 13a2.6 2.6 0 1 1 0 5.2 2.6 2.6 0 0 1 0-5.2z'],
        'bandage' => ['0 0 20 20', 'M13.1 1.6a3.1 3.1 0 0 1 4.4 0l.9.9a3.1 3.1 0 0 1 0 4.4L6.9 18.4a3.1 3.1 0 0 1-4.4 0l-.9-.9a3.1 3.1 0 0 1 0-4.4zM1.6 6.9a3.1 3.1 0 0 1 0-4.4l.9-.9a3.1 3.1 0 0 1 4.4 0L9 3.7 3.7 9zm16.8 6.2a3.1 3.1 0 0 1 0 4.4l-.9.9a3.1 3.1 0 0 1-4.4 0L11 16.3l5.3-5.3zM9 8.2a.9.9 0 1 0 0 1.8.9.9 0 0 0 0-1.8zm2 2a.9.9 0 1 0 0 1.8.9.9 0 0 0 0-1.8z'],
        'sack' => ['0 0 20 20', 'M6.5 1.5h7l-1.8 3.3c3.6 1.6 5.8 5.3 5.8 8.9 0 3.1-2.8 4.8-7.5 4.8s-7.5-1.7-7.5-4.8c0-3.6 2.2-7.3 5.8-8.9zM15.2 1.8l1.6 1.2-3.1 3.6-1-1.2z'],
        'swords' => ['0 0 20 19.06', 'M16.22 13.08l2.14-2.14-1.37-1.36-1.63 1.63-2.15-2.15 4.74-4.73L19.06 0l-4.32 1.11L10 5.85 5.26 1.11.94 0l1.11 4.33 4.74 4.73-2.15 2.15-1.63-1.63-1.37 1.36 2.14 2.14L0 16.86l2.21 2.2 3.78-3.77 2.13 2.13 1.37-1.37-1.63-1.63L10 12.28l2.14 2.14-1.63 1.63 1.37 1.37 2.14-2.13 3.77 3.77 2.21-2.2-3.78-3.78z'],
    ];

    private static bool $tableReady = false;

    public static function ensureTable(): void
    {
        if (self::$tableReady) {
            return;
        }
        DB::getInstance()->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE . '` (
            `uid` INT UNSIGNED NOT NULL,
            `kid` INT UNSIGNED NOT NULL,
            `links` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`uid`, `kid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        self::$tableReady = true;
    }

    /** The slots of a set: kid 0 = the village list (4), else the village (5); '' = empty slot. */
    public static function slots(int $uid, int $kid): array
    {
        self::ensureTable();
        $count = $kid ? self::VILLAGE_SLOTS : self::LIST_SLOTS;
        $row = DB::getInstance()->run('SELECT links FROM `' . self::TABLE . '` WHERE uid = ? AND kid = ?', [$uid, $kid]);
        $row = $row ? $row->get_result()->fetch_row() : null;
        $saved = $row ? json_decode((string)$row[0], true) : null;
        $keys = is_array($saved) ? $saved : ($kid ? self::DEFAULT_VILLAGE : self::DEFAULT_LIST);
        return self::clean($keys, $count);
    }

    /** Stores a set (unknown keys and repeated links are dropped). */
    public static function save(int $uid, int $kid, array $keys): array
    {
        self::ensureTable();
        $keys = self::clean($keys, $kid ? self::VILLAGE_SLOTS : self::LIST_SLOTS);
        DB::getInstance()->run('REPLACE INTO `' . self::TABLE . '` (uid, kid, links) VALUES (?, ?, ?)', [$uid, $kid, json_encode($keys)]);
        return $keys;
    }

    private static function clean(array $keys, int $count): array
    {
        $out = [];
        $seen = [];
        for ($i = 0; $i < $count; ++$i) {
            $key = is_string($keys[$i] ?? null) ? $keys[$i] : '';
            if ($key !== '' && (!isset(self::LINKS[$key]) || isset($seen[$key]))) {
                $key = '';
            }
            $seen[$key] = true;
            $out[] = $key;
        }
        return $out;
    }

    public static function icon(string $name): string
    {
        $legends = ['market' => 'sbMarket', 'barracks' => 'sbBarracks', 'stable' => 'sbStable', 'workshop' => 'sbWorkshop'];
        if (isset($legends[$name])) {
            // the sidebar icons are nested svgs (outline + icon); one svg with the inner viewBox centres in the button
            $svg = LegendsUi::svg($legends[$name]);
            if (preg_match('#<svg viewBox="([^"]+)" class="outline">(.*?)</svg>#s', $svg, $m)) {
                return LegendsUi::icon($m[1], $m[2], $name);
            }
            return LegendsUi::svg($legends[$name], $name);
        }
        [$box, $path] = self::ICONS[$name] ?? self::ICONS['swords'];
        return LegendsUi::icon($box, '<path d="' . $path . '"/>', $name);
    }

    /** Building slot of the link in the active village (rally point: 39), or null when the building is missing. */
    private static function slot(array $gids): ?int
    {
        $village = Village::getInstance();
        if ($gids === [16]) {
            return (int)($village->getField(39)['item_id'] ?? 0) === 16 ? 39 : null;
        }
        foreach ($gids as $gid) {
            $slot = (int)$village->findBuildingByGid($gid);
            if ($slot > 0) {
                return $slot;
            }
        }
        return null;
    }

    /** All links for the active village: key => [title, sub, url|null, icon html, gold]. */
    public static function all(): array
    {
        $out = [];
        foreach (self::LINKS as $key => [$gids, $sub, $url, $icon, $title, $gold]) {
            $names = implode('/', array_map(static fn($g) => T('Buildings', $g . '.title'), $gids));
            $slot = self::slot($gids);
            $out[$key] = [
                'title' => T('QuickLinks', 'name.' . $title, $names),
                'sub' => T('QuickLinks', 'sub.' . $sub, $sub),
                'url' => $slot === null ? null : ($url === null ? 'build.php?id=' . $slot : sprintf($url, $slot)),
                'icon' => LegendsQuickLinkIcons::svg($key) ?? self::icon($icon),
                'gold' => $gold,
            ];
        }
        return $out;
    }

    /** Round button of a link in a slot or in the dialog. */
    public static function button(string $key, array $link): string
    {
        $session = Session::getInstance();
        $plus = $session->hasPlus() || $session->isAdmin();
        $enabled = $link['url'] !== null;
        $href = $enabled && $plus ? $link['url'] : '#';
        // without PLUS the game's own PLUS dialog (like the classic direct links of the active village box)
        $onclick = !$enabled ? 'return false;' : ($plus ? '' : "jQuery(window).trigger('buttonClicked', [this, {type: 'gold', plusDialog: {featureKey: 'directLinks', infoIcon: ''}}]); return false;");
        return '<a class="layoutButton buttonFramed withIcon round ' . (!empty($link['gold']) ? 'gold' : 'green') . ' quickLink' . ($enabled ? '' : ' disabled') . '"'
            . ' data-key="' . LegendsUi::e($key) . '" href="' . LegendsUi::e($href) . '" title="' . LegendsUi::e($enabled ? $link['title'] . ' – ' . $link['sub'] : T('QuickLinks', 'notAvailable', 'The building is not available in the active village')) . '"'
            . ($onclick !== '' ? ' onclick="' . LegendsUi::e($onclick) . '"' : '') . '>' . $link['icon'] . '</a>';
    }

    /** The slots of a set as markup. */
    public static function slotsHtml(array $keys, array $links): string
    {
        $indicator = LegendsQuickLinkIcons::ring();
        $plusIcon = '<svg viewBox="0 0 18 18"><path d="M16 10h-6v6H8v-6H2V8h6V2h2v6h6z"/></svg>';
        $html = '';
        foreach ($keys as $i => $key) {
            $html .= '<div class="quickLinkSlot" data-slot="' . $i . '">' . $indicator . '<div class="slot">' . $plusIcon . '</div>'
                . ($key !== '' && isset($links[$key]) ? self::button($key, $links[$key]) : '') . '</div>';
        }
        return $html;
    }

    /** The "…" button that opens the editor of a set ($scope 'list' or 'village'); an X while editing. */
    public static function settingsButton(string $scope): string
    {
        $title = $scope === 'village' ? T('QuickLinks', 'villageEdit', 'Edit the village\'s quick links') : T('QuickLinks', 'edit', 'Edit quick links');
        return '<button type="button" class="layoutButton buttonFramed withIcon round settings gold' . ($scope === 'village' ? ' small' : '') . '" title="' . LegendsUi::e($title) . '"'
            . ' onclick="LegendsQuickLinks.toggle(undefined, \'' . $scope . '\');">' . LegendsUi::svg('sbMore')
            . '<svg class="closeIcon" viewBox="0 0 20 20"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"/></svg></button>';
    }

    /** window.LegendsQuickLinksConfig for the editor. */
    public static function config(array $links): string
    {
        return json_encode([
            'T' => [
                'listTitle' => T('QuickLinks', 'listTitle', 'Available quick links for the village list'),
                'listText' => T('QuickLinks', 'listText', 'Drag up to 4 buttons into the slots of the village list to customise your quick links.'),
                'villageTitle' => sprintf(T('QuickLinks', 'villageTitle', 'Available quick links for the village %s'), Village::getInstance()->getName()),
                'villageText' => T('QuickLinks', 'villageText', 'Drag up to 5 buttons into the slots of the active village to customise your quick links.'),
                'close' => T('Global', 'General.close', 'Close'),
                'used' => T('QuickLinks', 'used', 'This quick link is already in use'),
            ],
            'ring' => LegendsQuickLinkIcons::ring(),
            'links' => array_map(static fn($key) => ['key' => $key, 'title' => $links[$key]['title'], 'sub' => $links[$key]['sub'],
                'html' => self::button($key, $links[$key])], array_keys($links)),
        ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS);
    }
}
