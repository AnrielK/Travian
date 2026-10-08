#!/usr/bin/env python3
"""
Builds an OpenVillage graphic pack from a Travian Legends gpack folder (img_ltr, img_rtl, css_ltr/css_rtl
imports_compressed.css, flags) – e.g. the extracted "gpack/655.3".

    scripts/gpack/import-legends-gpack.py <legends gpack dir> <pack name> [--template 29c89d54] [--no-rtl]

The result (sections/gpack/<pack name>/mainPage) uses the Legends page layout (layout/LayoutT46.php):
  css/default_compressed*.css, custom_…     base styles of the template pack (old class names), loaded first
  css/legends_ltr.css, css/legends_rtl.css  the Legends stylesheets as distributed, loaded on top
  css_ltr/, css_rtl/                          T4.4 widget/page styles the game still needs (from the template pack)
  css/openvillage-t46.css                     OpenVillage adapter, image paths remapped to this pack
  css/openvillage-t46-tribes.css              tribe building images for the game's <img class="building gN">
  lang/<locale>/compact.css …                 entry points loaded by layout/head.php
Every image the copied CSS refers to and the pack lacks is copied from the template pack (or a17a8f72), so the
pack is self-contained.
"""
import argparse, hashlib, os, re, shutil, sys

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
GPACK = os.path.join(ROOT, 'sections', 'gpack')
URL = re.compile(r"""url\(\s*(['"]?)([^'")]+)\1\s*\)""")


def md5(path):
    with open(path, 'rb') as f:
        return hashlib.md5(f.read()).hexdigest()


def png_size(path):
    with open(path, 'rb') as f:
        head = f.read(24)
    return int.from_bytes(head[16:20], 'big'), int.from_bytes(head[20:24], 'big')


