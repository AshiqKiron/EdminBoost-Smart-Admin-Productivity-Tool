#!/usr/bin/env bash
#
# Build the direct-download / Freemius premium plugin zip (includes includes/pro/).
# Output: dist/edminboost-admin-customization-premium.zip
#
# Usage:
#   bash bin/build-premium-zip.sh
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=build-zip-lib.sh
source "$SCRIPT_DIR/build-zip-lib.sh"

PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
PLUGIN_SLUG="edminboost-admin-customization"
BUILD_DIR="$PLUGIN_DIR/dist"
STAGE_DIR="$BUILD_DIR/$PLUGIN_SLUG"
ZIP_BASENAME="${PLUGIN_SLUG}-premium.zip"

edminboost_build_require_tools

edminboost_build_rsync_stage "$PLUGIN_DIR" "$STAGE_DIR" \
	--exclude='uninstall.php'

ZIP_PATH="$(edminboost_build_create_zip "$BUILD_DIR" "$PLUGIN_SLUG" "$ZIP_BASENAME")"

echo "Built premium package: $ZIP_PATH"

if [[ -x "$SCRIPT_DIR/verify-premium-zip.sh" ]]; then
	bash "$SCRIPT_DIR/verify-premium-zip.sh" "$ZIP_PATH"
fi
