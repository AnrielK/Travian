import Foundation

struct ServiceStatus: Identifiable, Equatable {
    enum State: Equatable { case running, starting, stopped, failed, unknown }
    var id: String { name }
    var name: String
    var state: State
    var detail: String = ""
}

struct TestItem: Identifiable, Hashable {
    let id: String
    let title: String
    let detail: String
    var slow: Bool = false
}

struct LogSource: Identifiable, Hashable {
    enum Kind: Hashable { case file(URL), command([String]) }
    let id: String
    let title: String
    let kind: Kind
}

struct DoctorItem: Identifiable {
    let id = UUID()
    let ok: Bool
    let name: String
    let hint: String
}

/// Everything the UI needs from a runtime. Two implementations: native (Homebrew) and Docker Compose.
protocol GameBackend {
    var kind: BackendKind { get }
    var project: URL { get }
    func prepare() throws
    func doctor() async -> [DoctorItem]
    func installMissing() -> CommandSpec?
    func start() -> CommandSpec
    func stop() -> CommandSpec
    func rebuild() -> CommandSpec
    func bootstrap() -> CommandSpec
    func status() async -> [ServiceStatus]
    func sql(_ query: String, global: Bool) -> CommandSpec
    func tests() async -> [TestItem]
    func test(_ id: String) -> CommandSpec
    func flushCache() -> CommandSpec
    func backup() -> CommandSpec
    func erase() -> CommandSpec
    func logSources() -> [LogSource]
    func logCommand(_ source: LogSource) -> CommandSpec?
    func adminPassword() -> String?
    func shellHint() -> String
}

// MARK: - Native

final class NativeBackend: GameBackend {
    let kind = BackendKind.native
    let project: URL
    let settings: LauncherSettings

    init(project: URL, settings: LauncherSettings) {
        self.project = project
        self.settings = settings
    }

    var script: URL { project.appendingPathComponent("scripts/native/openvillage-native.sh") }
    var data: URL { AppPaths.nativeData }
    var logs: URL { data.appendingPathComponent("logs") }

    var env: [String: String] {
        [
            "OV_DATA_DIR": data.path,
            "OV_PORT": String(settings.port),
            "OV_REDIS_PORT": String(settings.redisPort),
            "OV_DB_PORT": String(settings.dbPort),
            "GAME_SPEED": String(settings.gameSpeed),
            "GAME_MAP_SIZE": String(settings.mapSize),
            "GAME_ROUND_LENGTH_DAYS": String(settings.roundLengthDays),
            "APP_TIMEZONE": settings.timezone,
        ]
    }

    func cmd(_ args: String...) -> CommandSpec { cmd(args) }
    func cmd(_ args: [String]) -> CommandSpec {
        CommandSpec(executable: "/bin/bash", args: [script.path] + args, cwd: project, env: env)
    }

    func prepare() throws {
        guard FileManager.default.fileExists(atPath: script.path) else {
            throw LauncherError("V projektu chybí scripts/native/openvillage-native.sh")
        }
    }

    func doctor() async -> [DoctorItem] {
        let r = await Shell.run(cmd("doctor"))
        return r.lines.compactMap { line in
            if line.hasPrefix("OK") {
                return DoctorItem(ok: true, name: line.dropFirst(2).trimmingCharacters(in: .whitespaces), hint: "")
            }
            if line.hasPrefix("MISSING") {
                let rest = line.dropFirst(7).trimmingCharacters(in: .whitespaces)
                let parts = rest.components(separatedBy: "->")
                return DoctorItem(ok: false, name: parts[0].trimmingCharacters(in: .whitespaces),
                                  hint: parts.count > 1 ? parts[1].trimmingCharacters(in: .whitespaces) : "")
            }
            return nil
        }
    }

    func installMissing() -> CommandSpec? {
        guard let brew = Shell.which("brew") else { return nil }
        let script = """
        set -e
        "\(brew)" install php@8.3 mariadb redis ripgrep node autoconf pkg-config
        PECL="$("\(brew)" --prefix php@8.3)/bin/pecl"
        if ! "$("\(brew)" --prefix php@8.3)/bin/php" -m | grep -qi '^redis$'; then
          yes '' | "$PECL" install redis || true
        fi
        "$("\(brew)" --prefix php@8.3)/bin/php" -m | grep -i '^redis$' && echo 'Hotovo.'
        """
        return .sh(script, cwd: project)
    }

