/*
 * Exchange office of the auctions in the Travian Legends 692.2 look (dialog "basic exchangeOfficeDialog", markup of
 * #exchangeOffice styled by legends_ltr.css): gold to silver (1 : 100) or silver to gold (200 : 1), the swap button in
 * the middle changes the direction; the preview shows the current and the new gold and silver. "Exchange" posts
 * ajax.php?cmd=premiumFeature (featureKey exchangeSilver) or ajax.php?cmd=silverExchange and shows the new balance.
 * Opened by the exchange widget of the auctions (data-exchange="GoldToSilver" / "SilverToGold" / "open").
 * Texts and icons: window.LegendsExchangeTexts (HeroAuctionCtrl).
 */
var LegendsExchange = (function () {
    var RATE = {GoldToSilver: [1, 100], SilverToGold: [200, 1]};
    var LINE = '<svg viewBox="0 0 380 10"><path d="M0 0L186 0L190 3L194 0L380 0"></path><path d="M186 0L190 3L194 0z"></path></svg>';
    var ARROW = '<svg viewBox="0 0 16.5 7"><path d="M13 0v2H0v3h13v2l3.5-3.5"></path></svg>';
    var dialog = null;

    function fmt(n) {
        return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
    function coin(T, kind) {
        return kind === 'gold' ? T.goldCoin : T.silverCoin;
    }
    function render(T, direction, value) {
        var toSilver = direction === 'GoldToSilver';
        var from = toSilver ? 'gold' : 'silver', to = toSilver ? 'silver' : 'gold';
        var button = function (kind) {
            return '<button class="' + (kind === 'gold' ? 'exchangeGold' : 'exchangeSilver') + '" type="button"><div class="'
                + (kind === 'gold' ? 'goldCoin' : 'silverCoin') + '">' + coin(T, kind) + '</div><span>' + (kind === 'gold' ? T.gold : T.silver) + '</span></button>';
        };
        return '<div id="exchangeOffice"><h3>' + T.title + '</h3>'
            + '<div class="changeDirection" data-direction="' + direction + '">' + button(from)
            + '<button class="textButtonV2 buttonFramed openExchangeOffice round withIcon green" type="button" title="' + T.swap + '">' + T.exchangeIcon + '</button>'
            + button(to) + '</div>'
            + '<div class="exchangeRate"><span>' + RATE[direction][0] + '</span><span>:</span><span>' + RATE[direction][1] + '</span></div>'
            + '<div class="formV2 valueInput">' + LINE + '<label class="input fixed"><input inputmode="numeric" maxlength="6" class="exchangeValue" type="text" value="'
            + value + '">' + coin(T, from) + '</label><span class="equals">=</span><div class="result">' + coin(T, to) + '<span></span></div>' + LINE + '</div>'
            + '<table class="preview transparent"><thead><tr><td colspan="2" class="current">' + T.current + '</td><td></td><td colspan="2" class="new">' + T.next
            + '</td></tr></thead><tbody>'
            + '<tr><td>' + T.goldCoin + '</td><td class="current">' + fmt(T.goldAmount) + '</td><td class="arrow">' + ARROW + '</td><td>' + T.goldCoin + '</td><td class="new newGold"></td></tr>'
            + '<tr><td>' + T.silverCoin + '</td><td class="current">' + fmt(T.silverAmount) + '</td><td class="arrow">' + ARROW + '</td><td>' + T.silverCoin + '</td><td class="new newSilver"></td></tr>'
            + '</tbody></table>'
            + (toSilver
                ? '<button class="textButtonV2 buttonFramed exchange rectangle withText gold" type="submit"><div>' + T.exchange + T.goldCoin.replace('<svg', '<svg class="goldCoin"') + '&nbsp;<span class="goldValue"></span></div></button>'
                : '<button class="textButtonV2 buttonFramed exchange rectangle withText green" type="submit"><div>' + T.exchange + '</div></button>')
            + '</div>';
    }
    // Result, preview and button for the entered amount; the button is disabled for an amount that cannot be exchanged.
    function update(T, root) {
        var direction = root.querySelector('.changeDirection').getAttribute('data-direction');
        var input = root.querySelector('.exchangeValue');
        var value = parseInt(input.value.replace(/[^0-9]/g, ''), 10) || 0;
        var gold = T.goldAmount, silver = T.silverAmount, result, ok;
        if (direction === 'GoldToSilver') {
            result = value * 100;
            ok = value >= 1 && value <= gold;
            gold -= value;
            silver += result;
            var price = root.querySelector('.exchange .goldValue');
            if (price) price.textContent = fmt(value);
        } else {
            result = Math.floor(value / 200);
            ok = result >= 1 && value <= silver;
            gold += result;
            silver -= result * 200;
        }
        root.querySelector('.result span').textContent = fmt(result);
        root.querySelector('.newGold').textContent = fmt(gold);
        root.querySelector('.newSilver').textContent = fmt(silver);
        var button = root.querySelector('button.exchange');
        button.disabled = !ok;
        button.classList.toggle('disabled', !ok);
    }
    function show(direction, value) {
        var T = window.LegendsExchangeTexts;
        var html = render(T, direction, value);
        if (dialog && dialog.parentNode) {
            dialog.querySelector('#dialogContent').innerHTML = html;
        } else {
            dialog = window.LegendsDialog.open({cls: 'basic exchangeOfficeDialog', overlayCls: 'doNotCoverHeader stickToHeader', html: html,
                closeText: T.close, onClose: function () { dialog = null; }});
        }
        var root = dialog.querySelector('#exchangeOffice');
        update(T, root);
        var input = root.querySelector('.exchangeValue');
        input.addEventListener('input', function () { update(T, root); });
        root.querySelector('.changeDirection .openExchangeOffice').addEventListener('click', function () {
            show(direction === 'GoldToSilver' ? 'SilverToGold' : 'GoldToSilver', direction === 'GoldToSilver' ? 200 : 1);
        });
        root.querySelector('button.exchange').addEventListener('click', function (ev) {
            ev.preventDefault();
            var amount = parseInt(input.value.replace(/[^0-9]/g, ''), 10) || 0;
            this.disabled = true;
            var data = direction === 'GoldToSilver'
                ? {cmd: 'premiumFeature', featureKey: 'exchangeSilver', coins: amount, context: 'auction'}
                : {cmd: 'silverExchange', s: amount, g: Math.floor(amount / 200), exTyp: 'SilverToGold'};
            Travian.ajax({data: data, onSuccess: function (response) { applyResult(direction, response); },
                onFailure: function () { window.location.reload(); }});
        });
    }
    // After an exchange the dialog stays open (as in Legends) with the new gold and silver; the header and the
    // exchange widget of the auctions show them too. Without new amounts in the response the page is reloaded.
    function applyResult(direction, response) {
        var r = response && (response.options || response);
        if (!r || r.newGold === undefined || r.newSilver === undefined) {
            window.location.reload();
            return;
        }
        var T = window.LegendsExchangeTexts;
        T.goldAmount = parseInt(r.newGold, 10) || 0;
        T.silverAmount = parseInt(r.newSilver, 10) || 0;
        document.querySelectorAll('.ajaxReplaceableGoldAmount').forEach(function (e) { e.textContent = T.goldAmount; });
        document.querySelectorAll('.ajaxReplaceableSilverAmount').forEach(function (e) { e.textContent = T.silverAmount; });
        var gold = document.querySelector('.exchangeWidget .exchangeGold span'), silver = document.querySelector('.exchangeWidget .exchangeSilver span');
        if (gold) gold.textContent = fmt(T.goldAmount);
        if (silver) silver.textContent = fmt(T.silverAmount);
        show(direction, direction === 'GoldToSilver' ? 1 : 200);
    }
    function open(direction) {
        if (!window.LegendsExchangeTexts || !window.LegendsDialog) return;
        direction = direction === 'SilverToGold' ? 'SilverToGold' : 'GoldToSilver';
        show(direction, direction === 'GoldToSilver' ? 1 : 200);
    }
    return {open: open};
})();
