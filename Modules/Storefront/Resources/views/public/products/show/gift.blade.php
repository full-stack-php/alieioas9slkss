@php
    $productGiftRules = collect($product->productGifts ?? [])
        ->filter(function ($gift) {
            return $gift->is_active
                && empty($gift->parent_packaging_id)
                && $gift->giftProduct;
        })
        ->values();
@endphp

@if($productGiftRules->isNotEmpty())
    <div class="product-gifts">
        <div class="product-gifts__title">
            {{ trans('storefront::product.gifts_title') }}
        </div>

        <div class="product-gifts__list">
            @foreach($productGiftRules as $gift)
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

                <div class="product-gifts__item"
                     data-min-qty="{{ $gift->min_qty }}"
                     data-toggle="tooltip"
                     title="Потрібно мінімум {{ $gift->min_qty }} шт.">

                    @if($gift->price > 0)
                        <span class="product-gifts__price">
                            за {{ number_format((float) $gift->price, 0, '.', ' ') }} {{ currency_symbol(currency()) }}
                        </span>
                    @endif

                    <label title="{{ $giftProduct->name }}"
                           data-toggle="tooltip"
                           data-max-cnt="1"
                           data-min-qty="{{ $gift->min_qty }}"
                           for="ch-gift-rule-{{ $gift->id }}">

                        <input autocomplete="off"
                               type="checkbox"
                               name="ch_gifts[]"
                               value="{{ $gift->id }}"
                               id="ch-gift-rule-{{ $gift->id }}"
                               class="gift-checkbox js-product-gift-rule"
                               data-gift-price="{{ (float) $gift->price }}"
                               data-min-qty="{{ (int) $gift->min_qty }}"
                               data-gift-qty="{{ (int) ($gift->gift_qty ?: 1) }}"
                               data-is-repeatable="{{ $gift->is_repeatable ? 1 : 0 }}">

                        <div class="product-gifts__image">
                            <img width="56"
                                 height="56"
                                 src="{{ $giftProduct->base_image->resizeAndCrop(50, 50) }}"
                                 alt="{{ $giftProduct->name }}"
                                 loading="lazy">
                        </div>

                        @if($giftPackaging || $giftOptionsText)
                            <div class="product-gifts__info small text-muted">
                                @if($giftPackaging)
                                    <div>
                                        {{ sprintf($giftPackaging->name, $giftPackaging->qty) }}
                                    </div>
                                @endif

                                @if($giftOptionsText)
                                    <div>
                                        {{ $giftOptionsText }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </label>
                </div>
            @endforeach
        </div>
    </div>
@endif
