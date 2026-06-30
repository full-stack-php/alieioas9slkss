@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('product::products.one_c_mappings.single')]))
    @slot('subtitle', $mapping->one_c_id)

    <li class="breadcrumb-item">
        <a href="{{ route('admin.product_one_c_mappings.index') }}">
            {{ trans('product::products.one_c_mappings.title') }}
        </a>
    </li>

    <li class="breadcrumb-item active">
        {{ trans('admin::resource.edit', ['resource' => trans('product::products.one_c_mappings.single')]) }}
    </li>
@endcomponent

@section('content')
    <form method="POST"
          action="{{ route('admin.product_one_c_mappings.update', $mapping->id) }}"
          class="form-horizontal"
          id="product-one-c-mapping-edit-form"
          novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        @include('product::admin.product_one_c_mappings.partials.form')
    </form>
@endsection
