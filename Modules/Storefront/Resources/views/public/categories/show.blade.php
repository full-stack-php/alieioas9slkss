@extends('storefront::public.layout')
@use('Spatie\SchemaOrg\Schema')
@section('title', isset($seoFilter) && filled($seoFilter->meta_title) ? $seoFilter->meta_title : ($category->meta->meta_title ?: $category->name))

@push('meta')
    <meta name="title" content="{{ isset($seoFilter) && filled($seoFilter->meta_title) ? $seoFilter->meta_title : ($category->meta->meta_title ?: $category->name) }}">
    <meta name="description" content="{{ isset($seoFilter) && filled($seoFilter->meta_description) ? $seoFilter->meta_description : $category->meta->meta_description }}">
    <meta name="twitter:card" content="summary">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ isset($seoFilter) && filled($seoFilter->meta_title) ? $seoFilter->meta_title : ($category->meta->meta_title ?: $category->name) }}">
    <meta property="og:description" content="{{ isset($seoFilter) && filled($seoFilter->meta_description) ? $seoFilter->meta_description : $category->meta->meta_description }}">
    <meta property="og:image" content="{{ $category->logo->path }}">
    <meta property="og:locale" content="{{ locale() }}">

    @foreach (supported_locale_keys() as $code)
        <meta property="og:locale:alternate" content="{{ $code }}">
    @endforeach
@endpush

