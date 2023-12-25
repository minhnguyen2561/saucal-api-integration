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

		//Enqueue the scripts/styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		//Ajax request
		add_action( 'wp_ajax_saucal_request_api', array( $this, 'saucal_request_api' ) );
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

	/**
	 * @return void
	 */
	public function register_api_integration_endpoint(): void {
		add_rewrite_endpoint( 'api-integration', EP_ROOT | EP_PAGES );
	}

	/**
	 * @param $vars
	 *
	 * @return mixed
	 */
	public function api_integration_query_vars( $vars ) {
		$vars[] = 'api-integration';

		return $vars;
	}

	/**
	 * @param $items
	 *
	 * @return mixed
	 */
	public function add_api_integration_item_tab( $items ) {
		$items['api-integration'] = 'API Integration';

		return $items;
	}

	/**
	 * @return void
	 */
	public function rendering_api_integration_page() {
		//Rendering the setting form here
		ob_start();
		require_once 'templates/myaccount/saucal-form.php';

		echo ob_get_clean();
	}

	/**
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'saucal-api-frontend-js', SAUCAL_URL . 'assets/js/frontend.js', array( 'jquery' ), null, true );
		wp_localize_script( 'saucal-api-frontend-js', 'saucal_ajax_obj',
			array(
				'ajaxurl'            => admin_url( 'admin-ajax.php' ),
				'current_user_id'    => is_user_logged_in() ? get_current_user_id() : 0,
				'nonce'              => wp_create_nonce( 'send-post-request' ),
				'i18n_time_out'      => __( 'Time out', 'sc-api-integration' ),
				'i18n_loading_state' => __( 'Loading...', 'sc-api-integration' ),
			)
		);
	}

	/**
	 * @return void
	 */
	public function saucal_request_api() {
		//Validating post data
		$validated_data = $this->validation_data();
		if ( empty( $validated_data ) ) {
			wp_send_json_error( array( 'code' => 'failed_validation', 'message' => __( 'Failed validation', 'sc-api-integration' ) ) );
		}
		$validated_data  = apply_filters( 'saucal_api_request_data', $validated_data );
		$current_user_id = (int) ( isset( $_POST['user_id'] ) ?? 0 );
		$cache_key       = 'saucal_api_response_for_user_' . $current_user_id;

		/**
		 * Checking for available cache here
		 *
		 * @todo:
		 *
		 */
		$cached_data = get_transient( $cache_key );
		if ( ! empty( $cached_data ) ) {
			$response  = $cached_data;
			$is_cached = true;
		} else {
			//Making the request to API
			$args    = array(
				'method'    => 'POST',
				'timeout'   => 10,
				'sslverify' => true,
				'body'      => json_encode( $validated_data ),
			);
			$request = wp_remote_post( SAUCAL_BASE_API_URL, apply_filters( 'saucal_api_request_args', $args ) );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				wp_send_json_error( array( 'code' => 'failed_request', 'message' => __( 'Failed request', 'sc-api-integration' ) ) );
			}

			$response = wp_remote_retrieve_headers( $request );
			if ( is_wp_error( $response ) ) {
				wp_send_json_error( array( 'code' => 'failed_request', 'message' => __( 'Failed request', 'sc-api-integration' ) ) );
			}

			//Set the cache
			//$todo: since it will save 1 cache for 1 user as the same time, if we need to cache for each request,
			//      it should be done on API server
			set_transient( $cache_key, $response, apply_filters( 'saucal_api_cache_duration', HOUR_IN_SECONDS ) );
			$is_cached = false;
		}

		//Rendering the API Response

		ob_start();
		require_once 'templates/myaccount/saucal-fetch-result.php';

		wp_send_json_success( array( 'data' => ob_get_clean(), 'cache' => $is_cached ) );
	}

	/**
	 * @return false|string[]
	 */
	private function validation_data() {
		if ( empty( $_POST['elements'] ) ) {
			return false;
		}

		if ( ! isset( $_POST['sc_api_request'] ) || ! check_ajax_referer( 'send-post-request', 'sc_api_request' ) ) {
			//Form validation failed here
			return false;
		}
		//Removing all spaces
		$submitted_elements = preg_replace( '/\s+/', '', $_POST['elements'] );

		//Removing non-characters and non-numberics
		$submitted_elements = preg_replace( "/[^A-Za-z0-9]/", "", $submitted_elements );

		//Converting to array
		return explode( ",", $submitted_elements );
	}

}