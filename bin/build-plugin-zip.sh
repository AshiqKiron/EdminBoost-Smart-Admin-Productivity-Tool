#!/usr/bin/env bash
#
# Back-compat alias: builds the WordPress.org zip (same as build-wporg-zip.sh).
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
exec bash "$SCRIPT_DIR/build-wporg-zip.sh" "$@"
