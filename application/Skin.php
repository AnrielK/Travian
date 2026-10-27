<?php
/**
 * Graphic packs ("vzhled") of the game.
 *
 *  ''           = the original T4.6 look (gpack/mainPage), unchanged.
 *  'legends655' = Travian Legends 655.3 (gpack/legends655/mainPage, taken over from the main branch):
 *                 the Legends stylesheet with the OpenVillage t46Layout adapter of the main branch, plus the
 *                 adapter of this branch's markup (css/t46-branch.css). The Legends hero is HeroLegends.php.
 *
 * The look is chosen per player (cookie t46_gpack: sidebar box "Settings", Preferences → Game) with the world's
 * default in config.gpack (admin panel → Server settings). The pages are not edited one by one: an output filter
 * swaps the stylesheet, adds the body classes the Legends CSS expects and moves a few blocks (stock bar, village
 * views), so the original look stays exactly as it was when no pack is chosen.
 */

final class Skin
{
    /** id => [name, path of the pack's mainPage folder] */
    const PACKS = [
        ''           => ['name' => 'Travian T4.6 (původní)', 'dir' => 'gpack/mainPage/'],
        'legends655' => ['name' => 'Travian Legends 655.3', 'dir' => 'gpack/legends655/mainPage/'],
    ];
    const COOKIE = 't46_gpack';
    /** Cache buster of the pack's stylesheet. */
    const VERSION = 6;

    private static $current = null;

    /** Packs whose folder is present (a trimmed build may leave one out). */
    public static function available(): array
    {
        $out = [];
        foreach (self::PACKS as $id => $p) {
            if ($id === '' || is_file(APP_MAIN_PATH . '/' . $p['dir'] . 'lang/en-US/compact.css')) $out[$id] = $p;
        }
        return $out;
    }

    public static function valid($id): bool
    {
        return is_string($id) && array_key_exists($id, self::available());
    }

    /** The world's default look (config.gpack). */
    public static function worldDefault(): string
    {
        $id = defined('GAME_GPACK') ? GAME_GPACK : '';
        return self::valid($id) ? $id : '';
    }

    /** The look of this request: the player's choice, else the world's default. */
    public static function current(): string
    {
        if (self::$current === null) {
            $c = $_COOKIE[self::COOKIE] ?? null;
            if ($c === 'default') $c = '';
            self::$current = ($c !== null && self::valid($c)) ? $c : self::worldDefault();
        }
        return self::$current;
    }

    public static function isLegends(): bool
    {
        return self::current() === 'legends655';
    }

    /** Remembers the player's choice ('' = original look, null = follow the world's default). */
    public static function choose($id): void
    {
        if ($id === null || $id === 'world') {
            setcookie(self::COOKIE, '', time() - 3600, '/');
            unset($_COOKIE[self::COOKIE]);
            self::$current = null;
            return;
        }
        if ($id === 'default') $id = '';
        if (!self::valid($id)) return;
        $v = $id === '' ? 'default' : $id;
        setcookie(self::COOKIE, $v, time() + 10 * 365 * 86400, '/');
        $_COOKIE[self::COOKIE] = $v;
        self::$current = null;
    }

    /** <option> list: "world default" and every available pack. */
    public static function options(string $selected): string
    {
        $world = self::PACKS[self::worldDefault()]['name'];
        $html = '<option value="world"' . ($selected === 'world' ? ' selected="selected"' : '') . '>'
            . htmlspecialchars((defined('SETTINGS_GPACK_WORLD') ? SETTINGS_GPACK_WORLD : 'World default') . ' (' . $world . ')') . '</option>';
        foreach (self::available() as $id => $p) {
            $html .= '<option value="' . ($id === '' ? 'default' : $id) . '"' . ($selected === $id && $selected !== 'world' ? ' selected="selected"' : '') . '>'
                . htmlspecialchars($p['name']) . '</option>';
        }
        return $html;
    }

    /** URL prefix of the current pack's mainPage folder (relative to the game root). */
    public static function dir(): string
    {
        return self::PACKS[self::current()]['dir'];
    }

