<section class="wpadpcbu-pcbuilder alignwide">
	<?php if ( isset(  $component['name'] ) && wpadpcbu_process()->search->breadcrumb_enable() ) { ?>
		<div class="wpadpcbu-breadcrumb">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-5 col-md-5">
						<ul class="wpadpcbu-nav">
							<li><a href="<?php echo esc_url( site_url() ); ?>"><i class="dashicons dashicons-admin-multisite" title="Home"></i></a></li>
							<li><a class="bclink" data-page="builder" href="#"><?php echo esc_attr( wpadpcbu_process()->builder->menu_title() ); ?></a></li>
							<li><a class="bclink" data-page="search" href="#"><?php echo esc_attr( $component['name'] ); ?></a></li>
						</ul>
						<div class="clear"></div>
					</div>
					<div class="col-xs-12 col-sm-7 col-md-7">
						<p class="page-heading"><?php echo esc_attr( wpadpcbu_process()->search->search_title( $component['name'] ) ); ?></p>
					</div>
				</div>
			</div>
		</div>

	<?php } ?>

	<div class="wpadpcbu-component-search">
		<div class="container">
			<div class="row">
				<?php if ( ! empty( $components ) ) { ?>
					<div class="col-xs-12 col-sm-12 col-md-3 wpadpcbu-filter-div">
						<div class="filter-show-hide back-button-icon text-center">
							<button type="button" id="filter-hide" class="btn btn-primary"><?php _e( 'Hide Filters', 'wpappsdev-pcbuilder' ); ?></button>
							<button type="button" id="filter-show" class="btn btn-primary"><?php _e( 'Show filters', 'wpappsdev-pcbuilder' ); ?></button>
						</div>
						<div id="wpadpcbu-search-filter" class="wpadpcbu-filter-panel">
							<div class="filter-panel">
								<div class="price-filter">
									<div class="label"><span><?php _e( 'Price Range', 'wpappsdev-pcbuilder' ); ?></span></div>
									<div class="extra-controls">
										<input type="text" class="wpadpcbu-price-start" value="0" />
										<input type="text" class="wpadpcbu-price-end" value="0" />
									</div>
									<div class="range-slider"><input type="text" class="wpadpcbu-price-range" value=""/></div>
								</div>
								<?php if ( ! empty( $filters ) ) { ?>
									<?php foreach ( $filters as $key => $filter ) { ?>
										<div id="<?php echo esc_attr( "fgp-{$key}" ); ?>" class="filter-group show">
											<div class="label">
												<span><?php echo esc_attr( $filter['name'] ); ?></span>
												<i class="toggler fa" data-group="<?php echo esc_attr( $key ); ?>" ></i>
											</div>
											<div id="<?php echo esc_attr( "fg-{$key}" ); ?>" class="items">
												<?php if ( isset( $filter['items'] ) && ! empty( $filter['items'] ) ) { ?>
													<?php foreach ( $filter['items'] as $item ) { ?>
														<label class="filter">
														<input type="checkbox" id="filter-data" data-tax="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $item['term_id'] ); ?>"/>
														<span><?php echo esc_attr( $item['name'] ); ?></span>
														</label>
													<?php } ?>
												<?php } ?>
											</div>
										</div>
									<?php }?>
								<?php }?>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-9 wpadpcbu-content-div">
						<div id="wpadpcbu-search-content" class="wpadpcbu-content-panel">
							<?php if ( wpadpcbu_process()->search->top_filter_enable() ) { ?>
								<div class="wpadpcbu-top-bar">
									<div class="row">
										<div class="col-xs-12 col-md-6">
											<div class="left-search">
												<div class="back-button-icon">
													<a class="back-button" href="#"><span><i class="dashicons dashicons-arrow-left-alt2"></i></span></a>
												</div>
												<div class="input-group-search">
													<input type="text" name="search" value="" id="input-search" placeholder="Search" class="form-control input-lg input-search" autocomplete="off">
												</div>
											</div>
										</div>
										<div class="col-xs-12 col-md-6">
											<div class="right-shortby">
												<label class="control-label" for="input-sort">
													<span><?php _e( 'Sort By:', 'wpappsdev-pcbuilder' ); ?></span>
													<select id="input-sort" class="form-control">
														<option value="titleasc" selected="selected"><?php _e( 'Name (A - Z)', 'wpappsdev-pcbuilder' ); ?></option>
														<option value="titledesc"><?php _e( 'Name (Z - A)', 'wpappsdev-pcbuilder' ); ?></option>
														<option value="priceasc"><?php _e( 'Price (Low &gt; High)', 'wpappsdev-pcbuilder' ); ?></option>
														<option value="pricedesc"><?php _e( 'Price (High &gt; Low)', 'wpappsdev-pcbuilder' ); ?></option>
													</select>
												</label>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
							<div class= "wpadpcbu-component-products" style="min-height: 500px;">
								<div id="wpadpcbu-products-row" class="row"></div>
							</div>
							<div class="wpadpcbu-pagination">
								<div class="row">
									<div class="col-sm-2 text-left">
										<a id="previous-btn" class="pagination-button" data-pagination="prev" href="#"><?php _e( 'PREV', 'wpappsdev-pcbuilder' ); ?></a>
									</div>
									<div class="col-sm-8 text-center page-info">
										<?php
											$start = ( $query['current_page'] - 1 ) * $query['per_page'] + 1;

											if ( $query['total_pages'] == 1 ) {
												$end = $query['total_items'];
											} else {
												$end = $query['current_page'] * $query['per_page'];
											}
										?>
										<p><?php _e( 'Total Products:', 'wpappsdev-pcbuilder' ); ?> <span class="total-products">0</span> <?php _e( 'Total Pages:', 'wpappsdev-pcbuilder' ); ?> <span class="total-pages">0</span> <?php _e( 'Current Page:', 'wpappsdev-pcbuilder' ); ?> <span class="current-page">0</span></p>
										<input type="hidden" name="total_items" value="0">
										<input type="hidden" name="total_pages" value="0">
										<input type="hidden" name="per_page" value="0">
										<input type="hidden" name="current_page" value="0">
									</div>
									<div class="col-sm-2 text-right">
										<a id="next-btn" class="pagination-button" data-pagination="next" href="#"><?php _e( 'NEXT', 'wpappsdev-pcbuilder' ); ?></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div></div>
				<?php }?>
			</div>
		</div>
	</div>
</section>
