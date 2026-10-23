import Foundation
import Darwin
import PHPCore
import MariaDBCore

/// Everything writable lives in Application Support/OpenVillage (visible in the Files app).
enum Paths {
    static let support: URL = {
        let base = FileManager.default.urls(for: .applicationSupportDirectory, in: .userDomainMask)[0]
        return base.appendingPathComponent("OpenVillage", isDirectory: true)
    }()
    static var www: URL { support.appendingPathComponent("www", isDirectory: true) }
    static var mysqlData: URL { support.appendingPathComponent("mariadb", isDirectory: true) }
    static var run: URL { support.appendingPathComponent("run", isDirectory: true) }
    static var sessions: URL { support.appendingPathComponent("sessions", isDirectory: true) }
    /// Logs live in Documents so they are visible in the Files app (Na iPhonu → OpenVillage → logs).
    static var logs: URL {
        FileManager.default.urls(for: .documentDirectory, in: .userDomainMask)[0].appendingPathComponent("logs", isDirectory: true)
    }
    /// Written before each start step and removed when the game runs: if it exists at launch, the previous
    /// start was killed (for example MariaDB aborting the process).
    static var startMarker: URL { logs.appendingPathComponent("last-start.txt") }
    static var tmp: URL { URL(fileURLWithPath: NSTemporaryDirectory()).appendingPathComponent("ov", isDirectory: true) }

    static var serverLog: URL { logs.appendingPathComponent("server.log") }
    static var phpErrorLog: URL { logs.appendingPathComponent("php-errors.log") }
    static var mariadbLog: URL { logs.appendingPathComponent("mariadb.log") }
    static var tickLog: URL { logs.appendingPathComponent("automation.log") }
    static var setupLog: URL { logs.appendingPathComponent("setup.log") }
    static var runtimeDir: URL { www.appendingPathComponent("main_script/copyable/include/runtime", isDirectory: true) }
    static var gameErrorLog: URL { runtimeDir.appendingPathComponent("error.log") }
    static var secrets: URL { support.appendingPathComponent("secrets.json") }

    static var bundledGame: URL { Bundle.main.resourceURL!.appendingPathComponent("game", isDirectory: true) }
    static var mariadbShare: URL { Bundle.main.resourceURL!.appendingPathComponent("mariadb-share", isDirectory: true) }
    static var mariadbTemplate: URL { Bundle.main.resourceURL!.appendingPathComponent("mariadb-template", isDirectory: true) }

    /// Unix socket paths are limited to 104 bytes (incl. the trailing NUL). On an iPhone the app's tmp
    /// folder is /private/var/mobile/Containers/Data/Application/<UUID>/tmp/ (89 bytes), so the file name
    /// must stay short – and /tmp is NOT writable there. Only the simulator (much longer container path,
    /// unsandboxed /tmp) falls back to /tmp.
    static let socket: String = {
        let p = NSTemporaryDirectory() + "m.sock"
        if p.utf8.count <= 100 { return p }
        #if targetEnvironment(simulator)
        return "/tmp/ov-\(getuid())-m.sock"
        #else
        return p   // cannot happen on a device; MariaDB will report the error in mariadb.log
        #endif
    }()
}

/// Generated once per installation.
struct Secrets: Codable {
    var adminPassword: String
    var controlToken: String
    var appSecret: String
    /// Must match ios/mariadb/make-template.sh (the bundled data directory).
    static let dbPassword = "openvillage-ios-local"

    static func load() -> Secrets {
        if let data = try? Data(contentsOf: Paths.secrets), let s = try? JSONDecoder().decode(Secrets.self, from: data) {
            return s
        }
        let s = Secrets(adminPassword: random(12), controlToken: random(32), appSecret: random(48))
        try? FileManager.default.createDirectory(at: Paths.support, withIntermediateDirectories: true)
        try? JSONEncoder().encode(s).write(to: Paths.secrets, options: .atomic)
        return s
    }

    static func random(_ n: Int) -> String {
        let chars = Array("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789")
        return String((0..<n).map { _ in chars[Int.random(in: 0..<chars.count)] })
    }
}

