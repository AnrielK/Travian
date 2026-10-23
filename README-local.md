# Travian Local – TravianT4.4 pro lokální spuštění a testování na Macu

Balíček obsahuje:

| Složka | Co to je |
|---|---|
| `overlay/` | Změněné a nové soubory hry (úpravy pro **PHP 8** a lokální běh) – `scripts/setup.sh` z nich a z vašeho původního archivu složí `game/` |
| `mac-app/` | macOS aplikace ve Swiftu (SwiftUI + WKWebView), která hru spouští a obsahuje testovací nástroje |
| `scripts/` | Totéž z Terminálu bez Xcode (`start.sh`, `fast-forward.sh`) |

## 1. Jednorázová příprava

```bash
brew install php mariadb
cd TravianLocal
scripts/setup.sh ~/Downloads/TravianT4_4-main_2.zip    # původní archiv hry → vytvoří složku game/
```

Nic dalšího se nastavovat nemusí. Aplikace si spouští **vlastní soukromou instanci MariaDB** na portu 3307
(data v `~/Library/Application Support/TravianLocal`), takže nekoliduje s MySQL/MariaDB, které už případně máte.
`brew services start mariadb` **není** potřeba.

## 2a. Spuštění přes aplikaci

```bash
cd mac-app
./build-app.sh            # vytvoří ../TravianLocal.app
open ../TravianLocal.app
```

Nebo otevřete `mac-app/Package.swift` v Xcode a dejte Run (⌘R). Nechte aplikaci ležet vedle složky `game/`,
pak ji najde sama (jinak Nastavení → Složka hry).

Při prvním spuštění se inicializuje databáze a naimportuje `travian.sql` (~10 s). Potom se otevře přihlášení.

**Co aplikace umí**

- Spustit / zastavit MariaDB + PHP server, při ukončení aplikace je zastaví.
- Hra v okně aplikace (Web Inspector: pravé tlačítko → Inspect Element).
- **Další hráč** (⇧⌘N) – nové okno se samostatnými cookies, takže můžete být přihlášení jako dva hráči a útočit sami na sebe.
- Logy v záložkách: výstup serveru, PHP chyby, SQL chyby, MariaDB, odeslané e-maily (aktivační maily se neposílají ven, jen zapíší).
- **SQL konzole** přímo nad herní databází.
- Testovací nástroje:
  - *Dokončit vše hned* (⇧⌘F) – stavby, výzkumy, trénink, pohyby vojsk i obchodníků doběhnou při dalším načtení stránky,
  - *Naplnit sklady*, *+1000 zlata*, *Zrušit ochranu nováčků*,
  - *Rychlost serveru* (×1 … ×1000),
  - *Obnovit databázi* – čistý import.

## 2b. Spuštění z Terminálu (bez Xcode)

```bash
scripts/start.sh            # spustí vše, Ctrl+C vše zastaví
scripts/start.sh --reset    # smaže herní databázi a naimportuje ji znovu
scripts/fast-forward.sh     # „dokončit vše hned“ (v druhém okně Terminálu)
```

Funguje i na Linuxu (`apt install php php-mysql php-gd php-mbstring mariadb-server`).

## Vzhled – grafické balíčky (gpack)

Ve hře **Nastavení → Graphic pack** (`options.php?s=3`, v iOS aplikaci Menu → *Vzhled*) si každý hráč vybere vzhled:

| Volba | Vzhled |
|---|---|
| **Travian 4.4** (výchozí) | současný vzhled T4.4 |
| **Travian 4.0 classic** | krajina se stromy, kulaté menu, dřevěné cedule v bočních panelech, budovy T4.0 (gpack `travian_Travian_4.0_41`) |
| **Travian basic** | světlý klasický vzhled – bílá stránka, klasické ikony menu (gpack `travian_basic`) |

- Volba se ukládá do cookie `gpack` (pro každý prohlížeč zvlášť, okno *2. hráč* může mít jiný vzhled).
  Výchozí vzhled pro nové prohlížeče lze změnit proměnnou prostředí `TRAVIAN_GPACK=t44|t40|basic`.
- Původní balíčky z Travian-T4 (PHP 8) jsou v `gpack/legacy/packs/`. Jejich CSS je psané pro staré HTML T4.0,
  takže je hra nepoužívá přímo: skiny `gpack/legacy/compat/t40.css` a `basic.css` přebarvují rozložení T4.4
  grafikou z těchto balíčků (podklady jsou v `gpack/legacy/compat/t40/` a `basic/`, pro LTR zrcadlené).
  Do iOS aplikace se kopírují jen skiny, ne celé balíčky.
