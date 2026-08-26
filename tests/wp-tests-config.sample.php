<?php
/**
 * PHPUnit WordPress test configuration (copy to wp-tests-config.php).
 *
 * @package EdminBoost
 */

define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_HOST', 'localhost:/Applications/MAMP/tmp/mysql/mysql.sock' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

define( 'ABSPATH', '/Applications/MAMP/htdocs/wordpress/' );

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'EdminBoost Tests' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WPLANG', '' );
define( 'WP_DEBUG', true );
define( 'WP_TESTS_MULTISITE', false );

$table_prefix = 'wptests_';
