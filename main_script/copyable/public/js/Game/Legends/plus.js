/*
 * Travian PLUS dialog in the Travian Legends 655 look (layout/legendsGoldClub.php), port of the travian-T4.6 branch:
 * the feature that was clicked on top (quick links by default), the notice, the other advantages, the duration and
 * "Activate" / "Extend" with the price; activation through ajax.php?cmd=legendsPlus.
 * Replaces the classic Travian.Game.PlusDialog, so every PLUS button of the game ("plusDialog" data of
 * Travian.Game.ButtonEventListener: quick links, building queue, village overview, …) opens it.
 * Also opened by elements with data-plus-feature="<feature>".
 */
var LegendsPlus = (function () {
    var FEATURES = ['directLinks', 'attackWarning', 'buildingQueue', 'linkList', 'villageStatistics', 'fullScreen', 'tradeMulti'];
    var aliases = {plus: 'directLinks', directLink: 'directLinks', quickLinks: 'directLinks', villageOverview: 'villageStatistics',
        largeMap: 'fullScreen', finishNow: 'buildingQueue'};

    function post(data) {
        data.ajaxToken = window.ajaxToken || '';
        var body = Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&');
        return fetch('ajax.php?cmd=legendsPlus', {method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}, body: body})
            .then(function (r) { return r.json(); })
            .then(function (j) { return j && j.response ? j.response : null; });
    }
    function feature(key, T) {
        var f = (T.features || {})[key] || {title: key, text: ''};
        return '<div class="feature"><div class="featureContent"><div class="featureImage ' + key + '"></div>'
            + '<div class="featureTitle">' + f.title + '</div><div class="featureDescription">' + f.text + '</div></div></div>';
    }
    var opening = false;
    function open(featureKey) {
        var T = window.LegendsPlusTexts;
        if (!T || !window.LegendsDialog || opening) return;
        featureKey = aliases[featureKey] || featureKey;
        if (FEATURES.indexOf(featureKey) === -1) featureKey = 'directLinks';
        opening = true;
        post({action: 'info'}).then(function (r) {
            opening = false;
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
                + '<div class="featureImage bookingScroll"></div>'
                + '<div class="featureDuration">' + (s.unit === 'days' ? T.durationDays : T.durationHours) + ': <b>' + s.duration + '</b></div>'
                + '<div class="featureButton"><button type="button" class="textButtonV2 buttonFramed rectangle withText gold plusActivate"'
                + (s.canSpend ? '' : ' disabled') + '><div>' + (s.active ? T.extend : T.activate) + ' ' + coin
                + '<span class="goldValue">' + s.cost + '</span></div></button></div>'
                + '</div></div></div>'
                + '<p class="plusError" hidden></p></div>';
            var d = LegendsDialog.open({cls: 'paymentShopV5 premiumFeaturePackage premiumFeaturePlus legendsPlusDialog',
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
        }).catch(function () { opening = false; });
    }
    // every PLUS button of the game ("plusDialog" data of Travian.Game.ButtonEventListener)
    function hook() {
        if (window.Travian && Travian.Game) {
            Travian.Game.PlusDialog = function (options) { open(options && options.featureKey); };
        }
    }
    hook();
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', hook);
    document.addEventListener('click', function (ev) {
        var el = ev.target.closest ? ev.target.closest('[data-plus-feature]') : null;
        if (!el) return;
        ev.preventDefault();
        open(el.getAttribute('data-plus-feature'));
    });
    return {open: open};
})();
