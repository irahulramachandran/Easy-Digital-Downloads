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
//edd_email_purchase_receipt_for_user( $payment->ID );
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
$useremail = edd_get_payment_booker_email($payment->ID);
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
<div class="modal fade" id="cancelPopup" role="dialog">
  <div class="modal-dialog modal-sm popupCancel">
    <!-- Modal content-->
    <div class="modal-content">
  	<div class="modal-header cancelHeader">
  	  <button type="button" class="close" data-dismiss="modal">&times;</button>
  	  <h4 class="modal-title">Modify Email ID</h4>
  	</div>
  	<div class="modal-body">
  	  Email ID: <input class="form-control" id="booker_email" value="<?php echo $useremail; ?>" />
  	</div>
  	<div class="modal-footer">
  	  <button type="button" class="btn btn-yes popBtn" data-dismiss="modal">Cancel</button>
  	  <button type="button" class="btn btn-danger popBtn modify_emailid" data-postid="<?php echo $payment->ID; ?>" data-dismiss="modal">Modify</button>
  	</div>
    </div>
  </div>
</div>
<div class="container-fluid no-padding" id="dvContents">
  <img src="<?php echo wpthumb($imageURL, 'width=1500&height=500&crop=1');?>" style="width:100%;" class="heroImg" alt="Booking confirmation" />
  <div class="hero" style="background-image:url(<?php echo $imageURL;?>); background-size:cover;">
		<div class="overlay">
		</div>
    <!-- <div class="container">
    </div> -->
	</div>
  <?php
    //the_content();
    //echo get_stylesheet_directory();
    include( get_stylesheet_directory() . '/templates/template-navbar.php');
  ?>
  <h2 class="pageheader-print">BOOKING CONFIRMATION <?php echo edd_get_reservation($payment->ID); ?></h2>
  <div class="container main-container">
    <div class="row">
      <div class="col-xs-12 confirmation-header margin-bottom-10 padding-bottom-10">
        <h2 class="pageheader pull-left no-margin hidden-xs hidden-sm">BOOKING CONFIRMATION <?php echo edd_get_reservation($payment->ID); ?></h2>
        <h2 class="padding-left-15 mobile-header hidden-md hidden-lg margin-top-0">BOOKING CONFIRMATION ID: <?php echo edd_get_reservation($payment->ID); ?></h2>
  			<div class="col-xs-5 pull-right no-padding">
  				<div class="pull-right confirmation-actions-container">
  					<a href="#" class="pull-left confirmation-action-btn btn-print" id="btnPrint"></a>
  					<a class="pull-left confirmation-action-btn btn-download" href="<?php echo esc_url( edd_pdf_invoices()->get_pdf_invoice_url( $payment->ID ) ); ?>" ></a>
  					<a href="#" class="pull-left confirmation-action-btn btn-share"></a>
            <ul class="list-unstyled social-share-icons recipt">
              <li><a href="https://www.facebook.com/sharer/sharer.php" target="_blank" class="social-share-icon fb display-block"></a></li>
              <li><a href="https://twitter.com/home?status=" target="_blank" class="social-share-icon twitter display-block"></a></li>
            </ul>
  				</div>
  			</div>
      </div>
			<div class="col-xs-12 margin-bottom-15 confirmation-content print-100">
				<p class="pull-left margin-bottom-15"><?php echo $bookingmessage; ?></p>
			</div>
		</div>
    <div class="row">
				<div class="col-xs-12 confirmation-summary">
					<span class="font-bold margin-bottom-5 margin-top-0 hidden-xs hidden-sm">Booking Summary</span>
          <?php
          $i = 0;
            if ($cart) : ?>
              <?php foreach ($cart as $key => $item) : ?>
                  <?php
                  // print_r(json_encode($item));
                  $item_title = $item['item_number']['options']['roomtypename'];
                  $rateplan_title = $item['item_number']['options']['name'];
                  $fromdatetime = strtotime($item['item_number']['options']['startdate']);
                  $todatetime = strtotime($item['item_number']['options']['enddate']);
                  $quantity = intval($item['item_number']['options']['quantity']);
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
                      <img src="<?php echo $imgurl;?>" class="heroImg" style="width:100%;" alt="" />
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
          						<div class="col-xs-12 no-padding margin-top-10 arrvial-departure-container">
          							<div class="pull-left arrival">
          								<div class="pull-left">
          									<span class="font-bold">Arrival Date</span>
          									<p class="arrival-date"><?php echo $fromdatetime; ?></p>
          								</div>
          								<!-- <div class="pull-right icon-thunder-rain arrival-weather-icon"></div> -->
          							</div>
          							<div class="pull-right departure">
          								<div class="pull-left">
          									<span class="font-bold display-block text-right">Departure Date</span>
          									<p class="arrival-date display-block text-right"><?php echo $todatetime; ?></p>
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

          						<div class="col-xs-12 no-padding margin-top-10 padding-bottom-10 room-total  <?php echo $className; ?>">
          							<span class="pull-left font-bold">Room Total</span>
          							<span class="pull-right font-bold display-block text-right"><?php echo edd_currency_filter(edd_format_amount(floatval($roomprice*$quantity))); ?></span>
          						</div>
                      <?php if($addonTotal > 0){
                        ?>
                        <div class="col-xs-12 no-padding margin-top-10 padding-bottom-10 room-total">
            							<span class="pull-left font-bold">Addon Total</span>
            							<span class="pull-right font-bold  display-block text-right"><?php echo edd_currency_filter(edd_format_amount(floatval($quantity*$addonTotal))); ?></span>
            						</div>
                        <?php
                        }
                      ?>
                      <?php
                      $i++;
                      if($i == $count){
                        ?>
                        <div class="col-xs-12 no-padding margin-top-10 padding-top-10">
            							<span class="pull-left font-bold">Included Tax</span>
            							<span class="pull-right font-bold"><?php
                          $cart_tax = (float) edd_ibe_calculate_tax(edd_get_payment_amount( $payment->ID ));
                          echo edd_currency_filter( edd_format_amount( $cart_tax ) ); ?></span>
            						</div>
                        <div class="col-xs-12 no-padding margin-top-10 padding-top-10">
            							<span class="pull-left font-bold">Total to be paid at hotel</span>
            							<span class="pull-right font-bold"><?php echo edd_payment_amount($payment->ID); ?></span>
            						</div>
            						<div class="row margin-top-10 confirmation-action-mobile-container">
            							<a href="<?php echo esc_url( edd_pdf_invoices()->get_pdf_invoice_url( $payment->ID ) ); ?>" class="col-xs-8 btn-danger download-btn-mobile">DOWNLOAD</a>
            							<a href="#" class="col-xs-4 btn-secondary download-btn-mobile share-btn-mobile">SHARE</a>
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
					<span class="font-bold margin-bottom-15">Enhance Your Stay</span>
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
              <img class="enhance-stay-img" src="<?php echo $imagePath; ?>" />
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
					<span class="font-bold margin-bottom-15">Top four things to do in Wollongong</span>
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
                <img class="enhance-stay-img" src="<?php echo $imagePath; ?>" />
                <a href="<?php echo get_post_meta($post->ID, 'siteurl', 1); ?>" target="_blank" class="display-block enhance-link">
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
    <div class="row margin-top-15 location-details">
			<div class="col-xs-12 col-md-7">
				<span class="font-bold margin-bottom-15">Location Details</span>
        <?php
          $map_image  = edd_get_option( 'map_image', '' );
          if(!empty($map_image)){
        ?>
        <img border="0" src="<?php echo $map_image; ?>" class="heroImg map_image">
        <?php
          }
        ?>
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
				<p class="pull-left margin-right-10 hotel-location-icons hotel-driving-directions"><a href="https://maps.google.com/?daddr=<?php echo $hotelname; ?>" target="_blank">Driving Directions</a></p>
				<p class="pull-left icon-parking"><a href="#" data-toggle="modal" data-target="#parking_div">Parking Instructions</a></p>
			</div>
		</div>
    <div id="parking_div" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body">
        <p><?php echo edd_get_option( 'parking_instructions', '' ) ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    <div class="row margin-top-15">
      <div class="col-xs-12">
        <?php
          $agree_text  = edd_get_option( 'agree_text', '' );
      		$agree_label = edd_get_option( 'agree_label', __( 'Terms and Conditions', 'easy-digital-downloads' ) );
        ?>
    		<div id="edd_terms_agreement" class="margin-top-10 termsnagreement">
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
    		<div id="edd_terms_agreement" class="margin-top-10 margin-bottom-20 policies">
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
  $(".btn-share").click(function(e){
      $("ul.list-unstyled.social-share-icons.recipt").show();
      return false;
  });

  $(".modify_emailid").click(function(e){
    var postId = $(this).data("postid");

    var Email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if ($("#booker_email").val() != "" && Email.test($('#booker_email').val()) == true) {

      var data   = {
				action: 'modify_email',
				emailid: $("#booker_email").val(),
        post_id: postId
			};

  		 $.ajax({
  			type: "POST",
  			data: data,
  			dataType: "json",
  			url: edd_scripts.ajaxurl,
  			xhrFields: {
  				withCredentials: true
  			},
  			success: function (response) {
          if(response == true){
            $(".modifiedemail").text($("#booker_email").val());
            return true;
          }
          else{
            e.stopPropagation();
            return false;
          }
        }
        }).fail(function (response) {
    			if ( window.console && window.console.log ) {
    				console.log( response );
    			}
    		}).done(function (response) {

    		});

    }
    else{
      $("#booker_email").parent(".modal-body").addClass("has-error");
      e.stopPropagation();
      return false;
    }
  });
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
          frameDoc.document.write('<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/styles/bootstrap.min.css" type="text/css" media="print" />');
          frameDoc.document.write('<link href="<?php bloginfo('template_directory'); ?>/assets/styles/print_min.css" rel="stylesheet" type="text/css"  media="print" />');
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