/// World settings, applied when the app starts (speed, round length) or when a new world is created (map size).
struct WorldSettings: Codable, Equatable {
    var speed = 10
    var mapSize = 25
    var roundDays = 35
    /// Graphic style (gpack "mainPage" hash); nil = default. Optional so older saved settings still decode.
    var gpack: String?
    /// Hero movement speed multiplier (1 = normal); nil in older saved settings.
    var heroSpeed: Int?
    var heroSpeedFactor: Int { max(1, heroSpeed ?? 1) }

    /// Styles shipped in sections/gpack (see gpack.php); only those present in the bundle are offered.
    static let allStyles: [(hash: String, name: String)] = [
        ("legends655", "Travian Legends 655.3 (výchozí)"),
        ("29c89d54", "Travian T4.6"),
        ("a17a8f72", "OpenVillage 4.6"),
        ("d11cb434", "Travian T4.4 Seasonized v2"),
        ("68597666", "Travian T4.4 Seasonized"),
        ("TravianOld", "Travian T4.4 Classic"),
        ("T40", "Travian 4.0"),
    ]
    static var availableStyles: [(hash: String, name: String)] {
        allStyles.filter { FileManager.default.fileExists(atPath: Paths.bundledGame
            .appendingPathComponent("sections/gpack/\($0.hash)/mainPage/lang/en-US/compact.css").path) }
    }
    /// The chosen style, or the default (legends655) when none is chosen or the chosen one is not in this build.
    var style: String {
        if let gpack, Self.availableStyles.contains(where: { $0.hash == gpack }) { return gpack }
        return "legends655"
    }

    private static let key = "OpenVillage.world"
    static func load() -> WorldSettings {
        guard let d = UserDefaults.standard.data(forKey: key), let s = try? JSONDecoder().decode(WorldSettings.self, from: d) else {
            return WorldSettings()
        }
        return s
    }
    func save() { UserDefaults.standard.set(try? JSONEncoder().encode(self), forKey: Self.key) }
}

enum GameFiles {
    /// Text files the game must see as real files (PHP resolves __DIR__ through symlinks).
    static let copiedExtensions: Set<String> = ["php", "tpl", "inc", "sql", "json", "txt"]

    /// Builds (or refreshes after an app update) the document root: code is copied, assets are symlinked.
    static func prepare() throws {
        let fm = FileManager.default
        for dir in [Paths.support, Paths.run, Paths.sessions, Paths.logs, Paths.tmp] {
            try fm.createDirectory(at: dir, withIntermediateDirectories: true)
        }
        let buildID = (try? String(contentsOf: Paths.bundledGame.appendingPathComponent(".build-id"), encoding: .utf8))?
            .trimmingCharacters(in: .whitespacesAndNewlines) ?? "dev"
        let marker = Paths.support.appendingPathComponent("www.build-id")
        if (try? String(contentsOf: marker, encoding: .utf8)) != buildID || !fm.fileExists(atPath: Paths.www.path) {
            // keep the runtime folder (tick state, game error log) across app updates
            let keep = Paths.support.appendingPathComponent("runtime-keep")
            try? fm.removeItem(at: keep)
            if fm.fileExists(atPath: Paths.runtimeDir.path) { try? fm.moveItem(at: Paths.runtimeDir, to: keep) }
            try? fm.removeItem(at: Paths.www)
            try fm.createDirectory(at: Paths.www, withIntermediateDirectories: true)
            try mirror(from: Paths.bundledGame, to: Paths.www)
            if fm.fileExists(atPath: keep.path) { try? fm.moveItem(at: keep, to: Paths.runtimeDir) }
            try buildID.write(to: marker, atomically: true, encoding: .utf8)
        }
        try fm.createDirectory(at: Paths.runtimeDir.appendingPathComponent("backups"), withIntermediateDirectories: true)
        if !fm.fileExists(atPath: Paths.mysqlData.appendingPathComponent("mysql").path) {
            try? fm.removeItem(at: Paths.mysqlData)
            try fm.copyItem(at: Paths.mariadbTemplate, to: Paths.mysqlData)
        }
    }

