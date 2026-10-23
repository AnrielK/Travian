<?php
/**
 * Graphic packs (gpack) – lets each player choose between the current T4.4 look and the older
 * Travian T4.0 packs (taken from the Travian-T4 "SHADOW" edition).
 *
 *  - The choice is stored in the "gpack" cookie (per browser, so the 2nd-player window can use another look).
 *  - Options → Graphic pack (options.php?s=3) shows the switcher.
 *  - The original packs (gpack/legacy/packs/) were written for the old T4.0 markup and do not fit the T4.4 pages,
 *    so their graphics are applied by a skin stylesheet gpack/legacy/compat/<id>.css (assets in compat/<id>/),
 *    loaded after the normal T4.4 stylesheets and scoped to <html class="gpack-<id>">.
 *  - Default for new browsers: env TRAVIAN_GPACK (t44 | t40 | basic), otherwise t44.
 */

const GPACK_COOKIE = 'gpack';

function gpack_list(): array
{
    return [
        't44'   => ['name' => 'Travian 4.4 (default)', 'desc' => 'The current T4.4 look.',
                    'preview' => 'gpack/legacy/preview/t44.jpg'],
        't40'   => ['name' => 'Travian 4.0 classic',   'desc' => 'Original T4.0 look – landscape with trees, round menu, wooden signs (gpack travian_Travian_4.0_41).',
                    'preview' => 'gpack/legacy/preview/t40.jpg'],
        'basic' => ['name' => 'Travian basic',         'desc' => 'Light, plain classic look – white page, classic menu icons (gpack travian_basic).',
                    'preview' => 'gpack/legacy/preview/basic.jpg'],
    ];
}

function gpack_current(): string
{
    static $cur = null;
    if ($cur !== null) return $cur;
    $id = $_COOKIE[GPACK_COOKIE] ?? (getenv('TRAVIAN_GPACK') ?: 't44');
    if (!isset(gpack_list()[$id])) $id = 't44';
    return $cur = $id;
}

function gpack_set(string $id): bool
{
    if (!isset(gpack_list()[$id]) || headers_sent()) return false;
    setcookie(GPACK_COOKIE, $id, ['expires' => time() + 10 * 365 * 86400, 'path' => '/', 'samesite' => 'Lax']);
    $_COOKIE[GPACK_COOKIE] = $id;
    return true;
}

function gpack_is_legacy(): bool
{
    return gpack_current() !== 't44';
}

/** <link> tags for the <head>. */
function gpack_css_links(): string
{
    $lang = defined('LANG_UR') ? LANG_UR : 'en';
    $v = '?rev14';
    $out = [];
    // T4.4 stylesheets are always the base – they style the T4.4 markup (village view, buildings, hero…).
    $out[] = "gpack/lang/$lang/compact.css";
    $out[] = "gpack/lang/$lang/lang.css";
    $out[] = "gpack/lang/$lang/fixes.css$v";
    if (gpack_is_legacy()) {
        $id = gpack_current();
        // the old look is applied by a theme sheet that re-skins the T4.4 layout with the legacy pack's images
        $out[] = "gpack/legacy/compat/$id.css$v";
    }
    $html = '';
    foreach ($out as $href) {
        $html .= '    <link href="' . $href . '" rel="stylesheet" type="text/css"/>' . "\n";
    }
    return $html . '    <script>document.documentElement.className += " gpack-' . gpack_current() . '";</script>' . "\n";
}
