#!/usr/bin/env bash
#
# Configure QA users on the existing MAMP WordPress install (no separate site download).
#
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
WP_PATH="${WP_PATH:-/Applications/MAMP/htdocs/wordpress}"
QA_URL="${QA_URL:-http://localhost:8888/wordpress}"
WP_CLI="$SCRIPT_DIR/wp-cli.phar"
MAMP_PHP="${MAMP_PHP:-/Applications/MAMP/bin/php/php8.3.28/bin/php}"
PLUGIN_SLUG="edminboost-smart-admin-productivity-tool"
PLUGIN_BASENAME="$(basename "$PLUGIN_DIR")"
PLUGINS_DIR="$WP_PATH/wp-content/plugins"

if [ ! -f "$WP_CLI" ]; then
	echo "Downloading WP-CLI..."
	curl -sS -o "$WP_CLI" https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
	chmod +x "$WP_CLI"
fi

if [ ! -f "$WP_PATH/wp-config.php" ]; then
	echo "WordPress not found at $WP_PATH. Set WP_PATH to your install." >&2
	exit 1
fi

PLUGIN_TARGET="$PLUGINS_DIR/$PLUGIN_SLUG"
PLUGIN_ALREADY_IN_PLUGINS=false

case "$PLUGIN_DIR" in
	"$PLUGINS_DIR"/*)
		PLUGIN_ALREADY_IN_PLUGINS=true
		PLUGIN_ID="$PLUGIN_BASENAME"
		;;
esac

if [ "$PLUGIN_ALREADY_IN_PLUGINS" = false ] && [ ! -e "$PLUGIN_TARGET" ]; then
	ln -s "$PLUGIN_DIR" "$PLUGIN_TARGET"
	echo "Symlinked plugin to $PLUGIN_TARGET"
	PLUGIN_ID="$PLUGIN_SLUG"
elif [ "$PLUGIN_ALREADY_IN_PLUGINS" = true ]; then
	echo "Plugin already installed at $PLUGIN_DIR (skipping symlink)"
else
	PLUGIN_ID="$PLUGIN_SLUG"
fi

"$MAMP_PHP" -d memory_limit=512M "$WP_CLI" plugin activate "$PLUGIN_ID" --path="$WP_PATH" 2>/dev/null || true

if ! "$MAMP_PHP" -d memory_limit=512M "$WP_CLI" user get qaadmin --path="$WP_PATH" --field=ID 2>/dev/null; then
	"$MAMP_PHP" -d memory_limit=512M "$WP_CLI" user create qaadmin qaadmin@example.org \
		--role=administrator --user_pass=qaadmin123 --path="$WP_PATH"
fi

if ! "$MAMP_PHP" -d memory_limit=512M "$WP_CLI" user get qaeditor --path="$WP_PATH" --field=ID 2>/dev/null; then
	"$MAMP_PHP" -d memory_limit=512M "$WP_CLI" user create qaeditor qaeditor@example.org \
		--role=editor --user_pass=qaeditor123 --path="$WP_PATH"
fi

"$MAMP_PHP" -d memory_limit=512M "$WP_CLI" post list --path="$WP_PATH" --post_type=post --format=count 2>/dev/null | grep -q '^0$' && \
	"$MAMP_PHP" -d memory_limit=512M "$WP_CLI" post create --post_title="QA Post" --post_status=publish --path="$WP_PATH" || true

echo ""
echo "QA environment ready on existing WordPress install:"
echo "  URL:    $QA_URL/wp-admin"
echo "  Admin:  qaadmin / qaadmin123"
echo "  Editor: qaeditor / qaeditor123"
echo ""
echo "Copy .env.qa.example to .env.qa and set BASE_URL=$QA_URL"
