<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:         Xpresslane Integration For WooCommerce
 * Plugin URI:          http://www.xpresslane.in/
 * Description:         Super optimized and secure checkout for e-commerce, with user identity and experience unified across merchants                       from different verticals. With all user's checkout details saved in a single account, now merchants can provide a single click checkout even for their new users.
 * Version:              1.0.0
 * Author:               Bueno Labs Pvt. Ltd.
 * Author URI:             http://www.xpresslane.in/
 * WC requires at least: 2.6.0
 * WC tested up to:      6.9.0
 * License:                GPL-2.0+
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          xpresslane-integration-for-woocommerce
 * Domain Path:          /languages
 * @package          Xpresslane_Integration_For_Woocommerce
 * @subpackage       Xpresslane_Integration_For_Woocommerce/admin
 * @link             https://www.xpresslane.in
 * @since            1.0.0
 */



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$activated = true;

	// Check if WooCommerce is active.
require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * To activate the plugin.
 * 
 * @since 1.0.0
 */
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	$activated = false;
}
if ( $activated ) {
	define( 'XPRESSLANE_DIRPATH', plugin_dir_path( __FILE__ ) );
	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'XPRESSLANE_INTEGRATION_FOR_WOOCOMMERCE_VERSION', '1.0.0' );

	/**
	 * The code that runs during plugin activation This action is documented in includes/class-xpresslane-integration-for-woocommerce-activator.php.
	 *
	 * @return void
	 */
	function activate_xpresslane_integration_for_woocommerce() {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-xpresslane-integration-for-woocommerce-activator.php';
		Xpresslane_Integration_For_Woocommerce_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.This action is documented in includes/class-xpresslane-integration-for-woocommerce-deactivator.php
	 * 
	 * @return void
	 */
	function deactivate_xpresslane_integration_for_woocommerce() {
		include_once plugin_dir_path( __FILE__ ) . 'includes/class-xpresslane-integration-for-woocommerce-deactivator.php';
		Xpresslane_Integration_For_Woocommerce_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_xpresslane_integration_for_woocommerce' );
	register_deactivation_hook( __FILE__, 'deactivate_xpresslane_integration_for_woocommerce' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	include plugin_dir_path( __FILE__ ) . 'includes/class-xpresslane-integration-for-woocommerce.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	function run_xpresslane_integration_for_woocommerce() {

		$plugin = new Xpresslane_Integration_For_Woocommerce();
		$plugin->run();

	}
	run_xpresslane_integration_for_woocommerce();

	// function xpresslane_register_session()
	// {
	//     @ob_start();
	//     if(!session_id() )
	//     session_start();
	//     @ob_flush();
	// }
	// add_action('init', 'xpresslane_register_session');


	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mwb_xlane_intgn_add_settings_link' );

	// Settings link.
	function mwb_xlane_intgn_add_settings_link( $links ) {

		$setting_link = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=mwb-xlane-intgn' ) . '">' . __( 'Settings', 'xpresslane-integration-for-woocommerce' ) . '</a>',
		);
		return array_merge( $setting_link, $links );
	}
	add_action( 'plugins_loaded', 'xpresslane_gateway_class' );
	function xpresslane_gateway_class() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}
		class WC_Xpresslane extends WC_Payment_Gateway {
		
			public $domain;
			const DEFAULT_LABEL = 'Checkout on Xpresslane';
			protected $visibleSettings = array(
				'title',
				
			);
			public $form_fields = array();
			
			public function get_title_name( $title ) {
				$this->title = esc_html( 'Xpresslane', $this->domain );
				/**
				 * To apply title.
				 * 
				 * @since 1.0.0
				 */
				return apply_filters( 'Xpresslane_integration_title', $this->title );
			}
			 
			 

			/**
			 * Constructor for the gateway.
			 */
			public function __construct() {

				$this->domain = 'xpresslane_payment';

				$this->id = 'xpresslane';
				/**
				 * To apply xpresslane icon.
				 * 
				 * @since 1.0.0
				 */
				$this->icon               = apply_filters( 'woocommerce_xpresslane_gateway_icon', '' );
				$this->has_fields         = false;
				$this->method_title       = esc_html( 'Xpresslane', $this->domain );
				$this->method_description = esc_html( 'Allows payments with Xpresslane gateway.', $this->domain );
				$this->title              = $this->get_title_name( 'title' );

				// Load the settings.
				// $this->init_form_fields();

				
			}
			
			

			public function init_form_fields() {
				 

				$defaultFormFields = array(
					 
					'title' => array(
						'title'       => esc_html( 'Title', $this->id ),
						'type'        => 'text',
						'description' => esc_html( 'This controls the title which the user sees during checkout.', $this->id ),
						'default'     => esc_html( static::DEFAULT_LABEL, $this->id ),
					),
					 
				);

				foreach ( $defaultFormFields as $key => $value ) {
					if ( in_array( $key, $this->visibleSettings, true ) ) {
						$this->form_fields[ $key ] = $value;
					}
				}
			}
		}
		 /**
		  * Add the Gateway to WooCommerce
		  **/
		function woocommerce_add_expresslane_gateway( $methods ) {
			$methods[] = 'WC_Xpresslane';
			return $methods;
		}

		add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_expresslane_gateway' );
	}
	add_action( 'woocommerce_review_order_before_submit', 'select_payment_mode_as_defined_here' );
	function select_payment_mode_as_defined_here() {
		$val = '<script>jQuery("#payment_method_xpresslane").prop("checked", true);</script>';
		
		$allowed_html = [
			'a'      => [
				'href'  => [],
				'title' => [],
			],
			'br'     => [],
			'em'     => [],
			'strong' => [],
			'script' => [],
		];
		
		echo wp_kses( $val, $allowed_html );
	}
	
	add_action( 'woocommerce_admin_order_data_after_order_details', 'Xpresslane_order_meta_general_custom' );
	function Xpresslane_order_meta_general_custom( $order ) {
		?>
			<!--<br class="clear" />-->
			<!--<h4>Xpresslane Payment Detail</h4>-->
		<?php 
		/*
				 * get the meta data value
				 */
		$payment_mode = get_post_meta( $order->get_id(), 'payment_mode', true );
			 
		if ( '' === get_post_meta( $order->get_id(), 'xpresslane_response_data', true ) || null === get_post_meta( $order->get_id(), 'xpresslane_response_data', true ) ) {
			$xpresslaneData = 'data not updated';
		} else {
			$xpresslaneData = get_post_meta( $order->get_id(), 'xpresslane_response_data', true );
		}
				
				 
		?>
			<div class="address xpresslane-payment-mode">
				<p><strong>Xpresslane Payment Mode:</strong> <?php echo esc_attr( $payment_mode ); ?></p>
				
				<p style="display:none;"><strong>Xpresslane Response Data:</strong> <?php echo esc_attr( $xpresslaneData ); ?></p>
			</div>
	<?php 
	}


	// handle output of new admin settings type
	add_action( 'woocommerce_admin_field_radiowithimg', 'output_radiowithimg_fields' );
	function output_radiowithimg_fields( $value ) {

		$option_value = $value['value'];
		// Custom attribute handling.
		$custom_attributes = array();

		if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
			foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
				  $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		?>
		<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wc_help_tip( $value['desc_tip'] ); // WPCS: XSS ok. ?></label>
		</th>
		<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>"style="padding-top: 5px;" >
			<fieldset>
		<?php // echo $description; // WPCS: XSS ok. ?>
				<p class="description"><?php echo esc_attr( $value['desc'] ); // WPCS: XSS ok. ?></p>
				<ul style="display: flex;align-items: center;margin: 0;">
		<?php
		foreach ( $value['options'] as $key => $val ) {
			$custom_attr = implode( ' ', $custom_attributes )
			?>
					<li>
						<label><input
							name="<?php echo esc_attr( $value['id'] ); ?>"
							value="<?php echo esc_attr( $key ); ?>"
							type="radio"
							style="<?php echo esc_attr( $value['css'] ); ?>"
							class="<?php echo esc_attr( $value['class'] ); ?>"
			<?php echo ( esc_attr( $custom_attr ) ); // WPCS: XSS ok. ?>
			<?php checked( $key, $option_value ); ?>
							/> <?php echo  wp_kses_post( $val ); ?></label>
					</li>
			<?php
		}
		?>
				</ul>
			</fieldset>
		</td>
	</tr>
	<?php

	}

	// sanitize data for new settings type
	// add_filter('woocommerce_admin_settings_sanitize_option_radiowithimg', 'sanitize_radiowithimg_option', 10, 3);
	function sanitize_radiowithimg_option( $value, $option, $raw_value ) {
		$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
		return $value;
	}

	add_action( 'woocommerce_admin_field_numbers', 'output_numbers_fields' );
	function output_numbers_fields( $value ) {

		$option_value = $value['value'];
		// Custom attribute handling.
		$custom_attributes = array();

		if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
			foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
				  $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		?>
		<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wc_help_tip( $value['desc_tip'] ); // WPCS: XSS ok. ?></label>
		</th>
		<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>" style="display: flex;" >
			<input
				name="<?php echo esc_attr( $value['id'] ); ?>"
				id="<?php echo esc_attr( $value['id'] ); ?>"
				type="number"
				style="<?php echo esc_attr( $value['css'] ); ?>"
				value="<?php echo esc_attr( $option_value ); ?>"
				class="<?php echo esc_attr( $value['class'] ); ?>"
				placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
		<?php echo esc_attr( implode( ' ', $custom_attributes ) ); // WPCS: XSS ok. ?>
				/><?php echo esc_html( $value['suffix'] ); ?> <p class="description" style="margin-left: 8px;" ><?php echo esc_attr( $value['desc'] ); // WPCS: XSS ok. ?></p>
		</td>
	</tr>
	<?php

	}

	// sanitize data for new settings type
	add_filter( 'woocommerce_admin_settings_sanitize_option_numbers', 'sanitize_numbers_option', 10, 3 );
	function sanitize_numbers_option( $value, $option, $raw_value ) {
		$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
		return $value;
	}

	add_action( 'woocommerce_admin_field_padding', 'output_padding_fields' );
	function output_padding_fields( $value ) {

		if ( empty( $value['value'] ) ) {
			$option_value = '';
		} else {
			$option_value = $value['value'];
		}
		// Custom attribute handling.
		$custom_attributes = array();

		if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
			foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		?>
		</tbody>
	</table>
	<span class="mwb-admin-setting-padding-main <?php echo esc_html( $value['title'] ); ?>" >
		<?php if ( ! empty( $value['title'] ) ) { ?>
			<div class="mwb_padding_label_left">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</div>
		<?php } ?>
		<?php 
		if ( ! empty( $value['desc_tip'] ) ) {
			echo wc_help_tip( $value['desc_tip'] ); // WPCS: XSS ok. 
		} 
		?>
		<div class="mwb_padding_right forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
			<input
				name="<?php echo esc_attr( $value['id'] ); ?>"
				id="<?php echo esc_attr( $value['id'] ); ?>"
				type="number"
				style="<?php echo esc_attr( $value['css'] ); ?>"
				value="<?php echo esc_attr( $option_value ); ?>"
				step=".01"
				class="<?php echo esc_attr( $value['class'] ); ?>"
				placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
		<?php echo esc_attr( implode( ' ', $custom_attributes ) ); // WPCS: XSS ok. ?>
				/><?php echo esc_html( $value['suffix'] ); ?> <p class="description" style="margin-left: 8px;" ><?php echo esc_attr( $value['desc'] ); // WPCS: XSS ok. ?></p>
		</div>
	</span>
		<?php

	}

	// sanitize data for new settings type
	add_filter( 'woocommerce_admin_settings_sanitize_option_padding', 'sanitize_padding_option', 10, 3 );
	function sanitize_padding_option( $value, $option, $raw_value ) {
		$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
		return $value;
	}

	// THE AJAX ADD ACTIONS
	add_action( 'wp_ajax_set_form', 'set_form' );    //execute when wp logged in
	add_action( 'wp_ajax_nopriv_set_form', 'set_form' ); //execute when logged out

	function set_form() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'xpresslane_orders';
		if ( ! ( isset( $_POST['wpnonce'] ) || wp_verify_nonce( sanitize_key( $_POST['wpnonce'] ), 'xplane_nonce' ) ) ) { // Input var okay.
			return false;
		}
		$single_quantity = isset( $_POST['prod_quantity'] ) ? sanitize_text_field( $_POST['prod_quantity'] ) : '';
		$merchant_id = isset( $_POST['merchantid'] ) ? sanitize_text_field( $_POST['merchantid'] ) : '';
		$mwb_cart_data = isset( $_POST['checksum'] ) ? sanitize_text_field( $_POST['checksum'] ) : '';
		$mwb_cart_datas = isset( $_POST['mwb_cart_data'] ) ? sanitize_text_field( $_POST['mwb_cart_data'] ) : '';
		$mwb_cart_data_decode = ! empty( $mwb_cart_datas ) ? unserialize( base64_decode( $mwb_cart_datas ) ) : '';
		$mwb_cart_data_decode['total'] = 0;

		$mwb_cart_var_datas = isset( $_POST['product_variations'] ) ? sanitize_text_field( $_POST['product_variations'] ) : '';
		$mwb_variationID = isset( $_POST['mwb_variation_id'] ) ? sanitize_text_field( $_POST['mwb_variation_id'] ) : '';
		$mwb_variationName = isset( $_POST['attribute_pa_size'] ) ? sanitize_text_field( $_POST['attribute_pa_size'] ) : '';
		$mwb_variation_decode = unserialize( base64_decode( $mwb_cart_var_datas ) );
	
		if ( $mwb_variation_decode ) {
			foreach ( $mwb_variation_decode as $variation ) {
				if ( $variation['variation_id'] == $mwb_variationID ) {
					  $var_regular_price = $variation['regular_price']; //50
					  $var_display_price = $variation['sale_price']; //40
				}
			}
		}
		
		if ( ! empty( $mwb_variationID ) ) {
			$variation = wc_get_product( $mwb_variationID );
		   
			$mwb_cart_data_decode['grandtotal'] = $single_quantity * $var_display_price;
			$mwb_cart_data_decode['total'] = $mwb_cart_data_decode['total'] + ( $var_regular_price * $single_quantity );
			$mwb_cart_data_decode['orderitems']['0']['originalprice'] = $var_regular_price;
			$mwb_cart_data_decode['orderitems']['0']['unitprice'] = $var_regular_price;
			$mwb_cart_data_decode['orderitems']['0']['productname'] = $variation->get_title() . ' - ' . $mwb_variationName;
			$mwb_cart_data_decode['orderitems']['0']['sku'] = $mwb_variationName . '|' . $mwb_variationID;
			$mwb_cart_data_decode['orderitems']['0']['merchantproductid'] = $mwb_variationID;
			$mwb_cart_data_decode['orderitems']['0']['productid'] = $variation->get_parent_id();

			$image_id  = $variation->get_image_id();
			if ( isset( $image_id ) && ! empty( $image_id ) ) {
					
				$image_url = wp_get_attachment_image_url( $image_id, 'full' );
			} else {
				$image_url = wc_placeholder_img_src();
			}
			$mwb_cart_data_decode['orderitems']['0']['productimage'] = $image_url;

			$mwb_cart_data_decode['preshiptotal'] = $single_quantity * $var_display_price;
			$mwb_cart_data_decode['subtotal'] = $mwb_cart_data_decode['preshiptotal'];
			$mwb_cart_data_decode['orderitems']['0']['quantity'] = $single_quantity;
			$mwb_cart_data_decode['discount'] = 0;
			$mwb_cart_data_decode['orderitems']['0']['actualprice'] = $var_display_price;
			if ( '' === $mwb_cart_data_decode['orderitems']['0']['actualprice'] ) {
				$mwb_cart_data_decode['orderitems']['0']['actualprice'] = $var_regular_price;
			} else {
				$discount_amount = $mwb_cart_data_decode['orderitems']['0']['originalprice'] - $mwb_cart_data_decode['orderitems']['0']['actualprice'];
				$mwb_cart_data_decode['discount'] = $mwb_cart_data_decode['discount'] + ( $discount_amount * $single_quantity );
			}
			$mwb_cart_data_decode['orderitems']['0']['discountamount'] = $discount_amount;
			$mwb_cart_data_decode['orderitems']['0']['discountunitprice'] = $discount_amount;
		}
		if ( '' === $mwb_variationID || null === $mwb_variationID ) {
			
			$variation = wc_get_product( $mwb_variationID );
			$mwb_cart_data_decode['grandtotal'] = $single_quantity * $mwb_cart_data_decode['orderitems']['0']['actualprice'];
			$mwb_cart_data_decode['total'] = $mwb_cart_data_decode['total'] + ( $mwb_cart_data_decode['orderitems']['0']['originalprice'] * $single_quantity );
			$mwb_cart_data_decode['orderitems']['0']['originalprice'] = $mwb_cart_data_decode['orderitems']['0']['originalprice'];
			$mwb_cart_data_decode['orderitems']['0']['unitprice'] = $mwb_cart_data_decode['orderitems']['0']['unitprice'];
			$mwb_cart_data_decode['orderitems']['0']['productname'] = $mwb_cart_data_decode['orderitems']['0']['productname'];
			$mwb_cart_data_decode['orderitems']['0']['sku'] = $mwb_cart_data_decode['orderitems']['0']['sku'];

			$mwb_cart_data_decode['preshiptotal'] = $single_quantity * $mwb_cart_data_decode['orderitems']['0']['actualprice'];
			$mwb_cart_data_decode['subtotal'] = $mwb_cart_data_decode['preshiptotal'];
			$mwb_cart_data_decode['orderitems']['0']['quantity'] = $single_quantity;
			$mwb_cart_data_decode['discount'] = 0;
			if ( '' === $mwb_cart_data_decode['orderitems']['0']['actualprice'] ) {
				$mwb_cart_data_decode['orderitems']['0']['actualprice'] = $mwb_cart_data_decode['orderitems']['0']['originalprice'];
			} else {
				$discount_amount = $mwb_cart_data_decode['orderitems']['0']['originalprice'] - $mwb_cart_data_decode['orderitems']['0']['actualprice'];
				$mwb_cart_data_decode['discount'] = $mwb_cart_data_decode['discount'] + ( $discount_amount * $single_quantity );
			}
		}
		// for shipping
		if ( isset( $mwb_cart_data_decode['shippingoptions'] ) && strpos( $mwb_cart_data_decode['shippingoptions'][0]['shippingprice'], '*' ) ) {
			$mwb_cart_data_decode['shippingoptions'][0]['shippingprice'] = explode( '*', $mwb_cart_data_decode['shippingoptions'][0]['shippingprice'] );
			$mwb_cart_data_decode['shippingoptions'][0]['shippingprice'] = $mwb_cart_data_decode['shippingoptions'][0]['shippingprice'][0] * $single_quantity;
		}
		if ( false !== strpos( $mwb_cart_data_decode['shippingoptions'][1]['shippingprice'], '*' ) ) {
			$mwb_cart_data_decode['shippingoptions'][1]['shippingprice'] = explode( '*', $mwb_cart_data_decode['shippingoptions'][1]['shippingprice'] );
			$mwb_cart_data_decode['shippingoptions'][1]['shippingprice'] = $mwb_cart_data_decode['shippingoptions'][1]['shippingprice'][0] * $single_quantity;
		}
		if ( false !== strpos( $mwb_cart_data_decode['shippingoptions'][2]['shippingprice'], '*' ) ) {
			$mwb_cart_data_decode['shippingoptions'][2]['shippingprice'] = explode( '*', $mwb_cart_data_decode['shippingoptions'][2]['shippingprice'] );
			$mwb_cart_data_decode['shippingoptions'][2]['shippingprice'] = $mwb_cart_data_decode['shippingoptions'][2]['shippingprice'][0] * $single_quantity;
		}

		$wcpa_vals = isset( $_POST['wcpa_values'] ) ? sanitize_text_field( $_POST['wcpa_values'] ) : '';
		$wcpa_title_vals = isset( $_POST['wcpa_for_titles'] ) ? sanitize_text_field( $_POST['wcpa_for_titles'] ) : '';
		if ( ! empty( $wcpa_vals ) ) {
			$mwb_cart_data_decode['orderitems']['0']['customfield1'] = $wcpa_vals;
		}
		if ( ! empty( $wcpa_title_vals ) ) {
			$mwb_cart_data_decode['orderitems']['0']['productname'] = $mwb_cart_data_decode['orderitems']['0']['productname'] . $wcpa_title_vals;
		}
		 

		$mwb_cart_data_encode = json_encode( $mwb_cart_data_decode );
		update_option( $mwb_cart_data_decode['merchantorderid'], $mwb_cart_data_decode['merchantorderid'] );
		$call_test = get_option( $mwb_cart_data_decode['merchantorderid'], true );
		
		$log = wc_get_logger();
			   $head = '<-------------------Product PAGE------------------->/n';
			   $log_text = $head . print_r( (object) $call_test, true );
			   $context = array( 'source' => 'eh_XPDB_log' );
			   $log->log( 'debug', $log_text, $context );
		//         $sql = $wpdb->prepare("INSERT INTO ".$table_name." (merchant_order_id,cart_json,status) VALUES ( %s, %s, %s )", $mwb_cart_data_decode['merchantorderid'], $mwb_cart_data_encode, 'processing');
		//         $public_order_id = $wpdb->query($sql);
	
	
		$secretKey = substr( $mwb_cart_data_decode['secretkey'], 0, 16 );
		include_once XPRESSLANE_DIRPATH . 'includes/class-xpresslane-aes-encrypt-decrypt.php';
		$mwb_encrypted_data = AesCipher::encrypt( $secretKey, $mwb_cart_data_encode );
		$mwb_encrypted_data = $mwb_encrypted_data->getData();
		// echo base64_encode(serialize($mwb_cart_data_decode));
			wp_die(
				json_encode(
					array(
						'status' => 200,
						'cart_data' => $mwb_cart_data_encode,
						'encrypt_payload' => $mwb_encrypted_data,
					) 
				) 
			);
	}
} else {
	// WooCommerce is not active so deactivate this plugin.
	add_action( 'admin_init', 'mwb_xlane_intgn_actiavtion_fail' );

	// Deactivate this plugin.
	function mwb_xlane_intgn_actiavtion_fail() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'mwb_xlane_intgn_activation_failure_admin_notice' );

	// This function is used to display admin error notice when WooCommerce is not active.
	function mwb_xlane_intgn_activation_failure_admin_notice() {
		// to hide Plugin activated notice.
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to activate Xpresslane Integration For WooCommerce.', 'xpresslane-integration-for-woocommerce' ); ?></p>
		</div>

		<?php
	}
}

