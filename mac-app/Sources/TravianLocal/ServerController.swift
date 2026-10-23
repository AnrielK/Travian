import Foundation
import AppKit

enum LogChannel: String, CaseIterable, Identifiable {
    case server = "Server"
    case phpErrors = "PHP chyby"
    case sqlErrors = "SQL chyby"
    case mariadb = "MariaDB"
    case mail = "E-maily"
    var id: String { rawValue }
}

enum ServerState: Equatable {
    case stopped, starting, running, stopping
    case failed(String)

    var label: String {
        switch self {
        case .stopped: return "Zastaveno"
        case .starting: return "Spouštím…"
        case .running: return "Běží"
        case .stopping: return "Zastavuji…"
        case .failed: return "Chyba"
        }
    }
}

/// Owns the private MariaDB instance and the PHP built-in server.
@MainActor
final class ServerController: ObservableObject {
    static weak var shared: ServerController?

    let settings: AppSettings
    @Published private(set) var state: ServerState = .stopped
    @Published private(set) var status = "Připraveno"
    @Published private(set) var tools = Toolset()
    @Published private(set) var phpVersion = ""
    @Published private(set) var mariadbVersion = ""
    @Published private(set) var logs: [LogChannel: [String]] = [:]
    @Published private(set) var unreadErrors = 0
    @Published var busy = false

    private var mariadb: ManagedProcess?
    private var php: ManagedProcess?
    private var tailers: [(LogChannel, FileTailer)] = []
    private var tailTimer: Timer?
    private let maxLines = 3000

    let dbName = "travian3", dbUser = "travian", dbPass = "travian"

    init(settings: AppSettings) {
        self.settings = settings
        ServerController.shared = self
        refreshTools()
    }

    // MARK: Paths

    var dataDir: URL { settings.dataDirectory }
    var dbDataDir: URL { dataDir.appendingPathComponent("mariadb", isDirectory: true) }
    var socketPath: String { dataDir.appendingPathComponent("mariadb.sock").path }
    var pidFile: URL { dataDir.appendingPathComponent("mariadb.pid") }
    var logsDir: URL { dataDir.appendingPathComponent("logs", isDirectory: true) }
    var gameDir: URL { URL(fileURLWithPath: settings.gamePath, isDirectory: true) }
    var gameURL: URL { URL(string: "http://127.0.0.1:\(settings.phpPort)/")! }
    func url(_ path: String) -> URL { URL(string: path, relativeTo: gameURL)!.absoluteURL }

    // MARK: Tools

    func refreshTools() {
        tools = Toolset.discover(phpOverride: settings.phpPath, mariadbOverride: settings.mariadbPath)
        Task {
            if let php = tools.php, let r = try? await Shell.run(php, ["-r", "echo PHP_VERSION;"]) {
                phpVersion = r.output.trimmingCharacters(in: .whitespacesAndNewlines)
            }
            if let server = tools.mariadbd, let r = try? await Shell.run(server, ["--version"]) {
                mariadbVersion = r.output.trimmingCharacters(in: .whitespacesAndNewlines)
            }
        }
    }

    // MARK: Logging

    func log(_ channel: LogChannel, _ line: String) { append(channel, [line]) }

    private func append(_ channel: LogChannel, _ lines: [String]) {
        guard !lines.isEmpty else { return }
        var current = logs[channel] ?? []
        current.append(contentsOf: lines)
        if current.count > maxLines { current.removeFirst(current.count - maxLines) }
        logs[channel] = current
        if channel == .phpErrors || channel == .sqlErrors { unreadErrors += lines.count }
        if channel == .server {
            let fatal = lines.filter { $0.contains("Fatal error") || $0.contains("Uncaught") }
            if !fatal.isEmpty { append(.phpErrors, fatal) }
        }
    }

    func clearLog(_ channel: LogChannel) { logs[channel] = [] }
    func markErrorsRead() { unreadErrors = 0 }

