<?php

namespace WPAppsDev\PCBU;

/**
 * The frontend class.
 */
class Frontend {
	/**
	 * Initialize the class.
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'maybe_register_session' ], 99 );
		add_action( 'wp_footer', [ $this, 'add_custom_css_code' ] );
	}

	/**
	 * Set WC session for non-login user.
	 *
	 * @return void
	 */
	public function maybe_register_session() {
		if ( ! is_user_logged_in() && ! headers_sent() ) {
			if ( isset( WC()->session ) && is_callable( [ WC()->session, 'has_session' ] ) ) {
				if ( ! WC()->session->has_session() ) {
					WC()->session->set_customer_session_cookie( true );
				}
			}
		}
	}

	/**
	 * Add custom css code in footer.
	 *
	 * @return void
	 */
	public function add_custom_css_code() {
		echo '<style>
			body.woocommerce-account ul li.woocommerce-MyAccount-navigation-link.woocommerce-MyAccount-navigation-link--savedpcs a::before {
				content: "\f472";
				font-family: dashicons;
			}
		</style>';
	}
}
