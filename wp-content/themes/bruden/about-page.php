<?php
/*
Template Name: About us page
*/
?>

<?php 
    $page_id = get_the_ID();
    $advatagesList = carbon_get_post_meta($page_id, 'advantages_list');
?>

<?php get_header(); ?>

<main class="main">
    <section class="about" style="padding-top: 90px">
        <div class="container">
            <h1 class="page__title"><?php the_title()?></h1>
            <div class="about__cols">
                <div class="about__content">
                    <p class="about__uptitle"><?php echo carbon_get_post_meta($page_id, 'about_uptitle')?></p>
                    <h2 class="about__title"><?php echo carbon_get_post_meta($page_id, 'about_title')?></h2>
                    <p class="about__text about__text_1">
                    <?php echo carbon_get_post_meta($page_id, 'about_text_1')?>
                    </p>
                    <p class="about__text about__text_2">
                    <?php echo carbon_get_post_meta($page_id, 'about_text_2')?>
                    </p>
                </div>
                <div class="about__img">
                    <?php echo wp_get_attachment_image( carbon_get_post_meta($page_id, 'about_img'), 'full' ) ?>
                </div>
            </div>
        </div>
    </section>
    <section class="advantages">
        <div class="container">

        <?php if(! empty($advatagesList)) : ?>
            <ul class="advantages__list">
            <?php foreach($advatagesList as $advantagesItem) : ?>
                <li class="advantages__item">
                    <h3 class="advantages__title"><?php echo $advantagesItem['advantages_list_title']?></h3>
                    <p class="advantages__text"><?php echo $advantagesItem['advantages_list_text']?></p>
                </li>
            <?php endforeach ?>
            </ul>
            <?php endif ?>
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