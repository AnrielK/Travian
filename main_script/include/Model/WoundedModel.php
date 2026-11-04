<?php

namespace Model;

use Core\Database\DB;

/**
 * Troops wounded in battle (Travian Legends): a village with a Hospital (46) gets back 40 % of its lost infantry and
 * cavalry as wounded, a Spartan village with an Asclepeion (48) 60 %. Wounded troops do not fight and eat no crop;
 * those not being healed lose 10 % a day. Healing (Controller\Build\TroopBuilding in the Hospital) costs the training
 * price, takes half the training time and goes through the training queue with item_id 46 / 48.
 */
class WoundedModel
{
    const HOSPITAL = 46;
    const ASCLEPEION = 48;
    /** u1 - u6: infantry and cavalry of every tribe */
    const UNITS = 6;
    const DAILY_DECAY = 0.9;

    /** [gid, level] of the Hospital or Asclepeion of the village, [0, 0] without one. */
    public static function healingBuilding($kid)
    {
        $kid = (int)$kid;
        $row = DB::getInstance()->query("SELECT * FROM fdata WHERE kid=$kid")->fetch_assoc();
        if ($row) {
            for ($i = 19; $i <= 38; ++$i) {
                $gid = (int)$row['f' . $i . 't'];
                if (($gid == self::HOSPITAL || $gid == self::ASCLEPEION) && $row['f' . $i] > 0) {
                    return [$gid, (int)$row['f' . $i]];
                }
            }
        }
        return [0, 0];
    }

    /** Share of the losses that come back wounded: 40 % (Hospital), 60 % (Asclepeion), 0 without one. */
    public static function woundedRate($kid)
    {
        list($gid) = self::healingBuilding($kid);
        return $gid == self::ASCLEPEION ? 0.6 : ($gid == self::HOSPITAL ? 0.4 : 0);
    }

    /** The wounded of the village after the daily decay, as whole units [1 => .., 6 => ..]. */
    public function getWounded($kid)
    {
        $row = $this->decayed($kid);
        $units = [];
        for ($i = 1; $i <= self::UNITS; ++$i) {
            $units[$i] = (int)floor($row['u' . $i] + 1e-9);
        }
        return $units;
    }

    /** Adds the wounded of a battle: $dead are the losses of the village's troops (1 - 11). */
    public function addFromBattle($kid, array $dead)
    {
        $rate = self::woundedRate($kid);
        if ($rate <= 0) {
            return;
        }
        $add = [];
        for ($i = 1; $i <= self::UNITS; ++$i) {
            $add[$i] = floor(($dead[$i] ?? 0) * $rate);
        }
        if (array_sum($add) <= 0) {
            return;
        }
        $this->decayed($kid);
        $set = [];
        foreach ($add as $i => $num) {
            if ($num > 0) {
                $set[] = "u$i=u$i+$num";
            }
        }
        DB::getInstance()->query("UPDATE wounded SET " . implode(",", $set) . " WHERE kid=" . (int)$kid);
    }

    /** Takes $num wounded of unit $nr into healing; returns how many were taken. */
    public function take($kid, $nr, $num)
    {
        $nr = (int)$nr;
        if ($nr < 1 || $nr > self::UNITS) {
            return 0;
        }
        $num = min((int)$num, $this->getWounded($kid)[$nr]);
        if ($num > 0) {
            DB::getInstance()->query("UPDATE wounded SET u$nr=GREATEST(0, u$nr-$num) WHERE kid=" . (int)$kid);
        }
        return max(0, $num);
    }

    /** All wounded of a conquered village are lost. */
    public function clear($kid)
    {
        DB::getInstance()->query("DELETE FROM wounded WHERE kid=" . (int)$kid);
    }

    /** The row of the village with the decay since the last update applied (created when missing). */
    private function decayed($kid)
    {
        $db = DB::getInstance();
        $kid = (int)$kid;
        $now = time();
        $row = $db->query("SELECT * FROM wounded WHERE kid=$kid")->fetch_assoc();
        if (!$row) {
            $db->query("INSERT IGNORE INTO wounded (kid, updated) VALUES ($kid, $now)");
            return array_merge(array_fill_keys(array_map(fn($i) => 'u' . $i, range(1, self::UNITS)), 0), ['kid' => $kid, 'updated' => $now]);
        }
        $days = ($now - (int)$row['updated']) / 86400;
        if ($days > 0) {
            $factor = pow(self::DAILY_DECAY, $days);
            $set = [];
            for ($i = 1; $i <= self::UNITS; ++$i) {
                $row['u' . $i] = $row['u' . $i] * $factor;
                if ($row['u' . $i] < 1) {
                    $row['u' . $i] = 0;
                }
                $set[] = "u$i=" . $row['u' . $i];
            }
            $set[] = "updated=$now";
            $db->query("UPDATE wounded SET " . implode(",", $set) . " WHERE kid=$kid");
        }
        return $row;
    }
}
