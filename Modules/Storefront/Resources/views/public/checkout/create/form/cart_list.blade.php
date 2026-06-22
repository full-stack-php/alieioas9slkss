<div class="cart-list">
    @foreach (Cart::instance()->items() as $cartItem)
        @php
            $product = $cartItem->item;
            $isGift = $cartItem->isGift();
            $hasOptions = $cartItem->options->isNotEmpty();
            $packaging = $cartItem->packaging->id ?? null;
        @endphp

        <div class="cart-item d-flex">
            <div class="cart-item-left">
                <a href="{{ $cartItem->product->slug ? route('products.show', $cartItem->product->slug) : '#' }}">
                    @if ($cartItem->product->base_image->path ?? false)
                        <img src="{{ $cartItem->product->base_image->path }}" alt="{{ $cartItem->product->name }}" class="img-responsive" width="60" height="60" />
                    @else
                        <div class="image-placeholder" style="width: 60px; height: 60px; background: #eee;"></div>
                    @endif
                </a>
            </div>

            <div class="cart-item-center d-flex flex-column">
                <div class="cart-item-prod-name">
                    <a href="{{ $cartItem->product->slug ? route('products.show', $cartItem->product->slug) : '#' }}">
                        {{ $cartItem->product->name }}
                    </a>
                </div>

                <!-- Артикул (Если нужно) -->
                @if(isset($cartItem->product->sku))
                    <div class="product-model">{{ trans('checkout::attributes.model') ?? 'Артикул:' }} {{ $cartItem->product->sku }}</div>
                @endif

                <!-- Опции товара -->
                @if ($hasOptions)
                    <div class="cart-item-options">
                        @foreach ($cartItem->options as $option)
                            <div class="cart-item-option d-flex">
                                <div class="cart-item-option-name">{{ $option->name }}:</div>
                                <div class="cart-item-option-value">
                                    @foreach($option->values as $value)
                                        {{ $value->label }}@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Упаковка (Если есть) -->
                @if ($packaging)
                    <div class="cart-item-options">
                        <div class="cart-item-option d-flex">
                            <div class="cart-item-option-name">{{ trans('storefront::cart.table.packaging') ?? 'Упаковка' }}:</div>
                            <div class="cart-item-option-value">{{ $cartItem->packaging->name }} ({{ $cartItem->packaging->qty }} шт.)</div>
                        </div>
                    </div>
                @endif

                <!-- Метка подарка -->
                @if($isGift)
                    <div class="ch-gift" style="color: #28a745; font-weight: bold; font-size: 12px; margin-top: 5px;">{{ trans('storefront::cart.gift') ?? 'Подарок' }}</div>
                @endif
            </div>

            <div class="cart-item-price-quantity d-flex">
                <div class="d-flex justify-content-end align-items-center">
                    @if(!$isGift)
                        <div class="ch-cart-quantity border rounded">
                            <span class="input-btn">
                                <button
                                    class="btn btn-quantity-minus opc-btn-update"
                                    type="button"
                                    data-action="minus"
                                    data-key="{{ $cartItem->id }}"
                                >
                                    <svg class="icon icon-14">
                                        <use xlink:href="#angel-left"></use>
                                    </svg>
                                </button>
                            </span>

                            <input
                                type="text"
                                class="form-control opc-cart-qty-input"
                                value="{{ $cartItem->qty }}"
                                data-key="{{ $cartItem->id }}"
                                data-minimum="{{ $cartItem->item->minimum ?? 1 }}"
                                inputmode="numeric"
                                autocomplete="off"
                            >

                            <span class="input-btn">
                                <button
                                    class="btn btn-quantity-plus opc-btn-update"
                                    type="button"
                                    data-action="plus"
                                    data-key="{{ $cartItem->id }}"
                                >
                                    <svg class="icon icon-14">
                                        <use xlink:href="#angel-right"></use>
                                    </svg>
                                </button>
                            </span>
                        </div>
                    @else
                        <div class="ch-cart-quantity" style="padding: 0 15px;">{{ $cartItem->qty }} шт.</div>
                    @endif
                    @if(!$isGift)
                            <button
                                type="button"
                                title="{{ trans('storefront::cart.remove') }}"
                                class="btn btn-remove opc-btn-remove"
                                data-action="remove"
                                data-key="{{ $cartItem->id }}"
                            >
                            <svg class="icon icon-11">
                                <use xlink:href="#cross"></use>
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="cart-totals d-flex">
                    <div class="cart-item-price"><span class="text-cart-item-price">{{ trans('storefront::cart.table.unit_price') ?? 'Цена:' }}</span> {{ $cartItem->unitPrice()->format() }}</div>
                    <div class="cart-item-total"><span class="text-cart-item-total">{{ trans('storefront::cart.table.line_total') ?? 'Итого:' }}</span> {{ $cartItem->totalPrice()->format() }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
