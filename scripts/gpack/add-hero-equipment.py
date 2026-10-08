#!/usr/bin/env python3
"""Adds hero equipment layers to a graphic pack's hero appearance (Game/Hero/HeroAppearanceV2.php draws them).

Source: <dir>/<male|female>/equipment/<slot>/<name>.png, each a transparent PNG on the full 875 x 1574 hero
canvas (see scripts/gpack/hero-item-templates.py and extract-hero-item.py). Every layer is cropped to its
content and stored as heroAppearance/parts/<gender>/equipment/<slot>/<name>.png with its position in index.json.

Usage: python3 -I scripts/gpack/add-hero-equipment.py [dir (default scripts/gpack/hero-equipment)] [mainPage dir]
"""
import glob
import json
import os
import re
import sys

from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


def add_equipment(src: str, main_page: str) -> int:
    index_file = os.path.join(main_page, 'heroAppearance', 'index.json')
    index = json.load(open(index_file))
    size = tuple(index['size'])
    added = 0
    for path in sorted(glob.glob(os.path.join(src, '*', 'equipment', '*', '*.png'))):
        key = os.path.relpath(path, src)[:-4].replace(os.sep, '/')
        if not re.fullmatch(r'(male|female)/equipment/[A-Za-z]+/[A-Za-z]+\d+', key):
            print(f'skipped {key}: expected <gender>/equipment/<slot>/<name><number>', file=sys.stderr)
            continue
        image = Image.open(path).convert('RGBA')
        if image.size != size:
            print(f'skipped {key}: canvas {image.size}, expected {size}', file=sys.stderr)
            continue
        box = image.getchannel('A').getbbox()
        if not box:
            continue
        target = os.path.join(main_page, 'heroAppearance', 'parts', key + '.png')
        os.makedirs(os.path.dirname(target), exist_ok=True)
        image.crop(box).save(target, optimize=True)
        index['parts'][key] = [box[0], box[1], box[2] - box[0], box[3] - box[1]]
        added += 1
    with open(index_file, 'w') as fh:
        json.dump(index, fh, separators=(',', ':'), sort_keys=True)
    return added


if __name__ == '__main__':
    src = sys.argv[1] if len(sys.argv) > 1 else os.path.join(ROOT, 'scripts/gpack/hero-equipment')
    pack = sys.argv[2] if len(sys.argv) > 2 else os.path.join(ROOT, 'sections/gpack/legends655/mainPage')
    print(f'{add_equipment(src, pack)} equipment layers added')
