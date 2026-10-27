# Travian T4.6 — převod na PHP 8 + zabezpečení

Kód je převedený z PHP 7 na **PHP 8.0 – 8.3** (testováno na PHP 8.3.6 + MariaDB 10.11),
hra se chová stejně jako dřív, navíc jsou opravené bezpečnostní díry a několik chyb.

## Požadavky

- PHP 8.0+ s rozšířeními `pdo_mysql`, `mysqli`, `mbstring`, `gd`, `session`
- MySQL / MariaDB

## Upgrade běžícího serveru

1. Zálohujte soubory i databázi.
2. Nahrajte nové soubory **kromě** `application/config.php` (ponechte svůj).
3. Spusťte na databázi `setup/data/missing_tables.sql` — doplní tabulky, které původní
   instalační skript nevytvářel, a chybějící sloupce `users.god` / `users.favor`.
   Skript lze spustit opakovaně, nic nepřepíše.
4. **Změňte heslo účtu `Multihunter`** (výchozí `12345`) a heslo k databázi.

Nová instalace přes `setup/` funguje jako dřív a po dokončení se sama zamkne (viz níže).

---

## 1. Kompatibilita s PHP 8

Stejný princip jako u T4.4: kde PHP 8 změnilo výsledek nějaké operace, používá se pomocná
funkce z `application/php8_compat.php`, která vrací přesně to, co PHP 7.

| Oblast | Proč | Přibl. míst |
|---|---|---|
| `safe_count()` | `count(null)` je v PHP 8 `TypeError` | ~410 |
| `php7_is_zero()`, `php7_is_blank()` | `"" == 0`, `0 == ""` jsou nově `false` | ~1 400 |
| `safe_num()` | aritmetika s prázdným/nečíselným řetězcem je `TypeError` | ~300 |
| `safe_div()` | dělení nulou je `DivisionByZeroError` | ~190 |
| `php7_ts()` | `date($f, null)` = „teď“ místo 1. 1. 1970 | ~90 |
| `safe_constant()` | `constant()` na neexistující konstantu háže výjimku | ~85 |
| `safe_max/min`, `safe_array_rand`, `safe_in_array`, `safe_array_sum`, `safe_array_push`, `safe_implode`, `safe_strpos` | `null`/prázdné pole místo pole je `Type/ValueError` | ~300 |
| `safe_imagecopyresized()` a spol. | chybějící obrázek: PHP 7 kousek přeskočilo, PHP 8 shodí celou dlaždici mapy / portrét hrdiny | ~190 |
| `safe_mysqli_fetch_array/assoc/num_rows` | neúspěšný `mysqli` dotaz (false) je `TypeError` | ~50 |
| `filter_string_polyfill()` | náhrada za zrušený `FILTER_SANITIZE_STRING` | 8 |
| `php8_define_fallbacks()` | konstanty, které kód používá, ale nikdy nebyly definované | 11 |

Přímé opravy: krátké `<? ... ?>` tagy převedené na `<?php` (jinak by se celý soubor včetně
hesel poslal do prohlížeče); PDO už neháže `HY093` na nepoužité parametry; `htmlspecialchars()`
má původní PHP 7 flagy; zbytky `mysql_*` převedeny na PDO; PHPMailer aktualizován na 6.10.0;
`baglanti.php` opraven (chybějící spojení u `mysqli_query`, duplicitní spojení).

## 2. Zabezpečení

### SQL injection
Hra skládala skoro všechny dotazy přímo z `$_GET`/`$_POST`. Každá vkládaná hodnota teď prochází
pomocnou funkcí přímo v místě, kde se SQL skládá (`application/php8_compat.php`):
- `sql_esc($x)` — hodnota uvnitř uvozovek (`'".sql_esc($x)."'`), escapuje jako `mysql_real_escape_string`
- `sql_num($x)` — číselná pozice (`WHERE id = ".sql_num($x)`), propustí jen číslo
- `sql_id($x)` — název sloupce/tabulky z proměnné (whitelist)
- `sql_in_ints($x)` — seznam ID pro `IN (...)`

Celkem **~5 700** vložených hodnot ve ~170 souborech. Nasazeno token-based nástrojem, který
sleduje stav uvozovek v SQL; zbytek (názvy sloupců, kódem skládané fragmenty) ověřen a opraven ručně.
Funkčně ověřeno: text s apostrofy a uvozovkami (zpráva, jméno) se teď uloží správně — v původním
kódu takový vstup dotaz rozbil.

