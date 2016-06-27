<li class="edd-cart-item">
	<div class='col-xs-12 no-padding cart-item-row'>
		<span class="edd-cart-item-title">{item_title}</span>
		<a href="{remove_url}" data-cart-item="{cart_item_id}" data-download-id="{item_id}" data-action="edd_remove_from_cart" class="edd-remove-from-cart pull-right edd-cart-item-title"><?php _e( 'X', 'easy-digital-downloads' ); ?></a>
	<div>
	<div class='col-xs-12 no-padding cart-item-row'>
		<span class="edd-cart-item-title">{rateplan_item_title}</span>
		<span class="edd-cart-item-title pull-right">{item_quantity} x {item_amount}</span>
	</div>
	<div class='col-xs-12 no-padding cart-item-row'>
		<span>Check In</span></br>
		<span>{checkin_date}</span>
	</div>
	<div class='col-xs-12 no-padding cart-item-row'>
		<span>Check Out</span></br>
		<span>{checkout_date}</span>
	</div>
	<!-- <span class="edd-cart-item-separator">-</span><span class="edd-cart-item-quantity">&nbsp;{item_quantity}&nbsp;@&nbsp;</span><span class="edd-cart-item-price">&nbsp;{item_amount}&nbsp;</span><span class="edd-cart-item-separator">-</span> -->
</li>