    private static func mirror(from src: URL, to dst: URL) throws {
        let fm = FileManager.default
        let items = try fm.contentsOfDirectory(at: src, includingPropertiesForKeys: [.isDirectoryKey], options: [])
        for item in items {
            let name = item.lastPathComponent
            if name == ".build-id" { continue }
            let target = dst.appendingPathComponent(name)
            let isDir = (try? item.resourceValues(forKeys: [.isDirectoryKey]).isDirectory) ?? false
            if isDir {
                if containsCode(item) {
                    try fm.createDirectory(at: target, withIntermediateDirectories: true)
                    try mirror(from: item, to: target)
                } else {
                    try fm.createSymbolicLink(at: target, withDestinationURL: item)
                }
            } else if copiedExtensions.contains(item.pathExtension.lowercased()) || name.hasPrefix(".") {
                try fm.copyItem(at: item, to: target)
            } else {
                try fm.createSymbolicLink(at: target, withDestinationURL: item)
            }
        }
    }

    private static func containsCode(_ dir: URL) -> Bool {
        guard let e = FileManager.default.enumerator(at: dir, includingPropertiesForKeys: nil) else { return false }
        for case let url as URL in e where url.pathExtension.lowercased() == "php" { return true }
        return false
    }
}

/// Runs MariaDB (ov_mariadbd_main → mysqld_main) and PHP's built-in web server (php_cli_main) on background threads,
/// creates the world on first launch and drives the automation tick.
@MainActor
final class GameRuntime: ObservableObject {
    static let shared = GameRuntime()

    enum State: Equatable {
        case idle
        case working(String)
        case running
        case failed(String)
    }

    @Published private(set) var state: State = .idle
    @Published private(set) var port = 8080
    @Published private(set) var lastTick = ""
    @Published private(set) var tickErrors = 0
    @Published var tickEnabled = true
    /// Set when the previous launch died during start-up (step + end of mariadb.log).
    @Published var previousCrash: String?
    /// Bumped when the graphic style changes so open web views reload.
    @Published private(set) var styleVersion = 0
    let secrets = Secrets.load()
    var world = WorldSettings.load()

    var baseURL: URL { URL(string: "http://127.0.0.1:\(port)/")! }
    func url(_ path: String) -> URL { URL(string: path, relativeTo: baseURL)!.absoluteURL }

    private var mariadbStarted = false
    private var phpRuns = 0
    private var tickTimer: Timer?
    private var tickInFlight = false

    // MARK: start

    init() {
        if let step = try? String(contentsOf: Paths.startMarker, encoding: .utf8) {
            let tail = (try? String(contentsOf: Paths.mariadbLog, encoding: .utf8))?
                .split(separator: "\n").filter { !$0.hasSuffix(" Accepted") && !$0.hasSuffix(" Closing") }
                .suffix(12).joined(separator: "\n") ?? ""
            previousCrash = "Předchozí spuštění skončilo v kroku: \(step.trimmingCharacters(in: .whitespacesAndNewlines))\n\n\(tail)"
            appendLog(Paths.setupLog, "\(Date()) previous start died at: \(step)")
        }
    }

    nonisolated static func mark(_ step: String) {
        try? FileManager.default.createDirectory(at: Paths.logs, withIntermediateDirectories: true)
        try? step.write(to: Paths.startMarker, atomically: true, encoding: .utf8)
    }

