# Enable branding and plugin rebrand

This page covers the **White Label** master controls and **Plugin rebranding** section on **EdminBoost → White Label**.

## Open the settings page

Command Center tab **White Label** → single form with **Save white label settings** and **Reset to defaults** (shared form footer).

The entire form may be wrapped in a Pro section on premium Free builds; see [White Label overview](overview.md).

## Enable white-label branding

**Enable white-label branding** (`white_label.enabled`) is the master switch.

When **off**:

- Stored values remain in the database.
- **No** footer, plugin row, or menu label filters run (`EDMINBOOST_White_Label::is_active()` is false).
- **System status footer** and **Plugin rebranding** sections appear disabled in the UI until the master toggle is on.

When **on** (and Pro active + plugin enabled):

- White-label hooks apply across wp-admin as described below.

Save after changing the master toggle.

## Hide default WordPress footer credit

**Hide default WordPress footer credit** (`hide_wp_footer_credit`) clears the **left** admin footer text via the `admin_footer_text` filter when white label is active.

It does **not** remove EdminBoost or third-party footer injections unless they use the same core filter.

The **right** footer (version line) is handled separately by [System status footer](system-status-footer.md).

## Plugin rebranding

The **Plugin rebranding** section (`#edminboost-wl-rebrand-section`) includes text fields and a live preview of the **Plugins** screen row and **admin sidebar** menu item.

| Field | Setting key | Effect when non-empty |
|-------|-------------|------------------------|
| **Plugin name** | `plugin_name` | Plugins list **Name** column for EdminBoost. |
| **Plugin description** | `plugin_description` | Plugins list description line. |
| **Author / agency name** | `plugin_author` | Plugins list author. |
| **Plugin URL** | `plugin_uri` | Sets both **Plugin URI** and **Author URI** on the Plugins row. |
| **Admin menu label** | `menu_label` | Top-level **EdminBoost** menu text in the left admin menu. |

### Empty fields = defaults

If a field is left blank, EdminBoost keeps the **original plugin header** values from `edminboost-admin-customization.php` (name, description, author, URI) and the default menu label **EdminBoost**.

The preview partial uses `data-default-*` attributes so you can see fallbacks before saving.

### What rebranding does not change

- Plugin **folder/slug**, **text domain**, or **file names** on disk.
- Capability requirements (`manage_options` still required).
- Command Center **tab labels** (still “White Label”, “Theme”, etc.) unless you customize menu label only for the root menu item.
- Other plugins’ listings.

Runtime filters:

- `all_plugins` — `filter_plugin_row()`
- `admin_menu` (priority 999) — `filter_menu_label()`

## Live preview

**Plugin rebranding** preview (`edminboost-white-label-plugin-preview.php`):

- Mock **Plugins → Installed Plugins** row.
- Mock **sidebar** menu entry.

Admin JavaScript (`initWhiteLabelRebrandPreview`) updates the preview as you type.

## Saving

- Form class: `.edminboost-cc-form` / `.edminboost-settings-form`.
- Hidden `edminboost_settings[enabled]=1` prevents partial saves from disabling the whole plugin.
- AJAX save uses the same nonce as other Command Center pages.

On **premium Free** without Pro, `EDMINBOOST_Pro::enforce_plan_limits()` may reset `white_label` to defaults during sanitize.

## Suggested agency workflow

1. Enable **Enable white-label branding**.
2. Set **Plugin name** and **Admin menu label** to your client-facing product name (e.g. “Site Toolkit”).
3. Set **Author / agency name** and **Plugin URL** to your agency site.
4. Optionally enable [System status footer](system-status-footer.md) for support visibility.
5. Set [Admin favicon](admin-favicon.md) on **Theme** for a polished wp-admin tab icon.
6. Save and verify on **Plugins** and the admin menu as a non-admin test user is not required for label checks—admins see the menu.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| Menu label unchanged | Master toggle on; Pro active; non-empty **Admin menu label** saved. |
| Plugins row unchanged | Master toggle on; fill **Plugin name** (or other fields); clear object cache. |
| Section greyed out | Enable master **Enable white-label branding** toggle. |
| Settings revert after save | Pro enforcement on premium Free; confirm license or use WordPress.org build. |
| Preview differs from live | Save form; hard refresh wp-admin; preview uses sample layout, not full wp-admin chrome. |

## See also

- [White Label overview](overview.md)
- [System status footer](system-status-footer.md)
- [Admin favicon](admin-favicon.md)
