<?php

namespace WPAppsDev\PCBU;

class Assets {
	/**
	 * Initialize the class.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_all_scripts' ], 10 );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		} else {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_front_scripts' ] );
		}
	}

	/**
	 * Register all scripts and styles.
	 */
	public function register_all_scripts() {
		$styles  = $this->get_styles();
		$scripts = $this->get_scripts();

		$this->register_styles( $styles );
		$this->register_scripts( $scripts );

		do_action( 'wpadpcbu_register_scripts' );
	}

	/**
	 * Get registered styles.
	 *
	 * @return array
	 */
	public function get_styles() {
		$prefix = self::get_prefix();

		// All CSS file list.
		$styles = [
			'wpadpcbu-admin' => [
				'src'     => WPADPCBU_ASSETS . 'css/wpadpcbu-admin.css',
				'deps'    => [],
				'version' => filemtime( WPADPCBU_DIR . 'assets/css/wpadpcbu-admin.css' ),
			],
			'wpadpcbu-public' => [
				'src'     => WPADPCBU_ASSETS . 'css/wpadpcbu-public.css',
				'deps'    => [],
				'version' => filemtime( WPADPCBU_DIR . 'assets/css/wpadpcbu-public.css' ),
			],
			'wpadpcbu-waitMe' => [
				'src'  => WPADPCBU_ASSETS . 'lib/waitMe.min.css',
				'deps' => [],
			],
			'wpadpcbu-bootstrap.min' => [
				'src'  => WPADPCBU_ASSETS . 'lib/bootstrap.min.css',
				'deps' => [],
			],
			'wpadpcbu-bootstrap.min.map' => [
				'src'  => WPADPCBU_ASSETS . 'lib/bootstrap.min.css.map',
				'deps' => [],
			],
			'wpadpcbu-ion.rangeSlider.min' => [
				'src'  => WPADPCBU_ASSETS . 'lib/ion.rangeSlider.min.css',
				'deps' => [],
			],
		];

		return $styles;
	}

	/**
	 * Get all registered scripts.
	 *
	 * @return array
	 */
	public function get_scripts() {
		$prefix = self::get_prefix();

		// All JS file list.
		$scripts = [
			// Register scripts
			'wpadpcbu-admin' => [
				'src'     => WPADPCBU_ASSETS . "js/wpadpcbu-admin{$prefix}.js",
				'deps'    => [ 'jquery' ],
				'version' => filemtime( WPADPCBU_DIR . "assets/js/wpadpcbu-admin{$prefix}.js" ),
			],
			'wpadpcbu-public' => [
				'src'     => WPADPCBU_ASSETS . "js/wpadpcbu-public{$prefix}.js",
				'deps'    => [ 'jquery' ],
				'version' => filemtime( WPADPCBU_DIR . "assets/js/wpadpcbu-public{$prefix}.js" ),
			],
			'wpadpcbu-waitMe' => [
				'src'  => WPADPCBU_ASSETS . 'lib/waitMe.min.js',
				'deps' => [ 'jquery' ],
				//'version'   => filemtime( WPADPCBU_DIR . "assets/js/waitMe.min.js" ),
			],
			'wpadpcbu-ion.rangeSlider.min' => [
				'src'  => WPADPCBU_ASSETS . 'lib/ion.rangeSlider.min.js',
				'deps' => [ 'jquery' ],
				//'version'   => filemtime( WPADPCBU_DIR . "assets/js/waitMe.min.js" ),
			],
		];

		return $scripts;
	}

	/**
	 * Get file prefix.
	 *
	 * @return string
	 */
	public static function get_prefix() {
		$prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '';

		return $prefix;
	}

	/**
	 * Register scripts.
	 *
	 * @param array $scripts
	 *
	 * @return void
	 */
	public function register_scripts( $scripts ) {
		foreach ( $scripts as $handle => $script ) {
			$deps      = isset( $script['deps'] ) ? $script['deps'] : false;
			$in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : true;
			$version   = isset( $script['version'] ) ? $script['version'] : WPADPCBU_VERSION;

			wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
		}
	}

	/**
	 * Register styles.
	 *
	 * @param array $styles
	 *
	 * @return void
	 */
	public function register_styles( $styles ) {
		foreach ( $styles as $handle => $style ) {
			$deps    = isset( $style['deps'] ) ? $style['deps'] : false;
			$version = isset( $style['version'] ) ? $style['version'] : WPADPCBU_VERSION;

			wp_register_style( $handle, $style['src'], $deps, $version );
		}
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_admin_scripts( $hook ) {
		$default_script = [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'wpadpcbu-admin-security' ),
		];

		$localize_data = apply_filters( 'wpadpcbu_admin_localized_args', $default_script );

		// Enqueue scripts
		wp_enqueue_media();
		wp_enqueue_script( 'wpadpcbu-admin' );
		wp_localize_script( 'wpadpcbu-admin', 'wpadpcbu_admin', $localize_data );
		wp_enqueue_script( 'wpadpcbu-waitMe' );

		// Enqueue Styles
		wp_enqueue_style( 'wpadpcbu-admin' );
		wp_enqueue_style( 'wpadpcbu-waitMe' );

		do_action( 'wpadpcbu_enqueue_admin_scripts' );
	}

	/**
	 * Enqueue front-end scripts.
	 */
	public function enqueue_front_scripts() {
		global $wp_query;
		$query_vars = $wp_query->query_vars;

		$object_id = get_queried_object_id();
		$pages_id  = get_settings_pages_id();
		$savedpc   = isset( $query_vars['savedpcs'] ) ? true : false;
		$viewpc    = isset( $query_vars['view-savedpc'] ) ? true : false;

		do_action( 'wpadpcbu_before_public_enqueue' );

		if ( ! in_array( $object_id, $pages_id, true ) && ! $savedpc && ! $viewpc ) {
			return;
		}
		$default_script = [
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'wpadpcbu-security' ),
			'pcbucomp'    => isset( $_REQUEST['pcbucomp'] ) ? abs( $_REQUEST['pcbucomp'] ) : 0,
			'builder'     => get_builder_page(),
			'search'      => get_search_page(),
			'start_range' => wpadpcbu_process()->search->start_range(),
			'end_range'   => wpadpcbu_process()->search->end_range(),
		];

		// Front end localize data
		$localize_data = apply_filters( 'wpadpcbu_public_localized_args', $default_script );

		// Enqueue scripts
		wp_enqueue_script( 'wpadpcbu-ion.rangeSlider.min' );
		wp_enqueue_script( 'wpadpcbu-waitMe' );
		wp_enqueue_script( 'wpadpcbu-public' );
		wp_localize_script( 'wpadpcbu-public', 'wpadpcbu_public', $localize_data );

		// Enqueue Styles
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'wpadpcbu-ion.rangeSlider.min' );
		wp_enqueue_style( 'wpadpcbu-waitMe' );
		wp_enqueue_style( 'wpadpcbu-public' );

		if ( ! $savedpc && ! $viewpc ) {
			wp_enqueue_style( 'wpadpcbu-bootstrap.min' );
			//wp_enqueue_style( 'wpadpcbu-bootstrap.min.map' );
		}

		do_action( 'wpadpcbu_after_public_enqueue' );
	}
}
