# Productivity tools

**EdminBoost → Productivity** (Command Center tab) bundles admin UI cleanup, dashboard control, footer text, and editorial workflow helpers. Each toggle is optional; enable only what you need, then **Save**.

## Admin notices

**Hide routine admin notices**

- Hides non-critical admin notices (plugin promos, generic info).
- **Errors and warnings stay visible** so you do not miss serious issues.
- Uses scoped admin CSS on enqueue; includes a **live preview** panel that mirrors hidden vs visible notice types.

Best for: client sites cluttered with nags from many plugins.

## Screen tabs

**Hide Screen Options and Help tabs**

- Removes the **Screen Options** and **Help** tabs from the top of admin list and edit screens.
- Live **screen preview** shows the tab strip with items marked hidden when enabled.

Best for: simplified admin for roles that should not change screen columns or read core help tabs.

## Dashboard widgets

**Remove selected default dashboard widgets**

Master toggle plus checkboxes for individual core widgets:

| Widget option | Removes |
|---------------|---------|
| Welcome panel | “Welcome to WordPress” panel |
| Quick Draft | Quick Draft meta box |
| Activity | Activity widget |
| At a Glance | At a Glance summary |
| Site Health Status | Site Health dashboard widget |
| WordPress Events and News | Events and news widget |

Dependent options stay disabled until the master toggle is on. **Live dashboard preview** highlights removed widgets.

Does not remove third-party dashboard widgets unless they hook the same core widgets — only the listed core items are targeted.

## Admin footer

**Replace the default admin footer text**

- Master toggle plus a **custom footer text** field.
- Feature is active only when enabled **and** text is non-empty.
- Replaces the “Thank you for creating with WordPress” line (and similar) via `admin_footer_text`.

Best for: support links, agency credit (opt-in), or internal reminders. Not injected on the public site by default.

## Workflow tools

### Post and page duplicator

Adds a **Duplicate** row action on post and page list tables. Duplication runs through a secured admin action with capability checks. Default post types: **post** and **page** (stored in settings shape).

### Classic widgets screen

Disables the block-based widgets experience and restores the **classic widgets** admin screen (`after_setup_theme`).

Use when a site or team still relies on legacy widgets rather than block widget areas.

### Navigation menu duplication

Adds the ability to **duplicate a navigation menu** from the Menus admin screen (registers a duplicate action under `admin_menu`).

Useful when cloning menu structure for a new location or language.

## Custom list columns

**Add optional columns to post and page list tables**

Master toggle, then per **post** and **page**:

| Column | Shows |
|--------|--------|
| Featured image | Thumbnail when set |
| Post ID | Numeric ID |
| Meta key column | Value from a custom field key you enter |

Enter a single **meta key** per post type for the optional meta column (sanitized key).

## Post ordering

**Manual ordering via the Order column**

- Adds an **Order** column and drag-style ordering UI on supported list tables.
- Default post types in settings: **post** and **page**.
- Adjusts main queries with `pre_get_posts` when enabled.

Use for ordered lists on the front end when the theme respects `menu_order`.

## Live previews

Productivity fieldsets pair with preview partials synced by admin JavaScript:

- Notices → `#edminboost-productivity-notices-preview`
- Screen tabs → `#edminboost-productivity-screen-preview`
- Dashboard widgets → `#edminboost-productivity-dashboard-preview`

Previews use theme preview colors where applicable.

## Tips

- Combine **hide notices** with Command Center **Theme → Declutter** for a calmer top bar — they address different UI layers.
- For sidebar clutter, use **Menu Studio** hide/reorder instead of feature toggles.
- Enable **duplicator** and **post order** only on sites where editors need those workflows — they add UI surface area.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| Notices still show | Only non-error/warning notices are hidden; some plugins use error styling for promos. |
| Dashboard widget still visible | Widget not in the six core options; may be a plugin widget. |
| Footer unchanged | Master toggle on **and** custom text filled; Save succeeded. |
| Duplicate action missing | Feature enabled; user can `edit_posts`; refresh list table. |
| Order column missing | Feature enabled; correct post type; theme/query may ignore `menu_order`. |

## See also

- [Features overview](overview.md)
- [Command Center → Menu Studio](../command-center/menu-studio.md)
- [Command Center → Theme](../command-center/theme-appearance.md)
