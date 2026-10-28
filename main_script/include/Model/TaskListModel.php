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
     * Task chains in the order of the Travian Legends list. 'type' picks the picture/label style of the pack's #tasks
     * CSS, 'steps' are [goal, resources per type, experience] (resource field tasks: [fields (0 = all), level,
     * resources, experience]); 'stepTitle' = translation key of a step of a counting task ("Reach %s population").
     */
    private function definitions(string $scope): array
    {
        if ($scope === 'general') {
            return [
                'villages' => ['type' => 'count', 'image' => 'populationProgress', 'title' => T('Tasks', 'villages', 'Villages'), 'stepTitle' => 'stepVillages',
                    'steps' => [[2, 600, 40], [3, 1200, 60], [5, 2500, 100], [10, 5000, 200], [20, 10000, 400]]],
                'populationTotal' => ['type' => 'count', 'image' => 'populationProgress', 'title' => T('Tasks', 'populationTotal', 'Population of all villages'), 'stepTitle' => 'stepPopulationTotal',
                    'steps' => [[100, 375, 25], [250, 750, 40], [500, 1500, 60], [1000, 2500, 100], [2500, 5000, 150], [5000, 10000, 250]]],
                'culturePointsTotal' => ['type' => 'count', 'image' => 'culturePointsProductionProgress', 'title' => T('Tasks', 'culturePointsTotal', 'Culture points production of all villages'), 'stepTitle' => 'stepCultureTotal',
                    'steps' => [[100, 375, 25], [250, 750, 40], [500, 1500, 60], [1000, 2500, 100], [2500, 5000, 150]]],
            ];
        }
        // resource fields: like Travian Legends one task for a single field ("Iron mines") and one for all of them
        $resource = static fn(int $gid) => ['type' => 'fields', 'gid' => $gid, 'image' => 'building g' . $gid,
            'title' => T('Tasks', 'res' . $gid, T('Buildings', $gid . '.title')),
            'steps' => [[1, 2, 120, 8], [1, 4, 240, 16], [1, 7, 400, 26], [1, 10, 600, 39]]];
        $resourceAll = static fn(int $gid) => ['type' => 'fields', 'gid' => $gid, 'image' => 'building g' . $gid, 'all' => true,
            'title' => T('Tasks', 'resAll' . $gid, T('Buildings', $gid . '.title')),
            'steps' => [[0, 2, 240, 16], [0, 3, 385, 25], [0, 5, 590, 39], [0, 8, 900, 60], [0, 10, 1300, 85]]];
        // building levels: rewards like Travian Legends (level 1 = 300 of each resource / 20 XP … level 12 = 600 / 39)
        $building = static fn(int $gid, array $levels, int $base = 300) => ['type' => 'level', 'gid' => $gid, 'image' => 'building g' . $gid,
            'title' => T('Buildings', $gid . '.title'),
            'steps' => array_map(static function ($level) use ($base) {
                $resources = (int)round($base * (1 + max(0, $level - 1) * 0.09) / 5) * 5;
                return [$level, $resources, (int)round($resources / 15.2)];
            }, $levels)];
        $wall = self::WALLS[$this->session->getRace()] ?? 31;

        return [
            'population' => ['type' => 'count', 'image' => 'populationProgressInVillage', 'title' => T('Tasks', 'population', 'Population'), 'stepTitle' => 'stepPopulation',
                'steps' => [[50, 350, 23], [100, 710, 47], [150, 1105, 73], [250, 1500, 100], [400, 2200, 145], [600, 3000, 200], [800, 3800, 250], [1000, 4500, 300]]],
            'culturePoints' => ['type' => 'count', 'image' => 'culturePointsProductionProgressInVillage', 'title' => T('Tasks', 'culturePoints', 'Culture points production'), 'stepTitle' => 'stepCulture',
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
            'smithy' => $building(13, [1, 5, 10, 20]),
            'stable' => $building(20, [1, 5, 10, 20]),
            'residence' => $building(25, [1, 10, 20], 400),
        ];
    }

    /** All tasks of a scope with their current step, progress and state (open / achieved / collected). */
    public function getTasks(string $scope): array
    {
        $definitions = $this->definitions($scope);
        $tasks = [];
        foreach ($definitions as $key => $definition) {
            $tasks[] = $this->describe($scope, $key, $definition);
        }
        // tasks to collect first, then the list order of Legends, the finished chains at the end
        $order = ['achieved' => 0, 'open' => 1, 'collected' => 2];
        $position = array_flip(array_keys($definitions));
        usort($tasks, static fn($a, $b) => [$order[$a['state']], $position[$a['key']]] <=> [$order[$b['state']], $position[$b['key']]]);
        return $tasks;
    }

    /**
     * One task with its description and all its steps (Legends task details), from the highest level down: the
     * steps still ahead ("future", with their own progress), the current one and the collected ones; null for an
     * unknown task. 'currentIndex' = position of the current step in 'history'.
     */
    public function details(string $scope, string $key): ?array
    {
        $definitions = $this->definitions($scope);
        if (!isset($definitions[$key])) {
            return null;
        }
        $definition = $definitions[$key];
        $task = $this->describe($scope, $key, $definition);
        $bonus = 1 + $this->getBonusPercent($this->getHeroLevel()) / 100;
        $steps = [];
        $current = 0;
        for ($i = count($definition['steps']) - 1; $i >= 0; --$i) {
            $step = $definition['steps'][$i];
            [$resources, $experience] = $definition['type'] === 'fields' ? [$step[2], $step[3]] : [$step[1], $step[2]];
            $entry = ['index' => $i, 'title' => $this->stepTitle($definition, $step), 'type' => $definition['type'],
                'rewardResources' => (int)round($resources * $bonus), 'rewardExperience' => (int)round($experience * $bonus)];
            if ($i < $task['step']) {
                $entry['state'] = 'collected';
            } else {
                if ($i === $task['step']) {
                    $current = count($steps);
                }
                $entry += $this->progress($key, $definition, $step);
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
            $fields = $this->village->getTypeLevel($definition['gid']);
            $goal = $count ?: count($fields);
            $value = count(array_filter($fields, static fn($l) => $l >= $level));
            $out = ['value' => min($value, $goal), 'goal' => $goal, 'level' => $level];
        } elseif ($definition['type'] === 'level') {
            $goal = $step[0];
            $out = ['value' => min(max($this->village->getTypeLevel($definition['gid']) ?: [0]), $goal), 'goal' => $goal, 'level' => 0];
        } else {
            $goal = $step[0];
            $out = ['value' => min($this->countValue($key), $goal), 'goal' => $goal, 'level' => 0];
        }
        $out['ratio'] = $out['goal'] > 0 ? $out['value'] / $out['goal'] : 0;
        return $out;
    }

    /** Title of one step in the task details ("One building "Iron mine" to level 4", "Reach 250 population"). */
    private function stepTitle(array $definition, array $step): string
    {
        if ($definition['type'] === 'fields') {
            $gid = $definition['gid'];
            return $step[0]
                ? sprintf(T('Tasks', 'stepOne', 'One building "%s" to level %d'), T('Buildings', $gid . '.title'), $step[1])
                : sprintf(T('Tasks', 'stepAll', 'All buildings "%s" to level %d'), T('Tasks', 'res' . $gid, T('Buildings', $gid . '.title')), $step[1]);
        }
        if ($definition['type'] === 'level') {
            return sprintf(T('Tasks', 'stepLevel', 'Building "%s" to level %d'), T('Buildings', $definition['gid'] . '.title'), $step[0]);
        }
        $number = (int)$step[0] < 10000 ? (string)(int)$step[0] : number_format((int)$step[0], 0, ',', ' ');
        return sprintf(T('Tasks', $definition['stepTitle'], $definition['title'] . ': %s'), $number);
    }

    /** Text of the task details. */
    private function description(string $key, array $definition): string
    {
        $byKey = ['population' => 'descPopulation', 'culturePoints' => 'descCulture', 'villages' => 'descVillages',
            'populationTotal' => 'descPopulationTotal', 'culturePointsTotal' => 'descCultureTotal', 'wall' => 'descWall'];
        if (isset($byKey[$key])) {
            return T('Tasks', $byKey[$key], '');
        }
        if ($definition['type'] === 'fields') {
            return !empty($definition['all']) ? T('Tasks', 'descResAll', '') : T('Tasks', 'descRes' . $definition['gid'], '');
        }
        return T('Tasks', 'descG' . (int)$definition['gid'], T('Tasks', 'descBuilding', ''));
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
        if (\Game\Hero\HeroItems::resourceItemsEnabled()) {
            // Legends: task rewards land in the hero's bag as resource items (used from the inventory).
            \Game\Hero\HeroItems::giveResources((int)$uid, [$resources, $resources, $resources, $resources]);
        } else {
            $this->village->modifyResources([$resources, $resources, $resources, $resources], 1);
        }
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