    func start() -> CommandSpec { cmd("start") }
    func stop() -> CommandSpec { cmd("stop") }
    func rebuild() -> CommandSpec { cmd("restart") }
    func bootstrap() -> CommandSpec { cmd("bootstrap") }

    func status() async -> [ServiceStatus] {
        let r = await Shell.run(cmd("status"))
        var out: [ServiceStatus] = []
        for line in r.lines {
            let kv = line.split(separator: "=", maxSplits: 1).map(String.init)
            guard kv.count == 2 else { continue }
            let names = ["mariadb": "MariaDB", "redis": "Redis", "web": "Web (PHP server)", "worker": "Worker (Automation)"]
            if let title = names[kv[0]] {
                out.append(ServiceStatus(name: title, state: kv[1] == "running" ? .running : .stopped))
            }
        }
        return out
    }

    func sql(_ query: String, global: Bool) -> CommandSpec {
        global ? cmd("sql", "--global", query) : cmd("sql", query)
    }

    func tests() async -> [TestItem] {
        let r = await Shell.run(cmd("tests"))
        return r.lines.compactMap { line in
            let p = line.components(separatedBy: "|")
            guard p.count >= 4 else { return nil }
            return TestItem(id: p[0], title: p[0], detail: p[3], slow: p[0] == "runtime" || p[0] == "php-lint")
        }
    }

    func test(_ id: String) -> CommandSpec { cmd("test", id) }
    func flushCache() -> CommandSpec { cmd("flush-cache") }
    func backup() -> CommandSpec { cmd("backup", AppPaths.backups.path) }
    func erase() -> CommandSpec { cmd("reset", "--yes") }

    func logSources() -> [LogSource] {
        let files: [(String, String)] = [
            ("php-error.log", "PHP chyby"),
            ("web.log", "Web server (požadavky)"),
            ("worker.log", "Worker"),
            ("bootstrap.log", "Bootstrap / instalace"),
            ("mariadb.log", "MariaDB"),
            ("redis.log", "Redis"),
        ]
        var out = files.map { LogSource(id: $0.0, title: $0.1, kind: .file(logs.appendingPathComponent($0.0))) }
        out.append(LogSource(id: "game-error", title: "Herní error.log (runtime)",
                             kind: .file(project.appendingPathComponent("main_script/copyable/include/runtime/error.log"))))
        return out
    }

    func logCommand(_ source: LogSource) -> CommandSpec? { nil }

    func adminPassword() -> String? {
        DotEnv.parse(data.appendingPathComponent("secrets.env"))["INITIAL_ADMIN_PASSWORD"]
    }

    func shellHint() -> String {
        "OV_DATA_DIR='\(data.path)' OV_PORT=\(settings.port) '\(script.path)' status"
    }
}

// MARK: - Docker

final class DockerBackend: GameBackend {
    let kind = BackendKind.docker
    let project: URL
    let settings: LauncherSettings

    init(project: URL, settings: LauncherSettings) {
        self.project = project
        self.settings = settings
    }

    var docker: String { Shell.which("docker") ?? "/usr/local/bin/docker" }
    var dotEnvURL: URL { project.appendingPathComponent(".env") }
    var dotEnv: [String: String] { DotEnv.parse(dotEnvURL) }
    var appURL: String { "http://127.0.0.1:\(settings.port)" }

    var env: [String: String] {
        var e = dotEnv
        e["COMPOSE_FILE"] = "compose.yaml:" + AppPaths.dockerOverride.path
        e["APP_URL"] = appURL
        e["APP_TIMEZONE"] = settings.timezone
        e["GAME_SPEED"] = String(settings.gameSpeed)
        e["GAME_MAP_SIZE"] = String(settings.mapSize)
        e["GAME_ROUND_LENGTH_DAYS"] = String(settings.roundLengthDays)
        e["BUILDKIT_PROGRESS"] = "plain"
        return e
    }

    func compose(_ args: [String]) -> CommandSpec {
        CommandSpec(executable: docker, args: ["compose", "--ansi", "never"] + args, cwd: project, env: env)
    }
    func compose(_ args: String...) -> CommandSpec { compose(args) }
    func script(_ name: String, extraEnv: [String: String] = [:]) -> CommandSpec {
        var e = env
        for (k, v) in extraEnv { e[k] = v }
        return CommandSpec(executable: "/bin/sh", args: [project.appendingPathComponent("scripts/\(name)").path], cwd: project, env: e)
    }

