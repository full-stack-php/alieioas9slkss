<div class="row">
    <div class="col-md-8">
        {{ Form::checkbox('cod_enabled', trans('setting::attributes.cod_enabled'), trans('setting::settings.form.enable_cod'), $errors, $settings) }}

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#cod_{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="cod_{{ $locale }}">
                    {{ Form::text('translatable[cod_label][' . $locale . ']', trans('setting::attributes.translatable.cod_label'), $errors, $settings, ['required' => true]) }}


                    {{ Form::textarea('translatable[cod_description][' . $locale . ']', trans('setting::attributes.translatable.cod_description'), $errors, $settings, ['rows' => 3, 'required' => false]) }}
                </div>
            @endforeach
        </div>

    </div>
</div>
