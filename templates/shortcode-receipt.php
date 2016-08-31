<?php
/**
 * This template is used to display the purchase summary with [edd_receipt]
 */
global $edd_receipt_args;

$payment = get_post($edd_receipt_args['id']);
if (empty($payment)) :
    ?>

    <div class="edd_errors edd-alert edd-alert-error">
        <?php _e('The specified receipt ID appears to be invalid', 'easy-digital-downloads'); ?>
    </div>

    <?php
    return;
endif;
// error_log("SENDING EMAIL To the user");
// edd_email_purchase_receipt_for_user( $payment->ID );
// error_log("SEND EMAIL To the user");

$meta = edd_get_payment_meta($payment->ID);
$cart = edd_get_payment_meta_cart_details($payment->ID, true);
$user = edd_get_payment_meta_user_info($payment->ID);
$email = edd_get_payment_user_email($payment->ID);
$status = edd_get_payment_status($payment, true);
$settings = get_option("snc_theme_settings");
$hotelCode = esc_html(stripslashes($settings["snc_hotelid"]));
$hotelName = esc_html(stripslashes($settings["snc_hotelname"]));
$startdate = edd_booking_startdate($payment->ID);
$count = sizeof($cart);
$useremail = edd_get_payment_user_email($payment->ID);
$guestemail = edd_get_payment_guest_email($payment->ID);
$name = $user['first_name']." ".$user['last_name'];

$bookingtext  = "";

$bookingmessage = "";

if(!empty($guestemail)){
  if($useremail != $guestemail){
    $bookingmessage = edd_get_option( 'booking_for_guest_message', '' );
  }
  else{
    $bookingmessage = edd_get_option( 'booking_for_self_message', '' );
  }
}
else{
  $bookingmessage = edd_get_option( 'booking_for_self_message', '' );
}

//print_r(json_encode($meta));

$bookingmessage = get_booking_message($bookingmessage,$payment);

