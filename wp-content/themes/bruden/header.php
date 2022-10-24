
    <!DOCTYPE html>

    <html <?php language_attributes(); ?>>
        
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            
            <!-- Fonts -->
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Cormorant:wght@300;400;500;600&family=Josefin+Sans:wght@300;500&family=Lato:wght@700&display=swap" rel="stylesheet">

            <!-- Styles -->
            <?php wp_head(); ?>
        </head>

<body <?php body_class(); ?> style="overflow: hidden">
    <?php
      if ( function_exists( 'wp_body_open' ) ) {
        wp_body_open();
      }
    ?>
        <div class="preloader">
        <img class="preloader__img" src="<?php echo get_template_directory_uri()?>/assets/img/loader.gif">
    </div>
    <div class="wrapper">
        <header class="header">
            <div class="container">
                <?php echo get_custom_logo(); ?>
                <?php
                    wp_nav_menu( array( 
                        'theme_location' => 'main_menu', 
                        'container_class' => 'nav'
                    ) );
                ?>
                <div class="header__right">
                    <div class="header__right-item">
                        <a href="<?php echo carbon_get_theme_option('insta_url')?>" class="header__right__link" target="_blank">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/insta.svg">
                        </a>
                    </div>
                    <!-- <div class="header__right-item header__right-item_search">
                        <button class="header__right__link">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/search.svg">
                        </button>
                    </div>
                    <div class="header__search">
                        <form action="<?php bloginfo( 'url' ); ?>" method="get">
                            <input type="search" name="s" maxlength="70" placeholder="Enter your ask" required/>
                            <input type="submit" value="Search">
                        </form>
                    </div> -->
                    <div class="header__right-item">
                        <a href="<?php echo site_url('my-account') ?>" class="header__right__link">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/user.svg">
                        </a>
                    </div>
                    <?php get_sidebar( 'cart' ); ?>
                </div>
                <button class="header__burger-btn">
                    <span></span>
                </button>
            </div>
        </header>