import SwiftUI
import AppKit

enum SidebarItem: String, CaseIterable, Identifiable {
    case overview, players, tests, logs, sql, tools
    var id: String { rawValue }
    var title: String {
        switch self {
        case .overview: return "Přehled"
        case .players: return "Hra a hráči"
        case .tests: return "Testy"
        case .logs: return "Logy"
        case .sql: return "SQL konzole"
        case .tools: return "Nástroje"
        }
    }
    var icon: String {
        switch self {
        case .overview: return "gauge.with.dots.needle.33percent"
        case .players: return "person.2"
        case .tests: return "checkmark.seal"
        case .logs: return "doc.text.magnifyingglass"
        case .sql: return "cylinder.split.1x2"
        case .tools: return "wrench.and.screwdriver"
        }
    }
}

struct MainView: View {
    @EnvironmentObject var controller: ServerController
    @State private var section: SidebarItem? = .overview
    @Environment(\.openWindow) private var openWindow
    @StateObject private var testsModel = TestsModel()

    var body: some View {
        NavigationSplitView {
            List(SidebarItem.allCases, selection: $section) { s in
                Label(s.title, systemImage: s.icon).tag(s)
            }
            .navigationSplitViewColumnWidth(min: 180, ideal: 200)
            .safeAreaInset(edge: .bottom) { StatusFooter().padding(10) }
        } detail: {
            Group {
                if !controller.projectValid {
                    ProjectPickerView()
                } else {
                    switch section ?? .overview {
                    case .overview: OverviewView()
                    case .players: PlayersView()
                    case .tests: TestsView(model: testsModel)
                    case .logs: LogsView()
                    case .sql: SQLView()
                    case .tools: ToolsView()
                    }
                }
            }
            .toolbar { toolbarContent }
        }
        .alert("Chyba", isPresented: Binding(get: { controller.lastError != nil }, set: { if !$0 { controller.lastError = nil } })) {
            Button("OK", role: .cancel) {}
        } message: {
            Text(controller.lastError ?? "")
        }
    }
}

extension MainView {
    @ToolbarContentBuilder
    var toolbarContent: some ToolbarContent {
        ToolbarItemGroup(placement: .primaryAction) {
            if let busy = controller.busyAction {
                ProgressView().controlSize(.small)
                Text(busy).font(.callout).foregroundStyle(.secondary).lineLimit(1)
                Button { controller.cancelAction() } label: { Image(systemName: "xmark.circle") }
                    .help("Přerušit běžící akci")
            }
            if controller.phase == .running || controller.healthy {
                Button { Task { await controller.stop() } } label: { Label("Zastavit", systemImage: "stop.fill") }
                    .disabled(controller.busyAction != nil)
            } else {
                Button { Task { await controller.start() } } label: { Label("Spustit", systemImage: "play.fill") }
                    .disabled(controller.busyAction != nil || !controller.projectValid)
            }
            Button {
                if let first = controller.profiles.first { openWindow(id: "game", value: first.id) }
            } label: { Label("Hrát", systemImage: "gamecontroller") }
                .disabled(!controller.healthy)
                .help("Otevře hru v okně prvního hráče")
        }
    }
}

struct StatusFooter: View {
    @EnvironmentObject var controller: ServerController
    var body: some View {
        HStack(spacing: 8) {
            Circle().fill(color).frame(width: 9, height: 9)
            VStack(alignment: .leading, spacing: 1) {
                Text(text).font(.caption.weight(.semibold))
                Text(controller.settings.backend == .native ? "nativně · :\(controller.settings.port)" : "docker · :\(controller.settings.port)")
                    .font(.caption2).foregroundStyle(.secondary)
            }
            Spacer()
        }
    }
    var color: Color {
        if controller.healthy { return .green }
        switch controller.phase {
        case .starting, .stopping: return .orange
        case .error: return .red
        default: return .gray
        }
    }
    var text: String {
        if controller.healthy { return "Běží" }
        switch controller.phase {
        case .starting: return "Spouští se…"
        case .stopping: return "Zastavuje se…"
        case .error: return "Chyba"
        default: return "Zastaveno"
        }
    }
}

