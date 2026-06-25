@php
    $parentPackagings = collect($product->adminPackagings ?? $product->allPackagings ?? [])->map(function ($packaging) {
        return [
            'id' => $packaging->id,
            'name' => sprintf($packaging->name, $packaging->qty),
        ];
    });

    if (old('product_gifts')) {
        $existingGifts = collect(old('product_gifts'));
    } else {
        $existingGifts = collect($product->productGifts ?? [])->map(function ($gift) {
            return [
                'id' => $gift->id,
                'parent_packaging_id' => $gift->parent_packaging_id,
                'gift_product_id' => $gift->gift_product_id,
                'gift_product_name' => optional($gift->giftProduct)->name,
                'gift_product_sku' => optional($gift->giftProduct)->sku,
                'gift_packaging_id' => $gift->gift_packaging_id,
                'price' => $gift->price,
                'min_qty' => $gift->min_qty,
                'gift_qty' => $gift->gift_qty,
                'is_repeatable' => $gift->is_repeatable,
                'is_active' => $gift->is_active,
                'options' => method_exists($gift, 'selectedOptionsArray') ? $gift->selectedOptionsArray() : [],
            ];
        })->values();
    }
@endphp

<div id="product-gifts-wrapper">
    <div class="table-responsive">
        <table class="table table-bordered product-gifts-table">
            <thead>
            <tr>
                <th style="min-width: 220px;">Для упаковки</th>
                <th style="min-width: 280px;">Товар-подарок</th>
                <th style="min-width: 160px;">Мин. кол-во</th>
                <th style="min-width: 160px;">Кол-во подарка</th>
                <th style="min-width: 160px;">Повторять</th>
                <th style="min-width: 160px;">Цена подарка</th>
                <th style="min-width: 140px;">Активен</th>
                <th style="min-width: 360px;">Опции / упаковка подарка</th>
                <th></th>
            </tr>
            </thead>

            <tbody id="product-gifts-list">
            @foreach($existingGifts as $giftIndex => $gift)
                @include('product::admin.products.partials.gift_rule_item', [
                    'gift' => $gift,
                    'index' => $giftIndex,
                    'parentPackagings' => $parentPackagings,
                ])
            @endforeach
            </tbody>
        </table>
    </div>

    <button type="button" class="btn btn-secondary btn-sm" id="add-new-product-gift">
        Добавить подарок
    </button>

    <template id="product-gift-template">
        @include('product::admin.products.partials.gift_rule_item', [
            'gift' => null,
            'index' => '__INDEX__',
            'parentPackagings' => $parentPackagings,
        ])
    </template>
</div>
@push('globals')
    <script>
        Korf.data['gift_search'] = {
            searchPlaceholderValue: '{{ trans('product::products.form.gift.search_placeholder') }}',
            noResultsText: '{{ trans('product::products.form.gift.no_results') }}',
            itemSelectText: '{{ trans('product::products.form.gift.select') }}',
            searchChoices: false,
            shouldSort: false,
            removeItemButton: true,
        };

        Korf.data['ajax_url'] = '{{ route('admin.products.search') }}';

        Korf.data['gift_config_url'] = '{{ route('admin.products.gift_config', ['product' => '__PRODUCT_ID__']) }}';
    </script>
@endpush
