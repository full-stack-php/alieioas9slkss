@php
    $idKey = $index;
    $inputBase = "gift_packagings[{$idKey}]";
@endphp

<tr class="gift-row">
    <td>
        <input type="hidden" name="{{ $inputBase }}[id]" value="{{ data_get($packaging, 'id') }}">
        @foreach (supported_locales() as $locale => $language)
            <div class="input-group input-group-sm mb-1">
                <span class="input-group-text">{{ strtoupper($locale) }}</span>
                <input type="text" name="gift_packagings[{{ $index }}][{{ $locale }}][name]"
                       value="{{ old("packagings.{$index}.{$locale}.name", data_get($packaging, "{$locale}.name")) }}"
                       class="form-control gift-name-input">
            </div>
        @endforeach
    </td>


    <td>
        <input type="number" name="gift_packagings[{{ $index }}][qty]" value="{{ $packaging['qty'] ?? 1 }}" class="form-control gift-qty-input" >
    </td>

    <td>
        <input type="text" name="gift_packagings[{{ $index }}][price]" value="{{ $packaging['price'] ?? 1 }}" class="form-control gift-qty-input">
        <input type="hidden" name="{{ $inputBase }}[special_price]" value="0">
        <input type="hidden" name="{{ $inputBase }}[special_price_type]" value="fixed">
    </td>

    <td class="text-center">
        <button type="button" class="btn btn-soft-primary delete-gift-row">
            <i class="bx bx-trash-alt"></i>
        </button>
    </td>
</tr>
