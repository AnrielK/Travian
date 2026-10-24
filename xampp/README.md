# OpenVillage on XAMPP (Windows / Linux)

Runs the game with the PHP and MariaDB (MySQL) of an existing [XAMPP](https://www.apachefriends.org/)
installation, without Docker. A web wizard creates the database and the world on the first start.

## Requirements

- XAMPP with PHP 8.2 or newer (XAMPP 8.2.x), on Windows or Linux (`/opt/lampp`); the XAMPP app on macOS works too.
- PHP extensions `mysqli`, `pdo_mysql`, `mbstring`, `gd`, `iconv`, `ctype` – XAMPP enables them by default;
  the launcher switches on any that are commented out in `php.ini` when the extension file exists.
- MariaDB / MySQL of XAMPP on port 3306 with an administrator account (XAMPP default: `root`, no password).
- XAMPP's Apache is not needed (and may keep running): the game uses PHP's built-in web server.

## Start

1. Start **MySQL** in the XAMPP Control Panel (Linux: `sudo /opt/lampp/lampp startmysql`).
   On Windows the launcher starts XAMPP's MySQL itself when it is not running.
2. Start the game:
   - Windows: double-click `xampp\start.bat`
   - Linux / macOS: `./xampp/start.sh`
3. The browser opens <http://127.0.0.1:8080/>. On the first start it shows the installation wizard:
   requirements → database account → world settings (speed, map size, round length, hero speed, look,
   time zone, administrator password) → installation → done.
4. Keep the launcher window open while playing: it runs the game's automation (building, troops, …) once a
   second. Close it or press Ctrl+C to stop the game.

Options: `start.bat --port=8081` (another port; the next free one is used when 8080 is taken),
`--no-browser`. `start.bat` uses the first PHP 8.2+ it finds in `%XAMPP_PHP%`, `%XAMPP_HOME%\php`, the XAMPP
folder the game lies in (e.g. `C:\xampp8\htdocs\Travian`), `C:\xampp*`, `D:\xampp*`, `E:\xampp*` or the `PATH`;
an older PHP is skipped. `start.sh` looks in `$XAMPP_PHP`, `$XAMPP_HOME/bin`, `/opt/lampp/bin` or the `PATH`.

The administrator accounts **Multihunter** and **Support** use the password given in the wizard.

## What the wizard creates

- the database user `openvillage` (random password) with rights on `openvillage_%` only,
- the databases `openvillage_game`, `openvillage_global` and `openvillage_cache` (cache instead of Redis),
- `xampp/config.local.php` with the settings and generated secrets (not in git).

The look (graphic pack) is chosen once, when the world is created, and the world keeps it: it is stored in the
world's database, so changing `GAME_GPACK` in `config.local.php` later has no effect on it. The look also decides
the tribes players can choose: Travian 4.0 and T4.4 Classic offer Romans, Teutons and Gauls, the other looks
add Egyptians and Huns.

More worlds can run side by side, each with its own look, speed and map: the lobby's menu → *Game worlds*
(<http://127.0.0.1:8080/worlds.php>) creates a further world (database `openvillage_game_<id>`, pages at
`/game<id>/`) and deletes it again. The launcher runs the automation of every world. While a world is being
created (a minute or more for a big map), the game does not answer, because PHP's built-in server on Windows
handles one request at a time.

To install again from scratch, delete `xampp/config.local.php` and restart the launcher; the wizard asks before
overwriting existing game databases.

## Troubleshooting

**Broken MariaDB system tables** (`Incorrect file format 'tables_priv'`, `Got error 176 "Read page with wrong
checksum" from storage engine Aria`, …): the `mysql` system database of XAMPP is damaged or comes from another
version, typically a data folder kept from an older XAMPP. The wizard first repairs the named table; when that
does not help it runs the game with the administrator account from step 2 (`root`) instead of its own user, so
the game works anyway. To fix XAMPP's MariaDB itself (phpMyAdmin and other users need it too):

1. Stop MySQL in the XAMPP Control Panel and copy `xampp\mysql\data` somewhere as a backup.
2. Repair the system tables: open the XAMPP **Shell** and run
   `aria_chk -r mysql\data\mysql\*.MAI` (Linux: `/opt/lampp/bin/aria_chk -r /opt/lampp/var/mysql/mysql/*.MAI`),
   then start MySQL and run `mysql_upgrade -u root --force`.
3. If that fails, replace the system database with XAMPP's clean copy: stop MySQL, copy the folder
   `xampp\mysql\backup\mysql` over `xampp\mysql\data\mysql` and start MySQL again. This resets the MariaDB
   users to XAMPP's defaults (`root` without a password); the game's databases (`openvillage_*`) stay.

**Old PHP**: `start.bat` skips PHP older than 8.2; if it finds none, set `XAMPP_PHP` to the `php.exe` of an XAMPP
with PHP 8.2+.

## How it works

| File | Role |
| --- | --- |
| `start.bat`, `start.sh` | Find XAMPP's PHP and run `launcher.php` |
| `launcher.php` | Checks the PHP version (readable by old PHP too) and loads `launcher-run.php` |
| `launcher-run.php` | Checks the PHP extensions, starts MySQL (Windows), starts `php -S` with `router.php`, opens the browser and calls `/__openvillage/tick` every second |
| `router.php` | Loads `config.local.php` into the environment, serves the wizard until the world is installed, then hands requests to `scripts/native/router.php` |
| `installer/` | The installation wizard (`index.php`) and its back end (`api.php`) |
| `runtime/` | PHP sessions and logs: `logs/php-error.log`, `logs/server.log`, `logs/automation.log` (not in git) |

It is the runtime of the iOS app: Redis is replaced by a MariaDB table
(`scripts/native/runtime/redis-polyfill.php`) and the automation runs as a tick through the control endpoint
(`scripts/native/runtime/control.php`) instead of forked worker processes, which Windows does not have.
Database backups by the worker (`mysqldump`) are off.

The server listens on 127.0.0.1 only; the setup is meant for playing on one computer, not for public hosting.
