class AccountNPAddressForm {
    constructor(form, config = {}) {
        this.form = form;

        this.config = {
            urls: {
                areas: null,
                cities: null,
                warehouses: null,
                ...config.urls,
            },

            defaults: {
                country: 'UA',
                zip: '0',
                delay: 300,
                ...config.defaults,
            },

            messages: {
                area_placeholder: '',
                city_placeholder: '',
                branch_label: '',
                postomat_label: '',
                street_label: '',
                branch_placeholder: '',
                postomat_placeholder: '',
                street_placeholder: '',
                request_error: '',
                ...config.messages,
            },
        };

        this.areaTimer = null;
        this.cityTimer = null;
        this.warehouseTimer = null;
    }

    init() {
        this.cacheElements();

        if (!this.hasRequiredElements()) {
            return;
        }

        this.bindEvents();
        this.setupByType(false);
    }

    cacheElements() {
        this.typeSelect = this.form.querySelector('.js-np-address-type');

        this.areaInput = this.form.querySelector('.js-np-area');
        this.cityInput = this.form.querySelector('.js-np-city');
        this.addressInput = this.form.querySelector('.js-np-address');
        this.addressLabel = this.form.querySelector('.js-np-address-label');

        this.areaResults = this.form.querySelector('.js-np-area-results');
        this.cityResults = this.form.querySelector('.js-np-city-results');
        this.warehouseResults = this.form.querySelector('.js-np-warehouse-results');
    }

    hasRequiredElements() {
        return this.typeSelect
            && this.areaInput
            && this.cityInput
            && this.addressInput
            && this.areaResults
            && this.cityResults
            && this.warehouseResults
            && this.config.urls.areas
            && this.config.urls.cities
            && this.config.urls.warehouses;
    }

    bindEvents() {
        this.typeSelect.addEventListener('change', () => {
            this.setupByType(true);
        });

        this.areaInput.addEventListener('input', () => {
            this.searchAreasDebounced(this.areaInput.value);
        });

        this.areaInput.addEventListener('focus', () => {
            this.searchAreas('');
        });

        this.areaInput.addEventListener('click', () => {
            this.searchAreas('');
        });

        this.cityInput.addEventListener('input', () => {
            if (!this.areaInput.value) {
                return;
            }

            this.searchCitiesDebounced(this.cityInput.value);
        });

        this.cityInput.addEventListener('focus', () => {
            if (!this.areaInput.value) {
                return;
            }

            this.searchCities('');
        });

        this.cityInput.addEventListener('click', () => {
            if (!this.areaInput.value) {
                return;
            }

            this.searchCities('');
        });

        this.addressInput.addEventListener('input', () => {
            if (!this.needsWarehouseSearch() || !this.cityInput.value) {
                return;
            }

            this.searchWarehousesDebounced(this.addressInput.value);
        });

        this.addressInput.addEventListener('focus', () => {
            if (!this.needsWarehouseSearch() || !this.cityInput.value) {
                return;
            }

            this.searchWarehouses('');
        });

        this.addressInput.addEventListener('click', () => {
            if (!this.needsWarehouseSearch() || !this.cityInput.value) {
                return;
            }

            this.searchWarehouses('');
        });

        document.addEventListener('click', (event) => {
            if (
                !event.target.closest('.js-np-area-results')
                && !event.target.closest('.np-area-search')
            ) {
                this.clearResults(this.areaResults);
            }

            if (
                !event.target.closest('.js-np-city-results')
                && !event.target.closest('.np-city-search')
            ) {
                this.clearResults(this.cityResults);
            }

            if (
                !event.target.closest('.js-np-warehouse-results')
                && !event.target.closest('.np-address-search')
            ) {
                this.clearResults(this.warehouseResults);
            }
        });
    }

    setupByType(clearAddress = true) {
        if (clearAddress) {
            this.addressInput.value = '';
            this.clearResults(this.warehouseResults);
        }

        this.areaInput.placeholder = this.config.messages.area_placeholder;
        this.cityInput.placeholder = this.config.messages.city_placeholder;

        if (this.isBranch()) {
            this.setAddressLabel(this.config.messages.branch_label);
            this.addressInput.placeholder = this.config.messages.branch_placeholder;
            return;
        }

        if (this.isPostomat()) {
            this.setAddressLabel(this.config.messages.postomat_label);
            this.addressInput.placeholder = this.config.messages.postomat_placeholder;
            return;
        }

        this.setAddressLabel(this.config.messages.street_label);
        this.addressInput.placeholder = this.config.messages.street_placeholder;
        this.clearResults(this.warehouseResults);
    }

