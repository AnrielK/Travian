<?php
/** The red marks of the T4.4 tutorial in the T4.4 looks. Included by the page frame of those looks (Layout.php). */
?>
<style type="text/css">
    /* the red disc behind a highlighted button is drawn for the T4.4 button sizes: centre it under ours */
    .highlighted.background {
        background-position: center !important;
        background-repeat: no-repeat !important;
    }
</style>
<script type="text/javascript">
    (function () {
        var Image = window.Travian && Travian.Game && Travian.Game.Highlight && Travian.Game.Highlight.Renderer.Image;
        if (!Image) {
            return;
        }
        // The game places the disc by the position and margins of the T4.4 markup, so behind the T4.6 buttons it sat
        // a few pixels off (navigation, panel buttons, the close cross). Place it by where the button really is.
        Travian.Game.Highlight.Renderer.Image = function (options, highlight) {
            if (highlight.getElement().is('svg')) {
                // the outline drawn over an empty building place also answers to .iso: the place's picture gets
                // the disc, and the outline must not stop the marks of the places after it
                Travian.Game.Highlight.Renderer.Renderer.call(this, highlight);
                this.activate = this.deactivate = function () {
                    return this;
                };
                return;
            }
            Image.call(this, options, highlight);
            this.activate = function () {
                var button = this.getElement(), disc = this.getElementBackground(), around = this.options.borderSurround;
                disc.css({
                    position: 'absolute', margin: 0, padding: 0, border: 0, boxSizing: 'border-box',
                    left: 0, top: 0, right: 'auto', bottom: 'auto',
                    width: button.outerWidth() + 2 * around, height: button.outerHeight() + 2 * around
                }).show();
                var at = button.offset(), origin = disc.offset();
                disc.css({left: at.left - origin.left - around, top: at.top - origin.top - around});
                return this;
            };
        };
        Travian.Game.Highlight.Renderer.Image.prototype = Image.prototype;

        jQuery(function () {
            var redraw = function () {
                var quest = Travian.Game.Quest;
                if (quest && quest.highlightsToggle === true && quest.options && quest.options.highlightSelectors) {
                    quest.createHighlights();
                }
            };
            // opening a panel shows what the next mark points at (the hero's resource choice behind the closed
            // attributes panel)
            jQuery(document).on('click', '.openedClosedSwitch, .openCloseSwitchBar', function () {
                setTimeout(redraw, 0);
            });
            // the save button is marked once clay is picked
            jQuery(document).on('change', '#resourceHero2', function () {
                jQuery('#saveHeroAttributes').addClass('clayClicked');
                redraw();
            });
        });
    })();
</script>
