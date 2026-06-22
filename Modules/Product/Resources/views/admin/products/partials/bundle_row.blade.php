@php
    $specialPriceTypes = trans('product::products.form.special_price_types');
    $selectedId = old("bundles.{$index}.bundle_product_id", $bundle->bundle_product_id ?? null);

    $productObj = $bundle->bundleProduct ?? null;
    $productLabel = $productObj ? "{$productObj->name} (SKU: {$productObj->sku})" : ($selectedId ? "ID: {$selectedId}" : "");
@endphp
<tr class="bundle-row">
    <td>
        <div class="row g-2">
            <div class="col-6">
                <label class="small">{{ trans('product::products.form.bundle.main_product_qty') }}</label>
                <input type="number" name="bundles[{{ $index }}][product_qty]" value="{{ $bundle->product_qty ?? 1 }}" class="form-control form-control-sm">
                <input type="hidden" name="bundles[{{ $index }}][id]" value="{{ old("bundles.{$index}.id", $bundle->id ?? '') }}">
            </div>
            <div class="col-6">
                <label class="small">{{ trans('product::products.form.bundle.price') }}</label>
                <input type="number" name="bundles[{{ $index }}][product_price]" value="{{ $bundle->product_price ?? 0 }}" class="form-control form-control-sm" step="0.01">
            </div>
            <div class="col-6">
                <label class="small">{{ trans('product::products.form.bundle.special_price') }}</label>
                <input type="number" name="bundles[{{ $index }}][special_price]" value="{{ $bundle->special_price ?? 0 }}" class="form-control form-control-sm" step="0.01">
            </div>
            <div class="col-6">
                <label class="small">{{ trans('product::products.form.bundle.special_price_type') }}</label>
                <select name="bundles[{{ $index }}][special_price_type]" class="form-select form-select-sm">
                    @foreach ($specialPriceTypes as $key => $typeLabel)
                        <option value="{{ $key }}" {{ (isset($bundle->special_price_type) && $bundle->special_price_type === $key) ? 'selected' : '' }}>{{ $typeLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </td>

    <td style="vertical-align: middle;">
        <select name="bundles[{{ $index }}][bundle_product_id]" class="form-control ajax-product-search">
            @if($selectedId)
                <option value="{{ $selectedId }}" selected>
                    {{ $productLabel }}
                </option>
            @endif
        </select>
    </td>

    <td>
        <div class="row g-2">
            <div class="col-6">
                <label class="small">{{ trans('product::products.form.bundle.bundle_product_qty') }}</label>
                <input type="number" name="bundles[{{ $index }}][bundle_qty]" value="{{ $bundle->bundle_qty ?? 1 }}" class="form-control form-control-sm">
            </div>
            <div class="col-6">
                <label class="small">{{ trans('product::products.form.bundle.price') }}</label>
                <input type="number" name="bundles[{{ $index }}][bundle_price]" value="{{ $bundle->bundle_price ?? 0 }}" class="form-control form-control-sm" step="0.01">
            </div>
            <div class="col-6">
                <label class="small">{{ trans('product::products.form.bundle.special_price') }}</label>
                <input type="number" name="bundles[{{ $index }}][special_bundle_price]" value="{{ $bundle->special_bundle_price ?? 0 }}" class="form-control form-control-sm" step="0.01">
            </div>
            <div class="col-6">
                <label class="small">{{ trans('product::products.form.bundle.special_price_type') }}</label>
                <select name="bundles[{{ $index }}][special_bundle_price_type]" class="form-select form-select-sm">
                    @foreach ($specialPriceTypes as $key => $label)
                        <option value="{{ $key }}" {{ (isset($bundle->special_bundle_price_type) && $bundle->special_bundle_price_type === $key) ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </td>

    <td class="text-center" style="vertical-align: middle;">
        <button type="button" class="btn btn-soft-danger delete-bundle-row">
            <i class="bx bx-trash-alt"></i>
        </button>
    </td>
</tr>
