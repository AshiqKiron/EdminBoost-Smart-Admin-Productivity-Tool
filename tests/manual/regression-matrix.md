# EdminBoost Manual Regression Matrix

Run after PHPUnit and Playwright. Mark pass/fail in the Date column.

## Global

| ID | Check | Steps | Expected | Date |
|----|-------|-------|----------|------|
| G-01 | Plugin activates | Activate from Plugins screen | No PHP errors | |
| G-02 | Global kill switch | Disable in Settings, save | CC bar hidden; features off | |
| G-03 | Capability gate | Log in as Editor, open Settings URL | Access denied / redirect | |
| G-04 | Uninstall | Delete plugin | `edminboost_settings` and `edminboost_version` removed | |

## Productivity features

| ID | Feature | Steps | Expected | Date |
|----|---------|-------|----------|------|
| F-01 | Hide notices | Enable; visit Dashboard | Success notices hidden; errors/warnings visible | |
| F-02 | Hide notices exempt | Enable; open EdminBoost pages | Notices visible on plugin screens | |
| F-03 | Dashboard widgets | Remove Welcome panel; save | Welcome panel absent on Dashboard | |
| F-04 | Admin menu | Hide Posts; save | Posts missing from sidebar | |
| F-05 | Admin menu protected | Try hide Dashboard / Plugins / EdminBoost | Protected menus remain | |
| F-06 | Admin footer | Enable + custom text | Footer text replaced | |
| F-07 | Disable emojis | Enable | No emoji script in admin `<head>` | |
| F-08 | Admin bar tweaks | Hide WP logo | `wp-logo` node removed | |

## Home setup

| ID | Check | Expected | Date |
|----|-------|----------|------|
| CC-H-01 | First-run cards | Preset and manual setup links visible | |
| CC-H-02 | Look settings | Advanced drawer/badge/declutter settings save from Appearance page | |
| CC-H-03 | Setup wizard | Fresh install: activation redirects to Dashboard; complete 4-step wizard; overview shows after save | |
| CC-H-04 | Legacy URLs | `-onboarding` and `-behavior` redirect to Home | |

## Top Bar editor

| ID | Check | Expected | Date |
|----|-------|----------|------|
| CC-M-01 | Discovery list | Submenu pages appear | |
| CC-M-02 | Search filter | List filters by label | |
| CC-M-03 | Drag to canvas | Item added with correct slug | |
| CC-M-04 | Reorder canvas | Order persists on save | |
| CC-M-05 | Custom link valid | Item added from custom form | |
| CC-M-06 | Custom link invalid | Alert for bad path/anchor | |
| CC-M-07 | Duplicate link | Alert for duplicate slug+anchor | |
| CC-M-08 | Anchor field | Anchor saved and used in URL | |
| CC-M-09 | Drawer interaction | Preview opens drawer with iframe | |
| CC-M-10 | Clear all items | Save empty canvas clears stored layout | |
| CC-M-11 | Same slug, two anchors | Both items on canvas; discovered state correct | |

## Presets

| ID | Check | Expected | Date |
|----|-------|----------|------|
| CC-P-01 | Built-in presets | Apply preset populates top bar | |
| CC-P-02 | Save custom preset | Current layout saved as custom preset | |
| CC-P-03 | Role matrix | Hidden items per role apply on live bar | |

## Look settings (Home)

| ID | Check | Expected | Date |
|----|-------|----------|------|
| CC-B-01 | Drawer width | Panel class matches selection | |
| CC-B-02 | Animation speed | Drawer transition duration changes | |
| CC-B-03 | Glassmorphism | `is-glass` class on drawer | |
| CC-B-04 | Badge style | Live bar badge matches style | |
| CC-B-05 | Declutter toggles | WP logo / comments / howdy / updates hidden | |

## Live admin bar & drawer

| ID | Check | Expected | Date |
|----|-------|----------|------|
| CC-L-01 | Bar nodes | Saved items appear in `#wpadminbar` | |
| CC-L-02 | Redirect item | Navigates to target admin page | |
| CC-L-03 | Drawer open | Drawer opens; iframe loads admin page | |
| CC-L-04 | Drawer close | Backdrop, close button, Escape close drawer | |
| CC-L-05 | Open full page | Link opens correct URL in new tab | |
| CC-L-06 | Front-end bar | Drawer works on front-end with admin bar | |
| CC-L-07 | Badge sources | WC / comments / updates counts when plugins present | |

## Responsive & accessibility

| ID | Check | Breakpoint | Expected | Date |
|----|-------|------------|----------|------|
| UI-01 | Feature list stacks | 782px | Single column layout | |
| UI-02 | Mapper stacks | 960px | Sidebar above canvas | |
| UI-03 | Keyboard mapper | Tab / Enter | Focusable controls | |
| UI-04 | Drawer a11y | Open drawer | `role="dialog"`, close on Escape | |
| UI-05 | Screen reader | Icon-only buttons | `screen-reader-text` labels | |

## Cross-plugin

| ID | Check | Expected | Date |
|----|-------|----------|------|
| X-01 | WooCommerce menus | WC screens in discovery list | |
| X-02 | Non-standard notices | Third-party notice markup | Errors still visible when hide notices on | |
