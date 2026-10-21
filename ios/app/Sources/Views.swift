import SwiftUI
import UIKit

@MainActor
struct RootView: View {
    @EnvironmentObject var runtime: GameRuntime
    @StateObject private var browser = BrowserModel()
    @Environment(\.scenePhase) private var scenePhase
    @State private var sheet: Sheet?
    /// Bottom bar folded away (a small button brings it back), remembered between launches.
    @AppStorage("OpenVillage.toolbarHidden") private var barHidden = false

    enum Sheet: String, Identifiable {
        case tools, logs, sql, secondPlayer, settings
        var id: String { rawValue }
    }

    var body: some View {
        ZStack {
            // Classic bar: the web view ends above it. Liquid Glass bar (iOS 26): the page runs under the floating
            // bar and gets that much room at its end, so the end of long pages is never hidden behind it.
            WebView(webView: browser.webView)
                .ignoresSafeArea(edges: Self.glassBar ? .bottom : [])
            switch runtime.state {
            case .idle:
                StatusCard(icon: "hourglass", text: "Spouštím…", spinner: true)
            case .working(let message):
                StatusCard(icon: "hourglass", text: message, spinner: true)
            case .failed(let message):
                StatusCard(icon: "exclamationmark.triangle", text: message, spinner: false) {
                    Button("Zkusit znovu") { runtime.start() }.buttonStyle(.borderedProminent)
                    Button("Logy") { sheet = .logs }
                }
            case .running:
                if let error = browser.lastError {
                    StatusCard(icon: "bolt.horizontal.circle", text: error, spinner: false) {
                        Button("Načíst znovu") { browser.load(runtime.baseURL) }.buttonStyle(.borderedProminent)
                    }
                }
            }
        }
        .safeAreaInset(edge: .bottom) { if !Self.glassBar && !barHidden { toolbar } }
        .overlay(alignment: .bottom) { if Self.glassBar && !barHidden { glassToolbar } }
        .overlay(alignment: .bottomTrailing) { if barHidden { showBarButton } }
        .onChange(of: barHidden) { hidden in if hidden { browser.setBottomInset(0) } }
        .alert("Aplikace minule spadla při startu", isPresented: Binding(
            get: { runtime.previousCrash != nil }, set: { if !$0 { runtime.previousCrash = nil } })) {
            Button("Logy") { runtime.previousCrash = nil; sheet = .logs }
            Button("OK", role: .cancel) { runtime.previousCrash = nil }
        } message: {
            Text(runtime.previousCrash ?? "")
        }
        .onAppear { runtime.start() }
        .onChange(of: runtime.state) { state in
            if state == .running && browser.webView.url == nil { browser.load(runtime.baseURL) }
        }
        .onChange(of: runtime.styleVersion) { _ in browser.reload() }
        .onChange(of: scenePhase) { phase in
            if phase == .active { runtime.ensureRunning() }
        }
        .sheet(item: $sheet) { which in
            switch which {
            case .tools: ToolsView(browser: browser)
            case .logs: LogsView()
            case .sql: SQLConsoleView()
            case .secondPlayer: SecondPlayerView()
            case .settings: SettingsView()
            }
        }
    }

    /// Liquid Glass bottom bar: built with the iOS 26 SDK and running on iOS 26 or later.
    private static var glassBar: Bool {
        #if compiler(>=6.2)
        if #available(iOS 26.0, *) { return true }
        #endif
        return false
    }

