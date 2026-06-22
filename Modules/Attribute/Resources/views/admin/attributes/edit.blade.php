@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('attribute::admin.attribute')]))
    @slot('subtitle', $attribute->name)

    <li class="breadcrumb-item"><a href="{{ route('admin.attributes.index') }}">{{ trans('attribute::admin.attributes') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.edit', ['resource' => trans('attribute::admin.attribute')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.attributes.update', $attribute) }}" class="form-horizontal" id="attribute-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('attribute')) !!}
    </form>
@endsection


@push('globals')
    @vite([
        'Modules/Attribute/Resources/assets/admin/sass/main.scss',
        'Modules/Attribute/Resources/assets/admin/js/main.js',
    ])
@endpush
