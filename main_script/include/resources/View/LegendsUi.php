<?php

namespace resources\View;

use Game\Hero\HeroItems;

/**
 * Markup of the shared Travian Legends 655 components (buttons, progress bars, hero items, pagination, coins),
 * as the Legends client renders them, so the stylesheet of a 'heroV2' graphic pack styles them as they are.
 */
class LegendsUi
{
    /** Item slot of each item category (items.btype). */
    public const SLOTS = [1 => 'helmet', 2 => 'body', 3 => 'leftHand', 4 => 'rightHand', 5 => 'shoes', 6 => 'horse'];

    /** Order of the equipment slots in the hero window. */
    public const EQUIPMENT = ['helmet', 'body', 'shoes', 'leftHand', 'rightHand', 'horse', 'bag'];

    /** Slot filters of the inventory and the auctions (filter key => item slots it shows). */
    public const FILTERS = ['all', 'leftHand', 'rightHand', 'helmet', 'body', 'shoes', 'bag', 'horse'];

    public static function e($s): string
    {
        return htmlspecialchars((string)$s, ENT_QUOTES);
    }

    /** textButtonV2 (Legends button component). */
    public static function button(string $content, array $o = []): string
    {
        $class = 'textButtonV2 ' . (!empty($o['secondary']) ? 'buttonSecondary' : 'buttonFramed');
        $class .= !empty($o['class']) ? ' ' . $o['class'] : '';
        $class .= !empty($o['disabledClass']) ? ' disabled' : '';
        $class .= !empty($o['round']) ? ' round' : ' rectangle';
        $class .= !empty($o['icon']) ? ' withIcon' : ' withText';
        if (!empty($o['toggle'])) {
            $class .= ' toggle' . (!empty($o['activeNotClickable']) ? ' activeNotClickable' : '') . (!empty($o['active']) ? ' active' : '');
        }
        if (isset($o['gold'])) {
            $class .= ' gold';
        } elseif (!empty($o['toggle']) && empty($o['secondary'])) {
            $class .= !empty($o['active']) ? ' orange' : ' grey';
        } else {
            $class .= ' ' . ($o['appearance'] ?? 'green');
        }
        $attributes = '';
        foreach (['id', 'title', 'href', 'onclick', 'name', 'value', 'type'] as $key) {
            if (isset($o[$key])) {
                $attributes .= ' ' . $key . '="' . self::e($o[$key]) . '"';
            }
        }
        foreach ($o['data'] ?? [] as $key => $value) {
            $attributes .= ' data-' . $key . '="' . self::e($value) . '"';
        }
        if (!empty($o['disabled'])) {
            $attributes .= ' disabled';
        }
        $inner = !empty($o['icon']) ? $content : '<div>' . $content
            . (isset($o['gold']) ? self::goldCoin('goldCoin') . ' <span class="goldValue">' . self::e($o['gold']) . '</span>' : '') . '</div>';
        if (isset($o['href'])) {
            return '<a class="' . $class . '"' . $attributes . '>' . $inner . '</a>';
        }
        return '<button class="' . $class . '"' . (isset($o['type']) ? '' : ' type="button"') . $attributes . '>' . $inner . '</button>';
    }

    /** SVG icon drawn twice (outline + icon), the way Legends icon buttons carry their glyph. */
    public static function icon(string $viewBox, string $path, string $class = ''): string
    {
        return '<svg viewBox="' . $viewBox . '"' . ($class ? ' class="' . $class . '"' : '') . '>'
            . '<g class="outline">' . $path . '</g><g class="icon">' . $path . '</g></svg>';
    }

    public static function plusIcon(): string
    {
        return self::icon('0 0 18 18', '<path d="M16 10h-6v6H8v-6H2V8h6V2h2v6h6z"/>');
    }

    public static function minusIcon(): string
    {
        return self::icon('0 0 18 6', '<path d="M16 4H2V2h14z"/>');
    }

    /** Arrow decoration at the ends of a progress bar (Legends "decoration" svg with the frame gradient). */
    private static function barDecoration(string $class): string
    {
        static $n = 0;
        $id = 'pbd' . (++$n) . substr(md5(uniqid('', true)), 0, 4);
        $stops = '';
        foreach ([5, 13, 32, 48, 72, 93, 100] as $i => $offset) {
            $stops .= '<stop class="colorStop' . ($i + 1) . '" offset="' . $offset . '%"/>';
        }
        return '<svg class="decoration ' . $class . '" viewBox="0 0 11.97 19" id="svg-' . $id . '">'
            . '<style>#svg-' . $id . '{--linearGradient:url(#svg-gradient-' . $id . ');}</style>'
            . '<defs><linearGradient class="frameGradient" id="svg-gradient-' . $id . '" spreadMethod="pad" gradientTransform="rotate(90)">' . $stops . '</linearGradient></defs>'
            . '<path d="M3.94 19c-.53 0-.94-.45-.94-1.03V1.03C3 .45 3.42 0 3.94 0c.27 0 .51.11.68.33l7.11 8.47c.32.4.32.99 0 1.39l-7.11 8.47c-.18.21-.42.33-.68.33Z"/>'
            . '<path d="M1.13 17.64C.5 17.64 0 17.08 0 16.39V2.6c0-.69.5-1.25 1.13-1.25.31 0 .61.14.83.4l5.81 6.89c.4.47.4 1.23 0 1.7l-5.81 6.9c-.21.26-.5.4-.82.4h-.01Z"/>'
            . '</svg>';
    }

