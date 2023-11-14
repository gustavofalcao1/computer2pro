<?php
/**
 * Plugin Name:       WPAppsDev - WooCommerce PcBuilder Toolkit
 * Description:       Complete PC Components Selling Solution Toolkit for WooCommerce.
 * Version:           1.2.0
 * Author:            Saiful Islam Ananda
 * Author URI:        http://saifulananda.me/
 * License:           GNU General Public License v2 or later
 * Text Domain:       wpappsdev-pcbuilder
 * Domain Path:       /languages
 * WC tested up to:   6.8.2
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPAppsDev_Pcbuilder class.
 *
 * @class WPAppsDev_Pcbuilder The class that holds the entire WPAppsDev_Pcbuilder plugin
 *
 * @since 1.0.0
 *
 * @author Saiful Islam Ananda
 */
final class WPAppsDev_Pcbuilder {
	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.1.0';

	/**
	 * Instance of self.
	 *
	 * @var WPAppsDev_Pcbuilder
	 */
	private static $instance = null;

	/**
	 * Holds various class instances.
	 *
	 * @var array
	 */
	private $container = [];

	/**
	 * Constructor for the WPAppsDev_Pcbuilder class.
	 *
	 * Sets up all the appropriate hooks and actions within our plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function __construct() {
		require_once __DIR__ . '/vendor/autoload.php';

		$this->define_constants();

		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
	}

	/**
	 * Initializes the WPAppsDev_Pcbuilder() class.
	 *
	 * Checks for an existing WPAppsDev_Pcbuilder() instance and if it doesn't find one, creates it.
	 *
	 * @since 1.0.0
	 *
	 * @return WPAppsDev_Pcbuilder|bool
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Magic getter to bypass referencing objects.
	 *
	 * @param $prop
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
			return $this->container[ $prop ];
		}
	}

	/**
	 * Magic isset to bypass referencing plugin.
	 *
	 * @param $prop
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function __isset( $prop ) {
		return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
	}

	/**
	 * Define the required plugin constants.
	 *
	 * @return void
	 */
	public function define_constants() {
		$this->define( 'WPADPCBU', __FILE__ );
		$this->define( 'WPADPCBU_NAME', 'wpappsdev-pcbuilder' );
		$this->define( 'WPADPCBU_VERSION', $this->version );
		$this->define( 'WPADPCBU_DIR', trailingslashit( plugin_dir_path( WPADPCBU ) ) );
		$this->define( 'WPADPCBU_URL', trailingslashit( plugin_dir_url( WPADPCBU ) ) );
		$this->define( 'WPADPCBU_ASSETS', trailingslashit( WPADPCBU_URL . 'assets' ) );
	}

	/**
	 * Define constant if not already defined.
	 *
	 * @param string      $name
	 * @param string|bool $value
	 *
	 * @return void
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init_plugin() {
		$this->includes();
		$this->init_hooks();

		do_action( 'wpadpcbu_loaded' );
	}

	/**
	 * Include all the required files.
	 *
	 * @return void
	 */
	public function includes() {
		//require_once WPADPCBU_DIR . 'includes/settings.php';
	}

	/**
	 * Initialize the action and filter hooks.
	 *
	 * @return void
	 */
	public function init_hooks() {
		if ( ! self::check_required_plugin() ) {
			add_action( 'admin_notices', [ $this, 'required_plugin_notice' ] );

			return;
		}
		// Localize our plugin
		add_action( 'init', [ $this, 'localization_setup' ] );

		// initialize the classes
		add_action( 'init', [ $this, 'init_classes' ], 5 );
		add_action( 'init', [ $this, 'wpdb_table_shortcuts' ] );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'plugin_action_links' ] );
	}

	/**
	 * Initialize plugin for localization.
	 *
	 * @uses load_plugin_textdomain()
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'wpappsdev-pcbuilder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Init all the classes.
	 *
	 * @return void
	 */
	public function init_classes() {
		if ( is_admin() ) {
			new WPAppsDev\PCBU\Admin();
		} else {
			new WPAppsDev\PCBU\Frontend();

			$this->container['shortcodes'] = new WPAppsDev\PCBU\Shortcodes\Shortcodes();
		}
		new WPAppsDev\PCBU\Frontend\CustomerDashboard();

		$this->container['Component']       = new WPAppsDev\PCBU\Component();
		$this->container['FiltersGroup']    = new WPAppsDev\PCBU\FiltersGroup();
		$this->container['DynamicTaxonomy'] = new WPAppsDev\PCBU\DynamicTaxonomy();
		$this->container['scripts']         = new WPAppsDev\PCBU\Assets();

		// Add helper class.
		$this->container['savedpc'] = new WPAppsDev\PCBU\Helper\SavedPcManager();
		$this->container['builder'] = new WPAppsDev\PCBU\Helper\BuilderManager();
		$this->container['search']  = new WPAppsDev\PCBU\Helper\SearchManager();

		$this->container = apply_filters( 'wpadpcbu_class_container', $this->container );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			new WPAppsDev\PCBU\Ajax();
		}
	}

	/**
	 * Load table prefix for custom table.
	 *
	 * @return void
	 */
	public function wpdb_table_shortcuts() {
		global $wpdb;

		$wpdb->wpadpcbu_saved_pc = $wpdb->prefix . 'wpadpcbu_saved_pc';
	}

	/**
	 * Plugin settings action link.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function plugin_action_links( $actions ) {
		$actions[] = '<a href="' . esc_url( get_admin_url( null, 'edit-tags.php?taxonomy=pcbucomp&post_type=product' ) ) . '">' . __( 'Configure', 'wpappsdev-pcbuilder' ) . '</a>';

		return $actions;
	}

	/**
	 * Do stuff upon plugin activation.
	 *
	 * @return void
	 */
	public function activate() {
		$installed = get_option( 'wpadpcbu_installed' );

		if ( ! $installed ) {
			update_option( 'wpadpcbu_installed', time() );
		}

		update_option( 'wpadpcbu_version', WPADPCBU_VERSION );
		set_transient( 'wpadpcbu_flush_rewrite', true );

		$installer = new \WPAppsDev\PCBU\Installer();
		$installer->do_install();
	}

	/**
	 * Do stuff upon plugin deactivation.
	 *
	 * @return void
	 */
	public function deactivate() {
	}

	/**
	 * Required plugins validation.
	 *
	 * @return void
	 */
	public static function check_required_plugin() {
		if ( in_array( 'woocommerce/woocommerce.php', (array) get_option( 'active_plugins', [] ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * CF7 plugin activation notice.
	 *
	 * @return void
	 * */
	public function required_plugin_notice() {
		$core_plugin_file = 'woocommerce/woocommerce.php';

		include_once WPADPCBU_DIR . 'templates/admin/admin-notice.php';
	}
}

/**
 * Initializes the main plugin.
 *
 * @return \WPAppsDev_Pcbuilder
 */
function wpadpcbu_process() {
	return WPAppsDev_Pcbuilder::init();
}

// Lets Go....
wpadpcbu_process();
