<?php

namespace Controller;

use Core\Config;
use Core\Database\DB;
use Core\Helper\PageNavigator;
use Core\Helper\TimezoneHelper;
use Core\Session;
use Game\Formulas;
use function getDisplay;
use function isServerFinished;
use Model\AutomationModel;
use Model\BerichteModel;
use Model\Quest;
use Model\StatisticsGeneralModel;
use Model\StatisticsModel;
use Model\SummaryModel;
use resources\View\GameView;
use resources\View\PHPBatchView;

class StatistikenCtrl extends GameCtrl
{
    private $pageSize = 20;
    private $content;
    private $showFakeUsers = true;

    public function __construct()
    {
        parent::__construct();
        Quest::getInstance()->setQuestBitwise('world', 1, \Model\Quest::QUEST_FINISHED);
        $this->view = new GameView();
        $this->view->vars['titleInHeader'] = T("Statistics", "titleInHeader");
        $this->view->vars['bodyCssClass'] = 'perspectiveResources';
        $this->view->vars['contentCssClass'] = 'statistics';
        $selectedTabIndex = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : Session::getInstance()->getFavoriteTab('statistics');
        $selectedSubIndex = isset($_REQUEST['idSub']) ? (int)$_REQUEST['idSub'] : Session::getInstance()->getFavoriteTab($selectedTabIndex == 0 ? 'statisticsTablePlayer' : 'statisticsTableAlly');
        if ($selectedTabIndex < 0 || $selectedTabIndex > 8) {
            $selectedTabIndex = 0;
        }
        if ($selectedTabIndex < 2) {
            $selectedSubIndex = $selectedSubIndex >= 0 && $selectedSubIndex < 4 ? $selectedSubIndex : 0;
        } else {
            $selectedSubIndex = 0;
        }
        $this->content = new PHPBatchView("statistics/main");
        $this->content->vars = [
            "selectedTabIndex" => $selectedTabIndex,
            "selectedSubIndex" => $selectedSubIndex,
            "playersTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedTabIndex == 0,
                "text" => T("Statistics", "tabs.players"),
            ],
            "alliancesTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedTabIndex == 1,
                "text" => T("Statistics", "tabs.alliances"),
            ],
            "villagesTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedTabIndex == 2,
                "text" => T("Statistics", "tabs.villages"),
            ],
            "heroTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedTabIndex == 3,
                "text" => T("Statistics", "tabs.hero"),
            ],
            "plusTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedTabIndex == 6,
                "text" => T("Statistics", "tabs.plus"),
            ],
            "farmTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedTabIndex == 8,
                "text" => T("Statistics", "tabs.farm"),
            ],
            "GeneralTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedTabIndex == 5,
                "text" => T("Statistics", "tabs.General"),
            ],
            "BonusTabProperties" => [
                "active" => $selectedTabIndex == 7,
                "text" => T("Statistics", "tabs.Bonus"),
            ],
            "WonderTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedTabIndex == 6,
                "text" => T("Statistics", "tabs.WonderOfTheWorld"),
            ],
            "overviewSubTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedSubIndex == 0,
                "text" => T("Statistics", "subTabs.overview"),
            ],
            "attackerSubTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedSubIndex == 1,
                "text" => T("Statistics", "subTabs.attacker"),
            ],
            "defenderSubTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedSubIndex == 2,
                "text" => T("Statistics", "subTabs.defender"),
            ],
            "top10SubTabProperties" => [
                "id" => get_button_id(),
                "active" => $selectedSubIndex == 3,
                "text" => T("Statistics", "subTabs.Top 10"),

            ],
            "error" => '',
            "other" => '',
        ];
        $listTabs = [
            0 => 'players',
            1 => 'alliances',
            2 => 'villages',
            3 => 'hero',
            6 => 'plus',
            8 => 'farm',
            5 => 'General',
            7 => 'Bonus'
        ];
        $listSubs = ['overview', 'attacker', 'defender', 'Top 10'];
        $this->content->vars['favorText'] = sprintf(T("Global", "Select tab %s as favourite"),
            T("Statistics", "tabs." . $listTabs[$selectedTabIndex]));
        $this->content->vars['favorText2'] = sprintf(T("Global", "Select tab %s as favourite"),
            T("Statistics", "subTabs." . $listSubs[$selectedSubIndex]));
        if (Session::getInstance()->isAdmin()) {
            if (isset($_GET['showFakeUsers'])) {
                Session::setCookie('showFakeUsers', $_GET['showFakeUsers'] == 1 ? 1 : 0, 7 * 86400);
            }
            $this->showFakeUsers = Session::getCookie('showFakeUsers', 0);
        }
        $this->content->vars['other'] = $this->handle($selectedTabIndex, $selectedSubIndex);
        $this->view->vars['content'] .= $this->content->output();
    }

    public function handle($selectedTabIndex, $selectedSubIndex)
    {
        switch ($selectedTabIndex) {
            case 0:
                switch ($selectedSubIndex) {
                    case 0:
                        return $this->processPlayersOverview($selectedTabIndex, $selectedSubIndex);
                        break;
                    case 1:
                    case 2:
                        return $this->processPlayerAttackerOrDefender($selectedTabIndex,
                            $selectedSubIndex,
                            $selectedSubIndex == 2);
                        break;
                    case 3:
                        return $this->processTOP10($selectedTabIndex == 1);
                        break;
                }
                break; //players
            case 1:
                switch ($selectedSubIndex) {
                    case 0:
                        return $this->processAlliancesOverview($selectedTabIndex, $selectedSubIndex);
                        break;
                    case 1:
                    case 2:
                        return $this->processAllianceAttackerOrDefender($selectedTabIndex,
                            $selectedSubIndex,
                            $selectedSubIndex == 2);
                        break;
                    case 3:
                        return $this->processTOP10($selectedTabIndex == 1);
                        break;
                }
                break; //alliances
            case 2:
                return $this->processVillagesOverview($selectedTabIndex, $selectedSubIndex);
                break;
            case 3:
                return $this->processHerosOverview($selectedTabIndex, $selectedSubIndex);
                break;
            case 4:
                return PHPBatchView::render('statistics/plus',
                    [
                        'uid' => Session::getInstance()->getPlayerId(),
                        'chartHashId' => Session::getInstance()->getSecureSessionKey(),
                    ]);
                break;
            case 5:
                if (!is_classic_gpack() && !is_t40_layout()) {
                    // Travian Legends "General" page: world progress, tribes and the player's ranks with day-by-day charts
                    $this->view->vars['contentCssClass'] = 'statistics statisticsGeneral';
                    $model = new StatisticsGeneralModel((int)Session::getInstance()->getPlayerId());
                    return PHPBatchView::render('statistics/generalLegends', ['data' => $model->build()]);
                }
                $statistics = new StatisticsModel();
                return PHPBatchView::render('statistics/general',
                    [
                        "tribes" => $statistics->getTribesStatus(),
                        "players" => $statistics->getPlayersStatus(),
                        "world_misc" => $statistics->getWorldMisc(),
                        "casualtiesData" => $statistics->getWorldMisc(true),
                        "country_ranks" => $statistics->getCountryRanks(),
                        'chartHashId' => Session::getInstance()->getSecureSessionKey(),
                        'playerId' => Session::getInstance()->getPlayerId(),
                        'hasPlus' => Session::getInstance()->hasPlus(),
                    ]);
            case 6:
                $statistics = new StatisticsModel();
                return PHPBatchView::render('statistics/WonderOfTheWorld',
                    ["wonders" => $statistics->getWonderVillages()]);
                break;
            case 7:
                if (getDisplay("showBonusTabInStatistics")) {
                    return PHPBatchView::render('statistics/bonus');
                }
                break;
            case 8: //farms
                if (getDisplay("showFarmsInStatistics")) {
                    return $this->showFarms();
                }
                break;
        }
    }

    private function processPlayersOverview($selectedTabIndex, $selectedSubIndex)
    {
        $statistics = new StatisticsModel();
        $selectedRank = 0;
        if (isset($_REQUEST['name']) && trim($_REQUEST['name']) != "") {
            $selectedRank = $statistics->getPlayerRankByName(filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            if (!$selectedRank) {
                $this->content->vars['error'] = sprintf(T("Statistics", "errors.userNotFound"),
                    filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            }
        } else if (isset($_REQUEST['rank']) && is_numeric($_REQUEST['rank'])) {
            $selectedRank = (int)$_REQUEST['rank'];
            if ($selectedRank < 1) {
                $selectedRank = 1;
            }
        } else if (!isset($_GET['page'])) {
            $selectedRank = $statistics->getPlayerRankById(Session::getInstance()->getPlayerId());
        }
        $pageIndex = !$selectedRank ? (isset($_GET['page']) ? (int)$_GET['page'] - 1 : 0) : floor(($selectedRank - 1) / $this->pageSize);
        $prefix = [];
        $prefix['id'] = $selectedTabIndex;
        $prefix['idSub'] = $selectedSubIndex;
        if ($pageIndex < 0) {
            $pageIndex = 0;
        }
        $total = $statistics->getPlayerListCount();
        $tot_pages = ceil($total / $this->pageSize);
        if ($pageIndex + 1 > $tot_pages) {
            $pageIndex = 0;
        }
        $nav = new PageNavigator($pageIndex + 1,
            $statistics->getPlayerListCount(),
            $this->pageSize,
            $prefix,
            "statistiken.php");
        $rows = $statistics->getPlayerList($pageIndex, $this->pageSize);
        $tableBody = '';
        $rowIndex = 0;
        $rows = is_array($rows) ? $rows : iterator_to_array($rows);
        $tribes = $this->playersTribeAlliance(array_column($rows, 'id'));
        foreach ($rows as $row) {
            $rank = ++$rowIndex + $pageIndex * $this->pageSize;
            if (!$this->showFakeUsers && $row['access'] == 3) {
                continue;
            }
            $alliance = '-';
            if ($row['aid']) {
                $alliance = '<a href="allianz.php?aid=' . $row['aid'] . '">' . $row['alliance_name'] . '</a>';
            }
            $class = $rank == $selectedRank ? 'hl' : 'hover';
            $admin = null;
            $flag = null;
            if (Session::getInstance()->isAdmin()) {
                $admin = '<td class="buttons" style="padding: 2px 0 2px 2px;white-space: nowrap;width: 1%;"><a href="admin.php?action=editPlayer&uid=' . $row['id'] . '"><button type="button" class="icon"><img style="' . (getDirection() == 'LTR' ? 'left' : 'right') . ': 6px;" src="' . get_gpack_cdn_mainPage_url() . 'img_ltr/f/edit.gif"></button></a></td>';
            }
            if (Session::getInstance()->isAdmin() || getDisplay("showFlagsInStatistics")) {
                $flag = '<td class="flags " >';
                if ($row['access'] <> 3) {
                    if (!empty($row['countryFlag']) && $row['countryFlag'] != "-" && $row['id'] > 2) {
                        $flag .= '<img src="img/x.gif" class="flags flag_' . $row['countryFlag'] . '" title="' . $row['countryFlag'] . '" alt="' . $row['countryFlag'] . '">';
                    } else if ($row['countryFlag'] == "-" || empty($row['countryFlag'])) {
                        $flag .= '?';
                    } else if ($row['id'] <= 2) {
                        $flag .= '!';
                    }
                } else {
                    $flag .= '!';
                }
                $flag .= '</td>';
            }
            if (Session::getInstance()->isAdmin() && $row['access'] <> 3) {
                if ($row['access'] == 0 && $row['id'] > 2) {
                    $row['name'] = '<span style="color:red;">' . $row['name'] . '</span>';
                } else if ($row['access'] == 0 && $row['id'] == 2) {
                    $row['name'] = '<span style="color:blue;">' . $row['name'] . '</span>';
                }
                $login_stat = AutomationModel::getOnlineStatusAsImg($row['last_login_time']);
                $row['name'] = $login_stat . ' ' . $row['name'];
            }
            $tribe = self::tribeCell($tribes[(int)$row['id']] ?? []);
            $tableBody .= <<<HTML
<tr class="{$class}">
			<td class="ra " >{$rank}.</td>
			{$tribe}
			<td class="pla " ><a href="spieler.php?uid={$row['id']}">{$row['name']}</a> </td>
			<td class="al " >$alliance</td>
			{$flag}
			<td class="pop " >{$row['total_population']}</td>
			<td class="vil " >{$row['total_villages_count']}</td>
			{$admin}
		</tr>
HTML;
        }
        return PHPBatchView::render('statistics/table',
            [
                "tableId" => "player",
                "tableColumns" => '<tr>
			<td></td>
			<td class="tribe"></td>
			<td>' . T("Statistics", "tabs.players") . '</td>
			<td>' . T("Statistics", "alliance") . '</td>
			' . (Session::getInstance()->isAdmin() || getDisplay("showFlagsInStatistics") ? '<td class="flags">' . T("Statistics",
                            "General.CountryFlag") . '</td>' : '') . '
			<td>' . T("Statistics", "population") . '</td>
			<td>' . T("Statistics", "village") . '</td>
			' . (Session::getInstance()->isAdmin() ? '<td class="buttons" style="padding: 2px 0 2px 2px;white-space: nowrap;width: 1%;">' . T("Statistics",
                            "Actions") . '</td>' : '') . '
		</tr>',
                "headerRound" => T("Statistics", "largestPlayers"),
                "http_query" => !sizeof($prefix) ? '' : "?" . http_build_query($prefix),
                "tableBody" => $tableBody,
                "Navigator" => $nav->get(),
                "selectedRank" => isset($_REQUEST['rank']) ? $_REQUEST['rank'] : $selectedRank,
                "selectedName" => isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
            ]);
    }

    /** Tribe and alliance (id, tag) of the given players, for the tribe icon and alliance columns of the Legends tables. */
    private function playersTribeAlliance(array $uids): array
    {
        $uids = array_filter(array_map('intval', $uids));
        if (!$uids) {
            return [];
        }
        $result = \Core\Database\DB::getInstance()->query('SELECT u.id, u.race, u.aid, a.tag FROM users u LEFT JOIN alidata a ON a.id=u.aid WHERE u.id IN(' . implode(',', $uids) . ')');
        $map = [];
        while ($row = $result->fetch_assoc()) {
            $map[(int)$row['id']] = $row;
        }
        return $map;
    }

    private static function tribeCell(array $info): string
    {
        return '<td class="tribe "><i class="tribe' . (int)($info['race'] ?? 0) . '_medium" title="' . htmlspecialchars(T('Global', 'races.' . (int)($info['race'] ?? 0))) . '"></i></td>';
    }

    /** players */
    private function processPlayerAttackerOrDefender($selectedTabIndex, $selectedSubIndex, $isDefender)
    {
        $statistics = new StatisticsModel();
        $selectedRank = 0;
        if (isset($_REQUEST['name']) && trim($_REQUEST['name']) != "") {
            $selectedRank = $statistics->getPlayersPointsByName(filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING),
                $isDefender);
            if (!$selectedRank) {
                $this->content->vars['error'] = sprintf(T("Statistics", "errors.userNotFound"),
                    filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            }
        } else if (isset($_REQUEST['rank']) && is_numeric($_REQUEST['rank'])) {
            $selectedRank = (int)$_REQUEST['rank'];
            if ($selectedRank < 1) {
                $selectedRank = 1;
            }
        } else if (!isset($_GET['page'])) {
            $selectedRank = $statistics->getPlayersPointsById(Session::getInstance()->get("id"), $isDefender);
        }
        $pageIndex = !$selectedRank ? (isset($_GET['page']) ? (int)$_GET['page'] - 1 : 0) : floor(($selectedRank - 1) / $this->pageSize);
        $prefix = [];
        $prefix['id'] = $selectedTabIndex;
        $prefix['idSub'] = $selectedSubIndex;
        if ($pageIndex < 0) {
            $pageIndex = 0;
        }
        $total = $statistics->getPlayersPointsListCount();
        $tot_pages = ceil($total / $this->pageSize);
        if ($pageIndex + 1 > $tot_pages) {
            $pageIndex = 0;
        }
        $nav = new PageNavigator($pageIndex + 1, $total, $this->pageSize, $prefix, "statistiken.php");
        $rows = $statistics->getPlayersPointsList($pageIndex, $this->pageSize, $isDefender);
        $tableBody = '';
        $rowIndex = 0;
        foreach ($rows as $row) {
            $rank = ++$rowIndex + $pageIndex * $this->pageSize;
            $class = $rank == $selectedRank ? 'hl' : 'hover';
            $tableBody .= <<<HTML
<tr class="{$class}">

			<td class="ra " >{$rank}.</td>
			<td class="pla " ><a href="spieler.php?uid={$row['id']}">{$row['name']}</a> </td>
			<td class="pop " >{$row['total_population']}</td>
			<td class="vil " >{$row['total_villages_count']}</td>
			<td class="po " >{$row['points']}</td>
		</tr>
HTML;
        }

        return PHPBatchView::render("statistics/table",
            [
                "tableId" => "player",
                "tableColumns" => '<tr>
			<td></td>
			<td>' . T("Statistics", "tabs.players") . '</td>
			<td>' . T("Statistics", "population") . '</td>
			<td>' . T("Statistics", "village") . '</td>
			<td>' . T("Statistics", "points") . '</td>
		</tr>',
                "headerRound" => T("Statistics", "largestPlayers") . ' (' . T("Statistics",
                        "subTabs." . ($isDefender ? "defender" : "attacker")) . ')',
                "http_query" => !sizeof($prefix) ? '' : "?" . http_build_query($prefix),
                "tableBody" => $tableBody,
                "Navigator" => $nav->get(),
                "selectedRank" => isset($_REQUEST['rank']) ? $_REQUEST['rank'] : $selectedRank,
                "selectedName" => isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
            ]);
    }

    /** top 10 both alliances and players */
    private function processTOP10($isAlliance)
    {
        $session = Session::getInstance();
        $statistics = new StatisticsModel();
        $find = $isAlliance ? $session->getAllianceId() : Session::getInstance()->getPlayerId();
        $top10_off_top = $top10_def_top = null;
        $reportModel = new BerichteModel();
        if (!$isAlliance) {
            {
                $show_report = isServerFinished() || $session->isAdmin();
                //start
                $off_point_top = $statistics->getTop10(!$isAlliance, "max_off_point");
                $top10_off_top_own = null;
                $rowIndex = 0;
                foreach ($off_point_top as $row) {
                    ++$rowIndex;
                    $classR = $isAlliance ? "al " : "pla ";
                    $link = $isAlliance ? "allianz.php?aid=" . $row['id'] : "spieler.php?uid=" . $row['id'];
                    $class = $row['id'] == $find ? 'hl' : 'hover';
                    $name = $isAlliance ? $row['tag'] : $row['name'];
                    $report = null;
                    if ($show_report || $row['id'] == $find) {
                        $reportLink = $reportModel->getReportLinkWithKey($row['max_off_nid']);
                        if ($reportLink) {
                            $report = '<a target="_blank" href="' . $reportLink . '"><img src="img/x.gif" class="iReport iReport0" alt="iReport iReport0"></a>&nbsp;&nbsp;';
                        }
                    }
                    if ($row['id'] == $find) {
                        $top10_off_top_own = $row;
                        $top10_off_top_own['rank'] = $rowIndex;
                        $top10_off_top_own['name'] = $name;
                        $top10_off_top_own['link'] = $link;
                        $top10_off_top_own['report'] = $report;
                    }
                    $points = TimezoneHelper::autoDateString($row['max_off_time'], TRUE);
                    $top10_off_top .= <<<HTML
<tr  class="$class">
            <td class="ra fc">{$rowIndex}.&nbsp;</td>
            <td class="{$classR}">{$report}<a href="$link">$name</a>
            </td>
            <td class="val lc">$points</td>
        </tr>
HTML;
                }
                $top10_off_top .= '<tr><td colspan="3" class="empty"></td></tr>';
                if (!isset($top10_off_top_own)) {
                    $top10_off_top_own = [
                        "rank" => "?",
                        "name" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT tag FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getName(),
                        "link" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : "allianz.php?aid=" . $session->getAllianceId()) : "spieler.php?uid=" . $session->getPlayerId(),
                        "points" => $session->get("max_off_point"),
                        'report' => null,
                    ];
                    //
                    $top10_off_top_own['points'] = '-';
                } else {
                    $top10_off_top_own['points'] = TimezoneHelper::autoDateString($top10_off_top_own['max_off_time'],
                        TRUE);
                }
                $top10_off_top .= <<<HTML
<tr class="own hl">
            <td class="ra fc">{$top10_off_top_own['rank']}.&nbsp;</td>
            <td class="pla">{$top10_off_top_own['report']}<a href="{$top10_off_top_own['link']}">{$top10_off_top_own['name']}</a></td>
            <td class="val lc">{$top10_off_top_own['points']}</td>
        </tr>
HTML;
                //finish
            }
            {
                $off_point_top = $statistics->getTop10(!$isAlliance, "max_def_point");
                $top10_off_top_own = null;
                $rowIndex = 0;
                foreach ($off_point_top as $row) {
                    ++$rowIndex;
                    $classR = $isAlliance ? "al " : "pla ";
                    $link = $isAlliance ? "allianz.php?aid=" . $row['id'] : "spieler.php?uid=" . $row['id'];
                    $class = $row['id'] == $find ? 'hl' : 'hover';
                    $name = $isAlliance ? $row['tag'] : $row['name'];
                    $report = null;
                    if ($show_report || $row['id'] == $find) {
                        $reportLink = $reportModel->getReportLinkWithKey($row['max_def_nid']);
                        if ($reportLink) {
                            $report = '<a target="_blank" href="' . $reportLink . '"><img src="img/x.gif" class="iReport iReport0" alt="iReport iReport0"></a>&nbsp;&nbsp;';
                        }
                    }
                    if ($row['id'] == $find) {
                        $top10_off_top_own = $row;
                        $top10_off_top_own['rank'] = $rowIndex;
                        $top10_off_top_own['name'] = $name;
                        $top10_off_top_own['link'] = $link;
                        $top10_off_top_own['report'] = $report;
                    }
                    $points = TimezoneHelper::autoDateString($row['max_def_time'], TRUE);
                    $top10_def_top .= <<<HTML
<tr  class="$class">
            <td class="ra fc">{$rowIndex}.&nbsp;</td>
            <td class="{$classR}">{$report}<a href="$link">$name</a>
            </td>
            <td class="val lc">{$points}</td>
        </tr>
HTML;
                }
                $top10_def_top .= '<tr><td colspan="3" class="empty"></td></tr>';
                if (!isset($top10_off_top_own)) {
                    $top10_off_top_own = [
                        "rank" => "?",
                        "name" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT tag FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getName(),
                        "link" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : "allianz.php?aid=" . $session->getAllianceId()) : "spieler.php?uid=" . $session->getPlayerId(),
                        "points" => $session->get("max_def_time"),
                        'report' => null,
                    ];
                    $top10_off_top_own['points'] = '-';
                } else {
                    $top10_off_top_own['points'] = TimezoneHelper::autoDateString($top10_off_top_own['max_def_time'],
                        TRUE);
                }
                $top10_def_top .= <<<HTML
<tr class="own hl">
            <td class="ra fc">{$top10_off_top_own['rank']}.&nbsp;</td>
            <td class="pla">{$top10_off_top_own['report']}<a href="{$top10_off_top_own['link']}">{$top10_off_top_own['name']}</a></td>
            <td class="val lc">{$top10_off_top_own['points']}</td>
        </tr>
HTML;
            }
        }
        $offs = $statistics->getTop10(!$isAlliance, "week_attack_points");
        $top10_offs = '';
        $rowIndex = 0;
        foreach ($offs as $row) {
            ++$rowIndex;
            $classR = $isAlliance ? "al " : "pla ";
            $link = $isAlliance ? "allianz.php?aid=" . $row['id'] : "spieler.php?uid=" . $row['id'];
            $class = $row['id'] == $find ? 'hl' : 'hover';
            $name = $isAlliance ? $row['tag'] : $row['name'];
            if ($row['id'] == $find) {
                $top10_off_own = $row;
                $top10_off_own['rank'] = $rowIndex;
                $top10_off_own['name'] = $name;
                $top10_off_own['link'] = $link;
            }
            $row['points'] = $this->number_format($row['points']);
            $top10_offs .= <<<HTML
<tr  class="$class">
            <td class="ra fc">{$rowIndex}.&nbsp;</td>
            <td class="{$classR}"><a href="$link">$name</a>
            </td>
            <td class="val lc">{$row['points']}</td>
        </tr>
HTML;
        }
        $top10_offs .= '<tr>
            <td colspan="3" class="empty"></td>
        </tr>';
        if (!isset($top10_off_own)) {
            $top10_off_own = [
                "rank" => "?",
                "name" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT tag FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getName(),
                "link" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : "allianz.php?aid=" . $session->getAllianceId()) : "spieler.php?uid=" . $session->getPlayerId(),
                "points" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT week_attack_points FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getWeekAttackPoints(),
            ];
        }
        $top10_off_own['points'] = $this->number_format($top10_off_own['points']);

        $top10_offs .= <<<HTML