    /**
     * Progress bar. $o: color, primary (percent), secondary (percent, optional), title,
     * buttons (plus/minus with data-attribute), plusDisabled, minusDisabled.
     */
    public static function progressBar(array $o): string
    {
        $color = $o['color'] ?? 'green';
        $html = '<div class="progressBar preventMobileSwipeNavigation' . (!empty($o['disabled']) ? ' disabled' : '') . '">';
        $html .= '<div class="bar"' . (isset($o['title']) ? ' title="' . self::e($o['title']) . '"' : '') . '>';
        $html .= self::barDecoration('start');
        $html .= '<div class="filling primary ' . $color . '" style="width:' . max(0, min(100, (float)($o['primary'] ?? 0))) . '%"></div>';
        if (array_key_exists('secondary', $o)) {
            $html .= '<div class="filling secondary ' . $color . '" style="width:' . max(0, min(100, (float)$o['secondary'])) . '%"></div>';
        }
        $html .= self::barDecoration('end') . '</div>';
        if (!empty($o['buttons'])) {
            $html .= self::button(self::plusIcon(), ['icon' => true, 'class' => 'plus', 'disabled' => !empty($o['plusDisabled']), 'data' => ['attribute' => $o['buttons'], 'step' => 1]]);
            $html .= self::button(self::minusIcon(), ['icon' => true, 'class' => 'minus', 'disabled' => !empty($o['minusDisabled']), 'data' => ['attribute' => $o['buttons'], 'step' => -1]]);
        }
        return $html . '</div>';
    }

    /** Legends pagination: back, page dots with numbers, forward. $url gets the page number appended. */
    public static function pagination(int $page, int $count, string $url): string
    {
        if ($count < 2) {
            return '';
        }
        $pages = [1];
        if ($count > 2) {
            $pages[] = $count > 9 && $page > 5 ? null : 2;
        }
        if ($count > 4) {
            $from = max(3, min($count - 5, $page - 1));
            $to = min($count - 1, max(7, $page + 2));
            for ($i = $from; $i <= $to; $i++) {
                $pages[] = $i;
            }
        }
        if ($count > 3) {
            $pages[] = $count > 9 && $page < $count - 4 ? null : $count - 1;
        }
        $pages[] = $count;
        $pages = array_values(array_unique(array_filter($pages, static fn($p) => $p === null || ($p >= 1 && $p <= $count)), SORT_REGULAR));
        $go = static fn(int $p) => ' onclick="window.location.href=\'' . self::e(addslashes($url . $p)) . '\'"';
        $html = '<div class="pagination">';
        $html .= '<button type="button" class="back' . ($page <= 1 ? ' disabled' : '') . '"' . ($page > 1 ? $go($page - 1) : '') . '></button>';
        $last = 0;
        foreach ($pages as $p) {
            if ($p === null) {
                $html .= '<div class="emptyPage">..</div>';
                continue;
            }
            if ($p <= $last) {
                continue;
            }
            $last = $p;
            $html .= '<button type="button" class="page' . ($p === $page ? ' selected' : '') . '"' . $go($p) . '></button><div class="pageIndex">' . $p . '</div>';
        }
        $html .= '<button type="button" class="forward' . ($page >= $count ? ' disabled' : '') . '"' . ($page < $count ? $go($page + 1) : '') . '></button>';
        return $html . '</div>';
    }

    /** Legends item tier: consumables, else the 3 qualities every equipment item comes in. */
    public static function itemTier(int $btype, int $type): string
    {
        if ($btype >= 7) {
            return 'consumable';
        }
        $first = [1 => 1, 2 => 82, 3 => 61, 4 => 16, 5 => 94, 6 => 103][$btype] ?? 1;
        if ($btype === 3 && $type >= 73) {
            $first = 73;
        }
        if ($btype === 4 && $type >= 115) {
            $first = 115;
        }
        return 'tier' . ((($type - $first) % 3 + 3) % 3 + 1);
    }

