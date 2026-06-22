/**
 * Класс для управления слайдером комплектов (Bundles)
 * Файл: resources/js/BundleSliderManager.js
 */
export class BundleSliderManager {
    constructor(sliderElement) {
        // Оборачиваем переданный DOM-элемент в jQuery
        this.$slider = $(sliderElement);

        // Находим родительский контейнер, чтобы искать элементы только внутри текущего блока
        this.$container = this.$slider.closest('.container-module');

        // Находим кнопки и элементы навигации
        this.$navigation = this.$container.find('.swiper-mod-navigation');
        this.$nextBtn = this.$container.find('.next-prod')[0];
        this.$prevBtn = this.$container.find('.prev-prod')[0];

        // Считаем количество слайдов
        this.slideCount = this.$slider.find('.swiper-slide').length;

        // Переменная для хранения инстанса Swiper
        this.swiper = null;

        // Запускаем инициализацию
        this.init();
    }

    init() {
        // Создаем слайдер. Используем стрелочные функции () => {},
        // чтобы внутри событий (on) this ссылался на наш класс, а не на сам Swiper
        this.swiper = new Swiper(this.$slider[0], {
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
            watchOverflow: true,
            observer: true,
            observeParents: true,
            slidesPerView: 1,
            spaceBetween: 20,
            nested: true,
            speed: 400,
            breakpointsBase: 'container',
            grabCursor: true,
            navigation: {
                nextEl: this.$nextBtn,
                prevEl: this.$prevBtn,
            },
            breakpoints: {
                800: {
                    slidesPerView: 2,
                },
                1200: {
                    // Используем подсчитанное количество слайдов
                    slidesPerView: this.slideCount > 2 ? 2.5 : 2,
                },
            },
            on: {
                afterInit: () => {
                    setTimeout(() => {
                        this.$slider.addClass('swiper-visible');
                    }, 500);
                    this.updateNavigation();
                },
                resize: () => {
                    setTimeout(() => {
                        this.updateNavigation();
                    }, 300);
                },
                sliderMove: () => {
                    this.$slider.find('.swiper-slide-visible').addClass('disabled-swiper-bg');
                },
                touchEnd: () => {
                    setTimeout(() => {
                        this.$slider.find('.swiper-slide-visible').removeClass('disabled-swiper-bg');
                    }, 100);
                },
                slideChangeTransitionStart: () => {
                    this.$slider.find('.swiper-slide-visible').addClass('disabled-swiper-bg');
                },
                slideChangeTransitionEnd: () => {
                    this.$slider.find('.swiper-slide-visible').removeClass('disabled-swiper-bg');
                }
            }
        });
    }

    /**
     * Обновление состояния видимости кнопок навигации
     */
    updateNavigation() {
        // Ищем заблокированные кнопки внутри текущего контейнера
        const lockedButtons = this.$container.find('.swiper-mod-navigation .swiper-button-lock');

        if (lockedButtons.length === 2) {
            this.$navigation.addClass('disabled-navigation');
        } else {
            this.$navigation.removeClass('disabled-navigation');
        }
    }
}
