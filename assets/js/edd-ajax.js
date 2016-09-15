var edd_scripts;
jQuery(document).ready(function ($) {

	// Hide unneeded elements. These are things that are required in case JS breaks or isn't present
	$('.edd-no-js').hide();
	$('a.edd-add-to-cart').addClass('edd-has-js');

	// var creditly = Creditly.initialize('.payment-information .edd_expiry', '.payment-information .edd_number', '.payment-information .edd_cvc', '.payment-information .card-type');

	_bindQuantityChangeEvent();

	// Send Remove from Cart requests
	$('body').on('click.eddRemoveFromCart', '.edd-remove-from-cart', function (event) {
		var $this  = $(this),
			item   = $this.data('cart-item'),
			action = $this.data('action'),
			id     = $this.data('download-id'),
			data   = {
				action: action,
				cart_item: item
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
				console.log(edd_scripts.position_in_cart);
				if (response.removed) {
					if ( parseInt( edd_scripts.position_in_cart, 10 ) === parseInt( item, 10 ) ) {
						window.location = window.location;
						return false;
					}

					// Remove the selected cart item
					$('.edd-cart').find("[data-cart-item='" + item + "']").parents(".edd-cart-item").remove();

					//Reset the data-cart-item attributes to match their new values in the EDD session cart array
					var cart_item_counter = 0;
					$('.edd-cart').find("[data-cart-item]").each(function(){
						$(this).attr('data-cart-item', cart_item_counter);
						cart_item_counter = cart_item_counter + 1;
					});

					// Check to see if the purchase form(s) for this download is present on this page
					if( $( '[id^=edd_purchase_' + id + ']' ).length ) {
						$( '[id^=edd_purchase_' + id + '] .edd_go_to_checkout' ).hide();
						$( '[id^=edd_purchase_' + id + '] a.edd-add-to-cart' ).show().removeAttr('data-edd-loading');
						if ( edd_scripts.quantities_enabled == '1' ) {
							$( '[id^=edd_purchase_' + id + '] .edd_download_quantity_wrapper' ).show();
						}
					}

					$('span.edd-cart-quantity').text( response.cart_quantity );
					$('body').trigger('edd_quantity_updated', [ response.cart_quantity ]);
					if ( edd_scripts.taxes_enabled ) {
						$('.cart_item.edd_subtotal span').html( response.subtotal );
						$('.cart_item.edd_cart_tax span').html( response.tax );
					}

					$('.cart_item.edd_total span').html( response.total );
					$(".edd_cart_roomtotal span").html(response.roomtotal);
					$(".edd_cart_addontotal span").html(response.addontotal);

					if( response.cart_quantity == 0 ) {
						$("#edd_checkout_wrap").find(".col-xs-12.no-padding").first().html("");
						$("#edd_checkout_wrap").find(".col-xs-12.no-padding").first().html(edd_scripts.empty_cart_message);
						if( edd_scripts.is_checkout == '1'){
							window.location = site_url+'/accommodation/';
						}
						// $('.edd-cart li').hide();
						// $('.edd-cart').append('<li class="cart_item empty cart-item-row">' + edd_scripts.empty_cart_message + '</li>');
						$(".mini-cart-wrapper .reservation-summary-header").fadeOut("fast");
						$(".mini-cart-wrapper .edd-cart").fadeOut("fast");
					}
					else{
						$(".mini-cart-wrapper .reservation-summary-header .item-count").html( response.cart_quantity );
						$(".mini-cart-wrapper .reservation-summary-header .total-price").html( response.total );
						$(".mini-cart-wrapper .reservation-summary-header").fadeIn("fast");
					}
					_bindQuantityChangeEvent();
					$('body').trigger('edd_cart_item_removed', [ response ]);
				}
			}
		}).fail(function (response) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		}).done(function (response) {

		});

		return false;
	});

	//Rate button
	$(".book-from-rate").click(function(){
        //alert('first');
		$(this).parents(".roomrow").find('.table-body-row:first .table-body-column a.edd-add-to-cart:last').click();
		//});
    });

	// Send Add to Cart request
	$('body').on('click.eddAddToCart', '.edd-add-to-cart', function (e) {

		e.preventDefault();

		var $this = $(this), form = $this.closest('form');

		// Disable button, preventing rapid additions to cart during ajax request
		$this.prop('disabled', true);

		// var $spinner = $this.find('.edd-loading');
		// var container = $this.closest('div');
		//
		// var spinnerWidth  = $spinner.width(),
		// 	spinnerHeight = $spinner.height();
		//
		// // Show the spinner
		// $this.attr('data-edd-loading', '');
		//
		// $spinner.css({
		// 	'margin-left': spinnerWidth / -2,
		// 	'margin-top' : spinnerHeight / -2
		// });

		$("#loading").show();

		var form           = $this.parents('form').last();
		var download       = $this.data('download-id');
		var variable_price = $this.data('variable-price');
		var price_mode     = $this.data('price-mode');
		var directcheckout = $this.data("directcheckout");
		var ratesperdate = $this.data('ratesperdate');
		var selectedAddons = $this.parents(".table-body-row").find(".addons-dropdown").val();
		if(selectedAddons != undefined){
			selectedAddons = selectedAddons.join();
		}
		else{
			selectedAddons = "";
		}

		var item_price_ids = [];
		var free_items     = true;

		if( variable_price == 'yes' ) {

			if ( form.find('.edd_price_option_' + download).is('input:hidden') ) {
				item_price_ids[0] = $('.edd_price_option_' + download, form).val();
				if ( form.find('.edd-submit').data('price') && form.find('.edd-submit').data('price') > 0 ) {
					free_items = false;
				}
			} else {
				if( ! form.find('.edd_price_option_' + download + ':checked', form).length ) {
					 // hide the spinner
					 $("#loading").hide();
					// $this.removeAttr( 'data-edd-loading' );
					alert( edd_scripts.select_option );
					return;
				}

				form.find('.edd_price_option_' + download + ':checked', form).each(function( index ) {
					item_price_ids[ index ] = $(this).val();

					// If we're still only at free items, check if this one is free also
					if ( true === free_items ) {
						var item_price = $(this).data('price');
						if ( item_price && item_price > 0 ) {
							// We now have a paid item, we can't use add_to_cart
							free_items = false;
						}
					}

				});
			}

		} else {
			item_price_ids[0] = download;
			if ( $this.data('price') && $this.data('price') > 0 ) {
				free_items = false;
			}
		}

		// If we've got nothing but free items being added, change to add_to_cart
		if ( free_items ) {
			form.find('.edd_action_input').val('add_to_cart');
		}

		if( 'straight_to_gateway' == form.find('.edd_action_input').val() ) {
			form.submit();
			return true; // Submit the form
		}

		var action = $this.data('action');
		var data   = {
			action: action,
			download_id: download,
			price_ids : item_price_ids,
			ratesperdate: ratesperdate,
			selectedAddons: selectedAddons,
			post_data: $(form).serialize()
		};

		console.log(data);

		$.ajax({
			type: "POST",
			data: data,
			dataType: "json",
			url: edd_scripts.ajaxurl,
			xhrFields: {
				withCredentials: true
			},
			success: function (response) {
				console.log(response);
				if( edd_scripts.redirect_to_checkout == '1' && form.find( '#edd_redirect_to_checkout' ).val() == '1' ) {

					window.location = edd_scripts.checkout_page;

				}
				else if(directcheckout == '1'){
					window.location = edd_scripts.checkout_page;
				} else {

					// // Add the new item to the cart widget
					if ( edd_scripts.taxes_enabled === '1' ) {
						$('.cart_item.edd_subtotal').show();
						$('.cart_item.edd_cart_tax').show();
					}

					$('.cart_item.edd_total').show();
					$('.cart_item.edd_checkout').show();

					if ($('.cart_item.empty').length) {
						$(response.cart_item).insertBefore('.edd_cart_roomtotal:first');
						$(".edd_cart_roomtotal,.edd_cart_addontotal").show();
						$('.cart_item.empty').hide();
					} else {
						$(response.cart_item).insertBefore('.edd_cart_roomtotal:first');
					}

					// Update the totals
					if ( edd_scripts.taxes_enabled === '1' ) {
						// $('.edd-cart-meta.edd_subtotal span').html( response.subtotal );
						$('.edd-cart-meta.edd_cart_tax span').html( response.tax );
					}

					$('.edd-cart-meta.edd_total span').html( response.total );
					$(".edd_cart_roomtotal span").html(response.roomtotal);
					$(".edd_cart_addontotal span").html(response.addontotal);

					if(response.cart_quantity != "0"){
						$(".mini-cart-wrapper .reservation-summary-header .item-count").html( response.cart_quantity );
						$(".mini-cart-wrapper .reservation-summary-header .total-price").html( response.total );
						$(".mini-cart-wrapper .reservation-summary-header").fadeIn("fast");

						$this.parents(".table-body-row").find('.addons-dropdown').val(undefined);
						$this.parents(".table-body-row").find(".multiselect-container input[type='checkbox']").removeAttr("checked");
						$this.parents(".table-body-row").find(".multiselect-selected-text").text("Select Addons");
						$this.parents(".table-body-row").find('.addons-dropdown').change();
						// $(dropdown).parent(".col-xs-12").parent(".col-xs-12").find(".totalprice").html(jsonData[i].rate);
						// $(".edd-add-to-cart[data-download-id='"+download+"']").attr("value");
					}
					else{
						$(".mini-cart-wrapper .reservation-summary-header").fadeOut("fast");
						$(".mini-cart-wrapper .edd-cart").fadeOut("fast");
					}

					// Update the cart quantity
					var items_added = $( '.edd-cart-item-title', response.cart_item ).length;

					$('span.edd-cart-quantity').each(function() {
						$(this).text(response.cart_quantity);
						$('body').trigger('edd_quantity_updated', [ response.cart_quantity ]);
					});
					//
					// // Show the "number of items in cart" message
					if ( $('.edd-cart-number-of-items').css('display') == 'none') {
						$('.edd-cart-number-of-items').show('slow');

					}
					$("#loading").hide();
					//
					// if( variable_price == 'no' || price_mode != 'multi' ) {
					// 	// Switch purchase to checkout if a single price item or variable priced with radio buttons
					// 	// $('a.edd-add-to-cart', container).toggle();
					// 	// $('.edd_go_to_checkout', container).css('display', 'inline-block');
					// }
					//
					// $this.parents("td").attr("colspan","2");
					// $this.parents("td")
					// $this.parents("td").next("td").remove();
					// $this.parents("tr").find("a.btn-danger").fadeOut();
					// $this.parent("td").html("<a href='"+edd_scripts.checkout_page+"' class='btn btn-danger btn-sm btn-checkout'>Checkout</a>");
					// if ( price_mode == 'multi' ) {
					// 	// remove spinner for multi
					// }
					//
					// // Update all buttons for same download
					// if( $( '.edd_download_purchase_form' ).length && ( variable_price == 'no' || ! form.find('.edd_price_option_' + download).is('input:hidden') ) ) {
					// 	var parent_form = $('.edd_download_purchase_form *[data-download-id="' + download + '"]').parents('form');
					// 	$( 'a.edd-add-to-cart', parent_form ).hide();
					// 	if( price_mode != 'multi' ) {
					// 		parent_form.find('.edd_download_quantity_wrapper').slideUp();
					// 	}
					// 	$( '.edd_go_to_checkout', parent_form ).show().removeAttr( 'data-edd-loading' );
					// }

					_bindQuantityChangeEvent();
					// if( response != 'incart' ) {
					// 	// Show the added message
					// 	// $('.edd-cart-added-alert', container).fadeIn();
					// 	// setTimeout(function () {
					// 	// 	$('.edd-cart-added-alert', container).fadeOut();
					// 	// }, 3000);
					// }

					// Re-enable the add to cart button
					$this.prop('disabled', false);

					$('body').trigger('edd_cart_item_added', [ response ]);
					_deselectAddonDropDown();

				}
			}
		}).fail(function (response) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		}).done(function (response) {

		});
		return false;
	});

	// Show the login form on the checkout page
	$('#edd_checkout_form_wrap').on('click', '.edd_checkout_register_login', function () {
		var $this = $(this),
			data = {
				action: $this.data('action')
			};
		// Show the ajax loader
		$('.edd-cart-ajax').show();

		$.post(edd_scripts.ajaxurl, data, function (checkout_response) {
			$('#edd_checkout_login_register').html(edd_scripts.loading);
			$('#edd_checkout_login_register').html(checkout_response);
			// Hide the ajax loader
			$('.edd-cart-ajax').hide();
		});
		return false;
	});

	// Process the login form via ajax
	$(document).on('click', '#edd_purchase_form #edd_login_fields input[type=submit]', function(e) {

		e.preventDefault();

		var complete_purchase_val = $(this).val();

		$(this).val(edd_global_vars.purchase_loading);

		$(this).after('<span class="edd-cart-ajax"><i class="edd-icon-spinner edd-icon-spin"></i></span>');

		var data = {
			action : 'edd_process_checkout_login',
			edd_ajax : 1,
			edd_user_login : $('#edd_login_fields #edd_user_login').val(),
			edd_user_pass : $('#edd_login_fields #edd_user_pass').val()
		};

		$.post(edd_global_vars.ajaxurl, data, function(data) {

			if ( $.trim(data) == 'success' ) {
				$('.edd_errors').remove();
				window.location = edd_scripts.checkout_page;
			} else {
				$('#edd_login_fields input[type=submit]').val(complete_purchase_val);
				$('.edd-cart-ajax').remove();
				$('.edd_errors').remove();
				$('#edd-user-login-submit').before(data);
			}
		});

	});

	// Load the fields for the selected payment method
	$('select#edd-gateway, input.edd-gateway').change( function (e) {

		var payment_mode = $('#edd-gateway option:selected, input.edd-gateway:checked').val();

		if( payment_mode == '0' )
			return false;

		edd_load_gateway( payment_mode );

		return false;
	});

	if($("#card_number").size() > 0){
		edd_load_gateway("paypal");
	}

	// Auto load first payment gateway
	if( edd_scripts.is_checkout == '1' && $('select#edd-gateway, input.edd-gateway').length ) {
		setTimeout( function() {
			edd_load_gateway( edd_scripts.default_gateway );
		}, 200);
	}

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

	var errors = [];

	function valid(){
		errors = [];

		if($("#edd_email").val() == ""){
			var error = {};
			error.id = "#edd_email";
			error.message = "Email cannot be empty";
			errors.push(error);
			$("#edd_email").parent(".margin-top-10").addClass("has-error");
		}

		var Email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if ($("#edd_email").val()!= "" && Email.test($('#edd_email').val()) == false) {

        var error = {};
        error.id = "#edd_email";
        error.message = "Email is not valid.";
        errors.push(error);
				$("#edd_email").parent(".margin-top-10").addClass("has-error");
    }

		if($("#card_name").val() == ""){
			var error = {};
			error.id = "#card_name";
			error.message = "Name on the Card field cannot be empty";
			errors.push(error);
			$("#card_name").parent(".col-xs-12").addClass("has-error");
		}

		if($("#card_number").val() == ""){
			var error = {};
			error.id = "#card_number";
			error.message = "Card number field cannot be empty";
			errors.push(error);
			$("#card_number").parent(".col-xs-12").addClass("has-error");
		}
		else if(!valid_credit_card($("#card_number").val())){
			var error = {};
			error.id = "#card_number";
			error.message = "Invalid credit card number";
			errors.push(error);
			$("#card_number").parent(".col-xs-12").addClass("has-error");
		}

		if($("#card_expiry_month").val() == ""){
			var error = {};
			error.id = "#card_expiry_month";
			error.message = "Expiry Month field cannot be empty";
			errors.push(error);
			$("#card_expiry_month").parent(".col-xs-6").addClass("has-error");
		}
		else if($.trim($("#card_expiry_month").val()).length == 2){
			var valString = $("#card_expiry_month").val();
			// var mnthAndYear = valString.split('/');
			if(Number(valString) > 12){
				var error = {};
				error.id = "#card_expiry";
				error.message = "Invalid month in expiry date";
				errors.push(error);
				$("#card_expiry_month").parent(".col-xs-6").addClass("has-error");
			}
		}

		if($("#card_expiry_year").val() == ""){
			var error = {};
			error.id = "#card_expiry_year";
			error.message = "Expiry Year field cannot be empty";
			errors.push(error);
			$("#card_expiry_year").parent(".col-xs-6").addClass("has-error");
		}
		else if($.trim($("#card_expiry_year").val()).length == 4){
			var valString = $("#card_expiry_year").val();
			var d = new Date();
			var n = d.getFullYear();
			var currentYear = n.toString().substring(2);

			if(Number(valString) < Number(n)){
				var error = {};
				error.id = "#card_expiry_year";
				error.message = "Invalid year in expiry date";
				errors.push(error);
				$("#card_expiry_year").parent(".col-xs-6").addClass("has-error");
			}
		}


		if($("#edd_cvc").val() == ""){
			var error = {};
			error.id = "#edd_cvc";
			error.message = "CVV field cannot be empty";
			errors.push(error);
			$("#edd_cvc").parent(".col-xs-7").addClass("has-error");
		}

		if(!$("#termsandcondition").attr("checked")){
			var error = {};
			error.id = "#termsandcondition";
			error.message = "Please agree to the terms and conditions";
			errors.push(error);
			$("#termsandcondition").parent(".col-xs-12").addClass("has-error");
		}

		if(errors.length>0){
			var errorsString="";
			for (var i = 0; i < errors.length; i++) {
				errorsString += errors[i].message+"</br>";
			}
			$(".alert-checkout-error").remove();
			$("#edd_checkout_cart_wrap").prepend("<div class='alert alert-danger alert-dismissible alert-checkout-error' role='alert'> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><strong>Error!</strong></br>"+errorsString+"</div>");
			return false;
		}
		return true;
	}

	// $("#confirmBooking").unbind("click");
	$(document).on('click', '.confirmBooking', function(e) {

		var eddPurchaseform = document.getElementById('edd_purchase_form');

		$("#loading").show();
		if(!valid())
		{
			$("#loading").hide();
			return false;
		}
		// if( typeof eddPurchaseform.checkValidity === "function" && false === eddPurchaseform.checkValidity() ) {
		// 	return;
		// }

		e.preventDefault();

		// var complete_purchase_val = $(this).val();


		$.post(edd_global_vars.ajaxurl, $('#edd_purchase_form').serialize() + '&action=edd_process_checkout&edd_ajax=true', function(data) {
			$("#loading").hide();
			if ( $.trim(data) == 'success' ) {
				$('.edd_errors').remove();
				$('.edd-error').hide();
				localStorage.removeItem('pageno');
				localStorage.removeItem('add_info');
				$(eddPurchaseform).submit();
			} else {
				// $('#edd-purchase-button').val(complete_purchase_val);
				var jsonData = JSON.parse(data);
				if(jsonData.status = "false"){
					$(".alert-checkout-error").remove();
					$("#edd_checkout_cart_wrap").prepend("<div class='alert alert-danger alert-dismissible alert-checkout-error' role='alert'> <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>"+jsonData.message+"</div>");
				}

			}
		});

	});

	$(document).on('click', '#edd_purchase_form #edd_purchase_submit input[type=submit]', function(e) {

		var eddPurchaseform = document.getElementById('edd_purchase_form');

		if( typeof eddPurchaseform.checkValidity === "function" && false === eddPurchaseform.checkValidity() ) {
			return;
		}

		e.preventDefault();

		var complete_purchase_val = $(this).val();

		$(this).val(edd_global_vars.purchase_loading);

		$(this).after('<span class="edd-cart-ajax"><i class="edd-icon-spinner edd-icon-spin"></i></span>');

		$.post(edd_global_vars.ajaxurl, $('#edd_purchase_form').serialize() + '&action=edd_process_checkout&edd_ajax=true', function(data) {
			if ( $.trim(data) == 'success' ) {
				$('.edd_errors').remove();
				$('.edd-error').hide();
				$(eddPurchaseform).submit();
			} else {
				$('#edd-purchase-button').val(complete_purchase_val);
				$('.edd-cart-ajax').remove();
				$('.edd_errors').remove();
				$('.edd-error').hide();
				$('#edd_purchase_submit').before(data);
			}
		});

	});

});