    /** Translated name of an item slot. */
    public static function slotName(string $slot): string
    {
        $defaults = ['helmet' => 'Helmet', 'body' => 'Armour', 'leftHand' => 'Left hand', 'rightHand' => 'Weapon',
            'shoes' => 'Boots', 'horse' => 'Horse', 'bag' => 'Bag', 'all' => 'All items'];
        return T('HeroV2', 'slot_' . $slot, $defaults[$slot] ?? ucfirst($slot));
    }

    public static function itemSlot(int $btype): string
    {
        return self::SLOTS[$btype] ?? 'bag';
    }

    private static function itemArrows(): string
    {
        return '<svg class="arrows" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 62 62">'
            . '<path class="top" d="M31 4l2-4h-4l2 4z"/><path class="bottom" d="M29 62h4l-2-4-2 4z"/>'
            . '<path class="from" d="M0 33l4-2-4-2v4z"/><path class="to" d="M58 31l4 2v-4l-4 2z"/></svg>';
    }

    /**
     * Hero item (heroItemV1). $item: btype, type, num, id, placeId; $o: class, title, attributes, withAmount.
     */
    public static function heroItem(array $item, array $o = []): string
    {
        $btype = (int)$item['btype'];
        $type = (int)$item['type'];
        $tier = self::itemTier($btype, $type);
        $slot = self::itemSlot($btype);
        $class = 'heroItem' . (!empty($o['class']) ? ' ' . $o['class'] : '') . ' ' . $tier . ' ' . $slot . ' heroItemV1';
        $attributes = '';
        foreach (['id' => 'data-id', 'placeId' => 'data-placeid'] as $key => $attribute) {
            if (isset($item[$key])) {
                $attributes .= ' ' . $attribute . '="' . (int)$item[$key] . '"';
            }
        }
        $attributes .= ' data-slot="' . $slot . '" data-tier="' . $tier . '" data-type="' . $type . '" data-btype="' . $btype . '"';
        if (isset($o['title'])) {
            $attributes .= ' title="' . self::e($o['title']) . '"';
        }
        foreach ($o['attributes'] ?? [] as $key => $value) {
            $attributes .= ' ' . $key . '="' . self::e($value) . '"';
        }
        $amount = (int)($item['num'] ?? 0);
        return '<div class="' . $class . '"' . $attributes . '>' . self::itemArrows()
            . '<div class="item item' . $type . '"></div>'
            . ($tier === 'consumable' && $amount > 0 && ($o['withAmount'] ?? true) ? '<div class="count">' . $amount . '</div>' : '')
            . '</div>';
    }

    /** Empty item slot of the given slot name (or an empty bag place). */
    public static function emptySlot(string $slot = '', array $o = []): string
    {
        $attributes = $slot !== '' ? ' data-slot="' . $slot . '"' : '';
        foreach ($o['attributes'] ?? [] as $key => $value) {
            $attributes .= ' ' . $key . '="' . self::e($value) . '"';
        }
        if (isset($o['title'])) {
            $attributes .= ' title="' . self::e($o['title']) . '"';
        }
        return '<div class="heroItem empty neutral' . ($slot !== '' ? ' ' . $slot : '') . (!empty($o['class']) ? ' ' . $o['class'] : '') . ' heroItemV1"' . $attributes . '>'
            . self::itemArrows() . '<div class="item"></div></div>';
    }

    /** Tooltip text of an item ("name||attributes"). */
    public static function itemTitle(int $btype, int $type): string
    {
        $properties = (new HeroItems())->getHeroItemProperties($btype, $type);
        return $properties['name'] . '||' . $properties['title'];
    }

    /** Slot filter row (heroItemSlotFilter). $hrefPattern gets the filter key appended; null = client side. */
    public static function slotFilter(string $active, ?string $hrefPattern = null): string
    {
        $html = '<div class="heroItemSlotFilter filterSlotsRow">';
        foreach (self::FILTERS as $key) {
            $title = self::slotName($key);
            $inner = '<i></i>';
            $class = 'filterSlot ' . $key . ($key === $active ? ' active' : '');
            $html .= $hrefPattern === null
                ? '<div class="' . $class . '" data-key="' . $key . '" title="' . self::e($title) . '">' . $inner . '</div>'
                : '<a class="' . $class . '" data-key="' . $key . '" title="' . self::e($title) . '" href="' . self::e($hrefPattern . $key) . '">' . $inner . '</a>';
        }
        return $html . '</div>';
    }

