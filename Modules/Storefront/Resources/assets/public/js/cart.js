export default class Cart {
    constructor() {
        this.baseUrl = window.Korf.data.baseUrl;
        this.routes = window.Korf.data.routes;

        this.selectors = {
            addToCartBtn: '#button-cart',
            productIdInput: 'input[name="product_id"]',
            qtyInput: 'input[name="quantity"]',
            optionsContainer: '.options',
            packagingContainer: '.product-packagings',
            packagingInput: 'input[name="packaging_id"]:checked',
            giftCheckboxes: '.gift-checkbox:checked:not(:disabled)',
            giftPackagingCheckboxes: '.gift-packaging-checkbox:checked:not(:disabled)',
            cartCount: '.cart-total',
            cartTotal: '.cart-total-value',
            cartWrapper: '.cart-content',
            cartScrollArea: '.header-cart-scroll'
        };

        this.init();
    }

    init() {
        this.setupAjax();
        this.bindEvents();
        window.cart = this;
    }

    setupAjax() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.Korf.data.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
    }

    bindEvents() {
        $(document).on('click', this.selectors.addToCartBtn, (e) => {
            e.preventDefault();
            this.handleAddToCart();
        });

        $(document).on('click', '.cart-close, .cart-overlay', () => this.close());

        // Обычный подарок может быть только один
        $(document).on('change', '.gift-checkbox', function () {
            if ($(this).is(':checked')) {
                $('.gift-checkbox').not(this).prop('checked', false);
            }
        });

        // Подарочные упаковки специально НЕ ограничиваем одной.
        // Их может быть несколько.
    }

    initStickyButton() {
        const $hcs = $(this.selectors.cartScrollArea);
        if (!$hcs.length) return;

        const checkScroll = (elem) => {
            return $(elem).prop('scrollHeight') - $(elem).innerHeight() - 55;
        };

        const $body = $('body');
        $body.removeClass('cart-is-sticky');

        if (checkScroll($hcs) > 0) {
            $body.addClass('cart-is-sticky');
        }

        $hcs.off('scroll').on('scroll', function () {
            if ($(this).scrollTop() >= checkScroll(this)) {
                $body.removeClass('cart-is-sticky');
            } else {
                $body.addClass('cart-is-sticky');
            }
        });
    }

    open(elem, data = null) {
        const $wrapper = $(this.selectors.cartWrapper);
        const $container = $('.shopping-cart');

        $('.box-account, #language .btn-group, #currency .btn-group').removeClass('open');
        $('body').addClass('no-scroll');
        $wrapper.removeClass('d-none');

        const renderCart = (response, event = null) => {
            if (response.html) {
                $wrapper.html(response.html);
            }

            this.refreshUI(response, event);

            setTimeout(() => {
                $container.addClass('cart-is-open');
                this.initStickyButton();
            }, 50);
        };

        if (data) {
            renderCart(data, 'addToCart');
        } else {
            $.get(this.routes.cart_get, (response) => {
                renderCart(response);
            });
        }
    }

    close() {
        $('body').removeClass('no-scroll cart-is-sticky');
        $('.cart-is-open').removeClass('cart-is-open');
        $(this.selectors.cartWrapper).addClass('d-none');
    }

    remove(id) {
        $.ajax({
            method: 'DELETE',
            url: `${this.baseUrl}/cart/items/${id}`,
            success: (response) => {
                this.refreshUI(response, 'removeProduct');
            }
        });
    }

    updateQuantity(id, qty) {
        if (qty < 1) return this.remove(id);

        $.ajax({
            method: 'PUT',
            url: `${this.baseUrl}/cart/items/${id}`,
            data: { qty },
            success: (response) => {
                this.refreshUI(response, 'updateQuantity');
            }
        });
    }

    handleAddToCart() {
        const $btn = $(this.selectors.addToCartBtn);
        const productId = $(this.selectors.productIdInput).val();
        const qty = $(this.selectors.qtyInput).val() || 1;
        const isMirrored = $('#is_mirrored').val() === '1';

        const packagingId = this.getSelectedPackagingId();
        const rawOptions = this.getRawOptions();
        const giftIds = this.getSelectedGiftIds();
        const giftPackagingIds = this.getSelectedGiftPackagingIds();

        $btn.prop('disabled', true).addClass('loading');

        if (isMirrored) {
            const firstEye = this.extractOptions(rawOptions, false);
            const secondEye = this.extractOptions(rawOptions, true);

            this.sendAddRequest({
                product_id: productId,
                qty: qty,
                options: firstEye,
                packaging_id: packagingId,
                ch_gifts: giftIds,
                gift_packaging_ids: giftPackagingIds
            })
                .then(() => {
                    return this.sendAddRequest({
                        product_id: productId,
                        qty: qty,
                        options: secondEye,
                        packaging_id: packagingId,
                        ch_gifts: [],
                        gift_packaging_ids: giftPackagingIds
                    });
                })
                .done((cart) => {
                    this.open($btn, cart);
                })
                .always(() => {
                    $btn.prop('disabled', false).removeClass('loading');
                });

            return;
        }

        const finalOptions = this.extractOptions(rawOptions, false);

        this.sendAddRequest({
            product_id: productId,
            qty: qty,
            options: finalOptions,
            packaging_id: packagingId,
            ch_gifts: giftIds,
            gift_packaging_ids: giftPackagingIds
        })
            .done((cart) => {
                this.open($btn, cart);
            })
            .always(() => {
                $btn.prop('disabled', false).removeClass('loading');
            });
    }

    getSelectedPackagingId() {
        return $(this.selectors.packagingInput).val() || null;
    }

    getSelectedGiftIds() {
        const giftIds = [];

        $(this.selectors.giftCheckboxes).each(function () {
            giftIds.push($(this).val());
        });

        return giftIds;
    }

    getSelectedGiftPackagingIds() {
        const giftPackagingIds = [];

        $(this.selectors.giftPackagingCheckboxes).each(function () {
            giftPackagingIds.push($(this).val());
        });

        return giftPackagingIds;
    }

    getRawOptions() {
        const rawOptions = {};

        $(this.selectors.optionsContainer)
            .find('select, input[type="radio"]:checked, input[type="hidden"]')
            .each(function () {
                const name = $(this).attr('name');
                const val = $(this).val();

                if (
                    val
                    && name
                    && (name.startsWith('options[') || name.startsWith('m_options['))
                ) {
                    rawOptions[name] = val;
                }
            });

        return rawOptions;
    }

    extractOptions(rawOptions, forMirror) {
        const result = {};

        Object.keys(rawOptions).forEach((name) => {
            const val = rawOptions[name];
            const match = name.match(/\[(\d+)\]/);

            if (!match) return;

            const id = match[1];
            const isMirrorInput = name.startsWith('m_options[');

            if (forMirror) {
                if (isMirrorInput) {
                    result[id] = val;
                } else if (!Object.prototype.hasOwnProperty.call(rawOptions, `m_options[${id}]`)) {
                    result[id] = val;
                }

                return;
            }

            if (!isMirrorInput) {
                result[id] = val;
            }
        });

        return result;
    }

    sendAddRequest(payload) {
        return $.ajax({
            method: 'POST',
            url: this.routes.cart_add,
            data: payload
        }).fail((xhr) => {
            alert(
                xhr.responseJSON?.message
                || window.Korf.data.cartAddError
            );
        });
    }

    refreshUI(data, event = null) {
        $.get(this.routes.cart_get, (res) => {
            $(this.selectors.cartWrapper).html(res.html);
            this.initStickyButton();
        });

        if (data.quantity !== undefined) {
            $(this.selectors.cartCount).text(data.quantity);
        }

        if (data.total && data.total.formatted) {
            $(this.selectors.cartTotal).text(data.total.formatted);
        }

        if (data.html) {
            $(this.selectors.cartWrapper).html(data.html);
            this.initStickyButton();
        }

        if (typeof window.Korf?.data?.cartManipulationCallback === 'function') {
            window.Korf.data.cartManipulationCallback(data, event);
        }

        $(document).trigger('cart:updated', [data]);
    }

    bundle_add(bundleProductId) {
        const productId = $(this.selectors.productIdInput).val();
        const $btn = $('.bundle-cart .btn');

        $btn.prop('disabled', true).addClass('loading');

        $.ajax({
            method: 'POST',
            url: `${this.baseUrl}/cart/items/bundle`,
            data: {
                product_id: productId,
                bundle_product_id: bundleProductId
            },
            success: (response) => {
                this.open($btn, response);
            },
            error: (xhr) => {
                alert(
                    xhr.responseJSON?.message
                    || window.Korf.data.bundleAddError
                );
            },
            complete: () => {
                $btn.prop('disabled', false).removeClass('loading');
            }
        });
    }
}
