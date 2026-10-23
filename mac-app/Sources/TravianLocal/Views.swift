import SwiftUI
import AppKit

@MainActor
struct ContentView: View {
    @EnvironmentObject var server: ServerController
    @EnvironmentObject var settings: AppSettings
    @StateObject private var browser = BrowserModel()
    @State private var showLogs = true

    var body: some View {
        NavigationSplitView {
            SidebarView(browser: browser)
                .navigationSplitViewColumnWidth(min: 260, ideal: 290, max: 360)
        } detail: {
            VSplitView {
                BrowserPane(model: browser, baseURL: server.gameURL)
                    .frame(minHeight: 300)
                if showLogs {
                    LogPanel()
                        .frame(minHeight: 140, idealHeight: 220)
                }
            }
        }
        .toolbar {
            ToolbarItem(placement: .navigation) {
                StatusBadge(state: server.state)
            }
            ToolbarItemGroup {
                Button { showLogs.toggle() } label: {
                    Label("Logy", systemImage: server.unreadErrors > 0 ? "exclamationmark.bubble.fill" : "text.alignleft")
                }
                .help("Zobrazit/skrýt logy")
                Button { PlayerWindows.open(baseURL: server.gameURL) } label: {
                    Label("Další hráč", systemImage: "person.2")
                }
                .help("Nové okno se samostatným přihlášením (druhý hráč)")
                .disabled(server.state != .running)
            }
        }
        .navigationTitle("Travian Local")
        .onChange(of: server.state) { state in
            if state == .running { browser.load(server.url("login.php")) }
        }
        .task {
            if settings.startAutomatically && server.tools.missing.isEmpty && AppSettings.isGameFolder(settings.gamePath) {
                await server.start()
            }
        }
    }
}

@MainActor
struct StatusBadge: View {
    let state: ServerState
    var color: Color {
        switch state {
        case .running: return .green
        case .starting, .stopping: return .orange
        case .failed: return .red
        case .stopped: return .secondary
        }
    }
    var body: some View {
        HStack(spacing: 6) {
            Circle().fill(color).frame(width: 9, height: 9)
            Text(state.label).font(.callout)
        }
        .padding(.horizontal, 8)
    }
}

@MainActor
struct SidebarView: View {
    @EnvironmentObject var server: ServerController
    @EnvironmentObject var settings: AppSettings
    @ObservedObject var browser: BrowserModel
    @State private var speed = 50
    @State private var confirmReset = false
    @State private var playersText = ""

    private var running: Bool { server.state == .running }

    var body: some View {
        List {
            Section("Server") {
                VStack(alignment: .leading, spacing: 8) {
                    HStack {
                        if running || server.state == .starting {
                            Button { server.stop() } label: { Label("Zastavit", systemImage: "stop.fill") }
                                .keyboardShortcut(".", modifiers: .command)
                        } else {
                            Button { Task { await server.start() } } label: { Label("Spustit", systemImage: "play.fill") }
                                .keyboardShortcut("s", modifiers: [.command, .shift])
                                .buttonStyle(.borderedProminent)
                                .disabled(server.state == .stopping)
                        }
                        if server.state == .starting || server.busy { ProgressView().controlSize(.small) }
                    }
                    Text(server.status)
                        .font(.caption)
                        .foregroundStyle(isError ? Color.red : Color.secondary)
                        .textSelection(.enabled)
                        .fixedSize(horizontal: false, vertical: true)
                }
                .padding(.vertical, 4)

                if !server.tools.missing.isEmpty {
                    SetupHint(missing: server.tools.missing)
                }
                if !AppSettings.isGameFolder(settings.gamePath) {
                    VStack(alignment: .leading, spacing: 6) {
                        Text("Není vybraná složka hry (obsahuje travian.sql).").font(.caption).foregroundStyle(.red)
                        Button("Vybrat složku hry…") { SettingsView.chooseGameFolder(settings) }
                    }
                }
            }

            Section("Rychlé odkazy") {
                link("Přihlášení", "login.php", "person.crop.circle")
                link("Registrace", "anmelden.php", "person.badge.plus")
                link("Vesnice", "dorf1.php", "house")
                link("Centrum vesnice", "dorf2.php", "building.2")
                link("Shromaždiště", "build.php?id=39", "flag")
                link("Hlášení", "berichte.php", "doc.text")
                link("Admin panel", "panel/", "lock.shield")
                Text("Admin: Multihunter / admin123").font(.caption2).foregroundStyle(.secondary)
            }

            Section("Testovací nástroje") {
                tool("Dokončit vše hned", "forward.end.fill",
                     help: "Stavby, výzkumy, trénink i pohyby vojsk/obchodníků doběhnou při dalším načtení stránky") {
                    if await server.fastForward() { browser.reload() }
                }
                tool("Naplnit sklady", "shippingbox.fill") { if await server.fillResources() { browser.reload() } }
                tool("+1000 zlata", "dollarsign.circle.fill") { if await server.addGold() { browser.reload() } }
                tool("Zrušit ochranu nováčků", "shield.slash") { _ = await server.removeProtection() }
                HStack {
                    Text("Rychlost ×")
                    Picker("", selection: $speed) {
                        ForEach([1, 3, 5, 10, 50, 100, 500, 1000], id: \.self) { Text("\($0)").tag($0) }
                    }
                    .labelsHidden()
                    .frame(width: 80)
                    Button("Nastavit") { Task { _ = await server.setSpeed(speed) } }
                }
                .disabled(!running || server.busy)
                Button { Task { playersText = await server.players() } } label: { Label("Seznam hráčů", systemImage: "list.bullet") }
                    .disabled(!running)
                if !playersText.isEmpty {
                    Text(playersText).font(.system(size: 10, design: .monospaced)).textSelection(.enabled)
                }
                Button(role: .destructive) { confirmReset = true } label: {
                    Label("Obnovit databázi…", systemImage: "arrow.counterclockwise")
                }
                .disabled(server.busy || server.state == .starting)
            }

            Section("Prostředí") {
                info("PHP", server.phpVersion.isEmpty ? (server.tools.php ?? "nenalezeno") : server.phpVersion)
                info("MariaDB", server.mariadbVersion.isEmpty ? (server.tools.mariadbd ?? "nenalezeno") : shortVersion(server.mariadbVersion))
                info("Hra", server.gameURL.absoluteString)
                Button { server.openDataFolder() } label: { Label("Otevřít datovou složku", systemImage: "folder") }
            }
        }
        .listStyle(.sidebar)
        .task(id: server.state) {
            if running, let s = await server.currentSpeed() { speed = s }
        }
        .confirmationDialog("Smazat všechny hráče a vesnice a naimportovat čistou databázi?",
                            isPresented: $confirmReset) {
            Button("Obnovit databázi", role: .destructive) {
                Task { await server.resetDatabase(); browser.load(server.url("anmelden.php")) }
            }
        }
    }

