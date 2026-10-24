<?php

declare(strict_types=1);

/*
 * Language of the lobby pages: ?lang=cz|en (remembered in the same "travian-lang" cookie the game uses),
 * the cookie, or the browser / device language. Texts are written in English in the templates and
 * looked up here; missing ones stay English.
 */
function lobby_language(): string
{
    static $language = null;
    if ($language !== null) {
        return $language;
    }
    $choice = isset($_GET['lang']) ? (string)$_GET['lang'] : (string)($_COOKIE['travian-lang'] ?? '');
    if (isset($_GET['lang']) && in_array($choice, ['cz', 'en'], true)) {
        setcookie('travian-lang', $choice, ['expires' => time() + 365 * 86400, 'path' => '/', 'samesite' => 'Lax']);
    }
    if ($choice === 'cz' || $choice === 'en') {
        return $language = $choice === 'cz' ? 'cs' : 'en';
    }
    foreach (explode(',', (string)($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '')) as $part) {
        $code = strtolower(substr(trim($part), 0, 2));
        if ($code === 'cs' || $code === 'sk') {
            return $language = 'cs';
        }
        if ($code !== '') {
            break;
        }
    }
    return $language = 'en';
}

function lt(string $text): string
{
    static $czech = [
        'Your gameworlds' => 'Tvé herní světy',
        'Your game worlds' => 'Tvé herní světy',
        'Join new gameworld' => 'Připojit se k novému světu',
        'Join a new game world' => 'Připojit se k novému světu',
        'Login' => 'Přihlášení',
        'Rules and operations' => 'Pravidla a provoz',
        'Back to your gameworlds' => 'Zpět na tvé herní světy',
        'Local world' => 'Místní svět',
        'Play now' => 'Hrát',
        'Another account' => 'Další účet',
        'Gameworld details' => 'Podrobnosti herního světa',
        'Game world details' => 'Podrobnosti herního světa',
        'Server age in days' => 'Stáří serveru ve dnech',
        'Server speed' => 'Rychlost serveru',
        'Recommended' => 'Doporučeno',
        'Lobby' => 'Lobby',
        'Terms' => 'Podmínky',
        'Privacy' => 'Soukromí',
        'Health' => 'Stav serveru',
        'OpenVillage preservation project · Not affiliated with or endorsed by Travian Games.' => 'Projekt OpenVillage na zachování hry · Nesouvisí se společností Travian Games ani není jí podporován.',
        'Independent preservation project. Not affiliated with or endorsed by Travian Games.' => 'Nezávislý projekt na zachování hry. Nesouvisí se společností Travian Games ani není jí podporován.',
        'Age:' => 'Stáří:',
        'day' => 'den',
        'days' => 'dní',
        'Speed:' => 'Rychlost:',
        'Speed' => 'Rychlost',
        'Players:' => 'Hráči:',
        'Map size:' => 'Velikost mapy:',
        'Small' => 'Malá',
        'Medium' => 'Střední',
        'Large' => 'Velká',
        'Join gameworld' => 'Připojit se k hernímu světu',
        'Join the game world' => 'Připojit se k hernímu světu',
        'Player name' => 'Jméno hráče',
        'Email' => 'E-mail',
        'Password' => 'Heslo',
        'I accept the <a href="/terms.html">terms</a> and <a href="/privacy.html">privacy notice</a>.' => 'Souhlasím s <a href="/terms.html">podmínkami</a> a <a href="/privacy.html">zásadami ochrany soukromí</a>.',
        'Next you choose your tribe, the starting sector and the name of your character.' => 'Potom si vybereš národ, výchozí oblast a jméno své postavy.',
        'I already have an account – log in' => 'Už mám účet – přihlásit se',
        'I already play here – log in' => 'Už tady hraju – přihlásit se',
        'No account yet – join the game world' => 'Ještě nemám účet – připojit se k hernímu světu',
        'Continue' => 'Pokračovat',
        'Account name or e-mail address' => 'Jméno účtu nebo e-mailová adresa',
        'Password forgotten?' => 'Zapomenuté heslo?',
        'Log in with the account you created on this game world.' => 'Přihlas se účtem, který sis v tomto herním světě založil.',
        'Close' => 'Zavřít',
        'Back' => 'Zpět',
        'Menu' => 'Menu',
        'Language' => 'Jazyk',
        'Romans' => 'Římané',
        'Teutons' => 'Germáni',
        'Gauls' => 'Galové',
        'Egyptians' => 'Egypťané',
        'Huns' => 'Hunové',
        'Your form expired. Please try again.' => 'Platnost formuláře vypršela. Zkus to prosím znovu.',
        'Too many attempts. Please wait one minute.' => 'Příliš mnoho pokusů. Počkej prosím minutu.',
        'You must accept the terms and privacy notice.' => 'Musíš souhlasit s podmínkami a zásadami ochrany soukromí.',
        'Player name must be 3–15 letters, numbers, spaces, underscores, or hyphens.' => 'Jméno hráče musí mít 3–15 písmen, číslic, mezer, podtržítek nebo pomlček.',
        'Enter a valid email address.' => 'Zadej platnou e-mailovou adresu.',
        'Password must be 10–128 characters.' => 'Heslo musí mít 10–128 znaků.',
        'That player name or email is already registered.' => 'Toto jméno hráče nebo e-mail už je zaregistrovaný.',
        'Registration is temporarily unavailable.' => 'Registrace je dočasně nedostupná.',
        'Spartans' => 'Sparťané',
        'Game worlds' => 'Herní světy',
        'Create a new game world' => 'Založit nový herní svět',
        'World' => 'Svět',
        'Look' => 'Vzhled',
        'Tribes' => 'Národy',
        'Name of the world' => 'Název světa',
        'cannot be changed later' => 'později už nejde změnit',
        'a bigger map takes longer to create' => 'větší mapa = delší zakládání',
        'Round length (days)' => 'Délka kola (dny)',
        'Hero speed' => 'Rychlost hrdiny',
        'Administrator password' => 'Heslo správce',
        'Multihunter and Support of this world, at least 6 characters' => 'Multihunter a Support tohoto světa, aspoň 6 znaků',
        'Create world' => 'Založit svět',
        'Creating the world, this can take several minutes…' => 'Zakládám svět, může to trvat i několik minut…',
        'Delete' => 'Smazat',
        'Delete this world with all its players and villages?' => 'Smazat tento svět se všemi hráči a vesnicemi?',
        'The world was deleted.' => 'Svět je smazaný.',
        'The world could not be created:' => 'Svět se nepodařilo založit:',
        'Worlds can only be created and deleted on this computer.' => 'Světy jde zakládat a mazat jen na tomto počítači.',
        'Enter a name of the world (at most 40 characters).' => 'Zadej název světa (nejvýš 40 znaků).',
        'Choose a look.' => 'Vyber vzhled.',
        'The administrator password must have at least 6 characters.' => 'Heslo správce musí mít aspoň 6 znaků.',
        'Game worlds…' => 'Herní světy…',
    ];
    return lobby_language() === 'cs' ? ($czech[$text] ?? $text) : $text;
}
