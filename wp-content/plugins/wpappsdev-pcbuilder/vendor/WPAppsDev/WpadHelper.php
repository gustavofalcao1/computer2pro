<?php

namespace WPAppsDev;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

/**
 * WPAppsDev helper Class
 *
 * @author  Saiful Islam Ananda
 * @link    http://saifulananda.me/
 * @version 1.0.0
 */
abstract class WpadHelper {
	/**
	 * @param string $action Name of the action.
	 * @param string $function Function to hook that will run on action.
	 * @param integet $priority Order in which to execute the function, relation to other functions hooked to this action.
	 * @param integer $accepted_args The number of arguments the function accepts.
	 */
	static public function add_action( $action, $function, $priority = 10, $accepted_args = 1 ) {
		// Pass variables into WordPress add_action function
		add_action( $action, $function, $priority, $accepted_args );
	}

	/**
	 * @param  string  $action           Name of the action to hook to, e.g 'init'.
	 * @param  string  $function         Function to hook that will run on @action.
	 * @param  int     $priority         Order in which to execute the function, relation to other function hooked to this action.
	 * @param  int     $accepted_args    The number of arguements the function accepts.
	 */
	static public function add_filter( $action, $function, $priority = 10, $accepted_args = 1 ) {
		// Pass variables into Wordpress add_action function
		add_filter( $action, $function, $priority, $accepted_args );
	}

	/**
	 * Helper method
	 * That passed a function to the 'init' WP action
	 * @param function $cb Passed callback function.
	 */
	static public function init( $cb ) {
		add_action( "init", $cb );
	}

	/**
	 * Helper method
	 * That passed a function to the 'admin_init' WP action
	 * @param function $cb Passed callback function.
	 */
	static public function admin_init( $cb ) {
		add_action("admin_init", $cb);
	}

	/**
	 * Human friendly a string.
	 *
	 * Returns the human friendly name.
	 *
	 *    ucwords      capitalize words
	 *    strtolower   makes string lowercase before capitalizing
	 *    str_replace  replace all instances of hyphens and underscores to spaces
	 *
	 * @param string $string The name you want to make friendly.
	 * @return string The human friendly name.
	 */
	static public function beautify( $string ) {
		// Return human friendly name.
		$search = [ "tj-", "tj_", "-", "_" ];
		$replace = [ "", "", " ", " " ];
		return apply_filters( 'wpappsdev_beautify', ucwords( strtolower( str_replace( $search, $replace, $string ) ) ) );
	}

	/**
	 * Uglifies a string.
	 *
	 * Returns an url friendly slug.
	 *
	 *    strtolower   makes string lowercase.
	 *    str_replace  replace all instances of spaces and underscores to hyphens.
	 *
	 * @param  string $string Name to slugify.
	 * @return string Returns the slug.
	 */
	static public function uglify( $string ) {
		// Return an url friendly slug.
		return apply_filters( 'wpappsdev_uglify', str_replace( "_", "-", str_replace( " ", "-", strtolower( $string ) ) ) );
	}

	/**
	 * Makes a word plural
	 *
	 * @param 	string 			$string
	 * @return 	string
	 *
	 */
	static public function pluralize( $string ) {
		$plural = array(
			array( '/(quiz)$/i',               "$1zes"   ),
			array( '/^(ox)$/i',                "$1en"    ),
			array( '/([m|l])ouse$/i',          "$1ice"   ),
			array( '/(matr|vert|ind)ix|ex$/i', "$1ices"  ),
			array( '/(x|ch|ss|sh)$/i',         "$1es"    ),
			array( '/([^aeiouy]|qu)y$/i',      "$1ies"   ),
			array( '/([^aeiouy]|qu)ies$/i',    "$1y"     ),
			array( '/(hive)$/i',               "$1s"     ),
			array( '/(?:([^f])fe|([lr])f)$/i', "$1$2ves" ),
			array( '/sis$/i',                  "ses"     ),
			array( '/([ti])um$/i',             "$1a"     ),
			array( '/(buffal|tomat)o$/i',      "$1oes"   ),
			array( '/(bu)s$/i',                "$1ses"   ),
			array( '/(alias|status)$/i',       "$1es"    ),
			array( '/(octop|vir)us$/i',        "$1i"     ),
			array( '/(ax|test)is$/i',          "$1es"    ),
			array( '/s$/i',                    "s"       ),
			array( '/$/',                      "s"       )
		);

		$irregular = array(
			array( 'move',   'moves'    ),
			array( 'sex',    'sexes'    ),
			array( 'child',  'children' ),
			array( 'man',    'men'      ),
			array( 'person', 'people'   )
		);

		$uncountable = array(
			'sheep',
			'fish',
			'series',
			'species',
			'money',
			'rice',
			'information',
			'equipment'
		);

		// Save time if string in uncountable
		if ( in_array( strtolower( $string ), $uncountable ) ) :
			return apply_filters( 'wpappsdev_pluralize', $string );
		endif;

		// Check for irregular words
		foreach ( $irregular as $noun ) :
			if ( strtolower( $string ) == $noun[0] ) :
				return apply_filters( 'wpappsdev_pluralize', $noun[1] );
			endif;
		endforeach;

		// Check for plural forms
		foreach ( $plural as $pattern ) :
			if ( preg_match( $pattern[0], $string ) ) :
				return apply_filters( 'wpappsdev_pluralize', preg_replace( $pattern[0], $pattern[1], $string ) );
			endif;
		endforeach;

		// Return if noting found
		return apply_filters( 'wpappsdev_pluralize', $string );
	}
}