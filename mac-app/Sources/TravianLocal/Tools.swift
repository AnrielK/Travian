import Foundation

/// Finds PHP and MariaDB executables. Apps started from Finder get a minimal PATH,
/// so Homebrew locations are searched explicitly.
struct Toolset {
    var php: String?
    var mariadbd: String?
    var installDB: String?
    var client: String?

    static let searchDirs: [String] = {
        var dirs = (ProcessInfo.processInfo.environment["PATH"] ?? "").split(separator: ":").map(String.init)
        dirs += ["/opt/homebrew/bin", "/opt/homebrew/sbin", "/opt/homebrew/opt/mariadb/bin",
                 "/usr/local/bin", "/usr/local/sbin", "/usr/local/opt/mariadb/bin",
                 "/opt/local/bin", "/opt/local/sbin", "/usr/bin", "/usr/sbin"]
        var seen = Set<String>()
        return dirs.filter { seen.insert($0).inserted }
    }()

    static func find(_ names: [String], override: String? = nil) -> String? {
        let fm = FileManager.default
        if let override, !override.isEmpty, fm.isExecutableFile(atPath: override) { return override }
        for name in names {
            for dir in searchDirs {
                let path = (dir as NSString).appendingPathComponent(name)
                if fm.isExecutableFile(atPath: path) { return path }
            }
        }
        return nil
    }

    static func discover(phpOverride: String?, mariadbOverride: String?) -> Toolset {
        var tools = Toolset()
        tools.php = find(["php"], override: phpOverride)
        tools.mariadbd = find(["mariadbd", "mysqld"], override: mariadbOverride)
        // Prefer client tools that live next to the server binary.
        if let server = tools.mariadbd {
            let binDir = (URL(fileURLWithPath: server).resolvingSymlinksInPath().deletingLastPathComponent().path)
            let fm = FileManager.default
            for name in ["mariadb-install-db", "mysql_install_db"] where tools.installDB == nil {
                let p = (binDir as NSString).appendingPathComponent(name)
                if fm.isExecutableFile(atPath: p) { tools.installDB = p }
            }
            for name in ["mariadb", "mysql"] where tools.client == nil {
                let p = (binDir as NSString).appendingPathComponent(name)
                if fm.isExecutableFile(atPath: p) { tools.client = p }
            }
        }
        tools.installDB = tools.installDB ?? find(["mariadb-install-db", "mysql_install_db"])
        tools.client = tools.client ?? find(["mariadb", "mysql"])
        return tools
    }

    /// MariaDB "basedir" (the folder containing bin/ and share/) derived from the server binary.
    var mariadbBaseDir: String? {
        guard let mariadbd else { return nil }
        return URL(fileURLWithPath: mariadbd).resolvingSymlinksInPath()
            .deletingLastPathComponent().deletingLastPathComponent().path
    }

    var missing: [String] {
        var m: [String] = []
        if php == nil { m.append("php") }
        if mariadbd == nil { m.append("mariadbd") }
        if installDB == nil { m.append("mariadb-install-db") }
        if client == nil { m.append("mariadb (klient)") }
        return m
    }
}

/// Persistent user settings.
final class AppSettings: ObservableObject {
    private let defaults = UserDefaults.standard

    @Published var gamePath: String { didSet { defaults.set(gamePath, forKey: "gamePath") } }
    @Published var phpPort: Int { didSet { defaults.set(phpPort, forKey: "phpPort") } }
    @Published var dbPort: Int { didSet { defaults.set(dbPort, forKey: "dbPort") } }
    @Published var phpPath: String { didSet { defaults.set(phpPath, forKey: "phpPath") } }
    @Published var mariadbPath: String { didSet { defaults.set(mariadbPath, forKey: "mariadbPath") } }
    @Published var showSQLErrorsInPage: Bool { didSet { defaults.set(showSQLErrorsInPage, forKey: "showSQLErrorsInPage") } }
    @Published var allowMultiAccounts: Bool { didSet { defaults.set(allowMultiAccounts, forKey: "allowMultiAccounts") } }
    @Published var startAutomatically: Bool { didSet { defaults.set(startAutomatically, forKey: "startAutomatically") } }

    init() {
        gamePath = defaults.string(forKey: "gamePath") ?? ""
        phpPort = defaults.object(forKey: "phpPort") as? Int ?? 8080
        dbPort = defaults.object(forKey: "dbPort") as? Int ?? 3307
        phpPath = defaults.string(forKey: "phpPath") ?? ""
        mariadbPath = defaults.string(forKey: "mariadbPath") ?? ""
        showSQLErrorsInPage = defaults.object(forKey: "showSQLErrorsInPage") as? Bool ?? false
        allowMultiAccounts = defaults.object(forKey: "allowMultiAccounts") as? Bool ?? true
        startAutomatically = defaults.object(forKey: "startAutomatically") as? Bool ?? true
        if gamePath.isEmpty || !AppSettings.isGameFolder(gamePath) {
            gamePath = AppSettings.detectGameFolder() ?? ""
        }
    }

    static func isGameFolder(_ path: String) -> Bool {
        FileManager.default.fileExists(atPath: (path as NSString).appendingPathComponent("application/config.php"))
            && FileManager.default.fileExists(atPath: (path as NSString).appendingPathComponent("travian.sql"))
    }

    /// Looks for `game/` next to the app (or its parents) and in the current directory.
    static func detectGameFolder() -> String? {
        var starts = [Bundle.main.bundleURL, URL(fileURLWithPath: FileManager.default.currentDirectoryPath)]
        if let exe = Bundle.main.executableURL { starts.append(exe) }
        for start in starts {
            var dir = start
            for _ in 0..<7 {
                for candidate in [dir.appendingPathComponent("game"), dir] where isGameFolder(candidate.path) {
                    return candidate.path
                }
                dir.deleteLastPathComponent()
            }
        }
        return nil
    }

    var dataDirectory: URL {
        let base = FileManager.default.urls(for: .applicationSupportDirectory, in: .userDomainMask).first!
        return base.appendingPathComponent("TravianLocal", isDirectory: true)
    }
}
