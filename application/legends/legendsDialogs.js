/*
 * Dialogs of the Travian Legends look (DialogV2 markup styled by legends_ltr.css), loaded on every page with the
 * legends655 pack (application/Skin.php):
 *   LegendsDialog.open({cls, title, html, modal, onClose}) / close(el) / confirm(text, cancel, ok, cb) / alert(text)
 *   LegendsPlus.open(featureKey) – the Travian PLUS dialog; it also replaces the classic PLUS dialog of the game
 *   ("plusDialog" buttons).
 * Texts: window.LegendsTexts (Skin.php).
 */
var LegendsDialog = (function () {
    var X = '<svg viewBox="0 0 20 20"><g class="outline"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"></path></g>'
        + '<g class="icon"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"></path></g></svg>';

    function open(o) {
        var overlay = document.createElement('div');
        overlay.className = 'dialogOverlay legendsDialogOverlay' + (o.modal === false ? '' : ' enabled');
        overlay.innerHTML = '<div class="dialogWrapper dialogV2"><div class="dialog ' + (o.cls || '') + '"><div class="dialogContainer"><div class="dialogContents">'
            + (o.title ? '<h1 class="titleInHeader">' + o.title + '</h1>' : '')
            + '<form action="#" onsubmit="return false;">'
            + (o.header || '')
            + (o.closable === false ? '' : '<a class="dialogCancelButton iconButton buttonFramed green withIcon rectangle cancel" href="#" title="' + ((window.LegendsTexts || {}).close || 'Close') + '">' + X + '</a>')
            + (o.heading ? '<h2 class="dialogHeading">' + o.heading + '</h2>' : '')
            + '<div class="content" id="dialogContent">' + (o.html || '') + '</div>'
            + (o.buttons ? '<div class="buttons">' + o.buttons + '</div>' : '') + '</form>'
            + '</div></div></div></div>';
        overlay._onClose = o.onClose;
        document.body.appendChild(overlay);
        var cancel = overlay.querySelector('.dialogCancelButton');
        if (cancel) cancel.addEventListener('click', function (ev) { ev.preventDefault(); close(overlay); });
        if (o.modal !== false) {
            overlay.addEventListener('click', function (ev) { if (ev.target === overlay && !o.basic) close(overlay); });
        }
        requestAnimationFrame(function () { overlay.classList.add('dialogVisible'); });
        return overlay;
    }
    function close(overlay) {
        if (!overlay || !overlay.parentNode) return;
        overlay.parentNode.removeChild(overlay);
        if (overlay._onClose) overlay._onClose();
    }
    function button(text, cls) {
        return '<button type="button" class="textButtonV2 buttonFramed rectangle withText ' + cls + '"><div>' + text + '</div></button>';
    }
    function confirm(text, cancelText, okText, cb) {
        var d = open({cls: 'basic legendsConfirm', basic: true, closable: false, html: '<p class="confirmText">' + text + '</p>',
            buttons: button(cancelText, 'grey cancelAction') + button(okText, 'red okAction')});
        d.querySelector('.cancelAction').addEventListener('click', function () { close(d); cb(false); });
        d.querySelector('.okAction').addEventListener('click', function () { close(d); cb(true); });
        return d;
    }
    function alert(text) {
        var d = open({cls: 'basic legendsConfirm', basic: true, closable: false, html: '<p class="confirmText">' + text + '</p>', buttons: button('OK', 'green okAction')});
        d.querySelector('.okAction').addEventListener('click', function () { close(d); });
        return d;
    }
    return {open: open, close: close, confirm: confirm, alert: alert};
})();

var LegendsPlus = (function () {
    var FEATURES = ['directLinks', 'attackWarning', 'buildingQueue', 'linkList', 'villageStatistics', 'fullScreen', 'tradeMulti'];
    var aliases = {plus: 'directLinks', directLink: 'directLinks', villageOverview: 'villageStatistics', largeMap: 'fullScreen'};

    function post(data) {
        var body = Object.keys(data).map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]); }).join('&');
        return fetch('ajax.php?cmd=legendsPlus', {method: 'POST', credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: body})
            .then(function (r) { return r.json(); }).then(function (j) { return j && j.response ? j.response.data : null; });
    }
    function feature(key, T) {
        var f = T.features[key] || {title: key, text: ''};
        return '<div class="feature"><div class="featureContent"><div class="featureImage ' + key + '"></div>'
            + '<div class="featureTitle">' + f.title + '</div><div class="featureDescription">' + f.text + '</div></div></div>';
    }
    function open(featureKey) {
        var T = (window.LegendsTexts || {}).plus;
        if (!T) return;
        featureKey = aliases[featureKey] || featureKey;
        if (FEATURES.indexOf(featureKey) === -1) featureKey = 'directLinks';
        post({action: 'info'}).then(function (s) {
            if (!s) return;
            var coin = (window.LegendsTexts || {}).goldCoin || '';
            var others = FEATURES.filter(function (k) { return k !== featureKey; });
            var html = '<div class="featureCollection">'
                + '<div class="packageFeatures mainFeature">' + feature(featureKey, T) + '</div>'
                + (s.active ? '' : '<div class="plusNotice">' + T.notice + '</div>')
                + '<div class="plusExtra">' + T.extra + '</div>'
                + '<div class="packageFeatures">' + others.map(function (k) { return feature(k, T); }).join('') + '</div>'
                + '<div class="featureBooking"><div class="feature"><div class="featureContent">'
                + '<div class="featureImage bookingScroll"></div>'
                + '<div class="featureDuration">' + (s.unit === 'days' ? T.durationDays : T.durationHours) + ': <b>' + s.duration + '</b></div>'
                + '<div class="featureButton"><button type="button" class="textButtonV2 buttonFramed rectangle withText gold plusActivate"><div>'
                + (s.active ? T.extend : T.activate) + ' ' + coin + '<span class="goldValue">' + s.cost + '</span></div></button></div>'
                + '</div></div></div>'
                + '<p class="plusError" hidden></p></div>';
            var d = LegendsDialog.open({cls: 'paymentShopV5 premiumFeaturePackage premiumFeaturePlus legendsPlusDialog', title: T.title, html: html,
                header: '<div class="dialogGold" title="' + T.gold + '">' + coin + '<span>' + s.gold + '</span></div>'});
            var btn = d.querySelector('.plusActivate');
            btn.addEventListener('click', function () {
                btn.disabled = true;
                post({action: 'activate'}).then(function (r) {
                    if (r && r.activated) { location.reload(); return; }
                    var err = d.querySelector('.plusError');
                    err.textContent = T.notEnough;
                    err.hidden = false;
                    btn.disabled = false;
                });
            });
        });
    }
    // the game's own PLUS dialogs ("plusDialog" on a button, the ajax plusPopup) open this one instead
    function hookGame() {
        if (window.fireEvent && !window.__legendsPlusHook) {
            window.__legendsPlusHook = true;
            var orig = window.fireEvent;
            window.fireEvent = function (type, args) {
                if (type === 'buttonClicked' && args && args[1] && args[1].plusDialog) {
                    open(args[1].plusDialog.featureKey || 'directLinks');
                    return this;
                }
                return orig.apply(this, arguments);
            };
        }
    }
    hookGame();
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', hookGame);
    return {open: open};
})();
