export function initAccordionFooterMenu() {
    document.addEventListener('click', (e) => {
        const title = e.target.closest('.title-f');
        if (!title || window.innerWidth >= 768) return;

        const content = title.nextElementSibling;
        if (!content) return;
        const isOpened = title.classList.toggle('show-info');

        if (isOpened) {
            content.style.display = 'block';
            const height = content.scrollHeight;

            content.style.maxHeight = '0px';
            content.style.overflow = 'hidden';
            content.style.transition = 'max-height 0.1s ease-out';

            requestAnimationFrame(() => {
                content.style.maxHeight = height + 'px';
            });

            setTimeout(() => {
                if (title.classList.contains('show-info')) {
                    content.style.maxHeight = 'none';
                    content.style.overflow = 'visible';
                }
            }, 100);

        } else {
            content.style.maxHeight = content.scrollHeight + 'px';
            content.style.overflow = 'hidden';
            content.style.transition = 'max-height 0.1s ease-out';

            requestAnimationFrame(() => {
                content.style.maxHeight = '0px';
            });

            setTimeout(() => {
                if (!title.classList.contains('show-info')) {
                    content.style.display = 'none';
                }
            }, 100);
        }

        setTimeout(() => {
            const targetY = title.getBoundingClientRect().top + window.scrollY - 80;
            window.scrollTo({
                top: targetY,
                behavior: 'smooth'
            });
        }, 50);
    });
}
