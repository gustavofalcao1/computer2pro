<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	$value = $meta != '' ? intval( $meta ) : '0';
		echo '<div id="' . esc_attr( $id ) . '-slider"></div>
				<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $value . '" size="5" />
				<br />' . $desc;