<?php
/**
 * Bootstrap the PHPUnit tests.
 */

declare( strict_types=1 );

// Detect where to load the WordPress tests environment from.
if ( false !== getenv( 'WP_TESTS_DIR' ) ) {
	$_test_root = getenv( 'WP_TESTS_DIR' );
} else {
	$_test_root = './tests/tmp/wordpress-tests-lib';
}

if ( ! file_exists( $_test_root . '/includes/functions.php' ) ) {
	echo "Could not find $_test_root/includes/functions.php. Seems test environment is not ready.";
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_test_root . '/includes/functions.php';

// Load Composer dependencies if applicable.
if ( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __DIR__ ) . '/vendor/autoload.php';
}

// Activate the plugin.
tests_add_filter(
	'muplugins_loaded',
	static function (): void {
		require dirname( __DIR__ ) . '/storepress-base-plugin.php';
	}
);

// Start up the WP testing environment.
require $_test_root . '/includes/bootstrap.php';
