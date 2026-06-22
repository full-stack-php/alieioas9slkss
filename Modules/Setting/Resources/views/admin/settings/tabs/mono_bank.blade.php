<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox(
            'monobank_enabled',
            trans('setting::attributes.monobank_enabled'),
            trans('setting::settings.form.enable_monobank'),
            $errors,
            $settings
        ) }}

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a
                        href="#monobank_{{ $locale }}"
                        data-bs-toggle="tab"
                        aria-expanded="true"
                        class="nav-link {{ $locale === locale() ? 'active' : '' }}"
                    >
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div
                    class="tab-pane {{ $locale === locale() ? 'show active' : '' }}"
                    id="monobank_{{ $locale }}"
                >
                    {{ Form::text(
                        'translatable[monobank_label][' . $locale . ']',
                        trans('setting::attributes.translatable.monobank_label'),
                        $errors,
                        $settings,
                        ['required' => true]
                    ) }}

                    {{ Form::textarea(
                        'translatable[monobank_description][' . $locale . ']',
                        trans('setting::attributes.translatable.monobank_description'),
                        $errors,
                        $settings
                    ) }}
                </div>
            @endforeach
        </div>

        {{ Form::checkbox(
            'monobank_test_mode',
            trans('setting::attributes.monobank_test_mode'),
            trans('setting::settings.form.use_sandbox_for_test_payments'),
            $errors,
            $settings
        ) }}

        {{ Form::checkbox(
            'monobank_verify_signature',
            trans('setting::attributes.monobank_verify_signature'),
            trans('setting::settings.form.verify_payment_signature'),
            $errors,
            $settings
        ) }}

        <div
            class="{{ old('monobank_enabled', array_get($settings, 'monobank_enabled')) ? '' : 'hide' }}"
            id="monobank-fields"
        >
            {{ Form::password(
                'monobank_token',
                trans('setting::attributes.monobank_token'),
                $errors,
                $settings,
                ['required' => true]
            ) }}
        </div>
    </div>
</div>
