<?php foreach ( $products as $product ) { ?>
<div class="col-md-12">
	<div id="component-product-<?php echo esc_attr( $product->get_id() ); ?>" class="component-product">
		<div class="product-img">
			<a target="_blank" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
				<?php echo wp_kses( get_the_post_thumbnail( $product->get_id(), [150, 150] ), wpadpcbu_allowed_html() ); ?>
			</a>
		</div>
		<div class="product-info">
			<div class="product-info-top">
				<div class="price">
					<span><?php echo wc_clean( wc_price( $product->get_price() ) ); ?></span>
				</div>
				<div class="wpadpcbu-actions">
					<a class="choose" data-componentid="<?php echo esc_attr( $pcbucomp ); ?>" data-productid="<?php echo esc_attr( $product->get_id() ); ?>" href="#"><i class="dashicons dashicons-insert"></i></a>
				</div>
			</div>
			<div class="product-content-blcok">
				<h4 class="product-name">
					<a target="_blank" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php echo esc_attr( $product->get_name() ); ?></a>
				</h4>
				<div class="product-features">
					<?php
						$features    = get_post_meta( $product->get_id(), 'wpadpcbu_product_features_list', true );
						$chunk_items = ceil( count( $features ) / 2 );

						if ( ! wpadpcbu_is_repeatable_empty( $features ) ) {
							foreach ( array_chunk( $features, $chunk_items ) as $items ) {
								echo '<ul>';

								foreach ( $items as $item ) {
									echo sprintf( '<li>%s</li>', esc_attr( $item['feature'] ) );
								}
								echo '</ul>';
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
