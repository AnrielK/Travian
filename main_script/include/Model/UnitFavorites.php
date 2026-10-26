<?php

namespace Model;

use Core\Database\DB;

/**
 * The units a player marked with the star in a training building (as in Travian Legends): they are listed first.
 * Kept in table ov_unit_favorites, created on first use so existing worlds need no migration.
 */
class UnitFavorites
{
    public const TABLE = 'ov_unit_favorites';

    private static bool $tableReady = false;

    public static function ensureTable(): void
    {
        if (self::$tableReady) {
            return;
        }
        DB::getInstance()->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE . '` (
            `uid` INT UNSIGNED NOT NULL,
            `unit_id` SMALLINT UNSIGNED NOT NULL,
            PRIMARY KEY (`uid`, `unit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        self::$tableReady = true;
    }

    /** @return int[] unit ids the player marked */
    public static function get(int $uid): array
    {
        self::ensureTable();
        $result = DB::getInstance()->query('SELECT unit_id FROM `' . self::TABLE . '` WHERE uid=' . $uid);
        $units = [];
        while ($row = $result->fetch_assoc()) {
            $units[] = (int)$row['unit_id'];
        }
        return $units;
    }

    public static function set(int $uid, int $unitId, bool $favorite): void
    {
        self::ensureTable();
        if ($favorite) {
            DB::getInstance()->query('INSERT IGNORE INTO `' . self::TABLE . "` (uid, unit_id) VALUES ($uid, $unitId)");
        } else {
            DB::getInstance()->query('DELETE FROM `' . self::TABLE . "` WHERE uid=$uid AND unit_id=$unitId");
        }
    }
}
