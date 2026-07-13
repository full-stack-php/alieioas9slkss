import Cart from './cart.js';
import { getViewport } from './partials/viewport.js';
import { initPhoneDropdown } from './partials/phoneDropdown.js';
import { initMenuControl } from './partials/menu.js';
import { initAccordion } from './partials/accordion.js';
import { initAccordionFooterMenu } from './partials/footerAccordion.js';
import { initMobileContact } from './partials/mobileContact.js';
import { initStickyMenu } from './partials/stickyMenu.js';
import { heightMenu } from './partials/menuHeight.js';
import { AutocompleteSearch } from './partials/autocomplete.js';
import { getAjaxLiveSearch } from './partials/ajaxSearch.js';
import { initVoiceSearch } from './partials/voiceSearch.js';
import { initSearch } from './partials/search.js';
import { initMobileMenu } from './partials/mobileMenu.js';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': window.Korf.data.csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
    }
});

document.addEventListener('DOMContentLoaded', () => {

    window.viewport = getViewport();
    let cart = new Cart();



    initPhoneDropdown();
    initMobileContact();
    initMobileMenu();

    initMenuControl();
    initStickyMenu();
    heightMenu();
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            heightMenu();
        }, 100);
    });


    initAccordion();
    initAccordionFooterMenu();
    initSearch();

    window.getAjaxLiveSearch = getAjaxLiveSearch;

    let searchInputs = document.querySelectorAll("input[name='search']");
    searchInputs.forEach(input => {
        new AutocompleteSearch(input, {
            source: getAjaxLiveSearch
        });
    });

    initVoiceSearch();
});

