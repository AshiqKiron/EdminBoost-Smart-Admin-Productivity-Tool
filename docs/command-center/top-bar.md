# Top Bar

**EdminBoost → Top Bar** (Layout Studio / mapper) is a **visual builder** for shortcuts on the WordPress **admin bar** (`#wpadminbar`). Each item can open its target **in place** (redirect) or inside a **slide-out drawer** (iframe panel).

## Prerequisites

- EdminBoost **enabled** in settings.
- User logged in with **admin bar showing** (typically administrators in wp-admin).
- At least one saved top bar item for the bar to render (after setup, presets usually add items automatically).

Items respect **capabilities** and **role visibility** — users only see links they are allowed to access.

## Builder layout

The page has two main areas:

1. **Discovered links** — searchable list of admin menu pages and plugin entries detected from your site. Drag rows onto the canvas, or click a row to toggle it (except drag handles and control areas).
2. **Canvas** — ordered list of active top bar items. Drag to reorder; select an item to edit details in the side panel.

Hidden form inputs sync canvas state before **Save**.

### Plugin search

Use **Search plugins and menus** to filter the discovered list by label.

## Item settings

Each top bar item supports:

| Field | Description |
|-------|-------------|
| **Slug / path** | Admin path (e.g. `edit.php`, `woocommerce`) or full `http(s)` URL for external tools. |
| **Anchor** | Optional hash fragment **without** `#` (e.g. tab id on a settings page). Uniqueness is **slug + anchor**. |
| **Label** | Text shown on the admin bar (and drawer title). |
| **Icon** | Dashicon class (picker) or fallback when the menu uses an image icon. |
| **Interaction** | **Redirect** — normal navigation. **Drawer** — opens slide-out panel (Pro-gated on some premium Free builds; full on WordPress.org). |
| **Badge source** | Optional live counter on the bar item (see below). |

### Custom admin link

Add a path with optional **label** and **anchor**. Paths may include query strings and `#fragment`. Duplicates are blocked for the same slug+anchor pair.

## Slide-out drawer

Drawer items:

- Use `href="#"` on the admin bar; JavaScript opens `#edminboost-cc-drawer`.
- Load the target admin screen in an **iframe** with admin chrome stripped for a focused panel.
- Require a valid **nonce** on iframe loads; only whitelisted slug+anchor pairs are allowed.

**Front end:** When drawer items exist, the drawer shell and scripts may load on the public site if the **admin bar is visible** (e.g. logged-in administrator viewing the site).

### Panel & badges section

Below the canvas, **Panel & badges** configures drawer **behavior** (stored under `command_center.behavior`):

| Setting | Options / notes |
|---------|------------------|
| **Drawer width** | Compact, Standard, Fullscreen, Custom (400–800 px). Custom/fullscreen may be Pro-gated on premium Free. |
| **Animation speed** | Fast (~150 ms), Normal (~300 ms), Slow (~500 ms). |
| **Badge style** | Dot, Pill, Accent (Pro-gated on some premium Free builds). |
| **Glassmorphism** | Optional frosted drawer backdrop (Pro-gated on some premium Free builds). |
| **Badge refresh rate** | Seconds between local badge recounts (15–600). |
| **Autosave interval** | Related editor autosave hint interval (10–600). |

The **Panel & badges** block stays **disabled** until at least one canvas item uses **Drawer** interaction (UI and server markup).

### Drawer preview on this page

When a drawer item is selected, use **Preview drawer** to open the panel via AJAX-signed URLs (Layout Studio preview context). Requires administrator capability and nonce.

## Live badges

Optional **badge source** per item (local data only):

| Source | Counts |
|--------|--------|
| None | No badge |
| WooCommerce — unread orders | Local orders |
| WooCommerce — pending reviews | Local reviews |
| WordPress — pending comments | Moderation queue |
| WordPress — available updates | Core/plugin/theme updates |
| WPForms — unread entries | Local entries table when WPForms is active |

Badges refresh on the configured interval; no external HTTP calls.

## Saving

**Save** on Top Bar sets `_layout_studio_save` so the submitted `top_bar_items` array **always** replaces the stored layout — including an **empty canvas**, which clears the top bar intentionally.

Other Command Center pages **omit** this marker so they do not overwrite top bar items accidentally.

## Runtime behavior

- Nodes register on `admin_bar_menu` with ids derived from slug and anchor.
- **Declutter** toggles on [Theme (Appearance)](theme-appearance.md) remove core nodes separately.
- On premium Free, plan enforcement may downgrade drawer items to redirect and clear Pro badge options at runtime when not licensed.

## Tips

- Use **drawer** for frequent tools (orders, comments) to avoid losing your place in wp-admin.
- Pair WooCommerce **orders** badge with a drawer link to `edit.php?post_type=shop_order` for a mini operations desk.
- Keep the canvas focused — too many items wrap poorly on smaller screens.
- Anchors are useful for deep links into plugin settings tabs.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| No EdminBoost items on admin bar | Plugin enabled; items saved; user role/capability; items not hidden by role visibility. |
| Drawer blank or blocked | Nonce expiry — refresh and retry; slug must match whitelist; user must keep admin session in iframe. |
| Badge always zero | Source plugin inactive; no pending items; WPForms table missing. |
| Panel & badges greyed out | Set at least one item to **Drawer** interaction. |
| Item missing from discovered list | Some menus register late; reload Top Bar tab; check plugin active. |

## See also

- [Command Center overview](overview.md)
- [Layout Presets](layout-presets.md) — bundled top bar templates
- [Theme (Appearance)](theme-appearance.md) — declutter core admin bar
- [Dashboard](dashboard.md) — read-only link summary
