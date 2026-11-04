/*
 * Gold shop of the Travian Legends look in the payment wizard dialog of Legends 692.2 (dialog "paymentShopV5"): the gold
 * coin of the header and every link to gold.php open it with the content of gold.php?dialog=1 (GoldShopCtrl,
 * goldShop/legendsDialog.php); the tabs load their content into it. "Next" credits the chosen package in an offline
 * world (gold.php?package=…) or starts the game's payment wizard; the advantages book through Travian.Game.WayOfPayment.
 * After a change of the gold or of a feature the page is reloaded and the dialog opened again.
 */
(function () {
    var REOPEN = 'ovGoldShopDialog';
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
            dialog = window.LegendsDialog.open({cls: 'paymentShopV5 legendsGoldShop', html: html,
                onClose: function () { dialog = null; }});
        });
    }
    function reopenAfterReload(url) {
        try { sessionStorage.setItem(REOPEN, url); } catch (e) {}
        window.location.reload();
    }

    document.addEventListener('click', function (ev) {
        if (!ev.target.closest || !window.LegendsDialog) return;
        var shop = dialog && ev.target.closest('.legendsGoldShop');
        if (shop) {
            var tab = ev.target.closest('[data-shop-tab]');
            if (tab) {
                ev.preventDefault();
                show(tab.getAttribute('href'));
                return;
            }
            var next = ev.target.closest('#continueBuy');
            if (next) {
                ev.preventDefault();
                var p = shop.querySelector('input[name=package]:checked');
                if (next.getAttribute('data-offline') !== '1') {
                    jQuery(window).trigger('startPaymentWizard', {});
                    return;
                }
                if (!p) return;
                next.disabled = true;
                load('gold.php?package=' + p.value + '&c=' + encodeURIComponent(next.getAttribute('data-checker')))
                    .then(function () { reopenAfterReload('gold.php?credited=' + p.value); });
                return;
            }
            var book = ev.target.closest('.prosButton[data-feature]');
            if (book) {
                ev.preventDefault();
                new Travian.Game.WayOfPayment(book.getAttribute('data-feature'), 'goldShop');
            }
            return;
        }
        var link = ev.target.closest('a.shop, a.currency, a[href^="gold.php"]');
        if (link && !link.closest('#goldShopT46')) {
            ev.preventDefault();
            show(link.getAttribute('href') === 'gold.php?t=pros' ? 'gold.php?t=pros' : 'gold.php');
        }
    });
    document.addEventListener('change', function (ev) {
        var box = ev.target.closest && ev.target.closest('.legendsGoldShop input[name=autoProlong]');
        if (!box) return;
        box.disabled = true;
        Travian.ajax({data: {cmd: 'premiumFeature', featureKey: box.getAttribute('data-feature'), toggleAutoprolong: 1, context: 'goldShop'},
            onSuccess: function () { reopenAfterReload('gold.php?t=pros'); }});
    });

    // Help icons of the advantages: the card's <template class="bonusTooltip"> in a tooltip box of 350px below the icon,
    // its right edge at the icon's (above it when there is no room below), as the tippy tooltips of Legends.
    var tip = null;
    function hideTip() {
        if (tip && tip.parentNode) tip.parentNode.removeChild(tip);
        tip = null;
    }
    document.addEventListener('mouseover', function (ev) {
        var icon = ev.target.closest && ev.target.closest('.legendsGoldShop .bonusExplanation');
        if (!icon) {
            hideTip();
            return;
        }
        if (tip && tip._icon === icon) return;
        hideTip();
        var content = icon.querySelector('template.bonusTooltip');
        if (!content) return;
        tip = document.createElement('div');
        tip._icon = icon;
        tip.className = 'legendsShopTip';
        tip.innerHTML = '<div class="tippy-box" data-theme="custom" data-placement="bottom-end" role="tooltip"><div class="tippy-content">'
            + '<div class="text"><div class="contentV2"></div></div></div></div>';
        tip.querySelector('.contentV2').appendChild(content.content.cloneNode(true));
        document.body.appendChild(tip);
        var r = icon.getBoundingClientRect(), box = tip.getBoundingClientRect(), gap = 8;
        var top = r.bottom + gap;
        if (top + box.height > window.innerHeight && r.top - gap - box.height > 0) {
            top = r.top - gap - box.height;
            tip.firstChild.setAttribute('data-placement', 'top-end');
        }
        tip.style.left = Math.max(4, r.right - box.width + window.scrollX) + 'px';
        tip.style.top = (top + window.scrollY) + 'px';
    });

    var reopen = null;
    try {
        reopen = sessionStorage.getItem(REOPEN);
        sessionStorage.removeItem(REOPEN);
    } catch (e) {}
    if (reopen) {
        jQuery(function () { show(reopen); });
    }
})();
