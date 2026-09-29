#!/usr/bin/env python3
"""Generate WordPress.org plugin banner concepts (772×250 and 1544×500)."""

from __future__ import annotations

import importlib.util
from pathlib import Path
from typing import Callable

from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = Path(__file__).resolve().parents[1]
BANNERS_DIR = ROOT / "assets" / "banners"

BANNER_1X = (772, 250)
BANNER_2X = (1544, 500)
SUPERSAMPLE = 3

_ICONS_PATH = ROOT / "bin" / "generate-plugin-icons.py"
_spec = importlib.util.spec_from_file_location("edminboost_icons", _ICONS_PATH)
icons = importlib.util.module_from_spec(_spec)
assert _spec.loader is not None
_spec.loader.exec_module(icons)

DrawFn = Callable[[ImageDraw.ImageDraw, int], None]

ACCENT = icons.ACCENT
ACCENT_DIM = icons.ACCENT_DIM
BADGE = icons.BADGE
CARD_SHADOW = icons.CARD_SHADOW
DARK = icons.DARK
DIVIDER = icons.DIVIDER
LIGHT_CONTENT = icons.LIGHT_CONTENT
MUTED = icons.MUTED
OUTLINE = icons.OUTLINE
SHELL_EDGE = icons.SHELL_EDGE
SIDEBAR_ROW = icons.SIDEBAR_ROW
SURFACE = icons.SURFACE
SURFACE_MUTED = icons.SURFACE_MUTED

_i = icons._i
_dotted_outline = icons._dotted_outline
_draw_gear = icons._draw_gear
_draw_grip_dots = icons._draw_grip_dots
_draw_l_frame = icons._draw_l_frame
_draw_slider = icons._draw_slider
_rr = icons._rr


def _banner_gradient(w: int, h: int) -> Image.Image:
    strip = Image.new("RGBA", (1, h))
    px = strip.load()
    top = icons.CANVAS_TOP
    bottom = icons.CANVAS_BOTTOM
    denom = max(h - 1, 1)
    for y in range(h):
        t = y / denom
        px[0, y] = tuple(_i(top[i] * (1 - t) + bottom[i] * t) for i in range(3)) + (255,)
    return strip.resize((w, h), Image.Resampling.LANCZOS)


def _font(size: int, bold: bool = True) -> ImageFont.FreeTypeFont | ImageFont.ImageFont:
    candidates = (
        "/System/Library/Fonts/Supplemental/Arial Bold.ttf",
        "/System/Library/Fonts/Supplemental/Arial.ttf",
        "/Library/Fonts/Arial Bold.ttf",
        "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf",
        "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf",
    )
    if not bold:
        candidates = candidates[1:] + candidates[:1]
    for path in candidates:
        p = Path(path)
        if p.exists():
            return ImageFont.truetype(str(p), size)
    return ImageFont.load_default()


def _text_wh(draw: ImageDraw.ImageDraw, text: str, font: ImageFont.ImageFont) -> tuple[int, int]:
    box = draw.textbbox((0, 0), text, font=font)
    return box[2] - box[0], box[3] - box[1]


def _draw_tagline(
    draw: ImageDraw.ImageDraw,
    w: int,
    h: int,
    lines: tuple[str, ...],
    *,
    align: str = "right",
    margin_x: float = 0.06,
    margin_y: float = 0.18,
    size_ratio: float = 0.048,
    accent_line: bool = True,
) -> None:
    if not lines:
        return
    fs = max(14, _i(h * size_ratio))
    font = _font(fs)
    font_sub = _font(max(12, _i(fs * 0.78)), bold=False)
    mx = _i(w * margin_x)
    gap = _i(h * 0.04)
    line_gap = _i(h * 0.06)
    heights = []
    fonts = [font] + [font_sub] * (len(lines) - 1)
    for i, line in enumerate(lines):
        _, th = _text_wh(draw, line, fonts[i])
        heights.append(th)
    total_h = sum(heights) + line_gap * (len(lines) - 1)
    y = _i(h * margin_y)
    if align == "center":
        x_anchor = w // 2
    elif align == "left":
        x_anchor = mx
    else:
        x_anchor = w - mx

    for i, line in enumerate(lines):
        f = fonts[i]
        tw, th = _text_wh(draw, line, f)
        if align == "center":
            x = x_anchor - tw // 2
        elif align == "left":
            x = x_anchor
        else:
            x = x_anchor - tw
        draw.text((x, y), line, fill=SURFACE, font=f)
        if i == 0 and accent_line:
            ly = y + th + gap
            lw = min(tw, _i(w * 0.28))
            lx = x if align != "center" else x_anchor - lw // 2
            _rr(draw, (lx, ly, lx + lw, ly + max(3, _i(h * 0.012))), 2, ACCENT)
        y += th + line_gap


