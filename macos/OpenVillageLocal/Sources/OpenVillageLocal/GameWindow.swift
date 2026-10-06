import SwiftUI
import WebKit
import AppKit

/// One embedded browser per player profile. Every profile has its own persistent WKWebsiteDataStore,
/// so several accounts can be logged in at the same time (multi-player testing on one Mac).
@MainActor
final class GameBrowser: NSObject, ObservableObject, WKNavigationDelegate, WKUIDelegate, WKDownloadDelegate {
    let webView: WKWebView
    @Published var title = ""
    @Published var urlText = ""
    @Published var canGoBack = false
    @Published var canGoForward = false
    @Published var isLoading = false
    @Published var progress = 0.0
    @Published var lastDownload: URL?
    private var observers: [NSKeyValueObservation] = []

    init(profileID: UUID) {
        let config = WKWebViewConfiguration()
        config.websiteDataStore = WKWebsiteDataStore(forIdentifier: profileID)
        config.preferences.javaScriptCanOpenWindowsAutomatically = true
        config.preferences.isElementFullscreenEnabled = true
        webView = WKWebView(frame: .zero, configuration: config)
        webView.isInspectable = true                      // right click → Inspect Element (Web Inspector)
        webView.allowsBackForwardNavigationGestures = true
        webView.allowsMagnification = true
        super.init()
        webView.navigationDelegate = self
        webView.uiDelegate = self
        // WKWebView reports KVO changes on the main thread.
        observers = [
            webView.observe(\.title) { [weak self] wv, _ in MainActor.assumeIsolated { self?.title = wv.title ?? "" } },
            webView.observe(\.url) { [weak self] wv, _ in MainActor.assumeIsolated { self?.urlText = wv.url?.absoluteString ?? "" } },
            webView.observe(\.canGoBack) { [weak self] wv, _ in MainActor.assumeIsolated { self?.canGoBack = wv.canGoBack } },
            webView.observe(\.canGoForward) { [weak self] wv, _ in MainActor.assumeIsolated { self?.canGoForward = wv.canGoForward } },
            webView.observe(\.isLoading) { [weak self] wv, _ in MainActor.assumeIsolated { self?.isLoading = wv.isLoading } },
            webView.observe(\.estimatedProgress) { [weak self] wv, _ in MainActor.assumeIsolated { self?.progress = wv.estimatedProgress } },
        ]
    }

    func load(_ url: URL) { webView.load(URLRequest(url: url)) }

    func go(_ text: String, base: URL) {
        var t = text.trimmingCharacters(in: .whitespaces)
        if t.isEmpty { return }
        if t.hasPrefix("/") { t = String(t.dropFirst()) }
        if let u = URL(string: t), u.scheme != nil { load(u) } else if let u = URL(string: t, relativeTo: base) { load(u.absoluteURL) }
    }

    func clearData() async {
        let store = webView.configuration.websiteDataStore
        let types = WKWebsiteDataStore.allWebsiteDataTypes()
        await store.removeData(ofTypes: types, modifiedSince: .distantPast)
    }

    // MARK: WKUIDelegate – the game uses alert/confirm/prompt and window.open

    func webView(_ webView: WKWebView, createWebViewWith configuration: WKWebViewConfiguration,
                 for navigationAction: WKNavigationAction, windowFeatures: WKWindowFeatures) -> WKWebView? {
        if let url = navigationAction.request.url {
            if url.host == "127.0.0.1" || url.host == "localhost" { webView.load(navigationAction.request) }
            else { NSWorkspace.shared.open(url) }
        }
        return nil
    }

    func webView(_ webView: WKWebView, runJavaScriptAlertPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo) async {
        let alert = NSAlert()
        alert.messageText = message
        alert.addButton(withTitle: "OK")
        alert.runModal()
    }

    func webView(_ webView: WKWebView, runJavaScriptConfirmPanelWithMessage message: String,
                 initiatedByFrame frame: WKFrameInfo) async -> Bool {
        let alert = NSAlert()
        alert.messageText = message
        alert.addButton(withTitle: "OK")
        alert.addButton(withTitle: "Zrušit")
        return alert.runModal() == .alertFirstButtonReturn
    }

    func webView(_ webView: WKWebView, runJavaScriptTextInputPanelWithPrompt prompt: String, defaultText: String?,
                 initiatedByFrame frame: WKFrameInfo) async -> String? {
        let alert = NSAlert()
        alert.messageText = prompt
        let field = NSTextField(frame: NSRect(x: 0, y: 0, width: 280, height: 24))
        field.stringValue = defaultText ?? ""
        alert.accessoryView = field
        alert.addButton(withTitle: "OK")
        alert.addButton(withTitle: "Zrušit")
        return alert.runModal() == .alertFirstButtonReturn ? field.stringValue : nil
    }

    // MARK: Navigation – external links go to the default browser, attachments are downloaded

