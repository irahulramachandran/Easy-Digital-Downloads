<li class="edd-cart-item">
	<div class='col-xs-12 no-padding cart-item-row'>
		<img class="roomimage" src="{item_img}"/>
		<div class="overlay"></div>
		<div class="item-titles">
			<div class="col-xs-8 no-padding">
				<span class="item_title">{item_title}</span></br>
				<span class="item_title margin-top-5">{rateplan_item_title}</span>
			</div>
			<div class="col-xs-4 no-padding">
				<a href="{remove_url}" data-cart-item="{cart_item_id}" data-download-id="{item_id}" data-action="edd_remove_from_cart" class="edd-remove-from-cart pull-right"><?php _e( 'Remove', 'easy-digital-downloads' ); ?></a>
			</div>
		</div>
	<div>
	<div class="col-xs-12 cart-item-date">
		<div class="col-xs-4 no-padding border-right">
			{checkin_date}
		</div>
		<div class="col-xs-4 no-padding">
			<i class="nights"></i>
			{no_of_night}
		</div>
		<div class="col-xs-4 no-padding border-left">
			{checkout_date}
		</div>
	</div>
	<div class="col-xs-12 cart-item-occupancy">
		<div class="col-xs-6 no-padding">
			{occupany}
		</div>
		<div class="col-xs-6 no-padding">
			{room_quanity}
		</div>
	</div>
	<div class="col-xs-12 cart-item-inclusion">
		{inclusion}
	</div>
	<div class="col-xs-12 cart-item-info">
		<a href="#">Rate Description</a>
		<a href="#">All Inclusions</a>
		<a href="#">Policies</a>
	</div>
	<div class='col-xs-12 cart-item-roomrate'>
		<div class="col-xs-6 no-padding">Room Rate</div>
		<div class="col-xs-6 no-padding"><span class="pull-right">{item_amount}</span></div>
	</div>
	<div class='col-xs-12 cart-item-rate' id="addons{download_id}">
		{addons}
	</div>
	<!-- <span class="edd-cart-item-separator">-</span><span class="edd-cart-item-quantity">&nbsp;{item_quantity}&nbsp;@&nbsp;</span><span class="edd-cart-item-price">&nbsp;{item_amount}&nbsp;</span><span class="edd-cart-item-separator">-</span> -->
</li>
