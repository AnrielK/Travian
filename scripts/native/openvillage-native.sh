#!/usr/bin/env bash
# OpenVillage – native local runtime (no Docker).
#
# Runs the same stack as compose.yaml directly on the host:
#   MariaDB (private datadir, unix socket only)  ·  Redis (private port)
#   PHP built-in web server + scripts/native/router.php (replaces Apache)
#   Automation worker (main_script/copyable/include/Automation.php)
#
# Used by the OpenVillage Local macOS app, and usable on its own:
#   scripts/native/openvillage-native.sh doctor|start|stop|restart|status|bootstrap|test <name|all>|tests
#                                        sql [--global] "<query>"|backup [dir]|restore <file> --yes|flush-cache|shell|env|paths|reset --yes
#
# macOS prerequisites (Homebrew):  brew install php@8.3 mariadb redis ripgrep autoconf pkg-config && $(brew --prefix php@8.3)/bin/pecl install redis
# Settings come from environment variables (see "Configuration" below); the macOS app sets them.
# Compatible with the bash 3.2 that ships with macOS.

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

# ---------------------------------------------------------------- Configuration
if [ "$(uname -s)" = "Darwin" ]; then
    DEFAULT_DATA="$HOME/Library/Application Support/OpenVillageLocal/native"
else
    DEFAULT_DATA="${XDG_DATA_HOME:-$HOME/.local/share}/openvillage-local/native"
fi
DATA="${OV_DATA_DIR:-$DEFAULT_DATA}"
PORT="${OV_PORT:-8080}"
REDIS_PORT="${OV_REDIS_PORT:-16379}"
DB_PORT="${OV_DB_PORT:-0}"
LCTN="${OV_LOWER_CASE_TABLE_NAMES:-}"     # e.g. 1 = like the iOS app (set before the first start)               # 0 = unix socket only; set e.g. 33306 to reach the DB from TablePlus etc.
WEB_WORKERS="${OV_WEB_WORKERS:-6}"       # parallel PHP built-in server workers
# OV_IOS_EMULATION=1 runs exactly like the iOS app: no Redis server (redis-polyfill.php), no forked worker
# (automation ticks through /__openvillage/tick), world created by /__openvillage/setup, only the PHP
# extensions compiled into the iOS build, process functions disabled.
IOS_EMU="${OV_IOS_EMULATION:-0}"
PHP_ARGS=()
GAME_SPEED="${GAME_SPEED:-10}"
GAME_MAP_SIZE="${GAME_MAP_SIZE:-25}"
GAME_ROUND_LENGTH_DAYS="${GAME_ROUND_LENGTH_DAYS:-35}"
APP_TIMEZONE="${APP_TIMEZONE:-UTC}"
GAME_DB_NAME="${GAME_DB_NAME:-openvillage_game}"
GLOBAL_DB_NAME="${GLOBAL_DB_NAME:-openvillage_global}"
GAME_DB_USER="${GAME_DB_USER:-openvillage}"

# Homebrew locations (Apple Silicon + Intel), preferring the PHP 8.3 keg the project targets.
for p in /opt/homebrew/opt/php@8.3/bin /usr/local/opt/php@8.3/bin /opt/homebrew/bin /opt/homebrew/sbin /usr/local/bin /usr/local/sbin; do
    case ":$PATH:" in *":$p:"*) ;; *) [ -d "$p" ] && PATH="$p:$PATH" ;; esac
done
export PATH

PHP="${OV_PHP:-$(command -v php || true)}"

RUN="$DATA/run"; LOGS="$DATA/logs"; DBDIR="$DATA/mariadb"; REDISDIR="$DATA/redis"
# unix sockets are limited to ~104 bytes on macOS – fall back to a short path when needed
SOCK="$RUN/mariadb.sock"
if [ "${#SOCK}" -gt 100 ]; then SOCK="${TMPDIR:-/tmp}/ov-$(id -u)-mariadb.sock"; fi
RUNTIME_DIR="$ROOT/main_script/copyable/include/runtime"

say()  { printf '%s\n' "$*"; }
step() { printf '==> %s\n' "$*"; }
die()  { printf 'ERROR: %s\n' "$*" >&2; exit 1; }