    func webView(_ webView: WKWebView, decidePolicyFor navigationAction: WKNavigationAction) async -> WKNavigationActionPolicy {
        if let url = navigationAction.request.url, let scheme = url.scheme,
           ["http", "https"].contains(scheme), url.host != "127.0.0.1", url.host != "localhost",
           navigationAction.targetFrame?.isMainFrame ?? true {
            NSWorkspace.shared.open(url)
            return .cancel
        }
        return navigationAction.shouldPerformDownload ? .download : .allow
    }

    func webView(_ webView: WKWebView, decidePolicyFor navigationResponse: WKNavigationResponse) async -> WKNavigationResponsePolicy {
        var attachment = false
        if let http = navigationResponse.response as? HTTPURLResponse,
           let cd = http.value(forHTTPHeaderField: "Content-Disposition"), cd.lowercased().contains("attachment") {
            attachment = true
        }
        return attachment || !navigationResponse.canShowMIMEType ? .download : .allow
    }

    func webView(_ webView: WKWebView, navigationAction: WKNavigationAction, didBecome download: WKDownload) {
        download.delegate = self
    }

    func webView(_ webView: WKWebView, navigationResponse: WKNavigationResponse, didBecome download: WKDownload) {
        download.delegate = self
    }

    func download(_ download: WKDownload, decideDestinationUsing response: URLResponse,
                  suggestedFilename: String) async -> URL? {
        let downloads = FileManager.default.urls(for: .downloadsDirectory, in: .userDomainMask)[0]
        var target = downloads.appendingPathComponent(suggestedFilename)
        var n = 1
        while FileManager.default.fileExists(atPath: target.path) {
            let base = (suggestedFilename as NSString).deletingPathExtension
            let ext = (suggestedFilename as NSString).pathExtension
            target = downloads.appendingPathComponent("\(base)-\(n)" + (ext.isEmpty ? "" : ".\(ext)"))
            n += 1
        }
        lastDownload = target
        return target
    }

    func downloadDidFinish(_ download: WKDownload) {
        if let url = lastDownload { NSWorkspace.shared.activateFileViewerSelecting([url]) }
    }
}

struct WebViewHost: NSViewRepresentable {
    let webView: WKWebView
    func makeNSView(context: Context) -> WKWebView { webView }
    func updateNSView(_ nsView: WKWebView, context: Context) {}
}

struct GameWindowView: View {
    @EnvironmentObject var controller: ServerController
    let profileID: UUID?
    @StateObject private var holder = BrowserHolder()

    var body: some View {
        Group {
            if let browser = holder.browser {
                BrowserPane(browser: browser, profileID: profileID)
            } else {
                Text("Neznámý profil hráče").foregroundStyle(.secondary)
                    .frame(maxWidth: .infinity, maxHeight: .infinity)
            }
        }
        .onAppear {
            if holder.browser == nil, let id = profileID {
                let b = GameBrowser(profileID: id)
                holder.browser = b
                b.load(controller.baseURL)
            }
        }
    }
}

struct BrowserPane: View {
    @EnvironmentObject var controller: ServerController
    @ObservedObject var browser: GameBrowser
    let profileID: UUID?
    @State private var address = ""

    var profile: PlayerProfile? { controller.profiles.first { $0.id == profileID } }

    var body: some View {
        VStack(spacing: 0) {
            toolbar
            if browser.isLoading {
                ProgressView(value: browser.progress).progressViewStyle(.linear).frame(height: 2)
            } else {
                Divider()
            }
            WebViewHost(webView: browser.webView)
        }
        .onChange(of: browser.urlText) { _, new in address = new }
        .navigationTitle("\(profile?.name ?? "Hráč") – \(browser.title.isEmpty ? "OpenVillage" : browser.title)")
    }

    var toolbar: some View {
        HStack(spacing: 6) {
            Button { browser.webView.goBack() } label: { Image(systemName: "chevron.left") }.disabled(!browser.canGoBack)
            Button { browser.webView.goForward() } label: { Image(systemName: "chevron.right") }.disabled(!browser.canGoForward)
            Button { browser.webView.reload() } label: { Image(systemName: "arrow.clockwise") }
            Menu {
                Button("Spouštěč / registrace") { browser.load(controller.baseURL) }
                Button("Vesnice (dorf1)") { browser.load(controller.gameURL.appendingPathComponent("dorf1.php")) }
                Button("Centrum (dorf2)") { browser.load(controller.gameURL.appendingPathComponent("dorf2.php")) }
                Button("Mapa") { browser.load(controller.gameURL.appendingPathComponent("karte.php")) }
                Button("Admin panel") { browser.load(controller.adminURL) }
                Button("health.php") { browser.load(controller.baseURL.appendingPathComponent("health.php")) }
            } label: { Image(systemName: "house") }
                .menuIndicator(.hidden)
                .fixedSize()
            TextField("Adresa", text: $address)
                .textFieldStyle(.roundedBorder)
                .font(.callout.monospaced())
                .onSubmit { browser.go(address, base: controller.baseURL) }
            Text(profile?.name ?? "").font(.caption.weight(.semibold))
                .padding(.horizontal, 8).padding(.vertical, 3)
                .background(Capsule().fill(Color.accentColor.opacity(0.15)))
            Menu {
                Button("Otevřít v prohlížeči") { if let u = browser.webView.url { NSWorkspace.shared.open(u) } }
                Button("Kopírovat adresu") { copy(browser.urlText) }
                Divider()
                Button("Zvětšit") { browser.webView.magnification += 0.1 }
                Button("Zmenšit") { browser.webView.magnification = max(0.5, browser.webView.magnification - 0.1) }
                Button("Skutečná velikost") { browser.webView.magnification = 1 }
                Divider()
                Button("Odhlásit (smazat cookies profilu)") {
                    Task { await browser.clearData(); browser.load(controller.baseURL) }
                }
            } label: { Image(systemName: "ellipsis.circle") }
                .menuIndicator(.hidden)
                .fixedSize()
        }
        .buttonStyle(.borderless)
        .padding(.horizontal, 10).padding(.vertical, 6)
    }
}

