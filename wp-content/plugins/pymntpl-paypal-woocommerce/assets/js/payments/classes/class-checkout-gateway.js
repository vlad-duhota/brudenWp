import $ from 'jquery';
import BaseGateway from './class-base-gateway';
import {
    getFieldValue,
    submitErrorMessage,
    setFieldValue,
    convertPayPalAddressToCart,
    extractFullName,
    isValidAddress, isValidFieldValue, extractShippingMethod, getPage
} from "@ppcp/utils";
import {isEmpty, isEqual} from 'lodash';

class CheckoutGateway extends BaseGateway {

    constructor(cart, props) {
        super(props);
        this.cart = cart;
        this.initialize();
    }


    initialize() {
        super.initialize();
        this.setVariable('readyToCheckout', false);
        this.actions = {};
        this.cart.on('updatedCheckout', this.updatedCheckout.bind(this));
        $(document.body).on('payment_method_selected', this.paymentMethodSelected.bind(this));
        $(document.body).on(`checkout_place_order_${this.id}`, this.validateCheckoutFields.bind(this));
        $(document.body).on('click', '.wc-ppcp-cancel__payment', this.cancelPayment.bind(this));
        $(document.body).on('change', '[name="terms"]', this.handleTermsClick.bind(this));
        window.addEventListener('hashchange', this.handleHashError.bind(this));
        this.handleOrderPay();
    }

    needsShipping() {
        return getPage() !== 'order_pay' && this.cart.needsShipping();
    }

    getFunding() {
        const funding = super.getFunding();
        if (this.isFundingActive('venmo') && this.isSectionEnabled('venmo', 'checkout')) {
            funding.push(paypal.FUNDING.VENMO);
        }
        return funding;
    }

    updatedCheckout() {
        super.initialize();
        this.paymentMethodSelected();
        if (this.isOrderReview()) {
            this.displayPaymentReadyMessage();
        } else if (this.isReadyToCheckout()) {
            this.displayPaymentReadyMessage();
        }
    }

    /**
     * Method that is called when a payment method is selected
     */
    paymentMethodSelected() {
        if (this.isPaymentGatewaySelected() && !this.isReadyToCheckout()) {
            this.displayPaymentButton();
        } else {
            this.hidePaymentButton();
        }
    }

    handleHashError(e) {
        var match = e.newURL.match(/ppcp_error=(.*)/);
        if (match) {
            const {1: error} = match;
            if (error == 'true') {
                this.displayPaymentButton();
                history.pushState({}, '', window.location.pathname + window.location.search);
            }
        }
    }

    isOrderReview() {
        let match = window?.location?.search?.match(/_ppcp_order_review=(.*)/);
        return match?.length > 0;
    }

    handleOrderPay() {
        if (this.isOrderReview()) {
            try {
                let match = window?.location?.search?.match(/_ppcp_order_review=(.*)/);
                const {1: obj} = match;
                const {payment_method, paypal_order, fields} = JSON.parse(atob(decodeURIComponent(obj)));
                setFieldValue(this.order_field_key, paypal_order, '');
                if (!isEmpty(fields)) {
                    for (let key in fields) {
                        setFieldValue(key, fields[key], '');
                    }
                }
                this.setVariable('readyToCheckout', true);
                //this.readyToCheckout = true;
                this.hidePaymentButton();
                if (this.needsShipping() && $('[name="ship_to_different_address"]')?.length) {
                    const bool = !isEqual(this.getCartAddress('billing'), this.getCartAddress('shipping'))
                    $('[name="ship_to_different_address"]').prop('checked', bool).trigger('change');
                }
            } catch (error) {
                console.log(error);
            }
        }
    }

    createOrder(data, actions) {
        if (this.isPage('checkout')) {
            const formData = this.convertFormToData();
            return this.cart.createOrder(formData).then(orderId => {
                return orderId;
            })
        } else {
            return this.cart.doOrderPay(this.id).then(orderId => {
                return orderId;
            })
        }
    }

    createButton() {
        super.createButton();
        this.paymentMethodSelected();
    }

    displayPaymentButton() {
        this.getButton()?.show();
        this.hidePlaceOrderButton();
    }

    hidePaymentButton() {
        this.getButton()?.hide();
        this.displayPlaceOrderButton();
    }

