# Layout Presets

**EdminBoost → Layouts** (sidebar label **Layouts**, page title **Layout Presets**) is where you choose **templates** that configure both the **WordPress admin top bar** and the **sidebar menu baseline**, assign layouts **per user role**, and control **which menu items each role may see**.

## What a layout preset contains

Each preset defines:

- **Top bar items** — admin links (slug, label, icon, optional URL anchor, redirect or drawer interaction, optional badge source).
- **Menu Studio baseline** — sidebar order, hidden items, and related defaults bundled with that template.

Applying a preset **replaces** the saved top bar and sidebar preset portions on save. **Custom sidebar links** you saved separately are **re-merged at runtime** when a role-specific preset is active, so custom links are not permanently lost when switching role assignments.

## Preset picker groups

The layout dropdown is grouped into:

| Group | Meaning |
|-------|---------|
| **Current layout** | **Default** — falls back to your stored default preset id. **Custom** — your hand-tuned top bar + Menu Studio (edit on Top Bar / Menu Studio). |
| **By use case** | Scenario templates (client site, ecommerce, developer, agency, etc.). |
| **By role** | Workflow presets aligned with WordPress roles (one preset per editable role). |
| **Your saved layouts** | Custom presets you saved from the current layout. |

System presets are **read-only**; you duplicate or save-as to create editable copies under **Your saved layouts**.

## Apply a preset

1. Open **Layouts**.
2. Select a preset in the picker (previews update for top bar and sidebar).
3. Click **Save**.

On save, the hidden `_apply_preset` field tells EdminBoost to load that preset’s top bar and sidebar configuration. The active picker selection should match what you intend before saving.

You can also apply presets from:

- **Dashboard** overview (layout card).
- **Setup wizard** step 1.

## Save your own preset

Use **Save current layout as preset** (header action) when you have a top bar configuration worth reusing.

1. Configure links on **Top Bar** and sidebar on **Menu Studio** first.
2. On **Layouts**, click **Save current layout as preset**.
3. Enter a **name** and confirm.

Saved presets appear under **Your saved layouts** with ids like `custom_*`. You can **rename** saved custom presets from the picker UI.

**Free tier note (premium builds):** Free may allow **one** saved custom layout; Pro allows unlimited saves. The WordPress.org build has no save limit. See [Free vs Pro](../plans-and-billing/free-vs-pro.md).

## Default preset

The **default preset** id (stored in settings) is used when the picker shows **Default**. It is typically a scenario preset such as **Client site** unless you change it during setup.

## Role assignments

Below the preset picker, **role assignment** rows map each WordPress role to a layout preset.

- When a user logs in, EdminBoost resolves their **first matching role** with an assignment and applies that preset’s **top bar** and **Menu Studio baseline** for that user.
- Roles without an assignment use the globally saved top bar and menu settings.

Role assignments are editable on all builds. They do not require the visibility matrix.

## Role visibility matrix

The scrollable **role visibility** table lists **every discovered admin menu item** (top-level and submenu). For each role column:

- **Checked** = item is **visible** to that role.
- **Unchecked** = item is **hidden** (stored as hidden slugs per role).

Behavior details:

- **Protected top-level items** — Dashboard (`index.php`), Plugins, and EdminBoost stay visible and cannot be unchecked.
- **Submenu rows** show the parent menu name; hiding a submenu slug removes that submenu entry for the role.
- Items **not in the assigned preset layout** may start unchecked but stay enabled so you can opt in.
- Items the role **cannot access by capability** appear restricted (typically unchecked, marked in the UI).

In **premium Free** builds, matrix checkboxes may be locked with a Pro upgrade prompt; assignments still work. **WordPress.org** builds include full matrix editing.

When a role preset is active, visibility merges with Menu Studio hide rules.

## Duplicate preset

Where available in the UI, duplicating creates a new saved preset from an existing system or custom preset so you can edit without changing the original system template.

## How EdminBoost detects “active” layout

EdminBoost compares your saved top bar and Menu Studio settings to known presets to highlight **Custom** vs a matching system preset in pickers. Minor manual edits after applying a preset usually switch the active selection to **Custom**.

## Interactions with other screens

| Screen | Relationship |
|--------|----------------|
| [Top Bar](top-bar.md) | Defines or overrides top bar items; saving from Top Bar does not apply a full preset unless you choose one on Layouts. |
| [Menu Studio](menu-studio.md) | Sidebar order, hide, colors; preset apply replaces sidebar baseline on Layouts save. |
| [Dashboard](dashboard.md) | Quick preset apply from overview card. |

## Tips

- Assign **Subscriber** or **Customer**-style roles a minimal preset, then use the matrix to hide advanced menus.
- After applying a heavy preset, open **Menu Studio** to enable sidebar customization and tweak order.
- Use **Custom** in the picker when you only changed a few links — you do not need to save a new named preset for every tweak.

## Troubleshooting

| Issue | What to try |
|-------|-------------|
| Preset apply wiped sidebar tweaks | Expected for preset apply on save; reconfigure Menu Studio or save a custom preset first. |
| Role sees wrong top bar | Check **role assignments** order (first matching role wins) and that the user’s role is assignable. |
| Menu item still visible | Matrix hides by slug; another plugin may register the same page under a different slug. Check capability and Menu Studio **hidden items**. |
| Cannot check matrix cells | Pro lock on premium Free — upgrade or use WordPress.org build; confirm you are not editing a protected row. |

## See also

- [Command Center overview](overview.md)
- [Top Bar](top-bar.md)
- [Menu Studio](menu-studio.md)
