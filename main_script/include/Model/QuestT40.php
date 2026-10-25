<?php

namespace Model;

use Core\Config;
use Core\Database\DB;
use Core\Session;
use Core\Village;
use Game\Buildings\BuildingAction;
use Game\Formulas;
use Game\GoldHelper;

/**
 * The task line of Travian 4.0 (graphic pack T40, layout/LayoutT40.php): a welcome, 28 tasks in a fixed order (task 21
 * lets the player choose the economy or the military branch) and 11 optional tasks, each with its own 4.0 picture.
 * It replaces the T4.4 tutorial and task list in worlds with the T40 look.
 *
 * Progress is computed from the live game data. Only the current task, the collected optional tasks and one value
 * per task (the neighbour village to look for, the taskmaster message, "answered") are stored, in table ov_quest40,
 * created on first use so existing worlds need no migration.
 *
 * state: 0 welcome, 1-28 current task, 29 optional tasks, -1 the player chose to play without tasks.
 */
class QuestT40
{
    public const TABLE = 'ov_quest40';
    public const WELCOME = 0;
    public const OPTIONAL = 29;
    public const DECLINED = -1;
    /** Tasks the player answers with a value (rank, coordinates, lumber, gold): data -1 = answered. */
    private const ANSWER_TASKS = [6, 10, 18, 20, 28];
    /** Tribe wall building per race. */
    private const WALLS = [1 => 31, 2 => 32, 3 => 33, 6 => 42, 7 => 43];
    /** 4.0 picture of every task (img/q/<name>.jpg of the 4.0 pack). */
    private const IMAGES = [
        1 => 'wood', 2 => 'farm', 3 => 'village_name', 4 => 'rally', 5 => 'first_adventure', 6 => 'rank1',
        7 => 'clay_iron', 8 => 'msg', 9 => 'res_one_of_each', 10 => 'neighbour', 11 => 'heroproduction',
        12 => 'adventure_report', 13 => 'second_adventure', 14 => 'res_all_one', 15 => 'medal', 16 => 'cranny',
        17 => 'res_two_of_each', 18 => 'cost', 19 => 'main', 20 => 'rank2', 21 => 'granary_barracks',
        22 => 'granary_barracks1', 23 => 'warehouse', 24 => 'market', 25 => 'granary_barracks2', 26 => 'units',
        27 => 'wall', 28 => 'freunde', 30 => 'quest_res_all_two', 31 => 'quest_dispatch', 32 => 'quest_alliance',
        33 => 'quest_finished_ten_adventures', 34 => 'quest_main_on_five', 35 => 'quest_granary_on_three',
        36 => 'quest_warehouse_on_seven', 37 => 'quest_res_all_five', 38 => 'quest_palace_or_residence',
        39 => 'quest_three_settlers', 40 => 'quest_new_village',
    ];
    /**
     * Reward for completing a task (as in the 4.0 task line): resources [wood, clay, iron, crop], 'plus' days, 'gold',
     * 'silver', 'finish' (the task's building orders are completed at once) or 'hero' (task 11, by the hero's production).
     */
    private const REWARDS = [
        1 => ['finish' => 1], 2 => ['plus' => 1], 3 => [30, 60, 30, 20], 4 => ['finish' => 16], 5 => [],
        6 => [85, 95, 55, 25], 7 => [150, 160, 130, 130], 8 => ['gold' => 20], 9 => [110, 130, 60, 45],
        10 => [60, 40, 40, 90], 11 => ['hero' => true], 12 => [75, 120, 30, 50], 13 => ['plus' => 2],
        14 => [120, 150, 80, 70], 15 => [80, 70, 60, 30], 16 => [50, 60, 55, 35], 17 => [80, 95, 80, 50],
        18 => [200, 250, 160, 140], 19 => [120, 135, 70, 45], 20 => [55, 70, 50, 30], 22 => [130, 160, 130, 100],
        23 => [120, 150, 100, 60], 24 => [75, 75, 75, 20], 25 => [175, 175, 175, 175], 26 => [240, 200, 130, 90],
        27 => [75, 75, 75, 20], 28 => [50, 50, 50, 50],
        30 => ['gold' => 20], 31 => [130, 160, 130, 100], 32 => [350, 260, 230, 190], 33 => ['silver' => 100],
        34 => [235, 275, 200, 300], 35 => [380, 460, 350, 400], 36 => [1550, 1860, 1500, 900],
        37 => [1300, 1700, 1300, 700], 38 => [4350, 6800, 5500, 4800], 39 => [750, 750, 750, 750],
        40 => [1000, 1000, 1000, 1000],
    ];

