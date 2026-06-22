@extends('storefront::public.layout')

@section('title',  $blogCategory->meta->meta_title ?: $blogCategory->name)

@push('meta')
    <meta name="title" content="{{ $blogCategory->meta->meta_title ?: $blogCategory->name }}">
    <meta name="description" content="{{ $blogCategory->meta->meta_description }}">
    <meta name="twitter:card" content="summary">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $blogCategory->meta->meta_title ?: $blogCategory->name }}">
    <meta property="og:description" content="{{ $blogCategory->meta->meta_description }}">
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
                    <h1>{{ $blogCategory->h1_name ?? $blogCategory->name }}</h1>
                </div>
            </div>

            @if(count($blogCategories) > 0)
            <div class="row mb-3">
                <div class="col-xs-12 col-sm-12 blog-categories-list">
                @foreach($blogCategories as $child)
                    @if($child->blog_posts_count > 0 && $child->id != $blogCategory->id)
                    <div class="blog-categories-item d-flex align-items-center justify-content-between">
                        <a href="{{ $child->getFullPath() }}" class="{{ request()->routeIs('blog_category.blog_posts.index') && request()->route()->parameter('category') == $child->id ? 'active' : '' }}">
                            {{ $child->name }}
                        </a>

                        <span class="count">{{ $child->blog_posts_count }}</span>
                    </div>
                    @endif
                @endforeach
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-12 col-12">
                    <div class="articles__list row-flex">
                        @forelse ($blogPosts as $blogPost)
                            @include('storefront::public.partials.blog_post_card', $blogPost)
                        @empty
                            <div class="empty-message">
                                @include('storefront::public.products.index.empty_results_logo')
                                <h2>{{ trans('storefront::blog.blog_posts.no_blog_post_found') }}</h2>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            {{ $blogPosts->links() }}

            @if(strlen($blogCategory->description) > 7)
            <div class="row mt-5">
                <div class="col-12 col-12">
                    <div class="p-content">
                        {!! $blogCategory->description !!}
                    </div>
                </div>
            </div>
            @endif

        </div>
    </main>
@endsection

@push('globals')

@endPush
