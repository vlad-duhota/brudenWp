
        <footer class="footer">
            <div class="container">
                <div class="footer__col">
                    <a href="<?php echo get_site_url()?>" class="footer__logo logo">
                        <img src="<?php echo carbon_get_theme_option('logo')?>">
                    </a>
                    <a href="<?php echo carbon_get_theme_option('insta_url')?>" class="footer__insta" target="_blank">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/insta.svg">
                        <?php echo carbon_get_theme_option('insta_name')?>
                    </a>
                    <p class="footer__copy-right"><span>&copy</span>2022 by Bruden Mode. Proudly created with</p>
                </div>
                <?php get_sidebar( 'foot' ); ?>
            </div>
        </footer>
    </div>
    <!-- <script 
  src="https://cdn2.woxo.tech/a.js#633e7143bedc8621852eaa7f" 
  async data-usrc>
</script> -->
<?php wp_footer()?>
</body>
</html>