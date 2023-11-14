<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	echo '<ul class="meta_box_items">';
	foreach ( $options as $option )
		echo '<li><input type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '-' . $option['value'] . '" value="' . $option['value'] . '" ' . checked( $meta, $option['value'], false ) . ' />
				<label for="' . esc_attr( $id ) . '-' . $option['value'] . '">' . $option['label'] . '</label></li>';
	echo '</ul>' . $desc;