    func start() {
        if case .working = state { return }
        if state == .running { return }
        state = .working("Připravuji soubory…")
        port = Self.requestedPort() ?? (Self.isPortOpen(8080) ? Int.random(in: 49152...60000) : 8080)
        let port = self.port
        Task.detached(priority: .userInitiated) { [self] in
            do {
                Self.mark("příprava souborů")
                try GameFiles.prepare()
                Self.redirectStdio()
                Self.mark("start PHP")
                // PHP first: it captures the process working directory as its (virtual) cwd at startup.
                FileManager.default.changeCurrentDirectoryPath(Paths.www.path)
                await self.launchPHP(port: port)
                try await Self.wait("PHP server", seconds: 30) { Self.isPortOpen(port) }
                let alreadyStarted = await self.mariadbStarted
                if !alreadyStarted {
                    await self.setState(.working("Spouštím databázi…"))
                    Self.mark("start MariaDB (socket \(Paths.socket))")
                    await self.launchMariaDB()
                    try await Self.wait("MariaDB", seconds: 90) { Self.isSocketOpen(Paths.socket) }
                }
                Self.mark("založení světa")
                try await self.ensureWorld()
                try? FileManager.default.removeItem(at: Paths.startMarker)
                await self.setState(.running)
                await self.startTicker()
            } catch {
                appendLog(Paths.setupLog, "\(Date()) start failed: \(error.localizedDescription)")
                await self.setState(.failed(error.localizedDescription))
            }
        }
    }

    private func setState(_ s: State) { state = s }

    /// After returning from the background iOS may have closed the listening socket.
    func ensureRunning() {
        guard state == .running, !Self.isPortOpen(port) else { return }
        state = .idle
        start()
    }

    // MARK: world

    private func ensureWorld() async throws {
        state = .working("Kontroluji svět…")
        let status = try? await control("status", timeout: 30)
        appendLog(Paths.setupLog, "\(Date()) status: \(status.map { "\($0)" } ?? "no response")")
        if status?["installed"] as? Bool == true { return }
        let error = status?["error"] as? String ?? ""
        if error.contains("doesn't exist") || error.contains("Unknown database") {
            state = .working("Zakládám databáze…")
            _ = try await controlChecked("reset", form: [:], timeout: 900)
        }
        state = .working("Vytvářím svět (mapa \(world.mapSize * 2 + 1)×\(world.mapSize * 2 + 1))…\nPrvní spuštění trvá desítky sekund.")
        let result = try await controlChecked("setup", form: [:], timeout: 1800)
        appendLog(Paths.setupLog, "\(Date()) setup: \(result)")
    }

    /// Drops all data and creates a new world (map size from settings).
    func newWorld() async throws {
        tickEnabled = false
        defer { tickEnabled = true }
        state = .working("Mažu svět…")
        _ = try await controlChecked("reset", form: [:], timeout: 900)
        setenv("GAME_MAP_SIZE", String(world.mapSize), 1)
        try await ensureWorld()
        state = .running
    }

    /// HeroHelper reads GAME_HERO_SPEED on every request, so a new hero speed applies to the next movement.
    func applyHeroSpeed(_ factor: Int) {
        setenv("GAME_HERO_SPEED", String(max(1, factor)), 1)
    }

    /// sections/gpack/gpack.php reads GAME_GPACK on every request, so a new style applies immediately.
    func applyStyle(_ hash: String) {
        setenv("GAME_GPACK", hash, 1)
        Task {
            _ = try? await control("flush", form: [:])
            styleVersion += 1
        }
    }

    // MARK: control endpoint

    func control(_ action: String, form: [String: String]? = nil, timeout: TimeInterval = 120) async throws -> [String: Any] {
        var req = URLRequest(url: url("__openvillage/\(action)"))
        req.timeoutInterval = timeout
        req.setValue(secrets.controlToken, forHTTPHeaderField: "X-OV-Token")
        if let form {
            req.httpMethod = "POST"
            req.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type")
            req.httpBody = form.map { "\(Self.formEncode($0.key))=\(Self.formEncode($0.value))" }
                .joined(separator: "&").data(using: .utf8)
        }
        let (data, _) = try await URLSession.shared.data(for: req)
        guard let obj = try JSONSerialization.jsonObject(with: data) as? [String: Any] else {
            throw RuntimeError(String(decoding: data.prefix(400), as: UTF8.self))
        }
        return obj
    }

