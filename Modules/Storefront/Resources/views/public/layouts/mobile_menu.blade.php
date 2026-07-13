@php
    $activePhone = null;

    if (!empty($contactData['phone_1'])) {
        $activePhone = $contactData['phone_1'];
    } elseif (!empty($contactData['phone_2'])) {
        $activePhone = $contactData['phone_2'];
    } elseif (!empty($contactData['phone_3'])) {
        $activePhone = $contactData['phone_3'];
    }

    $cleanPhone = $activePhone ? preg_replace('/[^0-9\+]/', '', $activePhone) : null;
@endphp

<div
    id="fm-fixed-mobile"
    class="mob-fix-panel topmm-fix d-none"
    data-mobile-menu-url="{{ route('storefront.mobile_menu.index') }}"
    data-mobile-menu-loading-text="{{ trans('storefront::layouts.loading') }}"
    data-mobile-menu-error-text="{{ trans('storefront::layouts.mobile_menu_load_error') }}"
>
    <div class="mobile-sidebar-search hidden shtop">
        <div class="mobile-sidebar-search__top">
            <div class="mobile-sidebar-search__title">
                {{ trans('storefront::layouts.search_for_products') }}
            </div>

            <button
                type="button"
                class="mobile-sidebar-search__close fm-close-search"
                aria-label="{{ trans('storefront::layouts.close') }}"
            >
                <svg class="icon icon-22">
                    <use xlink:href="#cross"></use>
                </svg>
            </button>
        </div>

        <div class="mobile-sidebar-search__content"></div>
    </div>

    <div class="mobile-sidebar-phones hidden">
        <div class="mobile-sidebar-phones__top no-shadow">
            <div class="mobile-sidebar-phones__title">
                {{ trans('storefront::layouts.contact_us') }}
            </div>

            <button
                type="button"
                class="mobile-sidebar-phones__close fm-close-phones"
                aria-label="{{ trans('storefront::layouts.close') }}"
            >
                <svg class="icon icon-22">
                    <use xlink:href="#cross"></use>
                </svg>
            </button>
        </div>

        <div class="mobile-sidebar-phones__content">
            <div class="mobile-sidebar-phones__inner"></div>
        </div>
    </div>

    <div class="mob-menu-info-fixed-left hidden">
        <div class="mob-first-menu active" id="mobm-left-content">
            <div class="mobm-top">
                <div class="mh-left-b mob-language dflex align-items-center">
                    <div class="dropdown-box">
                        <ul class="dropdown-menu dropdown-menu-right up-compact-dropdown">
                            @foreach (supported_locales() as $locale => $language)
                                <li class="{{ $locale === locale() ? 'active' : '' }}">
                                    <button
                                        class="btn-lang-select"
                                        type="button"
                                        onclick="location ='{{ localized_url($locale) }}'"
                                    >
                                        {{ $locale === 'uk' ? 'UA' : strtoupper($locale) }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>


                @if ($activePhone)
                    <div class="mobile-first-tel">
                        <a href="tel:{{ $cleanPhone }}">
                            {{ $activePhone }}
                        </a>

                        @if (!empty($contactData['openTime']))
                            <div class="up-header-phones__text-af">
                                {{ $contactData['openTime'] }}
                            </div>
                        @endif
                    </div>
                @endif

                <button
                    type="button"
                    class="mobm-close-menu"
                    aria-label="{{ trans('storefront::layouts.close') }}"
                    data-toggle="close_mob_menu"
                >
                    <svg class="icon icon-22">
                        <use xlink:href="#cross"></use>
                    </svg>
                </button>
            </div>

            <div class="mobm-body">
                <div class="mob-menu" id="mob-catalog-left">
                    <div class="mobm-body"></div>
                </div>
                @if ($repeat_btn)
                    <div class="mob-reoder mt-5">
                        <div class="reorder_inner">
                            <button class="btn btn-primary btn-lg w-100" type="button">
                                {{ trans('storefront::layouts.repeat_latest_order_btn') }}
                            </button>
                        </div>
                    </div>
                @endif

                @if ($mobileMenuSocialLinks->isNotEmpty())
                    <div class="mobile-menu-socials">
                        <div class="mobile-menu-socials__title mb-3">
                            {{ trans('storefront::layouts.social_networks') }}
                        </div>

                        <ul class="mobile-menu-socials__list list-unstyled d-flex flex-row gap-4">
                            @foreach ($mobileMenuSocialLinks as $social)
                                <li class="mobile-menu-socials__item">
                                    <a
                                        href="{{ $social['url'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer nofollow"
                                        class="mobile-menu-socials__link"
                                        aria-label="{{ $social['name'] }}"
                                    >
                                        <svg class="icon icon-32 icon-mob-menu">
                                            <use xlink:href="#{{ $social['icon'] }}"></use>
                                        </svg>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($mobileMenuWorkingHours))
                    <div class="desc_info_mob">
                        {!! $mobileMenuWorkingHours !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
