@extends('admin::layout')

@section('title', trans('storefront::storefront.storefront'))

@component('admin::components.page.header')
    @slot('title', trans('storefront::storefront.storefront'))

    <li class="breadcrumb-item active">{{ trans('storefront::storefront.storefront') }}</li>
@endcomponent


@section('content')
    <form method="POST" action="{{ route('admin.storefront.settings.update') }}" class="form-horizontal" id="storefront-settings-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('settings')) !!}
    </form>
@endsection

@push('globals')
    @vite([
        'Modules/Storefront/Resources/assets/admin/sass/main.scss',
        'Modules/Storefront/Resources/assets/admin/js/main.js',
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js'
    ])
@endpush
