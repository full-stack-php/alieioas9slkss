@extends('admin::layout')

@component('admin::components.page.header')
    @slot(
        'title',
        trans('admin::resource.edit', [
            'resource' => trans('sticker::stickers.sticker')
        ])
    )

    @slot(
        'subtitle',
        $sticker->name ?: '#' . $sticker->id
    )

    <li class="breadcrumb-item">
        <a href="{{ route('admin.stickers.index') }}">
            {{ trans('sticker::stickers.stickers') }}
        </a>
    </li>

    <li class="breadcrumb-item active">
        {{ trans('admin::resource.edit', [
            'resource' => trans('sticker::stickers.sticker')
        ]) }}
    </li>
@endcomponent

@section('content')
    <form
        method="POST"
        action="{{ route('admin.stickers.update', $sticker) }}"
        class="form-horizontal"
        id="sticker-edit-form"
        novalidate
    >
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('sticker')) !!}
    </form>
@endsection

@push('globals')
    @vite([
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js',
    ])
@endpush
