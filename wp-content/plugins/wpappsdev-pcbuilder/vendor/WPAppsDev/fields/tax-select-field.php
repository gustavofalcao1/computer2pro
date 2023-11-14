<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '">
			<option value="">Select One</option>'; // Select One
	$terms = get_terms( $id, 'get=all' );
	$post_terms = wp_get_object_terms( get_the_ID(), $id );
	$taxonomy = get_taxonomy( $id );
	$selected = $post_terms ? $taxonomy->hierarchical ? $post_terms[0]->term_id : $post_terms[0]->slug : null;
	foreach ( $terms as $term ) {
		$term_value = $taxonomy->hierarchical ? $term->term_id : $term->slug;
		echo '<option value="' . $term_value . '"' . selected( $selected, $term_value, false ) . '>' . $term->name . '</option>';
	}
	echo '</select> &nbsp;<span class="description"><a href="'. esc_url(home_url()) . '/wp-admin/edit-tags.php?taxonomy=' . $id . '">Manage ' . $taxonomy->label . '</a></span>
		<br />' . $desc;