    /** Output filter: points HTML pages at the chosen pack. */
    public static function filter(string $html): string
    {
        if (!self::isLegends()) return $html;
        // pages that start their own ob_gzhandler hand us the compressed page
        if (strncmp($html, "\x1f\x8b", 2) === 0) {
            $plain = @gzdecode($html);
            return $plain === false ? $html : gzencode(self::filter($plain), 6);
        }
        if (stripos($html, '<html') === false) return $html;
        $pack = self::PACKS['legends655']['dir'];

        // Stylesheets: the T4.6 bundle -> the Legends bundle made for this branch (lang.css / fixes.css stay).
        $html = preg_replace(
            '#<link[^>]+href="gpack/mainPage/lang/[a-zA-Z-]+/compact[^"]*\.css"[^>]*>#i',
            '<link href="' . $pack . 'lang/en-US/t46.css?v=' . self::VERSION . '" rel="stylesheet" type="text/css" />',
            $html, 1, $swapped);
        if (!$swapped) return $html;

        // body classes the Legends stylesheet and the t46Layout adapter key on
        $tribe = self::tribeName();
        $html = preg_replace_callback('#<body([^>]*?)class="([^"]*)"#i', function ($m) use ($tribe) {
            return '<body' . $m[1] . 'class="' . $m[2] . ' t46Layout t46-' . $tribe . ' legendsV2 legends655" data-theme="default"';
        }, $html, 1);

        // Legends shows the stock bar in the header, under the navigation.
        $stock = self::cutBlock($html, '<div id="stockBar"');
        if ($stock !== null) {
            $pos = self::blockEnd($html, '<div id="header"');
            if ($pos !== null) {
                $html = substr($html, 0, $pos) . $stock . substr($html, $pos);
            } else {
                $html = str_replace('<div id="contentOuterContainer"', $stock . '<div id="contentOuterContainer"', $html);
            }
        }
        // #background like the main branch: the tribe only (no showWall – the Legends CSS draws a gradient for it)
        $html = preg_replace('#<div id="background" class="[^"]*"#', '<div id="background" class="' . $tribe . '"', $html, 1);
        self::header($html);
        // the "Q" button opens the Legends daily quests page instead of the classic dialog
        $html = preg_replace('#<a class="dailyQuests" href="\#"([^>]*?) onclick="Travian\.Game\.Quest\.openTodoListDialog\(\'\', true\); return false;"#',
            '<a class="dailyQuests" href="dailyquests.php"$1', $html);
        self::tabs($html);
        self::sidebars($html);
        self::buildPage($html, $tribe);
        // window title buttons like LayoutT46: knowledge base button id, contextual help, favourite tab, button count class on the title bar
        $html = str_replace('id="answersButton"', 'id="knowledgeBaseButton"', $html);
        // pages with the old plain title buttons (map, …): framed buttons, the close button with its X
        $html = preg_replace_callback('#<a id="(closeContentButton|knowledgeBaseButton)" class="contentTitleButton"([^>]*)>\s*(?:&nbsp;)?\s*</a>#', function ($m) {
            $x = 'M0 17.01L7.01 10 .14 3.13 3.13.14 10 7.01 17.01 0 20 2.99 12.99 10l6.87 6.87-2.99 2.99L10 12.99 2.99 20 0 17.01z';
            $inner = $m[1] === 'closeContentButton'
                ? '<svg viewBox="0 0 20 20"><g class="outline"><path d="' . $x . '"></path></g><g class="icon"><path d="' . $x . '"></path></g></svg>'
                : '&nbsp;';
            return '<a id="' . $m[1] . '" class="contentTitleButton buttonFramed withIcon rectangle green"' . $m[2] . '>' . $inner . '</a>';
        }, $html);
        self::titleButtons($html);
        // Legends dialogs (Travian PLUS, confirmations) on every page
        $html = preg_replace('#</body>#i', self::dialogScripts() . '</body>', $html, 1);
        // no empty line under the window (the frame ends right under the content like in the main branch)
        $html = str_replace('<div class="contentFooter">&nbsp;</div>', '<div class="contentFooter"></div>', $html);
        $html = preg_replace_callback('#<div class="contentTitle">(.*?)</div>#s', function ($m) {
            $n = substr_count($m[1], 'contentTitleButton') - (strpos($m[1], 'closeContentButton') !== false ? 1 : 0);
            return '<div class="contentTitle buttonCount' . max(0, $n) . '">' . $m[1] . '</div>';
        }, $html, 1);

        // Legends: village views fill the page (no window), every other page is a "content page" window.
        if (preg_match('#<div id="content" class="([^"]*)"#i', $html, $c) && preg_match('#\bvillage[12]\b#', $c[1])) {
            self::cutBlock($html, '<div class="contentTitle');
            self::unwrap($html, '<div class="contentContainer"');
            if (strpos($c[1], 'village1') !== false) self::resourceView($html);
            else self::villageView($html, $tribe);
        } else {
            $html = preg_replace('#<div id="contentOuterContainer"( class="([^"]*)")?#i', '<div id="contentOuterContainer" class="$2 contentPage"', $html, 1);
        }
        return $html;
    }

