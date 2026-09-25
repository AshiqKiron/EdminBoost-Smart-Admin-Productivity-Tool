# System status footer

When **White Label** is active, you can replace the **right-hand** admin footer line (where WordPress normally shows version text) with a compact **system status** summary built from toggles on **EdminBoost → White Label**.

## Prerequisites

- EdminBoost **enabled**
- **Pro or Agency** active (or `edminboost_is_pro_active` true)
- **Enable white-label branding** checked and saved

See [White Label overview](overview.md).

## Where it appears

WordPress admin footer has two areas:

| Area | White Label behavior |
|------|----------------------|
| **Left** (`admin_footer_text`) | Optional [Hide default WordPress footer credit](enable-branding-and-plugin-rebrand.md) |
| **Right** (`update_footer`) | **System status** segments when any status toggle is on |

Status segments are joined with ` | ` (space-pipe-space). Output is **escaped for HTML display** in the admin footer.

## Available status toggles

Each checkbox maps to a setting key under `white_label`:

| Toggle | Setting key | Example output |
|--------|-------------|----------------|
| Show IP address | `show_ip` | `IP: 203.0.113.1` (from `SERVER_ADDR` on the server) |
| Show PHP version | `show_php_version` | `PHP 8.3.x` |
| Show WordPress version | `show_wp_version` | `WP 6.x` |
| Show memory usage | `show_memory_usage` | `Memory: 45 MB of 256 MB (18%)` |
| Show memory limit | `show_memory_limit` | `Limit: 256 MB` |
| Show memory available | `show_memory_available` | `Available: 211 MB` |

Memory values come from PHP `memory_get_usage()` and `memory_limit` ini setting, formatted with WordPress `size_format()`.

If **no** status toggles are enabled, the right footer falls back to WordPress default version text (white label active but no status parts).

## Live preview

The **System status footer** section includes `#edminboost-wl-status-preview`:

- Mirrors enabled segments as you toggle checkboxes.
- Shows placeholder left credit text (“Thank you for creating with WordPress”) for context.
- Displays “No status details selected” when all status toggles are off.

JavaScript: `initWhiteLabelStatusPreview()` syncs preview segments from checkbox state.

## Configuration steps

1. Open **White Label**.
2. Enable **Enable white-label branding**.
3. In **System status footer**, check the metrics you want visible to admins.
4. Watch the **live preview** on the right.
5. Click **Save white label settings**.

Dependent section `#edminboost-wl-status-section` is disabled in the UI until the master white-label toggle is on.

## Use cases

- **Agency support:** quick PHP/WP versions on client sites without opening Site Health.
- **Hosting diagnostics:** memory limit vs usage on heavy admin pages.
- **Server context:** datacenter/server IP (`SERVER_ADDR`) — useful on some VPS setups; not the visitor IP.

## Privacy and security

- Status data is **generated on each admin page load** from the server environment; nothing is transmitted to EdminBoost or third parties.
- Any administrator who can see wp-admin footers can see enabled stats—do not enable sensitive details on shared admin accounts if that is a concern.
- **IP shown is server address**, not the logged-in user’s public IP.

## Troubleshooting

| Issue | What to check |
|-------|----------------|
| No status line | At least one status toggle on; white label master on; Pro active; save succeeded. |
| Still see WP version only | No toggles enabled, or white label not active. |
| Memory shows 0% | `memory_limit` misconfigured in php.ini. |
| IP shows em dash | `SERVER_ADDR` unavailable in CLI or local dev. |
| Preview works, live does not | Save form; confirm `is_active()` (Pro + enabled). |

## See also

- [White Label overview](overview.md)
- [Enable branding and plugin rebrand](enable-branding-and-plugin-rebrand.md)