find_bin() { # first existing command from the list
    for c in "$@"; do command -v "$c" >/dev/null 2>&1 && { command -v "$c"; return 0; }; done
    for c in "$@"; do for d in /opt/homebrew/opt/mariadb/bin /usr/local/opt/mariadb/bin /usr/sbin /usr/local/mysql/bin; do
        [ -x "$d/$c" ] && { echo "$d/$c"; return 0; }; done; done
    return 1
}
MARIADBD="$(find_bin mariadbd mysqld || true)"
MARIADB="$(find_bin mariadb mysql || true)"
MARIADB_ADMIN="$(find_bin mariadb-admin mysqladmin || true)"
MARIADB_INSTALL="$(find_bin mariadb-install-db mysql_install_db || true)"
REDIS_SERVER="$(find_bin redis-server || true)"
REDIS_CLI="$(find_bin redis-cli || true)"

secrets_file="$DATA/secrets.env"
load_secrets() {
    mkdir -p "$DATA" "$RUN" "$LOGS"
    if [ ! -f "$secrets_file" ]; then
        rnd() { LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom | head -c "$1"; }
        umask 077
        {
            echo "GAME_DB_PASSWORD=$(rnd 32)"
            echo "APP_SECRET=$(rnd 48)"
            echo "INITIAL_ADMIN_PASSWORD=${OV_ADMIN_PASSWORD:-$(rnd 16)}"
            echo "OV_CONTROL_TOKEN=$(rnd 32)"
        } >"$secrets_file"
        umask 022
    fi
    # shellcheck disable=SC1090
    . "$secrets_file"
    [ -n "${OV_CONTROL_TOKEN:-}" ] || { OV_CONTROL_TOKEN="$(LC_ALL=C tr -dc 'A-Za-z0-9' </dev/urandom 2>/dev/null | head -c 32 || true)"; echo "OV_CONTROL_TOKEN=$OV_CONTROL_TOKEN" >>"$secrets_file"; }
    export GAME_DB_PASSWORD APP_SECRET INITIAL_ADMIN_PASSWORD OV_CONTROL_TOKEN
}

app_env() {
    export APP_URL="http://127.0.0.1:$PORT"
    export GAME_WORLD_URL="http://127.0.0.1:$PORT/game/"
    export APP_TIMEZONE GAME_SPEED GAME_MAP_SIZE GAME_ROUND_LENGTH_DAYS
    export GAME_DB_HOST=localhost GLOBAL_DB_HOST=localhost       # "localhost" = unix socket (see write_php_ini)
    export GAME_DB_NAME GAME_DB_USER GLOBAL_DB_NAME
    export GLOBAL_DB_USER="$GAME_DB_USER" GLOBAL_DB_PASSWORD="$GAME_DB_PASSWORD"
    export REDIS_HOST=127.0.0.1 REDIS_PORT="$REDIS_PORT"
    export GAME_WORLD_ID=local GAME_TITLE="${GAME_TITLE:-OpenVillage 4.6}" GAME_SERVER_NAME="${GAME_SERVER_NAME:-Local World}"
    export MYSQL_UNIX_PORT="$SOCK"                                 # mysqldump/mariadb clients used by the worker backups
    export OV_NATIVE=1
    write_php_ini
}

