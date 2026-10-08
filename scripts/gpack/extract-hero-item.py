#!/usr/bin/env python3
"""Cuts one equipment item out of a generated/painted hero picture and saves it as a hero layer:
transparent PNG on the full 875 x 1574 Legends hero canvas, aligned to the template figure.

  python3 -I scripts/gpack/extract-hero-item.py --template male-down.png --generated gen.png \
      --slot body --out male/equipment/body/body1.png [--erase male-arms-fistDown.png] [--preview check.png]

How it works
  1. background of the generated picture (flat colour, usually white) -> transparent; only background
     connected to the image border is removed, so white cloth inside the figure stays
  2. picture resized to the canvas and aligned to the template figure (OpenCV ECC, affine)
  3. item = pixels that are not in the template figure or clearly differ from it (Lab colour distance),
     cleaned (close/open, holes filled, small specks dropped) and clipped to the slot zone
  4. edge colours freed from the background colour, saved as RGBA PNG 875 x 1574
Needs: pip install pillow numpy opencv-python-headless
"""
import argparse
import os
import sys

import cv2
import numpy as np
from PIL import Image

W, H = 875, 1574
# Zones on the canvas (x0, y0, x1, y1) per slot; generous so items may overflow the figure a bit.
ZONES = {
    'male': {'helmet': (300, 60, 600, 400), 'body': (150, 300, 720, 1150), 'shoes': (200, 1250, 680, 1574),
             'weapon': (0, 0, 875, 1574), 'shield': (0, 0, 875, 1574), 'any': (0, 0, 875, 1574)},
    'female': {'helmet': (320, 110, 620, 440), 'body': (200, 340, 760, 1160), 'shoes': (200, 1270, 700, 1574),
               'weapon': (0, 0, 875, 1574), 'shield': (0, 0, 875, 1574), 'any': (0, 0, 875, 1574)},
}


def load_rgba(path):
    return np.asarray(Image.open(path).convert('RGBA')).astype(np.float32)


def remove_background(img, tol):
    """img RGBA float. Returns (rgb, alpha 0..1, bg colour)."""
    rgb, a = img[..., :3], img[..., 3] / 255.0
    if (a < 0.5).mean() > 0.05:  # already transparent
        return rgb, a, None
    border = np.concatenate([rgb[0], rgb[-1], rgb[:, 0], rgb[:, -1]])
    bg = np.median(border, axis=0)
    dist = np.linalg.norm(rgb - bg, axis=2)
    near = (dist < tol).astype(np.uint8)
    n, lab = cv2.connectedComponents(near, connectivity=4)
    edge = np.unique(np.concatenate([lab[0], lab[-1], lab[:, 0], lab[:, -1]]))
    bgmask = np.isin(lab, edge[edge > 0]) & (near > 0)
    alpha = (~bgmask).astype(np.float32)
    # soft edge: in a 2 px band outside the figure alpha follows the colour distance
    band = cv2.dilate(alpha, np.ones((5, 5), np.uint8)) - alpha
    alpha = np.where(band > 0, np.clip(dist / (tol * 3), 0, 1), alpha)
    return rgb, alpha, bg


def fit_canvas(rgb, alpha):
    h, w = alpha.shape
    s = H / h
    if abs(w * s - W) > W * 0.03:  # different aspect: fit by height, centre
        nw = round(w * s)
        rgb = cv2.resize(rgb, (nw, H), interpolation=cv2.INTER_AREA)
        alpha = cv2.resize(alpha, (nw, H), interpolation=cv2.INTER_AREA)
        out_rgb = np.full((H, W, 3), 255, np.float32)
        out_a = np.zeros((H, W), np.float32)
        x = (W - nw) // 2
        sx0, dx0 = max(0, -x), max(0, x)
        n = min(nw - sx0, W - dx0)
        out_rgb[:, dx0:dx0 + n] = rgb[:, sx0:sx0 + n]
        out_a[:, dx0:dx0 + n] = alpha[:, sx0:sx0 + n]
        return out_rgb, out_a
    return (cv2.resize(rgb, (W, H), interpolation=cv2.INTER_AREA),
            cv2.resize(alpha, (W, H), interpolation=cv2.INTER_AREA))


