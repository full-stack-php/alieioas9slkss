window.admin.removeSubmitButtonOffsetOn(["#logo", "#courier"]);

let currencyRateExchangeService = $("#currency_rate_exchange_service");

$(`#${currencyRateExchangeService.val()}-service`).removeClass("hide");

currencyRateExchangeService.on("change", (e) => {
    $(".currency-rate-exchange-service").addClass("hide");

    $(`#${e.currentTarget.value}-service`).removeClass("hide");
});

$("#auto_refresh_currency_rates").on("change", () => {
    $("#auto-refresh-currency-rates-frequency-field").toggleClass("hide");
});

$("#auto_refresh_currency_rates").on("change", () => {
    $("#auto-refresh-frequency-field").toggleClass("hide");
});

let smsService = $("#sms_service");

$(`#${smsService.val()}-service`).removeClass("hide");

smsService.on("change", (e) => {
    $(".sms-service").addClass("hide");

    $(`#${e.currentTarget.value}-service`).removeClass("hide");
});

$("#google_recaptcha_enabled").on("change", () => {
    $("#google-recaptcha-fields").toggleClass("hide");
});

$("#facebook_login_enabled").on("change", () => {
    $("#facebook-login-fields").toggleClass("hide");
});

$("#google_login_enabled").on("change", () => {
    $("#google-login-fields").toggleClass("hide");
});

$("#paypal_enabled").on("change", () => {
    $("#paypal-fields").toggleClass("hide");
});

$("#stripe_enabled").on("change", () => {
    $("#stripe-fields").toggleClass("hide");
});

$("#paytm_enabled").on("change", () => {
    $("#paytm-fields").toggleClass("hide");
});

$("#razorpay_enabled").on("change", () => {
    $("#razorpay-fields").toggleClass("hide");
});

$("#instamojo_enabled").on("change", () => {
    $("#instamojo-fields").toggleClass("hide");
});

$("#paystack_enabled").on("change", () => {
    $("#paystack-fields").toggleClass("hide");
});

$("#authorizenet_enabled").on("change", () => {
    $("#authorizenet-fields").toggleClass("hide");
});

$("#flutterwave_enabled").on("change", () => {
    $("#flutterwave-fields").toggleClass("hide");
});

$("#iyzico_enabled").on("change", () => {
    $("#iyzico-fields").toggleClass("hide");
});

$("#bkash_enabled").on("change", () => {
    $("#bkash-fields").toggleClass("hide");
});

$("#nagad_enabled").on("change", () => {
    $("#nagad-fields").toggleClass("hide");
});

$("#sslcommerz_enabled").on("change", () => {
    $("#sslcommerz-fields").toggleClass("hide");
});

$("#payfast_enabled").on("change", () => {
    $("#payfast-fields").toggleClass("hide");
});

$("#bank_transfer_enabled").on("change", () => {
    $("#bank-transfer-fields").toggleClass("hide");
});

$("#check_payment_enabled").on("change", () => {
    $("#check-payment-fields").toggleClass("hide");
});

$("#store_country").on("change", (e) => {
    let oldState = $("#store_state").val();

    $.ajax({
        url: `${Korf.baseUrl}/countries/${e.currentTarget.value}/states`,
        type: 'GET',
        success: function(data) {
            $(".store-state").addClass("hide");

            if (_.isEmpty(data)) {
                return $(".store-state.input")
                    .removeClass("hide")
                    .find("input")
                    .val(oldState);
            }

            let options = "";

            for (let code in data) {
                options += `<option value="${code}">${data[code]}</option>`;
            }

            $(".store-state.select")
                .removeClass("hide")
                .find("select")
                .html(options)
                .val(oldState);
        },
        error: function(xhr, status, error) {
            console.error("Ошибка при получении регионов:", error);
        }
    });
});

$(function () {
    $("#store_country").trigger("change");

    if ($("#logo").hasClass("active")) {
        $("#logo")
            .parent()
            .find('button[type="submit"]')
            .parent()
            .removeClass("col-md-offset-2");
    }
});

