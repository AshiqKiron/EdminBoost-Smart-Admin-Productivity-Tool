#!/usr/bin/env python3
"""Generate WordPress.org plugin icon concepts (128 and 256)."""

from __future__ import annotations

import math
from pathlib import Path
from typing import Callable

from PIL import Image, ImageDraw, ImageFilter

ROOT = Path(__file__).resolve().parents[1]
ICONS_DIR = ROOT / "assets" / "icons"

CANVAS_TOP = (18, 21, 28, 255)
CANVAS_BOTTOM = (29, 35, 39, 255)
SHELL_EDGE = (48, 54, 61, 255)
DARK = (18, 22, 28, 255)
SURFACE = (255, 255, 255, 255)
SURFACE_MUTED = (240, 242, 245, 255)
ACCENT = (139, 156, 255, 255)
ACCENT_DIM = (107, 122, 214, 255)
MUTED = (72, 79, 94, 255)
OUTLINE = (148, 154, 166, 255)
BADGE = (239, 68, 68, 255)
CARD_SHADOW = (10, 14, 20, 255)
DIVIDER = (203, 213, 225, 255)
LIGHT_CONTENT = (248, 250, 252, 255)
SIDEBAR_ROW = (55, 65, 81, 255)

MASTER = 4096
SUPERSAMPLE = 3
ROOT_ASSETS = ROOT / "assets"

DrawFn = Callable[[ImageDraw.ImageDraw, int], None]


def _i(value: float) -> int:
    return int(round(value))


def _vertical_gradient(size: int) -> Image.Image:
    """1×N gradient expanded with Lanczos — avoids horizontal banding after downscale."""
    strip = Image.new("RGBA", (1, size))
    px = strip.load()
    denom = max(size - 1, 1)
    for y in range(size):
        t = y / denom
        px[0, y] = tuple(
            _i(CANVAS_TOP[i] * (1 - t) + CANVAS_BOTTOM[i] * t) for i in range(3)
        ) + (255,)
    return strip.resize((size, size), Image.Resampling.LANCZOS)


