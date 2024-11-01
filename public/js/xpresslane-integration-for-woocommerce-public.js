(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 $(document).ready(function(){
	 	var enable_checkout_button = mwb_xpresslane_params.checkout_enable
		$(document.body).trigger('wc_fragment_refresh');
	 });
				

	 jQuery(document).ready(function($){
		  //Checkout with Cart
			console.log(jQuery("#xplane-nonce").val());
			var nonce =  jQuery("#xplane-nonce").val()
	 	jQuery(document).on('click','.mwb_cart_checkout_button', function(e){
	 		e.preventDefault();
	 		e.stopPropagation();
	 		var cart_checksum = jQuery(document).find('form#order #checksum').val();
			console.log(jQuery("#xplane-nonce").val())
			 console.log(cart_checksum);
	 		jQuery.ajax({
	 			url : cpm_object.ajax_url,
	 			type : 'POST',
	 			cache : false,
	 			data : {
	 				action : 'mwb_xlane_insert_db_cart',
	 				xlane_cart_data : cart_checksum,
					 wpnonce : nonce
	 			},success:function(response){
					var res = JSON.parse(response)
	 				if( res.status == 200 ){

						  if(  window.fbq ){
							
							let fbqData  = fbgaData(res.cart_data)
							window.fbq('track', 'InitiateCheckout', {
								value: fbqData.items_subtotal_price,
								currency: fbqData.currency,
								content_ids: fbqData.product_ids,
								content_type: 'product_group',
								num_items: fbqData.item_count,
							  }, { eventID: window.localStorage.getItem('initiatecheckouteventid') });
						  }
						  jQuery(document).find('form#order').submit();
	 				}else{
	 					window.location.reload();
	 				}
	 			}
	 		})
	 	});
		
		 //Checkout with MiniCart
		 jQuery(document).on('click','.mwb_mini_cart_checkout_button', function(e){
			e.preventDefault();
			e.stopPropagation();
			console.log(jQuery("#xplane-nonce").val())
			var cart_checksum = jQuery(document).find('form#order #checksum').val();
			console.log(cart_checksum);
			jQuery.ajax({
				url : cpm_object.ajax_url,
				type : 'POST',
				cache : false,
				data : {
					action : 'mwb_xlane_insert_db_cart',
					xlane_cart_data : cart_checksum,
					wpnonce : nonce
				},success:function(response){
					var res = JSON.parse(response)
					if( res.status == 200 ){

						  if(  window.fbq ){
							
							let fbqData  = fbgaData(res.cart_data)
							window.fbq('track', 'InitiateCheckout', {
								value: fbqData.items_subtotal_price,
								currency: fbqData.currency,
								content_ids: fbqData.product_ids,
								content_type: 'product_group',
								num_items: fbqData.item_count,
							  }, { eventID: window.localStorage.getItem('initiatecheckouteventid') });
						  }
						  jQuery('.xlane_minicart_button').find('form#order').submit();
	 				}else{
						window.location.reload();
					}
				}
			})
		});


		$(document).on('click','button#mwb_xpresslane_top_new.xlane-mini-cart',function(e){
			$(".success_msg").css("display", "block");
		});
		$(document).on('click','button#mwb_xpresslane_top_new.checkout-button',function(e){
			$(".success_msg").css("display", "block");
		});
		$('form.ajax').on('submit', function(e){
		   e.preventDefault();
		   $(".success_msg").css("display", "block");
		   var that = $(this),
		   url = that.attr('action'),
		   type = that.attr('method');
		   var prod_quantity = $(document).find('.single_quantity').val();
		   var m_merchantid = $('#merchantid').val();
		   var c_checksum = $('#checksum').val();
		   var m_mwb_cart_data = $('#mwb_cart_data').val();
		   var action_url = $('#action_url').val();
		   if( $('#mwb_variation_id').val() != null ){
		   	var mwb_variation_id = $('#mwb_variation_id').val();
		   }
		   if( $('#attribute_pa_size').val() != null ){
		   	var attribute_pa_size = $('#attribute_pa_size').val();
		   }
		   if( $('#product_variations').val() != null ){
		   	var product_variations = $('#product_variations').val();
		   }
		  
		   $.ajax({
		      url: cpm_object.ajax_url,
		      type:"POST",
		      dataType:'text',
		      data: {
		         action:'set_form',
		         prod_quantity:prod_quantity,
		         merchantid:m_merchantid,
		         mwb_cart_data:m_mwb_cart_data,
		         mwb_variation_id:mwb_variation_id,
		         attribute_pa_size:attribute_pa_size,
		         product_variations:product_variations,
		         action_url:action_url,
				 nonce : nonce,
		    },   success: function(response){
		        $(".success_msg").css("display","block");
// 				$("<form id='order' method='POST' action="+action_url+"><input type='hidden' id='merchantid' name='merchantid' value='"+m_merchantid+"'/><input type='hidden' id='checksum' name='checksum' value='"+response+"'/></form>").appendTo("body").submit();
				
				//jQuery(document).find('form#order').submit();
		     }, error: function(data){
		         $(".error_msg").css("display","block");      }
		   });
			$('.ajax')[0].reset();
		  });
	});
	
	//FB Analytics Initiate Event
	function fbgaData (cart_data){
		const product_data = {
			currency: '', items_subtotal_price: 0, item_count: 0, quantity: 0, product_ids: [],
		
			};
			if( cart_data.orderitems.length > 0 ) {
				var product_qyt =0 ;

				var prod_ids = []
				cart_data.orderitems.forEach(item => {
					product_qyt += item.quantity
					if( item.merchantproductid != "" ){
						var id = item.merchantproductid
						prod_ids.push(id.toString())
					}else{
						var id = item.productid
						prod_ids.push(id.toString())
					}
					
				});
			}
			product_data.currency =  cart_data.currency
			product_data.items_subtotal_price = cart_data.grandtotal
			product_data.item_count = cart_data.orderitems.length
			product_data.quantity = product_qyt
			 product_data.product_ids = prod_ids

			 
		return product_data
	}
	//FB Analytics Purchase Event
	function fbqpurchaseData(order_data){
		localStorage.setItem('xpresslane-order',JSON.stringify(order_data));
		const order = {
			email: '', first_name: '', last_name: '', phone: 0, grand_total: 0,  product_ids: [], item_count : 0,
			xpresslane_order_id:'',
			};

			if( order_data.orderitems.length > 0 ) {
				var product_qyt =0 ;

				var prod_ids = []
				order_data.orderitems.forEach(item => {
					console.log(order_data.orderitems)
					product_qyt += item.quantity
					if( item.merchantproductid != "" &&  item.merchantproductid != "0"){
						var id = item.merchantproductid
						console.log(item)
						prod_ids.push(id.toString())
					}else{
						var id = item.productid
						prod_ids.push(id.toString())
					}
					
				});
			}
			order.email =  order_data.email
			order.first_name = order_data.billing_firstname
			order.last_name = order_data.billing_lastname
			order.phone =  order_data.billing_telephone
			order.product_ids = prod_ids
			order.item_count =  order_data.orderitems.length
			order.grand_total =order_data.totalAmount
			order.xpresslane_order_id = order_data.xpresslane_txn_id
			
			return order
	}
	
	//FB Analytics Purchase Event
	if( window.location.href.includes('/order-received/') == true ){
		var url = window.location.href
		var pathname = new URL(url).pathname;

		console.log(pathname.split('/'));
		var path_split = pathname.split('/')
		var order_id = path_split[3]
		console.log(order_id)
		jQuery.ajax({
			url : cpm_object.ajax_url,
			type : 'POST',
			cache : false,
			data : {
				action : 'mwb_xlane_get_order_data',
				wc_order_id : order_id,
				wpnonce : cpm_object.wpnonce,
			},success:function(response){
				console.log(response)
			   var res = JSON.parse(response)
			   console.log(res.cart_data)
			   let order  = fbqpurchaseData(res.cart_data)
					   console.log(order)
				if( res.status == 200 ){
					 if(  window.fbq ){
						window.fbq('track', 'Purchase', {
							em: order.email.toString().toLowerCase(),
							fn: order.first_name.toString().toLowerCase(),
							ln: order.last_name.toString().toLowerCase(),
							ph: order.phone.toString().toLowerCase(),
							value: parseFloat(order.grand_total),
							currency: 'INR',
							content_ids: order.product_ids,
							content_type: 'product_group',
							affiliation: 'Xpresslane',
							num_items: order.item_count,
						  }, { eventID: `${order.xpresslane_order_id}-purchase` });
					 }
				}
			}
		});

	}
	

})( jQuery );
