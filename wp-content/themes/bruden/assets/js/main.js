const swiper = new Swiper('.hero-swiper', {
    // Optional parameters
    direction: 'vertical',
    loop: true,
  
    // If we need pagination
    pagination: {
      el: '.hero-swiper__pagination',
      clickable: true,
    },
//     effect: 'fade',
//   fadeEffect: {
//     crossFade: true
  
  breakpoints: {
    320: {
        direction: 'horizontal',
      },
      992: {
        direction: 'vertical',
      }
    },
    effect: 'fade',
    fadeEffect: {
      crossFade: true
    },
    speed: 1000,
    autoplay: {
      delay: 6500,
    },
}
);

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
const swiper4 = new Swiper('.deal-swiper', {
    loop: true,
    slidesPerView: 2,
    spaceBetween: 60,
    speed: 600,
    navigation: {
        nextEl: '.deal__navigation .nagination__next-btn',
        prevEl: '.deal__navigation .nagination__prev-btn',
      },
      breakpoints: {
        320: {
            slidesPerView: 1,
        },
        1155: {
            slidesPerView: 2,
        },
      },
      
}
);