<?php

namespace Game\Helpers;

use Core\Database\DB;

/**
 * Names of computer-controlled players (bots) added when a world is created (GAME_BOTS) or by the administration:
 * a word of the list, with a number when it is taken. At most 15 bytes (RegisterModel::addUser() cuts longer names).
 */
class BotNames
{
    const NAMES = [
        'Bořivoj', 'Kazimír', 'Radegast', 'Ludmila', 'Svatopluk', 'Drahomíra', 'Přemysl', 'Libuše', 'Krok', 'Bivoj',
        'Horymír', 'Šemík', 'Vratislav', 'Dobromil', 'Mstislav', 'Jaromír', 'Zbyhněv', 'Ctirad', 'Šárka', 'Vlasta',
        'Spytihněv', 'Boleslav', 'Soběslav', 'Oldřich', 'Břetislav', 'Dalibor', 'Neklan', 'Hostivít', 'Kazi', 'Teta',
        'Lumír', 'Záboj', 'Slavoj', 'Bolemír', 'Vítězslav', 'Miloš', 'Radim', 'Jaroslava', 'Bohuslav', 'Dobroslava',
        'Marcus', 'Brutus', 'Aurelia', 'Gaius', 'Livia', 'Octavia', 'Titus', 'Varus', 'Sigurd', 'Brunhild',
        'Ragnar', 'Gunnar', 'Freya', 'Ulrich', 'Wotan', 'Hilda', 'Vercingetor', 'Boudica', 'Ambiorix', 'Brennus',
        'Cassivel', 'Epona', 'Nefertari', 'Ramses', 'Imhotep', 'Hatšepsut', 'Attila', 'Bleda', 'Ruga', 'Ellak',
        'Stará Hora', 'Vlčí Doupě', 'Zlatý Klas', 'Kamenný Most', 'Tichý Les', 'Rudý Štít', 'Železná Pěst',
        'Mlýnský Dvůr', 'Černá Věž', 'Bílá Skála',
    ];

    /** $count different names that no player of the world has yet. */
    public static function generate(int $count): array
    {
        $db = DB::getInstance();
        $taken = [];
        $result = $db->query("SELECT name FROM users");
        while ($result && ($row = $result->fetch_row())) {
            $taken[mb_strtolower($row[0])] = true;
        }
        $pool = self::NAMES;
        shuffle($pool);
        $names = [];
        for ($round = 0; sizeof($names) < $count && $round < 100; ++$round) {
            foreach ($pool as $name) {
                if (sizeof($names) >= $count) {
                    break;
                }
                $suffix = $round === 0 ? '' : ' ' . ($round + 1);
                $candidate = rtrim(mb_strcut($name, 0, 15 - strlen($suffix), 'UTF-8')) . $suffix;
                $key = mb_strtolower($candidate);
                if (!isset($taken[$key])) {
                    $taken[$key] = true;
                    $names[] = $candidate;
                }
            }
        }
        return $names;
    }
}
