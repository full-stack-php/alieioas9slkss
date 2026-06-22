@if ($product->hasGift())
    <div class="product-gifts">
        <div class="product-gifts__title">
            {{ trans('storefront::product.gifts_title') }}
        </div>

        <div class="product-gifts__list">
            @foreach ($product->gifts as $gift)
                <div class="product-gifts__item"
                     data-min-qty="{{ $gift->pivot->min_qty }}"
                     data-toggle="tooltip"
                     title="Потрібно мінімум {{ $gift->pivot->min_qty }} шт."
                >
                    @if($gift->pivot->price > 0)
                        <span class="product-gifts__price">
                                за {{ number_format((float)$gift->pivot->price, 0, '.', ' ') }} {{ currency_symbol(currency()) }}
                            </span>
                    @endif

                    <label title="{{ $gift->name }}"
                           data-toggle="tooltip"
                           data-max-cnt="1"
                           data-min-qty="{{ $gift->pivot->min_qty }}"
                           for="ch-gift-{{ $gift->id }}">

                        <input autocomplete="off"
                               type="checkbox"
                               name="ch_gifts[]"
                               value="{{ $gift->id }}"
                               id="ch-gift-{{ $gift->id }}"
                               class="gift-checkbox">

                        <div class="product-gifts__image">
                            <img width="56"
                                 height="56"
                                 src="{{ $gift->base_image->resizeAndCrop(50, 50) }}"
                                 alt="{{ $gift->name }}"
                                 loading="lazy">
                        </div>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
@endif
