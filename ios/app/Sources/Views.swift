import SwiftUI

@MainActor
struct RootView: View {
    @EnvironmentObject var server: GameServer
    @StateObject private var browser = BrowserModel()
    @Environment(\.scenePhase) private var scenePhase
    @State private var sheet: Sheet?

    enum Sheet: String, Identifiable {
        case tools, logs, secondPlayer
        var id: String { rawValue }
    }

    var body: some View {
        ZStack {
            WebView(webView: browser.webView)
                .ignoresSafeArea(edges: .bottom)
            switch server.state {
            case .idle, .preparing:
                StatusCard(icon: "hourglass", text: "Připravuji hru…\nPrvní spuštění trvá několik sekund.", spinner: true)
            case .failed(let message):
                StatusCard(icon: "exclamationmark.triangle", text: message, spinner: false) {
                    Button("Zkusit znovu") { server.start() }.buttonStyle(.borderedProminent)
                    Button("Logy") { sheet = .logs }
                }
            case .running:
                if let error = browser.lastError {
                    StatusCard(icon: "bolt.horizontal.circle", text: error, spinner: false) {
                        Button("Načíst znovu") { browser.load(server.url("login.php")) }.buttonStyle(.borderedProminent)
                    }
                }
            }
        }
        .safeAreaInset(edge: .bottom) { toolbar }
        .onAppear { server.start() }
        .onChange(of: server.state) { state in
            if state == .running { browser.load(server.url("login.php")) }
        }
        .onChange(of: scenePhase) { phase in
            if phase == .active { server.ensureRunning() }
        }
        .sheet(item: $sheet) { which in
            switch which {
            case .tools: ToolsView(browser: browser)
            case .logs: LogsView()
            case .secondPlayer: SecondPlayerView()
            }
        }
    }

    private var toolbar: some View {
        HStack(spacing: 0) {
            barButton("chevron.left", "Zpět") { browser.webView.goBack() }.disabled(!browser.canGoBack)
            barButton("arrow.clockwise", "Obnovit") { browser.reload() }
            Menu {
                Button { go("dorf1.php") } label: { Label("Vesnice", systemImage: "house") }
                Button { go("dorf2.php") } label: { Label("Centrum vesnice", systemImage: "building.2") }
                Button { go("karte.php") } label: { Label("Mapa", systemImage: "map") }
                Button { go("build.php?id=39") } label: { Label("Shromaždiště", systemImage: "flag") }
                Button { go("berichte.php") } label: { Label("Hlášení", systemImage: "doc.text") }
                Button { go("nachrichten.php") } label: { Label("Zprávy", systemImage: "envelope") }
                Divider()
                Button { go("login.php") } label: { Label("Přihlášení", systemImage: "person.crop.circle") }
                Button { go("anmelden.php") } label: { Label("Registrace", systemImage: "person.badge.plus") }
                Button { go("panel/") } label: { Label("Admin panel (Multihunter / admin123)", systemImage: "lock.shield") }
            } label: {
                barLabel("square.grid.2x2", "Menu")
            }
            barButton("wand.and.stars", "Nástroje") { sheet = .tools }
            barButton("person.2", "2. hráč") { sheet = .secondPlayer }
            barButton("text.alignleft", "Logy") { sheet = .logs }
        }
        .padding(.horizontal, 6)
        .padding(.top, 4)
        .background(.bar)
    }

    private func go(_ path: String) { browser.load(server.url(path)) }

    private func barButton(_ icon: String, _ title: String, action: @escaping () -> Void) -> some View {
        Button(action: action) { barLabel(icon, title) }
    }

    private func barLabel(_ icon: String, _ title: String) -> some View {
        VStack(spacing: 2) {
            Image(systemName: icon).font(.system(size: 18))
            Text(title).font(.system(size: 10))
        }
        .frame(maxWidth: .infinity)
        .padding(.vertical, 4)
    }
}

extension StatusCard where Actions == EmptyView {
    init(icon: String, text: String, spinner: Bool) {
        self.init(icon: icon, text: text, spinner: spinner) { EmptyView() }
    }
}

struct StatusCard<Actions: View>: View {
    let icon: String
    let text: String
    let spinner: Bool
    @ViewBuilder var actions: () -> Actions

    init(icon: String, text: String, spinner: Bool, @ViewBuilder actions: @escaping () -> Actions) {
        self.icon = icon; self.text = text; self.spinner = spinner; self.actions = actions
    }

    var body: some View {
        VStack(spacing: 12) {
            if spinner { ProgressView() } else { Image(systemName: icon).font(.largeTitle) }
            Text(text).multilineTextAlignment(.center)
            actions()
        }
        .padding(24)
        .background(.regularMaterial, in: RoundedRectangle(cornerRadius: 16))
        .padding(32)
    }
}

@MainActor
struct ToolsView: View {
    @ObservedObject var browser: BrowserModel
    @EnvironmentObject var server: GameServer
    @Environment(\.dismiss) private var dismiss
    @State private var message = ""
    @State private var speed = 50
    @State private var confirmReset = false
    @State private var players: [[String]] = []

