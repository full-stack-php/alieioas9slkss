export function initMenuControl() {
    const menuItems = document.querySelectorAll('.menu-big .container > ul > li');

    menuItems.forEach(item => {
        item.addEventListener('mouseenter', () => {

            menuItems.forEach(el => {
                const body = el.querySelector('.menu__body');
                const link = el.querySelector('a');

                if (body) body.style.display = 'none';
                el.style.background = 'transparent';
                if (link) link.style.color = '#262326';
            });

            const activeBody = item.querySelector('.menu__body');
            const activeLink = item.querySelector('a');

            if (activeBody) activeBody.style.display = 'block';
            if (activeLink) activeLink.style.color = '#7a4cd9';
            item.style.background = '#fff';
        });
    });


    const menuBtn = document.querySelector('.catalog-btn');
    const menuBig = document.querySelector('.menu-big');
    const closeCatalog = document.querySelector('.menu-big__top button');
    const wrapper = document.querySelector('.sl-bg');

    const openMenu = () => {
        menuBig?.classList.add('open');
        wrapper?.classList.add('active');
    };

    const closeMenu = () => {
        menuBig?.classList.remove('open');
        wrapper?.classList.remove('active');
    };

    menuBtn?.addEventListener('click', openMenu);
    closeCatalog?.addEventListener('click', closeMenu);

    wrapper?.addEventListener('click', (e) => {
        if (wrapper.classList.contains('overlay') && e.target === wrapper) {
            closeMenu();
        }
    });
}
