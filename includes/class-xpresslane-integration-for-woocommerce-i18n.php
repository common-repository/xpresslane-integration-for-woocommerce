<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link  https://xpresslane.in
 * @since 1.0.0
 *
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/includes
 */
class XpresslaneIntegrationForWoocommercei18n {



	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'xpresslane-integration-for-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
