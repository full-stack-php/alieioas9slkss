@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('page::pages.page')]))
    @slot('subtitle', $page->title)

    <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">{{ trans('page::pages.pages') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.edit', ['resource' => trans('page::pages.page')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="form-horizontal" id="page-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('page')) !!}
    </form>
@endsection

@push('globals')
    @vite([
        'Modules/Page/Resources/assets/admin/sass/main.scss',
        'Modules/Page/Resources/assets/admin/js/main.js',
    ])
@endpush
