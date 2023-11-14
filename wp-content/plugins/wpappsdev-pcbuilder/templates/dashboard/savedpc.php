<?php defined( 'ABSPATH' ) || exit; ?>

<?php if ( $has_pcs ) { ?>

	<table class="woocommerce-savedpcs-table woocommerce-MyAccount-savedpcs shop_table shop_table_responsive my_account_savedpcs account-savedpcs-table">
		<thead>
			<tr>
				<?php foreach ( $columns as $column_id => $column_name ) { ?>
					<th class="woocommerce-savedpcs-table__header woocommerce-savedpcs-table__header-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php } ?>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $saved_pcs as $saved_pc ) { ?>
				<tr class="woocommerce-savedpcs-table__row order">
					<?php foreach ( $columns as $column_id => $column_name ) { ?>
						<td class="woocommerce-savedpcs-table__cell woocommerce-savedpcs-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php if ( 'savepc-id' === $column_id ) { ?>
								<a href="<?php echo esc_url( wc_get_endpoint_url( 'view-savedpc', $saved_pc->id ) ); ?>">
									<?php echo esc_html( _x( '#', 'hash before saved pc number', 'wpappsdev-pcbuilder' ) . $saved_pc->id ); ?>
								</a>

							<?php } elseif ( 'savepc-date' === $column_id ) { ?>
								<time><?php echo date( get_option( 'date_format', 'F j, Y' ), strtotime( $saved_pc->created_at ) ); ?></time>

							<?php } elseif ( 'savepc-total' === $column_id ) { ?>
								<?php echo wc_price( wpadpcbu_process()->savedpc->pc_total( $saved_pc->configurations ) ); ?>

							<?php } elseif ( 'savepc-actions' === $column_id ) { ?>
								<?php echo '<a href="' . esc_url( wc_get_endpoint_url( 'view-savedpc', $saved_pc->id ) ) . '" class="pc-builder-button" title="' . esc_html__( 'View', 'wpappsdev-pcbuilder' ) . '"><i class="dashicons dashicons-visibility"></i></a>'; ?>
								<?php echo '<a class="wpadpcbu-remove-savedpc pc-builder-button" data-id = "' . (int) $saved_pc->id . '" href="#" title="' . esc_html__( 'Remove', 'wpappsdev-pcbuilder' ) . '"><i class="dashicons dashicons-trash"></i></a>'; ?>
								<?php do_action( 'wpadpcbu_pc_configuration_row_action', $saved_pc ); ?>
							<?php } else {?>
								<?php do_action( 'wpadpcbu_pc_configuration_column_data', $column_id, $saved_pc ); ?>
							<?php } ?>
						</td>
					<?php } ?>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if ( 1 < $max_pages ) { ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) { ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'savedpcs', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'wpappsdev-pcbuilder' ); ?></a>
			<?php } ?>

			<?php if ( intval( $max_pages ) !== $current_page ) { ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'savedpcs', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'wpappsdev-pcbuilder' ); ?></a>
			<?php } ?>
		</div>
	<?php } ?>

<?php } else { ?>
	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<?php esc_html_e( 'No saved PC configuration found.', 'wpappsdev-pcbuilder' ); ?>
	</div>
<?php } ?>
