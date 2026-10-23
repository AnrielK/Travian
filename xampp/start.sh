#!/usr/bin/env bash
# OpenVillage for XAMPP on Linux (and macOS): starts the game with XAMPP's PHP, see xampp/README.md.
#   xampp/start.sh [--port=8080] [--no-browser]
# PHP 8.2+ is taken from $XAMPP_PHP, $XAMPP_HOME/bin/php, /opt/lampp/bin/php, the XAMPP app on macOS or the PATH
# (the first one new enough).
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PHP="${XAMPP_PHP:-}"
for candidate in "${XAMPP_HOME:-/opt/lampp}/bin/php" /opt/lampp/bin/php \
                 /Applications/XAMPP/xamppfiles/bin/php "$(command -v php || true)"; do
    if [ -z "$PHP" ] && [ -n "$candidate" ] && [ -x "$candidate" ] \
       && "$candidate" -n -r "exit(version_compare(PHP_VERSION, '8.2.0', '<') ? 1 : 0);" 2>/dev/null; then
        PHP="$candidate"
    fi
done
if [ -z "$PHP" ]; then
    echo "CHYBA: PHP 8.2 nebo novější nebylo nalezeno. Nainstaluj XAMPP do /opt/lampp nebo nastav XAMPP_PHP=/cesta/k/php." >&2
    exit 1
fi
exec "$PHP" "$DIR/launcher.php" "$@"
