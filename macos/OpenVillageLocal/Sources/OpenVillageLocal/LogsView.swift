import SwiftUI
import AppKit

/// Follows a log file (native) or a `docker compose logs -f` stream (Docker).
@MainActor
final class LogFollower: ObservableObject {
    @Published var lines: [String] = []
    @Published var missing = false
    private var timer: Timer?
    private var offset: UInt64 = 0
    private var file: URL?
    private var handle: CommandHandle?
    private let maxLines = 6000

    func follow(_ source: LogSource, backend: GameBackend) {
        stop()
        lines = []
        missing = false
        switch source.kind {
        case .file(let url):
            file = url
            offset = 0
            // start with the last ~256 KB
            if let size = (try? FileManager.default.attributesOfItem(atPath: url.path)[.size]) as? UInt64, size > 262_144 {
                offset = size - 262_144
            }
            readFile()
            timer = Timer.scheduledTimer(withTimeInterval: 0.8, repeats: true) { [weak self] _ in
                Task { @MainActor in self?.readFile() }
            }
        case .command:
            guard let spec = backend.logCommand(source) else { return }
            let h = CommandHandle()
            handle = h
            Task {
                _ = await Shell.run(spec, handle: h, collect: false) { [weak self] newLines in
                    MainActor.assumeIsolated { self?.append(newLines) }
                }
            }
        }
    }

    func stop() {
        timer?.invalidate(); timer = nil
        handle?.cancel(); handle = nil
        file = nil
    }

    func clear() { lines = [] }

    private func append(_ new: [String]) {
        lines.append(contentsOf: new)
        if lines.count > maxLines { lines.removeFirst(lines.count - maxLines) }
    }

    private func readFile() {
        guard let file else { return }
        guard let fh = try? FileHandle(forReadingFrom: file) else { missing = true; return }
        defer { try? fh.close() }
        missing = false
        let size = (try? fh.seekToEnd()) ?? 0
        if size < offset { offset = 0; append(["──── soubor byl zkrácen / přetočen ────"]) }
        guard size > offset else { return }
        try? fh.seek(toOffset: offset)
        guard let data = try? fh.read(upToCount: Int(size - offset)), !data.isEmpty else { return }
        // only consume complete lines
        if let lastNL = data.lastIndex(of: 0x0A) {
            let chunk = data[data.startIndex...lastNL]
            offset += UInt64(chunk.count)
            let text = String(decoding: chunk, as: UTF8.self)
            var newLines = text.components(separatedBy: "\n")
            if newLines.last == "" { newLines.removeLast() }
            append(newLines.map(Shell.stripANSI))
        }
    }
}

struct LogsView: View {
    @EnvironmentObject var controller: ServerController
    @StateObject private var follower = LogFollower()
    @State private var sourceID: String = ""
    @State private var filter = ""
    @State private var onlyProblems = false
    @State private var hideNoise = true

    var sources: [LogSource] { controller.backend?.logSources() ?? [] }

    var visible: [String] {
        follower.lines.filter { line in
            if hideNoise, isNoise(line) { return false }
            if onlyProblems, !isProblem(line) { return false }
            if !filter.isEmpty, !line.localizedCaseInsensitiveContains(filter) { return false }
            return true
        }
    }

    var body: some View {
        VStack(spacing: 0) {
            HStack(spacing: 10) {
                Picker("Zdroj", selection: $sourceID) {
                    ForEach(sources) { Text($0.title).tag($0.id) }
                }
                .frame(width: 280)
                TextField("Filtr", text: $filter).textFieldStyle(.roundedBorder).frame(maxWidth: 260)
                Toggle("Jen chyby a varování", isOn: $onlyProblems).toggleStyle(.checkbox)
                Toggle("Skrýt šum", isOn: $hideNoise).toggleStyle(.checkbox)
                    .help("Skryje řádky Accepted/Closing z PHP serveru")
                Spacer()
                if case .file(let url)? = sources.first(where: { $0.id == sourceID })?.kind {
                    Button { NSWorkspace.shared.activateFileViewerSelecting([url]) } label: { Image(systemName: "folder") }
                        .help("Ukázat ve Finderu")
                }
            }
            .padding(10)
            Divider()
            if follower.missing {
                Text("Soubor zatím neexistuje – vznikne po prvním spuštění / první chybě.")
                    .foregroundStyle(.secondary).frame(maxWidth: .infinity, maxHeight: .infinity)
            } else {
                ConsoleView(lines: visible, onClear: { follower.clear() },
                            title: "\(visible.count) řádků" + (visible.count != follower.lines.count ? " (z \(follower.lines.count))" : ""))
            }
        }
        .onAppear {
            if sourceID.isEmpty || !sources.contains(where: { $0.id == sourceID }) { sourceID = sources.first?.id ?? "" }
            restart()
        }
        .onDisappear { follower.stop() }
        .onChange(of: sourceID) { _, _ in restart() }
        .onChange(of: controller.settings.backend) { _, _ in
            sourceID = sources.first?.id ?? ""
            restart()
        }
    }

    func restart() {
        guard let backend = controller.backend, let src = sources.first(where: { $0.id == sourceID }) else { return }
        follower.follow(src, backend: backend)
    }

    func isNoise(_ l: String) -> Bool {
        (l.hasSuffix(" Accepted") || l.hasSuffix(" Closing") || l.contains("Failed to poll event"))
    }

    func isProblem(_ l: String) -> Bool {
        let s = l.lowercased()
        return s.contains("error") || s.contains("fatal") || s.contains("warning") || s.contains("exception")
            || s.contains("uncaught") || s.contains("deprecated") || s.contains("notice") || s.contains(" [4") || s.contains(" [5")
    }
}
