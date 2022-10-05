
        <footer class="footer">
            <div class="container">
                <div class="footer__col">
                    <a href="<?php echo get_site_url()?>" class="footer__logo logo">
                        <img src="<?php echo carbon_get_theme_option('logo')?>">
                    </a>
                    <a href="<?php echo carbon_get_theme_option('insta_url')?>" class="footer__insta">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/insta.svg">
                        <?php echo carbon_get_theme_option('insta_name')?>
                    </a>
                    <a href="#" class="footer__copy-right"><span>&copy</span>2022 by Bruden Mode. Proudly created with</a>
                </div>
                <div class="footer__col">
                    <h3 class="footer__title">Shop</h3>
                    <ul class="footer__list">
                        <li class="footer__item">
                            <a href="#" class="footer__link">Bags</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Belts</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Sunglasses</a>
                        </li>
                    </ul>
                </div>  
                <div class="footer__col">
                    <h3 class="footer__title">Account</h3>
                    <ul class="footer__list">
                        <li class="footer__item">
                            <a href="#" class="footer__link">Orders</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Checkout</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">My Account</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Account Details
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer__col">
                    <h3 class="footer__title">Information</h3>
                    <ul class="footer__list">
                        <li class="footer__item">
                            <a href="#" class="footer__link">FAQ’s</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Contact Us</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Privacy Policy</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Return policy
                            </a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Shipping Policy
                            </a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Terms and Conditions
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer__col">
                    <h3 class="footer__title">Contacts</h3>
                    <ul class="footer__list">
                        <li class="footer__item">
                            <a href="mailto: email@mail.com" class="footer__link">email@mail.com</a>
                        </li>
                        <li class="footer__item">
                            <a href="tel: +1-000-000-0000" class="footer__link">+1-000-000-0000</a>
                        </li>
                        <li class="footer__item">
                            <a href="#" class="footer__link">Bld, Street, town, ZIP Code </a>
                        </li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
<?php wp_footer()?>
</body>
</html>