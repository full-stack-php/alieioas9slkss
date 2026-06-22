@use('Spatie\SchemaOrg\Schema')
@extends('storefront::public.layout')

@section('title', $brand->meta->meta_title ?? $brand->name )
@push('meta')
    <meta name="title" content="{{ $brand->meta->meta_title ?? $brand->name }}">
    <meta name="description" content="{{ $brand->meta->meta_description?? '' }}">
    <meta name="twitter:card" content="summary">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $brand->meta->meta_title ?? $brand->name }}">
    <meta property="og:description" content="{{ $brand->meta->meta_description?? '' }}">
    <meta property="og:image" content="{{ $brand->logo->path }}">
    <meta property="og:locale" content="{{ locale() }}">

    @foreach (supported_locale_keys() as $code)
        <meta property="og:locale:alternate" content="{{ $code }}">
    @endforeach
@endpush

@php
    $listItems = [
        Schema::listItem()
            ->position(1)
            ->name(setting('storefront_schema_site_name') ?? 'Superlens')
            ->item(route('home'))
    ];

    $listItems[] = Schema::listItem()
        ->position(2)
        ->name(trans('storefront::brands.brands'))
        ->item(route('brands.index'));

    $listItems[] = Schema::listItem()
        ->position(3)
        ->name($brand->name)
        ->item($brand->url());


    $breadcrumbSchema = Schema::breadcrumbList()
        ->itemListElement($listItems);
@endphp

@push('schema')
    {!! $breadcrumbSchema->toScript() !!}
@endpush

@section('content')

    <main>
        <div class="container">

            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    <li><a href="{{ route('brands.index') }}">{{ trans('storefront::brands.brands') }}</a></li>
                    <li><span>{{ $brand->name }}</span></li>
                </ul>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <h1>{{ $brand->h1_name ?? $brand->name }}</h1>
                </div>
            </div>

            @if(count($products) > 0)
                <div class="row mt-2">

                    <div id="content" class="col-12">
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
                                        <button aria-label="List View" type="button" id="list-view" class="btn btn-view " data-toggle="tooltip" title="Список">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-width="1.5" d="M4.8 1H1.2a.2.2 0 00-.2.2v3.029c0 .11.09.2.2.2h3.6a.2.2 0 00.2-.2V1.2a.2.2 0 00-.2-.2zM16.8 1H8.2a.2.2 0 00-.2.2v3.029c0 .11.09.2.2.2h8.6a.2.2 0 00.2-.2V1.2a.2.2 0 00-.2-.2zM4.8 7.286H1.2a.2.2 0 00-.2.2v3.028c0 .11.09.2.2.2h3.6a.2.2 0 00.2-.2V7.486a.2.2 0 00-.2-.2zM16.8 7.286H8.2a.2.2 0 00-.2.2v3.028c0 .11.09.2.2.2h8.6a.2.2 0 00.2-.2V7.486a.2.2 0 00-.2-.2zM4.8 13.571H1.2a.2.2 0 00-.2.2V16.8c0 .11.09.2.2.2h3.6a.2.2 0 00.2-.2v-3.029a.2.2 0 00-.2-.2zM16.8 13.571H8.2a.2.2 0 00-.2.2V16.8c0 .11.09.2.2.2h8.6a.2.2 0 00.2-.2v-3.029a.2.2 0 00-.2-.2z"/>
                                            </svg>
                                        </button>
                                        <button aria-label="Price View" type="button" id="price-view" class="btn btn-view visible-lg " data-toggle="tooltip" title="Прайс">
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
                            @include('storefront::public.products.index.list_view_products', ['class' => 'col-6 col-sm-6 col-md-3 col-lg-3'])
                        </div>
                    </div>
                </div>
                {{ $products->links() }}

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
            @endif


            @if(strlen($brand->description) > 7)
                <div class="row mt-5">
                    <div class="col-12 col-12">
                        <div class="p-content">
                            {!! $brand->description !!}
                        </div>
                    </div>
                </div>
            @endif


            @if($brand->faqs)
                @include('storefront::public.partials.faqs', ['faqs'=> $brand->faqs])
            @endif


        </div>
    </main>
@endsection


@push('scripts')
    @vite([
        'Modules/Storefront/Resources/assets/public/js/product_list_view.js',
    ])
@endpush

