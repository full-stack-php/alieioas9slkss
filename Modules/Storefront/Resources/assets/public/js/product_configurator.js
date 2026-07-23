export default class ProductConfigurator {
    constructor() {
        this.$modal = $('#product-configurator-modal');

        if (!this.$modal.length) {
            return;
        }

        this.$content = this.$modal.find(
            '.js-product-configurator-content'
        );

        this.modal = bootstrap.Modal.getOrCreateInstance(
            this.$modal[0]
        );

        this.routes = window.Korf.data.routes;
        this.translations = (
            window.Korf.data.productConfigurator || {}
        );

        this.$trigger = null;
        this.isSecondaryRequest = false;

        this.bindEvents();
    }

    bindEvents() {
        $(document).on(
            'click',
            '.js-product-configurator-open',
            (event) => {
                event.preventDefault();

                this.open($(event.currentTarget));
            }
        );

        $(document).on(
            'submit',
            '.js-product-configurator-form',
            (event) => {
                event.preventDefault();

                this.submit($(event.currentTarget));
            }
        );

        $(document).on(
            'change input',
            [
                '#product-configurator-modal select',
                '#product-configurator-modal input',
                '#product-configurator-modal textarea',
            ].join(','),
            () => {
                this.syncQuantityMax();
                this.recalculate();
            }
        );

        $(document).on(
            'click',
            '.js-product-configurator-plus',
            () => {
                this.changeQuantity(1);
            }
        );

        $(document).on(
            'click',
            '.js-product-configurator-minus',
            () => {
                this.changeQuantity(-1);
            }
        );

        $(document).on(
            'click',
            '.js-product-configurator-mirror-toggle',
            (event) => {
                this.toggleMirroredOptions(
                    $(event.currentTarget)
                );
            }
        );

        this.$modal.on('hidden.bs.modal', () => {
            this.$content.empty();
            this.$trigger = null;
            this.isSecondaryRequest = false;
        });
    }

    open($trigger) {
        const productId = $trigger.data('product-id');
        const routeTemplate = this.routes.product_cart_popup;

        if (!productId || !routeTemplate) {
            return;
        }

        this.$trigger = $trigger;

        this.renderLoading();
        this.modal.show();

        $trigger
            .prop('disabled', true)
            .addClass('loading');

        $.ajax({
            method: 'GET',

            url: routeTemplate.replace(
                '__PRODUCT__',
                encodeURIComponent(productId)
            ),
        })
            .done((html) => {
                this.$content.html(html);

                this.syncQuantityMax();
                this.recalculate();
            })
            .fail((xhr) => {
                this.renderLoadError(
                    xhr.responseJSON?.message
                    || this.translations.load_error
                );
            })
            .always(() => {
                $trigger
                    .prop('disabled', false)
                    .removeClass('loading');
            });
    }

    renderLoading() {
        const $loading = $('<div>', {
            class: 'product-configurator-loading',
        }).text(this.translations.loading || '');

        this.$content
            .empty()
            .append($loading);
    }

    renderLoadError(message) {
        const $error = $('<div>', {
            class: 'alert alert-danger',
            role: 'alert',
        }).text(
            message || this.translations.load_error || ''
        );

        this.$content
            .empty()
            .append($error);
    }

    toggleMirroredOptions($button) {
        const $form = $button.closest(
            '.js-product-configurator-form'
        );

        const $input = $form.find(
            'input[name="is_mirrored"]'
        );

        const enabled = $input.val() !== '1';

        $input.val(enabled ? '1' : '0');

        $form
            .find('.js-product-configurator-mirror')
            .stop(true, true)
            .slideToggle(200);

        $button.text(
            enabled
                ? $button.data('disable-label')
                : $button.data('enable-label')
        );

        this.recalculate();
    }

    changeQuantity(change) {
        const $quantity = this.$content.find(
            '.js-product-configurator-quantity'
        );

        if (!$quantity.length) {
            return;
        }

        const min = Math.max(
            1,
            this.toNumber($quantity.attr('min')) || 1
        );

        const max = Math.max(
            min,
            this.toNumber($quantity.attr('max')) || 999
        );

        const current = Math.max(
            min,
            this.toNumber($quantity.val()) || 1
        );

        const next = Math.min(
            max,
            Math.max(min, current + change)
        );

        $quantity.val(next);

        this.recalculate();
    }

    syncQuantityMax() {
        const $root = this.getRoot();
        const $form = this.getForm();
        const $quantity = $form.find(
            '.js-product-configurator-quantity'
        );

        if (
            !$root.length
            || !$form.length
            || !$quantity.length
        ) {
            return;
        }

        let maxQuantity = this.toNumber(
            $root.data('base-max-quantity')
        ) || 999;

        const $packaging = $form.find(
            'input[name="packaging_id"]:checked'
        );

        if ($packaging.length) {
            maxQuantity = this.toNumber(
                $packaging.data('max-quantity')
            ) || maxQuantity;
        }

        maxQuantity = Math.max(1, maxQuantity);

        $quantity.attr('max', maxQuantity);

        if (
            this.toNumber($quantity.val()) > maxQuantity
        ) {
            $quantity.val(maxQuantity);
        }

        if (this.toNumber($quantity.val()) < 1) {
            $quantity.val(1);
        }
    }

    recalculate() {
        const $root = this.getRoot();
        const $form = this.getForm();

        if (!$root.length || !$form.length) {
            return;
        }

        const quantity = Math.max(
            1,
            this.toNumber(
                $form.find(
                    '.js-product-configurator-quantity'
                ).val()
            ) || 1
        );

        const primaryGroups = $form.find(
            '[data-option-group][data-name-prefix="options"]'
        );

        const primaryElements = this.getSelectedPriceElements(
            primaryGroups
        );

        let totalUnitPrice = this.calculateUnitPrice(
            primaryElements
        );

        const mirrored = (
            $form.find(
                'input[name="is_mirrored"]'
            ).val() === '1'
        );

        if (mirrored) {
            const regularPrimaryGroups = primaryGroups.filter(
                '[data-option-mirrored="0"]'
            );

            const secondaryGroups = $form.find(
                '[data-option-group][data-name-prefix="m_options"]'
            );

            const secondaryElements = [
                ...this.getSelectedPriceElements(
                    regularPrimaryGroups
                ),
                ...this.getSelectedPriceElements(
                    secondaryGroups
                ),
            ];

            totalUnitPrice += this.calculateUnitPrice(
                secondaryElements
            );
        }

        const total = totalUnitPrice * quantity;

        $form
            .find('.js-product-configurator-total')
            .text(
                this.formatCurrency(total)
            );
    }

    calculateUnitPrice(selectedElements) {
        const $root = this.getRoot();
        const $form = this.getForm();

        const $packaging = $form.find(
            'input[name="packaging_id"]:checked'
        );

        if ($packaging.length) {
            return Math.max(
                0,
                this.toNumber(
                    $packaging.data('final-price')
                )
            );
        }

        let finalPrice = this.toNumber(
            $root.data('base-price')
        );

        for (const element of selectedElements) {
            const $element = $(element);

            const priceType = $element.data(
                'price-type'
            );

            const specialPriceType = $element.data(
                'special-price-type'
            );

            const price = this.toNumber(
                $element.data('price')
            );

            const specialPrice = this.toNumber(
                $element.data('special-price')
            );

            if (
                specialPriceType === 'fixed'
                && specialPrice > 0
            ) {
                finalPrice = specialPrice;
                break;
            }

            if (
                priceType === 'fixed'
                && price > 0
            ) {
                finalPrice = price;
                break;
            }
        }

        for (const element of selectedElements) {
            const $element = $(element);

            const priceType = $element.data(
                'price-type'
            );

            const specialPriceType = $element.data(
                'special-price-type'
            );

            const price = this.toNumber(
                $element.data('price')
            );

            const specialPrice = this.toNumber(
                $element.data('special-price')
            );

            if (
                specialPriceType === 'percent'
                && priceType === 'percent'
            ) {
                if (price > 0) {
                    finalPrice = (
                        finalPrice
                        * price
                        / 100
                    );
                }

                if (specialPrice > 0) {
                    finalPrice = (
                        finalPrice
                        * (1 - specialPrice / 100)
                    );
                }

                continue;
            }

            if (specialPriceType === 'percent') {
                if (specialPrice > 0) {
                    finalPrice = (
                        finalPrice
                        * (1 - specialPrice / 100)
                    );
                }

                continue;
            }

            if (
                priceType === 'percent'
                && price > 0
            ) {
                finalPrice = (
                    finalPrice
                    * price
                    / 100
                );
            }
        }

        return Math.max(0, finalPrice);
    }

    getSelectedPriceElements($groups) {
        const elements = [];

        $groups.each(function () {
            const $group = $(this);

            $group.find('select').each(function () {
                const $selected = $(this).find(
                    'option:selected[data-config-price]'
                );

                if (
                    $selected.length
                    && $selected.val()
                ) {
                    elements.push($selected[0]);
                }
            });

            $group.find(
                [
                    'input[type="radio"][data-config-price]:checked',
                    'input[type="checkbox"][data-config-price]:checked',
                ].join(',')
            ).each(function () {
                elements.push(this);
            });

            $group.find(
                [
                    'input[data-config-price]',
                    'textarea[data-config-price]',
                ].join(',')
            )
                .not(
                    'input[type="radio"], input[type="checkbox"]'
                )
                .each(function () {
                    if (String($(this).val() || '').trim()) {
                        elements.push(this);
                    }
                });
        });

        return elements;
    }

    submit($form) {
        this.clearErrors();

        const $submit = $form.find(
            '.js-product-configurator-submit'
        );

        const firstPayload = this.buildPayload(
            $form,
            'options'
        );

        const mirrored = (
            $form.find(
                'input[name="is_mirrored"]'
            ).val() === '1'
        );

        this.isSecondaryRequest = false;

        $submit
            .prop('disabled', true)
            .addClass('loading');

        let request = this.sendAddRequest(
            firstPayload
        );

        if (mirrored) {
            request = request.then(() => {
                this.isSecondaryRequest = true;

                const mirroredOptions = this.collectOptions(
                    $form,
                    'm_options'
                );

                const secondPayload = {
                    ...firstPayload,

                    options: {
                        ...firstPayload.options,
                        ...mirroredOptions,
                    },
                };

                return this.sendAddRequest(
                    secondPayload
                );
            });
        }

        request
            .done((response) => {
                const $trigger = this.$trigger;

                this.$modal.one(
                    'hidden.bs.modal',
                    () => {
                        if (window.cart) {
                            window.cart.open(
                                $trigger,
                                response
                            );
                        }
                    }
                );

                this.modal.hide();
            })
            .fail((xhr) => {
                this.renderErrors(
                    xhr,
                    this.isSecondaryRequest
                );
            })
            .always(() => {
                $submit
                    .prop('disabled', false)
                    .removeClass('loading');

                this.isSecondaryRequest = false;
            });
    }

    buildPayload($form, prefix) {
        return {
            product_id: $form
                .find('input[name="product_id"]')
                .val(),

            qty: Math.max(
                1,
                this.toNumber(
                    $form
                        .find('input[name="qty"]')
                        .val()
                ) || 1
            ),

            packaging_id: $form
                .find(
                    'input[name="packaging_id"]:checked'
                )
                .val() || null,

            options: this.collectOptions(
                $form,
                prefix
            ),
        };
    }

    collectOptions($form, prefix) {
        const options = {};

        $form
            .find(
                `[data-option-group][data-name-prefix="${prefix}"]`
            )
            .each(function () {
                const $group = $(this);
                const optionId = String(
                    $group.data('option-id')
                );

                const $select = $group.find(
                    `select[name^="${prefix}["]`
                );

                if ($select.length) {
                    const value = $select.val();

                    if (
                        Array.isArray(value)
                            ? value.length
                            : value
                    ) {
                        options[optionId] = value;
                    }

                    return;
                }

                const $checked = $group.find(
                    [
                        `input[type="radio"][name^="${prefix}["]:checked`,
                        `input[type="checkbox"][name^="${prefix}["]:checked`,
                    ].join(',')
                );

                if ($checked.length) {
                    const values = $checked
                        .map(function () {
                            return $(this).val();
                        })
                        .get();

                    options[optionId] = (
                        $checked.first().is(
                            'input[type="checkbox"]'
                        )
                            ? values
                            : values[0]
                    );

                    return;
                }

                const $field = $group.find(
                    [
                        `input[name^="${prefix}["]`,
                        `textarea[name^="${prefix}["]`,
                    ].join(',')
                ).first();

                if ($field.length) {
                    const value = String(
                        $field.val() || ''
                    ).trim();

                    if (value) {
                        options[optionId] = value;
                    }
                }
            });

        return options;
    }

    sendAddRequest(payload) {
        return $.ajax({
            method: 'POST',
            url: this.routes.cart_add,
            data: payload,
        });
    }

    renderErrors(xhr, secondaryRequest) {
        this.clearErrors();

        const response = xhr.responseJSON || {};
        const errors = response.errors || {};

        let renderedFieldError = false;

        Object.entries(errors).forEach(
            ([errorKey, messages]) => {
                let normalizedKey = errorKey.replace(
                    /^(options\.\d+)\.\d+$/,
                    '$1'
                );

                if (
                    secondaryRequest
                    && normalizedKey.startsWith(
                        'options.'
                    )
                ) {
                    const mirroredKey = normalizedKey.replace(
                        'options.',
                        'm_options.'
                    );

                    if (
                        this.findErrorContainer(
                            mirroredKey
                        ).length
                    ) {
                        normalizedKey = mirroredKey;
                    }
                }

                const $container = this.findErrorContainer(
                    normalizedKey
                );

                if (!$container.length) {
                    return;
                }

                renderedFieldError = true;

                $container.addClass('has-error');

                $container
                    .find(
                        'input, select, textarea'
                    )
                    .addClass('is-invalid');

                $container
                    .find(
                        '.js-configurator-field-error'
                    )
                    .removeClass('d-none')
                    .text(
                        Array.isArray(messages)
                            ? messages[0]
                            : messages
                    );
            }
        );

        if (!renderedFieldError) {
            this.showGeneralError(
                response.message
                || this.translations.add_error
            );
        }
    }

    findErrorContainer(errorKey) {
        return this.$content
            .find('[data-error-key]')
            .filter(function () {
                return (
                    String(
                        $(this).attr(
                            'data-error-key'
                        )
                    ) === String(errorKey)
                );
            })
            .first();
    }

    clearErrors() {
        this.$content
            .find('.has-error')
            .removeClass('has-error');

        this.$content
            .find('.is-invalid')
            .removeClass('is-invalid');

        this.$content
            .find(
                '.js-configurator-field-error'
            )
            .addClass('d-none')
            .text('');

        this.$content
            .find(
                '.js-product-configurator-error'
            )
            .addClass('d-none')
            .text('');
    }

    showGeneralError(message) {
        this.$content
            .find(
                '.js-product-configurator-error'
            )
            .removeClass('d-none')
            .text(
                message
                || this.translations.add_error
                || ''
            );
    }

    getRoot() {
        return this.$content.find(
            '.js-product-configurator'
        ).first();
    }

    getForm() {
        return this.$content.find(
            '.js-product-configurator-form'
        ).first();
    }

    formatCurrency(value) {
        const currency = (
            this.getRoot().data('currency')
            || 'UAH'
        );

        const locale = (
            document.documentElement.lang
            || 'uk'
        );

        try {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency,
                minimumFractionDigits: 0,
                maximumFractionDigits: 2,
            }).format(value);
        } catch (error) {
            return `${Number(value).toFixed(2)} ${currency}`;
        }
    }

    toNumber(value) {
        const number = Number.parseFloat(value);

        return Number.isFinite(number)
            ? number
            : 0;
    }
}
