<?php
add_filter('show_admin_bar', '__return_false');

remove_action('wp_head',             'print_emoji_detection_script', 7 );
remove_action('admin_print_scripts', 'print_emoji_detection_script' );
remove_action('wp_print_styles',     'print_emoji_styles' );
remove_action('admin_print_styles',  'print_emoji_styles' );

remove_action('wp_head', 'wp_resource_hints', 2 );          //remove dns-prefetch
remove_action('wp_head', 'wp_generator');                   //remove meta name="generator"
remove_action('wp_head', 'wlwmanifest_link');               //remove wlwmanifest
remove_action('wp_head', 'rsd_link');                       //remove EditURI
remove_action('wp_head', 'rest_output_link_wp_head');       //remove 'https://api.w.org/
remove_action('wp_head', 'rel_canonical');                  //remove canonical
remove_action('wp_head', 'wp_shortlink_wp_head', 10);       //remove shortlink
remove_action('wp_head', 'wp_oembed_add_discovery_links');  //remove alternate

// styles
add_action('wp_enqueue_scripts', 'site_styles');
function site_styles () {
    $version = '0.1';
    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', [], $version);
    wp_enqueue_style('theme-style', get_stylesheet_uri());
    wp_enqueue_style('main-style', get_template_directory_uri() . '/assets/css/style.css', [], $version);
    if(is_page_template('front-page.php')){
        wp_enqueue_style('front-page-style', get_template_directory_uri() . '/assets/css/main.css', [], $version);
    }
    // if(is_search()){
    //     wp_enqueue_style('search-style', get_template_directory_uri() . '/assets/css/search.css', [], $version);
    // }
    // if(is_archive()){
    //     wp_enqueue_style('archive-style', get_template_directory_uri() . '/assets/css/archive.css', [], $version);
    // }
    if (is_single()) {
        wp_enqueue_style('post-style', get_template_directory_uri() . '/assets/css/post.css', [], $version);
    }
    if (is_home()) {
        wp_enqueue_style('bloh-style', get_template_directory_uri() . '/assets/css/blog.css', [], $version);
    }
}

// scripts
add_action('wp_enqueue_scripts', 'site_scripts');
function site_scripts() {
    $version = '0.1';
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', [], $version , true);
    if(is_page_template('front-page.php')){
        wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main.js', [], $version , true);
    }
    // if (is_single()) {
    //     wp_enqueue_script('post-script', get_template_directory_uri() . '/assets/js/post.js', [], $version, true);
    // }
}

// Carbon Fields
add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
	require_once( 'includes/carbon-fields/vendor/autoload.php' );
	\Carbon_Fields\Carbon_Fields::boot();
}

add_action('carbon_fields_register_fields', 'register_carbon_fields');
function register_carbon_fields () {
    require_once('includes/carbon-fields-options/theme-options.php');
    require_once('includes/carbon-fields-options/post-meta.php');
}

// Theme support
add_theme_support( 'title-tag' );
add_theme_support( 'custom-logo' );
add_theme_support('post-thumbnails');
add_image_size('product', 500, 313, true);

// Global variables

// hide front page text editor
function disable_content_editor()
{
    if (isset($_GET['post'])) {
        $post_ID = $_GET['post'];
    } else if (isset($_POST['post_ID'])) {
        $post_ID = $_POST['post_ID'];
    }

    if (!isset($post_ID) || empty($post_ID)) {
        return;
    }

    $page_template = get_post_meta($post_ID, '_wp_page_template', true);
    if ($page_template == 'front-page.php') {
        remove_post_type_support('page', 'editor');
    }
}
add_action('admin_init', 'disable_content_editor');

function siteDefPaging( \WP_Query $wp_query=null, $echo=true, $params=[] ){
    if ( null === $wp_query ) {
        global $wp_query;
    }
    $add_args = [];
    $pages = paginate_links( array_merge( [
            'joocy'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'       => '?paged=%#%',
            'current'      => max( 1, get_query_var( 'paged' ) ),
            'total'        => $wp_query->max_num_pages,
            'type'         => 'array',
            'show_all'     => false,
            'end_size'     => 3,
            'mid_size'     => 1,
            'prev_next'    => true,
            'prev_text'    => '<div class="blog__paggination-arrow blog__paggination-arrow_left"><</div>',
            'next_text'    => '<div class="blog__paggination-arrow blog__paggination-arrow_right">></div>',
            'add_args'     => $add_args,
            'add_fragment' => ''
        ], $params )
    );

    if( is_array( $pages ) ) {
        $pagination = '';
        foreach ( $pages as $page ) {
            $pagination .= '<li class="blog__paggination-item' . ( strpos( $page, 'current') !== false ? ' blog__paggination-item_active' : '') . '"> ' . str_replace('page-numbers', 'page-link', $page ) . '</li>';
        }
        if ( $echo ) {
            echo $pagination;
        } else {
            return $pagination;
        }
    }

    return null;
}

// include the menu etc
add_action( 'after_setup_theme', 'theme_support' );
function theme_support() {
  register_nav_menu( 'main_menu', 'Main menu' );
}


function true_register_wp_sidebars() {
 
	/* В боковой колонке - первый сайдбар */
	register_sidebar(
		array(
			'id' => 'true_side', // уникальный id
			'name' => 'Siidebar', // название сайдбара
			'description' => 'Drag widgets into it to show them on sidebar.', // описание
			'before_widget' => '<div id="%1$s" class="side-widget %2$s">', // по умолчанию виджеты выводятся <li>-списком
			'after_widget' => '</div>',
			'before_title' => '<h2 class="widget-title">', // по умолчанию заголовки виджетов в <h2>
			'after_title' => '</h2>'
		)
	);
 
}
 
add_action( 'widgets_init', 'true_register_wp_sidebars' );