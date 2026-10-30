<?php

declare(strict_types=1);

use Core\Automation;
use Core\Database\DB;
use Game\Buildings\BuildingRelocation;

require __DIR__ . '/../main_script/copyable/include/env.php';
require __DIR__ . '/../main_script/include/bootstrap.php';

mysqli_report(MYSQLI_REPORT_OFF);
set_error_handler(static function (int $severity, string $message): bool {
    if (str_contains($message, 'Mysqli Error') || str_contains($message, 'SQL syntax')) {
        throw new RuntimeException($message);
    }
    return false;
});

function relocation_expect_same(mixed $expected, mixed $actual, string $label): void
{
    if ($actual !== $expected) {
        throw new RuntimeException(sprintf(
            '%s: expected %s, received %s',
            $label,
            var_export($expected, true),
            var_export($actual, true)
        ));
    }
}

function relocation_slots(DB $db, int $kid, array $slots): array
{
    $columns = [];
    foreach ($slots as $slot) {
        $columns[] = "CONCAT(f{$slot}t, ':', f{$slot})";
    }
    $row = $db->query('SELECT ' . implode(', ', $columns) . " FROM fdata WHERE kid=$kid")->fetch_row();
    return array_combine($slots, $row);
}

// Placement validation (no database needed).
relocation_expect_same([], BuildingRelocation::normalizePlacement([]), 'empty placement is a no-op');
relocation_expect_same([], BuildingRelocation::normalizePlacement(['20' => '20']), 'unchanged slots are dropped');
relocation_expect_same([19 => 25, 25 => 19], BuildingRelocation::normalizePlacement(['25' => '19', '19' => '25']), 'swap accepted');
relocation_expect_same([19 => 21, 20 => 19, 21 => 20], BuildingRelocation::normalizePlacement([19 => 21, 20 => 19, 21 => 20]), 'rotation accepted');
relocation_expect_same(null, BuildingRelocation::normalizePlacement([19 => 20]), 'one-sided move rejected (would duplicate a building)');
relocation_expect_same(null, BuildingRelocation::normalizePlacement([19 => 20, 21 => 20, 20 => 19]), 'duplicate source rejected');
relocation_expect_same(null, BuildingRelocation::normalizePlacement([19 => 39, 39 => 19]), 'rally point stays in place');
relocation_expect_same(null, BuildingRelocation::normalizePlacement([19 => 40, 40 => 19]), 'wall stays in place');
relocation_expect_same(null, BuildingRelocation::normalizePlacement([1 => 19, 19 => 1]), 'resource fields cannot be moved');
relocation_expect_same(null, BuildingRelocation::normalizePlacement(['19a' => 20, 20 => 19]), 'malformed slot rejected');
relocation_expect_same(null, BuildingRelocation::normalizePlacement('19=20'), 'non-array placement rejected');