$('.btn-sync-nova-poshta').on('click', function(e) {
    e.preventDefault();

    $('#sync-global-status').text('Отправка запроса...').removeClass('text-danger text-success').addClass('text-primary');
    $('#btn-close-sync-modal').prop('disabled', true);

    let entities = ['areas', 'cities', 'warehouses'];
    entities.forEach(type => {
        $(`#bar-${type}`).css('width', '0%').removeClass('progress-bar-animated bg-success bg-danger bg-primary').addClass('bg-secondary');
        $(`#text-${type}`).text('0 / 0');
    });

    $.ajax({
        type: 'POST',
        url: `${Korf.baseUrl}/admin/shipping/nova-poshta/sync/start`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
            let pollInterval = setInterval(() => {
                $.ajax({
                    type: 'GET',
                    url: `${Korf.baseUrl}/admin/shipping/nova-poshta/sync/status`,
                    success: function(data) {

                        $('#sync-global-status').text(data.message);

                        entities.forEach(type => {
                            let item = data[type];
                            if (item) {
                                $(`#text-${type}`).text(`${item.current} / ${item.total}`);
                                $(`#bar-${type}`).css('width', `${item.percent}%`);

                                if (item.percent > 0 && item.percent < 100) {
                                    $(`#bar-${type}`).removeClass('bg-secondary bg-success').addClass('bg-primary progress-bar-animated');
                                }
                                else if (item.percent === 100) {
                                    $(`#bar-${type}`).removeClass('bg-primary progress-bar-animated').addClass('bg-success');
                                }
                            }
                        });

                        if (data.is_finished) {
                            clearInterval(pollInterval);
                            $('#btn-close-sync-modal').prop('disabled', false);

                            if (data.error) {
                                $('#sync-global-status').removeClass('text-primary').addClass('text-danger');
                                $('.progress-bar:not(.bg-success)').removeClass('bg-primary bg-secondary progress-bar-animated').addClass('bg-danger');
                            } else {
                                $('#sync-global-status').text('Синхронизация успешно завершена!').removeClass('text-primary').addClass('text-success');
                            }
                        }
                    },
                    error: function() {
                        clearInterval(pollInterval);
                        $('#sync-global-status').text('Потеряно соединение с сервером.').addClass('text-danger');
                        $('#btn-close-sync-modal').prop('disabled', false);
                    }
                });
            }, 1500);
        },
        error: function() {
            $('#sync-global-status').text('Не удалось запустить синхронизацию.').addClass('text-danger');
            $('#btn-close-sync-modal').prop('disabled', false);
        }
    });
});

$('.btn-sync-meest').on('click', function(e) {
    e.preventDefault();

    return 0;

    $('#sync-global-status')
        .text('Отправка запроса...')
        .removeClass('text-danger text-success')
        .addClass('text-primary');

    $('#btn-close-sync-modal').prop('disabled', true);

    let entities = ['poshtomat', 'minibranch', 'mainbranch'];

    entities.forEach(type => {
        $(`#bar-${type}`)
            .css('width', '0%')
            .removeClass('progress-bar-animated bg-success bg-danger bg-primary')
            .addClass('bg-secondary');

        $(`#text-${type}`).text('0 / 0');
    });

    $.ajax({
        type: 'POST',
        url: `${Korf.baseUrl}/admin/shipping/meest/sync/start`,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
            let pollInterval = setInterval(() => {
                $.ajax({
                    type: 'GET',
                    url: `${Korf.baseUrl}/admin/shipping/meest/sync/status`,
                    success: function(data) {
                        $('#sync-global-status').text(data.message);

                        entities.forEach(type => {
                            let item = data[type];

                            if (item) {
                                $(`#text-${type}`).text(`${item.current} / ${item.total}`);
                                $(`#bar-${type}`).css('width', `${item.percent}%`);

                                if (item.percent > 0 && item.percent < 100) {
                                    $(`#bar-${type}`)
                                        .removeClass('bg-secondary bg-success bg-danger')
                                        .addClass('bg-primary progress-bar-animated');
                                } else if (item.percent === 100) {
                                    $(`#bar-${type}`)
                                        .removeClass('bg-primary bg-secondary bg-danger progress-bar-animated')
                                        .addClass('bg-success');
                                }
                            }
                        });

                        if (data.is_finished) {
                            clearInterval(pollInterval);
                            $('#btn-close-sync-modal').prop('disabled', false);

                            if (data.error) {
                                $('#sync-global-status')
                                    .removeClass('text-primary text-success')
                                    .addClass('text-danger');

                                $('.progress-bar:not(.bg-success)')
                                    .removeClass('bg-primary bg-secondary progress-bar-animated')
                                    .addClass('bg-danger');
                            } else {
                                $('#sync-global-status')
                                    .text('Синхронизация Meest успешно завершена!')
                                    .removeClass('text-primary text-danger')
                                    .addClass('text-success');
                            }
                        }
                    },
                    error: function() {
                        clearInterval(pollInterval);

                        $('#sync-global-status')
                            .text('Потеряно соединение с сервером.')
                            .removeClass('text-primary text-success')
                            .addClass('text-danger');

                        $('#btn-close-sync-modal').prop('disabled', false);
                    }
                });
            }, 1500);
        },
        error: function() {
            $('#sync-global-status')
                .text('Не удалось запустить синхронизацию Meest.')
                .removeClass('text-primary text-success')
                .addClass('text-danger');

            $('#btn-close-sync-modal').prop('disabled', false);
        }
    });
});


