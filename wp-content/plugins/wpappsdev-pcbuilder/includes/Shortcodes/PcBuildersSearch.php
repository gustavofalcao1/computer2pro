<?php

namespace WPAppsDev\PCBU\Shortcodes;

use WPAppsDev\PCBU\Abstracts\WPAppsDevShortcode;

class PcBuildersSearch extends WPAppsDevShortcode {
	protected $shortcode = 'wpadpcbu-pcbuilder-search';

	/**
	 * Load template files.
	 *
	 * @param array $atts
	 *
	 * @return void
	 */
	public function render_shortcode( $atts ) {
		$this->enqueue_scripts();

		$pcbucomp   = isset( $_REQUEST['pcbucomp'] ) ? abs( $_REQUEST['pcbucomp'] ) : 0;
		$components = get_component_data();
		$datakey    = "CI{$pcbucomp}";
		$current    = isset( $components[ $datakey ] ) ? $components[ $datakey ] : [];

		$product_args = [
			'tax_args' => [
				'pcbucomp' => $pcbucomp,
			],
		];

		$query = wpadpcbu_query( $product_args );

		$args = [
			'components' => $components,
			'comid'      => $pcbucomp,
			'pcbucomp'   => $pcbucomp,
			'datakey'    => $datakey,
			'component'  => $current,
			'query'      => $query,
			'products'   => isset( $query['products'] ) ? $query['products'] : [],
			'filters'    => get_component_filters( $pcbucomp ),
		];

		return wpadpcbu_get_template_html( 'shortcodes/pc-builder-search.php', $args );
	}

	/**
	 * Load enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
	}
}
