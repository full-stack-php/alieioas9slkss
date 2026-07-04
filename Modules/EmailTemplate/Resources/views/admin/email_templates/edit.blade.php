@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('emailtemplate::email_templates.email_template')]))
    @slot('subtitle', $emailTemplate->name)

    <li class="breadcrumb-item">
        <a href="{{ route('admin.email_templates.index') }}">
            {{ trans('emailtemplate::email_templates.email_templates') }}
        </a>
    </li>
    <li class="breadcrumb-item active">
        {{ trans('admin::resource.edit', ['resource' => trans('emailtemplate::email_templates.email_template')]) }}
    </li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.email_templates.update', $emailTemplate) }}" class="form-horizontal" id="email-template-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('emailTemplate', 'types', 'recipients', 'statusKeys', 'shortcodes')) !!}
    </form>
@endsection

@push('globals')
    @vite([
        'Modules/EmailTemplate/Resources/assets/admin/js/main.js',
    ])
@endpush
