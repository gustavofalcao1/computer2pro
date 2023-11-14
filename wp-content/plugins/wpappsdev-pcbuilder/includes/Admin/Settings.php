<?php

namespace WPAppsDev\PCBU\Admin;

use WPAppsDev\WpadSettingApi;

/**
 * The settings handler class.
 */
class Settings {
	/**
	 * Hold settings api object.
	 *
	 * @var object
	 */
	protected static $settings;

	/**
	 * Initialize the class.
	 */
	public function __construct() {
		self::$settings = new WpadSettingApi();

		// Register admin menu.
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		// Init settings api.
		add_action( 'admin_init', [ $this, 'settings_api_init' ] );
	}

	/**
	 * Register admin menu.
	 *
	 * @return void
	 */
	public function admin_menu() {
		$base_slug  = 'wpadpcbu-pcbuilder';
		$manage     = 'manage_woocommerce';
		$capability = apply_filters( 'wpadpcbu_manager_capability', $manage );

		add_submenu_page(
			$base_slug,
			__( 'Pc Builder Settings', 'wpappsdev-pcbuilder' ),
			__( 'Settings', 'wpappsdev-pcbuilder' ),
			$capability,
			'pcbusettings',
			[ $this, 'settings_page' ]
		);
	}

	/**
	 * Display Settings Page.
	 *
	 * @return void
	 */
	public function settings_page() {
		echo '<div class="wrap">';
		echo '<div class="settings-body">';
		self::$settings->show_navigation();
		self::$settings->show_forms();
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Init settings api options.
	 *
	 * @return void
	 */
	public function settings_api_init() {
		//set the settings
		self::$settings->set_sections( self::settings_sections() );
		self::$settings->set_fields( self::settings_fields() );

		//initialize settings
		self::$settings->admin_init();
	}

	/**
	 * Returns all the settings sections.
	 *
	 * @return array
	 */
	public static function settings_sections() {
		$sections = [
			[
				'id'         => 'page_settings',
				'title'      => __( 'Builder Pages Settings', 'wpappsdev-pcbuilder' ),
				'menu_title' => __( 'Pages Settings', 'wpappsdev-pcbuilder' ),
			],
			[
				'id'         => 'builder_page_settings',
				'title'      => __( 'Builder Page Settings', 'wpappsdev-pcbuilder' ),
				'menu_title' => __( 'Builder Page', 'wpappsdev-pcbuilder' ),
			],
			[
				'id'         => 'search_page_settings',
				'title'      => __( 'Search Page Settings', 'wpappsdev-pcbuilder' ),
				'menu_title' => __( 'Search Page', 'wpappsdev-pcbuilder' ),
			],
		];

		$sections = apply_filters( 'wpadpcbu_section_settings_tabs', $sections );

		return $sections;
	}

	/**
	 * Returns all the settings fields.
	 *
	 * @return array
	 */
	public static function settings_fields() {
		$settings_fields = [
			'page_settings' => apply_filters( 'wpadpcbu_page_settings_fields',
				[
					[
						'name'    => 'wpadpcbu_builder_page',
						'label'   => __( 'Select Builder Page', 'wpappsdev-pcbuilder' ),
						'type'    => 'select',
						'options' => self::get_pages_options(),
					],
					[
						'name'    => 'wpadpcbu_builder_search_page',
						'label'   => __( 'Select Builder Search Page', 'wpappsdev-pcbuilder' ),
						'type'    => 'select',
						'options' => self::get_pages_options(),
					],
				]
			),
			'builder_page_settings' => apply_filters( 'wpadpcbu_builder_page_settings_fields',
				[
					[
						'name'  => 'wpadpcbu_builder_breadcrumb_disable',
						'label' => __( 'Disable Breadcrumb', 'wpappsdev-pcbuilder' ),
						'desc'  => __( 'Disable builder page breadcrumb section.', 'wpappsdev-pcbuilder' ),
						'type'  => 'checkbox',
					],
					[
						'name'    => 'wpadpcbu_builder_breadcrumb_menu_name',
						'label'   => __( 'Breadcrumb Menu Name', 'wpappsdev-pcbuilder' ),
						'desc'    => __( 'Input breadcrumb section menu name.', 'wpappsdev-pcbuilder' ),
						'type'    => 'text',
						'default' => __( 'PC Builder', 'wpappsdev-pcbuilder' ),
					],
					[
						'name'    => 'wpadpcbu_builder_breadcrumb_title',
						'label'   => __( 'Breadcrumb Title', 'wpappsdev-pcbuilder' ),
						'desc'    => __( 'Input breadcrumb section title.', 'wpappsdev-pcbuilder' ),
						'type'    => 'text',
						'default' => __( 'PC Builder - Build Your Own Computer', 'wpappsdev-pcbuilder' ),
					],
					[
						'name'  => 'wpadpcbu_builder_actions_disable',
						'label' => __( 'Disable Actions', 'wpappsdev-pcbuilder' ),
						'desc'  => __( 'Disable builder page actions section.', 'wpappsdev-pcbuilder' ),
						'type'  => 'checkbox',
					],
					[
						'name'  => 'wpadpcbu_redirect_checkout_disable',
						'label' => __( 'Redirect Checkout', 'wpappsdev-pcbuilder' ),
						'desc'  => __( 'Enable redirect to checkout page after product added to cart.', 'wpappsdev-pcbuilder' ),
						'type'  => 'checkbox',
					],
					// [
					// 	'name'    => 'wpadpcbu_builder_disable_table_column',
					// 	'label'   => __( 'Disable Columns', 'wpappsdev-pcbuilder' ),
					// 	'desc'    => __( 'Disable builder page table columns.', 'wpappsdev-pcbuilder' ),
					// 	'type'    => 'multicheck',
					// 	'options' => [
					// 		'image' => __( 'Image', 'wpappsdev-pcbuilder' ),
					// 		'name'  => __( 'Product Name', 'wpappsdev-pcbuilder' ),
					// 		'price' => __( 'Price', 'wpappsdev-pcbuilder' ),
					// 	],
					// ],
				]
			),
			'search_page_settings' => apply_filters( 'wpadpcbu_search_page_settings_fields',
				[
					[
						'name'    => 'wpadpcbu_search_per_page',
						'label'   => __( 'Per Page', 'wpappsdev-pcbuilder' ),
						'type'    => 'text',
						'default' => 10,
					],
					[
						'name'  => 'wpadpcbu_search_breadcrumb_disable',
						'label' => __( 'Disable Breadcrumb', 'wpappsdev-pcbuilder' ),
						'desc'  => __( 'Disable search page breadcrumb section.', 'wpappsdev-pcbuilder' ),
						'type'  => 'checkbox',
					],
					[
						'name'    => 'wpadpcbu_search_breadcrumb_title',
						'label'   => __( 'Breadcrumb Title', 'wpappsdev-pcbuilder' ),
						'desc'    => __( 'Input breadcrumb section title. Short Tag: {component_name}', 'wpappsdev-pcbuilder' ),
						'type'    => 'text',
						'default' => __( 'PC Builder - Find Your {component_name} Component', 'wpappsdev-pcbuilder' ),
					],
					[
						'name'  => 'wpadpcbu_search_top_filter_disable',
						'label' => __( 'Disable Top Filter', 'wpappsdev-pcbuilder' ),
						'desc'  => __( 'Disable search page top filter section.', 'wpappsdev-pcbuilder' ),
						'type'  => 'checkbox',
					],
					[
						'name'    => 'wpadpcbu_search_price_range_start',
						'label'   => __( 'Price range start', 'wpappsdev-pcbuilder' ),
						'type'    => 'text',
						'default' => 0,
					],
					[
						'name'    => 'wpadpcbu_search_price_range_end',
						'label'   => __( 'Price range end', 'wpappsdev-pcbuilder' ),
						'type'    => 'text',
						'default' => 80000,
					],
				]
			),
		];

		$settings_fields = apply_filters( 'wpadpcbu_section_settings_tabs_fields', $settings_fields );

		return $settings_fields;
	}

	/**
	 * Get page select options.
	 *
	 * @return void
	 */
	public static function get_pages_options() {
		$pages   = get_pages();
		$options = [ -1 => __( 'Select Page', 'wpappsdev-pcbuilder' ) ];

		foreach ( $pages as $page ) {
			$options[ $page->ID ] = $page->post_title;
		}

		return $options;
	}
}