    private var isError: Bool { if case .failed = server.state { return true }; return server.status.hasPrefix("✖") }

    private func link(_ title: String, _ path: String, _ icon: String) -> some View {
        Button { browser.load(server.url(path)) } label: { Label(title, systemImage: icon) }
            .buttonStyle(.plain)
            .disabled(!running)
    }

    private func tool(_ title: String, _ icon: String, help: String? = nil, action: @escaping () async -> Void) -> some View {
        Button { Task { await action() } } label: { Label(title, systemImage: icon) }
            .disabled(!running || server.busy)
            .help(help ?? title)
    }

    private func info(_ key: String, _ value: String) -> some View {
        VStack(alignment: .leading, spacing: 1) {
            Text(key).font(.caption2).foregroundStyle(.secondary)
            Text(value).font(.caption).textSelection(.enabled).lineLimit(2)
        }
    }

    private func shortVersion(_ s: String) -> String {
        if let r = s.range(of: #"\d+\.\d+\.\d+-MariaDB"#, options: .regularExpression) { return String(s[r]) }
        return s
    }
}

@MainActor
struct SetupHint: View {
    let missing: [String]
    @EnvironmentObject var server: ServerController
    let command = "brew install php mariadb"
    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            Text("Chybí: \(missing.joined(separator: ", "))").font(.caption).foregroundStyle(.red)
            Text("V Terminálu spusťte:").font(.caption)
            HStack {
                Text(command).font(.system(.caption, design: .monospaced)).textSelection(.enabled)
                Button { NSPasteboard.general.clearContents(); NSPasteboard.general.setString(command, forType: .string) } label: {
                    Image(systemName: "doc.on.doc")
                }.buttonStyle(.borderless).help("Kopírovat")
            }
            Button("Zkontrolovat znovu") { server.refreshTools() }
        }
        .padding(.vertical, 4)
    }
}

@MainActor
struct LogPanel: View {
    @EnvironmentObject var server: ServerController
    @State private var channel: LogChannel = .server
    @State private var sqlText = "SELECT id, username, tribe, gold FROM users;"
    @State private var sqlOutput = ""
    @State private var showConsole = false

