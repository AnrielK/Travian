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
   requirements → database account → world settings (speed, map size, round length, hero speed, graphics,
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

To install again (a new world), delete `xampp/config.local.php` and restart the launcher; the wizard asks
before overwriting existing game databases.

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