<tr class="own hl">
            <td class="ra fc">{$top10_off_own['rank']}.&nbsp;</td>
            <td class="pla"><a href="{$top10_off_own['link']}">{$top10_off_own['name']}</a></td>
            <td class="val lc">{$top10_off_own['points']}</td>
        </tr>
HTML;
        {
            $defs = $statistics->getTop10(!$isAlliance, "week_defense_points");
            $top10_deffs = '';
            $rowIndex = 0;
            foreach ($defs as $row) {
                ++$rowIndex;
                $classR = $isAlliance ? "al " : "pla ";
                $link = $isAlliance ? "allianz.php?aid=" . $row['id'] : "spieler.php?uid=" . $row['id'];
                $class = $row['id'] == $find ? 'hl' : 'hover';
                $name = $isAlliance ? $row['tag'] : $row['name'];
                if ($row['id'] == $find) {
                    $top10_def_own = $row;
                    $top10_def_own['rank'] = $rowIndex;
                    $top10_def_own['name'] = $name;
                    $top10_def_own['link'] = $link;
                }
                $row['points'] = $this->number_format($row['points']);
                $top10_deffs .= <<<HTML
<tr  class="$class">
            <td class="ra fc">{$rowIndex}.&nbsp;</td>
            <td class="{$classR}"><a href="$link">$name</a>
            </td>
            <td class="val lc">{$row['points']}</td>
        </tr>
HTML;
            }
            $top10_deffs .= '<tr>
            <td colspan="3" class="empty"></td>
        </tr>';
            if (!isset($top10_def_own)) {
                $top10_def_own = [
                    "rank" => "?",
                    "name" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT tag FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getName(),
                    "link" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : "allianz.php?aid=" . $session->getAllianceId()) : "spieler.php?uid=" . $session->getPlayerId(),
                    "points" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT week_defense_points FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getWeekDefensePoints(),
                ];
            }
            $top10_def_own['points'] = $this->number_format($top10_def_own['points']);
            $top10_deffs .= <<<HTML