    @discardableResult
    func controlChecked(_ action: String, form: [String: String]? = nil, timeout: TimeInterval = 120) async throws -> [String: Any] {
        let r = try await control(action, form: form, timeout: timeout)
        guard r["ok"] as? Bool == true else {
            throw RuntimeError("\(action): \(r["error"] as? String ?? "\(r)")")
        }
        return r
    }

    struct SQLResult {
        var columns: [String] = []
        var rows: [[String]] = []
        var affected = 0
        var error: String?
        var ms = 0
    }

    func sql(_ query: String, global: Bool = false) async -> SQLResult {
        do {
            let r = try await control("sql", form: ["query": query, "db": global ? "global" : "game"], timeout: 300)
            var out = SQLResult(affected: r["affected"] as? Int ?? 0, error: r["error"] as? String, ms: r["ms"] as? Int ?? 0)
            if let sets = r["sets"] as? [[String: Any]], let last = sets.last {
                out.columns = last["columns"] as? [String] ?? []
                out.rows = last["rows"] as? [[String]] ?? []
            }
            return out
        } catch {
            return SQLResult(error: error.localizedDescription)
        }
    }

    /// Runs a write statement for the test tools, then clears the cache.
    @discardableResult
    func devAction(_ sql: String) async -> String? {
        let r = await self.sql(sql)
        _ = try? await control("flush", form: [:])
        return r.error
    }

    // MARK: automation tick (replaces the forked Automation.php workers)

    private func startTicker() {
        tickTimer?.invalidate()
        tickTimer = Timer.scheduledTimer(withTimeInterval: 1, repeats: true) { [weak self] _ in
            Task { @MainActor in await self?.tick() }
        }
    }

    func tick() async {
        guard tickEnabled, state == .running, !tickInFlight else { return }
        tickInFlight = true
        defer { tickInFlight = false }
        guard let r = try? await control("tick", timeout: 300) else { return }
        let ran = r["ran"] as? [String] ?? []
        let errors = r["errors"] as? [String] ?? []
        if !errors.isEmpty { tickErrors += errors.count }
        if !ran.isEmpty || !errors.isEmpty {
            let line = "\(Self.stamp()) \(r["ms"] ?? 0) ms  " + (ran + errors.map { "ERROR " + $0 }).joined(separator: ", ")
            lastTick = line
            appendLog(Paths.tickLog, line)
        }
    }

    // MARK: threads

    private func launchMariaDB() {
        mariadbStarted = true
        try? FileManager.default.removeItem(atPath: Paths.socket)
        try? FileManager.default.removeItem(at: Paths.run.appendingPathComponent("mariadbd.pid"))
        let args: [String] = [
            "mariadbd", "--no-defaults",
            "--datadir=\(Paths.mysqlData.path)",
            "--socket=\(Paths.socket)",
            "--skip-networking",
            "--pid-file=\(Paths.run.appendingPathComponent("mariadbd.pid").path)",
            "--log-error=\(Paths.mariadbLog.path)",
            "--tmpdir=\(Paths.tmp.path)",
            "--basedir=\(Bundle.main.resourcePath!)",
            "--lc-messages-dir=\(Paths.mariadbShare.path)",
            "--character-sets-dir=\(Paths.mariadbShare.appendingPathComponent("charsets").path)/",
            "--character-set-server=utf8mb4", "--collation-server=utf8mb4_unicode_ci",
            "--lower-case-table-names=1",          // must match ios/mariadb/make-template.sh
            "--skip-name-resolve", "--skip-stack-trace",
            // InnoDB reserves address space for innodb_buffer_pool_size_max up front; the default is derived
            // from RAM (131072m on an iPhone) and iOS refuses such a mapping ("Cannot map …").
            "--innodb-buffer-pool-size=48M", "--innodb-buffer-pool-size-max=64M",
            "--innodb-log-file-size=16M",
            "--innodb-flush-log-at-trx-commit=2",
            "--max-connections=60", "--table-open-cache=400", "--thread-cache-size=8",
            "--key-buffer-size=4M", "--aria-pagecache-buffer-size=8M",
            "--tmp-table-size=16M", "--max-heap-table-size=32M",
        ]
        let thread = Thread {
            var cargs: [UnsafeMutablePointer<CChar>?] = args.map { strdup($0) }
            cargs.append(nil)
            let code = ov_mariadbd_main(Int32(args.count), &cargs)
            fputs("[OpenVillage] mysqld_main returned \(code)\n", stderr)
        }
        thread.name = "mariadbd"
        thread.stackSize = 16 << 20
        thread.qualityOfService = .userInitiated
        thread.start()
    }

