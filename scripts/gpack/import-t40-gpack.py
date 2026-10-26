#!/usr/bin/env python3
"""Builds the Travian 4.0 graphic pack (sections/gpack/T40) from travian_Travian_4.0_41.

T40 has its own page frame, layout/LayoutT40.php (gpack.php 'layout' => 't40'): the 4.0 header with the round
navigation, the resource line below it, the village sign on the left and hero, player, alliance and village list
signs on the right. The pages inside the frame are the T4.4 ones, which grew from 4.0, so the pack is
  1. a copy of TravianOld (T4.4 Classic) whose pictures are replaced by the 4.0 ones wherever 4.0 has the same
     picture in the same size: buildings, resource fields, units, icons, ...,
  2. without the T4.4 rules for the page frame (compact*.css),
  3. plus lang/openvillage-t40.css: the 4.0 rules for the page frame, with the 4.0 pictures they need in img40/.
Some of the 4.0 "-rtl" pictures are drawn for its left-to-right English stylesheet (page frame, backgrounds,
resource fields), others are really mirrored (buildings, units); see replace_pictures().
openvillage-compat.css is generated afterwards (scripts/gpack/make-classic-compat.py T40).

Usage: python3 -I scripts/gpack/import-t40-gpack.py <unzipped gpack/travian_Travian_4.0_41>
"""
import os
import re
import shutil
import sys

from PIL import Image, ImageChops, ImageOps, ImageStat

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
GPACK = os.path.join(ROOT, 'sections', 'gpack')

# The page frame of T4.4 (Layout.php) and of 4.0 (LayoutT40.php): selectors naming one of these belong to it.
FRAME_T44 = re.compile(r'#(header|logo|navigation|background|bodyWrapper|center|contentOuterContainer|footer|'
                       r'outOfGame|goldSilver|servertime|headerBar|ingameManual|stockBar\w*|sidebarBeforeContent|'
                       r'myGameLink\w*)\b|^\s*body(\.[\w-]+)*\s*$|(^|[\s,])\.stime\b')
FRAME_40 = re.compile(r'#(wrapper|staticElements|header|mtop|logo|navigation|plusLink|plus|gs|myGameLink\w*|mid|'
                      r'side_info|villageName|villageList|linkList|res|contentOuterContainer|footer|mfoot|'
                      r'ingameManual|logout|anwersQuestionMark|sideInfoCountdown|side_navi)\b|\.questMaster\b|^\s*body(\.[\w-]+)*\s*$|(^|[\s,])\.stime\b|'
                      r'^\s*(body(\.[\w-]+)*\s+)?#content(\.[\w-]+)?(>h1)?\s*$|^\s*\.bubble\b')
# 4.0 rules for pages that are not part of the game (login, sign-up, error pages)
OUTSIDE = re.compile(r'\b(loader|login|logout|signup|support|activate|error_site|fatal_error|banned_cn|'
                     r'support_form|presto|ie\d?|manual|map\.fullScreen)\b|cooperationLogo|#betaBox')
# ... except the pages that run inside the 4.0 frame: tribe and start choice (activate.php), login, logout and
# the error pages
ACTIVATE = re.compile(r'^\s*body\.(activate|login|logout|error_site)\b')
# 4.0 rules for page parts whose markup T4.4 kept from 4.0 (tabs, ...): they replace the T4.4 look
COMPONENTS_40 = re.compile(r'^\s*\.(contentNavi|subNavi|tabNavi)\b')
# The T4.4 pages inside the 4.0 frame: where 4.0 placed them differently
PAGES_IN_FRAME = '''
div.village1 div#map_details{position:absolute;right:0;top:10px;width:245px;margin:0;float:none}
#sideInfoCountdown{z-index:60}
#sideInfoCountdown .content ul{margin:0;padding:0;list-style:none}
#sideInfoCountdown .content{padding-bottom:14px}
#sideInfoCountdown .content li{display:none;overflow:hidden;max-height:64px}
#sideInfoCountdown .content li p{margin:0}
#sideInfoCountdown .content li.shown{display:block}
/* the close cross stays on the board (T4.4 places it absolutely, onto the frame), the text flows round it */
#sideInfoCountdown .content li .infoboxDeleteButton{float:right;position:static;margin:2px 0 2px 3px}
/* the button (Prolong, 25 gold) is as wide as the board's inner width at most: narrower ends */
#sideInfoCountdown .content li button{max-width:100%}
#sideInfoCountdown .content li button .button-content{padding-left:10px;padding-right:10px}
#sideInfoCountdown .content li button .buttonMiddle{margin:0 10px}
#sideInfoCountdown .pager{margin-top:2px;font-size:11px}
#sideInfoCountdown .pager a{cursor:pointer;font-weight:bold;padding:0 4px}
.questMaster .master{display:block;border:0;padding:0;background-color:transparent}
/* 4.0: the quest master crosses his arms while a task is open and leans on the sign once it is done */
.questMaster .master,.questMaster .master:hover{background-position:right top}
.questMaster:has(+ #t40Questmaster .newQuestSpeechBubble) .master{background-position:left top}
/* the Teuton's arms-crossed picture of the 4.0 pack leans the RTL way (towards the left): mirror it */
.questMaster .master.nation2{transform:scaleX(-1)}
.questMaster:has(+ #t40Questmaster .newQuestSpeechBubble) .master.nation2{transform:none}
#t40Questmaster{display:none}
'''

