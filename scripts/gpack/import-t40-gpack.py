#!/usr/bin/env python3
"""Builds the Travian 4.0 graphic pack (sections/gpack/T40) from travian_Travian_4.0_41.

The 4.0 pack only exists as a right-to-left pack, and its stylesheet styles the Travian 4.0 page, which the
game no longer renders. T4.4 Classic (TravianOld) grew from it and styles the current page, so T40 is a copy
of TravianOld whose pictures are replaced by the 4.0 ones wherever 4.0 has the same picture in the same size:
buildings, village backgrounds, units, icons, … A right-to-left picture is the mirror image of the
left-to-right one, so mirroring the 4.0 "-rtl" pictures gives the 4.0 "-ltr" pictures.
openvillage-compat.css is generated afterwards (scripts/gpack/make-classic-compat.py T40).

Usage: python3 -I scripts/gpack/import-t40-gpack.py <unzipped gpack/travian_Travian_4.0_41>
"""
import os
import re
import shutil
import sys

from PIL import Image, ImageOps

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
GPACK = os.path.join(ROOT, 'sections', 'gpack')
# The 4.0 resource fields (g/f*) and their backgrounds (layout/bgResources*) carry the 4.0 page background
# around the fields, which does not line up with the current page: the T4.4 ones (soft edges) stay.
KEEP = re.compile(r'(g/f\d+|layout/bgResources[^/]*)-(ltr|rtl)\.')


def mirrored(source: str, target: str) -> bool:
    image = Image.open(source)
    if getattr(image, 'n_frames', 1) > 1:   # animations stay as in T4.4
        return False
    info = dict(image.info)
    flipped = ImageOps.mirror(image)
    options = {k: info[k] for k in ('transparency', 'quality') if k in info}
    if target.lower().endswith(('.jpg', '.jpeg')):
        flipped = flipped.convert('RGB')
        options = {'quality': 92}
    flipped.save(target, **options)
    return True


def main(src: str) -> None:
    src_img = os.path.join(src, 'img')
    base = os.path.join(GPACK, 'TravianOld', 'mainPage')
    pack = os.path.join(GPACK, 'T40', 'mainPage')
    if os.path.exists(os.path.dirname(pack)):
        shutil.rmtree(os.path.dirname(pack))
    shutil.copytree(base, pack, ignore=shutil.ignore_patterns('t46', 'openvillage-compat.css'))
    replaced = 0
    for folder, _, files in os.walk(src_img):
        for name in files:
            source = os.path.join(folder, name)
            rel = os.path.relpath(source, src_img)
            target = os.path.join(pack, 'img', rel)
            if not os.path.isfile(target) or KEEP.match(rel.replace(os.sep, '/')):
                continue
            new, old = open(source, 'rb').read(), open(target, 'rb').read()
            if new == old:
                continue
            try:
                if Image.open(source).size != Image.open(target).size:
                    continue   # another sprite layout: the T4.4 stylesheet would cut it wrong
            except OSError:
                continue
            shutil.copyfile(source, target)
            replaced += 1
            if '-rtl.' in name:
                ltr = os.path.join(pack, 'img', rel.replace('-rtl.', '-ltr.'))
                if os.path.isfile(ltr) and Image.open(ltr).size == Image.open(source).size and mirrored(source, ltr):
                    replaced += 1
    print(f'{pack}: {replaced} pictures from Travian 4.0')


if __name__ == '__main__':
    if len(sys.argv) != 2:
        sys.exit(__doc__)
    main(sys.argv[1])
