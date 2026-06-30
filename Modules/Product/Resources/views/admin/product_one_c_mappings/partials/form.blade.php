@php
    $selectedOptions = old('product_options', $mapping->product_options ?? []);
    $selectedProductId = old('product_id', $mapping->product_id);
    $selectedPackagingId = old('product_packaging_id', $mapping->product_packaging_id);
    $externalId = old('external_id', $mapping->external_id);
@endphp

<div class="card card-body">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">
                    {{ trans('product::products.one_c_mappings.form.product') }}
                </label>

                <input type="text"
                       id="product-search-input"
                       class="form-control"
                       placeholder="{{ trans('product::products.one_c_mappings.form.product_search_placeholder') }}"
                       value="{{ $mapping->product_id ? $mapping->product->name : '' }}"
                       autocomplete="off">

                <input type="hidden"
                       name="product_id"
                       id="product-id-input"
                       value="{{ $selectedProductId }}">

                <div id="product-search-results"
                     class="list-group mt-1"
                     style="display: none;"></div>

                @if($errors->has('product_id'))
                    <span class="invalid-feedback d-block">{{ $errors->first('product_id') }}</span>
                @endif
            </div>
        </div>

        <div class="col-md-12">
            <div id="selected-product-info"
                 class="alert alert-info"
                 style="display: none;">
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">
                    {{ trans('product::products.one_c_mappings.form.packaging') }}
                </label>

                <select name="product_packaging_id"
                        id="product-packaging-select"
                        class="form-control form-select">
                    <option value="">
                        {{ trans('admin::admin.form.please_select') }}
                    </option>
                </select>

                @if($errors->has('product_packaging_id'))
                    <span class="invalid-feedback d-block">{{ $errors->first('product_packaging_id') }}</span>
                @endif
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">
                    {{ trans('product::products.one_c_mappings.form.options') }}
                </label>

                <div id="product-options-wrapper" class="border rounded p-3">
                    <div class="text-muted">
                        {{ trans('product::products.one_c_mappings.form.select_product_first') }}
                    </div>
                </div>

                @if($errors->has('product_options'))
                    <span class="invalid-feedback d-block">{{ $errors->first('product_options') }}</span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">
                    {{ trans('product::products.one_c_mappings.form.external_id') }}
                </label>

                <input type="text"
                       name="external_id"
                       id="external-id-input"
                       class="form-control"
                       value="{{ $externalId }}">

                @if($errors->has('external_id'))
                    <span class="invalid-feedback d-block">{{ $errors->first('external_id') }}</span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">
                    {{ trans('product::products.one_c_mappings.form.one_c_id_preview') }}
                </label>

                <input type="text"
                       id="one-c-id-preview"
                       class="form-control"
                       value="{{ $mapping->one_c_id }}"
                       readonly>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            {{ trans('admin::admin.buttons.save') ?? 'Сохранить' }}
        </button>

        <a href="{{ route('admin.product_one_c_mappings.index') }}" class="btn btn-secondary">
            {{ trans('admin::admin.buttons.cancel') ?? 'Отмена' }}
        </a>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('product-search-input');
            const productIdInput = document.getElementById('product-id-input');
            const resultsWrapper = document.getElementById('product-search-results');
            const packagingSelect = document.getElementById('product-packaging-select');
            const optionsWrapper = document.getElementById('product-options-wrapper');
            const selectedProductInfo = document.getElementById('selected-product-info');
            const externalIdInput = document.getElementById('external-id-input');
            const oneCPreview = document.getElementById('one-c-id-preview');

            const selectedPackagingId = @json((string) $selectedPackagingId);
            const selectedOptions = @json($selectedOptions ?: []);

            let currentProduct = null;
            let searchTimer = null;

            const searchUrl = @json(route('admin.product_one_c_mappings.products.search'));
            const configUrlTemplate = @json(route('admin.product_one_c_mappings.products.config', ['id' => '__ID__']));

            function buildOneCPreview() {
                const externalId = externalIdInput.value.trim();

                if (!externalId) {
                    oneCPreview.value = '';
                    return;
                }

                const baseOneCId = currentProduct && currentProduct.base_1c_id
                    ? String(currentProduct.base_1c_id).trim()
                    : '';

                if (baseOneCId && baseOneCId !== '0') {
                    oneCPreview.value = baseOneCId + '#' + externalId;
                    return;
                }

                oneCPreview.value = externalId;
            }

            function renderProductInfo(product) {
                if (!product) {
                    selectedProductInfo.style.display = 'none';
                    selectedProductInfo.innerHTML = '';
                    return;
                }

                selectedProductInfo.style.display = 'block';
                selectedProductInfo.innerHTML =
                    '<strong>' + product.name + '</strong>' +
                    (product.sku ? '<br>SKU: ' + product.sku : '') +
                    '<br>Базовый 1С ID товара: ' + (product.base_1c_id || 'не указан');
            }

            function renderPackagings(packagings) {
                packagingSelect.innerHTML = '<option value="">{{ trans('admin::admin.form.please_select') }}</option>';

                packagings.forEach(function (packaging) {
                    const option = document.createElement('option');

                    option.value = packaging.id;
                    option.textContent = packaging.name + ' / qty: ' + packaging.qty;

                    if (String(packaging.id) === String(selectedPackagingId)) {
                        option.selected = true;
                    }

                    packagingSelect.appendChild(option);
                });
            }

            function renderOptions(options) {
                optionsWrapper.innerHTML = '';

                if (!options.length) {
                    optionsWrapper.innerHTML = '<div class="text-muted">{{ trans('product::products.one_c_mappings.form.no_options') }}</div>';
                    return;
                }

                options.forEach(function (option) {
                    const group = document.createElement('div');
                    group.className = 'mb-3';

                    const label = document.createElement('label');
                    label.className = 'form-label';
                    label.textContent = option.name;

                    const select = document.createElement('select');
                    select.className = 'form-control form-select';
                    select.name = 'product_options[' + option.id + ']';

                    const emptyOption = document.createElement('option');
                    emptyOption.value = '';
                    emptyOption.textContent = '{{ trans('admin::admin.form.please_select') }}';
                    select.appendChild(emptyOption);

                    option.values.forEach(function (value) {
                        const valueOption = document.createElement('option');

                        valueOption.value = value.id;
                        valueOption.textContent = value.label;

                        if (
                            selectedOptions &&
                            String(selectedOptions[option.id]) === String(value.id)
                        ) {
                            valueOption.selected = true;
                        }

                        select.appendChild(valueOption);
                    });

                    group.appendChild(label);
                    group.appendChild(select);

                    optionsWrapper.appendChild(group);
                });
            }

            function loadProductConfig(productId) {
                if (!productId) {
                    return;
                }

                fetch(configUrlTemplate.replace('__ID__', productId), {
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(function (product) {
                        currentProduct = product;

                        renderProductInfo(product);
                        renderPackagings(product.packagings || []);
                        renderOptions(product.options || []);
                        buildOneCPreview();
                    });
            }

            function renderSearchResults(products) {
                resultsWrapper.innerHTML = '';

                if (!products.length) {
                    resultsWrapper.style.display = 'none';
                    return;
                }

                products.forEach(function (product) {
                    const item = document.createElement('button');

                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = product.text;

                    item.addEventListener('click', function () {
                        productIdInput.value = product.id;
                        searchInput.value = product.text;
                        resultsWrapper.style.display = 'none';

                        loadProductConfig(product.id);
                    });

                    resultsWrapper.appendChild(item);
                });

                resultsWrapper.style.display = 'block';
            }

            searchInput.addEventListener('input', function () {
                const query = searchInput.value.trim();

                productIdInput.value = '';

                clearTimeout(searchTimer);

                if (query.length < 2) {
                    resultsWrapper.style.display = 'none';
                    return;
                }

                searchTimer = setTimeout(function () {
                    fetch(searchUrl + '?q=' + encodeURIComponent(query), {
                        headers: {
                            'Accept': 'application/json',
                        },
                    })
                        .then(response => response.json())
                        .then(renderSearchResults);
                }, 300);
            });

            externalIdInput.addEventListener('input', buildOneCPreview);

            if (productIdInput.value) {
                loadProductConfig(productIdInput.value);
            }
        });
    </script>
@endpush
