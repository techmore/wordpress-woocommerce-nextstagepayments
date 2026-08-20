<?php
/**
 * NextStagePayments API client.
 *
 * STUB: Replace the placeholder logic with the real NextStage Payments API.
 * Developer docs: https://nextstagepayments.com/developers/
 */

defined( 'ABSPATH' ) || exit;

class NSP_API {

	const TEST_ENDPOINT = 'https://sandbox.nextstagepayments.com/api/v1/charges';
	const LIVE_ENDPOINT = 'https://api.nextstagepayments.com/api/v1/charges';

	/** @var string */
	private $merchant_id;

	/** @var string */
	private $api_key;

	/** @var string */
	private $api_endpoint;

	/** @var bool */
	private $test_mode;

	/**
	 * @param string $merchant_id   Merchant identifier issued by NextStage.
	 * @param string $api_key       API key issued by NextStage.
	 * @param string $api_endpoint  Override endpoint, or empty for built-in.
	 * @param bool   $test_mode     Approve transactions without hitting the gateway.
	 */
	public function __construct( $merchant_id, $api_key, $api_endpoint, $test_mode ) {
		$this->merchant_id  = $merchant_id;
		$this->api_key      = $api_key;
		$this->test_mode    = $test_mode;
		$this->api_endpoint = $api_endpoint ? $api_endpoint : ( $test_mode ? self::TEST_ENDPOINT : self::LIVE_ENDPOINT );
	}

	/**
	 * Authorize a charge for the given order.
	 *
	 * @param WC_Order $order Order to charge.
	 * @return object|WP_Error Stub success object, or WP_Error on failure.
	 */
	public function authorize( $order ) {
		if ( $this->test_mode ) {
			// STUB: fake a successful transaction in test mode.
			return (object) array(
				'success'        => true,
				'transaction_id' => 'TEST-' . time() . '-' . $order->get_id(),
				'message'        => __( 'Test transaction approved.', 'nextstagepayments' ),
			);
		}

		// TODO: Implement the real request once NextStage credentials/endpoint
		// are available. Suggested shape:
		//
		// $payload = array(
		//   'merchant_id'   => $this->merchant_id,
		//   'amount'        => $order->get_total(),
		//   'currency'      => $order->get_currency(),
		//   'order_id'      => (string) $order->get_id(),
		//   'card_token'    => $order->get_meta( '_nsp_card_token' ),
		//   'billing_name'  => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
		//   'billing_email' => $order->get_billing_email(),
		// );
		//
		// $response = wp_remote_post(
		//   $this->api_endpoint,
		//   array(
		//     'headers' => array(
		//       'Content-Type'  => 'application/json',
		//       'Authorization' => 'Bearer ' . $this->api_key,
		//     ),
		//     'body'    => wp_json_encode( $payload ),
		//     'timeout' => 30,
		//   )
		// );
		//
		// if ( is_wp_error( $response ) ) {
		//   return $response;
		// }
		//
		// $body = json_decode( wp_remote_retrieve_body( $response ) );
		// if ( isset( $body->approved ) && $body->approved ) {
		//   return (object) array(
		//     'success'        => true,
		//     'transaction_id' => isset( $body->transaction_id ) ? $body->transaction_id : '',
		//   );
		// }
		//
		// return new WP_Error(
		//   'nsp_declined',
		//   isset( $body->message ) ? $body->message : __( 'Transaction declined.', 'nextstagepayments' )
		// );

		return new WP_Error(
			'nsp_not_configured',
			__( 'NextStagePayments live gateway is not configured yet.', 'nextstagepayments' )
		);
	}
}
