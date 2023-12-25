<?php
/**
 * Plugin Name: Saucal API Integration
 * Version: 1.0.0
 * Text Domain: sc-api-integration
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SC_BASE_API_URL', 'https://httpbin.org/post' );

/**
 * Adding new tab to the WC My Account page
 */

//Registering new API endpoint
function register_api_integration_endpoint() {
	add_rewrite_endpoint( 'api-integration', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'register_api_integration_endpoint' );

//Making the new endpoint available
function api_integration_query_vars( $vars ) {

	$vars[] = 'api-integration';

	return $vars;
}

add_filter( 'query_vars', 'api_integration_query_vars' );

//Adding new item to the My Account menu items
function add_api_integration_item_tab( $items ) {

	$items['api-integration'] = 'API Integration';

	return $items;
}

add_filter( 'woocommerce_account_menu_items', 'add_api_integration_item_tab' );

//Add callback to rendering the API Integration page
function rendering_api_integration_page() {
	//Handling the form submission
	$response = handling_api_form_submission();
	//Rendering the setting form here
	echo output_api_form_setting();
	//@todo: rendering the $response data from API to show here
}

add_action( 'woocommerce_account_api-integration_endpoint', 'rendering_api_integration_page' );

/**
 * Creating the form element for API Integration here
 * I am not too clear with how the Customer will interact with the form
 * so, I will make it will a text form field and use it to submit to the API
 *
 * @return false|string
 */
function output_api_form_setting() {
	ob_start();
	?>
    <form action="" method="post">
		<?php wp_nonce_field( 'send-post-request', 'sc-api-request' ); ?>
        <label>
			<?php _e( 'Entering elements to fetch, separating by comma', 'sc-api-integration' ); ?>
            <input type="text" name="sc_api_data"/>
        </label>
    </form>
	<?php
	return ob_get_clean();
}

function handling_api_form_submission() {
	$validated_data = validation_data();
	if ( empty( $validated_data ) ) {
		return false;
	}

	//@todo: make the request to API here, using wp_remote_post(), wp_remote_retrieve_body() with SC_BASE_API_URL constant
	//@todo: considering to cache using wp_cache_set() for faster result next time, no need to fetch from API

}

function validation_data() {
	if ( empty( $_POST['sc_api_data'] ) ) {
		return array();
	}
	if ( ! isset( $_POST['sc-api-request'] ) || ! wp_verify_nonce( $_POST['sc-api-request'], 'send-post-request' ) ) {
		//Form validation failed here
		return array();
	}
	//Removing all spaces
	$submitted_elements = preg_replace( '/\s+/', '', $_POST['sc_api_data'] );

	//Converting to array
	$element_to_fetch = explode( ",", $submitted_elements );

	//@todo: validate to remove non-characters and non-number from each element item

	return $element_to_fetch;
}