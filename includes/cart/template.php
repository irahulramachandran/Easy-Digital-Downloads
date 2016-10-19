<?php
/**
 * Cart Template
 *
 * @package     EDD
 * @subpackage  Cart
 * @copyright   Copyright (c) 2015, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Builds the Cart by providing hooks and calling all the hooks for the Cart
 *
 * @since 1.0
 * @return void
 */
function edd_checkout_cart() {

	// Check if the Update cart button should be shown
	if( edd_item_quantities_enabled() ) {
		//add_action( 'edd_cart_footer_buttons', 'edd_update_cart_button' );
	}

	// Check if the Save Cart button should be shown
	if( ! edd_is_cart_saving_disabled() ) {
		add_action( 'edd_cart_footer_buttons', 'edd_save_cart_button' );
	}

	do_action( 'edd_before_checkout_cart' );
	echo '<div id="edd_checkout_cart_form">';
		echo '<div id="edd_checkout_cart_wrap">';
			edd_get_template_part( 'checkout_cart' );
		echo '</div>';
	echo '</div>';
	do_action( 'edd_after_checkout_cart' );
}

/**
 * Renders the Shopping Cart
 *
 * @since 1.0
 *
 * @param bool $echo
 * @return string Fully formatted cart
 */
function edd_shopping_cart( $echo = false ) {
	ob_start();

	do_action( 'edd_before_cart' );

	edd_get_template_part( 'widget', 'cart' );

	do_action( 'edd_after_cart' );

	if ( $echo )
		echo ob_get_clean();
	else
		return ob_get_clean();
}

