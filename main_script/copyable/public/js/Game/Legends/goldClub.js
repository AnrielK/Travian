/*
 * Gold Club ("Bonifácův klub") dialog in the Travian Legends 655 look (layout/legendsGoldClub.php): the feature that was
 * clicked on top, the notice, the other advantages, the duration and "Activate" with the price; activation through
 * ajax.php?cmd=legendsGoldClub. Replaces the classic Travian.Game.GoldclubDialog, so every Gold Club button of the game
 * (rally point evasion and farm list, crop finder, archives, trade routes) opens it.
 * Also opened by elements with data-goldclub-feature="<feature>".
 */
var LegendsGoldClub = (function () {
    var FEATURES = ['troopEscape', 'raidList', 'tradeThreeTimes', 'tradeRoute', 'cropFinder', 'messageArchive'];
    var aliases = {farmList: 'raidList', farmlist: 'raidList', evasion: 'troopEscape', archive: 'messageArchive', tradeRoutes: 'tradeRoute'};

    function post(data) {
        data.ajaxToken = window.ajaxToken || '';
        var body = Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&');
        return fetch('ajax.php?cmd=legendsGoldClub', {method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: body})
            .then(function (r) { return r.json(); })
            .then(function (j) { return j && j.response ? j.response : null; });
    }
    function feature(key, T) {
        var f = (T.features || {})[key] || {title: key, text: ''};
        return '<div class="feature"><div class="featureContent"><div class="featureImage ' + key + '"></div>'
            + '<div class="featureTitle">' + f.title + '</div><div class="featureDescription">' + f.text + '</div></div></div>';
    }
    function open(featureKey) {
        var T = window.LegendsGoldClubTexts;
        if (!T || !window.LegendsDialog) return;
        featureKey = aliases[featureKey] || featureKey;
        if (FEATURES.indexOf(featureKey) === -1) featureKey = 'raidList';
        post({action: 'info'}).then(function (r) {
            var s = r && !r.error ? r.data : null;
            if (!s) return;
            var coin = T.goldCoin || '';
            var others = FEATURES.filter(function (k) { return k !== featureKey; });
            var html = '<div class="featureCollection">'
                + '<div class="packageFeatures mainFeature">' + feature(featureKey, T) + '</div>'
                + '<div class="plusNotice">' + (s.active ? T.active : T.notice) + '</div>'
                + '<div class="plusExtra">' + T.extra + '</div>'
                + '<div class="packageFeatures">' + others.map(function (k) { return feature(k, T); }).join('') + '</div>'
                + '<div class="featureBooking"><div class="feature"><div class="featureContent">'
                + '<div class="featureImage goldclubDuration"></div>'
                + '<div class="featureDuration">' + T.duration + ': <b>' + T.durationRound + '</b></div>'
                + (s.active ? '' : '<div class="featureButton"><button type="button" class="textButtonV2 buttonFramed rectangle withText gold plusActivate"'
                    + (s.canSpend ? '' : ' disabled') + '><div>' + T.activate + ' ' + coin + '<span class="goldValue">' + s.cost + '</span></div></button></div>')
                + '</div></div></div>'
                + '<p class="plusError" hidden></p></div>';
            var d = LegendsDialog.open({cls: 'paymentShopV5 premiumFeaturePackage premiumFeatureGoldclub legendsPlusDialog legendsGoldClubDialog',
                title: T.title, html: html, closeText: T.close,
                header: '<div class="dialogGold" title="' + T.gold + '">' + coin + '<span>' + s.gold + '</span></div>'});
            var btn = d.querySelector('.plusActivate');
            if (!btn) return;
            btn.addEventListener('click', function () {
                btn.disabled = true;
                post({action: 'activate'}).then(function (a) {
                    if (a && !a.error && a.data && a.data.activated) { location.reload(); return; }
                    var err = d.querySelector('.plusError');
                    err.textContent = a && a.error ? a.errorMsg : T.notEnough;
                    err.hidden = false;
                    btn.disabled = false;
                });
            });
        });
    }
    // every Gold Club button of the game ("goldclubDialog" data of Travian.Game.ButtonEventListener)
    function hook() {
        if (window.Travian && Travian.Game) {
            Travian.Game.GoldclubDialog = function (options) { open(options && options.featureKey); };
        }
    }
    hook();
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', hook);
    document.addEventListener('click', function (ev) {
        var el = ev.target.closest ? ev.target.closest('[data-goldclub-feature]') : null;
        if (!el) return;
        ev.preventDefault();
        open(el.getAttribute('data-goldclub-feature'));
    });
    return {open: open};
})();
