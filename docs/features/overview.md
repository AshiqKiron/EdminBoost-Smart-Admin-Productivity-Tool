# Features overview

EdminBoost **feature tools** are optional toggles that change WordPress admin and site behavior. They are separate from the **Command Center** (layout, theme, top bar, Menu Studio) but use the same save flow and global **enable** switch.

## Where to configure features

Open **EdminBoost** in wp-admin, then use the Command Center **tab bar** (not the left sidebar):

| Tab | Focus |
|-----|--------|
| **Productivity** | Cleaner admin UI, dashboard, workflows, list tables |
| **Security** | Hardening, comments, login/logout redirects |
| **Performance** | Scripts, assets, Heartbeat API |

**White Label** is a separate Command Center tab (agency branding), not part of the feature registry.

**Settings** (sidebar) is for JSON backup/import only — not individual feature toggles.

Requires **Administrator** (`manage_options`).

## Global enable switch

All features respect the plugin-wide **Enabled** toggle on **EdminBoost → Settings** (and the hidden `enabled=1` field on Command Center saves). When EdminBoost is disabled, **no feature hooks run**.

Individual features stay off until you turn them on and **Save** on the relevant tab.

## How settings are stored

Feature options live under:

`edminboost_settings['features']`

Each feature has a stable **snake_case** id (for example `hide_admin_notices`, `disable_xmlrpc`). Defaults are applied on activation and merged when settings load. Legacy keys `admin_bar` and `admin_menu` are stripped on read — admin bar cleanup moved to **Theme → Declutter**; sidebar layout moved to **Menu Studio**.

## Saving

Productivity, Security, and Performance pages submit through the WordPress Settings API (`options.php`), intercepted by **AJAX Save** like other Command Center forms.

- **Save** — writes current toggles to the database.
- **Reset to defaults** — reloads factory form values for that tab; click **Save** again to persist.

Info icons beside fields open short help tooltips.

## What is not a “feature module”

These are configured elsewhere but often grouped with “admin productivity” in conversation:

| Capability | Where to configure |
|------------|------------------|
| Top bar shortcuts & drawer | Command Center → **Top Bar** |
| Admin bar declutter (WP logo, Howdy, etc.) | Command Center → **Theme** |
| Sidebar reorder / hide / custom links | Command Center → **Menu Studio** |
| Visual theme skins | Command Center → **Theme** |
| White label / system status footer | Command Center → **White Label** |

## Feature list by tab

### Productivity (9 tools)

- Hide routine admin notices (keep errors/warnings)
- Hide Screen Options and Help tabs
- Remove selected dashboard widgets
- Replace admin footer text
- Post/page duplicator row action
- Classic widgets screen
- Navigation menu duplication
- Custom post/page list columns (thumbnail, ID, meta key)
- Manual post ordering (Order column)

### Security (5 areas)

- Disable XML-RPC
- Disable RSS/Atom feeds
- REST API hardening (hide head link, disable guests)
- Disable comments per post type
- Role-based login and logout redirects

### Performance (5 areas)

- Disable emoji detection scripts (admin, front end, or both)
- Remove `ver` query strings from assets
- Remove Dashicons on the front end for visitors
- Disable embeds / oEmbed discovery
- Heartbeat API control (admin, editor, front end)

## WordPress.org vs premium builds

The **WordPress.org** build includes **all feature tools** with **no in-plugin license locks**. See [Free vs Pro](../plans-and-billing/free-vs-pro.md).

**Premium Free** builds may Pro-gate **login redirects** on Security (UI lock + sanitizer reset when not licensed). Other features remain available unless your distribution differs.

## Privacy and performance notes

- Features run **locally** in WordPress — no telemetry or external API calls from these toggles.
- Scope hooks to **admin** unless noted (emoji disable can target front end; Heartbeat and embeds can affect front end).
- Test **Security** and **Performance** changes on staging — disabling REST, feeds, or Heartbeat can affect plugins, mobile apps, or editors.

## Related documentation

- [Productivity tools](productivity-tools.md)
- [Security tools](security-tools.md)
- [Performance tools](performance-tools.md)
- [Command Center overview](../command-center/overview.md)
