@extends('storefront::public.layout')

@section('title')
    @if (request()->has('query') || request()->has('search'))
        {{ trans('storefront::products.search_results_for') }}: "{{ request('query') ?? request('search') }}"
    @else
        {{ trans('storefront::products.shop') }}
    @endif
@endsection

@section('content')
    <main>
        <div class="container">
            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    <li><span>{{ trans('storefront::products.search') }}</span></li>
                </ul>
            </div>

            <div class="row">

                <div id="content">
                    <div class="row mb-4">
                        <div class="col-12 col-sm-10">
                            <input type="text" name="page_search" value="{{ request('search', request('query')) }}" placeholder="{{ trans('storefront::layouts.search_for_products') }}" id="page-search" class="form-control search-form-input" />
                        </div>
                        <div class="col-12 col-sm-2">
                            <input type="button" value="{{ trans('storefront::products.search') ?? 'Найти' }}" id="button-search" class="btn btn-primary w-100" />
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <h1>
                                @if(request('query') || request('search'))
                                    {{ trans('storefront::products.search_results_for') }} <span>{{ request('query') ?? request('search') }}</span>
                                @else
                                    {{ trans('storefront::products.shop') }}
                                @endif
                            </h1>
                        </div>
                    </div>

                    @if ($products->isNotEmpty())
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

                    @else
                        <div class="empty-message text-center" style="margin-top: 40px;">
                            <h2>{{ trans('storefront::products.no_products_found') ?? 'Товары не найдены' }}</h2>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
    @vite([
        'Modules/Storefront/Resources/assets/public/js/product_list_view.js',
    ])
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#button-search').on('click', function () {
                let url = new URL(window.location.href);
                url.searchParams.delete('page');

                let search = $('input[name=\'page_search\']').val();
                if (search) {
                    url.searchParams.set('query', search);
                } else {
                    url.searchParams.delete('query');
                }

                window.location.href = url.toString();
            });

            $('input[name=\'page_search\']').on('keydown', function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    $('#button-search').trigger('click');
                }
            });
        });
    </script>
@endpush
