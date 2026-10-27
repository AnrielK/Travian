<?php
/**
 * Travian Legends hero ("nový vzhled hrdiny"), taken over from the main branch (Game/Hero/HeroAppearanceV2).
 *
 * The hero is composed of transparent parts of gpack/legends655/mainPage/heroAppearance (positions on an
 * 875x1574 canvas in index.json). It exists next to the classic T4.6 hero (img/hero/…, table heroface), which
 * stays untouched:
 *   - table hero_legends (created on first use): uid, style, gender, config (JSON), lastupdate
 *   - style 'legends' / 'classic' is the player's choice; without a choice ('auto') the Legends hero is shown
 *     to viewers who use the Legends look (Skin::isLegends()) and the classic one to the others.
 *   - a player without a saved Legends look gets the closest one to his classic face.
 * Images: hero_legends.php?uid=…&size=… (PNG, cached in hcache/legends/).
 */
final class HeroLegends
{
    const TABLE = 'hero_legends';
    const PACK_DIR = 'gpack/legends655/mainPage/heroAppearance/';
    /** Bumped whenever the composition changes, so cached images are drawn again. */
    const RENDER_VERSION = 1;

    /** Drawing order: layer => [category it shows (null = always), colour group]. */
    const LAYERS = [
        'backHair' => ['hair', 'hairColor'],
        'base' => [null, 'skinColor'],
        'body' => [null, 'tribeColor'],      // clothes in the colours of the player's tribe
        'leftArm' => [null, 'skinColor'],
        'rightArm' => [null, 'skinColor'],
        'jaw' => ['jaw', 'skinColor'],
        'ears' => ['ears', 'skinColor'],
        'eyesBase' => ['eyes', 'skinColor'],
        'eyes' => ['eyes', 'eyeColor'],
        'browsBase' => ['brows', 'skinColor'],
        'brows' => ['brows', 'hairColor'],
        'mouth' => ['mouth', 'skinColor'],   // carries the upper lip, the nose is drawn over it
        'nose' => ['nose', 'skinColor'],
        'scar' => ['scar', 'skinColor'],
        'tattoo' => ['tattoo', null],
        'beard' => ['beard', 'hairColor'],
        'frontHair' => ['hair', 'hairColor'],
    ];

    /** Editor categories => [layer whose variants are the options, colour palette shown, may be empty]. */
    const CATEGORIES = [
        'jaw' => ['jaw', 'skinColor', false],
        'eyes' => ['eyes', 'eyeColor', false],
        'brows' => ['brows', 'hairColor', false],
        'nose' => ['nose', 'skinColor', false],
        'mouth' => ['mouth', null, false],
        'ears' => ['ears', 'skinColor', false],
        'hair' => ['frontHair', 'hairColor', true],
        'beard' => ['beard', 'hairColor', true],
        'scar' => ['scar', null, true],
        'tattoo' => ['tattoo', null, true],
    ];

    const COLORS = ['skinColor', 'eyeColor', 'hairColor'];
    const TRIBES = [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 6 => 'egyptian', 7 => 'hun'];
    const STYLES = ['auto', 'legends', 'classic'];

    /** Image sizes: name => [mode, width, height]. */
    const SIZES = [
        'head_81' => ['head', 81, 81],
        'sideinfo' => ['portrait', 160, 160],     // top bar (hero button)
        'head_79x91' => ['head', 79, 91],
        'editor' => ['head', 254, 330],            // appearance page preview
        'body_inventory' => ['body', 330, 594],    // hero inventory
        'body_profile' => ['bust', 160, 205],      // player profile
    ];

    private static $index = false;
    private static $tableReady = false;
    private static $rows = [];

    // ---- pack ---------------------------------------------------------------------------------------------

    public static function index(): ?array
    {
        if (self::$index === false) {
            self::$index = null;
            $file = APP_MAIN_PATH . '/' . self::PACK_DIR . 'index.json';
            if (is_file($file) && function_exists('imagecreatetruecolor')) {
                $data = json_decode((string)file_get_contents($file), true);
                if (is_array($data) && isset($data['parts'], $data['size'])) self::$index = $data;
            }
        }
        return self::$index;
    }

    public static function available(): bool
    {
        return self::index() !== null;
    }

    public static function partsDir(): string
    {
        return APP_MAIN_PATH . '/' . self::PACK_DIR . 'parts/';
    }

    public static function partsUrl(): string
    {
        return self::PACK_DIR . 'parts/';
    }

