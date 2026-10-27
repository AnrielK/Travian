<?php
/**
 * Quick links of the Legends village list ("Dostupné rychlé odkazy pro seznam vesnic"): up to four round buttons in
 * the header of the village list, chosen in a dialog. The links are the same for all villages; state and tooltip
 * follow the active village (a link is greyed when the building is missing there). They are a Travian PLUS feature:
 * without PLUS a click opens the PLUS dialog.
 */
final class LegendsQuickLinks
{
    const DEFAULT_SLOTS = ['rallySend', 'marketSend', '', ''];

    /** key => [building ids, subtitle constant, English subtitle, url (%d = slot, null = overview), icon] */
    const LINKS = [
        'smithy'        => [[12], 'QL_MANAGE', 'Manage', null, 'smithy'],
        'rallyOverview' => [[16], 'QL_OVERVIEW', 'Overview', null, 'overview'],
        'rallySend'     => [[16], 'QL_SEND_TROOPS', 'Send troops', 'build.php?t=2&id=%d', 'swords'],
        'rallyFarmlist' => [[16], 'QL_FARMLIST', 'Farm list', 'build.php?t=99&id=%d', 'farmlist'],
        'marketSend'    => [[17], 'QL_SEND_RESOURCES', 'Send resources', 'build.php?t=5&id=%d', 'market'],
        'marketRoutes'  => [[17], 'QL_TRADE_ROUTES', 'Trade routes', 'build.php?t=4&id=%d', 'routes'],
        'marketBuy'     => [[17], 'QL_BUY', 'Buy', 'build.php?t=1&id=%d', 'scales'],
        'barracks'      => [[19], 'QL_MANAGE', 'Manage', null, 'barracks'],
        'greatBarracks' => [[29], 'QL_MANAGE', 'Manage', null, 'barracks'],
        'stable'        => [[20], 'QL_MANAGE', 'Manage', null, 'stable'],
        'greatStable'   => [[30], 'QL_MANAGE', 'Manage', null, 'stable'],
        'workshop'      => [[21], 'QL_MANAGE', 'Manage', null, 'workshop'],
        'townHall'      => [[24], 'QL_CELEBRATIONS', 'Celebrations', null, 'celebration'],
        'trapper'       => [[36], 'QL_MANAGE', 'Manage', null, 'trap'],
        'heroMansion'   => [[37], 'QL_MANAGE', 'Manage', null, 'hero'],
        'residence'     => [[25, 26, 44], 'QL_TRAIN', 'Train', null, 'crown'],
    ];

    const ICONS = [
        'smithy' => ['0 0 20 20', 'M1 5h12c0 2.5 2 4 6 4v2c-3 0-5 .5-6 2v2h2v2H4v-2h2v-2C5 11.5 3 10.5 1 10z'],
        'farmlist' => ['0 0 20 20', 'M2 3h3v3H2zm5 .5h11v2H7zM2 8.5h3v3H2zm5 .5h11v2H7zM2 14h3v3H2zm5 .5h11v2H7z'],
        'routes' => ['0 0 20 20', 'M2 5h11V2l5 4-5 4V7H2zm16 8H7v3l-5-4 5-4v3h11z'],
        'scales' => ['0 0 20 20', 'M9 2h2v2l5 1.4V7h-.5L18 13a3.5 3.5 0 0 1-7 0l2.5-6h-1.3L11 6.6V16h3v2H6v-2h3V6.6L7.8 7H6.5L9 13a3.5 3.5 0 0 1-7 0l2.5-6H4V5.4L9 4z'],
        'celebration' => ['0 0 20 20', 'M7 2h11v12a3 3 0 1 1-2-2.8V6H9v10a3 3 0 1 1-2-2.8z'],
        'trap' => ['0 0 20 20', 'M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm0 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zM9 7h2v6H9z'],
        'hero' => ['0 0 20 20', 'M10 1c4.4 0 7 3.4 7 8v8h-4v-6H7v6H3V9c0-4.6 2.6-8 7-8zM9 4v5h2V4z'],
        'crown' => ['0 0 20 20', 'M2 5l4 3.5L10 2l4 6.5L18 5l-2 10H4zm2 11.5h12V19H4z'],
        'swords' => ['0 0 20 19.06', 'M16.22 13.08l2.14-2.14-1.37-1.36-1.63 1.63-2.15-2.15 4.74-4.73L19.06 0l-4.32 1.11L10 5.85 5.26 1.11.94 0l1.11 4.33 4.74 4.73-2.15 2.15-1.63-1.63-1.37 1.36 2.14 2.14L0 16.86l2.21 2.2 3.78-3.77 2.13 2.13 1.37-1.37-1.63-1.63L10 12.28l2.14 2.14-1.63 1.63 1.37 1.37 2.14-2.13 3.77 3.77 2.21-2.2-3.78-3.78z'],
    ];

    public static function icon(string $name): string
    {
        $legends = ['overview' => 'sbOverview', 'market' => 'sbMarket', 'barracks' => 'sbBarracks', 'stable' => 'sbStable', 'workshop' => 'sbWorkshop'];
        if (isset($legends[$name])) return LegendsUi::svg($legends[$name], $name);
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

    /** Data of all links for the active village: key => [title, subtitle, url|null, icon html]. */
    public static function all(array $resarray): array
    {
        global $lang;
        $out = [];
        foreach (self::LINKS as $key => [$gids, $subConst, $subDefault, $url, $icon]) {
            $names = array_map(function ($g) use ($lang) { return (string)($lang['buildings'][$g] ?? $g); }, $gids);
            $slot = self::slot($key, $resarray);
            $out[$key] = [
                'title' => implode('/', $names),
                'sub' => LegendsUi::t($subConst, $subDefault),
                'url' => $slot === null ? null : ($url === null ? 'build.php?id=' . $slot : sprintf($url, $slot)),
                'icon' => self::icon($icon),
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
        return '<a class="layoutButton buttonFramed withIcon round green quickLink' . ($enabled ? '' : ' disabled') . '" data-key="' . $e($key) . '"'
            . ' href="' . $e($href) . '" title="' . $e($title) . '"' . ($onclick !== '' ? ' onclick="' . $onclick . '"' : '') . '>' . $link['icon'] . '</a>';
    }
}
