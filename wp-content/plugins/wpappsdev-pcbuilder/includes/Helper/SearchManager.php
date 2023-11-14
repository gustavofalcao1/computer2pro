<?php

namespace WPAppsDev\PCBU\Helper;

/**
 * The PC builder search manager helper class.
 */
class SearchManager {
	/**
	 * Initialize the class.
	 */
	public function __construct() {
	}

	/**
	 * Search page breadcrumb enable/disable.
	 *
	 * @return bool
	 */
	public function breadcrumb_enable() {
		$option = wpadpcbu_get_option( 'wpadpcbu_search_breadcrumb_disable', 'search_page_settings', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}

	/**
	 * Search page breadcrumb title.
	 *
	 * @return bool
	 */
	public function search_title( $component_name ) {
		$title = wpadpcbu_get_option( 'wpadpcbu_search_breadcrumb_title', 'search_page_settings', __( 'PC Builder - Find Your {component_name} Component', 'wpappsdev-pcbuilder' ) );
		$title = str_replace( '{component_name}', $component_name, $title );

		return $title;
	}

	/**
	 * Search page top filter enable/disable.
	 *
	 * @return bool
	 */
	public function top_filter_enable() {
		$option = wpadpcbu_get_option( 'wpadpcbu_search_top_filter_disable', 'search_page_settings', 'off' );

		if ( 'on' === $option ) {
			return false;
		}

		return true;
	}

	/**
	 * Search page per_page value.
	 *
	 * @return bool
	 */
	public function per_page() {
		return wpadpcbu_get_option( 'wpadpcbu_search_per_page', 'search_page_settings', 10 );
	}

	/**
	 * Search page price start range value.
	 *
	 * @return bool
	 */
	public function start_range() {
		return wpadpcbu_get_option( 'wpadpcbu_search_price_range_start', 'search_page_settings', 0 );
	}

	/**
	 * Search page price end range value.
	 *
	 * @return bool
	 */
	public function end_range() {
		return wpadpcbu_get_option( 'wpadpcbu_search_price_range_end', 'search_page_settings', 80000 );
	}
}