struct ProjectPickerView: View {
    @EnvironmentObject var controller: ServerController
    var body: some View {
        VStack(spacing: 16) {
            Image(systemName: "folder.badge.questionmark").font(.system(size: 48)).foregroundStyle(.secondary)
            Text("Vyberte složku projektu OpenVillage 4.6").font(.title2.weight(.semibold))
            Text("Složka, která obsahuje compose.yaml, main_script a scripts/native.")
                .foregroundStyle(.secondary)
            if !controller.settings.projectPath.isEmpty {
                Text("Aktuální cesta není platný projekt:\n\(controller.settings.projectPath)")
                    .font(.callout).foregroundStyle(.red).multilineTextAlignment(.center)
            }
            Button("Vybrat složku…") { pickProjectFolder(controller) }
                .buttonStyle(.borderedProminent).controlSize(.large)
        }
        .padding(40)
        .frame(maxWidth: .infinity, maxHeight: .infinity)
    }
}

@MainActor
func pickProjectFolder(_ controller: ServerController) {
    let panel = NSOpenPanel()
    panel.canChooseDirectories = true
    panel.canChooseFiles = false
    panel.allowsMultipleSelection = false
    panel.prompt = "Vybrat"
    panel.message = "Vyberte kořenovou složku projektu (TravianT4.6-main)"
    if panel.runModal() == .OK, let url = panel.url {
        if ProjectLocator.isProject(url) {
            controller.settings.projectPath = url.path
        } else {
            controller.lastError = "Ve složce chybí compose.yaml nebo main_script/include/schema/T4.4.sql."
        }
    }
}

// MARK: - Overview

struct OverviewView: View {
    @EnvironmentObject var controller: ServerController
    @Environment(\.openWindow) private var openWindow
    @State private var confirmErase = false

    var body: some View {
        VSplitView {
            ScrollView {
                VStack(alignment: .leading, spacing: 18) {
                    header
                    servicesGrid
                    HStack(alignment: .top, spacing: 18) {
                        accessBox
                        doctorBox
                    }
                }
                .padding(20)
            }
            .frame(minHeight: 300)

            ConsoleView(lines: controller.console, onClear: { controller.console.removeAll() })
                .frame(minHeight: 160)
        }
        .confirmationDialog("Smazat celý lokální svět?", isPresented: $confirmErase, titleVisibility: .visible) {
            Button("Smazat databázi, Redis a účty", role: .destructive) { Task { await controller.erase() } }
        } message: {
            Text(controller.settings.backend == .docker
                 ? "Spustí „docker compose down -v“ – odstraní volumes database-data, redis-data a runtime-data."
                 : "Zastaví služby a smaže datovou složku nativního běhu (MariaDB, Redis, hesla).")
        }
    }

    var header: some View {
        HStack(alignment: .center, spacing: 14) {
            VStack(alignment: .leading, spacing: 4) {
                Text("OpenVillage 4.6 – lokální server").font(.title.weight(.semibold))
                Text(controller.settings.backend.subtitle).foregroundStyle(.secondary)
                Text(controller.settings.projectPath).font(.caption.monospaced()).foregroundStyle(.tertiary)
                    .textSelection(.enabled)
            }
            Spacer()
            Picker("", selection: $controller.settings.backend) {
                ForEach(BackendKind.allCases) { Text($0.title).tag($0) }
            }
            .pickerStyle(.segmented)
            .frame(width: 300)
            .disabled(controller.healthy || controller.busyAction != nil)
            .help("Režim lze změnit jen při zastaveném serveru")
        }
    }

