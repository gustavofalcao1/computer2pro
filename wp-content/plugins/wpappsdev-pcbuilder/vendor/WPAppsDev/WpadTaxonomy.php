<?php

namespace WPAppsDev;
use WPAppsDev\WpadHelper;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

/**
 * Taxonomies Class.
 * Used to help create custom taxonomies for Wordpress.
 *
 * @author  Saiful Islam Ananda
 * @link    http://saifulananda.me/
 * @version 1.0.0
 */
class WpadTaxonomy extends WpadHelper {
	/**
	 * Post type name.
	 *
	 * @var string $post_type Holds the name of the post type.
	 */
	private $post_type;

	/**
	 * User submitted args assigned on __construct().
	 *
	 * @var array $args Holds the user submitted post type argument.
	 */
	public $args;

	/**
	 * User submitted labels assigned on __construct().
	 *
	 * @var array $labels Holds the user submitted post type labels argument.
	 */
	public $labels;

	/**
	 * Holds the singular name of the post type. This is a human friendly
	 * name, capitalized with spaces assigned on __construct().
	 *
	 * @var string $singular Post type singular name.
	 */
	private $singular;

	/**
	 * Holds the plural name of the post type. This is a human friendly
	 * name, capitalized with spaces assigned on __construct().
	 *
	 * @var string $plural Singular post type name.
	 */
	private $plural;

	/**
	 * Post type slug. This is a robot friendly name, all lowercase and uses
	 * hyphens assigned on __construct().
	 *
	 * @var string $slug Holds the post type slug name.
	 */
	private $slug;

	/**
	 * Constructs the class with important vars and method calls
	 * If the taxonomy exists, it will be attached to the post type
	 *
	 * @param 	string 			$name
	 * @param 	string 			$post_type
	 * @param 	array 			$args
	 * @param 	array 			$labels
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 */
	function __construct( $taxnomoy_names, $post_type,  $singular = "", $plural = "",  $args = array(), $labels = array() ) {
		$this->post_type        = self::uglify( $post_type );
		$this->singular 		= ( ! empty( $singular ) ) ? $singular : self::beautify( $taxnomoy_names );
		$this->plural 			= ( ! empty( $plural ) ) ? $plural : self::pluralize( self::beautify( $taxnomoy_names ) );
		$this->slug             = self::uglify( $taxnomoy_names );
		$this->args             = (array) $args;
		$this->labels           = (array)$labels;

		// Register the taxnomoy type.
		self::init( [ $this, 'register_taxonomy' ] );
	}

