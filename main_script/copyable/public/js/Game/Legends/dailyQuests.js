/*
 * Daily quests of the Travian Legends look in a dialog, as on Travian (dialog "basic dailyQuestsDialog" below the
 * header, layout/legendsDailyQuests.php): the daily quests button of the navigation opens it with the content of
 * dailyquests.php?dialog=1 (DailyQuestsCtrl), a quest opens its details, "Back" the list again. Collecting the
 * rewards reloads the page (resources in the header) and opens the dialog again. On dailyquests.php itself the
 * links (data-href) are followed as pages.
 */
(function () {
    var REOPEN = 'ovDailyQuestsDialog';
    var dialog = null;

    function load(url) {
        return fetch(url + (url.indexOf('?') === -1 ? '?' : '&') + 'dialog=1',
            {credentials: 'same-origin', headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(function (r) { return r.ok ? r.text() : null; })
            .catch(function () { return null; });
    }
    function show(url) {
        return load(url).then(function (html) {
            if (!html) {
                window.location.href = url;
                return;
            }
            if (dialog && dialog.parentNode) {
                dialog.querySelector('#dialogContent').innerHTML = html;
                return;
            }
            dialog = window.LegendsDialog.open({cls: 'basic dailyQuestsDialog', overlayCls: 'doNotCoverHeader stickToHeader', html: html,
                closeText: (window.LegendsDailyQuestsTexts || {}).close});
        });
    }

    document.addEventListener('click', function (ev) {
        if (!ev.target.closest) return;
        if (ev.target.closest('#navigation a.dailyQuests') && window.LegendsDialog) {
            ev.preventDefault();
            show('dailyquests.php');
            return;
        }
        var link = ev.target.closest('#dailyQuests [data-href]');
        if (!link || link.disabled) return;
        ev.preventDefault();
        var href = link.getAttribute('data-href');
        if (!link.closest('.dailyQuestsDialog')) {
            window.location.href = href;
            return;
        }
        if (href.indexOf('collect=1') !== -1) {
            link.disabled = true;
            load(href).then(function () {
                try { sessionStorage.setItem(REOPEN, '1'); } catch (e) {}
                window.location.reload();
            });
            return;
        }
        show(href);
    });

    try {
        if (sessionStorage.getItem(REOPEN)) {
            sessionStorage.removeItem(REOPEN);
            show('dailyquests.php');
        }
    } catch (e) {}
})();
