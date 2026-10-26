<map name="rx" id="rx">
    <?php use Core\Session;

    $index = 0; ?>
    <?php foreach ($vars['areas'] as $area):++$index; ?>
        <?php $area['title'] = htmlspecialchars($area['title']); ?>
        <?php $area['alt'] = htmlspecialchars($area['alt']); ?>
        <area data-index="<?= $index; ?>"
              href="build.php?id=<?=$index; ?><?= Session::getInstance()->fastUpgradeActive() ? '&fastUP=1' : null; ?>"
              coords="<?=$area['coordinates']; ?>" shape="circle"
              title="<?=$area['title']; ?>" alt="<?=$area['alt']; ?>"/>
    <?php endforeach; ?>
    <area href="dorf2.php" coords="197,191,32" shape="circle" title="<?=T("inGame", "Navigation.Buildings"); ?>">
</map>
<img id="resfeld" usemap="#rx" src="img/x.gif" alt=""/>
<div id="village_map" class="f<?=$vars['fieldType']; ?>">
    <?php if (is_t40_layout()): ?>
        <?php
        // the fields of the 4.0 picture lie where the Legends picture has them: its outlines (LegendsUi::fieldShape),
        // moved onto the 4.0 picture of #village_map by one matrix fitted on all 12 layouts; the right-to-left
        // picture is the mirror image
        ?>
        <svg class="fieldShapes" width="500" height="370" viewBox="0 0 500 370">
            <g<?=getDirection() == 'RTL' ? ' transform="matrix(-1 0 0 1 500 0)"' : ''; ?>>
                <g transform="matrix(0.9719 -0.031 0.0181 0.9548 13.17 75.16)">
                    <?php for ($field = 1; $field <= 18; ++$field): ?>
                        <?php if ($shape = \resources\View\LegendsUi::fieldShape((int)$vars['fieldType'], $field)): ?>
                            <path data-field="<?=$field; ?>" d="<?=$shape; ?>"/>
                        <?php endif; ?>
                    <?php endfor; ?>
                </g>
            </g>
        </svg>
    <?php endif; ?>
    <?php if ($vars['showColored']): ?>
        <?php $index = 0; ?>
        <?php foreach ($vars['maps'] as $index => $map):++$index; ?>
            <div class="level colorLayer <?=$map['color']; ?> gid<?=$map['item_id']; ?> level<?=$map['level']; ?> <?=$map['upgradeState'] ? ('aid' . $index . ' underConstruction') : 'aid' . ($index); ?>"
                 style="<?=$map['style']; ?>">
                <div class="labelLayer"><?=$map['level'] > 0 ? $map['level'] : ''; ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php $index = 0; ?>
        <?php foreach ($vars['maps'] as $map):++$index; ?>
            <div class="level <?=$map['color']; ?> gid<?=$map['item_id']; ?> level<?=$map['level']; ?> <?=$map['upgradeState'] ? ('aid' . $index . ' underConstruction') : 'aid' . ($index); ?>"
                 style="<?=$map['style']; ?>">
                <?=$map['level'] > 0 ? $map['level'] : ''; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<div id="map_details">
    <div class="movements">
        <?=$vars['movements']; ?>
    </div>
    <div class="boxes villageList production">
        <div class="boxes-tl"></div>
        <div class="boxes-tr"></div>
        <div class="boxes-tc"></div>
        <div class="boxes-ml"></div>
        <div class="boxes-mr"></div>
        <div class="boxes-mc"></div>
        <div class="boxes-bl"></div>
        <div class="boxes-br"></div>
        <div class="boxes-bc"></div>
        <div class="boxes-contents cf">
            <table id="production" cellpadding="1" cellspacing="1">
                <thead>
                <tr>
                    <th colspan="4">
                        <?=T("Dorf1", "production.production per hour"); ?>:
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="ico">
                        <div>
                            <?php if ($vars['productionBoost'][0]): ?>
                                <img src="img/x.gif" class="productionBoost"
                                     alt="<?=T("inGame", "productionBoost.woodProductionBoost"); ?>">
                            <?php endif; ?>
                            <i class="r1"></i>
                        </div>
                    </td>
                    <td class="res">
                        <?=T("Dorf1", "production.resources.1"); ?>:
                    </td>
                    <td class="num">
                        <?=number_format_x($vars['production'][0], 1e12); ?>
                    </td>
                </tr>
                <tr>
                    <td class="ico">
                        <div>
                            <?php if ($vars['productionBoost'][1]): ?>
                                <img src="img/x.gif" class="productionBoost"
                                     alt="<?=T("inGame", "productionBoost.woodProductionBoost"); ?>">
                            <?php endif; ?>
                            <i class="r2"></i>
                        </div>
                    </td>
                    <td class="res">
                        <?=T("Dorf1", "production.resources.2"); ?>:
                    </td>
                    <td class="num">
                        <?=number_format_x($vars['production'][1], 1e12); ?>
                    </td>
                </tr>
                <tr>
                    <td class="ico">
                        <div>
                            <?php if ($vars['productionBoost'][2]): ?>
                                <img src="img/x.gif" class="productionBoost"
                                     alt="<?=T("inGame", "productionBoost.woodProductionBoost"); ?>">
                            <?php endif; ?>
                            <i class="r3"></i>
                        </div>
                    </td>
                    <td class="res">
                        <?=T("Dorf1", "production.resources.3"); ?>:
                    </td>
                    <td class="num">
                        <?=number_format_x($vars['production'][2], 1e12); ?>
                    </td>
                </tr>
                <tr>
                    <td class="ico">
                        <div>
                            <?php if ($vars['productionBoost'][3]): ?>
                                <img src="img/x.gif" class="productionBoost"
                                     alt="<?=T("inGame", "productionBoost.woodProductionBoost"); ?>">
                            <?php endif; ?>
                            <i class="r4"></i>
                        </div>
                    </td>
                    <td class="res">
                        <?=T("Dorf1", "production.resources.4"); ?>:
                    </td>
                    <td class="num">
                        <?=number_format_x($vars['production'][3], 1e12); ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <?=$vars['goldProductionBoostButton']; ?>
        </div>
    </div>
    <div class="boxes villageList units">
        <div class="boxes-tl"></div>
        <div class="boxes-tr"></div>
        <div class="boxes-tc"></div>
        <div class="boxes-ml"></div>
        <div class="boxes-mr"></div>
        <div class="boxes-mc"></div>
        <div class="boxes-bl"></div>
        <div class="boxes-br"></div>
        <div class="boxes-bc"></div>
        <div class="boxes-contents cf">
            <table id="troops" cellpadding="1" cellspacing="1">
                <thead>
                <tr>
                    <th colspan="3">
                        <?=T("Dorf1", "units"); ?>:
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php if ($vars['unitsSummary'] <= 0): ?>
                    <tr>
                        <td><?=T("Dorf1", "none"); ?>.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vars['units'] as $key => $num): ?>
                        <tr>
                            <td class="ico">
                                <a href="build.php?gid=16&tt=1#td">
                                    <img class="unit u<?=$key == 98 ? 'hero' : $key; ?>" src="img/x.gif"
                                         alt="<?=T("Troops", ($key == 98 ? 'hero' : $key) . '.title'); ?>">
                                </a>
                            </td>
                            <td class="num"><?=number_format_x($num, 1e12); ?></td>
                            <td class="un"><?=T("Troops", ($key == 98 ? 'hero' : $key) . '.title'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($vars['heroOnly']): ?>
                        <tr>
                            <td><?=T("Dorf1", "none"); ?>.</td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?=$vars['onLoadBuildings']; ?>
<?php if (is_t40_layout()): ?>
    <script type="text/javascript">
        // light up the outline of the field under the pointer in the colours of the Legends look (white fill with a
        // lighter border), below the level circles; a field without an outline gets the circle of the image map
        jQuery(function () {
            var map = jQuery('#village_map'), field = jQuery('#resfeld');
            var glow = jQuery('<div class="fieldGlow"></div>').prependTo(map);
            jQuery('#rx area[data-index]').on('mouseenter', function () {
                var shape = map.find('svg.fieldShapes path[data-field="' + this.getAttribute('data-index') + '"]');
                if (shape.length) {
                    shape.addClass('shown');
                    return;
                }
                var circle = (this.getAttribute('coords') || '').split(',');
                if (circle.length < 3) {
                    return;
                }
                // the image map counts from the top left corner of the picture of the fields
                var picture = field[0].getBoundingClientRect(), box = map[0].getBoundingClientRect();
                var radius = parseInt(circle[2], 10);
                glow.css({
                    left: picture.left - box.left + parseInt(circle[0], 10) - radius,
                    top: picture.top - box.top + parseInt(circle[1], 10) - radius,
                    width: 2 * radius,
                    height: 2 * radius
                }).addClass('shown');
            }).on('mouseleave', function () {
                map.find('svg.fieldShapes path').removeClass('shown');
                glow.removeClass('shown');
            });
        });
    </script>
<?php endif; ?>
