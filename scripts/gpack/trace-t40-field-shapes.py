#!/usr/bin/env python3
"""Trace the outline of every resource field on the Travian 4.0 village pictures (T40 pack, img/g/fN-ltr.jpg).

The 4.0 pictures have no shapes for the resource fields and paint neighbouring fields of one resource as one
patch (four crop fields in one clover, a forest of two fields). This script finds the patches by colour (forest,
clay pit, mountains, crop) and gives every field the part of its patch that is nearest to it along the patch,
so the hover of dorf1 (resources/Templates/dorf1/main.php) can light up the field under the pointer the way the
Legends look does. It writes main_script/include/resources/View/t40FieldShapes.json:

    {"3": {"1": "M...z", ..., "18": "M...z"}, ...}

layout (village field type 1-12) -> field 1-18 -> SVG path in the coordinates of #village_map (500 x 370, the
picture sits centred at its bottom). The pictures for right-to-left languages are mirror images; the template
mirrors the paths for them.

Usage: python3 -I scripts/gpack/trace-t40-field-shapes.py [T40 mainPage dir]
"""
import json
import os
import sys
from collections import deque

import cv2
import numpy as np

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
PACK = sys.argv[1] if len(sys.argv) > 1 else os.path.join(ROOT, 'sections/gpack/T40/mainPage')
OUT = os.path.join(ROOT, 'main_script/include/resources/View/t40FieldShapes.json')
MAP_WIDTH, MAP_HEIGHT = 500, 370
# centres of the fields in the image map of dorf1 (Controller/Dorf1Ctrl.php, LTR); the image map starts 56 px
# right and 4 px below the corner of #village_map (openvillage-t40.css)
CENTRES = [(137, 88), (227, 89), (295, 101), (79, 127), (192, 140), (249, 147), (334, 145), (19, 178), (100, 179),
           (290, 179), (377, 179), (27, 239), (100, 229), (236, 265), (358, 234), (131, 319), (222, 324), (312, 301)]
MAP_OFFSET = (56, 4)
# resource of every field per layout (Game/Formulas.php getFieldTypeResourceMap): 1 wood 2 clay 3 iron 4 crop
LAYOUTS = {
    1: '4,4,1,4,4,2,3,4,4,3,3,4,4,1,4,2,1,2',
    2: '3,4,1,3,2,2,3,4,4,3,3,4,4,1,4,2,1,2',
    3: '1,4,1,3,2,2,3,4,4,3,3,4,4,1,4,2,1,2',
    4: '1,4,1,2,2,2,3,4,4,3,3,4,4,1,4,2,1,2',
    5: '1,4,1,3,1,2,3,4,4,3,3,4,4,1,4,2,1,2',
    6: '4,4,1,3,4,4,4,4,4,4,4,4,4,4,4,2,4,4',
    7: '1,4,4,1,2,2,3,4,4,3,3,4,4,1,4,2,1,2',
    8: '3,4,4,1,2,2,3,4,4,3,3,4,4,1,4,2,1,2',
    9: '3,4,4,1,1,2,3,4,4,3,3,4,4,1,4,2,1,2',
    10: '3,4,1,2,2,2,3,4,4,3,3,4,4,1,4,2,1,2',
    11: '3,1,1,3,1,4,4,3,3,2,2,3,1,4,4,2,4,4',
    12: '1,4,1,1,2,2,3,4,4,3,3,4,4,1,4,2,1,2',
}


def disc(size):
    return cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (size, size))


