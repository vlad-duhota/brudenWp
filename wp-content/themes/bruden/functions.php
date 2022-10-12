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
    wp_enqueue_style('shop', get_template_directory_uri() . '/assets/css/shop.css', [], $version);
    if(is_page_template('front-page.php')){
        wp_enqueue_style('front-page-style', get_template_directory_uri() . '/assets/css/main.css', [], $version);
    }
    if(is_page_template('about-page.php')){
        wp_enqueue_style('about-style', get_template_directory_uri() . '/assets/css/about.css', [], $version);
    }
    if(is_page_template('contact-page.php')){
        wp_enqueue_style('contact-style', get_template_directory_uri() . '/assets/css/contact.css', [], $version);
    }
    if(is_archive()){
        wp_enqueue_style('bloh-style', get_template_directory_uri() . '/assets/css/blog.css', [], $version);
    }
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
    wp_enqueue_script('menu', get_template_directory_uri() . '/assets/js/menu.js', [], $version , true);
    wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main.js', [], $version , true);
    if(is_page_template('about-page.php')){
        wp_enqueue_script('about', get_template_directory_uri() . '/assets/js/about.js', [], $version , true);
    }
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
add_image_size('thumbnail_520x680', 520, 680, true);

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


function bruden_register_wp_sidebars() {
	/* In aside */
	register_sidebar(
		array(
			'id' => 'bruden_aside',
			'name' => 'Siidebar',
			'description' => 'Drag widgets into it to show them on sidebar.',
			'before_widget' => '<div id="%1$s" class="sidebar-aside %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		)
	);
    /* In footer */
	register_sidebar(
		array(
			'id' => 'bruden_foot',
			'name' => 'Footer',
			'description' => 'Drag widgets into it to show them on sidebar.',
			'before_widget' => '<div id="%1$s" class="sidebar-foot widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		)
	);
    /* In Shop Aside */
	register_sidebar(
		array(
			'id' => 'bruden_shop',
			'name' => 'Shop && Product',
			'description' => 'Drag widgets into it to show them on sidebar.',
			'before_widget' => '<div id="%1$s" class="sidebar-woo widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>'
		)
	);
}
add_action( 'widgets_init', 'bruden_register_wp_sidebars' );


// include woocommerce
if ( class_exists( 'WooCommerce' ) ) {
    require_once( get_template_directory() . '/wooc.php' );
}

// off woocommerce styles
add_filter( 'woocommerce_enqueue_styles', '__return_false' );