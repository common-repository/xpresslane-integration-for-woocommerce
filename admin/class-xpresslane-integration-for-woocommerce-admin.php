<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/admin
 * @link       https://www.xpresslane.com
 * @since      1.0.0
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/admin
 * @link       https://www.xpresslane.com
 * @since      1.0.0
 */
class Xpresslane_Integration_For_Woocommerce_Admin {


	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $_plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $version    The current version of this plugin.
	 */
	private $_version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $_plugin_name The name of this plugin.
	 * @param string $_version     The version of this plugin.
	 * 
	 * @since 1.0.0
	 */
	public function __construct( $_plugin_name, $_version ) {

		$this->plugin_name = $_plugin_name;
		$this->version = $_version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param $hooks string 
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function enqueueStyles( $hooks ) {

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

		$current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';

		if ( 'woocommerce_page_wc-settings' != $hooks && ( ! empty( $current_tab ) && 'mwb-xlane-intgn' !== $current_tab ) ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/xpresslane-integration-for-woocommerce-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 * 
	 * @param $hooks string 
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function enqueueScripts( $hooks ) {

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
		// Enqueue styles only on this plugin's menu page.

		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';

		if ( 'woocommerce_page_wc-settings' !== $hooks && ( isset( $_GET['tab'] ) && 'mwb-xlane-intgn' !== $tab ) ) {
			return;
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/xpresslane-integration-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'pluginDir',
			array(
				'pluginDirUrl' => plugin_dir_url( __FILE__ ),
			)
		);
		
	}

	/**
	 * Adding settings menu for Xpresslane in Woocommerce Settings Page.
	 *
	 * @param array $settings_tabs all settings tabs.
	 * 
	 * @name mwbXlaneIntgnWooSettingsTabsOption
	 * 
	 * @since 1.0.0
	 * 
	 * @return array of settings.
	 */
	public function mwbXlaneIntgnWooSettingsTabsOption( $settings_tabs ) {
		$settings_tabs['mwb-xlane-intgn'] = __( 'Xpresslane', 'mwb_xpresslane_integ' );
		return $settings_tabs;
	}

	/**
	 * Display the html of each setting page.
	 *
	 * @name mwbXlaneIntgnSettingsTab
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function mwbXlaneIntgnSettingsTab() {
		global $current_section;

		woocommerce_admin_fields( self::mwbXlaneIntgnGetSettings( $current_section ) );
	}

	/**
	 * Display the html of each sections using Setting API.
	 *
	 * @param array  $current_section array of the display sections.
	 * @param string $section         array of the display sections.
	 * 
	 * @name mwbXlaneIntgnGetSettings
	 * 
	 * @since 1.0.0
	 * 
	 * @return array settings
	 */
	public function mwbXlaneIntgnGetSettings( $current_section, $section = true ) {
		global $settings;
		$settings = array();
		if ( '' === $current_section ) {
			$logo_black_img = plugin_dir_url( __FILE__ ) . 'logo/xpresslane_button-black-bg.png';
			$logo_white_img = plugin_dir_url( __FILE__ ) . 'logo/xpresslane_button-white-bg.png';
			$secure_bl_logo = plugin_dir_url( __FILE__ ) . 'logo/secured_by_xpresslane.svg';
			$secure_wh_logo = plugin_dir_url( __FILE__ ) . 'logo/xpresslane_logo-White-Secured-02.svg';
			
			$secure_black_logo = "<img class = 'mwb-xlane-img-logo' src='" . $secure_bl_logo . "' style='background-color:white; width: 70%; vertical-align :middle;'>";
			$secure_white_logo = "<img class = 'mwb-xlane-img-logo' src='" . $secure_wh_logo . "' style='width: 42%; background-color:black; vertical-align :middle;'>";
			$logo_black = "<img class = 'mwb-xlane-img-logo' src='" . $logo_black_img . "' style='width: 70%;
    vertical-align: middle;'>";
			$white_black = "<img class = 'mwb-xlane-img-logo' src='" . $logo_white_img . "' style='width: 70%;
    vertical-align: middle;'>";
			if ( $section ) {

				  $current_tab = ( ! empty( $_GET['subtab'] ) ) ? sanitize_text_field( $_GET['subtab'] ) : 'general';

				$tabs = array(
					'general' => __( 'General', 'xpresslane-integration-for-woocommerce' ),
					'design' => __( 'Design', 'xpresslane-integration-for-woocommerce' ),
				
				);
				$html = '  <div class="wrap">
					<h1 class="nav-tab-wrapper">';

				foreach ( $tabs as $stab => $name ) {
					$class = ( $stab == $current_tab ) ? 'nav-tab-active' : '';
					$style = ( $stab == $current_tab ) ? 'border-bottom: 1px solid transparent !important;' : '';
					$html .= '<a style="text-decoration:none !important;' . $style . '" class="nav-tab ' . $class . ' tab_' . $stab . '" >' . $name . '</a>';
				}
				$html .= '</h1><br>';

				

				echo wp_kses_post( $html );
				$sub = '<ul class = "subsubsub" id = "mwb-xlane-section-design" >
						<li ><a class = "xlm" id="xlm-logo" >  <label> Logo Pattern</label>  </a></li> |
						<li> <a class = "xlm" id ="xlm-product"><label> Product Button</label>  </a></li> |
						<li> <a class = "xlm" id ="xlm-cart"> <label>  Cart Button </label>  </a> </li> |
						<li> <a class = "xlm" id ="xlm-mini-cart">  <label>  Mini-Cart Button </label>  </a> </li> |
					 </ul>
					 <br>';

				echo wp_kses_post( $sub );
			}
			$settings = array(
				array(
					'title' => __( 'Settings ', 'xpresslane-integration-for-woocommerce' ),
					'type'  => 'title',
					'id'    => 'generals_option',
					'class' => 'wc_settings_tab_demo_section_title',
				),
				
				array(
					'title'   => __( 'Enable/Disable ', 'mwb_xpresslane_integ' ),
					'desc'    => __( 'Enable/Disable Xpresslane Checkout', 'xpresslane-integration-for-woocommerce' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'id'      => 'mwb-xlane-plugin-enable',
					'class'   => 'mwb-xlane-general-settings',
				), 
				array(
					'title'   => __( 'Hide Checkout Button On cart Page', 'xpresslane-integration-for-woocommerce' ),
					'desc'    => __( 'Hide default proceed to checkout button on cart page', 'xpresslane-integration-for-woocommerce' ),
					'default' => 'no',
					'type'    => 'checkbox',
					'class'      => 'mwb-xlane-option-enable mwb-xlane-general-settings',
					'id'      => 'mwb-xlane-checkout-feature',
				),
				array(
					'title'    => __( 'Merchant key', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'password',
					'default'  => '',
					'id'       => 'mwb-xlane-merchant-key',
					'class'    => 'mwb-xlane-general-settings mwb_xlane_intgn_class mwb-xlane-option-enable',
					'desc_tip' => __( 'Enter the merchant key.', 'xpresslane-integration-for-woocommerce' ),
					'placeholder' => __( 'Enter the merchant key.', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Merchant id', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => '',
					'id'       => 'mwb-xlane-merchant-id',
					'class'    => 'mwb-xlane-general-settings mwb_xlane_intgn_class mwb-xlane-option-enable',
					'desc_tip' => __( 'Enter the merchant id.', 'xpresslane-integration-for-woocommerce' ),
					'placeholder' => __( 'Enter the merchant id.', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'    => __( 'COD Order', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'select',
					'default'  => '',
					'options'   => wc_get_order_statuses(), 
					'id'       => 'mwb-xlane-order-status',
					'class'    => 'mwb-xlane-general-settings mwb_xlane_intgn_class mwb-xlane-option-enable',
					'desc_tip' => __( 'Set COD order as per need .', 'xpresslane-integration-for-woocommerce' ),
					'placeholder' => __( 'Enter the merchant id.', 'xpresslane-integration-for-woocommerce' ),
				),
				
		   
				array(
					'title'    => __( 'Select Xpresslane checkout url', 'xpresslane-integration-for-woocommerce' ),
					'default'  => 1,
					'type'     => 'select',
					'id'       => 'mwb-xlane-url-option',
					'class'    => 'mwb-xlane-general-settings mwb_xlane_intgn_class mwb-xlane-option-enable',
					'css'      => 'width:25%',
					'options'  => array(
						'mwb_xlane_stage_url_option'   => __( 'Staging Checkout Url', 'xpresslane-integration-for-woocommerce' ),
						'mwb_xlane_live_url_option' => __( 'Live Checkout Url', 'xpresslane-integration-for-woocommerce' ),
					),
					'desc_tip' => __( 'Choose staging site url or live site url for Xpresslane checkout', 'xpresslane-integration-for-woocommerce' ),
					'desc'     => __( 'Select checkout url option for Xpresslane. ', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Staging checkout url', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => '',
					'id'       => 'mwb-xlane-stage-url',
					'class'    => 'mwb-xlane-general-settings mwb_xlane_intgn_class mwb-xlane-option-enable',
					'desc_tip' => __( 'Enter the staging site checkout url for checkout.', 'xpresslane-integration-for-woocommerce' ),
					'placeholder' => __( 'Enter the staging site checkout url for checkout.', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Live checkout url', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => '',
					'id'       => 'mwb-xlane-live-url',
					'class'    => 'mwb-xlane-general-settings mwb_xlane_intgn_class mwb-xlane-option-enable',
					'desc_tip' => __( 'Enter the live site checkout url for checkout.', 'xpresslane-integration-for-woocommerce' ),
					'placeholder' => __( 'Enter the live site checkout url for checkout.', 'xpresslane-integration-for-woocommerce' ),
				),

				/*array(
				'title' => __( 'Settings ', 'xpresslane-integration-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'setting_options',
				),*/
			


				

				array(
					'title'   => __( 'Enable/Disable on Product Page ', 'xpresslane-integration-for-woocommerce' ),
					'desc'    => __( 'Enable/Disable Xpresslane Checkout on Product Page', 'xpresslane-integration-for-woocommerce' ),
					'default' => 'no',
					'class'    => 'mwb-xlane-product-button mwb-xlane-design mwb_xlane_prod_class mwb-xlane-option-enable',
					'type'    => 'checkbox',
					'id'      => 'mwb-xlane-prod-page',
				),
				// array(
				//     'title'    => __( 'Product page', 'xpresslane-integration-for-woocommerce' ),
				//     'type'     => 'title',
				//     'default'  => 'Checkout on',
				//     'id'       => 'mwb-xlane-text-product',
				//     'class'      => 'mwb-xlane-design  mwb-xlane-option-enable',
				//     'desc_tip' => __( 'This text will shown on Product Page', 'xpresslane-integration-for-woocommerce' ),
				// ),

				array(
					'title'    => __( 'Text on Product page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => 'Checkout on',
					'id'       => 'mwb-xlane-text-product',
					'class'      => 'mwb-xlane-product-button mwb-xlane-design  mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Product Page', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'    => __( 'Text Size on Product page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'number',
					'css'        => 'width: 70px;',
					'default'  => '10',
					'id'       => 'mwb-xlane-size-product',
					'class'      => 'mwb-xlane-product-button mwb-xlane-design  mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Product Page', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Text-Font on Product page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => 'Montserrat, sans-serif',
					'id'       => 'mwb-xlane-font-prod',
					'class'      => 'mwb-xlane-product-button mwb-xlane-design  mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Product Page', 'xpresslane-integration-for-woocommerce' ),
				),


				array(
					'title'    => __( 'Text on Cart Page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => 'Checkout on',
					'id'       => 'mwb-xlane-text-cart',
					'class'      => 'mwb-xlane-cart-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Cart Page', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'    => __( 'Text-Font on Cart Page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => 'Montserrat, sans-serif',
					'id'       => 'mwb-xlane-font-cart',
					'class'      => 'mwb-xlane-cart-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Cart Page', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'    => __( 'Text Size on Cart Page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'number',
					'css'        => 'width: 70px;',
					'default'  => '10',
					'id'       => 'mwb-xlane-size-cart',
					'class'      => 'mwb-xlane-cart-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Cart Page', 'xpresslane-integration-for-woocommerce' ),
				),


				array(
					'title'    => __( 'Text on Mini-Cart Page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					
					'default'  => 'Checkout on',
					'id'       => 'mwb-xlane-text-mini-cart',
					'class'      => 'mwb-xlane-mini-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Cart Page', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'    => __( 'Text Font on Mini-Cart Page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => 'Montserrat, sans-serif',
					'id'       => 'mwb-xlane-font-mini-cart',
					'class'      => 'mwb-xlane-mini-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Cart Page', 'xpresslane-integration-for-woocommerce' ),
				),


				array(
					'title'    => __( 'Text Size on Mini-Cart Page', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'number',
					'css'        => 'width: 70px;',
					'default'  => '10',
					'id'       => 'mwb-xlane-size-mini-cart',
					'class'      => 'mwb-xlane-mini-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip' => __( 'This text will shown on Cart Page', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'   => __( 'Enable/Disable Logo Button ', 'xpresslane-integration-for-woocommerce' ),
					'default' => 'logo-in',
					'class'    => ' mwb-xlane-design mwb_xlane_logo_button_class mwb-xlane-option-enable mwb-xlane-logo',
					'type'    => 'radio',
					'options'  => array(
						'logo-in'        => 'Logo In Button',
						'logo-below'    => 'Logo Below Button',
					),
					'id'      => 'mwb-xlane-logo-button',
				),
				array(
					'title'    => __( 'Logo', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'radiowithimg',
					'default'  => 'logo-black',
					'options'  => array(
						'logo-black'    => $logo_black,
						'logo-white'    => $white_black,
					),
					'id'       => 'mwb-xlane-img-logo',
					'class'      => 'mwb-xlane-design mwb-xlane-logo mwb-xlane-option-enable mwb-xlane-img-logo',
					'desc_tip' => __( 'This logo shows on button', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Logo', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'radiowithimg',
					'default'  => 'logo-svg-black',
					'options'  => array(
						'logo-svg-black'    => $secure_black_logo,
						'logo-svg-white'    => $secure_white_logo,
					),
					'id'       => 'mwb-xlane-img-logo-below',
					'class'      => 'mwb-xlane-design mwb-xlane-logo-below mwb-xlane-option-enable mwb-xlane-img-logo-below',
					'desc_tip' => __( 'This logo shows on button', 'xpresslane-integration-for-woocommerce' ),
				),
				//Product page color
				array(
					'title'    => __( 'Button Color', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'color',
					'default'  => '#000000',
					'id'       => 'mwb-xlane-product-button-color',
					'class'      => 'mwb-xlane-design mwb-xlane-product-button mwb-xlane-option-enable',
					'desc_tip' => __( 'Choosen color will be use in your checkout button', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Text Color', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'color',
					'default'  => '#FFFFFF',
					'id'       => 'mwb-xlane-product-button-text-color',
					'class'      => 'mwb-xlane-design mwb-xlane-product-button mwb-xlane-option-enable',
					'desc_tip' => __( 'Choosen text color will be use in your checkout button', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'    => __( 'Custom Text', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => '',
					'id'       => 'mwb-xlane-custom-text',
					'class'      => 'mwb-xlane-general-settings mwb-xlane-option-enable ',
					'desc_tip' => __( 'Text will be use in under the xpresslane checkout button', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Custom Text Font', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'text',
					'default'  => 'Montserrat, sans-serif',
					'id'       => 'mwb-xlane-custom-font',
					'class'      => 'mwb-xlane-general-settings mwb-xlane-option-enable',
					'desc_tip' => __( 'Text will be use in under the xpresslane checkout button', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Custom Text Size', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'number',
					'default'  => '0',
					'min'      => '0',
					'id'       => 'mwb-xlane-custom-size',
					'class'      => 'mwb-xlane-general-settings mwb-xlane-option-enable',
					'desc_tip' => __( 'Text will be use in under the xpresslane checkout button', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'    => __( 'Custom Text Style', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'select',
					'default'  => '',
					'options'  => array(
						'normal'    => 'Normal',
						'italic'    => 'Italic',
						'bold'        => 'Bold',
					),
					'id'       => 'mwb-xlane-custom-style',
					'class'      => 'mwb-xlane-general-settings mwb-xlane-option-enable',
					'desc_tip' => __( 'Text will be use in under the xpresslane checkout button', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'        => __( 'Button Border Radius', 'xpresslane-integration-for-woocommerce' ),
					'type'        => 'numbers',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-product-btn-radius',
					'default'    => '0',
					'desc'        => 'px',
					'class'          => 'mwb-xlane-design mwb-xlane-option-enable mwb-xlane-product-button',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				//Cart button color
				array(
					'title'    => __( 'Button Color', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'color',
					'default'  => '#000000',
					'id'       => 'mwb-xlane-cart-button-color',
					'class'      => 'mwb-xlane-design mwb-xlane-cart-button mwb-xlane-option-enable',
					'desc_tip' => __( 'Choosen color will be use in your checkout button', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Text Color', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'color',
					'default'  => '#FFFFFF',
					'id'       => 'mwb-xlane-cart-button-text-color',
					'class'      => ' mwb-xlane-design mwb-xlane-cart-button mwb-xlane-option-enable',
					'desc_tip' => __( 'Choosen text color will be use in your checkout button', 'xpresslane-integration-for-woocommerce' ),
				),

				array(
					'title'        => __( 'Button Border Radius', 'xpresslane-integration-for-woocommerce' ),
					'type'        => 'numbers',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-cart-btn-radius',
					'default'    => '0',
					'desc'        => 'px',
					'class'          => 'mwb-xlane-design mwb-xlane-option-enable mwb-xlane-cart-button',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				//Mini Cart Button Color
				
				array(
					'title'    => __( 'Button Color', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'color',
					'default'  => '#000000',
					'id'       => 'mwb-xlane-mini-cart-button-color',
					'class'      => 'mwb-xlane-design mwb-xlane-mini-button mwb-xlane-option-enable',
					'desc_tip' => __( 'Choosen color will be use in your checkout button', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'    => __( 'Text Color', 'xpresslane-integration-for-woocommerce' ),
					'type'     => 'color',
					'default'  => '#FFFFFF',
					'id'       => 'mwb-xlane-mini-cart-button-text-color',
					'class'      => 'mwb-xlane-design mwb-xlane-mini-button mwb-xlane-option-enable',
					'desc_tip' => __( 'Choosen text color will be use in your checkout button', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'        => __( 'Button Border Radius', 'xpresslane-integration-for-woocommerce' ),
					'type'        => 'numbers',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-mini-cart-btn-radius',
					'default'    => '0',
					'desc'        => 'px',
					'class'          => 'mwb-xlane-design mwb-xlane-option-enable mwb-xlane-mini-button',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				//Other Settings
				array(
					'title'        => __( 'Single Product', 'xpresslane-integration-for-woocommerce' ),
					'type'        => 'padding',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-btn-width-product',
					'default'    => '270',
					'desc'        => 'width (px)',
					'class'         => 'mwb-xlane-product-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'type'        => 'padding',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-btn-height-product',
					'default'    => '38',
					'desc'        => 'height (px)',
					'class'        => 'mwb-xlane-product-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'        => __( 'Cart Page', 'xpresslane-integration-for-woocommerce' ),
					'type'        => 'padding',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-btn-width-cart',
					'default'    => '310',
					'desc'        => 'width (px)',
					'class'        => 'mwb-xlane-cart-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					
					'type'        => 'padding',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-btn-height-cart',
					'default'    => '38',
					'desc'        => 'height (px)',
					'class'        => 'mwb-xlane-cart-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					'title'        => __( 'Mini Cart Page', 'xpresslane-integration-for-woocommerce' ),
					'type'        => 'padding',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-btn-width-mini-cart',
					'default'    => '262',
					'desc'        => 'width (px)',
					'class'      => 'mwb-xlane-mini-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				array(
					
					'type'        => 'padding',
					'css'        => 'width: 70px;',
					'id'        => 'mwb-xlane-btn-height-mini-cart',
					'default'    => '38',
					'desc'        => 'height (px)',
					'class'      => 'mwb-xlane-mini-button mwb-xlane-design mwb-xlane-option-enable',
					'desc_tip'    => __( 'Add only number', 'xpresslane-integration-for-woocommerce' ),
				),
				// array(
				//     'title'        => __('Button Padding for Single Product','xpresslane-integration-for-woocommerce'),
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-padding-1',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Top)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-padding-2',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Right)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-padding-3',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Bottom)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-padding-4',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Left)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),
				// array(
				//     'title'        => __('Button Padding for Cart Page','xpresslane-integration-for-woocommerce'),
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-cart-padding-1',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Top)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-cart-padding-2',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Right)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-cart-padding-3',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Bottom)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-cart-padding-4',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Left)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),
				// array(
				//     'title'        => __('Button Padding for Mini Cart Page','xpresslane-integration-for-woocommerce'),
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-minicart-padding-1',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Top)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-minicart-padding-2',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Right)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-minicart-padding-3',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Bottom)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),array(
				//     'title'        => '',
				//     'type'        => 'padding',
				//     'css'        => 'width: 100px;',
				//     'id'        =>    'mwb-xlane-btn-minicart-padding-4',
				//     'class'          => 'mwb-xlane-option-enable',
				//     'desc'        => 'px (Left)',
				//     'desc_tip'    => __('configure button padding','xpresslane-integration-for-woocommerce')
				// ),
				array(
					'type' => 'sectionend',
					'id'   => 'general_options',
					'class' => 'mwb-xlane-general-settings',
				),
			);
		}
		/**
		 * To get Xpresslane Settings.
		 * 
		 * @since 1.0.0
		 */
		return apply_filters( 'mwb_xlane_intgn_settings', $settings );
	}

	/**
	 * Save the data using Setting API
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function mwbXlaneIntgnSettingSave() {
		global $current_section;
		$settings = $this->mwbXlaneIntgnGetSettings( $current_section, $section = false );
		woocommerce_update_options( $settings );
	}
	/**
	 * Creating the data using Setting Menu
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function mwbXpresslaneMenu() {
		add_menu_page(
			__( 'Xpresslane', 'xpresslane-integration-for-woocommerce' ),
			__( 'Xpresslane', 'xpresslane-integration-for-woocommerce' ),
			'manage_options',
			'wc-settings&tab=mwb-xlane-intgn',
			array( $this, 'mwb_xpresslane_integration' ),
			'',
			55.567
		);
	}

}
