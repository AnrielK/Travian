# Travian Local pro iOS

Celá hra běží **přímo v iPhonu**, bez Macu a bez internetu:

- **PHP 8.3** je přeložené pro iOS jako knihovna (`ios/php/build-php-ios.sh` → `PHPCore.xcframework`)
  a v aplikaci běží jeho vestavěný webový server (`php -S`) ve vlákně na `127.0.0.1`.
- Místo MariaDB se používá **SQLite** (`application/SqliteCompat.php` překládá MySQL dotazy, registruje
  MySQL funkce a napodobuje MySQL chování – dělení, auto-increment, prázdné řetězce v číselných sloupcích…).
  Stejný PHP kód běží dál i na MySQL; přepíná se proměnnou `TRAVIAN_DB_DRIVER=sqlite`.
- Hra se zobrazuje ve WKWebView. Spodní lišta: Zpět, Obnovit, Menu (Vesnice, Mapa, Hlášení, Admin…),
  **Nástroje**, **2. hráč**, **Logy**.

## Jak získat aplikaci

GitHub Actions (`.github/workflows/ios-php.yml`, větev `ios-local`) při každém pushi:
1. přeloží PHP + libpng + libjpeg pro iOS (výsledek se cachuje),
2. vygeneruje Xcode projekt (XcodeGen, `ios/app/project.yml`) a přeloží aplikaci,
3. **spustí ji v iOS simulátoru a otestuje celou hru** (`ios/tests/smoke.py`: registrace dvou hráčů,
   stavba, trénink, zprávy, aliance, trh, útok a bitva, dokončení časovačů, mapa, crawl ~330 stránek),
4. vyrobí **nepodepsanou IPA** pro iPhone – artefakt `TravianLocal-unsigned-ipa` u běhu workflow.

IPA nainstalujete přes Sideloadly / AltStore (podepíší ji vaším Apple ID), nebo projekt otevřete v Xcode:

```bash
cd ios/app && xcodegen generate && open TravianLocal.xcodeproj
```

(`PHPCore.xcframework` stáhněte z artefaktu do `ios/php/build/`, nebo ho na Macu sestavte `ios/php/build-php-ios.sh`, ~15 min.)

## Nástroje v aplikaci

- **Dokončit vše hned** – stavby, výzkumy, trénink i pohyby vojsk a obchodníků doběhnou při dalším načtení stránky
- **Naplnit sklady**, **+1000 zlata**, **Zrušit ochranu nováčků**, **Rychlost serveru** ×1–×1000
- **Hráči** – přehled účtů, **Obnovit databázi** – čistý svět
- **2. hráč** – samostatné okno s vlastními cookies (můžete útočit sami na sebe)
- **Logy** – přístupový log serveru, PHP chyby, SQL chyby
- Admin panel: Menu → Admin panel, účet **Multihunter / admin123**
- Data (databáze, logy) jsou vidět v aplikaci Soubory → Na iPhonu → Travian Local

## Omezení

- Hra má desktopové rozložení – na iPhonu je zmenšená (lze přiblížit prsty), lépe vypadá na šířku nebo na iPadu.
- Když iOS aplikaci uspí na dlouho, může zavřít síťový socket serveru; aplikace se ho po návratu pokusí
  spustit znovu. Kdyby hra nenaběhla, aplikaci ukončete a spusťte znovu.
- E-maily (aktivace) se neposílají; registrace funguje bez nich.
