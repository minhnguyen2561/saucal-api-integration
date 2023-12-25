<?php
/**
 * Plugin Name: SauCal API Integration
 * Version: 1.0.0
 * Text Domain: sc-api-integration
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Include the main class file
require __DIR__ . '/SauCal_API_Integration.php';

define( 'SC_BASE_API_URL', 'https://httpbin.org/post' );

/**
 * Returns the main instance of SauCal API.
 *
 * @return SauCal_API_Integration
 */
function saucal() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return SauCal_API_Integration::get_instance();
}

saucal();