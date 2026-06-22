@extends('storefront::public.layout')

@section('title', trans('storefront::contact.contact'))
@push('meta')
    <meta name="title" content="{{ $blogPost->meta->meta_title }}">
    <meta name="description" content="{{ $blogPost->meta->meta_description }}">
    <meta name="twitter:card" content="summary">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $blogPost->meta->meta_title }}">
    <meta property="og:description" content="{{ $blogPost->meta->meta_description }}">
    <meta property="og:image" content="{{ $blogPost->full_image->path }}">
    <meta property="og:locale" content="{{ locale() }}">

    @foreach (supported_locale_keys() as $code)
        <meta property="og:locale:alternate" content="{{ $code }}">
    @endforeach
@endpush
@section('content')
<main>
    <div class="container">

        @include('storefront::public.partials.breadcrumbs')

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h1>{{ $blogPost->h1_name ?? $blogPost->name }}</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                @if (!$blogPost->full_image->path)
                    <div class="image-artricle w-100 mb-3">
                        <img src="{{ asset('build/assets/image-placeholder.png') }}" alt="{{ $blogPost->name }}" class="img-fluid rounded-2 w-100" />
                    </div>
                @else
                    <div class="image-artricle w-100  mb-3">
                        <img src="{{ $blogPost->full_image->path }}" alt="{{ $blogPost->name }}" class="img-fluid rounded-2 w-100" />
                    </div>
                @endif

                <div class="info-article d-flex flex-wrap align-items-center">
                    <div class="date-added-article d-flex align-items-center gap-2">
                        <span class="icon-calendar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="20" fill="none" viewBox="0 0 19 20">
                                <path fill="currentColor" fill-rule="evenodd" d="M5.506.25a.75.75 0 0 1 .75.75v1.05h5.873V1a.75.75 0 0 1 1.5 0v1.05h.157a4.35 4.35 0 0 1 4.35 4.35v9a4.35 4.35 0 0 1-4.35 4.35H4.6A4.35 4.35 0 0 1 .25 15.4v-9A4.35 4.35 0 0 1 4.6 2.05h.156V1a.75.75 0 0 1 .75-.75Zm-.75 3.3H4.6A2.85 2.85 0 0 0 1.75 6.4v1.05h14.886V6.4a2.85 2.85 0 0 0-2.85-2.85h-.156V4.6a.75.75 0 0 1-1.5 0V3.55H6.256V4.6a.75.75 0 0 1-1.5 0V3.55Zm11.88 5.4H1.75v6.45a2.85 2.85 0 0 0 2.85 2.85h9.186a2.85 2.85 0 0 0 2.85-2.85V8.95ZM4.3 12.25a.75.75 0 0 1 .75-.75h.9a.75.75 0 0 1 0 1.5h-.9a.75.75 0 0 1-.75-.75Zm3.6 0a.75.75 0 0 1 .75-.75h.9a.75.75 0 0 1 0 1.5h-.9a.75.75 0 0 1-.75-.75Zm3.6 0a.75.75 0 0 1 .75-.75h.9a.75.75 0 0 1 0 1.5h-.9a.75.75 0 0 1-.75-.75Zm-7.2 2.7a.75.75 0 0 1 .75-.75h.9a.75.75 0 0 1 0 1.5h-.9a.75.75 0 0 1-.75-.75Zm3.6 0a.75.75 0 0 1 .75-.75h.9a.75.75 0 0 1 0 1.5h-.9a.75.75 0 0 1-.75-.75Zm3.6 0a.75.75 0 0 1 .75-.75h.9a.75.75 0 0 1 0 1.5h-.9a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                        {{ $blogPost->created_at->format('d M, Y') }}
                    </div>
                </div>

                <div class="description-article">
                    {!! $blogPost->description !!}
                </div>

                @if($blogPost->faqs)
                        @include('storefront::public.partials.faqs', ['faqs'=> $blogPost->faqs])
                @endif
                @include('storefront::public.partials.ai-sharing', ['type' => 'post'])
            </div>
        </div>
    </div>
</main>

@endsection


