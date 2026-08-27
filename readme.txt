=== EdminBoost - Smart Admin Productivity Tool ===
Contributors: ashiquzzaman
Tags: admin, dashboard, productivity, admin-tools, admin-menu
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.4.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Boost WordPress admin productivity with smart tools to simplify workflows, customize the dashboard, and streamline daily admin tasks.

== Description ==

EdminBoost is a smart admin productivity plugin for WordPress. It helps site administrators customize and optimize the WordPress admin experience with modular, opt-in tools.

= Features =

* **Hide Admin Notices** — Reduce clutter by hiding notices outside EdminBoost screens
* **Dashboard Widget Control** — Remove selected default dashboard widgets
* **Custom Admin Footer** — Replace the default WordPress footer text
* **Disable Emojis** — Remove emoji detection scripts from the admin area
* **Admin Bar Tweaks** — Hide selected admin bar items
* Modular, extensible architecture with developer filters
* Translation-ready with a dedicated text domain
* Uninstall-safe — removes all plugin options on uninstall

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **EdminBoost** in the admin sidebar to get started.
4. Open **Settings** to enable individual productivity features.

== Frequently Asked Questions ==

= What does this plugin do? =

EdminBoost provides modular admin productivity tools. Each feature can be enabled or disabled independently from the Settings page.

= Who can access the plugin settings? =

Only users with the `manage_options` capability (typically Administrators) can access EdminBoost settings.

= Does this plugin send data to external servers? =

No. EdminBoost does not track users or send site data to external servers.

= Is the plugin uninstall-safe? =

Yes. When uninstalled, all plugin options are removed from the database.

== Changelog ==

= 1.4.0 =
* Add a Billing page with Free ($0), Pro ($49 / 1 site), and Agency ($99 / 10 sites) plans in the Command Center and EdminBoost sidebar menu.
* Fix top bar icons not rendering for plugin menus that use SVG or image sidebar icons.
* Theme the WordPress admin footer when a visual theme is active so it matches the content background.
* Dashboard color theme previews now respect the saved light/dark mode instead of always showing the dark palette.
* Add layout presets for every WordPress role shown on Users → Add New (Administrator, Editor, Author, etc.).
* Remove the Custom option from the layout preset dropdown; use Top Bar to edit layouts that do not match a preset.
* Fix Dashboard color theme picker not applying the live admin theme preview when a new preset is selected.
* Remove Productivity, Security, Performance, and White Label from the EdminBoost admin sidebar menu.
* Add Productivity, Security, Performance, and White Label Command Center pages.
* Add post duplicator, hide screen options/help, classic widgets, custom admin columns, and menu duplicator.
* Add security tools: disable XML-RPC, disable feeds, REST API hardening, login/logout redirects, and disable comments.
* Add performance tools: emoji scope toggle, remove asset versions, front-end Dashicons removal, Heartbeat control, and disable embeds.
* Add manual post ordering via list table Order column.
* Apply layout preset role assignments at runtime for the top bar.
* Consolidate admin bar cleanup toggles into Appearance (including New content and Customize).
* Extend visual theme with admin favicon, background, font size, post status colors, and schedule tokens.
* Extend Menu Studio with width, typography, padding tokens, and icon/text display modes.
* Add white-label branding, login customization, system status footer, and settings JSON export/import.

