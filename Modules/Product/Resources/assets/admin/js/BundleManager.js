import Choices from 'choices.js';

export default class {
    constructor() {
        this.container = document.getElementById('product-bundles-container');
        this.template = document.getElementById('bundle-row-template')?.innerHTML;
        this.addBtn = document.getElementById('add-bundle-btn');

        this.index = this.container?.querySelectorAll('tr.bundle-row').length || 0;

        if (this.container) {
            this.init();
        }
    }

    init() {
        this.container.querySelectorAll('.ajax-product-search').forEach(select => {
            this.initChoices(select);
        });

        this.addBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            const row = this.renderRow();
            if (row) {
                const newSelect = row.querySelector('.ajax-product-search');
                this.initChoices(newSelect);
            }
        });

        // 3. Делегирование события удаления
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.delete-bundle-row');
            if (btn) {
                e.preventDefault();
                btn.closest('tr').remove();
            }
        });
    }

    /**
     * Рендеринг строки из шаблона
     */
    renderRow() {
        if (!this.template) return null;

        let html = this.template.replace(/__INDEX__/g, this.index++);
        const tempTable = document.createElement('table');
        tempTable.innerHTML = `<tbody>${html.trim()}</tbody>`;

        const row = tempTable.querySelector('tbody').firstElementChild;
        this.container.appendChild(row);

        return row;
    }

    /**
     * Настройка Choices.js для AJAX поиска
     */
    initChoices(element) {
        if (!element) return;

        const choices = new Choices(element, Korf.data['gift_search'] || {});
        const url = Korf.data['ajax_url'];
        let currentData = [];

        element.addEventListener('search', (event) => {
            const query = event.detail.value;
            if (query.length < 2) return;

            fetch(`${url}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    currentData = data;
                    const choicesList = data.map(product => ({
                        value: String(product.id),
                        label: product.text,
                        selected: false,
                    }));

                    choices.clearChoices();
                    choices.setChoices(choicesList, 'value', 'label', false);
                });
        }, false);

        element.addEventListener('addItem', (event) => {
            let selectedId = event.detail.value;
            let product = currentData.find(p => String(p.id) === String(selectedId));
            console.log(product);
            console.log(selectedId);


            if (product && product.price) {
                let container = element.closest('.bundle-row');
                if (container) {
                    let priceInput = container.querySelector('input[name*="[bundle_price]"]');
                    if (priceInput) priceInput.value = product.price;
                }
            }
        }, false);
    }
}