    private func startTailing() {
        stopTailing()
        let files: [(LogChannel, URL)] = [
            (.phpErrors, gameDir.appendingPathComponent("local/logs/php_errors.log")),
            (.sqlErrors, gameDir.appendingPathComponent("local/logs/sql_errors.log")),
            (.mariadb, logsDir.appendingPathComponent("mariadb.log")),
            (.mail, logsDir.appendingPathComponent("mail.log")),
        ]
        tailers = files.map { ($0.0, FileTailer(url: $0.1, fromEnd: true)) }
        tailTimer = Timer.scheduledTimer(withTimeInterval: 1.0, repeats: true) { [weak self] _ in
            Task { @MainActor in
                guard let self else { return }
                for (channel, tailer) in self.tailers { self.append(channel, tailer.poll()) }
            }
        }
    }

    private func stopTailing() {
        tailTimer?.invalidate()
        tailTimer = nil
    }

    // MARK: Start / stop

    func start() async {
        guard state != .running && state != .starting else { return }
        refreshTools()
        state = .starting
        do {
            try await startDatabase()
            try await prepareDatabase(reset: false)
            try prepareGameFolder()
            try await startPHP()
            startTailing()
            state = .running
            status = "Hra běží na \(gameURL.absoluteString)"
        } catch {
            let message = (error as? LocalizedError)?.errorDescription ?? error.localizedDescription
            log(.server, "✖ \(message)")
            status = message
            stopProcesses()
            state = .failed(message)
        }
    }

    func stop() {
        guard state == .running || state == .starting || isFailed else { return }
        state = .stopping
        status = "Zastavuji…"
        let php = self.php, db = self.mariadb
        Task.detached {
            php?.stop(timeout: 3)
            db?.stop(timeout: 15)
            await MainActor.run {
                self.php = nil
                self.mariadb = nil
                self.stopTailing()
                self.state = .stopped
                self.status = "Zastaveno"
            }
        }
    }

    private var isFailed: Bool { if case .failed = state { return true } else { return false } }

    /// Synchronous stop used when the app quits (child processes would otherwise keep running).
    func stopForTermination() {
        php?.stop(timeout: 3)
        mariadb?.stop(timeout: 15)
        php = nil
        mariadb = nil
    }

    private func stopProcesses() {
        php?.stop(timeout: 3); php = nil
        mariadb?.stop(timeout: 15); mariadb = nil
        stopTailing()
    }

    // MARK: MariaDB

    private func requireTools() throws -> (php: String, server: String, install: String, client: String) {
        guard let php = tools.php, let server = tools.mariadbd, let install = tools.installDB, let client = tools.client else {
            throw LauncherError.missingTools(tools.missing)
        }
        return (php, server, install, client)
    }

    private func startDatabase() async throws {
        let t = try requireTools()
        let fm = FileManager.default
        try fm.createDirectory(at: logsDir, withIntermediateDirectories: true)

        if !mariadbVersion.isEmpty && !mariadbVersion.lowercased().contains("mariadb") {
            throw LauncherError.message("\(t.server) je MySQL. Hra potřebuje MariaDB (brew install mariadb).")
        }

        // Leftover server from a crashed session?
        killStaleDatabase()
        if Shell.isPortOpen(settings.dbPort) {
            throw LauncherError.message("Port \(settings.dbPort) pro databázi je obsazený. Změňte ho v Nastavení.")
        }

        if !fm.fileExists(atPath: dbDataDir.appendingPathComponent("mysql").path) {
            status = "Inicializuji MariaDB (jen poprvé)…"
            log(.server, "▶ Inicializace MariaDB v \(dbDataDir.path)")
            var args = ["--no-defaults", "--datadir=\(dbDataDir.path)",
                        "--auth-root-authentication-method=normal", "--skip-test-db"]
            if let base = tools.mariadbBaseDir { args.insert("--basedir=\(base)", at: 1) }
            let r = try await Shell.run(t.install, args)
            append(.mariadb, r.output.split(separator: "\n").map(String.init))
            guard r.ok else {
                try? fm.removeItem(at: dbDataDir)
                throw LauncherError.message("mariadb-install-db selhal (viz záložka MariaDB).")
            }
        }

        status = "Spouštím MariaDB…"
        log(.server, "▶ MariaDB na 127.0.0.1:\(settings.dbPort)")
        var args = ["--no-defaults", "--datadir=\(dbDataDir.path)", "--port=\(settings.dbPort)",
                    "--bind-address=127.0.0.1", "--socket=\(socketPath)", "--pid-file=\(pidFile.path)",
                    "--log-error=\(logsDir.appendingPathComponent("mariadb.log").path)",
                    "--character-set-server=utf8", "--collation-server=utf8_general_ci", "--sql-mode="]
        if let base = tools.mariadbBaseDir { args.insert("--basedir=\(base)", at: 1) }
        let process = ManagedProcess(name: "mariadb", executable: t.server, arguments: args,
            onLine: { line in Task { @MainActor in ServerController.shared?.log(.mariadb, line) } },
            onExit: { code in Task { @MainActor in ServerController.shared?.processExited("MariaDB", code) } })
        try process.start()
        mariadb = process

        for _ in 0..<120 {
            if let r = try? await rootSQL("SELECT 1", database: nil), r.ok { return }
            if !process.isRunning { break }
            try await Task.sleep(nanoseconds: 250_000_000)
        }
        throw LauncherError.message("MariaDB se nespustila (viz záložka MariaDB).")
    }

