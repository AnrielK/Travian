/*
 * Dialogs of the Travian Legends 655 look (DialogV2 markup styled by legends_ltr.css), used by the quick links editor
 * and the Gold Club dialog: LegendsDialog.open({cls, title, heading, header, html, buttons, modal, closable, closeText,
 * onClose}) / close(el) / confirm(text, cancel, ok, cb) / alert(text).
 */
window.LegendsDialog = window.LegendsDialog || (function () {
    var X = '<svg viewBox="0 0 20 20"><g class="outline"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"></path></g>'
        + '<g class="icon"><path d="M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z"></path></g></svg>';

    function open(o) {
        var overlay = document.createElement('div');
        overlay.className = 'dialogOverlay legendsDialogOverlay' + (o.modal === false ? '' : ' enabled');
        overlay.innerHTML = '<div class="dialogWrapper dialogV2"><div class="dialog ' + (o.cls || '') + '"><div class="dialogContainer"><div class="dialogContents">'
            + (o.title ? '<h1 class="titleInHeader">' + o.title + '</h1>' : '')
            + '<form action="#" onsubmit="return false;">'
            + (o.header || '')
            + (o.closable === false ? '' : '<a class="dialogCancelButton iconButton buttonFramed green withIcon rectangle cancel" href="#" title="' + (o.closeText || 'Close') + '">' + X + '</a>')
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