= 1.3.0 =
* Load Command Center tabs (Dashboard, Appearance, Top Bar, Layout Presets, Menu Studio, Settings) without a full page reload.
* Fix Top Bar and Menu Studio discovered admin page lists staying empty when a tab is loaded via AJAX navigation.
* Show Settings inside the Command Center tab navigation like other EdminBoost pages.
* Remove the duplicate Admin menu feature from Settings; use Menu Studio to hide, reorder, and style sidebar items instead.
* Add a four-step Dashboard setup wizard: layout preset, color theme, top bar review, and save.
* Replace layout preset card grids with a compact dropdown picker (description + top bar preview) on the setup wizard and Layout Presets page.
* Show layout preset, color theme, and top bar descriptions on Dashboard overview cards after setup.
* Add Default and Custom options at the top of the layout preset dropdown on Dashboard overview and Layout Presets pages.
* Add compact visual previews under each Dashboard overview card (layout top bar, theme colors, live top bar links).
* Redirect to the Dashboard wizard after plugin activation when setup is incomplete.
* Add a dedicated Appearance page for visual theme, panel behavior, badges, and admin bar cleanup.
* Rename Presets menu item to Layout Presets and add consistent Command Center navigation across pages.
* Fix setup completion so onboarding is only marked done when a top bar layout exists.
* Redirect legacy Behavior & Style URLs to Appearance instead of Dashboard.
* Add seven real-life scenario presets: Friend's Website, Family Member's Site, Client's Website, Your Own Website, Small Business Site, Nonprofit / Community, and Freelancer / Agency.
* Group the preset library by scenario, role, and saved layouts on the Presets page.
* Remove the Dashboard quick look skin cards (Clean, Focused, Full); use advanced look settings directly instead.
* Rename the Home admin page to Dashboard.
* Remove the Current setup status block from the Dashboard page.
* Add six inspired visual theme presets: Dracula, Nord, Solarized, Sakura, Ocean, and Forest.
* Apply the visual theme preset to the wp-admin top bar, sidebar menu, and main content background (Menu Studio custom colors still override sidebar when enabled).
* Add custom top bar, sidebar, and content area color overrides to the visual theme settings.
* Move visual theme preset selection to a color-swatch dropdown with short theme descriptions.
* Add a Custom visual theme preset that reveals Accent, Surface, Text, Top bar, Sidebar, and Content area color pickers.
* Expand visual theme font options with additional system font stacks.
* Fix unreadable sidebar submenu text on light visual theme presets (Neon Outrun, Vapor, and similar).
* Add Menu Studio page to reorder the wp-admin sidebar, add custom sidebar links, and style parent menu, submenu, and notification badge colors.
* Simplify admin navigation to Dashboard, Top Bar, Presets, and Settings.
* Replace the dashboard and onboarding flow with a Dashboard setup hub and quick look skins.
* Make built-in presets apply real top bar layouts in one click.
* Move advanced look settings (slide-out panel, badges, admin bar cleanup) to Dashboard.
* Redirect legacy Onboarding and Behavior & Style URLs to Dashboard.

= 1.2.0 =
* Fix Command Center drawer pages stuck on "Loading…" for async admin content (WooCommerce dashboard widgets, Site Health, Appearance, and similar screens).
* Fix plugin slug typo: admin page URLs now use edminboost-smart-admin-productivity-tool (was roductivity).
* Fix WordPress 6.9.1 script notice when loading admin pages inside the Command Center slide-out drawer iframe.
* Add custom AJAX slide-out drawer width option with a 400–800px slider on Behavior & Style.
* Save Command Center and Settings page changes without a full page reload.
* Setup wizard launch, preset apply/duplicate/save, and all other save buttons now stay on the page after saving.
* Fix Layout Studio drawer preview AJAX so preview works from admin-ajax requests.
* Fix Layout Studio discovered-item active state when canvas items use slug plus anchor pairs.
* Fix AJAX slide-out drawer for top bar items configured with drawer interaction in Layout Studio.
* Fix drawer iframe 404s by normalizing menu slugs and same-site admin URLs before building iframe targets.
* Restore horizontal padding for admin pages loaded inside the slide-out drawer.
* Fix admin bar drawer clicks by matching WordPress `wp-admin-bar-` node IDs to drawer item config.
* Add a Layout Studio "Preview AJAX drawer" button to test drawer interaction before saving.
* Improve Layout Studio layout: sticky scrollable sidebar, side-by-side item configuration panel, and collapsible custom link form.
* Add EdminBoost Command Center with four admin pages: Onboarding wizard, Layout Studio, Presets & Roles, and Behavior & Style.
* Add persona-based preset selector, top-bar mapper UI, role visibility matrix, and behavior styling controls.
* Render saved Layout Studio items on the live WordPress admin bar.
* Extend Layout Studio discovery to include submenu admin pages (e.g. taxonomy screens).
* Add a custom admin link control in Layout Studio for pages not auto-discovered.
* Add optional anchor support for top bar links to scroll to in-page sections.
* Fix Layout Studio save so removing all items clears the stored top bar layout.
* Extend settings with command_center option group and interactive admin assets.
* Correct plugin display name to EdminBoost - Smart Admin Productivity Tool throughout the admin UI.

= 1.1.1 =
* Hide admin notices feature now preserves error and warning notices.
* Disable emojis feature is scoped to admin hooks only.
* Harden settings sanitization and menu protection for EdminBoost screens.

= 1.1.0 =
* Add modular productivity features: hide notices, dashboard widgets, admin menu, footer, emojis, and admin bar controls.
* Add centralized settings class and feature registry.
* Expand settings page and dashboard with feature status overview.

= 1.0.0 =
* Initial release with plugin scaffold, dashboard, and settings page.