$db = DB::getInstance();
$started = false;
try {
    relocation_expect_same(true, $db->begin_transaction(), 'fixture transaction started');
    $started = true;

    $db->query(
        "INSERT INTO users (uuid, name, password, email, race, kid, cp, lastupdate, signupTime, desc1, desc2, note, ajax_token)
         VALUES (UUID(), 'Relocation fixture', 'fixture-password', 'relocation@example.test', 1, 0, 0, 100, 100, '', '', '', 'relocation-ajax-token')"
    );
    $uid = (int)$db->lastInsertId();
    $kid = (int)$db->fetchScalar('SELECT id FROM wdata WHERE fieldtype > 0 AND occupied = 0 ORDER BY id LIMIT 1');
    relocation_expect_same(true, $uid > 0 && $kid > 0, 'fixture player and village position available');
    $db->query("UPDATE users SET kid=$kid WHERE id=$uid");
    $db->query(
        "INSERT INTO vdata (kid, owner, fieldtype, name, capital, pop, cp, maxstore, maxcrop, last_loyalty_update, lastmupdate, created, expandedfrom)
         VALUES ($kid, $uid, 1, 'Relocation village', 1, 20, 10, 800, 800, 100, 100, 100, 0)"
    );
    // 19 warehouse 5 · 20 granary 3 (upgrading) · 21 empty · 22 barracks 2 (being demolished) · 26 main building 10
    // 39 rally point 1 · 40 city wall 2
    $db->query(
        "INSERT INTO fdata (kid, f19, f19t, f20, f20t, f22, f22t, f26, f26t, f39, f39t, f40, f40t)
         VALUES ($kid, 5, 10, 3, 11, 2, 19, 10, 15, 1, 16, 2, 31)"
    );
    $future = time() + 86400;
    $db->query("INSERT INTO building_upgrade (kid, building_field, isMaster, start_time, commence) VALUES ($kid, 20, 0, 100, $future)");
    $upgradeTask = (int)$db->lastInsertId();
    $db->query("INSERT INTO demolition (kid, building_field, end_time, complete) VALUES ($kid, 22, $future, 0)");
    $demolitionTask = (int)$db->lastInsertId();

    // granary → empty site 21, warehouse ↔ main building, barracks stays
    relocation_expect_same(true, BuildingRelocation::apply($kid, [21 => 20, 20 => 21, 19 => 26, 26 => 19]), 'relocation applied');
    relocation_expect_same(
        [19 => '15:10', 20 => '0:0', 21 => '11:3', 22 => '19:2', 26 => '10:5', 39 => '16:1', 40 => '31:2'],
        relocation_slots($db, $kid, [19, 20, 21, 22, 26, 39, 40]),
        'buildings moved with their levels'
    );
    relocation_expect_same(21, (int)$db->fetchScalar("SELECT building_field FROM building_upgrade WHERE id=$upgradeTask"), 'construction task follows the building');
    relocation_expect_same(22, (int)$db->fetchScalar("SELECT building_field FROM demolition WHERE id=$demolitionTask"), 'unmoved demolition stays');

    // barracks (being demolished) → 30, the demolition goes along
    relocation_expect_same(true, BuildingRelocation::apply($kid, [30 => 22, 22 => 30]), 'second relocation applied');
    relocation_expect_same(30, (int)$db->fetchScalar("SELECT building_field FROM demolition WHERE id=$demolitionTask"), 'demolition task follows the building');
    relocation_expect_same(['22' => '0:0', '30' => '19:2'], array_combine(['22', '30'], array_values(relocation_slots($db, $kid, [22, 30]))), 'barracks moved');

    // invalid placements change nothing
    $before = relocation_slots($db, $kid, range(19, 40));
    relocation_expect_same(false, BuildingRelocation::apply($kid, [19 => 39, 39 => 19]), 'rally point move refused');
    relocation_expect_same(false, BuildingRelocation::apply($kid, [19 => 21]), 'duplicating move refused');
    relocation_expect_same($before, relocation_slots($db, $kid, range(19, 40)), 'refused placements leave the village untouched');

    // the worker completes the queued upgrade on the building's new site
    $db->query("UPDATE building_upgrade SET commence=0 WHERE id=$upgradeTask");
    relocation_expect_same(true, Automation::getInstance()->processBuildingTask($upgradeTask), 'moved construction task processed');
    relocation_expect_same('11:4', relocation_slots($db, $kid, [21])[21], 'granary upgraded on its new site');
    relocation_expect_same('0:0', relocation_slots($db, $kid, [20])[20], 'old site untouched by the upgrade');

    // a Wonder of the World village keeps its layout
    $db->query("UPDATE vdata SET isWW=1 WHERE kid=$kid");
    relocation_expect_same(false, BuildingRelocation::apply($kid, [19 => 20, 20 => 19]), 'wonder village refused');
    relocation_expect_same(true, $db->inTransaction(), 'refusals keep the outer transaction usable');

    relocation_expect_same(true, $db->rollback(), 'fixture rolled back');
    $started = false;
    relocation_expect_same(0, (int)$db->fetchScalar("SELECT COUNT(*) FROM users WHERE name='Relocation fixture'"), 'fixture cleanup');
    echo "Building relocation regression passed.\n";
} catch (Throwable $exception) {
    if ($started) {
        $db->rollback();
    }
    fwrite(STDERR, "building-relocation: {$exception->getMessage()}\n");
    exit(1);
}
