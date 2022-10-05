<?php
if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('theme_options', 'Theme options')
->add_fields( array(
    Field::make( 'separator', 'crb_separator', 'Header'  ),
    Field::make( 'image', 'logo', 'Logo' )
    ->set_value_type('url'),
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
) );