$imageURL = $cart[0]['item_number']['options']['imgurl'];
?>
<div class="container-fluid no-padding" id="dvContents">
  <div class="hero" style="background-image:url(<?php echo $imageURL;?>)">
		<div class="overlay">
		</div>
    <div class="container">
      <h2 class="pageheader">BOOKING CONFIRMATION <?php echo edd_get_reservation($payment->ID); ?></h2>
    </div>
	</div>
  <div class="container main-container margin-top-30">
    <div class="row">
			<div class="col-xs-12 padding-bottom-15 confirmation-header margin-bottom-10 ">
				<div class="pull-right confirmation-actions-container">
					<a href="#" class="pull-left confirmation-action-btn btn-print" id="btnPrint"></a>
					<a class="pull-left confirmation-action-btn btn-download" href="<?php echo esc_url( edd_pdf_invoices()->get_pdf_invoice_url( $payment->ID ) ); ?>" ></a>
					<a href="#" class="pull-left confirmation-action-btn btn-share"></a>
				</div>
			</div>
			<div class="col-xs-12 margin-bottom-15 confirmation-content">
				<p class="pull-left margin-bottom-15"><?php echo $bookingmessage; ?></p>
			</div>
		</div>
    <div class="row">
				<div class="col-xs-12 confirmation-summary">
					<h6 class="font-bold margin-bottom-5 margin-top-0">Booking Summary</h6>
          <?php
          $i = 0;
            if ($cart) : ?>
              <?php foreach ($cart as $key => $item) : ?>
                  <?php
                  //print_r(json_encode($item));
                  $item_title = $item['item_number']['options']['roomtypename'];
                  $rateplan_title = $item['item_number']['options']['name'];
                  $fromdatetime = strtotime($item['item_number']['options']['startdate']);
                  $todatetime = strtotime($item['item_number']['options']['enddate']);
                  $imgurl = $item['item_number']['options']['imgurl'];
                  $noofdays = $item['item_number']['options']['noofdays'];
                  $roomprice = $item['item_number']['options']['roomprice'];
                  $price = $item['item_number']['options']['price'];
                  $addonTotal = $item['item_number']['options']['addontotal'];
                  $fromdatetime = date('d M Y', $fromdatetime);
                  $todatetime = date('d M Y', $todatetime);
                  ?>
                  <div class="roomitem margin-top-10 pull-left col-xs-12 no-padding"> <!-- Loop Starts -->
          					<div class="col-xs-12 col-md-7 no-padding room-image-container">
          						<div class="room-image" style="background-image:url(<?php echo $imgurl;?>)">
          						</div>
          					</div>
          					<div class="col-xs-12 col-md-5 room-details-container">
          						<div class="row no-margin">
          							<div class="room-name-plan-duration">
          								<div class="pull-left">
          									<h2 class="room-name"><?php echo $item_title; ?></h2>
          									<h5 class="margin-bottom-0 room-plan"><?php echo $rateplan_title; ?></h5>
          								</div>
          								<div class="pull-right border-left-light">
          									<div class="duration-icon"></div>
                            <?php
                            if($noofdays == 1){
                              ?>
                              <p>1 Night</p>
                              <?php
                            }
                            else{
                              ?>
                              <p><?php echo $noofdays;?> Nights</p>
                              <?php
                            }
                            ?>
          								</div>
          							</div>
          						</div>
          						<div class="row no-margin margin-top-50 arrvial-departure-container">
          							<div class="pull-left arrival">
          								<div class="pull-left">
          									<span class="font-bold">Arrival Date</span>
          									<p class="arrival-date"><?php echo $fromdatetime; ?></p>
          								</div>
          								<!-- <div class="pull-right icon-thunder-rain arrival-weather-icon"></div> -->
          							</div>
          							<div class="pull-right departure">
          								<div class="pull-left">
          									<span class="font-bold">Departure Date</span>
          									<p class="arrival-date"><?php echo $todatetime; ?></p>
          								</div>
          								<!-- <div class="pull-right icon-sunny arrival-weather-icon"></div> -->
          							</div>
          						</div>
                      <?php if($addonTotal > 0){
                        $className = "";
                      }else{
                        $className = "border-bottom-light";
                      }
                        ?>

          						<div class="row no-margin margin-top-30 padding-bottom-10 room-total <?php echo $className; ?>">
          							<span class="pull-left font-bold">Room Total</span>
          							<span class="pull-right font-bold"><?php echo edd_currency_filter(edd_format_amount($roomprice)); ?></span>
          						</div>
                      <?php if($addonTotal > 0){
                        ?>
                        <div class="row no-margin margin-top-30 padding-bottom-10 border-bottom-light room-total">
            							<span class="pull-left font-bold">Addon Total</span>
            							<span class="pull-right font-bold"><?php echo edd_currency_filter(edd_format_amount($addonTotal)); ?></span>
            						</div>
                        <?php
                        }
                      ?>
                      <?php
                      $i++;
                      if($i == $count){
                        ?>
                        <div class="row no-margin margin-top-10">
            							<span class="pull-left font-bold">Total to be paid at hotel</span>
            							<span class="pull-right font-bold"><?php echo edd_currency_filter(edd_format_amount($price)); ?></span>
            						</div>
            						<div class="row margin-top-10 confirmation-action-mobile-container">
            							<a href="#" class="col-xs-8 no-padding btn-primary download-btn-mobile">DOWNLOAD</a>
            							<a href="#" class="col-xs-4 no-padding btn-secondary share-btn-mobile">SHARE</a>
            						</div>
                        <?php
                      }
                      ?>
          					</div>
        				  </div> <!-- Loop End -->
                <?php endforeach; ?>
            <?php endif; ?>
				</div>
			</div>
      <div class="row margin-top-15">
				<div class="col-xs-12 enhance-stay-outer-container">
					<h6 class="font-bold margin-bottom-15">Enhance Your Stay</h6>
					<div class="pull-left enhance-stay-inner-container">
						<div class="fake-left"></div>
            <div class="pull-left enhance-stay-container">

            <?php
            $args = array(
              'posts_per_page'   => -1,
              'orderby'          => 'date',
              'order'            => 'DESC',
              'post_type'        => 'snhotel_addons',
              'post_status'      => 'publish',
              );
              $addonsFromDB = get_posts( $args );
              foreach ( $addonsFromDB as $post ) : setup_postdata( $post );
                if (has_post_thumbnail($post->ID)) {
                    $image = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); //the_post_thumbnail_url();//wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'archive-post-thumbnail');
                    $imagePath = $image;
                }
            ?>

							<div class="pull-left no-padding enhance-stay" style="background-image:url(<?php echo $imagePath; ?>)">
								<div class="gradient-overlay">
									<p class="pull-right price">From <span class="text-bold"><?php echo edd_currency_filter(edd_format_amount(get_post_meta($post->ID, 'pricefield', 1))); ?></span></p>
									<p class="pull-left title margin-bottom-0"><?php echo $post->post_title; ?></p>
								</div>
							</div>
              <?php endforeach;
              wp_reset_postdata();?>
            </div>
            <div class="fake-right"></div>
					</div>
				</div>
			</div>
      <div class="row margin-top-15">
				<div class="col-xs-12 things-to-do-outer-container">
					<h6 class="font-bold margin-bottom-15">Top four things to do in Brisbane</h6>
					<div class="pull-left things-to-do-inner-container">
					<div class="fake-left"></div>
						<div class="pull-left things-to-do-container">
                <?php
                $args = array(
                  'posts_per_page'   => -1,
                  'orderby'          => 'date',
                  'order'            => 'DESC',
                  'post_type'        => 'snhotel_thingstodo',
                  'post_status'      => 'publish',
                  );
                  $addonsFromDB = get_posts( $args );
                  foreach ( $addonsFromDB as $post ) : setup_postdata( $post );
                    if (has_post_thumbnail($post->ID)) {
                        $image = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
                        $imagePath = $image;
                    }
                ?>
                <a href="<?php echo get_post_meta($post->ID, 'siteurl', 1); ?>" target="_blank" class="display-block">
                  <div class="pull-left no-padding things-to-do" style="background-image:url(<?php echo $imagePath; ?>)">
    								<div class="gradient-overlay">
    									<p class="pull-right price">From <span class="text-bold"><?php echo edd_currency_filter(edd_format_amount(get_post_meta($post->ID, 'price', 1))); ?></span></p>
    									<p class="pull-left title margin-bottom-0"><?php echo $post->post_title; ?></p>
    								</div>
    							</div>
                </a>
              <?php
                endforeach;
                wp_reset_postdata();
              ?>
            </div>
          <div class="fake-right"></div>
        </div>
      </div>
    </div>
    <?php
      $settings = get_option( "snc_theme_settings" );
      $hotelname = esc_html( stripslashes( $settings['snc_hotelname'] ) );
      $add1 = esc_html( stripslashes( $settings["snc_add1"] ) );
  		$add2 = esc_html( stripslashes( $settings["snc_add2"] ) );
  		$city = esc_html( stripslashes( $settings["snc_city"] ) );
  		$state = esc_html( stripslashes( $settings["snc_state"] ) );
  		$pcode = esc_html( stripslashes( $settings["snc_pcode"] ) );
  		$country = esc_html( stripslashes( $settings["snc_country"] ) );
      $phone = esc_html( stripslashes( $settings["snc_phone"] ) );
      $email = esc_html( stripslashes( $settings["snc_email"] ) );
      $latitude = esc_html( stripslashes( $settings["snc_lat"] ) );
      $longitude = esc_html( stripslashes( $settings["snc_long"] ) );
    ?>
    <div class="row margin-top-15">
			<div class="col-xs-12 col-md-7">
				<h6 class="font-bold margin-bottom-15">Location Details</h6>
        <div id="confimationmap"></div>
			</div>
			<div class="col-xs-12 col-md-5 margin-top-30">
				<p class="hotel-location-icons hotel-address">
          <?php
          echo $hotelname.',';
          ?>
          <?php
            echo $add1.', ';
            if(!empty($add2)){ echo $add2.', ';}
            if(!empty($city)){ echo $city.', '; }
            if(!empty($state)){ echo $state.', '; }
            if(!empty($country)){  echo $country; }
          ?>
				</p>
				<p class="hotel-location-icons hotel-telephone"><?php echo $phone;?></p>
				<p class="hotel-location-icons hotel-email"><a href="mailto:<?php echo $email;?>"><?php echo $email;?></a></p>
				<p class="pull-left margin-right-10 hotel-location-icons hotel-driving-directions"><a href="#">Driving Directions</a></p>
				<p class="pull-left icon-parking"><a href="#">Parking Instructions</a></p>
			</div>
		</div>
    <div class="row margin-top-15">
      <div class="col-xs-12">
        <?php
          $agree_text  = edd_get_option( 'agree_text', '' );
      		$agree_label = edd_get_option( 'agree_label', __( 'Terms and Conditions', 'easy-digital-downloads' ) );
        ?>
    		<div id="edd_terms_agreement" class="margin-top-10">
    			<label for="edd_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
    			<div id="edd_terms">
    				<?php
    					echo wpautop( stripslashes( $agree_text ) );
    				?>
    			</div>
    		</div>
      </div>
    </div>
    <div class="row margin-top-15">
      <div class="col-xs-12">
        <?php
          $agree_text  = edd_get_option( 'policy_text', '' );
      		$agree_label = edd_get_option( 'policy_title', __( 'Policy', 'easy-digital-downloads' ) );
        ?>
    		<div id="edd_terms_agreement" class="margin-top-10 margin-bottom-20">
    			<label for="edd_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
    			<div id="edd_terms">
    				<?php
    					echo wpautop( stripslashes( $agree_text ) );
    				?>
    			</div>
    		</div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
  initMap();
});