def _paste_concept(img: Image.Image, draw_fn: DrawFn, box: tuple[int, int, int, int]) -> None:
    x0, y0, x1, y1 = box
    bw, bh = x1 - x0, y1 - y0
    s = min(bw, bh)
    tile = _banner_gradient(s, s)
    d = ImageDraw.Draw(tile, "RGBA")
    draw_fn(d, s)
    px = x0 + (bw - s) // 2
    py = y0 + (bh - s) // 2
    img.paste(tile, (px, py), tile)


def _draw_admin_panorama(img: Image.Image, w: int, h: int) -> None:
    """Wide Command Center mock (sidebar + top bar + content card)."""
    pad = _i(h * 0.1)
    ui_x0 = pad
    ui_y0 = _i(h * 0.12)
    ui_x1 = _i(w * 0.62)
    ui_y1 = h - pad
    tile_w = ui_x1 - ui_x0
    tile_h = ui_y1 - ui_y0
    s = max(tile_w, tile_h)
    sub = Image.new("RGBA", (s, s), (0, 0, 0, 0))
    sd = ImageDraw.Draw(sub, "RGBA")
    inset = _i(s * 0.06)
    th = _i(s * 0.14)
    sw = _i(s * 0.26)
    t_x1 = s - inset
    t_y1 = s - inset
    top_y1 = inset + th
    side_x1 = inset + sw
    _rr(sd, (inset, inset, t_x1, top_y1), _i(s * 0.035), DARK)
    _rr(sd, (inset, top_y1 - _i(s * 0.012), side_x1, t_y1), _i(s * 0.04), DARK)

    row_h = _i(s * 0.045)
    gap = _i(s * 0.022)
    sx0 = inset + _i(s * 0.035)
    sx1 = side_x1 - _i(s * 0.03)
    sy = top_y1 + _i(s * 0.05)
    for i in range(5):
        lift = _i(s * 0.025) if i == 1 else 0
        fill = ACCENT if i == 1 else SIDEBAR_ROW
        ry0 = sy + i * (row_h + gap) - lift
        _rr(sd, (sx0, ry0, sx1, ry0 + row_h), _i(s * 0.015), fill)
        if i == 1:
            _draw_grip_dots(sd, sx0 + _i(s * 0.05), ry0 + row_h // 2, s, SURFACE)

    slot_r = _i(s * 0.018)
    slot_y = inset + th // 2
    slot_x = side_x1 + _i(s * 0.04)
    for i in range(4):
        cx = slot_x + i * _i(s * 0.055)
        fill = ACCENT if i == 2 else MUTED
        sd.ellipse((cx - slot_r, slot_y - slot_r, cx + slot_r, slot_y + slot_r), fill=fill)
        if i == 2:
            br = _i(s * 0.01)
            sd.ellipse(
                (cx + slot_r - br, slot_y - slot_r - br, cx + slot_r + br, slot_y - slot_r + br),
                fill=BADGE,
            )

    card = (
        side_x1 + _i(s * 0.04),
        top_y1 + _i(s * 0.04),
        t_x1 - _i(s * 0.04),
        t_y1 - _i(s * 0.04),
    )
    _rr(
        sd,
        card,
        _i(s * 0.04),
        SURFACE,
        outline=(226, 232, 240, 255),
        width=max(2, _i(s * 0.003)),
    )
    gcx = (card[0] + card[2]) // 2
    gear_cy = (card[1] + card[3]) // 2 - _i(s * 0.03)
    _draw_gear(sd, gcx, gear_cy, s * 0.07, s * 0.054, s * 0.018, 10, ACCENT, ACCENT_DIM, SURFACE, max(2, _i(s * 0.003)))
    sl_y0 = gear_cy + _i(s * 0.09)
    _draw_slider(sd, card[0] + _i(s * 0.07), sl_y0, card[2] - _i(s * 0.07), s, 0.62)

    crop = sub.crop((0, 0, tile_w, tile_h))
    img.paste(crop, (ui_x0, ui_y0), crop)

    glow_r = _i(h * 0.35)
    gx = ui_x1 - _i(w * 0.02)
    gy = ui_y1 - _i(h * 0.15)
    for alpha, radius in ((28, glow_r), (18, _i(glow_r * 1.25))):
        layer = Image.new("RGBA", (w, h), (0, 0, 0, 0))
        ld = ImageDraw.Draw(layer)
        glow = (ACCENT[0], ACCENT[1], ACCENT[2], alpha)
        ld.ellipse((gx - radius, gy - radius, gx + radius, gy + radius), fill=glow)
        img.alpha_composite(layer)


def banner_command_center(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    _draw_admin_panorama(img, w, h)
    _draw_tagline(
        draw,
        w,
        h,
        (
            "Customize admin layout,",
            "menu, theme & shortcuts",
        ),
        align="right",
        margin_x=0.05,
        margin_y=0.28,
    )


def banner_before_after(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad = _i(h * 0.1)
    split = _i(w * 0.42)
    mid_y0 = pad
    mid_y1 = h - pad
    # Before panel
    _rr(draw, (pad, mid_y0, split - _i(w * 0.02), mid_y1), _i(h * 0.06), (32, 36, 44, 255))
    notice_h = _i(h * 0.09)
    notice_gap = _i(h * 0.035)
    nx0 = pad + _i(w * 0.025)
    nx1 = split - _i(w * 0.05)
    ny = mid_y0 + _i(h * 0.08)
    colors = ((239, 68, 68, 200), (245, 158, 11, 200), (59, 130, 246, 200), (16, 185, 129, 200))
    for i, col in enumerate(colors):
        y0 = ny + i * (notice_h + notice_gap)
        _rr(draw, (nx0, y0, nx1, y0 + notice_h), _i(h * 0.02), col)
        _rr(draw, (nx0 + _i(w * 0.02), y0 + _i(h * 0.03), nx1 - _i(w * 0.08), y0 + _i(h * 0.05)), _i(h * 0.008), (255, 255, 255, 120))

    fs = max(12, _i(h * 0.07))
    draw.text((pad + _i(w * 0.02), mid_y0 + _i(h * 0.02)), "Before", fill=(180, 185, 195, 255), font=_font(fs))

    # Divider
    div_x = split
    draw.line([(div_x, mid_y0), (div_x, mid_y1)], fill=ACCENT, width=max(3, _i(h * 0.016)))

    # After — clean admin
    ax0 = split + _i(w * 0.02)
    box = (ax0, mid_y0, w - pad, mid_y1)
    _paste_concept(img, icons.draw_option_g_tune_admin, box)
    draw.text((ax0, mid_y0 - _i(h * 0.02)), "After", fill=ACCENT, font=_font(fs))

    _draw_tagline(
        draw,
        w,
        h,
        ("Less clutter.", "Faster admin workflows."),
        align="right",
        margin_y=0.22,
        size_ratio=0.052,
    )


def banner_four_pillars(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad_x = _i(w * 0.04)
    pad_y = _i(h * 0.14)
    gap = _i(w * 0.022)
    labels = ("Layout", "Menu", "Theme", "Top bar")
    draw_fns = (
        icons.draw_option_e_preset_grid,
        icons.draw_option_b_sidebar_editor,
        icons.draw_option_d_theme_swatch,
        icons.draw_option_c_topbar_drawer,
    )
    usable = w - pad_x * 2 - gap * 3
    cell_w = usable // 4
    cell_h = h - pad_y - _i(h * 0.22)
    label_fs = max(11, _i(h * 0.065))
    lfont = _font(label_fs, bold=False)
    for i, (label, fn) in enumerate(zip(labels, draw_fns)):
        x0 = pad_x + i * (cell_w + gap)
        y0 = pad_y
        x1 = x0 + cell_w
        y1 = y0 + cell_h
        sh = max(2, _i(h * 0.012))
        _rr(draw, (x0 + sh, y0 + sh, x1 + sh, y1 + sh), _i(h * 0.05), CARD_SHADOW)
        _rr(draw, (x0, y0, x1, y1), _i(h * 0.05), (40, 46, 56, 255), outline=SHELL_EDGE, width=max(1, _i(h * 0.006)))
        inner = (x0 + _i(w * 0.008), y0 + _i(h * 0.04), x1 - _i(w * 0.008), y1 - _i(h * 0.04))
        _paste_concept(img, fn, inner)
        tw, _ = _text_wh(draw, label, lfont)
        draw.text((x0 + (cell_w - tw) // 2, y1 + _i(h * 0.035)), label, fill=(210, 215, 225, 255), font=lfont)

    _draw_tagline(
        draw,
        w,
        h,
        ("Customize your WordPress Admin Panel",),
        align="center",
        margin_y=0.06,
        size_ratio=0.055,
        accent_line=False,
    )


def banner_theme_hero(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad = _i(h * 0.08)
    box = (pad, pad, _i(w * 0.55), h - pad)
    _paste_concept(img, icons.draw_option_d_theme_swatch, box)
    _draw_tagline(
        draw,
        w,
        h,
        (
            "Visual admin themes",
            "Light · dark · auto",
            "Custom colors & presets",
        ),
        align="left",
        margin_x=0.58,
        margin_y=0.26,
        size_ratio=0.05,
    )


def banner_menu_editor(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad = _i(w * 0.04)
    main_box = (pad, _i(h * 0.1), _i(w * 0.68), h - _i(h * 0.1))
    _paste_concept(img, icons.draw_option_b_sidebar_editor, main_box)

    # Ghost hidden item (from layout-blocks motif)
    gx0 = _i(w * 0.52)
    gy0 = _i(h * 0.55)
    gw = _i(w * 0.12)
    gh = _i(h * 0.14)
    _dotted_outline(
        draw,
        (gx0, gy0, gx0 + gw, gy0 + gh),
        _i(h * 0.02),
        OUTLINE,
        _i(w * 0.012),
        _i(w * 0.01),
        max(2, _i(h * 0.008)),
    )

    _draw_tagline(
        draw,
        w,
        h,
        (
            "Reorder & hide",
            "admin menu items",
        ),
        align="right",
        margin_y=0.3,
    )


def _draw_mini_sidebar(
    draw: ImageDraw.ImageDraw,
    box: tuple[int, int, int, int],
    row_count: int,
    *,
    accent_row: int = 1,
) -> None:
    x0, y0, x1, y1 = box
    bw, bh = x1 - x0, y1 - y0
    _rr(draw, box, _i(bh * 0.08), DARK, outline=SHELL_EDGE, width=max(1, _i(bh * 0.012)))
    pad = _i(bw * 0.08)
    row_h = max(4, _i((bh - pad * 2) / (row_count + (row_count - 1) * 0.35)))
    gap = _i(row_h * 0.35)
    sy = y0 + pad
    sx0 = x0 + pad
    sx1 = x1 - pad
    for i in range(row_count):
        fill = ACCENT if i == accent_row else SIDEBAR_ROW
        ry0 = sy + i * (row_h + gap)
        if ry0 + row_h > y1 - pad:
            break
        _rr(draw, (sx0, ry0, sx1, ry0 + row_h), _i(row_h * 0.35), fill)


def _draw_browser_chrome(draw: ImageDraw.ImageDraw, w: int, h: int, x0: int, y0: int, x1: int, bar_h: int) -> None:
    _rr(draw, (x0, y0, x1, y0 + bar_h), _i(bar_h * 0.35), (52, 58, 68, 255))
    dot_r = _i(bar_h * 0.18)
    dcy = y0 + bar_h // 2
    for i, col in enumerate(((239, 68, 68, 255), (245, 158, 11, 255), (34, 197, 94, 255))):
        dcx = x0 + _i(bar_h * 0.45) + i * _i(bar_h * 0.55)
        draw.ellipse((dcx - dot_r, dcy - dot_r, dcx + dot_r, dcy + dot_r), fill=col)
    fav = _i(bar_h * 0.42)
    fx = x1 - _i(bar_h * 0.55)
    _rr(draw, (fx, dcy - fav // 2, fx + fav, dcy + fav // 2), _i(fav * 0.22), ACCENT)


def banner_stack_replacement(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad = _i(h * 0.12)
    stack_x1 = _i(w * 0.38)
    tile = _i(h * 0.11)
    gap = _i(h * 0.025)
    colors = (
        (100, 116, 139, 255),
        (249, 115, 22, 255),
        (59, 130, 246, 255),
        (168, 85, 247, 255),
        (236, 72, 153, 255),
    )
    sy = pad + _i(h * 0.06)
    for i, col in enumerate(colors):
        ox = pad + (i % 2) * _i(tile * 0.35)
        oy = sy + i * (tile + gap)
        _rr(draw, (ox, oy, ox + tile, oy + tile), _i(tile * 0.18), col)
        _rr(
            draw,
            (ox + _i(tile * 0.22), oy + _i(tile * 0.38), ox + tile - _i(tile * 0.15), oy + _i(tile * 0.52)),
            _i(tile * 0.06),
            (255, 255, 255, 90),
        )

    mid_x = stack_x1 + _i(w * 0.04)
    mid_y0 = _i(h * 0.35)
    mid_y1 = _i(h * 0.65)
    draw.line([(stack_x1, mid_y0), (mid_x, (mid_y0 + mid_y1) // 2), (stack_x1, mid_y1)], fill=MUTED, width=max(2, _i(h * 0.008)))
    ax0 = _i(w * 0.42)
    arr_y = (mid_y0 + mid_y1) // 2
    draw.polygon(
        [(ax0, arr_y), (ax0 - _i(w * 0.025), arr_y - _i(h * 0.04)), (ax0 - _i(w * 0.025), arr_y + _i(h * 0.04))],
        fill=ACCENT,
    )

    box = (ax0 + _i(w * 0.02), pad, w - pad, h - _i(h * 0.2))
    _paste_concept(img, icons.draw_option_g_tune_admin, box)

    _draw_tagline(
        draw,
        w,
        h,
        ("Stop stitching plugins together.", "One Command Center."),
        align="left",
        margin_x=0.04,
        margin_y=0.06,
        size_ratio=0.044,
    )


def banner_role_split(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad_x = _i(w * 0.04)
    pad_y = _i(h * 0.16)
    gap = _i(w * 0.035)
    panel_w = (w - pad_x * 2 - gap) // 2
    panel_h = h - pad_y - _i(h * 0.22)
    label_fs = max(11, _i(h * 0.055))
    lfont = _font(label_fs, bold=False)

    panels = (
        ("Administrator", 7, 2),
        ("Client", 4, 0),
    )
    for i, (label, rows, accent) in enumerate(panels):
        x0 = pad_x + i * (panel_w + gap)
        y0 = pad_y
        x1 = x0 + panel_w
        y1 = y0 + panel_h
        sh = max(2, _i(h * 0.01))
        _rr(draw, (x0 + sh, y0 + sh, x1 + sh, y1 + sh), _i(h * 0.04), CARD_SHADOW)
        _rr(draw, (x0, y0, x1, y1), _i(h * 0.04), (40, 46, 56, 255), outline=SHELL_EDGE, width=max(1, _i(h * 0.006)))
        tw, _ = _text_wh(draw, label, lfont)
        draw.text((x0 + (panel_w - tw) // 2, y0 - _i(h * 0.07)), label, fill=(200, 206, 218, 255), font=lfont)
        inner = (x0 + _i(w * 0.012), y0 + _i(h * 0.04), x1 - _i(w * 0.012), y1 - _i(h * 0.04))
        _draw_mini_sidebar(draw, inner, rows, accent_row=accent)
        cx0 = inner[0] + _i(panel_w * 0.38)
        _rr(draw, (cx0, inner[1], inner[2], inner[3]), _i(h * 0.03), SURFACE_MUTED)

    _draw_tagline(
        draw,
        w,
        h,
        ("Role layouts clients understand.",),
        align="center",
        margin_y=0.04,
        size_ratio=0.052,
        accent_line=False,
    )


def banner_white_label_trio(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad = _i(w * 0.04)
    chrome_h = _i(h * 0.07)
    ui_y0 = pad + chrome_h + _i(h * 0.02)
    ui_x0 = pad
    ui_x1 = _i(w * 0.68)
    ui_y1 = h - _i(h * 0.18)
    _draw_browser_chrome(draw, w, h, ui_x0, pad, ui_x1, chrome_h)

    side_w = _i((ui_x1 - ui_x0) * 0.28)
    _rr(draw, (ui_x0, ui_y0, ui_x0 + side_w, ui_y1), _i(h * 0.025), DARK)
    logo_y1 = ui_y0 + _i(h * 0.12)
    _rr(draw, (ui_x0 + _i(w * 0.015), ui_y0 + _i(h * 0.035), ui_x0 + side_w - _i(w * 0.015), logo_y1), _i(h * 0.02), ACCENT)
    for i in range(4):
        ry = logo_y1 + _i(h * 0.04) + i * _i(h * 0.055)
        _rr(
            draw,
            (ui_x0 + _i(w * 0.02), ry, ui_x0 + side_w - _i(w * 0.02), ry + _i(h * 0.035)),
            _i(h * 0.012),
            SIDEBAR_ROW,
        )

    cx0 = ui_x0 + side_w
    _rr(draw, (cx0, ui_y0, ui_x1, ui_y1), _i(h * 0.025), SURFACE_MUTED)
    foot_y0 = ui_y1 - _i(h * 0.08)
    _rr(draw, (cx0 + _i(w * 0.02), foot_y0, ui_x1 - _i(w * 0.02), ui_y1 - _i(h * 0.025)), _i(h * 0.015), (226, 232, 240, 255))
    foot_fs = max(10, _i(h * 0.032))
    ffont = _font(foot_fs, bold=False)
    draw.text((cx0 + _i(w * 0.03), foot_y0 + _i(h * 0.022)), "© Your Agency", fill=MUTED, font=ffont)

    callout_fs = max(10, _i(h * 0.038))
    cfont = _font(callout_fs, bold=False)
    callouts = (
        (ui_x0 + side_w // 2, ui_y0 + _i(h * 0.06), "Logo"),
        (ui_x1 - _i(w * 0.04), pad + chrome_h // 2, "Favicon"),
        (cx0 + _i(w * 0.2), foot_y0 + _i(h * 0.04), "Footer"),
    )
    for i, (px, py, text) in enumerate(callouts):
        tx = _i(w * 0.72) + (i % 2) * _i(w * 0.02)
        ty = _i(h * 0.22) + i * _i(h * 0.14)
        draw.line([(px, py), (tx - _i(w * 0.04), ty)], fill=ACCENT_DIM, width=max(2, _i(h * 0.005)))
        tw, th = _text_wh(draw, text, cfont)
        _rr(draw, (tx, ty - th // 2, tx + tw + _i(w * 0.02), ty + th // 2 + _i(h * 0.01)), _i(h * 0.012), (48, 54, 64, 240), ACCENT, max(1, _i(h * 0.004)))
        draw.text((tx + _i(w * 0.01), ty - th // 2), text, fill=SURFACE, font=cfont)

    _draw_tagline(
        draw,
        w,
        h,
        ("Your agency,", "not default WordPress."),
        align="right",
        margin_x=0.04,
        margin_y=0.52,
        size_ratio=0.046,
    )


def banner_menu_drag(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad = _i(w * 0.04)
    box = (pad, _i(h * 0.1), _i(w * 0.72), h - _i(h * 0.2))
    _paste_concept(img, icons.draw_option_b_sidebar_editor, box)

    x0, y0, x1, y1 = box
    bw, bh = x1 - x0, y1 - y0
    s = min(bw, bh)
    ghost_off = _i(s * 0.06)
    side_w = _i(s * 0.34)
    row_h = _i(s * 0.07)
    gap = _i(s * 0.028)
    inset = _i(s * 0.1)
    gx0 = x0 + (bw - s) // 2 + inset + ghost_off
    gy0 = y0 + (bh - s) // 2 + inset + _i(s * 0.1) + row_h + gap - _i(s * 0.035)
    gx1 = gx0 + side_w
    _rr(draw, (gx0, gy0, gx1, gy0 + row_h), _i(s * 0.025), ACCENT)
    _draw_grip_dots(draw, gx0 + _i(s * 0.07), gy0 + row_h // 2, s, SURFACE)

    hx0 = gx0 + side_w + _i(s * 0.03)
    hx1 = hx0 + _i(s * 0.14)
    _dotted_outline(
        draw,
        (hx0, gy0, hx1, gy0 + row_h),
        _i(s * 0.02),
        OUTLINE,
        _i(s * 0.015),
        _i(s * 0.012),
        max(2, _i(s * 0.004)),
    )

    _draw_tagline(
        draw,
        w,
        h,
        ("Reorder, rename, hide", "— no code."),
        align="right",
        margin_y=0.24,
        size_ratio=0.05,
    )


def banner_role_presets(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad_x = _i(w * 0.04)
    pad_y = _i(h * 0.18)
    gap = _i(w * 0.025)
    card_w = (w - pad_x * 2 - gap * 2) // 3
    card_h = h - pad_y - _i(h * 0.24)
    titles = ("Shop manager", "Content editor", "Developer")
    active = 1
    title_fs = max(10, _i(h * 0.042))
    tfont = _font(title_fs, bold=False)

    for i, title in enumerate(titles):
        x0 = pad_x + i * (card_w + gap)
        y0 = pad_y
        x1 = x0 + card_w
        y1 = y0 + card_h
        outline = ACCENT if i == active else SHELL_EDGE
        ow = max(2, _i(h * 0.008)) if i == active else max(1, _i(h * 0.005))
        _rr(draw, (x0, y0, x1, y1), _i(h * 0.035), (40, 46, 56, 255), outline=outline, width=ow)
        if i == active:
            badge = "Active"
            bfs = max(9, _i(h * 0.032))
            bfont = _font(bfs, bold=False)
            btw, bth = _text_wh(draw, badge, bfont)
            bx1 = x1 - _i(w * 0.012)
            bx0 = bx1 - btw - _i(w * 0.02)
            by0 = y0 + _i(h * 0.02)
            _rr(draw, (bx0, by0, bx1, by0 + bth + _i(h * 0.012)), _i(h * 0.01), ACCENT)
            draw.text((bx0 + _i(w * 0.01), by0 + _i(h * 0.006)), badge, fill=SURFACE, font=bfont)

        inner = (x0 + _i(w * 0.01), y0 + _i(h * 0.1), x1 - _i(w * 0.01), y1 - _i(h * 0.08))
        _paste_concept(img, icons.draw_option_e_preset_grid, inner)

        tw, _ = _text_wh(draw, title, tfont)
        draw.text((x0 + (card_w - tw) // 2, y1 + _i(h * 0.025)), title, fill=(210, 215, 225, 255), font=tfont)

    _draw_tagline(
        draw,
        w,
        h,
        ("Scenario layouts in one click.",),
        align="center",
        margin_y=0.05,
        size_ratio=0.05,
        accent_line=False,
    )


def banner_wizard_live(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    pad = _i(w * 0.04)
    strip_y0 = _i(h * 0.12)
    strip_y1 = _i(h * 0.26)
    steps = ("1 Layout", "2 Theme", "3 Top bar", "4 Review")
    step_w = (w - pad * 2 - _i(w * 0.03) * 3) // 4
    sfs = max(10, _i(h * 0.038))
    sfont = _font(sfs, bold=False)
    for i, label in enumerate(steps):
        x0 = pad + i * (step_w + _i(w * 0.03))
        fill = ACCENT if i < 3 else (55, 62, 74, 255)
        _rr(draw, (x0, strip_y0, x0 + step_w, strip_y1), _i(h * 0.025), fill)
        tw, th = _text_wh(draw, label, sfont)
        draw.text((x0 + (step_w - tw) // 2, strip_y0 + (strip_y1 - strip_y0 - th) // 2), label, fill=SURFACE, font=sfont)
        if i < 3:
            ax = x0 + step_w + _i(w * 0.015)
            ay = (strip_y0 + strip_y1) // 2
            draw.polygon(
                [(ax + _i(w * 0.012), ay), (ax, ay - _i(h * 0.025)), (ax, ay + _i(h * 0.025))],
                fill=ACCENT_DIM,
            )

    box = (pad, _i(h * 0.3), w - _i(w * 0.38), h - _i(h * 0.14))
    _paste_concept(img, icons.draw_option_g_tune_admin, box)
    arr_x = box[2] + _i(w * 0.02)
    arr_y = (box[1] + box[3]) // 2
    draw.line([(box[2], arr_y), (arr_x + _i(w * 0.08), arr_y)], fill=ACCENT_DIM, width=max(2, _i(h * 0.006)))
    draw.polygon(
        [
            (arr_x + _i(w * 0.1), arr_y),
            (arr_x + _i(w * 0.06), arr_y - _i(h * 0.03)),
            (arr_x + _i(w * 0.06), arr_y + _i(h * 0.03)),
        ],
        fill=ACCENT,
    )
    preview_box = (arr_x + _i(w * 0.12), _i(h * 0.32), w - pad, h - _i(h * 0.16))
    _paste_concept(img, icons.draw_option_a_layout_blocks, preview_box)

    _draw_tagline(
        draw,
        w,
        h,
        ("Guided setup.", "Live in minutes."),
        align="right",
        margin_y=0.08,
        size_ratio=0.048,
    )


def banner_live_admin_bar(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    """Wide top bar + drawer peek (website hero)."""
    pad = _i(w * 0.04)
    box = (pad, _i(h * 0.08), w - pad, h - _i(h * 0.22))
    _paste_concept(img, icons.draw_option_c_topbar_drawer, box)

    glow_r = _i(h * 0.45)
    gx = w - _i(w * 0.12)
    gy = _i(h * 0.35)
    for alpha, radius in ((24, glow_r), (14, _i(glow_r * 1.2))):
        layer = Image.new("RGBA", (w, h), (0, 0, 0, 0))
        ld = ImageDraw.Draw(layer)
        glow = (ACCENT[0], ACCENT[1], ACCENT[2], alpha)
        ld.ellipse((gx - radius, gy - radius, gx + radius, gy + radius), fill=glow)
        img.alpha_composite(layer)

    _draw_tagline(
        draw,
        w,
        h,
        (
            "Top bar shortcuts",
            "Badges & slide-out drawers",
        ),
        align="left",
        margin_x=0.04,
        margin_y=0.08,
        size_ratio=0.048,
    )


def banner_typography(img: Image.Image, draw: ImageDraw.ImageDraw, w: int, h: int) -> None:
    title = "EdminBoost"
    title_fs = max(28, _i(h * 0.22))
    tfont = _font(title_fs)
    tw, th = _text_wh(draw, title, tfont)
    lx = _i(w * 0.08)
    ly = _i(h * 0.28)
    draw.text((lx, ly), title, fill=SURFACE, font=tfont)
    _rr(draw, (lx, ly + th + _i(h * 0.04), lx + tw, ly + th + _i(h * 0.04) + max(4, _i(h * 0.014))), 2, ACCENT)

    sub_fs = max(14, _i(h * 0.065))
    sfont = _font(sub_fs, bold=False)
    sub = "Admin white label · Menu editor · Theme · Top bar"
    draw.text((lx, ly + th + _i(h * 0.12)), sub, fill=(190, 196, 210, 255), font=sfont)

    # Outline L-frame on the right
    rx0 = _i(w * 0.58)
    ry0 = _i(h * 0.12)
    rw = w - rx0 - _i(w * 0.06)
    rh = h - ry0 - _i(h * 0.12)
    s = min(rw, rh)
    inset = _i(s * 0.08)
    top_h = _i(s * 0.14)
    side_w = _i(s * 0.22)
    ox = rx0 + (rw - s) // 2
    oy = ry0 + (rh - s) // 2
    x1 = ox + s - inset
    y1 = oy + s - inset
    top_y1 = oy + inset + top_h
    side_x1 = ox + inset + side_w
    ow = max(3, _i(h * 0.012))
    _rr(draw, (ox + inset, oy + inset, x1, top_y1), _i(s * 0.04), (0, 0, 0, 0), ACCENT, ow)
    _rr(draw, (ox + inset, top_y1 - _i(s * 0.01), side_x1, y1), _i(s * 0.04), (0, 0, 0, 0), ACCENT_DIM, ow)
    _rr(
        draw,
        (side_x1 + _i(s * 0.03), top_y1 + _i(s * 0.03), x1 - _i(s * 0.03), y1 - _i(s * 0.03)),
        _i(s * 0.04),
        (0, 0, 0, 0),
        OUTLINE,
        max(2, ow // 2),
    )


BannerFn = Callable[[Image.Image, ImageDraw.ImageDraw, int, int], None]

CONCEPTS: tuple[tuple[str, BannerFn], ...] = (
    ("concept-01-command-center", banner_command_center),
    ("concept-02-before-after", banner_before_after),
    ("concept-03-four-pillars", banner_four_pillars),
    ("concept-04-theme-hero", banner_theme_hero),
    ("concept-05-menu-editor", banner_menu_editor),
    ("concept-06-typography", banner_typography),
)


def _render_banner(draw_fn: BannerFn, out_w: int, out_h: int) -> Image.Image:
    sw = out_w * SUPERSAMPLE
    sh = out_h * SUPERSAMPLE
    img = _banner_gradient(sw, sh)
    draw = ImageDraw.Draw(img, "RGBA")
    draw_fn(img, draw, sw, sh)
    return img.resize((out_w, out_h), Image.Resampling.LANCZOS).filter(
        ImageFilter.UnsharpMask(radius=1.0, percent=120, threshold=2)
    )


def _save_png(img: Image.Image, path: Path) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    img.save(path, format="PNG", compress_level=9, optimize=True)


def main() -> None:
    BANNERS_DIR.mkdir(parents=True, exist_ok=True)
    for folder, draw_fn in CONCEPTS:
        out_dir = BANNERS_DIR / folder
        for w, h in (BANNER_1X, BANNER_2X):
            fname = f"banner-{w}x{h}.png"
            path = out_dir / fname
            _save_png(_render_banner(draw_fn, w, h), path)
            print(f"Wrote {path}")


if __name__ == "__main__":
    main()
