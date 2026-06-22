@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('brand::brands.brand')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">{{ trans('brand::brands.brands') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('brand::brands.brand')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.brands.store') }}" class="form-horizontal" id="brand-create-form" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('brand')) !!}
    </form>
@endsection


@push('globals')
    @vite([
        'Modules/Brand/Resources/assets/admin/js/main.js',
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js',
    ])
@endpush