def patches(image):
    """resource -> mask of the picture painted with it (gaps between trees and rocks closed, holes filled)"""
    hsv = cv2.cvtColor(cv2.medianBlur(image, 5), cv2.COLOR_BGR2HSV).astype(int)
    h, s, v = hsv[..., 0], hsv[..., 1], hsv[..., 2]
    colours = {
        1: (h >= 35) & (h <= 80) & (v < 195) & (s > 80),  # forest: darker than the meadow
        2: (h <= 17) & (s > 80) & (v > 60),  # clay pit: orange
        3: ((s < 75) & (v > 120) & ~((h >= 40) & (h <= 60) & (s > 45)))  # mountains: grey and white
           | ((h >= 85) & (h <= 125) & (s < 110) & (v > 120)),  # and their blue shadows
        4: (h >= 18) & (h <= 33) & (s > 60) & (v > 190),  # crop: yellow
    }
    closing = {1: 9, 2: 7, 3: 9, 4: 7}
    result = {}
    for resource, found in colours.items():
        m = found.astype(np.uint8) * 255
        m = cv2.morphologyEx(m, cv2.MORPH_CLOSE, disc(closing[resource]))
        m = cv2.morphologyEx(m, cv2.MORPH_OPEN, disc(5))
        contours, _ = cv2.findContours(m, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_NONE)
        filled = np.zeros_like(m)
        cv2.drawContours(filled, [c for c in contours if cv2.contourArea(c) > 150], -1, 255, -1)
        result[resource] = filled > 0
    return result


def seed(mask, centre, reach=40):
    """the point of the patch nearest to the centre of the field (the centres sit near, not always on, it)"""
    x, y = centre
    if 0 <= y < mask.shape[0] and 0 <= x < mask.shape[1] and mask[y, x]:
        return x, y
    ys, xs = np.nonzero(mask)
    if not len(xs):
        return None
    distance = (xs - x) ** 2 + (ys - y) ** 2
    i = distance.argmin()
    return (int(xs[i]), int(ys[i])) if distance[i] <= reach * reach else None


def share(mask, seeds):
    """field of every pixel of the patches: the one nearest to it along the patch"""
    height, width = mask.shape
    owner = -np.ones((height, width), int)
    queue = deque()
    for field, (x, y) in seeds.items():
        owner[y, x] = field
        queue.append((x, y))
    while queue:
        x, y = queue.popleft()
        for dx, dy in ((1, 0), (-1, 0), (0, 1), (0, -1)):
            nx, ny = x + dx, y + dy
            if 0 <= nx < width and 0 <= ny < height and mask[ny, nx] and owner[ny, nx] < 0:
                owner[ny, nx] = owner[y, x]
                queue.append((nx, ny))
    return owner


def svg_path(points, offset):
    return 'M' + 'L'.join('%d %d' % (x + offset[0], y + offset[1]) for x, y in points) + 'z'


def trace(layout):
    image = cv2.imread(os.path.join(PACK, 'img/g/f%d-ltr.jpg' % layout))
    height, width = image.shape[:2]
    # the picture is centred at the bottom of #village_map (background-position: 50% 100%)
    offset = ((MAP_WIDTH - width) // 2, MAP_HEIGHT - height)
    centres = [(x + MAP_OFFSET[0] - offset[0], y + MAP_OFFSET[1] - offset[1]) for x, y in CENTRES]
    resources = [int(r) for r in LAYOUTS[layout].split(',')]
    masks = patches(image)
    shapes = {}
    for resource, mask in masks.items():
        seeds = {}
        for field in range(18):
            if resources[field] == resource:
                point = seed(mask, centres[field])
                if point:
                    seeds[field] = point
        owner = share(mask, seeds)
        for field in seeds:
            part = cv2.morphologyEx(((owner == field) * 255).astype(np.uint8), cv2.MORPH_OPEN, disc(5))
            contours, _ = cv2.findContours(part, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_NONE)
            if not contours:
                continue
            outline = max(contours, key=cv2.contourArea)
            if cv2.contourArea(outline) < 200:
                continue
            shapes[str(field + 1)] = svg_path(cv2.approxPolyDP(outline, 1.2, True).reshape(-1, 2), offset)
    missing = [field for field in range(1, 19) if str(field) not in shapes]
    if missing:
        print('layout %d: no outline for fields %s (they keep the circle)' % (layout, missing))
    return {key: shapes[key] for key in sorted(shapes, key=int)}


def main():
    result = {str(layout): trace(layout) for layout in LAYOUTS}
    with open(OUT, 'w') as f:
        json.dump(result, f, separators=(',', ':'))
    print('wrote', OUT)


if __name__ == '__main__':
    main()
