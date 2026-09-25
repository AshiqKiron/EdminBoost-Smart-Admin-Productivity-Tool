# White Label overview

**White Label** lets agencies rebrand how EdminBoost appears in wp-admin and optionally replace the default WordPress admin footer with **system status** details (PHP version, memory, IP, and similar). It is aimed at client handoff and support workflows—not public-site branding.

## Where to find it

1. Log in as an **Administrator** (`manage_options`).
2. Open **EdminBoost** in the left menu.
3. Click the **White Label** tab in the Command Center sub-navigation (tab-only page; not listed separately in the sidebar).

Admin URL pattern:

`wp-admin/admin.php?page={plugin-slug}-white-label`

## What White Label includes

| Area | What it does |
|------|----------------|
| **Master toggle** | Turns white-label behavior on or off. |
| **Hide WordPress footer credit** | Clears the left admin footer “Thank you for creating with WordPress” line when active. |
| **System status footer** | Replaces the **right** admin footer line with selected server/site stats. |
| **Plugin rebranding** | Changes EdminBoost’s **Plugins** screen row and **admin menu** label (empty fields fall back to plugin defaults). |
| **Admin favicon** | Configured on **Theme (Appearance) → extras**; output in wp-admin via White Label hooks (see [Admin favicon](admin-favicon.md)). |

Live previews on the White Label page:

- **System status** — `#edminboost-wl-status-preview`
- **Plugin row + menu** — `#edminboost-wl-rebrand-preview`

## When White Label actually runs

Runtime branding and footer filters apply only when **all** of the following are true:

1. EdminBoost plugin **enabled** (global toggle).
2. **Pro or Agency** is active on the site (`EDMINBOOST_Pro::is_active()` — billing plan or `edminboost_is_pro_active` filter).
3. **Enable white-label branding** is checked and saved.

If the master toggle is off, settings are stored but filters do not change wp-admin.

## WordPress.org vs premium builds

Per [Free vs Pro](../plans-and-billing/free-vs-pro.md), the **WordPress.org** distribution is documented as including **White Label with no in-plugin license locks**.

**Premium Free** builds may show a **Pro** badge on the White Label form and lock fields until Pro is active; sanitizer may reset `white_label` to defaults on save when not licensed.

## Settings storage

White Label options live under:

`edminboost_settings['white_label']`

Uninstall removes plugin options via `uninstall.php` (including white label fields).

## Related pages

| Topic | Doc |
|-------|-----|
| Enable toggle, hide credit, plugin rebrand | [Enable branding and plugin rebrand](enable-branding-and-plugin-rebrand.md) |
| Favicon in wp-admin | [Admin favicon](admin-favicon.md) |
| Status footer toggles and preview | [System status footer](system-status-footer.md) |
| Command Center navigation | [Command Center overview](../command-center/overview.md) |
| Theme extras (favicon field) | [Theme (Appearance)](../command-center/theme-appearance.md) |

## Tips

- Turn on **Enable white-label branding** first; dependent sections stay visually disabled until then (JavaScript syncs `aria-disabled` on status and rebrand blocks).
- Leave rebrand text fields **empty** to keep the original plugin name, description, author, and menu label — only filled fields override defaults.
- System status appears on the **right** footer; hiding the WordPress credit affects the **left** footer only.
- For a fully custom admin look, combine White Label with [Theme](../command-center/theme-appearance.md) and [Menu Studio](../command-center/menu-studio.md).

## Privacy note

Optional **IP address** in the status footer reads `SERVER_ADDR` on the server (hosting context), not the visitor’s browser IP. No data is sent externally.
