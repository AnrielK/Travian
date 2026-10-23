import SwiftUI
import AppKit
import WebKit

/// Holds one WKWebView and mirrors its navigation state for SwiftUI.
final class BrowserModel: NSObject, ObservableObject, WKNavigationDelegate, WKUIDelegate {
    let webView: WKWebView
    @Published var address = ""
    @Published var title = ""
    @Published var canGoBack = false
    @Published var canGoForward = false
    @Published var isLoading = false
    @Published var lastError: String?

    /// `ephemeral` gives the view its own cookie jar, so a second player can be logged in at the same time.
    init(ephemeral: Bool = false) {
        let configuration = WKWebViewConfiguration()
        configuration.websiteDataStore = ephemeral ? .nonPersistent() : .default()
        configuration.preferences.javaScriptCanOpenWindowsAutomatically = true
        webView = WKWebView(frame: .zero, configuration: configuration)
        super.init()
        webView.navigationDelegate = self
        webView.uiDelegate = self
        webView.allowsBackForwardNavigationGestures = true
        if #available(macOS 13.3, *) { webView.isInspectable = true }   // right click → Inspect Element
    }

    func load(_ url: URL) {
        lastError = nil
        webView.load(URLRequest(url: url, cachePolicy: .reloadIgnoringLocalCacheData))
    }

    func go(to text: String, base: URL) {
        let trimmed = text.trimmingCharacters(in: .whitespaces)
        guard !trimmed.isEmpty else { return }
        if let url = URL(string: trimmed), url.scheme != nil { load(url) }
        else if let url = URL(string: trimmed, relativeTo: base) { load(url.absoluteURL) }
    }

    func reload() { webView.reload() }
    func back() { webView.goBack() }
    func forward() { webView.goForward() }

    private func sync() {
        address = webView.url?.absoluteString ?? address
        title = webView.title ?? ""
        canGoBack = webView.canGoBack
        canGoForward = webView.canGoForward
        isLoading = webView.isLoading
    }

    // MARK: WKNavigationDelegate
    func webView(_ webView: WKWebView, didStartProvisionalNavigation navigation: WKNavigation!) { isLoading = true; lastError = nil }
    func webView(_ webView: WKWebView, didCommit navigation: WKNavigation!) { sync() }
    func webView(_ webView: WKWebView, didFinish navigation: WKNavigation!) { sync() }
    func webView(_ webView: WKWebView, didFail navigation: WKNavigation!, withError error: Error) { fail(error) }
    func webView(_ webView: WKWebView, didFailProvisionalNavigation navigation: WKNavigation!, withError error: Error) { fail(error) }

    private func fail(_ error: Error) {
        sync()
        let ns = error as NSError
        if ns.domain == NSURLErrorDomain && ns.code == NSURLErrorCancelled { return }
        lastError = ns.code == NSURLErrorCannotConnectToHost ? "Server neběží – klikněte na Spustit." : error.localizedDescription
    }

    // MARK: WKUIDelegate – the game uses window.open, alert() and confirm()
    func webView(_ webView: WKWebView, createWebViewWith configuration: WKWebViewConfiguration,
                 for navigationAction: WKNavigationAction, windowFeatures: WKWindowFeatures) -> WKWebView? {
        if navigationAction.targetFrame == nil { webView.load(navigationAction.request) }
        return nil
    }

    func webView(_ webView: WKWebView, runJavaScriptAlertPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo, completionHandler: @escaping () -> Void) {
        let alert = NSAlert()
        alert.messageText = message
        alert.addButton(withTitle: "OK")
        alert.runModal()
        completionHandler()
    }

    func webView(_ webView: WKWebView, runJavaScriptConfirmPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo, completionHandler: @escaping (Bool) -> Void) {
        let alert = NSAlert()
        alert.messageText = message
        alert.addButton(withTitle: "OK")
        alert.addButton(withTitle: "Zrušit")
        completionHandler(alert.runModal() == .alertFirstButtonReturn)
    }