    /// Small round button that brings the folded bottom bar back.
    @ViewBuilder private var showBarButton: some View {
        let button = Button { withAnimation(.easeOut(duration: 0.2)) { barHidden = false } } label: {
            Image(systemName: "chevron.up").font(.system(size: 16, weight: .semibold)).frame(width: 44, height: 44)
        }
        .buttonStyle(.plain)
        .foregroundStyle(.primary)
        .accessibilityLabel("Zobrazit lištu")
        #if compiler(>=6.2)
        if #available(iOS 26.0, *) {
            button.glassEffect(.regular, in: .circle).padding(.trailing, 16).padding(.bottom, 4)
        } else {
            button.background(.bar, in: Circle()).padding(.trailing, 16).padding(.bottom, 4)
        }
        #else
        button.background(.bar, in: Circle()).padding(.trailing, 16).padding(.bottom, 4)
        #endif
    }

    private var toolbar: some View {
        toolbarButtons
            .padding(.horizontal, 6)
            .padding(.top, 4)
            .background(.bar)
    }

    @ViewBuilder private var glassToolbar: some View {
        #if compiler(>=6.2)
        if #available(iOS 26.0, *) {
            // One clear glass capsule as wide as on a phone in portrait (also in landscape), plain buttons inside.
            toolbarButtons
                .buttonStyle(.plain)
                .menuStyle(.button)
                .foregroundStyle(.primary)
                .padding(.horizontal, 6)
                .padding(.vertical, 2)
                .frame(maxWidth: 420)
                .glassEffect(.regular, in: .capsule)
                .padding(.horizontal, 14)
                .padding(.bottom, 2)
                .background(GeometryReader { geo in
                    Color.clear
                        .onAppear { browser.setBottomInset(geo.size.height + 8) }
                        .onChange(of: geo.size.height) { height in browser.setBottomInset(height + 8) }
                })
        }
        #endif
    }

    private var toolbarButtons: some View {
        HStack(spacing: 0) {
            barButton("chevron.left", "Zpět") { browser.webView.goBack() }.disabled(!browser.canGoBack)
            barButton("arrow.clockwise", "Obnovit") { browser.reload() }
            Menu {
                Button { go("game/dorf1.php") } label: { Label("Vesnice", systemImage: "house") }
                Button { go("game/dorf2.php") } label: { Label("Centrum vesnice", systemImage: "building.2") }
                Button { go("game/karte.php") } label: { Label("Mapa", systemImage: "map") }
                Button { go("game/build.php?id=39") } label: { Label("Shromaždiště", systemImage: "flag") }
                Button { go("game/berichte.php") } label: { Label("Hlášení", systemImage: "doc.text") }
                Button { go("game/messages.php") } label: { Label("Zprávy", systemImage: "envelope") }
                Button { go("game/statistiken.php") } label: { Label("Statistiky", systemImage: "chart.bar") }
                Divider()
                Button { go("") } label: { Label("Spouštěč / registrace", systemImage: "person.badge.plus") }
                Button { go("game/login.php") } label: { Label("Přihlášení", systemImage: "person.crop.circle") }
                Button { go("game/admin.php") } label: { Label("Admin panel (Support)", systemImage: "lock.shield") }
            } label: {
                barLabel("square.grid.2x2", "Menu")
            }
            barButton("wand.and.stars", "Nástroje") { sheet = .tools }
            barButton("person.2", "2. hráč") { sheet = .secondPlayer }
            Menu {
                Button { sheet = .logs } label: { Label("Logy", systemImage: "text.alignleft") }
                Button { sheet = .sql } label: { Label("SQL konzole", systemImage: "cylinder.split.1x2") }
                Button { sheet = .settings } label: { Label("Nastavení světa", systemImage: "gearshape") }
            } label: {
                barLabel("ellipsis.circle", "Více")
            }
            Button { withAnimation(.easeOut(duration: 0.2)) { barHidden = true } } label: {
                Image(systemName: "chevron.down").font(.system(size: 15, weight: .semibold))
                    .frame(width: 30).padding(.vertical, 12)
            }
            .accessibilityLabel("Schovat lištu")
        }
    }

    private func go(_ path: String) { browser.load(runtime.url(path)) }

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

// MARK: - Tools

@MainActor
struct ToolsView: View {
    @ObservedObject var browser: BrowserModel
    @EnvironmentObject var runtime: GameRuntime
    @Environment(\.dismiss) private var dismiss
    @State private var message = ""
    @State private var busy = false
    @State private var confirmReset = false
    @State private var players: [[String]] = []
    @State private var target = "all"