    var body: some View {
        NavigationView {
            Form {
                Section {
                    tool("Dokončit vše hned", "forward.end.fill",
                         "Stavby, výzkumy, trénink i pohyby vojsk a obchodníků doběhnou") { try GameDB.fastForward() }
                    tool("Naplnit sklady", "shippingbox.fill", nil) { try GameDB.fillResources() }
                    tool("+1000 zlata všem", "dollarsign.circle.fill", nil) { try GameDB.addGold() }
                    tool("Zrušit ochranu nováčků", "shield.slash", nil) { try GameDB.removeProtection() }
                } header: { Text("Testovací nástroje") } footer: { if !message.isEmpty { Text(message) } }

                Section("Rychlost serveru") {
                    Picker("Rychlost", selection: $speed) {
                        ForEach([1, 3, 5, 10, 50, 100, 500, 1000], id: \.self) { Text("×\($0)").tag($0) }
                    }
                    Button("Nastavit ×\(speed)") { run("Rychlost ×\(speed)") { try GameDB.setSpeed(speed) } }
                }

                Section("Hráči") {
                    if players.isEmpty { Text("Zatím nikdo – zaregistrujte se.").foregroundStyle(.secondary) }
                    ForEach(players.indices, id: \.self) { i in
                        let p = players[i]
                        HStack {
                            VStack(alignment: .leading) {
                                Text(p[0]).bold()
                                Text("\(p[1]) · vesnice \(p[3]) · populace \(p[4])").font(.caption).foregroundStyle(.secondary)
                            }
                            Spacer()
                            Text("\(p[2]) 🪙").font(.caption)
                        }
                    }
                }

                Section("Server") {
                    LabeledContent("Adresa", value: server.baseURL.absoluteString)
                    LabeledContent("Data", value: "Soubory → Na iPhonu → Travian Local")
                    Button("Obnovit databázi…", role: .destructive) { confirmReset = true }
                }
            }
            .navigationTitle("Nástroje")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar { ToolbarItem(placement: .confirmationAction) { Button("Hotovo") { dismiss() } } }
            .onAppear { refresh() }
            .confirmationDialog("Smazat všechny hráče a vesnice a začít s čistou databází?",
                                isPresented: $confirmReset, titleVisibility: .visible) {
                Button("Obnovit databázi", role: .destructive) {
                    run("Databáze obnovena") { try GameFiles.installFreshDatabase() }
                    browser.load(server.url("anmelden.php"))
                }
            }
        }
    }

    private func refresh() {
        speed = GameDB.speed() ?? speed
        players = GameDB.players()
    }

    private func run(_ title: String, _ action: () throws -> Void) {
        do {
            try action()
            message = "✓ \(title)"
            browser.reload()
            refresh()
        } catch {
            message = "✖ \(title): \(error.localizedDescription)"
        }
    }

    private func tool(_ title: String, _ icon: String, _ help: String?, _ action: @escaping () throws -> Void) -> some View {
        Button { run(title, action) } label: {
            VStack(alignment: .leading, spacing: 2) {
                Label(title, systemImage: icon)
                if let help { Text(help).font(.caption).foregroundStyle(.secondary) }
            }
        }
    }
}

@MainActor
struct SecondPlayerView: View {
    @EnvironmentObject var server: GameServer
    @Environment(\.dismiss) private var dismiss
    @StateObject private var browser = BrowserModel(ephemeral: true)

    var body: some View {
        NavigationView {
            WebView(webView: browser.webView)
                .ignoresSafeArea(edges: .bottom)
                .navigationTitle("Druhý hráč")
                .navigationBarTitleDisplayMode(.inline)
                .toolbar {
                    ToolbarItem(placement: .cancellationAction) { Button("Zavřít") { dismiss() } }
                    ToolbarItem(placement: .primaryAction) {
                        Button { browser.reload() } label: { Image(systemName: "arrow.clockwise") }
                    }
                }
                .onAppear { browser.load(server.url("login.php")) }
        }
    }
}

@MainActor
struct LogsView: View {
    @Environment(\.dismiss) private var dismiss
    @State private var which = 0
    @State private var text = ""
    private let files: [(String, URL)] = [
        ("Server", Paths.serverLog), ("PHP chyby", Paths.phpErrorLog), ("SQL chyby", Paths.sqlErrorLog),
    ]

    var body: some View {
        NavigationView {
            VStack(spacing: 0) {
                Picker("", selection: $which) {
                    ForEach(files.indices, id: \.self) { Text(files[$0].0).tag($0) }
                }
                .pickerStyle(.segmented)
                .padding()
                ScrollView {
                    Text(text.isEmpty ? "(prázdné)" : text)
                        .font(.system(size: 11, design: .monospaced))
                        .textSelection(.enabled)
                        .frame(maxWidth: .infinity, alignment: .leading)
                        .padding(.horizontal)
                }
            }
            .navigationTitle("Logy")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .cancellationAction) { Button("Zavřít") { dismiss() } }
                ToolbarItem(placement: .primaryAction) { Button("Obnovit") { load() } }
            }
            .onAppear { load() }
            .onChange(of: which) { _ in load() }
        }
    }

    private func load() {
        let url = files[which].1
        guard let data = try? Data(contentsOf: url) else { text = ""; return }
        let tail = data.suffix(60_000)
        text = String(decoding: tail, as: UTF8.self)
            .split(separator: "\n").filter { !$0.hasSuffix(" Accepted") && !$0.hasSuffix(" Closing") }
            .suffix(400).joined(separator: "\n")
    }
}