    private func launchPHP(port: Int) {
        let env: [String: String] = [
            "APP_URL": "http://127.0.0.1:\(port)",
            "GAME_WORLD_URL": "http://127.0.0.1:\(port)/game/",
            "APP_SECRET": secrets.appSecret,
            "APP_TIMEZONE": TimeZone.current.identifier,
            "GAME_DB_HOST": "localhost", "GLOBAL_DB_HOST": "localhost",
            "GAME_DB_NAME": "openvillage_game", "GLOBAL_DB_NAME": "openvillage_global",
            "GAME_DB_USER": "openvillage", "GLOBAL_DB_USER": "openvillage",
            "GAME_DB_PASSWORD": Secrets.dbPassword, "GLOBAL_DB_PASSWORD": Secrets.dbPassword,
            "GAME_SPEED": String(world.speed), "GAME_MAP_SIZE": String(world.mapSize),
            "GAME_GPACK": world.style,
            "GAME_HERO_SPEED": String(world.heroSpeedFactor),
            "GAME_ROUND_LENGTH_DAYS": String(world.roundDays),
            "GAME_WORLD_ID": "local", "GAME_TITLE": "OpenVillage 4.6", "GAME_SERVER_NAME": "iPhone World",
            "INITIAL_ADMIN_PASSWORD": secrets.adminPassword,
            "OV_CONTROL_TOKEN": secrets.controlToken,
            "OV_KV_DB": "openvillage_cache",
            "OV_TICK_SKIP": "mayExitJobs:backup",
            "OV_NATIVE": "1", "OV_IOS": "1",
            "MYSQL_UNIX_PORT": Paths.socket,
            "TMPDIR": Paths.tmp.path,
        ]
        for (k, v) in env { setenv(k, v, 1) }
        let www = Paths.www.path
        let args: [String] = [
            "php", "-n",
            "-d", "mysqli.default_socket=\(Paths.socket)",
            "-d", "pdo_mysql.default_socket=\(Paths.socket)",
            "-d", "auto_prepend_file=\(www)/scripts/native/runtime/redis-polyfill.php",
            "-d", "display_errors=0",
            "-d", "log_errors=1",
            "-d", "error_log=\(Paths.phpErrorLog.path)",
            "-d", "error_reporting=E_ALL",
            "-d", "memory_limit=512M",
            "-d", "max_execution_time=0",
            "-d", "post_max_size=8M",
            "-d", "upload_max_filesize=8M",
            "-d", "date.timezone=\(TimeZone.current.identifier)",
            "-d", "session.save_path=\(Paths.sessions.path)",
            "-d", "session.use_strict_mode=1",
            "-d", "session.cookie_httponly=1",
            "-d", "session.cookie_samesite=Lax",
            "-d", "expose_php=0",
            "-d", "default_charset=UTF-8",
            "-d", "sendmail_path=/usr/bin/false",
            "-S", "127.0.0.1:\(port)",
            "-t", "\(www)/web/public",
            "\(www)/scripts/native/router.php",
        ]
        let thread = Thread {
            var cargs: [UnsafeMutablePointer<CChar>?] = args.map { strdup($0) }
            cargs.append(nil)
            let code = php_cli_main(Int32(args.count), &cargs)
            for p in cargs { free(p) }
            fputs("[OpenVillage] php_cli_main returned \(code)\n", stderr)
            Task { @MainActor in
                if GameRuntime.shared.state == .running { GameRuntime.shared.state = .failed("PHP skončil (kód \(code)).") }
            }
        }
        thread.name = "php-server"
        thread.stackSize = 16 << 20
        thread.qualityOfService = .userInitiated
        phpRuns += 1
        thread.start()
    }

