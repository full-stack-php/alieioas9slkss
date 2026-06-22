@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('product::products.product')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">{{ trans('product::products.products') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('product::products.product')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.products.store') }}" class="form-horizontal" id="product-create-form" novalidate>
        {{ csrf_field() }}

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
