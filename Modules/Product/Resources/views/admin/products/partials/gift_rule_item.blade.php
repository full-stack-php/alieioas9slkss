@php
    $gift = $gift ?? null;
    $index = $index ?? '__INDEX__';
    $inputBase = 'product_gifts[' . $index . ']';

    $giftId = data_get($gift, 'id');
    $parentPackagingId = data_get($gift, 'parent_packaging_id');

    $giftProductId = data_get($gift, 'gift_product_id');
    $giftProductName = data_get($gift, 'gift_product_name') ?: data_get($gift, 'giftProduct.name');
    $giftProductSku = data_get($gift, 'gift_product_sku') ?: data_get($gift, 'giftProduct.sku');

    $giftPackagingId = data_get($gift, 'gift_packaging_id');
    $giftOptions = data_get($gift, 'options', []);
    $isActive = (int) data_get($gift, 'is_active', 1);

    $selectedProductLabel = '';

    if ($giftProductId) {
        if ($giftProductName) {
            $selectedProductLabel = $giftProductName . ($giftProductSku ? " (SKU: {$giftProductSku})" : '');
        } else {
            $selectedProductLabel = "ID: {$giftProductId}";
        }
    }
@endphp

<tr class="gift-rule"
    data-index="{{ $index }}"
    data-selected-options='@json($giftOptions)'
    data-selected-packaging="{{ $giftPackagingId }}">

    <td>
        <input type="hidden" name="{{ $inputBase }}[id]" value="{{ $giftId }}">

        <select name="{{ $inputBase }}[parent_packaging_id]" class="form-control parent-packaging-selector">
            <option value="">Для всего товара</option>

            @foreach($parentPackagings as $packaging)
                <option value="{{ $packaging['id'] }}" {{ (string) $parentPackagingId === (string) $packaging['id'] ? 'selected' : '' }}>
                    {{ $packaging['name'] }}
                </option>
            @endforeach
        </select>
    </td>

    <td>
        <select name="{{ $inputBase }}[gift_product_id]"
                class="form-control ajax-product-search gift-product-selector"
                data-selected="{{ $giftProductId }}">
            @if($giftProductId)
                <option value="{{ $giftProductId }}" selected>
                    {{ $selectedProductLabel }}
                </option>
            @endif
        </select>
    </td>

    <td>
        <input type="number"
               min="1"
               name="{{ $inputBase }}[min_qty]"
               value="{{ data_get($gift, 'min_qty', 1) }}"
               class="form-control">
    </td>

    <td>
        <input type="number"
               min="1"
               name="{{ $inputBase }}[gift_qty]"
               value="{{ data_get($gift, 'gift_qty', 1) }}"
               class="form-control">
    </td>

    <td class="text-center">
        <input type="hidden" name="{{ $inputBase }}[is_repeatable]" value="0">

        <input type="checkbox"
               name="{{ $inputBase }}[is_repeatable]"
               value="1"
            {{ data_get($gift, 'is_repeatable', 0) ? 'checked' : '' }}>
    </td>

    <td>
        <input type="text"
               name="{{ $inputBase }}[price]"
               value="{{ data_get($gift, 'price', 0) }}"
               class="form-control">
    </td>

    <td class="text-center">
        <input type="hidden" name="{{ $inputBase }}[is_active]" value="0">

        <input type="checkbox"
               name="{{ $inputBase }}[is_active]"
               value="1"
            {{ $isActive ? 'checked' : '' }}>
    </td>

    <td>
        <div class="gift-product-config {{ $giftProductId ? '' : 'd-none' }}">
            <div class="gift-product-options mb-2"></div>

            <label class="small mb-1">Упаковка подарка</label>

            <select name="{{ $inputBase }}[gift_packaging_id]"
                    class="form-control gift-packaging-selector"
                    data-selected="{{ $giftPackagingId }}">
                <option value="">Без упаковки</option>
            </select>
        </div>
    </td>

    <td class="text-center">
        <button type="button" class="btn btn-soft-primary delete-gift-rule">
            <i class="bx bx-trash-alt"></i>
        </button>
    </td>
</tr>