    /// Creates .env with random secrets (if missing) and writes the launcher compose override.
    func prepare() throws {
        let fm = FileManager.default
        if !fm.fileExists(atPath: dotEnvURL.path) {
            let example = (try? String(contentsOf: project.appendingPathComponent(".env.example"), encoding: .utf8)) ?? ""
            var text = example
            let replacements = [
                "replace-with-a-long-random-value": DotEnv.randomSecret(32),
                "replace-with-a-different-long-random-value": DotEnv.randomSecret(32),
                "replace-with-at-least-32-random-bytes": DotEnv.randomSecret(48),
                "replace-with-a-long-admin-password": DotEnv.randomSecret(16),
            ]
            for (k, v) in replacements { text = text.replacingOccurrences(of: k, with: v) }
            if text.isEmpty {
                text = "MARIADB_ROOT_PASSWORD=\(DotEnv.randomSecret(32))\nGAME_DB_PASSWORD=\(DotEnv.randomSecret(32))\nAPP_SECRET=\(DotEnv.randomSecret(48))\nINITIAL_ADMIN_PASSWORD=\(DotEnv.randomSecret(16))\n"
            }
            text += "\n# Created by OpenVillage Local\n"
            try text.write(to: dotEnvURL, atomically: true, encoding: .utf8)
            try? fm.setAttributes([.posixPermissions: 0o600], ofItemAtPath: dotEnvURL.path)
        }
        try overrideYAML().write(to: AppPaths.dockerOverride, atomically: true, encoding: .utf8)
    }

    func overrideYAML() -> String {
        var lines = ["# Generated by OpenVillage Local – changes are overwritten.", "services:"]
        for service in ["bootstrap", "app", "worker"] {
            lines.append("  \(service):")
            lines.append("    environment:")
            lines.append("      APP_URL: \"\(appURL)\"")
            lines.append("      GAME_WORLD_URL: \"\(appURL)/game/\"")
            if service == "app" {
                lines.append("    ports: !override")
                lines.append("      - \"127.0.0.1:\(settings.port):80\"")
            }
            if settings.dockerLiveSource {
                // live source: PHP edits in the checkout are visible without rebuilding the image
                lines.append("    volumes:")
                lines.append("      - type: bind")
                lines.append("        source: \"\(project.path)\"")
                lines.append("        target: /app")
            }
        }
        return lines.joined(separator: "\n") + "\n"
    }

    func doctor() async -> [DoctorItem] {
        var items: [DoctorItem] = []
        let dockerPath = Shell.which("docker")
        items.append(DoctorItem(ok: dockerPath != nil, name: "docker" + (dockerPath.map { "  (\($0))" } ?? ""),
                                hint: "Nainstalujte Docker Desktop, OrbStack nebo Colima"))
        if dockerPath != nil {
            let info = await Shell.run(CommandSpec(executable: docker, args: ["info", "--format", "{{.ServerVersion}}"], cwd: project))
            items.append(DoctorItem(ok: info.succeeded, name: "Docker daemon" + (info.succeeded ? "  (\(info.lines.first ?? ""))" : ""),
                                    hint: "Spusťte Docker Desktop / OrbStack"))
            let comp = await Shell.run(CommandSpec(executable: docker, args: ["compose", "version", "--short"], cwd: project))
            items.append(DoctorItem(ok: comp.succeeded, name: "docker compose" + (comp.succeeded ? "  (\(comp.lines.first ?? ""))" : ""),
                                    hint: "Potřeba Compose v2.24+"))
        }
        let rg = Shell.which("rg")
        items.append(DoctorItem(ok: rg != nil, name: "ripgrep (rg)", hint: "brew install ripgrep – vyžadují ho ověřovací skripty"))
        let node = Shell.which("node")
        items.append(DoctorItem(ok: node != nil, name: "node", hint: "brew install node – jen pro kontrolu JavaScriptu"))
        let hasEnv = FileManager.default.fileExists(atPath: dotEnvURL.path)
        items.append(DoctorItem(ok: true, name: hasEnv ? ".env existuje" : ".env se vytvoří při startu (náhodná hesla)", hint: ""))
        return items
    }