function _bindQuantityChangeEvent(){
	$(".edd-quanity").unbind("change");
	$(".edd-quanity").change(function(){
		var newQuanity = $(this).val();
		var download_id = $(this).attr("data-download-id");

		var postData = {
			action: 'edd_update_quantity',
			quantity: newQuanity,
			download_id: download_id
			//options: options
		};

		//edd_discount_loader.show();
		$.ajax({
			type: "POST",
			data: postData,
			dataType: "json",
			url: ajaxUrl,
			xhrFields: {
				withCredentials: true
			},
			success: function (response) {

				$('.edd_cart_subtotal_amount').each(function() {
					$(this).text(response.subtotal);
				});

				$('.edd_cart_tax_amount').each(function() {
					// alert(response.taxes);
					$(this).text(response.taxes);
				});

				$(".edd_cart_roomtotal span").html(response.roomtotal);
				$(".edd_cart_addontotal span").html(response.addontotal);

				$('.edd_cart_amount').each(function() {
					$(this).text(response.total);
					//$body.trigger('edd_quantity_updated', [ response ]);
				});

				$('.cart_item.edd_cart_tax span').html( response.taxes );

				$('.cart_item.edd_total span').html( response.total );
				$(".mini-cart-wrapper .reservation-summary-header .item-count").html( response.quantity );
				$(".mini-cart-wrapper .reservation-summary-header .total-price").html( response.total );
			}
		}).fail(function (data) {
			if ( window.console && window.console.log ) {
				console.log( data );
			}
		});

		return false;
	});

	$(".mini-cart-wrapper .reservation-summary-header").unbind("click");
	$(".mini-cart-wrapper .reservation-summary-header").click(function(){
		if(!$(this).parents(".mini-cart-wrapper").hasClass("position-releative")){
			$(this).next(".edd-cart").slideToggle();
			if($(window).width() < 768){
				var height = $(window).height();
				$(".edd-cart").css("max-height",(height-44-42)+"px");
				$(".edd-cart").css("height",(height-44-42)+"px !important");
			}
			if($(this).find(".glyphicon").hasClass("glyphicon-menu-down")){
				$(this).find(".glyphicon").removeClass("glyphicon-menu-down").addClass("glyphicon-menu-up");
			}
			else{
				$(this).find(".glyphicon").removeClass("glyphicon-menu-up").addClass("glyphicon-menu-down");
			}
		}
		else{
			return false;
		}
	});

	$(".btn-addanotherroom").unbind("click");
	$(".btn-addanotherroom").click(function(e){
		e.stopPropagation();
		$(".mini-cart-wrapper .reservation-summary-header").click();
		return false;
	});
}