# Runtime php.ini (mirror of docker/php/php.ini + socket paths). Delivered through PHP_INI_SCAN_DIR (":dir" =
# appended to the default scan dir), so PHP child processes spawned by the worker and the tests inherit it too.
write_php_ini() {
    mkdir -p "$RUN/php" "$RUN/sessions"
    cat >"$RUN/php/zz-openvillage.ini" <<INI
mysqli.default_socket = "$SOCK"
pdo_mysql.default_socket = "$SOCK"
display_errors = Off
log_errors = On
error_reporting = E_ALL
error_log = "$LOGS/php-error.log"
memory_limit = 512M
max_execution_time = 120
post_max_size = 8M
upload_max_filesize = 8M
session.use_strict_mode = 1
session.cookie_httponly = 1
session.cookie_samesite = Lax
session.save_path = "$RUN/sessions"
expose_php = Off
date.timezone = "$APP_TIMEZONE"
INI
    export PHP_INI_SCAN_DIR=":$RUN/php"
    if [ "$IOS_EMU" = 1 ]; then
        mkdir -p "$RUN/php-ios"
        {
            for ext in mysqlnd mysqli pdo pdo_mysql mbstring iconv ctype gd; do
                "$PHP" -n -m 2>/dev/null | grep -qi "^$ext\$" || echo "extension=$ext"
            done
            sed 's/^/ /;s/^ //' "$RUN/php/zz-openvillage.ini"
            echo "auto_prepend_file = \"$ROOT/scripts/native/runtime/redis-polyfill.php\""
            echo "disable_functions = pcntl_fork,pcntl_exec,exec,shell_exec,proc_open,popen,system,passthru"
        } >"$RUN/php-ios/php.ini"
        export PHP_INI_SCAN_DIR=""            # empty = no extra ini directories (like php -n on iOS)
        PHP_ARGS=(-c "$RUN/php-ios/php.ini")
        export OV_KV_DB=openvillage_cache OV_TICK_SKIP="mayExitJobs:backup"
    fi
}
php_run() { "$PHP" ${PHP_ARGS[@]+"${PHP_ARGS[@]}"} "$@"; }

pid_alive() { [ -f "$RUN/$1.pid" ] && kill -0 "$(cat "$RUN/$1.pid")" 2>/dev/null; }
db_root() { "$MARIADB" --no-defaults --protocol=socket --socket="$SOCK" -uroot "$@"; }
db_app()  { "$MARIADB" --no-defaults --protocol=socket --socket="$SOCK" -u"$GAME_DB_USER" -p"$GAME_DB_PASSWORD" "$@"; }

# ---------------------------------------------------------------- doctor
cmd_doctor() {
    local bad=0
    check() { if [ -n "$2" ]; then say "OK      $1  ($2)"; else say "MISSING $1  -> $3"; bad=1; fi; }
    check php "$PHP" "brew install php@8.3"
    if [ -n "$PHP" ]; then
        local v; v="$("$PHP" -r 'echo PHP_VERSION;')"
        if "$PHP" -r 'exit(version_compare(PHP_VERSION,"8.3.0",">=")?0:1);'; then say "OK      php version $v"; else say "MISSING php >= 8.3 (found $v) -> brew install php@8.3"; bad=1; fi
        for ext in mysqli pdo_mysql redis gd zip bcmath pcntl mbstring; do
            if "$PHP" -m | grep -qi "^$ext\$"; then say "OK      php ext $ext"
            else
                case $ext in redis) hint="pecl install redis";; *) hint="rebuild/reinstall php with $ext";; esac
                say "MISSING php ext $ext -> $hint"; bad=1
            fi
        done
    fi
    check mariadbd "$MARIADBD" "brew install mariadb"
    check mariadb-client "$MARIADB" "brew install mariadb"
    check mariadb-install-db "$MARIADB_INSTALL" "brew install mariadb"
    check redis-server "$REDIS_SERVER" "brew install redis"
    check ripgrep "$(command -v rg || true)" "brew install ripgrep   (only for the static check scripts)"
    check node "$(command -v node || true)" "brew install node   (only for the JavaScript syntax check)"
    say "DATA    $DATA"
    return $bad
}

