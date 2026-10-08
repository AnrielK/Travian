<?php

namespace Game\Hero;

use Core\Caching\Caching;
use Core\Database\DB;

/**
 * Travian Legends hero appearance (graphic packs with heroAppearance/index.json, e.g. legends655).
 *
 * A hero is composed of transparent parts placed on a 875x1574 canvas (positions in index.json). The look is
 * stored per player in ov_hero_appearance (created on first use; a missing row is derived from the classic
 * face). Layer order and the colour each layer takes are defined here, the Legends client gets them from its
 * server.
 */
class HeroAppearanceV2
{
    public const TABLE = 'ov_hero_appearance';
    /** Bumped whenever the composition changes, so cached images are drawn again. */
    public const RENDER_VERSION = 3;

    /** Drawing order: layer => [category it shows (null = always), colour group it is tinted with]. */
    public const LAYERS = [
        'backHair' => ['hair', 'hairColor'],
        'base' => [null, 'skinColor'],
        'leftArm' => [null, 'skinColor'],
        'rightArm' => [null, 'skinColor'],
        // Clothes in the colours of the player's tribe (parts <gender>/tribeColor/<tribe>/body/body-<tribe>).
        'body' => [null, 'tribeColor'],
        'jaw' => ['jaw', 'skinColor'],
        'ears' => ['ears', 'skinColor'],
        'eyesBase' => ['eyes', 'skinColor'],
        'eyes' => ['eyes', 'eyeColor'],
        'browsBase' => ['brows', 'skinColor'],
        'brows' => ['brows', 'hairColor'],
        // The mouth parts carry the skin of the upper lip, so the nose is drawn over them.
        'mouth' => ['mouth', 'skinColor'],
        'nose' => ['nose', 'skinColor'],
        'scar' => ['scar', 'skinColor'],
        'tattoo' => ['tattoo', null],
        'beard' => ['beard', 'hairColor'],
        'frontHair' => ['hair', 'hairColor'],
    ];