@section('content')

    <main>
        <div class="container">

            <div id="listing-breadcrumbs">
                @include('storefront::public.partials.breadcrumbs')
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12" id="listing-heading">
                    <h1>{{ $seoFilter->h1 ?? $category->h1_name ?? $category->name }}</h1>
                </div>

                <div class="sub-categories">
                    <div class="swiper swiper-module swiper-sub-category">
                        <div class="swiper-sub-category__navigation type_arrow_bg_item">
                            <div class="swiper-sub-category__arrow swiper-sub-category__arrow_prev swiper-button-disabled">
                                <svg class="icon icon-18">
                                    <use xlink:href="#arrow-left"></use>
                                </svg>
                            </div>
                            <div class="swiper-sub-category__arrow swiper-sub-category__arrow_next">
                                <svg class="icon icon-18">
                                    <use xlink:href="#arrow-right"></use>
                                </svg>
                            </div>
                        </div>

                        <div class="swiper-wrapper">
                            @foreach ($category->children as $child)
                            <div class="swiper-slide text-center swiper-sub-category__item h-auto">
                                <a class="subcategory bg_item" href="{{ $child->getFullPath() }}">
                                    <div class="sl-image">
                                        <img loading="lazy" width="220" height="220" class="img-fluid" alt="{{ $child->name }}" src="{{ $child->logo->path }}">
                                    </div>
                                    <div class="sl-name">{{ $child->name }}</div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>


            <div class="row mt-2">
                <div id="column-left" class="col-12 col-md-3">
                    @include('storefront::public.products.index.oc_filter')
                </div>

                <div id="content" class="col-12 col-md-9">
                    <div
                        id="product-listing-content"
                        data-total="{{ method_exists($products, 'total') ? $products->total() : $products->count() }}"
                    >
                        @if($products->isNotEmpty())
                            <div class="view-box">
                                <div class="row">
                                    <div class="col-12 col-sm-12 localstorage d-flex align-items-center justify-content-between">
                                        <div class="ch-limit-sorts d-flex">
                                            <div class="btn-group d-inline-flex align-items-center">
                                                <button type="button" class="btn btn-sort-limit dropdown-toggle d-block d-md-none" data-bs-toggle="dropdown">
                                                    Дешевле
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" fill="none" viewBox="0 0 7 5"><path fill="currentColor" fill-rule="evenodd" d="M3.174 2.856a.5.5 0 00.707-.004L6.144.562a.5.5 0 01.712.704L4.592 3.555a1.5 1.5 0 01-2.121.012L.148 1.27A.5.5 0 11.852.559l2.322 2.297z" clip-rule="evenodd"></path></svg>
                                                </button>

                                                <div class="us-category-sort-title">Сортировка:</div>

                                                <ul class="dropdown-menu ddm-sort dropdown-menu-left ch-dropdown ddm-sort-list-pc">
                                                    @foreach (trans('storefront::products.sort_options') as $key => $value)
                                                        @php
                                                            $isActive = request('sort', 'latest') === $key;
                                                        @endphp

                                                        <li class="{{ $isActive ? 'active' : '' }}">
                                                            <button class="btn-sort-link" onclick="location.href='{{ request()->fullUrlWithQuery(['sort' => $key]) }}'">
                                                                {{ $value }}
                                                            </button>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="btn-group d-flex localstorage product_list_toolbar">
                                            <button aria-label="Grid View" type="button" id="grid-view" class="btn btn-view active" data-toggle="tooltip" title="Сетка">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M6.5 1H2a1 1 0 00-1 1v4.5a1 1 0 001 1h4.5a1 1 0 001-1V2a1 1 0 00-1-1zM16 1h-4.5a1 1 0 00-1 1v4.5a1 1 0 001 1H16a1 1 0 001-1V2a1 1 0 00-1-1zM6.5 10.5H2a1 1 0 00-1 1V16a1 1 0 001 1h4.5a1 1 0 001-1v-4.5a1 1 0 00-1-1zM16 10.5h-4.5a1 1 0 00-1 1V16a1 1 0 001 1H16a1 1 0 001-1v-4.5a1 1 0 00-1-1z"/>
                                                </svg>
                                            </button>

                                            <button aria-label="List View" type="button" id="list-view" class="btn btn-view" data-toggle="tooltip" title="Список">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-width="1.5" d="M4.8 1H1.2a.2.2 0 00-.2.2v3.029c0 .11.09.2.2.2h3.6a.2.2 0 00.2-.2V1.2a.2.2 0 00-.2-.2zM16.8 1H8.2a.2.2 0 00-.2.2v3.029c0 .11.09.2.2.2h8.6a.2.2 0 00.2-.2V1.2a.2.2 0 00-.2-.2zM4.8 7.286H1.2a.2.2 0 00-.2.2v3.028c0 .11.09.2.2.2h3.6a.2.2 0 00.2-.2V7.486a.2.2 0 00-.2-.2zM16.8 7.286H8.2a.2.2 0 00-.2.2v3.028c0 .11.09.2.2.2h8.6a.2.2 0 00.2-.2V7.486a.2.2 0 00-.2-.2zM4.8 13.571H1.2a.2.2 0 00-.2.2V16.8c0 .11.09.2.2.2h3.6a.2.2 0 00.2-.2v-3.029a.2.2 0 00-.2-.2zM16.8 13.571h-4.5a.2.2 0 00-.2.2V16.8c0 .11.09.2.2.2h4.5a.2.2 0 00.2-.2v-3.029a.2.2 0 00-.2-.2z"/>
                                                </svg>
                                            </button>

                                            <button aria-label="Price View" type="button" id="price-view" class="btn btn-view visible-lg" data-toggle="tooltip" title="Прайс">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-width="1.5" d="M16.8 1H1.2a.2.2 0 00-.2.2v3.029c0 .11.09.2.2.2h15.6a.2.2 0 00.2-.2V1.2a.2.2 0 00-.2-.2zM16.8 7.286H1.2a.2.2 0 00-.2.2v3.028c0 .11.09.2.2.2h15.6a.2.2 0 00.2-.2V7.486a.2.2 0 00-.2-.2zM1 16.8v-3.029c0-.11.09-.2.2-.2h15.6c.11 0 .2.09.2.2V16.8a.2.2 0 01-.2.2H1.2a.2.2 0 01-.2-.2z"/>
                                                </svg>
                                            </button>

                                            <div class="indicator-active"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row category-page no_bg_image">
                                @include('storefront::public.products.index.list_view_products', [
                                    'class' => 'col-6 col-sm-6 col-md-3 col-lg-3'
                                ])
                            </div>

                            {{ $products->links() }}
                        @else
                            <div class="empty-message text-center" style="margin-top: 40px;">
                                <h2>{{ trans('storefront::products.no_products_found') ?? 'Товары не найдены' }}</h2>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
                @if($products && $products->isNotEmpty())
                    @push('schema')
                        {!! Schema::itemList()->itemListElement(
                            $products->values()->map(fn($product, $index) =>
                               Schema::listItem()
                                    ->position($index + 1)
                                    ->url($product->url())
                            )->toArray()
                        )->toScript() !!}
                    @endpush
                @endif

            <div id="listing-seo-content">
                @if(isset($seoFilter) && filled($seoFilter->description))
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="p-content">
                                {!! $seoFilter->description !!}
                            </div>
                        </div>
                    </div>
                @elseif(strlen($category->description) > 7)
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="p-content">
                                {!! $category->description !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            @if($category->faqs)
                @include('storefront::public.partials.faqs', ['faqs'=> $category->faqs])
            @endif

            @include('storefront::public.partials.ai-sharing', ['type' => 'product_listing'])
        </div>
    </main>

@endsection
@push('scripts')
    @vite([
        'Modules/Storefront/Resources/assets/public/js/category.js',
        'Modules/Storefront/Resources/assets/public/js/oc_filter.js',
        'Modules/Storefront/Resources/assets/public/js/product_list_view.js',
    ])
@endpush
