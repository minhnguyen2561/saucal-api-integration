<?php
/**
 * Plugin Name: SauCal API Integration
 * Description: Simple plugin to demonstrate the external API integration
 * Version: 1.0.0
 * Text Domain: sc-api-integration
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SAUCAL_BASE_API_URL', 'https://httpbin.org/post' );
define( 'SAUCAL_DIR', __DIR__ );
define( 'SAUCAL_URL', plugin_dir_url( __FILE__ ) );

//Include the main class file
require SAUCAL_DIR . '/SauCal_API_Integration.php';

/**
 * Returns the main instance of SauCal API.
 *
 * @return SauCal_API_Integration
 */
function saucal() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return SauCal_API_Integration::get_instance();
}

saucal();