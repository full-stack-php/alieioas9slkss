<div id="product-attributes-wrapper">
    <div class="table-responsive">
        <table class="options table table-bordered">
            <thead class="hidden-xs">
                <tr>
                    <th></th>
                    <th>{{ trans('attribute::admin.form.product.attribute') }}</th>
                    <th>{{ trans('attribute::admin.form.product.values') }}</th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="product-attributes">
                {{-- Product attributes will be added here dynamically using JS --}}
            </tbody>
        </table>
    </div>

    <button type="button" class="btn btn-secondary btn-sm" id="add-new-attribute">
        {{ trans('attribute::admin.form.product.add_new_attribute') }}
    </button>
</div>

@include('attribute::admin.products.tabs.templates.attribute')

@push('globals')
    <script>
        Korf.data['product.attributes'] = @json($productAttributes);
        Korf.errors['product.attributes'] = @json($errors->get('attributes.*'), JSON_FORCE_OBJECT);
    </script>
@endpush
@push('globals')
    @vite([
        'Modules/Attribute/Resources/assets/admin/sass/main.scss',
        'Modules/Attribute/Resources/assets/admin/js/main.js',
    ])
@endpush
