<?php

namespace Model;

use Core\Database\DB;
use Core\Session;
use Core\Village;

/**
 * Task list of the Travian Legends style (graphic pack 29c89d54, tasks.php).
 *
 * Every task is a chain of steps ("reach 50 population", "100", …). The current step of a task is the first one
 * that has not been collected yet; once its goal is reached the player collects resources (each of the four
 * types) and hero experience. Progress is computed from the live game data, only the collected step count is
 * stored (table ov_task_progress, created on first use so existing worlds need no migration).
 *
 * Village tasks are bound to the active village (kid), general tasks to the account (kid = 0).
 */
class TaskListModel
{
    public const TABLE = 'ov_task_progress';

    /** Tribe wall building per race. */
    private const WALLS = [1 => 31, 2 => 32, 3 => 33, 6 => 42, 7 => 43];

    private static bool $tableReady = false;

    private Session $session;
    private Village $village;
    private ?array $collected = null;
    private ?array $account = null;

    public function __construct()
    {
        $this->session = Session::getInstance();
        $this->village = Village::getInstance();
        self::ensureTable();
    }

    public static function ensureTable(): void
    {
        if (self::$tableReady) {
            return;
        }
        DB::getInstance()->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE . '` (
            `uid` INT UNSIGNED NOT NULL,
            `kid` INT UNSIGNED NOT NULL,
            `task` VARCHAR(48) NOT NULL,
            `steps` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`uid`, `kid`, `task`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        self::$tableReady = true;
    }

    /**
     * Task chains. 'type' picks the picture/label style of the pack's #tasks CSS, 'steps' are
     * [goal, resources per type, experience] (resource tasks: [fields, level, resources, experience]).
     */
    private function definitions(string $scope): array
    {
        if ($scope === 'general') {
            return [
                'villages' => ['type' => 'count', 'image' => 'populationProgress', 'title' => T('Tasks', 'villages', 'Villages'),
                    'steps' => [[2, 600, 40], [3, 1200, 60], [5, 2500, 100], [10, 5000, 200], [20, 10000, 400]]],
                'populationTotal' => ['type' => 'count', 'image' => 'populationProgress', 'title' => T('Tasks', 'populationTotal', 'Population of all villages'),
                    'steps' => [[100, 375, 25], [250, 750, 40], [500, 1500, 60], [1000, 2500, 100], [2500, 5000, 150], [5000, 10000, 250]]],
                'culturePointsTotal' => ['type' => 'count', 'image' => 'culturePointsProductionProgress', 'title' => T('Tasks', 'culturePointsTotal', 'Culture points production of all villages'),
                    'steps' => [[100, 375, 25], [250, 750, 40], [500, 1500, 60], [1000, 2500, 100], [2500, 5000, 150]]],
            ];
        }
        $resource = static fn(int $gid, string $title) => ['type' => 'fields', 'gid' => $gid, 'image' => 'building g' . $gid, 'title' => $title,
            'steps' => [[1, 2, 150, 10], [1, 4, 300, 20], [0, 4, 600, 30], [1, 7, 900, 40], [0, 7, 1500, 60], [1, 10, 2500, 80], [0, 10, 4000, 120]]];
        $building = static fn(int $gid, array $levels, int $base = 150) => ['type' => 'level', 'gid' => $gid, 'image' => 'building g' . $gid,
            'title' => T('Buildings', $gid . '.title'),
            'steps' => array_map(static fn($level) => [$level, $base * max(1, $level), (int)round(10 + $level * 4)], $levels)];
        $wall = self::WALLS[$this->session->getRace()] ?? 31;

        return [
            'population' => ['type' => 'count', 'image' => 'populationProgressInVillage', 'title' => T('Tasks', 'population', 'Population'),
                'steps' => [[50, 375, 25], [100, 750, 40], [200, 1200, 60], [350, 2000, 80], [500, 3000, 100], [750, 4500, 140], [1000, 6000, 200]]],
            'culturePoints' => ['type' => 'count', 'image' => 'culturePointsProductionProgressInVillage', 'title' => T('Tasks', 'culturePoints', 'Culture points production'),
                'steps' => [[50, 375, 25], [100, 750, 40], [200, 1200, 60], [350, 2000, 80], [500, 3000, 100]]],
            'embassy' => $building(18, [1, 5, 10, 20], 300),
            'woodcutter' => $resource(1, T('Buildings', '1.title') . 's'),
            'claypit' => $resource(2, T('Buildings', '2.title') . 's'),
            'wall' => $building($wall, [1, 5, 10, 15, 20]),
            'cropland' => $resource(4, T('Buildings', '4.title') . 's'),
            'ironmine' => $resource(3, T('Buildings', '3.title') . 's'),
            'mainBuilding' => $building(15, [3, 5, 10, 15, 20]),
            'warehouse' => $building(10, [1, 3, 7, 12, 20]),
            'granary' => $building(11, [1, 3, 7, 12, 20]),
            'rallyPoint' => $building(16, [1, 5, 10]),
            'marketplace' => $building(17, [1, 5, 10, 20]),
            'cranny' => $building(23, [1, 5, 10]),
            'barracks' => $building(19, [1, 3, 5, 10, 20]),
            'academy' => $building(22, [1, 5, 10, 20]),
            'smithy' => $building(13, [1, 5, 10, 20]),
            'stable' => $building(20, [1, 5, 10, 20]),
            'residence' => $building(25, [1, 10, 20], 400),
        ];
    }

