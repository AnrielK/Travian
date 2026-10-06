import Foundation

enum BackendKind: String, Codable, CaseIterable, Identifiable {
    case native, docker
    var id: String { rawValue }
    var title: String {
        switch self {
        case .native: return "Nativně (Homebrew)"
        case .docker: return "Docker Compose"
        }
    }
    var subtitle: String {
        switch self {
        case .native: return "PHP + MariaDB + Redis přímo na Macu. Změny v kódu se projeví okamžitě."
        case .docker: return "Oficiální stack z compose.yaml (PHP 8.3, MariaDB 11.4, Redis 7.4, Apache)."
        }
    }
}

struct LauncherSettings: Codable, Equatable {
    var projectPath: String = ""
    var backend: BackendKind = .native
    var port: Int = 8080
    var redisPort: Int = 16379
    var dbPort: Int = 0                 // native: 0 = unix socket only
    var gameSpeed: Int = 10
    var mapSize: Int = 25
    var roundLengthDays: Int = 35
    var timezone: String = "Europe/Prague"
    var dockerLiveSource: Bool = true   // bind-mount the checkout into the containers
    var stopOnQuit: Bool = true
    var startOnLaunch: Bool = false

    var projectURL: URL? { projectPath.isEmpty ? nil : URL(fileURLWithPath: projectPath) }
    var baseURL: URL { URL(string: "http://127.0.0.1:\(port)/")! }

    private static let key = "OpenVillageLocal.settings"

    static func load() -> LauncherSettings {
        if let data = UserDefaults.standard.data(forKey: key),
           let s = try? JSONDecoder().decode(LauncherSettings.self, from: data) {
            return s
        }
        var s = LauncherSettings()
        if let found = ProjectLocator.guess() { s.projectPath = found.path }
        return s
    }

    func save() {
        if let data = try? JSONEncoder().encode(self) { UserDefaults.standard.set(data, forKey: Self.key) }
    }
}

enum ProjectLocator {
    static func isProject(_ url: URL) -> Bool {
        let fm = FileManager.default
        return fm.fileExists(atPath: url.appendingPathComponent("compose.yaml").path)
            && fm.fileExists(atPath: url.appendingPathComponent("main_script/include/schema/T4.4.sql").path)
    }

    /// The app is normally built inside <project>/macos/… – walk up from the bundle.
    static func guess() -> URL? {
        var url = Bundle.main.bundleURL
        for _ in 0..<8 {
            url.deleteLastPathComponent()
            if isProject(url) { return url }
        }
        return nil
    }
}

struct PlayerProfile: Codable, Identifiable, Hashable {
    var id = UUID()
    var name: String
    var note: String = ""

    private static let key = "OpenVillageLocal.profiles"

    static func load() -> [PlayerProfile] {
        if let data = UserDefaults.standard.data(forKey: key),
           let p = try? JSONDecoder().decode([PlayerProfile].self, from: data), !p.isEmpty {
            return p
        }
        return [PlayerProfile(name: "Hráč 1"), PlayerProfile(name: "Hráč 2")]
    }

    static func save(_ profiles: [PlayerProfile]) {
        if let data = try? JSONEncoder().encode(profiles) { UserDefaults.standard.set(data, forKey: key) }
    }
}

enum AppPaths {
    static var support: URL {
        let base = FileManager.default.urls(for: .applicationSupportDirectory, in: .userDomainMask)[0]
        let url = base.appendingPathComponent("OpenVillageLocal", isDirectory: true)
        try? FileManager.default.createDirectory(at: url, withIntermediateDirectories: true)
        return url
    }
    static var nativeData: URL { support.appendingPathComponent("native", isDirectory: true) }
    static var dockerOverride: URL { support.appendingPathComponent("compose.launcher.yaml") }
    static var backups: URL {
        let url = support.appendingPathComponent("backups", isDirectory: true)
        try? FileManager.default.createDirectory(at: url, withIntermediateDirectories: true)
        return url
    }
}
