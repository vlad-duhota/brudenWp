const swiper2 = new Swiper('.category-swiper', {
    loop: true,
    slidesPerView: 3,
    spaceBetween: 40,
    speed: 600,
    navigation: {
        nextEl: '.category__navigation .nagination__next-btn',
        prevEl: '.category__navigation .nagination__prev-btn',
      },
      breakpoints: {
        320: {
            slidesPerView: 1,
        },
        578: {
            slidesPerView: 3,
        },
      },
}
);

const swiper3 = new Swiper('.news-swiper', {
    loop: true,
    slidesPerView: 3,
    spaceBetween: 40,
    speed: 600,
    navigation: {
        nextEl: '.news__navigation .nagination__next-btn',
        prevEl: '.news__navigation .nagination__prev-btn',
      },
      breakpoints: {
        320: {
            slidesPerView: 1,
        },
        871: {
            slidesPerView: 3,
        },
      },
}
);