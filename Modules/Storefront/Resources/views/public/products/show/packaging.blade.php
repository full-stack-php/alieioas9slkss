@if($product->packagings->isNotEmpty())
    <div class="product-packagings mb-5">
        <div class="product-packagings__title">{{ trans('storefront::product.packaging_title') }}</div>

        <div class="product-packagings__list">
            @foreach($product->packagings as $packaging)
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

                        <label class="form-check-label d-flex justify-content-between align-center align-content-center flex-wrap" for="packaging-{{ $packaging->id }}">
                            <div class="packaging-info d-flex justify-content-center align-center align-content-center flex-wrap flex-column">
                                <span class="packaging-name">
                                    {{ sprintf($packaging->name, $packaging->qty) }}
                                </span>

                                @if($packaging->gift)
                                    <div class="packaging-gift-label text-success small">
                                        + {{ $packaging->gift->name }} {{ trans('storefront::product.gift') }} за {!!  $packaging->gift->formatted_price !!}
                                    </div>
                                @endif
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
