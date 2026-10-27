# T4.6 – převod na PHP 8, bezpečnost a opravy chyb

Kód běží na **PHP 8.0 – 8.3** (testováno na 8.3.6 + MariaDB 10.11). Herní chování zůstalo stejné jako
v PHP 7: diferenciální test (původní kód na PHP 7.4 a nový na PHP 8.3, stejné kroky) skončil se shodnou
databází včetně 46 hlášení z bitev.

## Upgrade běžícího serveru

1. Zálohujte soubory i databázi.
2. Nahrajte nové soubory **kromě** `application/config.php` (váš zůstává, funguje dál).
3. Spusťte jednou nad databází `setup/data/missing_tables.sql` (doplní tabulky a sloupce, které původní
   instalace vynechala – viz níže; skript je bezpečné pustit opakovaně).
4. **Změňte heslo účtu Multihunter**, pokud je stále výchozí `12345`.
5. Doporučujeme smazat záložní a vývojářské soubory (seznam na konci).

## 1. Převod na PHP 8

Stejný postup jako u T4.4: soubory shodné s T4.4 převzaty z otestovaného převodu, zbytek prošel stejnými
nástroji. Pomocné funkce v `application/php8_compat.php` vracejí přesně výsledky PHP 7:

| Funkce | Proč |
|---|---|
| `safe_count()` | `count(null)` je v PHP 8 chyba |
| `php7_is_zero()`, `php7_is_blank()` | změna porovnání řetězce s číslem (`"" == 0` je teď false) |
| `safe_num()`, `safe_div()` | `"" + 1` a dělení nulou jsou v PHP 8 chyba |
| `php7_ts()` | `date($f, null)` znamená v PHP 8 „teď“ |
| `safe_max/min/in_array/implode/array_push/…` | `null` / prázdné pole místo pole je chyba |
| `safe_image*()` | chybějící obrázek shodil celou dlaždici mapy / portrét hrdiny |
| `safe_mysqli_fetch_*()` | neúspěšný dotaz shodil stránku |

Navíc ve 4.6:
- krátké tagy `<?` převedeny na `<?php` (13 souborů) – s výchozím `short_open_