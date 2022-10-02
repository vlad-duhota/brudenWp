<?php
/*
Template Name: Home page
*/
?>

<?php 
    $page_id = get_the_ID();
?>

<?php get_header(); ?>
  
<main class="main">
            <section class="hero">
              <div class="container">
                <div class="hero-swiper swiper">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper hero-swiper__wrapper">
                      <!-- Slides -->
                      <div class="swiper-slide hero__swiper-slide"><div class="hero__swiper-item">     
                        <div class="hero__content">
                        <ul class="hero__cats">
                            <li class="hero__cats-item">bags</li>
                            <li class="hero__cats-item">sunglasses</li>
                            <li class="hero__cats-item">belts</li>
                        </ul>
                        <h1 class="hero__title">Exclusive accessories <br> for connoisseurs of style</h1>
                        <p class="hero__text">Designed in Montreal, Bruden's Backpack embodies luxury fashion while keeping comfort and versatility</p>
                        <button class="btn hero__btn">Shop now</button>
                </div>
                    <img class="hero__img" src="<?php echo get_template_directory_uri() ?>/assets/img/hero_1.png"></div></div>
                    <div class="swiper-slide hero__swiper-slide"><div class="hero__swiper-item">     
                        <div class="hero__content">
                        <ul class="hero__cats">
                            <li class="hero__cats-item">bags</li>
                            <li class="hero__cats-item">sunglasses</li>
                            <li class="hero__cats-item">belts</li>
                        </ul>
                        <h1 class="hero__title">Exclusive accessories <br> for connoisseurs of style</h1>
                        <p class="hero__text">Designed in Montreal, Bruden's Backpack embodies luxury fashion while keeping comfort and versatility</p>
                        <button class="btn hero__btn">Shop now</button>
                </div>
                    <img class="hero__img" src="<?php echo get_template_directory_uri() ?>/assets/img/hero_2.png"></div></div>
                    <div class="swiper-slide hero__swiper-slide"><div class="hero__swiper-item">     
                        <div class="hero__content">
                        <ul class="hero__cats">
                            <li class="hero__cats-item">bags</li>
                            <li class="hero__cats-item">sunglasses</li>
                            <li class="hero__cats-item">belts</li>
                        </ul>
                        <h1 class="hero__title">Exclusive accessories <br> for connoisseurs of style</h1>
                        <p class="hero__text">Designed in Montreal, Bruden's Backpack embodies luxury fashion while keeping comfort and versatility</p>
                        <button class="btn hero__btn">Shop now</button>
                </div>
                    <img class="hero__img" src="<?php echo get_template_directory_uri() ?>/assets/img/hero_3.png"></div></div>
                    </div>
                    <!-- If we need pagination -->
                </div>
                <div class="hero-swiper__pagination"></div>
              </div>
            </section>
            <section class="category">
                <div class="container">
                    <h2 class="category__title title_dashed">
                        Shop by category
                    </h2>
                    <div class="category-swiper swiper">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper category-swiper__wrapper">
                          <!-- Slides -->
                          <div class="swiper-slide category__swiper-slide"> 
                            <a href="#">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/category_1.png" class="category__img">
                                <h3 class="category__name">Bags</h3>
                            </a>
                        </div>
                        <div class="swiper-slide category__swiper-slide"> 
                            <a href="#">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/category_2.png" class="category__img">
                                <h3 class="category__name">Sunglasses</h3>
                            </a>
                        </div>
                        <div class="swiper-slide category__swiper-slide"> 
                            <a href="#">
                                <img src="<?php echo get_template_directory_uri() ?>/assets/img/category_3.png" class="category__img">
                                <h3 class="category__name">Belts</h3>
                            </a>
                        </div>  
                        </div>
                        <!-- If we need pagination -->
                    </div>
                    <div class="navigation category__navigation">
                        <button class="nagination__prev-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/left_arr.png"></button>
                        <button class="nagination__next-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/right_arr.png"></button>
                    </div>
                </div>
            </section>
            <section class="additional">
                <div class="container" style="background: url(<?php echo get_template_directory_uri()?>/assets/img/additional.png) center/cover no-repeat;">
                    <h2 class="additional__title">An addition to all your fits</h2>
                    <button class="btn btn_white additional__btn">Shop now</button>
                </div>
            </section>
            <section class="deal">
                <div class="container">
                    <h2 class="deal__title title_dashed">
                        Deal of the week
                    </h2>
                    <div class="deal-swiper swiper">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper deal-swiper__wrapper">
                          <!-- Slides -->
                          <div class="swiper-slide deal__swiper-slide"> 
                            <img class="deal__img" src="<?php echo get_template_directory_uri() ?>/assets/img/deal_1.png">
                            <div class="deal__content">
                                <h4 class="deal__name">Fiery Red Bruden Backpack</h4>
                                <div class="deal__stars">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                    <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                </div>
                                <p class="deal__price">C$ 99.99</p>
                                <p class="deal__text">Bruden's Backpack will give all your essentials a home while still feeling comfortable and having a... </p>
                                <div class="deal__cols">
                                    <button class="deal__btn">Add to cart</button>
                                    <button class="deal__heart">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/heart.svg">
                                    </button>
                                    <button class="deal__seen">
                                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/eye.svg">
                                    </button>
                                </div>
                            </div>
                        </div>
                            <!-- Slides -->
                            <div class="swiper-slide deal__swiper-slide"> 
                              <img class="deal__img" src="<?php echo get_template_directory_uri() ?>/assets/img/deal_2.png">
                              <div class="deal__content">
                                  <h4 class="deal__name">Bruden's Backpack</h4>
                                  <div class="deal__stars">
                                      <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                      <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                      <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                      <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                      <img src="<?php echo get_template_directory_uri() ?>/assets/img/star.png">
                                  </div>
                                  <p class="deal__price">C$ 66.49</p>
                                  <p class="deal__text">Bruden's Backpack will give all your essentials a home while still feeling comfortable and having a... </p>
                                  <div class="deal__cols">
                                      <button class="deal__btn">Add to cart</button>
                                      <button class="deal__heart">
                                          <img src="<?php echo get_template_directory_uri() ?>/assets/img/heart.svg">
                                      </button>
                                      <button class="deal__seen">
                                          <img src="<?php echo get_template_directory_uri() ?>/assets/img/eye.svg">
                                      </button>
                                  </div>
                              </div>
                    </div>
                        <!-- If we need pagination -->
                    </div>
                    <div class="navigation deal__navigation">
                        <button class="nagination__prev-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/left_arr.png"></button>
                        <button class="nagination__next-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/right_arr.png"></button>
                    </div>
                </div>
            </section>
            <section class="banner">
                <div class="container">
                    <div class="banner__left banner__part" style="background: url(<?php echo get_template_directory_uri()?>/assets/img/banner_1.png) center / cover no-repeat;">
                        <h2 class="banner__title">New arrival <span>Exclusive Denisha Bruce Red Backpack Launch</span></h2>
                        <button class="btn">Shop now</button>
                    </div>
                    <div class="banner__right banner__part" style="background: url(<?php echo get_template_directory_uri()?>/assets/img/banner_2.png) center / cover no-repeat;">
                        <h2 class="banner__title">An addition to all your fits</h2>
                        <button class="btn">Shop now</button>
                    </div>
                </div>
            </section>
            <section class="news">
                <div class="container">
                    <h2 class="news__title title_dashed">
                        Latest news
                    </h2>
                    <div class="news-swiper swiper">
                        <!-- Additional required wrapper -->
                        <div class="swiper-wrapper news-swiper__wrapper">
                          <!-- Slides -->
                          <div class="swiper-slide news__swiper-slide"> 
                            <a href="#">
                             <img src="<?php echo get_template_directory_uri() ?>/assets/img/news_1.png" class="news__img">
                             <h3 class="news__name">Bruden Mode Is Taking Care of Women</h3>
                             <p class="news__text">Bruden Mode in Montreal has taken a lot of time and put a lot of effort into helping women not only look great but to be more healthy.</p>
                             <a href="#" class="news__more">Read More</a>
                             <p class="news__date">April 3 - 2 Min Read</p>
                            </a>
                        </div>
                        <div class="swiper-slide news__swiper-slide"> 
                            <a href="#">
                             <img src="<?php echo get_template_directory_uri() ?>/assets/img/news_2.png" class="news__img">
                             <h3 class="news__name">Bruden Mode Accessories</h3>
                             <p class="news__text">Finding the perfect little thing that compliments your wardrobe can give you the confidence and the feeling you love to be your best.</p>
                             <a href="#" class="news__more">Read More</a>
                             <p class="news__date">April 3 - 2 Min Read</p>
                            </a>
                        </div>
                        <div class="swiper-slide news__swiper-slide"> 
                            <a href="#">
                             <img src="<?php echo get_template_directory_uri() ?>/assets/img/news_3.png" class="news__img">
                             <h3 class="news__name">Different Types of Backpacks</h3>
                             <p class="news__text">Different backpacks do different things and serve various purposes.
                            </p>
                             <a href="#" class="news__more">Read More</a>
                             <p class="news__date">April 3 - 2 Min Read</p>
                            </a>
                        </div>
                        </div>
                        <!-- If we need pagination -->
                    </div>
                    <div class="navigation news__navigation">
                        <button class="nagination__prev-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/left_arr.png"></button>
                        <button class="nagination__next-btn"><img src="<?php echo get_template_directory_uri() ?>/assets/img/right_arr.png"></button>
                    </div>
                </div>
                </div>
            </section>
            <section class="fits">
                <div class="container">
                  <div class="fits__inner">
                        <div class="fits__content">
                            <h2 class="fits__title">Fits any style</h2>
                            <p class="fits__text">Precise fit on sliding buckle with no holes</p>
                            <button class="btn btn_white fits__btn">Shop now</button>
                        </div>
                        <div class="fits__img" style="background: url(<?php echo get_template_directory_uri()?>/assets/img/fits_1.png) center / cover no-repeat;">
                  </div>
                </div>
            </section>
            <section class="special">
                <div class="container">
                    <h2 class="special__title title_dashed">
                        Special products
                    </h2>
                    <p class="special__text">Register now to get updates on promotions </p>
                    <form action="#" class="special__form">
                        <input type="email" class="special__form-input special__form-input_email">
                        <input type="submit" class="special__form-input special__form-input_submit" value="subscribe">
                    </form>
                </div>
            </section>
        </main>

  <?php get_footer() ?>