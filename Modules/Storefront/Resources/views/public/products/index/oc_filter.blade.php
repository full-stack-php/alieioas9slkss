<aside class="oc-filter-box" id="product-filter">
    <form
        id="product-filter-form"
        action="{{ url()->current() }}"
        method="GET"
        data-is-seo-filter="{{ isset($seoFilter) ? '1' : '0' }}"
        data-listing-url="{{ $seoFilterResetUrl ?? url()->current() }}"
        data-base-attribute="{{ $seoFilterBaseFilters['attribute'] ?? '' }}"
        data-base-manufacturers="{{ $seoFilterBaseFilters['manufacturers'] ?? '' }}"
        data-base-has-discount="{{ $seoFilterBaseFilters['has_discount'] ?? '' }}"
        data-base-price-min="{{ $seoFilterBaseFilters['price_min'] ?? '' }}"
        data-base-price-max="{{ $seoFilterBaseFilters['price_max'] ?? '' }}"
    >
        @if(request('query'))
            <input type="hidden" name="query" value="{{ request('query') }}">
        @endif

        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif

        <div class="filter-section ocf-price-section">
            <h6>Цена</h6>

            <div class="row g-2">
                <div class="col-6">
                    <input
                        type="number"
                        name="price[min]"
                        class="form-control js-price-min"
                        min="{{ $filterPrice['min'] ?? 0 }}"
                        max="{{ $filterPrice['max'] ?? 0 }}"
                        placeholder="{{ $filterPrice['min'] ?? 0 }}"
                        value="{{ request('price.min', $filterPrice['start_min'] ?? ($filterPrice['min'] ?? 0)) }}"
                    >
                </div>

                <div class="col-6">
                    <input
                        type="number"
                        name="price[max]"
                        class="form-control js-price-max"
                        min="{{ $filterPrice['min'] ?? 0 }}"
                        max="{{ $filterPrice['max'] ?? 0 }}"
                        placeholder="{{ $filterPrice['max'] ?? 0 }}"
                        value="{{ request('price.max', $filterPrice['start_max'] ?? ($filterPrice['max'] ?? 0)) }}"
                    >
                </div>
            </div>

            <div
                class="ocf-price-slider js-price-slider"
                data-min="{{ $filterPrice['min'] ?? 0 }}"
                data-max="{{ $filterPrice['max'] ?? 0 }}"
                data-start-min="{{ request('price.min', $filterPrice['start_min'] ?? ($filterPrice['min'] ?? 0)) }}"
                data-start-max="{{ request('price.max', $filterPrice['start_max'] ?? ($filterPrice['max'] ?? 0)) }}"
            ></div>
        </div>

        <div class="filter-section mt-3">
            <h6>Акционная цена</h6>

            <label class="form-check {{ !$filterDiscountSelected && $filterDiscountCount < 1 ? 'ocf-disabled' : '' }}">
                <input
                    type="checkbox"
                    class="form-check-input"
                    name="has_discount"
                    value="1"
                    @checked($filterDiscountSelected)
                    @disabled(!$filterDiscountSelected && $filterDiscountCount < 1)
                >

                <span class="form-check-label">
                    Только товары со скидкой
                </span>

                <span class="filter-count">{{ $filterDiscountCount }}</span>
            </label>
        </div>

        @if(isset($filterManufacturers) && $filterManufacturers->isNotEmpty())
            <div class="filter-section mt-3">
                <h6>Производитель</h6>

                <div class="filter-checkbox custom-scrollbar">
                    @foreach($filterManufacturers as $manufacturer)
                        <label class="form-check {{ !$manufacturer->is_filter_selected && $manufacturer->filter_count < 1 ? 'ocf-disabled' : '' }}">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="manufacturers[]"
                                value="{{ $manufacturer->id }}"
                                @checked($manufacturer->is_filter_selected)
                                @disabled(!$manufacturer->is_filter_selected && $manufacturer->filter_count < 1)
                            >

                            <span class="form-check-label">
                                {{ $manufacturer->name }}
                            </span>

                            <span class="filter-count">{{ $manufacturer->filter_count }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        @if(isset($attributes) && $attributes->isNotEmpty())
            @foreach($attributes as $attribute)
                <div class="filter-section mt-3">
                    <h6>{{ $attribute->name }}</h6>

                    <div class="filter-checkbox custom-scrollbar">
                        @foreach($attribute->values as $value)
                            <label class="form-check {{ !$value->is_filter_selected && $value->filter_count < 1 ? 'ocf-disabled' : '' }}">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="attribute[{{ $attribute->id }}][]"
                                    value="{{ $value->id }}"
                                    data-filter="attribute"
                                    data-attribute-id="{{ $attribute->id }}"
                                    data-value-id="{{ $value->id }}"
                                    @checked($value->is_filter_selected)
                                    @disabled(!$value->is_filter_selected && $value->filter_count < 1)
                                >

                                <span class="form-check-label">
                                    {{ $value->value }}
                                </span>

                                <span class="filter-count">{{ $value->filter_count }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif

        <div class="filter-actions mt-3">
            <button type="submit" class="btn btn-primary w-100">
                Показать
            </button>

            @if($filterHasActiveFilters)
                <a href="{{ $seoFilterResetUrl ?? url()->current() }}" class="btn btn-light w-100 mt-2">
                    Сбросить
                </a>
            @endif
        </div>
    </form>
</aside>

@push('styles')
    @vite([
        'Modules/Storefront/Resources/assets/public/css/oc_filter.css',
    ])
@endpush