# ---------------------------------------------------------------- services
start_mariadb() {
    if pid_alive mariadb; then return 0; fi
    [ -n "$MARIADBD" ] && [ -n "$MARIADB_INSTALL" ] || die "MariaDB not found (brew install mariadb)"
    local user_args=(); [ "$(id -u)" = 0 ] && user_args=(--user=root)
    if [ ! -d "$DBDIR/mysql" ]; then
        step "Initialising MariaDB data directory"
        mkdir -p "$DBDIR"
        local real basedir
        real="$(realpath "$MARIADBD" 2>/dev/null || perl -MCwd -e 'print Cwd::abs_path(shift)' "$MARIADBD")"
        basedir="$(cd "$(dirname "$real")/.." && pwd)"   # e.g. /opt/homebrew/Cellar/mariadb/<ver>
        "$MARIADB_INSTALL" --no-defaults --basedir="$basedir" --datadir="$DBDIR" \
            --auth-root-authentication-method=normal --skip-test-db ${LCTN:+--lower-case-table-names=$LCTN} \
            ${user_args[@]+"${user_args[@]}"} >"$LOGS/mariadb-install.log" 2>&1 \
            || { cat "$LOGS/mariadb-install.log" >&2; die "mariadb-install-db failed"; }
    fi
    local net=(--skip-networking)
    [ "$DB_PORT" != 0 ] && net=(--port="$DB_PORT" --bind-address=127.0.0.1)
    step "Starting MariaDB"
    nohup "$MARIADBD" --no-defaults --datadir="$DBDIR" --socket="$SOCK" --pid-file="$RUN/mariadbd.pid" \
        --log-error="$LOGS/mariadb.log" "${net[@]}" ${user_args[@]+"${user_args[@]}"} ${LCTN:+--lower-case-table-names=$LCTN} \
        --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci \
        --innodb-buffer-pool-size=256M --max-connections=300 >/dev/null 2>&1 &
    echo $! >"$RUN/mariadb.pid"
    local i; for i in $(seq 1 60); do
        db_root -e 'SELECT 1' >/dev/null 2>&1 && return 0
        kill -0 "$(cat "$RUN/mariadb.pid")" 2>/dev/null || { tail -20 "$LOGS/mariadb.log" >&2; die "MariaDB exited"; }
        sleep 0.5
    done
    die "MariaDB did not become ready (see $LOGS/mariadb.log)"
}

start_redis() {
    if pid_alive redis; then return 0; fi
    [ -n "$REDIS_SERVER" ] || die "redis-server not found (brew install redis)"
    mkdir -p "$REDISDIR"
    step "Starting Redis on 127.0.0.1:$REDIS_PORT"
    nohup "$REDIS_SERVER" --port "$REDIS_PORT" --bind 127.0.0.1 --dir "$REDISDIR" --save 60 1 --appendonly yes \
        --logfile "$LOGS/redis.log" --daemonize no >/dev/null 2>&1 &
    echo $! >"$RUN/redis.pid"
    local i; for i in $(seq 1 40); do
        if [ -n "$REDIS_CLI" ]; then "$REDIS_CLI" -p "$REDIS_PORT" ping >/dev/null 2>&1 && return 0
        else "$PHP" -r '$r=new Redis(); exit(@$r->connect("127.0.0.1",(int)$argv[1],0.3)?0:1);' "$REDIS_PORT" 2>/dev/null && return 0; fi
        kill -0 "$(cat "$RUN/redis.pid")" 2>/dev/null || { tail -20 "$LOGS/redis.log" >&2; die "Redis exited (port $REDIS_PORT busy?)"; }
        sleep 0.25
    done
    die "Redis did not become ready"
}

# Databases of further worlds (<game db>_<id>, scripts/native/runtime/worlds.php); also for older data directories.
grant_world_databases() {
    local pattern="${GAME_DB_NAME//_/\\_}\\_%"
    db_root -e "GRANT ALL PRIVILEGES ON \`$pattern\`.* TO '$GAME_DB_USER'@'localhost'; GRANT ALL PRIVILEGES ON \`$pattern\`.* TO '$GAME_DB_USER'@'127.0.0.1'; FLUSH PRIVILEGES;"
}

