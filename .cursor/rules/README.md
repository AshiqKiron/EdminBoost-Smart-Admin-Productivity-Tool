# EdminBoost Cursor rules

Agent and editor guidance for this repo. Rules live as `.mdc` files in this folder; Cursor loads them by `alwaysApply`, `description`, and `globs`.

## Always applied

| File | Role |
|------|------|
| `edminboost-project.mdc` | Architecture, directory layout, admin pages, naming, hooks, builds (WordPress.org vs premium) |
| `edminboost-standards.mdc` | WPCS, i18n, a11y, admin UX, performance, privacy, uninstall, release |
| `edminboost-compliance.mdc` | Security gate + WordPress.org checklist — run before shipping code |

## Scoped by path

| File | Globs (summary) | Role |
|------|-----------------|------|
| `edminboost-command-center.mdc` | CC PHP, CC partials (incl. `admin/partials/pro/`), pro CC modules, CC bar/theme/menu CSS | Dashboard, presets, mapper, Menu Studio, theme, billing, white label, live admin bar |
| `edminboost-features.mdc` | `includes/features/**/*.php` | Feature module registration and current feature catalog |
| `admin-assets.mdc` | `admin/**/*.{js,css}` incl. `admin/{js,css}/pro/` + CC/theme/menu CSS | `edminboost-admin.js`, `edminboost-admin-pro.js`, previews, AJAX save, CSS BEM classes |
| `wordpress-php.mdc` | `**/*.php` | PHP headers, Settings API, class wiring, option shape |
| `edminboost-qa.mdc` | `tests/**`, `bin/**`, composer/package/phpunit config | PHPUnit, Playwright, release scripts, manual matrix |

## How to use

1. **Any change** — skim `edminboost-compliance.mdc` self-review checklist.
2. **Command Center / settings UI** — `edminboost-command-center.mdc` + `admin-assets.mdc`.
3. **New feature module** — `edminboost-features.mdc` + `wordpress-php.mdc`.
4. **Ship / QA** — `edminboost-qa.mdc`; run `composer test` and `npm run test:e2e` for CC/settings work.

## Canonical identifiers

| Concept | Value |
|---------|--------|
| Plugin slug / bootstrap file | `edminboost-admin-customization` |
| Text domain (`EDMINBOOST_TEXT_DOMAIN`) | `edminboost-admin-customization` (must match header `Text Domain`) |
| Hook/option prefix | `edminboost` / `EDMINBOOST_` (10-char prefix — **not** the text domain) |

The git checkout folder name (e.g. `EdminBoost - Smart Admin Productivity Tool`) is not the slug. Do not use legacy domains such as `edminboost-smart-admin-productivity-tool` in `__()` / `_e()` calls — WordPress.org Plugin Check expects the header domain.

## Keeping rules in sync

When you change architecture, admin pages, Pro gating, i18n, or QA scripts:

1. Update the relevant `.mdc` file(s) in the same PR/commit as the code when possible.
2. After slug or text-domain work, grep the tree for wrong domains (see `edminboost-standards.mdc` I18N).
3. After adding partials or JS entry points, extend `edminboost-command-center.mdc` and/or `admin-assets.mdc` globs if new files fall outside existing patterns.
4. Premium-only UI belongs under `admin/partials/pro/`, `admin/js/pro/`, or `admin/css/pro/` and PHP under `includes/pro/` — WordPress.org zip excludes those paths (see `bin/build-wporg-zip.sh`).
5. Shared gating: prefer `EDMINBOOST_Plan::*` in core partials; implement licensed behavior in `includes/pro/` and extension slots on `edminboost_admin_extension` (`EDMINBOOST_Pro::ADMIN_EXTENSION_PARTIALS`: `settings_backup`, `mapper_look_section`, `security_login_redirects`, `menu_custom_link`, `menu_display_mode`, `presets_role_matrix_head`, `presets_role_matrix_cells`, `presets_role_visibility_help`, `theme_extras_schedule_preview`, `theme_schedule_dark_mode`). Top Bar item drawer UI also swaps via filter `edminboost_mapper_item_sidebar_partial`.

Current plugin version is defined in `EDMINBOOST_VERSION` / plugin header / `readme.txt` `Stable tag` — do not hardcode version numbers in rules unless documenting a one-time migration.
