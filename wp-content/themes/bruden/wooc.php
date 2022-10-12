<?php

add_action( 'after_setup_theme', 'ms_woocommerce_support' );
function ms_woocommerce_support() {
    add_theme_support( 'woocommerce' );
    // add_theme_support( 'wc-product-gallery-zoom' );
    // add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
}

// add_filter('woocommerce_get_image_size_single', function ($size){
//     return array(
//         'width' => 520,
//         'height' => 680,
//         'crop'   => 0,
//     );
// });

// add_filter( 'woocommerce_output_cart_shortcode_content', 'jy_cart_shortcode', 25 );
// function jy_cart_shortcode( $display_cart ) {

//     if( is_wc_endpoint_url( 'order-received' ) ) {
//         $display_cart = false;
//     }
//     return $display_cart;

// }