

<?php 
    $page_id = get_the_ID();
?>

<?php get_header(); ?>

    <main class="main">
        <section class="post">
            <div class="container">
                <h1 class="post__title">Blog</h1>
            <div class="sidebar__container">
            <div class="main-part">
                <img class="post__img" src=" <?php echo get_the_post_thumbnail_url()?>">
                <p class="post__info">
                    <span class="post__info-date"><?php echo get_the_date('d/m/Y')?></span>|
                    <span class="post__info-category">
                        <?php foreach(get_the_category() as $cat) : ?>
                        <?php echo $cat -> name?> | 
                        <?php endforeach; ?>
                    </span>
                </p>
                <h2 class="post__name"><?php the_title() ?></h2>
                <div class="post__content">
                    <?php the_content()?>
                </div>
            </div>
            <?php get_sidebar( 'aside' ); ?>
        </section>
        <section class="latest">
            <div class="container">
                <h2 class="latest__title title_dashed">Latest Posts</h2>
                <div class="latest-swiper swiper">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper latest-swiper__wrapper"><?php 
                            // $this_tag = $wp_query->queried_object->slug;
                            query_posts([
                                'post_type' => 'product',
                                'posts_per_page' => -1,
                                // 'product_tag' => 'deal-of-the-week',
                            ])
                        ?>
                        <?php if ( have_posts() ) :
                            while ( have_posts() ) :
                            the_post(); ?>
                                <div class="swiper-slide latest__swiper-slide">
                                    <a href="<?php the_permalink() ?>">
                                        <?php echo get_the_post_thumbnail( null, 'thumbnail_520x680' ) ?>
                                    </a>
                                    <div class="latest__content">
                                        <h4 class="latest__name"><?php the_title() ?></h4>
                                        <div class="latest__stars">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                            <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                        </div>
                                        <p class="latest__price">C$ 99.99</p>
                                    </div>
                                </div>
                            <?php endwhile;
                                wp_reset_query();
                            endif;
                        ?>
                    </div>
                    <!-- If we need pagination -->
                </div>
                <div class="navigation latest__navigation">
                    <button class="nagination__prev-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/left_arr.png"></button>
                    <button class="nagination__next-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/right_arr.png"></button>
                </div>
            </div>
        </section>
    </main>
<?php get_footer() ?>