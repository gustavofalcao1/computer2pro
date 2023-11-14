<?php

namespace WPAppsDev\PCBU;

/**
 * Ajax handler for this plugins.
 */
class Ajax {
	/**
	 * Initialize the class.
	 */
	public function __construct() {
		// Generate component filters field for product meta box.
		add_action( 'wp_ajax_generate_component_filters', [ $this, 'generate_component_filters_field' ] );
		add_action( 'wp_ajax_nopriv_generate_component_filters', [ $this, 'generate_component_filters_field' ] );

		// Add component product to pc builder table.
		add_action( 'wp_ajax_add_component_product', [ $this, 'add_component_product_process' ] );
		add_action( 'wp_ajax_nopriv_add_component_product', [ $this, 'add_component_product_process' ] );

		// Remove component product from pc builder table.
		add_action( 'wp_ajax_remove_component_product', [ $this, 'remove_component_product_process' ] );
		add_action( 'wp_ajax_nopriv_remove_component_product', [ $this, 'remove_component_product_process' ] );

		// Components product added to wc cart from pc builder data.
		add_action( 'wp_ajax_add_components_product_to_cart', [ $this, 'add_components_product_to_cart_process' ] );
		add_action( 'wp_ajax_nopriv_add_components_product_to_cart', [ $this, 'add_components_product_to_cart_process' ] );

		// Store pc builder data.
		add_action( 'wp_ajax_pcbuilder_configuration_save', [ $this, 'pcbuilder_configuration_save_process' ] );
		add_action( 'wp_ajax_nopriv_pcbuilder_configuration_save', [ $this, 'pcbuilder_configuration_save_process' ] );

		// Remove saved pc builder data.
		add_action( 'wp_ajax_remove_savedpc', [ $this, 'remove_savedpc_process' ] );
		add_action( 'wp_ajax_nopriv_remove_savedpc', [ $this, 'remove_savedpc_process' ] );

		// Pc builder search page ajax filtering system for component products.
		add_action( 'wp_ajax_filter_component_product', [ $this, 'filter_component_product_process' ] );
		add_action( 'wp_ajax_nopriv_filter_component_product', [ $this, 'filter_component_product_process' ] );
	}

