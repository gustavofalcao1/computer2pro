<?php

namespace WPAppsDev\PCBU\Abstracts;

abstract class WPAppsDevShortcode {
	/**
	 * Shortcode ID.
	 *
	 * @var string
	 */
	protected $shortcode = '';

	/**
	 * Undocumented function
	 */
	public function __construct() {
		if ( empty( $this->shortcode ) ) {
			wpadpcbu_doing_it_wrong( static::class, __( '$shortcode property is empty.', 'wpappsdev-pcbuilder' ), '1.0.0' );
		}

		add_shortcode( $this->shortcode, [ $this, 'render_shortcode' ] );
	}

	/**
	 * Get shortcode id.
	 *
	 * @return string
	 */
	public function get_shortcode() {
		return $this->shortcode;
	}

	abstract public function render_shortcode( $atts );
}
