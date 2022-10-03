const menuBtn = document.querySelector('.header__burger-btn');
const menu = document.querySelector('.nav');
const right = document.querySelector('.header__right');

menuBtn.addEventListener('click', function(){
    this.classList.toggle('header__burger-btn_active');
    menu.classList.toggle('nav_active');
    right.classList.toggle('header__right_active');
});

