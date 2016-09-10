<?php
/**
 * This template is used to display the Downloads cart widget.
 */
$cart_items    = edd_get_cart_contents();
$cart_quantity = edd_get_cart_quantity();
$display       = $cart_quantity > 0 ? '' : ' style="display:none;"';
$total = edd_currency_filter( edd_format_amount( edd_get_cart_total() ) );
?>
<div class="reservation-summary-header" <?php echo $display; ?>>
	<span class="title">RESERVATION SUMMARY</span>
	<div class="item-count"><?php echo $cart_quantity; ?></div>
	<div class="total-price"><?php echo $total;?></div>
	<div class="glyphicon glyphicon-menu-up"></div>
</div>
<!-- <p class="edd-cart-number-of-items"<?php echo $display; ?>><?php _e( 'Number of items in cart', 'easy-digital-downloads' ); ?>: <span class="edd-cart-quantity"><?php echo $cart_quantity; ?></span></p> -->
<ul class="edd-cart">
<?php if( $cart_items ) : ?>

	<?php foreach( $cart_items as $key => $item ) : ?>

		<?php echo edd_get_cart_item_template( $key, $item, false ); ?>

	<?php endforeach; ?>

	<?php edd_get_template_part( 'widget', 'cart-checkout' ); ?>

<?php else : ?>

	<?php edd_get_template_part( 'widget', 'cart-empty' ); ?>

<?php endif; ?>
</ul>
