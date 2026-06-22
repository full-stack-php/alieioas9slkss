function initBrandFilter() {
    console.log('brand already initialized.');
    const input = document.getElementById('filter_brand');
    if (!input) return;

    const items = document.querySelectorAll('.brands_wrap .brand_item');

    input.addEventListener('input', function() {
        const mask = this.value.toLowerCase().trim();

        items.forEach(item => {
            const brandName = item.textContent.toLowerCase();

            if (brandName.includes(mask)) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    });
}

initBrandFilter();