function _bindsociallogin(){
	// login with facebook
$( document ).on( 'click', 'a.edd-slg-social-login-facebook', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

	errorel.hide();
	errorel.html('');

	if( EDDSlg.fberror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.fberrormsg );
		return false;
	} else {

		FB.login(function(response) {
			//alert(response.status);
			if (response.status === 'connected') {
				//creat user to site
				edd_slg_social_connect( 'facebook', object );
			}
		}, {scope:'email'});
	}
});

// login with google+
$( document ).on( 'click', 'a.edd-slg-social-login-googleplus', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

	errorel.hide();
	errorel.html('');

	if( EDDSlg.gperror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.gperrormsg );
		return false;
	} else {

		var googleurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-gp-redirect-url').val();

		if(googleurl == '') {
			alert( EDDSlg.urlerror );
			return false;
		}

		var googleLogin = window.open(googleurl, "google_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
		var gTimer = setInterval(function () { //set interval for executing the code to popup
			try {
				if (googleLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(gTimer);
					googleLogin.close();
					edd_slg_social_connect( 'googleplus', object );
				}
			} catch (e) {}
		}, 500);
	}
});

// login with linkedin
$( document ).on( 'click', 'a.edd-slg-social-login-linkedin', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

	errorel.hide();
	errorel.html('');

	if( EDDSlg.lierror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.lierrormsg );
		return false;
	} else {

		var linkedinurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-li-redirect-url').val();

		if(linkedinurl == '') {
			alert( EDDSlg.urlerror );
			return false;
		}
		var linkedinLogin = window.open(linkedinurl, "linkedin", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
		var lTimer = setInterval(function () { //set interval for executing the code to popup
			try {
				if (linkedinLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(lTimer);
					linkedinLogin.close();
					edd_slg_social_connect( 'linkedin', object );
				}
			} catch (e) {}
		}, 300);
	}

});