def _rr(
    draw: ImageDraw.ImageDraw,
    xy: tuple[int, int, int, int],
    radius: int,
    fill: tuple[int, int, int, int],
    outline: tuple[int, int, int, int] | None = None,
    width: int = 0,
) -> None:
    x0, y0, x1, y1 = xy
    r = min(radius, (x1 - x0) // 2, (y1 - y0) // 2)
    draw.rounded_rectangle(xy, radius=r, fill=fill, outline=outline, width=width)


def _dotted_outline(
    draw: ImageDraw.ImageDraw,
    xy: tuple[int, int, int, int],
    radius: int,
    color: tuple[int, int, int, int],
    dash: int,
    gap: int,
    width: int,
) -> None:
    x0, y0, x1, y1 = xy
    segments = [
        ((x0 + radius, y0), (x1 - radius, y0)),
        ((x1, y0 + radius), (x1, y1 - radius)),
        ((x1 - radius, y1), (x0 + radius, y1)),
        ((x0, y1 - radius), (x0, y0 + radius)),
    ]
    for (sx, sy), (ex, ey) in segments:
        length = int(math.hypot(ex - sx, ey - sy))
        if length <= 0:
            continue
        ux = (ex - sx) / length
        uy = (ey - sy) / length
        pos = 0
        while pos < length:
            end = min(pos + dash, length)
            draw.line(
                [
                    (sx + _i(ux * pos), sy + _i(uy * pos)),
                    (sx + _i(ux * end), sy + _i(uy * end)),
                ],
                fill=color,
                width=width,
            )
            pos += dash + gap


def _draw_l_frame(
    draw: ImageDraw.ImageDraw,
    s: int,
    inset: int,
    top_h: int,
    side_w: int,
    color: tuple[int, int, int, int],
) -> tuple[int, int, int, int]:
    """Return content box (x0, y0, x1, y1)."""
    x1 = s - inset
    y1 = s - inset
    top_y1 = inset + top_h
    side_x1 = inset + side_w
    _rr(draw, (inset, inset, x1, top_y1), _i(s * 0.04), color)
    _rr(draw, (inset, top_y1 - _i(s * 0.015), side_x1, y1), _i(s * 0.05), color)
    return side_x1, top_y1, x1, y1


def _draw_gear(
    draw: ImageDraw.ImageDraw,
    cx: int,
    cy: int,
    outer_r: float,
    root_r: float,
    hole_r: float,
    teeth: int,
    fill: tuple[int, int, int, int],
    ring: tuple[int, int, int, int],
    hole_fill: tuple[int, int, int, int],
    outline_w: int,
) -> None:
    """Filled gear silhouette — reads cleanly after downscale to 128px."""
    step = 360.0 / (teeth * 2)
    points: list[tuple[int, int]] = []
    for i in range(teeth * 2):
        ang = math.radians(i * step - 90.0)
        r = outer_r if i % 2 == 0 else root_r
        points.append((cx + _i(r * math.cos(ang)), cy + _i(r * math.sin(ang))))
    draw.polygon(points, fill=fill)
    hub_r = root_r * 0.92
    draw.ellipse(
        (cx - hub_r, cy - hub_r, cx + hub_r, cy + hub_r),
        fill=ring,
        outline=fill,
        width=max(1, outline_w // 2),
    )
    draw.ellipse((cx - hole_r, cy - hole_r, cx + hole_r, cy + hole_r), fill=hole_fill)


def _draw_slider(
    draw: ImageDraw.ImageDraw,
    x0: int,
    y0: int,
    x1: int,
    s: int,
    progress: float,
) -> None:
    track_h = max(4, _i(s * 0.028))
    cy = y0 + track_h // 2
    track_r = track_h // 2
    _rr(draw, (x0, y0, x1, y0 + track_h), track_r, (226, 232, 240, 255))
    fill_x = x0 + _i((x1 - x0) * progress)
    if fill_x > x0 + track_r:
        _rr(draw, (x0, y0, fill_x, y0 + track_h), track_r, ACCENT_DIM)
    thumb_r = max(3, _i(s * 0.024))
    thumb_cx = fill_x
    draw.ellipse(
        (thumb_cx - thumb_r, cy - thumb_r, thumb_cx + thumb_r, cy + thumb_r),
        fill=ACCENT,
        outline=SURFACE,
        width=max(2, _i(s * 0.004)),
    )


def _draw_grip_dots(
    draw: ImageDraw.ImageDraw,
    cx: int,
    cy: int,
    s: int,
    color: tuple[int, int, int, int],
) -> None:
    r = max(2, _i(s * 0.006))
    gap = _i(s * 0.014)
    for row in range(2):
        for col in range(3):
            dx = (col - 1) * gap
            dy = (row - 0.5) * gap
            draw.ellipse(
                (cx + dx - r, cy + dy - r, cx + dx + r, cy + dy + r),
                fill=color,
            )


def draw_option_a_layout_blocks(draw: ImageDraw.ImageDraw, s: int) -> None:
    """A — stacked layout bands with offset + dotted hidden band."""
    pad = _i(s * 0.14)
    w = s - pad * 2
    h_top = _i(s * 0.11)
    h_side = _i(s * 0.52)
    h_content = _i(s * 0.22)
    gap = _i(s * 0.035)
    r = _i(s * 0.04)

    top_x = pad + _i(s * 0.04)
    _rr(draw, (top_x, pad, top_x + w - _i(s * 0.08), pad + h_top), r, MUTED)

    side_x = pad
    side_y = pad + h_top + gap
    _rr(draw, (side_x, side_y, side_x + _i(s * 0.2), side_y + h_side), r, ACCENT)

    content_x = pad + _i(s * 0.1)
    content_y = side_y + _i(s * 0.06)
    _rr(
        draw,
        (content_x, content_y, pad + w, content_y + h_content),
        r,
        SURFACE,
        outline=(226, 232, 240, 255),
        width=max(2, _i(s * 0.004)),
    )

    ghost_x = side_x + _i(s * 0.24)
    ghost_y = side_y + _i(s * 0.08)
    ghost = (ghost_x, ghost_y, ghost_x + _i(s * 0.18), ghost_y + _i(s * 0.12))
    _dotted_outline(draw, ghost, _i(s * 0.02), OUTLINE, _i(s * 0.02), _i(s * 0.018), max(2, _i(s * 0.005)))


def draw_option_b_sidebar_editor(draw: ImageDraw.ImageDraw, s: int) -> None:
    """B — sidebar pills; one row lifted with grip dots."""
    inset = _i(s * 0.1)
    side_w = _i(s * 0.34)
    row_h = _i(s * 0.07)
    gap = _i(s * 0.028)
    r = _i(s * 0.025)
    x0 = inset
    x1 = x0 + side_w
    y = inset + _i(s * 0.1)

    _rr(draw, (x0, inset, x1, s - inset), _i(s * 0.05), DARK)

    for i in range(5):
        lift = _i(s * 0.035) if i == 1 else 0
        shade = ACCENT if i == 1 else MUTED
        y0 = y + i * (row_h + gap) - lift
        _rr(draw, (x0 + _i(s * 0.04), y0, x1 - _i(s * 0.04), y0 + row_h), r, shade)
        if i == 1:
            _draw_grip_dots(draw, x0 + _i(s * 0.07), y0 + row_h // 2, s, SURFACE)

    content_x0 = x1 + _i(s * 0.05)
    _rr(
        draw,
        (content_x0, inset + _i(s * 0.06), s - inset, s - inset),
        _i(s * 0.05),
        SURFACE_MUTED,
        outline=SHELL_EDGE,
        width=max(2, _i(s * 0.004)),
    )


def draw_option_c_topbar_drawer(draw: ImageDraw.ImageDraw, s: int) -> None:
    """C — top bar slots + badge; drawer panel from the right."""
    inset = _i(s * 0.1)
    top_h = _i(s * 0.14)
    x1 = s - inset
    y1 = s - inset
    _rr(draw, (inset, inset, x1, inset + top_h), _i(s * 0.035), DARK)

    slot_r = _i(s * 0.028)
    slot_y = inset + top_h // 2
    slot_x = inset + _i(s * 0.08)
    for i in range(4):
        cx = slot_x + i * _i(s * 0.09)
        fill = ACCENT if i == 2 else MUTED
        draw.ellipse(
            (cx - slot_r, slot_y - slot_r, cx + slot_r, slot_y + slot_r),
            fill=fill,
        )
        if i == 2:
            br = _i(s * 0.014)
            draw.ellipse(
                (cx + slot_r - br, slot_y - slot_r - br, cx + slot_r + br, slot_y - slot_r + br),
                fill=BADGE,
            )

    main_y0 = inset + top_h + _i(s * 0.04)
    _rr(draw, (inset, main_y0, x1, y1), _i(s * 0.05), SURFACE_MUTED)

    drawer_w = _i((x1 - inset) * 0.38)
    _rr(
        draw,
        (x1 - drawer_w, main_y0 + _i(s * 0.06), x1 - _i(s * 0.04), y1 - _i(s * 0.06)),
        _i(s * 0.04),
        SURFACE,
        outline=ACCENT,
        width=max(2, _i(s * 0.005)),
    )
    for i in range(3):
        ly = main_y0 + _i(s * 0.14) + i * _i(s * 0.05)
        _rr(
            draw,
            (x1 - drawer_w + _i(s * 0.05), ly, x1 - _i(s * 0.12), ly + _i(s * 0.018)),
            _i(s * 0.008),
            (226, 232, 240, 255),
        )


def draw_option_d_theme_swatch(draw: ImageDraw.ImageDraw, s: int) -> None:
    """D — theme swatch card: dark/light admin split + palette chips."""
    pad = _i(s * 0.1)
    card_r = _i(s * 0.075)
    card_top = pad + _i(s * 0.05)
    card_box = (pad, card_top, s - pad, s - pad - _i(s * 0.03))
    sh = max(2, _i(s * 0.012))
    shadow_box = (card_box[0] + sh, card_box[1] + sh, card_box[2] + sh, card_box[3] + sh)
    _rr(draw, shadow_box, card_r, CARD_SHADOW)
    _rr(
        draw,
        card_box,
        card_r,
        SURFACE,
        outline=SHELL_EDGE,
        width=max(2, _i(s * 0.004)),
    )

    x0, y0, x1, y1 = card_box
    inner = max(4, _i(s * 0.024))
    split_x = (x0 + x1) // 2
    half_r = _i(s * 0.058)

    _rr(draw, (x0 + inner, y0 + inner, split_x, y1 - inner), half_r, DARK)
    _rr(draw, (split_x, y0 + inner, x1 - inner, y1 - inner), half_r, LIGHT_CONTENT)

    div_w = max(2, _i(s * 0.003))
    draw.rectangle((split_x - div_w // 2, y0 + inner, split_x + div_w // 2, y1 - inner), fill=DIVIDER)

    # Dark side: mini sidebar rows.
    sx0 = x0 + inner + _i(s * 0.035)
    sx1 = split_x - _i(s * 0.04)
    row_h = _i(s * 0.028)
    row_gap = _i(s * 0.018)
    sy = y0 + inner + _i(s * 0.08)
    for i in range(4):
        fill = ACCENT if i == 0 else SIDEBAR_ROW
        ry0 = sy + i * (row_h + row_gap)
        _rr(draw, (sx0, ry0, sx1, ry0 + row_h), _i(s * 0.012), fill)

    # Light side: content placeholders.
    lx0 = split_x + _i(s * 0.05)
    lx1 = x1 - inner - _i(s * 0.05)
    ly = y0 + inner + _i(s * 0.09)
    for i, frac in enumerate((0.72, 0.48, 0.58)):
        y = ly + i * _i(s * 0.038)
        _rr(
            draw,
            (lx0, y, lx0 + _i((lx1 - lx0) * frac), y + max(2, _i(s * 0.014))),
            _i(s * 0.007),
            (226, 232, 240, 255),
        )

    # Palette chip tray (floating on card corner).
    tray_w = _i(s * 0.22)
    tray_h = _i(s * 0.065)
    tray_x1 = x1 - inner - _i(s * 0.02)
    tray_x0 = tray_x1 - tray_w
    tray_y0 = y0 + inner + _i(s * 0.02)
    _rr(
        draw,
        (tray_x0, tray_y0, tray_x1, tray_y0 + tray_h),
        _i(s * 0.02),
        SURFACE,
        outline=DIVIDER,
        width=max(2, _i(s * 0.003)),
    )

    chip_r = _i(s * 0.019)
    chip_cy = tray_y0 + tray_h // 2
    chip_colors = (ACCENT, ACCENT_DIM, MUTED)
    step = (tray_w - _i(s * 0.04)) // 2
    chip_start = tray_x0 + _i(s * 0.03) + chip_r
    ring = max(2, _i(s * 0.004))
    for i, col in enumerate(chip_colors):
        cx = chip_start + i * step
        draw.ellipse(
            (cx - chip_r - ring, chip_cy - chip_r - ring, cx + chip_r + ring, chip_cy + chip_r + ring),
            fill=DIVIDER,
        )
        draw.ellipse((cx - chip_r, chip_cy - chip_r, cx + chip_r, chip_cy + chip_r), fill=col)


def draw_option_e_preset_grid(draw: ImageDraw.ImageDraw, s: int) -> None:
    """E — 2×2 preset grid + bookmark tab."""
    cx = cy = s // 2
    cell = _i(s * 0.11)
    gap = _i(s * 0.034)
    grid_w = cell * 2 + gap
    gx0 = cx - grid_w // 2
    gy0 = cy - grid_w // 2
    cell_r = _i(s * 0.028)

    cells = ((True, ACCENT), (False, MUTED), (True, ACCENT_DIM), (False, MUTED))
    for row in range(2):
        for col in range(2):
            idx = row * 2 + col
            filled, fill = cells[idx]
            x0 = gx0 + col * (cell + gap)
            y0 = gy0 + row * (cell + gap)
            if filled:
                _rr(draw, (x0, y0, x0 + cell, y0 + cell), cell_r, fill)
            else:
                _rr(draw, (x0, y0, x0 + cell, y0 + cell), cell_r, (0, 0, 0, 0), OUTLINE, max(2, _i(s * 0.005)))

    tr_x1 = gx0 + cell + gap + cell
    tr_y0 = gy0
    tab_w = _i(s * 0.05)
    tab_h = _i(s * 0.035)
    _rr(draw, (tr_x1 - tab_w, tr_y0 - tab_h + _i(s * 0.008), tr_x1, tr_y0 + _i(s * 0.015)), _i(s * 0.012), ACCENT)


def draw_option_f_role_lens(draw: ImageDraw.ImageDraw, s: int) -> None:
    """F — user silhouette on admin L-frame with accent ring."""
    inset = _i(s * 0.11)
    top_h = _i(s * 0.12)
    side_w = _i(s * 0.22)
    cx0, cy0, cx1, cy1 = _draw_l_frame(draw, s, inset, top_h, side_w, DARK)

    _rr(
        draw,
        (cx0 + _i(s * 0.05), cy0 + _i(s * 0.05), cx1 - _i(s * 0.05), cy1 - _i(s * 0.05)),
        _i(s * 0.045),
        SURFACE_MUTED,
    )

    ucx = inset + side_w // 2 + _i(s * 0.02)
    ucy = cy0 + (cy1 - cy0) // 2 + _i(s * 0.04)
    head_r = _i(s * 0.055)
    draw.ellipse(
        (ucx - head_r, ucy - head_r - _i(s * 0.04), ucx + head_r, ucy + head_r - _i(s * 0.04)),
        fill=MUTED,
    )
    body_top = ucy + _i(s * 0.02)
    draw.pieslice(
        (ucx - _i(s * 0.09), body_top, ucx + _i(s * 0.09), body_top + _i(s * 0.14)),
        200,
        340,
        fill=MUTED,
    )

    ring_cx = (cx0 + cx1) // 2
    ring_cy = (cy0 + cy1) // 2
    ring_r = _i(min(cx1 - cx0, cy1 - cy0) * 0.42)
    draw.ellipse(
        (ring_cx - ring_r, ring_cy - ring_r, ring_cx + ring_r, ring_cy + ring_r),
        outline=ACCENT,
        width=max(3, _i(s * 0.008)),
    )


def draw_option_g_tune_admin(draw: ImageDraw.ImageDraw, s: int) -> None:
    """G — L-frame with gear/slider in content area."""
    inset = _i(s * 0.11)
    top_h = _i(s * 0.13)
    side_w = _i(s * 0.24)
    cx0, cy0, cx1, cy1 = _draw_l_frame(draw, s, inset, top_h, side_w, DARK)

    row_h = _i(s * 0.028)
    row_gap = _i(s * 0.022)
    sx0 = inset + _i(s * 0.045)
    sx1 = inset + side_w - _i(s * 0.05)
    sy = inset + top_h + _i(s * 0.07)
    for i in range(4):
        fill = ACCENT if i == 0 else (58, 66, 82, 255)
        ry0 = sy + i * (row_h + row_gap)
        _rr(draw, (sx0, ry0, sx1, ry0 + row_h), _i(s * 0.012), fill)

    card = (
        cx0 + _i(s * 0.05),
        cy0 + _i(s * 0.05),
        cx1 - _i(s * 0.05),
        cy1 - _i(s * 0.05),
    )
    _rr(
        draw,
        card,
        _i(s * 0.05),
        SURFACE,
        outline=(226, 232, 240, 255),
        width=max(2, _i(s * 0.003)),
    )

    gcx = (card[0] + card[2]) // 2
    gear_cy = (card[1] + card[3]) // 2 - _i(s * 0.04)
    outer_r = s * 0.088
    root_r = s * 0.068
    hole_r = s * 0.024
    ow = max(2, _i(s * 0.004))
    _draw_gear(draw, gcx, gear_cy, outer_r, root_r, hole_r, 10, ACCENT, ACCENT_DIM, SURFACE, ow)

    sl_x0 = card[0] + _i(s * 0.08)
    sl_x1 = card[2] - _i(s * 0.08)
    sl_y0 = gear_cy + _i(s * 0.11)
    _draw_slider(draw, sl_x0, sl_y0, sl_x1, s, 0.62)


CONCEPTS: tuple[tuple[str, str, DrawFn], ...] = (
    ("option-a", "layout-blocks", draw_option_a_layout_blocks),
    ("option-b", "sidebar-editor", draw_option_b_sidebar_editor),
    ("option-c", "topbar-drawer", draw_option_c_topbar_drawer),
    ("option-d", "theme-swatch", draw_option_d_theme_swatch),
    ("option-e", "preset-grid", draw_option_e_preset_grid),
    ("option-f", "role-lens", draw_option_f_role_lens),
    ("option-g", "tune-admin", draw_option_g_tune_admin),
)


def _render_master(draw_fn: DrawFn, supersample: int = SUPERSAMPLE) -> Image.Image:
    """Draw at high supersample, then merge to MASTER for smoother vector-like edges."""
    ss = MASTER * supersample
    img = _vertical_gradient(ss)
    draw = ImageDraw.Draw(img, "RGBA")
    draw_fn(draw, ss)
    return img.resize((MASTER, MASTER), Image.Resampling.LANCZOS)


def _downscale(img: Image.Image, size: int) -> Image.Image:
    """Progressive halving preserves edge detail better than one-shot resize."""
    current = img
    while current.width // 2 >= size * 2:
        half = current.width // 2
        current = current.resize((half, half), Image.Resampling.LANCZOS)
    if current.width != size:
        current = current.resize((size, size), Image.Resampling.LANCZOS)
    if size <= 256:
        current = current.filter(
            ImageFilter.UnsharpMask(radius=1.0, percent=125, threshold=2)
        )
    if size <= 128:
        current = current.filter(
            ImageFilter.UnsharpMask(radius=0.6, percent=155, threshold=0)
        )
    return current


def _save_png(img: Image.Image, path: Path) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    img.save(path, format="PNG", compress_level=9, optimize=True)


def _export_sizes(master: Image.Image, directory: Path) -> None:
    for size, name in ((256, "icon-256x256.png"), (128, "icon-128x128.png")):
        path = directory / name
        _save_png(_downscale(master, size), path)
        print(f"Wrote {path}")


def main() -> None:
    ICONS_DIR.mkdir(parents=True, exist_ok=True)
    for folder, _slug, draw_fn in CONCEPTS:
        out = ICONS_DIR / folder
        master = _render_master(draw_fn)
        _export_sizes(master, out)

    official = _render_master(draw_option_g_tune_admin)
    _export_sizes(official, ROOT_ASSETS)
    _export_sizes(official, ICONS_DIR / "option-g")
    print("Official plugin icons: option G (tune admin)")

if __name__ == "__main__":
    main()
