<?php

do_action( 'saucal_api_before_user_form_tag' ); ?>
<form class="woocommerce-saucal-api-form" action="" method="post">
	<?php do_action( 'saucal_api_user_form_start' ); ?>
    <p class="woocommerce-form-row form-row form-row-full">
        <label for="sc_api_data">
			<?php _e( 'Elements to fetch', 'sc-api-integration' ); ?>
            <span class="required">*</span>
        </label>
        <textarea type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="sc_api_data" id="sc_api_data" required></textarea>
        <span>
            <em>
                <?php esc_html_e( 'Separating by comma', 'sc-api-integration' ); ?>
            </em>
        </span>
    </p>
    <p>
		<?php wp_nonce_field( 'send-post-request', 'sc-api-request' ); ?>
        <button type="submit" class="woocommerce-Button button">
			<?php esc_html_e( 'Submit', 'sc-api-integration' ); ?>
        </button>
        <input type="hidden" name="action" value="saucal_get_api_response"/>
    </p>
	<?php do_action( 'saucal_api_user_form_end' ); ?>
</form>
<?php do_action( 'saucal_api_after_user_form_tag' ); ?>
