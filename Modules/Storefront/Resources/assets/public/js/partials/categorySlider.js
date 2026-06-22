export function initCategorySlide() {
    const swiperContainers = document.querySelectorAll('.swiper-sub-category');

    if (swiperContainers.length === 0) {
        console.warn('Элемент с классом .swiper-sub-category не найден в разметке.');
        return;
    }

    swiperContainers.forEach(container => {
        const sc_prev = container.querySelector('.swiper-sub-category__arrow_prev');
        const sc_next = container.querySelector('.swiper-sub-category__arrow_next');
        const navWrapper = container.closest('.category-module-wrapper')?.querySelector('.swiper-sub-category__navigation')
            || document.querySelector('.swiper-sub-category__navigation');
        const updateNavigation = () => {
            if (!navWrapper) return;

            const lockedButtons = navWrapper.querySelectorAll('.swiper-button-lock');
            if (lockedButtons.length === 2) {
                navWrapper.classList.add('disabled-navigation');
            } else {
                navWrapper.classList.remove('disabled-navigation');
            }
        };

        new Swiper(container, {
            slidesPerView: 'auto',
            watchSlidesProgress: true,
            watchOverflow: true,
            observer: true,
            observeParents: true,
            nested: true,
            speed: 400,
            breakpointsBase: 'container',
            grabCursor: true,
            navigation: (sc_next && sc_prev) ? {
                nextEl: sc_next,
                prevEl: sc_prev,
            } : false,

            on: {
                afterInit: function () {
                    setTimeout(() => {
                        container.classList.add('swiper-visible');
                    }, 500);
                    updateNavigation();
                },
                resize: function () {
                    setTimeout(() => {
                        updateNavigation();
                    }, 300);
                }
            },
            breakpoints: {
                200: { slidesPerView: 2 },
                500: { slidesPerView: 3 },
                768: { slidesPerView: 4 },
                992: { slidesPerView: 5 },
                1150: { slidesPerView: 6 },
                1350: { slidesPerView: 8 },
            },
        });
    });
}
