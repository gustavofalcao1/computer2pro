<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

	$terms = get_terms( $id, 'get=all' );
	$post_terms = wp_get_object_terms( get_the_ID(), $id );
	$taxonomy = get_taxonomy( $id );
	$checked = $post_terms ? $taxonomy->hierarchical ? $post_terms[0]->term_id : $post_terms[0]->slug : null;
	foreach ( $terms as $term ) {
		$term_value = $taxonomy->hierarchical ? $term->term_id : $term->slug;
		echo '<input type="checkbox" value="' . $term_value . '" name="' . $id . '[]" id="term-' . $term_value . '"' . checked( $checked, $term_value, false ) . ' /> <label for="term-' . $term_value . '">' . $term->name . '</label><br />';
	}
	echo '<span class="description">' . $field['desc'] . ' <a href="'. esc_url(home_url()) . '/wp-admin/edit-tags.php?taxonomy=' . $id . '&post_type=' . $page . '">Manage ' . $taxonomy->label . '</a></span>';