import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

document.addEventListener('DOMContentLoaded', () => {
    const filterBox = document.getElementById('product-filter');
    let content = document.getElementById('product-listing-content');

    if (!filterBox || !content) {
        return;
    }

    let timer = null;
    let previewHtml = null;
    let previewFilterHtml = null;
    let previewDoc = null;
    let previewUrl = null;
    let activeController = null;
    let priceSliderChanging = false;
    let lastPopoverTarget = null;

    const mobileMedia = window.matchMedia('(max-width: 767px)');
    let mobileStartX = 0;
    let mobileStartY = 0;

    const isMobileFilter = () => mobileMedia.matches;

    const openMobileFilter = () => {
        filterBox.classList.add('ocf-mobile-active');
        document.body.classList.add('ocf-overflow-hidden');
    };

    const closeMobileFilter = () => {
        filterBox.classList.remove('ocf-mobile-active');
        document.body.classList.remove('ocf-overflow-hidden');
        hidePopover();
    };

    const updateStaticButton = (count) => {
        const button = filterBox.querySelector('.ocf-search-btn-static');

        if (!button) {
            return;
        }

        button.disabled = false;
        button.classList.remove('ocf-disabled');

        if (count === null || count === undefined) {
            button.innerHTML = 'Показать';
            return;
        }

        button.innerHTML = `Показать <b class="ocf-btn-label">${count}</b> товаров`;
    };

    const setStaticButtonLoading = () => {
        const button = filterBox.querySelector('.ocf-search-btn-static');

        if (!button) {
            return;
        }

        button.disabled = true;
        button.classList.add('ocf-disabled');
        button.innerHTML = 'Загрузка...';
    };

    const filterKeys = [
        'price',
        'manufacturers',
        'manufacturer',
        'attribute',
        'attributes',
        'has_discount',
        'specials',
        'page',
    ];

    const getForm = () => document.getElementById('product-filter-form');

    const cleanParams = (params) => {
        for (const key of Array.from(params.keys())) {
            if (filterKeys.some(filterKey => key === filterKey || key.startsWith(filterKey + '['))) {
                params.delete(key);
            }
        }
    };
    const getBaseFilters = () => {
        const form = getForm();

        return {
            attribute: form?.dataset.baseAttribute || '',
            manufacturers: form?.dataset.baseManufacturers || '',
            hasDiscount: form?.dataset.baseHasDiscount || '',
            priceMin: form?.dataset.basePriceMin || '',
            priceMax: form?.dataset.basePriceMax || '',
        };
    };

    const isSeoFilterPage = () => {
        const form = getForm();

        return form?.dataset.isSeoFilter === '1';
    };

    const isBrandLandingPage = () => {
        const form = getForm();

        return form?.dataset.isBrandLanding === '1';
    };

    const getListingUrl = () => {
        const form = getForm();

        return form?.dataset.listingUrl || window.location.href;
    };

    const getDiscountToken = (formData) => {
        return formData.has('has_discount') || formData.has('specials') ? '1' : '';
    };

    const shouldLeaveSeoFilterUrl = (baseFilters, attributeToken, manufacturerToken, discountToken) => {
        if (!isSeoFilterPage()) {
            return false;
        }

        return attributeToken !== baseFilters.attribute
            || manufacturerToken !== baseFilters.manufacturers
            || discountToken !== baseFilters.hasDiscount;
    };

    const getPriceDefaults = () => {
        const slider = filterBox.querySelector('.js-price-slider');

        if (!slider) {
            return {
                min: null,
                max: null,
            };
        }

        return {
            min: Number(slider.dataset.min),
            max: Number(slider.dataset.max),
        };
    };

    const shouldAppendPrice = (key, value) => {
        if (value === '') {
            return false;
        }

        const baseFilters = getBaseFilters();

        if (key === 'price[min]' && baseFilters.priceMin !== '') {
            return String(value) !== String(baseFilters.priceMin);
        }

        if (key === 'price[max]' && baseFilters.priceMax !== '') {
            return String(value) !== String(baseFilters.priceMax);
        }

        const priceDefaults = getPriceDefaults();
        const numericValue = Number(value);

        if (Number.isNaN(numericValue)) {
            return false;
        }

        if (key === 'price[min]') {
            return priceDefaults.min === null || numericValue > priceDefaults.min;
        }

        if (key === 'price[max]') {
            return priceDefaults.max === null || numericValue < priceDefaults.max;
        }

        return true;
    };

    const buildManufacturerToken = () => {
        const ids = [];

        filterBox
            .querySelectorAll('input[name="manufacturers[]"]:checked')
            .forEach((input) => {
                if (input.value !== '') {
                    ids.push(Number(input.value));
                }
            });

        return [...new Set(ids)]
            .filter(id => !Number.isNaN(id))
            .sort((a, b) => a - b)
            .map(id => `M${id}`)
            .join('');
    };

    const buildAttributeToken = () => {
        const groups = new Map();

        filterBox
            .querySelectorAll('input[data-filter="attribute"]:checked')
            .forEach((input) => {
                const attributeId = Number(input.dataset.attributeId);
                const valueId = Number(input.dataset.valueId);

                if (!attributeId || !valueId) {
                    return;
                }

                if (!groups.has(attributeId)) {
                    groups.set(attributeId, []);
                }

                groups.get(attributeId).push(valueId);
            });

        let token = '';

        [...groups.keys()]
            .sort((a, b) => a - b)
            .forEach((attributeId) => {
                token += `F${attributeId}`;

                [...new Set(groups.get(attributeId))]
                    .sort((a, b) => a - b)
                    .forEach((valueId) => {
                        token += `V${valueId}`;
                    });
            });

        return token;
    };

    const shouldLeaveBrandLandingUrl = (
        baseFilters,
        attributeToken,
        manufacturerToken,
        discountToken,
        priceChanged
    ) => {
        if (!isBrandLandingPage()) {
            return false;
        }

        return priceChanged
            || attributeToken !== baseFilters.attribute
            || manufacturerToken !== baseFilters.manufacturers
            || discountToken !== baseFilters.hasDiscount;
    };

    const buildUrl = () => {
        const form = getForm();
        const formData = new FormData(form);
        const baseFilters = getBaseFilters();

        const attributeToken = buildAttributeToken();
        const manufacturerToken = buildManufacturerToken();
        const discountToken = getDiscountToken(formData);

        const priceChanged = ['price[min]', 'price[max]'].some((key) => {
            return formData.has(key) && shouldAppendPrice(key, formData.get(key));
        });

        const shouldLeaveLanding = shouldLeaveSeoFilterUrl(
            baseFilters,
            attributeToken,
            manufacturerToken,
            discountToken
        ) || shouldLeaveBrandLandingUrl(
            baseFilters,
            attributeToken,
            manufacturerToken,
            discountToken,
            priceChanged
        );

        const url = new URL(
            shouldLeaveLanding ? getListingUrl() : window.location.href
        );

        const params = new URLSearchParams(url.search);

        cleanParams(params);

        for (const [key, value] of formData.entries()) {
            if (
                key === 'attribute' ||
                key === 'attributes' ||
                key.startsWith('attribute[') ||
                key.startsWith('attributes[')
            ) {
                continue;
            }

            if (
                key === 'manufacturers' ||
                key === 'manufacturer' ||
                key === 'manufacturers[]' ||
                key.startsWith('manufacturers[') ||
                key.startsWith('manufacturer[')
            ) {
                continue;
            }

            if (
                key === 'has_discount' ||
                key === 'specials'
            ) {
                continue;
            }

            if (key === 'price[min]' || key === 'price[max]') {
                if (shouldAppendPrice(key, value)) {
                    params.append(key, value);
                }

                continue;
            }

            if (value !== '') {
                params.append(key, value);
            }
        }

        if (
            attributeToken !== '' &&
            (shouldLeaveLanding || attributeToken !== baseFilters.attribute)
        ) {
            params.set('attribute', attributeToken);
        }

        if (
            manufacturerToken !== '' &&
            (shouldLeaveLanding || manufacturerToken !== baseFilters.manufacturers)
        ) {
            params.set('manufacturers', manufacturerToken);
        }

        if (
            discountToken !== '' &&
            (shouldLeaveLanding || discountToken !== baseFilters.hasDiscount)
        ) {
            params.set('has_discount', discountToken);
        }

        url.search = params.toString();

        return url;
    };

    const getPopover = () => {
        let popover = filterBox.querySelector('#oc-filter-popover');

        if (popover) {
            return popover;
        }

        popover = document.createElement('div');
        popover.className = 'ocf-popover ocf-right';
        popover.id = 'oc-filter-popover';
        popover.setAttribute('role', 'ocf-popover');
        popover.innerHTML = `
            <div class="ocf-arrow"></div>
            <div class="ocf-popover-content">
                <button type="button" class="ocf-btn ocf-search-btn-popover">
                    Показать <b class="ocf-btn-label">0</b> товаров
                </button>
            </div>
        `;

        filterBox.appendChild(popover);

        popover.querySelector('.ocf-search-btn-popover').addEventListener('click', applyPreview);

        return popover;
    };

    const hidePopover = () => {
        const popover = filterBox.querySelector('#oc-filter-popover');

        if (!popover) {
            return;
        }

        popover.classList.remove('show', 'ocf-in');
        popover.style.display = 'none';
    };

    const positionPopover = (target) => {
        const popover = getPopover();

        if (!target || !filterBox.contains(target)) {
            return;
        }

        lastPopoverTarget = target;

        const filterRect = filterBox.getBoundingClientRect();
        const targetRect = target.getBoundingClientRect();

        popover.style.display = 'block';
        popover.classList.remove('ocf-left', 'ocf-right');
        popover.classList.add('ocf-right');

        let top = targetRect.top - filterRect.top + filterBox.scrollTop + (targetRect.height / 2) - 24;
        let left = targetRect.right - filterRect.left + 12;

        popover.style.top = `${top}px`;
        popover.style.left = `${left}px`;

        const popoverRect = popover.getBoundingClientRect();

        if (popoverRect.right > window.innerWidth - 10) {
            left = targetRect.left - filterRect.left - popoverRect.width - 12;

            popover.classList.remove('ocf-right');
            popover.classList.add('ocf-left');
            popover.style.left = `${left}px`;
        }

        requestAnimationFrame(() => {
            popover.classList.add('show', 'ocf-in');
        });
    };

    const setPopoverLoading = (target) => {
        const popover = getPopover();
        const button = popover.querySelector('.ocf-search-btn-popover');

        button.disabled = true;
        button.classList.add('ocf-disabled');
        button.innerHTML = 'Загрузка...';

        positionPopover(target);
    };

    const setPopoverResult = (count, target) => {
        const popover = getPopover();
        const button = popover.querySelector('.ocf-search-btn-popover');

        button.disabled = false;
        button.classList.remove('ocf-disabled');

        button.innerHTML = `Показать <b class="ocf-btn-label">${count}</b> товаров`;

        positionPopover(target);
    };

    const getTotalFromDocument = (doc) => {
        const newContent = doc.getElementById('product-listing-content');

        if (!newContent) {
            return 0;
        }

        const total = Number(newContent.dataset.total);

        if (!Number.isNaN(total)) {
            return total;
        }

        return newContent.querySelectorAll('.product-layout').length;
    };

    const filterInputKey = (input) => {
        if (!input) {
            return '';
        }

        if (input.dataset.filter === 'attribute') {
            return [
                'attribute',
                input.dataset.attributeId || '',
                input.dataset.valueId || '',
            ].join(':');
        }

        if (input.name === 'manufacturers[]') {
            return [
                'manufacturers',
                input.value || '',
            ].join(':');
        }

        if (input.name === 'has_discount' || input.name === 'specials') {
            return 'has_discount';
        }

        return [
            input.name || '',
            input.value || '',
        ].join(':');
    };

    const syncFilterCountsFromDocument = (newFilter) => {
        if (!newFilter) {
            return;
        }

        const currentInputs = filterBox.querySelectorAll(
            '.form-check-input:not(.js-price-min):not(.js-price-max)'
        );

        currentInputs.forEach((currentInput) => {
            const key = filterInputKey(currentInput);

            if (!key) {
                return;
            }

            const newInput = [...newFilter.querySelectorAll('.form-check-input')]
                .find((input) => filterInputKey(input) === key);

            if (!newInput) {
                return;
            }

            const currentLabel = currentInput.closest('.form-check');
            const newLabel = newInput.closest('.form-check');

            if (!currentLabel || !newLabel) {
                return;
            }

            const currentCount = currentLabel.querySelector('.filter-count');
            const newCount = newLabel.querySelector('.filter-count');

            if (currentCount && newCount) {
                currentCount.textContent = newCount.textContent;
            }

            currentInput.disabled = newInput.disabled;

            currentLabel.classList.toggle(
                'ocf-disabled',
                newLabel.classList.contains('ocf-disabled')
            );
        });
    };

    const previewProducts = async (target) => {
        const url = buildUrl();

        previewUrl = url;
        previewHtml = null;
        previewFilterHtml = null;
        previewDoc = null;

        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();

        if (isMobileFilter()) {
            hidePopover();
            setStaticButtonLoading();
        } else {
            setPopoverLoading(target);
        }

        try {
            const response = await fetch(url.toString(), {
                signal: activeController.signal,
                headers: {
                    'Accept': 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.redirected && response.url) {
                previewUrl = new URL(response.url);
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            previewDoc = doc;

            const newContent = doc.getElementById('product-listing-content');
            const newFilter = doc.getElementById('product-filter');

            if (!newContent) {
                hidePopover();
                return;
            }

            previewHtml = newContent.innerHTML;

            if (newFilter) {
                previewFilterHtml = newFilter.innerHTML;
                syncFilterCountsFromDocument(newFilter);
            }

            const total = getTotalFromDocument(doc);

            updateStaticButton(total);

            if (!isMobileFilter()) {
                setPopoverResult(total, target);
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                hidePopover();
                updateStaticButton(null);
            }
        }
    };

    function applyPreview() {
        if (!previewUrl) {
            window.location.href = buildUrl().toString();
            return;
        }

        content = document.getElementById('product-listing-content');

        if (!content || previewHtml === null) {
            window.location.href = previewUrl.toString();
            return;
        }

        content.classList.add('is-loading');

        content.innerHTML = previewHtml;

        if (previewFilterHtml !== null) {
            filterBox.innerHTML = previewFilterHtml;
            initPriceSlider();
        }

        syncSeoFromDocument(previewDoc);

        window.history.pushState({}, '', previewUrl.toString());

        content.classList.remove('is-loading');

        hidePopover();

        if (typeof window.ProStickerLoad === 'function') {
            window.ProStickerLoad();
        }
    }

    const replaceHtmlFromDocument = (selector, doc) => {
        const currentElement = document.querySelector(selector);
        const newElement = doc.querySelector(selector);

        if (!currentElement || !newElement) {
            return;
        }

        currentElement.innerHTML = newElement.innerHTML;
    };

    const syncMetaTag = (selector, doc) => {
        const currentTag = document.head.querySelector(selector);
        const newTag = doc.head.querySelector(selector);

        if (!currentTag || !newTag) {
            return;
        }

        currentTag.setAttribute('content', newTag.getAttribute('content') || '');
    };

    const syncSeoFromDocument = (doc) => {
        if (!doc) {
            return;
        }

        if (doc.title) {
            document.title = doc.title;
        }

        syncMetaTag('meta[name="robots"]', doc);
        syncMetaTag('meta[name="title"]', doc);
        syncMetaTag('meta[name="description"]', doc);
        syncMetaTag('meta[property="og:url"]', doc);
        syncMetaTag('meta[property="og:title"]', doc);
        syncMetaTag('meta[property="og:description"]', doc);

        replaceHtmlFromDocument('#listing-breadcrumbs', doc);
        replaceHtmlFromDocument('#listing-heading', doc);
        replaceHtmlFromDocument('#listing-subcategories', doc);
        replaceHtmlFromDocument('#listing-seo-content', doc);
    };

    const initPriceSlider = () => {
        const slider = filterBox.querySelector('.js-price-slider');
        const minInput = filterBox.querySelector('.js-price-min');
        const maxInput = filterBox.querySelector('.js-price-max');

        if (!slider || !minInput || !maxInput) {
            return;
        }

        if (slider.noUiSlider) {
            slider.noUiSlider.destroy();
        }

        const min = Number(slider.dataset.min || 0);
        const max = Number(slider.dataset.max || 0);

        let startMin = slider.dataset.startMin !== undefined && slider.dataset.startMin !== ''
            ? Number(slider.dataset.startMin)
            : min;

        let startMax = slider.dataset.startMax !== undefined && slider.dataset.startMax !== ''
            ? Number(slider.dataset.startMax)
            : max;

        if (max <= min) {
            slider.style.display = 'none';
            return;
        }

        startMin = Math.max(min, Math.min(startMin, max));
        startMax = Math.max(min, Math.min(startMax, max));

        noUiSlider.create(slider, {
            start: [startMin, startMax],
            connect: true,
            step: 1,
            range: {
                min,
                max,
            },
            format: {
                to: value => Math.round(value),
                from: value => Number(value),
            },
        });

        slider.noUiSlider.on('update', (values) => {
            priceSliderChanging = true;
            minInput.value = values[0];
            maxInput.value = values[1];
            priceSliderChanging = false;
        });

        slider.noUiSlider.on('change', () => {
            previewProducts(slider);
        });

        minInput.addEventListener('change', () => {
            if (priceSliderChanging) {
                return;
            }

            slider.noUiSlider.set([minInput.value || min, null]);
            previewProducts(minInput);
        });

        maxInput.addEventListener('change', () => {
            if (priceSliderChanging) {
                return;
            }

            slider.noUiSlider.set([null, maxInput.value || max]);
            previewProducts(maxInput);
        });
    };

    filterBox.addEventListener('change', (event) => {
        const target = event.target;

        if (!target.matches('input, select')) {
            return;
        }

        if (target.classList.contains('js-price-min') || target.classList.contains('js-price-max')) {
            return;
        }

        clearTimeout(timer);

        timer = setTimeout(() => {
            const anchor = target.closest('.form-check') || target;
            previewProducts(anchor);
        }, 250);
    });

    filterBox.addEventListener('submit', (event) => {
        event.preventDefault();
        applyPreview();
    });



    document.addEventListener('click', (event) => {
        const openButton = event.target.closest('[data-ocf-mobile-open]');
        const closeButton = event.target.closest('[data-ocf-mobile-close]');

        if (openButton) {
            event.preventDefault();
            openMobileFilter();
            return;
        }

        if (closeButton) {
            event.preventDefault();
            closeMobileFilter();
            return;
        }

        if (
            isMobileFilter() &&
            filterBox.classList.contains('ocf-mobile-active') &&
            !filterBox.contains(event.target)
        ) {
            event.preventDefault();
            closeMobileFilter();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileFilter();
        }
    });

    filterBox.addEventListener('touchstart', (event) => {
        const touch = event.changedTouches[0];

        mobileStartX = touch.screenX;
        mobileStartY = touch.screenY;
    }, { passive: true });

    filterBox.addEventListener('touchend', (event) => {
        if (!isMobileFilter() || !filterBox.classList.contains('ocf-mobile-active')) {
            return;
        }

        const touch = event.changedTouches[0];
        const diffX = touch.screenX - mobileStartX;
        const diffY = touch.screenY - mobileStartY;

        const ratioX = Math.abs(diffX / diffY);
        const ratioY = Math.abs(diffY / diffX);
        const absDiff = Math.abs(ratioX > ratioY ? diffX : diffY);

        if (absDiff < 30 || ratioX <= ratioY) {
            return;
        }

        if (diffX < 0) {
            closeMobileFilter();
        }
    }, { passive: true });

    document.addEventListener('click', (event) => {
        const popover = filterBox.querySelector('#oc-filter-popover');

        if (!popover) {
            return;
        }

        if (
            popover.contains(event.target) ||
            filterBox.contains(event.target)
        ) {
            return;
        }

        hidePopover();
    });

    window.addEventListener('resize', () => {
        if (!isMobileFilter()) {
            closeMobileFilter();

            if (lastPopoverTarget) {
                positionPopover(lastPopoverTarget);
            }
        }
    });

    initPriceSlider();
});
