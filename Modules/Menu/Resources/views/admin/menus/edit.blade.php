@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('menu::menus.menu')]))
    @slot('subtitle', $menu->title)

    <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">{{ trans('menu::menus.menus') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.edit', ['resource' => trans('menu::menus.menu')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.menus.update', $menu) }}" class="form-horizontal" id="menu-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        @include('menu::admin.menus.form.fields')
    </form>
@endsection

@push('globals')
    <script>
        Korf.langs['menu::messages.menu_item_deleted'] = '{{ trans('menu::messages.menu_item_deleted') }}';
        Korf.langs['menu::messages.menu_items_order_updated'] = '{{ trans('menu::messages.menu_items_order_updated') }}';
    </script>

    @vite([
        'Modules/Menu/Resources/assets/admin/sass/main.scss',
        'Modules/Menu/Resources/assets/admin/js/main.js'
    ])
@endpush
