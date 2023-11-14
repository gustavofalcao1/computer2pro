<?php

namespace WPAppsDev\PCBU\Shortcodes;

use WPAppsDev\PCBU\Abstracts\WPAppsDevShortcode;

class PcBuilders extends WPAppsDevShortcode {
	protected $shortcode = 'wpadpcbu-pcbuilder';

	/**
	 * Load template files.
	 *
	 * @param array $atts
	 *
	 * @return void
	 */
	public function render_shortcode( $atts ) {
		$this->enqueue_scripts();

		$pcbu_data  = wpadpcbu_process()->builder->get_builder_data();
		$components = get_component_data();

		$args = [
			'components' => $components,
			'columns'    => wpadpcbu_process()->builder->table_columns(),
			'items'      => isset( $pcbu_data['items'] ) ? $pcbu_data['items'] : [],
			'total'      => isset( $pcbu_data['total'] ) ? wc_price( $pcbu_data['total'] ) : 0,
		];

		return wpadpcbu_get_template_html( 'shortcodes/pc-builder.php', $args );
	}

	/**
	 * Load enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
	}
}
