<footer class="pt-5">

    <div class="container">

        <div class="footer-center row-flex pt-5 row">
            <div class="col-12 col-sm-12 col-md-5 col-lg-4 footer-contact">
                <div id="logo-footer" class="d-flex w-100 mb-5">
                    @unless(request()->routeIs('home'))
                        <a href="{{ route('home') }}">
                            @endunless
                            @if (is_null($logo))
                                <h3>{{ setting('store_name') }}</h3>
                            @else
                                <img width="300" height="38" src="{{ $footer_logo }}" alt="{{ setting('store_name') ?? 'Logo' }}" class="img-responsive pe-5">
                            @endif
                            @unless(request()->routeIs('home'))
                        </a>
                    @endunless
                </div>
                <div class="d-flex flex-column flex-sm-row">
                    <ul class="list-unstyled">
                        <li>
                            <a class="d-flex" style="margin-bottom:20px;" href="tel:0-800-303-202" target="_blank">
                                <div class="icon-image">
                                    <img loading="lazy" width="22" height="22" src="{{ asset('build/assets/img/phone.svg') }}" alt="">
                                </div>
                                <div style="font-size:16px;color:#fff;font-weight:500;">
                                    @if(!empty($contactData['phone_1']))
                                        <div style="margin-bottom:5px;">{{ $contactData['phone_1'] }}</div>
                                    @endif
                                    @if(!empty($contactData['phone_2']))
                                        <div style="margin-bottom:5px;">{{ $contactData['phone_2'] }}</div>
                                    @endif
                                    @if(!empty($contactData['phone_3']))
                                        <div style="margin-bottom:5px;">{{ $contactData['phone_3'] }}</div>
                                    @endif
                                </div>
                            </a>
                        </li>
                        @if(!empty($contactData['store_email']))
                        <li>
                            <a class="d-flex" style="margin-bottom:20px;" target="_blank" href="mailto:{{ $contactData['store_email']  }}">
                                <div class="icon-image">
                                    <img loading="lazy" width="22" height="22" src="{{ asset('build/assets/img/email.svg') }}" alt="">
                                </div>
                                <span style="color:#7a4cd9;font-size:16px;font-weight:500;">{{ $contactData['store_email']  }}</span>
                            </a>
                        </li>
                        @endif
                        @if(!empty($contactData['footer_open_time']))
                        <li>
                            <div class="d-flex">
                                <div class="icon-image">
                                    <img loading="lazy" width="22" height="22" src="{{ asset('build/assets/img/time.svg') }}" alt="">
                                </div>
                                <div><div style="margin-bottom:20px;">{!! $contactData['footer_open_time'] !!}</div></div>
                            </div>
                        </li>
                        @endif
                        @if(!empty($contactData['footer_address']))
                        <li>
                            <div class="d-flex">
                                <div class="icon-image">
                                    <img loading="lazy" width="22" height="22" src="{{ asset('build/assets/img/location.svg') }}" alt="">
                                </div>
                                <div>{{ $contactData['footer_address'] }}</div>
                            </div>
                        </li>
                        @endif
                    </ul>
                    <ul class="ch-socials list-unstyled">
                        @if(!empty($contactData['telegram']))
                        <li>
                            <a class="d-flex align-items-center" href="{{ $contactData['telegram'] }}">
                                <img loading="lazy" width="25" height="25" src="{{ asset('build/assets/img/telegram.svg') }}" alt="">
                                Telegram
                            </a>
                        </li>
                        @endif
                        @if(!empty($contactData['viber']))
                        <li>
                            <a class="d-flex align-items-center" href="{{ $contactData['viber'] }}">
                                <img loading="lazy" width="25" height="25" src="{{ asset('build/assets/img/viber.svg') }}" alt="">
                                Viber
                            </a>
                        </li>
                        @endif
                        @if(!empty($contactData['whatsapp']))
                        <li>
                            <a class="d-flex align-items-center" href="{{ $contactData['whatsapp'] }}">
                                <img loading="lazy" width="25" height="25" src="{{ asset('build/assets/img/whatsapp.svg') }}" alt="">
                                Whatsapp
                            </a>
                        </li>
                        @endif
                        @if(!empty($contactData['facebook']))
                        <li>
                            <a class="d-flex align-items-center" href="{{ $contactData['facebook'] }}">
                                <img loading="lazy" width="25" height="25" src="{{ asset('build/assets/img/messenger.svg') }}" alt="">
                                Messenger
                            </a>
                        </li>
                        @endif

                    </ul>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-7 col-lg-8 f-column">
                @if ($footerMenuOne->isNotEmpty())
                <div class="col-12 col-sm-4">
                    <div class="title-f">
                        {{ trans('storefront::layouts.footer_heading_1') }}
                    </div>
                    @include('storefront::public.layouts.sidebar_menu.footer_menu', ['menu' => $footerMenuOne])
                </div>
                @endif
                @if ($footerMenuTwo->isNotEmpty())
                <div class="col-12 col-sm-4">
                    <div class="title-f">
                        {{ trans('storefront::layouts.footer_heading_2') }}
                    </div>
                    @include('storefront::public.layouts.sidebar_menu.footer_menu', ['menu' => $footerMenuTwo])
                </div>
                @endif
                @if ($footerMenuThree->isNotEmpty())
                <div class="col-12 col-sm-4">
                    <div class="title-f">
                        {{ trans('storefront::layouts.footer_heading_3') }}
                    </div>
                    @include('storefront::public.layouts.sidebar_menu.footer_menu', ['menu' => $footerMenuThree])
                </div>
                @endif
            </div>
            <div class="copyright__payments col-12 d-flex flex-column flex-md-row justify-content-between w-100">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="copyright">{!! $copyrightText !!}</div>
                </div>
            </div>
        </div>
    </div>
</footer>



<script src="https://code.jquery.com/jquery-4.0.0.min.js"></script>

<script>
    window.Korf = {
        data: {
            baseUrl: '{{ url('/') }}',
            csrfToken: '{{ csrf_token() }}',
            routes: {
                cart_add: '{{ route('cart.items.store') }}',
                cart_get: '{{ route('cart.get') }}',
            }
        }
    };
</script>

@vite([
   'Modules/Storefront/Resources/assets/public/css/lib.css',
   'Modules/Storefront/Resources/assets/public/js/bootstrap.bundle.min.js',
   'Modules/Storefront/Resources/assets/public/js/lib.js',
   'Modules/Storefront/Resources/assets/public/js/main.js',
])

@if(!empty($contactData['showCallBackForm']))
    <script>
        window.callbackModalUrl = '{{ route('contact.callback.modal') }}';
        window.callbackStoreUrl = '{{ route('contact.callback') }}';
    </script>

    @vite([
        'Modules/Storefront/Resources/assets/public/js/callback.js',
    ])
@endif


@push('scripts')
    <script type="module">
        $('.store-phone').attr('href', `tel:{{ setting('store_phone') }}`);
        $('.store-email').attr('href', `mailto:{{ setting('store_email') }}`);
    </script>
@endpush