- Kód: `application/gpack.php` (seznam balíčků, cookie, `<link>` do hlavičky), `application/views/Profile/gpack.php`.

## Adresy a účty

- Hra: http://127.0.0.1:8080/login.php – účet si založte přes *Registrace*.
- Admin panel: http://127.0.0.1:8080/panel/ – **Multihunter / admin123** (heslo nastavené jen lokálně).
- Lokálně je povoleno víc účtů z jedné IP (původní server to blokoval); lze vypnout v Nastavení.

## Co bylo v kódu změněno

Původní kód byl psaný pro PHP 7.1 na sdíleném hostingu. Změny jsou minimální a cílené:

**PHP 8 kompatibilita**
- `application/compat.php` (nový): konstanty, které kód používá, ale nikde nedefinuje (PHP 7 je bral jako text, PHP 8 padá);
  `compat_count()` – `count()/sizeof()` na ne-polích (436 volání v 150 souborech přepsáno přes tokenizer, ne regexem,
  takže JavaScript v šablonách zůstal netknutý); shim pro staré `mysql_*` v admin panelu; bezpečné načítání spritů mapy.
- `application/Lang/fallback.php` (nový): texty, které existují jen v arabské nebo jen v anglické lokalizaci.
- Jednotlivé opravy: pořadí načtení konfigurace v `Account.php`, `session_unset()` s argumentem, `imagegif()` s třetím argumentem,
  `foreach` přes jeden řádek v admin panelu, prázdné ID v SQL u zpráv, chybějící cache složky (`mcache`, `hcache`, `mmark`),
  sprity mapy hledané i v `gpack/travian_default/`.

**Databáze** – `game/local/migrations.sql` doplňuje sloupce a tabulky, na které kód odkazuje a které v dumpu chyběly
(`activate.country`, `users.type`, `bank`, `operation`, `login_log`, `illegal_log`, `medalhas`, `notificacao`),
nastaví server jako „otevřený teď“ a admin heslo. Spouští se při každém startu (je idempotentní).

**Lokální běh**
- `application/config.php`: přístup k DB z proměnných prostředí (`TRAVIAN_DB_*`), výchozí `travian/travian@127.0.0.1:3306`.
- SQL chyby se zapisují do `game/local/logs/sql_errors.log` místo vypisování do stránky (`TRAVIAN_DEBUG=1` je vrátí do stránky).
- PHP server běží s `output_buffering=4096` jako původní hosting (kód posílá přesměrování až po HTML výstupu).

**Bezpečnost** – odstraněno: `cpanel/again.php` (mazal tabulky na vzdáleném hostingu s natvrdo zapsaným heslem),
hesla ke čtyřem cizím databázím v `Index/serverlist.php` (nahrazeno lokálním serverem), staré logy v `application/queue2/`
(obsahovaly session data a IP adresy hráčů z původního serveru), duplicitní dumpy v `cpanel/`.

## Jak bylo otestováno

Na PHP 8.3 + MariaDB 10.11:
- registrace (výběr kmene a sektoru), přihlášení, stavba polí a budov včetně dokončení přes frontu,
- trénink jednotek, útok na druhého hráče (bitva, hlášení obou stran, návrat vojsk), poslání surovin přes tržiště,
  zprávy, založení aliance, generované dlaždice mapy,
- automatický průchod ~740 stránkami hry za dva hráče a 250 stránkami admin panelu: **0 fatálních chyb, 0 SQL chyb**,
- `scripts/start.sh` z čisté instalace včetně Ctrl+C úklidu.

Swift aplikace je napsaná pro macOS 13+ a syntakticky zkontrolovaná, ale **nebyla zkompilovaná** (testovací prostředí běží
na Linuxu bez Xcode). Pokud Xcode nahlásí chybu, pošlete mi její text. Alternativně ji sestaví GitHub Actions
(`.github/workflows/build-mac-app.yml`, výsledek je ke stažení jako artefakt).

## Známá omezení

- Kód má i původní chyby nezávislé na PHP verzi (např. některé questy počítají sloupce místo řádků) – ty jsem neměnil,
  aby se nezměnilo chování hry.
- Platby (`tgpay.php`, `banksystem.php`) a mezi-serverová banka zlata jsou lokálně bez funkce.
- Hra počítá s rychlostí ×50 (výchozí v dumpu), změníte ji v aplikaci.
