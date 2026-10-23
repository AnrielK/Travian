<?php
/*
 * OpenVillage for XAMPP (Windows / Linux): starts the game with the PHP and MariaDB (MySQL) of XAMPP.
 * Started by start.bat / start.sh; usable on its own:
 *
 *   php xampp/launcher.php [--port=8080] [--no-browser]
 *
 * This file only checks the PHP version, in syntax that even PHP 5 can read, so an old PHP (e.g. an older
 * XAMPP found on the PATH) gets a clear message instead of a parse error (ASCII only: old PHP writes it to the
 * Windows console unconverted); the launcher is launcher-run.php.
 */

if (version_compare(PHP_VERSION, '8.2.0', '<')) {
    fwrite(STDERR, 'CHYBA: Hra potrebuje PHP 8.2 nebo novejsi, ale bylo spusteno PHP ' . PHP_VERSION
        . ' (' . PHP_BINARY . ').' . PHP_EOL
        . 'Spust start.bat ze slozky hry uvnitr XAMPPu s PHP 8.2+, nebo nastav cestu k jeho php.exe:' . PHP_EOL
        . '  set XAMPP_PHP=C:\\xampp\\php\\php.exe' . PHP_EOL);
    exit(1);
}
require __DIR__ . '/launcher-run.php';
