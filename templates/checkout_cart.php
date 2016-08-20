<?php
/**
 *  This template is used to display the Checkout page when items are in the cart
 */

global $post; ?>

<!--<div class="col-xs-12 no-padding">
	<div class="table-header col-xs-12 no-padding hidden-xs hidden-sm">
		<div class="header-column col-xs-12 col-md-6 no-padding">
			Rate Plans
		</div>
		<div class="header-column col-xs-12 col-md-3 no-padding">
			Price
		</div>
		<div class="header-column col-xs-12 col-md-3 no-padding">
			Quantity
		</div>
		<!-- <div class="header-column col-xs-12 col-md-3 no-padding">
			Sub Total
		</div> 
	</div>
	<div class="table-body col-xs-12 no-padding">
		<?php $cart_items = edd_get_cart_contents(); ?>
		<?php do_action( 'edd_cart_items_before' ); ?>
		<?php if ( $cart_items ) : ?>
			<?php foreach ( $cart_items as $key => $item ) : ?>
				<div class="table-body-row col-xs-12 no-padding">
					<?php
						$item_title = $item['options']['name'];
						// $quantity = edd_get_cart_item_quantity( $item['id'], $item['options'] );
						$fromdatetime = strtotime($item['options']['startdate']);
						$todatetime = strtotime($item['options']['enddate']);
						$noofdays = $item['options']['noofdays'];
						$fromdatetime = date('Y-m-d', $fromdatetime);
						$todatetime = date('Y-m-d', $todatetime);
						$nights = "Nights";
						if($noofdays == 1){
							$nights = "Night";
						}
					?>
					<div class="header-column col-xs-12 col-md-6 no-padding">
						<?php
						echo '<span class="edd_checkout_cart_item_title">' . esc_html( $item_title ) . '</span></br><span class="condtion">(<span class="quantitynumber">'.$noofdays.'</span> '.$nights.', arriving <span class="arrivingDate">'.$fromdatetime.'</span>, departing <span class="departingDate">'.$todatetime.'</span>)</span>';
						do_action( 'edd_checkout_cart_item_title_after', $item );
						?>
					</div>
					<div class="header-column col-xs-12 col-md-3 no-padding">
						<?php
						echo '<span class="edd_checkout_cart_item_title">'.edd_cart_item_price( $item['id'], $item['options'] ).'</span>';
						do_action( 'edd_checkout_cart_item_price_after', $item );
						?>
					</div>
					<div class="header-column cart_quantity_item col-xs-12 col-md-3 no-padding" data-download-id="<?php echo esc_attr( $item['id'] ); ?>">
						<?php if( edd_item_quantities_enabled() ) : ?>
							<?php $availablequantity = $item['options']['availablequantity']; ?>
							<input type="number" min="1" max="<?php echo $availablequantity;?>" step="1" name="edd-cart-download-<?php echo $key; ?>-quantity" data-key="<?php echo $key; ?>" class="edd-input edd-item-quantity form-control qunatityinput" value="<?php echo edd_get_cart_item_quantity( $item['id'], $item['options'] ); ?>"/>
							<input type="hidden" name="edd-cart-downloads[]" value="<?php echo $item['id']; ?>"/>
							<input type="hidden" name="edd-cart-download-<?php echo $key; ?>-options" value="<?php echo esc_attr( serialize( $item['options'] ) ); ?>"/>
						<?php endif; ?>
						<?php do_action( 'edd_cart_actions', $item, $key ); ?>
						<a class="edd_cart_remove_item_btn btn btn-danger btn-small" href="<?php echo esc_url( edd_remove_item_url( $key ) ); ?>"><?php _e( 'x', 'easy-digital-downloads' ); ?></a>
					</div>
					<?php do_action( 'edd_cart_items_after' ); ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="table-footer col-xs-12 no-padding">
		<div class="col-xs-12 no-padding edd_cart_footer_row edd_cart_subtotal_row no-border"<?php if ( ! edd_is_cart_taxed() ) echo ' style=""'; ?>>
			<?php do_action( 'edd_checkout_table_subtotal_first' ); ?>
			<div class="header-column col-xs-12 col-md-3 no-padding pull-right edd_cart_subtotal">
				<?php _e( 'Total', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_subtotal_amount pull-right"><?php echo edd_cart_subtotal(); ?></span>
			</div>
			<?php do_action( 'edd_checkout_table_subtotal_last' ); ?>
		</div>

		<div class="col-xs-12 no-padding edd_cart_footer_row edd_cart_discount_row" <?php if( ! edd_cart_has_discounts() )  echo ' style="display:none;"'; ?>>
			<?php do_action( 'edd_checkout_table_discount_first' ); ?>
			<div class="header-column col-xs-12 col-md-3 no-padding pull-right edd_cart_discount">
				<?php edd_cart_discounts_html(); ?>
			</div>
			<?php do_action( 'edd_checkout_table_discount_last' ); ?>
		</div>

		<?php if( edd_use_taxes() ) : ?>
			<div class="col-xs-12 no-padding edd_cart_footer_row edd_cart_tax_row"<?php if( ! edd_is_cart_taxed() ) echo ' style="display:none;"'; ?>>
				<?php do_action( 'edd_checkout_table_tax_first' ); ?>
				<div class="header-column col-xs-12 col-md-3 no-padding pull-right edd_cart_tax">
					<?php _e( 'Tax', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_tax_amount  pull-right" data-tax="<?php echo edd_get_cart_tax( false ); ?>"><?php echo esc_html( edd_cart_tax() ); ?></span>
				</div>
				<?php do_action( 'edd_checkout_table_tax_last' ); ?>
			</div>

		<?php endif; ?>

		<div class="col-xs-12 no-padding edd_cart_footer_row">
			<?php do_action( 'edd_checkout_table_footer_first' ); ?>
				<div class="header-column col-xs-12 col-md-3 no-padding pull-right edd_cart_total">
						<?php _e( 'You Pay', 'easy-digital-downloads' ); ?>: <span class="edd_cart_amount  pull-right" data-subtotal="<?php echo edd_get_cart_total(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"><?php edd_cart_total(); ?></span>
				</div>
		</div>
		<?php do_action( 'edd_checkout_table_footer_last' ); ?>
		<div class="col-xs-12 no-padding margin-bottom-30">
			<textarea	class="form-control add_info" name="add_info" placeholder="Special Requests"></textarea>
		</div>
		<div class='col-xs-12 no-padding footer-buttons text-center margin-t10'>
			<a href="<?php echo get_site_url();?>/accommodations" class="btn btn-danger btn-md btn-addmorerooms">Add more Rooms</a>
			<a href="#" class="btn btn-danger btn-md btn-proceed margin-top-10-mobile">Proceed</a>
		</div>
	</div>
</div>-->
<table id="edd_checkout_cart" <?php if ( ! edd_is_ajax_disabled() ) { echo 'class="table hidden-xs hidden-sm hidden-md hidden-lg ajaxed"'; } ?>>
	 <thead>
		<tr class="edd_cart_header_row">
			<?php do_action( 'edd_checkout_table_header_first' ); ?>
			<th class="edd_cart_item_name"><?php _e( 'Rate Plans', 'easy-digital-downloads' ); ?></th>
			<th class="edd_cart_item_price"><?php _e( 'Price', 'easy-digital-downloads' ); ?></th>
			<th class="edd_cart_actions"><?php _e( 'Quantity', 'easy-digital-downloads' ); ?></th>
			<?php do_action( 'edd_checkout_table_header_last' ); ?>
		</tr>
	</thead>
	<tbody>
		<?php $cart_items = edd_get_cart_contents(); ?>
		<?php do_action( 'edd_cart_items_before' ); ?>
		<?php if ( $cart_items ) : ?>
			<?php foreach ( $cart_items as $key => $item ) : ?>
				<tr class="edd_cart_item" id="edd_cart_item_<?php echo esc_attr( $key ) . '_' . esc_attr( $item['id'] ); ?>" data-download-id="<?php echo esc_attr( $item['id'] ); ?>">
					<?php do_action( 'edd_checkout_table_body_first', $item ); ?>
					<td class="edd_cart_item_name">
						<?php
							if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $item['id'] ) ) {
								echo '<div class="edd_cart_item_image">';
									echo get_the_post_thumbnail( $item['id'], apply_filters( 'edd_checkout_image_size', array( 25,25 ) ) );
								echo '</div>';
							}
							// $item_title = edd_get_cart_item_name( $item );
							// print_r(json_encode($item));
							$item_title = $item['options']['name'];
							// $quantity = edd_get_cart_item_quantity( $item['id'], $item['options'] );
							$fromdatetime = strtotime($item['options']['startdate']);
							$todatetime = strtotime($item['options']['enddate']);
							$noofdays = $item['options']['noofdays'];
							$fromdatetime = date('Y-m-d', $fromdatetime);
							$todatetime = date('Y-m-d', $todatetime);
							$nights = "Nights";
							if($noofdays == 1){
								$nights = "Night";
							}
							echo '<span class="edd_checkout_cart_item_title">' . esc_html( $item_title ) . '</span></br><span class="condtion">(<span class="quantitynumber">'.$noofdays.'</span> '.$nights.', arriving <span class="arrivingDate">'.$fromdatetime.'</span>, departing <span class="departingDate">'.$todatetime.'</span>)</span>';
							do_action( 'edd_checkout_cart_item_title_after', $item );
						?>
					</td>
					<td class="edd_cart_item_price">
						<?php
						echo edd_cart_item_price( $item['id'], $item['options'] );
						do_action( 'edd_checkout_cart_item_price_after', $item );
						?>
					</td>
					<td class="edd_cart_actions">
						<?php if( edd_item_quantities_enabled() ) : ?>
							<?php $availablequantity = $item['options']['availablequantity']; ?>
							<input type="number" min="1" max="<?php echo $availablequantity;?>" step="1" name="edd-cart-download-<?php echo $key; ?>-quantity" data-key="<?php echo $key; ?>" class="edd-input edd-item-quantity form-control qunatityinput" value="<?php echo edd_get_cart_item_quantity( $item['id'], $item['options'] ); ?>"/>
							<input type="hidden" name="edd-cart-downloads[]" value="<?php echo $item['id']; ?>"/>
							<input type="hidden" name="edd-cart-download-<?php echo $key; ?>-options" value="<?php echo esc_attr( serialize( $item['options'] ) ); ?>"/>
						<?php endif; ?>
						<?php do_action( 'edd_cart_actions', $item, $key ); ?>
						<a class="edd_cart_remove_item_btn btn btn-danger btn-small" href="<?php echo esc_url( edd_remove_item_url( $key ) ); ?>"><?php _e( 'x', 'easy-digital-downloads' ); ?></a>
					</td>
					<?php do_action( 'edd_checkout_table_body_last', $item ); ?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php do_action( 'edd_cart_items_middle' ); ?>
		<!-- Show any cart fees, both positive and negative fees -->
		<?php if( edd_cart_has_fees() ) : ?>
			<?php foreach( edd_get_cart_fees() as $fee_id => $fee ) : ?>
				<tr class="edd_cart_fee" id="edd_cart_fee_<?php echo $fee_id; ?>">

					<?php do_action( 'edd_cart_fee_rows_before', $fee_id, $fee ); ?>

					<td class="edd_cart_fee_label"><?php echo esc_html( $fee['label'] ); ?></td>
					<td class="edd_cart_fee_amount"><?php echo esc_html( edd_currency_filter( edd_format_amount( $fee['amount'] ) ) ); ?></td>
					<td>
						<?php if( ! empty( $fee['type'] ) && 'item' == $fee['type'] ) : ?>
							<a href="<?php echo esc_url( edd_remove_cart_fee_url( $fee_id ) ); ?>"><?php _e( 'Remove', 'easy-digital-downloads' ); ?></a>
						<?php endif; ?>

					</td>

					<?php do_action( 'edd_cart_fee_rows_after', $fee_id, $fee ); ?>

				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php do_action( 'edd_cart_items_after' ); ?>
	</tbody>
	<tfoot>

		<?php if( has_action( 'edd_cart_footer_buttons' ) ) : ?>
			<tr class="edd_cart_footer_row<?php if ( edd_is_cart_saving_disabled() ) { echo ' edd-no-js'; } ?>">
				<th colspan="<?php echo edd_checkout_cart_columns(); ?>">
					<?php do_action( 'edd_cart_footer_buttons' ); ?>
				</th>
			</tr>
		<?php endif; ?>

		<?php //if( edd_use_taxes() && ! edd_prices_include_tax() ) : ?>
			<tr class="edd_cart_footer_row edd_cart_subtotal_row no-border"<?php if ( ! edd_is_cart_taxed() ) echo ' style=""'; ?>>
				<?php do_action( 'edd_checkout_table_subtotal_first' ); ?>
				<th></th>
				<th></th>
				<th colspan="3" class="edd_cart_subtotal text-right">
					<?php _e( 'Total', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_subtotal_amount pull-right"><?php echo edd_cart_subtotal(); ?></span>
				</th>
				<?php do_action( 'edd_checkout_table_subtotal_last' ); ?>
			</tr>
		<?php //endif; ?>

		<tr class="edd_cart_footer_row edd_cart_discount_row" <?php if( ! edd_cart_has_discounts() )  echo ' style="display:none;"'; ?>>
			<?php do_action( 'edd_checkout_table_discount_first' ); ?>
			<th></th>
			<th></th>
			<th colspan="3" class="edd_cart_discount">
				<?php edd_cart_discounts_html(); ?>
			</th>
			<?php do_action( 'edd_checkout_table_discount_last' ); ?>
		</tr>

		<?php if( edd_use_taxes() ) : ?>
			<tr class="edd_cart_footer_row edd_cart_tax_row"<?php if( ! edd_is_cart_taxed() ) echo ' style="display:none;"'; ?>>
				<?php do_action( 'edd_checkout_table_tax_first' ); ?>
				<th></th>
				<th></th>
				<th colspan="3" class="edd_cart_tax">
					<?php _e( 'Tax', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_tax_amount  pull-right" data-tax="<?php echo edd_get_cart_tax( false ); ?>"><?php echo esc_html( edd_cart_tax() ); ?></span>
				</th>
				<?php do_action( 'edd_checkout_table_tax_last' ); ?>
			</tr>

		<?php endif; ?>

		<tr class="edd_cart_footer_row">
			<?php do_action( 'edd_checkout_table_footer_first' ); ?>
			<th></th>
			<th></th>
			<th colspan="3" class="edd_cart_total"><?php _e( 'You Pay', 'easy-digital-downloads' ); ?>: <span class="edd_cart_amount  pull-right" data-subtotal="<?php echo edd_get_cart_total(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"><?php edd_cart_total(); ?></span></th>
			<?php do_action( 'edd_checkout_table_footer_last' ); ?>
		</tr>
	</tfoot>
</table>



