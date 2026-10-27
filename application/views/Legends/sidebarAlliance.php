<?php
/*
 * Alliance box of the Travian Legends look (left sidebar, sideinfo.php): four round green buttons (regions, alliance,
 * forum, embassy) and the alliance banner with its name; without an alliance only the embassy button is active.
 * Uses $posolstvo / $embassySlot from sideinfo.php.
 */
$e = [LegendsUi::class, 'e'];
$t = [LegendsUi::class, 't'];
$inAlliance = !php7_is_zero($session->alliance);
$ally = $inAlliance ? ($allianceinfoMY ?? $database->getAlliance($session->alliance)) : null;
$button = function (string $name, string $svg, string $href, string $title, bool $enabled) use ($e): string {
    return '<a class="layoutButton buttonFramed withIcon round ' . $name . ' green' . ($enabled ? '' : ' disabled') . '"'
        . ' href="' . ($enabled ? $e($href) : '#') . '"' . ($enabled ? '' : ' onclick="return false;"') . ' title="' . $e($title) . '">'
        . LegendsUi::svg($svg, $name) . '</a>';
};
$embassyHref = !empty($embassySlot) ? 'build.php?id=' . $embassySlot[0] . '&newdid=' . $embassySlot[1] : '#';
?>
<div id="sidebarBoxAlliance" class="sidebarBox">
    <div class="header">
        <div class="buttonsWrapper">
            <?php
            echo $button('region', 'sbRegion', 'karte.php', $t('ALLY_REGIONS', 'Regions'), $inAlliance);
            echo $button('alliance', 'sbAlliance', 'allianz.php', $t('ALLY_OVERVIEW', 'Alliance overview'), $inAlliance);
            echo $button('forum', 'sbForum', 'allianz.php?s=2', $t('ALLY_FORUM', 'Alliance forum'), $inAlliance);
            echo $button('embassy', 'sbEmbassy', $embassyHref, $t('ALLY_EMBASSY', 'Embassy'), $embassyHref !== '#');
            ?>
        </div>
    </div>
    <div class="content">
        <?php if ($inAlliance && $ally) {
            // banner: a shield in a colour of the alliance with its tag (T4.6 has no banner editor)
            $hue = ((int)$ally['id'] * 67) % 360;
            $tag = function_exists('mb_substr') ? mb_substr((string)$ally['tag'], 0, 3) : substr((string)$ally['tag'], 0, 3);
            ?>
            <a class="boxTitle withBanner" href="allianz.php">
                <svg class="allianceBanner" viewBox="0 0 50 50" width="50" height="50" aria-hidden="true">
                    <path d="M25 3 44 9v15c0 12-8 19-19 23C14 43 6 36 6 24V9z" fill="hsl(<?php echo $hue; ?>,45%,32%)" stroke="#2b1d12" stroke-width="2"/>
                    <path d="M25 3v44C14 43 6 36 6 24V9z" fill="hsl(<?php echo ($hue + 180) % 360; ?>,40%,22%)" opacity=".55"/>
                    <path d="M10 6 40 38M40 6 10 38" stroke="#1f2b6a" stroke-width="2.4" stroke-linecap="round"/>
                    <text x="25" y="30" text-anchor="middle" font-family="'Noto Serif',Georgia,serif" font-weight="700" font-size="12" fill="#f4efe4"><?php echo $e($tag); ?></text>
                </svg>
                <span class="name"><?php echo $e($ally['name'] !== '' ? $ally['name'] : $ally['tag']); ?></span>
            </a>
        <?php } else { ?>
            <div class="boxTitle"><span class="name"><?php echo $e($t('ALLY_NONE', 'No alliance')); ?></span></div>
            <div class="noAlliance"><?php echo $lang['Alliance']['00']; ?></div>
        <?php } ?>
    </div>
</div>
