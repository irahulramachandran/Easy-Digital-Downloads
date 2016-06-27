<?php if ( edd_use_taxes() ) : ?>
<li class="cart_item edd-cart-meta edd_cart_tax"><?php _e( 'Tax Included:', 'easy-digital-downloads' ); ?> <span class="cart-tax pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_tax() ) ); ?></span></li>
<?php endif; ?>
<li class="cart_item edd-cart-meta edd_total edd-cart-item"><?php _e( 'Grand Total:', 'easy-digital-downloads' ); ?> <span class="cart-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_total() ) ); ?></span></li>
<li class="cart_item edd_checkout"><a href="<?php echo edd_get_checkout_uri(); ?>"><?php _e( 'Make this reservation', 'easy-digital-downloads' ); ?></a></li>
