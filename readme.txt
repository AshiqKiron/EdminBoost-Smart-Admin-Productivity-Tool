=== EdminBoost - Smart Admin Productivity Tool ===
Contributors: ashiquzzaman
Tags: admin, dashboard, productivity, admin-tools, admin-menu
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Boost WordPress admin productivity with smart tools to simplify workflows, customize the dashboard, and streamline daily admin tasks.

== Description ==

EdminBoost is a smart admin productivity plugin for WordPress. It helps site administrators customize and optimize the WordPress admin experience with modular, opt-in tools.

= Features =

* **Hide Admin Notices** — Reduce clutter by hiding notices outside EdminBoost screens
* **Dashboard Widget Control** — Remove selected default dashboard widgets
* **Admin Menu Customizer** — Hide menu items you do not use
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

= 1.3.0 =
* Add Menu Studio page to reorder the wp-admin sidebar, add custom sidebar links, and style parent menu, submenu, and notification badge colors.
* Simplify admin navigation to Home, Top Bar, Presets, and Settings.
* Replace the dashboard and onboarding flow with a Home setup hub and quick look skins.
* Make built-in presets apply real top bar layouts in one click.
* Move advanced look settings (slide-out panel, badges, admin bar cleanup) to Home.
* Redirect legacy Onboarding and Behavior & Style URLs to Home.

= 1.2.0 =
* Fix Command Center drawer pages stuck on "Loading…" for async admin content (WooCommerce dashboard widgets, Site Health, Appearance, and similar screens).
* Fix plugin slug typo: admin page URLs now use edminboost-smart-admin-productivity-tool (was roductivity).
* Fix WordPress 6.9.1 script notice when loading admin pages inside the Command Center slide-out drawer iframe.
* Add custom AJAX slide-out drawer width option with a 400–800px slider on Behavior & Style.
* Save Command Center and Settings page changes without a full page reload.
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
