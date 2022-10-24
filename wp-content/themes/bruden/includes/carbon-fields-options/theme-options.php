<?php
if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('theme_options', 'Theme options')
->add_tab( __('Global Settings'), array(
    Field::make( 'separator', 'crb_separator', 'Header'  ),
    Field::make( 'text', 'insta_url', 'Instagram Url' ),
    Field::make( 'separator', 'crb_separator_2', 'Footer'  ),
    Field::make( 'text', 'insta_name', 'Instagram Profile Name' ),
    Field::make( 'separator', 'crb_separator_3', 'Contacts'  ),
    Field::make( 'text', 'street', 'Street' )
    ->set_width(50),
    Field::make( 'text', 'street_url', 'Street Url' )
    ->set_width(50),
    Field::make( 'text', 'phone', 'phone number' )
    ->set_width(50),
    Field::make( 'text', 'phone_url', 'phone number without any symbols and spaces' )
    ->set_width(50),
    Field::make( 'text', 'email', 'email' )
    ->set_width(100),
) )
->add_tab( __('Home Page'), array(
    Field::make( 'separator', 'crb_separator_hero', 'Hero'  ),
    Field::make( 'complex', 'hero_slider', 'slider' )
    ->add_fields( array(
        Field::make( 'image', 'hero_slider_img', 'Img' ),
        Field::make( 'text', 'hero_slider_title', 'Title' ),
        Field::make( 'text', 'hero_slider_text', 'Text' ),
        Field::make( 'complex', 'hero_slider_tags', 'Tags' )
        ->add_fields( array(
            Field::make( 'text', 'hero_slider_tag', 'Tag' ),
        ) )
    ->set_max(3),
    ) )
    ->set_max(6),
    Field::make( 'separator', 'crb_separator_titles', 'Titles'  ),
    Field::make( 'text', 'title_1', 'Categories title' ),
    Field::make( 'text', 'title_2', 'Deal Of The Week title' ),
    Field::make( 'text', 'title_3', 'Latest News title' ),
    Field::make( 'text', 'title_4', 'Special Products title' ),
    Field::make( 'separator', 'crb_separator_banner_1', 'First banner'  ),
    Field::make( 'text', 'banner_1_title', 'Banner title' )
    ->set_width(80),
    Field::make( 'image', 'underlay', 'Main page image' )
    ->set_width(20),
    Field::make( 'separator', 'crb_separator_banner_2', 'Second banner'  ),
    Field::make( 'text', 'banner_2_title_1', 'Banner title 1' )
    ->set_width(28),
    Field::make( 'text', 'banner_2_text_1', 'Banner 1 text' )
    ->set_width(28),
    Field::make( 'text', 'banner_2_link_1', 'Banner 1 link' )
    ->set_width(28),
    Field::make( 'image', 'banner_2_img_1', 'Banner 1 img' )
    ->set_width(16),
    Field::make( 'separator', 'crb_separator_banner_sep', 'Next part of second banner'  ),
    Field::make( 'text', 'banner_2_title_2', 'Banner title 2' )
    ->set_width(50),
    Field::make( 'text', 'banner_2_link_2', 'Banner 2 link' )
    ->set_width(30),
    Field::make( 'image', 'banner_2_img_2', 'Banner 2 img' )
    ->set_width(20),
    Field::make( 'separator', 'crb_separator_fits', 'Fits section'  ),
    Field::make( 'text', 'fits_title', 'Fits title' )
    ->set_width(40),
    Field::make( 'text', 'fits_text', 'Fits text' )
    ->set_width(40),
    Field::make( 'image', 'fits_img', 'Fits img' )
    ->set_width(20),
) );