    /** Texts and script of the Legends dialogs (application/legends/legendsDialogs.js). */
    private static function dialogScripts(): string
    {
        $t = function (string $c, string $d) { return LegendsUi::t($c, $d); };
        $features = [];
        foreach ([
            'directLinks' => ['Quick links', 'Improve your game with personalised quick links. Set up unique shortcuts for every village and reach your most important buildings quickly.'],
            'attackWarning' => ['Attack warning', 'Incoming attacks are shown in the village list.'],
            'buildingQueue' => ['Waiting loop', 'The waiting loop lets you queue the construction of one more building.'],
            'linkList' => ['Link list', 'Lets you set up direct links that take you to any page of the game with one click.'],
            'villageStatistics' => ['Village overview', 'Overviews of production, storage, culture points and units of all villages.'],
            'fullScreen' => ['Larger map', 'A larger map gives you a better overview of your surroundings.'],
            'tradeMulti' => ['Merchants can run 2x', 'After returning from their first delivery, merchants set off again automatically.'],
        ] as $key => [$title, $text]) {
            $c = 'LP_' . strtoupper(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key));
            $features[$key] = ['title' => $t($c, $title), 'text' => $t($c . '_TEXT', $text)];
        }
        $texts = [
            'close' => $t('UI_CLOSE', 'Close'),
            'goldCoin' => LegendsUi::goldCoin('goldCoin'),
            'plus' => [
                'title' => 'Travian PLUS',
                'gold' => $t('LP_GOLD', 'Gold'),
                'notice' => $t('LP_NOTICE', 'You need an active Travian PLUS account to use this feature!'),
                'extra' => $t('LP_EXTRA', 'You also get the following advantages:'),
                'durationDays' => $t('LP_DURATION_DAYS', 'Bonus duration (days)'),
                'durationHours' => $t('LP_DURATION_HOURS', 'Bonus duration (hours)'),
                'activate' => $t('LP_ACTIVATE', 'Activate'),
                'extend' => $t('LP_EXTEND', 'Extend'),
                'notEnough' => $t('LP_NOT_ENOUGH', 'You do not have enough gold.'),
                'features' => $features,
            ],
        ];
        return '<script type="text/javascript">window.LegendsTexts = ' . json_encode($texts, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . ';</script>'
            . '<script type="text/javascript" src="application/legends/legendsDialogs.js?v=' . self::VERSION . '"></script>';
    }

    /** Offset of the closing tag of the element starting with $start (balanced <div>s), before "</div>". */
    private static function blockEnd(string $html, string $start): ?int
    {
        $from = strpos($html, $start);
        if ($from === false) return null;
        $depth = 0;
        if (!preg_match_all('#<div\b|</div\s*>#i', $html, $m, PREG_OFFSET_CAPTURE, $from)) return null;
        foreach ($m[0] as [$tag, $off]) {
            $depth += ($tag[1] === '/') ? -1 : 1;
            if ($depth === 0) return $off;
        }
        return null;
    }

