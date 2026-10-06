import SwiftUI

@main
@MainActor
struct OpenVillageApp: App {
    @StateObject private var runtime = GameRuntime.shared

    var body: some Scene {
        WindowGroup {
            RootView().environmentObject(runtime)
        }
    }
}
