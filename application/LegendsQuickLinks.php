<?php
/**
 * Quick links of the Legends look: up to four round buttons in the header of the village list ("Dostupné rychlé odkazy
 * pro seznam vesnic", the same for all villages) and up to five in the box of the active village ("… pro vesnici",
 * chosen per village), both chosen in a dialog. The links are the same for all villages; state and tooltip
 * follow the active village (a link is greyed when the building is missing there). They are a Travian PLUS feature:
 * without PLUS a click opens the PLUS dialog.
 */
final class LegendsQuickLinks
{
    const DEFAULT_SLOTS = ['rallySend', 'marketSend', '', ''];
    /** Quick links of the active village box (five slots per village). */
    const DEFAULT_VILLAGE_SLOTS = ['', 'marketSend', 'barracks', 'stable', 'workshop'];

    /**
     * key => [building ids, subtitle constant, English subtitle, url (%d = slot, null = overview), icon, title constant, gold]
     * in the order of the Travian Legends dialog; gold = a Gold Club feature (golden button like in Legends).
     */
    const LINKS = [
        'smithy'        => [[12], 'QL_MANAGE', 'Manage', null, 'smithy', 'QLN_SMITHY', false],
        'rallyOverview' => [[16], 'QL_OVERVIEW', 'Overview', 'build.php?t=1&id=%d', 'banner', 'QLN_RALLY', false],
        'rallySend'     => [[16], 'QL_SEND_TROOPS', 'Send troops', 'build.php?t=2&id=%d', 'swords', 'QLN_RALLY', false],
        'rallyFarmlist' => [[16], 'QL_FARMLIST', 'Farm list', 'build.php?t=99&id=%d', 'scroll', 'QLN_RALLY', true],
        'marketSend'    => [[17], 'QL_SEND_RESOURCES', 'Send resources', 'build.php?t=5&id=%d', 'market', 'QLN_MARKET', false],
        'marketRoutes'  => [[17], 'QL_TRADE_ROUTES', 'Trade routes', 'build.php?t=4&id=%d', 'wheel', 'QLN_MARKET', true],
        'marketBuy'     => [[17], 'QL_BUY', 'Buy', 'build.php?t=1&id=%d', 'scales', 'QLN_MARKET', false],
        'barracks'      => [[19], 'QL_MANAGE', 'Manage', null, 'barracks', 'QLN_BARRACKS', false],
        'greatBarracks' => [[29], 'QL_MANAGE', 'Manage', null, 'barracks', 'QLN_GREAT_BARRACKS', false],
        'stable'        => [[20], 'QL_MANAGE', 'Manage', null, 'stable', 'QLN_STABLE', false],
        'greatStable'   => [[30], 'QL_MANAGE', 'Manage', null, 'stable', 'QLN_GREAT_STABLE', false],
        'workshop'      => [[21], 'QL_MANAGE', 'Manage', null, 'workshop', 'QLN_WORKSHOP', false],
        'townHall'      => [[24], 'QL_CELEBRATIONS', 'Celebrations', null, 'celebration', 'QLN_TOWN_HALL', false],
        'heroMansion'   => [[37], 'QL_MANAGE', 'Manage', null, 'tree', 'QLN_HERO_MANSION', false],
        'hospital'      => [[46], 'QL_MANAGE', 'Manage', null, 'bandage', 'QLN_HOSPITAL', false],
        'residence'     => [[25, 26, 44], 'QL_TRAIN', 'Train', null, 'sack', 'QLN_RESIDENCE', false],
    ];

    const ICONS = [
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

    /** Slot of the first building of the link in the active village (rally point: always 39), or null. */
    public static function slot(string $key, array $resarray): ?int
    {
        $gids = self::LINKS[$key][0];
        if ($gids === [16]) return (int)($resarray['f39t'] ?? 0) === 16 ? 39 : null;
        for ($i = 19; $i <= 40; $i++) {
            if (in_array((int)($resarray['f' . $i . 't'] ?? 0), $gids, true)) return $i;
        }
        return null;
    }

    /** Data of all links for the active village: key => [title, subtitle, url|null, icon html, gold]. */
    public static function all(array $resarray): array
    {
        global $lang;
        $out = [];
        foreach (self::LINKS as $key => [$gids, $subConst, $subDefault, $url, $icon, $titleConst, $gold]) {
            $names = array_map(function ($g) use ($lang) { return (string)($lang['buildings'][$g] ?? $g); }, $gids);
            $slot = self::slot($key, $resarray);
            $out[$key] = [
                'title' => LegendsUi::t($titleConst, implode('/', $names)),
                'sub' => LegendsUi::t($subConst, $subDefault),
                'url' => $slot === null ? null : ($url === null ? 'build.php?id=' . $slot : sprintf($url, $slot)),
                'icon' => self::icon($icon),
                'gold' => $gold,
            ];
        }
        return $out;
    }

    /** Round button of a link in a slot or in the dialog. */
    public static function button(string $key, array $link, bool $plus): string
    {
        $e = [LegendsUi::class, 'e'];
        $title = $link['title'] . ' – ' . $link['sub'];
        $enabled = $link['url'] !== null;
        $href = !$enabled ? '#' : ($plus ? $link['url'] : '#');
        $onclick = !$enabled ? 'return false;' : ($plus ? '' : "LegendsPlus.open('directLinks');return false;");
        return '<a class="layoutButton buttonFramed withIcon round ' . (!empty($link['gold']) ? 'gold' : 'green') . ' quickLink' . ($enabled ? '' : ' disabled') . '" data-key="' . $e($key) . '"'
            . ' href="' . $e($href) . '" title="' . $e($title) . '"' . ($onclick !== '' ? ' onclick="' . $onclick . '"' : '') . '>' . $link['icon'] . '</a>';
    }
}
