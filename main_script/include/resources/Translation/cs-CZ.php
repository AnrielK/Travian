<?php

use Core\Config;
use Model\DailyQuestModel;
use Model\Quest;

global $Definition;

$Definition['403Error']['You cannot pass!'] = 'Tudy neprojdeš!';
$Definition['403Error']['I am a servant of the Secret Order, wielder of the flame of 403, You cannot pass'] = 'Jsem služebník Tajného řádu, strážce plamene 403. Tudy neprojdeš.';
//Academy
$Definition['Academy'] = [
    "zoomIn" => "přiblížit",
    "Researches for village %s" => "Výzkumy pro vesnici %s",
    "ResearchesForVillage" => "Výzkumy pro vesnici",
    "noResearchAvailableDesc" => "Momentálně nelze vyzkoumat žádné nové jednotky. Předpoklady pro nové jednotky zjistíš kliknutím na obrázek příslušné jednotky v příručce.",
    "showMore" => "zobrazit více",
    "hideMore" => "zobrazit méně",
    "Researching" => "Probíhá výzkum",
    "unit" => "Jednotka",
    "research" => "Vyzkoumat",
    "one_research_is_going" => "Jeden výzkum už probíhá.",
];

$Definition['ActivationNew'] = [
    'unableToGenerateBase' => 'Nepodařilo se nám pro tebe vytvořit novou vesnici. Zkus to prosím za chvíli znovu nebo kontaktuj administrátora.',

    'vidSelectDescription' => 'Velké říše začínají důležitými rozhodnutími! Jsi útočník, který miluje soutěžení? Nebo máš na hru spíše málo času? Jsi týmový hráč, který rád buduje prosperující ekonomiku?',
    'sectorSelectDescription' => 'Kde chceš začít budovat svou říši? Pro nejlepší polohu zvol „doporučenou“ oblast. Nebo si vyber oblast, kde jsou tví přátelé, a spojte se!',
    'recommendedForNewPlayers' => 'Doporučeno pro nové hráče',
    'Select your starting position' => 'Vyber si výchozí pozici',
    'Select your tribe' => 'Vyber si národ',
    'Confirm' => 'Potvrdit',
    'back' => 'zpět',
    'New' => 'Nové',
    'RECOMMENDED' => 'DOPORUČENO',
    'Ready to rule the world?' => 'Připraven vládnout světu?',
    'PLAY NOW' => 'HRÁT',
    'North - West' => 'Severozápad',
    'North - East' => 'Severovýchod',
    'South - West' => 'Jihozápad',
    'South - East' => 'Jihovýchod',
    'selectionComplete' => 'Výběr je hotový. Ještě jedno kliknutí a přijmeš výzvu!',
    "race1_attributes" => [
        0 => 'Střední časová náročnost',
        1 => 'Nejrychleji rozvíjí vesnice',
        2 => 'Velmi silné, ale drahé jednotky',
        3 => 'Pro nové hráče náročný',
    ],
    'race2_attributes' => [
        0 => 'Vysoká časová náročnost',
        1 => 'Dobrý v loupení na začátku hry',
        2 => 'Silná a levná pěchota',
        3 => 'Pro agresivní hráče',
    ],
    "race3_attributes" => [
        0 => 'Nízká časová náročnost',
        1 => 'Ochrana před loupením a dobrá obrana',
        2 => 'Výborná a rychlá jízda',
        3 => 'Vhodný pro nové hráče',
    ],
    "race6_attributes" => [
        0 => 'Nízká časová náročnost',
        1 => 'Více dostupných surovin',
        2 => 'Výborné obranné jednotky',
        3 => 'Vhodný pro nové hráče',
    ],
    "race7_attributes" => [
        0 => 'Vysoká časová náročnost',
        1 => 'Působivě silná jízda',
        2 => 'Při ochraně závisí na ostatních',
        3 => 'Nedoporučeno pro nové hráče!',
    ],
];

$Definition['activation'] = [
    'unableToGenerateBase' => 'Nepodařilo se nám pro tebe vytvořit novou vesnici. Zkus to prosím za chvíli znovu nebo kontaktuj administrátora.',

    "selectASector" => "Vyber výchozí pozici",
    "sector_nw" => "Začneš na severozápadě.",
    "sector_ne" => "Začneš na severovýchodě.",
    "sector_sw" => "Začneš na jihozápadě.",
    "sector_se" => "Začneš na jihovýchodě.",
    "race_helpers" => [
        1 => "Quintus",
        2 => 'Henrik',
        3 => 'Ambiorix',
    ],
    "selectedRace" => "Vybral sis národ %s. %s bude od teď tvým rádcem.",
    "changeVid" => "Změnit národ",
    "submitSector" => "Založit vesnici",
    "sectorDescription" => 'Vyber výchozí polohu své vesnice kliknutím na mapu.',
    "selectARace" => "Vyber národ.",
    "submitKind" => "Vyber národ",
    "attributes" => "Vlastnosti",
    "thanksForActivation" => 'Děkujeme za aktivaci účtu.',
    "pleaseChooseATribe" => 'Vyber si národ pro tento svět.',
    "vidDescription" => 'Pokud jsi v TRAVIANu nový, doporučujeme zvolit Galy.',
    "race1_attributes" => [
        0 => 'Střední časová náročnost.',
        1 => 'Může nejrychleji rozvíjet vesnice.',
        2 => 'Velmi silné, ale drahé jednotky, dobrá <br />pěchota.',
        3 => 'Na začátku hry nemá téměř žádné výhody, <br /> nedoporučeno pro nové <br /> hráče.',
    ],
    'race2_attributes' => [
        0 => 'Útočná hra vyžaduje dostatek času',
        1 => 'Levné jednotky lze vyrábět rychle,<br /> dobré v loupení.',
        2 => 'Pro agresivní a zkušené hráče.',
    ],
    "race3_attributes" => [
        0 => 'Nízká časová náročnost.',
        1 => 'Lepší ochrana před loupením a dobrá obrana už od začátku.',
        2 => 'Velmi dobrá jízda, nejrychlejší jednotky ve hře.',
        3 => 'Vhodný pro nové hráče.',
    ],
];
//Alliance
$Definition['Alliance']['Kicking/inviting is not allowed at this time'] = 'Vyhazování/zvaní momentálně není povoleno.';
$Definition['Alliance']['Enter tag'] = 'Zadej zkratku.';
$Definition['Alliance']['Enter name'] = 'Zadej jméno.';
$Definition['Alliance']['Tag too long'] = 'Zkratka je příliš dlouhá.';
$Definition['Alliance']['Name too long'] = 'Jméno je příliš dlouhé.';
$Definition['Alliance']['Category'] = 'Kategorie';
$Definition['Alliance']['week'] = 'Týden';
$Definition['Alliance']['bbcode'] = 'BB kód';
$Definition['Alliance']['Medals'] = 'Medaile';
$Definition['Alliance']['BB codes'] = 'BB kód';
$Definition['Alliance']['News'] = 'Novinky';
$Definition['Alliance']['Number'] = 'Počet';
$Definition['Alliance']['losses'] = 'ztráty';
$Definition['Alliance']['strength'] = 'síla';
$Definition['Alliance']['Title'] = 'Název';
$Definition['Alliance']['URL'] = 'URL';
$Definition['Alliance']['Hint'] = 'Nápověda';
$Definition['Alliance']['Tip'] = 'Tip';
$Definition['Alliance']['confederacy with x'] = 'Konfederace s %s';
$Definition['Alliance']['NAP with x'] = 'Pakt o neútočení s %s';
$Definition['Alliance']['war with x'] = 'Válka s %s';
$Definition['Alliance']['accept'] = 'Přijmout';
$Definition['Alliance']['Select'] = 'Vybrat';
$Definition['Alliance']['Confirm'] = 'Potvrdit';
$Definition['Alliance']['wrongPassword'] = 'Špatné heslo';
$Definition['Alliance']['In order to kick the player you have to enter your password again for security reasons'] = 'Pro vyhození hráče musíš z bezpečnostních důvodů znovu zadat heslo.';
$Definition['Alliance']['Password'] = 'Heslo';
$Definition['Alliance']['Cant kick player'] = 'Hráče nelze vyhodit.';
$Definition['Alliance']['InvitesAreClosed'] = 'Pozvánky jsou uzavřené.';
$Definition['Alliance']['There is already an offer'] = 'Nabídka už existuje.';
$Definition['Alliance']['Alliance x does not exists'] = 'Aliance %s neexistuje.';
$Definition['Alliance']['offer a confederacy'] = 'nabídnout konfederaci';
$Definition['Alliance']['offer a NAP'] = 'nabídnout pakt o neútočení';
$Definition['Alliance']['declare war'] = 'vyhlásit válku';
$Definition['Alliance']['Own offers'] = 'Vlastní nabídky';
$Definition['Alliance']['Foreign offers'] = 'Cizí nabídky';
$Definition['Alliance']['Send'] = 'Odeslat';
$Definition['Alliance']['DiplomacyShowText'] = '<div class="text">Pokud chceš v popisu aliance automaticky zobrazit vztahy, napiš do popisu <span class="e">[diplomatie]</span>, možné jsou také <span class="e">[ally]</span>, <span class="e">[nap]</span> a <span class="e">[war]</span>.		</div>';
$Definition['Alliance']['DiplomacyHint'] = 'K diplomatické etiketě patří promluvit si s druhou aliancí a vyjednávat dříve, než pošleš nabídku paktu o neútočení (NAP) nebo konfederace.';
$Definition['Alliance']['Existing relationships'] = 'Existující vztahy';
$Definition['Alliance']['The player x does`t exists'] = 'Hráč %s neexistuje.';
$Definition['Alliance']['none'] = 'žádné';
$Definition['Alliance']['draw back'] = 'Zrušit';
$Definition['Alliance']['invite sent to x'] = 'pozvánka odeslána hráči %s.';
$Definition['Alliance']['invitation for x'] = 'pozvánka pro %s';
$Definition['Alliance']['test has already received an invitation'] = '%s už pozvánku dostal.';
$Definition['Alliance']['Invitations'] = 'Pozvánky';
$Definition['Alliance']['invite'] = 'pozvat';
$Definition['Alliance']['toTheForum'] = 'Odkaz na fórum';
$Definition['Alliance']['you just left your alliance'] = 'právě jsi opustil svou alianci.';
$Definition['Alliance']['In order to quit the alliance you have to enter your password gain for safety reasons'] = 'Pro opuštění aliance musíš z bezpečnostních důvodů znovu zadat heslo.';
$Definition['Alliance']['x has been kicked from the alliance'] = '%s byl z aliance vyhozen.';
$Definition['Alliance']['If your alliance wants to use an external forum, you can enter the URL here'] = 'Pokud chce tvá aliance používat externí fórum, můžeš sem zadat jeho URL.';
$Definition['Alliance']['choose player'] = 'Vyber hráče';
$Definition['Alliance']['Manage flags and markers in map'] = 'Spravovat vlajky a značky na mapě';
$Definition['Alliance']['Manage forums'] = 'Spravovat fóra';
$Definition['Alliance']['IGMs to every alliance member'] = 'Zprávy všem členům aliance';
$Definition['Alliance']['You can set up different permissions for each alliance member and assign positions'] = 'Každému členovi aliance můžeš nastavit různá oprávnění a přidělit pozice.';
$Definition['Alliance']['fighting points'] = 'bojové body';
$Definition['Alliance']['Tag exists'] = 'Zkratka už existuje.';
$Definition['Alliance']['Changes saved'] = 'Změny uloženy.';
$Definition['Alliance']['Date'] = 'Datum';
$Definition['Alliance']['Don´t show attacks of own alliance (under 100 units, no losses)'] = 'Nezobrazovat útoky vlastní aliance (pod 100 jednotek, bez ztrát)';
$Definition['Alliance']['noReports'] = 'žádná hlášení.';
$Definition['Alliance']['online now'] = 'právě online';
$Definition['Alliance']['active players'] = 'Aktivní během 10 minut';
$Definition['Alliance']['active 3days'] = 'Aktivní během 3 dnů';
$Definition['Alliance']['active 7days'] = 'Aktivní během 7 dnů';
$Definition['Alliance']['inactive'] = 'neaktivní';
$Definition['Alliance']['Rank'] = 'Pořadí';
$Definition['Alliance']['Points'] = 'Body';
$Definition['Alliance']['Change name'] = 'Změnit jméno';
$Definition['Alliance']['Change alliance description'] = 'Změnit popis aliance';
$Definition['Alliance']['Edit internal info page'] = 'Upravit interní stránku';
$Definition['Alliance']['Assign to position'] = 'Přidělit pozici';
$Definition['Alliance']['Link to the forum'] = 'Odkaz na fórum';
$Definition['Alliance']['Settings'] = 'Nastavení';
$Definition['Alliance']['Actions'] = 'Akce';
$Definition['Alliance']['Invite a player into the alliance'] = 'Pozvat hráče do aliance';
$Definition['Alliance']['Alliance diplomacy'] = 'Diplomacie aliance';
$Definition['Alliance']['Quit alliance'] = 'Opustit alianci';
$Definition['Alliance']['Kick player'] = 'Vyhodit hráče';
$Definition['Alliance']['Population'] = 'Obyvatelé';
$Definition['Alliance']['Details'] = 'Podrobnosti';
$Definition['Alliance']['Members'] = 'Členové';
$Definition['Alliance']['Position'] = 'Pozice';
$Definition['Alliance']['no thread created'] = 'Zatím nebylo vytvořeno žádné téma.';
$Definition['Alliance']['This survey ends on x'] = 'Tato anketa končí %s.';
$Definition['Alliance']['voting finished'] = 'hlasování skončilo.';
$Definition['Alliance']['move topic'] = 'přesunout téma';
$Definition['Alliance']['edit topic'] = 'upravit témata';
$Definition['Alliance']['x times edited, last edit by y'] = '%sx upraveno, naposledy %s dne %s.';
$Definition['Alliance']['Player ID'] = 'ID hráče';
$Definition['Alliance']['user_list_headline'] = 'Otevřít fórum těmto hráčům';
$Definition['Alliance']['answer(s)'] = 'odpověď(i)';
$Definition['Alliance']['Last post'] = 'Poslední příspěvek';
$Definition['Alliance']['Posts:'] = 'Příspěvky:';
$Definition['Alliance']['pop'] = 'obyv.';
$Definition['Alliance']['Villages'] = 'Vesnice';
$Definition['Alliance']['post reply'] = 'odpovědět';
$Definition['Alliance']['created'] = 'vytvořeno';
$Definition['Alliance']['author'] = 'autor';
$Definition['Alliance']['messages'] = 'zprávy';
$Definition['Alliance']['reply'] = 'odpovědět';
$Definition['Alliance']['vote'] = 'hlasovat';
$Definition['Alliance']['to result'] = 'k výsledku';
$Definition['Alliance']['to survey'] = 'k anketě';
$Definition['Alliance']['name'] = 'jméno';
$Definition['Alliance']['addLine'] = 'Přidat';
$Definition['Alliance']['New Thread'] = 'Nové téma';
$Definition['Alliance']['Post new thread'] = 'Založit nové téma';
$Definition['Alliance']['Thread'] = 'Téma';
$Definition['Alliance']['Report'] = 'Hlášení';
$Definition['Alliance']['Coordinates'] = 'Souřadnice';
$Definition['Alliance']['report'] = 'hlášení';
$Definition['Alliance']['Troops'] = 'Jednotky';
$Definition['Alliance']['Vote_options'] = 'Možnosti hlasování';
$Definition['Alliance']['Survey'] = 'Anketa';
$Definition['Alliance']['ends on'] = 'Končí';
$Definition['Alliance']['open_topic'] = 'otevřít téma';
$Definition['Alliance']['close_topic'] = 'Zavřít téma';
$Definition['Alliance']['stick_topic'] = 'připíchnout téma';
$Definition['Alliance']['unstick_topic'] = 'Odepnout téma';
$Definition['Alliance']['preview'] = 'náhled';
$Definition['Alliance']['underline'] = 'podtržení';
$Definition['Alliance']['Player'] = 'Hráč';
$Definition['Alliance']['Alliance ID'] = 'ID aliance';
$Definition['Alliance']['ally_list_headline'] = 'Otevřeno pro jiné aliance:';
$Definition['Alliance']['sitters_allowed'] = 'Zástupci mají přístup';
$Definition['Alliance']['open forum for the following alliances'] = 'Otevřít fórum těmto aliancím';
$Definition['Alliance']['edit forum'] = 'upravit fórum';
$Definition['Alliance']['create_in_area'] = 'vytvořit v oblasti';
$Definition['Alliance']['public_forum'] = 'Veřejné fórum';
$Definition['Alliance']['forum_name'] = 'Název fóra';
$Definition['Alliance']['new_forum'] = 'Nové fórum';
$Definition['Alliance']['desc'] = 'Popis';
$Definition['Alliance']['alliance_forum'] = 'Fórum aliance';
$Definition['Alliance']['conf_forum'] = 'Důvěrné fórum';
$Definition['Alliance']['closed_forum'] = 'Uzavřené fórum';
$Definition['Alliance']['Tag'] = 'Zkratka';
$Definition['Alliance']['Forum name'] = 'Název fóra';
$Definition['Alliance']['Threads'] = 'Témata';
$Definition['Alliance']['Last post'] = 'Poslední příspěvek';
$Definition['Alliance']['to the top'] = 'nahoru';
$Definition['Alliance']['to the bottom'] = 'dolů';
$Definition['Alliance']['Delete'] = 'Smazat';
$Definition['Alliance']['Confirm deletion?'] = 'Opravdu smazat?';
$Definition['Alliance']['show last post'] = 'zobrazit poslední příspěvek';
$Definition['Alliance']['edit'] = 'upravit';
$Definition['Alliance']['thread without new entries'] = 'Téma bez nových příspěvků';
$Definition['Alliance']['thread with new entries'] = 'Téma s novými příspěvky';
$Definition['Alliance']['noForum'] = 'Zatím nebylo vytvořeno žádné fórum.';
$Definition['Alliance']['switch admin'] = 'přepnout na správce';
$Definition['Alliance']['switch non admin'] = 'přepnout na běžný režim';
$Definition['Alliance']['set x as favor tab'] = 'Nastavit záložku %s jako oblíbenou.';
$Definition['Alliance']['This tab is set as favourite'] = 'Tato záložka je nastavena jako oblíbená';
$Definition['Alliance']['Overview'] = 'Přehled';
$Definition['Alliance']['NewForum'] = 'Nové fórum';
$Definition['Alliance']['Attacks'] = 'Útoky';
$Definition['Alliance']['Bonuses'] = 'Bonusy';
$Definition['Alliance']['Forum'] = 'Fórum';
$Definition['Alliance']['Options'] = 'Možnosti';
$Definition['Alliance']['Profile'] = 'Profil';
$Definition['Alliance']['Alliance'] = 'Aliance';
$Definition['Alliance']['no Alliance'] = 'Bez aliance';
$Definition['Alliance']['You are currently not in an alliance In order to join an alliance, you need a level 1 Embassy and an invitation'] = 'Momentálně nejsi v žádné alianci. Abys mohl vstoupit do aliance, potřebuješ ambasádu na úrovni 1 a pozvánku.';
//Artefacts
$Definition['Artefacts']['numbers'] = [
    1 => 'I',
    2 => 'II',
    3 => 'III',
    4 => 'IV',
    5 => 'V',
    6 => 'VI',
    7 => 'VII',
    8 => 'VIII',
    9 => 'IX',
    10 => 'X',
];
$Definition['Artefacts'][2] = [
    'names' => [
        1 => 'Malé tajemství architektů %s',
        2 => 'Velké tajemství architektů %s',
        3 => 'Jedinečné tajemství architektů',
    ],
    'desc' => 'Tento artefakt dává tvé vesnici dodatečnou ochranu proti katapultům a beranidlům. Budovy a hradby jsou ‎‭‭%s odolnější.',
];
$Definition['Artefacts'][4] = [
    'names' => [
        1 => 'Malé boty titánů %s',
        2 => 'Velké boty titánů %s',
        3 => 'Jedinečné boty titánů',
    ],
    'desc' => 'Tento artefakt zvyšuje rychlost tvých jednotek na %s‎ původní hodnoty.',
];
$Definition['Artefacts'][5] = [
    'names' => [
        1 => 'Malé orlí oči %s',
        2 => 'Velké orlí oči %s',
        3 => 'Jedinečné orlí oči',
    ],
    'desc' => 'Tento artefakt dělá tvé zvědy ‎‭‭%s&times;‬‎ silnějšími. Týká se všech zvědů ve vesnici i všech zvědů vyslaných z této vesnice na špehování. Navíc na shromaždišti uvidíš typ útočících jednotek, ale ne jejich počet.',
];
$Definition['Artefacts'][6] = [
    'names' => [
        1 => 'Malá kontrola stravy %s',
        2 => 'Velká kontrola stravy %s',
        3 => 'Jedinečná kontrola stravy',
    ],
    'desc' => 'Tento artefakt snižuje spotřebu obilí tvých i cizích jednotek ve tvé vesnici na ‎‭‭‭%s&times; původní hodnoty.',
];
$Definition['Artefacts'][8] = [
    'names' => [
        1 => 'Malý talent cvičitelů %s',
        2 => 'Velký talent cvičitelů %s',
        3 => 'Jedinečný talent cvičitelů',
    ],
    'desc' => 'Tento artefakt zkracuje dobu výcviku v paláci, rezidenci, dílně, stáji a kasárnách na %s&times; původní hodnoty.',
];
$Definition['Artefacts'][9] = [
    'names' => [
        1 => 'Malý plán skladování %s',
        2 => 'Velký plán skladování %s',
    ],
    'desc' => 'Tento stavební plán umožňuje stavět velké sýpky a velké sklady. Plán je potřeba i pro stavbu vyšších úrovní těchto budov.',
];
$Definition['Artefacts'][10] = [
    'names' => [
        1 => 'Malý zmatek soupeřů %s',
        2 => 'Velký zmatek soupeřů %s',
        3 => 'Jedinečný zmatek soupeřů',
    ],
    "desc" => nl2br("Tento artefakt zvyšuje kapacitu úkrytů %s&times;‬‎.

Další účinek: Katapulty mohou na vesnice pod vlivem artefaktu střílet jen náhodně. Výjimkou u vesnických a účtových artefaktů jsou div světa a pokladnice. U jedinečného artefaktu je výjimkou pouze div světa."),
];
$Definition['Artefacts'][11] = [
    'names' => [
        1 => 'Artefakt malého blázna',
        3 => 'Artefakt jedinečného blázna',
    ],//no desc for this one.
];
$Definition['Artefacts'][12] = [
    'name' => 'Stavební plán divu světa',
    'desc' => 'Stavební plán ti umožní postavit div světa v natarských vesnicích. Ke stavbě až do úrovně 49 stačí jeden plán. Pro vyšší úrovně je potřeba další plán, který drží člen aliance.',
    //no desc for this one.
];
//Auction
$Definition['Auction']['notEnoughGold'] = 'Nedostatek zlata.';
$Definition['Auction']['autoCorrect'] = 'Automaticky opraveno.';
$Definition['Auction']['disabledSubmitTooltip'] = 'Nebylo zadáno množství zlata.';
$Definition['Auction']['disabledSubmitTooltip2'] = 'Je potřeba alespoň 200 stříbra.';
$Definition['Auction']['enabledSubmitTooltip'] = 'směnit';
$Definition['Auction']['enabledSubmitTooltip2'] = 'Směnit stříbro za zlato';
$Definition['Auction']['maxAmountTooltip'] = 'Koupit zlato';
$Definition['Auction']['exchange'] = 'směnit';
$Definition['Auction']['Exchange'] = 'Směnit';
$Definition['Auction']['silverExchange'] = 'Směnárna';
$Definition['Auction']['sitterError'] = 'Jako zástupce sem nemáš přístup.';
$Definition['Auction']['deletionError'] = 'Sem nemáš přístup, protože tvůj účet se maže.';
$Definition['Auction']['yes'] = 'ano';
$Definition['Auction']['no'] = 'ne';
$Definition['Auction']['Confirm sale:'] = 'Potvrdit prodej:';
$Definition['Auction']['You can only have x auctions at a time'] = 'Najednou můžeš mít jen %s aukcí.';
$Definition['Auction']['10AdventuresError'] = 'Dokonči 10 dobrodružství a odemkni aukce!';
$Definition['Auction']['Finish 10 adventures to unlock the auctions!'] = 'Dokonči 10 dobrodružství a odemkni aukce!';
$Definition['Auction']['Choose an item to sell An auction can last up to x hours'] = 'Vyber předmět k prodeji. Aukce může trvat až %s hodin.';
$Definition['Auction']['Finished auctions'] = 'Skončené aukce';
$Definition['Auction']['AuctionNotFound'] = 'Aukce nenalezena!';
$Definition['Auction']['Really sell this item?'] = 'Opravdu prodat tento předmět?';
$Definition['Auction']['Sell [AMOUNT] units?'] = 'Prodat [AMOUNT] kusů?';
$Definition['Auction']['Not enough items available for auction ([MIN_AMOUNT] items)'] = 'Pro aukci není dost předmětů ([MIN_AMOUNT] kusů)';
$Definition['Auction']['Do you want to sell this horse for 100 silver?'] = 'Chceš prodat tohoto koně za 100 stříbra?';
$Definition['Auction']['You cannot sell your horse!'] = 'Svého koně nemůžeš prodat!';
$Definition['Auction']['AuctionFinished'] = 'Tato aukce skončila.';
$Definition['Auction']['notEnoughSilverAuctionError'] = 'Nemáš dost stříbra. Potřebuješ alespoň %s stříbra!';
$Definition['Auction']['min'] = 'min';
$Definition['Auction']['bidFor'] = 'Přihodit';
$Definition['Auction']['balanceSince'] = 'obchody za posledních 7 dní';
$Definition['Auction']['cause'] = 'Důvod';
$Definition['Auction']['date'] = 'Datum';
$Definition['Auction']['showAccounting'] = 'Zobrazit podrobnosti účetnictví.';
$Definition['Auction']['hideAccounting'] = 'Skrýt podrobnosti účetnictví.';
$Definition['Auction']['noBooking'] = 'Nic nenalezeno.';
$Definition['Auction']['Adventure'] = 'Dobrodružství';
$Definition['Auction']['Quest reward'] = 'Odměna za úkol';
$Definition['Auction']['sell x items of y'] = 'prodej %s kusů: %s';
$Definition['Auction']['buy x items of y'] = 'nákup %s kusů: %s';
$Definition['Auction']['currentBid'] = 'Aktuální nabídka';
$Definition['Auction']['currentBidder'] = 'Aktuální dražitel';
$Definition['Auction']['notEnoughSilver'] = 'Nedostatek stříbra';
$Definition['Auction']['desc'] = 'popis';
$Definition['Auction']['clock'] = 'čas';
$Definition['Auction']['bid'] = 'Aukce';
$Definition['Auction']['noAuction'] = 'Žádné aukce nenalezeny.';
$Definition['Auction']['Change'] = 'Změnit';
$Definition['Auction']['del'] = 'smazat';
$Definition['Auction']['select all'] = 'vybrat vše';
$Definition['Auction']['won'] = 'vyhráno';
$Definition['Auction']['outbid'] = 'přeplaceno';
$Definition['Auction']['reserve'] = 'Zaúčtování';
$Definition['Auction']['balance'] = 'Zůstatek';
$Definition['Auction']['running'] = 'probíhá';
$Definition['Auction']['x unit perItem'] = 'x za kus';
$Definition['Auction']['buy'] = 'koupit';
$Definition['Auction']['sell'] = 'prodat';
$Definition['Auction']['bids'] = 'nabídky';
$Definition['Auction']['doBid'] = 'nabídka';
$Definition['Auction']['accounting'] = 'účetnictví';
$Definition['Auction']['cancelled'] = 'zrušeno';
$Definition['Auction']['finished'] = 'skončeno';
$Definition['Auction']['filterFor'] = 'Filtrovat';
$Definition['Auction']['silver'] = 'stříbro';
$Definition['Auction']['helmet'] = 'helmy';
$Definition['Auction']['body'] = 'brnění';
$Definition['Auction']['leftHand'] = 'předměty do levé ruky';
$Definition['Auction']['rightHand'] = 'zbraně';
$Definition['Auction']['shoes'] = 'boty';
$Definition['Auction']['horse'] = 'koně';
$Definition['Auction']['cage'] = 'klece';
$Definition['Auction']['scroll'] = 'svitky';
$Definition['Auction']['ointment'] = 'masti';
$Definition['Auction']['bandage25'] = 'malé obvazy';
$Definition['Auction']['bandage%s'] = 'obvazy';
$Definition['Auction']['bucketOfWater'] = 'Vědra';
$Definition['Auction']['bookOfWisdom'] = 'Kniha moudrosti';
$Definition['Auction']['lawTables'] = 'Tabulky zákonů';
$Definition['Auction']['artWork'] = 'Umělecká díla';
//BBCode
$Definition['BBCode']['this player is registered at x'] = 'Tento hráč si zaregistroval účet v tomto herním světě %s';
$Definition['BBCode']['this player is under protection to x'] = 'Tento hráč je pod ochranou do %s';
$Definition['BBCode']['x has refused a confederacy to y'] = '%s odmítl konfederaci s %s.';
$Definition['BBCode']['x has refused nap to y'] = '%s odmítl pakt o neútočení s %s.';
$Definition['BBCode']['x has refused war to y'] = '%s odmítl válku s %s.';
$Definition['BBCode']['confederacies with'] = 'konfederace s';
$Definition['BBCode']['non-aggression pact(s) with'] = 'pakt(y) o neútočení s';
$Definition['BBCode']['at war(s) with'] = 've válce s';
$Definition['BBCode']['Forum'] = 'Fórum';
$Definition['BBCode']['News'] = 'Novinky';
$Definition['BBCode']['Strength of own alliance'] = 'Síla vlastní aliance';
$Definition['BBCode']['X kicked Y from Alliance'] = '%s vyhodil %s z aliance';
$Definition['BBCode']['Fighting points (difference to yesterday)'] = 'Bojové body (rozdíl oproti včerejšku)';
$Definition['BBCode']['troops destroyed by alliance Ally'] = 'jednotky zničené aliancí %s';
$Definition['BBCode']['resources stolen by alliance Ally'] = 'suroviny ukradené aliancí %s';
$Definition['BBCode']['troops destroyed of alliance Ally'] = 'zničené jednotky aliance %s';
$Definition['BBCode']['resources stolen of alliance Ally'] = 'ukradené suroviny aliance %s';
$Definition['BBCode']['This alliance cannot be found'] = 'Tato aliance nebyla nalezena';
$Definition['BBCode']['latest postings on forum'] = 'poslední příspěvky ve fóru';
$Definition['BBCode']['Alliance Events'] = 'Události aliance';
$Definition['BBCode']['X joined Alliance'] = '%s vstoupil do aliance.';
$Definition['BBCode']['X left Alliance'] = '%s opustil alianci';
$Definition['BBCode']['X created new village'] = '%s založil novou vesnici';
$Definition['BBCode']['X invited Y'] = '%s pozval %s do aliance. ';
$Definition['BBCode']['x has offered a confederacy to y'] = '%s nabídl konfederaci %s';
$Definition['BBCode']['x has offered war to y'] = '%s vyhlásil válku %s';
$Definition['BBCode']['x has offered nap to y'] = '%s nabídl pakt o neútočení %s';
$Definition['BBCode']['x has accepted a confederacy to y'] = '%s přijal konfederaci s';
$Definition['BBCode']['x has accepted nap to y'] = '%s přijal pakt o neútočení s';
$Definition['BBCode']['x has accepted war to y'] = '%s přijal válku s';
$Definition['BBCode']['Losses compared to alliance'] = 'Ztráty v porovnání s aliancí %s';
$Definition['BBCode']['attack'] = 'Útok';
$Definition['BBCode']['defense'] = 'Obrana';
//Buildings
$Definition['Buildings']['increase warehouse storage by level 20 storage value'] = 'zvýší kapacitu skladu o hodnotu skladu úrovně 20';
$Definition['Buildings']['increase granny storage by level 20 storage value'] = 'zvýší kapacitu sýpky o hodnotu sýpky úrovně 20';
$Definition['Buildings']['Alliance Founder'] = 'Zakladatel aliance';
$Definition['Buildings']['Alliance'] = 'Aliance';
$Definition['Buildings']['to the alliance'] = 'K alianci';
$Definition['Buildings']['Tag'] = 'Zkratka';
$Definition['Buildings']['Name'] = 'Jméno';
$Definition['Buildings']['FoundAlliance'] = 'Založit alianci';
$Definition['Buildings']['accept'] = 'přijmout';
$Definition['Buildings']['refuse'] = 'odmítnout';
$Definition['Buildings']['ww_change_name_desc'] = 'Název divu světa';
$Definition['Buildings']['allianceFull'] = 'Tato aliance má maximální počet členů.';
$Definition['Buildings']['Enter Tag'] = 'Zadej zkratku.';
$Definition['Buildings']['Tag exists'] = 'Zkratka už existuje.';
$Definition['Buildings']['Enter Name'] = 'Zadej jméno.';
$Definition['Buildings']['Join alliance'] = 'Vstoupit do aliance';
$Definition['Buildings']['There are no invitations available'] = 'Momentálně nemáš žádnou pozvánku.';
$Definition['Buildings']['Buildings'] = 'Budova';
$Definition['Buildings']['onOffLevelSwitch'] = 'Zobrazit/skrýt úrovně budov';
$Definition['Buildings']['maxMasterBuilderReached'] = 'Ve frontě stavitele můžeš mít jen %s stavebních zakázek.';
$Definition['Buildings']['enoughResourcesAt'] = 'Dostatek surovin %s';
$Definition['Buildings']['constructBuilding'] = 'Postavit budovu';
$Definition['Buildings']['upgradeBuilding'] = 'Rozšířit na úroveň %s';
$Definition['Buildings']['waitLoop'] = 'Vyčkávací smyčka';
$Definition['Buildings']['workersBusy'] = 'Stavitelé už pracují.';
$Definition['Buildings']['enoughResourcesAtNever'] = 'Tvá produkce obilí je záporná, takže nikdy nebudeš mít dostatek surovin.';
$Definition['Buildings']['(not possible)'] = '(není možné)';
$Definition['Buildings']['finishNow']['finishNow'] = 'dokončit stavbu okamžitě';
$Definition['Buildings']['mainBuilding']['Demolish building'] = 'Zbourat budovu';
$Definition['Buildings']['mainBuilding']['demolish_desc'] = 'Pokud budovu už nepotřebuješ, můžeš svým stavitelům nařídit, aby ji zbourali';
$Definition['Buildings']['mainBuilding']['demolish'] = 'zbourat';
$Definition['Buildings']['mainBuilding']['Demolish completely'] = 'Zbourat úplně';
$Definition['Buildings']['mainBuilding']['complete_demolish_title'] = 'Okamžitě zbourat vybranou budovu? Bude z vesnice odstraněna';
$Definition['Buildings']['buildingQueue']['buildingQueue'] = 'Stavební fronta';
$Definition['Buildings']['buildingQueue']['name'] = 'Travian PLUS';
$Definition['Buildings']['buildingQueue']['desc'] = 'S Travian PLUS můžeš zadat do fronty stavbu další budovy.';
$Definition['Buildings']['newBuilding']['Infrastructure'] = 'Infrastruktura';
$Definition['Buildings']['newBuilding']['Military'] = 'Vojenské';
$Definition['Buildings']['newBuilding']['Resources'] = 'Surovinové';
$Definition['Buildings']['Infrastructure'] = 'Infrastruktura';
$Definition['Buildings']['Military'] = 'Vojenské';
$Definition['Buildings']['resources'] = 'Surovinové';
$Definition['Buildings']['costsForUpgradeToLvl'] = '<b>Náklady</b> na rozšíření na úroveň %s';
$Definition['Buildings']['costs'] = 'Náklady';
$Definition['Buildings']['errors']['foodShortage'] = 'Nedostatek potravy.';
$Definition['Buildings']['errors']['upgradeWareHouse'] = 'Rozšiř sklad surovin.';
$Definition['Buildings']['errors']['upgradeGranny'] = 'Rozšiř sýpku.';
$Definition['Buildings']['errors']['constructWarehouse'] = 'Postav sklad surovin.';
$Definition['Buildings']['errors']['constructGranny'] = 'Postav sýpku.';
$Definition['Buildings']['errors']['noGreatArtefact'] = 'K rozšíření této budovy potřebuješ artefakt.';
$Definition['Buildings']['errors']['wwPlans'] = 'Divy světa lze stavět jen v jedné ze starých natarských vesnic. Potřebný je ale i stavební plán. Od úrovně 50 je k dokončení potřeba další plán, který musí vlastnit jiný hráč ze stejné aliance.';
$Definition['Buildings']['construct_with_master_builder'] = 'Postavit se stavbyvedoucím';
$Definition['Buildings']['constructNewBuilding'] = 'Postavit novou budovu';
$Definition['Buildings']['preRequests'] = 'Předpoklady';
$Definition['Buildings']['no_building_available'] = 'Momentálně nelze postavit žádnou budovu.<br />Většina budov má určité předpoklady. Více zjistíš v příručce.';
$Definition['Buildings']['soon_available'] = 'Budovy dostupné brzy';
$Definition['Buildings']['level'] = 'úroveň';
$Definition['Buildings']['upgradeNotices']['reachedMaxLvL'] = 'dosáhla maximální úrovně.';
$Definition['Buildings']['upgradeNotices']['buildingIsOnDemolition'] = 'Tato budova se bourá.';
$Definition['Buildings']['upgradeNotices']['upCostsToLevel'] = 'Náklady na rozšíření na úroveň';
$Definition['Buildings']['upgradeNotices']['currentlyUpgradingToLevel'] = 'Momentálně se rozšiřuje na úroveň %s';
$Definition['Buildings']['upgradeNotices']['currentlyReachingMaxLevel'] = '%s se nyní rozšiřuje na maximum.';
$Definition['Buildings']['masterBuilder']['masterBuilder'] = 'Stavitel';
$Definition['Buildings']['masterBuilder']['atStartOfConstruction'] = 'Na začátku stavby';
$Definition['Buildings']['buildingSites']['rallyPoint'] = 'Místo pro shromaždiště';
$Definition['Buildings']['buildingSites']['building'] = 'Stavební místo';
$Definition['Buildings']['buildingSites']['WorldWonder'] = 'Místo pro div světa';
$Definition['Buildings'][1]['title'] = 'Dřevorubec';
$Definition['Buildings'][1]['desc'] = 'Dřevorubec kácí stromy a vyrábí dřevo. Čím více dřevorubce rozšíříš, tím více dřeva vyrobí';
$Definition['Buildings'][1]['current_prod'] = 'Současná produkce';
$Definition['Buildings'][1]['next_prod'] = 'Produkce na úrovni';
$Definition['Buildings'][1]['unit'] = 'za hodinu';
$Definition['Buildings'][2]['title'] = 'Hliněný důl';
$Definition['Buildings'][2]['desc'] = 'Zde se těží hlína. Rozšířením zvýšíš produkci hlíny.';
$Definition['Buildings'][2]['current_prod'] = 'Současná produkce';
$Definition['Buildings'][2]['next_prod'] = 'Produkce na úrovni';
$Definition['Buildings'][2]['unit'] = 'za hodinu';
$Definition['Buildings'][3]['title'] = 'Železný důl';
$Definition['Buildings'][3]['desc'] = 'Zde horníci těží vzácné železo. Rozšířením dolu zvýšíš produkci železa.';
$Definition['Buildings'][3]['current_prod'] = 'Současná produkce';
$Definition['Buildings'][3]['next_prod'] = 'Produkce na úrovni';
$Definition['Buildings'][3]['unit'] = 'za hodinu';
$Definition['Buildings'][4]['title'] = 'Obilné pole';
$Definition['Buildings'][4]['desc'] = 'Zde se pěstuje potrava pro tvé obyvatele. Rozšířením pole zvýšíš produkci obilí.';
$Definition['Buildings'][4]['current_prod'] = 'Současná produkce';
$Definition['Buildings'][4]['next_prod'] = 'Produkce na úrovni';
$Definition['Buildings'][4]['unit'] = 'za hodinu';
$Definition['Buildings'][5]['title'] = 'Pila';
$Definition['Buildings'][5]['desc'] = 'Zde se zpracovává dřevo z dřevorubců. Podle úrovně může pila zvýšit produkci dřeva až o 25 procent.';
$Definition['Buildings'][5]['current_prod'] = 'Současné zvýšení produkce';
$Definition['Buildings'][5]['next_prod'] = 'Zvýšení na úrovni';
$Definition['Buildings'][5]['unit'] = 'procent';
$Definition['Buildings'][6]['title'] = 'Cihelna';
$Definition['Buildings'][6]['desc'] = 'Cihelna zpracovává hlínu na cihly. Podle úrovně může cihelna zvýšit produkci hlíny až o 25 procent.';
$Definition['Buildings'][6]['current_prod'] = 'Současné zvýšení produkce';
$Definition['Buildings'][6]['next_prod'] = 'Zvýšení na úrovni';
$Definition['Buildings'][6]['unit'] = 'procent';
$Definition['Buildings'][7]['title'] = 'Slévárna';
$Definition['Buildings'][7]['desc'] = 'Slévárna taví železo. Podle úrovně může slévárna zvýšit produkci železa až o 25 procent.';
$Definition['Buildings'][7]['current_prod'] = 'Současné zvýšení produkce';
$Definition['Buildings'][7]['next_prod'] = 'Zvýšení na úrovni';
$Definition['Buildings'][7]['unit'] = 'procent';
$Definition['Buildings'][8]['title'] = 'Mlýn';
$Definition['Buildings'][8]['desc'] = 'Zde se obilí mele na mouku. Podle úrovně může mlýn zvýšit produkci obilí až o 25 procent. ';
$Definition['Buildings'][8]['current_prod'] = 'Současné zvýšení produkce';
$Definition['Buildings'][8]['next_prod'] = 'Zvýšení na úrovni';
$Definition['Buildings'][8]['unit'] = 'procent';
$Definition['Buildings'][9]['title'] = 'Pekárna';
$Definition['Buildings'][9]['desc'] = 'Pekárna peče z mouky chléb. Spolu s mlýnem může produkce obilí vzrůst celkem až o 50 procent.';
$Definition['Buildings'][9]['current_prod'] = 'Současné zvýšení produkce';
$Definition['Buildings'][9]['next_prod'] = 'Zvýšení na úrovni';
$Definition['Buildings'][9]['unit'] = 'procent';
$Definition['Buildings'][10]['title'] = 'Sklad surovin';
$Definition['Buildings'][10]['desc'] = 'Ve skladu surovin se skladuje dřevo, hlína a železo. Rozšířením zvýšíš jeho kapacitu.';
$Definition['Buildings'][10]['current_prod'] = 'Současná kapacita';
$Definition['Buildings'][10]['next_prod'] = 'Kapacita na úrovni';
$Definition['Buildings'][10]['unit'] = 'jednotek surovin';
$Definition['Buildings'][11]['title'] = 'Sýpka';
$Definition['Buildings'][11]['desc'] = 'V sýpce se skladuje obilí z tvých polí. Rozšířením zvýšíš její kapacitu.';
$Definition['Buildings'][11]['current_prod'] = 'Současná kapacita';
$Definition['Buildings'][11]['next_prod'] = 'Kapacita na úrovni';
$Definition['Buildings'][11]['unit'] = 'jednotek surovin';
$Definition['Buildings'][13]['title'] = 'Kovárna';
$Definition['Buildings'][13]['desc'] = 'Kovárna vylepšuje zbraně a zbroj tvých jednotek. Čím vyšší úroveň, tím lepší zbraně a zbroj můžeš nechat vyrobit. ';
$Definition['Buildings'][14]['title'] = 'Turnajové hřiště';
$Definition['Buildings'][14]['desc'] = 'Na turnajovém hřišti mohou tvé jednotky zvýšit svou výdrž. Čím více je budova rozšířena, tím rychlejší jsou tvé jednotky na vzdálenost nad 20 polí.';
$Definition['Buildings'][14]['current_prod'] = 'Současné zvýšení rychlosti';
$Definition['Buildings'][14]['next_prod'] = 'zvýšení na úrovni';
$Definition['Buildings'][14]['unit'] = 'procent';
$Definition['Buildings'][15]['title'] = 'Hlavní budova';
$Definition['Buildings'][15]['desc'] = 'V hlavní budově sídlí stavitelé vesnice. Čím vyšší úroveň, tím rychleji stavitelé dokončují nové budovy. ';
$Definition['Buildings'][15]['current_prod'] = 'Současná doba stavby';
$Definition['Buildings'][15]['next_prod'] = 'Doba stavby na úrovni';
$Definition['Buildings'][15]['unit'] = 'procent';
$Definition['Buildings'][16]['title'] = 'Shromaždiště';
$Definition['Buildings'][16]['desc'] = 'Na shromaždišti se setkává tvoje armáda. Odtud ji můžeš posílat dobývat, útočit, loupit nebo posilovat jiné vesnice.';
$Definition['Buildings'][17]['title'] = 'Tržiště';
$Definition['Buildings'][17]['desc'] = 'Na tržišti můžeš obchodovat s ostatními hráči. Čím vyšší úroveň, tím více surovin lze přepravovat najednou. ';
$Definition['Buildings'][18]['title'] = 'Ambasáda';
$Definition['Buildings'][18]['desc'] = 'Ambasáda je místem pro diplomaty. Čím vyšší úroveň, tím více možností král získá.';
$Definition['Buildings'][19]['title'] = 'Kasárny';
$Definition['Buildings'][19]['desc'] = 'V kasárnách se cvičí pěchota. Čím vyšší úroveň, tím rychleji se jednotky cvičí.';
$Definition['Buildings'][20]['title'] = 'Stáj';
$Definition['Buildings'][20]['desc'] = 'Ve stáji se cvičí jízda. Čím vyšší úroveň, tím rychleji se jednotky cvičí. ';
$Definition['Buildings'][21]['title'] = 'Dílna';
$Definition['Buildings'][21]['desc'] = 'V dílně se staví obléhací stroje, jako jsou katapulty a beranidla. Čím vyšší úroveň, tím rychleji se tyto jednotky vyrábějí.';
$Definition['Buildings'][22]['title'] = 'Akademie';
$Definition['Buildings'][22]['desc'] = 'V akademii se vyzkoumávají nové typy jednotek. Rozšířením můžeš vyzkoumat lepší jednotky. <br><br> Jakmile jednotky v akademii vyzkoumáš, můžeš je v této vesnici cvičit. ';
$Definition['Buildings'][23]['title'] = 'Úkryt';
$Definition['Buildings'][23]['desc'] = 'Úkryt slouží ke schování části surovin při napadení vesnice. Tyto suroviny nemohou být ukradeny. ';
$Definition['Buildings'][23]['current_prod'] = 'Současná kapacita';
$Definition['Buildings'][23]['next_prod'] = 'Kapacita na úrovni';
$Definition['Buildings'][23]['overall_storage'] = 'Celková kapacita';
$Definition['Buildings'][23]['unit'] = 'jednotka surovin';
$Definition['Buildings'][24]['title'] = 'Radnice';
$Definition['Buildings'][24]['desc'] = 'Na radnici můžeš pořádat okázalé slavnosti. Slavnost zvyšuje tvé kulturní body.';
$Definition['Buildings'][25]['title'] = 'Rezidence';
$Definition['Buildings'][25]['desc'] = 'Rezidence je malý palác, ve kterém bydlí král nebo královna, když navštíví vesnici. Rezidence chrání vesnici před nepřáteli, kteří ji chtějí dobýt.';
$Definition['Buildings'][26]['title'] = 'Palác';
$Definition['Buildings'][26]['desc'] = 'V paláci bydlí král nebo královna říše. V celé říši může být najednou jen jeden palác. Palác potřebuješ k tomu, abys mohl vesnici prohlásit za hlavní město.';
$Definition['Buildings'][27]['title'] = 'Pokladnice';
$Definition['Buildings'][27]['desc'] = 'V pokladnici se uchovávají poklady tvé říše. Do pokladnice se vejde jen jeden poklad. Po ukořistění artefaktu trvá na normálním serveru 24 hodin a na 3x rychlém serveru 12 hodin, než začne artefakt působit. ';
$Definition['Buildings'][28]['title'] = 'Obchodní kancelář';
$Definition['Buildings'][28]['desc'] = 'V obchodní kanceláři se vylepšují vozy obchodníků a zapřahají se do nich silní koně. Čím vyšší úroveň, tím více tvoji obchodníci unesou. ';
$Definition['Buildings'][28]['current_prod'] = 'Zvýšení v procentech';
$Definition['Buildings'][28]['next_prod'] = 'Zvýšení v procentech na úrovni';
$Definition['Buildings'][28]['unit'] = 'Procent';
$Definition['Buildings'][29]['title'] = 'Velké kasárny';
$Definition['Buildings'][29]['desc'] = 'Velké kasárny umožňují cvičit více jednotek najednou, ty ale stojí trojnásobek původní ceny. ';
$Definition['Buildings'][30]['title'] = 'Velká stáj';
$Definition['Buildings'][30]['desc'] = 'Velká stáj umožňuje cvičit více jednotek najednou, ty ale stojí trojnásobek původní ceny.';
$Definition['Buildings'][31]['title'] = 'Městská zeď';
$Definition['Buildings'][31]['desc'] = 'Stavbou městské zdi můžeš svou vesnici chránit před barbarskými hordami nepřátel. Čím vyšší úroveň, tím vyšší bonus k obraně tvých vojsk. ';
$Definition['Buildings'][31]['current_prod'] = 'Současný bonus obrany';
$Definition['Buildings'][31]['next_prod'] = 'Bonus obrany na úrovni';
$Definition['Buildings'][31]['unit'] = 'procent';
$Definition['Buildings'][32]['title'] = 'Zemní val';
$Definition['Buildings'][32]['desc'] = 'Stavbou zemního valu můžeš svou vesnici chránit před barbarskými hordami nepřátel. Čím vyšší úroveň, tím vyšší bonus k obraně tvých vojsk.';
$Definition['Buildings'][32]['current_prod'] = 'Současný bonus obrany';
$Definition['Buildings'][32]['next_prod'] = 'Bonus obrany na úrovni';
$Definition['Buildings'][32]['unit'] = 'procent';
$Definition['Buildings'][33]['title'] = 'Palisáda';
$Definition['Buildings'][33]['desc'] = 'Stavbou palisády můžeš svou vesnici chránit před barbarskými hordami nepřátel. Čím vyšší úroveň, tím vyšší bonus k obraně tvých vojsk. ';
$Definition['Buildings'][33]['current_prod'] = 'Současný bonus obrany';
$Definition['Buildings'][33]['next_prod'] = 'Bonus obrany na úrovni';
$Definition['Buildings'][33]['unit'] = 'procent';
$Definition['Buildings'][34]['title'] = 'Kameník';
$Definition['Buildings'][34]['desc'] = 'Kameník je expert v zacházení s kamenem. Čím vyšší úroveň, tím stabilnější jsou budovy ve vesnici. Bonus platí pro všechny budovy včetně surovinových polí a hradeb.';
$Definition['Buildings'][34]['current_prod'] = 'Současná stabilita';
$Definition['Buildings'][34]['next_prod'] = 'Stabilita budov na úrovni';
$Definition['Buildings'][34]['unit'] = 'Procent';
$Definition['Buildings'][35]['title'] = 'Pivovar';
$Definition['Buildings'][35]['desc'] = 'V pivovaru se vaří chutná medovina, kterou pak vojáci pijí při oslavách. <br><br> Tyto nápoje dělají tvé vojáky při útoku statečnějšími a silnějšími (1 % za úroveň). Bohužel se snižuje přesvědčovací schopnost náčelníků a katapulty mohou střílet jen náhodně. <br><br> Mohou jej stavět jen Germáni a jen v hlavním městě. Působí na celou říši.';
$Definition['Buildings'][36]['title'] = 'Pastičkář';
$Definition['Buildings'][36]['desc'] = 'Pastičkář chrání vesnici dobře ukrytými pastmi. Neopatrní nepřátelé tak mohou být uvězněni a vesnici už neublíží. ';
$Definition['Buildings'][36]['current_prod'] = 'Současný počet pastí';
$Definition['Buildings'][36]['next_prod'] = 'Počet pastí na úrovni';
$Definition['Buildings'][36]['unit'] = 'Pasti';
$Definition['Buildings'][36]['overall_storage'] = 'Celkový počet pastí';
$Definition['Buildings'][37]['title'] = 'Hrdinský dvůr';
$Definition['Buildings'][37]['desc'] = 'V hrdinském dvoře získáš přehled o okolních oázách. Od úrovně 10 můžeš hrdinou obsazovat oázy a zvýšit tak produkci surovin své vesnice. ';
$Definition['Buildings'][38]['title'] = 'Velký sklad surovin';
$Definition['Buildings'][38]['desc'] = 'Velký sklad surovin má trojnásobnou kapacitu běžného skladu. <br><br> Tuto budovu lze postavit jen ve vesnicích s divem světa nebo se speciálním natarským artefaktem.';
$Definition['Buildings'][38]['current_prod'] = 'Současná kapacita';
$Definition['Buildings'][38]['next_prod'] = 'Kapacita na úrovni';
$Definition['Buildings'][38]['unit'] = 'jednotek surovin';
$Definition['Buildings'][39]['title'] = 'Velká sýpka';
$Definition['Buildings'][39]['desc'] = 'Velká sýpka má trojnásobnou kapacitu běžné sýpky. <br><BR> Tuto budovu lze postavit jen ve vesnicích s divem světa nebo se speciálním natarským artefaktem.';
$Definition['Buildings'][39]['current_prod'] = 'Současná kapacita';
$Definition['Buildings'][39]['next_prod'] = 'Kapacita na úrovni';
$Definition['Buildings'][39]['unit'] = 'jednotek surovin';
$Definition['Buildings'][40]['title'] = 'Div světa';
$Definition['Buildings'][40]['desc'] = 'Div světa představuje vrchol veškeré architektury. Jen ti nejmocnější a nejbohatší dokážou takové mistrovské dílo postavit a ubránit ho před závistivými nepřáteli. <br><br>Divy světa lze stavět jen v jedné ze starých natarských vesnic. Potřebný je ale i stavební plán. Od úrovně 50 je k dokončení potřeba další plán, který musí vlastnit jiný hráč ze stejné aliance.';
$Definition['Buildings'][41]['title'] = 'Koňské napajedlo';
$Definition['Buildings'][41]['desc'] = 'Koňské napajedlo pečuje o pohodu tvých koní, a tím zrychluje jejich výcvik.  <br><br> Koňské napajedlo snižuje spotřebu obilí o jedno u těchto jednotek: Equites Legati od úrovně 10, Equites Imperatoris od úrovně 15 a Equites Caesaris od úrovně 20. <br ><BR> Koňské napajedlo mohou stavět jen Římané.';
$Definition['Buildings'][41]['current_prod'] = 'Současná doba výcviku';
$Definition['Buildings'][41]['next_prod'] = 'Doba výcviku na úrovni';
$Definition['Buildings'][41]['unit'] = 'Procent';

$Definition['Buildings'][42]['title'] = 'Kamenná zeď';
$Definition['Buildings'][42]['desc'] = 'Stavbou kamenné zdi můžeš svou vesnici chránit před barbarskými hordami nepřátel. Čím vyšší úroveň, tím vyšší bonus k obraně tvých vojsk. <br>Kamennou zeď mohou stavět jen Egypťané, bonus obrany má jako galská palisáda a odolnost jako germánský zemní val.';
$Definition['Buildings'][42]['current_prod'] = 'Současný bonus obrany';
$Definition['Buildings'][42]['next_prod'] = 'Bonus obrany na úrovni';
$Definition['Buildings'][42]['unit'] = 'procent';

$Definition['Buildings'][43]['title'] = 'Provizorní zeď';
$Definition['Buildings'][43]['desc'] = 'Stavbou provizorní zdi můžeš svou vesnici chránit před barbarskými hordami nepřátel. Čím vyšší úroveň, tím vyšší bonus k obraně tvých vojsk. <br/>Provizorní zeď mohou stavět jen Hunové, bonus obrany má jako germánský zemní val a odolnost jako římská městská zeď.';
$Definition['Buildings'][43]['current_prod'] = 'Současný bonus obrany';
$Definition['Buildings'][43]['next_prod'] = 'Bonus obrany na úrovni';
$Definition['Buildings'][43]['unit'] = 'procent';

$Definition['Buildings'][44]['title'] = 'Velitelství';
$Definition['Buildings'][44]['desc'] = 'Velitelství chrání vesnici před dobytím nepřítelem. V každé vesnici můžeš postavit jedno velitelství. Lze v něm cvičit osadníky a senátory/náčelníky/stařešiny/nomarchy/logady.';

$Definition['Buildings'][45]['title'] = 'Vodárna';
$Definition['Buildings'][45]['desc'] = 'Vodárnou řídíš přívod vody do svých oáz. To pomáhá nejen růstu stromů a obilí, ale i lomům a dolům, které zásobuje vodou pro dělníky a pro přepravu surovin. <br />Tato budova zvyšuje bonus všech připojených oáz. Na úrovni 20 účinek oáz zdvojnásobí. <br />Vodárnu mohou stavět jen Egypťané.';
$Definition['Buildings'][45]['current_prod'] = 'Současné zvýšení produkce';
$Definition['Buildings'][45]['next_prod'] = 'Zvýšení na úrovni';
$Definition['Buildings'][45]['unit'] = 'procent';

//Combat
$Definition['combat'] = [
    "simulate" => "Simulovat",
    "attacker" => "Útočník",
    "defender" => "Obránce",
    "number" => "Počet",
    "unit_level" => "Úroveň",
    "other" => "Ostatní",
    "Population" => "Obyvatelé",
    "catapult_target_level" => "Cíl katapultů",
    "hero_off_bonus" => "Hrdina (bonus útoku)",
    "hero_power" => "Hrdina (útočná síla)",
    "Palace_Resident" => "Rezidence/palác",
    "loyaltyReducedBy" => "Loajalita snížena o",
    "DamageByCatapult" => "Poškození katapulty",
    "DamageByRam" => "Poškození beranidly",
    "from" => "Od",
    "to" => "Do",
    "normal" => "Normální",
    "raid" => "Loupež",
    "attack_type" => "Typ útoku",
    "troops" => "Jednotky",
    "casualties" => "Ztráty",
    "attack_settings" => "Nastavení útoku",
    "attack_types" => [
        1 => "Špehovat",
        2 => "Normální",
        3 => "Loupež",
    ],
];
//cropFinder
$Definition['cropFinder']['title'] = 'Hledač obilí';
$Definition['cropFinder']['Start position:'] = 'Výchozí pozice:';
$Definition['cropFinder']['cropper'] = 'obilné vesnice';
$Definition['cropFinder']['both'] = 'obojí';
$Definition['cropFinder']['Type'] = 'Typ';
$Definition['cropFinder']['any'] = 'libovolný';
$Definition['cropFinder']['Oasis crop bonus (at least)'] = 'Bonus obilí z oáz (alespoň)';
$Definition['cropFinder']['only show unoccupied'] = 'Zobrazit jen neobsazené';
$Definition['cropFinder']['search'] = 'Hledat';
$Definition['cropFinder']['Croplands'] = 'Obilná pole';
$Definition['cropFinder']['distance'] = 'vzdálenost';
$Definition['cropFinder']['Position'] = 'Pozice';
$Definition['cropFinder']['Oasis'] = 'Oáza';
$Definition['cropFinder']['Occupied by'] = 'Obsazeno';
$Definition['cropFinder']['Alliance'] = 'Aliance';
$Definition['cropFinder']['noRows'] = 'S vybraným nastavením nebyla nalezena žádná obilná pole.';
//DailyQuest
$Definition['DailyQuest']['You`ve reached max voting limit Try again later'] = 'Dosáhl jsi denního limitu hlasování. Zkus to prosím později.';
$Definition['DailyQuest']['Daily Quests'] = 'Denní úkoly ';
$Definition['DailyQuest']['Collect daily rewards'] = 'Vyzvednout denní odměny';
$Definition['DailyQuest']['Click for details'] = 'Klikni pro podrobnosti';
$Definition['DailyQuest']['points'] = 'Body ';
$Definition['DailyQuest']['Collect reward'] = 'Vyzvednout odměnu';
$Definition['DailyQuest']['Overview'] = 'Přehled';
$Definition['DailyQuest']['This account is banned'] = 'Tento účet je zablokovaný.';
$Definition['DailyQuest']['Congratulations! You have collected enough points to get a reward!'] = 'Gratulujeme! Nasbíral jsi dost bodů na odměnu!';
$Definition['DailyQuest']['For collecting x points today, you receive the following reward'] = 'Za dnešních %s bodů dostaneš tuto odměnu';
$Definition['DailyQuest']['For collecting x points today, you can now collect your reward'] = 'Za dnešních %s bodů si teď můžeš vyzvednout odměnu';
$Definition['DailyQuest']['Your daily reward is determined randomly from these options'] = 'Tvá denní odměna se náhodně vybere z těchto možností';
$Definition['DailyQuest']['reward_25%_desc'] = 'Za 25 bodů dostaneš jednu z těchto odměn:
<br />
 <ul>
<li>+' . calculate_dailyquest_bonus(200, 'res') . ' surovin od každého druhu</li>
<li>+' . calculate_dailyquest_bonus(50, 'exp') . ' zkušeností hrdiny</li>
<li>+' . calculate_dailyquest_bonus(50, 'cp') . ' kulturních bodů</li>
<li>+' . calculate_dailyquest_bonus(1000, 'res') . ' surovin jednoho náhodného druhu</li>
</ul>';
$Definition['DailyQuest']['you collected x points today!'] = 'dnes jsi nasbíral %s bodů!';
$Definition['DailyQuest']['Your Reward:'] = 'Tvá odměna:';
$Definition['DailyQuest']['reward_25%_rows'][] = '<li> +' . calculate_dailyquest_bonus(200, 'res') . ' surovin od každého druhu</li>';
$Definition['DailyQuest']['reward_25%_rows'][] = '<li> +' . calculate_dailyquest_bonus(50, 'exp') . ' zkušeností hrdiny</li>';
$Definition['DailyQuest']['reward_25%_rows'][] = '<li>+' . calculate_dailyquest_bonus(50, 'cp') . ' kulturních bodů</li>';
$Definition['DailyQuest']['reward_25%_rows'][] = '<li>+' . calculate_dailyquest_bonus(1000, 'res') . ' surovin jednoho náhodného druhu</li>';
$Definition['DailyQuest']['x points achieved'] = 'Dosaženo %s bodů.';
$Definition['DailyQuest']['reward_50%_desc'] = 'Za 50 bodů dostaneš jednu z těchto odměn:
<br />
 <ul>
<li> +' . calculate_dailyquest_bonus(86400, 'plus') . ' PLUS účtu</li>
<li> +' . calculate_dailyquest_bonus(86400, 'productionBoost') . ' +25 % produkce dřeva</li>
<li>+' . calculate_dailyquest_bonus(86400, 'productionBoost') . ' +25 % produkce hlíny</li>
<li>+' . calculate_dailyquest_bonus(86400, 'productionBoost') . ' +25 % produkce železa</li>
<li>+' . calculate_dailyquest_bonus(86400, 'productionBoost') . ' +25 % produkce obilí</li>
</ul>';
$Definition['DailyQuest']['reward_50%_rows'][] = '<li> +' . calculate_dailyquest_bonus(86400, 'plus') . ' PLUS účtu</li>';
$Definition['DailyQuest']['reward_50%_rows'][] = '<li> +' . calculate_dailyquest_bonus(86400, 'productionBoost') . ' +25 % produkce dřeva</li>';
$Definition['DailyQuest']['reward_50%_rows'][] = '<li> +' . calculate_dailyquest_bonus(86400, 'productionBoost') . ' +25 % produkce hlíny</li>';
$Definition['DailyQuest']['reward_50%_rows'][] = '<li> +' . calculate_dailyquest_bonus(86400, 'productionBoost') . ' +25 % produkce železa</li>';
$Definition['DailyQuest']['reward_50%_rows'][] = '<li> +' . calculate_dailyquest_bonus(86400, 'productionBoost') . ' +25 % produkce obilí</li>';
$Definition['DailyQuest']['reward_75%_desc'] = 'Za 75 bodů dostaneš jednu z těchto odměn:
<br />
 <ul>
<li>+' . calculate_dailyquest_bonus(5, 'item') . ' mastí</li>
<li>+' . calculate_dailyquest_bonus(5, 'item') . ' tabulek zákonů</li>
<li>+' . calculate_dailyquest_bonus(5, 'item') . ' malých obvazů </li>
<li>+' . calculate_dailyquest_bonus(5, 'item') . ' klecí</li>
<li>+' . calculate_dailyquest_bonus(1, 'adv') . ' dobrodružství navíc</li>
</ul>';
$Definition['DailyQuest']['reward_75%_rows'][] = '<li>+' . calculate_dailyquest_bonus(5, 'item') . ' mastí</li>';
$Definition['DailyQuest']['reward_75%_rows'][] = '<li>+' . calculate_dailyquest_bonus(5, 'item') . ' tabulek zákonů</li>';
$Definition['DailyQuest']['reward_75%_rows'][] = '<li>+' . calculate_dailyquest_bonus(5, 'item') . ' malých obvazů </li>';
$Definition['DailyQuest']['reward_75%_rows'][] = '<li>+' . calculate_dailyquest_bonus(5, 'item') . ' klecí</li>';
$Definition['DailyQuest']['reward_75%_rows'][] = '<li>+' . calculate_dailyquest_bonus(1, 'adv') . ' dobrodružství navíc</li>';
$Definition['DailyQuest']['reward_100%_desc'] = 'Za 100 bodů dostaneš jednu z těchto odměn:
<br />
 <ul>
<li>+' . calculate_dailyquest_bonus(400, 'cp') . ' kulturních bodů</li>
<li>+' . calculate_dailyquest_bonus(20000, 'res') . ' surovin jednoho náhodného druhu</li>
<li>+' . calculate_dailyquest_bonus(400, 'exp') . ' zkušeností hrdiny</li>
<li>+' . calculate_dailyquest_bonus(4000, 'res') . ' surovin od každého druhu</li>
</ul>';
$Definition['DailyQuest']['reward_100%_rows'][] = '<li>+' . calculate_dailyquest_bonus(400, 'cp') . ' kulturních bodů</li>';
$Definition['DailyQuest']['reward_100%_rows'][] = '<li>+' . calculate_dailyquest_bonus(20000, 'res') . ' surovin jednoho náhodného druhu</li>';
$Definition['DailyQuest']['reward_100%_rows'][] = '<li>+' . calculate_dailyquest_bonus(400, 'exp') . ' zkušeností hrdiny</li>';
$Definition['DailyQuest']['reward_100%_rows'][] = '<li>+' . calculate_dailyquest_bonus(4000, 'res') . ' surovin od každého druhu</li>';
$Definition['DailyQuest']['Receive these free rewards every day!'] = 'Získávej tyto odměny zdarma každý den!';
$Definition['DailyQuest']['nextResetDesc'] = 'Další obnovení v %s hodin. Nezapomeň si do té doby vyzvednout odměnu!';
$Definition['DailyQuest']['Quest is complete for today'] = 'Úkol je pro dnešek splněn';
$Definition['DailyQuest']['Quest is still open'] = 'Úkol je stále otevřený.';
$Definition['DailyQuest']['Difficulty'] = 'Obtížnost';
$Definition['DailyQuest']['Requirement'] = 'Požadavek';
$Definition['DailyQuest']['Overview'] = 'Přehled';
$Definition['DailyQuest']['This quest is worth + x points'] = 'Tento úkol má hodnotu + %s bodů';
$Definition['DailyQuest']['Points granted for this quest: + x / y'] = 'Body získané za tento úkol: + %s / %s';
$Definition['DailyQuest']['The points for this quest can be achieved x times per day'] = 'Body za tento úkol lze získat %s× denně';
$Definition['DailyQuest']['Difficulties'] = [
    "challenging" => 'náročné',
    "hard" => 'těžké',
    "moderate" => 'střední',
];
$Definition['DailyQuest']['questData'] = [
    1 => [
        'name' => 'Dokonči dobrodružství',
        'desc' => 'Pošli hrdinu na dobrodružství. Úkol je splněn, jakmile hrdina dorazí, i když dobrodružství nepřežije.
Hrdinu pošleš na dobrodružství kliknutím na ikonu na obrázku.',
        'difficulty' => 'střední',
        'Requirement' => 'Dostupné dobrodružství',
    ],
    2 => [
        'name' => 'Vyloupi neobsazenou oázu',
        'desc' => 'Pošli jednotky vyloupit neobsazenou oázu. Úkol je splněn, jakmile tvé vojsko dorazí, i když v boji padne. Použití klecí k vyhnutí se boji ti za tento úkol body nepřinese.
Výsledek loupeže si můžeš spočítat v simulátoru boje, který najdeš na shromaždišti.',
        'difficulty' => 'těžké',
        'Requirement' => '(Hodně) jednotek',
    ],
    3 => [
        'name' => 'Vyloupi/zaútoč na natarskou vesnici',
        'desc' => 'Pošli jednotky vyloupit nebo napadnout natarskou vesnici. Úkol je splněn, jakmile tvé vojsko dorazí, i když v boji padne.
Na vesnici s divem světa ovládanou Natary bys neměl útočit dřív, než shromáždíš alespoň 100 000 jednotek.
<br />',
        'difficulty' => 'náročné',
        'Requirement' => '(Hodně) jednotek',
    ],
    4 => [
        'name' => 'Vyhraj aukci',
        'desc' => 'Účast v aukci ti přinese dvojí výhru – poprvé, když vyhraješ aukci a získáš předmět, a podruhé, když získáš body do denních odměn.
Body dostaneš, jakmile vyhraješ jakoukoli aukci, do které jsi přihodil.
<br />',
        'difficulty' => 'náročné',
        'Requirement' => 'Dokonči 10 dobrodružství',
    ],
    5 => [
        'name' => 'Získej nebo utrať zlato',
        'desc' => 'Body za tento úkol získáš, když zlato získáš nebo utratíš. Je jen na tobě, jak a k čemu jakékoli množství zlata získáš nebo použiješ.
<br />',
        'difficulty' => 'střední',
        'Requirement' => 'Žádný',
    ],
    6 => [
        'name' => 'Rozšiř budovu',
        'desc' => 'Body za tento úkol získáš, když rozšíříš existující budovu nebo postavíš novou.
Body dostaneš po dokončení stavby.
<br />',
        'difficulty' => 'střední',
        'Requirement' => 'Surovinové',
    ],
    7 => [
        'name' => 'Rozšiř surovinové pole',
        'desc' => 'Body za tento úkol získáš, když rozšíříš existující surovinové pole nebo postavíš nové.
Body dostaneš po dokončení stavby.
<br />',
        'difficulty' => 'střední',
        'Requirement' => 'Surovinové',
    ],
    8 => [
        'name' => 'Vycvič najednou 20 pěšáků jednoho typu',
        'desc' => 'Body za tento úkol získáš, když v kasárnách najednou zadáš výcvik 20 pěšáků.
Pěšáci, kteří už jsou ve frontě výcviku, se do úkolu nepočítají.
<br />',
        'difficulty' => 'náročné',
        'Requirement' => 'Kasárny',
    ],
    9 => [
        'name' => 'Vycvič najednou 20 jezdců jednoho typu',
        'desc' => 'Body za tento úkol získáš, když ve stáji najednou zadáš výcvik 20 jezdců.
Jezdci, kteří už jsou ve frontě výcviku, se do úkolu nepočítají.
<br />',
        'difficulty' => 'náročné',
        'Requirement' => 'Stáj',
    ],
    10 => [
        'name' => 'Uspořádej malou nebo velkou slavnost ',
        'desc' => '
Uspořádej malou nebo velkou slavnost
Uspořádej na radnici jednu malou nebo velkou slavnost.
<br/>
Body dostaneš, jakmile uspořádáš jakoukoli slavnost. Už probíhající slavnosti se nepočítají.
<br/>',
        'difficulty' => 'těžké',
        'Requirement' => '3 radnice',
    ],
    11 => [
        'name' => 'Přispěj %s surovinami do bonusu aliance',
        'desc' => 'Na podporu aliance můžeš přispívat surovinami do bonusu aliance. Jakmile aliance nasbírá dost příspěvků na odemčení nebo zvýšení bonusu, má z jeho účinku prospěch celá aliance. Úkol je splněn, jakmile přispěješ do jakéhokoli bonusu aliance.',
        'difficulty' => 'náročné',
        'Requirement' => 'Členství v alianci',
    ],
];
$Definition['DailyQuest']['VotingSystemTitle'] = 'Získej zlato zdarma!';
$Definition['DailyQuest']['Vote'] = 'Hlasovat';
$Definition['DailyQuest']['VoteRewardDesc'] = 'Za každý hlas dostaneš +10 <img class="gold" src="img/x.gif">.';
$Definition['DailyQuest']['Earn free gold!!!'] = 'Získej zlato zdarma!!!';
$Definition['DailyQuest']['Click on Consultant to open Hints page'] = 'Klikni na rádce a otevři stránku s tipy';
$Definition['DailyQuest']['if you abuse the vote system you will be banned'] = 'Pokud budeš hlasování zneužívat, budeš zablokován.';
$Definition['DailyQuest']['voteQuestDescription'] = 'Hlasováním můžeš získat zlato zdarma. <br> Pro hlasování klikni na obrázek níže';
//Dorf1
$Definition['Dorf1']['units'] = 'Jednotky';
$Definition['Dorf1']['none'] = 'žádné';
$Definition['Dorf1']['production']['production per hour'] = 'Produkce za hodinu';
$Definition['Dorf1']['production']['resources'][1] = 'Dřevo';
$Definition['Dorf1']['production']['resources'][2] = 'Hlína';
$Definition['Dorf1']['production']['resources'][3] = 'Železo';
$Definition['Dorf1']['production']['resources'][4] = 'Obilí';
$Definition['Dorf1']['production']['productionBoostButton'] = 'Více informací v bonusu produkce.';
$Definition['Dorf1']['movements']['incoming'] = 'Přicházející jednotky';
$Definition['Dorf1']['movements']['outgoing'] = 'Odcházející jednotky';
$Definition['Dorf1']['movements']['hour'] = 'hod.';
$Definition['Dorf1']['movements']['in'] = 'za ';
$Definition['Dorf1']['movements']['incomingAttacksToOases'] = 'Útoky na mé oázy';
$Definition['Dorf1']['movements']['incomingAttacksToMe'] = 'Útoky na mě';
$Definition['Dorf1']['movements']['reinforcement'] = 'Pos.';
$Definition['Dorf1']['movements']['incomingReinforcements'] = 'Přicházející posily';
$Definition['Dorf1']['movements']['incomingReinforcementsToMyOases'] = 'Posily do mých oáz';
$Definition['Dorf1']['movements']['outGoingAttacks'] = 'Vlastní útoky';
$Definition['Dorf1']['movements']['attack'] = 'Útok(y)';
$Definition['Dorf1']['movements']['outGoingReinforcements'] = 'Vlastní posily';
$Definition['Dorf1']['movements']['adventure'] = 'Dobrodružství';
$Definition['Dorf1']['movements']['outGoingAdventure'] = 'Hrdina je na dobrodružství.';
$Definition['Dorf1']['movements']['evasion'] = 'Únik';
$Definition['Dorf1']['movements']['settlers'] = 'Osadníci';
$Definition['Dorf1']['movements']['settlersOnTheWay'] = 'Osadníci na cestě';
$Definition['Dorf1']['movements']['outGoingEvasion'] = 'Únik vlastních jednotek';
//embassyWhite
$Definition['embassyWhite']['embassy'] = 'Ambasáda';
$Definition['embassyWhite']['invites to you:'] = 'Pozvánky pro tebe:';
$Definition['embassyWhite']['max embassy level:'] = 'Max. úroveň ambasády: ';
$Definition['embassyWhite']['construct an embassy'] = 'postav ambasádu';
$Definition['embassyWhite']['Alliance forum'] = 'Fórum aliance';
$Definition['embassyWhite']['Alliance overview'] = 'Přehled aliance';
$Definition['embassyWhite']['no alliance'] = 'Bez aliance';
$Definition['embassyWhite']['You are currently not part of any alliance'] = 'Momentálně nejsi členem žádné aliance';
//ExtraModules
$Definition['ExtraModules'] = [
    'addFarmsNearby' => 'Přidat %sx%s blízkých farem',
    'addFarms' => 'Automaticky přidat 100 jedinečných farem',
    'addFarmsIsDisabledTill' => 'Tato funkce je vypnutá do %s.',
    'buyAdventure' => 'Najít dobrodružství',
    'upgradeToMaxLevel' => 'Rozšířit na max. úroveň',
    'upgradeStorageToMaxLevel' => 'Rozšířit na max. úroveň',
    'increaseStorage' => 'Zvýšit kapacitu skladů',
    'smithyMaxLevel' => 'Rozšířit na max. úroveň',
    'smithyUpgradeAllToMax' => 'Rozšířit vše na max. úroveň',
    'academyResearchAll' => 'Vyzkoumat všechny jednotky',
    'finishTraining' => 'Dokončit veškerý výcvik',
    'Used %s of %s' => 'Použito %s z %s',
    'Feature limit reached' => 'Dosažen limit funkce.',
    'Errors' => [
        'WWDisabled' => 'Tuto funkci nelze použít ve vesnicích s divem světa.',
        'Feature limit reached' => 'Dosažen limit funkce.',
    ],
];
//Farmlist
$Definition['FarmList'] = [
    'You can only have one autoraid per account' => 'Na účet můžeš mít jen jednu automatickou loupež.',
    'Auto raid On' => 'Automatická loupež zapnuta',
    'Auto raid Off' => 'Automatická loupež vypnuta',
    'Auto raid costs %s silver(s) every %s seconds when it hits the farmlist' => 'Automatická loupež stojí %s <img class="silver" src="img/x.gif"> a v průběhu času náhodně útočí na seznam farem.',
    "System: You must wait some time before sending another raid" => "Systém: Před odesláním další loupeže musíš chvíli počkat.",
    "nameTooLong" => "Jméno je příliš dlouhé.",
    "enterListName" => "Zadej název seznamu.",
    "nameIsNotUnique" => "Tento název už je použitý. Název musí být v rámci vesnice jedinečný.",
    "nRaidsMade" => "Provedeno %s loupeží.",
    "delete" => "Smazat",
    "choose_village" => "Vyber vesnici",
    "add" => "Přidat",
    "addRaid" => "Přidat loupež",
    "editRaid" => "Upravit loupež",
    "choose_target" => "Vyber cíl",
    "FarmList" => "Seznam farem",
    "reallyDelete" => "Opravdu smazat?",
    "raidList" => "Seznam loupeží",
    "lastTargets" => "Poslední cíle",
    "create_new_list" => "Vytvořit nový seznam",
    "rename_list" => "Přejmenovat seznam",
    "rename" => "Přejmenovat",
    "Village" => "Vesnice",
    "pop" => "Obyv.",
    "distance" => "Vzdálenost",
    "troops" => "Jednotky",
    "lastRaid" => "Poslední loupež",
    "name" => "Jméno",
    "create" => "Vytvořit",
    "checkAll" => "Označit vše",
    "startRaid" => "Spustit loupež",
    "details" => "Podrobnosti",
    "occupiedOasis" => "Obsazená oáza",
    "unoccupiedOasis" => "Neobsazená oáza",
    "edit" => "Upravit",
    "outGoingAttack" => "Vlastní útoky",
    "noSlot" => "Do seznamu není přidána žádná farma.",
    "noVillageInTarget" => "Na těchto souřadnicích nebyla nalezena žádná vesnice.",
    "noTroopsSelected" => "Nebyly vybrány žádné jednotky.",
    "sameVillageEntered" => "Nemůžeš zaútočit na právě vybranou vesnici.",
    "errorWorldWonderVillage" => "Na tuto vesnici nemůžeš zaútočit.",
    "slotsFull" => "Všechna místa jsou obsazena.",
    'editAllSlotsRaid' => 'Upravit všechna místa',
    'Attack_iReport1' => 'Útok na <img src=\'img/x.gif\' class=\'iReport iReport1\'/><img src=\'img/x.gif\' class=\'iReport iReport2\'/><img src=\'img/x.gif\' class=\'iReport iReport3\'/>',
    'Attack_iReport2' => 'Útok na <img src=\'img/x.gif\' class=\'iReport iReport1\'/>',
    'Attack_iReport3' => 'Útok na <img src=\'img/x.gif\' class=\'iReport iReport1\'/><img src=\'img/x.gif\' class=\'iReport iReport2\'/> kromě zcela ubráněných.',
    'Attack_att3' => '<img src=\'img/x.gif\' class=\'att3\'/><br /> Útočit náhodně, když máš méně jednotek, než je součet seznamu.',
    'underProtection' => 'Dokud máš aktivní ochranu nováčka, nemůžeš útočit na jiné hráče.',
    'You attacked %s seconds ago, you need to wait %s seconds before sending another raid' => 'Poslední útok jsi poslal před %s sekundami. Před další loupeží musíš počkat %s sekund.',
];

use Core\Helper\WebService;
$Definition['Global']['Building settings'] = 'Nastavení budov';
$Definition['Global']['Times'] = 'Opakování';
$Definition['Global']['Unlimited'] = 'Neomezeně';
$Definition['Global']['Hour'] = 'Hodina';
$Definition['Global']['Day'] = 'Den';
$Definition['Global']['Minute'] = 'Minuta';
$Definition['Global']['Second'] = 'Sekunda';
$Definition['Global']['This tab is set as favourite'] = 'Tato záložka je nastavena jako oblíbená';
$Definition['Global']['Select tab %s as favourite'] = 'Vybrat záložku %s jako oblíbenou';
$Definition['Global']['loadingData'] = 'Načítání...';

$Definition['Global']['registration_startgame_message_subject'] = 'Předmět';
$Definition['Global']['registration_startgame_message_message'] = '';


$Definition['Global']['Newsletter_NewServer_subject'] = 'Nový %s %sx server';
$Definition['Global']['Newsletter_NewServer_content'] = '<strong> Ahoj</strong>,
<br/>
<br/>
<br/>
Tvůj čas nadešel!
<strong>Dnes spouštíme nový <a href="[REGISTRATION_URL]" style="color:#f88c1f;" target="_blank"
                                        title="Travian"> server</a> Travianu</strong>. Vstup do panteonu nejsilnějších a nejstatečnějších bojovníků Travianu!
<br/>
<br/>
<div align="center">
    <a href="[REGISTRATION_URL]" target="_blank" title="Hrát">
        <img alt="Hrát" height="47" src="[INDEX_URL]img/Webstart/Play_Now_Button_x120_rot_EN.jpg"
             style="border-width: 0px; border-style: solid;" title="Hrát" width="120"/></a>
</div>
<br/>
Boj o artefakty a legendární divy světa začíná znovu.
<br/>
<br/>
<strong><a href="[REGISTRATION_URL]" style="color:#f88c1f;" target="_blank" title="Travian">Začni novou bitvu hned teď</a></strong> a přidej se k silné alianci. Neztrácej čas!<br/>
<br/>Staň se nejsilnějším hráčem Travianu!<br/><br/><strong>Tvůj tým Travianu<br/><br/><br/><br/></strong>';
$Definition['Global']['Languages'] = [
    'en' => 'en',
    'ir' => 'ir',
];
$Definition['Global']['edit'] = 'upravit';
$Definition['Global']['ms'] = 'ms';
$Definition['Global']['ns'] = 'ns';
$Definition['Global']['Best regards,The Travian Team'] = 'S pozdravem,<br />tým Travianu';
$Definition['Global']['FreeGoldTitle'] = 'Zlato zdarma';
$Definition['Global']['FreeGold'] = "Milý hráči,

na tvůj účet bylo připsáno %s zlata.

S pozdravem

tým Travianu";
$Definition['Global']['BuyGoldSubject'] = 'Přenos dokončen';
$Definition['Global']['BuyGoldText'] = 'Přenos úspěšně dokončen' . PHP_EOL . PHP_EOL . 'Na tvůj účet bylo připsáno %s zlata.' . PHP_EOL . PHP_EOL . "Tvůj sledovací kód: %s" . PHP_EOL . PHP_EOL . "S pozdravem
tým Travianu";
$Definition['Global']['voucherTitle'] = 'Přenos dokončen';
$Definition['Global']['voucherText'] = 'Tvůj kód poukázky byl použit a zlato bylo převedeno na tvůj účet.' . PHP_EOL . PHP_EOL . 'Na tvůj účet bylo připsáno %s zlata.' . PHP_EOL . PHP_EOL . "Tvůj sledovací kód: %s" . PHP_EOL . PHP_EOL . "S pozdravem
tým Travianu";
$Definition['Global']['Travian'] = 'Travian';
$Definition['Global']['Farm'] = 'Farma';
$Definition['Global']['Farms'] = 'Farmy';
$Definition['Global']['The process may take some time Please wait'] = 'Proces může chvíli trvat. Počkej prosím.';
$Definition['Global']['Loading'] = 'Načítání';
$Definition['Global']['Contact admin'] = 'Kontaktovat podporu';
$Definition['Global']['VoucherEmailSubject'] = 'Kód poukázky Travian';
$Definition['Global']['VoucherEmailMessage'] = 'Milý hráči,<br />nedávno jsi smazal svůj účet v Travianu a měl jsi zakoupené zlato.<br /><br />Pomocí kódu níže si teď můžeš zlato převést na jiný účet v Travianu. <br /> Kód poukázky: %s<br /><br />S pozdravem tým Travianu';
$Definition['Global']['FAQ'] = 'Časté dotazy';
$Definition['Global']['cropfinder']['no results found'] = 'nic nenalezeno.';
$Definition['Global']['today'] = 'Dnes';
$Definition['Global']['yesterday'] = 'Včera';
$Definition['Global']['tomorrow'] = 'zítra';
$Definition['Global']['x days later'] = 'za %s dní';
$Definition['Global']['x days past'] = 'před %s dny';
$Definition['Global']['Dear [PlayerName],'] = 'Milý/á %s,';
$Definition['Global']['beforeyesterday'] = 'Předevčírem';
$Definition['Global']['newVillageName'] = 'Nová vesnice';
$Definition['Global']['moreInformation'] = 'Více informací';
$Definition['Global']['Hello x'] = 'Ahoj %s,';
$Definition['Global']['continue'] = 'pokračovat';
$Definition['Global']['gold'] = 'zlato';
$Definition['Global']['silver'] = 'stříbro';
$Definition['Global']['convertedTo'] = 'Převedeno na';
$Definition['Global']['Login'] = 'Přihlásit';
$Definition['Global']['Register'] = 'Registrovat';
$Definition['Global']['Support'] = 'Podpora';
$Definition['Global']['Wilderness'] = 'Divočina';
$Definition['Global']['OccupiedOasis'] = 'Obsazená oáza';
$Definition['Global']['unOccupiedOasis'] = 'Neobsazená oáza';
$Definition['Global']['Abandoned valley'] = 'Opuštěné údolí';
$Definition['Global']['Player not found'] = 'Hráč nenalezen';
$Definition['Global']['Alliance not found'] = 'Aliance nenalezena';
$Definition['Global']['Invalid Report ID'] = 'Neplatné ID hlášení';
$Definition['Global']['Invalid private key'] = 'Neplatný soukromý klíč';
$Definition['Global']['General']['instructions'] = 'pokyny';
$Definition['Global']['General']['ok'] = 'ok';
$Definition['Global']['General']['cancel'] = 'zrušit';
$Definition['Global']['General']['close'] = 'zavřít';
$Definition['Global']['General']['or'] = 'nebo';
$Definition['Global']['General']['closeWindow'] = 'Zavřít okno';
$Definition['Global']['General']['level'] = 'úroveň';
$Definition['Global']['General']['in'] = 'za';
$Definition['Global']['General']['at'] = 'v';
$Definition['Global']['General']['endat'] = 'Konec v';
$Definition['Global']['General']['hour'] = 'hod.';
$Definition['Global']['General']['startat'] = 'Začátek za';
$Definition['Global']['General']['duration'] = 'trvání';
$Definition['Global']['General']['cost'] = 'cena';
$Definition['Global']['General']['perHour'] = 'za hodinu';
$Definition['Global']['General']['save'] = 'uložit';
$Definition['Global']['NatarsName'] = 'Nataři';
$Definition['Global']['playerVillageName'] = "Vesnice hráče %s";
$Definition['Global']['wwName'] = 'Vesnice s divem světa';
$Definition['Global']['NatureName'] = 'Příroda';
$Definition['Global']['races'][1] = 'Římané';
$Definition['Global']['races'][2] = 'Germáni';
$Definition['Global']['races'][3] = 'Galové';
$Definition['Global']['races'][4] = 'Příroda';
$Definition['Global']['races'][5] = 'Nataři';
$Definition['Global']['races'][6] = 'Egypťané';
$Definition['Global']['races'][7] = 'Hunové';
$Definition['Global']['UnloadHelper']['message'] = 'Provedl jsi změny. Opravdu chceš tuto stránku opustit?';
$Definition['Global']['Footer']['FAQ'] = 'Časté dotazy – odpovědi';
$Definition['Global']['Footer']['Credits'] = 'Autoři';
$Definition['Global']['Footer']['HomePage'] = 'Domovská stránka';
$Definition['Global']['Footer']['Forum'] = 'Fórum';
$Definition['Global']['Footer']['Links'] = 'Odkazy';
$Definition['Global']['Footer']['Terms'] = 'Podmínky';
$Definition['Global']['Footer']['Imprint'] = 'Tiráž';
$Definition['Global']['Footer']['Register'] = 'REGISTRACE';
$Definition['Global']['SUPPORT_MESSAGE_EXTRA_THINGS'] = '<br /><br />-----<br />POZOR: Rádi bychom tě pozvali ke zlepšení naší podpory! <br />Budeme rádi, když si najdeš pár minut na krátký dotazník!<br />K dotazníku: <a target="_blank" href="/docs/">/docs/</a><br />-----<br /><br />Online nápověda T4 Travian: <a target="_blank" href="/docs/">/docs/</a><br />Online nápověda T2.5/T3 Travian: <a target="_blank" href="/docs/">/docs/</a><br />Web: <a target="_blank" href="/docs/">/docs/</a><br />E-mail: <a href="mailto:admin@' . WebService::getJustDomain() . '">admin@' . WebService::getJustDomain() . '</a><br /><br />--<br />Travian Games GmbH<br />Wilhelm-Wagenfeld-Straße 22<br />80807 München<br />Německo<br /><br /><a target="_blank" href="/docs/">/docs/</a><br /><br />CEO: Lars Janssen<br /><br />Rejstříkový soud: Okresní soud Mnichov<br />Číslo živnostenského oprávnění: HRB 173511<br /><br />DIČ: DE 246258085<br />–<br />Tento e-mail a jeho přílohy jsou přísně důvěrné a určené<br />výhradně osobě, které je adresován. Pokud nejsi<br />zamýšleným příjemcem tohoto e-mailu, smaž ho prosím i s<br />přílohami a dej nám vědět.<br />–<br /><br /><br />-----<br /><br /><br />';
$Definition['Global']['SUPPORT_MESSAGE_EXTRA_THINGS_UNIQUE_FINDER'] = 'POZOR: Rádi bychom tě pozvali ke zlepšení naší podpory!';
$Definition['Global']['INVITATION_WITH_PRE_REGISTRATION_CODE_EMAIL_SUBJECT'] = 'Blíží se nový server';
$Definition['Global']['INVITATION_WITH_PRE_REGISTRATION_CODE_EMAIL'] = '<strong>Ahoj [EMAIL]</strong>,
<br />
<br />Tvůj čas nadešel! <strong>Dnes spouštíme</strong>
<strong><a href="[GAME_WORLD_URL_REGISTRATION]" style="color:#f88c1f;" target="_blank" title="Travian">server [WORLD_ID]</a></strong>
<strong>Travianu. </strong>
Vstup do panteonu nejsilnějších a nejstatečnějších bojovníků Travianu!
<br />
<br />
<div align="left">
    <a href="[GAME_WORLD_URL_REGISTRATION]" target="_blank" title="Hrát">
        <img alt="Hrát" height="47" src="[GAME_WORLD_URL]img/en/Play_Now_Button_x120_rot_EN.jpg" style="border-width: 0px; border-style: solid;" title="Hrát" width="120" />
    </a>
</div>
<br />Boj o artefakty a legendární divy světa začíná znovu
<br />
<br />
<strong>
    <a href="[GAME_WORLD_URL_REGISTRATION]" style="color:#f88c1f;" target="_blank" title="Travian">Začni novou bitvu hned teď</a>
</strong> a přidej se k silné alianci. Neztrácej čas!
<br />
<br />Staň se nejsilnějším hráčem Travianu!
<br />
<p>Tvůj kód předregistrace: [PRE_REGISTRATION_CODE]</p>
<br />
<strong>Tvůj tým Travianu</strong>';
$Definition['Global']['WWPlansReleaseMessage'] = '
Od prvních bitev pod hradbami prokletých vesnic
obávaných Natarů uplynuly nespočetné dny. Mnoho armád
svobodných národů i natarské říše bojovalo a padlo před
hradbami pevností, z nichž kdysi Nataři vládli celému
stvoření. Teď, když se prach usadil a mezi přeživšími
vojáky se rozhostil nejistý klid, začaly armády počítat
ztráty a sbírat své mrtvé. Pach boje dosud visel v nočním
vzduchu – pach jatek nezapomenutelných svým rozsahem
a krutostí, které však měly být brzy překonány dalšími.
Největší armády svobodných národů se chystaly k dalšímu
útoku a obávaní Nataři šikovali obranu proti dalšímu
náporu na kdysi vytoužené pevnosti natarské říše.
<br><br>
Brzy dorazili zvědové se zprávami o úchvatném a zároveň
mrazivém pohledu: obávaná armáda nepředstavitelné velikosti
byla spatřena na cestě na samý konec světa, do natarského
hlavního města. Síla tak velká, že prach jejího pochodu
zakrýval světlo, síla tak brutální a nelítostná, že
zadusila veškerou naději. Svobodní lidé věděli, že musí
udělat nemožné: v závodě s časem a nekonečnými hordami
natarské říše postavit div světa, aby vrátili světu mír
a jednou provždy porazili natarskou hrozbu.
<br><br>
Postavit tak velký div ale nebude snadné. Byly potřeba
stavební plány vytvořené a dávno zapomenuté v dávné
minulosti, plány tak tajemné, že ani ti nejmoudřejší
mudrci neznali jejich obsah ani místo uložení.
<br><br>
Desetitisíce zvědů marně procházely celý svět a hledaly
tyto bájné plány všude kromě obávaného natarského hlavního
města, ale nenašly je. Dnes se však vracejí s dobrou
zprávou: místa, kde jsou plány uloženy, byla konečně
odhalena. Natarské armády je ukryly v tajných pevnostech
postavených tak, aby zůstaly lidským očím skryté.
<br><br>
Teď začíná poslední úsek, kdy se největší armády
svobodných národů a Natarů střetnou na největších
bojištích světa, aby rozhodly o osudu všeho pod nebesy.
Meč proti meči – to je válka, jejíž ozvěna se ponese
věky. Je to tvoje válka a zde vepíšeš své jméno do
dějin. Zde se staneš legendou...
<br><br>
<span style="font-size:60%; color: #666;">(Příběh napsal
Grozoth)</span>
<br><br>
<br><br>
<b>Fakta:</b><i>Aby šlo plán ukrást, musí se stát
následující:</i><br>
<li>Musíš na vesnici zaútočit (žádná loupež!)</li>
<li>Útok VYHRÁT</li>
<li>Zničit pokladnici</li>
<li>Útoku se musí účastnit hrdina</li>
<li>Ve vesnici, ze které útok vyšel, MUSÍ být prázdná
pokladnice na úrovni 10</li>
<br><br>
Pokud ne, plán získá další vítězný útok na tuto vesnici
s hrdinou a prázdnou pokladnicí.
<br><br>
Abys mohl stavět div světa, musíš od úrovně 0 do 49 sám
vlastnit plán (ty = vlastník vesnice s divem světa), od
úrovně 50 do 100 potřebuješ další plán, který patří hráči
z tvé aliance! Dva plány na účtu vlastníka divu světa
nestačí!
';
$Definition['Global']['WWConstructStart'] = $Definition['Global']['WWPlansReleaseMessage'];
$Definition['Global']['ArtifactsReleaseMessage'] = <<<HTML_ENTITIES
<div style="width:450px; height:830px; padding: 95px 60px
60px 25px; background:
url(img/Natars_Banner_gross.jpg)
no-repeat;">
        <center>
            <h1>Artefacts</h1>
            <p style="font-size:85%; text-align:justify; width:400px">
                Whispered rumours echo throughout the villages, pertaining
                legends told only by the best of storytellers. They
                concern the Natars, Travian’s most fearsome warriors. It
                is every hero’s dream to slay them, every soldier’s goal.
                No one knows how the Natars grew to be so powerful, their
                soldiers so fearsome. Determined to discover the source of
                their power, an elite group of scouts is sent out to spy
                on them.  They return a few hours later with terrified
                faces and wild theories: it seems the Natars’ power comes
                from mysterious items they call artefacts, which they had
                stolen from your ancestors. Manage to steal the artefacts,
                they say, and you can steal their power.
                <br><br><img src="img/msg.jpg"
                             alt="Artefacts" width="400" height="200" style="float:
right">
                <br><br>
                The time has come to reclaim the artefacts. Conspire with
                your alliance and assemble your warriors to retrieve these
                coveted objects. However, the Natars will not give the
                artefacts up without a fight… and neither shall your
                enemies. If you can manage to successfully repossess the
                artefacts and fend off your enemies, then you will be able
                to reap the rewards. Your buildings shall become
                incredibly strong and powerful, your troops much faster
                and more crop-efficient. Seize the artefacts, bring glory
                to your empire, and become the new legends for your
                successors.
            </p><br/><br/>
<span style="font-size:60%; color: #666;">(Story by
Cryptid_Hunter)</span>
</center>
</div>
HTML_ENTITIES;
/*$Definition['Global']['ServerFinishWinner'] = 'Dear Travian players,
<br><br>
<img src="/img/ww100.png" style="float:right;">
All good things must come to an end, and so too must this
age. Once Solomon was given a ring, upon which was
inscribed a message that could take away all the joys or
sorrows of the world, that message was roughly translated,
“this too shall pass”. It is both our joy and sorrow to
announce to all Travian players that this too has now
passed! We hope you enjoyed your time with us as much as
we enjoyed serving you and thank you for staying until the
very end!
<br><br>
The results:
Day had long since passed into night, yet the workers of
the village <i><b>%s</b></i>, laboured on throughout
the wintery eve, ever wary of the countless armies
marching to destroy their work, knowing that they raced
against time and the greatest threat that had ever faced
the free tribes. Their tireless struggles were rewarded at
<b>11:12 pm</b> after a nameless worker laid the final
stone in what will be forever known as the greatest and
most magnificent creation in all of history since the fall
of the Natars.
<br><br>
Together with the alliance <i>"<b>%s.</b>"</i>, <i>"
<b>%s</b>"</i> was the first to finish the Wonder of the
World, using millions of resources whilst also protecting
it with hundreds of thousands of brave defenders. It is
therefore <i>"<b>%s</b>"</i> who receives the title
"winner of this era"!
<br><br>
<i>"<b>%s</b>"</i> was ruler over the largest personal
empire, followed closely by <i>"<b>%s</b>"</i> and <i>"
<b>%s</b>"</i>.
<br>
<i>"<b>%s</b>"</i> slew more than any other, and was
the mightiest, most fearsome commander.
<br>
<i>"<b>%s</b>"</i> was the most glorious defender,
slaughtering enemies at the village gates, staining the
lands around those villages with their blood.
<br><br>
Best regards,<br>
Your Travian-Team
<br><br>
<span style="font-size:60%; color: #666;">(Story by
Grozoth)</span>';*/
$Definition['Global']['NatarsAreBuildingWW'] = '<center><span style="text-align: center; color: red;"><b>Důležitá zpráva</b></span></center><br />Ach ne, dostali jsme zprávu, že <font color="blue"><b>Nataři</b></font> našli sílu postavit div světa a velmi rychle ho rozšiřují (%s úroveň/úrovně za %s).<img src="/img/natars.jpg" style="float:right;"><br><br><br /><br />Proti jejich síle je nedokážeme zničit, a proto musíme zrychlit stavbu, abychom <font color="blue"><b>Natary</b></font> porazili.';
$Definition['Global']['ServerFinishWinner'] = 'Milí hráči Travianu,
<br><br>
<img src="/img/ww100.png" style="float:right;">
Všechno dobré jednou končí, a tak končí i tento věk.
Šalomoun kdysi dostal prsten s nápisem, který dokázal
odnést všechny radosti i strasti světa. Ten nápis v
překladu zněl zhruba „i toto pomine“. S radostí i
smutkem oznamujeme všem hráčům Travianu, že i toto
nyní pominulo! Doufáme, že jste si čas s námi užili
stejně jako my, a děkujeme, že jste vydrželi až do
samého konce!
<br><br>
Výsledky:
Den se dávno změnil v noc, a přesto dělníci vesnice
<i><b>%s</b></i> pracovali celý zimní večer, stále
ostražití před nespočetnými armádami táhnoucími zničit
jejich dílo, vědomi si toho, že závodí s časem a s
největší hrozbou, jaké kdy svobodné národy čelily.
Jejich neúnavné úsilí bylo odměněno, když bezejmenný
dělník položil poslední kámen díla, které bude navždy
známo jako největší a nejvelkolepější výtvor v dějinách
od pádu Natarů.
<br><br>
Společně s aliancí <i>"<b>%s.</b>"</i> dokončil <i>"
<b>%s</b>"</i> jako první div světa, spotřeboval
miliony surovin a chránil ho statisíci statečných
obránců. Titul "vítěz tohoto věku" proto získává
<i>"<b>%s</b>"</i>!
<br><br>
<i>"<b>%s</b>"</i> vládl největší osobní říši,
těsně následován hráči <i>"<b>%s</b>"</i> a <i>"
<b>%s</b>"</i>.
<br>
<i>"<b>%s</b>"</i> pobil více nepřátel než kdokoli
jiný a byl nejmocnějším a nejobávanějším velitelem.
<br>
<i>"<b>[DEFENDER]</b>"</i> byl nejslavnějším obráncem,
který pobíjel nepřátele u bran vesnic a barvil zemi
kolem nich jejich krví.
<br><br>
S pozdravem<br>
tvůj tým Travianu
<br><br>
<span style="font-size:60%; color: #666;">(Příběh napsal
Grozoth)</span>';
$Definition['Global']['ServerFinishNoWinner'] = 'Milí hráči Travianu,
<br><br>
<img src="/img/ww100.png" style="float:right;">
Všechno dobré jednou končí, a tak končí i tento věk.
Šalomoun kdysi dostal prsten s nápisem, který dokázal
odnést všechny radosti i strasti světa. Ten nápis v
překladu zněl zhruba „i toto pomine“. S radostí i
smutkem oznamujeme všem hráčům Travianu, že i toto
nyní pominulo! Doufáme, že jste si čas s námi užili
stejně jako my, a děkujeme, že jste vydrželi až do
samého konce!
<br><br>
Výsledky:
Hráči se ze všech sil snažili rozšiřovat div světa, ale Nataři ho postavili rychleji.
Vítězi jsou tedy Nataři.
<br><br>
<i>"<b>%s</b>"</i> vládl největší osobní říši,
těsně následován hráči <i>"<b>%s</b>"</i> a <i>"
<b>%s</b>"</i>.
<br>
<i>"<b>%s</b>"</i> pobil více nepřátel než kdokoli
jiný a byl nejmocnějším a nejobávanějším velitelem.
<br>
<i>"<b>[DEFENDER]</b>"</i> byl nejslavnějším obráncem,
který pobíjel nepřátele u bran vesnic a barvil zemi
kolem nich jejich krví.
<br><br>
S pozdravem<br>
tvůj tým Travianu
<br><br>
<span style="font-size:60%; color: #666;">(Příběh napsal
Grozoth)</span>';
$Definition['Global']['SUPPORT_MESSAGE_EXTRA_THINGS'] = '<br /><br />';
$Definition['Global']['SUPPORT_MESSAGE_EXTRA_THINGS_UNIQUE_FINDER'] = '<br />';
$Definition['Global']['GoldPromotionPublicMsg'] = '<font color="#998200" size="4"><b>Zlatá akce!!!</b></font><br /><br />Tato zlatá akce probíhá od %s do %s.<br /><br />Za veškeré zlato zakoupené v této době dostaneš o <b>20%%</b> zlata více než obvykle.';
//GoldHelper
$Definition['GoldHelper']['exchangeResources']['exchangeResources'] = 'Vyměnit suroviny';
$Definition['GoldHelper']['exchangeResources']['error_in_ww'] = 'Tuto funkci nelze použít ve vesnicích s divem světa.';
$Definition['GoldHelper']['exchangeResources']['error_no_marketplace'] = 'Postav tržiště';
$Definition['GoldHelper']['exchangeResources']['error_low_pop'] = 'Musíš mít alespoň 40 obyvatel';
$Definition['GoldHelper']['exchangeResources']['error_low_resources'] = 'Musíš mít alespoň 50 surovin.';
//Help
$Definition['Help']['Help system'] = 'Systém nápovědy';
$Definition['Help']['FAQ - Answers'] = 'Časté dotazy – odpovědi';
$Definition['Help']['Game rules'] = 'Pravidla hry';
$Definition['Help']['Contact ingame support'] = 'Kontaktovat podporu ve hře';
$Definition['Help']['If you couldn\'t find an answer, contact the ingame support here'] = 'Pokud jsi nenašel odpověď, kontaktuj zde podporu ve hře.';
$Definition['Help']['Plus questions'] = 'Dotazy k PLUS';
$Definition['Help']['You can ask questions about payment and premium features here'] = 'Zde se můžeš ptát na platby a prémiové funkce.';
$Definition['Help']['Forum'] = 'Fórum';
$Definition['Help']['On our Forum, you can meet and converse with other players'] = 'Na našem fóru se můžeš setkat a bavit s ostatními hráči.';
$Definition['Help']['Short instruction'] = 'Krátký návod';
$Definition['Help']['Here you can find short explanations about the troops and buildings found in Travian'] = 'Zde najdeš krátká vysvětlení k jednotkám a budovám v Travianu.';
$Definition['Help']['Interface help'] = 'Nápověda k rozhraní';
$Definition['Help']['An overview of the user interface with short descriptions of the different functions'] = 'Přehled uživatelského rozhraní s krátkým popisem jednotlivých funkcí.';
$Definition['Help']['Here, you can find the current game rules'] = 'Zde najdeš aktuální pravidla hry.';
$Definition['Help']['Here, you can find your answers about Travian If you really can\'t find your answer here, you can also contact our ingame support afterwards'] = 'Zde najdeš odpovědi na otázky o Travianu. Pokud odpověď opravdu nenajdeš, můžeš potom kontaktovat naši podporu ve hře.';
$Definition['Help']['inGameSupport'] = [
    'description' => 'V našem systému nápovědy Odpovědi rychle a bez dlouhého hledání najdeš odpovědi na všechny obecné otázky o Travianu. Navíc máš možnost kontaktovat podporu. Odpověď podpory může trvat až 24 hodin. Pro rychlejší odpověď zkus Odpovědi.',
    'FAQ - go to Answers' => 'Časté dotazy – přejít na Odpovědi',
    'I tried Answers but I want to contact the support' => 'Zkusil jsem Odpovědi, ale chci kontaktovat podporu.',
    'Contact ingame support' => 'Kontaktovat podporu ve hře',
];
//Hero
$Definition['Hero'] = [
    "Attributes" => "Vlastnosti",
    "Appearance" => "Vzhled",
    "Adventure" => "Dobrodružství",
    "Auction" => "Aukce",
    "showInformation" => "Zobrazit informace",
    "hideInformation" => "Skrýt informace",
    "normal" => "normální",
    "hard" => "těžké",
];
//HeroAdventure
$Definition['HeroAdventure']['Duration to the adventure'] = 'Doba cesty na dobrodružství';
$Definition['HeroAdventure']['Arrival in: %s hrs | Return in: %s hrs'] = 'Příchod za: %s hod. | Návrat za: %s hod.';
$Definition['HeroAdventure']['No rallyPoint'] = 'Ve vesnici hrdiny není shromaždiště.';
$Definition['HeroAdventure']['headerAdventures'] = 'Aktuálně dostupná dobrodružství';
$Definition['HeroAdventure']['location'] = 'místo';
$Definition['HeroAdventure']['moveTime'] = 'Trvání';
$Definition['HeroAdventure']['difficulty'] = 'obtížnost';
$Definition['HeroAdventure']['timeLeft'] = 'Zbývající čas';
$Definition['HeroAdventure']['goTo'] = 'Odkaz';
$Definition['HeroAdventure']['gotoAdventure'] = 'Na dobrodružství.';
$Definition['HeroAdventure']['normal'] = 'Normální';
$Definition['HeroAdventure']['hard'] = 'Těžké';
$Definition['HeroAdventure']['wald'] = 'Les';
$Definition['HeroAdventure']['clay'] = 'Hlína';
$Definition['HeroAdventure']['hill'] = 'Kopec';
$Definition['HeroAdventure']['lake'] = 'Jezero';
$Definition['HeroAdventure']['adventure_dif_hard'] = 'Těžké dobrodružství';
$Definition['HeroAdventure']['adventure_dif_normal'] = 'Normální dobrodružství';
$Definition['HeroAdventure']['natarsLandscape'] = 'Divočina';
$Definition['HeroAdventure']['natars'] = 'Natarská vesnice';
$Definition['HeroAdventure']['adventure'] = 'Dobrodružství';
$Definition['HeroAdventure']['unOccupiedOasis'] = 'Neobsazená oáza';
$Definition['HeroAdventure']['ok'] = 'ok';
$Definition['HeroAdventure']['no adventures'] = 'Žádné dobrodružství nenalezeno.';
$Definition['HeroAdventure']['Hero not available'] = 'Hrdina není k dispozici.';
$Definition['HeroAdventure']['Hero is stationed in village'] = 'Hrdina je v této vesnici';
$Definition['HeroAdventure']['Hero is not in selected village at the moment'] = 'Hrdina momentálně není ve vybrané vesnici.';
$Definition['HeroAdventure']['StartAdventure'] = 'Vyrazit na dobrodružství';
$Definition['HeroAdventure']['back'] = 'zpět';
$Definition['HeroAdventure']['rallyPointNeeded'] = 'Je potřeba shromaždiště.';
$Definition['HeroAdventure']['Send hero to village'] = 'Poslat hrdinu do vesnice';
$Definition['HeroAdventure']['Travel time to village'] = 'Doba cesty do vesnice';
$Definition['HeroAdventure']['Revive hero first'] = 'Nejdřív hrdinu oživ';
$Definition['HeroAdventure']['The Hero must be stationed in the selected village first in order to start an adventure from there'] = 'Aby mohl hrdina vyrazit na dobrodružství z vybrané vesnice, musí v ní nejdřív sídlit.';
$Definition['HeroAdventure']['Travel time calculation for other villages'] = 'Výpočet doby cesty pro ostatní vesnice';
$Definition['HeroAdventure']['Show travel time calculation for other villages'] = 'Zobrazit výpočet doby cesty pro ostatní vesnice';
$Definition['HeroAdventure']['Hide travel time calculation for other villages'] = 'Skrýt výpočet doby cesty pro ostatní vesnice';
$Definition['HeroAdventure']['Hero on his way'] = 'Hrdina je na cestě';
//HeroFace
$Definition['HeroFace'] = [
    "Gender" => "Pohlaví",
    "male" => "muž",
    "female" => "žena",
    "save" => "uložit",
    "random" => "náhodně",
    "headProfile" => "hlava",
    "hairColor" => "barva vlasů",
    "hairStyle" => "účes",
    "ears" => "uši",
    "eyebrow" => "obočí",
    "eyes" => "oči",
    "nose" => "nos",
    "mouth" => "ústa",
    "beard" => "vousy",
];
//Hero Global
$Definition['HeroGlobal']['HeroOverview'] = 'Přehled hrdiny';
$Definition['HeroGlobal']['health'] = 'Zdraví';
$Definition['HeroGlobal']['Experience'] = 'Zkušenosti';
$Definition['HeroGlobal']['Appear'] = 'Vzhled';
$Definition['HeroGlobal']['Attributes'] = 'Vlastnosti';
$Definition['HeroGlobal']['showInformation'] = 'Zobrazit informace';
$Definition['HeroGlobal']['hideInformation'] = 'Skrýt informace';
$Definition['HeroGlobal']['available_adventures:'] = 'Dostupná dobrodružství:';
$Definition['HeroGlobal']['next adventure will expire in:'] = 'Další dobrodružství vyprší za: %s';
$Definition['HeroGlobal']['Auctions'] = 'Aukce';
$Definition['HeroGlobal']['Auctions with maximum bid:'] = 'Aukce s nejvyšší nabídkou:';
$Definition['HeroGlobal']['Auctions which you got outbid:'] = 'Aukce, ve kterých tě přeplatili:';
$Definition['HeroGlobal']['Adventure'] = 'Dobrodružství';
$Definition['HeroGlobal']['Tooltip loading'] = 'Načítání nápovědy...';
$Definition['HeroGlobal']['shortStatus']['Home'] = '	v domovské vesnici';
$Definition['HeroGlobal']['shortStatus']['defending'] = 'brání';
$Definition['HeroGlobal']['shortStatus']['trapped'] = 'v zajetí';
$Definition['HeroGlobal']['shortStatus']['dead'] = 'mrtvý';
$Definition['HeroGlobal']['shortStatus']['return'] = 'na cestě';
$Definition['HeroGlobal']['shortStatus']['adventure'] = 'na cestě';
$Definition['HeroGlobal']['shortStatus']['reinforcement'] = 'na cestě';
$Definition['HeroGlobal']['shortStatus']['attack'] = 'na cestě';
$Definition['HeroGlobal']['shortStatus']['escape'] = 'na cestě';
$Definition['HeroGlobal']['shortStatus']['reviving'] = 'Regeneruje se';
$Definition['HeroGlobal']['longStatus']['Home'] = 'Tvůj hrdina je momentálně ve vesnici %s.';
$Definition['HeroGlobal']['longStatus']['defending'] = 'Tvůj hrdina brání vesnici %s.';
$Definition['HeroGlobal']['longStatus']['trapped'] = 'Tvůj hrdina je uvězněn ve vesnici %s.';
$Definition['HeroGlobal']['longStatus']['dead'] = 'Tvůj hrdina je mrtvý.';
$Definition['HeroGlobal']['longStatus']['return'] = 'Tvůj hrdina se vrací do vesnice %s.';
$Definition['HeroGlobal']['longStatus']['return'] .= 'Příchod za %s v %s.';
$Definition['HeroGlobal']['longStatus']['adventure'] = 'Hrdina je na dobrodružství %s';
$Definition['HeroGlobal']['longStatus']['reinforcement'] = 'Domovská vesnice je %s. Hrdina je na cestě.';
$Definition['HeroGlobal']['longStatus']['reinforcement'] .= 'Příchod za %s v %s.';
$Definition['HeroGlobal']['longStatus']['attack'] = 'Domovská vesnice je %s. Hrdina je na cestě.';
$Definition['HeroGlobal']['longStatus']['attack'] .= 'Příchod za %s v %s.';
$Definition['HeroGlobal']['longStatus']['escape'] = 'Hrdina utíká';
$Definition['HeroGlobal']['longStatus']['escape'] .= 'Příchod za %s v %s.';
$Definition['HeroGlobal']['longStatus']['reviving'] = 'Tvůj hrdina se regeneruje ve vesnici';
$Definition['HeroGlobal']['inventoryStatus']['Home'] = 'Tvůj hrdina je momentálně ve vesnici <a href="karte.php?d=%s">%s</a>.';
$Definition['HeroGlobal']['inventoryStatus']['defending'] = 'Tvůj hrdina brání vesnici <a href="karte.php?d=%s">%s</a>.';
$Definition['HeroGlobal']['inventoryStatus']['trapped'] = 'Tvůj hrdina je uvězněn ve vesnici <a href="karte.php?d=%s">%s</a>.';
$Definition['HeroGlobal']['inventoryStatus']['dead'] = 'Tvůj hrdina je mrtvý.';
$Definition['HeroGlobal']['inventoryStatus']['return'] = 'Tvůj hrdina se vrací do vesnice <a href="karte.php?d=%s">%s</a>.';
$Definition['HeroGlobal']['inventoryStatus']['return'] .= 'Příchod za %s v %s.';
$Definition['HeroGlobal']['inventoryStatus']['adventure'] = 'Hrdina je na dobrodružství %s. Příchod za %s v %s.';
$Definition['HeroGlobal']['inventoryStatus']['adventure'] .= '<div class="subMessage"> (Průběh dobrodružství uvidíš na <a href="build.php?gid=16&amp;tt=1&amp;newdid=%s">shromaždišti</a>.) </div>';
$Definition['HeroGlobal']['inventoryStatus']['reinforcement'] = 'Domovská vesnice je <a href="karte.php?d=%s">%s</a>. Hrdina je na cestě.';
$Definition['HeroGlobal']['inventoryStatus']['reinforcement'] .= 'Příchod za %s v %s.';
$Definition['HeroGlobal']['inventoryStatus']['attack'] = 'Domovská vesnice je <a href="karte.php?d=%s">%s</a>. Hrdina je na cestě.';
$Definition['HeroGlobal']['inventoryStatus']['attack'] .= 'Příchod za %s v %s.';
$Definition['HeroGlobal']['inventoryStatus']['escape'] = 'Hrdina utíká';
$Definition['HeroGlobal']['inventoryStatus']['escape'] .= 'Příchod za %s v %s.';
$Definition['HeroGlobal']['inventoryStatus']['reviving'] = 'Tvůj hrdina se regeneruje ve vesnici <a href="karte.php?d=%s">%s</a>';
$Definition['HeroGlobal']['inventoryStatus']['reviving'] .= ' Zbývající čas:';
//HeroInventory
$Definition['HeroInventory'] = [
    "loyaltyIsMaxYourCannotIncreaseItMore" => "Loajalita tvé vesnice je na maximu. Dál ji zvýšit nemůžeš.",
    "HeroAliveAndCannotUseBucket" => "Tvůj hrdina žije, takže nemůžeš použít živou vodu.",
    "YourHeroWillBeAliveImmediatelyAndOneWaterBucketWillBeUsedAndNoResourcesWillBeRefunded" => "Tvůj hrdina okamžitě ožije. Spotřebuje se jedna živá voda a suroviny se nevrátí.",
    "YouCannotUseThisItemCurrently" => "Tento předmět teď nemůžeš použít.",
    "waterBucketsUsed" => "Živou vodu jsi už použil maximální počet krát. Další použití: %s",
    "currentHeroExperience" => "Současné zkušenosti hrdiny",
    "heroExperienceGainFromItems" => "Zkušenosti získané z předmětu",
    "heroExperienceAfterUse" => "Zkušenosti hrdiny po použití",
    "currentCulturePoints" => "Současné kulturní body",
    "culturePointsGainFromItems" => "Kulturní body z uměleckého díla",
    "culturePointsAfterUsage" => "Kulturní body po použití",
    "heroLevel" => "Úroveň hrdiny",
    "RomanSpecialHeroAttribute" => "Tvůj hrdina získá %s útočné síly za každý bod.",
    "TeutonSpecialHeroAttribute" => "Když je hrdina s jednotkami, máš bonus %s k loupeži.",
    "GualSpecialHeroAttribute" => "Pokud je tvůj hrdina jízdní, bude o %s rychlejší.",
    "notMoveableText" => 'Tvůj hrdina je mrtvý nebo není ve vesnici, takže tento předmět nemůžeš použít.',
    "notMoveableTextDead" => 'Tento předmět nemůžeš použít. Nejdřív oživ hrdinu.',
    "moveDialogDescription" => "Počet předmětů",
    "useDialogDescription" => "Počet předmětů",
    "useOneDialogTitle" => "Opravdu chceš tento předmět použít?",
    "moveDialogTitle" => "Přesunout",
    "useDialogTitle" => "Použít",
    "buttonOk" => "ok",
    "buttonCancel" => "Zrušit",
    "save" => "Uložit změny",
    "Body" => "Tělo",
    "BuyItem" => "Koupit předmět",
    "sellItem" => "Prodat předmět",
    "productBonus" => "Surovinové",
    "increaseOfProduction" => "Zvýšení produkce",
    "productBonusDesc" => "Zvyšuje produkci surovin ve vesnici, ve které hrdina právě sídlí.",
    "defBonus" => "Def bonus",
    "defBonusDesc" => "Zvyšuje obrannou sílu všech jednotek, které doprovází tvůj hrdina.",
    "offBonus" => "Off bonus",
    "offBonusDesc" => "Zvyšuje útočnou sílu všech tvých útočících jednotek, které doprovází tvůj hrdina.",
    "fightingStrength" => "Síla",
    "fightingStrengthDesc" => "Síla hrdiny spojuje jeho obrannou i útočnou sílu. Čím vyšší je jeho síla, tím menší zranění na dobrodružstvích utrží.",
    "attackBehaviourSettings" => "Ukrýt hrdinu",
    "availablePoints" => "Body k dispozici",
    "SaveChanges" => "Ulož prosím změny.",
    "HeroHideDesc" => "Hrdina se při útoku na domovskou vesnici ukryje. ",
    "HeroShowDesc" => "Tvůj hrdina zůstane vždy s jednotkami",
    "ReviveHeroInVillage" => "Pokud chceš nastavit jinou domovskou vesnici, přepni se do ní a zahaj oživení tam.",
    "ResourcesRequiredToReviveHero" => "Suroviny potřebné k oživení hrdiny",
    'HeroReviveInVillageDescription' => 'Domovská vesnice hrdiny byla <a href="karte.php?d=%s">%s</a>. Hrdina bude oživen ve vesnici <a href="karte.php?d=%s">%s</a>.',
    "EnoughResourcesAt" => "Dostatek surovin",
    "changeResourcesHeadline" => "Změnit produkci surovin hrdiny",
    "Regenerate" => "Oživit hrdinu",
    "health" => "Zdraví",
    "heroRegenerationRate" => "Regenerace tvého hrdiny",
    "perDay" => "za den",
    "fromHero" => "od hrdiny",
    "fromItem" => "z předmětů",
    "experience" => "Zkušenosti",
    "experienceNeededToNextLevel" => "Tvůj hrdina potřebuje ještě %s zkušeností k dosažení úrovně %s.",
    "speed" => "Rychlost",
    "speedOfYourHero" => "Rychlost tvého hrdiny",
    "fieldPerHour" => "polí/hod.",
    "fromHorse" => "Z koně",
    "HeroProduction" => "Produkce hrdiny",
    "currentHeroProduction" => "Současná produkce hrdiny",
    "Bonus" => "Bonus",
    "Percent" => "Procent",
    "level" => "úroveň",
    'heroExperienceBonus' => 'Bonus',
];
//HeroItems
$Definition['HeroItems'] = [
    1 => [
        1 => [
            "name" => "Helma poznání",
            "title" => "o %s&#37;+ více zkušeností",
        ],
        2 => [
            "name" => "Helma osvícení",
            "title" => "o %s&#37;+ více zkušeností",
        ],
        3 => [
            "name" => "Helma moudrosti",
            "title" => "o %s&#37;+ více zkušeností",
        ],
        4 => [
            "name" => "Galénova helma",
            "title" => "+%s zdraví každý den",
        ],
        5 => [
            "name" => "Asklépiova helma",
            "title" => "+%s zdraví každý den",
        ],
        6 => [
            "name" => "Hippokratova helma",
            "title" => "+%s zdraví každý den",
        ],
        7 => [
            "name" => "Helma gladiátora",
            "title" => "+%s kulturních bodů za den",
        ],
        8 => [
            "name" => "Helma tribuna",
            "title" => "+%s kulturních bodů za den",
        ],
        9 => [
            "name" => "Helma konzula",
            "title" => "+%s kulturních bodů za den",
        ],
        10 => [
            "name" => "Helma jezdce",
            "title" => "Doba výcviku ve stáji snížena na %s&#37;.",
        ],
        11 => [
            "name" => "Helma kavalerie",
            "title" => "Doba výcviku ve stáji snížena na %s&#37;.",
        ],
        12 => [
            "name" => "Helma obrněné kavalerie",
            "title" => "Doba výcviku ve stáji snížena na %s&#37;.",
        ],
        13 => [
            "name" => "Helma pěšáka",
            "title" => "Doba výcviku v kasárnách snížena na %s&#37;.",
        ],
        14 => [
            "name" => "Helma bojovníka",
            "title" => "Doba výcviku v kasárnách snížena o %s%%.",
        ],
        15 => [
            "name" => "Helma velitele",
            "title" => "Doba výcviku v kasárnách snížena o %s%%.",
        ],
    ],
    2 => [
        82 => [
            "name" => "Lehké brnění regenerace",
            "title" => "+%s zdraví každý den",
        ],
        83 => [
            "name" => "Brnění regenerace",
            "title" => "+%s zdraví každý den",
        ],
        84 => [
            "name" => "Těžké brnění regenerace",
            "title" => "+%s zdraví každý den",
        ],
        85 => [
            "name" => "Lehké brnění",
            "title" => "%s sníží zranění.<br />+%s zdraví každý den",
        ],
        86 => [
            "name" => "Brnění",
            "title" => "%s sníží zranění.<br />+%s zdraví každý den",
        ],
        87 => [
            "name" => "Těžké brnění",
            "title" => "%s sníží zranění.<br />+%s zdraví každý den",
        ],
        88 => [
            "name" => "Lehký hrudní pancíř",
            "title" => "+%s útočné síly hrdiny",
        ],
        89 => [
            "name" => "Hrudní pancíř",
            "title" => "+%s útočné síly hrdiny",
        ],
        90 => [
            "name" => "Těžký hrudní pancíř",
            "title" => "+%s útočné síly hrdiny",
        ],
        91 => [
            "name" => "Lehké ocelové brnění",
            "title" => "%s sníží zranění.<br />+%s útočné síly hrdiny",
        ],
        92 => [
            "name" => "Ocelové brnění",
            "title" => "%s sníží zranění.<br />+%s útočné síly hrdiny",
        ],
        93 => [
            "name" => "Těžké ocelové brnění",
            "title" => "%s sníží zranění.<br />+%s útočné síly hrdiny",
        ],
    ],
    3 => [
        61 => [
            "name" => "Malá mapa",
            "title" => "o %s&#37; rychlejší návrat do vesnice",
        ],
        62 => [
            "name" => "Mapa",
            "title" => "o %s&#37; rychlejší návrat do vesnice",
        ],
        63 => [
            "name" => "Velká mapa",
            "title" => "o %s&#37; rychlejší návrat do vesnice",
        ],
        64 => [
            "name" => "Malá standarta",
            "title" => "o %s&#37; rychlejší přesun vlastních jednotek mezi vlastními vesnicemi.",
        ],
        65 => [
            "name" => "Standarta",
            "title" => "o %s&#37; rychlejší přesun vlastních jednotek mezi vlastními vesnicemi.",
        ],
        66 => [
            "name" => "Velká standarta",
            "title" => "o %s&#37; rychlejší přesun vlastních jednotek mezi vlastními vesnicemi.",
        ],
        67 => [
            "name" => "Malá standarta aliance",
            "title" => "Rychlost jednotek mezi spojenci se zvýší o %s&#37;",
        ],
        68 => [
            "name" => "Standarta aliance",
            "title" => "Rychlost jednotek mezi spojenci se zvýší o %s&#37;",
        ],
        69 => [
            "name" => "Velká standarta aliance",
            "title" => "Rychlost jednotek mezi spojenci se zvýší o %s&#37;",
        ],
        73 => [
            "name" => "Zlodějův pytlík",
            "title" => "+%s&#37; kořisti",
        ],
        74 => ["name" => "Zlodějův pytel", "title" => "+%s&#37; kořisti"],
        75 => [
            "name" => "Zlodějův velký pytel",
            "title" => "+%s&#37; kořisti",
        ],
        76 => [
            "name" => "Malý štít",
            "title" => "+%s útočné síly hrdiny",
        ],
        77 => [
            "name" => "Štít",
            "title" => "+%s útočné síly hrdiny",
        ],
        78 => [
            "name" => "Velký štít",
            "title" => "+%s útočné síly hrdiny",
        ],
        79 => [
            "name" => "Malý natarský roh",
            "title" => "+%s&#37; síly útoku proti Natarům",
        ],
        80 => [
            "name" => "Natarský roh",
            "title" => "+%s&#37; síly útoku proti Natarům",
        ],
        81 => [
            "name" => "Velký natarský roh",
            "title" => "+%s&#37; síly útoku proti Natarům",
        ],
    ],
    4 => [
        16 => [
            "name" => "Legionářova dýka",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého legionáře",
        ],
        17 => [
            "name" => "Legionářův meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého legionáře",
        ],
        18 => [
            "name" => "Legionářův velký meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého legionáře",
        ],
        19 => [//start
               "name" => "Pretoriánova dýka",
               "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého pretoriána",
        ],
        20 => [
            "name" => "Pretoriánův meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého pretoriána",
        ],
        21 => [
            "name" => "Pretoriánův velký meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého pretoriána",
        ],
        22 => [
            "name" => "Imperiánova dýka",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého imperiána",
        ],
        23 => [
            "name" => "Imperiánův meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého imperiána",
        ],
        24 => [
            "name" => "Imperiánův velký meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého imperiána",
        ],
        25 => [
            "name" => "Imperatorisova dýka",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Equites Imperatoris",
        ],
        26 => [
            "name" => "Imperatorisův meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Equites Imperatoris",
        ],
        27 => [
            "name" => "Imperatorisův velký meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Equites Imperatoris",
        ],
        28 => [
            "name" => "Caesarisovo lehké kopí",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Equites Caesaris",
        ],
        29 => [
            "name" => "Caesarisovo kopí",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Equites Caesaris",
        ],
        30 => [
            "name" => "Caesarisovo těžké kopí",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Equites Caesaris",
        ],
        31 => [
            "name" => "Falangova píka",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každou falangu",
        ],
        32 => [
            "name" => "Falangův oštěp",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každou falangu",
        ],
        33 => [
            "name" => "Falangovo kopí",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každou falangu",
        ],
        34 => [
            "name" => "Šermířova dýka",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého šermíře",
        ],
        35 => [
            "name" => "Šermířův meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého šermíře",
        ],
        36 => [
            "name" => "Šermířův dlouhý meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého šermíře",
        ],
        37 => [
            "name" => "Theutatesův krátký luk",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každý Theutates blesk",
        ],
        38 => [
            "name" => "Theutatesův luk",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každý Theutates blesk",
        ],
        39 => [
            "name" => "Theutatesův dlouhý luk",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každý Theutates blesk",
        ],
        40 => [
            "name" => "Druidova cestovní hůl",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého druida jezdce",
        ],
        41 => [
            "name" => "Druidova hůl",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého druida jezdce",
        ],
        42 => [
            "name" => "Druidova bojová hůl",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého druida jezdce",
        ],
        43 => [
            "name" => "Haeduanovo lehké kopí",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého haeduana",
        ],
        44 => [
            "name" => "Haeduanovo kopí",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého haeduana",
        ],
        45 => [
            "name" => "Haeduanovo těžké kopí",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého haeduana",
        ],
        46 => [
            "name" => "Pálkařův kyj",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého pálkaře",
        ],
        47 => [
            "name" => "Pálkařova palice",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého pálkaře",
        ],
        48 => [
            "name" => "Pálkařova obří palice",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého pálkaře",
        ],
        49 => [
            "name" => "Oštěpařova píka",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého oštěpaře",
        ],
        50 => [
            "name" => "Oštěpařův oštěp",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého oštěpaře",
        ],
        51 => [
            "name" => "Oštěpařovo kopí",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého oštěpaře",
        ],
        52 => [
            "name" => "Sekerníkova sekerka",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého sekerníka",
        ],
        53 => [
            "name" => "Sekerníkova sekera",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého sekerníka",
        ],
        54 => [
            "name" => "Sekerníkova bitevní sekera",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého sekerníka",
        ],
        55 => [
            "name" => "Paladinovo lehké kladivo",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého paladina",
        ],
        56 => [
            "name" => "Paladinovo kladivo",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého paladina",
        ],
        57 => [
            "name" => "Paladinovo těžké kladivo",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého paladina",
        ],
        58 => [
            "name" => "Teutonova dýka",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého germánského rytíře",
        ],
        59 => [
            "name" => "Teutonův meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého germánského rytíře",
        ],
        60 => [
            "name" => "Teutonův dlouhý meč",
            "title" => "+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého germánského rytíře",
        ],
        115 => [ //start
                 'name' => 'Kyj domobrany otroků',
                 'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého člena domobrany otroků',
        ],
        116 => [
            'name' => 'Palice domobrany otroků',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého člena domobrany otroků',
        ],
        117 => [
            'name' => 'Obří palice domobrany otroků',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého člena domobrany otroků',
        ],
        118 => [ //start
                 'name' => 'Sekerka Ashova strážce',
                 'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Ashova strážce',
        ],
        119 => [
            'name' => 'Sekera Ashova strážce',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Ashova strážce',
        ],
        120 => [
            'name' => 'Bitevní sekera Ashova strážce',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Ashova strážce',
        ],
        121 => [//start
                'name' => 'Válečníkův krátký chopeš',
                'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého válečníka s chopešem',
        ],
        122 => [
            'name' => 'Válečníkův chopeš',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého válečníka s chopešem',
        ],
        123 => [
            'name' => 'Válečníkův dlouhý chopeš',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého válečníka s chopešem',
        ],
        124 => [//start
                'name' => 'Píka Anhurova strážce',
                'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Anhurova strážce',
        ],
        125 => [
            'name' => 'Píka Anhurova strážce',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Anhurova strážce',
        ],
        126 => [
            'name' => 'Kopí Anhurova strážce',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého Anhurova strážce',
        ],
        127 => [//start
                'name' => 'Krátký luk Rešepova jezdce',
                'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každý Rešepův vůz',
        ],
        128 => [
            'name' => 'Luk Rešepova jezdce',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každý Rešepův vůz',
        ],
        129 => [
            'name' => 'Dlouhý luk Rešepova jezdce',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každý Rešepův vůz',
        ],
        130 => [ //start
                 'name' => 'Žoldnéřova sekerka',
                 'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého žoldnéře',
        ],
        131 => [
            'name' => 'Žoldnéřova sekera',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého žoldnéře',
        ],
        132 => [
            'name' => 'Žoldnéřova bitevní sekera',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého žoldnéře',
        ],
        133 => [ //start
                 'name' => 'Lučištníkův krátký luk',
                 'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého lučištníka',
        ],
        134 => [
            'name' => 'Lučištníkův luk',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého lučištníka',
        ],
        135 => [
            'name' => 'Lučištníkův dlouhý luk',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého lučištníka',
        ],
        136 => [ //start
                 'name' => 'Krátký meč stepního jezdce',
                 'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého stepního jezdce',
        ],
        137 => [
            'name' => 'Meč stepního jezdce',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého stepního jezdce',
        ],
        138 => [
            'name' => 'Dlouhý meč stepního jezdce',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého stepního jezdce',
        ],
        139 => [ //start
                 'name' => 'Ostrostřelcův krátký luk',
                 'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého ostrostřelce',
        ],
        140 => [
            'name' => 'Ostrostřelcův luk',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého ostrostřelce',
        ],
        141 => [
            'name' => 'Ostrostřelcův dlouhý luk',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého ostrostřelce',
        ],
        142 => [ //start
                 'name' => 'Nájezdníkův krátký meč',
                 'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého nájezdníka',
        ],
        143 => [
            'name' => 'Nájezdníkův meč',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého nájezdníka',
        ],
        144 => [
            'name' => 'Nájezdníkův dlouhý meč',
            'title' => '+%s útočné síly hrdiny<br />a +%s útoku a +%s obrany pro každého nájezdníka',
        ],
    ],
    5 => [
        94 => [
            "name" => "Galénovy boty",
            "title" => "+%s zdraví každý den",
        ],
        95 => [
            "name" => "Asklépiovy boty",
            "title" => "+%s zdraví každý den",
        ],
        96 => [
            "name" => "Hippokratovy boty",
            "title" => "+%s zdraví každý den",
        ],
        97 => [
            "name" => "Boty pěšáka",
            "title" => "o %s&#37;+ rychlejší jednotky na vzdálenost delší než %s polí",
        ],
        98 => [
            "name" => "Boty bojovníka",
            "title" => "o %s&#37;+ rychlejší jednotky na vzdálenost delší než %s polí",
        ],
        99 => [
            "name" => "Boty velitele",
            "title" => "o %s&#37;+ rychlejší jednotky na vzdálenost delší než %s polí",
        ],
        100 => [
            "name" => "Bonifácovy botičky",
            "title" => "+%s polí za hodinu pro hrdinu na koni",
        ],
        101 => [
            "name" => "Bonifácovy botky",
            "title" => "+%s polí za hodinu pro hrdinu na koni",
        ],
        102 => [
            "name" => "Bonifácovy boty",
            "title" => "+%s polí za hodinu pro hrdinu na koni",
        ],
    ],
    6 => [
        103 => [
            "name" => "Poník",
            "title" => "Hrdina na koni: +%s polí za hodinu",
        ],
        104 => [
            "name" => "Plnokrevník",
            "title" => "Hrdina na koni: +%s polí za hodinu",
        ],
        105 => [
            "name" => "Válečný kůň",
            "title" => "Hrdina na koni: +%s polí za hodinu",
        ],
    ],
    7 => [
        112 => [
            "name" => "Náplast",
            "title" => "Nasazené náplasti mohou ihned po bitvě vyléčit až 25&#37; ztrát. Vyléčit lze jen tolik jednotek, kolik máš náplastí. Doba léčení se rovná době návratu těchto jednotek, ale nejméně 24 hodin.<br />Předmět lze skládat.<br />Aby účinkoval, musí být nasazen před bitvou.",
        ],
    ],
    8 => [
        113 => [
            "name" => "Obvaz",
            "title" => "Nasazené obvazy mohou ihned po bitvě vyléčit až 33&#37; ztrát. Vyléčit lze jen tolik jednotek, kolik máš obvazů. Doba léčení se rovná době návratu těchto jednotek, ale nejméně 24 hodin.<br />Předmět lze skládat.<br />Aby účinkoval, musí být nasazen před bitvou.",
        ],
    ],
    9 => [
        114 => [
            "name" => "Klec",
            "title" => "S tímto předmětem může hrdina chytat zvířata v oázách a přivést je domů jako obranné jednotky.",
        ],
    ],
    10 => [
        107 => [
            "name" => "Svitek",
            "title" => "Zvýší zkušenosti tvého hrdiny.",
        ],
    ],
    11 => [
        106 => [
            "name" => "Bylinková mast",
            "title" => "Obnoví zdraví hrdiny až na %s&#37;. Každá kapka masti obnoví 1 bod zdraví hrdiny.",
        ],
    ],
    12 => [
        108 => [
            "name" => "Živá voda",
            "title" => "Okamžitě oživí tvého hrdinu.",
        ],
    ],
    13 => [
        110 => [
            "name" => "Kniha moudrosti",
            "title" => "S tímto předmětem můžeš znovu rozdělit všechny body svého hrdiny.",
        ],
    ],
    14 => [
        109 => [
            "name" => "Tabulka zákona",
            "title" => "Okamžitě zvýší loajalitu domovské vesnice hrdiny o %s&#37; za tabulku, nejvýše na %s&#37;.",
        ],
    ],
    15 => [
        111 => [
            "name" => "Umělecké dílo",
            "title" => "Použitím uměleckého díla okamžitě získáš další kulturní body podle produkce kulturních bodů tvých vesnic (+%s kulturních bodů), nejvýše %s kulturních bodů za jedno dílo.<br />Účinkuje po nasazení.<br />Předmět lze skládat.",
        ],
    ],
];
//HeroMansion
$Definition['HeroMansion'] = [
    "AnnexedOasis" => "Připojená oáza",
    "type" => "Typ",
    "owner" => "Vlastník",
    "Village" => "Vesnice",
    "Coordinates" => "Souřadnice",
    "Resources" => "Surovinové",
    "Loyalty" => "Loajalita",
    "conquered" => "Dobyto",
    "Forest" => "Les",
    "Clay" => "Hlína",
    "Hill" => "Kopec",
    "Lake" => "Jezero",
    "nextOasisInHeroMansionLevel" => "Další oáza na úrovni hrdinského dvora ",
    "inReachOases" => "Oázy v dosahu",
    "noSlot" => "Nenalezena žádná oáza.",
    "duration" => "Trvání",
    "finished" => "Dokončeno",
    "AbandonOases" => "Opustit oázu",
    "areYouSure" => "Jsi si jistý?",
    "del" => "smazat",
    "noNearOasis" => "V okolí nebyly nalezeny žádné oázy.",
];
//inGame
$Definition['inGame']['Enable this button to construct by clicking on the field'] = 'Zapni toto tlačítko a stav kliknutím na pole';
$Definition['inGame']['Disable fast upgrade'] = 'Vypnout rychlé rozšiřování';
$Definition['inGame']['Inadmissible name/message'] = 'Nepřípustné jméno/zpráva.';
$Definition['inGame']['Error: very long input'] = 'Chyba: Příliš dlouhé jméno/zpráva.';
$Definition['inGame']['TrainingTime'] = 'Doba výcviku: %s';
$Definition['inGame']['noTroopInBeingTrained'] = 'Nejsou cvičeny žádné jednotky.';
$Definition['inGame']['FreeMerchants'] = 'Volní obchodníci: %s/%s';
$Definition['inGame']['No further tasks available in this category'] = 'V této kategorii už nejsou další úkoly.';
$Definition['inGame']['Click here to extend your beginners protection by 3 days'] = 'Klikni sem a prodluž ochranu nováčka o %s %s';
$Definition['inGame']['You cannot send resources to other players, attack them or be attacked by them while in beginners protection'] = 'Během ochrany nováčka nemůžeš posílat suroviny jiným hráčům, útočit na ně ani být napaden';
$Definition['inGame']['Are you sure you want to extend your beginners protection?'] = 'Opravdu chceš prodloužit ochranu nováčka?';
$Definition['inGame']['Hours'] = 'Hodiny';
$Definition['inGame']['Days'] = 'Dny';
$Definition['inGame']['Minutes'] = 'Minuty';
$Definition['inGame']['Seconds'] = 'Sekundy';
$Definition['inGame']['extend'] = 'Prodloužit';
$Definition['inGame']['Exchange'] = 'Směnit';
$Definition['inGame']['Are you sure you want to convert x gold to y silver?'] = 'Opravdu chceš směnit %s zlata za %s stříbra?';
$Definition['inGame']['maintenanceDesc'] = 'Probíhá údržba systému.<br /> Naši odborníci pracují na co nejrychlejší opravě.<br />Může to trvat několik minut i hodin.<br /> Děkujeme za trpělivost. <br /> Tým Travianu';
$Definition['inGame']['serverTime'] = 'Čas serveru';
$Definition['inGame']['loyalty'] = 'Loajalita';
$Definition['inGame']['needPlusDesc'] = 'Pro tuto funkci potřebuješ aktivní Travian Plus.';
$Definition['inGame']['noWorkShop'] = 'V této vesnici není dílna.';
$Definition['inGame']['DirectLinks'] = 'Přímé odkazy';
$Definition['inGame']['noStable'] = 'V této vesnici není stáj.';
$Definition['inGame']['noBarracks'] = 'V této vesnici nejsou kasárny.';
$Definition['inGame']['noMarketplace'] = 'V této vesnici není tržiště.';
$Definition['inGame']['changeVillageName'] = 'změnit jméno vesnice.';
$Definition['inGame']['trap_desc'] = 'máš %s pastí. %s pastí už je obsazeno.';
$Definition['inGame']['newVillageName'] = 'nové jméno vesnice';
$Definition['inGame']['DoubleClickToChangeVillageName'] = 'Dvojklikem změníš jméno vesnice.';
$Definition['inGame']['villages'] = 'Vesnice';
$Definition['inGame']['hideCoordinates'] = 'skrýt souřadnice';
$Definition['inGame']['showCoordinates'] = 'zobrazit souřadnice';
$Definition['inGame']['Village statistics'] = 'Statistika vesnice';
$Definition['inGame']['culture points'] = 'Kulturní body';
$Definition['inGame']['Culture points generated to take control of another village:'] = 'Kulturní body potřebné k získání další vesnice:';
$Definition['inGame']['MaintenanceWork'] = 'Údržba';
$Definition['inGame']['run celebration'] = 'Uspořádat slavnost';
$Definition['inGame']['festival'] = 'Slavnost';
$Definition['inGame']['type'] = 'typ';
$Definition['inGame']['one celebration is running'] = 'jedna slavnost už probíhá.';
$Definition['inGame']['celebrationRunning'] = 'Probíhající slavnosti';
$Definition['inGame']['Here you can subscribe or unsubscribe to our newsletter'] = 'Zde se můžeš přihlásit k odběru našeho zpravodaje nebo se odhlásit.';
$Definition['inGame']['sitterChangeNameDesc'] = 'Jako zástupce nemůžeš měnit jméno vesnice.';
$Definition['inGame']['The account will be deleted in'] = 'Účet bude smazán za	%s.';
$Definition['inGame']['infoBox']['ArtifactsWillBeReleasedIn'] = 'Artefakty budou vydány za %s hodin.';
$Definition['inGame']['infoBox']['wwPlansWillBeReleasedIn'] = 'Plány divu světa budou vydány za %s hodin.';
$Definition['inGame']['infoBox']['youCanBuildWWIn'] = 'Div světa můžeš stavět za %s hodin.';
$Definition['inGame']['infoBox']['MedalsWillBeGivenIn'] = 'Medaile budou uděleny za %s hodin.';
$Definition['inGame']['infoBox']['AutoFinishTime'] = 'Server Nataři ukončí za %s hodin.';
$Definition['inGame']['infoBox']['CatapultAvailableIn'] = 'Katapulty budou dostupné za %s hodin.';
$Definition['inGame']['infoBox']['PlusWillBeFinished'] = ' Tvůj PLUS účet vyprší za %s.';
$Definition['inGame']['infoBox']['Boost1WillBeFinished'] = 'Bonus produkce dřeva skončí za %s hodin.';
$Definition['inGame']['infoBox']['Boost2WillBeFinished'] = 'Bonus produkce hlíny skončí za %s hodin.';
$Definition['inGame']['infoBox']['Boost3WillBeFinished'] = 'Bonus produkce železa skončí za %s hodin.';
$Definition['inGame']['infoBox']['Boost4WillBeFinished'] = 'Bonus produkce obilí skončí za %s hodin.';
$Definition['inGame']['infoBox']['plusAccountExpired'] = 'Tvůj PLUS účet vypršel.';
$Definition['inGame']['infoBox']['productionBoost1Expired'] = 'Bonus produkce dřeva vypršel.';
$Definition['inGame']['infoBox']['productionBoost2Expired'] = 'Bonus produkce hlíny vypršel.';
$Definition['inGame']['infoBox']['productionBoost3Expired'] = 'Bonus produkce železa vypršel.';
$Definition['inGame']['infoBox']['productionBoost4Expired'] = 'Bonus produkce obilí vypršel.';
$Definition['inGame']['infoBox']['protection'] = 'Ochrana nováčka ti zbývá ještě %s hodin.';
$Definition['inGame']['infoBox']['Your account was banned!'] = 'Tvůj účet byl zablokován!';
$Definition['inGame']['infoBox']['Reason'] = 'Důvod';
$Definition['inGame']['infoBox']['Not enough gold to extend this feature'] = 'Na prodloužení této funkce nemáš dost zlata.';
$Definition['inGame']['infoBox']['Reasons'] = [
    'Pushing' => 'Pushing',
    'Cheat' => 'Podvádění',
    'Hack' => 'Hackování',
    'Bug' => 'Chyba hry',
    'Bad Name' => 'Nevhodné jméno',
    'Multiaccount' => 'Multiúčet',
    'Swearing' => 'Sprostá mluva',
    'Insults' => 'Urážky',
    'Spam' => 'Spam',
    'Another' => 'Jiné',
    'Other' => 'Ostatní'
];
$Definition['inGame']['infoBox']['AutoType_GoldPromotion'] = '<font color="#ff8000" size="2"><b>Zlatá akce</b></font><br />Tato zlatá akce probíhá od %s do %s.<br />Za veškeré zlato zakoupené v této době dostaneš o <b>20%%</b> zlata více než obvykle.';
$Definition['inGame']['infoBox']['Your account is in vacation until [time]'] = 'Tvůj účet je v dovolenkovém režimu do %s.';
$Definition['inGame']['infoBox']['day(s)'] = 'den/dny';
$Definition['inGame']['infoBox']['hour(s)'] = 'hodina/hodiny';
$Definition['inGame']['infoBox']['overFlowMessage'] = 'Tvá schránka hlášení je zaplněná z více než 90%%. Přebytečná hlášení starší než %d %s mohou být smazána.';
$Definition['inGame']['More Information'] = 'Více informací';
//Your report box has over 90% of its size used. Overflowing reports, which are older than one day may be deleted.
$Definition['inGame']['total_messages'] = 'Zprávy';
$Definition['inGame']['unReadMessages'] = 'Nepřečtené zprávy';
$Definition['inGame']['showMoreMessages'] = 'Zobrazit více zpráv';
$Definition['inGame']['hideMoreMessages'] = 'Skrýt zprávy';
$Definition['inGame']['Confirm'] = 'Potvrdit';
$Definition['inGame']['InfoBox'] = 'Infobox';
$Definition['inGame']['Delete this message permanently?'] = 'Trvale smazat tuto zprávu?';
$Definition['inGame']['This tab is selected as your fav tab'] = 'Tato záložka je vybrána jako oblíbená';
$Definition['inGame']['Select this tab as fav'] = 'Vybrat tuto záložku jako oblíbenou';
$Definition['inGame']['sendMessage'] = 'Poslat zprávu';
$Definition['inGame']['zoom_in'] = 'Přiblížit';
$Definition['inGame']['zoomIn'] = 'Přiblížit';
$Definition['inGame']['available'] = 'k dispozici';
$Definition['inGame']['Amount'] = 'Množství';
$Definition['inGame']['train_troops'] = 'Cvičit jednotky';
$Definition['inGame']['bannedSmallPage'] = 'Jsi zablokován.';
$Definition['inGame']['in_training'] = 've výcviku';
$Definition['inGame']['next_creating'] = 'Další past bude hotová za %s hodin.';
$Definition['inGame']['next_training'] = 'Další jednotka bude vycvičena za %s hodin.';
$Definition['inGame']['in_creating'] = 'Ve výrobě';
$Definition['inGame']['train'] = 'Cvičit';
$Definition['inGame']['palaceNoTrainText'] = 'K založení nové vesnice potřebuješ palác na úrovni 10, 15 nebo 20 a 3 osadníky. K dobytí nové vesnice potřebuješ palác na úrovni 10, 15 nebo 20 a senátora, náčelníka nebo stařešinu.';
$Definition['inGame']['residenceNoTrainText'] = 'K založení nové vesnice potřebuješ rezidenci na úrovni 10 nebo 20 a 3 osadníky. K dobytí nové vesnice potřebuješ rezidenci na úrovni 10 nebo 20 a senátora, náčelníka nebo stařešinu.';
$Definition['inGame']['noTroops'] = 'Pro tuto budovu zatím nebyly vyzkoumány žádné jednotky.<br />Nové jednotky můžeš vyzkoumat v akademii.';
$Definition['inGame']['Profile']['Profile'] = 'Profil';
$Definition['inGame']['Profile']['edit profile description'] = 'Upravit popis profilu.';
$Definition['inGame']['Options']['Options'] = 'Možnosti';
$Definition['inGame']['Options']['edit account settings'] = 'Upravit nastavení účtu.';
$Definition['inGame']['Options']['you may not edit settings of another account'] = 'Nastavení cizího účtu nemůžeš upravovat.';
$Definition['inGame']['Forum']['Forum'] = 'Fórum';
$Definition['inGame']['Forum']['Meet other players on our external forum'] = 'Setkej se s ostatními hráči na našem externím fóru.';
$Definition['inGame']['Help']['Help'] = 'Nápověda';
$Definition['inGame']['Help']['Manuals, Answers and Support'] = 'Příručky, Odpovědi a podpora';
$Definition['inGame']['Logout']['Logout'] = 'Odhlásit';
$Definition['inGame']['Logout']['Log out from the game'] = 'Odhlásit se ze hry.';
$Definition['inGame']['Navigation']['Resources'] = 'Surovinové';
$Definition['inGame']['Navigation']['Buildings'] = 'Budovy';
$Definition['inGame']['Navigation']['Map'] = 'Mapa';
$Definition['inGame']['Navigation']['Statistics'] = 'Statistiky';
$Definition['inGame']['Navigation']['Reports'] = 'Hlášení';
$Definition['inGame']['Navigation']['newReports'] = 'Nová hlášení';
$Definition['inGame']['Navigation']['Messages'] = 'Zprávy';
$Definition['inGame']['Navigation']['newMessages'] = 'Nové zprávy';
$Definition['inGame']['Navigation']['Buy gold'] = 'Koupit zlato';
$Definition['inGame']['gold'] = 'Zlato';
$Definition['inGame']['goldShort'] = 'Zlato';
$Definition['inGame']['silver'] = 'Stříbro';
$Definition['inGame']['silverShort'] = 'Stříbro';
$Definition['inGame']['endAt'] = 'Hotovo v';
$Definition['inGame']['finishNow'] = 'Dokončit hned';
$Definition['inGame']['finishNowSitterNoPermission'] = 'Jako zástupce nemáš oprávnění používat zlato.';
$Definition['inGame']['finishNowWWDisabled'] = 'Ve vesnici s divem světa nemůžeš používat zlato.';
$Definition['inGame']['resources']['resources'] = 'Surovinové';
$Definition['inGame']['resources']['r0'] = 'Vše';
$Definition['inGame']['resources']['r1'] = 'Dřevo';
$Definition['inGame']['resources']['r2'] = 'Hlína';
$Definition['inGame']['resources']['r3'] = 'Železo';
$Definition['inGame']['resources']['r4'] = 'Obilí';
$Definition['inGame']['resources']['r5'] = 'Volné obilí';
$Definition['inGame']['Small celebration'] = 'Malá slavnost';
$Definition['inGame']['Big celebration'] = 'Velká slavnost';
$Definition['inGame']['Hold a celebration'] = 'Uspořádat slavnost';
$Definition['inGame']['productionBoost']['activate'] = 'Aktivní';
$Definition['inGame']['productionBoost']['remainingTime'] = 'Zbývající čas: %s do %s.';
$Definition['inGame']['productionBoost']['remainingTime2'] = 'Končí v %s hod.';
$Definition['inGame']['productionBoost']['woodProductionBoost'] = 'Více produkce dřeva';
$Definition['inGame']['productionBoost']['clayProductionBoost'] = 'Více produkce hlíny';
$Definition['inGame']['productionBoost']['ironProductionBoost'] = 'Více produkce železa';
$Definition['inGame']['productionBoost']['cropProductionBoost'] = 'Více produkce obilí';
$Definition['inGame']['productionBoost']['extend'] = 'Prodloužit';
$Definition['inGame']['productionBoost']['activate now'] = 'Aktivovat hned <br> Trvání bonusu ve dnech: %s';
$Definition['inGame']['productionBoost']['extend now'] = 'Prodloužit hned <br> Trvání bonusu ve dnech: %s';
$Definition['inGame']['plus']['remainingTime'] = 'Zbývající čas: %s dní do %s';
$Definition['inGame']['plus']['remainingTime2'] = 'Končí %s';
$Definition['inGame']['plus']['activate'] = 'Aktivovat';
$Definition['inGame']['plus']['extend'] = 'Prodloužit';
$Definition['inGame']['plus']['activate now'] = 'Aktivovat hned.<br> Trvání: %s den';
$Definition['inGame']['plus']['extend now'] = 'Prodloužit hned.<br> Trvání: %s den';
$Definition['inGame']['sitterNoPermForGold'] = 'Jako zástupce to můžeš udělat jen tehdy, když ti to vlastník účtu povolil.';
$Definition['inGame']['continue'] = 'pokračovat';
$Definition['inGame']['stockBar']['production']['r1'] = 'Dřevo||Produkce: %s<br>Plno za: %s<br>Klikni pro více informací';
$Definition['inGame']['stockBar']['production']['r2'] = 'Hlína||Produkce: %s<br>Plno za: %s<br>Klikni pro více informací';
$Definition['inGame']['stockBar']['production']['r3'] = 'Železo||Produkce: %s<br>Plno za: %s<br>Klikni pro více informací';
$Definition['inGame']['stockBar']['production']['r4'] = 'Obilí||Produkce po odečtení spotřeby budov: %s<br>Plno za: %s<br>Klikni pro více informací';
$Definition['inGame']['stockBar']['production']['r4Empty'] = 'Obilí||Produkce po odečtení spotřeby budov: %s<br><span class="red">Prázdno za: %s</span><br>Klikni pro více informací';
$Definition['inGame']['stockBar']['production']['r5'] = 'Volné obilí pro další budovy.||Bilance obilí: %s<br>Klikni pro více informací';
$Definition['inGame']['bannedPage'] = 'Ahoj %s!
</br></br>Byl jsi zablokován kvůli porušení pravidel. Každý hráč smí na každém serveru vlastnit a hrát jen jeden účet.
</br></br>Aby ses vyhnul dalšímu zablokování, přečti si pečlivě pravidla:
</br></br><center><a href="http://' . WebService::getJustDomain() . 'spielregeln.php">» Pravidla hry</a></center>
</br></br></br>
Pokud chceš pokračovat ve hře, kontaktuj Multihuntera a vyřeš to s ním
</br></br><center><a href="messages.php?t=1&id=2">» Napsat zprávu</a></center>
</br></br>
Při psaní zprávy se drž těchto rad:
</br></br>
● Každé zablokování má svůj důvod. Zamysli se, co mohlo být příčinou, a vyřeš to s Multihunterem.
</br>
● Multihunteři mohou prověřit obrovské množství informací o účtech. Drž se pravdy a nevymlouvej se.
</br>
● Buď vstřícný a soudný, trest to může zmírnit.
</br>
● Pokud Multihunter neodpoví hned, pravděpodobně není online.</br>
 Posíláním více zpráv se nic nevyřeší rychleji, zvlášť když ještě nečetl ani tu první.
</br>
● Pokud jsi byl opravdu zablokován neprávem, zůstaň v klidu a zdvořile Multihunterovi vysvětli svůj pohled.
</p>
<small><i>S pozdravem tvůj tým Travianu</i></small></p>';
$Definition['inGame']['bannedPageWithTime'] = 'Ahoj %s!
</br></br>Byl jsi zablokován z důvodu: %s. Každý hráč smí na každém serveru vlastnit a hrát jen jeden účet.
</br></br>Aby ses vyhnul dalšímu zablokování, přečti si pečlivě pravidla:
</br></br><center><a href="http://' . WebService::getJustDomain() . 'spielregeln.php">» Pravidla hry</a></center>
</br></br></br>
Pokud chceš pokračovat ve hře, počkej do %s nebo kontaktuj Multihuntera a vyřeš to s ním
</br></br><center><a href="messages.php?t=1&id=2">» Napsat zprávu</a></center>
</br></br>
Při psaní zprávy se drž těchto rad:
</br></br>
● Každé zablokování má svůj důvod. Zamysli se, co mohlo být příčinou, a vyřeš to s Multihunterem.
</br>
● Multihunteři mohou prověřit obrovské množství informací o účtech. Drž se pravdy a nevymlouvej se.
</br>
● Buď vstřícný a soudný, trest to může zmírnit.
</br>
● Pokud Multihunter neodpoví hned, pravděpodobně není online.</br>
 Posíláním více zpráv se nic nevyřeší rychleji, zvlášť když ještě nečetl ani tu první.
</br>
● Pokud jsi byl opravdu zablokován neprávem, zůstaň v klidu a zdvořile Multihunterovi vysvětli svůj pohled.
</p>
<small><i>S pozdravem tvůj tým Travianu</i></small></p>';
$Definition['inGame']['bannedClickPage'] = '
<h2>Ahoj %s,</h2>
<br/>
<center><b>Tvůj účet byl zablokován</b>
    <br>
</center>
<div>Zablokování může být dočasné kvůli příliš rychlému klikání, v tom případě se po 5 sekundách samo zruší.
    <br>
    <br>nebo k němu mohlo dojít kvůli:
    <br>
    <br>
    <li>Používání nepovoleného skriptu</li>
    <li>Pushingu</li>
    <li>Spamu</li>
    <li>Nadávání ostatním hráčům</li>
    <li>Pokusu o napadení systému</li>
    <br>
    <br>Pro více informací kontaktuj <a href="messages.php?t=1&id=4">Multihuntera</a> nebo <a href="messages.php?t=1&id=1">podporu</a> nebo pošli e-mail <b>podpoře ve hře</b>.
    <br>
    <br>S pozdravem</div>
';
$answersUrl = getAnswersUrl();
$Definition['inGame']['QUEST_MESSAGE_SUBJECT'] = 'Rady pro začátek hry';
$Definition['inGame']['QUEST_MESSAGE'] = <<<HTML
Hello %s,<br><br>
<br><br>
Travian is a game of domination through strategy and diplomacy. At the core, it is about standing up against other players. Seek allies to reach your goals; even the strongest players require assistance.<br><br>
<br><br>
The predominant task is always to survive and grow. In order to start on a sound footing, simply solve the tasks I give you. Protect your resources and villages from greedy neighbours. In case you are struggling, you can find some additional help here: <a href="{$answersUrl}index.php?aid=104#go2answer">Help I am a farm!</a><br><br>
<br><br>
At the start, you can often avoid enemy attacks, however you should defend yourself against conquer attempts. Without troops, you won't be able to help yourself nor your allies. More information here: <a href="{$answersUrl}index.php?aid=172#go2answer">Troops</a><br><br>
<br><br>
Once the dust of the first battles has settled, the strongest alliances will start fighting for control of ever larger territories. They will destroy threats and competitors and form new alliances. Particularly strong alliances will later fight for control of powerful artefacts and - as a display of their own power - to be the first to erect a wonder of the world.<br><br>
<br><br>
Adjust your personal goals to the time you can spend playing - the more ambitious your aims, the more time will be required to build, negotiate and fight.<br><br>
<br><br>
But enough talking! Get together with old and new friends, fight your enemies - and have fun together!
HTML;
$Definition['links']['These links are found helpful by many players Add them to your personal link list'] = 'Tyto odkazy považuje mnoho hráčů za užitečné. Přidej si je do svého seznamu odkazů.';
$Definition['links']['Recommended links'] = 'Doporučené odkazy';
$Definition['links']['recommenced_links'] = [
    [
        'name' => 'Travian Odpovědi',
        'url' => getAnswersUrl() . '*',
    ],
    [
        'name' => 'Přehled vesnic',
        'url' => 'dorf3.php?s=0',
    ],
    [
        'name' => 'Přehled skladů',
        'url' => 'dorf3.php?s=3',
    ],
    [
        'name' => 'Hlášení aliance',
        'url' => 'allianz.php?s=3',
    ],
    [
        'name' => 'Hlášení z okolí',
        'url' => 'reports.php?t=5',
    ],
    [
        'name' => 'Možnosti',
        'url' => 'options.php',
    ],
    [
        'name' => 'Komunita',
        'url' => getForumUrl() . '*',
    ],
];
$Definition['links']['Define often-used pages as direct links Place a * at the end of the link and it will be opened in a new tab'] = 'Nastav si často používané stránky jako přímé odkazy. Když na konec odkazu dáš *, otevře se v nové záložce.';
$Definition['links']['Delete entry'] = 'Smazat položku';
$Definition['links']['add entry'] = 'Přidat položku';
$Definition['links']['No'] = 'Č.';
$Definition['links']['Link Name'] = 'Název odkazu';
$Definition['links']['Link Target'] = 'Cíl odkazu';
$Definition['links']['Link list'] = 'Seznam odkazů';
$Definition['links']['linkWillOpenInNewTab'] = 'Odkaz se otevře v nové záložce.';
$Definition['links']['edit link list'] = 'Upravit seznam odkazů a přidat odkazy.';
$Definition['links']['Travian Plus allows you to make a link list'] = 'S Travian Plus si můžeš vytvořit seznam odkazů.';
//Login
$Definition['Login']['Server will start in'] = 'Server začne za:';
$Definition['Login']['registrationSuccessful'] = 'Děkujeme za registraci. Až server začne, pošleme ti e-mail.';
$Definition['Login']['registrationSuccessful'] .= ' Počkej na e-mail.';
$Definition['Login']['Login'] = 'Přihlásit';
$Definition['Login']['Welcome'] = 'Vítej na serveru %s';
$Definition['Login']['noJavascript'] = 'JavaScript je vypnutý. Abys mohl hrát Travian, musíš ho zapnout v nastavení prohlížeče.';
$Definition['Login']['accountNameOrEmailAddress'] = 'Jméno účtu nebo e-mailová adresa';
$Definition['Login']['pass'] = 'Heslo';
$Definition['Login']['lowResOption'] = 'Verze pro hráče ';
$Definition['Login']['lowRes'] = 's pomalým připojením k internetu';
$Definition['Login']['lowResInfo'] = '(Poznámka: tato verze mapy nemá zapnuté všechny možnosti)';
$Definition['Login']['Captcha'] = 'Captcha';
$Definition['Login']['CaptchaError'] = 'Zadej prosím captchu znovu.';
$Definition['Login']['PasswordForgotten?'] = 'Zapomenuté heslo? ';
$Definition['Login']['We will send you a new password It will be activated as soon as you confirm receipt?'] = 'Pošleme ti nové heslo. Aktivuje se, jakmile potvrdíš jeho přijetí.';
$Definition['Login']['Request new CAPTCHA'] = 'Vyžádat novou CAPTCHU.';
$Definition['Login']['Email'] = 'E-mail';
$Definition['Login']['Request password'] = 'Vyžádat heslo';
$Definition['Login']['Sent to'] = 'Heslo bylo odesláno';
$Definition['Login']['EmailEmpty'] = 'E-mail je prázdný.';
$Definition['Login']['PasswordChangedSuccessfully'] = 'Heslo bylo úspěšně změněno.';
$Definition['Login']['PasswordFail'] = 'Heslo nebylo změněno. Kód už byl možná použit.';
$Definition['Login']['pw_forgot_email'] = nl2br('
<div style="direction: ltr; text-align: left">
Ahoj %s,
vyžádal sis nové heslo pro Travian. Tvé nové přístupové údaje<br>
pro Travian najdeš ve zprávě níže:
---------------------------------------------------------------------------------------------------------------------------------

Tvé přístupové údaje:
Jméno hráče:  %s
E-mailová adresa: %s
Heslo: %s
Herní svět: %s

---------------------------------------------------------------------------------------------------------------------------------
Kliknutím na tento odkaz aktivuješ nové heslo. Staré heslo
pak přestane platit:
<a href="%s">%s</a>

Pokud chceš nové heslo změnit, můžeš zadat jiné ve svém profilu
na záložce "účet".

Pokud sis nové heslo nevyžádal, můžeš tento e-mail ignorovat.

Tvůj tým Travianu
</div>
Tiráž:
Travian Games GmbH, Wilhelm-Wagenfeld-Str. 22, 80807 München, Německo
Tel.: +49 (0)89 3249150, Fax: +49 (0)89 324915970, www.traviangames.de
CEO: Siegfried Müller
Rejstříkový soud: Amtsgericht München, HRB 173511,
DIČ: DE 246258085');
$Definition['Login']['userErrors']['empty'] = 'Zadej přihlašovací jméno.';
$Definition['Login']['userErrors']['notFound'] = ' Přihlašovací jméno neexistuje. Jsi si jistý, že hraješ na tomto serveru?';
$Definition['Login']['pwErrors']['empty'] = 'Zadej heslo';
$Definition['Login']['pwErrors']['wrong'] = 'Heslo je špatné.';
$Definition['Login']['pwErrors']['error21Days'] = 'Tento uživatel se nepřihlásil déle než 21 dní. Přihlášení zástupce není povoleno.';
//Logout
$Definition['Logout'] = [
    "Logout" => "Odhlášení proběhlo úspěšně.",
    "delete_cookies" => "Smazat cookies.",
    "thanks_for_your_visit" => 'Děkujeme za návštěvu',
    "cookieDesc" => "Pokud tento počítač používají i jiní lidé, měl bys pro svou bezpečnost smazat cookies",
    "back_to_the_game" => "Zpět do hry",
];
//Manual
$Definition['Manual'] = [
    'fightingStrength' => "Síla",
    "fightingStrengthAgainstInf" => "Síla proti pěchotě",
    'fightingStrengthAgainstCav' => "Síla proti jízdě",
    "speed" => "rychlost",
    "CarrySize" => "Nosnost",
    "TravianAnswers" => "Travian Odpovědi",
    "moreInTravianAnswers" => "Více v Travian Odpovědích",
    "durationOfTraining" => "Doba výcviku",
    "fieldsPerHour" => "polí za hodinu",
    "preRequests" => "Předpoklady",
    "toOverview" => "na přehled",
    "None" => "Žádný",
    "and" => "a",
    "construction_time" => "Doba stavby",
    "for" => "pro",
    "level" => "úroveň",
    "BuildingPlan" => "Stavební plán",
    "Capital" => "Hlavní město",
    "onlyForTribe" => "Jen pro",
    "Units" => "Jednotky",
];
//Map
$Definition['map'] = [
    "player" => "Hráč",
    "alliance" => "Aliance",
    "owner" => "Vlastník",
    "Tribe" => "Národ",
    "Village" => "Vesnice",
    "Annex Oasis" => "Připojit oázu",
    "sendTroops" => "Poslat jednotky",
    "no free slots" => "Hrdinský dvůr má nyní volná místa.",
    "distribution" => "Rozložení",
    "pop" => "Obyvatelé",
    "capital" => "Hlavní město",
    'x is under protection to y' => 'Tento hráč je pod ochranou do %s',
    'banned' => 'Tento hráč je zablokován.',
    'sendMerchants' => 'Poslat obchodníky',
    "constructMarketplace" => "Postav tržiště.",
    "readySettlers" => "Připravení osadníci",
    "foundNewVillage" => "Založit novou vesnici",
    "culturePointsForNewVillage" => "Kulturní body pro založení nové vesnice ",
    "noResourcesForNewVillage" => "Nedostatek surovin (potřeba alespoň 750 jednotek od každé suroviny).",
    "Add to farm list" => "Přidat do seznamu farem",
    "noFarmList" => "Pro tuto vesnici neexistuje žádný seznam farem. Nejdřív nějaký vytvoř.",
    "Edit farm list (Oasis already in farm list x)" => "Upravit seznam farem (oáza už je v seznamu farem %s)",
    "Edit farm list (Village already in farm list x)" => "Upravit seznam farem (vesnice už je v seznamu farem %s)",
    "move up" => "Posunout nahoru",
    "move down" => "Posunout dolů",
    "move right" => "Posunout doprava",
    "move left" => "Posunout doleva",
    "for this feature you need the goldclub actived" => "Pro tuto funkci potřebuješ aktivovaný Zlatý klub.",
    "vacationModeActive" => "Tento hráč je na dovolené.",
    'noUnits' => 'Žádné.',
    'units' => 'jednotky',
    'Bonus' => 'Bonus',
    'Reports' => 'Hlášení',
    'Surrounding' => 'Okolí',
    'Other Information' => 'Další informace',
    'fields' => 'polí',
    'Distance' => 'Vzdálenost',
    'Simulate raid' => 'Simulovat loupež',
    'No information<br>available!' => 'Žádné informace<br>nejsou k dispozici!',
    'landscape_desc' => 'Krajina',
    'centerMap' => 'Vystředit mapu',
    'constructRallyPoint' => 'Postav shromaždiště.',
    'startAdventure' => 'Vyrazit na dobrodružství',
    "mark_not_found" => "Nic nenalezeno",
    "ok" => "ok",
    "markAlliance" => "Označit alianci",
    "markPlayer" => "Označit hráče",
    "markField" => "označit pole",
    "players" => "hráči",
    "please_resend_all_data" => "Jsou potřeba všechny údaje.",
    "alliances_mark_exists" => "Tato aliance už je označená.",
    "player_mark_exists" => "Tento hráč už je označený.",
    "invalid_coordinates" => "Neplatné souřadnice.",
    "colour_does_not_exists" => "Neplatná barva.",
    "no_alliance_map_mark_error" => "Hráč není v žádné alianci.",
    "map" => "Mapa",
    "zoomIn" => "Přiblížit",
    "zoomOut" => "Oddálit",
    "zoomLevel" => "Úroveň přiblížení",
    "filter" => "filtr",
    "showAllianceMarks" => "Zobrazit značky aliancí",
    "hideAllianceMarks" => "Skrýt značky aliancí",
    "showUserMarks" => "Zobrazit značky hráčů",
    "hideUserMarks" => "Skrýt značky hráčů",
    "minimap" => "Minimapa",
    "outline" => "Značky",
    "users" => "Uživatelé",
    "population" => "Obyvatelé",
    "tribe" => "Národ",
    "village" => "Vesnice",
    "loadingData" => "Načítání...",
    "landscape" => "Krajina‎‭",
    "freeOasis" => "Neobsazená oáza",
    "occupiedOasis" => "Obsazená oáza",
    "natarsVillage" => "Natarská vesnice",
    "bounty" => "Kořist",
    "difficulty" => "Obtížnost",
    "arrival" => "Příchod za",
    "supply" => "Zásoby",
    "spy" => "Špehovat",
    "return" => "Návrat",
    "raid" => "Loupež",
    "attack" => "Útok",
    "save" => "Uložit",
    "cancel" => "Zrušit",
    'flags' => "Standarta aliance",
    "Adventure" => "Dobrodružství",
    "normal" => "Normální",
    "hard" => "Těžké",
    "ownPlayerMarkTitle" => "Vlastní značky polí.",
    "ownAllianceMarks" => "Značky polí pro mou alianci.",
    "ownFlags" => "Vlastní vlajky.",
    "allianceMarkTitle" => 'Značka aliance pro mou alianci',
    "playerMarksTitle" => "Značka hráče pro mou alianci",
    "allianceFlags" => 'Značka pole pro mou alianci',
    "largeMap" => "Celá obrazovka",
    "needPlus" => "Pro použití této funkce musíš aktivovat Plus!",
    "cropFinder" => "Hledač obilí",
    "needClub" => "Pro použití této funkce musíš aktivovat Zlatý klub!",
    "YouAreBanned" => "Jsi zablokován.",
    "YouAreInVacationMode" => "Jsi v dovolenkovém režimu.",
    "YouAreProtected" => "Jsi pod ochranou nováčka a nemůžeš provést tuto akci.",
    'mapFlagsLimitReached' => 'Dosáhl jsi maxima 5 vlajek. Než přidáš další, jednu smaž.',
    'mapMarksLimitReached' => 'Dosáhl jsi maxima 10 značek. Než přidáš další, jednu smaž.',
];
//Market place
$Definition['MarketPlace']['While you are in beginners protection you are only allowed to do a 1:1 or better trade'] = 'Během ochrany nováčka smíš obchodovat jen v poměru 1:1 nebo lepším.';
$Definition['MarketPlace']['x\'s offering has been accepted'] = 'Nabídka hráče x byla přijata'; //new
$Definition['MarketPlace']['are on their way to you'] = 'jsou na cestě k tobě.';
$Definition['MarketPlace']['have been dispatched by your merchants'] = 'byly odeslány tvými obchodníky';
$Definition['MarketPlace']['Management'] = 'Správa';
$Definition['MarketPlace']['SendResources'] = 'Poslat suroviny';
$Definition['MarketPlace']['resourcesSent'] = 'Suroviny byly odeslány.';
$Definition['MarketPlace']['sitterNoPermissions'] = 'Jako zástupce to můžeš udělat jen tehdy, když ti to vlastník účtu povolil.';
$Definition['MarketPlace']['sameVillage'] = 'Tví obchodníci už jsou v této vesnici.';
$Definition['MarketPlace']['Buy'] = 'Koupit';
$Definition['MarketPlace']['Offer'] = 'Nabídnout';
$Definition['MarketPlace']['Delete'] = 'Smazat';
$Definition['MarketPlace']['Select x as favor tab'] = 'Nastavit %s jako oblíbenou záložku';
$Definition['MarketPlace']['This tab is set as favourite'] = 'Tato záložka je nastavena jako oblíbená.';
$Definition['MarketPlace']['Free merchants'] = 'Volní obchodníci';
$Definition['MarketPlace']['Own merchants and NPC'] = 'Vlastní obchodníci a NPC';
$Definition['MarketPlace']['Merchants offering resources'] = 'Obchodníci nabízející suroviny';
$Definition['MarketPlace']['you are banned'] = 'Byl jsi zablokován kvůli porušení pravidel.';
$Definition['MarketPlace']['Merchants underway'] = 'Obchodníci na cestě';
$Definition['MarketPlace']['Exchange resources'] = 'Směnit suroviny';
$Definition['MarketPlace']['Trade routes'] = 'Obchodní trasy';
$Definition['MarketPlace']['Create new trade route'] = 'Vytvořit novou obchodní trasu';
$Definition['MarketPlace']['Start'] = 'Start';
$Definition['MarketPlace']['Merchants'] = 'Obchodníci';
$Definition['MarketPlace']['Action'] = 'Akce';
$Definition['MarketPlace']['GoldClub'] = 'Zlatý klub';
$Definition['MarketPlace']['Description'] = 'Popis';
$Definition['MarketPlace']['Trade route to x'] = 'Obchodní trasa do %s';
$Definition['MarketPlace']['edit'] = 'upravit';
$Definition['MarketPlace']['cancel'] = 'zrušit';
$Definition['MarketPlace']['Resources'] = 'Surovinové';
$Definition['MarketPlace']['Target village'] = 'Cílová vesnice';
$Definition['MarketPlace']['Target'] = 'Cíl';
$Definition['MarketPlace']['all'] = 'vše';
$Definition['MarketPlace']['only mine'] = 'jen moje';
$Definition['MarketPlace']['others'] = 'ostatní';
$Definition['MarketPlace']['Show villages in list'] = 'Zobrazit vesnice v seznamu';
$Definition['MarketPlace']['Start time'] = 'Čas startu';
$Definition['MarketPlace']['nextTradeRoute'] = 'Další obchodní trasa v %s potřebuje %s obchodníků.';
$Definition['MarketPlace']['not enough merchants'] = 'Nedostatek obchodníků';
$Definition['MarketPlace']['offer added successfully'] = 'Nabídka byla úspěšně přidána.';
$Definition['MarketPlace']['own alliance only'] = 'Jen vlastní aliance';
$Definition['MarketPlace']['max, time of transport'] = 'max. doba přepravy';
$Definition['MarketPlace']["Own offers"] = 'Vlastní nabídky';
$Definition['MarketPlace']["I'm searching"] = 'Hledám';
$Definition['MarketPlace']["Alliance"] = 'Aliance';
$Definition['MarketPlace']["hours"] = 'hodin.';
$Definition['MarketPlace']["accept offer"] = 'přijmout nabídku';
$Definition['MarketPlace']["yes"] = 'ano';
$Definition['MarketPlace']["no"] = 'ne';
$Definition['MarketPlace']["Offers at the marketplace"] = 'Nabídky na tržišti';
$Definition['MarketPlace']["Offered to me"] = 'Nabízeno mně';
$Definition['MarketPlace']["Wanted from me"] = 'Požadováno ode mě';
$Definition['MarketPlace']["Player"] = 'Hráč';
$Definition['MarketPlace']["Duration"] = 'Trvání';
$Definition['MarketPlace']["Action"] = 'Akce';
$Definition['MarketPlace']["onReturnMerchants"] = 'Vracející se obchodníci';
$Definition['MarketPlace']["onComingMerchants"] = 'Přicházející obchodníci';
$Definition['MarketPlace']["onGoingMerchants"] = 'Odcházející obchodníci';
$Definition['MarketPlace']["noResourcesEntered"] = 'Nebyly vybrány žádné suroviny.';
$Definition['MarketPlace']["noVillageWithName"] = 'Vesnice s tímto jménem nebyla nalezena.';
$Definition['MarketPlace']["noVillageInCoordinate"] = 'Na těchto souřadnicích není žádná vesnice.';
$Definition['MarketPlace']["enterVillageNameOrCoordinate"] = 'Neplatné souřadnice.';
$Definition['MarketPlace']["PlayerBanned"] = 'Hráč byl zablokován.';
$Definition['MarketPlace']["serverFinished"] = 'Tento server skončil.';
$Definition['MarketPlace']["go"] = 'jít';
$Definition['MarketPlace']["goBack"] = 'Zpět';
$Definition['MarketPlace']["or"] = 'nebo';
$Definition['MarketPlace']["Village"] = 'Vesnice';
$Definition['MarketPlace']["Arrival"] = 'Příchod';
$Definition['MarketPlace']["in"] = 'za';
$Definition['MarketPlace']["hour"] = 'hod.';
$Definition['MarketPlace']["at"] = 'v';
$Definition['MarketPlace']["sendToVillage"] = 'Poslat do vesnice';
$Definition['MarketPlace']["returnFromVillage"] = 'Návrat z vesnice';
$Definition['MarketPlace']["receiveFromVillage"] = 'Přijato z vesnice';
$Definition['MarketPlace']["Each of your merchants can carry"] = 'Každý tvůj obchodník unese';
$Definition['MarketPlace']["Each of your merchants can carry resources"] = 'surovin';
$Definition['MarketPlace']["prepare"] = 'připravit';
$Definition['MarketPlace']["noOffers"] = 'Nebyly nalezeny žádné nabídky';
$Definition['MarketPlace']["I'm offering"] = 'Nabízím';
$Definition['MarketPlace']['not enough resources'] = 'nedostatek surovin';
$Definition['MarketPlace']['Unable to create offer; maximum ratio allowed is 2:1'] = 'Nabídku nelze vytvořit, maximální povolený poměr je 2:1.';
$Definition['MarketPlace']['Search'] = 'Hledat';
$Definition['MarketPlace']['Deliveries'] = 'Dodávky';
$Definition['MarketPlace']['noRoute'] = 'Nenalezena žádná obchodní trasa.';
$Definition['MarketPlace']['Create new trade route'] = 'Vytvořit novou obchodní trasu';
$Definition['MarketPlace']['notEnoughMerchants'] = "Nedostatek obchodníků. Je potřeba [MERCHANTS_NEEDED] obchodníků, ale k dispozici je jen [MERCHANTS_AVAILABLE].";
$Definition['MarketPlace']['tradeRouteDesc'] = 'Zlatý klub ti umožní nastavit obchodní trasy, které automaticky posílají suroviny mezi tvými vesnicemi.';
$Definition['MarketPlace']['Enabled'] = 'Zapnuto';
$Definition['MarketPlace']['needToBeActive'] = 'Pro použití této funkce musíš aktivovat Zlatý klub!';
$Definition['MarketPlace']['Click here to exchange resources'] = 'Klikni sem a vyměň suroviny.';
$Definition['MarketPlace']['Trade your village\'s resources immediately with NPC merchant 1:1'] = 'Okamžitě vyměň suroviny vesnice s NPC obchodníkem v poměru 1:1';
$Definition['MarketPlace']['Send time'] = 'Čas odeslání';
$Definition['MarketPlace']['every %s minutes'] = 'každých %s minut';
$Definition['MarketPlace']['every %s hours'] = 'každých %s hodin';
$Definition['MarketPlace']['every %s days'] = 'každých %s dní';
$Definition['MarketPlace']['You are under protection'] = 'Jsi pod ochranou.';

//Medals
$Definition['Medals']['Category'] = 'Kategorie';
$Definition['Medals']['Week'] = 'Týden';
$Definition['Medals']['Rank'] = 'Pořadí';
$Definition['Medals']['Resources'] = 'Surovinové';
$Definition['Medals']['Points'] = 'Body';
$Definition['Medals']['Received in week:'] = 'Získáno v týdnu:';
$Definition['Medals']['category15'] = 'Tato medaile ukazuje, že jsi byl %s× mezi 10 nejlepšími lupiči';
$Definition['Medals']['category9'] = 'Tato medaile ukazuje, že jsi byl %s× mezi 3 nejlepšími lupiči';
$Definition['Medals']['category14'] = 'Tato medaile ukazuje, že jsi byl %s× mezi 10 nejlepšími skokany.';
$Definition['Medals']['category8'] = 'Tato medaile ukazuje, že jsi byl %s× mezi 3 nejlepšími skokany.';
$Definition['Medals']['category7'] = 'Tato medaile ukazuje, že jsi byl %s× mezi 10 nejlepšími obránci.';
$Definition['Medals']['category13'] = 'Tato medaile ukazuje, že jsi byl %s× mezi 3 nejlepšími obránci.';
$Definition['Medals']['category12'] = 'Tato medaile ukazuje, že jsi byl %s× mezi 10 nejlepšími útočníky.';
$Definition['Medals']['category6'] = 'Tato medaile ukazuje, že jsi byl %s× mezi 3 nejlepšími útočníky.';
$Definition['Medals']['category5'] = 'Tato medaile ukazuje, že jsi byl %s× zároveň mezi nejlepšími útočníky i obránci.';
$Definition['Medals']['names'][1] = 'Útočníci týdne';
$Definition['Medals']['names'][2] = 'Obránci týdne';
$Definition['Medals']['names'][3] = 'Skokani týdne';
$Definition['Medals']['names'][4] = 'Lupiči týdne';
$Definition['Medals']['names'][5] = 'Top 10 útočníků a obránců týdne';
$Definition['Medals']['names'][6] = 'Top 3 útočníci';
$Definition['Medals']['names'][12] = 'Top 10 útočníků';
$Definition['Medals']['names'][7] = 'Top 3 obránci';
$Definition['Medals']['names'][13] = 'Top 10 obránců';
$Definition['Medals']['names'][8] = 'Top 3 skokani';
$Definition['Medals']['names'][14] = 'Top 10 skokanů';
$Definition['Medals']['names'][9] = 'Top 3 lupiči';
$Definition['Medals']['names'][15] = 'Top 10 lupičů';
$Definition['Medals']['SpecialMedals'] = [
    // Ranke Wonder Of World
    1 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Div světa<br>Jméno: %s<br>Národ: %s<br>Pořadí: 1<br>Úroveň divu světa: %s</div>',
    2 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Div světa<br>Jméno: %s<br>Národ: %s<br>Pořadí: 2<br>Úroveň divu světa: %s</div>',
    3 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Div světa<br>Jméno: %s<br>Národ: %s<br>Pořadí: 3<br>Úroveň divu světa: %s</div>',

    // Ranke Offensive 1 ta 4 top 10
    4 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Útok<br>Jméno: %s<br>Národ: %s<br>Pořadí: 1<br>Body: %s</div>',
    5 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Útok<br>Jméno: %s<br>Národ: %s<br>Pořadí: 2<br>Body: %s</div>',
    6 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Útok<br>Jméno: %s<br>Národ: %s<br>Pořadí: 3<br>Body: %s</div>',
    7 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Útok<br>Jméno: %s<br>Národ: %s<br>Pořadí: 4<br>Body: %s</div>',
    // Ranke Defensive 1 ta 4 top 10
    8 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Obrana<br>Jméno: %s<br>Národ: %s<br>Pořadí: 1<br>Body: %s</div>',
    9 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Obrana<br>Jméno: %s<br>Národ: %s<br>Pořadí: 2<br>Body: %s</div>',
    10 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Obrana<br>Jméno: %s<br>Národ: %s<br>Pořadí: 3<br>Body: %s</div>',
    11 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Obrana<br>Jméno: %s<br>Národ: %s<br>Pořadí: 4<br>Body: %s</div>',
    // Ranke Pop Nafarate Avale 1 ta 4
    12 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Obyvatelé <br>Jméno: %s<br>Národ: %s<br>Pořadí: 1<br>Body: %s</div>',
    13 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Obyvatelé <br>Jméno: %s<br>Národ: %s<br>Pořadí: 2<br>Body: %s</div>',
    14 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Obyvatelé <br>Jméno: %s<br>Národ: %s<br>Pořadí: 3<br>Body: %s</div>',
    15 => '<span class="gloriatitle">Národní šampionát</span><div class="gloriacontent">Země: %s<br>Server: %s<br>Kategorie: Obyvatelé <br>Jméno: %s<br>Národ: %s<br>Pořadí: 4<br>Body: %s</div>',
];
//Messages
$Definition['Messages']['You cannot send a message to more than %s users'] = 'Zprávu nemůžeš poslat více než %s uživatelům.';
$Definition['Messages']['spam_unread_protection'] = 'Ochrana proti spamu: Tento hráč má od tebe %s nepřečtených zpráv. Dokud je nepřečte, nemůžeš mu poslat další zprávu, nebo musíš počkat dvě hodiny.';
$Definition['Messages']['Current message has already been reported as: x'] = 'Tato zpráva už byla nahlášena jako: %s.';
$Definition['Messages']['Error: Very long subject!'] = 'Chyba: Příliš dlouhý předmět!';
$Definition['Messages']['Spam protection: Please wait for 10 minutes and try again'] = '<b>Ochrana proti spamu</b>: Počkej prosím 10 minut a zkus to znovu.';
$Definition['Messages']['You are currently not part of any alliance'] = 'Momentálně nejsi členem žádné aliance.';
$Definition['Messages']['Inadmissible message'] = 'Nepřípustná zpráva.';
$Definition['Messages']['Mark as read'] = 'Označit jako přečtené';
$Definition['Messages']['Mark as unread'] = 'Označit jako nepřečtené';
$Definition['Messages']['reportingSuccessful'] = 'Nahlášení bylo úspěšně odesláno.';
$Definition['Messages']['closeButtonText'] = 'Zavřít';
$Definition['Messages']['Messages'] = 'Zprávy';
$Definition['Messages']['Inbox'] = 'Doručené';
$Definition['Messages']['needClub'] = 'Pro tuto funkci potřebuješ aktivovaný Zlatý klub.';
$Definition['Messages']['Delete'] = 'Smazat';
$Definition['Messages']['Subject'] = 'Předmět';
$Definition['Messages']['Sender'] = 'Odesílatel';
$Definition['Messages']['Sent at'] = 'Odeslané';
$Definition['Messages']['Read'] = 'Přečtené';
$Definition['Messages']['Unread'] = 'Nepřečtené';
$Definition['Messages']['Your note is too long!'] = 'Tvá poznámka je příliš dlouhá!';
$Definition['Messages']['select all'] = 'vybrat vše';
$Definition['Messages']['Recipient'] = 'Příjemce';
$Definition['Messages']['Choose reason'] = 'Vyber důvod.';
$Definition['Messages']['Advertisement'] = 'Reklama';
$Definition['Messages']['harassment'] = 'obtěžování';
$Definition['Messages']['gold'] = 'Nákup zlata';
$Definition['Messages']['Other'] = 'Ostatní';
$Definition['Messages']['report'] = 'Hlášení';
$Definition['Messages']['Attention: Misuse of the report function is punishable'] = 'Pozor: Zneužití funkce nahlášení je trestné.';
$Definition['Messages']['sender'] = 'Odesílatel';
$Definition['Messages']['Report as spam'] = 'Nahlásit jako spam';
$Definition['Messages']['back to send box'] = 'zpět do doručených';
$Definition['Messages']['There are no messages available in the sentbox'] = 'V odeslaných nejsou žádné zprávy.';
$Definition['Messages']['There are no messages available in the inbox'] = 'V doručených nejsou žádné zprávy.';
$Definition['Messages']['There are no messages available in the archive'] = 'V archivu nejsou žádné zprávy.';
$Definition['Messages']['waiting for confirmation'] = 'čeká na potvrzení';
$Definition['Messages']['Recover'] = 'Obnovit';
$Definition['Messages']['Addressbook'] = 'Adresář';
$Definition['Messages']['send'] = 'odeslat';
$Definition['Messages']['Alliance'] = 'Aliance';
$Definition['Messages']['xxx wrote:'] = '%s napsal:';
$Definition['Messages']['You dont have permission here'] = 'Sem nemáš přístup';
$Definition['Messages']['player x does not exists'] = 'Jméno %s neexistuje.';
$Definition['Messages']['you send your last message in x and you will be able to send message in y'] = 'Poslední zprávu jsi poslal v %s a další budeš moci poslat v %s.';
$Definition['Messages']['no subject'] = 'Bez předmětu';
$Definition['Messages']['ConfirmDelete'] = 'Opravdu chceš tuto zprávu smazat?';
$Definition['Messages']['answer'] = 'Odpovědět';
$Definition['Messages']['Player'] = 'Hráč';
$Definition['Messages']['Coordinates'] = 'Souřadnice';
$Definition['Messages']['report'] = 'Hlášení';
$Definition['Messages']['Troops'] = 'Jednotky';
$Definition['Messages']['preview'] = 'Náhled';
$Definition['Messages']['back to the inbox'] = 'zpět do doručených';
$Definition['Messages']['Write'] = 'Napsat';
$Definition['Messages']['Sent'] = 'Odeslané';
$Definition['Messages']['- saved -'] = '- Uloženo -';
$Definition['Messages']['Archive'] = 'Archiv';
$Definition['Messages']['Notes'] = 'Poznámky';
$Definition['Messages']['Ignored players'] = 'Ignorovaní hráči';
$Definition['Messages']['online now'] = 'Právě online';
$Definition['Messages']['active players'] = 'max. 10 h';
$Definition['Messages']['active 3days'] = 'max. 3 dny';
$Definition['Messages']['active 7days'] = 'max. 7 dní';
$Definition['Messages']['inactive'] = 'neaktivní';
$Definition['Messages']['Ambassador'] = 'Velvyslanec';
$Definition['Messages']['Alliance Invitation received'] = 'Přijata pozvánka do aliance';
$Definition['Messages']['Alliance Invitation revoked'] = 'Pozvánka do aliance byla stažena';
$Definition['Messages']['Spam protection: You must have at least %s pop to be able to send messages'] = 'Ochrana proti spamu: Pro posílání zpráv musíš mít alespoň %s obyvatel';
$Definition['Messages']['AllianceInvitationRevokeText'] = <<<HTML
Hello %s,
<br /><br />
your invitation to join the alliance %s has been revoked by %s.
<br /><br />
Have fun and good luck!
HTML;
$Definition['Messages']['AllianceInvitationReceiveText'] = <<<HTML
Hello %s,
<br /><br />
you are invited by [player]%s[/player] to join the alliance [alliance]%s[/alliance].
<br /><br />
To accept the invitation go to your embassy and join the alliance. If you don't have an embassy yet, build one first.
<br /><br />
Have fun and good luck.
HTML;
$Definition['Messages']['WrittenBySitter%s'] = '- Napsal zástupce %s -';
//NotFound
$Definition['notFound']['title'] = 'Nic tu není!';
$Definition['notFound']['desc'] = 'Hledali jsme už 404krát, ale nic jsme nenašli';
//NPC
$Definition['npc'] = [
    "disabled_in_ww" => "Tuto funkci nelze použít ve vesnicích s divem světa.",
    'npc_desc' => 'S NPC obchodníkem můžeš suroviny ve skladu rozdělit podle libosti.<br><br>První řádek ukazuje současnou zásobu. Ve druhém řádku můžeš zvolit jiné rozdělení. Třetí řádek ukazuje rozdíl mezi starou a novou zásobou.',
    'sum' => 'Součet',
    'distribute_remaining_resources' => 'Rozdělit zbývající suroviny.',
    'exchangeResources' => 'Směnit suroviny',
    'redeem_now' => 'Uplatnit hned',
    'redeem' => 'Uplatnit',
    'remain' => 'zbývá',
    "build_marketPlace" => "Postav tržiště",
];
//Options
$Definition['Options']['Change name'] = 'Změnit jméno';
$Definition['Options']['Yes'] = 'Ano';
$Definition['Options']['No'] = 'Č.';
$Definition['Options']['You need to wait 7 days before deletion'] = 'Zlato teď bohužel nemůžeš převést. S dotazy piš na plus@' . (WebService::getJustDomain()) . '.';
$Definition['Options']['Newsletter'] = 'Zpravodaj';
$Definition['Options']['sitter'] = 'Zástupce';
$Definition['Options']['Sitter(s) for this account'] = 'Zástupci tohoto účtu';
$Definition['Options']['Sitting for other account(s)'] = 'Zastupuješ jiné účty';
$Definition['Options']['Sitting for other account(s)Desc'] = 'Jsi nastaven jako zástupce těchto účtů. Kliknutím na červené X se můžeš jako zástupce odebrat.';
$Definition['Options']['MySittersDesc'] = 'Zástupce se může přihlásit do tvého účtu tvým jménem a svým heslem. Můžeš mít nejvýše dva zástupce. ';
$Definition['Options']['no entry'] = 'Žádný záznam';
$Definition['Options']['Here you can subscribe or unsubscribe to our newsletter'] = 'Zde se můžeš přihlásit k odběru našeho zpravodaje nebo se odhlásit.';
$Definition['Options']['Travian Games'] = 'Travian Games';
$Definition['Options']['invalid old email'] = 'Starý e-mail je neplatný.';
$Definition['Options']['invalid new email'] = 'Nová e-mailová adresa je neplatná!';
$Definition['Options']['Receive notifications about invitations to an alliance'] = 'Dostávat upozornění na pozvánky do aliance';
$Definition['Options']['new email exists'] = 'nový e-mail už existuje.';
$Definition['Options']['Name black listed'] = 'Nové jméno účtu už existuje nebo není povoleno. Zkus prosím jiné jméno.';
$Definition['Options']['Name exists'] = 'Nové jméno účtu už existuje nebo není povoleno. Zkus prosím jiné jméno.';
$Definition['Options']['Name too short'] = 'Jméno je příliš krátké.';
$Definition['Options']['Name too long'] = 'Jméno je příliš dlouhé.';
$Definition['Options']['Please enter a new account name and confirmation password'] = 'Zadej nové jméno účtu a heslo pro potvrzení.';
$Definition['Options']['Confirmation password does not match'] = 'Heslo pro potvrzení nesouhlasí.';
$Definition['Options']['Confirm with password'] = 'Potvrdit heslem:';
$Definition['Options']['Change account name'] = 'Změnit jméno účtu';
$Definition['Options']['Number of changes'] = 'Počet změn:';
$Definition['Options']['Enter new account name'] = 'Zadej nové jméno účtu';
$Definition['Options']['changeNameDesc'] = 'Zde můžeš změnit jméno svého účtu. Jméno nesmí porušovat pravidla hry ani obchodní podmínky. Jméno můžeš zdarma změnit %s× nebo dokud nedosáhneš %s obyvatel. Potom stojí změna jména %s zlata. Při více než %s obyvatelích změna jména není možná!';
$Definition['Options']['Change password'] = 'Změnit heslo';
$Definition['Options']['Old Password'] = 'Staré heslo';
$Definition['Options']['New Password'] = 'Nové heslo:';
$Definition['Options']['confirm'] = 'potvrdit';
$Definition['Options']['Change email'] = 'Změnit e-mail';
$Definition['Options']['Old email'] = 'Starý e-mail';
$Definition['Options']['New email'] = 'Nový e-mail';
$Definition['Options']['Change email desc'] = 'Zadej starou a novou e-mailovou adresu. Na obě adresy pak dostaneš ověřovací kód, který musíš zadat sem.';
$Definition['Options']['Game'] = 'Hra';
$Definition['Options']['Account'] = 'Účet';
$Definition['Options']['Delete account'] = 'Smazat účet';
$Definition['Options']['Delete account?'] = 'Smazat účet? ';
$Definition['Options']['Sitter'] = 'Zástupce';
$Definition['Options']['Preferences'] = 'Předvolby';
$Definition['Options']['Report filter'] = 'Filtr hlášení';
$Definition['Options']['Auto-complete'] = 'Automatické doplňování';
$Definition['Options']['No reports about transfers between your own villages'] = 'Žádná hlášení o přesunech mezi vlastními vesnicemi.';
$Definition['Options']['No reports about transfers to foreign villages'] = 'Žádná hlášení o přesunech do cizích vesnic.';
$Definition['Options']['No reports about transfers from foreign villages'] = 'Žádná hlášení o přesunech z cizích vesnic.';
$Definition['Options']['The LowRes version does not display images in reports'] = 'Verze LowRes nezobrazuje v hlášeních obrázky.';
$Definition['Options']['Complete for rally point and marketplace'] = 'Doplňovat na shromaždišti a tržišti:';
$Definition['Options']['Own villages'] = 'Vlastní vesnice ';
$Definition['Options']['Nearby villages'] = 'Vesnice v okolí';
$Definition['Options']["Alliance members' villages"] = 'Vesnice členů aliance';
$Definition['Options']["Display"] = 'Zobrazení';
$Definition['Options']["Don't display images in reports"] = 'Nezobrazovat obrázky v hlášeních.';
$Definition['Options']["Messages and reports per page"] = 'Zpráv a hlášení na stránku';
$Definition['Options']["Troop movements per page in rally point"] = 'Pohybů jednotek na stránku na shromaždišti:';
$Definition['Options']["Time zone preferences"] = 'Nastavení časového pásma';
$Definition['Options']["You can change your time zone here"] = 'Zde můžeš změnit časové pásmo.';
$Definition['Options']["Time zone"] = 'Časové pásmo:';
$Definition['Options']["Date format"] = 'Formát data:';
$Definition['Options']["local time zones"] = 'místní časová pásma';
$Definition['Options']["general time zones"] = 'obecná časová pásma';
$Definition['Options']["change_mail_desc"] = 'Na starou i novou e-mailovou adresu byl odeslán ověřovací kód. Oba zadej do správných polí zde.';
$Definition['Options']["Old email code"] = 'Kód ze starého e-mailu';
$Definition['Options']["New email code"] = 'Kód z nového e-mailu';
$Definition['Options']["x of y changes"] = '%s z %s změn ';
$Definition['Options']["password wrong"] = 'Heslo je špatné.';
$Definition['Options']["deleteDesc"] = 'Zde můžeš smazat svůj účet. Po zahájení mazání trvá hodinu, než je účet smazán. Mazání můžeš kdykoli zrušit kliknutím na červené X. Účet, který se maže, už nemá přístup k aukcím ani ke směnárně!';
$Definition['Options']["codes are not correct"] = 'Neplatné kódy!';
$Definition['Options']["email_changed"] = 'E-mail změněn';
$Definition['Options']["Email change is in progress"] = 'Probíhá změna e-mailu. ';
$Definition['Options']["EmailChange_new_subject"] = 'Změna e-mailu účtu Travian, část 2';
$Definition['Options']["EmailChange_new"] = '
<div style="text-align: left; direction: LTR">
Ahoj %s,

tuto e-mailovou adresu jsi zvolil jako nový e-mail svého účtu Travian
ve světě comx. Zadej prosím následující kód do pole "Kód nové adresy"
ve svém profilu na záložce "účet":

Kód: %s

Na starou adresu ti přijde další e-mail s druhým kódem, který
zadej do pole "Kód staré adresy".
</div>
';
$Definition['Options']["EmailChange_old_subject"] = 'Změna e-mailu účtu Travian, část 1';
$Definition['Options']["EmailChange_old"] = '
<div style="text-align: left; direction: LTR">
Ahoj %s,

vyžádal sis změnu tohoto e-mailu u svého účtu Travian ve světě
comx.
Zadej prosím následující kód do pole "Kód staré adresy" ve svém profilu na
záložce "účet":

Kód: %s

Na novou adresu ti přijde další e-mail s druhým kódem, který
zadej do pole "Kód nové adresy".
</div>';
$Definition['Options']['Vacation'] = 'Dovolená';
$Definition['Options']['Use the vacation to protect your villages while being abroad'] = 'Dovolenou použij k ochraně svých vesnic, když jsi pryč.';
$Definition['Options']['While in vacation mode the following actions will be deactivated'] = 'V dovolenkovém režimu budou vypnuty tyto akce';
$Definition['Options']['send or receive troops'] = 'posílání a přijímání jednotek';
$Definition['Options']['start new building orders'] = 'zahájení nové stavby';
$Definition['Options']['using the marketplace'] = 'používání tržiště';
$Definition['Options']['start training troops'] = 'zahájení výcviku jednotek';
$Definition['Options']['join alliances'] = 'vstup do aliance';
$Definition['Options']['delete your account'] = 'smazání účtu';
$Definition['Options']['Alliance settings'] = 'Nastavení aliance';
$Definition['Options']['Show alliance news'] = 'Zobrazovat novinky aliance';
$Definition['Options']['Alliance members founded new village'] = 'Člen aliance založil novou vesnici';
$Definition['Options']['New alliance member joined'] = 'Do aliance vstoupil nový člen';
$Definition['Options']['Player has been invited'] = 'Hráč byl pozván';
$Definition['Options']['Player has left alliance'] = 'Hráč opustil alianci';
$Definition['Options']['Player was kicked from alliance'] = 'Hráč byl z aliance vyhozen';
$Definition['Options']['There are no outgoing troops'] = 'Nemáš žádné odcházející jednotky';
$Definition['Options']['There are no incoming troops'] = 'Nemáš žádné přicházející jednotky';
$Definition['Options']['There are no troops reinforing other players'] = 'Žádné jednotky neposilují jiné hráče';
$Definition['Options']['No other player reinforces you'] = 'Žádný jiný hráč tě neposiluje';
$Definition['Options']['You dont own a Wonder of the World Village'] = 'Nevlastníš vesnici s divem světa';
$Definition['Options']['You dont own an artifact'] = 'Nevlastníš žádný artefakt';
$Definition['Options']['You dont have beginners protection left'] = 'Ochrana nováčka ti už nezbývá';
$Definition['Options']['There are no troops in your traps'] = 'V tvých pastích nejsou žádné jednotky';
$Definition['Options']['x/9 conditions met'] = 'Splněno %s/9 podmínek';
$Definition['Options']['Available Days x/y'] = 'Dostupné dny %s/%s';
$Definition['Options']['How many days should the vacation mode last'] = 'Kolik dní má dovolenkový režim trvat';
$Definition['Options']['Vacation til'] = 'Dovolená do';
$Definition['Options']['Your account is not in deletion'] = 'Tvůj účet se nemaže';
$Definition['Options']['enter vacation mode now'] = 'zapnout dovolenkový režim hned';
$Definition['Options']['Vacation mode is active'] = 'Dovolenkový režim je aktivní';
$Definition['Options']['Are you sure you want to enter vacation mode?'] = 'Opravdu chceš zapnout dovolenkový režim?';
$Definition['Options']['Your account will be protected, and the resource production will continue'] = 'Tvůj účet bude chráněn a produkce surovin bude pokračovat.';
$Definition['Options']['So will the crop consumption by troops too, which may lead to troop starvation'] = 'Stejně tak bude pokračovat spotřeba obilí jednotkami, což může vést k vyhladovění jednotek.';
$Definition['Options']['Your are not able to'] = 'Nebudeš moci';
$Definition['Options']['upgrade buildings'] = 'rozšiřovat budovy';
$Definition['Options']['train troops'] = 'cvičit jednotky';
$Definition['Options']['send troops'] = 'posílat jednotky';
$Definition['Options']['send merchants'] = 'posílat obchodníky';
$Definition['Options']['delete your account'] = 'smazání účtu';
$Definition['Options']['Remaining days in vacation mode'] = 'Zbývající dny dovolenkového režimu';
$Definition['Options']['day(s)'] = 'den/dny';
$Definition['Options']['You can abort vacation mode now'] = 'Dovolenkový režim teď můžeš ukončit';
$Definition['Options']['abort vacation mode'] = 'ukončit dovolenkový režim';
$Definition['Options']['Vacation mode runs til'] = 'Dovolenkový režim trvá do';
$Definition['Options']['player name'] = 'jméno hráče';
$Definition['Options']['Send raids'] = 'Posílat loupeže';
$Definition['Options']['Send reinforcements to other players'] = 'Posílat posily jiným hráčům';
$Definition['Options']['Send resources to other players'] = 'Posílat suroviny jiným hráčům';
$Definition['Options']['Buy and spend Gold'] = 'Kupovat a utrácet zlato';
$Definition['Options']['Delete and archive messages and reports'] = 'Číst a posílat zprávy';
$Definition['Options']['Contribute resources to alliance bonuses'] = 'Přispívat surovinami do bonusů aliance';
$Definition['Options']['Read and send messages'] = 'Mazat a archivovat zprávy a hlášení';
$Definition['Options']['language settings'] = 'nastavení jazyka';
$Definition['Options']['Language'] = 'Jazyk';
$Definition['Options']['Support colorBlind title'] = 'Podpora pro barvoslepé';
$Definition['Options']['Use colorBlind?'] = 'Použít podporu pro barvoslepé?';
$Definition['Options']['Support colorBlind desc'] = 'Můžeš zapnout podporu pro barvoslepé s optimalizovanými ikonami a vylepšenými barvami. Tento režim nemění žádnou herní mechaniku.';
$Definition['Options']['Transfer gold'] = 'Převést zlato';
$Definition['Options']['You have Gold %s pieces of gold, %s pieces can be transferred after deleting your account!'] = 'Máš %s zlatých. Po smazání účtu lze převést %s zlatých!';
$Definition['Options']['Auto reload status'] = 'Nastavení automatického obnovení';
$Definition['Options']['Enable auto reload?'] = 'Zapnout automatické obnovení?';
$Definition['Options']['Use fast upgrade on buildings'] = 'Používat rychlé rozšiřování budov';
$Definition['Options']['This player is sitter for 2 players'] = 'Tento hráč je zástupcem 2 hráčů.';
$Definition['Options']['You can set the page to auto reload, the page will be automatically refreshed when timer reaches 0'] = 'Můžeš zapnout automatické obnovení stránky, stránka se obnoví, jakmile časovač dosáhne 0.';

$Definition['Options']['Graphic pack'] = 'Grafický balíček';
$Definition['Options']['You can change the way the game looks for you'] = 'Můžeš změnit, jak hra vypadá.';
$Definition['Options']['new'] = 'Nové';


//PaymentWizard
$Definition['PaymentWizard']['You have a WW village or Artifact So you cannot use this feature'] = 'Máš vesnici s divem světa nebo artefakt, proto tuto funkci nemůžeš použít.';
$Definition['PaymentWizard']['Gold'] = 'Zlato';
$Definition['PaymentWizard']['goldClub'] = 'Zlatý klub';
$Definition['PaymentWizard']['x gold moneyUnit'] = '%s zlata %s %s';
$Definition['PaymentWizard']['paymentUnAvailable'] = 'Platba momentálně není dostupná.';
$Definition['PaymentWizard']['location'] = 'Země';
$Definition['PaymentWizard']['ChangeLocation'] = 'Změnit zemi';
$Definition['PaymentWizard']['Package'] = 'Balíček';
$Definition['PaymentWizard']['ChoosePackage'] = 'Vyber balíček';
$Definition['PaymentWizard']['changePackage'] = 'Změnit balíček';
$Definition['PaymentWizard']['BuySettings'] = 'Nastavení nákupu';
$Definition['PaymentWizard']['hour'] = 'hod.';
$Definition['PaymentWizard']['Not Available yet'] = 'Zatím nedostupné';
$Definition['PaymentWizard']['All displayed prices are final prices'] = 'Všechny zobrazené ceny jsou konečné.';
$Definition['PaymentWizard']['You can check the status of your order at any time'] = 'Stav své objednávky můžeš kdykoli zkontrolovat.';
$Definition['PaymentWizard']['Show open orders'] = 'Zobrazit otevřené objednávky';
$Definition['PaymentWizard']['Hide open orders'] = 'Skrýt otevřené objednávky';
$Definition['PaymentWizard']['Payment options:'] = 'Způsoby platby:';
$Definition['PaymentWizard']['Show payment methods'] = 'Zobrazit způsoby platby';
$Definition['PaymentWizard']['Buy gold'] = 'Koupit zlato';
$Definition['PaymentWizard']['Advantages'] = 'Výhody';
$Definition['PaymentWizard']['Plus Support'] = 'Podpora Plus';
$Definition['PaymentWizard']['Earn Gold'] = 'Získat zlato';
$Definition['PaymentWizard']['Delivery:'] = 'Doručení:';
$Definition['PaymentWizard']['Select payment method'] = 'Vyber způsob platby';
$Definition['PaymentWizard']['Step3-ChoosePayment'] = 'Krok 3 – výběr platby';
$Definition['PaymentWizard']['step'] = 'Krok';
$Definition['PaymentWizard']['notEnoughGoldForThisOption'] = 'Na použití této funkce nemáš dost zlata!';
$Definition['PaymentWizard']['ChooseAnotherPackage'] = 'Vybrat jiný balíček';
$Definition['PaymentWizard']['BuyNow'] = 'Koupit teď?';
$Definition['PaymentWizard']['Gold'] = 'Zlato';
$Definition['PaymentWizard']['Please activate advantage that you can choose'] = 'Vyber, kterou výhodu chceš odemknout:';
$Definition['PaymentWizard']['ToTheEndOfTheGame'] = 'celé herní kolo';
$Definition['PaymentWizard']['Days remaining'] = 'Zbývající dny:';
$Definition['PaymentWizard']['until'] = 'do';
$Definition['PaymentWizard']['EndsAtX'] = 'Končí %s.';
$Definition['PaymentWizard']['Bonus duration'] = 'Trvání bonusu';
$Definition['PaymentWizard']['Days'] = 'Dny';
$Definition['PaymentWizard']['Payment rules'] = 'Pravidla plateb';
$Definition['PaymentWizard']['PaymentRulesHTML'] = '<div style="width: 400px; font-size: 13px;"><h1 style="color: red; text-align: center;">Pravidla nákupu zlata</h1><h2>Pravidla pro nákup zlata</h2><h3>1. Automatický systém</h3>Náš platební systém je automatický a po úspěšném nákupu se zlato automaticky připíše na tvůj účet<h3>2. Systém ti nepřipsal zlato?</h3>Někdy se systém zasekne nebo platební brána musí informace ověřit, než nám dá vědět, a zlato se na účet nepřevede! V takovém případě nemůžeš žádat vrácení peněz!! Stačí poslat zprávu Multihunterovi a zlato ti co nejdřív připíšeme.<h3 style="color: red;">Důležité:</h3><p>Pokud zlato koupíš, hraješ s ním a pak požádáš PayPal o vrácení peněz, nemůžeme to akceptovat a tvůj účet bude zablokován!</p><h3 style="color: red;">Důležité:</h3>Pokud náš systém vypadne, dáme ti zlato navíc nebo jinou kompenzaci, abychom tě potěšili a vrátili do hry.<h3 style="color: red;">Důležité:</h3>Z žádného důvodu nemůžeš žádat vrácení peněz od nás ani od <b>PayPalu</b>.<h3 style="color: red;">Důležité:</h3>Pokud bude náš systém mimo provoz déle než 1 den, zlato ti vrátíme</p><p>Tato pravidla si <b>musíš</b> přečíst před nákupem zlata, nákupem zlata s nimi souhlasíš!</div>';
$Definition['PaymentWizard']['Extend automatically'] = 'Prodlužovat automaticky';
$Definition['PaymentWizard']['Activated To End Of the game'] = 'Aktivováno do <b>konce hry</b>';
$Definition['PaymentWizard']['GoldClub'] = 'Zlatý klub';
$Definition['PaymentWizard']['goldClubDesc'] = '<p>Dává výhody na celé herní kolo!</p>
<p>Umožní tvým obchodníkům automaticky posílat suroviny opakovaně, rychleji najít na mapě pole se zvýšenou produkcí obilí a archivovat zprávy a hlášení. Používej seznam farem ke správě útoků a nech své jednotky uniknout při útoku na hlavní město!</p>';
$Definition['PaymentWizard']['Plus'] = 'Plus';
$Definition['PaymentWizard']['PlusDesc'] = ' <p>Po zobrazenou dobu poskytuje lepší přehled a další výhody!</p>
                <p>Přizpůsob si hru svému stylu pomocí přímých odkazů a využij lepší přehled velké mapy. Dostávej varování před útoky a podrobné informace v přehledu vesnic. Nech obchodníky automaticky zopakovat dodávku a zařaď do fronty další stavební zakázku!</p>';
$Definition['PaymentWizard']['+25Wood'] = '+25 % produkce dřeva';
$Definition['PaymentWizard']['+25WoodDesc'] = ' <p>Po zobrazenou dobu zvyšuje produkci vybrané suroviny ve všech tvých vesnicích o 25 %.</p>
                <p>Bonus 25 % se nevztahuje jen na základní produkci suroviny, ale i na všechny ostatní bonusy!</p>';
$Definition['PaymentWizard']['+25Clay'] = '+25 % produkce hlíny';
$Definition['PaymentWizard']['+25ClayDesc'] = '<p>Po zobrazenou dobu zvyšuje produkci vybrané suroviny ve všech tvých vesnicích o 25 %.</p>
                <p>Bonus 25 % se nevztahuje jen na základní produkci suroviny, ale i na všechny ostatní bonusy!</p>';
$Definition['PaymentWizard']['+25Iron'] = '+25 % produkce železa';
$Definition['PaymentWizard']['+25IronDesc'] = ' <p>Po zobrazenou dobu zvyšuje produkci vybrané suroviny ve všech tvých vesnicích o 25 %.</p>
                <p>Bonus 25 % se nevztahuje jen na základní produkci suroviny, ale i na všechny ostatní bonusy!</p>';
$Definition['PaymentWizard']['+25Crop'] = '+25 % produkce obilí';
$Definition['PaymentWizard']['+25CropDesc'] = '<p>Po zobrazenou dobu zvyšuje produkci vybrané suroviny ve všech tvých vesnicích o 25 %.</p>
                <p>Bonus 25 % se nevztahuje jen na základní produkci suroviny, ale i na všechny ostatní bonusy!</p>';
$Definition['PaymentWizard']['Travian Answers'] = 'Travian Odpovědi';
$Definition['PaymentWizard']['Recipient [RECEIVER_COUNT]:'] = 'Příjemce [RECEIVER_COUNT]:';
$Definition['PaymentWizard']['mentor'] = 'Rádce';
$Definition['PaymentWizard']['How can we help?'] = 'Jak můžeme pomoci?';
$Definition['PaymentWizard']['Plus FAQ'] = 'Časté dotazy k Plus';
$Definition['PaymentWizard']['Plus FAQ Desc'] = 'Pokud máš dotazy k převodu zlata, k funkcím Plus nebo k nákupu zlata, pomůže ti náš nástroj podpory "Odpovědi".';
$Definition['PaymentWizard']['Contact Plus Support'] = 'Kontaktovat podporu Plus';
$Definition['PaymentWizard']['Plus Support'] = 'Podpora Plus';
$Definition['PaymentWizard']['Plus Support Desc'] = 'Máš problém a nemůžeš najít odpověď? Zde můžeš kontaktovat naši podporu Plus.';
$Definition['PaymentWizard']['Miscellaneous information'] = 'Další informace';
$Definition['PaymentWizard']['Miscellaneous information warning'] = 'Pokud chceš smazat účet, je to možné až 7 dní po posledním nákupu nebo převodu zlata.';
$Definition['PaymentWizard']['How can I invite players?'] = 'Jak mohu zvát hráče?';
$Definition['PaymentWizard']['Players invited so far'] = 'Dosud pozvaní hráči';
$Definition['PaymentWizard']['If you invite players to open an account in Travian on this server, you can receive Gold as a reward, You can use this Gold to purchase a Plus Account or other Gold advantages'] = 'Když pozveš hráče, aby si na tomto serveru založili účet v Travianu, můžeš jako odměnu získat zlato. Za toto zlato si můžeš koupit PLUS účet nebo jiné výhody.';
$Definition['PaymentWizard']['To bring in new players, you can invite them by email or have them click on your REF link'] = 'Nové hráče můžeš pozvat e-mailem nebo jim dát kliknout na svůj REF odkaz.';
$Definition['PaymentWizard']['As soon as an invited player has reached x villages'] = 'Jakmile pozvaný hráč dosáhne <span class="amount">2</span> vesnic, dostaneš <span class="goldReward"><img src="img/x.gif" class="gold" alt="Zlato"> <span class="amount">' . Config::getProperty("gold", "invitePlayerGold") . '.</span></span>';
$Definition['PaymentWizard']['back to overview'] = 'zpět na přehled';
$Definition['PaymentWizard']['Choose an option to earn gold'] = 'Vyber způsob, jak získat zlato';
$Definition['PaymentWizard']['Send link to friends'] = 'Poslat odkaz přátelům';
$Definition['PaymentWizard']['You can send a link to your friends via email, inviting them to Travian'] = 'Přátelům můžeš e-mailem poslat odkaz s pozvánkou do Travianu.';
$Definition['PaymentWizard']['Send email to friends'] = 'Poslat e-mail přátelům';
$Definition['PaymentWizard']['Your personal referral link'] = 'Tvůj osobní odkaz pro doporučení';
$Definition['PaymentWizard']['Display a list of all the players you have invited so far'] = 'Zobrazit seznam všech hráčů, které jsi dosud pozval';
$Definition['PaymentWizard']['Display list of all invited players'] = 'Zobrazit seznam všech pozvaných hráčů';
$Definition['PaymentWizard']['Please enter recipients email addresses'] = 'Zadej e-mailové adresy příjemců';
$Definition['PaymentWizard']['Recipient 1'] = 'Příjemce 1';
$Definition['PaymentWizard']['Recipient 2'] = 'Příjemce 2';
$Definition['PaymentWizard']['Add more people'] = 'Přidat další';
$Definition['PaymentWizard']['Add a personal message (optional)'] = 'Přidej osobní zprávu (nepovinné)';
$Definition['PaymentWizard']['Cancel'] = 'Zrušit';
$Definition['PaymentWizard']['Send invitation'] = 'Poslat pozvánku';

$Definition['PaymentWizard']['INVITE_EMAIL_MESSAGE'] = 'Ahoj,

hráč [PLAYERNAME] ([PLAYEREMAIL]) tě zve,
abyste spolu hráli strategickou prohlížečovou hru Travian.
[PLAYERNAME] hraje ve světě [GAMEWORLD] za národ [TRIBE].

Zaregistrovat se můžeš přes tento odkaz:
<a href="[INVITE_LINK]">[INVITE_LINK]</a>

----------

[CUSTOM_MESSAGE]';
$Definition['PaymentWizard']['You have not brought in any new players yet'] = 'Zatím jsi nepřivedl žádné nové hráče.';
$Definition['PaymentWizard']['INVITE_EMAIL_SUBJECT'] = 'Travian: Pozvánka od hráče [PLAYERNAME]';
$Definition['PaymentWizard']['Number of successfully sent invitations: x'] = 'Počet úspěšně odeslaných pozvánek: %s';
$Definition['PaymentWizard']['OpenOffers'] = 'Otevřené nabídky';
$Definition['PaymentWizard']['Order Date'] = 'Datum objednávky';
$Definition['PaymentWizard']['Payment'] = 'Platba';
$Definition['PaymentWizard']['Booking'] = 'Zaúčtování';
$Definition['PaymentWizard']['Presenter'] = 'Doporučující';
$Definition['PaymentWizard']['Units'] = 'Jednotky';
$Definition['PaymentWizard']['Price'] = 'Cena';
$Definition['PaymentWizard']['Pending'] = 'Čeká';
$Definition['PaymentWizard']['Success'] = 'Úspěch';
$Definition['PaymentWizard']['Success2'] = 'Selhalo';
$Definition['PaymentWizard']['Cancelled'] = 'Zrušeno';
$Definition['PaymentWizard']['booked'] = 'Zaúčtováno';
$Definition['PaymentWizard']['not booked'] = 'Nezaúčtováno';
$Definition['PaymentWizard']['no Open Orders'] = 'Žádné otevřené objednávky.';
$Definition['PaymentWizard']['AccountDeletionErr'] = 'Účet se maže.<br>Nelze kupovat zlato.<br><br>Bohužel je to nutné kvůli prevenci platebních podvodů.';
$Definition['PaymentWizard']['The payment system is not available'] = 'Platební systém není dostupný.';
$Definition['PaymentWizard']['An error occurred The payment system is not available at the moment Please try again later'] = 'Došlo k chybě. Platební systém momentálně není dostupný. Zkus to prosím později.';
$Definition['PaymentWizard']['World'] = 'Svět';
$Definition['PaymentWizard']['UID'] = 'UID';
$Definition['PaymentWizard']['Member since'] = 'Členem od';
$Definition['PaymentWizard']['Inhabitants'] = 'Obyvatelé';
$Definition['PaymentWizard']['Villages'] = 'Vesnice';
$Definition['PaymentWizard']['paymentFeatures'] = 'Funkce Plus';
$Definition['PaymentWizard']['Troops'] = 'Jednotky';
$Definition['PaymentWizard']['buyAnimal'] = 'Koupit zvířata';
$Definition['PaymentWizard']['General'] = 'Obecné';
$Definition['PaymentWizard']['GeneralOptions'] = 'Obecné možnosti';
$Definition['PaymentWizard']['buyTroops'] = 'Koupit jednotky';
$Definition['PaymentWizard']['buyResources'] = 'Koupit suroviny';
$Definition['PaymentWizard']['WWDisabled'] = 'Tuto funkci nelze použít ve vesnici s divem světa.';
$Definition['PaymentWizard']['Buy'] = 'Koupit';
$Definition['PaymentWizard']['delivery'] = 'Doručení';
$Definition['PaymentWizard']['Immediately'] = 'Okamžitě';
$Definition['PaymentWizard']['Minutes'] = 'min';
$Definition['PaymentWizard']['rallyPointTo20'] = 'Postavit shromaždiště úrovně 20';
$Definition['PaymentWizard']['rallyPointTo20Desc'] = 'Postaví v aktuální vesnici shromaždiště úrovně 20.';
$Definition['PaymentWizard']['oneHourOfProduction'] = 'Koupit hodinovou produkci surovin vesnice';
$Definition['PaymentWizard']['oneHourOfProductionDesc'] = 'Hodinová produkce surovin bude připsána do aktuální vesnice.';
$Definition['PaymentWizard']['finishTraining'] = 'Okamžitě dokončit výcvik všech jednotek';
$Definition['PaymentWizard']['finishTrainingDesc'] = 'Všechny jednotky ve frontě výcviku v aktuální vesnici budou vycvičeny okamžitě.';
$Definition['PaymentWizard']['moreProtection'] = 'Ochrana';
$Definition['PaymentWizard']['moreProtectionDesc'] = 'Se zapnutou ochranou na tebe nikdo nemůže zaútočit, když jsi pryč.';
$Definition['PaymentWizard']['fasterTraining'] = 'o %s%% rychlejší výcvik';
$Definition['PaymentWizard']['fasterTrainingDesc'] = 'Po tuto dobu můžeš ve všech vesnicích cvičit rychleji.';
$Definition['PaymentWizard']['academyResearchAll'] = 'Vyzkoumat všechny jednotky';
$Definition['PaymentWizard']['academyResearchAllDesc'] = 'Vyzkoumá ve vesnici všechny jednotky. V akademii už nebude potřeba nic zkoumat.';
$Definition['PaymentWizard']['smithyUpgradeAllToMax'] = 'Vylepšit všechny jednotky v kovárně na max. úroveň';
$Definition['PaymentWizard']['smithyUpgradeAllToMaxDesc'] = 'Všechny jednotky budou v kovárně vylepšeny na maximální úroveň.';
$Definition['PaymentWizard']['cancelTrainingQueue'] = 'Vyčistit frontu výcviku';
$Definition['PaymentWizard']['cancelTrainingQueueDesc'] = 'Celá fronta výcviku bude smazána. Suroviny se nevracejí.';
$Definition['PaymentWizard']['increaseStorage'] = 'Zvýšit kapacitu skladů';
$Definition['PaymentWizard']['increaseStorageDesc'] = 'Zvýší současnou kapacitu skladů o kapacitu skladu úrovně 20.';
$Definition['PaymentWizard']['Gold bank'] = 'Zlatá banka';
$Definition['PaymentWizard']['Show all the golds you have saved'] = 'Zobrazit všechno uložené zlato';
$Definition['PaymentWizard']['Display saved golds and how to use them'] = 'Zobrazit uložené zlato a jak ho použít';
$Definition['PaymentWizard']['Voucher code'] = 'Kód poukázky';
$Definition['PaymentWizard']['Voucher ID'] = 'ID poukázky';
$Definition['PaymentWizard']['Gold'] = 'Zlato';
$Definition['PaymentWizard']['Action'] = 'Akce';
$Definition['PaymentWizard']['Use'] = 'Použít';
$Definition['PaymentWizard']['Date'] = 'Datum';
$Definition['PaymentWizard']['Used: %s of %s'] = 'Použito: %s z %s';
$Definition['PaymentWizard']['No saved golds'] = 'Nemáš žádné uložené zlato!';
$Definition['PaymentWizard']['Are you sure?'] = 'Jsi si jistý?';
$Definition['PaymentWizard']['This feature is disabled'] = 'Tato funkce je vypnutá.';
$Definition['PaymentWizard']['Gold bank is disabled'] = 'Zlatá banka je vypnutá';
$Definition['PaymentWizard']['Invitation is closed'] = 'Pozvánky jsou uzavřené';
$Definition['PaymentWizard']['Server is closed for invitations'] = 'Server je pro pozvánky uzavřen.';
$Definition['PaymentWizard']['Gold bank is disabled'] = 'Zlatá banka je vypnutá.';
$Definition['PaymentWizard']['Power'] = 'Síla!';
$Definition['PaymentWizard']['Attack/Defense Bonus'] = 'Bonus útoku/obrany';


$Definition['PaymentWizard']['atkBonus'] = '+%s%% bonus útoku';
$Definition['PaymentWizard']['atkBonusDesc'] = 'Zvýší celkový útočný bonus tvých jednotek o procenta.';
$Definition['PaymentWizard']['defBonus'] = '+%s%% bonus obrany';
$Definition['PaymentWizard']['defBonusDesc'] = 'Zvýší celkový obranný bonus tvých jednotek o procenta.';
$Definition['PaymentWizard']['Protection'] = 'Ochrana';
$Definition['PaymentWizard']['Protection Packages'] = 'Balíčky ochrany';
$Definition['PaymentWizard']['Protection daily limit reached'] = 'Dosažen denní limit ochrany.';
$Definition['PaymentWizard']['You have %s hour(s) of protection left'] = 'Zbývá ti %s hodin ochrany.';
$Definition['PaymentWizard']['You have no protection left'] = 'Nemáš žádnou zbývající ochranu.';
$Definition['PaymentWizard']['You can buy %s hour(s) of protection per day'] = 'Denně můžeš koupit %s hodin ochrany.';
$Definition['PaymentWizard']['You have %s golds in your vouchers'] = 'Ve svých poukázkách máš %s zlata.';


$Definition['PaymentWizard']['Location'] = 'Země';
$Definition['PaymentWizard']['Packages'] = 'Balíčky';
$Definition['PaymentWizard']['Payment methods'] = 'Způsoby platby';
$Definition['PaymentWizard']['Overview'] = 'Přehled';
$Definition['PaymentWizard']['Selected package'] = 'Vybraný balíček';
$Definition['PaymentWizard']['Your new gold balance'] = 'Tvůj nový zůstatek zlata';
$Definition['PaymentWizard']['Gold'] = 'Zlato';
$Definition['PaymentWizard']['Price'] = 'Cena';
$Definition['PaymentWizard']['Buy now'] = 'Koupit hned';
$Definition['PaymentWizard']['Voucher'] = 'Poukázka';
$Definition['PaymentWizard']['Redeem'] = 'Uplatnit';
$Definition['PaymentWizard']['Redeem voucher'] = 'Uplatnit poukázku';
$Definition['PaymentWizard']['Open orders'] = 'Otevřené objednávky';
$Definition['PaymentWizard']['Voucher rules'] = 'Pravidla poukázek';
$Definition['PaymentWizard']['voucherRules'] = [
    'Převedeno bude jen ' . (100 - Config::getProperty("gold", "voucherTaxPercent")) . ' % zbývajícího zlata.',
    'Kód poukázky lze uplatnit na libovolném uživatelském účtu (bez e-mailu).',
    'Jako poukázky se ukládá jen koupené zlato a bonusy.',
    sprintf('Cena pro alianci je pro %s hlavních členů, jejichž počet obyvatel je nad %s.', Config::getProperty("bonus", "bonusGoldTopAllianceCount"), Config::getProperty("bonus", "bonusGoldTopAllianceMinPop")),
    'Poukázky nemůžeš prodávat.',
    'Kódy poukázek platí jen ' . Config::getProperty("gold", "voucherExpireDays") . ' dní.',
];
$Definition['PaymentWizard']['Reason'] = 'Důvod';
$Definition['PaymentWizard']['Show'] = 'Zobrazit';
$Definition['PaymentWizard']['Voucher'] = 'Poukázka';
$Definition['PaymentWizard']['Vouchers'] = 'Poukázky';
$Definition['PaymentWizard']['History'] = 'Historie';
$Definition['PaymentWizard']['Email'] = 'E-mail';
$Definition['PaymentWizard']['Yes'] = 'Ano';
$Definition['PaymentWizard']['Used'] = 'Použito';
$Definition['PaymentWizard']['Use details'] = 'Podrobnosti použití';
$Definition['PaymentWizard']['Voucher details'] = 'Podrobnosti poukázky';
$Definition['PaymentWizard']['World ID'] = 'ID světa';
$Definition['PaymentWizard']['Player'] = 'Hráč';
$Definition['PaymentWizard']['Gold num'] = 'Množství zlata';
$Definition['PaymentWizard']['Use voucher'] = 'Použít poukázky';
$Definition['PaymentWizard']['Invalid voucher code'] = 'Neplatný kód poukázky';
$Definition['PaymentWizard']['Unable to use voucher'] = 'Poukázku nelze použít';
$Definition['PaymentWizard']['Redeem gold by gold number'] = 'Uplatnit zlato podle množství';
$Definition['PaymentWizard']['You don`t have enough gold in your bank'] = 'V bance nemáš dost zlata.';
$Definition['PaymentWizard']['Gold number must be 20 or more'] = 'Množství zlata musí být 20 nebo více.';
$Definition['PaymentWizard']['%s golds was added to your account'] = 'Na tvůj účet bylo připsáno %s zlata.';
$Definition['PaymentWizard']['Voucher code'] = 'Kód poukázky';
$Definition['PaymentWizard']['Use voucher with code'] = 'Použít poukázku s kódem';
$Definition['PaymentWizard']['Your voucher(s)'] = 'Tvé poukázky';
$Definition['PaymentWizard']['You have no voucher codes'] = "Nemáš žádné poukázky.";
$Definition['PaymentWizard']['VoucherReasons']['gift'] = 'Dárek';
$Definition['PaymentWizard']['VoucherReasons']['remaining'] = 'Zbývající zlato';
$Definition['PaymentWizard']['VoucherReasons']['winner'] = 'Vítěz';
$Definition['PaymentWizard']['VoucherReasons']['2ndWinner'] = '2. místo';
$Definition['PaymentWizard']['VoucherReasons']['3rdWinner'] = '3. místo';
$Definition['PaymentWizard']['VoucherReasons']['winnerAlliance'] = 'Vítězná aliance';
$Definition['PaymentWizard']['VoucherReasons']['topOff'] = 'Nejlepší útočník';
$Definition['PaymentWizard']['VoucherReasons']['topDef'] = 'Nejlepší obránce';
$Definition['PaymentWizard']['VoucherReasons']['topClimber'] = 'Nejlepší skokan';
$Definition['PaymentWizard']['VoucherReasons']['topOffHammer'] = 'Nejlepší útočné kladivo';
$Definition['PaymentWizard']['VoucherReasons']['topDefHammer'] = 'Nejlepší obranné kladivo';
$Definition['PaymentWizard']['VoucherReasons']['payment'] = 'Průvodce platbou';
$Definition['PaymentWizard']['VoucherReasons']['Partial use'] = 'Částečné použití';
$Definition['PaymentWizard']['PayPalVATDesc'] = 'K platbě přes PayPal se připočítá 3 % DPH + 0,30 $.';
$Definition['PaymentWizard']['Animals purchased'] = 'Zvířata koupena!';
$Definition['PaymentWizard']['Troops purchased'] = 'Jednotky koupeny!';
$Definition['PaymentWizard']['Resources purchased!'] = 'Suroviny koupeny!';
$Definition['PaymentWizard']['You can buy animals every %s %s'] = 'Zvířata můžeš kupovat každých %s %s.';
$Definition['PaymentWizard']['You can buy resources every %s %s'] = 'Suroviny můžeš kupovat každých %s %s.';
$Definition['PaymentWizard']['You can buy troops every %s %s'] = 'Jednotky můžeš kupovat každých %s %s.';
$Definition['PaymentWizard']['You need to wait %s %s before buying this package'] = 'Před nákupem tohoto balíčku musíš počkat %s %s.';

$Definition['PaymentWizard']['buyBuildings'] = 'Koupit budovy';
$Definition['PaymentWizard']['buildMaxWarehouse'] = 'Postaví ve vesnici sklad surovin úrovně 20.';
$Definition['PaymentWizard']['buildMaxWarehouseDesc'] = '';
$Definition['PaymentWizard']['buildMaxGranary'] = 'Postaví ve vesnici sýpku úrovně 20.';
$Definition['PaymentWizard']['buildMaxGranaryDesc'] = '';
$Definition['PaymentWizard']['upgradeMainBuildingTo20'] = 'Rozšíří ve vesnici hlavní budovu na úroveň 20.';
$Definition['PaymentWizard']['upgradeMainBuildingTo20Desc'] = '';
$Definition['PaymentWizard']['buildMaxRallyPoint'] = 'Postaví ve vesnici shromaždiště úrovně 20.';
$Definition['PaymentWizard']['buildMaxRallyPointDesc'] = '';
$Definition['PaymentWizard']['buildMaxBarracks'] = 'Postaví ve vesnici kasárny úrovně 20.';
$Definition['PaymentWizard']['buildMaxBarracksDesc'] = '';
$Definition['PaymentWizard']['buildMaxStable'] = 'Postaví ve vesnici stáj úrovně 20.';
$Definition['PaymentWizard']['buildMaxStableDesc'] = '';
$Definition['PaymentWizard']['buildMaxWorkshop'] = 'Postaví ve vesnici dílnu úrovně 20.';
$Definition['PaymentWizard']['buildMaxWorkshopDesc'] = '';
$Definition['PaymentWizard']['buildMaxAcademy'] = 'Postaví ve vesnici akademii úrovně 20.';
$Definition['PaymentWizard']['buildMaxAcademyDesc'] = '';
$Definition['PaymentWizard']['buildMaxSmithy'] = 'Postaví ve vesnici kovárnu úrovně 20.';
$Definition['PaymentWizard']['buildMaxSmithyDesc'] = '';
$Definition['PaymentWizard']['buildMaxTrasury'] = 'Postaví ve vesnici pokladnici úrovně 20.';
$Definition['PaymentWizard']['buildMaxTrasuryDesc'] = '';
$Definition['PaymentWizard']['buildMaxTournametnSquare'] = 'Postaví ve vesnici turnajové hřiště úrovně 20.';
$Definition['PaymentWizard']['buildMaxTournametnSquareDesc'] = '';
$Definition['PaymentWizard']['upgradeAllResourcesTo5'] = 'Rozšířit surovinová pole na úroveň 5';
$Definition['PaymentWizard']['upgradeAllResourcesTo5Desc'] = '';
$Definition['PaymentWizard']['upgradeAllResourcesTo10'] = 'Rozšířit surovinová pole na úroveň 10';
$Definition['PaymentWizard']['upgradeAllResourcesTo10Desc'] = '';
$Definition['PaymentWizard']['upgradeAllResourcesTo20'] = 'Rozšířit surovinová pole na úroveň 20';
$Definition['PaymentWizard']['upgradeAllResourcesTo20Desc'] = '';
$Definition['PaymentWizard']['upgradeAllResourcesTo30'] = 'Rozšířit surovinová pole na úroveň 30';
$Definition['PaymentWizard']['upgradeAllResourcesTo30Desc'] = '';

//
$Definition['productionOverview'] = [
    "productionOverview" => "Přehled produkce",
    "balance" => "bilance",
    "production_field" => "Surovinové pole",
    "production_per_hour" => "Hodinová produkce",
    "production" => "produkce",
    "bonus" => "Bonus",
    "production_bonus" => "Bonus produkce",
    "Oases" => "Oázy",
    "total_bonus" => "Celkový bonus",
    "total_production_per_hour" => "Celková produkce za hodinu",
    "hero_production" => "Produkce hrdiny",
    "interim_balance" => "Průběžná bilance",
    "total" => "Celkem",
    "HDP" => "Koňské napajedlo",
    "sum" => "Součet",
    "inactive" => "neaktivní",
    "productionBoostSpeechBubbleFurtherInfo" => 'Bonus produkce zvyšuje produkci suroviny ve <span class="underlined">všech</span> tvých vesnicích.',
    "productionWithBoost" => "Hodinová produkce včetně bonusu produkce",
    "extendNow" => "Prodloužit hned",
    "activeNow" => "Aktivovat hned",
    "durationDays" => "Trvání ve dnech",
    "Production of buildings and oases" => "Produkce budov a oáz",
    "Population and construction orders" => "Obyvatelé a stavební zakázky",
    "Incomplete construction orders" => "Nedokončené stavební zakázky",
    "Consumption of own troops" => "Spotřeba vlastních jednotek",
    "in village" => "ve vesnici",
    "in oasis or reinforcements from own villages" => "v oázách nebo posily z vlastních vesnic",
    "imprisoned" => "uvězněné",
    "on the way" => "na cestě",
    "Artefact bonus" => "Bonus artefaktu",
    "Consumption of foreign troops" => "Spotřeba cizích jednotek",
    "Crop balance" => "Bilance obilí",
    "WW effect" => "Účinek divu světa",
];
//
$Definition['Alliance']['In order to quit alliance you need an embassy'] = 'K opuštění aliance potřebuješ ambasádu.';
$Definition['Alliance']['in vacation'] = 'na dovolené';
$Definition['Profile']['Player Profile'] = 'Profil hráče';
$Definition['Profile']['Details'] = 'Podrobnosti';
$Definition['Profile']['Rank'] = 'Pořadí';
$Definition['Profile']['Tribe'] = 'Národ';
$Definition['Profile']['Alliance'] = 'Aliance';
$Definition['Profile']['Villages'] = 'Vesnice';
$Definition['Profile']['Status'] = 'Stav';
$Definition['Profile']['Login to player account'] = 'Přihlásit se do účtu hráče';
$Definition['Profile']['Population'] = 'Obyvatelé';
$Definition['Profile']['Description'] = 'Popis';
$Definition['Profile']['Name'] = 'Jméno';
$Definition['Profile']['Active'] = 'Aktivní';
$Definition['Profile']['inActive'] = 'Neaktivní';
$Definition['Profile']['This player messages are banned'] = 'Tento hráč má zakázanou komunikaci ve hře.';
$Definition['Profile']['Escape in villages'] = 'Únik ve vesnicích';
$Definition['Profile']['Oases'] = 'Oázy';
$Definition['Profile']['Inhabitants'] = 'Obyvatelé';
$Definition['Profile']['Coordinates'] = 'Souřadnice';
$Definition['Profile']['Stop ignoring this player'] = 'Přestat ignorovat tohoto hráče';
$Definition['Profile']['Accept messages from this player'] = 'Přijímat zprávy od tohoto hráče';
$Definition['Profile']['Write message'] = 'Napsat zprávu';
$Definition['Profile']['Write Message'] = 'Napsat zprávu';
$Definition['Profile']['Ignore Player'] = 'Ignorovat hráče';
$Definition['Profile']['Edit list'] = 'Upravit seznam';
$Definition['Profile']['Ignore list is full'] = 'Seznam ignorovaných je plný.';
$Definition['Profile']['Player will be ignored'] = 'Hráč bude ignorován.';
$Definition['Profile']['WoW'] = 'Div světa';
$Definition['Profile']['Support'] = 'Podpora';
$Definition['Profile']['Game rules'] = 'Pravidla hry';
$Definition['Profile']['To ignore messages from a specific player, go to its profile and click on "Ignore"!'] = 'Pokud chceš ignorovat zprávy od určitého hráče, otevři jeho profil a klikni na "Ignorovat"!';
$Definition['Profile']['MultihunterDesc'] = 'Multihunteři odpovídají za dodržování <a href="/terms.html" target="_blank">pravidel hry</a>. Pokud máš dotazy k pravidlům nebo chceš nahlásit porušení, můžeš Multihunterům napsat.';
$Definition['Profile']['Support and Multihunter'] = 'Podpora a Multihunter';
$Definition['Profile']['The support consists of experienced players who will gladly answer your questions'] = 'Podporu tvoří zkušení hráči, kteří ti rádi odpoví na otázky.';
$Definition['Profile']['capital'] = 'Hlavní město';
$Definition['Profile']['Artifact'] = 'Artefakt';
$Definition['Profile']['Birthday'] = 'Narozeniny';
$Definition['Profile']['Gender'] = 'Pohlaví';
$Definition['Profile']['n/a'] = 'N/A';
$Definition['Profile']['male'] = 'muž';
$Definition['Profile']['female'] = 'žena';
$Definition['Profile']['Location'] = 'Země';
$Definition['Profile']['Age'] = 'Věk';
$Definition['Profile']['Overview'] = 'Přehled';
$Definition['Profile']['Edit Profile'] = 'Upravit profil';
$Definition['Profile']['Medals'] = 'Medaile';
$Definition['Profile']['DoveOfPeace'] = 'Holubice míru';
$Definition['Profile']['Category'] = 'Kategorie';
$Definition['Profile']['Rank'] = 'Pořadí';
$Definition['Profile']['Week'] = 'Týden';
$Definition['Profile']['BB-Code'] = 'BB kód';
$Definition['Profile']['SpecialMedalsTitle'] = [
    1 => 'Vítěz – 1. místo',
    2 => 'Vítěz – 2. místo',
    3 => 'Vítěz – 3. místo',

    4 => 'Nejlepší útočník – 1. místo',
    5 => 'Nejlepší útočník – 2. místo',
    6 => 'Nejlepší útočník – 3. místo',
    7 => 'Nejlepší útočník – 4. místo',

    8 => 'Nejlepší obránce – 1. místo',
    9 => 'Nejlepší obránce – 2. místo',
    10 => 'Nejlepší obránce – 3. místo',
    11 => 'Nejlepší obránce – 4. místo',

    12 => 'Nejlepší skokan – 1. místo',
    13 => 'Nejlepší skokan – 2. místo',
    14 => 'Nejlepší skokan – 3. místo',
    15 => 'Nejlepší skokan – 4. místo',

    16 => 'Nejlepší útočné kladivo',
    17 => 'Nejlepší obranné kladivo',
    18 => 'Vítězná aliance',
];
$Definition['Profile']['Player special medals'] = 'Zvláštní medaile hráče';
$Definition['Profile']['mySpeicalMTitle'] = [
    1 => 'Div světa',
    2 => 'Div světa',
    3 => 'Div světa',

    4 => "Útok",
    5 => "Útok",
    6 => "Útok",
    7 => "Útok",

    8 => 'Obrana',
    9 => 'Obrana',
    10 => 'Obrana',
    11 => 'Obrana',

    12 => 'Obyvatelé',
    13 => 'Obyvatelé',
    14 => 'Obyvatelé',
    15 => 'Obyvatelé',

    16 => 'Nejlepší útočné kladivo',
    17 => 'Nejlepší obranné kladivo',
    18 => 'Vítězná aliance',
];
$Definition['Profile']['mySpeicalM'] = [
// Ranke Wonder Of World
1 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 1 - Úroveň divu světa: %s',
2 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 2 - Úroveň divu světa: %s',
3 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 3 - Úroveň divu světa: %s',

// Ranke Offensive 1 ta 4 top 10
4 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 1 - B: %s',
5 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 2 - B: %s',
6 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 3 - B: %s',
7 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 4 - B: %s',
// Ranke Defensive 1 ta 4 top 10
8 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 1 - B: %s',
9 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 2 - B: %s',
10 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 3 - B: %s',
11 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 4 - B: %s',
// Ranke Pop Nafarate Avale 1 ta 4
12 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 1 - Body: %s',
13 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 2 - Body: %s',
14 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 3 - Body: %s',
15 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Pořadí: 4 - Body: %s',

16 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Body: %s',
17 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - Body: %s',
18 => 'S: <span class="warning"><b>%s</b></span> - Jméno: <span class="warning"><b>%s</b></span> - %s - A: <span class="warning"><b>%s</b></span>',

];
$Definition['Profile']['SpecialMedalsLayout'] = 'Server %s  &nbsp; &nbsp; &nbsp; Účet %s';
$Definition['Profile']['SpecialMedalsState'] = 'Stav zvláštních medailí';
$Definition['Profile']['SpecialShow'] = 'Zobrazit';
$Definition['Profile']['SpecialHide'] = 'Skrýt';
$Definition['Profile']['SpecialMedals'] = 'Světové medaile';
$Definition['Profile']['Language'] = 'Jazyk';
$Definition['Profile']['Show country flag in your profile'] = 'Zobrazit v profilu vlajku země';
$Definition['Profile']['Show special medals?'] = 'Zobrazit zvláštní medaile?';
$Definition['Profile']['YES'] = 'ANO';
$Definition['Profile']['NO'] = 'NE';
$Definition['Profile']['Note'] = 'Poznámka';
$Definition['Profile']['Edit'] = 'Upravit';
$Definition['Profile']['NoteDescription'] = 'Uprav svou osobní poznámku o tomto hráči. Max. délka: 500 znaků';


$Definition['Quest'] = [];
$Definition['Quest']['welcome'] = 'Vítej';
$Definition['Quest']['helperAndTasks'] = 'Úkoly a nápověda';
$Definition['Quest']['taskList'] = 'Seznam úkolů';
$Definition['Quest']['questButtonActivateTips'] = 'Zobrazit tipy';
$Definition['Quest']['questButtonDeactivateTips'] = 'Vypnout tipy';
$Definition['Quest']['questTipsToggleDescription'] = 'Tipy zap./vyp.';
$Definition['Quest']['startTutorial'] = 'Pokračovat';
$Definition['Quest']['getReward'] = 'Vyzvednout odměnu';
$Definition['Quest']['SkipTutorial'] = 'Přeskočit úkoly';
$Definition['Quest']['skip'] = 'Přeskočit úkoly';
$Definition['Quest']['questRewardTitle'] = 'Tvá odměna:';
$Definition['Quest']['overview'] = 'Přehled';
$Definition['Quest']['questTaskTitle'] = 'Úkol';
$Definition['Quest']['endOfTutorial'] = 'konec výuky';
$Definition['Quest']['continue'] = 'Pokračovat';
$Definition['Quest']['battle'] = 'Boj ';
$Definition['Quest']['economy'] = 'Ekonomika';
$Definition['Quest']['world'] = 'Svět ';
$Definition['Quest']['Tutorial_01']['questTitle'] = 'Vítej';
$Definition['Quest']['Tutorial_01']['questDescription'] = 'Ahoj %s, vítej v Travianu! <br>Dokud si nepostavíš novou vesnici, budu ti pomáhat. V této výuce si vybuduješ vlastní vesnici a poznáš smysl hry a její funkce.';
$Definition['Quest']['Tutorial_01']['continue'] = 'Pokračovat';
$Definition['Quest']['Tutorial_01']['skip'] = 'Přeskočit úkoly';
$Definition['Quest']['Tutorial_01']['todo'] = [0 => 'Výuka vysvětluje hlavní funkce hry a zabere jen pár minut. Začni hned!',];
$Definition['Quest']['Tutorial_01']['steps'] = [0 => 'Začít výuku',];
$Definition['Quest']['Tutorial_02']['questTitle'] = 'Úkoly a nápověda';
$Definition['Quest']['Tutorial_02']['questDescription'] = 'Stránku s úkoly můžeš přesunout nebo zavřít. Znovu ji otevřeš kliknutím na můj obrázek v levém horním rohu. Tipy a úkoly ti pomohou ve hře.';
$Definition['Quest']['Tutorial_02']['questDescriptionReward'] = 'Teď můžeš kdykoli zjistit informace o aktuálním úkolu. Další úkol začneš, jakmile si vyzvedneš odměnu. Vyzvedni si hliněný důl!';
$Definition['Quest']['Tutorial_02']['rewardDescription'] = 'Čeká na tebe hliněný důl úrovně 1!';
$Definition['Quest']['Tutorial_02']['todo'] = [
    0 => 'Zavřít úkoly ',
    1 => 'klikni na rádce a otevři stránku s tipy ',
    2 => 'Vypnout tipy k funkcím ',
];
$Definition['Quest']['Tutorial_02']['steps'] = [
    0 => 'Zavřít úkoly',
    1 => 'Otevřít úkoly',
    2 => 'Vypnout tipy',
];
$Definition['Quest']['Tutorial_03']['questTitle'] = 'Postav dřevorubce ';
$Definition['Quest']['Tutorial_03']['questDescription'] = 'K rozvoji vesnice, stavbě budov, výcviku jednotek i růstu říše potřebuješ hodně surovin! Nejdřív zvyš produkci surovin – postav dřevorubce!';
$Definition['Quest']['Tutorial_03']['questDescriptionReward'] = 'Silná ekonomika je základ dobrého startu. Stavbu dřevorubce jsem ti právě dokončil, abys mohl pokračovat.';
$Definition['Quest']['Tutorial_03']['rewardDescription'] = 'dokončit dřevorubce úrovně 1';
$Definition['Quest']['Tutorial_03']['todo'] = [
    0 => 'Otevři lesní pole kliknutím na něj ',
    1 => 'Zadej stavbu dřevorubce úrovně 1 ',
];
$Definition['Quest']['Tutorial_03']['steps'] = [
    0 => 'Otevřít pole dřevorubce',
    1 => 'postavit dřevorubce',
];
$Definition['Quest']['Tutorial_04']['questTitle'] = 'Rozšiř dřevorubce ';
$Definition['Quest']['Tutorial_04']['questDescription'] = 'Větší budova vyžaduje s každým rozšířením více surovin, ale zato také více vyrábí. Rozšiř teď dřevorubce z úrovně 1 na úroveň 2!';
$Definition['Quest']['Tutorial_04']['questDescriptionReward'] = 'Přehled tvých skladů a zásob najdeš nad vesnicí. Náklady na stavbu se odečtou ze zásob. Stavbu ti zase hned dokončím.';
$Definition['Quest']['Tutorial_04']['questTaskTitle'] = 'Úkol';
$Definition['Quest']['Tutorial_04']['rewardDescription'] = 'Okamžitě dokonči stavbu dřevorubce úrovně 2';
$Definition['Quest']['Tutorial_04']['todo'] = [
    0 => 'Otevři dřevorubce úrovně 1 ',
    1 => 'Zadej stavbu dřevorubce úrovně 2 ',
];
$Definition['Quest']['Tutorial_04']['steps'] = [
    0 => 'Otevřít budovu ',
    1 => 'Dřevorubec 2',
];
$Definition['Quest']['Tutorial_05']['questTitle'] = 'Postav obilné pole ';
$Definition['Quest']['Tutorial_05']['questDescription'] = 'Když se podíváš na suroviny, všimni si spotřeby obilí v levém rohu. Obilí je potřeba pro jednotky i budovy. Postav prosím obilné pole';
$Definition['Quest']['Tutorial_05']['questDescriptionReward'] = 'Tvá vesnice teď produkuje dost obilí na stavbu nových budov. Obilím se živí obyvatelé i jednotky umístěné ve vesnici.';
$Definition['Quest']['Tutorial_05']['rewardDescription'] = 'Dokonči obilné pole úrovně 1 a rozšiř ho na úroveň 2';
$Definition['Quest']['Tutorial_05']['todo'] = [
    0 => 'Klikni na obilné pole ',
    1 => 'Postav obilné pole na úroveň 1 ',
];
$Definition['Quest']['Tutorial_05']['steps'] = [
    0 => 'Otevřít obilné pole ',
    1 => 'Postavit obilné pole',
];
$Definition['Quest']['Tutorial_06']['questTitle'] = 'Produkce hrdiny ';
$Definition['Quest']['Tutorial_06']['questDescription'] = 'Pokud tvůj hrdina žije, může vyrábět suroviny pro svou vesnici. Na naše stavby máme jen málo hlíny. Změň produkci hrdiny na hlínu';
$Definition['Quest']['Tutorial_06']['questDescriptionReward'] = 'Skvělé! Teď ti hrdina pomůže vyrábět více surovin. Všechny vyrobené suroviny se připíší do hlavní vesnice. Trochu ti suroviny navýším.';
$Definition['Quest']['Tutorial_06']['rewardDescription'] = '<div class="inlineIconList resourceWrapper"><div class="inlineIcon resources" title=""><i class="r2 questRewardTypeResource questRewardTypeClay"></i><span class="value questRewardValue">'.Quest::getInstance()->multiply(200).'</span></div></div>';
$Definition['Quest']['Tutorial_06']['todo'] = [
    0 => 'Klikni na obrázek hrdiny a otevři přehled. ',
    1 => 'Změň produkci hrdiny na hlínu a ulož. ',
];
$Definition['Quest']['Tutorial_06']['steps'] = [
    0 => 'Klikni na obrázek hrdiny',
    1 => 'Změnit produkci ',
];
$Definition['Quest']['Tutorial_07']['questTitle'] = 'Vstup do centra vesnice ';
$Definition['Quest']['Tutorial_07']['questDescription'] = 'Dále zvýšíme kapacitu skladů, kterou vidíš nahoře v menu hry. K tomu potřebujeme budovy uvnitř vesnice. Vstup do centra vesnice.';
$Definition['Quest']['Tutorial_07']['todo'] = [0 => 'Vstup do centra vesnice.',];
$Definition['Quest']['Tutorial_07']['steps'] = [0 => 'Vstoupit do centra vesnice',];
$Definition['Quest']['Tutorial_08']['questTitle'] = 'Postav sklad surovin ';
$Definition['Quest']['Tutorial_08']['questDescription'] = 'Bez skladu surovin můžeš ve vesnici uskladnit jen málo surovin. Klikni na stavební místo, v nabídce staveb najdi sklad surovin a postav ho!';
$Definition['Quest']['Tutorial_08']['questDescriptionReward'] = 'Stavba začala a brzy budeš moci uskladnit a uloupit více surovin. Dám ti ' . Quest::calcEffect(86400, 'plus', true) . ' Travian Plus, se kterým můžeš zadat další stavbu do fronty, i když první stavba ještě není dokončená.';
$Definition['Quest']['Tutorial_08']['rewardDescription'] = 'Jeden den Travian Plus';
$Definition['Quest']['Tutorial_08']['todo'] = [
    0 => 'otevři stavební místo a v nabídce klikni na infrastrukturu. ',
    1 => 'Zadej stavbu skladu surovin úrovně 1. ',
];
$Definition['Quest']['Tutorial_08']['steps'] = [
    0 => 'Klikni na stavební místo',
    1 => 'Postavit sklad surovin',
];
$Definition['Quest']['Tutorial_09']['questTitle'] = 'Shromaždiště ';
$Definition['Quest']['Tutorial_09']['questDescription'] = 'Abys mohl posílat hrdinu na dobrodružství, potřebuješ shromaždiště – najdeš ho v centru vesnice! Postav ho na úroveň 1';
$Definition['Quest']['Tutorial_09']['questDescriptionReward'] = 'Vypadá to skvěle! Teď můžeš poslat hrdinu na dobrodružství. K tomu ti dám trochu zlata, které můžeš rozumně utratit.';
$Definition['Quest']['Tutorial_09']['rewardDescription'] = '<img src="img/x.gif" class="questRewardTypeGold"> <span class="questRewardValue">2</span>';
$Definition['Quest']['Tutorial_09']['todo'] = [
    0 => 'Klikni na stavební místo shromaždiště. ',
    1 => 'Postav shromaždiště úrovně 1 ',
];
$Definition['Quest']['Tutorial_09']['steps'] = [
    0 => 'Klikni na místo shromaždiště',
    1 => 'Postavit shromaždiště',
];
$Definition['Quest']['Tutorial_10']['questTitle'] = 'Dokončit okamžitě ';
$Definition['Quest']['Tutorial_10']['questDescription'] = 'Pod vesnicí najdeš seznam všech aktuálních staveb. Tentokrát můžeš stavbu urychlit sám. Použij zlato z minulého úkolu a dokonči stavby kliknutím na "dokončit stavbu okamžitě".';
$Definition['Quest']['Tutorial_10']['questDescriptionReward'] = 'Teď můžeš poslat hrdinu na dobrodružství. Nejdřív zadej stavby, aby tvá vesnice stále rostla. Vezmi si toto zlato a použij ho moudře.';
$Definition['Quest']['Tutorial_10']['rewardDescription'] = '<img src="img/x.gif" class="questRewardTypeGold"> <span class="questRewardValue">10</span>';
$Definition['Quest']['Tutorial_10']['todo'] = [0 => 'Okamžitě dokonči stavby ',];
$Definition['Quest']['Tutorial_10']['steps'] = [0 => 'Dokončit stavbu ',];
$Definition['Quest']['Tutorial_11']['questTitle'] = 'Vyraz na dobrodružství ';
$Definition['Quest']['Tutorial_11']['questDescription'] = 'Objevuj tajemná místa v okolí a sbírej zkušenosti a cennou kořist. Otevři seznam dobrodružství a pošli hrdinu na jeho první dobrodružství.';
$Definition['Quest']['Tutorial_11']['questDescriptionReward'] = 'Skvělé, tvůj hrdina je na cestě za dobrodružstvím – co asi najde? Pod obrázkem hrdiny vidíš, že je na cestě. Hrdinu ti OKAMŽITĚ vrátím, abys viděl, co se stalo.';
$Definition['Quest']['Tutorial_11']['rewardDescription'] = 'Tvůj hrdina se z dobrodružství okamžitě vrátí';
$Definition['Quest']['Tutorial_11']['todo'] = [0 => 'Pošli hrdinu na jeho první dobrodružství ',];
$Definition['Quest']['Tutorial_11']['steps'] = [0 => 'Dobrodružství hrdiny ',];
$Definition['Quest']['Tutorial_12']['questTitle'] = 'Hlášení';
$Definition['Quest']['Tutorial_12']['questDescription'] = 'Tvůj hrdina se teď vrací ze svého prvního dobrodružství. V menu nahoře najdeš hlášení. Otevři seznam hlášení a přečti si hlášení o dobrodružství.';
$Definition['Quest']['Tutorial_12']['questDescriptionReward'] = 'Vidíš, jakou odměnu tvůj hrdina našel. Hrdina je také lehce zraněný – aby se to nestalo znovu, dám ti trochu masti.';
$Definition['Quest']['Tutorial_12']['rewardDescription'] = '<img src="img/x.gif" class="questRewardTypeItem item106"> <span class="questRewardValue">' . Quest::calcEffect(10, 'item', true) . '</span>';
$Definition['Quest']['Tutorial_12']['todo'] = [
    0 => 'Otevři seznam hlášení',
    1 => 'Zobraz nové hlášení o dobrodružství',
];
$Definition['Quest']['Tutorial_12']['steps'] = [
    0 => 'Menu hlášení ',
    1 => 'Přečíst hlášení ',
];
$Definition['Quest']['Tutorial_13']['questTitle'] = 'Vyléčit hrdinu';
$Definition['Quest']['Tutorial_13']['questDescription'] = 'Tvůj hrdina byl lehce zraněn. Kliknutím na jeho obrázek otevři přehled hrdiny. Pak v inventáři klikni na masti a použij je tlačítkem "ok". Spotřebuje se jen potřebné množství.';
$Definition['Quest']['Tutorial_13']['questDescriptionReward'] = 'Všechny nástroje a zbraně se používají stejně. Informace o předmětu zobrazíš podržením kurzoru nad ním.';
$Definition['Quest']['Tutorial_13']['rewardDescription'] = 'Tvůj hrdina navíc získal 20 bodů zkušeností.';
$Definition['Quest']['Tutorial_13']['todo'] = [
    0 => 'Klikni na obrázek hrdiny a otevři inventář ',
    1 => 'Klikni na masti v inventáři a použij je',
];
$Definition['Quest']['Tutorial_13']['steps'] = [
    0 => 'Inventář hrdiny ',
    1 => 'Vyléčit hrdinu',
];
$Definition['Quest']['Tutorial_14']['questTitle'] = 'Nápověda k rozhraní ';
$Definition['Quest']['Tutorial_14']['questDescription'] = 'Vedle mého obrázku najdeš další nápovědu ke hře. Jsou tam vysvětleny rozvržení a jednotlivé části uživatelského rozhraní. Zkus to!';
$Definition['Quest']['Tutorial_14']['questDescriptionReward'] = 'Pokud máš konkrétní otázky, nejdřív je hledej v "Travian Odpovědích". Najdeš je pod "i" v záhlaví tohoto okna nebo v rohu obrazovky.';
$Definition['Quest']['Tutorial_14']['rewardDescription'] = buildResourcesReward([270, 300, 270, 220]);
$Definition['Quest']['Tutorial_14']['todo'] = [0 => 'Otevři nápovědu k uživatelskému rozhraní a rozhlédni se ',];
$Definition['Quest']['Tutorial_14']['steps'] = [0 => 'Nápověda k uživatelskému rozhraní ',];
$Definition['Quest']['Tutorial_15']['questTitle'] = 'Konec výuky ';
$Definition['Quest']['Tutorial_15']['questDescription'] = $Definition['Quest']['Tutorial_15a']['questDescriptionReward'] = 'Teď znáš základy hry. Důležité informace, například časově omezené údaje pro nováčky, se zobrazí v infoboxu vlevo. Užij si Travian!';
$Definition['Quest']['Tutorial_15']['rewardDescription'] = 'Dokončit výuku.';
$Definition['Quest']['Tutorial_15']['todo'] = [0 => 'Konec výuky',];
$Definition['Quest']['Tutorial_15']['steps'] = [0 => 'Konec výuky',];
$Definition['Quest']['Tutorial_15a']['questTitle'] = 'Přeskočit výuku';
$Definition['Quest']['Tutorial_15a']['questDescription'] = 'Na začátek ti dám budovy a výhody z výuky. Až do založení druhé vesnice na tebe čekají další úkoly a odměny. Užij si hraní Travianu!';
$Definition['Quest']['Tutorial_15a']['rewardDescription'] = 'Shromaždiště, hliněný důl, dřevorubec 2, obilné pole 2, 10 zlata, ' . Quest::calcEffect(86400, 'plus', true) . ' PLUS';
$Definition['Quest']['Tutorial_15a']['steps'] = [0 => 'Přeskočit výuku',];
$Definition['Quest']['battle_01_name'] = 'Další dobrodružství ';
$Definition['Quest']['battle_02_name'] = 'Postav úkryt ';
$Definition['Quest']['battle_03_name'] = 'Postav kasárny ';
$Definition['Quest']['battle_04_name'] = 'Úroveň hrdiny ';
$Definition['Quest']['battle_05_name'] = 'Cvičit jednotky';
$Definition['Quest']['battle_06_name'] = 'Zemní val';
$Definition['Quest']['battle_07_name'] = 'Zaútoč na oázu';
$Definition['Quest']['battle_08_name'] = '10 dobrodružství';
$Definition['Quest']['battle_09_name'] = 'Aukce';
$Definition['Quest']['battle_10_name'] = 'Rozšiř kasárny';
$Definition['Quest']['battle_11_name'] = 'Postav akademii';
$Definition['Quest']['battle_12_name'] = 'Vyzkoumej jednotku';
$Definition['Quest']['battle_13_name'] = 'Postav kovárnu';
$Definition['Quest']['battle_14_name'] = 'Vylepši jednotky';
$Definition['Quest']['battle_15_name'] = 'Dokonči 5 dobrodružství';
$Definition['Quest']['Battle_01']['questTitle'] = 'Další dobrodružství';
$Definition['Quest']['Battle_01']['questDescription'] = 'Během výuky už jsi z dobrodružství získal nějaké zkušenosti. Až se hrdina vrátí do vesnice, vyraz na další dobrodružství. Kořist a zkušenosti ti pomohou růst rychleji.';
$Definition['Quest']['Battle_01']['questDescriptionReward'] = 'Výborně, tvůj hrdina už je na cestě. Tip: Čím větší sílu hrdina má, tím menší zranění na dobrodružstvích utrží.';
$Definition['Quest']['Battle_01']['rewardDescription'] = '30 zkušeností hrdiny';
$Definition['Quest']['Battle_01']['todo'] = [0 => 'Vyraz na druhé dobrodružství ',];
$Definition['Quest']['Battle_02']['questTitle'] = 'Postav úkryt ';
$Definition['Quest']['Battle_02']['questDescription'] = 'Mnoho hráčů žije z loupení surovin jiných hráčů. Na začátku hry máš ochranu nováčka a jsi v bezpečí. Postav úkryt, abys ochránil před vyloupením alespoň část surovin.';
$Definition['Quest']['Battle_02']['questDescriptionReward'] = 'Skvělé, lupiči to teď u tebe nebudou mít tak snadné. V infoboxu uvidíš, kolik ti zbývá ochrany nováčka.';
$Definition['Quest']['Battle_02']['rewardDescription'] = buildResourcesReward([130, 150, 120, 100]);
$Definition['Quest']['Battle_02']['todo'] = [0 => 'Postav ve vesnici úkryt ',];
$Definition['Quest']['Battle_03']['questTitle'] = 'Postav kasárny ';
$Definition['Quest']['Battle_03']['questDescription'] = 'Kasárny jsou první budova, ve které můžeš cvičit jednotky. I jako mírumilovný hráč budeš jednotky potřebovat k ochraně sebe a svých spojenců před nepřáteli.';
$Definition['Quest']['Battle_03']['questDescriptionReward'] = 'Kasárny jsou postavené! Dobrý krok k ovládnutí světa!';
$Definition['Quest']['Battle_03']['rewardDescription'] = buildResourcesReward([110, 140, 160, 30]);
$Definition['Quest']['Battle_03']['todo'] = [0 => ' Postav kasárny ',];
$Definition['Quest']['Battle_04']['questTitle'] = 'Úroveň hrdiny ';
$Definition['Quest']['Battle_04']['questDescription'] = 'Kdykoli hrdina dosáhne nové úrovně, zesílí. Otevři vlastnosti hrdiny a rozděl získané body.';
$Definition['Quest']['Battle_04']['questDescriptionReward'] = 'Rozdělení bodů můžeš kdykoli změnit. Potřebuješ k tomu jen knihu moudrosti, kterou lze najít na dobrodružstvích.';
$Definition['Quest']['Battle_04']['rewardDescription'] = buildResourcesReward([190, 250, 150, 110]);
$Definition['Quest']['Battle_04']['todo'] = [0 => 'Po postupu na vyšší úroveň rozděl body vlastností hrdiny.',];
$Definition['Quest']['Battle_05']['questTitle'] = 'Cvičit jednotky';
$Definition['Quest']['Battle_05']['questDescription'] = 'Teď je čas vycvičit první jednotky. V kasárnách už můžeš cvičit jeden typ pěchoty.';
$Definition['Quest']['Battle_05']['questDescriptionReward'] = 'Základní kámen slavné armády je položen! Pamatuj, že na tebe mohou zaútočit, i když nejsi online.';
$Definition['Quest']['Battle_05']['rewardDescription'] = '<img src="img/x.gif" class="questRewardTypeItem item114"> <span class="questRewardValue">1</span>';
$Definition['Quest']['Battle_05']['todo'] = [0 => 'Vycvič v kasárnách dvě jednotky ',];
$Definition['Quest']['Battle_06']['questTitle'] = 'Zemní val ';
$Definition['Quest']['Battle_06']['questDescription'] = 'Teď bys měl postavit i obranu. Opevnění zvýší základní obranu vesnice a také sílu bránících jednotek.';
$Definition['Quest']['Battle_06']['questDescriptionReward'] = 'Výborně, obránci tvé vesnice jsou teď lépe chráněni.';
$Definition['Quest']['Battle_06']['rewardDescription'] = buildResourcesReward([120, 120, 90, 50]);
$Definition['Quest']['Battle_06']['todo'] = [0 => 'Postav kolem vesnice opevnění. ',];
$Definition['Quest']['Battle_07']['questTitle'] = 'Zaútoč na oázu ';
$Definition['Quest']['Battle_07']['questDescription'] = 'Najdi na mapě volnou oázu v okolí a vyloupi ji. Pokud ji brání zvířata, pošli hrdinu vybaveného klecemi, aby je pochytal.';
$Definition['Quest']['Battle_07']['questDescriptionReward'] = 'Gratuluji, tvůj první útok je na cestě! Ještě chvíli ho můžeš na shromaždišti zrušit.';
$Definition['Quest']['Battle_07']['rewardDescription'] = Quest::calcEffect(2, 'troops', true) . ' základních jednotek';
$Definition['Quest']['Battle_07']['todo'] = [0 => 'Otevři na mapě volnou oázu a zaútoč na ni. ',];
$Definition['Quest']['Battle_08']['questTitle'] = '10 dobrodružství ';
$Definition['Quest']['Battle_08']['questDescription'] = 'Posílej hrdinu dál na dobrodružství. Po dokončení 10 dobrodružství se můžeš účastnit aukcí a obchodovat s předměty s ostatními hráči.';
$Definition['Quest']['Battle_08']['questDescriptionReward'] = 'Gratuluji, teď můžeš používat aukční dům. Vezmi si toto stříbro, ať máš hned na obchodování.';
$Definition['Quest']['Battle_08']['rewardDescription'] = '500 stříbra';
$Definition['Quest']['Battle_08']['todo'] = [0 => 'Dokonči 10 dobrodružství',];
$Definition['Quest']['Battle_09']['questTitle'] = 'Aukce';
$Definition['Quest']['Battle_09']['questDescription'] = 'Jdi do aukčního domu a podívej se, jaké předměty jsou právě v nabídce. Možná chceš část kořisti z dobrodružství proměnit ve stříbro?';
$Definition['Quest']['Battle_09']['questDescriptionReward'] = 'Skvělé, teď víš, jak obchodovat s výbavou a spotřebními předměty s ostatními hráči.';
$Definition['Quest']['Battle_09']['rewardDescription'] = buildResourcesReward([280, 120, 220, 110]);
$Definition['Quest']['Battle_09']['todo'] = [0 => 'Vytvoř aukci nebo přihoď v aukci.',];
$Definition['Quest']['Battle_10']['questTitle'] = 'Rozšiř kasárny ';
$Definition['Quest']['Battle_10']['questDescription'] = 'Rozšiř teď kasárny. Splníš tím předpoklady pro odemčení dalších budov.';
$Definition['Quest']['Battle_10']['questDescriptionReward'] = 'Výborně. Tvé jednotky se teď cvičí rychleji a můžeš postavit akademii.';
$Definition['Quest']['Battle_10']['rewardDescription'] = buildResourcesReward([440, 290, 430, 240]);
$Definition['Quest']['Battle_10']['todo'] = [0 => 'Rozšiř kasárny na úroveň 3. ',];
$Definition['Quest']['Battle_11']['questTitle'] = 'Postav akademii';
$Definition['Quest']['Battle_11']['questDescription'] = 'V akademii lze vyzkoumat nové a silnější jednotky pro tvou vesnici. Některé jednotky jsou velmi drahé a mají před vyzkoumáním vysoké předpoklady.';
$Definition['Quest']['Battle_11']['questDescriptionReward'] = 'Dobrá práce. Brzy se dozvíš více o vojácích svého národa.';
$Definition['Quest']['Battle_11']['rewardDescription'] = buildResourcesReward([210, 170, 245, 115]);
$Definition['Quest']['Battle_11']['todo'] = [0 => 'Postav teď akademii.',];
$Definition['Quest']['Battle_12']['questTitle'] = 'Vyzkoumej jednotku';
$Definition['Quest']['Battle_12']['questDescription'] = 'Podívej se na možnosti výzkumu. Jsou tu pěší a jízdní jednotky i obléhací stroje. Jednotky se specializují hlavně na útok, nebo na obranu.';
$Definition['Quest']['Battle_12']['questDescriptionReward'] = 'Samotný výzkum samozřejmě nestačí, jednotky je potřeba také vycvičit.';
$Definition['Quest']['Battle_12']['rewardDescription'] = buildResourcesReward([450, 435, 515, 550]);
$Definition['Quest']['Battle_12']['todo'] = [0 => 'Vyzkoumej další typ jednotky.',];
$Definition['Quest']['Battle_13']['questTitle'] = 'Postav kovárnu ';
$Definition['Quest']['Battle_13']['questDescription'] = 'V kovárně můžeš lépe vyzbrojit a vybavit své vojáky. Kovárna je navíc potřeba ke stavbě dalších vojenských budov.';
$Definition['Quest']['Battle_13']['questDescriptionReward'] = 'Výborně. Teď můžeš své vojáky lépe vybavit.';
$Definition['Quest']['Battle_13']['rewardDescription'] = buildResourcesReward([500, 400, 700, 400]);
$Definition['Quest']['Battle_13']['todo'] = [0 => 'Postav kovárnu.',];
$Definition['Quest']['Battle_14']['questTitle'] = 'Vylepši jednotky';
$Definition['Quest']['Battle_14']['questDescription'] = 'Vylepšení výbavy vojáků není levné. Čím víc vojáků máš, tím víc se vylepšení vyplatí. Tentokrát získáš víc, než kolik stálo.';
$Definition['Quest']['Battle_14']['questDescriptionReward'] = 'Vyzkoumej v kovárně vylepšení jednotky.';
$Definition['Quest']['Battle_14']['rewardDescription'] = '

								<img src="img/x.gif" class="questRewardTypeItem item112"> <span class="questRewardValue">' . Quest::calcEffect(10, 'item', true) . '</span>

							';
$Definition['Quest']['Battle_14']['todo'] = [0 => 'Dokonči vylepšení typu jednotky.',];
$Definition['Quest']['Battle_15']['questTitle'] = 'Dokonči 5 dobrodružství';
$Definition['Quest']['Battle_15']['questDescription'] = 'Více dobrodružství znamená více kořisti a zkušeností. Udržuj hrdinu aktivního, ale dopřej mu odpočinek, když má málo zdraví.';
$Definition['Quest']['Battle_15']['questDescriptionReward'] = 'Mastmi můžeš hrdinu léčit. Když masti nasadíš, použijí se, jakmile hrdina utrží zranění.';
$Definition['Quest']['Battle_15']['rewardDescription'] = '<img src="img/x.gif" class="questRewardTypeItem item106"> <span class="questRewardValue">' . Quest::calcEffect(15, 'item', true) . '</span>';
$Definition['Quest']['Battle_15']['todo'] = [0 => 'Dokonči 5 dobrodružství',];
$Definition['Quest']['economy_01_name'] = 'Železný důl';
$Definition['Quest']['economy_02_name'] = 'Více surovin ';
$Definition['Quest']['economy_03_name'] = 'Sýpka';
$Definition['Quest']['economy_04_name'] = 'Vše na jedničku ';
$Definition['Quest']['economy_05_name'] = 'Na dvojku!';
$Definition['Quest']['economy_06_name'] = 'tržiště';
$Definition['Quest']['economy_07_name'] = 'Obchod';
$Definition['Quest']['economy_08_name'] = 'Vše na 2';
$Definition['Quest']['economy_09_name'] = 'Sklad surovin úrovně 3';
$Definition['Quest']['economy_10_name'] = 'Sýpka úrovně 3';
$Definition['Quest']['economy_11_name'] = 'Mlýn';
$Definition['Quest']['economy_12_name'] = 'Vše na 5';
$Definition['Quest']['Economy_01']['questTitle'] = 'Železný důl';
$Definition['Quest']['Economy_01']['questDescription'] = 'Zadej stavbu železného dolu! Tvým hlavním cílem je stále vysoká produkce surovin, abys mohl rychle růst.';
$Definition['Quest']['Economy_01']['questDescriptionReward'] = 'Vyšší produkce železa ve tvé vesnici. Bonus produkce ti pomůže produkci určité suroviny ještě zvýšit.';
$Definition['Quest']['Economy_01']['rewardDescription'] = Quest::calcEffect(86400, 'productionBoost', true) . ' bonus +25 % k produkci všech surovin';
$Definition['Quest']['Economy_01']['todo'] = [0 => 'Zahaj stavbu železného dolu.',];
$Definition['Quest']['Economy_02']['questTitle'] = 'Více surovin';
$Definition['Quest']['Economy_02']['questDescription'] = 'Rozšiř po jednom poli dřeva, hlíny, železa a obilí na úroveň 1. Ke splnění úkolu musíš mít alespoň 2 pole od každé suroviny nad úrovní 0. Dokud máš aktivní Travian PLUS, můžeš vždy zadat ještě jednu stavbu najednou.';
$Definition['Quest']['Economy_02']['questDescriptionReward'] = 'Gratuluji! Tvá vesnice roste a vzkvétá...';
$Definition['Quest']['Economy_02']['rewardDescription'] = 'Jeden den bonusu +25 % k produkci všech surovin';
$Definition['Quest']['Economy_02']['todo'] = [0 => 'Rozšiř ještě jedno pole od každé suroviny na úroveň 1.',];
$Definition['Quest']['Economy_03']['questTitle'] = 'Sýpka';
$Definition['Quest']['Economy_03']['questDescription'] = 'Pro uskladnění více obilí potřebuješ sýpku. Svůj současný limit skladování najdeš na liště surovin.';
$Definition['Quest']['Economy_03']['questDescriptionReward'] = 'Pěkně! Sýpka ti teď umožní uskladnit více obilí.';
$Definition['Quest']['Economy_03']['rewardDescription'] = buildResourcesReward([250, 290, 100, 130]);
$Definition['Quest']['Economy_03']['todo'] = [0 => 'Postav sýpku.',];
$Definition['Quest']['Economy_04']['questTitle'] = 'Vše na jedničku ';
$Definition['Quest']['Economy_04']['questDescription'] = 'Na začátku je nejlepší soustředit se na suroviny. Rozšiř prosím všechna surovinová pole na úroveň 1.';
$Definition['Quest']['Economy_04']['questDescriptionReward'] = 'Produkce surovin se pěkně rozvíjí. Brzy můžeme začít stavět další budovy ve vesnici.';
$Definition['Quest']['Economy_04']['rewardDescription'] = buildResourcesReward([400, 460, 330, 270]);
$Definition['Quest']['Economy_04']['todo'] = [0 => 'Rozšiř všechna surovinová pole na úroveň 1.',];
$Definition['Quest']['Economy_05']['questTitle'] = 'Na dvojku! ';
$Definition['Quest']['Economy_05']['questDescription'] = 'Dobrá práce! Pokud potřebuješ více informací o produkci, klikni na své zásoby.';
$Definition['Quest']['Economy_05']['questDescriptionReward'] = 'Dobrá práce! Pokud potřebuješ více informací o produkci, klikni na své zásoby.';
$Definition['Quest']['Economy_05']['rewardDescription'] = buildResourcesReward([240, 255, 190, 160]);
$Definition['Quest']['Economy_05']['todo'] = [0 => 'Rozšiř od každé suroviny 1 pole na úroveň 2.',];
$Definition['Quest']['Economy_06']['questTitle'] = 'Tržiště ';
$Definition['Quest']['Economy_06']['questDescription'] = 'Když ti nějaká surovina chybí, můžeš ji na tržišti vyměnit s ostatními hráči za jiné suroviny. Ke stavbě malého tržiště potřebuješ větší hlavní budovu.';
$Definition['Quest']['Economy_06']['questDescriptionReward'] = 'Tržiště je hotové a teď můžeš obchodovat s ostatními hráči. Nenech se ale nachytat na opravdu špatné nabídky!';
$Definition['Quest']['Economy_06']['rewardDescription'] = '
								<img src="img/x.gif" class="questRewardTypeResource questRewardTypeWood"> <span class="questRewardValue">' . Quest::getInstance()->multiply(600) . '</span>
							';
$Definition['Quest']['Economy_06']['todo'] = [0 => 'Postav tržiště ',];
$Definition['Quest']['Economy_07']['questTitle'] = 'Obchod ';
$Definition['Quest']['Economy_07']['questDescription'] = 'Existující nabídky na tržišti uvidíš po kliknutí na \\"koupit\\". Zkontroluj směnný kurz a vzdálenost. Pokud nenajdeš vhodnou nabídku, klikni na \\"nabídnout\\" a vytvoř nabídku sám.';
$Definition['Quest']['Economy_07']['questDescriptionReward'] = 'Skvělé, zahájil jsi svůj první obchod.';
$Definition['Quest']['Economy_07']['rewardDescription'] = buildResourcesReward([100, 99, 99, 99]);
$Definition['Quest']['Economy_07']['todo'] = [0 => 'Vytvoř nabídku na tržišti nebo nějakou přijmi',];
$Definition['Quest']['Economy_08']['questTitle'] = 'Vše na 2';
$Definition['Quest']['Economy_08']['questDescription'] = 'Než začneš stavět dražší budovy, měli bychom dál zvýšit produkci surovin. Rozšiř všechna surovinová pole na úroveň 2.';
$Definition['Quest']['Economy_08']['questDescriptionReward'] = 'Gratuluji! Produkce surovin se pěkně rozvíjí.';
$Definition['Quest']['Economy_08']['rewardDescription'] = buildResourcesReward([400, 400, 400, 200]);
$Definition['Quest']['Economy_08']['todo'] = [0 => 'Rozšiř všechna surovinová pole na úroveň 2',];
$Definition['Quest']['Economy_09']['questTitle'] = 'Sklad surovin úrovně 3 ';
$Definition['Quest']['Economy_09']['questDescription'] = 'Je čas přizpůsobit sklad surovin zvýšené produkci. Nečekaná kořist hrdiny může sklad také přeplnit.';
$Definition['Quest']['Economy_09']['questDescriptionReward'] = 'Moc dobře, teď už žádné cenné suroviny nepřijdou nazmar.';
$Definition['Quest']['Economy_09']['rewardDescription'] = buildResourcesReward([620, 750, 560, 230]);
$Definition['Quest']['Economy_09']['todo'] = [0 => 'Rozšiř sklad surovin na úroveň 3',];
$Definition['Quest']['Economy_10']['questTitle'] = 'Sýpka úrovně 3';
$Definition['Quest']['Economy_10']['questDescription'] = 'Čím vyšší produkce, tím rychleji se sklady plní. Měl bys rozšířit i sýpku.';
$Definition['Quest']['Economy_10']['questDescriptionReward'] = 'V sýpce je teď zase místo, takže produkce může pokračovat i v tvé nepřítomnosti.';
$Definition['Quest']['Economy_10']['rewardDescription'] = buildResourcesReward([880, 1020, 590, 320]);
$Definition['Quest']['Economy_10']['todo'] = [0 => 'Rozšiř sýpku na úroveň 3.',];
$Definition['Quest']['Economy_11']['questTitle'] = 'Mlýn';
$Definition['Quest']['Economy_11']['questDescription'] = 'Mlýn zvyšuje produkci všech obilných polí. Aby se jeho cena vyplatila, potřebuješ dostatečně vysokou základní produkci.';
$Definition['Quest']['Economy_11']['questDescriptionReward'] = 'Teď máš pro další stavby k dispozici hodně volného obilí. Existují i budovy, které zvyšují produkci ostatních surovin.';
$Definition['Quest']['Economy_11']['rewardDescription'] = 'Mlýn úrovně 2';
$Definition['Quest']['Economy_11']['todo'] = [0 => 'Rozšiř jedno obilné pole na úroveň 5',];
$Definition['Quest']['Economy_12']['questTitle'] = 'Vše na 5';
$Definition['Quest']['Economy_12']['questDescription'] = 'Potřebuješ mnohem vyšší produkci, abys nemusel dlouho čekat, než si budeš moci dovolit budovy a osadníky potřebné pro druhou vesnici. Rozšiř všechna surovinová pole na úroveň 5.';
$Definition['Quest']['Economy_12']['questDescriptionReward'] = 'Výborně, tvá produkce je teď rychlejší a můžeš toho postavit víc.';
$Definition['Quest']['Economy_12']['rewardDescription'] = Quest::calcEffect(86400, 'productionBoost', true) . ' bonus +25 % k produkci všech surovin';
$Definition['Quest']['Economy_12']['todo'] = [0 => 'Rozšiř všechna surovinová pole na úroveň 5.',];
$Definition['Quest']['World_01']['questTitle'] = 'Zobrazit statistiky';
$Definition['Quest']['World_01']['questDescription'] = 'Ve světě Travianu soupeříš s tisíci dalšími hráči. Ve statistikách zjistíš více o své pozici ve hře';
$Definition['Quest']['World_01']['questDescriptionReward'] = 'Kromě pořadí tam najdeš i další užitečné informace. Záložka Top 10 ukazuje nejsilnější útočníky a nejúspěšnější lupiče.';
$Definition['Quest']['World_01']['rewardDescription'] = buildResourcesReward([90, 120, 60, 30]);
$Definition['Quest']['World_01']['todo'] = [0 => 'Otevři statistiky a porovnej se s ostatními hráči. ',];
$Definition['Quest']['World_02']['questTitle'] = 'Změň jméno vesnice ';
$Definition['Quest']['World_02']['questDescription'] = 'Jméno vesnice, které si sám zvolíš, ukazuje ostatním hráčům, že tvou říši někdo aktivně vede.';
$Definition['Quest']['World_02']['questDescriptionReward'] = 'Pěkně, teď jsi udělal první krok k tomu, abys ve světě Travianu zanechal stopu.';
$Definition['Quest']['World_02']['rewardDescription'] = Quest::calcEffect(100, 'cp', true) . ' kulturních bodů';
$Definition['Quest']['World_02']['todo'] = [0 => 'Změň jméno vesnice na ceduli vesnice.',];
$Definition['Quest']['World_03']['questTitle'] = 'Hlavní budova úrovně 3 ';
$Definition['Quest']['World_03']['questDescription'] = 'Větší hlavní budova odemyká nové budovy a zrychluje práci stavitelů. Rychlejší stavění se ale vyplatí jen tehdy, když produkuješ dost surovin.';
$Definition['Quest']['World_03']['questDescriptionReward'] = 'Skvělé, větší hlavní budova ti teď umožní stavět další budovy, které jsi právě odemkl.';
$Definition['Quest']['World_03']['rewardDescription'] = buildResourcesReward([170, 100, 130, 70]);
$Definition['Quest']['World_03']['todo'] = [0 => 'Rozšiř hlavní budovu na úroveň 3.',];
$Definition['Quest']['World_04']['questTitle'] = 'Postav ambasádu ';
$Definition['Quest']['World_04']['questDescription'] = 'Svět Travianu je nebezpečné místo a musíš se umět bránit. Nejlepší další obranu nabízejí silní spojenci. Postav ambasádu, abys mohl vstoupit do aliance.';
$Definition['Quest']['World_04']['questDescriptionReward'] = 'Výborně, teď můžeš přijímat pozvánky do aliance. Pozvánky najdeš v ambasádě.';
$Definition['Quest']['World_04']['rewardDescription'] = buildResourcesReward([215, 145, 195, 50]);
$Definition['Quest']['World_04']['todo'] = [0 => 'Postav ambasádu.',];
$Definition['Quest']['World_05']['questTitle'] = 'Otevři mapu ';
$Definition['Quest']['World_05']['questDescription'] = 'Mapa ti ukazuje svět Travianu. Prohlédni si sousedy, najdi spojence a odhal hrozby.';
$Definition['Quest']['World_05']['questDescriptionReward'] = 'Jsou poblíž silní hráči nebo aliance? Mapa ti také pomůže najít oázy a místa, kde můžeš založit nové vesnice.';
$Definition['Quest']['World_05']['rewardDescription'] = buildResourcesReward([90, 160, 90, 95]);
$Definition['Quest']['World_05']['todo'] = [0 => 'Otevři mapu v menu.',];
$Definition['Quest']['World_06']['questTitle'] = 'Přečti zprávu';
$Definition['Quest']['World_06']['questDescription'] = 'Právě jsi dostal zprávu s užitečnými tipy. Nepřečtené zprávy poznáš podle čísla nad tlačítkem. Podívej se na ni.';
$Definition['Quest']['World_06']['questDescriptionReward'] = 'Zprávami komunikuješ s ostatními hráči. Vždy se vyplatí být klidný a zdvořilý, i když spolu bojujete.';
$Definition['Quest']['World_06']['rewardDescription'] = buildResourcesReward([280, 315, 200, 145]);
$Definition['Quest']['World_06']['todo'] = [0 => 'Otevři přehled zpráv a přečti si zprávu od zadavatele úkolů! ',];
$Definition['Quest']['World_07']['questTitle'] = 'Zlato navíc ';
$Definition['Quest']['World_07']['questDescription'] = 'Během výuky jsi už zlatem urychlil stavby. V obchodě se zlatem zjistíš, k čemu dalšímu můžeš zlato použít.';
$Definition['Quest']['World_07']['questDescriptionReward'] = 'Tady máš zase trochu zlata zdarma, abys mohl využít některé zlaté výhody.';
$Definition['Quest']['World_07']['rewardDescription'] = '

								<img src="img/x.gif" class="questRewardTypeGold"> <span class="questRewardValue">20</span>

							';
$Definition['Quest']['World_07']['todo'] = [0 => 'Podívej se na výhody, které si můžeš koupit za zlato.',];
$Definition['Quest']['World_08']['questTitle'] = 'Aliance ';
$Definition['Quest']['World_08']['questDescription'] = 'Hledej spojence a vstup do aliance. Pokud zatím nemáš žádné kontakty, podívej se na aliance hráčů ve svém okolí nebo hledej alianci na fóru.';
$Definition['Quest']['World_08']['questDescriptionReward'] = 'Máme skvělý začátek. Čím silnější a aktivnější je každý hráč, tím silnější budete jako tým. Už víte, jak si navzájem hlásit útoky a žádat o pomoc?';
$Definition['Quest']['World_08']['rewardDescription'] = buildResourcesReward([295, 210, 235, 185]);
$Definition['Quest']['World_08']['todo'] = [0 => 'Vstup do aliance.',];
$Definition['Quest']['World_09']['questTitle'] = 'Hlavní budova úrovně 5';
$Definition['Quest']['World_09']['questDescription'] = 'Je čas rozšířit hlavní budovu, abys mohl stavět nové budovy. Nezapomeň zároveň pečovat o produkci surovin.';
$Definition['Quest']['World_09']['questDescriptionReward'] = 'Skvělé, že? Teď se tvé budovy budou stavět rychleji. Vyzvedni si odměnu a připrav se na další úkol.';
$Definition['Quest']['World_09']['rewardDescription'] = buildResourcesReward([570, 470, 560, 265]);
$Definition['Quest']['World_09']['todo'] = [0 => ' Rozšiř hlavní budovu na úroveň 5.',];
$Definition['Quest']['World_10']['questTitle'] = 'Sídlo vlády ';
$Definition['Quest']['World_10']['questDescription'] = 'Postav teď sídlo vlády, abys mohl brzy založit novou vesnici. Pokud si nejsi jistý, jestli má tato vesnice zůstat tvým hlavním městem, vyber rezidenci.';
$Definition['Quest']['World_10']['questDescriptionReward'] = 'Tato budova je potřeba k založení nebo dobytí nové vesnice. Její úroveň omezuje počet možných rozšíření.';
$Definition['Quest']['World_10']['rewardDescription'] = buildResourcesReward([525, 420, 620, 335]);
$Definition['Quest']['World_10']['todo'] = [0 => 'Sídlo vlády',];
$Definition['Quest']['World_11']['questTitle'] = 'Kulturní body';
$Definition['Quest']['World_11']['questDescription'] = 'Abys mohl ve své říši vládnout více vesnicím, potřebuješ kulturní body. Přehled v rezidenci nebo paláci ti ukáže, kolik ti chybí a jak dlouho to bude trvat.';
$Definition['Quest']['World_11']['questDescriptionReward'] = 'V seznamu vesnic také vidíš aktuální stav možných nových vesnic a počet chybějících kulturních bodů. V \\"Odpovědích\\" zjistíš, jak kulturní body rychle zvýšit.';
$Definition['Quest']['World_11']['rewardDescription'] = buildResourcesReward([650, 800, 740, 530]);
$Definition['Quest']['World_11']['todo'] = [0 => 'Otevři v rezidenci nebo paláci záložku kulturních bodů.',];
$Definition['Quest']['World_12']['questTitle'] = 'Sklad surovin úrovně 7 ';
$Definition['Quest']['World_12']['questDescription'] = 'Rozšiř sklad surovin a připrav se na založení nové vesnice. Současná kapacita brzy nebude stačit na potřebné budovy a osadníky.';
$Definition['Quest']['World_12']['questDescriptionReward'] = 'Skvělé, kapacita skladů by teď nějakou dobu měla stačit. Nezapomeň své cenné suroviny bránit nebo schovávat.';
$Definition['Quest']['World_12']['rewardDescription'] = buildResourcesReward([2650, 2150, 1810, 1320]);
$Definition['Quest']['World_12']['todo'] = [0 => 'Rozšiř sklad surovin na úroveň 7.',];
$Definition['Quest']['World_13']['questTitle'] = 'Hlášení z okolí';
$Definition['Quest']['World_13']['questDescription'] = 'Hlášení z okolí tě informují o událostech a změnách ve tvém sousedství.';
$Definition['Quest']['World_13']['questDescriptionReward'] = 'Od změn jmen po provedené loupeže a dobytí – stát se může mnoho. Doufám, že sis čtení hlášení užil.';
$Definition['Quest']['World_13']['rewardDescription'] = buildResourcesReward([800, 700, 750, 600]);
$Definition['Quest']['World_13']['todo'] = [0 => 'Otevři hlášení a přečti si hlášení z okolí.',];
$Definition['Quest']['World_14']['questTitle'] = 'Rezidence nebo palác úrovně 10 ';
$Definition['Quest']['World_14']['questDescription'] = 'Osadníky lze cvičit v paláci nebo rezidenci. Na záložce "Cvičit" uvidíš potřebnou úroveň budovy.';
$Definition['Quest']['World_14']['questDescriptionReward'] = 'Z každé vesnice můžeš ovládnout jen 2 až 3 nové vesnice. Pro novou vesnici ti teď chybí už jen 3 osadníci a spousta kulturních bodů.';
$Definition['Quest']['World_14']['rewardDescription'] = Quest::calcEffect(500, 'cp', true) . ' kulturních bodů. ';
$Definition['Quest']['World_14']['todo'] = [0 => 'Rozšiř rezidenci nebo palác na úroveň 10. ',];
$Definition['Quest']['World_15']['questTitle'] = 'Vycvič tři osadníky';
$Definition['Quest']['World_15']['questDescription'] = 'Osadníci při zakládání nové vesnice cestují vždy v malé skupině.';
$Definition['Quest']['World_15']['questDescriptionReward'] = 'Dobře své osadníky chraň před útoky, dokud nebudou připraveni vyrazit.';
$Definition['Quest']['World_15']['rewardDescription'] = buildResourcesReward([1050, 800, 900, 750]);
$Definition['Quest']['World_15']['todo'] = [0 => 'Vycvič 3 osadníky v rezidenci/paláci.',];
$Definition['Quest']['World_16']['questTitle'] = 'Zakládání nové vesnice';
$Definition['Quest']['World_16']['questDescription'] = 'Najdi na mapě dobré místo k osídlení. Chceš ho mít blízko své vesnice, s vyšší produkcí určité suroviny, nebo u mnoha oáz? Založ s osadníky druhou vesnici.';
$Definition['Quest']['World_16']['questDescriptionReward'] = "Skvělé, patříš mezi nejmocnější říše ve světě Travianu. Hraj dál a cvič spoustu jednotek k obraně před nepřáteli.";
$Definition['Quest']['World_16']['rewardDescription'] = Quest::calcEffect(172800, 'plus', true) . ' Travian Plus.';
$Definition['Quest']['World_16']['todo'] = [0 => 'Založ novou vesnici.',];
$Definition['Quest']['world_01_name'] = 'Zobrazit statistiky';
$Definition['Quest']['world_02_name'] = 'Změnit jméno vesnice';
$Definition['Quest']['world_03_name'] = 'Hlavní budova úrovně 3 ';
$Definition['Quest']['world_04_name'] = 'Postav ambasádu ';
$Definition['Quest']['world_05_name'] = 'Otevřít mapu';
$Definition['Quest']['world_06_name'] = 'Přečti zprávu';
$Definition['Quest']['world_07_name'] = 'Zlato navíc';
$Definition['Quest']['world_08_name'] = 'Aliance';
$Definition['Quest']['world_09_name'] = 'Hlavní budova úrovně 5';
$Definition['Quest']['world_10_name'] = 'Sídlo vlády';
$Definition['Quest']['world_11_name'] = 'Kulturní body';
$Definition['Quest']['world_12_name'] = 'Sklad surovin úrovně 7';
$Definition['Quest']['world_13_name'] = 'Hlášení z okolí';
$Definition['Quest']['world_14_name'] = 'Rezidence nebo palác úrovně 10 ';
$Definition['Quest']['world_15_name'] = '3 osadníci';
$Definition['Quest']['world_16_name'] = 'Nová vesnice';
//
$Definition['RallyPoint']['greyAreaNewVillageCaution'] = 'Pozor: Pokud na těchto souřadnicích založíš vesnici, Nataři se tě budou snažit napadnout a vyhnat z této oblasti.<br />Vesnice v této oblasti nebudou produkovat kulturní body.';
$Definition['RallyPoint']['Changed successfully: %s will be the new home village for hero'] = 'Úspěšně změněno: %s bude novou domovskou vesnicí hrdiny.';
$Definition['RallyPoint']['nowSettlersWillGoNeedsXResources'] = 'Osadníci teď vyrazí založit novou vesnici.<br />Založení nové vesnice stojí %s od každé suroviny (dřevo, hlína, železo, obilí).';
$Definition['RallyPoint']['Settlers']['notEnoughResources'] = 'Nedostatek surovin.';
$Definition['RallyPoint']['reinforcement'] = 'Posily';
$Definition['RallyPoint']['normal'] = 'Útok';
$Definition['RallyPoint']['raid'] = 'Loupež';
$Definition['RallyPoint']['attack'] = 'Útok';
$Definition['RallyPoint']['Village'] = 'Vesnice';
$Definition['RallyPoint']['changeHeroHomeVillage'] = 'Změnit domov hrdiny';
$Definition['RallyPoint']['or'] = 'nebo';
$Definition['RallyPoint']['number'] = 'počet';
$Definition['RallyPoint']['withdraw'] = 'Stáhnout';
$Definition['RallyPoint']['sendBack'] = 'Poslat zpět';
$Definition['RallyPoint']['reviving'] = 'Oživování';
$Definition['RallyPoint']['createNewVillage'] = 'Založit novou vesnici';
$Definition['RallyPoint']['loyaltyReducedBy'] = 'Loajalita snížena o ';
$Definition['RallyPoint']['spy'] = 'Špehovat';
$Definition['RallyPoint']['supply'] = 'Posily';
$Definition['RallyPoint']['Errors']['activeVillageChanged'] = 'Aktivní vesnice byla změněna.';
$Definition['RallyPoint']['Errors']['EnterCoordinateOrDname'] = 'Zadej jméno nebo souřadnice.';
$Definition['RallyPoint']['Errors']['noVillageWithThisName'] = 'Vesnice neexistuje.';
$Definition['RallyPoint']['Errors']['noVillageInCoordinate'] = 'Souřadnice neexistují.';
$Definition['RallyPoint']['Errors']['NatarCapitalError'] = 'Do natarského hlavního města nemůžeš posílat posily!';
$Definition['RallyPoint']['Errors']['noTroopsSelected'] = 'Musíš označit alespoň jednu jednotku.';
$Definition['RallyPoint']['Errors']['playerHasBeginnerProtection'] = 'Tento hráč je pod ochranou.';
$Definition['RallyPoint']['Errors']['cantAttackUrSelf'] = 'Tvé jednotky už jsou ve tvé vesnici';
$Definition['RallyPoint']['Errors']['cantAttackDuringProtection'] = 'Během ochrany nemůžeš útočit.';
$Definition['RallyPoint']['Errors']['reallyAttackOwn?'] = 'Opravdu chceš zaútočit sám na sebe?';
$Definition['RallyPoint']['Errors']['reallyAttackFriend?'] = 'Opravdu chceš zaútočit na svého spojence?';
$Definition['RallyPoint']['Errors']['protectionWillBeGone'] = 'Ochrana bude zrušena. Jsi si jistý?';
$Definition['RallyPoint']['Errors']['youCannotAttackArtifactWhileInProtection'] = 'Během ochrany nemůžeš zaútočit na vesnici s artefaktem.';
$Definition['RallyPoint']['Errors']['youAreBanned'] = 'Jsi zablokován.';
$Definition['RallyPoint']['Errors']['playerBanned'] = 'Tento účet je zablokován kvůli porušení pravidel hry';
$Definition['RallyPoint']['Errors']['cantSendReinforcementsDuringProtection'] = 'Během ochrany nemůžeš posílat posily.';
$Definition['RallyPoint']['Errors']['heroDeployError'] = 'Hrdinu sem můžeš přesunout jen jako posilu. Cílová vesnice musí mít shromaždiště.';
$Definition['RallyPoint']['Errors']['serverFinished'] = 'Hra skončila.';
$Definition['RallyPoint']['Errors']['playerIsInVacation'] = 'Hráč je na dovolené.';
$Definition['RallyPoint']['Errors']['farmsAreProtectedTill'] = 'Farmy jsou chráněné do %s.';
$Definition['RallyPoint']['reinforcementForVillageName'] = 'Posila pro %s';
$Definition['RallyPoint']['send'] = 'Odeslat';
$Definition['RallyPoint']['edit'] = 'Upravit';
$Definition['RallyPoint']['reinforcementForPlayerName'] = 'Posila pro %s';
$Definition['RallyPoint']['imprisendInVillage'] = 'Uvězněno v %s';
$Definition['RallyPoint']['imprisendPlayer'] = 'Uvězněno: %s';
$Definition['RallyPoint']['showAll'] = 'zobrazit vše';
$Definition['RallyPoint']['no_incoming_troops_error'] = 'Žádné přicházející jednotky.';
$Definition['RallyPoint']['no_outgoing_troops_error'] = 'Žádné jednotky na cestě.';
$Definition['RallyPoint']['no_outvillage_troops_error'] = 'Žádné jednotky mimo vesnici.';
$Definition['RallyPoint']['return'] = 'Návrat';
$Definition['RallyPoint']['units'] = 'Jednotky';
$Definition['RallyPoint']['ownTroops'] = 'Vlastní jednotky';
$Definition['RallyPoint']['from'] = 'z';
$Definition['RallyPoint']['CrannyForest'] = 'Lesní úkryt';
$Definition['RallyPoint']['adventure'] = 'Dobrodružství';
$Definition['RallyPoint']['occupiedOasis'] = 'Obsazená oáza';
$Definition['RallyPoint']['unoccupiedOasis'] = 'Neobsazená oáza';
$Definition['RallyPoint']['ArrivalIn'] = 'Příchod za';
$Definition['RallyPoint']['against'] = 'proti';
$Definition['RallyPoint']['for'] = 'pro';
$Definition['RallyPoint']['of'] = '';
$Definition['RallyPoint']['catapultTargets'] = 'Cíl';
//attack types
$Definition['RallyPoint']['inAttack'] = $Definition['RallyPoint']['outAttack'] = $Definition['RallyPoint']['inAttackOasis'] = 'Útok';
$Definition['RallyPoint']['inRaid'] = $Definition['RallyPoint']['outRaid'] = $Definition['RallyPoint']['inRaidOasis'] = 'Loupež';
$Definition['RallyPoint']['inSupply'] = $Definition['RallyPoint']['outSupply'] = $Definition['RallyPoint']['inSupplyOasis'] = 'Posily';
$Definition['RallyPoint']['outSettlers'] = 'Založit novou vesnici';
$Definition['RallyPoint']['outSpy'] = 'Špehovat';
$Definition['RallyPoint']['myOasis'] = 'Moje oáza';
$Definition['RallyPoint']['outEscape'] = 'Uniknout do lesního úkrytu';
$Definition['RallyPoint']['filters'][1] = 'Přicházející jednotky';
$Definition['RallyPoint']['filters'][2] = 'Odcházející jednotky';
$Definition['RallyPoint']['filters'][3] = 'Jednotky v této vesnici a jejích oázách';
$Definition['RallyPoint']['filters'][4] = 'Jednotky v jiných vesnicích nebo oázách';
$Definition['RallyPoint']['SubFilters'][1] = 'Odcházející útoky/loupeže';
$Definition['RallyPoint']['SubFilters'][2] = 'Přicházející jednotky';
$Definition['RallyPoint']['SubFilters'][3] = 'Posily';
$Definition['RallyPoint']['SubFilters'][4] = 'Odcházející útoky/loupeže';
$Definition['RallyPoint']['SubFilters'][5] = 'Vlastní posily';
$Definition['RallyPoint']['SubFilters'][6] = 'Řadit podle času odeslání';
$Definition['RallyPoint']['goldClubEvasionDesc'] = 'Se Zlatým klubem můžeš jednotkám ve svém hlavním městě nařídit, aby před útoky utekly.';
$Definition['RallyPoint']['evasion in capital'] = 'Únik v hlavním městě';
$Definition['RallyPoint']['goldclub'] = 'Zlatý klub';
$Definition['RallyPoint']['EvasionSettings'] = 'Nastavení úniku';
$Definition['RallyPoint']['HeroShowDesc'] = ' Tvůj hrdina zůstane vždy s jednotkami';
$Definition['RallyPoint']['HeroHideDesc'] = 'Hrdina se při útoku na domovskou vesnici ukryje.';
$Definition['RallyPoint']["EvasionDesc"] = 'Jednotky odejdou v okamžiku útoku a vrátí se 180 sekund poté. Jednotky utečou jen tehdy, když se během 10 sekund před útokem domů NEVRACEJÍ žádné jednotky, s výjimkou jednotek vracejících se z použití této možnosti. Utéct mohou všechny jednotky vycvičené ve vesnici, posily ale NEutečou.';
$Definition['RallyPoint']['Management'] = 'Správa';
$Definition['RallyPoint']['overview'] = 'Přehled';
$Definition['RallyPoint']['sendTroops'] = 'Poslat jednotky';
$Definition['RallyPoint']['combatSimulator'] = 'Simulátor boje';
$Definition['RallyPoint']['farmlist'] = 'Seznam farem';
$Definition['RallyPoint']['needClubToBeActive'] = 'Pro tuto funkci potřebuješ aktivovaný Zlatý klub.';
$Definition['RallyPoint']['This tab is set as favourite'] = 'Tato záložka je nastavena jako oblíbená';
$Definition['RallyPoint']['Set tab x as favourite'] = 'Nastavit záložku %s jako oblíbenou';
$Definition['RallyPoint']['ArrivalIn'] = 'Příchod za';
$Definition['RallyPoint']['kill'] = 'Zabít';
$Definition['RallyPoint']['target'] = 'Cíl';
$Definition['RallyPoint']['player'] = 'Hráč';
$Definition['RallyPoint']['catapult only attacks in normal type'] = 'Katapulty útočí jen při <b>normálním</b> útoku';
$Definition['RallyPoint']['troops'] = 'Jednotky';
$Definition['RallyPoint']['random'] = 'Náhodně';
$Definition['RallyPoint']['willBeAttackedTarget'] = 'Bude zasaženo katapulty';
$Definition['RallyPoint']['options'] = 'Možnosti';
$Definition['RallyPoint']['Consumption'] = 'Spotřeba';
$Definition['RallyPoint']['withdraw'] = 'stáhnout';
$Definition['RallyPoint']['TroopKillDesc'] = 'Opravdu chceš jednotky zabít?';
$Definition['RallyPoint']['back'] = 'Poslat zpět';
$Definition['RallyPoint']['free'] = 'Propustit';
$Definition['RallyPoint']['spyTarget'] = 'Typ špehování';
$Definition['RallyPoint']['spyTargetTroopsBuildings'] = 'Zjistit obranu a jednotky';
$Definition['RallyPoint']['spyTargetTroopsResources'] = 'Zjistit suroviny a jednotky';
//
$Definition['reCaptcha']['title'] = 'Systém rozpoznání botů';
$Definition['reCaptcha']['desc'] = 'Potřebujeme ověřit, že hru nehraje robot.<br /><br />Klikni na zaškrtávací políčko a potvrď, že nejsi robot.';
$Definition['reCaptcha']['Sorry you submitted wrong answer'] = 'Bohužel, tvá odpověď není správná.';

$Definition['farmListLockHandle']['title'] = 'Systém rozpoznání botů';
$Definition['farmListLockHandle']['captcha'] = 'Captcha';
$Definition['farmListLockHandle']['submit'] = 'Odeslat';
$Definition['farmListLockHandle']['newCode'] = 'Vyžádat nový kód';
$Definition['farmListLockHandle']['desc'] = 'V krátké době jsi použil seznam farem víc, než se čekalo. Seznam farem teď nemůžeš používat. Účet odblokuješ zadáním textu níže do pole.';
$Definition['farmListLockHandle']['Sorry you submitted wrong answer'] = 'Bohužel, tvá odpověď není správná.';

//
$Definition['Reports'] = [
    "reportTypes" => [
        1 => 'Vítězství útočníka bez ztrát',
        0 => 'Hlášení',
        2 => 'Vítězství útočníka se ztrátami',
        3 => 'Porážka útočníka se ztrátami',
        4 => 'Vítězství obránce bez ztrát',
        5 => 'Vítězství obránce se ztrátami',
        6 => 'Porážka obránce se ztrátami',
        7 => 'Porážka obránce bez ztrát',
        8 => 'Posily',
        11 => 'Dřevo doručeno.',
        12 => 'Hlína doručena.',
        13 => 'Železo doručeno.',
        14 => 'Obilí doručeno.',
        15 => 'Úspěšné špehování útočníkem',
        16 => 'Úspěšné špehování útočníkem, ale obránce špehy odhalil',
        17 => 'Neúspěšné špehování útočníkem',
        18 => 'Úspěšná obrana proti špehování',
        19 => 'Neúspěšná obrana proti špehování',
        20 => 'Zvířata chycena',
        21 => 'Hlášení o dobrodružství',
        22 => 'Osadníci založili novou vesnici',
    ],
    'not_destroyed_reason' => [
        'disabled' => 'Vesnice nebyla zničena, protože ničení vesnic je vypnuto',
        'isWW' => 'Vesnice nebyla zničena, protože je to vesnice s divem světa',
        'isFarm' => 'Vesnice nebyla zničena, protože je to farmářská vesnice',
        'disabledCapitalOnZeroPop' => 'Vesnice nebyla zničena, protože hlavní město se při nule obyvatel neničí',
        'OnlyOneVillage' => 'Vesnice nebyla zničena, protože hráč má jen jednu vesnici',
        'ArtifactExists' => 'Vesnice nebyla zničena, protože obsahuje artefakt',
        'OnePlusWW' => 'Vesnice nebyla zničena, protože všechny zbývající vesnice jsou vesnice s divem světa',
        'NoCapitalSuccessor' => 'Vesnice nebyla zničena, protože se žádná běžná vesnice nemůže stát novým hlavním městem',
    ],
];
$Definition['Reports']['There was no village at target destination'] = 'V cíli nebyla žádná vesnice.';
$Definition['Reports']['newVillageCreatedSuccussFully'] = 'Nová vesnice byla úspěšně založena';
$Definition['Reports']['escapeNonCapitalErr'] = 'Tvé jednotky neutekly, protože únik je možný jen v hlavním městě.';
$Definition['Reports']['escapeTroopsComingErr'] = 'Tvé jednotky neutekly, protože se během 10 sekund vracely jiné jednotky.';
$Definition['Reports']['evasionNotEnabled'] = 'Jednotky neutekly, protože únik není zapnutý.';
$Definition['Reports']['escapeDisabledBecauseYourPopulationIsTooLow'] = 'Jednotky neutekly, protože tvá vesnice má příliš málo obyvatel.';
$Definition['Reports']['Overflowing reports will be deleted automatically, if they are older than %s hours'] = 'Přebytečná hlášení starší než %s hodin budou automaticky smazána.';
$Definition['Reports']['Archive||For this feature you need the Gold club activated'] = 'Archiv||Pro tuto funkci potřebuješ aktivovaný Zlatý klub.';
$Definition['Reports']['Add to farm list||For this feature you need the Gold club activated'] = 'Přidat do seznamu farem||Pro tuto funkci potřebuješ aktivovaný Zlatý klub';
$Definition['Reports']['Mark as read'] = 'Označit jako přečtené';
$Definition['Reports']['Mark as unread'] = 'Označit jako nepřečtené';
$Definition['Reports']['Combat simulator'] = 'Simulátor boje';
$Definition['Reports']['Repeat attack'] = 'Opakovat útok';
$Definition['Reports']['Tabs']['All'] = 'Vše';
$Definition['Reports']['Tabs']['Troops'] = 'Jednotky';
$Definition['Reports']['Tabs']['Trade'] = 'Obchod';
$Definition['Reports']['Tabs']['Miscellaneous'] = 'Různé';
$Definition['Reports']['Tabs']['Archive'] = 'Archiv';
$Definition['Reports']['Tabs']['Surrounding'] = 'Okolí';
$Definition['Reports']['Tabs']['Attacks'] = 'Útoky';
$Definition['Reports']['Tabs']['Defense'] = 'Obrana';
$Definition['Reports']['Tabs']['Spy'] = 'Špehovat';
$Definition['Reports']['Tabs']['Other'] = 'Ostatní';
$Definition['Reports']['needClub'] = 'Pro tuto funkci potřebuješ aktivní Zlatý klub.';
$Definition['Reports']['village totally destroyed'] = 'Vesnice zcela zničena.';
$Definition['Reports']['village x of player y was destroyed'] = 'Vesnice %s hráče %s byla zničena.';
$Definition['Reports']['adventureFailed'] = 'Dobrodružství nebylo úspěšné.';
$Definition['Reports']['Silver'] = 'Stříbro';
$Definition['Reports']['WWPlanCaptured'] = 'Stavební plán divu světa ukořistěn.';
$Definition['Reports']['heroArtifactCapture'] = 'Artefakt ukořistěn.';
$Definition['Reports']['x didnt damaged'] = '%s nebyl poškozen.';
$Definition['Reports']['TrapFreeAllianceAndMe'] = 'Osvobodil jsi %s svých jednotek a %s jednotek své aliance, ostatní byly zabity.';
$Definition['Reports']['TrapFreeMe'] = 'Osvobodil jsi %s svých jednotek, ostatní byly zabity.';
$Definition['Reports']['TrapFreeAlliance'] = 'Osvobodil jsi %s uvězněných jednotek své aliance, ostatní byly zabity.';
$Definition['Reports']['select all'] = 'Vybrat vše';
$Definition['Reports']['Unread'] = 'Nepřečtené';
$Definition['Reports']['location'] = 'místo';
$Definition['Reports']['distance'] = 'vzdálenost';
$Definition['Reports']['Read'] = 'Přečtené';
$Definition['Reports']['newer reports'] = 'Novější hlášení';
$Definition['Reports']['older reports'] = 'Starší hlášení';
$Definition['Reports']['Really delete this report?'] = 'Opravdu smazat toto hlášení?';
$Definition['Reports']['Delete report'] = 'Smazat hlášení';
$Definition['Reports']['Delete'] = 'Smazat';
$Definition['Reports']['Archive'] = 'Archiv';
$Definition['Reports']['Recover'] = 'Obnovit';
$Definition['Reports']['Access permissions'] = 'Přístupová oprávnění';
$Definition['Reports']['make opponent anonymous'] = 'skrýt jméno protivníka';
$Definition['Reports']['make myself anonymous'] = 'skrýt vlastní jméno';
$Definition['Reports']['hide own troops'] = 'skrýt vlastní jednotky';
$Definition['Reports']['hide opposing troops'] = 'skrýt jednotky protivníka';
$Definition['Reports']['Description:'] = 'Popis:';
$Definition['Reports']['VillageCaptured'] = 'Obyvatelé vesnice %s se rozhodli připojit k tvé říši.';
$Definition['Reports']['CantCaptureCapital'] = 'Hlavní město nemůžeš dobýt.';
$Definition['Reports']['culturePointsErr'] = 'Nedostatek kulturních bodů.';
$Definition['Reports']['rpExists'] = 'Rezidence nebo palác stále stojí.';
$Definition['Reports']['You can only have 1 ww village at a time'] = 'Najednou můžeš mít jen 1 vesnici s divem světa.';
$Definition['Reports']['You cannot capture your alliance members village'] = 'Vesnici člena své aliance nemůžeš dobýt.';
$Definition['Reports']['randomTargetsWereChosen'] = 'Byly vybrány náhodné cíle.';
$Definition['Reports']['defenderIsSupportedByTheFollowingArtifact'] = 'Obránce je chráněn tímto artefaktem: %s';
$Definition['Reports']['NoFreeAttackerTreasurySpace'] = 'Chybí pokladnice nebo je pokladnice plná.';
$Definition['Reports']['maxArtifactReached'] = 'Máš maximální možný počet artefaktů.';
$Definition['Reports']['TreasuryExists'] = 'Pokladnice stojí';
$Definition['Reports']['From'] = 'Od';
$Definition['Reports']['Village'] = 'Vesnice';
$Definition['Reports']['oasis'] = 'oáza';
$Definition['Reports']['Troops'] = 'Jednotky';
$Definition['Reports']['units'] = 'Jednotky';
$Definition['Reports']['Trapped'] = 'V zajetí';
$Definition['Reports']['casualties'] = 'Ztráty';
$Definition['Reports']['from oasis'] = 'z oázy';
$Definition['Reports']['you used x cages'] = 'Použil jsi %s klecí.';
$Definition['Reports']['x attacks y'] = '%s útočí na %s.';
$Definition['Reports']['x send resources to y'] = '%s posílá suroviny do %s.';
$Definition['Reports']['Bounty'] = 'Kořist';
$Definition['Reports']['exp'] = 'zkušenosti';
$Definition['Reports']['noValuableThingFound'] = 'Tvůj hrdina nic nenašel.';
$Definition['Reports']['injury'] = 'zdraví';
$Definition['Reports']['unknown'] = 'neznámé';
$Definition['Reports']['Sender'] = 'Odesílatel';
$Definition['Reports']['Recipient'] = 'Příjemce';
$Definition['Reports']['supplies'] = 'zásoby';
$Definition['Reports']['from alliance'] = 'z aliance';
$Definition['Reports']['x supplies y'] = '%s zásobuje %s';
$Definition['Reports']['Consumption'] = 'Spotřeba';
$Definition['Reports']['reinforced'] = 'posílil';
$Definition['Reports']['x reinforced y'] = '%s posiluje %s';
$Definition['Reports']['perHR'] = 'Za hodinu';
$Definition['Reports']['x destroyed'] = '%s zničen.';
$Definition['Reports']['reduced lvl from x to y'] = 'Úroveň budovy %s snížena z %s na %s.';
$Definition['Reports']['x did not damaged'] = '%s nebyl poškozen.';
$Definition['Reports']['you troops in village x were attacked'] = 'Tvé jednotky ve vesnici %s byly napadeny';
$Definition['Reports']['x is on adventure'] = '%s prozkoumává %s.';
$Definition['Reports']['An Oasis was plundered'] = 'Oáza byla vyloupena.';
$Definition['Reports']['x has conquered an oasis'] = '%s obsadil oázu.';
$Definition['Reports']['An Oasis was abandoned'] = 'Oáza byla opuštěna.';
$Definition['Reports']['x has founded y'] = '%s založil %s.';
$Definition['Reports']['x has conquered y'] = '%s dobyl %s.';
$Definition['Reports']['x has lost y'] = '%s ztratil %s.';
$Definition['Reports']['x renamed y to z'] = '%s přejmenoval %s na %s.';
$Definition['Reports']['A fight took at village name of player name'] = 'Bitva u %s hráče %s.';
$Definition['Reports']['noData'] = 'Nejsou k dispozici žádná hlášení.';
$Definition['Reports']['Subject'] = 'Předmět';
$Definition['Reports']['Sent'] = 'Odeslané';
//report types
$Definition['Reports']['AnimalsCaught'] = 'Zvířata chycena.';
$Definition['Reports']['x spies y'] = '%s špehuje %s.';
$Definition['Reports']['x founds a new village'] = '%s zakládá novou vesnici.';
$Definition['Reports']['occupiedOasis'] = 'Obsazená oáza';
$Definition['Reports']['unoccupiedOasis'] = 'Neobsazená oáza';
$Definition['Reports']['Attacker'] = 'Útočník';
$Definition['Reports']['Defender'] = 'Obránce';
$Definition['Reports']['Reinforcement'] = 'Posily';
$Definition['Reports']['Information'] = 'Informace';
$Definition['Reports']['Resources'] = 'Surovinové';
$Definition['Reports']['None of your soldiers returned'] = 'Žádný z tvých vojáků se nevrátil.';
$Definition['Reports']['None of your spies returned'] = 'Žádný z tvých špehů se nevrátil.';
$Definition['Reports']['Ram does not work on alliance members'] = 'Beranidla na členy aliance nepůsobí.';
$Definition['Reports']['Cata is disabled'] = 'Systém katapultů je vypnutý.';
$Definition['Reports']['Cata does not work on alliance members'] = 'Katapulty na členy aliance nepůsobí.';
$Definition['Reports']['NoFreeSlotsToCaptureOasis'] = 'K obsazování oáz rozšiř hrdinský dvůr';
$Definition['Reports']['OasisCaptured'] = 'Hrdina obsadil oázu.';
$Definition['Reports']['LoyaltyLowered'] = 'Loajalita snížena z <b>%d</b> na <b>%d</b>';
$Definition['Reports']['Inbox - All reports older than seven days will be deleted'] = 'Doručené – všechna hlášení, která nejsou v archivu, budou po 3 hodinách smazána.'; //Inbox - All reports older than seven days will be deleted.

$Definition['Reports']['Management'] = 'Správa';
$Definition['Reports']['ManagementDesc'] = 'Zde můžeš spravovat svá hlášení.';
$Definition['Reports']['ManagementOptions'] = [
    1 => 'Vítězství útočníka bez ztrát',
    2 => 'Vítězství útočníka se ztrátami',
    3 => 'Porážka útočníka se ztrátami',
    4 => 'Vítězství obránce bez ztrát',
    5 => 'Vítězství obránce se ztrátami',
    6 => 'Porážka obránce se ztrátami',
    7 => 'Porážka obránce bez ztrát',
    8 => 'Posily',
    9 => 'Obchodníci',
    10 => 'Špehovat',
    11 => 'Dobrodružství',
    12 => 'Všechna hlášení',
];
$Definition['Reports']['startTime'] = [
    0 => 'Začátek serveru',
    600 => 'před 10 minutami',
    1800 => 'před 30 minutami',
    3600 => 'před 1 hodinou',
    7200 => 'před 2 hodinami',
    10800 => 'před 3 hodinami',
    21600 => 'před 6 hodinami',
    43200 => 'před 12 hodinami',
    86400 => 'před 1 dnem',
    172800 => 'před 2 dny',
    604800 => 'před 7 dny',
];
$Definition['Reports']['ManagementDeleteDesc'] = "Smazat hlášení typu %s od %s %s";
$Definition['Reports']['ButtonOK'] = 'OK';
$Definition['Reports']['ReportsStatistics'] = 'Statistika hlášení';
$Definition['Reports']['%s report(s) removed successfully'] = 'Úspěšně odstraněno %s hlášení.';
$Definition['Reports']['AllReportsCount'] = 'Všechna hlášení';
$Definition['Reports']['ReportsWithoutCasualties'] = 'Hlášení o útocích bez ztrát';
$Definition['Reports']['ReportsWithCasualties'] = 'Hlášení o útocích se ztrátami';
$Definition['Reports']['ReportsDefWithoutCasualties'] = 'Hlášení o obraně bez ztrát';
$Definition['Reports']['ReportsDefWithCasualties'] = 'Hlášení o obraně se ztrátami';
$Definition['Reports']['ReportsOtherReports'] = 'Ostatní hlášení';
//
$Definition['ResidencePalace']['Management'] = 'Správa';
$Definition['ResidencePalace']['Train'] = 'Cvičit';
$Definition['ResidencePalace']['CulturePoints'] = 'Kulturní body';
$Definition['ResidencePalace']['Loyalty'] = 'Loajalita';
$Definition['ResidencePalace']['Expansion'] = 'Expanze';
$Definition['ResidencePalace']['This tab is set as favourite'] = 'Tato záložka je nastavena jako oblíbená';
$Definition['ResidencePalace']['Select x as favor tab'] = 'Nastavit %s jako oblíbenou záložku.';
$Definition['ResidencePalace']['Controllable villages'] = 'Ovladatelné vesnice';
$Definition['ResidencePalace']['Number of your villages'] = 'Počet tvých vesnic';
$Definition['ResidencePalace']['Maximum controllable villages'] = 'Maximum ovladatelných vesnic';
$Definition['ResidencePalace']['Culture points per day'] = 'Kulturní body za den';
$Definition['ResidencePalace']['Culture points produced so far'] = 'Dosud vyprodukované kulturní body';
$Definition['ResidencePalace']['Next village controllable at'] = 'Další vesnice ovladatelná při';
$Definition['ResidencePalace']['Culture points still needed'] = 'Ještě potřebné kulturní body';
$Definition['ResidencePalace']['Active village'] = 'Aktivní vesnice';
$Definition['ResidencePalace']['Other villages'] = 'Ostatní vesnice';
$Definition['ResidencePalace']['Hero'] = 'Hrdina';
$Definition['ResidencePalace']['Total'] = 'Celkem';
$Definition['ResidencePalace']['loyaltyDesc'] = 'Celkem';
$Definition['ResidencePalace']['Coordinates'] = 'Souřadnice';
$Definition['ResidencePalace']['noExpansion'] = 'Nenalezeny žádné vesnice.';
$Definition['ResidencePalace']['ChangeCapital'] = 'Zvolit tuto vesnici jako hlavní město';
$Definition['ResidencePalace']['Cant set ww as capital'] = 'Vesnici s divem světa nemůžeš zvolit jako hlavní město.';
$Definition['ResidencePalace']['Password'] = 'Heslo';
$Definition['ResidencePalace']['wrongPass'] = 'Nesprávné heslo.';
$Definition['ResidencePalace']['capitalChangeFailed'] = 'Hlavní město nebylo změněno, protože se stav vesnice změnil nebo už nesplňuje podmínky.';
$Definition['ResidencePalace']['ConfirmChangeCapital'] = 'Jsi si jistý?';
$Definition['ResidencePalace']['This is your capital'] = 'Toto je tvé hlavní město.';
$Definition['ResidencePalace']['Date'] = 'Datum';
$Definition['ResidencePalace']['In order to found or conquer further villages, you require culture points An additional village will predictably be controllable on x (±5 minutes)'] = 'K založení nebo dobytí dalších vesnic potřebuješ kulturní body. Další vesnici budeš pravděpodobně moci ovládat %s (±5 minut)';
$Definition['ResidencePalace']['The further the buildings of your villages are upgraded, the more culture points per day they will produce'] = 'Čím více jsou budovy ve tvých vesnicích rozšířené, tím více kulturních bodů za den vyprodukují.';
$Definition['ResidencePalace']['Loyalty in the current village'] = 'Loajalita v aktuální vesnici';
$Definition['ResidencePalace']['Loyalty overview'] = 'Přehled loajality';
$Definition['ResidencePalace']['village'] = 'Vesnice';
$Definition['ResidencePalace']['Inhabitants'] = 'Obyvatelé';
$Definition['ResidencePalace']['Player(s)'] = 'Hráči';
$Definition['ResidencePalace']['A palace or residence protect a village from being conquered If the seat of government has been destroyed, a village`s loyalty can be lowered by attacks with chieftains, chiefs and senators If the loyalty is lowered to zero, the village will join the attacker`s empire An ongoing great celebration in the village of either attacker or defender will increase or lower the rate at which each attacking administrator will lower the loyalty Each level of the seat of government increases the speed at which the loyalty of a village increases to 100% again A hero stationed in the village can use tablets of law to additionally increase loyalty'] = 'Palác nebo rezidence chrání vesnici před dobytím. Pokud je sídlo vlády zničeno, může být loajalita vesnice snižována útoky stařešinů, náčelníků a senátorů. Když loajalita klesne na nulu, vesnice se připojí k říši útočníka. Probíhající velká slavnost ve vesnici útočníka nebo obránce zvyšuje nebo snižuje, o kolik každý útočící správce loajalitu sníží. Každá úroveň sídla vlády zrychluje, jak rychle se loajalita vesnice vrací na 100 %. Hrdina umístěný ve vesnici může loajalitu dále zvýšit tabulkami zákona.';
//
$Definition['Smithy']['Improve weapons and armour'] = 'Vylepšit zbraně a zbroj';
$Definition['Smithy']['one_research_is_going'] = 'Jeden výzkum už probíhá.';
$Definition['Smithy']['reachedMaxLvl'] = '%s dosáhl maximální úrovně.';
$Definition['Smithy']['improve'] = 'Vylepšit';
$Definition['Smithy']['Researching'] = 'Probíhá výzkum';
$Definition['Smithy']['unit'] = 'Jednotka';
$Definition['Smithy']['upgradeSmithy'] = 'Příliš nízká úroveň kovárny';
//
$Definition['Statistics']['Prizes for top 10'] = 'Ceny pro top 10';
$Definition['Statistics']['Bonus name'] = 'Název bonusu';
$Definition['Statistics']['No prize is declared for top10'] = 'Pro top 10 není vyhlášena žádná cena.';
$Definition['Statistics']['top10 prize distribution desc'] = 'Ceny pro top 10 budou hráčům uděleny po resetu top 10.';

$Definition['Statistics']['Average number of troops per player'] = 'Průměrný počet jednotek na hráče';
$Definition['Statistics']['Game development'] = 'Vývoj hry';
$Definition['Statistics']['The following graphs show a time progression of economy, population, and the military strength of your army'] = 'Následující grafy ukazují vývoj ekonomiky, počtu obyvatel a vojenské síly tvé armády v čase.';
$Definition['Statistics']['Number of troops'] = 'Počet jednotek';
$Definition['Statistics']['titleInHeader'] = 'Statistiky';
$Definition['Statistics']['edit'] = 'upravit';
$Definition['Statistics']['tabs'] = [];
$Definition['Statistics']['tabs']['players'] = 'Hráči';
$Definition['Statistics']['tabs']['alliances'] = 'Aliance';
$Definition['Statistics']['tabs']['villages'] = 'Vesnice';
$Definition['Statistics']['tabs']['hero'] = 'Hrdinové';
$Definition['Statistics']['tabs']['plus'] = 'Plus';
$Definition['Statistics']['tabs']['General'] = 'Všeobecné';
$Definition['Statistics']['tabs']['WonderOfTheWorld'] = 'Div světa';
//$Definition['Statistics']['tabs']['WonderOfTheWorld'] = 'WonderOfTheWorld';
$Definition['Statistics']['tabs']['Bonus'] = 'Bonusy a stav';
$Definition['Statistics']['tabs']['farm'] = 'Farma';
$Definition['Statistics']['Actions'] = 'Akce';
$Definition['Statistics']['subTabs'] = [];
$Definition['Statistics']['subTabs']['overview'] = 'Přehled';
$Definition['Statistics']['subTabs']['attacker'] = 'Útočník';
$Definition['Statistics']['subTabs']['defender'] = 'Obránce';
$Definition['Statistics']['subTabs']['Top 10'] = 'Top 10';
$Definition['Statistics']['player'] = 'Hráč';
$Definition['Statistics']['alliance'] = 'Aliance';
$Definition['Statistics']['rank'] = 'Pořadí';
$Definition['Statistics']['name'] = 'Jméno';
$Definition['Statistics']['largestPlayers'] = 'Největší hráči';
$Definition['Statistics']['largestAlliances'] = 'Největší aliance';
$Definition['Statistics']['largestVillages'] = 'Největší vesnice';
$Definition['Statistics']['level'] = 'úroveň';
$Definition['Statistics']['xp'] = 'Zkušenosti';
$Definition['Statistics']['most exp heros'] = 'Nejzkušenější hrdinové';
$Definition['Statistics']['alliance'] = 'Aliance';
$Definition['Statistics']['population'] = 'Obyvatelé';
$Definition['Statistics']['village'] = 'Vesnice';
$Definition['Statistics']['points'] = 'Body';
$Definition['Statistics']['coordinates'] = 'Souřadnice';
$Definition['Statistics']['errors'] = [];
$Definition['Statistics']['errors']['userNotFound'] = 'Hráč %s nenalezen.';
$Definition['Statistics']['errors']['allianceNotFound'] = 'Aliance %s nenalezena.';
$Definition['Statistics']['errors']['villageNotFound'] = 'Vesnice %s nenalezena.';
$Definition['Statistics']['top10'] = [];
$Definition['Statistics']['top10']['attackers of the week'] = 'Útočníci týdne';
$Definition['Statistics']['top10']['defenders of the week'] = 'Obránci týdne';
$Definition['Statistics']['top10']['robbers of the week'] = 'Lupiči týdne';
$Definition['Statistics']['top10']['climbers of the week'] = 'Skokani týdne';
$Definition['Statistics']['top10']['resources'] = 'Surovinové';
$Definition['Statistics']['top10']['ranks'] = 'pořadí';
$Definition['Statistics']['top10']['pop'] = 'obyv.';
$Definition['Statistics']['top10']['points'] = 'body';
$Definition['Statistics']['top10']['No'] = 'Č.';
$Definition['Statistics']['top10']['top off hammer'] = 'Nejlepší útočné kladivo';
$Definition['Statistics']['top10']['top def hammer'] = 'Nejlepší obranné kladivo';
$Definition['Statistics']['top10']['date'] = 'datum';
$Definition['Statistics']['General'] = [];
$Definition['Statistics']['General']['Country ranks'] = 'Pořadí zemí';
$Definition['Statistics']['General']['Country name'] = 'Název země';
$Definition['Statistics']['General']['Players'] = 'Hráči';
$Definition['Statistics']['General']['Points'] = 'Body';
$Definition['Statistics']['General']['CountryFlag'] = 'CF';
$Definition['Statistics']['General']['Total country population'] = 'Celkový počet obyvatel země';
$Definition['Statistics']['General']['Tribe'] = 'Národ';
$Definition['Statistics']['General']['Tribes'] = 'Národy';
$Definition['Statistics']['General']['Romans'] = 'Římané';
$Definition['Statistics']['General']['Teutons'] = 'Germáni';
$Definition['Statistics']['General']['Gauls'] = 'Galové';
$Definition['Statistics']['General']['Egyptians'] = 'Egypťané';
$Definition['Statistics']['General']['Huns'] = 'Hunové';
$Definition['Statistics']['General']['Miscellaneous'] = 'Různé';
$Definition['Statistics']['General']['Attacks'] = 'Útoky';
$Definition['Statistics']['General']['Casualties'] = 'Ztráty';
$Definition['Statistics']['General']['Date'] = 'Datum';
$Definition['Statistics']['General']['Registered'] = 'Registrováno';
$Definition['Statistics']['General']['Percent'] = 'Procent';
$Definition['Statistics']['General']['Players'] = 'Hráči';
$Definition['Statistics']['General']['RegisteredPlayers'] = 'Registrovaní hráči';
$Definition['Statistics']['General']['ActivePlayers'] = 'Aktivní hráči';
$Definition['Statistics']['General']['onlinePlayers'] = 'Hráči online';
$Definition['Statistics']['General']['Attacks and casualties'] = 'Útoky a ztráty';
$Definition['Statistics']['WonderOfTheWorld'] = [];
$Definition['Statistics']['WonderOfTheWorld']['player'] = 'Hráč';
$Definition['Statistics']['WonderOfTheWorld']['name'] = 'Jméno';
$Definition['Statistics']['WonderOfTheWorld']['alliance'] = 'Aliance';
$Definition['Statistics']['WonderOfTheWorld']['level'] = 'úroveň';
$Definition['Statistics']['WonderOfTheWorld']['attackToWonder'] = '%s';
$Definition['Statistics']['Game development'] = 'Vývoj hry';
$Definition['Statistics']['The following graphs show a time progression of economy, population, and the military strength of your army'] = 'Následující grafy ukazují vývoj ekonomiky, počtu obyvatel a vojenské síly tvé armády v čase.';
$Definition['Statistics']['Number of troops'] = 'Počet jednotek';
$Definition['Statistics']['total'] = 'celkem';
$Definition['Statistics']['reinforcements'] = 'posily';
$Definition['Statistics']['Resource production and population'] = 'Produkce surovin a obyvatelé';
$Definition['Statistics']['resources/4'] = 'suroviny/4';
$Definition['Statistics']['Inhabitants'] = 'Obyvatelé';
$Definition['Statistics']['Rank'] = 'Pořadí';
$Definition['Statistics']['Number of troops killed'] = 'Počet zabitých jednotek';
$Definition['Statistics']['attack'] = 'útok';
$Definition['Statistics']['Winner and top player bonuses'] = 'Bonusy pro vítěze a nejlepší hráče';
$Definition['Statistics']['Winner Player'] = 'Vítězný hráč';
$Definition['Statistics']['Second Winner Player'] = 'Hráč na 2. místě';
$Definition['Statistics']['Third Winner Player'] = 'Hráč na 3. místě';
$Definition['Statistics']['Bonus rules'] = 'Pravidla bonusů';
$Definition['Statistics']['bonus_rules_array'] = [
    'Hráčům s multiúčty nedáváme žádné ceny.',
    'Pokud je vypsaná cena pro 2. a 3. místo, musí mít div světa k jejímu získání úroveň 50+.',
    sprintf('Cena pro alianci je pro %s nejlepších členů (kromě vítěze), jejichž počet obyvatel je nad %s.', Config::getProperty("bonus", "bonusGoldTopAllianceCount"), Config::getProperty("bonus", "bonusGoldTopAllianceMinPop")),
    'Některé servery mohou mít peněžní ceny, v tom případě tě budeme informovat.',
    'Po skončení serveru budou ceny připsány do tvých poukázek (uložené podle e-mailu).',
];
$Definition['Statistics']['Winner Alliance (Top 5)'] = 'Vítězná aliance (top 5)';
$Definition['Statistics']['Top attacker'] = 'Nejlepší útočník';
$Definition['Statistics']['Top defender'] = 'Nejlepší obránce';
$Definition['Statistics']['Top climber'] = 'Nejlepší skokan';
$Definition['Statistics']['Server states'] = 'Stav serveru';
$Definition['Statistics']['Daily gold will be given in'] = 'Denní zlato bude uděleno za';
$Definition['Statistics']['Daily quest will reset in'] = 'Denní úkoly se obnoví za';
$Definition['Statistics']['Medals will be given in'] = 'Medaile budou uděleny za';
$Definition['Statistics']['Artifacts will be released in'] = 'Artefakty budou vydány za';
$Definition['Statistics']['WWPlans will be released in'] = 'Plány divu světa budou vydány za';
$Definition['Statistics']['You can build WW in'] = 'Div světa můžeš stavět za';
$Definition['Statistics']['Game will be finished in'] = 'Hra skončí za';
$Definition['Statistics']['hours'] = 'hodin';
$Definition['Statistics']['Top Off hammer'] = 'Nejlepší útočné kladivo';
$Definition['Statistics']['Top Def hammer'] = 'Nejlepší obranné kladivo';
$Definition['Statistics']['raid'] = 'loupež';
$Definition['Statistics']['Other prises'] = 'Další ceny';
//
$Definition['Treasury']['ArtifactIsDisabled'] = 'Artefakty jsou vypnuté.';
$Definition['Treasury']['This tab is set as favourite'] = 'Tato záložka je nastavena jako oblíbená';
$Definition['Treasury']['Management'] = 'Správa';
$Definition['Treasury']['Artefacts in your area'] = 'Artefakty ve tvé oblasti';
$Definition['Treasury']['Small artefacts'] = 'Malé artefakty';
$Definition['Treasury']['Large artefacts'] = 'Velké artefakty';
$Definition['Treasury']['select tab x as favor tab'] = 'Vybrat záložku %s jako oblíbenou.';
$Definition['Treasury']['Stored artefact'] = 'Uložený artefakt';
$Definition['Treasury']['Name'] = 'Jméno';
$Definition['Treasury']['Village'] = 'Vesnice';
$Definition['Treasury']['conquered'] = 'Dobyto';
$Definition['Treasury']['Former owner'] = 'Bývalý vlastník';
$Definition['Treasury']['you dont own any artefacts'] = 'Nevlastníš žádné artefakty.';
$Definition['Treasury']['Effect'] = 'Účinek';
$Definition['Treasury']['Village'] = 'Vesnice';
$Definition['Treasury']['Account'] = 'Účet';
$Definition['Treasury']['Player'] = 'Hráč';
$Definition['Treasury']['Area of effect'] = 'Oblast působení';
$Definition['Treasury']['Bonus'] = 'Bonus';
$Definition['Treasury']['Required level'] = 'Potřebná úroveň';
$Definition['Treasury']['Time of conquer'] = 'Čas dobytí';
$Definition['Treasury']['Active'] = 'Aktivní';
$Definition['Treasury']['Time of activation'] = 'Čas aktivace';
$Definition['Treasury']['Alliance'] = 'Aliance';
$Definition['Treasury']['Distance'] = 'Vzdálenost';
$Definition['Treasury']['Owner'] = 'Vlastník';
$Definition['Treasury']['No Artefact'] = 'Artefakty zatím nebyly vydány.';
//
$Definition['Troops']['hero']['title'] = 'Hrdina';
$Definition['Troops'][1]['title'] = 'Legionář';
$Definition['Troops'][1]['desc'] = 'Legionář je jednoduchá a všestranná pěchota římské říše. Díky vyváženému výcviku je dobrý v obraně i v útoku. Úrovně specializovanějších jednotek ale nikdy nedosáhne.';
$Definition['Troops'][2]['title'] = 'Pretorián';
$Definition['Troops'][2]['desc'] = 'Pretoriáni jsou císařova garda a brání ho vlastním životem. Protože je jejich výcvik zaměřen na obranu, jsou velmi slabí útočníci.';
$Definition['Troops'][3]['title'] = 'Imperián';
$Definition['Troops'][3]['desc'] = 'Imperián je nejlepší útočník římské říše. Je rychlý, silný a je noční můrou všech obránců. Jeho výcvik je ale drahý a časově náročný.';
$Definition['Troops'][4]['title'] = 'Equites Legati';
$Definition['Troops'][4]['desc'] = 'Equites Legati jsou římské průzkumné jednotky. Jsou docela rychlí a mohou špehovat nepřátelské vesnice, aby zjistili suroviny a jednotky.
<br><br>
Pokud ve špehované vesnici nejsou žádní zvědové, Equites Legati ani slídiči, zůstane špehování nepozorováno.';
$Definition['Troops'][5]['title'] = 'Equites Imperatoris';
$Definition['Troops'][5]['desc'] = 'Equites Imperatoris jsou standardní jízda římské armády a jsou velmi dobře vyzbrojeni. Nejsou nejrychlejší, ale pro nepřipraveného nepřítele jsou postrachem. Vždy ale pamatuj, že živit koně i jezdce není levné.';
$Definition['Troops'][6]['title'] = 'Equites Caesaris';
$Definition['Troops'][6]['desc'] = 'Equites Caesaris jsou těžká jízda Říma. Jsou velmi dobře obrnění a způsobují velké škody, ale veškerá zbroj a zbraně něco stojí. Jsou pomalí, unesou méně surovin a jejich krmení je drahé.';
$Definition['Troops'][7]['title'] = 'Římské beranidlo';
$Definition['Troops'][7]['desc'] = 'Beranidlo je těžká podpůrná zbraň pro tvou pěchotu a jízdu. Jeho úkolem je ničit nepřátelské hradby a zvyšovat tak šance tvých jednotek překonat opevnění nepřítele.';
$Definition['Troops'][8]['title'] = 'Ohnivý katapult';
$Definition['Troops'][8]['desc'] = 'Katapult je výborná zbraň na dálku, která slouží k ničení polí a budov nepřátelských vesnic. Bez doprovodu je ale téměř bezbranný, proto s ním nezapomeň poslat i nějaké jednotky.
<br><br>
Vysoká úroveň shromaždiště zpřesňuje tvé katapulty a umožňuje mířit na další nepřátelské budovy. Více informací najdeš <a href="' . getAnswersUrl() . '?view=answers&amp;action=answer&amp;aid=157#go2answer" target="blank">zde</a>.
<br>
TIP: Při náhodné střelbě MOHOU katapulty zasáhnout úkryt, pastičkáře nebo kameníka.';
$Definition['Troops'][9]['title'] = 'Senátor';
$Definition['Troops'][9]['desc'] = 'Senátor je zvolený vůdce národa. Je dobrý řečník a umí přesvědčovat. Dokáže přesvědčit jiné vesnice, aby bojovaly za říši.
<br><br>
Pokaždé, když senátor promluví k obyvatelům vesnice, klesne loajalita nepřítele, dokud vesnice nebude tvoje.';
$Definition['Troops'][10]['title'] = 'Osadník';
$Definition['Troops'][10]['desc'] = 'Osadníci jsou stateční a odvážní občané, kteří po dlouhém výcviku opustí vesnici, aby na tvou počest založili novou vesnici.
<br><br>
Protože cesta i založení nové vesnice jsou velmi náročné, drží tři osadníci vždy pohromadě. Potřebují základ 750 jednotek od každé suroviny.';
$Definition['Troops'][11]['title'] = 'Pálkař';
$Definition['Troops'][11]['desc'] = 'Pálkaři jsou nejlevnější jednotkou v Travianu. Rychle se cvičí a mají průměrnou útočnou sílu, jejich zbroj ale není nejlepší. Proti jízdě jsou pálkaři téměř bezbranní a snadno je převálcuje.';
$Definition['Troops'][12]['title'] = 'Oštěpař';
$Definition['Troops'][12]['desc'] = 'V germánské armádě má oštěpař na starosti obranu. Díky délce svých zbraní je obzvlášť dobrý proti jízdě.

V útoku ho ale nepoužívej, jeho útočné schopnosti jsou velmi nízké.';
$Definition['Troops'][13]['title'] = 'Sekerník';
$Definition['Troops'][13]['desc'] = 'Sekerník je nejsilnější germánská pěchota. Je silný v útoku i v obraně, ale je pomalejší a dražší než ostatní jednotky.';
$Definition['Troops'][14]['title'] = 'Zvěd';
$Definition['Troops'][14]['desc'] = 'Zvěd postupuje daleko před germánskými jednotkami, aby zjistil sílu nepřítele a jeho vesnic. Pohybuje se pěšky, a je proto pomalejší než jeho římský nebo galský protějšek. Zjišťuje nepřátelské jednotky, suroviny a opevnění.
<br><br>
Pokud ve špehované vesnici nejsou žádní nepřátelští zvědové, slídiči ani Equites Legati, zůstane špehování nepozorováno.';
$Definition['Troops'][15]['title'] = 'Rytíř';
$Definition['Troops'][15]['desc'] = 'Rytíři jsou díky těžké zbroji výbornou obrannou jednotkou. Pěchota má s jejich mohutným štítem obzvlášť velké potíže.
<br><br>
Jejich útočné schopnosti jsou bohužel spíše nízké a jejich rychlost je ve srovnání s ostatní jízdou podprůměrná. Jejich výcvik trvá velmi dlouho a je dost drahý.';
$Definition['Troops'][16]['title'] = 'Teutonský rytíř';
$Definition['Troops'][16]['desc'] = 'Teutonský rytíř je impozantní válečník, který v nepřátelích vzbuzuje strach a zoufalství. V obraně vyniká proti nepřátelské jízdě. Náklady na jeho výcvik a krmení jsou ale mimořádné.';
$Definition['Troops'][17]['title'] = 'Beranidlo';
$Definition['Troops'][17]['desc'] = 'Beranidlo je těžká podpůrná zbraň pro tvou pěchotu a jízdu. Jeho úkolem je ničit nepřátelské hradby a zvyšovat tak šance tvých jednotek překonat opevnění nepřítele.';
$Definition['Troops'][18]['title'] = 'Katapult';
$Definition['Troops'][18]['desc'] = 'Katapult je výborná zbraň na dálku, která slouží k ničení polí a budov nepřátelských vesnic. Bez doprovodu je ale téměř bezbranný, proto s ním nezapomeň poslat i nějaké jednotky.
<br><br>
Vysoká úroveň shromaždiště zpřesňuje tvé katapulty a umožňuje mířit na další nepřátelské budovy. Více informací najdeš <a href="' . getAnswersUrl() . '?view=answers&amp;action=answer&amp;aid=157#go2answer" target="blank">zde</a>.
<br>
TIP: Při náhodné střelbě MOHOU katapulty zasáhnout úkryt, pastičkáře nebo kameníka.';
$Definition['Troops'][19]['title'] = 'Kmenový vůdce';
$Definition['Troops'][19]['desc'] = 'Germáni si kmenového vůdce volí z řad těch nejlepších. Ke zvolení nestačí odvaha a strategie, musíš být i výborný řečník, protože hlavním úkolem kmenového vůdce je přesvědčit obyvatele cizích vesnic, aby se přidali k jeho kmeni.
<br><br>
Čím častěji kmenový vůdce k obyvatelům vesnice promluví, tím více klesá loajalita vesnice, až se nakonec přidá k jeho kmeni.';
$Definition['Troops'][20]['title'] = 'Osadník';
$Definition['Troops'][20]['desc'] = 'Osadníci jsou stateční a odvážní občané, kteří po dlouhém výcviku opustí vesnici, aby na tvou počest založili novou vesnici.
<br><br>
Protože cesta i založení nové vesnice jsou velmi náročné, drží tři osadníci vždy pohromadě. Potřebují základ 750 jednotek od každé suroviny.';
$Definition['Troops'][21]['title'] = 'Falanga';
$Definition['Troops'][21]['desc'] = 'Falanga je pěchota, a proto je levná a rychle se vyrábí.
<br><br>
Jejich útočná síla je nízká, v obraně jsou ale docela silní proti pěchotě i jízdě.';
$Definition['Troops'][22]['title'] = 'Šermíř';
$Definition['Troops'][22]['desc'] = 'Šermíři jsou dražší než falanga, ale jsou útočnou jednotkou.
<br><br>
V obraně jsou dost slabí, zvláště proti jízdě.';
$Definition['Troops'][23]['title'] = 'Slídič';
$Definition['Troops'][23]['desc'] = 'Slídič je galská průzkumná jednotka. Je velmi rychlý a dokáže se nepozorovaně přiblížit k nepřátelským jednotkám, surovinám nebo budovám a vyšpehovat je.
<br><br>
Pokud ve špehované vesnici nejsou žádní zvědové, Equites Legati ani slídiči, zůstane špehování nepozorováno.';
$Definition['Troops'][24]['title'] = 'Theutates blesk';
$Definition['Troops'][24]['desc'] = 'Theutates blesky jsou velmi rychlé a silné jízdní jednotky. Unesou velké množství surovin, takže jsou i výbornými lupiči.
<br><br>
V obraně jsou jejich schopnosti nanejvýš průměrné.';
$Definition['Troops'][25]['title'] = 'Druid jezdec';
$Definition['Troops'][25]['desc'] = 'Tato středně těžká jízda vyniká v obraně. Hlavním úkolem druida jezdce je obrana proti nepřátelské pěchotě. Jeho výcvik a strava jsou poměrně drahé.';
$Definition['Troops'][26]['title'] = 'Haeduan';
$Definition['Troops'][26]['desc'] = 'Haeduani jsou nejlepší galskou zbraní pro útok i obranu proti jízdě. Jen málo jednotek se jim vyrovná.
<br><br>
Jejich výcvik a výzbroj jsou ale velmi drahé. Dobře si rozmysli, jestli se vyplatí vynaložené úsilí a náklady.';
$Definition['Troops'][27]['title'] = 'Beranidlo';
$Definition['Troops'][27]['desc'] = 'Beranidlo je těžká podpůrná zbraň pro tvou pěchotu a jízdu. Jeho úkolem je ničit nepřátelské hradby a zvyšovat tak šance tvých jednotek překonat opevnění nepřítele.';
$Definition['Troops'][28]['title'] = 'Katapult';
$Definition['Troops'][28]['desc'] = 'Katapult je výborná zbraň na dálku, která slouží k ničení polí a budov nepřátelských vesnic. Bez doprovodu je ale téměř bezbranný, proto s ním nezapomeň poslat i nějaké jednotky.
<br><br>
Vysoká úroveň shromaždiště zpřesňuje tvé katapulty a umožňuje mířit na další nepřátelské budovy. Více informací najdeš <a href="' . getAnswersUrl() . '?view=answers&amp;action=answer&amp;aid=157#go2answer" target="blank">zde</a>.
<br>
TIP: Při náhodné střelbě MOHOU katapulty zasáhnout úkryt, pastičkáře nebo kameníka.';
$Definition['Troops'][29]['title'] = 'Náčelník';
$Definition['Troops'][29]['desc'] = 'Každý národ má zkušeného bojovníka, jehož přítomnost a projevy dokážou přesvědčit obyvatele nepřátelských vesnic, aby se přidali k jeho národu.
<br><br>
Čím častěji náčelník promluví před hradbami nepřátelské vesnice, tím více klesá její loajalita, až se přidá k náčelníkovu národu.';
$Definition['Troops'][30]['title'] = 'Osadník';
$Definition['Troops'][30]['desc'] = 'Osadníci jsou stateční a odvážní občané, kteří po dlouhém výcviku opustí vesnici, aby na tvou počest založili novou vesnici.
<br><br>
Protože cesta i založení nové vesnice jsou velmi náročné, drží tři osadníci vždy pohromadě. Potřebují základ 750 jednotek od každé suroviny.';
$Definition['Troops'][31]['title'] = 'Krysa';
$Definition['Troops'][32]['title'] = 'Pavouk';
$Definition['Troops'][33]['title'] = 'Had';
$Definition['Troops'][34]['title'] = 'Netopýr';
$Definition['Troops'][35]['title'] = 'Divočák';
$Definition['Troops'][36]['title'] = 'Vlk';
$Definition['Troops'][37]['title'] = 'Medvěd';
$Definition['Troops'][38]['title'] = 'Krokodýl';
$Definition['Troops'][39]['title'] = 'Tygr';
$Definition['Troops'][40]['title'] = 'Slon';
$Definition['Troops'][41]['title'] = 'Pikenýr';
$Definition['Troops'][42]['title'] = 'Trnitý válečník';
$Definition['Troops'][43]['title'] = 'Strážce';
$Definition['Troops'][44]['title'] = 'Draví ptáci';
$Definition['Troops'][45]['title'] = 'Jezdec se sekerou';
$Definition['Troops'][46]['title'] = 'Natarský rytíř';
$Definition['Troops'][47]['title'] = 'Válečný slon';
$Definition['Troops'][48]['title'] = 'Balista';
$Definition['Troops'][49]['title'] = 'Natarský císař';
$Definition['Troops'][50]['title'] = 'Osadník';
$Definition['Troops'][51]['title'] = 'Domobrana otroků';
$Definition['Troops'][51]['desc'] = 'Domobrana otroků je nejlevnější jednotka s nejkratší dobou výcviku ve hře. Díky tomu můžeš velmi rychle postavit obranu, silou se ale ostatním obranným jednotkám nevyrovná.';
$Definition['Troops'][52]['title'] = 'Ashův strážce';
$Definition['Troops'][52]['desc'] = 'Ashův strážce je standardní obranný pěšák se solidní bojovou silou. Výborně si vede proti ostatní pěchotě, ale ani jeho obranu proti jízdě nelze přehlížet.';
$Definition['Troops'][53]['title'] = 'Válečník s chopešem';
$Definition['Troops'][53]['desc'] = 'Válečník s chopešem je elitní voják, který se může pochlubit silným útokem i pozoruhodnou obranou proti ostatním pěším bojovníkům.';
$Definition['Troops'][54]['title'] = 'Sopduův průzkumník';
$Definition['Troops'][54]['desc'] = 'Sopduův průzkumník vyjíždí do neznámých krajů, aby prozkoumal okolí a spočítal vojska tvých nepřátel. Dokáže také prozkoumat uspořádání nepřátelských vesnic a odhalit jejich slabá místa.';
$Definition['Troops'][55]['title'] = 'Anhurova stráž';
$Definition['Troops'][55]['desc'] = 'Anhurova stráž je jízdní obránce, který ovládá každou útočící pěchotu. Kromě průměrné síly v útoku a v obraně proti jízdě je po Sopduově průzkumníkovi nejrychlejší egyptskou jednotkou.';
$Definition['Troops'][56]['title'] = 'Rešepův vůz';
$Definition['Troops'][56]['desc'] = 'Rešepův vůz ovládá všechna bojová umění. Má silný útok i velmi solidní obranu proti pěchotě, ale skutečně vyniká v obraně proti jízdě. Na oplátku je jeho výcvik velmi drahý a spotřebuje hodně obilí.';
$Definition['Troops'][57]['title'] = 'Beranidlo';
$Definition['Troops'][57]['desc'] = 'Beranidlo je těžká podpůrná zbraň pro tvou pěchotu a jízdu. Jeho úkolem je ničit nepřátelské hradby a zvyšovat tak šance tvých jednotek překonat opevnění nepřítele.';
$Definition['Troops'][58]['title'] = 'Kamenný katapult';
$Definition['Troops'][58]['desc'] = 'Kamenný katapult je výborná zbraň na dálku, která slouží k ničení polí a budov nepřátelských vesnic. Bez doprovodu je ale téměř bezbranný, proto s ním nezapomeň poslat i nějaké jednotky. 
<br/>
Vysoká úroveň shromaždiště zpřesňuje tvé katapulty a umožňuje mířit na další nepřátelské budovy. Více informací najdeš zde. TIP: Při náhodné střelbě MOHOU katapulty zasáhnout úkryt, pastičkáře nebo kameníka.';
$Definition['Troops'][59]['title'] = 'Nomarcha';
$Definition['Troops'][59]['desc'] = 'Nomarcha je správní vůdce Egypťanů. Vyjednává podmínky kapitulace a díky svému charismatu dokáže přesvědčit nepřátelský lid, aby se přidal k tvé říši. 
<br/>
S každou návštěvou nomarchy klesá loajalita nepřátelského obyvatelstva, dokud nezmizí úplně a lid nepřijme tvou vládu.';
$Definition['Troops'][60]['title'] = 'Osadník';
$Definition['Troops'][60]['desc'] = 'Osadníci jsou stateční a odvážní občané, kteří po dlouhém výcviku opustí vesnici, aby na tvou počest založili novou vesnici. 
<br/>
Protože cesta i založení nové vesnice jsou velmi náročné, drží tři osadníci vždy pohromadě. Potřebují základ 750 jednotek od každé suroviny.';
$Definition['Troops'][61]['title'] = 'Žoldnéř';
$Definition['Troops'][61]['desc'] = 'Žoldnéř je všeuměl. Jeho útok i obrana jsou velmi vyrovnané, v žádném druhu boje ale nevyniká. Tomu odpovídají i jeho střední náklady na výcvik.';
$Definition['Troops'][62]['title'] = 'Lučištník';
$Definition['Troops'][62]['desc'] = 'Lučištník je nejlepší volba pro velké útočné údery. Miluje přední linie, což je štěstí, protože jeho obrana je chabá.';
$Definition['Troops'][63]['title'] = 'Pozorovatel';
$Definition['Troops'][63]['desc'] = 'Pozorovatel je bleskově rychlý průzkumník, který odhaluje vojenská a hospodářská tajemství nepřátelských vesnic a předává ti je.';
$Definition['Troops'][64]['title'] = 'Stepní jezdec';
$Definition['Troops'][64]['desc'] = 'Stepní jezdec je vynikající útočník, jehož výcvik je rychlejší než u většiny jiných jezdců. Proto je ale v obraně velmi slabý.';
$Definition['Troops'][65]['title'] = 'Ostrostřelec';
$Definition['Troops'][65]['desc'] = 'Ostrostřelec je vyvážená jízdní jednotka. Jeho solidní útočnou sílu zastiňuje skutečnost, že je jediným schopným obranným vojákem Hunů.';
$Definition['Troops'][66]['title'] = 'Nájezdník';
$Definition['Troops'][66]['desc'] = 'Nájezdník je síla, se kterou je třeba počítat. S neuvěřitelnou útočnou silou a působivou rychlostí převálcuje většinu obran bez škrábnutí na zbroji.';
$Definition['Troops'][67]['title'] = 'Beranidlo';
$Definition['Troops'][67]['desc'] = 'Beranidlo je těžká podpůrná zbraň pro tvou pěchotu a jízdu. Jeho úkolem je ničit nepřátelské hradby a zvyšovat tak šance tvých jednotek překonat opevnění nepřítele.';
$Definition['Troops'][68]['title'] = 'Katapult';
$Definition['Troops'][68]['desc'] = 'Katapult je výborná zbraň na dálku, která slouží k ničení polí a budov nepřátelských vesnic. Bez doprovodu je ale téměř bezbranný, proto s ním nezapomeň poslat i nějaké jednotky. 
<br/>
Vysoká úroveň shromaždiště zpřesňuje tvé katapulty a umožňuje mířit na další nepřátelské budovy. Více informací najdeš zde. TIP: Při náhodné střelbě MOHOU katapulty zasáhnout úkryt, pastičkáře nebo kameníka.';
$Definition['Troops'][69]['title'] = 'Logades';
$Definition['Troops'][69]['desc'] = 'Logades si své místo vybojoval porážkou všech vyzyvatelů ve smrtelném souboji těla i ducha. Teď opouští své obydlí jen proto, aby dobýval. 
<br/>
Pokaždé, když vstoupí do nepřátelské vesnice, jeho mocná přítomnost snižuje loajalitu lidu k předchozímu vládci, až se rozhodnou přidat k tobě.';
$Definition['Troops'][70]['title'] = 'Osadník';
$Definition['Troops'][70]['desc'] = 'Osadníci jsou stateční a odvážní občané, kteří po dlouhém výcviku opustí vesnici, aby na tvou počest založili novou vesnici. 
<br/>
Protože cesta i založení nové vesnice jsou velmi náročné, drží tři osadníci vždy pohromadě. Potřebují základ 750 jednotek od každé suroviny.';

$Definition['Troops'][98]['title'] = 'Hrdina';
$Definition['Troops'][99]['title'] = 'Past';
//
$Definition['villageOverview']['villageOverview'] = 'Přehled vesnic';
$Definition['villageOverview']['Overview'] = 'Přehled';
$Definition['villageOverview']['Resources'] = 'Surovinové';
$Definition['villageOverview']['Warehouse'] = 'Sklad surovin';
$Definition['villageOverview']['CulturePoints'] = 'Kulturní body';
$Definition['villageOverview']['Troops'] = 'Jednotky';
$Definition['villageOverview']['Select x as favor tab'] = 'Vybrat %s jako oblíbenou záložku.';
$Definition['villageOverview']['This tab is set as favourite'] = 'Tato záložka je nastavena jako oblíbená.';
$Definition['villageOverview']['Village statistics||For this feature you need Travian Plus activated'] = 'Statistika vesnice||Pro tuto funkci potřebuješ aktivní Travian Plus.';
$Definition['villageOverview']['Village'] = 'Vesnice';
$Definition['villageOverview']['Attacks'] = 'Útoky';
$Definition['villageOverview']['Building'] = 'Budova';
$Definition['villageOverview']['Troops'] = 'Jednotky';
$Definition['villageOverview']['Merchants'] = 'Obchodníci';
$Definition['villageOverview']['Own attacking troops'] = 'Vlastní útočící jednotky';
$Definition['villageOverview']['Oasis attacking troops'] = 'Jednotky útočící na oázy';
$Definition['villageOverview']['Oasis reinforcing troops'] = 'Posily v oázách';
$Definition['villageOverview']['Village reinforcing troops'] = 'Posily ve vesnici';
$Definition['villageOverview']['Other attacking troops'] = 'Ostatní útočící jednotky';
$Definition['villageOverview']['Own Reinforcing troops'] = 'Vlastní posily';
$Definition['villageOverview']['Sum'] = 'Součet';
$Definition['villageOverview']['duration'] = 'trvání';
$Definition['villageOverview']['CPs/Day'] = 'KB/den';
$Definition['villageOverview']['Celebrations'] = 'Slavnosti';
$Definition['villageOverview']['Slots'] = 'Místa';
$Definition['villageOverview']['Own Troops'] = 'Vlastní jednotky';
$Definition['villageOverview']['Troops in villages'] = 'Jednotky ve vesnicích';
$Definition['villageOverview']['Upkeep'] = 'Údržba';
$Definition['villageOverview']['Armory'] = 'Zbrojnice';
$Definition['villageOverview']['inResearch'] = 'Probíhá';
$Definition['villageOverview']['research_level'] = 'Úroveň výzkumu';
$Definition['villageOverview']['per hour'] = 'za hodinu';
//
$Definition['demolishNowPopup']['Redeem'] = 'Uplatnit';
$Definition['demolishNowPopup']['desc'] = 'Opravdu chceš tuto budovu úplně zbourat? Budova bude z vesnice odstraněna.';
//
$Definition['finishNowPopup']['title'] = 'Dokončit stavbu okamžitě';
$Definition['finishNowPopup']['desc'] = 'Okamžitě budou dokončeny tyto zakázky:';
$Definition['finishNowPopup']['Redeem'] = 'Uplatnit';
$Definition['finishNowPopup']['buildingOrders'] = 'Stavební zakázky:';
$Definition['finishNowPopup']['academy'] = 'Vyzkoumat' . '(Akademie)';
$Definition['finishNowPopup']['smithy'] = 'Vyzkoumat' . '(kovárna)';
$Definition['finishNowPopup']['demolishBuildingLevel'] = 'Zbourat úroveň budovy';
$Definition['finishNowPopup']['level'] = 'Úroveň';
$Definition['finishNowPopup']['No construction orders or research that could be completed instantly'] = 'Nejsou žádné stavby ani výzkumy, které by šlo okamžitě dokončit.';
//
$Definition['goldClubPopup']['title'] = 'Zlatý klub';
$Definition['goldClubPopup']['gold'] = 'Zlato';
$Definition['goldClubPopup']['Bonus duration'] = 'Trvání bonusu';
$Definition['goldClubPopup']['whole game round'] = 'celé herní kolo';
$Definition['goldClubPopup']['Additionally, you will have access to the following features:'] = 'Navíc získáš přístup k těmto funkcím:';
$Definition['goldClubPopup']['In order to use this feature, you need to activate the Gold club!'] = 'Pro použití této funkce musíš aktivovat Zlatý klub!';
$Definition['goldClubPopup']['troopEscape'] = [
    'title' => 'Únik v hlavním městě',
    'text' => 'Vlastním jednotkám v hlavním městě můžeš přes shromaždiště nařídit, aby před útoky automaticky utekly z vesnice.',
];
$Definition['goldClubPopup']['raidList'] = [
    'title' => 'Seznam farem',
    'text' => 'Na shromaždišti můžeš pomocí seznamu farem spravovat útoky na lukrativní cíle.',
];
$Definition['goldClubPopup']['tradeThreeTimes'] = [
    'title' => 'Obchodníci mohou jet třikrát',
    'text' => 'Obchodníci mohou automaticky provést dodávku surovin až třikrát za sebou.',
];
$Definition['goldClubPopup']['tradeThreeTimes'] = [
    'title' => 'Automatické obchodní trasy',
    'text' => 'Můžeš nastavit pravidelné dodávky surovin mezi vlastními vesnicemi v určených časech.',
];
$Definition['goldClubPopup']['cropFinder'] = [
    'title' => 'Hledač obilí (9 a 15) na mapě',
    'text' => 'Vyhledávání přímo na mapě ti umožní najít pole se zvýšenou produkcí obilí a bonusem oáz.',
];
$Definition['goldClubPopup']['messageArchive'] = [
    'title' => 'Archiv zpráv a hlášení',
    'text' => 'Důležité zprávy a hlášení můžeš archivovat a později k nim snadno najít cestu.',
];
$Definition['goldClubPopup']['furtherInfos'] = '<B>Zlatý klub</b> zpřístupní tyto funkce do konce herního kola. Pro více informací klikni na "i".';
//
$Definition['overlay'] = [
    'defaultTitle' => 'Vítej v novém uživatelském panelu Travianu',
    'defaultDescription' => 'Pro více informací o jednotlivých částech najeď kurzorem myši na příslušný box.',
    'closeLink' => 'Zavřít nápovědu a pokračovat ve hře.',
    'mainPageTitle' => 'Domů',
    'mainPageDescription' => '<span class=\\"important\\">Znak Travianu<\\/span> tě přenese na naši hlavní stránku s dalšími informacemi a hrami.',
    'villageSwitchTitle' => 'Přepnout pohled na vesnici',
    'villageSwitchDescription' => 'Přepínej mezi surovinovými poli mimo vesnici a centrem vesnice se všemi ostatními budovami. Kliknutím otevřeš surovinové pole, budovu nebo prázdné místo. Tam je můžeš rozšířit nebo použít.',
    'mainNavigationTitle' => 'Zvláštní přehledy hry',
    'mainNavigationDescription' => '<span class=\\"important\\">Mapa světa:<\\/span> Mapa ukazuje okolí tvé vesnice. Uvidíš na ní cíle i možné hrozby.<br \\/><span class=\\"important\\">Statistiky:<\\/span> Vybrané údaje o všech hráčích a aliancích ve hře.<br \\/><span class=\\"important\\">Hlášení:<\\/span> Hlášení o událostech, jako jsou bitvy, obchody a dobrodružství.<br \\/><span class=\\"important\\">Zprávy:<\\/span> Uzavírej dohody nebo plánuj přímo s ostatními hráči.',
    'premiumFeaturesTitle' => 'Koupit a používat zlato',
    'premiumFeaturesDescription' => 'Zde si můžeš koupit <span class="important">zlato</span> a získat přístup k exkluzivním funkcím a vylepšením rozhraní.<br \\/><span class=\\"important\\">Stříbro<\\/span> se používá v aukcích hrdinů a v aukčním domě ho můžeš směnit za zlato.',
    'outOfGameTitle' => 'Správa',
    'outOfGameDescription' => 'Užitečné funkce hry:<br /><span class="important">Profil:</span> Úprava veřejného profilu hráče.<br /><span class="important">Nastavení:</span> Předvolby a technická nastavení.<br /><span class="important">Fórum:</span> Přímo na oficiální fórum Travianu.<br /><span class="important">IRC:</span> Chat podpory, kde najdeš pomoc.<br /><span class="important">Odpovědi:</span> Stránky nápovědy Travianu.<br /><span class="important">Odhlásit:</span> Odhlášení z účtu.',
    'villageResourcesTitle' => 'Suroviny vybrané vesnice',
    'villageResourcesDescription' => 'Zobrazuje velikost skladu surovin a současné zásoby dřeva, hlíny a železa.<br \\/>Kliknutím na zásobu zobrazíš přehled, jak se počítá současná produkce suroviny.',
    'villageCropTitle' => 'Obilí vybrané vesnice',
    'villageCropDescription' => 'Zobrazuje velikost sýpky a současnou zásobu obilí. Volné obilí je potřeba na údržbu nových nebo rozšířených budov. Ve hře klikni na příslušná tlačítka pro podrobnější informace.',
    'sidebarBoxHeroTitle' => 'Avatar tvého hrdiny',
    'sidebarBoxHeroDescription' => 'Tvůj hrdina a několik důležitých údajů o něm.<br \\/>Kliknutím na obrázek změníš vlastnosti nebo nasadíš předměty.<br \\/>První tlačítko vede k dostupným dobrodružstvím, odkud můžeš hrdinu rovnou poslat na cestu. Druhé tlačítko vede do aukčního domu, kde můžeš prodat nasbírané předměty nebo koupit předměty od jiných hráčů.',
    'sidebarBoxAllianceTitle' => 'Tvá aliance',
    'sidebarBoxAllianceDescription' => 'V alianci mohou hráči lépe spolupracovat a navzájem se podporovat. Pro svou bezpečnost by sis měl rychle najít spojence. Když jsi členem aliance, tlačítka vedou na profil aliance a fórum aliance.',
    'sidebarBoxInfoboxTitle' => 'Infobox',
    'sidebarBoxInfoboxDescription' => 'Zde najdeš důležité systémové zprávy.',
    'sidebarBoxLinklistTitle' => 'Seznam odkazů',
    'sidebarBoxLinklistDescription' => 'K používání seznamu odkazů potřebuješ Travian PLUS.<br \\/>Můžeš vytvářet přímé odkazy na důležité cíle nebo budovy i externí odkazy. Tlačítkem seznam upravíš.',
    'sidebarBoxActiveVillageTitle' => 'Cedule aktivní vesnice',
    'sidebarBoxActiveVillageDescription' => 'Jméno vybrané vesnice a loajalita jejích obyvatel k tvé vládě.<br \\/>Nahoře najdou uživatelé PLUS 4 přímé odkazy na tržiště a vojenské budovy. Tlačítkem úprav změníš jméno vesnice.',
    'sidebarBoxVillagelistTitle' => 'Seznam všech tvých vesnic',
    'sidebarBoxVillagelistDescription' => 'V záhlaví vidíš, kolik vesnic momentálně vlastníš a kolik bys jich mohl mít. Níže vidíš postup kulturních bodů potřebných pro další vesnici. Tlačítka otevírají další přehledy a zobrazují souřadnice vesnic.',
    'sidebarBoxQuestmasterTitle' => 'Rádce a zadavatel úkolů',
    'sidebarBoxQuestmasterDescription' => 'Kliknutím na zadavatele úkolů zobrazíš nebo skryješ jeho úkoly pro tebe. Dá ti vědět, když je něco nového. Níže najdeš přehled aktuálních úkolů.',
];
//
$Definition['plusPopup'] = [
    'title' => 'Travian PLUS',
    'subHeadLine' => 'Pro použití této funkce musíš aktivovat Travian PLUS.',
    'plusPopupButtonExtraFeatures' => 'Navíc získáš přístup k těmto funkcím:',
    'Bonus duration in days:' => 'Trvání bonusu ve dnech:',
    "features" => [
        'attackWarning' => [
            'title' => 'Varování před útokem',
            'text' => 'Přicházející útoky se zobrazí v seznamu vesnic.',
        ],
        'buildingQueue' => [
            'title' => 'Stavební fronta',
            'text' => 'Stavební fronta ti umožní zařadit do fronty další stavební zakázku.',
        ],
        'directLinks' => [
            'title' => 'Přímé odkazy',
            'text' => 'Přímé odkazy na tržiště, kasárny, stáj a dílnu a další důležité informace v nápovědách.',
        ],
        'linkList' => [
            'title' => 'Seznam odkazů',
            'text' => 'Umožňuje libovolně nastavitelné odkazy, kterými se jedním kliknutím dostaneš na jakoukoli stránku ve hře.',
        ],
        'villageStatistics' => [
            'title' => 'Přehled vesnic',
            'text' => 'Přehled produkce, zásob, kulturních bodů a jednotek ve všech tvých vesnicích.',
        ],
        'fullScreen' => [
            'title' => 'Větší mapa',
            'text' => 'Pro lepší přehled lze mapu roztáhnout na celou obrazovku a zobrazit větší oblast.',
        ],
        'tradeMulti' => [
            'title' => 'Obchodníci mohou jet dvakrát',
            'text' => 'Obchodníci mohou automaticky zopakovat dodávku surovin podruhé.',
        ],
    ],
    'furtherInfos' => 'Travian PLUS zpřístupní výše uvedené funkce na %s dní a můžeš ho kdykoli prodloužit. Pokud zvolíš automatické prodloužení, prodlouží se den před vypršením a zlato se strhne až tehdy. Pro více informací klikni na "i".',
];
//
$Definition['productionBoostPopup']['+25%‎ lumber production'] = '+25 % produkce dřeva';
$Definition['productionBoostPopup']['+25%‎ clay production'] = '+25 % produkce hlíny';
$Definition['productionBoostPopup']['+25%‎ iron production'] = '+25 % produkce železa';
$Definition['productionBoostPopup']['+25%‎ crop production'] = '+25 % produkce obilí';
$Definition['productionBoostPopup']['Production bonus'] = 'Bonusy produkce';
$Definition['productionBoostPopup']['Select which resources production you would like to increase:'] = 'Funkce, která zvyšuje vybrané suroviny:';
$Definition['productionBoostPopup']['Bonus duration in days:'] = 'trvání ve dnech:';
$Definition['productionBoostPopup']['furtherInfos'] = 'Bonus produkce zvýší produkci vybraných surovin ve všech tvých vesnicích o 25%% na %s dní. Pokud zvolíš automatické prodloužení, bonus se po vypršení automaticky znovu aktivuje.';

$Definition['Support'] = [
    'Support' => 'Podpora',
    'description' => 'Pomocí následujícího formuláře můžeš poslat dotaz podpoře. Věnuj prosím chvíli tomu, abys otázky ve formuláři zodpověděl co nejpodrobněji, abychom ti mohli odpovědět rychle a důkladně. Bez platné e-mailové adresy nebude tvůj dotaz zpracován.',
    'Game errors, login errors and game rules related questions' => 'Chyby ve hře, problémy s přihlášením a dotazy k pravidlům',
    'Game world' => 'Herní svět',
    'please select' => 'vyber prosím',
    'I don´t know' => 'Nevím',
    'Username' => 'Uživatelské jméno',
    'Email' => 'E-mail',
    'Category' => 'Kategorie',
    'Message' => 'Zpráva',
    'General questions' => 'Obecné dotazy',
    'I cannot log in' => 'Nemohu se přihlásit',
    'I cannot register an account' => 'Nemohu si zaregistrovat účet',
    'send request' => 'odeslat dotaz',
    'Captcha' => 'Captcha',
    'errors' => [
        'vyber prosím',
        'This field is necessary' => 'Toto pole je povinné.',
        'Entry is too short' => 'Text je příliš krátký.',
        'Invalid email address' => 'Neplatná e-mailová adresa.',
        'Wrong captcha' => 'Špatná captcha.',
    ],
    'done' => 'Pokusíme se ti co nejdřív pomoci. Buď prosím trpělivý – odpověď obvykle dostaneš do 24 hodin.',
];

$Definition['inGameSupport'] = [
    'Support' => 'Podpora',
    'description' => 'V našem systému nápovědy Odpovědi rychle a bez dlouhého hledání najdeš odpovědi na všechny obecné otázky o Travianu. Navíc máš možnost kontaktovat podporu. Odpověď podpory může trvat až 24 hodin. Pro rychlejší odpověď zkus Odpovědi.',
    'Game errors, login errors and game rules related questions' => 'Chyby ve hře, problémy s přihlášením a dotazy k pravidlům',
    'Category' => 'Kategorie',
    'Message' => 'Zpráva',
    'general questions' => 'obecné dotazy',
    'report an error' => 'nahlásit chybu',
    'send request' => 'odeslat dotaz',
    'please select' => 'vyber prosím',
    'Game support' => 'Podpora hry',
    'Violation of the rules' => 'Porušení pravidel',
    'Plus support' => 'Podpora Plus',
    'Back to the village' => 'Zpět do vesnice.',
    'Captcha' => 'Captcha',
    'errors' => [
        'vyber prosím',
        'This field is necessary' => 'Toto pole je povinné.',
        'Entry is too short' => 'Text je příliš krátký.',
        'Wrong captcha' => 'Špatná captcha.',
    ],
    'done' => 'Pokusíme se ti co nejdřív pomoci. Buď prosím trpělivý – odpověď obvykle dostaneš do 24 hodin.',
];
$Definition['LinkList']['Vouchers (GoldBank)'] = 'Poukázky (Zlatá banka)';
$Definition['LinkList']['Farmlist'] = 'Seznam farem';
$Definition['LinkList']['Farms'] = 'Farmy';
$Definition['LinkList']['Go to admin panel'] = 'Přejít do administrace';
$Definition['LinkList']['Contact Support'] = 'Kontaktovat podporu';

$Definition['Email']['serverStartEmailSubject'] = 'Brzy začíná nový server';
$Definition['Email']['serverStartEmail'] = '
<div style="font-size: 14px;">
Milý/á [PLAYERNAME], <br /> herní svět <b><u>[SERVER_NAME]</b></u> brzy začne!
<br />
<p style="color: red; font-weight: bold; font-size: 16px; text-align: center;">Server začne <u>[SERVER_START_TIME]</u>.</p>
<br />
Tímto e-mailem tě upozorňujeme, že herní server <b><u>[SERVER_NAME]</u></b> <b>brzy začne</b>. Nic nepropásni. Buď mezi prvními, kdo se aktivuje a připojí.
<br />
<p style="color: green; font-weight: bold; font-size: 16px; text-align: center;">
Klikni na odkaz a užij si hru: <a href="[SERVER_URL]">[SERVER_URL]</a>
</p>
</div>';
$Definition['redeemCode'] = [
    'Redeem' => 'Uplatnit',
    'EnterYourCodeTo' => 'Pokud máš kód k uplatnění, zadej ho níže a uplatni svůj nákup:',
    'Redeem code' => 'Uplatnit kód',
    'Purchased code' => 'Zakoupený kód',
    'invalidCode' => 'Neplatný kód.',
    'codeIsUsed' => 'Tento kód už byl použit.',
    'redeemSuccess' => 'Kód byl úspěšně uplatněn. Zlato ti bude brzy připsáno.',
    'tooManyTries' => 'Příliš mnoho pokusů. Zkus to prosím za chvíli.',
    'unknownError' => 'Neznámá chyba',
];
$Definition['Voting'] = [
    'description' => 'Hlasováním na těchto webech můžeš zdarma získat %s zlata.',
    'Next vote in %s hours' => 'Další hlasování za %s hodin.',
    'Vote at TopG' => 'Hlasovat na TopG',
    'Vote at Arena Top 100' => 'Hlasovat na Arena Top 100',
    'Vote at GTop100' => 'Hlasovat na GTop100',
];
$Definition['Embassy']['Alliance'] = 'Aliance';
$Definition['Embassy']['Password'] = 'Heslo';
$Definition['Embassy']['Confirm'] = 'Potvrdit';
$Definition['Embassy']['No nearby alliance found'] = 'V okolí nebyla nalezena žádná aliance';
$Definition['Embassy']['Population'] = 'Obyvatelé';
$Definition['Embassy']['Villages within 50 fields'] = 'Vesnice do 50 polí';
$Definition['Embassy']['Alliance %s (%s), invited by %s'] = 'Aliance %s (%s), pozval tě %s';
$Definition['Embassy']['Found alliance'] = 'Založit alianci';
$Definition['Embassy']['In order to found an alliance you need to have an embassy level 3'] = 'K založení aliance potřebuješ ambasádu na úrovni 3';
$Definition['Embassy']['Join alliance'] = 'Vstoupit do aliance';
$Definition['Embassy']['Find alliance'] = 'Najít alianci';
$Definition['Embassy']['The password is wrong'] = 'Heslo je špatné.';
$Definition['Embassy']['In order to quit the alliance you have to enter your password again for safety reasons'] = 'Pro opuštění aliance musíš z bezpečnostních důvodů znovu zadat heslo';
$Definition['Embassy']['Leave the alliance'] = 'Opustit alianci';
$Definition['Embassy']['Alliance leave contribution note'] = 'Ovlivní to jen tvé celkové pořadí příspěvků. Příspěvky do současných bonusů aliance zůstanou. Pokud se vrátíš, bude tvůj součet příspěvků 0.';
$Definition['Embassy']['If you leave the alliance your contribution statistics will reset'] = 'Pokud alianci opustíš, tvé statistiky příspěvků se vynulují';

$Definition['Mail']['Hello'] = 'Ahoj';
$Definition['Mail']['Activate'] = 'Aktivovat';
$Definition['Mail']['Activation code'] = 'Aktivační kód';
$Definition['Mail']['Email verification reminder'] = 'Připomenutí ověření e-mailu';
$Definition['Mail']['Activation progress reminder'] = 'Připomenutí dokončení aktivace';
$Definition['Mail']['_TO_ACTIVATE_YOUR_ACCOUNT_PLEASE_'] = 'Účet aktivuješ kliknutím na tlačítko potvrzení nebo zkopírováním aktivačního kódu níže do prohlížeče.';
$Definition['Mail']['Continue Registration Progress'] = 'Pokračovat v registraci';
$Definition['Mail']['Your Travian Team'] = 'Tvůj tým Travianu';
$Definition['Mail']['Enjoy the game and fight many glorious battles'] = 'Užij si hru a vybojuj mnoho slavných bitev';
$Definition['Mail']['Game world Url'] = 'URL herního světa';
$Definition['Mail']['Thank you for registering on %s'] = 'Děkujeme za registraci na %s';
$Definition['Mail']['APPROVED_EMAIL_TO_CONTINUE_INSTRUCTIONS'] = 'Svou e-mailovou adresu už jsi potvrdil. Pro dokončení registrace a hraní se přihlas ke svému účtu nebo klikni na odkaz níže:';

$Definition['EVerify'] = [
    'Email verification' => 'Ověření e-mailu',
    'VERIFICATION_CODE' => 'Ověřovací kód',
    'NEED_VERIFICATION_FOR_FEATURES' => 'Abys mohl dál používat všechny funkce hry, musíš ověřit svůj e-mail.',
    'ADD_NEW_VERIFY_PROGRESS' => 'Zahájit nové ověření',
    'VERIFY' => 'Ověřit',
    'EMAIL_ADDRESS' => 'e-mailová adresa',
    'VERIFICATION_IN_PROGRESS' => 'Probíhá ověření.',
    'ENTER_CODE_OR_CLICK_ON_THE_LINK' => 'Zadej ověřovací kód nebo klikni na odkaz v e-mailu a ověř svou e-mailovou adresu.',
    'PLEASE_CHECK_SPAM_BOX' => 'Zkontroluj prosím i <b>složku se spamem</b>.',
    'TO_CANCEL_AND_CREATE_NEW_ONE_CLICK_HERE' => 'Pro zrušení současného ověření a zahájení nového <a href="/verify.php?cancelProgress">klikni sem</a>.',
    'errors' => [
        'emptyEmail' => 'Zadej prosím e-mail.',
        'invalidEmail' => 'Tvůj e-mail má špatný formát.',
        'alreadyInProgress' => 'Máš jedno probíhající ověření.',
        'emailAlreadyExists' => 'Tato e-mailová adresa už existuje. Na jeden e-mail je povolen jeden účet.',
        'emailBlacklisted' => 'Tento e-mail je na černé listině. S touto adresou účet ověřit nemůžeš.',
        'noVerificationInProgress' => 'Neprobíhá žádné ověření.',
        'emptyCode' => 'Zadej prosím ověřovací kód.',
        'invalidCode' => 'Ověřovací kód je neplatný.',
        'verificationSuccessFull' => 'Ověření proběhlo úspěšně. Všechny funkce jsou odemčené.',
        'invalidCaptcha' => 'Neplatná captcha.',
        'verificationEmailHasBeenSent' => 'Aktivační kód byl odeslán na tvůj e-mail.<br/>Klikni na odkaz v e-mailu nebo sem zadej obdržený kód a potvrď, že e-mailová adresa patří tobě.',
        'tooManyResends' => 'Požádal jsi o opětovné odeslání příliš mnohokrát. Chvíli počkej.',
    ],
    'limitations' => [
        'Pokud heslo zapomeneš, nebudeš ho moci obnovit.',
        'Nemůžeš posílat žádné pohyby (útoky, posily) kromě dobrodružství a obchodů.',
        'Nemůžeš nic kupovat.',
        'Nemůžeš používat systém poukázek.',
        'Tvé zbývající zlato a bonusy se k tvému e-mailu neuloží.',
        'Tvé medaile se neuloží.',
        'Tvé medaile se nebudou zobrazovat.',
    ],
    'UNVERIFIED_ACCOUNT_LIMITATIONS' => 'Omezení neověřeného účtu',
    'YOU_CANNOT_DO_THE_FOLLOWING_THINGS' => 'Pokud svůj e-mail neověříš, nemůžeš dělat následující',
    'WE_ARE_NOT_RESPONSIBLE_AS_ADMINISTRATORS_TO_RESTORE' => 'Pokud svůj e-mail neověříš, nepomůžeme ti jako administrátoři obnovit heslo k účtu, uložit zbývající zlato, obnovit medaile atd.',
    'YOU_ARE_THE_ONLY_ONE_RESPONSIBLE' => 'Za toto ověření odpovídáš jen ty.',
    'TO_LOGIN_PLEASE_CLICK_HERE' => 'Pro přihlášení <a href="login.php">klikni sem</a>.',
    'NO_CODE_ENTERED' => 'Nebyl zadán žádný kód.',
    'ENTERED_CODE_INVALID' => 'Zadaný kód je neplatný.',
    'EMAIL_EXISTS_CANT_VERIFY' => 'Tvou e-mailovou adresu jsme nemohli ověřit, protože už je ověřená u jiného účtu.',
    'EMAIL_BLACKLISTED_CANT_VERIFY' => 'Tvou e-mailovou adresu jsme nemohli ověřit, protože je na černé listině.',
    'NO_MATCHING_ROWS_FOUND' => 'K tvému ověřovacímu kódu jsme nenašli žádný záznam.',
    'YOUR_ACCOUNT_WAS_VERIFIED_SUCCESSFULLY' => 'Tvůj e-mail byl úspěšně ověřen.',
    'email' => [
        'HELLO_X' => 'Ahoj %s,',
        'THANKS_FOR_REGISTERING_ACCOUNT' => 'Děkujeme za registraci účtu v Travianu!',
        'INSTRUCTIONS' => 'Zaregistroval ses do světa Travianu. Svou e-mailovou adresu ověříš kliknutím na následující odkaz nebo zadáním ověřovacího kódu přímo do systému:',
        'YOUR_ACCESS_DATA' => 'Tvé přístupové údaje',
        'PLAYER_NAME' => 'Jméno hráče',
        'EMAIL_ADDRESS' => 'E-mailová adresa',
        'GAME_WORLD' => 'Herní svět',
        'VERIFICATION_CODE' => 'Ověřovací kód',
        'HAVE_A_GOOD_TIME_AND_MANY_GLORIOUS_BATTLES' => 'Užij si hru a mnoho slavných bitev.',
        'YOUR_TRAVIAN_TEAM' => 'Tvůj tým Travianu',
        'GAME_HINTS' => 'Tipy ke hře',
        'IN_OUR_ANSWERS_AND_FORUM' => 'V Travian Odpovědích najdeš odpovědi na %s mnoho otázek o Travianu. Navštívit můžeš i naše fórum na %s, kde najdeš novinky a můžeš se bavit s ostatními hráči.',
    ],
    'YOUR_EMAIL_ADDRESS_IS_NOT_VERIFIED_TO_VERIFY_CLICK_HERE' => 'Tvá e-mailová adresa není ověřená. Pro ověření <a href="/verify.php">klikni sem</a>.',
    'TO_RESEND_EMAIL_PLEASE_CLICK_HERE' => 'Pro opětovné odeslání ověřovacího e-mailu <a href="verify.php?resendEmail">klikni sem</a>.',
    'LOGIN_BEFORE_GAME_DESCRIPTION_VERIFY' => 'Tvůj účet je aktivovaný a e-mail zatím ověřovat nemusíš. Hned po spuštění serveru můžeš hrát se svým uživatelským jménem a heslem.',
];
$Definition['Truce'] = [
    'public_infobox' => 'Veřejné příměří aktivní<br>Začátek: %s<br>Konec: %s',
    'reasons' => [
        0 => 'Normální',
        1 => 'Vánoce',
        2 => 'Nový rok',
        3 => 'Velikonoce',
        4 => 'Smutek Ášúry',
    ],
    'infobox_reasons' => [
        0 => 'Běžné příměří aktivní<br>Začátek: %s<br>Konec:%s',
        1 => 'Vánoční příměří aktivní<br>Začátek: %s<br>Konec:%s',
        2 => 'Novoroční příměří aktivní<br>Začátek: %s<br>Konec:%s',
        3 => 'Velikonoční příměří aktivní<br>Začátek: %s<br>Konec:%s',
        4 => 'Příměří Ášúry aktivní<br>Začátek: %s<br>Konec:%s',
    ],
    'report_descriptions' => [
        0 => '%s navštívil tvou vesnici.',
        1 => '%s ti přeje veselé Vánoce.',
        2 => '%s ti přeje šťastný nový rok.',
        3 => '%s ti přeje veselé Velikonoce.',
        4 => '%s vyjadřuje soustrast k Ášúře',
    ],
];
$Definition['AllianceBonus'] = [
    'As sitter you can`t contribute to alliance bonuses' => 'Jako zástupce nemůžeš přispívat do bonusů aliance.',
    'You have reached you daily contribution limit, Reset in %s' => 'Dosáhl jsi denního limitu příspěvků. Obnovení za %s.',
    'Your contribution will be tripled' => 'Tvůj příspěvek bude ztrojnásoben',
    'Your active village was changed' => 'Tvá aktivní vesnice byla změněna',
    'Invalid resources entered' => 'Zadány neplatné suroviny',
    'Not enough are not available' => 'Nedostatek surovin',
    'The amount of gold could not be subtracted from your account' => 'Zlato se z tvého účtu nepodařilo odečíst',
    'Contribution: %s resources' => 'Příspěvek: %s surovin',
    'Tripled: %s resources' => 'Ztrojnásobeno: %s surovin',
    'Contribute' => 'Přispět',
    'Contribution failed:' => 'Příspěvek se nezdařil:',
    'You can`t contribute when next level is unlocking' => 'Během odemykání další úrovně nemůžeš přispívat',
    'Contribution limit for today is reached' => 'Denní limit příspěvků je vyčerpán',
    'Recruitment' => 'Nábor',
    'Philosophy' => 'Filozofie',
    'Metallurgy' => 'Metalurgie',
    'Commerce' => 'Obchod',

    'training_upgrading' => 'Nábor se vylepšuje',
    'cp_upgrading' => 'Filozofie se vylepšuje',
    'armor_upgrading' => 'Metalurgie se vylepšuje',
    'trade_upgrading' => 'Obchod se vylepšuje',

    'training_bonus_maxed' => 'Nábor je na maximální úrovni',
    'cp_bonus_maxed' => 'Filozofie je na maximální úrovni',
    'armor_bonus_maxed' => 'Metalurgie je na maximální úrovni',
    'trade_bonus_maxed' => 'Obchod je na maximální úrovni',

    'Faster troop production bonus' => 'Bonus rychlejšího výcviku jednotek',
    'Culture Points production bonus' => 'Bonus produkce kulturních bodů',
    'Weapons and armor bonus' => 'Bonus zbraní a zbroje',
    'Merchant capacity bonus' => 'Bonus kapacity obchodníků',
    'Please select a bonus' => 'Vyber prosím bonus.',
    'Sum:' => 'Součet:',
    'Contribute x3' => 'Přispět ×3',
    'AllianceBonusDescription' => 'Týmová práce je ve světě Travianu klíčem k úspěchu. Přispívej surovinami, získej bonusy pro celou alianci a zvyš svou výhodu nad ostatními hráči. Protože bonusy vyžadují velké množství surovin, musí přispívat celá aliance.',
    'Contribute resources' => 'Přispět surovinami',
    'My daily contribution limit (reset in %s)' => 'Můj denní limit příspěvků (obnovení za %s)‎',
    'Amount of resources you can still contribute: %s' => 'Množství surovin, které ještě můžeš přispět: %s',
    'Contribution successful' => 'Příspěvek byl úspěšný.',
    'Alliance bonus overview' => 'Přehled bonusů aliance',
    'Contributors of the Week' => 'Přispěvatelé týdne',
    'Contributors of all Time' => 'Přispěvatelé všech dob',
    'NoColumn' => 'Č.',
    'Player(s)' => 'Hráči',
    'Resources' => 'Surovinové',
    'Amount of resources needed to reach next level: %s' => 'Množství surovin potřebné k dosažení další úrovně: %s',
    'training_bonus_desc' => 'Sdílení vojenských poznatků může být k vítězství ve válce zásadní. Pokroky ve vývoji obléhacích strojů a dobře vyškolení instruktoři rychleji zvětší tvou armádu.',
    'armor_bonus_desc' => 'K prosperitě říše je potřeba víc než jen armáda. Dopřej svému lidu vzdělání v humanitních vědách, ať se stane průkopníkem architektury a plánování vesnic. Vlivná kultura přiměje cizí národy přijmout tvou vládu.',
    'cp_bonus_desc' => 'Konflikty v dějinách rozhodoval vždy stejný princip: kdo má větší klacek. Vylepšená zbroj a lepší zbraně umožní tvým vojákům přemoci nepřátele.',
    'trade_bonus_desc' => 'Bitva bez zásobování je prohraná bitva. Soumaři a větší vozy umožní tvým obchodníkům odvézt více surovin tam, kde jsou nejvíc potřeba, ať je to fronta, nebo spolubojovník v nouzi.',
    'training_bonus_upgrading' => 'Bonus náboru se vylepšuje a bude dostupný %s',
    'armor_bonus_upgrading' => 'Bonus filozofie se vylepšuje a bude dostupný %s',
    'cp_bonus_upgrading' => 'Bonus metalurgie se vylepšuje a bude dostupný %s',
    'trade_bonus_upgrading' => 'Bonus obchodu se vylepšuje a bude dostupný %s',
    'You joined the alliance less than 24:00 hours ago' => 'Do aliance jsi vstoupil před méně než 24:00 hodinami.',
    'You can still donate, but you may have to wait for the bonuses to unlock' => 'Přispívat můžeš i tak, ale na odemčení bonusů možná budeš muset počkat.',
    'Bonus activation time: %s' => 'Čas aktivace bonusu: %s',
    'Alliance bonus level %s (+%s%%)' => 'Bonus aliance úrovně %s (+%s%%)',
];
$Definition['WWChangeName'] = [
    'Name' => 'Jméno',
    'Wonder of the world name' => 'Název divu světa',
    'What do you want Wonder of the world name to be?' => 'Jak se má tvůj div světa jmenovat?',
    'Can be changed until level 11' => 'Lze změnit do úrovně 11',
];


$Definition['TransferGold'] = [
    'title' => 'Převést zlato',
    'desc' => 'Zlato můžeš ze svého účtu převést na jiné účty.',
    'transfer_cost' => 'Poplatek za převod: %s%% zlata',
    'only_bought' => '🔴 Převést lze jen koupené zlato.',
    'the_following_errors' => 'Došlo k těmto chybám:',
    'available_gold' => 'Dostupné zlato',
    'gold_amount' => 'Množství zlata',
    'target_player' => 'Cílový hráč',
    'cost' => 'Poplatek',
    'transfer' => 'Převést',
    'InvalidGoldAmount' => 'Neplatné množství zlata',
    'YouDoNotHaveEnoughGold' => 'Nemáš dost zlata',
    'TargetPlayerUnknown' => 'Neznámý cílový hráč',
    'TargetPlayerNotFound' => 'Cílový hráč nenalezen',
    'transfer_success_msg' => '%s <img src="img/x.gif" class="gold"> bylo převedeno hráči %s',
    'recent_transfers' => 'Poslední převody',
    'player' => 'Hráč',
    'amount' => 'Množství',
    'date' => 'Datum',
    'TransferMsg' => [
        'Subject' => 'Převod zlata dokončen',
        'Message' => "Ahoj %s,

na tvůj účet bylo převedeno %s zlata od hráče %s.

Ať se daří.",
    ],
    'SenderEmailAddress' => 'E-mailová adresa odesílatele',
    'InvalidAccountEmail' => 'E-mailová adresa odesílatele je neplatná.',
];


// Travian Legends style (graphic pack 29c89d54): task list, auction lock box, hero inventory tab.
$Definition['Tasks']['title'] = 'Tvé úkoly';
$Definition['Tasks']['villageTasks'] = 'Úkoly pro vesnici %s';
$Definition['Tasks']['generalTasks'] = 'Obecné úkoly';
$Definition['Tasks']['collect'] = 'Vyzvednout';
$Definition['Tasks']['allDone'] = 'Všechny kroky splněny';
$Definition['Tasks']['toLevel'] = 'na úroveň %d';
$Definition['Tasks']['heroLevel'] = 'Úroveň hrdiny';
$Definition['Tasks']['bonusRewards'] = 'Bonus k odměnám = %d%%';
$Definition['Tasks']['bonusDescription'] = 'Tvůj hrdina ti podle své úrovně dává bonus k odměnám.';
$Definition['Tasks']['nextLevel'] = 'Další úroveň = %d%%';
$Definition['Tasks']['rewardResources'] = 'Suroviny od každého druhu';
$Definition['Tasks']['rewardExperience'] = 'Zkušenosti hrdiny';
$Definition['Tasks']['overview'] = 'Přehled úkolů';
$Definition['Tasks']['population'] = 'Obyvatelé';
$Definition['Tasks']['culturePoints'] = 'Produkce kulturních bodů';
$Definition['Tasks']['villages'] = 'Vesnice';
$Definition['Tasks']['populationTotal'] = 'Obyvatelé všech vesnic';
$Definition['Tasks']['culturePointsTotal'] = 'Produkce kulturních bodů všech vesnic';
$Definition['Auction']['auctionsLocked'] = 'Aukce zamčené';
$Definition['HeroGlobal']['Inventory'] = 'Inventář';

$Definition['Tasks']['dailyReset'] = 'Další obnovení: %s. Do té doby si vyzvedni odměny!';
$Definition['Tasks']['collectRewards'] = 'Vyzvednout odměny';
$Definition['Tasks']['goldShopTitle'] = 'Zlato a Plus';
$Definition['Tasks']['goldCredited'] = 'Na tvůj účet bylo připsáno %d zlata.';
$Definition['Tasks']['offlineGold'] = 'Offline svět: platby nejsou nastavené, balíčky se připisují zdarma.';
$Definition['Tasks']['bestSeller'] = 'Nejprodávanější';
$Definition['Tasks']['bestValue'] = 'Nejvýhodnější';
$Definition['Tasks']['free'] = 'Propustit';

// Travian Legends 655 hero window and auctions (graphic packs with heroV2)
$Definition['HeroV2']['amount'] = 'Množství';
$Definition['HeroV2']['auctionsBuy'] = 'Koupit';
$Definition['HeroV2']['auctionsOverview'] = 'Přehled aukcí';
$Definition['HeroV2']['auctionsSell'] = 'Prodat';
$Definition['HeroV2']['bidsHeader'] = 'Nabídky';
$Definition['HeroV2']['buyOptions'] = 'Koupit vybrané možnosti';
$Definition['HeroV2']['cancel'] = 'Zrušit';
$Definition['HeroV2']['consumables'] = 'Spotřební předměty';
$Definition['HeroV2']['equipmentBenefits'] = 'Výhody hrdinova vybavení';
$Definition['HeroV2']['fieldsPerHour'] = 'polí za hodinu';
$Definition['HeroV2']['finishAdventures'] = 'Dokonči %s <a href="%s">dobrodružství</a> a odemkni aukce a směnárnu!';
$Definition['HeroV2']['grouped'] = 'Seskupit';
$Definition['HeroV2']['heroProduction'] = 'Produkce hrdiny';
$Definition['HeroV2']['highestBidderYou'] = 'Ty';
$Definition['HeroV2']['item'] = 'Předmět';
$Definition['HeroV2']['itemLevel'] = 'Úroveň %s';
$Definition['HeroV2']['maximumBid'] = 'Nejvyšší nabídka';
$Definition['HeroV2']['nextFinish'] = 'Nejbližší konec';
$Definition['HeroV2']['nextPrice'] = 'Další cena';
$Definition['HeroV2']['noBuyOptions'] = 'Všechny možnosti v tomto světě jsou zdarma.';
$Definition['HeroV2']['noItems'] = 'Momentálně nejsou k dispozici žádné předměty';
$Definition['HeroV2']['price'] = 'Cena';
$Definition['HeroV2']['random'] = 'Náhodně';
$Definition['HeroV2']['reload'] = 'Obnovit';
$Definition['HeroV2']['resetChanges'] = 'Vrátit změny';
$Definition['HeroV2']['runningAuctions'] = 'Probíhající aukce';
$Definition['HeroV2']['scar'] = 'Jizva';
$Definition['HeroV2']['sellDescription'] = 'Kliknutím na předmět v inventáři zahájíš aukci. Ostatní hráči na něj mohou přihazovat. Aukce trvá až %s hodin.';
$Definition['HeroV2']['sellHorseForSilver'] = 'Vyměnit tohoto koně za %s stříbra';
$Definition['HeroV2']['sellItems'] = 'Prodat předměty';
$Definition['HeroV2']['slot_all'] = 'Všechny předměty';
$Definition['HeroV2']['slot_bag'] = 'Brašna';
$Definition['HeroV2']['slot_body'] = 'Brnění';
$Definition['HeroV2']['slot_helmet'] = 'Helma';
$Definition['HeroV2']['slot_horse'] = 'Poník';
$Definition['HeroV2']['slot_leftHand'] = 'Levá ruka';
$Definition['HeroV2']['slot_rightHand'] = 'Zbraň';
$Definition['HeroV2']['slot_shoes'] = 'Boty';
$Definition['HeroV2']['startAuction'] = 'Zahájit aukci';
$Definition['HeroV2']['startedAt'] = 'Končí';
$Definition['HeroV2']['status'] = 'Stav';
$Definition['HeroV2']['tattoo'] = 'Tetování';
$Definition['HeroV2']['timeLeft'] = 'Zbývající čas';
$Definition['HeroV2']['tradeItems'] = 'Obchod s předměty';
$Definition['HeroV2']['xAuctions'] = '%s aukcí';
$Definition['HeroV2']['yourAuctions'] = 'Tvé aukce';
$Definition['HeroV2']['yourBids'] = 'Tvé nabídky';

// <ov-legends-ui> OpenVillage Legends UI strings (hero resource items, inventory menu, build page, adventures).
$Definition['Buildings']['description'] = 'Popis';
$Definition['HeroItems'][16][145]['name'] = 'Vozík dřeva';
$Definition['HeroItems'][16][145]['title'] = 'Dřevo: přesune dřevo do skladu surovin tvé aktuální vesnice.';
$Definition['HeroItems'][16][146]['name'] = 'Vozík hlíny';
$Definition['HeroItems'][16][146]['title'] = 'Hlína: přesune hlínu do skladu surovin tvé aktuální vesnice.';
$Definition['HeroItems'][16][147]['name'] = 'Vozík železa';
$Definition['HeroItems'][16][147]['title'] = 'Železo: přesune železo do skladu surovin tvé aktuální vesnice.';
$Definition['HeroItems'][16][148]['name'] = 'Pytel obilí';
$Definition['HeroItems'][16][148]['title'] = 'Obilí: přesune obilí do sýpky tvé aktuální vesnice.';
$Definition['HeroV2']['adventureDistance'] = 'Vzdálenost';
$Definition['HeroV2']['adventureFields'] = '%s polí';
$Definition['HeroV2']['adventurePlace'] = 'Místo';
$Definition['HeroV2']['adventures'] = 'Dobrodružství';
$Definition['HeroV2']['consumable'] = 'Spotřební předměty';
$Definition['HeroV2']['continue'] = 'Pokračovat';
$Definition['HeroV2']['equip'] = 'Nasadit';
$Definition['HeroV2']['explore'] = 'Prozkoumat';
$Definition['HeroV2']['info'] = 'Info';
$Definition['HeroV2']['move'] = 'Přesunout';
$Definition['HeroV2']['moveAmount1'] = 'Množství dřeva k přesunu';
$Definition['HeroV2']['moveAmount2'] = 'Množství hlíny k přesunu';
$Definition['HeroV2']['moveAmount3'] = 'Množství železa k přesunu';
$Definition['HeroV2']['moveAmount4'] = 'Množství obilí k přesunu';
$Definition['HeroV2']['moveMax'] = 'Doplnit';
$Definition['HeroV2']['moveMaximum'] = 'Přesunout maximum';
$Definition['HeroV2']['moveResources'] = 'Přesunout suroviny';
$Definition['HeroV2']['moveResourcesText'] = 'Přesunout suroviny do skladu tvé aktuální vesnice: %s';
$Definition['HeroV2']['resourcesMoved'] = 'Suroviny byly přesunuty do vesnice.';
$Definition['HeroV2']['resourcesNotHome'] = 'Pro přesun surovin musí být hrdina v aktivní vesnici.';
$Definition['HeroV2']['sort'] = 'Seřadit';
$Definition['HeroV2']['sortDone'] = 'Hotovo';
$Definition['HeroV2']['sortHint'] = 'Přetáhni předměty na nová místa a pak klikni na Hotovo.';
$Definition['HeroV2']['stackable'] = 'Předmět lze skládat.';
$Definition['HeroV2']['unequip'] = 'Sundat';
$Definition['HeroV2']['use'] = 'Použít';
$Definition['Tasks']['rewardToHero'] = 'Suroviny byly vloženy do inventáře tvého hrdiny.';
// </ov-legends-ui>
// <ov-cs-extra> keys used by the OpenVillage templates that only had an English default in the code
$Definition['ActivationNew']['change'] = 'Změnit';
$Definition['ActivationNew']['characterName'] = 'Zadej jméno své postavy:';
$Definition['ActivationNew']['characterNameInfo'] = 'Pod tímto jménem tě uvidí ostatní hráči v herním světě.';
$Definition['ActivationNew']['characterNamePlaceholder'] = 'Jméno postavy';
$Definition['ActivationNew']['confirmChoice'] = 'Potvrď svůj výběr';
$Definition['ActivationNew']['confirmDescription'] = 'Potvrď svůj výběr, zvol jméno své postavy a začni své dobrodružství.';
$Definition['ActivationNew']['letsPlay'] = 'Jdeme hrát';
$Definition['ActivationNew']['nameInvalid'] = 'Jméno musí mít 3–15 písmen, číslic, mezer, podtržítek nebo pomlček.';
$Definition['ActivationNew']['nameTaken'] = 'Toto jméno už je obsazené.';
$Definition['ActivationNew']['selectStartingSector'] = 'Vyber výchozí oblast';
$Definition['HeroInventory']['inventory'] = 'Inventář';
$Definition['HeroV2']['cat_jaw'] = 'Brada';
$Definition['HeroV2']['eyeColor'] = 'Barva očí';
$Definition['HeroV2']['skinColor'] = 'Barva pleti';
$Definition['HeroV2']['zoom'] = 'Přiblížit';
$Definition['Login']['registerNow'] = 'Připojit se k hernímu světu';
$Definition['GoldBank']['Are you sure you want to transfer %s gold to this account?'] = 'Opravdu chceš na tento účet převést %s zlata?';
$Definition['GoldBank']['Transfer'] = 'Převést';
$Definition['MarketPlace']["x's offering has been accepted"] = 'Nabídka hráče x byla přijata';
$Definition['MarketPlace']['you are in vacationMode'] = 'Jsi v dovolenkovém režimu.';
$Definition['PaymentWizard']['Amount'] = 'Množství';
$Definition['PaymentWizard']['Building upgraded'] = 'Budova rozšířena';
$Definition['PaymentWizard']['Buildings'] = 'Budovy';
$Definition['PaymentWizard']['Delivery'] = 'Doručení';
$Definition['PaymentWizard']['Resources'] = 'Suroviny';
$Definition['PaymentWizard']['Troop type'] = 'Typ jednotky';
$Definition['Reports']['Inbox size'] = 'Velikost schránky';
$Definition['Reports']['x switched from y to z'] = '%s přešel z aliance %s do %s.';
$Definition['Tasks']['activated'] = 'Aktivováno';
$Definition['Tasks']['choosePayment'] = 'Vyber způsob platby';
$Definition['Tasks']['forDuration'] = 'na %s';
$Definition['Tasks']['goldClubShort'] = 'Větší efektivita díky automatickým obchodníkům, seznamům farem a dalším výhodám.';
$Definition['Tasks']['increaseProductionClay'] = 'Zvýšit produkci hlíny';
$Definition['Tasks']['increaseProductionCrop'] = 'Zvýšit produkci obilí';
$Definition['Tasks']['increaseProductionIron'] = 'Zvýšit produkci železa';
$Definition['Tasks']['increaseProductionWood'] = 'Zvýšit produkci dřeva';
$Definition['Tasks']['nDays'] = '%d dní';
$Definition['Tasks']['nHours'] = '%d hodin';
$Definition['Tasks']['next'] = 'Další';
$Definition['Tasks']['oneDay'] = '%d den';
$Definition['Tasks']['oneHour'] = '%d hodina';
$Definition['Tasks']['plusShort'] = 'Vylepši svou říši větší stavební frontou, varováním před útoky a dalšími možnostmi.';
$Definition['Tasks']['stillActive'] = 'Ještě aktivní:';
$Definition['Tasks']['travianPlus'] = 'Travian Plus';
for ($i = 1; $i <= 4; $i++) {
    $Definition['HeroItems'][16][144 + $i]['title'] = 'Přesune suroviny do skladu tvé aktuální vesnice.';
}
// </ov-cs-extra>
// <ov-openvillage> texty stránek OpenVillage (patička, menu lobby)
$Definition['OpenVillage'] = [
    'home' => 'Domů',
    'lobby' => 'Lobby',
    'login' => 'Přihlášení',
    'rules' => 'Pravidla a provoz',
    'rulesShort' => 'Pravidla',
    'terms' => 'Podmínky',
    'privacy' => 'Soukromí',
    'project' => 'Projekt OpenVillage na zachování hry',
    'copyright' => 'Projekt OpenVillage na zachování hry · Nesouvisí se společností Travian Games.',
    'copyrightLong' => 'Projekt OpenVillage na zachování hry · Nesouvisí se společností Travian Games ani není jí podporován.',
    'joinGameworld' => 'Připojit se k hernímu světu',
];
// </ov-openvillage>
// <ov-auctions-rog> texts of the Rise of Governors auction pages
$Definition['HeroV2']['back'] = 'Zpět';
$Definition['HeroV2']['detailedList'] = 'podrobný seznam';
$Definition['HeroV2']['compactList'] = 'kompaktní seznam';
$Definition['HeroV2']['searchItem'] = 'Hledat předmět';
$Definition['HeroV2']['bidHint'] = 'Přihoď na položky se stanovenou minimální cenou.';
$Definition['HeroV2']['itemRarity'] = 'Vzácnost předmětu';
$Definition['HeroV2']['rarityAll'] = 'Všechny';
$Definition['HeroV2']['rarityCommon'] = 'Běžné';
$Definition['HeroV2']['rarityUncommon'] = 'Neobvyklé';
$Definition['HeroV2']['rarityRare'] = 'Vzácné';
$Definition['HeroV2']['rarityEpic'] = 'Epické';
$Definition['HeroV2']['lowestPrice'] = 'Nejnižší prodejní cena';
$Definition['HeroV2']['averagePrice'] = 'Průměrná prodejní cena';
$Definition['HeroV2']['highestPrice'] = 'Nejvyšší prodejní cena';
$Definition['HeroV2']['priceChartTitle'] = 'Průměrná cena a prodeje za posledních 24 hodin';
$Definition['HeroV2']['sales'] = 'Prodeje';
$Definition['HeroV2']['averagePriceShort'] = 'Průměrná cena';
$Definition['HeroV2']['hoursAgo23'] = '23 h';
$Definition['HeroV2']['hoursAgo12'] = '12 h';
$Definition['HeroV2']['now'] = 'Nyní';
$Definition['HeroV2']['bidsColumn'] = 'nabídky';
$Definition['HeroV2']['endsColumn'] = 'Konec';
$Definition['HeroV2']['xBids'] = '%s nabídky';
$Definition['HeroV2']['done'] = 'Hotovo';
$Definition['HeroV2']['startedAtColumn'] = 'Začalo v';
$Definition['HeroV2']['statusColumn'] = 'Status';
$Definition['HeroV2']['yourBidsDescription'] = 'Všechny tvé nabídky v aktivních a ukončených aukcích.';
$Definition['HeroV2']['showMore'] = 'Zobrazit více';
$Definition['HeroV2']['soldItems'] = 'Prodané předměty';
$Definition['HeroV2']['payout'] = 'Výplata';
$Definition['HeroV2']['nextFinish'] = 'Další dokončení';
$Definition['HeroV2']['xAuctions'] = '%s Aukce';
$Definition['HeroV2']['maximumBid'] = 'Maximální nabídka';
$Definition['HeroV2']['buttonBid'] = 'Nabídnout';
$Definition['HeroV2']['highestBidderYou'] = 'Ty';
$Definition['HeroV2']['reload'] = 'obnovit';
$Definition['HeroV2']['grouped'] = 'seskupit';
// </ov-auctions-rog>
$Definition['Buildings']['fasterBuild'] = 'O %s%% rychlejší stavba';
// <ov-contextual-help> texts of the Legends contextual help tours
$Definition['ContextualHelp']['button'] = 'Zobrazit kontextovou nápovědu na této stránce';
$Definition['ContextualHelp']['close'] = 'Rozumím';
$Definition['ContextualHelp']['heroStatsTitle'] = 'Stav hrdiny';
$Definition['ContextualHelp']['heroStats'] = 'Zdraví, zkušenosti a rychlost tvého hrdiny. Zdraví se každý den obnovuje; když klesne na nulu, hrdina zemře a musí být oživen. Zkušenosti z bitev a dobrodružství zvyšují úroveň hrdiny.';
$Definition['ContextualHelp']['heroProductionTitle'] = 'Produkce hrdiny';
$Definition['ContextualHelp']['heroProduction'] = 'Vyber, kterou surovinu bude hrdina produkovat ve své domovské vesnici. „Všechny suroviny“ dávají menší množství od každé suroviny.';
$Definition['ContextualHelp']['heroAttributesTitle'] = 'Body vlastností';
$Definition['ContextualHelp']['heroAttributes'] = 'S každou úrovní získáš nové body. Rozděl je mezi sílu, útočný a obranný bonus nebo produkci surovin a potvrď je tlačítkem Uložit.';
$Definition['ContextualHelp']['heroBenefitsTitle'] = 'Bonusy z vybavení';
$Definition['ContextualHelp']['heroBenefits'] = 'Přehled všech bonusů, které hrdina právě dostává z nasazených předmětů.';
$Definition['ContextualHelp']['heroEquipmentTitle'] = 'Vybavení';
$Definition['ContextualHelp']['heroEquipment'] = 'Helma, zbroj, boty, obě ruce, kůň a taška. Předmět nasadíš přetažením z inventáře na hrdinu nebo dvojklikem.';
$Definition['ContextualHelp']['heroFiltersTitle'] = 'Filtry';
$Definition['ContextualHelp']['heroFilters'] = 'Zobrazí jen předměty, které patří do vybraného slotu vybavení.';
$Definition['ContextualHelp']['heroInventoryTitle'] = 'Inventář';
$Definition['ContextualHelp']['heroInventory'] = 'Všechny předměty, které hrdina nese. Spotřební předmět, například mast nebo svitek, použiješ kliknutím.';
$Definition['ContextualHelp']['heroTradeTitle'] = 'Obchodovat';
$Definition['ContextualHelp']['heroTrade'] = 'Otevře aukce, kde můžeš prodat nepotřebné předměty a koupit lepší za stříbro.';
$Definition['ContextualHelp']['auctionViewTitle'] = 'Zobrazení seznamu';
$Definition['ContextualHelp']['auctionView'] = 'Seskup nabídky podle typu předmětu, nebo zobraz všechny aukce. Tlačítka přepínají podrobný a kompaktní seznam a obnoví ho.';
$Definition['ContextualHelp']['auctionFilterTitle'] = 'Filtr';
$Definition['ContextualHelp']['auctionFilter'] = 'Zobrazí jen předměty pro jeden slot vybavení nebo zvolené vzácnosti.';
$Definition['ContextualHelp']['auctionSilverTitle'] = 'Stříbro';
$Definition['ContextualHelp']['auctionSilver'] = 'Aukce se platí stříbrem. Ve směnárně si vyměníš zlato a stříbro.';
$Definition['ContextualHelp']['auctionListTitle'] = 'Nabídky';
$Definition['ContextualHelp']['auctionList'] = 'Kliknutím na řádek zobrazíš všechny aukce daného předmětu. Nabídni maximum, které jsi ochoten zaplatit; zaplatíš jen o jedno stříbro víc než druhá nejvyšší nabídka.';
// </ov-contextual-help>
// Registration page of a world in its own look (register.php, RegisterCtrl)
$Definition['Register']['title'] = 'Připojit se k hernímu světu';
$Definition['Register']['heading'] = 'Založ si účet';
$Definition['Register']['expired'] = 'Formulář vypršel. Zkus to prosím znovu.';
$Definition['Register']['tooMany'] = 'Příliš mnoho pokusů. Počkej prosím minutu.';
$Definition['Register']['nameInvalid'] = 'Jméno hráče musí mít 3–15 písmen, číslic, mezer, podtržítek nebo pomlček.';
$Definition['Register']['emailInvalid'] = 'Zadej platnou e-mailovou adresu.';
$Definition['Register']['passwordInvalid'] = 'Heslo musí mít 10–128 znaků.';
$Definition['Register']['termsMissing'] = 'Musíš přijmout podmínky a zásady ochrany soukromí.';
$Definition['Register']['unavailable'] = 'Registrace je dočasně nedostupná.';
$Definition['Register']['taken'] = 'Toto jméno hráče nebo e-mail už je registrovaný.';
$Definition['Register']['name'] = 'Jméno hráče';
$Definition['Register']['email'] = 'E-mail';
$Definition['Register']['terms'] = 'Souhlasím s <a href="/terms.html">podmínkami</a> a <a href="/privacy.html">zásadami ochrany soukromí</a>.';
$Definition['Register']['next'] = 'Potom si vybereš národ a místo, kde začne tvoje vesnice.';
$Definition['Register']['submit'] = 'Registrovat';
$Definition['Register']['haveAccount'] = 'Už mám účet – přihlásit se';
return $Definition;
