#!/usr/bin/env python3
"""Builds mainPage/lang/openvillage-compat.css for the Travian T4.4 graphic packs (gpack.php 'isNew' => false).

The game renders T4.6 markup: icons are <i class="r1">, <i class="tribe1_medium">, layout buttons hold an <i>,
and dorf2 places buildings in <div class="buildingSlot aNN"> (body class buildingsV1). The T4.4 stylesheets
style <img> icons and an image-map village centre instead, so without this file the buttons and bars have no
icons and every building site piles up in the corner of the village. The generated file
  1. repeats every T4.4 rule whose subject is an <img> for the matching <i> (icons, sprites, sizes),
  2. moves the T4.4 building level circles from #levels into the building slots,
  3. adds the buildingsV1 slot positions of a17a8f72 (same 120 x 120 building images as T4.4),
  4. adds a few rules for T4.6 elements whose T4.4 class names changed.
The pack's own building pictures stay in use; the a17a8f72 pictures the file needs are copied into the pack
(mainPage/t46/), so no pack depends on another. head.php loads the file for every pack with 'isNew' => false.

Usage: python3 -I scripts/gpack/make-classic-compat.py [pack ...]   (default: every T4.4 pack present)
"""
import os
import re
import shutil
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
GPACK = os.path.join(ROOT, 'sections', 'gpack')
PACKS = ['TravianOld', '68597666', 'd11cb434', 'T40']
MODERN_PREFIX = 'a17a8f72/mainPage/'
MODERN_CSS = os.path.join(GPACK, 'a17a8f72', 'mainPage', 'lang', 'en-US', 'compact.css')
# <body> classes of the T4.6 layout (Layout.php): not a reason to take a rule from a17a8f72
# Packs built from another T4.4 pack without its page frame rules (T40: scripts/gpack/import-t40-gpack.py);
# the frame of the base pack still counts as known, so no a17a8f72 frame rule comes back.
BASE_PACKS = {'T40': 'TravianOld'}
BODY_CLASSES = {'.v35', '.webkit', '.chrome', '.ltr', '.rtl', '.buildingsV1', '.buildingsV2', '.buildingsV3',
                '.season-winter', '.season-spring', '.season-summer', '.season-fall', '.openvillage'}


def read_css(path: str, seen=None) -> str:
    """The stylesheet with its relative @imports inlined and every url() rewritten to "@/<path below gpack>"."""
    seen = seen if seen is not None else set()
    if path in seen or not os.path.isfile(path):
        return ''
    seen.add(path)
    css = open(path, encoding='utf-8', errors='replace').read()
    css = re.sub(r'/\*.*?\*/', '', css, flags=re.S)
    base = os.path.dirname(path)

    def inline(m):
        return read_css(os.path.join(base, m.group(1)), seen)
    css = re.sub(r'@import\s+(?:url\()?["\']?([^"\')]+)["\']?\)?\s*;?', inline, css)

    def absolute(m):
        url = m.group(2)
        if re.match(r'(data:|https?:|/|@/|#)', url):
            return m.group(0)
        target = os.path.relpath(os.path.normpath(os.path.join(base, url)), GPACK)
        return 'url(' + m.group(1) + '@/' + target.replace(os.sep, '/') + m.group(1) + ')'
    return re.sub(r'url\(\s*(["\']?)([^"\')]+)\1\s*\)', absolute, css)


def rules(css: str):
    """(prelude, body) pairs; @media bodies are returned as nested lists."""
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
        # statements such as @charset end at ';' before the next block
        prelude = prelude.rsplit(';', 1)[-1].strip() if prelude.startswith('@') or ';' in prelude else prelude
        if prelude.startswith('@media'):
            out.append((prelude, rules(body)))
        elif not prelude.startswith('@'):
            out.append((prelude, body.strip()))
        i = k
    return out


SUBJECT_IMG = re.compile(r'(^|[\s>+~])img(?=[.#:\[]|$)')


def img_to_i(selector: str):
    """The selector for <i> when its subject (last compound) is an <img>, else None."""
    s = selector.strip()
    if not s or re.match(r'\.ie\d', s):
        return None
    last = re.split(r'[\s>+~]+', s)[-1]
    if not re.match(r'img(?=[.#:\[]|$)', last):
        return None
    head = s[:len(s) - len(last)]
    return head + 'i' + last[3:]


def levels_to_slots(selector: str):
    s = selector.strip()
    if 'div#levels' not in s or not re.search(r'colorLayer|labelLayer', s) or re.match(r'\.ie\d', s):
        return None
    return s.replace('div#levels div.', 'div.buildingSlot div.').replace('div#levels ', 'div.buildingSlot ')


def convert(items, fn, fix_body):
    out = []
    for prelude, body in items:
        if isinstance(body, list):
            inner = convert(body, fn, fix_body)
            if inner:
                out.append(prelude + '{' + ''.join(inner) + '}')
            continue
        sels = [x for x in (fn(p) for p in prelude.split(',')) if x]
        if sels:
            out.append(','.join(sels) + '{' + fix_body(body) + '}')
    return out


def tokens(selector: str) -> set:
    return set(re.findall(r'[.#][A-Za-z_-][\w-]*', re.sub(r'\[[^\]]*\]|\([^)]*\)', '', selector)))


def selectors_of(items):
    for prelude, body in items:
        if isinstance(body, list):
            yield from selectors_of(body)
        else:
            yield from prelude.split(',')