// login with twitter
$( document ).on( 'click', 'a.edd-slg-social-login-twitter', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');
	//var redirect_url = $(this).parents('.edd-slg-social-container').find('.edd-slg-redirect-url').val();
	var parents = $(this).parents( 'div.edd-slg-social-container' );
	var appendurl = '';

	//check button is clicked form widget
	if( parents.hasClass('edd-slg-widget-content') ) {
		appendurl = '&container=widget';
	}

	errorel.hide();
	errorel.html('');

	if( EDDSlg.twerror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.twerrormsg );
		return false;
	} else {

		var twitterurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-tw-redirect-url').val();

		if( twitterurl == '' ) {
			alert( EDDSlg.urlerror );
			return false;
		}

		var twLogin = window.open(twitterurl, "twitter_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
		var tTimer = setInterval(function () { //set interval for executing the code to popup
			try {
				/*if ( twLogin.location.hostname == window.location.hostname ) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(tTimer);
					twLogin.close();
					window.parent.location = EDDSlg.socialloginredirect+appendurl;
				}*/
				if ( twLogin.location.hostname == window.location.hostname ) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(tTimer);
					twLogin.close();
					if(EDDSlg.userid != ''){
						edd_slg_social_connect( 'twitter', object );
					}
					else{
						window.parent.location = EDDSlg.socialloginredirect+appendurl;
					}
				}
			} catch (e) {}
		}, 300);
	}

});

