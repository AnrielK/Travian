<?php

namespace resources\View;

use Core\Database\DB;

/**
 * Onboarding guide of the Travian Legends look for new players (js/Game/Legends/tutorial.js): dark page, the element of
 * the step lit, a dark speech bubble with the text. Steps: resource fields → upgrade a field (production, costs, build
 * queue) → a second order → the hero and an adventure → the tasks. Only for accounts created with the Legends look
 * (RegisterModel::createBaseVillage starts it); stored in ov_legends_tutorial (uid, step, 0 = done).
 */
final class LegendsTutorial
{
    public const LAST = 10;
    private static bool $ready = false;

    private static function ensure(): void
    {
        if (self::$ready) {
            return;
        }
        DB::getInstance()->query('CREATE TABLE IF NOT EXISTS `ov_legends_tutorial` (
            `uid` INT UNSIGNED NOT NULL PRIMARY KEY,
            `step` TINYINT UNSIGNED NOT NULL DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        self::$ready = true;
    }

    public static function start(int $uid): void
    {
        self::ensure();
        DB::getInstance()->query('REPLACE INTO ov_legends_tutorial (uid, step) VALUES (' . $uid . ', 1)');
    }

    /** Current step of the player, 0 when there is none (finished or an older account). */
    public static function step(int $uid): int
    {
        self::ensure();
        return (int)DB::getInstance()->fetchScalar('SELECT step FROM ov_legends_tutorial WHERE uid=' . $uid);
    }

    public static function set(int $uid, int $step): int
    {
        self::ensure();
        $step = $step > self::LAST || $step < 0 ? 0 : $step;
        DB::getInstance()->query('UPDATE ov_legends_tutorial SET step=' . $step . ' WHERE uid=' . $uid);
        return $step;
    }
}
