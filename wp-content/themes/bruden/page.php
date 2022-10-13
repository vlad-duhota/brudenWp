<?php get_header(); ?>
    <main>
        <div class="container" style="padding-top: 90px; padding-bottom: 90px">
            <?php get_sidebar( 'aside' ); ?>
            <div class="content-wrapper">
                <h1><?php the_title()?></h1>
                <?php the_content()?>
            </div>
        </div>
    </main>
<?php get_footer(); ?>