<?php
require_once('UI/Login.php');
require_once('UI/Register.php');
require_once('UI/Footer.php');
require_once('UI/ToolBar.php');
require_once('UI/Support.php');
require_once('UI/Admin.php');
require_once('UI/Errors.php');
require_once('UI/Build.php');
require_once('UI/News.php');
require_once('UI/Plus.php');
require_once('UI/Options.php');
require_once('UI/Manual.php');
require_once('UI/svg.php');
require_once('UI/Skin.php');

define("BUILDING_BUILDING", 'Budovy:');
define("BUILDING_COMPLETE", 'dokončit stavbu okamžitě');
define("BUILDING_LEVEL", 'úroveň');
define("BUILDING_QUEUE", 'fronta');
define("BUILDING_TIMER", 'hod. Hotovo v ');
define("BUILDING_ARCHITECT", 'Budova');
define("SIDEINFO_HERO1", 'Tvůj hrdina je mrtvý');
define("SIDEINFO_HERO2", 'Hrdina je zdravý');
define("SIDEINFO_HERO3", 'Hrdina je ve své domovské vesnici');
define("SIDEINFO_HERO4", 'Tvůj hrdina je ve tvé vesnici');
define("SIDEINFO_HERO5", 'na cestě');
define("SIDEINFO_HERO5H", 've vesnici');
define("SIDEINFO_HERO6", 'Domovská vesnice hrdiny je');
define("SIDEINFO_HERO7", 'Žádný hrdina.');
define("SIDEINFO_HERO8", 'přehled');
define("SIDEINFO_HERO9", 'Dobrodružství');
define("SIDEINFO_HERO10", 'Dostupná dobrodružství');
define("SIDEINFO_HERO11", 'Vyléčit');
define("SIDEINFO_HERO12", 'Zkušenosti');

//exchange gold and silver
define("exchange0", 'Směnárna stříbra');
define("exchange1", 'Směnit');
define("exchange2", 'Nemáš dost zlata');
define("exchange3", 'Nemáš dost stříbra');
define("exchange4", 'Nebylo zadáno zlato ke směně');
define("exchange5", 'Převod zlata');
define("exchange6", 'Nemáš dost zlata');
define("exchange7", 'Směnárna stříbra');
define("exchange8", 'Vyměnit stříbro za zlato');
define("exchange9", 'Absolvuj');
define("exchange10", 'dobrodružství, aby se ti otevřela aukce!');

define("LANG_UR", "en");
define("DIRECTION", "LTR");
define("DIRECTION_2", "left");
define("LOCATION_NAME", 'Global - CZ');

define("INCLUDED", "");
define("INS0", 'Změna práv složek (CHMOD)');
define("INS1", 'Po instalaci');
define("INS2", 'Smaž instalační složku');
define("INS3", 'Vrať CHMOD složky GameEngine zpět na 644');
define("INS4", 'Instalací a používáním této hry neseš plnou odpovědnost za veškeré právní důsledky, které mohou vyvolat vlastníci nelicencovaného obsahu, jehož zveřejňování tvou kopií hry povolíš.');
define("INS5", 'Tým, který vytvořil tento skript, ani tým, který jej upravil do této distribuce, neodpovídá za žádné škody na tvém počítači či serveru.');
define("INS6", 'Tým autorů ověřil, že veškerý kód v době vydání fungoval správně a bez jim známých bezpečnostních rizik. Totéž platí pro tým, který skript upravil do této distribuce.');
define("INS7", 'Uživatelé by měli kód zkontrolovat sami na vlastní odpovědnost.');
define("INS8", 'Nemáš právo upravovat upozornění o autorských právech ani vydávat tento skript za vlastní.');
define("INS9", 'A nakonec – užij si to.');
define("INS10", 'Chyba při vytváření constant.php, zkontroluj CHMOD.');
define("INS11", 'Měsíc');
define("INS12", 'Den');
define("INS13", 'rok');
define("INS14", 'Hod');
define("INS15", 'Min');
define("INS16", 'Sek');
define("INS17", 'Název serveru:');
define("INS18", 'Rychlost serveru:');
define("INS19", 'Rychlost jednotek:');
define("INS20", 'Kapacita obchodníků (1 = 1x...):');
define("INS21", 'Velikost mapy:');
define("INS21M", "25x25");
define("INS22", "50x50");
define("INS23", "100x100");
define("INS24", "150x150");
define("INS25", "200x200");
define("INS26", "250x250");
define("INS27", "300x300");
define("INS28", "350x350");
define("INS29", "400x400");
define("INS30", 'Domovská stránka:');
define("INS31", 'Ochrana nových hráčů');
define("INS31M", '2 hodiny');
define("INS32", '3 hodiny');
define("INS33", '5 hodin');
define("INS34", '8 hodin');
define("INS35", '10 hodin');
define("INS36", '12 hodin');
define("INS37", '24 hodin (1 den)');
define("INS38", '48 hodin (2 dny)');
define("INS39", '72 hodin (3 dny)');
define("INS40", '120 hodin (5 dní)');
define("INS41", 'Kapacita skladu:');
define("INS42", 'Účinek turnajového hřiště (po x polích):');
define("INS43", 'Admin');
define("INS44", 'Viditelnost admina na stránce:');
define("INS45", 'Ano');
define("INS46", 'Ne');
define("INS47", 'DATABÁZE');
define("INS48", 'Hostitel:');
define("INS49", 'Uživatelské jméno:');
define("INS50", 'Heslo:');
define("INS51", 'Název databáze:');
define("INS52", 'Nastavení Travian Plus');
define("INS53", 'Doba trvání Travian Plus:');
define("INS54", '12 hodin');
define("INS55", '1 den');
define("INS56", '2 dny');
define("INS57", '3 dny');
define("INS58", '4 dny');
define("INS59", '5 dní');
define("INS60", '6 dní');
define("INS61", '7 dní');
define("INS62", 'Bonus +25 %:');
define("INS63", '12 hodin');
define("INS64", '1 den');
define("INS65", '2 dny');
define("INS66", '3 dny');
define("INS67", '4 dny');
define("INS68", '5 dní');
define("INS69", '6 dní');
define("INS70", '7 dní');
define("INS71", 'Lze prodávat suroviny?');
define("INS72", 'Ano');
define("INS73", 'Ne');
define("INS74", 'Lze prodávat kulturní body?');
define("INS75", 'Ano');
define("INS76", 'Ne');
define("INS77", 'Kolik surovin se prodá?');
define("INS78", 'Cena za suroviny');
define("INS79", 'Cena kulturních bodů');
define("INS80", 'Kolik kulturních bodů?');
define("INS81", 'Výchozí počet zlata');
define("INS82", 'Počet vesnic pro pozvánky');
define("INS83", 'Zlato za pozvání');
define("INS84", 'Nastavení serveru');
define("INS85", 'Server začne:');
define("INS86", 'Časové razítko (generuje se v UTC+0!)');
define("INS87", 'Artefakty:');
define("INS88", '(časové razítko)');
define("INS89", 'Vesnice Divů světa:');
define("INS90", 'Časové razítko ');
define("INS91", 'Svitky:');
define("INS92", 'Časové razítko');
define("INS92M", 'Množství kulturních bodů:');
define("INS93", 'Vysoké');
define("INS94", 'Nízké');
define("INS95", 'Úkoly');
define("INS96", 'Ano');
define("INS97", 'Ne');
define("INS98", 'Max. počet obrázků mapy v mezipaměti');
define("INS99", 'Max. počet obrázků hrdiny v mezipaměti');
define("INS100", 'Kvalita mapy ');
define("INS101", 'Okamžitý výcvik jednotek');
define("INS102", 'Ano');
define("INS103", 'Ne');
define("INS104", 'Doba aukce');
define("INS105", 'Hodnoty oáz');
define("INS106", 'Ochrana začátečníků se prodlouží každých 12 hodin o ...(v sekundách)');
define("INS107", 'Pokud tvůj hosting nemá mnoho místa, zvaž to! ');
define("INS108", '1000 obrázků = ~80 MB');
define("INS109", 'Pokud tvůj hosting nemá mnoho paměti RAM, zvaž to');
define("INS110", '1000 obrázků = ~2,60 GB');
define("INS111", 'Perfektní kvalita – 100, střední – 75 (obrázek bude 3× horší než při maximu)');
define("INS112", 'Zalidnit oázy');
define("INS113", 'Upozornění');
define("INS114", ': Může to chvíli trvat. Neklikej, počkej, než se načte další stránka!');
define("INS115", 'Vytvořit účet Multihunter');
define("INS116", 'Jméno:');
define("INS117", 'Heslo:');
define("INS118", 'Pozor: zapamatuj si toto heslo! Budeš ho potřebovat do administrace');
define("INS119", 'Chyba při vytváření dat světa. Zkontroluj konfiguraci nebo soubor.');
define("INS120", 'Vytvořit data světa');
define("INS121", 'Upozornění');
define("BUILDING_CANCEL", 'Zrušit');
define("INS122", ': Může to chvíli trvat. Neklikej, počkej, než se načte další stránka!');
define("INS123", 'Děkujeme za instalaci skriptu.');
define("INS124", 'Odstraň nebo přejmenuj instalační složku.');
define("INS125", 'Všechny soubory jsou na místě a databáze je vytvořená – můžeš začít hrát na vlastním Travianu.');
define("INS126", 'Chyba při importu databáze. Zkontroluj konfiguraci.');
define("INS127", 'Vytvořit strukturu databáze');
define("INS128", 'Upozornění: Může to chvíli trvat. Neklikej, počkej, než se načte další stránka');
define("INS129", 'Kolik dobrodružství přibude za den?');

