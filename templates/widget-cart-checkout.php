<li class="col-xs-12">
  <?php _e( 'Room Total:', 'easy-digital-downloads' ); ?> <span class="cart-room-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_room_total() ) ); ?></span>
</li>
<?php if ( edd_use_taxes() ) : ?>
<li class="cart_item edd-cart-meta edd_cart_tax col-xs-12"><?php _e( 'Tax Included:', 'easy-digital-downloads' ); ?> <span class="cart-tax pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_tax() ) ); ?></span></li>
<?php endif; ?>
<li class="cart_item edd-cart-meta edd_total edd-cart-item col-xs-12"><?php _e( 'Grand Total:', 'easy-digital-downloads' ); ?> <span class="cart-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_total() ) ); ?></span></li>
<li class="cart_item edd_checkout">
  <a href="#" class="btn btn-primary btn-addanotherroom col-xs-6 no-padding"><?php _e( 'ADD ANOTHER ROOM', 'easy-digital-downloads' ); ?></a>
  <a href="<?php echo edd_get_checkout_uri(); ?>"  class="btn btn-danger col-xs-6 no-padding"><?php _e( 'CHECKOUT', 'easy-digital-downloads' ); ?></a>
</li>
