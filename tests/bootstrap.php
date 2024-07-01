<?php
declare( strict_types=1 );
/**
 * PHPUnit bootstrap file
 *
 * @package Sample_Plugin
 */

// check location from phpunit.xml
$_tests_dir = './tests/tmp/wordpress-tests-lib';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?";
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

// Require the composer autoloader.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';
/**
 * Manually load the plugin being tested.
 */
tests_add_filter( 'muplugins_loaded', function(){
	require dirname( __DIR__ ) . '/storepress-base-plugin.php';
} );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