    private func killStaleDatabase() {
        guard let text = try? String(contentsOf: pidFile, encoding: .utf8),
              let pid = Int32(text.trimmingCharacters(in: .whitespacesAndNewlines)), pid > 0,
              kill(pid, 0) == 0 else { return }
        log(.server, "▶ Ukončuji starou instanci MariaDB (pid \(pid))")
        kill(pid, SIGTERM)
        for _ in 0..<100 where kill(pid, 0) == 0 { Thread.sleep(forTimeInterval: 0.1) }
    }

    func processExited(_ name: String, _ code: Int32) {
        log(.server, "■ \(name) skončil (kód \(code))")
        if state == .running {
            state = .failed("\(name) neočekávaně skončil")
            status = "\(name) neočekávaně skončil – zkuste Spustit znovu"
        }
    }

    /// Runs SQL as the private instance's root user over the unix socket.
    func rootSQL(_ sql: String, database: String? = "travian3", table: Bool = false) async throws -> CommandResult {
        let t = try requireTools()
        var args = ["--no-defaults", "--socket=\(socketPath)", "-uroot", "--default-character-set=utf8"]
        if table { args.append("--table") }
        if let database { args.append(database) }
        args += ["-e", sql]
        return try await Shell.run(t.client, args)
    }

    private func importFile(_ file: URL) async throws {
        let t = try requireTools()
        let r = try await Shell.run(t.client, ["--no-defaults", "--socket=\(socketPath)", "-uroot",
                                               "--default-character-set=utf8", dbName], stdinFile: file)
        guard r.ok else { throw LauncherError.message("Import \(file.lastPathComponent) selhal: \(r.output.prefix(400))") }
    }

    private func prepareDatabase(reset: Bool) async throws {
        if reset {
            log(.server, "▶ Mažu databázi \(dbName)")
            _ = try await rootSQL("DROP DATABASE IF EXISTS \(dbName)", database: nil)
        }
        let exists = (try? await rootSQL("SELECT 1", database: dbName))?.ok ?? false
        if !exists {
            status = "Importuji databázi (≈10 s)…"
            log(.server, "▶ Vytvářím databázi a importuji travian.sql")
            let setup = """
            CREATE DATABASE \(dbName) CHARACTER SET utf8;
            CREATE USER IF NOT EXISTS '\(dbUser)'@'localhost' IDENTIFIED BY '\(dbPass)';
            CREATE USER IF NOT EXISTS '\(dbUser)'@'127.0.0.1' IDENTIFIED BY '\(dbPass)';
            GRANT ALL ON \(dbName).* TO '\(dbUser)'@'localhost';
            GRANT ALL ON \(dbName).* TO '\(dbUser)'@'127.0.0.1';
            """
            let r = try await rootSQL(setup, database: nil)
            guard r.ok else { throw LauncherError.message("Vytvoření databáze selhalo: \(r.output)") }
            try await importFile(gameDir.appendingPathComponent("travian.sql"))
            clearMapCache()
        }
        try await importFile(gameDir.appendingPathComponent("local/migrations.sql"))
        _ = try await rootSQL("UPDATE config SET HOMEPAGE='\(gameURL.absoluteString)' WHERE id=1")
    }

