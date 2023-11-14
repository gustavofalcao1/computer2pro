<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	//areas
	$post_type_object = get_post_type_object( $post_type );
	echo '<p>' . $desc . ' &nbsp;<span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $post_type . '">Manage ' . $post_type_object->label ) . '</a></span></p><div class="post_drop_sort_areas">';
	foreach ( $areas as $area ) {
		echo '<ul id="area-' . $area['id']  . '" class="sort_list">
				<li class="post_drop_sort_area_name">' . $area['label'] . '</li>';
				if ( is_array( $meta ) ) {
					$items = explode( ',', $meta[$area['id']] );
					foreach ( $items as $item ) {
						$output = $display == 'thumbnail' ? get_the_post_thumbnail( $item, array( 204, 30 ) ) : get_the_title( $item );
						echo '<li id="' . $item . '">' . $output . '</li>';
					}
				}
		echo '</ul>
			<input type="hidden" name="' . esc_attr( $name ) . '[' . $area['id'] . ']"
			class="store-area-' . $area['id'] . '"
			value="' , $meta ? $meta[$area['id']] : '' , '" />';
	}
	echo '</div>';
	// source
	$exclude = null;
	if ( !empty( $meta ) ) {
		$exclude = implode( ',', $meta ); // because each ID is in a unique key
		$exclude = explode( ',', $exclude ); // put all the ID's back into a single array
	}
	$posts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1, 'post__not_in' => $exclude ) );
	echo '<ul class="post_drop_sort_source sort_list">
			<li class="post_drop_sort_area_name">Available ' . $label . '</li>';
	foreach ( $posts as $item ) {
		$output = $display == 'thumbnail' ? get_the_post_thumbnail( $item->ID, array( 204, 30 ) ) : get_the_title( $item->ID );
		echo '<li id="' . $item->ID . '">' . $output . '</li>';
	}
	echo '</ul>';