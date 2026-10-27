import SwiftUI
import UIKit
import WebKit

/// One WKWebView + its navigation state. `ephemeral` = own cookies (second player).
final class BrowserModel: NSObject, ObservableObject, WKNavigationDelegate, WKUIDelegate {
    let webView: WKWebView
    @Published var canGoBack = false
    @Published var canGoForward = false
    @Published var isLoading = false
    @Published var title = ""
    @Published var lastError: String?

    init(ephemeral: Bool = false) {
        let config = WKWebViewConfiguration()
        config.websiteDataStore = ephemeral ? .nonPersistent() : .default()
        config.preferences.javaScriptCanOpenWindowsAutomatically = true
        config.allowsInlineMediaPlayback = true
        // The game is a desktop layout (~1280 px) but its pages declare width=device-width,
        // which shows it at 1:1 on a phone. Lay it out at desktop width and let the user zoom.
        let viewport = """
        (function () {
          var m = document.querySelector('meta[name=viewport]');
          if (!m) { m = document.createElement('meta'); m.name = 'viewport'; (document.head || document.documentElement).appendChild(m); }
          m.content = 'width=1280';
        })();
        """
        config.userContentController.addUserScript(
            WKUserScript(source: viewport, injectionTime: .atDocumentEnd, forMainFrameOnly: true))
        webView = WKWebView(frame: .zero, configuration: config)
        super.init()
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

    private func sync() {
        canGoBack = webView.canGoBack
        canGoForward = webView.canGoForward
        isLoading = webView.isLoading
        title = webView.title ?? ""
    }

    func webView(_ webView: WKWebView, didStartProvisionalNavigation navigation: WKNavigation!) { isLoading = true; lastError = nil }
    func webView(_ webView: WKWebView, didCommit navigation: WKNavigation!) { sync() }
    func webView(_ webView: WKWebView, didFinish navigation: WKNavigation!) { sync() }
    func webView(_ webView: WKWebView, didFail navigation: WKNavigation!, withError error: Error) { fail(error) }
    func webView(_ webView: WKWebView, didFailProvisionalNavigation navigation: WKNavigation!, withError error: Error) { fail(error) }

    private func fail(_ error: Error) {
        sync()
        let ns = error as NSError
        if ns.domain == NSURLErrorDomain && ns.code == NSURLErrorCancelled { return }
        lastError = error.localizedDescription
    }

    // The game uses window.open, alert() and confirm().
    func webView(_ webView: WKWebView, createWebViewWith configuration: WKWebViewConfiguration,
                 for navigationAction: WKNavigationAction, windowFeatures: WKWindowFeatures) -> WKWebView? {
        if navigationAction.targetFrame == nil { webView.load(navigationAction.request) }
        return nil
    }

    func webView(_ webView: WKWebView, runJavaScriptAlertPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo, completionHandler: @escaping () -> Void) {
        let alert = UIAlertController(title: nil, message: message, preferredStyle: .alert)
        alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in completionHandler() })
        present(alert, fallback: completionHandler)
    }

    func webView(_ webView: WKWebView, runJavaScriptConfirmPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo, completionHandler: @escaping (Bool) -> Void) {
        let alert = UIAlertController(title: nil, message: message, preferredStyle: .alert)
        alert.addAction(UIAlertAction(title: "Zrušit", style: .cancel) { _ in completionHandler(false) })
        alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in completionHandler(true) })
        present(alert, fallback: { completionHandler(false) })
    }

    func webView(_ webView: WKWebView, runJavaScriptTextInputPanelWithPrompt prompt: String, defaultText: String?,
                 initiatedByFrame frame: WKFrameInfo, completionHandler: @escaping (String?) -> Void) {
        let alert = UIAlertController(title: nil, message: prompt, preferredStyle: .alert)
        alert.addTextField { $0.text = defaultText }
        alert.addAction(UIAlertAction(title: "Zrušit", style: .cancel) { _ in completionHandler(nil) })
        alert.addAction(UIAlertAction(title: "OK", style: .default) { _ in completionHandler(alert.textFields?.first?.text) })
        present(alert, fallback: { completionHandler(nil) })
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
