<div class="search-result">
    <div class="search-result-top">
        <div class="content-left">
            @if(request('query'))
                <h4>
                    {{ trans('storefront::products.search_results_for') }}
                    <span>{{ request('query') }}</span>
                </h4>
            @elseif(isset($brand))
                <h4>{{ $brand->name }}</h4>
            @elseif(request('category'))
                <h4>{{ $categoryName ?? request('category') }}</h4>
            @elseif(request('tag'))
                <h4>{{ $tagName ?? request('tag') }}</h4>
            @else
                <h4>{{ trans('storefront::products.shop') }}</h4>
            @endif
        </div>

        <div class="content-right">
            <div class="sorting-bar">
                <div class="mobile-view-filter">
                    <i class="las la-sliders-h"></i>
                    {{ trans('storefront::products.filters') }}
                </div>

                <div class="view-type">
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
                       class="btn btn-grid-view {{ request('view', 'grid') === 'grid' ? 'active' : '' }}"
                       title="{{ trans('storefront::products.grid_view') }}">
                        <i class="las la-th-large"></i>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
                       class="btn btn-list-view {{ request('view', 'list') === 'list' ? 'active' : '' }}"
                       title="{{ trans('storefront::products.list_view') }}">
                        <i class="las la-list"></i>
                    </a>
                </div>

                <div class="mobile-view-filter-dropdown">
                    <div class="dropdown custom-dropdown">
                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                            <span>
                                {{ trans('storefront::products.sort_options')[request('sort', 'latest')] ?? trans('storefront::products.sort_options')['latest'] }}
                            </span>
                            <i class="las la-angle-down"></i>
                        </button>

                        <ul class="dropdown-menu">
                            <div class="dropdown-menu-scroll">
                                @foreach (trans('storefront::products.sort_options') as $key => $value)
                                    <li>
                                        <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => $key]) }}">
                                            {{ $value }}
                                        </a>
                                    </li>
                                @endforeach
                            </div>
                        </ul>
                    </div>
                    <div class="dropdown custom-dropdown">
                        <button class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                            <span>{{ request('perPage', 20) }}</span>
                            <i class="las la-angle-down"></i>
                        </button>

                        <ul class="dropdown-menu">
                            @foreach (trans('storefront::products.per_page_options') as $value)
                                <li>
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['perPage' => $value]) }}">
                                        {{ $value }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="search-result-middle">
        @if($products->isEmpty())
            <div class="empty-message">
                @include('storefront::public.products.index.empty_results_logo')
                <h2>{{ trans('storefront::products.no_products_found') }}</h2>
            </div>
        @else
          @include('storefront::public.products.index.list_view_products')
        @endif
    </div>

    @if($products->isNotEmpty())
        <div class="search-result-bottom">
            <span class="showing-results">
                {{ trans('storefront::products.showing_results', [
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'total' => $products->total()
                ]) }}
            </span>

            <div class="pagination-wrapper">
                {{ $products->appends(request()->query())->links('storefront::public.partials.pagination') }}
            </div>
        </div>
    @endif
</div>
