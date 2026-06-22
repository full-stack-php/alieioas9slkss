@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('blog::admin.blog_post.name')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.blog_posts.index') }}">{{ trans('blog::admin.blog_posts.name') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('blog::admin.blog_post.name')]) }}</li>
@endcomponent

@section('content')

    <form method="POST" action="{{ route('admin.blog_posts.store') }}" class="form-horizontal" id="post-create-form" novalidate>
        {{ csrf_field() }}
        {!! $tabs->render(compact('blogPost')) !!}
    </form>
@endsection
@push('scripts')
<script>
    window.Korf.supportedLocales = @json(array_keys(supported_locales()));
</script>
@endpush

@push('globals')
    @vite([
        'Modules/Blog/Resources/assets/admin/posts/sass/main.scss',
        'Modules/Blog/Resources/assets/admin/posts/js/main.js',
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js'
    ])
@endpush
