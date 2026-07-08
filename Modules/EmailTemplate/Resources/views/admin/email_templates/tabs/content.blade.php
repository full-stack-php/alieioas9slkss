<div class="alert alert-info">
    <strong>{{ trans('emailtemplate::email_templates.form.available_shortcodes') }}:</strong>

    <div
        class="mt-2"
        id="email-template-shortcodes"
        data-shortcodes-by-type='@json($shortcodesByType)'
        data-empty-text="{{ trans('emailtemplate::email_templates.form.no_shortcodes') }}"
    ></div>
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

@push('scripts')
    <script type="module">
        const typeSelect = document.querySelector('select[name="type"]');
        const shortcodesContainer = document.getElementById('email-template-shortcodes');

        if (typeSelect && shortcodesContainer) {
            const shortcodesByType = JSON.parse(shortcodesContainer.dataset.shortcodesByType || '{}');
            const emptyText = shortcodesContainer.dataset.emptyText || '';

            const renderShortcodes = () => {
                const selectedType = typeSelect.value;
                const shortcodes = shortcodesByType[selectedType] || [];

                shortcodesContainer.innerHTML = '';

                if (!shortcodes.length) {
                    shortcodesContainer.textContent = emptyText;

                    return;
                }

                shortcodes.forEach((shortcode) => {
                    const code = document.createElement('code');

                    code.classList.add('me-2');
                    code.textContent = shortcode;

                    shortcodesContainer.appendChild(code);
                });
            };

            typeSelect.addEventListener('change', renderShortcodes);

            renderShortcodes();
        }
    </script>
@endpush

