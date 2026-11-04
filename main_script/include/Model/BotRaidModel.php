<?php

namespace Model;

use Core\Config;
use Core\Database\DB;
use Game\Formulas;
use Game\SpeedCalculator;
use function getGameElapsedSeconds;
use function getGameSpeed;
use function isServerFinished;
use function logError;
use function make_seed;
use function miliseconds;
use function nrToUnitId;

/**
 * Raids of the computer-controlled players (bots: users.access = 3). Building, training and new villages of the
 * bots are done by FakeUserModel / Core\AI; this sends their offensive troops out.
 *
 * Every village of a bot raids once every 2-4 hours of game time (divided by the world speed, vdata.botRaidTime).
 * It needs a rally point, at most MAX_RAIDS_OUT raids already on the way and enough offensive units (infantry and cavalry whose attack is above their defence;
 * defenders, rams, catapults, chiefs and settlers stay at home; a healthy hero goes along now and then). The target is a nearby (at most
 * RADIUS fields) unoccupied oasis, Natar farm, village of another bot or of a player, weaker than the raid:
 * its troops at home must not defend more than half of the raid's attack. Players and bots under beginner
 * protection or on vacation are skipped; a bot still under protection itself raids only oases and Natars, like
 * the farm list (FarmListModel::autoRaidFarmList). A target where the bot's last raid lost half of its troops is
 * left alone for a day of game time. Members of the bot's alliance are never raided (alliances of bots:
 * BotAllianceModel).
 */
class BotRaidModel
{
    const RADIUS = 12;
    const MIN_ATTACK = 500;          // smallest raid (sum of the units' attack values) at speed up to 100
    const MAX_RAIDS_OUT = 2;         // raids of one village on the way at the same time
    const VILLAGES_PER_RUN = 10;

