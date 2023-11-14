<?php

namespace WPAppsDev\PCBU;

use WPAppsDev\WpadTaxonomy;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Component class.
 */
class Component {
	/**
	 * User submitted args assigned on __construct().
	 *
	 * @var array holds the user submitted post type argument
	 */
	public $component;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$args = [
			'show_in_nav_menus'  => false,
			'meta_box_cb'        => false,
			'publicly_queryable' => false,
			'show_in_quick_edit' => false,
		];

		$this->component = new WpadTaxonomy( 'pcbucomp', 'product', __( 'Computer Component', 'wpappsdev-pcbuilder' ), __( 'Computer Components', 'wpappsdev-pcbuilder' ), $args );

		// Add custom fields into taxonomy add form.
		add_action( 'pcbucomp_add_form_fields', [ $this, 'add_form_custom_fields' ] );
		// Add custom fields into taxonomy edit form.
		add_action( 'pcbucomp_edit_form_fields', [ $this, 'edit_form_custom_fields' ] );
		// Save taxonomy custom fields data.
		add_action( 'edited_pcbucomp', [ $this, 'save_custom_fields_data' ] );
		add_action( 'create_pcbucomp', [ $this, 'save_custom_fields_data' ] );
		// Add custom columns into taxonomy admin table.
		add_filter( 'manage_edit-pcbucomp_columns', [ $this, 'add_custom_columns' ] );
		add_filter( 'manage_edit-pcbucomp_sortable_columns', [ $this, 'add_custom_columns' ] );
		// Display taxonomy admin table custom columns data.
		add_action( 'manage_pcbucomp_custom_column', [ $this, 'display_custom_columns_data' ], 10, 3 );
		// Add terms order by arguments.
		add_filter( 'get_terms_args', [ $this, 'add_terms_query_args' ], 100, 2 );
		add_filter( 'get_terms_orderby', [ $this, 'change_term_order'], 10, 3 );
	}

	/**
	 * Add custom fields into taxonomy add form.
	 *
	 * @return void
	 */
	public function add_form_custom_fields() {
		wpadpcbu_get_template( 'admin/category-add-form.php' );
	}

	/**
	 * Add custom fields into taxonomy edit form.
	 *
	 * @param object $term
	 *
	 * @return void
	 */
	public function edit_form_custom_fields( $term ) {
		//getting term ID
		$term_id = $term->term_id;

		$args = [
			'filters_group' => get_term_meta( $term_id, 'wpadpcbu_component_filters_group', true ),
			'image_id'      => get_term_meta( $term_id, 'component-image-id', true ),
			'serial'        => get_term_meta( $term_id, 'wpadpcbu_component_serial', true ),
			'required'      => strtolower( get_term_meta( $term_id, 'wpadpcbu_required_component', true ) ),
		];

		wpadpcbu_get_template( 'admin/category-edit-form.php', $args );
	}

	/**
	 * Save taxonomy custom fields data.
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function save_custom_fields_data( $term_id ) {
		$postdata = wc_clean( $_POST );

		if ( isset( $postdata['wpadpcbu_component_serial'] ) ) {
			$serial = (int) $postdata['wpadpcbu_component_serial'];
			update_term_meta( $term_id, 'wpadpcbu_component_serial', $serial );
		}

		if ( isset( $postdata['wpadpcbu_component_filters_group'] ) ) {
			$cf_group = (int) $postdata['wpadpcbu_component_filters_group'];
			update_term_meta( $term_id, 'wpadpcbu_component_filters_group', $cf_group );
		}

		if ( isset( $postdata['component-image-id'] ) && '' !== $postdata['component-image-id'] ) {
			$image = (int) $postdata['component-image-id'];
			update_term_meta( $term_id, 'component-image-id', $image );
		}

		if ( isset( $postdata['wpadpcbu_required_component'] ) ) {
			$required = $postdata['wpadpcbu_required_component'];
			update_term_meta( $term_id, 'wpadpcbu_required_component', $required );
		}
	}

	/**
	 * Add custom columns into taxonomy admin table.
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_custom_columns( $columns ) {
		$final_columns = [];
		unset( $columns['description'] );
		unset( $columns['slug'] );
		$columns['name']           = __( 'Component Name', 'wpappsdev-pcbuilder' );
		$final_columns['cfgroups'] = __( 'Filters Group', 'wpappsdev-pcbuilder' );
		$final_columns['icon']     = __( 'Icon', 'wpappsdev-pcbuilder' );
		$final_columns['required'] = __( 'Required', 'wpappsdev-pcbuilder' );
		$final_columns['serial']   = __( 'Serial', 'wpappsdev-pcbuilder' );

		return wpadpcbu_array_insert_after( $columns, 'name', $final_columns );
	}

	/**
	 * Display taxonomy admin table custom columns data.
	 *
	 * @param string $content
	 * @param string $column_name
	 * @param int    $term_id
	 *
	 * @return string
	 */
	public function display_custom_columns_data( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'cfgroups':
				$cf_group_id = get_term_meta( $term_id, 'wpadpcbu_component_filters_group', true );
				$title       = get_the_title( $cf_group_id );
				$link        = add_query_arg(
					[
						'post'   => $cf_group_id,
						'action' => 'edit',
					],
					admin_url( 'post.php' )
				);

				$content = sprintf( '<strong><a target="_blank" class="row-title" href="%s">%s</a></strong>', esc_url( $link ), esc_attr( $title ) );
				break;

			case 'icon':
				$image_id = get_term_meta( $term_id, 'component-image-id', true );
				$content  = wp_get_attachment_image( $image_id, [25, 25] );
				break;

			case 'serial':
				echo (int) get_term_meta( $term_id, 'wpadpcbu_component_serial', true );
				break;

			case 'required':
				echo ucwords( get_term_meta( $term_id, 'wpadpcbu_required_component', true ) );
				break;

			default:
				// code...
				break;
		}

		return $content;
	}

	/**
	 * Add sortable columns query var.
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function add_terms_query_args( $args, $taxonomies ) {
		if (  ! is_admin() || empty( $taxonomies ) || 'pcbucomp' !== $taxonomies[0] ) {
			return $args;
		}

		$filter_args = [
			'orderby'    => 'meta_value_num',
			'order'      => 'ASC',
			'meta_query' => [
				[
					'key'     => 'wpadpcbu_component_serial',
					'value'   => 0,
					'compare' => '>=',
					'type'    => 'NUMERIC',
				],
			],
		];

		$args = array_merge( $args, $filter_args );

		return $args;
	}

	/**
	 * Modify the admin listed order of terms
	 *
	 * @param string $orderby
	 * @param array  $args
	 * @param array  $taxonomies
	 *
	 * @return string $orderby
	 */
	public function change_term_order( $orderby, $args, $taxonomies ) {
		if ( ! is_admin() ) {
			return $orderby;
		}

		if ( 'pcbucomp' == $taxonomies[0] ) {
			global $wpdb;
			$orderby = $wpdb->prefix . 'termmeta.meta_value+0';
		}

		return $orderby;
	}
}
