#!/usr/bin/env bash
#
# Verify a distributable plugin zip is safe for the WordPress.org free build.
# Fails when premium/licensing SDK code or Freemius symbols are present.
#
# Usage:
#   bash bin/verify-wporg-zip.sh [path/to/edminboost-admin-customization.zip]
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
PLUGIN_SLUG="edminboost-admin-customization"
ZIP_PATH="${1:-$PLUGIN_DIR/dist/$PLUGIN_SLUG.zip}"

if [[ ! -f "$ZIP_PATH" ]]; then
	echo "ZIP not found: $ZIP_PATH" >&2
	echo "Build one first: bash bin/build-wporg-zip.sh" >&2
	exit 1
fi

if ! command -v unzip >/dev/null 2>&1; then
	echo "unzip is required but was not found in PATH." >&2
	exit 1
fi

TMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TMP_DIR"' EXIT

unzip -q "$ZIP_PATH" -d "$TMP_DIR"

STAGE_DIR="$TMP_DIR/$PLUGIN_SLUG"
if [[ ! -d "$STAGE_DIR" ]]; then
	echo "Expected top-level folder $PLUGIN_SLUG inside the zip." >&2
	exit 1
fi

BOOTSTRAP="$STAGE_DIR/${PLUGIN_SLUG}.php"
if [[ ! -f "$BOOTSTRAP" ]]; then
	echo "FAIL: expected bootstrap file ${PLUGIN_SLUG}.php at zip root." >&2
	exit 1
fi

if ! grep -q "Text Domain: ${PLUGIN_SLUG}" "$BOOTSTRAP"; then
	echo "FAIL: bootstrap Text Domain header must be ${PLUGIN_SLUG}." >&2
	exit 1
fi

LEGACY_BOOTSTRAP="$STAGE_DIR/edminboost-smart-admin-productivity-tool.php"
if [[ -f "$LEGACY_BOOTSTRAP" ]]; then
	echo "FAIL: legacy bootstrap file must not ship in the WordPress.org zip." >&2
	exit 1
fi

FORBIDDEN_PATHS=(
	"vendor/freemius"
	"includes/pro"
	"admin/partials/pro"
	"admin/js/pro"
	"admin/css/pro"
)

FORBIDDEN_FILES=(
	"includes/class-edminboost-pro.php"
	"includes/class-edminboost-white-label.php"
)

# WordPress.org plugin directory banners/icons ship via SVN assets/, not the plugin zip.
WPORG_PLUGIN_ZIP_ASSET_GLOBS=(
	'assets/banner-*.png'
	'assets/icon-*.png'
)

FORBIDDEN_PATTERNS=(
	'[Ff]reemius'
	'fs_dynamic_init'
	'wp_org_gatekeeper'
	'is_premium([^_]|$)'
	'edminboost_is_pro_active'
	'edminboost_active_billing_plan'
	'EDMINBOOST_UPGRADE_URL'
	'class[[:space:]]+EDMINBOOST_Billing'
	'class[[:space:]]+EDMINBOOST_Plan_Licensing'
	'edminboost-billing-page\.php'
)

errors=0

echo "Verifying WordPress.org free build: $ZIP_PATH"

for rel_path in "${FORBIDDEN_PATHS[@]}"; do
	if [[ -e "$STAGE_DIR/$rel_path" ]]; then
		echo "FAIL: forbidden path present: $rel_path" >&2
		errors=$(( errors + 1 ))
	fi
done

for rel_file in "${FORBIDDEN_FILES[@]}"; do
	if [[ -f "$STAGE_DIR/$rel_file" ]]; then
		echo "FAIL: forbidden file present: $rel_file" >&2
		errors=$(( errors + 1 ))
	fi
done

if grep -R -n -E 'class[[:space:]]+EDMINBOOST_Pro' --include='*.php' "$STAGE_DIR" 2>/dev/null | grep -v '/includes/pro/' >/dev/null 2>&1; then
	echo "FAIL: EDMINBOOST_Pro class must ship only inside includes/pro/ (premium package)." >&2
	grep -R -n -E 'class[[:space:]]+EDMINBOOST_Pro' --include='*.php' "$STAGE_DIR" | grep -v '/includes/pro/' | head -10 >&2
	errors=$(( errors + 1 ))
fi

shopt -s nullglob
for pattern in "${WPORG_PLUGIN_ZIP_ASSET_GLOBS[@]}"; do
	for found in "$STAGE_DIR/$pattern"; do
		if [[ -f "$found" ]]; then
			echo "FAIL: WordPress.org banner/icon must not ship in plugin zip: ${found#$STAGE_DIR/}" >&2
			errors=$(( errors + 1 ))
		fi
	done
done
shopt -u nullglob

for pattern in "${FORBIDDEN_PATTERNS[@]}"; do
	if grep -R -n -E --include='*.php' --include='*.js' --include='*.css' "$pattern" "$STAGE_DIR" >/dev/null 2>&1; then
		echo "FAIL: forbidden pattern \"$pattern\" found:" >&2
		grep -R -n -E --include='*.php' --include='*.js' --include='*.css' "$pattern" "$STAGE_DIR" | head -20 >&2
		errors=$(( errors + 1 ))
	fi
done

if [[ "$errors" -gt 0 ]]; then
	echo "" >&2
	echo "$errors verification check(s) failed. This zip is not safe for WordPress.org." >&2
	exit 1
fi

echo "OK: no Freemius SDK, premium bootstrap, or wp_org_gatekeeper detected."
