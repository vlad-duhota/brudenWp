<?php
/*
Template Name: Home page
*/
?>

<?php 
    $page_id = get_the_ID();
    $slider = carbon_get_theme_option('hero_slider');
?>

<?php get_header(); ?>
  
        <main class="main">
            <section class="hero">
              <div class="container">
                <div class="hero-swiper swiper">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper hero-swiper__wrapper">
                      <!-- Slides -->

                      <?php if(! empty($slider)) : ?>
            <?php foreach($slider as $slide) : ?>
                <div class="swiper-slide hero__swiper-slide"><div class="hero__swiper-item">     
                        <div class="hero__content">
                        <?php if(!empty($slide['hero_slider_tags'])) : ?>
                            <ul class="hero__cats">
                                <?php foreach($slide['hero_slider_tags'] as $tag) : ?>
                                    <li class="hero__cats-item"><?php echo $tag['hero_slider_tag']?></li>
                                <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                        <h1 class="hero__title"><?php echo $slide['hero_slider_title']?></h1>
                        <p class="hero__text"><?php echo $slide['hero_slider_text']?></p>
                        <button class="btn hero__btn">Shop now</button>
                </div>
                    <?php echo wp_get_attachment_image($slide['hero_slider_img'], 'full')?>
                    <!-- <img class="hero__img" src="<?php echo get_template_directory_uri() ?>/assets/img/hero_1.png"> -->
                </div></div> 
            <?php endforeach ?>
            <?php endif ?>
                    <!-- If we need pagination -->
                </div>
                </div>
                <div class="hero-swiper__pagination"></div>
              </div>
            </section>
            <section class="category">
                <div class="container">
                    <h2 class="category__title title_dashed">
                        <?php echo carbon_get_theme_option('title_1')?>
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
                            
                                if ($woo_cat_name !== "Misc" && $woo_cat_name !== "Deal of the week") : ?>
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
                <div class="container">
                    <h2 class="additional__title">    <?php echo carbon_get_theme_option('banner_1_title')?></h2>
                    <a href="<?php echo site_url()?>/shop" class="btn btn_white additional__btn">Shop now</a>
                    <?php echo wp_get_attachment_image( carbon_get_theme_option('underlay'), 'full' ) ?>
                </div>
            </section>
            <section class="deal">
                <div class="container">
                    <h2 class="deal__title title_dashed">
                    <?php echo carbon_get_theme_option('title_2')?>
                    </h2>
                    <div class="deal-swiper swiper">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper deal-swiper__wrapper"><?php 
                                $this_tag = $wp_query->queried_object->slug;
                                query_posts([
                                    'post_type' => 'product',
                                    'posts_per_page' => -1,
                                    'product_tag' => 'deal-of-the-week',
                                ])
                            ?>
                            <?php if ( have_posts() ) :
                                while ( have_posts() ) :
                                    the_post(); ?>
                                        <div class="swiper-slide deal__swiper-slide">
                                            <a href="<?php the_permalink() ?>">
                                                <div class="deal__img">
                                                    <?php echo get_the_post_thumbnail( null, 'thumbnail_520x680' ) ?>
                                                </div>
                                                <div class="deal__content">
                                                    <h4 class="deal__name"><?php the_title() ?></h4>
                                                    <div class="deal__stars">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                                    </div>
                                                    <?php
                                                        $price = get_post_meta( get_the_ID(), '_price', true);
                                                        if( $price ) :
                                                    ?>
                                                    <p class="deal__price">C$<?php echo $price ?></p>
                                                    <?php endif; ?>
                                                    <p class="deal__text">Bruden's Backpack will give all your essentials a home while still feeling comfortable and having a... </p>
                                                    <div class="deal__cols"><?php
                                                    /**
                                                        * Hook: woocommerce_after_shop_loop_item.
                                                        *
                                                        * @hooked woocommerce_template_loop_product_link_close - 5
                                                        * @hooked woocommerce_template_loop_add_to_cart - 10
                                                        */
                                                        do_action( 'woocommerce_after_shop_loop_item' ); ?>
                                                        <a href="<?php echo site_url('wishlist') ?>" class="deal__heart">
                                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/heart.svg">
                                                        </a>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                <?php endwhile;
                                    wp_reset_query();
                                endif;
                            ?>
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
                    <?php
                        $banner_l = wp_get_attachment_url( carbon_get_theme_option('banner_2_img_1'), 'full' );
                    ?>
                     <?php
                        $banner_2 = wp_get_attachment_url( carbon_get_theme_option('banner_2_img_2'), 'full' );
                    ?>
                    <div class="banner__left banner__part" style="background: url(<?php echo $banner_l ?>) center / cover no-repeat;">
                        <h2 class="banner__title"><?php echo carbon_get_theme_option('banner_2_title_1')?> <span><?php echo carbon_get_theme_option('banner_2_text_1')?></span></h2>
                        <a href="<?php echo site_url()?>/shop" class="btn">Shop now</a>
                    </div>
                    <div class="banner__right banner__part" style="background: url(<?php echo $banner_2 ?>) center / cover no-repeat;">
                        <h2 class="banner__title"><?php echo carbon_get_theme_option('banner_2_title_2')?></h2>
                        <a href="<?php echo site_url()?>/shop" class="btn">Shop now</a>
                    </div>
                </div>
            </section>
            <section class="news">
                <div class="container">
                    <h2 class="news__title title_dashed">
                        <?php echo carbon_get_theme_option('title_3')?>
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
                  <?php
                        $fits_img = wp_get_attachment_url( carbon_get_theme_option('fits_img'), 'full' );
                    ?>
                        <div class="fits__content">
                            <h2 class="fits__title"><?php echo carbon_get_theme_option('fits_title')?></h2>
                            <p class="fits__text"><?php echo carbon_get_theme_option('fits_text')?></p>
                            <a href="<?php echo site_url()?>/shop" class="btn btn_white fits__btn">Shop now</a>
                        </div>
                        <div class="fits__img" style="background: url(<?php echo $fits_img?>) center / cover no-repeat;">
                  </div>
                </div>
            </section>
            <section class="special">
                <div class="container">
                    <h2 class="special__title title_dashed">
                        <?php echo carbon_get_theme_option('title_4')?>
                    </h2>
                    <p class="special__text">Register now to get updates on promotions </p>
                    <?php echo do_shortcode('[contact-form-7 id="69" title="Email"]')?>
                </div>
            </section>
        </main>

  <?php get_footer() ?>