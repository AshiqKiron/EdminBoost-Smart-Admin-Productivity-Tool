# Menu Studio

**EdminBoost → Menu Studio** customizes the WordPress **left admin menu**: reorder items, hide entries, add **custom links** (top-level or submenu), adjust **layout and typography**, and optionally apply **sidebar-specific colors**. It runs only when **Menu Studio is enabled** on this page.

## Enable Menu Studio

1. Open **Menu Studio**.
2. Turn on **Enable Menu Studio**.
3. Configure the canvas, layout, and colors.
4. Click **Save** (sets `_menu_studio_save` so sidebar settings always persist from this form).

When disabled, WordPress uses the default menu order (subject to other plugins); preset sidebar data may still exist in settings but is not applied until enabled.

## Builder (same ideas as Top Bar)

- **Discovered menu** — all top-level and submenu entries from your site.
- **Canvas** — desired order; drag from discovered list or reorder on canvas.
- **Hide** — move items to hidden state or use hide controls (hidden slugs stored in `hidden_items`).

**Protected slugs** cannot be hidden or removed from order:

- Dashboard (`index.php`)
- Plugins (`plugins.php`)
- EdminBoost plugin root page

Submenu hiding uses `remove_submenu_page()` with the correct parent; top-level hiding uses `remove_menu_page()`.

## Custom sidebar links

Add links with:

- **Label**
- **Path** — admin-relative path or URL (validated character set; `#` fragments allowed)
- **Icon** — dashicon
- **Parent** — optional; set parent slug to nest under an existing top-level menu

Custom entries register on `admin_menu` with slugs like `edminboost_ms_{id}`.

**Premium Free:** custom links may be Pro-gated. **WordPress.org** includes custom links.

Saved custom items are **preserved** when a [layout preset](layout-presets.md) or **role assignment** replaces other sidebar settings — EdminBoost merges stored custom links back when resolving menu config for a user.

## Layout and typography

| Setting | Purpose |
|---------|---------|
| **Menu width** | Sidebar width in px (approx. 120–300). |
| **Font size** | Menu text size (approx. 10–24 px). |
| **Line height** | Row height (approx. 12–36 px). |
| **Letter spacing** | Tracking (approx. -2 to 6 px). |
| **Display mode** | **Icons + text**, **Icons only**, or **Text only** (display mode may be Pro-gated on premium Free). |

Live **layout preview** on the page reflects these values as you adjust controls.

### Padding

Advanced padding fields control wrapper and submenu inset (top/right/bottom/left in px) for fine-tuned spacing.

## Sidebar colors

When Menu Studio is enabled, **menu colors** are saved (the UI submits colors as active). Color groups include:

- Parent item background, text, hover
- Submenu background, text, hover
- Active/highlight and notification badge tones

Color pickers sync with hex text fields; the **color preview** panel updates live.

Menu Studio colors **override** theme sidebar tokens from [Theme (Appearance)](theme-appearance.md) while `edminboost-menu-studio-colors` is active on `body`.

## Role visibility and presets

- **[Layout Presets](layout-presets.md) role assignments** load a preset sidebar baseline per role.
- **Role visibility matrix** adds per-role hidden slugs on top of Menu Studio hides.
- **Capabilities** still control whether a user can access a page; hiding only removes the menu entry.

## Assets and body classes

When active, Menu Studio enqueues `edminboost-admin-menu.css` and inline CSS variables for layout and colors. Body classes include:

- `edminboost-menu-studio-active`
- `edminboost-menu-studio-colors` (when colors apply)
- `edminboost-menu-studio-display--{both|icon|text}`

## Saving from other pages

Only the Menu Studio form should set `_menu_studio_save`. Applying a **layout preset** on Layouts or Dashboard replaces `menu_studio` baseline from the preset on that save — plan custom sidebar work accordingly or save a custom preset first.

## Tips

- Hide noisy plugin menus for clients, expose only Dashboard + key CPTs.
- Nest custom links under **Settings** or **Tools** for a cleaner top level.
- Use **icons only** mode for a compact sidebar on small laptops (when available on your build).
- After major plugin installs, reopen Menu Studio — new menu slugs appear in the discovered list.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| Changes not visible | **Enable Menu Studio** toggle saved; clear object cache; conflicting plugin reordering menus. |
| Item reappears | Plugin re-registers menu on every request with a new slug; hide again or disable conflicting tool. |
| Custom link 404 | Path must be valid admin URL; external URLs open in same window unless path is full URL. |
| Colors wrong | Menu Studio colors override theme sidebar; reset color fields or adjust theme. |
| Order resets after preset | Applying preset replaces sidebar baseline — merge custom links should still return; re-order in canvas and save. |

## See also

- [Command Center overview](overview.md)
- [Layout Presets](layout-presets.md)
- [Theme (Appearance)](theme-appearance.md)
- [Top Bar](top-bar.md)
