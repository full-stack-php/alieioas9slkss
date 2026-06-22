@php
    $idKey = $index;
    $inputBase = 'packagings[' . $idKey . ']';
    $specialPriceTypes = trans('product::products.form.special_price_types');
@endphp

<tr class="packaging-row">
    <td>
        <input type="hidden" name="{{ $inputBase }}[id]" value="{{ data_get($packaging, 'id') }}">
        @foreach (supported_locales() as $locale => $language)
            <div class="input-group input-group-sm mb-1">
                <span class="input-group-text" id="inputGroupPrepend{{$idKey}}">{{ $locale }}</span>
                <input type="text"
                       name="{{ $inputBase }}[{{ $locale }}][name]"
                       value="{{ old("packagings.{$index}.{$locale}.name", data_get($packaging, "{$locale}.name")) }}"
                       class="form-control packaging-name-input"
                       aria-describedby="inputGroupPrepend{{$idKey}}">
            </div>
            @if($errors->has("packagings.{$idKey}.{$locale}.name"))
                <span class="invalid-feedback">{{ $errors->first("packagings.{$idKey}.{$locale}.name") }}</span>
            @endif
        @endforeach
    </td>

    <td>
        <input type="text" name="{{ $inputBase }}[qty]" value="{{ $packaging['qty'] ?? 1 }}" class="form-control packaging-qty-input">
    </td>

    <td>
        <input type="text" name="{{ $inputBase }}[price]" value="{{ $packaging['price'] ?? 0 }}" class="form-control">
    </td>

    <td>
        <input type="text" name="{{ $inputBase }}[special_price]" value="{{ $packaging['special_price'] ?? 0 }}" class="form-control">
    </td>

    <td>
        <select name="{{ $inputBase }}[special_price_type]" class="form-control form-select">
            @foreach ($specialPriceTypes as $key => $label)
                <option value="{{ $key }}" {{ (isset($packaging['special_price_type']) && $packaging['special_price_type'] === $key) ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </td>

    <td>
        <select name="{{ $inputBase }}[gift_id]"
                class="form-control gift-id-selector"
                data-selected="{{ old("packagings.{$index}.gift_id", data_get($packaging, 'gift_id')) }}">
            <option value="">{{ trans('product::products.form.packaging.select') }}</option>
        </select>
    </td>

    <td class="text-center">
        <button type="button" class="btn btn-soft-primary delete-row" data-toggle="tooltip" title="{{ trans('product::products.form.packaging.delete_packaging') }}">
            <i class="bx bx-trash-alt"></i>
        </button>
    </td>
</tr>
