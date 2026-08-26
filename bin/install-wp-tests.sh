#!/usr/bin/env bash
#
# Install WordPress test library for PHPUnit (MAMP-friendly defaults).
#
# Usage: bash bin/install-wp-tests.sh [db-name] [db-user] [db-pass] [db-host] [wp-version]
#
set -e

DB_NAME=${1:-edminboost_qa}
DB_USER=${2:-root}
DB_PASS=${3:-root}
DB_HOST=${4:-localhost:8889}
WP_VERSION=${5:-latest}
TMPDIR=${TMPDIR:-/tmp}
WP_TESTS_DIR=${WP_TESTS_DIR:-$TMPDIR/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR:-$TMPDIR/wordpress}

download() {
	if command -v curl >/dev/null 2>&1; then
		curl -s "$1"
	elif command -v wget >/dev/null 2>&1; then
		wget -q -O - "$1"
	else
		echo "curl or wget required." >&2
		exit 1
	fi
}

if [ ! -d "$WP_TESTS_DIR" ]; then
	mkdir -p "$WP_TESTS_DIR"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/functions.php > "$WP_TESTS_DIR/includes/functions.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/bootstrap.php > "$WP_TESTS_DIR/includes/bootstrap.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/class-wp-unittest-case.php > "$WP_TESTS_DIR/includes/class-wp-unittest-case.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/class-wp-unittest-case-base.php > "$WP_TESTS_DIR/includes/class-wp-unittest-case-base.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory.php > "$WP_TESTS_DIR/includes/factory.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory-for-thing.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory-for-thing.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory-for-post.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory-for-post.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory-for-user.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory-for-user.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory-for-comment.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory-for-comment.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory-for-bookmark.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory-for-bookmark.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory-for-blog.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory-for-blog.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory-for-network.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory-for-network.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/factory/class-wp-unittest-factory-for-term.php > "$WP_TESTS_DIR/includes/factory/class-wp-unittest-factory-for-term.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/utils.php > "$WP_TESTS_DIR/includes/utils.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/trac.php > "$WP_TESTS_DIR/includes/trac.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/mock-mailer.php > "$WP_TESTS_DIR/includes/mock-mailer.php"
	download https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/wp-tests-config.php > "$WP_TESTS_DIR/wp-tests-config.php"
fi

if [ ! -d "$WP_CORE_DIR" ]; then
	mkdir -p "$WP_CORE_DIR"
	download https://wordpress.org/wordpress-${WP_VERSION}.tar.gz | tar xz -C "$TMPDIR"
	mv "$TMPDIR/wordpress" "$WP_CORE_DIR"
fi

cat > "$WP_TESTS_DIR/wp-tests-config.php" <<EOF
<?php
define( 'DB_NAME', '$DB_NAME' );
define( 'DB_USER', '$DB_USER' );
define( 'DB_PASSWORD', '$DB_PASS' );
define( 'DB_HOST', '$DB_HOST' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'EdminBoost Tests' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_TESTS_MULTISITE', false );
define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );
\$table_prefix = 'wptests_';
EOF

echo "WP test library installed at: $WP_TESTS_DIR"
echo "WP core at: $WP_CORE_DIR"
echo "Export before running PHPUnit:"
echo "  export WP_TESTS_DIR=$WP_TESTS_DIR"
echo "  export WP_CORE_DIR=$WP_CORE_DIR"
