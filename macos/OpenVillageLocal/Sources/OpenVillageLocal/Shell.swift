import Foundation

/// A command ready to be executed.
struct CommandSpec {
    var executable: String
    var args: [String]
    var cwd: URL?
    var env: [String: String] = [:]

    /// `/bin/sh -c …` helper for pipelines.
    static func sh(_ script: String, cwd: URL?, env: [String: String] = [:]) -> CommandSpec {
        CommandSpec(executable: "/bin/sh", args: ["-c", script], cwd: cwd, env: env)
    }

    var displayString: String {
        ([URL(fileURLWithPath: executable).lastPathComponent] + args.map { $0.contains(" ") ? "'\($0)'" : $0 })
            .joined(separator: " ")
    }
}

struct CommandResult {
    let exitCode: Int32
    let lines: [String]
    var succeeded: Bool { exitCode == 0 }
    var output: String { lines.joined(separator: "\n") }
}

/// Handle that allows cancelling a running command (e.g. a `logs -f` stream or a long test).
final class CommandHandle: @unchecked Sendable {
    private let lock = NSLock()
    private var process: Process?
    private(set) var cancelled = false

    func attach(_ p: Process) {
        lock.lock(); process = p; let c = cancelled; lock.unlock()
        if c { p.terminate() }
    }

    func cancel() {
        lock.lock(); cancelled = true; let p = process; lock.unlock()
        if let p, p.isRunning {
            p.interrupt()
            DispatchQueue.global().asyncAfter(deadline: .now() + 3) { if p.isRunning { p.terminate() } }
        }
    }
}

enum Shell {
    /// GUI apps start with a minimal PATH – add Homebrew, Docker Desktop, OrbStack and Colima locations.
    static var searchPaths: [String] {
        let home = NSHomeDirectory()
        return [
            "/opt/homebrew/opt/php@8.3/bin", "/usr/local/opt/php@8.3/bin",
            "/opt/homebrew/bin", "/opt/homebrew/sbin", "/usr/local/bin", "/usr/local/sbin",
            home + "/.docker/bin", home + "/.orbstack/bin", home + "/.colima/bin",
            "/Applications/Docker.app/Contents/Resources/bin",
            "/usr/bin", "/bin", "/usr/sbin", "/sbin",
        ]
    }

    static func environment(_ extra: [String: String] = [:]) -> [String: String] {
        var env = ProcessInfo.processInfo.environment
        var path: [String] = []
        for p in searchPaths + (env["PATH"] ?? "").split(separator: ":").map(String.init) where !path.contains(p) {
            path.append(p)
        }
        env["PATH"] = path.joined(separator: ":")
        if env["LANG"] == nil { env["LANG"] = "en_US.UTF-8" }
        env["NO_COLOR"] = "1"
        env["HOMEBREW_NO_AUTO_UPDATE"] = "1"
        for (k, v) in extra { env[k] = v }
        return env
    }

    static func which(_ name: String) -> String? {
        for dir in (environment()["PATH"] ?? "").split(separator: ":") {
            let p = "\(dir)/\(name)"
            if FileManager.default.isExecutableFile(atPath: p) { return p }
        }
        return nil
    }

    private static let ansi = try! NSRegularExpression(pattern: "\u{1B}\\[[0-9;?]*[ -/]*[@-~]")

    static func stripANSI(_ s: String) -> String {
        guard s.contains("\u{1B}") else { return s }
        return ansi.stringByReplacingMatches(in: s, range: NSRange(s.startIndex..., in: s), withTemplate: "")
    }

    /// Runs a command. stdout+stderr are merged and delivered line by line (on the main queue) to `onLines`.
    @discardableResult
    static func run(_ spec: CommandSpec,
                    handle: CommandHandle? = nil,
                    collect: Bool = true,
                    onLines: (([String]) -> Void)? = nil) async -> CommandResult {
        await withCheckedContinuation { (cont: CheckedContinuation<CommandResult, Never>) in
            DispatchQueue.global(qos: .userInitiated).async {
                cont.resume(returning: runBlocking(spec, handle: handle, collect: collect, onLines: onLines))
            }
        }
    }

