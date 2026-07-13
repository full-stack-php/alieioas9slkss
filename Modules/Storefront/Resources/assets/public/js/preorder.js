$(document).ready(function () {
    let preorderModal = null;

    function getModal() {
        const modalElement = document.getElementById('modal-preorder');

        if (!modalElement) {
            return null;
        }

        if (!preorderModal) {
            preorderModal = new bootstrap.Modal(modalElement);
        }

        return preorderModal;
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
            const $field = $('#preorder-form').find(
                '[name="' + field + '"]'
            );

            if (!$field.length) {
                showAlert(message);
                return;
            }

            $field.addClass('error_input');

            $field
                .closest('.form-group')
                .append(
                    '<div class="us-text-error">'
                    + message
                    + '</div>'
                );
        });
    }

    $(document).on('click', '.js-preorder-open', function (event) {
        event.preventDefault();

        clearErrors();

        const form = document.getElementById('preorder-form');

        if (form) {
            form.reset();
        }

        const modal = getModal();

        if (modal) {
            modal.show();
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
                const modal = getModal();

                if (modal) {
                    modal.hide();
                }

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

                const message = xhr.responseJSON
                && xhr.responseJSON.message
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
