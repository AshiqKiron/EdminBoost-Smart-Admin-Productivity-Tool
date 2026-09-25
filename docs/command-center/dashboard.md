# Dashboard

The **Dashboard** is the main EdminBoost entry point in wp-admin (**EdminBoost → Dashboard**). It either guides you through **first-time setup** or, after setup, gives a **compact control panel** for layout and theme without opening every sub-page.

## Who can access it

Users with **Administrator** access (`manage_options`). Other roles do not see EdminBoost settings screens.

## Two modes

### 1. Setup wizard (first run)

Shown when setup is **not** complete: onboarding has not been finished **and** there are no saved top bar items.

The wizard is a **single form** with four steps (stepper at the top):

| Step | Name | What you do |
|------|------|-------------|
| 1 | Layout | Choose a **layout preset** (scenario or role-based template). This sets initial top bar links and sidebar layout. |
| 2 | Color theme | Choose a **visual theme preset**, **mode** (light/dark/auto), and **font** stack. |
| 3 | Top bar | Review the **read-only summary** of links the preset will add. Customize later on **Top Bar**. |
| 4 | Review | Summary of layout + theme + top bar before you finish. |

**Navigation:** Use **Back**, **Next**, and **Finish setup** (submit on step 4). Finishing saves settings and marks onboarding complete.

**What gets saved on finish:**

- Selected layout preset is **applied** (top bar + sidebar from preset definition).
- Theme choices from step 2.
- `onboarding_completed` is set.
- EdminBoost remains **enabled**.

You can change everything afterward on Layout Presets, Theme, Top Bar, and Menu Studio.

### 2. Overview dashboard (after setup)

Shown when onboarding is complete **or** you already have saved top bar items.

Overview cards typically include:

- **Layout preset** — compact picker with mini **sidebar** and **top bar** previews. Changing the preset here applies that layout and saves via AJAX (page may reload to refresh state).
- **Theme** — preset list with live **color swatch** preview; changes sync to the admin preview and save silently over AJAX.
- **Top bar summary** — read-only list of configured links (redirect vs drawer counts in the card description).
- Quick links to **Theme**, **Top Bar**, and **Layout Presets** for deeper editing.

The overview form includes hidden fields so partial theme saves stay consistent (mode, font, custom color tokens when using a custom theme).

**Note:** Setting help info icons are **hidden** on the Dashboard to keep the overview clean; full field help appears on dedicated pages.

## Command Center tabs on Dashboard

- **Before setup completes:** sub-nav tabs are **hidden**; only the wizard is shown.
- **After setup:** the full Command Center tab bar appears (Dashboard, Layouts, Theme, Top Bar, Menu Studio, Productivity, Security, Performance, White Label, Billing).

## Saving behavior

- **Wizard:** standard form submit to WordPress Settings API (also intercepted by AJAX save like other CC forms).
- **Overview:** layout preset clicks and theme preset clicks trigger **AJAX save**; wait for save to finish before switching tabs (the UI queues tab navigation when a dashboard save is pending).

## Legacy URL

Old bookmarks to **EdminBoost → Onboarding** redirect to the Dashboard.

## Common tasks

### Skip the wizard by configuring manually

If you already saved top bar items (for example via import or another admin), the overview appears instead of the wizard. Otherwise, finish the wizard once or add top bar items on **Top Bar** and save.

### Change layout quickly after setup

On the overview, open the **layout preset** list, select a preset, and let the auto-save apply it. For role-specific layouts or the visibility matrix, use [Layout Presets](layout-presets.md).

### Change colors quickly

Use the overview **theme** picker; open [Theme (Appearance)](theme-appearance.md) for declutter toggles, custom colors, favicon, post status colors, and scheduled dark mode.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| Stuck on wizard every visit | Finish step 4 or ensure at least one top bar item is saved and onboarding flag is set. |
| Top bar empty after setup | Open **Top Bar**, add links from the discovered plugins/menu list, Save. |
| Theme change does not stick | Confirm save succeeded (notice); hard refresh wp-admin. Another admin tab may have overwritten settings. |
| Tabs missing | Complete setup first; tabs appear only after setup is complete. |

## See also

- [Command Center overview](overview.md)
- [Layout Presets](layout-presets.md)
- [Theme (Appearance)](theme-appearance.md)
- [Top Bar](top-bar.md)