var latitude = <?php echo $latitude; ?>;
var longitude = <?php echo $longitude; ?>;
// When you add a marker using a Place instead of a location, the Maps
// API will automatically add a 'Save to Google Maps' link to any info
// window associated with that marker.
function initMap() {
  var map = new google.maps.Map(document.getElementById('confimationmap'), {
    zoom: 17,
    center: {lat: latitude, lng: longitude}
  });

  var marker = new google.maps.Marker({
    map: map,
    // Define the place with a location, and a query string.
    place: {
      location: {lat: latitude, lng: longitude},
      query: '<?php echo $hotelname; ?>'
    },
    // Attributions help users find your site again.
    attribution: {
      source: '<?php echo $hotelname; ?>',
      webUrl: '<?php echo get_site_url(); ?>'
    }
  });

  // Construct a new InfoWindow.
  var infoWindow = new google.maps.InfoWindow({
    content: '<?php echo $hotelname; ?>'
  });

  // Opens the InfoWindow when marker is clicked.
  marker.addListener('click', function() {
    infoWindow.open(map, marker);
  });
}
</script>

<div class="col-xs-12 no-padding hide">
<div class="col-xs-12 no-padding border-1px-grey margin-bottom-10">
  <div class="col-xs-12 col-md-6 no-padding">
      <div class="col-xs-12 col-md-4">
        <?php do_action('edd_payment_receipt_before', $payment, $edd_receipt_args); ?>
        <p><strong><?php _e('Reservation ID', 'easy-digital-downloads'); ?>:</strong></p>
      </div>
      <div class="col-xs-12 col-md-8 no-padding">
        <div class="reservid"><strong><?php echo edd_get_reservation($payment->ID); ?></strong></div>
      </div>
  </div>
  <div class="col-xs-12 col-md-6 no-padding text-right">
      <div class="col-xs-12 col-md-12">
          <div class="edd_receipt_payment_status pull-right <?php echo strtolower($status); ?>">Pending</div>
          <strong class="pull-right"><?php _e('Payment Status', 'easy-digital-downloads'); ?>:</strong>
      </div>
  </div>
