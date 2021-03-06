<?php
/**
 * Checkout Template
 *
 * @package     EDD
 * @subpackage  Checkout
 * @copyright   Copyright (c) 2015, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Checkout Form
 *
 * @since 1.0
 * @return string
 */
function edd_checkout_form() {
	$payment_mode = edd_get_chosen_gateway();
	$payment_mode = '';
	$form_action  = esc_url( edd_get_checkout_uri( 'payment-mode=' . $payment_mode ) );

	ob_start();
	// $postid = get_page_by_path('checkout');
	// $postItem = get_post($postid);
	?>
	<?php
  $cart_quantity = edd_get_cart_quantity();
	  $className  = $cart_quantity > 0 ? ' active' : '';
	  $widthOption  = $cart_quantity > 0 ? ' width:50%' : 'width:0%';
	  $valueOption  = $cart_quantity > 0 ? ' 50' : '0';
	?>
	<div class='bar_group' max='100'>
	    <div class='bar_group__bar thin elastic' label='Oranges' style="<?php echo $widthOption; ?>" value='<?php echo $valueOption; ?>'></div>
	    <div class="col-xs-3 text-center search-rooms progress-indicator <?php echo $className; ?>">Search</div>
	    <div class="col-xs-3 text-center select-rooms progress-indicator <?php echo $className; ?>">Select Room</div>
	    <div class="col-xs-3 text-center personal-info progress-indicator">Personal Information</div>
	    <div class="col-xs-3 text-center confirm-booking progress-indicator">Confimation</div>
	</div>
	<?php
		echo '<div id="edd_checkout_wrap"> <div class="col-xs-12 no-padding">';
		if ( edd_get_cart_contents() || edd_cart_has_fees() ) :

			edd_checkout_cart();
?>
			<div id="edd_checkout_form_wrap" class="edd_clearfix">
			<!--	<h2 class='checkout-wizard-title purchase-details-title margin-bottom-20'>Purchase Details</h2>-->
				<?php do_action( 'edd_before_purchase_form' ); ?>
				<form id="edd_purchase_form" class="edd_form" method="POST" action="<?php echo $form_action; ?>">
					<?php
					/**
					 * Hooks in at the top of the checkout form
					 *
					 * @since 1.0
					 */
					do_action( 'edd_checkout_form_top' );
					if ( edd_show_gateways() ) {
						do_action( 'edd_payment_mode_select'  );
					} else {
						do_action( 'edd_purchase_form' );
					}

					/**
					 * Hooks in at the bottom of the checkout form
					 *
					 * @since 1.0
					 */
					do_action( 'edd_checkout_form_bottom' )
					?>
					<div class='personal-payment-information'></div>
				</form>
				<?php do_action( 'edd_after_purchase_form' ); ?>
			</div><!--end #edd_checkout_form_wrap-->
		<?php
		else:
			/**
			 * Fires off when there is nothing in the cart
			 *
			 * @since 1.0
			 */
			do_action( 'edd_cart_empty' );
		endif;
		echo '</div></div><!--end #edd_checkout_wrap-->';
	return ob_get_clean();
}

/**
 * Renders the Purchase Form, hooks are provided to add to the purchase form.
 * The default Purchase Form rendered displays a list of the enabled payment
 * gateways, a user registration form (if enable) and a credit card info form
 * if credit cards are enabled
 *
 * @since 1.4
 * @return string
 */
function edd_show_purchase_form() {
	$payment_mode = edd_get_chosen_gateway();

	/**
	 * Hooks in at the top of the purchase form
	 *
	 * @since 1.4
	 */
	do_action( 'edd_purchase_form_top' );

	if ( edd_can_checkout() ) {

		do_action( 'edd_purchase_form_before_register_login' );

		$show_register_form = edd_get_option( 'show_register_form', 'none' ) ;
		if( ( $show_register_form === 'registration' || ( $show_register_form === 'both' && ! isset( $_GET['login'] ) ) ) && ! is_user_logged_in() ) : ?>
			<div id="edd_checkout_login_register">
				<?php do_action( 'edd_purchase_form_register_fields' ); ?>
			</div>
		<?php elseif( ( $show_register_form === 'login' || ( $show_register_form === 'both' && isset( $_GET['login'] ) ) ) && ! is_user_logged_in() ) : ?>
			<div id="edd_checkout_login_register">
				<?php do_action( 'edd_purchase_form_login_fields' ); ?>
			</div>
		<?php endif; ?>

		<?php if( ( ! isset( $_GET['login'] ) && is_user_logged_in() ) || ! isset( $show_register_form ) || 'none' === $show_register_form || 'login' === $show_register_form ) {
			do_action( 'edd_purchase_form_after_user_info' );
		}

		/**
		 * Hooks in before Credit Card Form
		 *
		 * @since 1.4
		 */
		do_action( 'edd_purchase_form_before_cc_form' );

		if( edd_get_cart_total() > 0 ) {

			// Load the credit card form and allow gateways to load their own if they wish
			if ( has_action( 'edd_' . $payment_mode . '_cc_form' ) ) {
				do_action( 'edd_' . $payment_mode . '_cc_form' );
			} else {
				do_action( 'edd_cc_form' );
			}

		}

		/**
		 * Hooks in after Credit Card Form
		 *
		 * @since 1.4
		 */
		do_action( 'edd_purchase_form_after_cc_form' );

	} else {
		// Can't checkout
		do_action( 'edd_purchase_form_no_access' );
	}

	/**
	 * Hooks in at the bottom of the purchase form
	 *
	 * @since 1.4
	 */
	do_action( 'edd_purchase_form_bottom' );
}
add_action( 'edd_purchase_form', 'edd_show_purchase_form' );

/**
 * Shows the User Info fields in the Personal Info box, more fields can be added
 * via the hooks provided.
 *
 * @since 1.3.3
 * @return void
 */
