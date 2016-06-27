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

$meta = edd_get_payment_meta($payment->ID);
$cart = edd_get_payment_meta_cart_details($payment->ID, true);
$user = edd_get_payment_meta_user_info($payment->ID);
$email = edd_get_payment_user_email($payment->ID);
$status = edd_get_payment_status($payment, true);
$settings = get_option("snc_theme_settings");
$hotelCode = esc_html(stripslashes($settings["snc_hotelid"]));
$hotelName = esc_html(stripslashes($settings["snc_hotelname"]));
$startdate = edd_booking_startdate($payment->ID);
?>
<div class="col-xs-12 no-padding" id="dvContents">
<div class="col-xs-12 no-padding border-1px-grey margin-bottom-10">
  <div class="col-xs-12 col-md-6 no-padding">
      <div class="col-xs-12 col-md-3">
        <?php do_action('edd_payment_receipt_before', $payment, $edd_receipt_args); ?>
        <p><strong><?php _e('Reservation ID', 'easy-digital-downloads'); ?>:</strong></p>
      </div>
      <div class="col-xs-12 col-md-9 no-padding">
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

    <div class="col-xs-12 border-1px-grey"></div>
    <div class="col-xs-12 no-padding margin-top-20 margin-bottom-10">
        <div class="col-xs-12 col-md-7" >
        </div>
        <div class="col-xs-12 col-md-5 text-right">
          <div class="textcolorredlarge pull-right"><strong><?php echo edd_currency_filter(edd_format_amount(edd_payment_amount($payment->ID))); ?></strong></div><strong class="pull-right"><p>Total to be paid at hotel:</p></strong>
        </div>
    </div>
    </div>
    <div class="printbtn" >
        <a href="#" id="btnPrint" class="btn btn-danger" >Print</a>
        <a target="_blank" href="<?php echo esc_url( edd_pdf_invoices()->get_pdf_invoice_url( $payment->ID ) );     ?>" id="btnSaveasPDF" class="btn btn-danger">Save as PDF</a>
        <!-- <a href="#" id="btnEmail" class="btn btn-danger">Email</a> -->
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#btnPrint").click(function () {
                var contents = $(".pagecontent-wrapper").html();
                var frame1 = $('<iframe />');
                frame1[0].name = "frame1";
                frame1.css({"position": "absolute", "top": "-1000000px"});
                $("body").append(frame1);
                var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
                frameDoc.document.open();
                //Create a new HTML document.
                frameDoc.document.write('<html><head><title>DIV Contents</title>');
                frameDoc.document.write('</head><body>');
                //Append the external CSS file.
                frameDoc.document.write('<link href="<?php bloginfo('template_directory'); ?>/assets/styles/fonts.css" rel="stylesheet" type="text/css" />');
                frameDoc.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">');
                frameDoc.document.write('<link href="<?php bloginfo('template_directory'); ?>/style.css" rel="stylesheet" type="text/css" />');
                //Append the DIV contents.
                // frameDoc.document.write($("#page-header").html())
                frameDoc.document.write(contents);
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
<?php endif; ?>
