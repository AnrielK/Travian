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
        webView = WKWebView(frame: .zero, configuration: config)
        super.init()
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
    func webView(_ webView: WKWebView, didCommit navigation: WKNavigation!) { sync() }
    func webView(_ webView: WKWebView, didFinish navigation: WKNavigation!) { sync(); fitToScreen(newPage: true) }

    /// Phones: portrait shows 480 px of the 1280 px Legends layout (scrolled to the middle on a new page),
    /// landscape the whole layout width. Also after a rotation, where WebKit would keep the previous zoom.
    func fitToScreen(newPage: Bool) {
        guard UIDevice.current.userInterfaceIdiom == .phone else { return }
        Task { @MainActor [weak self] in
            try? await Task.sleep(nanoseconds: 200_000_000)   // content size follows the new viewport
            guard let self else { return }
            let scroll = self.webView.scrollView
            let width = scroll.bounds.width - scroll.adjustedContentInset.left - scroll.adjustedContentInset.right
            let pageWidth = scroll.contentSize.width / max(scroll.zoomScale, 0.01)
            guard width > 0, pageWidth > 0 else { return }
            let portrait = scroll.bounds.height > scroll.bounds.width
            let target = portrait ? width / 480 : width / pageWidth
            if !newPage || abs(scroll.zoomScale - target) > 0.01 {
                scroll.setZoomScale(target, animated: false)
            }
            // Portrait: the page fits the screen height, long windows scroll inside (see ovFitHeight).
            let visible = (scroll.bounds.height - scroll.adjustedContentInset.top - scroll.adjustedContentInset.bottom)
                / max(scroll.zoomScale, 0.01)
            self.webView.evaluateJavaScript("window.ovFitHeight && ovFitHeight(\(portrait ? Int(visible) : 0)); 0",
                                            completionHandler: nil)
            let extra = scroll.contentSize.width - width
            if portrait {
                let x = extra > 1 && (scroll.contentOffset.x <= 0.5 || !newPage) ? (extra / 2).rounded() : scroll.contentOffset.x
                scroll.setContentOffset(CGPoint(x: x, y: -scroll.adjustedContentInset.top), animated: false)
            } else {
                scroll.setContentOffset(CGPoint(x: -scroll.adjustedContentInset.left, y: scroll.contentOffset.y), animated: false)
            }
        }
    }

    /// Viewport of the game pages on phones (the game itself sets none, so WebKit would shrink the whole
    /// 1280 px layout to the screen width).
    private static let phoneViewport = """
(function () {
    // Phones in portrait: the Legends layout (min. 1280 px wide) at the scale of the original mobile view –
    // 480 px across the screen, scrolled to the middle – instead of the whole page shrunk to the width.
    if (Math.min(screen.width, screen.height) >= 600) return;   // tablets keep the full layout
    var meta = document.querySelector('meta[name=viewport]');
    if (!meta) {
        meta = document.createElement('meta');
        meta.name = 'viewport';
        (document.head || document.documentElement).appendChild(meta);
    }
    function portrait() {
        return typeof window.orientation === 'number' ? window.orientation % 180 === 0 : screen.height > screen.width;
    }
    function center() {
        var view = window.visualViewport ? window.visualViewport.width : window.innerWidth;
        var max = document.documentElement.scrollWidth - view;
        if (max > 0 && window.scrollX < 1 && (!window.visualViewport || window.visualViewport.pageLeft < 1)) window.scrollTo(Math.round(max / 2), window.scrollY);
    }
    var last = null;
    function apply() {
        var p = portrait();
        if (p === last) return;
        last = p;
        meta.content = (p ? 'width=480, initial-scale=' + (Math.min(screen.width, screen.height) / 480).toFixed(3)
            + ', shrink-to-fit=no' : 'width=1280') + ', minimum-scale=0.25, maximum-scale=4, user-scalable=yes';
        if (p) setTimeout(center, 60);
    }
    // Portrait: the page is as high as the screen and does not scroll up and down; a window (building list,
    // hero, …) taller than that scrolls inside itself. The app calls ovFitHeight(visible height in CSS px),
    // or ovFitHeight(0) in landscape.
    var style = document.createElement('style');
    style.textContent = 'html.ovFitHeight,html.ovFitHeight body{height:var(--ov-view-h)!important;min-height:0!important;overflow-y:hidden!important}'
        + 'html.ovFitHeight #contentOuterContainer{max-height:var(--ov-content-h);overflow-y:auto;overscroll-behavior:contain}'
        + 'html.ovFitHeight .dialogWrapper{max-height:calc(var(--ov-view-h) - 20px);overflow-y:auto}';
    (document.head || document.documentElement).appendChild(style);
    window.ovFitHeight = function (height) {
        var root = document.documentElement;
        if (!height) { root.classList.remove('ovFitHeight'); return; }
        root.style.setProperty('--ov-view-h', height + 'px');
        var box = document.getElementById('contentOuterContainer');
        var top = box ? box.getBoundingClientRect().top + window.scrollY : 0;
        root.style.setProperty('--ov-content-h', Math.max(200, Math.floor(height - top - 8)) + 'px');
        root.classList.add('ovFitHeight');
    };
    apply();
    window.addEventListener('orientationchange', function () { setTimeout(apply, 150); });
    window.addEventListener('load', function () { if (last) center(); });
})();
"""

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
