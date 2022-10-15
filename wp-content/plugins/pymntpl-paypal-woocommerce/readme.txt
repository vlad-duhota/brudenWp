=== Payment Plugins for PayPal WooCommerce ===
Contributors: mr.clayton
Tags: paypal, paylater, venmo, credit cards
Requires at least: 4.7
Tested up to: 6.0
Requires PHP: 7.1
Stable tag: 1.0.12
Copyright: Payment Plugins
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Developed exclusively between Payment Plugins and PayPal, PayPal for WooCommerce integrates with PayPal's newest API's.
To boost conversion rates, you can offer PayPal, Pay Later, Venmo, or credit cards on your site. There are many supported features so
merchants can tweak the plugin to suit their business needs.

= Supports =
- WooCommerce Subscriptions
- WooCommerce Pre-Orders
- WooCommerce Blocks
- WooFunnels AeroCheckout and Upsell
- Integrates with [Payment Plugins for Stripe WooCommerce](https://wordpress.org/plugins/woo-stripe-payment/)
- [CheckoutWC](https://www.checkoutwc.com/)

= Payment Plugins is an official PayPal Partner =

== Screenshots ==
1. Offer payment buttons and display Pay Later messaging on product pages
2. Offer payment buttons and display Pay Later messaging on the cart page
3. Customize the checkout page button location
4. Customize the checkout page button location
5. Express payment buttons on the checkout page
6. Easily connect your PayPal account to your WooCommerce store
7. Lots of options for customizing the Pay Later messaging

== Frequently Asked Questions ==
= How do I test the plugin? =
The plugin has a sandbox option, where you can test payments. Our documentation shows you how to setup a Sandbox account.
[Documentation](https://docs.paymentplugins.com/wc-paypal/config/#/create_sandbox_account)

= How do I connect my PayPal account? =
Our documentation has a step-by-step guide on how to connect the plugin to your PayPal account. [Documentation](https://docs.paymentplugins.com/wc-paypal/config/#/connect)

= Who is Payment Plugins =
Payment Plugins is the team behind several of the highest reviewed and installed Payment integrations for WooCommerce.

== Changelog ==
= 1.0.12 - 10.12.22 =
* Updated - Improved messaging when reference transactions aren't enabled on the Merchant PayPal business account
* Updated - Improved Express Checkout buttons compatibility with currency plugins
* Added - Support for the new WooCommerce custom order tables (HPOS)
* Added - Capture On Status option which allows merchants to capture an authorized order on either the processing or completed status. Setting can be set to manual as well
* Fixed - Don't override shipping label first and last name with billing name
= 1.0.11 - 10.1.22 =
* Fixed - WooFunnels Upsell refund not processing
* Fixed - WooFunnels Upsell amount not always accurate
* Fixed - For currencies with no decimal points, adjust rounding to prevent decimals
= 1.0.10 - 9/23/22 =
* Updated - Use refund ID instead of order ID when processing refund.
* Updated - Don't override the shipping address first, last name if already populated on checkout page
* Fixed - WooFunnels One Click upsell, upsell not being triggered
= 1.0.9 - 9/14/22 =
* Fixed - Restrict item name length to 127 characters to prevent invalid schema error
* Updated - WC tested up to: 6.9
* Updated - Improved WooCommerce Blocks UI
= 1.0.8 - 9/12/22 =
* Updated - Improved billing address validations
* Updated - Only fill the billing email address field with the paypal email address if the field is blank
* Fixed - Compatibility with WooCommerce PayPal Payments recurring payments
* Added - Option to disable the credit card button tagline
= 1.0.7 - 8/29/22 =
* Updated - Improved Payer address validation for digital products
= 1.0.6 - 8/8/22 =
* Updated - WC tested up to: 6.8
* Added - Better compatability with the WooCommerce Checkout Add-Ons plugin
* Added - Better compatability with the WooCommerce Advanced Shipping Packages plugin
= 1.0.5 =
* Added - Compatibility with the [WooCommerce PayPal Checkout Gateway](https://wordpress.org/plugins/woocommerce-gateway-paypal-express-checkout/)
 plugin to ensure subscriptions process automatically and seamlessly when merchants switch.
* Added - Compatibility with the WooCommerce PayPal AngellEYE plugin to ensure subscriptions process automatically and seamlessly when merchants switch
= 1.0.4 - 7/21/22 =
* Fixed - CheckoutWC discounted order bump error
* Updated - WC tested up to: 6.7
* Added - Populate shipping_phone value if it exists and PayPal provides customer's phone number
= 1.0.3 - 6/27/22 =
* Fixed - PayLater messaging if option "Display prices in the shop" is enabled.
* Added - PayPal option in the CheckoutBlock payment gateways section and in the Express section
* Updated - Improved autofill logic for billing and shipping address
* Updated - WC tested up to: 6.6
= 1.0.2 - 5/20/22 =
* Fixed - Malformed request error during order creation if Payer's billing address is not valid
= 1.0.1 - 5/11/22 =
* Updated - Deactivation modal on Admin plugins page
* Updated - Improved error handling for PayPal script params.
= 1.0.0 - 5/4/22 =
* Initial release