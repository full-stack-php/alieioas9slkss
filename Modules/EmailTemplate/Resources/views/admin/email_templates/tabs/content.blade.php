<div class="alert alert-info">
    <strong>{{ trans('emailtemplate::email_templates.form.available_shortcodes') }}:</strong>
    <div class="mt-2">
        @foreach($shortcodes as $shortcode)
            <code class="me-2">{{ $shortcode }}</code>
        @endforeach
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


