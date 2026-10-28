<?php

namespace Controller\Ajax;

use Controller\RallyPoint\Simulator;

/**
 * Combat simulator of the Travian Legends 655 rally point (rallypoint/legendsSimulators.php, js/Game/Legends/simulators.js):
 * runs the game's battle (Controller\RallyPoint\Simulator, the same one the classic simulator uses) and answers the
 * loss ratios. POST battle = JSON {raid, attacker: {tribe, units[10], levels[10], pop, hero: {on, power, items, bonus,
 * horse}}, defenders: [{tribe, units[10], levels[10], hero}], pop, wall, residence, stonemason, scout, durability}.
 */
class legendsCombat extends AjaxBase
{
    private const TRIBES = [1, 2, 3, 4, 5, 6, 7];

    public function dispatch()
    {
        if (!$this->session->isValid() || !is_hero_v2()) {
            return $this->error('Not available.');
        }
        $in = json_decode((string)($_POST['battle'] ?? ''), true);
        if (!is_array($in) || !is_array($in['attacker'] ?? null) || !is_array($in['defenders'] ?? null) || !$in['defenders']) {
            return $this->error('Invalid battle.');
        }
        $tribe = static fn($t, $default) => in_array((int)$t, self::TRIBES, true) ? (int)$t : $default;
        $list = static function ($values, int $max) {
            $out = [];
            for ($i = 0; $i < 10; ++$i) {
                $out[] = max(0, min($max, (int)($values[$i] ?? 0)));
            }
            return $out;
        };
        $hero = static function ($h, string $side) {
            $h = is_array($h) ? $h : [];
            return [
                'health' => 100,
                'power' => max(0, min(100, (int)($h['power'] ?? 0))),
                'str' => max(0, min(100000, (int)($h['items'] ?? 0))),
                ($side . 'Bonus') => max(0, min(100, (int)($h['bonus'] ?? 0))),
                'isCavalry' => !empty($h['horse']),
                'arm' => 0, 'n' => 0, 'cages' => 0,
            ];
        };
        $attacker = $in['attacker'];
        $attTribe = $tribe($attacker['tribe'] ?? 1, 1);
        if ($attTribe === 4 || $attTribe === 5) {
            $attTribe = 1;
        }
        $mainTribe = $tribe($in['defenders'][0]['tribe'] ?? 1, 1);
        $data = [
            'R' => !empty($in['raid']),
            'trapped2killed' => [],
            'defender' => [
                'r' => $mainTribe - 1,
                't' => 0,
                'p' => max(1, (int)($in['pop'] ?? 1)),
                'rpLevel' => max(0, min(20, (int)($in['residence'] ?? 0))),
                'wLevel' => max(0, min(20, (int)($in['wall'] ?? 0))),
                'stone' => max(0, min(20, (int)($in['stonemason'] ?? 0))),
                'artifacts' => ['durability' => max(0, min(5, (int)($in['durability'] ?? 0))), 'scout' => max(0, min(5, (int)($in['scout'] ?? 0)))],
            ],
            'waves' => [],
        ];
        foreach (array_slice($in['defenders'], 0, 20) as $defender) {
            if (!is_array($defender)) {
                continue;
            }
            $wave = ['r' => $tribe($defender['tribe'] ?? 1, 1) - 1, 'u' => $list($defender['units'] ?? [], PHP_INT_MAX), 'U' => $list($defender['levels'] ?? [], 20), 'side' => 'def'];
            if (!empty($defender['hero']['on'])) {
                $wave['h'] = true;
                $wave['hero'] = $hero($defender['hero'], 'def');
            }
            $data['waves'][] = $wave;
        }
        $units = $list($attacker['units'] ?? [], PHP_INT_MAX);
        if (!array_sum($units) && empty($attacker['hero']['on'])) {
            $this->response['data'] = ['losses' => [0, 0], 'deadHeroes' => ['off' => 0, 'def' => []], 'info' => []];
            return true;
        }
        $data['waves'][] = [
            'r' => $attTribe - 1,
            'u' => $units,
            'U' => $list($attacker['levels'] ?? [], 20),
            'side' => 'off',
            'h' => !empty($attacker['hero']['on']),
            'hero' => $hero($attacker['hero'] ?? [], 'off'),
            'p' => max(1, (int)($attacker['pop'] ?? 1)),
            'b' => [max(0, min(20, (int)($in['catapultLevel'] ?? 0))), 0],
        ];
        $report = (new Simulator())->init($data);
        $result = $report[0] ?? [];
        $this->response['data'] = [
            'losses' => array_map('floatval', $result['losses'] ?? [0, 0]),
            'deadHeroes' => $result['dead_heroes'] ?? ['off' => 0, 'def' => []],
            'info' => $result['info'] ?? [],
        ];
        return true;
    }
}
