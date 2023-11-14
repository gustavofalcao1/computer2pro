<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	$meta = $meta ? $meta : '#';
	echo '<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $meta . '" size="10" />
		<br />' . $desc;
	echo '<div id="colorpicker-' . esc_attr( $id ) . '"></div>
		<script type="text/javascript">
		jQuery(function(jQuery) {
			jQuery("#colorpicker-' . esc_attr( $id ) . '").hide();
			jQuery("#colorpicker-' . esc_attr( $id ) . '").farbtastic("#' . esc_attr( $id ) . '");
			jQuery("#' . esc_attr( $id ) . '").bind("blur", function() { jQuery("#colorpicker-' . esc_attr( $id ) . '").slideToggle(); } );
			jQuery("#' . esc_attr( $id ) . '").bind("focus", function() { jQuery("#colorpicker-' . esc_attr( $id ) . '").slideToggle(); } );
		});
		</script>';