    var servicesGrid: some View {
        GroupBox {
            VStack(alignment: .leading, spacing: 10) {
                HStack {
                    Text("Služby").font(.headline)
                    Spacer()
                    Text("health.php: \(controller.healthText)").font(.caption).foregroundStyle(controller.healthy ? .green : .secondary)
                    Button { Task { await controller.refreshStatus() } } label: { Image(systemName: "arrow.clockwise") }
                        .buttonStyle(.borderless)
                }
                if controller.services.isEmpty {
                    Text("Stav zatím není známý.").foregroundStyle(.secondary)
                } else {
                    LazyVGrid(columns: [GridItem(.adaptive(minimum: 170), spacing: 10)], spacing: 10) {
                        ForEach(controller.services) { s in ServiceCard(status: s) }
                    }
                }
                HStack(spacing: 10) {
                    Button { Task { await controller.start() } } label: { Label("Spustit", systemImage: "play.fill") }
                        .buttonStyle(.borderedProminent)
                    Button { Task { await controller.stop() } } label: { Label("Zastavit", systemImage: "stop.fill") }
                    Button { Task { await controller.restart() } } label: {
                        Label(controller.settings.backend == .docker ? "Přestavět a restartovat" : "Restartovat", systemImage: "arrow.triangle.2.circlepath")
                    }
                    Button { Task { await controller.runBootstrap() } } label: { Label("Migrace", systemImage: "square.stack.3d.up") }
                        .help("Znovu spustí bootstrap – dopředné migrace a instalaci světa (bezpečné opakovat)")
                    Spacer()
                    Button(role: .destructive) { confirmErase = true } label: { Label("Smazat svět…", systemImage: "trash") }
                }
                .disabled(controller.busyAction != nil)
                .padding(.top, 4)
            }
            .padding(6)
        }
    }

    var accessBox: some View {
        GroupBox {
            VStack(alignment: .leading, spacing: 10) {
                Text("Přístup").font(.headline)
                LabeledContent("Spouštěč") { Link(controller.baseURL.absoluteString, destination: controller.baseURL) }
                LabeledContent("Hra") { Link(controller.gameURL.absoluteString, destination: controller.gameURL) }
                LabeledContent("Admin") {
                    HStack {
                        Text("Support")
                        if let pw = controller.backend?.adminPassword() {
                            Text(pw).font(.body.monospaced()).textSelection(.enabled)
                            Button { copy(pw) } label: { Image(systemName: "doc.on.doc") }.buttonStyle(.borderless)
                        } else {
                            Text("(heslo vznikne při 1. startu)").foregroundStyle(.secondary)
                        }
                    }
                }
                HStack {
                    Button { if let p = controller.profiles.first { openWindow(id: "game", value: p.id) } } label: {
                        Label("Hrát v aplikaci", systemImage: "gamecontroller")
                    }
                    Button { NSWorkspace.shared.open(controller.baseURL) } label: { Label("V prohlížeči", systemImage: "safari") }
                    Button { NSWorkspace.shared.open(controller.adminURL) } label: { Label("Admin panel", systemImage: "shield") }
                }
                .disabled(!controller.healthy)
                Divider()
                Text("Terminál").font(.caption.weight(.semibold)).foregroundStyle(.secondary)
                if let hint = controller.backend?.shellHint() {
                    HStack {
                        Text(hint).font(.caption.monospaced()).lineLimit(2).textSelection(.enabled)
                        Button { copy(hint) } label: { Image(systemName: "doc.on.doc") }.buttonStyle(.borderless)
                    }
                }
            }
            .padding(6)
            .frame(maxWidth: .infinity, alignment: .leading)
        }
    }

    var doctorBox: some View {
        GroupBox {
            VStack(alignment: .leading, spacing: 6) {
                HStack {
                    Text("Závislosti").font(.headline)
                    Spacer()
                    Button { Task { await controller.refreshDoctor() } } label: { Image(systemName: "arrow.clockwise") }
                        .buttonStyle(.borderless)
                }
                ForEach(controller.doctorItems) { item in
                    HStack(alignment: .firstTextBaseline, spacing: 6) {
                        Image(systemName: item.ok ? "checkmark.circle.fill" : "exclamationmark.triangle.fill")
                            .foregroundStyle(item.ok ? .green : .orange)
                        VStack(alignment: .leading, spacing: 1) {
                            Text(item.name).font(.callout)
                            if !item.ok, !item.hint.isEmpty {
                                Text(item.hint).font(.caption.monospaced()).foregroundStyle(.secondary).textSelection(.enabled)
                            }
                        }
                    }
                }
                if controller.doctorItems.contains(where: { !$0.ok }) {
                    Button { Task { await controller.installMissing() } } label: {
                        Label("Doinstalovat přes Homebrew", systemImage: "shippingbox")
                    }
                    .disabled(controller.busyAction != nil)
                    .padding(.top, 4)
                }
            }
            .padding(6)
            .frame(maxWidth: .infinity, alignment: .leading)
        }
    }
}

