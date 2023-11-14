<?php
namespace WPAppsDev;
use WPAppsDev\WpadHelper;
use WPAppsDev\WpadTaxonomy;
use WPAppsDev\WpadMetaBox;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

/**
 * Custom Post Type Class.
 * Used to help create custom post types for Wordpress.
 *
 * @author  Saiful Islam Ananda
 * @link    http://saifulananda.me/
 * @version 1.0.0
 */
class WpadCpt extends WpadHelper {
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
	 * Hold WpadTaxonomy object.
	 *
	 * @var object $taxonomy Holds the WpadTaxonomy object.
	 */
	private $taxonomy;

	/**
	 * Hold WpadMetaBox object.
	 *
	 * @var object $metabox Holds the WpadMetaBox object.
	 */
	private $metabox;

	private $add_supports = [];
	private $rem_supports = [];

	/**
	 * Sets default values, registers the passed post type, and
	 * listens for when the post is saved.
	 *
	 * @param string $post_type The name of the desired post type.
	 * @param string $singular Singular name for this post type.
	 * @param string $plural Plural name of this post type.
	 * @param array @args Override the options.
	 */
	function __construct( $post_type, $singular = "", $plural = "", $args = array(), $labels = array() ) {
		$this->post_type = self::uglify( $post_type );
		$this->singular  = ( ! empty( $singular ) ) ? $singular : self::beautify( $post_type );
		$this->plural    = ( ! empty( $plural ) ) ? $plural : self::pluralize( self::beautify( $post_type ) );
		$this->slug      = self::uglify( $post_type );
		$this->args      = (array) $args;
		$this->labels    = (array) $labels;

		// First step, register that new post type
		self::init( [ $this, "register_post_type" ] );

		self::add_filter( 'enter_title_here', [ $this, 'post_title' ] );

		self::init( [ $this, "add_support" ] );
		self::init( [ $this, "remove_supports" ] );
	}

	/**
	 * Register Post Type
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 * @see http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_type() {
		// Default labels.
		$labels = [
			'name'               => sprintf( __( '%s', 'wpappsdev-pcbuilder' ), $this->plural ),
			'singular_name'      => sprintf( __( '%s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'menu_name'          => sprintf( __( '%s', 'wpappsdev-pcbuilder' ), $this->plural ),
			'all_items'          => sprintf( __( '%s', 'wpappsdev-pcbuilder' ), $this->plural ),
			'add_new'            => __( 'Add New', 'wpappsdev-pcbuilder' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'edit_item'          => sprintf( __( 'Edit %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'new_item'           => sprintf( __( 'New %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'view_item'          => sprintf( __( 'View %s', 'wpappsdev-pcbuilder' ), $this->singular ),
			'search_items'       => sprintf( __( 'Search %s', 'wpappsdev-pcbuilder' ), $this->plural ),
			'not_found'          => sprintf( __( 'No %s found', 'wpappsdev-pcbuilder' ), $this->plural ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'wpappsdev-pcbuilder' ), $this->plural ),
			'parent_item_colon'  => sprintf( __( 'Parent %s:', 'wpappsdev-pcbuilder' ), $this->singular )
		];

		// Merge user submitted options with defaults.
		$this->labels = array_replace_recursive( $labels, $this->labels );

		/* Post Type args Array */
		$args = [
			'public'             => true,
			'labels'             => $this->labels,
			'show_ui'            => true,
			'query_var'          => true,
			'has_archive'        => $this->post_type,
			'rewrite'            => [ 'slug' => $this->slug ],
			'supports'           => ['title'],
			'publicly_queryable' => true,
			'capability_type'    => 'post',
		];

		// Merge user submitted options with defaults.
		// Set the object options as full options passed.
		$this->args = array_replace_recursive( $args, $this->args );