	/**
	 * Registers the custom taxonomy with the given arguments
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 * @see http://codex.wordpress.org/Function_Reference/register_taxonomy
	 *
	 */
	public function register_taxonomy() {
		// Default labels.
		$labels = [
			'name'                       => sprintf( __( '%s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'singular_name'              => sprintf( __( '%s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'menu_name'                  => sprintf( __( '%s', 'wpappsdev-pcbuilder' ), $this->plural ),
			'all_items'                  => sprintf( __( 'All %s', 'wpappsdev-pcbuilder' ), $this->plural ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'view_item'                  => sprintf( __( 'View %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'update_item'                => sprintf( __( 'Update %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'wpappsdev-pcbuilder' ), $this->singular ),
			'parent_item'                => sprintf( __( 'Parent %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:', 'wpappsdev-pcbuilder' ), $this->singular ),
			'search_items'               => sprintf( __( 'Search %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'popular_items'              => sprintf( __( 'Popular %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'separate_items_with_commas' => sprintf( __( 'Seperate %s with commas', 'wpappsdev-pcbuilder' ), $this->singular ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'choose_from_most_used'      => sprintf( __( 'Choose from most used %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'not_found'                  => sprintf( __( 'No %s found', 'wpappsdev-pcbuilder' ), $this->singular ),
			'back_to_items'              => sprintf( __( '← Back to %s', 'wpappsdev-pcbuilder' ), $this->plural ),
		];

		// Merge user submitted options with defaults.
		$this->labels = array_replace_recursive( $labels, $this->labels );

		// Default options.
		$args = [
			'labels'                => $this->labels,
			//'description'         => '',
			//'public'              => true,
			'hierarchical'          => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			//'show_tagcloud'       => null,
			'show_in_quick_edit'    => true,
			'show_admin_column'     => true,
			//'meta_box_cb'         => null,
			//'capabilities'        => array(),
			'rewrite'               => array( $slug = $this->slug ),
			'query_var'             => true,
			'update_count_callback' => '',
			//'_builtin'            => false,
		];

		// Check if admin column is filterable or not.
		if( isset( $this->args['admin_column_filter'] ) && $this->args['admin_column_filter'] ) :
			$admin_column_filter = true;
			unset( $this->args['admin_column_filter'] );
		else :
			$admin_column_filter = false;
		endif;

		//Check if admin column is sortable or nor
		if( isset( $this->args['admin_column_sortable'] ) && $this->args['admin_column_sortable'] ) :
			$admin_column_sortable = true;
			unset( $this->args['admin_column_sortable'] );
		else :
			$admin_column_sortable = false;
		endif;

		//Check if only admin can add delete and edit terms
		if( isset( $this->args['user_read_only'] ) && $this->args['user_read_only'] ) :
			$user_read_only = true;
			unset( $this->args['user_read_only'] );
		else :
			$user_read_only = false;
		endif;

		// Merge default options with user submitted options.
		$this->args = array_replace_recursive( $args, $this->args );

		// Register the taxonomy if it doesn't exist.
		if ( ! taxonomy_exists( $this->slug ) ) :
			// Register the taxonomy with Wordpress
			register_taxonomy( $this->slug, $this->post_type, $this->args );
		else :
			// If taxonomy exists, register it later with register_exisiting_taxonomies
			self::register_existing_taxonomy( $this->slug, $this->post_type );
		endif;

		// Check if wordpress version gaterthan 3.5 or not
		if( get_bloginfo( 'version' ) < '3.5' ) :
			self::add_filter( 'manage_' . $this->post_type . '_posts_columns', [ $this, 'add_column' ] );
			self::add_action( 'manage_' . $this->post_type . '_posts_custom_column', [ $this, 'add_column_content' ], 10, 2 );
		endif;

		// If admin sortable column enable add sortable column hook.
		if( $admin_column_sortable ) :
			self::add_action( 'manage_edit-' . $this->post_type . '_sortable_columns', [ $this, 'sortable_column' ], 10, 2 );
		endif;

		// If admin filter field enable add filter field hook.
		if( $admin_column_filter ) :
			// add filter field
			self::add_action( 'restrict_manage_posts', [ $this, 'post_filter' ] );
			// set query value for this filter.
			self::add_filter( 'parse_query', [ $this, 'post_filter_query' ] );
		endif;

		// If admin filter field enable add filter field hook.
		if( $user_read_only ) :
			self::add_action( 'pre_insert_term', [ $this, 'admin_can_add_terms' ] , 0, 2 );
			self::add_action( 'pre_delete_term', [ $this, 'admin_can_delete_terms' ] , 0, 2 );
			self::add_action( 'edit_terms', [ $this, 'admin_can_edit_terms' ] , 0, 2 );
		endif;
	}

	/**
	 * Used to attach the existing taxonomy to the post type
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	static public function register_existing_taxonomy( $slug, $post_type ) {
		register_taxonomy_for_object_type( $slug, $post_type );
	}

	/**
	 * Used to add a column head to the Post Type's List Table
	 *
	 * @param 	array 			$columns
	 * @return 	array
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function add_column( $columns ) {
		$temp = $columns['date'];
		unset( $columns['date'] );

		$columns[ $this->slug ] = $this->singular;
		$columns['date'] = $temp;

		return $columns;
	}

	/**
	 * Used to add the column content to the column head
	 *
	 * @param 	string 			$column
	 * @param 	integer 		$post_id
	 * @return 	mixed
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function add_column_content( $column, $post_id ) {
		if ( $column === $this->slug ) :
			$terms = wp_get_post_terms( $post_id, $this->slug, array( 'fields' => 'names' ) );
			echo esc_attr( implode( $terms, ', ' ) );
		endif;
	}

	/**
	 * Used to make all columns sortable
	 *
	 * @param 	array 			$columns
	 * @return  array
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function sortable_column( $columns ) {
		$columns[ ( get_bloginfo( 'version' ) < '3.5' ) ? $this->slug : 'taxonomy-' . $this->slug ] = $this->slug;

		return $columns;
	}

	/**
	 * Adds a filter to the post table filters
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function post_filter() {
		global $typenow, $wp_query;
		$args = array(
			'post_type'=> $this->post_type,
		);

		$post_count = count( get_posts( $args ) );

		if( $typenow == $this->post_type && $post_count > 0 ) :
			wp_dropdown_categories( [
				'show_option_all'	=> sprintf( __( '%s', 'wpappsdev-pcbuilder' ), $this->plural ),
				'show_option_none'	=> sprintf( __( ' Select %s', 'wpappsdev-pcbuilder' ), $this->singular ),
				'option_none_value' => -1,
				'taxonomy'       	=> $this->slug,
				'name'            	=> $this->slug,
				'orderby'         	=> 'name',
				'selected'        	=> isset( $wp_query->query[ $this->slug ] ) ? $wp_query->query[ $this->slug ] : '',
				'hierarchical'    	=> true,
				'show_count'      	=> true,
				'hide_empty'      	=> true,
			] );
		endif;
	}

	/**
	 * Applies the selected filter to the query
	 *
	 * @param 	object 			$query
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 */
	public function post_filter_query( $query ) {
		global $pagenow;
		$vars = &$query->query_vars;

		if( $pagenow == 'edit.php' && isset( $vars[ $this->slug ] ) && is_numeric( $vars[ $this->slug ] ) && $vars[ $this->slug ] ) :
			$term = get_term_by( 'id', $vars[ $this->slug ], $this->slug );
			if( is_object( $term ) ) :
				$vars[ $this->slug ] = $term->slug;
			endif;
		endif;

		return $vars;
	}

	/**
	 * Only admin can add taxonomy terms.
	 *
	 * @param 	integer			$term
	 * @param 	string 			$taxonomy
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function admin_can_add_terms( $term, $taxonomy ) {
		return ( $this->slug === $taxonomy && !current_user_can( 'activate_plugins' ) ) ? new WP_Error( 'term_addition_blocked', __( 'Only admin can add terms to this taxonomy.' ) ) : $term;
	}

	/**
	 * Only admin can delete taxonomy terms.
	 *
	 * @param 	integer			$term
	 * @param 	string 			$taxonomy
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function admin_can_delete_terms( $term, $taxonomy ) {
		if( $this->slug === $taxonomy && ! current_user_can( 'activate_plugins' ) ) :
			wp_die( esc_attr__( 'Only admin can delete terms to this taxonomy.' ) );
		else:
			return $term;
		endif;
	}

	/**
	 * Only admin can edit taxonomy terms.
	 *
	 * @param 	integer			$term
	 * @param 	string 			$taxonomy
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 */
	public function admin_can_edit_terms( $term, $taxonomy ) {
		if( $this->slug === $taxonomy && !current_user_can( 'activate_plugins' ) ) :
			wp_die( esc_attr__('Only admin can edit terms to this taxonomy.') );
		else:
			return $term;
		endif;
	}
}
