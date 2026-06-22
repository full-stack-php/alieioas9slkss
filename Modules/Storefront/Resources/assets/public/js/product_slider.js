import { initSwiperModule } from './partials/swiperHelper.js';
export function initProductSlider() {
    const modules = document.querySelectorAll('.popular_product');

    modules.forEach(module => {
        const isInNoOwlContainer = module.closest('.position-no-owl') !== null;

        if (isInNoOwlContainer) {
            const wrappers = module.querySelectorAll('.swiper-wrapper');

            wrappers.forEach(wrapper => {
                const items = Array.from(wrapper.querySelectorAll(':scope > div.item'));
                items.forEach(item => {
                    const itemName = item.querySelector('.product-name');
                    const image = item.querySelector('.image');
                    if (itemName && image) {
                        item.insertBefore(itemName, image);
                    }
                });

                for (let i = 0; i < items.length; i += 2) {
                    // Создаем обертку
                    const rowWrapper = document.createElement('div');
                    rowWrapper.className = 'row_items swiper-slide';

                    wrapper.insertBefore(rowWrapper, items[i]);
                    rowWrapper.appendChild(items[i]);
                    items[i].classList.remove('swiper-slide');
                    if (items[i + 1]) {
                        rowWrapper.appendChild(items[i + 1]);
                        items[i + 1].classList.remove('swiper-slide');
                    }
                }
            });
        }

        const swiperMode = isInNoOwlContainer ? 1 : false;
        initSwiperModule(module, swiperMode);
    });
}

initProductSlider();