    /** Resource view: the T4.6 info boxes -> the Legends ones (villageInfoWrapper / villageInfobox). */
    private static function resourceView(string &$html): void
    {
        // Legends shows the wall behind windows only, not behind the resource fields.
        $html = preg_replace('#(<div id="background" class="[^"]*?)\bshowWall\b#', '$1', $html, 1);
        $html = str_replace('<div class="village1Content">', '<div class="villageInfoWrapper">', $html);
        self::unwrap($html, '<div id="map_details"');
        $html = preg_replace('#<div class="boxes-(?:tl|tr|tc|ml|mr|mc|bl|br|bc)">\s*</div>\s*#', '', $html);
        while (strpos($html, '<div class="boxes-contents cf">') !== false) self::unwrap($html, '<div class="boxes-contents cf">');
        $html = preg_replace('#<div class="boxes villageList (production|units)"#', '<div class="villageInfobox $1"', $html);
        $html = preg_replace('#<div class="movements"#', '<div class="villageInfobox movements"', $html, 1);
    }

    /** Top bar like LayoutT46 of the main branch: Legends logo, server time box, round framed buttons. */
    private static function header(string &$html): void
    {
        $logo = '<a id="logo" class="legendsLogo" href="dorf1.php" title="Travian"><svg viewBox="0 0 172 40" aria-label="Travian">'
            . '<path class="emblem" d="M4 3h18v18c0 8-5 13-9 16-4-3-9-8-9-16z"/>'
            . '<circle class="coin" cx="13" cy="17" r="6.5"/><path class="coinMark" d="M10 13.5h6v1.6h-2.2v6.4h-1.6v-6.4H10z"/>'
            . '<text class="title" x="30" y="23">TRAVIAN</text><text class="subtitle" x="31" y="35">L E G E N D S</text></svg></a>';
        $from = strpos($html, '<a id="logo"');
        if ($from !== false && ($end = strpos($html, '</a>', $from)) !== false) {
            $html = substr($html, 0, $from) . $logo . substr($html, $end + 4);
        }
        // <ul id="outOfGame"> (T4.6) -> <nav id="outOfGame"> with the four round buttons
        if (preg_match('#<ul id="outOfGame"[^>]*>.*?</ul>#s', $html, $m, PREG_OFFSET_CAPTURE)) {
            $ul = $m[0][0];
            $href = static function (string $class, string $default) use ($ul): string {
                return preg_match('#<li class="' . $class . '[^"]*">\s*<a[^>]*href="([^"]*)"#', $ul, $h) ? $h[1] : $default;
            };
            $title = static function (string $class) use ($ul): string {
                return preg_match('#<li class="' . $class . '[^"]*">\s*<a[^>]*title="([^"]*)"#', $ul, $h) ? ' title="' . $h[1] . '"' : '';
            };
            $icon = static function (string $d): string {
                return '<g class="outline"><path d="' . $d . '"/></g><g class="icon"><path d="' . $d . '"/></g>';
            };
            $paths = [
                'profile' => 'M8 1.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7zM1.5 15c0-3.3 2.9-5.2 6.5-5.2s6.5 1.9 6.5 5.2z',
                'options' => 'M6.8 1h2.4l.4 2 1.4.6 1.7-1.2 1.7 1.7-1.2 1.7.6 1.4 2 .4v2.4l-2 .4-.6 1.4 1.2 1.7-1.7 1.7-1.7-1.2-1.4.6-.4 2H6.8l-.4-2-1.4-.6-1.7 1.2-1.7-1.7 1.2-1.7-.6-1.4-2-.4V6.8l2-.4.6-1.4-1.2-1.7 1.7-1.7 1.7 1.2 1.4-.6zM8 5.6a2.4 2.4 0 1 0 0 4.8 2.4 2.4 0 0 0 0-4.8z',
                'help' => 'M4.6 5.4C4.6 3.3 6.1 2 8.1 2s3.4 1.2 3.4 3c0 1.5-.9 2.2-1.9 2.9-.8.5-1 .9-1 1.8v.6H6.7v-.8c0-1.4.5-2.1 1.5-2.8.8-.5 1.1-.9 1.1-1.5 0-.7-.5-1.2-1.3-1.2-.9 0-1.4.6-1.4 1.4zM6.6 11.6h2.2V14H6.6z',
                'logout' => 'M3.2 1.5 8 6.3l4.8-4.8 1.7 1.7L9.7 8l4.8 4.8-1.7 1.7L8 9.7l-4.8 4.8-1.7-1.7L6.3 8 1.5 3.2z',
            ];
            $defaults = ['profile' => 'spieler.php', 'options' => 'options.php', 'help' => 'manual.php', 'logout' => 'logout.php'];
            $nav = '<nav id="outOfGame" class="LTR">';
            foreach ($paths as $name => $d) {
                $nav .= '<a class="layoutButton buttonFramed withIcon round ' . $name . '" href="' . $href($name, $defaults[$name]) . '"' . $title($name) . '>'
                    . '<svg class="' . $name . '" viewBox="0 0 16 16">' . $icon($d) . '</svg></a>';
            }
            $nav .= '</nav>';
            $html = substr($html, 0, $m[0][1]) . $nav . substr($html, $m[0][1] + strlen($ul));
        }
        // server time: a box under the logo (Legends), at the top of #center
        if (preg_match('#<div id="servertime" class="stime">\s*(.*?)\s*(<span class="timer"[^>]*>[^<]*</span>)\s*</div>#s', $html, $m, PREG_OFFSET_CAPTURE)) {
            $label = trim(str_replace('&nbsp;', ' ', strip_tags($m[1][0])), " :\t\n");
            $box = '<div id="servertime" class="stime"><span>' . $label . ':</span><span>' . $m[2][0] . '&nbsp;(UTC&nbsp;' . date('P') . ')</span></div>';
            $html = substr($html, 0, $m[0][1]) . substr($html, $m[0][1] + strlen($m[0][0]));
            $html = preg_replace('#(<div id="center"[^>]*>)#', '$1' . $box, $html, 1);
        }
    }

