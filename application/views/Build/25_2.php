<?php
if ($session->qArrayW1[0] == 11 && php7_is_zero($session->qArrayW1[1])) {
    $database->query("UPDATE quests SET world1='11,1' WHERE userid = " . sql_num($session->uid) . "");
}
?>

<p><?= RE8 ?></p>

<table cellpadding="1" cellspacing="1" id="build_value">
    <tr>
        <th><?= RE9 ?></th>
        <td><b><?php echo $village->cp; ?></b> <?= RE10 ?></td>
    </tr>
    <tr>
        <th><?= RE11 ?></th>
        <td><b><?php echo $database->getVSumField($session->uid, 'cp'); ?></b> <?= RE10 ?></td>
    </tr>
</table><p><?= RE12 ?> <b><?php echo $session->cp; ?></b> <?= RE13 ?> <b><?php $mode = CP;
        $total = safe_count($session->villages);
        echo ${'cp' . $mode}[$total + 1]; ?></b> <?= RE14 ?>.</p>
