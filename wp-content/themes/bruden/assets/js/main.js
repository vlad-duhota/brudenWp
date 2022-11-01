const swiper = new Swiper('.hero-swiper', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,
  
    // If we need pagination
    pagination: {
      el: '.hero-swiper__pagination',
      clickable: true,
    },
//     effect: 'fade',
//   fadeEffect: {
//     crossFade: true
 
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
const swiper5 = new Swiper('.latest-swiper', {
    loop: true,
    slidesPerView: 3,
    grid: {
      rows: 2,
    },
    spaceBetween: 40,
    speed: 600,
    navigation: {
      nextEl: '.latest__navigation .nagination__next-btn',
      prevEl: '.latest__navigation .nagination__prev-btn',
    },
    breakpoints: {
      320: {
          slidesPerView: 1,
      },
      1155: {
          slidesPerView: 3,
      },
    },
  }
);

// hide title on link hover
// $('a').on('mouseenter', function () {
//   var $link = $(this);
//       title = $link.attr('title');

//   $link.attr('title', '').data('title', title);

// console.log('off', title, $link.attr('title'), $link.data());
// });

// $('a').on('mouseleave', function () {
//   var $link = $(this);
//       title = $link.data('title');

//   $link.attr('title', title).data('title', '');

//   console.log('on', title, $link.attr('title'), $link.data());
// });