def build(pack: str) -> str:
    items = rules(read_css(os.path.join(GPACK, pack, 'mainPage', 'lang', 'en-US', 'compact.css')))
    keep = lambda body: body

    icons = convert(items, img_to_i, keep)
    # <i> is inline: give the converted icons a box so width/height apply, as they do on <img>
    boxed = [r.split('{', 1)[0] for r in icons if re.search(r'(^|[{;])\s*(width|height)\s*:', r.split('{', 1)[1])]
    preamble = (','.join(boxed) + '{display:inline-block}') if boxed else ''

    levels = convert(items, levels_to_slots, keep)

    # a17a8f72 rules for T4.6 elements the T4.4 stylesheet has never heard of (a selector with a class or id
    # unknown to it); everything the T4.4 stylesheet styles itself keeps its classic look
    known = set(BODY_CLASSES)
    for sel in selectors_of(items):
        known |= tokens(sel)
    if pack in BASE_PACKS:
        base = os.path.join(GPACK, BASE_PACKS[pack], 'mainPage', 'lang', 'en-US', 'compact.css')
        for sel in selectors_of(rules(read_css(base))):
            known |= tokens(sel)
    # the page frame (header, navigation, stock bar, sidebars) keeps the T4.4 layout entirely
    frame = re.compile(r'sidebar|#header|navigation|stockBar|layoutButton|#footer|outOfGame|#logo|#background|'
                       r'bodyWrapper|snowAnimation|^\s*\.ie\d', re.I)
    unknown = lambda sel: sel.strip() if tokens(sel) - known and not frame.search(sel) else None
    modern = convert(rules(read_css(MODERN_CSS)), unknown, keep)

    css = '\n'.join([
        '/* Generated by scripts/gpack/make-classic-compat.py from ' + pack + ' and a17a8f72 - do not edit. */',
        '/* 1. T4.6 elements unknown to T4.4 (a17a8f72 styles) */',
        *modern,
        '/* 2. T4.4 <img> icon rules for the <i> icons of the T4.6 markup */',
        preamble,
        *icons,
        '/* 3. building level circles inside the village centre building slots */',
        *levels,
        '.buildingsV1 #village_map{width:628px;height:534px;position:absolute;z-index:1}',
        # the empty rally point site is the T4.4 picture: T4.4 places it like the rally point (a17a8f72 has its
        # own, taller picture 15px higher, which put the T4.4 one across the village square)
        '.buildingsV1 .buildingSlot.a39.g0{left:356px;top:171px}',
        # its hover outline is the a17a8f72 shape: light up the T4.4 picture itself instead
        '.buildingsV1 .buildingSlot.a39.g0 svg.buildingShape path{fill:transparent !important}',
        '.buildingsV1 .buildingSlot.a39.g0:has(.clickShape:hover) img.g16e{filter:brightness(1.25)}',
        '.buildingsV1 .buildingSlot.a39.g0:has(.clickShape:active) img.g16e{filter:brightness(1.45)}',
        # T4.4 places the rally point / Wonder of the World on the village map itself; here the slot does it
        '.buildingsV1 #village_map .buildingSlot img.building{left:0;top:0;width:120px;height:120px}',
        '.buildingsV1 #village_map .buildingSlot img.g16,.buildingsV1 #village_map .buildingSlot img.g16b,'
        '.buildingsV1 #village_map .buildingSlot img.g16e{height:160px}',
        '.buildingsV1 #village_map .buildingSlot img.g16_ww,.buildingsV1 #village_map .buildingSlot img.g16b_ww,'
        '.buildingsV1 #village_map .buildingSlot img.g16e_ww{width:83px;height:133px}',
        '.buildingsV1 #village_map .buildingSlot img.g40{width:298px;height:318px}',
        '.buildingsV1 #village_map .buildingSlot .colorLayer{position:absolute}',
        '/* 4. T4.6 markup without a T4.4 counterpart */',
        # the resource icons lost their T4.4 class "res"
        'ul#stockBar .stockBarButton .middle>i{position:absolute;left:3px;top:4px}',
        '',
    ])
    # The file lives in <pack>/mainPage/lang/. Pictures of a17a8f72 are copied into the pack (mainPage/t46/), so
    # every pack works on its own (iOS app, trimmed distributions); pictures of the pack itself stay where they are.
    own = os.path.join(GPACK, pack, 'mainPage')
    shutil.rmtree(os.path.join(own, 't46'), ignore_errors=True)

    def local(m):
        quote, path = m.group(1), m.group(2)
        clean = re.split(r'[?#]', path)[0]
        if clean.startswith(MODERN_PREFIX):
            source = os.path.join(GPACK, clean)
            if not os.path.isfile(source):
                return 'url(' + quote + '../../../' + path + quote + ')'
            below = clean[len(MODERN_PREFIX):]
            target = os.path.join(own, 't46', below)
            os.makedirs(os.path.dirname(target), exist_ok=True)
            shutil.copyfile(source, target)
            return 'url(' + quote + '../t46/' + below + path[len(clean):] + quote + ')'
        if clean.startswith(pack + '/mainPage/'):
            return 'url(' + quote + '../' + path[len(pack + '/mainPage/'):] + quote + ')'
        return 'url(' + quote + '../../../' + path + quote + ')'
    return re.sub(r'url\((["\']?)@/([^"\')]+)\1\)', local, css)


def main(argv):
    packs = argv or [p for p in PACKS if os.path.isdir(os.path.join(GPACK, p, 'mainPage'))]
    for pack in packs:
        target = os.path.join(GPACK, pack, 'mainPage', 'lang', 'openvillage-compat.css')
        css = build(pack)
        with open(target, 'w', encoding='utf-8') as f:
            f.write(css)
        print(f'{target}: {len(css)} bytes')


if __name__ == '__main__':
    main(sys.argv[1:])
