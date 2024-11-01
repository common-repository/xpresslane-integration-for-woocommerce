<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  https://xpresslane.in
 * @since 1.0.0
 *
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Xpresslane_Integration_For_Woocommerce
 * @subpackage Xpresslane_Integration_For_Woocommerce/includes
 */
class Xpresslane_Integration_For_Woocommerce {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0.0
	 * @var    Xpresslane_Integration_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( defined( 'XPRESSLANE_INTEGRATION_FOR_WOOCOMMERCE_VERSION' ) ) {
			$this->version = XPRESSLANE_INTEGRATION_FOR_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'xpresslane-integration-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Xpresslane_Integration_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - XpresslaneIntegrationForWoocommercei18n. Defines internationalization functionality.
	 * - Xpresslane_Integration_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Xpresslane_Integration_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xpresslane-integration-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xpresslane-integration-for-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-xpresslane-integration-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-xpresslane-integration-for-woocommerce-public.php';

		$this->loader = new Xpresslane_Integration_For_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the XpresslaneIntegrationForWoocommercei18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new XpresslaneIntegrationForWoocommercei18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Xpresslane_Integration_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->id = 'mwb-xlane-intgn';
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueueStyles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueueScripts' );
		$this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin, 'mwbXlaneIntgnWooSettingsTabsOption', 50 );
		$this->loader->add_action( 'woocommerce_settings_tabs_' . $this->id, $plugin_admin, 'mwbXlaneIntgnSettingsTab' );
		$this->loader->add_action( 'woocommerce_settings_save_' . $this->id, $plugin_admin, 'mwbXlaneIntgnSettingSave' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mwbXpresslaneMenu', 50 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 */
	private function define_public_hooks() {

		$mwb_xlane_plugin_enable = get_option( 'mwb-xlane-plugin-enable', false );
		$plugin_public = new Xpresslane_Integration_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );
		

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueueStyles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueueScripts' );
		
		if ( isset( $mwb_xlane_plugin_enable ) && 'yes' == $mwb_xlane_plugin_enable ) {

			$this->loader->add_action( 'woocommerce_after_add_to_cart_button', $plugin_public, 'mwb_xlane_intgn_nonce_form', 1 );

			$this->loader->add_action( 'woocommerce_after_cart_totals', $plugin_public, 'mwb_xlane_intgn_add_payment_form' );
		
			$mwb_xlane_enable = get_option( 'mwb-xlane-prod-page', false );

			if ( isset( $mwb_xlane_enable ) && 'yes' == $mwb_xlane_enable ) {
				 $this->loader->add_action( 'woocommerce_after_add_to_cart_form', $plugin_public, 'mwb_xlane_intgn_add_payment_form', 9 );
			}
			$this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_public, 'mwb_xlane_custom_shop_order_column', 10 );
			$this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_public, 'mwb_xlane_custom_orders_list_column_content', 10, 2 );
			$this->loader->add_action( 'rest_api_init', $plugin_public, 'mwb_xlane_intgn_endpoint_url' );
			$this->loader->add_action( 'init', $plugin_public, 'mwb_remove_default_checkout_button' );

			$this->loader->add_action( 'woocommerce_after_mini_cart', $plugin_public, 'display_mini_xpresslane_button', 20 );

			$this->loader->add_action( 'wp_head', $plugin_public, 'mwb_prevent_checkout_page_access' );
			$this->loader->add_action( 'wp_ajax_mwb_xlane_insert_db_cart', $plugin_public, 'mwb_xlane_insert_db_cart' );
			$this->loader->add_action( 'wp_ajax_nopriv_mwb_xlane_insert_db_cart', $plugin_public, 'mwb_xlane_insert_db_cart' );
			$this->loader->add_action( 'wp_ajax_mwb_xlane_get_order_data', $plugin_public, 'mwb_xlane_get_order_data' );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Xpresslane_Integration_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