    /** Tabs (contentNavi) in the Legends markup: div.container.active|normal around each tab. */
    /**
     * Title bar buttons of the window like the main branch: the light bulb (contextual help tour of the page, steps set
     * by the Legends views through LegendsUi::helpStep) and the "favourite tab" button next to the title of pages with
     * tabs. The favourite tab is kept in a cookie (ajax.php?cmd=tabFavorite) and opened when the page is opened plainly.
     */
    private static function titleButtons(string &$html): void
    {
        if (LegendsUi::$help && strpos($html, 'id="contextualHelpButton"') === false
            && preg_match('#<div class="contentTitle">(.*?)</div>#s', $html, $m, PREG_OFFSET_CAPTURE)) {
            $pos = $m[1][1] + strlen($m[1][0]);
            $html = substr($html, 0, $pos) . LegendsUi::helpButton() . substr($html, $pos);
            $html = preg_replace('#</body>#i', LegendsUi::helpScript() . '</body>', $html, 1);
        }
        $content = strpos($html, '<div id="content"');
        if ($content === false || strpos($html, 'id="tabFavorButton"') !== false) return;
        $from = strpos($html, '<div class="contentNavi subNavi', $content);
        if ($from === false) return;
        $end = self::blockEnd(substr($html, $from), '<div class="contentNavi subNavi');
        if ($end === null) return;
        $end += $from + 6;
        $block = substr($html, $from, $end - $from);
        if (!preg_match('#<div class="container active">.*?class="content[^"]*\bfavorKey([0-9]+)\b#s', $block, $a)) return;
        $script = basename($_SERVER['SCRIPT_NAME'] ?? 'page', '.php');
        $name = preg_replace('/[^a-zA-Z0-9_]/', '', $script . (preg_match('#<div id="build" class="gid(\d+)#', $html, $g) ? '_g' . $g[1] : ''));
        $fav = isset($_COOKIE['t46fav_' . $name]) ? preg_replace('/[^0-9]/', '', (string)$_COOKIE['t46fav_' . $name]) : '';
        if ($fav !== '' && strpos($block, 'favorKey' . $fav . '"') === false && !preg_match('#favorKey' . $fav . '[ "]#', $block)) $fav = '';
        if ($fav !== '') {
            $block = str_replace(' favorActive', '', $block);
            $block = preg_replace('#(class="content[^"]*\bfavorKey' . $fav . ')([ "])#', '$1 favorActive$2', $block, 1);
            // opened without a tab: go to the favourite tab
            $query = $_GET;
            unset($query['id']);
            if ($fav !== $a[1] && !$query && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'
                && preg_match('#class="content[^"]*\bfavorKey' . $fav . '\b[^"]*">\s*<a\b[^>]*href="([^"]+)"#', $block, $h)) {
                $html = preg_replace('#<head>#i', '<head><script type="text/javascript">location.replace(' . json_encode(html_entity_decode($h[1])) . ');</script>', $html, 1);
            }
        }
        $html = substr($html, 0, $from) . $block . substr($html, $end);
        // the button sits next to the title (in #content, positioned into the title bar by the Legends CSS)
        $button = LegendsUi::favorButton($name, $a[1]);
        if (preg_match('#<h1 class="titleInHeader">.*?</h1>#s', $html, $t, PREG_OFFSET_CAPTURE, $content)) {
            $pos = $t[0][1] + strlen($t[0][0]);
            $html = substr($html, 0, $pos) . $button . substr($html, $pos);
        } else {
            $html = substr($html, 0, $from) . $button . substr($html, $from);
        }
    }

