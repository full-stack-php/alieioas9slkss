import Choices from 'choices.js';

export default class {
    constructor() {
        this.container = document.getElementById('product-gifts-container');
        this.template = document.getElementById('gift-row-template')?.innerHTML;
        this.addBtn = document.getElementById('add-gift-btn');
        this.index = this.container?.querySelectorAll('tr.gift-row').length || 0;

        if (this.container) this.init();
    }

    init() {
        this.container.querySelectorAll('.ajax-product-search').forEach(select => {
            this.initChoices(select);
        });

        this.addBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            const row = this.renderRow();
            const newSelect = row.querySelector('.ajax-product-search');
            this.initChoices(newSelect);
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.delete-gift-row');
            if (btn) {
                btn.closest('tr').remove();
            }
        });
    }

    renderRow() {
        let html = this.template.replace(/__INDEX__/g, this.index++);
        const tempTable = document.createElement('table');
        tempTable.innerHTML = `<tbody>${html.trim()}</tbody>`;
        const row = tempTable.querySelector('tbody').firstElementChild;
        this.container.appendChild(row);
        return row;
    }

    initChoices(element) {
        const choices = new Choices(element, Korf.data['gift_search'] || {});
        const url = Korf.data['ajax_url'];

        element.addEventListener('search', (event) => {
            const query = event.detail.value;
            if (query.length < 2) return;

            fetch(`${url}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    const choicesList = data.map(product => ({
                        value: String(product.id),
                        label: product.text,
                        selected: false,
                    }));

                    choices.clearChoices();
                    choices.setChoices(choicesList, 'value', 'label', false);
                })
                .catch(err => console.error('AJAX Error:', err));
        }, false);
    }
}
