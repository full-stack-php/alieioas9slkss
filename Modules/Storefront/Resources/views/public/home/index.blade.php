@use('Spatie\SchemaOrg\Schema')
@extends('storefront::public.layout')

@section('title', setting('storefront_seo_data_meta_title'))
@push('meta')
    <meta name="description" content="{{ setting('storefront_seo_data_meta_description') }}">
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
            ->name(setting('storefront_schema_site_home_page_name'))
            ->item(route('home') . '#' . setting('storefront_schema_site_home_page_name_hashtage'));


    $breadcrumbSchema = Schema::breadcrumbList()
        ->itemListElement($listItems);
@endphp

@push('schema')
    {!! $breadcrumbSchema->toScript() !!}
@endpush


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 home-page-content-top">
                <div class="row chm-ms-box">
                    @includeUnless(is_null($slider), 'storefront::public.home.sections.hero')

                    @includeUnless(is_null($secondSlider), 'storefront::public.home.sections.hero_second')

                </div>
            </div>
        </div>

        @if (setting('storefront_features_section_enabled'))
            @include('storefront::public.home.sections.home_features')
        @endif

        @if (setting('storefront_three_column_banners_enabled'))
            @include('storefront::public.home.sections.three_column_full_width_banner')
        @endif

        @if (setting('storefront_product_tabs_1_section_enabled'))
            @include('storefront::public.home.sections.product_tabs_one')
        @endif



        @if (setting('storefront_blogs_section_enabled'))
            @include('storefront::public.home.sections.blog')
        @endif

        @php
            $seoDescription = setting('storefront_seo_data_description');
        @endphp
        @if(!empty($seoDescription))
            <div class="row">
                <div class="col-12">
                    <div class="p-content">
                        {!! $seoDescription !!}
                    </div>
                </div>
            </div>
        @endif

        @include('storefront::public.partials.ai-sharing', ['type' => 'home_page'])

    </div>
@endsection


@push('globals')
{{--    @vite([--}}
{{--        'Modules/Storefront/Resources/assets/public/sass/pages/home/main.scss',--}}
{{--        'Modules/Storefront/Resources/assets/public/js/pages/home/main.js',--}}
{{--    ])--}}
@endpush
