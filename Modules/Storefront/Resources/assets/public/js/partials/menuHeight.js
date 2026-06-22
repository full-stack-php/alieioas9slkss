export function heightMenuOpenHp() {
    if (window.innerWidth <= 1199) return;

    const menuHeaderBox = document.querySelector('.menu-header-box.type-menu-h-1');
    if (!menuHeaderBox || menuHeaderBox.classList.contains('m-sticky') || !menuHeaderBox.classList.contains('mm_open_hp')) {
        return;
    }
    const contentTop = document.querySelector('.home-page-content-top');
    const menuBox = document.querySelector('.menu-box.m_type_header_1');
    if (contentTop && menuBox) {
        const mmh = contentTop.clientHeight;
        menuBox.style.height = `${mmh - 40}px`;
    }
}

export function heightMenu() {
    const menuBox = document.querySelector('.menu-box.m_type_header_1');
    if (menuBox) {
        menuBox.style.height = '';
    }
    heightMenuOpenHp();
}
