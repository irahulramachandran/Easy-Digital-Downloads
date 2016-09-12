<li class="cart_item empty cart-item-row"><?php echo edd_empty_cart_message(); ?></li>
<li class="cart_item edd_cart_roomtotal col-xs-12" style="display:none;">
  <?php _e( 'Room Total:', 'easy-digital-downloads' ); ?> <span class="cart-room-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_room_total() ) ); ?></span>
</li>
<li class="cart_item edd_cart_addontotal col-xs-12" style="display:none;">
  <?php _e( 'Addon Total:', 'easy-digital-downloads' ); ?> <span class="cart-room-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_addon_total() ) ); ?></span>
</li>
<?php if ( edd_use_taxes() ) : ?>
<li class="cart_item edd-cart-meta edd_cart_tax col-xs-12" style="display:none;"><?php _e( 'Tax Included:', 'easy-digital-downloads' ); ?> <span class="cart-tax pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_calculate_tax(edd_get_cart_total()) ) ); ?></span></li>
<?php endif; ?>
<li class="cart_item edd-cart-meta edd_total col-xs-12" style="display:none;"><?php _e( 'Grand Total:', 'easy-digital-downloads' ); ?> <span class="cart-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_total() ) ); ?></span></li>
<li class="cart_item edd_checkout" style="display:none;">
  <a href="#" class="btn btn-primary btn-addanotherroom col-xs-6 no-padding"><?php _e( 'ADD ANOTHER ROOM', 'easy-digital-downloads' ); ?></a>
  <a href="<?php echo edd_get_checkout_uri(); ?>"  class="btn btn-danger col-xs-6 no-padding"><?php _e( 'CHECKOUT', 'easy-digital-downloads' ); ?></a>
</li>
