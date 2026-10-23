import SwiftUI

@main
@MainActor
struct TravianLocalApp: App {
    @StateObject private var server = GameServer.shared

    var body: some Scene {
        WindowGroup {
            RootView().environmentObject(server)
        }
    }
}