    private var userFilter: String { target == "all" ? "id > 2" : "id = \(Int(target) ?? -1)" }
    private var ownerFilter: String { target == "all" ? "owner > 2" : "owner = \(Int(target) ?? -1)" }
    private var kidFilter: String { target == "all" ? "1=1" : "kid IN (SELECT kid FROM vdata WHERE owner = \(Int(target) ?? -1))" }

    var body: some View {
        NavigationView {
            Form {
                Section {
                    Picker("Hráč", selection: $target) {
                        Text("Všichni hráči").tag("all")
                        ForEach(players.indices, id: \.self) { i in Text(players[i][1]).tag(players[i][0]) }
                    }
                    tool("Dokončit fronty hned", "forward.end.fill",
                         "Stavby, bourání, výzkum, výcvik a pohyby vojsk – dokončí je worker během vteřiny") {
                        """
                        UPDATE building_upgrade SET commence = LEAST(commence, UNIX_TIMESTAMP()) WHERE \(kidFilter);
                        UPDATE demolition SET end_time = LEAST(end_time, UNIX_TIMESTAMP()) WHERE \(kidFilter);
                        UPDATE research SET end_time = LEAST(end_time, UNIX_TIMESTAMP()) WHERE \(kidFilter);
                        UPDATE training SET commence = 0 WHERE \(kidFilter);
                        UPDATE movement SET end_time = LEAST(end_time, CAST(UNIX_TIMESTAMP(NOW(3)) * 1000 AS UNSIGNED)) WHERE proc = 0;
                        """
                    }
                    tool("Naplnit sklady", "shippingbox.fill", nil) {
                        "UPDATE vdata SET wood = maxstore, clay = maxstore, iron = maxstore, crop = maxcrop WHERE \(ownerFilter);"
                    }
                    tool("+1000 zlata", "dollarsign.circle.fill", nil) { "UPDATE users SET gift_gold = gift_gold + 1000 WHERE \(userFilter);" }
                    tool("+10 000 stříbra", "circle.hexagongrid.fill", nil) { "UPDATE users SET silver = silver + 10000 WHERE \(userFilter);" }
                    tool("Zrušit ochranu začátečníka", "shield.slash", nil) { "UPDATE users SET protection = 0 WHERE \(userFilter);" }
                } header: { Text("Testovací nástroje") } footer: { if !message.isEmpty { Text(message) } }

                Section("Hráči ve světě") {
                    if players.isEmpty { Text("Zatím nikdo – zaregistrujte se na spouštěči.").foregroundStyle(.secondary) }
                    ForEach(players.indices, id: \.self) { i in
                        let p = players[i]
                        HStack {
                            VStack(alignment: .leading) {
                                Text(p[1]).bold()
                                Text("\(p[2]) · vesnice \(p[5]) · populace \(p[6])").font(.caption).foregroundStyle(.secondary)
                            }
                            Spacer()
                            Text("\(p[3]) 🪙").font(.caption)
                        }
                    }
                }

                Section("Server") {
                    LabeledContent("Adresa", value: runtime.baseURL.absoluteString)
                    LabeledContent("Admin", value: "Support / \(runtime.secrets.adminPassword)")
                        .textSelection(.enabled)
                    Toggle("Automatizace (worker)", isOn: $runtime.tickEnabled)
                    if !runtime.lastTick.isEmpty {
                        Text(runtime.lastTick).font(.caption2.monospaced()).foregroundStyle(.secondary).lineLimit(3)
                    }
                    Button("Vyprázdnit cache") { Task { _ = try? await runtime.control("flush", form: [:]); message = "✓ Cache vyprázdněna" } }
                    Button("Nový svět…", role: .destructive) { confirmReset = true }
                }
            }
            .disabled(busy)
            .navigationTitle("Nástroje")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar { ToolbarItem(placement: .confirmationAction) { Button("Hotovo") { dismiss() } } }
            .task { await refresh() }
            .confirmationDialog("Smazat všechny hráče a vesnice a vytvořit nový svět?",
                                isPresented: $confirmReset, titleVisibility: .visible) {
                Button("Vytvořit nový svět", role: .destructive) {
                    Task {
                        busy = true
                        do { try await runtime.newWorld(); message = "✓ Nový svět vytvořen" } catch { message = "✖ \(error.localizedDescription)" }
                        busy = false
                        browser.load(runtime.baseURL)
                        await refresh()
                    }
                }
            }
        }
    }

