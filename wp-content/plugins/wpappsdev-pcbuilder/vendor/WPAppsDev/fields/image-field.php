<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	$image = str_replace( '/fields', '', plugin_dir_url( __FILE__ ) ) . '/assets/images/image.png';
	echo '<div class="meta_box_image"><span class="meta_box_default_image" style="display:none">' . $image . '</span>';
	if ( $meta ) {
		$image = wp_get_attachment_image_src( intval( $meta ), 'medium' );
		$image = $image[0];
	}
	echo	'<input name="' . esc_attr( $name ) . '" type="hidden" class="meta_box_upload_image" value="' . intval( $meta ) . '" />
				<img src="' . esc_attr( $image ) . '" class="meta_box_preview_image" alt="" />
					<a href="#" class="meta_box_upload_image_button button" rel="' . get_the_ID() . '">Choose Image</a>
					<small>&nbsp;<a href="#" class="meta_box_clear_image_button">Remove Image</a></small></div>
					<br clear="all" />' . $desc;