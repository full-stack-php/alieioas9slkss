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
                <th></th>
            </tr>
            </thead>

            <tbody id="product-packagings">

            @php
                if (old('packagings')) {
                    $existingPackagings = collect(old('packagings'));
                } else {
                    $existingPackagings = collect($product->packagings ?? [])->map(function($item) {
                        $data = $item->toArray();
                        // Превращаем список переводов в ассоциативный массив по локалям
                        foreach ($item->translations as $translation) {
                            $data[$translation->locale] = $translation->toArray();
                        }
                        return $data;
                    });
                }

                // Подготовка подарков
                if (old('gift_packagings')) {
                    $existingGifts = collect(old('gift_packagings'));
                } else {
                    $existingGifts = collect($product->giftPackagings ?? [])->map(function($item) {
                        $data = $item->toArray();
                        foreach ($item->translations as $translation) {
                            $data[$translation->locale] = $translation->toArray();
                        }
                        return $data;
                    });
                }
            @endphp

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

<div id="product-gift-packagings-wrapper">

    <hr />

    <h4 class="card-title my-3">{{ trans('product::products.tabs.gift_packaging') }}</h4>
    <div class="table-responsive">
        <table class="gift-packagings table table-bordered">
            <thead class="hidden-xs">
                <tr>
                    <th>{{ trans('product::products.form.packaging.name') }}</th>
                    <th>{{ trans('product::products.form.packaging.qty') }}</th>
                    <th>{{ trans('product::products.form.packaging.price') }}</th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="product-gift-packagings">
                @foreach ($existingGifts as $index => $gift)
                    @include('product::admin.products.partials.packaging_item_gift', [
                        'packaging' => $gift,
                        'index' => $index,
                    ])
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="button" class="btn btn-secondary btn-sm" id="add-new-packaging-gift">
        {{ trans('product::products.form.packaging.add_new_packaging_gift') }}
    </button>

    <template id="gift-packaging-template">
        @include('product::admin.products.partials.packaging_item_gift', [
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

