class NovaPoshtaCheckout {
    constructor(config = {}) {
        this.config = {
            selectors: {
                areaInput: '#billing-state',
                cityInput: '#billing-city',
                addressInput: '#billing-address-1',
                addressLabel: '#billing-address-1-label',
                countryInput: '#billing-country',
                zipInput: '#billing-zip',

                areaResults: '#np-area-results',
                cityResults: '#np-city-results',
                warehouseResults: '#np-warehouse-results',

                shippingMethod: 'input[name="shipping_method"], select[name="shipping_method"]',

                ...config.selectors,
            },

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

            shippingMethods: {
                branch: 'nova_poshta_branch',
                address: 'nova_poshta_address',
                postomat: 'nova_poshta_postomat',

                ...config.shippingMethods,
            },

            messages: {
                area_placeholder: 'Почніть вводити область',
                city_placeholder: 'Спочатку виберіть область',
                branch_placeholder: 'Оберіть відділення',
                postomat_placeholder: 'Оберіть поштомат',
                address_placeholder: 'Вулиця, будинок, квартира',
                request_error: 'Не вдалося завантажити дані Нової Пошти',
                street_label: 'Street Address',
                branch_label: 'Відділення Нової Пошти',
                postomat_label: 'Поштомат Нової Пошти',
                street_placeholder: 'Вулиця, будинок, квартира',

                ...config.messages,
            },
        };

        this.areaTimer = null;
        this.cityTimer = null;
        this.warehouseTimer = null;
        this.previousShippingMethod = null;
    }

    init() {
        this.cacheElements();

        if (!this.hasRequiredElements()) {
            return;
        }

        this.previousShippingMethod = this.currentShippingMethod();

        this.bindEvents();
        this.bindSavedAddressEvents();
        this.filterSavedAddressesByShippingMethod();

        this.setupFields();
    }

    cacheElements() {
        const selectors = this.config.selectors;

        this.areaInput = document.querySelector(selectors.areaInput);
        this.cityInput = document.querySelector(selectors.cityInput);
        this.addressInput = document.querySelector(selectors.addressInput);
        this.addressLabel = document.querySelector(selectors.addressLabel);
        this.countryInput = document.querySelector(selectors.countryInput);
        this.zipInput = document.querySelector(selectors.zipInput);

        this.areaResults = document.querySelector(selectors.areaResults);
        this.cityResults = document.querySelector(selectors.cityResults);
        this.warehouseResults = document.querySelector(selectors.warehouseResults);

        this.shippingMethodElements = document.querySelectorAll(selectors.shippingMethod);

        this.savedAddresses = document.querySelector(selectors.savedAddresses);
        this.savedAddressItems = document.querySelectorAll(selectors.savedAddressItem);
        this.savedAddressRadios = document.querySelectorAll(selectors.savedAddressRadio);
        this.savedAddressNewRadio = document.querySelector(selectors.savedAddressNewRadio);
        this.newAddressForm = document.querySelector(selectors.newAddressForm);
    }

    setAddressLabel(text) {
        if (!this.addressLabel) {
            return;
        }

        this.addressLabel.textContent = text;
    }

