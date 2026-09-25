# Theme (Appearance)

**EdminBoost → Theme** (admin slug `-appearance`) controls the **visual skin** of wp-admin and EdminBoost UI: color presets, light/dark/auto mode, fonts, admin chrome extras, and **admin bar declutter** toggles. Panel width, drawer animation, and badge styling moved to **Top Bar → Panel & badges** — see [Top Bar](top-bar.md).

## What the theme affects

When EdminBoost is enabled and you are logged in, the active theme:

- Applies **CSS color tokens** to EdminBoost screens, the **Command Center bar**, the **drawer**, and (when no Menu Studio sidebar colors override) the core **`#wpadminbar`** and **`#adminmenu`**.
- Adds body classes such as `edminboost-theme-active`, preset id, mode, and font stack.
- Uses **system font stacks only** — no Google Fonts or remote font requests.

Menu Studio **custom sidebar colors** take precedence over theme sidebar tokens when Menu Studio colors are enabled.

## Theme preset library

Built-in skins include (among others):

**Default**, **Midnight**, **Terminal**, **Neon Outrun**, **Vapor**, **Desert**, **Dracula**, **Nord**, **Solarized**, **Sakura**, **Ocean**, **Forest**, **Tron**, **Night City**, **Pip-Boy**, **Portal**, **Gotham**, **Citadel**, **Blade Noir**, **Hyrule**, and **Custom**.

Each preset defines six tokens used across the UI:

| Token | Typical use |
|-------|-------------|
| Accent | Buttons, links, highlights |
| Surface | Panels and cards |
| Text | Primary text |
| Top bar | Admin bar background |
| Sidebar | Left menu background |
| Content area | Main admin content background |

**Custom** reveals color pickers and hex fields for each token.

**Premium Free tier:** some skins may be marked Pro in premium builds; WordPress.org includes all skins. See [Free vs Pro](../plans-and-billing/free-vs-pro.md).

## Color mode

| Mode | Behavior |
|------|----------|
| **Light** | Light surfaces regardless of OS. |
| **Dark** | Dark surfaces. |
| **Auto (system)** | Follows the user’s OS light/dark preference where supported. |

### Scheduled dark mode

Optional **schedule** switches to dark mode between a **start** and **end** time (24-hour fields, e.g. 18:00–06:00). Enable the schedule toggle, then set times in the dependent options panel.

Scheduled dark mode may be Pro-gated in premium Free builds; available on WordPress.org.

## Font

Choose a **font stack** for EdminBoost-themed admin chrome:

- WordPress default, System UI, common sans/serif/mono stacks (Arial, Verdana, Tahoma, Trebuchet, Lucida, Palatino, Humanist sans, Monospace, Serif, Rounded UI).

**Font size** (Appearance extras) adjusts base sizing for themed UI (clamped to a readable range).

## Appearance extras

The **extras** section includes live preview sync for:

- **Admin font size** (range + number input).
- **Admin background color** and optional **background image** (media library attachment).
- **Admin favicon** (attachment id) for wp-admin.
- **Post status row colors** — optional hex overrides for publish, pending, future, private, draft, trash in list tables when theme is active.

Use the preview panel beside these fields to see changes before saving.

## Admin bar declutter

Separate from layout presets, **declutter** checkboxes remove core **WordPress admin bar** nodes when enabled:

| Toggle | Hides |
|--------|--------|
| WordPress logo | `wp-logo` |
| Updates counter | Update count badge area |
| Howdy / account phrasing | “Howdy” portion of my-account |
| Comments | Comments admin bar node |
| New content | “+ New” dropdown |
| Customize | Customize link (when shown) |

Declutter affects **core** admin bar items, not your EdminBoost top bar shortcuts. A live **declutter preview** on this page mirrors which default items disappear.

EdminBoost **top bar links** are configured on [Top Bar](top-bar.md).

## Saving and reset

- **Save** — writes `command_center.theme` and `command_center.behavior` declutter keys (behavior declutter only on this page; drawer/badge behavior on Top Bar).
- **Reset to defaults** — reloads factory form values for the current tab; click **Save** to persist.

Theme changes on this page update live preview classes on `body` while you edit. AJAX save responses refresh localized `themeSettings` in admin JS on non-dashboard pages.

## Dashboard vs Theme page

The **Dashboard** overview includes a compact theme picker with swatch preview. The **Theme** page adds declutter, full extras, custom colors, and scheduled dark mode. Both write to the same `command_center.theme` settings.

## Tips

- Pick **Auto** mode if editors mix light and dark OS settings.
- If sidebar colors look wrong, check **Menu Studio** — custom menu colors override theme sidebar tokens.
- Declutter is ideal for client sites where you expose only EdminBoost shortcuts on the top bar.
- Custom post status colors help teams scan list tables quickly without changing WordPress core.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| Theme not visible on front end | Visual theme targets wp-admin; Command Center bar/drawer CSS may load on the front end when the admin bar shows and drawer items exist. |
| Colors revert after save | Confirm preset is not reset by plan enforcement on premium Free; check for conflicting Menu Studio colors. |
| Dark schedule ignored | Ensure schedule toggle is on, times are valid, and save succeeded. |
| Core admin bar still cluttered | Declutter toggles must be saved; some nodes reappear if another plugin re-registers them. |

## See also

- [Command Center overview](overview.md)
- [Top Bar](top-bar.md) — drawer width, badges, animation
- [Menu Studio](menu-studio.md) — sidebar color overrides
- [Dashboard](dashboard.md) — quick theme picker
