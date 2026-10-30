<?php

namespace Model;

use Core\Config;
use Core\Database\DB;
use Game\Formulas;
use function get_world_tribes;
use function getGame;

/**
 * Data of Statistics → General in the Travian Legends look (template statistics/generalLegends.php): world progress
 * timeline, tribe distribution, and the population, resource, military strength and culture point ranks of a player.
 * Current values and ranks come from the live tables, the day-by-day charts from stats_history (StatisticsHistoryModel).
 * Players = accounts with id > 2 that are not hidden (the same set the player statistics rank).
 */
class StatisticsGeneralModel
{
    /** Players of the population / culture point ranks, like the player statistics (StatisticsModel). */
    private const RANKED = 'id > 1 AND hidden = 0';
    /** Accounts counted as players of the world (without Natars and the Multihunter). */
    private const PLAYERS = 'id > 2 AND hidden = 0';

    private $db;
    private $uid;
    private $user;

    public function __construct(int $uid)
    {
        $this->db = DB::getInstance();
        $this->uid = $uid;
        $this->user = $this->db->query("SELECT id, name, aid, race, total_pop, cp, cp_prod FROM users WHERE id=$uid")->fetch_assoc() ?: [
            'id' => $uid, 'name' => '', 'aid' => 0, 'race' => 1, 'total_pop' => 0, 'cp' => 0, 'cp_prod' => 0,
        ];
    }

    public function build(): array
    {
        $history = new StatisticsHistoryModel();
        if ($this->uid > 2) {
            $history->ensureToday($this->uid);
        }
        $rows = $history->getHistory($this->uid);
        return [
            'serverProgression' => $this->serverProgression(),
            'populationRank' => $this->populationRank($rows),
            'resourceRank' => $this->resourceRank($rows),
            'militaryStrength' => $this->militaryStrength(),
            'culturePointsRank' => $this->culturePointsRank($rows),
        ];
    }

    private function scalar(string $sql, $default = 0)
    {
        return $this->db->fetchScalar($sql, [], $default);
    }

    /** 1 + number of rows of $table (filtered by $where) whose $score beats the player's (ties: lower id wins). */
    private function rankOf(string $scoreSql, $myScore, string $where, string $idColumn = 'id', string $from = 'users'): int
    {
        $myScore = (float)$myScore;
        return 1 + (int)$this->scalar("SELECT COUNT(*) FROM $from WHERE $where AND (($scoreSql) > $myScore OR (($scoreSql) = $myScore AND $idColumn < {$this->uid}))");
    }

    // ------------------------------------------------------------------------------------------- world progression

    private function playerTribe(?string $name): int
    {
        if ($name === null || $name === '') {
            return 0;
        }
        $name = $this->db->real_escape_string($name);
        return (int)$this->scalar("SELECT race FROM users WHERE name='$name' LIMIT 1");
    }