    /** Options of every category and the colours of both genders, from the parts of the pack. */
    public static function options(): array
    {
        static $options = null;
        if ($options !== null) return $options;
        $options = [];
        foreach (['male', 'female'] as $gender) {
            $colors = array_fill_keys(self::COLORS, []);
            $variants = [];
            foreach (array_keys(self::index()['parts']) as $key) {
                $p = explode('/', $key);
                if ($p[0] !== $gender) continue;
                if (isset($colors[$p[1]]) && isset($p[2])) $colors[$p[1]][$p[2]] = true;
                if (preg_match('/^([a-zA-Z]+?)(\d+)(?:-|$)/', end($p), $m)) $variants[$m[1]][(int)$m[2]] = true;
            }
            $categories = [];
            foreach (self::CATEGORIES as $category => [$layer, , $optional]) {
                $list = array_keys($variants[$layer] ?? []);
                sort($list);
                if ($list && $optional) $list[] = null;
                $categories[$category] = $list;
            }
            $options[$gender] = [
                'categories' => $categories,
                'colors' => array_map(static function ($c) {
                    $c = array_keys($c);
                    natsort($c);
                    return array_values($c);
                }, $colors),
            ];
        }
        return $options;
    }

    // ---- storage ------------------------------------------------------------------------------------------

    private static function db(): ?PDO
    {
        $pdo = function_exists('travian_pdo') ? travian_pdo() : null;
        return $pdo instanceof PDO ? $pdo : null;
    }

    public static function ensureTable(): void
    {
        if (self::$tableReady || !($db = self::db())) return;
        try {
            $db->exec('CREATE TABLE IF NOT EXISTS ' . self::TABLE . ' (
                uid INT NOT NULL PRIMARY KEY,
                style VARCHAR(8) NOT NULL DEFAULT \'auto\',
                gender VARCHAR(6) NOT NULL DEFAULT \'male\',
                config TEXT,
                lastupdate INT NOT NULL DEFAULT 0
            )');
        } catch (Throwable $e) {
            error_log('hero_legends: ' . $e->getMessage());
        }
        self::$tableReady = true;
    }

    private static function row(int $uid): ?array
    {
        if (!array_key_exists($uid, self::$rows)) {
            self::$rows[$uid] = null;
            self::ensureTable();
            if ($db = self::db()) {
                try {
                    $st = $db->prepare('SELECT * FROM ' . self::TABLE . ' WHERE uid = ?');
                    $st->execute([$uid]);
                    $r = $st->fetch(PDO::FETCH_ASSOC);
                    self::$rows[$uid] = $r ?: null;
                } catch (Throwable $e) {
                }
            }
        }
        return self::$rows[$uid];
    }

    private static function write(int $uid, array $fields): void
    {
        self::ensureTable();
        if (!($db = self::db())) return;
        $row = self::row($uid) ?: ['uid' => $uid, 'style' => 'auto', 'gender' => 'male', 'config' => null];
        $row = array_merge($row, $fields, ['uid' => $uid, 'lastupdate' => time()]);
        $db->prepare('DELETE FROM ' . self::TABLE . ' WHERE uid = ?')->execute([$uid]);
        $db->prepare('INSERT INTO ' . self::TABLE . ' (uid, style, gender, config, lastupdate) VALUES (?, ?, ?, ?, ?)')
            ->execute([$uid, $row['style'], $row['gender'], $row['config'], $row['lastupdate']]);
        self::$rows[$uid] = $row;
    }

    /** The player's choice: 'auto' (follow the viewer's look), 'legends' or 'classic'. */
    public static function storedStyle(int $uid): string
    {
        $style = self::row($uid)['style'] ?? 'auto';
        return in_array($style, self::STYLES, true) ? $style : 'auto';
    }

    public static function setStyle(int $uid, $style): void
    {
        if ($uid > 0 && in_array($style, self::STYLES, true) && self::available()) self::write($uid, ['style' => $style]);
    }

    /** Whether the hero of $uid is shown as the Legends hero (to the current viewer). */
    public static function active(int $uid): bool
    {
        if (!self::available() || $uid <= 0) return false;
        $style = self::storedStyle($uid);
        if ($style === 'auto') return class_exists('Skin') && Skin::isLegends();
        return $style === 'legends';
    }

    public static function styleOptions(string $selected): string
    {
        $names = [
            'auto' => defined('SETTINGS_HERO_AUTO') ? SETTINGS_HERO_AUTO : 'Like the look of the game',
            'legends' => defined('SETTINGS_HERO_LEGENDS') ? SETTINGS_HERO_LEGENDS : 'Travian Legends (new)',
            'classic' => defined('SETTINGS_HERO_CLASSIC') ? SETTINGS_HERO_CLASSIC : 'Classic T4.6',
        ];
        $html = '';
        foreach ($names as $id => $name) {
            $html .= '<option value="' . $id . '"' . ($id === $selected ? ' selected="selected"' : '') . '>' . htmlspecialchars($name) . '</option>';
        }
        return $html;
    }

