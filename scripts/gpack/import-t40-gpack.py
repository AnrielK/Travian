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
                      r'ingameManual|logout|anwersQuestionMark)\b|^\s*body(\.[\w-]+)*\s*$|(^|[\s,])\.stime\b|'
                      r'^\s*(body(\.[\w-]+)*\s+)?#content(\.[\w-]+)?(>h1)?\s*$|^\s*\.bubble\b')
# 4.0 rules for pages that are not part of the game (login, sign-up, error pages)
OUTSIDE = re.compile(r'\b(loader|login|logout|signup|support|activate|error_site|fatal_error|banned_cn|'
                     r'support_form|presto|ie\d?|manual|map\.fullScreen)\b|cooperationLogo|#betaBox')
# The T4.4 pages inside the 4.0 frame: where 4.0 placed them differently
PAGES_IN_FRAME = '''
div.village1 div#map_details{position:absolute;right:0;top:10px;width:245px;margin:0;float:none}
#side_info #sidebarAfterContent{float:none;position:relative;left:2px;top:0;width:auto;margin:6px 0 0;padding:0}
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
    css = re.sub(r'@(import|charset)[^;]+;', '', css)
    out = serialize(blocks(css), lambda s: FRAME_40.search(s) and not OUTSIDE.search(s))

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
    out = re.sub(r'url\(\s*(["\']?)([^"\')]+)\1\s*\)', local, out)
    return '/* Generated by scripts/gpack/import-t40-gpack.py from travian_Travian_4.0_41 - do not edit. */\n' + out + \
        '\n/* T4.4 pages in the 4.0 frame */' + PAGES_IN_FRAME


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