struct ServiceCard: View {
    let status: ServiceStatus
    var body: some View {
        HStack(spacing: 8) {
            Circle().fill(color).frame(width: 10, height: 10)
            VStack(alignment: .leading, spacing: 2) {
                Text(status.name).font(.callout.weight(.medium))
                Text(status.detail.isEmpty ? label : status.detail).font(.caption).foregroundStyle(.secondary).lineLimit(1)
            }
            Spacer(minLength: 0)
        }
        .padding(10)
        .background(RoundedRectangle(cornerRadius: 8).fill(Color(nsColor: .controlBackgroundColor)))
        .overlay(RoundedRectangle(cornerRadius: 8).strokeBorder(Color.primary.opacity(0.08)))
    }
    var color: Color {
        switch status.state {
        case .running: return .green
        case .starting: return .orange
        case .failed: return .red
        case .stopped, .unknown: return .gray
        }
    }
    var label: String {
        switch status.state {
        case .running: return "běží"
        case .starting: return "startuje"
        case .failed: return "chyba"
        case .stopped: return "zastaveno"
        case .unknown: return "?"
        }
    }
}

/// Monospaced, auto-scrolling output pane.
struct ConsoleView: View {
    let lines: [String]
    var onClear: (() -> Void)?
    var title = "Konzole"

    var body: some View {
        VStack(spacing: 0) {
            HStack {
                Text(title).font(.caption.weight(.semibold)).foregroundStyle(.secondary)
                Spacer()
                Button("Kopírovat") { copy(lines.joined(separator: "\n")) }.buttonStyle(.borderless).font(.caption)
                if let onClear { Button("Vyčistit", action: onClear).buttonStyle(.borderless).font(.caption) }
            }
            .padding(.horizontal, 10).padding(.vertical, 5)
            Divider()
            ScrollViewReader { proxy in
                ScrollView([.vertical, .horizontal]) {
                    LazyVStack(alignment: .leading, spacing: 0) {
                        ForEach(Array(lines.enumerated()), id: \.offset) { i, line in
                            Text(line.isEmpty ? " " : line)
                                .font(.system(size: 11.5, design: .monospaced))
                                .foregroundStyle(lineColor(line))
                                .textSelection(.enabled)
                                .fixedSize(horizontal: true, vertical: false)
                                .id(i)
                        }
                    }
                    .padding(8)
                    .frame(maxWidth: .infinity, alignment: .leading)
                }
                .background(Color(nsColor: .textBackgroundColor))
                .onChange(of: lines.count) { _, n in
                    if n > 0 { proxy.scrollTo(n - 1, anchor: .bottom) }
                }
                .onAppear { if !lines.isEmpty { proxy.scrollTo(lines.count - 1, anchor: .bottom) } }
            }
        }
    }
}

func lineColor(_ line: String) -> Color {
    let l = line.lowercased()
    if l.hasPrefix("✓") || l.contains(" passed") || l.hasPrefix("ready") { return .green }
    if l.hasPrefix("✗") || l.contains("fatal") || l.contains("error") || l.contains("uncaught") || l.contains("failed") || l.hasPrefix("missing") { return .red }
    if l.contains("warning") || l.contains("deprecated") || l.contains("notice") { return .orange }
    if l.hasPrefix("────") || l.hasPrefix("==>") || l.hasPrefix("$ ") { return .accentColor }
    return .primary
}

func copy(_ s: String) {
    NSPasteboard.general.clearContents()
    NSPasteboard.general.setString(s, forType: .string)
}
