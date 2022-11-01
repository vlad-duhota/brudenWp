
        <footer class="footer">
            <div class="container">
                <div class="footer__col">
                    <?php echo get_custom_logo(); ?>
                    <a href="<?php echo carbon_get_theme_option('insta_url')?>" class="footer__insta" target="_blank">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/insta.svg">
                        <?php echo carbon_get_theme_option('insta_name')?>
                    </a>
                    <p class="footer__copy-right"><span>&copy</span><?php the_time('Y') ?> Bruden Mode All rights reserved | Powered by <a href="http://biramedia.com" target="_blank">Biramedia.com</a></p>
                </div>
                <?php get_sidebar( 'foot' ); ?>
            </div>
        </footer>
    </div>

    <!-- <script 
        src="https://cdn2.woxo.tech/a.js#633e7143bedc8621852eaa7f" async data-usrc>
    </script> -->
<?php wp_footer()?>
</body>
</html>