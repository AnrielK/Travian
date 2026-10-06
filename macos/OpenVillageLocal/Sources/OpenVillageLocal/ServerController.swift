import Foundation
import AppKit

/// Central app state: settings, the active backend, service status, health and the activity console.
@MainActor
final class ServerController: ObservableObject {
    static let shared = ServerController()

    enum Phase: Equatable { case stopped, starting, running, stopping, error }

    @Published var settings: LauncherSettings { didSet { if settings != oldValue { settings.save(); backendChanged(oldValue) } } }
    @Published var profiles: [PlayerProfile] { didSet { PlayerProfile.save(profiles) } }
    @Published private(set) var phase: Phase = .stopped
    @Published private(set) var services: [ServiceStatus] = []
    @Published private(set) var healthy = false
    @Published private(set) var healthText = "—"
    @Published private(set) var busyAction: String?
    @Published var console: [String] = []
    @Published var doctorItems: [DoctorItem] = []
    @Published var lastError: String?

    private var pollTimer: Timer?
    private var actionHandle: CommandHandle?

    init() {
        settings = LauncherSettings.load()
        profiles = PlayerProfile.load()
        startPolling()
        Task { await refreshDoctor(); await refreshStatus() }
        if settings.startOnLaunch {
            Task { await start() }
        }
    }

    // MARK: Backend

    var projectValid: Bool { settings.projectURL.map(ProjectLocator.isProject) ?? false }

    var backend: GameBackend? {
        guard let url = settings.projectURL, ProjectLocator.isProject(url) else { return nil }
        switch settings.backend {
        case .native: return NativeBackend(project: url, settings: settings)
        case .docker: return DockerBackend(project: url, settings: settings)
        }
    }

    private func backendChanged(_ old: LauncherSettings) {
        if old.backend != settings.backend || old.projectPath != settings.projectPath {
            services = []
            Task { await refreshDoctor(); await refreshStatus() }
        }
    }

    var baseURL: URL { settings.baseURL }
    var gameURL: URL { settings.baseURL.appendingPathComponent("game/") }
    var adminURL: URL { URL(string: "game/admin.php", relativeTo: settings.baseURL)!.absoluteURL }

    // MARK: Console

    func log(_ lines: [String]) {
        console.append(contentsOf: lines)
        if console.count > 4000 { console.removeFirst(console.count - 4000) }
    }
    func log(_ line: String) { log([line]) }

    /// Runs a backend command with output streamed into the console.
    @discardableResult
    func perform(_ title: String, _ spec: CommandSpec) async -> CommandResult {
        busyAction = title
        let stamp = Date().formatted(date: .omitted, time: .standard)
        log("──── \(stamp)  \(title)")
        log("$ \(spec.displayString)")
        let handle = CommandHandle()
        actionHandle = handle
        let result = await Shell.run(spec, handle: handle) { [weak self] lines in
            MainActor.assumeIsolated { self?.log(lines) }   // delivered on the main queue
        }
        log(result.succeeded ? "✓ \(title) – hotovo" : "✗ \(title) – selhalo (exit \(result.exitCode))")
        busyAction = nil
        actionHandle = nil
        if !result.succeeded { lastError = "\(title) selhalo – detail v konzoli." }
        return result
    }

    func cancelAction() { actionHandle?.cancel() }

    // MARK: Lifecycle

    func start() async {
        guard let backend else { lastError = "Nejdřív vyberte složku projektu v Nastavení."; return }
        do { try backend.prepare() } catch { lastError = error.localizedDescription; return }
        phase = .starting
        let r = await perform("Spuštění (\(backend.kind.title))", backend.start())
        await refreshStatus()
        phase = r.succeeded && healthy ? .running : (r.succeeded ? .starting : .error)
    }

    func stop() async {
        guard let backend else { return }
        phase = .stopping
        await perform("Zastavení", backend.stop())
        await refreshStatus()
        phase = .stopped
    }

