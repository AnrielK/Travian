<?php

namespace Model;

use Core\Config;
use Core\Database\DB;
use Game\Formulas;
use Game\Hero\HeroHelper;
use Game\SpeedCalculator;
use function getGame;
use function getGameElapsedSeconds;
use function getGameSpeed;
use function isServerFinished;
use function logError;
use function make_seed;
use function miliseconds;
use function nanoseconds;

/**
 * Heroes of the computer-controlled players (bots, users.access = 3), under the players' rules:
 * - the hero gets its experience on adventures (AdventureModel::checkForNewAdventures() gives bots adventures too)
 *   and in battles; a bot sends it to the nearest adventure from its village when it is at home with enough
 *   health (a hard adventure only above HARD_HEALTH) and the village has a rally point;
 * - new attribute points go mostly to fighting strength and resource production, some to the attack bonus;
 * - a dead hero is revived in its village when the village can pay the revival costs (it takes the revival time);
 * - BotRaidModel takes a healthy hero along on raids from its village.
 */
class BotHeroModel
{
    const ADVENTURE_HEALTH = 40;
    const HARD_HEALTH = 75;
    const RAID_HEALTH = 60;
    const HEROES_PER_RUN = 20;

    public function handleBotHeroes()
    {
        if (getGameElapsedSeconds() <= 0 || isServerFinished() || !Config::getProperty("dynamic", "fakeAccountProcess")) {
            return;
        }
        make_seed();
        $db = DB::getInstance();
        $now = time();
        $wait = max(60, (int)round(1800 / max(1, getGameSpeed())));   // half an hour of game time between visits
        $result = $db->query("SELECT u.id, u.race, h.kid, h.exp, h.health, h.power, h.offBonus, h.defBonus, h.production
            FROM users u JOIN hero h ON h.uid=u.id
            WHERE u.access=3 AND u.lastHeroExpCheck <= " . ($now - $wait) . " ORDER BY u.lastHeroExpCheck LIMIT " . self::HEROES_PER_RUN);
        while ($result && ($hero = $result->fetch_assoc())) {
            $db->query("UPDATE users SET lastHeroExpCheck=$now WHERE id={$hero['id']}");
            try {
                $this->spendPoints($hero);
                if ((float)$hero['health'] <= 0) {
                    $this->revive($hero);
                } else {
                    $this->startAdventure($hero);
                }
            } catch (\Throwable $e) {
                logError('Bot hero of ' . $hero['id'] . ' failed: ' . $e->getMessage());
            }
        }
    }

    /** Free attribute points of a hero (like SessionHero::getAvailablePoints()). */
    public static function freePoints(array $hero): int
    {
        $level = Formulas::heroLevel($hero['exp']);
        $points = min(100, $level) * 4 + ($level < 100 ? 4 : 0);
        return $points - ($hero['power'] + $hero['offBonus'] + $hero['defBonus'] + $hero['production']);
    }

    private function spendPoints(array $hero): void
    {
        $free = self::freePoints($hero);
        if ($free <= 0) {
            return;
        }
        $add = ['power' => 0, 'offBonus' => 0, 'defBonus' => 0, 'production' => 0];
        $weights = ['power' => 4, 'offBonus' => 2, 'production' => 4];
        for (; $free > 0; --$free) {
            $open = array_filter($weights, static function ($column) use ($hero, $add) {
                return $hero[$column] + $add[$column] < 100;
            }, ARRAY_FILTER_USE_KEY);
            if (!$open) {
                $column = $hero['defBonus'] + $add['defBonus'] < 100 ? 'defBonus' : null;
            } else {
                $roll = mt_rand(1, array_sum($open));
                foreach ($open as $column => $weight) {
                    if (($roll -= $weight) <= 0) {
                        break;
                    }
                }
            }
            if ($column === null) {
                break;
            }
            ++$add[$column];
        }
        DB::getInstance()->query("UPDATE hero SET power=power+{$add['power']}, offBonus=offBonus+{$add['offBonus']},
            defBonus=defBonus+{$add['defBonus']}, production=production+{$add['production']} WHERE uid={$hero['id']}");
        if ($add['production']) {
            \Game\ResourcesHelper::updateVillageResources((int)$hero['kid'], false);
        }
    }

    /** Revives a dead hero in its village (like the hero page: pay the costs, wait the revival time). */
    private function revive(array $hero): void
    {
        $db = DB::getInstance();
        $kid = (int)$hero['kid'];
        if ($db->fetchScalar("SELECT COUNT(*) FROM training WHERE item_id=98 AND kid=$kid")) {
            return;   // already reviving
        }
        if (!$db->fetchScalar("SELECT COUNT(*) FROM vdata WHERE kid=$kid AND owner={$hero['id']}")) {
            $kid = (int)$db->fetchScalar("SELECT kid FROM vdata WHERE owner={$hero['id']} AND capital=1 LIMIT 1");
            if (!$kid) {
                return;
            }
        }
        $level = Formulas::heroLevel($hero['exp']);
        $costs = Formulas::heroRegenerateCost($level, (int)$hero['race']);
        $db->query("UPDATE vdata SET wood=wood-{$costs[0]}, clay=clay-{$costs[1]}, iron=iron-{$costs[2]}, crop=crop-{$costs[3]}
            WHERE kid=$kid AND wood>={$costs[0]} AND clay>={$costs[1]} AND iron>={$costs[2]} AND crop>={$costs[3]}");
        if ($db->affectedRows() !== 1) {
            return;   // not enough resources yet
        }
        $neededTime = Formulas::heroRegenerationTime($level);
        $endTime = $neededTime + (getGame("useNanoseconds") ? nanoseconds() : (getGame("useMilSeconds") ? miliseconds() : time()));
        $db->query("INSERT INTO training (kid, nr, num, item_id, training_time, commence, end_time) VALUES ($kid, 11, 1, 98, $neededTime, $endTime, $endTime)");
        $db->query("UPDATE hero SET kid=$kid WHERE uid={$hero['id']}");
    }

    /** Sends a hero that is at home to the nearest adventure; returns the movement id or 0. */
    public function startAdventure(array $hero): int
    {
        $db = DB::getInstance();
        $kid = (int)$hero['kid'];
        $uid = (int)$hero['id'];
        if ((float)$hero['health'] < self::ADVENTURE_HEALTH) {
            return 0;
        }
        if (!(int)$db->fetchScalar("SELECT u11 FROM units WHERE kid=$kid") || !(new AdventureModel())->getHeroVillageRallyPoint($kid)) {
            return 0;
        }
        $hard = (float)$hero['health'] >= self::HARD_HEALTH ? '' : ' AND dif=0';
        $adventures = $db->query("SELECT kid FROM adventure WHERE uid=$uid AND end=0 AND time>" . time() . $hard);
        $target = 0;
        $best = PHP_INT_MAX;
        while ($adventures && ($row = $adventures->fetch_assoc())) {
            $distance = Formulas::getDistance($kid, (int)$row['kid']);
            if ($distance < $best) {
                $best = $distance;
                $target = (int)$row['kid'];
            }
        }
        if (!$target) {
            return 0;
        }
        $calculator = self::heroCalculator($uid, (int)$hero['race'], $kid, $target);
        $now = miliseconds();
        $units = array_fill(1, 11, 0);
        $units[11] = 1;
        return (int)(new MovementsModel())->addMovementWithSourceMutation(
            static function () use ($kid): bool {
                return Units::debitIfAvailable($kid, [11 => 1]);
            },
            $kid, $target, (int)$hero['race'], $units, 0, 0, 0, 0, 0, MovementsModel::ATTACKTYPE_ADVENTURE,
            $now, $now + 1000 * $calculator->calc()
        );
    }

    /** Speed of the player's hero with its horse and boots (the slowest unit of a troop decides its speed). */
    public static function heroSpeed(int $uid, int $race, bool $cavalryOnly = false): float
    {
        $inventory = DB::getInstance()->query("SELECT horse, shoes FROM inventory WHERE uid=$uid")->fetch_assoc() ?: ['horse' => 0, 'shoes' => 0];
        return (new HeroHelper())->calcTotalSpeed($race, $inventory['horse'], $inventory['shoes'], $cavalryOnly);
    }

    private static function heroCalculator(int $uid, int $race, int $from, int $to): SpeedCalculator
    {
        $inventory = DB::getInstance()->query("SELECT leftHand, shoes FROM inventory WHERE uid=$uid")->fetch_assoc() ?: ['leftHand' => 0, 'shoes' => 0];
        $calculator = new SpeedCalculator();
        $calculator->setFrom($from);
        $calculator->setTo($to);
        $calculator->setLeftHand($inventory['leftHand']);
        $calculator->setShoes($inventory['shoes']);
        $calculator->setMinSpeed(self::heroSpeed($uid, $race));
        $calculator->setArtefactEffect(ArtefactsModel::getArtifactEffectByType($uid, $from, ArtefactsModel::ARTIFACT_INCREASE_SPEED));
        $calculator->setTournamentSqLvl(VillageModel::getTournamentSquireLevel($from));
        $calculator->hasHero();
        return $calculator;
    }
}
