<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	echo wp_editor( $meta, $id, $settings ) . '<br />' . $desc;