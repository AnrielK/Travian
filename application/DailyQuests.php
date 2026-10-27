<?php
/**
 * Daily quests of T4.6 (table achiev: counters a1..a10, reward1..4 = "type,collected") for the Travian Legends page
 * dailyquests.php (markup of the main branch, templates dailyQuestT46/legends). The counters are filled by the game
 * as before; points, steps and the four rewards (25/50/75/100 points) are read here the same way as the classic
 * dialog of ajax.php (cmd=dailyquests), and collect() grants a reward like the dialog's "Collect reward" did.
 */
final class DailyQuests
{
    /** quest => [counter column, points per step, steps]; counters a8/a9 count trained units (see points()). */
    const QUESTS = [
        1 => [5, 1], 2 => [3, 3], 3 => [3, 3], 4 => [5, 1], 5 => [2, 3],
        6 => [4, 3], 7 => [5, 3], 8 => [4, 3], 9 => [4, 3], 10 => [5, 3],
    ];

    public static function row(int $uid): array
    {
        global $database;
        $row = $database->getAchiev($uid);
        if (!$row) {
            $database->AddAchiev($uid);
            $row = $database->getAchiev($uid);
        }
        return is_array($row) ? $row : [];
    }

    /** Points of one quest (same as the classic dialog). */
    public static function questPoints(array $row, int $id): int
    {
        $a = (int)($row['a' . $id] ?? 0);
        if ($id === 8 || $id === 9) {
            // 20 units trained at once = one step of 4 points (counter += 5 per 20 units, see the classic dialog)
            return 4 * min(3, intdiv(intdiv($a, 5), 4));
        }
        [$step, $steps] = self::QUESTS[$id];
        return min($step * $steps, $a);
    }

    public static function points(array $row): int
    {
        $sum = 0;
        foreach (self::QUESTS as $id => $_) $sum += self::questPoints($row, $id);
        return $sum;
    }

    /** The ten quests: id, name, points per step, steps, finished steps, points. */
    public static function quests(array $row): array
    {
        global $lang;
        $out = [];
        foreach (self::QUESTS as $id => [$step, $steps]) {
            $points = self::questPoints($row, $id);
            $out[] = [
                'id' => $id,
                'name' => $lang['Daily'][sprintf('%02d', $id)] ?? ('#' . $id),
                'reward' => $step,
                'steps' => $steps,
                'done' => min($steps, intdiv($points, $step)),
                'points' => $points,
                'total' => $step * $steps,
            ];
        }
        return $out;
    }

    /** [type, collected] of reward 1..4. */
    public static function reward(array $row, int $n): array
    {
        $r = explode(',', (string)($row['reward' . $n] ?? '0,0'));
        return [(int)($r[0] ?? 0), (int)($r[1] ?? 0)];
    }

    /** Text of the possible rewards of a milestone (classic dialog: questday4 …) or of the one drawn. */
    public static function rewardText(int $n, int $type = 0): string
    {
        $first = [1 => 5, 2 => 9, 3 => 15, 4 => 19][$n];
        $count = [1 => 3, 2 => 5, 3 => 3, 4 => 3][$n];
        if ($type > 0) {
            $c = 'questday' . ($first + $type - 1);
            return defined($c) ? constant($c) : '';
        }
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $c = 'questday' . ($first + $i);
            if (defined($c)) $items[] = constant($c);
        }
        return implode(' / ', $items);
    }

    /** The four milestones: points, reached, collected, text. */
    public static function milestones(array $row, int $points): array
    {
        $out = [];
        foreach ([1, 2, 3, 4] as $n) {
            [$type, $done] = self::reward($row, $n);
            $out[$n] = [
                'points' => $n * 25,
                'reached' => $points >= $n * 25,
                'done' => (bool)$done,
                'description' => self::rewardText($n, $done ? $type : 0),
            ];
        }
        return $out;
    }

    /**
     * Collects reward $n (1..4) when its points are reached: draws the reward type like the classic dialog
     * (Quest::dailyReward) if none is drawn yet, grants it and marks it collected. Returns the reward text or null.
     */
    public static function collect(int $uid, int $n, int $wid): ?string
    {
        global $database, $session;
        if ($n < 1 || $n > 4) return null;
        $row = self::row($uid);
        if (self::points($row) < $n * 25) return null;
        [$type, $done] = self::reward($row, $n);
        if ($done) return null;
        if (!$type) {
            $type = rand(1, $n === 2 ? 5 : 3);
        }
        // mark first, so a double click cannot collect twice
        $database->query("UPDATE achiev SET reward$n = '" . (int)$type . ",1' WHERE uid = " . (int)$uid . " AND reward$n NOT LIKE '%,1'");
        $check = self::reward(self::row($uid), $n);
        if ($check[0] !== $type || !$check[1]) return null;

        switch ($n * 10 + $type) {
            case 11: $database->addAdventure($session->vid, $uid, 5); break;
            case 12: $database->setCelCp($uid, 5000); break;
            case 13:
                $r = [0, 0, 0, 0];
                $r[rand(0, 3)] = HOWRES;
                $database->modifyResource($wid, $r[0], $r[1], $r[2], $r[3], 1);
                break;
            case 21: case 22: case 23: case 24: case 25:
                $col = [1 => 'plus', 2 => 'b1', 3 => 'b2', 4 => 'b3', 5 => 'b4'][$type];
                $until = (int)$database->single("SELECT `$col` FROM users WHERE id = '" . (int)$uid . "'");
                $until = ($until > time() ? $until : time()) + 86400;
                $database->query("UPDATE users SET `$col` = '" . $until . "' WHERE id = '" . (int)$uid . "'");
                break;
            case 31: $database->addAdventure($session->vid, $uid, 20); break;
            case 32: $database->addHeroItem($uid, 12, 0, 1); $database->addHeroItem($uid, 12, 0, 1); break;
            case 33: $database->setSilver($uid, 1000, 1); break;
            case 41: $database->modifyGold($uid, 40, 1); break;
            case 42: $database->setSilver($uid, 4000, 1); break;
            case 43: $database->addAdventure($session->vid, $uid, 50); break;
        }
        return self::rewardText($n, $type);
    }
}