    private static function tabs(string &$html): void
    {
        $offset = 0;
        while (($from = strpos($html, '<div class="contentNavi subNavi', $offset)) !== false) {
            $end = self::blockEnd(substr($html, $from), '<div class="contentNavi subNavi');
            if ($end === null) break;
            $end += $from + 6;   // after "</div>"
            $block = substr($html, $from, $end - $from);
            if (strpos($block, 'class="container') === false) {
                $block = preg_replace('#<button type="button" class="scroll(?:From|To)"[^>]*></button>#', '', $block);
                if (strpos($block, '<div class="scrollingContainer">') !== false) {
                    self::unwrap($block, '<div class="scrollingContainer">');
                }
                $block = preg_replace_callback('#<div(?: title="[^"]*")? class="content((?: [a-zA-Z]+)*? favor[^"]*)">\s*(<a\b[^>]*class="tabItem\s*(active|normal)?[^"]*"[^>]*>.*?</a>)\s*</div>#s', function ($t) {
                    $state = ($t[3] ?? '') === 'active' ? 'active' : 'normal';
                    return '<div class="container ' . $state . '"><div class="background-start">&nbsp;</div><div class="background-end">&nbsp;</div>'
                        . '<div class="content' . $t[1] . '">' . $t[2] . '</div></div>';
                }, $block);
                $html = substr($html, 0, $from) . $block . substr($html, $end);
            }
            $offset = $from + strlen($block);
        }
    }

