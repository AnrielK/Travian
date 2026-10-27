import SwiftUI

@main
@MainActor
struct TravianT46App: App {
    @StateObject private var server = GameServer.shared

    var body: some Scene {
        WindowGroup {
            RootView().environmentObject(server)
        }
    }
}
