#!/usr/bin/env bash
#
# Run PHPUnit with MAMP PHP (homebrew PHP cannot reach MAMP MySQL by default).
#
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

MAMP_PHP="${MAMP_PHP:-/Applications/MAMP/bin/php/php8.3.28/bin/php}"

if [ ! -x "$MAMP_PHP" ]; then
	echo "MAMP PHP not found at $MAMP_PHP. Set MAMP_PHP to your MAMP php binary." >&2
	exit 1
fi

cd "$PLUGIN_DIR"
exec "$MAMP_PHP" vendor/bin/phpunit "$@"