    /** Appearance of a player: [gender, colours…, categories…, tribeColor]. */
    public static function get(int $uid): array
    {
        return ['tribeColor' => self::tribeOf($uid)] + self::stored($uid);
    }

    public static function tribeOf(int $uid): string
    {
        $tribe = 1;
        if ($db = self::db()) {
            try {
                $st = $db->prepare('SELECT tribe FROM users WHERE id = ?');
                $st->execute([$uid]);
                $tribe = (int)$st->fetchColumn();
            } catch (Throwable $e) {
            }
        }
        return self::TRIBES[$tribe] ?? 'roman';
    }

    /** The saved look, or the classic face converted. */
    private static function stored(int $uid): array
    {
        $row = self::row($uid);
        if ($row && $row['config']) {
            $config = json_decode($row['config'], true);
            if (is_array($config)) return self::normalize(['gender' => $row['gender']] + $config);
        }
        $face = null;
        if ($db = self::db()) {
            try {
                $st = $db->prepare('SELECT * FROM heroface WHERE uid = ?');
                $st->execute([$uid]);
                $face = $st->fetch(PDO::FETCH_ASSOC) ?: null;
            } catch (Throwable $e) {
            }
        }
        return self::fromClassicFace($face ?: []);
    }

    /** Closest Legends look to a classic T4.6 face (heroface: options 0-4, beard 5 = none, colours 0-4). */
    public static function fromClassicFace(array $face): array
    {
        $gender = (int)($face['gender'] ?? 0) === 1 ? 'female' : 'male';
        $pick = static function ($v) { return isset($v) ? (int)$v + 1 : 1; };
        $hairColors = ['black', 'brown', 'brightbrown', 'blonde', 'red'];
        return self::normalize([
            'gender' => $gender,
            'skinColor' => 'skin1',
            'eyeColor' => 'brown',
            'hairColor' => $hairColors[(int)($face['color'] ?? 1)] ?? 'brown',
            'jaw' => $pick($face['face'] ?? null),
            'eyes' => $pick($face['eye'] ?? null),
            'brows' => $pick($face['eyebrow'] ?? null),
            'nose' => $pick($face['nose'] ?? null),
            'mouth' => $pick($face['mouth'] ?? null),
            'ears' => $pick($face['ear'] ?? null),
            'hair' => $pick($face['hair'] ?? null),
            'beard' => $gender === 'female' || (int)($face['beard'] ?? 0) === 5 ? null : $pick($face['beard'] ?? null),
            'scar' => null,
            'tattoo' => null,
        ]);
    }

    /** Keeps only values the pack has (unknown values fall back to the first option / colour). */
    public static function normalize(array $config): array
    {
        $gender = ($config['gender'] ?? 'male') === 'female' ? 'female' : 'male';
        $options = self::options()[$gender];
        $result = ['gender' => $gender];
        foreach (self::COLORS as $color) {
            $list = $options['colors'][$color];
            $value = $config[$color] ?? null;
            $result[$color] = in_array($value, $list, true) ? $value : ($list[0] ?? null);
        }
        foreach ($options['categories'] as $category => $list) {
            $value = $config[$category] ?? null;
            $value = $value === null || $value === '' ? null : (int)$value;
            if (!in_array($value, $list, true)) $value = $list ? $list[0] : null;
            $result[$category] = $value;
        }
        return $result;
    }

    /** Whether every value of $config is an option of the pack. */
    public static function isValid(array $config): bool
    {
        if (!in_array($config['gender'] ?? '', ['male', 'female'], true)) return false;
        foreach (self::normalize($config) as $key => $value) {
            $given = $config[$key] ?? null;
            if ($key !== 'gender' && !in_array($key, self::COLORS, true)) $given = $given === null || $given === '' ? null : (int)$given;
            if ($given !== $value) return false;
        }
        return true;
    }

    /** Saves the look and switches the player to the Legends hero. */
    public static function save(int $uid, array $config): void
    {
        $config = self::normalize($config);
        $gender = $config['gender'];
        unset($config['gender']);
        self::write($uid, ['style' => 'legends', 'gender' => $gender, 'config' => json_encode($config)]);
    }

