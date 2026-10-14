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
4. **Ship / QA** — `edminboost-qa.mdc`; run `composer test` and `npm run test:e2e` for CC/settings work; regenerate WordPress.org screenshots with `node bin/capture-wporg-screenshots.mjs` when readme assets change.
5. **Local full Pro UI (MAMP dev tree)** — copy `bin/edminboost-localhost.sample.php` to gitignored `edminboost-localhost.php`, or run `bin/setup-qa-site.sh`. PHPUnit and release zips never include that file.

## Canonical identifiers

| Concept | Value |
|---------|--------|
| Plugin slug / bootstrap file | `edminboost-admin-customization` |
| Text domain (`EDMINBOOST_TEXT_DOMAIN`) | `edminboost-admin-customization` (must match header `Text Domain`) |
| Hook/option prefix | `edminboost` / `EDMINBOOST_` (10-char prefix — **not** the text domain) |
| Local licensed Pro QA (dev only) | Gitignored `edminboost-localhost.php` (from `bin/edminboost-localhost.sample.php`); never in release zips |
| PHPUnit / CI | `EDMINBOOST_DISABLE_LOCALHOST` (see `tests/bootstrap.php`); `EDMINBOOST_TEST_WPORG=1` simulates WP.org package |

The git checkout folder name (e.g. `EdminBoost - Smart Admin Productivity Tool`) is not the slug. Do not use legacy domains such as `edminboost-smart-admin-productivity-tool` in `__()` / `_e()` calls — WordPress.org Plugin Check expects the header domain.

## Keeping rules in sync

When you change architecture, admin pages, Pro gating, i18n, or QA scripts:

1. Update the relevant `.mdc` file(s) in the same PR/commit as the code when possible.
2. After slug or text-domain work, grep the tree for wrong domains (see `edminboost-standards.mdc` I18N).
3. After adding partials or JS entry points, extend `edminboost-command-center.mdc` and/or `admin-assets.mdc` globs if new files fall outside existing patterns.
4. After changing `readme.txt` Screenshots copy or capture targets, update `bin/capture-wporg-screenshots.mjs` / `bin/seed-wporg-screenshot-demo.php` and regenerate `assets/screenshot-*.png` (see `edminboost-qa.mdc`).
5. Premium-only UI belongs under `admin/partials/pro/`, `admin/js/pro/`, or `admin/css/pro/` and PHP under `includes/pro/`. Release zips exclude dev paths via `bin/build-zip-lib.sh` (including `edminboost-localhost.php`); WordPress.org also omits `includes/pro/`, pro admin assets, repo-root `assets/`, and `docs/` (see `bin/build-wporg-zip.sh` / `bin/verify-wporg-zip.sh`). Premium zip injects `edminboost-premium.php` and `edminboost_premium_load_pro_package()` into staged bootstrap — dev checkout bootstrap stays WordPress.org–safe until localhost shim or premium zip paths load pro code.
6. Shared gating: prefer filters in core (no direct `EDMINBOOST_Pro` calls). Licensed menus/assets: `edminboost_should_register_licensed_admin_menus`, `edminboost_should_enqueue_licensed_admin_assets` (premium package when `EDMINBOOST_Pro::has_licensed_admin_ui()`). WordPress.org strips Pro-only stored settings via `EDMINBOOST_Settings::strip_free_build_product_features()`. Licensed CC partials: core `EDMINBOOST_Admin::include_filtered_partials( $slot, ...$args )` on filter `edminboost_command_center_partial_paths`; premium maps slot keys in `EDMINBOOST_Pro::LICENSED_PARTIAL_FILTERS` (`edminboost_mapper_after_canvas_partials`, `edminboost_presets_role_matrix_head_partials`, `edminboost_presets_role_matrix_row_partials`, `edminboost_presets_role_matrix_after_table_partials`, `edminboost_theme_settings_schedule_partials`, `edminboost_theme_extras_schedule_preview_partials`, `edminboost_security_page_extra_partials`, `edminboost_menu_studio_custom_link_partials`, `edminboost_menu_studio_display_mode_partials`). Settings JSON backup loads via `EDMINBOOST_Pro::include_licensed_partial()` on `pro/edminboost-settings-page.php`. Other pro partials use direct `is_readable()` includes — e.g. `edminboost-animation-speed-field.php`, `edminboost-overview-topbar-link-meta.php`, `edminboost-theme-settings-licensed-desc.php`. Settings/White Label: `EDMINBOOST_Admin_Licensed` (submenus + `edminboost_render_cc_page` / `edminboost_cc_page_title`). Sanitize/help: `EDMINBOOST_Command_Center_Role_Visibility_Pro` + `EDMINBOOST_Settings_Sanitize_Pro` on `edminboost_sanitize_command_center`; `EDMINBOOST_Setting_Help_Pro` on `edminboost_setting_help_tooltips`. Runtime role visibility: `edminboost_menu_studio_for_user`, `edminboost_top_bar_items_for_user`. Top Bar item drawer UI: `edminboost_mapper_item_sidebar_partial`. Licensed Dashboard copy: `edminboostData.hasLicensedAdminUi` + `edminboost_admin_script_data`. Pro admin JS/CSS when `edminboost_should_enqueue_licensed_admin_assets` is true (not merely `is_direct_build()`).

Current plugin version is defined in `EDMINBOOST_VERSION` / plugin header / `readme.txt` `Stable tag` — do not hardcode version numbers in rules unless documenting a one-time migration.