    var body: some View {
        VStack(spacing: 0) {
            HStack {
                Picker("", selection: $channel) {
                    ForEach(LogChannel.allCases) { c in
                        Text(title(for: c)).tag(c)
                    }
                }
                .pickerStyle(.segmented)
                .labelsHidden()
                .disabled(showConsole)
                Toggle(isOn: $showConsole) { Label("SQL konzole", systemImage: "terminal") }
                    .toggleStyle(.button)
                Spacer()
                Button { server.clearLog(channel) } label: { Image(systemName: "trash") }
                    .buttonStyle(.borderless).help("Vyčistit")
                    .disabled(showConsole)
            }
            .padding(.horizontal, 8).padding(.vertical, 4)
            .background(.bar)

            if showConsole {
                HStack(alignment: .top, spacing: 0) {
                    VStack(alignment: .trailing) {
                        TextEditor(text: $sqlText)
                            .font(.system(.body, design: .monospaced))
                        Button("Spustit SQL (⌘↩)") { Task { sqlOutput = await server.runUserSQL(sqlText) } }
                            .keyboardShortcut(.return, modifiers: .command)
                            .padding(6)
                    }
                    .frame(minWidth: 240, idealWidth: 320)
                    Divider()
                    ScrollView([.vertical, .horizontal]) {
                        Text(sqlOutput).font(.system(size: 11, design: .monospaced))
                            .textSelection(.enabled)
                            .frame(maxWidth: .infinity, alignment: .leading)
                            .padding(6)
                    }
                }
            } else {
                LogText(lines: server.logs[channel] ?? [])
            }
        }
        .onChange(of: channel) { c in if c == .phpErrors || c == .sqlErrors { server.markErrorsRead() } }
    }

    private func title(for c: LogChannel) -> String {
        let n = server.logs[c]?.count ?? 0
        if (c == .phpErrors || c == .sqlErrors) && n > 0 { return "\(c.rawValue) (\(n))" }
        return c.rawValue
    }
}

@MainActor
struct LogText: View {
    let lines: [String]
    var body: some View {
        ScrollViewReader { proxy in
            ScrollView {
                Text(lines.suffix(800).joined(separator: "\n"))
                    .font(.system(size: 11, design: .monospaced))
                    .textSelection(.enabled)
                    .frame(maxWidth: .infinity, alignment: .leading)
                    .padding(6)
                Color.clear.frame(height: 1).id("bottom")
            }
            .onChange(of: lines.count) { _ in proxy.scrollTo("bottom", anchor: .bottom) }
            .onAppear { proxy.scrollTo("bottom", anchor: .bottom) }
        }
        .background(Color(nsColor: .textBackgroundColor))
    }
}

@MainActor
struct SettingsView: View {
    @EnvironmentObject var settings: AppSettings
    @EnvironmentObject var server: ServerController

    var body: some View {
        Form {
            Section("Hra") {
                HStack {
                    TextField("Složka hry", text: $settings.gamePath)
                    Button("Vybrat…") { SettingsView.chooseGameFolder(settings) }
                }
                if !AppSettings.isGameFolder(settings.gamePath) {
                    Text("Složka musí obsahovat application/config.php a travian.sql").font(.caption).foregroundStyle(.red)
                }
                TextField("Port hry", value: $settings.phpPort, format: .number.grouping(.never))
                TextField("Port databáze", value: $settings.dbPort, format: .number.grouping(.never))
                Toggle("Spustit server po otevření aplikace", isOn: $settings.startAutomatically)
            }
            Section("Testování") {
                Toggle("Zobrazovat SQL chyby přímo ve stránce", isOn: $settings.showSQLErrorsInPage)
                Toggle("Povolit více účtů z jedné IP (127.0.0.1)", isOn: $settings.allowMultiAccounts)
                Text("Změny se projeví po restartu serveru.").font(.caption).foregroundStyle(.secondary)
            }
            Section("Programy (prázdné = najít automaticky)") {
                TextField("Cesta k php", text: $settings.phpPath, prompt: Text(server.tools.php ?? "/opt/homebrew/bin/php"))
                TextField("Cesta k mariadbd", text: $settings.mariadbPath, prompt: Text(server.tools.mariadbd ?? "/opt/homebrew/bin/mariadbd"))
                Button("Znovu vyhledat") { server.refreshTools() }
            }
        }
        .formStyle(.grouped)
        .frame(width: 520)
        .padding()
    }

    @MainActor
    static func chooseGameFolder(_ settings: AppSettings) {
        let panel = NSOpenPanel()
        panel.canChooseDirectories = true
        panel.canChooseFiles = false
        panel.allowsMultipleSelection = false
        panel.message = "Vyberte složku „game“ (obsahuje travian.sql)"
        if panel.runModal() == .OK, let url = panel.url {
            if AppSettings.isGameFolder(url.path) {
                settings.gamePath = url.path
            } else if AppSettings.isGameFolder(url.appendingPathComponent("game").path) {
                settings.gamePath = url.appendingPathComponent("game").path
            } else {
                let alert = NSAlert()
                alert.messageText = "Tahle složka neobsahuje hru (chybí application/config.php nebo travian.sql)."
                alert.runModal()
            }
        }
    }
}