    // MARK: helpers

    nonisolated static func formEncode(_ s: String) -> String {
        let allowed = CharacterSet(charactersIn: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~")
        return s.addingPercentEncoding(withAllowedCharacters: allowed) ?? s
    }

    private static func requestedPort() -> Int? {
        let args = ProcessInfo.processInfo.arguments
        if let i = args.firstIndex(of: "-port"), i + 1 < args.count { return Int(args[i + 1]) }
        return nil
    }

    nonisolated private static func wait(_ what: String, seconds: Double, _ ok: @escaping () -> Bool) async throws {
        let deadline = Date().addingTimeInterval(seconds)
        while Date() < deadline {
            if ok() { return }
            try await Task.sleep(nanoseconds: 100_000_000)
        }
        throw RuntimeError("\(what) se nespustil – podrobnosti v Logy.")
    }

    nonisolated static func stamp() -> String {
        let f = DateFormatter()
        f.dateFormat = "HH:mm:ss"
        return f.string(from: Date())
    }

    /// stdout/stderr (PHP's access log, fatal errors) go to logs/server.log.
    nonisolated private static func redirectStdio() {
        let path = Paths.serverLog.path
        if let size = (try? FileManager.default.attributesOfItem(atPath: path))?[.size] as? Int, size > 5_000_000 {
            try? FileManager.default.removeItem(atPath: path)
        }
        freopen(path, "a", stderr)
        freopen(path, "a", stdout)
        setvbuf(stderr, nil, _IONBF, 0)
        setvbuf(stdout, nil, _IOLBF, 0)
    }

    nonisolated static func isPortOpen(_ port: Int) -> Bool {
        let fd = socket(AF_INET, SOCK_STREAM, 0)
        guard fd >= 0 else { return false }
        defer { close(fd) }
        var addr = sockaddr_in()
        addr.sin_len = UInt8(MemoryLayout<sockaddr_in>.size)
        addr.sin_family = sa_family_t(AF_INET)
        addr.sin_port = in_port_t(UInt16(truncatingIfNeeded: port).bigEndian)
        addr.sin_addr.s_addr = inet_addr("127.0.0.1")
        let r = withUnsafePointer(to: &addr) {
            $0.withMemoryRebound(to: sockaddr.self, capacity: 1) { connect(fd, $0, socklen_t(MemoryLayout<sockaddr_in>.size)) }
        }
        return r == 0
    }

    nonisolated static func isSocketOpen(_ path: String) -> Bool {
        let fd = socket(AF_UNIX, SOCK_STREAM, 0)
        guard fd >= 0 else { return false }
        defer { close(fd) }
        var addr = sockaddr_un()
        addr.sun_family = sa_family_t(AF_UNIX)
        let bytes = Array(path.utf8)
        guard bytes.count < MemoryLayout.size(ofValue: addr.sun_path) else { return false }
        withUnsafeMutableBytes(of: &addr.sun_path) { buf in
            for (i, b) in bytes.enumerated() { buf[i] = b }
            buf[bytes.count] = 0
        }
        addr.sun_len = UInt8(MemoryLayout<sockaddr_un>.size)
        let r = withUnsafePointer(to: &addr) {
            $0.withMemoryRebound(to: sockaddr.self, capacity: 1) { connect(fd, $0, socklen_t(MemoryLayout<sockaddr_un>.size)) }
        }
        return r == 0
    }
}

struct RuntimeError: LocalizedError {
    let message: String
    init(_ message: String) { self.message = message }
    var errorDescription: String? { message }
}

func appendLog(_ url: URL, _ line: String) {
    let data = Data((line + "\n").utf8)
    if let h = try? FileHandle(forWritingTo: url) {
        defer { try? h.close() }
        _ = try? h.seekToEnd()
        try? h.write(contentsOf: data)
    } else {
        try? data.write(to: url)
    }
}
