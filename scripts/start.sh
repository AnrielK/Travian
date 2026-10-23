#!/usr/bin/env bash
# TravianLocal – command-line launcher (same steps as the macOS app).
# Starts a private MariaDB instance + PHP built-in server and keeps them running until Ctrl+C.
#
#   scripts/start.sh            start (first run creates and imports the database)
#   scripts/start.sh --reset    wipe the game database and import it again, then start
#
# Env overrides: TRAVIAN_PORT (8080), TRAVIAN_DB_PORT (3307), TRAVIAN_DATA (data folder), TRAVIAN_DEBUG=1
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
GAME="$ROOT/game"; [[ -f "$GAME/travian.sql" ]] || GAME="$ROOT"   # repo layout: game files at the root
PORT="${TRAVIAN_PORT:-8080}"
DB_PORT="${TRAVIAN_DB_PORT:-3307}"
if [[ "$(uname)" == "Darwin" ]]; then
  DATA="${TRAVIAN_DATA:-$HOME/Library/Application Support/TravianLocal}"
else
  DATA="${TRAVIAN_DATA:-$HOME/.local/share/TravianLocal}"
fi
DATADIR="$DATA/mariadb"
SOCK="$DATA/mariadb.sock"
LOGS="$DATA/logs"
DB_NAME=travian3; DB_USER=travian; DB_PASS=travian
RESET=0; [[ "${1:-}" == "--reset" ]] && RESET=1

say() { printf '\033[1;32m▶\033[0m %s\n' "$*"; }
die() { printf '\033[1;31m✖\033[0m %s\n' "$*" >&2; exit 1; }

find_bin() { # first existing candidate from PATH or Homebrew locations
  local name
  for name in "$@"; do
    for dir in $(echo "$PATH" | tr ':' ' ') /opt/homebrew/bin /opt/homebrew/sbin /usr/local/bin /usr/local/sbin \
               /opt/homebrew/opt/mariadb/bin /usr/local/opt/mariadb/bin /usr/sbin /usr/bin; do
      [[ -x "$dir/$name" ]] && { echo "$dir/$name"; return 0; }
    done
  done
  return 1
}

PHP="$(find_bin php)" || die "PHP not found. Install: brew install php"
MARIADBD="$(find_bin mariadbd mysqld)" || die "MariaDB not found. Install: brew install mariadb"
INSTALL_DB="$(find_bin mariadb-install-db mysql_install_db)" || die "mariadb-install-db not found"
CLIENT="$(find_bin mariadb mysql)" || die "mariadb client not found"
"$MARIADBD" --version | grep -qi mariadb || die "$MARIADBD is MySQL, MariaDB is required (brew install mariadb)"
for ext in pdo_mysql mysqli gd mbstring; do
  "$PHP" -m | grep -qix "$ext" || die "PHP extension '$ext' is missing"
done

real() { python3 -c 'import os,sys;print(os.path.realpath(sys.argv[1]))' "$1" 2>/dev/null || readlink -f "$1"; }
BASEDIR="$(dirname "$(dirname "$(real "$MARIADBD")")")"
USER_OPT=(); [[ "$(id -u)" == "0" ]] && USER_OPT=(--user=root)

mkdir -p "$DATA" "$LOGS"
sql_root() { "$CLIENT" --no-defaults --socket="$SOCK" -uroot "$@"; }

# 1) MariaDB data directory
if [[ ! -d "$DATADIR/mysql" ]]; then
  say "Initializing MariaDB data directory ($DATADIR)"
  "$INSTALL_DB" --no-defaults --basedir="$BASEDIR" --datadir="$DATADIR" \
      --auth-root-authentication-method=normal --skip-test-db "${USER_OPT[@]}" > "$LOGS/mariadb-install.log" 2>&1 \
      || die "mariadb-install-db failed, see $LOGS/mariadb-install.log"
fi

# 2) Start MariaDB (private instance; does not touch any MySQL you already run)
cleanup() {
  say "Stopping…"
  [[ -n "${TAIL_PID:-}" ]] && kill "$TAIL_PID" 2>/dev/null || true
  [[ -n "${PHP_PID:-}" ]] && kill "$PHP_PID" 2>/dev/null || true
  [[ -n "${DB_PID:-}" ]] && kill "$DB_PID" 2>/dev/null || true
  wait 2>/dev/null || true
}
trap cleanup EXIT INT TERM

