#!/usr/bin/env bash
# Builds "OpenVillage Local.app" into macos/build/. Requires Xcode or the Command Line Tools (Swift 5.9+), macOS 14+.
set -euo pipefail
cd "$(dirname "$0")/OpenVillageLocal"
swift build -c release
BIN="$(swift build -c release --show-bin-path)/OpenVillageLocal"
APP="../build/OpenVillage Local.app"
rm -rf "$APP"
mkdir -p "$APP/Contents/MacOS" "$APP/Contents/Resources"
cp "$BIN" "$APP/Contents/MacOS/OpenVillageLocal"
cat > "$APP/Contents/Info.plist" <<'PLIST'
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>CFBundleName</key><string>OpenVillage Local</string>
    <key>CFBundleDisplayName</key><string>OpenVillage Local</string>
    <key>CFBundleIdentifier</key><string>local.openvillage.launcher</string>
    <key>CFBundleExecutable</key><string>OpenVillageLocal</string>
    <key>CFBundlePackageType</key><string>APPL</string>
    <key>CFBundleShortVersionString</key><string>1.0</string>
    <key>CFBundleVersion</key><string>1</string>
    <key>CFBundleDevelopmentRegion</key><string>cs</string>
    <key>LSMinimumSystemVersion</key><string>14.0</string>
    <key>LSApplicationCategoryType</key><string>public.app-category.developer-tools</string>
    <key>NSHighResolutionCapable</key><true/>
    <key>NSPrincipalClass</key><string>NSApplication</string>
    <key>NSAppTransportSecurity</key>
    <dict>
        <key>NSAllowsLocalNetworking</key><true/>
    </dict>
</dict>
</plist>
PLIST
codesign --force --sign - "$APP" >/dev/null 2>&1 || true   # ad-hoc signature so the app runs locally
echo "Hotovo: $(cd ../build && pwd)/OpenVillage Local.app"
