<?php
namespace WPAppsDev;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

/**
 * Takes in a few peices of data and creates a custom meta box
 *
 * @param	string			$id			meta box id
 * @param	string			$title		title
 * @param	array			$fields		array of each field the box should include
 * @param	string|array	$page		post type to add meta box to
 */

class WpadMetaBox  {

	public $id;
	public $title;
	public $fields;
	public $page;
	public $context;
	public $priority;

	public function __construct( $id, $title, $fields, $page, $context = 'normal', $priority = 'high' ) {
		$this->id = $id;
		$this->title = $title;
		$this->fields = $fields;
		$this->page = $page;
		$this->context = $context;
		$this->priority = $priority;

		if( ! is_array( $this->page ) )
			$this->page = array( $this->page );

		//$this->page = $this->page ;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_head',  array( $this, 'admin_head' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post',  array( $this, 'save_box' ));
	}

	/**
	 * enqueue necessary scripts and styles
	 */
	function admin_enqueue_scripts() {
		global $pagenow;
		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && in_array( get_post_type(), $this->page ) ) {
			// js
			$deps = array();
			if ( self::meta_box_find_field_type( 'date', $this->fields ) )
				$deps[] = 'jquery-ui-datepicker';
			if ( self::meta_box_find_field_type( 'slider', $this->fields ) )
				$deps[] = 'jquery-ui-slider';
			if ( self::meta_box_find_field_type( 'color', $this->fields ) )
				$deps[] = 'farbtastic';
			if ( in_array( true, array(
				self::meta_box_find_field_type( 'chosen', $this->fields ),
				self::meta_box_find_field_type( 'post_chosen', $this->fields )
			) ) ) {
				wp_register_script( 'chosen', plugin_dir_url( __FILE__ ) . '/assets/js/chosen.js', array( 'jquery' ) );
				$deps[] = 'chosen';
				wp_enqueue_style( 'chosen', plugin_dir_url( __FILE__ ) . '/assets/css/chosen.css' );
			}
			if ( in_array( true, array(
				self::meta_box_find_field_type( 'date', $this->fields ),
				self::meta_box_find_field_type( 'slider', $this->fields ),
				self::meta_box_find_field_type( 'color', $this->fields ),
				self::meta_box_find_field_type( 'chosen', $this->fields ),
				self::meta_box_find_field_type( 'post_chosen', $this->fields ),
				self::meta_box_find_repeatable( 'repeatable', $this->fields ),
				self::meta_box_find_field_type( 'image', $this->fields ),
				self::meta_box_find_field_type( 'multiimage', $this->fields ),
				self::meta_box_find_field_type( 'file', $this->fields )
			) ) ):
				wp_enqueue_script( 'meta_box', plugin_dir_url( __FILE__ ) . '/assets/js/scripts.js', $deps );
				wp_enqueue_script( 'jquery-ui-core' );
			endif;

			// css
			$deps = array();
			wp_register_style( 'jqueryui', plugin_dir_url( __FILE__ ) . '/assets/css/jqueryui.css' );
			if ( self::meta_box_find_field_type( 'date', $this->fields ) || self::meta_box_find_field_type( 'slider', $this->fields ) )
				$deps[] = 'jqueryui';
			if ( self::meta_box_find_field_type( 'color', $this->fields ) )
				$deps[] = 'farbtastic';
			wp_enqueue_style( 'meta_box', plugin_dir_url( __FILE__ ) . '/assets/css/meta_box.css', $deps );
			wp_enqueue_media();
			wp_enqueue_script( 'meta_box_nap', plugin_dir_url( __FILE__ ) . '/assets/js/nap.js');
		}
	}

	/**
	 * adds scripts to the head for special fields with extra js requirements
	 */
	function admin_head() {
		global $pagenow;
		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && in_array( get_post_type(), $this->page ) && ( self::meta_box_find_field_type( 'date', $this->fields ) || self::meta_box_find_field_type( 'slider', $this->fields ) ) ) {

			echo '<script type="text/javascript">
						jQuery(function( $) {';

			foreach ( $this->fields as $field ) {
				switch( $field['type'] ) {
					// date
					case 'date' :
						echo '$("#' . $field['id'] . '").datepicker({
								dateFormat: \'yy-mm-dd\'
							});';
					break;
					// slider
					case 'slider' :
					$value = get_post_meta( get_the_ID(), $field['id'], true );
					if ( $value == '' )
						$value = $field['min'];
					echo '
							$( "#' . $field['id'] . '-slider" ).slider({
								value: ' . $value . ',
								min: ' . $field['min'] . ',
								max: ' . $field['max'] . ',
								step: ' . $field['step'] . ',
								slide: function( event, ui ) {
									$( "#' . $field['id'] . '" ).val( ui.value );
								}
							});';
					break;
				}
			}

			echo '});
				</script>';

		}
	}

	/**
	 * adds the meta box for every post type in $page
	 */
	function add_box() {
		foreach ( $this->page as $page ) {
			add_meta_box( $this->id, $this->title, array( $this, 'meta_box_callback' ), $page, $this->context, $this->priority );
		}
	}

	/**
	 * outputs the meta box
	 */
	function meta_box_callback() {
		// Use nonce for verification
		wp_nonce_field( 'custom_meta_box_nonce_action', 'custom_meta_box_nonce_field' );

		// Begin the field table and loop
		echo '<table class="form-table meta_box">';
		foreach ( $this->fields as $field) {
			if ( $field['type'] == 'section' ) {
				echo '<tr>
						<td colspan="2">
							<h2>' . $field['label'] . '</h2>
						</td>
					</tr>';
			}
			else {
				$repeatable = $field['type'] == 'repeatable' ? true : false;
				echo '<tr class="tj_'. $field['type'] .'">
						<th class="col-md-4"><label for="' . $field['id'] . '">' . $field['label'] . '</label></th>
						<td class="col-md-8">';

						$meta = get_post_meta( get_the_ID(), $field['id'], true);
						echo self::custom_meta_box_field( $field, $meta, $repeatable );

				echo     '</td>
					</tr>';
			}
		} // end foreach
		echo '</table>'; // end table
	}

	/**
	 * saves the captured data
	 */
	function save_box( $post_id ) {
		$post_type = get_post_type();

		// verify nonce
		if ( ! isset( $_POST['custom_meta_box_nonce_field'] ) )
			return $post_id;
		if ( ! ( in_array( $post_type, $this->page ) || wp_verify_nonce( $_POST['custom_meta_box_nonce_field'],  'custom_meta_box_nonce_action' ) ) )
			return $post_id;
		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		// check permissions
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;

		// loop through fields and save the data
		foreach ( $this->fields as $field ) {
			if( $field['type'] == 'section' ) {
				$sanitizer = null;
				continue;
			}
			if( in_array( $field['type'], array( 'tax_select', 'tax_checkboxes' ) ) ) {
				// save taxonomies
				if ( isset( $_POST[$field['id']] ) ) {
					$term = $_POST[$field['id']];
					wp_set_object_terms( $post_id, $term, $field['id'] );
				}
			}
			else {
				// save the rest
				$new = false;
				$old = get_post_meta( $post_id, $field['id'], true );
				if ( isset( $_POST[$field['id']] ) )
					$new = $_POST[$field['id']];
				if ( isset( $new ) && '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				} elseif ( isset( $new ) && $new != $old ) {
					$sanitizer = isset( $field['sanitizer'] ) ? $field['sanitizer'] : 'sanitize_text_field';
					if ( is_array( $new ) ) :
						$new = $this->meta_box_array_map_r( [ $this, 'meta_box_sanitize' ], $new, $sanitizer );
					else :
						$new = self::meta_box_sanitize( $new, $sanitizer );
					endif;
					update_post_meta( $post_id, $field['id'], $new );
				}
			}
		} // end foreach
	}

	/**
	 * recives data about a form field and spits out the proper html
	 *
	 * @param	array					$field			array with various bits of information about the field
	 * @param	string|int|bool|array	$meta			the saved data for this field
	 * @param	array					$repeatable		if is this for a repeatable field, contains parant id and the current integar
	 *
	 * @return	string									html for the field
	 */
	public static function custom_meta_box_field( $field, $meta = null, $repeatable = null ) {
		if ( ! ( $field || is_array( $field ) ) )
			return;
		//echo "<pre>"; print_r($field); echo "</pre>";
		// get field data
		$type = isset( $field['type'] ) ? $field['type'] : null;
		$label = isset( $field['label'] ) ? $field['label'] : null;
		$desc = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : null;
		$place = isset( $field['place'] ) ? $field['place'] : null;
		$size = isset( $field['size'] ) ? $field['size'] : null;
		$post_type = isset( $field['post_type'] ) ? $field['post_type'] : null;
		$options = isset( $field['options'] ) ? $field['options'] : null;
		$settings = isset( $field['settings'] ) ? $field['settings'] : null;
		$repeatable_fields = isset( $field['repeatable_fields'] ) ? $field['repeatable_fields'] : null;
		$readonly = isset( $field['readonly'] ) ? $field['readonly'] : '';
		// the id and name for each field
		$id = $name = isset( $field['id'] ) ? $field['id'] : null;
		if ( is_array( $repeatable ) ) {
			$name = $repeatable[0] . '[' . $repeatable[1] . '][' . $id .']';
			//$id = $repeatable[0] . '_' . $repeatable[1] . '_' . $id;
		}
		switch( $type ) {
			// basic
			case 'text':
			case 'tel':
			default:
				include __DIR__ . '/fields/text-field.php';
			break;
			case 'email':
				include __DIR__ . '/fields/email-field.php';
			break;
			case 'url':
				include __DIR__ . '/fields/url-field.php';
			break;
			case 'number':
				include __DIR__ . '/fields/number-field.php';
			break;
			// textarea
			case 'textarea':
				include __DIR__ . '/fields/textarea-field.php';
			break;
			// editor
			case 'editor':
				include __DIR__ . '/fields/editor-field.php';
			break;
			// checkbox
			case 'checkbox':
				include __DIR__ . '/fields/checkbox-field.php';
			break;
			// select, chosen
			case 'select':
			case 'chosen':
				include __DIR__ . '/fields/select-field.php';
			break;
			// radio
			case 'radio':
				include __DIR__ . '/fields/radio-field.php';
			break;
			// checkbox_group
			case 'checkbox_group':
				include __DIR__ . '/fields/checkbox-group-field.php';
			break;
			// color
			case 'color':
				include __DIR__ . '/fields/color-field.php';
			break;
			// post_select, post_chosen
			case 'post_select':
			case 'post_list':
			case 'post_chosen':
				include __DIR__ . '/fields/post-select-field.php';
			break;
			// post_checkboxes
			case 'post_checkboxes':
				include __DIR__ . '/fields/post-checkboxes-field.php';
			break;
			// post_drop_sort
			case 'post_drop_sort':
				include __DIR__ . '/fields/post-drop-sort-field.php';
			break;
			// tax_select
			case 'tax_select':
				include __DIR__ . '/fields/tax-select-field.php';
			break;
			// tax_checkboxes
			case 'tax_checkboxes':
				include __DIR__ . '/fields/tax-checkboxes-field.php';
			break;
			// date
			case 'date':
				include __DIR__ . '/fields/date-field.php';
			break;
			// slider
			case 'slider':
				include __DIR__ . '/fields/slider-field.php';
			break;
			// image
			case 'image':
				include __DIR__ . '/fields/image-field.php';
			break;
			// file
			case 'file':
				include __DIR__ . '/fields/file-field.php';
			break;
			// repeatable
			case 'repeatable':
				include __DIR__ . '/fields/repeatable-field.php';
			break;
			// mmultiimage-field
			case 'multiimage':
				include __DIR__ . '/fields/multiimage-field.php';
			break;
		} //end switch

	}
	/**
	 * Map a multideminsional array
	 *
	 * @param	string	$func		the function to map
	 * @param	array	$meta		a multidimensional array
	 * @param	array	$sanitizer	a matching multidimensional array of sanitizers
	 *
	 * @return	array				new array, fully mapped with the provided arrays
	 */
	function meta_box_array_map_r( $func, $meta, $sanitizer ) {

		$newMeta = array();
		$meta = array_values( $meta );

		foreach( $meta as $key => $array ) {
			if ( $array == '' )
				continue;
			/**
			 * some values are stored as array, we only want multidimensional ones
			 */
			if ( ! is_array( $array ) ) {
				return array_map( $func, $meta, (array)$sanitizer );
				break;
			}
			/**
			 * the sanitizer will have all of the fields, but the item may only
			 * have valeus for a few, remove the ones we don't have from the santizer
			 */
			$keys = array_keys( $array );
			$newSanitizer = $sanitizer;
			if ( is_array( $sanitizer ) ) {
				foreach( $newSanitizer as $sanitizerKey => $value )
					if ( ! in_array( $sanitizerKey, $keys ) )
						unset( $newSanitizer[$sanitizerKey] );
			}
			/**
			 * run the function as deep as the array goes
			 */
			foreach( $array as $arrayKey => $arrayValue )
				if ( is_array( $arrayValue ) )
					$array[$arrayKey] = $this->meta_box_array_map_r( $func, $arrayValue, $newSanitizer[$arrayKey] );

			$array = array_map( $func, $array, $newSanitizer );
			$newMeta[$key] = array_combine( $keys, array_values( $array ) );
		}
		return $newMeta;
	}

	/**
	 * Find repeatable
	 *
	 * This function does almost the same exact thing that the above function
	 * does, except we're exclusively looking for the repeatable field. The
	 * reason is that we need a way to look for other fields nested within a
	 * repeatable, but also need a way to stop at repeatable being true.
	 * Hopefully I'll find a better way to do this later.
	 *
	 * @param	string	$needle 	field type to look for
	 * @param	array	$haystack	an array to search the type in
	 *
	 * @return	bool				whether or not the type is in the provided array
	 */

	static function meta_box_find_repeatable( $needle = 'repeatable', $haystack ) {
		foreach ( $haystack as $h )
			if ( isset( $h['type'] ) && $h['type'] == $needle )
				return true;
		return false;
	}
	/**
	 * outputs properly sanitized data
	 *
	 * @param	string	$string		the string to run through a validation function
	 * @param	string	$function	the validation function
	 *
	 * @return						a validated string
	 */

	static function meta_box_sanitize( $string, $function = 'sanitize_text_field' ) {
		switch ( $function ) {
			case 'intval':
				return intval( $string );
			case 'absint':
				return absint( $string );
			case 'wp_kses_post':
				return wp_kses_post( $string );
			case 'wp_kses_data':
				return wp_kses_data( $string );
			case 'esc_url_raw':
				return esc_url_raw( $string );
			case 'is_email':
				return is_email( $string );
			case 'sanitize_title':
				return sanitize_title( $string );
			case 'santitize_boolean':
				return santitize_boolean( $string );
			case 'wpad_wp_kses':
				$allowed_html = [
					'em'     => [],
					'strong' => [],
					'br'     => [],
					'span'   => [],
				];
				return wp_kses( $string, $allowed_html );
			case 'sanitize_text_field':
			default:
				return sanitize_text_field( $string );
		}
	}

	/**
	 * sanitize boolean inputs
	 */
	function meta_box_santitize_boolean( $string ) {
		if ( ! isset( $string ) || $string != 1 || $string != true )
			return false;
		else
			return true;
	}

	/**
	 * Finds any item in any level of an array
	 *
	 * @param	string	$needle 	field type to look for
	 * @param	array	$haystack	an array to search the type in
	 *
	 * @return	bool				whether or not the type is in the provided array
	 */
	static function meta_box_find_field_type( $needle, $haystack ) {
		foreach ( $haystack as $h )
			if ( isset( $h['type'] ) && $h['type'] == 'repeatable' )
				return self::meta_box_find_field_type( $needle, $h['repeatable_fields'] );
			elseif ( ( isset( $h['type'] ) && $h['type'] == $needle ) || ( isset( $h['repeatable_type'] ) && $h['repeatable_type'] == $needle ) )
				return true;
		return false;
	}
}
