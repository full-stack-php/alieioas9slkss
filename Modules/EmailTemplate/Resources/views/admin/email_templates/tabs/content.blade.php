<div class="alert alert-info">
    <strong>{{ trans('emailtemplate::email_templates.form.available_shortcodes') }}:</strong>

    <div
        class="mt-2"
        id="email-template-shortcodes"
        data-shortcodes-by-type='@json($shortcodesByType)'
        data-empty-text="{{ trans('emailtemplate::email_templates.form.no_shortcodes') }}"
    ></div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-3">
            {{ trans('emailtemplate::email_templates.form.test_email') }}
        </h6>

        <div class="row align-items-end">
            <div class="col-md-8">
                <label for="email-template-test-email" class="form-label">
                    {{ trans('emailtemplate::email_templates.form.test_email_address') }}
                </label>

                <input
                    type="email"
                    name="test_email"
                    id="email-template-test-email"
                    class="form-control"
                    placeholder="{{ trans('emailtemplate::email_templates.form.test_email_placeholder') }}"
                    autocomplete="off"
                >

                <small class="form-text text-muted">
                    {{ trans('emailtemplate::email_templates.form.test_email_help') }}
                </small>
            </div>

            <div class="col-md-4">
                <button
                    type="button"
                    class="btn btn-primary w-100"
                    id="email-template-send-test"
                    data-url="{{ route('admin.email_templates.test') }}"
                    data-default-text="{{ trans('emailtemplate::email_templates.form.send_test_email') }}"
                    data-sending-text="{{ trans('emailtemplate::email_templates.form.sending_test_email') }}"
                    data-error-text="{{ trans('emailtemplate::email_templates.form.test_email_failed') }}"
                >
                    {{ trans('emailtemplate::email_templates.form.send_test_email') }}
                </button>
            </div>
        </div>

        <div id="email-template-test-message" class="mt-2"></div>
    </div>
</div>

<ul class="nav nav-pills">
    @foreach (supported_locales() as $locale => $language)
        <li class="nav-item">
            <a href="#emailTemplateContentTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                <span class="d-none d-sm-block">{{ $language['name'] }}</span>
            </a>
        </li>
    @endforeach
</ul>

<div class="tab-content pt-2 text-muted">
    @foreach (supported_locales() as $locale => $language)
        <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="emailTemplateContentTabs{{ $locale }}">
            {{ Form::text(
                "{$locale}[name]",
                trans('emailtemplate::attributes.name'),
                $errors,
                $emailTemplate,
                ['required' => true]
            ) }}

            {{ Form::text(
                "{$locale}[subject]",
                trans('emailtemplate::attributes.subject'),
                $errors,
                $emailTemplate,
                ['required' => true]
            ) }}

            {{ Form::wysiwyg(
                "{$locale}[content]",
                trans('emailtemplate::attributes.content'),
                $errors,
                $emailTemplate,
                ['required' => true]
            ) }}
        </div>
    @endforeach
</div>

@push('globals')
    @vite([
        'Modules/EmailTemplate/Resources/assets/admin/js/mailEditor.js',
    ])
@endpush

