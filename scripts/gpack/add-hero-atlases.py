#!/usr/bin/env python3
"""Adds Legends hero texture atlases (TexturePacker JSON + PNG, e.g. male-tribeColor-roman.json/.png) to a
graphic pack's hero appearance: every frame becomes heroAppearance/parts/<frame key>.png and an index.json entry
[x, y, w, h] on the hero canvas, next to the parts imported by import-legends-gpack.py --hero-appearance.

Usage: python3 -I scripts/gpack/add-hero-atlases.py <dir with *.json + *.png> [graphic pack mainPage dir]
"""
import glob
import json
import os
import re
import sys

from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


def add_atlases(src: str, main_page: str) -> int:
    index_file = os.path.join(main_page, 'heroAppearance', 'index.json')
    index = json.load(open(index_file))
    added = 0
    for atlas in sorted(glob.glob(os.path.join(src, '*.json'))):
        data = json.load(open(atlas))
        sheet = Image.open(os.path.join(src, data['meta']['image'])).convert('RGBA')
        for key, frame in data['frames'].items():
            if not re.fullmatch(r'[A-Za-z0-9/_-]+', key):
                continue
            f = frame['frame']
            w, h = (f['h'], f['w']) if frame.get('rotated') else (f['w'], f['h'])
            part = sheet.crop((f['x'], f['y'], f['x'] + w, f['y'] + h))
            if frame.get('rotated'):  # TexturePacker stores rotated frames turned 90° clockwise
                part = part.transpose(Image.Transpose.ROTATE_90)
            target = os.path.join(main_page, 'heroAppearance', 'parts', key + '.png')
            os.makedirs(os.path.dirname(target), exist_ok=True)
            part.save(target, optimize=True)
            offset = frame['spriteSourceSize']
            index['parts'][key] = [offset['x'], offset['y'], part.width, part.height]
            added += 1
    with open(index_file, 'w') as fh:
        json.dump(index, fh, separators=(',', ':'), sort_keys=True)
    return added


if __name__ == '__main__':
    pack = sys.argv[2] if len(sys.argv) > 2 else os.path.join(ROOT, 'sections/gpack/legends655/mainPage')
    print(f'{add_atlases(sys.argv[1], pack)} hero parts added')
