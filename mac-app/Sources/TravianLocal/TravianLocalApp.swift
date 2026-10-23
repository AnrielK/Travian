import SwiftUI
import AppKit

@main
@MainActor
struct TravianLocalApp: App {
    @NSApplicationDelegateAdaptor(AppDelegate.self) private var appDelegate
    @StateObject private var settings: AppSettings
    @StateObject private var server: ServerController

    init() {
        let settings = AppSettings()
        _settings = StateObject(wrappedValue: settings)
        _server = StateObject(wrappedValue: ServerController(settings: settings))
    }

    var body: some Scene {
        WindowGroup("Travian Local") {
            ContentView()
                .environmentObject(settings)
                .environmentObject(server)
                .frame(minWidth: 1100, minHeight: 720)
        }
        .commands {
            CommandGroup(replacing: .newItem) {}
            CommandMenu("Server") {
                Button("Spustit") { Task { await server.start() } }
                    .disabled(server.state == .running || server.state == .starting)
                Button("Zastavit") { server.stop() }
                    .disabled(server.state != .running)
                Divider()
                Button("Dokončit vše hned") { Task { _ = await server.fastForward() } }
                    .keyboardShortcut("f", modifiers: [.command, .shift])
                    .disabled(server.state != .running)
                Button("Nové okno hráče") { PlayerWindows.open(baseURL: server.gameURL) }
                    .keyboardShortcut("n", modifiers: [.command, .shift])
                    .disabled(server.state != .running)
                Divider()
                Button("Otevřít datovou složku") { server.openDataFolder() }
            }
        }

        Settings {
            SettingsView()
                .environmentObject(settings)
                .environmentObject(server)
        }
    }
}

@MainActor
final class AppDelegate: NSObject, NSApplicationDelegate {
    func applicationDidFinishLaunching(_ notification: Notification) {
        // Needed when started via `swift run` (no .app bundle): show a Dock icon and focus the window.
        NSApp.setActivationPolicy(.regular)
        NSApp.activate(ignoringOtherApps: true)
    }

    func applicationShouldTerminateAfterLastWindowClosed(_ sender: NSApplication) -> Bool { true }

    func applicationWillTerminate(_ notification: Notification) {
        // Child processes (MariaDB, PHP) would otherwise keep running after the app quits.
        ServerController.shared?.stopForTermination()
    }
}
