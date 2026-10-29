<?php

namespace resources\View;

use Core\Database\DB;

/**
 * Onboarding guide of the Travian Legends look for new players (js/Game/Legends/tutorial.js): dark speech bubbles with
 * an arrow at an element of the page, nothing dimmed. The guide has independent threads that each continue on the
 * page they belong to: F resource fields (upgrade a field, the build queue, a second order), B new buildings (the
 * building sites, the build menu), H hero (the hero, adventures), Q tasks (after H), M the adventure on its way (after H).
 * Only for accounts created with the Legends look (RegisterModel::createBaseVillage starts it); stored in
 * ov_legends_guide (uid, state = JSON {thread: step}, step 0 = not started, -1 = done).
 */
final class LegendsTutorial
{
    public const THREADS = ['F' => 6, 'B' => 4, 'H' => 5, 'Q' => 1, 'M' => 1];
    private static bool $ready = false;

    private static function ensure(): void
    {
        if (self::$ready) {
            return;
        }
        DB::getInstance()->query('CREATE TABLE IF NOT EXISTS `ov_legends_guide` (
            `uid` INT UNSIGNED NOT NULL PRIMARY KEY,
            `state` VARCHAR(255) NOT NULL DEFAULT \'{}\'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        self::$ready = true;
    }

    public static function start(int $uid): void
    {
        self::ensure();
        DB::getInstance()->query('REPLACE INTO ov_legends_guide (uid, state) VALUES (' . $uid . ', \'{}\')');
    }

    /** State of the player ({thread: step}), null when there is no guide (finished or an older account). */
    public static function state(int $uid): ?array
    {
        self::ensure();
        $raw = DB::getInstance()->fetchScalar('SELECT state FROM ov_legends_guide WHERE uid=' . $uid);
        if ($raw === false || $raw === null) {
            return null;
        }
        $state = [];
        $saved = json_decode((string)$raw, true) ?: [];
        foreach (self::THREADS as $thread => $last) {
            $step = (int)($saved[$thread] ?? 0);
            $state[$thread] = $step < 0 || $step > $last ? -1 : $step;
        }
        if (!array_filter($state, static fn($s) => $s >= 0)) {
            return null;
        }
        return $state;
    }

    public static function set(int $uid, string $thread, int $step): ?array
    {
        $state = self::state($uid);
        if ($state === null || !isset(self::THREADS[$thread])) {
            return $state;
        }
        $state[$thread] = $step < 0 || $step > self::THREADS[$thread] ? -1 : $step;
        DB::getInstance()->query('UPDATE ov_legends_guide SET state=\'' . json_encode($state) . '\' WHERE uid=' . $uid);
        return $state;
    }
}
