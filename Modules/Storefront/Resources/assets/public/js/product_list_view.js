import { setCookieView } from './partials/cookies.js';

export function initProductListView() {
    // Находим кнопки переключения
    const listViewBtn = document.getElementById('list-view');
    const gridViewBtn = document.getElementById('grid-view');
    const priceViewBtn = document.getElementById('price-view');

    // Функция для запуска стороннего скрипта стикеров
    const triggerStickers = () => {
        // Заменили строку setTimeout('...', 1500) на безопасный вызов
        if (typeof window.ProStickerLoad === 'function') {
            setTimeout(window.ProStickerLoad, 1500);
        }
    };

    // Объект с методами отображения (аналог вашего displayView)
    const displayView = {
        init: function() {
            const currentDisplay = localStorage.getItem('display');

            if (currentDisplay === 'list') {
                this.list_view();
            } else if (currentDisplay === 'price') {
                this.price_view();
            } else {
                this.grid_view();
            }

            // Вешаем обработчики кликов (используем опциональную цепочку ?.,
            // чтобы избежать ошибок, если на странице нет этих кнопок)
            listViewBtn?.addEventListener('click', () => {
                this.list_view();
                localStorage.setItem('display_old', localStorage.getItem('display'));
                triggerStickers();
            });

            gridViewBtn?.addEventListener('click', () => {
                this.grid_view();
                localStorage.setItem('display_old', localStorage.getItem('display'));
                triggerStickers();
            });

            priceViewBtn?.addEventListener('click', () => {
                this.price_view();
                localStorage.setItem('display_old', localStorage.getItem('display'));
                triggerStickers();
            });

            // Запускаем слушатель ресайза
            window.addEventListener('resize', () => {
                // Используем таймер (debounce) для оптимизации
                clearTimeout(this.resizeTimer);
                this.resizeTimer = setTimeout(() => this.resize(), 150);
            });
        },

        list_view: function() {
            document.querySelectorAll('#content .product-layout').forEach(el => {
                el.className = 'product-layout product-list col-xs-12';
            });

            listViewBtn?.classList.add('active');
            gridViewBtn?.classList.remove('active');
            priceViewBtn?.classList.remove('active');

            localStorage.setItem('display', 'list');
            setCookieView('display', 'list', 365); // <-- Используем импортированную функцию!
        },

        grid_view: function() {
            const cols = document.querySelectorAll('#column-left').length;
            let gridClass = 'product-layout product-grid ';

            if (cols === 2) {
                gridClass += 'col-6 col-sm-6 col-md-6 col-lg-4';
            } else if (cols === 1) {
                gridClass += 'col-6 col-sm-6 col-md-4 col-lg-4';
            } else {
                gridClass += 'col-6 col-sm-6 col-md-3 col-lg-3 col-lg-1-5';
            }

            document.querySelectorAll('#content .product-layout').forEach(el => {
                el.className = gridClass;
            });

            gridViewBtn?.classList.add('active');
            listViewBtn?.classList.remove('active');
            priceViewBtn?.classList.remove('active');

            localStorage.setItem('display', 'grid');
            setCookieView('display', 'grid', 365); // <-- Используем импортированную функцию!
        },

        price_view: function() {
            document.querySelectorAll('#content .product-layout').forEach(el => {
                el.className = 'product-layout product-price col-12';
            });

            priceViewBtn?.classList.add('active');
            listViewBtn?.classList.remove('active');
            gridViewBtn?.classList.remove('active');

            localStorage.setItem('display', 'price');
            setCookieView('display', 'price', 365); // <-- Используем импортированную функцию!
        },

        resize: function() {
            if (localStorage.getItem('display') !== 'grid') {
                if (window.innerWidth < 1200) { // <-- Используем встроенный window.innerWidth
                    localStorage.setItem('display_old', localStorage.getItem('display'));
                    this.grid_view();
                }
            }
            if (localStorage.getItem('display_old') === 'price' && window.innerWidth > 1200) {
                this.price_view();
            }
        }
    };

    displayView.init();
}

initProductListView();
