<?php /** Travian 4.0 task line (Model\QuestT40): the quest master sign of LayoutT40.php opens it with T40Quest.open(). */ ?>
<span id="t40QuestReward"><?php if ($vars['reward']): ?><span class="newQuestSpeechBubble"></span><?php endif; ?></span>
<script type="text/javascript">
    window.T40Quest = (function () {
        var dialog = null;

        function content() {
            return document.querySelector('.questT40Dialog .questT40');
        }

        function close() {
            if (dialog && content()) {
                dialog.close();
            }
            dialog = null;
        }

        function show(data) {
            if (data.reload) {
                try { sessionStorage.setItem('t40QuestOpen', '1'); } catch (e) {}
                window.location.reload();
                return;
            }
            document.getElementById('t40QuestReward').innerHTML = data.reward ? '<span class="newQuestSpeechBubble"></span>' : '';
            if (data.declined) {
                close();
                jQuery('.questMaster').remove();
                return;
            }
            if (content()) {
                jQuery(content()).replaceWith(data.html);
            } else {
                dialog = new Travian.Dialog.Dialog({cssClass: 'white questInformation questT40Dialog', buttonOk: false});
                dialog.setContent(data.html);
                dialog.show();
            }
        }

        function call(params) {
            Travian.ajax({data: jQuery.extend({cmd: 'questT40'}, params), onSuccess: show});
        }

        // the dialog already sits in a form, so the answer fields are no form of their own
        function answer(box) {
            var params = {action: 'answer'};
            jQuery(box).find('input').each(function () { params[this.name] = this.value; });
            call(params);
        }

        jQuery(document).on('click', '.questT40 [data-t40-action]', function (event) {
            event.preventDefault();
            var action = this.getAttribute('data-t40-action');
            if (action === 'close') {
                close();
            } else if (action === 'answer') {
                answer(jQuery(this).closest('.answer'));
            } else {
                call(jQuery.extend({action: action}, jQuery(this).data()));
            }
        });
        jQuery(document).on('keydown', '.questT40 .answer input', function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                answer(jQuery(this).closest('.answer'));
            }
        });

        jQuery(function () {
            var reopen = false;
            try { reopen = sessionStorage.getItem('t40QuestOpen') === '1'; sessionStorage.removeItem('t40QuestOpen'); } catch (e) {}
            <?php if ($vars['welcome']): ?>
            // 4.0 greets a new chieftain with the task master's welcome (once per visit when he looks around first)
            try { if (!sessionStorage.getItem('t40QuestWelcome')) { sessionStorage.setItem('t40QuestWelcome', '1'); reopen = true; } } catch (e) {}
            <?php endif; ?>
            if (reopen) {
                call({action: 'view'});
            }
        });

        return {
            open: function (task) {
                call({action: 'view', task: task || 0});
            }
        };
    })();
</script>
