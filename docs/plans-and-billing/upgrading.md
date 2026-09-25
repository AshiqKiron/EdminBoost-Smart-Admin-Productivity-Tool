# How to purchase Pro or Agency

Purchase a commercial license when you want **direct support from Asphalt Themes** or a **1-site / 10-site license pack** — not because features are locked in the WordPress.org build.

The WordPress.org free plugin already includes every Command Center and feature tool with no in-plugin license checks.

For plan details, see [Free vs Pro](free-vs-pro.md).

---

## Before you purchase

1. **Confirm you want a commercial license.** Review the [Free vs Pro comparison](free-vs-pro.md). Common reasons to purchase:
   - Priority email support from the plugin author
   - A licensed Pro or Agency package from Asphalt Themes
   - A **1-site** (Pro) or **10-site** (Agency) license pack for client work

2. **Check your site count.**
   - **Pro** covers **1 production site** ($49 / year).
   - **Agency** covers **10 sites** ($99 / year). See [Agency plan](agency-plan.md) if you manage a client portfolio.

3. **Note:** Checkout and license activation happen on the **EdminBoost pricing page**, not inside WordPress. The plugin does not process payments or validate licenses in wp-admin.

---

## Open pricing from inside WordPress

### Option A — Billing page (recommended)

1. Log in as an **Administrator** (`manage_options` capability).
2. Open **EdminBoost → Billing** in the wp-admin sidebar.
3. Review the **Current plan** section (Free on a new install).
4. Scroll to the plan cards or the **Compare plans** table at the bottom.
5. Click **View Pro pricing** or **View Agency pricing**.
6. The pricing page opens in a **new browser tab** at [https://asphaltthemes.com/edminboost](https://asphaltthemes.com/edminboost).
7. Complete checkout on the pricing page.
8. Follow the post-purchase instructions (license key or download link, when provided) for your licensed build.

### Option B — Command Center tab bar

1. Open any EdminBoost Command Center page (Dashboard, Theme, Top Bar, etc.).
2. Click **Billing** in the Command Center tab navigation.
3. Follow steps 3–8 from Option A above.

### Option C — Plugins screen Docs link

1. Go to **Plugins** in wp-admin.
2. Find **Edminboost** in the plugin list.
3. Click **Docs** in the row meta links (opens documentation in a new tab).
4. Navigate to the pricing section, or go directly to [https://asphaltthemes.com/edminboost](https://asphaltthemes.com/edminboost).

---

## After you purchase

Follow the instructions provided after checkout for your licensed package. The WordPress.org free build does not require license activation to use any feature.

When license activation is available for direct-download customers, **EdminBoost → Billing** may show **Pro** or **Agency** as the current plan via the `edminboost_active_billing_plan` filter — display only; it does not gate tools in the WordPress.org build.

---

## Purchase Agency instead of Pro

The steps are the same as Pro, but click **View Agency pricing** on the Billing page. Agency includes everything in Pro plus a **10-site license pack** for $99 / year.

See [Agency plan](agency-plan.md) for details.

---

## Frequently asked questions

### Is checkout handled inside WordPress?

No. Pricing buttons on the Billing page open the external pricing page in a new tab. There is no in-plugin checkout or payment form.

### Does purchasing send data to external servers?

Only when **you** click a pricing link. EdminBoost does not track usage or send site data in the background.

### Can I downgrade later?

Your settings remain in the database. The WordPress.org build does not disable tools when you remain on Free. Export your settings from **EdminBoost → Settings** if you need a backup before changing installs.

### I installed from WordPress.org — do I need Pro?

No. The WordPress.org build includes all features without license checks. Purchase Pro or Agency when you want a **licensed package**, multi-site licensing, or **priority support** from Asphalt Themes.

### Where is the pricing URL defined?

Developers can filter the link with the `edminboost_upgrade_url` WordPress filter. The default is `https://asphaltthemes.com/edminboost`.

---

## Related pages

- [Free vs Pro — full comparison](free-vs-pro.md)
- [Plans overview](plans-overview.md)
- [Agency plan](agency-plan.md)