    private static bool $tableReady = false;

    private DB $db;
    private Session $session;
    private Village $village;
    private ?array $row = null;

    public function __construct()
    {
        $this->db = DB::getInstance();
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
            `state` SMALLINT NOT NULL DEFAULT 0,
            `optional` INT UNSIGNED NOT NULL DEFAULT 0,
            `data` INT NOT NULL DEFAULT 0,
            PRIMARY KEY (`uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        self::$tableReady = true;
    }

    private function row(): array
    {
        if ($this->row === null) {
            $uid = (int)$this->session->getPlayerId();
            $row = $this->db->query('SELECT state, optional, data FROM `' . self::TABLE . '` WHERE uid=' . $uid)->fetch_assoc();
            if (!$row) {
                $this->db->query('INSERT IGNORE INTO `' . self::TABLE . '` (uid) VALUES (' . $uid . ')');
                $row = ['state' => 0, 'optional' => 0, 'data' => 0];
            }
            $this->row = array_map('intval', $row);
        }
        return $this->row;
    }

    private function save(array $values): void
    {
        $this->row = array_merge($this->row(), $values);
        $set = [];
        foreach ($values as $key => $value) {
            $set[] = '`' . $key . '`=' . (int)$value;
        }
        $this->db->query('UPDATE `' . self::TABLE . '` SET ' . implode(',', $set) . ' WHERE uid=' . (int)$this->session->getPlayerId());
    }

    public function getState(): int
    {
        return $this->row()['state'];
    }

    public function isDeclined(): bool
    {
        return $this->getState() === self::DECLINED;
    }

    public function isOptionalCollected(int $task): bool
    {
        return ($this->row()['optional'] & (1 << ($task - 30))) !== 0;
    }

    /** The quest master leans on his sign when a reward waits (4.0: he crosses his arms while a task is open). */
    public function hasReward(): bool
    {
        $state = $this->getState();
        if ($state >= 1 && $state <= 28 && $state !== 21) {
            return $this->isComplete($state);
        }
        if ($state === self::OPTIONAL) {
            for ($task = 30; $task <= 40; ++$task) {
                if (!$this->isOptionalCollected($task) && $this->isComplete($task)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function image(int $task): string
    {
        if ($task === 27) {
            return [1 => 'wall', 2 => 'earthwall', 3 => 'pallisade'][Session::getInstance()->getRace()] ?? 'wall';
        }
        return self::IMAGES[$task] ?? 'intro';
    }

    /** The task after $task in the line (task 21 is the choice, see choose()). */
    private static function next(int $task): int
    {
        if ($task === 24 || $task === 27) {
            return 28;
        }
        return $task + 1;
    }

    /* ---------------------------------------------------------------- game data */

    /** Levels of the resource fields of the active village per type (1 wood, 2 clay, 3 iron, 4 crop). */
    private function fieldLevels(): array
    {
        $levels = [1 => [], 2 => [], 3 => [], 4 => []];
        for ($i = 1; $i <= 18; ++$i) {
            $field = $this->village->getField($i);
            if ($field && isset($levels[$field['item_id']])) {
                $levels[$field['item_id']][] = (int)$field['level'];
            }
        }
        return $levels;
    }

    /** At least $count fields of every type at $level or higher; $count 0 = all of them. */
    private function fieldsAtLeast(int $level, int $count): bool
    {
        foreach ($this->fieldLevels() as $levels) {
            $reached = count(array_filter($levels, static fn($l) => $l >= $level));
            if ($reached < ($count ?: count($levels))) {
                return false;
            }
        }
        return true;
    }

    /** Fields of the active village with building orders, by building type. */
    private function queued(int $gid): array
    {
        $fields = [];
        foreach ($this->village->onLoadBuildings['normal'] ?? [] as $order) {
            if ((int)$this->village->getField($order['building_field'])['item_id'] === $gid) {
                $fields[] = $order;
            }
        }
        return $fields;
    }

    private function level(int $gid): int
    {
        return (int)max($this->village->getTypeLevel($gid) ?: [0]);
    }

    private function builtOrQueued(int $gid): bool
    {
        return $this->level($gid) > 0 || count($this->queued($gid)) > 0;
    }

    private function adventureRunning(): bool
    {
        return (bool)$this->db->fetchScalar('SELECT COUNT(*) FROM movement m JOIN vdata v ON v.kid=m.kid WHERE v.owner='
            . (int)$this->session->getPlayerId() . ' AND m.attack_type=' . MovementsModel::ATTACKTYPE_ADVENTURE);
    }

    /** What the hero produces: 0 all resources, 1-4 one resource. */
    public function heroProductionType(): int
    {
        return (int)$this->db->fetchScalar('SELECT productionType FROM hero WHERE uid=' . (int)$this->session->getPlayerId());
    }

    public function adventuresDone(): int
    {
        return (int)$this->db->fetchScalar('SELECT success_adventures_count FROM users WHERE id=' . (int)$this->session->getPlayerId());
    }

    private function unitCount(int $nr): int
    {
        return (int)$this->db->fetchScalar('SELECT SUM(u.u' . $nr . ') FROM units u JOIN vdata v ON v.kid=u.kid WHERE v.owner='
            . (int)$this->session->getPlayerId());
    }

    private function villageCount(): int
    {
        return (int)$this->db->fetchScalar('SELECT COUNT(*) FROM vdata WHERE owner=' . (int)$this->session->getPlayerId());
    }

    /** A village of the player carries a name of its own instead of the one it got at registration. */
    private function villageRenamed(): bool
    {
        $player = $this->session->getName();
        $defaults = [sprintf(T('Global', 'playerVillageName'), $player), sprintf("%s's village", $player),
            T('Global', 'newVillageName'), 'New village'];
        $normalize = static fn($name) => mb_strtolower(str_replace(["'", '`'], '', html_entity_decode((string)$name, ENT_QUOTES)));
        $defaults = array_map($normalize, $defaults);
        $result = $this->db->query('SELECT name FROM vdata WHERE owner=' . (int)$this->session->getPlayerId());
        while ($row = $result->fetch_assoc()) {
            if (!in_array($normalize($row['name']), $defaults, true)) {
                return true;
            }
        }
        return false;
    }

    public function rank(): int
    {
        return (int)(new StatisticsModel())->getPlayerRankById($this->session->getPlayerId(), true);
    }

    public static function barracksLumber(): int
    {
        return (int)Formulas::buildingUpgradeCosts(19, 1)[0];
    }

    public static function inviteGold(): int
    {
        return (int)Config::getProperty('gold', 'invitePlayerGold');
    }

    /** The village task 10 asks for: the nearest village of another player (or of the Natars), else the own one. */
    public function neighbour(): array
    {
        // data holds the village (negated once the coordinates were found, see answer())
        $kid = abs($this->row()['data']);
        if ($kid === 0) {
            $own = $this->village->getKid();
            $best = null;
            $result = $this->db->query('SELECT kid, owner FROM vdata WHERE owner<>' . (int)$this->session->getPlayerId() . ' AND owner>0 AND owner<>2');
            while ($row = $result->fetch_assoc()) {
                $distance = Formulas::getDistance($own, (int)$row['kid']) + ((int)$row['owner'] === 1 ? 10000 : 0);
                if ($best === null || $distance < $best[0]) {
                    $best = [$distance, (int)$row['kid']];
                }
            }
            $kid = $best ? $best[1] : $own;
            $this->save(['data' => $kid]);
        }
        $name = $this->db->fetchScalar('SELECT name FROM vdata WHERE kid=' . (int)$kid);
        return ['kid' => $kid, 'name' => (string)$name] + Formulas::kid2xy($kid);
    }

    /* ---------------------------------------------------------------- tasks */

    public function isComplete(int $task): bool
    {
        if (in_array($task, self::ANSWER_TASKS, true)) {
            return $this->getState() === $task && $this->row()['data'] < 0;
        }
        switch ($task) {
            case 1:
                return $this->builtOrQueued(1);
            case 2:
                return $this->builtOrQueued(4);
            case 3:
                return $this->villageRenamed();
            case 4:
                return $this->builtOrQueued(16);
            case 5:
                return $this->adventureRunning() || $this->adventuresDone() > 0;
            case 7:
                return $this->level(3) > 0 && $this->level(2) > 0;
            case 8:
                $id = $this->row()['data'];
                return $id > 0 && (int)$this->db->fetchScalar('SELECT viewed FROM mdata WHERE id=' . $id) === 1;
            case 9:
                return $this->fieldsAtLeast(1, 2);
            case 11:
                return $this->heroProductionType() > 0;
            case 12:
                return (bool)$this->db->fetchScalar('SELECT COUNT(*) FROM ndata WHERE uid=' . (int)$this->session->getPlayerId()
                    . ' AND type=21 AND viewed=1');
            case 13:
                return $this->adventuresDone() >= 2 || ($this->adventuresDone() >= 1 && $this->adventureRunning());
            case 14:
                return $this->fieldsAtLeast(1, 0);
            case 15:
                $row = $this->db->query('SELECT desc1, desc2 FROM users WHERE id=' . (int)$this->session->getPlayerId())->fetch_assoc();
                return strpos($row['desc1'] . $row['desc2'], '[#0]') !== false;
            case 16:
                return $this->level(23) > 0;
            case 17:
            case 30:
                return $this->fieldsAtLeast(2, $task === 17 ? 1 : 0);
            case 19:
                return $this->level(15) >= 3;
            case 22:
                return $this->level(11) > 0;
            case 23:
                return $this->level(10) > 0;
            case 24:
                return $this->level(17) > 0;
            case 25:
                return $this->level(19) > 0;
            case 26:
                return $this->unitCount(1) >= 2;
            case 27:
                return $this->level(self::WALLS[$this->session->getRace()] ?? 31) > 0;
            case 31:
                return $this->level(18) > 0;
            case 32:
                return $this->session->getAllianceId() > 0;
            case 33:
                return $this->adventuresDone() >= 10;
            case 34:
                return $this->level(15) >= 5;
            case 35:
                return $this->level(11) >= 3;
            case 36:
                return $this->level(10) >= 7;
            case 37:
                return $this->fieldsAtLeast(5, 0);
            case 38:
                return $this->level(25) >= 10 || $this->level(26) >= 10;
            case 39:
                return $this->unitCount(10) >= 3 || $this->villageCount() >= 2;
            case 40:
                return $this->villageCount() >= 2;
        }
        return false;
    }

    /** Checks the answer of an answer task; true when it is right (the task is then complete). */
    public function answer(int $task, array $input): bool
    {
        if ($this->getState() !== $task || !in_array($task, self::ANSWER_TASKS, true)) {
            return false;
        }
        $value = static fn($key) => (int)preg_replace('/[^\d-]/', '', (string)($input[$key] ?? ''));
        switch ($task) {
            case 6:
            case 20:
                $right = $value('value') === $this->rank();
                break;
            case 10:
                $neighbour = $this->neighbour();
                $right = $value('x') === (int)$neighbour['x'] && $value('y') === (int)$neighbour['y'];
                break;
            case 18:
                $right = $value('value') === self::barracksLumber();
                break;
            default:
                $right = $value('value') === self::inviteGold();
        }
        if ($right) {
            $this->save(['data' => $task === 10 ? -$this->neighbour()['kid'] : -1]);
        }
        return $right;
    }

    /** Starts the task line (welcome: "to the first task") or ends it ("play without tasks"). */
    public function start(bool $withTasks): void
    {
        if ($this->getState() === self::WELCOME) {
            $this->save(['state' => $withTasks ? 1 : self::DECLINED, 'data' => 0]);
        }
    }

    /** Task 21: economy (granary, warehouse, marketplace) or military (barracks, troops, wall). */
    public function choose(bool $economy): void
    {
        if ($this->getState() === 21) {
            $this->save(['state' => $economy ? 22 : 25, 'data' => 0]);
        }
    }

    /** Collects the reward of the current task and moves on. */
    public function collect(): bool
    {
        $task = $this->getState();
        if ($task < 1 || $task > 28 || $task === 21 || !$this->isComplete($task)) {
            return false;
        }
        $this->reward($task);
        $next = self::next($task);
        $this->save(['state' => $next, 'data' => 0]);
        if ($next === 8) {
            $message = new MessageModel();
            $this->save(['data' => (int)$message->sendMessage(0, $this->session->getPlayerId(),
                T('QuestT40', 'messageSubject'), T('QuestT40', 'messageText'))]);
        }
        return true;
    }

    /** Collects the reward of an optional task (30-40). */
    public function collectOptional(int $task): bool
    {
        if ($this->getState() !== self::OPTIONAL || $task < 30 || $task > 40 || $this->isOptionalCollected($task)
            || !$this->isComplete($task)) {
            return false;
        }
        $this->save(['optional' => $this->row()['optional'] | (1 << ($task - 30))]);
        $this->reward($task);
        return true;
    }

    /** The reward of a task as shown in the dialog: [type => amount] with resources as 'res' => [4 amounts]. */
    public function rewardOf(int $task): array
    {
        $reward = self::REWARDS[$task] ?? [];
        if (isset($reward['hero'])) {
            $type = $this->heroProductionType();
            $res = $type >= 1 && $type <= 4 ? array_replace([0, 0, 0, 0], [$type - 1 => 400]) : [120, 120, 120, 120];
            return ['res' => Quest::getInstance()->multiply($res)];
        }
        if (isset($reward[0])) {
            return ['res' => Quest::getInstance()->multiply($reward)];
        }
        return $reward;
    }

    private function reward(int $task): void
    {
        $reward = $this->rewardOf($task);
        $uid = (int)$this->session->getPlayerId();
        if (isset($reward['res'])) {
            $this->village->modifyResources($reward['res'], 1);
        }
        if (isset($reward['plus'])) {
            (new GoldHelper())->giftPlus($reward['plus'] * 86400);
        }
        if (isset($reward['gold'])) {
            $this->db->query('UPDATE users SET gift_gold=gift_gold+' . (int)$reward['gold'] . ' WHERE id=' . $uid);
        }
        if (isset($reward['silver'])) {
            $this->db->query('UPDATE users SET silver=silver+' . (int)$reward['silver'] . ' WHERE id=' . $uid);
        }
        if (isset($reward['finish'])) {
            // the 4.0 task master "helped a bit": the building orders of the task are completed at once, free of charge
            foreach ($this->queued($reward['finish']) as $order) {
                $this->db->query('DELETE FROM building_upgrade WHERE id=' . (int)$order['id']);
                BuildingAction::upgrade($this->village->getKid(), $order['building_field']);
                unset($this->village->onLoadBuildings['normal'][$order['id']]);
            }
            $this->village->recalculateBuildingTimes();
        }
    }
}