</div>
</hr>
<div class="col-md-12 margin-top-20 margin-bottom-10">
    <p><strong>Booking Confirmation</strong></p>
</div>
<div class="col-md-12 margin-bottom-10 border-1px-grey">
    <div class="confirmatn-pagetextcolor">
        Please print this aknowledement and present it to the property reception on arrival.A receipt will be emailed to you at the address entered during the booking process
    </div>
</div>
<div class="col-md-12 margin-top-20">
    <p><strong>Contact Details</strong></p>
</div>
<div class="col-md-12 no-padding margin-top-10 margin-bottom-20 border-1px-grey">
  <div class="col-md-5"><div class="textcolorred"><strong>Address</strong></div></div>
  <div class="col-md-4"><div class="textcolorred"><strong>Telephone</strong></div></div>
  <div class="col-md-3"><div class="textcolorred"><strong>Email</strong></div></div>
  <?php
    $settings = get_option( "snc_theme_settings" );
    $hotelname = esc_html( stripslashes( $settings['snc_hotelname'] ) );
    $add1 = esc_html( stripslashes( $settings["snc_add1"] ) );
		$add2 = esc_html( stripslashes( $settings["snc_add2"] ) );
		$city = esc_html( stripslashes( $settings["snc_city"] ) );
		$state = esc_html( stripslashes( $settings["snc_state"] ) );
		$pcode = esc_html( stripslashes( $settings["snc_pcode"] ) );
		$country = esc_html( stripslashes( $settings["snc_country"] ) );
    $phone = esc_html( stripslashes( $settings["snc_phone"] ) );
    $email = esc_html( stripslashes( $settings["snc_email"] ) );
  ?>
  <div class="col-md-5"><div class="confirmatn-pagetextcolor"><p>
    <?php
    echo $hotelname.',';
    ?>
    <br>
    <?php
      echo $add1.', ';
      if(!empty($add2)){ echo $add2.', ';}
      if(!empty($city)){ echo $city.', '; }
      if(!empty($state)){ echo $state.', '; }
      if(!empty($country)){  echo $country; }
    ?>
  </p></div></div>
  <div class="col-md-4"><div class="confirmatn-pagetextcolor"><p><?php echo $phone;?></p></div></div>
  <div class="col-md-3"><div class="confirmatn-pagetextcolor"><p><?php echo $email;?></p></div></div>
</div>
<div class="col-md-12">
  <p><strong>Booking summary</strong></p>
</div>
<div class="col-md-12 margin-top-10 margin-bottom-10 no-padding">
  <div class="col-md-5"><div class="textcolorred"><strong><?php _e('Name', 'easy-digital-downloads'); ?></strong></div></div>
  <div class="col-md-2"><div class="textcolorred"><strong><?php _e('Arrival Date', 'easy-digital-downloads'); ?></strong></div></div>
  <div class="col-md-2"><div class="textcolorred"><strong><?php _e('Departure Date', 'easy-digital-downloads'); ?></strong></div></div>
  <div class="col-md-2"><div class="textcolorred"><strong><?php _e('Nights', 'easy-digital-downloads'); ?></strong></div></div>
  <div class="col-md-1 text-right"><div class="textcolorred"><strong><?php _e('Total', 'easy-digital-downloads'); ?></strong></div></div>
