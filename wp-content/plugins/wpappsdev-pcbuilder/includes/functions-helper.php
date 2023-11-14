<?php
/**
 * Helper functions for this plugins.
 *
 * @since 1.0.0
 */

// File Security Check
defined( 'ABSPATH' ) || exit;

/**
 * Print debug Data.
 *
 * @param array/string $data
 *
 * @return void
 */
function wpadpcbu_print( $data ) {
	if ( ! WP_DEBUG ) {
		return;
	}

	echo '<pre>';

	if ( is_array( $data ) || is_object( $data ) ) {
		print_r( $data );
	} else {
		echo wc_clean( $data );
	}
	echo '</pre>';
}

/**
 * Get allowed html for wp_kses.
 *
 * @return array
 */
function wpadpcbu_allowed_html() {
	$allowed_html = [
		'img' => [
			'width'   => [],
			'height'  => [],
			'src'     => [],
			'class'   => [],
			'srcset'  => [],
			'sizes'   => [],
			'loading' => [],
		],
		'option' => [
			'value'    => [],
			'selected' => [],
		],
	];

	return $allowed_html;
}

/**
 * Get settings option value.
 *
 * @param string $option
 * @param string $section
 * @param string $default
 *
 * @return mixed
 */
function wpadpcbu_get_option( $option, $section, $default = '' ) {
	$options = get_option( $section );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}

/**
 * Get post meta value.
 *
 * @param $pid
 * @param $pkey
 * @param $default
 *
 * @return mixed
 */
function wpadpcbu_meta( $pid, $pkey, $default = '' ) {
	$value = get_post_meta( $pid, $pkey, true );

	if ( isset( $value ) && ! empty( $value ) ) {
		$option = stripslashes_deep( $value );
	} else {
		$option = $default;
	}

	return $option;
}

/**
 * Get other templates passing attributes and including the file.
 *
 * Search for the template and include the file.
 *
 * @see wpadpcbu_locate_template()
 *
 * @param string $template_name template to load
 * @param array  $args          args (optional) Passed arguments for the template file
 * @param string $template_path (optional) Path to templates
 * @param string $default_path  (optional) Default path to template files
 */
