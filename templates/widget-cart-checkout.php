<li class="cart_item edd_cart_roomtotal col-xs-12">
  <?php _e( 'Room Total', 'easy-digital-downloads' ); ?> <span class="cart-room-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_room_total() ) ); ?></span>
</li>
<li class="cart_item edd_cart_addontotal col-xs-12">
  <?php _e( 'Addon Total', 'easy-digital-downloads' ); ?> <span class="cart-room-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_addon_total() ) ); ?></span>
</li>
<?php if(edd_cart_has_discounts() ){ ?>
  <li class="cart_item edd-cart-meta col-xs-12"> <?php edd_cart_discounts_html(); ?></li>
<?php } ?>
<?php if ( edd_use_taxes() ) : ?>
<li class="cart_item edd-cart-meta edd_cart_tax col-xs-12">
  <?php _e( 'Included Tax', 'easy-digital-downloads' ); ?>
  <a href="#" class="btn-information" data-toggle="tooltip" data-placement="right" title="GST: <?php echo edd_get_formatted_tax_rate(edd_get_tax_rates()); ?>"></a>
  <span class="cart-tax pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_ibe_calculate_tax(edd_get_cart_total()) ) ); ?></span>
</li>
<?php endif; ?>
<li class="cart_item edd-cart-meta edd_total edd-cart-item col-xs-12"><?php _e( 'Grand Total', 'easy-digital-downloads' ); ?> <span class="cart-total pull-right"><?php echo edd_currency_filter( edd_format_amount( edd_get_cart_total() ) ); ?></span></li>
<li class="cart_item edd_checkout">
  <a href="#" class="btn btn-primary btn-addanotherroom col-xs-6 no-padding"><?php _e( 'ADD ANOTHER ROOM', 'easy-digital-downloads' ); ?></a>
  <a href="<?php echo edd_get_checkout_uri(); ?>"  class="btn btn-danger col-xs-6 no-padding"><?php _e( 'CHECKOUT', 'easy-digital-downloads' ); ?></a>
</li>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
