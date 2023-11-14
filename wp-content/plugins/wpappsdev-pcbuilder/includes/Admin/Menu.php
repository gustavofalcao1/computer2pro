<?php

namespace WPAppsDev\PCBU\Admin;

use WPAppsDev\WpadSettingApi;

/**
 * The Menu handler class.
 */
class Menu {
	/**
	 * Hold menu page ID.
	 *
	 * @var string
	 */
	protected $menupage;

	/**
	 * Hold settings api object.
	 *
	 * @var object
	 */
	protected static $settings;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		self::$settings = new WpadSettingApi();

		// Register admin menu.
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		// Change admin menu order.
		add_action( 'admin_menu', [ $this, 'change_menu_order' ], 99 );
		// Update active menu parent file for new menu order.
		add_action( 'admin_head', [ $this, 'maybe_change_menu_parent_file' ] );
	}

	/**
	 * Register admin menu.
	 *
	 * @return void
	 */
	public function admin_menu() {
		$base_slug  = 'wpadpcbu-pcbuilder';
		$manage     = 'manage_woocommerce';
		$capability = apply_filters( 'wpadpcbu_manager_capability', $manage );

		$this->menupage = add_menu_page(
			__( 'WC Pc Builder', 'wpappsdev-pcbuilder' ),
			__( 'WC PcBuilder', 'wpappsdev-pcbuilder' ),
			$capability,
			$base_slug,
			[ $this, 'settings_page' ],
			'dashicons-welcome-widgets-menus',
			50
		);
	}

	/**
	 * Change admin menu order.
	 *
	 * @return void
	 */
	public function change_menu_order() {
		global $submenu;
		$component_menu = [];
		$p_menus        = $submenu['edit.php?post_type=product'];

		foreach ( $p_menus as $key => $p_menu ) {
			if ( 'edit-tags.php?taxonomy=pcbucomp&amp;post_type=product' === $p_menu[2] ) {
				$component_menu    = $p_menu;
				$component_menu[0] = __( 'PC Components', 'wpappsdev-pcbuilder' );
				unset( $submenu['edit.php?post_type=product'][ $key ] );
			}
		}

		if ( ! empty( $component_menu ) ) {
			$pcbuilder_menu                = [ $component_menu ];
			$pcbuilder_menu                = array_merge( $pcbuilder_menu, $submenu['wpadpcbu-pcbuilder'] );
			$submenu['wpadpcbu-pcbuilder'] = $pcbuilder_menu;
		}
	}

	/**
	 * Update active menu parent file for new menu order.
	 *
	 * @return void
	 */
	public function maybe_change_menu_parent_file() {
		global $parent_file;

		if ( 'edit.php?post_type=product' != $parent_file ) {
			return;
		}

		if ( ! isset( $_REQUEST['taxonomy'] ) ) {
			return;
		}

		if ( 'pcbucomp' == $_REQUEST['taxonomy'] ) {
			$parent_file = 'wpadpcbu-pcbuilder';
		}

		$tax_array = explode( '-', wc_clean( $_REQUEST['taxonomy'] ) );

		if ( count( $tax_array ) > 1 && 'cf' == $tax_array[0] ) {
			$parent_file = 'wpadpcbu-pcbuilder';
		}

		return;
	}
}
