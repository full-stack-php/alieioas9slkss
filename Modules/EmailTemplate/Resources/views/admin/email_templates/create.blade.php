@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.create', ['resource' => trans('emailtemplate::email_templates.email_template')]))

    <li class="breadcrumb-item">
        <a href="{{ route('admin.email_templates.index') }}">
            {{ trans('emailtemplate::email_templates.email_templates') }}
        </a>
    </li>
    <li class="breadcrumb-item active">
        {{ trans('admin::resource.create', ['resource' => trans('emailtemplate::email_templates.email_template')]) }}
    </li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.email_templates.store') }}" class="form-horizontal" id="email-template-create-form" novalidate>
        {{ csrf_field() }}

        {!! $tabs->render(compact('emailTemplate', 'types', 'recipients', 'statusKeys', 'shortcodesByType')) !!}
    </form>
@endsection
