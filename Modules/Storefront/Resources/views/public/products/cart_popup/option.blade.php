<div
    class="product-configurator__option {{ $option['is_required'] ? 'required' : '' }}"
    data-option-group
    data-option-id="{{ $option['id'] }}"
    data-option-mirrored="{{ $option['is_mirrored'] ? 1 : 0 }}"
    data-name-prefix="{{ $namePrefix }}"
    data-error-key="{{ $namePrefix }}.{{ $option['id'] }}"
>
    <label class="product-configurator__option-label">
        {{ $option['name'] }}

        @if($option['is_required'])
            <span aria-hidden="true">*</span>
        @endif
    </label>

    @if($option['control'] === 'select')
        <select
            class="form-control"
            name="{{ $namePrefix }}[{{ $option['id'] }}]"
            id="{{ $idPrefix }}-option-{{ $option['id'] }}"
        >
            @if(count($option['values']) !== 1)
                <option value="">
                    {{ trans('storefront::product_configurator.choose_option') }}
                </option>
            @endif

            @foreach($option['values'] as $value)
                <option
                    value="{{ $value['id'] }}"
                    data-config-price
                    data-price-type="{{ $value['price_type'] }}"
                    data-price="{{ $value['price'] }}"
                    data-special-price-type="{{ $value['special_price_type'] }}"
                    data-special-price="{{ $value['special_price'] }}"
                    {{ count($option['values']) === 1 ? 'selected' : '' }}
                >
                    {{ $value['label'] }}
                </option>
            @endforeach
        </select>
    @elseif($option['control'] === 'radio')
        <div class="product-configurator__option-values">
            @foreach($option['values'] as $value)
                <label
                    class="product-configurator__option-value"
                    for="{{ $idPrefix }}-option-{{ $option['id'] }}-value-{{ $value['id'] }}"
                >
                    <input
                        type="radio"
                        name="{{ $namePrefix }}[{{ $option['id'] }}]"
                        id="{{ $idPrefix }}-option-{{ $option['id'] }}-value-{{ $value['id'] }}"
                        value="{{ $value['id'] }}"
                        data-config-price
                        data-price-type="{{ $value['price_type'] }}"
                        data-price="{{ $value['price'] }}"
                        data-special-price-type="{{ $value['special_price_type'] }}"
                        data-special-price="{{ $value['special_price'] }}"
                        {{ $loop->first ? 'checked' : '' }}
                    >

                    <span>{{ $value['label'] }}</span>
                </label>
            @endforeach
        </div>
    @elseif($option['control'] === 'checkbox')
        <div class="product-configurator__option-values">
            @foreach($option['values'] as $value)
                <label
                    class="product-configurator__option-value"
                    for="{{ $idPrefix }}-option-{{ $option['id'] }}-value-{{ $value['id'] }}"
                >
                    <input
                        type="checkbox"
                        name="{{ $namePrefix }}[{{ $option['id'] }}][]"
                        id="{{ $idPrefix }}-option-{{ $option['id'] }}-value-{{ $value['id'] }}"
                        value="{{ $value['id'] }}"
                        data-config-price
                        data-price-type="{{ $value['price_type'] }}"
                        data-price="{{ $value['price'] }}"
                        data-special-price-type="{{ $value['special_price_type'] }}"
                        data-special-price="{{ $value['special_price'] }}"
                    >

                    <span>{{ $value['label'] }}</span>
                </label>
            @endforeach
        </div>
    @elseif($option['control'] === 'textarea')
        <textarea
            class="form-control"
            name="{{ $namePrefix }}[{{ $option['id'] }}]"
            id="{{ $idPrefix }}-option-{{ $option['id'] }}"
            data-config-price
            data-price-type="{{ $option['pricing']['price_type'] }}"
            data-price="{{ $option['pricing']['price'] }}"
            data-special-price-type="{{ $option['pricing']['special_price_type'] }}"
            data-special-price="{{ $option['pricing']['special_price'] }}"
        ></textarea>
    @else
        <input
            type="{{ $option['input_type'] }}"
            class="form-control"
            name="{{ $namePrefix }}[{{ $option['id'] }}]"
            id="{{ $idPrefix }}-option-{{ $option['id'] }}"
            data-config-price
            data-price-type="{{ $option['pricing']['price_type'] }}"
            data-price="{{ $option['pricing']['price'] }}"
            data-special-price-type="{{ $option['pricing']['special_price_type'] }}"
            data-special-price="{{ $option['pricing']['special_price'] }}"
        >
    @endif

    <div
        class="invalid-feedback d-none js-configurator-field-error"
    ></div>
</div>
