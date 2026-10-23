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
# ... except the tribe and start choice (activate.php), which runs inside the 4.0 frame
ACTIVATE = re.compile(r'^\s*body\.activate\b')
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
#sideInfoCountdown .content li .infoboxDeleteButton{float:right}
#sideInfoCountdown .pager{margin-top:2px;font-size:11px}
#sideInfoCountdown .pager a{cursor:pointer;font-weight:bold;padding:0 4px}
.questMaster .master{display:block;border:0;padding:0;background-color:transparent}
.questMaster .master:hover{background-position:right top}
#t40Questmaster{display:none}
'''

# The T4.4 pages restyled the way they looked in Travian 4.0 (after a 2012 video of a 4.0 server): green buttons that
# turn grey under the mouse, the Travian logo, the white quest popup with its big picture, the building page, the white level circles
# on the resource fields and the tribe choice. Pictures as in the 4.0 stylesheet (../../img/) come from the 4.0 pack.
LOOK_40 = '''
body.layoutT40 div#header #logo{background-image:url(../../img/layout/logoSmall.png) !important;font-size:0;line-height:0}
body.layoutT40.activate div#header #logo{background-image:url(../../img/layout/logoBig.png) !important}
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
body.gidResources{background-image:url(../../img/layout/bgR.jpg)}
body.gidResources #wrapper{background-image:url(../../img/layout/bgResources-rtl.jpg)}
div.village1 #village_map .colorLayer,div.village1 #village_map .colorLayer.good,div.village1 #village_map .colorLayer.notNow,div.village1 #village_map .colorLayer.maxLevel{border:0;margin:2px 0 0 2px;width:24px;height:23px;border-radius:0;box-shadow:none;background:url(../img/g/circle.png) 0 0 no-repeat}
div.village1 #village_map .colorLayer.underConstruction{background-position:0 -33px}
div.village1 #village_map .colorLayer .labelLayer,div.village1 #village_map .colorLayer.underConstruction .labelLayer{top:0;left:0;right:auto;width:23px;height:23px;background:none;border-radius:0;line-height:23px;font-size:13px;font-family:Arial,Helvetica,Verdana,sans-serif;color:#252525}
.contentNavi.subNavi{display:flex;align-items:flex-start;width:auto;margin-right:-46px;overflow:visible}
#content>a#tabFavorButton{position:absolute}
.tabNavi .container.active .content .tabItem{color:#252525}
.contentNavi.subNavi .container{flex:none}
.subNavi .container .content .tabItem{padding:11px 6px 0}
div.village2 div#village_map div.buildingSlot div.colorLayer,div.village2 div#village_map div.buildingSlot div.colorLayer.good,div.village2 div#village_map div.buildingSlot div.colorLayer.notNow,div.village2 div#village_map div.buildingSlot div.colorLayer.maxLevel{border:0;margin:2px 0 0 2px;width:24px;height:23px;border-radius:0;box-shadow:none;background:url(../img/g/circle.png) 0 0 no-repeat}
div.village2 div#village_map div.buildingSlot div.colorLayer.underConstruction{background-position:0 -33px}
div.village2 div#village_map div.buildingSlot div.colorLayer .labelLayer,div.village2 div#village_map div.buildingSlot div.colorLayer.underConstruction .labelLayer{top:0;left:0;right:auto;width:23px;height:23px;background:none;border-radius:0;line-height:23px;font-size:13px;font-family:Arial,Helvetica,Verdana,sans-serif;color:#252525}
#content.build h1.titleInHeader .level{text-transform:capitalize}
#build .roundedCornersBox.big{background:none;border:0;box-shadow:none;padding:0;margin:15px 0 0}
#build .roundedCornersBox.big>h4{display:none}
div#build .upgradeButtonsContainer,div#build .upgradeButtonsContainer .section1,div#build .upgradeButtonsContainer .section2{display:block;border:0;background:none;text-align:left;float:none;width:auto}
div#build .culturePointsAndPopulation{text-align:left;margin:8px 0 0}
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
#side_navi ul li a{font-weight:bold;text-transform:uppercase}
.activate .activationScreen{box-sizing:border-box;background:#e4e4e4;border-radius:5px;padding:12px 20px 5px}
.activate .activationScreen #presentation{padding:15px 0 0;background:none}
.activate .activationScreen #presentation>svg{display:none}
.activate .activationScreen #presentation #tribeSelectors{display:block;position:relative;height:262px;margin-bottom:30px}
.activate .activationScreen #presentation #tribeSelectors input + .selector{position:absolute;left:0;width:80px;min-width:0;max-width:none;height:72px;margin:0;border:0;border-radius:0;box-shadow:none;background:url(../img/activate/volkIcon-ltr.png) no-repeat}
.activate .activationScreen #presentation #tribeSelectors input:checked + .selector{border:0;box-shadow:none}
.activate .activationScreen #presentation #tribeSelectors input + .selector[data-text]:before{bottom:2px;right:2px}
.activate .activationScreen #presentation #tribeSelectors input[value="3"] + .selector{top:10px;background-position:-11px -88px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"] + .selector{top:100px;background-position:-11px -388px}
.activate .activationScreen #presentation #tribeSelectors input[value="2"] + .selector{top:190px;background-position:-11px -688px}
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
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription ul:after{bottom:-134px;right:-12px;z-index:3;width:188px;height:200px;background-image:url(../../img/activate/avatars-rtl.png)}
.activate .activationScreen #presentation #tribeSelectors input[value="3"] + .selector + .tribeDescription ul:after{background-position:0 0}
.activate .activationScreen #presentation #tribeSelectors input[value="2"] + .selector + .tribeDescription ul:after{background-position:0 -200px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"] + .selector + .tribeDescription ul:after{background-position:0 -400px}
.activate .activationScreen #presentation #tribeSelectors input + .selector + .tribeDescription:after{content:'';position:absolute;left:-7px;width:12px;height:12px;box-sizing:border-box;border:solid #d6d6d6;border-width:0 0 1px 1px;background:#fff;transform:rotate(45deg)}
.activate .activationScreen #presentation #tribeSelectors input[value="3"] + .selector + .tribeDescription:after{top:40px}
.activate .activationScreen #presentation #tribeSelectors input[value="1"] + .selector + .tribeDescription:after{top:130px}
.activate .activationScreen #presentation #tribeSelectors input[value="2"] + .selector + .tribeDescription:after{top:220px}
.activate .greenbox.t40Thanks{margin:0 0 12px;padding:8px 10px;border-radius:3px;background:#fff}
.activate .greenbox.t40Thanks .greenbox-content{float:none;width:auto;padding:0;color:#4b7e0d;font-weight:normal}
.activate .activationScreen .acceptChallenge{color:#252525;text-shadow:none}
.activate .activationScreen .buttonContainer{height:60px}
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
