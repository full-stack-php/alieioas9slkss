@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', trans('admin::resource.edit', ['resource' => trans('review::reviews.review')]))

    <li class="breadcrumb-item"><a href="{{ route('admin.questions_answers.index') }}">{{ trans('questionanswer::questions_answers.questions_answers') }}</a></li>
    <li class="breadcrumb-item active">{{ trans('admin::resource.edit', ['resource' => trans('questionanswer::questions_answers.question')]) }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.questions_answers.update', $questionanswer) }}" class="form-horizontal" id="questionanswer-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        {!! $tabs->render(compact('questionanswer')) !!}
    </form>
@endsection


@push('scripts')
    <script type="module">
        keypressAction([
            { key: 'b', route: "{{ route('admin.reviews.index') }}" },
        ]);
    </script>
@endpush
