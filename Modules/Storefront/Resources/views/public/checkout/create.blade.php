@extends('storefront::public.layout')

@section('title', trans('storefront::checkout.checkout'))

@section('content')
    <main class="checkout-wrap page-cart checkout_form" id="onepcheckout">
        <div class="container">

            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    <li><span>{{ trans('storefront::checkout.checkout') }}</span></li>
                </ul>
            </div>

            <div class="row">
                <div class="col-12 col-sm-12">
                    <h1>{{ trans('storefront::checkout.checkout') }}</h1>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert opc-alert-danger">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17ZM9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18Z" fill="#d8300e"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 4.50952C9.27614 4.50952 9.5 4.73338 9.5 5.00952V10.2151C9.5 10.4913 9.27614 10.7151 9 10.7151C8.72386 10.7151 8.5 10.4913 8.5 10.2151V5.00952C8.5 4.73338 8.72386 4.50952 9 4.50952Z" fill="#d8300e"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9 12.2985C9.27614 12.2985 9.5 12.5223 9.5 12.7985V13.6879C9.5 13.964 9.27614 14.1879 9 14.1879C8.72386 14.1879 8.5 13.964 8.5 13.6879V12.7985C8.5 12.5223 8.72386 12.2985 9 12.2985Z" fill="#d8300e"></path>
                    </svg>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">×</button>
                </div>
            @endif


            <form method="POST" action="{{ route('checkout.store') }}" class="checkout-form" id="main-checkout-form">
                @csrf

                <div class="row d-flex checkout-col-center">

                    <div class="checkout-col-left col-lg-8 col-md-12" style="width:70%">
                            <div class="col-12 col-sm-12 col-md-12 mb-30 opc_block_cart">
                                @include('storefront::public.checkout.create.form.cart_list')
                            </div>


                            <div class="col-12 col-sm-12 col-md-12 mb-30 opc_block_customer">
                                @include('storefront::public.checkout.create.form.account_details')
                            </div>

                            <div class="col-12 two-column">
                                <div class="row">
                                    <div class="col-12 col-lg-6 d-flex flex-column opc-left-column">
                                        <div class="h-100 w-100 mb-4 opc_block_shipping_method">
                                            @include('storefront::public.checkout.create.shipping')
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-6 d-flex flex-column opc-right-column">
                                        <div class="h-100 w-100 mb-4 opc_block_payment_method">
                                            @include('storefront::public.checkout.create.payment')
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 mb-30 opc_block_shipping_address">
                                @include('storefront::public.checkout.create.form.shipping_details')
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 mb-30 opc_block_comment">
                                @include('storefront::public.checkout.create.form.order_note')
                            </div>

                    </div>

                    <div class="checkout-col-fix-right col-lg-4 col-md-12" style="width: 30%;">
                        <div class="col-12 col-sm-12 col-md-12 mb-30 opc_block_totals">
                            @include('storefront::public.checkout.create.order_summary')
                        </div>
                    </div>

                </div>
            </form>

            <div id="opc-payment-gateways" class="hidden">

            </div>

        </div>
    </main>
@endsection

@push('pre-scripts')
    @if (setting('paypal_enabled'))
        <script src="https://www.paypal.com/sdk/js?client-id={{ setting('paypal_client_id') }}&currency={{ setting('default_currency') }}&disable-funding=credit,card,venmo,sepa,bancontact,eps,giropay,ideal,mybank,p24,p24"></script>
    @endif
@endpush
@push('scripts')
    <script>
        window.Korf = window.Korf || {};
        Korf.stripePublishableKey = '{{ setting("stripe_publishable_key") }}';
        Korf.stripeEnabled = {{ setting("stripe_enabled") ? 'true' : 'false' }};
        Korf.stripeIntegrationType = '{{ setting("stripe_integration_type") }}';

        Korf.langs = Korf.langs || {};
        Korf.langs['storefront::checkout.payment_for_order'] = '{{ trans("storefront::checkout.payment_for_order") }}';
        Korf.langs['storefront::checkout.remember_about_your_order'] = '{{ trans("storefront::checkout.remember_about_your_order") }}';

        window.CheckoutConfig = {
            cartUrl: `{{ route('cart.index') }}`,
            customerEmail: '{{ auth()->user()->email ?? null }}',
            customerPhone: '{{ auth()->user()->phone ?? null }}',
            customer_group_discount: @json(trans('storefront::checkout.customer_group_discount')),
            addresses: @json($addresses),
            defaultAddress: @json($defaultAddress),
            gateways: @json($gateways),
            countries: @json($countries),
            messages: {
                agree_terms: @json(trans('storefront::checkout.must_agree_to_terms')),
                coupon_success: @json(trans('storefront::checkout.coupon_applied')),
                coupon_error: @json(trans('storefront::checkout.coupon_error')),
                order_error: @json(trans('storefront::checkout.order_error')),
                select_option: @json(trans('storefront::checkout.select_option')),

                price: @json(trans('storefront::cart.table.unit_price')),
                total: @json(trans('storefront::cart.table.line_total')),
                subtotal: @json(trans('storefront::checkout.subtotal')),
                shipping: @json(trans('storefront::checkout.shipping_cost')),
                order_total: @json(trans('storefront::checkout.total')),
                coupon: @json(trans('storefront::checkout.coupon')),
                carrier_tariffs: @json(trans('shipping::shipping.carrier_tariffs'))
            }
        };
    </script>

    @vite([
       'Modules/Storefront/Resources/assets/public/js/checkout.js',
   ])

@endpush
@push('styles')
    @vite([
        'Modules/Storefront/Resources/assets/public/css/checkout.css',
    ])
@endpush
