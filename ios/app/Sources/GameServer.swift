import Foundation
import PHPCore

/// Paths used by the app. Everything writable lives in Application Support/Travian.
enum Paths {
    static let support: URL = {
        let base = FileManager.default.urls(for: .applicationSupportDirectory, in: .userDomainMask)[0]
        return base.appendingPathComponent("Travian", isDirectory: true)
    }()
    /// Document root served by PHP (a mirror of the bundled game: PHP files copied, assets symlinked).
    static var www: URL { support.appendingPathComponent("www", isDirectory: true) }
    static var data: URL { support.appendingPathComponent("data", isDirectory: true) }
    static var database: URL { data.appendingPathComponent("travian.sqlite") }
    static var sessions: URL { data.appendingPathComponent("sessions", isDirectory: true) }
    static var logs: URL { support.appendingPathComponent("logs", isDirectory: true) }
    static var serverLog: URL { logs.appendingPathComponent("server.log") }
    static var phpErrorLog: URL { logs.appendingPathComponent("php_errors.log") }
    static var sqlErrorLog: URL { www.appendingPathComponent("local/logs/sql_errors.log") }
    static var queueLock: URL { www.appendingPathComponent("application/queue.txt") }
    static var bundledGame: URL { Bundle.main.resourceURL!.appendingPathComponent("game", isDirectory: true) }
    static var bundledDatabase: URL { Bundle.main.url(forResource: "travian", withExtension: "sqlite")! }
}

enum GameFiles {
    /// Directories the game writes into – always real, empty folders in the mirror.
    static let writableDirs: Set<String> = ["mcache", "hcache", "mmark", "local"]
    /// Text files the game rewrites at runtime.
    static let copiedExtensions: Set<String> = ["php", "txt", "tpl", "inc", "sql"]

    /// Builds (or refreshes after an app update) the document root and the database.
    static func prepare() throws {
        let fm = FileManager.default
        try fm.createDirectory(at: Paths.data, withIntermediateDirectories: true)
        try fm.createDirectory(at: Paths.sessions, withIntermediateDirectories: true)
        try fm.createDirectory(at: Paths.logs, withIntermediateDirectories: true)

        let buildID = bundleBuildID()
        let marker = Paths.www.appendingPathComponent(".build-id")
        let current = try? String(contentsOf: marker, encoding: .utf8)
        if current != buildID {
            // Keep the SQL error log across updates.
            try? fm.removeItem(at: Paths.www)
            try fm.createDirectory(at: Paths.www, withIntermediateDirectories: true)
            try mirror(from: Paths.bundledGame, to: Paths.www, topLevel: true)
            for dir in writableDirs.sorted() {
                try fm.createDirectory(at: Paths.www.appendingPathComponent(dir), withIntermediateDirectories: true)
            }
            try fm.createDirectory(at: Paths.www.appendingPathComponent("local/logs"), withIntermediateDirectories: true)
            try fm.createDirectory(at: Paths.www.appendingPathComponent("hcache/body"), withIntermediateDirectories: true)
            if !fm.fileExists(atPath: Paths.queueLock.path) { fm.createFile(atPath: Paths.queueLock.path, contents: Data()) }
            try buildID.write(to: marker, atomically: true, encoding: .utf8)
        }
        if !fm.fileExists(atPath: Paths.database.path) {
            try installFreshDatabase()
        }
    }

    static func installFreshDatabase() throws {
        let fm = FileManager.default
        for suffix in ["", "-wal", "-shm"] {
            try? fm.removeItem(atPath: Paths.database.path + suffix)
        }
        try fm.copyItem(at: Paths.bundledDatabase, to: Paths.database)
        // Server "opens" now; registration on.
        try GameDB.exec("""
            UPDATE config SET OPENING = CAST(strftime('%s','now') AS INTEGER) - 60, regstatus = 1 WHERE id = 1;
            """)
        // Old map tiles belong to the old world.
        for dir in ["mcache", "hcache/body", "mmark"] {
            let url = Paths.www.appendingPathComponent(dir)
            for item in (try? fm.contentsOfDirectory(atPath: url.path)) ?? [] {
                try? fm.removeItem(at: url.appendingPathComponent(item))
            }
        }
    }

    private static func bundleBuildID() -> String {
        if let id = try? String(contentsOf: Paths.bundledGame.appendingPathComponent(".build-id"), encoding: .utf8) {
            return id.trimmingCharacters(in: .whitespacesAndNewlines)
        }
        let info = Bundle.main.infoDictionary ?? [:]
        return "\(info["CFBundleShortVersionString"] ?? "")-\(info["CFBundleVersion"] ?? "")"
    }

