#!/usr/bin/env bash
#
# Regenerate the plugin POT template (text domain = plugin slug).
#
# Usage:
#   bash bin/make-pot.sh
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
DOMAIN="edminboost-admin-customization"
OUT_FILE="$PLUGIN_DIR/languages/${DOMAIN}.pot"

cd "$PLUGIN_DIR"

if [[ ! -x vendor/bin/wp ]]; then
	echo "Run composer install first (requires wp-cli/i18n-command)." >&2
	exit 1
fi

mkdir -p "$PLUGIN_DIR/languages"

vendor/bin/wp i18n make-pot "$PLUGIN_DIR" "$OUT_FILE" \
	--domain="$DOMAIN" \
	--slug="$DOMAIN" \
	--exclude=includes/pro/freemius,vendor,node_modules,tests,dist,build,.git,.cursor \
	--headers='{"Report-Msgid-Bugs-To":"https://asphaltthemes.com/edminboost"}'

echo "Wrote $OUT_FILE"
