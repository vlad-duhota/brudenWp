<?php

if (!defined('ABSPATH')) {
   exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// =========== HOME PAGE ===========

Container::make('post meta', 'Проект')
->where( 'post_type', '=', 'project' )    
->add_fields( array(
   Field::make( 'text', 'project_desc', 'Опис' ),
   Field::make( 'text', 'project_link', 'Посилання' ),
   Field::make( 'text', 'project_author', 'Автор' ),
   Field::make( 'text', 'project_age', 'Вік' ),
   Field::make( 'image', 'project_author_img', 'Фото автора(авторів)' )
   ->set_value_type('url'),
   Field::make( 'image', 'project_mini_img', 'Мініатюра фото проекта' )
   ->set_value_type('url'),
) );