    public static function goldCoin(string $class = ''): string
    {
        return '<svg viewBox="0 0 50 50"' . ($class ? ' class="' . $class . '"' : '') . '><circle cx="25" cy="25" r="25" fill="#522d1c"/>'
            . '<circle cx="25" cy="25" r="23" fill="#e9bf60"/><path fill="#be8b34" d="M2.59 30C8 53.76 41.81 54.3 47.39 30H2.59Z"/>'
            . '<circle cx="25" cy="25" r="17" fill="#f9d77c"/><circle cx="25" cy="25" r="15" fill="#e2a944"/>'
            . '<path fill="#522d1c" d="m32.36 19.12 2.6 3.97 1.74-2.73-1.21-7.01h-3.18l-.4.6H18.08l-.4-.6H14.5l-1.22 7.08 2.04 2.49 2.55-3.77h3.77v13.57c-.06.74-2.18 1.16-3.1 1.22v2.72l1 1v.46h11v-.36l1-1.14v-2.75c-.91.01-3.17-.72-3.1-1.23V19.11h3.91Z"/>'
            . '<path fill="#f7ce7c" d="m34.95 21.25.7-1.1-1-5.8h-1.8l-.4.6h-14.9l-.4-.6h-1.8l-1 5.8.9 1.1 2.1-3.1h5.3v14.6c0 1.7-3.1 2.1-3.1 2.1v1.4l.8.8h9.5l.7-.8v-1.5s-3.1-.4-3.1-2.1v-14.6h5.4l2.1 3.2Z"/></svg>';
    }

    public static function silverCoin(string $class = ''): string
    {
        return '<svg viewBox="0 0 50 50"' . ($class ? ' class="' . $class . '"' : '') . '><circle cx="25" cy="25" r="25" fill="#303a4c"/>'
            . '<circle cx="25" cy="25" r="23" fill="#b7cdeb"/><path fill="#8ca8d2" d="M2.59 30C8 53.76 41.81 54.3 47.39 30H2.59Z"/>'
            . '<circle cx="25" cy="25" r="17" fill="#e4eef9"/><circle cx="25" cy="25" r="15" fill="#a9bfd9"/>'
            . '<path fill="#303a4c" d="m32.36 19.12 2.6 3.97 1.74-2.73-1.21-7.01h-3.18l-.4.6H18.08l-.4-.6H14.5l-1.22 7.08 2.04 2.49 2.55-3.77h3.77v13.57c-.06.74-2.18 1.16-3.1 1.22v2.72l1 1v.46h11v-.36l1-1.14v-2.75c-.91.01-3.17-.72-3.1-1.23V19.11h3.91Z"/>'
            . '<path fill="#eff8f9" d="m34.95 21.25.7-1.1-1-5.8h-1.8l-.4.6h-14.9l-.4-.6h-1.8l-1 5.8.9 1.1 2.1-3.1h5.3v14.6c0 1.7-3.1 2.1-3.1 2.1v1.4l.8.8h9.5l.7-.8v-1.5s-3.1-.4-3.1-2.1v-14.6h5.4l2.1 3.2Z"/></svg>';
    }

    /** Exchange arrows of the gold/silver widget. */
    public static function exchangeIcon(): string
    {
        return self::icon('0 0 17 13', '<path d="m4.581 12.184.17.157.17-.158 1.05-.98.197-.184-.2-.183L4.512 9.5H9.5V7.625H4.511L5.97 6.289l.2-.183-.198-.184-1.05-.98-.17-.158-.17.157-3.75 3.437-.201.184.201.185 3.75 3.437Zm7.498-4.126.17.158.17-.157 3.75-3.437.201-.184-.201-.185-3.75-3.437-.17-.157-.17.158-1.05.98-.197.184.2.183L12.488 3.5H7.5v1.875h4.989L11.03 6.711l-.2.183.199.184 1.05.98Z"/>');
    }

    /**
     * Vector art of the Legends client (laurel, reward frame, laurel icon, check) from legendsSvg.json, with a class.
     */
    public static function svg(string $name, string $class = ''): string
    {
        static $svgs = null;
        if ($svgs === null) {
            $svgs = json_decode((string)file_get_contents(__DIR__ . '/legendsSvg.json'), true) ?: [];
        }
        $svg = $svgs[$name] ?? '';
        return $class === '' || $svg === '' ? $svg : preg_replace('/^<svg/', '<svg class="' . self::e($class) . '"', $svg, 1);
    }

    /** Switch (formV2 label.switch with checkbox). */
    public static function toggleSwitch(string $name, bool $checked, string $label, string $wrapperClass = ''): string
    {
        return '<div class="formV2' . ($wrapperClass ? ' ' . $wrapperClass : '') . '"><label class="switch">'
            . '<input type="checkbox" name="' . self::e($name) . '" value="1"' . ($checked ? ' checked' : '') . '>'
            . '<div class="switch"></div><div class="label">' . $label . '</div></label></div>';
    }
}