# Same work as docker/mariadb/init.sh – only once per data directory.
init_databases() {
    [ -f "$DATA/.db-initialised" ] && return 0
    step "Creating databases and importing schema"
    db_root <<SQL
CREATE DATABASE IF NOT EXISTS \`$GLOBAL_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS \`$GAME_DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$GAME_DB_USER'@'localhost' IDENTIFIED BY '$GAME_DB_PASSWORD';
CREATE USER IF NOT EXISTS '$GAME_DB_USER'@'127.0.0.1' IDENTIFIED BY '$GAME_DB_PASSWORD';
GRANT ALL PRIVILEGES ON \`$GLOBAL_DB_NAME\`.* TO '$GAME_DB_USER'@'localhost';
GRANT ALL PRIVILEGES ON \`$GAME_DB_NAME\`.* TO '$GAME_DB_USER'@'localhost';
GRANT ALL PRIVILEGES ON \`$GLOBAL_DB_NAME\`.* TO '$GAME_DB_USER'@'127.0.0.1';
GRANT ALL PRIVILEGES ON \`$GAME_DB_NAME\`.* TO '$GAME_DB_USER'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
    db_root -e "CREATE DATABASE IF NOT EXISTS openvillage_cache; GRANT ALL PRIVILEGES ON openvillage_cache.* TO '$GAME_DB_USER'@'localhost'; GRANT ALL PRIVILEGES ON openvillage_cache.* TO '$GAME_DB_USER'@'127.0.0.1';"
    db_root "$GLOBAL_DB_NAME" <"$ROOT/data/main.sql"
    db_root "$GAME_DB_NAME" <"$ROOT/main_script/include/schema/T4.4.sql"
    if [ "$IOS_EMU" = 1 ]; then touch "$DATA/.db-initialised"; return 0; fi   # setup endpoint does the rest
    db_root "$GAME_DB_NAME" -e "INSERT INTO config (startTime, map_size, worldUniqueId, installed, loginInfoTitle, loginInfoHTML, message) VALUES (UNIX_TIMESTAMP(), $GAME_MAP_SIZE, 1, 0, '', '', '');"
    db_root "$GLOBAL_DB_NAME" -e "UPDATE gameServers SET speed=$GAME_SPEED, startTime=UNIX_TIMESTAMP() WHERE id=1;"
    touch "$DATA/.db-initialised"
}

cmd_bootstrap() { # = compose "bootstrap" service: migrations + one-time world install
    step "Bootstrap (migrations + world install)"
    mkdir -p "$RUNTIME_DIR/backups" "$RUN/sessions"
    php_run "$ROOT/main_script/copyable/include/install.php" local "$INITIAL_ADMIN_PASSWORD" >"$LOGS/bootstrap.log" 2>&1 \
        || { tail -40 "$LOGS/bootstrap.log" >&2; die "bootstrap failed (see $LOGS/bootstrap.log)"; }
    tail -1 "$LOGS/bootstrap.log"; echo
}

start_web() {
    if pid_alive web; then return 0; fi
    if (exec 3<>"/dev/tcp/127.0.0.1/$PORT") 2>/dev/null; then die "Port $PORT is already in use"; fi
    step "Starting web server on http://127.0.0.1:$PORT/"
    local workers="$WEB_WORKERS"; [ "$IOS_EMU" = 1 ] && workers=1      # iOS: one in-process server thread
    PHP_CLI_SERVER_WORKERS="$workers" nohup "$PHP" ${PHP_ARGS[@]+"${PHP_ARGS[@]}"} -S "127.0.0.1:$PORT" -t "$ROOT/web/public" \
        "$ROOT/scripts/native/router.php" >>"$LOGS/web.log" 2>&1 &
    echo $! >"$RUN/web.pid"
}

control() { # control <action> [curl args…]
    local action=$1; shift
    curl -sS --max-time 600 -H "X-OV-Token: $OV_CONTROL_TOKEN" ${1+"$@"} "http://127.0.0.1:$PORT/__openvillage/$action" 2>&1
}

start_ticker() { # start_ticker <pid name> <first world>: calls /__openvillage/tick?world=N every second
    # iOS emulation: every world (the app ticks the same way); otherwise the further worlds of the lobby's
    # "Game worlds" page (scripts/native/runtime/worlds.php), next to the forked worker of world 1.
    local name=$1 first=$2
    if pid_alive "$name"; then return 0; fi
    step "Starting automation ticker (1 s, worlds from $first)"
    OV_BASE="http://127.0.0.1:$PORT/__openvillage" OV_FIRST="$first" nohup bash -c '
        trap "exit 0" TERM INT
        worlds=1; checked=0
        while true; do
            if [ $(( $(date +%s) - checked )) -ge 10 ]; then
                worlds="$(curl -s --max-time 10 -H "X-OV-Token: $OV_CONTROL_TOKEN" "$OV_BASE/worlds" | sed -n "s/.*\"worlds\":\[\([0-9,]*\)\].*/\1/p" | tr , " ")"
                checked=$(date +%s)
            fi
            for w in ${worlds:-1}; do
                [ "$w" -ge "$OV_FIRST" ] || continue
                out="$(curl -s --max-time 300 -H "X-OV-Token: $OV_CONTROL_TOKEN" "$OV_BASE/tick?world=$w")"
                case "$out" in *"\"errors\":[]"*|*"\"busy\":true"*|"") ;; *) echo "$(date "+%F %T") [world $w] $out" ;; esac
            done
            sleep 1
        done' >>"$LOGS/$name.log" 2>&1 &
    echo $! >"$RUN/$name.pid"
}

start_worker() {
    if [ "$IOS_EMU" = 1 ]; then start_ticker worker 1; return; fi
    start_ticker worlds 2
    if pid_alive worker; then return 0; fi
    step "Starting automation worker"
    # Supervised like the compose service (restart: unless-stopped): if the engine exits, start it again.
    OV_PHP_BIN="$PHP" OV_ENGINE="$ROOT/main_script/copyable/include/Automation.php" nohup bash -c '
        child=""
        trap '"'"'[ -n "$child" ] && kill -TERM "$child" 2>/dev/null; wait "$child" 2>/dev/null; exit 0'"'"' TERM INT
        while true; do
            "$OV_PHP_BIN" "$OV_ENGINE" & child=$!
            wait "$child"; code=$?
            echo "[supervisor] $(date "+%F %T") automation worker exited (code $code) - restarting in 2 s"
            sleep 2
        done' >>"$LOGS/worker.log" 2>&1 &
    echo $! >"$RUN/worker.pid"
}

wait_health() {
    local i body; for i in $(seq 1 60); do
        body="$(curl -s --max-time 2 "http://127.0.0.1:$PORT/health.php" || true)"
        case "$body" in *'"status":"ready"'*) say "READY   http://127.0.0.1:$PORT/"; return 0;; esac
        pid_alive web || { tail -20 "$LOGS/web.log" >&2; die "web server exited"; }
        sleep 0.5
    done
    say "$body"; die "health check did not report ready"
}

stop_one() { # name signal timeout
    local name=$1 sig=${2:-TERM} t=${3:-15}
    pid_alive "$name" || { rm -f "$RUN/$name.pid"; return 0; }
    local pid; pid="$(cat "$RUN/$name.pid")"
    step "Stopping $name ($pid)"
    kill "-$sig" "$pid" 2>/dev/null || true
    local i; for i in $(seq 1 $((t * 4))); do kill -0 "$pid" 2>/dev/null || break; sleep 0.25; done
    kill -0 "$pid" 2>/dev/null && kill -9 "$pid" 2>/dev/null || true
    rm -f "$RUN/$name.pid"
}

cmd_start() {
    load_secrets; app_env
    [ -n "$PHP" ] || die "php not found (brew install php@8.3)"
    php_run -r 'exit(ini_get("mysqli.default_socket")===getenv("MYSQL_UNIX_PORT")?0:1);' \
        || die "PHP ignores PHP_INI_SCAN_DIR – cannot apply the runtime php.ini"
    start_mariadb
    grant_world_databases
    if [ "$IOS_EMU" = 1 ]; then
        init_databases
        start_web
        step "World setup through /__openvillage/setup"
        local i; for i in $(seq 1 40); do curl -s -o /dev/null --max-time 1 "http://127.0.0.1:$PORT/" && break; sleep 0.25; done
        control setup -X POST | tee "$LOGS/bootstrap.log"; echo
        grep -q '"ok":true' "$LOGS/bootstrap.log" || die "setup failed"
    else
        start_redis
        init_databases
        cmd_bootstrap
        start_web
    fi
    start_worker
    wait_health
    say "ADMIN   Support / $INITIAL_ADMIN_PASSWORD  (secrets: $secrets_file)"
}

cmd_stop() {
    load_secrets
    stop_one worker TERM 20
    stop_one worlds TERM 20
    # the built-in server forks workers that share its process group
    if pid_alive web; then pkill -TERM -P "$(cat "$RUN/web.pid")" 2>/dev/null || true; fi
    stop_one web TERM 5
    if pid_alive redis && [ -n "$REDIS_CLI" ]; then "$REDIS_CLI" -p "$REDIS_PORT" shutdown >/dev/null 2>&1 || true; fi
    stop_one redis TERM 10
    if pid_alive mariadb && [ -n "$MARIADB_ADMIN" ]; then "$MARIADB_ADMIN" --no-defaults --socket="$SOCK" -uroot shutdown >/dev/null 2>&1 || true; fi
    stop_one mariadb TERM 30
}

cmd_status() {
    local s
    for s in mariadb redis web worker; do
        if pid_alive "$s"; then say "$s=running"; else say "$s=stopped"; fi
    done
    local body; body="$(curl -s --max-time 2 "http://127.0.0.1:$PORT/health.php" 2>/dev/null || true)"
    case "$body" in *'"status":"ready"'*) say "health=ready";; "") say "health=down";; *) say "health=not-ready";; esac
    say "url=http://127.0.0.1:$PORT/"
}

# ---------------------------------------------------------------- tests
# name|kind|target|description   (kind: php = tests/<file> run with the runtime env, sh = shell script)
test_catalog() {
    cat <<'T'
runtime|php|runtime-regression.php|Runtime regression (transactions, workers, concurrency)
formulas|php|formula-golden-regression.php|Formula golden values (ruleset)
complete-round|php|complete-round-regression.php|Complete round regression
account-export|php|account-export-regression.php|Account export isolation
smoke|sh|smoke-game.sh|End-to-end HTTP smoke flow (creates a disposable player)
php-lint|lint||PHP syntax of all source files
game-actions|sh|check-game-actions.sh|Static contract: rally point actions
account-actions|sh|check-account-actions.sh|Static contract: account actions
sitter-actions|sh|check-sitter-actions.sh|Static contract: sitter actions
javascript|sh|check-javascript.sh|JavaScript syntax (needs node)
T
}

cmd_tests() { test_catalog; }

run_test() {
    local name=$1 line kind target
    line="$(test_catalog | grep "^$name|" || true)"
    [ -n "$line" ] || die "unknown test '$name' (see: tests)"
    kind="$(echo "$line" | cut -d'|' -f2)"; target="$(echo "$line" | cut -d'|' -f3)"
    case $kind in
        php)
            ( cd "$ROOT" && php_run "$ROOT/tests/$target" ) ;;
        lint)
            local f n=0 failed=0
            while IFS= read -r -d '' f; do
                n=$((n + 1))
                out="$("$PHP" -l "$f" 2>&1)" || { echo "$out"; failed=1; }
            done < <(find "$ROOT/main_script" "$ROOT/web" "$ROOT/sections" -type f -name '*.php' -print0)
            [ $failed = 0 ] && echo "PHP syntax check passed ($n files)."; return $failed ;;
        sh)
            if [ "$target" = smoke-game.sh ]; then
                # smoke-game.sh calls "docker compose exec -T app php /app/tests/…" for its fixture – shim it natively
                local shim="$RUN/shim"; mkdir -p "$shim"
                cat >"$shim/docker" <<SHIM
#!/usr/bin/env bash
# native shim: docker compose exec -T app php /app/<path> args…
while [ \$# -gt 0 ] && [ "\$1" != php ]; do shift; done; shift
script="\$1"; shift
case "\$script" in /app/*) script="$ROOT/\${script#/app/}";; esac
exec "$ROOT/scripts/native/openvillage-native.sh" __php "\$script" "\$@"
SHIM
                chmod +x "$shim/docker"
                ( cd "$ROOT" && PATH="$shim:$PATH" APP_URL="http://127.0.0.1:$PORT" sh "$ROOT/scripts/$target" )
            else
                ( cd "$ROOT" && sh "$ROOT/scripts/$target" )
            fi ;;
    esac
}

cmd_test() {
    load_secrets; app_env
    local name=${1:-all} rc=0 n start
    if [ "$name" = all ]; then
        for n in $(test_catalog | cut -d'|' -f1); do
            start=$(date +%s)
            say "::test-start $n"
            if run_test "$n"; then say "::test-end $n pass $(( $(date +%s) - start ))s"
            else rc=1; say "::test-end $n fail $(( $(date +%s) - start ))s"; fi
        done
        return $rc
    fi
    run_test "$name"
}

# ---------------------------------------------------------------- misc
cmd_sql() {
    load_secrets
    local db="$GAME_DB_NAME"
    if [ "${1:-}" = "--global" ]; then db="$GLOBAL_DB_NAME"; shift; fi
    if [ $# -gt 0 ]; then db_app --batch --default-character-set=utf8mb4 "$db" -e "$*"
    else db_app --default-character-set=utf8mb4 "$db"; fi
}

cmd_env() {
    load_secrets; app_env
    for v in APP_URL GAME_WORLD_URL APP_SECRET APP_TIMEZONE GAME_DB_HOST GAME_DB_NAME GAME_DB_USER GAME_DB_PASSWORD \
             GLOBAL_DB_HOST GLOBAL_DB_NAME GLOBAL_DB_USER GLOBAL_DB_PASSWORD REDIS_HOST REDIS_PORT GAME_SPEED \
             GAME_MAP_SIZE GAME_ROUND_LENGTH_DAYS GAME_WORLD_ID GAME_TITLE GAME_SERVER_NAME MYSQL_UNIX_PORT INITIAL_ADMIN_PASSWORD; do
        eval "say \"$v=\${$v}\""
    done
}

cmd_paths() {
    say "root=$ROOT"; say "data=$DATA"; say "logs=$LOGS"; say "socket=$SOCK"; say "php=$PHP"
    say "mariadbd=$MARIADBD"; say "redis=$REDIS_SERVER"; say "runtime=$RUNTIME_DIR"
}

cmd_backup() { # backup [dir] – gzip dump of both databases
    load_secrets
    local dir="${1:-$DATA/backups}" dump file
    dump="$(find_bin mariadb-dump mysqldump)" || die "mariadb-dump not found"
    pid_alive mariadb || die "MariaDB is not running"
    mkdir -p "$dir"
    file="$dir/openvillage-native-$(date -u +%Y%m%dT%H%M%SZ).sql.gz"
    "$dump" --no-defaults --protocol=socket --socket="$SOCK" -uroot --single-transaction --routines \
        --default-character-set=utf8mb4 --databases "$GAME_DB_NAME" "$GLOBAL_DB_NAME" | gzip -c >"$file"
    gzip -t "$file"
    say "BACKUP  $file"
}

cmd_restore() { # restore <file.sql.gz> --yes
    load_secrets
    local file="${1:-}"
    [ -f "$file" ] || die "backup file not found: $file"
    [ "${2:-}" = "--yes" ] || die "restore replaces both databases. Re-run with: restore <file> --yes"
    pid_alive mariadb || die "MariaDB is not running"
    step "Restoring $file"
    gzip -dc "$file" | db_root
    if [ -n "$REDIS_CLI" ] && pid_alive redis; then "$REDIS_CLI" -p "$REDIS_PORT" flushall >/dev/null || true; fi
    say "RESTORED"
}

cmd_flush() {
    [ -n "$REDIS_CLI" ] || die "redis-cli not found"
    "$REDIS_CLI" -p "$REDIS_PORT" flushall
}

cmd_reset() {
    [ "${1:-}" = "--yes" ] || die "this erases the whole native world (database, redis, secrets). Re-run with: reset --yes"
    cmd_stop
    step "Erasing $DATA"
    rm -rf "$DATA"
    rm -rf "$RUNTIME_DIR"/*.log 2>/dev/null || true
}

case "${1:-}" in
    doctor) cmd_doctor ;;
    start) cmd_start ;;
    stop) cmd_stop ;;
    restart) cmd_stop; cmd_start ;;
    status) cmd_status ;;
    bootstrap) load_secrets; app_env; cmd_bootstrap ;;
    test) shift; cmd_test "${1:-all}" ;;
    tests) cmd_tests ;;
    sql) shift; cmd_sql "$@" ;;
    shell) shift; load_secrets; app_env; cd "$ROOT"; exec "${SHELL:-/bin/bash}" ;;
    env) cmd_env ;;
    paths) cmd_paths ;;
    reset) shift; cmd_reset "${1:-}" ;;
    backup) shift; cmd_backup "${1:-}" ;;
    restore) shift; cmd_restore "${1:-}" "${2:-}" ;;
    flush-cache) cmd_flush ;;
    __php) shift; load_secrets; app_env; php_run "$@" ;;
    *) sed -n '2,15p' "$0"; exit 64 ;;
esac
