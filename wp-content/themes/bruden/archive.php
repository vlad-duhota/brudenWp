<?php get_header(); ?>

<main>
    <div class="container">
    <h2 class="archive__title">Проекти</h2>
    <ul class="archive__list">
    <?php while ( have_posts() ) : ?>
        <?php the_post()?>
        <li class="archive__list-item">
        <div class="card">
            <h3 class="card__title">
                <a href="<?php echo the_permalink() ?>"><?php echo the_title() ?><a>
            </h3>
            <time class="card__date" datetime="<?php echo get_the_time('d.m.Y')?>"><?php echo get_the_time('d.m.Y')?></time>
            <p class="card__content">
            <img src="<?php echo carbon_get_post_meta($post->ID, 'project_mini_img')?>">
                <?php echo carbon_get_post_meta($post->ID, 'project_desc')?>
            </p>
            <a href="<?php echo the_permalink() ?>" class="card__more">
                Дізнатись більше
                <img src="<?php echo get_template_directory_uri()?>/assets/img/right-arrow.png">
            </a>
        </div>
        </li>
    <?php endwhile ?>
    </ul>
    </div>
</main>
<?php get_footer(); ?>
