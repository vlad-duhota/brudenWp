<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version     3.3.2
 * @package 	WooCommerce/Templates
 *
 * @author 		WooThemes
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.

global $product;

$post_thumbnail_id = $product->get_image_id();
$gallery_options   = get_option( 'wpgs_form' );

// Use new, updated functions
$attachment_ids         = $product->get_gallery_image_ids();
$gallery_thumbnail_size = $gallery_options['thumbnail_image_size'];

if ( $attachment_ids && has_post_thumbnail() ) {
	echo '<div class="wpgs-nav">';
	$image = wp_get_attachment_image( $post_thumbnail_id, $gallery_thumbnail_size, true, [
		"class" => "wpgs-thumb-main-image",
		'alt'   => trim( wp_strip_all_tags( get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true ) ) ),
	] );
	echo '<div>' . $image . '</div>';

	foreach ( $attachment_ids as $attachment_id ) {
		$thumbnail_image = wp_get_attachment_image( $attachment_id, $gallery_thumbnail_size, true, [
			'alt' => trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
		] );
		echo '<div>' . $thumbnail_image . '</div>';
	}
	echo "</div>";
}
do_action( 'wpgs_after_image_gallery' );
