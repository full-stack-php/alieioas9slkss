export function initSwiperModule(selector, row_items = false) {
    const swiperElement = typeof selector === 'string' ? document.querySelector(selector) : selector;
    if (!swiperElement) return;

    const containerModule = swiperElement.closest('.container-module');
    if (!containerModule) return;

    let bpoint = 2;
    let bpoint_2 = 2;
    let bpoint_3 = 3;

    if (row_items == 1) {
        bpoint = 1;
        bpoint_2 = 1;
        bpoint_3 = 2;
    }
    if (window.innerWidth > 768) {
        bpoint = 1;
    }
    if (window.innerWidth > 1200) {
        bpoint_3 = 2;
    }

    const nx = containerModule.querySelector('.next-prod');
    const pr = containerModule.querySelector('.prev-prod');
    const sb = swiperElement.querySelector('.swiper-scrollbar');
    const navigation = containerModule.querySelector('.swiper-mod-navigation');
    const updateNavigation = () => {
        if (!navigation) return;
        const lockedButtons = containerModule.querySelectorAll('.swiper-mod-navigation .swiper-button-lock');

        if (lockedButtons.length === 2) {
            navigation.classList.add('disabled-navigation');
        } else {
            navigation.classList.remove('disabled-navigation');
        }
    };

    new Swiper(swiperElement, {
        watchSlidesProgress: true,
        watchOverflow: true,
        observer: true,
        observeParents: true,
        slidesPerView: 1,
        nested: true,
        speed: 400,
        breakpointsBase: 'container',
        grabCursor: true,

        scrollbar: sb ? { el: sb, draggable: true } : false,
        navigation: (nx && pr) ? { nextEl: nx, prevEl: pr } : false,

        on: {
            afterInit: function () {
                setTimeout(() => {
                    swiperElement.classList.add('swiper-visible');
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
            350: { slidesPerView: bpoint },
            500: { slidesPerView: bpoint_2 },
            710: { slidesPerView: bpoint_3 },
            992: { slidesPerView: 4 },
            1220: { slidesPerView: 5 }
        }
    });
}