<tr class="own hl">
            <td class="ra fc">{$top10_def_own['rank']}.&nbsp;</td>
            <td class="pla"><a href="{$top10_def_own['link']}">{$top10_def_own['name']}</a></td>
            <td class="val lc">{$top10_def_own['points']}</td>
        </tr>
HTML;
        }
        {
            $raiders = $statistics->getTop10(!$isAlliance, "week_robber_points");
            $top10_robbers = '';
            $rowIndex = 0;
            foreach ($raiders as $row) {
                ++$rowIndex;
                $classR = $isAlliance ? "al " : "pla ";
                $link = $isAlliance ? "allianz.php?aid=" . $row['id'] : "spieler.php?uid=" . $row['id'];
                $class = $row['id'] == $find ? 'hl' : 'hover';
                $name = $isAlliance ? $row['tag'] : $row['name'];
                if ($row['id'] == $find) {
                    $top10_robbers_own = $row;
                    $top10_robbers_own['rank'] = $rowIndex;
                    $top10_robbers_own['name'] = $name;
                    $top10_robbers_own['link'] = $link;
                }
                $row['points'] = $this->number_format($row['points']);
                $top10_robbers .= <<<HTML
<tr  class="$class">
            <td class="ra fc">{$rowIndex}.&nbsp;</td>
            <td class="{$classR}"><a href="$link">$name</a>
            </td>
            <td class="val lc">{$row['points']}</td>
        </tr>
HTML;
            }
            $top10_robbers .= '<tr>
            <td colspan="3" class="empty"></td>
        </tr>';
            if (!isset($top10_robbers_own)) {
                $top10_robbers_own = [
                    "rank" => "?",
                    "name" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT tag FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getName(),
                    "link" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : "allianz.php?aid=" . $session->getAllianceId()) : "spieler.php?uid=" . $session->getPlayerId(),
                    "points" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT week_robber_points FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getWeekRobbersPoints(),
                ];
            }
            $top10_robbers_own['points'] = $this->number_format($top10_robbers_own['points']);
            $top10_robbers .= <<<HTML
