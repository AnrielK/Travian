/*
 * Rename the active village in place, as in Travian Legends (layout/sidebarBoxActiveVillages.php): a tap anywhere on the
 * framed name field (or on its pencil) turns the name into a text field inside the same frame; Enter or leaving the
 * field saves it (ajax.php?cmd=changeVillageName, like the game's own rename dialog), Escape keeps the old name.
 * Replaces the rename dialog of the game for the Legends look.
 */
(function () {
    function init() {
        var box = document.getElementById('sidebarBoxActiveVillage');
        var field = document.getElementById('villageNameField');
        if (!box || !field || !field.hasAttribute('data-did')) return;
        var pencil = box.querySelector('.innerBox.footer .layoutButton.edit');
        var did = field.getAttribute('data-did');
        var editing = false;

        function setName(html) {
            field.innerHTML = html;
            // the village list shows the new name too
            var row = document.querySelector('#sidebarBoxVillagelist .listEntry.village[data-kid="' + did + '"] .name');
            if (row) row.innerHTML = html;
        }
        function start(ev) {
            if (ev) { ev.preventDefault(); ev.stopImmediatePropagation(); }
            if (editing) return;
            editing = true;
            var oldHtml = field.innerHTML;
            var tmp = document.createElement('textarea');
            tmp.innerHTML = oldHtml;
            var oldName = tmp.value.trim();
            var input = document.createElement('input');
            input.type = 'text';
            input.className = 'villageNameInput';
            input.maxLength = 20;
            input.value = oldName;
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('autocorrect', 'off');
            input.setAttribute('spellcheck', 'false');
            field.innerHTML = '';
            field.appendChild(input);
            field.classList.add('editing');
            box.classList.add('renamingVillage');
            input.focus();
            try { input.setSelectionRange(input.value.length, input.value.length); } catch (e) {}
            var done = false;
            function finish(html) {
                if (done) return;
                done = true;
                editing = false;
                field.classList.remove('editing');
                box.classList.remove('renamingVillage');
                setName(html);
            }
            function save() {
                if (done) return;
                var name = input.value.replace(/\s+/g, ' ').trim();
                if (!name || name === oldName || !window.Travian || !Travian.ajax) { finish(oldHtml); return; }
                input.disabled = true;
                Travian.ajax({
                    data: {cmd: 'changeVillageName', name: name, did: did},
                    onSuccess: function (r) { finish(r && r.name ? r.name : oldHtml); },
                    onFailure: function () { finish(oldHtml); }
                });
                // the game's ajax shows its own error message; never leave the field disabled
                setTimeout(function () { if (!done) finish(oldHtml); }, 15000);
            }
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); save(); }
                else if (e.key === 'Escape') { e.preventDefault(); finish(oldHtml); }
            });
            input.addEventListener('blur', save);
            input.addEventListener('click', function (e) { e.stopPropagation(); });
        }
        // capture on the box: runs before the game's own handler of the pencil (its rename dialog)
        box.addEventListener('click', function (ev) {
            if (ev.target.closest('input.villageNameInput')) return;
            if (ev.target.closest('#villageNameField') || (pencil && pencil.contains(ev.target))) start(ev);
        }, true);
        if (pencil) pencil.addEventListener('mousedown', function (e) { if (editing) e.preventDefault(); }, true);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
