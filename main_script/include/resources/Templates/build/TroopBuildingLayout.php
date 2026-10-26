<?php if($vars['canTrain']):?>
    <?php if($vars['item_id'] != 36):?>
        <h4 class="round"><?=T("inGame", "train_troops");?></h4>
    <?php else:?>
        <?=$vars['trap_desc'];?>
    <?php endif;?>
    <?php $isResidenceOrPalace = $vars['item_id'] == 25 || $vars['item_id'] == 26; ?>
    <form method="post" name="snd" action="build.php?id=<?=$vars['index'];?><?=$isResidenceOrPalace ? '&s=1' : '';?>">
        <input type="hidden" name="id" value="<?=$vars['index'];?>"/>
        <input type="hidden" name="z" value="<?=$vars['checker'];?>"/>
        <input type="hidden" name="a" value="2"/>
        <?php if($vars['item_id'] != 36):?>
            <input type="hidden" name="s" value="1"/>
        <?php endif;?>
        <div class="buildActionOverview trainUnits">
            <?=$vars['wrappers'];?>
        </div>
        <button type="submit" value="ok" name="s1" id="s1" class="green startTraining">
            <div class="button-container addHoverClick">
                <div class="button-background">
                    <div class="buttonStart">
                        <div class="buttonEnd">
                            <div class="buttonMiddle"></div>
                        </div>
                    </div>
                </div>
                <div class="button-content"><?=T("inGame", "train");?></div>
            </div>
        </button>
        <script type="text/javascript">
            jQuery(function() {
                    jQuery('#s1').click(function (event) {
                        jQuery(window).trigger('buttonClicked', [this, {
                            "type": "submit",
                            "value": "ok",
                            "name": "s1",
                            "id": "s1",
                            "class": "green startTraining",
                            "title": "",
                            "confirm": "",
                            "onclick": ""
                        }]);
                    });
            });
        </script>
        <?=$vars['finishTraining_button'];?>
    </form>
    <script type="text/javascript">
        // The star of a unit (as in Travian Legends): marked units are listed first, in their usual order; the card
        // slides to its new place. The choice is kept for the player (ajax unitFavorite).
        jQuery(function () {
            var list = jQuery('.trainUnits');
            list.on('click', '.ovFavorite', function () {
                var star = jQuery(this), card = star.closest('.ovTrainCard'), favorite = !card.hasClass('favorite');
                card.toggleClass('favorite', favorite);
                star.toggleClass('active', favorite).attr('title', star.data(favorite ? 'remove' : 'add'));
                Travian.ajax({data: {cmd: 'unitFavorite', unitId: card.data('unit'), favorite: favorite ? 1 : 0}});

                var cards = list.children('.ovTrainCard'), before = new Map();
                cards.each(function () {
                    before.set(this, this.getBoundingClientRect().top);
                });
                cards.sort(function (a, b) {
                    var fa = jQuery(a).hasClass('favorite'), fb = jQuery(b).hasClass('favorite');
                    return fa !== fb ? (fa ? -1 : 1) : jQuery(a).data('order') - jQuery(b).data('order');
                }).appendTo(list);
                cards.removeClass('first').first().addClass('first');
                cards.each(function () {
                    var shift = before.get(this) - this.getBoundingClientRect().top;
                    if (!shift) {
                        return;
                    }
                    var moved = this;
                    moved.style.transition = 'none';
                    moved.style.transform = 'translateY(' + shift + 'px)';
                    moved.getBoundingClientRect();
                    moved.style.transition = 'transform .45s ease';
                    moved.style.transform = '';
                    jQuery(moved).toggleClass('moving', moved === card[0]);
                    setTimeout(function () {
                        moved.style.transition = '';
                        jQuery(moved).removeClass('moving');
                    }, 460);
                });
            });
        });
    </script>
<?php elseif( $vars['item_id'] != 25 and $vars['item_id'] != 26):?>
    <h4 class="round"><?=T("inGame", "train_troops");?></h4>
    <span class="errorMessage"><?=T("inGame", "noTroops");?></span>
<?php else:?>
    <?=T("inGame", $vars['item_id'] == 25 ? 'residenceNoTrainText' : 'palaceNoTrainText');?>
<?php endif;?>
<?php if($vars['isTraining']):?>
    <h4 class="round spacer"><?=T("inGame", $vars['item_id'] == 36 ? "in_creating" : 'in_training');?></h4>
    <table cellpadding="1" cellspacing="1" class="under_progress">
        <thead>
        <tr>
            <td><?=T("inGame", "Amount");?></td>
            <td><?=T("Global", "General.duration");?></td>
            <td><?=T("Global", "General.endat");?></td>
        </tr>
        </thead>
        <tbody>
        <?=$vars['training'];?>
        </tbody>
    </table>
<?php endif;?>