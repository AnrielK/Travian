import SwiftUI
import UIKit
import WebKit

/// One WKWebView + its navigation state. `profile` != nil = separate, persistent cookies (second player).
@MainActor
final class BrowserModel: NSObject, ObservableObject, WKNavigationDelegate, WKUIDelegate {
    let webView: WKWebView
    @Published var canGoBack = false
    @Published var canGoForward = false
    @Published var isLoading = false
    @Published var title = ""
    @Published var lastError: String?
    /// Liquid Glass bar minimised while the page is scrolled down (as in Safari on iOS 26); scrolling up expands it.
    @Published var barMinimized = false
    private var offsetObservation: NSKeyValueObservation?
    private var lastOffsetY: CGFloat = 0
    private var scrollTravel: CGFloat = 0

    init(profile: UUID? = nil) {
        let config = WKWebViewConfiguration()
        if let profile {
            if #available(iOS 17.0, *) {
                config.websiteDataStore = WKWebsiteDataStore(forIdentifier: profile)
            } else {
                config.websiteDataStore = .nonPersistent()
            }
        } else {
            config.websiteDataStore = .default()
        }
        config.preferences.javaScriptCanOpenWindowsAutomatically = true
        config.allowsInlineMediaPlayback = true
        config.userContentController.addUserScript(WKUserScript(source: Self.phoneViewport, injectionTime: .atDocumentEnd,
                                                                forMainFrameOnly: true))
        let fitRelay = FitMessageRelay()
        config.userContentController.add(fitRelay, name: "ovFit")
        webView = WKWebView(frame: .zero, configuration: config)
        super.init()
        fitRelay.browser = self
        UIDevice.current.beginGeneratingDeviceOrientationNotifications()
        NotificationCenter.default.addObserver(forName: UIDevice.orientationDidChangeNotification, object: nil,
                                               queue: .main) { [weak self] _ in
            Task { @MainActor in
                try? await Task.sleep(nanoseconds: 300_000_000)   // after the rotation animation
                self?.fitToScreen(newPage: false)
            }
        }
        webView.navigationDelegate = self
        webView.uiDelegate = self
        webView.allowsBackForwardNavigationGestures = true
        webView.scrollView.contentInsetAdjustmentBehavior = .automatic
        if #available(iOS 16.4, *) { webView.isInspectable = true }   // Safari → Develop → Simulator/iPhone
        offsetObservation = webView.scrollView.observe(\.contentOffset, options: [.new]) { [weak self] scroll, _ in
            let y = scroll.contentOffset.y
            let top = -scroll.adjustedContentInset.top
            let bottom = scroll.contentSize.height - scroll.bounds.height + scroll.adjustedContentInset.bottom
            let userScroll = scroll.isTracking || scroll.isDragging || scroll.isDecelerating
            Task { @MainActor in self?.scrolled(y: y, top: top, bottom: bottom, userScroll: userScroll) }
        }
    }

    /// Scroll down by a few points → minimise the bar; scroll up (or reach the top) → expand it again.
    private func scrolled(y: CGFloat, top: CGFloat, bottom: CGFloat, userScroll: Bool) {
        let delta = y - lastOffsetY
        lastOffsetY = y
        guard userScroll else { return }
        if y <= top + 4 {
            scrollTravel = 0
            if barMinimized { barMinimized = false }
            return
        }
        if y >= bottom - 2 && delta > 0 { return }   // rubber band at the end of the page
        if (delta > 0) != (scrollTravel > 0) { scrollTravel = 0 }
        scrollTravel += delta
        if scrollTravel > 24 && !barMinimized {
            barMinimized = true
        } else if scrollTravel < -24 && barMinimized {
            barMinimized = false
        }
    }

    func load(_ url: URL) {
        lastError = nil
        webView.load(URLRequest(url: url, cachePolicy: .reloadIgnoringLocalCacheData))
    }

    func reload() { webView.reload() }

    /// Room at the end of the page for a toolbar floating over the web view (Liquid Glass bar).
    func setBottomInset(_ inset: CGFloat) {
        let scroll = webView.scrollView
        guard abs(scroll.contentInset.bottom - inset) > 0.5 else { return }
        scroll.contentInset.bottom = inset
        scroll.verticalScrollIndicatorInsets.bottom = inset
    }

    private func sync() {
        canGoBack = webView.canGoBack
        canGoForward = webView.canGoForward
        isLoading = webView.isLoading
        title = webView.title ?? ""
    }

    func webView(_ webView: WKWebView, didStartProvisionalNavigation navigation: WKNavigation!) { isLoading = true; lastError = nil }
    func webView(_ webView: WKWebView, didCommit navigation: WKNavigation!) { sync(); barMinimized = false; scrollTravel = 0 }
    func webView(_ webView: WKWebView, didFinish navigation: WKNavigation!) { sync(); fitToScreen(newPage: true) }

    /// Phones: portrait shows 480 px of the 1280 px Legends layout (scrolled to the middle on a new page),
    /// landscape the whole layout width. Also after a rotation, where WebKit would keep the previous zoom.
    func fitToScreen(newPage: Bool) {
        guard UIDevice.current.userInterfaceIdiom == .phone else { return }
        Task { @MainActor [weak self] in
            try? await Task.sleep(nanoseconds: 200_000_000)   // content size follows the new viewport
            guard let self else { return }
            let scroll = self.webView.scrollView
            // Pages made for phones (lobby, login, activation) have their own viewport: no zoom carried over
            // from the previous game page, start at the left edge.
            let own = (try? await self.webView.evaluateJavaScript(
                "document.querySelector('meta[name=viewport]:not([data-ov])') ? 1 : 0")) as? Int ?? 0
            if own == 1 {
                let fit = scroll.minimumZoomScale
                if abs(scroll.zoomScale - fit) > 0.01 { scroll.setZoomScale(fit, animated: false) }
                scroll.setContentOffset(CGPoint(x: -scroll.adjustedContentInset.left, y: scroll.contentOffset.y), animated: false)
                return
            }
            let width = scroll.bounds.width - scroll.adjustedContentInset.left - scroll.adjustedContentInset.right
            let pageWidth = scroll.contentSize.width / max(scroll.zoomScale, 0.01)
            guard width > 0, pageWidth > 1000 else { return }   // only the wide game layout
            let portrait = scroll.bounds.height > scroll.bounds.width
            // Portrait: the village views show 480 px around the middle; pages with a content window (building
            // lists, hero, reports, …) show the whole window.
            var shown: CGFloat = 480
            var left: CGFloat? = nil
            if portrait, let box = (try? await self.webView.evaluateJavaScript(Self.contentWindowScript)) as? [NSNumber], box.count == 2 {
                shown = CGFloat(truncating: box[1]) + 24
                left = CGFloat(truncating: box[0]) - 12
            }
            let target = portrait ? width / shown : width / pageWidth
            if !newPage || abs(scroll.zoomScale - target) > 0.01 {
                scroll.setZoomScale(target, animated: false)
            }
            let extra = scroll.contentSize.width - width
            if portrait, let left, extra > 1 {
                let x = min(max(0, left * scroll.zoomScale), extra)
                scroll.setContentOffset(CGPoint(x: x.rounded(), y: scroll.contentOffset.y), animated: false)
            } else if portrait && extra > 1 && (scroll.contentOffset.x <= 0.5 || !newPage) {
                scroll.setContentOffset(CGPoint(x: (extra / 2).rounded(), y: scroll.contentOffset.y), animated: false)
            } else if !portrait {
                scroll.setContentOffset(CGPoint(x: -scroll.adjustedContentInset.left, y: scroll.contentOffset.y), animated: false)
            }
        }
    }

    /// Left edge and width (CSS px) of the content window (also the map window), or null on the village views.
    private static let contentWindowScript = """
    (function () {
        if (/(dorf1|dorf2)\\.php/.test(location.pathname)) return null;
        var box = document.getElementById('contentOuterContainer');
        if (!box) return null;
        var r = box.getBoundingClientRect();
        return [r.left + window.scrollX, r.width];
    })()
    """

    /// Viewport of the game pages on phones (the game itself sets none, so WebKit would shrink the whole
    /// 1280 px layout to the screen width).
    private static let phoneViewport = """
(function () {
    // Phones in portrait: the Legends layout (min. 1280 px wide) at the scale of the original mobile view –
    // the whole content window across the screen (480 px around the middle on the village views) – instead
    // of the whole page shrunk to the width.
    if (Math.min(screen.width, screen.height) >= 600) return;   // tablets keep the full layout
    if (document.querySelector('meta[name=viewport]')) return;   // pages made for phones (launcher) stay as they are
    var meta = document.createElement('meta');
    meta.name = 'viewport';
    meta.setAttribute('data-ov', '1');
    (document.head || document.documentElement).appendChild(meta);
    function portrait() {
        return typeof window.orientation === 'number' ? window.orientation % 180 === 0 : screen.height > screen.width;
    }
    // Left edge and width of the content window (messages, statistics, map, building list, …), null on the
    // village views or while the styles are not there yet.
    function windowBox() {
        if (/(dorf1|dorf2)\\.php/.test(location.pathname)) return null;
        var box = document.getElementById('contentOuterContainer');
        if (!box) return null;
        var r = box.getBoundingClientRect();
        return r.width > 300 && r.width < 1000 ? [r.left + window.scrollX, r.width] : null;
    }
    function center() {
        var view = window.visualViewport ? window.visualViewport.width : window.innerWidth;
        var max = document.documentElement.scrollWidth - view;
        if (max <= 0 || window.scrollX >= 1 || (window.visualViewport && window.visualViewport.pageLeft >= 1)) return;
        var box = windowBox();
        window.scrollTo(Math.round(box ? Math.min(Math.max(0, box[0] - 12), max) : max / 2), window.scrollY);
    }
    // Portrait: the viewport is as wide as the content window (+ margin), so every window is shown whole like
    // the building list; the village views keep 480 px around the middle.
    var last = null;
    function apply() {
        var p = portrait(), box = p ? windowBox() : null;
        var key = p ? (box ? Math.round(box[1] + 24) : 480) : 'landscape';
        if (key === last) return;
        last = key;
        meta.content = (p ? 'width=' + key + ', initial-scale=' + (Math.min(screen.width, screen.height) / key).toFixed(3)
            + ', shrink-to-fit=no' : 'width=1280') + ', minimum-scale=0.25, maximum-scale=4, user-scalable=yes';
        if (p) setTimeout(center, 60);
    }
    // Portrait: no automatic zoom when a text field gets the focus (iOS zooms in on small fonts) – the zoom
    // is limited to the current one while the field is focused, pinch zoom works again afterwards.
    var focusLock = false;
    document.addEventListener('focusin', function (event) {
        var el = event.target;
        if (last === null || last === 'landscape' || !el || !/^(INPUT|TEXTAREA|SELECT)$/.test(el.tagName)) return;
        var scale = window.visualViewport ? window.visualViewport.scale : 1;
        meta.content = meta.content.replace(/maximum-scale=[0-9.]+/, 'maximum-scale=' + scale.toFixed(3));
        focusLock = true;
    });
    document.addEventListener('focusout', function () {
        if (!focusLock) return;
        focusLock = false;
        setTimeout(function () { meta.content = meta.content.replace(/maximum-scale=[0-9.]+/, 'maximum-scale=4'); }, 300);
    });
    apply();
    // Ask the app to fit the content window again once the page has settled (pictures, scripts that scroll)
    // and when a page comes back from the back/forward cache, so every window sits like the building list.
    function refit() {
        try { window.webkit.messageHandlers.ovFit.postMessage(1); } catch (e) {}
    }
    window.addEventListener('load', function () { if (!focusLock) apply(); setTimeout(refit, 450); setTimeout(refit, 1300); });
    window.addEventListener('pageshow', function (e) { if (e.persisted) setTimeout(refit, 60); });
    window.addEventListener('orientationchange', function () { setTimeout(apply, 150); });
    window.addEventListener('load', function () { if (last !== 'landscape') center(); });
})();
"""

    /// The page asked for another fit (see `phoneViewport`); skipped while the user is touching the page.
    func refitRequested() {
        let scroll = webView.scrollView
        guard !scroll.isTracking, !scroll.isDragging, !scroll.isDecelerating, !scroll.isZooming else { return }
        fitToScreen(newPage: true)
    }

    func webView(_ webView: WKWebView, didFail navigation: WKNavigation!, withError error: Error) { fail(error) }
    func webView(_ webView: WKWebView, didFailProvisionalNavigation navigation: WKNavigation!, withError error: Error) { fail(error) }

    private func fail(_ error: Error) {
        sync()
        let ns = error as NSError
        if ns.domain == NSURLErrorDomain && ns.code == NSURLErrorCancelled { return }
        lastError = error.localizedDescription
    }

    func webView(_ webView: WKWebView, decidePolicyFor navigationAction: WKNavigationAction) async -> WKNavigationActionPolicy {
        if let url = navigationAction.request.url, let host = url.host, url.scheme?.hasPrefix("http") == true,
           host != "127.0.0.1", host != "localhost" {
            await UIApplication.shared.open(url)
            return .cancel
        }
        return .allow
    }

    func clearCookies() async {
        await webView.configuration.websiteDataStore.removeData(ofTypes: WKWebsiteDataStore.allWebsiteDataTypes(),
                                                                 modifiedSince: .distantPast)
    }

    // The game uses window.open, alert() and confirm().
    func webView(_ webView: WKWebView, createWebViewWith configuration: WKWebViewConfiguration,
                 for navigationAction: WKNavigationAction, windowFeatures: WKWindowFeatures) -> WKWebView? {
        if navigationAction.targetFrame == nil { webView.load(navigationAction.request) }
        return nil
    }

    func webView(_ webView: WKWebView, runJavaScriptAlertPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo) async {
        await withCheckedContinuation { (done: CheckedContinuation<Void, Never>) in
            let alert = UIAlertController(title: nil, message: message, preferredStyle: .alert)
            alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in done.resume() })
            present(alert, fallback: { done.resume() })
        }
    }

    func webView(_ webView: WKWebView, runJavaScriptConfirmPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo) async -> Bool {
        await withCheckedContinuation { (done: CheckedContinuation<Bool, Never>) in
            let alert = UIAlertController(title: nil, message: message, preferredStyle: .alert)
            alert.addAction(UIAlertAction(title: "Zrušit", style: .cancel) { _ in done.resume(returning: false) })
            alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in done.resume(returning: true) })
            present(alert, fallback: { done.resume(returning: false) })
        }
    }

    func webView(_ webView: WKWebView, runJavaScriptTextInputPanelWithPrompt prompt: String, defaultText: String?,
                 initiatedByFrame frame: WKFrameInfo) async -> String? {
        await withCheckedContinuation { (done: CheckedContinuation<String?, Never>) in
            let alert = UIAlertController(title: nil, message: prompt, preferredStyle: .alert)
            alert.addTextField { $0.text = defaultText }
            alert.addAction(UIAlertAction(title: "Zrušit", style: .cancel) { _ in done.resume(returning: nil) })
            alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in done.resume(returning: alert.textFields?.first?.text) })
            present(alert, fallback: { done.resume(returning: nil) })
        }
    }

    private func present(_ alert: UIAlertController, fallback: @escaping () -> Void) {
        var top = webView.window?.rootViewController
        while let presented = top?.presentedViewController { top = presented }
        guard let top else { fallback(); return }
        top.present(alert, animated: true)
    }
}

struct WebView: UIViewRepresentable {
    let webView: WKWebView
    func makeUIView(context: Context) -> WKWebView { webView }
    func updateUIView(_ uiView: WKWebView, context: Context) {}
}

/// Forwards the page's "ovFit" messages without the content controller keeping the browser alive.
final class FitMessageRelay: NSObject, WKScriptMessageHandler {
    weak var browser: BrowserModel?

    func userContentController(_ userContentController: WKUserContentController, didReceive message: WKScriptMessage) {
        Task { @MainActor [weak self] in self?.browser?.refitRequested() }
    }
}
