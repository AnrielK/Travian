#!/usr/bin/env python3
"""Writes the templates for drawing hero equipment layers (see scripts/gpack/extract-hero-item.py):
<gender>-<pose>.png        the Legends hero figure (roman clothing, skin2) on the 875 x 1574 canvas
<gender>-<pose>-arms.png   only its arms (mask for extract-hero-item.py --erase)
Poses: default, down (fists down – one-handed weapons), up (fists raised), rightDown-leftUp (weapon + standard).

Usage: python3 -I scripts/gpack/hero-item-templates.py <out dir> [graphic pack mainPage dir]
"""
import json
import os
import sys

from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
POSES = {'default': ('Default', 'Default'), 'down': ('Down', 'Down'), 'up': ('Up', 'Up'),
         'rightDown-leftUp': ('Down', 'Up')}


def main():
    out = sys.argv[1]
    pack = sys.argv[2] if len(sys.argv) > 2 else os.path.join(ROOT, 'sections/gpack/legends655/mainPage')
    app = os.path.join(pack, 'heroAppearance')
    parts = json.load(open(os.path.join(app, 'index.json')))['parts']

    def part(key):
        x, y, _, _ = parts[key]
        canvas = Image.new('RGBA', (875, 1574), (0, 0, 0, 0))
        canvas.alpha_composite(Image.open(os.path.join(app, 'parts', key + '.png')).convert('RGBA'), (x, y))
        return canvas

    os.makedirs(out, exist_ok=True)
    for g in ('male', 'female'):
        for name, (right, left) in POSES.items():
            fig = part(f'{g}/skinColor/skin2/base/base-skin2')
            fig.alpha_composite(part(f'{g}/tribeColor/roman/body/body-roman'))
            arms = part(f'{g}/skinColor/skin2/leftArm/leftArm{left}-skin2')
            arms.alpha_composite(part(f'{g}/skinColor/skin2/rightArm/rightArm{right}-skin2'))
            fig.alpha_composite(arms)
            fig.alpha_composite(part(f'{g}/skinColor/skin2/jaw/jaw1-skin2'))
            fig.save(os.path.join(out, f'{g}-{name}.png'), optimize=True)
            arms.save(os.path.join(out, f'{g}-{name}-arms.png'), optimize=True)
    print(f'templates written to {out}')


if __name__ == '__main__':
    main()
