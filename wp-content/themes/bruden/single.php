

<?php 
    $page_id = get_the_ID();
?>

<?php get_header(); ?>

    <main class="main">
        <section class="post">
            <div class="container">
                <h1 class="post__title">Blog</h1>
                <img class="post__img" src=" <?php echo get_the_post_thumbnail_url()?>">
                <p class="post__info"><span class="post__info-date"><?php echo get_the_date('d/m/Y')?></span>|<span class="post__info-category">
                    <?php foreach(get_the_category() as $cat) : ?>
                    <?php echo $cat -> name?> | 
                    <?php endforeach; ?>
                </span></p>
                <h2 class="post__name"><?php the_title() ?></h2>
                <div class="post__content">
                    <?php the_content()?>
                </div>
            </div>
        </section>
    </main>
<?php get_footer() ?>