    /**
     * Building page header like the main branch: building picture with the level values (upgradeHeader) and a
     * collapsible description, instead of the T4.6 text + small picture.
     */
    private static function buildPage(string &$html, string $tribe): void
    {
        if (strpos($html, '<div id="descriptionAndInfo">') === false || !preg_match('#<div id="build" class="gid(\d+)#', $html, $g)) return;
        $from = strpos($html, '<div id="descriptionAndInfo">');
        $end = self::blockEnd(substr($html, $from), '<div id="descriptionAndInfo">');
        if ($end === null) return;
        $end = $from + $end + 6;
        $info = substr($html, $from, $end - $from);
        $desc = preg_match('#<div class="build_desc">(.*?)</div>#s', $info, $d) ? trim($d[1]) : '';
        $table = preg_match('#<table[^>]*id="build_value".*?</table>#s', $info, $tb) ? $tb[0] : '';
        $label = defined('UI_DESCRIPTION') ? UI_DESCRIPTION : 'Description';
        $new = '<div class="upgradeHeader">' . $table . '<div class="buildingIllustration g' . (int)$g[1] . ' ' . $tribe . '"></div></div>'
            . ($desc !== '' ? '<div class="buildingDescription"><div class="headline"><button type="button" class="openedClosedSwitch switchOpened"'
                . ' onclick="this.classList.toggle(\'switchOpened\');this.classList.toggle(\'switchClosed\');var d=this.parentNode.nextElementSibling;d.style.display=d.style.display===\'none\'?\'\':\'none\';"></button>'
                . '<h4>' . $label . '</h4></div><div class="description">' . $desc . '</div></div>' : '');
        $html = substr($html, 0, $from) . $new . substr($html, $end);
        // the contract box: no small picture and headline (the picture is in the header now)
        $html = preg_replace('#<div class="stickyImage">.*?</div>\s*<h4>[^<]*</h4>#s', '', $html, 1);
        $html = preg_replace('#<div class="roundedCornersBox big ">(\s*<div id="contractSpacer"></div>)?#', '<div class="upgradeBuilding">', $html, 1);
        $html = str_replace('class="culturePointsAndPopulation ', 'class="buildingBenefits ', $html);
    }

    /** Sidebar box buttons like the main branch (LegendsUi::framedSidebarButtons): building buttons gold with Legends icons. */
    private static function sidebars(string &$html): void
    {
        $icons = ['workshop' => 'sbWorkshop', 'stable' => 'sbStable', 'barracks' => 'sbBarracks', 'market' => 'sbMarket'];
        $start = strpos($html, 'id="sidebarBoxActiveVillage"');
        if ($start === false) return;
        $end = strpos($html, 'class="content"', $start);
        if ($end === false) return;
        $part = substr($html, $start, $end - $start);
        $part = preg_replace_callback('#<a class="layoutButton buttonFramed withIcon round (workshop|stable|barracks|market) green([^"]*)"([^>]*)>\s*<svg.*?</svg>#s', function ($m) use ($icons) {
            return '<a class="layoutButton buttonFramed withIcon round ' . $m[1] . ' gold' . $m[2] . '"' . $m[3] . '>' . LegendsUi::svg($icons[$m[1]]);
        }, $part);
        $html = substr($html, 0, $start) . $part . substr($html, $end);
    }

    /** Village view: the Legends stylesheet places the building slots inside #villageContent.<tribe>. */
    private static function villageView(string &$html, string $tribe): void
    {
        $html = preg_replace('#<div id="village_map" class="[^"]*"#', '<div id="villageContent" class="' . $tribe . '"', $html, 1);
        $html = str_replace('#village_map ', '#villageContent ', $html);   // tutorial highlights
    }

    /** Removes the start and end tag of the element starting with $start, keeping its children. */
    private static function unwrap(string &$html, string $start): void
    {
        $from = strpos($html, $start);
        $end = self::blockEnd($html, $start);
        if ($from === false || $end === null) return;
        $close = strpos($html, '>', $end) + 1;
        $open = strpos($html, '>', $from) + 1;
        $html = substr($html, 0, $from) . substr($html, $open, $end - $open) . substr($html, $close);
    }

    /** Removes the element starting with $start from $html and returns it. */
    private static function cutBlock(string &$html, string $start): ?string
    {
        $from = strpos($html, $start);
        $end = self::blockEnd($html, $start);
        if ($from === false || $end === null) return null;
        $end = strpos($html, '>', $end) + 1;
        $block = substr($html, $from, $end - $from);
        $html = substr($html, 0, $from) . substr($html, $end);
        return $block;
    }

    private static function tribeName(): string
    {
        global $session;
        $t = isset($session) && is_object($session) ? (int)$session->tribe : 1;
        return [1 => 'roman', 2 => 'teuton', 3 => 'gaul', 6 => 'egyptian', 7 => 'hun'][$t] ?? 'roman';
    }
}
