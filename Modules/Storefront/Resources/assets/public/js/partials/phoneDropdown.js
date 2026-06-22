export function initPhoneDropdown() {
    const desktopQuery = window.matchMedia('(min-width: 992px)');
    const $phonesWrapper = $('.up-header-phones');
    const handleHoverLogic = (e) => {
        if (e.matches) {
            $phonesWrapper.on('mouseenter.phoneDropdown', function() {
                let $dropdown = $(this).find('.up-header-phones__dropdown');
                let $top = $(this).find('.up-header-phones__top');

                if ($dropdown.outerWidth() > $top.outerWidth()) {
                    $dropdown.addClass('top-left-radius');
                }
                $(this).addClass('open');

            }).on('mouseleave.phoneDropdown', function() {
                $(this).removeClass('open');
                $(this).find('.up-header-phones__dropdown').removeClass('top-left-radius');
            });

        } else {
            $phonesWrapper.off('mouseenter.phoneDropdown mouseleave.phoneDropdown');
            $phonesWrapper.removeClass('open');
            $phonesWrapper.find('.up-header-phones__dropdown').removeClass('top-left-radius');
        }
    };
    handleHoverLogic(desktopQuery);
    desktopQuery.addEventListener('change', handleHoverLogic);
}
