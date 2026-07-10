$(document).ready(function () {
    let quickOrderModal = null;

    function getModal() {
        const modalElement = document.getElementById('modal-quickorder');

        if (!modalElement) {
            return null;
        }

        if (!quickOrderModal) {
            quickOrderModal = new bootstrap.Modal(modalElement);
        }

        return quickOrderModal;
    }

    function clearQuickOrderErrors() {
        const $form = $('#fastorder_data');

        $form.find('.error_input').removeClass('error_input');
        $form.find('.us-text-error').remove();
        $form.find('.quick-order-alert').addClass('d-none').html('');
    }

    function showQuickOrderAlert(message) {
        $('#fastorder_data .quick-order-alert')
            .removeClass('d-none')
            .html(message);
    }

    function showQuickOrderFieldErrors(errors) {
        Object.keys(errors).forEach(function (field) {
            const message = errors[field][0];
            const $field = $('#fastorder_data').find('[name="' + field + '"]');

            if (!$field.length) {
                showQuickOrderAlert(message);

                return;
            }

            $field.addClass('error_input');
            $field.closest('.form-group').append('<div class="us-text-error">' + message + '</div>');
        });
    }

    function btnMinusQuickOrder() {
        const $input = $('#htop_quickorder');
        const minimum = parseInt($input.attr('data-minimum')) || 1;
        let count = parseInt($input.val()) || minimum;

        count = count - minimum;
        count = count < minimum ? minimum : count;

        $input.val(count).trigger('change');
    }

    function btnPlusQuickOrder() {
        const $input = $('#htop_quickorder');
        const minimum = parseInt($input.attr('data-minimum')) || 1;
        let count = parseInt($input.val()) || minimum;

        count = count + minimum;

        $input.val(count).trigger('change');
    }

    function normalizeQuantity() {
        const $input = $('#htop_quickorder');
        const minimum = parseInt($input.attr('data-minimum')) || 1;
        let count = parseInt($input.val()) || minimum;

        if (count < minimum) {
            count = minimum;
        }

        $input.val(count);
    }

    function rawProductOptions() {
        const options = {};

        $('.options')
            .find('select, input[type="radio"]:checked, input[type="hidden"]')
            .each(function () {
                const name = $(this).attr('name');
                const value = $(this).val();

                if (
                    value
                    && name
                    && (name.startsWith('options[') || name.startsWith('m_options['))
                ) {
                    options[name] = value;
                }
            });

        return options;
    }

    function extractOptions(rawOptions, forMirror) {
        const result = {};

        Object.keys(rawOptions).forEach(function (name) {
            const value = rawOptions[name];
            const match = name.match(/\[(\d+)\]/);

            if (!match) {
                return;
            }

            const id = match[1];
            const isMirrorInput = name.startsWith('m_options[');

            if (forMirror) {
                if (isMirrorInput) {
                    result[id] = value;
                } else if (!Object.prototype.hasOwnProperty.call(rawOptions, 'm_options[' + id + ']')) {
                    result[id] = value;
                }

                return;
            }

            if (!isMirrorInput) {
                result[id] = value;
            }
        });

        return result;
    }

    function selectedGifts() {
        const giftIds = [];

        $('.gift-checkbox:checked:not(:disabled)').each(function () {
            giftIds.push($(this).val());
        });

        return giftIds;
    }

    function selectedGiftPackagings() {
        const giftPackagingIds = [];

        $('.gift-packaging-checkbox:checked:not(:disabled)').each(function () {
            giftPackagingIds.push($(this).val());
        });

        return giftPackagingIds;
    }

    function quickOrderPayload() {
        const rawOptions = rawProductOptions();

        return {
            product_id: $('#fastorder_data input[name="product_id"]').val(),
            qty: selectedQuantity(),
            phone: $('#fastorder_data [name="phone"]').val(),
            comment: $('#fastorder_data [name="comment"]').val(),
            packaging_id: $('input[name="packaging_id"]:checked').val() || null,
            is_mirrored: $('#is_mirrored').val() === '1' ? 1 : 0,
            options: extractOptions(rawOptions, false),
            m_options: extractOptions(rawOptions, true),
            ch_gifts: selectedGifts(),
            gift_packaging_ids: selectedGiftPackagings()
        };
    }

    function selectedQuantity() {
        const popupQty = $('#htop_quickorder').val();
        const productQty = $('input[name="quantity"]').not('#htop_quickorder').val();

        return popupQty || productQty || 1;
    }

    $(document).on('click', '.js-quick-order-open', function (e) {
        e.preventDefault();

        clearQuickOrderErrors();

        const form = document.getElementById('fastorder_data');

        if (form) {
            form.reset();
        }

        $('#htop_quickorder').val($('input[name="quantity"]').val() || 1);

        const modal = getModal();

        if (modal) {
            modal.show();
        }
    });

    $(document).on('click', '.js-quick-order-qty-minus', function (e) {
        e.preventDefault();

        btnMinusQuickOrder();
    });

    $(document).on('click', '.js-quick-order-qty-plus', function (e) {
        e.preventDefault();

        btnPlusQuickOrder();
    });

    $(document).on('change keyup', '#htop_quickorder', function () {
        normalizeQuantity();
    });

    $(document).on('submit', '#fastorder_data', function (e) {
        e.preventDefault();

        const $button = $('#up-btn-fastorder');

        clearQuickOrderErrors();

        $.ajax({
            method: 'POST',
            url: window.Korf.data.routes.quick_order,
            data: quickOrderPayload(),

            beforeSend: function () {
                $button.data('original-content', $button.html());
                $button.prop('disabled', true).addClass('loading');
            },

            success: function (response) {
                const modal = getModal();

                if (modal) {
                    modal.hide();
                }

                if (typeof window.showModalWithMessage === 'function') {
                    window.showModalWithMessage(response.message);

                    return;
                }

                showQuickOrderAlert(response.message);
            },

            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    showQuickOrderFieldErrors(xhr.responseJSON.errors);

                    return;
                }

                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : window.Korf.data.quick_order_error;

                showQuickOrderAlert(message);
            },

            complete: function () {
                $button
                    .html($button.data('original-content'))
                    .prop('disabled', false)
                    .removeClass('loading');
            }
        });
    });
});
