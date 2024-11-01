<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/public
 * @link       https://www.xpresslane.com
 * @since      1.0.0
 */


/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * 
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/public
 * @link       https://www.xpresslane.com
 * @since      1.0.0
 */
class Xpresslane_Integration_For_Woocommerce_Public {


	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * Xpresslane_Integration_For_Woocommerce/public
	 */
	public function enqueueStyles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Xpresslane_Integration_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Xpresslane_Integration_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/xpresslane-integration-for-woocommerce-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 */
	public function enqueueScripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Xpresslane_Integration_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Xpresslane_Integration_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$check = 'no';
		$show_hide_checkout_button = $this->mwb_xlane_intgn_default_checkout();
		if ( isset( $show_hide_checkout_button ) && 'yes' === $show_hide_checkout_button ) {
			$check = 'yes';
		}
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/xpresslane-integration-for-woocommerce-public.js', array( 'jquery' ), time(), false );

		$translation_array = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'wpnonce' => wp_create_nonce(),
		);
		wp_localize_script( $this->plugin_name, 'cpm_object', $translation_array );


		wp_localize_script(
			$this->plugin_name,
			'mwb_xpresslane_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'checkout_enable' => $check,
				'wpnonce' => wp_create_nonce(),
			) 
		);
		wp_enqueue_script( $this->plugin_name );

	}

	public function mwb_prevent_checkout_page_access() {
		global $woocommerce;
		$mwb_cart_url = wc_get_cart_url();
		$mwb_xlane_default_checkout = get_option( 'mwb-xlane-checkout-feature', false );
		$checkout_url = '';
		if ( function_exists( 'wc_get_page_permalink' ) ) {
			$checkout_url = wc_get_page_permalink( 'checkout' );
		}
		if ( isset( $mwb_xlane_default_checkout ) && 'yes' === $mwb_xlane_default_checkout && ! array_key_exists( 'HTTP_REFERER', $_SERVER ) && get_page_link() === $checkout_url ) {
			/* choose the appropriate page to redirect users */
			wp_redirect( $mwb_cart_url );
		}
	}
	public function mwb_xlane_custom_shop_order_column( $columns ) {
		$reordered_columns = array();

		// Inserting columns to a specific location
		foreach ( $columns as $key => $column ) {
			$reordered_columns[ $key ] = $column;
			if ( 'order_status' === $key ) {
				// Inserting after "Status" column
				$reordered_columns['xpresslane'] = __( 'Xpresslane Payment Mode', 'xpresslane-integration-for-woocommerce' );
				$reordered_columns['xpresslane_trans'] = __( 'Xpresslane Transaction Id', 'xpresslane-integration-for-woocommerce' );

			}       
		}
		return $reordered_columns;
	}

	public function mwb_xlane_remove_subtotal_from_orders_total_lines( $totals ) {
		unset( $totals['cart_subtotal'] );
		return $totals;
	}


	public function mwb_xlane_custom_orders_list_column_content( $column, $post_id ) {

		switch ( $column ) {

			case 'xpresslane':
				$custom_field_value = get_post_meta( $post_id, 'payment_mode', true );
				if ( ! empty( $custom_field_value ) ) {
				echo esc_attr( $custom_field_value );
				}
				break;
			case 'xpresslane_trans':
				$transc_id = get_post_meta( $post_id, 'mwb_xlane_trasaction_id', true );
				if ( ! empty( $transc_id ) ) {
				echo esc_attr( $transc_id );
				}
				break;
					
		}
	}

	/**
	 * This function is used to get enable.
	 *
	 * @name  mwb_xlane_intgn_enable_plugin
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_enable_plugin() {
		$mwb_xlane_enable = get_option( 'mwb-xlane-plugin-enable', false );
		if ( isset( $mwb_xlane_enable ) && 'yes' == $mwb_xlane_enable ) {
			$mwb_xlane_enable = true;
		} else {
			$mwb_xlane_enable = false;
		}
		return $mwb_xlane_enable;
	}

	/**
	 * This function is used to get merchant key.
	 *
	 * @name  mwb_xlane_intgn_merchant_key
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_merchant_key() {
		$mwb_xlane_merchant_key = get_option( 'mwb-xlane-merchant-key', '' );
		return $mwb_xlane_merchant_key;
	}

	/**
	 * This function is used to get merchant key.
	 *
	 * @name  mwb_xlane_intgn_merchant_key
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_default_checkout() {
		$mwb_xlane_default_checkout = get_option( 'mwb-xlane-checkout-feature', false );
		return $mwb_xlane_default_checkout;
	}

	/**
	 * This function is used to get merchant ID.
	 *
	 * @name  mwb_xlane_intgn_merchant_id
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_merchant_id() {
		$mwb_xlane_merchant_id = get_option( 'mwb-xlane-merchant-id', '' );
		return $mwb_xlane_merchant_id;
	}

	/**
	 * This function is used to get button text.
	 *
	 * @name  mwb_xlane_intgn_button_text
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_button_text() {
		$mwb_xlane_intgn_button_text = get_option( 'mwb-xlane-button-txt', '' );
		return $mwb_xlane_intgn_button_text;
	}

	/**
	 * This function is used to get button tex colort.
	 *
	 * @name  mwb_xlane_intgn_button_text_color
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_button_text_color( $button_type ) {
		$mwb_xlane_intgn_button_text = get_option( $button_type, '' );
		return $mwb_xlane_intgn_button_text;
	}

	/**
	 * This function is used to get button back color.
	 *
	 * @name  mwb_xlane_intgn_button_back_color
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_button_back_color( $button_type ) {
		$mwb_xlane_intgn_button_text = get_option( $button_type, '' );
		return $mwb_xlane_intgn_button_text;
	}

	public function mwb_remove_default_checkout_button() {
		$show_hide_checkout_button = $this->mwb_xlane_intgn_default_checkout();
		if ( isset( $show_hide_checkout_button ) && 'yes' === $show_hide_checkout_button ) {
			remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
			remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
		}
	}
	/**
	 * This function is used to create Xpresslane noce at cart page.
	 *
	 * @name  mwb_xlane_intgn_nonce_form
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_nonce_form() {
		wp_nonce_field( 'xplane_nonce', 'xplane-nonce' );
	}
	/**
	 * This function is used to create Xpresslane form at cart page.
	 *
	 * @name  mwb_xlane_intgn_add_payment_form
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_add_payment_form() {
		global $woocommerce, $product;
		if ( $this->mwb_xlane_intgn_enable_plugin() ) {            
			$mwb_data = array();
			$mwb_data[ $woocommerce->cart->get_cart_hash() ] = WC()->cart;
			$retrive_data = WC()->session->get( 'mwb_xlane_cart_session' );

			if ( empty( $retrive_data ) ) {
				WC()->session->set( 'mwb_xlane_cart_session', $mwb_data );
				// $_SESSION['mwb_xlane_cart_session'] = $mwb_data;
			}

			// WC()->session->set('mwb_xlane_cart_session', $mwb_data );
			$session = WC()->session->get_session_cookie();
			$session_id = ! empty( $session[0] ) ? $session[0] : '' ;
			$cart_user_id   = get_current_user_id() ? get_current_user_id() : $session_id ;
			$cart_hash_id = $woocommerce->cart->get_cart_hash();
			$merchantid = $this->mwb_xlane_intgn_merchant_id();
			$secretkey = $this->mwb_xlane_intgn_merchant_key();
			$grandtotal = $woocommerce->cart->total;
			$subtotal = $woocommerce->cart->subtotal;
			$orderdate = gmdate( 'Y-m-d\TH:i:s', current_time( 'timestamp' ) );
			$orderdate = $orderdate . '.0Z';
			$cart_hash_id = $cart_hash_id . ':' . $orderdate;
			$coupon_code = '';
			if ( is_cart() ) {
				$coupon_code = ! empty( $woocommerce->cart->get_applied_coupons() ) ? $woocommerce->cart->get_applied_coupons() : '';
				
				if ( isset( $coupon_code ) && ! empty( $coupon_code ) && is_array( $coupon_code ) ) {
					$coupon_code = implode( ',', $coupon_code );
				}
			}
			$discount = '0';
			if ( is_cart() ) {

				if ( ! empty( $woocommerce->cart->get_coupon_discount_totals() ) ) {
					$discount_array     = $woocommerce->cart->get_coupon_discount_amount( $coupon_code );
					$discount           = ! empty( $discount_array ) ? $discount_array : 0 ;
					$discount_tax_array = $woocommerce->cart->get_coupon_discount_tax_amount( $coupon_code );
					$discount            += $discount_tax_array ;
				}
			}
			if ( 0 !== $discount ) {
				$preshiptotal = $woocommerce->cart->subtotal - $discount;
			} else {
				$preshiptotal = $woocommerce->cart->subtotal;
			}
			$tax_amount = ! empty( $woocommerce->cart->get_total_tax() ) ? $woocommerce->cart->get_total_tax() : 0;
			$orderitems = array();
			$totaldiscount = 0;
			$actualitemtotal = 0;

			/*if(is_product()){
			$product_id = esc_attr( $product->get_id() );
			$prod_quantity = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity();

			$woocommerce->cart->add_to_cart( $product_id,$prod_quantity );  
			}*/ 

			$items = $woocommerce->cart->get_cart();
		   
			if ( isset( $items ) && ! empty( $items ) && isset( $items ) ) {
				foreach ( $items as $key => $values ) {
					$discount_amount = 0;
					$variation_id = 0;
					if ( isset( $values['variation_id'] ) && 0 !== $values['variation_id'] ) {
						$product = wc_get_product( $values['variation_id'] );
						$variation_id = $values['variation_id'];
					} else {

						$product = wc_get_product( $values['product_id'] );
					}
					$regular_price = $product->get_regular_price();
					$sale_price = $product->get_sale_price();
					if ( '' === $sale_price ) {
						$sale_price = $regular_price;
					} else {
						$discount_amount = $regular_price - $sale_price;
						$totaldiscount = $totaldiscount + ( $discount_amount * $values['quantity'] );
					}
					$product_desc = ! empty( $product->get_short_description() ) ? $product->get_short_description() : $product->get_title();
					
					$image_id  = $product->get_image_id();
					if ( isset( $image_id ) && ! empty( $image_id ) ) {
						
						$image_url = wp_get_attachment_image_url( $image_id, 'full' );
					} else {
						$image_url = wc_placeholder_img_src();
					}

					$data = '';
					$wcpa_values = '';
					if ( ! empty( $values['wcpa_data'] ) && array_key_exists( 'wcpa_data', $values ) ) {
						$display_data = '';
						foreach ( $values['wcpa_data'] as $key => $value ) {
							$label = $value['label'];
							if ( is_array( $value['value'] ) ) {
								foreach ( $value['value'] as $key => $vals ) {
									$val = $vals['label']; 
								}
								$data_value = $val;
							} else {
								$data_value = $value['value'];
							}
							if ( ! empty( $data_value ) ) {
								$display_data .= $label . ' : ' . $data_value . ' | ';
								$data .= $label . ' [] ' . $data_value . ' [] ';
							} else {
								$display_data .= '';
								$data .= '';
							}
						}
						if ( ! empty( $display_data ) ) {
							$display_data = rtrim( $display_data, ' | ' ); 
							$wcpa_values = ' | ' . $display_data;
						}
					}
					$terms = get_the_terms( $values['product_id'], 'product_cat' );
					$cat_name = '';
					if ( ! empty( $terms ) ) {
						foreach ( $terms as $product_cat_data ) {
							$cat_name = $product_cat_data->name;
						}
					}
					$img_urls = array();
					//Custom image upload for woo-addons-upload
					if ( isset( $values['wau_addon_ids'] ) && ! empty( $values['wau_addon_ids'] ) ) {
						$count = 1;
						foreach ( $values['wau_addon_ids'] as $img_data ) {
							$img_urls = array(
								'upload_image' . $count => array(
									'url' => $img_data['media_url'], 
									'id' => $img_data['media_id'], 
								),
							);
							$count++;
						}
					}
					$var_id = isset( $values['variation_id'] ) && ! empty( $values['variation_id'] ) ? $values['variation_id'] : '';

					$variantion_val = isset( $values['variation']['attribute_size'] ) && ! empty( $values['variation']['attribute_size'] ) ? '-' . $values['variation']['attribute_size'] : ''; 
					if ( is_cart() ) {
						$orderitems[] = array(
							'productname' => $product->get_title() . $wcpa_values . $variantion_val,
							'sku' => $product->get_sku() . '|' . $variation_id . '',
							'quantity' => $values['quantity'],
							'productdescription' => $product_desc,
							'unitprice' => $regular_price,
							'discountamount' => $discount_amount,
							'discountunitprice' => $discount_amount,
							'originalprice' => $regular_price,
							'actualprice' => $sale_price,
							'productimage' => $image_url,
							'productid' => $values['product_id'],
							// 'variationid' => $product_id,
							'varianttitle' => $cat_name,
							'merchantproductid' => $var_id,
							'customfield1'    => $data,
							'properties' => $img_urls,
						);
					}
					$variation[] = array(
						'variation_id' => 'fngc', 
					);
					$actualitemtotal = $actualitemtotal + ( $regular_price * $values['quantity'] );
				}
			}
			$shipping = array();
			$shipping_data = WC()->shipping()->get_packages();

			if ( isset( $shipping_data ) && ! empty( $shipping_data ) && is_array( $shipping_data ) ) {
				foreach ( $shipping_data as $key => $data ) {
					if ( isset( $data ) && ! empty( $data ) && is_array( $data ) ) {
						foreach ( $data['rates'] as $key => $value ) {
							$shipping[] = array(
								'shippingcode' => $value->method_id,
								'shippingname' => $value->label,
								'shippingprice' => $value->cost, 
							); 
						}
					}               
				}
			}
			//shipping order.
			if ( ! empty( $shipping ) && 1 >= count( $shipping ) ) {
				
				$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			
				if ( isset( $chosen_shipping_methods ) && ! empty( $chosen_shipping_methods ) && is_array( $chosen_shipping_methods ) ) {
					$shipping_methods_array = explode( ':', $chosen_shipping_methods[0] );
					$shipping_methods_id = $shipping_methods_array[0];
					$key = array_search( $shipping_methods_id, array_column( $shipping, 'shippingcode' ) );
					if ( array_key_exists( $key, $shipping ) ) {
						$new_shipping = $shipping;
						$shippings = array();
						$shippings[] = $new_shipping[ $key ];
					}
				}
			}
			/* Single Product */
			if ( is_product() ) {
				//global $product;
				global $post;
				$product_id = $post->ID;
				$product = wc_get_product( $product_id );
				$product_hash = $product;
				$prod_hash_value        = $product_hash ? md5( wp_json_encode( $product_hash ) . $product->get_price() . '.00' ) : '';
				$cart_hash_id = ( $prod_hash_value ? $prod_hash_value : esc_attr( $product->get_id() ) );
				?>
				<script type="text/javascript">
					 function getHouseModel() {
					  var model = jQuery('.input-text.qty.text').val();
					  console.log('From getHouseModel', model);
					}
					jQuery(document).on('click', '.quantity.buttons_added', function() { 
						getHouseModel();
						var prod_quantity_value = jQuery('.input-text.qty.text').val();
						jQuery("#prod_quantity").val(prod_quantity_value);
					});
					jQuery(document).on('click', '.quantity', function() { 
						getHouseModel();
						var prod_quantity_value = jQuery('.input-text.qty.text').val();
						jQuery("#prod_quantity").val(prod_quantity_value);
					});
					
					function updateWcpa(){ 
						var html = '';
						var wcpa_val = '';
						var wcpa_title = '';
						jQuery(".wcpa_form_item" ).each(function() {
							var rr = jQuery( this ).find('input').attr('name');
							if( jQuery( this ).find('input').attr('type') == 'text' || jQuery( this ).find('input').attr('type') == 'textarea' ){
								var input_val = jQuery("input[name=" + rr + "]").val();
								var label_text = jQuery( this ).find('label').contents().first('[nodeType=3]').text();
								wcpa_val += label_text+' [] '+input_val +' [] ';
								wcpa_title += label_text+' : '+input_val+' | ';
								 
							}else{
								var input_val = jQuery("input[name=" + rr + "]:checked").val().toUpperCase();
								var label_text = jQuery( this ).find('label').contents().first('[nodeType=3]').text();
								wcpa_val += label_text+' [] '+input_val+' [] ';
								wcpa_title += label_text+' : '+input_val+' | ';
							}

						});

						var slice_title = wcpa_title.slice(0,-2);
						console.log(wcpa_title);
						var wcpa_ttle = ' | '+slice_title;

						html += '<input type="hidden" id="mwb-wcpa-all-values" class="mwb-wcpa-all-values"';
						html += 'name="wcpa-vals"';
						html += 'value="'+wcpa_val+'">';

						html += '<input type="hidden" id="mwb-wcpa-title" class="mwb-wcpa-title"';
						html += 'name="wcpa-for-title"';
						html += 'value="'+wcpa_ttle+'">';
						   jQuery("#mwb-wcpa-fields").html(html);
					}
					jQuery(document).on('click change', ".wcpa_form_item", function(){
						updateWcpa();
					});
					function AlertMessage(){
						window.alert('Please select some product options.');
					}
					function AlertMessageStock(){
						window.alert('Sorry, this product is unavailable. Please choose a different combination.');
					}
					// get default selected variation ID
					jQuery(function($){
						var i = 'input[name="variation_id"]';
						// Get the default selected variation ID  - AFTER (if set on the variable product | delay 300 ms)
						setTimeout( function(){
							console.log( 'Default Variation id (on start +300ms): '+$(i).val() );
							jQuery("#mwb_variation_id").val($(i).val());
							
							if( jQuery("#mwb_variation_id").val() == 0 ){
								 
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).removeClass( "mwb_enabled" );
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).addClass( "mwb_disabled" );
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_disabled').prop('type', 'button');
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_disabled').attr('onclick', 'AlertMessage()'); 
								
							}
							else if( jQuery("#mwb_variation_id").val() != 0 && jQuery(".single_add_to_cart_button").is(".wc-variation-is-unavailable") == true ){
								 
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).addClass( "mwb_stock_disabled" );
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).removeClass( "mwb_enabled" );
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_stock_disabled').prop('type', 'button');
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_stock_disabled').attr('onclick', 'AlertMessageStock()');
							}
							else if( jQuery("#mwb_variation_id").val() != 0 && jQuery(".single_add_to_cart_button").is(".wc-variation-is-unavailable") == false ){
							 
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).removeClass( "mwb_disabled" );
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).removeClass( "mwb_stock_disabled" );
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).addClass( "mwb_enabled" );
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_enabled').prop('type', 'submit');
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_enabled').removeAttr('onclick');

							}
						}, 300 );
				
					});
					
					// get default selected variation attribute name
					jQuery(function($){
						var attribute_value = jQuery('select').data('attribute_name');
						var pa_size = jQuery("select[name="+attribute_value+"] :selected").val();
						
						// Get the default selected variation ID  - AFTER (if set on the variable product | delay 300 ms)
						setTimeout( function(){
							console.log( 'Default Variation attribute name (on start +300ms): '+pa_size );
							jQuery("#attribute_pa_size").val(pa_size);
						}, 300 );
				
					});
					
				   var checkExist = setInterval(function() {
						 var dynamicID = jQuery("table.variations select").attr('id');

					  jQuery(document,'#'+dynamicID ).change(function() {
							   
							var prod_variation_value = jQuery('input[name="variation_id"]').val();
							jQuery("#mwb_variation_id").val(prod_variation_value);
					
							var attribute_value = jQuery('select').data('attribute_name');
							var pa_size = jQuery("select[name="+attribute_value+"] :selected").val();
							jQuery("#attribute_pa_size").val(pa_size);
 
							if( jQuery("#mwb_variation_id").val() == 0 ){
								 
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).removeClass( "mwb_enabled" );
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).addClass( "mwb_disabled" );
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_disabled').prop('type', 'button');
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_disabled').attr('onclick', 'AlertMessage()'); 
								
							}
							else if( jQuery("#mwb_variation_id").val() != 0 && jQuery(".single_add_to_cart_button").is(".wc-variation-is-unavailable") == true ){
								 
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).addClass( "mwb_stock_disabled" );
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).removeClass( "mwb_enabled" );
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_stock_disabled').prop('type', 'button');
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_stock_disabled').attr('onclick', 'AlertMessageStock()');
							}
							else if( jQuery("#mwb_variation_id").val() != 0 && jQuery(".single_add_to_cart_button").is(".wc-variation-is-unavailable") == false ){
								 
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).removeClass( "mwb_disabled" );
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).removeClass( "mwb_stock_disabled" );
								jQuery( "button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button" ).addClass( "mwb_enabled" );
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_enabled').prop('type', 'submit');
								jQuery('button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_enabled').removeAttr('onclick');

							}
											
										});
										  console.log("Exists!");
										  clearInterval(checkExist);
										
									}, 300); // check every 100ms
				
				
					jQuery(document).on('change', 'input[name=variation_id]', function() {
						var prod_variation_value = jQuery('input[name=variation_id]').val();
						jQuery("#mwb_variation_id").val(prod_variation_value);
				
						var attribute_value = jQuery('select').data('attribute_name');
						var pa_size = jQuery("select[name="+attribute_value+"] :selected").val();
						jQuery("#attribute_pa_size").val(pa_size);

					});
					// for "Quick view" feature need to target the submit button class rather than the form submit event because "Quick view" functionality uses the ajax call for add-to-cart using button class and we have the same class--for styling-- for xpresslane button
					jQuery(document).ready(function($){
						$(document).on('change','.quantity > input.qty',function(){
							var $change_qty = $(this).val();
							$(document).find('.single_quantity').val($change_qty);
						});

						 $(document).on('click','button#mwb_xpresslane_top_new.mwb_single_add_to_cart_button.mwb_enabled',function(e){
							e.preventDefault();
							e.stopPropagation();
							$(".success_msg").css("display", "block");
							var that = $(this),
								url = that.attr('action'),
								type = that.attr('method');
							var prod_quantity = $('.single_quantity').val();
							var wcpa_values = $('#mwb-wcpa-all-values').val();
							var wcpa_for_titles = $('#mwb-wcpa-title').val();
							var m_merchantid = $('#merchantid').val();
							var c_checksum = $('#checksum').val();
							var m_mwb_cart_data = $('#mwb_cart_data').val();
							var action_url = $('#action_url').val();
							console.log(jQuery("#xplane-nonce").val());
							 var nonce =  jQuery("#xplane-nonce").val()
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
									wcpa_values:wcpa_values,
									wcpa_for_titles:wcpa_for_titles,
									merchantid:m_merchantid,
									mwb_cart_data:m_mwb_cart_data,
									mwb_variation_id:mwb_variation_id,
									 attribute_pa_size:attribute_pa_size,
									 product_variations:product_variations,
									action_url:action_url,
									wpnonce: nonce,
								},   success: function(response){
									$(".success_msg").css("display","block");
									console.log(response);
									var res = JSON.parse(response)
									console.log(res.cart_data)
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
									 $("<form id='order' method='POST' action="+action_url+"><input type='hidden' id='merchantid' name='merchantid' value='"+m_merchantid+"'/><input type='hidden' id='checksum' name='checksum' value='"+res.encrypt_payload+"'/></form>").appendTo("body").submit();
								}, error: function(data){
									$(".error_msg").css("display","block");      }
							});
							$('.ajax')[0].reset();
						});
					});
							function fbgaData (cart_data){
					const product_data = {
					currency: '', items_subtotal_price: 0, item_count: 0, quantity: 0, product_ids: [],
				
					};
					cart_data = JSON.parse(cart_data);
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
				</script>
				<?php
				ob_start();
				?>
					<script type="text/javascript">
						var change_qty = '';
						let target_element = document.querySelector('.quantity > input.qty');  
						target_element.addEventListener('change', function () {  
							change_qty = this.value;
						});  
					</script>
				<?php
				$updated_prod_qty = ob_get_clean();
				?>
				<?php
				$prod_quantity  = 1 ;
				if ( isset( $_POST ) && ! empty( $_POST ) ) {
					if ( ! ( isset( $_POST['xplane-nonce'] ) || wp_verify_nonce( sanitize_key( $_POST['xplane-nonce'] ), 'xplane_nonce' ) ) ) { // Input var okay.
						return false;
					}
					$prod_quantity = isset( $_POST['prod_quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['prod_quantity'] ) ) : 1;

				}
				$variations = array();
				$mwb_variation = array();
				if ( $product->is_type( 'variable' ) ) {
					$var_product = new WC_Product_Variable( $product->get_id() );
					$variations = $var_product->get_available_variations();
					foreach ( $variations as $variation ) { 
							 $var_regular_price = $variation['display_regular_price'];
							 $display_price = $variation['display_price'];
							 $mwb_variation[] = array(
								 'variation_id'    => $variation['variation_id'],
								 'regular_price'    => $variation['display_regular_price'],
								 'sale_price'    => $variation['display_price'],
							 );
							 // $stock_disable_class = '';
							 // if( $variation['is_in_stock'] ){
							 //     $stock_disable_class = 'mwb_stock_disabled';
							 // }
					}
					$grandtotal = $display_price;
					$actualitemtotal = $actualitemtotal + ( $var_regular_price * $prod_quantity );
					$regular_price = $var_regular_price;
				}
				if ( $product->is_type( 'simple' ) ) {
					$grandtotal = $product->get_price();
					$actualitemtotal = $actualitemtotal + ( $product->regular_price * $prod_quantity );
					$regular_price = $product->get_regular_price();
				}
				/*if( !empty(is_cart()) ){}*/
				$preshiptotal = $prod_quantity * $grandtotal;
				$subtotal = $prod_quantity * $grandtotal;
				// Get the prices
				$price_excl_tax = wc_get_price_excluding_tax( $product ); // price without VAT
				$price_incl_tax = wc_get_price_including_tax( $product );  // price with VAT
				$total_tax_amount     = $price_incl_tax - $price_excl_tax; // VAT amount
				$tax_amount = ! empty( $total_tax_amount ) ? $total_tax_amount : 0;
				// for order items array
				$variation_id = 0;
			
				// if (isset($values['variation_id']) && 0 != $values['variation_id']) {
				//     $product = wc_get_product($values['variation_id']);
				//     $variation_id =  $values['variation_id'];
				// }
				
				
				$product_desc = ! empty( $product->get_short_description() ) ? $product->get_short_description() : $product->get_title();
				$sale_price = $product->get_sale_price();
				$discount_amount = 0 ;
				if ( '' === $sale_price ) {
					$sale_price = $regular_price;
				} else {
					$discount_amount = $regular_price - $sale_price;
					$totaldiscount = $totaldiscount + ( $discount_amount * $prod_quantity );
				}
				$image_id  = $product->get_image_id();
				if ( isset( $image_id ) && ! empty( $image_id ) ) {
					
					$image_url = wp_get_attachment_image_url( $image_id, 'full' );
				} else {
					$image_url = wc_placeholder_img_src();
				}
				$terms = get_the_terms( $product->get_id(), 'product_cat' );
				
				$cat_name = '';
				
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $product_cat_data ) {
						$cat_name = $product_cat_data->name;
					}
				}
				$variabled_id = isset( $values['variation_id'] ) && ! empty( $values['variation_id'] ) ? $values['variation_id'] : '';
			

				//Single Product Page Request
				$orderitems[] = array(
					'productname' => $product->get_title(),
					'sku' => $product->get_sku() . '|' . $variation_id . '',
					'quantity' => $prod_quantity,
					'productdescription' => $product_desc,
					'unitprice' => $regular_price,
					'discountamount' => $discount_amount,
					'discountunitprice' => $discount_amount,
					'originalprice' => $regular_price,
					'actualprice' => $sale_price,
					'productimage' => $image_url,
					'productid' => esc_attr( $product->get_id() ),
					'varianttitle' => $cat_name,
					'merchantproductid' => $variation_id,
						// 'variationid' => $variation_id,
						//'customfield1'    => "Madhukar",
				);
			}
			// echo "<pre>";
			// print_r($orderitems); die;
			$cart_hash_id = $cart_hash_id . ':' . $orderdate;

			$mwb_cart_data = array(
				'merchantsuccessurl' => site_url() . '/wp-json/v1/mwb_xlane',
				'enablepolling' => 'true',
				'merchantcarturl' => ( is_product() ? get_permalink( $product->get_id() ) : wc_get_cart_url() ),
				'cart_user_id' => $cart_user_id,
				'merchantid' => $merchantid,
				'secretkey' => $secretkey,
				'merchantorderid' => $cart_hash_id, // product id (for single product)
				'orderdate' => $orderdate,
				'grandtotal' => $grandtotal,
				'preshiptotal' => $preshiptotal,
				'coupon_code' => $coupon_code,
				'cgst' => 0,
				'coupondiscount' => $discount,
				'discount' => $totaldiscount,
				'subtotal' => $subtotal,
				'total' => $actualitemtotal,
				'currency' => 'INR',
				'orderitems' => $orderitems,
			);
		
			if ( is_product() ) {
				  // The product shipping class ID
				$product_class_id = $product->get_shipping_class_id();

				$zone_ids = array_keys( array( '' ) + WC_Shipping_Zones::get_zones() );
					 
				// Loop through Zone IDs
				foreach ( $zone_ids as $zone_id ) {
					// Get the shipping Zone object
					$shipping_zone = new WC_Shipping_Zone( $zone_id );
					// Get all shipping method values for the shipping zone
					$shipping_methods = $shipping_zone->get_shipping_methods( true, 'values' );
					$single_shipping  = [];
					//print_r($shipping_methods); 
					// Loop through Zone IDs
					$i = 0;
					foreach ( $shipping_methods as $instance_id => $shipping_method ) {
						// Shipping method rate ID
						$rate_id = $shipping_method->get_rate_id();
						// // Shipping method ID
						 $method_id = explode( ':', $rate_id );
						 $method_id = reset( $method_id );

						// Targeting a specific shipping method ID
						if ( 'flat_rate' === $method_id ) {
							$shipping_method_id = $method_id;
							// Get Shipping method title (label)
							$title = $shipping_method->get_title();
							$title = empty( $title ) ? $shipping_method->get_method_title() : $title;

							// Get shipping method settings data
							$data = $shipping_method->instance_settings;

							// COST:

							// For a defined shipping class
							if ( isset( $product_class_id ) && ! empty( $product_class_id ) && isset( $data[ 'class_cost_' . $product_class_id ] ) ) {
								$cost = $data[ 'class_cost_' . $product_class_id ];
							} elseif ( isset( $product_class_id ) && empty( $product_class_id ) 
								&& isset( $data['no_class_cost'] ) && $data['no_class_cost'] > 0 
							) {
								// For no defined shipping class when "no class cost" is defined
								$cost = $data['no_class_cost'];
							} else {
							// When there is no defined shipping class and when "no class cost" is defined
								$cost = $data['cost'];
							}
							//$cost = explode('*', $cost);
							//$cost = $cost[0];
							$cost = $cost;
						}

						$data = $shipping_method->instance_settings;
						global $product;
						$product_price = $product->get_sale_price();
						if ( empty( $data['min_amount'] ) ) {
							$data['min_amount'] = '0';
						}
						if ( $product_price >= $data['min_amount'] ) {

							if ( 'free_shipping' === $method_id ) {
								// Get Shipping method title (label)
								$shipping_method_id = $method_id;
								$title = $shipping_method->get_title();
								$title = empty( $title ) ? $shipping_method->get_method_title() : $title;


								// COST:

								$cost = $data['min_amount'];
								$cost = explode( '.', $cost );
								$cost = $cost[0] . '.00';
							}
						}
							
						if ( 'local_pickup' === $method_id ) {
							$shipping_method_id = $method_id;
							// Get Shipping method title (label)
							$title = $shipping_method->get_title();
							$title = empty( $title ) ? $shipping_method->get_method_title() : $title;

							// Get shipping method settings data
							$data = $shipping_method->instance_settings;

							// COST:

							$cost = $data['cost'];
							$cost = explode( '.', $cost );
							$cost = $cost[0] . '.00';
							// Testing output
							  // echo '<p><strong>'.$title.'</strong>: '.$cost.'</p>';
						}

						$single_shipping[] = array(
							'shippingcode'    => $shipping_method_id,
							'shippingname'    => $title,
							'shippingprice'    => $cost,
						);

						$i++;
					}

					if ( ! empty( $single_shipping ) && $single_shipping[0] == $single_shipping[1] ) {
						unset( $single_shipping[1] );

					}
					if ( null !== $single_shipping ) {
						$single_shipping = array_values( $single_shipping );
					}

					if ( isset( $single_shipping ) && ! empty( $single_shipping ) && is_array( $single_shipping ) && is_product() ) {
						if ( 1 >= count( $single_shipping ) ) {
							$mwb_cart_data['shippingoptions'] = $single_shipping;
						}
					}              
				}
			}
			if ( isset( $shippings ) && ! empty( $shippings ) && is_array( $shippings ) && is_cart() ) {
				$mwb_cart_data['shippingoptions'] = $shippings;
			
			}
			// echo "<pre>";
			//    print_r($mwb_cart_data);die;
			$mwb_cart_data_encode = json_encode( $mwb_cart_data ); 
			
			$secretKey = substr( $secretkey, 0, 16 );
			include_once XPRESSLANE_DIRPATH . 'includes/class-xpresslane-aes-encrypt-decrypt.php';
			$mwb_encrypted_data = AesCipher::encrypt( $secretKey, $mwb_cart_data_encode );
			$mwb_encrypted_data = $mwb_encrypted_data->getData();
			$button_text = $this->mwb_xlane_intgn_button_text();
			$mwb_site_url_option = get_option( 'mwb-xlane-url-option' );
			if ( 'mwb_xlane_live_url_option' === $mwb_site_url_option ) {
				$mwb_cart_url = get_option( 'mwb-xlane-live-url' );
			} elseif ( 'mwb_xlane_stage_url_option' === $mwb_site_url_option ) {
				$mwb_cart_url = get_option( 'mwb-xlane-stage-url' );
			}
			//Size and Font on product page buttons
			$mwb_prod_page_text = get_option( 'mwb-xlane-text-product' );
			$mwb_prod_page_text_size = get_option( 'mwb-xlane-size-prod', '10' ); 
			$mwb_prod_page_text_font = get_option( 'mwb-xlane-font-prod', 'Montserrat, sans-serif' );
			$button_prod_color = $this->mwb_xlane_intgn_button_back_color( 'mwb-xlane-product-button-color' );
			$button_prod_text_color = $this->mwb_xlane_intgn_button_text_color( 'mwb-xlane-product-button-text-color' );
			$button_prod_custom_text = get_option( 'mwb-xlane-product-custom-text' );
			$button_prod_custom_size = get_option( 'mwb-xlane-product-custom-size', '10' );
			$button_prod_custom_font = get_option( 'mwb-xlane-product-custom-font', 'Montserrat, sans-serif' );

			//Size and Font on Cart page buttons
			$mwb_cart_page_text = get_option( 'mwb-xlane-text-cart' );
			$mwb_cart_page_text_size = get_option( 'mwb-xlane-size-cart' ); 
			$mwb_cart_page_text_font = get_option( 'mwb-xlane-font-cart' );
			$button_cart_color = $this->mwb_xlane_intgn_button_back_color( 'mwb-xlane-cart-button-color' );
			$button_cart_text_color = $this->mwb_xlane_intgn_button_text_color( 'mwb-xlane-cart-button-text-color' );
			$button_cart_custom_text = get_option( 'mwb-xlane-cart-custom-text' );
			$button_cart_custom_size = get_option( 'mwb-xlane-cart-custom-size', '10' );
			$button_cart_custom_font = get_option( 'mwb-xlane-cart-custom-font', 'Montserrat, sans-serif' );

			
			$mwb_padding_1 = get_option( 'mwb-xlane-btn-padding-1' );
			$mwb_padding_2 = get_option( 'mwb-xlane-btn-padding-2' );
			$mwb_padding_3 = get_option( 'mwb-xlane-btn-padding-3' );
			$mwb_padding_4 = get_option( 'mwb-xlane-btn-padding-4' );
			$mwb_cart_padding_1 = get_option( 'mwb-xlane-btn-cart-padding-1' );
			$mwb_cart_padding_2 = get_option( 'mwb-xlane-btn-cart-padding-2' );
			$mwb_cart_padding_3 = get_option( 'mwb-xlane-btn-cart-padding-3' );
			$mwb_cart_padding_4 = get_option( 'mwb-xlane-btn-cart-padding-4' );
			/*$mwb_btn_width = get_option('mwb-xlane-btn-width');*/
			$mwb_logo_img = get_option( 'mwb-xlane-img-logo' );
			$img = plugin_dir_url( __FILE__ ) . 'css/expresslane.png';
			$white_logo_class = '';
			if ( 'logo-black' === $mwb_logo_img ) {
				$white_logo_class = '';
				$img = plugin_dir_url( __FILE__ ) . 'css/expresslane.png';
			} elseif ( 'logo-white' === $mwb_logo_img ) {
				$white_logo_class = 'white-logo';
				$img = plugin_dir_url( __FILE__ ) . 'css/white_bg_logo.png';
			}
			$mwb_btn_radius = get_option( 'mwb-xlane-btn-radius' );
			$mwb_btn_width_single = get_option( 'mwb-xlane-btn-width-single' );

			
			$mwb_btn_height_single = get_option( 'mwb-xlane-btn-height-single' );
			$mwb_btn_width_cart = get_option( 'mwb-xlane-btn-width-cart' );
			$mwb_btn_height_cart = get_option( 'mwb-xlane-btn-height-cart' );
			$button_logo_type    = get_option( 'mwb-xlane-logo-button' );
			
			$class = '';
			if ( is_product() ) {
				$action_url = esc_url( get_permalink( $product->get_id() ) );
				$class = "class='ajax cart'";
			} else {
				$action_url = esc_url( $mwb_cart_url );
			}

			 $cart_class = '';
			if ( is_cart() ) {
				$cart_class = 'wc-proceed-to-checkout';
			}
			?>
			<div class="mwb_xlane_payment_button <?php echo esc_attr( $cart_class ); ?>" style="padding:10px 0px;">
				<form id="order" method="POST" action="<?php echo esc_url( $action_url ); ?>" <?php echo esc_attr( $class ); ?> >
					<?php wp_nonce_field( 'xplane_nonce', 'xplane-nonce' ); ?>
					<input type="hidden" id="merchantid" name="merchantid" value="<?php echo esc_attr( $merchantid ); ?>" />
			<?php if ( is_product() ) { ?>
								<input type="hidden" id="prod_quantity" class="single_quantity" name="prod_quantity" value="1"  />
								<?php
								$postvalue = base64_encode( serialize( $mwb_cart_data ) );
								?>
								<input type="hidden" id="mwb_cart_data" name="mwb_cart_data" value="<?php echo esc_attr( $postvalue ); ?>" />
								<input type="hidden" id="action_url" name="action_url" value="<?php echo esc_url( $mwb_cart_url ); ?>" />
								
				<?php 
				//global $product; 
				global $post;
								$product_id = $post->ID;
								$product = wc_get_product( $product_id );
				if ( $product->is_type( 'variable' ) ) {
					$product_variations = base64_encode( serialize( $mwb_variation ) ); 
					?>
									<input type="hidden" id="attribute_pa_size" name="attribute_pa_size" value="" />
									<input type="hidden" id="product_variations" name="product_variations" value="<?php echo esc_attr( $product_variations ); ?>" />
									<input type="hidden" id="mwb_variation_id" name="mwb_variation_id" value="" />
				<?php } ?>
			<?php } else { ?>

							<input type="hidden" id="checksum" name="checksum" value="<?php echo esc_attr( $mwb_encrypted_data ); ?>" />
					<!-- new button design -->
			<?php 
			}

			if ( isset( $mwb_prod_page_text ) && ! empty( $mwb_prod_page_text ) && is_product() ) {
				// $single_prod_padding = '';
				// if( ($mwb_padding_1 && $mwb_padding_2 && $mwb_padding_3 && $mwb_padding_4 ) != '' ){
				//     $single_prod_padding = 'padding:'.$mwb_padding_1.'px '.$mwb_padding_2.'px '.$mwb_padding_3.'px '.$mwb_padding_4.'px;';
				// } 
				$this->mwb_xlane_display_button(
					'mwb_single_add_to_cart_button button alt',
					$button_logo_type,
					'product'
				);
							
			}
			if ( isset( $mwb_cart_page_text ) && ! empty( $mwb_cart_page_text ) && ( is_page( 'cart' ) || is_cart() ) ) { 
				// $cart_padding = '';
				// if( ($mwb_cart_padding_1 && $mwb_cart_padding_2 && $mwb_cart_padding_3 && $mwb_cart_padding_4 ) != '' ){
				//     $cart_padding = 'padding:'.$mwb_cart_padding_1.'px '.$mwb_cart_padding_2.'px '.$mwb_cart_padding_3.'px '.$mwb_cart_padding_4.'px;';
				// }
				$this->mwb_xlane_display_button(
					'mwb_cart_checkout_button checkout-button button alt wc-forward',
					$button_logo_type,
					'cart'
				);

						
			}
			?>
					<!-- end new button design -->
				</form>
			</div>
			<?php
		}
	}

	/**
	 * This function is used to create the endpoint for success url.
	 *
	 * @name  mwb_xlane_intgn_endpoint_url
	 * @since 1.0.0
	 */
	public function mwb_xlane_intgn_endpoint_url() {

		register_rest_route(
			'v1',
			'mwb_xlane_webhook',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'mwb_xlane_intgn_webhook_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		// Route to verify the coupon 
		register_rest_route(
			'v1',
			'mwb_xlane_verify_coupon',
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'mwb_xlane_verify_coupon_callback' ),
				'permission_callback' => '__return_true',
			)
		);

	}


	/**
	 * This function is used to verify the coupon code .
	 *
	 * @name  mwb_xlane_verify_coupon
	 * @since 1.0.0
	 * @param $request request object.
	 */
	public function mwb_xlane_verify_coupon_callback( $request ) {
		global $woocommerce;

		$headers = $request->get_headers();

		if ( is_array( $headers ) && ! array_key_exists( 'authorization', $headers ) ) {
			$headers = $request->get_header( 'Authentication' );
		} else {
			$headers = $request->get_header( 'authorization' );
		}
		
		$auth_data    = explode( 'Basic', $headers );
		
		$auth_details = base64_decode( $auth_data[1] );

		if ( empty( $auth_details ) ) {
			return rest_ensure_response( 'CliendId and Clientsecret should not be empty' );
		}
		$auth_verify  = explode( ':', $auth_details );
		
		//Validate the Cred
		if ( ! ( 'xpress@123' === $auth_verify[1] ) || ! ( 'xpresslane' === $auth_verify[0] ) ) {

			return rest_ensure_response( 'Invlaid cliendId and clientsecret' );
		}
		
		$coupon_code = $request->get_param( 'coupon_code' );
		
		$order_details = $request->get_params( 'JSON' );
		
		//Return if no coupon code or order items
		if ( empty( $coupon_code ) && empty( $order_items ) ) {
			return rest_ensure_response( 'Coupon Code or Order Items are should not be empty' );
		}

		$coupon_details = new WC_Coupon( $coupon_code );
		
		
		//Return if invalid coupon code
		if ( ! isset( $coupon_details->amount ) || 0 === $coupon_details->get_amount() ) {
			$coupon_response->coupon_applied = false;
			$coupon_response->discounted_amount = 0;
			return rest_ensure_response( json_encode( $coupon_response ) );
		}
		

		$coupon_data = $this->mwb_xlane_get_coupon_applied( $order_details['order'], $coupon_details, $coupon_code );
		  
		return rest_ensure_response( json_encode( $coupon_data ) );
		

	}
	/**
	 * This function is used to apply the coupon code .
	 *
	 * @name   mwb_xlane_apply_coupon
	 * @since  1.0.0
	 * @param  $order_items object, $discount_type String.
	 * @return $coupon_data object
	 */

	public function mwb_xlane_get_coupon_applied( $order_details, $coupon_details, $coupon_code ) {
		
		global $woocommerce;
		$coupon_validity = false;
		$coupon_data     = new stdClass();
		$coupon_value    = $coupon_details->get_amount();
		$coupon_type     = $coupon_details->get_discount_type();
		$order_total     = $order_details['subtotal'];
		$coupon_data->coupon_applied    = true;
		$coupon_data->discounted_amount = 0;
		$coupon_data->disable_cod = false;
		
		if ( null === WC()->cart && function_exists( 'wc_load_cart' ) ) {
			wc_load_cart();
		}
	
		foreach ( $order_details['order_items'] as $key => $order ) {
			
			if ( $order['merchantproductid'] ) {

				WC()->cart->add_to_cart( $order['productid'], $order['quantity'], $order['merchantproductid'] );
			} else {
				
				WC()->cart->add_to_cart( $order['productid'], $order['quantity'] );
			}       
		}
		
		
		if ( ! isset( WC()->session->chosen_payment_method ) || WC()->session->chosen_payment_method == '' || ! is_checkout() ) {
			WC()->session->set( 'chosen_payment_method', 'xpresslane' );
		}

		$coupon_check = WC()->cart->apply_coupon( $coupon_code );
		
		if ( $coupon_check ) {
			$discount_ex_total = WC()->cart->get_discount_total();
			$discount_tax   = WC()->cart->get_discount_tax();

			$discount_total = $discount_ex_total + $discount_tax;
			
			$coupon_data->coupon_applied = true;
			$coupon_data->discounted_amount = (float) $discount_total;
			
			$meta_cod = $coupon_details->get_meta( '_wt_sc_payment_methods' ) !== null ? $coupon_details->get_meta( '_wt_sc_payment_methods' ) : '';
		
			if ( $meta_cod ) {
				$meta_cods = explode( ',', $meta_cod );
			}
		
			if ( $meta_cod && ! in_array( 'cod', $meta_cods ) ) {
				$coupon_data->disable_cod = true;
			}
			
			return $coupon_data;
			
		} else {
			$coupon_data->coupon_applied = false;
			$coupon_data->discounted_amount = 0;
			
			return $coupon_data;
		}
	}    
	/**
	 * This function is used to process order webhook when success payment.
	 *
	 * @name  mwb_xlane_intgn_webhook_callback
	 * @since 1.0.0
	 * @param $request request object.
	 */
	public function mwb_xlane_intgn_webhook_callback( $request ) {
		global $wpdb;
		
		$log = wc_get_logger();
			   $head = '<-------------------WEBHOOK NONCE ------------------->/n';
			   $log_text = $head . print_r( (object) $request, true );
			   $context = array( 'source' => 'xp_nonce_log' );
			   $log->log( 'debug', $log_text, $context );
		
		
		$table_name = $wpdb->prefix . 'xpresslane_orders';
		$headers = $request->get_headers();
		if ( is_array( $headers ) && ! array_key_exists( 'authorization', $headers ) ) {
			$headers = $request->get_header( 'Authentication' );
		} else {
			$headers = $request->get_header( 'authorization' );
		}
		$headers = explode( ' ', $headers );
		$basic_auth = base64_decode( $headers[1] );
		if ( isset( $basic_auth ) && ! empty( $basic_auth ) ) {
			$user_details = explode( ':', $basic_auth );
			if ( is_array( $user_details ) && ! empty( $user_details ) ) {
				if ( 'xpresslane' === $user_details[0] && 'xpress@123' === $user_details[1] ) {
					
					$data = $request->get_body();
				
					$payload_data = $data;
					$mwb_data_decrypt = json_decode( $data, true );
					
					if ( isset( $mwb_data_decrypt['merchantorderid'] ) && '' != $mwb_data_decrypt['merchantorderid'] ) {
						file_put_contents( XPRESSLANE_DIRPATH . 'db-log.txt', $payload_data );
						//                         $sql = 'SELECT * 
						//                                 FROM '.$table_name.' 
						//                                 WHERE merchant_order_id = "'.$mwb_data_decrypt['merchantorderid'].'"';

						//                         $result = $wpdb->get_results($sql);
						// print_r($result);die("-->hell");
						//$time = 'a115dd0c3ecdf81751bc1e105763a8dc:2021-10-04T09:10:28.0Z:2021-10-04T09:10:28.0Z';
						// print_r($result);die;
						$xp_merchant_order_id = get_option( $mwb_data_decrypt['merchantorderid'], true );
						if ( ! empty( $xp_merchant_order_id ) ) {
						
							if ( ! empty( $xp_merchant_order_id ) && $mwb_data_decrypt['merchantorderid'] == $xp_merchant_order_id ) {
								
									
								$check_merchant = get_post_meta( 'mwxplane', $mwb_data_decrypt['merchantorderid'] );
								$merchant_orderId = $xp_merchant_order_id;
								$order_response = $this->create_order_for_woo( $mwb_data_decrypt, $xp_merchant_order_id );
								file_put_contents( XPRESSLANE_DIRPATH . 'db1-log.txt', json_encode( $order_response ) );
								return $order_response;
							}                       
						} else {
							
							return new WP_Error( 'woo error', __( 'order not pre existed', 'xpresslane-integration-for-woocommerce' ) );
						}
					} else {
						return new WP_Error( 'payload error', __( 'payload data missing', 'xpresslane-integration-for-woocommerce' ) );
					}
				} else {
					return new WP_Error( 'credentials', __( 'not correct authrization credentials', 'xpresslane-integration-for-woocommerce' ) );
				}
			} else {
				return new WP_Error( 'missing user details', __( 'User details are missing', 'xpresslane-integration-for-woocommerce' ) );
			}
		} else {
			return new WP_Error( 'auth issue', __( 'not authenticated', 'xpresslane-integration-for-woocommerce' ) );
		}
	}

	public function create_order_for_woo( $mwb_data_decrypt, $merchant_orderId ) {
		
		
		$log = wc_get_logger();
			   $head = '<-------------------WEBHOOK NONCE ------------------->/n';
			   $log_text = $head . print_r( (object) $merchant_orderId, true );
			   $context = array( 'source' => 'xp_nonce_log' );
			   $log->log( 'debug', $log_text, $context );
		
		
		global $woocommerce;
		global $wpdb;
		$table_name = $wpdb->prefix . 'xpresslane_orders';
		$get_merchant_orderid  = '';
		// $mwb_data_decrypt = get_option('xlane_payload', false);
		$merchant_order_id = explode( ':', $mwb_data_decrypt['merchantorderid'] );
		$get_merchant_orderid = get_post_meta( $mwb_data_decrypt['merchantorderid'], 'xl_merchant_order_id' ); 

		if ( empty( $get_merchant_orderid ) || ! ( $get_merchant_orderid ) ) {
			
			update_post_meta( $mwb_data_decrypt['merchantorderid'], 'xl_merchant_order_id', true ); 
		} else {
			return;
		}
		if ( isset( $mwb_data_decrypt['xpresslane_payment_status'] ) && 'SUCCESS' === $mwb_data_decrypt['xpresslane_payment_status'] ) {
			$country_id = '';
		
			if ( isset( $mwb_data_decrypt['billing_countryid'] ) ) {
				$country_id = $mwb_data_decrypt['billing_countryid'];
			} elseif ( isset( $mwb_data_decrypt['billing_countryid'] ) ) {
				$country_id = $mwb_data_decrypt['billing_countryid'];
			}
			$state_code = $mwb_data_decrypt['billing_region'];
			$ava_states = WC()->countries->get_states( $mwb_data_decrypt['billing_countryid'] );
			 
			
			if ( ! empty( $ava_states ) ) {
				foreach ( $ava_states as $state_key => $state_val ) {
					
					if ( strtolower( $mwb_data_decrypt['billing_region'] ) == strtolower( $state_val ) ) {
						$state_code = $state_key;
						
					}
				}
			}
			$mwb_customer_address['billing'] = array(
				'first_name' => $mwb_data_decrypt['billing_firstname'],
				'last_name' => $mwb_data_decrypt['billing_lastname'],
				'company' => '',
				'country' => $country_id,
				'state' => $state_code,
				'address_1' => $mwb_data_decrypt['billing_street_1'],
				'address_2' => $mwb_data_decrypt['billing_street_2'],
				'city' => $mwb_data_decrypt['billing_city'],
				'postcode' => $mwb_data_decrypt['billing_postcode'],
				'phone' => $mwb_data_decrypt['billing_telephone'],
				'email' => $mwb_data_decrypt['email'],
				'shipping_method' => $mwb_data_decrypt['shipping_code'],
				'payment_method' => 'Xpresslane : ' . $mwb_data_decrypt['paymentmode'],
			);
			$mwb_customer_address['shipping']['first_name'] = $mwb_data_decrypt['billing_firstname'];
			$mwb_customer_address['shipping']['last_name'] = $mwb_data_decrypt['billing_lastname'];
			$mwb_customer_address['shipping']['company'] = '';
			$mwb_customer_address['shipping']['country'] = $mwb_data_decrypt['billing_countryid'];
			$mwb_customer_address['shipping']['state'] = $state_code;
			$mwb_customer_address['shipping']['address_1'] = $mwb_data_decrypt['billing_street_1'];
			$mwb_customer_address['shipping']['address_2'] = $mwb_data_decrypt['billing_street_2'];
			$mwb_customer_address['shipping']['city'] = $mwb_data_decrypt['billing_city'];
			$mwb_customer_address['shipping']['postcode'] = $mwb_data_decrypt['billing_postcode'];
		  
			$orderShippingCode = isset( $mwb_data_decrypt['shipping_code'] ) && '' !== $mwb_data_decrypt['shipping_code'] ? $mwb_data_decrypt['shipping_code'] : '';
			$orderShippingTitle = isset( $mwb_data_decrypt['shipping_title'] ) && '' !== $mwb_data_decrypt['shipping_title'] ? $mwb_data_decrypt['shipping_title'] : '';
			$orderShippingPrice = isset( $mwb_data_decrypt['shipping_price'] ) && '' !== $mwb_data_decrypt['shipping_price'] ? $mwb_data_decrypt['shipping_price'] : '';
			// file_put_contents(XPRESSLANE_DIRPATH.'db1-log.txt', $mwb_customer_address);
			$order = wc_create_order();
			$calculate_taxes_for = array(
				'country'  => ! empty( $mwb_customer_address['shipping']['country'] ) ? $mwb_customer_address['shipping']['country'] : $mwb_customer_address['billing']['country'],
				'state'    => ! empty( $mwb_customer_address['shipping']['state'] ) ? $mwb_customer_address['shipping']['state'] : $mwb_customer_address[ [ 'billing' ]['state'] ],
				'postcode' => ! empty( $mwb_customer_address['shipping']['postcode'] ) ? $mwb_customer_address['shipping']['postcode'] : $mwb_customer_address['billing']['postcode'],
				'city'     => ! empty( $mwb_customer_address['shipping']['city'] ) ? $mwb_customer_address['shipping']['city'] : $mwb_customer_address['billing']['city'],
			);
			$orderItems = isset( $mwb_data_decrypt['orderitems'] ) && '' !== $mwb_data_decrypt['orderitems'] ? $mwb_data_decrypt['orderitems'] : array();
			if ( is_array( $orderItems ) && ! empty( $orderItems ) ) {
				foreach ( $orderItems as $orderItem ) { 
					 $_product = wc_get_product( $orderItem['productid'] );
					
					
					if ( 'variable' === $_product->get_type() ) {
						   $membershipProduct = new WC_Product_Variable( $orderItem['productid'] );
						$theMemberships = $membershipProduct->get_available_variations();
						$variationsArray = array();
						
						foreach ( $theMemberships as $membership ) {
							$var_id = $orderItem['merchantproductid'];
							if ( $membership['variation_id'] == $var_id ) {
								$variationID = $var_id;
								$variationsArray['variation'] = $membership['attributes'];
							}
						}
						if ( $variationID ) {
							$varProduct = new WC_Product_Variation( $variationID );
							$item_id = $order->add_product( $varProduct, $orderItem['quantity'], $variationsArray );
							$item    = $order->get_item( $item_id, false );
							 //Custom image or properties adding in order 
							if ( isset( $orderItem['properties'] ) ) {
								foreach ( $orderItem['properties'] as $key => $props ) {
										$media_url = wp_get_attachment_url( $props->id );
										$item->add_meta_data( __( 'Uploaded Media', 'woo-addon-uploads' ), $media_url );
										$item->save();
									
								}                           
							}
							$item->calculate_taxes( $calculate_taxes_for );
							$item->save();
						}                   
					} else {
						$item_id = $order->add_product( $_product, $orderItem['quantity'] );
						$item    = $order->get_item( $item_id, false );
						//Custom image or properties adding in order 
						//Custom image or properties adding in order 
						if ( isset( $orderItem['properties'] ) ) {
							$props_items = json_decode( $orderItem['properties'] );
							foreach ( $props_items as $key => $props ) {
									$media_url = wp_get_attachment_url( $props->id );
									$item->add_meta_data( __( 'Uploaded Media', 'woo-addon-uploads' ), $media_url );
									$item->save();
								
							}                       
						}
						$item->calculate_taxes( $calculate_taxes_for );
						$item->save();
					}
					if ( ! empty( $orderItem['customfield1'] ) ) {
						 $explode_fields = explode( ' [] ', $orderItem['customfield1'] );

						 $arrays = array_chunk( $explode_fields, 2 );
 
						foreach ( $arrays as $key => $value ) {
						
							$explode_fields = array_filter( $explode_fields );
							$value[1] = str_replace( '[]', '', $value[1] );
							wc_add_order_item_meta( $item_id, $value[0], $value[1] );
						}                   
					}
				}
			}

			$order->set_address( $mwb_customer_address['billing'], 'billing' );
			$order->set_address( $mwb_customer_address['shipping'], 'shipping' );
			$order->save();
			// print_r($order->get_data());die("-->hell");
			
			$item = new WC_Order_Item_Shipping();
			$item->set_method_title( $orderShippingTitle );
			$item->set_method_id( $orderShippingCode );
			
			$tax_for   = array(
				'country'   => $order->get_shipping_country(),
				'state'     => $order->get_shipping_state(),
				'postcode'  => $order->get_shipping_postcode(),
				'city'      => $order->get_shipping_city(),
				'tax_class' => $item->get_tax_class(),
			);
			
			$tax_rates = WC_Tax::find_rates( $tax_for );
			$taxes     = WC_Tax::calc_tax( $orderShippingPrice, $tax_rates, true );
			$subtotal_taxes = WC_Tax::calc_tax( $orderShippingPrice, $tax_rates, true );
			$arr = array_key_first( $taxes );
			$item->set_total( $orderShippingPrice - $taxes[ $arr ] );
			$taxes_sum = array_sum( $taxes );
			$built_taxes[2] = (string) $taxes[ $arr ];
						
			$item->set_taxes(
				array(
					'total' => $built_taxes,
					'subtotal' => $built_taxes,
				) 
			);
			$item->save();

			$order->add_item( $item );
			$order->update_taxes();
			$order->calculate_totals( true );
			$payment_name = WC()->payment_gateways->payment_gateways()['xpresslane'];
			$name = '(' . $mwb_data_decrypt['paymentmode'] . ')';
			$order->set_payment_method( $payment_name );
		
			if ( ! empty( $mwb_data_decrypt['payment_mode_discount'] ) || ! empty( $mwb_data_decrypt['payment_mode_charge'] ) ) {
				 
				if ( isset( $mwb_data_decrypt['payment_mode_discount_value'] ) && ! empty( $mwb_data_decrypt['payment_mode_discount_value'] ) ) {

							
					$order->calculate_totals();
					$subtotal = $order->get_subtotal();
					$item     = new WC_Order_Item_Fee();

					$discount = (float) str_replace( ' ', '', $mwb_data_decrypt['payment_mode_discount_value'] );
					$tax_for   = array(
						'country'   => $order->get_shipping_country(),
						'state'     => $order->get_shipping_state(),
						'postcode'  => $order->get_shipping_postcode(),
						'city'      => $order->get_shipping_city(),
						'tax_class' => $item->get_tax_class(),
					);
					$tax_rates = WC_Tax::find_rates( $tax_for );
					$taxes     = WC_Tax::calc_tax( $discount, $tax_rates, true );
					$subtotal_taxes = WC_Tax::calc_tax( $discount, $tax_rates, true );
					$discount = -$discount;
					$tax_val = 0;
					 
					
					if ( ! empty( $taxes ) ) {
						$tax_val = array_sum( $taxes );
					} else {
						$item->calculate_taxes( $tax_for );
					}
					$title = 'Payment Mode Discount ( ' . $mwb_data_decrypt['payment_mode_discount'] . ' )';
					$item->set_name( $title );
					$item->set_amount( $discount + $tax_val );
					$item->set_total( $discount + $tax_val );    
					$taxes_sum = array_sum( $taxes );
					$built_taxes[2] = '-' . 1;
					$item->set_total_tax( -$taxes_sum );
					$item->set_taxes(
						array(
							'total' => $built_taxes,
							'subtotal' => $built_taxes,
						) 
					);
					$item->save();
					$order->add_item( $item );
					$order->update_taxes();
					$order->calculate_totals( true );
					$order->save();
				}
				if ( isset( $mwb_data_decrypt['payment_mode_charge_value'] ) && ! empty( $mwb_data_decrypt['payment_mode_charge_value'] ) ) {
					$order->calculate_totals();
					$subtotal = $order->get_subtotal();
					$item     = new WC_Order_Item_Fee();
					$discount = (float) str_replace( ' ', '', $mwb_data_decrypt['payment_mode_charge_value'] );
					$tax_for   = array(
						'country'   => $order->get_shipping_country(),
						'state'     => $order->get_shipping_state(),
						'postcode'  => $order->get_shipping_postcode(),
						'city'      => $order->get_shipping_city(),
						'tax_class' => $item->get_tax_class(),
					);
					$tax_rates = WC_Tax::find_rates( $tax_for );
					$taxes     = WC_Tax::calc_tax( $discount, $tax_rates, true );
					$subtotal_taxes = WC_Tax::calc_tax( $discount, $tax_rates, true );

					$discount = +$discount;
					$title = 'Payment Mode Charge ( ' . $mwb_data_decrypt['payment_mode_charge'] . ' )';
					$item->set_name( $title );
					$item->set_amount( $discount - array_sum( $taxes ) );
					$item->set_total( $discount - array_sum( $taxes ) );
					$taxes_sum = array_sum( $taxes );
					$built_taxes[2] = '-' . $taxes[2];
					$item->set_total_tax( -$taxes_sum );
					$item->set_taxes(
						array(
							'total' => $built_taxes,
							'subtotal' => $built_taxes,
						) 
					);                
					$item->save();
					$order->add_item( $item );
					$order->update_taxes();
					$order->calculate_totals( true );
					$order->save();
				}
				
				// for coupon with xpresslane discount
				if ( ! empty( $mwb_data_decrypt['coupon_code'] ) ) {
					if ( ! empty( $mwb_data_decrypt['coupon_discount'] ) ) {
						$discount_total = $mwb_data_decrypt['coupon_discount'];
					}
					
					if ( null === WC()->cart && function_exists( 'wc_load_cart' ) ) {
						wc_load_cart();
					}foreach ( $orderItems as $orderItem ) { 
						 
						if ( $orderItem['merchantproductid'] ) {
				
							WC()->cart->add_to_cart( $orderItem['productid'], $orderItem['quantity'], $orderItem['merchantproductid'] );
						} else {

							WC()->cart->add_to_cart( $orderItem['productid'], $orderItem['quantity'] );
						}
					}
					
					$abc = $order->apply_coupon( $mwb_data_decrypt['coupon_code'] );
				   
					$order_id = $order->save();
					$woocommerce->cart->empty_cart();
				}
			} elseif ( ! empty( $mwb_data_decrypt['coupon_code'] ) ) {
				
				if ( null === WC()->cart && function_exists( 'wc_load_cart' ) ) {
						wc_load_cart();
				}foreach ( $orderItems as $orderItem ) { 
						 
					if ( $orderItem['merchantproductid'] ) {
				
						WC()->cart->add_to_cart( $orderItem['productid'], $orderItem['quantity'], $orderItem['merchantproductid'] );
					} else {

						WC()->cart->add_to_cart( $orderItem['productid'], $orderItem['quantity'] );
					}
				}
				
				 $order->apply_coupon( strtolower( $mwb_data_decrypt['coupon_code'] ) );
				
				
				$order_id = $order->save();
				// $order->calculate_totals(false);
			} else {    
				$order->save();
				$order->calculate_totals(); 
			}
			if ( isset( $order ) && ! empty( $order ) ) {
				$order_id = $order->get_id();
				$user_data = get_user_by( 'email', $mwb_data_decrypt['email'] );
				if ( isset( $user_data->ID ) && '' != $user_data->ID ) {
					  $userid = $user_data->ID;
					  update_post_meta( $order_id, '_customer_user', $userid );
				}
				update_post_meta( $order_id, 'xpresslane_response_data', $mwb_data_decrypt );

				//Get Pg order id and payment    
				if ( isset( $mwb_data_decrypt['payment']['pg_order_id'] ) && ! empty( $mwb_data_decrypt['payment']['pg_order_id'] ) ) {

					update_post_meta( $order_id, 'mwb_xlane_pg_order_id', $mwb_data_decrypt['payment']['pg_order_id'] );
				}
				if ( isset( $mwb_data_decrypt['payment']['pg_payment_id'] ) && ! empty( $mwb_data_decrypt['payment']['pg_payment_id'] ) ) {
					update_post_meta( $order_id, 'mwb_xlane_pg_payment_id', $mwb_data_decrypt['payment']['pg_payment_id'] );
				}
				update_post_meta( $order_id, 'mwb_xlane_trasaction_id', $mwb_data_decrypt['xpresslane_txn_id'] );
				update_post_meta( $order_id, 'payment_mode', $mwb_data_decrypt['paymentmode'] );
				update_post_meta( $order_id, 'mwb_xlane_trasaction_data', $mwb_data_decrypt );
				update_post_meta( $order_id, '_transaction_id', $mwb_data_decrypt['xpresslane_txn_id'] );
				if ( isset( $mwb_data_decrypt['paymentmode'] ) && 'COD' === $mwb_data_decrypt['paymentmode'] ) {
					update_post_meta( $order_id, '_payment_method', 'cod' );
				}
				update_post_meta( $order_id, 'mwb_xlane_merchantorderid', $mwb_data_decrypt['merchantorderid'] );
				if ( $merchant_orderId ) {
							update_post_meta( $order_id, 'mwb_xlane_order_type', 'webhook orders' );
				} else {
					update_post_meta( $order_id, 'mwb_xlane_order_type', 'Redirect' );
				}
				$status = get_option( 'mwb-xlane-order-status', 'processing' );
				if ( isset( $mwb_data_decrypt['paymentmode'] ) && 'COD' === $mwb_data_decrypt['paymentmode'] ) {
					$order->update_status( $status );
				} else {
					$order->update_status( 'processing' );
				}
				
				$oredr_note = __( 'Xpresslane merchant order id ' . $mwb_data_decrypt['merchantorderid'], 'xpresslane-integration-for-woocommerce' );
				$order->add_order_note( $oredr_note );
				$oredr_note = __( 'Xpresslane transaction id ' . $mwb_data_decrypt['xpresslane_txn_id'], 'xpresslane-integration-for-woocommerce' );
				$order->add_order_note( $oredr_note );
				if ( $merchant_orderId ) {
					$order->add_order_note( 'Order created via xpressLane webhook' );
				} else {
					$order->add_order_note( 'Order created via' . $mwb_data_decrypt['xpresslane_txn_id'] );
				}
				$return_url = $order->get_checkout_order_received_url();
				
				if ( ! empty( $merchant_orderId ) ) {
					update_option( 'xl_wc_order' . $mwb_data_decrypt['merchantorderid'], $order->get_id() );
					$order_data = array(
						'order_id' => $order->get_id(),
						'redirect_url' => $return_url,
					);
					
					$log = wc_get_logger();
			   $head = '<-------------------WEBHOOK NONCE ------------------->/n';
			   $log_text = $head . print_r( (object) $order_data, true );
			   $context = array( 'source' => 'xp_nonce_log' );
			   $log->log( 'debug', $log_text, $context );
					
					return rest_ensure_response( json_encode( $order_data ) );
				}
				wp_safe_redirect( $return_url );
				exit();
			}
		} else {
			return new WP_Error( 'error', __( "can't create orders", 'xpresslane-integration-for-woocommerce' ) );
		}
	}

	/**
	 * This function is used to show Xpresslane checkout button in mini cart.
	 *
	 * @name  display_mini_xpresslane_button
	 * @since 1.0.0
	 */
	public function display_mini_xpresslane_button() {
		global $woocommerce;
		//session_start();
		if ( 0 === WC()->cart->get_cart_contents_count() || ! WC()->cart->needs_payment() ) {
			return;
		}
	   
		if ( $this->mwb_xlane_intgn_enable_plugin() ) {
			$mwb_data = array();
			$mwb_data[ $woocommerce->cart->get_cart_hash() ] = WC()->cart;
			$retrive_data = WC()->session->get( 'mwb_xlane_cart_session' );

			if ( empty( $retrive_data ) ) {
				WC()->session->set( 'mwb_xlane_cart_session', $mwb_data );
				// $_SESSION['mwb_xlane_cart_session'] = $mwb_data;
			}
			//    WC()->session->set('mwb_xlane_cart_session', $mwb_data );

			$cart_hash_id = $woocommerce->cart->get_cart_hash();
			$merchantid = $this->mwb_xlane_intgn_merchant_id();
			$secretkey = $this->mwb_xlane_intgn_merchant_key();
			$grandtotal = $woocommerce->cart->total;
			$subtotal = $woocommerce->cart->subtotal;
			$orderdate = gmdate( 'Y-m-d\TH:i:s', current_time( 'timestamp' ) );
			$orderdate = $orderdate . '.0Z';
			$cart_hash_id = $cart_hash_id . ':' . $orderdate;
			$coupon_code = ! empty( $woocommerce->cart->get_applied_coupons() ) ? $woocommerce->cart->get_applied_coupons() : '';
			$meta_cod    = '';
			if ( isset( $coupon_code ) && ! empty( $coupon_code ) && is_array( $coupon_code ) ) {
				$coupon_code = implode( ',', $coupon_code );
			}
			if ( $coupon_code ) {
				$coupon_details = new WC_Coupon( $coupon_code );

				if ( $coupon_details ) {
					$meta_cod = $coupon_details->get_meta( '_wt_sc_payment_methods' ) !== null ? $coupon_details->get_meta( '_wt_sc_payment_methods' ) : '';
				}
			}
			
			
			if ( $meta_cod ) {
				$meta_cods = explode( ',', $meta_cod );
			}
			
			if ( $meta_cod && in_array( 'cod', $meta_cods ) ) {
				$coupon_data->enable_cod = true;
			}

			$discount = ! empty( $woocommerce->cart->get_cart_discount_total() ) ? $woocommerce->cart->get_cart_discount_total() : 0;
			$discount_tax = ! empty( $woocommerce->cart->get_cart_discount_tax_total() ) ? $woocommerce->cart->get_cart_discount_tax_total() : 0;
			$discount += $discount_tax;

			if ( 0 !== $discount ) {    
				$preshiptotal = $woocommerce->cart->subtotal - $discount;    
			} else {    
				$preshiptotal = $woocommerce->cart->subtotal;    
			}
			$tax_amount = ! empty( $woocommerce->cart->get_total_tax() ) ? $woocommerce->cart->get_total_tax() : 0;

			$orderitems = array();
			$totaldiscount = 0;
			$actualitemtotal = 0;
			$items = $woocommerce->cart->get_cart();
			if ( isset( $items ) && ! empty( $items ) && isset( $items ) ) {
				foreach ( $items as $key => $values ) {
					$discount_amount = 0;

					if ( isset( $values['variation_id'] ) && 0 !== $values['variation_id'] ) {
						$product = wc_get_product( $values['variation_id'] );    
					} else {

						$product = wc_get_product( $values['product_id'] );
					}
					$regular_price = $product->get_regular_price();
					$sale_price = $product->get_sale_price();
					if ( '' === $sale_price ) {
						$sale_price = $regular_price;
					} else {
						$discount_amount = $regular_price - $sale_price;
						$totaldiscount = $totaldiscount + ( $discount_amount * $values['quantity'] );
					}
					$product_desc = ! empty( $product->get_short_description() ) ? $product->get_short_description() : $product->get_title();

					$image_id  = $product->get_image_id();
					if ( isset( $image_id ) && ! empty( $image_id ) ) {

						$image_url = wp_get_attachment_image_url( $image_id, 'full' );
					} else {
						$image_url = wc_placeholder_img_src();
					}

					$data = '';
					$wcpa_values = '';
					if ( ! empty( $values['wcpa_data'] ) && array_key_exists( 'wcpa_data', $values ) ) {
						$display_data = '';
						foreach ( $values['wcpa_data'] as $key => $value ) {
							$label = $value['label'];
							if ( is_array( $value['value'] ) ) {
								foreach ( $value['value'] as $key => $vals ) {
									$val = $vals['label']; 
								}
								$data_value = $val;
							} else {
									  $data_value = $value['value'];
							}
							if ( ! empty( $data_value ) ) {
								$display_data .= $label . ' : ' . $data_value . ' | ';
								$data .= $label . ' [] ' . $data_value . ' [] ';
							} else {
								$display_data .= '';
								$data .= '';
							}
						}
						if ( ! empty( $display_data ) ) {
							$display_data = rtrim( $display_data, ' | ' );
; 
							$wcpa_values = ' | ' . $display_data;
						}
					}
					$terms = get_the_terms( $values['product_id'], 'product_cat' );
					$cat_name = '';
					
					if ( ! empty( $terms ) ) {
						foreach ( $terms as $product_cat_data ) {
							$cat_name = $product_cat_data->name;
						}
					}
					$img_urls = array();
					//Custom image upload for woo-addons-upload
					if ( isset( $values['wau_addon_ids'] ) && ! empty( $values['wau_addon_ids'] ) ) {
						$count = 1;
						foreach ( $values['wau_addon_ids'] as $img_data ) {
							$img_urls = array(
								'upload_image' . $count => array(
									'url' => $img_data['media_url'], 
									'id' => $img_data['media_id'], 
								),
							);
							$count++;
						}
					}
					$var_id = isset( $values['variation_id'] ) && ! empty( $values['variation_id'] ) ? $values['variation_id'] : '';
					$variantion_val = isset( $values['variation']['attribute_size'] ) && ! empty( $values['variation']['attribute_size'] ) ? '-' . $values['variation']['attribute_size'] : ''; 

					$orderitems[] = array(
						'productname' => $product->get_title() . $wcpa_values . $variantion_val,
						'sku' => $product->get_sku() . '|' . $var_id . '',
						'quantity' => $values['quantity'],
						'productdescription' => $product_desc,
						'unitprice' => $regular_price,
						'discountamount' => $discount_amount,
						'discountunitprice' => $discount_amount,
						'originalprice' => $regular_price,
						'actualprice' => $sale_price,
						'productimage' => $image_url,
						// 'variationid' => $var_id,
						'productid' => $values['product_id'],
						'varianttitle' => $cat_name,
						'merchantproductid' => $var_id,
						'customfield1'    => $data,
						'properties' => $img_urls,
					);
					$actualitemtotal = $actualitemtotal + ( $regular_price * $values['quantity'] );
				}
			}
			$shipping = array();
			$shipping_data = WC()->session->get( 'shipping_for_package_0' );
			

			if ( isset( $shipping_data ) && ! empty( $shipping_data ) && is_array( $shipping_data ) ) {
				foreach ( $shipping_data as $key => $data ) {
					if ( isset( $key ) && 'rates' === $key ) {
						if ( isset( $data ) && ! empty( $data ) && is_array( $data ) ) {
							foreach ( $data as $shippingkey => $shippingvalue ) {
								$shipping[] = array(
									'shippingcode' => $shippingvalue->get_method_id(),
									'shippingname' => $shippingvalue->get_label(),
									'shippingprice' => $shippingvalue->get_cost(), 
								); 
							}
						}
					}
				}
			}

			
			$mwb_cart_data = array(
				'merchantsuccessurl' => site_url() . '/wp-json/v1/mwb_xlane',
				'enablepolling' => 'true',
				'merchantcarturl' => wc_get_cart_url(),
				'merchantid' => $merchantid,
				'secretkey' => $secretkey,
				'merchantorderid' => $cart_hash_id,
				'orderdate' => $orderdate,
				'grandtotal' => $grandtotal,
				'preshiptotal' => $preshiptotal,
				'coupon_code' => $coupon_code,
				'cgst' => 0,
				'coupondiscount' => $discount,
				'discount' => $totaldiscount,
				'subtotal' => $subtotal,
				'total' => $actualitemtotal,
				'currency' => 'INR',
				'orderitems' => $orderitems,
			);

			if ( isset( $shipping ) && ! empty( $shipping ) && is_array( $shipping ) ) {
				$mwb_cart_data['shippingoptions'] = $shipping;
			}
			
			$mwb_cart_data = json_encode( $mwb_cart_data ); 

			$secretKey = substr( $secretkey, 0, 16 );
			include_once XPRESSLANE_DIRPATH . 'includes/class-xpresslane-aes-encrypt-decrypt.php';
			$mwb_encrypted_data = AesCipher::encrypt( $secretKey, $mwb_cart_data );
			$mwb_encrypted_data = $mwb_encrypted_data->getData();
			
			
			$button_custom_text = get_option( 'mwb-xlane-custom-text' );
			$button_logo_type    = get_option( 'mwb-xlane-logo-button' );


			$mwb_site_url_option = get_option( 'mwb-xlane-url-option' );
			if ( 'mwb_xlane_live_url_option' === $mwb_site_url_option ) {
				$mwb_cart_url = get_option( 'mwb-xlane-live-url' );
			} elseif ( 'mwb_xlane_stage_url_option' === $mwb_site_url_option ) {
				$mwb_cart_url = get_option( 'mwb-xlane-stage-url' );
			}
			$mwb_cart_page_text = get_option( 'mwb-xlane-text-cart' );
			$mwb_padding = get_option( 'mwb-xlane-btn-padding' );
			/*$mwb_btn_width = get_option('mwb-xlane-btn-width');*/
			$mwb_logo_img = get_option( 'mwb-xlane-img-logo' );
			$img = plugin_dir_url( __FILE__ ) . 'css/expresslane.png';
			$white_logo_class = '';
			if ( 'logo-black' === $mwb_logo_img ) {
				$white_logo_class = '';
				$img = plugin_dir_url( __FILE__ ) . 'css/expresslane.png';
			} elseif ( 'logo-white' === $mwb_logo_img ) {
				$white_logo_class = 'white-logo';
				$img = plugin_dir_url( __FILE__ ) . 'css/white_bg_logo.png';
			}
			$mwb_btn_radius = get_option( 'mwb-xlane-btn-radius' );
			$mwb_btn_width_minicart = get_option( 'mwb-xlane-btn-width-mini-cart' );
			$mwb_btn_height_minicart = get_option( 'mwb-xlane-btn-height-mini-cart' );
			?>
			<div class="mwb_xlane_payment_button xlane_minicart_button buttons">
				<form id="order" method="POST" action="<?php echo esc_url( $mwb_cart_url ); ?>">
					<input type="hidden" id="merchantid" name="merchantid" value="<?php echo esc_attr( $merchantid ); ?>" />
					<input type="hidden" id="checksum" name="checksum" value="<?php echo esc_attr( $mwb_encrypted_data ); ?>" />

					<!-- new button design -->
			<?php
			$mwb_minicart_padding_1 = get_option( 'mwb-xlane-btn-minicart-padding-1' );
			$mwb_minicart_padding_2 = get_option( 'mwb-xlane-btn-minicart-padding-2' );
			$mwb_minicart_padding_3 = get_option( 'mwb-xlane-btn-minicart-padding-3' );
			$mwb_minicart_padding_4 = get_option( 'mwb-xlane-btn-minicart-padding-4' );

					
			if ( isset( $mwb_cart_page_text ) && ! empty( $mwb_cart_page_text ) ) { 
				$this->mwb_xlane_display_button(
					'mwb_mini_cart_checkout_button button checkout wc-forward xlane-mini-cart',
					$button_logo_type,
					'mini-cart'
				);
				// $minicart_padding = '';
				// if( ($mwb_minicart_padding_1 && $mwb_minicart_padding_2 && $mwb_minicart_padding_3 && $mwb_minicart_padding_4 ) != '' ){
				//     $minicart_padding = 'padding:'.$mwb_minicart_padding_1.'px '.$mwb_minicart_padding_2.'px '.$mwb_minicart_padding_3.'px '.$mwb_minicart_padding_4.'px;';
				// }
						
			}
			?>
					<!-- <button type="submit" id="mwb_xpresslane_top_new" class="button checkout wc-forward" style="border-radius: 50px; ">Checkout on <img src="https://www.altprice.in/wp-content/plugins/xpresslane-integration-for-woocommerce/public/css/expresslane.png" class="mwb_xpresslane_cart_page_img"></button> -->

					<!-- <button type="submit" id="mwb_xpresslane_top"><img src="<?php //echo plugin_dir_url( __FILE__ ) . 'css/Xpresslane-button.png'; ?>"></button> -->
					
					<!-- end new button design -->
				</form>
			</div>
			<?php
		}
	}

	public function mwb_xlane_display_button( $button_type, 
		$type, 
		$page 
	) {

			

		$page_text = get_option( 'mwb-xlane-text-' . $page );
		$page_text_size = get_option( 'mwb-xlane-size-' . $page ); 
		$page_text_font = get_option( 'mwb-xlane-font-' . $page );
		$button_color = $this->mwb_xlane_intgn_button_back_color( 'mwb-xlane-' . $page . '-button-color' );
		$button_text_color = $this->mwb_xlane_intgn_button_text_color( 'mwb-xlane-' . $page . '-button-text-color' );
		$custom_text = get_option( 'mwb-xlane-custom-text' );
		$custom_size = get_option( 'mwb-xlane-custom-size', '10' );
		$custom_font = get_option( 'mwb-xlane-custom-font', 'Montserrat, sans-serif' );
		$custom_font_style = get_option( 'mwb-xlane-custom-style', 'normal' );                                        
		$mwb_btn_radius = get_option( 'mwb-xlane-' . $page . '-btn-radius' );
		
		$btn_width = get_option( 'mwb-xlane-btn-width-' . $page );
		$btn_height = get_option( 'mwb-xlane-btn-height-' . $page );
		$mwb_logo_img = get_option( 'mwb-xlane-img-logo' );
		$svg_mode = get_option( 'mwb-xlane-img-logo-below' );
		$nonce = wp_nonce_field( 'xplane_nonce', 'xplane-nonce' );
	   
		if ( 'logo-svg-black' === $svg_mode ) {
			$svg_img = plugin_dir_url( __FILE__ ) . 'assest/secured_by_xpresslane.svg';
		} elseif ( 'logo-svg-white' === $svg_mode ) {
			$svg_img = plugin_dir_url( __FILE__ ) . 'assest/xpresslane_logo-White-Secured-02.svg';
		}
		$display_val = plugin_dir_url( __FILE__ ) . 'css/Ajax-loader.svg';
		$img = plugin_dir_url( __FILE__ ) . 'css/expresslane.png';
		$white_logo_class = '';
		if ( 'logo-black' === $mwb_logo_img ) {
			$white_logo_class = '';
			$img = plugin_dir_url( __FILE__ ) . 'assest/xpresslane.png';
				
		} elseif ( 'logo-white' === $mwb_logo_img ) {
			$white_logo_class = 'white-logo';
			$img = plugin_dir_url( __FILE__ ) . 'assest/white_bg_logo.png';

		}
		$buttonType = $button_type . ' ' . $white_logo_class;
		
		?>
			<div class="mwb_cart_checkout_secure" style="text-align: center;">
			<?php wp_nonce_field( 'xplane_nonce', 'xplane-nonce' ); ?>
			<button type="submit" 
				id="mwb_xpresslane_top_new" 
				class="<?php echo ( esc_attr( $buttonType ) ); ?> mwb_xpresslane_desktop"   
				style="background-color : <?php echo( esc_attr( $button_color ) ); ?> !important ; border-radius: <?php echo ( esc_attr( $mwb_btn_radius ) ); ?>px !important;width: <?php echo ( esc_attr( $btn_width ) ); ?>px !important;height:<?php echo ( esc_attr( $btn_height ) ); ?>px !important; color: <?php echo ( esc_attr( $button_text_color ) ) ; ?>; font-family:<?php echo ( esc_attr( $page_text_font ) ); ?>; font-size: <?php echo ( esc_attr( $page_text_size ) ); ?>px;"><?php echo ( esc_attr( $page_text ) ); ?>
		<?php
		if ( 'logo-in' === $type ) {
			?>
				<img src="<?php echo ( esc_url( $img ) ); ?>" class="mwb_xpresslane_cart_page_img">
			<?php
		}
		?>
			</button>
			<button type="submit" 
				id="mwb_xpresslane_top_new" 
				class="<?php echo ( esc_attr( $buttonType ) ); ?> mwb_xpresslane_mobile"    
				style="background-color : <?php echo( esc_attr( $button_color ) ); ?> !important ; border-radius: <?php echo ( esc_attr( $mwb_btn_radius ) ); ?>px !important;height:<?php echo ( esc_attr( $btn_height ) ); ?>px !important; color: <?php echo ( esc_attr( $button_text_color ) ) ; ?>; font-family:<?php echo ( esc_attr( $page_text_font ) ); ?>; font-size: <?php echo ( esc_attr( $page_text_size ) ); ?>px; word-break: break-all;"><?php echo ( esc_attr( $page_text ) ); ?>
		<?php
		if ( 'logo-in' === $type ) {
			?>
				<img src="<?php echo ( esc_url( $img ) ); ?>" class="mwb_xpresslane_cart_page_img">
			<?php
		}
		?>
			</button>
		<?php
		if ( $custom_text ) {
			?>
						<br>
						<span style="word-break: break-all; font-family:<?php echo ( esc_attr( $custom_font ) ); ?>; font-size: <?php echo ( esc_attr( $custom_size ) ); ?>px; font-style: <?php echo( esc_attr( $custom_font_style ) ); ?>"> <?php echo( esc_attr( $custom_text ) ); ?> </span>
			<?php
		}
		?>
			
		<?php
		if ( 'logo-below' === $type ) {
			?>
						<div>
							<img class ="secure_image"src ="<?php echo ( esc_url( $svg_img ) ); ?>" style = "max-height:18px"></img>
						</div>
			<?php
		}    
		?>
				
			<div class="success_msg" style="display: none"><img src="<?php echo ( esc_url( $display_val ) ); ?>"></div>
			</div>
		<?php
		if ( function_exists( 'wcpa_init' ) ) { 
			?>
				<div id="mwb-wcpa-fields" style="display: none !important;"> </div>
		<?php 
		} 
		
		

	}
	public function mwb_xlane_insert_db_cart() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'xpresslane_orders';
		if ( ! ( isset( $_POST['wpnonce'] ) || wp_verify_nonce( sanitize_key( $_POST['wpnonce'] ), 'xplane_nonce' ) ) ) { // Input var okay.
			return false;
		}
		$cart_data = isset( $_POST['xlane_cart_data'] ) ? sanitize_text_field( wp_unslash( $_POST['xlane_cart_data'] ) ) : '';
		$secretkey = $this->mwb_xlane_intgn_merchant_key();
		$secretKey = substr( $secretkey, 0, 16 );
		include_once XPRESSLANE_DIRPATH . 'includes/class-xpresslane-aes-encrypt-decrypt.php';
		$mwb_encrypted_data = AesCipher::decrypt( $secretKey, $cart_data );
		$mwb_encrypted_data = $mwb_encrypted_data->getData();
		$cart_json_data = $mwb_encrypted_data;
		$mwb_encrypted_data = json_decode( $mwb_encrypted_data, true );
		if ( is_array( $mwb_encrypted_data ) && ! empty( $mwb_encrypted_data ) ) {
			update_option( $mwb_encrypted_data['merchantorderid'], $mwb_encrypted_data['merchantorderid'] );
			wp_die(
				json_encode(
					array(
						'status' => 200,
						'cart_data' => $mwb_encrypted_data,
					) 
				) 
			);
		}

		wp_die();
	}
	//FB Purchase Enet get order data
	public function mwb_xlane_get_order_data() {
		global $wpdb;
		if ( ! ( isset( $_POST['wpnonce'] ) || wp_verify_nonce( sanitize_key( $_POST['wpnonce'] ) ) ) ) { // Input var okay.
			return false;
		}
		$order_id = isset( $_POST['wc_order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wc_order_id'] ) ) : '';
		$fb_check = get_post_meta( $order_id, 'mwb_xlane_fb_purchase_event', true );    
		if ( ! $fb_check ) {
			$cart_data = get_post_meta( $order_id, 'mwb_xlane_trasaction_data' );
			update_post_meta( $order_id, 'mwb_xlane_fb_purchase_event', true );
			if ( $cart_data ) {
				wp_die(
					json_encode(
						array(
							'status' => 200,
							'cart_data' => $cart_data[0],
						) 
					) 
				);
			}
		}
		wp_die();
	}
	
}