say "Starting MariaDB on 127.0.0.1:$DB_PORT"
"$MARIADBD" --no-defaults --basedir="$BASEDIR" --datadir="$DATADIR" --port="$DB_PORT" --bind-address=127.0.0.1 \
    --socket="$SOCK" --pid-file="$DATA/mariadb.pid" --log-error="$LOGS/mariadb.log" \
    --character-set-server=utf8 --collation-server=utf8_general_ci --sql-mode="" "${USER_OPT[@]}" &
DB_PID=$!
for i in $(seq 1 60); do sql_root -e "SELECT 1" >/dev/null 2>&1 && break; sleep 0.5
  kill -0 "$DB_PID" 2>/dev/null || die "MariaDB exited, see $LOGS/mariadb.log"; done
sql_root -e "SELECT 1" >/dev/null 2>&1 || die "MariaDB did not start, see $LOGS/mariadb.log"

# 3) Database + import
if [[ $RESET == 1 ]]; then say "Dropping database $DB_NAME"; sql_root -e "DROP DATABASE IF EXISTS $DB_NAME"; fi
if ! sql_root -e "USE $DB_NAME" >/dev/null 2>&1; then
  say "Creating database and importing travian.sql (takes ~10 s)"
  sql_root -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8;
    CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
    CREATE USER IF NOT EXISTS '$DB_USER'@'127.0.0.1' IDENTIFIED BY '$DB_PASS';
    GRANT ALL ON $DB_NAME.* TO '$DB_USER'@'localhost'; GRANT ALL ON $DB_NAME.* TO '$DB_USER'@'127.0.0.1';"
  sql_root "$DB_NAME" < "$GAME/travian.sql"
  rm -rf "$GAME/mcache/"*.jpg "$GAME/hcache/body/"*/ 2>/dev/null || true
fi
sql_root "$DB_NAME" < "$GAME/local/migrations.sql"
sql_root "$DB_NAME" -e "UPDATE config SET HOMEPAGE='http://127.0.0.1:$PORT/' WHERE id=1"

# 4) Folders the game expects
mkdir -p "$GAME/mcache" "$GAME/hcache/body" "$GAME/local/logs"
touch "$GAME/application/queue.txt"   # lock file of the event processor; must exist

# 5) PHP server
say "Starting PHP $("$PHP" -r 'echo PHP_VERSION;') on http://127.0.0.1:$PORT"
export TRAVIAN_DB_HOST=127.0.0.1 TRAVIAN_DB_PORT="$DB_PORT" TRAVIAN_DB_USER="$DB_USER" TRAVIAN_DB_PASS="$DB_PASS" TRAVIAN_DB_NAME="$DB_NAME"
cd "$GAME"
"$PHP" -d output_buffering=4096 -d short_open_tag=On -d display_errors=0 -d log_errors=1 \
  -d error_log="$GAME/local/logs/php_errors.log" -d error_reporting=E_ALL -d memory_limit=512M \
  -d date.timezone="${TZ:-Europe/Prague}" -d session.save_path="$DATA" \
  -d sendmail_path="tee -a '$LOGS/mail.log' >/dev/null" \
  -S "127.0.0.1:$PORT" -t "$GAME" >> "$LOGS/php-server.log" 2>&1 &
PHP_PID=$!
tail -n 0 -f "$LOGS/php-server.log" | grep --line-buffered -v -E "Accepted$|Closing$" &
TAIL_PID=$!
sleep 1
say "Game:   http://127.0.0.1:$PORT/login.php"
say "Admin:  http://127.0.0.1:$PORT/panel/  (Multihunter / admin123)"
say "Logs:   $LOGS  and  $GAME/local/logs (sql_errors.log, php_errors.log)"
say "Ctrl+C to stop."
if [[ "$(uname)" == "Darwin" ]]; then open "http://127.0.0.1:$PORT/login.php"; fi
wait "$PHP_PID"