    public function handleBotRaids()
    {
        if (getGameElapsedSeconds() <= 0 || isServerFinished() || !Config::getProperty("dynamic", "fakeAccountProcess")) {
            return;
        }
        make_seed();
        $db = DB::getInstance();
        $now = time();
        $result = $db->query("SELECT v.kid, v.owner, v.botRaidTime, u.race, u.protection FROM vdata v JOIN users u ON u.id=v.owner
            WHERE u.access=3 AND v.isWW=0 AND v.botRaidTime <= $now ORDER BY v.botRaidTime LIMIT " . self::VILLAGES_PER_RUN);
        if (!$result) {
            return;
        }
        while ($row = $result->fetch_assoc()) {
            $db->query("UPDATE vdata SET botRaidTime=" . ($now + self::interval()) . " WHERE kid={$row['kid']}");
            if ((int)$row['botRaidTime'] === 0) {
                continue;   // first visit: only plan the first raid
            }
            try {
                $this->raidFrom((int)$row['kid'], (int)$row['owner'], (int)$row['race'], (int)$row['protection'] > $now);
            } catch (\Throwable $e) {
                logError('Bot raid from ' . $row['kid'] . ' failed: ' . $e->getMessage());
            }
        }
    }

    /** Seconds until a village of a bot raids again: 2-4 hours of game time. */
    public static function interval(): int
    {
        return max(120, (int)round(mt_rand(7200, 14400) / max(1, getGameSpeed())));
    }

    /** Sends one raid from village $kid; returns the movement id or 0. */
    public function raidFrom(int $kid, int $owner, int $race, bool $protected): int
    {
        $db = DB::getInstance();
        if (!(new AdventureModel())->getHeroVillageRallyPoint($kid)) {
            return 0;
        }
        $out = (int)$db->fetchScalar("SELECT COUNT(id) FROM movement WHERE kid=$kid AND mode=0 AND attack_type=" . MovementsModel::ATTACKTYPE_RAID);
        if ($out >= self::MAX_RAIDS_OUT) {
            return 0;
        }
        $units = $db->query("SELECT * FROM units WHERE kid=$kid")->fetch_assoc();
        if (!$units) {
            return 0;
        }
        $send = array_fill(1, 11, 0);
        $attack = 0;
        $speeds = [];
        foreach (self::offensiveUnits($race) as $nr => $off) {
            $count = (int)$units['u' . $nr];
            if ($count <= 0) {
                continue;
            }
            $count = max(1, (int)floor($count * mt_rand(70, 100) / 100));
            $send[$nr] = $count;
            $attack += $count * $off;
            $speeds[] = Formulas::uSpeed(nrToUnitId($nr, $race));
        }
        if ($attack < self::MIN_ATTACK * max(1, getGameSpeed() / 100)) {
            return 0;
        }
        // a healthy hero at home goes along now and then (BotHeroModel sends it on adventures otherwise)
        $withHero = false;
        if ((int)$units['u11'] > 0 && mt_rand(1, 2) === 1) {
            $health = $db->fetchScalar("SELECT health FROM hero WHERE uid=$owner AND kid=$kid");
            if ($health !== false && (float)$health >= BotHeroModel::RAID_HEALTH) {
                $withHero = true;
                $send[11] = 1;
                $cavalryOnly = (new SpeedCalculator())->isCavalryOnly(array_map(static function ($nr) use ($race) {
                    return nrToUnitId($nr, $race);
                }, array_keys(array_filter(array_slice($send, 0, 10, true)))));
                $speeds[] = BotHeroModel::heroSpeed($owner, $race, $cavalryOnly);
            }
        }
        $target = $this->chooseTarget($kid, $owner, $attack, $protected);
        if (!$target) {
            return 0;
        }
        $calculator = new SpeedCalculator();
        $calculator->setFrom($kid);
        $calculator->setTo($target);
        $calculator->setMinSpeed($speeds);
        if ($withHero) {
            $calculator->hasHero();
            $calculator->troopsWithHero();
        }
        $fields = $db->query("SELECT * FROM fdata WHERE kid=$kid")->fetch_assoc() ?: [];
        for ($i = 19; $i <= 38; ++$i) {
            if ((int)($fields['f' . $i . 't'] ?? 0) === 14) {
                $calculator->setTournamentSqLvl((int)$fields['f' . $i]);
                break;
            }
        }
        $calculator->setArtefactEffect(ArtefactsModel::getArtifactEffectByType($owner, $kid, ArtefactsModel::ARTIFACT_INCREASE_SPEED));
        $now = miliseconds();
        $debit = array_filter($send);
        return (int)(new MovementsModel())->addMovementWithSourceMutation(
            static function () use ($kid, $debit): bool {
                return Units::debitIfAvailable($kid, $debit);
            },
            $kid,
            $target,
            $race,
            $send,
            0,
            0,
            0,
            0,
            0,
            MovementsModel::ATTACKTYPE_RAID,
            $now,
            $now + 1000 * $calculator->calc()
        );
    }

    /** Units a bot raids with: nr => attack value (infantry and cavalry that attack better than they defend). */
    public static function offensiveUnits(int $race): array
    {
        $units = [];
        for ($nr = 1; $nr <= 6; ++$nr) {
            $data = Formulas::$data['units'][$race - 1][$nr - 1] ?? null;
            if (!$data || (int)$data['cap'] <= 0) {
                continue;
            }
            if ($data['off'] > max($data['def_i'], $data['def_c'])) {
                $units[$nr] = (int)$data['off'];
            }
        }
        return $units;
    }

    /** Defence of the troops at home in $kid (the higher of infantry and cavalry defence of each unit). */
    public static function homeDefence(int $kid): int
    {
        $row = DB::getInstance()->query("SELECT * FROM units WHERE kid=$kid")->fetch_assoc();
        if (!$row || !$row['race']) {
            return 0;
        }
        $defence = 0;
        for ($nr = 1; $nr <= 10; ++$nr) {
            $data = Formulas::$data['units'][$row['race'] - 1][$nr - 1] ?? null;
            if ($data && $row['u' . $nr] > 0) {
                $defence += $row['u' . $nr] * max($data['def_i'], $data['def_c']);
            }
        }
        return (int)$defence;
    }

    /** Fields of the map at most RADIUS fields from $kid (the map wraps around its edges). */
    private static function nearbyKids(int $kid): array
    {
        $center = Formulas::kid2xy($kid);
        $radius = min(self::RADIUS, defined('MAP_SIZE') ? (int)MAP_SIZE : self::RADIUS);
        $kids = [];
        for ($dx = -$radius; $dx <= $radius; ++$dx) {
            for ($dy = -$radius; $dy <= $radius; ++$dy) {
                if (($dx || $dy) && $dx * $dx + $dy * $dy <= $radius * $radius) {
                    $kids[Formulas::xy2kid($center['x'] + $dx, $center['y'] + $dy)] = true;
                }
            }
        }
        unset($kids[$kid]);
        return array_keys($kids);
    }

    /** The target of a raid with $attack from $kid (owned by $owner), or 0 when nothing is worth raiding. */
    public function chooseTarget(int $kid, int $owner, int $attack, bool $protected): int
    {
        $db = DB::getInstance();
        $kids = self::nearbyKids($kid);
        if (!$kids) {
            return 0;
        }
        $in = implode(',', $kids);
        $now = time();
        $candidates = [];
        $oases = $db->query("SELECT kid FROM odata WHERE owner=0 AND kid IN($in)");
        while ($oases && ($row = $oases->fetch_assoc())) {
            $candidates[] = (int)$row['kid'];
        }
        $aid = (int)$db->fetchScalar("SELECT aid FROM users WHERE id=$owner");
        $ally = $aid > 0 ? " AND u.aid <> $aid" : '';
        $players = $protected ? 'FALSE' : "(u.id > 2 AND u.id <> $owner AND u.access > 0 AND u.protection <= $now AND u.vacationActiveTil <= $now$ally)";
        $villages = $db->query("SELECT v.kid FROM vdata v JOIN users u ON u.id=v.owner
            WHERE v.kid IN($in) AND v.isWW=0 AND ((u.id=1 AND v.isFarm=1) OR $players)");
        while ($villages && ($row = $villages->fetch_assoc())) {
            $candidates[] = (int)$row['kid'];
        }
        if (!$candidates) {
            return 0;
        }
        usort($candidates, static function ($a, $b) use ($kid) {
            return Formulas::getDistance($kid, $a) <=> Formulas::getDistance($kid, $b);
        });
        $suitable = [];
        $cooldown = $now - (int)round(86400 / max(1, getGameSpeed()));
        foreach ($candidates as $target) {
            if (self::homeDefence($target) * 2 > $attack) {
                continue;
            }
            $sent = (int)$db->fetchScalar("SELECT COUNT(id) FROM movement WHERE kid=$kid AND to_kid=$target AND mode=0");
            if ($sent > 0) {
                continue;
            }
            $lastLosses = $db->fetchScalar("SELECT losses FROM ndata WHERE uid=$owner AND to_kid=$target AND time >= $cooldown ORDER BY id DESC LIMIT 1");
            if ($lastLosses !== null && $lastLosses !== false && (int)$lastLosses >= 50) {
                continue;
            }
            $suitable[] = $target;
            if (sizeof($suitable) >= 3) {
                break;
            }
        }
        return $suitable ? $suitable[mt_rand(0, sizeof($suitable) - 1)] : 0;
    }
}
