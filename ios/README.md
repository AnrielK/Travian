# Travian T4.6 – iOS aplikace (offline, PHP 8 v telefonu)

Větev `travian-T4.6`: hra Travian T4.6 (převedená na PHP 8, viz `PHP8-MIGRACE.md`) běží přímo
v iPhonu/iPadu – stejně jako TravianT4.4. Aplikace v sobě má PHP 8.3 (`php -S`), hra se zobrazuje
ve WKWebView a data jsou v SQLite. Žádný server ani připojení k internetu nejsou potřeba.

## Aplikace „Travian T4.6“

- Při prvním spuštění se připraví hotový svět (mapa 201×201, oázy, Natarové, Multihunter).
- Registrace: Menu → Registrace (víc účtů z jednoho zařízení je povoleno).
- Admin panel: Menu → Admin panel – **Multihunter / 12345**.
- Nástroje: dokončit vše hned (stavby, výzkum, trénink, pohyby), naplnit sklady, +1000 zlata,
  zrušit ochranu, rychlost serveru, seznam hráčů, obnovit databázi, druhý hráč (oddělené cookies), logy.
- Data: Soubory → Na iPhonu → Travian T4.6 (`www/`, `data/travian.sqlite`, `logs/`).

## Sestavení (GitHub Actions)

`.github/workflows/ios-php.yml` (macOS 15) při každém pushi do `travian-T4.6`:
1. sestaví PHP 8.3 pro iOS jako `PHPCore.xcframework` (`ios/php/build-php-ios.sh`, cache),
2. sestaví aplikaci pro simulátor, spustí ji a projede `ios/tests/smoke.py` – registrace dvou hráčů,
   stavba, trénink, zpráva, aliance, trh, útok + bitva, stovky stránek hráče a admin panelu,
3. vytvoří nepodepsanou **TravianT46Local-unsigned.ipa** (artefakt běhu) – podepsat a nainstalovat
   lze např. přes AltStore nebo Sideloadly.

Logy z CI se ukládají do větve `ci-logs-t46`.

Lokálně na Macu: `ios/php/build-php-ios.sh`, pak `cd ios/app && xcodegen generate` a otevřít
`TravianT46Local.xcodeproj`.

## Co bylo potřeba změnit

- `application/db_settings.php` – nastavení databáze z proměnných prostředí (`TRAVIAN_DB_DRIVER=mysql|sqlite`,
  `TRAVIAN_SQLITE_PATH`, `TRAVIAN_DB_HOST/PORT/USER/PASS/NAME`); `config.php`, `baglanti.php`, `durum.php`
  používají jedno sdílené spojení.
- `application/SqliteCompat.php` – překlad MySQL dotazů do SQLite (stejný jako u T4.4, navíc `INSERT … SET`,
  `&&`/`||`, `USE INDEX`, `SQL_CACHE`); `SqliteCompatPDO` překládá i dotazy přes `$baglanDB`.
- `application/sqlite_mysqli.php` – náhrada `mysqli_*` pro stránky envanter, gods, flags, žebříčky…
  (iOS PHP nemá rozšíření mysqli).
- Opravy: žebříčky (`Ranking.php`, `winner.php` – korelované poddotazy přes alias), oživení hrdiny
  (UPDATE dvou tabulek najednou), značky na mapě (`map_mark.php` – `imagegif()` s neplatným argumentem
  shazoval všechny dlaždice), admin logy přihlášení a vesnic, přesměrování `arastirma.php` na cizí server,
  obrázky bohů a knihovna d3 pro kolo štěstí se načítají lokálně (offline).
- Více účtů z jedné IP adresy (`TRAVIAN_SINGLE_ACCOUNT_PER_IP=1` vrátí původní pravidlo).

## Nový svět pro aplikaci

`local/make-sqlite.sh "mysql -uroot" http://127.0.0.1:8096` vytvoří v MariaDB čerstvý svět instalátorem hry
(`local/setup-db.sh`, bez přepsání `config.php`) a převede ho do `ios/app/Resources/travian.sqlite`.
Vyžaduje běžící herní server v režimu MySQL (`TRAVIAN_DB_NAME=travian46`, uživatel `travian`/`travian`).