    static func runBlocking(_ spec: CommandSpec,
                            handle: CommandHandle? = nil,
                            collect: Bool = true,
                            onLines: (([String]) -> Void)? = nil) -> CommandResult {
        let process = Process()
        process.executableURL = URL(fileURLWithPath: spec.executable)
        process.arguments = spec.args
        if let cwd = spec.cwd { process.currentDirectoryURL = cwd }
        process.environment = environment(spec.env)
        let pipe = Pipe()
        process.standardOutput = pipe
        process.standardError = pipe
        process.standardInput = FileHandle.nullDevice

        let output = OutputCollector(collect: collect, onLines: onLines)

        do {
            try process.run()
        } catch {
            let msg = "Nelze spustit \(spec.executable): \(error.localizedDescription)"
            output.emit([msg])
            return CommandResult(exitCode: 127, lines: [msg])
        }
        handle?.attach(process)

        // Reader thread: reads until EOF. A detached grandchild could keep the pipe open,
        // so after the process exits we wait at most 1.5 s for the reader.
        let readerDone = DispatchSemaphore(value: 0)
        let reader = pipe.fileHandleForReading
        Thread.detachNewThread {
            while true {
                let data = reader.availableData
                if data.isEmpty { break }
                output.feed(data)
            }
            readerDone.signal()
        }
        process.waitUntilExit()
        _ = readerDone.wait(timeout: .now() + 1.5)
        output.finish()
        return CommandResult(exitCode: process.terminationStatus, lines: output.lines)
    }
}

/// Thread-safe line splitter for process output.
final class OutputCollector: @unchecked Sendable {
    private let lock = NSLock()
    private var pending = Data()
    private var collected: [String] = []
    private let collect: Bool
    private let onLines: (([String]) -> Void)?

    init(collect: Bool, onLines: (([String]) -> Void)?) {
        self.collect = collect
        self.onLines = onLines
    }

    var lines: [String] { lock.lock(); defer { lock.unlock() }; return collected }

    func emit(_ lines: [String]) {
        guard !lines.isEmpty else { return }
        if collect { lock.lock(); collected.append(contentsOf: lines); lock.unlock() }
        if let onLines { DispatchQueue.main.async { onLines(lines) } }
    }

    func feed(_ data: Data) {
        lock.lock(); pending.append(data); let lines = takeLines(final: false); lock.unlock()
        emit(lines)
    }

    func finish() {
        lock.lock(); let lines = takeLines(final: true); lock.unlock()
        emit(lines)
    }

    private func takeLines(final: Bool) -> [String] {
        var lines: [String] = []
        while let nl = pending.firstIndex(of: 0x0A) {
            let lineData = pending[pending.startIndex..<nl]
            var s = String(decoding: lineData, as: UTF8.self)
            pending.removeSubrange(pending.startIndex...nl)
            if s.hasSuffix("\r") { s.removeLast() }
            // progress output uses bare \r – keep the last state only
            if let r = s.lastIndex(of: "\r") { s = String(s[s.index(after: r)...]) }
            lines.append(Shell.stripANSI(s))
        }
        if final, !pending.isEmpty {
            lines.append(Shell.stripANSI(String(decoding: pending, as: UTF8.self)))
            pending.removeAll()
        }
        return lines
    }
}

/// Parses KEY=VALUE files (.env, secrets.env).
enum DotEnv {
    static func parse(_ url: URL) -> [String: String] {
        guard let text = try? String(contentsOf: url, encoding: .utf8) else { return [:] }
        var out: [String: String] = [:]
        for raw in text.split(whereSeparator: \.isNewline) {
            let line = raw.trimmingCharacters(in: .whitespaces)
            guard !line.isEmpty, !line.hasPrefix("#"), let eq = line.firstIndex(of: "=") else { continue }
            let key = String(line[..<eq]).trimmingCharacters(in: .whitespaces)
            var value = String(line[line.index(after: eq)...]).trimmingCharacters(in: .whitespaces)
            if value.count >= 2, (value.hasPrefix("\"") && value.hasSuffix("\"")) || (value.hasPrefix("'") && value.hasSuffix("'")) {
                value = String(value.dropFirst().dropLast())
            }
            out[key] = value
        }
        return out
    }

    static func randomSecret(_ length: Int) -> String {
        let chars = Array("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789")
        var rng = SystemRandomNumberGenerator()
        return String((0..<length).map { _ in chars[Int(rng.next() % UInt64(chars.count))] })
    }
}