# The T4.4 pages restyled the way they looked in Travian 4.0 (after a 2012 video of a 4.0 server): green buttons that
# turn grey under the mouse, the Travian logo, the white quest popup with its big picture, the building page, the white level circles
# on the resource fields and the tribe choice. Pictures as in the 4.0 stylesheet (../../img/) come from the 4.0 pack.
LOOK_40 = '''
body.layoutT40 div#header #logo{background-image:url(../../img/layout/logoSmall.png) !important;font-size:0;line-height:0}
body.layoutT40.activate div#header #logo,body.layoutT40.login div#header #logo,body.layoutT40.logout div#header #logo,body.layoutT40.error_site div#header #logo{background-image:url(../../img/layout/logoBig.png) !important}
body.layoutT40 div#header #logo::before{content:none}
#plusLink #plus{top:-4px;z-index:2}
#plusLink #plus a.plusBtn .plusBtn-l,#plusLink #plus a.plusBtn .plusBtn-r{vertical-align:top}
#side_info .sideInfoHero .heroImage{width:112px;margin-left:5px;margin-top:22px}
button.green .button-background,button.orange .button-background{display:none}
button.green .button-container,button.orange .button-container{height:25px;border-radius:4px;background:linear-gradient(#c4d983,#abc558 46%,#7a9d0e 54%,#708f11);box-shadow:0 1px 1px rgba(0,0,0,.25)}
button.green:hover .button-container,button.orange:hover .button-container,button.green .button-container.hover,button.orange .button-container.hover,button.green .button-container.click,button.orange .button-container.click{background:linear-gradient(#b2b2b2,#939393 46%,#5c5c5c 54%,#555)}
button.green .button-container .button-content,button.orange .button-container .button-content{color:#fff;text-shadow:none}
button.green.disabled .button-container,button.green:disabled .button-container,button.orange.disabled .button-container,button.orange:disabled .button-container{background:linear-gradient(#f0f0f0,#d6d6d6);box-shadow:none}
button.green.disabled .button-container .button-content,button.green:disabled .button-container .button-content,button.orange.disabled .button-container .button-content,button.orange:disabled .button-container .button-content{color:#999}
/* buttons the scripts build without the T4.4 inner boxes (the OK button of the game dialogs, e.g. using a hero item) */
button.green:not(:has(.button-container)),button.orange:not(:has(.button-container)){height:25px;padding:0 20px;border-radius:4px;outline:none;color:#fff;font-weight:bold;font-size:11px;line-height:25px;text-align:center;background:linear-gradient(#c4d983,#abc558 46%,#7a9d0e 54%,#708f11);box-shadow:0 1px 1px rgba(0,0,0,.25)}
button.green:not(:has(.button-container)):hover,button.orange:not(:has(.button-container)):hover{background:linear-gradient(#b2b2b2,#939393 46%,#5c5c5c 54%,#555)}
button.green:not(:has(.button-container)):disabled,button.orange:not(:has(.button-container)):disabled,button.green.disabled:not(:has(.button-container)),button.orange.disabled:not(:has(.button-container)){color:#999;background:linear-gradient(#f0f0f0,#d6d6d6);box-shadow:none}
body.gidResources{background-image:url(../../img/layout/bgR.jpg)}
body.gidResources #wrapper{background-image:url(../../img/layout/bgResources-rtl.jpg)}
/* 4.0 had no tutorial highlight: the T4.4 red frame would surround the hidden quest master button */
body.layoutT40 .highlighted.rectangle{display:none !important}
/* 4.0: the question mark leans the other way, in the middle of the book cover */
#ingameManual .question{transform:translate(5px,-2px) rotate(-35deg)}
/* free building sites a little lighter than the T4.4 pictures */
.buildingsV1 #village_map .buildingSlot img.iso,.buildingsV1 #village_map .buildingSlot img.g16e{opacity:.75}
/* phones in portrait (iOS app): zoom out so the whole left side of the game shows, starting at its left edge */
body.layoutT40{--ov-portrait-zoom-out:1.45;--ov-portrait-align:left}
/* a window narrower than the game (phones in portrait): the signs and the quest master on the left tree stick out
   of the 990px game, so keep a margin on the left where they can show (the scenery goes on in it); the trees of
   #staticElements cover the whole wrapper, not 30px past its right edge (that widened the page past the scenery) */
@media (max-width:1060px){body.layoutT40{min-width:1020px}body.layoutT40 #wrapper{box-sizing:border-box;padding-left:30px;background-origin:content-box}body.layoutT40 #wrapper #staticElements{left:0;background-position:calc(50% + 15px) top}}
/* the image map of the resource fields uses the 400px wide T4.4 coordinates: centre it on the 500px wide 4.0 fields */
div.village1 img#resfeld{left:56px;margin-top:4px}
/* level circles as in the Legends look: coloured ring by state (green can be built, yellow not now, blue max),
 white middle, orange while building */
div.village1 #village_map .level.colorLayer,div.village1 #village_map .level.colorLayer.good,div.village1 #village_map .level.colorLayer.notNow,div.village1 #village_map .level.colorLayer.maxLevel{box-sizing:content-box;width:26px;height:26px;margin:0;border-radius:50%;box-shadow:inset 0 0 2px 0 #fff;border:1px solid #333;background:linear-gradient(to top,#606060,#c8c8c8 100%)}
div.village1 #village_map .level.colorLayer.good{border-color:#506d00;background:linear-gradient(to top,#7da100,#c7e94f 100%)}
div.village1 #village_map .level.colorLayer.notNow{border-color:#988b42;background:linear-gradient(to top,#988b42,#fdf15f 100%)}
div.village1 #village_map .level.colorLayer.maxLevel{border-color:#0d648e;background:linear-gradient(to top,#0d648e,#b1e4ff 100%)}
div.village1 #village_map .level.colorLayer .labelLayer,div.village1 #village_map .level.colorLayer.underConstruction .labelLayer{position:absolute;top:4px;left:4px;right:auto;width:18px;height:18px;border-radius:50%;background:#fff;line-height:18px;font-size:11px;font-family:Verdana,sans-serif;text-align:center;color:#333}
div.village1 #village_map .level.colorLayer.underConstruction .labelLayer{background:#ffa84f}
/* the village name on the sign stays one line with the loyalty below it (dorf3 on a click, rename on a double click) */
#villageName #villageNameField{display:inline;position:static;top:auto;left:auto;width:auto;height:auto;padding:0;overflow:visible}
/* 4.0 resource line: green bars, red once the store is full */
#res li .bar{background-color:#046802}
#res li .bar.stockFull{background-color:#d40000}
/* tutorial tip: the T4.4 highlighter puts an empty copy of the field circle 4px around it (class "highlighted
   background", inline size 32x31): no circle in the copy, a thin red ring around the 4.0 circle instead of the
   thick T4.4 ring */
div.village1 #village_map .level.highlighted.background{background:none;margin:2px 0 0 2px}
div.village1 #village_map .level.highlighted.background::after{content:"";position:absolute;left:0;top:0;width:31px;height:31px;box-sizing:border-box;border:2.5px solid red;border-radius:50%;pointer-events:none}
.colorBlind div.village1 #village_map .level.highlighted.background::after{border-color:#0024d8}
.contentNavi.subNavi{display:flex;align-items:flex-start;width:auto;margin-right:-46px;overflow:visible}
#content>a#tabFavorButton{position:absolute}
.tabNavi .container.active .content .tabItem{color:#252525}
.contentNavi.subNavi .container{flex:none}
.subNavi .container .content .tabItem{padding:11px 6px 0}
div.village2 div#village_map div.buildingSlot .level.colorLayer,div.village2 div#village_map div.buildingSlot .level.colorLayer.good,div.village2 div#village_map div.buildingSlot .level.colorLayer.notNow,div.village2 div#village_map div.buildingSlot .level.colorLayer.maxLevel{box-sizing:content-box;width:26px;height:26px;margin:0;border-radius:50%;box-shadow:inset 0 0 2px 0 #fff;border:1px solid #333;background:linear-gradient(to top,#606060,#c8c8c8 100%)}
div.village2 div#village_map div.buildingSlot .level.colorLayer.good{border-color:#506d00;background:linear-gradient(to top,#7da100,#c7e94f 100%)}
div.village2 div#village_map div.buildingSlot .level.colorLayer.notNow{border-color:#988b42;background:linear-gradient(to top,#988b42,#fdf15f 100%)}
div.village2 div#village_map div.buildingSlot .level.colorLayer.maxLevel{border-color:#0d648e;background:linear-gradient(to top,#0d648e,#b1e4ff 100%)}
div.village2 div#village_map div.buildingSlot .level.colorLayer .labelLayer,div.village2 div#village_map div.buildingSlot .level.colorLayer.underConstruction .labelLayer{position:absolute;top:4px;left:4px;right:auto;width:18px;height:18px;border-radius:50%;background:#fff;line-height:18px;font-size:11px;font-family:Verdana,sans-serif;text-align:center;color:#333}
div.village2 div#village_map div.buildingSlot .level.colorLayer.underConstruction .labelLayer{background:#ffa84f}
/* the level switch ("+" / "-") hides the level circles of the buildings */
div.village2 div#village_map.levelsHidden .level{display:none}
#content.build h1.titleInHeader .level{text-transform:capitalize}
#build .roundedCornersBox.big{background:none;border:0;box-shadow:none;padding:0;margin:15px 0 0}
#build .roundedCornersBox.big>h4{display:none}
div#build .upgradeButtonsContainer,div#build .upgradeButtonsContainer .section1,div#build .upgradeButtonsContainer .section2{display:block;border:0;background:none;text-align:left;float:none;width:auto}
div#build .culturePointsAndPopulation{text-align:left;margin:8px 0 0}
div#build i.cropConsumptionBig,div#build i.culturePoints_medium,div#build i.population_medium,div#build i.clock_medium{width:16px;height:16px;background-size:contain;background-position:center;vertical-align:middle}
div#build table#build_value{width:auto}
div#build table#build_value th{width:170px;padding-right:10px}
div#build table#build_value td{text-align:left}
div#build .upgradeButtonsContainer .section1{display:flex;flex-direction:column-reverse;align-items:flex-start}
div#build .upgradeButtonsContainer .section1 .inlineIcon.duration{margin:0 0 12px}
body:has(.dialog.questInformation) > .overlay{background:none !important}
.dialog.questInformation{width:513px;color:#252525}
.dialog.questInformation .dialog-container{padding:8px 0;background:url(../../img/q/whitePopupTop.png) left top no-repeat border-box,url(../../img/q/whitePopupBottom.png) left bottom no-repeat border-box,url(../../img/q/whitePopupMiddle.png) left top repeat-y content-box}
.dialog.questInformation .dialog-container>div[class^=dialog-background]{display:none}
.dialog.questInformation .dialog-contents{padding:8px 20px 12px;width:auto}
.dialog.white.questInformation .dialog-contents .dialog-dragbar{position:absolute;margin:0;left:7px;top:-1px;width:499px !important;height:18px;background:#eee}
.dialog.questInformation .dialog-dragbar div{display:none}
.dialog.questInformation #dialogCancelButton{position:absolute;top:-1px;right:7px;left:auto;width:18px;height:18px;border:0;margin:0;background:url(../../img/q/close.png) 5px 4px no-repeat;z-index:3}
.dialog.questInformation #dialogCancelButton:hover{background-position:5px -15px}
.dialog.questInformation .questWrapper{display:flex;flex-direction:column;width:auto}
.dialog.questInformation .questWrapper .questText{order:1;float:none;width:auto;margin:0}
.dialog.questInformation .questWrapper .questImage{order:3;float:none;width:auto;height:auto;margin:5px 0 0;padding:0;border:0;background:none;text-align:center}
.dialog.questInformation .questWrapper .clear{order:4}
.dialog.questInformation .questWrapper .questButtons{order:2;display:flex;height:auto;margin:12px 0 0}
.dialog.questInformation .questWrapper .questButtons button{position:static;margin:0;height:auto}
.dialog.questInformation .questWrapper .questButtons button:last-of-type{margin-left:auto}
.dialog.questInformation .questWrapper .questButtons button .button-container,.dialog.questInformation .questWrapper .questButtons button .button-container.hover,.dialog.questInformation .questWrapper .questButtons button:hover .button-container{height:auto;background:none;box-shadow:none}
.dialog.questInformation .questWrapper .questButtons button .button-container .button-content{padding:0;font-size:13px;color:#99c01a}
.dialog.questInformation .questWrapper .questButtons button:hover .button-container .button-content{color:#00bc00}
.dialog.questInformation .questWrapper .questButtons button .button-content:before{content:"\\25B8";color:#252525;margin-right:4px}
.dialog.questInformation .questDescription h4{font-size:13px;font-weight:bold;margin:12px 0 2px}
.dialog.questInformation #questTodolist{margin:0;padding:0;list-style:none}
.dialog.questInformation .questDescription,.dialog.questInformation .questTasks{margin:0;padding:0}
.dialog.questInformation .questTasks ul#questTodolist li{padding:0;margin:0}
.dialog.questInformation #questTodolist li img{display:none}
.dialog.questInformation .questDescription>br{display:none}
.dialog.questInformation .questTitle{font-size:13px;font-weight:bold;margin:3px 0 20px}
.dialog.questInformation .questDescription #questDescription{font-style:italic}
.dialog.questInformation #questLogo.tutorial_01_task_image_vid1{width:473px;height:257px;background:url(../../img/q/introRomans.jpg)}
.dialog.questInformation #questLogo.tutorial_01_task_image_vid2{width:473px;height:257px;background:url(../../img/q/introTeutons.jpg)}
.dialog.questInformation #questLogo.tutorial_01_task_image_vid3{width:473px;height:257px;background:url(../../img/q/introGauls.jpg)}
.dialog.questInformation #questLogo.tutorial_01_task_image_vid6,.dialog.questInformation #questLogo.tutorial_01_task_image_vid7{width:473px;height:257px;background:url(../../img/q/intro.jpg)}
.questT40Dialog .dialog-contents>.questT40{width:473px}
.questT40 h4{font-size:13px;font-weight:bold;margin:16px 0 20px}
.questT40 .spoken{margin-bottom:15px;font-style:italic}
.questT40 .rew{margin-bottom:15px;overflow:hidden}
.questT40 .rew p.ta_aw{margin:0 0 5px;font-weight:bold}
.questT40 .rew .resources{float:left;margin-right:16px;white-space:nowrap}
.questT40 .rew .resources i,.questT40 .rew .resources img{margin-right:5px;vertical-align:middle}
.questT40 .rew b.plus{color:#71d000}
.questT40 .links{display:flex;justify-content:space-between;margin:0 0 12px}
.questT40 a.arrow{background:none;padding:0;color:#99c01a;font-weight:bold}
.questT40 a.arrow:hover{color:#00bc00}
.questT40 a.arrow:before{content:"\\25B8";color:#252525;margin-right:4px}
.questT40 .answer{display:flex;align-items:center;gap:10px;margin:0 0 12px}
.questT40 .answer input.text{width:60px}
.questT40 .choice{display:flex;justify-content:space-between;margin:0 12px 12px}
.questT40 p.wrong{color:#c00;margin:0 0 12px}
.questT40 ul.optional{margin:0 0 12px;padding:0;list-style:none}
.questT40 ul.optional li{padding:2px 0}
.questT40 ul.optional span.done{color:#777;text-decoration:line-through}
.questT40 ul.optional span.ready{margin-left:6px;color:#00bc00;font-size:11px}
.questT40 #qstbg{margin-top:5px;height:257px;width:473px;background-repeat:no-repeat}
.questT40 #qstbg.wood{background-image:url(../../img/q/wood.jpg)}
.questT40 #qstbg.farm{background-image:url(../../img/q/farm.jpg)}
.questT40 #qstbg.village_name{background-image:url(../../img/q/village_name.jpg)}
.questT40 #qstbg.rally{background-image:url(../../img/q/rally.jpg)}
.questT40 #qstbg.first_adventure{background-image:url(../../img/q/first_adventure.jpg)}
.questT40 #qstbg.rank1{background-image:url(../../img/q/rank1.jpg)}
.questT40 #qstbg.clay_iron{background-image:url(../../img/q/clay_iron.jpg)}
.questT40 #qstbg.msg{background-image:url(../../img/q/msg.jpg)}
.questT40 #qstbg.res_one_of_each{background-image:url(../../img/q/res_one_of_each.jpg)}
.questT40 #qstbg.neighbour{background-image:url(../../img/q/neighbour.jpg)}
.questT40 #qstbg.heroproduction{background-image:url(../../img/q/heroproduction.jpg)}
.questT40 #qstbg.adventure_report{background-image:url(../../img/q/adventure_report.jpg)}
.questT40 #qstbg.second_adventure{background-image:url(../../img/q/second_adventure.jpg)}
.questT40 #qstbg.res_all_one{background-image:url(../../img/q/res_all_one.jpg)}
.questT40 #qstbg.medal{background-image:url(../../img/q/medal.jpg)}
.questT40 #qstbg.cranny{background-image:url(../../img/q/cranny.jpg)}
.questT40 #qstbg.res_two_of_each{background-image:url(../../img/q/res_two_of_each.jpg)}
.questT40 #qstbg.cost{background-image:url(../../img/q/cost.jpg)}
.questT40 #qstbg.main{background-image:url(../../img/q/main.jpg)}
.questT40 #qstbg.rank2{background-image:url(../../img/q/rank2.jpg)}
.questT40 #qstbg.granary_barracks{background-image:url(../../img/q/granary_barracks.jpg)}
.questT40 #qstbg.granary_barracks1{background-image:url(../../img/q/granary_barracks1.jpg)}
.questT40 #qstbg.warehouse{background-image:url(../../img/q/warehouse.jpg)}
.questT40 #qstbg.market{background-image:url(../../img/q/market.jpg)}
.questT40 #qstbg.granary_barracks2{background-image:url(../../img/q/granary_barracks2.jpg)}
.questT40 #qstbg.units{background-image:url(../../img/q/units.jpg)}
.questT40 #qstbg.wall{background-image:url(../../img/q/wall.jpg)}
.questT40 #qstbg.earthwall{background-image:url(../../img/q/earthwall.jpg)}
.questT40 #qstbg.pallisade{background-image:url(../../img/q/pallisade.jpg)}
.questT40 #qstbg.freunde{background-image:url(../../img/q/freunde.jpg)}
.questT40 #qstbg.quest_res_all_two{background-image:url(../../img/q/quest_res_all_two.jpg)}
.questT40 #qstbg.quest_dispatch{background-image:url(../../img/q/quest_dispatch.jpg)}
.questT40 #qstbg.quest_alliance{background-image:url(../../img/q/quest_alliance.jpg)}
.questT40 #qstbg.quest_finished_ten_adventures{background-image:url(../../img/q/quest_finished_ten_adventures.jpg)}
.questT40 #qstbg.quest_main_on_five{background-image:url(../../img/q/quest_main_on_five.jpg)}
.questT40 #qstbg.quest_granary_on_three{background-image:url(../../img/q/quest_granary_on_three.jpg)}
.questT40 #qstbg.quest_warehouse_on_seven{background-image:url(../../img/q/quest_warehouse_on_seven.jpg)}
.questT40 #qstbg.quest_res_all_five{background-image:url(../../img/q/quest_res_all_five.jpg)}
.questT40 #qstbg.quest_palace_or_residence{background-image:url(../../img/q/quest_palace_or_residence.jpg)}
.questT40 #qstbg.quest_three_settlers{background-image:url(../../img/q/quest_three_settlers.jpg)}
.questT40 #qstbg.quest_new_village{background-image:url(../../img/q/quest_new_village.jpg)}
.questT40 #qstbg.intro{background-image:url(../../img/q/intro.jpg)}
.questT40 #qstbg.introRomans{background-image:url(../../img/q/introRomans.jpg)}
.questT40 #qstbg.introTeutons{background-image:url(../../img/q/introTeutons.jpg)}
.questT40 #qstbg.introGauls{background-image:url(../../img/q/introGauls.jpg)}
#side_navi ul li a{font-weight:bold;text-transform:uppercase}
.activate .activationScreen{box-sizing:border-box;background:#e4e4e4;border-radius:5px;padding:12px 20px 5px}
.activate .activationScreen #presentation{padding:15px 0 0;background:none}
.activate .activationScreen #presentation>svg{display:none}
.activate .activationScreen #presentation #tribeSelectors{display:block;position:relative;height:262px;width:calc(100% + 9px);margin:0 0 0 -9px}
.activate .activationScreen #presentation #tribeSelectors input + .selector{position:absolute;left:0;width:80px;min-width:0;max-width:none;height:72px;margin:0;border:0;border-radius:0;box-shadow:none;background:url(../img/activate/volkIcon-ltr.png) no-repeat}
.activate .activationScreen #presentation #tribeSelectors input:checked + .selector{border:0;box-shadow:none}
.activate .activationScreen #presentation #tribeSelectors input + .selector[data-text]:before{bottom:2px;right:2px}
.activate .activationScreen #presentation #tribeSelectors input[value="3"] + .selector{top:24px;background-position:-11px -88px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"] + .selector{top:100px;background-position:-11px -388px}
.activate .activationScreen #presentation #tribeSelectors input[value="2"] + .selector{top:176px;background-position:-11px -688px}
.activate .activationScreen #presentation #tribeSelectors input[value="3"] + .selector:hover{background-position:-11px -182px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"] + .selector:hover{background-position:-11px -482px}
.activate .activationScreen #presentation #tribeSelectors input[value="2"] + .selector:hover{background-position:-11px -782px}
.activate .activationScreen #presentation #tribeSelectors input[value="3"]:checked + .selector{background-position:-11px -282px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"]:checked + .selector{background-position:-11px -582px}
.activate .activationScreen #presentation #tribeSelectors input[value="2"]:checked + .selector{background-position:-11px -882px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"] + .selector:after,.activate .activationScreen #presentation #tribeSelectors input[value="2"] + .selector:after,.activate .activationScreen #presentation #tribeSelectors input[value="3"] + .selector:after{display:none}
.activate .activationScreen #presentation #tribeSelectors input[value="6"] + .selector,.activate .activationScreen #presentation #tribeSelectors input[value="7"] + .selector{display:none}
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription{display:none;left:91px;top:0;width:calc(100% - 91px);height:262px;padding:15px 18px;border:1px solid #d6d6d6;border-radius:3px;opacity:1;color:#252525;background:linear-gradient(#fff calc(100% - 247px),rgba(255,255,255,0) calc(100% - 167px)),#fff url(../img/activate/volkBackground-ltr.jpg) right bottom no-repeat}
.activate .activationScreen #presentation #tribeSelectors input:checked + .selector + .tribeDescription{display:block}
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription[data-text]:before{position:static;display:block;float:none;margin:0 0 8px;padding:0;background:none;border:0;color:#99c01a;text-transform:none;font-size:12px}
.activate .activationScreen #presentation #tribeSelectors .tribeDescription h2{font-size:120%;color:#252525;text-shadow:none;text-transform:none;margin:0 0 10px}
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription ul{position:static;padding:0;width:auto;height:auto;font-weight:normal}
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription ul li{max-width:250px;padding:2px 0 2px 16px;margin:0;line-height:140%;background:url(../img/activate/dot.png) left 9px no-repeat}
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription ul li:before{display:none}
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription ul:after{bottom:-70px;right:-12px;z-index:3;width:188px;height:200px;background-image:url(../../img/activate/avatars-rtl.png)}
.activate .activationScreen #presentation #tribeSelectors input[value="3"] + .selector + .tribeDescription ul:after{background-position:0 0}
.activate .activationScreen #presentation #tribeSelectors input[value="2"] + .selector + .tribeDescription ul:after{background-position:0 -200px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"] + .selector + .tribeDescription ul:after{background-position:0 -400px}
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription:after{content:'';position:absolute;left:-7px;width:12px;height:12px;box-sizing:border-box;border:solid #d6d6d6;border-width:0 0 1px 1px;background:#fff;transform:rotate(45deg)}
.activate .activationScreen #presentation #tribeSelectors input[value="3"] + .selector + .tribeDescription:after{top:54px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"] + .selector + .tribeDescription:after{top:130px}
.activate .activationScreen #presentation #tribeSelectors input[value="2"] + .selector + .tribeDescription:after{top:206px}
.activate .greenbox.t40Thanks{margin:0 0 12px;padding:8px 10px;border-radius:3px;background:#fff}
.activate .greenbox.t40Thanks .greenbox-content{float:none;width:auto;padding:0;color:#4b7e0d;font-weight:normal}
.activate .activationScreen .acceptChallenge{color:#252525;text-shadow:none}
body.activate #content.activate{padding-bottom:0}
.activate .activationScreen #presentation{height:auto;min-height:0}
.activate .activationScreen .buttonContainer{height:50px}
.activate .activationScreen .buttonContainer button{transform:none;box-shadow:none}
.activate .activationScreen .buttonContainer button.green:not(:hover):not(:focus):not(:active){text-shadow:none;animation:none}
.activate .activationScreen .buttonContainer button.green:not(:hover):not(:focus):not(:active):not(:disabled):before,.activate .activationScreen .buttonContainer button.green:not(:hover):not(:focus):not(:active):not(:disabled):after{display:none}
.activate .activationScreen .buttonContainer button.green:not(:hover):not(:focus):not(:active):not(:disabled) span{animation:none}
'''