		// Check that the post type doesn't already exist.
		if ( ! post_type_exists( $this->post_type ) ) {
			// Register the post type.
			register_post_type( $this->post_type, $this->args );
		}
	}

	/**
	 * Add a taxonomy to the Post Type
	 *
	 * @param 	string|array 	$taxonomy_name
	 * @param 	array 			$args
	 * @param 	array 			$labels
	 * @return  object 			Cuztom_Post_Type
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function add_taxonomy( $taxonomy_name, $singular = "", $plural = "", $args = array(), $labels = array() ) {
		$this->taxonomy = new WpadTaxonomy( $taxonomy_name, $this->post_type, $singular, $plural, $args, $labels );

		// For method chaining
		return $this;
	}

	/**
	 * Add a taxonomy to the Post Type
	 *
	 * @param 	string|array 	$taxonomy_name
	 * @param 	array 			$args
	 * @param 	array 			$labels
	 * @return  object 			Cuztom_Post_Type
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function add_metabox( $id, $title, $fields, $context = 'normal', $priority = 'high' ) {
		$this->metabox = new WpadMetaBox( $id, $title, $fields, $this->post_type, $context, $priority );

		// For method chaining
		return $this;
	}

	/**
	 * [set_supports description]
	 * @param [type] $supports [description]
	 */
	public function set_supports( $supports ){
		$this->add_supports = (array) $supports;

		// For method chaining
		return $this;
	}

	/**
	 * Add action to register support of certain features for a post type.
	 *
	 * @param 	array 	$support
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function add_support() {
		foreach ( $this->add_supports as $support ) {
			add_post_type_support( $this->post_type, $support );

			if( 'thumbnail' === $support ):
				//Change Feature Image title
				self::add_action( 'add_meta_boxes', [ $this, 'featured_image_title' ] );
				//Change Feature Image Link Test
				self::add_filter( 'admin_post_thumbnail_html', [ $this, 'featured_image_content' ] );
				self::add_filter( 'media_view_strings', [ $this, 'featured_image_media_strings' ], 10, 2 );
			endif;
		}
	}

	/**
	 * [rem_supports description]
	 * @param [type] $supports [description]
	 */
	public function rem_supports( $supports ){
		$this->rem_supports = (array) $supports;
		// For method chaining
		return $this;
	}

	/**
	 * Add action to remove support of certain features for a post type.
	 *
	 * @param 	string|array 	$features 			The feature being removed, can be an array of feature strings or a single string
	 * @return 	object 			Cuztom_Post_Type
	 *
	 * @author  Saiful Islam Ananda
	 * @link    http://saifulananda.me/
	 * @version 1.0.0
	 *
	 */
	public function remove_supports() {
		foreach( $this->rem_supports as $feature ) {
			remove_post_type_support( $this->post_type, $feature );
		}
	}

	/**
	 * Change Custom Post Title Placeholder
	 *
	 * @param $title
	 */

	public function post_title( $title ){
		$screen = get_current_screen();
		if  ( $this->post_type == $screen->post_type ):
		  $title = $this->singular . ' Name';
		endif;

		return $title;
	}

	/**
	 * Change Feature Image title
	 *
	 * @param $post_type
	 */
	public function featured_image_title( $post_type ) {
		if ( $post_type === $this->post_type ) :
			//remove original featured image metabox
			remove_meta_box( 'postimagediv', $this->post_type, 'side' );
			//add our customized metabox
			add_meta_box( 'postimagediv', $this->singular . ' Image', 'post_thumbnail_meta_box', $this->post_type, 'side', 'low' );
		endif;
	}

	/**
	 * Change Feature Image Link Test
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	function featured_image_content( $content ) {
		$content = str_replace( __( 'Set featured image', 'wpappsdev-pcbuilder' ), 'Add Image', $content );
		$content = str_replace( __( 'Remove featured image', 'wpappsdev-pcbuilder' ), 'Remove Image', $content );

		return $content;
	}

	/**
	 * Change the strings for the media manager modal
	 *
	 * @param $strings
	 * @param $post
	 *
	 * @return mixed
	 */
	public function featured_image_media_strings( $strings, $post ) {
		// checks if post object is empty or not
		if ( ! empty( $post ) ) :
			if ( $post->post_type === $this->post_type ) {
				$strings[ 'setFeaturedImage' ]      = "Set {$this->singular} Image";
				$strings[ 'setFeaturedImageTitle' ] = "{$this->singular} Image";
			}
		endif;

		return $strings;
	}
}