#!/usr/bin/env bash
#
# Build the WordPress.org free plugin zip (no premium / Freemius code).
# Output: dist/edminboost-admin-customization.zip
#
# Release checklist: docs/release-wordpress-org.md
#
# Usage:
#   bash bin/build-wporg-zip.sh
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=build-zip-lib.sh
source "$SCRIPT_DIR/build-zip-lib.sh"

PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
PLUGIN_SLUG="edminboost-admin-customization"
BUILD_DIR="$PLUGIN_DIR/dist"
STAGE_DIR="$BUILD_DIR/$PLUGIN_SLUG"
ZIP_BASENAME="$PLUGIN_SLUG.zip"

edminboost_build_require_tools

edminboost_build_rsync_stage "$PLUGIN_DIR" "$STAGE_DIR" \
	--exclude='includes/pro/' \
	--exclude='admin/partials/pro/' \
	--exclude='admin/js/pro/' \
	--exclude='admin/css/pro/' \
	--exclude='assets/banner-*.png' \
	--exclude='assets/icon-*.png'

ZIP_PATH="$(edminboost_build_create_zip "$BUILD_DIR" "$PLUGIN_SLUG" "$ZIP_BASENAME")"

echo "Built WordPress.org package: $ZIP_PATH"

if [[ -x "$SCRIPT_DIR/verify-wporg-zip.sh" ]]; then
	bash "$SCRIPT_DIR/verify-wporg-zip.sh" "$ZIP_PATH"
fi