</div>
<?php do_action('edd_payment_receipt_after_table', $payment, $edd_receipt_args); ?>
<?php if (filter_var($edd_receipt_args['products'], FILTER_VALIDATE_BOOLEAN)) : ?>
    <?php if ($cart) : ?>
        <?php foreach ($cart as $key => $item) : ?>

            <?php
            //print_r(json_encode($item));
            $item_title = $item['item_number']['options']['name'];
            $fromdatetime = strtotime($item['item_number']['options']['startdate']);
            $todatetime = strtotime($item['item_number']['options']['enddate']);
            $noofdays = $item['item_number']['options']['noofdays'];
            $fromdatetime = date('Y/m/d', $fromdatetime);
            $todatetime = date('Y/m/d', $todatetime);
            ?>
          <div class="col-xs-12 no-padding">
              <div class="col-md-5">

                  <?php
                  $price_id = edd_get_cart_item_price_id($item);
                  $download_files = edd_get_download_files($item['id'], $price_id);
                  ?>

                  <div class="edd_purchase_receipt_product_name">
                      <div class="confirmatn-pagetextcolor"> <p><?php echo esc_html($item_title); ?></p></div>
                  </div>

                  <?php if ($edd_receipt_args['notes']) : ?>
                      <div class="edd_purchase_receipt_product_notes"><?php echo wpautop(edd_get_product_notes($item['id'])); ?></div>
                  <?php endif; ?>

                  <?php if (edd_is_payment_complete($payment->ID) && edd_receipt_show_download_files($item['id'], $edd_receipt_args, $item)) : ?>
                      <ul class="edd_purchase_receipt_files">
                          <?php
                          if (!empty($download_files) && is_array($download_files)) :

                              foreach ($download_files as $filekey => $file) :

                                  $download_url = edd_get_download_file_url($meta['key'], $email, $filekey, $item['id'], $price_id);
                                  ?>
                                  <li class="edd_download_file">
                                      <a href="<?php echo esc_url($download_url); ?>" class="edd_download_file_link"><?php echo edd_get_file_name($file); ?></a>
                                  </li>
                                  <?php
                                  do_action('edd_receipt_files', $filekey, $file, $item['id'], $payment->ID, $meta);
                              endforeach;

                          elseif (edd_is_bundled_product($item['id'])) :

                              $bundled_products = edd_get_bundled_products($item['id']);

                              foreach ($bundled_products as $bundle_item) :
                                  ?>
                                  <li class="edd_bundled_product">
                                      <span class="edd_bundled_product_name"><?php echo get_the_title($bundle_item); ?></span>
                                      <ul class="edd_bundled_product_files">
                                          <?php
                                          $download_files = edd_get_download_files($bundle_item);

                                          if ($download_files && is_array($download_files)) :

                                              foreach ($download_files as $filekey => $file) :

                                                  $download_url = edd_get_download_file_url($meta['key'], $email, $filekey, $bundle_item, $price_id);
                                                  ?>
                                                  <li class="edd_download_file">
                                                      <a href="<?php echo esc_url($download_url); ?>" class="edd_download_file_link"><?php echo esc_html($file['name']); ?></a>
                                                  </li>
                                                  <?php
                                                  do_action('edd_receipt_bundle_files', $filekey, $file, $item['id'], $bundle_item, $payment->ID, $meta);

                                              endforeach;
                                          else :
                                              echo '<li>' . __('No downloadable files found for this bundled item.', 'easy-digital-downloads') . '</li>';
                                          endif;
                                          ?>
                                      </ul>
                                  </li>
                                  <?php
                              endforeach;

                          else :
                          // echo '<li>' . apply_filters( 'edd_receipt_no_files_found_text', __( 'No downloadable files found.', 'easy-digital-downloads' ), $item['id'] ) . '</li>';
                          endif;
                          ?>
                      </ul>
                  <?php endif; ?>

              </div>
              <div class="col-md-2">
                  <div class="confirmatn-pagetextcolor"> <p><?php echo $fromdatetime; ?></p></div>
              </div>
              <div class="col-md-2">
                  <div class="confirmatn-pagetextcolor"> <p><?php echo $todatetime; ?></p></div>
              </div>
              <div class="col-md-2">
                  <div class="confirmatn-pagetextcolor"><p><?php echo $noofdays; ?></p></div>
              </div>
              <div class="col-md-1 text-right">
                      <div class="confirmatn-pagetextcolor"><p> <?php echo edd_currency_filter(edd_format_amount($item['price'])); ?> </p></div>
              </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>
    <div class="col-xs-12 border-1px-grey"></div>
    <div class="col-xs-12 no-padding margin-top-20 margin-bottom-10">
        <div class="col-xs-12 col-md-7" >
        </div>
        <div class="col-xs-12 col-md-5 text-right">
          <div class="textcolorredlarge pull-right"><strong><?php echo edd_payment_amount($payment->ID); ?></strong></div><strong class="pull-right"><p>Total to be paid at hotel:</p></strong>
        </div>
    </div>
    </div>
    <div class="printbtn hide" >
        <a href="#" class="btn btn-danger" >Print</a>
        <a target="_blank" href="" id="btnSaveasPDF" class="btn btn-danger">Save as PDF</a>
    </div>







    <!--content for print-->



    <div id="printContents" class="font-display" style="display:none;">

        <?php
        ?>
        <?php
        if (has_post_thumbnail(get_the_ID())) {
            $image = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())); //the_post_thumbnail_url();//wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'archive-post-thumbnail');
            $imagePath = $image;
        } else {
            $imagePath = get_bloginfo('template_directory') . '/imgs/banner.png';
        }
        ?>
        <!--<div class="img">-->
        <div id="page-header" style="z-index:1;">
            <div class="bannerText">

                <div id="page-header" style=" background-image: url('<?php echo wpthumb($imagePath, 'width=1500&height=300&crop=1&resize=1'); ?>')">

                    <div class="container">
                        <div class="pageheader">
                            <i><h2><?php the_title(); ?></h2></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--     </div>-->
        <div class="containeraboutus">
            <div id="aboutus" style="z-index:200 ;position:relative;">
                <div class="container" >

                    <div class="jumbotron purchaseconfirmatn" >



                        <div class="col-md-2">
    <?php do_action('edd_payment_receipt_before', $payment, $edd_receipt_args); ?>
                            <?php if (filter_var($edd_receipt_args['payment_id'], FILTER_VALIDATE_BOOLEAN)) : ?>

                                <strong><p><?php _e('Reservation ID', 'easy-digital-downloads'); ?>:</p></strong>
                            </div>
                            <div class="col-md-5">
                                <div class="reservid"> <strong><?php echo edd_get_reservation($payment->ID); ?></strong></div>


    <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                                   <div class="Payment-Status">

                            <table>
                                <tr>
                                    <td>
