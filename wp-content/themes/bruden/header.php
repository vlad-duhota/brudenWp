
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

<body <?php body_class(); ?>>
    <?php
      if ( function_exists( 'wp_body_open' ) ) {
        wp_body_open();
      }
    ?>
    <div class="wrapper">
        <header class="header">
            <div class="container">
                <a href="index.html" class="logo">
                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/logo.png">
                </a>
                <?php
          wp_nav_menu( array( 
            'theme_location' => 'main_menu', 
            'container_class' => 'nav' ) ); 
          ?>
                <div class="header__right">
                    <div class="header__right-item">
                        <a href="#" class="header__right__link">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/insta.svg">
                        </a>
                    </div>
                    <div class="header__right-item header__right-item_search">
                        <button class="header__right__link">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/search.svg">
                        </button>
                    </div>
                    <div class="header__right-item">
                        <a class="header__right__link">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/user.svg">
                        </a>
                    </div>
                    <div class="header__right-item header__right-item_cart">
                        <a class="header__right__link">
                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/cart.svg">
                            <span> 0 </span>
                        </a>
                        <p class="header__right-price">
                            <span>&#36</span> 0.00
                        </p>
                    </div>
                </div>
                <button class="header__burger-btn">
                    <span></span>
                </button>
            </div>
        </header>