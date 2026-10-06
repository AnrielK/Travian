import SwiftUI

struct SettingsView: View {
    @EnvironmentObject var controller: ServerController
    @State private var draft = LauncherSettings()
    @State private var loaded = false

    var running: Bool { controller.healthy || controller.phase == .running }

    var body: some View {
        Form {
            SwiftUI.Section("Projekt") {
                HStack {
                    Text(draft.projectPath.isEmpty ? "nevybráno" : draft.projectPath)
                        .font(.callout.monospaced()).lineLimit(2).truncationMode(.middle)
                        .foregroundStyle(ProjectLocator.isProject(URL(fileURLWithPath: draft.projectPath)) ? Color.primary : Color.red)
                    Spacer()
                    Button("Změnit…") {
                        pickProjectFolder(controller)
                        draft.projectPath = controller.settings.projectPath
                    }
                }
                Picker("Režim běhu", selection: $draft.backend) {
                    ForEach(BackendKind.allCases) { Text($0.title).tag($0) }
                }
                Text(draft.backend.subtitle).font(.caption).foregroundStyle(.secondary)
            }

            SwiftUI.Section("Síť") {
                TextField("Port webu", value: $draft.port, format: .number.grouping(.never))
                if draft.backend == .native {
                    TextField("Port Redisu", value: $draft.redisPort, format: .number.grouping(.never))
                    TextField("TCP port MariaDB (0 = jen socket)", value: $draft.dbPort, format: .number.grouping(.never))
                    Text("S TCP portem se k databázi připojíte i z TablePlus/Sequel Ace (uživatel openvillage, heslo v secrets.env).")
                        .font(.caption).foregroundStyle(.secondary)
                }
            }

            SwiftUI.Section("Svět") {
                Stepper("Rychlost hry: \(draft.gameSpeed)×", value: $draft.gameSpeed, in: 1...5000, step: draft.gameSpeed < 10 ? 1 : 10)
                Stepper("Velikost mapy: \(draft.mapSize) (\(draft.mapSize * 2 + 1)×\(draft.mapSize * 2 + 1))", value: $draft.mapSize, in: 10...400, step: 5)
                Stepper("Délka kola: \(draft.roundLengthDays) dní", value: $draft.roundLengthDays, in: 1...365)
                TextField("Časové pásmo", text: $draft.timezone)
                Text("Velikost mapy se použije jen při vytvoření nového světa (po „Smazat svět“). Rychlost a délka kola platí po restartu.")
                    .font(.caption).foregroundStyle(.secondary)
            }

            if draft.backend == .docker {
                SwiftUI.Section("Docker") {
                    Toggle("Živý zdrojový kód (bind mount do /app)", isOn: $draft.dockerLiveSource)
                    Text("Úpravy PHP v projektu se projeví bez přestavění image. Vypněte pro test přesně sestaveného image.")
                        .font(.caption).foregroundStyle(.secondary)
                }
            }

            SwiftUI.Section("Chování") {
                Toggle("Zastavit server při ukončení aplikace", isOn: $draft.stopOnQuit)
                Toggle("Spustit server po otevření aplikace", isOn: $draft.startOnLaunch)
            }

            HStack {
                if running && needsRestart {
                    Label("Změna se projeví po restartu serveru.", systemImage: "info.circle").font(.caption).foregroundStyle(.orange)
                }
                Spacer()
                Button("Vrátit") { draft = controller.settings }.disabled(draft == controller.settings)
                Button("Uložit") { save() }
                    .keyboardShortcut(.defaultAction)
                    .disabled(draft == controller.settings)
            }
        }
        .formStyle(.grouped)
        .padding()
        .onAppear { if !loaded { draft = controller.settings; loaded = true } }
    }

    var needsRestart: Bool {
        let s = controller.settings
        return draft.port != s.port || draft.gameSpeed != s.gameSpeed || draft.roundLengthDays != s.roundLengthDays
            || draft.timezone != s.timezone || draft.redisPort != s.redisPort || draft.dbPort != s.dbPort
            || draft.dockerLiveSource != s.dockerLiveSource
    }

    func save() {
        if running && draft.backend != controller.settings.backend {
            controller.lastError = "Režim běhu změňte až po zastavení serveru."
            draft.backend = controller.settings.backend
        }
        controller.settings = draft
    }
}