@MainActor
final class BrowserHolder: ObservableObject {
    @Published var browser: GameBrowser?
}

// MARK: - Players

struct PlayersView: View {
    @EnvironmentObject var controller: ServerController
    @Environment(\.openWindow) private var openWindow
    @State private var players: [[String]] = []
    @State private var loading = false

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 18) {
                Text("Hra a hráči").font(.title.weight(.semibold))
                Text("Každý profil má vlastní cookies a session, takže můžete mít přihlášeno více účtů najednou a testovat interakce (útoky, obchod, zprávy, aliance). Účet v profilu vytvoříte registrací na spouštěči.")
                    .foregroundStyle(.secondary)
                    .fixedSize(horizontal: false, vertical: true)

                GroupBox {
                    VStack(alignment: .leading, spacing: 8) {
                        HStack {
                            Text("Profily prohlížeče").font(.headline)
                            Spacer()
                            Button { controller.profiles.append(PlayerProfile(name: "Hráč \(controller.profiles.count + 1)")) } label: {
                                Label("Přidat profil", systemImage: "plus")
                            }
                        }
                        ForEach($controller.profiles) { $p in
                            HStack(spacing: 10) {
                                Image(systemName: "person.crop.circle").font(.title2).foregroundStyle(.secondary)
                                TextField("Název", text: $p.name).textFieldStyle(.roundedBorder).frame(width: 160)
                                TextField("Poznámka (účet, kmen…)", text: $p.note).textFieldStyle(.roundedBorder)
                                Button { openWindow(id: "game", value: p.id) } label: { Label("Otevřít", systemImage: "macwindow") }
                                    .disabled(!controller.healthy)
                                Button(role: .destructive) {
                                    let id = p.id
                                    controller.profiles.removeAll { $0.id == id }
                                    Task { try? await WKWebsiteDataStore.remove(forIdentifier: id) }
                                } label: { Image(systemName: "trash") }
                                    .buttonStyle(.borderless)
                                    .disabled(controller.profiles.count <= 1)
                            }
                        }
                    }
                    .padding(6)
                }

                GroupBox {
                    VStack(alignment: .leading, spacing: 8) {
                        HStack {
                            Text("Účty ve světě").font(.headline)
                            Spacer()
                            if loading { ProgressView().controlSize(.small) }
                            Button { Task { await loadPlayers() } } label: { Image(systemName: "arrow.clockwise") }
                                .buttonStyle(.borderless)
                        }
                        if players.isEmpty {
                            Text(controller.healthy ? "Zatím žádní hráči – zaregistrujte se na spouštěči." : "Server neběží.")
                                .foregroundStyle(.secondary)
                        } else {
                            Grid(alignment: .leading, horizontalSpacing: 18, verticalSpacing: 6) {
                                GridRow {
                                    ForEach(["ID", "Hráč", "Kmen", "Zlato", "Stříbro", "Vesnice", "Populace", "Ochrana do"], id: \.self) {
                                        Text($0).font(.caption.weight(.semibold)).foregroundStyle(.secondary)
                                    }
                                }
                                Divider()
                                ForEach(players, id: \.self) { row in
                                    GridRow {
                                        ForEach(Array(row.enumerated()), id: \.offset) { _, v in Text(v).font(.callout.monospacedDigit()) }
                                    }
                                }
                            }
                        }
                    }
                    .padding(6)
                }
            }
            .padding(20)
        }
        .task { await loadPlayers() }
    }

    func loadPlayers() async {
        guard controller.healthy else { players = []; return }
        loading = true
        let r = await controller.query("""
            SELECT u.id, u.name,
                   CASE u.race WHEN 1 THEN 'Římané' WHEN 2 THEN 'Germáni' WHEN 3 THEN 'Galové' WHEN 4 THEN 'Příroda' WHEN 5 THEN 'Natarové' ELSE u.race END,
                   u.gift_gold + u.bought_gold, u.silver,
                   (SELECT COUNT(*) FROM vdata v WHERE v.owner = u.id), u.total_pop,
                   IF(u.protection > UNIX_TIMESTAMP(), FROM_UNIXTIME(u.protection, '%d.%m. %H:%i'), '–')
            FROM users u WHERE u.id > 2 ORDER BY u.id
            """)
        players = r.rows
        loading = false
    }
}
