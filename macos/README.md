# OpenVillage Local – macOS aplikace

Nativní SwiftUI aplikace pro lokální spuštění a testování OpenVillage 4.6 na Macu.

## Co umí

- **Dva režimy běhu**
  - **Nativně (Homebrew)** – PHP 8.3 + MariaDB + Redis přímo na Macu, bez Dockeru. Start trvá pár sekund
    a každá úprava PHP kódu se projeví hned po obnovení stránky.
  - **Docker Compose** – oficiální stack z `compose.yaml`. Volitelně s „živým zdrojákem“ (projekt je
    připojený do `/app`, takže není nutné přestavovat image).
- **Přehled** – stav služeb (MariaDB, Redis, web, worker), `health.php`, start/stop/restart, migrace,
  smazání světa, heslo admina (účet Support), kontrola a doinstalování závislostí přes Homebrew.
- **Hra a hráči** – vestavěný prohlížeč (WebKit) s profily hráčů. Každý profil má vlastní cookies,
  takže můžete mít přihlášených několik účtů najednou a testovat útoky, obchod, zprávy či aliance.
  Web Inspector je zapnutý (pravé tlačítko → Inspect Element), exporty účtu se stahují do Stažených.
- **Testy** – všechny regresní testy a kontroly z `tests/` a `scripts/` jedním klikem, s výstupem,
  časem a stavem; „Spustit vše“ / „Znovu neúspěšné“.
- **Logy** – živé sledování PHP chyb, web serveru, workeru, bootstrapu, MariaDB, Redisu a herního
  `error.log` (v Dockeru `docker compose logs -f`), s filtrem a režimem „jen chyby“.
- **SQL konzole** – dotazy do herní i globální databáze, tabulkový výsledek, šablony a historie (⌘↩︎).
- **Nástroje** – dokončit fronty (stavby, výzkum, výcvik, pohyby vojsk), naplnit sklady, přidat
  zlato/stříbro, zrušit ochranu začátečníka, vyprázdnit cache, zálohy a obnova.
  Fronty se jen „posunou na teď“ – dokončení provede skutečný worker, takže herní logika běží normálně.

## Sestavení

Potřebujete macOS 14+ a Xcode nebo Command Line Tools (`xcode-select --install`).

```sh
./macos/build-app.sh
open "macos/build/OpenVillage Local.app"
```

Aplikace si složku projektu najde sama (hledá nahoru od místa, kde leží `.app`). Jinak ji vyberte
v Nastavení (⌘,).

## Nativní režim – první spuštění

Závislosti doinstaluje tlačítko **Doinstalovat přes Homebrew** na Přehledu, nebo ručně:

```sh
brew install php@8.3 mariadb redis ripgrep node autoconf pkg-config
$(brew --prefix php@8.3)/bin/pecl install redis
```

Aplikace nepoužívá vaši systémovou MariaDB ani Redis – spouští vlastní instance s daty v
`~/Library/Application Support/OpenVillageLocal/native` (MariaDB jen přes unix socket, Redis na portu
16379). Hesla se vygenerují při prvním startu do `secrets.env` ve stejné složce.

Stejný běh lze ovládat i z terminálu:

```sh
scripts/native/openvillage-native.sh start | stop | status | test all | sql "SELECT …" | backup
```

## Docker režim

Stačí Docker Desktop, OrbStack nebo Colima. Při prvním startu aplikace vytvoří `.env` s náhodnými
hesly (pokud chybí) a vlastní override `compose.launcher.yaml` (port, živý zdroják) v Application Support.
Kontrolní skripty potřebují `ripgrep` (`brew install ripgrep`).