    private function serverProgression(): array
    {
        global $config;
        $start = (int)getGame('start_time');
        $summary = $this->db->query("SELECT * FROM summary LIMIT 1")->fetch_assoc() ?: [];
        $steps = [];
        $steps[] = ['id' => 'start', 'state' => $start > time() ? 'locked' : 'achieved', 'time' => $start];
        if (!empty($summary['first_village_time'])) {
            $steps[] = [
                'id' => 'secondVillage', 'state' => 'achieved', 'player' => $summary['first_village_player_name'],
                'tribe' => $this->playerTribe($summary['first_village_player_name']), 'time' => (int)$summary['first_village_time'],
            ];
        } else {
            $steps[] = ['id' => 'secondVillage', 'state' => 'locked'];
        }
        $artifacts = (int)Config::getProperty('timers', 'ArtifactsReleaseTime');
        if (!empty($summary['first_art_time'])) {
            $steps[] = [
                'id' => 'artifacts', 'state' => 'achieved', 'player' => $summary['first_art_player_name'],
                'tribe' => $this->playerTribe($summary['first_art_player_name']), 'time' => (int)$summary['first_art_time'],
                'release' => $artifacts,
            ];
        } else {
            $steps[] = ['id' => 'artifacts', 'state' => $artifacts > time() ? 'locked' : 'unlocked', 'release' => $artifacts];
        }
        $plans = (int)Config::getProperty('timers', 'wwPlansReleaseTime');
        if (!empty($summary['first_ww_plan_time'])) {
            $steps[] = [
                'id' => 'constructionPlans', 'state' => 'achieved', 'player' => $summary['first_ww_plan_player_name'],
                'tribe' => $this->playerTribe($summary['first_ww_plan_player_name']), 'time' => (int)$summary['first_ww_plan_time'],
                'release' => $plans,
            ];
        } else {
            $steps[] = ['id' => 'constructionPlans', 'state' => $plans > time() ? 'locked' : 'unlocked', 'release' => $plans];
        }
        if (!empty($summary['first_ww_time'])) {
            $steps[] = [
                'id' => 'firstWWLevelOne', 'state' => 'achieved', 'player' => $summary['first_ww_player_name'],
                'tribe' => $this->playerTribe($summary['first_ww_player_name']), 'time' => (int)$summary['first_ww_time'],
            ];
        } else {
            $steps[] = ['id' => 'firstWWLevelOne', 'state' => 'locked'];
        }
        $finished = !empty($config->dynamic->serverFinished);
        $steps[] = [
            'id' => 'serverEnd', 'state' => $finished ? 'achieved' : 'locked',
            'time' => $finished ? (int)$config->dynamic->serverFinishTime : (int)Config::getProperty('timers', 'AutoFinishTime'),
        ];
        // the bar runs up to the last step reached without a gap
        $reached = 0;
        foreach ($steps as $step) {
            if ($step['state'] === 'locked') {
                break;
            }
            ++$reached;
        }

        $tribes = get_world_tribes();
        $counts = [];
        $result = $this->db->query("SELECT race, COUNT(*) c FROM users WHERE " . self::PLAYERS . " GROUP BY race");
        while ($result && ($row = $result->fetch_assoc())) {
            $counts[(int)$row['race']] = (int)$row['c'];
            if ((int)$row['c'] > 0 && !in_array((int)$row['race'], $tribes) && in_array((int)$row['race'], [1, 2, 3, 6, 7, 8])) {
                $tribes[] = (int)$row['race'];
            }
        }
        $total = array_sum(array_intersect_key($counts, array_flip($tribes)));
        $distribution = [];
        foreach ($tribes as $tribe) {
            $distribution[$tribe] = $total ? round(($counts[$tribe] ?? 0) / $total * 100, 1) : 0;
        }

        return [
            'steps' => $steps,
            'reached' => $reached,
            'ageInDays' => $start > time() ? 0 : (int)floor((time() - $start) / 86400),
            'activatedPlayers' => (int)$this->scalar("SELECT COUNT(*) FROM users WHERE " . self::PLAYERS),
            'tribeDistribution' => $distribution,
        ];
    }

    // ------------------------------------------------------------------------------------------- population

    private function populationRank(array $rows): array
    {
        $aid = (int)$this->user['aid'];
        $pop = (int)$this->user['total_pop'];
        $serverRank = $this->rankOf('total_pop', $pop, self::RANKED);
        $allianceRank = $aid ? $this->rankOf('total_pop', $pop, self::RANKED . " AND aid=$aid") : 0;
        $series = [];
        foreach ($rows as $row) {
            if ((int)$row['pop_rank'] > 0) {
                $series[(int)$row['day']] = ['server' => (int)$row['pop_rank'], 'alliance' => (int)$row['ally_pop_rank']];
            }
        }
        $series[StatisticsHistoryModel::today()] = ['server' => $serverRank, 'alliance' => $allianceRank];
        ksort($series);
        return [
            'hasAlliance' => $aid > 0,
            'serverRank' => $serverRank,
            'allianceRank' => $allianceRank,
            'history' => $series,
        ];
    }

    // ------------------------------------------------------------------------------------------- resources