    private func refresh() async {
        let r = await runtime.sql("""
            SELECT u.id, u.name,
                   CASE u.race WHEN 1 THEN 'Římané' WHEN 2 THEN 'Germáni' WHEN 3 THEN 'Galové' ELSE u.race END,
                   u.gift_gold + u.bought_gold, u.silver,
                   (SELECT COUNT(*) FROM vdata v WHERE v.owner = u.id), u.total_pop
            FROM users u WHERE u.id > 2 ORDER BY u.id
            """)
        players = r.rows.filter { $0.count >= 7 }
    }

    private func tool(_ title: String, _ icon: String, _ help: String?, _ sql: @escaping () -> String) -> some View {
        Button {
            Task {
                busy = true
                let error = await runtime.devAction(sql())
                message = error == nil ? "✓ \(title)" : "✖ \(title): \(error!)"
                busy = false
                browser.reload()
                await refresh()
            }
        } label: {
            VStack(alignment: .leading, spacing: 2) {
                Label(title, systemImage: icon)
                if let help { Text(help).font(.caption).foregroundStyle(.secondary) }
            }
        }
    }
}

// MARK: - Second player

@MainActor
struct SecondPlayerView: View {
    @EnvironmentObject var runtime: GameRuntime
    @Environment(\.dismiss) private var dismiss
    @StateObject private var browser = BrowserModel(profile: SecondPlayerView.profileID)

    static let profileID: UUID = {
        let key = "OpenVillage.secondPlayerProfile"
        if let s = UserDefaults.standard.string(forKey: key), let id = UUID(uuidString: s) { return id }
        let id = UUID()
        UserDefaults.standard.set(id.uuidString, forKey: key)
        return id
    }()

    var body: some View {
        NavigationView {
            WebView(webView: browser.webView)
                .ignoresSafeArea(edges: .bottom)
                .navigationTitle("Druhý hráč")
                .navigationBarTitleDisplayMode(.inline)
                .toolbar {
                    ToolbarItem(placement: .cancellationAction) { Button("Zavřít") { dismiss() } }
                    ToolbarItemGroup(placement: .primaryAction) {
                        Menu {
                            Button("Vesnice") { browser.load(runtime.url("game/dorf1.php")) }
                            Button("Mapa") { browser.load(runtime.url("game/karte.php")) }
                            Button("Zprávy") { browser.load(runtime.url("game/messages.php")) }
                            Button("Spouštěč / registrace") { browser.load(runtime.baseURL) }
                            Button("Odhlásit (smazat cookies)", role: .destructive) {
                                Task { await browser.clearCookies(); browser.load(runtime.baseURL) }
                            }
                        } label: { Image(systemName: "list.bullet") }
                        Button { browser.reload() } label: { Image(systemName: "arrow.clockwise") }
                    }
                }
                .onAppear { if browser.webView.url == nil { browser.load(runtime.baseURL) } }
        }
    }
}

// MARK: - Logs

@MainActor
struct LogsView: View {
    @Environment(\.dismiss) private var dismiss
    @State private var which = 0
    @State private var text = ""
    @State private var onlyProblems = false
    private let files: [(String, URL)] = [
        ("PHP chyby", Paths.phpErrorLog), ("Server", Paths.serverLog), ("Worker", Paths.tickLog),
        ("MariaDB", Paths.mariadbLog), ("Hra", Paths.gameErrorLog), ("Start", Paths.setupLog),
    ]

