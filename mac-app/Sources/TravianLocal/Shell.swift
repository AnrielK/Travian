import Foundation

/// Result of a finished command.
struct CommandResult {
    let status: Int32
    let output: String
    var ok: Bool { status == 0 }
}

enum ShellError: LocalizedError {
    case cannotOpen(URL)
    var errorDescription: String? {
        switch self {
        case .cannotOpen(let url): return "Nelze otevřít soubor \(url.path)"
        }
    }
}

enum Shell {
    /// Runs a command to completion off the main thread and returns its combined stdout+stderr.
    static func run(_ executable: String,
                    _ arguments: [String],
                    stdinFile: URL? = nil,
                    environment: [String: String]? = nil,
                    currentDirectory: URL? = nil) async throws -> CommandResult {
        try await withCheckedThrowingContinuation { continuation in
            let process = Process()
            process.executableURL = URL(fileURLWithPath: executable)
            process.arguments = arguments
            if let environment { process.environment = environment }
            if let currentDirectory { process.currentDirectoryURL = currentDirectory }

            let pipe = Pipe()
            process.standardOutput = pipe
            process.standardError = pipe
            if let stdinFile {
                guard let handle = try? FileHandle(forReadingFrom: stdinFile) else {
                    continuation.resume(throwing: ShellError.cannotOpen(stdinFile))
                    return
                }
                process.standardInput = handle
            } else {
                process.standardInput = FileHandle.nullDevice
            }

            do {
                try process.run()
            } catch {
                continuation.resume(throwing: error)
                return
            }
            DispatchQueue.global(qos: .userInitiated).async {
                let data = pipe.fileHandleForReading.readDataToEndOfFile()
                process.waitUntilExit()
                continuation.resume(returning: CommandResult(status: process.terminationStatus,
                                                             output: String(decoding: data, as: UTF8.self)))
            }
        }
    }

    /// True when something accepts TCP connections on 127.0.0.1:port.
    static func isPortOpen(_ port: Int) -> Bool {
        let fd = socket(AF_INET, SOCK_STREAM, 0)
        guard fd >= 0 else { return false }
        defer { close(fd) }
        var addr = sockaddr_in()
        addr.sin_len = UInt8(MemoryLayout<sockaddr_in>.size)
        addr.sin_family = sa_family_t(AF_INET)
        addr.sin_port = in_port_t(UInt16(truncatingIfNeeded: port).bigEndian)
        addr.sin_addr.s_addr = inet_addr("127.0.0.1")
        let result = withUnsafePointer(to: &addr) { pointer in
            pointer.withMemoryRebound(to: sockaddr.self, capacity: 1) {
                connect(fd, $0, socklen_t(MemoryLayout<sockaddr_in>.size))
            }
        }
        return result == 0
    }
}

/// A long-running child process (MariaDB server, PHP server) whose output is streamed line by line.
final class ManagedProcess {
    let name: String
    private let process = Process()
    private let pipe = Pipe()
    private var partial = ""
    private let onLine: (String) -> Void
    private let onExit: (Int32) -> Void

    init(name: String,
         executable: String,
         arguments: [String],
         environment: [String: String]? = nil,
         currentDirectory: URL? = nil,
         onLine: @escaping (String) -> Void,
         onExit: @escaping (Int32) -> Void) {
        self.name = name
        self.onLine = onLine
        self.onExit = onExit
        process.executableURL = URL(fileURLWithPath: executable)
        process.arguments = arguments
        if let environment { process.environment = environment }
        if let currentDirectory { process.currentDirectoryURL = currentDirectory }
        process.standardOutput = pipe
        process.standardError = pipe
        process.standardInput = FileHandle.nullDevice
    }

    var isRunning: Bool { process.isRunning }
    var pid: Int32 { process.processIdentifier }

    func start() throws {
        pipe.fileHandleForReading.readabilityHandler = { [weak self] handle in
            let data = handle.availableData
            guard let self else { return }
            if data.isEmpty {
                handle.readabilityHandler = nil
                return
            }
            self.consume(String(decoding: data, as: UTF8.self))
        }
        process.terminationHandler = { [weak self] proc in
            guard let self else { return }
            self.pipe.fileHandleForReading.readabilityHandler = nil
            if !self.partial.isEmpty { self.onLine(self.partial); self.partial = "" }
            self.onExit(proc.terminationStatus)
        }
        try process.run()
    }

    private func consume(_ chunk: String) {
        partial += chunk
        while let range = partial.range(of: "\n") {
            let line = String(partial[partial.startIndex..<range.lowerBound])
            partial = String(partial[range.upperBound...])
            if !line.isEmpty { onLine(line) }
        }
    }

    /// SIGTERM, wait up to `timeout` seconds, then SIGKILL. Blocks the caller.
    func stop(timeout: TimeInterval = 8) {
        guard process.isRunning else { return }
        process.terminate()
        let deadline = Date().addingTimeInterval(timeout)
        while process.isRunning && Date() < deadline {
            Thread.sleep(forTimeInterval: 0.1)
        }
        if process.isRunning { kill(process.processIdentifier, SIGKILL) }
    }
}

/// Follows a growing log file (like `tail -f`) and reports new lines.
final class FileTailer {
    private let url: URL
    private var offset: UInt64 = 0
    private var partial = ""

    init(url: URL, fromEnd: Bool = true) {
        self.url = url
        if fromEnd, let size = try? FileManager.default.attributesOfItem(atPath: url.path)[.size] as? UInt64 {
            offset = size
        }
    }

    func poll() -> [String] {
        guard let handle = try? FileHandle(forReadingFrom: url) else { return [] }
        defer { try? handle.close() }
        let size = (try? handle.seekToEnd()) ?? 0
        if size < offset { offset = 0 }           // file was truncated or replaced
        guard size > offset else { return [] }
        try? handle.seek(toOffset: offset)
        let data = (try? handle.read(upToCount: Int(size - offset))) ?? Data()
        offset = size
        partial += String(decoding: data, as: UTF8.self)
        var lines: [String] = []
        while let range = partial.range(of: "\n") {
            lines.append(String(partial[partial.startIndex..<range.lowerBound]))
            partial = String(partial[range.upperBound...])
        }
        return lines
    }
}
