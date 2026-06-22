/**
 * Класс для управления отзывами о товаре
 * Файл: resources/js/ProductReviewManager.js (или внутри product_page.js)
 */
export class ProductReviewManager {
    constructor() {
        // Селекторы
        this.formSelector = '#form-review';
        this.reviewContainer = '#review';
        this.submitBtn = '#button-review';
        this.reviewModal = '#ch-modal-review';
        this.reviewsListSection = '.ch-product-reviews';
        this.starsSelector = '.label-star-prod';

        this.alertTimeoutId = null;

        if ($(this.formSelector).length === 0) return;

        this.init();
    }

    init() {
        this.bindPagination();
        this.bindFormSubmit();
        this.bindStarsHover();
        this.bindStarsClick();
    }

    /**
     * Управление звездами: наведение (Hover)
     */
    bindStarsHover() {
        $(document).on('mouseenter', this.starsSelector, (e) => {
            const $currentStar = $(e.currentTarget);
            $currentStar.prevAll(this.starsSelector).addClass('active');
            $currentStar.addClass('active');
        });

        $(document).on('mouseleave', this.starsSelector, (e) => {
            const $currentStar = $(e.currentTarget);
            $currentStar.prevAll(this.starsSelector).removeClass('active');
            $currentStar.removeClass('active');
        });
    }

    /**
     * Управление звездами: клик (Выбор оценки)
     */
    bindStarsClick() {
        $(document).on('click', this.starsSelector, (e) => {
            const $currentStar = $(e.currentTarget);

            // Очищаем все звезды
            $(this.starsSelector).removeClass('checked');

            // Закрашиваем текущую и все предыдущие
            $currentStar.addClass('checked');
            $currentStar.prevAll(this.starsSelector).addClass('checked');
        });
    }

    /**
     * Пагинация отзывов (AJAX подгрузка)
     */
    bindPagination() {
        $(document).on('click', `${this.reviewContainer} .pagination a`, (e) => {
            e.preventDefault();

            const url = $(e.currentTarget).attr('href');
            const $container = $(this.reviewContainer);

            $container.fadeOut('slow', () => {
                $container.load(url, () => {
                    $container.fadeIn('slow');

                    const targetOffset = $(this.reviewsListSection).length
                        ? $(this.reviewsListSection).offset().top - 50
                        : $container.offset().top - 50;

                    $('html, body').animate({ scrollTop: targetOffset }, 250);
                });
            });
        });
    }

    /**
     * Отправка формы отзыва
     */
    bindFormSubmit() {
        $(document).on('submit', this.formSelector, (e) => {
            e.preventDefault();
            this.submitReview($(e.currentTarget));
        });

        $(document).on('click', this.submitBtn, (e) => {
            e.preventDefault();
            const $form = $(this.formSelector);
            if ($form.length) {
                this.submitReview($form);
            }
        });
    }

    submitReview($form) {
        const url = $form.attr('action');
        const formData = $form.serialize();
        const $btn = $form.find('button[type="submit"], #button-review');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: formData,
            beforeSend: () => {
                $btn.prop('disabled', true).addClass('btn-loading');
                clearTimeout(this.alertTimeoutId);
                this.clearErrors($form);
            },
            complete: () => {
                $btn.prop('disabled', false).removeClass('btn-loading');
            },
            success: (response) => {
                if (response.success || response.message) {
                    this.handleSuccess($form, response.message || response.success);
                } else if (response.error) {
                    this.showErrorAlert(response.error);
                }
            },
            error: (xhr) => {
                // Обработка 422 ошибки валидации Laravel
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    this.handleValidationErrors($form, xhr.responseJSON.errors);
                    this.showErrorAlert('Пожалуйста, проверьте правильность заполнения формы.');
                } else {
                    const msg = xhr.responseJSON?.message || 'Произошла неизвестная ошибка при отправке.';
                    this.showErrorAlert(msg);
                }
            }
        });
    }

    /**
     * Очистка старых ошибок
     */
    clearErrors($form) {
        $('.alert-success, .alert.ch-alert-danger').remove();
        $form.find('.has-error').removeClass('has-error');
        $form.find('.help-block.text-danger').remove();
    }

    /**
     * Вывод полей с ошибками (Laravel style)
     */
    handleValidationErrors($form, errors) {
        $.each(errors, (field, messages) => {
            const $input = $form.find(`[name="${field}"]`);

            if ($input.length) {
                const $formGroup = $input.closest('.form-group');
                $formGroup.addClass('has-error');
                if ($input.is(':radio') || $input.is(':checkbox')) {
                    $formGroup.append(`<span class="help-block text-danger">${messages[0]}</span>`);
                } else {
                    $input.after(`<span class="help-block text-danger">${messages[0]}</span>`);
                }
            }
        });
    }

    /**
     * Показ всплывающей плашки с ошибкой
     */
    showErrorAlert(message) {
        const alertHtml = `
            <div class="alert ch-alert-danger">
                <img class="success-icon" alt="warning-icon" src="storage/media/warning-icon.svg">
                <div class="text-modal-block">${message}</div>
                <button type="button" class="close" data-bs-dismiss="alert">
                     <svg class="icon icon-11">
                        <use xlink:href="#cross"></use>
                    </svg>
                </button>
            </div>`;

        $('body').append(alertHtml);

        this.alertTimeoutId = setTimeout(() => {
            $('.ch-alert-danger').fadeOut(400, function() {
                $(this).remove();
            });
        }, 7000);
    }

    /**
     * Успешная отправка
     */
    handleSuccess($form, message) {
        $form[0].reset();
        $form.find('input[name="rating"]').prop('checked', false);
        $form.find(this.starsSelector).removeClass('checked active');

        if ($(this.reviewModal).length) {
            $(this.reviewModal).modal('hide');
        }

        $('#modal-review-success').remove();

        const successModalHtml = `
            <div id="modal-review-success" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content ch-modal-success">
                        <div class="modal-body text-center">
                            <img class="success-icon" alt="success-icon" src="storage/media/success-icon.svg">
                            <div class="text-modal-block mt-3">${message}</div>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">
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
            $('#modal-review-success').modal('show');
        }, 500);
    }
}
