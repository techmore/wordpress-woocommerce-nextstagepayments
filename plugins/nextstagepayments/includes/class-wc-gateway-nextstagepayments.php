<?php
/**
 * NextStagePayments WooCommerce payment gateway (stub).
 */

defined( 'ABSPATH' ) || exit;

class WC_Gateway_NextStagePayments extends WC_Payment_Gateway {

	/** @var bool */
	public $test_mode;

	/** @var string */
	public $merchant_id;

	/** @var string */
	public $api_key;

	/** @var string */
	public $api_endpoint;

	public function __construct() {
		$this->id                 = 'nextstagepayments';
		$this->has_fields         = false;
		$this->method_title       = __( 'NextStagePayments', 'nextstagepayments' );
		$this->method_description = __( 'Accept card payments via Next Stage Payments.', 'nextstagepayments' );

		$this->supports = array( 'products' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title        = $this->get_option( 'title' );
		$this->description  = $this->get_option( 'description' );
		$this->test_mode    = 'yes' === $this->get_option( 'test_mode' );
		$this->merchant_id  = $this->get_option( 'merchant_id' );
		$this->api_key      = $this->get_option( 'api_key' );
		$this->api_endpoint = $this->get_option( 'api_endpoint' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'      => array(
				'title'   => __( 'Enable/Disable', 'nextstagepayments' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable NextStagePayments', 'nextstagepayments' ),
				'default' => 'no',
			),
			'title'        => array(
				'title'       => __( 'Title', 'nextstagepayments' ),
				'type'        => 'text',
				'description' => __( 'Title shown to the customer at checkout.', 'nextstagepayments' ),
				'default'     => __( 'Credit Card', 'nextstagepayments' ),
				'desc_tip'    => true,
			),
			'description'  => array(
				'title'       => __( 'Description', 'nextstagepayments' ),
				'type'        => 'textarea',
				'description' => __( 'Description shown to the customer at checkout.', 'nextstagepayments' ),
				'default'     => __( 'Pay securely with your credit or debit card.', 'nextstagepayments' ),
			),
			'test_mode'    => array(
				'title'       => __( 'Test mode', 'nextstagepayments' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable test mode', 'nextstagepayments' ),
				'description' => __( 'Approve transactions without contacting the payment gateway.', 'nextstagepayments' ),
				'default'     => 'yes',
			),
			'merchant_id'  => array(
				'title' => __( 'Merchant ID', 'nextstagepayments' ),
				'type'  => 'text',
			),
			'api_key'      => array(
				'title' => __( 'API Key', 'nextstagepayments' ),
				'type'  => 'password',
			),
			'api_endpoint' => array(
				'title'       => __( 'API Endpoint', 'nextstagepayments' ),
				'type'        => 'url',
				'description' => __( 'Leave empty to use the built-in test/live NextStage endpoint.', 'nextstagepayments' ),
				'default'     => '',
			),
		);
	}

	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		$api    = new NSP_API( $this->merchant_id, $this->api_key, $this->api_endpoint, $this->test_mode );
		$result = $api->authorize( $order );

		if ( is_wp_error( $result ) ) {
			wc_add_notice( $result->get_error_message(), 'error' );
			return array( 'result' => 'failure' );
		}

		if ( ! empty( $result->transaction_id ) ) {
			$order->set_transaction_id( $result->transaction_id );
		}

		$order->add_order_note(
			sprintf(
				// translators: 1: transaction id, 2: gateway title.
				__( '%2$s charge approved (transaction %1$s).', 'nextstagepayments' ),
				! empty( $result->transaction_id ) ? $result->transaction_id : 'n/a',
				$this->method_title
			)
		);

		$order->update_status( 'processing' );
		$order->reduce_order_stock();

		WC()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
