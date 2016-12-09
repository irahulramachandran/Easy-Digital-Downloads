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