    setAddressLabel(text) {
        if (!this.addressLabel) {
            return;
        }

        const required = this.addressLabel.querySelector('.text-danger');

        this.addressLabel.textContent = text + ' ';

        if (required) {
            this.addressLabel.appendChild(required);
        }
    }

    selectedType() {
        return parseInt(this.typeSelect.value, 10);
    }

    isBranch() {
        return this.selectedType() === 1;
    }

    isAddressDelivery() {
        return this.selectedType() === 2;
    }

    isPostomat() {
        return this.selectedType() === 3;
    }

    needsWarehouseSearch() {
        return this.isBranch() || this.isPostomat();
    }

    searchAreasDebounced(query) {
        clearTimeout(this.areaTimer);

        this.areaTimer = setTimeout(() => {
            this.searchAreas(query);
        }, this.config.defaults.delay);
    }

    searchCitiesDebounced(query) {
        clearTimeout(this.cityTimer);

        this.cityTimer = setTimeout(() => {
            this.searchCities(query);
        }, this.config.defaults.delay);
    }

    searchWarehousesDebounced(query) {
        clearTimeout(this.warehouseTimer);

        this.warehouseTimer = setTimeout(() => {
            this.searchWarehouses(query);
        }, this.config.defaults.delay);
    }

    searchAreas(query = '') {
        this.request(this.config.urls.areas, {
            q: query || '',
        }).then((items) => {
            this.renderResults(this.areaResults, items, (item) => {
                this.areaInput.value = item.text || item.name || '';

                this.cityInput.value = '';
                this.addressInput.value = '';

                this.clearResults(this.cityResults);
                this.clearResults(this.warehouseResults);
            });
        });
    }

    searchCities(query = '') {
        if (!this.areaInput.value) {
            return;
        }

        this.request(this.config.urls.cities, {
            area: this.areaInput.value,
            q: query || '',
        }).then((items) => {
            this.renderResults(this.cityResults, items, (item) => {
                this.cityInput.value = item.name || item.text || '';
                this.addressInput.value = '';

                this.clearResults(this.warehouseResults);
            });
        });
    }

    searchWarehouses(query = '') {
        if (!this.cityInput.value || !this.needsWarehouseSearch()) {
            return;
        }

        this.request(this.config.urls.warehouses, {
            city: this.cityInput.value,
            type: this.isPostomat() ? 'postomat' : 'branch',
            q: query || '',
        }).then((items) => {
            this.renderResults(this.warehouseResults, items, (item) => {
                this.addressInput.value = item.text || item.name || '';
            });
        });
    }

    request(url, params = {}) {
        const query = new URLSearchParams(params);

        return fetch(`${url}?${query.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`${this.config.messages.request_error}: ${response.status}`);
                }

                return response.json();
            })
            .catch((error) => {
                console.error(error);

                return [];
            });
    }

    renderResults(container, items, onSelect) {
        container.innerHTML = '';

        if (!Array.isArray(items) || items.length === 0) {
            container.style.display = 'none';
            return;
        }

        const list = document.createElement('ul');

        list.className = 'np-search-list list-unstyled mb-0';
        list.setAttribute('role', 'listbox');

        items.forEach((item) => {
            const label = item.text || item.name || '';

            const listItem = document.createElement('li');
            const option = document.createElement('div');

            option.className = 'np-search-result';
            option.textContent = label;
            option.setAttribute('role', 'option');
            option.setAttribute('tabindex', '0');

            option.addEventListener('click', () => {
                onSelect(item);
                this.clearResults(container);
            });

            option.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();

                onSelect(item);
                this.clearResults(container);
            });

            listItem.appendChild(option);
            list.appendChild(listItem);
        });

        container.appendChild(list);
        container.style.display = 'block';
    }

    clearResults(container) {
        if (!container) {
            return;
        }

        container.innerHTML = '';
        container.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const config = window.AccountNPAddressConfig || {};

    document.querySelectorAll('[data-np-address-form]').forEach((form) => {
        new AccountNPAddressForm(form, config).init();
    });
});