<!--                                         <div class="col-md-6">-->
                                <div><strong><p><?php _e('Payment Status', 'easy-digital-downloads'); ?>:</p></strong></div>
<!--                            </div>-->
                                    </td>
                                     <td>
                                        <div class="edd_receipt_payment_status <?php echo strtolower($status); ?>"><p>Pending</p>


                                </div>
                                    </td>
                                </tr>
                            </table>


<!--                            <div class="col-md-1">


                            </div>-->


                        </div>
                        </div>



                        <div class="col-md-12">
                            <hr>
                        </div>


                        <div class="col-md-12">
                            <p><strong>Booking Confirmation</strong></p>
                        </div>     <div class="col-md-12">
                            <div class="confirmatn-pagetextcolor">
                                <p>Please print this aknowledement and present it to the property reception on arrival.A receipt will be emailed to you at the address entered during the booking process</p>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>

                        <div class="col-md-12">
                            <p><strong>Contact Details</strong></p>
                        </div>


                        <table>
                            <tr>
                                <td>   <div class="col-md-4"> <div class="textcolorred"><p>Address</p></div></div>

                                </td>
                                 <td>
                                      <div class="col-md-4">  <div class="textcolorred"><p>Telephone<p></div></div>
                                </td>
                                <td>
                                       <div class="col-md-4"><div class="textcolorred"><p>Email</p></div></div>
                                </td>

                            </tr>
                               <tr>
                                <td>
                                     <div class="col-md-4"> <div class="confirmatn-pagetextcolor"><p>22/13 NorthRoad,Ban Naviengkham,Luang Prabang,Lao PDR</p></div></div>
                                </td>
                                 <td>
                                                  <div class="col-md-4"> <div class="confirmatn-pagetextcolor"><p>+85671261888</p></div></div>
                                </td>
                                <td>
                                    <div class="col-md-4"> <div class="confirmatn-pagetextcolor"><p>Hellow@kiridara.com</p></div></div>
                                </td>

                            </tr>
                            </table>







                        <div class="col-md-12">
                            <hr>
                        </div>



                        <div class="col-md-12">     <p><strong>Booking summary</strong></p></div>

  <div class="col-md-12">
                          <table class="bookingsummary" >
                            <tr>
                                <td>  <div class="col-md-4"><div class="textcolorred"><p><?php _e('Name', 'easy-digital-downloads'); ?></p></div></div></td>
                                  <td> <div class="col-md-2"><div class="textcolorred"><p><?php _e('Arrival Date', 'easy-digital-downloads'); ?></p></div></div></td>
                                    <td> <div class="col-md-2"><div class="textcolorred"><p><?php _e('Departure Date', 'easy-digital-downloads'); ?></p></div></div></td>
                                      <td> <div class="col-md-2"><div class="textcolorred"><p><?php _e('Nights', 'easy-digital-downloads'); ?></p></div></div></td>
                                        <td>  <div class="col-md-2"><div class="textcolorred"><p><?php _e('Total', 'easy-digital-downloads'); ?></p></div></div></td>
                            </tr>












    <?php do_action('edd_payment_receipt_after_table', $payment, $edd_receipt_args); ?>

                        <?php if (filter_var($edd_receipt_args['products'], FILTER_VALIDATE_BOOLEAN)) : ?>

                            <?php if ($cart) : ?>
                                <?php foreach ($cart as $key => $item) : ?>

                                    <?php
                                    //print_r(json_encode($item));
                                    $item_title = $item['item_number']['options']['name'];
                                    $fromdatetime = strtotime($item['item_number']['options']['startdate']);
                                    $todatetime = strtotime($item['item_number']['options']['enddate']);
                                    $noofdays = $item['item_number']['options']['noofdays'];
                                    $fromdatetime = date('Y-m-d', $fromdatetime);
                                    $todatetime = date('Y-m-d', $todatetime);
                                    ?>

                                    <?php if (!apply_filters('edd_user_can_view_receipt_item', true, $item)) : ?>
                                        <?php continue; // Skip this item if can't view it  ?>
                                    <?php endif; ?>

                                    <?php if (empty($item['in_bundle'])) : ?>
                                        <tr>
                                            <td>
