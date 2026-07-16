$(document).ready(function () {
    let productSelectionAlertTimeoutId = null;

    function getModal() {
        return $('#modal-preorder');
    }

    function showPreorderModal() {
        const $modal = getModal();

        if (!$modal.length) {
            return;
        }

        $modal
            .addClass('show')
            .attr('aria-hidden', 'false')
            .show();

        $('body').addClass('modal-open');

        if (!$('.js-preorder-backdrop').length) {
            $('body').append(
                '<div class="modal-backdrop fade show js-preorder-backdrop"></div>'
            );
        }
    }

    function hidePreorderModal() {
        const $modal = getModal();

        if (!$modal.length) {
            return;
        }

        $modal
            .removeClass('show')
            .attr('aria-hidden', 'true')
            .hide();

        $('body').removeClass('modal-open');
        $('.js-preorder-backdrop').remove();
    }

    function clearModalErrors() {
        const $form = $('#preorder-form');

        $form
            .find('.error_input')
            .removeClass('error_input');

        $form
            .find('.us-text-error')
            .remove();

        $form
            .find('.preorder-alert')
            .addClass('d-none')
            .html('');
    }

    function clearProductSelectionErrors() {
        $('.options .form-group, .product-packagings')
            .removeClass('has-error option-error');

        $('.preorder-option-error').remove();
        $('.preorder-option-danger').remove();

        if (productSelectionAlertTimeoutId) {
            clearTimeout(productSelectionAlertTimeoutId);
            productSelectionAlertTimeoutId = null;
        }
    }

    function showModalAlert(message) {
        $('#preorder-form .preorder-alert')
            .removeClass('d-none')
            .text(message);
    }

    function showModalFieldErrors(errors) {
        const $form = $('#preorder-form');

        Object.keys(errors).forEach(function (field) {
            const message = errors[field][0];
            const $field = $form.find(
                '[name="' + field + '"]'
            );

            if (
                !$field.length
                || $field.attr('type') === 'hidden'
            ) {
                showModalAlert(message);

                return;
            }

            const $formGroup = $field.closest(
                '.form-group'
            );

            if (!$formGroup.length) {
                showModalAlert(message);

                return;
            }

            $field.addClass('error_input');

            $('<div>', {
                class: 'us-text-error',
                text: message,
            }).appendTo($formGroup);
        });
    }

    function isProductSelectionError(field) {
        return field === 'packaging_id'
            || /^(options|m_options|secondary_options)\.\d+/.test(
                field
            );
    }

    function findProductSelectionContainer(field) {
        if (field === 'packaging_id') {
            return $('.product-packagings').first();
        }

        const match = field.match(
            /^(options|m_options|secondary_options)\.(\d+)/
        );

        if (!match) {
            return $();
        }

        const fieldType = match[1];
        const optionId = match[2];

        const isSecondary = fieldType === 'm_options'
            || fieldType === 'secondary_options';

        const $options = $('.options');

        let $fields = $();

        if (isSecondary) {
            $fields = $options.find(
                '[name="m_options[' + optionId + ']"],'
                + '[name="m_options[' + optionId + '][]"]'
            );

            if ($fields.length) {
                return $fields
                    .first()
                    .closest('.form-group');
            }

            /*
             * В некоторых m_custom_options используется такое же
             * имя options[id], как у первой опции.
             * В этом случае берём последний найденный блок.
             */
            $fields = $options.find(
                '[name="options[' + optionId + ']"],'
                + '[name="options[' + optionId + '][]"]'
            );

            if ($fields.length) {
                return $fields
                    .last()
                    .closest('.form-group');
            }

            return $options
                .find('#m_option-' + optionId)
                .closest('.form-group');
        }

        $fields = $options.find(
            '[name="options[' + optionId + ']"],'
            + '[name="options[' + optionId + '][]"]'
        );

        if ($fields.length) {
            return $fields
                .first()
                .closest('.form-group');
        }

        $fields = $options.find(
            '#option-' + optionId
            + ', #input-option' + optionId
        );

        return $fields
            .first()
            .closest('.form-group');
    }

    function showProductSelectionAlert(message) {
        $('.preorder-option-danger').remove();

        const fallbackMessage = getModal().attr(
            'data-options-required-message'
        );

        const closeLabel = getModal().attr(
            'data-close-label'
        );

        const $alert = $('<div>', {
            class: [
                'alert',
                'option-danger',
                'preorder-option-danger',
            ].join(' '),

            role: 'alert',
        });

        $('<div>', {
            class: 'text-modal-block',
            text: message || fallbackMessage,
        }).appendTo($alert);

        const $closeButton = $('<button>', {
            type: 'button',
            class: 'close js-preorder-option-alert-close',
            'aria-label': closeLabel,
        });

        $('<span>', {
            'aria-hidden': 'true',
            html: '&times;',
        }).appendTo($closeButton);

        $closeButton.appendTo($alert);

        const $top = $('#top');

        if ($top.length) {
            $top.before($alert);
        } else {
            $('body').prepend($alert);
        }

        productSelectionAlertTimeoutId = setTimeout(
            function () {
                $('.preorder-option-danger').remove();

                productSelectionAlertTimeoutId = null;
            },
            7000
        );
    }

    function scrollToProductSelection($container) {
        let $target = $container;

        if (!$target || !$target.length) {
            $target = $('.options').first();
        }

        if (!$target.length || !$target.offset()) {
            return;
        }

        $('html, body').animate(
            {
                scrollTop: $target.offset().top - 150,
            },
            250
        );
    }

    function showProductSelectionErrors(errors) {
        clearProductSelectionErrors();

        let firstMessage = null;
        let $firstContainer = $();

        Object.keys(errors).forEach(function (field) {
            const messages = errors[field] || [];
            const message = messages[0];

            if (!message) {
                return;
            }

            if (!firstMessage) {
                firstMessage = message;
            }

            const $container = findProductSelectionContainer(
                field
            );

            if (!$container.length) {
                return;
            }

            if (!$firstContainer.length) {
                $firstContainer = $container;
            }

            $container.addClass(
                'has-error option-error'
            );

            if (
                !$container
                    .find('.preorder-option-error')
                    .length
            ) {
                $('<div>', {
                    class: [
                        'text-danger',
                        'preorder-option-error',
                    ].join(' '),

                    text: message,
                }).appendTo($container);
            }
        });

        showProductSelectionAlert(firstMessage);

        setTimeout(function () {
            scrollToProductSelection(
                $firstContainer
            );
        }, 50);
    }

    function splitValidationErrors(errors) {
        const selectionErrors = {};
        const modalErrors = {};

        Object.keys(errors).forEach(function (field) {
            if (isProductSelectionError(field)) {
                selectionErrors[field] = errors[field];

                return;
            }

            modalErrors[field] = errors[field];
        });

        return {
            selectionErrors: selectionErrors,
            modalErrors: modalErrors,
        };
    }

    function getProductSelections() {
        const options = {};
        const mirroredOptions = {};

        $('.options :input')
            .serializeArray()
            .forEach(function (field) {
                const match = field.name.match(
                    /^(m_)?options\[(\d+)](\[\])?$/
                );

                if (!match) {
                    return;
                }

                const isMirrored = Boolean(match[1]);
                const optionId = match[2];
                const isMultiple = Boolean(match[3]);

                const target = isMirrored
                    ? mirroredOptions
                    : options;

                if (isMultiple) {
                    if (!Array.isArray(target[optionId])) {
                        target[optionId] = [];
                    }

                    target[optionId].push(field.value);

                    return;
                }

                target[optionId] = field.value;
            });

        return {
            options: options,

            m_options: mirroredOptions,

            is_mirrored: $('#is_mirrored').val() === '1'
                ? 1
                : 0,

            packaging_id: $(
                'input[name="packaging_id"]:checked'
            ).val() || null,
        };
    }

    $(document).on(
        'click',
        '.js-preorder-open',
        function (event) {
            event.preventDefault();

            /*
             * Если сервер ранее подсветил обязательную опцию,
             * popup повторно не открываем до изменения поля.
             */
            const $invalidSelection = $(
                '.options .option-error,'
                + '.product-packagings.option-error'
            ).first();

            if ($invalidSelection.length) {
                showProductSelectionAlert(
                    getModal().attr(
                        'data-options-required-message'
                    )
                );

                scrollToProductSelection(
                    $invalidSelection
                );

                return;
            }

            clearModalErrors();

            const form = document.getElementById(
                'preorder-form'
            );

            if (form) {
                form.reset();
            }

            showPreorderModal();
        }
    );

    $(document).on(
        'click',
        [
            '#modal-preorder [data-bs-dismiss="modal"]',
            '#modal-preorder .close-modal',
        ].join(', '),
        function (event) {
            event.preventDefault();

            hidePreorderModal();
        }
    );

    $(document).on(
        'click',
        '.js-preorder-option-alert-close',
        function () {
            $(this)
                .closest('.preorder-option-danger')
                .remove();
        }
    );

    $(document).on(
        'click',
        '#modal-preorder',
        function (event) {
            if (event.target === this) {
                hidePreorderModal();
            }
        }
    );

    $(document).on(
        'keyup',
        function (event) {
            if (
                event.key === 'Escape'
                && getModal().hasClass('show')
            ) {
                hidePreorderModal();
            }
        }
    );

    /*
     * После изменения опции снимаем её ошибку.
     */
    $(document).on(
        'input change',
        [
            '.options :input',
            'input[name="packaging_id"]',
        ].join(', '),
        function () {
            const $container = $(this).closest(
                '.form-group, .product-packagings'
            );

            $container.removeClass(
                'has-error option-error'
            );

            $container
                .find('.preorder-option-error')
                .remove();

            if (
                !$(
                    '.options .option-error,'
                    + '.product-packagings.option-error'
                ).length
            ) {
                $('.preorder-option-danger').remove();
            }
        }
    );

    $(document).on(
        'submit',
        '#preorder-form',
        function (event) {
            event.preventDefault();

            const $form = $(this);
            const $button = $('#preorder-submit');

            const selections = getProductSelections();

            clearModalErrors();

            $.ajax({
                method: 'POST',

                url: window.Korf.data.routes.preorder,

                data: {
                    product_id: $form
                        .find('[name="product_id"]')
                        .val(),

                    phone: $form
                        .find('[name="phone"]')
                        .val(),

                    options: selections.options,

                    m_options: selections.m_options,

                    is_mirrored: selections.is_mirrored,

                    packaging_id: selections.packaging_id,
                },

                beforeSend: function () {
                    $button.data(
                        'original-content',
                        $button.html()
                    );

                    $button
                        .prop('disabled', true)
                        .addClass('loading');
                },

                success: function (response) {
                    hidePreorderModal();
                    clearProductSelectionErrors();

                    $form[0].reset();

                    if (
                        typeof window.showModalWithMessage
                        === 'function'
                    ) {
                        window.showModalWithMessage(
                            response.message
                        );

                        return;
                    }

                    showModalAlert(response.message);
                },

                error: function (xhr) {
                    const response = xhr.responseJSON || {};

                    if (response.errors) {
                        const validationErrors =
                            splitValidationErrors(
                                response.errors
                            );

                        if (
                            Object.keys(
                                validationErrors.selectionErrors
                            ).length
                        ) {
                            hidePreorderModal();

                            showProductSelectionErrors(
                                validationErrors.selectionErrors
                            );

                            return;
                        }

                        showModalFieldErrors(
                            validationErrors.modalErrors
                        );

                        return;
                    }

                    const message = response.message
                        || window.Korf.data.preorder_error;

                    showModalAlert(message);
                },

                complete: function () {
                    $button
                        .html(
                            $button.data(
                                'original-content'
                            )
                        )
                        .prop('disabled', false)
                        .removeClass('loading');
                },
            });
        }
    );
});
