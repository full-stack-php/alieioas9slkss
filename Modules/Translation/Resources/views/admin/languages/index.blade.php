@extends('admin::layout')

@section('title', trans('translation::languages.languages'))

@component('admin::components.page.header')
    @slot('title', trans('translation::languages.languages'))

    <li class="breadcrumb-item active">{{ trans('translation::languages.languages') }}</li>
@endcomponent

@section('content')
    <div class="card">
        <div class="d-flex card-header justify-content-between align-items-center">
            <div class="row">
                <div class="btn-group pull-right">
                    <a href="{{ route("admin.languages.add") }}" class="btn btn-primary btn-actions btn-create">
                        {{ trans("translation::languages.add_language") }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
                @php
                    $defaultLanguageKey = collect($languages)->firstWhere('is_default')['key'] ?? 'en';
                @endphp

                <div class="table-responsive mb-0">
                    <table class="table languages-table" id="languages-table">
                        <thead>
                        <tr>
                            <th>{{ __('translation::languages.table.name') }}</th>
                            <th>{{ __('translation::languages.table.default') }}</th>
                            <th>{{ __('translation::languages.table.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($languages as $locale)
                            <tr data-locale-key="{{ $locale['key'] }}">
                                <td>
                                    {{-- Ссылка на название теперь ведет на страницу переводов --}}
                                    <a href="{{ route('admin.language.translations.index', ['locale' => $locale['key']]) }}">
                                        {{ $locale['name'] }}
                                    </a>
                                </td>
                                <td>
                                    <div class="switch">
                                        <input
                                            type="radio"
                                            name="is_default"
                                            id="is_default_{{ $locale['key'] }}"
                                            value="{{ $locale['key'] }}"
                                            {{ $locale['is_default'] ? 'checked' : '' }}
                                            class="form-check-input make-default-radio" {{-- Добавлен класс для JS --}}
                                            data-url="{{ route('admin.languages.make_default') }}"
                                        />
                                        <label class="form-check-label" for="is_default_{{ $locale['key'] }}"></label>
                                    </div>
                                </td>
                                <td>
                                    {{-- Ссылка на планету (переводы) --}}
                                    <a
                                        href="{{ route('admin.language.translations.index', ['locale' => $locale['key']]) }}"
                                        class="btn btn-primary"
                                        title="{{ __('translation::languages.table.translations') }}"
                                    >
                                        <i class='bx bx-planet'></i>
                                    </a>

                                    <button
                                        class="btn {{ count($languages) <= 1 || $locale['is_default'] ? 'btn-secondary' : 'btn-primary' }} delete-language-btn"
                                        data-key="{{ $locale['key'] }}"
                                        data-url="{{ route('admin.languages.destroy', ['locale' => $locale['key']]) }}"
                                        {{ count($languages) <= 1 || $locale['is_default'] ? 'disabled' : '' }}
                                        title="{{ __('translation::languages.table.delete') }}"
                                    >
                                        <i class='bx bx-trash-alt'></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <form id="delete-form" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>

                <input type="hidden" id="default-language-key" value="{{ $defaultLanguageKey }}">

                <script>
                    window.addEventListener('load', function () {
                        $(document).ready(function () {
                            let defaultLanguageKey = $('#default-language-key').val();
                            const csrfToken = $('meta[name="csrf-token"]').attr('content');
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            $('#languages-table').on('change', '.make-default-radio', function () {
                                const $radio = $(this);
                                const newDefaultKey = $radio.val();
                                const url = $radio.data('url');

                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: { language: newDefaultKey },
                                    success: function (response) {
                                        defaultLanguageKey = newDefaultKey;
                                        $('#default-language-key').val(newDefaultKey);

                                        if (window.toaster) {
                                            toaster("{{ __('translation::languages.default_language_updated') }}", { type: "success" });
                                        }

                                        $('.delete-language-btn').prop('disabled', false);
                                        $(`.delete-language-btn[data-key="${newDefaultKey}"]`).prop('disabled', true);
                                    },
                                    error: function (xhr) {
                                        $(`.make-default-radio[value="${defaultLanguageKey}"]`).prop('checked', true);

                                        const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Произошла ошибка.';
                                        if (window.toaster) {
                                            toaster(message, { type: "error" });
                                        }
                                    }
                                });
                            });

                            // Удаление языка
                            $('#languages-table').on('click', '.delete-language-btn', function () {
                                const $btn = $(this);
                                const keyToDelete = $btn.data('key');
                                const url = $btn.data('url');

                                if (keyToDelete === defaultLanguageKey) {
                                    return;
                                }

                                if (!confirm('{{ __('admin::admin.confirmation.delete') }}')) {
                                    return;
                                }

                                $.ajax({
                                    url: url,
                                    type: 'DELETE',
                                    success: function (response) {
                                        window.location.reload();
                                    },
                                    error: function (xhr) {
                                        const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Произошла ошибка при удалении.';
                                        if (window.toaster) {
                                            toaster(message, { type: "error" });
                                        }
                                    }
                                });
                            });
                        });
                    });
                </script>
            </div>

    </div>
@endsection

@push('globals')
    <script>
        Korf.data['languages'] = @json($languages);
        Korf.langs['translation::languages.table.name'] = '{{ trans('translation::languages.table.name') }}';
        Korf.langs['translation::languages.table.default'] = '{{ trans('translation::languages.table.default') }}';
        Korf.langs['translation::languages.table.actions'] = '{{ trans('translation::languages.table.actions') }}';
        Korf.langs['translation::languages.table.translations'] = '{{ trans('translation::languages.table.translations') }}';
        Korf.langs['translation::languages.table.delete'] = '{{ trans('translation::languages.table.delete') }}';
        Korf.langs['translation::languages.default_language_updated'] = '{{ trans('translation::languages.default_language_updated') }}';
    </script>

    @vite([
        'Modules/Translation/Resources/assets/admin/languages/sass/main.scss',
    ])
@endpush
