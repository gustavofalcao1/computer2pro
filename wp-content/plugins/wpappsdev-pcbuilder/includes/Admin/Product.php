<?php

namespace WPAppsDev\PCBU\Admin;

use WPAppsDev\WpadMetaBox;

/**
 * The Product handler class.
 */
class Product {
	public $features;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_product_pcbuilder_metabox' ] );
		add_action( 'save_post', [ $this, 'save_pcbuilder_settings' ] );
		$this->features = new WpadMetaBox( 'product-features', __( 'Product Features', 'wpappsdev-pcbuilder' ), self::get_features_fields(), 'product', 'side' );
	}

	/**
	 * Add PC builder settings custom metabox.
	 *
	 * @return void
	 */
	public function add_product_pcbuilder_metabox() {
		add_meta_box(
			'pcbuilder-settings',
			__( 'PC Builder Configuration', 'wpappsdev-pcbuilder' ),
			[ $this, 'display_pcbulider_settings_fields' ],
			'product',
			'side',
			'high'
		);
	}

	/**
	 * Display PC builder settings fields.
	 *
	 * @return void
	 */
	public function display_pcbulider_settings_fields() {
		global $post;
		$pcbu_settings = get_post_meta( $post->ID, 'wpadpcbu_pcbuilder_settings', true );
		$component_id  = isset( $pcbu_settings['pcbucomp'] ) ? wc_clean( $pcbu_settings['pcbucomp'] ) : 0;
		$cfgroup_id    = get_term_meta( $component_id, 'wpadpcbu_component_filters_group', true );

		$args = [
			'pcbu_settings' => get_post_meta( $post->ID, 'wpadpcbu_pcbuilder_settings', true ),
			'filters'       => isset( $pcbu_settings['filters'] ) ? $pcbu_settings['filters'] : [],
			'filter_items'  => get_post_meta( $cfgroup_id, 'wpadpcbu_component_filter_list', true ),
		];

		wpadpcbu_get_template( 'admin/product-metabox.php', $args );
	}

	/**
	 * Save PC builder settings.
	 *
	 * @return void
	 */
	public function save_pcbuilder_settings() {
		$post_data = wc_clean( $_POST );

		if ( ! isset( $post_data['save_pcbu_settings'] ) ) {
			return;
		}

		$post_id       = (int) $post_data['post_ID'];
		$pre_settings  = get_post_meta( $post_id, 'wpadpcbu_pcbuilder_settings', true );
		$pcbu_settings = $post_data['pcbu_settings'];
		$component     = isset( $pcbu_settings['pcbucomp'] ) ? (array) $pcbu_settings['pcbucomp'] : [];
		$filters       = isset( $pcbu_settings['filters'] ) ? $pcbu_settings['filters'] : [];

		if ( ! empty( $component ) ) {
			wp_set_post_terms( $post_id, $component, 'pcbucomp' );
		}

		if ( ! empty( $filters ) ) {
			foreach ( $filters as $tax_slug => $tax_value ) {
				wp_set_post_terms( $post_id, (array) $tax_value, $tax_slug );
			}
		}

		update_post_meta( $post_id, 'wpadpcbu_pcbuilder_settings', $pcbu_settings );
	}

	/**
	 * @return mixed
	 */
	public static function get_features_fields() {
		$meta_data = [
			[
				'label'     => '',
				'desc'      => __( 'Add component product features lists.', 'wpappsdev-pcbuilder' ),
				'id'        => 'wpadpcbu_product_features_list',
				'type'      => 'repeatable',
				'sanitizer' => [
					//'feature' => 'sanitize_title',
				],
				'repeatable_fields' => [
					'feature' => [
						'label' => '',
						'id'    => 'feature',
						'type'  => 'text',
					],
				],
			],
		];

		$meta_data = apply_filters( 'wpadpcbu_product_features_meta_fields', $meta_data );

		return $meta_data;
	}
}
