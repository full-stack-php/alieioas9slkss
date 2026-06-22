export function initMobileContact() {
    document.addEventListener('click', (e) => {

        if (e.target.closest('.btn-open-contact')) {
            const fixedMobile = document.getElementById('fm-fixed-mobile');
            const sidebarPhones = document.querySelector('.mobile-sidebar-phones');
            const sidebarInner = document.querySelector('.mobile-sidebar-phones__inner');

            if (fixedMobile) fixedMobile.classList.remove('d-none');

            if (sidebarInner && sidebarInner.children.length === 0) {

                const replaceClasses = (element) => {
                    [element, ...element.querySelectorAll('*')].forEach(el => {
                        if (typeof el.className === 'string' && el.className.includes('up-header-phones')) {
                            el.className = el.className.replace(/up-header-phones/g, 'mobile-sidebar-phones');
                        }
                    });
                };

                const topItems = document.querySelector('.up-header-phones__items');
                if (topItems) {
                    const cloneTop = topItems.cloneNode(true);
                    replaceClasses(cloneTop);
                    sidebarInner.append(cloneTop);
                }

                const dropdownItems = document.querySelector('.up-header-phones__dropdown');
                if (dropdownItems) {
                    const cloneDropdown = dropdownItems.cloneNode(true);
                    cloneDropdown.classList.remove('dropdown-menu', 'ch-dropdown');
                    cloneDropdown.classList.add('list-unstyled');
                    replaceClasses(cloneDropdown);
                    sidebarInner.append(cloneDropdown);
                }
            }

            const bottomMobile = document.getElementById('fm-fixed-mobile-bottom');
            if (bottomMobile) bottomMobile.classList.add('z-index-low');

            document.body.classList.add('no-scroll');
            if (sidebarPhones) sidebarPhones.classList.remove('hidden');

            setTimeout(() => {
                if (sidebarPhones) sidebarPhones.classList.add('open-phones');
            }, 20);

            if (!document.querySelector('.sl-bg-phones') && sidebarPhones) {
                sidebarPhones.insertAdjacentHTML('beforebegin', '<div class="sl-bg-mob sl-bg-phones hidden-md hidden-lg active"></div>');
            }

            setTimeout(() => {
                const sideMenuHeader = document.querySelector('.mobile-sidebar-phones__top');
                if (sidebarInner && sideMenuHeader) {
                    sidebarInner.onscroll = () => {
                        if (sidebarInner.scrollTop > 0) {
                            sideMenuHeader.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.06)';
                            sideMenuHeader.classList.remove('no-shadow');
                        } else {
                            sideMenuHeader.style.boxShadow = 'none';
                            sideMenuHeader.classList.add('no-shadow');
                        }
                    };
                }
            }, 400);

            return;
        }

        if (e.target.closest('.fm-close-phones') || e.target.closest('.sl-bg-phones')) {
            const sidebarPhones = document.querySelector('.mobile-sidebar-phones');
            const bottomMobile = document.getElementById('fm-fixed-mobile-bottom');

            if (sidebarPhones) sidebarPhones.classList.remove('open-phones');
            if (bottomMobile) bottomMobile.classList.remove('z-index-low');
            document.body.classList.remove('no-scroll');

            document.querySelectorAll('.sl-bg-mob').forEach(bg => bg.remove());

            setTimeout(() => {
                const searchSidebar = document.querySelector('.mobile-sidebar-search');
                const fixedMobile = document.getElementById('fm-fixed-mobile');
                const sideMenuHeader = document.querySelector('.mobile-sidebar-phones__top');

                if (searchSidebar) searchSidebar.classList.add('hidden');
                if (fixedMobile) fixedMobile.classList.add('d-none');

                if (sideMenuHeader) {
                    sideMenuHeader.style.boxShadow = 'none';
                    sideMenuHeader.classList.add('no-shadow');
                }
            }, 200);
        }
    });
}
