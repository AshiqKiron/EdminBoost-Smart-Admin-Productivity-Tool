# Command Center overview

The **Command Center** is EdminBoost’s admin hub for customizing how WordPress looks and how you navigate it. It configures:

- **Layout presets** — bundled top bar links and sidebar structure for common scenarios and roles.
- **Theme (Appearance)** — color skins, light/dark mode, fonts, and admin bar declutter options.
- **Top Bar** — drag-and-drop shortcuts on the WordPress admin bar (with optional slide-out drawers).
- **Menu Studio** — reorder, hide, style, and extend the left admin menu.

Productivity, Security, Performance, and White Label live on separate tabs in the same Command Center navigation bar. Billing and Settings are linked from the WordPress sidebar under **EdminBoost**.

## Where to find it

In wp-admin, open **EdminBoost** in the left menu. You need the **Administrator** capability (`manage_options`).

| Screen | Sidebar label | Purpose |
|--------|---------------|---------|
| [Dashboard](dashboard.md) | Dashboard | Setup wizard or at-a-glance overview |
| [Layout Presets](layout-presets.md) | Layouts | Templates, role assignments, visibility matrix |
| [Theme (Appearance)](theme-appearance.md) | Theme | Visual skins, mode, fonts, declutter |
| [Top Bar](top-bar.md) | Top Bar | Admin bar link builder |
| [Menu Studio](menu-studio.md) | Menu Studio | Sidebar layout and styling |

Tab-only pages (Command Center sub-nav, not in the sidebar): **Productivity**, **Security**, **Performance**, **White Label**.

## How the pieces fit together

```text
Layout preset          Theme (Appearance)
     │                        │
     ├─ top bar links ────────┼──► Live WordPress admin bar (Command Center bar)
     └─ sidebar shape ────────┼──► Menu Studio applies order/hide/colors
                              │
                              └──► CSS tokens color wp-admin + plugin UI
```

1. **Choose a layout preset** (or build a custom layout in Top Bar + Menu Studio). Presets set both the **top bar** and a starting **sidebar** configuration.
2. **Pick a theme** for colors and light/dark behavior across wp-admin and EdminBoost screens.
3. **Refine the top bar** — add links, choose redirect vs drawer, optional live badges.
4. **Turn on Menu Studio** when you need sidebar reorder, hidden items, custom links, or menu-specific colors.

Settings are stored in the WordPress option `edminboost_settings` under the `command_center` key. Saving on any Command Center form keeps the plugin **enabled** (partial saves do not turn EdminBoost off).

## First visit: setup wizard

Until setup is complete, the Dashboard shows a **four-step wizard** instead of the overview cards:

1. **Layout** — pick a scenario or role-based layout preset.
2. **Color theme** — choose a visual skin and mode.
3. **Top bar** — review preset links (fine-tune later on Top Bar).
4. **Review** — confirm and finish.

Setup is considered complete when **onboarding is marked finished** or you have **at least one top bar item** saved. After that, the Dashboard shows overview cards and the full Command Center tab bar.

See [Dashboard](dashboard.md) for wizard and overview details.

## Navigation inside Command Center

After setup, a horizontal **tab bar** appears at the top of each Command Center screen. Clicking a tab loads that page **without a full reload** when possible (AJAX). Links still work as normal URLs if JavaScript is unavailable.

- **Save** — persists the current form via AJAX (same data as submitting to `options.php`).
- **Reset to defaults** — reloads the current tab with factory default **form values**; you must click **Save** again to write them to the database.

Info icons beside fields (on pages other than Dashboard) open short help tooltips.

## Live behavior on your site

When EdminBoost is enabled and configured:

- **Top bar items** appear on `#wpadminbar` for logged-in users who are allowed to see them (role visibility and capabilities apply).
- **Drawer links** open admin pages in a slide-out panel instead of navigating away; width, animation, and badge styling are configured on Top Bar (Panel & badges section).
- **Declutter toggles** on Theme remove core admin bar items (WordPress logo, comments, “Howdy”, updates, New, Customize).
- **Visual theme** CSS applies across wp-admin when enabled, including core top bar and sidebar menu colors (Menu Studio custom colors override theme sidebar tokens when both are active).
- **Menu Studio** runs only when enabled in its settings; it reorders, hides, and registers custom menu entries at runtime.

Badge counts (orders, comments, updates, etc.) read **local WordPress/WooCommerce/WPForms data only** — no external API calls.

## WordPress.org vs premium builds

The **WordPress.org** build includes the full Command Center with **no in-plugin license locks**. See [Free vs Pro](../plans-and-billing/free-vs-pro.md).

Some **premium distribution builds** may show Pro badges and limits (for example drawer interactions, certain layout/theme presets, or role visibility checkboxes). Those limits are described on the in-plugin **Billing** page and in [Free vs Pro](../plans-and-billing/free-vs-pro.md). Upgrade links open the external pricing site in a new tab; checkout does not run inside WordPress.

## Related documentation

| Topic | Page |
|-------|------|
| Setup and overview cards | [Dashboard](dashboard.md) |
| Presets and roles | [Layout Presets](layout-presets.md) |
| Skins and declutter | [Theme (Appearance)](theme-appearance.md) |
| Admin bar builder | [Top Bar](top-bar.md) |
| Sidebar editor | [Menu Studio](menu-studio.md) |
| Plans | [Plans overview](../plans-and-billing/plans-overview.md) |
| Export/import settings | EdminBoost → **Settings** (backup section may be Pro-gated in premium builds) |

## Tips

- **Start with a preset**, then switch to **Custom** in the layout picker when you only need small top bar or sidebar tweaks.
- **Role assignments** (Layout Presets) let Editors, Authors, etc. get a different preset top bar and sidebar baseline; stored **custom menu links** are merged back in when a role preset applies.
- **Protected menu items** — Dashboard, Plugins, and EdminBoost cannot be hidden from the role matrix’s protected top-level rows.
- If the top bar looks empty, open **Top Bar**, add links from the discovered list, and **Save** (that page sets a save marker so an empty canvas clears the layout intentionally).