    private function resourceRank(array $rows): array
    {
        $uid = $this->uid;
        $aid = (int)$this->user['aid'];
        $perDay = $this->db->query("SELECT COALESCE(SUM(woodp),0)*24 r1, COALESCE(SUM(clayp),0)*24 r2, COALESCE(SUM(ironp),0)*24 r3, COALESCE(SUM(cropp),0)*24 r4 FROM vdata WHERE owner=$uid")->fetch_assoc();
        $perDay = array_map('intval', $perDay);

        $today = StatisticsHistoryModel::today();
        $todayShare = min(1, max(0, (time() - StatisticsHistoryModel::dayStart($today)) / 86400));
        $soFar = ['r1' => 0, 'r2' => 0, 'r3' => 0, 'r4' => 0];
        $raid = $trade = [];
        $raidIn = $raidOut = $tradeIn = $tradeOut = 0;
        foreach ($rows as $row) {
            $day = (int)$row['day'];
            $share = $day == $today ? $todayShare : ($day > $today ? 0 : 1);
            foreach ($soFar as $k => $v) {
                $soFar[$k] += (int)$row['prod_' . $k] * $share;
            }
            $raidIn += (int)$row['raid_in'];
            $raidOut += (int)$row['raid_out'];
            $tradeIn += (int)$row['trade_in'];
            $tradeOut += (int)$row['trade_out'];
            $raid[$day] = ['in' => (int)$row['raid_in'], 'out' => (int)$row['raid_out']];
            $trade[$day] = ['in' => (int)$row['trade_in'], 'out' => (int)$row['trade_out']];
        }
        $soFar = array_map(function ($x) {
            return (int)round($x);
        }, $soFar);
        $production = array_sum($soFar);

        // ranks: production per day of all villages, and production so far (whole days of the history)
        $dailyScore = "(SELECT COALESCE(SUM(woodp+clayp+ironp+cropp),0) FROM vdata WHERE vdata.owner=users.id)";
        $myDaily = array_sum($perDay) / 24;
        $soFarScore = "(SELECT COALESCE(SUM(prod_r1+prod_r2+prod_r3+prod_r4),0) FROM stats_history h WHERE h.uid=users.id)";
        $mySoFar = (float)$this->scalar("SELECT COALESCE(SUM(prod_r1+prod_r2+prod_r3+prod_r4),0) FROM stats_history WHERE uid=$uid");
        return [
            'hasAlliance' => $aid > 0,
            'positive' => ['raid' => $raidIn, 'trade' => $tradeIn, 'production' => $production, 'sum' => $raidIn + $tradeIn + $production],
            'negative' => ['raid' => $raidOut, 'trade' => $tradeOut, 'sum' => $raidOut + $tradeOut],
            'balance' => $raidIn + $tradeIn + $production - $raidOut - $tradeOut,
            'production' => ['perDay' => $perDay, 'soFar' => $soFar],
            'rank' => [
                'perDay' => [
                    'server' => $this->rankOf($dailyScore, $myDaily, self::PLAYERS),
                    'alliance' => $aid ? $this->rankOf($dailyScore, $myDaily, self::PLAYERS . " AND aid=$aid") : 0,
                ],
                'soFar' => [
                    'server' => $this->rankOf($soFarScore, $mySoFar, self::PLAYERS),
                    'alliance' => $aid ? $this->rankOf($soFarScore, $mySoFar, self::PLAYERS . " AND aid=$aid") : 0,
                ],
            ],
            'raidHistory' => $raid,
            'tradeHistory' => $trade,
        ];
    }

    // ------------------------------------------------------------------------------------------- military strength

    /** Offence and defence of the own troops at home in every player village (base unit values, without the hero). */
    private function villageStrengths(): array
    {
        $units = Formulas::$data['units'];
        $result = $this->db->query("SELECT v.kid, v.owner, v.name, u.aid, t.race, t.u1, t.u2, t.u3, t.u4, t.u5, t.u6, t.u7, t.u8, t.u9, t.u10
            FROM vdata v JOIN users u ON u.id=v.owner JOIN units t ON t.kid=v.kid
            WHERE v.owner > 2 AND u.hidden = 0");
        $villages = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $race = (int)$row['race'] ?: 1;
            $off = $defI = $defC = 0;
            for ($i = 1; $i <= 10; ++$i) {
                $count = (int)$row['u' . $i];
                $unit = $units[$race - 1][$i - 1] ?? null;
                if (!$count || !$unit) {
                    continue;
                }
                $off += $count * (int)$unit['off'];
                $defI += $count * (int)$unit['def_i'];
                $defC += $count * (int)$unit['def_c'];
            }
            $villages[(int)$row['kid']] = [
                'kid' => (int)$row['kid'], 'owner' => (int)$row['owner'], 'aid' => (int)$row['aid'], 'name' => $row['name'],
                'off' => $off, 'defI' => $defI, 'defC' => $defC,
            ];
        }
        return $villages;
    }

