(function ($) {
    'use strict';

    function startPageLoader() {
        if (typeof creatOverlayLoadPage === 'function') {
            creatOverlayLoadPage(true);
        }
    }

    function stopPageLoader() {
        if (typeof creatOverlayLoadPage === 'function') {
            creatOverlayLoadPage(false);
        }
    }

    function removeCallbackModal() {
        $('#modal-callback').remove();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    }

    function clearCallbackNotifications() {
        $('#popup-callback .form-control').removeClass('error_input success_input');
        $('#popup-callback').find('.us-error-agree').removeClass('us-error-agree');
        $('.alert.ch-alert-danger,.us-success-icon,.us-error-icon,.us-text-error').remove();
    }

    function normalizeValidationErrors(errors) {
        var normalizedErrors = {};

        for (var field in errors) {
            if (errors.hasOwnProperty(field)) {
                normalizedErrors[field] = Array.isArray(errors[field])
                    ? errors[field][0]
                    : errors[field];
            }
        }

        return normalizedErrors;
    }

    function showCallbackValidationErrors(errors) {
        var normalizedErrors = normalizeValidationErrors(errors);

        if (typeof handleFieldNotifications === 'function') {
            handleFieldNotifications('#modal-callback', normalizedErrors);
            return;
        }

        for (var field in normalizedErrors) {
            if (normalizedErrors.hasOwnProperty(field)) {
                var input = $('#modal-callback').find('[name="' + field + '"]');
                var formGroup = input.closest('.form-group');

                input.addClass('error_input');
                formGroup.append('<div class="us-text-error">' + normalizedErrors[field] + '</div>');
                formGroup.append('<div class="us-error-icon"><img class="success-icon" alt="success-icon" src="catalog/view/theme/upstore/image/form-icon/error-icon.svg"></div>');
            }
        }
    }

    function showCallbackSuccessMessage(message) {
        $('#modal-callback').modal('hide');

        if (typeof showModalWithMessage === 'function') {
            showModalWithMessage(message);
            return;
        }

        alert(message);
    }

    function showCallbackErrorMessage(message) {
        if (typeof showModalWithMessage === 'function') {
            showModalWithMessage(message);
            return;
        }

        alert(message);
    }

    function setCallbackButtonLoading(isLoading) {
        var button = $('#up-btn-callback');

        if (isLoading) {
            button.data('original-content', button.html());
            button.html('<img src="/storage/media/spiner.svg" alt="Loading..." style="width: 1em; height: 1em; vertical-align: middle;">')
                .prop('disabled', true);
            return;
        }

        button.html(button.data('original-content')).prop('disabled', false);
    }

    function initCallbackPlugins() {
        if ($.fn.datetimepicker && $('#modal-callback .callback-datetime').length) {
            $('#modal-callback .callback-datetime').datetimepicker({
                pickDate: true,
                minDate: typeof moment !== 'undefined' ? moment() : false,
                pickTime: true
            });
        }

        if ($.fn.mask && $('#modal-callback .callback-phone').length && window.callbackPhoneMask) {
            $('#modal-callback .callback-phone').mask(window.callbackPhoneMask);
        }
    }

    function getModalCallbacking() {
        $.ajax({
            type: 'GET',
            url: window.callbackModalUrl,

            beforeSend: function () {
                startPageLoader();
            },

            complete: function () {
                stopPageLoader();
            },

            success: function (data) {
                removeCallbackModal();

                $('html body').append(
                    '<div id="modal-callback" class="modal fade" role="dialog">' + data + '</div>'
                );

                $('#modal-callback').modal('show');

                initCallbackPlugins();

                $(document).one('hide.bs.modal', '#modal-callback.modal.fade', function () {
                    $('#modal-callback').remove();
                });
            },

            error: function () {
                showCallbackErrorMessage('Не удалось открыть форму. Попробуйте позже.');
            }
        });
    }

    function sendCallback() {
        $('#callback_url').val(window.location.href);

        clearCallbackNotifications();

        $.ajax({
            url: window.callbackStoreUrl,
            type: 'POST',
            data: $('#callback_data').serialize(),
            dataType: 'json',

            beforeSend: function () {
                setCallbackButtonLoading(true);
            },

            complete: function () {
                setCallbackButtonLoading(false);
            },

            success: function (json) {
                clearCallbackNotifications();

                if (json.warning) {
                    showCallbackValidationErrors(json.warning);
                    return;
                }

                if (json.errors) {
                    showCallbackValidationErrors(json.errors);
                    return;
                }

                if (json.success) {
                    showCallbackSuccessMessage(json.success);
                }
            },

            error: function (xhr) {
                clearCallbackNotifications();

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showCallbackValidationErrors(xhr.responseJSON.errors);
                    return;
                }

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    showCallbackErrorMessage(xhr.responseJSON.message);
                    return;
                }

                showCallbackErrorMessage('Ошибка отправки формы. Попробуйте позже.');
            }
        });
    }

    $(document).on('click', '.js-open-callback-modal', function (event) {
        event.preventDefault();

        getModalCallbacking();
    });

    $(document).on('click', '#up-btn-callback', function (event) {
        event.preventDefault();

        sendCallback();
    });

    window.get_modal_callbacking = getModalCallbacking;
    window.sendCallback = sendCallback;

})(jQuery);
