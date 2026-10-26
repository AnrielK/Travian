<?php /* A unit of a training building, laid out as in Travian Legends: picture, name with the smithy level and the
          units at home, costs, time, then the count and the exchange button in one row; the star lists it first. */ ?>
<div class="action ovTrainCard<?= $vars['index'] == 1 ? ' first' : ''; ?><?= $vars['favorite'] ? ' favorite' : ''; ?>"
     data-unit="<?= $vars['unitId']; ?>" data-order="<?= $vars['order']; ?>">
    <?php if ($vars['favorite'] !== null): ?>
        <button type="button" class="ovFavorite<?= $vars['favorite'] ? ' active' : ''; ?>"
                title="<?= T("inGame", $vars['favorite'] ? "unitFavoriteRemove" : "unitFavorite"); ?>"
                data-add="<?= T("inGame", "unitFavorite"); ?>" data-remove="<?= T("inGame", "unitFavoriteRemove"); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.8l2.75 5.95 6.5.72-4.86 4.4 1.36 6.4L12 17.02l-5.75 3.25 1.36-6.4-4.86-4.4 6.5-.72z"/></svg>
        </button>
    <?php endif; ?>
    <?php if ($vars['nr'] != 99): ?>
        <div class="bigUnitSection">
            <a href="#"
               onclick="return Travian.Game.iPopup(<?= $vars['unitId']; ?>,1);">
                <img class="unitSection u<?= $vars['unitId']; ?>Section"
                     src="img/x.gif"
                     alt="<?= T("Troops", $vars['unitId'] . ".title"); ?>"
                     title="<?= T(
                         "Troops", $vars['unitId'] . ".title"
                     ); ?>"/>
            </a>
            <a href="#" class="zoom"
               onclick="return Travian.Game.unitZoom(<?= $vars['unitId']; ?>);">
                <img class="zoom" src="img/x.gif"
                     alt="<?= T("inGame", "zoom_in"); ?>"
                     title="<?= T("inGame", "zoom_in"); ?>"/>
            </a>
        </div>
    <?php endif; ?>
    <div class="details">
        <div class="tit">
            <a href="#"
               onclick="return Travian.Game.iPopup(<?= $vars['unitId']
               == 99 ? '36, 4' : $vars['unitId'] . ', 1'; ?>);"><img
                        class="unit u<?= $vars['unitId']; ?>" src="img/x.gif"
                        alt="<?= T("Troops", $vars['unitId'] . ".title"); ?>"
                        title="<?= T(
                            "Troops", $vars['unitId'] . ".title"
                        ); ?>"/></a>
            <a href="#"
               onclick="return Travian.Game.iPopup(<?= $vars['unitId']
               == 99 ? '36, 4' : $vars['unitId'] . ', 1'; ?>);"><?= T(
                    "Troops", $vars['unitId'] . ".title"
                ); ?></a>
            <?php if ($vars['favorite'] !== null && $vars['nr'] <= 8): ?>
                <span class="level"><?= mb_convert_case(T("Buildings", "level"), MB_CASE_TITLE); ?> <?= $vars['smithyLevel']; ?></span>
            <?php endif; ?>
            <span class="furtherInfo">(<?= T(
                    "inGame", "available"
                ); ?> <?= number_format_x($vars['available']); ?>)</span>
        </div>
        <div class="inlineIconList resourceWrapper">
            <div class="inlineIcon resource"><i class="r1Big"></i><span
                        class="value value"><?= $vars['cost'][0]; ?></span></div>
            <div class="inlineIcon resource"><i class="r2Big"></i><span
                        class="value value"><?= $vars['cost'][1]; ?></span></div>
            <div class="inlineIcon resource"><i class="r3Big"></i><span
                        class="value value"><?= $vars['cost'][2]; ?></span></div>
            <div class="inlineIcon resource"><i class="r4Big"></i><span
                        class="value value"><?= $vars['cost'][3]; ?></span></div>
            <div class="inlineIcon resource"><i class="cropConsumptionBig"></i><span
                        class="value value"><?= $vars['upkeep']; ?></span></div>
        </div>
        <div class="inlineIcon duration"><i class="<?=$vars['durationClass'];?>"></i><span
                    class="value "><?= $vars['duration']; ?></span></div>
        <div class="cta">
            <div class="ovCount">
                <span class="value"><?= T("inGame", "trainCount"); ?></span>
                <input type="text" class="text" name="t<?= $vars['index']; ?>" value="0"
                       maxlength="<?= max(strlen($vars['max']), 4); ?>"/><span class="value"> / </span>
                <a href="#"
                   onclick="jQuery(this).closest('div.details').find('input').val(<?= $vars['max']; ?>); return false;"><?= number_format_x($vars['max']); ?></a>
            </div>
            <?= $vars['npc']; ?>
            <div class="status none"><?= $vars['none_status']; ?></div>
        </div>
    </div>
    <div class="clear"></div>
</div>