/**
 * Get Cart Item Template
 *
 * @since 1.0
 * @param int $cart_key Cart key
 * @param array $item Cart item
 * @param bool $ajax AJAX?
 * @return string Cart item
*/
function edd_get_cart_item_template( $cart_key, $item, $ajax = false ) {
	global $post;

	$id = is_array( $item ) ? $item['id'] : $item;

	$remove_url = edd_remove_item_url( $cart_key );
	$options    = !empty( $item['options'] ) ? $item['options'] : array();
	$title  = $options['roomtypename'];
	$downid  = $options['id'];
	$rateplantitle = $options['name'];
	$imgurl = $options['imgurl'];
	$roomprice = $options['roomprice'];
	$roomtypecode = $options['roomtypecode'];
	$rateplancode = $options['rateplancode'];
	$rateplanObj = new Rateplan;
  $rt1 = $rateplanObj->getByCode($rateplancode);
	$ratedescription = $options['roomtypedescription'];
  $policies = $options['policies'];
	//print_r(json_encode());
	$inclusion = $options['inclusion'];
	$adultoccupancy = $options['adultoccupancy'];
	$download_id = $options['id'];
	$childoccupancy = $options['childoccupancy'];
	$availablequantity = $options['availablequantity'];
	$occupany = "";
	$occupany = "<div class='icon ico-iconguest-icon pull-left'></div>";
	if($adultoccupancy == 1){
		$occupany .= "<span class='pull-left margin-left-5'>1 Adult </span>";
	}
	else{
		$occupany .= "<span class='pull-left margin-left-5'>".$adultoccupancy." Adults </span>";
	}
	if($childoccupancy != 0){
		if($childoccupancy == 1){
			$occupany .= "<span class='pull-left'>".$childoccupancy." Child</span>";
		}
		else{
			$occupany .= "<span class='pull-left'>".$childoccupancy." Children</span>";
		}
	}

	//$page = get_page_by_title( $rt1->id, OBJECT, 'snhotel_rateplan' );
//	print_r(json_encode($rateplantitle));
	$terms = wp_get_post_terms( $rt1->id,'snhotel_hotel_cancelpenalties' );//get_terms( 'snhotel_hotel_cancelpenalties' );
	//$penality = print_r($terms);

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){

	    $penality = "<ul class='list-unstyled list-inline inclusion-list margin-top-5'>";
	    foreach ( $terms as $term ) {
	       //$penality .= "<li style='color:".$term_meta['color'].";'>" . $term->name . "</li>";
						$penality .= "<span style='color:".$term_meta['color'].";'>".$term->name . "</span></br>";
	    }
	    $penality .= "</ul>";
	}


		//$penality = $page->ID;
	if($inclusion != null){
		$inclusionsText = "<ul class='list-unstyled list-inline inclusion-list margin-top-5'>";
		foreach ((array)$inclusion as $key => $value) {
			$term_meta = get_option( "taxonomy_".$value->term_id );
			$inclusionsText .= "<li style='color:".$term_meta['color']."; padding-right:0px;'>".$value->name."</li>";
			$inclusionsText .="<li  style='color:".$term_meta['color'].";' class='no-padding'>,</li>";
		}

		$inclusionsText .="</ul>";
	}
	else{
		$inclusionsText = "";
	}
	$quanity ="<input type='hidden' value='". $item['quantity'] ."' class='room_value'>";
	$quanity .= "<select data-download-id='".$downid."' class='quanity edd-quanity pull-right'>";
	for ($i=1; $i <= intval($availablequantity); $i++) {
		if($i <= 5){
			$isSelectedValue = "";
			if($i == $item['quantity']){
				$isSelectedValue = " selected='selected'";
			}
			if($i == 1){
				$quanity .= "<option value='".$i."' ".$isSelectedValue.">".$i." Room</option>";
			}else{
				$quanity .= "<option value='".$i."'  ".$isSelectedValue.">".$i." Rooms</option>";
		}


		}
	}

	$quanity .="</select>";

	$startdate = "";
	$startdateNumber = date("d", strtotime($options['startdate']));
	$startday = date("D", strtotime($options['startdate']));
	$startmnth = date("M", strtotime($options['startdate']));

	$startdate = "<div class='col-xs-6 no-padding date'>".$startdateNumber."</div><div class='col-xs-6 no-padding'><div class='col-xs-12 no-padding day'>".$startday."</div><div class='col-xs-12 no-padding month'>".$startmnth."</div></div>";

	$enddate = "";
	$enddateNumber = date("d", strtotime($options['enddate']));
	$endday = date("D", strtotime($options['enddate']));
	$endmnth = date("M", strtotime($options['enddate']));

	$enddate = "<div class='col-xs-6 date'>".$enddateNumber."</div><div class='col-xs-6'><div class='col-xs-12 no-padding day'>".$endday."</div><div class='col-xs-12 no-padding month'>".$endmnth."</div></div>";

	$quantity   = edd_get_cart_item_quantity( $id, $options );
	$price      = edd_get_cart_item_price( $id, $options );

	if ( ! empty( $options ) ) {
		//$title .= ( edd_has_variable_prices( $item['id'] ) ) ? ' <span class="edd-cart-item-separator">-</span> ' . edd_get_price_name( $id, $item['options'] ) : edd_get_price_name( $id, $item['options'] );
	}

	$fromdatetime = strtotime($options['startdate']);
  $todatetime = strtotime($options['enddate']);

	$datediff = $todatetime - $fromdatetime;
  $noofdays = floor($datediff/(60*60*24));

	if($noofdays == 1){
		$noofdays = "<div class='text-center'>1 Night</div>";
	}else{
		$noofdays = "<div class='text-center noofdays'>".$noofdays." Nights</div>";
	}

	$addons = $options['addons'];

	$addonTotal =0;
	$addonHTML = "";

	if(sizeof($addons) > 0 && !empty($addons[0])){
		foreach ($addons as $key => $value) {
			$addontitle = $value->title;
			$id = $value->id;
			$addonprice = $value->price;
			$addonTotal+=$addonprice;
			$addonHTML .= "<div class='col-xs-6 no-padding'>".$value->title."</div><div class='col-xs-6 no-padding'><span class='pull-right'>".edd_currency_filter( edd_format_amount( $value->price ) )."</span></div>";
		}
	}

	ob_start();

	edd_get_template_part( 'widget', 'cart-item' );

	$item = ob_get_clean();

	$item = str_replace( '{item_title}', $title, $item );

	$item = str_replace( '{RateDescriptionId}', $roomtypecode, $item );
	$item = str_replace( '{ModalInclusionId}', $roomtypecode, $item );
	$item = str_replace( '{PenalitiesId}', $roomtypecode, $item );
	$item = str_replace( '{RateDescriptionPlanId}', $rateplancode, $item );
	$item = str_replace( '{ModalInclusionPlanId}', $rateplancode, $item );
	$item = str_replace( '{PenalitiesPlanId}', $rateplancode, $item );
	$item = str_replace( '{RateDescription}', $ratedescription, $item );
	$item = str_replace( '{Penality}', $penality, $item );
	$item = str_replace( '{cart_key}', $cart_key, $item );


	$item = str_replace('{item_img}', wpthumb( $imgurl, 'width=335&height=223&crop=1' ), $item);
	$item = str_replace('{inclusion}', $inclusionsText, $item);
	$item = str_replace('{download_id}', $download_id, $item);
	$item = str_replace('{rateplan_item_title}', $rateplantitle, $item);
	$item = str_replace('{checkin_date}', $startdate, $item);
	$item = str_replace('{no_of_night}', $noofdays, $item);
	$item = str_replace('{checkout_date}', $enddate, $item);
	$item = str_replace( '{item_amount}', edd_currency_filter( edd_format_amount( $roomprice ) ), $item );
	$item = str_replace( '{room_quanity}', $quanity, $item );
	$item = str_replace( '{addons}', $addonHTML, $item );
	$item = str_replace( '{addon_total}', $addontotalHTML, $item );
	$item = str_replace( '{cart_item_id}', absint( $cart_key ), $item );
	$item = str_replace( '{item_id}', absint( $id ), $item );
	$item = str_replace( '{occupany}', $occupany, $item );
	$item = str_replace( '{item_quantity}', absint( $quantity ), $item );
	$item = str_replace( '{remove_url}', $remove_url, $item );
  	$subtotal = '';
  	if ( $ajax ){
   	 $subtotal = edd_currency_filter( edd_format_amount( edd_get_cart_subtotal() ) ) ;
  	}
 	$item = str_replace( '{subtotal}', $subtotal, $item );

	return apply_filters( 'edd_cart_item', $item, $id );
}

