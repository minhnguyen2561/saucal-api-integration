<?php

class SauCal_API_Integration {
	private static $instance = null;

	private function __construct() {
		//Registering new API endpoint
		add_action( 'init', array( $this, 'register_api_integration_endpoint' ) );

		//Making the new endpoint available
		add_filter( 'query_vars', array( $this, 'api_integration_query_vars' ) );

		//Adding new item to the My Account menu items
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_api_integration_item_tab' ) );

		//Add callback to rendering the API Integration page
		add_action( 'woocommerce_account_api-integration_endpoint', array( $this, 'rendering_api_integration_page' ) );
	}

	/**
	 * @return SauCal_API_Integration
	 */
	public static function get_instance(): SauCal_API_Integration {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function register_api_integration_endpoint() {
		add_rewrite_endpoint( 'api-integration', EP_ROOT | EP_PAGES );
	}

	public function api_integration_query_vars( $vars ) {
		$vars[] = 'api-integration';

		return $vars;
	}

	public function add_api_integration_item_tab( $items ) {
		$items['api-integration'] = 'API Integration';

		return $items;
	}

	public function rendering_api_integration_page() {
		//Handling the form submission
		$response = $this->handling_api_form_submission();
		//Rendering the setting form here
		echo $this->output_api_form_setting();
		//@todo: rendering the $response data from API to show here
	}

	/**
	 * Creating the form element for API Integration here
	 * I am not too clear with how the Customer will interact with the form
	 * so, I will make it will a text form field and use it to submit to the API
	 *
	 * @return false|string
	 */
	private function output_api_form_setting() {
		ob_start();
		?>
        <form action="" method="post">
			<?php
			wp_nonce_field( 'send-post-request', 'sc-api-request' ); ?>
            <label>
				<?php
				_e( 'Entering elements to fetch, separating by comma', 'sc-api-integration' ); ?>
                <input type="text" name="sc_api_data" required/>
                <input type="submit" value="<?php
				_e( 'Retrieve data', 'sc-api-integration' ); ?>">
            </label>
        </form>
		<?php
		return ob_get_clean();
	}

	private function handling_api_form_submission() {
		$validated_data = $this->validation_data();
		if ( empty( $validated_data ) ) {
			return false;
		}

		//@todo: make the request to API here, using wp_remote_post(), wp_remote_retrieve_body() with SC_BASE_API_URL constant
		//@todo: considering to cache using wp_cache_set() for faster result next time, no need to fetch from API

	}

	private function validation_data() {
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

}