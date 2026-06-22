<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('check_payment_enabled', trans('setting::attributes.check_payment_enabled'), trans('setting::settings.form.enable_check_payment'), $errors, $settings) }}

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#check_payment_{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="check_payment_{{ $locale }}">
                    {{ Form::text('translatable[check_payment_label][' . $locale . ']', trans('setting::attributes.translatable.check_payment_label'), $errors, $settings, ['required' => true]) }}
                    {{ Form::textarea('translatable[check_payment_description][' . $locale . ']', trans('setting::attributes.translatable.check_payment_description'), $errors, $settings, ['rows' => 3, 'required' => true]) }}
                    {{ Form::textarea('translatable[check_payment_instructions][' . $locale . ']', trans('setting::attributes.translatable.check_payment_instructions'), $errors, $settings, ['rows' => 3, 'required' => true]) }}
                </div>
            @endforeach
        </div>

    </div>
</div>
