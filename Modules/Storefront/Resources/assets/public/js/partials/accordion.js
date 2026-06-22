export function initAccordion() {
    const intros = document.querySelectorAll('.accordion__intro');
    const allContents = document.querySelectorAll('.accordion__content');

    // === 1. Базовая настройка ===
    allContents.forEach(content => {
        content.style.transition = 'max-height 0.35s ease';
        content.style.overflow = 'hidden';

        const parent = content.closest('.accordion-item-custom');
        if (parent && parent.classList.contains('accordion-active')) {
            content.style.maxHeight = content.scrollHeight + 'px';
        } else {
            content.style.maxHeight = '0px';
        }
    });

    // === 2. Логика клика ===
    intros.forEach(intro => {
        intro.addEventListener('click', function() {
            const currentItem = this.closest('.accordion-item-custom');
            if (!currentItem) return;

            const currentContent = currentItem.querySelector('.accordion__content');
            const isActive = currentItem.classList.contains('accordion-active');

            const activeItems = document.querySelectorAll('.accordion-item-custom.accordion-active');

            activeItems.forEach(item => {
                if (item === currentItem) return;

                item.classList.remove('accordion-active');
                const itemContent = item.querySelector('.accordion__content');
                if (itemContent) {
                    itemContent.style.maxHeight = '0px';
                }
            });

            if (!isActive) {
                currentItem.classList.add('accordion-active');
                if (currentContent) {
                    currentContent.style.maxHeight = currentContent.scrollHeight + 'px';
                }
            }
            else {
                currentItem.classList.remove('accordion-active');
                if (currentContent) {
                    currentContent.style.maxHeight = '0px';
                }
            }
        });
    });

    window.addEventListener('resize', () => {
        const activeContents = document.querySelectorAll('.accordion-item-custom.accordion-active .accordion__content');
        activeContents.forEach(content => {
            content.style.maxHeight = 'none';
            const newHeight = content.scrollHeight;
            content.style.maxHeight = newHeight + 'px';
        });
    });
}