def copytree(src, dst):
    if os.path.isdir(src):
        shutil.copytree(src, dst, dirs_exist_ok=True)


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('source')
    ap.add_argument('name')
    ap.add_argument('--template', default='29c89d54')
    ap.add_argument('--hero-appearance', metavar='DIR',
                    help='extracted "Hero Appearance" add-on (parts/, parts-index.json): enables the Legends hero editor')
    ap.add_argument('--hero-atlases', metavar='DIR',
                    help='extra hero texture atlases (e.g. tribe clothing *-tribeColor-*.json/.png) added to the parts')
    ap.add_argument('--hero-equipment', metavar='DIR', nargs='?', const=os.path.join(ROOT, 'scripts', 'gpack', 'hero-equipment'),
                    help='hero equipment layers <gender>/equipment/<slot>/<name>.png (default scripts/gpack/hero-equipment)')
    ap.add_argument('--no-rtl', action='store_true',
                    help='leave out right-to-left assets and locales (Arabic, Persian) – about half the size')
    a = ap.parse_args()
    if not re.fullmatch(r'[A-Za-z0-9]+', a.name):
        sys.exit('pack name must be alphanumeric (it is a URL path segment)')
    src = os.path.abspath(a.source)
    tpl = os.path.join(GPACK, a.template, 'mainPage')
    old = os.path.join(GPACK, 'a17a8f72', 'mainPage')
    dst = os.path.join(GPACK, a.name, 'mainPage')
    # Always build from scratch: the adapter remap below depends on which pictures the pack has itself.
    if os.path.isdir(dst):
        shutil.rmtree(dst)
    os.makedirs(os.path.join(dst, 'css'))

    # 1. Legends assets and stylesheets.
    sides = ('ltr',) if a.no_rtl else ('ltr', 'rtl')
    for d in tuple(f'img_{side}' for side in sides) + ('flags',):
        copytree(os.path.join(src, d), os.path.join(dst, d))
    for d in sides:
        shutil.copy2(os.path.join(src, f'css_{d}', 'imports_compressed.css'), os.path.join(dst, 'css', f'legends_{d}.css'))

    # 2. T4.4 widget styles, fonts and entry points from the template pack. The template's own base styles
    #    stay underneath the Legends stylesheet: they cover the markup of this game that the newer Legends
    #    release no longer styles (old class names), the Legends rules win wherever both match.
    for d in tuple(f'css_{side}' for side in sides) + ('fonts',):
        copytree(os.path.join(tpl, d), os.path.join(dst, d))
    for fn in os.listdir(os.path.join(tpl, 'css')):
        if fn.startswith(('default_compressed', 'custom_compressed')):
            shutil.copy2(os.path.join(tpl, 'css', fn), os.path.join(dst, 'css', fn))
    copytree(os.path.join(tpl, 'lang'), os.path.join(dst, 'lang'))
    for locale in os.listdir(os.path.join(dst, 'lang')):
        f = os.path.join(dst, 'lang', locale, 'compact.css')
        if not os.path.isfile(f):
            continue
        s = open(f).read()
        rtl = '@import url("../../css_rtl/' in s
        if rtl and a.no_rtl:
            shutil.rmtree(os.path.dirname(f))
            continue
        s = re.sub(r'(@import url\("\.\./\.\./css/custom_compressed\.css"\);)(?!@import url\("\.\./\.\./css/legends_)',
                   lambda m: m.group(1) + f'@import url("../../css/legends_{"rtl" if rtl else "ltr"}.css");', s)
        open(f, 'w').write(s)

    legends = open(os.path.join(dst, 'css', 'legends_ltr.css'), errors='replace').read()

    # 3. Tribe building images for the game's markup (no tribe class on the <img>).
    out = ['@charset "UTF-8";',
           '/* Generated by scripts/gpack/import-legends-gpack.py: building pictures for <img class="building gN"> (the tribe',
           '   comes from the body class t46-<tribe>, see LayoutT46.php); level-0 construction sites use the same picture. */']
    seen = set()
    for g, tribe, kind, body in re.findall(r'img\.(g\d+)\.(roman|teuton|gaul|egyptian|hun)(\.big|:not\(\.big\))\{([^}]*)\}', legends):
        m = re.search(r'background-image:url\([^)]*\)', body)
        if not m:
            continue
        for sel in ([f'body.t46-{tribe} img.building.{g}.big'] if kind == '.big'
                    else [f'body.t46-{tribe} img.building.{g}:not(.big)', f'body.t46-{tribe} img.building.{g}b']):
            if sel not in seen:
                seen.add(sel)
                out.append(sel + '{' + m.group(0) + '}')
    open(os.path.join(dst, 'css', 'openvillage-t46-tribes.css'), 'w').write('\n'.join(out) + '\n')

    # 4. Adapter: same rules as the template, image paths remapped to this pack where the picture exists here.
    by_hash, by_name = {}, {}
    for r, _, fs in os.walk(os.path.join(dst, 'img_ltr')):
        for fn in fs:
            p = os.path.join(r, fn)
            rel = '../' + os.path.relpath(p, dst).replace(os.sep, '/')
            by_hash.setdefault(md5(p), rel)
            by_name.setdefault(fn, []).append(rel)
    adapter = open(os.path.join(tpl, 'css', 'openvillage-t46.css')).read()
    remapped = kept = 0

    def remap(m):
        nonlocal remapped, kept
        url = m.group(2)
        if url.startswith(('data:', '#', 'http')) or not url.startswith('../img_'):
            return m.group(0)
        if os.path.isfile(os.path.normpath(os.path.join(dst, 'css', url))):
            return m.group(0)
        tpl_file = os.path.normpath(os.path.join(tpl, 'css', url))
        target = None
        if os.path.isfile(tpl_file):
            target = by_hash.get(md5(tpl_file))
            if not target and len(by_name.get(os.path.basename(url), [])) == 1:
                target = by_name[os.path.basename(url)][0]
        if target:
            remapped += 1
            return f"url('{target}')"
        kept += 1
        return m.group(0)

    adapter = URL.sub(remap, adapter)
    adapter = adapter.replace('OpenVillage adapter for graphic pack 29c89d54',
                              f'OpenVillage adapter for graphic pack {a.name} (generated from {a.template})')
    open(os.path.join(dst, 'css', 'openvillage-t46.css'), 'w').write(adapter)

    # 5. Make the pack self-contained: copy every image a stylesheet refers to and the pack lacks.
    copied = missing = 0
    for r, _, fs in os.walk(dst):
        for fn in fs:
            if not fn.endswith('.css') or fn.startswith('legends_'):
                continue
            css = os.path.join(r, fn)
            for _, url in URL.findall(open(css, errors='replace').read()):
                if url.startswith(('data:', '#', 'http')):
                    continue
                url = url.split('?')[0].split('#')[0]
                target = os.path.normpath(os.path.join(r, url))
                if not target.startswith(dst) or os.path.isfile(target):
                    continue
                rel = os.path.relpath(target, dst)
                for base in (tpl, old):
                    if os.path.isfile(os.path.join(base, rel)):
                        os.makedirs(os.path.dirname(target), exist_ok=True)
                        shutil.copy2(os.path.join(base, rel), target)
                        copied += 1
                        break
                else:
                    missing += 1
    # 6. Pictures the PHP templates load directly from the pack (not through CSS).
    for rel in ('img_ltr/hud/topBar/header/hero', 'img_ltr/report/reportAction.png',
                'img_ltr/f/edit.gif', 'img_ltr/misc/flags/country_sprite.png',
                'img_ltr/round/button/content/goldInner-ltr.png'):
        # The T4.4 packs keep some of these under img/ (e.g. report/reportAction.png) instead of img_ltr/.
        t44 = os.path.join(GPACK, 'd11cb434', 'mainPage')
        for base, src_rel in ((tpl, rel), (old, rel), (t44, rel.replace('img_ltr/', 'img/', 1))):
            src = os.path.join(base, src_rel)
            target = os.path.join(dst, rel)
            if os.path.isdir(src):
                for fn in os.listdir(src):
                    if not os.path.exists(os.path.join(target, fn)):
                        os.makedirs(target, exist_ok=True)
                        shutil.copy2(os.path.join(src, fn), os.path.join(target, fn))
                        copied += 1
                break
            if os.path.isfile(src):
                if not os.path.exists(target):
                    os.makedirs(os.path.dirname(target), exist_ok=True)
                    shutil.copy2(src, target)
                    copied += 1
                break
    # 7. Hero appearance parts (Legends hero editor): one PNG per part + heroAppearance/index.json with the
    #    position of every part on the 875x1574 hero canvas (the game composes heroes from it, see
    #    Game/Hero/HeroAppearanceV2.php).
    if a.hero_appearance:
        import json
        app = os.path.abspath(a.hero_appearance)
        index = json.load(open(os.path.join(app, 'parts-index.json')))
        parts = {}
        size = None
        for entry in index:
            key = entry['key']
            if not re.fullmatch(r'[A-Za-z0-9/_-]+', key):
                continue
            src_png = os.path.join(app, entry['file'])
            if not os.path.isfile(src_png):
                continue
            target = os.path.join(dst, 'heroAppearance', 'parts', key + '.png')
            os.makedirs(os.path.dirname(target), exist_ok=True)
            shutil.copy2(src_png, target)
            w, h = png_size(target)
            parts[key] = [entry['offset']['x'], entry['offset']['y'], w, h]
            size = [entry['sourceSize']['w'], entry['sourceSize']['h']]
        with open(os.path.join(dst, 'heroAppearance', 'index.json'), 'w') as f:
            json.dump({'size': size, 'parts': parts}, f, separators=(',', ':'), sort_keys=True)
        print(f'{a.name}: hero appearance {len(parts)} parts')
        if a.hero_atlases:
            sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
            from importlib import import_module
            added = import_module('add-hero-atlases').add_atlases(os.path.abspath(a.hero_atlases), dst)
            print(f'{a.name}: hero appearance +{added} atlas parts')
        if a.hero_equipment:
            sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
            from importlib import import_module
            added = import_module('add-hero-equipment').add_equipment(os.path.abspath(a.hero_equipment), dst)
            print(f'{a.name}: hero appearance +{added} equipment layers')
    print(f'{a.name}: tribes {len(seen)} rules, adapter remapped {remapped} / kept {kept}, '
          f'copied {copied} images from {a.template}/a17a8f72, unresolved {missing}')


if __name__ == '__main__':
    main()
