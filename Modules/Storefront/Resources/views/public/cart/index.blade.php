@extends('storefront::public.layout')

@section('title', trans('storefront::cart.cart'))

@section('content')
    <section class="cart-wrap pb-5" id="cart-page-wrapper">
        <div class="container">

            @if(isset($breadcrumbs))
                <ul class="breadcrumb mt-3 mb-4">
                    @foreach ($breadcrumbs as $breadcrumb)
                        <li class="breadcrumb-item"><a href="{{ $breadcrumb['href'] }}">{{ $breadcrumb['text'] }}</a></li>
                    @endforeach
                </ul>
            @endif

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fa fa-check-circle me-2 fs-4"></i>
                    <div>{!! session('success') !!}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fa fa-exclamation-triangle me-2 fs-4"></i>
                    <div>{!! session('error') !!}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <h1 class="page-title mb-4">
                {{ trans('storefront::cart.cart') }}
                @if(!$cart->isEmpty())
                    <span class="text-muted fs-5">({{ $cart->count() }})</span>
                @endif
            </h1>

            @if ($cart->isEmpty())
                {{-- Пустая корзина --}}
                @include('storefront::public.cart.index.empty_cart')
            @else

                    <div class="page-cart d-flex flex-wrap flex-lg-nowrap gap-4">

                    <div class="cart-col-left">
                        <div class="cart-list">
                            @foreach ($cart->items() as $cartItem)
                                @php
                                    $product = $cartItem->item;
                                    $isGift = $cartItem->isGift();
                                    $hasOptions = $cartItem->options->isNotEmpty();
                                    $packaging = $cartItem->packaging->id ?? null;
                                @endphp

                                <div class="cart-item d-flex" data-cart-item="{{ $cartItem->id }}">

                                    <div class="cart-item-left">
                                        <a href="{{ route('products.show', $cartItem->product->slug) }}">
                                            @if ($cartItem->product->base_image->path ?? false)
                                                <img src="{{ $cartItem->product->base_image->path }}" alt="{{ $cartItem->product->name }}" class="img-fluid rounded" />
                                            @else
                                                <div class="image-placeholder rounded" style="width: 100px; height: 100px; background: #f4f4f4;"></div>
                                            @endif
                                        </a>
                                    </div>

                                    <div class="cart-item-center d-flex flex-column">
                                        <div class="cart-item-prod-name">
                                            <a href="{{ route('products.show', $cartItem->product->slug) }}">
                                                {{ $cartItem->product->name }}
                                            </a>
                                        </div>

                                        @if(isset($cartItem->product->sku))
                                            <div class="product-model text-muted small mb-2">{{ trans('checkout::attributes.model') ?? 'Артикул:' }} {{ $cartItem->product->sku }}</div>
                                        @endif

                                        @if ($hasOptions)
                                            <div class="cart-item-options">
                                                @foreach ($cartItem->options as $option)
                                                    <div class="cart-item-option d-flex">
                                                        <strong class="cart-item-option-name">{{ $option->name }}:</strong>
                                                        <span class="cart-item-option-value">
                                                            @foreach($option->values as $value)
                                                                    {{ $value->label }}@if(!$loop->last), @endif
                                                            @endforeach
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($packaging)
                                            <div class="cart-item-options small mb-1">
                                                <div class="cart-item-option d-flex">
                                                    <span class="cart-item-option-value">{{ sprintf($cartItem->packaging->name, $cartItem->packaging->qty) }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        @if($isGift)
                                            <div class="ch-gift">{{ trans('storefront::cart.gift') }}</div>
                                        @endif
                                    </div>

                                    <div class="cart-item-price-quantity d-flex">
                                        <div class="d-flex justify-content-end">

                                            @if(!$cartItem->isGift())
                                                <div class="sl-cart-quantity d-flex align-items-center border rounded">
                                                    <button class="btn btn-quantity-minus" type="button" onclick="sl_cart_minus('#input_pr_quantity_{{ $cartItem->id }}')">
                                                        <svg class="icon icon-14">
                                                            <use xlink:href="#angel-left"></use>
                                                        </svg>
                                                    </button>
                                                    <input type="text" class="form-control" value="{{ $cartItem->qty }}" id="input_pr_quantity_{{ $cartItem->id }}" onchange="updateQuantityCart('{{ $cartItem->id }}', $(this).val());" >
                                                    <button class="btn btn-quantity-plus" type="button" onclick="sl_cart_plus('#input_pr_quantity_{{ $cartItem->id }}')">
                                                        <svg class="icon icon-14">
                                                            <use xlink:href="#angel-right"></use>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                            <button type="button" data-toggle="tooltip" title="Видалити" class="btn btn-remove" onclick="cart.remove('{{ $cartItem->id }}');">
                                                <svg class="icon icon-11">
                                                    <use xlink:href="#cross"></use>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="cart-totals d-flex">
                                            <div class="cart-item-price">
                                                <span class="text-cart-item-price">{{ trans('storefront::cart.table.unit_price') }}</span>

                                                <span class="d-flex justify-content-center align-baseline gap-2" data-cart-unit-price-html>
                                                    @if($cartItem->hasDiscountedPrice())
                                                        <span class="price-old">{{ $cartItem->regularUnitPrice()->format() }}</span>
                                                        <span class="price-new">{{ $cartItem->unitPrice()->format() }}</span>
                                                    @else
                                                        <span class="price-new">{{ $cartItem->unitPrice()->format() }}</span>
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="cart-item-total" data-cart-line-total-html>
                                                <span class="text-cart-item-total">{{ trans('storefront::cart.total') }}</span>{{ $cartItem->totalPrice()->format() }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="cart-col-right">

                        <div class="checkout-cart-accordion" id="accordion">
                            <a href="#collapse-coupon" class="text-checkout-modules accordion-toggle d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-parent="#accordion">
                                {{ trans('storefront::cart.use_coupon') ?? 'Применить купон' }}
                                <svg class="icon icon-angel">
                                    <use xlink:href="#angel-down"></use>
                                </svg>
                            </a>
                            <div id="collapse-coupon" class="panel-collapse collapse">
                                @include('storefront::public.cart.index.coupon')
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-cart">
                                    <tr>
                                        <td class="text-left total-title">{{ trans('storefront::checkout.subtotal') }}:</td>
                                        <td  id="checkout_sub_total" class="text-right total-text">{{ $cart->subTotal()->format() }}</td>
                                    </tr>

                                    @if($cart->hasCoupon())
                                        <tr>
                                            <td class="text-left total-title">{{ trans('storefront::checkout.coupon') }} ({{ $cart->coupon()->code() }}):</td>
                                            <td class="text-right total-text">-{{ $cart->coupon()->value()->format() }}</td>
                                        </tr>
                                    @endif

                                    @if($cart->shouldShowCustomerGroupDiscount())
                                        <tr id="customer_group_discount_row">
                                            <td class="text-left total-title">
                                                {{ $cart->customerGroupDiscountLabel() }}:
                                            </td>
                                            <td id="customer_group_discount_value" class="text-right total-text">
                                                -{{ $cart->customerGroupDiscount()->format() }}
                                            </td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td class="text-left total-title total-last">{{ trans('storefront::checkout.total') }}:</td>
                                        <td id="checkout_total" class="text-right total-text">{{ $cart->total()->format() }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="d-flex">
                            <a href="{{ route('checkout.create') }}" class="btn btn-primary w-100">{{ trans('storefront::cart.checkout') }}</a>
                        </div>
                    </div>

                </div>
            @endif

            @if (isset($crossSellProducts) && $crossSellProducts->isNotEmpty())
                  345
            @endif
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        window.cartTotalLabel = @json(trans('storefront::cart.total'));
    </script>

    @vite([
       'Modules/Storefront/Resources/assets/public/js/checkout_cart.js',
    ])
@endpush

@push('styles')
    @vite([
       'Modules/Storefront/Resources/assets/public/css/checkout.css',
    ])
@endpush