    displayPlaceOrderButton() {
        this.getPlaceOrderButton()?.removeClass('wc-ppcp-hide-button');
    }

    hidePlaceOrderButton() {
        this.getPlaceOrderButton()?.addClass('wc-ppcp-hide-button');
    }

    getPlaceOrderButton() {
        return $('#place_order');
    }

    getPayPalSDKArgs() {
        let data = {
            ...super.getPayPalSDKArgs(), ...{
                'commit': 'true',
            }
        };
        return data;
    }

    getButtonPlacement() {
        return this.settings?.buttonPlacement || 'place_order';
    }

    isPlaceOrderPlacement() {
        return this.getButtonPlacement() == 'place_order';
    }

    getButtonContainer() {
        let $parent = null;
        switch (this.getButtonPlacement()) {
            case 'place_order':
                $parent = $('#place_order');
                let $container = $parent?.parent().find('.wc-ppcp-checkout-container');
                if (!$container?.length) {
                    $parent.after('<div class="wc-ppcp-checkout-container"></div>');
                }
                break;
            case 'payment_method':
                $parent = $(`div.payment_method_${this.id}`);
                $('.wc-ppcp-payment-method__container').append('<div class="wc-ppcp-checkout-container"></div>');
                break;
        }
        // add container to parent;
        return document.querySelector('.wc-ppcp-checkout-container');
    }

    isPaymentGatewaySelected() {
        return $('[name="payment_method"]:checked')?.val() === this.id;
    }

    submitError(error) {
        submitErrorMessage(error, this.getForm(), 'checkout');
    }

    getShippingPrefix() {
        if ($('[name="ship_to_different_address"]')?.length) {
            if ($('[name="ship_to_different_address"]').is(':checked')) {
                return 'shipping';
            }
        }
        return 'billing';
    }

    handleOnApproveResponse(data, response) {
        this.populateCheckoutFields(response);
        this.processCheckout(data);
    }

    processCheckout(data) {
        this.hidePaymentButton();
        this.setVariable('readyToCheckout', true);
        if (this.update_required) {
            $(document.body).one('updated_checkout', () => {
                if (data.billingToken && this.needsShipping()) {
                    // show message that they can click place order
                    this.displayPaymentReadyMessage();
                } else {
                    this.getForm().submit();
                }
            });
            $('[name="billing_country"],[name="billing_state"]').trigger('change');
            if (this.shipToDifferentAddressChecked()) {
                $('[name="shipping_country"],[name="shipping_state"]').trigger('change');
            }
            $(document.body).trigger('update_checkout', {update_shipping_method: false});
        } else {
            this.getForm().submit();
        }
    }

    handleBillingToken(token, data) {
        super.handleBillingToken(token);
        if (this.needsShipping()) {
            this.update_required = true;
        }
        this.maybeShipToDifferentAddress();
        this.processCheckout(data);
    }

    populateCheckoutFields(response) {
        if (!isEmpty(response?.payer?.address)) {
            let address = convertPayPalAddressToCart(response.payer.address);
            if (isValidAddress(address, ['first_name', 'last_name']) && !isEqual(this.getCartAddress('billing'), address)) {
                this.populateBillingAddressFields(address);
            }
        }
        if (response?.payer?.name) {
            this.populateNameFields(response.payer.name, 'billing');
        }
        // only overwrite billing email if the field is blank
        if (response?.payer?.email_address && !isValidFieldValue(getFieldValue('billing_email'))) {
            setFieldValue('billing_email', response.payer.email_address);
        }
        if (response?.payer?.phone?.phone_number?.national_number) {
            setFieldValue('billing_phone', response.payer.phone.phone_number.national_number);
            if (this.needsShipping()) {
                setFieldValue('shipping_phone', response.payer.phone.phone_number.national_number);
            }
        }
        // update the shipping address if one is included
        if (this.needsShipping()) {
            if (!isEmpty(response?.purchase_units?.[0]?.shipping?.address)) {
                let address = convertPayPalAddressToCart(response.purchase_units[0].shipping.address);
                let name = '';
                if (!isEqual(this.cartAddress, address)) {
                    this.update_required = true;
                    this.cartAddress = address;
                    this.populateShippingAddressFields(address);
                    $(document.body).one('updated_checkout', () => this.populateShippingAddressFields(address));
                }
                if (response.purchase_units[0].shipping?.name?.full_name) {
                    name = extractFullName(response.purchase_units[0].shipping.name.full_name);
                    this.populateNameFields(name, 'shipping');
                }
                // add billing address if possible
                if (!isValidAddress(this.getCartFullAddress('billing'), ['phone', 'email'])) {
                    if (name && !isValidFieldValue(getFieldValue('billing_first_name')) && !isValidFieldValue(getFieldValue('billing_last_name'))) {
                        this.populateNameFields(name, 'billing');
                    }
                    this.populateBillingAddressFields(address);
                }
            }
            this.maybeShipToDifferentAddress();
        }
    }