    /** Editor categories => the layer whose variants are the options, the colour palette shown, may be empty. */
    public const CATEGORIES = [
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

    public const COLORS = ['skinColor', 'eyeColor', 'hairColor'];

    private static $index = false;
    private static $tableReady = false;

    /** Parts index of the active graphic pack or null when it has no hero appearance. */
    public static function index(): ?array
    {
        if (self::$index === false) {
            self::$index = null;
            if (function_exists('is_hero_v2') && is_hero_v2()) {
                $file = get_gpack_mainPage_dir() . 'heroAppearance/index.json';
                if (is_file($file)) {
                    $data = json_decode((string)file_get_contents($file), true);
                    if (is_array($data) && isset($data['parts'], $data['size'])) {
                        self::$index = $data;
                    }
                }
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
        return get_gpack_mainPage_dir() . 'heroAppearance/parts/';
    }

    public static function partsUrl(): string
    {
        return get_gpack_cdn_mainPage_url() . 'heroAppearance/parts/';
    }

    /** Options of every category and the colours of both genders, from the parts of the pack. */
    public static function options(): array
    {
        static $options = null;
        if ($options !== null) {
            return $options;
        }
        $options = [];
        foreach (['male', 'female'] as $gender) {
            $colors = array_fill_keys(self::COLORS, []);
            $variants = [];
            foreach (array_keys(self::index()['parts']) as $key) {
                $p = explode('/', $key);
                if ($p[0] !== $gender) {
                    continue;
                }
                if (isset($colors[$p[1]]) && isset($p[2])) {
                    $colors[$p[1]][$p[2]] = true;
                }
                $name = end($p);
                if (preg_match('/^([a-zA-Z]+?)(\d+)(?:-|$)/', $name, $m)) {
                    $variants[$m[1]][(int)$m[2]] = true;
                }
            }
            $categories = [];
            foreach (self::CATEGORIES as $category => [$layer, , $optional]) {
                $list = array_keys($variants[$layer] ?? []);
                sort($list);
                if ($list && $optional) {
                    $list[] = null;
                }
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

    public static function ensureTable(): void
    {
        if (self::$tableReady) {
            return;
        }
        DB::getInstance()->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE . '` (
            `uid` INT UNSIGNED NOT NULL PRIMARY KEY,
            `gender` VARCHAR(6) NOT NULL DEFAULT \'male\',
            `config` TEXT NOT NULL,
            `lastupdate` INT UNSIGNED NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        self::$tableReady = true;
    }

    /** Appearance of a player: [gender, colors..., categories...]. */
    /** Tribe colours of the clothes (part folder names) per race. */
    public const TRIBES = [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 6 => 'egyptian', 7 => 'hun', 8 => 'spartan', 9 => 'viking'];

    /** Clothes colour of a player's hero: their tribe (roman when unknown). */
    public static function tribeOf(int $uid): string
    {
        $race = (int)DB::getInstance()->fetchScalar('SELECT race FROM users WHERE id=' . $uid);
        return self::TRIBES[$race] ?? 'roman';
    }

    public static function get(int $uid): array
    {
        return ['tribeColor' => self::tribeOf($uid)] + self::stored($uid);
    }

    /** The saved look (or the classic face converted), without the tribe. */
    private static function stored(int $uid): array
    {
        self::ensureTable();
        $row = DB::getInstance()->query('SELECT gender, config FROM `' . self::TABLE . '` WHERE uid=' . $uid);
        if ($row && ($row = $row->fetch_assoc())) {
            $config = json_decode($row['config'], true);
            if (is_array($config)) {
                return self::normalize(['gender' => $row['gender']] + $config);
            }
        }
        $face = DB::getInstance()->query('SELECT * FROM face WHERE uid=' . $uid);
        return self::fromClassicFace($face && ($face = $face->fetch_assoc()) ? $face : ['gender' => 'male']);
    }

    /** Closest Legends look to a classic face (options 0-9 become 1-10, "none" stays empty). */
    public static function fromClassicFace(array $face): array
    {
        $gender = ($face['gender'] ?? 'male') === 'female' ? 'female' : 'male';
        $pick = static fn($v) => isset($v) ? (int)$v + 1 : 1;
        $hairColors = ['black', 'brightbrown', 'brown', 'blonde', 'red'];
        return self::normalize([
            'gender' => $gender,
            'skinColor' => 'skin1',
            'eyeColor' => 'brown',
            'hairColor' => $hairColors[(int)($face['hairColor'] ?? 0)] ?? 'brown',
            'jaw' => $pick($face['headProfile'] ?? null),
            'eyes' => $pick($face['eyes'] ?? null),
            'brows' => $pick($face['eyebrow'] ?? null),
            'nose' => $pick($face['nose'] ?? null),
            'mouth' => $pick($face['mouth'] ?? null),
            'ears' => $pick($face['ears'] ?? null),
            'hair' => $gender === 'male' && (int)($face['hairStyle'] ?? 0) === 5 ? null : $pick($face['hairStyle'] ?? null),
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
            if (!in_array($value, $list, true)) {
                $value = $list ? $list[0] : null;
            }
            $result[$category] = $value;
        }
        return $result;
    }

    /** Whether every value of $config is an option of the pack. */
    public static function isValid(array $config): bool
    {
        if (!in_array($config['gender'] ?? '', ['male', 'female'], true)) {
            return false;
        }
        $normalized = self::normalize($config);
        foreach ($normalized as $key => $value) {
            $given = $config[$key] ?? null;
            if ($key !== 'gender' && !in_array($key, self::COLORS, true)) {
                $given = $given === null || $given === '' ? null : (int)$given;
            }
            if ($given !== $value) {
                return false;
            }
        }
        return true;
    }

    public static function save(int $uid, array $config): void
    {
        self::ensureTable();
        $config = self::normalize($config);
        $gender = $config['gender'];
        unset($config['gender']);
        $db = DB::getInstance();
        $db->run('REPLACE INTO `' . self::TABLE . '` (uid, gender, config, lastupdate) VALUES (?, ?, ?, ?)',
            [$uid, $gender, json_encode($config), time()]);
        // The classic face keeps the gender and the image hash used in the page URLs.
        $db->query("UPDATE face SET gender='" . $gender . "', lastupdate=" . time() . " WHERE uid=" . $uid);
        $db->query('UPDATE inventory SET lastupdate=' . time() . ' WHERE uid=' . $uid);
        self::invalidate($uid);
    }

    public static function invalidate(int $uid): void
    {
        $cache = Caching::getInstance();
        foreach (['head_81', 'portrait', 'head_79x91', 'body_inventory', 'body_profile'] as $size) {
            $cache->delete("hero_v2_r" . self::RENDER_VERSION . "_{$size}_{$uid}_" . self::tribeOf($uid));
        }
        foreach (['hero_image_%d_79x91', 'hero_image_%d_81x81', 'hero_body_%d_330x422', 'hero_body_%d_330x422_nohorse',
                     'hero_body_%d_160x205', 'heroImageHash%d', 'hero_sidebar_%d'] as $key) {
            $cache->delete(sprintf($key, $uid));
        }
    }

    /** Part keys of a look, in drawing order. */
    public static function layerKeys(array $config): array
    {
        $parts = self::index()['parts'];
        $g = $config['gender'];
        $keys = [];
        foreach (self::LAYERS as $layer => [$category, $color]) {
            $variant = $category === null ? '' : ($config[$category] ?? null);
            if ($category !== null && $variant === null) {
                continue;
            }
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
            if (isset($parts[$key])) {
                $keys[] = $key;
            }
        }
        return $keys;
    }

    /** Square around the head on the hero canvas: [x, y, size]. */
    public static function headBox(array $config, int $size = 280, float $jawAt = 0.78): array
    {
        $parts = self::index()['parts'];
        $jaw = null;
        foreach (self::layerKeys($config) as $key) {
            if (strpos($key, '/jaw/') !== false) {
                $jaw = $parts[$key];
            }
        }
        [$x, $y, $w, $h] = $jaw ?: [387, 226, 122, 106];
        return [(int)round($x + $w / 2 - $size / 2), (int)round($y + $h - $size * $jawAt), $size];
    }

    /** Composes the hero (GD, true colour with alpha) and cuts / scales it to the wanted image. */
    public static function render(array $config, string $mode, int $width, int $height)
    {
        [$canvasW, $canvasH] = self::index()['size'];
        if ($mode === 'portrait') {
            // Head and shoulders, the way the Legends top bar shows the hero.
            [$sx, $sy, $size] = self::headBox($config, 400, 0.6);
            $source = [$sx, $sy, $size, $size];
        } elseif ($mode === 'head') {
            [$sx, $sy, $size] = self::headBox($config);
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
            if (!is_file($file) || !($part = @imagecreatefrompng($file))) {
                continue;
            }
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

    /** Sends the PNG of a player's hero ($size: head_81, head_79x91, body_inventory, body_profile), cached. */
    public static function output(int $uid, string $size): void
    {
        $sizes = [
            'head_81' => ['head', 81, 81],
            'portrait' => ['portrait', 160, 160],
            'head_79x91' => ['head', 79, 91],
            'body_inventory' => ['body', 438, 788],
            'body_profile' => ['bust', 160, 205],
        ];
        [$mode, $w, $h] = $sizes[$size] ?? $sizes['head_81'];
        $cacheKey = "hero_v2_r" . self::RENDER_VERSION . "_{$size}_{$uid}_" . self::tribeOf($uid);
        $cache = Caching::getInstance();
        $png = $cache->get($cacheKey);
        if (!$png) {
            $image = self::render(self::get($uid), $mode, $w, $h);
            ob_start();
            imagepng($image, null, 6);
            $png = ob_get_clean();
            imagedestroy($image);
            $cache->set($cacheKey, $png, 86400);
        }
        header('Content-Type: image/png');
        header('Cache-Control: max-age=86400');
        echo $png;
    }
}
