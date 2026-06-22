@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('contact::contact_submissions.contact_submission') . ' #' . $contactSubmission->id)

    <li class="breadcrumb-item">
        <a href="{{ route('admin.contact_submissions.index') }}">
            {{ trans('contact::contact_submissions.contact_submissions') }}
        </a>
    </li>

    <li class="breadcrumb-item active">
        {{ trans('contact::contact_submissions.contact_submission') }} #{{ $contactSubmission->id }}
    </li>
@endcomponent

@section('content')
    <div class="card card-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-4">
                    {{ trans('contact::contact_submissions.show.information') }}
                </h4>

                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th style="width: 250px;">{{ trans('contact::contact_submissions.fields.id') }}</th>
                        <td>{{ $contactSubmission->id }}</td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.type') }}</th>
                        <td>{{ $contactSubmission->type_label }}</td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.name') }}</th>
                        <td>{{ $contactSubmission->name ?: trans('contact::contact_submissions.empty') }}</td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.phone') }}</th>
                        <td>
                            @if($contactSubmission->phone)
                                <a href="tel:{{ preg_replace('/[^0-9\+]/', '', $contactSubmission->phone) }}">
                                    {{ $contactSubmission->phone }}
                                </a>
                            @else
                                {{ trans('contact::contact_submissions.empty') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.email') }}</th>
                        <td>
                            @if($contactSubmission->email)
                                <a href="mailto:{{ $contactSubmission->email }}">
                                    {{ $contactSubmission->email }}
                                </a>
                            @else
                                {{ trans('contact::contact_submissions.empty') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.topic') }}</th>
                        <td>
                            {{ $contactSubmission->topic ?: $contactSubmission->subject ?: trans('contact::contact_submissions.empty') }}
                        </td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.comment') }}</th>
                        <td>{!! nl2br(e($contactSubmission->message ?: trans('contact::contact_submissions.empty'))) !!}</td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.preferred_call_at') }}</th>
                        <td>
                            {{ optional($contactSubmission->preferred_call_at)->format('d.m.Y H:i') ?: trans('contact::contact_submissions.empty') }}
                        </td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.source_url') }}</th>
                        <td>
                            @if($contactSubmission->source_url)
                                <a href="{{ $contactSubmission->source_url }}" target="_blank" rel="noopener noreferrer">
                                    {{ $contactSubmission->source_url }}
                                </a>
                            @else
                                {{ trans('contact::contact_submissions.empty') }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.ip_address') }}</th>
                        <td>{{ $contactSubmission->ip_address ?: trans('contact::contact_submissions.empty') }}</td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.user_agent') }}</th>
                        <td style="word-break: break-word;">
                            {{ $contactSubmission->user_agent ?: trans('contact::contact_submissions.empty') }}
                        </td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.created_at') }}</th>
                        <td>{{ optional($contactSubmission->created_at)->format('d.m.Y H:i:s') }}</td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.read_at') }}</th>
                        <td>
                            {{ optional($contactSubmission->read_at)->format('d.m.Y H:i:s') ?: trans('contact::contact_submissions.empty') }}
                        </td>
                    </tr>

                    <tr>
                        <th>{{ trans('contact::contact_submissions.fields.processed_at') }}</th>
                        <td>
                            @if($contactSubmission->processed_at)
                                {{ $contactSubmission->processed_at->format('d.m.Y H:i:s') }}

                                @if($contactSubmission->processor)
                                    <br>
                                    <small>
                                        {{ trans('contact::contact_submissions.fields.processed_by') }}:
                                        {{ $contactSubmission->processor->full_name ?? $contactSubmission->processor->email }}
                                    </small>
                                @endif
                            @else
                                {{ trans('contact::contact_submissions.empty') }}
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>

                @php
                    $filters = request()->only(['type', 'read', 'processed']);
                @endphp

                <div class="d-flex gap-2 mt-4">
                    @if(!$contactSubmission->is_processed)
                        <form method="POST" action="{{ route('admin.contact_submissions.processed', $contactSubmission->id) }}">
                            @csrf
                            @method('PUT')

                            @foreach($filters as $name => $value)
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endforeach

                            <button type="submit" class="btn btn-primary">
                                {{ trans('contact::contact_submissions.show.mark_as_processed') }}
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.contact_submissions.unprocessed', $contactSubmission->id) }}">
                            @csrf
                            @method('PUT')

                            @foreach($filters as $name => $value)
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endforeach

                            <button type="submit" class="btn btn-warning">
                                {{ trans('contact::contact_submissions.show.mark_as_unprocessed') }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.contact_submissions.index', $filters) }}" class="btn btn-secondary">
                        {{ trans('contact::contact_submissions.show.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