def difference(a, b) -> float:
    return sum(ImageStat.Stat(ImageChops.difference(a, b)).mean)


def replace_pictures(src_img: str, pack_img: str) -> int:
    """Puts the 4.0 pictures over the T4.4 ones of the same name and size. A 4.0 "-rtl" picture is either drawn
    for the left-to-right page (page backgrounds, resource fields) or really mirrored (buildings, units, ...):
    it goes in the way round that is closer to the T4.4 picture it replaces."""
    replaced = 0
    for folder, _, files in os.walk(src_img):
        for name in files:
            source = os.path.join(folder, name)
            rel = os.path.relpath(source, src_img)
            names = [rel] + ([rel.replace('-rtl.', '-ltr.')] if '-rtl.' in name else [])
            for target in (os.path.join(pack_img, n) for n in names):
                if not os.path.isfile(target) or open(source, 'rb').read() == open(target, 'rb').read():
                    continue
                try:
                    new, old = Image.open(source), Image.open(target)
                    if new.size != old.size:
                        continue   # another sprite layout: the T4.4 stylesheet would cut it wrong
                    flip = False
                    if '-rtl.' in name:
                        a, b = new.convert('RGBA'), old.convert('RGBA')
                        straight, mirrored = difference(a, b), difference(ImageOps.mirror(a), b)
                        if min(straight, mirrored) < 0.5:
                            continue   # the T4.4 picture, only the other way round
                        flip = mirrored < straight
                except OSError:
                    continue
                if not flip:
                    shutil.copyfile(source, target)
                elif getattr(new, 'n_frames', 1) == 1:
                    image = ImageOps.mirror(new)
                    if target.lower().endswith(('.jpg', '.jpeg')):
                        image.convert('RGB').save(target, quality=92)
                    else:
                        image.save(target, **{k: new.info[k] for k in ('transparency',) if k in new.info})
                else:
                    continue   # mirrored animation: the T4.4 one stays
                replaced += 1
    return replaced