    func installMissing() -> CommandSpec? {
        guard let brew = Shell.which("brew") else { return nil }
        return .sh("\"\(brew)\" install ripgrep node", cwd: project)
    }

    func start() -> CommandSpec { compose("--progress", "plain", "up", "-d", "--build", "--wait", "--wait-timeout", "300") }
    func stop() -> CommandSpec { compose("down") }
    func rebuild() -> CommandSpec { compose("--progress", "plain", "up", "-d", "--build", "--force-recreate", "--wait", "--wait-timeout", "300") }
    func bootstrap() -> CommandSpec { compose("run", "--rm", "bootstrap") }

    func status() async -> [ServiceStatus] {
        let r = await Shell.run(compose("ps", "--all", "--format", "json"))
        var byService: [String: ServiceStatus] = [:]
        var objects: [[String: Any]] = []
        let text = r.output.trimmingCharacters(in: .whitespacesAndNewlines)
        if text.hasPrefix("[") {
            objects = (try? JSONSerialization.jsonObject(with: Data(text.utf8))) as? [[String: Any]] ?? []
        } else {
            for line in r.lines where line.hasPrefix("{") {
                if let o = try? JSONSerialization.jsonObject(with: Data(line.utf8)) as? [String: Any] { objects.append(o) }
            }
        }
        for o in objects {
            let service = o["Service"] as? String ?? "?"
            let state = (o["State"] as? String ?? "").lowercased()
            let health = (o["Health"] as? String ?? "").lowercased()
            let exit = o["ExitCode"] as? Int ?? 0
            var s: ServiceStatus.State = .unknown
            switch state {
            case "running": s = (health == "starting") ? .starting : (health == "unhealthy" ? .failed : .running)
            case "restarting", "created": s = .starting
            case "exited": s = (service == "bootstrap" && exit == 0) ? .stopped : (exit == 0 ? .stopped : .failed)
            default: s = .stopped
            }
            var detail = o["Status"] as? String ?? state
            if service == "bootstrap" && state == "exited" { detail = exit == 0 ? "dokončeno" : "chyba (exit \(exit))" }
            byService[service] = ServiceStatus(name: service, state: s, detail: detail)
        }
        let order = ["database", "redis", "bootstrap", "app", "worker"]
        return order.map { byService[$0] ?? ServiceStatus(name: $0, state: .stopped, detail: "neběží") }
    }

    func sql(_ query: String, global: Bool) -> CommandSpec {
        let e = dotEnv
        let user = e["GAME_DB_USER"] ?? "openvillage"
        let pass = e["GAME_DB_PASSWORD"] ?? "local-game-password"
        let db = global ? (e["GLOBAL_DB_NAME"] ?? "openvillage_global") : (e["GAME_DB_NAME"] ?? "openvillage_game")
        return compose("exec", "-T", "database", "mariadb", "--batch", "--default-character-set=utf8mb4",
                       "-u\(user)", "-p\(pass)", db, "-e", query)
    }

    func tests() async -> [TestItem] {
        [
            TestItem(id: "verify", title: "verify", detail: "Kompletní ověření (scripts/verify.sh)", slow: true),
            TestItem(id: "runtime", title: "runtime", detail: "Runtime regrese – transakce, workery, souběh", slow: true),
            TestItem(id: "formulas", title: "formulas", detail: "Golden hodnoty vzorců (ruleset)"),
            TestItem(id: "complete-round", title: "complete-round", detail: "Regrese celého kola"),
            TestItem(id: "account-export", title: "account-export", detail: "Izolace exportu účtu"),
            TestItem(id: "smoke", title: "smoke", detail: "End-to-end HTTP průchod (vytvoří testovacího hráče)"),
            TestItem(id: "php-lint", title: "php-lint", detail: "Syntaxe všech PHP souborů v kontejneru"),
            TestItem(id: "static", title: "static", detail: "PHPStan (stáhne pinned image)", slow: true),
            TestItem(id: "javascript", title: "javascript", detail: "Syntaxe JavaScriptu (node)"),
            TestItem(id: "game-actions", title: "game-actions", detail: "Statický kontrakt – akce shromaždiště"),
            TestItem(id: "account-actions", title: "account-actions", detail: "Statický kontrakt – akce účtu"),
            TestItem(id: "sitter-actions", title: "sitter-actions", detail: "Statický kontrakt – zástupci"),
            TestItem(id: "hygiene", title: "hygiene", detail: "Hygiena repozitáře (vyžaduje git)"),
            TestItem(id: "clean-install", title: "clean-install", detail: "Instalace z prázdných volumes (izolovaný projekt)", slow: true),
        ]
    }

