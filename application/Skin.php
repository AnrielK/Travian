<?php
/**
 * Graphic packs ("vzhled") of the game.
 *
 *  ''           = the original T4.6 look (gpack/mainPage), unchanged.
 *  'legends655' = Travian Legends 655.3 (gpack/legends655/mainPage, taken over from the main branch):
 *                 the Legends stylesheet with the OpenVillage t46Layout adapter of the main branch, plus the
 *                 adapter of this branch's markup (css/t46-branch.css). The Legends hero is HeroLegends.php.
 *
 * The look is chosen per player (cookie t46_gpack: sidebar box "Settings", Preferences → Game) with the world's
 * default in config.gpack (admin panel → Server settings). The pages are not edited one by one: an output filter
 * swaps the stylesheet, adds the body classes the Legends CSS expects and moves a few blocks (stock bar, village
 * views), so the original look stays exactly as it was when no pack is chosen.
 */

final class Skin
{
    /** id => [name, path of the pack's mainPage folder] */
    const PACKS = [
        ''           => ['name' => 'Travian T4.6 (původní)', 'dir' => 'gpack/mainPage/'],
        'legends655' => ['name' => 'Travian Legends 655.3', 'dir' => 'gpack/legends655/mainPage/'],
    ];
    const COOKIE = 't46_gpack';
    /** Cache buster of the pack's stylesheet. */
    const VERSION = 1;

    private static $current = null;

    /** Packs whose folder is present (a trimmed build may leave one out). */
    public static function available(): array
    {
        $out = [];
        foreach (self::PACKS as $id => $p) {
            if ($id === '' || is_file(APP_MAIN_PATH . '/' . $p['dir'] . 'lang/en-US/compact.css')) $out[$id] = $p;
        }
        return $out;
    }

    public static function valid($id): bool
    {
        return is_string($id) && array_key_exists($id, self::available());
    }

    /** The world's default look (config.gpack). */
    public static function worldDefault(): string
    {
        $id = defined('GAME_GPACK') ? GAME_GPACK : '';
        return self::valid($id) ? $id : '';
    }

    /** The look of this request: the player's choice, else the world's default. */
    public static function current(): string
    {
        if (self::$current === null) {
            $c = $_COOKIE[self::COOKIE] ?? null;
            if ($c === 'default') $c = '';
            self::$current = ($c !== null && self::valid($c)) ? $c : self::worldDefault();
        }
        return self::$current;
    }

    public static function isLegends(): bool
    {
        return self::current() === 'legends655';
    }

    /** Remembers the player's choice ('' = original look, null = follow the world's default). */
    public static function choose($id): void
    {
        if ($id === null || $id === 'world') {
            setcookie(self::COOKIE, '', time() - 3600, '/');
            unset($_COOKIE[self::COOKIE]);
            self::$current = null;
            return;
        }
        if ($id === 'default') $id = '';
        if (!self::valid($id)) return;
        $v = $id === '' ? 'default' : $id;
        setcookie(self::COOKIE, $v, time() + 10 * 365 * 86400, '/');
        $_COOKIE[self::COOKIE] = $v;
        self::$current = null;
    }

    /** <option> list: "world default" and every available pack. */
    public static function options(string $selected): string
    {
        $world = self::PACKS[self::worldDefault()]['name'];
        $html = '<option value="world"' . ($selected === 'world' ? ' selected="selected"' : '') . '>'
            . htmlspecialchars((defined('SETTINGS_GPACK_WORLD') ? SETTINGS_GPACK_WORLD : 'World default') . ' (' . $world . ')') . '</option>';
        foreach (self::available() as $id => $p) {
            $html .= '<option value="' . ($id === '' ? 'default' : $id) . '"' . ($selected === $id && $selected !== 'world' ? ' selected="selected"' : '') . '>'
                . htmlspecialchars($p['name']) . '</option>';
        }
        return $html;
    }

    /** URL prefix of the current pack's mainPage folder (relative to the game root). */
    public static function dir(): string
    {
        return self::PACKS[self::current()]['dir'];
    }

