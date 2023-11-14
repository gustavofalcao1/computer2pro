<?php

namespace WPAppsDev\PCBU\Frontend;

use WPAppsDev\PCBU\Traits\Singleton;

/**
 * The customer dashboard class.
 */
class CustomerDashboard {
	use Singleton;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		// Register new permalink endpoints
		add_action( 'init', [ $this, 'add_custom_endpoints' ] );
		// Add new query vars
		add_filter( 'query_vars', [ $this, 'add_custom_query_vars' ] );
		// Add new link tab to My Account menu
		add_filter( 'woocommerce_account_menu_items', [ $this, 'add_custom_menu_items' ] );
		// Add save pc tab content
		add_action( 'woocommerce_account_savedpcs_endpoint', [ $this, 'saved_pcs_content' ] );
		// View saved pc content
		add_action( 'woocommerce_account_view-savedpc_endpoint', [ $this, 'view_savedpc_content' ] );
	}

	/**
	 * Register new permalink endpoints
	 *
	 * @return void
	 */
	public function add_custom_endpoints() {
		add_rewrite_endpoint( 'savedpcs', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'view-savedpc', EP_ROOT | EP_PAGES );

		if ( get_transient( 'wpadpcbu_flush_rewrite' ) ) {
			flush_rewrite_rules();
			delete_transient( 'wpadpcbu_flush_rewrite' );
		}
	}

	/**
	 * Add new query vars
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function add_custom_query_vars( $vars ) {
		$vars[] = 'savedpcs';
		$vars[] = 'view-savedpc';

		return $vars;
	}

	/**
	 *  Add new link tab to My Account menu
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public function add_custom_menu_items( $items ) {
		$new = [
			'savedpcs' => __( 'PC Configurations', 'wpappsdev-pcbuilder' ),
		];

		$items = wpadpcbu_array_insert_after( $items, 'orders', $new );

		return $items;
	}

	/**
	 * Add save pc tab content
	 *
	 * @return void
	 */
	public function saved_pcs_content() {
		$columns = [
			'savepc-id'      => __( 'ID', 'wpappsdev-pcbuilder' ),
			'savepc-date'    => __( 'Date Added', 'wpappsdev-pcbuilder' ),
			'savepc-actions' => __( 'Actions', 'wpappsdev-pcbuilder' ),
		];

		$customer     = get_current_user_id();
		$current_page = absint( get_query_var( 'savedpcs' ) ) ? absint( get_query_var( 'savedpcs' ) ) : 1;
		$limit        = apply_filters( 'wpadpcbu_config_list_table_limit', 10 );
		$offset       = ( $current_page - 1 ) * $limit;
		$total_items  = wpadpcbu_process()->savedpc->count( $customer );
		$saved_pcs    = wpadpcbu_process()->savedpc->user_pcs( $customer, $offset, $limit );

		$args = [
			'saved_pcs'    => $saved_pcs,
			'has_pcs'      => 0 < wpadpcbu_process()->savedpc->count( $customer ),
			'columns'      => apply_filters( 'wpadpcbu_config_list_table_columns', $columns ),
			'current_page' => absint( $current_page ),
			'max_pages'    => ( 0 == $total_items ) ? 0 : ceil( $total_items / $limit ),
		];

		wpadpcbu_get_template( 'dashboard/savedpc.php', $args );
	}

	public function view_savedpc_content() {
		$columns = [
			'info'  => __( 'Product Info', 'wpappsdev-pcbuilder' ),
			'image' => __( 'Image', 'wpappsdev-pcbuilder' ),
			'price' => __( 'Price', 'wpappsdev-pcbuilder' ),
			'stock' => __( 'Stock', 'wpappsdev-pcbuilder' ),
		];

		$id       = absint( get_query_var( 'view-savedpc' ) ) ? absint( get_query_var( 'view-savedpc' ) ) : 0;
		$saved_pc = wpadpcbu_process()->savedpc->single_pc( $id );

		if ( is_null( $saved_pc ) ) {
			$items = [];
		} else {
			$configurations = json_decode( $saved_pc->configurations, true );
			$items          = $configurations['items'] ?? $configurations;
		}

		$args = [
			'columns' => apply_filters( 'wpadpcbu_view_config_table_columns', $columns ),
			'items'   => $items,
		];

		wpadpcbu_get_template( 'dashboard/view-savedpc.php', $args );
	}
}
