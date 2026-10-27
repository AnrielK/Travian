<?php
/**
 * Languages of the game (folders in application/Lang/<code>/Lang.php).
 * The names are shown in the language pickers (sidebar box "Settings", Preferences → Game).
 */
final class Languages
{
    /** code => name (in that language). The folder "ar" holds the Turkish texts of the original release. */
    const LIST = [
        'en' => 'English',
        'cs' => 'Čeština',
        'ar' => 'Türkçe',
    ];

    /** Languages whose folder is present. */
    public static function available(): array
    {
        $out = [];
        foreach (self::LIST as $code => $name) {
            if (is_file(__DIR__ . '/Lang/' . $code . '/Lang.php')) $out[$code] = $name;
        }
        return $out;
    }

    public static function valid($code): bool
    {
        return is_string($code) && array_key_exists($code, self::available());
    }

    /** A safe language code (unknown values -> the world's language, else English). */
    public static function sanitize($code): string
    {
        if (self::valid($code)) return $code;
        return (defined('LANG') && self::valid(LANG)) ? LANG : 'en';
    }

    /** <option> list for a <select>. */
    public static function options(string $selected): string
    {
        $html = '';
        foreach (self::available() as $code => $name) {
            $html .= '<option value="' . $code . '"' . ($code === $selected ? ' selected="selected"' : '') . '>'
                . htmlspecialchars($name) . '</option>';
        }
        return $html;
    }
}
