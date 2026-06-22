@extends('storefront::public.layout')

@section('title', $page->name)

@push('meta')
    <meta name="title" content="{{ $page->meta->meta_title ?: $page->name }}">
    <meta name="description" content="{{ $page->meta->meta_description }}">
    <meta name="twitter:card" content="summary">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $page->meta->meta_title ?: $page->name }}">
    <meta property="og:description" content="{{ $page->meta->meta_description }}">
    <meta property="og:image" content="{{ $logo }}">
    <meta property="og:locale" content="{{ locale() }}">

    @foreach (supported_locale_keys() as $code)
        <meta property="og:locale:alternate" content="{{ $code }}">
    @endforeach
@endpush

@section('content')
    <main>
        <div class="container">

            <div class="breadcrumb-box">
                <ul class="breadcrumb">
                    <li><a href="{{ route('home') }}">{{ trans('storefront::layouts.home') }}</a></li>
                    <li><span>{{ $page->name }}</span></li>
                </ul>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12">
                    <h1>{{ $page->h1_name ?? $page->name }}</h1>
                </div>
            </div>


            {!! $page->body !!}

        </div>
    </main>
@endsection

@push('globals')

@endpush