// login with yahoo
$( document ).on( 'click', 'a.edd-slg-social-login-yahoo', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

	errorel.hide();
	errorel.html('');

	if( EDDSlg.yherror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.yherrormsg );
		return false;
	} else {

		var yahoourl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-yh-redirect-url').val();

		if(yahoourl == '') {
			alert( EDDSlg.urlerror );
			return false;
		}
		var yhLogin = window.open(yahoourl, "yahoo_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
		var yTimer = setInterval(function () { //set interval for executing the code to popup
			try {
				if (yhLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(yTimer);
					yhLogin.close();
					edd_slg_social_connect( 'yahoo', object );
				}
			} catch (e) {}
		}, 300);
	}
});

// login with foursquare
$( document ).on( 'click', 'a.edd-slg-social-login-foursquare', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

	errorel.hide();
	errorel.html('');

	if( EDDSlg.fserror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.fserrormsg );
		return false;
	} else {

		var foursquareurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-fs-redirect-url').val();

		if(foursquareurl == '') {
			alert( EDDSlg.urlerror );
			return false;
		}
		var fsLogin = window.open(foursquareurl, "foursquare_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
		var fsTimer = setInterval(function () { //set interval for executing the code to popup
			try {
				if (fsLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(fsTimer);
					fsLogin.close();
					edd_slg_social_connect( 'foursquare', object );
				}
			} catch (e) {}
		}, 300);
	}
});

