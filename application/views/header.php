<div id="footer" class="size1">
<?php if (Skin::isLegends()) { // footer links of Travian Legends ?>
<div id="pageLinks">
        <a href="index.php" target="_blank"><?= LegendsUi::e(LegendsUi::t('FOOTER_START', 'Homepage')); ?></a>
        <a href="#" target="_blank">Discord</a>
        <a href="#" target="_blank"><?= LegendsUi::e(LegendsUi::t('FOOTER_NEWS', 'News')); ?></a>
        <a href="support.php"><?= LegendsUi::e(LegendsUi::t('FOOTER_SUPPORT', 'Support')); ?></a>
        <a href="rules.php"><?= LegendsUi::e(LegendsUi::t('FOOTER_RULES', 'Game rules')); ?></a>
        <a href="#" target="_blank"><?= LegendsUi::e(LegendsUi::t('FOOTER_TERMS', 'Terms')); ?></a>
        <a href="#" target="_blank"><?= LegendsUi::e(LegendsUi::t('FOOTER_ABOUT', 'About us')); ?></a>
        <div class="clear"></div>
    </div>
    <br>
    <p class="copyright" style="direction:ltr;">© 2004 - <?= date('Y'); ?> Travian Games GmbH</p>
    <br>
</div>
<?php } else { ?>
<div id="pageLinks">
        <a href="https://newtravian.com" target="_blank"><?= $lang['Footer']['Homepage']; ?></a>
        <a href="#" target="_blank"><?= $lang['Footer']['Forum']; ?></a>
        <a href="https://newtravian.com" target="_blank"><?= $lang['Footer']['Links']; ?></a>
        <a href="#" target="_blank"><?= $lang['Footer']['FAQ']; ?></a>
        <a href="#" target="_blank"><?= $lang['Footer']['Terms']; ?></a>
        <a href="#" target="_blank"><?= $lang['Footer']['Imprint']; ?></a>
        <div class="clear"></div>
    </div>
    <br>
    <p class="copyright" style="direction:ltr;">© 2007 - 2023 NewTravian Games GmbH</p>
    <br>
</div>
<?php } ?>
<div id="servertime" class="stime">
    <?php echo SERVER_TIME; ?>&nbsp;
    <span class="timer" counting="up" value="<?php echo time(); ?>"><?php echo date('H:i:s'); ?></span>
</div>

