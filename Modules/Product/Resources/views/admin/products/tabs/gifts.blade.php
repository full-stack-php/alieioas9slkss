<div id="product-gifts-wrapper">
    @php
        $oldGifts = old('gifts');

        if ($oldGifts) {
            $giftIds = collect($oldGifts)->pluck('gift_product_id')->filter()->toArray();
            $productsInOld = \Modules\Product\Entities\Product::whereIn('id', $giftIds)->get()->keyBy('id');

            $existingGifts = collect($oldGifts)->map(function($item) use ($productsInOld) {
                $productId = $item['gift_product_id'] ?? null;
                $product = $productId ? $productsInOld->get($productId) : null;
                return (object)[
                    'id' => $productId,
                    'giftProduct' => $product,
                    'pivot' => (object)[
                        'price' => $item['price'] ?? 0,
                        'min_qty' => $item['min_qty'] ?? 1,
                    ]
                ];
            });
        } else {
            $existingGifts = $product->gifts;
        }
    @endphp


    <table class="table table-bordered">
        <thead>
        <tr>
            <th>{{ trans('product::products.form.gift.select_product') }}</th>
            <th>{{ trans('product::products.form.gift.price') }}</th>
            <th>{{ trans('product::products.form.gift.min_qty') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody id="product-gifts-container">
        @foreach ($existingGifts as $index => $gift)
            @include('product::admin.products.partials.gift_row', [
                'gift' => $gift,
                'index' => $index,
            ])
        @endforeach
        </tbody>
    </table>
    <button type="button" class="btn btn-secondary btn-sm" id="add-gift-btn">{{ trans('product::products.form.gift.add_gift') }}</button>
</div>

<template id="gift-row-template">
    @include('product::admin.products.partials.gift_row', ['index' => '__INDEX__', 'gift' => null])
</template>

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
    </script>
@endpush
