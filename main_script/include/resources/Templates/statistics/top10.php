<?php

use Core\Config;

if (!$vars['isAlliance']): ?>
    <?php
    $config = Config::getInstance();
    $size = 0;
    $max = 0;
    $arr = ['topAttacker', 'topDefender', 'topClimber', 'topRaider'];
    foreach ($arr as $name) {
        $bonus = Config::getProperty("bonus", "top10", $name);
        if (!$bonus->enabled) continue;
        $size++;
        $x = max(array_keys($bonus->ranks));
        if ($x > $max) {
            $max = $x;
        }
    }
    if ($size): ?>
        <div id="allianceBonusWrapper">
            <div class="roundedCornersBox bonusBox" id="bonusInfo0">
                <h4>
                    <button type="button" class="icon bonusCollapse" ref="bonusInfo0">
                        <img src="img/x.gif" class="openedClosedSwitch switchClosed"
                             alt="openedClosedSwitch switchClosed">
                    </button>
                    <span style="font-size: 14px;"><strong><?= T("Statistics", "Prizes for top 10"); ?> 😍</strong></span>
                </h4>
                <div class="bonusInfo collapsed bonusInfo0 hide">
                        <table cellpadding="1" cellspacing="1">
                            <thead>
                            <tr>
                                <th><?= T("Statistics", "Bonus name"); ?></th>
                                <?php for ($i = 1; $i <= $max; ++$i): ?>
                                    <th><?= $i; ?></th>
                                <?php endfor; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $translation = [
                                'topAttacker' => T("Statistics", "top10.attackers of the week"),
                                'topDefender' => T("Statistics", "top10.defenders of the week"),
                                'topClimber'  => T("Statistics", "top10.climbers of the week"),
                                'topRaider'   => T("Statistics", "top10.robbers of the week"),
                            ];
                            foreach ($arr as $name):
                                $bonus = Config::getProperty("bonus", "top10", $name);
                                if (!$bonus->enabled) continue;
                                ?>
                                <tr>
                                    <td><b><?= $translation[$name]; ?></b></td>
                                    <?php for ($i = 1; $i <= $max; ++$i): ?>
                                        <?php if (isset($bonus->ranks[$i]) && $bonus->ranks[$i] > 0): ?>
                                            <td style="background-color: palegoldenrod">
                                                <b><?= $bonus->ranks[$i]; ?></b> <img src="img/x.gif" class="gold"
                                                                                      alt="gold">
                                            </td>
                                        <?php else: ?>
                                            <td>-</td>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($size === 0): ?>
                                <tr>
                                    <td class="noData" colspan="11"><?= T("Statistics",
                                            "No prize is declared for top10"); ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <p>
                            <?= sprintf(T("inGame", "infoBox.MedalsWillBeGivenIn"),
                                appendTimer((($config->dynamic->lastMedalsGiven + $config->game->medals_interval)) - time()))
                            ?>
                        </p>
                        <p class="warning" style="font-weight: bold"><?= T("Statistics",
                                "top10 prize distribution desc"); ?></p>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        <script type="text/javascript">
            jQuery(function() {
                Travian.Game.AllianceDonation.initBonusOverview();
            });
        </script>
    <br/>
    <?php endif; ?>
<?php endif; ?>
<?php $top10 = []; ?>
<?php ob_start(); ?>
    <h4
            class="round small  top top10_offs"><?=T("Statistics", "top10.attackers of the week"); ?></h4>
    <table cellpadding="1" cellspacing="1" id="top10_offs"
           class="top10 row_table_data">
        <thead>
        <tr>
            <td><?=T("Statistics", "top10.No"); ?>.</td>
            <td><?=T("Statistics", $vars['isAlliance'] ? "alliance" : "player"); ?></td>
            <td><?=T("Statistics", "top10.points"); ?></td>
        </tr>
        </thead>
        <tbody>
        <?=$vars['top10_offs']; ?>
        </tbody>
    </table>
