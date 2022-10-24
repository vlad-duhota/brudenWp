<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'Psr\\Http\\Message\\' => array($vendorDir . '/psr/http-message/src', $vendorDir . '/psr/http-factory/src'),
    'Psr\\Http\\Client\\' => array($vendorDir . '/psr/http-client/src'),
    'PaymentPlugins\\WooCommerce\\PPCP\\' => array($baseDir . '/src'),
    'PaymentPlugins\\PayPalSDK\\' => array($vendorDir . '/paymentplugins/paypal-php-sdk/src'),
    'PaymentPlugins\\PPCP\\WooFunnels\\' => array($baseDir . '/packages/woofunnels/src'),
    'PaymentPlugins\\PPCP\\Stripe\\' => array($baseDir . '/packages/stripe/src'),
    'PaymentPlugins\\PPCP\\CheckoutWC\\' => array($baseDir . '/packages/checkoutwc/src'),
    'PaymentPlugins\\PPCP\\Blocks\\' => array($baseDir . '/packages/blocks/src'),
    'GuzzleHttp\\Psr7\\' => array($vendorDir . '/guzzlehttp/psr7/src'),
    'GuzzleHttp\\Promise\\' => array($vendorDir . '/guzzlehttp/promises/src'),
    'GuzzleHttp\\' => array($vendorDir . '/guzzlehttp/guzzle/src'),
);