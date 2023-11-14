<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;
	echo '<input type="' . $type . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . sanitize_email( $meta ) . '" class="regular-text" size="30" />
			<br />' . $desc;