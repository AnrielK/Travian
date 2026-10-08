#!/usr/bin/env python3
"""Trace click/hover shapes for the Legends village walls and rally point from the graphic pack images.

The T4.6 templates carry hand-drawn SVG shapes made for the old artwork; the Legends 655 walls (794x540) and
rally point (125x160) are drawn differently, so their shapes no longer matched the picture. This script outlines
the opaque part of each image and writes main_script/include/resources/View/legendsShapes.json:

    {"roman/g31Bottom": {"w": 794, "h": 540, "d": "M..."}, ..., "emptyWall": {...}}

"emptyWall" is the footprint of the roman wall (bottom + top), used as the shadow of the empty wall site.

Usage: python3 -I scripts/gpack/trace-building-shapes.py [gpack mainPage dir]
"""
import json
import os
import sys

import cv2
import numpy as np

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
PACK = sys.argv[1] if len(sys.argv) > 1 else os.path.join(ROOT, 'sections/gpack/legends655/mainPage')
OUT = os.path.join(ROOT, 'main_script/include/resources/View/legendsShapes.json')
WALLS = {'roman': 31, 'teuton': 32, 'gaul': 33, 'egyptian': 42, 'hun': 43, 'spartan': 47, 'viking': 50}


def mask(path, threshold=60):
    image = cv2.imread(path, cv2.IMREAD_UNCHANGED)
    alpha = image[:, :, 3] if image.ndim == 3 and image.shape[2] == 4 else np.full(image.shape[:2], 255, np.uint8)
    m = (alpha > threshold).astype(np.uint8) * 255
    # Close small gaps (railings, crenels) so the outline is one solid, clickable area.
    m = cv2.morphologyEx(m, cv2.MORPH_CLOSE, cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (9, 9)))
    return m


def path_of(m, min_area=60):
    contours, hierarchy = cv2.findContours(m, cv2.RETR_CCOMP, cv2.CHAIN_APPROX_NONE)
    parts = []
    for i, c in enumerate(contours):
        if abs(cv2.contourArea(c)) < min_area:
            continue
        c = cv2.approxPolyDP(c, 1.2, True)
        if len(c) < 3:
            continue
        points = ' '.join(f'{p[0][0]} {p[0][1]}' for p in c)
        parts.append(f'M{points}Z')
    # Holes (e.g. the inside of the wall ring) are inner contours: with fill-rule evenodd they stay empty.
    return ''.join(parts)


def main():
    base = os.path.join(PACK, 'img_ltr/themes/default/buildings')
    shapes = {}
    for tribe, gid in WALLS.items():
        for part in ('Bottom', 'Top'):
            file = os.path.join(base, tribe, f'g{gid}{part}.png')
            if os.path.isfile(file):
                m = mask(file)
                shapes[f'{tribe}/g{gid}{part}'] = {'w': m.shape[1], 'h': m.shape[0], 'd': path_of(m)}
        file = os.path.join(base, tribe, 'g16.png')
        if os.path.isfile(file):
            m = mask(file)
            shapes[f'{tribe}/g16'] = {'w': m.shape[1], 'h': m.shape[0], 'd': path_of(m)}
    bottom = mask(os.path.join(base, 'roman', 'g31Bottom.png'))
    # Empty wall site: an elliptic band where the wall ring stands (fitted to the roman wall's stone ring,
    # which is the opaque part only; the pictures' soft ground shadow would otherwise fill the whole village).
    solid = cv2.bitwise_or(mask(os.path.join(base, 'roman', 'g31Bottom.png'), 230), mask(os.path.join(base, 'roman', 'g31Top.png'), 230))
    contours, _ = cv2.findContours(solid, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_NONE)
    points = np.vstack([c for c in contours if cv2.contourArea(c) > 200])
    (cx, cy), (w, h), angle = cv2.fitEllipse(points)
    if 45 < angle % 180 < 135:  # OpenCV reports the axes of a rotated box; keep width horizontal.
        w, h = h, w
    rx, ry, band = w / 2 - 6, h / 2 - 6, 30

    def ellipse(ex, ey):
        return (f'M{cx - ex:.1f} {cy:.1f}a{ex:.1f} {ey:.1f} 0 1 0 {2 * ex:.1f} 0'
                f'a{ex:.1f} {ey:.1f} 0 1 0 {-2 * ex:.1f} 0Z')

    shapes['emptyWall'] = {'w': bottom.shape[1], 'h': bottom.shape[0],
                           'd': ellipse(rx, ry) + ellipse(rx - band, ry - band * ry / rx)}
    with open(OUT, 'w') as fh:
        json.dump(shapes, fh, separators=(',', ':'), sort_keys=True)
    print(f'{len(shapes)} shapes -> {os.path.relpath(OUT, ROOT)} ({os.path.getsize(OUT) // 1024} KiB)')


if __name__ == '__main__':
    main()