define("CLANG", 'Vyber jazyk:');
define("MULTI_RULES", 'Zakázáno:<br/>Registrovat více než 1 účet z 1 IP adresy.<br/>Nezajímá nás, že hraješ s bratrem/sestrou/rodinou apod.: 1 IP – 1 účet.<br/>
	Zakázáno je registrovat účty s pomocí přátel nebo z falešných IP adres,<br/>takové účty budou odhaleny a potrestány:<br/> například ztrátou 50 % jednotek.<br />Buď čestný a hraj fér.');
define("OK", 'OK');
define("CROPFINDER", 'Hledač obilí');
define("MAP", 'Mapa');
define("MINIMAP", 'Minimapa');
define("GO", 'Jít');
define("GO_TO", 'Přejít na');
define("PLEASE_WAIT", 'počkej');

define("CATEGORY", '\'Kategorie');
define("EDITPROFILE", 'Upravit profil');
define("COORDIANTES", 'Souř.');
define("POPULATION", 'Obyvatelé');
define("WOOD", 'dřevo');
define("ABONDENDVALLY", 'Opuštěné údolí');
define("UNOCCUPIEDOASES", 'Neobsazená oáza');
define("UNOCCUPIEDOASIS", 'Neobsazená oáza');
define("OCCUPIEDOASES", 'Obsazená oáza');
define("OCCUPIEDOASIS", 'Obsazená oáza');
define("ABANDONEDVALLEY", 'Opuštěné údolí');
define("BUILDRALLYPOINTTORAID", '(postav shromaždiště)');
define("PLAYER", 'Hráč');
define("TRIBE", 'Národ');
define("VILLAGE", 'Vesnice');
define("ALLIANCE", 'Aliance');
define("SIDEINFO_ADVENTURES", 'Dobrodružství');
define("SIDEINFO_AUCTIONS", 'Aukce');
define("SIDEINFO_PROFILE", 'Profil');
define("SIDEINFO_ALLIANCE", 'Aliance');
define("SIDEINFO_ALLY_FORUM", 'Fórum aliance');
define("SIDEINFO_CHANGE_TITLE", 'Klikni pro změnu titulu');
define("SIDEINFO_CHANGEVIL_TITLE", 'Přejmenovat vesnici');
define("SIDEINFO_CHANGEVIL_LABEL", 'Nové jméno vesnice');
define("SIDEINFO_CHANGEVIL_BTN", 'Přijmout');
define("HEADER_MESSAGE_NEW", 'Nové');


define("MAINMENU", 'Hlavní menu');


define("POPUALTION", 'Obyvatelé');
//MAIN MENU
define("TRIBE1", 'Římané');
define("TRIBE2", 'Germáni');
define("TRIBE3", 'Galové');
define("TRIBE4", 'Příroda');
define("TRIBE5", 'Nataři');
define("TRIBE6", 'Egypťané');
define("TRIBE7", 'Hunové');
define("PRISONERS", 'Zajatci');
define("PRISONERSIN", 'Zajatci v');
define("PRISONERSFROM", 'Zajatci z');
define("HOME", 'Domovská stránka');
define("PW_ER", 'špatné heslo');
define("INSTRUCT", 'Pokyny');
define("ADMIN_PANEL", 'Administrace');
define("MASS_MESSAGE", 'Hromadná zpráva');
define("LOGOUT", 'Odhlásit');
define("PROFILE", 'Profil');
define("SUPPORT", 'Podpora');
define("RULES", 'Pravidla');

define("P", "P");
define("L", "l");
define("U", "u");
define("S", "s");
define("UPDATE_T_10", 'Aktualizovat Top 10');
define("SYSTEM_MESSAGE", 'Systémová zpráva');
define("TRAVIAN_PLUS", "Travian <b><font color=\"#71D000\">P</font><font color=\"#FF6F0F\">l</font><font color=\"#71D000\">u</font><font color=\"#FF6F0F\">s</font></b>");
define("CONTACT", 'Kontaktuj nás!');
define("HEADER_MESSAGES_NEW", 'Nové');

define("HEADER_PLUS", 'Plus');
define("HEADER_ADMIN", 'Admin');
define("HEADER_PLUSMENU", 'Menu Plus');

define("HEADER_DORF1", 'Suroviny');
define("HEADER_DORF2", 'Centrum vesnice');
define("HEADER_STATS", 'Statistiky');
define("HEADER_MAP", 'Mapa');
define("HEADER_NOTICES", 'Hlášení');
define("HEADER_MESSAGES", 'Zpráva');

define("HEADER_GOLD", 'Zlato');
define("HEADER_SILVER", 'Stříbro');
define("HEADER_NIGHT", 'Noc');
define("HEADER_DAY", 'Den');
define("HEADER_NOTICES_NEW", 'Nové hlášení');
define("NO_PERMISSION", 'Nemáš oprávnění');


define("LOGOUT_TITLE", 'Odhlášení dokončeno!');
define("LOGOUT_H4", 'Děkujeme za návštěvu');
define("LOGOUT_DESC", 'Pokud tento počítač používají i jiní lidé, měl bys pro svou bezpečnost smazat cookies:');
define("LOGOUT_LINK", 'Vymazat cookies');
define("PREREG1", 'Rychlost jednotek');
define("PREREG2", 'Administrátor');
define("PREREG3", 'Medaile v ');
define("PREREG4", 'Server začne: ');
define("PREREG5", 'Do startu: ');

define("LOGIN_PW_SENT", 'Zpráva pro obnovení hesla byla odeslána.');

define("REGISTER_USERINFO", 'Registrovat');
define("REGISTER_USERNAME", 'Jméno');
define("REGISTER_EMAIL", 'E-mail');
define("REGISTER_PASSWORD", 'Heslo');
define("REGISTER_STARTER", "");
define("REGISTER_SELECT_TRIBE", 'Vyber národ');
define("REGISTER_LOCATION", 'Místo');
define("REGISTER_NE", 'Severovýchod');
define("REGISTER_NW", 'Severozápad');
define("REGISTER_SE", 'Jihovýchod');
define("REGISTER_SW", 'Jihozápad');
define("REGISTER_RANDOM", 'Náhodně');
define("REGISTER_MOREINFO", "T&C");
define("REGISTER_CLOSED", 'Registrace je uzavřená. Na tento server se nelze zaregistrovat.');
define("newmsg", 'Nové zprávy:');
//MENU
define("REG", 'Registrovat');
define("FORUM", 'Fórum');
define("CHAT", 'Chat');
define("IMPRINT", 'Tiráž');
define("MORE_LINKS", 'Další odkazy');
define("TOUR", 'Prohlídka hry');
//PLUS
define("PLUS0", 'Funkce Plus');
define("PLUS1", 'Přehled');
define("PLUS2", 'Trvání');
define("PLUS3", 'Zlato');
define("PLUS4", 'Akce');
define("PLUS5", 'Máš ');
define("PLUS6", 'zlatých mincí');
define("PLUS7", 'Zbývá:');
define("PLUS8", 'Dny');
define("PLUS9", 'Hodiny');
define("PLUS10", 'Minuty');
define("PLUS11", 'Zlato');
define("PLUS12", 'Aktivovat');
define("PLUS13", 'Prodloužit');
define("PLUS14", 'Nedostatek zlata');
define("PLUS15", 'Produkce: dřevo');
define("PLUS16", 'Produkce: hlína');
define("PLUS17", 'Produkce: železo');
define("PLUS18", 'Produkce: obilí');
define("PLUS19", 'NPC obchodník ');
define("PLUS20", 'Nyní');
define("PLUS21", 'Na tržiště!');
define("PLUS22", 'Směna zlata a stříbra');
define("PLUS23", 'Směnit');
define("PLUS24", 'Dokončit všechny stavby a výzkumy');
define("PLUS25", 'Dokončit');
define("PLUS26", 'Koupit');
define("PLUS27", 'Kulturní body');
define("PLUS28", 'Koupit');
define("PLUS29", 'Všechny druhy surovin');
define("PLUS30", 'Den');
define("PLUS31", 'Zlatý klub');
define("PLUS32", 'Funkce Zlatého klubu:');
define("PLUS33", '1. Seznam farem');
define("PLUS34", '2. Obchodní trasy');
define("PLUS35", '3. Automatický úkryt jednotek');
define("PLUS36", '4. Hledač obilí');
define("PLUS37", '5. Stavitel');
define("PLUS38", '6. Obchod x3');
define("PLUS39", 'Všechny funkce jsou zdarma!');
define("PLUS40", 'Celá hra');
define("PLUS41", 'Aktivováno');


//active
define("ACTIV1", 'Ahoj');
define("ACTIV2", 'Registrace proběhla úspěšně. Během několika minut ti přijde e-mail s přístupovými údaji.<br /><br />

	E-mail bude odeslán na tuto adresu:');

define("ACTIV3", 'Pro aktivaci účtu zadej kód nebo klikni na odkaz v e-mailu.');
define("ACTIV4", 'Aktivační kód');
define("ACTIV5", 'Nepřišel ti e-mail?');
define("ACTIV6", 'E-mail se někdy přesune do složky se spamem. Další pomoc najdeš ');
define("ACTIV7", 'zde');
define("ACTIV8", 'Registraci můžeš zrušit a zaregistrovat se znovu s <u>jinou e-mailovou adresou</u>.
	Aktivační kód ti pak přijde znovu');
define("ACTIV9", 'Tvůj účet byl úspěšně aktivován.</p><p class="f9">Pokračuj tímto odkazem na <a href="login.php">přihlášení</a>');
define("ACTIV10", 'Buď je heslo špatně, nebo už byla registrace smazána.');
//ERRORS
define("USRNM_EMPTY", '(Jméno je prázdné)');
define("USRNM_TAKEN", '(Jméno už se používá.)');
define("USRNM_SHORT", '(min. 3 znaky)');
define("USRNM_CHAR", '(Neplatné znaky, povoleny jen А-Я, A-Z, 0-9)');
define("PW_EMPTY", '(Heslo je prázdné)');
define("PW_SHORT", '(min. 4 znaky)');
define("PW_INSECURE", '(Heslo není bezpečné. Zvol bezpečnější.)');
define("EMAIL_EMPTY", '(E-mail je prázdný)');
define("EMAIL_INVALID", '(Neplatná e-mailová adresa)');
define("EMAIL_TAKEN", '(E-mail už se používá)');
define("TRIBE_EMPTY", '<li>Vyber si národ.</li>');
define("AGREE_ERROR", '<li>Pro registraci musíš souhlasit s pravidly hry a všeobecnými obchodními podmínkami.</li>');
define("LOGIN_USR_EMPTY", 'Zadej jméno.');
define("LOGIN_PASS_EMPTY", 'Zadej heslo.');
define("EMAIL_ERROR", 'E-mail neodpovídá existujícímu');
define("PASS_MISMATCH", 'Hesla se neshodují');
define("ALLI_OWNER", 'Před smazáním urči vlastníka aliance');
define("SIT_ERROR", 'Zástupce už je nastaven');
define("USR_NT_FOUND", 'Jméno neexistuje.');
define("LOGIN_PW_ERROR", 'Heslo je špatné.');
define("WEL_TOPIC", 'Užitečné tipy a informace ');
define("ATAG_EMPTY", 'Zkratka je prázdná');
define("ANAME_EMPTY", 'Název je prázdný');
define("ATAG_EXIST", 'Zkratka je obsazená');
define("ANAME_EXIST", 'Název je obsazený');


define("CUR_PROD", 'Současná produkce');
define("NEXT_PROD", 'Produkce na úrovni ');

//BUILDINGS
define("B1", 'Dřevorubec');
define("B1_DESC", 'Dřevorubec kácí stromy a vyrábí dřevo. Čím víc ho rozšíříš, tím víc dřeva vyrobí.');
define("B2", 'Hliněný důl');
define("B2_DESC", 'Zde se těží hlína. Zvýšením úrovně zvýšíš produkci hlíny.');
define("B3", 'Železný důl');
define("B3_DESC", 'Zde horníci těží vzácné železo. Zvýšením úrovně dolu zvýšíš produkci železa.');
define("B4", 'Obilné pole');
define("B4_DESC", 'Zde se pěstuje jídlo pro tvé obyvatelstvo. Zvýšením úrovně pole zvýšíš produkci obilí.');

//DORF1
define("LUMBER", 'Dřevo');
define("CLAY", 'Hlína');
define("IRON", 'Železo');
define("CROP", 'Obilí');
define("LEVEL", 'Úroveň');
define("CROP_COM", CROP . ' spotřeba');
define("PER_HR", 'za hodinu');
define("PROD_HEADER", 'Produkce');
define("ANNOUNCEMENT", 'Oznámení');
define("GO2MY_VILLAGE", 'Přejít do mé vesnice');
define("VILLAGE_CENTER", 'Centrum vesnice');
define("FINISH_GOLD", 'Okamžitě dokončit všechny stavby a výzkumy v této vesnici za 2 zlaté?');
define("WAITING_LOOP", '(ve frontě)');
define("HRS", '(hod.)');
define("DONE_AT", 'hotovo v');
define("CANCEL", 'zrušit');
define("LOYALTY", 'Loajalita');
define("CALCULATED_IN", 'Vypočteno za');
define("SEVER_TIME", 'Čas serveru:');


//======================================================//
//================ UNITS - DO NOT EDIT! ================//
//======================================================//
define("U0", 'Hrdina');

//ROMAN UNITS
define("U1", 'Legionář');
define("U2", 'Pretorián');
define("U3", 'Imperián');
define("U4", "Equites Legati");
define("U5", "Equites Imperatoris");
define("U6", "Equites Caesaris");
define("U7", 'Beranidlo');
define("U8", 'Ohnivý katapult');
define("U9", 'Senátor');
define("U10", 'Osadník');

//TEUTON UNITS
define("U11", 'Pálkař');
define("U12", 'Oštěpař');
define("U13", 'Sekerník');
define("U14", 'Zvěd');
define("U15", 'Paladin');
define("U16", 'Germánský rytíř');
define("U17", 'Beranidlo');
define("U18", 'Katapult');
define("U19", 'Kmenový vůdce');
define("U20", 'Osadník');

//GAUL UNITS
define("U21", 'Falanga');
define("U22", 'Šermíř');
define("U23", 'Slídič');
define("U24", 'Theutatesův blesk');
define("U25", 'Druid jezdec');
define("U26", "Haeduan");
define("U27", 'Beranidlo');
define("U28", 'Válečný katapult');
define("U29", 'Náčelník');
define("U30", 'Osadník');
define("U99", 'Pasti');

//NATURE UNITS
define("U31", 'Krysa');
define("U32", 'Pavouk');
define("U33", 'Had');
define("U34", 'Netopýr');
define("U35", 'Divočák');
define("U36", 'Vlk');
define("U37", 'Medvěd');
define("U38", 'Krokodýl');
define("U39", 'Tygr');
define("U40", 'Slon');

//NATARS UNITS
define("U41", 'Pikenýr');
define("U42", 'Trnitý válečník');
define("U43", 'Strážce');
define("U44", 'Draví ptáci');
define("U45", 'Jezdec se sekerou');
define("U46", 'Natarský rytíř');
define("U47", 'Válečný slon');
define("U48", 'Balista');
define("U49", 'Natarský císař');
define("U50", 'Natarský osadník');

//Egyptian UNITS
define("U51", 'Domobrana otroků');
define("U52", 'Ashův strážce');
define("U53", 'Válečník s chopešem');
define("U54", 'Sopduův průzkumník');
define("U55", 'Anhurova stráž');
define("U56", 'Rešepův vůz');
define("U57", 'Beranidlo');
define("U58", 'Kamenný katapult');
define("U59", 'Nomarcha');
define("U60", 'Osadník');

//Huns UNITS
define("U61", 'Žoldnéř');
define("U62", 'Lučištník');
define("U63", 'Pozorovatel');
define("U64", 'Stepní jezdec');
define("U65", 'Ostrostřelec');
define("U66", 'Nájezdník');
define("U67", 'Beranidlo');
define("U68", 'Katapult');
define("U69", 'Logades');
define("U70", 'Osadník');


define("U99", 'Pasti');
//INDEX.php

define("PLAYER_STATISTICS", 'Statistiky hráčů');


define("P_ONLINE", 'Hráči online: ');
define("P_TOTAL", 'Hráčů celkem: ');
define("CHOOSE", 'Vyber server.');
//define("STARTED"," The server started ". round((time()-COMMENCE)/86400) ." days ago.");

//ANMELDEN.php
define("NICKNAME", 'Přezdívka');

define("INVITED", 'Pozval tě (pokud existuje)');
define("EMAIL", 'E-mail');
define("PASSWORD", 'Heslo');
define("ROMANS", 'Římané');
define("TEUTONS", 'Germáni');
define("GAULS", 'Galové');
define("NW", 'Severozápad');
define("NE", 'Severovýchod');
define("SW", 'Jihozápad');
define("SE", 'Jihovýchod');
define("RANDOM", 'náhodně');
define("ACCEPT_RULES", ' Souhlasím s pravidly hry a všeobecnými <a href=\'rules.php\' target=\'_blank\'>obchodními podmínkami</a>.');
define("ONE_PER_SERVER", 'Každý hráč smí mít na serveru jen JEDEN účet.');
define("BEFORE_REGISTER", 'Před registrací účtu si přečti <a href=\'../anleitung.php\' target=\'_blank\'>pravidla</a>');
define("MULTIBAN", 'Jeden počítač – jeden účet. Všechny multiúčty budou zablokovány!');
define("HOURS", 'hodiny');
define("SIGN1", 'Registrovat');
define("SIGN2", 'Vyber národ');
define("SIGN3", 'Vyber oblast');
define("SIGN4", 'Domovská stránka');
define("SIGN5", 'Registrovat');
define("SIGN6", 'Přihlásit');
define("SIGN7", 'Aktivovat účet');
define("SIGN8", 'Banka');

//QUESTS
define("QST0", 'Úkol');
define("QST1", 'Úkol');
define("QST2", 'Dřevorubec');
define("QST3", 'Postav dřevorubce na úroveň <b>5</b>');
define("QST4", 'Obilné pole');
define("QST5", 'Postav obilné pole na úroveň <b>3</b>');
define("QST6", 'Železo a hlína');
define("QST7", 'Postav hliněný a železný důl na úroveň <b>4</b>');
define("QST8", 'Hlavní budova');
define("QST9", 'Postav hlavní budovu na úroveň <b>8</b>');
define("QST10", 'Ekonomika');
define("QST11", 'Postav sklad surovin a sýpku na úroveň <b>4</b>, tržiště na úroveň <b>1</b>');
define("QST12", 'Militarizace');
define("QST13", 'Postav shromaždiště na úroveň <b>1</b>, kasárna na úroveň <b>3</b>');
define("QST14", 'Spolehlivá ochrana');
define("QST15", 'Vycvič 100 jednotek a postav hradbu na úroveň <b>8</b>');
define("QST16", 'První krev');
define("QST17", 'Získej <b>1000</b> útočných bodů.');
define("QST18", 'Viva la Socium!');
define("QST19", 'Navštiv jednu z našich skupin');
define("QST20", 'Konec úkolů!');
define("QST21", 'Momentálně tu už nejsou další úkoly. Časem přibudou, zatím přejeme dobrou hru! :)');
define("QST22", 'Výborně! Tvoje odměna:');
define("QST23", 'Na další úkol!');

//ATTACKS ETC.
define("TROOP_MOVEMENTS", 'Pohyby jednotek:');
define("ARRIVING_REINF_TROOPS", 'Přicházející podpora');
define("ARRIVING_REINF_TROOPS_SHORT", 'Podp.');
define("OWN_ATTACKING_TROOPS", 'Vlastní útočící jednotky');
define("ATTACK", 'Útok');
define("OWN_REINFORCING_TROOPS", 'Vlastní posily');
define("TROOPS_DORF", 'Jednotky:');


//LOGIN.php
define("COOKIES", 'Pro přihlášení musíš mít povolené cookies. Pokud počítač sdílíš s dalšími lidmi, odhlas se kvůli vlastnímu bezpečí po každém hraní.');
define("NAME", 'Jméno');
define("PW_FORGOTTEN", 'Zapomenuté heslo?');
define("PW_REQUEST", 'Pak si můžeš vyžádat nové, které ti přijde na e-mail.');
define("PW_GENERATE", 'Všechna pole jsou povinná');
define("EMAIL_NOT_VERIFIED", 'E-mail není ověřen!');
define("EMAIL_FOLLOW", 'Klikni na tento odkaz pro aktivaci účtu.');
define("VERIFY_EMAIL", 'Ověř e-mail.');
define("LOGIN_SERVER_START", 'Server začne za:');
define("LOGIN_FOR_GAME", 'Přihlásit');

//404.php
define("NOTHING_HERE", 'Nic tu není!');
define("WE_LOOKED", 'Hledali jsme už 404krát, ale nic jsme nenašli');

//TIME RELATED
define("CALCULATED", 'Vypočteno za');
define("SERVER_TIME", 'Čas serveru:');

//MASSMESSAGE.php
define("MASS", 'Obsah zprávy');
define("MASS_SUBJECT", 'Předmět:');
define("MASS_COLOR", 'Barva zprávy:');
define("MASS_REQUIRED", 'Všechna pole jsou povinná');
define("MASS_UNITS", 'Obrázky (jednotky):');
define("MASS_SHOWHIDE", 'Zobrazit/skrýt');
define("MASS_READ", 'Pozor: po přidání smajlíku musíš za číslo doplnit left nebo right, jinak obrázek nebude fungovat');
define("MASS_CONFIRM", 'Potvrzení');
define("MASS_REALLY", 'Opravdu chceš odeslat hromadnou zprávu?');
define("MASS_ABORT", 'Ihned přerušuji');
define("MASS_SENT", 'Hromadná zpráva byla odeslána');

// Menu items

define("GAME_TOUR", 'Prohlídka hry');

define("FORUM_LINK", "http://forum.travian.com");
define("MORE_GAMES", 'Další hry');
define("REGISTER", 'Registrovat');
define("LOGIN", 'Přihlásit');
define("MANUAL", 'Manuál');
define("TUTORIAL", 'Návod');

define("FAQ", 'Časté dotazy');
define("SPIELREGELN", 'Pravidla hry');
define("AGB", 'Podmínky');

define("LINKS", 'Odkazy');

define("INSTRUCTIONS", 'Pokyny');
define("MULTIHUNTER_PANEL", 'Panel Multihuntera');
define("UPDATE_TOP_TEN", 'Aktualizovat Top 10');

define("HELP", 'Nápověda');

// Index


//profile
define("PROFHEAD", "");
define("ACC1", 'Změnit heslo');
define("ACC2", 'Staré heslo');
define("ACC3", 'Nové heslo');
define("ACC4", 'Změnit e-mail');
define("ACC5", 'Zadej svou starou a novou e-mailovou adresu. Na obě adresy ti přijde část kódu, kterou je třeba zadat zde.');
define("ACC6", 'Starý e-mail');
define("ACC7", 'Nový e-mail');
define("ACC8", 'Zástupci účtu');
define("ACC9", 'Zástupce se může přihlásit do tvého účtu tvým jménem a svým heslem. Můžeš mít až dva zástupce.');
define("ACC10", 'Jméno zástupce');
define("ACC11", 'Nemáš žádné zástupce.');
define("ACC12", '<td class="note" colspan="2">Zástupce smažeš kliknutím na <img class="del" src="img/x.gif" title="smazat" alt="smazat"></td>');
define("ACC13", 'Smazat účet');
define("ACC14", 'Zde můžeš smazat svůj účet. Smazání se dokončí 24 hodin po zahájení. Během prvních 12 hodin ho můžeš zrušit. Administrace mazání nezrušuje.');
define("ACC15", 'Smazat účet?');
define("ACC16", 'Zadej své heslo:');
define("ACC17", 'Ano');
define("ACC18", 'Ne');
define("ACC19", 'Smazat za:');
define("ACC20", 'Účty, které zastupuješ');
define("ACC21", 'Zástupce');
define("SAVE", 'Uložit');
//menu prof
define("PROFM1", 'Přehled');
define("PROFM2", 'Profil');
define("PROFM3", 'Odkazy');
define("PROFM4", 'Účet');
define("PROFM5", 'Relace');
define("PROFM6", 'Stav');
define("PROFM7", 'Kdo');
define("PROFM8", 'Poslední aktivita');
define("PROFM9", 'Vlastník');
define("PROFM10", 'Zástupce');
define("PROFM11", 'Dual');
define("PROFM12", 'Ty');
//OVERVIEW
define("OVERVIEW1", 'Hráč');
define("OVERVIEW2", 'Podrobnosti');
define("OVERVIEW3", 'Popis');
define("OVERVIEW4", 'Pořadí');
define("OVERVIEW5", 'Národ');
define("OVERVIEW6", 'Aliance');
define("OVERVIEW7", 'Obyvatelé');
define("OVERVIEW8", 'Obyvatelé');
define("OVERVIEW9", 'Věk');
define("OVERVIEW10", 'Muž');
define("OVERVIEW11", 'Žena');
define("OVERVIEW12", 'Pohlaví');
define("OVERVIEW13", 'Místo');
define("OVERVIEW14", 'Profil');
define("OVERVIEW15", 'Napsat zprávu');
define("OVERVIEW16", 'Vesnice');
define("OVERVIEW17", 'Jméno');
define("OVERVIEW18", 'Obyvatelé');
define("OVERVIEW19", 'Souřadnice');
define("OVERVIEW20", 'Hlavní město');
define("OVERVIEW21", 'Zablokováno');
define("OVERVIEW22", 'Narozeniny');
define("OVERVIEW23", 'Leden');
define("OVERVIEW24", 'Únor');
define("OVERVIEW25", 'Březen');
define("OVERVIEW26", 'Duben');
define("OVERVIEW27", 'Květen');
define("OVERVIEW28", 'Červen');
define("OVERVIEW29", 'Červenec');
define("OVERVIEW30", 'Srpen');
define("OVERVIEW31", 'Září');
define("OVERVIEW32", 'Listopad');
define("OVERVIEW33", 'Prosinec');
define("OVERVIEW35", 'Medaile');
define("OVERVIEW36", 'Kategorie');
define("OVERVIEW37", 'Týden');
define("OVERVIEW38", 'Kód');
define("OVERVIEW39", 'Muž');
define("OVERVIEW40", 'Žena');
//medals
define("MEDAL1", 'Útočník ');
define("MEDAL2", 'Obránce ');
define("MEDAL3", 'Nejrychleji rostoucí ');
define("MEDAL4", 'Lupič ');
define("MEDAL5", 'Top 10 útočníků i obránců zároveň');
define("MEDAL6", 'Top 3 útočníků ');
define("MEDAL7", 'Top 3 obránců ');
define("MEDAL8", 'Top 3 nejrychleji rostoucích ');
define("MEDAL9", 'Top 3 lupičů ');
define("MEDAL10", 'Skokan v žebříčku ');
define("MEDAL11", 'Top 3 skokanů v žebříčku ');
define("MEDAL12", 'Top 10 útočníků ');
define("MEDAL20", 'Den');
define("DNYA", 'dne');
define("TIMES", 'opakování');
define("MEDAL15", "");
define("MEDAL16", "");
define("MEDAL17", 'Hrdina ');
define("MEDAL18", 'Obchodník ');
define("MEDAL19", 'v řadě');
define("BONUS", 'Bonus');
//statistic..zzzest'
define("STATISTIC1", 'Statistiky hráčů');
define("STATISTIC2", 'Hráč nenalezen');
define("STATISTIC3", 'Nenalezeno');
define("STATISTIC4", 'Statistiky aliancí');
define("STATISTIC5", 'Aliance nenalezena');
define("STATISTIC6", 'Body');
define("STATISTIC7", 'Statistiky aliancí (útok)');
define("STATISTIC8", 'Statistiky aliancí (obrana)');
define("STATISTIC9", 'Top');
define("STATISTIC10", 'Statistiky hrdinů');
define("STATISTIC11", 'Zkušenosti');
define("STATISTIC12", 'Hrdina nenalezen');
define("STATISTIC13", 'Statistiky hráčů (útok)');
define("STATISTIC14", 'Statistiky hráčů (obrana)');
define("STATISTIC15", 'Statistiky hráčů (Římané)');
define("STATISTIC16", 'Statistiky hráčů (Germáni)');
define("STATISTIC17", 'Statistiky hráčů (Galové)');
define("STATISTIC18", 'Suroviny');
define("STATISTIC19", 'Hráči');
define("STATISTIC20", 'Celkem registrováno');
define("STATISTIC21", 'Aktivní hráči');
define("STATISTIC22", 'Hráči online');
define("STATISTIC23", 'Národy');
define("STATISTIC24", 'Národ');
define("STATISTIC25", 'Registrováno');
define("STATISTIC26", 'Procent');
define("STATISTIC27", 'Div světa');
define("STATISTIC28", 'Hráči');
define("STATISTIC29", 'Aliance');
define("STATISTIC30", 'Hrdinové');
define("STATISTIC31", 'Obecné');
define("STATISTIC32", 'Statistiky');
define("STATISTIC33", 'nebo');
define("STATISTIC34", 'Zpět');
define("STATISTIC35", 'Vpřed');
define("STATISTIC36", 'Statistiky hráčů (Příšery)');
define("STATISTIC37", 'Obrana');
define("STATISTIC38", 'Vesnice');
define("STATISTIC39", 'Odměny');
define("STATISTIC40", 'Vesnice');
//alliance
define("ALLIANCE1", 'Možnosti');
define("ALLIANCE2", 'Přidělit pozici');
define("ALLIANCE3", 'Změnit jméno');
define("ALLIANCE4", 'Vyhodit hráče');
define("ALLIANCE5", 'Změnit popis');
define("ALLIANCE6", 'Diplomacie');
define("ALLIANCE7", 'Pozvat hráče');
define("ALLIANCE8", 'Vyloučit z aliance');
define("ALLIANCE9", 'Události aliance');
define("ALLIANCE10", 'Událost');
define("ALLIANCE11", 'Datum');
define("ALLIANCE12", 'Pro opuštění aliance musíš z bezpečnostních důvodů znovu zadat heslo.');
define("ALLIANCE13", 'Diplomacie aliance');
define("ALLIANCE14", ' nabízí konfederaci');
define("ALLIANCE15", ' nabízí pakt o neútočení');
define("ALLIANCE16", ' vyhlásit válku');
define("ALLIANCE17", 'Manuál');
define("ALLIANCE18", 'Chceš-li v popisu aliance automaticky zobrazit vztahy, napiš do popisu <span class="e">[diplomatie]</span>; možné jsou také <span class="e">[ally]</span>, <span class="e">[nap]</span> a <span class="e">[war]</span>.');
define("ALLIANCE19", 'Vlastní nabídky');
define("ALLIANCE20", 'Cizí nabídky');
define("ALLIANCE21", 'Existující vztahy');
define("ALLIANCE22", 'Konf.');
define("ALLIANCE23", 'PoN');
define("ALLIANCE24", 'Válka');
define("ALLIANCE25", 'pozice');
define("ALLIANCE26", 'Přidělit oprávnění');
define("ALLIANCE27", 'Hromadná zpráva');
define("ALLIANCE28", 'Zde můžeš hráčům své aliance přidělovat práva a pozice.');
define("ALLIANCE29", 'Útoky');
define("ALLIANCE30", 'Zde můžeš hráče vyloučit ze své aliance.');
define("ALLIANCE31", 'Pozván');
define("ALLIANCE32", 'konfederace');
define("ALLIANCE33", ' pozván hráčem ');
define("ALLIANCE34", ' odmítl pozvánku');
define("ALLIANCE35", ' smazal pozvánku pro');
define("ALLIANCE36", ' vstoupil do aliance');
define("ALLIANCE37", 'V alianci je příliš mnoho členů');
define("ALLIANCE38", 'Zakladatel aliance');
define("ALLIANCE39", 'Alianci založil');
define("ALLIANCE40", ' změnil název aliance');
define("ALLIANCE41", ' vstoupil do aliance');
define("ALLIANCE42", ' změnil popis aliance');
define("ALLIANCE43", ' změnil oprávnění');
define("ALLIANCE44", ' opustil alianci');
define("ALLIANCE45", ' nabízí konfederaci alianci');
define("ALLIANCE46", ' nabízí pakt o neútočení alianci');
define("ALLIANCE47", ' vyhlašuje válku alianci');
define("ALLIANCE48", 'Pozvánka odeslána');
define("ALLIANCE49", 'Pozvánku už jsi jim poslal');
define("ALLIANCE50", 'hacker? jo?');
define("ALLIANCE51", 'Aliance neexistuje');
//crop finder or nice place for ddos,lol
define("FINDER1", 'Zde můžeš hledat vesnice s 9 a 15 obilnými poli a obilnými oázami.');
define("FINDER2", 'Hledat');
define("FINDER3", 'Výchozí pozice');
define("FINDER4", 'Typ');
define("FINDER5", 'Oáza');
define("FINDER6", 'Neobsazeno');
define("FINDER7", 'Neobsazeno');
define("FINDER8", 'Vzdálenost');
define("FINDER9", 'Pozice');
define("FINDER10", 'Obsazeno');
define("FINDER11", 'Hledání vesnic s 9 a 15 obilnými poli');
define("FINDER12", 'Oázy');
define("FINDER13", "");
define("FINDER14", "");
define("FINDER15", "");
define("FINDER16", "");
define("FINDER17", "");
//otpravka figni
define("OTPRAV1", 'Poslat jednotky');
define("OTPRAV2", 'Posily');
define("OTPRAV3", 'Útok');
define("OTPRAV4", 'Loupež');
define("OTPRAV5", 'Zvěd');
define("OTPRAV6", 'Podpora pro');
define("OTPRAV7", 'Útok na');
define("OTPRAV8", 'Loupež na');
define("OTPRAV9", 'Zvěd');
define("OTPRAV10", 'Prozkoumat suroviny a jednotky<br>');
define("OTPRAV11", 'Prozkoumat jednotky a obranu');
define("OTPRAV12", 'Cíl');
define("OTPRAV13", 'Náhodně');
define("OTPRAV14", 'Pořádáš pivní slavnost. Jen náhodné cíle.');
define("OTPRAV15", '(bude ostřelováno katapulty)');
define("OTPRAV16", 'Artefakt');
define("OTPRAV17", 'Dorazilo');
define("OTPRAV18", 'Hráč má momentálně ochranu začátečníka');
define("OTPRAV19", 'Jakýkoli útok na jiného hráče ukončí tvou ochranu začátečníka');
define("OTPRAV20", "");

//Artefacti
define("ART1", 'Malé tajemství architektů');
define("ART2", 'Velké tajemství architektů');
define("ART3", 'Jedinečné tajemství architektů');
define("ART4", 'Malé titánské boty');
define("ART5", 'Velké titánské boty');
define("ART6", 'Jedinečné boty titánů');
define("ART7", 'Malé orlí oči');
define("ART8", 'Velké orlí oči');
define("ART9", 'Jedinečné orlí oči');
define("ART10", 'Malý talent cvičitele');
define("ART11", 'Velký talent cvičitele');
define("ART12", 'Jedinečný talent cvičitelů');
define("ART13", 'Plán skladování');
define("ART14", 'Stavební plán Divu světa');
define("ART15", 'Přístup k budově.');
define("ART16", 'Tento artefakt chrání tvou vesnici před katapulty a beranidly. Díky němu jsou všechny budovy i hradba odolnější.');
define("ART17", 'Tento artefakt zrychluje pohyb tvých jednotek.');
define("ART18", 'Tento artefakt posiluje tvé zvědy. Bonus dostanou všichni zvědové – ve vesnici i ti vyslaní na průzkum jiné vesnice. Při útoku na tebe navíc na shromaždišti uvidíš typ nepřátelských jednotek.');
define("ART19", 'Artefakt zkracuje dobu výcviku jednotek v kasárnách, stájích a dílně.');
define("ART20", 'Tento artefakt ti umožní postavit velký sklad a velkou sýpku. Artefakt je potřeba i pro rozšiřování obou budov.');
define("ART21", 'Artefakt potřebný ke stavbě Divu světa.');
//karta vrode
define("BAN", 'Zablokováno');
define("KAR1", 'Opuštěné údolí');
define("KAR2", 'Neobsazená oáza');
define("KAR3", 'Obsazená oáza');
define("KAR4", 'Nejsou k dispozici<br>žádné informace.');
define("KAR5", 'Založit novou vesnici');
define("KAR6", 'Kulturní body');
define("KAR7", 'Osadníci');
define("KAR8", 'postav shromaždiště');
define("KAR9", 'Poslat jednotky');
define("KAR10", 'ochrana začátečníka');
define("KAR11", 'Poslat obchodníky');
define("KAR12", 'Přidat do seznamu farem');
define("KAR13", 'Přidat do seznamu farem (už je tam)');
define("KAR14", 'Přidat do seznamu farem (limit dosažen)');
define("KAR15", 'Rozložení polí');
define("KAR16", 'Vystředit mapu');
define("KAR17", 'postav tržiště');
define("KAR18", 'Mapa');
define("KAR19", 'Hledat');
define("KAR20", 'Seznam oblíbených cílů');
define("KAR21", 'Pozice');
define("KAR22", 'Poslední útok');
define("KAR23", 'Mrtvý');
define("KAR24", 'Kořist');

//ss6Ilo4ki EBAN6IE:D
define("LINK1", 'Odkaz');
define("LINK2", 'Adresa');
define("LINK3", "");
define("LINK4", "");
//pravila anti-gad6I
define("RULES1", 'Pravidla hry');
define("RULES2", 'Tato pravidla hry stanovila administrace serveru. V případě zablokování účtu i pro lepší pochopení zakázaných činností se obrať na Multihuntera (viz §3).<br>
Obcházení pravidel je jejich porušením. Pravidla jsou jednotná a závazná pro všechny hráče, včetně těch, kteří se chystají smazat nebo už mažou svůj účet.');
define("RULES3", "§ 1 Учетная запись");
define("RULES4", '§1.1. Každý hráč smí na jednom herním serveru vlastnit pouze jeden účet.');
define("RULES5", '§1.2. Vlastníkem herního účtu je hráč, jehož e-mailová adresa je uvedena v nastavení účtu. E-mailovou adresu lze změnit v profilu účtu (Profil » Účet).');
define("RULES6", '§1.3. Předávat heslo k účtu lidem, kteří hrají na stejném serveru, je zakázáno. Zakázáno je také přihlašovat se na účty jiných hráčů pomocí hesla vlastníka. Každá taková činnost se považuje za vlastnictví dvou či více účtů na jednom serveru. ');
define("RULES7", 'Hra více hráčů na jednom účtu je povolena za podmínky, že žádný z nich nevlastní ani nehraje jiný účet na stejném serveru. ');
define("RULES8", 'Zakázáno je rovněž používat stejná hesla u účtů při sdílení počítače a/nebo zastupování.');
define("RULES9", '§1.4. Vlastník účtu nese plnou odpovědnost za vše, co se s jeho účtem děje.');
define("RULES10", '§ 2 Herní etika');
define("RULES11", '§2.1. Prodej a nákup účtů, jednotek, surovin nebo služeb hráčů je zakázán. Týká se to i času investovaného do účtu.');
define("RULES12", '§2.2. Urážky, ponižování a jiné nevhodné výroky o ostatních hráčích jsou v jakékoli formě a kdekoli ve hře zakázány. Zakázáno je i používání vulgarismů (včetně skrytých) a jakékoli výhrůžky týkající se skutečného života.');
define("RULES13", '§2.3. Je zakázáno vydávat se za oficiální orgány serveru a používat jména či názvy, které jsou urážlivé nebo nevhodné z hlediska mravních a politických norem.');
define("RULES14", '§2.4. Profily hráčů a aliancí lze psát pouze v jazycích serveru.');
define("RULES15", "§2.5. Реклама любого рода, спам или цепочные письма запрещены.");
define("RULES16", '§2.6. Zveřejňování herních zpráv a e-mailů hráčů, multihunterů nebo správců komunity je zakázáno.');
define("RULES17", '§2.7. Je zakázáno navádět hráče k porušování pravidel, ke smazání účtu, k předání hesla, ke společné hře na jednom účtu, vyžadovat předání účtu nebo jmenování zástupcem.');
define("RULES18", '§2.8. Zneužívání chyb hry k získání jakékoli výhody je zakázáno. Zakázáno je i používání programů, které automatizují nebo napodobují činnost hráče nebo jakkoli mění vzhled hry. Výjimkou jsou grafické balíčky.');
define("RULES19", '§ 3 Správní ustanovení');
define("RULES20", '§3.1. Způsob potrestání za porušení pravidel určují multihunteři a/nebo administrace. Trest vždy převýší výhodu získanou porušením. Bez výjimky budou potrestány všechny účty, které se na porušení pravidel podílely. Suroviny, budovy, vesnice ani jednotky ztracené během zablokování se nenahrazují. Zlato ani čas Plus ztracený v důsledku zablokování se nevrací. Hráči, kteří kupují zlato, nemají při vyřizování zpráv a určování trestu žádné výhody.');
define("RULES21", '§3.2. Multihunter je jedinou kontaktní osobou ve věci porušení pravidel. Hráči mohou předložit argumenty na svou obhajobu zprávou multihunterovi. Pokud s jeho rozhodnutím nesouhlasí, mohou se obrátit na administraci.');
define("RULES22", 'Veškeré otázky porušení pravidel a trestů řeší tým serveru pouze s vlastníkem účtu.');
define("RULES23", '§3.3. Tým serveru si vyhrazuje právo kdykoli změnit pravidla hry.');
define("PERC", 'Procent');

$lang['header'] = array(
    0 => 'Suroviny',
    1 => 'Centrum vesnice',
    2 => 'Mapa',
    3 => 'Statistiky',
    4 => 'Hlášení',
    5 => 'Zprávy',
    6 => 'Menu Plus');

$lang['buildings'] = array(
    1 => 'Dřevorubec',
    2 => 'Hliněný důl',
    3 => 'Železný důl',
    4 => 'Obilné pole',
    5 => 'Pila',
    6 => 'Cihelna',
    7 => 'Slévárna',
    8 => 'Mlýn',
    9 => 'Pekárna',
    10 => 'Sklad surovin',
    11 => 'Sýpka',
    12 => 'Kovárna',
    14 => 'Turnajové hřiště',
    15 => 'Hlavní budova',
    16 => 'Shromaždiště',
    17 => 'Tržiště',
    18 => 'Ambasáda',
    19 => 'Kasárna',
    20 => 'Stáj',
    21 => 'Dílna',
    22 => 'Akademie',
    23 => 'Úkryt',
    24 => 'Radnice',
    25 => 'Rezidence',
    26 => 'Palác',
    27 => 'Pokladnice',
    28 => 'Obchodní kancelář',
    29 => 'Velká kasárna',
    30 => 'Velká stáj',
    31 => 'Městská zeď',
    32 => 'Zemní val',
    33 => 'Palisáda',
    34 => 'Kameník',
    35 => 'Pivovar',
    36 => 'Pastičkář',
    37 => 'Hostinec',
    38 => 'Velký sklad surovin',
    39 => 'Velká sýpka',
    40 => 'Div světa',
    41 => 'Koňské napajedlo',
    42 => 'Kamenná zeď',
    43 => 'Provizorní zeď',
    44 => 'Velitelství',
    45 => 'Vodárna');

$lang['desc'][1] = array(0 => 'Dřevorubec kácí stromy a vyrábí dřevo. Čím víc ho rozšíříš, tím víc dřeva vyrobí.');
$lang['desc'][2] = array(0 => 'Zde se těží hlína. Zvýšením úrovně zvýšíš produkci hlíny.');
$lang['desc'][3] = array(0 => 'Zde horníci těží vzácné železo. Zvýšením úrovně dolu zvýšíš produkci železa.');
$lang['desc'][4] = array(0 => 'Zde se pěstuje jídlo pro tvé obyvatelstvo. Zvýšením úrovně pole zvýšíš produkci obilí.');
$lang['desc'][5] = array(0 => 'Na pile se dřevo dále zpracovává. Podle úrovně pily lze produkci dřeva zvýšit až o 25 procent.');
$lang['desc'][6] = array(0 => 'V cihelně se z hlíny vyrábějí cihly. Podle úrovně cihelny lze produkci hlíny zvýšit až o 25 procent.');
$lang['desc'][7] = array(0 => 'Ve slévárně se zpracovává železo. Podle úrovně slévárny lze produkci železa zvýšit až o 25 procent.');
$lang['desc'][8] = array(0 => 'Ve mlýně se obilí mele na mouku. Podle úrovně mlýna lze produkci obilí zvýšit až o 25 procent.');
$lang['desc'][9] = array(0 => 'V pekárně se z mouky peče chléb. Podle úrovně lze produkci obilí zvýšit spolu s mlýnem až o 50 procent.');
$lang['desc'][10] = array(0 => 'Ve skladu surovin se skladuje dřevo, hlína a železo. Zvýšením úrovně zvětšíš kapacitu skladu.');
$lang['desc'][11] = array(0 => 'V sýpce se skladuje obilí z obilných polí. Čím vyšší úroveň, tím větší kapacita.');
$lang['desc'][12] = array(0 => 'Ve zbrojnici můžeš vylepšovat zbraně svých vojáků. S rozvojem budovy roste maximální úroveň vylepšení zbraní.');
$lang['desc'][13] = array(0 => 'V kovárně můžeš vylepšovat zbroj svých vojáků. S rozvojem budovy roste maximální úroveň vylepšení zbroje.');
$lang['desc'][14] = array(0 => 'Na turnajovém hřišti trénují tví vojáci vytrvalost. S jeho rozvojem roste rychlost jednotek na vzdálenost nad 20 polí.');
$lang['desc'][15] = array(0 => 'V hlavní budově bydlí stavitelé vesnice. Čím vyšší úroveň, tím rychleji se stavějí a rozšiřují ostatní budovy.');
$lang['desc'][16] = array(0 => 'Na shromaždišti se scházejí jednotky vesnice. Odtud je můžeš vyslat jako podporu, na loupež nebo na dobytí jiné vesnice.');
$lang['desc'][17] = array(0 => 'Na tržišti můžeš obchodovat se surovinami s ostatními hráči. Čím vyšší úroveň, tím víc obchodníků máš k dispozici.');
$lang['desc'][18] = array(0 => 'Ambasáda slouží k diplomatickým vztahům. Na úrovni 1 můžeš vstoupit do aliance, na úrovni 3 můžeš alianci i založit.');
$lang['desc'][19] = array(0 => 'V kasárnách se cvičí pěchota. S rozvojem kasáren se zkracuje doba výcviku. ');
$lang['desc'][20] = array(0 => 'Ve stáji se cvičí veškerá jízda. S rozvojem stáje se zkracuje doba výcviku. ');
$lang['desc'][21] = array(0 => 'V dílně se vyrábějí obléhací stroje jako katapulty a beranidla. S rozvojem dílny se zkracuje doba výroby. ');
$lang['desc'][22] = array(0 => 'V akademii se zkoumají nové druhy jednotek. S rozvojem akademie přibývají jednotky, které lze zkoumat. ');
$lang['desc'][23] = array(0 => 'Při útoku na vesnici ukryjí obyvatelé část surovin ze skladu v úkrytu. Tyto suroviny útočníci nemohou ukrást. ');
$lang['desc'][24] = array(0 => 'Na radnici můžeš pro obyvatele pořádat velkolepé slavnosti, a zvyšovat tak počet kulturních bodů. ');
$lang['desc'][25] = array(0 => 'Rezidence je malý palác, kde bydlí král nebo královna při návštěvě vesnice. Rezidence chrání vesnici před nepřáteli, kteří ji chtějí dobýt. ');
$lang['desc'][26] = array(0 => 'V paláci bydlí král nebo královna říše. Palác určuje hlavní město říše, a proto může stát jen jeden. ');
$lang['desc'][27] = array(0 => 'V pokladnici se uchovávají poklady tvé říše. Každá pokladnice má místo pro jeden artefakt. Účinek artefaktu se projeví až 24 hodin (na normálním serveru) nebo 12 hodin (na rychlém serveru) po jeho získání. ');
$lang['desc'][28] = array(0 => 'V obchodní kanceláři můžeš vylepšit vozy a vyšlechtit silnější koně. S jejím rozvojem roste množství surovin, které unese jeden obchodník. ');
$lang['desc'][29] = array(0 => 'Velká kasárna umožňují cvičit vojáky, výcvik však stojí trojnásobek. S velkými kasárnami a kasárnami vycvičíš za stejný čas dvakrát víc vojáků. Velká kasárna nelze postavit v hlavním městě. ');
$lang['desc'][30] = array(0 => 'Velká stáj umožňuje cvičit jízdu, výcvik však stojí trojnásobek. Velkou stáj nelze postavit v hlavním městě. ');
$lang['desc'][31] = array(0 => 'Městská zeď chrání vesnici před útoky. Čím vyšší úroveň zdi, tím účinnější je obrana tvé vesnice. ');
$lang['desc'][32] = array(0 => 'Hradba chrání tvou vesnici před útoky. Čím vyšší hradba, tím snáz vesnici ubráníš před loupežemi a útoky nepřátel. ');
$lang['desc'][33] = array(0 => 'Hradba chrání tvou vesnici před útoky. Čím vyšší hradba, tím snáz vesnici ubráníš před loupežemi a útoky nepřátel. ');
$lang['desc'][34] = array(0 => 'Kameník je odborník na opracování kamene. S rozvojem budovy roste odolnost všech budov ve vesnici. ');
$lang['desc'][35] = array(0 => 'V pivovaru se pro lid vaří lahodné nápoje. Tví vojáci jsou díky nim v boji statečnější a silnější. Má to ale i stinnou stránku: tví vůdci nejsou v nepřátelských vesnicích tak výmluvní a katapulty míří jen na náhodné cíle. Pivovar lze postavit jen v hlavním městě. ');
$lang['desc'][36] = array(0 => 'Pastičkář chrání vesnici dobře ukrytými pastmi. Nepřátelé mohou být zajati a už tvé vesnici neublíží. Zajaté jednotky jedí obilí vesnice, ze které pocházejí. ');

$lang['desc'][37] = array(0 => 'V hrdinském dvoře získáš přehled o okolních oázách. Od úrovně 10 můžeš svým hrdinou obsazovat oázy a zvýšit tak produkci surovin vesnice. ');
$lang['desc'][38] = array(0 => 'Velký sklad pojme 3× víc surovin než běžný sklad. ');
$lang['desc'][39] = array(0 => 'Velká sýpka pojme 3× víc obilí než běžná sýpka. ');
$lang['desc'][40] = array(0 => 'Div světa je vrcholem civilizace. Jen nejmocnější a nejbohatší říše dokáže takové dílo postavit a ubránit ho před útoky nepřátel. ');
$lang['desc'][41] = array(0 => 'Díky napajedlu se tvým koním daří lépe, výcvik jízdy je rychlejší a jízda spotřebuje méně obilí. ');
$lang['desc'][42] = array(0 => 'Kamennou zdí ochráníš vesnici před barbarskými hordami nepřátel. Čím vyšší úroveň, tím větší bonus k obraně tvých jednotek.<br>Kamennou zeď mohou stavět jen Egypťané, její obranný bonus odpovídá galské palisádě a odolnost germánskému zemnímu valu.');
$lang['desc'][43] = array(0 => 'Provizorní zdí ochráníš vesnici před barbarskými hordami nepřátel. Čím vyšší úroveň, tím větší bonus k obraně tvých jednotek.<br>Provizorní zeď mohou stavět jen Hunové, její obranný bonus odpovídá germánskému zemnímu valu a odolnost římské městské zdi.');
$lang['desc'][44] = array(0 => 'Velitelství chrání vesnici před dobytím nepřítelem. V každé vesnici můžeš postavit jedno velitelství. Lze v něm cvičit osadníky a senátory/náčelníky/stařešiny/nomarchy/logady.');
$lang['desc'][45] = array(0 => 'Vodárnou řídíš přítok vody do svých oáz. Pomáhá to nejen růstu stromů a obilí, ale i lomům a dolům, které zásobuje vodou pro dělníky a pro dopravu surovin.<br>Budova zvyšuje bonus všech připojených oáz. Na úrovni 20 účinek oáz zdvojnásobí.<br>Vodárnu mohou stavět jen Egypťané.');

$lang['descs'][5] = array(0 => array(1, 10), array(15, 5));
$lang['descs'][6] = array(0 => array(2, 10), array(15, 5));
$lang['descs'][7] = array(0 => array(3, 10), array(15, 5));
$lang['descs'][8] = array(0 => array(4, 5));
$lang['descs'][9] = array(0 => array(4, 10), array(15, 5));
$lang['descs'][12] = array(0 => array(22, 1), array(15, 3));
$lang['descs'][13] = array(0 => array(22, 1), array(15, 3));
$lang['descs'][14] = array(0 => array(16, 15));
$lang['descs'][17] = array(0 => array(15, 3), array(10, 1), array(11, 1));
$lang['descs'][19] = array(0 => array(16, 1), array(15, 3));
$lang['descs'][20] = array(0 => array(22, 5), array(12, 3));
$lang['descs'][21] = array(0 => array(22, 10), array(15, 5));
$lang['descs'][22] = array(0 => array(19, 3), array(15, 3));
$lang['descs'][24] = array(0 => array(15, 10), array(22, 10));
$lang['descs'][25] = array(0 => array(15, 5));
$lang['descs'][26] = array(0 => array(18, 1), array(15, 5));
$lang['descs'][27] = array(0 => array(15, 10));
$lang['descs'][28] = array(0 => array(17, 20), array(20, 10));
$lang['descs'][29] = array(0 => array(19, 20));
$lang['descs'][30] = array(0 => array(20, 20));
$lang['descs'][34] = array(0 => array(26, 3), array(15, 5));
$lang['descs'][35] = array(0 => array(11, 20), array(16, 10));
$lang['descs'][36] = array(array(16, 1));
$lang['descs'][37] = array(0 => array(16, 1), array(15, 3));
$lang['descs'][38] = array(0 => array(15, 10));
$lang['descs'][39] = array(0 => array(15, 10));
$lang['descs'][41] = array(0 => array(20, 20), array(16, 10));
$lang['fields'] = array(
    0 => '&nbsp;úroveň',
    1 => 'Dřevorubec úroveň',
    2 => 'Hliněný důl úroveň ',
    3 => 'Železný důl úroveň',
    4 => 'Obilné pole úroveň',
    5 => 'Наружняя строительная площадка',
    6 => 'Строительная площадка',
    7 => 'Строительная площадка пункта сбора');

$lang['npc'] = array(
    0 => 'NPC obchodník');

$lang['upgrade'] = array(
    0 => 'Budova je už na maximální úrovni',
    1 => 'Staví se maximální úroveň budovy',
    2 => 'Здание будет снесено',
    3 => '<b>Cena</b> stavby na úroveň&nbsp;',
    4 => 'Рабочие заняты.',
    5 => 'Nedostatek jídla. Rozšiř obilná pole.',
    6 => 'Постройте склад.',
    7 => 'Постройте амбар.',
    8 => 'Dost surovin bude&nbsp;',
    9 => '&nbsp;v&nbsp;&nbsp;',
    10 => 'Rozšířit na úroveň ',
    11 => 'сегодня',
    12 => 'zítra');

$lang['movement'] = array(
    0 => 'v&nbsp;');

$lang['troops'] = array(
    0 => 'нет',
    1 => 'Герой');

// Player statics


define("PLAYERS_ACTIVE", 'Aktivní hráči');
define("PLAYERS_ONLINE", 'Hráči online');
define("PLAYERS", 'Hráči');
define("ACTIVE", 'Aktivní');
define("ONLINE", 'Online');


//NOTICES
define("REPORT_SUBJECT", 'Předmět:');
define("REPORT_ATTACKER", 'Útočník');
define("REPORT_DEFENDER", 'Obránce');
define("REPORT_RESOURCES", 'Suroviny');
define("REPORT_FROM_VIL", 'z vesnice');
define("REPORT_FROM_ALLY", 'z aliance');
define("REPORT_SENT", 'Odesláno:');
define("REPORT_SENDER", 'Odesílatel');
define("REPORT_RECEIVER", 'Příjemce');
define("REPORT_AT", 'V');
define("REPORT_TO", 'Do');
define("REPORT_SEND_RES", 'poslat suroviny');
define("REPORT_DEL_BTN", 'smazat hlášení');
define("REPORT_DEL_QST", 'Opravdu chceš hlášení smazat?');
define("REPORT_WARSIM", 'simulátor boje');
define("REPORT_ATK_AGAIN", 'návrat z útoku');
define("REPORT_TROOPS", 'Jednotky');
define("REPORT_REINF", 'Podpora');
define("REPORT_CASUALTIES", 'Ztráty');
define("REPORT_INFORMATION", 'informace');
define("REPORT_BOUNTY", 'Kořist');
define("REPORT_CLOCK", 'Čas');
define("REPORT_UPKEEP", 'Údržba');
define("REPORT_PER_HOURS", 'za hodinu');
define("REPORT_SEND_REINF_TO", 'poslat podporu do vesnice');
define("REPORT_NO", 'Nejsou k dispozici žádná hlášení.');
define("REPORT1", ' zvědové ');
define("REPORT2", ' útoky ');


define("NGZ2", 'Současná doba stavby');
define("NGZ3", 'Doba stavby na úrovni');


//CTENA
define("C1", ' Městská zeď úroveň');
define("C2", ' Městskou zdí ochráníš vesnici před barbarskými hordami nepřátel. Čím vyšší úroveň zdi, tím větší bonus k obraně tvých jednotek.');
define("C3", 'Bez obranného bonusu');
define("C4", 'Obranný bonus na úrovni');
define("C5", " ");
define("C6", " ");
define("C7", 'Zemní val úroveň');
define("C8", 'Palisáda úroveň');
define("C9", 'Městská zeď úroveň');

//CKLAD
define("CK0", 'Sklad surovin úroveň ');
define("CK", ' Ve skladu surovin se skladuje dřevo, hlína a železo. Čím vyšší úroveň, tím větší kapacita skladu. ');


//AMBAR
define("AM", ' Sýpka úroveň ');
define("AM1", ' V sýpce se skladuje obilí z obilných polí. Čím vyšší úroveň, tím větší kapacita.');

define("AM4", 'Kapacita na úrovni');

define("CAPACITY", 'Kapacita');
define("CAPACITYA", 'Kapacita na úrovni');


//upgrade.php
define("UPG0", 'Budova je na maximální úrovni.');
define("UPG1", 'Stavíme maximální úroveň.');
define("UPG2", 'Budova se bourá.');
define("UPG3", 'Cena');
define("UPG4", 'rozšíření na úroveň');
define("UPG5", 'Všichni stavitelé jsou zaneprázdnění.');
define("UPG6", 'Všichni stavitelé jsou zaneprázdnění. (Fronta)');
define("UPG7", 'Nedostatek jídla. Rozšiř obilná pole.');
define("UPG8", 'Rozšiř sklad surovin.');
define("UPG9", 'Rozšiř sýpku. ');
define("UPG10", 'Dost surovin – nikdy');
define("UPG11", 'Rozšířit na úroveň ');
define("UPG12", 'Postavit pomocí stavitele ');


//отпраinка inойск
define("nap0", 'Posily');
define("nap1", 'Normální útok');
define("nap2", 'Loupež');


define("PY1", 'Pohyby do vesnice ');
define("PY2", 'Přehled ');
define("PY3", 'Poslat jednotky ');
define("PY5", 'Jednotky ve vesnici ');
define("PY6", 'Vesnice');
define("PY7", 'Vlastní jednotky');
define("PY8", 'Jednotky ');
define("PY9", 'Výživa ');
define("PY10", 'za hodinu ');
define("PY11", 'Poslat zpět ');
define("PY12", 'Jednotky v jiných vesnicích ');
define("PY13", 'Podpora pro ');
define("PY14", 'Stáhnout zpět ');
define("PY15", 'Pohyby tvých jednotek ');
define("PY16", 'Únik');
define("PY17", 'Seznam farem');
define("PY18", 'Oáza');
define("PY19", 'Jednotky v oázách ');
define("GOLDC", 'Zlatý klub');
//KAZARMA
define("KA", ' Kasárna úroveň ');
define("KA1", ' V kasárnách se cvičí veškerá pěchota. Čím vyšší úroveň kasáren, tím rychleji se jednotky cvičí.');
define("KA2", 'Kasárna');
define("KA3", 'Výcvik může začít po dokončení kasáren.');

//RYNOK
define("RY", ' Tržiště úroveň ');
define("RY1", ' Na tržišti můžeš obchodovat s ostatními hráči. Čím vyšší úroveň, tím více surovin lze přepravovat najednou.');


//DVOREZ
define("DV", ' Palác úroveň ');
define("DV1", ' V paláci bydlí vládce národa. Palác lze postavit jen v hlavním městě. Čím vyšší úroveň, tím těžší je pro nepřátele vesnici dobýt.');
define("dvrc0", 'heslo je špatně');
define("dvrc1", 'K založení nové vesnice potřebuješ palác na úrovni 10, 15 nebo 20 a 3 osadníky. K dobytí nové vesnice potřebuješ palác na úrovni 10, 15 nebo 20 a senátora, náčelníka nebo stařešinu.');
define("dvrc2", 'Toto je tvé hlavní město');
define("dvrc3", 'Opravdu chceš změnit hlavní město?');
define("dvrc4", 'Tuto akci nelze vrátit!');
define("dvrc5", 'Z bezpečnostních důvodů zadej pro potvrzení své heslo:');
define("dvrc6", 'Změnit');
define("dvrc7", 'Palác se staví');
define("dvrc8", 'Heslo:');
define("dvrc9", 'Jméno');
define("dvrc10", 'Množství');
define("dvrc11", 'Max');

//POSOLbSTVO
define("posl0", 'Aliance');
define("posl1", 'Zkratka');
define("posl2", 'Jméno');
define("posl3", 'k alianci');
define("posl4", 'Pozvánky');
define("posl5", 'Přijmout');
define("posl6", 'Žádné pozvánky nejsou k dispozici');
define("posl7", 'založit alianci');
define("posl8", 'Vytvořit');

//masterskaya
define("mastr0", 'Jednotky je třeba vyzkoumat');
define("mastr1", 'Cvičit');
define("mastr2", 'Výcvik');
define("mastr3", 'Trvání');
define("mastr4", 'Skončeno');
define("mastr5", 'Celkem');

//REZA
define("RE", ' Rezidence úroveň ');
define("RE1", ' Rezidence je malý palác, kde bydlí král při své návštěvě. Rezidence chrání vesnici před dobytím nepřítelem. ');
define("RE2", 'Toto je tvé hlavní město');
define("RE3", 'Rezidence');
define("RE4", 'Cvičit');
define("RE5", 'Kulturní body');
define("RE6", 'Loajalita');
define("RE7", 'Expanze');
define("RE8", 'Pro rozšiřování říše potřebuješ kulturní body. Získáváš je postupně ze svých budov – a čím vyšší úrovně, tím rychleji.');
define("RE9", 'Produkce této vesnice:');
define("RE10", 'Kulturní body za den');
define("RE11", 'Produkce všech vesnic:');
define("RE12", 'Tvé vesnice vyprodukovaly');
define("RE13", 'bodů celkem. K založení nebo dobytí nové vesnice potřebuješ');
define("RE14", 'body');
define("RE15", 'Útoky senátorů, kmenových vůdců nebo náčelníků lze snížit loajalitu vesnice. Klesne-li na nulu, vesnice přejde do říše útočníka. Loajalita této vesnice je momentálně');
define("RE16", 'Vesnice založené nebo dobyté touto vesnicí');
define("RE17", 'Vesnice');
define("RE18", 'Hráč');
define("RE19", 'Obyvatelé');
define("RE20", 'Souřadnice');
define("RE21", 'Datum');
define("RE22", 'Tato vesnice zatím nezaložila ani nedobyla žádnou jinou vesnici.');
define("RE23", 'Ve výcviku');
define("RE24", 'Trvání');
define("RE25", 'Hotovo');
define("RE26", 'Cvičit');
define("RE27", 'K založení nové vesnice potřebuješ rezidenci na úrovni 10 nebo 20 a 3 osadníky. K dobytí nové vesnice potřebuješ rezidenci na úrovni 10 nebo 20 a senátora, náčelníka nebo stařešinu.');

//AKADEM
define("AK", ' Akademie úroveň ');
define("AK1", ' V akademii se zkoumají nové druhy jednotek. Zvýšením úrovně můžeš zkoumat lepší jednotky. ');
define("AK2", 'Akademie');
define("AK3", 'Nejsou k dispozici žádné výzkumy');
define("AK4", 'Akce');
define("AK5", 'Předpoklady');
define("AK6", 'zobrazit více');
define("AK7", 'skrýt více');
define("AK8", 'Vyzkoumat');

//MELNIZA
define("ME", ' Mlýn úroveň ');
define("ME1", ' Ve mlýně se obilí mele na mouku. Podle úrovně může mlýn zvýšit produkci obilí až o 25 procent ');
define("ME2", "");
define("ME3", "");


//KON
define("KO", ' Stáj úroveň ');
define("KO1", ' Ve stáji se cvičí veškerá jízda. Čím vyšší úroveň, tím rychleji se jednotky cvičí. ');
define("KO2", 'Nejsou dostupné žádné jednotky. Vyzkoumej je v akademii');
define("KO3", 'Výcvik může začít po dokončení velké stáje.');
define("KZ333", 'Výcvik může začít po dokončení velkých kasáren.');


//GLAVNOE ZDANIE
define("gz0", 'Zbourání budovy:');
define("gz1", 'Pokud budovu už nepotřebuješ, můžeš nařídit její zbourání.');
define("gz2", 'Zbourání');
define("gz3", 'Okamžitě dokončit všechny stavby a výzkumy v této vesnici za 2 zlaté?');
define("gz4", 'Zbourat budovu');

//COKRA
define("CO", ' Pokladnice úroveň ');
define("CO1", ' V pokladnici se uchovávají poklady tvé říše. Tvá pokladnice má místo jen pro jeden poklad. Po získání artefaktu trvá 24 hodin na normálním serveru a 12 hodin na 3× rychlém serveru, než začne působit.');
define("CO2", "");
define("CO3", "");
define("CO4", "");

//GHYGYN
define("GH", ' Slévárna úroveň ');
define("GH1", ' Ve slévárně se taví železo. Podle úrovně může slévárna zvýšit produkci železa až o 25 procent.');
define("GH2", 'Aktuální bonus železa');
define("GH3", 'Bonus železa na úrovni');
define("GH4", "");

//KIRPIGH
define("KI", ' Cihelna úroveň ');
define("KI1", ' V cihelně se z hlíny pálí cihly. Podle úrovně může cihelna zvýšit produkci hlíny až o 25 procent.');
define("KI2", 'Aktuální bonus hlíny');
define("KI3", 'Bonus hlíny na úrovni');
define("KI4", "");
define("KI5", "");
define("KI6", "");

define("CURB", 'Aktuální bonus');
define("CURBL", 'Bonus na úrovni');
define("NOTDONEU", 'Stavba ještě není dokončená.');
define("SPEEDB", 'Aktuální bonus rychlosti');
define("SPEEDBL", 'Bonus rychlosti na úrovni');

//ратуша
define("ratusha0", 'Slavnosti lze pořádat po dokončení radnice.');
define("ratusha1", 'Slavnosti');
define("ratusha2", 'Akce');
define("ratusha3", 'Slavnost');
define("ratusha4", 'probíhá');
define("ratusha5", 'Produkce obilí je záporná, potřebných surovin nikdy nedosáhneš');
define("ratusha6", 'Příliš málo');
define("ratusha7", 'suroviny');
define("ratusha8", 'pořádat');
define("ratusha9", 'Velká slavnost (2000 kulturních bodů)');
define("ratusha10", 'Dost surovin');
define("ratusha11", 'kulturní body');
define("ratusha12", 'Trvání');
define("ratusha13", 'Dokončit');
define("ratusha14", 'Malá slavnost');
define("ratusha15", "");
define("ratusha16", "");
define("ratusha17", "");
define("ratusha18", "");
define("ratusha19", "");
define("ratusha20", "");


//ARENA
define("AR", ' Turnajové hřiště úroveň ');
define("AR1", ' Na turnajovém hřišti si tvé jednotky zlepšují výdrž. Čím vyšší úroveň, tím rychleji se pohybují na vzdálenost nad 30 polí.');
define("AR2", "");
define("AR3", "");
define("AR4", "");

//MASTERSKAI
define("MA", ' Dílna úroveň ');
define("MA1", 'V dílně se vyrábějí obléhací stroje jako katapulty a beranidla. Čím vyšší úroveň, tím rychleji se vyrábějí. ');
define("MA2", "");
define("MA3", "");
define("MA4", "");

//PEKARNIA
define("PE", ' Pekárna úroveň ');
define("PE1", ' V pekárně se z mouky z mlýna peče chléb. Spolu s mlýnem může pekárna zvýšit produkci obilí až o 50 procent. ');
define("PE2", "");
define("PE3", "");
define("PE4", "");

//RATYSHA
define("RAT", ' Radnice úroveň ');
define("RAT1", ' Na radnici můžeš pro své obyvatele pořádat velkolepé slavnosti, které zvyšují počet kulturních bodů.');
define("RAT2", "");
define("RAT3", "");
define("RAT4", "");

//PALATA
define("PAL", ' Obchodní kancelář úroveň ');
define("PAL1", ' V obchodní kanceláři lze vylepšit vozy obchodníků. Čím vyšší úroveň, tím víc každý obchodník unese. ');
define("PAL2", "");
define("PAL3", "");
define("PAL4", "");

//VODOPOI
define("VO", ' Koňské napajedlo úroveň ');
define("VO1", ' Koňské napajedlo pečuje o tvé koně, snižuje jejich spotřebu obilí a zrychluje jejich výcvik. S každou úrovní se zkracuje doba výcviku ve stáji. ');
define("VO2", "");
define("VO3", "");
define("VO4", "");


//BA
define("BA", ' Velká sýpka úroveň ');
define("BA1", ' V sýpce se skladuje obilí z tvých polí. Velká sýpka nabízí ještě víc místa, kde obilí zůstane suché, v bezpečí a bez červů. ');
define("BA2", "");
define("BA3", "");
define("BA4", "");

//BK
define("BK", ' Velký sklad úroveň ');
define("BK1", ' Ve skladu se skladuje dřevo, hlína a železo. Velký sklad nabízí ještě víc místa, kde zůstanou tvé suroviny suché a v bezpečí. ');
define("BK2", "");
define("BK3", "");
define("BK4", "");
define("BK5", "");

//PIVO
define("PI", ' Pivovar úroveň ');
define("PI1", ' V pivovaru se vaří lahodná medovina, kterou pak vojáci popíjejí během oslav. ');
define("PI2", "");
define("PI3", "");
define("PI4", "");

//CHYDO
define("CHY", ' Div světa úroveň ');
define("CHY1", ' Div světa je pýchou stvoření. Jen ti nejmocnější a nejbohatší dokážou postavit takové mistrovské dílo a ubránit ho před závistivými nepřáteli. ');
define("CHY2", "");
define("CHY3", "");
define("CHY4", "");
define("CHY5", "");

//KAZARMA BIG
define("BIG", 'Velká kasárna úroveň ');
define("BIG1", 'Velké kasárny umožňují cvičit více jednotek najednou, ty ale stojí trojnásobek původní ceny. ');
define("BIG2", "");
define("BIG3", "");
define("BIG4", "");

//KONI BIG
define("KONI ", 'Velká stáj úroveň ');
define("KONI1", 'Velká stáj umožňuje cvičit více jednotek najednou, ty ale stojí trojnásobek původní ceny. ');
define("KONI2", "");
define("KONI3", "");
define("KONI4", "");

//KAPKAN
define("KAP", 'Pastičkář úroveň ');
define("KAP1", 'Pastičkář chrání tvou vesnici dobře ukrytými pastmi. Neopatrní nepřátelé tak mohou být zajati a tvé vesnici už neublíží. ');
define("KAP2", "");
define("KAP3", "");
define("KAP4", "");

//KYZNIA
define("KY", ' Zbrojnice úroveň ');
define("KY1", ' V tavicích pecích zbrojnice se vylepšuje zbroj tvých válečníků. Zvýšením úrovně můžeš nechat vyrobit ještě lepší zbroj.');
define("KY2", "");
define("KY3", "");
define("KY4", "");


//KYZNIZA
define("KZ", ' Kovárna úroveň ');
define("KZ1", ' V tavicích pecích kovárny se vylepšují zbraně tvých válečníků. Zvýšením úrovně můžeš nechat vyrobit ještě lepší zbraně.');
define("KZ2", 'Vylepšit');
define("KZ3", 'Kovárna');
define("KZ4", 'Akce');
define("KZ5", 'Vylepšuje se');
define("KZ6", 'Trvání');
define("KZ7", 'Dokončit');
define("KZ8", 'Vylepšuje se<br>Kovárna');
define("KZ9", 'Vylepšování<br> probíhá');

define("oasis", 'Oázy');
define("Namet", 'Jméno');
define("Quantityе", 'Množství');
define("Maxе", 'Max');
define("Avaliablet", 'Dostupné');
define("TRA1", 'Výcvik');
define("TRA2", 'Trvání');
define("TRA3", 'Skončeno');
define("Workshop", 'Dílna');
define("RallyPoint", 'Shromaždiště');
define("Blacksmith", 'Kovárna');
define("Armoury", 'Zbrojnice');
define("Stable", 'Stáj');
define("SendResouces", 'Poslat suroviny');
define("Buyma", 'Koupit');
define("Offerma", 'Nabídnout');
define("ONPCtrading", 'Obchod s NPC');
define("ilior", 'nebo');
define("markgo", 'jít');
define("Constructnewbuilding", 'Postavit novou budovu');
define("SOCR", 'V pokladnici se uchovávají poklady tvé říše. Pokladnice pojme jen jeden artefakt. <br><br> Pro malý artefakt potřebuješ pokladnici na úrovni 10, pro velký na úrovni 20.');
define("mesotkogo", 'Odesílatel:');
define("mestena", 'Předmět:');
define("meskomy", 'Příjemce:');
//Самая жопа avaliable
define("avaAcademy", 'Akademie');
define("avaAcademy1", 'V akademii se zkoumají nové druhy jednotek. Zvýšením úrovně můžeš zkoumat lepší jednotky.');
define("avaArmoury", 'Zbrojnice');
define("avaArmoury1", 'V tavicích pecích zbrojnice se vylepšuje zbroj tvých válečníků. Zvýšením úrovně můžeš nechat vyrobit ještě lepší zbroj.');
define("avaCityWall", 'Městská zeď');
define("avaCityWall1", 'Městskou zdí ochráníš vesnici před barbarskými hordami nepřátel. Čím vyšší úroveň zdi, tím větší bonus k obraně tvých jednotek.');


define("ITEM0", 'Helma bdělosti');
define("IEFF0", '+15 % zkušeností navíc.');
define("ITEM1", 'Helma osvícení');
define("IEFF1", '+20 % zkušeností navíc.');
define("ITEM2", 'Helma moudrosti');
define("IEFF2", '+25 % zkušeností navíc.');
define("ITEM3", 'Helma regenerace');
define("IEFF3", '+10 bodů zdraví/den');
define("ITEM4", 'Helma zdraví');
define("IEFF4", '+15 bodů zdraví/den');
define("ITEM5", 'Helma léčení');
define("IEFF5", '+20 bodů zdraví/den');
define("ITEM6", 'Helma gladiátora');
define("IEFF6", '+100 kulturních bodů/den');
define("ITEM7", 'Helma tribuna');
define("IEFF7", '+400 kulturních bodů/den');
define("ITEM8", 'Helma konzula');
define("IEFF8", '+800 kulturních bodů/den');
define("ITEM9", 'Helma jezdce');
define("IEFF9", 'Doba výcviku ve stáji zkrácena o 10 %');
define("ITEM10", 'Helma kavalerie');
define("IEFF10", 'Doba výcviku ve stáji zkrácena o 15 %');
define("ITEM11", 'Helma těžké kavalerie');
define("IEFF11", 'Doba výcviku ve stáji zkrácena o 20 %');
define("ITEM12", 'Helma žoldnéře');
define("IEFF12", 'Doba výcviku v kasárnách zkrácena o 10 %');
define("ITEM13", 'Helma bojovníka');
define("IEFF13", 'Doba výcviku v kasárnách zkrácena o 15 %');
define("ITEM14", 'Helma velitele');
define("IEFF14", 'Doba výcviku v kasárnách zkrácena o 20 %');
define("ITEM15", 'Zbroj regenerace');
define("IEFF15", '+20 bodů regenerace zdraví za den');
define("ITEM16", 'Zbroj zdraví');
define("IEFF16", '+30 bodů regenerace zdraví za den');
define("ITEM17", 'Zbroj léčení');
define("IEFF17", '+40 bodů regenerace zdraví za den');
define("ITEM18", 'Lehká šupinová zbroj');
define("IEFF18", 'Poškození sníženo o 4 bodů zdraví + regenerace 10 HP');
define("ITEM19", 'Šupinová zbroj');
define("IEFF19", 'Poškození sníženo o 6 bodů zdraví + regenerace 15 HP');
define("ITEM20", 'Těžká šupinová zbroj');
define("IEFF20", 'Poškození sníženo o 8 bodů zdraví + regenerace 20 HP');
define("ITEM21", 'Lehký kyrys');
define("IEFF21", '+500 k bojové síle hrdiny');
define("ITEM22", 'Kyrys');
define("IEFF22", '+1000 k bojové síle hrdiny');
define("ITEM23", 'Těžký kyrys');
define("IEFF23", '+1500 k bojové síle hrdiny');
define("ITEM24", 'Lehká plátová zbroj');
define("IEFF24", 'Poškození sníženo o 3; +250 k bojové síle hrdiny');
define("ITEM25", 'Plátová zbroj');
define("IEFF25", '  Poškození sníženo o 4; +500 k bojové síle hrdiny');
define("ITEM26", 'Těžká plátová zbroj');
define("IEFF26", ' Poškození sníženo o 5; +750 k bojové síle hrdiny');
define("ITEM27", 'Malá mapa');
define("IEFF27", 'O 30 % rychlejší návrat');
define("ITEM28", 'Mapa');
define("IEFF28", 'O 40 % rychlejší návrat');
define("ITEM29", 'Velká mapa');
define("IEFF29", 'O 50 % rychlejší návrat');
define("ITEM30", 'Malá vlaječka');
define("IEFF30", 'O 30 % rychlejší jednotky mezi vlastními vesnicemi');
define("ITEM31", 'Vlaječka');
define("IEFF31", 'O 40 % rychlejší jednotky mezi vlastními vesnicemi');
define("ITEM32", 'Velká vlaječka');
define("IEFF32", 'O 50 % rychlejší jednotky mezi vlastními vesnicemi');
define("ITEM33", 'Malá standarta');
define("IEFF33", 'O 15 % rychlejší jednotky mezi členy aliance');
define("ITEM34", 'Standarta');
define("IEFF34", 'O 20 % rychlejší jednotky mezi členy aliance');
define("ITEM35", 'Velká standarta');
define("IEFF35", 'O 25 % rychlejší jednotky mezi členy aliance');
define("ITEM36", 'Zlodějův měšec');
define("IEFF36", '+10 % k bonusu kořisti');
define("ITEM37", 'Zlodějova brašna');
define("IEFF37", '+15 % k bonusu kořisti');
define("ITEM38", 'Zlodějův pytel');
define("IEFF38", '+20 % k bonusu kořisti');
define("ITEM39", 'Malý štít');
define("IEFF39", '+500 k síle hrdiny');
define("ITEM40", 'Štít');
define("IEFF40", '+1000 k síle hrdiny');
define("ITEM41", 'Velký štít');
define("IEFF41", '+1500 k síle hrdiny');
define("ITEM42", 'Malý natarský roh');
define("IEFF42", '+20 % bojové síly proti Natarům');
define("ITEM43", 'Natarský roh');
define("IEFF43", '+25 % bojové síly proti Natarům');
define("ITEM44", 'Velký natarský roh');
define("IEFF44", '+30 % bojové síly proti Natarům');
define("ITEM45", 'Krátký meč legionáře');
define("IEFF45", '+500 k síle hrdiny, za každého legionáře: +3 k útoku a +3 k obraně');
define("ITEM46", 'Meč legionáře');
define("IEFF46", '+1000 k síle hrdiny, za každého legionáře: +4 k útoku a +4 k obraně');
define("ITEM47", 'Dlouhý meč legionáře');
define("IEFF47", '+1500 k síle hrdiny, za každého legionáře: +5 k útoku a +5 k obraně');
define("ITEM48", 'Krátký meč pretoriána');
define("IEFF48", '+500 k síle hrdiny, za každého pretoriána: +3 k útoku a +3 k obraně');
define("ITEM49", 'Meč pretoriána');
define("IEFF49", '+1000 k síle hrdiny, za každého pretoriána: +4 k útoku a +4 k obraně');
define("ITEM50", 'Dlouhý meč pretoriána');
define("IEFF50", '+1500 k síle hrdiny, za každého pretoriána: +5 k útoku a +5 k obraně');
define("ITEM51", 'Krátký meč imperiána');
define("IEFF51", '+500 k síle hrdiny, za každého imperiána: +3 k útoku a +3 k obraně');
define("ITEM52", 'Meč imperiána');
define("IEFF52", '+1000 k síle hrdiny, za každého imperiána: +4 k útoku a +4 k obraně');
define("ITEM53", 'Dlouhý meč imperiána');
define("IEFF53", '+1500 k síle hrdiny, za každého imperiána: +5 k útoku a +5 k obraně');
define("ITEM54", 'Krátký meč Imperatoris');
define("IEFF54", '+500 k síle hrdiny, za každého Equites Imperatoris: +9 k útoku a +9 k obraně');
define("ITEM55", 'Meč Imperatoris');
define("IEFF55", '+1000 k síle hrdiny, za každého Equites Imperatoris: +12 k útoku a +12 k obraně');
define("ITEM56", 'Dlouhý meč Imperatoris');
define("IEFF56", '+1500 k síle hrdiny, za každého Equites Imperatoris: +15 k útoku a +15 k obraně');
define("ITEM57", 'Lehké kopí Caesaris');
define("IEFF57", '+500 k síle hrdiny, za každého Equites Caesaris: +12 k útoku a +12 k obraně');
define("ITEM58", 'Kopí Caesaris');
define("IEFF58", '+1000 k síle hrdiny, za každého Equites Caesaris: +16 k útoku a +16 k obraně');
define("ITEM59", 'Těžké kopí Caesaris');
define("IEFF59", '+1500 k síle hrdiny, za každého Equites Caesaris: +20 k útoku a +20 k obraně');
define("ITEM60", 'Oštěp falangy');
define("IEFF60", '+500 k síle hrdiny, za každého falangu: +3 k útoku a +3 k obraně');
define("ITEM61", 'Píka falangy');
define("IEFF61", '+1000 k síle hrdiny, za každého falangu: +4 k útoku a +4 k obraně');
define("ITEM62", 'Kopí falangy');
define("IEFF62", '+1500 k síle hrdiny, za každého falangu: +5 k útoku a +5 k obraně');
define("ITEM63", 'Krátký meč šermíře');
define("IEFF63", '+500 k síle hrdiny, za každého šermíře: +3 k útoku a +3 k obraně');
define("ITEM64", 'Meč šermíře');
define("IEFF64", '+1000 k síle hrdiny, za každého šermíře: +4 k útoku a +4 k obraně');
define("ITEM65", 'Dlouhý meč šermíře');
define("IEFF65", '+1500 k síle hrdiny, za každého šermíře: +5 k útoku a +5 k obraně');
define("ITEM66", 'Krátký luk Theutatů');
define("IEFF66", '+500 k síle hrdiny, za každého Theutatesův blesk: +6 k útoku a +6 k obraně');
define("ITEM67", 'Luk Theutatů');
define("IEFF67", '+1000 k síle hrdiny, za každého Theutatesův blesk: +8 k útoku a +8 k obraně');
define("ITEM68", 'Dlouhý luk Theutatů');
define("IEFF68", '+1500 k síle hrdiny, za každého Theutatesův blesk: +10 k útoku a +10 k obraně');
define("ITEM69", 'Hůl druida jezdce');
define("IEFF69", '+500 k síle hrdiny, za každého druida jezdce: +6 k útoku a +6 k obraně');
define("ITEM70", 'Velká hůl druida jezdce');
define("IEFF70", '+1000 k síle hrdiny, za každého druida jezdce: +8 k útoku a +8 k obraně');
define("ITEM71", 'Druidova bojová hůl');
define("IEFF71", '+1500 k síle hrdiny, za každého druida jezdce: +10 k útoku a +10 k obraně');
define("ITEM72", 'Haeduanovo lehké kopí');
define("IEFF72", '+500 k síle hrdiny, za každého haeduana: +9 k útoku a +9 k obraně');
define("ITEM73", 'Haeduanovo kopí');
define("IEFF73", '+1000 k síle hrdiny, za každého haeduana: +12 k útoku a +12 k obraně');
define("ITEM74", 'Haeduanovo těžké kopí');
define("IEFF74", '+1500 k síle hrdiny, za každého haeduana: +15 k útoku a +15 k obraně');
define("ITEM75", 'Pálkařův kyj');
define("IEFF75", '+500 k síle hrdiny, za každého pálkaře: +3 k útoku a +3 k obraně');
define("ITEM76", 'Pálkařova palice');
define("IEFF76", '+1000 k síle hrdiny, za každého pálkaře: +4 k útoku a +4 k obraně');
define("ITEM77", 'Pálkařova obří palice');
define("IEFF77", '+1500 k síle hrdiny, za každého pálkaře: +5 k útoku a +5 k obraně');
define("ITEM78", 'Oštěpařova píka');
define("IEFF78", '+500 k síle hrdiny, za každého oštěpaře: +3 k útoku a +3 k obraně');
define("ITEM79", 'Oštěpařův oštěp');
define("IEFF79", '+1000 k síle hrdiny, za každého oštěpaře: +4 k útoku a +4 k obraně');
define("ITEM80", 'Oštěpařovo kopí');
define("IEFF80", '+1500 k síle hrdiny, za každého oštěpaře: +5 k útoku a +5 k obraně');
define("ITEM81", 'Sekerníkova sekerka');
define("IEFF81", '+500 k síle hrdiny, za každého sekerníka: +3 k útoku a +3 k obraně');
define("ITEM82", 'Sekerníkova sekera');
define("IEFF82", '+1000 k síle hrdiny, za každého sekerníka: +4 k útoku a +4 k obraně');
define("ITEM83", 'Sekerníkova bitevní sekera');
define("IEFF83", '+1500 k síle hrdiny, za každého sekerníka: +5 k útoku a +5 k obraně');
define("ITEM84", 'Paladinovo lehké kladivo');
define("IEFF84", '+500 k síle hrdiny, za každého paladina: +6 k útoku a +6 k obraně');
define("ITEM85", 'Paladinovo kladivo');
define("IEFF85", '+1000 k síle hrdiny, za každého paladina: +8 k útoku a +8 k obraně');
define("ITEM86", 'Paladinovo těžké kladivo');
define("IEFF86", '+1500 k síle hrdiny, za každého paladina: +10 k útoku a +10 k obraně');
define("ITEM87", 'Teutonova dýka');
define("IEFF87", '+500 k síle hrdiny, za každého germánského rytíře: +9 k útoku a +9 k obraně');
define("ITEM88", 'Teutonův meč');
define("IEFF88", '+1000 k síle hrdiny, za každého germánského rytíře: +12 k útoku a +12 k obraně');
define("ITEM89", 'Teutonův dlouhý meč');
define("IEFF89", '+1500 k síle hrdiny, za každého germánského rytíře: +15 k útoku a +15 k obraně');
define("ITEM90", 'Boty regenerace');
define("IEFF90", '+10 bodů zdraví/den');
define("ITEM91", 'Boty zdraví');
define("IEFF91", '+15 bodů zdraví/den');
define("ITEM92", 'Boty léčení');
define("IEFF92", '+20 bodů zdraví/den');
define("ITEM93", 'Boty žoldnéře');
define("IEFF93", '+25 % rychlosti armády');
define("ITEM94", 'Boty válečníka');
define("IEFF94", '+50 % rychlosti armády');
define("ITEM95", 'Boty archonta');
define("IEFF95", '+75 % rychlosti armády');
define("ITEM96", 'Malé ostruhy');
define("IEFF96", 'rychlost hrdiny +3');
define("ITEM97", 'Ostruhy');
define("IEFF97", 'rychlost hrdiny +4');
define("ITEM98", 'Ostré ostruhy');
define("IEFF98", 'rychlost hrdiny +5');
define("ITEM99", 'Valach');
define("IEFF99", 'Rychlost hrdiny je 14');
define("ITEM100", 'Plnokrevník');
define("IEFF100", 'Rychlost hrdiny je 17');
define("ITEM101", 'Válečný kůň');
define("IEFF101", 'Rychlost hrdiny je 20');
define("ITEM102", 'Malý obvaz');
define("IEFF102", 'Vyléčí, max. 25 %  Lze skládat');
define("ITEM103", 'Obvaz');
define("IEFF103", 'Vyléčí, max. 33 % Lze skládat');
define("ITEM104", 'Klec');
define("IEFF104", 'V oáze lze chytit zvíře. Lze skládat');
define("ITEM105", 'Svitek');
define("IEFF105", 'Dá hrdinovi 10 zkušeností. Lze skládat');
define("ITEM106", 'Bylinková mast');
define("IEFF106", 'Okamžitě vyléčí hrdinu o 1 %. Lze skládat');
define("ITEM107", 'Vědro');
define("IEFF107", 'Okamžitě oživí tvého hrdinu');
define("ITEM108", 'Kniha moudrosti');
define("IEFF108", 'Přerozdělí dovednosti hrdiny');
define("ITEM109", 'Deska zákona');
define("IEFF109", '+1 % loajality ve vesnici, max. 125 %. Lze skládat');
define("ITEM110", 'Umělecké dílo');
define("IEFF110", 'Okamžitě dá kulturní body ve výši denní produkce, nejvýš však 5000. Lze skládat');
define("ITEM111", "");
define("IEFF111", "");
define("ITEM112", "");
define("IEFF112", "");
define("ITEM113", "");
define("IEFF113", "");
define("ITEM114", "");

// iRedux - New tribes items
define("ITEM115", 'Hůl otrocké milice');
define("IEFF115", '+500 k síle hrdiny, za každého otrockou milici: +3 k útoku a +3 k obraně');
define("ITEM116", 'Žezlo otrocké milice');
define("IEFF116", '+1000 k síle hrdiny, za každého otrockou milici: +4 k útoku a +4 k obraně');
define("ITEM117", 'Hvězdice otrocké milice');
define("IEFF117", '+1500 k síle hrdiny, za každého otrockou milici: +5 k útoku a +5 k obraně');
define("ITEM118", 'Sekera strážce popela');
define("IEFF118", '+500 k síle hrdiny, za každého strážce popela: +3 k útoku a +3 k obraně');
define("ITEM119", 'Lepší sekera strážce popela');
define("IEFF119", '+1000 k síle hrdiny, za každého strážce popela: +4 k útoku a +4 k obraně');
define("ITEM120", 'Válečná sekera strážce popela');
define("IEFF120", '+1500 k síle hrdiny, za každého strážce popela: +5 k útoku a +5 k obraně');
define("ITEM121", 'Válečníkův krátký chopeš');
define("IEFF121", '+500 k síle hrdiny, za každého válečníka s chopešem: +3 k útoku a +3 k obraně');
define("ITEM122", 'Válečníkův chopeš');
define("IEFF122", '+1000 k síle hrdiny, za každého válečníka s chopešem: +4 k útoku a +4 k obraně');
define("ITEM123", 'Válečníkův dlouhý chopeš');
define("IEFF123", '+1500 k síle hrdiny, za každého válečníka s chopešem: +5 k útoku a +5 k obraně');
define("ITEM124", 'Píka Anhurova strážce');
define("IEFF124", '+500 k síle hrdiny, za každého strážce Anhura: +6 k útoku a +6 k obraně');
define("ITEM125", 'Píka Anhurova strážce');
define("IEFF125", '+1000 k síle hrdiny, za každého strážce Anhura: +8 k útoku a +8 k obraně');
define("ITEM126", 'Kopí Anhurova strážce');
define("IEFF126", '+1500 k síle hrdiny, za každého strážce Anhura: +10 k útoku a +10 k obraně');
define("ITEM127", 'Krátký luk Rešepova jezdce');
define("IEFF127", '+500 k síle hrdiny, za každého Rešefův vůz: +9 k útoku a +9 k obraně');
define("ITEM128", 'Luk Rešepova jezdce');
define("IEFF128", '+1000 k síle hrdiny, za každého Rešefův vůz: +12 k útoku a +12 k obraně');
define("ITEM129", 'Dlouhý luk Rešepova jezdce');
define("IEFF129", '+1500 k síle hrdiny, za každého Rešefův vůz: +15 k útoku a +15 k obraně');

define("ITEM130", 'Žoldnéřova sekerka');
define("IEFF130", '+500 k síle hrdiny, za každého žoldnéře: +3 k útoku a +3 k obraně');
define("ITEM131", 'Žoldnéřova sekera');
define("IEFF131", '+1000 k síle hrdiny, za každého žoldnéře: +4 k útoku a +4 k obraně');
define("ITEM132", 'Žoldnéřova bitevní sekera');
define("IEFF132", '+1500 k síle hrdiny, za každého žoldnéře: +5 k útoku a +5 k obraně');
define("ITEM133", 'Lučištníkův krátký luk');
define("IEFF133", '+500 k síle hrdiny, za každého lučištníka: +3 k útoku a +3 k obraně');
define("ITEM134", 'Lučištníkův luk');
define("IEFF134", '+1000 k síle hrdiny, za každého lučištníka: +4 k útoku a +4 k obraně');
define("ITEM135", 'Lučištníkův dlouhý luk');
define("IEFF135", '+1500 k síle hrdiny, za každého lučištníka: +5 k útoku a +5 k obraně');
define("ITEM136", 'Krátký meč stepního jezdce');
define("IEFF136", '+500 k síle hrdiny, za každého stepního jezdce: +6 k útoku a +6 k obraně');
define("ITEM137", 'Meč stepního jezdce');
define("IEFF137", '+1000 k síle hrdiny, za každého stepního jezdce: +8 k útoku a +8 k obraně');
define("ITEM138", 'Dlouhý meč stepního jezdce');
define("IEFF138", '+1500 k síle hrdiny, za každého stepního jezdce: +10 k útoku a +10 k obraně');
define("ITEM139", 'Ostrostřelcův krátký luk');
define("IEFF139", '+500 k síle hrdiny, za každého střelce: +6 k útoku a +6 k obraně');
define("ITEM140", 'Ostrostřelcův luk');
define("IEFF140", '+1000 k síle hrdiny, za každého střelce: +8 k útoku a +8 k obraně');
define("ITEM141", 'Ostrostřelcův dlouhý luk');
define("IEFF141", '+1500 k síle hrdiny, za každého střelce: +10 k útoku a +10 k obraně');
define("ITEM142", 'Nájezdníkův krátký meč');
define("IEFF142", '+500 k síle hrdiny, za každého nájezdníka: +9 k útoku a +9 k obraně');
define("ITEM143", 'Nájezdníkův meč');
define("IEFF143", '+500 k síle hrdiny, za každého nájezdníka: +12 k útoku a +12 k obraně');
define("ITEM144", 'Nájezdníkův dlouhý meč');
define("IEFF144", '+500 k síle hrdiny, za každého nájezdníka: +15 k útoku a +15 k obraně');
define("HEROI0", 'Vlastnosti');
define("HEROI1", 'Bod');
define("HEROI2", 'popisek síly vlastnosti');
define("HEROI3", 'Bojová síla se sčítá s útokem a obranou hrdiny. Čím vyšší bojová síla, tím lépe si hrdina vede v boji.');
define("HEROI4", 'Bojová síla:');
define("HEROI5", 'od hrdiny');
define("HEROI6", 'popisek síly vlastnosti');
define("HEROI7", 'Bojová síla se sčítá s útokem a obranou hrdiny. Čím vyšší bojová síla, tím lépe si hrdina vede v boji.');
define("HEROI8", 'Bojová síla:');
define("HEROI9", 'od hrdiny +');
define("HEROI10", 'Vlastnosti');
define("HEROI11", 'Bojová síla');
define("HEROI12", 'Max');
define("HEROI13", 'Bonus k útoku přidává sílu při útoku.');
define("HEROI14", 'Bonus k útoku');
define("HEROI15", 'Bonus k obraně přidává sílu při obraně.');
define("HEROI16", 'Bonus k obraně');
define("HEROI17", 'Hrdina také získává suroviny – čím vyšší úroveň, tím víc surovin.');
define("HEROI18", 'Bonus surovin :');
define("HEROI19", 'Suroviny');
define("HEROI20", 'Rozdělit');
define("HEROI21", 'Změnit produkci surovin hrdiny');
define("HEROI22", 'Všechny suroviny');
define("HEROI23", 'Regenerace tvého hrdiny:');
define("HEROI24", 'za den');
define("HEROI25", 'Zdraví:');
define("HEROI26", 'Hrdina oživne v této vesnici');
define("HEROI27", 'Nedostatek surovin na oživení hrdiny');
define("HEROI28", 'Oživit');
define("HEROI29", 'Hrdina bude připraven za');
define("HEROI30", 'Trvání');
define("HEROI31", 'Tvůj hrdina potřebuje ještě');
define("HEROI32", 'zkušeností k dosažení úrovně');
define("HEROI33", 'Zkušenosti:');
define("HEROI34", 'Čím vyšší úroveň hrdiny, tím víc bodů získáš.');
define("HEROI35", 'Úroveň hrdiny');
define("HEROI36", 'Rychlost hrdiny určuje, kolik polí urazí za hodinu');
define("HEROI37", 'Rychlost:');
define("HEROI38", 'Polí za hodinu');
define("HEROI39", 'Tvůj hrdina zůstane vždy s jednotkami.');
define("HEROI40", 'Hrdina se při útoku na domovskou vesnici ukryje. ');
define("HEROI41", 'Ukrýt hrdinu:');
define("HEROI43", 'Produkce hrdiny:');
define("HEROI44", 'Aktuální regenerace hrdiny: 20 % za den');
define("HEROI45", 'Rychlost');
define("HEROI46", 'Rychlost tvého hrdiny');
define("HEROI47", 'Aktuální produkce hrdiny:');
define("HEROI48", 'Vlastnosti:');
define("HEROI49", 'Ulož prosím změny.');
define("HEROI50", 'Volné body:');
define("HEROI51", 'Uložit změny');
define("HEROI53", 'Suroviny potřebné k oživení hrdiny:');
define("HEROA0", 'Místo');
define("HEROA1", 'Trvání');
define("HEROA2", 'Obtížnost');
define("HEROA3", 'Zbývající čas');
define("HEROA4", 'Odkaz');
define("HEROA5", 'Žádné dobrodružství nenalezeno.');
define("HEROA6", 'Na dobrodružství.');
define("HEROA7", 'Les');
define("HEROA8", 'Jezero');
define("HEROA9", 'Hory');
define("HEROA10", 'Oceán');
define("HEROF0", 'hlava');
define("HEROF1", 'barva vlasů');
define("HEROF2", 'účes');
define("HEROF3", 'uši');
define("HEROF4", 'obočí');
define("HEROF5", 'oči');
define("HEROF6", 'nos');
define("HEROF7", 'ústa');
define("HEROF8", 'vousy');
define("HEROAC0", 'Filtr: helmy');
define("HEROAC1", 'Filtr: zbroje');
define("HEROAC2", 'Filtr: předměty do levé ruky');
define("HEROAC3", 'Filtr: předměty do pravé ruky');
define("HEROAC4", 'Filtr: boty');
define("HEROAC5", 'Filtr: koně');
define("HEROAC6", 'Filtr: malé obvazy');
define("HEROAC7", 'Filtr: obvazy');
define("HEROAC8", 'Filtr: klece');
define("HEROAC9", 'Filtr: svitky');
define("HEROAC10", 'Filtr: masti');
define("HEROAC11", 'Filtr: vědra');
define("HEROAC12", 'Filtr: knihy moudrosti');
define("HEROAC13", 'Filtr: desky zákona');
define("HEROAC14", 'Filtr: umělecká díla');
define("HEROAC15", 'Předměty nenalezeny.');
define("HEROAC16", 'Otevřené');
define("HEROAC17", 'Uzavřené');
define("HEROAC18", 'Nedostatek stříbra');
define("HEROAC19", 'Aktuální nabídka:');
define("HEROAC20", 'Nejvyšší nabídka:');
define("HEROAC21", 'Nová nabídka:');
define("HEROAC22", 'Nabídka');
define("HEROAC23", 'Aukce');
define("HEROAC24", 'Žádné nabídky.');
define("HEROAC25", 'Čas');
define("HEROAC26", 'Vyhráno');
define("HEROAC27", 'Skončené aukce');
define("HEROAC28", 'Vybrat vše');
define("HEROAC29", 'Žádné předměty nejsou k dispozici.');
define("HEROAC30", 'Koupit');
define("HEROAC31", 'Prodat');
define("HEROAC32", 'Nabídky');
define("HEROAC33", 'Žádné předměty');
define("HEROAC34", 'Za každou jednotku');
define("HEROAC35", 'Momentálně máš');
define("HEROAC36", 'předmětů v aukci (najednou je povoleno max. 5)');
define("HEROAC37", 'Skončené aukce.');
define("HEROAC38", 'Žádné prodeje.');
define("HEROAC39", 'Opravdu prodat tento předmět?');
define("HEROAC40", 'Prodat &lt');
define("HEROAC41", 'Množství');
define("HEROAC42", 'Změnit nabídku');
define("HEROAC43", 'Přihodit');
define("HEROAC44", 'Za kus');

define("PRODUCTION_OVERVIEW", 'Přehled produkce');
define("PRODUCTION_BONUS", 'Bonus produkce');
define("PRODUCTION_TOTAL_BONUS", 'Celkový bonus');
define("PRODUCTION_FIELD", 'Zdroj');
define("PRODUCTION", 'Produkce');
define("PRODUCTION_BONUS2", 'Bonus');
define("PRODUCTION_HP", 'Produkce hrdiny');
define("PRODUCTION_BALANCE", 'Průběžná bilance');
define("PRODUCTION_P25", '+25 % produkce');
define("PRODUCTION_INACTIVE", 'Neaktivní');
define("PRODUCTION_TOTAL", 'Celkem');
define("PRODUCTION_PROD_TOTAL", 'Celková produkce za hodinu:');
define("PRODUCTION_PROD_S1", 'Hliněný důl úroveň');
define("PRODUCTION_PROD_S2", 'Dřevorubec úroveň');
define("PRODUCTION_PROD_S3", 'Železný důl úroveň');
define("PRODUCTION_PROD_S4", 'Obilné pole úroveň');
define("PRODUCTION_PROD_S5", 'Hodinová produkce včetně bonusu produkce: ');
define("PRODUCTION_PROD_S6", 'Bonus produkce zvyšuje produkci suroviny ve <span class="underlined">všech</span> tvých vesnicích.');
define("PRODUCTION_PROD_T1", 'Pila úroveň');
define("PRODUCTION_PROD_T2", 'Oáza');
define("PRODUCTION_PROD_T3", 'Cihelna úroveň');
define("PRODUCTION_PROD_T4", 'Slévárna úroveň');
define("PRODUCTION_PROD_T5", 'Mlýn úroveň');
define("PRODUCTION_PROD_T6", 'Pekárna úroveň');

define("ESCAPE_GORIZ", 'Útěk');
define("CAPTCHA_1", 'Klikni na obrázek pro nový');
define("PLUS_TIME_ENABLE", '<p>Tvůj účet Plus je aktivní ještě <b><span id="timer100">%s</span></b> d.</p>');
define("RENEW", 'Prodloužit');
define("USERS_ACTIVE", 'Aktivní uživatelé');
define("USERS_ONLINE", 'Uživatelé online');
define("USERS_TOTAL", 'Uživatelů celkem');
define("dorf1_links", 'Seznam odkazů');
define("dorf1_activateplus", 'Aktivovat účet Plus');
define("dorf1_villageNameBox_1", 'Žádné tržiště');
define("dorf1_villageNameBox_2", 'Postav tržiště');
define("dorf1_villageNameBox_3", 'Žádná kasárna');
define("dorf1_villageNameBox_4", 'Postav kasárna');
define("dorf1_villageNameBox_5", 'Žádná stáj');
define("dorf1_villageNameBox_6", 'Postavit stáj');
define("dorf1_villageNameBox_7", 'V této vesnici není dílna');
define("dorf1_villageNameBox_8", 'Žádná dílna');
define("dorf1_villageNameBox_9", 'Přejít na tržiště');
define("dorf1_villageNameBox_10", 'Přejít do kasáren');
define("dorf1_villageNameBox_11", 'Probíhá výcvik');
define("dorf1_villageNameBox_11n", 'Neprobíhá žádný výcvik');
define("dorf1_villageNameBox_12", 'Přejít do stáje');
define("dorf1_villageNameBox_13", 'Probíhá výcvik');
define("dorf1_villageNameBox_14", 'Přejít do dílny');
define("dorf1_villageNameBox_15", 'Probíhá výcvik');
define("dorf1_villageNameBox_16", 'Změnit název vesnice');
define("dorf1_villageNameBox2_1", 'Statistiky vesnic');
define("dorf1_villageNameBox2_2", 'Zobrazit souřadnice');
define("dorf1_villageNameBox2_4", 'Skrýt souřadnice');
define("dorf1_villageNameBox2_3", 'Vesnice');
define("dorf1_villageNameBox2_5", 'Kulturní body potřebné k získání další vesnice:');
define("Link_desc_text_1", 'S Travian Plus si můžeš vytvořit seznam odkazů.');
define("infobox_desc_text_1", 'Infobox');
define("Questbox_desc_text_1", 'Vítej');
define("Questbox_desc_text_2", 'Začít úkol');
define("LVL", 'úroveň');
define("SIDE_I_1", 'Artefakty budou vypuštěny za');
define("SIDE_I_2", "");
define("SIDE_I_3", 'Artefakty jsou vypuštěny');
define("SIDE_I_4", '<font style=\'font-size:11px;\'>Stavební plány budou vypuštěny za</font>');
define("SIDE_I_5", 'Stavební plány jsou vypuštěny');
define("SIDE_I_6", 'Medaile budou uděleny za');
define("SIDE_I_7", "");
define("CS", 'Stavební místo');
define("UPGRADECOST", 'Cena za rozšíření na úroveň %s ');
define("SERVER_INFO", 'Informace o serveru');
define("MORE_ADVS_1", 'Musíš se vydat na dobrodružství ještě');
define("MORE_ADVS_2", 'krát, abys mohl nakupovat');
define("WORLDWONDER", 'Div světa');
define("WAREHOUSE", 'Sklad surovin');
define("NO_FARM", 'Zatím tu nejsou žádné farmy!');
define("FARMLIST_FOOTER", '<small>
Suroviny farem se přepočítávají přibližně každých 30 sekund.<br>
Kapacita skladu a obilí je u farem stejná.<br>
Seznam je seřazen podle času vytvoření položek.<br>
</small>');
define("PROTECTION_TIME", 'Ochranu začátečníka máš ještě <b><span class="timer" counting="down" value="%s">%s</span></b>.');
define("ACCOUNT_DELETION", 'Tvůj účet bude smazán za <b><span id="timer100">%s</span></b>');
define("Ally_dorf1", 'Aliance');
define("DIRECT_LINK", 'Přímý odkaz');
define("NO_PLUS_ESI", 'Tato možnost vyžaduje aktivní účet Plus');
define("NO_PLUS_ESI2", 'Přejít do obchodu');
define("NO_PLUS_ESI3", 'Přejít na tržiště');

define("plus_goldcount", 'Zlato');
define("buygold_DESC_1", 'Místo');
define("buygold_DESC_2", 'Vyber svou polohu:');
define("buygold_DESC_3", 'Změnit');
define("buygold_DESC_4", 'Balíčky zlata:');
define("buygold_DESC_5", 'Vyber balíček');
define("buygold_DESC_7", 'Platební brány');
define("buygold_DESC_8", 'Je třeba vybrat');
define("buygold_DESC_9", 'Ceny jsou konečné');
define("buygold_DESC_10", 'Platby proběhnou okamžitě');
define("buygold_DESC_11", 'Koupit zlato');
define("buygold_DESC2_1", 'Vybrat jiný balíček');
define("buygold_DESC_6", 'Krok 2 – vyber balíček');
define("first_desc1", 'Vyber národ');
define("first_desc2", 'Děkujeme za aktivaci účtu');
define("first_desc3", 'Vyber národ, za který budeš na tomto serveru hrát');
define("first_desc4", 'Pokud hraješ Travian poprvé, doporučujeme Galy');
define("first_Gauls_desc1", 'Galové');
define("first_Gauls_desc2", 'Vlastnosti:');
define("first_Gauls_desc3", 'Stačí málo času.');
define("first_Gauls_desc4", 'Od začátku hry jsou lépe chráněni proti loupežím.');
define("first_Gauls_desc5", 'Vynikající obchodníci a nejrychlejší jednotky ve hře.');
define("first_Gauls_desc6", 'Nejlepší volba pro nové hráče.');
define("first_Roman_desc1", 'Římané');
define("first_Roman_desc2", 'Zvláštnosti:');
define("first_Roman_desc3", 'Je nutné dobré plánování času.');
define("first_Roman_desc4", 'Mohou rozvíjet vesnice rychleji než ostatní.');
define("first_Roman_desc5", 'Silná, ale drahá armáda; velmi silné obléhací stroje.');
define("first_Roman_desc6", 'Na začátku hry je to velmi těžký národ, novým hráčům ho nedoporučujeme.');
define("first_Teutons_desc1", 'Germáni');
define("first_Teutons_desc2", 'Zvláštnosti:');
define("first_Teutons_desc3", 'Hodí se pro agresivní hráče s dostatkem času.');
define("first_Teutons_desc4", 'Levné jednotky se rychle cvičí a jsou dobré na loupení.');
define("first_Teutons_desc5", 'Pro agresivní a zkušené hráče.');
define("first_page_second_step_desc1", 'Vesnice bude v místě podle tvé volby.');
define("first_page_tribe1", 'Římané');
define("first_page_tribe2", 'Germáni');
define("first_page_tribe3", 'Galové');
define("first_page_tribe1_lead", 'Quintus');
define("first_page_tribe2_lead", 'Henrik');
define("first_page_tribe3_lead", 'Ambiorix');
define("first_Gauls_chosen_desc1", 'Zvolil sis %s. Tvým průvodcem bude %s.');
define("first_Romans_chosen_desc1", 'Zvolil sis %s. Tvým průvodcem bude %s.');
define("first_Teutons_chosen_desc1", 'Zvolil sis %s. Tvým průvodcem bude %s.');
define("first_page_second_step_desc2", 'Změnit národ');
define("first_page_second_step_desc3", 'Postavíme tvou vesnici; místo můžeš změnit kliknutím na mapu.');
define("first_page_second_step_desc4", 'Začneš na severozápadě');
define("first_page_second_step_desc5", 'Začneš na severovýchodě');
define("first_page_second_step_desc6", 'Začneš na jihozápadě');
define("first_page_second_step_desc7", 'Začneš na jihovýchodě');
define("first_page_second_step_desc8", 'Založit vesnici');
define("BUILDINGS", 'Centrum vesnice');
define("CHANGING_YOUR_VILLAGE_NAME", 'změnit jméno vesnice');
define("NEW_NAME", 'nové jméno vesnice');
define("IS_ON_ADVENTURE", 'Na dobrodružství');
define("MOVING_FROM", 'Na cestě z');
define("LN_TO", 'do');
define("SOME_CHANGES_DONE", 'Tvé změny budou zahozeny. Opravdu chceš odejít?');
define("MORE_INFO_25_BUTTON", 'Více o 25% bonusu produkce surovin');
define("HEROFULLLVL", 'Tvůj hrdina dosáhl maximální úrovně.');
define("LN_QUEST", 'Úkol');
define("SHOW_HINTS_PANEL", 'Zobrazit panel s tipy');
define("JR_CONSTRUCTION_PLANS_TITLE", 'Stavební plán Divu světa');
define("JR_CONSTRUCTION_PLANS_VNAME", 'Stavební plán – vesnice');
define("JR_CONSTRUCTION_PLANS_DESC", 'S tímto starobylým stavebním plánem můžeš postavit Div světa až do úrovně 50. Pro další stavbu musí tvá aliance držet alespoň dva plány.');
define("JR_CONSTRUCTION_PLANS_RELEASE_TITLE", 'Stavební plány');
define("JR_CONSTRUCTION_PLANS_RELEASE_DESC", 'Před mnoha desítkami let překvapil národy Travianu nečekaný návrat Natarů. Tento národ dávné moudrosti, nesrovnatelné velikosti a slávy se chystal znovu ohrozit svobodné lidi. Proto se vynaložilo veškeré úsilí na přípravu poslední války, která měla Natary navždy vyhnat. Mnozí za řešení považovali takzvané „Divy světa“ – legendární stavby. Říkalo se, že jejich dokončení učiní stavitele vládci a dobyvateli Travianu. ');
define("JR_CONSTRUCTION_PLANS_RELEASE_TREASURY_DESC", 'Ke stavbě takové budovy je však potřeba plán. Architekti proto vymysleli důmyslné způsoby, jak plány bezpečně uschovat. Po čase se v mnoha městech objevily stavby podobné chrámům – pokladnice.');
define("JR_CONSTRUCTION_PLANS_RELEASE_MYTHS_DESC", 'Bohužel nikdo – ani ti nejmoudřejší a nejzkušenější – netušil, kde by se plány mohly nacházet. Čím víc lidí je hledalo, tím víc se zdálo, že jde jen o mýty.');
define("JR_CONSTRUCTION_PLANS_RELEASE_DISCOVERED_DESC", 'Dnes však bude odhaleno i toto poslední tajemství. Ztráty a marné snahy minulosti skončily, protože zvědové několika národů našli místa, kde jsou stavební plány ukryty. Dobře střežené Natary leží v mnoha oázách po celé zemi Travianu. Jen nejstatečnější hrdinové dokážou plán získat a bezpečně dopravit domů, aby mohla stavba začít.');
define("JR_CONSTRUCTION_PLANS_RELEASE_LINK_DESC", 'Všechny informace o fungování stavebních plánů najdeš na serverech.');
define("JR_HERE", 'Zde');
define("JR_travian_TEAM", 'Tým Travianu');
define("JR_CONTINUE", 'pokračovat');
define("JR_ATTACK_COMBAT_MODEL", 'Bojový model');
define("JR_ATTACK_NORMAL", 'Normální útok');
define("JR_ATTACK_RAID", 'Loupež');
define("JR_WARSIM_ATTACKER", 'Útočník');
define("JR_WARSIM_DEFENDERS", 'Obránci');
define("JR_WARSIM_ATTACKCONFIG", 'Nastavení útoku');
define("JR_WARSIM_SIMULATE", 'Simulovat');
define("JR_POWERED_BY", 'Powered by mehran');
define("JR_RIGHTS", 'Všechna práva vyhrazena');
define("JR_ZRAVIANX", "mehran");
define("JR_COPYYEAR", "&copy; 2011 - 2012");
define("JR_FOOTER_SPECIAL_S", '<b>Vyvinul: <a href="404/" target="_blank" style="color:purple">mehran</a> (vývojář, překladatel a designér)</b> <br/><br/> Velké díky patří <a style="color:green">Mehran EBDa (vývojář), Papa Grumps (anglický překlad) a akshay9 (vývojář) </a>');
define("JR_NOT_ADMIN", 'Přístup odepřen: nejsi admin!');
define("JR_ART_BPTTL", 'Starobylý stavební plán');
define("JR_ART_BPVN", 'Stavební plán Divu světa');
define("JR_ART_BPDES", 'S tímto starobylým stavebním plánem můžeš postavit Div světa až do úrovně 50. Pro další stavbu musí tvá aliance držet alespoň dva plány.');
define('WILLACTIN', 'Aktivuje se za');
define("JR_CATEGORY", 'Kategorie');
define("JR_SELECT", 'Vybrat');
define("JR_GENERALQUESTIONS", 'Obecné dotazy');
define("JR_ICANNOTLOGIN", 'Nemůžu se přihlásit');
define("JR_ICANNOTREGISTER", 'Nemůžu se zaregistrovat');
define("JR_SEND", 'Odeslat');
define("JR_REGISTERFIRST", 'Nejdřív si zaregistruj účet.');
define("JR_HEROATTRIBUTES", 'Vlastnosti');
define("JR_HEROAPPEARANCE", 'Vzhled');
define("JR_HEROADVENTURE", 'Dobrodružství');
define("JR_HEROAUCTION", 'Aukce');
define("JR_HEROHEAD", 'hlava');
define("JR_HEROHAIRCOLOR", 'barva vlasů');
define("JR_HEROHAIRSTYLE", 'účes');
define("JR_HEROEARS", 'uši');
define("JR_HEROEYEBROWS", 'obočí');
define("JR_HEROEYES", 'oči');
define("JR_HERONOSE", 'nos');
define("JR_HEROMOUTH", 'ústa');
define("JR_HEROBEARD", 'vousy');
define("JR_HEROLOCATION", 'Místo');
define("JR_HEROTIME", 'Trvání');
define("JR_HERODIFFICULTY", 'Obtížnost');
define("JR_HEROTIMELEFT", 'Zbývající čas');
define("JR_HEROLINK", 'Odkaz');
define("JR_HEROBIDERROR1", 'Nemáš dost stříbrných mincí na koupi tohoto předmětu. Potřebuješ alespoň');
define("JR_HEROBIDERROR2", 'minci/mincí');
define("JR_HEROBIDERROR3", 'Nedostatek stříbrných mincí na nabídku.');
define("JR_HEROBIDERROR4", 'Existuje vyšší nabídka.');
define("JR_HEROBIDERROR5", 'Aukce skončila.');
define("JR_HEROBIDERROR6", 'Aukce neexistuje.');
define("JR_HEROEVASION", 'Je-li zaškrtnuto, hrdina se při útoku na vesnici schová.');
define("JR_HERODEADORNOTHERE", 'Tvůj hrdina je mrtvý nebo není v této vesnici, takže tento předmět nemůžeš použít.');
define("HEROISDEAD", 'Hrdina je mrtvý');
define("JR_HEROBUYITEMS", 'Koupit předměty');
define("JR_HEROSELLITEMS", 'Prodat předměty');
define("JR_HEROEXP", 'zkušenosti');
define("JR_HEROEXPGROW", 'získané zkušenosti');
define("JR_HEROEXPWILLBE", 'po použití budou zkušenosti');
define("JR_HEROCURRENTCP", 'Aktuální kulturní body');
define("JR_HEROCPVALUE", 'Kulturní body');
define("JR_HEROCPAFTERUSE", 'Kulturní body po použití');
define("JR_HEROWANNAWEAR", 'Opravdu si chceš nasadit tento předmět?');
define("JR_HEROTIU", 'Použito předmětů celkem');


define("JR_SAVE", 'Uložit');

define("JR_FOREST", 'les');
define("JR_FIELD", 'pole');
define("JR_MOUNTAIN", 'hory');
define("JR_SEA", 'moře');
define("JR_OK", 'OK');

define("JR_CANCEL", 'Zrušit');
define("JR_YES", 'Ano');
define("JR_NO", 'Ne');
define("NUM", 'Ne.');
define("JR_NOTFINISHED", 'Nedokončeno');
define("JR_CONSUMPTION", 'Spotřeba');

define("JR_MOW", 'Medaile týdne');
define("JR_MEDALSCONFIRM", 'Potvrdit udělení medailí');
define("JR_MEDALSCONFIRMNOTE", 'Pozor: může to chvíli trvat');
define("JR_CONFIRM", 'Potvrdit');
define("GENDER", 'Pohlaví');
define("GENDER0", 'Neuvedeno');
define("GENDER1", 'Muž');
define("GENDER2", 'Žena');

//logint4.4
define("logint40", 'Větší mapa||Pro tuto funkci potřebuješ aktivní Travian Plus.');
define("logint41", 'Obsazeno');
define("logint42", 'Neobsazeno');
define("logint43", 'Divočina');
define("logint410", 'Vystředit mapu');
define("logint411", 'Založit novou vesnici');
define("logint412", 'Hlášení');
define("logint413", 'Hráč');
define("logint414", 'Národ');
define("logint415", 'Aliance');
define("logint416", 'Vlastník');
define("logint417", 'Obyvatelé');
define("logint418", 'Rozložení');
define("logint419", 'Poslat jednotky');

//pluspalladiys
define("pluss0", 'Pro nákup zlata jinými způsoby (qiwi, webmoney, paypal) kontaktuj');
define("pluss1", 'administrátora');
define("pluss2", 'Po nákupu zlata přejdi na');
define("pluss3", 'Banka');
define("pluss4", 'Tam můžeš zlato převést zčásti nebo celé na libovolný účet na serveru.');
define("pluss5", 'Nákup zlata');
define("pluss6", 'Funkce Plus');
define("pluss7", 'Popis');
define("pluss8", 'Trvání');
define("pluss9", 'Zlato');
define("pluss10", 'Akce');
define("pluss11", 'Účet');
define("pluss12", 'Zbývá');
define("pluss13", 'dny');
define("pluss14", 'Směnárna');
define("pluss15", 'Zadej množství zlata nebo stříbra, které chceš vyměnit.');
define("pluss16", 'Směnné kurzy');
define("pluss17", '1 zlato : 100 stříbra');
define("pluss18", '2000 stříbra : 1 zlato');
define("pluss19", 'směnit');
define("pluss20", 'CHYBAAAAAAA');
define("pluss21", 'Koupit zlato');
define("pluss22", 'Funkce');
define("pluss23", 'Získat zlato');
define("pluss24", 'Směna zlata a stříbra');
define("pluss25", 'Aktivovat');
define("pluss26", 'Prodloužit');
define("pluss27", 'Potřebuje zlato');
define("pluss28", 'Zbývá');
define("pluss29", 'dny');
define("pluss30", 'hodiny');
define("pluss31", 'minuty');
define("pluss32", 'Máš');
define("pluss33", 'zlatých');
define("pluss34", 'Produkce: dřevo');
define("pluss35", 'Produkce: hlína');
define("pluss36", 'Produkce: železo');
define("pluss37", 'Produkce: obilí');
define("pluss38", 'NPC 1:1');
define("pluss39", "");
define("pluss40", "");
define("pluss41", "");
define("pluss42", "");
define("pluss43", "");
define("pluss44", "");
define("pluss45", "");

//herohome
define("herohero0", 'Vlastnosti');
define("herohero1", 'Vzhled');
define("herohero2", 'Dobrodružství');
define("herohero3", 'Aukce');
define("herohero4", 'Koupit předmět.');
define("herohero5", 'Prodat předmět.');
define("herohero6", 'Náhodně');

//ban_msg.tpl
define("yubnd", 'Byl jsi zablokován. Napiš prosím Multihunterovi');

define("SOKI_1", 'Dobrodružství');
define("SOKI_2", 'Jednotky');
define("SOKI_3", 'Příchod');
define("SOKI_4", 'Vyrazit na dobrodružství');
define("SOKI_5", 'Upozornění');
define("SOKI_6", 'Objevené svitky');

define("attack1", 'Suroviny');
define("attack2", 'Infrastruktura');
define("attack3", 'Jednotky');
define("attack4", 'Suroviny');
define("attack5", 'Infrastruktura');
define("attack6", 'Jednotky');
define("attack7", 'Diamantové dláto');
define("attack8", 'Obří mramorové kladivo)');
define("attack9", 'Hemonovy svitky');
define("attack10", 'Opálová podkova');
define("attack11", 'Zlatý vůz');
define("attack12", 'Feidippidovy sandály');
define("attack13", 'Příběh krysy');
define("attack14", 'Generálův dopis');
define("attack15", 'Deník Sun-c’');
define("attack16", 'Sepsaná vojenská přísaha');
define("attack17", 'Vyhlášení války');
define("attack18", 'Paměti Alexandra Velikého');
define("attack19", 'Plán velkého skladu nebo velké sýpky!');
define("attack20", 'Stavitelé Divu světa!');
define("attack21", 'potvrdit');

define("newdorf1", 'Založit novou vesnici');
define("newdorf2", 'nové');
define("newdorf3", 'Založit novou vesnici');
define("newdorf4", 'jednotky');
define("newdorf5", 'Trvání');
define("newdorf6", 'Suroviny');
define("newdorf7", 'potvrdit');
define("newdorf8", 'kulturní body');

define("search1", 'Posily');
define("search2", 'Normální útok');
define("search3", 'Loupež');
define("search4", 'Souřadnice');
define("search5", "X"); // not sure if really needed lol
define("search6", "Y");
define("search7", 'Potvrdit');

define("sendback1", 'Poslat domů');
define("sendback2", 'Poslat tyto jednotky zpět');
define("sendback3", 'Jednotky');
define("sendback4", 'Čas');
define("sendback5", 'za');
define("sendback6", 'do');
define("sendback7", 'potvrdit');

define("startraid1", 'Nemáš dost jednotek!');


define("activated1", 'zaregistruj se do hry');
define("activated2", 'registrace');

define("delete", 'zaregistruj se do hry');
define("delete2", 'smazat');

define("allimenu1", 'aktivní');
define("allimenu2", 'normální');
define("allimenu3", 'Novinky');
define("allimenu4", 'Útoky');
define("allimenu5", 'Možnosti');

define("allidesc1", 'Obchod');

define("assignpos1", 'Přidělit');

define("attacker1", 'Nejsou k dispozici žádná hlášení');

define("defender1", 'Nejsou k dispozici žádná hlášení');

define("attacks1", 'Vojenské události');
define("attacks2", 'Obránce');
define("attacks3", 'Útočník');

define("changepos0", 'Jméno');
define("changepos1", 'Přidělit');
define("changepos2", 'Vyloučit');
define("changepos3", 'Změnit popis');
define("changepos4", 'Diplomacie aliance');
define("changepos5", 'Zprávy celé alianci');
define("changepos6", 'Pozvat');
define("changepos7", 'Název pozice');

define("changediplo1", 'Žádné');

define("invite1", 'Žádné pozvánky');
define("invite2", 'Odmítnuto');
define("invite3", 'Pozván');

define("kick1", 'Jít');

define("option1", 'Jít');

define("overvieww1", 'Podrobnosti');
define("overvieww2", 'Pozice');
define("overvieww", 'Členové');


define("quitally1", 'Odejít');

define("bids1", 'Za každou jednotku');
define("bids2", 'Nabídky');
define("bids3", 'Stříbro');
define("bids4", 'Čas');
define("bids5", 'Smazat');

define("buy1", 'stříbro');
define("buy2", 'za kus');
define("buy3", 'Nová nabídka');
define("buy4", 'Nabídnout');

define("build1", 'Rozšiř sklad surovin');
define("build2", 'Rozšířit sýpku');
define("build3", 'Dost surovin');
define("build4", 'Produkce obilí je záporná, surovin nikdy nebude dost');
define("build5", 'málo surovin');
define("build6", 'Plně rozvinuto');
define("build7", 'Vylepšit kovárnu');
define("build8", 'Probíhá výzkum');
define("build9", 'vývoj');
define("build10", 'Jednotka');
define("build11", 'Zbývající čas');
define("build12", 'Skončeno');
define("build13", 'Zbourání budovy');
define("build14", 'Pokud budovu už nepotřebuješ, můžeš nařídit její zbourání');
define("build15", 'Zbourání');
define("build16", 'Okamžitě dokončit všechny stavby a výzkumy v této vesnici za 2 zlaté?');
define("build17", 'Zbourat budovu');
define("build18", 'Zabít');
define("build19", 'Seznam farem je zdarma, ale jen se Zlatým klubem.');
define("build20", 'Nastavení úniku');
define("build21", 'aktivuj únik jednotek pro své hlavní město.');
define("build22", 'Útok na ');
define("build23", 'Loupež na ');
define("build24", 'Podpora pro ');
define("build25", 'Jednotky');
define("build26", 'Příchod');
define("build27", 'za');
define("build28", 'Návrat z ');
define("build29", 'Podpora pro ');
define("build30", 'Návrat z ');
define("build31", 'Návrat z ');
define("build32", 'Návrat z krytu ');
define("build33", 'Oáza');
define("build34", 'Kryt');
define("build35", 'Návrat z ');
define("build36", 'Jednotky');
define("build37", 'Ukradeno');
define("build38", 'Příchod');
define("build39", 'Útok na tvou');
define("build40", 'Oáza');
define("build41", 'Jednotky');
define("build42", 'Příchod');
define("build43", 'za');
define("build44", 'Jednotky');
define("build45", 'Spotřeba');
define("build46", 'za hodinu');
define("build47", 'Podpora pro');
define("build48", 'Zvěd');
define("build49", 'Útok na');
define("build50", 'Loupež na');
define("build51", 'Založení vesnice');
define("build52", 'Dobrodružství');
define("build53", 'Oáza');
define("build54", 'Jednotky');
define("build55", 'Obchodníci');
define("build56", 'Dřevo');
define("build57", 'Železo');
define("build58", 'Obilí');
define("build59", 'Souřadnice');
define("build60", 'Hráč');
define("build61", 'trvání');
define("build62", 'Obchodníci');
define("build63", 'Poslat obchodníky');
define("build64", 'Obilí');
define("build65", 'Souřadnice');
define("build66", 'Každý obchodník unese');
define("build67", 'Poslat suroviny');
define("build68", 'Nebyly zvoleny souřadnice');
define("build69", 'Nemůžeš posílat suroviny do stejné vesnice');
define("build70", 'Hráč je zablokován. Suroviny mu poslat nemůžeš.');
define("build71", 'Nebyly zvoleny suroviny.');
define("build72", 'Zadej souřadnice nebo název vesnice.');
define("build73", 'Příliš málo obchodníků.');
define("build74", 'Přicházející obchodníci');
define("build75", 'Doprava z');
define("build76", 'Příchod za');
define("build77", 'Vlastní obchodníci na cestě');
define("build78", 'Doprava do');
define("build79", 'Příchod za');
define("build80", 'Surovina');
define("build81", 'Vracející se obchodníci');
define("build82", 'Návrat z');
define("build83", 'Příchod za');
define("build84", 'Surovina');
define("build85", 'Hledám');
define("build86", 'Nabízím');
define("build87", 'Nabídky na tržišti');
define("build88", 'Nabízí');
define("build89", 'mně');
define("build90", 'Hledá');
define("build91", 'ode mě');
define("build92", 'Hráč');
define("build93", 'Trvání');
define("build94", 'Akce');
define("build95", 'Nedostatek surovin');
define("build96", 'Nedostatek obchodníků');
define("build97", 'Přijmout nabídku');
define("build98", 'Na trhu nejsou žádné nabídky');
define("build99", 'Nabídka');
define("build100", 'Dřevo');
define("build101", 'Hlína');
define("build102", 'Železo');
define("build103", 'Obilí');
define("build104", 'max. doba dopravy');
define("build105", 'hodiny');
define("build106", 'Hledá se');
define("build107", 'jen vlastní aliance');
define("build108", 'Obchodníci');
define("build109", 'Prodat');
define("build110", 'Nabídnout');
define("build111", 'Aliance');
define("build112", 'Trvání');
define("build113", ' hod.');
define("build114", 'Vše');
define("build115", 'NPC obchod dokončen');
define("build116", 'Cena 3');
define("build117", 'Zpět k budově');
define("build118", 'Obchod s NPC');
define("build119", 'Směna surovin (krok 2 ze 2)');
define("build120", 'Náklady');
define("build121", 'Ve vesnici s Divem světa nelze obchodovat s NPC.');
define("build122", 'Start');
define("build123", 'Obchodníci');
define("build124", 'Akce');
define("build125", 'Žádné aktivní obchodní trasy');
define("build126", 'upravit');
define("build127", 'Vytvořit novou obchodní trasu');
define("build128", 'Vytvořit obchodní trasu');
define("build129", 'Cílová vesnice');
define("build130", 'Suroviny');
define("build131", 'Čas startu');
define("build132", 'Dodávky');
define("build133", 'Upravit obchodní trasu');
define("build134", 'Suroviny');
define("build135", 'Čas startu');
define("build136", 'Dodávky');
define("build137", 'Aliance');
define("build138", 'Zkratka');
define("build139", 'Jméno');
define("build140", 'Pozvánky');
define("build141", 'Přijmout');
define("build142", 'Žádné pozvánky nejsou k dispozici');
define("build143", 'založit alianci');
define("build144", 'Zkratka');
define("build145", 'Jméno');
define("build146", 'vytvořit');
define("build147", 'Cvičit');
define("build148", 'Výcvik');
define("build149", 'Nejsou dostupné žádné jednotky. Vyzkoumej je v akademii');
define("build150", 'K dispozici');
define("build151", 'vše');
define("build152", 'Jednotky je třeba vyzkoumat');
define("build153", 'Cvičit');
define("build154", 'Jednotky je třeba vyzkoumat');
define("build155", 'Výcvik');
define("build156", 'Trvání');
define("build157", 'Skončeno');
define("build158", 'Výzkum může začít po dokončení akademie');
define("build159", 'Rozšířit sklad surovin');
define("build160", 'Rozšířit<br>sklad');
define("build161", 'Rozšířit sýpku');
define("build162", 'Rozšířit<br>sýpku');
define("build163", 'Dost surovin');
define("build164", 'Produkce obilí je záporná, potřebných surovin nikdy nedosáhneš');
define("build165", 'Příliš málo');
define("build166", 'suroviny');
define("build167", 'Probíhá výzkum');
define("build168", 'Probíhá výzkum');
define("build169", 'Trvání');
define("build170", 'Dokončit');
define("build171", 'hod.');
define("build172", 'Aktuálně ukryto z každé suroviny');
define("build173", 'Ukryto z každé suroviny na úrovni');
define("build174", 'Slavnosti lze pořádat po dokončení radnice.');
define("build175", 'Slavnosti');
define("build176", 'Akce');
define("build177", 'probíhá');
define("build178", 'v');
define("build179", 'pořádat');
define("build180", 'Velká slavnost (2000 kulturních bodů)');
define("build181", 'Dokončit');
define("build182", 'Slavnost');
define("build183", 'Pro rozšiřování říše potřebuješ kulturní body. Získáváš je postupně ze svých budov – a čím vyšší úrovně, tím rychleji.');
define("build184", 'Produkce této vesnice');
define("build185", 'Kulturní body za den');
define("build186", 'Produkce všech vesnic');
define("build187", 'Kulturní body za den');
define("build188", 'Tvé vesnice vyprodukovaly');
define("build189", 'bodů celkem. K založení nebo dobytí nové vesnice potřebuješ');
define("build190", 'body.');
define("build191", 'Útoky senátorů, kmenových vůdců nebo náčelníků lze snížit loajalitu vesnice. Klesne-li na nulu, vesnice přejde do říše útočníka. Loajalita této vesnice je momentálně');
define("build192", 'procent');
define("build193", 'Vesnice založené nebo dobyté touto vesnicí');
define("build194", 'Vesnice');
define("build195", 'Hráč');
define("build196", 'Obyvatelé');
define("build197", 'Souřadnice');
define("build198", 'Datum');
define("build199", 'Tato vesnice zatím nezaložila ani nedobyla žádnou jinou vesnici.');
define("build200", 'výcvik');
define("build201", 'doba');
define("build202", 'hotovo');
define("build203", 'v');
define("build204", 'Cvičit');
define("build205", 'K založení nové vesnice potřebuješ rezidenci na úrovni 10 nebo 20 a 3 osadníky. K dobytí nové vesnice potřebuješ rezidenci na úrovni 10 nebo 20 a senátora, náčelníka nebo stařešinu.');
define("build206", 'heslo je špatně');
define("build207", 'Toto je tvé hlavní město');
define("build208", 'změnit hlavní město');
define("build209", 'Opravdu chceš změnit hlavní město?<br/><b>Tuto akci nelze vrátit!</b><br/>Z bezpečnostních důvodů zadej pro potvrzení své heslo');
define("build210", 'Změnit');
define("build211", 'Palác se staví');
define("build212", 'K založení nové vesnice potřebuješ palác na úrovni 10, 15 nebo 20 a 3 osadníky. K dobytí nové vesnice potřebuješ palác na úrovni 10, 15 nebo 20 a senátora, náčelníka nebo stařešinu.');
define("build213", 'Tvé artefakty');
define("build214", 'Název');
define("build215", 'Vesnice');
define("build216", 'Získáno');
define("build217", 'Nemáš žádné artefakty');
define("build218", 'Diamantové dláto');
define("build219", 'Budovy jsou odolnější proti útokům katapultů a beranidel. Neplatí pro Div světa, ale artefakty s účinkem na celý účet a unikátní artefakty platí pro všechny ostatní budovy ve vesnici s Divem světa.');
define("build220", 'Obří mramorové kladivo');
define("build221", 'Budovy jsou odolnější proti útokům katapultů a beranidel. Neplatí pro Div světa, ale artefakty s účinkem na celý účet a unikátní artefakty platí pro všechny ostatní budovy ve vesnici s Divem světa.');
define("build222", 'Hemonovy svitky');
define("build223", 'Budovy jsou odolnější proti útokům katapultů a beranidel. Neplatí pro Div světa, ale artefakty s účinkem na celý účet a unikátní artefakty platí pro všechny ostatní budovy ve vesnici s Divem světa.');
define("build224", 'Opálová podkova');
define("build225", 'Jednotky se pohybují rychleji.');
define("build226", 'Zlatý vůz');
define("build227", 'Jednotky se pohybují rychleji.');
define("build228", 'Feidippidovy sandály');
define("build229", 'Jednotky se pohybují rychleji.');
define("build230", 'Příběh krysy');
define("build231", 'Zvědové, Equites Legati a slídiči lépe špehují a lépe se brání proti špehování. Účinek platí pro všechny zvědy ve vesnici/účtu i pro zvědy vyslané na průzkum z této vesnice/účtu. Netýká se zvědů poslaných jako podpora do vesnic, na které artefakt nepůsobí. Navíc na shromaždišti uvidíš typ útočících jednotek, ale ne jejich počet.');
define("build232", 'Generálův dopis');
define("build233", 'Zvědové, Equites Legati a slídiči lépe špehují a lépe se brání proti špehování. Účinek platí pro všechny zvědy ve vesnici/účtu i pro zvědy vyslané na průzkum z této vesnice/účtu. Netýká se zvědů poslaných jako podpora do vesnic, na které artefakt nepůsobí. Navíc na shromaždišti uvidíš typ útočících jednotek, ale ne jejich počet.');
define("build234", 'Deník Sun-c’');
define("build235", 'Zvědové, Equites Legati a slídiči lépe špehují a lépe se brání proti špehování. Účinek platí pro všechny zvědy ve vesnici/účtu i pro zvědy vyslané na průzkum z této vesnice/účtu. Netýká se zvědů poslaných jako podpora do vesnic, na které artefakt nepůsobí. Navíc na shromaždišti uvidíš typ útočících jednotek, ale ne jejich počet.');
define("build236", 'Stříbrný podnos');
define("build237", 'Jednotky spotřebují méně obilí.');
define("build238", 'Posvátný lovecký luk');
define("build239", 'Jednotky spotřebují méně obilí.');
define("build240", 'Kalich krále Artuše');
define("build241", 'Jednotky spotřebují méně obilí.');
define("build242", 'Sepsaná vojenská přísaha');
define("build243", 'Jednotky se cvičí rychleji.');
define("build244", 'Vyhlášení války');
define("build245", 'Jednotky se cvičí rychleji.');
define("build246", 'Paměti Alexandra Velikého');
define("build247", 'Jednotky se cvičí rychleji.');
define("build248", 'Plán velkého skladu nebo velké sýpky');
define("build249", 'Umožní postavit velký sklad nebo velkou sýpku.');
define("build250", 'Přístup k budovám');
define("build251", 'Mapa skrytých jeskyní');
define("build252", 'Artefakt zvyšuje kapacitu úkrytu a navíc způsobí, že nepřátelské katapulty střílejí náhodně. Div světa lze zasáhnout vždy.');
define("build253", 'Bezedná brašna');
define("build254", 'Artefakt zvyšuje kapacitu úkrytu a navíc způsobí, že nepřátelské katapulty střílejí náhodně. Div světa lze zasáhnout vždy.');
define("build255", 'Trojský kůň');
define("build256", 'Artefakt zvyšuje kapacitu úkrytu a navíc způsobí, že nepřátelské katapulty střílejí náhodně. Div světa lze zasáhnout vždy.');
define("build257", 'Přívěsek nezbedy');
define("build258", 'Tento artefakt mění svůj účinek každých 24 hodin a může získat účinek kteréhokoli jiného artefaktu kromě stavebních plánů Divu světa, velkých sýpek a velkých skladů. Každých 24 hodin se také náhodně určí, zda působí na vesnici, nebo na celý účet.');
define("build259", 'Náhodný');
define("build260", 'Zakázaný rukopis');
define("build261", 'Tento artefakt mění svůj účinek každých 24 hodin a může získat účinek kteréhokoli jiného artefaktu kromě stavebních plánů Divu světa, velkých sýpek a velkých skladů. Každých 24 hodin se také náhodně určí, zda působí na vesnici, nebo na celý účet.');
define("build262", 'Náhodný');
define("build263", 'Stavební svitek Divu světa');
define("build264", 'Artefakt potřebný ke stavbě Divu světa');
define("build265", 'Přístup k budovám');
define("build266", 'Vesnice');
define("build267", 'Účet');
define("build268", 'Pokladnice');
define("build269", 'Vliv');
define("build270", 'Nejbližší artefakty');
define("build271", 'Název');
define("build272", 'Hráč');
define("build273", 'Vzdálenost');
define("build274", 'Žádné nejbližší artefakty.');
define("build275", 'Diamantové dláto');
define("build276", 'Obří mramorové kladivo');
define("build277", 'Hemonovy svitky');
define("build278", 'Opálová podkova');
define("build279", 'Zlatý vůz');
define("build280", 'Feidippidovy sandály');
define("build281", 'Příběh krysy');
define("build282", 'Generálův dopis');
define("build283", 'Deník Sun-c’');
define("build284", 'Sepsaná vojenská přísaha');
define("build285", 'Vyhlášení války');
define("build286", 'Paměti Alexandra Velikého');
define("build287", 'Plán velkého skladu nebo velké sýpky');
define("build288", 'Přístup k budovám');
define("build289", 'Stavební svitek Divu světa');
define("build290", 'Přístup k budovám');
define("build291", 'Vesnice');
define("build292", 'Účet');
define("build293", 'Vzdálenost');
define("build294", 'Účinek');
define("build295", 'Malé artefakty');
define("build296", 'Název');
define("build297", 'Hráč');
define("build298", 'Aliance');
define("build299", 'Nejsou žádné artefakty.');
define("build300", 'Diamantové dláto');
define("build301", 'Opálová podkova');
define("build302", 'Příběh krysy');
define("build303", 'Sepsaná vojenská přísaha');
define("build304", 'Plán velkého skladu nebo velké sýpky');
define("build305", 'Přístup k budovám');
define("build306", 'Stavební svitek Divu světa');
define("build307", 'Přístup k budovám');
define("build308", 'Pokladnice');
define("build309", 'Účinek');
define("build310", 'Vesnice');
define("build311", 'Velké artefakty');
define("build312", 'Název');
define("build313", 'Hráč');
define("build314", 'Aliance');
define("build315", 'Obří mramorové kladivo');
define("build316", 'Hemonovy svitky');
define("build317", 'Zlatý vůz');
define("build318", 'Feidippidovy sandály');
define("build319", 'Generálův dopis');
define("build320", 'Deník Sun-c’');
define("build321", 'Vyhlášení války');
define("build322", 'Paměti Alexandra Velikého');
define("build323", 'Plán velkého skladu nebo velké sýpky');
define("build324", 'Přístup k budovám');
define("build325", 'Účet');
define("build326", 'Nejsou žádné artefakty.');
define("build327", 'Pokladnice');
define("build328", 'Účinek');
define("build329", 'Malé artefakty');
define("build330", 'Velké artefakty');
define("build331", 'Vesnice');
define("build332", 'Účet');
define("build333", 'Neaktivní');
define("build334", 'Aktivní');
define("build335", 'Diamantové dláto');
define("build336", 'Budovy jsou odolnější proti útokům katapultů a beranidel. Neplatí pro Div světa, ale artefakty s účinkem na celý účet a unikátní artefakty platí pro všechny ostatní budovy ve vesnici s Divem světa.');
define("build337", 'Obří mramorové kladivo');
define("build338", 'Budovy jsou odolnější proti útokům katapultů a beranidel. Neplatí pro Div světa, ale artefakty s účinkem na celý účet a unikátní artefakty platí pro všechny ostatní budovy ve vesnici s Divem světa.');
define("build339", 'Hemonovy svitky');
define("build340", 'Budovy jsou odolnější proti útokům katapultů a beranidel. Neplatí pro Div světa, ale artefakty s účinkem na celý účet a unikátní artefakty platí pro všechny ostatní budovy ve vesnici s Divem světa.');
define("build341", 'Opálová podkova');
define("build342", 'Jednotky se pohybují rychleji.');
define("build343", 'Zlatý vůz');
define("build344", 'Jednotky se pohybují rychleji.');
define("build345", 'Feidippidovy sandály');
define("build346", 'Jednotky se pohybují rychleji.');
define("build347", 'Příběh krysy');
define("build348", 'Zvědové, Equites Legati a slídiči lépe špehují a lépe se brání proti špehování. Účinek platí pro všechny zvědy ve vesnici/účtu i pro zvědy vyslané na průzkum z této vesnice/účtu. Netýká se zvědů poslaných jako podpora do vesnic, na které artefakt nepůsobí. Navíc na shromaždišti uvidíš typ útočících jednotek, ale ne jejich počet.');
define("build349", 'Generálův dopis');
define("build350", 'Zvědové, Equites Legati a slídiči lépe špehují a lépe se brání proti špehování. Účinek platí pro všechny zvědy ve vesnici/účtu i pro zvědy vyslané na průzkum z této vesnice/účtu. Netýká se zvědů poslaných jako podpora do vesnic, na které artefakt nepůsobí. Navíc na shromaždišti uvidíš typ útočících jednotek, ale ne jejich počet.');
define("build351", 'Deník Sun-c’');
define("build352", 'Zvědové, Equites Legati a slídiči lépe špehují a lépe se brání proti špehování. Účinek platí pro všechny zvědy ve vesnici/účtu i pro zvědy vyslané na průzkum z této vesnice/účtu. Netýká se zvědů poslaných jako podpora do vesnic, na které artefakt nepůsobí. Navíc na shromaždišti uvidíš typ útočících jednotek, ale ne jejich počet.');
define("build353", 'Sepsaná vojenská přísaha');
define("build354", 'Jednotky se cvičí rychleji.');
define("build355", 'Vyhlášení války');
define("build356", 'Paměti Alexandra Velikého');
define("build357", 'Plán velkého skladu nebo velké sýpky');
define("build358", 'Umožní postavit velký sklad nebo velkou sýpku.');
define("build359", 'Přístup k budovám');
define("build360", 'Stavební svitek Divu světa');
define("build361", 'Artefakt potřebný ke stavbě Divu světa');
define("build362", 'Přístup k budovám');
define("build363", 'Vlastník');
define("build364", 'Vesnice');
define("build365", 'Aliance');
define("build366", 'Účinek');
define("build367", 'Bonus');
define("build368", 'Aktivita');
define("build369", 'Uloženo v');
define("build370", 'Pokladnice');
define("build371", 'Úroveň');
define("build372", 'Získáno');
define("build373", 'Aktuální nosnost obchodníka');
define("build374", 'Nosnost obchodníka na úrovni');
define("build375", 'Nosnost obchodníka na úrovni 20');
define("build376", 'Cvičit');
define("build377", 'Výcvik může začít po dokončení velkých kasáren.');
define("build378", 'K dispozici');
define("build379", 'Výcvik může začít po dokončení velké stáje.');
define("build380", 'Výcvik');
define("build381", 'Trvání');
define("build382", 'Skončeno');
define("build383", 'Nejdřív vyzkoumej jízdu v akademii.');
define("build384", 'Aktuální bonus odolnosti');
define("build385", 'Aktuální bonus');
define("build386", 'Bonus na úrovni');
define("build387", 'Slavnost');
define("build388", 'Akce');
define("build389", 'Svátek');
define("build390", 'po dobu');
define("build391", 'Surovin bude dost');
define("build392", 'Nedostatek obilí.');
define("build393", 'Nedostatek');
define("build394", 'suroviny');
define("build395", 'slavit');
define("build396", 'Svátek trvá');
define("build397", 'Bude dokončeno');
define("build398", 'Aktuální max. počet pastí k výrobě');
define("build399", 'Pasti');
define("build400", 'Max. počet pastí k výrobě na úrovni');
define("build401", 'Max. počet pastí k výrobě na úrovni 20');
define("build402", 'Momentálně máš');
define("build403", 'K dispozici');
define("build404", 'Vyrobit');
define("build405", 'Výroba může začít po dokončení pastičkáře.');
define("build406", 'Oázu obsadil');
define("build407", 'vesnice');
define("build408", 'Typ');
define("build409", 'Loajalita');
define("build410", 'Dobyto');
define("build411", 'Souřadnice');
define("build412", 'Suroviny');
define("build413", '1. další oáza od hrdinského dvora úrovně 10');
define("build414", '2. další oáza od hrdinského dvora úrovně 15');
define("build415", '3. další oáza od hrdinského dvora úrovně 20');
define("build416", '2. další oáza od hrdinského dvora úrovně 15');
define("build417", '3. další oáza od hrdinského dvora úrovně 20');
define("build418", '3. další oáza od hrdinského dvora úrovně 20');
define("build419", 'Ostatní oázy');
define("build420", 'Vlastník');
define("build421", 'Vesnice');
define("build422", 'Souřadnice');
define("build423", 'Surovina');
define("build424", 'Současná kapacita');
define("build425", 'jednotky');
define("build426", 'Kapacita na úrovni');
define("build427", 'Cena');
define("build428", 'Stavitelé pracují');
define("build429", 'Nedostatek jídla: nejdřív rozšiř obilné pole!');
define("build430", 'Rozšiř sklad surovin.');
define("build431", 'Rozšiř sýpku.');
define("build432", 'Dostatek surovin');
define("build433", 'Postavit budovu');
define("build434", 'Fronta');
define("build435", 'Postavit budovu');
define("build436", 'Stavitel');
define("build437", 'Stavba nové budovy');
define("build438", 'Zobrazit stavby dostupné brzy');
define("build439", 'Požadavky');
define("build440", 'Úroveň');
define("build441", 'Budova je na maximální úrovni.');
define("build442", 'Postavena maximální úroveň.');
define("build443", 'Budova se bourá.');
define("build444", 'Náklady');
define("build445", 'pro stavbu na úroveň');
define("build446", 'Všichni stavitelé jsou zaneprázdnění');
define("build447", 'Všichni stavitelé jsou zaneprázdnění. (Fronta)');
define("build448", 'Nedostatek jídla. Postav víc obilných polí.');
define("build449", 'Postav sklad surovin.');
define("build450", 'Postav sýpku.');
define("build451", 'Surovin nikdy nebude dost');
define("build452", 'dost surovin');
define("build453", 'Zvýšit úroveň');
define("build454", 'tah');
define("build455", 'Stavitel');
define("build456", 'Div světa');
define("build457", 'Úroveň');
define("build459", 'Pro změnu názvu Divu světa musí mít Div světa alespoň úroveň 1.');
define("build460", 'Název Divu světa');
define("build461", 'Po úrovni 10 už název Divu světa nelze změnit.');
define("build462", 'Název změněn.');
define("build463", 'Budova je na maximální úrovni.');
define("build464", 'Postavena maximální úroveň.');
define("build465", 'Budova se bourá.');
define("build466", 'Náklady');
define("build467", 'pro stavbu na úroveň');
define("build468", 'Všichni stavitelé jsou zaneprázdnění.');
define("build469", 'Všichni stavitelé jsou zaneprázdnění. (Fronta)');
define("build470", 'Nedostatek jídla. Postav víc obilných polí.');
define("build471", 'Postav sklad surovin.');
define("build472", 'Postav sýpku.');
define("build473", 'dost surovin');
define("build474", 'za');
define("build475", 'Zvýšit úroveň');
define("build476", 'Stavitel');
define("build477", 'Je potřeba stavební plán Divu světa.');
define("build478", 'Je potřeba další stavební plán Divu světa.');

define("dorf31", 'Vesnice');
define("dorf32", 'Vesnice');
define("dorf33", 'Budova');
define("dorf34", 'Jednotky');
define("dorf35", 'Obchodníci');
define("dorf36", 'Suroviny');
define("dorf37", 'Součet');
define("dorf38", 'Sklad surovin');
define("dorf39", 'Kulturní body');
define("dorf310", 'KB/den');
define("dorf311", 'Slavnosti');
define("dorf312", 'Jednotky');
define("dorf313", 'Místa');
define("dorf314", 'Vlastní jednotky');
define("dorf315", 'KB');
define("dorf316", 'Útoky');

define("gclub0", 'Vesnice');
define("gclub1", 'v');
define("gclub2", 'Vzdálenost');
define("gclub3", 'Jednotky');
define("gclub4", 'Poslední loupež');
define("gclub5", 'Neexistuje žádný seznam loupeží.');
define("gclub6", 'Oáza');
define("gclub7", 'upravit');
define("gclub8", 'Vybrat vše');
define("gclub9", 'Přidat loupež');
define("gclub10", 'Spustit loupež');
define("gclub11", 'opravdu chceš tento seznam smazat?'); //farmlist.php line 225, not sure if it will work
define("gclub12", 'Podrobnosti');
define("gclub13", 'Vytvořit nový seznam');
define("gclub14", 'Jméno');
define("gclub15", 'Vesnice');
define("gclub16", 'Vytvořit');
define("gclub17", 'Na těchto souřadnicích není žádná vesnice.');
define("gclub18", 'Do seznamu nemůžeš přidat vesnici, ve které se nacházíš.');
define("gclub19", 'Nebyly vybrány žádné jednotky.');
define("gclub20", 'Zadej správné souřadnice.');
define("gclub21", 'Přidat položku');
define("gclub22", 'Název farmy');
define("gclub23", 'Vyber cíl');
define("gclub24", 'Tohle není tvůj seznam!');
define("gclub25", 'Na těchto souřadnicích není žádná vesnice.');
define("gclub26", 'Do seznamu nemůžeš přidat vesnici, ve které se nacházíš.');
define("gclub27", 'Zadej správné souřadnice.');
define("gclub28", 'Nebyly vybrány žádné jednotky.');
define("gclub29", 'Zadej správné souřadnice.');
define("gclub30", 'Smazat');

define("map1", 'Vystředit mapu');
define("map2", 'Založit novou vesnici');
define("map3", 'Poslat jednotky');
define("map4", 'Rozložení:');
define("map5", 'Jednotky');
define("map6", 'Hráč');
define("map7", 'Národ');
define("map8", 'Aliance');
define("map9", 'Vlastník');
define("map10", 'Obyvatelé');
define("map11", 'Hlášení');

define("msg0", 'Předmět');
define("msg1", 'Hráč');
define("msg2", 'Odeslané');
define("msg3", 'Nejsou k dispozici žádné zprávy.');
define("msg4", 'Označit vše');
define("msg5", 'Smazat');
define("msg6", 'Doručené');
define("msg7", 'Napsat');
define("msg8", 'Odeslané');
define("msg9", 'Žádná zpráva.');
define("msg10", 'Příjemce');
define("msg11", 'Předmět');
define("msg12", 'Odeslat');

define("notice0", 'z vesnice');
define("notice1", 'Jednotky');
define("notice2", 'ztraceno');
define("notice3", 'zajatci');
define("notice4", 'Informace');
define("notice5", 'obrana');
define("notice6", 'posily');
define("notice7", 'útok');
define("notice8", 'z vesnice');
define("notice9", 'Odesláno');
define("notice10", 'za');
define("notice11", 'odesílatel');
define("notice12", 'z vesnice');
define("notice13", 'Jednotky');
define("notice14", 'Jednotky');
define("notice15", 'Informace');
define("notice16", 'Suroviny získané z');
define("notice17", 'Odesláno');
define("notice18", 'z vesnice');
define("notice19", 'Suroviny');
define("notice20", 've vesnici');
define("notice21", 'zvědové');
define("notice22", 'Žádná hlášení');
define("notice23", 'Označit vše');
define("notice24", 'Smazat');

define("plus0", 'Koupit zlato');
define("plus1", 'Cena');
define("plus2", 'počet');
define("plus3", 'Koupit');
define("plus4", 'Tarif');
define("plus5", 'Koupit');
define("plus6", 'SPECIÁLNÍ');
define("plus7", 'Cena zlata');
define("plus8", 'Cena');
define("plus9", 'Množství');
define("plus10", 'Koupit (PayPal)');
define("plus11", 'Tarif');
define("plus12", 'Koupit');
define("plus13", 'Nákup zlata na jinou částku (UnitPay)');
define("plus14", "Сумма платежа");
define("plus15", "Итого");
define("plus16", '*Při nákupu zlata nad 300 rublů je zlato výhodnější než podle tarifů!');
define("plus17", 'S dotazy se obracej na');
define("plus18", "Администратору");
define("plus19", 'Po nákupu zlata je třeba přejít do');
define("plus20", "Банк");
define("plus21", 'Tam můžeš zlato převést zčásti nebo celé na libovolný účet na dostupném serveru.');
define("plus22", "рублей");
define("plus23", 'Banka (mini verze)');
define("plus24", 'Zde se uchovává koupené či převedené zlato z minulých kol (pokud zůstatek přesáhl 100 mincí).<br />Můžeš ho převést na <s>jakýkoli jiný účet</s> <b>svůj účet</b> (mini verze) v rámci serveru');
define("plus25", 'Zadej svůj kód pro převod zlata.');
define("plus26", '(Byl ti zaslán e-mailem.)');
define("plus27", 'E-mail nenalezen!');
define("plus28", "Код принят!");
define("plus29", 'K převodu je k dispozici');
define("plus30", "золота");
define("plus31", 'Zlato k převodu');
define("plus32", 'Herní přezdívka');
define("plus33", 'Neplatný kód!');
define("plus34", "Что-то пошло не так.<br />Попробуйте ещё раз.");
define("plus35", 'Hráč');
define("plus36", 'Nalezeno');
define("plus37", 'ID hráče');
define("plus38", 'Bude převedeno');
define("plus39", "золота");
define("plus40", "Игрок не найден");
define("plus41", "Назад");
define("plus42", "Что-то пошло не так.<br />Попробуйте ещё раз.");
define("plus43", 'Zlato bylo úspěšně převedeno!');
define("plus44", "Что-то пошло не так.<br />Попробуйте ещё раз.");
define("plus45", 'E-mail');
define("plus46", 'Plná verze');
define("plus47", 'Pozvi přátele a vyhraj zlato!');
define("plus48", 'Pošli příteli svůj osobní referenční odkaz');
define("plus49", 'Podmínky pro získání odměny');
define("plus50", '1. Jakmile populace říše pozvaného hráče přesáhne ');
define("plus51", 'můžeš si vyzvednout');
define("plus52", 'zlata kliknutím na ikonu');
define("plus53", '2. Nesmíš být zástupcem pozvaného hráče.');
define("plus54", "Приглашенные Игроки");
define("plus55", "Игрок");
define("plus56", "Дата Регистрации");
define("plus57", "Население");
define("plus58", 'Vesnice');
define("plus59", "Забрать");
define("plus60", 'Zatím jsi nepřivedl žádné nové hráče.');
define("plus61", 'Nejlepší doporučitelé');
define("plus62", "Место");
define("plus63", "Игрок");
define("plus64", 'Směnárna');
define("plus65", 'Zadej množství zlata nebo stříbra, které chceš vyměnit');
define("plus66", 'Směnné kurzy');
define("plus67", '1 zlato : 100 stříbra<br>2000 stříbra : 1 zlato');
define("plus68", 'směnit');
define("plus69", 'Travian');
define("plus74", "Купить Золото");
define("plus75", "Функции");
define("plus76", "Заработать золото");
define("plus77", "Банк");

define("other0", 'Byl jsi zablokován. Napiš administrátorovi');
define("other1", "xTravian.net");
define("other2", 'Dokončit');
define("other3", 'Dokončeno v');
define("other4", 'Úroveň');
define("other5", 'Vnější stavební místo');
define("other6", 'Div světa');
define("other7", 'Stavební místo');
define("other8", 'Místo pro shromaždiště');
define("other9", 'Stavební místo');
define("other10", 'VKontakte');
define("other11", 'Facebook');
define("other12", 'Dobrodružství');
define("other13", 'hodin ochrany.');
define("other14", 'Účet bude smazán za');
define("other15", "Артефакты");
define("other16", 'Div vesnice');
define("other17", 'Svitky přes');
define("other18", "Изменить Язык на Русский");
define("other19", 'Přepnout jazyk na angličtinu');
define("other20", 'Produkce');
define("other21", 'Žádné');
define("other22", "Щелкните для");
define("other23", 'stavby kliknutím na pole');

//пункт сбора
define("punktxuev0", 'Suroviny');
define("punktxuev1", 'Infrastruktura');
define("punktxuev2", 'Vojenské');
define("punktxuev3", 'Potvrdit');
define("punktxuev4", 'Vyrazit na dobrodružství');
define("punktxuev5", 'Jednotky');
define("punktxuev6", 'Příchod');
define("punktxuev7", 'Tvůj hrdina ještě není v této vesnici.');
define("punktxuev8", 'Tvůj hrdina je mrtvý.');
define("punktxuev9", 'Musíš postavit shromaždiště.');
define("punktxuev10", 'Poslat domů');
define("punktxuev11", 'Poslat tyto jednotky zpět');
define("punktxuev12", 'Jednotky');
define("punktxuev13", 'čas');
define("punktxuev14", 'Nedostatek jednotek!');

//актиinация
define("activate0", 'Pro hraní musíš aktivovat svůj účet.');
define("activate1", 'Aktivační kód:	');
define("activate2", 'Aktivovat a začít hrát');
define("activate3", 'Překlep v e-mailu nebo jménu?');
define("activate4", 'Zde můžeš registraci zrušit a zaregistrovat se znovu.');
define("activate5", 'Tvůj e-mail:');
define("activate6", 'Tvé jméno:');
define("activate7", 'Tvé heslo:');
define("activate8", 'Zkontroluj správnost zadaných údajů.');
define("activate9", 'Ujisti se, že účet ještě nebyl aktivován.');
define("activate10", 'Odeslat');
define("activate11", 'Nebo');

//альянс
define("ally0", 'Pozvánky');
define("ally1", 'Žádné pozvánky');
define("ally2", 'Pozvánka pro');
define("ally3", 'Jít');
define("ally4", 'Podrobnosti');
define("ally5", 'Pozice');
define("ally6", 'Členové');
define("ally7", 'Odejít');
define("ally8", 'Novinky');
define("ally9", 'Útoky');
define("ally10", 'Možnosti');
define("ally11", 'Přidělit');
define("ally12", 'Žádné');
define("ally13", "");
define("ally14", "");

//фармлист
define("farmlist0", 'Vesnice');
define("farmlist1", 'Podrobnosti');
define("farmlist2", "");
define("farmlist3", 'Vzdálenost');
define("farmlist4", 'Jednotky');
define("farmlist5", 'Poslední loupež');
define("farmlist6", 'Přidat loupež');
define("farmlist7", 'Spustit loupež');
define("farmlist8", 'opravdu chceš tento seznam smazat?');
define("farmlist9", 'Vytvořit nový seznam');
define("farmlist10", 'Neexistuje žádný seznam loupeží.');
define("farmlist11", 'Jméno:');
define("farmlist12", 'Vytvořit');
define("farmlist13", 'Tohle není tvůj seznam, pane!');
define("farmlist14", 'Na těchto souřadnicích není žádná vesnice.');
define("farmlist15", 'Nebyly vybrány žádné jednotky.');
define("farmlist16", 'Zadej správné souřadnice.');
define("farmlist17", 'Přidat položku');
define("farmlist18", 'Název farmy:');
define("farmlist19", 'Vyber cíl:');
define("farmlist20", 'Smazat');
define("farmlist21", 'Divočina');

//dorf3
define("dorf0", 'Přehled');
define("dorf1", 'Suroviny');
define("dorf2", 'Sklad surovin');
define("dorf3", 'Kulturní body');
define("dorf4", 'Jednotky');
define("dorf5", 'Vesnice');
define("dorf6", 'Útoky');
define("dorf7", 'Budova');
define("dorf8", 'Jednotky');
define("dorf9", 'Obchodníci');
define("dorf10", 'Součet');
define("dorf11", 'KB/den');
define("dorf12", 'Slavnosti');
define("dorf13", 'Místa');
define("dorf14", 'Vlastní jednotky');
define("dorf15", "");
define("dorf16", "");
define("dorf17", "");
define("dorf18", "");
define("dorf19", "");
define("dorf20", "");
define("dorf21", "");
define("dorf22", "");
define("dorf23", "");
define("dorf24", "");
define("dorf25", "");
define("dorf26", "");
define("dorf27", "");
define("dorf28", "");
define("dorf29", "");
define("dorf30", "");


//makegold in плюсе
define("mkg0", 'Pozvi přátele a získej zlato!');
define("mkg1", 'Jak na to?');
define("mkg2", 'Pošli příteli');
define("mkg3", 'Tvůj osobní odkaz pro doporučení');
define("mkg4", 'Podmínky pro získání odměny:');
define("mkg5", '1. Jakmile populace říše pozvaného hráče přesáhne');
define("mkg6", ', můžeš si vyzvednout');
define("mkg7", 'zlata kliknutím na ikonu');
define("mkg8", '2. Nesmíš být zástupcem pozvaného hráče.');
define("mkg9", 'pozvaní hráči:');
define("mkg10", 'Hráč');
define("mkg11", 'Datum registrace');
define("mkg12", 'Obyvatelé');
define("mkg13", 'Vesnice');
define("mkg14", 'vyzvednout');
define("mkg15", 'Zatím jsi nepozval žádné nové hráče.');

//сообщения
define("MSG0", 'Předmět');
define("MSG1", 'Hráč');
define("MSG2", 'Datum');
define("MSG3", 'Nejsou k dispozici žádné zprávy');
define("MSG4", 'Označit vše');
define("MSG5", 'Smazat');
define("MSG6", 'Doručené');
define("MSG7", 'Napsat');
define("MSG8", 'Odeslané');
define("MSG9", 'Datum odeslání');
define("MSG10", 'Odpovědět');
define("MSG11", 'Návrat');
define("MSG12", 'Příjemce:');
define("MSG13", 'Odeslat');


//TAINIK
define("TA", ' Úkryt úroveň  ');
define("TA1", ' Úkryt slouží ke schování části surovin při napadení vesnice. Tyto suroviny nemohou být ukradeny. ');
define("TA2", ' Úkryt ');
define("TA3", ' jednotky');
define("TA4", ' Úkryt na úrovni ');
define("TA6", ' Cena stavby na úroveň ');
define("TA7", ' Rozšířit na úroveň ');
define("newrpt", 'Nová hlášení:');

//SOKR
define("sokr0", 'Vlastník');
define("sokr1", 'Vesnice');
define("sokr2", 'Aliance');
define("sokr3", 'Účinek');
define("sokr4", 'Bonus');
define("sokr5", 'Aktivita');
define("sokr6", 'Uloženo v:');
define("sokr7", 'Pokladnice');
define("sokr8", 'úr.');
define("sokr9", 'Získáno');
define("sokr10", 'Tvůj artefakt ');
define("sokr11", 'Jméno');
define("sokr12", 'Nemáš žádný artefakt.');
define("sokr13", 'Nejbližší artefakty');
define("sokr14", 'Hráč');
define("sokr15", 'Vzdálenost');
define("sokr16", 'Žádné nejbližší artefakty.');
define("sokr17", 'Pokladnice');
define("sokr18", 'Malý artefakt');
define("sokr19", 'Nejsou žádné artefakty.');
define("sokr20", 'Velké artefakty ');
define("sokr21", 'Neaktivní.');
define("sokr22", 'Aktivní');

//taverna
define("TVRN0", 'Oázu obsadil');
define("TVRN1", "");
define("TVRN2", 'Typ');
define("TVRN3", 'Loajalita');
define("TVRN4", 'Dobyto');
define("TVRN5", 'Souřadnice');
define("TVRN6", 'Suroviny');
define("TVRN7", 'Další oáza od hrdinského dvora úrovně 10');
define("TVRN8", 'Další oáza od hrdinského dvora úrovně 15');
define("TVRN9", 'Další oáza od hrdinského dvora úrovně 20');
define("TVRN10", 'Ostatní oázy ');
define("TVRN11", 'Vlastník');
define("TVRN12", 'Dřevo');
define("TVRN13", 'Hlína');
define("TVRN14", 'Železo');
define("TVRN15", 'Obilí');

//отчеты
define("rpts0", 'Jednotky');
define("rpts1", 'Ztráty');
define("rpts2", 'Získáno');
define("rpts3", 'Informace');
define("rpts4", 'z vesnice');
define("rpts5", 'Posily');
define("rpts6", 'Předmět');
define("rpts7", 'posílá suroviny do');
define("rpts8", 'Odeslal');
define("rpts9", 'Obrana');
define("rpts10", 've vesnici');
define("rpts11", 'Útok');
define("rpts12", 'zvědové');
define("rpts13", '(nové)');
define("rpts14", 'Žádná hlášení.');
define("rpts15", 'odesílatel');
define("ot4m0", 'Vše');
define("ot4m1", 'Útok');
define("ot4m2", 'Posily');
define("ot4m3", 'Různé');
define("ot4m4", 'Obchod');
define("XUYXUYXUY", 'Hlášení');
define("REPORT_TODAY", 'Dnes');
define("REPROT_YESTERDAY", 'Včera');
define("len0", 'Sklad surovin');
define("len1", 'Vesnice');

//рынок
define("MERCHANTS", 'Obchodníci');
define("IMSEARCHING", 'Hledám');
define("IMOFFERING", 'Nabízím');
define("OFFEREDONTHEMARKET", 'Nabízím');
define("market0", 'Obchodní trasy');
define("market1", 'Nabídky na tržišti');
define("market2", 'Nabízí');
define("market3", 'mně');
define("market4", 'Hledá');
define("market5", 'ode mě');
define("market6", 'Hráč');
define("market7", 'Trvání');
define("market8", 'Akce');
define("market9", 'Nedostatek surovin');
define("market10", 'Nedostatek obchodníků');
define("market11", 'Přijmout');
define("market12", 'Na trhu nejsou žádné nabídky');
define("market13", 'Poslat obchodníky');
define("market14", 'Každý obchodník unese');
define("market15", 'jednotek suroviny');
define("market16", 'Nebyly zvoleny souřadnice');
define("market17", 'Nemůžeš posílat suroviny do stejné vesnice');
define("market18", 'Hráč je zablokován. Suroviny mu poslat nemůžeš.');
define("market19", 'Nebyly zvoleny suroviny.');
define("market20", 'Zadej souřadnice nebo název vesnice.');
define("market21", 'Příliš málo obchodníků.');
define("market22", 'Přicházející obchodníci');
define("market23", 'Příchod za');
define("market24", 'Surovina');
define("market25", 'Vlastní obchodníci na cestě:');
define("market26", 'Vracející se obchodníci');
define("market27", 'Nabídka');
define("market28", 'Hledá se');
define("market29", 'max. doba dopravy:');
define("market30", 'hodiny');
define("market31", 'jen vlastní aliance');
define("market32", 'Prodat');
define("market33", 'Vlastní nabídky');
define("market34", 'Nabídnout');
define("market35", 'Hledat');
define("market36", 'Aliance');
define("market37", 'hod.');
define("market38", 'Vše');
define("market39", 'NPC obchod dokončen.');
define("market40", 'Cena');
define("market41", 'Zpět k budově');
define("market42", 'Rozdělení surovin (krok 1 ze 2)');
define("market43", 'Směna surovin (krok 2 ze 2)');
define("market44", 'Ve vesnici s Divem světa nelze obchodovat s NPC.');
define("market45", 'Start');
define("market46", 'Žádné aktivní obchodní trasy.');
define("market47", 'Obchodní trasa do');
define("market48", 'upravit');
define("market49", 'Vytvořit novou obchodní trasu');
define("market50", 'Suroviny');
define("market51", 'Čas startu');
define("market52", 'Dodávky');
define("market53", 'Upravit obchodní trasu');
define("market54", 'Cílová vesnice');

define("anlm0", 'Zadej své jméno');
define("anlm1", 'Zadej svůj e-mail');
define("anlm2", 'Zadej své heslo');

define("upgra0", 'Cena:');
define("upgra1", 'Stavitelé pracují.');
define("upgra2", 'Nedostatek jídla: nejdřív rozšiř obilné pole!');
define("upgra3", 'Rozšiř sklad surovin.');
define("upgra4", 'Rozšiř sýpku.');
define("upgra5", 'Dostatek surovin');
define("upgra6", 'Postavit budovu');
define("upgra7", '(Fronta)');
define("upgra8", 'Stavitel');
define("upgra9", '(cena:');

//world wonder
define("ww0", 'Div světa je stejně úžasný, jak zní. Staví se proto, aby vyhrál server. Každá úroveň Divu světa stojí stovky tisíc (i miliony) surovin.');
define("ww1", 'Pro změnu názvu Divu světa musí mít Div světa alespoň úroveň 1.');
define("ww2", 'Název Divu světa:');
define("ww3", 'Po úrovni 10 už název Divu světa nelze změnit.');
define("ww4", 'Název změněn.');
define("ww5", 'Je potřeba stavební plán Divu světa.');
define("ww6", 'Je potřeba další stavební plán Divu světa.');
define("ww7", 'Pro stavbu na úroveň ');
define("ww8", 'Pro stavbu Divu světa nad úroveň 50 potřebujete 2 stavební plány (jeden musíš mít ty a druhý jiný člen tvé aliance!)');
define("ww9", 'Pro stavbu Divu světa musíš mít na účtu první stavební plán a/nebo (od úrovně 50) musí druhý plán vlastnit člen tvé aliance!');

//kuznicaupgrade
define("kuzupg0", 'Plně rozvinuto ');
define("kuzupg1", 'Vylepšit kovárnu');
define("kuzupg2", 'Probíhá výzkum');
define("destroyvil", 'Vesnice už je zničena.');

//sitter
//заместители
define("accsit0", 'Poslat loupež');
define("accsit1", 'Posílat posily jiným hráčům');
define("accsit2", 'Posílat suroviny jiným hráčům');
define("accsit3", 'Utrácení zlata');
define("accsit4", 'Čtení a psaní zpráv');
define("accsit5", 'Mazání zpráv a hlášení');


//онлайн in альянсе(online in alliance)
define("oweronline0", 'Právě online');
define("oweronline1", 'Offline');
define("oweronline2", 'Poslední 3 dny');
define("oweronline3", 'Posledních 7 dní');

//doperevod
define("heroh0", 'spotřeba');
define("heroh1", 'Opravdu si chceš nasadit tento předmět?');
define("heroh2", 'Aktuální počet kulturních bodů');
define("heroh3", 'Přidat kulturní body:');
define("heroh4", 'Po použití bude: ');
define("sendmsg", 'Poslat zprávu');

//EVERYDAY QUEST
define("questday0", 'Úkol je pro dnešek splněn');
define("questday1", 'Úkol je stále otevřený');
define("questday2", 'Denní úkoly');
define("questday3", 'Body');
define("questday4", 'Za 25 nasbíraných bodů získáš jednu z těchto odměn:');
define("questday5", '5 dobrodružství');
define("questday6", '+5000 kulturních bodů');
if (!defined("HOWRES")) {
    define("HOWRES", 100000);
} //делаем опасный трюк.
define("questday7", HOWRES . ' surovin jednoho náhodného druhu');
define("questday8", 'Za 50 nasbíraných bodů získáš jednu z těchto odměn:');
define("questday9", '+1 den účtu PLUS');
define("questday10", '+1 den +25 % produkce dřeva');
define("questday11", '+1 den +25 % produkce hlíny');
define("questday12", '+1 den +25 % produkce železa');
define("questday13", '+1 den +25 % produkce obilí');
define("questday14", 'Za 75 nasbíraných bodů získáš jednu z těchto odměn:');
define("questday15", '+20 dobrodružství');
define("questday16", '+2 vědra');
define("questday17", '+1000 stříbra');
define("questday18", 'Za 100 nasbíraných bodů získáš jednu z těchto odměn:');
define("questday19", '+50 zlata');
define("questday20", '+4000 stříbra');
define("questday21", '+50 dobrodružství');
define("questday22", 'Získávej tyto odměny zdarma každý den!');
define("questday23", '(Další reset v 18:00. Vyzvedni si odměnu předtím!)');
define("questday24", 'Dokonči dobrodružství');
define("questday25", 'Získej medaili');
define("questday26", 'Pozvi hráče');
define("questday27", 'Postav nebo dobuď novou vesnici');
define("questday28", 'Získej nebo utrať zlato');
define("questday29", 'Obsaď 1 oázu');
define("questday30", 'Navštiv naši skupinu na Facebooku (klikni zde)');
define("questday31", 'Vylepši libovolnou jednotku v kovárně na maximální úroveň');
define("questday32", 'Gratulujeme! Nasbíral jsi potřebný počet bodů pro odměnu!');
define("questday33", 'nasbíráno');
define("questday34", 'bod');
define("questday35", ' Odměnu si můžeš vyzvednout za');
define("questday36", 'bodů dnes.');
define("questday37", 'Odměna se náhodně vybere z tohoto seznamu:');
define("questday38", 'Dnes jsi nasbíral');
define("questday39", 'bodů a za to dostáváš tuto odměnu:');
define("questday40", 'Tvá dnešní odměna:');

define("REP_С1", ' Propuštěny <b>všechny</b> jednotky.');
define("REP_С2", 'Rezidence/palác');
define("REP_С3", ' Kapacita úkrytu');
define("REP_С4", 'Hradba');
define("REP_С5", 'Tvůj hrdina získal 0 zkušeností');
define("REP_С6", 'Tvůj hrdina získal');
define("REP_С7", 'Tvůj hrdina snížil loajalitu z ');
define("REP_С8", 'do');
define("REP_С9", 'a získal');
define("REP_С10", 'Tvůj hrdina obsadil oázu a získal');
define("REP_С11", "");
define("REP_С12", 'Vesnici nelze obsadit');
define("REP_С13", 'Nedostatek kulturních bodů');
define("REP_С14", 'Obyvatelé vesnice');
define("REP_С15", 'se rozhodli připojit k tvé říši');
define("REP_С16", "");
define("REP_С17", 'Rezidence nebo palác nebyly zničeny');
define("REP_С18", 'Vesnice už je zničena');
define("REP_С19", 'Zničeno z úrovně');
define("REP_С20", "");
define("REP_С21", ' úroveň');
define("REP_С22", "");
define("REP_С23", 'Hradba nebyla poškozena');
define("REP_С24", 'Hradba zničena');
define("REP_С25", 'Žádná hradba');
define("REP_С26", 'Podpora napadena v');
define("REP_С27", 'útoky');
define("REP_С28", 'Obdržena podpora od');
define("REP_С29", 'Podpora doručena do');
define("REP_С30", 'Obsazená oáza');
define("REP_С31", 'Nic cenného nebylo nalezeno');
define("REP_С32", "");
define("REP_С33", 'Tvůj hrdina získal artefakt a získal');
define("REP_С34", 'Tvůj hrdina získal NEAKTIVOVANÝ artefakt a získal');
define("REP_С35", 'Tvůj hrdina artefakt nezískal a získal');
define("REP_С36", 'Máš maximální počet artefaktů. Hrdina získal');
define("REP_С37", 'Špehové');
define("REP_С38", 'Obdrženy suroviny od');
define("REP_С39", 'Suroviny doručeny do');
define("REP_С40", 'zničeno');
define("REP_С41", 'nepoškozeno');
define("REP_С42", 'Loajalita byla snížena z');
define("REP_С43", 'Chycená zvířata');
define("REP_С44", "");
define("REP_С45", 'Oáza');
define("REP_С46", 'tvůj hrdina dobrodružství nepřežil');
define("REP_С47", "");
define("REP_С48", 'zkoumá');
define("savebankgold", 'Zůstatek zlata z předchozího kola můžeš převést přes');
define("allgold", 'Všechno zlato');
define("adminhelp", ' S dotazy se obracej na');
define("endround", 'Zlato je na účet připsáno ihned po zaplacení');
define("endround1", 'Na konci kola / při smazání účtu se zlato připíše do banky podle vzorce:');

//КinЕСТЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫЫ
define("quest1", 'Další dobrodružství');
define("quest2", 'Během výuky už jsi z dobrodružství získal nějaké zkušenosti. Až se hrdina vrátí do vesnice, vyraz na další dobrodružství. Kořist a zkušenosti ti pomohou růst rychleji.');
define("quest3", 'Vyraz na druhé dobrodružství');
define("quest4", '30 zkušeností hrdiny');
define("quest5", 'Výborně, tvůj hrdina už je na cestě. Tip: Čím větší sílu hrdina má, tím menší zranění na dobrodružstvích utrží.');
define("quest6", 'Postav úkryt');
define("quest7", 'Mnoho hráčů žije z loupení surovin jiných hráčů. Na začátku hry máš ochranu nováčka a jsi v bezpečí. Postav úkryt, abys ochránil před vyloupením alespoň část surovin.');
define("quest8", 'Postav ve vesnici úkryt');
define("quest9", 'Skvělé, lupiči to teď u tebe nebudou mít tak snadné. V infoboxu uvidíš, kolik ti zbývá ochrany nováčka.');
define("quest10", 'Postav kasárna');
define("quest11", 'Kasárny jsou první budova, ve které můžeš cvičit jednotky. I jako mírumilovný hráč budeš jednotky potřebovat k ochraně sebe a svých spojenců před nepřáteli.');
define("quest12", 'Postav kasárna');
define("quest13", 'Kasárny jsou postavené! Dobrý krok k ovládnutí světa!');
define("quest14", 'Úroveň hrdiny');
define("quest15", 'Tvůj hrdina s každou novou úrovní sílí. Otevři vlastnosti hrdiny a rozděl dostupné body.');
define("quest16", 'Po dosažení nové úrovně hrdiny rozděl dostupné body.');
define("quest17", 'Rozdělení bodů můžeš kdykoli změnit. Potřebuješ k tomu jen knihu moudrosti, kterou lze najít na dobrodružstvích.');
define("quest18", 'Cvičit jednotky');
define("quest19", 'Teď je čas vycvičit první jednotky. V kasárnách už můžeš cvičit jeden typ pěchoty.');
define("quest20", 'Cvičit ' . round(250 * xQUEST) . ' jednotek v kasárnách');
define("quest21", 'Základní kámen slavné armády je položen! Pamatuj, že na tebe mohou zaútočit, i když nejsi online.');
define("quest22", 'Městská zeď');
define("quest23", 'Teď bys měl postavit i obranu. Opevnění zvýší základní obranu vesnice a také sílu bránících jednotek.');
define("quest24", 'Postav kolem vesnice opevnění.');
define("quest25", 'Výborně, obránci tvé vesnice jsou teď lépe chráněni.');
define("quest26", 'Zaútoč na oázu');
define("quest27", 'Najdi na mapě volnou oázu v okolí a vyloupi ji. Pokud ji brání zvířata, pošli hrdinu vybaveného klecemi, aby je pochytal.');
define("quest28", 'Otevři na mapě volnou oázu a zaútoč na ni.');
define("quest29", '2 základní jednotky');
define("quest30", 'Gratuluji, tvůj první útok je na cestě! Ještě chvíli ho můžeš na shromaždišti zrušit.');
define("quest31", '10 dobrodružství');
define("quest32", 'Posílej hrdinu dál na dobrodružství. Po dokončení 10 dobrodružství se můžeš účastnit aukcí a obchodovat s předměty s ostatními hráči.');
define("quest33", 'Dokonči 10 dobrodružství');
define("quest34", '500 stříbra');
define("quest35", 'Gratuluji, teď můžeš používat aukční dům. Vezmi si toto stříbro, ať máš hned na obchodování.');
define("quest36", 'Aukce');
define("quest37", 'Jdi do aukčního domu a podívej se, jaké předměty jsou právě v nabídce. Možná chceš část kořisti z dobrodružství proměnit ve stříbro?');
define("quest38", 'Vytvoř aukci nebo přihoď v aukci.');
define("quest39", 'Skvělé, teď víš, jak obchodovat s výbavou a spotřebními předměty s ostatními hráči.');
define("quest40", 'Rozšiř kasárna');
define("quest41", 'Rozšiř teď kasárny. Splníš tím předpoklady pro odemčení dalších budov.');
define("quest42", 'Rozšiř kasárny na úroveň 3.');
define("quest43", 'Výborně. Tvé jednotky se teď cvičí rychleji a můžeš postavit akademii.');
define("quest44", 'Postav akademii');
define("quest45", 'V akademii lze vyzkoumat nové a silnější jednotky pro tvou vesnici. Některé jednotky jsou velmi drahé a mají před vyzkoumáním vysoké předpoklady.');
define("quest46", 'Postav teď akademii.');
define("quest47", 'Dobrá práce. Brzy se dozvíš více o vojácích svého národa.');
define("quest48", 'Vyzkoumej jednotku');
define("quest49", 'Podívej se na možnosti výzkumu. Jsou tu pěší a jízdní jednotky i obléhací stroje. Jednotky se specializují hlavně na útok, nebo na obranu.');
define("quest50", 'Vyzkoumej další typ jednotky.');
define("quest51", 'Samotný výzkum samozřejmě nestačí, jednotky je potřeba také vycvičit.');
define("quest52", 'Postav kovárnu');
define("quest53", 'V kovárně můžeš lépe vyzbrojit a vybavit své vojáky. Kovárna je navíc potřeba ke stavbě dalších vojenských budov.');
define("quest54", 'Postav kovárnu.');
define("quest55", 'Výborně. Teď můžeš své vojáky lépe vybavit.');
define("quest56", 'Vylepši jednotky');
define("quest57", 'Vylepšení výbavy vojáků není levné. Čím víc vojáků máš, tím víc se vylepšení vyplatí. Tentokrát získáš víc, než kolik stálo.');
define("quest58", 'Vyzkoumej v kovárně vylepšení jednotky.');
define("quest59", 'Výborně, schopnost tvých jednotek útočit a bránit se se zlepšila.');
define("quest60", 'Železný důl');
define("quest61", 'Zadej stavbu železného dolu! Tvým hlavním cílem je stále vysoká produkce surovin, abys mohl rychle růst.');
define("quest62", 'Zahaj stavbu železného dolu');
define("quest63", 'Jeden den bonusu +25 % k produkci všech surovin');
define("quest64", 'Vyšší produkce železa ve tvé vesnici. Bonus produkce ti pomůže produkci určité suroviny ještě zvýšit.');
define("quest65", 'Více surovin');
define("quest66", 'Rozšiř po jednom poli dřeva, hlíny, železa a obilí na úroveň 1. Ke splnění úkolu musíš mít alespoň 2 pole od každé suroviny nad úrovní 0. Dokud máš aktivní Travian PLUS, můžeš vždy zadat ještě jednu stavbu najednou.');
define("quest67", 'Rozšiř ještě jedno pole od každé suroviny na úroveň 1.');
define("quest68", 'Gratuluji! Tvá vesnice roste a vzkvétá...');
define("quest69", 'Sýpka');
define("quest70", 'Pro uskladnění více obilí potřebuješ sýpku. Svůj současný limit skladování najdeš na liště surovin.');
define("quest71", 'Postav sýpku');
define("quest72", 'Pěkně! Sýpka ti teď umožní uskladnit více obilí.');
define("quest73", 'Vše na jedničku');
define("quest74", 'Na začátku je nejlepší soustředit se na suroviny. Rozšiř prosím všechna surovinová pole na úroveň 1.');
define("quest75", 'Rozšiř všechna surovinová pole na úroveň 1 ');
define("quest76", 'Produkce surovin se pěkně rozvíjí. Brzy můžeme začít stavět další budovy ve vesnici.');
define("quest77", 'Na dvojku!');
define("quest78", 'Dál zvyšuj produkci. Rozšiř po jednom dřevorubci, hliněném dolu, železném dolu a obilném poli na úroveň 2!');
define("quest79", 'Rozšiř po jednom surovinovém poli od každého druhu na úroveň 2');
define("quest80", 'Dobrá práce! Pokud potřebuješ více informací o produkci, klikni na své zásoby.');
define("quest81", 'Tržiště');
define("quest82", 'Když ti nějaká surovina chybí, můžeš ji na tržišti vyměnit s ostatními hráči za jiné suroviny. Ke stavbě malého tržiště potřebuješ větší hlavní budovu.');
define("quest83", 'Postav tržiště');
define("quest84", 'Tržiště je hotové a teď můžeš obchodovat s ostatními hráči. Nenech se ale nachytat na opravdu špatné nabídky!');
define("quest85", 'Obchod');
define("quest86", 'Existující nabídky na tržišti uvidíš po kliknutí na „koupit“. Zkontroluj směnný kurz a vzdálenost. Pokud nenajdeš vhodnou nabídku, klikni na „nabídnout“ a vytvoř si vlastní.');
define("quest87", 'Vytvoř nabídku na tržišti nebo nějakou přijmi');
define("quest88", 'Skvělé, zahájil jsi svůj první obchod.');
define("quest89", 'Vše na 2');
define("quest90", 'Než začneš stavět dražší budovy, měli bychom dál zvýšit produkci surovin. Rozšiř všechna surovinová pole na úroveň 2.');
define("quest91", 'Rozšiř všechna surovinová pole na úroveň 2');
define("quest92", 'Gratuluji! Produkce surovin se pěkně rozvíjí.');
define("quest93", 'Sklad surovin úrovně 3');
define("quest94", 'Je čas přizpůsobit sklad surovin zvýšené produkci. Nečekaná kořist hrdiny může sklad také přeplnit.');
define("quest95", 'Rozšiř sklad surovin na úroveň 3');
define("quest96", 'Moc dobře, teď už žádné cenné suroviny nepřijdou nazmar.');
define("quest97", 'Sýpka úrovně 3');
define("quest98", 'Čím vyšší produkce, tím rychleji se sklady plní. Měl bys rozšířit i sýpku.');
define("quest99", 'Rozšiř sýpku na úroveň 3');
define("quest100", 'V sýpce je teď zase místo, takže produkce může pokračovat i v tvé nepřítomnosti.');
define("quest101", 'Mlýn');
define("quest102", 'Mlýn zvyšuje produkci všech obilných polí. Aby se jeho cena vyplatila, potřebuješ dostatečně vysokou základní produkci.');
define("quest103", 'Postav mlýn úrovně 1');
define("quest104", 'Mlýn úrovně 2');
define("quest105", 'Teď máš pro další stavby k dispozici hodně volného obilí. Existují i budovy, které zvyšují produkci ostatních surovin.');
define("quest106", 'Vše na 5');
define("quest107", 'Potřebuješ mnohem vyšší produkci, abys nemusel dlouho čekat, než si budeš moci dovolit budovy a osadníky potřebné pro druhou vesnici. Rozšiř všechna surovinová pole na úroveň 5.');
define("quest108", 'Rozšiř všechna surovinová pole na úroveň 5');
define("quest109", 'Jeden den bonus +25 % k produkci všech surovin.');
define("quest110", 'Výborně, teď máš slušnou produkci.');
define("quest111", 'Zobrazit statistiky');
define("quest112", 'Ve světě Travianu soupeříš s tisíci dalšími hráči. Ve statistikách zjistíš více o své pozici ve hře.');
define("quest113", 'Otevři statistiky a porovnej se s ostatními hráči.');
define("quest114", 'Kromě pořadí tam najdeš i další užitečné informace. Záložka Top 10 ukazuje nejsilnější útočníky a nejúspěšnější lupiče.');
define("quest115", 'Změnit jméno vesnice');
define("quest116", 'Jméno vesnice, které si sám zvolíš, ukazuje ostatním hráčům, že tvou říši někdo aktivně vede.');
define("quest117", 'Změň jméno vesnice na ceduli vesnice.');
define("quest118", '33 kulturních bodů');
define("quest119", 'Pěkně, teď jsi udělal první krok k tomu, abys ve světě Travianu zanechal stopu.');
define("quest120", 'Hlavní budova úrovně 3');
define("quest121", 'Větší hlavní budova odemyká nové budovy a zrychluje práci stavitelů. Rychlejší stavění se ale vyplatí jen tehdy, když produkuješ dost surovin.');
define("quest122", 'Rozšiř hlavní budovu na úroveň 3.');
define("quest123", 'Skvělé, větší hlavní budova ti teď umožní stavět další budovy, které jsi právě odemkl.');
define("quest124", 'Postav ambasádu');
define("quest125", 'Svět Travianu je nebezpečné místo a musíš se umět bránit. Nejlepší další obranu nabízejí silní spojenci. Postav ambasádu, abys mohl vstoupit do aliance.');
define("quest126", 'Postav ambasádu.');
define("quest127", 'Výborně, teď můžeš přijímat pozvánky do aliance. Pozvánky najdeš v ambasádě.');
define("quest128", 'Otevři mapu');
define("quest129", 'Mapa ti ukazuje svět Travianu. Prohlédni si sousedy, najdi spojence a odhal hrozby');
define("quest130", 'Otevři mapu v menu.');
define("quest131", 'Jsou poblíž silní hráči nebo aliance? Mapa ti také pomůže najít oázy a místa, kde můžeš založit nové vesnice.');
define("quest132", 'Přečti zprávu');
define("quest133", 'Právě jsi dostal zprávu s užitečnými tipy. Nepřečtené zprávy poznáš podle čísla nad tlačítkem. Podívej se na ni.');
define("quest134", 'Otevři přehled zpráv a přečti si zprávu od zadavatele úkolů!');
define("quest135", 'Zprávami komunikuješ s ostatními hráči. Vždy se vyplatí být klidný a zdvořilý, i když spolu bojujete.');
define("quest136", 'Zlato navíc');
define("quest137", 'Během výuky jsi už zlatem urychlil stavby. V obchodě se zlatem zjistíš, k čemu dalšímu můžeš zlato použít.');
define("quest138", 'Podívej se na výhody, které si můžeš koupit za zlato.');
define("quest139", 'Tady máš zase trochu zlata zdarma, abys mohl využít některé zlaté výhody.');
define("quest140", 'Aliance');
define("quest141", 'Hledej spojence a vstup do aliance. Pokud zatím nemáš žádné kontakty, podívej se na aliance hráčů ve svém okolí nebo hledej alianci na fóru.');
define("quest142", 'Vstup do aliance.');
define("quest143", 'Máme skvělý začátek. Čím silnější a aktivnější je každý hráč, tím silnější budete jako tým. Už víte, jak si navzájem hlásit útoky a žádat o pomoc?');
define("quest144", 'Hlavní budova úrovně 5');
define("quest145", 'Je čas rozšířit hlavní budovu, abys mohl stavět nové budovy. Nezapomeň zároveň pečovat o produkci surovin.');
define("quest146", 'Rozšiř hlavní budovu na úroveň 5.');
define("quest147", 'Skvělé, teď můžeš postavit rezidenci. Tví stavitelé jsou také rychlejší.');
define("quest148", 'Sídlo vlády');
define("quest149", 'Postav teď sídlo vlády, abys mohl brzy založit novou vesnici. Pokud si nejsi jistý, jestli má tato vesnice zůstat tvým hlavním městem, vyber rezidenci.');
define("quest150", 'Postav rezidenci nebo palác.');
define("quest151", 'Tato budova je potřeba k založení nebo dobytí nové vesnice. Její úroveň omezuje počet možných rozšíření.');
define("quest152", 'Kulturní body');
define("quest153", 'Abys mohl ve své říši vládnout více vesnicím, potřebuješ kulturní body. Přehled v rezidenci nebo paláci ti ukáže, kolik ti chybí a jak dlouho to bude trvat.');
define("quest154", 'Otevři v rezidenci nebo paláci záložku kulturních bodů.');
define("quest155", 'V seznamu vesnic vidíš i aktuální stav možných nových vesnic a počet chybějících kulturních bodů. V „Answers“ zjistíš, jak kulturní body rychle zvýšit.');
define("quest156", 'Sklad surovin úrovně 7');
define("quest157", 'Rozšiř sklad surovin a připrav se na založení nové vesnice. Současná kapacita brzy nebude stačit na potřebné budovy a osadníky.');
define("quest158", 'Rozšiř sklad surovin na úroveň 7.');
define("quest159", 'Skvělé, kapacita skladů by teď nějakou dobu měla stačit. Nezapomeň své cenné suroviny bránit nebo schovávat.');
define("quest160", 'Rezidence nebo palác úrovně 10');
define("quest161", 'Osadníky lze cvičit v paláci nebo rezidenci. Na záložce "Cvičit" uvidíš potřebnou úroveň budovy.');
define("quest162", 'Rozšiř rezidenci nebo palác na úroveň 10.');
define("quest163", '167 kulturních bodů');
define("quest164", 'Z každé vesnice můžeš ovládnout jen 2 až 3 nové vesnice. Pro novou vesnici ti teď chybí už jen 3 osadníci a spousta kulturních bodů.');
define("quest165", 'Vycvič tři osadníky');
define("quest166", 'Osadníci při zakládání nové vesnice cestují vždy v malé skupině. Dobře je chraň před útoky, dokud nejsou připraveni vyrazit.');
define("quest167", 'Vycvič tři osadníky.');
define("quest168", 'Pěkné. Osadníci si vždy vezmou do nové vesnice nějaké suroviny, aby ji mohli hned začít budovat.');
define("quest169", 'Založit novou vesnici');
define("quest170", 'Najdi na mapě dobré místo k osídlení. Chceš ho mít blízko své vesnice, s vyšší produkcí jedné suroviny nebo poblíž mnoha oáz?');
define("quest171", 'Založ pomocí osadníků druhou vesnici.');
define("quest172", 'Výborně. Teď ti dám další 2 dny Travian Plus – to se ti bude hodit.');
define("quest173", 'Denní úkoly');
define("quest174", 'Denní ceny');
define("quest175", 'Klikni pro podrobnosti');


//register mail
define("register1", 'Vítej v');
define("register2", 'Ahoj');
define("register3", 'Děkujeme za registraci.');
define("register4", 'Přihlásit');
define("register5", 'Heslo');
define("register6", 'Aktivační kód');
define("register7", 'Klikni na odkaz níže pro aktivaci účtu:');
define("register8", 'S pozdravem,');
define("register9", 'Tým TravianT4');
define("register10", 'Nové heslo');
define("register11", 'Požádal(a) jsi o nové heslo do Travianu.');
define("register12", 'Klikni na odkaz níže pro aktivaci nového hesla. Tvé dosavadní heslo přestane platit:');
define("register13", 'Heslo si můžeš kdykoli změnit ve svém profilu ve hře.');
define("register14", 'Pokud jsi o nové heslo nežádal(a), tento e-mail ignoruj.');

define("new_village", 'Nová vesnice');
define("new_village2", ' – vesnice');


$lang_winner['1'] = 'Milý/á ' . SERVER_NAME . ' hráči';
$lang_winner['2'] = 'Všechno dobré jednou končí, a tak končí i tento věk. Kdysi dostal Šalamoun prsten s nápisem, který dokázal zahnat všechny radosti i strasti světa; volně přeloženo zněl: „I toto pomine.“ S radostí i smutkem oznamujeme všem ' . SERVER_NAME . ' hráčům, že i toto nyní pominulo! Doufáme, že jste si čas s námi užili stejně jako my a děkujeme, že jste vydrželi až do samého konce!';
$lang_winner['3'] = 'Výsledky: Den se už dávno změnil v noc, a přesto dělníci vesnice';
$lang_winner['4'] = 'pracovali celý zimní večer, ve střehu před nesčetnými armádami, které táhly zničit jejich dílo, vědomi si toho, že závodí s časem a s největší hrozbou, jaké kdy svobodné národy čelily. Jejich neúnavné úsilí bylo odměněno, když bezejmenný dělník položil poslední kámen do stavby, která navždy zůstane největším a nejvelkolepějším dílem celé historie od pádu Natarů.';
$lang_winner['5'] = 'Spolu s aliancí';
$lang_winner['6'] = 'jako první dokončil Div světa, spotřeboval miliony surovin a chránil ho statisíci statečných obránců. Proto je to';
$lang_winner['7'] = 'kdo získává titul „vítěz tohoto věku“!';
$lang_winner['8'] = 'vládl největší osobní říši, těsně následován hráčem';
$lang_winner['9'] = 'a';
$lang_winner['10'] = 'pobil víc nepřátel než kdokoli jiný a byl nejmocnějším a nejobávanějším velitelem.';
$lang_winner['11'] = 'byl nejslavnějším obráncem, pobíjel nepřátele u bran vesnic a jejich krví zbarvil okolní krajinu.';
$lang_winner['12'] = 'S pozdravem';
$lang_winner['13'] = 'Tvůj ' . SERVER_NAME . ' tým';
$lang_winner['14'] = 'pokračovat';
$lang_winner['desc1'] = 'Vesnic celkem';
$lang_winner['desc2'] = 'Populace celkem';
$lang_winner['desc3'] = 'Útočné body';
$lang_winner['desc4'] = 'Obranné body';
define("PL_01", 'Zlatý klub');
define("PL_02", 'Skvělé funkce pro každý herní styl!');
define("PL_03", 'Nech obchodníky automaticky přepravovat víc surovin najednou, hledej obilí na mapě, ukládej si zprávy a rozhoduj, do kterých bitev se pustit. Pomocí seznamu farem plánuj útoky... Chraň svou armádu únikem z hlavního města, když na tebe někdo útočí. Zobraz útok protivníka!');
define("PL_04", 'Travian Plus');
define("PL_05", 'přehledy a lepší funkce!');
define("PL_06", 'Nech obchodníky automaticky přepravovat víc surovin najednou, hledej obilí na mapě, ukládej si zprávy a rozhoduj, do kterých bitev se pustit. Pomocí seznamu farem plánuj útoky... Chraň svou armádu únikem z hlavního města, když na tebe někdo útočí. Zobraz útok protivníka!');
define("PL_07", '+25 % dřeva');
define("PL_08", '+25 % hlíny');
define("PL_09", '+25 % železa');
define("PL_10", '+25 % obilí');
define("PL_11", 'Zvýšení produkce o 25 %');
define("PL_12", '25 % celkové produkce');

define("PL_13", 'Vyber funkci, kterou chceš použít:');
define("PL_14", 'probíhá');
define("PL_15", "");
define("PL_16", 'Celá prohlídka');
define("PL_17", 'A M.');
define("PL_18", 'Udělej to.');
define("PL_19", 'Další');

// productionBoostPopup
define("BD_LEVEL", 'úroveň');
define("MAXLEVEL", 'Budova dosáhla maximální úrovně');
define("TOP10", 'top 10');


$lang['buildings']['texts'] = array(
    'TRA0' => 'Seznam výcviku',
    'TRA1' => 'Jednotky',
    'TRA2' => 'Trvání',
    'TRA3' => 'Skončeno',
    'AKA1' => 'Probíhá výzkum',
    'AKA2' => 'Výzkum lze zahájit po dokončení stavby budovy.',

);

$lang['profile'] = array(
    '1' => 'Profil',
    '2' => 'Pořadí podle populace',
    '3' => 'Obyvatelé',
    '4' => 'Pořadí útočníků',
    '5' => 'Body',
    '6' => 'Pořadí obránců',
    '7' => 'Úroveň hrdiny',
    '8' => 'ZK',

    // Medals
    '9' => 'Kategorie',
    '10' => 'Týden',
    '11' => 'Pořadí',
    '12' => 'Body'
);


$lang['quests'] = array(
    'Next' => 'pokračovat',
    'getRewards' => 'Vyzvednout odměny',
    'ActivateTips' => 'Zobrazit tipy',
    'DeactivateTips' => 'Skrýt tipy',
    'TipsToggleDescription' => 'Tipy zobrazit / skrýt',
    'GetRewards' => 'Vyzvednout odměnu',
    'Overview' => 'Přehled',
    'Battle' => 'Boj',
    'Economy' => 'Ekonomika',
    'World' => 'Svět',

    '1' => array(
        'Title' => 'Vítej',
        'Description' => 'Ahoj ' . $session->username . ', vítej v Travianu!<br>Dokud si budeš budovat novou vesnici, budu ti pomáhat. V tomto návodu si vybuduješ vlastní vesnici a poznáš smysl hry a její funkce',
        'toDo' => array('Výuka vysvětluje hlavní funkce hry a zabere jen pár minut. Začni hned!')
    ),
    '2' => array(
        'Title' => 'Úkoly a nápověda',
        'Description' => 'Stránku s úkoly můžeš přesunout nebo zavřít. Znovu ji otevřeš kliknutím na můj obrázek v levém horním rohu. Tipy a úkoly ti pomohou ve hře.',
        'toDo' => array('Zavřít úkoly', 'Klikni na rádce a otevři stránku s tipy', 'Vypnout tipy k funkcím'),
        'reward' => 'Čeká na tebe hliněný důl úrovně 1!',
        'completed' => 'Teď můžeš kdykoli zjistit informace o aktuálním úkolu. Další úkol můžeš začít, jakmile získáš odměnu za úkol. Postav svůj hliněný důl.'
    ),

    '3' => array(
        'Title' => 'Postav dřevorubce',
        'Description' => 'K rozvoji vesnice, stavbě budov, výcviku jednotek i růstu říše potřebuješ hodně surovin! Nejdřív zvyš produkci surovin – postav dřevorubce!',
        'toDo' => array('Otevři lesní pole kliknutím na něj', 'Zadej stavbu dřevorubce úrovně 1'),
        'reward' => 'dokončit dřevorubce úrovně 1',
        'completed' => 'Silná ekonomika je základ dobrého startu. Stavbu dřevorubce jsem ti právě dokončil, abys mohl pokračovat.'
    ),

    '4' => array(
        'Title' => 'Rozšiř dřevorubce',
        'Description' => 'Větší budova vyžaduje s každým rozšířením více surovin, ale zato také více vyrábí. Rozšiř teď dřevorubce z úrovně 1 na úroveň 2!',
        'toDo' => array('Otevři dřevorubce úrovně 1', 'Zadej stavbu dřevorubce úrovně 2'),
        'reward' => 'Okamžitě dokonči stavbu dřevorubce úrovně 2',
        'completed' => 'Přehled tvých skladů a zásob najdeš nad vesnicí. Náklady na stavbu se odečtou ze zásob. Stavbu ti zase hned dokončím.'
    ),

    '5' => array(
        'Title' => 'Postav obilné pole',
        'Description' => 'Když se podíváš na suroviny, všimni si spotřeby obilí v levém rohu. Obilí je potřeba pro jednotky i budovy. Postav prosím obilné pole.',
        'toDo' => array('Klikni na obilné pole', 'Postav obilné pole na úroveň 1'),
        'reward' => 'Dokonči obilné pole úrovně 1 a rozšiř ho na úroveň 2',
        'completed' => 'Tvá vesnice teď produkuje dost obilí na stavbu nových budov. Obilím se živí obyvatelé i jednotky umístěné ve vesnici.'
    ),

    '6' => array(
        'Title' => 'Produkce hrdiny',
        'Description' => 'Pokud tvůj hrdina žije, může vyrábět suroviny pro svou vesnici. Na naše stavby máme jen málo hlíny. Změň produkci hrdiny na hlínu.',
        'toDo' => array('Klikni na obrázek hrdiny a otevři přehled', 'Změň produkci hrdiny na hlínu a ulož'),
        'reward' => '' . number_format(200 * SPEED) . ' <i class="r2"></i>',
        'completed' => 'Skvělé! Teď ti hrdina pomůže vyrábět více surovin. Všechny vyrobené suroviny se připíší do hlavní vesnice. Trochu ti suroviny navýším.'
    ),

    '7' => array(
        'Title' => 'Vstup do centra vesnice',
        'Description' => 'Dále zvýšíme kapacitu skladů, kterou vidíš nahoře v menu hry. K tomu potřebujeme budovy uvnitř vesnice. Vstup do centra vesnice.',
        'toDo' => array('Vstup do centra vesnice')
    ),

    '8' => array(
        'Title' => 'Postav sklad surovin',
        'Description' => 'Bez skladu surovin můžeš ve vesnici uskladnit jen málo surovin. Klikni na stavební místo, v nabídce staveb najdi sklad surovin a postav ho!.',
        'toDo' => array('otevři stavební místo a v nabídce klikni na infrastrukturu', 'Zadej stavbu skladu surovin úrovně 1'),
        'reward' => 'Jeden den Travian Plus',
        'completed' => 'Stavba začala a brzy budeš moci ukládat víc surovin i loupit. Dám ti 1,8 hodiny Travian Plus, se kterým můžeš zadávat stavby do fronty, i když první stavba ještě není dokončená.'
    ),

    '9' => array(
        'Title' => 'Shromaždiště',
        'Description' => 'Abys mohl posílat hrdinu na dobrodružství, potřebuješ shromaždiště – najdeš ho v centru vesnice! Postav ho na úroveň 1.',
        'toDo' => array('Klikni na stavební místo shromaždiště', 'Postav shromaždiště úrovně 1'),
        'reward' => '<img src="img/x.gif" alt="gold" title="gold" class="gold"> 2',
        'completed' => 'Vypadá to skvěle! Teď můžeš poslat hrdinu na dobrodružství. K tomu ti dám trochu zlata, které můžeš rozumně utratit.'
    ),

    '10' => array(
        'Title' => 'Dokončit okamžitě',
        'Description' => 'Pod vesnicí najdeš seznam všech aktuálních staveb. Tentokrát můžeš stavbu urychlit sám. Použij zlato z minulého úkolu a dokonči stavby kliknutím na "dokončit stavbu okamžitě".',
        'toDo' => array('Okamžitě dokonči stavby'),
        'reward' => '<img src="img/x.gif" alt="gold" title="gold" class="gold"> 10',
        'completed' => 'Teď můžeš poslat hrdinu na dobrodružství. Nejdřív zadej stavby, aby tvá vesnice stále rostla. Vezmi si toto zlato a použij ho moudře.'
    ),

    '11' => array(
        'Title' => 'Vyraz na dobrodružství',
        'Description' => 'Objevuj tajemná místa v okolí a sbírej zkušenosti a cennou kořist. Otevři seznam dobrodružství a pošli hrdinu na jeho první dobrodružství.',
        'toDo' => array('Pošli hrdinu na jeho první dobrodružství'),
        'reward' => 'Tvůj hrdina se z dobrodružství okamžitě vrátí',
        'completed' => 'SKVĚLÉ, tvůj hrdina vyrazil na dobrodružství – co asi najde? Pod obrázkem hrdiny vidíš, že je na cestě. Nechám ho rychle dorazit, ať vidíme, co se stane.'
    ),

    '12' => array(
        'Title' => 'Hlášení',
        'Description' => 'Tvůj hrdina se teď vrací ze svého prvního dobrodružství. V menu nahoře najdeš hlášení. Otevři seznam hlášení a přečti si hlášení o dobrodružství.',
        'toDo' => array('Otevři seznam hlášení', 'Zobraz nové hlášení o dobrodružství'),
        'reward' => '<img src="img/x.gif" class="questRewardTypeItem item106"> <span class="questRewardValue">10</span>',
        'completed' => 'Vidíš, jakou odměnu tvůj hrdina našel. Hrdina je také lehce zraněný – aby se to nestalo znovu, dám ti trochu masti.'
    ),

    '13' => array(
        'Title' => 'Vyléčit hrdinu',
        'Description' => 'Tvůj hrdina byl lehce zraněn. Kliknutím na jeho obrázek otevři přehled hrdiny. Pak v inventáři klikni na masti a použij je tlačítkem "ok". Spotřebuje se jen potřebné množství.',
        'toDo' => array('Klikni na obrázek hrdiny a otevři inventář', 'Klikni na masti v inventáři a použij je'),
        'reward' => 'Tvůj hrdina navíc získal ' . number_format(20 * SPEED) . ' zkušenostních bodů.',
        'completed' => 'Všechny nástroje a zbraně se používají stejně. Informace o předmětu zobrazíš podržením kurzoru nad ním.'
    ),

    '14' => array(
        'Title' => 'Nápověda k rozhraní',
        'Description' => 'Vedle mého obrázku najdeš další nápovědu ke hře. Jsou tam vysvětleny rozvržení a jednotlivé části uživatelského rozhraní. Zkus to!',
        'toDo' => array('Otevři nápovědu k uživatelskému rozhraní a rozhlédni se'),
        'reward' => '<i class="r1"></i> ' . number_format(270 * SPEED) . ' <i class="r2"></i> ' . number_format(300 * SPEED) . ' <i class="r3"></i> ' . number_format(270 * SPEED) . ' <i class="r4"></i> ' . number_format(220 * SPEED) . '',
        'completed' => 'Pokud máš konkrétní otázky, nejdřív je hledej v "Travian Odpovědích". Najdeš je pod "i" v záhlaví tohoto okna nebo v rohu obrazovky.'
    ),

    '15' => array(
        'Title' => 'Konec výuky',
        'completed' => 'Teď znáš základy hry. Důležité informace, například časově omezené údaje pro nováčky, se zobrazí v infoboxu vlevo. Užij si Travian!',
        'toDo' => array('Konec výuky'),
        'reward' => 'Dokončit výcvik'
    ),
    '15a' => array(
        'Title' => 'Přeskočit výuku',
        'Description' => 'Teď znáš základy hry. Důležité informace, například časově omezené údaje pro nováčky, se zobrazí v infoboxu vlevo. Užij si Travian!',
        'toDo' => array(),
        'reward' => 'Shromaždiště, hliněný důl, dřevorubec 2, obilné pole 2, 10 zlata, 1,8 hodiny PLUS'
    ),

);

$lang['quests']['battle'] = array(
    '1' => array(
        'Title' => 'Další dobrodružství',
        'Description' => 'Během výuky už jsi z dobrodružství získal nějaké zkušenosti. Až se hrdina vrátí do vesnice, vyraz na další dobrodružství. Kořist a zkušenosti ti pomohou růst rychleji.',
        'toDo' => array('Vyraz na druhé dobrodružství'),
        'reward' => '' . number_format(30 * SPEED) . ' zkušenosti hrdiny',

        'completed' => 'Výborně, tvůj hrdina už je na cestě. Tip: Čím větší sílu hrdina má, tím menší zranění na dobrodružstvích utrží.'
    ),
    '2' => array(
        'Title' => 'Postav úkryt',
        'Description' => 'Mnoho hráčů žije z loupení surovin jiných hráčů. Na začátku hry máš ochranu nováčka a jsi v bezpečí. Postav úkryt, abys ochránil před vyloupením alespoň část surovin.',
        'toDo' => array('Postav ve vesnici úkryt'),
        'reward' => '<i class="r1"></i> ' . number_format(130 * SPEED) . ' <i class="r2"></i> ' . number_format(150 * SPEED) . ' <i class="r3"></i> ' . number_format(120 * SPEED) . ' <i class="r4"></i> ' . number_format(100 * SPEED) . '',

        'completed' => 'Skvělé, lupiči to teď u tebe nebudou mít tak snadné. V infoboxu uvidíš, kolik ti zbývá ochrany nováčka.'
    ),
    '3' => array(
        'Title' => 'Postav kasárna',
        'Description' => 'Kasárny jsou první budova, ve které můžeš cvičit jednotky. I jako mírumilovný hráč budeš jednotky potřebovat k ochraně sebe a svých spojenců před nepřáteli.',
        'toDo' => array('Postav kasárna'),
        'reward' => '<i class="r1"></i> ' . number_format(110 * SPEED) . ' <i class="r2"></i> ' . number_format(140 * SPEED) . ' <i class="r3"></i> ' . number_format(160 * SPEED) . ' <i class="r4"></i> ' . number_format(30 * SPEED) . '',

        'completed' => 'Kasárny jsou postavené! Dobrý krok k ovládnutí světa!'
    ),
    '4' => array(
        'Title' => 'Dokonči 5 dobrodružství',
        'Description' => 'Víc dobrodružství, víc kořisti – posílej hrdinu na dobrodružství, kdykoli je nějaké k dispozici, a když má málo zdraví, nech ho odpočinout nebo mu dej léčivou mast.',
        'toDo' => array('Dokonči 5 dobrodružství'),
        'reward' => '<img src="img/x.gif" title="Ointments" class="questRewardTypeItem item106"> <span class="questRewardValue">150</span>',

        'completed' => 'Mastmi můžeš hrdinu léčit. Když masti nasadíš, použijí se, jakmile hrdina utrží zranění.'
    ),
    '5' => array(
        'Title' => 'Cvičit jednotky',
        'Description' => 'Teď je čas vycvičit první jednotky. V kasárnách už můžeš cvičit jeden typ pěchoty.',
        'toDo' => array('Vycvič v kasárnách dvě jednotky'),
        'reward' => '<img src="img/x.gif" title="Cage" class="questRewardTypeItem item114"> <span class="questRewardValue">1</span>',

        'completed' => 'Základní kámen slavné armády je položen! Pamatuj, že na tebe mohou zaútočit, i když nejsi online.'
    ),
    '6' => array(
        'Title' => 'Zemní val',
        'Description' => 'Teď bys měl postavit i obranu. Opevnění zvýší základní obranu vesnice a také sílu bránících jednotek.',
        'toDo' => array('Postav kolem vesnice opevnění'),
        'reward' => '<i class="r1"></i> ' . number_format(120 * SPEED) . ' <i class="r2"></i> ' . number_format(120 * SPEED) . ' <i class="r3"></i> ' . number_format(90 * SPEED) . ' <i class="r4"></i> ' . number_format(50 * SPEED) . '',

        'completed' => 'Výborně, obránci tvé vesnice jsou teď lépe chráněni.'
    ),
    '7' => array(
        'Title' => 'Zaútoč na oázu',
        'Description' => 'Najdi na mapě volnou oázu v okolí a vyloupi ji. Pokud ji brání zvířata, pošli hrdinu vybaveného klecemi, aby je pochytal.',
        'toDo' => array('Otevři na mapě volnou oázu a zaútoč na ni'),
        'reward' => '2 základní jednotky',

        'completed' => 'Gratuluji, tvůj první útok je na cestě! Ještě chvíli ho můžeš na shromaždišti zrušit.'
    ),
    '8' => array(
        'Title' => '10 dobrodružství',
        'Description' => 'Posílej hrdinu dál na dobrodružství. Po dokončení 10 dobrodružství se můžeš účastnit aukcí a obchodovat s předměty s ostatními hráči.',
        'toDo' => array('Dokonči 10 dobrodružství'),
        'reward' => '500 stříbra',

        'completed' => 'Gratuluji, teď můžeš používat aukční dům. Vezmi si toto stříbro, ať máš hned na obchodování.'
    ),
    '9' => array(
        'Title' => 'Aukce',
        'Description' => 'Jdi do aukčního domu a podívej se, jaké předměty jsou právě v nabídce. Možná chceš část kořisti z dobrodružství proměnit ve stříbro?',
        'toDo' => array('Vytvoř aukci nebo přihoď v aukci'),
        'reward' => '<i class="r1"></i> ' . number_format(280 * SPEED) . ' <i class="r2"></i> ' . number_format(120 * SPEED) . ' <i class="r3"></i> ' . number_format(220 * SPEED) . ' <i class="r4"></i> ' . number_format(110 * SPEED) . '',

        'completed' => 'Skvělé, teď víš, jak obchodovat s výbavou a spotřebními předměty s ostatními hráči.'
    ),
    '10' => array(
        'Title' => 'Rozšiř kasárna',
        'Description' => 'Rozšiř teď kasárny. Splníš tím předpoklady pro odemčení dalších budov.',
        'toDo' => array('Rozšiř kasárny na úroveň 3'),
        'reward' => '<i class="r1"></i> ' . number_format(240 * SPEED) . ' <i class="r2"></i> ' . number_format(290 * SPEED) . ' <i class="r3"></i> ' . number_format(430 * SPEED) . ' <i class="r4"></i> ' . number_format(240 * SPEED) . '',

        'completed' => 'Výborně. Tvé jednotky se teď cvičí rychleji a můžeš postavit akademii.'
    ),
    '11' => array(
        'Title' => 'Postav akademii',
        'Description' => 'Nové a silnější jednotky pro svou vesnici můžeš vyzkoumat v akademii. Některé jednotky jsou velmi drahé a mají vysoké požadavky, než je lze vyzkoumat!',
        'toDo' => array('Postav teď akademii'),
        'reward' => '<i class="r1"></i> ' . number_format(210 * SPEED) . ' <i class="r2"></i> ' . number_format(170 * SPEED) . ' <i class="r3"></i> ' . number_format(245 * SPEED) . ' <i class="r4"></i> ' . number_format(115 * SPEED) . '',

        'completed' => 'Dobrá práce. Brzy se dozvíš více o vojácích svého národa.'
    ),
    '12' => array(
        'Title' => 'Vyzkoumej jednotku',
        'Description' => 'Podívej se na možnosti výzkumu. Jsou tu pěší a jízdní jednotky i obléhací stroje. Jednotky se specializují hlavně na útok, nebo na obranu.',
        'toDo' => array('Vyzkoumej další typ jednotky'),
        'reward' => '<i class="r1"></i> ' . number_format(450 * SPEED) . ' <i class="r2"></i> ' . number_format(435 * SPEED) . ' <i class="r3"></i> ' . number_format(515 * SPEED) . ' <i class="r4"></i> ' . number_format(550 * SPEED) . '',

        'completed' => 'Samotný výzkum samozřejmě nestačí – jednotky je potřeba i vycvičit!'
    ),
    '13' => array(
        'Title' => 'Postav kovárnu',
        'Description' => 'V kovárně můžeš lépe vyzbrojit a vybavit své vojáky. Kovárna je navíc potřeba ke stavbě dalších vojenských budov.',
        'toDo' => array('Postav kovárnu'),
        'reward' => '<i class="r1"></i> ' . number_format(500 * SPEED) . ' <i class="r2"></i> ' . number_format(400 * SPEED) . ' <i class="r3"></i> ' . number_format(700 * SPEED) . ' <i class="r4"></i> ' . number_format(400 * SPEED) . '',

        'completed' => 'Výborně. Teď můžeš své vojáky lépe vybavit!'
    ),
    '14' => array(
        'Title' => 'Vylepši jednotky',
        'Description' => 'Vylepšení výzbroje vojáků není levné. Čím víc vojáků máš, tím víc se vylepšení vyplatí. Tentokrát ale dostaneš víc, než kolik tě to stálo!',
        'toDo' => array('Dokonči vylepšení typu jednotky'),
        'reward' => '<img src="img/x.gif" class="questRewardTypeItem item112"> <span class="questRewardValue">10</span>',

        'completed' => 'Vyzkoumej v kovárně vylepšení jednotky!'
    ),
);

$lang['quests']['economy'] = array(
    '1' => array(
        'Title' => 'Železný důl',
        'Description' => 'Vyšší produkce železa ve tvé vesnici. Bonus produkce ti pomůže produkci určité suroviny ještě zvýšit.',
        'toDo' => array('Rozšiř železný důl'),
        'reward' => '1 den bonus +25 % k produkci všech surovin',

        'completed' => 'Vyšší produkce železa ve tvé vesnici. Bonus produkce ti pomůže produkci určité suroviny ještě zvýšit.'
    ),
    '2' => array(
        'Title' => 'Více surovin',
        'Description' => 'Rozšiř po jednom poli dřeva, hlíny, železa a obilí na úroveň 1. Ke splnění úkolu musíš mít alespoň 2 pole od každé suroviny nad úrovní 0. Dokud máš aktivní Travian PLUS, můžeš vždy zadat ještě jednu stavbu najednou.',
        'toDo' => array('Rozšiř ještě jedno pole od každé suroviny na úroveň 1'),
        'reward' => '<i class="r1"></i> ' . number_format(160 * SPEED) . ' <i class="r2"></i> ' . number_format(190 * SPEED) . ' <i class="r3"></i> ' . number_format(150 * SPEED) . ' <i class="r4"></i> ' . number_format(70 * SPEED) . '',

        'completed' => 'Gratuluji! Tvá vesnice roste a vzkvétá...'
    ),
    '3' => array(
        'Title' => 'Sýpka',
        'Description' => 'Pro uskladnění více obilí potřebuješ sýpku. Svůj současný limit skladování najdeš na liště surovin.',
        'toDo' => array('Postav sýpku'),
        'reward' => '<i class="r1"></i> ' . number_format(250 * SPEED) . ' <i class="r2"></i> ' . number_format(290 * SPEED) . ' <i class="r3"></i> ' . number_format(100 * SPEED) . ' <i class="r4"></i> ' . number_format(130 * SPEED) . '',

        'completed' => 'Pěkně! Sýpka ti teď umožní uskladnit více obilí.'
    ),
    '4' => array(
        'Title' => 'Vše na jedničku',
        'Description' => 'Na začátku je nejlepší soustředit se na suroviny. Rozšiř prosím všechna surovinová pole na úroveň 1.',
        'toDo' => array('Rozšiř všechna surovinová pole na úroveň 1'),
        'reward' => '<i class="r1"></i> ' . number_format(400 * SPEED) . ' <i class="r2"></i> ' . number_format(460 * SPEED) . ' <i class="r3"></i> ' . number_format(330 * SPEED) . ' <i class="r4"></i> ' . number_format(270 * SPEED) . '',

        'completed' => 'Produkce surovin se pěkně rozvíjí. Brzy můžeme začít stavět další budovy ve vesnici.'
    ),
    '5' => array(
        'Title' => 'Na dvojku!',
        'Description' => 'Výborně! Další informace o produkci najdeš po kliknutí na své zásoby.',
        'toDo' => array('Rozšiř od každé suroviny 1 pole na úroveň 2'),
        'reward' => '<i class="r1"></i> ' . number_format(240 * SPEED) . ' <i class="r2"></i> ' . number_format(255 * SPEED) . ' <i class="r3"></i> ' . number_format(190 * SPEED) . ' <i class="r4"></i> ' . number_format(160 * SPEED) . '',

        'completed' => 'Dobrá práce! Pokud potřebuješ více informací o produkci, klikni na své zásoby.'
    ),
    '6' => array(
        'Title' => 'Tržiště',
        'Description' => 'Když ti nějaká surovina chybí, můžeš ji na tržišti vyměnit s ostatními hráči za jiné suroviny. Ke stavbě malého tržiště potřebuješ větší hlavní budovu.',
        'toDo' => array('Postav tržiště'),
        'reward' => '<i class="r1"></i> ' . number_format(600 * SPEED) . '',

        'completed' => 'Tržiště je hotové a teď můžeš obchodovat s ostatními hráči. Nenech se ale nachytat na opravdu špatné nabídky!'
    ),
    '7' => array(
        'Title' => 'Obchod',
        'Description' => 'Existující nabídky na tržišti uvidíš po kliknutí na koupit. Zkontroluj směnný kurz a vzdálenost. Pokud nenajdeš vhodnou nabídku, klikni na nabídnout a vytvoř si vlastní.',
        'toDo' => array('Vytvoř nabídku na tržišti nebo nějakou přijmi'),
        'reward' => '<i class="r1"></i> ' . number_format(100 * SPEED) . ' <i class="r2"></i> ' . number_format(99 * SPEED) . ' <i class="r3"></i> ' . number_format(99 * SPEED) . ' <i class="r4"></i> ' . number_format(99 * SPEED) . '',

        'completed' => 'Skvělé, zahájil jsi svůj první obchod.'
    ),
    '8' => array(
        'Title' => 'Vše na 2',
        'Description' => 'Než začneš stavět dražší budovy, měli bychom dál zvýšit produkci surovin. Rozšiř všechna surovinová pole na úroveň 2.',
        'toDo' => array('Rozšiř všechna surovinová pole na úroveň 2'),
        'reward' => '<i class="r1"></i> ' . number_format(400 * SPEED) . ' <i class="r2"></i> ' . number_format(400 * SPEED) . ' <i class="r3"></i> ' . number_format(400 * SPEED) . ' <i class="r4"></i> ' . number_format(200 * SPEED) . '',

        'completed' => 'Gratuluji! Produkce surovin se pěkně rozvíjí.'
    ),
    '9' => array(
        'Title' => 'Sklad surovin úrovně 3',
        'Description' => 'Je čas přizpůsobit sklad surovin zvýšené produkci. Nečekaná kořist hrdiny může sklad také přeplnit.',
        'toDo' => array('Rozšiř sklad surovin na úroveň 3'),
        'reward' => '<i class="r1"></i> ' . number_format(620 * SPEED) . ' <i class="r2"></i> ' . number_format(730 * SPEED) . ' <i class="r3"></i> ' . number_format(560 * SPEED) . ' <i class="r4"></i> ' . number_format(230 * SPEED) . '',

        'completed' => 'Moc dobře, teď už žádné cenné suroviny nepřijdou nazmar.'
    ),
    '10' => array(
        'Title' => 'Sýpka úrovně 3',
        'Description' => 'Čím vyšší produkce, tím rychleji se sklady plní. Měl bys rozšířit i sýpku.',
        'toDo' => array('Rozšiř sýpku na úroveň 3'),
        'reward' => '<i class="r1"></i> ' . number_format(880 * SPEED) . ' <i class="r2"></i> ' . number_format(1020 * SPEED) . ' <i class="r3"></i> ' . number_format(590 * SPEED) . ' <i class="r4"></i> ' . number_format(320 * SPEED) . '',

        'completed' => 'V sýpce je teď zase místo, takže produkce může pokračovat i v tvé nepřítomnosti.'
    ),
    '11' => array(
        'Title' => 'Mlýn',
        'Description' => 'Mlýn zvyšuje produkci všech obilných polí. Aby se jeho cena vyplatila, potřebuješ dostatečně vysokou základní produkci.',
        'toDo' => array('Rozšiř jedno obilné pole na úroveň 1'),
        'reward' => 'Mlýn úrovně 2',

        'completed' => 'Teď máš pro další stavby k dispozici hodně volného obilí. Existují i budovy, které zvyšují produkci ostatních surovin.'
    ),
    '12' => array(
        'Title' => 'Vše na 5',
        'Description' => 'Potřebuješ mnohem vyšší produkci, abys nemusel dlouho čekat, než si budeš moci dovolit budovy a osadníky potřebné pro druhou vesnici. Rozšiř všechna surovinová pole na úroveň 5.',
        'toDo' => array('Rozšiř všechna surovinová pole na úroveň 5'),
        'reward' => '1 den bonus +25 % k produkci všech surovin',

        'completed' => 'Výborně! Vlastní produkce ti pomůže vycvičit osadníky'
    ),
);
$lang['quests']['world'] = array(
    '1' => array(
        'Title' => 'Zobrazit statistiky',
        'Description' => 'Ve světě Travianu soupeříš s tisíci dalšími hráči. Ve statistikách zjistíš více o své pozici ve hře.',
        'toDo' => array('Otevři statistiky a porovnej se s ostatními hráči'),
        'reward' => '<i class="r1"></i> ' . number_format(90 * SPEED) . ' <i class="r2"></i> ' . number_format(120 * SPEED) . ' <i class="r3"></i> ' . number_format(60 * SPEED) . ' <i class="r4"></i> ' . number_format(30 * SPEED) . '',

        'completed' => 'Kromě pořadí tam najdeš i další užitečné informace. Záložka Top 10 ukazuje nejsilnější útočníky a nejúspěšnější lupiče.'
    ),
    '2' => array(
        'Title' => 'Změnit jméno vesnice',
        'Description' => 'Jméno vesnice, které si sám zvolíš, ukazuje ostatním hráčům, že tvou říši někdo aktivně vede.',
        'toDo' => array('Změň jméno vesnice na ceduli vesnice'),
        'reward' => '100 kulturních bodů',

        'completed' => 'Pěkně, teď jsi udělal první krok k tomu, abys ve světě Travianu zanechal stopu.'
    ),
    '3' => array(
        'Title' => 'Hlavní budova úrovně 3',
        'Description' => 'Abys mohl stavět některé budovy, potřebuješ vyšší úroveň hlavní budovy; navíc čím vyšší úroveň hlavní budovy, tím rychleji stavitelé pracují. Na jedno ale nezapomeň: všechno potřebuje suroviny!',
        'toDo' => array('Rozšiř hlavní budovu na úroveň 3'),
        'reward' => '<i class="r1"></i> ' . number_format(170 * SPEED) . ' <i class="r2"></i> ' . number_format(100 * SPEED) . ' <i class="r3"></i> ' . number_format(130 * SPEED) . ' <i class="r4"></i> ' . number_format(70 * SPEED) . '',

        'completed' => 'Skvělé, větší hlavní budova ti teď umožní stavět další budovy, které jsi právě odemkl.'
    ),
    '4' => array(
        'Title' => 'Postav ambasádu',
        'Description' => 'Svět Travianu je nebezpečné místo a musíš se umět bránit. Nejlepší další obranu nabízejí silní spojenci. Postav ambasádu, abys mohl vstoupit do aliance.',
        'toDo' => array('Postav ambasádu'),
        'reward' => '<i class="r1"></i> ' . number_format(215 * SPEED) . ' <i class="r2"></i> ' . number_format(145 * SPEED) . ' <i class="r3"></i> ' . number_format(195 * SPEED) . ' <i class="r4"></i> ' . number_format(50 * SPEED) . '',

        'completed' => 'Výborně, teď můžeš přijímat pozvánky do aliance. Pozvánky najdeš v ambasádě.'
    ),
    '5' => array(
        'Title' => 'Otevři mapu',
        'Description' => 'Mapa ti ukazuje svět Travianu. Prohlédni si sousedy, najdi spojence a odhal hrozby!',
        'toDo' => array('Otevři mapu v menu'),
        'reward' => '<i class="r1"></i> ' . number_format(90 * SPEED) . ' <i class="r2"></i> ' . number_format(160 * SPEED) . ' <i class="r3"></i> ' . number_format(90 * SPEED) . ' <i class="r4"></i> ' . number_format(95 * SPEED) . '',

        'completed' => 'Jsou poblíž silní hráči nebo aliance? Mapa ti také pomůže najít oázy a místa, kde můžeš založit nové vesnice.'
    ),
    '6' => array(
        'Title' => 'Přečti zprávu',
        'Description' => 'Právě jsi dostal zprávu s užitečnými tipy. Nepřečtené zprávy poznáš podle čísla nad tlačítkem. Podívej se na ni.',
        'toDo' => array('Otevři přehled zpráv a přečti si zprávu od zadavatele úkolů!'),
        'reward' => '<i class="r1"></i> ' . number_format(280 * SPEED) . ' <i class="r2"></i> ' . number_format(315 * SPEED) . ' <i class="r3"></i> ' . number_format(200 * SPEED) . ' <i class="r4"></i> ' . number_format(145 * SPEED) . '',

        'completed' => 'Zprávami komunikuješ s ostatními hráči. Vždy se vyplatí být klidný a zdvořilý, i když spolu bojujete.'
    ),
    '7' => array(
        'Title' => 'Zlato navíc',
        'Description' => 'Během výuky jsi už zlatem urychlil stavby. V obchodě se zlatem zjistíš, k čemu dalšímu můžeš zlato použít.',
        'toDo' => array('Podívej se na výhody, které si můžeš koupit za zlato'),
        'reward' => '<img src="img/x.gif" title="gold" class="gold"> 20',

        'completed' => 'Tady máš zase trochu zlata zdarma, abys mohl využít některé zlaté výhody.'
    ),
    '8' => array(
        'Title' => 'Aliance',
        'Description' => 'Hledej spojence a vstup do aliance. Pokud zatím nemáš žádné kontakty, podívej se na aliance hráčů ve svém okolí nebo hledej alianci na fóru.',
        'toDo' => array('Vstup do aliance'),
        'reward' => '<i class="r1"></i> ' . number_format(295 * SPEED) . ' <i class="r2"></i> ' . number_format(210 * SPEED) . ' <i class="r3"></i> ' . number_format(235 * SPEED) . ' <i class="r4"></i> ' . number_format(185 * SPEED) . '',

        'completed' => 'Máme skvělý začátek. Čím silnější a aktivnější je každý hráč, tím silnější budete jako tým. Už víte, jak si navzájem hlásit útoky a žádat o pomoc?'
    ),
    '9' => array(
        'Title' => 'Hlavní budova úrovně 5',
        'Description' => 'Je čas rozšířit hlavní budovu, abys mohl stavět nové budovy. Nezapomeň zároveň pečovat o produkci surovin.',
        'toDo' => array('Rozšiř hlavní budovu na úroveň 5'),
        'reward' => '<i class="r1"></i> ' . number_format(570 * SPEED) . ' <i class="r2"></i> ' . number_format(470 * SPEED) . ' <i class="r3"></i> ' . number_format(560 * SPEED) . ' <i class="r4"></i> ' . number_format(265 * SPEED) . '',

        'completed' => 'Skvělé, že? Teď se tvé budovy budou stavět rychleji. Vyzvedni si odměnu a připrav se na další úkol.'
    ),
    '10' => array(
        'Title' => 'Sídlo vlády',
        'Description' => 'Postav teď sídlo vlády, abys mohl brzy založit novou vesnici. Pokud si nejsi jistý, jestli má tato vesnice zůstat tvým hlavním městem, vyber rezidenci.',
        'toDo' => array('Sídlo vlády'),
        'reward' => '<i class="r1"></i> ' . number_format(525 * SPEED) . ' <i class="r2"></i> ' . number_format(420 * SPEED) . ' <i class="r3"></i> ' . number_format(620 * SPEED) . ' <i class="r4"></i> ' . number_format(335 * SPEED) . '',

        'completed' => 'Tato budova je potřeba k založení nebo dobytí nové vesnice. Její úroveň omezuje počet možných rozšíření.'
    ),
    '11' => array(
        'Title' => 'Kulturní body',
        'Description' => 'Abys mohl ve své říši vládnout více vesnicím, potřebuješ kulturní body. Přehled v rezidenci nebo paláci ti ukáže, kolik ti chybí a jak dlouho to bude trvat.',
        'toDo' => array('Otevři v rezidenci nebo paláci záložku kulturních bodů'),
        'reward' => '<i class="r1"></i> ' . number_format(650 * SPEED) . ' <i class="r2"></i> ' . number_format(800 * SPEED) . ' <i class="r3"></i> ' . number_format(740 * SPEED) . ' <i class="r4"></i> ' . number_format(530 * SPEED) . '',

        'completed' => 'V seznamu vesnic také vidíš aktuální stav možných nových vesnic a počet chybějících kulturních bodů. V \\"Odpovědích\\" zjistíš, jak kulturní body rychle zvýšit.'
    ),
    '12' => array(
        'Title' => 'Sklad surovin úrovně 7',
        'Description' => 'Rozšiř sklad surovin a připrav se na založení nové vesnice. Současná kapacita brzy nebude stačit na potřebné budovy a osadníky.',
        'toDo' => array('Rozšiř sklad surovin na úroveň 7'),
        'reward' => '<i class="r1"></i> ' . number_format(2650 * SPEED) . ' <i class="r2"></i> ' . number_format(2150 * SPEED) . ' <i class="r3"></i> ' . number_format(1810 * SPEED) . ' <i class="r4"></i> ' . number_format(1320 * SPEED) . '',

        'completed' => 'Skvělé, kapacita skladů by teď měla nějakou dobu stačit. Nezapomeň své cenné suroviny bránit nebo schovávat!'
    ),

    '13' => array(
        'Title' => 'Rezidence nebo palác úrovně 10',
        'Description' => 'Osadníky lze cvičit v paláci nebo rezidenci. Na záložce "Cvičit" uvidíš potřebnou úroveň budovy.',
        'toDo' => array('Rozšiř rezidenci nebo palác na úroveň 10'),
        'reward' => '500 kulturních bodů',

        'completed' => 'Z každé vesnice můžeš ovládat jen 2 až 3 nové vesnice. K nové vesnici ti teď chybí jen 3 osadníci a hodně kulturních bodů!'
    ),
    '14' => array(
        'Title' => 'Vycvič tři osadníky',
        'Description' => 'K založení nové vesnice musí všichni tři osadníci vyrazit společně. Ve vesnici je proto musíš chránit před útoky protivníků a vyslat je co nejdřív, jakmile budou podmínky splněny.',
        'toDo' => array('Vycvič tři osadníky'),
        'reward' => '<i class="r1"></i> ' . number_format(1050 * SPEED) . ' <i class="r2"></i> ' . number_format(800 * SPEED) . ' <i class="r3"></i> ' . number_format(900 * SPEED) . ' <i class="r4"></i> ' . number_format(750 * SPEED) . '',

        'completed' => 'Dobře chraň své osadníky před útoky, dokud nejsou připraveni vyrazit!'
    ),
    '15' => array(
        'Title' => 'Zakládání nové vesnice',
        'Description' => 'Najdi na mapě dobré místo k osídlení. Chceš ho mít blízko své vesnice, s vyšší produkcí jedné suroviny nebo poblíž mnoha oáz? Založ pomocí osadníků druhou vesnici!',
        'toDo' => array('Založit novou vesnici'),
        'reward' => '48 hodin Travian Plus.',

        'completed' => 'Skvělé, patříš mezi nejmocnější říše ve světě Travianu. Hraj dál a cvič spoustu jednotek k obraně před nepřáteli.'
    ),
);

$lang['quests']['monitor'] = array(
    '1' => array('Začít výuku'),
    '2' => array('Zavřít úkoly', 'Otevřít úkoly', 'Vypnout tipy'),
    '3' => array('Otevřít pole dřevorubce', 'postavit dřevorubce'),
    '4' => array('Otevřít budovu', 'Dřevorubec 2'),
    '5' => array('Otevřít obilné pole', 'Postavit obilné pole'),
    '6' => array('Klikni na obrázek hrdiny', 'Změnit produkci'),
    '7' => array('Vstup do centra vesnice'),
    '8' => array('Klikni na stavební místo', 'Postav sklad surovin'),
    '9' => array('Klikni na místo shromaždiště', 'Postav shromaždiště'),
    '10' => array('Dokončit stavbu'),
    '11' => array('Dobrodružství hrdiny'),
    '12' => array('Menu hlášení', 'Přečíst hlášení'),
    '13' => array('Inventář hrdiny', 'Vyléčit hrdinu'),
    '14' => array('Nápověda k uživatelskému rozhraní'),
    '15' => array('Konec výuky')
);

$lang['quests']['monitor']['battle'] = array(
    '01' => 'Další dobrodružství',
    '02' => 'Postav úkryt',
    '03' => 'Postav kasárna',
    '04' => 'Dokonči 5 dobrodružství',
    '05' => 'Cvičit jednotky',
    '06' => 'Zemní val',
    '07' => 'Zaútoč na oázu',
    '08' => '10 dobrodružství',
    '09' => 'Aukce',
    '10' => 'Rozšiř kasárna',
    '11' => 'Postav akademii',
    '12' => 'Vyzkoumej jednotku',
    '13' => 'Postav kovárnu',
    '14' => 'Vylepši jednotky'
);

$lang['quests']['monitor']['economy'] = array(
    '01' => 'Železný důl',
    '02' => 'Více surovin',
    '03' => 'Sýpka',
    '04' => 'Vše na jedničku',
    '05' => 'Na dvojku!',
    '06' => 'Tržiště',
    '07' => 'Obchod',
    '08' => 'Vše na 2',
    '09' => 'Sklad surovin úrovně 3',
    '10' => 'Sýpka úrovně 3',
    '11' => 'Mlýn',
    '12' => 'Vše na 5'
);

$lang['quests']['monitor']['world'] = array(
    '01' => 'Zobrazit statistiky',
    '02' => 'Změnit jméno vesnice',
    '03' => 'Hlavní budova úrovně 3',
    '04' => 'Postav ambasádu',
    '05' => 'Otevři mapu',
    '06' => 'Přečti zprávu',
    '07' => 'Zlato navíc',
    '08' => 'Aliance',
    '09' => 'Hlavní budova úrovně 5',
    '10' => 'Sídlo vlády',
    '11' => 'Kulturní body',
    '12' => 'Sklad surovin úrovně 7',
    '13' => 'Rezidence nebo palác úrovně 10',
    '14' => '3 osadníci',
    '15' => 'Nová vesnice',
);

$lang['quests']['Main'] = array(
    'QuestsList' => 'Seznam úkolů',
    'Quest' => 'Úkol',
    'Reward' => 'Tvá odměna',
);

$lang['main']['options'] = array(
    '1' => 'Hra',
    '2' => 'nastavení jazyka',
    '3' => 'Jazyk',
    '4' => 'Angličtina',
    '5' => 'Turečtina',
    '6' => 'uložit',
);

$lang['links'] = array(
    'Farms' => 'Systém seznamu farem',
    'Support' => 'Kontaktovat podporu',
);

$lang['Report'] = array(
    'Silver' => 'Stříbro',
);


$lang['Msgs'] = array(
    'wMSGT' => 'Zpráva od Multihuntera',
    'wMSGI' => 'Čeká na tebe skvělá cena.<br><br>Tato zpráva byla vygenerována automaticky, neodpovídej na ni.',
    'Arts' => '<div style="width:450px; height:830px; padding: 95px 60px
	60px 25px; background:
	url(img/Natars_Banner_gross.jpg)
	no-repeat;">
			<center>
				<h1>Artefakty</h1>
				<p style="font-size:85%; text-align:justify; width:400px">
				Objevily se artefakty a je čas získat ty užitečné. Pospěš si, ať ti artefakt nikdo nevyfoukne!
					<br><br><img src="img/msg.jpg" alt="Artefakty" width="400" height="200" style="float:
	right">
					<br><br>
					Artefakty jsou cenné a užitečné a každý má jiný účinek. Někteří hráči touží po artefaktech, které zrychlují výcvik jednotek nebo snižují spotřebu obilí, jiní dávají přednost jiným účinkům. Co chceš ty? Pospěš si a získej artefakt – a nezapomeň, že samotné vlastnictví nestačí: k jeho obraně budeš potřebovat podporu své aliance, tak se dobře připrav!	</p><br><br>
	<span style="font-size:60%; color: #666;"></span>
	</center>
	</div>',


    'WW' => 'Od prvních bitev u natarských hradeb uplynula nespočetná doba, mnoho armád padlo pod jejich zdmi a krajinou se začal šířit pach smrti. A to je teprve začátek! Připrav se na to, co přijde!
	<br> <br>
	Zvědové se vrátili s příběhy a výjevy, z nichž běhá mráz po zádech: nikdo, kdo se vydal na cestu, se nevrátil živý. Je to obrovská, krutá a nemilosrdná síla, která drtí naděje lidí. Začal závod – vyzbroj svou obranu i armádu k odporu i k útoku, dokud není pozdě.
	<br> <br>
	Objevily se stavební plány, díky nimž lze zvyšovat úroveň Divu světa. Získej je dřív, než ti je vezme protivník!
	<br> <br>
	Desítky zvědů bloudí krajinou a hledají cokoli, ale k místům síly se jen tak nedostaneš! Potíže však začaly a tajemství této říše vycházejí najevo!
	<br> <br>
	Blíží se konec, kdy se nejsilnější armády střetnou na bojišti. Tato válka se bude v čase opakovat – je to tvá válka a tvá šance zapsat své jméno do dějin. Tady se můžeš stát legendou...
	<br> <br>
	<span style="font-size:60%; color: #666;"></span>
	<br><br>
	<br><br>
	<b> Požadavky </b> <i>: Pro ukořistění stavebního plánu musí být splněny tyto podmínky </i> <br>
	<li> Pošli útok (ne loupež) na vesnici s artefaktem  </li>
	<li> Musíš bitvu vyhrát </li>
	<li> Musíš zničit pokladnici, ve které je artefakt </li>
	<li> Hrdina se musí bitvy účastnit, aby artefakt odnesl </li>
	<li> Potřebná úroveň pokladnice: 10 </li>
	<br> <br>
	Tak se vydej pro svůj artefakt! Pokud útok uspěje a artefakt ukořistíš, po příchodu do tvé pokladnice se aktivuje jeho zvláštní schopnost!
	<br> <br>
	Pro stavbu Divu světa do úrovně 49 potřebuješ vlastnit stavební plán; pro stavbu nad úroveň 49 až do úrovně 100 musí druhý stavební plán vlastnit tvůj spojenec z aliance!');

$lang['Footer'] = array(
    'Homepage' => 'Domovská stránka',
    'Forum' => 'Fórum',
    'Links' => 'Odkazy',
    'FAQ' => 'Časté dotazy – odpovědi',
    'Terms' => 'Podmínky',
    'Imprint' => 'Tiráž'
);
$lang['Hero'] = array(
    'status01' => 'Hrdina je na dobrodružství',
    'status02' => 'Hrdina se vrací',
    'status03' => 'Hrdina je mrtvý',
    'status04' => 'Hrdina brání vesnici',
    'status05' => 'Hrdina je momentálně ve vesnici',

    'adv00' => 'Nové dobrodružství',
    'adv01' => 'Čas dobrodružství',
    'adv02' => 'Příchod za',
    'adv03' => 'Zpět za',
    'adv04' => 'Hod',
    'adv05' => 'Na dobrodružství',
    'adv06' => 'zpět',

    'Speed' => 'rychlost',
    'Attributes' => 'Vlastnosti',
    'saveChanges' => 'Ulož prosím změny',

    'FStrength' => 'Bojová síla',
    'FHero' => 'od hrdiny',

    'OW01' => 'Příchod za',
    'OW02' => 'v',
    'OW03' => 'Průběh dobrodružství uvidíš na',
    'OW04' => 'Shromaždiště',

    'Revive01' => 'Domovem hrdiny je',
    'Revive02' => 'bude oživen ve vesnici',
    'Revive03' => 'pro změnu domovské vesnice hrdiny nebo jeho oživení v jiné vesnici',
    'Revive04' => 'Cena oživení',
    'Revive05' => 'Hrdina se oživuje v',
    'Revive06' => 'Zbývající čas',
);

$lang['Map'] = array(
    '0' => 'Mapa',
    '1' => 'Filtr',
    '2' => 'ok',
    '3' => 'souřadnice',
    '4' => 'aliance',

    '5' => 'hráč',
    '6' => 'obyvatelé',
    '7' => 'aliance',
    '8' => 'národ',
    '9' => 'vesnice',
    '10' => 'Neobsazená oáza',
    '11' => 'Obsazená oáza',
    '12' => 'Souřadnice',
    '13' => 'načítání',

    '14' => 'Říman',
    '15' => 'Germán',
    '16' => 'Galové',
    '17' => 'Nataři',
    '18' => 'Nataři',
    '19' => 'Egypťané',
    '20' => 'Hunové',

    '21' => 'Natarská vesnice',
    '22' => 'Vyhrál jsi útok bez ztrát',
    '23' => 'Vyhrál jsi útok se ztrátami',
    '24' => 'Tvým vojákům nikdo neunikl',
    '25' => 'Ubránil ses bez ztrát',
    '26' => 'Ubránil ses se ztrátami',
    '27' => 'Vesnice byla napadena',
    '28' => 'Ubránil ses se ztrátami',

    '29' => 'Dřevo',
    '30' => 'Hlína',
    '31' => 'Železo',
    '32' => 'Obilí',
);

$lang['quests']['achievements'] = array(
    '1' => array(
        'Title' => 'Dokonči dobrodružství',
        'Description' => 'Pošli hrdinu na dobrodružství. Úkol je splněn, jakmile hrdina dorazí, i když dobrodružství nepřežije. Hrdinu na dobrodružství pošleš kliknutím na ikonu na obrázku.<br><br>Body za tento úkol lze získat 1× denně',
        'toDo' => 'Tento úkol má hodnotu + 5 bodů',
        'questIn' => array('questGive' => 5, 'Hard' => 'střední', 'needReq' => 'Dostupné dobrodružství')
    ),
    '2' => array(
        'Title' => 'Vyloupi neobsazenou oázu',
        'Description' => 'Pošli jednotky vyloupit neobsazenou oázu. Úkol je splněn, jakmile armáda dorazí, i když v boji padne. Použití klecí k vyhnutí se boji body nepřinese. Výsledek loupeže si můžeš spočítat v bojovém simulátoru na shromaždišti.<br><br>Body za tento úkol lze získat 3× denně',
        'toDo' => 'Tento úkol má hodnotu + 3 body',
        'questIn' => array('questGive' => 3, 'Hard' => 'těžké', 'needReq' => '(Hodně) jednotek')
    ),
    '3' => array(
        'Title' => 'Vyloupi/zaútoč na natarskou vesnici',
        'Description' => 'Pošli jednotky vyloupit/napadnout natarskou vesnici. Úkol je splněn, jakmile armáda dorazí, i když v boji padne. Na vesnici s Divem světa ovládanou Natary neútoč, dokud nemáš alespoň 100 000 jednotek.
		<br><br>Body za tento úkol lze získat 3× denně',
        'toDo' => 'Tento úkol má hodnotu + 3 body',
        'questIn' => array('questGive' => 9, 'Hard' => 'náročné', 'needReq' => '(Hodně) jednotek')
    ),
    '4' => array(
        'Title' => 'Vyhraj aukci',
        'Description' => 'Účastí v aukci vyhraješ dvakrát – poprvé, když aukci vyhraješ a získáš předmět, a podruhé, když získáš body do denních odměn. Body dostaneš, jakmile vyhraješ jakoukoli aukci, ve které přihazuješ.<br><br>Body za tento úkol lze získat 1× denně',
        'toDo' => 'Tento úkol má hodnotu + 5 bodů',
        'questIn' => array('questGive' => 9, 'Hard' => 'náročné', 'needReq' => 'Dokonči 10 dobrodružství')
    ),
    '5' => array(
        'Title' => '0 / 3 Získej nebo utrať zlato',
        'Description' => 'Pro získání bodů za tento úkol musíš zlato buď získat, nebo utratit. Je jen na tobě, jak a k čemu svůj zůstatek zlata použiješ.<br><br>Body za tento úkol lze získat 3× denně',
        'toDo' => 'Tento úkol má hodnotu + 2 body',
        'questIn' => array('questGive' => 6, 'Hard' => 'střední', 'needReq' => 'Zlato')
    ),
    '6' => array(
        'Title' => 'Rozšiř budovu',
        'Description' => 'Pro získání bodů za tento úkol musíš rozšířit existující budovu nebo postavit novou. Body dostaneš po dokončení stavby.<br><br>Body za tento úkol lze získat 3× denně',
        'toDo' => 'Tento úkol má hodnotu + 4 body',
        'questIn' => array('questGive' => 12, 'Hard' => 'střední', 'needReq' => 'Suroviny')
    ),
    '7' => array(
        'Title' => 'Rozšiř surovinové pole',
        'Description' => 'Pro získání bodů za tento úkol musíš rozšířit existující surovinové pole nebo postavit nové. Body dostaneš po dokončení stavby.<br><br>Body za tento úkol lze získat 3× denně.',
        'toDo' => 'Tento úkol má hodnotu + 5 bodů',
        'questIn' => array('questGive' => 15, 'Hard' => 'střední', 'needReq' => 'Suroviny')
    ),
    '8' => array(
        'Title' => 'Vycvič najednou 20 pěšáků jednoho typu',
        'Description' => 'Pro získání bodů za tento úkol musíš v kasárnách najednou zadat výcvik 20 pěších jednotek. Jednotky, které už jsou ve frontě, se nepočítají.<br><br>Body za tento úkol lze získat 3× denně',
        'toDo' => 'Tento úkol má hodnotu + 4 body',
        'questIn' => array('questGive' => 12, 'Hard' => 'náročné', 'needReq' => 'Kasárna')
    ),
    '9' => array(
        'Title' => 'Vycvič najednou 20 jezdců jednoho typu',
        'Description' => 'Pro získání bodů za tento úkol musíš ve stáji najednou zadat výcvik 20 jízdních jednotek. Jednotky, které už jsou ve frontě, se nepočítají.<br><br>Body za tento úkol lze získat 3× denně',
        'toDo' => 'Tento úkol má hodnotu + 4 body',
        'questIn' => array('questGive' => 12, 'Hard' => 'náročné', 'needReq' => 'Stáj')
    ),
    '10' => array(
        'Title' => 'Uspořádej malou nebo velkou slavnost',
        'Description' => 'Uspořádej malou nebo velkou slavnost na své radnici.<br>Body dostaneš, když uspořádáš jakoukoli slavnost. Již probíhající slavnosti se nepočítají.<br><br>Body za tento úkol lze získat 3× denně',
        'toDo' => 'Tento úkol má hodnotu + 5 bodů',
        'questIn' => array('questGive' => 15, 'Hard' => 'těžké', 'needReq' => 'Radnice')
    ),
);

$lang['Daily'] = array(
    '01' => 'Dokonči dobrodružství',
    '02' => 'Vyloupi neobsazenou oázu',
    '03' => 'Vyloupi/zaútoč na natarskou vesnici',
    '04' => 'Vyhraj aukci',
    '05' => 'Získej nebo utrať zlato',
    '06' => 'Rozšiř budovu',
    '07' => 'Rozšiř surovinové pole',
    '08' => 'Vycvič najednou 20 pěšáků jednoho typu',
    '09' => 'Vycvič najednou 20 jezdců jednoho typu',
    '10' => 'Uspořádej malou nebo velkou slavnost',

    'Close' => 'zavřít',
    'Overview' => 'Přehled',
    'PG' => 'Body udělené za tento úkol:',
    'Difficulty' => 'Obtížnost',
    'Requirement' => 'Požadavek',
    'CRewards' => 'Vyzvednout odměnu',

    'Congrats01' => 'Gratulujeme! Nasbíral jsi dost bodů na odměnu!',
    'Congrats02' => 'Za nasbíraných',
    'Congrats03' => 'bodů dnes si teď můžeš vyzvednout odměnu',
    'Congrats04' => 'Tvá denní odměna se náhodně vybere z těchto možností',
    'Congrats05' => 'Za nasbíraných',
    'Congrats06' => 'bodů dnes získáváš tuto odměnu',
);

define('markASRead', 'Označit jako přečtené');
define('rMessage', 'Napsat zprávu');
define('Ignored', 'Ignorovaní hráči');
define('Ignored01', 'Pokud chceš ignorovat zprávy od určitého hráče, otevři jeho profil a klikni na "Ignorovat"!');
define('Ignored02', 'Ignorovat hráče.');
define('Ignored03', 'Hráč bude ignorován.');
define('Ignored04', 'Přestat ignorovat tohoto hráče.');
define('Ignored05', 'Přijímat zprávy od hráče.');

define("herostatus9", 'Na cestě');
define("herostatus100", 'Ve vesnici');
define("herostatus101", 'Hrdina je mrtvý');
define("herostatus102", 'Hrdina je v kleci');
define("herostatus103", 'Hrdina brání');


$lang['Profile'] = array(
    '00' => 'Profil hráče',
    '01' => 'Podrobnosti',
    '02' => 'Narozeniny',
    '03' => 'Pohlaví',
    '04' => 'N/A',
    '05' => 'muž',
    '06' => 'žena',
);

$lang['Alliance'] = array(
    '00' => 'Nejsi v alianci.',
);

$lang['Logout'] = array(
    '01' => 'Zpět do hry',
    '02' => 'Jméno účtu nebo e-mailová adresa',
    '03' => 'Heslo',
    '04' => 'Přihlásit',
);


// PHP 8: constants used by the templates but missing from this language file.
// PHP 7 printed the bare constant name; PHP 8 throws "Undefined constant".
foreach (array(
	'R1' => 'Dřevo', 'R2' => 'Hlína', 'R3' => 'Železo', 'R4' => 'Obilí',
	'KARG' => 'Natarská oáza',
	'IEFF32s' => 'O 50 % rychlejší jednotky mezi vlastními vesnicemi',
	'XTRAVIAN' => 'Travian ', 'RU' => '',
	'Villages' => 'Vesnice',
	'DAYS' => 'dny', 'STARTNOW' => 'Server byl spuštěn!',
	'LOGIN_NO_JAVASCRIPT' => 'Pro hraní povol v prohlížeči JavaScript.',
	'NAME_EMPTY' => 'Jméno je prázdné', 'NAME_NO_EXIST' => 'Hráč neexistuje: ',
	'SAME_NAME' => 'Nemůžeš pozvat sám sebe',
	'ALREADY_INVITED' => ' už je pozván', 'ALREADY_IN_ALLY' => ' už je v této alianci',
	'BL_CAPITAL' => 'Hlavní město', 'HERO_HERODEADORNOTHERE' => 'Hrdina je mrtvý nebo není ve vesnici',
	'PD_LUMBER' => 'Dřevo', 'PD_CLAY' => 'Hlína', 'PD_IRON' => 'Železo', 'PD_CROP' => 'Obilí',
	'RES_PRODUCTION' => 'Produkce', 'RES_FULLIN' => 'Plné za', 'RES_MOREINFO' => 'Klikni pro další informace',
	'RES_CROPBALANCETITLE' => 'Bilance obilí', 'RES_CROPBALANCE' => 'Zůstatek',
	'farmlist27' => 'Upravit všechny loupeže', 'flag' => 'Vlajka',
) as $__k => $__v) {
	if (!defined($__k)) define($__k, $__v);
}
unset($__k, $__v);
