<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#contactAddressTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="contactAddressTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_contact_address][' . $locale . ']', trans('storefront::attributes.storefront_contact_address'), $errors, $settings) }}
                </div>
            @endforeach
        </div>

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#openTimeContactTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="openTimeContactTabs{{ $locale }}">
                    {{ Form::wysiwyg('translatable[storefront_contact_open_time][' . $locale . ']', trans('storefront::attributes.storefront_contact_open_time'), $errors, $settings) }}
                </div>
            @endforeach
        </div>

        {{ Form::text('storefront_map_api_key', trans('storefront::attributes.storefront_map_api_key'), $errors, $settings) }}
        {{ Form::text('storefront_longitude', trans('storefront::attributes.storefront_longitude'), $errors, $settings) }}
        {{ Form::text('storefront_latitude', trans('storefront::attributes.storefront_latitude'), $errors, $settings) }}
        {{ Form::text('storefront_gm_link', trans('storefront::attributes.storefront_gm_link'), $errors, $settings) }}
        {{ Form::textarea('storefront_embed_map_code', trans('storefront::attributes.storefront_embed_map_code'), $errors, $settings) }}

        <div class="media-picker-divider"></div>

        @include('media::admin.image_picker.single', [
            'title' => trans('storefront::storefront.form.contact_bg'),
            'inputName' => 'storefront_contact_bg',
            'file' => $contactBg,
        ])
    </div>
</div>