    private function militaryStrength(): array
    {
        $uid = $this->uid;
        $aid = (int)$this->user['aid'];
        $villages = $this->villageStrengths();
        $better = function (array $a, array $b, string $key): bool {
            return $a[$key] > $b[$key] || ($a[$key] == $b[$key] && $a['kid'] < $b['kid']);
        };

        $own = [];
        foreach ($villages as $village) {
            if ($village['owner'] != $uid) {
                continue;
            }
            $server = $alliance = 1;
            foreach ($villages as $other) {
                if ($better($other, $village, 'off')) {
                    ++$server;
                    if ($aid && $other['aid'] == $aid) {
                        ++$alliance;
                    }
                }
            }
            $own[] = [
                'kid' => $village['kid'], 'name' => $village['name'], 'off' => $village['off'],
                'def' => ($village['defI'] + $village['defC']) / 2, 'defI' => $village['defI'], 'defC' => $village['defC'],
                'offRank' => ['server' => $server, 'alliance' => $aid ? $alliance : 0],
            ];
        }
        usort($own, function ($a, $b) {
            return $b['off'] <=> $a['off'] ?: $a['kid'] <=> $b['kid'];
        });

        // defence: the sum over all villages of a player, ranked among the players
        $players = [];
        foreach ($villages as $village) {
            $p = &$players[$village['owner']];
            $p['owner'] = $village['owner'];
            $p['aid'] = $village['aid'];
            $p['def'] = ($p['def'] ?? 0) + ($village['defI'] + $village['defC']) / 2;
            unset($p);
        }
        $myDef = $players[$uid]['def'] ?? 0;
        $defServer = $defAlliance = 1;
        foreach ($players as $owner => $p) {
            if ($p['def'] > $myDef || ($p['def'] == $myDef && $owner < $uid)) {
                ++$defServer;
                if ($aid && $p['aid'] == $aid) {
                    ++$defAlliance;
                }
            }
        }
        $totalPlayers = (int)$this->scalar("SELECT COUNT(*) FROM users WHERE " . self::PLAYERS);
        return [
            'hasAlliance' => $aid > 0,
            'totalVillages' => count($villages),
            'totalPlayers' => max($totalPlayers, count($players)),
            'villages' => $own,
            'defence' => [
                'total' => array_sum(array_column($own, 'def')),
                'infantry' => array_sum(array_column($own, 'defI')),
                'cavalry' => array_sum(array_column($own, 'defC')),
                'rank' => ['server' => $defServer, 'alliance' => $aid ? $defAlliance : 0],
            ],
        ];
    }

    // ------------------------------------------------------------------------------------------- culture points

    private function culturePointsRank(array $rows): array
    {
        $aid = (int)$this->user['aid'];
        $cp = (int)$this->user['cp'];
        $cpProd = (int)$this->user['cp_prod'];
        $rank = function (string $column, int $mine) use ($aid) {
            return [
                'server' => $this->rankOf($column, $mine, self::PLAYERS),
                'alliance' => $aid ? $this->rankOf($column, $mine, self::PLAYERS . " AND aid=$aid") : 0,
            ];
        };
        $progression = ['perDay' => [], 'soFar' => []];
        foreach ($rows as $row) {
            if ((int)$row['pop_rank'] > 0) {
                $progression['perDay'][(int)$row['day']] = (int)$row['cp_prod'];
                $progression['soFar'][(int)$row['day']] = (int)$row['cp'];
            }
        }
        $today = StatisticsHistoryModel::today();
        $progression['perDay'][$today] = $cpProd;
        $progression['soFar'][$today] = $cp;
        ksort($progression['perDay']);
        ksort($progression['soFar']);
        return [
            'hasAlliance' => $aid > 0,
            'totalPlayers' => (int)$this->scalar("SELECT COUNT(*) FROM users WHERE " . self::PLAYERS),
            'totalPlayersInAlliance' => $aid ? (int)$this->scalar("SELECT COUNT(*) FROM users WHERE " . self::PLAYERS . " AND aid=$aid") : 0,
            'perDay' => ['rank' => $rank('cp_prod', $cpProd), 'value' => $cpProd],
            'soFar' => ['rank' => $rank('cp', $cp), 'value' => $cp],
            'distribution' => [
                'perDay' => ['buildings' => $cpProd],
                'soFar' => ['buildings' => $cp],
            ],
            'progression' => $progression,
        ];
    }
}
