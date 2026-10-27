<?php
/* Switch between the classic T4.6 hero and the Travian Legends hero (under the appearance editor). */
if (HeroLegends::available()) {
    $hlStyle = HeroLegends::storedStyle((int)$session->uid);
    ?>
    <form method="post" action="hero.php" class="heroLookSwitch" style="margin:10px 0 4px;padding:8px 10px;border:1px solid #d6c7a7;border-radius:6px;background:#f7f1e3;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <label for="heroLookSelect" style="font-weight:bold;color:#5c4134;"><?php echo defined('SETTINGS_HERO') ? SETTINGS_HERO : 'Hero'; ?>:</label>
        <select id="heroLookSelect" name="heroLook" onchange="this.form.submit()"><?php echo HeroLegends::styleOptions($hlStyle); ?></select>
        <noscript><input type="submit" value="OK"></noscript>
    </form>
<?php } ?>
