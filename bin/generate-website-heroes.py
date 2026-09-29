#!/usr/bin/env python3
"""Generate website hero images (900×490) for EdminBoost marketing concepts."""

from __future__ import annotations

import importlib.util
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
HEROES_DIR = ROOT / "assets" / "heroes"
HERO_W, HERO_H = 900, 490

_BANNERS_PATH = ROOT / "bin" / "generate-plugin-banners.py"
_spec = importlib.util.spec_from_file_location("edminboost_banners", _BANNERS_PATH)
banners = importlib.util.module_from_spec(_spec)
assert _spec.loader is not None
_spec.loader.exec_module(banners)

HERO_CONCEPTS: tuple[tuple[str, banners.BannerFn], ...] = (
    ("hero-01-command-center", banners.banner_command_center),
    ("hero-02-before-after", banners.banner_before_after),
    ("hero-03-four-pillars", banners.banner_four_pillars),
    ("hero-04-theme-hero", banners.banner_theme_hero),
    ("hero-05-menu-editor", banners.banner_menu_editor),
    ("hero-06-typography", banners.banner_typography),
    ("hero-07-live-admin-bar", banners.banner_live_admin_bar),
    ("hero-08-stack-replacement", banners.banner_stack_replacement),
    ("hero-09-role-split", banners.banner_role_split),
    ("hero-10-white-label-trio", banners.banner_white_label_trio),
    ("hero-11-menu-drag", banners.banner_menu_drag),
    ("hero-12-role-presets", banners.banner_role_presets),
    ("hero-13-wizard-live", banners.banner_wizard_live),
)


def main() -> None:
    HEROES_DIR.mkdir(parents=True, exist_ok=True)
    fname = f"hero-{HERO_W}x{HERO_H}.png"
    for folder, draw_fn in HERO_CONCEPTS:
        out_dir = HEROES_DIR / folder
        path = out_dir / fname
        img = banners._render_banner(draw_fn, HERO_W, HERO_H)
        banners._save_png(img, path)
        print(f"Wrote {path}")


if __name__ == "__main__":
    main()
