import SwiftUI

@MainActor
final class TestsModel: ObservableObject {
    enum Status { case idle, running, passed, failed, cancelled }

    struct Run {
        var status: Status = .idle
        var seconds: Double = 0
        var output: [String] = []
        var finishedAt: Date?
    }

    @Published var items: [TestItem] = []
    @Published var runs: [String: Run] = [:]
    @Published var selected: String?
    @Published var runningAll = false
    private var handle: CommandHandle?
    private var stopAll = false
    private var loadedFor: String = ""

    func load(_ controller: ServerController) async {
        guard let backend = controller.backend else { items = []; return }
        let key = backend.kind.rawValue + backend.project.path
        guard key != loadedFor || items.isEmpty else { return }
        loadedFor = key
        items = await backend.tests()
        runs = [:]
        if selected == nil { selected = items.first?.id }
    }

    var isRunning: Bool { runs.values.contains { $0.status == .running } }

    func run(_ id: String, controller: ServerController) async -> Bool {
        guard let backend = controller.backend else { return false }
        var run = Run(status: .running)
        runs[id] = run
        selected = id
        let started = Date()
        let h = CommandHandle()
        handle = h
        let spec = backend.test(id)
        runs[id]?.output = ["$ \(spec.displayString)"]
        let result = await Shell.run(spec, handle: h) { [weak self] lines in
            MainActor.assumeIsolated {
                guard let self else { return }
                self.runs[id]?.output.append(contentsOf: lines)
            }
        }
        run = runs[id] ?? run
        run.seconds = Date().timeIntervalSince(started)
        run.finishedAt = Date()
        run.status = h.cancelled ? .cancelled : (result.succeeded ? .passed : .failed)
        run.output.append(result.succeeded ? "✓ OK (\(String(format: "%.1f", run.seconds)) s)" : "✗ exit \(result.exitCode)")
        runs[id] = run
        handle = nil
        return result.succeeded
    }

    func runAll(controller: ServerController, includeSlow: Bool) async {
        runningAll = true
        stopAll = false
        for item in items where includeSlow || !item.slow {
            if stopAll { break }
            _ = await run(item.id, controller: controller)
        }
        runningAll = false
    }

    func runFailed(controller: ServerController) async {
        runningAll = true
        stopAll = false
        for item in items where runs[item.id]?.status == .failed {
            if stopAll { break }
            _ = await run(item.id, controller: controller)
        }
        runningAll = false
    }

    func cancel() {
        stopAll = true
        handle?.cancel()
    }

    var summary: (passed: Int, failed: Int) {
        (runs.values.filter { $0.status == .passed }.count, runs.values.filter { $0.status == .failed }.count)
    }
}

struct TestsView: View {
    @EnvironmentObject var controller: ServerController
    @ObservedObject var model: TestsModel
    @State private var includeSlow = true

    var body: some View {
        HSplitView {
            VStack(spacing: 0) {
                header
                Divider()
                List(selection: $model.selected) {
                    ForEach(model.items) { item in
                        row(item)
                    }
                }
                .listStyle(.inset)
            }
            .frame(minWidth: 360, idealWidth: 420)

            VStack(spacing: 0) {
                if let id = model.selected {
                    ConsoleView(lines: model.runs[id]?.output ?? ["Test zatím neproběhl. Spusťte ho tlačítkem ▶︎."],
                                title: "Výstup – \(id)")
                } else {
                    Text("Vyberte test").foregroundStyle(.secondary).frame(maxWidth: .infinity, maxHeight: .infinity)
                }
            }
            .frame(minWidth: 380)
        }
        .task { await model.load(controller) }
        .onChange(of: controller.settings.backend) { _, _ in Task { await model.load(controller) } }
    }

    var header: some View {
        VStack(alignment: .leading, spacing: 8) {
            HStack {
                Text("Testy").font(.title2.weight(.semibold))
                Spacer()
                let s = model.summary
                if s.passed + s.failed > 0 {
                    Text("✓ \(s.passed)").foregroundStyle(.green)
                    Text("✗ \(s.failed)").foregroundStyle(s.failed > 0 ? .red : .secondary)
                }
            }
            Text(controller.settings.backend == .docker
                 ? "Spouští skripty ze složky scripts/ proti běžícím kontejnerům."
                 : "Spouští regresní testy z tests/ přímo nativním PHP proti lokální databázi.")
                .font(.caption).foregroundStyle(.secondary)
            HStack {
                if model.runningAll || model.isRunning {
                    Button(role: .cancel) { model.cancel() } label: { Label("Zastavit", systemImage: "stop.fill") }
                } else {
                    Button { Task { await model.runAll(controller: controller, includeSlow: includeSlow) } } label: {
                        Label("Spustit vše", systemImage: "play.fill")
                    }
                    .buttonStyle(.borderedProminent)
                    .disabled(!controller.healthy)
                    if model.summary.failed > 0 {
                        Button { Task { await model.runFailed(controller: controller) } } label: {
                            Label("Znovu neúspěšné", systemImage: "arrow.counterclockwise")
                        }
                    }
                }
                Toggle("i pomalé", isOn: $includeSlow).toggleStyle(.checkbox)
                Spacer()
            }
            if !controller.healthy {
                Label("Server neběží – většina testů potřebuje běžící svět.", systemImage: "exclamationmark.triangle")
                    .font(.caption).foregroundStyle(.orange)
            }
        }
        .padding(12)
    }

    func row(_ item: TestItem) -> some View {
        let run = model.runs[item.id]
        return HStack(spacing: 10) {
            statusIcon(run?.status ?? .idle)
                .frame(width: 18)
            VStack(alignment: .leading, spacing: 2) {
                HStack(spacing: 6) {
                    Text(item.title).font(.body.monospaced())
                    if item.slow { Text("pomalý").font(.caption2).foregroundStyle(.secondary)
                        .padding(.horizontal, 5).background(Capsule().fill(Color.secondary.opacity(0.15))) }
                }
                Text(item.detail).font(.caption).foregroundStyle(.secondary).lineLimit(2)
            }
            Spacer()
            if let run, run.status != .running, run.status != .idle {
                Text(String(format: "%.1f s", run.seconds)).font(.caption.monospacedDigit()).foregroundStyle(.secondary)
            }
            Button { Task { _ = await model.run(item.id, controller: controller) } } label: { Image(systemName: "play.circle") }
                .buttonStyle(.borderless)
                .disabled(model.isRunning)
                .help("Spustit test")
        }
        .padding(.vertical, 3)
    }

    @ViewBuilder
    func statusIcon(_ s: TestsModel.Status) -> some View {
        switch s {
        case .idle: Image(systemName: "circle").foregroundStyle(.tertiary)
        case .running: ProgressView().controlSize(.small)
        case .passed: Image(systemName: "checkmark.circle.fill").foregroundStyle(.green)
        case .failed: Image(systemName: "xmark.circle.fill").foregroundStyle(.red)
        case .cancelled: Image(systemName: "minus.circle.fill").foregroundStyle(.orange)
        }
    }
}
