<?php

namespace WPAppsDev\PCBU;

use WPAppsDev\WpadTaxonomy;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DynamicTaxonomy class.
 */
class DynamicTaxonomy {
	/**
	 * Hold component filters taxonomy class object.
	 *
	 * @var array
	 */
	public $component_filters;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$this->create_dynamic_taxonomy();
	}

	public function create_dynamic_taxonomy() {
		$taxonomies = [];
		$cf_groups  = get_component_filter( true );

		foreach ( $cf_groups as $group_id ) {
			$cf_filters = get_post_meta( $group_id, 'wpadpcbu_component_filter_list', true );

			if ( is_array( $cf_filters ) && ! wpadpcbu_is_repeatable_empty( $cf_filters ) ) {
				foreach ( $cf_filters as $filter ) {
					$taxonomies[] = $filter;
				}
			}
		}

		foreach ( $taxonomies as $taxonomy ) {
			$args = [
				'show_in_menu'       => false,
				'meta_box_cb'        => false,
				'show_admin_column'  => false,
				'show_in_quick_edit' => false,
				'hierarchical'       => true,
				'has_archive'        => false,
			];

			$this->component_filters[] = new WpadTaxonomy( 'cf-' . $taxonomy['filter_slug'], 'product', $taxonomy['filter_name'], $taxonomy['filter_name'], $args );
		}
	}
}
