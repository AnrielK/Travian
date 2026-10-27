#!/usr/bin/env bash
# Creates a fresh Travian T4.6 world in MariaDB/MySQL with the installer's own SQL and PHP code
# (tables, config, map, Multihunter + Natars, oases) – without rewriting application/config.php.
#   local/setup-db.sh "<mysql root client cmd>" <URL of the running game server, mysql mode>
#   e.g. local/setup-db.sh "mysql -uroot" http://127.0.0.1:8096
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
MYSQL=${1:-"mysql -uroot"}; URL=${2:-http://127.0.0.1:8096}; DB=${TRAVIAN_DB_NAME:-travian46}
SPEED=${TRAVIAN_SPEED:-100}
$MYSQL -e "DROP DATABASE IF EXISTS $DB; CREATE DATABASE $DB CHARACTER SET utf8;
  CREATE USER IF NOT EXISTS 'travian'@'localhost' IDENTIFIED BY 'travian'; CREATE USER IF NOT EXISTS 'travian'@'127.0.0.1' IDENTIFIED BY 'travian';
  GRANT ALL ON $DB.* TO 'travian'@'localhost'; GRANT ALL ON $DB.* TO 'travian'@'127.0.0.1';"
cat "$ROOT/setup/data/sql.sql" "$ROOT/setup/data/missing_tables.sql" | $MYSQL $DB
NOW=$(date +%s)
# same row as setup/process.php (constForm) with the installer's default form values
$MYSQL $DB -e "SET SESSION sql_mode=''; INSERT INTO config VALUES(NULL,$NOW,$NOW,0,1,'','Travian T4.6 Local',999999,10800,'gpack/img_rtl/',
  $((NOW-60)),1,$SPEED,0,$((NOW+345600)),$((NOW+518400)),1,10,$(( (SPEED+40)/80 )),1,100,43200,1,604800,604800,'$URL/','admin@test.com',
  10,100,5,1,25,400,80,50,1000,2500,4900,75,150,250,180000000,560000000,1800000000,'en',0,0,0)"
rm -f "$ROOT/setup/install.lock"
curl -sf -o /dev/null --max-time 900 -X POST "$URL/setup/include/wdata.php"
curl -sf -o /dev/null --max-time 900 --data "username=Multihunter&password=12345" "$URL/setup/include/multihunter.php"
curl -sf -o /dev/null --max-time 900 -X POST "$URL/setup/include/oasis.php"
rm -rf "$ROOT"/mcache/*.jpg "$ROOT"/mcache/*.png "$ROOT"/hcache/body/* 2>/dev/null || true
$MYSQL $DB -N -e "SELECT CONCAT('tiles ', COUNT(*)) FROM wdata; SELECT CONCAT('oases ', COUNT(*)) FROM odata; SELECT CONCAT('villages ', COUNT(*)) FROM vdata;"
