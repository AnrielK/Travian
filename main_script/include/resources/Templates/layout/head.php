<?php
use Core\Helper\PreferencesHelper;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?= get_language_properties('title'); ?></title>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="imagetoolbar" content="no"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="content-language" content="<?=get_locale(); ?>"/>
    <link href="<?=get_gpack_link_and_hash("compact.css"); ?>"
          rel="stylesheet" type="text/css"/>
    <?=ov_gpack_adapter_links(); ?>
    <link href="<?=get_gpack_link_and_hash("lang.css"); ?>"
          rel="stylesheet" type="text/css"/>
    <?php if (is_lowres()): ?>
        <link href="<?=get_gpack_link_and_hash("compact-lowres.css"); ?>" rel="stylesheet" type="text/css"/>

    <?php endif; ?>
    <link href="<?=get_gpack_link_and_hash("fixes.css", false); ?>?rev13" rel="stylesheet" type="text/css"/>
    <?php if (is_classic_gpack()): ?>
        <!-- T4.4 packs: icons and village centre slots for the T4.6 markup (scripts/gpack/make-classic-compat.py) -->
        <link href="<?=get_gpack_link_and_hash("openvillage-compat.css", false); ?>?rev2" rel="stylesheet" type="text/css"/>
    <?php endif; ?>
    <link href="openvillage.css?rev33" rel="stylesheet" type="text/css"/>
    <?php if (is_t40_layout()): ?>
        <!-- Travian 4.0: page frame of layout/LayoutT40.php (scripts/gpack/import-t40-gpack.py) -->
        <link href="<?=get_gpack_link_and_hash("openvillage-t40.css", false); ?>?rev1" rel="stylesheet" type="text/css"/>
    <?php endif; ?>

    <script type="text/javascript">
        window.ajaxToken = '<?=(isset($vars['ajaxToken']) ? $vars['ajaxToken'] : null);?>';
        window._player_uuid = '<?=(isset($vars['_player_uuid']) ? $vars['_player_uuid'] : null);?>';
    </script>
    <script type="text/javascript" src="js/default/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/default/jquery.md5.min.js"></script>
    <script type="text/javascript" src="js/default/jquery.scrollbar.min.js"></script>
    <script type="text/javascript" src="js/Game/General/General.js"></script>
    <script type="text/javascript">
        <?php
        $data = [
            'Map' => [
                'Size' => [
                    'width'  => (2 * MAP_SIZE) + 1,
                    'height' => (2 * MAP_SIZE) + 1,
                    'left'   => -MAP_SIZE,
                    'right'  => MAP_SIZE,
                    'bottom' => -MAP_SIZE,
                    'top'    => MAP_SIZE,
                ],
            ],
            'Season' => detect_season(),
        ];
        ?>
        window.TravianDefaults = Object.assign(<?=json_encode($data);?>, false || {});
    </script>
    <script type="text/javascript" src="<?=get_crypt_js_link(); ?>"></script>
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <script type="text/javascript">
        <?php if(!(isset($vars['autoReload']) && $vars['autoReload'] == 1)):?>
        window.reload_enabled = false;
        <?php endif;?>
        Travian.Translation.add({
            'allgemein.anleitung': '<?=T("Global", "General.instructions");?>',
            'allgemein.cancel': '<?=T("Global", "General.cancel");?>',
            'allgemein.ok': '<?=T("Global", "General.ok");?>',
            'allgemein.close': '<?=T("Global", "General.close");?>',
            'allgemein.close_with_capital_c': '<?=T("Global", "General.close");?>',
            'cropfinder.keine_ergebnisse': '<?=T("Global", "cropfinder.no results found");?>'
        });
        <?php
        $buttonTemplate = '<button ><div class="button-container addHoverClick"><div class="button-background"><div class="buttonStart"><div class="buttonEnd"><div class="buttonMiddle"></div></div></div></div><div class="button-content"></div></div></button>';

        $buttonTemplate = '<button ><div class="button-container addHoverClick"><div class="button-background"><div class="buttonStart"><div class="buttonEnd"><div class="buttonMiddle"></div></div></div></div><div class="button-content"></div></div></button>';


        $eventJamHtml = '<a href="javascript:void" onclick="document.location.reload();"><img src="img/refresh.png" alt="Reload"></a>';
        ?>
        Travian.applicationId = 'OpenVillage Game';
        Travian.Game.version = '4.6';
        Travian.Game.worldId = '<?=getWorldId();?>';
        Travian.Game.speed = <?=getGameSpeed();?>;
        Travian.Game.country = '<?=get_language_properties('country'); ?>';
        Travian.Templates = {};
        Travian.Templates.ButtonTemplate = '<?=$buttonTemplate;?>';
        Travian.Game.eventJamHtml = '<?=$eventJamHtml;?>';
        Travian.Form.UnloadHelper.message = '<?=T("Global", "UnloadHelper.message");?>';
        <?php
        $preferences = PreferencesHelper::getPreferences();
        if (isset($preferences['allianceBonusesOverview'])) {
            $preferences['allianceBonusesOverview'] = json_encode($preferences['allianceBonusesOverview']);
        }
        ?>
        Travian.alphabetizeTeletypesBrushesUnwarranted = function () {
            return '<?=isset($vars['ajaxToken']) ? $vars['ajaxToken'] : null;?>';
        };
        Travian.Game.Preferences.initialize(<?=json_encode($preferences);?>);
        // the manual (manual.php) in its original 475 x 525 window, titled "Manuál" as in Travian Legends; its pages
        // fit the window (openvillage.css), so it does not scroll and its content cannot be moved; narrower on a phone
        Travian.Game.iPopup = function (gid, typ) {
            var dialog = new Travian.Dialog.Dialog({
                title: <?=json_encode(T("Manual", "title"));?>, buttonOk: false, enableBackground: false, draggable: true,
                cssClass: 'white ovManualDialog', fx: {open: {opacity: 1}, close: {opacity: 0}, duration: 0}
            });
            dialog.setContent('<iframe class="popup" frameborder="0" id="Frame" src="manual.php?typ=' + typ + '&amp;gid=' + gid
                + '" width="' + Math.min(475, jQuery(window).width() - 30) + '" height="525" scrolling="no" allowTransparency="true"></iframe>');
            dialog.show();
            return false;
        };
        // the resource counters write with Travian.Formatter, which groups thousands the German way ("74.529"):
        // group them like the numbers from the server (number_format_x), "74 529" in Czech
        (function (Formatter, separator) {
            Travian.Formatter = function (options) {
                Formatter.call(this, options);
                var format = this.getFormattedNumber;
                this.getFormattedNumber = function (number) {
                    return String(format.call(this, number)).replace(/\./g, separator);
                };
            };
            Travian.Formatter.prototype = Formatter.prototype;
        })(Travian.Formatter, <?=json_encode(getDisplay("separateNumbers3By3") ? number_thousands_separator() : '');?>);
    </script>
</head>
