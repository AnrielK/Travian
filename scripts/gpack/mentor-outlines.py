#!/usr/bin/env python3
"""
Outlines of the task mentors (sidebar "Task overview" of the Legends look): Legends draws a green silhouette, a little
larger than the mentor picture, behind it (svg path with the mentor_gradient, see #questmasterButton in
legends_ltr.css) and lets it glow while a task can be collected. The graphic pack only has the pictures, so the
silhouettes are traced from their alpha channel: dilated by RADIUS px, the picture centred at the bottom as in the
button's grid, the outer border traced and simplified.

Writes main_script/include/resources/View/legendsMentorOutlines.json: {"<tribe>": {"w", "h", "d"}}.

Usage: python3 -I scripts/gpack/mentor-outlines.py [graphic pack mainPage dir]
"""
import json
import os
import sys

import numpy as np
from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
RADIUS = 5
TRIBES = ['Roman', 'Teuton', 'Gaul', 'Egyptian', 'Hun', 'Spartan']


def dilate(mask, r):
    out = mask.copy()
    ys, xs = np.mgrid[-r:r + 1, -r:r + 1]
    for dy, dx in zip(ys.ravel(), xs.ravel()):
        if dx * dx + dy * dy > r * r:
            continue
        out |= np.roll(np.roll(mask, dy, axis=0), dx, axis=1)
    return out


def trace(mask):
    """Outer border of the largest shape as a polygon of pixel corners (walking with the shape on the right)."""
    h, w = mask.shape
    m = np.zeros((h + 2, w + 2), bool)
    m[1:-1, 1:-1] = mask
    ys, xs = np.nonzero(m)
    start = (xs[np.argmin(ys * (w + 2) + xs)], ys.min())  # top-most, left-most pixel: its top-left corner
    x, y = start
    d = (1, 0)  # walking right along the top edge
    pts = [(x, y)]

    def filled(cx, cy):
        return m[cy, cx]

    while True:
        # pixels left / right ahead of the corner (x, y) in direction d
        dx, dy = d
        if d == (1, 0):
            right, left = (x, y), (x, y - 1)
        elif d == (0, 1):
            right, left = (x - 1, y), (x, y)
        elif d == (-1, 0):
            right, left = (x - 1, y - 1), (x - 1, y)
        else:
            right, left = (x, y - 1), (x - 1, y - 1)
        if filled(*left):
            d = (dy, -dx)  # turn left
        elif not filled(*right):
            d = (-dy, dx)  # turn right
        x, y = x + d[0], y + d[1]
        pts.append((x, y))
        if (x, y) == start:
            break
    return [(px - 1, py - 1) for px, py in pts]


def simplify(pts, eps):
    if len(pts) < 3:
        return pts
    a, b = np.array(pts[0], float), np.array(pts[-1], float)
    ab = b - a
    n = np.hypot(*ab) or 1.0
    dists = [abs(ab[0] * (a[1] - p[1]) - ab[1] * (a[0] - p[0])) / n for p in pts[1:-1]]
    i = int(np.argmax(dists)) + 1
    if dists[i - 1] > eps:
        return simplify(pts[:i + 1], eps)[:-1] + simplify(pts[i:], eps)
    return [pts[0], pts[-1]]


def outline(path):
    img = Image.open(path).convert('RGBA')
    w, h = img.size
    alpha = np.array(img)[:, :, 3] > 40
    cw, ch = w + 2 * RADIUS, h + RADIUS
    canvas = np.zeros((ch + RADIUS, cw), bool)  # room below for the dilation, cut off at the bottom edge
    canvas[RADIUS:RADIUS + h, RADIUS:RADIUS + w] = alpha
    shape = dilate(canvas, RADIUS)[:ch]
    pts = trace(shape)
    pts = simplify(pts[:len(pts) // 2 + 1], 0.8)[:-1] + simplify(pts[len(pts) // 2:], 0.8)
    d = 'M' + ' L'.join('%d %d' % (px, min(py, ch)) for px, py in pts) + 'Z'
    return {'w': cw, 'h': ch, 'd': d}


def main():
    pack = sys.argv[1] if len(sys.argv) > 1 else os.path.join(ROOT, 'sections/gpack/legends655/mainPage')
    out = {}
    for tribe in TRIBES:
        png = os.path.join(pack, 'img_ltr/hud/sidebar/tasks/mentor%s.png' % tribe)
        if os.path.isfile(png):
            out[tribe.lower()] = outline(png)
    target = os.path.join(ROOT, 'main_script/include/resources/View/legendsMentorOutlines.json')
    with open(target, 'w') as f:
        json.dump(out, f, separators=(',', ':'), sort_keys=True)
    print('%d outlines -> %s' % (len(out), os.path.relpath(target, ROOT)))


if __name__ == '__main__':
    main()
