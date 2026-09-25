#!/usr/bin/env bash
#
# Verify a distributable plugin zip is safe for the WordPress.org free build.
# Fails when premium/licensing SDK code or Freemius symbols are present.
#
# Usage:
#   bash bin/verify-wporg-zip.sh [path/to/edminboost-smart-admin-productivity-tool.zip]
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
PLUGIN_SLUG="edminboost-smart-admin-productivity-tool"
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

FORBIDDEN_PATHS=(
	"vendor/freemius"
	"includes/pro"
)

FORBIDDEN_PATTERNS=(
	'[Ff]reemius'
	'fs_dynamic_init'
	'wp_org_gatekeeper'
	'is_premium'
)

errors=0

echo "Verifying WordPress.org free build: $ZIP_PATH"

for rel_path in "${FORBIDDEN_PATHS[@]}"; do
	if [[ -e "$STAGE_DIR/$rel_path" ]]; then
		echo "FAIL: forbidden path present: $rel_path" >&2
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

if [[ "$errors" -gt 0 ]]; then
	echo "" >&2
	echo "$errors verification check(s) failed. This zip is not safe for WordPress.org." >&2
	exit 1
fi

echo "OK: no Freemius SDK, premium bootstrap, or wp_org_gatekeeper detected."