<!--                                        <div class="col-md-4">-->

                    <?php
                    $price_id = edd_get_cart_item_price_id($item);
                    $download_files = edd_get_download_files($item['id'], $price_id);
                    ?>

                                            <div class="edd_purchase_receipt_product_name">
                                                <div class="confirmatn-pagetextcolor"> <p><?php echo esc_html($item_title); ?></p></div>
                    <?php if (!is_null($price_id)) : ?>


                                                    <span class="edd_purchase_receipt_price_name">&nbsp;&ndash;&nbsp;<?php echo edd_get_price_option_name($item['id'], $price_id, $payment->ID); ?></span>
                    <?php endif; ?>
                                            </div>

                    <?php if ($edd_receipt_args['notes']) : ?>
                                                <div class="edd_purchase_receipt_product_notes"><?php echo wpautop(edd_get_product_notes($item['id'])); ?></div>
                                            <?php endif; ?>

                                            <?php if (edd_is_payment_complete($payment->ID) && edd_receipt_show_download_files($item['id'], $edd_receipt_args, $item)) : ?>
                                                <ul class="edd_purchase_receipt_files">
                                                <?php
                                                if (!empty($download_files) && is_array($download_files)) :

                                                    foreach ($download_files as $filekey => $file) :

                                                        $download_url = edd_get_download_file_url($meta['key'], $email, $filekey, $item['id'], $price_id);
                                                        ?>
                                                            <li class="edd_download_file">
                                                                <a href="<?php echo esc_url($download_url); ?>" class="edd_download_file_link"><?php echo edd_get_file_name($file); ?></a>
                                                            </li>
                                <?php
                                do_action('edd_receipt_files', $filekey, $file, $item['id'], $payment->ID, $meta);
                            endforeach;

                        elseif (edd_is_bundled_product($item['id'])) :

                            $bundled_products = edd_get_bundled_products($item['id']);

                            foreach ($bundled_products as $bundle_item) :
                                ?>
                                                            <li class="edd_bundled_product">
                                                                <span class="edd_bundled_product_name"><?php echo get_the_title($bundle_item); ?></span>
                                                                <ul class="edd_bundled_product_files">
                                <?php
                                $download_files = edd_get_download_files($bundle_item);

                                if ($download_files && is_array($download_files)) :

                                    foreach ($download_files as $filekey => $file) :

                                        $download_url = edd_get_download_file_url($meta['key'], $email, $filekey, $bundle_item, $price_id);
                                        ?>
                                                                            <li class="edd_download_file">
                                                                                <a href="<?php echo esc_url($download_url); ?>" class="edd_download_file_link"><?php echo esc_html($file['name']); ?></a>
                                                                            </li>
                                        <?php
                                        do_action('edd_receipt_bundle_files', $filekey, $file, $item['id'], $bundle_item, $payment->ID, $meta);

                                    endforeach;
                                else :
                                    echo '<li>' . __('No downloadable files found for this bundled item.', 'easy-digital-downloads') . '</li>';
                                endif;
                                ?>
                                                                </ul>
                                                            </li>
                                <?php
                            endforeach;

                        else :
                        // echo '<li>' . apply_filters( 'edd_receipt_no_files_found_text', __( 'No downloadable files found.', 'easy-digital-downloads' ), $item['id'] ) . '</li>';
                        endif;
                        ?>
                                                </ul>
                                                <?php endif; ?>
                                        </td>
