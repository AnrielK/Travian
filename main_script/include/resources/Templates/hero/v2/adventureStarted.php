<?php
/* Adventure picture with the hero's state (StartAdventure in the Travian Legends look). */

use resources\View\LegendsUi as UI;

$e = [UI::class, 'e'];
?>
<div class="contentV2">
    <div class="adventureStarted <?=$e($vars['place']); ?><?=$vars['canStart'] ? '' : ' sent'; ?>">
        <div class="heroState">
            <span class="heroStatusMessage"><img src="img/x.gif" class="heroStatus<?=(int)$vars['status']; ?>" alt=""></span>
            <div><?=$vars['statusMessage']; ?></div>
        </div>
        <?php if ($vars['canStart']): ?>
            <div class="adventureArrival"><?=$vars['arrival']; ?></div>
            <form method="post" action="start_adventure.php">
                <input type="hidden" name="send" value="1">
                <input type="hidden" name="kid" value="<?=(int)$vars['kid']; ?>">
                <input type="hidden" name="from" value="list">
                <input type="hidden" name="a" value="1">
                <?=UI::button($e(T('HeroV2', 'explore', 'Explore')), ['type' => 'submit']); ?>
            </form>
        <?php else: ?>
            <?=UI::button($e(T('HeroV2', 'continue', 'Continue')), ['onclick' => "window.location.href='hero.php?t=3'"]); ?>
        <?php endif; ?>
    </div>
</div>
