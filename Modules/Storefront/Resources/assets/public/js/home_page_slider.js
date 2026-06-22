function initHomePageSlider() {
    const megaSliderEl = document.querySelector('#mslider0');

    if (megaSliderEl) {
        const megaContainer = megaSliderEl.closest('.megasliderpro');

        const ms_next = megaContainer?.querySelector('.megasliderpro__arrow_next');
        const ms_prev = megaContainer?.querySelector('.megasliderpro__arrow_prev');
        const ms_pagination = megaContainer?.querySelector('.swiper-ms-pagination');

        new Swiper(megaSliderEl, {
            watchSlidesProgress: true,
            watchOverflow: true,
            observer: true,
            observeParents: true,
            slidesPerView: 1,
            effect: 'fade',
            parallax: true,
            loop: true,
            autoplay: {
                delay: 12000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: (ms_next && ms_prev) ? {
                nextEl: ms_next,
                prevEl: ms_prev,
                navigationDisabledClass: 'swiper-navigation-disabled',
            } : false,
            pagination: ms_pagination ? {
                el: ms_pagination,
                type: 'bullets',
                clickable: false,
            } : false,
            speed: 300,
        });
    }

    const smallSliderEl = document.querySelector('.small-slider .small_slider_swiper_0');

    if (smallSliderEl) {
        const smallContainer = smallSliderEl.closest('.small-slider');

        const card_prev = smallContainer?.querySelector('.small-slider__arrow_prev');
        const card_next = smallContainer?.querySelector('.small-slider__arrow_next');
        const card_pagination = smallContainer?.querySelector('.small-slider__pagination');

        const dataset = smallSliderEl.dataset;
        const delay = parseInt(dataset.delay) || 12000;

        const hasAutoplay = dataset.autoplay !== 'false';
        const hasPagination = dataset.pagination !== 'false';
        const hasNavigation = dataset.navigation !== 'false';

        new Swiper(smallSliderEl, {
            watchSlidesProgress: true,
            watchOverflow: true,
            observer: true,
            observeParents: true,
            slidesPerView: 1,
            effect: 'fade',
            parallax: true,
            loop: true,
            fadeEffect: {
                crossFade: true
            },
            autoplay: hasAutoplay ? {
                delay: delay,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            } : false,
            navigation: (hasNavigation && card_next && card_prev) ? {
                nextEl: card_next,
                prevEl: card_prev,
            } : false,
            pagination: (hasPagination && card_pagination) ? {
                el: card_pagination,
                type: 'bullets',
                clickable: false,
            } : false,
            speed: 500,
        });
    }
}
initHomePageSlider();
