#!/usr/bin/env bash
#
# Sanity-check a premium distributable zip (includes premium bootstrap path).
#
# Usage:
#   bash bin/verify-premium-zip.sh [path/to/edminboost-admin-customization-premium.zip]
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
PLUGIN_SLUG="edminboost-admin-customization"
ZIP_PATH="${1:-$PLUGIN_DIR/dist/${PLUGIN_SLUG}-premium.zip}"

if [[ ! -f "$ZIP_PATH" ]]; then
	echo "ZIP not found: $ZIP_PATH" >&2
	echo "Build one first: bash bin/build-premium-zip.sh" >&2
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

echo "Verifying premium build: $ZIP_PATH"

errors=0

BOOTSTRAP="$STAGE_DIR/edminboost-admin-customization.php"
if [[ ! -f "$BOOTSTRAP" ]]; then
	echo "FAIL: plugin bootstrap missing." >&2
	exit 1
fi

if ! grep -q "Text Domain: ${PLUGIN_SLUG}" "$BOOTSTRAP"; then
	echo "FAIL: bootstrap Text Domain header must be ${PLUGIN_SLUG}." >&2
	exit 1
fi

if grep -q "includes/pro/edminboost-premium.php" "$BOOTSTRAP" 2>/dev/null; then
	echo "OK: bootstrap loads includes/pro/edminboost-premium.php when present."
else
	echo "WARN: bootstrap does not reference includes/pro/edminboost-premium.php yet."
	echo "      Add a conditional require before shipping the premium build to customers."
fi

if [[ -f "$STAGE_DIR/uninstall.php" ]]; then
	echo "FAIL: uninstall.php must not ship in the premium zip (use Freemius after_uninstall)." >&2
	exit 1
fi
echo "OK: uninstall.php omitted (Freemius after_uninstall cleanup)."

if [[ -d "$STAGE_DIR/docs" ]]; then
	echo "FAIL: docs/ must not ship in distributable zips (external docs site only)." >&2
	errors=$(( errors + 1 ))
else
	echo "OK: docs/ omitted."
fi

if [[ -d "$STAGE_DIR/.cursor" ]]; then
	echo "FAIL: .cursor/ must not ship in distributable zips." >&2
	errors=$(( errors + 1 ))
fi

if [[ -f "$STAGE_DIR/EdminBoost.code-workspace" ]]; then
	echo "FAIL: editor workspace files must not ship in distributable zips." >&2
	errors=$(( errors + 1 ))
fi

while IFS= read -r -d '' md_file; do
	if [[ "$md_file" == *"/includes/pro/freemius/"* ]]; then
		continue
	fi
	rel_md="${md_file#"$STAGE_DIR"/}"
	echo "FAIL: product documentation must not ship in distributable zips: $rel_md" >&2
	errors=$(( errors + 1 ))
done < <(find "$STAGE_DIR" -name '*.md' -print0 2>/dev/null)

if [[ "$errors" -gt 0 ]]; then
	echo "" >&2
	echo "$errors verification check(s) failed." >&2
	exit 1
fi

if [[ -d "$STAGE_DIR/includes/pro" ]]; then
	echo "OK: includes/pro/ is present in the premium zip."
	if [[ ! -f "$STAGE_DIR/includes/pro/edminboost-premium.php" ]]; then
		echo "WARN: includes/pro/edminboost-premium.php is missing — add licensing init before release."
	fi
else
	echo "WARN: includes/pro/ is not in the repo; premium zip matches core until you add licensing files."
fi

echo "Premium zip structure check passed."
