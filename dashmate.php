<?php
/**
 * Plugin Name: Dashmate
 * Plugin URI: https://github.com/ernilambar/dashmate/
 * Description: Dashboard helper.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Nilambar Sharma
 * Author URI: https://nilambar.net/
 * License: GPLv2 or later
 * Text Domain: dashmate
 *
 * @package Dashmate
 */

use Nilambar\Dashmate\Boot\Loader;
use Nilambar\Dashmate\Widget_Initializer;

// Define.
define( 'DASHMATE_VERSION', '1.0.0' );
define( 'DASHMATE_BASE_NAME', basename( __DIR__ ) );
define( 'DASHMATE_BASE_FILEPATH', __FILE__ );
define( 'DASHMATE_BASE_FILENAME', plugin_basename( __FILE__ ) );
define( 'DASHMATE_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'DASHMATE_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );

// Include autoload.
if ( file_exists( DASHMATE_DIR . '/vendor/autoload.php' ) ) {
	require_once DASHMATE_DIR . '/vendor/autoload.php';
}

// Load CLI.
require_once DASHMATE_DIR . '/cli.php';

// Activation hook.
register_activation_hook( __FILE__, 'dashmate_activate' );

/**
 * Plugin activation callback.
 *
 * @since 1.0.0
 */
function dashmate_activate() {
	// Ensure autoload is available.
	if ( file_exists( DASHMATE_DIR . '/vendor/autoload.php' ) ) {
		require_once DASHMATE_DIR . '/vendor/autoload.php';
	}

	Widget_Initializer::create_default_dashboard_on_activation();
}

// Init.
new Loader();
