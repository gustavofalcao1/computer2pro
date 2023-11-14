<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;
	?>
	<label>
		<div class="gallery-screenshot clearfix tj-row">
			<?php
				$ids = explode(',', $meta);
				foreach ( $ids as $attachment_id ) {
					$img = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
					echo '<div class="screen-thumb tj-col-3"><img src="' . esc_url( $img[0]) . '" /></div>';
				}
			?>
		</div>

		<input id="edit-gallery" class="button upload_gallery_button" type="button" value="<?php esc_html_e('Add/Edit Gallery') ?>"/>
		<input id="clear-gallery" class="button upload_gallery_button" type="button" value="<?php esc_html_e('Clear') ?>"/>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" class="gallery_values" value="<?php echo esc_attr( $meta ); ?>">
	</label>