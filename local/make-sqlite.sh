#!/bin/bash
# Builds ios/app/Resources/travian.sqlite (the world bundled in the iOS app):
# fresh world in MariaDB (local/setup-db.sh, game server running in mysql mode) -> SQLite.
#   local/make-sqlite.sh "<mysql root cmd>" <URL of the game server>
set -euo pipefail
cd "$(dirname "$0")/.."
local/setup-db.sh "${1:-mysql -uroot}" "${2:-http://127.0.0.1:8096}"
OUT=ios/app/Resources/travian.sqlite; rm -f "$OUT"
python3 ios/tools/mysql_to_sqlite.py "${TRAVIAN_DB_NAME:-travian46}" "$OUT" >/dev/null
# links/redirects must stay on the local server whatever port the app gets
sqlite3 "$OUT" "UPDATE config SET HOMEPAGE = ''; VACUUM;"
ls -la "$OUT"