    /// Mirrors `src` into `dst`: directories containing PHP become real directories, PHP/text files are
    /// copied (PHP resolves __DIR__ through symlinks, so code must be real files), everything else is a symlink
    /// into the read-only app bundle.
    private static func mirror(from src: URL, to dst: URL, topLevel: Bool) throws {
        let fm = FileManager.default
        let items = try fm.contentsOfDirectory(at: src, includingPropertiesForKeys: [.isDirectoryKey], options: [])
        for item in items {
            let name = item.lastPathComponent
            if name == ".build-id" { continue }
            if topLevel && writableDirs.contains(name) { continue }
            let target = dst.appendingPathComponent(name)
            let isDir = (try? item.resourceValues(forKeys: [.isDirectoryKey]).isDirectory) ?? false
            if isDir {
                if containsCode(item) {
                    try fm.createDirectory(at: target, withIntermediateDirectories: true)
                    try mirror(from: item, to: target, topLevel: false)
                } else {
                    try fm.createSymbolicLink(at: target, withDestinationURL: item)
                }
            } else if copiedExtensions.contains(item.pathExtension.lowercased()) {
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

/// Runs PHP's built-in web server (`php -S`) on a background thread inside the app.
@MainActor
final class GameServer: ObservableObject {
    static let shared = GameServer()

    enum State: Equatable {
        case idle, preparing, running, failed(String)
    }

    @Published private(set) var state: State = .idle
    @Published private(set) var port: Int = 8080
    @Published private(set) var phpRuns = 0

    var baseURL: URL { URL(string: "http://127.0.0.1:\(port)/")! }
    func url(_ path: String) -> URL { URL(string: path, relativeTo: baseURL)!.absoluteURL }

    private var thread: Thread?

    func start() {
        guard state != .preparing && state != .running else { return }
        state = .preparing
        port = Self.requestedPort() ?? (Self.isPortOpen(8080) ? Int.random(in: 49152...60000) : 8080)
        let port = self.port
        Task.detached(priority: .userInitiated) {
            do {
                try GameFiles.prepare()
                Self.redirectStdio()
                await MainActor.run { self.launchPHP(port: port) }
                for _ in 0..<200 {
                    if Self.isPortOpen(port) {
                        await MainActor.run { self.state = .running }
                        return
                    }
                    try await Task.sleep(nanoseconds: 50_000_000)
                }
                await MainActor.run { self.state = .failed("PHP server se nespustil – viz Logy.") }
            } catch {
                await MainActor.run { self.state = .failed(error.localizedDescription) }
            }
        }
    }

    /// Called when the app returns to the foreground: iOS may have closed the listening socket.
    func ensureRunning() {
        guard state == .running, !Self.isPortOpen(port) else { return }
        state = .idle
        start()
    }

    private func launchPHP(port: Int) {
        setenv("TRAVIAN_DB_DRIVER", "sqlite", 1)
        setenv("TRAVIAN_SQLITE_PATH", Paths.database.path, 1)
        setenv("TMPDIR", NSTemporaryDirectory(), 1)
        let args: [String] = [
            "php", "-n",
            "-d", "output_buffering=4096",
            "-d", "short_open_tag=On",
            "-d", "display_errors=0",
            "-d", "log_errors=1",
            "-d", "error_log=\(Paths.phpErrorLog.path)",
            "-d", "error_reporting=E_ALL & ~E_DEPRECATED",
            "-d", "memory_limit=256M",
            "-d", "max_execution_time=0",
            "-d", "date.timezone=\(TimeZone.current.identifier)",
            "-d", "session.save_path=\(Paths.sessions.path)",
            "-d", "sendmail_path=/usr/bin/false",
            "-d", "default_charset=UTF-8",
            "-S", "127.0.0.1:\(port)",
            "-t", Paths.www.path,
        ]
        let thread = Thread {
            var cargs: [UnsafeMutablePointer<CChar>?] = args.map { strdup($0) }
            cargs.append(nil)
            FileManager.default.changeCurrentDirectoryPath(Paths.www.path)
            let code = php_cli_main(Int32(args.count), &cargs)
            for p in cargs { free(p) }
            fputs("[TravianLocal] php_cli_main returned \(code)\n", stderr)
            Task { @MainActor in
                if GameServer.shared.state == .running { GameServer.shared.state = .failed("PHP skončil (kód \(code)).") }
            }
        }
        thread.name = "php-server"
        thread.stackSize = 16 << 20      // PHP needs a deep C stack (default secondary thread stack is 512 KB)
        thread.qualityOfService = .userInitiated
        self.thread = thread
        phpRuns += 1
        thread.start()
    }

    // MARK: helpers

    private static func requestedPort() -> Int? {
        let args = ProcessInfo.processInfo.arguments
        if let i = args.firstIndex(of: "-port"), i + 1 < args.count { return Int(args[i + 1]) }
        return nil
    }

    /// stdout/stderr (PHP's access log and fatal errors) go to logs/server.log.
    nonisolated private static func redirectStdio() {
        let path = Paths.serverLog.path
        if let attrs = try? FileManager.default.attributesOfItem(atPath: path),
           let size = attrs[.size] as? Int, size > 5_000_000 {
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
}
