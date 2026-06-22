@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('support::export.export')]))
    @slot('subtitle', $export->name)

    <li class="breadcrumb-item"><a href="{{ route('admin.exports.index') }}">{{ trans('support::export.exports') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.edit', ['resource' => trans('support::export.export')]) }}</li>
@endcomponent

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.exports.update', $export) }}" class="form-horizontal" id="export-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('export')) !!}
    </form>
@endsection

@push('globals')
    @vite([
         'Modules/Support/Resources/assets/admin/js/export.js',
    ])
@endpush
