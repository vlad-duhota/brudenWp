<?php
/*
Template Name: Home page
*/
?>

<?php 
    $page_id = get_the_ID();
?>

<?php get_header(); ?>
  
        <main class="main">
            <section class="hero">
              <div class="container">
                <div class="hero-swiper swiper">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper hero-swiper__wrapper">
                      <!-- Slides -->
                      <div class="swiper-slide hero__swiper-slide"><div class="hero__swiper-item">     
                        <div class="hero__content">
                        <ul class="hero__cats">
                            <li class="hero__cats-item">bags</li>
                            <li class="hero__cats-item">sunglasses</li>
                            <li class="hero__cats-item">belts</li>
                        </ul>
                        <h1 class="hero__title">Exclusive accessories <br> for connoisseurs of style</h1>
                        <p class="hero__text">Designed in Montreal, Bruden's Backpack embodies luxury fashion while keeping comfort and versatility</p>
                        <button class="btn hero__btn">Shop now</button>
                </div>
                    <img class="hero__img" src="<?php echo get_template_directory_uri() ?>/assets/img/hero_1.png"></div></div>
                    <div class="swiper-slide hero__swiper-slide"><div class="hero__swiper-item">     
                        <div class="hero__content">
                        <ul class="hero__cats">
                            <li class="hero__cats-item">bags</li>
                            <li class="hero__cats-item">sunglasses</li>
                            <li class="hero__cats-item">belts</li>
                        </ul>
                        <h1 class="hero__title">Exclusive accessories <br> for connoisseurs of style</h1>
                        <p class="hero__text">Designed in Montreal, Bruden's Backpack embodies luxury fashion while keeping comfort and versatility</p>
                        <button class="btn hero__btn">Shop now</button>
                </div>
                    <img class="hero__img" src="<?php echo get_template_directory_uri() ?>/assets/img/hero_2.png"></div></div>
                    <div class="swiper-slide hero__swiper-slide"><div class="hero__swiper-item">     
                        <div class="hero__content">
                        <ul class="hero__cats">
                            <li class="hero__cats-item">bags</li>
                            <li class="hero__cats-item">sunglasses</li>
                            <li class="hero__cats-item">belts</li>
                        </ul>
                        <h1 class="hero__title">Exclusive accessories <br> for connoisseurs of style</h1>
                        <p class="hero__text">Designed in Montreal, Bruden's Backpack embodies luxury fashion while keeping comfort and versatility</p>
                        <button class="btn hero__btn">Shop now</button>
                </div>
                    <img class="hero__img" src="<?php echo get_template_directory_uri() ?>/assets/img/hero_3.png"></div></div>
                    </div>
                    <!-- If we need pagination -->
                </div>
                <div class="hero-swiper__pagination"></div>
              </div>
            </section>
            <section class="category">
                <div class="container">
                    <h2 class="category__title title_dashed">
                        Shop by category
                    </h2>
                    <div class="category-swiper swiper">
                        <div class="swiper-wrapper category-swiper__wrapper"><?php
                            $prod_cat_args = array(
                                'taxonomy'    => 'product_cat',
                                'orderby'     => 'id', // здесь по какому полю сортировать
                                'hide_empty'  => false, // скрывать категории без товаров или нет
                                'parent'      => 0 // id родительской категории
                            );
                            $woo_categories = get_categories($prod_cat_args);
                            foreach ($woo_categories as $woo_cat) :
                                $woo_cat_id = $woo_cat->term_id; //category ID
                                $woo_cat_name = $woo_cat->name; //category name
                                $woo_cat_slug = $woo_cat->slug; //category slug
                            
                                if ($woo_cat_name !== "Misc") : ?>
                                        <div class="swiper-slide category__swiper-slide">
                                            <a href="<?php get_term_link($woo_cat_id, 'product_cat') ?>'"><?php
                                                $category_thumbnail_id = get_term_meta($woo_cat_id, 'thumbnail_id', true);
                                                $thumbnail_image_url = wp_get_attachment_url($category_thumbnail_id); ?>
                                                <img src="<?php echo $thumbnail_image_url ?>'" class="category__img"/>
                                                <h3 class="category__name"><?php echo $woo_cat_name ?></h3>
                                            </a>
                                        </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="navigation category__navigation">
                        <button class="nagination__prev-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/left_arr.png"></button>
                        <button class="nagination__next-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/right_arr.png"></button>
                    </div>
                </div>
            </section>
            <section class="additional">
                <div class="container" style="background: url(<?php echo get_template_directory_uri()?>/assets/img/additional.png) center/cover no-repeat;">
                    <h2 class="additional__title">An addition to all your fits</h2>
                    <button class="btn btn_white additional__btn">Shop now</button>
                </div>
            </section>
            <section class="deal">
                <div class="container"><?php 
                        query_posts([
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                        ]) 
                    ?>
                    <?php if ( have_posts() ) {
                        while ( have_posts() ) {
                            the_post(); ?>
                                <li class="blog__item">
                                    <a href="<?php the_permalink() ?>">
                                    <img class="blog__item-img" src=" <?php echo get_the_post_thumbnail_url()?>">
                                    </a>
                                    <h4 class="blog__item-title"><?php the_title() ?></h4>
                                </li>                     
                        <?php } 
                            wp_reset_query();
                        }
                    ?>
                    <h2 class="deal__title title_dashed">
                        Deal of the week
                    </h2>
                    <div class="deal-swiper swiper">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper deal-swiper__wrapper">
                            <!-- Slides -->
                            <div class="swiper-slide deal__swiper-slide"> 
                                <img class="deal__img" src="<?php echo get_template_directory_uri() ?>/assets/img/deal_1.png">
                                <div class="deal__content">
                                    <h4 class="deal__name">Fiery Red Bruden Backpack</h4>
                                    <div class="deal__stars">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                    </div>
                                    <p class="deal__price">C$ 99.99</p>
                                    <p class="deal__text">Bruden's Backpack will give all your essentials a home while still feeling comfortable and having a... </p>
                                    <div class="deal__cols">
                                        <button class="deal__btn">Add to cart</button>
                                        <button class="deal__heart">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/heart.svg">
                                        </button>
                                        <button class="deal__seen">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/eye.svg">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Slides -->
                            <div class="swiper-slide deal__swiper-slide"> 
                                <img class="deal__img" src="<?php echo get_template_directory_uri() ?>/assets/img/deal_2.png">
                                <div class="deal__content">
                                    <h4 class="deal__name">Bruden's Backpack</h4>
                                    <div class="deal__stars">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                    </div>
                                    <p class="deal__price">C$ 66.49</p>
                                    <p class="deal__text">Bruden's Backpack will give all your essentials a home while still feeling comfortable and having a... </p>
                                    <div class="deal__cols">
                                        <button class="deal__btn">Add to cart</button>
                                        <button class="deal__heart">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/heart.svg">
                                        </button>
                                        <button class="deal__seen">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/eye.svg">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- If we need pagination -->
                    </div>
                    <div class="navigation deal__navigation">
                        <button class="nagination__prev-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/left_arr.png"></button>
                        <button class="nagination__next-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/right_arr.png"></button>
                    </div>
                </div>
            </section>
            <section class="banner">
                <div class="container">
                    <div class="banner__left banner__part" style="background: url(<?php echo get_template_directory_uri()?>/assets/img/banner_1.png) center / cover no-repeat;">
                        <h2 class="banner__title">New arrival <span>Exclusive Denisha Bruce Red Backpack Launch</span></h2>
                        <button class="btn">Shop now</button>
                    </div>
                    <div class="banner__right banner__part" style="background: url(<?php echo get_template_directory_uri()?>/assets/img/banner_2.png) center / cover no-repeat;">
                        <h2 class="banner__title">An addition to all your fits</h2>
                        <button class="btn">Shop now</button>
                    </div>
                </div>
            </section>
            <section class="news">
                <div class="container">
                    <h2 class="news__title title_dashed">
                        Latest news
                    </h2>
                    <div class="news-swiper swiper">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper news-swiper__wrapper">
                            <!-- Slides -->
                            <?php 
                            $args = array('posts_per_page' => 6,);
                            $query = new WP_Query( $args );
                            // Цикл
                            if ( $query->have_posts() ) {
                                while ( $query->have_posts() ) {
                                    $query->the_post();
                                    ?>
                                    <div class="swiper-slide news__swiper-slide"> 
                                        <a href="<?php the_permalink()?>">
                                        <img src="<?php echo get_the_post_thumbnail_url()?>" class="news__img">
                                        <h3 class="news__name"><?php the_title() ?></h3>
                                        <p class="news__text"> <?php echo wp_trim_words(get_the_content(), 28)?></p>
                                        <a href="<?php the_permalink()?>" class="news__more">Read more</a>
                                        <p class="news__date"> <?php echo get_the_date('d/m/Y')?> Min Read</p>
                                        </a>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <!-- If we need pagination -->
                    </div>
                    <div class="navigation news__navigation">
                        <button class="nagination__prev-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/left_arr.png"></button>
                        <button class="nagination__next-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/right_arr.png"></button>
                    </div>
                </div>
            </section>
            <section class="fits">
                <div class="container">
                  <div class="fits__inner">
                        <div class="fits__content">
                            <h2 class="fits__title">Fits any style</h2>
                            <p class="fits__text">Precise fit on sliding buckle with no holes</p>
                            <button class="btn btn_white fits__btn">Shop now</button>
                        </div>
                        <div class="fits__img" style="background: url(<?php echo get_template_directory_uri()?>/assets/img/fits_1.png) center / cover no-repeat;">
                  </div>
                </div>
            </section>
            <section class="special">
                <div class="container">
                    <h2 class="special__title title_dashed">
                        Special products
                    </h2>
                    <p class="special__text">Register now to get updates on promotions </p>
                    <form action="#" class="special__form">
                        <input type="email" class="special__form-input special__form-input_email">
                        <input type="submit" class="special__form-input special__form-input_submit" value="subscribe">
                    </form>
                </div>
            </section>
        </main>

  <?php get_footer() ?>