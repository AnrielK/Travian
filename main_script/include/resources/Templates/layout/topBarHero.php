<?php
/*
 * Hero portrait in the top bar (graphic pack 29c89d54 / Travian Legends layout).
 * Same data as layout/sidebarBoxHero.php, rendered by GameView::renderHeroBox().
 */
use Game\Hero\SessionHero;

$health = (int)round($vars['regenerating'] ? $vars['regeneratedHealth'] : $vars['health']);
$health = max(0, min(100, $health));
$xp = max(0, min(100, (int)round($vars['expPercent'])));
// Both bars are arcs on the left and right half of the portrait ring (r = 50, half circumference ≈ 157).
$half = 157.08;
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
                <path d="M55 105 A50 50 0 0 1 55 5" fill="none" stroke="#3b2a1e" stroke-width="7"/>
                <path d="M55 105 A50 50 0 0 1 55 5" fill="none" stroke="<?=$vars['regenerating'] ? '#d9a13a' : '#76b82a'; ?>" stroke-width="5"
                      stroke-dasharray="<?=round($half * $health / 100, 2); ?> 400"/>
            </svg>
        </div>
        <div class="experience" title="<?=T("HeroGlobal", "Experience"); ?>: <?=$vars['exp']; ?>">
            <svg width="110" height="110" viewBox="0 0 110 110">
                <path d="M55 105 A50 50 0 0 0 55 5" fill="none" stroke="#3b2a1e" stroke-width="7"/>
                <path d="M55 105 A50 50 0 0 0 55 5" fill="none" stroke="#5aa6d6" stroke-width="5"
                      stroke-dasharray="<?=round($half * $xp / 100, 2); ?> 400"/>
            </svg>
        </div>
        <a id="heroImageButton" class="heroImageButton"
           href="hero.php<?=$vars['hasNewPoints'] ? "?flagAttributesBoxOpen" : ''; ?>"
           title="<?=T("HeroGlobal", "HeroOverview"); ?>||<?=htmlspecialchars($vars['longStatus']); ?>">
            <div class="heroImageHover">
                <img class="heroImage heroImageMale"
                     src="hero_head.php?uid=<?=$vars['uid']; ?>&amp;size=sideinfo&amp;<?=$vars['heroImageHash']; ?>"
                     alt="Hero"/>
            </div>
        </a>
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
        <?php if ($vars['lvlUp'] || $vars['hasNewPoints']): ?>
            <a class="levelUp show" href="hero.php?flagAttributesBoxOpen" title="<?=T("HeroGlobal", "HeroOverview"); ?>"></a>
        <?php endif; ?>
        <a id="<?=$vars['adventureWhiteButton']['id']; ?>" class="layoutButton buttonFramed withIcon round adventure green<?=$vars['adventureWhiteButton']['adventureCount'] ? ' attention' : ''; ?>"
           href="hero.php?t=3" title="<?=T("HeroGlobal", "Adventure"); ?>">
            <?php if ($vars['adventureWhiteButton']['adventureCount']): ?>
                <div class="content"><?=$vars['adventureWhiteButton']['adventureCount']; ?></div>
            <?php else: ?>
                <svg viewBox="0 0 20 20"><path class="outline" d="M10 1a6 6 0 0 0-6 6c0 4.5 6 12 6 12s6-7.5 6-12a6 6 0 0 0-6-6zm0 8.5A2.5 2.5 0 1 1 10 4.5a2.5 2.5 0 0 1 0 5z"/></svg>
            <?php endif; ?>
        </a>
        <a id="<?=$vars['auctionWhiteButton']['id']; ?>" class="layoutButton buttonFramed withIcon round auction green"
           href="hero.php?t=4" title="<?=T("HeroGlobal", "Auctions"); ?>">
            <svg viewBox="0 0 20 20"><path class="outline" d="M12.5 1.5 18.5 7.5 16 10 10 4zM9 5l6 6-2 2-6-6zM8 10l2 2-7 7-2-2z"/></svg>
        </a>
    </div>
</div>
