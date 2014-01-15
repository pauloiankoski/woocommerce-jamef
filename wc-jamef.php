<?php
/**
 * Plugin Name: WooCommerce Jamef
 * Plugin URI: https://github.com/pauloiankoski/woocommerce-jamef
 * Description: Jamef para WooCommerce
 * Author: pauloiankoski
 * Author URI: http://paulor.com.br/
 * Version: 1.0
 * License: GPLv2 or later
 * Text Domain: wcjamef
 * Domain Path: /languages/
 */

define( 'WOO_JAMEF_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_JAMEF_URL', plugin_dir_url( __FILE__ ) );

/**
 * WooCommerce fallback notice.
 */
function wcjamef_woocommerce_fallback_notice() {
	echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Jamef depends on %s to work!', 'wcjamef' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>';
}

/**
 * SimpleXML missing notice.
 */
function wcjamef_extensions_missing_notice() {
	echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Jamef depends to %s to work!', 'wcjamef' ), '<a href="http://php.net/manual/en/book.simplexml.php">SimpleXML</a>' ) . '</p></div>';
}

/**
 * Load functions.
 */
function wcjamef_shipping_load() {

	if ( ! class_exists( 'WC_Shipping_Method' ) ) {
		add_action( 'admin_notices', 'wcjamef_woocommerce_fallback_notice' );

		return;
	}

	if ( ! class_exists( 'SimpleXmlElement' ) ) {
		add_action( 'admin_notices', 'wcjamef_extensions_missing_notice' );

		return;
	}

	/**
	 * Load textdomain.
	 */
	load_plugin_textdomain( 'wcjamef', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/**
	 * Add the Jamef to shipping methods.
	 *
	 * @param array $methods
	 *
	 * @return array
	 */
	function wcjamef_add_method( $methods ) {
		$methods[] = 'WC_Jamef';

		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'wcjamef_add_method' );

	// WC_Jamef class.
	include_once WOO_JAMEF_PATH . 'includes/class-wc-jamef.php';

	// Metabox.
	//include_once WOO_JAMEF_PATH . 'includes/class-wc-jamef-tracking.php';
	//$wc_jamef_metabox = new WC_Jamef_Tracking;
}

add_action( 'plugins_loaded', 'wcjamef_shipping_load', 0 );
