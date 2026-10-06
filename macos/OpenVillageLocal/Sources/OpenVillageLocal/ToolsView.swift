import SwiftUI
import AppKit

/// Shortcuts for testing gameplay without waiting: they edit the database directly, then clear the Redis cache.
/// Queue shortcuts only move due times to "now" – the real worker still performs the completion, so game logic
/// (transactions, reports, quests) runs exactly as in normal play.
struct ToolsView: View {
    @EnvironmentObject var controller: ServerController
    @State private var players: [(id: String, name: String)] = []
    @State private var target = "all"
    @State private var gold = 1000
    @State private var silver = 1000
    @State private var confirmRestore: URL?

    var whereOwner: String { target == "all" ? "owner > 2" : "owner = \(Int(target) ?? -1)" }
    var whereUser: String { target == "all" ? "id > 2" : "id = \(Int(target) ?? -1)" }
    var kidFilter: String { target == "all" ? "1=1" : "kid IN (SELECT kid FROM vdata WHERE owner = \(Int(target) ?? -1))" }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 18) {
                HStack {
                    Text("Nástroje pro testování").font(.title.weight(.semibold))
                    Spacer()
                    Picker("Hráč", selection: $target) {
                        Text("Všichni hráči").tag("all")
                        ForEach(players, id: \.id) { p in Text("\(p.name) (#\(p.id))").tag(p.id) }
                    }
                    .frame(width: 280)
                    Button { Task { await loadPlayers() } } label: { Image(systemName: "arrow.clockwise") }.buttonStyle(.borderless)
                }
                if !controller.healthy {
                    Label("Server neběží – nástroje pracují s živou databází.", systemImage: "exclamationmark.triangle")
                        .foregroundStyle(.orange)
                }

                toolGroup("Čas a fronty") {
                    tool("Dokončit vše ve frontě", "hourglass.bottomhalf.filled",
                         "Stavby, bourání, výzkum, výcvik a pohyby vojsk vybraného hráče budou splatné hned; worker je zpracuje během pár sekund.") {
                        _ = await controller.devAction("Dokončit fronty", """
                            UPDATE building_upgrade SET commence = LEAST(commence, UNIX_TIMESTAMP()) WHERE \(kidFilter);
                            UPDATE demolition SET end_time = LEAST(end_time, UNIX_TIMESTAMP()) WHERE \(kidFilter);
                            UPDATE research SET end_time = LEAST(end_time, UNIX_TIMESTAMP()) WHERE \(kidFilter);
                            UPDATE training SET commence = 0 WHERE \(kidFilter);
                            UPDATE movement SET end_time = LEAST(end_time, CAST(UNIX_TIMESTAMP(NOW(3)) * 1000 AS UNSIGNED)) WHERE proc = 0 AND (\(kidFilter) OR \(kidFilter.replacingOccurrences(of: "kid IN", with: "to_kid IN")));
                            """)
                    }
                    tool("Jen pohyby vojsk", "figure.walk.arrival",
                         "Útoky, podpory, návraty a obchodníci dorazí okamžitě – vhodné pro testování bojů.") {
                        _ = await controller.devAction("Dorazit pohyby", """
                            UPDATE movement SET end_time = LEAST(end_time, CAST(UNIX_TIMESTAMP(NOW(3)) * 1000 AS UNSIGNED)) WHERE proc = 0;
                            """)
                    }
                    tool("Zrušit ochranu začátečníka", "shield.slash", "Hráč půjde okamžitě napadnout.") {
                        _ = await controller.devAction("Zrušit ochranu", "UPDATE users SET protection = 0 WHERE \(whereUser);")
                    }
                }

                toolGroup("Suroviny a měna") {
                    tool("Naplnit sklady", "shippingbox.fill", "Dřevo, hlína a železo na kapacitu skladu, obilí na kapacitu sýpky.") {
                        _ = await controller.devAction("Naplnit sklady", """
                            UPDATE vdata SET wood = maxstore, clay = maxstore, iron = maxstore, crop = maxcrop WHERE \(whereOwner);
                            """)
                    }
                    HStack {
                        Stepper("Zlato: \(gold)", value: $gold, in: 100...100_000, step: 500).frame(width: 200)
                        Button("Přidat zlato") {
                            Task { await controller.devAction("Přidat zlato", "UPDATE users SET gift_gold = gift_gold + \(gold) WHERE \(whereUser);") }
                        }
                        Stepper("Stříbro: \(silver)", value: $silver, in: 100...1_000_000, step: 1000).frame(width: 220).padding(.leading, 20)
                        Button("Přidat stříbro") {
                            Task { await controller.devAction("Přidat stříbro", "UPDATE users SET silver = silver + \(silver) WHERE \(whereUser);") }
                        }
                    }
                    .disabled(!controller.healthy || controller.busyAction != nil)
                }

                toolGroup("Server") {
                    tool("Vyprázdnit cache (Redis)", "arrow.3.trianglepath", "Po ručních úpravách v SQL konzoli.") {
                        await controller.flushCache()
                    }
                    tool("Zálohovat databázi", "externaldrive.badge.timemachine", "Gzip dump herní i globální DB do Application Support/OpenVillageLocal/backups.") {
                        await controller.backup()
                    }
                    HStack(spacing: 10) {
                        Button { NSWorkspace.shared.open(AppPaths.backups) } label: { Label("Složka záloh", systemImage: "folder") }
                        if controller.settings.backend == .native {
                            Button { pickRestore() } label: { Label("Obnovit ze zálohy…", systemImage: "clock.arrow.circlepath") }
                                .disabled(!controller.healthy)
                        }
                        Button { NSWorkspace.shared.open(AppPaths.support) } label: { Label("Data aplikace", systemImage: "internaldrive") }
                        if let project = controller.settings.projectURL {
                            Button { NSWorkspace.shared.open(project) } label: { Label("Projekt", systemImage: "folder.badge.gearshape") }
                            Button { openInTerminal(project) } label: { Label("Terminál v projektu", systemImage: "terminal") }
                        }
                    }
                }
            }
            .padding(20)
        }
        .task { await loadPlayers() }
        .confirmationDialog("Obnovit databázi ze zálohy?", isPresented: Binding(get: { confirmRestore != nil }, set: { if !$0 { confirmRestore = nil } }), titleVisibility: .visible) {
            Button("Obnovit (přepíše obě databáze)", role: .destructive) {
                if let url = confirmRestore, let backend = controller.backend as? NativeBackend {
                    Task { await controller.perform("Obnova zálohy", backend.cmd("restore", url.path, "--yes")) }
                }
            }
        } message: { Text(confirmRestore?.lastPathComponent ?? "") }
    }

    func toolGroup<Content: View>(_ title: String, @ViewBuilder content: () -> Content) -> some View {
        GroupBox {
            VStack(alignment: .leading, spacing: 10) {
                Text(title).font(.headline)
                content()
            }
            .padding(6)
            .frame(maxWidth: .infinity, alignment: .leading)
        }
    }

    func tool(_ title: String, _ icon: String, _ detail: String, action: @escaping () async -> Void) -> some View {
        HStack(alignment: .center, spacing: 12) {
            Button { Task { await action() } } label: { Label(title, systemImage: icon).frame(minWidth: 220, alignment: .leading) }
                .disabled(!controller.healthy || controller.busyAction != nil)
            Text(detail).font(.callout).foregroundStyle(.secondary).fixedSize(horizontal: false, vertical: true)
        }
    }

    func loadPlayers() async {
        guard controller.healthy else { return }
        let r = await controller.query("SELECT id, name FROM users WHERE id > 2 ORDER BY id")
        players = r.rows.compactMap { $0.count >= 2 ? (id: $0[0], name: $0[1]) : nil }
        if target != "all", !players.contains(where: { $0.id == target }) { target = "all" }
    }

    func pickRestore() {
        let panel = NSOpenPanel()
        panel.directoryURL = AppPaths.backups
        panel.allowedContentTypes = []
        panel.allowsOtherFileTypes = true
        panel.canChooseDirectories = false
        panel.message = "Vyberte zálohu *.sql.gz"
        if panel.runModal() == .OK, let url = panel.url { confirmRestore = url }
    }

    func openInTerminal(_ url: URL) {
        let terminal = URL(fileURLWithPath: "/System/Applications/Utilities/Terminal.app")
        NSWorkspace.shared.open([url], withApplicationAt: terminal, configuration: NSWorkspace.OpenConfiguration())
    }
}