    maybeShipToDifferentAddress() {
        // compare billing and shipping address. If not equal, then select ship to different address
        if ($('[name="ship_to_different_address"]')?.length) {
            const bool = !isEqual({
                ...this.getCartAddress('billing'),
                name: this.getFullName('billing')
            }, {...this.getCartAddress('shipping'), name: this.getFullName('shipping')})
            $('[name="ship_to_different_address"]').prop('checked', bool).trigger('change');
        }
    }

    getForm() {
        if (this.isPage('checkout')) {
            return $(this.container).closest('form.checkout');
        } else {
            return $(this.container).closest('form');
        }
    }

    validateTerms() {
        if ($('[name="terms"]').length && $('[name="terms"]').is(':visible')) {
            if (!$('[name="terms"]').is(':checked')) {
                return false;
            }
        }
        return true;
    }

    validateCheckoutFields() {
        if (!this.validateTerms()) {
            this.submitError({code: 'terms'});
            return false;
        }
        return true;
    }

    handleTermsClick() {
        if (this.isPlaceOrderPlacement()) {
            if ($('[name="terms"]').length) {
                const checked = $('[name="terms"]').is(':checked');
                if (checked) {
                    this.enableButtons();
                } else {
                    this.disableButtons();
                }
            }
        }
    }

    onInit(source, data, actions) {
        super.onInit(source, data, actions);
        this.handleTermsClick();
    }

    onClick(data, actions) {
        if (this.isPlaceOrderPlacement() && !this.validateTerms()) {
            this.submitError({code: 'terms'});
        }
    }

    onShippingChange(data, actions) {
        if (data?.selected_shipping_option?.id) {
            const shippingMethod = extractShippingMethod(data.selected_shipping_option.id);
            for (let index of Object.keys(shippingMethod)) {
                const method = shippingMethod[index];
                const el = $(`[name="shipping_method[${index}]"][value="${method}"]`);
                if (el.length) {
                    el.prop('checked', true);
                }
            }
        }
        return super.onShippingChange(data, actions, this.convertFormToData());
    }

    shipToDifferentAddressChecked() {
        if ($('[name="ship_to_different_address"]')?.length) {
            return $('[name="ship_to_different_address"]').is(':checked');
        }
        return false;
    }

    displayPaymentReadyMessage() {
        $('.wc-ppcp-popup__container').hide();
        $('.wc-ppcp-order-review-message__container').show();
        const txt = $('.wc-ppcp-order-review__message').text().replace('%s', $('#place_order').text());
        $('.wc-ppcp-order-review__message').text(txt);
    }

    hidePaymentReadyMessage() {
        $('.wc-ppcp-popup__container').show();
        $('.wc-ppcp-order-review-message__container').hide();
    }

    /**
     * Cancels an existing payment method
     */
    cancelPayment(e) {
        e.preventDefault();
        this.setVariable('readyToCheckout', false);
        this.hidePaymentReadyMessage();
        this.displayPaymentButton();
    }

    getProcessingSelector() {
        return this.container;
    }

    getProcessingMessage() {
        return null;
    }

    fetchBillingToken(token) {
        this.showProcessing();
        return super.fetchBillingToken(token).then(response => {
            this.hideProcessing();
            return response;
        });
    }

    isReadyToCheckout() {
        return this.getVariable('readyToCheckout', false);
    }

    convertFormToData() {
        return $('form.checkout').serializeArray().reduce((prev, current) => ({...prev, [current.name]: current.value}), {});
    }
}

export {
    CheckoutGateway
}