<tr class="own hl">
            <td class="ra fc">{$top10_robbers_own['rank']}.&nbsp;</td>
            <td class="pla"><a href="{$top10_robbers_own['link']}">{$top10_robbers_own['name']}</a></td>
            <td class="val lc">{$top10_robbers_own['points']}</td>
        </tr>
HTML;
        }
        {

            $climbers = $statistics->getTop10Climbers(!$isAlliance);
            $top10_climbers = '';
            $rowIndex = 0;
            foreach ($climbers as $row) {
                ++$rowIndex;
                $classR = $isAlliance ? "al " : "pla ";
                $link = $isAlliance ? "allianz.php?aid=" . $row['id'] : "spieler.php?uid=" . $row['id'];
                $class = $row['id'] == $find ? 'hl' : 'hover';
                $name = $isAlliance ? $row['tag'] : $row['name'];
                if ($row['id'] == $find) {
                    $top10_climbers_own = $row;
                    $top10_climbers_own['rank'] = $rowIndex;
                    $top10_climbers_own['name'] = $name;
                    $top10_climbers_own['link'] = $link;
                }
                $top10_climbers .= <<<HTML
<tr  class="$class">
            <td class="ra fc">{$rowIndex}.&nbsp;</td>
            <td class="{$classR}"><a href="$link">$name</a>
            </td>
            <td class="val lc">{$row['points']}</td>
        </tr>
HTML;
            }
            $top10_climbers .= '<tr>
            <td colspan="3" class="empty"></td>
        </tr>';
            if (!isset($top10_climbers_own)) {
                $top10_climbers_own = [
                    "rank" => "?",
                    "name" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : DB::getInstance()->fetchScalar("SELECT tag FROM alidata WHERE id=" . $session->getAllianceId())) : $session->getName(),
                    "link" => $isAlliance ? ($session->getAllianceId() <= 0 ? '' : "allianz.php?aid=" . $session->getAllianceId()) : "spieler.php?uid=" . $session->getPlayerId(),
                ];
                if ($isAlliance) {
                    $top10_climbers_own['points'] = $session->getAllianceId() <= 0 ? '' : $statistics->getAllianceClimbersPointById($session->getAllianceId());
                } else if (getCustom("usePopulationAsClimbersRank")) {
                    $top10_climbers_own['points'] = $session->get("total_pop") - $session->getOldRank();
                } else if ($session->get('signupTime') < Config::getProperty('dynamic', 'lastMedalsGiven')) {
                    $top10_climbers_own['points'] = $session->getOldRank() - $statistics->getPlayerRankById($session->getPlayerId());
                } else {
                    $db = DB::getInstance();
                    $totalPlayers = $db->fetchScalar("SELECT COUNT(id) FROM users WHERE users.id > 1");
                    $top10_climbers_own['points'] = $totalPlayers - $statistics->getPlayerRankById($session->getPlayerId());
                }
            }
            $top10_climbers .= <<<HTML