    func test(_ id: String) -> CommandSpec {
        switch id {
        case "verify": return script("verify.sh")
        case "runtime": return script("test-runtime.sh")
        case "formulas": return script("test-formulas.sh")
        case "complete-round": return script("test-complete-round.sh")
        case "account-export": return compose("exec", "-T", "app", "php", "/app/tests/account-export-regression.php")
        case "smoke": return script("smoke-game.sh")
        case "php-lint":
            return compose("exec", "-T", "app", "sh", "-lc",
                           "n=0; for f in $(find /app/main_script /app/web /app/sections -type f -name '*.php'); do n=$((n+1)); php -l \"$f\" >/dev/null || exit 1; done; echo \"PHP syntax check passed ($n files).\"")
        case "static": return script("check-static.sh")
        case "javascript": return script("check-javascript.sh")
        case "game-actions": return script("check-game-actions.sh")
        case "account-actions": return script("check-account-actions.sh")
        case "sitter-actions": return script("check-sitter-actions.sh")
        case "hygiene": return script("check-public-hygiene.sh")
        case "clean-install":
            var e = env
            e.removeValue(forKey: "COMPOSE_FILE")
            return CommandSpec(executable: "/bin/sh", args: [project.appendingPathComponent("scripts/verify-clean-install.sh").path], cwd: project, env: e)
        default: return .sh("echo 'Neznámý test \(id)'; exit 1", cwd: project)
        }
    }

    func flushCache() -> CommandSpec { compose("exec", "-T", "redis", "redis-cli", "FLUSHALL") }

    func backup() -> CommandSpec {
        let e = dotEnv
        let file = AppPaths.backups.appendingPathComponent("openvillage-docker-\(Self.stamp()).sql.gz").path
        let pass = e["MARIADB_ROOT_PASSWORD"] ?? "local-root-password"
        let dbs = "\(e["GAME_DB_NAME"] ?? "openvillage_game") \(e["GLOBAL_DB_NAME"] ?? "openvillage_global")"
        let cmd = "set -e; \"\(docker)\" compose --ansi never exec -T database mariadb-dump -uroot -p'\(pass)' --single-transaction --routines --default-character-set=utf8mb4 --databases \(dbs) | gzip -c > '\(file)'; gzip -t '\(file)'; echo \"BACKUP  \(file)\""
        return .sh(cmd, cwd: project, env: env)
    }

    func erase() -> CommandSpec { compose("down", "-v") }

    func logSources() -> [LogSource] {
        [
            LogSource(id: "app", title: "app (Apache + PHP)", kind: .command(["app"])),
            LogSource(id: "worker", title: "worker", kind: .command(["worker"])),
            LogSource(id: "bootstrap", title: "bootstrap", kind: .command(["bootstrap"])),
            LogSource(id: "database", title: "database (MariaDB)", kind: .command(["database"])),
            LogSource(id: "redis", title: "redis", kind: .command(["redis"])),
            LogSource(id: "all", title: "všechny služby", kind: .command([])),
        ]
    }

    func logCommand(_ source: LogSource) -> CommandSpec? {
        guard case .command(let services) = source.kind else { return nil }
        return compose(["logs", "-f", "--tail", "500", "--no-color"] + services)
    }

    func adminPassword() -> String? { dotEnv["INITIAL_ADMIN_PASSWORD"] }

    func shellHint() -> String {
        "cd '\(project.path)' && COMPOSE_FILE='compose.yaml:\(AppPaths.dockerOverride.path)' docker compose ps"
    }

    static func stamp() -> String {
        let f = DateFormatter()
        f.dateFormat = "yyyyMMdd'T'HHmmss"
        f.locale = Locale(identifier: "en_US_POSIX")
        return f.string(from: Date())
    }
}

struct LauncherError: LocalizedError {
    let message: String
    init(_ message: String) { self.message = message }
    var errorDescription: String? { message }
}
