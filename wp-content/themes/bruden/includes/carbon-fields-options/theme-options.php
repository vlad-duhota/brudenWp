<?php
if (!defined('ABSPATH')) {
    exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make( 'theme_options', 'Опції Теми' )
    ->add_tab( __('Загальне'), array(
        Field::make( 'image', 'logo', 'Логотип' )
        ->set_value_type('url'),
        Field::make( 'text', 'page_text_1', 'Текст сторінки 1' )
        ->set_width(50),
        Field::make( 'text', 'page_text_2', 'Текст сторінки 2' )
        ->set_width(50),
        Field::make( 'checkbox', 'language_select', 'Показувати селект для зміни мови' ),

    ) )
        ->add_tab( __('Хедер'), array(
        Field::make( 'text', 'title', 'Заголовок' )
        ->set_help_text( 'оберніть жовтий текст у тег span' ),
        Field::make( 'text', 'subtitle', 'Підзаголовок' ),
    ) )
    ->add_tab( __('Футер'), array(
        Field::make( 'complex', 'tels', 'Телефони' )
        ->add_fields( array(
            Field::make( 'text', 'tel', 'Номер телефону' ),
        ) )
        ->set_max(3),
        Field::make( 'complex', 'socials', 'Соціальні мережі' )
        ->add_fields( array(
            Field::make( 'text', 'socials_url', 'Посилання' ),
            Field::make( 'image', 'socials_img', 'Зображення соцмережі' )
            ->set_value_type('url'),
        ) )
        ->set_max(4),
    ) );