    /** Output filter: points HTML pages at the chosen pack. */
    public static function filter(string $html): string
    {
        if (!self::isLegends()) return $html;
        // pages that start their own ob_gzhandler hand us the compressed page
        if (strncmp($html, "\x1f\x8b", 2) === 0) {
            $plain = @gzdecode($html);
            return $plain === false ? $html : gzencode(self::filter($plain), 6);
        }
        if (stripos($html, '<html') === false) return $html;
        $pack = self::PACKS['legends655']['dir'];

        // Stylesheets: the T4.6 bundle -> the Legends bundle made for this branch (lang.css / fixes.css stay).
        $html = preg_replace(
            '#<link[^>]+href="gpack/mainPage/lang/[a-zA-Z-]+/compact[^"]*\.css"[^>]*>#i',
            '<link href="' . $pack . 'lang/en-US/t46.css?v=' . self::VERSION . '" rel="stylesheet" type="text/css" />',
            $html, 1, $swapped);
        if (!$swapped) return $html;

        // body classes the Legends stylesheet and the t46Layout adapter key on
        $tribe = self::tribeName();
        $html = preg_replace_callback('#<body([^>]*?)class="([^"]*)"#i', function ($m) use ($tribe) {
            return '<body' . $m[1] . 'class="' . $m[2] . ' t46Layout t46-' . $tribe . ' legendsV2 legends655" data-theme="default"';
        }, $html, 1);

        // Legends shows the stock bar in the header, under the navigation.
        $stock = self::cutBlock($html, '<div id="stockBar"');
        if ($stock !== null) {
            $pos = self::blockEnd($html, '<div id="header"');
            if ($pos !== null) {
                $html = substr($html, 0, $pos) . $stock . substr($html, $pos);
            } else {
                $html = str_replace('<div id="contentOuterContainer"', $stock . '<div id="contentOuterContainer"', $html);
            }
        }
        // Legends: village views fill the page (no window), every other page is a "content page" window.
        if (preg_match('#<div id="content" class="([^"]*)"#i', $html, $c) && preg_match('#\bvillage[12]\b#', $c[1])) {
            self::cutBlock($html, '<div class="contentTitle"');
            self::unwrap($html, '<div class="contentContainer"');
            if (strpos($c[1], 'village1') !== false) self::resourceView($html);
            else self::villageView($html, $tribe);
        } else {
            $html = preg_replace('#<div id="contentOuterContainer"( class="([^"]*)")?#i', '<div id="contentOuterContainer" class="$2 contentPage"', $html, 1);
        }
        return $html;
    }

    /** Offset of the closing tag of the element starting with $start (balanced <div>s), before "</div>". */
    private static function blockEnd(string $html, string $start): ?int
    {
        $from = strpos($html, $start);
        if ($from === false) return null;
        $depth = 0;
        if (!preg_match_all('#<div\b|</div\s*>#i', $html, $m, PREG_OFFSET_CAPTURE, $from)) return null;
        foreach ($m[0] as [$tag, $off]) {
            $depth += ($tag[1] === '/') ? -1 : 1;
            if ($depth === 0) return $off;
        }
        return null;
    }

    /** Resource view: the T4.6 info boxes -> the Legends ones (villageInfoWrapper / villageInfobox). */
    private static function resourceView(string &$html): void
    {
        // Legends shows the wall behind windows only, not behind the resource fields.
        $html = preg_replace('#(<div id="background" class="[^"]*?)\bshowWall\b#', '$1', $html, 1);
        $html = str_replace('<div class="village1Content">', '<div class="villageInfoWrapper">', $html);
        self::unwrap($html, '<div id="map_details"');
        $html = preg_replace('#<div class="boxes-(?:tl|tr|tc|ml|mr|mc|bl|br|bc)">\s*</div>\s*#', '', $html);
        while (strpos($html, '<div class="boxes-contents cf">') !== false) self::unwrap($html, '<div class="boxes-contents cf">');
        $html = preg_replace('#<div class="boxes villageList (production|units)"#', '<div class="villageInfobox $1"', $html);
        $html = preg_replace('#<div class="movements"#', '<div class="villageInfobox movements"', $html, 1);
    }

    /** Village view: the Legends stylesheet places the building slots inside #villageContent.<tribe>. */
    private static function villageView(string &$html, string $tribe): void
    {
        $html = preg_replace('#<div id="village_map" class="[^"]*"#', '<div id="villageContent" class="' . $tribe . '"', $html, 1);
        $html = str_replace('#village_map ', '#villageContent ', $html);   // tutorial highlights
    }

    /** Removes the start and end tag of the element starting with $start, keeping its children. */
    private static function unwrap(string &$html, string $start): void
    {
        $from = strpos($html, $start);
        $end = self::blockEnd($html, $start);
        if ($from === false || $end === null) return;
        $close = strpos($html, '>', $end) + 1;
        $open = strpos($html, '>', $from) + 1;
        $html = substr($html, 0, $from) . substr($html, $open, $end - $open) . substr($html, $close);
    }

    /** Removes the element starting with $start from $html and returns it. */
    private static function cutBlock(string &$html, string $start): ?string
    {
        $from = strpos($html, $start);
        $end = self::blockEnd($html, $start);
        if ($from === false || $end === null) return null;
        $end = strpos($html, '>', $end) + 1;
        $block = substr($html, $from, $end - $from);
        $html = substr($html, 0, $from) . substr($html, $end);
        return $block;
    }

    private static function tribeName(): string
    {
        global $session;
        $t = isset($session) && is_object($session) ? (int)$session->tribe : 1;
        return [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 6 => 'egyptian', 7 => 'hun'][$t] ?? 'roman';
    }
}
