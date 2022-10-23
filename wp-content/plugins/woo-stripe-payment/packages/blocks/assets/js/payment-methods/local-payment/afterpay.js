import {useState, useEffect} from '@wordpress/element';
import {registerPaymentMethod} from '@woocommerce/blocks-registry';
import {getSettings, initStripe} from "../util";
import {LocalPaymentIntentContent} from './local-payment-method';
import {PaymentMethod} from "../../components/checkout";
import {canMakePayment} from "./local-payment-method";
import {AfterpayClearpayMessageElement, Elements} from "@stripe/react-stripe-js";
import {sprintf, __} from '@wordpress/i18n';
import {ExperimentalOrderMeta, TotalsWrapper} from '@woocommerce/blocks-checkout';
import {registerPlugin} from '@wordpress/plugins';

const getData = getSettings('stripe_afterpay_data');
let variablesHandler;
let globalVariables = {};
const setVariablesHandler = (handler) => {
    variablesHandler = handler;
}

const isAvailable = ({total, currency, country}) => {
    let available = false;
    const billingCountry = country;
    const requiredParams = getData('requiredParams');
    const accountCountry = getData('accountCountry');
    const requiredParamObj = requiredParams.hasOwnProperty(currency) ? requiredParams[currency] : false;
    if (requiredParamObj) {
        let countries = requiredParamObj?.[0];
        if (!Array.isArray(countries)) {
            countries = [countries];
        }
        available = countries.indexOf(accountCountry) > -1
            && (currency !== 'EUR' || !billingCountry || accountCountry === billingCountry)
            && (total > requiredParamObj?.[1] && total < requiredParamObj?.[2]);
    }
    return available;
}

const PaymentMethodLabel = ({getData}) => {
    const [variables, setVariables] = useState({
        amount: getData('cartTotal'),
        currency: getData('currency'),
        isCartEligible: getData('msgOptions').isEligible,
        ...globalVariables
    });
    setVariablesHandler(setVariables);
    return (
        <Elements stripe={initStripe} options={getData('elementOptions')}>
            <div className='wc-stripe-blocks-afterpay__label'>
                <AfterpayClearpayMessageElement options={{
                    ...getData('msgOptions'),
                    ...{
                        amount: variables.amount,
                        currency: variables.currency,
                        isCartEligible: variables.isCartEligible
                    }
                }}/>
            </div>
        </Elements>
    );
}

const AfterpayPaymentMethod = ({content, billing, shippingData, ...props}) => {
    const Content = content;
    const {cartTotal, currency, billingData: {country}} = billing;
    const {needsShipping} = shippingData
    const total = parseInt(cartTotal.value) / 10 ** currency.minorUnit;
    const isCartEligible = isAvailable({total, currency: currency.code, country});
    useEffect(() => {
        variablesHandler({
            amount: cartTotal.value,
            currency: currency.code,
            isCartEligible
        });
    }, [
        cartTotal.value,
        currency.code,
    ]);
    return (
        <>
            <div className='wc-stripe-blocks-payment-method-content'>
                {isCartEligible && <div className="wc-stripe-blocks-afterpay-offsite__container">
                    <div className="wc-stripe-blocks-afterpay__offsite">
                        <img src={getData('offSiteSrc')}/>
                        <p>{sprintf(__('After clicking "%s", you will be redirected to Afterpay to complete your purchase securely.', 'woo-stripe-payment'), getData('placeOrderButtonLabel'))}</p>
                    </div>
                </div>}
                <Content {...{...props, billing, shippingData}}/>
            </div>
        </>
    );
}

const OrderItemMessaging = ({cart, extensions, context}) => {
    const {cartTotals, cartNeedsShipping: needsShipping, billingAddress: {country}} = cart;
    const {total_price, currency_code: currency} = cartTotals;
    const totalInCents = parseInt(cartTotals.total_price);
    const total = parseInt(cartTotals.total_price) / (10 ** cartTotals.currency_minor_unit);
    if (!isAvailable({total, currency, country})) {
        return null;
    }
    return (
        <TotalsWrapper>
            <Elements stripe={initStripe} options={getData('elementOptions')}>
                <div className='wc-stripe-blocks-afterpay-totals__item wc-block-components-totals-item'>
                    <AfterpayClearpayMessageElement options={{
                        ...getData('msgOptions'),
                        ...{
                            amount: totalInCents,
                            currency,
                            isCartEligible: isAvailable({total, currency, country})
                        }
                    }}/>
                </div>
            </Elements>
        </TotalsWrapper>
    );
}

if (getData()) {
    registerPaymentMethod({
        name: getData('name'),
        label: <PaymentMethodLabel
            getData={getData}/>,
        ariaLabel: __('Afterpay', 'woo-stripe-payment'),
        placeOrderButtonLabel: getData('placeOrderButtonLabel'),
        canMakePayment: canMakePayment(getData, ({settings, cartTotals, billingData}) => {
            const {currency_code: currency, currency_minor_unit, total_price} = cartTotals;
            const {country} = billingData;
            const total = parseInt(total_price) / (10 ** currency_minor_unit);
            if (variablesHandler) {
                variablesHandler({
                    amount: parseInt(cartTotals.total_price),
                    currency,
                    isCartEligible: isAvailable({total, currency, country})
                });
            } else {
                globalVariables = {
                    amount: parseInt(cartTotals.total_price),
                    currency,
                    isCartEligible: isAvailable({total, currency, country})
                };
            }
            const available = isAvailable({total, currency, country});
            if (!available && !settings('hideIneligible')) {
                return true;
            }
            return available;
        }),
        content: <AfterpayPaymentMethod
            content={LocalPaymentIntentContent}
            getData={getData}
            confirmationMethod={'confirmAfterpayClearpayPayment'}/>,
        edit: <PaymentMethod content={LocalPaymentIntentContent} getData={getData}/>,
        supports: {
            showSavedCards: false,
            showSaveOption: false,
            features: getData('features')
        }
    });

    const render = () => {
        return (
            <ExperimentalOrderMeta>
                <OrderItemMessaging/>
            </ExperimentalOrderMeta>
        )
    }
    registerPlugin('wc-stripe', {
        render: render,
        scope: 'woocommerce-checkout'
    })
}