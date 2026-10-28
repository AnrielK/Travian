<?php
/**
 * Task list of the Travian Legends look ("Přehled úkolů", tasks.php) – port of Model\TaskListModel of the main branch.
 *
 * Every task is a chain of steps ("reach 50 population", "100", …). The current step is the first one not collected
 * yet; once its goal is reached the player collects resources (each of the four types, as resource items into the
 * hero's inventory – used from there into a village, like Travian Legends) and
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
                    'stepTitle' => 'TASKS_STEP_VILLAGES',
                'steps' => [[2, 600, 40], [3, 1200, 60], [5, 2500, 100], [10, 5000, 200], [20, 10000, 400]]],
                'populationTotal' => ['type' => 'count', 'image' => 'populationProgress', 'title' => self::t('TASKS_POPULATION_TOTAL', 'Population of all villages'),
                    'stepTitle' => 'TASKS_STEP_POPULATION_TOTAL',
                'steps' => [[100, 375, 25], [250, 750, 40], [500, 1500, 60], [1000, 2500, 100], [2500, 5000, 150], [5000, 10000, 250]]],
                'culturePointsTotal' => ['type' => 'count', 'image' => 'culturePointsProductionProgress', 'title' => self::t('TASKS_CULTURE_TOTAL', 'Culture points production of all villages'),
                    'stepTitle' => 'TASKS_STEP_CULTURE_TOTAL',
                'steps' => [[100, 375, 25], [250, 750, 40], [500, 1500, 60], [1000, 2500, 100], [2500, 5000, 150]]],
            ];
        }
        // resource fields: like Travian Legends one task for a single field ("Železné doly") and one for all of them
        // ("Všechny železné doly"); steps [fields (0 = all), level, resources, experience]
        $resource = function (int $gid) {
            return ['type' => 'fields', 'gid' => $gid, 'image' => 'building g' . $gid, 'title' => self::t('TASKS_RES_' . $gid, self::building($gid)),
                'steps' => [[1, 2, 120, 8], [1, 4, 240, 16], [1, 7, 400, 26], [1, 10, 600, 39]]];
        };
        $resourceAll = function (int $gid) {
            return ['type' => 'fields', 'gid' => $gid, 'image' => 'building g' . $gid, 'title' => self::t('TASKS_RES_ALL_' . $gid, self::building($gid)),
                'steps' => [[0, 2, 240, 16], [0, 3, 385, 25], [0, 5, 590, 39], [0, 8, 900, 60], [0, 10, 1300, 85]]];
        };
        // building levels: rewards like Travian Legends (level 1 = 300 of each resource / 20 XP … level 12 = 600 / 39)
        $building = function (int $gid, array $levels, int $base = 300) {
            return ['type' => 'level', 'gid' => $gid, 'image' => 'building g' . $gid, 'title' => self::building($gid),
                'steps' => array_map(function ($level) use ($base) {
                    $res = (int)round($base * (1 + max(0, $level - 1) * 0.09) / 5) * 5;
                    return [$level, $res, (int)round($res / 15.2)];
                }, $levels)];
        };
        $wall = self::WALLS[$this->tribe] ?? 31;
        return [
            // the order of the Travian Legends list
            'population' => ['type' => 'count', 'image' => 'populationProgressInVillage', 'title' => self::t('TASKS_POPULATION', 'Population'),
                'stepTitle' => 'TASKS_STEP_POPULATION',
                'steps' => [[50, 350, 23], [100, 710, 47], [150, 1105, 73], [250, 1500, 100], [400, 2200, 145], [600, 3000, 200], [800, 3800, 250], [1000, 4500, 300]]],
            'culturePoints' => ['type' => 'count', 'image' => 'culturePointsProductionProgressInVillage', 'title' => self::t('TASKS_CULTURE', 'Culture points production'),
                'stepTitle' => 'TASKS_STEP_CULTURE',
                'steps' => [[50, 375, 25], [100, 750, 50], [150, 1125, 75], [250, 1500, 100], [400, 2250, 150], [600, 3000, 200]]],
            'embassy' => $building(18, [1, 5, 10, 20]),
            'woodcutter' => $resource(1),
            'mainBuilding' => $building(15, [3, 5, 7, 10, 12, 15, 20]),
            'claypit' => $resource(2),
            'cropland' => $resource(4),
            'ironmine' => $resource(3),
            'woodcutterAll' => $resourceAll(1),
            'claypitAll' => $resourceAll(2),
            'ironmineAll' => $resourceAll(3),
            'croplandAll' => $resourceAll(4),
            'wall' => $building($wall, [1, 5, 10, 15, 20]),
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
        // tasks to collect first, then the list order of Legends, the finished ones last
        $order = ['achieved' => 0, 'open' => 1, 'collected' => 2];
        $position = array_flip(array_keys($this->definitions($scope)));
        usort($tasks, function ($a, $b) use ($order, $position) {
            return [$order[$a['state']], $position[$a['key']]] <=> [$order[$b['state']], $position[$b['key']]];
        });
        return $tasks;
    }

    /**
     * One task with its description and all its steps (Legends task details), from the highest level down: the
     * steps still ahead ("future", with their own progress), the current one and the collected ones; null for an
     * unknown task. 'currentIndex' = position of the current step in that list.
     */
    public function details(string $scope, string $key): ?array
    {
        $definitions = $this->definitions($scope);
        if (!isset($definitions[$key])) return null;
        $definition = $definitions[$key];
        $task = $this->describe($scope, $key, $definition);
        $bonus = 1 + self::bonusPercent(self::heroLevel()) / 100;
        $steps = [];
        $current = 0;
        for ($i = count($definition['steps']) - 1; $i >= 0; $i--) {
            $step = $definition['steps'][$i];
            [$resources, $experience] = $definition['type'] === 'fields' ? [$step[2], $step[3]] : [$step[1], $step[2]];
            $entry = ['index' => $i, 'title' => $this->stepTitle($key, $definition, $step),
                'rewardResources' => (int)round($resources * $bonus), 'rewardExperience' => (int)round($experience * $bonus)];
            if ($i < $task['step']) {
                $entry['state'] = 'collected';
            } else {
                if ($i === $task['step']) $current = count($steps);
                $entry += $this->progress($key, $definition, $step) + ['type' => $definition['type']];
                $entry['state'] = $i === $task['step'] ? $task['state'] : 'future';
            }
            $steps[] = $entry;
        }
        $task['description'] = $this->description($key, $definition);
        $task['history'] = $steps;
        $task['currentIndex'] = $task['state'] === 'collected' ? 0 : $current;
        return $task;
    }

    /** Progress of a task towards one of its steps: value, goal, ratio (and the level of resource field steps). */
    private function progress(string $key, array $definition, array $step): array
    {
        if ($definition['type'] === 'fields') {
            [$count, $level] = $step;
            $fields = $this->levels($definition['gid'], 1, 18);
            $goal = $count ?: count($fields);
            $value = count(array_filter($fields, function ($l) use ($level) { return $l >= $level; }));
            $out = ['value' => min($value, $goal), 'goal' => $goal, 'level' => $level];
        } elseif ($definition['type'] === 'level') {
            $goal = $step[0];
            $out = ['value' => min(max($this->levels($definition['gid'], 19, 40) ?: [0]), $goal), 'goal' => $goal, 'level' => 0];
        } else {
            $goal = $step[0];
            $out = ['value' => min($this->countValue($key), $goal), 'goal' => $goal, 'level' => 0];
        }
        $out['ratio'] = $out['goal'] > 0 ? $out['value'] / $out['goal'] : 0;
        return $out;
    }

    private function stepTitle(string $key, array $definition, array $step): string
    {
        if ($definition['type'] === 'fields') {
            return $step[0]
                ? sprintf(self::t('TASKS_STEP_ONE', 'One building "%s" to level %d'), self::building($definition['gid']), $step[1])
                : sprintf(self::t('TASKS_STEP_ALL', 'All buildings "%s" to level %d'), self::t('TASKS_RES_' . $definition['gid'], self::building($definition['gid'])), $step[1]);
        }
        if ($definition['type'] === 'level') {
            return sprintf(self::t('TASKS_STEP_LEVEL', 'Building "%s" to level %d'), self::building($definition['gid']), $step[0]);
        }
        $n = (int)$step[0] < 10000 ? (string)(int)$step[0] : number_format((int)$step[0], 0, ',', ' ');
        if (!empty($definition['stepTitle']) && defined($definition['stepTitle'])) return sprintf(constant($definition['stepTitle']), $n);
        return sprintf(self::t('TASKS_STEP_COUNT', '%s: %s'), $definition['title'], $n);
    }

    private function description(string $key, array $definition): string
    {
        $byKey = ['population' => 'TASKS_DESC_POPULATION', 'culturePoints' => 'TASKS_DESC_CULTURE', 'villages' => 'TASKS_DESC_VILLAGES',
            'populationTotal' => 'TASKS_DESC_POPULATION_TOTAL', 'culturePointsTotal' => 'TASKS_DESC_CULTURE_TOTAL', 'wall' => 'TASKS_DESC_WALL'];
        if (isset($byKey[$key])) return self::t($byKey[$key], '');
        if ($definition['type'] === 'fields') {
            return substr($key, -3) === 'All' ? self::t('TASKS_DESC_RES_ALL', '') : self::t('TASKS_DESC_RES_' . $definition['gid'], '');
        }
        $gid = (int)($definition['gid'] ?? 0);
        return self::t('TASKS_DESC_G' . $gid, self::t('TASKS_DESC_BUILDING', ''));
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
        self::addResourceItems($this->uid, $res);
        $exp = (int)$task['rewardExperience'];
        if ($exp > 0) $database->query('UPDATE hero SET experience = experience + ' . $exp . ' WHERE uid = ' . $this->uid);
        $this->collected = null;
        return ['resources' => $res, 'experience' => $exp];
    }

    /** Hero items holding resources (Legends: rewards go to the hero's inventory): btype 16, type = item picture. */
    const RESOURCE_ITEM_BTYPE = HeroBag::RESOURCE_BTYPE;
    const RESOURCE_ITEMS = [145 => 'wood', 146 => 'clay', 147 => 'iron', 148 => 'crop'];

    /** Adds $amount of each resource to the hero's inventory (one stack per resource). */
    public static function addResourceItems(int $uid, int $amount): void
    {
        global $database;
        if ($amount <= 0) return;
        foreach (array_keys(self::RESOURCE_ITEMS) as $type) {
            $row = $database->query('SELECT id FROM heroitems WHERE uid = ' . $uid . ' AND btype = ' . self::RESOURCE_ITEM_BTYPE . ' AND type = ' . $type . ' AND proc = 0 LIMIT 1');
            if (is_array($row) && isset($row[0]['id'])) {
                $database->editHeroNum((int)$row[0]['id'], $amount, 1);
            } else {
                $database->addHeroItem($uid, self::RESOURCE_ITEM_BTYPE, $type, $amount, 0);
            }
        }
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
