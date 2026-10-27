<?php
/**
 * Task list of the Travian Legends look ("Přehled úkolů", tasks.php) – port of Model\TaskListModel of the main branch.
 *
 * Every task is a chain of steps ("reach 50 population", "100", …). The current step is the first one not collected
 * yet; once its goal is reached the player collects resources (each of the four types, into the active village) and
 * hero experience. Progress comes from the live game data, only the number of collected steps is stored
 * (table ov_task_progress, created on first use). Village tasks belong to the active village (kid), general tasks to
 * the account (kid = 0).
 */
final class LegendsTasks
{
    const TABLE = 'ov_task_progress';
    /** Tribe wall per race. */
    const WALLS = [1 => 31, 2 => 32, 3 => 33, 6 => 42, 7 => 43];

    private static $ready = false;
    private $uid;
    private $kid;
    private $tribe;
    private $village;
    private $collected = null;
    private $account = null;

    public function __construct()
    {
        global $session, $village;
        $this->uid = (int)$session->uid;
        $this->tribe = (int)$session->tribe;
        $this->village = $village;
        $this->kid = (int)$village->wid;
        self::ensureTable();
    }

    private static function db(): ?PDO
    {
        $pdo = function_exists('travian_pdo') ? travian_pdo() : null;
        return $pdo instanceof PDO ? $pdo : null;
    }

    public static function ensureTable(): void
    {
        if (self::$ready || !($db = self::db())) return;
        try {
            $db->exec('CREATE TABLE IF NOT EXISTS ' . self::TABLE . ' (
                uid INT NOT NULL,
                kid INT NOT NULL,
                task VARCHAR(48) NOT NULL,
                steps INT NOT NULL DEFAULT 0,
                PRIMARY KEY (uid, kid, task)
            )');
        } catch (Throwable $e) {
            error_log('tasks: ' . $e->getMessage());
        }
        self::$ready = true;
    }

    private static function t(string $const, string $default): string
    {
        return defined($const) ? constant($const) : $default;
    }

    private static function building(int $gid): string
    {
        global $lang;
        return (string)($lang['buildings'][$gid] ?? ('#' . $gid));
    }