<?php $top10['offs'] = ob_get_clean(); ?>
<?php ob_start(); ?>
    <h4
            class="round small spacer top top10_defs"><?=T("Statistics", "top10.defenders of the week"); ?></h4>
    <table cellpadding="1" cellspacing="1" id="top10_defs"
           class="top10 row_table_data">
        <thead>
        <tr>
            <td><?=T("Statistics", "top10.No"); ?>.</td>
            <td><?=T("Statistics", $vars['isAlliance'] ? "alliance" : "player"); ?></td>
            <td><?=T("Statistics", "top10.points"); ?></td>
        </tr>
        </thead>
        <tbody>
        <?=$vars['top10_deffs']; ?>
        </tbody>
    </table>
<?php $top10['defs'] = ob_get_clean(); ?>
<?php ob_start(); ?>
    <?php if (!$vars['isAlliance'] && getDisplay("topHammers")): ?>
        <h4 class="round small spacer top top10_defs"><?=T("Statistics", "top10.top off hammer"); ?></h4>
        <table cellpadding="1" cellspacing="1" id="top10_raiders"
               class="top10 row_table_data">
            <thead>
            <tr>
                <td><?=T("Statistics", "top10.No"); ?>.</td>
                <td><?=T("Statistics", $vars['isAlliance'] ? "alliance" : "player"); ?></td>
                <td><?=T("Statistics", "top10.date"); ?></td>
            </tr>
            </thead>
            <tbody>
            <?=$vars['top10_off_top']; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php $top10['offHammer'] = ob_get_clean(); ?>
<?php ob_start(); ?>
    <h4 class="round small  top top10_climbers"><?=T("Statistics", "top10.climbers of the week"); ?></h4>
    <table cellpadding="1" cellspacing="1" id="top10_climbers"
           class="top10 row_table_data">
        <thead>
        <tr>
            <td><?=T("Statistics", "top10.No"); ?>.</td>
            <td><?=T("Statistics", $vars['isAlliance'] ? "alliance" : "player"); ?></td>
            <td><?=$vars['isAlliance'] || getCustom("usePopulationAsClimbersRank") ? T("Statistics",
                    "top10.pop") : T("Statistics", "top10.ranks"); ?></td>
        </tr>
        </thead>
        <tbody>
        <?=$vars['top10_climbers']; ?>
        </tbody>
    </table>
<?php $top10['climbers'] = ob_get_clean(); ?>
<?php ob_start(); ?>
    <h4 class="round small spacer top top10_raiders"><?=T("Statistics", "top10.robbers of the week"); ?></h4>
    <table cellpadding="1" cellspacing="1" id="top10_raiders"
           class="top10 row_table_data">
        <thead>
        <tr>
            <td><?=T("Statistics", "top10.No"); ?>.</td>
            <td><?=T("Statistics", $vars['isAlliance'] ? "alliance" : "player"); ?></td>
            <td><?=T("Statistics", "top10.resources"); ?></td>
        </tr>
        </thead>
        <tbody>
        <?=$vars['top10_robbers']; ?>
        </tbody>
    </table>
<?php $top10['robbers'] = ob_get_clean(); ?>
<?php ob_start(); ?>
    <?php if (!$vars['isAlliance'] && getDisplay("topHammers")): ?>
        <h4 class="round small spacer top top10_raiders"><?=T("Statistics", "top10.top def hammer"); ?></h4>
        <table cellpadding="1" cellspacing="1" id="top10_raiders"
               class="top10 row_table_data">
            <thead>
            <tr>
                <td><?=T("Statistics", "top10.No"); ?>.</td>
                <td><?=T("Statistics", $vars['isAlliance'] ? "alliance" : "player"); ?></td>
                <td><?=T("Statistics", "top10.date"); ?></td>
            </tr>
            </thead>
            <tbody>
            <?=$vars['top10_def_top']; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php $top10['defHammer'] = ob_get_clean(); ?>
<?php if (is_hero_v2()): /* Legends: one grid of heading + table pairs, two per row */ ?>
<div class="top10Wrapper">
<?= $top10['offs'] . $top10['defs'] . $top10['climbers'] . $top10['robbers'] . $top10['offHammer'] . $top10['defHammer']; ?>
</div>
<?php else: ?>
<div id="statLeft" class="top10Wrapper">
<?= $top10['offs'] . $top10['defs'] . $top10['offHammer']; ?>
</div>
<div id="statRight" class="top10Wrapper">
<?= $top10['climbers'] . $top10['robbers'] . $top10['defHammer']; ?>
</div>
<?php endif; ?>
