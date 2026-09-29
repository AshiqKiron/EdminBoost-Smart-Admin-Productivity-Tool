# WordPress.org release checklist

Use this when publishing **EdminBoost** to the [WordPress.org plugin directory](https://wordpress.org/plugins/). The directory must receive the **free build only** — no Freemius SDK, no `includes/pro/` bootstrap, and no premium licensing code.

## Build the free package

From the plugin root:

```bash
bash bin/build-wporg-zip.sh
```

This writes `dist/edminboost-admin-customization.zip` and runs [`bin/verify-wporg-zip.sh`](../bin/verify-wporg-zip.sh) automatically.

Manual verification (optional):

```bash
bash bin/verify-wporg-zip.sh dist/edminboost-admin-customization.zip
```

Expected result: `OK: no Freemius SDK, premium bootstrap, or wp_org_gatekeeper detected.`

## What must not ship to WordPress.org SVN

- `includes/pro/` (Freemius bootstrap and SDK)
- `vendor/freemius/`
- Dev-only paths already excluded by [`bin/build-zip-lib.sh`](../bin/build-zip-lib.sh) (tests, bin, node_modules, etc.)

The **git development repo** may contain `includes/pro/` for the direct-download premium zip; **do not** copy the full monorepo into SVN trunk.

## Deploy to SVN

1. Unzip `dist/edminboost-admin-customization.zip` locally.
2. Sync the extracted `edminboost-admin-customization/` folder to your plugin’s **trunk** on WordPress.org (replace trunk contents; do not add `includes/pro/`).
3. Tag the release from trunk per [WordPress.org plugin developer handbook](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/).

## Free-tier behavior (WordPress.org installs)

- No in-plugin license API; plan stays **Free** unless a custom filter is added.
- Pro-only admin screens and controls are **omitted** (not locked with upsell prompts).
- Plan limits are enforced on settings read/save via `EDMINBOOST_Pro::enforce_plan_limits()`.
- Upgrade links on the in-plugin **Billing** page open the external pricing site in a new tab only when clicked — no background checkout or telemetry.

See also: [plans-and-billing/free-vs-pro.md](plans-and-billing/free-vs-pro.md) and the FAQ in [`readme.txt`](../readme.txt).

## Premium (direct download) builds

For licensed builds that bundle Freemius, use:

```bash
bash bin/build-premium-zip.sh
bash bin/verify-premium-zip.sh
```

That artifact is **not** uploaded to WordPress.org.
