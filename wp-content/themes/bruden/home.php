
<?php get_header(); ?>

<main class="main">
    <section class="blog">
        <div class="container">
            <h1 class="blog__title">Blog</h1>
            <div class="sidebar__container">
                <?php get_sidebar( 'aside' ); ?>
                <div class="main-part">
                    <ul class="blog__list">
                        <?php $args = [ 'post_type' => 'post', ]; 
                        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                        $query = new WP_Query( array(
                            'paged' => $paged,
                            'posts_per_page' => 4
                            ) );
                        ?>
                        <?php query_posts([
                            'post_type' => 'post', 
                            'paged' => $paged,
                            'posts_per_page' => 4
                            ]) 
                        ?>
                    <?php if ( have_posts() ) :
                        while ( have_posts() ) :
                            the_post(); ?>
                                <li class="blog__item">
                                    <a href="<?php the_permalink() ?>">
                                        <img class="blog__item-img" src=" <?php echo get_the_post_thumbnail_url()?>">
                                    </a>
                                    <a href="<?php the_permalink() ?>" class="blog__item-title">
                                        <h4><?php the_title() ?></h4>
                                    </a>
                                    <p class="blog__item-date"><?php echo get_the_date('d/m/Y')?></p>
                                    <p class="blog__item-content">   <?php echo wp_trim_words(get_the_content(), 28)?> </p>
                                    <a href="<?php the_permalink() ?>" class="blog__item-more">Read more</a>
                                </li>                     
                        <?php endwhile;
                        endif;
                        ?>
                    </ul>
                    <ul class="blog__paggination">
                        <?php siteDefPaging($query) ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="insta">
        <div class="container">
            <h2 class="special__title title_dashed">Insta-gallery</h2>
            <div data-mc-src="da54c84f-049d-4c54-b03a-28fd6224fcbe#instagram"></div>
        </div>
        <div style="height: 50px; margin-top: -50px; background: #fff; position: relative; z-index: 3;"></div>
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
<script 
  src="https://cdn2.woxo.tech/a.js#633e7143bedc8621852eaa7f" 
  async data-usrc>
</script>
<?php get_footer(); ?>