<!--                                        </div>-->
<td>
<!--                                        <div class="col-md-2">-->
                                            <div class="confirmatn-pagetextcolor"> <p><?php echo $fromdatetime; ?></p></div>
<!--                                        </div>-->
</td>
<td>
<!--                                        <div class="col-md-2">-->
                                            <div class="confirmatn-pagetextcolor"> <p><?php echo $todatetime; ?></p></div>
<!--                                        </div>-->
</td>
<td>
<!--                                        <div class="col-md-2">-->
                                            <div class="confirmatn-pagetextcolor"><p><?php echo $noofdays; ?></p></div>
<!--                                        </div>-->
</td>
                                        <!-- <?php if (edd_use_skus()) : ?>
                                                                        <td><?php echo edd_get_download_sku($item['id']); ?></td>
                    <?php endif; ?>
                                        <?php if (edd_item_quantities_enabled()) { ?>
                                                                        <td><?php echo $item['quantity']; ?></td>
                                        <?php } ?> -->
                                        <td>
                                        <div class="col-md-2">
                                        <?php if (empty($item['in_bundle'])) : // Only show price when product is not part of a bundle  ?>
                                                <div class="confirmatn-pagetextcolor"><p> <?php echo edd_currency_filter(edd_format_amount($item['price'])); ?> </p></div>
                                            <?php endif; ?>
<!--                                        </div>-->
                                        </td>
                                        </tr>
                <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (( $fees = edd_get_payment_fees($payment->ID, 'item'))) : ?>
                                <?php foreach ($fees as $fee) : ?>
                                    <tr>
                                        <td class="edd_fee_label"><?php echo esc_html($fee['label']); ?></td>
                <?php if (edd_item_quantities_enabled()) : ?>
                                            <td></td>
                                        <?php endif; ?>
                                        <td class="edd_fee_amount"><?php echo edd_currency_filter(edd_format_amount($fee['amount'])); ?></td>
                                    </tr>
            <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php //endif; ?>
                                    <?php //endif; ?>
                        </table>
</div>

                    <div class="col-md-12">

                    </div>
                    <div class="col-md-12">
                        <hr>
                    </div>

 <div class="col-md-12">

                    </div>

                    <div class="col-md-8" >
                        &nbsp;
                    </div>
                    <div class="col-md-3" >  <strong><p>Total to be paid at hotel:</p></strong>

                    </div>
                    <div class="col-md-1" >

                        <div class="textcolorredlarge">  <strong><?php echo edd_payment_amount($payment->ID); ?></strong></div>


                    </div>




                </div>



            </div>
        </div>
    </div>



</div>
   <script type="text/javascript">
    $(document).ready(function () {
        $("#btnPrint").click(function () {
            var contents = $("#dvContents").html();
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            // $("#frame1").width(10000);
            // $("#frame1").height(10000);
            // $("#frame1")[0].setAttribute("width", "1000");
            // $('#frame1', window.parent.document).width('5000px');

            frame1.css({"width": "20000px", "height": "20000px"});
            frame1.css({"position": "absolute", "top": "-1000000px"});
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();

            //Create a new HTML document.
            frameDoc.document.write('<html><head><title>BOOKING CONFIRMED</title>');
            frameDoc.document.write('</head><body>');
            //Append the external CSS file.
            // frameDoc.document.write('<link href="<?php //bloginfo('template_directory');  ?>/assets/styles/fonts.css" rel="stylesheet" type="text/css" media="print"/>');
            frameDoc.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">');
            frameDoc.document.write('<link href="<?php bloginfo('template_directory'); ?>/assets/styles/print.css" rel="stylesheet" type="text/css"  media="print" />');
            //frameDoc.document.write('<link rel="stylesheet" href="<?php //bloginfo('template_directory');  ?>/style.css" type="text/css" media="print" />');

            //Append the DIV contents.
            // frameDoc.document.write($("#page-header").html())
            frameDoc.document.write(contents);
            // alert(contents);
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                frame1.remove();
            }, 500);
            return false;
        });
    });
</script>