def align(rgb, alpha, tpl_rgb, tpl_a, max_shift):
    """Affine ECC of the generated figure onto the template figure (on the alpha silhouettes + grey)."""
    def prep(c, a):
        g = cv2.cvtColor(c.astype(np.uint8), cv2.COLOR_RGB2GRAY).astype(np.float32) / 255.0
        return cv2.GaussianBlur(g * a * 0.5 + a * 0.5, (0, 0), 3)
    src, dst = prep(rgb, alpha), prep(tpl_rgb, tpl_a)
    mask = (cv2.dilate(tpl_a, np.ones((31, 31), np.uint8)) > 0).astype(np.uint8)
    warp = np.eye(2, 3, dtype=np.float32)
    try:
        _, warp = cv2.findTransformECC(dst, src, warp, cv2.MOTION_AFFINE,
                                       (cv2.TERM_CRITERIA_EPS | cv2.TERM_CRITERIA_COUNT, 200, 1e-6), mask, 5)
    except cv2.error as e:
        print('align: ECC did not converge, using plain resize –', str(e).splitlines()[-1], file=sys.stderr)
        return rgb, alpha, None
    scale = np.sqrt(abs(np.linalg.det(warp[:, :2])))
    if not (0.85 < scale < 1.15) or np.abs(warp[:, 2]).max() > max_shift:
        print(f'align: implausible transform (scale {scale:.3f}, shift {warp[:, 2]}), using plain resize',
              file=sys.stderr)
        return rgb, alpha, None
    flags = cv2.INTER_LINEAR | cv2.WARP_INVERSE_MAP
    rgb = cv2.warpAffine(rgb, warp, (W, H), flags=flags, borderValue=(255, 255, 255))
    alpha = cv2.warpAffine(alpha, warp, (W, H), flags=flags, borderValue=0)
    return rgb, alpha, warp


def item_mask(rgb, alpha, tpl_rgb, tpl_a, thr, zone, erase, min_area):
    lab1 = cv2.cvtColor((rgb / 255).astype(np.float32), cv2.COLOR_RGB2LAB)
    lab2 = cv2.cvtColor((tpl_rgb / 255).astype(np.float32), cv2.COLOR_RGB2LAB)
    de = np.linalg.norm(cv2.GaussianBlur(lab1, (0, 0), 1.5) - cv2.GaussianBlur(lab2, (0, 0), 1.5), axis=2)
    m = (alpha > 0.5) & ((tpl_a < 0.5) | (de > thr))
    m = m.astype(np.uint8) * 255
    m = cv2.morphologyEx(m, cv2.MORPH_CLOSE, cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (9, 9)))
    m = cv2.morphologyEx(m, cv2.MORPH_OPEN, cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (5, 5)))
    # fill holes (cloth that happens to match the template colour, inside the item)
    ff = m.copy()
    cv2.floodFill(ff, np.zeros((H + 2, W + 2), np.uint8), (0, 0), 255)
    m = m | cv2.bitwise_not(ff)
    x0, y0, x1, y1 = zone
    clip = np.zeros_like(m)
    clip[y0:y1, x0:x1] = 255
    m &= clip
    n, lab, stats, _ = cv2.connectedComponentsWithStats(m, connectivity=8)
    for i in range(1, n):
        if stats[i, cv2.CC_STAT_AREA] < min_area:
            m[lab == i] = 0
    m = cv2.GaussianBlur(m.astype(np.float32) / 255, (0, 0), 0.8)
    if erase is not None:
        m *= 1 - erase
    return np.clip(m, 0, 1) * alpha