/**
 * Returns the Empty Cart Message
 *
 * @since 1.0
 * @return string Cart is empty message
 */
function edd_empty_cart_message() {
	return apply_filters( 'edd_empty_cart_message', '<span class="edd_empty_cart">' . __( 'Your cart is empty.', 'easy-digital-downloads' ) . '</span>' );
}

/**
 * Echoes the Empty Cart Message
 *
 * @since 1.0
 * @return void
 */
function edd_empty_checkout_cart() {
	echo edd_empty_cart_message();
}
add_action( 'edd_cart_empty', 'edd_empty_checkout_cart' );

/*
 * Calculate the number of columns in the cart table dynamically.
 *
 * @since 1.8
 * @return int The number of columns
 */
function edd_checkout_cart_columns() {
	$head_first = did_action( 'edd_checkout_table_header_first' );
	$head_last  = did_action( 'edd_checkout_table_header_last' );
	$default    = 3;

	return apply_filters( 'edd_checkout_cart_columns', $head_first + $head_last + $default );
}

/**
 * Display the "Save Cart" button on the checkout
 *
 * @since 1.8
 * @return void
 */
function edd_save_cart_button() {
	if ( edd_is_cart_saving_disabled() )
		return;

	$color = edd_get_option( 'checkout_color', 'blue' );
	$color = ( $color == 'inherit' ) ? '' : $color;

	if ( edd_is_cart_saved() ) : ?>
		<a class="edd-cart-saving-button edd-submit button<?php echo ' ' . $color; ?>" id="edd-restore-cart-button" href="<?php echo esc_url( add_query_arg( array( 'edd_action' => 'restore_cart', 'edd_cart_token' => edd_get_cart_token() ) ) ); ?>"><?php _e( 'Restore Previous Cart', 'easy-digital-downloads' ); ?></a>
	<?php endif; ?>
	<a class="edd-cart-saving-button edd-submit button<?php echo ' ' . $color; ?>" id="edd-save-cart-button" href="<?php echo esc_url( add_query_arg( 'edd_action', 'save_cart' ) ); ?>"><?php _e( 'Save Cart', 'easy-digital-downloads' ); ?></a>
	<?php
}

/**
 * Displays the restore cart link on the empty cart page, if a cart is saved
 *
 * @since 1.8
 * @return void
 */
