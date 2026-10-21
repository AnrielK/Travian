#!/usr/bin/env python3
"""Downloads the Travian Legends lobby stylesheet and the pictures / fonts the OpenVillage lobby uses into
web/public/legends/ (not part of the repository: graphics of Travian Games, fetched for the local build).
With them web/public/index.php shows the lobby in the original look, without them its own look.

Usage: python3 -I scripts/gpack/fetch-lobby-assets.py [--version 408.6]
"""
import argparse
import json
import os
import re
import sys
import urllib.request

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
BASE = 'https://lobby.legends.travian.com'
# rules whose pictures are not needed (country flags, special worlds, season passes, wonder of the world)
ICONS = {'play': 'Qe', 'duals': '$e', 'close': 'R', 'clock': 'aa', 'menu': 'Na', 'chevron': 'ja', 'plus': 'Da', 'info': '_e',
         'recommended': 'We'}
SKIP = re.compile(r'i\.flag|seasonPass|seasonalRewards|wonderOfTheWorld|background\.(?!default\b|defaultSpeed\b)')


def get(url: str) -> bytes:
    with urllib.request.urlopen(urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'}), timeout=60) as r:
        return r.read()


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument('--version', default='408.6')
    ap.add_argument('--out', default=os.path.join(ROOT, 'web', 'public', 'legends'))
    a = ap.parse_args()
    css = get(f'{BASE}/bundle/lobby.{a.version}.css').decode('utf-8')
    wanted = set()
    for m in re.finditer(r'([^{}]+)\{([^{}]*)\}', css):
        selector, body = m.group(1), m.group(2)
        for url in re.findall(r'url\((/bundle/[^)?#]+)', body):
            if '@font-face' in selector:
                if url.endswith('.woff2'):
                    wanted.add(url)
            elif not SKIP.search(selector) and not url.endswith('.svg'):
                wanted.add(url)
    os.makedirs(os.path.join(a.out, 'bundle'), exist_ok=True)
    for url in sorted(wanted):
        target = os.path.join(a.out, url.lstrip('/'))
        if not os.path.isfile(target):
            with open(target, 'wb') as fh:
                fh.write(get(BASE + url))
    # paths relative to the stylesheet; fonts only as woff2 (all WebKit / Chromium versions in use)
    css = re.sub(r'url\(/bundle/', 'url(bundle/', css)
    css = re.sub(r'src:url\(bundle/[^)]+\.eot\);', '', css)
    with open(os.path.join(a.out, 'lobby.css'), 'w', encoding='utf-8') as fh:
        fh.write(css)
    # SVG icons of the lobby (play, info, invite, close, clock, menu, chevron, plus, recommended)
    js = get(f'{BASE}/bundle/lobby.{a.version}.js').decode('utf-8')
    icons = {}
    for key, name in ICONS.items():
        start = js.find(f'const {name}=function')
        if start < 0:
            continue
        code = js[start:js.find('};const ', start)]
        box = re.search(r'viewBox:"([^"]+)"', code)
        paths = re.findall(r'"path",\{(?:className:"([^"]*)",)?d:"([^"]+)"', code)
        if box and paths:
            icons[key] = {'viewBox': box.group(1), 'paths': [[c, d] for c, d in paths]}
    with open(os.path.join(a.out, 'icons.json'), 'w', encoding='utf-8') as fh:
        json.dump(icons, fh)
    print(f'lobby assets: {len(wanted)} files in {a.out}')
    return 0


if __name__ == '__main__':
    try:
        sys.exit(main())
    except Exception as exc:  # the build continues with the lobby's own look
        print(f'lobby assets not fetched: {exc}', file=sys.stderr)
        sys.exit(0)
