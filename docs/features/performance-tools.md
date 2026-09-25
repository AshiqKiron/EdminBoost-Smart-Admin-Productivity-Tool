# Performance tools

**EdminBoost → Performance** (Command Center tab) reduces optional WordPress scripts and API traffic. Changes can affect **admin**, the **block/post editor**, or the **public site** depending on the toggle — read scope before saving.

## Emoji scripts

**Disable emoji detection scripts**

- Master toggle plus **Scope**:
  - **Admin only**
  - **Front end only**
  - **Admin and front end**

Removes WordPress emoji detection/enqueue hooks for the selected scope. Emoji characters in content still work; the extra JS/CSS pipeline is skipped.

Includes a **live preview** (`#edminboost-performance-emoji-preview`) showing admin vs front-end panels when scope changes.

## Assets

Three independent checkboxes with a shared **assets preview**:

### Remove version query strings

Strips `?ver=` from enqueued **script and style** URLs (`style_loader_src`, `script_loader_src`).

**Trade-off:** Browsers may cache assets longer after updates until a hard refresh or cache bust from another plugin. Sometimes used with external CDNs or aggressive caching strategies.

### Remove Dashicons on the front end

Dequeues Dashicons for **visitors** on the public site when they are not needed.

**Note:** Logged-in users with the admin bar may still load icons. Test icon-heavy themes or front-end blocks that rely on Dashicons.

### Disable embeds and oEmbed discovery

Disables WordPress embed script, oEmbed discovery, and related TinyMCE plugin hooks.

**Affects:** Auto-embed of YouTube/Twitter URLs, oEmbed consumer features. Content still stores URLs; automatic embed iframes may not render without another solution.

Preview items in `#edminboost-performance-assets-preview` mark removed pieces when toggles are on.

## Heartbeat API

WordPress **Heartbeat** powers autosave, post lock, and some dashboard updates. Configure **three contexts** independently:

| Context | Typical location |
|---------|------------------|
| **Admin screens** | General wp-admin |
| **Post editor** | Block/classic editor sessions |
| **Front end** | Logged-in or front-end Heartbeat (default setting in factory defaults: **Disable** on front end) |

Each context offers:

| Mode | Behavior |
|------|----------|
| **Default** | WordPress core interval |
| **Slow (60s)** | Longer interval via `heartbeat_settings` |
| **Disable** | Deregisters Heartbeat script in that context |

**Trade-offs:**

- **Disable in editor** — reduces server load but can affect **post lock** and **autosave** reliability; use with caution on multi-author sites.
- **Disable on front end** — often desirable for performance when nothing needs live admin polling on public pages.

Setting help icons explain each context on the Performance page.

## Interaction with Command Center

Performance features do not change Command Center bar badges or drawer refresh — badge refresh for top bar items is configured under **Top Bar → Panel & badges** (`badge_refresh_rate` in `command_center.behavior`).

## Saving

Settings under `edminboost_settings['features']`. Use **Save** after changes; **Reset to defaults** reloads factory values (Heartbeat front end defaults to **Disable** in plugin defaults).

## Tips

- Start with **emoji disable (admin only)** and **front-end Heartbeat disable** — low risk for many sites.
- Test **remove asset versions** after deployments; purge CDN/cache if styles look stale.
- Before **disable embeds**, confirm editors do not rely on paste-to-embed in posts.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| Editor autosave flaky | Heartbeat **Post editor** set to Disable or Slow; set to Default. |
| Another user not locked out of post | Heartbeat disabled in editor. |
| Icons missing on front end | **Remove Dashicons** enabled; theme needed Dashicons for visitors. |
| Embeds show as plain URLs | **Disable embeds** on; re-enable or use block embeds manually. |
| Caching stale CSS | **Remove asset versions** + strong browser/CDN cache. |

## See also

- [Features overview](overview.md)
- [Command Center → Top Bar](../command-center/top-bar.md) — badge refresh interval
