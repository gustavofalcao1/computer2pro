<tr class="form-field">
	<th scope="row" valign="top"><label for="meta_desc"><?php esc_html_e( 'Component Serial', 'wpappsdev-pcbuilder' ); ?></label></th>
	<td>
		<div class="form-field term-filters-wrap">
			<div class="form-field term-serial-wrap">
				<input name="wpadpcbu_component_serial" id="wpadpcbu-component-serial" type="number" value="<?php echo (int) $serial; ?>" min='0' aria-required="true">
				<p><?php _e( 'Input component serial number.', 'wpappsdev-pcbuilder' ); ?></p>
			</div>
		</div>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="meta_desc"><?php esc_html_e( 'Required Component', 'wpappsdev-pcbuilder' ); ?></label></th>
	<td>
		<div class="form-field term-filters-wrap">
			<select name="wpadpcbu_required_component" id="wpadpcbu-required-component" class="select-item">
				<option <?php echo ( 'no' === $required ) ? 'selected="selected"' : ''; ?>  value="no"><?php esc_html_e( 'No', 'wpappsdev-pcbuilder' ); ?></option>
				<option <?php echo ( 'yes' === $required ) ? 'selected="selected"' : ''; ?> value="yes"><?php esc_html_e( 'Yes', 'wpappsdev-pcbuilder' ); ?></option>
			</select>
		</div>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="meta_desc"><?php esc_html_e( 'Assign Attribute Group', 'wpappsdev-pcbuilder' ); ?></label></th>
	<td>
		<div class="form-field term-filters-wrap">
			<select name="wpadpcbu_component_filters_group" id="wpadpcbu_component_filters_group" class="select-item">
				<option value="-1"><?php echo esc_attr__( 'Select Filters Group', 'wpappsdev-pcbuilder' ); ?></option>
				<?php $cf_groups = get_component_filter(); ?>
				<?php echo sprintf( '%s', wp_kses( generating_select_options( $cf_groups, 'ID', 'post_title', $filters_group ), wpadpcbu_allowed_html() ) ); ?>
			</select>
		</div>
	</td>
</tr>
<tr class="form-field term-image-wrap">
	<th scope="row">
		<label for="component-image-id"><?php _e( 'Component Icon', 'wpappsdev-pcbuilder' ); ?></label>
	</th>
	<td>
		<input type="hidden" id="component-image-id" name="component-image-id" value="<?php echo (int) $image_id; ?>">
		<div id="component-image-wrapper">
			<?php if ( $image_id ) { ?>
				<?php echo wp_kses( wp_get_attachment_image( $image_id, 'thumbnail' ), wpadpcbu_allowed_html() ); ?>
			<?php } ?>
		</div>
		<p>
			<input type="button" class="button button-secondary component_media_button" id="component_media_button" name="component_media_button" value="<?php _e( 'Add Image', 'wpappsdev-pcbuilder' ); ?>" />
			<input type="button" class="button button-secondary component_media_remove" id="component_media_remove" name="component_media_remove" value="<?php _e( 'Remove Image', 'wpappsdev-pcbuilder' ); ?>" />
		</p>
	</td>
</tr>