    /** Task chains: steps [goal, resources per type, experience]; resource field tasks [fields (0 = all), level, resources, experience]. */
    private function definitions(string $scope): array
    {
        if ($scope === 'general') {
            return [
                'villages' => ['type' => 'count', 'image' => 'populationProgress', 'title' => self::t('TASKS_VILLAGES', 'Villages'),
                    'steps' => [[2, 600, 40], [3, 1200, 60], [5, 2500, 100], [10, 5000, 200], [20, 10000, 400]]],
                'populationTotal' => ['type' => 'count', 'image' => 'populationProgress', 'title' => self::t('TASKS_POPULATION_TOTAL', 'Population of all villages'),
                    'steps' => [[100, 375, 25], [250, 750, 40], [500, 1500, 60], [1000, 2500, 100], [2500, 5000, 150], [5000, 10000, 250]]],
                'culturePointsTotal' => ['type' => 'count', 'image' => 'culturePointsProductionProgress', 'title' => self::t('TASKS_CULTURE_TOTAL', 'Culture points production of all villages'),
                    'steps' => [[100, 375, 25], [250, 750, 40], [500, 1500, 60], [1000, 2500, 100], [2500, 5000, 150]]],
            ];
        }
        $resource = function (int $gid) {
            return ['type' => 'fields', 'gid' => $gid, 'image' => 'building g' . $gid, 'title' => self::building($gid),
                'steps' => [[1, 2, 150, 10], [1, 4, 300, 20], [0, 4, 600, 30], [1, 7, 900, 40], [0, 7, 1500, 60], [1, 10, 2500, 80], [0, 10, 4000, 120]]];
        };
        $building = function (int $gid, array $levels, int $base = 150) {
            return ['type' => 'level', 'gid' => $gid, 'image' => 'building g' . $gid, 'title' => self::building($gid),
                'steps' => array_map(function ($level) use ($base) { return [$level, $base * max(1, $level), (int)round(10 + $level * 4)]; }, $levels)];
        };
        $wall = self::WALLS[$this->tribe] ?? 31;
        return [
            'population' => ['type' => 'count', 'image' => 'populationProgressInVillage', 'title' => self::t('TASKS_POPULATION', 'Population'),
                'steps' => [[50, 375, 25], [100, 750, 40], [200, 1200, 60], [350, 2000, 80], [500, 3000, 100], [750, 4500, 140], [1000, 6000, 200]]],
            'culturePoints' => ['type' => 'count', 'image' => 'culturePointsProductionProgressInVillage', 'title' => self::t('TASKS_CULTURE', 'Culture points production'),
                'steps' => [[50, 375, 25], [100, 750, 40], [200, 1200, 60], [350, 2000, 80], [500, 3000, 100]]],
            'embassy' => $building(18, [1, 5, 10, 20], 300),
            'woodcutter' => $resource(1),
            'claypit' => $resource(2),
            'wall' => $building($wall, [1, 5, 10, 15, 20]),
            'cropland' => $resource(4),
            'ironmine' => $resource(3),
            'mainBuilding' => $building(15, [3, 5, 10, 15, 20]),
            'warehouse' => $building(10, [1, 3, 7, 12, 20]),
            'granary' => $building(11, [1, 3, 7, 12, 20]),
            'rallyPoint' => $building(16, [1, 5, 10]),
            'marketplace' => $building(17, [1, 5, 10, 20]),
            'cranny' => $building(23, [1, 5, 10]),
            'barracks' => $building(19, [1, 3, 5, 10, 20]),
            'academy' => $building(22, [1, 5, 10, 20]),
            'smithy' => $building(12, [1, 5, 10, 20]),
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
        $order = ['achieved' => 0, 'open' => 1, 'collected' => 2];
        usort($tasks, function ($a, $b) use ($order) {
            return [$order[$a['state']], -$a['ratio']] <=> [$order[$b['state']], -$b['ratio']];
        });
        return $tasks;
    }

    /** Whether a village or general task waits to be collected (mentor's exclamation bubble). */
    public function hasCollectable(): bool
    {
        foreach (['village', 'general'] as $scope) {
            foreach ($this->definitions($scope) as $key => $definition) {
                if ($this->describe($scope, $key, $definition)['state'] === 'achieved') return true;
            }
        }
        return false;
    }

    /** Collects the current step of a task when its goal is reached; returns the reward or null. */
    public function collect(string $scope, string $key): ?array
    {
        global $database;
        $definitions = $this->definitions($scope);
        if (!isset($definitions[$key]) || !($db = self::db())) return null;
        $task = $this->describe($scope, $key, $definitions[$key]);
        if ($task['state'] !== 'achieved') return null;
        $kid = $scope === 'general' ? 0 : $this->kid;
        try {
            $db->prepare('INSERT INTO ' . self::TABLE . ' (uid, kid, task, steps) SELECT ?, ?, ?, 0 WHERE NOT EXISTS
                (SELECT 1 FROM ' . self::TABLE . ' WHERE uid = ? AND kid = ? AND task = ?)')->execute([$this->uid, $kid, $key, $this->uid, $kid, $key]);
            // only the request that moves the counter from its current value gets the reward
            $st = $db->prepare('UPDATE ' . self::TABLE . ' SET steps = steps + 1 WHERE uid = ? AND kid = ? AND task = ? AND steps = ?');
            $st->execute([$this->uid, $kid, $key, $task['step']]);
            if ($st->rowCount() !== 1) return null;
        } catch (Throwable $e) {
            error_log('tasks: ' . $e->getMessage());
            return null;
        }
        $res = (int)$task['rewardResources'];
        $database->modifyResource($this->kid, $res, $res, $res, $res, 1);
        $exp = (int)$task['rewardExperience'];
        if ($exp > 0) $database->query('UPDATE hero SET experience = experience + ' . $exp . ' WHERE uid = ' . $this->uid);
        $this->collected = null;
        return ['resources' => $res, 'experience' => $exp];
    }

    /** Bonus on all rewards from the hero level (Legends: rewards grow with the hero). */
    public static function bonusPercent(int $heroLevel): int
    {
        return min(100, max(0, $heroLevel - 1) * 2);
    }

    public static function heroLevel(): int
    {
        global $session;
        return (int)($session->heroD['level'] ?? 0);
    }

    private function describe(string $scope, string $key, array $definition): array
    {
        $kid = $scope === 'general' ? 0 : $this->kid;
        $done = $this->collectedSteps($kid, $key);
        $steps = $definition['steps'];
        $finished = $done >= count($steps);
        $step = $steps[min($done, count($steps) - 1)];
        $bonus = 1 + self::bonusPercent(self::heroLevel()) / 100;
        $task = ['scope' => $scope, 'key' => $key, 'type' => $definition['type'], 'image' => $definition['image'],
            'title' => $definition['title'], 'step' => $done, 'steps' => count($steps)];
        if ($definition['type'] === 'fields') {
            [$count, $level, $resources, $experience] = $step;
            $fields = $this->levels($definition['gid'], 1, 18);
            $goal = $count ?: count($fields);
            $value = count(array_filter($fields, function ($l) use ($level) { return $l >= $level; }));
            $task += ['value' => min($value, $goal), 'goal' => $goal, 'level' => $level];
        } elseif ($definition['type'] === 'level') {
            [$goal, $resources, $experience] = $step;
            $value = max($this->levels($definition['gid'], 19, 40) ?: [0]);
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

    /** Levels of the slots $from..$to holding building $gid in the active village. */
    private function levels(int $gid, int $from, int $to): array
    {
        $out = [];
        $f = $this->village->resarray;
        for ($i = $from; $i <= $to; $i++) {
            if ((int)($f['f' . $i . 't'] ?? 0) === $gid) $out[] = (int)($f['f' . $i] ?? 0);
        }
        return $out;
    }

    private function countValue(string $key): int
    {
        if ($key === 'population') return (int)$this->village->pop;
        if ($key === 'culturePoints') return (int)$this->village->cp;
        if ($this->account === null) {
            global $database;
            $row = $database->query('SELECT COUNT(*) AS n, COALESCE(SUM(pop), 0) AS pop, COALESCE(SUM(cp), 0) AS cp FROM vdata WHERE owner = ' . $this->uid);
            $row = is_array($row) && isset($row[0]) ? $row[0] : ['n' => 0, 'pop' => 0, 'cp' => 0];
            $this->account = ['villages' => (int)$row['n'], 'populationTotal' => (int)$row['pop'], 'culturePointsTotal' => (int)$row['cp']];
        }
        return (int)($this->account[$key] ?? 0);
    }

    private function collectedSteps(int $kid, string $key): int
    {
        if ($this->collected === null) {
            $this->collected = [];
            if ($db = self::db()) {
                try {
                    $st = $db->prepare('SELECT kid, task, steps FROM ' . self::TABLE . ' WHERE uid = ?');
                    $st->execute([$this->uid]);
                    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) $this->collected[$r['kid'] . ':' . $r['task']] = (int)$r['steps'];
                } catch (Throwable $e) {
                }
            }
        }
        return $this->collected[$kid . ':' . $key] ?? 0;
    }
}
