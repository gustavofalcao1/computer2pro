<?php

namespace WPAppsDev\PCBU;

use WPAppsDev\WpadCpt;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FiltersGroup class.
 */
class FiltersGroup {
	/**
	 * Hold custom post class object.
	 *
	 * @var object
	 */
	public $com_filters;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		$args = [
			'menu_icon'          => 'dashicons-admin-generic',
			'show_in_menu'       => 'wpadpcbu-pcbuilder',
			'public'             => false,
			'show_in_admin_bar'  => false,
			'has_archive'        => false,
			'publicly_queryable' => false,
		];

		// Register custom post type.
		$this->com_filters = new WpadCpt( 'pcbucf', __( 'Filter Group', 'wpappsdev-pcbuilder' ), __( 'Filter Groups', 'wpappsdev-pcbuilder' ), $args );
		// Filters list meta box.
		$this->com_filters->add_metabox( 'filters_list', __( 'Filters List', 'wpappsdev-pcbuilder' ), self::get_filters_list_metabox_fields() );
		// Filters configuration meta box.
		add_action( 'add_meta_boxes', [ $this, 'add_filter_groups_metaboxes' ] );
		// Add custom columns into custom post admin table.
		add_filter( 'manage_edit-pcbucf_columns', [ $this, 'columns_title' ] );
		// Display custom post admin table custom columns data.
		add_action( 'manage_pcbucf_posts_custom_column', [ $this, 'columns_data' ], 10, 2 );
	}

	/**
	 * @return mixed
	 */
	public static function get_filters_list_metabox_fields() {
		$meta_data = [
			[
				'label'     => __( 'Filters List', 'wpappsdev-pcbuilder' ),
				'desc'      => __( 'Add filters list. Filter slug must be unique. If you wants to use same filter into different filters groups, you can keep it same.', 'wpappsdev-pcbuilder' ),
				'id'        => 'wpadpcbu_component_filter_list',
				'type'      => 'repeatable',
				'sanitizer' => [
					//'filter_name' => 'sanitize_title',
				],
				'repeatable_fields' => [
					'filter_name' => [
						'label' => __( 'Filter Name', 'wpappsdev-pcbuilder' ),
						'id'    => 'filter_name',
						'type'  => 'text',
					],
					'filter_slug' => [
						'label' => __( 'Filter Slug', 'wpappsdev-pcbuilder' ),
						'id'    => 'filter_slug',
						'type'  => 'text',
					],
				],
			],
		];

		$meta_data = apply_filters( 'wpadpcbu_filters_list_meta_fields', $meta_data );

		return $meta_data;
	}

	/**
	 * Add filter groups meta boxes.
	 *
	 * @return void
	 */
	public function add_filter_groups_metaboxes() {
		add_meta_box(
			'display-filters-config',
			__( 'Filters Configuration', 'wpappsdev-pcbuilder' ),
			[ $this, 'display_component_filter_metabox' ],
			'pcbucf',
			'side'
		);
	}

	/**
	 * Display filters configuration information.
	 *
	 * @return void
	 */
	public function display_component_filter_metabox() {
		global $post;
		self::filter_configuration_list( $post->ID );
	}

	/**
	 * Add custom columns into custom post admin table.
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function columns_title( $columns ) {
		unset( $columns['date'] );

		$columns['title']      = __( 'Filters Group', 'wpappsdev-pcbuilder' );
		$columns['filterinfo'] = __( 'Filters Information', 'wpappsdev-pcbuilder' );

		return $columns;
	}

	/**
	 * Display custom post admin table custom columns data.
	 *
	 * @param string $column_name
	 * @param int    $post_ID
	 *
	 * @return void
	 */
	public function columns_data( $column_name, $post_ID ) {
		switch ( $column_name ) {
			case 'filterinfo':
				self::filter_configuration_list( $post_ID );
				break;
			default:
				// code...
			break;
		}
	}

	/**
	 * Filters configuration link list html markup generator.
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public static function filter_configuration_list( $post_id ) {
		$cf_items = get_post_meta( $post_id, 'wpadpcbu_component_filter_list', true );

		echo '<div class="filter-list-div">';
		if ( is_array( $cf_items ) && ! wpadpcbu_is_repeatable_empty( $cf_items ) ) {
			echo '<a href="#" class="button filter-display">Show Filters</a>';
			echo '<div class="filter-list wpadpcbu-hide" style="margin-top:10px;">';
			foreach ( $cf_items as $cf_item ) {
				$link = add_query_arg(
					[
						'taxonomy'  => 'cf-' . esc_attr( $cf_item['filter_slug'] ),
						'post_type' => 'product',
					],
					admin_url( 'edit-tags.php' )
				);

				echo '<p><span style="font-weight:600;font-size:15px;">' . esc_attr( ucwords( $cf_item['filter_name'] ) ) . '</span> <br/> <a href="' . esc_url( $link ) . '" target="_blank" class="configure-terms">' . __( 'Configure terms', 'wpappsdev-pcbuilder' ) . '</a></p>';
			}
			echo '</div>';
		} else {
			_e( 'Empty filters list. Please first add & save filters list.', 'wpappsdev-pcbuilder' );
		}
		echo '</div>';

	}
}
