<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\PayPalSDK\PurchaseUnit;
use PaymentPlugins\PayPalSDK\ShippingAddress;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;
use PaymentPlugins\WooCommerce\PPCP\Utilities\OrderLock;
use PaymentPlugins\WooCommerce\PPCP\Utilities\PayPalFee;

class PaymentHandler {

	public $client;

	private $factories;

	/**
	 * @var AbstractGateway
	 */
	protected $payment_method;

	private $current_status = [];

	private $use_billing_agreement = false;

	public function __construct( WPPayPalClient $client, CoreFactories $factories ) {
		$this->client    = $client;
		$this->factories = $factories;
	}

	public function set_payment_method( AbstractGateway $payment_method ) {
		$this->payment_method = $payment_method;
	}

	public function set_use_billing_agreement( bool $bool ) {
		$this->use_billing_agreement = $bool;
	}

	public function process_payment( \WC_Order $order ) {
		$this->set_processing( 'payment' );
		$this->factories->initialize( $order );
		try {
			if ( $this->use_billing_agreement ) {
				$paypal_order = $this->client->orderMode( $order )->orders->create(
					apply_filters( 'wc_ppcp_create_order_params', $this->get_create_order_params( $order ), $order, $this )
				);
			} else {
				$paypal_order_id = $this->get_paypal_order_id_from_request();
				$paypal_order    = $this->client->orderMode( $order )->orders->retrieve( $paypal_order_id );
			}
			if ( is_wp_error( $paypal_order ) ) {
				throw new \Exception( $paypal_order->get_error_message() );
			}
			if ( ! $this->use_billing_agreement ) {
				// update the order, so it has the most recent order data.
				$response = $this->client->orders->update( $paypal_order->id, $this->get_update_order_params( $order, $paypal_order ) );
				if ( is_wp_error( $response ) ) {
					throw new \Exception( $response->get_error_message() );
				}
			}
			if ( Order::CAPTURE == $paypal_order->intent ) {
				OrderLock::set_order_lock( $order );
				$response = $this->client->orders->capture( $paypal_order->id, $this->get_payment_source( $order ) );
			} else {
				$response = $this->client->orders->authorize( $paypal_order->id, $this->get_payment_source( $order ) );
			}
			$result = new PaymentResult( $response, $order, $this->payment_method );

			if ( $result->success() ) {
				$this->payment_complete( $order, $result );
			} else {
				$order->update_status( 'failed' );
				$order->add_order_note( sprintf( __( 'Error processing payment. Reason: %s', 'pymntpl-paypal-woocommerce' ),
					$result->get_error_message() ) );
			}
			OrderLock::release_order_lock( $order );

			return $result;
		} catch ( \Exception $e ) {
			return new PaymentResult( false, $order, $this->payment_method, $e->getMessage() );
		}
	}

	public function get_payment_source( \WC_Order $order ) {
		if ( $this->use_billing_agreement ) {
			$this->factories->initialize( $order );

			return [ 'payment_source' => $this->factories->paymentSource->from_order() ];
		}

		return [];
	}

	/**
	 * @param \WC_Order     $order
	 * @param PaymentResult $result
	 */
	public function payment_complete( \WC_Order $order, PaymentResult $result ) {
		$paypal_order = $result->paypal_order;
		if ( $result->is_captured() ) {
			PayPalFee::add_fee_to_order( $order, $result->get_capture()->seller_receivable_breakdown, false );
			$order->payment_complete( $result->get_capture_id() );
		} else {
			$order->update_meta_data( Constants::AUTHORIZATION_ID, $result->get_authorization_id() );
			$order->set_status( apply_filters( 'wc_ppcp_authorized_order_status', $this->payment_method->get_option( 'authorize_status', 'on-hold' ), $order, $paypal_order, $this ) );
		}
		$this->add_payment_complete_message( $order, $result );
		$this->save_order_meta_data( $order, $paypal_order );
		do_action( 'wc_ppcp_order_payment_complete', $order, $result, $this );
	}

	public function add_payment_complete_message( \WC_Order $order, PaymentResult $result ) {
		$order->add_order_note( sprintf( __( 'PayPal order %s created. %s', 'pymntpl-paypal-woocommerce' ),
			$result->paypal_order->id, $result->is_captured() ? sprintf( __( 'Capture ID: %s', 'pymntpl-paypal-woocommerce' ), $result->get_capture_id() ) : sprintf( __( 'Authorization ID: %s', 'pymntpl-paypal-woocommerce' ), $result->get_authorization_id() ) ) );
	}

