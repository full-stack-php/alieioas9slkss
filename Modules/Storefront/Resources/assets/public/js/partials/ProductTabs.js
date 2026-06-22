export class ProductTabs {
    constructor() {
        this.manualTabChange = false;
        this.activeTabId = null;
        this.ticking = false;
        this.scrollContainer = document.querySelector('.tabs__header__scroll');
        this.tabsContainer = document.querySelector('.tabs__header__scroll .nav-tabs.my-tabs');
        this.activeLine = document.querySelector('.tabs__active_line');
        this.tabLinks = document.querySelectorAll('.tabs__header__scroll .my-tabs li a');
        this.tabContents = document.querySelectorAll('.tab-pane, #main_product');
        this.header = document.querySelector('.fix-header');
        this.tabsTop = document.querySelector('.product_page .tabs__header.tabs_top');
        this.handleScroll = this.handleScroll.bind(this);
    }

    init() {
        if (!this.tabsContainer) return;

        this.checkHash();
        this.bindEvents();
        this.updateActiveLine();
        this.handleScroll();
    }

    checkHash() {
        const hash = window.location.hash;
        if (hash) {
            const targetLink = document.querySelector(`a[href="${hash}"]`);
            if (targetLink) targetLink.click();
        }
    }
    updateActiveLine() {
        setTimeout(() => {
            const activeLi = this.tabsContainer.querySelector('li.active');

            if (activeLi) {
                activeLi.classList.remove('before-load');

                if (this.activeLine) {
                    this.activeLine.style.width = `${activeLi.offsetWidth -1}px`;
                    this.activeLine.style.transform = `translateX(${activeLi.offsetLeft - 7}px)`;
                }
            }
        }, 100);
    }
    scrollToActiveTab(tabLi) {
        this.updateActiveLine();

        if (!this.scrollContainer || !tabLi) return;

        const containerWidth = this.scrollContainer.offsetWidth;
        const tabWidth = tabLi.offsetWidth;
        const tabLeft = tabLi.offsetLeft;

        const goLeft = tabLeft - (containerWidth / 2) + (tabWidth / 2);
        this.scrollContainer.scrollTo({
            left: Math.max(0, goLeft),
            behavior: 'smooth'
        });
    }

    goTab(selector) {
        const link = document.querySelector(`a[href="${selector}"]`);
        if (link) link.click();
    }

    setActiveTab(tabId) {
        this.activeTabId = tabId;

        const allLis = this.tabsContainer.querySelectorAll('li');
        allLis.forEach(li => li.classList.remove('active'));

        const activeLink = this.tabsContainer.querySelector(`li a[href="#${tabId}"]`);
        if (activeLink) {
            const activeLi = activeLink.parentElement;
            activeLi.classList.add('active');
            this.scrollToActiveTab(activeLi);
        }
    }

    bindEvents() {
        this.tabLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.manualTabChange = true;

                const li = link.parentElement;
                this.scrollToActiveTab(li);

                const targetId = link.getAttribute('href');
                const targetEl = document.querySelector(targetId);

                if (targetEl) {
                    let topOffset = 0;

                    if (window.innerWidth > 992) {
                        if (this.header) topOffset += this.header.offsetHeight;
                        if (this.tabsTop) topOffset += this.tabsTop.offsetHeight;
                    } else {
                        topOffset += 100;
                    }

                    const targetY = targetEl.getBoundingClientRect().top + window.scrollY - topOffset - 18;

                    window.scrollTo({
                        top: targetY,
                        behavior: 'auto'
                    });

                    setTimeout(() => {
                        this.manualTabChange = false;
                    }, 10);
                }
            });
        });
        window.addEventListener('scroll', () => {
            if (!this.ticking) {
                window.requestAnimationFrame(() => {
                    this.handleScroll();
                    this.ticking = false;
                });
                this.ticking = true;
            }
        }, { passive: true });
    }

    handleScroll() {
        if (this.manualTabChange) return;

        const scrollPosition = window.scrollY;
        const headerHeight = this.header ? this.header.offsetHeight : 0;
        const thresholdOffset = window.innerWidth > 768 ? headerHeight + 90 : 130;

        this.tabContents.forEach(content => {
            const tabId = content.getAttribute('id');
            if (!tabId) return;

            const top = content.getBoundingClientRect().top + window.scrollY;
            const height = content.offsetHeight;
            const threshold = top - thresholdOffset;

            if (threshold <= scrollPosition && (threshold + height + 70) > scrollPosition && this.activeTabId !== tabId) {
                this.setActiveTab(tabId);
            }
        });
    }
}
