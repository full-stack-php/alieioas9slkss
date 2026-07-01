(function($) {
    var OnePageCheckout = function(options) {

        var self = this;
        self.$elem = $('#main-checkout-form');
        self.options = options || {};
        self.$sfields = $('.checkout_form input[type=\'text\'], .checkout-address input[type=\'radio\'], .checkout-address select', self.$elem);
        self.clickSelectors = '.cart-list .btn, [id^="button-"], input[name="register"]';

        self.form = {};
        self.placingOrder = false;
        self.authorizeNetToken = null;
        self.stripe = null;
        self.stripeElements = null;

        self.init = function() {
            self.response();
            self.initSelect2();
            self.attachEventHandlers();
            self.bindCartUpdated();
            self.initRelatedSlider();
            self.initMaskPhone();
            self.initDateTimePicker();
            self.initAccountToggle();

            if ($('input[name="payment_method"]:checked').val() === 'paypal') {
                self.renderPayPalButton();
            }
        };

        self.initAccountToggle = function() {
            var $checkbox = $('#create-an-account');
            var $form = $('#create-account-form');

            $checkbox.on('change', function() {
                if ($(this).is(':checked')) {
                    $form.slideDown();
                } else {
                    $form.slideUp();
                }
            });
        };

        self.syncNativeForm = function() {
            if (!self.$elem.length) return;

            if (window.novaPoshtaCheckout && typeof window.novaPoshtaCheckout.prepareForSubmit === 'function') {
                window.novaPoshtaCheckout.prepareForSubmit();
            }

            var formData = new FormData(self.$elem[0]);

            self.form.billing = {};
            self.form.shipping = {};

            for (let [key, value] of formData.entries()) {
                let billingMatch = key.match(/^billing\[(.*?)\]$/);
                let shippingMatch = key.match(/^shipping\[(.*?)\]$/);

                if (billingMatch) {
                    self.form.billing[billingMatch[1]] = value;
                } else if (shippingMatch) {
                    self.form.shipping[shippingMatch[1]] = value;
                } else {
                    self.form[key] = value;
                }
            }

            self.form.ship_to_a_different_address = $('#ship-to-a-different-address').is(':checked') ? 1 : 0;
            self.form.terms_and_conditions = $('input[name="terms_and_conditions"]').is(':checked') ? 1 : 0;
        };

        self.attachEventHandlers = function(){

            self.$elem.on('click', '#place-order-btn', function (e) {
                e.preventDefault();

                self.placeOrder();
            });

            self.$elem.on('submit', function (e) {
                e.preventDefault();

                self.placeOrder();
            });

            self.$elem.on('click', self.clickSelectors, function (e) {
                e.preventDefault();

                var $target = $(this);

                if ($target.hasClass('btn') && $target.closest('.cart-list').length > 0) {
                    var action = $target.data('action');
                    var key = $target.data('key');

                    if (action === 'minus' || action === 'plus') {
                        self.plusMinusQty($target, action);
                    } else if (action === 'remove') {
                        self.removeProduct(key);
                    }
                } else if ($target.attr('id') && $target.attr('id').startsWith('button-')) {
                    var buttonId = $target.attr('id');

                    if (buttonId === 'button-coupon') {
                        self.handleCouponButtonClick();
                    } else if (buttonId === 'button-register' || buttonId === 'button-confirm') {
                        self.placeOrder();
                    }
                }
            });

            self.bindCartUpdated = function () {
                $(document).on('cart-updated', function (event, data) {
                    if (self.isCartEmpty(data)) {
                        self.redirectToCart();
                        return;
                    }

                    self.updateExistingCartRows(data);
                    self.renderShippingMethods(data);
                    self.renderTotals(data);
                    self.refreshSelectedShippingMethod(data);
                });
            };

            self.updateCartRow = function ($row, cartItem) {
                var qty = parseInt(cartItem.qty) || 1;
                var unitPrice = cartItem.unitPrice?.formatted || '';
                var total = cartItem.total?.formatted || '';

                var hasDiscountedPrice = cartItem.has_discounted_price === true;
                var regularUnitPrice = cartItem.regularUnitPrice?.formatted || '';

                var unitPriceHtml = hasDiscountedPrice
                    ? `<span class="price-old">${regularUnitPrice}</span> <span class="price-new">${unitPrice}</span>`
                    : `<span class="price-new">${unitPrice}</span>`;

                $row.find('.opc-cart-qty-input').val(qty);

                $row.find('[data-cart-unit-price-html]').html(unitPriceHtml);

                $row.find('[data-cart-line-total-html]').html(
                    `<span class="text-cart-item-total">${self.options.messages.total || 'Всего'}</span>${total}`
                );
            };

            self.updateExistingCartRows = function (data) {
                if (!data.items) {
                    return;
                }

                $.each(data.items, function (key, cartItem) {
                    var itemKey = cartItem.id || key;

                    var $row = $('[data-cart-item]').filter(function () {
                        return String($(this).attr('data-cart-item')) === String(itemKey);
                    });

                    if (!$row.length) {
                        return;
                    }

                    self.updateCartRow($row, cartItem);
                });
            };

            self.isCartEmpty = function (data) {
                if (!data) {
                    return false;
                }

                if (typeof data.quantity !== 'undefined') {
                    return parseInt(data.quantity) <= 0;
                }

                if (Array.isArray(data.items)) {
                    return data.items.length === 0;
                }

                if (data.items && typeof data.items === 'object') {
                    return Object.keys(data.items).length === 0;
                }

                return false;
            };

            self.redirectToCart = function () {
                window.location.href = self.options.cartUrl || '/cart';
            };

            self.renderCart = function (data) {
                if (self.isCartEmpty(data)) {
                    self.redirectToCart();
                    return;
                }

                if (!data.items) {
                    return;
                }

                var html = '';

                $.each(data.items, function (key, cartItem) {
                    html += self.renderCartItem(key, cartItem);
                });

                $('.cart-list').html(html);
            };

            self.renderCartItem = function (key, cartItem) {
                var product = cartItem.product || {};
                var image = product.base_image && product.base_image.path ? product.base_image.path : '';
                var productUrl = product.slug ? `/product/${product.slug}` : '#';
                var qty = parseInt(cartItem.qty) || 1;
                var minQty = parseInt(cartItem.item?.minimum) || 1;

                var unitPrice = cartItem.unitPrice?.formatted || '';
                var total = cartItem.total?.formatted || '';

                var hasDiscountedPrice = cartItem.has_discounted_price === true;
                var regularUnitPrice = cartItem.regularUnitPrice?.formatted || '';
                var regularTotal = cartItem.regularTotal?.formatted || '';

                var unitPriceHtml = hasDiscountedPrice
                    ? `<span class="price-old">${regularUnitPrice}</span> <span class="price-new">${unitPrice}</span>`
                    : `<span class="price-new">${unitPrice}</span>`;

                var minusQty = qty - minQty;
                if (minusQty < minQty) {
                    minusQty = minQty;
                }

                var plusQty = qty + minQty;

                return `
        <div class="cart-item d-flex" data-cart-item="${key}">
            <div class="cart-item-left">
                <a href="${productUrl}">
                    ${image ? `<img src="${image}" alt="${self.escapeHtml(product.name || '')}" class="img-responsive" width="60" height="60">` : ''}
                </a>
            </div>

            <div class="cart-item-center d-flex flex-column">
                <div class="cart-item-prod-name">
                    <a href="${productUrl}">
                        ${self.escapeHtml(product.name || '')}
                    </a>
                </div>

                ${self.renderCartItemOptions(cartItem)}
                ${self.renderCartItemPackaging(cartItem)}
            </div>

            <div class="cart-item-price-quantity d-flex">
                <div class="d-flex justify-content-end align-items-center">
                   <div class="ch-cart-quantity border rounded">
                    <span class="input-btn">
                        <button
                            class="btn btn-quantity-minus opc-btn-update"
                            type="button"
                            data-action="minus"
                            data-key="${key}"
                        >
                            <svg class="icon icon-14">
                                <use xlink:href="#angel-left"></use>
                            </svg>
                        </button>
                    </span>

                    <input
                        type="text"
                        class="form-control opc-cart-qty-input"
                        value="${qty}"
                        data-key="${key}"
                        data-minimum="${minQty}"
                        inputmode="numeric"
                        autocomplete="off"
                    >

                    <span class="input-btn">
                        <button
                            class="btn btn-quantity-plus opc-btn-update"
                            type="button"
                            data-action="plus"
                            data-key="${key}"
                        >
                            <svg class="icon icon-14">
                                <use xlink:href="#angel-right"></use>
                            </svg>
                        </button>
                    </span>
                </div>

                    <button
                        type="button"
                        class="btn btn-remove opc-btn-remove"
                        data-action="remove"
                        data-key="${key}"
                    >
                        <svg class="icon icon-11">
                            <use xlink:href="#cross"></use>
                        </svg>
                    </button>
                </div>

                <div class="cart-totals d-flex">
                    <div class="cart-item-price">
                        <span class="text-cart-item-price">${self.options.messages.price || 'Цена'}</span>
                        ${unitPriceHtml}
                    </div>

                    <div class="cart-item-total">
                        <span class="text-cart-item-total">${self.options.messages.total || 'Всего'}</span>
                        ${total}
                    </div>
                </div>
            </div>
        </div>
    `;
            };

            self.renderCartItemOptions = function (cartItem) {
                if (!cartItem.options || !Object.keys(cartItem.options).length) {
                    return '';
                }

                var html = '<div class="cart-item-options">';

                $.each(cartItem.options, function (key, option) {
                    var values = option.values || [];

                    $.each(values, function (index, value) {
                        html += `
                <div class="cart-item-option d-flex">
                    <div class="cart-item-option-name">${self.escapeHtml(option.name || '')}:</div>
                    <div class="cart-item-option-value">${self.escapeHtml(value.label || '')}</div>
                </div>
            `;
                    });
                });

                html += '</div>';

                return html;
            };

            self.renderCartItemPackaging = function (cartItem) {
                if (!cartItem.packaging || !Object.keys(cartItem.packaging).length) {
                    return '';
                }

                return '';
            };

            self.renderShippingMethods = function (data) {
                if (!data.availableShippingMethods) {
                    return;
                }

                var selectedMethod = data.shippingMethodName || $('input[name="shipping_method"]:checked').val();
                var shippingLabel = self.getShippingLabel(data);
                var html = '';

                $.each(data.availableShippingMethods, function (name, method) {
                    var checked = selectedMethod === name ? 'checked="checked"' : '';

                    html += `
            <div class="radio chm-radio shipping-method-item">
                <label for="sm_${name}">
                    <input
                        type="radio"
                        name="shipping_method"
                        id="sm_${name}"
                        value="${name}"
                        class="shipping-method-input"
                        ${checked}
                    >
                    <span class="checkbox-radio"></span>

                    <span class="shipping-method-name">
                        ${self.escapeHtml(method.label || '')}
                    </span>

                    <span class="shipping-method-cost" style="margin-left: 5px; font-weight: bold;">
                        — ${self.escapeHtml(shippingLabel)}
                    </span>
                </label>
            </div>
        `;
                });

                $('#shipping-methods-container').html(html);

                if (window.novaPoshtaCheckout) {
                    window.novaPoshtaCheckout.refreshShippingMethods();
                }
            };

            self.getShippingLabel = function (data) {
                if (data.free_shipping && data.free_shipping.shipping_label) {
                    return data.free_shipping.shipping_label;
                }

                if (data.shippingLabel) {
                    return data.shippingLabel;
                }

                return self.options.messages.carrier_tariffs || 'по тарифам перевозчика';
            };

            self.renderFreeShipping = function (data) {
                var freeShipping = data.free_shipping;
                var $block = $('#free-shipping-left');

                if (!$block.length) {
                    return;
                }

                if (!freeShipping || !freeShipping.enabled) {
                    $block.addClass('d-none');
                    return;
                }

                $block.removeClass('d-none');

                var percentage = freeShipping.percentage || 0;
                var messageText = freeShipping.message_text || '';
                var amountLeft = freeShipping.amount_left_formatted || freeShipping.formatted_amount_left || '';

                if (freeShipping.available) {
                    $block.find('.free-ship-progress-bar').remove();

                    $block.find('.free-ship-info')
                        .addClass('active-free-ship')
                        .html(`
                <div class="text-free-shipping">${self.escapeHtml(messageText)}</div>
            `);

                    return;
                }

                if (!$block.find('.free-ship-progress-bar').length) {
                    $block.find('.free-shipping-inner').prepend(`
                        <div class="free-ship-progress-bar">
                            <div class="free-ship-bar-fill" style="width: 0%"></div>
                        </div>
                    `);
                }

                $block.find('.free-ship-bar-fill').css('width', percentage + '%');

                $block.find('.free-ship-info')
                    .removeClass('active-free-ship')
                    .html(`
                        <div class="text-free-shipping">${self.escapeHtml(messageText)}</div>
                        <span class="sum-free-shipping-left">${self.escapeHtml(amountLeft)}</span>
                    `);
            };

            self.renderTotals = function (data) {
                var subtotal = data.subTotal && data.subTotal.formatted ? data.subTotal.formatted : '';
                var total = data.total && data.total.formatted ? data.total.formatted : '';
                var shippingLabel = self.getShippingLabel(data);
                var freeShippingAvailable = data.free_shipping && data.free_shipping.available;

                var html = `
                        <table class="table table_total table-cart" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td class="text-left total-title">${self.options.messages.subtotal || 'Промежуточная сумма:'}</td>
                                    <td class="text-right total-text">${subtotal}</td>
                                </tr>
                    `;

                if (data.coupon && data.coupon.code) {
                    var couponValue = data.coupon.value && data.coupon.value.formatted
                        ? data.coupon.value.formatted
                        : '';

                    html += `
                        <tr>
                            <td class="text-left total-title text-success">
                                ${self.options.messages.coupon || 'Купон'} (${self.escapeHtml(data.coupon.code)}):
                            </td>
                            <td class="text-right total-text text-success">
                                -${couponValue}
                            </td>
                        </tr>
                    `;
                }

                if (
                    data.customer_group_discount
                    && data.customer_group_discount.show
                ) {
                    var groupDiscountValue = data.customer_group_discount.value
                    && data.customer_group_discount.value.formatted
                        ? data.customer_group_discount.value.formatted
                        : '';

                    var groupDiscountLabel = data.customer_group_discount.label
                        || self.options.messages.customer_group_discount
                        || 'Discount';

                    html += `
                        <tr>
                            <td class="text-left total-title text-success">
                                ${self.escapeHtml(groupDiscountLabel)}:
                            </td>
                            <td class="text-right total-text text-success">
                                -${groupDiscountValue}
                            </td>
                        </tr>
                    `;
                }

                var hasShippingMethods = data.availableShippingMethods
                    && Object.keys(data.availableShippingMethods).length > 0;

                if (data.shippingMethodName || hasShippingMethods) {
                    html += `
                        <tr>
                            <td class="text-left total-title">${self.options.messages.shipping || 'Доставка:'}</td>
                            <td
                                class="text-right total-text shipping-cost-text ${freeShippingAvailable ? 'text-success' : ''}"
                                style="color: #d8300e;"
                            >
                                ${self.escapeHtml(shippingLabel)}
                            </td>
                        </tr>
                    `;
                }

                html += `
                            <tr>
                                <td class="text-left total-title total-last">${self.options.messages.order_total || 'Итого:'}</td>
                                <td class="text-right total-text">${total}</td>
                            </tr>
                        </tbody>
                    </table>
                `;

                $('.checkout-totals').html(html);

                self.renderFreeShipping(data);
            };

            self.refreshSelectedShippingMethod = function (data) {
                if (data.shippingMethodName) {
                    $(`input[name="shipping_method"][value="${data.shippingMethodName}"]`).prop('checked', true);
                }
            };

            self.escapeHtml = function (value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            $(document).on('change', 'select[name*="[country]"], select[name*="[state]"], input[name="shipping_method"], input[name*="[city]"], input[name*="[zip]"], input[name="payment_method"], #ship-to-a-different-address', function(e) {

                var name = this.name;

                if (name.includes('[country]')) {
                    var type = name.includes('billing') ? 'billing' : 'shipping';
                    self.getZones(this.value, type);
                }

                else if (name === 'payment_method') {
                    self.form.payment_method = $(this).val();
                    if (self.form.payment_method === 'paypal') {
                        setTimeout(function() { self.renderPayPalButton(); }, 100);
                    }
                }

                else if (name === 'shipping_method') {
                    self.updateShippingMethod($(this).val());
                }
                else { }
            });

            var inputTimeout;

            $(document).on('input', '.opc-cart-qty-input', function () {
                var input = this;

                input.value = input.value.replace(/[^\d]/g, '');

                clearTimeout(inputTimeout);

                inputTimeout = setTimeout(function () {
                    self.opcValidateQty(input);
                }, 600);
            });

            $(document).on('blur', '.opc-cart-qty-input', function () {
                clearTimeout(inputTimeout);
                self.opcValidateQty(this);
            });

            $(document).on('keydown', '.opc-cart-qty-input', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    clearTimeout(inputTimeout);
                    self.opcValidateQty(this);

                    this.blur();
                }
            });
        };


        self.placeOrder = function(){
            self.syncNativeForm();

            if (!self.form.terms_and_conditions) {
                self.showErrorAlert(self.options.messages.agree_terms);
                return;
            }

            if (self.placingOrder) {
                return;
            }

            self.placingOrder = true;

            $.ajax({
                url: '/checkout',
                type: 'post',
                data: self.form,
                dataType: 'json',
                beforeSend: function() {
                    $('.ch-alert-danger, .opc-text-error').remove();
                    $('.form-control, .control-label').removeClass('error_input_checkout');
                    self.loading_mask(true);
                },
                success: function(data) {
                    if (data.redirectUrl) {
                        window.location.href = data.redirectUrl;
                    } else if (self.form.payment_method === "stripe") {
                        self.confirmStripePayment(data);
                    } else {
                        self.confirmOrder(data.orderId, self.form.payment_method);
                    }
                },
                error: function(xhr) {
                    self.placingOrder = false;
                    self.loading_mask(false);

                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        self.showValidationErrors(xhr.responseJSON.errors);
                    } else {
                        self.showErrorAlert(xhr.responseJSON?.message || self.options.messages.order_error);
                    }
                }
            });
        };

        self.updateShippingMethod = function(method) {
            if (!method) return;
            $.ajax({
                url: '/cart/shipping-method',
                type: 'post',
                data: { shipping_method: method },
                beforeSend: function() { self.loading_mask(true); },
                complete: function() { self.loading_mask(false); },
                success: function(data) {
                    $(document).trigger('cart-updated', data);
                }
            });
        };

        self.getZones = function(country_id, type) {
            var stateSelect = $(`select[name="${type}[state]"]`);
            if (!country_id || !stateSelect.length) return;

            $.ajax({
                url: `/countries/${country_id}/states`,
                type: 'get',
                dataType: 'json',
                beforeSend: function() { self.loading_mask(true); },
                complete: function() { self.loading_mask(false); },
                success: function(json) {
                    var html = `<option value="">${self.options.text_select || 'Выберите...'}</option>`;
                    $.each(json, function(code, name) {
                        html += `<option value="${code}">${name}</option>`;
                    });
                    stateSelect.html(html);
                }
            });
        };

        self.handleCouponButtonClick = function(){
            var couponCode = $('input[name=\'coupon\']').val();
            $.ajax({
                url: '/cart/coupon',
                type: 'post',
                data: { coupon: couponCode },
                beforeSend: function() {
                    self.loading_mask(true);
                },
                complete: function() {
                    self.loading_mask(false);
                },
                success: function(data) {
                    $('.alert').remove();
                    self.showSuccessAlert(self.options.messages.coupon_success);
                    $(document).trigger('cart-updated', data);
                },
                error: function(xhr) {
                    self.showErrorAlert(xhr.responseJSON?.message || self.options.messages.coupon_error);
                }
            });
        };

        self.updateQty = function(key, quantity, minimum = 1){
            if(quantity >= minimum){
                $.ajax({
                    url: `/cart/items/${key}`,
                    type: 'put',
                    data: { qty: quantity },
                    beforeSend: function() { self.loading_mask(true); },
                    complete: function() { self.loading_mask(false); },
                    success: function(data) {
                        $(document).trigger('cart-updated', data);
                    }
                });
            }
        }

        self.removeProduct = function(key) {
            if (!key) {
                return;
            }

            $.ajax({
                url: `/cart/items/${key}`,
                type: 'delete',
                beforeSend: function() {
                    self.loading_mask(true);
                },
                complete: function() {
                    self.loading_mask(false);
                },
                success: function(data) {
                    $(document).trigger('cart-updated', data);
                }
            });
        };



        self.confirmOrder = function(orderId, paymentMethod, params = {}) {
            var requestData = $.extend({}, { paymentMethod: paymentMethod }, params);
            $.get(`/checkout/${orderId}/complete`, requestData)
                .done(function() {
                    window.location.href = "/checkout/complete";
                })
                .fail(function() {
                    self.placingOrder = false;
                    self.deleteOrder(orderId);
                    self.loading_mask(false);
                });
        };

        self.deleteOrder = function(orderId) {
            if (!orderId) return;
            $.get(`/checkout/${orderId}/payment-canceled`);
        };

        self.renderPayPalButton = function() {
            if (!window.paypal) return;
            $('#paypal-button-container').empty();

            window.paypal.Buttons({
                createOrder: function() {
                    self.syncNativeForm();
                    return $.post("/checkout", self.form)
                        .then(function(data) { return data.resourceId; })
                        .catch(function(xhr) {
                            if (xhr.status === 422) self.showValidationErrors(xhr.responseJSON?.errors);
                        });
                },
                onApprove: function(data) { self.confirmOrder(data.orderID, "paypal", data); },
                onError: function(err) { console.error(err); },
                onCancel: function(data) { self.deleteOrder(data.orderID); }
            }).render("#paypal-button-container");
        };


        self.showValidationErrors = function(errors) {
            for (var field in errors) {
                var inputName = field.replace(/\./g, '\\[').replace(/$/, '\\]');
                if(!field.includes('.')) inputName = field;

                var $input = $(`[name="${inputName}"]`);
                $input.closest('.form-group').find('.control-label').addClass('error_input_checkout');
                $input.after(`<div class="opc-text-error">${errors[field][0]}</div>`);
            }

            var $firstError = $('.error_input_checkout').first();
            if ($firstError.length) {
                $('html, body').animate({ scrollTop: $firstError.offset().top - 120 }, 'slow');
            }
        };

        self.showErrorAlert = function(message) {
            $('.ch-alert-danger').remove();
            var block = $(`<div class="alert ch-alert-danger"><img class="warning-icon" src="storage/media/warning-icon.svg"><div class="text-modal-block">${message}</div><button type="button" class="close" data-bs-dismiss="alert"><svg class="icon icon-11"><use xlink:href="#cross"></use></svg></button></div>`);
            $('body').append(block);
            setTimeout(() => block.remove(), 5000);
        };

        self.showSuccessAlert = function(message) {
            $('.ch-alert-success').remove();
            var block = $(`<div class="alert ch-alert-success"><img class="success-icon" src="storage/media/success-icon.svg"><div class="text-modal-block">${message}</div><button type="button" class="close" data-bs-dismiss="alert"><svg class="icon icon-11"><use xlink:href="#cross"></use></svg></button></div>`);
            $('body').append(block);
            setTimeout(() => block.remove(), 5000);
        };

        self.plusMinusQty = function(elem, action) {
            var $parent = elem.closest('.ch-cart-quantity');
            var $input = $parent.find('.opc-cart-qty-input');

            var key = elem.data('key') || $input.data('key');
            var minimum = parseFloat($input.data('minimum')) || 1;
            var quantity = parseFloat(String($input.val()).replace(/[^\d]/g, ''));

            if (isNaN(quantity) || quantity < minimum) {
                quantity = minimum;
            }

            if (action === 'plus') {
                quantity += minimum;
            }

            if (action === 'minus') {
                quantity = quantity <= minimum ? minimum : quantity - minimum;
            }

            $input.val(quantity);

            self.updateQty(key, quantity, minimum);
        };

        self.opcValidateQty = function(elem) {
            var input = $(elem);
            var minimum = parseFloat(input.data('minimum')) || 1;
            var key = input.data('key');

            var value = String(input.val()).replace(/[^\d]/g, '');
            var quantity = parseFloat(value);

            if (isNaN(quantity) || quantity < minimum) {
                quantity = minimum;
            }

            input.val(quantity);

            self.updateQty(key, quantity, minimum);
        };

        self.loading_mask = function(action){
            if (action) {
                if(!$('.loading_mask').length) $('body').append('<div class="loading_mask"></div>');
                $('.loading_mask').html('<div class="center-body"><div class="opc-loader-circle"></div></div>').show();
            } else {
                $('.loading_mask').empty().hide();
            }
        };

        self.initSelect2 = function () {
            if ($.fn.select2) {
                $('.checkout-address').find("select[data-type=select2]").select2();
            }
        };

        self.addTopCartRight = function () {
            if(self.viewport().width > 991){
                if($('header').hasClass('fix-header')){
                    $('.checkout-col-fix-right').css('top', (document.querySelector('header')?.clientHeight || 0) + 30);
                } else {
                    $('.checkout-col-fix-right').css('top', 30);
                }
            } else {
                $('.checkout-col-fix-right').css('top', 0);
            }
        };

        self.response = function () {
            var base = this, smallDelay, lastWindowWidth = $(window).width();
            self.addTopCartRight();
            base.resizer = function () {
                if ($(window).width() !== lastWindowWidth) {
                    window.clearTimeout(smallDelay);
                    smallDelay = window.setTimeout(function () {
                        lastWindowWidth = $(window).width();
                        self.addTopCartRight();
                    }, 200);
                }
            };
            $(window).resize(base.resizer);
        };

        self.viewport = function(){
            let e = window, a = 'inner';
            if (!('innerWidth' in window )) {
                a = 'client'; e = document.documentElement || document.body;
            }
            return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
        };

        self.initRelatedSlider = function(){
            if (typeof Swiper !== 'undefined' && $('.carousel_related_prodcuts').length) {
                new Swiper('.carousel_related_prodcuts', {
                    slidesPerView: 2, spaceBetween: 20, grabCursor: true,
                    scrollbar: { el: '.carousel-related-scrollbar', draggable: true },
                    on: { afterInit: function () { setTimeout(() => $('.carousel_related_prodcuts').addClass('swiper-visible'), 500); } },
                    breakpoints: { 400: {slidesPerView: 2}, 600: {slidesPerView: 3}, 740: {slidesPerView: 4}, 992: {slidesPerView: 5}, 1200: {slidesPerView: 6} }
                });
            }
        };

        self.initIntlTelInput = function () {
            var $input = $("#input-opc-telephone-main");
            var $phoneFullInput = $("#telephone_full");
            var $countryCodeInput = $("#country_code");

            if (!window.intlTelInput || !$.fn.mask || !$input.length) return;

            var iti = window.intlTelInput($input[0], {
                initialCountry: self.options.initial_country || "auto",
                separateDialCode: true,
                geoIpLookup: function(callback) {
                    $.get("https://ipapi.co/json").done(function(data) { callback(data.country_code); }).fail(function() { callback(); });
                },
                customPlaceholder: function(placeholder) {
                    $input.mask(placeholder.replace(/\d/g, "9").replace(/\s/g, "-"));
                    return placeholder.replace(/\d/g, "_").replace(/\s/g, "-");
                }
            });

            function handlePhoneInput() {
                if (iti.isValidNumber()) $phoneFullInput.val(iti.getNumber().replace(/[^\d+]/g, ''));
                else $phoneFullInput.val('');
            }

            $input.on("countrychange blur input", function () {
                handlePhoneInput();
                $countryCodeInput.val(iti.getSelectedCountryData().dialCode);
            });
        };

        self.initMaskPhone = function(){
            if(self.options.tel_mask && self.options.tel_mask.length && $("#input-opc-telephone").length){
                $("#input-opc-telephone").mask(self.options.tel_mask);
            }
            if($('#input-opc-telephone-main').length){
                self.initIntlTelInput();
            }
        };

        self.initDateTimePicker = function(){
            if ($.fn.datetimepicker) {
                $('.date').each(function() { $(this).datetimepicker({ pickTime: false, minDate: new Date() }); });
                $('.time').each(function() { $(this).datetimepicker({ pickDate: false }); });
                $('.datetime').each(function() { $(this).datetimepicker({ pickDate: true, pickTime: true }); });
            }
        };
    }

    window.OnePageCheckout = OnePageCheckout;

    $(document).ready(function() {
        if (typeof window.CheckoutConfig !== 'undefined') {
            window.checkoutApp = new OnePageCheckout(window.CheckoutConfig);
            window.checkoutApp.init();
        }
    });

})(jQuery);
