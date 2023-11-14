<div class="updated" id="installer-notice" style="padding: 1em; position: relative;">
	<h2><?php _e( 'Required plugin notice for WPAppsDev - PcBuilder', 'wpappsdev-pcbuilder' ); ?></h2>
	<?php if ( file_exists( WP_PLUGIN_DIR . '/' . $core_plugin_file ) && is_plugin_inactive( 'woocommerce/woocommerce.php' ) ) { ?>
		<p><?php echo sprintf( __( 'You just need to activate the %s to make it functional.', 'wpappsdev-pcbuilder' ), '<strong>WooCommerce</strong>' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $core_plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $core_plugin_file ); ?>"  title="<?php _e( 'Activate this plugin', 'wpappsdev-pcbuilder' ); ?>"><?php _e( 'Activate', 'wpappsdev-pcbuilder' ); ?></a>
		</p>
	<?php } else { ?>
		<p><?php echo sprintf( __( 'You just need to install & active the %1$sWooCommerce%2$s to make it functional.', 'wpappsdev-pcbuilder' ), '<a target="_blank" href="https://wordpress.org/plugins/woocommerce/">', '</a>' ); ?></p>
	<?php } ?>
</div>