	public function save_order_meta_data( \WC_Order $order, Order $paypal_order ) {
		$token = $this->get_payment_method_token_from_paypal_order( $paypal_order );
		$order->set_payment_method_title( $token->get_payment_method_title() );
		$order->update_meta_data( Constants::ORDER_ID, $paypal_order->id );
		$order->update_meta_data( Constants::PPCP_ENVIRONMENT, $this->client->getEnvironment() );
		$order->update_meta_data( Constants::PAYER_ID, $paypal_order->payer->payer_id );
		try {
			do_action( 'wc_ppcp_save_order_meta_data', $order, $paypal_order, $this->payment_method );
		} catch ( \Exception $e ) {
			$this->payment_method->logger->info( sprintf( 'Error saving order data. Error: %s', $e->getMessage() ) );
		}
		$order->save();
	}

	public function get_paypal_order_id_from_request() {
		return isset( $_POST[ $this->payment_method->id . '_paypal_order_id' ] ) ? sanitize_text_field( $_POST[ $this->payment_method->id . '_paypal_order_id' ] ) : null;
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \PaymentPlugins\PayPalSDK\Order
	 */
	public function get_create_order_params( \WC_Order $order ) {
		$this->factories->initialize( $order );
		$paypal_order = $this->factories->order->from_order( $this->payment_method->get_option( 'intent' ) );
		/**
		 * @var PurchaseUnit $purchase_unit
		 */
		$purchase_unit = $paypal_order->getPurchaseUnits()->get( 0 );
		if ( ! $purchase_unit->getAmount()->amountEqualsBreakdown() ) {
			unset( $purchase_unit->getAmount()->breakdown );
			unset( $purchase_unit->items );
		}

		return $paypal_order;
	}

	protected function get_update_order_params( \WC_Order $order, Order $paypal_order ) {
		$this->factories->initialize( $order );
		$patches = [];
		$pu      = $this->factories->purchaseUnit->from_order();
		/**
		 * @var PurchaseUnit $purchase_unit
		 */
		foreach ( $paypal_order->purchase_units as $purchase_unit ) {
			$purchase_unit->patch();
			// update the purchase unit using the values from the factory created purchase unit
			$purchase_unit->setInvoiceId( $pu->getInvoiceId() )
			              ->setCustomId( $pu->getCustomId() )
			              ->setDescription( $pu->getDescription() );


			if ( isset( $purchase_unit->shipping, $pu->shipping ) ) {
				$purchase_unit->getShipping()
				              ->setAddress( $pu->getShipping()->getAddress() )
				              ->setName( $pu->getShipping()->getName() );

				$purchase_unit->addPatchRequest( 'shipping/address' )
				              ->addPatchRequest( 'shipping/name' );
			}
			$purchase_unit->addPatchRequest( 'invoice_id' )
			              ->addPatchRequest( 'custom_id' )
			              ->addPatchRequest( 'description' );

			$patches = array_merge( $patches, $purchase_unit->getPatchRequests() );
		}

		/**
		 * @param array                                           $patches
		 * @param \WC_Order                                       $order
		 * @param Order                                           $paypal_order
		 * @param \PaymentPlugins\WooCommerce\PPCP\PaymentHandler $this
		 */
		return apply_filters( 'wc_ppcp_get_update_order_params', $patches, $order, $paypal_order, $this );
	}

	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * @param \WC_Order $order
	 * @param           $amount
	 * @param string    $reason
	 */
	public function process_refund( \WC_Order $order, $amount, $reason = '' ) {
		// make sure the order can be refunded.
		$paypal_order = $this->client->orderMode( $order )->orders->retrieve( $order->get_meta( Constants::ORDER_ID ) );
		if ( is_wp_error( $paypal_order ) ) {
			return $paypal_order;
		}

		$id = $order->get_transaction_id();
		if ( empty( $id ) ) {
			// transaction is empty so check if there is an authorization ID.
			$auth_id = $order->get_meta( Constants::AUTHORIZATION_ID );
			if ( ! $auth_id ) {
				throw new \Exception( __( 'To process a refund, there must be a transaction id associated with the order.',
					'pymntpl-paypal-woocommerce' ) );
			} else {
				throw new \Exception( __( 'This payment has a status of Authorize. Only captured payments can be refunded.',
					'pymntpl-paypal-woocommerce' ) );
			}
		}
		$refunds = $order->get_refunds();
		usort( $refunds, function ( $a, $b ) {
			return $a < $b ? 1 : - 1;
		} );

		// get latest refund
		$settings = Main::container()->get( AdvancedSettings::class );

		return $this->client->orderMode( $order )->captures->refund( $id, [
			'amount'        => [
				'value'         => NumberUtil::round_incl_currency( $amount, $order->get_currency() ),
				'currency_code' => $order->get_currency()
			],
			'invoice_id'    => trim( $settings->get_option( 'order_prefix' ) . $refunds[0]->get_id() ),
			'note_to_payer' => ! $reason ? null : $reason
		] );
	}

	public function process_capture( \WC_Order $order, $amount = '' ) {
		$auth_id = $order->get_meta( Constants::AUTHORIZATION_ID );
		$result  = false;
		if ( $auth_id ) {
			$authorization = $this->client->orderMode( $order )->authorizations->retrieve( $auth_id );
			if ( ! is_wp_error( $authorization ) && ! $authorization->isCaptured() ) {
				OrderLock::set_order_lock( $order );
				$amount = $amount ? $amount : $order->get_total();
				$result = $this->client->orderMode( $order )->authorizations->capture( $auth_id, [
					'amount' => [
						'value'         => NumberUtil::round_incl_currency( $amount, $order->get_currency() ),
						'currency_code' => $order->get_currency()
					]
				] );
				if ( is_wp_error( $result ) ) {
					OrderLock::release_order_lock( $order );
					$order->add_order_note( sprintf( __( 'Error capturing payment. Reason: %s', 'pymntpl-paypal-woocommerce' ),
						$result->get_error_message() ) );
				} else {
					PayPalFee::add_fee_to_order( $order, $result->seller_receivable_breakdown );
					$order->add_order_note( sprintf( __( 'Payment captured in PayPal. Capture ID: %s Amount: %s', 'pymntpl-paypal-woocommerce' ),
						$result->id,
						wc_price( $amount, [ 'currency' => $order->get_currency() ] ) ) );
					$order->set_transaction_id( $result->id );
					if ( ! $this->is_processing( 'capture' ) ) {
						// set status to on hold so that when $order->payment_complete() is called, it
						// passes the $this->has_status() check.
						$order->set_status( 'on-hold' );
						$this->set_processing( 'capture' );
						$order->payment_complete();
					}
					$order->save();
				}
			}
		}

		return $result;
	}

	/**
	 * Void an authorized payment
	 *
	 * @param \WC_Order $order
	 * @param bool      $manual
	 */
	public function process_void( \WC_Order $order, $manual = false ) {
		try {
			$authorization_id = $order->get_meta( Constants::AUTHORIZATION_ID );
			if ( ! $authorization_id ) {
				if ( ! $manual ) {
					return;
				}
				throw new \Exception( __( 'A valid authorization ID is required to perform a void.', 'pymntpl-paypal-woocommerce' ) );
			}
			// fetch the authorization object and verify that it can be voided.
			$authorization = $this->client->orderMode( $order )->authorizations->retrieve( $authorization_id );
			if ( is_wp_error( $authorization ) ) {
				throw new \Exception( $authorization->get_error_message() );
			}
			if ( $authorization->isCreated() ) {
				$result = $this->client->orderMode( $order )->authorizations->void( $authorization_id );
				if ( is_wp_error( $result ) ) {
					throw new \Exception( $authorization->get_error_message() );
				}
				if ( ! $order->has_status( 'cancelled' ) ) {
					$this->set_processing( 'void' );
					$order->update_status( 'cancelled', __( 'Order cancelled via a void.', 'pymntpl-paypal-woocommerce' ) );
				}
			}
		} catch ( \Exception $e ) {
			$order->add_order_note( sprintf( __( 'Error processing void. Reason: %s' ), $e->getMessage() ) );

			return new \WP_Error( 'void-error', $e->getMessage() );
		}

		return true;
	}

	public function set_processing( $status ) {
		$this->current_status = $status;
	}

	public function remove_processing() {
		$this->current_status = null;
	}

	public function is_processing( $status ) {
		if ( \is_array( $status ) ) {
			return in_array( $this->current_status, $status );
		}

		return $this->current_status === $status;
	}

	protected function get_payment_method_token_from_paypal_order( Order $order ) {
		$token = $this->payment_method->get_payment_method_token_instance();
		$token->initialize_from_payer( $order->payer );

		return $token;
	}

}