    private func clearMapCache() {
        let fm = FileManager.default
        for sub in ["mcache", "hcache/body"] {
            let dir = gameDir.appendingPathComponent(sub)
            for item in (try? fm.contentsOfDirectory(atPath: dir.path)) ?? [] where item != ".keep" {
                try? fm.removeItem(at: dir.appendingPathComponent(item))
            }
        }
    }

    private func prepareGameFolder() throws {
        guard AppSettings.isGameFolder(settings.gamePath) else {
            throw LauncherError.message("Složka hry není nastavená (Nastavení → Složka hry).")
        }
        let fm = FileManager.default
        for sub in ["mcache", "hcache/body", "local/logs"] {
            try fm.createDirectory(at: gameDir.appendingPathComponent(sub), withIntermediateDirectories: true)
        }
        // Lock file of the game's event processor – if it is missing, queued events are never processed.
        let lock = gameDir.appendingPathComponent("application/queue.txt")
        if !fm.fileExists(atPath: lock.path) { fm.createFile(atPath: lock.path, contents: Data()) }
    }

    // MARK: PHP

    private func startPHP() async throws {
        let t = try requireTools()
        if Shell.isPortOpen(settings.phpPort) {
            throw LauncherError.message("Port \(settings.phpPort) je obsazený. Změňte ho v Nastavení.")
        }
        let modules = (try? await Shell.run(t.php, ["-m"]))?.output.lowercased() ?? ""
        let needed = ["pdo_mysql", "mysqli", "gd", "mbstring"]
        let missing = needed.filter { !modules.split(separator: "\n").contains(Substring($0)) }
        if !missing.isEmpty {
            throw LauncherError.message("PHP nemá rozšíření: \(missing.joined(separator: ", ")).")
        }

        var env = ProcessInfo.processInfo.environment
        env["TRAVIAN_DB_HOST"] = "127.0.0.1"
        env["TRAVIAN_DB_PORT"] = String(settings.dbPort)
        env["TRAVIAN_DB_USER"] = dbUser
        env["TRAVIAN_DB_PASS"] = dbPass
        env["TRAVIAN_DB_NAME"] = dbName
        env["TRAVIAN_DEBUG"] = settings.showSQLErrorsInPage ? "1" : nil
        env["TRAVIAN_SINGLE_ACCOUNT_PER_IP"] = settings.allowMultiAccounts ? nil : "1"

        let mailLog = logsDir.appendingPathComponent("mail.log").path
        let args = [
            "-d", "output_buffering=4096",
            "-d", "short_open_tag=On",
            "-d", "display_errors=0",
            "-d", "log_errors=1",
            "-d", "error_log=\(gameDir.appendingPathComponent("local/logs/php_errors.log").path)",
            "-d", "error_reporting=E_ALL",
            "-d", "memory_limit=512M",
            "-d", "date.timezone=\(TimeZone.current.identifier)",
            "-d", "session.save_path=\(dataDir.path)",
            "-d", "sendmail_path=tee -a '\(mailLog)' >/dev/null",
            "-S", "127.0.0.1:\(settings.phpPort)",
            "-t", gameDir.path,
        ]
        status = "Spouštím PHP…"
        log(.server, "▶ PHP \(phpVersion) na \(gameURL.absoluteString)")
        let process = ManagedProcess(name: "php", executable: t.php, arguments: args, environment: env,
            currentDirectory: gameDir,
            onLine: { line in
                guard !line.hasSuffix(" Accepted"), !line.hasSuffix(" Closing") else { return }
                Task { @MainActor in ServerController.shared?.log(.server, line) }
            },
            onExit: { code in Task { @MainActor in ServerController.shared?.processExited("PHP", code) } })
        try process.start()
        php = process
        for _ in 0..<40 {
            if Shell.isPortOpen(settings.phpPort) { return }
            if !process.isRunning { break }
            try await Task.sleep(nanoseconds: 100_000_000)
        }
        throw LauncherError.message("PHP server se nespustil (viz záložka Server).")
    }

    // MARK: Test tools

    func resetDatabase() async {
        busy = true; defer { busy = false }
        let wasRunning = state == .running
        if !wasRunning { await start() }
        guard state == .running else { return }
        do {
            try await prepareDatabase(reset: true)
            status = "Databáze obnovena – vytvořte si nový účet"
        } catch {
            status = (error as? LocalizedError)?.errorDescription ?? error.localizedDescription
            log(.server, "✖ \(status)")
        }
    }

