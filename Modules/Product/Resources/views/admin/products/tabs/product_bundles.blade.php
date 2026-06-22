<div id="product-bundles-wrapper">
    @php
        $oldBundles = old('bundles');

        if ($oldBundles) {
            $bundleProductIds = collect($oldBundles)->pluck('bundle_product_id')->filter()->toArray();
            $productsInOld = \Modules\Product\Entities\Product::whereIn('id', $bundleProductIds)->get()->keyBy('id');

            $existingBundles = collect($oldBundles)->map(function($item) use ($productsInOld) {
                $bundleProductId = $item['bundle_product_id'] ?? null;

                return (object)[
                    'id' => $item['id'] ?? null,
                    'bundle_product_id' => $bundleProductId,
                    // ИСПРАВЛЕНО: было $productId, стало $bundleProductId
                    'bundleProduct' => $bundleProductId ? $productsInOld->get($bundleProductId) : null,
                    'product_qty' => $item['product_qty'] ?? 1,
                    'product_price' => $item['product_price'] ?? 0,
                    'special_price' => $item['special_price'] ?? 0,
                    'special_price_type' => $item['special_price_type'] ?? 'fixed',
                    'bundle_qty' => $item['bundle_qty'] ?? 1,
                    'bundle_price' => $item['bundle_price'] ?? 0,
                    'special_bundle_price' => $item['special_bundle_price'] ?? 0,
                    'special_bundle_price_type' => $item['special_bundle_price_type'] ?? 'fixed',
                ];
            });
        } else {
            $existingBundles = $product->bundles;
        }
    @endphp

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>{{ trans('product::products.form.bundle.main_product_settings') }}</th>
            <th style="width: 40%">{{ trans('product::products.form.bundle.bundle_product') }}</th>
            <th>{{ trans('product::products.form.bundle.bundle_product_settings') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody id="product-bundles-container">
        @foreach($existingBundles as $index => $bundle)
            @include('product::admin.products.partials.bundle_row', ['index' => $index, 'bundle' => $bundle])
        @endforeach
        </tbody>
    </table>
    <button type="button" class="btn btn-secondary btn-sm" id="add-bundle-btn">{{ trans('product::products.form.bundle.add_bundle') }}</button>
</div>

<template id="bundle-row-template">
    @include('product::admin.products.partials.bundle_row', ['index' => '__INDEX__', 'bundle' => null])
</template>
