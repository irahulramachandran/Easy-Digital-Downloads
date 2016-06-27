<li class="cart_item empty cart-item-row"><?php echo edd_empty_cart_message(); ?></li>
<?php if ( edd_use_taxes() ) : ?>
<li class="cart_item edd-cart-meta edd_cart_tax" style="display:none;"><?php _e( 'Tax Included:', 'easy-digital-downloads' ); ?> <span class="cart-tax"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_tax() ) ); ?></span></li>
<?php endif; ?>
<li class="cart_item edd-cart-meta edd_total" style="display:none;"><?php _e( 'Grand Total:', 'easy-digital-downloads' ); ?> <span class="cart-total"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_total() ) ); ?></span></li>
<li class="cart_item edd_checkout" style="display:none;"><a href="<?php echo edd_get_checkout_uri(); ?>"><?php _e( 'Make this reservation', 'easy-digital-downloads' ); ?></a></li>