	/**
	 * Generate component filters field for product meta box.
	 *
	 * @return void
	 */
	public function generate_component_filters_field() {
		$post_data = wc_clean( $_POST );
		$nonce     = isset( $post_data['_nonce'] ) ? wc_clean( $post_data['_nonce'] ) : '';

		//Nonce protection.
		if ( ! wp_verify_nonce( $nonce, 'wpadpcbu-admin-security' ) ) {
			wp_send_json_error(
				[
					'type'    => 'nonce',
					'message' => __( 'Are you cheating?', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		/**
		 * Component filters field generating process.
		 */

		// Component id validation.
		$component_id = (int) $post_data['selectedComponent'];

		if ( -1 == $component_id ) {
			wp_send_json_error(
				[
					'type'    => 'error',
					'message' => __( 'Please select valid PC component.', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		// Component filters group id validation.
		$cfgroup_id = (int) get_term_meta( $component_id, 'wpadpcbu_component_filters_group', true );

		if ( -1 == $cfgroup_id ) {
			$link = add_query_arg(
				[
					'taxonomy'  => 'pcbucomp',
					'post_type' => 'product',
					'tag_ID'    => $component_id,
				],
				admin_url( 'term.php' )
			);

			$link_tag = '<a href="' . esc_url( $link ) . '" target="_blank" class="configure-terms">' . __( 'Configure', 'wpappsdev-pcbuilder' ) . '</a></p>';

			wp_send_json_error(
				[
					'type'    => 'nonce',
					'message' => __( 'Please assign component filters group.', 'wpappsdev-pcbuilder' ) . " {$link_tag}",
				]
			);

			wp_die();
		}

		// Validation checking for empty filter items.
		$filter_items = (array) get_post_meta( $cfgroup_id, 'wpadpcbu_component_filter_list', true );

		if ( wpadpcbu_is_repeatable_empty( $filter_items ) ) {
			$link = add_query_arg(
				[
					'post'   => $cfgroup_id,
					'action' => 'edit',
				],
				admin_url( 'post.php' )
			);

			$link_tag = '<a href="' . esc_url( $link ) . '" target="_blank" class="configure-terms">' . __( 'Configure', 'wpappsdev-pcbuilder' ) . '</a></p>';

			wp_send_json_error(
				[
					'type'    => 'nonce',
					'message' => __( 'No filter found for this component. Please add filters for this component.', 'wpappsdev-pcbuilder' ) . " {$link_tag}",
				]
			);

			wp_die();
		}

		// Generate filters input fields html markup.
		ob_start();

		foreach ( $filter_items as $item ) {
			$taxonomy = 'cf-' . $item['filter_slug'];
			$name     = $item['filter_name'];
			$options  = generating_select_options( get_tax_terms_list( $taxonomy ), 'term_id', 'name', '' );

			echo sprintf( '<p class="form-field wpadpcbu_%1$s_field"><label for="wpadpcbu_%1$s"">%2$s</label><select id="wpadpcbu_%1$s" name="pcbu_settings[filters][%1$s]" class="select short"><option value="-1">Select %2$s</option>%3$s</select></p>', esc_attr( $taxonomy ), esc_attr( $name ), wp_kses( $options, wpadpcbu_allowed_html() ) );
		}

		$output = ob_get_contents();
		ob_end_clean();

		wp_send_json_success( $output );
		wp_die();
	}

	/**
	 * Add component product to pc builder table.
	 *
	 * @return void
	 */
	public function add_component_product_process() {
		$post_data = wc_clean( $_POST );

		// Request validations.
		self::validations( $post_data );

		/**
		 * Component product adding process to pc builder table.
		 */
		$total        = 0;
		$component_id = (int) $post_data['componentId'];
		$product_id   = (int) $post_data['productId'];
		$_product     = wc_get_product( $product_id );
		$pcbu_data    = WC()->session->get( 'wpadpcbu_pc_builder_data', [] );
		$item_key     = "CI{$component_id}";
		$pre_items    = isset( $pcbu_data['items'] ) ? $pcbu_data['items'] : [];
		$image_url    = wp_get_attachment_url( get_post_thumbnail_id( $product_id ) );
		$image_html   = sprintf( '<img width="80" height="80" src="%s" class="attachment-80x80 size-80x80" alt="" loading="lazy">', esc_url( $image_url ) );
		//wpadpcbu_print( $pcbu_data );

		// Add new component product to pc builder items data.
		$pre_items[ $item_key ] = [
			'id'     => $product_id,
			'name'   => $_product->get_name(),
			'price'  => $_product->get_price(),
			'fprice' => wc_price( $_product->get_price() ),
			'image'  => $image_html,
		];

		// Calculate price total.
		foreach ( $pre_items as $item ) {
			$total += $item['price'];
		}

		// Set pc builder data.
		$data = [
			'items' => $pre_items,
			'total' => $total,
		];
		WC()->session->set( 'wpadpcbu_pc_builder_data', $data );

		wp_send_json_success();
		wp_die();
	}

	/**
	 * Remove component product from pc builder table.
	 *
	 * @return void
	 */
	public function remove_component_product_process() {
		$post_data = wc_clean( $_POST );

		// Request validations.
		self::validations( $post_data );

		/**
		 * Component product removing process from pc builder table.
		 */
		$total        = 0;
		$pcbu_data    = [];
		$component_id = (int) $post_data['componentId'];
		$item_key     = "CI{$component_id}";
		$pcbu_data    = WC()->session->get( 'wpadpcbu_pc_builder_data', [] );

		if ( isset( $pcbu_data['items'][ $item_key ] ) ) {
			// Remove component product.
			unset( $pcbu_data['items'][ $item_key ] );
			// Calculate total.
			foreach ( $pcbu_data['items'] as $item ) {
				$total += $item['price'];
			}
			$pcbu_data['total'] = $total;
			// Update pc builder data.
			WC()->session->set( 'wpadpcbu_pc_builder_data', $pcbu_data );
		}

		wp_send_json_success( $component_id );
		wp_die();
	}

	/**
	 * Components product added to wc cart from pc builder data.
	 *
	 * @return void
	 */
	public function add_components_product_to_cart_process() {
		$post_data = wc_clean( $_POST );

		// Request validations.
		self::validations( $post_data );

		/**
		 * Components product add to cart process from pc builder data.
		 */
		$pcbu_data = WC()->session->get( 'wpadpcbu_pc_builder_data', [] );

		$items = $pcbu_data['items'] ?? [];

		if ( empty( $items ) ) {
			wp_send_json_error(
				[
					'type'    => 'error',
					'message' => __( 'Please select components product.', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		// Required component validations.
		self::component_validations( $items );

		// Clear cart
		WC()->cart->empty_cart();

		// Product added to cart.
		foreach ( $items as $item ) {
			WC()->cart->add_to_cart( $item['id'], 1 );
		}

		if ( wpadpcbu_process()->builder->redirect_checkout_enable() ) {
			$redirect = true;
		} else {
			$redirect = false;
		}

		wp_send_json_success(
			[
				'url'      => wc_get_checkout_url(),
				'redirect' => $redirect,
			]
		);

		wp_die();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function pcbuilder_configuration_save_process() {
		$post_data = wc_clean( $_POST );

		self::validations( $post_data );

		// Checking user login status.
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			wp_send_json_error(
				[
					'type'    => 'error',
					'message' => __( 'You can not save pc configuration without login.', 'wpappsdev-pcbuilder' ),
				]
			);
			wp_die();
		}

		// Get pc builder data from session.
		$pcbu_data = WC()->session->get( 'wpadpcbu_pc_builder_data', [] );
		$items     = $pcbu_data['items'] ?? [];

		if ( empty( $items ) ) {
			wp_send_json_error(
				[
					'type'    => 'error',
					'message' => __( 'You can not save empty configuration data.', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		// Required component validations.
		self::component_validations( $items );

		$pc_config = [];

		foreach ( $items as $key => $item ) {
			$pc_config[ $key ] = $item['id'];
		}

		$saved_data = [
			'user_id'        => (int) $user_id,
			'configurations' => $pc_config,
		];

		// Pc builder data save process.
		$saved_id = wpadpcbu_process()->savedpc->create( $saved_data );

		// Return error if save process unsuccessful.
		if ( is_wp_error( $saved_id ) ) {
			wp_send_json_error(
				[
					'type'    => 'error',
					'message' => __( 'Something wrong when save pc configuration.', 'wpappsdev-pcbuilder' ),
				]
			);
			wp_die();
		}

		wp_send_json_success(
			[
				'url' => wc_get_account_endpoint_url( 'savedpcs' ),
				'id'  => $saved_id,
			]
		);
		wp_die();
	}

	/**
	 * Remove saved pc configuration from database.
	 *
	 * @return void
	 */
	public function remove_savedpc_process() {
		$post_data = wc_clean( $_POST );
		$nonce     = isset( $post_data['_nonce'] ) ? wc_clean( $post_data['_nonce'] ) : '';

		//Nonce protection
		if ( ! wp_verify_nonce( $nonce, 'wpadpcbu-security' ) ) {
			wp_send_json_error( [
				'type'    => 'nonce',
				'message' => __( 'Invalid nonce.', 'wpappsdev-pcbuilder' ),
			] );

			wp_die();
		}

		// Checking user login status.
		$saved_id = isset( $post_data['pcId'] ) ? abs( $post_data['pcId'] ) : 0;

		if ( ! $saved_id ) {
			wp_send_json_error(
				[
					'type'    => 'error',
					'message' => __( 'Saved pc configuration id mission.', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		// Pc builder data remove process.
		$saved_id = wpadpcbu_process()->savedpc->delete( $saved_id );

		// Return error if save process unsuccessful.
		if ( is_wp_error( $saved_id ) ) {
			wp_send_json_error(
				[
					'type'    => 'error',
					'message' => __( 'Something wrong when remove saved pc configuration.', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		wp_send_json_success();
		wp_die();
	}

	/**
	 * Pc builder search page ajax filtering system for component products.
	 *
	 * @return void
	 */
	public function filter_component_product_process() {
		$tax_filters = [];
		$post_data   = wc_clean( $_POST );
		$nonce       = isset( $post_data['_nonce'] ) ? wc_clean( $post_data['_nonce'] ) : '';

		//Nonce protection
		if ( ! wp_verify_nonce( $nonce, 'wpadpcbu-security' ) ) {
			wp_send_json_error(
				[
					'type'    => 'nonce',
					'message' => __( 'Invalid nonce.', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		// Action checking.
		if ( ! isset( $post_data['action'] ) || 'filter_component_product' != $post_data['action'] ) {
			wp_send_json_error(
				[
					'type'    => 'action',
					'message' => __( 'Invalid action.', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		// Component id checking.
		if ( ! isset( $post_data['pcbucomp'] ) || 0 == $post_data['pcbucomp'] ) {
			wp_send_json_error(
				[
					'type'    => 'pcbucomp',
					'message' => __( 'Invalid component id.', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		// Set Component filter id.
		$tax_filters['pcbucomp'] = $post_data['pcbucomp'];

		$temp_filters = isset( $post_data['taxFilters'] ) ? $post_data['taxFilters'] : [];

		foreach ( $temp_filters as $filter ) {
			$tax_filters[ "cf-{$filter['tax']}" ][] = $filter['val'];
		}

		$search_filter = isset( $post_data['searchFilter'] ) ? wc_clean( $post_data['searchFilter'] ) : '';
		$sortby_filter = isset( $post_data['sortByFilter'] ) ? wc_clean( $post_data['sortByFilter'] ) : 'titleasc';
		$start_price   = isset( $post_data['priceStart'] ) ? wc_clean( $post_data['priceStart'] ) : 0;
		$end_price     = isset( $post_data['priceEnd'] ) ? wc_clean( $post_data['priceEnd'] ) : 80000;

		$args = [
			'tax_args'    => $tax_filters,
			'search_args' => $search_filter,
			'sortby_args' => $sortby_filter,
			'price_range' => [ $start_price, $end_price ],
		];

		if ( isset( $post_data['paged'] ) ) {
			$args['paged'] = $post_data['paged'];
		}

		$p_query  = wpadpcbu_query( $args );
		$products = isset( $p_query['products'] ) ? $p_query['products'] : [];

		if ( empty( $products ) ) {
			$p_markup = '<div class="col-md-12"><p>Products not found.</p></div>';
		} else {
			$args = [
				'pcbucomp' => $post_data['pcbucomp'],
				'products' => $products,
			];

			$p_markup = wpadpcbu_get_template_html( 'shortcodes/pc-builder-product.php', $args );
		}

		unset( $p_query['products'] );
		$p_query['markup'] = $p_markup;

		wp_send_json_success( $p_query );
		wp_die();
	}

	/**
	 * Ajax request validations.
	 *
	 * @param array $post_data
	 *
	 * @return void
	 */
	public static function validations( $post_data ) {
		$nonce = isset( $post_data['_nonce'] ) ? wc_clean( $post_data['_nonce'] ) : '';

		//Nonce protection.
		if ( ! wp_verify_nonce( $nonce, 'wpadpcbu-security' ) ) {
			wp_send_json_error(
				[
					'type'    => 'nonce',
					'message' => __( 'Are you cheating?', 'wpappsdev-pcbuilder' ),
				]
			);

			wp_die();
		}

		// WC session validations.
		if ( ! is_callable( [ WC()->session, 'get' ] ) ) {
			wp_send_json_error( [
				'type'    => 'error',
				'message' => __( 'WC session get function is not callable', 'wpappsdev-pcbuilder' ),
			] );

			wp_die();
		}

		do_action( '$hook_name:string', $post_data );
	}

	public static function component_validations( $items ) {
		$missing    = [];
		$components = get_required_component_list();

		foreach ( $components as $cid ) {
			if ( ! isset( $items[ "CI{$cid}" ] ) ) {
				$missing[] = $cid;
			}
		}

		if ( count( $missing ) ) {
			wp_send_json_error(
				[
					'type'    => 'missing-component',
					'missing' => $missing,
					'message' => __( 'Please select required components.', 'wpappsdev-pcbuilder' ),
				]
			);
			wp_die();
		}
	}
}
