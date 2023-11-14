<?php

namespace WPAppsDev\PCBU\Helper;

/**
 * The PC builder manager helper class.
 */
class BuilderManager {
	/**
	 * Initialize the class.
	 */
	public function __construct() {
	}

	/**
	 * Builder page breadcrumb enable/disable.
	 *
	 * @return bool
	 */
	public function breadcrumb_enable() {
		$option = wpadpcbu_get_option( 'wpadpcbu_builder_breadcrumb_disable', 'builder_page_settings', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}

	/**
	 * Builder page breadcrumb enable/disable.
	 *
	 * @return bool
	 */
	public function builder_title() {
		return wpadpcbu_get_option( 'wpadpcbu_builder_breadcrumb_title', 'builder_page_settings', __( 'PC Builder - Build Your Own Computer', 'wpappsdev-pcbuilder' ) );
	}

	/**
	 * Builder page breadcrumb enable/disable.
	 *
	 * @return bool
	 */
	public function menu_title() {
		return wpadpcbu_get_option( 'wpadpcbu_builder_breadcrumb_menu_name', 'builder_page_settings', __( 'PC Builder', 'wpappsdev-pcbuilder' ) );
	}

	/**
	 * Builder page actions enable/disable.
	 *
	 * @return bool
	 */
	public function actions_enable() {
		$option = wpadpcbu_get_option( 'wpadpcbu_builder_actions_disable', 'builder_page_settings', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}

	/**
	 * Redirect checkout page enable/disable.
	 *
	 * @return bool
	 */
	public function redirect_checkout_enable() {
		$option = wpadpcbu_get_option( 'wpadpcbu_redirect_checkout_disable', 'builder_page_settings', 'off' );

		if ( 'on' === $option ) {
			return true;
		}

		return false;
	}

	/**
	 * Builder page actions list.
	 *
	 * @return bool
	 */
	public function builder_actions() {
		$actions = [
			'wpadpcbu-product-cart' => [
				'name'       => esc_attr__( 'Add To Cart', 'wpappsdev-pcbuilder' ),
				'href'       => '#',
				'icon-class' => 'dashicons dashicons-cart',
			],
			'wpadpcbu-save' => [
				'name'       => esc_attr__( 'Saved PC Configuration', 'wpappsdev-pcbuilder' ),
				'href'       => '#',
				'icon-class' => 'dashicons dashicons-vault',
			],
			'wpadpcbu-screenshots' => [
				'name'       => esc_attr__( 'Screenshots', 'wpappsdev-pcbuilder' ),
				'href'       => '#',
				'icon-class' => 'dashicons dashicons-camera',
			],
		];

		return apply_filters( 'wpadpcbu_builder_table_actions', $actions );
	}

	/**
	 * Builder page columns list.
	 *
	 * @return array
	 */
	public function table_columns() {
		$column = [
			'component' => __( 'Component', 'wpappsdev-pcbuilder' ),
			'image'     => __( 'Image', 'wpappsdev-pcbuilder' ),
			'name'      => __( 'Product Name', 'wpappsdev-pcbuilder' ),
			'price'     => __( 'Price', 'wpappsdev-pcbuilder' ),
			'action'    => __( 'Action', 'wpappsdev-pcbuilder' ),
		];

		return apply_filters( 'wpadpcbu_builder_table_columns', $column );
	}

	public function get_builder_data() {
		$pcbu_data = [];

		if ( is_callable( [ WC()->session, 'get' ] ) ) {
			$pcbu_data = WC()->session->get( 'wpadpcbu_pc_builder_data', [] );
		}

		return $pcbu_data;
	}
}
