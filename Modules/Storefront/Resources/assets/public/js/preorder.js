$(document).ready(function () {
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
            $('body').append('<div class="modal-backdrop fade show js-preorder-backdrop"></div>');
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

    function clearErrors() {
        const $form = $('#preorder-form');

        $form.find('.error_input').removeClass('error_input');
        $form.find('.us-text-error').remove();
        $form.find('.preorder-alert').addClass('d-none').html('');
    }

    function showAlert(message) {
        $('#preorder-form .preorder-alert')
            .removeClass('d-none')
            .html(message);
    }

    function showFieldErrors(errors) {
        Object.keys(errors).forEach(function (field) {
            const message = errors[field][0];
            const $field = $('#preorder-form').find('[name="' + field + '"]');

            if (!$field.length) {
                showAlert(message);
                return;
            }

            $field.addClass('error_input');

            $field
                .closest('.form-group')
                .append('<div class="us-text-error">' + message + '</div>');
        });
    }

    $(document).on('click', '.js-preorder-open', function (event) {
        event.preventDefault();

        clearErrors();

        const form = document.getElementById('preorder-form');

        if (form) {
            form.reset();
        }

        showPreorderModal();
    });

    $(document).on('click', '#modal-preorder [data-bs-dismiss="modal"], #modal-preorder .close-modal', function (event) {
        event.preventDefault();

        hidePreorderModal();
    });

    $(document).on('click', '#modal-preorder', function (event) {
        if (event.target === this) {
            hidePreorderModal();
        }
    });

    $(document).on('keyup', function (event) {
        if (event.key === 'Escape' && $('#modal-preorder').hasClass('show')) {
            hidePreorderModal();
        }
    });

    $(document).on('submit', '#preorder-form', function (event) {
        event.preventDefault();

        const $form = $(this);
        const $button = $('#preorder-submit');

        clearErrors();

        $.ajax({
            method: 'POST',
            url: window.Korf.data.routes.preorder,
            data: {
                product_id: $form.find('[name="product_id"]').val(),
                phone: $form.find('[name="phone"]').val(),
            },

            beforeSend: function () {
                $button.data('original-content', $button.html());
                $button.prop('disabled', true).addClass('loading');
            },

            success: function (response) {
                hidePreorderModal();

                $form[0].reset();

                if (typeof window.showModalWithMessage === 'function') {
                    window.showModalWithMessage(response.message);
                    return;
                }

                showAlert(response.message);
            },

            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    showFieldErrors(xhr.responseJSON.errors);
                    return;
                }

                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : window.Korf.data.preorder_error;

                showAlert(message);
            },

            complete: function () {
                $button
                    .html($button.data('original-content'))
                    .prop('disabled', false)
                    .removeClass('loading');
            },
        });
    });
});
