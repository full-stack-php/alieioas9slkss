// partials/stickyMenu.js

export function initStickyMenu() {
    // === 1. Кэшируем элементы (ищем их только один раз при загрузке) ===
    const header = document.querySelector('header.fix-header');
    const topNav = document.getElementById('top');
    const htopBPc = document.querySelector('.htop-b-pc');
    const tabsHeader = document.querySelector('.tabs__header.tabs_top');
    const navTabs = document.querySelector('.tabs__header .nav-tabs');
    const stickyLeft = document.querySelector('.sticky-left-block');
    const stickyProduct = document.querySelector('.sticky-product-info');
    const pageWrap = document.getElementById('page_wrap');
    const upHeader = document.querySelector('.up-header');

    // Элементы для десктопного меню
    const menuHeaderBox = document.querySelector('.menu-header-box.type-menu-h-1');
    const contentTopBox = document.querySelector('.home-page-content-top');
    const menuItems = document.querySelectorAll('.menu-header-box.type-menu-h-1 #menu-vertical-list > li');

    let ticking = false; // Флаг для оптимизации скролла

    // === 2. Основная логика вычислений ===
    function handleScroll() {
        const scrollTop = window.scrollY; // Текущая позиция скролла
        const isDesktop = window.innerWidth > 991; // Замена вашей функции viewport()

        // Динамически получаем высоты (т.к. они могут меняться при ресайзе или загрузке картинок)
        const topNavHeight = topNav ? topNav.offsetHeight : 0;
        const headerHeight = header ? header.offsetHeight : 0;
        const htopBHeight = htopBPc ? htopBPc.offsetHeight : 0;
        const htab = (tabsHeader && navTabs) ? navTabs.offsetHeight : 0;

        // --- Логика 1: Обновление позиций Sticky блоков ---
        if (isDesktop) {
            if (tabsHeader) tabsHeader.style.top = `${headerHeight - 1}px`;
            if (stickyLeft) stickyLeft.style.top = `${headerHeight + htab + 20}px`;
            if (stickyProduct) stickyProduct.style.top = `${headerHeight + htab + 20}px`;
        } else {
            if (tabsHeader) tabsHeader.style.removeProperty('top');
            if (stickyLeft) stickyLeft.style.top = '0px'; // Исправлена логическая ошибка из оригинала
            if (stickyProduct) stickyProduct.style.top = '0px';
        }

        // --- Логика 2: Проверка прилипания табов ---
        if (tabsHeader && pageWrap) {
            // Вычисляем позицию блока относительно документа
            const contentTop = pageWrap.getBoundingClientRect().top + window.scrollY;
            const currentHeaderHeight = isDesktop ? headerHeight : 54;

            if (scrollTop > contentTop - currentHeaderHeight) {
                tabsHeader.classList.add('active-tab-sticky');
                if (!isDesktop && upHeader) {
                    upHeader.classList.add('header-no-shadow');
                } else if (upHeader) {
                    upHeader.classList.remove('header-no-shadow');
                }
            } else {
                tabsHeader.classList.remove('active-tab-sticky');
                if (upHeader) upHeader.classList.remove('header-no-shadow');
            }
        }

        // --- Логика 3: Фиксация десктопного меню ---
        if (header && menuHeaderBox) {
            if (scrollTop > topNavHeight + htopBHeight && isDesktop) {
                if (menuHeaderBox.classList.contains('mm_open_hp')) {
                    if (contentTopBox) {
                        // Вычисляем нижнюю границу контентного блока
                        const contentTopBoxBottom = contentTopBox.getBoundingClientRect().bottom + window.scrollY;

                        if (scrollTop > contentTopBoxBottom - headerHeight + 2) {
                            menuHeaderBox.classList.add('m-sticky');
                            menuHeaderBox.style.top = `${headerHeight}px`;
                        } else {
                            menuItems.forEach(li => li.classList.remove('menu-open'));
                            menuHeaderBox.classList.remove('m-sticky');
                            menuHeaderBox.style.top = '0px';
                        }
                    }
                } else {
                    menuHeaderBox.classList.add('m-sticky');
                    menuHeaderBox.style.top = `${headerHeight}px`;
                }
            } else {
                menuHeaderBox.classList.remove('m-sticky');
                menuHeaderBox.style.top = '0px';
            }
        }
    }

    // === 3. Оптимизированные слушатели событий ===
    const onScrollOrResize = () => {
        // requestAnimationFrame гарантирует, что браузер выполнит вычисления
        // прямо перед отрисовкой следующего кадра. Это убирает "дергания" при скролле.
        if (!ticking) {
            window.requestAnimationFrame(() => {
                handleScroll();
                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener('scroll', onScrollOrResize, { passive: true });
    // Добавляем ресайз, так как при повороте экрана (планшет) высоты шапки могут измениться
    window.addEventListener('resize', onScrollOrResize, { passive: true });

    // Запускаем один раз при инициализации
    handleScroll();
}
