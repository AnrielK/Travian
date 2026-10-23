// swift-tools-version:5.9
import PackageDescription

let package = Package(
    name: "TravianLocal",
    platforms: [.macOS(.v13)],
    targets: [
        .executableTarget(
            name: "TravianLocal",
            path: "Sources/TravianLocal"
        )
    ]
)