def blocks(css: str):
    """Top-level (prelude, body) pairs; @media bodies are nested lists, other @-blocks stay as text."""
    out, i, n = [], 0, len(css)
    while i < n:
        j = css.find('{', i)
        if j < 0:
            break
        prelude = css[i:j].strip()
        depth, k = 1, j + 1
        while k < n and depth:
            depth += {'{': 1, '}': -1}.get(css[k], 0)
            k += 1
        body = css[j + 1:k - 1]
        out.append((prelude, blocks(body) if prelude.startswith('@media') else body.strip()))
        i = k
    return out


def serialize(items, keep) -> str:
    out = []
    for prelude, body in items:
        if isinstance(body, list):
            inner = serialize(body, keep)
            if inner:
                out.append(prelude + '{' + inner + '}')
        elif prelude.startswith('@'):
            out.append(prelude + '{' + body + '}')
        else:
            sels = [s.strip() for s in prelude.split(',') if keep(s)]
            if sels:
                out.append(','.join(sels) + '{' + body + '}')
    return '\n'.join(out)


def strip_frame(css_dir: str) -> None:
    """Drops the T4.4 page frame rules from the pack's stylesheets (LayoutT40.php brings the 4.0 frame)."""
    for name in os.listdir(css_dir):
        if not name.endswith('.css'):
            continue
        path = os.path.join(css_dir, name)
        css = open(path, encoding='utf-8', errors='replace').read()
        imports = ''.join(re.findall(r'@import[^;]+;', css))
        css = re.sub(r'/\*.*?\*/', '', css, flags=re.S)
        css = re.sub(r'@(import|charset)[^;]+;', '', css)
        kept = serialize(blocks(css), lambda s: not FRAME_T44.search(s))
        with open(path, 'w', encoding='utf-8') as f:
            f.write('@charset "UTF-8";' + imports + '\n' + kept + '\n')