    hasRequiredElements() {
        return this.areaInput
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
        this.areaInput.addEventListener('input', () => {
            if (!this.isNovaPoshta()) {
                return;
            }

            this.searchAreasDebounced(this.areaInput.value);
        });

        this.areaInput.addEventListener('focus', () => {
            if (!this.isNovaPoshta()) {
                return;
            }

            this.searchAreas('');
        });

        this.areaInput.addEventListener('click', () => {
            if (!this.isNovaPoshta()) {
                return;
            }

            this.searchAreas('');
        });

        this.cityInput.addEventListener('input', () => {
            if (!this.isNovaPoshta() || !this.areaInput.value) {
                return;
            }

            this.searchCitiesDebounced(this.cityInput.value);
        });

        this.cityInput.addEventListener('focus', () => {
            if (!this.isNovaPoshta() || !this.areaInput.value) {
                return;
            }

            this.searchCities('');
        });

        this.cityInput.addEventListener('click', () => {
            if (!this.isNovaPoshta() || !this.areaInput.value) {
                return;
            }

            this.searchCities('');
        });

        this.addressInput.addEventListener('input', () => {
            if (!this.isBranch() && !this.isPostomat()) {
                return;
            }

            if (!this.cityInput.value) {
                return;
            }

            this.searchWarehousesDebounced(this.addressInput.value);
        });

        this.addressInput.addEventListener('focus', () => {
            if (!this.isBranch() && !this.isPostomat()) {
                return;
            }

            if (!this.cityInput.value) {
                return;
            }

            this.searchWarehouses('');
        });

        this.addressInput.addEventListener('click', () => {
            if (!this.isBranch() && !this.isPostomat()) {
                return;
            }

            if (!this.cityInput.value) {
                return;
            }

            this.searchWarehouses('');
        });

        this.shippingMethodElements.forEach((element) => {
            element.addEventListener('change', () => {
                const newShippingMethod = this.currentShippingMethod();

                this.clearFieldsAfterMethodChanged(this.previousShippingMethod, newShippingMethod);

                this.previousShippingMethod = newShippingMethod;

                this.filterSavedAddressesByShippingMethod();
                this.setupFields();
            });
        });

        document.addEventListener('click', (event) => {
            if (
                !event.target.closest(this.config.selectors.areaResults)
                && !event.target.closest('.np-area-search')
            ) {
                this.clearResults(this.areaResults);
            }

            if (
                !event.target.closest(this.config.selectors.cityResults)
                && !event.target.closest('.np-city-search')
            ) {
                this.clearResults(this.cityResults);
            }

            if (
                !event.target.closest(this.config.selectors.warehouseResults)
                && !event.target.closest('.np-address-search')
            ) {
                this.clearResults(this.warehouseResults);
            }
        });
    }

    refreshShippingMethods() {
        this.shippingMethodElements = document.querySelectorAll(this.config.selectors.shippingMethod);

        this.shippingMethodElements.forEach((element) => {
            element.addEventListener('change', () => {
                const newShippingMethod = this.currentShippingMethod();

                this.clearFieldsAfterMethodChanged(this.previousShippingMethod, newShippingMethod);

                this.previousShippingMethod = newShippingMethod;

                this.filterSavedAddressesByShippingMethod();
                this.setupFields();
            });
        });

        this.filterSavedAddressesByShippingMethod();
        this.setupFields();
    }

    currentShippingMethod() {
        const checkedRadio = document.querySelector('input[name="shipping_method"]:checked');

        if (checkedRadio) {
            return checkedRadio.value;
        }

        const select = document.querySelector('select[name="shipping_method"]');

        if (select) {
            return select.value;
        }

        return null;
    }

    isNovaPoshtaMethod(method) {
        return [
            this.config.shippingMethods.branch,
            this.config.shippingMethods.address,
            this.config.shippingMethods.postomat,
        ].includes(method);
    }

    isNovaPoshta() {
        return this.isNovaPoshtaMethod(this.currentShippingMethod());
    }

    isBranch() {
        return this.currentShippingMethod() === this.config.shippingMethods.branch;
    }

    isAddress() {
        return this.currentShippingMethod() === this.config.shippingMethods.address;
    }

    isPostomat() {
        return this.currentShippingMethod() === this.config.shippingMethods.postomat;
    }

    setupFields() {
        const usingSavedAddress = this.savedAddressNewRadio
            && !this.savedAddressNewRadio.checked
            && this.newAddressForm
            && this.newAddressForm.style.display === 'none';

        if (!this.isNovaPoshta()) {
            this.enableDefaultMode();
            return;
        }

        if (this.countryInput) {
            this.countryInput.value = this.config.defaults.country;
        }

        if (this.zipInput && !this.zipInput.value) {
            this.zipInput.value = this.config.defaults.zip;
        }

        this.areaInput.placeholder = this.config.messages.area_placeholder;

        this.cityInput.placeholder = this.config.messages.city_placeholder;
        this.cityInput.disabled = usingSavedAddress ? false : !this.areaInput.value;

        if (this.isBranch()) {
            this.setAddressLabel(this.config.messages.branch_label);
            this.addressInput.placeholder = this.config.messages.branch_placeholder;
            this.addressInput.disabled = usingSavedAddress ? false : !this.cityInput.value;
        }

        if (this.isPostomat()) {
            this.setAddressLabel(this.config.messages.postomat_label);
            this.addressInput.placeholder = this.config.messages.postomat_placeholder;
            this.addressInput.disabled = usingSavedAddress ? false : !this.cityInput.value;
        }

        if (this.isAddress()) {
            this.setAddressLabel(this.config.messages.street_label);
            this.addressInput.placeholder = this.config.messages.street_placeholder;
            this.addressInput.disabled = usingSavedAddress ? false : !this.cityInput.value;
        }

        this.clearResults(this.areaResults);
        this.clearResults(this.cityResults);
        this.clearResults(this.warehouseResults);
    }