// login with windows live
$( document ).on( 'click', 'a.edd-slg-social-login-windowslive', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

	errorel.hide();
	errorel.html('');

	if( EDDSlg.wlerror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.wlerrormsg );
		return false;
	} else {

		var windowsliveurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-wl-redirect-url').val();

		if(windowsliveurl == '') {
			alert( EDDSlg.urlerror );
			return false;
		}
		var wlLogin = window.open(windowsliveurl, "windowslive_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
		var wlTimer = setInterval(function () { //set interval for executing the code to popup
			try {
				if (wlLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(wlTimer);
					wlLogin.close();
					edd_slg_social_connect( 'windowslive', object );
				}
			} catch (e) {}
		}, 300);
	}
});

// login with VK.com
$( document ).on( 'click', 'a.edd-slg-social-login-vk', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

	errorel.hide();
	errorel.html('');

	if( EDDSlg.vkerror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.vkerrormsg );
		return false;
	} else {

		var vkurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-vk-redirect-url').val();

		if(vkurl == '') {
			alert( EDDSlg.urlerror );
			return false;
		}

		var vkLogin = window.open(vkurl, "vk_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
		var vkTimer = setInterval(function () { //set interval for executing the code to popup
			try {
				if (vkLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(vkTimer);
					vkLogin.close();
					edd_slg_social_connect( 'vk', object );
				}
			} catch (e) {}
		}, 300);
	}
});

