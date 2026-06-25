@php
    if (old('packagings')) {
        $existingPackagings = collect(old('packagings'));
    } else {
        $existingPackagings = collect($product->adminPackagings ?? $product->allPackagings ?? [])->map(function($item) {
            $data = $item->toArray();

            foreach ($item->translations as $translation) {
                $data[$translation->locale] = $translation->toArray();
            }

            return $data;
        });
    }
@endphp

<div id="product-packagings-wrapper">
    <div class="table-responsive">
        <table class="packagings table table-bordered">
            <thead class="hidden-xs">
            <tr>
                <th>{{ trans('product::products.form.packaging.name') }}</th>
                <th>{{ trans('product::products.form.packaging.qty') }}</th>
                <th>{{ trans('product::products.form.packaging.price') }}</th>
                <th>{{ trans('product::products.form.packaging.special_price') }}</th>
                <th>{{ trans('product::products.form.packaging.special_price_type') }}</th>
                <th>Активна</th>
                <th></th>
            </tr>
            </thead>

            <tbody id="product-packagings">
            @foreach ($existingPackagings as $index => $packaging)
                @include('product::admin.products.partials.packaging_item', [
                    'packaging' => $packaging,
                    'index' => $index,
                ])
            @endforeach
            </tbody>
        </table>
    </div>

    <button type="button" class="btn btn-secondary btn-sm mb-3" id="add-new-packaging">
        {{ trans('product::products.form.packaging.add_new_packaging') }}
    </button>

    <template id="packaging-template">
        @include('product::admin.products.partials.packaging_item', [
            'packaging' => null,
            'index' => '__INDEX__',
        ])
    </template>
</div>

@push('globals')
    <script>
        Korf.data['current_locale'] = '{{ locale() }}';
        Korf.errors['product.attributes'] = @json($errors->get('attributes.*'), JSON_FORCE_OBJECT);
    </script>
@endpush
