<label for="wpadpcbu-component-serial"><?php esc_html_e( 'Component Serial', 'wpappsdev-pcbuilder' ); ?></label>
<div class="form-field term-serial-wrap">
	<input name="wpadpcbu_component_serial" id="wpadpcbu-component-serial" type="number" value="0" min='0' aria-required="true">
	<p><?php esc_html_e( 'Input component serial number.', 'wpappsdev-pcbuilder' ); ?></p>
</div>

<label for="meta_desc"><?php esc_html_e( 'Required Component', 'wpappsdev-pcbuilder' ); ?></label>
<div class="form-field term-filters-wrap">
	<select name="wpadpcbu_required_component" id="wpadpcbu-required-component" class="select-item">
		<option value="no"><?php esc_html_e( 'No', 'wpappsdev-pcbuilder' ); ?></option>
		<option value="yes"><?php esc_html_e( 'Yes', 'wpappsdev-pcbuilder' ); ?></option>
	</select>
</div>

<label for="meta_desc"><?php esc_html_e( 'Assign Component Filters Group', 'wpappsdev-pcbuilder' ); ?></label>
<div class="form-field term-filters-wrap">
	<select name="wpadpcbu_component_filters_group" id="wpadpcbu-component-filters-group" class="select-item">
		<option value="-1"><?php esc_html_e( 'Select Filters Group', 'wpappsdev-pcbuilder' ); ?></option>
		<?php $cf_groups = get_component_filter(); ?>
		<?php echo sprintf( '%s', wp_kses( generating_select_options( $cf_groups, 'ID', 'post_title', '' ), wpadpcbu_allowed_html() ) ); ?>
	</select>
</div>

<label for="component-image-id"><?php _e( 'Component Icon', 'wpappsdev-pcbuilder' ); ?></label>
<div class="form-field term-image-wrap">
	<input type="hidden" id="component-image-id" name="component-image-id" class="custom_media_url" value="">
	<div id="component-image-wrapper"></div>
	<p>
		<input type="button" class="button button-secondary component_media_button" id="component_media_button" name="component_media_button" value="<?php _e( 'Add Image', 'wpappsdev-pcbuilder' ); ?>" />
		<input type="button" class="button button-secondary component_media_remove hidden" id="component_media_remove" name="component_media_remove" value="<?php _e( 'Remove Image', 'wpappsdev-pcbuilder' ); ?>" />
	</p>
</div>
