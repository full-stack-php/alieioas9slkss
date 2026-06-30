@php
    $idKey = $index;
    $inputBase = 'packagings[' . $idKey . ']';
    $errorBase = 'packagings.' . $idKey;
    $specialPriceTypes = trans('product::products.form.special_price_types');

    $hasRowErrors = collect($errors->getMessages())
        ->keys()
        ->contains(function ($key) use ($errorBase) {
            return str_starts_with($key, $errorBase . '.');
        });
@endphp

<tr class="packaging-row {{ $hasRowErrors ? 'has-error' : '' }}">
    <td>
        <input type="hidden" name="{{ $inputBase }}[id]" value="{{ data_get($packaging, 'id') }}">

        @foreach (supported_locales() as $locale => $language)
            @php
                $nameErrorKey = "{$errorBase}.{$locale}.name";
            @endphp

            <div class="input-group input-group-sm mb-1">
                <span class="input-group-text" id="inputGroupPrepend{{ $idKey }}{{ $locale }}">
                    {{ $locale }}
                </span>

                <input type="text"
                       name="{{ $inputBase }}[{{ $locale }}][name]"
                       value="{{ old("packagings.{$index}.{$locale}.name", data_get($packaging, "{$locale}.name")) }}"
                       class="form-control packaging-name-input {{ $errors->has($nameErrorKey) ? 'is-invalid' : '' }}"
                       aria-describedby="inputGroupPrepend{{ $idKey }}{{ $locale }}">
            </div>

            @if($errors->has($nameErrorKey))
                <div class="invalid-feedback d-block">
                    {{ $errors->first($nameErrorKey) }}
                </div>
            @endif
        @endforeach
    </td>

    <td>
        @php $qtyErrorKey = "{$errorBase}.qty"; @endphp

        <input type="text"
               name="{{ $inputBase }}[qty]"
               value="{{ old("packagings.{$index}.qty", data_get($packaging, 'qty', 1)) }}"
               class="form-control packaging-qty-input {{ $errors->has($qtyErrorKey) ? 'is-invalid' : '' }}">

        @if($errors->has($qtyErrorKey))
            <div class="invalid-feedback d-block">
                {{ $errors->first($qtyErrorKey) }}
            </div>
        @endif
    </td>

    <td>
        @php $priceErrorKey = "{$errorBase}.price"; @endphp

        <input type="text"
               name="{{ $inputBase }}[price]"
               value="{{ old("packagings.{$index}.price", data_get($packaging, 'price', 0)) }}"
               class="form-control {{ $errors->has($priceErrorKey) ? 'is-invalid' : '' }}">

        @if($errors->has($priceErrorKey))
            <div class="invalid-feedback d-block">
                {{ $errors->first($priceErrorKey) }}
            </div>
        @endif
    </td>

    <td>
        @php $specialPriceErrorKey = "{$errorBase}.special_price"; @endphp

        <input type="text"
               name="{{ $inputBase }}[special_price]"
               value="{{ old("packagings.{$index}.special_price", data_get($packaging, 'special_price', 0)) }}"
               class="form-control {{ $errors->has($specialPriceErrorKey) ? 'is-invalid' : '' }}">

        @if($errors->has($specialPriceErrorKey))
            <div class="invalid-feedback d-block">
                {{ $errors->first($specialPriceErrorKey) }}
            </div>
        @endif
    </td>

    <td>
        @php $specialPriceTypeErrorKey = "{$errorBase}.special_price_type"; @endphp

        <select name="{{ $inputBase }}[special_price_type]"
                class="form-control form-select {{ $errors->has($specialPriceTypeErrorKey) ? 'is-invalid' : '' }}">
            @foreach ($specialPriceTypes as $key => $label)
                <option value="{{ $key }}" {{ old("packagings.{$index}.special_price_type", data_get($packaging, 'special_price_type')) === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @if($errors->has($specialPriceTypeErrorKey))
            <div class="invalid-feedback d-block">
                {{ $errors->first($specialPriceTypeErrorKey) }}
            </div>
        @endif
    </td>

    <td class="text-center">
        <input type="hidden" name="{{ $inputBase }}[is_active]" value="0">

        <input type="checkbox"
               name="{{ $inputBase }}[is_active]"
               value="1"
            {{ old("packagings.{$index}.is_active", data_get($packaging, 'is_active', 1)) ? 'checked' : '' }}>
    </td>

    <td class="text-center">
        <button type="button"
                class="btn btn-soft-primary delete-row"
                data-toggle="tooltip"
                title="{{ trans('product::products.form.packaging.delete_packaging') }}">
            <i class="bx bx-trash-alt"></i>
        </button>
    </td>
</tr>