function edd_user_info_fields() {

	$customer = EDD()->session->get( 'customer' );
	$customer = wp_parse_args( $customer, array( 'first_name' => '', 'last_name' => '', 'email' => '' ) );

	if( is_user_logged_in() ) {
		$user_data = get_userdata( get_current_user_id() );
		foreach( $customer as $key => $field ) {

			if ( 'email' == $key && empty( $field ) ) {
				$customer[ $key ] = $user_data->user_email;
			} elseif ( empty( $field ) ) {
				$customer[ $key ] = $user_data->$key;
			}

		}
	}

	$customer = array_map( 'sanitize_text_field', $customer );
	if(is_user_logged_in()){
		$current_user = wp_get_current_user();
	}
	?>
	<div id="loading">
  	<div class="loading_img"></div>
	</div>
	<div class="modal fade" tabindex="-1" role="dialog" id="loginpopup">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Login</h4>
	      </div>
	      <div class="modal-body">
					<?php do_shortcode('[edd_login]'); ?>
	      </div>
	    </div>
	  </div>
	</div>
	<div class="col-xs-12 no-padding" id="checkoutitems">
		<div class="row text-center no-margin choice_mob_header">
			<div class="choice_mob">
				<div class="col-xs-12 hidden-sm hidden-md hidden-lg no-padding">
					<h2>I AM BOOKING FOR</h2>

						<div class="checkbox">
							<div class="label">
							  <input type="radio" name="booker1" class="booker" value="2" checked="checked"> <span>MY SELF</span>
							</div>
							<div class="label">
							  <input type="radio" name="booker1" class="booker" value="1"> <span>SOMEONE ELSE</span>
							</div>
						</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12 hidden-md hidden-lg">
			<div class="col-xs-12 margin-top-10 checkout-wizard-title hidden-sm hidden-md hidden-lg"><b>Personal Details</b></div>
			<?php
			if(is_user_logged_in()){ ?>
			<div class="col-xs-12"><h4 class="username"><?php echo "Welcome ".$current_user->user_firstname." ".$current_user->user_lastname.","; ?></h4></div>
			<?php } ?>
		</div>

		<fieldset  class="col-xs-12 no-padding margin-top-10">
			<div class="col-xs-12 col-sm-8 col-md-9 col-lg-9">
				<div class="hidden-xs col-sm-12 col-md-12">
					<h2>I AM BOOKING FOR</h2>

						<div class="checkbox">
							<div class="label">
							  <input type="radio" name="booker" class="booker" value="2" checked="checked"> <span>MY SELF</span>
							</div>
							<div class="label">
							  <input type="radio" name="booker" class="booker" value="1"> <span>SOMEONE ELSE</span>
							</div>
						</div>

				</div>
				<?php
				if(is_user_logged_in()){ ?>
				<div class="col-xs-12 hidden-xs hidden-sm margin-bottom-10"><h3 class="username"><?php echo "Welcome ".$current_user->user_firstname." ".$current_user->user_lastname.","; ?></h3></div>
				<?php } ?>
				<?php
				if(!is_user_logged_in()){
					?>
				<div class="col-xs-12 no-padding">
					<div class="col-xs-12 margin-top-10 no-padding">
						<?php do_action( 'edd_checkout_login_button' ); ?>

							<div class="col-xs-12">
								<img src="<?php echo get_stylesheet_directory_uri();?>/imgs/separator_checkout.png" class="img_res"/>
							</div>
					</div>
				</div>
				<?php
				}
				?>
		 <!--<h2 class="checkout-wizard-title hidden-sm hidden-md hidden-lg">Personal Information</h2>-->
		<!-- <form class="payment-form" method="POST"> -->
	    <div class="form-container">
	        <div class="personal-information">
						<div class="col-xs-12 no-padding">
							<div class="col-xs-12 col-sm-6">
								<div class="margin-top-10">
									First Name*
									<input class="form-control edd_col_3" type="text" name="edd_first" id="edd_first" maxlength="50" value="<?php echo esc_attr( $customer['first_name'] ); ?>" placeholder="First Name" maxlength="50"/>
								</div>
								<div class="margin-top-10">
									Last Name*
									<input class="form-control edd_col_3" type="text" name="edd_last" id="edd_last" maxlength="50" value="<?php echo esc_attr( $customer['last_name'] ); ?>" placeholder="Last Name" maxlength="50"/>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="margin-top-10">
									<?php do_action( 'edd_purchase_form_before_email' ); ?>
									Email*
									<input class="form-control edd_col_3" type="email" name="edd_email" id="edd_email" maxlength="100" autocomplete="on" placeholder="Email" value="<?php echo esc_attr( $customer['email'] ); ?>" maxlength="50"/>
									<?php do_action( 'edd_purchase_form_after_email' ); ?>
									</div>
									<div class="margin-top-10 hidden-xs">
									Phone
									<input class="form-control edd_col_3" type="text" name="edd_phonenumber" id="edd_phonenumber" maxlength="12" value="<?php echo esc_attr( $customer['phonenumber'] ); ?>" placeholder="Phone Number" maxlength="15"/>
								</div>
							</div>
						</div>
						<div class="additonal-information col-xs-12 no-padding">
							<div class="col-xs-12 col-sm-6 ">
								<div class="margin-top-10">
									Address
									<input class="form-control edd_col_4" type="text" name="edd_address" id="edd_address" maxlength="200" value="<?php echo esc_attr( $customer['address'] ); ?>" placeholder="Address" maxlength="300"/>
								</div>
								<div class="hidden-sm hidden-md hidden-lg margin-top-10">
									City
									<input class="form-control edd_col_4 CityName_mobile" type="text" name="edd_CityName" id="edd_CityName" maxlength="200" value="<?php echo esc_attr( $customer['CityName'] ); ?>" placeholder="City" maxlength="50"/>
								</div>
								<div class="margin-top-10">
									State
									<input class="form-control edd_col_4" type="text" name="edd_StateProv" id="edd_StateProv" maxlength="200" value="<?php echo esc_attr( $customer['StateProv'] ); ?>" placeholder="State" maxlength="300"/>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6 ">
								<div class="hidden-xs margin-top-10">
									City
									<input class="form-control edd_col_4 CityName_web" type="text" name="edd_CityName" id="edd_CityName" maxlength="200" value="<?php echo esc_attr( $customer['CityName'] ); ?>" placeholder="City" maxlength="50"/>
								</div>
								<div class="margin-top-10">
									Country
									<select class="form-control edd_col_4" name="edd_country" id="edd_country">
										<option value="">Select Country</option>
										<?php
										$countries = edd_get_country_list();
										foreach( $countries as $country_code => $country ) {
										  echo '<option value="' . esc_attr( $country_code ) . '">' . $country . '</option>';
										}
										?>
									</select>
								</div>

							</div>
						</div>
						<div class="col-xs-12">
							<div class="margin-top-10">
								<label>
								  <input type="checkbox" name="addInfo" class="addInfo"> <span class="addInfo_ckbox">I WANT TO PROVIDE ADDITIONAL INFORMATION</span>
								</label>
								<!-- <label>
								  <input type="checkbox" name="earnDollars" class="earnDollars" checked> <span class="addInfo_ckbox">REGISTER TO EARN 744 $ILVER DOLLARS</span>
								</label> -->
							</div>
						</div>

							<!-- <input class="form-control edd_col_4" type="text" name="edd_StateProv" id="edd_StateProv" value="<?php echo esc_attr( $customer['StateProv'] ); ?>" placeholder="State"/> -->

	        </div>

					<div class="guest-information col-xs-12 no-padding">
						<div class="col-xs-12 margin-top-10">
							<h4 class="checkout-wizard-title margin-top-0 margin-bottom-10">Guest Information</h4>
						</div>
						<div class="col-xs-12 col-sm-6 ">
							<div class="margin-top-10">
								First Name
								<input class="form-control edd_col_3" type="text" name="edd_guest_first" id="edd_guest_first" maxlength="50" value="<?php echo esc_attr( $customer['guest_first_name'] ); ?>" placeholder="Guest First Name" maxlength="50"/>
							</div>
							<div class="margin-top-10">
								Last Name
								<input class="form-control edd_col_3" type="text" name="edd_guest_last" id="edd_guest_last" maxlength="50" value="<?php echo esc_attr( $customer['guest_last_name'] ); ?>" placeholder="Guest Last Name" maxlength="50"/>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6 ">

							<div class="margin-top-10">
								Email
								<input class="form-control edd_col_3" type="email" name="edd_guest_email" id="edd_guest_email" maxlength="100" autocomplete="on" placeholder="Guest Email" value="" maxlength="50"/>
							</div>
							<div class="margin-top-10 hidden-xs">
								Phone
								<input class="form-control edd_col_3" type="text" name="edd_guest_phonenumber" id="edd_guest_phonenumber" value="<?php echo esc_attr( $customer['guest_phonenumber'] ); ?>" placeholder="Guest Phone Number" maxlength="12"/>
							</div>
						</div>
					<div class="guest-additonal-information col-xs-12 no-padding">
						<div class="col-xs-12 col-sm-6 ">
							<div class="margin-top-10">
								Address
								<input class="form-control edd_col_4" type="text" name="edd_guest_address" id="edd_guest_address" maxlength="200" value="<?php echo esc_attr( $customer['guest_address'] ); ?>" placeholder="Guest Address" maxlength="300"/>
							</div>
							<div class="hidden-sm hidden-md hidden-lg margin-top-10">
								City
								<input class="form-control edd_col_4 CityName_tablet" type="text" name="edd_CityName" id="edd_CityName" maxlength="200" value="<?php echo esc_attr( $customer['CityName'] ); ?>" placeholder="City" maxlength="50"/>
							</div>
							<div class="margin-top-10">
								State
								<input class="form-control edd_col_4" type="text" name="edd_guest_StateProv" id="guest_StateProv" maxlength="200" value="<?php echo esc_attr( $customer['guest_StateProv'] ); ?>" placeholder="Guest State"/>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="hidden-xs margin-top-10">
								City
								<input class="form-control edd_col_4" type="text" name="edd_guest_CityName" id="edd_guest_CityName" maxlength="200" value="<?php echo esc_attr( $customer['guest_CityName'] ); ?>" placeholder="Guest City" maxlength="50"/>
							</div>
							<div class="margin-top-10">
								Country
								<select class="form-control edd_col_4" name="edd_guest_country" id="edd_guest_country">
									<option value="">Select Guest Country</option>
									<?php
									$countries = edd_get_country_list();
									foreach( $countries as $country_code => $country ) {
									  echo '<option value="' . esc_attr( $country_code ) . '">' . $country . '</option>';
									}
									?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12  no-padding">
					<div class="payment-information">
						<div class="col-xs-12 margin-top-10">
							<h4 class="checkout-wizard-title margin-top-0 margin-bottom-10"><b>Payment Details <span>(Your card will not be charged. It serves as guarantee only.)</span></b></h4>
						</div>
							<div class="col-xs-12 col-md-6 margin-top-10">
								Name On Card*
								<input class="form-control edd_card_name"  maxlength="100" autocomplete="off" type="text" name="card_name" id="card_name" />
							</div>
							<div class="col-xs-12 col-md-6 margin-top-10">
								Card Number*
								<input class="form-control edd_number ccFormatMonitor" maxlength="19" autocomplete="off" type="text" name="card_number" id="card_number"/>
								<input type='hidden' name='card_type' id="card_type"/>
							</div>
							<div class="col-xs-12 col-md-6 no-padding margin-top-10">
								<div class="col-xs-12">
								Expiry Date*
								</div>
								<div class="col-xs-6">
									<input class="form-control edd_expiry" placeholder='MM' autocomplete="off" type="text" name="card_expiry_month" maxlength="2" id="card_expiry_month"/>
								</div>
								<div class="col-xs-6">
									<input class="form-control edd_expiry" placeholder='YYYY' autocomplete="off" type="text" name="card_expiry_year" maxlength="4" id="card_expiry_year"/>
								</div>
							</div>

							<div class="col-xs-12 col-md-6 no-padding margin-top-10">
								<div class="col-xs-12">
								CVV*
								</div>
								<div class="col-xs-7">
									<input class="form-control edd_cvc"  autocomplete="off" type="password" name="card_cvc" maxlength="4" id="edd_cvc" />
								</div>
								<div class="col-xs-5">
									<img src="<?php echo get_stylesheet_directory_uri()?>/imgs/cardcvv.png" data-toggle="modal" data-target="#mycvv" class="cvv-cursor"/>
								</div>
							</div>
							<div id="mycvv" class="modal fade" role="dialog">
						  <div class="modal-dialog">

						    <!-- Modal content-->
						    <div class="modal-content">
						      <div class="modal-body text-center">
						        <img src="<?php echo get_template_directory_uri()?>/imgs/cvv.png"/>
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						      </div>
						    </div>

						  </div>
						</div>
							<div class="guest-additonal-information col-xs-12 no-padding">
								<div class="row hidden-sm hidden-md hidden-lg"><hr></div>
								<div class="margin-top-10">
										<div class="col-xs-12">Special Request</div>
										<div class="col-xs-12">
											<textarea class="form-control special_req" name="comment" placeholder="Please ensure guest can check in at any time"></textarea>
										</div>
								</div>
							</div>
						<div class="col-xs-12 margin-top-10">
							<label>
							  <input type="checkbox" name="earnDollars" class="earnDollars" id="termsandcondition" checked> <span class="addInfo_ckbox">I have read and agreed to the <a href="<?php echo edd_get_termsncondition_page_uri(); ?>" target="new">terms & conditions</a> governing this booking.</span>
							</label>
						</div>
						<div class='hidden-xs col-sm-12 footer-button'>
							<!--<a href="#" class="btn btn-danger btn-md btn-backbtn">Back</a>-->
							<input class="btn btn-danger btn-md confirmBooking" type="submit" value="Confirm Booking" id="confirmBooking"/>
						</div>
					</div>

				</div>
	<!-- </form> -->
				<div class="row">
					<div class="col-xs-12 hidden-sm hidden-md hidden-lg footer-button no-padding">
						<!--<a href="#" class="btn btn-danger btn-md btn-backbtn">Back</a>-->
						<input class="btn btn-danger btn-md confirmBooking" type="submit" value="Confirm Booking" id="confirmBooking"/>
					</div>
				</div>
				<div class="col-xs-12">
					<?php do_action( 'edd_purchase_form_before_submit' ); ?>
				</div>
			</div>
	</div>
	<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 no-padding">
		<div class='mini-cart-wrapper checkout_page position-releative'>
			<?php do_shortcode('[download_cart]'); ?>
		</div>
	</div>
	</fieldset>
</div>
	<script type="text/javascript">
		function valid_credit_card(value) {
			if($("#card_number").hasClass("valid")){
				if($("#card_number").hasClass('amex')){
					$("#card_type").val("AX");
					return true;
				}
				else if($("#card_number").hasClass('diners_club_carte_blanche')){
					$("#card_type").val("CB");
					return true;
				}
				else if($("#card_number").hasClass('diners_club_international')){
					$("#card_type").val("DN");
					return true;
				}
				else if($("#card_number").hasClass('discover')){
					$("#card_type").val("DS");
					return true;
				}
				else if($("#card_number").hasClass('mastercard')){
					$("#card_type").val("MC");
					return true;
				}
				else if($("#card_number").hasClass('visa')){
					$("#card_type").val("VI");
					return true;
				}
				else if($("#card_number").hasClass('visa_electron')){
					$("#card_type").val("VE");
					return true;
				}
				else if($("#card_number").hasClass('jcb')){
					$("#card_type").val("JC");
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		$(document).ready(function(){

			//Restrict Space
			$('#card_number').keydown(function(e) {
				if (e.keyCode == 32) {
					return false;
				}
			});
			$('#edd_cvc').keydown(function(e) {
				if (e.keyCode == 32) {
					return false;
				}
			});

			var $inputs = $('.CityName_mobile, .CityName_web, .CityName_tablet');

			$inputs.keyup(function(){
			    $inputs.val($(this).val());
			});

			$inputs.change(function(){
			    $inputs.val($(this).val());
			});

			var stopProgressInterval = false;
			setInterval(function(){
				if(Number($(".bar_group__bar").attr("value")) != 100)
				{
					var Email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
					if ($("#edd_email").val() == "" && Email.test($('#edd_email').val()) == false) {
							$(".bar_group__bar").attr("value","50").css("width","50%");
							$(".bar_group .personal-info").removeClass("active");
							return false;
					}

					if ($("#edd_first").val() == "") {
						$(".bar_group__bar").attr("value","50").css("width","50%");
						$(".bar_group .personal-info").removeClass("active");
						return false;
					}

					if ($("#edd_last").val() == "") {
						$(".bar_group__bar").attr("value","50").css("width","50%");
						$(".bar_group .personal-info").removeClass("active");
						return false;
					}

					if ($("#card_name").val() == "") {
						$(".bar_group__bar").attr("value","50").css("width","50%");
						$(".bar_group .personal-info").removeClass("active");
						return false;
					}

					if ($("#card_number").val() == "" && !valid_credit_card($("#card_number").val())) {
						$(".bar_group__bar").attr("value","50").css("width","50%");
						$(".bar_group .personal-info").removeClass("active");
						return false;
					}

					if($("#card_expiry_month").val() == "" || Number($("#card_expiry_month").val()) > 12){
						$(".bar_group__bar").attr("value","50").css("width","50%");
						$(".bar_group .personal-info").removeClass("active");
						return false;
					}

					if($("#card_expiry_year").val() == "" || $.trim($("#card_expiry_year").val()).length != 4){
						$(".bar_group__bar").attr("value","50").css("width","50%");
						$(".bar_group .personal-info").removeClass("active");
						return false;
					}

					if($("#edd_cvc").val() == ""){
						$(".bar_group__bar").attr("value","50").css("width","50%");
						$(".bar_group .personal-info").removeClass("active");
						return false;
					}

					$(".bar_group__bar").attr("value","75").css("width","75%");
					$(".bar_group .personal-info").addClass("active");
					return false;
				}
			}, 500);

			$(".additonal-information,.guest-additonal-information").hide();
			$('.guest-information').hide();
			$(".addInfo").change(function(){
				$(".additonal-information,.guest-additonal-information").slideToggle("fast");
			});

			//$('input.booker.mobile').first().prop('checked',true);

			$('input[name="booker"]').bind('change',function(e){
				e.preventDefault();
				var showOrHide = ($(this).val() == 1) ? true : false;
				$('.guest-information').toggle(showOrHide);
			});
			$('input[name="booker1"]').bind('change',function(e){
				e.preventDefault();
				$('input[name="booker"]').val($(this).val());
				var showOrHide = ($(this).val() == 1) ? true : false;
				$('.guest-information').toggle(showOrHide);
			});
			if($(window).width() < 768){
				if($(".mini-cart-wrapper").size() > 0 && $(".mini-cart-wrapper").hasClass("position-releative")){
					$(".mini-cart-wrapper").removeClass("position-releative");
				}
			}

			// if($(".edd-slg-login-wrapper").size() > 0){
			// 	var html = "<div class='edd-slg-login-wrapper nomargin'>";
			// 			html += "<a title='Connect with SN Select' href='javascript:void(0);' class='edd-slg-social-login-snselect social-button snselect-button'  data-toggle='modal' data-target='#loginpopup'>";
			// 			html += "</a></div>";
			//
			// 	$(".edd-slg-clear").before(html);
			//
			// 	$(".snselect-button").click(function(){
			// 		// $('#loginpopup').modal({
			// 		//   keyboard: false
			// 		// });
			// 		$("#loginpopup").addClass("in");
			// 		$("#loginpopup").css("display","block !important");
			// 	});
			// }

			$("#card_expiry_month").mask("00");
			$("#card_expiry_year").mask("0000");
			$("#edd_cvc").mask("0000");

			$('#card_number').validateCreditCard(function(result) {
				console.log(result);
				if(result.card_type == null)
				{
						$('#card_number').removeClass().addClass("form-control");
				}
				else
				{
						$('#card_number').addClass(result.card_type.name);
				}

				if(!result.valid)
				{
						$('#card_number').removeClass("valid");
				}
				else
				{
						$('#card_number').addClass("valid");
				}
			});
		});
	</script>

	<?php do_action( 'edd_purchase_form_user_info' ); ?>
	<?php do_action( 'edd_purchase_form_user_info_fields' ); ?>
	<?php
}
add_action( 'edd_purchase_form_after_user_info', 'edd_user_info_fields' );
add_action( 'edd_register_fields_before', 'edd_user_info_fields' );

/**
 * Renders the credit card info form.
 *
 * @since 1.0
 * @return void
 */
function edd_get_cc_form() {
	ob_start(); ?>

	<?php do_action( 'edd_before_cc_fields' ); ?>

	<!--<fieldset id="edd_cc_fields" class="edd-do-validate">
		<span><legend><?php _e( 'Credit Card Info', 'easy-digital-downloads' ); ?></legend></span>
		<?php if( is_ssl() ) : ?>
			<div id="edd_secure_site_wrapper">
				<span class="padlock"></span>
				<span><?php _e( 'This is a secure SSL encrypted payment.', 'easy-digital-downloads' ); ?></span>
			</div>
		<?php endif; ?>
		<p id="edd-card-number-wrap">
			<label for="card_number" class="edd-label">
				<?php _e( 'Card Number', 'easy-digital-downloads' ); ?>
				<span class="edd-required-indicator">*</span>
				<span class="card-type"></span>
			</label>
			<span class="edd-description"><?php _e( 'The (typically) 16 digits on the front of your credit card.', 'easy-digital-downloads' ); ?></span>
			<input type="text" autocomplete="off" name="card_number" id="card_number" class="card-number edd-input required" placeholder="<?php _e( 'Card number', 'easy-digital-downloads' ); ?>" />
		</p>
		<p id="edd-card-cvc-wrap">
			<label for="card_cvc" class="edd-label">
				<?php _e( 'CVC', 'easy-digital-downloads' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'easy-digital-downloads' ); ?></span>
			<input type="text" size="4" maxlength="4" autocomplete="off" name="card_cvc" id="card_cvc" class="card-cvc edd-input required" placeholder="<?php _e( 'Security code', 'easy-digital-downloads' ); ?>" />
		</p>
		<p id="edd-card-name-wrap">
			<label for="card_name" class="edd-label">
				<?php _e( 'Name on the Card', 'easy-digital-downloads' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The name printed on the front of your credit card.', 'easy-digital-downloads' ); ?></span>
			<input type="text" autocomplete="off" name="card_name" id="card_name" class="card-name edd-input required" placeholder="<?php _e( 'Card name', 'easy-digital-downloads' ); ?>" />
		</p>
		<?php do_action( 'edd_before_cc_expiration' ); ?>
		<p class="card-expiration">
			<label for="card_exp_month" class="edd-label">
				<?php _e( 'Expiration (MM/YY)', 'easy-digital-downloads' ); ?>
				<span class="edd-required-indicator">*</span>
			</label>
			<span class="edd-description"><?php _e( 'The date your credit card expires, typically on the front of the card.', 'easy-digital-downloads' ); ?></span>
			<select id="card_exp_month" name="card_exp_month" class="card-expiry-month edd-select edd-select-small required">
				<?php for( $i = 1; $i <= 12; $i++ ) { echo '<option value="' . $i . '">' . sprintf ('%02d', $i ) . '</option>'; } ?>
			</select>
			<span class="exp-divider"> / </span>
			<select id="card_exp_year" name="card_exp_year" class="card-expiry-year edd-select edd-select-small required">
				<?php for( $i = date('Y'); $i <= date('Y') + 30; $i++ ) { echo '<option value="' . $i . '">' . substr( $i, 2 ) . '</option>'; } ?>
			</select>
		</p>
		<?php do_action( 'edd_after_cc_expiration' ); ?>

	</fieldset>-->
	<?php
	do_action( 'edd_after_cc_fields' );

	echo ob_get_clean();
}
add_action( 'edd_cc_form', 'edd_get_cc_form' );

/**
 * Outputs the default credit card address fields
 *
 * @since 1.0
 * @return void
 */
function edd_default_cc_address_fields() {

	$logged_in = is_user_logged_in();
	$customer  = EDD()->session->get( 'customer' );
	$customer  = wp_parse_args( $customer, array( 'address' => array(
		'line1'   => '',
		'line2'   => '',
		'city'    => '',
		'zip'     => '',
		'state'   => '',
		'country' => ''
	) ) );

	$customer['address'] = array_map( 'sanitize_text_field', $customer['address'] );

	if( $logged_in ) {

		$user_address = get_user_meta( get_current_user_id(), '_edd_user_address', true );

		foreach( $customer['address'] as $key => $field ) {

			if ( empty( $field ) && ! empty( $user_address[ $key ] ) ) {
				$customer['address'][ $key ] = $user_address[ $key ];
			} else {
				$customer['address'][ $key ] = '';
			}

		}

	}

	ob_start(); ?>
	<!-- <fieldset id="edd_cc_address" class="cc-address">
		<span><legend><?php _e( 'Billing Details', 'easy-digital-downloads' ); ?></legend></span>
		<?php do_action( 'edd_cc_billing_top' ); ?>
		<p id="edd-card-address-wrap">
			<label for="card_address" class="edd-label">
				<?php _e( 'Billing Address', 'easy-digital-downloads' ); ?>
				<?php if( edd_field_is_required( 'card_address' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The primary billing address for your credit card.', 'easy-digital-downloads' ); ?></span>
			<input type="text" id="card_address" name="card_address" class="card-address edd-input<?php if( edd_field_is_required( 'card_address' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Address line 1', 'easy-digital-downloads' ); ?>" value="<?php echo $customer['address']['line1']; ?>"<?php if( edd_field_is_required( 'card_address' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="edd-card-address-2-wrap">
			<label for="card_address_2" class="edd-label">
				<?php _e( 'Billing Address Line 2 (optional)', 'easy-digital-downloads' ); ?>
				<?php if( edd_field_is_required( 'card_address_2' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The suite, apt no, PO box, etc, associated with your billing address.', 'easy-digital-downloads' ); ?></span>
			<input type="text" id="card_address_2" name="card_address_2" class="card-address-2 edd-input<?php if( edd_field_is_required( 'card_address_2' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Address line 2', 'easy-digital-downloads' ); ?>" value="<?php echo $customer['address']['line2']; ?>"<?php if( edd_field_is_required( 'card_address_2' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="edd-card-city-wrap">
			<label for="card_city" class="edd-label">
				<?php _e( 'Billing City', 'easy-digital-downloads' ); ?>
				<?php if( edd_field_is_required( 'card_city' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The city for your billing address.', 'easy-digital-downloads' ); ?></span>
			<input type="text" id="card_city" name="card_city" class="card-city edd-input<?php if( edd_field_is_required( 'card_city' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'City', 'easy-digital-downloads' ); ?>" value="<?php echo $customer['address']['city']; ?>"<?php if( edd_field_is_required( 'card_city' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="edd-card-zip-wrap">
			<label for="card_zip" class="edd-label">
				<?php _e( 'Billing Zip / Postal Code', 'easy-digital-downloads' ); ?>
				<?php if( edd_field_is_required( 'card_zip' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The zip or postal code for your billing address.', 'easy-digital-downloads' ); ?></span>
			<input type="text" size="4" name="card_zip" class="card-zip edd-input<?php if( edd_field_is_required( 'card_zip' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Zip / Postal Code', 'easy-digital-downloads' ); ?>" value="<?php echo $customer['address']['zip']; ?>"<?php if( edd_field_is_required( 'card_zip' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="edd-card-country-wrap">
			<label for="billing_country" class="edd-label">
				<?php _e( 'Billing Country', 'easy-digital-downloads' ); ?>
				<?php if( edd_field_is_required( 'billing_country' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The country for your billing address.', 'easy-digital-downloads' ); ?></span>
			<select name="billing_country" id="billing_country" class="billing_country edd-select<?php if( edd_field_is_required( 'billing_country' ) ) { echo ' required'; } ?>"<?php if( edd_field_is_required( 'billing_country' ) ) {  echo ' required '; } ?>>
				<?php

				$selected_country = edd_get_shop_country();

				if( ! empty( $customer['address']['country'] ) && '*' !== $customer['address']['country'] ) {
					$selected_country = $customer['address']['country'];
				}

				$countries = edd_get_country_list();
				foreach( $countries as $country_code => $country ) {
				  echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
				}
				?>
			</select>
		</p>
		<p id="edd-card-state-wrap">
			<label for="card_state" class="edd-label">
				<?php _e( 'Billing State / Province', 'easy-digital-downloads' ); ?>
				<?php if( edd_field_is_required( 'card_state' ) ) { ?>
					<span class="edd-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="edd-description"><?php _e( 'The state or province for your billing address.', 'easy-digital-downloads' ); ?></span>
            <?php
            $selected_state = edd_get_shop_state();
            $states         = edd_get_shop_states( $selected_country );

            if( ! empty( $customer['address']['state'] ) ) {
				$selected_state = $customer['address']['state'];
			}

            if( ! empty( $states ) ) : ?>
            <select name="card_state" id="card_state" class="card_state edd-select<?php if( edd_field_is_required( 'card_state' ) ) { echo ' required'; } ?>">
                <?php
                    foreach( $states as $state_code => $state ) {
                        echo '<option value="' . $state_code . '"' . selected( $state_code, $selected_state, false ) . '>' . $state . '</option>';
                    }
                ?>
            </select>
        	<?php else : ?>
			<input type="text" size="6" name="card_state" id="card_state" class="card_state edd-input" placeholder="<?php _e( 'State / Province', 'easy-digital-downloads' ); ?>"/>
			<?php endif; ?>
		</p>
		<?php do_action( 'edd_cc_billing_bottom' ); ?>
	</fieldset> -->
	<?php
	echo ob_get_clean();
}
add_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' );


/**
 * Renders the billing address fields for cart taxation
 *
 * @since 1.6
 * @return void
 */
function edd_checkout_tax_fields() {
	if( edd_cart_needs_tax_address_fields() && edd_get_cart_total() )
		edd_default_cc_address_fields();
}
add_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_tax_fields', 999 );


/**
 * Renders the user registration fields. If the user is logged in, a login
 * form is displayed other a registration form is provided for the user to
 * create an account.
 *
 * @since 1.0
 * @return string
 */
function edd_get_register_fields() {
	$show_register_form = edd_get_option( 'show_register_form', 'none' );

	ob_start(); ?>
	<fieldset id="edd_register_fields">

		<?php if( $show_register_form == 'both' ) { ?>
			<p id="edd-login-account-wrap"><?php _e( 'Already have an account?', 'easy-digital-downloads' ); ?> <a href="<?php echo esc_url( add_query_arg( 'login', 1 ) ); ?>" class="edd_checkout_register_login" data-action="checkout_login"><?php _e( 'Login', 'easy-digital-downloads' ); ?></a></p>
		<?php } ?>

		<?php do_action('edd_register_fields_before'); ?>

		<fieldset id="edd_register_account_fields">
			<span><legend><?php _e( 'Create an account', 'easy-digital-downloads' ); if( !edd_no_guest_checkout() ) { echo ' ' . __( '(optional)', 'easy-digital-downloads' ); } ?></legend></span>
			<?php do_action('edd_register_account_fields_before'); ?>
			<p id="edd-user-login-wrap">
				<label for="edd_user_login">
					<?php _e( 'Username', 'easy-digital-downloads' ); ?>
					<?php if( edd_no_guest_checkout() ) { ?>
					<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span class="edd-description"><?php _e( 'The username you will use to log into your account.', 'easy-digital-downloads' ); ?></span>
				<input name="edd_user_login" id="edd_user_login" class="<?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input form-control" type="text" placeholder="<?php _e( 'Username', 'easy-digital-downloads' ); ?>" title="<?php _e( 'Username', 'easy-digital-downloads' ); ?>"/>
			</p>
			<p id="edd-user-pass-wrap">
				<label for="edd_user_pass">
					<?php _e( 'Password', 'easy-digital-downloads' ); ?>
					<?php if( edd_no_guest_checkout() ) { ?>
					<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span class="edd-description"><?php _e( 'The password used to access your account.', 'easy-digital-downloads' ); ?></span>
				<input name="edd_user_pass" id="edd_user_pass" class="<?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input form-control" placeholder="<?php _e( 'Password', 'easy-digital-downloads' ); ?>" type="password"/>
			</p>
			<p id="edd-user-pass-confirm-wrap" class="edd_register_password">
				<label for="edd_user_pass_confirm">
					<?php _e( 'Password Again', 'easy-digital-downloads' ); ?>
					<?php if( edd_no_guest_checkout() ) { ?>
					<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span class="edd-description"><?php _e( 'Confirm your password.', 'easy-digital-downloads' ); ?></span>
				<input name="edd_user_pass_confirm" id="edd_user_pass_confirm" class="<?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input form-control" placeholder="<?php _e( 'Confirm password', 'easy-digital-downloads' ); ?>" type="password"/>
			</p>
			<?php do_action( 'edd_register_account_fields_after' ); ?>
		</fieldset>

		<?php do_action('edd_register_fields_after'); ?>

		<input type="hidden" name="edd-purchase-var" value="needs-to-register"/>

		<?php do_action( 'edd_purchase_form_user_info' ); ?>
		<?php do_action( 'edd_purchase_form_user_register_fields' ); ?>

	</fieldset>
	<?php
	echo ob_get_clean();
}
add_action( 'edd_purchase_form_register_fields', 'edd_get_register_fields' );

/**
 * Gets the login fields for the login form on the checkout. This function hooks
 * on the edd_purchase_form_login_fields to display the login form if a user already
 * had an account.
 *
 * @since 1.0
 * @return string
 */
function edd_get_login_fields() {
	$color = edd_get_option( 'checkout_color', 'gray' );
	$color = ( $color == 'inherit' ) ? '' : $color;
	$style = edd_get_option( 'button_style', 'button' );

	$show_register_form = edd_get_option( 'show_register_form', 'none' );

	ob_start(); ?>
		<fieldset id="edd_login_fields">
			<?php if( $show_register_form == 'both' ) { ?>
				<p id="edd-new-account-wrap">
					<?php _e( 'Need to create an account?', 'easy-digital-downloads' ); ?>
					<a href="<?php echo esc_url( remove_query_arg('login') ); ?>" class="edd_checkout_register_login" data-action="checkout_register">
						<?php _e( 'Register', 'easy-digital-downloads' ); if(!edd_no_guest_checkout()) { echo ' ' . __( 'or checkout as a guest.', 'easy-digital-downloads' ); } ?>
					</a>
				</p>
			<?php } ?>
			<?php do_action('edd_checkout_login_fields_before'); ?>
			<p id="edd-user-login-wrap">
				<label class="edd-label" for="edd-username">
					<?php _e( 'Username', 'easy-digital-downloads' ); ?>
					<?php if( edd_no_guest_checkout() ) { ?>
					<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<input class="<?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input form-control" type="text" name="edd_user_login" id="edd_user_login" value="" placeholder="<?php _e( 'Your username', 'easy-digital-downloads' ); ?>"/>
			</p>
			<p id="edd-user-pass-wrap" class="edd_login_password">
				<label class="edd-label" for="edd-password">
					<?php _e( 'Password', 'easy-digital-downloads' ); ?>
					<?php if( edd_no_guest_checkout() ) { ?>
					<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<input class="<?php if(edd_no_guest_checkout()) { echo 'required '; } ?>edd-input form-control" type="password" name="edd_user_pass" id="edd_user_pass" placeholder="<?php _e( 'Your password', 'easy-digital-downloads' ); ?>"/>
				<?php if( edd_no_guest_checkout() ) : ?>
					<input type="hidden" name="edd-purchase-var" value="needs-to-login"/>
				<?php endif; ?>
			</p>
			<p id="edd-user-login-submit">
				<input type="submit" class="edd-submit button btn-danger <?php echo $color; ?>" name="edd_login_submit" value="<?php _e( 'Login', 'easy-digital-downloads' ); ?>"/>
			</p>
			<?php do_action('edd_checkout_login_fields_after'); ?>
		</fieldset><!--end #edd_login_fields-->
	<?php
	echo ob_get_clean();
}
add_action( 'edd_purchase_form_login_fields', 'edd_get_login_fields' );

/**
 * Renders the payment mode form by getting all the enabled payment gateways and
 * outputting them as radio buttons for the user to choose the payment gateway. If
 * a default payment gateway has been chosen from the EDD Settings, it will be
 * automatically selected.
 *
 * @since 1.2.2
 * @return void
 */
function edd_payment_mode_select() {
	$gateways = edd_get_enabled_payment_gateways( true );
	$page_URL = edd_get_current_page_url();
	//do_action('edd_payment_mode_top');
	?>
	<?php if( edd_is_ajax_disabled() ) { ?>
	<form id="edd_payment_mode" action="<?php echo $page_URL; ?>" method="GET">
	<?php } ?>
		<fieldset id="edd_payment_mode_select">
			<?php do_action( 'edd_payment_mode_before_gateways_wrap' ); ?>
			<div id="edd-payment-mode-wrap">
				<!-- <span class="edd-payment-mode-label"><?php _e( 'Select Payment Method', 'easy-digital-downloads' ); ?></span><br/> -->
				<?php

				do_action( 'edd_payment_mode_before_gateways' );

				foreach ( $gateways as $gateway_id => $gateway ) :

					$checked = checked( $gateway_id, edd_get_default_gateway(), false );
					$checked_class = $checked ? ' edd-gateway-option-selected' : '';
					echo '<label for="edd-gateway-' . esc_attr( $gateway_id ) . '" class="edd-gateway-option hidden-xs hidden-sm hidden-md hidden-lg' . $checked_class . '" id="edd-gateway-option-' . esc_attr( $gateway_id ) . '">';
						echo '<input type="hidden" name="payment-mode" class="edd-gateway" id="edd-gateway-' . esc_attr( $gateway_id ) . '" value="' . esc_attr( $gateway_id ) . '"' . $checked . '>' . esc_html( $gateway['checkout_label'] );
					echo '</label>';
				endforeach;

				do_action( 'edd_payment_mode_after_gateways' );

				?>
			</div>
			<?php do_action( 'edd_payment_mode_after_gateways_wrap' ); ?>
		</fieldset>
		<fieldset id="edd_payment_mode_submit" class="edd-no-js">
			<p id="edd-next-submit-wrap">
				<?php echo edd_checkout_button_next(); ?>
			</p>
		</fieldset>
	<?php if( edd_is_ajax_disabled() ) { ?>
	</form>
	<?php } ?>
	<div id="edd_purchase_form_wrap"></div><!-- the checkout fields are loaded into this-->
	<?php do_action('edd_payment_mode_bottom');
}
add_action( 'edd_payment_mode_select', 'edd_payment_mode_select' );


/**
 * Show Payment Icons by getting all the accepted icons from the EDD Settings
 * then outputting the icons.
 *
 * @since 1.0
 * @return void
*/
function edd_show_payment_icons() {

	if( edd_show_gateways() && did_action( 'edd_payment_mode_top' ) ) {
		return;
	}

	$payment_methods = edd_get_option( 'accepted_cards', array() );

	if( empty( $payment_methods ) ) {
		return;
	}

	echo '<div class="edd-payment-icons">';

	foreach( $payment_methods as $key => $card ) {

		if( edd_string_is_image_url( $key ) ) {

			echo '<img class="payment-icon" src="' . esc_url( $key ) . '"/>';

		} else {

			$card = strtolower( str_replace( ' ', '', $card ) );

			if( has_filter( 'edd_accepted_payment_' . $card . '_image' ) ) {

				$image = apply_filters( 'edd_accepted_payment_' . $card . '_image', '' );

			} else {

				$image       = edd_locate_template( 'images' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $card . '.gif', false );
				$content_dir = WP_CONTENT_DIR;

				if( function_exists( 'wp_normalize_path' ) ) {

					// Replaces backslashes with forward slashes for Windows systems
					$image = wp_normalize_path( $image );
					$content_dir = wp_normalize_path( $content_dir );

				}

				$image = str_replace( $content_dir, content_url(), $image );

			}

			if( edd_is_ssl_enforced() || is_ssl() ) {

				$image = edd_enforced_ssl_asset_filter( $image );

			}

			echo '<img class="payment-icon" src="' . esc_url( $image ) . '"/>';
		}

	}

	echo '</div>';
}
add_action( 'edd_payment_mode_top', 'edd_show_payment_icons' );
add_action( 'edd_checkout_form_top', 'edd_show_payment_icons' );


/**
 * Renders the Discount Code field which allows users to enter a discount code.
 * This field is only displayed if there are any active discounts on the site else
 * it's not displayed.
 *
 * @since 1.2.2
 * @return void
*/
function edd_discount_field() {
	if( isset( $_GET['payment-mode'] ) && edd_is_ajax_disabled() ) {
		return; // Only show before a payment method has been selected if ajax is disabled
	}

	if( ! edd_is_checkout() ) {
		return;
	}

	if ( edd_has_active_discounts() && edd_get_cart_total() ) :
		$color = edd_get_option( 'checkout_color', 'blue' );
		$color = ( $color == 'inherit' ) ? '' : $color;
		$style = edd_get_option( 'button_style', 'button' );
?>
		<fieldset id="edd_discount_code">
			<p id="edd_show_discount" style="display:none;">
				<?php _e( 'Have a discount code?', 'easy-digital-downloads' ); ?> <a href="#" class="edd_discount_link"><?php echo _x( 'Click to enter it', 'Entering a discount code', 'easy-digital-downloads' ); ?></a>
			</p>
			<p id="edd-discount-code-wrap" class="edd-cart-adjustment">
				<label class="edd-label" for="edd-discount">
					<?php _e( 'Discount', 'easy-digital-downloads' ); ?>
					<img src="<?php echo EDD_PLUGIN_URL; ?>assets/images/loading.gif" id="edd-discount-loader" style="display:none;"/>
				</label>
				<span class="edd-description"><?php _e( 'Enter a coupon code if you have one.', 'easy-digital-downloads' ); ?></span>
				<input class="edd-input form-control" type="text" id="edd-discount" name="edd-discount" placeholder="<?php _e( 'Enter discount', 'easy-digital-downloads' ); ?>"/>
				<input type="submit" class="edd-apply-discount edd-submit button btn btn-primary <?php echo $color . ' ' . $style; ?>" value="<?php echo _x( 'Apply', 'Apply discount at checkout', 'easy-digital-downloads' ); ?>"/>
				<span id="edd-discount-error-wrap" class="edd_error edd-alert edd-alert-error" style="display:none;"></span>
			</p>
		</fieldset>
<?php
	endif;
}
add_action( 'edd_checkout_form_top', 'edd_discount_field', -1 );

/**
 * Renders the Checkout Agree to Terms, this displays a checkbox for users to
 * agree the T&Cs set in the EDD Settings. This is only displayed if T&Cs are
 * set in the EDD Settings.
 *
 * @since 1.3.2
 * @return void
 */
function edd_terms_agreement() {
	if ( edd_get_option( 'show_agree_to_terms', false ) ) {
		$agree_text  = edd_get_option( 'agree_text', '' );
		$agree_label = edd_get_option( 'agree_label', __( 'Agree to Terms?', 'easy-digital-downloads' ) );
?>
		<div id="edd_terms_agreement">
			<label for="edd_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
			<div id="edd_terms">
				<?php
					do_action( 'edd_before_terms' );
					echo wpautop( stripslashes( $agree_text ) );
					do_action( 'edd_after_terms' );
				?>
			</div>
			<!-- <div id="edd_show_terms">
				<a href="#" class="edd_terms_links"><?php _e( 'Show Terms', 'easy-digital-downloads' ); ?></a>
				<a href="#" class="edd_terms_links" style="display:none;"><?php _e( 'Hide Terms', 'easy-digital-downloads' ); ?></a>
			</div>
			<label for="edd_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
			<input name="edd_agree_to_terms" class="required" type="checkbox" id="edd_agree_to_terms" value="1"/> -->
		</div>
<?php
	}
}
add_action( 'edd_purchase_form_before_submit', 'edd_terms_agreement' );

function edd_property_policy() {
	if ( edd_get_option( 'show_policy', false ) ) {
		$agree_text  = edd_get_option( 'policy_text', '' );
		$agree_label = edd_get_option( 'policy_title', __( 'Policy', 'easy-digital-downloads' ) );
?>
		<div id="edd_terms_agreement" class="margin-top-20 margin-bottom-20">
			<label for="edd_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
			<div id="edd_terms">
				<?php
					do_action( 'edd_before_terms' );
					echo wpautop( stripslashes( $agree_text ) );
					do_action( 'edd_after_terms' );
				?>
			</div>
			<!-- <div id="edd_show_terms">
				<a href="#" class="edd_terms_links"><?php _e( 'Show Terms', 'easy-digital-downloads' ); ?></a>
				<a href="#" class="edd_terms_links" style="display:none;"><?php _e( 'Hide Terms', 'easy-digital-downloads' ); ?></a>
			</div>
			<label for="edd_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
			<input name="edd_agree_to_terms" class="required" type="checkbox" id="edd_agree_to_terms" value="1"/> -->
		</div>
<?php
	}
}
add_action( 'edd_purchase_form_before_submit', 'edd_property_policy' );

/**
 * Shows the final purchase total at the bottom of the checkout page
 *
 * @since 1.5
 * @return void
 */
function edd_checkout_final_total() {
?>
<!-- <p id="edd_final_total_wrap">
	<strong><?php _e( 'Purchase Total:', 'easy-digital-downloads' ); ?></strong>
	<span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_subtotal(); ?>" data-total="<?php echo edd_get_cart_subtotal(); ?>"><?php edd_cart_total(); ?></span>
</p> -->
<?php
}
add_action( 'edd_purchase_form_before_submit', 'edd_checkout_final_total', 999 );


/**
 * Renders the Checkout Submit section
 *
 * @since 1.3.3
 * @return void
 */
function edd_checkout_submit() {
?>
	<fieldset id="edd_purchase_submit">

		<?php edd_checkout_hidden_fields(); ?>

		<?php echo edd_checkout_button_purchase(); ?>

		<?php do_action( 'edd_purchase_form_after_submit' ); ?>

		<?php if ( edd_is_ajax_disabled() ) { ?>
			<p class="edd-cancel"><a href="javascript:history.go(-1)"><?php _e( 'Go back', 'easy-digital-downloads' ); ?></a></p>
		<?php } ?>
	</fieldset>
<?php
}
add_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_submit', 9999 );

/**
 * Renders the Next button on the Checkout
 *
 * @since 1.2
 * @return string
 */
function edd_checkout_button_next() {
	$color = edd_get_option( 'checkout_color', 'blue' );
	$color = ( $color == 'inherit' ) ? '' : $color;
	$style = edd_get_option( 'button_style', 'button' );
	$purchase_page = edd_get_option( 'purchase_page', '0' );

	ob_start();
?>
	<input type="hidden" name="edd_action" value="gateway_select" />
	<input type="hidden" name="page_id" value="<?php echo absint( $purchase_page ); ?>"/>
	<input type="submit" name="gateway_submit" id="edd_next_button" class="edd-submit <?php echo $color; ?> <?php echo $style; ?>" value="<?php _e( 'Next', 'easy-digital-downloads' ); ?>"/>
<?php
	return apply_filters( 'edd_checkout_button_next', ob_get_clean() );
}

/**
 * Renders the Purchase button on the Checkout
 *
 * @since 1.2
 * @return string
 */
function edd_checkout_button_purchase() {
	$color = edd_get_option( 'checkout_color', 'blue' );
	$color = ( $color == 'inherit' ) ? '' : $color;
	$style = edd_get_option( 'button_style', 'button' );
	$label = edd_get_option( 'checkout_label', '' );

	if ( edd_get_cart_total() ) {
		$complete_purchase = ! empty( $label ) ? $label : __( 'Purchase', 'easy-digital-downloads' );
	} else {
		$complete_purchase = ! empty( $label ) ? $label : __( 'Free Download', 'easy-digital-downloads' );
	}

	ob_start();
?>
	<!-- <input type="submit" class="edd-submit <?php echo $color; ?> <?php echo $style; ?>" id="edd-purchase-button" name="edd-purchase" id="" value="Confirm Booking"/> -->
	<!-- <input type="submit" class="edd-submit <?php echo $color; ?> <?php echo $style; ?>" id="edd-purchase-button" name="edd-purchase" value="<?php echo $complete_purchase; ?>"/> -->
<?php
	return apply_filters( 'edd_checkout_button_purchase', ob_get_clean() );
}

/**
 * Outputs the JavaScript code for the Agree to Terms section to toggle
 * the T&Cs text
 *
 * @since 1.0
 * @return void
 */
function edd_agree_to_terms_js() {
	if ( edd_get_option( 'show_agree_to_terms', false ) ) {
?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$( document.body ).on('click', '.edd_terms_links', function(e) {
				//e.preventDefault();
				$('#edd_terms').slideToggle();
				$('.edd_terms_links').toggle();
				return false;
			});
		});
	</script>
<?php
	}
}
add_action( 'edd_checkout_form_top', 'edd_agree_to_terms_js' );

/**
 * Renders the hidden Checkout fields
 *
 * @since 1.3.2
 * @return void
 */
function edd_checkout_hidden_fields() {
?>
	<?php if ( is_user_logged_in() ) { ?>
	<input type="hidden" name="edd-user-id" value="<?php echo get_current_user_id(); ?>"/>
	<?php } ?>
	<input type="hidden" name="edd_action" value="purchase"/>
	<input type="hidden" name="edd-gateway" value="<?php echo edd_get_chosen_gateway(); ?>" />
<?php
}

/**
 * Filter Success Page Content
 *
 * Applies filters to the success page content.
 *
 * @since 1.0
 * @param string $content Content before filters
 * @return string $content Filtered content
 */
function edd_filter_success_page_content( $content ) {
	if ( isset( $_GET['payment-confirmation'] ) && edd_is_success_page() ) {
		if ( has_filter( 'edd_payment_confirm_' . $_GET['payment-confirmation'] ) ) {
			$content = apply_filters( 'edd_payment_confirm_' . $_GET['payment-confirmation'], $content );
		}
	}

	return $content;
}
add_filter( 'the_content', 'edd_filter_success_page_content', 99999 );

/**
 * Show a download's files in the purchase receipt
 *
 * @since  1.8.6
 * @param  int        $item_id      The download ID
 * @param  array      $receipt_args Args specified in the [edd_receipt] shortcode
 * @param  array      $item         Cart item array
 * @return boolean
 */
function edd_receipt_show_download_files( $item_id, $receipt_args, $item = array() ) {
	return apply_filters( 'edd_receipt_show_download_files', true, $item_id, $receipt_args, $item );
}
