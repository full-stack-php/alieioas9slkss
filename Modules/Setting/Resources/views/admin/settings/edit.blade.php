@extends('admin::layout')

@section('title', trans('setting::settings.settings'))

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h3>{{ trans('setting::settings.settings') }}</h3>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <ol class="breadcrumb ml-auto">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}" class="button-sm-hover-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24"><path fill="currentColor" d="M2 12.204c0-2.289 0-3.433.52-4.381c.518-.949 1.467-1.537 3.364-2.715l2-1.241C9.889 2.622 10.892 2 12 2s2.11.622 4.116 1.867l2 1.241c1.897 1.178 2.846 1.766 3.365 2.715S22 9.915 22 12.203v1.522c0 3.9 0 5.851-1.172 7.063S17.771 22 14 22h-4c-3.771 0-5.657 0-6.828-1.212S2 17.626 2 13.725z" opacity="0.5"/><path fill="currentColor" d="M11.25 18a.75.75 0 0 0 1.5 0v-3a.75.75 0 0 0-1.5 0z"/></svg>
                    </a>
                </li>

                <li class="breadcrumb-item active">{{ trans('setting::settings.settings') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger clearfix">
            <div class="d-flex align-items-center">
                <div class="avatar-sm rounded bg-danger d-flex justify-content-center align-items-center fs-18 me-2 flex-shrink-0">
                    <i class="bx bx-info-circle text-white"></i>
                </div>

                <div class="flex-grow-1">
                    {{ trans('core::messages.the_given_data_was_invalid') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="form-horizontal" id="settings-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('settings')) !!}
    </form>
@endsection

@push('globals')
    @vite([
        'Modules/Setting/Resources/assets/admin/js/main.js',
        'Modules/Media/Resources/assets/admin/sass/main.scss',
        'Modules/Media/Resources/assets/admin/js/main.js',
    ])
@endpush
