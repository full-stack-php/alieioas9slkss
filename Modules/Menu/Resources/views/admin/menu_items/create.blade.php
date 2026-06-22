@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('menu::menu_items.menu_item')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">{{ trans('menu::menus.menus') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.menus.edit', $menuId) }}">{{ trans('admin::resource.edit', ['resource' => trans('menu::menus.menu')]) }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.create', ['resource' => trans('menu::menu_items.menu_item')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.menus.items.store', $menuId) }}" class="form-horizontal" id="menu-item-create-form" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('menuId', 'menuItem')) !!}
    </form>
@endsection


@push('globals')
    @vite([
        'Modules/Menu/Resources/assets/admin/sass/main.scss',
        'Modules/Menu/Resources/assets/admin/js/main.js',
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js'
    ])
@endpush
