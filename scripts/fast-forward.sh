#!/usr/bin/env bash
# Finish all running builds, research, training and movements now (server started by start.sh must be running).
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
GAME="$ROOT/game"; [[ -f "$GAME/travian.sql" ]] || GAME="$ROOT"
if [[ "$(uname)" == "Darwin" ]]; then DATA="${TRAVIAN_DATA:-$HOME/Library/Application Support/TravianLocal}"; else DATA="${TRAVIAN_DATA:-$HOME/.local/share/TravianLocal}"; fi
CLIENT="$(command -v mariadb || command -v mysql || ls /opt/homebrew/bin/mariadb /usr/local/bin/mariadb 2>/dev/null | head -1)"
"$CLIENT" --no-defaults --socket="$DATA/mariadb.sock" -uroot travian3 < "$GAME/local/fast_forward.sql"
touch -t "$(date -v-1M +%Y%m%d%H%M.%S 2>/dev/null || date -d '-1 min' +%Y%m%d%H%M.%S)" "$GAME/application/queue.txt"
echo "Hotovo – obnovte stránku ve hře."
