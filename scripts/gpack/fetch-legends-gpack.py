#!/usr/bin/env python3
"""Downloads a Travian Legends gpack release from the CDN into a folder laid out like an extracted gpack
(css_ltr/ css_rtl/ imports_compressed.css, img_ltr/, img_rtl/, flags/), the input of import-legends-gpack.py.

The CDN has no directory listing: the file list is every url() of the two stylesheets plus every picture of an
existing pack (--like, default legends655), so pictures the game loads outside the CSS come along too; those the
release no longer has are copied from that pack.

Usage: python3 -I scripts/gpack/fetch-legends-gpack.py <output dir> [--version 692.2] [--like legends655] [--no-rtl]
"""
import argparse
import os
import re
import shutil
import sys
import urllib.error
import urllib.request
from concurrent.futures import ThreadPoolExecutor

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
GPACK = os.path.join(ROOT, 'sections', 'gpack')
CDN = 'https://cdn.legends.travian.com/gpack/'
URL = re.compile(r"""url\(\s*(['"]?)([^'")]+)\1\s*\)""")


def fetch(url: str) -> bytes:
    with urllib.request.urlopen(urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'}), timeout=60) as r:
        return r.read()


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('output')
    ap.add_argument('--version', default='692.2')
    ap.add_argument('--like', default='legends655', help='pack whose pictures are fetched as well (default legends655)')
    ap.add_argument('--no-rtl', action='store_true')
    a = ap.parse_args()
    if not re.fullmatch(r'\d+(\.\d+)*', a.version):
        sys.exit('version must look like 692.2')
    base = CDN + a.version + '/'
    out = os.path.abspath(a.output)
    sides = ('ltr',) if a.no_rtl else ('ltr', 'rtl')

    wanted = set()
    for side in sides:
        css = fetch(f'{base}css_{side}/imports_compressed.css')
        os.makedirs(os.path.join(out, f'css_{side}'), exist_ok=True)
        open(os.path.join(out, f'css_{side}', 'imports_compressed.css'), 'wb').write(css)
        for _, url in URL.findall(css.decode('utf-8', 'replace')):
            url = url.split('?')[0].split('#')[0]
            if url.startswith(('data:', 'http', '//')) or not url:
                continue
            rel = os.path.normpath(os.path.join(f'css_{side}', url)).replace(os.sep, '/')
            if not rel.startswith('..'):
                wanted.add(rel)
    like = os.path.join(GPACK, a.like, 'mainPage')
    for d in tuple(f'img_{side}' for side in sides) + ('flags',):
        for r, _, fs in os.walk(os.path.join(like, d)):
            for fn in fs:
                wanted.add(os.path.relpath(os.path.join(r, fn), like).replace(os.sep, '/'))

    def get(rel):
        target = os.path.join(out, rel)
        if os.path.isfile(target):
            return 'have'
        try:
            data = fetch(base + rel)
        except (urllib.error.URLError, TimeoutError):
            return 'missing'
        os.makedirs(os.path.dirname(target), exist_ok=True)
        open(target, 'wb').write(data)
        return 'fetched'

    with ThreadPoolExecutor(16) as pool:
        results = list(pool.map(get, sorted(wanted)))
    # Pictures the release no longer has stay as they are in the --like pack: the PHP templates may load them directly.
    missing, kept = [], 0
    for rel, res in zip(sorted(wanted), results):
        if res != 'missing':
            continue
        old = os.path.join(like, rel)
        if os.path.isfile(old):
            os.makedirs(os.path.dirname(os.path.join(out, rel)), exist_ok=True)
            shutil.copy2(old, os.path.join(out, rel))
            kept += 1
        else:
            missing.append(rel)
    with open(os.path.join(out, 'missing.txt'), 'w', newline='\n') as f:
        f.write('\n'.join(missing) + '\n')
    print(f'{a.version}: {results.count("fetched")} fetched, {results.count("have")} already there, '
          f'{kept} kept from {a.like}, {len(missing)} not found anywhere (missing.txt)')


if __name__ == '__main__':
    main()