function wpadpcbu_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
	$cache_key = sanitize_key( implode( '-', [ 'template', $template_name, $template_path, $default_path, WPADPCBU_VERSION ] ) );
	$template  = (string) wp_cache_get( $cache_key, WPADPCBU_NAME );

	if ( ! $template ) {
		$template = wpadpcbu_locate_template( $template_name, $template_path, $default_path );
		wp_cache_set( $cache_key, $template, WPADPCBU_NAME );
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'wpadpcbu_get_template', $template, $template_name, $args, $template_path, $default_path );

	if ( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			/* translators: %s template */
			wpadpcbu_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'wpappsdev-pcbuilder' ), '<code>' . $template . '</code>' ), '1.0.0' );

			return;
		}
		$template = $filter_template;
	}

	$action_args = [
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	];

	if ( ! empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			wpadpcbu_doing_it_wrong(
				__FUNCTION__,
				__( 'action_args should not be overwritten when calling wpadpcbu_get_template.', 'wpappsdev-pcbuilder' ),
				'1.0.0'
			);
			unset( $args['action_args'] );
		}
		extract( $args ); // @codingStandardsIgnoreLine
	}

	do_action( 'wpadpcbu_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	if ( file_exists( $action_args['located'] ) ) {
		include $action_args['located'];
	}

	do_action( 'wpadpcbu_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}

/**
 * Like wpadpcbu_get_template, but returns the HTML instead of outputting.
 *
 * @see wpadpcbu_get_template
 *
 * @param string $template_name template name
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string
 */
function wpadpcbu_get_template_html( $template_name, $args = [], $template_path = '', $default_path = '' ) {
	ob_start();
	wpadpcbu_get_template( $template_name, $args, $template_path, $default_path );

	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * Locate the called template.
 * Search Order:
 * 1. /themes/theme-name/plugins-name/$template_name
 * 2. /plugins/plugins-name/partials/templates/$template_name.
 *
 * @author Saiful Islam
 *
 * @param string $template_name template to load
 * @param string $template_path (optional) Path to templates
 * @param string $default_path  (optional) Default path to template files
 *
 * @return string $template path to the template file
 */
function wpadpcbu_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	// Set variable to search in templates folder of theme.
	if ( ! $template_path ) {
		$template_path = get_template_directory() . '/' . WPADPCBU_NAME . '/';
	}
	// Set default plugin templates path.
	if ( ! $default_path ) {
		$default_path = WPADPCBU_DIR . 'templates/';
	}
	// Search template file in theme folder.
	$template = locate_template( [ $template_path . $template_name, $template_name ] );
	// Get plugins template file.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	return apply_filters( 'wpadpcbu_locate_template', $template, $template_name, $template_path, $default_path );
}

/**
 * Wrapper for wpadpcbu_doing_it_wrong.
 *
 * @author Saiful Islam
 *
 * @param string $function function used
 * @param string $message  message to log
 * @param string $version  version the message was added in
 */
function wpadpcbu_doing_it_wrong( $function, $message, $version ) {
	// @codingStandardsIgnoreStart
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( is_ajax() ) {
		do_action( 'wpadpcbu_doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}
	// @codingStandardsIgnoreEnd
}

/**
 * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
 * to the end of the array.
 *
 * @param string $key
 *
 * @return array
 */
function wpadpcbu_array_insert_after( array $array, $key, array $new ) {
	$keys  = array_keys( $array );
	$index = array_search( $key, $keys );
	$pos   = false === $index ? count( $array ) : $index + 1;

	return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
}

/**
 * Check repeatable field empty or not.
 *
 * @param mixed $data
 *
 * @return bool
 */
function wpadpcbu_is_repeatable_empty( $data ) {
	if ( ! is_array( $data ) || 0 == count( $data ) ) {
		return true;
	}

	if ( 1 == count( $data ) ) {
		foreach ( $data[0] as $key => $value ) {
			if ( '' != trim( $value ) ) {
				return false;
			}
		}

		return true;
	}

	return false;
}

/**
 * Product query function.
 *
 * @param array $args
 */
function wpadpcbu_query( $args = [] ) {
	$tax_args       = [];
	$final_products = [];

	$default = [
		'post_type'      => [ 'product' ],
		'post_status'    => [ 'publish' ],
		'order'          => 'ASC',
		'orderby'        => 'title',
		'posts_per_page' => -1,
		'paged'          => 1,
	];

	if ( isset( $args['type'] ) ) {
		$default['post_type'] = $args['type'];
	}

	if ( isset( $args['status'] ) ) {
		$default['post_status'] = $args['status'];
	}

	if ( isset( $args['search_args'] ) ) {
		$default['s'] = esc_attr( $args['search_args'] );
	}

	if ( isset( $args['price_range'] ) ) {
		$default['meta_query'] = [
			[
				'key'     => '_regular_price',
				'value'   => $args['price_range'],
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			],
		];
	}

	if ( isset( $args['sortby_args'] ) ) {
		if ( 'titledesc' == $args['sortby_args'] ) {
			$default['order'] = 'DESC';
		}

		if ( 'pricedesc' == $args['sortby_args'] || 'priceasc' == $args['sortby_args'] ) {
			$default['orderby']  = 'meta_value_num';
			$default['meta_key'] = '_regular_price';

			if ( 'priceasc' == $args['sortby_args'] ) {
				$default['order'] = 'ASC';
			} else {
				$default['order'] = 'DESC';
			}
		}
	}

	if ( isset( $args['tax_args'] ) && is_array( $args['tax_args'] ) && ! empty( $args['tax_args'] ) ) {
		foreach ( $args['tax_args'] as $key => $value ) {
			$tax_args[] = [
				'taxonomy' => $key,
				'field'    => 'term_id',
				'terms'    => $value,
			];
		}
	}

	if ( count( $tax_args ) > 0 ) {
		$default['tax_query'] = $tax_args;

		if ( count( $tax_args ) > 1 ) {
			$default['tax_query']['relation'] = 'AND';
		}
	}

	$q_products   = new WP_Query( $default );
	$products     = $q_products->posts;
	$total_items  = count( $products );
	$per_page     = wpadpcbu_process()->search->per_page();
	$current_page = isset( $args['paged'] ) ? $args['paged'] : 1;
	$offset       = ( $current_page - 1 ) * $per_page;
	$items        = array_slice( $products, $offset, $per_page );

	foreach ( $items as $item ) {
		$final_products[] = wc_get_product( $item->ID );
	}

	$data = [
		'total_items'  => $total_items,
		'total_pages'  => ( 0 === $total_items ) ? 0 : ceil( $total_items / $per_page ),
		'per_page'     => $per_page,
		'products'     => $final_products,
		'current_page' => $current_page,
	];

	return $data;
}

/**
 * Generic function for getting taxonomy terms list.
 *
 * @param string $taxonomy
 * @param array  $args
 *
 * @return array
 */
function get_tax_terms_list( $taxonomy, $args = [] ) {
	$terms_list = [];

	$tax_args = [
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
		'orderby'    => 'term_id',
	];

	$tax_args = array_merge( $tax_args, $args );

	$tax_terms = get_terms( $tax_args );

	if ( is_array( $tax_terms ) ) {
		foreach ( $tax_terms as $term ) {
			$terms_list[] = [
				'term_id' => (int) $term->term_id,
				'slug'    => wc_clean( $term->slug ),
				'name'    => wc_clean( $term->name ),
			];
		}
	}

	return $terms_list;
}

/**
 * Generic function for generating select dropdown options.
 *
 * @param string $taxonomy
 *
 * @return array
 */
function generating_select_options( $data, $value_key, $title_key, $selected_val = '' ) {
	$select_options = '';

	if ( ! is_array( $data ) || empty( $data ) ) {
		return $select_options;
	}

	foreach ( $data as $item ) {
		$item     = (object) $item;
		$selected = '';

		if ( $item->$value_key == $selected_val ) {
			$selected = 'selected="selected"';
		}

		$select_options .= sprintf( '<option value="%1$s" %3$s>%2$s</option>', wc_clean( $item->$value_key ), ucfirst( wc_clean( $item->$title_key ) ), $selected );
	}

	return $select_options;
}

/**
 * Get builder page link or id.
 *
 * @param bool $return_id
 *
 * @return void
 */
function get_builder_page( $return_id = false ) {
	$page_id = wpadpcbu_get_option( 'wpadpcbu_builder_page', 'page_settings' );

	if ( $return_id ) {
		return $page_id;
	}

	return wpadpcbu_page_link( $page_id );
}

/**
 * Get builder search page link or id.
 *
 * @return void
 */
function get_search_page( $return_id = false ) {
	$page_id = wpadpcbu_get_option( 'wpadpcbu_builder_search_page', 'page_settings' );

	if ( $return_id ) {
		return (int) $page_id;
	}

	return wpadpcbu_page_link( $page_id );
}

/**
 * Get page link.
 *
 * @param int $page_id
 *
 * @return string
 */
function wpadpcbu_page_link( $page_id ) {
	if ( is_numeric( $page_id ) ) {
		return esc_url_raw( get_permalink( $page_id ) );
	} else {
		return '#';
	}
}

/**
 * Getting component data list.
 *
 * @return array
 */
function get_component_data( $item_key = '' ) {
	$data = [];
	$args = [
		'orderby'    => 'meta_value_num',
		'order'      => 'ASC',
		'meta_query' => [
			[
				'key'     => 'wpadpcbu_component_serial',
				'type'    => 'NUMERIC',
				'compare' => '>',
				'value'   => 0,
			],
		],
	];

	$components = get_tax_terms_list( 'pcbucomp', $args );

	foreach ( $components as $component ) {
		$cid      = (int) $component['term_id'];
		$key      = "CI{$cid}";
		$image_id = (int) get_term_meta( $cid, 'component-image-id', true );
		$required = strtolower( get_term_meta( $cid, 'wpadpcbu_required_component', true ) );

		$data[ $key ] = [
			'term_id'  => $cid,
			'name'     => wc_clean( $component['name'] ),
			'slug'     => wc_clean( $component['slug'] ),
			'required' => ( 'yes' === $required ) ? true : false,
		];

		if ( ! empty( $image_id ) ) {
			$image_url = wp_get_attachment_url( $image_id );

			$data[ $key ]['image_id'] = $image_id;
			$data[ $key ]['image']    = sprintf( '<img src="%s" class="component-icon" alt="" loading="lazy">', $image_url );
			// $data[ $key ]['image']    = wp_get_attachment_image( $image_id, [ 25, 25 ] );
		}
	}

	if ( '' != $item_key ) {
		return isset( $data[ $item_key ] ) ? wc_clean( $data[ $item_key ] ) : [];
	}

	return $data;
}

/**
 * Getting component filters data list.
 *
 * @return array
 */
function get_component_filters( $component_id ) {
	$filters = [];

	$cfgroup_id = (int) get_term_meta( $component_id, 'wpadpcbu_component_filters_group', true );

	if ( ( -1 != $cfgroup_id ) && ( '' != $cfgroup_id ) ) {
		$filters_list = get_post_meta( $cfgroup_id, 'wpadpcbu_component_filter_list', true );

		if ( ! wpadpcbu_is_repeatable_empty( $filters_list ) ) {
			foreach ( $filters_list as $filter ) {
				$filters[ $filter['filter_slug'] ]['name']  = wc_clean( $filter['filter_name'] );
				$filters[ $filter['filter_slug'] ]['items'] = get_tax_terms_list( "cf-{$filter['filter_slug']}" );
			}
		}
	}

	return $filters;
}

/**
 * Getting required component data list.
 *
 * @return array
 */
function get_required_component_list() {
	$component_list = [];

	$args = [
		'orderby'    => 'meta_value_num',
		'order'      => 'ASC',
		'hide_empty' => false,
		'meta_query' => [
			[
				'key'     => 'wpadpcbu_component_serial',
				'type'    => 'NUMERIC',
				'compare' => '>',
				'value'   => 0,
			],
			[
				'key' => 'wpadpcbu_required_component',
				'compare' => '=',
				'value'   => 'yes',
			],
		],
	];

	$components = get_terms( $args );

	foreach ( $components as $component ) {
		$component_list[] = (int) $component->term_id;
	}

	return $component_list;
}

/**
 * Getting component filters group list.
 *
 * @param bool $return_id
 *
 * @return array
 */
function get_component_filter( $return_id = false ) {
	$args = [
		'posts_per_page' => -1,
		'post_type'      => 'pcbucf',
		'post_status'    => 'publish',
	];

	$cf_groups = get_posts( $args );

	if ( $return_id ) {
		$cf_ids = [];

		foreach ( $cf_groups as $gorup ) {
			$cf_ids[] = (int) $gorup->ID;
		}

		return $cf_ids;
	}

	return $cf_groups;
}

/**
 * Get settings pages id.
 *
 * @return array
 */
function get_settings_pages_id() {
	$pages_id = [];
	$builder  = get_builder_page( true );
	$search   = get_search_page( true );

	if ( is_numeric( $builder ) ) {
		$pages_id[] = (int) $builder;
	}

	if ( is_numeric( $search ) ) {
		$pages_id[] = (int) $search;
	}

	return $pages_id;
}
