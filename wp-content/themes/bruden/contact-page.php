<?php
/*
Template Name: Contact us page
*/
?>

<?php 
    $page_id = get_the_ID();
?>

<?php get_header(); ?>
  

<main class="main">
    <section class="contact">
        <div class="container">
            <h1 class="contact__title">Contact Us</h1>
            <div class="sidebar__container">
                <div class="contact__cols">
                    <div class="contact__col contact__col_1">
                        <h2 class="contact__col-title">Our contact</h2>
                        <ul class="contact__list">
                            <li class="contact__item">
                                <h3 class="contact__item-title">Address:</h3>
                                <a href="<?php echo carbon_get_theme_option('street_url')?>" class="contact__item-link"><?php echo carbon_get_theme_option('street')?></a>
                            </li>
                            <li class="contact__item">
                                <h3 class="contact__item-title">Phone numbers:</h3>
                                <a href="tel:<?php echo carbon_get_theme_option('phone_url')?>" class="contact__item-link"><?php echo carbon_get_theme_option('phone')?></a>
                            </li>
                            <li class="contact__item">
                                <h3 class="contact__item-title">Email:</h3>
                                <a href="mailto:<?php echo carbon_get_theme_option('email')?>" class="contact__item-link"><?php echo carbon_get_theme_option('email')?></a>
                            </li>
                        </ul>
                    </div>
                    <div class="contact__col contact__col_2">
                        <h2 class="contact__col-title">Leave a reply</h2>
                      <?php echo do_shortcode('[contact-form-7 id="71" title="Contact us"]')?>
                    </div>
                </div>
                <?php get_sidebar( 'aside' ); ?>
            </div>
        </div>
    </section>
</main>


<?php get_footer(); ?>
  