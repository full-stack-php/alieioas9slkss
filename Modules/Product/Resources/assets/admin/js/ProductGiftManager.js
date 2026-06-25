import Choices from 'choices.js';

export default class ProductGiftManager {
    constructor() {
        this.giftConfigUrl = window.Korf.data['gift_config_url'];

        this.container = document.getElementById('product-gifts-list');
        this.template = document.getElementById('product-gift-template')?.innerHTML;
        this.addBtn = document.getElementById('add-new-product-gift');

        this.index = this.container?.querySelectorAll('tr.gift-rule').length || 0;

        if (this.container) {
            this.init();
        }
    }

    init() {
        this.bindEvents();
        this.initGiftRows(document);
        this.loadExistingGiftConfigs();
    }

    bindEvents() {
        this.addBtn?.addEventListener('click', (e) => {
            e.preventDefault();

            const row = this.renderRow(this.index++);

            if (row) {
                this.initGiftRows(row);
            }
        });

        document.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.delete-gift-rule');

            if (deleteBtn) {
                e.preventDefault();
                deleteBtn.closest('tr.gift-rule')?.remove();
            }
        });
    }

    renderRow(index) {
        if (!this.container || !this.template) {
            return null;
        }

        const html = this.template.replace(/__INDEX__/g, index);

        const tempTable = document.createElement('table');
        tempTable.innerHTML = `<tbody>${html.trim()}</tbody>`;

        const row = tempTable.querySelector('tbody').firstElementChild;

        this.container.appendChild(row);

        return row;
    }

    initGiftRows(scope) {
        scope.querySelectorAll('.ajax-product-search.gift-product-selector').forEach((select) => {
            this.initChoices(select);
        });
    }

    initChoices(element) {
        if (!element) {
            return;
        }

        if (element.choicesInstance) {
            return;
        }

        const choices = new Choices(element, Korf.data['gift_search'] || {});
        const url = Korf.data['ajax_url'];

        let currentData = [];

        element.choicesInstance = choices;

        element.addEventListener('search', (event) => {
            const query = event.detail.value;

            if (!query || query.length < 2) {
                return;
            }

            fetch(`${url}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    currentData = data || [];

                    const choicesList = currentData.map((product) => {
                        return {
                            value: String(product.id),
                            label: product.text || product.name || `ID: ${product.id}`,
                            selected: false,
                        };
                    });

                    choices.clearChoices();
                    choices.setChoices(choicesList, 'value', 'label', false);
                });
        }, false);

        element.addEventListener('addItem', (event) => {
            const selectedId = event.detail.value;
            const row = element.closest('tr.gift-rule');

            if (!row || !selectedId) {
                return;
            }

            this.clearGiftConfig(row);
            this.loadGiftConfig(row, selectedId);
        }, false);

        element.addEventListener('removeItem', () => {
            const row = element.closest('tr.gift-rule');

            if (row) {
                this.clearGiftConfig(row);
            }
        }, false);
    }

    loadExistingGiftConfigs() {
        this.container.querySelectorAll('tr.gift-rule').forEach((row) => {
            const select = row.querySelector('.gift-product-selector');
            const productId = select?.value || select?.dataset.selected;

            if (productId) {
                this.loadGiftConfig(row, productId);
            }
        });
    }

    clearGiftConfig(row) {
        if (!row) {
            return;
        }

        const config = row.querySelector('.gift-product-config');
        const optionsWrapper = row.querySelector('.gift-product-options');
        const packagingSelect = row.querySelector('.gift-packaging-selector');

        if (optionsWrapper) {
            optionsWrapper.innerHTML = '';
        }

        if (packagingSelect) {
            packagingSelect.innerHTML = '<option value="">Без упаковки</option>';
            packagingSelect.value = '';
        }

        if (config) {
            config.classList.add('d-none');
        }
    }

    loadGiftConfig(row, productId) {
        if (!this.giftConfigUrl) {
            console.error('Korf.data.gift_config_url is not defined');
            alert('Не задан URL для загрузки конфигурации подарка');
            return;
        }

        const url = this.giftConfigUrl.replace('__PRODUCT_ID__', productId);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                return response.json();
            })
            .then((data) => {
                console.log('gift-config response:', data);

                this.renderGiftOptions(row, data.options || []);
                this.renderGiftPackagings(row, data.packagings || []);

                const config = row.querySelector('.gift-product-config');

                if (config) {
                    config.classList.remove('d-none');
                }
            })
            .catch((error) => {
                console.error('gift-config error:', error);
                alert('Не удалось загрузить конфигурацию подарочного товара');
            });
    }

    renderGiftOptions(row, options) {
        const wrapper = row.querySelector('.gift-product-options');

        if (!wrapper) {
            return;
        }

        const selected = this.parseSelectedOptions(row.dataset.selectedOptions);
        const rowIndex = row.dataset.index;

        wrapper.innerHTML = '';

        if (!options.length) {
            return;
        }

        options.forEach((option) => {
            const optionBlock = document.createElement('div');
            optionBlock.classList.add('mb-2');

            const label = document.createElement('label');
            label.classList.add('small', 'mb-1');
            label.textContent = option.name;

            const select = document.createElement('select');
            select.classList.add('form-control');
            select.name = `product_gifts[${rowIndex}][options][${option.id}]`;

            // Всегда первое пустое значение
            const emptyOption = new Option('Выбрать', '');
            emptyOption.selected = !selected[option.id];
            select.appendChild(emptyOption);

            option.values.forEach((value) => {
                const optionElement = new Option(value.label, value.id);

                if (String(selected[option.id]) === String(value.id)) {
                    optionElement.selected = true;
                }

                select.appendChild(optionElement);
            });

            optionBlock.appendChild(label);
            optionBlock.appendChild(select);

            wrapper.appendChild(optionBlock);
        });
    }

    renderGiftPackagings(row, packagings) {
        const select = row.querySelector('.gift-packaging-selector');

        if (!select) {
            return;
        }

        const selected = select.dataset.selected || row.dataset.selectedPackaging || '';

        select.innerHTML = '<option value="">Без упаковки</option>';

        packagings.forEach((packaging) => {
            const option = new Option(packaging.name, packaging.id);

            if (String(selected) === String(packaging.id)) {
                option.selected = true;
            }

            select.appendChild(option);
        });

        select.value = selected || '';
    }

    parseSelectedOptions(value) {
        if (!value) {
            return {};
        }

        try {
            return JSON.parse(value);
        } catch (e) {
            return {};
        }
    }
}
