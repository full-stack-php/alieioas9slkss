<tr class="gift-row">
    <td style="width: 50%">
        <select name="gifts[{{ $index }}][gift_product_id]"
                class="form-control ajax-product-search">

            @php
                $selectedId = old("gifts.{$index}.gift_product_id", $gift->id ?? null);
                $productObj = null;

                if (isset($gift->giftProduct)) {
                    $productObj = $gift->giftProduct;
                } elseif (isset($gift->id) && $gift->id == $selectedId) {
                    $productObj = $gift;
                }

                $label = '';
                if ($selectedId) {
                    if ($productObj && isset($productObj->name)) {
                        $label = $productObj->name . ($productObj->sku ? " (SKU: {$productObj->sku})" : "");
                    } else {
                        $label = "ID: {$selectedId}";
                    }
                }
            @endphp

            @if($selectedId)
                <option value="{{ $selectedId }}" selected>{{ $label }}</option>
            @endif
        </select>
    </td>
    <td>
        <input type="text"
               name="gifts[{{ $index }}][price]"
               value="{{ old("gifts.{$index}.price", $gift->pivot->price ?? 0) }}"
               class="form-control">
    </td>
    <td>
        <input type="text"
               name="gifts[{{ $index }}][min_qty]"
               value="{{ old("gifts.{$index}.min_qty", $gift->pivot->min_qty ?? 1) }}"
               class="form-control">
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-soft-danger delete-gift-row">
            <i class="bx bx-trash-alt"></i>
        </button>
    </td>
</tr>
