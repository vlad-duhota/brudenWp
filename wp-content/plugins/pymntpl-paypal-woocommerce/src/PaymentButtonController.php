<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\ProductSettings;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;

class PaymentButtonController {

	private $payment_gateways;

	private $template_loader;

	private $assets_data;

	private $cart_priority = 30;

	private $cart_location;

	private $minicart_location;

	private $render_cart_buttons = true;

	private $render_product_buttons = true;

	private $render_express_buttons = true;

	public function __construct( PaymentGateways $payment_gateways, AssetDataApi $assets_data, TemplateLoader $template_loader ) {
		$this->payment_gateways = $payment_gateways;
		$this->assets_data      = $assets_data;
		$this->template_loader  = $template_loader;
	}

	public function initialize() {
		$this->init_dependencies();
		add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'add_product_action' ] );
		add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'get_order_review_fragments' ] );
		add_action( 'woocommerce_checkout_before_customer_details', [ $this, 'render_express_buttons' ] );
		if ( $this->minicart_location === 'above' ) {
			add_action( 'woocommerce_widget_shopping_cart_buttons', [ $this, 'render_minicart_buttons' ], 5 );
		} else {
			add_action( 'woocommerce_widget_shopping_cart_buttons', [ $this, 'render_minicart_buttons' ], 30 );
		}
	}

	public function init_dependencies() {
		$this->cart_priority = $this->cart_location === 'below' ? 30 : 10;
		$this->cart_priority = apply_filters( 'wc_ppcp_cart_payment_buttons_priority', $this->cart_priority );
		add_action( 'woocommerce_proceed_to_checkout', [ $this, 'render_cart_buttons' ], $this->cart_priority );
	}

	public function add_product_action() {
		global $product;
		$action = 'woocommerce_after_add_to_cart_button';
		if ( 'top' == $product->get_meta( '_ppcp_button_position' ) ) {
			$action = 'woocommerce_before_add_to_cart_button';
		}
		add_action( $action, [ $this, 'render_product_buttons' ] );
	}

	public function render_cart_buttons() {
		$payment_methods = $this->payment_gateways->get_cart_payment_gateways();
		if ( is_ajax() ) {
			$data = apply_filters( 'wc_ppcp_cart_data', Utils::get_cart_data( WC()->cart ) );
			$this->assets_data->print_data( 'wcPPCPCartData', $data );
		}
		if ( $this->render_cart_buttons && count( $payment_methods ) > 0 ) {
			$this->template_loader->load_template( 'cart/payment-methods.php', [
				'payment_methods'   => $payment_methods,
				'below_add_to_cart' => $this->cart_location === 'below'
			] );
		}
	}

	public function render_product_buttons() {
		global $product;
		$payment_methods = $this->payment_gateways->get_product_payment_gateways();
		if ( $product ) {
			$this->assets_data->add( 'product', Utils::get_product_data( $product ) );
		}
		if ( $this->render_product_buttons && count( $payment_methods ) > 0 ) {
			$this->template_loader->load_template( 'product/payment-methods.php', [
				'payment_methods' => $payment_methods
			] );
		}
	}

	public function get_order_review_fragments( $fragments ) {
		if ( isset( $fragments['.woocommerce-checkout-payment'] ) ) {
			ob_start();
			$data = apply_filters( 'wc_ppcp_cart_data', Utils::get_cart_data( WC()->cart ) );
			$this->assets_data->print_data( 'wcPPCPCartData', $data );
			$data                                       = ob_get_clean();
			$fragments['.woocommerce-checkout-payment'] .= $data;
		}
		$express = $this->render_express_buttons( true );
		if ( $express ) {
			$fragments['.wc-ppcp-express-checkout'] = $express;
		}

		return $fragments;
	}

	public function render_express_buttons( $get_template = false ) {
		$payment_methods    = $this->payment_gateways->get_express_payment_gateways();
		$available_gateways = \array_keys( WC()->payment_gateways()->get_available_payment_gateways() );
		$available_gateways = array_filter( $payment_methods, function ( $payment_method ) use ( $available_gateways ) {
			return \in_array( $payment_method->id, $available_gateways, true );
		} );
		if ( $this->render_express_buttons && ! empty( $payment_methods ) ) {
			if ( $get_template ) {
				return $this->template_loader->load_template_html( 'checkout/express-checkout.php', [
					'payment_methods'    => $payment_methods,
					'available_gateways' => $available_gateways
				] );
			} else {
				$this->template_loader->load_template( 'checkout/express-checkout.php', [
					'payment_methods'    => $payment_methods,
					'available_gateways' => $available_gateways
				] );
			}
		}
	}

	public function render_minicart_buttons() {
		$payment_methods = $this->payment_gateways->get_minicart_payment_gateways();
		if ( ! empty( $payment_methods ) ) {
			if ( is_ajax() ) {
				$this->assets_data->print_data( 'wcPPCPMiniCartUpdate', Utils::get_cart_data( WC()->cart ) );
			}
			$this->template_loader->load_template( 'minicart/payment-methods.php', [
				'payment_methods' => $payment_methods
			] );
		}
	}

	public function set_render_cart_buttons( $bool ) {
		$this->render_cart_buttons = $bool;
	}

	public function set_render_product_buttons( $bool ) {
		$this->render_product_buttons = $bool;
	}

	public function set_render_express_buttons( $bool ) {
		$this->render_express_buttons = $bool;
	}

	public function set_cart_location( $location ) {
		$this->cart_location = $location;
	}

	public function set_minicart_location( $location ) {
		$this->minicart_location = $location;
	}

}