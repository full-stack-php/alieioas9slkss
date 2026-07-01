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

function getCartItemFromResponse(data, key) {
    if (!data || !data.items) {
        return null;
    }

    if (data.items[key]) {
        return data.items[key];
    }

    if (Array.isArray(data.items)) {
        return data.items.find(function (item) {
            return item.id === key;
        }) || null;
    }

    return Object.values(data.items).find(function (item) {
        return item.id === key;
    }) || null;
}

function renderCartPriceHtml(currentPrice, regularPrice, hasDiscountedPrice) {
    var current = currentPrice && currentPrice.formatted ? currentPrice.formatted : '';
    var regular = regularPrice && regularPrice.formatted ? regularPrice.formatted : '';

    if (hasDiscountedPrice && regular) {
        return '<span class="price-old">' + regular + '</span> <span class="price-new">' + current + '</span>';
    }

    return '<span class="price-new">' + current + '</span>';
}

function updateCartItemRow(key, cartItem) {
    if (!cartItem) {
        return;
    }

    var $row = $('[data-cart-item]').filter(function () {
        return String($(this).attr('data-cart-item')) === String(key);
    });

    if (!$row.length) {
        return;
    }

    $row.find('[data-cart-unit-price-html]').html(
        renderCartPriceHtml(
            cartItem.unitPrice,
            cartItem.regularUnitPrice,
            cartItem.has_discounted_price === true
        )
    );

    var lineTotal = cartItem.total && cartItem.total.formatted
        ? cartItem.total.formatted
        : '';

    $row.find('[data-cart-line-total-html]').html(
        '<span class="text-cart-item-total">' + window.cartTotalLabel + '</span>' + lineTotal
    );

    if (typeof cartItem.qty !== 'undefined') {
        $row.find('input.form-control').val(cartItem.qty);
    }
}

function updateCartSummary(data) {
    $('.cart-total').text(data.quantity || 0);

    if (data.total && data.total.formatted) {
        $('#checkout_total').html(data.total.formatted);
    }

    if (data.subTotal && data.subTotal.formatted) {
        $('#checkout_sub_total').html(data.subTotal.formatted);
    }

    if (data.customer_group_discount) {
        if (data.customer_group_discount.show) {
            $('#customer_group_discount_row').show();

            if (data.customer_group_discount.value && data.customer_group_discount.value.formatted) {
                $('#customer_group_discount_value').html('-' + data.customer_group_discount.value.formatted);
            }
        } else {
            $('#customer_group_discount_row').hide();
        }
    }
}

window.updateQuantityCart = function(key, quantity) {
    $.ajax({
        url: `/cart/items/${key}`,
        type: 'put',
        data: {
            qty: typeof quantity !== 'undefined' ? quantity : 1
        },
        dataType: 'json',
        success: function (json) {
            updateCartItemRow(key, getCartItemFromResponse(json, key));
            updateCartSummary(json);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
};