var sl_delay = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout (timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

function swiperModule(selector, row_items = false){

    var bpoint = 2,
        bpoint_2 = 2,
        bpoint_3 = 3;

    if(row_items == 1){
        bpoint = 1;
        bpoint_2 = 1;
        bpoint_3 = 2;
    }
    if (viewport().width > 768) {
        bpoint = 1;
    }
    if (viewport().width > 1200) {
        bpoint_3 = 2;
    }

    var nx = $(selector).closest('.container-module').find('.next-prod')[0];
    var pr = $(selector).closest('.container-module').find('.prev-prod')[0];
    var sb = $(selector).find('.swiper-scrollbar')[0];
    var navigation = $(selector).closest('.container-module').find('.swiper-mod-navigation')[0];

    new Swiper(selector, {
        watchSlidesVisibility: true,
        watchSlidesProgress: true,
        watchOverflow: true,
        observer: true,
        observeParents: true,
        slidesPerView: 1,
        nested: true,
        speed: 400,
        breakpointsBase: 'container',
        grabCursor: true,
        scrollbar: {
            el: sb,
            draggable: true,
        },
        navigation: {
            nextEl: nx,
            prevEl: pr,
        },
        on: {
            afterInit: function () {
                setTimeout(function () {
                    $(selector).addClass('swiper-visible');
                }, 500);
                updateNavigation();
            },
            resize: function () {
                setTimeout(function () {
                    updateNavigation();
                }, 300);
            }
        },
        breakpoints: {
            350 : {
                slidesPerView: bpoint,
            },
            500 : {
                slidesPerView: bpoint_2,
            },
            710: {
                slidesPerView: bpoint_3,
            },
            992: {
                slidesPerView: 4,
            },
            1220: {
                slidesPerView: 5,
            }
        }
    });

    function updateNavigation() {
        var buttons = $(selector).closest('.container-module').find('.swiper-mod-navigation .swiper-button-lock');
        if (buttons.length === 2) {
            $(navigation).addClass('disabled-navigation');
        } else {
            $(navigation).removeClass('disabled-navigation');
        }
    }
}

$(document).on('click', '.header-search .categories a', function () {
    $('body').find('input[name=\'search_category_id\']').val($(this).attr('data-idsearch'));
    $('body').find('.btn-search-select').prop('title', $(this).html());
    $('body').find('li.sel-cat-search').removeClass('sel-cat-search');
    $('body').find("[data-idsearch='"+ $(this).attr('data-idsearch') +"']").parent().addClass('sel-cat-search');
});

function initUserPopup() {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showDangerAlert(message) {
        $('.alert.ch-alert-danger').remove();

        $('body').append(
            `<div class="alert ch-alert-danger"><img class="warning-icon" src="storage/media/warning-icon.svg"><div class="text-modal-block">${escapeHtml(message)}</div><button type="button" class="close" data-bs-dismiss="alert"><svg class="icon icon-11"><use xlink:href="#cross"></use></svg></button></div>`
        );

        setTimeout(function () {
            $('.ch-alert-danger').remove();
        }, 5000);
    }

    function openLoginPopup(href) {
        if (!href) {
            return;
        }

        $.ajax({
            url: href,
            type: 'get',

            beforeSend: function () {
                creatOverlayLoadPage(true);
            },

            complete: function () {
                creatOverlayLoadPage(false);
            },

            success: function (data) {
                $('#login-form-popup').remove();

                $('body').append(
                    '<div id="login-form-popup" class="modal fade" tabindex="-1" role="dialog">' + data + '</div>'
                );

                var modalElement = document.getElementById('login-form-popup');
                var modal = new bootstrap.Modal(modalElement);

                modal.show();

                modalElement.addEventListener('hidden.bs.modal', function () {
                    modal.dispose();
                    modalElement.remove();
                }, { once: true });
            },

            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    function handleSessionExpired(xhr) {
        var message = window.Korf?.data?.sessionExpiredMessage;

        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        } else if (xhr.responseJSON && xhr.responseJSON.error) {
            message = xhr.responseJSON.error;
        }

        showDangerAlert(message);

        var loginModalUrl = window.Korf?.data?.routes?.login_modal;

        if (xhr.responseJSON && xhr.responseJSON.login_modal_url) {
            loginModalUrl = xhr.responseJSON.login_modal_url;
        }

        openLoginPopup(loginModalUrl);
    }

    $(document).on('click', '#login-popup, #login-popup-mob, .i_am_registered', function (e) {
        e.preventDefault();

        openLoginPopup($(this).attr('data-load-url'));
    });

    $(document).ajaxError(function (event, xhr) {
        if (xhr.status === 419 && xhr.responseJSON && xhr.responseJSON.session_expired) {
            handleSessionExpired(xhr);
        }
    });

    if (window.Korf?.data?.openLoginModal) {
        showDangerAlert(window.Korf.data.sessionExpiredMessage);
        openLoginPopup(window.Korf.data.routes.login_modal);
    }

    $(document).on('click', '#button-login-popup', function (e) {
        e.preventDefault();

        var button = $(this);
        var form = $('#login_data');
        var action = button.attr('data-action');

        if (!action) {
            return;
        }

        $.ajax({
            url: action,
            type: 'post',
            data: form.serialize(),
            dataType: 'json',

            beforeSend: function () {
                $('.alert.ch-alert-danger').remove();

                creatOverlayLoadPage(true);

                button.data('original-content', button.html());
                button.html('Loading...').prop('disabled', true);
            },

            complete: function () {
                setTimeout(function () {
                    creatOverlayLoadPage(false);

                    button
                        .html(button.data('original-content'))
                        .prop('disabled', false);
                }, 300);
            },

            success: function (json) {
                if (json.success) {
                    if (json.redirect) {
                        window.location.href = json.redirect;
                    } else {
                        location.reload();
                    }
                }
            },

            error: function (xhr) {
                if (xhr.status === 419 && xhr.responseJSON && xhr.responseJSON.session_expired) {
                    return;
                }

                var message = 'Ошибка авторизации';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    message = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors)[0][0];
                }

                showDangerAlert(message);
            }
        });
    });
}

$(document).on('click', '.header-cart-backdrop,.header-cart-close', function () {
    $('body').removeClass('no-scroll');
    $('.shopping-cart').removeClass('cart-is-open');
    setTimeout(function () {
        $('.cart-content').addClass('d-none');
    }, 100);
});
function initMobileSearch() {

    $(document).on('click', '.btn-open-search', function () {
        $('#fm-fixed-mobile').removeClass('d-none');
        $('.mobile-sidebar-search__content').append( $('.box-search .header-search') );

        $('#fm-fixed-mobile-bottom').addClass('z-index-low');

        $('body').addClass('no-scroll');
        $('.mobile-sidebar-search').removeClass('d-none');

        setTimeout(function () {
            $('.mobile-sidebar-search').addClass('open-search');
        }, 20);
        $('.mobile-sidebar-search').before('<div class="sl-bg-search hidden-md hidden-lg"></div>');
        $('.sl-bg-search').toggleClass('active');
    });

    $(document).on('click', '.fm-close-search,.sl-bg-search', function () {
        $('.mobile-sidebar-search').removeClass('open-search');
        $('#fm-fixed-mobile-bottom').removeClass('z-index-low');
        $('.sl-bg-search').remove();
        $('body').removeClass('no-scroll');
        setTimeout(function () {
            $('.mobile-sidebar-search').addClass('hidden');
            $('#fm-fixed-mobile').addClass('d-none');
        }, 200);
    });
}

$(document).ready(function () {
    initUserPopup();
    initMobileSearch();
});


// Support functions

function creatOverlayLoadPage(action) {
    if (action) {
        $('body').prepend('<div id="messageLoadPage"></div>');
        $('#messageLoadPage').html('<img src="./storage/media/ring-alt.svg"/>');
        $('#messageLoadPage').show();
    } else {
        $('#messageLoadPage').html('');
        $('#messageLoadPage').hide();
        $('#messageLoadPage').remove();
    }
}

function handleFieldNotifications(selector, notifications) {
    var fields = [];

    $(selector).find('.form-control').each(function() {
        var name = $(this).attr('name');
        if (name) {
            fields.push(name);
        }
    });

    for (var key in notifications) {
        if (notifications.hasOwnProperty(key)) {
            var fieldName = key;
            var message = notifications[key];

            var $field = $(selector).find('[name="' + fieldName + '"]');
            var $formGroup = $field.closest('.form-group');

            if ($field.length > 0) {
                if (fieldName === 'agree') {
                    if (message) {
                        $field.parent().addClass('us-error-agree');
                    }
                } else {
                    if (message) {
                        $field.addClass('error_input');
                        $formGroup.append('<div class="us-text-error">' + message + '</div>');
                        $formGroup.append('<div class="us-error-icon"><img class="success-icon" alt="success-icon" src="storage/media/error-icon.svg"></div>');
                        $field.removeClass('success_input');
                    } else {
                        $field.addClass('success_input');
                        $formGroup.append('<div class="us-success-icon"><img class="success-icon" alt="success-icon" src="storage/media/success-icon.svg"></div>');
                        $field.removeClass('error_input');
                    }
                }
            }
        }
    }

    fields.forEach(function(fieldName) {
        if (!notifications.hasOwnProperty(fieldName)) {
            var $field = $(selector).find('[name="' + fieldName + '"]');
            var $formGroup = $field.closest('.form-group');

            if ($field.length > 0 && $field.val() != '' && fieldName !== 'agree') {
                $field.addClass('success_input');
                $formGroup.append('<div class="us-success-icon"><img class="success-icon" alt="success-icon" src="storage/media/success-icon.svg"></div>');
            }
        }
    });
}

function showModalWithMessage(message) {
    let html  = '<div id="modal-success-message" class="modal fade">';
    html += '  <div class="modal-dialog">';
    html += '    <div class="modal-content ch-modal-success">';
    html += '      <div class="modal-body"><img class="success-icon" alt="success-icon" src="storage/media/success-icon.svg"> <div class="text-modal-block">' + message + '</div><button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true"><svg class="icon icon-11"><use xlink:href="#cross"></use></svg></button></div>';
    html += '    </div>';
    html += '  </div>';
    html += '</div>';

    $('body').append(html);

    setTimeout(function () {
        $('#modal-success-message').modal('show');
    }, 700);

    $(document).on('hide.bs.modal', '#modal-success-message.modal.fade', function () {
        $('#modal-success-message').remove();
    });
}

window.creatOverlayLoadPage = creatOverlayLoadPage;
window.handleFieldNotifications = handleFieldNotifications;
window.showModalWithMessage = showModalWithMessage;

