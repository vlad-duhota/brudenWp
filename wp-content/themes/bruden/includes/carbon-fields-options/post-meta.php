<?php

if (!defined('ABSPATH')) {
   exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// =========== ABOUT PAGE ===========

Container::make('post meta', 'First section')
->show_on_template('about-page.php')
->add_fields( array(
   Field::make( 'text', 'about_uptitle', 'Uptitle' ),
   Field::make( 'text', 'about_title', 'Title' ),
   Field::make( 'text', 'about_text_1', 'Text paragraph 1' ),
   Field::make( 'text', 'about_text_2', 'Text paragraph 2' ),
   Field::make( 'image', 'about_img', 'Photo' ),
) );


Container::make('post meta', 'Advantages section')
->show_on_template('about-page.php')
->add_fields( array(
   Field::make( 'complex', 'advantages_list', 'List' )
   ->set_max(3)
   ->add_fields( array(
       Field::make( 'text', 'advantages_list_title', 'Title' ),
       Field::make( 'text', 'advantages_list_text', 'Text' ),
   ) )
) );





