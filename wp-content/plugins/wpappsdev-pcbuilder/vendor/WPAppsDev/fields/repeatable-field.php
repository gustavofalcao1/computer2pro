<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	echo '<table id="' . esc_attr( $id ) . '-repeatable" class="meta_box_repeatable" cellspacing="0">
		<thead>
			<tr>
				<th><span class="sort_label"></span></th>
				<th>Fields</th>
				<th><a class="meta_box_repeatable_add" href="#"></a></th>
			</tr>
		</thead>
		<tbody>';
	$i = 0;
	// create an empty array
	if ( $meta == '' || $meta == array() ) {
		$keys = wp_list_pluck( $repeatable_fields, 'id' );
		$meta = array ( array_fill_keys( $keys, null ) );
	}
	$meta = array_values( $meta );
// echo '<pre>'; print_r( $field ); echo '</pre>';
// echo '<pre>'; print_r( $meta ); echo '</pre>';
// echo '<pre>'; print_r( $repeatable_fields ); echo '</pre>';
// echo '<pre>'; print_r( $keys ); echo '</pre>';
	foreach( $meta as $row ) {
		echo '<tr>
				<td><span class="sort hndle"></span></td><td>';
		foreach ( $repeatable_fields as $repeatable_field ) {
			if ( ! array_key_exists( $repeatable_field['id'], $meta[$i] ) )
				$meta[$i][$repeatable_field['id']] = null;
			echo '<label>' . $repeatable_field['label']  . '</label><p>';
			echo self::custom_meta_box_field( $repeatable_field, $meta[$i][$repeatable_field['id']], array( $id, $i ) );
			echo '</p>';
		} // end each field
		echo '</td><td><a class="meta_box_repeatable_remove" href="#"></a></td></tr>';
		$i++;
	} // end each row
	echo '</tbody>';
	echo '
		<tfoot>
			<tr>
				<th><span class="sort_label"></span></th>
				<th>Fields</th>
				<th><a class="meta_box_repeatable_add" href="#"></a></th>
			</tr>
		</tfoot>';
	echo '</table>
		' . $desc;