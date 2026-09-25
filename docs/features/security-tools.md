# Security tools

**EdminBoost → Security** (Command Center tab) offers **hardening toggles**, **comment controls**, and optional **login/logout redirects**. Review impact on integrations (mobile apps, REST clients, feed readers) before enabling on production.

## Hardening

### Disable XML-RPC

Turns off XML-RPC via WordPress filters (`xmlrpc_enabled`, related headers).

**Affects:** Remote publishing clients, some Jetpack or legacy integrations, pingbacks.

**Leave off** if you rely on XML-RPC; enable for a typical brochure site with no remote posting.

### Disable RSS/Atom feeds

Disables feed endpoints and redirects feed URLs.

**Affects:** Feed readers, podcast clients, some SEO/social tools that consume feeds.

### REST API hardening

Two independent checkboxes:

| Option | Effect |
|--------|--------|
| **Remove REST API link from HTML head** | Stops emitting the REST discovery link in `wp_head` output. |
| **Disable REST API for guests** | Blocks unauthenticated REST access (logged-in users and authenticated requests follow WordPress rules). |

**Affects:** Headless front ends, public REST consumers, some blocks/plugins that expect guest REST. Test thoroughly.

Either option can enable the `rest_api_hardening` feature module.

## Comments

**Disable comments for selected post types**

- Master toggle plus checkboxes for each **public post type** on your site.
- Removes comment support, admin UI, and admin bar comment nodes for selected types when active.

Use to turn comments off on marketing sites while leaving them on blog posts only, for example.

## Login redirects

**Role-based login and logout redirects**

- Master toggle **Enable role-based login and logout redirects**.
- **Default login redirect URL** and **Default logout redirect URL** (fallbacks when no role-specific URL is set).
- Per **role** fields: **Login URL** and **Logout URL** for each assignable WordPress role.

Hooks: `login_redirect`, `wp_logout`.

**Premium Free builds:** this fieldset may show a Pro badge and be UI-locked; sanitizer may reset redirects when not licensed. **WordPress.org** build includes full login redirects with no lock.

Dependent URL fields stay disabled until the master toggle is on.

### Redirect tips

- Use full `https://` URLs to admin or front-end destinations the role can access.
- Test each role in a private browser session after saving.
- Avoid redirect loops (login URL pointing back to `wp-login.php` without completing auth).

## What Security does not cover

- **Command Center** top bar, drawer, and role menu visibility — see [Layout Presets](../command-center/layout-presets.md) and [Menu Studio](../command-center/menu-studio.md).
- **File permissions, firewall, 2FA** — use server security and dedicated security plugins.
- **Capability editing** — WordPress roles and capabilities are unchanged; redirects only run after successful login/logout.

## Saving

Same AJAX **Save** / **Reset to defaults** flow as other Command Center tabs. Settings live under `edminboost_settings['features']`.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| Mobile app cannot connect | XML-RPC or REST guest disable may block it; disable those toggles or allow auth. |
| Feed URLs 404 or redirect | Expected with **Disable feeds**; disable toggle if feeds required. |
| REST block breaks editor | Guest REST disable should not block logged-in editor; check plugins using anonymous REST. |
| Redirect not applied | Master toggle on; URL saved; test correct role; premium Free Pro lock on login redirects. |
| Comments still on | Post type not selected; cache; another plugin re-enabling comments. |

## See also

- [Features overview](overview.md)
- [Free vs Pro](../plans-and-billing/free-vs-pro.md) — login redirects on premium builds
