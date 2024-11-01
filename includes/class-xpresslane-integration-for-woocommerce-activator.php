<?php

/**
 * Fired during plugin activation
 *
 * @link  https://xpresslane.in
 * @since 1.0.0
 *
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/includes
 */
class Xpresslane_Integration_For_Woocommerce_Activator {


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE `{$wpdb->base_prefix}xpresslane_orders` (
		  public_order_id int NOT NULL AUTO_INCREMENT,
		  merchant_order_id varchar(255) NOT NULL,
		  woo_order_id varchar(255) NOT NULL,
		  cart_json varchar(255) NOT NULL,
		  payload_data varchar(255) NOT NULL,
		  webhook_order varchar(50) NULL,
		  status varchar(255) NOT NULL,
		  PRIMARY KEY  (public_order_id)
		) $charset_collate;";

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

}