// login with instagram
$( document ).on( 'click', 'a.edd-slg-social-login-instagram', function(){

	var object = $(this);
	var errorel = $(this).parents('.edd-slg-social-container').find('.edd-slg-login-error');

	errorel.hide();
	errorel.html('');

	if( EDDSlg.insterror == '1' ) {
		errorel.show();
		errorel.html( EDDSlg.insterrormsg );
		return false;
	} else {

		var instagramurl = $(this).closest('.edd-slg-social-container').find('.edd-slg-social-inst-redirect-url').val();

		if(instagramurl == '') {
			alert( EDDSlg.urlerror );
			return false;
		}
		var instLogin = window.open(instagramurl, "instagram_login", "scrollbars=yes,resizable=no,toolbar=no,location=no,directories=no,status=no,menubar=no,copyhistory=no,height=400,width=600");
		var instTimer = setInterval(function () { //set interval for executing the code to popup
			try {
				if (instLogin.location.hostname == window.location.hostname) { //if login domain host name and window location hostname is equal then it will go ahead
					clearInterval(instTimer);
					instLogin.close();
					edd_slg_social_connect( 'instagram', object );
				}
			} catch (e) {}
		}, 300);
	}
});
}

function edd_load_gateway( payment_mode ) {

	// Show the ajax loader
	jQuery('.edd-cart-ajax').show();
	$("#loading").show();
	// jQuery('#edd_purchase_form_wrap').html('<img src="' + edd_scripts.ajax_loader + '"/>');

	jQuery.post(edd_scripts.ajaxurl + '?payment-mode=' + payment_mode, { action: 'edd_load_gateway', edd_payment_mode: payment_mode },
		function(response){
			$("#loading").hide();
			jQuery("#checkoutitems").remove();
			jQuery('#edd_purchase_form').html(response);
			// if(location.pathname.indexOf('checkout')>0)
		  // {
		  //   var pageno = localStorage.getItem('pageno');
			// 	var addInfo = localStorage.getItem('add_info');
			// 	if(isUserLoggedIn != "1"){
			// 		pageno = 1;
			// 		localStorage.setItem('pageno',1);
			// 	}
			// 	if(addInfo !=""){
			// 		$('.add_info').val(addInfo);
			// 	}
		  //   if(pageno != undefined && pageno == 2){
		  //     $("#edd_checkout_cart_form").hide();
		  //     // $("#edd_checkout_form_wrap").show();
		  //     localStorage.setItem('pageno',2);
		  //   }
		  //   else{
		  //     $("#edd_checkout_cart_form").show();
		  //     // $("#edd_checkout_form_wrap").hide();
		  //     localStorage.setItem('pageno',1);
		  //   }
		  // }
			// else if(location.pathname.indexOf('checkout') == -1){
			// 	localStorage.setItem('pageno',1);
			// }

			// jQuery('.edd-no-js').hide();
			// jQuery(".btn-backbtn").click(function(e){
		  //   e.preventDefault();
			// 	jQuery("#edd_checkout_cart_form").show();
			// 	jQuery("#edd_checkout_form_wrap").hide();
		  //   localStorage.setItem('pageno',1);
		  //   return false;
		  // });
			//
			// if(jQuery(".edd-points-redeem-points-wrap").size() > 0)
			// {
			// 	var htmlItm = jQuery(".edd-points-checkout-message").html();
			// 	htmlItm += "</br>"+jQuery(".edd-points-redeem-message").html();
			// 	jQuery(".edd-points-redeem-message").html(htmlItm);
			// 	jQuery(".edd-points-checkout-message").remove();
			// }
			//
			// if(jQuery('.edd-points-remove-disocunt-message').size() > 0)
			// {
			// 	var htmlItm = "";
			// 	jQuery.each(jQuery('.edd-points-checkout-message'), function(index,item){
			// 		htmlItm += jQuery(this).html()+"</br>";
			// 		jQuery(this).hide();
			// 	});
			// jQuery('.edd-points-checkout-message').last().html(htmlItm);
			// jQuery('.edd-points-checkout-message').last().show().css({"width":"100%","margin":"0px 0px 10px 5px !important"});
			// 	//jQuery('.edd-points-checkout-message').not(":last-child").remove();
			// }

			_bindsociallogin();
			_bindQuantityChangeEvent();
		}
	);

}