    @discardableResult
    func runTool(_ title: String, _ sql: String) async -> Bool {
        busy = true; defer { busy = false }
        do {
            let r = try await rootSQL(sql)
            if r.ok { status = "✓ \(title)"; log(.server, "✓ \(title)") }
            else { status = "✖ \(title): \(r.output)"; log(.server, status) }
            return r.ok
        } catch {
            status = "✖ \(title): \(error.localizedDescription)"
            return false
        }
    }

    func fastForward() async -> Bool {
        let ok = await runTool("Všechny stavby, výzkumy, trénink a pohyby dokončeny", """
        SET @now = UNIX_TIMESTAMP();
        UPDATE queue      SET finish = @now       WHERE status = 0 AND finish > @now;
        UPDATE movement   SET endtime = @now      WHERE proc = 0 AND endtime > @now;
        UPDATE bdata      SET timestamp = @now    WHERE timestamp > @now;
        UPDATE research   SET timestamp = @now    WHERE timestamp > @now;
        UPDATE demolition SET timetofinish = @now WHERE timetofinish > @now;
        UPDATE training   SET lastupdate = lastupdate - CEIL(amt * eachtime) - 1, timestamp = @now WHERE amt > 0;
        """)
        // The game processes its event queue at most once per second (mtime of application/queue.txt).
        // Backdate the lock so the very next page load processes everything.
        let lock = gameDir.appendingPathComponent("application/queue.txt")
        try? FileManager.default.setAttributes([.modificationDate: Date().addingTimeInterval(-60)], ofItemAtPath: lock.path)
        return ok
    }

    func fillResources() async -> Bool {
        await runTool("Sklady naplněny", "UPDATE vdata SET wood = maxstore, clay = maxstore, iron = maxstore, crop = maxcrop WHERE owner > 6;")
    }

    func addGold() async -> Bool {
        await runTool("+1000 zlata všem hráčům", "UPDATE users SET gold = gold + 1000 WHERE id > 6;")
    }

    func removeProtection() async -> Bool {
        await runTool("Ochrana nováčků zrušena", "UPDATE users SET protect = 0, ptime = 0 WHERE id > 6;")
    }

    func setSpeed(_ speed: Int) async -> Bool {
        await runTool("Rychlost serveru nastavena na ×\(speed)", "UPDATE config SET SPEED = \(speed) WHERE id = 1;")
    }

    func currentSpeed() async -> Int? {
        guard let r = try? await rootSQL("SELECT SPEED FROM config WHERE id = 1"), r.ok else { return nil }
        return Int(r.output.split(separator: "\n").last.map(String.init)?.trimmingCharacters(in: .whitespaces) ?? "")
    }

    func players() async -> String {
        guard let r = try? await rootSQL("""
            SELECT u.id, u.username AS hrac, CASE u.tribe WHEN 1 THEN 'Římané' WHEN 2 THEN 'Germáni' WHEN 3 THEN 'Galové'
              WHEN 6 THEN 'Egypťané' WHEN 7 THEN 'Hunové' ELSE u.tribe END AS kmen, u.gold AS zlato,
              (SELECT COUNT(*) FROM vdata v WHERE v.owner = u.id) AS vesnice
            FROM users u WHERE u.id > 6 ORDER BY u.id
            """, table: true) else { return "" }
        return r.output
    }

    func runUserSQL(_ sql: String) async -> String {
        guard state == .running else { return "Server neběží." }
        do {
            let r = try await rootSQL(sql, table: true)
            return r.output.isEmpty ? (r.ok ? "OK (žádný výstup)" : "Chyba") : r.output
        } catch {
            return error.localizedDescription
        }
    }

    func openDataFolder() {
        try? FileManager.default.createDirectory(at: logsDir, withIntermediateDirectories: true)
        NSWorkspace.shared.open(dataDir)
    }
}

enum LauncherError: LocalizedError {
    case missingTools([String])
    case message(String)
    var errorDescription: String? {
        switch self {
        case .missingTools(let names):
            return "Chybí: \(names.joined(separator: ", ")). Nainstalujte: brew install php mariadb"
        case .message(let m): return m
        }
    }
}
