
<?php get_header(); ?>

<main class="main">
    <section class="blog">
        <div class="container">
            <h1 class="blog__title">Blog</h1>
        <div class="sidebar__container">
                <div class="sidebar">
                <?php if ( is_active_sidebar( 'true_side' ) ) : ?>
         
         <div id="true-side" class="sidebar">
        
             <?php dynamic_sidebar( 'true_side' ); ?>
        
         </div>
        
        <?php endif; ?>
                </div>
        <div class="main-part">
                <ul class="blog__list">
                <?php 
                $args = [
                'post_type' => 'post',
            ]; 
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
        <?php if ( have_posts() ) {
        	while ( have_posts() ) {
        		the_post(); ?>
                    <li class="blog__item">
                        <a href="<?php the_permalink() ?>">
                        <img class="blog__item-img" src=" <?php echo get_the_post_thumbnail_url()?>">
                        </a>
                        <h4 class="blog__item-title"><?php the_title() ?></h4>
                        <p class="blog__item-date"><?php echo get_the_date('d/m/Y')?></p>
                        <p class="blog__item-content">   <?php echo wp_trim_words(get_the_content(), 28)?> </p>
                         <a href="<?php the_permalink() ?>" class="blog__item-more">Read more</a>
                    </li>                     
        	<?php } 
            }
            ?>
                </ul>
                <ul class="blog__paggination">
                    <?php siteDefPaging($query) ?>
                </ul>
        </div>
        </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>