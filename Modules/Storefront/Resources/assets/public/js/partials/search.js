// main.js

export function initSearch() {
    document.addEventListener('click', (event) => {
        const searchBtn = event.target.closest('.btn-search');
        if (!searchBtn) return;

        event.preventDefault();
        performSearch(searchBtn);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && event.target.matches('input[name="search"]')) {
            const container = event.target.closest('.header-search');
            const searchBtn = container?.querySelector('.btn-search');

            if (searchBtn) {
                event.preventDefault();
                performSearch(searchBtn);
            }
        }
    });

    function performSearch(button) {
        const container = button.closest('.header-search');
        if (!container) return;

        const input = container.querySelector('input[name="search"]');
        const searchValue = input ? input.value.trim() : '';

        const baseUrl = document.querySelector('base')?.getAttribute('href') || '/';

        let url = `${window.Korf.data.baseUrl}/search?`;

        url += `query=${encodeURIComponent(searchValue)}`;

        const categoryInput = document.querySelector('input[name="search_category_id"]');
        const categoryId = categoryInput ? parseInt(categoryInput.value, 10) : 0;

        if (categoryId > 0) {
            url += `&category_id=${encodeURIComponent(categoryId)}&sub_category=true`;
        }
        window.location.href = url;
    }
}
