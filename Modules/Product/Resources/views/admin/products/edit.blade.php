@extends('admin::layout')

@php
    $filters = request()->only([
        'page_filter_category_id',
        'page_filter_brand_id',
        'page_filter_is_active',
    ]);
@endphp

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('product::products.product')]))
    @slot('subtitle', $product->name)

    <li class="breadcrumb-item">
        <a href="{{ route('admin.products.index', $filters) }}">
            {{ trans('product::products.products') }}
        </a>
    </li>

    <li class="breadcrumb-item active">
        {{ trans('admin::resource.edit', ['resource' => trans('product::products.product')]) }}
    </li>
@endcomponent

@section('content')
    <form
        method="POST"
        action="{{ route('admin.products.update', array_merge(['id' => $product->id], $filters)) }}"
        class="form-horizontal"
        id="product-edit-form"
        novalidate
    >
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('product')) !!}
    </form>
@endsection

@include('product::admin.products.partials.scripts')

@push('globals')
    @vite([
        'Modules/Product/Resources/assets/admin/js/main.js',
        'Modules/Attribute/Resources/assets/admin/sass/main.scss',
        'Modules/Option/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js',
        'node_modules/flatpickr/dist/flatpickr.min.css',
    ])
@endpush
