<?php //wpadpcbu_print( $pcbu_settings );?>
<div class="options_group">
	<?php if ( is_array( $pcbu_settings ) && ! empty( $pcbu_settings ) ) {?>
		<p class="form-field wpadpcbu_component_field">
			<label for="wpadpcbu_component"><?php echo esc_attr__( 'PC Component', 'wpappsdev-pcbuilder' ); ?></label>
			<select id="wpadpcbu_component" name="pcbu_settings[pcbucomp]" class="select short">
				<option value="-1"><?php echo esc_attr__( 'Select Component', 'wpappsdev-pcbuilder' ); ?></option>
				<?php echo wp_kses( generating_select_options( get_tax_terms_list( 'pcbucomp' ), 'term_id', 'name', $pcbu_settings['pcbucomp'] ), wpadpcbu_allowed_html() ); ?>
			</select>
		</p>
		<div id="wpadpcbu_filters_div">
		<?php
			if ( is_array( $filter_items ) ) {
				foreach ( $filter_items as $item ) {
					$taxonomy = 'cf-' . $item['filter_slug'];
					$name     = $item['filter_name'];
					$options  = generating_select_options( get_tax_terms_list( $taxonomy ), 'term_id', 'name', $filters[$taxonomy] );

					echo sprintf( '<p class="form-field wpadpcbu_%1$s_field"><label for="wpadpcbu_%1$s"">%2$s</label><select id="wpadpcbu_%1$s" name="pcbu_settings[filters][%1$s]" class="select short"><option value="-1">Select %2$s</option>%3$s</select></p>', esc_attr( $taxonomy ), esc_attr( $name ), wp_kses( $options, wpadpcbu_allowed_html() ) );
				}
			}
		?>
		</div>
	<?php } else { ?>
		<p class="form-field wpadpcbu_component_field">
			<label for="wpadpcbu_component"><?php echo esc_attr__( 'PC Component', 'wpappsdev-pcbuilder' ); ?></label>
			<select id="wpadpcbu_component" name="pcbu_settings[pcbucomp]" class="select short">
				<option value="-1"><?php echo esc_attr__( 'Select Component', 'wpappsdev-pcbuilder' ); ?></option>
				<?php echo wp_kses( generating_select_options( get_tax_terms_list( 'pcbucomp' ), 'term_id', 'name', '' ), wpadpcbu_allowed_html() ); ?>
			</select>
		</p>
		<div id="wpadpcbu_filters_div"></div>
	<?php } ?>
	<input type="hidden" name="save_pcbu_settings">
</div>

<style>
	#side-sortables .form-field label{
		display: block;
		width: 100%;
		margin-bottom: 5px;
		font-size: 15px;
		font-weight: 600;
	}
	#side-sortables .form-field select{
		display: block;
		width: 100%;
	}
</style>
