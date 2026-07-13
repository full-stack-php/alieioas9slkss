<div class="row">
    <div class="col-md-12">
        {{ Form::text(
            'sku',
            trans('product::attributes.sku'),
            $errors,
            $product
        ) }}

        {{ Form::select(
            'stock_status',
            trans('product::attributes.manage_stock'),
            $errors,
            trans('product::products.form.manage_stock_states'),
            $product
        ) }}

        <div
            class="{{ (int) old('stock_status', $product->stock_status) === 1 ? '' : 'hide' }}"
            id="qty-field"
        >
            {{ Form::number(
                'qty',
                trans('product::attributes.qty'),
                $errors,
                $product,
                ['required' => true]
            ) }}
        </div>

        <div
            class="{{ in_array((int) old('stock_status', $product->stock_status), [0, 1], true) ? '' : 'hide' }}"
            id="in-stock-field"
        >
            {{ Form::select(
                'in_stock',
                trans('product::attributes.in_stock'),
                $errors,
                trans('product::products.form.stock_availability_states'),
                $product
            ) }}
        </div>
    </div>
</div>
