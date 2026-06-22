$(document).ready(function() {
    function addTopCartRight() {
        if (window.viewport && window.viewport.width > 991) {
            $('.cart-col-right').css('top', document.querySelector('header').clientHeight + 20);
        } else {
            $('.cart-col-right').css('top', 0);
        }
    }

    $(window).resize(function () {
        addTopCartRight();
    });

    addTopCartRight();

    $('#button-coupon').on('click', function() {
        const url = $(this).parents('form').attr('action');
        console.log(url);
        $.ajax({
            url: url,
            type: 'post',
            data: 'coupon=' + encodeURIComponent($('input[name=\'coupon\']').val()),
            dataType: 'json',
            beforeSend: function() {
                $('input[name=\'coupon\']').attr('disabled', 'disabled');
            },
            complete: function() {
                $('input[name=\'coupon\']').removeAttr('disabled');
            },
            success: function(json) {
                $('.ch-alert-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                }
            },
            error: function(xhr) {
                $('body').append('<div class="alert ch-alert-danger"><img class="warning-icon" src="storage/media/warning-icon.svg"><div class="text-modal-block">' + xhr.responseJSON?.message || 'Ошибка применения промокода' + '</div><button type="button" class="close" data-bs-dismiss="alert"><svg class="icon icon-11"><use xlink:href="#cross"></use></svg></button></div>');
                setTimeout(function () {
                    $('.ch-alert-danger').remove();
                }, 7000);
            }
        });
    });
});

window.sl_cart_minus = function(elem) {
    var $input = $(elem).filter(':visible');
    var min = parseInt($input.attr('data-minimum')) || 1;
    var count = parseInt($input.val()) - min;
    var new_count = count < min ? min : count;

    if (count >= new_count) {
        $input.val(count).change();
    }
};

window.sl_cart_plus = function(elem) {
    var $input = $(elem).filter(':visible');
    var min = parseInt($input.attr('data-minimum')) || 1;
    var count = parseInt($input.val()) + min;

    $input.val(count).change();
};

window.updateQuantityCart = function(key, quantity) {
    $.ajax({
        url: `/cart/items/${key}`,
        type: 'put',
        data: { qty:  (typeof (quantity) != 'undefined' ? quantity : 1) },
        dataType: 'json',
        success: function (json) {
            $.get(window.Korf.data.routes.cart_get, (res) => {
                $('.cart-total').text(res.quantity);
                $("#checkout_total").html(res.total.formatted);
                $("#checkout_sub_total").html(res.sub_total);
            });
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
};