    var body: some View {
        NavigationView {
            VStack(spacing: 0) {
                Picker("", selection: $which) {
                    ForEach(files.indices, id: \.self) { Text(files[$0].0).tag($0) }
                }
                .pickerStyle(.segmented)
                .padding([.horizontal, .top])
                Toggle("Jen chyby a varování", isOn: $onlyProblems).font(.caption).padding(.horizontal)
                ScrollViewReader { proxy in
                    ScrollView {
                        Text(text.isEmpty ? "(prázdné)" : text)
                            .font(.system(size: 11, design: .monospaced))
                            .textSelection(.enabled)
                            .frame(maxWidth: .infinity, alignment: .leading)
                            .padding(.horizontal)
                        Color.clear.frame(height: 1).id("end")
                    }
                    .onChange(of: text) { _ in proxy.scrollTo("end") }
                }
            }
            .navigationTitle("Logy")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .cancellationAction) { Button("Zavřít") { dismiss() } }
                ToolbarItemGroup(placement: .primaryAction) {
                    ShareLink(item: files[which].1) { Image(systemName: "square.and.arrow.up") }
                    Button { load() } label: { Image(systemName: "arrow.clockwise") }
                }
            }
            .onAppear { load() }
            .onChange(of: which) { _ in load() }
            .onChange(of: onlyProblems) { _ in load() }
        }
    }

    private func load() {
        guard let data = try? Data(contentsOf: files[which].1) else { text = ""; return }
        let lines = String(decoding: data.suffix(120_000), as: UTF8.self)
            .split(separator: "\n", omittingEmptySubsequences: false)
            .filter { !$0.hasSuffix(" Accepted") && !$0.hasSuffix(" Closing") }
            .filter { line in
                guard onlyProblems else { return true }
                let l = line.lowercased()
                return l.contains("error") || l.contains("warning") || l.contains("fatal") || l.contains("exception")
                    || l.contains("deprecated") || l.contains("notice") || l.contains(" [5") || l.contains(" [4")
            }
        text = lines.suffix(600).joined(separator: "\n")
    }
}

// MARK: - SQL console

@MainActor
struct SQLConsoleView: View {
    @EnvironmentObject var runtime: GameRuntime
    @Environment(\.dismiss) private var dismiss
    @State private var query = "SELECT id, name, race, gift_gold, silver, total_pop FROM users ORDER BY id;"
    @State private var global = false
    @State private var result = GameRuntime.SQLResult()
    @State private var running = false

    static let snippets: [(String, String)] = [
        ("Hráči", "SELECT id, name, race, gift_gold + bought_gold AS gold, silver, total_pop FROM users ORDER BY id;"),
        ("Vesnice", "SELECT kid, owner, name, pop, ROUND(wood) wood, ROUND(clay) clay, ROUND(iron) iron, ROUND(crop) crop FROM vdata WHERE owner > 2;"),
        ("Fronta staveb", "SELECT id, kid, building_field, FROM_UNIXTIME(commence) AS done_at FROM building_upgrade ORDER BY commence;"),
        ("Pohyby vojsk", "SELECT id, kid, to_kid, mode, attack_type, FROM_UNIXTIME(end_time DIV 1000) AS arrives FROM movement WHERE proc = 0;"),
        ("Karanténa úloh", "SELECT task_table, task_id, attempts, last_error FROM scheduled_task_failures;"),
        ("Konfigurace", "SELECT * FROM config;"),
    ]

