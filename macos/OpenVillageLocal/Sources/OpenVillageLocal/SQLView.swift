import SwiftUI

struct SQLView: View {
    @EnvironmentObject var controller: ServerController
    @State private var query = "SELECT id, name, race, gift_gold, silver, total_pop FROM users ORDER BY id LIMIT 50;"
    @State private var globalDB = false
    @State private var result = ServerController.SQLResult()
    @State private var running = false
    @State private var showRaw = false
    @State private var elapsed = 0.0
    @AppStorage("OpenVillageLocal.sqlHistory") private var historyData = Data()

    var history: [String] { (try? JSONDecoder().decode([String].self, from: historyData)) ?? [] }

    static let snippets: [(String, String, Bool)] = [
        ("Hráči", "SELECT id, name, race, email, gift_gold + bought_gold AS gold, silver, total_pop, total_villages, FROM_UNIXTIME(signupTime) AS signup FROM users ORDER BY id;", false),
        ("Vesnice", "SELECT kid, owner, name, capital, pop, cp, ROUND(wood) wood, ROUND(clay) clay, ROUND(iron) iron, ROUND(crop) crop, maxstore, maxcrop FROM vdata WHERE owner > 2 ORDER BY owner, kid;", false),
        ("Fronta staveb", "SELECT b.id, v.name AS village, b.building_field, b.isMaster, FROM_UNIXTIME(b.commence) AS done_at FROM building_upgrade b JOIN vdata v ON v.kid = b.kid ORDER BY b.commence;", false),
        ("Výcvik", "SELECT t.id, v.name AS village, t.nr, t.num, t.training_time, t.commence, t.end_time FROM training t JOIN vdata v ON v.kid = t.kid ORDER BY t.end_time;", false),
        ("Pohyby vojsk", "SELECT id, kid, to_kid, mode, attack_type, u1, u2, u3, u4, u5, u6, u7, u8, u9, u10, u11, FROM_UNIXTIME(end_time DIV 1000) AS arrives, proc FROM movement ORDER BY end_time;", false),
        ("Výzkum", "SELECT r.id, v.name AS village, r.nr, r.mode, FROM_UNIXTIME(r.end_time) AS done_at FROM research r JOIN vdata v ON v.kid = r.kid ORDER BY r.end_time;", false),
        ("Karanténa úloh", "SELECT task_table, task_id, attempts, last_error, last_failed_at FROM scheduled_task_failures ORDER BY last_failed_at DESC;", false),
        ("Konfigurace světa", "SELECT * FROM config;", false),
        ("Migrace", "SELECT * FROM openvillage_schema_migrations ORDER BY version;", false),
        ("Tabulky + velikost", "SELECT table_name, table_rows, ROUND((data_length + index_length) / 1024) AS kb FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY kb DESC;", false),
        ("Herní servery (global)", "SELECT * FROM gameServers;", true),
    ]

    var body: some View {
        VSplitView {
            VStack(alignment: .leading, spacing: 8) {
                HStack {
                    Text("SQL konzole").font(.title2.weight(.semibold))
                    Picker("", selection: $globalDB) {
                        Text("herní DB").tag(false)
                        Text("globální DB").tag(true)
                    }
                    .pickerStyle(.segmented).frame(width: 220)
                    Spacer()
                    Menu("Šablony") {
                        ForEach(Self.snippets, id: \.0) { s in
                            Button(s.0) { query = s.1; globalDB = s.2 }
                        }
                    }
                    .fixedSize()
                    Menu("Historie") {
                        ForEach(history, id: \.self) { h in Button(String(h.prefix(90))) { query = h } }
                    }
                    .fixedSize()
                    .disabled(history.isEmpty)
                }
                TextEditor(text: $query)
                    .font(.system(size: 13, design: .monospaced))
                    .frame(minHeight: 90)
                    .overlay(RoundedRectangle(cornerRadius: 6).strokeBorder(Color.secondary.opacity(0.3)))
                HStack {
                    Button { Task { await run() } } label: { Label("Spustit", systemImage: "play.fill") }
                        .keyboardShortcut(.return, modifiers: .command)
                        .buttonStyle(.borderedProminent)
                        .disabled(running || !controller.healthy)
                    if running { ProgressView().controlSize(.small) }
                    Text("⌘↩︎").font(.caption).foregroundStyle(.secondary)
                    Spacer()
                    if !result.raw.isEmpty || !result.columns.isEmpty {
                        Text(result.ok ? "\(result.rows.count) řádků · \(String(format: "%.2f", elapsed)) s" : "chyba")
                            .font(.caption).foregroundStyle(result.ok ? .secondary : Color.red)
                    }
                    Toggle("Text", isOn: $showRaw).toggleStyle(.checkbox)
                }
            }
            .padding(12)
            .frame(minHeight: 200)

            Group {
                if showRaw || !result.ok || result.columns.isEmpty {
                    ConsoleView(lines: result.raw.isEmpty ? ["Zatím nic nespuštěno."] : result.raw.components(separatedBy: "\n"),
                                title: "Výstup")
                } else {
                    ResultGrid(columns: result.columns, rows: result.rows)
                }
            }
            .frame(minHeight: 200)
        }
    }

    func run() async {
        running = true
        let started = Date()
        result = await controller.query(query, global: globalDB)
        if result.ok && result.raw.isEmpty { result.raw = "OK – příkaz proveden (žádný výstup)." }
        elapsed = Date().timeIntervalSince(started)
        running = false
        var h = history.filter { $0 != query }
        h.insert(query, at: 0)
        historyData = (try? JSONEncoder().encode(Array(h.prefix(30)))) ?? Data()
    }
}

/// Simple scrollable result table with dynamic columns.
struct ResultGrid: View {
    let columns: [String]
    let rows: [[String]]

    var widths: [CGFloat] {
        columns.indices.map { i in
            let longest = ([columns[i]] + rows.prefix(200).map { i < $0.count ? $0[i] : "" }).map(\.count).max() ?? 4
            return min(360, max(50, CGFloat(longest) * 7.4 + 16))
        }
    }

    var body: some View {
        let w = widths
        ScrollView([.horizontal, .vertical]) {
            LazyVStack(alignment: .leading, spacing: 0, pinnedViews: [.sectionHeaders]) {
                SwiftUI.Section {
                    ForEach(Array(rows.enumerated()), id: \.offset) { idx, row in
                        HStack(spacing: 0) {
                            ForEach(columns.indices, id: \.self) { c in
                                let v = c < row.count ? row[c] : ""
                                Text(v)
                                    .font(.system(size: 12, design: .monospaced))
                                    .foregroundStyle(v == "NULL" ? Color.secondary : Color.primary)
                                    .lineLimit(1)
                                    .truncationMode(.tail)
                                    .frame(width: w[c], alignment: .leading)
                                    .padding(.horizontal, 6)
                                    .textSelection(.enabled)
                            }
                        }
                        .padding(.vertical, 3)
                        .background(idx % 2 == 0 ? Color.clear : Color.primary.opacity(0.04))
                    }
                } header: {
                    HStack(spacing: 0) {
                        ForEach(columns.indices, id: \.self) { c in
                            Text(columns[c])
                                .font(.system(size: 12, weight: .semibold))
                                .lineLimit(1)
                                .frame(width: w[c], alignment: .leading)
                                .padding(.horizontal, 6)
                        }
                    }
                    .padding(.vertical, 5)
                    .background(.bar)
                }
            }
            .padding(.bottom, 8)
        }
        .background(Color(nsColor: .textBackgroundColor))
    }
}
