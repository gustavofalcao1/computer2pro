<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	$iconClass = 'meta_box_file';
	if ( $meta ) $iconClass .= ' checked';
	echo	'<div class="meta_box_file_stuff"><input name="' . esc_attr( $name ) . '" type="hidden" class="meta_box_upload_file" value="' . esc_url( $meta ) . '" />
				<span class="' . $iconClass . '"></span>
				<span class="meta_box_filename">' . esc_url( $meta ) . '</span>
					<a href="#" class="meta_box_upload_image_button button" rel="' . get_the_ID() . '">Choose File</a>
					<small>&nbsp;<a href="#" class="meta_box_clear_file_button">Remove File</a></small></div>
					<br clear="all" />' . $desc;