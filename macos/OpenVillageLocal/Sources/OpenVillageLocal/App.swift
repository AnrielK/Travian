import SwiftUI
import AppKit

@main
struct OpenVillageLocalApp: App {
    @NSApplicationDelegateAdaptor(AppDelegate.self) private var appDelegate
    @StateObject private var controller = ServerController.shared

    var body: some Scene {
        Window("OpenVillage Local", id: "main") {
            MainView()
                .environmentObject(controller)
                .frame(minWidth: 1000, minHeight: 660)
        }
        .defaultSize(width: 1180, height: 780)
        .commands {
            CommandMenu("Server") {
                Button("Spustit") { Task { await controller.start() } }
                    .keyboardShortcut("r", modifiers: [.command, .shift])
                Button("Zastavit") { Task { await controller.stop() } }
                    .keyboardShortcut(".", modifiers: [.command, .shift])
                Button("Restart") { Task { await controller.restart() } }
                Divider()
                Button("Otevřít ve výchozím prohlížeči") { NSWorkspace.shared.open(controller.baseURL) }
                    .keyboardShortcut("o", modifiers: [.command, .shift])
            }
        }

        WindowGroup("Hra", id: "game", for: UUID.self) { $profileID in
            GameWindowView(profileID: profileID)
                .environmentObject(controller)
        }
        .defaultSize(width: 1340, height: 900)

        Settings {
            SettingsView()
                .environmentObject(controller)
                .frame(width: 560)
        }
    }
}

@MainActor
final class AppDelegate: NSObject, NSApplicationDelegate {
    func applicationDidFinishLaunching(_ notification: Notification) {
        NSApp.setActivationPolicy(.regular)
        NSApp.activate(ignoringOtherApps: true)
    }

    func applicationShouldTerminateAfterLastWindowClosed(_ sender: NSApplication) -> Bool { false }

    func applicationShouldTerminate(_ sender: NSApplication) -> NSApplication.TerminateReply {
        let controller = ServerController.shared
        guard controller.settings.stopOnQuit, controller.phase != .stopped else { return .terminateNow }
        Task { @MainActor in
            await controller.stopForQuit()
            NSApp.reply(toApplicationShouldTerminate: true)
        }
        return .terminateLater
    }
}
