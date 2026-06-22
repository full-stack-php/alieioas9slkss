<header class="up-header up-header-type-1 fix-header h-sticky">
    <div class="container">
        <div class="d-flex align-items-center pt-xs-5 pb-xs-5 pt-md-15 pb-md-15">
            <div class="up-header__left d-flex align-items-center">

                <button aria-label="Меню" type="button" class="up-header__btn-mobile-menu btn btn-menu-mobile hidden-md d-xl-none" onclick="open_mob_menu_left()">
                    <i class="up-icon-22 up-icon-menu-line" aria-hidden="true"></i>
                    <svg class="icon icon-22">
                        <use xlink:href="#bar"></use>
                    </svg>
                </button>

                <div id="logo" class="up-header__logo-top">
                    <div class="up-header__logo-desktop d-none d-sm-block">
                        @unless(request()->routeIs('home'))
                            <a href="{{ route('home') }}">
                        @endunless
                            @if (is_null($logo))
                                <h3>{{ setting('store_name') }}</h3>
                            @else
                                <img width="190" height="38" src="{{ $logo }}" alt="{{ setting('store_name') ?? 'Logo' }}" class="img-responsive">
                            @endif
                        @unless(request()->routeIs('home'))
                            </a>
                        @endunless
                    </div>
                    <div class="up-header__logo-mobile mr-auto">
                        @unless(request()->routeIs('home'))
                            <a href="{{ route('home') }}">
                        @endunless
                            @if (is_null($logo))
                                <h3>{{ setting('store_name') }}</h3>
                            @else
                                <img  width="135" height="40" src="{{ $logo }}" alt="{{ setting('store_name') ?? 'Logo' }}" class="img-responsive">
                            @endif
                        @unless(request()->routeIs('home'))
                            </a>
                        @endunless
                    </div>
                </div>
            </div>

            <div class="up-header__right d-flex align-items-center flex-grow-sm-1">

                <div class="phone-box col-auto f-order-3">
                    <button aria-label="Контакты" type="button" class="btn-open-contact d-flex align-items-center justify-content-center">
                        <svg class="icon icon-22">
                            <use xlink:href="#active-phone"></use>
                        </svg>
                    </button>

                    <div class="up-header-phones d-none d-md-block hp-dd">
                        <div class="up-header-phones__top dropdown-toggle up-icon">
                            <svg class="icon icon-22">
                                <use xlink:href="#active-phone"></use>
                            </svg>
                            <div class="up-header-phones__items">
                                <div class="up-header-phones__item">
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

                                    @if($activePhone)
                                        <a href="tel:{{ $cleanPhone }}" target="_blank">
                                            {{ $activePhone }}
                                        </a>
                                    @endif

                                </div>
                                @if(!empty($contactData['openTime']))
                                    <div class="up-header-phones__text-af">{{ $contactData['openTime'] }}</div>
                                @endif
                            </div>
                            <svg class="icon icon-angel">
                                <use xlink:href="#angel-down"></use>
                            </svg>

                        </div>

                        <ul class="up-header-phones__dropdown dropdown-menu ch-dropdown">
                            @if(!empty($contactData['addressHeader']))
                            <li><span>{{ $contactData['addressHeader'] }}</span></li>
                            @endif
                            @if(!empty($contactData['phone_2']) && $contactData['phone_2'] !== $activePhone)
                                <li>
                                    <a href="tel:{{ preg_replace('/[^0-9\+]/', '', $contactData['phone_2']) }}" target="_blank">
                                        {{ $contactData['phone_2'] }}
                                    </a>
                                </li>
                            @endif
                            @if(!empty($contactData['phone_3']) && $contactData['phone_3'] !== $activePhone)
                                <li>
                                    <a href="tel:{{ preg_replace('/[^0-9\+]/', '', $contactData['phone_3']) }}" target="_blank">
                                        {{ $contactData['phone_3'] }}
                                    </a>
                                </li>
                            @endif
                            @if(!empty($contactData['facebook']))
                            <li>
                                <a href="{{ $contactData['facebook'] }}" target="_blank">
                                    <div class="up-header-phones__icon-image">
                                        <img loading="lazy" width="25" height="25" src="{{ asset('build/assets/img/messenger.svg') }}" alt="Messenger">
                                    </div>
                                    Messenger
                                </a>
                            </li>
                            @endif
                            @if(!empty($contactData['telegram']))
                            <li>
                                <a href="{{ $contactData['telegram'] }}" target="_blank">
                                    <div class="up-header-phones__icon-image"><img loading="lazy" width="25" height="25" src="{{ asset('build/assets/img/telegram.svg') }}" alt="Telegram"></div>
                                    Telegram
                                </a>
                            </li>
                            @endif
                            @if(!empty($contactData['viber']))
                            <li>
                                <a href="{{ $contactData['viber'] }}" target="_blank">
                                    <div class="up-header-phones__icon-image"><img loading="lazy" width="25" height="25" src="{{ asset('build/assets/img/viber.svg') }}" alt="Viber"></div>
                                    Viber
                                </a>
                            </li>
                            @endif
                            @if($contactData['showCallBackForm'])
                            <li>
                                <a href="javascript:void(0);" class="js-open-callback-modal">
                                    <div class="up-header-phones__icon-image">
                                        <img loading="lazy" width="25" height="25" src="{{ asset('build/assets/img/callback.svg') }}" alt="Заказать звонок">
                                    </div>
                                    {{ trans("storefront::layouts.request_callback") }}
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                @include('storefront::public.layouts.header.header_search')



                <div class="box-account">
                    @if (auth()->check())
                        <button
                            aria-label="{{ trans('storefront::layouts.account') }}"
                            class="dropdown-toggle btn-account"
                            data-bs-toggle="dropdown"
                            type="button"
                        >
                            <svg class="icon icon-22">
                                <use xlink:href="#user"></use>
                            </svg>

                            <span class="text-a-icon d-none">
                                {{ trans('storefront::layouts.account') }}
                            </span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-right ch-dropdown">
                            <li>
                                <a rel="nofollow" href="{{ route('account.dashboard.index') }}">
                                    {{ trans('storefront::layouts.account') }}
                                </a>
                            </li>

                            <li>
                                <a rel="nofollow" href="{{ route('account.orders.index') }}">
                                    {{ trans('storefront::account.view_order.my_orders') }}
                                </a>
                            </li>

                            <li>
                                <a rel="nofollow" href="{{ route('logout') }}">
                                    {{ trans('user::auth.logout') }}
                                </a>
                            </li>
                        </ul>
                    @else
                        <button
                            aria-label="{{ trans('storefront::layouts.login_register') }}"
                            class="dropdown-toggle btn-account"
                            id="login-popup"
                            data-load-url="{{ route('login.modal') }}"
                            type="button"
                        >
                            <svg class="icon icon-22">
                                <use xlink:href="#user"></use>
                            </svg>

                            <span class="text-a-icon d-none">
                                {{ trans('storefront::layouts.account') }}
                            </span>
                        </button>
                    @endif
                </div>
                <div class="box-cart ">
                    <div id="cart" class="shopping-cart">
                        <button class="d-flex align-items-center btn" type="button" onclick="cart.open(this);">
                            <svg class="icon icon-22">
                                <use xlink:href="#cart"></use>
                            </svg>
                            <span class="cart-total">{{ $cartQuantity }}</span>
                            <span class="text-a-icon-cart d-none">{{ trans('storefront::cart.cart') }}</span>
                        </button>
                        <div class="cart-content">

                        </div>
                    </div>
                </div>

                @include('storefront::public.layouts.localization')


            </div>

        </div>
    </div>

    <div class="main-menu d-none d-lg-block">
        <div class="container">
            <div class="row d-flex">
                <nav class="header__menu align-items-center w-100">
                    <div class="catalog-wrapper">
                        <button class="catalog-btn" type="button">
                        <span class="catalog-btn__icon">
                          <svg class="icon icon-22">
                                <use xlink:href="#menu-second"></use>
                          </svg>
                        </span>
                            {{ trans('storefront::layouts.category_menu_btn') }}
                        </button>
                    </div>
                    @include('storefront::public.layouts.sidebar_menu.main_menu', ['type' => 'category_menu', 'menu' => $primaryMenu])
                </nav>
            </div>

        </div>
    </div>
</header>

@include('storefront::public.layouts.sidebar_menu.category', ['type' => 'category_menu', 'menu' => $categoryMenu])