def main():
    ap = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument('--template', required=True, help='template figure, transparent PNG 875x1574 (same pose!)')
    ap.add_argument('--generated', required=True, help='picture with the item drawn on the figure')
    ap.add_argument('--out', required=True)
    ap.add_argument('--slot', default='any', choices=['helmet', 'body', 'weapon', 'shield', 'shoes', 'any'])
    ap.add_argument('--gender', choices=['male', 'female'], help='zone set (default: from the template name)')
    ap.add_argument('--box', help='own zone x0,y0,x1,y1 on the 875x1574 canvas (overrides --slot)')
    ap.add_argument('--erase', help='PNG 875x1574: everything opaque in it is removed from the item '
                                     '(e.g. the arms mask, as the arms are drawn over the item anyway)')
    ap.add_argument('--keep-all', action='store_true',
                    help='take every figure pixel in the zone, not only the changed ones (item drawn alone)')
    ap.add_argument('--threshold', type=float, default=14, help='Lab distance counted as changed (default 14)')
    ap.add_argument('--bg-tol', type=float, default=24, help='background colour tolerance (default 24)')
    ap.add_argument('--min-area', type=int, default=400, help='drop specks smaller than this (px)')
    ap.add_argument('--max-shift', type=float, default=120, help='largest accepted alignment shift (px)')
    ap.add_argument('--no-align', action='store_true')
    ap.add_argument('--preview', help='also save a check picture (item alone | item on the figure)')
    a = ap.parse_args()

    tpl = load_rgba(a.template)
    if tpl.shape[:2] != (H, W):
        sys.exit(f'template must be {W}x{H}, is {tpl.shape[1]}x{tpl.shape[0]}')
    tpl_rgb, tpl_a = tpl[..., :3], tpl[..., 3] / 255
    gender = a.gender or ('female' if 'female' in os.path.basename(a.template).lower() else 'male')
    zone = tuple(int(v) for v in a.box.split(',')) if a.box else ZONES[gender][a.slot]

    rgb, alpha, bg = remove_background(load_rgba(a.generated), a.bg_tol)
    rgb, alpha = fit_canvas(rgb, alpha)
    warp = None
    if not a.no_align:
        rgb, alpha, warp = align(rgb, alpha, tpl_rgb, tpl_a, a.max_shift)
    erase = None
    if a.erase:
        e = load_rgba(a.erase)
        erase = cv2.dilate((e[..., 3] > 127).astype(np.uint8), np.ones((3, 3), np.uint8)).astype(np.float32)
    if a.keep_all:
        m = np.zeros((H, W), np.float32)
        x0, y0, x1, y1 = zone
        m[y0:y1, x0:x1] = 1
        if erase is not None:
            m *= 1 - erase
        out_a = m * alpha
    else:
        out_a = item_mask(rgb, alpha, tpl_rgb, tpl_a, a.threshold, zone, erase, a.min_area)
    # remove the background colour from semi-transparent edge pixels
    if bg is not None:
        aa = np.clip(alpha, 1e-3, 1)[..., None]
        rgb = np.where(alpha[..., None] < 0.99, (rgb - (1 - aa) * bg) / aa, rgb)
    out = np.dstack([np.clip(rgb, 0, 255), np.clip(out_a * 255, 0, 255)]).astype(np.uint8)
    out[out[..., 3] == 0, :3] = 0
    os.makedirs(os.path.dirname(os.path.abspath(a.out)), exist_ok=True)
    Image.fromarray(out, 'RGBA').save(a.out, optimize=True)
    box = Image.fromarray(out[..., 3]).getbbox()
    print(f'{a.out}: {W}x{H}, item box {box}, aligned: {"yes" if warp is not None else "no"}')

    if a.preview:
        item = Image.fromarray(out, 'RGBA')
        yy, xx = np.mgrid[0:H, 0:W]
        grey = np.where(((xx // 24 + yy // 24) % 2)[..., None] == 1, 222, 255).astype(np.uint8)
        left = Image.fromarray(np.dstack([grey.repeat(3, axis=2), np.full((H, W), 255, np.uint8)]), 'RGBA')
        left.alpha_composite(item)
        right = Image.new('RGBA', (W, H), (236, 232, 220, 255))
        right.alpha_composite(Image.fromarray(tpl.astype(np.uint8), 'RGBA'))
        right.alpha_composite(item)
        sheet = Image.new('RGB', (W * 2, H), 'white')
        sheet.paste(left.convert('RGB'), (0, 0))
        sheet.paste(right.convert('RGB'), (W, 0))
        sheet.resize((W, H // 2)).save(a.preview)


if __name__ == '__main__':
    main()
