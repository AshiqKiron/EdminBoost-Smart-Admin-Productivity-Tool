#!/usr/bin/env bash
#
# Shared staging helpers for EdminBoost distributable zips.
# Sourced by build-wporg-zip.sh and build-premium-zip.sh — do not run directly.
#
# shellcheck shell=bash

if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
	echo "Source this file from a build script; do not execute it directly." >&2
	exit 1
fi

edminboost_build_require_tools() {
	if ! command -v rsync >/dev/null 2>&1; then
		echo "rsync is required but was not found in PATH." >&2
		exit 1
	fi

	if ! command -v zip >/dev/null 2>&1; then
		echo "zip is required but was not found in PATH." >&2
		exit 1
	fi
}

# Common rsync excludes for public plugin packages (dev/QA tooling omitted).
edminboost_build_rsync_stage() {
	local plugin_dir="$1"
	local stage_dir="$2"
	shift 2

	rm -rf "$stage_dir"
	mkdir -p "$stage_dir"

	# Optional extra --exclude arguments (e.g. includes/pro/ for WordPress.org).
	local -a extra_excludes=()
	if [[ $# -gt 0 ]]; then
		extra_excludes=( "$@" )
	fi

	local -a rsync_args=(
		-a
		--exclude='.git/'
		--exclude='.gitignore'
		--exclude='.cursor/'
		--exclude='node_modules/'
		--exclude='vendor/'
		--exclude='tests/'
		--exclude='docs/'
		--exclude='bin/'
		--exclude='dist/'
		--exclude='*.code-workspace'
		--exclude='build/'
		--exclude='.env.qa'
		--exclude='.env.qa.example'
		--exclude='composer.json'
		--exclude='composer.lock'
		--exclude='package.json'
		--exclude='package-lock.json'
		--exclude='phpunit.xml.dist'
		--exclude='.phpunit.result.cache'
		--exclude='.playwright-browsers/'
		--exclude='playwright-report/'
		--exclude='test-results/'
		--exclude='.DS_Store'
		--exclude='Thumbs.db'
		--exclude='*.log'
		--exclude='edminboost-localhost.php'
	)

	if [[ ${#extra_excludes[@]} -gt 0 ]]; then
		rsync_args+=( "${extra_excludes[@]}" )
	fi

	rsync "${rsync_args[@]}" "$plugin_dir/" "$stage_dir/"
}

# Inject premium bootstrap requires into a staged copy (WordPress.org source stays pro-free).
edminboost_build_inject_premium_bootstrap() {
	local bootstrap="$1"

	if [[ ! -f "$bootstrap" ]]; then
		echo "Bootstrap not found: $bootstrap" >&2
		exit 1
	fi

	local tmp
	tmp="$(mktemp)"

	awk '
		/define\( '\''EDMINBOOST_PLUGIN_DOCS_URL'\''/ && !injected_premium {
			print
			print ""
			print "if ( file_exists( EDMINBOOST_PLUGIN_DIR . '\''includes/pro/edminboost-premium.php'\'' ) ) {"
			print "\trequire_once EDMINBOOST_PLUGIN_DIR . '\''includes/pro/edminboost-premium.php'\'';"
			print "}"
			injected_premium = 1
			next
		}
		/if \( null === \$plugin \)/ {
			print
			getline
			if ( !injected_package ) {
				print "\t\tif ( function_exists( '\''edminboost_premium_load_pro_package'\'' ) ) {"
				print "\t\t\tedminboost_premium_load_pro_package();"
				print "\t\t}"
				print ""
				injected_package = 1
			}
			print
			next
		}
		{ print }
	' "$bootstrap" > "$tmp"

	mv "$tmp" "$bootstrap"
}

# Regenerate languages/*.pot from staged sources (WordPress.org zip — no includes/pro/ in stage).
edminboost_build_regenerate_wporg_pot() {
	local dev_plugin_dir="$1"
	local stage_dir="$2"
	local domain="edminboost-admin-customization"
	local wp_i18n="$dev_plugin_dir/vendor/bin/wp"
	local out_file="$stage_dir/languages/${domain}.pot"

	if [[ ! -x "$wp_i18n" ]]; then
		echo "Note: vendor/bin/wp not found — WordPress.org zip keeps the repo POT (run composer install + rebuild for a Pro-free POT)." >&2
		return 0
	fi

	mkdir -p "$stage_dir/languages"

	"$wp_i18n" i18n make-pot "$stage_dir" "$out_file" \
		--domain="$domain" \
		--slug="$domain" \
		--exclude=vendor,node_modules,tests,dist,build,.git,.cursor \
		--headers='{"Report-Msgid-Bugs-To":"https://asphaltthemes.com/edminboost"}'

	echo "Regenerated Pro-free POT for WordPress.org package: $out_file"
}

edminboost_build_create_zip() {
	local build_dir="$1"
	local plugin_slug="$2"
	local zip_basename="$3"
	local stage_dir="$build_dir/$plugin_slug"
	local zip_path="$build_dir/$zip_basename"

	rm -f "$zip_path"
	(
		cd "$build_dir"
		zip -rq "$zip_basename" "$plugin_slug"
	)

	echo "$zip_path"
}
