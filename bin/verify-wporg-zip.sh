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
	"assets"
	"docs"
	".cursor"
)

FORBIDDEN_FILES=(
	"includes/class-edminboost-pro.php"
	"includes/class-edminboost-white-label.php"
	"admin/partials/edminboost-settings-page.php"
	"EdminBoost.code-workspace"
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
	'edminboost-pro-package\.php'
	'has_licensed_admin_ui'
	'EDMINBOOST_Command_Center_Bar_Pro'
)

errors=0

echo "Verifying WordPress.org free build: $ZIP_PATH"

if grep -q 'includes/pro/edminboost-premium.php' "$BOOTSTRAP" 2>/dev/null; then
	echo "FAIL: WordPress.org bootstrap must not load includes/pro/edminboost-premium.php." >&2
	errors=$(( errors + 1 ))
fi

if grep -q 'includes/pro/edminboost-pro-package.php' "$BOOTSTRAP" 2>/dev/null; then
	echo "FAIL: WordPress.org bootstrap must not reference includes/pro/edminboost-pro-package.php." >&2
	errors=$(( errors + 1 ))
fi

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

for pro_class in 'EDMINBOOST_Pro' 'EDMINBOOST_Command_Center_Bar_Pro' 'EDMINBOOST_White_Label' 'EDMINBOOST_Billing' 'EDMINBOOST_Plan_Licensing'; do
	if grep -R -n -E "class[[:space:]]+${pro_class}" --include='*.php' "$STAGE_DIR" 2>/dev/null | grep -v '/includes/pro/' >/dev/null 2>&1; then
		echo "FAIL: ${pro_class} class must ship only inside includes/pro/ (premium package)." >&2
		grep -R -n -E "class[[:space:]]+${pro_class}" --include='*.php' "$STAGE_DIR" | grep -v '/includes/pro/' | head -10 >&2
		errors=$(( errors + 1 ))
	fi
done

for pattern in "${FORBIDDEN_PATTERNS[@]}"; do
	if grep -R -n -E --include='*.php' --include='*.js' --include='*.css' "$pattern" "$STAGE_DIR" >/dev/null 2>&1; then
		echo "FAIL: forbidden pattern \"$pattern\" found:" >&2
		grep -R -n -E --include='*.php' --include='*.js' --include='*.css' "$pattern" "$STAGE_DIR" | head -20 >&2
		errors=$(( errors + 1 ))
	fi
done

while IFS= read -r -d '' md_file; do
	rel_md="${md_file#"$STAGE_DIR"/}"
	echo "FAIL: product documentation must not ship in the WordPress.org zip: $rel_md" >&2
	errors=$(( errors + 1 ))
done < <(find "$STAGE_DIR" -name '*.md' -print0 2>/dev/null)

POT_FILE="$STAGE_DIR/languages/${PLUGIN_SLUG}.pot"
if [[ -f "$POT_FILE" ]]; then
	if grep -qE 'admin/partials/pro/|admin/js/pro/|admin/css/pro/|includes/pro/' "$POT_FILE" 2>/dev/null; then
		echo "FAIL: WordPress.org POT must not reference premium source paths (rebuild with composer install so make-pot runs on the staged free tree)." >&2
		grep -n -E 'admin/partials/pro/|admin/js/pro/|admin/css/pro/|includes/pro/' "$POT_FILE" | head -15 >&2
		errors=$(( errors + 1 ))
	fi
fi

if [[ "$errors" -gt 0 ]]; then
	echo "" >&2
	echo "$errors verification check(s) failed. This zip is not safe for WordPress.org." >&2
	exit 1
fi

echo "OK: no Freemius SDK, premium bootstrap, or wp_org_gatekeeper detected."
