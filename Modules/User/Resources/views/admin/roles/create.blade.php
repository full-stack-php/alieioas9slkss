@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('user::roles.role')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">{{ trans('user::roles.roles') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('user::roles.role')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.roles.store') }}" class="form-horizontal" id="role-create-form" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('role')) !!}
    </form>
@endsection

@push('globals')
    @vite([
        'Modules/User/Resources/assets/admin/sass/main.scss',
        'Modules/User/Resources/assets/admin/js/main.js'
    ])
@endpush
