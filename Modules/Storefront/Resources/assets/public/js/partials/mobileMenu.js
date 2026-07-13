export function initMobileMenu() {

    function preloadMobileMenu() {
        if (document.readyState === 'complete') {
            loadMobileMenu();

            return;
        }

        window.addEventListener('load', function () {
            loadMobileMenu();
        }, { once: true });
    }

    function getMobileMenuData() {
        const fixedMobile = document.getElementById('fm-fixed-mobile');

        return {
            url: fixedMobile?.dataset?.mobileMenuUrl,
            loadingText: fixedMobile?.dataset?.mobileMenuLoadingText || '',
            errorText: fixedMobile?.dataset?.mobileMenuErrorText || '',
        };
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function addScrollShadow($sideMenu, $sideMenuHeader) {
        $sideMenu.on('scroll', function () {
            if ($sideMenu.scrollTop() > 0) {
                $sideMenuHeader.css('box-shadow', '0 4px 12px rgba(0, 0, 0, 0.06)');
            } else {
                $sideMenuHeader.css('box-shadow', 'none');
            }
        });
    }

    function openPanel() {
        $('#fm-fixed-mobile').removeClass('d-none hidden');
        $('.mob-menu-info-fixed-left').removeClass('d-none hidden');

        if ($('#fm-fixed-mobile-bottom').length) {
            $('#fm-fixed-mobile-bottom').addClass('z-index-low');
        }

        setTimeout(function () {
            $('.mob-menu-info-fixed-left').addClass('active');
        }, 20);

        $('body').addClass('no-scroll');

        if (!$('.sl-bg-mob').length) {
            $('.mob-menu-info-fixed-left').before('<div class="sl-bg-mob hidden-md hidden-lg"></div>');
        }

        $('.sl-bg-mob').addClass('active');

        const $sideMenu = $('#mobm-left-content > .mobm-body');
        const $sideMenuHeader = $('#mobm-left-content .mobm-top');

        addScrollShadow($sideMenu, $sideMenuHeader);
    }

    function closeMobMenu() {
        $('.mob-menu-info-fixed-left').removeClass('active');
        $('#fm-fixed-mobile-bottom').removeClass('z-index-low');
        $('body').removeClass('no-scroll');
        $('.sl-bg-mob').remove();

        setTimeout(function () {
            $('.mob-menu-info-fixed-left').addClass('hidden');
            $('#fm-fixed-mobile').addClass('d-none');
            $('#mobm-left-content').removeAttr('style');
            $('.mob-second-level').addClass('d-none');
        }, 200);
    }

    function loadMobileMenu() {
        const data = getMobileMenuData();
        const $container = $('#mob-catalog-left.mob-menu > .mobm-body');

        if (!data.url || $container.find('#mm-mobile').length || $container.data('loading')) {
            return;
        }

        $container.data('loading', true);
        $container.html('<div class="mobile-menu-loader">' + escapeHtml(data.loadingText) + '</div>');

        $.ajax({
            url: data.url,
            type: 'get',

            success: function (html) {
                $container.html(html);
            },

            error: function () {
                $container.html(
                    '<div id="mm-mobile" class="mobile-menu-empty">' + escapeHtml(data.errorText) + '</div>'
                );
            },

            complete: function () {
                $container.data('loading', false);
            },
        });
    }

    function openMobMenuLeft(event) {
        if (event) {
            event.preventDefault();
        }

        $('.mob-menu-info-fixed-left > div').removeClass('active').removeAttr('style');

        openPanel();
        loadMobileMenu();
    }

    $(document).on('click', '.js-open-mobile-menu, .btn-menu-mobile', openMobMenuLeft);

    $(document).on('click', '[data-toggle="close_mob_menu"], .sl-bg-mob', function () {
        closeMobMenu();
    });

    $(document).on('click', '#mm-mobile .go-2level', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $nextLevel = $this.siblings('.mob-second-level').first();
        const level = Number($nextLevel.data('level')) || 1;

        $nextLevel.removeClass('d-none');

        setTimeout(function () {
            $('#mobm-left-content').css('transform', 'translateX(-' + (level * 100) + '%)');

            const $sideMenu = $nextLevel.find('.m-mm-list').first();
            const $sideMenuHeader = $nextLevel.find('.mobm-top').first();

            addScrollShadow($sideMenu, $sideMenuHeader);
        }, 20);
    });

    $(document).on('click', '.mobm-close-menu', function (e) {
        e.preventDefault();
        e.stopPropagation();

        closeMobMenu();
    });

    $(document).on('click', '.back-2level', function () {
        const $this = $(this);
        const backLevel = Number($this.data('back-level')) || 0;
        const $parentLevel = $this.closest('.mob-second-level');

        $('#mobm-left-content').css('transform', 'translateX(-' + (backLevel * 100) + '%)');

        setTimeout(function () {
            $parentLevel.addClass('d-none');
        }, 500);
    });

    preloadMobileMenu();
    window.open_mob_menu_left = openMobMenuLeft;
    window.close_mob_menu = closeMobMenu;
}
