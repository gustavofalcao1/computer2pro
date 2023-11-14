<?php

namespace WPAppsDev\PCBU\Shortcodes;

class Shortcodes {
	private $shortcodes = [];

	/**
	 *  Register Dokan shortcodes.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->shortcodes = apply_filters( 'wpadpcbu_shortcodes', [
			'wpadpcbu-pcbuilder'        => new PcBuilders(),
			'wpadpcbu-pcbuilder-search' => new PcBuildersSearch(),
		] );
	}

	/**
	 * Get registered shortcode classes.
	 *
	 * @return array
	 */
	public function get_shortcodes() {
		return $this->shortcodes;
	}
}
