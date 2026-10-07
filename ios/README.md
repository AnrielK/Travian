# OpenVillage pro iOS (offline)

Celá hra běží **přímo v iPhonu**, bez Macu, bez Dockeru a bez internetu – se stejným kódem a stejnou
databází jako na serveru:

- **MariaDB 11.4** přeložená pro iOS jako knihovna (`ios/mariadb/build-mariadb-ios.sh`). Aplikace ji spouští
  ve vlákně (`mysqld_main`) s vlastním datovým adresářem; PHP se k ní připojuje unixovým socketem.
  Herní SQL se tedy nijak nepřekládá.
- **PHP 8.3 (ZTS)** jako knihovna (`ios/php/build-php-ios.sh`), ve vlákně běží vestavěný web server
  (`php -S`) s routerem `scripts/native/router.php` (náhrada Apache). ZTS drží virtuální pracovní adresář,
  takže `chdir()` v PHP nepřesune adresář procesu, na kterém závisí MariaDB.
- **Redis** nahrazuje `scripts/native/runtime/redis-polyfill.php` (tabulka v MariaDB, stejné API).
- **Worker** (`Automation.php` s `pcntl_fork`) nahrazuje automatizační tick: aplikace každou sekundu volá
  `/__openvillage/tick`, který podle původního `Launcher` spustí všechny splatné úlohy.
- Svět se vytvoří při prvním spuštění přes `/__openvillage/setup` (= `install.php`).

Spodní lišta: Zpět, Obnovit, Menu, **Nástroje** (dokončit fronty, suroviny, zlato, stříbro, ochrana,
nový svět, heslo admina), **2. hráč** (vlastní cookies), **Více** → Logy, SQL konzole, Nastavení světa (rychlost, délka kola, velikost mapy, **grafika**).

## Jak získat aplikaci

GitHub Actions (`.github/workflows/ios.yml`, větev `t46-ios`):
1. přeloží PHP a MariaDB pro iOS (výsledky se cachují),
2. vytvoří šablonu datového adresáře (`ios/mariadb/make-template.sh`, Homebrew `mariadb@11.4`),
3. přeloží aplikaci, **spustí ji v simulátoru a projde hru end-to-end** (`ios/tests/e2e.py`: registrace dvou
   hráčů, ~70 stránek, stavba dokončená automatizací, zprávy, admin),
4. vyrobí **nepodepsanou IPA** – artefakt `OpenVillage-unsigned-ipa`.

IPA nainstalujete přes Sideloadly / AltStore, nebo projekt otevřete v Xcode
(`cd ios/app && xcodegen generate && open OpenVillage.xcodeproj`, knihovny stáhněte z artefaktů CI).

## Stejný režim na Macu/Linuxu

`OV_IOS_EMULATION=1 scripts/native/openvillage-native.sh start` spustí hru stejně jako iOS aplikace
(bez Redisu, bez forku, tick, setup endpoint, jen rozšíření PHP z iOS buildu). Test:
`python3 ios/tests/e2e.py http://127.0.0.1:8080 <OV_CONTROL_TOKEN> <heslo admina>`.

## Když aplikace spadne při startu

Logy jsou v aplikaci Soubory → Na iPhonu → OpenVillage → `logs` (`mariadb.log`, `setup.log`,
`last-start.txt` s krokem, ve kterém start skončil). Při dalším spuštění je aplikace ukáže sama.

## Grafika (gpack)

Nastavení světa → Vzhled → Grafika: Travian T4.6 (29c89d54, výchozí), Travian Legends 655.3 (legends655),
OpenVillage 4.6 (a17a8f72),
Travian T4.4 Seasonized v2, T4.4 Seasonized a T4.4 Classic. Volí se balíček „mainPage“ (CSS a obrázky
rozhraní), změna platí hned. Na Macu/Linuxu stejně: `GAME_GPACK=TravianOld scripts/native/openvillage-native.sh start`.
29c89d54 má vzhled Travian Legends: vlastní rozložení stránky (`layout/LayoutT46.php` – horní lišta s hrdinou,
kulatá navigace, sklad pod navigací, rámované boční panely, celoplošné dorf1/dorf2 ve stylu buildingsV3),
jeho CSS + styly a17a8f72 pro dialogy, tlačítka a obsah stránek. Soubory, které balíčku chybí, server
(router i Apache) doplní z a17a8f72. Font Noto Serif (SIL OFL) je přibalený v `29c89d54/mainPage/fonts`.

legends655 je grafika Travian Legends 655.3 (obrázky + CSS) se stejným rozložením jako 29c89d54 (v `gpack.php`
`'layout' => 'legends'`). Vzniká skriptem `scripts/gpack/import-legends-gpack.py <rozbalený gpack/655.3> legends655 --no-rtl`
(`--no-rtl` vynechá obrázky a styly pro arabštinu/perštinu, balíček má pak ~124 MB):
základní CSS 29c89d54 zůstává pod CSS Legends (pokrývá starší názvy tříd), budovy podle kmene řeší vygenerované
`css/openvillage-t46-tribes.css` a chybějící obrázky se zkopírují z 29c89d54/a17a8f72. JavaScript Legends
(webpack/React klient) se nepoužívá – hra má vlastní skripty.
legends655 má navíc v `gpack.php` `'heroV2' => true` (`is_hero_v2()`): okno hrdiny (Inventář, Vlastnosti, Vzhled)
a aukce (Koupit, Prodat, Přehled aukcí) se vykreslí v nové podobě Legends (`Templates/hero/v2/*`,
`Controller/HeroAuctionV2.php`, komponenty v `resources/View/LegendsUi.php`); ostatní balíčky mají původní stránky.
