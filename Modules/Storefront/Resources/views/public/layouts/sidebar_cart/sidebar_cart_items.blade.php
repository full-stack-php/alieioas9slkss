<div class="header-cart-backdrop"></div>
<div class="cart-content">
    <div class="header-cart-fix-right" @if($cart->items()->isNotEmpty()) data-pids="{{ $cart->items()->pluck('product.id')->implode(',') }}" @endif>
        <div class="header-cart-top d-flex align-items-center justify-content-between">
            <div class="header-cart-title">{{ trans('storefront::cart.cart') }}</div>
            <button type="button" class="header-cart-close border-0 bg-transparent">
                <svg class="icon icon-22">
                    <use xlink:href="#cross"></use>
                </svg>
            </button>
        </div>


            @if ($cart->isEmpty())
                <div class="text-center box-empty-cart-icon mt-5">
                    <svg class="icon-empty-cart" id="icon-empty-shop-cart" xmlns="http://www.w3.org/2000/svg" width="195" height="151" fill="none" viewBox="0 0 195 151">
                        <path fill="#9CA9BC" d="m170.452 109.713 1.98.273-1.49 10.798-1.982-.274 1.492-10.797ZM38 45h2v32h-2V45Zm0 39h2v9h-2v-9Zm107.8 42.7c.2.3.5.4.8.4.2 0 .4-.1.6-.2 1.5-1 3.6-1.1 5.2-.1.5.3 1.1.2 1.4-.3.3-.5.2-1.1-.3-1.4-2.2-1.4-5.2-1.4-7.4.1-.5.4-.6 1-.3 1.5ZM70 113h9v2h-9v-2ZM5 144h13v2H5v-2Z"></path>
                        <path fill="#9CA9BC" d="m173 144 .3-.9 5-39.1h.7c2.2 0 4-1.4 4-3.2v-1.5c0-1.8-1.8-3.2-4-3.2h-34.7l9.1-22c.8-2-.1-4.4-2.2-5.2-1-.4-2.1-.4-3.1 0s-1.8 1.2-2.2 2.2L136.5 94c-1.2.4-2.1 1.1-2.9 2H132V37c0-3.3-2.7-6-5.9-6-6.3 0-12.6-.7-18.9-.9-4-14.7-14-25.1-25.7-25.1S59.8 15.4 55.8 30c-6.4.2-12.7.5-18.8 1-3.3 0-6 2.7-6 6v75.9L27.1 144H23v2h167v-2h-17Zm-1.7-1.2c-.1.9-.9 1.2-1.6 1.2h-33.8l-3.9-31.1V104h2.2c1.1 1 2.3 1.7 3.9 1.7s2.9-.6 3.9-1.7h34.3l-5 38.8Zm9.7-43.6v1.5c0 .6-.8 1.2-2 1.2h-35.1c.3-.7.5-1.5.5-2.3 0-.6-.1-1.1-.2-1.7H179c1.2.1 2 .8 2 1.3Zm-33.2-27.5c.2-.5.6-.9 1.1-1.1.5-.2 1-.2 1.5 0 1 .4 1.5 1.6 1.1 2.6l-9.1 22c-1.1-1-2.4-1.5-3.6-1.7l9-21.8Zm-9.5 24c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4Zm-5.7 2.3c-.2.5-.2 1.1-.2 1.7 0 .8.2 1.6.5 2.3h-.9v-4h.6ZM81.5 7c10.7 0 19.8 9.5 23.6 23-16.1-.5-31.9-.5-47.2 0 3.8-13.5 13-23 23.6-23ZM32.8 115H59v-2H33V37c0-2.2 1.8-4 4.1-4 6-.4 12.1-.7 18.3-.9-.5 2.3-.9 4.7-1.1 7.2-2.3.4-4.1 2.4-4.1 4.8 0 2.7 2.2 4.9 4.9 4.9s4.9-2.2 4.9-4.9c0-2.3-1.6-4.1-3.6-4.7.2-2.5.6-5 1.2-7.3 15.6-.5 31.7-.5 48.2.1.6 2.3.9 4.8 1.2 7.3-2.1.5-3.6 2.4-3.6 4.7 0 2.7 2.2 4.9 4.9 4.9s4.9-2.2 4.9-4.9c0-2.4-1.8-4.4-4.1-4.8-.2-2.4-.6-4.8-1.1-7.1 6.1.2 12.2.5 18.3.9 2.2 0 4 1.8 4 4v76H89v2h41.2l3.6 29H29.1l3.7-29.2Zm23.3-73.6c1 .4 1.8 1.5 1.8 2.6 0 1.6-1.3 2.9-2.9 2.9-1.6 0-2.9-1.3-2.9-2.9 0-1.3.8-2.3 1.9-2.7 0 .9-.1 1.8-.1 2.7h2c.1-.9.1-1.8.2-2.6ZM107 44h2c0-.9 0-1.8-.1-2.7 1.1.4 1.9 1.4 1.9 2.7 0 1.6-1.3 2.9-2.9 2.9-1.6 0-2.9-1.3-2.9-2.9 0-1.2.7-2.2 1.8-2.6.2.8.2 1.7.2 2.6Z"></path>
                        <path fill="#9CA9BC" d="M145.6 120.2a2 2 0 1 0 .001-3.999 2 2 0 0 0-.001 3.999Zm8 0a2 2 0 1 0 .001-3.999 2 2 0 0 0-.001 3.999Zm-67-40.9c-2.8-1.8-6.7-1.8-9.4.2-.5.3-.6.9-.3 1.4.2.3.5.4.8.4.2 0 .4-.1.6-.2 2.1-1.5 5.1-1.5 7.2-.1.5.3 1.1.2 1.4-.3.3-.5.1-1.1-.3-1.4Zm-12.9-5.9a2.7 2.7 0 1 0 0-5.4 2.7 2.7 0 0 0 0 5.4Zm16 0a2.7 2.7 0 1 0 0-5.4 2.7 2.7 0 0 0 0 5.4Z"></path>
                    </svg>
                </div>
                <div class="text-center cart-empty mt-3">{{ trans('storefront::cart.your_cart_is_empty') }}</div>
                <div class="text-center cart-empty-info-text text-muted">{{ trans('storefront::cart.looks_like_you_have_not_made_any_choice_yet') }}</div>
            @else
                <div class="header-cart-scroll d-flex flex-column">
                    <div class="header-cart-products">
                        @foreach ($cart->items() as $cartItem)
                            <div class="header-cart-product-item d-flex py-3">
                                <div class="header-cart-product-item-left position-relative" style="width: 80px;">
                                    <a href="{{ route('products.show', $cartItem->product->slug) }}">
                                        <img src="{{ $cartItem->product->base_image->path }}" alt="{{ $cartItem->product->name }}" class="img-fluid rounded" />
                                    </a>
                                    <button class="btn btn-link-delete text-danger p-0 position-absolute" type="button" onclick="cart.remove('{{ $cartItem->id }}');" title="Видалити">
                                        <svg class="icon icon-11">
                                            <use xlink:href="#cross"></use>
                                        </svg>
                                    </button>
                                </div>

                                <div class="header-cart-product-item-center d-flex flex-column flex-grow-1 px-3">
                                    <div class="header-cart-product-name mb-1">
                                        <a href="{{ route('products.show', $cartItem->product->slug) }}" class="text-decoration-none text-dark fw-bold">{{ $cartItem->product->name }}</a>
                                    </div>

                                    @if ($cartItem->options->isNotEmpty() || !empty($cartItem->packaging))
                                        <div class="header-cart-options small text-muted">
                                            {{-- Рендерим опции --}}
                                            @if ($cartItem->options->isNotEmpty())
                                                @foreach ($cartItem->options as $option)
                                                    <div class="header-cart-product-option d-flex">
                                                        <div class="hcp-option-name me-1">{{ $option->name }}:</div>
                                                        <div class="hcp-option-value">{{ $option->values->pluck('label')->implode(', ') }}</div>
                                                    </div>
                                                @endforeach
                                            @endif

                                            @if (!empty($cartItem->packaging))
                                                <div class="header-cart-product-option d-flex">
                                                    <div class="hcp-option-value">{{ sprintf($cartItem->packaging->name, $cartItem->packaging->qty) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($cartItem->isGift())
                                    <div class="ch-gift badge bg-success mt-1" style="width: fit-content;">{{ trans('storefront::cart.gift') }}</div>
                                    @endif
                                </div>

                                <div class="header-cart-price-quantity d-flex flex-column align-items-end justify-content-between">
                                    @if(!$cartItem->isGift())
                                        <div class="sl-cart-quantity d-flex align-items-center border rounded">
                                            <button class="btn btn-sm btn-quantity-minus border-0" type="button" onclick="cart.updateQuantity('{{ $cartItem->id }}', {{ $cartItem->qty - 1 }})">
                                                <svg class="icon icon-14">
                                                    <use xlink:href="#angel-left"></use>
                                                </svg>
                                            </button>
                                            <input type="text" class="form-control form-control-sm text-center border-0 p-0 m-0" style="width: 30px;" value="{{ $cartItem->qty }}" readonly>
                                            <button class="btn btn-sm btn-quantity-plus border-0" type="button" onclick="cart.updateQuantity('{{ $cartItem->id }}', {{ $cartItem->qty + 1 }})">
                                                <svg class="icon icon-14">
                                                    <use xlink:href="#angel-right"></use>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                    <div class="header-cart-item-total fw-bold mt-2">{{ $cartItem->totalPrice()->format() }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="header-cart-totals">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="header-cart-total-title">{{ trans('storefront::cart.cart_summary') }}:</span>
                            <span class="header-cart-total-text text-end">{{ $cart->subTotal()->format() }}</span>
                        </div>
                        @if($cart->hasShippingMethod())
                            <div class="d-flex justify-content-between mb-2">
                                <span class="header-cart-total-title">{{ trans('storefront::cart.delivery') }}:</span>
                                <span class="header-cart-total-text text-end">{{ $cart->shippingCost()->format() }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2">
                            <span class="header-cart-total-title">{{ trans('storefront::cart.total') }}:</span>
                            <span class="header-cart-total-text text-end">{{ $cart->total()->format() }}</span>
                        </div>
                    </div>

                    <div class="header-cart-actions mt-4">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <a class="btn btn-primary btn-lg w-100 mb-2 px-3" href="{{ route('checkout.create') }}">{{ trans('storefront::cart.proceed_to_checkout') }}</a>
                            </div>
                            <div class="col-12 col-sm-6">
                                <a class="chm-btn chm-btn-white chm-lg w-100" href="{{ route('cart.index') }}">{{ trans('storefront::cart.view_cart') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        <div class="header-cart-sticky">
            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <a class="chm-btn chm-btn-primary chm-lg w-100" href="{{ route('checkout.create') }}">Оформление заказа</a>
                </div>
            </div>
        </div>
    </div>
</div>
