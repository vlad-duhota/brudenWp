<?php

namespace PaymentPlugins\WooCommerce\PPCP\Admin\Settings;

class AdvancedSettings extends AbstractSettings {

	public $id = 'ppcp_advanced';

	protected $tab_label_priority = 20;

	public function __construct( ...$args ) {
		$this->tab_label = __( 'Advanced Settings', 'pymntpl-paypal-woocommerce' );
		parent::__construct( ...$args );
	}

	public function init_form_fields() {
		$this->form_fields = [
			'title'             => [
				'type'  => 'title',
				'title' => __( 'Advanced Settings', 'pymntpl-paypal-woocommerce' ),
			],
			'display_name'      => [
				'title'       => __( 'Display Name', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'text',
				'default'     => get_option( 'blogname' ),
				'desc_tip'    => true,
				'description' => __( 'This is the business name that is displayed in the PayPal popup.', 'pymntpl-paypal-woocommerce' ),
			],
			'order_prefix'      => [
				'title'       => __( 'Order Prefix', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'text',
				'default'     => '',
				'desc_tip'    => true,
				'description' => __( 'If you\'re using the same PayPal account on multiple sites we recommend adding an order prefix to prevent invoice duplicates in PayPal.', 'pymntpl-paypal-woocommerce' )
			]
			,
			'refund_cancel'     => array(
				'title'       => __( 'Refund On Cancel', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'If enabled, the plugin will process a payment cancellation or refund within PayPal when the order\'s status is set to cancelled.',
					'pymntpl-paypal-woocommerce' )
			),
			'capture_status'    => [
				'title'       => __( 'Capture Status', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'completed',
				'options'     => [
					'completed'  => __( 'Completed', 'pymntpl-paypal-woocommerce' ),
					'processing' => __( 'Processing', 'pymntpl-paypal-woocommerce' ),
					'manual'     => __( 'Manual', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'    => true,
				'description' => __( 'For orders that are authorized, when the order is set to this status, it will trigger a capture. When set to manual, the payment must be manually captured.', 'pymntpl-paypal-woocommerce' ),
			],
			'cart_location'     => [
				'title'       => __( 'Cart Button Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'below',
				'options'     => [
					'below' => __( 'Below checkout button', 'pymntpl-paypal-woocommerce' ),
					'above' => __( 'Above checkout button', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'    => true,
				'description' => __( 'The location of the payment buttons in relation to the Proceed to checkout button.', 'pymntpl-paypal-woocommerce' )
			],
			'minicart_location' => [
				'title'       => __( 'Mini-Cart Button Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'below',
				'options'     => [
					'below' => __( 'Below checkout button', 'pymntpl-paypal-woocommerce' ),
					'above' => __( 'Above checkout button', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'    => true,
				'description' => __( 'The location of the payment buttons in relation to the mini-cart checkout button.', 'pymntpl-paypal-woocommerce' )
			]
		];
	}

	public function is_refund_on_cancel() {
		return wc_string_to_bool( $this->get_option( 'refund_cancel', 'no' ) );
	}

	public function is_capture_on_complete() {
		return $this->get_option( 'capture_status', 'completed' ) === 'completed';
	}

	public function is_capture_on_processing() {
		return $this->get_option( 'capture_status', 'completed' ) === 'processing';
	}

	public function is_manual_capture() {
		return $this->get_option( 'capture_status', 'completed' ) === 'manual';
	}

	public function get_frontend_script_data() {
		return [
			'miniCartLocation' => $this->get_option( 'minicart_location' )
		];
	}

}