    func restart() async {
        guard let backend else { return }
        do { try backend.prepare() } catch { lastError = error.localizedDescription; return }
        phase = .starting
        let r = await perform(backend.kind == .docker ? "Přestavění a restart" : "Restart", backend.rebuild())
        await refreshStatus()
        phase = r.succeeded ? .running : .error
    }

    func runBootstrap() async {
        guard let backend else { return }
        await perform("Bootstrap (migrace)", backend.bootstrap())
    }

    func erase() async {
        guard let backend else { return }
        phase = .stopping
        await perform("Smazání světa", backend.erase())
        await refreshStatus()
        phase = .stopped
    }

    func backup() async {
        guard let backend else { return }
        let r = await perform("Záloha databáze", backend.backup())
        if r.succeeded { NSWorkspace.shared.open(AppPaths.backups) }
    }

    func flushCache() async {
        guard let backend else { return }
        await perform("Vyprázdnění cache (Redis)", backend.flushCache())
    }

    func installMissing() async {
        guard let backend, let spec = backend.installMissing() else {
            lastError = "Homebrew nebyl nalezen – nainstalujte ho z https://brew.sh"
            return
        }
        await perform("Instalace závislostí (Homebrew)", spec)
        await refreshDoctor()
    }

    /// Called from applicationShouldTerminate.
    func stopForQuit() async {
        guard settings.stopOnQuit, let backend, phase != .stopped || services.contains(where: { $0.state == .running }) else { return }
        _ = await Shell.run(backend.stop())
    }

    // MARK: Status

    func refreshDoctor() async {
        guard let backend else { doctorItems = []; return }
        doctorItems = await backend.doctor()
    }

    func refreshStatus() async {
        if let backend { services = await backend.status() } else { services = [] }
        await checkHealth()
        if busyAction == nil {
            let anyRunning = services.contains { $0.state == .running || $0.state == .starting }
            if healthy { phase = .running } else if !anyRunning, phase != .error { phase = .stopped }
        }
    }

    func checkHealth() async {
        var req = URLRequest(url: baseURL.appendingPathComponent("health.php"))
        req.timeoutInterval = 2
        req.cachePolicy = .reloadIgnoringLocalCacheData
        do {
            let (data, _) = try await URLSession.shared.data(for: req)
            let text = String(decoding: data, as: UTF8.self)
            healthy = text.contains("\"status\":\"ready\"")
            healthText = healthy ? "připraveno" : text.prefix(120).description
        } catch {
            healthy = false
            healthText = "nedostupné"
        }
    }

    private func startPolling() {
        pollTimer = Timer.scheduledTimer(withTimeInterval: 4, repeats: true) { [weak self] _ in
            Task { @MainActor [weak self] in
                guard let self, self.busyAction == nil else { return }
                await self.refreshStatus()
            }
        }
    }

    // MARK: SQL helpers

    struct SQLResult {
        var columns: [String] = []
        var rows: [[String]] = []
        var raw: String = ""
        var ok = true
    }

    func query(_ sql: String, global: Bool = false) async -> SQLResult {
        guard let backend else { return SQLResult(raw: "Projekt není nastaven", ok: false) }
        let r = await Shell.run(backend.sql(sql, global: global))
        var out = SQLResult(raw: r.output, ok: r.succeeded)
        // mariadb --batch: tab separated, first line = header. Warnings from the client start with "mysql:"/"mariadb:".
        let lines = r.lines.filter { !$0.hasPrefix("mariadb: [Warning]") && !$0.hasPrefix("mysql: [Warning]") && !$0.isEmpty }
        if r.succeeded, let header = lines.first {
            out.columns = header.components(separatedBy: "\t")
            out.rows = lines.dropFirst().map { $0.components(separatedBy: "\t") }
        }
        return out
    }

    /// Runs a write statement, then clears the Redis cache so the game sees the change.
    @discardableResult
    func devAction(_ title: String, _ sql: String) async -> Bool {
        guard let backend else { return false }
        let r = await perform(title, backend.sql(sql, global: false))
        if r.succeeded { _ = await Shell.run(backend.flushCache()) }
        return r.succeeded
    }
}