    func webView(_ webView: WKWebView, runJavaScriptTextInputPanelWithPrompt prompt: String, defaultText: String?,
                 initiatedByFrame frame: WKFrameInfo, completionHandler: @escaping (String?) -> Void) {
        let alert = NSAlert()
        alert.messageText = prompt
        let field = NSTextField(frame: NSRect(x: 0, y: 0, width: 260, height: 24))
        field.stringValue = defaultText ?? ""
        alert.accessoryView = field
        alert.addButton(withTitle: "OK")
        alert.addButton(withTitle: "Zrušit")
        completionHandler(alert.runModal() == .alertFirstButtonReturn ? field.stringValue : nil)
    }
}

struct WebViewRepresentable: NSViewRepresentable {
    let webView: WKWebView
    func makeNSView(context: Context) -> WKWebView { webView }
    func updateNSView(_ nsView: WKWebView, context: Context) {}
}

/// Address bar + web view.
@MainActor
struct BrowserPane: View {
    @ObservedObject var model: BrowserModel
    let baseURL: URL
    var showsHomeButtons = true
    @State private var editing = ""

    var body: some View {
        VStack(spacing: 0) {
            HStack(spacing: 6) {
                Button { model.back() } label: { Image(systemName: "chevron.left") }
                    .disabled(!model.canGoBack).help("Zpět")
                Button { model.forward() } label: { Image(systemName: "chevron.right") }
                    .disabled(!model.canGoForward).help("Vpřed")
                Button { model.reload() } label: { Image(systemName: model.isLoading ? "xmark" : "arrow.clockwise") }
                    .help("Obnovit (⌘R)")
                    .keyboardShortcut("r", modifiers: .command)
                TextField("Adresa", text: $editing)
                    .textFieldStyle(.roundedBorder)
                    .font(.system(.body, design: .monospaced))
                    .onSubmit { model.go(to: editing, base: baseURL) }
                if showsHomeButtons {
                    Button("Vesnice") { model.load(URL(string: "dorf1.php", relativeTo: baseURL)!.absoluteURL) }
                    Button("Mapa") { model.load(URL(string: "karte.php", relativeTo: baseURL)!.absoluteURL) }
                }
                Button { if let url = model.webView.url { NSWorkspace.shared.open(url) } } label: {
                    Image(systemName: "safari")
                }.help("Otevřít v Safari")
            }
            .buttonStyle(.borderless)
            .padding(.horizontal, 10).padding(.vertical, 6)
            .background(.bar)
            .onReceive(model.$address) { editing = $0 }

            ZStack {
                WebViewRepresentable(webView: model.webView)
                if let error = model.lastError {
                    VStack(spacing: 8) {
                        Image(systemName: "bolt.horizontal.circle").font(.largeTitle)
                        Text(error).multilineTextAlignment(.center)
                    }
                    .padding(24)
                    .background(.regularMaterial, in: RoundedRectangle(cornerRadius: 12))
                }
            }
        }
    }
}

/// Extra windows with their own cookies – log in as a second player and attack yourself.
@MainActor
enum PlayerWindows {
    private static var windows: [NSWindow] = []
    private static var counter = 1

    static func open(baseURL: URL) {
        counter += 1
        let model = BrowserModel(ephemeral: true)
        let window = NSWindow(contentRect: NSRect(x: 0, y: 0, width: 1200, height: 820),
                              styleMask: [.titled, .closable, .resizable, .miniaturizable],
                              backing: .buffered, defer: false)
        window.title = "Hráč \(counter) – samostatné přihlášení"
        window.isReleasedWhenClosed = false
        window.contentView = NSHostingView(rootView: BrowserPane(model: model, baseURL: baseURL))
        window.center()
        window.makeKeyAndOrderFront(nil)
        windows.append(window)
        windows.removeAll { !$0.isVisible && $0 !== window }
        model.load(URL(string: "login.php", relativeTo: baseURL)!.absoluteURL)
    }
}
