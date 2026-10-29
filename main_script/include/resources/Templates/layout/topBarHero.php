<?php
/*
 * Hero portrait in the top bar (graphic pack 29c89d54 / Travian Legends layout).
 * Same data as layout/sidebarBoxHero.php, rendered by GameView::renderHeroBox().
 */
use Game\Hero\SessionHero;

$health = (int)round($vars['regenerating'] ? $vars['regeneratedHealth'] : $vars['health']);
$health = max(0, min(100, $health));
$xp = max(0, min(100, (int)round($vars['expPercent'])));
/*
 * Health and experience are arcs of the pack's ring images (hud/topBar/header/hero/health.png, experience.png),
 * cut out with an SVG mask. Both start at the separator at the bottom (≈191° clockwise from 12 o'clock):
 * health runs counter-clockwise over the right side, experience clockwise over the left side.
 */
$heroImg = get_gpack_cdn_mainPage_url() . 'img_ltr/hud/topBar/header/hero/';
$t46Arc = static function (float $percent, bool $clockwise, float $span): string {
    if ($percent <= 0) {
        return '';
    }
    $start = 191.0;
    $end = $clockwise ? $start + $span * $percent / 100 : $start - $span * $percent / 100;
    $point = static fn(float $deg) => sprintf('%.2f %.2f', 55 + 50 * sin(deg2rad($deg)), 55 - 50 * cos(deg2rad($deg)));
    $large = ($span * $percent / 100) > 180 ? 1 : 0;
    return 'M' . $point($start) . ' A50 50 0 ' . $large . ' ' . ($clockwise ? 1 : 0) . ' ' . $point($end);
};
$healthArc = $t46Arc($health, false, 105); // ends at the auction (hammer) button
$xpArc = $t46Arc($xp, true, 105);   // ends at the hero status button
$statusIcon = [
    SessionHero::STATUS_HOME => 'heroHome',
    SessionHero::STATUS_DEAD => 'heroDead',
    SessionHero::STATUS_REVIVING => 'heroReviving',
    SessionHero::STATUS_TRAPPED => 'heroTrapped',
    SessionHero::STATUS_ON_REINFORCE => 'heroReinforcing',
    SessionHero::STATUS_IN_ENFORCEMENT => 'heroReinforcing',
][$vars['status']] ?? 'heroRunning';
?>
<div id="topBarHeroWrapper">
    <div id="topBarHero">
        <div class="health" title="<?=T("HeroGlobal", "health"); ?>: <?=$health; ?>%">
            <svg width="110" height="110" viewBox="0 0 110 110">
                <mask id="t46HealthMask"><path d="<?=$healthArc; ?>" fill="none" stroke="#fff" stroke-width="16"/></mask>
                <?php if ($healthArc !== ''): ?>
                    <image href="<?=$heroImg; ?>health.png" width="110" height="110" mask="url(#t46HealthMask)"<?=$vars['regenerating'] ? ' style="filter:hue-rotate(-60deg)"' : ''; ?>/>
                <?php endif; ?>
            </svg>
        </div>
        <div class="experience" title="<?=T("HeroGlobal", "Experience"); ?>: <?=$vars['exp']; ?>">
            <svg width="110" height="110" viewBox="0 0 110 110">
                <mask id="t46XpMask"><path d="<?=$xpArc; ?>" fill="none" stroke="#fff" stroke-width="16"/></mask>
                <?php if ($xpArc !== ''): ?>
                    <image href="<?=$heroImg; ?>experience.png" width="110" height="110" mask="url(#t46XpMask)"/>
                <?php endif; ?>
            </svg>
        </div>
        <a id="heroImageButton" class="heroImageButton"
           href="hero.php<?=$vars['hasNewPoints'] ? "?flagAttributesBoxOpen" : ''; ?>"
           title="<?=T("HeroGlobal", "HeroOverview"); ?>||<?=htmlspecialchars($vars['longStatus']); ?>">
            <div class="heroImageHover">
                <img class="heroImage"
                     src="hero_head.php?uid=<?=$vars['uid']; ?>&amp;size=sideinfo&amp;<?=$vars['heroImageHash']; ?>"
                     alt="Hero"/>
            </div>
        </a>
        <?php if (is_hero_v2()): ?>
        <div class="heroStatus" title="<?=htmlspecialchars($vars['longStatus']); ?>">
            <a href="hero.php"><i class="<?=$statusIcon; ?>"></i></a>
        </div>
        <?php else: ?>
        <div class="heroStatus" title="<?=htmlspecialchars($vars['longStatus']); ?>">
            <a href="hero.php"></a>
            <svg class="<?=$statusIcon; ?>" viewBox="0 0 20 20">
                <?php if ($statusIcon === 'heroHome'): ?>
                    <path class="outline" d="M10 2 1 10h3v8h5v-5h2v5h5v-8h3z"/>
                <?php elseif ($statusIcon === 'heroDead' || $statusIcon === 'heroReviving'): ?>
                    <path class="outline" d="M10 2C6 2 3 5 3 9c0 2.5 1.2 4.2 3 5.3V18h8v-3.7c1.8-1.1 3-2.8 3-5.3 0-4-3-7-7-7zM7 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3zm6 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3z"/>
                <?php elseif ($statusIcon === 'heroReinforcing'): ?>
                    <path class="outline" d="M10 1 2 4v6c0 4.5 3.4 8.2 8 9 4.6-.8 8-4.5 8-9V4z"/>
                <?php elseif ($statusIcon === 'heroTrapped'): ?>
                    <path class="outline" d="M3 3h14v14H3zM6 3v14M10 3v14M14 3v14"/>
                <?php else: ?>
                    <path class="outline" d="M3 8h9V4l6 6-6 6v-4H3z"/>
                <?php endif; ?>
            </svg>
        </div>
        <?php endif; ?>
        <?php if ($vars['lvlUp'] || $vars['hasNewPoints']): ?>
            <a class="levelUp show" href="hero.php?flagAttributesBoxOpen" title="<?=T("HeroGlobal", "HeroOverview"); ?>"></a>
        <?php endif; ?>
        <a id="<?=$vars['adventureWhiteButton']['id']; ?>" class="layoutButton buttonFramed withIcon round adventure green<?=$vars['adventureWhiteButton']['adventureCount'] ? ' attention' : ''; ?>"
           href="hero.php?t=3" title="<?=T("HeroGlobal", "Adventure"); ?>">
            <?php if ($vars['adventureWhiteButton']['adventureCount']): ?>
                <div class="content"><?=$vars['adventureWhiteButton']['adventureCount']; ?></div>
            <?php else: ?>
                <svg viewBox="0 0 19.75 20" class="adventure"><g class="outline"><path d="M7.82 5L11 8.14 9.22 9.92 6 6.74 2.86 9.92 1.08 8.14 4.26 5 1.08 1.78 2.86 0 6 3.18 9.22 0 11 1.78zm6.83 9.92a2.56 2.56 0 1 0 2.56 2.56 2.56 2.56 0 0 0-2.56-2.6zm-3.55 2.6s-2-.26-4.21-.73A14.89 14.89 0 0 1 2.6 15.4c.55-.37 2.13-1.1 6.49-2 7.6-1.58 10.69-3.1 10.66-5.25 0-1.86-2.44-2.86-4.26-3.45a30.7 30.7 0 0 0-3.58-.9l-.42 2.32c2.85.52 5.31 1.47 5.84 2.06-.32.35-1.82 1.48-8.72 2.91-6.27 1.3-8.68 2.53-8.61 4.39C.06 17 1.8 18 5.83 19c2.46.57 4.87.88 5 .9z"/></g><g class="icon"><path d="M7.82 5L11 8.14 9.22 9.92 6 6.74 2.86 9.92 1.08 8.14 4.26 5 1.08 1.78 2.86 0 6 3.18 9.22 0 11 1.78zm6.83 9.92a2.56 2.56 0 1 0 2.56 2.56 2.56 2.56 0 0 0-2.56-2.6zm-3.55 2.6s-2-.26-4.21-.73A14.89 14.89 0 0 1 2.6 15.4c.55-.37 2.13-1.1 6.49-2 7.6-1.58 10.69-3.1 10.66-5.25 0-1.86-2.44-2.86-4.26-3.45a30.7 30.7 0 0 0-3.58-.9l-.42 2.32c2.85.52 5.31 1.47 5.84 2.06-.32.35-1.82 1.48-8.72 2.91-6.27 1.3-8.68 2.53-8.61 4.39C.06 17 1.8 18 5.83 19c2.46.57 4.87.88 5 .9z"/></g></svg>
            <?php endif; ?>
        </a>
        <a id="<?=$vars['auctionWhiteButton']['id']; ?>" class="layoutButton buttonFramed withIcon round auction green"
           href="hero.php?t=4" title="<?=T("HeroGlobal", "Auctions"); ?>">
            <svg viewBox="0 0 20.18 19.44" class="auction"><g class="outline"><path d="M20 9.44l-6.14 6.16a.54.54 0 0 1-.78 0L11 13.5a.56.56 0 0 1 0-.78l1.64-1.64-.64-.64h-1.24l-7.38 8.7L0 15.76l8.67-7.41V7.13l-.57-.57-.74.75a.49.49 0 0 1-.69 0L4.19 4.83a.49.49 0 0 1 0-.69l4-4a.49.49 0 0 1 .69 0l2.45 2.45a.52.52 0 0 1 0 .74l-.45.46.65.65h3.14v3.14l.73.73 1.75-1.75a.54.54 0 0 1 .78 0L20 8.66a.54.54 0 0 1 0 .78zm-9.35 7v3h9v-3z"/></g><g class="icon"><path d="M20 9.44l-6.14 6.16a.54.54 0 0 1-.78 0L11 13.5a.56.56 0 0 1 0-.78l1.64-1.64-.64-.64h-1.24l-7.38 8.7L0 15.76l8.67-7.41V7.13l-.57-.57-.74.75a.49.49 0 0 1-.69 0L4.19 4.83a.49.49 0 0 1 0-.69l4-4a.49.49 0 0 1 .69 0l2.45 2.45a.52.52 0 0 1 0 .74l-.45.46.65.65h3.14v3.14l.73.73 1.75-1.75a.54.54 0 0 1 .78 0L20 8.66a.54.54 0 0 1 0 .78zm-9.35 7v3h9v-3z"/></g></svg>
        </a>
    </div>
</div>
