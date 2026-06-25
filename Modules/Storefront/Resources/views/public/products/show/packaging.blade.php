@if($product->packagings->isNotEmpty())
    @php
        $packagingGiftRules = collect($product->productGifts ?? [])
            ->filter(function ($gift) {
                return $gift->is_active
                    && !empty($gift->parent_packaging_id)
                    && $gift->giftProduct;
            })
            ->groupBy('parent_packaging_id');
    @endphp

    <div class="product-packagings mb-5">
        <div class="product-packagings__title">
            {{ trans('storefront::product.packaging_title') }}
        </div>

        <div class="product-packagings__list">
            @foreach($product->packagings as $packaging)
                @php
                    $giftsForPackaging = $packagingGiftRules->get($packaging->id, collect());
                @endphp

                <div class="product-packagings__item">
                    <div class="form-check packaging-input_wrap">
                        <input class="form-check-input"
                               type="radio"
                               name="packaging_id"
                               id="packaging-{{ $packaging->id }}"
                               data-prefix="fixed"
                               data-price="{{ $packaging->price * $packaging->qty }}"
                               data-prefix-special="{{ $packaging->special_price_type }}"
                               data-price-special="{{ $packaging->special_price_type == 'fixed' ? $packaging->special_price * $packaging->qty : $packaging->special_price }}"
                               value="{{ $packaging->id }}"
                            {{ $loop->first ? 'checked' : '' }}>

                        <label class="form-check-label d-flex justify-content-between align-center align-content-center flex-wrap"
                               for="packaging-{{ $packaging->id }}">

                            <div class="packaging-info d-flex justify-content-center align-center align-content-center flex-wrap flex-column">
                                <span class="packaging-name">
                                    {{ sprintf($packaging->name, $packaging->qty) }}
                                </span>

                                @foreach($giftsForPackaging as $gift)
                                    @php
                                        $giftProduct = $gift->giftProduct;
                                        $giftPackaging = $gift->giftPackaging;

                                        $giftOptionsText = collect($gift->options ?? [])
                                            ->map(function ($option) {
                                                $optionName = optional($option->productOption)->name;
                                                $valueName = optional($option->productOptionValue)->label;

                                                if (!$optionName && !$valueName) {
                                                    return null;
                                                }

                                                return $optionName && $valueName
                                                    ? $optionName . ': ' . $valueName
                                                    : ($valueName ?: $optionName);
                                            })
                                            ->filter()
                                            ->implode(', ');
                                    @endphp

                                    <div class="packaging-gift-label text-success small js-packaging-gift-rule"
                                         data-parent-packaging-id="{{ $packaging->id }}"
                                         data-gift-price="{{ (float) $gift->price }}"
                                         data-min-qty="{{ (int) $gift->min_qty }}"
                                         data-gift-qty="{{ (int) ($gift->gift_qty ?: 1) }}"
                                         data-is-repeatable="{{ $gift->is_repeatable ? 1 : 0 }}">
                                        + {{ trans('storefront::product.gift') }}

                                        {{ $giftProduct->name }}

                                        @if($giftPackaging)
                                            / {{ sprintf($giftPackaging->name, $giftPackaging->qty) }}
                                        @endif

                                        @if($giftOptionsText)
                                            / {{ $giftOptionsText }}
                                        @endif

                                        @if((float) $gift->price > 0)
                                            за {{ number_format((float) $gift->price, 0, '.', ' ') }} {{ currency_symbol(currency()) }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="packaging-price d-flex justify-content-center align-center align-content-center flex-wrap flex-column">
                                {!! $packaging->formatted_price !!}
                            </div>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