    enableDefaultMode() {
        this.cityInput.disabled = false;
        this.addressInput.disabled = false;

        this.areaInput.placeholder = '';
        this.cityInput.placeholder = '';
        this.addressInput.placeholder = this.config.messages.street_placeholder || '';

        this.setAddressLabel(this.config.messages.street_label);

        this.clearResults(this.areaResults);
        this.clearResults(this.cityResults);
        this.clearResults(this.warehouseResults);
    }

    clearFieldsAfterMethodChanged(oldMethod, newMethod) {
        const oldIsNovaPoshta = this.isNovaPoshtaMethod(oldMethod);
        const newIsNovaPoshta = this.isNovaPoshtaMethod(newMethod);

        if (!newIsNovaPoshta) {
            return;
        }

        if (this.savedAddressNewRadio && !this.savedAddressNewRadio.checked) {
            return;
        }

        if (!oldIsNovaPoshta && newIsNovaPoshta) {
            this.areaInput.value = '';
            this.cityInput.value = '';
            this.addressInput.value = '';

            return;
        }

        if (oldIsNovaPoshta && newIsNovaPoshta && oldMethod !== newMethod) {
            this.addressInput.value = '';
        }
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
                this.areaInput.value = item.text;

                this.cityInput.value = '';
                this.addressInput.value = '';

                this.cityInput.disabled = false;
                this.addressInput.disabled = true;

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
                this.cityInput.value = item.name || item.text;

                this.addressInput.value = '';
                this.addressInput.disabled = false;

                this.clearResults(this.warehouseResults);
            });
        });
    }

    searchWarehouses(query = '') {
        if (!this.cityInput.value) {
            return;
        }

        this.request(this.config.urls.warehouses, {
            city: this.cityInput.value,
            type: this.isPostomat() ? 'postomat' : 'branch',
            q: query || '',
        }).then((items) => {
            this.renderResults(this.warehouseResults, items, (item) => {
                this.addressInput.value = item.text;
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

        items.forEach((item) => {
            const button = document.createElement('button');

            button.type = 'button';
            button.className = 'np-search-result';
            button.textContent = item.text;

            button.addEventListener('click', () => {
                onSelect(item);
                this.clearResults(container);
            });

            container.appendChild(button);
        });

        container.style.display = 'block';
    }

    clearResults(container) {
        if (!container) {
            return;
        }

        container.innerHTML = '';
        container.style.display = 'none';
    }

    getNpAddressTypeByShippingMethod(method = this.currentShippingMethod()) {
        if (method === this.config.shippingMethods.branch) {
            return 1;
        }

        if (method === this.config.shippingMethods.address) {
            return 2;
        }

        if (method === this.config.shippingMethods.postomat) {
            return 3;
        }

        return null;
    }

    filterSavedAddressesByShippingMethod() {
        const npAddressType = this.getNpAddressTypeByShippingMethod();

        this.savedAddressItems = document.querySelectorAll(this.config.selectors.savedAddressItem);
        this.savedAddressRadios = document.querySelectorAll(this.config.selectors.savedAddressRadio);
        this.savedAddressNewRadio = document.querySelector(this.config.selectors.savedAddressNewRadio);

        if (!this.savedAddressItems.length || !npAddressType) {
            this.showNewAddressForm(true);
            return;
        }

        let hasVisibleSavedAddress = false;
        let checkedVisibleAddress = null;
        let firstVisibleAddress = null;

        this.savedAddressItems.forEach((item) => {
            const itemType = item.dataset.npAddressType;
            const radio = item.querySelector('input[type="radio"]');

            const isNewAddressItem = itemType === 'all';
            const isMatchingAddress = parseInt(itemType) === parseInt(npAddressType);
            const shouldShow = isNewAddressItem || isMatchingAddress;

            item.style.display = shouldShow ? '' : 'none';

            if (radio) {
                radio.disabled = !shouldShow;

                if (!shouldShow) {
                    radio.checked = false;
                }
            }

            if (!shouldShow || isNewAddressItem) {
                return;
            }

            hasVisibleSavedAddress = true;

            if (!firstVisibleAddress) {
                firstVisibleAddress = radio;
            }

            if (radio && radio.checked) {
                checkedVisibleAddress = radio;
            }
        });

        if (!hasVisibleSavedAddress) {
            if (this.savedAddressNewRadio) {
                this.savedAddressNewRadio.disabled = false;
                this.savedAddressNewRadio.checked = true;
            }

            this.clearBillingAddressFields();
            this.showNewAddressForm(true);
            this.setupFields();

            return;
        }

        if (this.isNewAddressSelected()) {
            this.clearBillingAddressFields();
            this.showNewAddressForm(true);
            this.setupFields();

            return;
        }

        if (checkedVisibleAddress) {
            this.fillBillingAddressFromRadio(checkedVisibleAddress);
            this.showNewAddressForm(false);
            this.setupFields();

            return;
        }

        if (firstVisibleAddress) {
            firstVisibleAddress.checked = true;
            this.fillBillingAddressFromRadio(firstVisibleAddress);
            this.showNewAddressForm(false);
            this.setupFields();
        }
    }

    bindSavedAddressEvents() {
        document.addEventListener('change', (event) => {
            const radio = event.target.closest(this.config.selectors.savedAddressRadio);

            if (!radio) {
                return;
            }

            this.savedAddressNewRadio = document.querySelector(this.config.selectors.savedAddressNewRadio);

            if (radio.value === 'new') {
                this.clearBillingAddressFields();
                this.showNewAddressForm(true);
                this.setupFields();

                return;
            }

            this.fillBillingAddressFromRadio(radio);
            this.showNewAddressForm(false);
            this.setupFields();
        });
    }

    isNewAddressSelected() {
        return this.savedAddressNewRadio && this.savedAddressNewRadio.checked;
    }

    showNewAddressForm(show) {
        if (!this.newAddressForm) {
            return;
        }

        this.newAddressForm.style.display = show ? 'flex' : 'none';
    }

    fillBillingAddressFromRadio(radio) {
        if (!radio || radio.value === 'new') {
            return;
        }

        this.setInputValue('#billing-first-name', radio.dataset.firstName || '');
        this.setInputValue('#billing-last-name', radio.dataset.lastName || '');

        this.areaInput.value = radio.dataset.state || '';
        this.cityInput.value = radio.dataset.city || '';

        this.addressInput.value = radio.getAttribute('data-address-1') || '';

        if (this.countryInput) {
            this.countryInput.value = radio.dataset.country || this.config.defaults.country;
        }

        if (this.zipInput) {
            this.zipInput.value = radio.dataset.zip || this.config.defaults.zip;
        }

        this.enableBillingAddressFields();
    }

    clearBillingAddressFields() {
        this.setInputValue('#billing-first-name', '');
        this.setInputValue('#billing-last-name', '');

        this.areaInput.value = '';
        this.cityInput.value = '';
        this.addressInput.value = '';

        if (this.countryInput) {
            this.countryInput.value = this.config.defaults.country;
        }

        if (this.zipInput) {
            this.zipInput.value = this.config.defaults.zip;
        }
    }

    setInputValue(selector, value) {
        const input = document.querySelector(selector);

        if (input) {
            input.value = value || '';
        }
    }

    prepareForSubmit() {
        const selectedAddress = document.querySelector(
            `${this.config.selectors.savedAddressRadio}:checked`
        );

        if (selectedAddress && selectedAddress.value !== 'new') {
            this.fillBillingAddressFromRadio(selectedAddress);
            this.enableBillingAddressFields();

            console.log('NP selected address:', {
                address_1: this.addressInput.value,
                city: this.cityInput.value,
                state: this.areaInput.value,
            });

            return;
        }

        this.enableBillingAddressFields();
    }

    enableBillingAddressFields() {
        this.areaInput.disabled = false;
        this.cityInput.disabled = false;
        this.addressInput.disabled = false;

        const firstNameInput = document.querySelector('#billing-first-name');
        const lastNameInput = document.querySelector('#billing-last-name');

        if (firstNameInput) {
            firstNameInput.disabled = false;
        }

        if (lastNameInput) {
            lastNameInput.disabled = false;
        }

        if (this.countryInput) {
            this.countryInput.disabled = false;
        }

        if (this.zipInput) {
            this.zipInput.disabled = false;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.NPConfig !== 'undefined') {
        window.novaPoshtaCheckout = new NovaPoshtaCheckout(window.NPConfig);
        window.novaPoshtaCheckout.init();
    }
});