def frame_css(src: str, pack: str) -> str:
    """The 4.0 page frame rules; the pictures they use are copied to img40/ (they differ from the T4.4 ones)."""
    lang = os.path.join(src, 'lang', 'en')
    css = open(os.path.join(lang, 'compact1.css'), encoding='utf-8', errors='replace').read() + \
        open(os.path.join(lang, 'compact.css'), encoding='utf-8', errors='replace').read()
    css = re.sub(r'/\*.*?\*/', '', css, flags=re.S)
    css = re.sub(r'@(import|charset)[^;]+;', '', css, flags=re.I)
    out = serialize(blocks(css), lambda s: FRAME_40.search(s) and (not OUTSIDE.search(s) or
                                                         ACTIVATE.search(s) and 'cooperationLogo' not in s))

    def local(m):
        path = m.group(2)
        source = os.path.normpath(os.path.join(lang, path))
        below = os.path.relpath(source, os.path.join(src, 'img'))
        if below.startswith('..') or not os.path.isfile(source):
            return m.group(0)
        target = os.path.join(pack, 'img40', below)
        os.makedirs(os.path.dirname(target), exist_ok=True)
        shutil.copyfile(source, target)
        return 'url(' + m.group(1) + '../img40/' + below.replace(os.sep, '/') + m.group(1) + ')'
    components = serialize(blocks(css), lambda s: COMPONENTS_40.search(s) and not OUTSIDE.search(s))
    url = re.compile(r'url\(\s*(["\']?)([^"\')]+)\1\s*\)')
    return '/* Generated by scripts/gpack/import-t40-gpack.py from travian_Travian_4.0_41 - do not edit. */\n' + \
        url.sub(local, out) + '\n/* T4.4 pages in the 4.0 frame */' + PAGES_IN_FRAME + \
        '/* 4.0 page parts */\n' + url.sub(local, components) + '\n' + \
        '/* T4.4 pages in the 4.0 look */' + url.sub(local, LOOK_40)


def main(src: str) -> None:
    base = os.path.join(GPACK, 'TravianOld', 'mainPage')
    pack = os.path.join(GPACK, 'T40', 'mainPage')
    if os.path.exists(os.path.dirname(pack)):
        shutil.rmtree(os.path.dirname(pack))
    shutil.copytree(base, pack, ignore=shutil.ignore_patterns('t46', 'openvillage-compat.css'))
    replaced = replace_pictures(os.path.join(src, 'img'), os.path.join(pack, 'img'))
    strip_frame(os.path.join(pack, 'lang', 'en-US'))
    with open(os.path.join(pack, 'lang', 'openvillage-t40.css'), 'w', encoding='utf-8') as f:
        f.write(frame_css(src, pack))
    print(f'{pack}: {replaced} pictures from Travian 4.0')


if __name__ == '__main__':
    if len(sys.argv) != 2:
        sys.exit(__doc__)
    main(sys.argv[1])