    /** All tasks of a scope with their current step, progress and state (open / achieved / collected). */
    public function getTasks(string $scope): array
    {
        $tasks = [];
        foreach ($this->definitions($scope) as $key => $definition) {
            $tasks[] = $this->describe($scope, $key, $definition);
        }
        // Collectable first, then the closest ones; finished chains at the end.
        usort($tasks, static function ($a, $b) {
            $order = ['achieved' => 0, 'open' => 1, 'collected' => 2];
            return [$order[$a['state']], -$a['ratio']] <=> [$order[$b['state']], -$b['ratio']];
        });
        return $tasks;
    }

    /** Whether any village or general task waits to be collected. */
    public function hasCollectable(): bool
    {
        foreach (['village', 'general'] as $scope) {
            foreach ($this->definitions($scope) as $key => $definition) {
                if ($this->describe($scope, $key, $definition)['state'] === 'achieved') {
                    return true;
                }
            }
        }
        return false;
    }

    /** Collects the current step of a task when its goal is reached. Returns the granted reward or null. */
    public function collect(string $scope, string $key): ?array
    {
        $definitions = $this->definitions($scope);
        if (!isset($definitions[$key])) {
            return null;
        }
        $task = $this->describe($scope, $key, $definitions[$key]);
        if ($task['state'] !== 'achieved') {
            return null;
        }
        $db = DB::getInstance();
        $kid = $scope === 'general' ? 0 : $this->village->getKid();
        $uid = $this->session->getPlayerId();
        // Only the request that moves the counter from its current value gets the reward.
        $db->run('INSERT IGNORE INTO `' . self::TABLE . '` (uid, kid, task, steps) VALUES (?, ?, ?, 0)', [$uid, $kid, $key]);
        $stmt = $db->run('UPDATE `' . self::TABLE . '` SET steps = steps + 1 WHERE uid = ? AND kid = ? AND task = ? AND steps = ?',
            [$uid, $kid, $key, $task['step']]);
        if (!$stmt || $stmt->affected_rows !== 1) {
            return null;
        }
        $resources = (int)$task['rewardResources'];
        $this->village->modifyResources([$resources, $resources, $resources, $resources], 1);
        $experience = (int)$task['rewardExperience'];
        if ($experience > 0) {
            $db->query('UPDATE hero SET exp = exp + ' . $experience . ' WHERE uid = ' . (int)$uid);
        }
        $this->collected = null;
        return ['resources' => $resources, 'experience' => $experience];
    }

    /** Bonus on all rewards from the hero level (Legends: rewards grow with the hero). */
    public function getBonusPercent(int $heroLevel): int
    {
        return min(100, max(0, $heroLevel - 1) * 2);
    }

    public function getHeroLevel(): int
    {
        return $this->session->hero ? (int)$this->session->hero->getLevel() : 0;
    }

    private function describe(string $scope, string $key, array $definition): array
    {
        $kid = $scope === 'general' ? 0 : $this->village->getKid();
        $done = $this->collectedSteps($kid, $key);
        $steps = $definition['steps'];
        $finished = $done >= count($steps);
        $step = $steps[min($done, count($steps) - 1)];
        $bonus = 1 + $this->getBonusPercent($this->getHeroLevel()) / 100;
        $task = [
            'scope' => $scope,
            'key' => $key,
            'type' => $definition['type'],
            'image' => $definition['image'],
            'title' => $definition['title'],
            'step' => $done,
            'steps' => count($steps),
        ];
        if ($definition['type'] === 'fields') {
            [$count, $level, $resources, $experience] = $step;
            $fields = $this->village->getTypeLevel($definition['gid']);
            $goal = $count ?: count($fields);
            $value = count(array_filter($fields, static fn($l) => $l >= $level));
            $task += ['value' => min($value, $goal), 'goal' => $goal, 'level' => $level];
        } elseif ($definition['type'] === 'level') {
            [$goal, $resources, $experience] = $step;
            $value = max($this->village->getTypeLevel($definition['gid']) ?: [0]);
            $task += ['value' => min($value, $goal), 'goal' => $goal];
        } else {
            [$goal, $resources, $experience] = $step;
            $task += ['value' => min($this->countValue($key), $goal), 'goal' => $goal];
        }
        $task['rewardResources'] = (int)round($resources * $bonus);
        $task['rewardExperience'] = (int)round($experience * $bonus);
        $task['ratio'] = $task['goal'] > 0 ? $task['value'] / $task['goal'] : 0;
        $task['state'] = $finished ? 'collected' : ($task['value'] >= $task['goal'] ? 'achieved' : 'open');
        return $task;
    }

    private function countValue(string $key): int
    {
        switch ($key) {
            case 'population':
                return (int)$this->village->getPop();
            case 'culturePoints':
                return (int)$this->village->getCP();
        }
        $account = $this->account();
        return (int)($account[$key] ?? 0);
    }

    private function account(): array
    {
        if ($this->account === null) {
            $uid = (int)$this->session->getPlayerId();
            $row = DB::getInstance()->query("SELECT COUNT(*), COALESCE(SUM(pop), 0), COALESCE(SUM(cp), 0) FROM vdata WHERE owner = $uid");
            [$villages, $population, $culture] = $row ? $row->fetch_row() : [0, 0, 0];
            $this->account = ['villages' => (int)$villages, 'populationTotal' => (int)$population, 'culturePointsTotal' => (int)$culture];
        }
        return $this->account;
    }

    private function collectedSteps(int $kid, string $key): int
    {
        if ($this->collected === null) {
            $this->collected = [];
            $uid = (int)$this->session->getPlayerId();
            $result = DB::getInstance()->query('SELECT kid, task, steps FROM `' . self::TABLE . "` WHERE uid = $uid");
            while ($result && ($row = $result->fetch_assoc())) {
                $this->collected[$row['kid'] . ':' . $row['task']] = (int)$row['steps'];
            }
        }
        return $this->collected[$kid . ':' . $key] ?? 0;
    }
}
