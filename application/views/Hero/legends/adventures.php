<?php
/*
 * Adventure list in the Travian Legends look (Templates/hero/v2/adventures.php of the main branch) with the T4.6
 * adventures (table adventure, start_adventure.php). Needs $session, $database, $generator, $where2.
 */
$database->query("DELETE FROM adventure WHERE `time`<= '" . time() . "' and `end`='0'");
$hlRows = $database->query("SELECT * FROM adventure WHERE `end` = '0' and `uid` = '" . sql_esc($session->uid) . "' ORDER BY time ASC");
$hlRows = is_array($hlRows) ? $hlRows : [];
$hlHome = $database->getCoor($session->heroD['wref']);
$hlPlaces = [1 => 'forest', 2 => 'lake', 3 => 'hill', 4 => 'sea'];
$hlNames = [1 => HEROA7, 2 => HEROA8, 3 => HEROA9, 4 => HEROA10];
$hlStatus = $heroF->getHeroStatus();
?>
<div class="contentV2">
    <div class="heroState">
        <span class="heroStatusMessage"><img src="img/x.gif" class="heroStatus<?php echo (int)$hlStatus; ?>" alt=""></span>
        <div><?php echo $where2; ?></div>
    </div>
    <table class="adventureList borderGap">
        <thead>
        <tr>
            <th class="place"><?php echo HEROA0; ?></th>
            <th class="distance"><?php echo LegendsUi::t('HEROL_DISTANCE', 'Distance'); ?></th>
            <th class="duration"><?php echo HEROA1; ?></th>
            <th class="difficulty"><?php echo HEROA2; ?></th>
            <th class="button"></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$hlRows) { ?>
            <tr><td colspan="5" class="noAdventures"><?php echo HEROA5; ?></td></tr>
        <?php } ?>
        <?php foreach ($hlRows as $hlRow) {
            $hlTo = ['x' => $hlRow['x'], 'y' => $hlRow['y']];
            $hlTime = $database->procDistanceTime2(['x' => $hlHome['x'], 'y' => $hlHome['y']], $hlTo, $session->heroD['speed'], 1);
            $hlDistance = sqrt(pow($hlHome['x'] - $hlRow['x'], 2) + pow($hlHome['y'] - $hlRow['y'], 2));
            $hlType = (int)$hlRow['type'];
            $hlHard = !empty($hlRow['dif']);
            $hlLeft = (int)$hlRow['time'] - time();
            ?>
            <tr id="adventure<?php echo (int)$hlRow['wref']; ?>">
                <td class="place"><img src="img/x.gif" class="<?php echo $hlPlaces[$hlType] ?? 'grassland'; ?>" alt="" title="<?php echo htmlspecialchars(($hlNames[$hlType] ?? '') . ' (' . $hlRow['x'] . '|' . $hlRow['y'] . ')'); ?>"></td>
                <td class="distance"><?php echo number_format($hlDistance, 2, ',', ' ') . ' ' . LegendsUi::t('HEROL_FIELDS', 'fields'); ?></td>
                <td class="duration"><div class="cellWrapper"><i class="clock_medium"></i><span class="duration"><?php echo $generator->getTimeFormat($hlTime); ?></span></div></td>
                <td class="difficulty">
                    <div class="iconWrapper" title="<?php echo htmlspecialchars(HEROA3 . ': ' . $generator->getTimeFormat($hlLeft)); ?>">
                        <i class="<?php echo $hlHard ? 'difficulty_hard' : 'difficulty_normal'; ?>"></i>
                    </div>
                </td>
                <td class="button">
                    <?php echo LegendsUi::button(LegendsUi::t('HEROL_EXPLORE', 'Explore'), ['href' => 'start_adventure.php?kid=' . (int)$hlRow['wref'] . '&h=1',
                        'disabledClass' => $hlStatus != 100]); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
