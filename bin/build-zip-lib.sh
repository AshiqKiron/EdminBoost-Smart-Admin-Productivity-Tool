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
		--exclude='.cursor/'
		--exclude='node_modules/'
		--exclude='vendor/'
		--exclude='tests/'
		--exclude='bin/'
		--exclude='dist/'
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
	)

	if [[ ${#extra_excludes[@]} -gt 0 ]]; then
		rsync_args+=( "${extra_excludes[@]}" )
	fi

	rsync "${rsync_args[@]}" "$plugin_dir/" "$stage_dir/"
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