function edd_empty_cart_restore_cart_link() {

	if( edd_is_cart_saving_disabled() )
		return;

	if( edd_is_cart_saved() ) {
		echo ' <a class="edd-cart-saving-link" id="edd-restore-cart-link" href="' . esc_url( add_query_arg( array( 'edd_action' => 'restore_cart', 'edd_cart_token' => edd_get_cart_token() ) ) ) . '">' . __( 'Restore Previous Cart.', 'easy-digital-downloads' ) . '</a>';
	}
}
add_action( 'edd_cart_empty', 'edd_empty_cart_restore_cart_link' );

/**
 * Display the "Save Cart" button on the checkout
 *
 * @since 1.8
 * @return void
 */
function edd_update_cart_button() {
	if ( ! edd_item_quantities_enabled() )
		return;

	$color = edd_get_option( 'checkout_color', 'blue' );
	$color = ( $color == 'inherit' ) ? '' : $color;
?>
	<input type="submit" name="edd_update_cart_submit" class="edd-submit  btn btn-danger edd-no-js button<?php echo ' ' . $color; ?>" value="<?php _e( 'Update Cart', 'easy-digital-downloads' ); ?>"/>
	<input type="hidden" name="edd_action" value="update_cart"/>
<?php

}

/**
 * Display the messages that are related to cart saving
 *
 * @since 1.8
 * @return void
 */
function edd_display_cart_messages() {
	$messages = EDD()->session->get( 'edd_cart_messages' );

	if ( $messages ) {
		foreach ( $messages as $message_id => $message ) {

			// Try and detect what type of message this is
			if ( strpos( strtolower( $message ), 'error' ) ) {
				$type = 'error';
			} elseif ( strpos( strtolower( $message ), 'success' ) ) {
				$type = 'success';
			} else {
				$type = 'info';
			}

			$classes = apply_filters( 'edd_' . $type . '_class', array(
				'edd_errors', 'edd-alert', 'edd-alert-' . $type
			) );

			echo '<div class="' . implode( ' ', $classes ) . '">';
				// Loop message codes and display messages
					echo '<p class="edd_error" id="edd_msg_' . $message_id . '">' . $message . '</p>';
			echo '</div>';

		}

		// Remove all of the cart saving messages
		EDD()->session->set( 'edd_cart_messages', null );
	}
}
add_action( 'edd_before_checkout_cart', 'edd_display_cart_messages' );

function edd_get_cart_room_total(){
	$cart_items    = edd_get_cart_contents();
	$roomtotal = 0;
	foreach ($cart_items as $key => $item) {
		$id = is_array( $item ) ? $item['id'] : $item;
		$options    = !empty( $item['options'] ) ? $item['options'] : array();
		$roomtotal = floatval($roomtotal+floatval($options['roomprice']*$item['quantity']));
	}
	return $roomtotal;
}

function edd_get_cart_addon_total(){
	$cart_items    = edd_get_cart_contents();
	$addontotal = 0;
	foreach ($cart_items as $key => $item) {
		$id = is_array( $item ) ? $item['id'] : $item;
		$options    = !empty( $item['options'] ) ? $item['options'] : array();
		if(!empty($options['addons'])){
			foreach ($options['addons'] as $key => $addon) {
				$addontotal = floatval($addontotal+floatval($addon->price*$item['quantity']));
			}
		}
	}
	return $addontotal;
}

/**
 * Show Added To Cart Messages
 *
 * @since 1.0
 * @param int $download_id Download (Post) ID
 * @return void
 */
function edd_show_added_to_cart_messages( $download_id ) {
	if ( isset( $_POST['edd_action'] ) && $_POST['edd_action'] == 'add_to_cart' ) {
		if ( $download_id != absint( $_POST['download_id'] ) )
			$download_id = absint( $_POST['download_id'] );

		$alert = '<div class="edd_added_to_cart_alert">'
		. sprintf( __('You have successfully added %s to your shopping cart.','easy-digital-downloads' ), get_the_title( $download_id ) )
		. ' <a href="' . edd_get_checkout_uri() . '" class="edd_alert_checkout_link">' . __('Checkout.','easy-digital-downloads' ) . '</a>'
		. '</div>';

		echo apply_filters( 'edd_show_added_to_cart_messages', $alert );
	}
}
add_action('edd_after_download_content', 'edd_show_added_to_cart_messages');
