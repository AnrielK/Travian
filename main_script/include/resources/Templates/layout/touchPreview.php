<?php
/** Touch screens (the iOS app): the first tap on a resource field or building shows its tooltip (upgrade costs and
 * time) and its light, the second tap opens it. Included by the page frames of the T4.4 looks (Layout.php, LayoutT40.php). */
?>
<script type="text/javascript">
    // Touch screens have no hover, so the tooltip with the upgrade costs of a field or building never showed: the
    // first tap on a field or building only shows its tooltip and light, a second tap on it opens it.
    (function () {
        var touch = false, shown = null;
        document.addEventListener('pointerdown', function (event) {
            touch = event.pointerType === 'touch' || event.pointerType === 'pen';
        }, true);
        document.addEventListener('click', function (event) {
            if (!touch || !event.target.closest) {
                return;
            }
            var target = event.target.closest('#rx area[data-index], #village_map .buildingSlot path, #village_map .buildingSlot .level');
            var slot = target && (target.closest('.buildingSlot') || target);
            if (!slot || slot === shown) {
                shown = null;
                jQuery('#village_map svg.buildingShape.preview').removeClass('preview hover');
                return;
            }
            event.preventDefault();
            event.stopImmediatePropagation();
            if (shown && shown !== slot) {
                ['mouseout', 'mouseleave'].forEach(function (type) {
                    jQuery(shown).find('path').addBack('area').each(function () {
                        this.dispatchEvent(new MouseEvent(type, {bubbles: type === 'mouseout', view: window}));
                    });
                });
            }
            shown = slot;
            // the light of a building: its outline takes the class the hover styles also answer to
            jQuery('#village_map svg.buildingShape.preview').removeClass('preview hover');
            jQuery(slot).find('svg.buildingShape').addClass('preview hover');
            ['mouseover', 'mouseenter', 'mousemove'].forEach(function (type) {
                target.dispatchEvent(new MouseEvent(type, {
                    bubbles: type !== 'mouseenter', view: window, clientX: event.clientX, clientY: event.clientY
                }));
            });
        }, true);
    })();
</script>
