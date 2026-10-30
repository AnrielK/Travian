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
import urllib.error
import urllib.request

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
BASE = 'https://lobby.legends.travian.com'
# the lobby now redirects every request to www.travian.com: then the start page bundle (fetch_startpage)
MIRRORS = [BASE]
# rules whose pictures are not needed (country flags, special worlds, season passes, wonder of the world)
ICONS = {'play': 'Qe', 'duals': '$e', 'close': 'R', 'clock': 'aa', 'menu': 'Na', 'chevron': 'ja', 'plus': 'Da', 'info': '_e',
         'recommended': 'We'}
SKIP_START = re.compile(r'i\.flag|seasonPass|seasonalRewards|wonderOfTheWorld|newYear|codexVictoria|gloryOfSparta|domainSpecial|northernLegends|reignOfFire|riseOfGovernors|shoresOfWar|tidesOfConquest|\.final\b|\.ptr\b')
SKIP = re.compile(r'i\.flag|seasonPass|seasonalRewards|wonderOfTheWorld|background\.(?!default\b|defaultSpeed\b)')


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        # an archived copy redirects to itself under another timestamp; anything else is a moved / removed file
        if 'web.archive.org/web/' in req.full_url and 'web.archive.org/web/' in newurl:
            return super().redirect_request(req, fp, code, msg, headers, newurl)
        raise urllib.error.HTTPError(req.full_url, code, f'redirected to {newurl}', headers, fp)


OPENER = urllib.request.build_opener(NoRedirect)
base = BASE


def fetch(url: str, html: bool = False) -> bytes:
    with OPENER.open(urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'}), timeout=60) as r:
        data = r.read()
    if data[:200].lstrip().lower().startswith((b'<!doctype', b'<html')) and not html and not url.endswith(('.svg', '.html')):
        raise ValueError(f'{url}: an HTML page instead of the file')
    return data


def get(path: str) -> bytes:
    return fetch(base + path)


def pick_base(version: str) -> str:
    # the first source that serves the stylesheet itself (not a redirect or an HTML page)
    for b in MIRRORS:
        try:
            css = fetch(f'{b}/bundle/lobby.{version}.css')
            if b'{' in css and b'}' in css:
                return b
        except Exception as exc:
            print(f'lobby assets: {b}: {exc}', file=sys.stderr)
    raise RuntimeError('no source serves the lobby stylesheet')


START = 'https://www.travian.com'


def fetch_startpage(out: str) -> int:
    """The lobby is now part of the start page of www.travian.com: its stylesheet (header, dialogs, buttons, fields,
    the gameworld details) with its pictures and fonts; web/public/lobby-legends.css adds what it no longer has."""
    global base
    base = START
    page = fetch(START + '/international', html=True).decode('utf-8', 'replace')
    m = re.search(r'/bundle/(startpage\.[0-9.]+\.css)', page)
    if not m:
        raise RuntimeError('no start page stylesheet found')
    css = get('/bundle/' + m.group(1)).decode('utf-8')
    wanted = set()
    for rule in re.finditer(r'([^{}]+)\{([^{}]*)\}', css):
        selector, body = rule.group(1), rule.group(2)
        for url in re.findall(r'url\((/bundle/[^)?#]+)', body):
            if '@font-face' in selector:
                if url.endswith(('.woff2', '.ttf')):
                    wanted.add(url)
            elif not SKIP_START.search(selector):
                wanted.add(url)
    os.makedirs(os.path.join(out, 'bundle'), exist_ok=True)
    for url in sorted(wanted):
        target = os.path.join(out, url.lstrip('/'))
        if not os.path.isfile(target):
            with open(target, 'wb') as fh:
                fh.write(get(url))
    # absolute paths: custom properties (--serverImage, --iconUrl) resolve them in web/public/lobby-legends.css too
    css = re.sub(r'url\(/bundle/', 'url(/legends/bundle/', css)
    css = re.sub(r'src:url\(/legends/bundle/[^)]+\.eot\);', '', css)
    # the details dialog of the start page's gameworld calendar is the details dialog of the lobby
    extra = []
    for rule in re.finditer(r'([^{}@]+)\{([^{}]*)\}', css):
        if '#calendarGameworldDetails' in rule.group(1):
            sel = ','.join(p for p in rule.group(1).split(',') if '#calendarGameworldDetails' in p)
            extra.append(sel.replace('#calendarGameworldDetails', '#gameworldDetails') + '{' + rule.group(2) + '}')
    with open(os.path.join(out, 'lobby.css'), 'w', encoding='utf-8') as fh:
        fh.write(css + '\n' + '\n'.join(extra))
    with open(os.path.join(out, 'source.txt'), 'w') as fh:
        fh.write('startpage ' + m.group(1) + '\n')
    print(f'lobby assets (start page {m.group(1)}): {len(wanted)} files in {out}')
    return 0


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument('--version', default='408.6')
    ap.add_argument('--out', default=os.path.join(ROOT, 'web', 'public', 'legends'))
    a = ap.parse_args()
    global base
    try:
        base = pick_base(a.version)
    except RuntimeError:
        return fetch_startpage(a.out)
    css = get(f'/bundle/lobby.{a.version}.css').decode('utf-8')
    wanted = set()
    for m in re.finditer(r'([^{}]+)\{([^{}]*)\}', css):
        selector, body = m.group(1), m.group(2)
        for url in re.findall(r'url\((/bundle/[^)?#]+)', body):
            if '@font-face' in selector:
                if url.endswith('.woff2'):
                    wanted.add(url)
            elif not SKIP.search(selector) and not url.endswith('.svg'):
                wanted.add(url)
    if '{' not in css:
        raise ValueError('the stylesheet is not CSS')
    os.makedirs(os.path.join(a.out, 'bundle'), exist_ok=True)
    for url in sorted(wanted):
        target = os.path.join(a.out, url.lstrip('/'))
        if not os.path.isfile(target):
            with open(target, 'wb') as fh:
                fh.write(get(url))
    # paths relative to the stylesheet; fonts only as woff2 (all WebKit / Chromium versions in use)
    css = re.sub(r'url\(/bundle/', 'url(bundle/', css)
    css = re.sub(r'src:url\(bundle/[^)]+\.eot\);', '', css)
    with open(os.path.join(a.out, 'lobby.css'), 'w', encoding='utf-8') as fh:
        fh.write(css)
    # SVG icons of the lobby (play, info, invite, close, clock, menu, chevron, plus, recommended)
    js = get(f'/bundle/lobby.{a.version}.js').decode('utf-8')
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
        # nothing half-done: without web/public/legends/lobby.css the lobby uses its own look
        try:
            out = sys.argv[sys.argv.index('--out') + 1] if '--out' in sys.argv else os.path.join(ROOT, 'web', 'public', 'legends')
            css = os.path.join(out, 'lobby.css')
            if os.path.isfile(css):
                os.remove(css)
        except Exception:
            pass
        sys.exit(0)
