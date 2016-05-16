<?php
/**
 * This template is used to display the purchase summary with [edd_receipt]
 */
global $edd_receipt_args;

$payment   = get_post( $edd_receipt_args['id'] );

if( empty( $payment ) ) : ?>

	<div class="edd_errors edd-alert edd-alert-error">
		<?php _e( 'The specified receipt ID appears to be invalid', 'easy-digital-downloads' ); ?>
	</div>

<?php
return;
endif;

$meta      = edd_get_payment_meta( $payment->ID );
$cart      = edd_get_payment_meta_cart_details( $payment->ID, true );
$user      = edd_get_payment_meta_user_info( $payment->ID );
$email     = edd_get_payment_user_email( $payment->ID );
$status    = edd_get_payment_status( $payment, true );
$settings = get_option( "snc_theme_settings" );
$hotelCode = esc_html( stripslashes( $settings["snc_hotelid"] ) );
$hotelName = esc_html( stripslashes( $settings["snc_hotelname"] ) );
$startdate = edd_booking_startdate($payment->ID);
?>
<div id="dvContents" class="font-display">
<div class="margin-t20 font-display">
<h4>Thank you for booking in <?php echo $hotelName;?>! We are looking forward to welcome you on <strong><?php echo date('Y-m-d', $startdate); ?></strong></h4>
</br>
<strong>The booking is confirmed</strong>, You will find the reservation details on follows:
</div>
<table id="edd_purchase_receipt" class="margin-t20 font-display">
	<tbody>
		<?php do_action( 'edd_payment_receipt_before', $payment, $edd_receipt_args ); ?>
		<?php if ( filter_var( $edd_receipt_args['payment_id'], FILTER_VALIDATE_BOOLEAN ) ) : ?>
		<tr>
			<th class="edd_receipt_payment_status"><strong><?php _e( 'Resrvation ID', 'easy-digital-downloads' ); ?>:</strong></th>
			<th class="edd_receipt_payment_status"><?php echo edd_get_payment_number( $payment->ID ); ?></th>
		</tr>
		<?php endif; ?>
		<tr>
			<td class="edd_receipt_payment_status"><strong><?php _e( 'Payment Status', 'easy-digital-downloads' ); ?>:</strong></td>
			<td class="edd_receipt_payment_status <?php echo strtolower( $status ); ?>">  <?php echo $status; ?></td>
		</tr>

		<?php if ( filter_var( $edd_receipt_args['payment_key'], FILTER_VALIDATE_BOOLEAN ) ) : ?>
			<tr>
				<td><strong><?php _e( 'Payment Key', 'easy-digital-downloads' ); ?>:</strong></td>
				<td><?php echo get_post_meta( $payment->ID, '_edd_payment_purchase_key', true ); ?></td>
			</tr>
		<?php endif; ?>

		<!-- <?php if ( filter_var( $edd_receipt_args['payment_method'], FILTER_VALIDATE_BOOLEAN ) ) : ?>
			<tr>
				<td><strong><?php _e( 'Payment Method', 'easy-digital-downloads' ); ?>:</strong></td>
				<td><?php echo edd_get_gateway_checkout_label( edd_get_payment_gateway( $payment->ID ) ); ?></td>
			</tr>
		<?php endif; ?> -->
		<!-- <?php if ( filter_var( $edd_receipt_args['date'], FILTER_VALIDATE_BOOLEAN ) ) : ?>
		<tr>
			<td><strong><?php _e( 'Date', 'easy-digital-downloads' ); ?>:</strong></td>
			<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $meta['date'] ) ); ?></td>
		</tr>
		<?php endif; ?>

		<?php if ( ( $fees = edd_get_payment_fees( $payment->ID, 'fee' ) ) ) : ?>
		<tr>
			<td><strong><?php _e( 'Fees', 'easy-digital-downloads' ); ?>:</strong></td>
			<td>
				<ul class="edd_receipt_fees">
				<?php foreach( $fees as $fee ) : ?>
					<li>
						<span class="edd_fee_label"><?php echo esc_html( $fee['label'] ); ?></span>
						<span class="edd_fee_sep">&nbsp;&ndash;&nbsp;</span>
						<span class="edd_fee_amount"><?php echo edd_currency_filter( edd_format_amount( $fee['amount'] ) ); ?></span>
					</li>
				<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php endif; ?> -->

		<!-- <?php if ( filter_var( $edd_receipt_args['discount'], FILTER_VALIDATE_BOOLEAN ) && isset( $user['discount'] ) && $user['discount'] != 'none' ) : ?>
			<tr>
				<td><strong><?php _e( 'Discount(s)', 'easy-digital-downloads' ); ?>:</strong></td>
				<td><?php echo $user['discount']; ?></td>
			</tr>
		<?php endif; ?>

		<?php if( edd_use_taxes() ) : ?>
			<tr>
				<td><strong><?php _e( 'Tax', 'easy-digital-downloads' ); ?></strong></td>
				<td><?php echo edd_payment_tax( $payment->ID ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( filter_var( $edd_receipt_args['price'], FILTER_VALIDATE_BOOLEAN ) ) : ?>

			<tr>
				<td><strong><?php _e( 'Subtotal', 'easy-digital-downloads' ); ?></strong></td>
				<td>
					<?php echo edd_payment_subtotal( $payment->ID ); ?>
				</td>
			</tr> -->

			<!-- <tr>
				<td><strong><?php _e( 'Total Price', 'easy-digital-downloads' ); ?>:</strong></td>
				<td><?php echo edd_payment_amount( $payment->ID ); ?></td>
			</tr>

		<?php endif; ?> -->

		<?php do_action( 'edd_payment_receipt_after', $payment, $edd_receipt_args ); ?>
	</tbody>
</table>
<?php do_action( 'edd_payment_receipt_after_table', $payment, $edd_receipt_args ); ?>

<?php if ( filter_var( $edd_receipt_args['products'], FILTER_VALIDATE_BOOLEAN ) ) : ?>

	<h4 class="font-display"><?php echo apply_filters( 'edd_payment_receipt_products_title', __( 'Room type(s) booked', 'easy-digital-downloads' ) ); ?></h4>

	<table id="edd_purchase_receipt_products" class="table font-display table-bordered">
		<thead>
			<th><?php _e( 'Name', 'easy-digital-downloads' ); ?></th>
			<th><?php _e( 'Arrival Date', 'easy-digital-downloads' ); ?></th>
			<th><?php _e( 'Departure Date', 'easy-digital-downloads' ); ?></th>
			<th><?php _e( 'Nights', 'easy-digital-downloads' ); ?></th>
			<th><?php _e( 'Product Total', 'easy-digital-downloads' ); ?></th>
		</thead>

		<tbody>
		<?php if( $cart ) : ?>
			<?php foreach ( $cart as $key => $item ) : ?>

				<?php
				//print_r(json_encode($item));
				$item_title = $item['item_number']['options']['name'];
				$fromdatetime = strtotime($item['item_number']['options']['startdate']);
				$todatetime = strtotime($item['item_number']['options']['enddate']);
				$noofdays = $item['item_number']['options']['noofdays'];
				$fromdatetime = date('Y-m-d', $fromdatetime);
				$todatetime = date('Y-m-d', $todatetime);
				?>

				<?php if( ! apply_filters( 'edd_user_can_view_receipt_item', true, $item ) ) : ?>
					<?php continue; // Skip this item if can't view it ?>
				<?php endif; ?>

				<?php if( empty( $item['in_bundle'] ) ) : ?>
				<tr>
					<td>

						<?php
						$price_id       = edd_get_cart_item_price_id( $item );
						$download_files = edd_get_download_files( $item['id'], $price_id );
						?>

						<div class="edd_purchase_receipt_product_name">
							<?php echo esc_html( $item_title ); ?>
							<?php if( ! is_null( $price_id ) ) : ?>
							<span class="edd_purchase_receipt_price_name">&nbsp;&ndash;&nbsp;<?php echo edd_get_price_option_name( $item['id'], $price_id, $payment->ID ); ?></span>
							<?php endif; ?>
						</div>

						<?php if ( $edd_receipt_args['notes'] ) : ?>
							<div class="edd_purchase_receipt_product_notes"><?php echo wpautop( edd_get_product_notes( $item['id'] ) ); ?></div>
						<?php endif; ?>

						<?php
						if( edd_is_payment_complete( $payment->ID ) && edd_receipt_show_download_files( $item['id'], $edd_receipt_args, $item ) ) : ?>
						<ul class="edd_purchase_receipt_files">
							<?php
							if ( ! empty( $download_files ) && is_array( $download_files ) ) :

								foreach ( $download_files as $filekey => $file ) :

									$download_url = edd_get_download_file_url( $meta['key'], $email, $filekey, $item['id'], $price_id );
									?>
									<li class="edd_download_file">
										<a href="<?php echo esc_url( $download_url ); ?>" class="edd_download_file_link"><?php echo edd_get_file_name( $file ); ?></a>
									</li>
									<?php
									do_action( 'edd_receipt_files', $filekey, $file, $item['id'], $payment->ID, $meta );
								endforeach;

							elseif( edd_is_bundled_product( $item['id'] ) ) :

								$bundled_products = edd_get_bundled_products( $item['id'] );

								foreach( $bundled_products as $bundle_item ) : ?>
									<li class="edd_bundled_product">
										<span class="edd_bundled_product_name"><?php echo get_the_title( $bundle_item ); ?></span>
										<ul class="edd_bundled_product_files">
											<?php
											$download_files = edd_get_download_files( $bundle_item );

											if( $download_files && is_array( $download_files ) ) :

												foreach ( $download_files as $filekey => $file ) :

													$download_url = edd_get_download_file_url( $meta['key'], $email, $filekey, $bundle_item, $price_id ); ?>
													<li class="edd_download_file">
														<a href="<?php echo esc_url( $download_url ); ?>" class="edd_download_file_link"><?php echo esc_html( $file['name'] ); ?></a>
													</li>
													<?php
													do_action( 'edd_receipt_bundle_files', $filekey, $file, $item['id'], $bundle_item, $payment->ID, $meta );

												endforeach;
											else :
												echo '<li>' . __( 'No downloadable files found for this bundled item.', 'easy-digital-downloads' ) . '</li>';
											endif;
											?>
										</ul>
									</li>
									<?php
								endforeach;

							else :
								echo '<li>' . apply_filters( 'edd_receipt_no_files_found_text', __( 'No downloadable files found.', 'easy-digital-downloads' ), $item['id'] ) . '</li>';
							endif; ?>
						</ul>
						<?php endif; ?>

					</td>
					<td>
						<?php echo $fromdatetime; ?>
					</td>
					<td>
						<?php echo $todatetime; ?>
					</td>
					<td>
						<?php echo $noofdays; ?>
					</td>
					<!-- <?php if ( edd_use_skus() ) : ?>
						<td><?php echo edd_get_download_sku( $item['id'] ); ?></td>
					<?php endif; ?>
					<?php if ( edd_item_quantities_enabled() ) { ?>
						<td><?php echo $item['quantity']; ?></td>
					<?php } ?> -->
					<td>
						<?php if( empty( $item['in_bundle'] ) ) : // Only show price when product is not part of a bundle ?>
							<?php echo edd_currency_filter( edd_format_amount( $item[ 'price' ] ) ); ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if ( ( $fees = edd_get_payment_fees( $payment->ID, 'item' ) ) ) : ?>
			<?php foreach( $fees as $fee ) : ?>
				<tr>
					<td class="edd_fee_label"><?php echo esc_html( $fee['label'] ); ?></td>
					<?php if ( edd_item_quantities_enabled() ) : ?>
						<td></td>
					<?php endif; ?>
					<td class="edd_fee_amount"><?php echo edd_currency_filter( edd_format_amount( $fee['amount'] ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>

	</table>
	Total to be paid at hotel: <?php echo edd_payment_amount( $payment->ID ); ?>
	</br>
	</br>
	</br>
	<a href="#" id="btnPrint" class="btn btn-danger">Print</a>
	<!-- <a href="#" id="btnSaveasPDF" class="btn btn-danger">Save as PDF</a>
	<a href="#" id="btnEmail" class="btn btn-danger">Email</a> -->
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("#btnPrint").click(function () {
        var contents = $("#dvContents").html();
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({ "position": "absolute", "top": "-1000000px" });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html><head><title>DIV Contents</title>');
        frameDoc.document.write('</head><body>');
        //Append the external CSS file.
				frameDoc.document.write('<link href="<?php bloginfo('template_directory'); ?>/assets/styles/fonts.css" rel="stylesheet" type="text/css" />');
				frameDoc.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">');
        frameDoc.document.write('<link href="<?php bloginfo('template_directory'); ?>/assets/styles/print.css" rel="stylesheet" type="text/css" />');
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