<tr class="own hl">
            <td class="ra fc">{$top10_climbers_own['rank']}.&nbsp;</td>
            <td class="pla"><a href="{$top10_climbers_own['link']}">{$top10_climbers_own['name']}</a></td>
            <td class="val lc">{$top10_climbers_own['points']}</td>
        </tr>
HTML;
        }
        return PHPBatchView::render('statistics/top10',
            [
                "top10_offs" => $top10_offs,
                "top10_deffs" => $top10_deffs,
                "top10_robbers" => $top10_robbers,
                "top10_climbers" => $top10_climbers,
                "isAlliance" => $isAlliance,
                'top10_off_top' => $top10_off_top,
                'top10_def_top' => $top10_def_top,
            ]);
    }

    private function number_format($x, $dec = 0)
    {
        return number_format_x($x, $dec);
    }

    private function processAlliancesOverview($selectedTabIndex, $selectedSubIndex)
    {
        $statistics = new StatisticsModel();
        $selectedRank = 0;
        if (isset($_REQUEST['name']) && trim($_REQUEST['name']) != "") {
            $selectedRank = $statistics->getAllianceRankByName(filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            if (!$selectedRank) {
                $this->content->vars['error'] = sprintf(T("Statistics", "errors.allianceNotFound"),
                    filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            }
        } else if (isset($_REQUEST['rank']) && is_numeric($_REQUEST['rank'])) {
            $selectedRank = (int)$_REQUEST['rank'];
            if ($selectedRank < 1) {
                $selectedRank = 1;
            }
        } else if (!isset($_GET['page'])) {
            $selectedRank = Session::getInstance()->getAllianceId() <= 0 ? 0 : $statistics->getAllianceRankById(Session::getInstance()->getAllianceId());
        }
        $pageIndex = !$selectedRank ? (isset($_GET['page']) ? (int)$_GET['page'] - 1 : 0) : floor(($selectedRank - 1) / $this->pageSize);
        $prefix = [];
        $prefix['id'] = $selectedTabIndex;
        $prefix['idSub'] = $selectedSubIndex;
        if ($pageIndex < 0) {
            $pageIndex = 0;
        }
        $total = $statistics->getAllianceListCount();
        $tot_pages = ceil($total / $this->pageSize);
        if ($pageIndex + 1 > $tot_pages) {
            $pageIndex = 0;
        }
        $nav = new PageNavigator($pageIndex + 1, $total, $this->pageSize, $prefix, "statistiken.php");
        $rows = $statistics->getAlliancesList($pageIndex, $this->pageSize);
        $tableBody = '';
        $rowIndex = 0;
        foreach ($rows as $row) {
            $rank = ++$rowIndex + $pageIndex * $this->pageSize;
            $class = $rank == $selectedRank ? 'hl' : 'hover';
            $avg = $row['memcount'] == 0 ? 0 : round($row['points'] / $row['memcount']);
            $admin = null;
            if (Session::getInstance()->isAdmin()) {
                $admin = '<td class="buttons" style="padding: 2px 0 2px 2px;white-space: nowrap;width: 1%;"><button type="button" class="icon" onclick="window.location.href=\'admin.php?action=editAlliance&aid=' . $row['id'] . '\'"><img style="' . (getDirection() == 'LTR' ? 'left' : 'right') . ': 6px;" src="' . get_gpack_cdn_mainPage_url() . 'img_ltr/f/edit.gif"></button></td>';
            }
            $tableBody .= <<<HTML
<tr class="{$class}">

			<td class="ra " >{$rank}.</td>
			<td class="al " ><a href="allianz.php?aid={$row['id']}">{$row['tag']}</a> </td>
			<td class="pla " >{$row['memcount']}</td>
			<td class="avg " >{$avg}</td>
			<td class="po " >{$row['points']}</td>
			{$admin}
		</tr>
HTML;
        }

        return PHPBatchView::render('statistics/table',
            [
                "tableId" => "",
                "tableColumns" => '<tr>
			<td></td>
			<td>' . T("Statistics", "tabs.alliances") . '</td>
			<td>' . T("Statistics", "tabs.players") . '</td>
			<td>Ø</td>
			<td>' . T("Statistics", "points") . '</td>
			' . (Session::getInstance()->isAdmin() ? '<td class="buttons">' . T("Statistics",
                            "Actions") . '</td>' : '') . '
		</tr>',
                "headerRound" => T("Statistics", "largestAlliances"),
                "http_query" => !sizeof($prefix) ? '' : "?" . http_build_query($prefix),
                "tableBody" => $tableBody,
                "Navigator" => $nav->get(),
                "selectedRank" => isset($_REQUEST['rank']) ? $_REQUEST['rank'] : $selectedRank,
                "selectedName" => isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
            ]);
    }

    /** alliances */
    private function processAllianceAttackerOrDefender($selectedTabIndex, $selectedSubIndex, $isDefender)
    {
        $statistics = new StatisticsModel();
        $selectedRank = 0;
        if (isset($_REQUEST['name']) && trim($_REQUEST['name']) != "") {
            $selectedRank = $statistics->getAlliancePointsRankByName(filter_var($_REQUEST['name'],
                FILTER_SANITIZE_STRING),
                $isDefender);
            if (!$selectedRank) {
                $this->content->vars['error'] = sprintf(T("Statistics", "errors.allianceNotFound"),
                    filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            }
        } else if (isset($_REQUEST['rank']) && is_numeric($_REQUEST['rank'])) {
            $selectedRank = (int)$_REQUEST['rank'];
            if ($selectedRank < 1) {
                $selectedRank = 1;
            }
        } else if (!isset($_GET['page'])) {
            $selectedRank = Session::getInstance()->getAllianceId() <= 0 ? 0 : $statistics->getAlliancePointsRankById(Session::getInstance()->getAllianceId(),
                $isDefender);
        }
        $pageIndex = !$selectedRank ? (isset($_GET['page']) ? (int)$_GET['page'] - 1 : 0) : floor(($selectedRank - 1) / $this->pageSize);
        $prefix = [];
        $prefix['id'] = $selectedTabIndex;
        $prefix['idSub'] = $selectedSubIndex;
        if ($pageIndex < 0) {
            $pageIndex = 0;
        }
        $total = $statistics->getAllianceListCount();
        $tot_pages = ceil($total / $this->pageSize);
        if ($pageIndex + 1 > $tot_pages) {
            $pageIndex = 0;
        }
        $nav = new PageNavigator($pageIndex + 1, $total, $this->pageSize, $prefix, "statistiken.php");
        $rows = $statistics->getAlliancePointsList($pageIndex, $this->pageSize, $isDefender);
        $tableBody = '';
        $rowIndex = 0;
        foreach ($rows as $row) {
            $rank = ++$rowIndex + $pageIndex * $this->pageSize;
            $class = $rank == $selectedRank ? 'hl' : 'hover';
            $avg = $row['memcount'] == 0 ? 0 : round($row['totalPop'] / $row['memcount']);
            $tableBody .= <<<HTML
<tr class="{$class}">

			<td class="ra " >{$rank}.</td>
			<td class="al " ><a href="allianz.php?aid={$row['id']}">{$row['tag']}</a> </td>
			<td class="pla " >{$row['memcount']}</td>
			<td class="avg " >{$avg}</td>
			<td class="po " >{$row['points']}</td>
		</tr>
HTML;
        }

        return PHPBatchView::render('statistics/table',
            [
                "tableId" => "",
                "tableColumns" => '<tr>
			<td></td>
			<td>' . T("Statistics", "tabs.alliances") . '</td>
			<td>' . T("Statistics", "tabs.players") . '</td>
			<td>Ø</td>
			<td>' . T("Statistics", "points") . '</td>
		</tr>',
                "headerRound" => T("Statistics", "largestAlliances") . ' (' . T("Statistics",
                        "subTabs." . ($isDefender ? "defender" : "attacker")) . ')',
                "http_query" => !sizeof($prefix) ? '' : "?" . http_build_query($prefix),
                "tableBody" => $tableBody,
                "Navigator" => $nav->get(),
                "selectedRank" => isset($_REQUEST['rank']) ? $_REQUEST['rank'] : $selectedRank,
                "selectedName" => isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
            ]);
    }

    /** villages */
    private function processVillagesOverview($selectedTabIndex, $selectedSubIndex)
    {
        $statistics = new StatisticsModel();
        $selectedRank = 0;
        if (isset($_REQUEST['name']) && trim($_REQUEST['name']) != "") {
            $selectedRank = $statistics->getVillageRankByName(filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            if (!$selectedRank) {
                $this->content->vars['error'] = sprintf(T("Statistics", "errors.villageNotFound"),
                    filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            }
        } else if (isset($_REQUEST['rank']) && is_numeric($_REQUEST['rank'])) {
            $selectedRank = (int)$_REQUEST['rank'];
            if ($selectedRank < 1) {
                $selectedRank = 1;
            }
        } else if (!isset($_GET['page'])) {
            $selectedRank = $statistics->getVillageRankById(Session::getInstance()->getSelectedVillageID());
        }
        $pageIndex = !$selectedRank ? (isset($_GET['page']) ? (int)$_GET['page'] - 1 : 0) : floor(($selectedRank - 1) / $this->pageSize);
        $prefix = [];
        $prefix['id'] = $selectedTabIndex;
        $prefix['idSub'] = $selectedSubIndex;
        if ($pageIndex < 0) {
            $pageIndex = 0;
        }
        $total = $statistics->getTotalVillagesCount();
        $tot_pages = ceil($total / $this->pageSize);
        if ($pageIndex + 1 > $tot_pages) {
            $pageIndex = 0;
        }
        $nav = new PageNavigator($pageIndex + 1, $total, $this->pageSize, $prefix, "statistiken.php");
        $rows = $statistics->getVillagesList($pageIndex, $this->pageSize);
        $tableBody = '';
        $rowIndex = 0;
        $direction = strtolower(getDirection());
        foreach ($rows as $row) {
            $rank = ++$rowIndex + $pageIndex * $this->pageSize;
            $class = $rank == $selectedRank ? 'hl' : 'hover';
            $xy = Formulas::kid2xy($row['kid']);
            $coords = '<a class="" href="karte.php?x=' . $xy['x'] . '&amp;y=' . $xy['y'] . '">‎‭<span class="coordinates coordinatesWrapper coordinatesAligned coordinates' . $direction . '"><span class="coordinateX">(‭' . $xy['x'] . '‬‬</span><span class="coordinatePipe">|</span><span class="coordinateY">‭‭' . $xy['y'] . '‬‬)</span></span>‬‎</a>';
            $tableBody .= <<<HTML
<tr class="{$class}">

			<td class="ra " >{$rank}.</td>
			<td class="villageName " ><a href="karte.php?d={$row['kid']}">{$row['name']}</a> </td>
			<td class="pla " ><a href="spieler.php?uid={$row['owner']}">{$row['player_name']}</a></td>
			<td class="hab " >{$row['pop']}</td>
			<td class="coords" >$coords</td>
		</tr>
HTML;
        }

        return PHPBatchView::render('statistics/table',
            [
                "tableId" => "villages",
                "tableColumns" => '<tr>
			<td></td>
			<td>' . T("Statistics", "tabs.villages") . '</td>
			<td>' . T("Statistics", "player") . '</td>
			<td>' . T("Statistics", "population") . '</td>
			<td>' . T("Statistics", "coordinates") . '</td>
		</tr>',
                "headerRound" => T("Statistics", "largestVillages"),
                "http_query" => !sizeof($prefix) ? '' : "?" . http_build_query($prefix),
                "tableBody" => $tableBody,
                "Navigator" => $nav->get(),
                "selectedRank" => isset($_REQUEST['rank']) ? $_REQUEST['rank'] : $selectedRank,
                "selectedName" => isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
            ]);
    }

    /** hero */
    private function processHerosOverview($selectedTabIndex, $selectedSubIndex)
    {
        $statistics = new StatisticsModel();
        $selectedRank = 0;
        if (isset($_REQUEST['name']) && trim($_REQUEST['name']) != "") {
            $selectedRank = $statistics->getHeroRankByName(filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            if (!$selectedRank) {
                $this->content->vars['error'] = sprintf(T("Statistics", "errors.userNotFound"), filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING));
            }
        } else if (isset($_REQUEST['rank']) && is_numeric($_REQUEST['rank'])) {
            $selectedRank = (int)$_REQUEST['rank'];
            if ($selectedRank < 1)
                $selectedRank = 1;
        } else if (!isset($_GET['page'])) {
            $selectedRank = $statistics->getHeroRankById(Session::getInstance()->getPlayerId());
        }
        $pageIndex = !$selectedRank ? (isset($_GET['page']) ? (int)$_GET['page'] - 1 : 0) : floor(($selectedRank - 1) / $this->pageSize);
        $prefix = [];
        $prefix['id'] = $selectedTabIndex;
        $prefix['idSub'] = $selectedSubIndex;
        if ($pageIndex < 0) $pageIndex = 0;
        $total = $statistics->getHeroListCount();
        $tot_pages = ceil($total / $this->pageSize);
        if ($pageIndex + 1 > $tot_pages) $pageIndex = 0;
        $nav = new PageNavigator($pageIndex + 1, $total, $this->pageSize, $prefix, "statistiken.php");
        $rows = $statistics->getHerosList($pageIndex, $this->pageSize);
        $rows = is_array($rows) ? $rows : iterator_to_array($rows);
        $tribes = $this->playersTribeAlliance(array_column($rows, 'uid'));
        $tableBody = '';
        $rowIndex = 0;
        foreach ($rows as $row) {
            $rank = ++$rowIndex + $pageIndex * $this->pageSize;
            $class = $rank == $selectedRank ? 'hl' : 'hover';
            $level = Formulas::heroLevel($row['exp']);
            $row['exp'] = number_format_x($row['exp']);
            $info = $tribes[(int)$row['uid']] ?? [];
            $tribe = self::tribeCell($info);
            $alliance = !empty($info['aid']) ? '<a href="allianz.php?aid=' . (int)$info['aid'] . '">' . htmlspecialchars((string)$info['tag']) . '</a>' : '-';
            $tableBody .= <<<HTML
<tr class="{$class}">

			<td class="ra " >{$rank}.</td>
			{$tribe}
			<td class="pla " ><a href="spieler.php?uid={$row['uid']}">{$row['player_name']}</a> </td>
			<td class="al " >{$alliance}</td>
			<td class="lev " >$level</td>
			<td class="xp " >{$row['exp']}</td>
		</tr>
HTML;
        }

        return PHPBatchView::render('statistics/table',
            [
                "tableId" => "heros",
                "tableColumns" => '<tr>
			<td></td>
			<td class="tribe"></td>
			<td>' . T("Statistics", "tabs.players") . '</td>
			<td>' . T("Statistics", "alliance") . '</td>
			<td>' . T("Statistics", "level") . '</td>
			<td>' . T("Statistics", "xp") . '</td>
		</tr>',
                "headerRound" => T("Statistics", "most exp heros"),
                "http_query" => !sizeof($prefix) ? '' : "?" . http_build_query($prefix),
                "tableBody" => $tableBody,
                "Navigator" => $nav->get(),
                "selectedRank" => isset($_REQUEST['rank']) ? $_REQUEST['rank'] : $selectedRank,
                "selectedName" => isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
            ]);
    }

    private function showFarms()
    {
        $m = new StatisticsModel();
        $page = isset($_REQUEST['page']) ? max(abs((int)$_REQUEST['page']), 1) : 1;
        $navigator = new PageNavigator($page, $m->getTotalFarmVillagesCount(), $this->pageSize, ['id' => 8], 'statistiken.php');
        $farms = $m->getFarmVillages($page, $this->pageSize);
        return PHPBatchView::render('statistics/farm', ['farms' => $farms, 'navigator' => $navigator->get()]);
    }
}