    var body: some View {
        NavigationView {
            VStack(spacing: 8) {
                TextEditor(text: $query)
                    .font(.system(size: 13, design: .monospaced))
                    .autocorrectionDisabled()
                    .textInputAutocapitalization(.never)
                    .frame(height: 120)
                    .overlay(RoundedRectangle(cornerRadius: 8).stroke(.quaternary))
                HStack {
                    Picker("", selection: $global) { Text("herní DB").tag(false); Text("globální").tag(true) }
                        .pickerStyle(.segmented).frame(maxWidth: 220)
                    Menu("Šablony") { ForEach(Self.snippets, id: \.0) { s in Button(s.0) { query = s.1; global = false } } }
                    Spacer()
                    Button { Task { await run() } } label: { running ? AnyView(ProgressView()) : AnyView(Label("Spustit", systemImage: "play.fill")) }
                        .buttonStyle(.borderedProminent).disabled(running)
                }
                if let error = result.error {
                    Text(error).font(.caption.monospaced()).foregroundStyle(.red).frame(maxWidth: .infinity, alignment: .leading)
                } else if !result.columns.isEmpty {
                    Text("\(result.rows.count) řádků · \(result.ms) ms").font(.caption).foregroundStyle(.secondary)
                        .frame(maxWidth: .infinity, alignment: .leading)
                } else if result.ms > 0 {
                    Text("OK · ovlivněno \(result.affected) řádků").font(.caption).foregroundStyle(.secondary)
                        .frame(maxWidth: .infinity, alignment: .leading)
                }
                ScrollView([.horizontal, .vertical]) {
                    Grid(alignment: .leading, horizontalSpacing: 12, verticalSpacing: 4) {
                        if !result.columns.isEmpty {
                            GridRow { ForEach(result.columns.indices, id: \.self) { Text(result.columns[$0]).bold() } }
                            Divider()
                        }
                        ForEach(result.rows.indices, id: \.self) { r in
                            GridRow {
                                ForEach(result.rows[r].indices, id: \.self) { c in
                                    Text(result.rows[r][c]).lineLimit(1).foregroundStyle(result.rows[r][c] == "NULL" ? .secondary : .primary)
                                }
                            }
                        }
                    }
                    .font(.system(size: 12, design: .monospaced))
                    .textSelection(.enabled)
                    .padding(.vertical, 4)
                }
            }
            .padding()
            .navigationTitle("SQL konzole")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar { ToolbarItem(placement: .cancellationAction) { Button("Zavřít") { dismiss() } } }
        }
    }

    private func run() async {
        running = true
        result = await runtime.sql(query, global: global)
        running = false
    }
}

// MARK: - Settings

@MainActor
struct SettingsView: View {
    @EnvironmentObject var runtime: GameRuntime
    @Environment(\.dismiss) private var dismiss
    @State private var world = WorldSettings.load()

    var body: some View {
        NavigationView {
            Form {
                Section {
                    Picker("Grafika", selection: Binding(get: { world.style }, set: { world.gpack = $0 })) {
                        ForEach(WorldSettings.availableStyles, id: \.hash) { Text($0.name).tag($0.hash) }
                    }
                } header: { Text("Vzhled") } footer: { Text("Grafický balíček (gpack) – změní se hned po uložení.") }
                Section {
                    Stepper("Rychlost: ×\(world.speed)", value: $world.speed, in: 1...1000, step: world.speed < 10 ? 1 : 10)
                    Stepper("Délka kola: \(world.roundDays) dní", value: $world.roundDays, in: 1...365)
                } footer: { Text("Platí po novém spuštění aplikace.") }
                Section {
                    Stepper("Rychlost hrdiny: ×\(world.heroSpeedFactor)",
                            value: Binding(get: { world.heroSpeedFactor }, set: { world.heroSpeed = $0 }),
                            in: 1...100, step: world.heroSpeedFactor < 10 ? 1 : 10)
                } header: { Text("Hrdina") } footer: { Text("Násobí rychlost pohybu hrdiny (dobrodružství, přesuny). Platí hned po uložení pro další vyslání hrdiny.") }
                Section {
                    Stepper("Mapa: \(world.mapSize * 2 + 1) × \(world.mapSize * 2 + 1)", value: $world.mapSize, in: 10...100, step: 5)
                } footer: { Text("Velikost mapy se použije při vytvoření nového světa (Nástroje → Nový svět).") }
                Section("Data") {
                    LabeledContent("Logy", value: "Soubory → Na iPhonu → OpenVillage → logs")
                    LabeledContent("PHP + MariaDB", value: "v aplikaci, offline")
                }
            }
            .navigationTitle("Nastavení světa")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .cancellationAction) { Button("Zavřít") { dismiss() } }
                ToolbarItem(placement: .confirmationAction) {
                    Button("Uložit") {
                        let styleChanged = world.style != runtime.world.style
                        world.save(); runtime.world = world
                        if styleChanged { runtime.applyStyle(world.style) }
                        runtime.applyHeroSpeed(world.heroSpeedFactor)
                        dismiss()
                    }
                }
            }
        }
    }
}