### Další bezpečnostní opravy
- **Zadní vrátka v admin panelu**: heslo `972624118` fungovalo jako univerzální heslo k jakémukoli
  adminskému účtu. Odstraněno.
- **Porovnání hesel** přešlo z `==` na `hash_equals()` — `==` u md5 hashů tvaru `0e…` propustí
  i špatné heslo („magic hash“).
- **Nechráněné skripty**: `dropdb.php` a `cpanel11/again.php` (obojí maže celou databázi),
  `php.php`, `urunekle.php`, `adminenvanter.php` běžely komukoli. Nyní jen z příkazové řádky,
  resp. jen pro admina; `cpanel11/again.php` navíc obsahoval heslo k cizímu hostingu — odstraněno.
- **Instalátor** (`setup/`) šel po instalaci znovu spustit a smazat server. Teď se po dokončení
  zamkne (`setup/install.lock` + kontrola, zda už existují hráči).
- **Únik dat v DB vrstvě**: `application/DB.php` při chybě vypisoval SQL dotaz návštěvníkovi a
  logoval celý `$_POST`/`$_SESSION` (včetně hesel) do souboru dostupného z webu. Výpis je teď jen
  při `define('SQL_DEBUG', true)`, log neobsahuje hesla, a přibalené logy (s reálnými hesly z
  autorova serveru) jsou vyprázdněné. Přidány `.htaccess` (deny) do `application/queue2/`,
  `cpanel11/`, `setup/data/`.
- `vote.php` a `baglanti.php` braly přihlašovací údaje z napevno zapsaných hodnot v souboru —
  teď z `application/config.php`.

Hra zůstává PHP aplikace skládající SQL ručně; doporučené další kroky jsou přechod na připravené
dotazy (prepared statements) a změna všech výchozích hesel.

## 3. Opravené chyby (nejen PHP 8)

- `bonusal.php` a `carkodul.php` přičítaly bonusové suroviny **všem vesnicím na serveru**
  (UPDATE bez WHERE) — doplněno `WHERE wref = …`, bonus teď dostane jen vesnice hráče.
- Chybějící databázové tabulky (`kasalar`, `bank`, `operation`, `reward_log`, `login_log`,
  `illegal_log`) a sloupce (`users.god`, `users.favor`) — stránky inventáře, banky, bohů a
  žebříčků (`statistiken.php`) kvůli nim padaly. Doplněno v `setup/data/missing_tables.sql`.
- Chybějící jazykové texty a konstanty (`flag`, `GP_LOCATION2`, `farmlist27`, `KARG`, `R1`–`R4`,
  proměnné proměnné v questech) — v PHP 8 jinak fatální chyba.
- `statistiken.php` padal přes `mysqli_query($connection3, …)` bez spojení; `envanter.php` přes
  `mysqli_fetch_array(false)` — ošetřeno.
- Mapa: chybějící dlaždicové obrázky v PHP 8 shodily celý obrázek (totéž co u T4.4) — ošetřeno.
- `dropdb.php` měl syntaktickou chybu.

## 4. Jak bylo testováno

- `php -l` všech souborů na PHP 8.3; vlastní skenery na vzory rizikové v PHP 8.
- Instalace přes `setup/`, registrace hráčů všech 5 kmenů, automatický průchod ~2 500 stránkami,
  odeslání všech formulářů (vyplněných i prázdných), admin panel, bitvy mezi kmeny (104 hlášení).
- SQL injection: háček uvnitř `sql_num`/`sql_esc` hlásil každou podezřelou hodnotu za běhu
  (po opravách nehlásí žádnou), plus nezávislý sken, že v žádném SQL řádku nezůstal neošetřený
  `$_GET`/`$_POST`.
- **Diferenciální test:** původní kód na PHP 7.4 a převedený na PHP 8.3 krokované stejnými
  požadavky se stejným seedem. Po celém scénáři (registrace, stavby, bitvy, 46 hlášení) jsou
  databáze obou verzí shodné; rozdíly ve stránkách jsou jen časování, `ORDER BY RAND()` a
  opravené chyby. PHP 7 verze přitom tiše spolkla 60 SQL chyb, kterých se PHP 8 verze po
  doplnění tabulek zbavila.
