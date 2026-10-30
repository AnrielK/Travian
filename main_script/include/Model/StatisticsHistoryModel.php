<?php

namespace Model;

use Core\Database\DB;
use function getGame;

/**
 * Daily history of the players for Statistics → General (stats_history, migration 007): one row per player and day
 * of the round. The worker job snapshot() keeps today's row of every player up to date (population rank in the
 * world and in the alliance, culture points, production per day); battles and merchant deliveries add the resources
 * won / lost and sent / received to the row of the day they happen (addRaid(), addTrade()).
 */
class StatisticsHistoryModel
{
    /** Day of the round of $time: 0 on the start day, 1 a day later, … (never negative). */
    public static function dayOf(int $time): int
    {
        $start = (int)getGame('start_time');
        return max(0, (int)floor(($time - $start) / 86400));
    }

    public static function today(): int
    {
        return self::dayOf(time());
    }

    /** Unix time the given day of the round starts. */
    public static function dayStart(int $day): int
    {
        return (int)getGame('start_time') + $day * 86400;
    }

    /**
     * Writes today's row of every player (ids > 2, not hidden): population and its ranks, culture points and the
     * production of all villages per day. The ranks follow the player statistics: population, then the older account.
     */
    public function snapshot(): void
    {
        $db = DB::getInstance();
        $day = self::today();
        $now = time();
        $db->query("INSERT INTO stats_history (uid, day, pop, pop_rank, ally_pop_rank, aid, cp, cp_prod, prod_r1, prod_r2, prod_r3, prod_r4, updated)
            SELECT r.id, $day, r.total_pop, r.pop_rank, r.ally_pop_rank, r.aid, r.cp, r.cp_prod,
                   COALESCE(p.r1, 0) * 24, COALESCE(p.r2, 0) * 24, COALESCE(p.r3, 0) * 24, COALESCE(p.r4, 0) * 24, $now
            FROM (
                SELECT id, aid, total_pop, cp, cp_prod,
                       ROW_NUMBER() OVER (ORDER BY total_pop DESC, id ASC) AS pop_rank,
                       IF(aid > 0, ROW_NUMBER() OVER (PARTITION BY aid ORDER BY total_pop DESC, id ASC), 0) AS ally_pop_rank
                FROM users WHERE id > 1 AND hidden = 0
            ) r
            LEFT JOIN (
                SELECT owner, SUM(woodp) r1, SUM(clayp) r2, SUM(ironp) r3, SUM(cropp) r4 FROM vdata GROUP BY owner
            ) p ON p.owner = r.id
            WHERE r.id > 2
            ON DUPLICATE KEY UPDATE pop=VALUES(pop), pop_rank=VALUES(pop_rank), ally_pop_rank=VALUES(ally_pop_rank),
                aid=VALUES(aid), cp=VALUES(cp), cp_prod=VALUES(cp_prod), prod_r1=VALUES(prod_r1), prod_r2=VALUES(prod_r2),
                prod_r3=VALUES(prod_r3), prod_r4=VALUES(prod_r4), updated=VALUES(updated)");
    }

    /** Takes a snapshot when the player has no row for today yet (first visit of the day before the worker ran). */
    public function ensureToday(int $uid): void
    {
        $day = self::today();
        $has = DB::getInstance()->fetchScalar("SELECT pop_rank FROM stats_history WHERE uid=$uid AND day=$day");
        if (!$has) {
            $this->snapshot();
        }
    }

    /** Resources taken by a raid: won by the attacker, lost by the owner of the raided village. */
    public static function addRaid(int $attackerUid, int $defenderUid, $amount): void
    {
        $amount = (int)round($amount);
        if ($amount <= 0) {
            return;
        }
        if ($attackerUid > 2) {
            self::add($attackerUid, 'raid_in', $amount);
        }
        if ($defenderUid > 2 && $defenderUid != $attackerUid) {
            self::add($defenderUid, 'raid_out', $amount);
        }
    }

    /** Resources delivered by merchants between two different players. */
    public static function addTrade(int $senderUid, int $receiverUid, $amount): void
    {
        $amount = (int)round($amount);
        if ($amount <= 0 || $senderUid == $receiverUid) {
            return;
        }
        if ($senderUid > 2) {
            self::add($senderUid, 'trade_out', $amount);
        }
        if ($receiverUid > 2) {
            self::add($receiverUid, 'trade_in', $amount);
        }
    }

    private static function add(int $uid, string $column, int $amount): void
    {
        $day = self::today();
        DB::getInstance()->query("INSERT INTO stats_history (uid, day, $column) VALUES ($uid, $day, $amount)
            ON DUPLICATE KEY UPDATE $column=$column+$amount");
    }

    /** Rows of a player, oldest day first. */
    public function getHistory(int $uid): array
    {
        $result = DB::getInstance()->query("SELECT * FROM stats_history WHERE uid=$uid ORDER BY day ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function deletePlayer(int $uid): void
    {
        DB::getInstance()->query("DELETE FROM stats_history WHERE uid=$uid");
    }
}
