export class ProductQuestionManager {
    constructor() {
        // Селекторы
        this.formSelector = '#form-question-answer';
        this.modalSelector = '#ch-modal-question-answer';
        this.submitBtn = '#button-question-answer';
        this.alertTimeoutId = null;

        if ($(this.formSelector).length === 0) return;

        this.init();
    }

    init() {
        this.bindFormSubmit();
    }

    bindFormSubmit() {
        $(document).on('submit', this.formSelector, (e) => {
            e.preventDefault();
            this.submitQuestion($(e.currentTarget));
        });

        $(document).on('click', this.submitBtn, (e) => {
            e.preventDefault();
            const $form = $(this.formSelector);
            if ($form.length) {
                this.submitQuestion($form);
            }
        });
    }

    submitQuestion($form) {
        const url = $form.attr('action');
        const formData = $form.serialize();
        const $btn = $(this.submitBtn);

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: formData,
            beforeSend: () => {
                if (!$btn.data('original-content')) {
                    $btn.data('original-content', $btn.html());
                }
                $btn.html('<i class="fa fa-spinner fa-spin"></i> Загрузка...')
                    .prop('disabled', true);

                this.clearErrors($form);
            },
            complete: () => {
                setTimeout(() => {
                    $btn.html($btn.data('original-content')).prop('disabled', false);
                }, 300);
            },
            success: (response) => {
                if (response.success || response.message) {
                    this.handleSuccess($form, response.message || response.success);
                } else if (response.error) {
                    this.showErrorAlert(response.error);
                }
            },
            error: (xhr) => {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    this.handleValidationErrors($form, xhr.responseJSON.errors);
                } else {
                    const msg = xhr.responseJSON?.message || 'Произошла неизвестная ошибка при отправке.';
                    this.showErrorAlert(msg);
                }
            }
        });
    }

    clearErrors($form) {
        $('.alert.ch-alert-danger').remove();
        $form.find('.has-error').removeClass('has-error');
        $form.find('.help-block.text-danger').remove();
    }

    handleValidationErrors($form, errors) {
        $.each(errors, (field, messages) => {
            const $input = $form.find(`[name="${field}"]`);
            if ($input.length) {
                $input.closest('.form-group').addClass('has-error');
                $input.after(`<span class="help-block text-danger">${messages[0]}</span>`);
            }
        });
    }

    showErrorAlert(message) {
        const alertHtml = `
            <div class="alert ch-alert-danger">
                <img class="success-icon" alt="warning-icon" src="storage/media/warning-icon.svg">
                <div class="text-modal-block">${message}</div>
                <button type="button" class="close" data-dismiss="alert">
                    <i class="up-icon-close" aria-hidden="true">&times;</i>
                </button>
            </div>`;

        $('body').append(alertHtml);

        this.alertTimeoutId = setTimeout(() => {
            $('.ch-alert-danger').fadeOut(400, function() {
                $(this).remove();
            });
        }, 7000);
    }

    handleSuccess($form, message) {
        $form.find('input[type="text"], input[type="email"], textarea').val('');

        // Прячем модалку с формой
        if ($(this.modalSelector).length) {
            $(this.modalSelector).modal('hide');
        }

        $('#modal-qa-success').remove();

        const successModalHtml = `
            <div id="modal-qa-success" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content ch-modal-success">
                        <div class="modal-body text-center p-4">
                            <img class="success-icon mb-3" alt="success-icon" src="storage/media/success-icon.svg" style="width: 50px;">
                            <div class="text-modal-block h5">${message}</div>
                            <button type="button" class="close position-absolute" style="top: 15px; right: 15px;" data-bs-dismiss="modal" aria-label="Close">
                               <svg class="icon icon-11">
                                    <use xlink:href="#cross"></use>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;

        $('body').append(successModalHtml);

        setTimeout(() => {
            $('#modal-qa-success').modal('show');
        }, 400);
    }
}
