<?php
/**
 * Plugin Name:       NextStagePayments Gateway
 * Plugin URI:        https://nextstagepayments.com/
 * Description:       WooCommerce payment gateway for Next Stage Payments. Stub integration - configure merchant credentials and API endpoint under WooCommerce > Settings > Payments > NextStagePayments.
 * Version:           0.1.0
 * Author:            Your Company
 * License:           GPL-2.0-or-later
 * Text Domain:       nextstagepayments
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 */

defined( 'ABSPATH' ) || exit;

define( 'NSP_PLUGIN_FILE', __FILE__ );
define( 'NSP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Register the gateway once WooCommerce is available.
 */
function nsp_init_gateway() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	require_once NSP_PLUGIN_PATH . 'includes/class-nsp-api.php';
	require_once NSP_PLUGIN_PATH . 'includes/class-wc-gateway-nextstagepayments.php';

	add_filter( 'woocommerce_payment_gateways', 'nsp_register_gateway' );
}
add_action( 'plugins_loaded', 'nsp_init_gateway' );

/**
 * Add the gateway to WooCommerce's list of payment gateways.
 *
 * @param array $gateways Existing gateways.
 * @return array
 */
function nsp_register_gateway( $gateways ) {
	$gateways[] = 'WC_Gateway_NextStagePayments';
	return $gateways;
}