    /** Hash of the look for image URLs (a changed look gets a new URL). */
    public static function hash(int $uid): string
    {
        return substr(md5(json_encode(self::get($uid)) . self::RENDER_VERSION), 0, 10);
    }

    public static function imageUrl(int $uid, string $size): string
    {
        return 'hero_legends.php?uid=' . $uid . '&amp;size=' . $size . '&amp;v=' . self::hash($uid);
    }

    // ---- drawing ------------------------------------------------------------------------------------------

    /** Part keys of a look, in drawing order. */
    public static function layerKeys(array $config): array
    {
        $parts = self::index()['parts'];
        $g = $config['gender'];
        $keys = [];
        foreach (self::LAYERS as $layer => [$category, $color]) {
            $variant = $category === null ? '' : ($config[$category] ?? null);
            if ($category !== null && $variant === null) continue;
            $shade = $color ? ($config[$color] ?? null) : null;
            switch ($layer) {
                case 'base':
                    $key = "$g/skinColor/$shade/base/base-$shade";
                    break;
                case 'leftArm':
                case 'rightArm':
                    $key = "$g/skinColor/$shade/$layer/{$layer}Default-$shade";
                    break;
                case 'body':
                    $tribe = $config['tribeColor'] ?? 'roman';
                    $key = "$g/tribeColor/$tribe/body/body-$tribe";
                    break;
                case 'tattoo':
                    $key = "$g/tattoo/tattoo$variant";
                    break;
                default:
                    $key = "$g/$color/$shade/$layer/$layer$variant-$shade";
            }
            if (isset($parts[$key])) $keys[] = $key;
        }
        return $keys;
    }

    /** Square around the head on the hero canvas: [x, y, size]. */
    public static function headBox(array $config, int $size = 280, float $jawAt = 0.78): array
    {
        $parts = self::index()['parts'];
        $jaw = null;
        foreach (self::layerKeys($config) as $key) {
            if (strpos($key, '/jaw/') !== false) $jaw = $parts[$key];
        }
        [$x, $y, $w, $h] = $jaw ?: [387, 226, 122, 106];
        return [(int)round($x + $w / 2 - $size / 2), (int)round($y + $h - $size * $jawAt), $size];
    }

    /** Composes the hero (GD, true colour with alpha) and cuts / scales it to the wanted image. */
    public static function render(array $config, string $mode, int $width, int $height)
    {
        [$canvasW, $canvasH] = self::index()['size'];
        if ($mode === 'portrait') {
            [$sx, $sy, $size] = self::headBox($config, 400, 0.6);
            $source = [$sx, $sy, $size, $size];
        } elseif ($mode === 'head') {
            [$sx, $sy, $size] = self::headBox($config, $height > $width ? 300 : 280);
            $source = [$sx, $sy, $size, (int)round($size * $height / $width)];
        } elseif ($mode === 'bust') {
            $source = [170, 90, 535, (int)round(535 * $height / $width)];
        } else {
            $source = [0, 0, $canvasW, $canvasH];
        }
        $canvas = imagecreatetruecolor($canvasW, $canvasH);
        imagealphablending($canvas, false);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        imagesavealpha($canvas, true);
        imagealphablending($canvas, true);
        $parts = self::index()['parts'];
        foreach (self::layerKeys($config) as $key) {
            $file = self::partsDir() . $key . '.png';
            if (!is_file($file) || !($part = @imagecreatefrompng($file))) continue;
            [$x, $y] = $parts[$key];
            imagecopy($canvas, $part, $x, $y, 0, 0, imagesx($part), imagesy($part));
            imagedestroy($part);
        }
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        imagesavealpha($image, true);
        imagecopyresampled($image, $canvas, 0, 0, $source[0], $source[1], $width, $height, $source[2], $source[3]);
        imagedestroy($canvas);
        return $image;
    }

    /** PNG bytes of a player's hero in one of SIZES (cached in hcache/legends/ when writable). */
    public static function png(int $uid, string $size): string
    {
        [$mode, $w, $h] = self::SIZES[$size] ?? self::SIZES['head_81'];
        $config = self::get($uid);
        $dir = APP_MAIN_PATH . '/hcache/legends/';
        $file = $dir . $size . '_' . md5(json_encode($config) . self::RENDER_VERSION) . '.png';
        if (is_file($file)) return (string)file_get_contents($file);
        $image = self::render($config, $mode, $w, $h);
        ob_start();
        imagepng($image, null, 6);
        $png = (string)ob_get_clean();
        imagedestroy($image);
        if ((is_dir($dir) || @mkdir($dir, 0775, true)) && is_writable($dir)) @file_put_contents($file, $png);
        return $png;
    }
}
