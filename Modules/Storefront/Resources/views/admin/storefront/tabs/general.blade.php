<div class="row">
    <div class="col-md-12">

        <ul class="nav nav-pills">

            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#notifyTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="notifyTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_notify_text][' . $locale . ']', trans('storefront::attributes.storefront_notify_text'), $errors, $settings) }}
                </div>
            @endforeach
        </div>



        @include('media::admin.image_picker.single', [
            'title' => trans('storefront::storefront.form.top_notify_bg'),
            'inputName' => 'storefront_top_notify_bg',
            'file' => $top_notify_bg,
        ])

        <div class="media-picker-divider"></div>


        @include('media::admin.image_picker.single', [
            'title' => trans('storefront::storefront.form.top_notify_mobile_bg'),
            'inputName' => 'storefront_top_notify_mobile_bg',
            'file' => $top_notify_mobile_bg,
        ])

        {{ Form::select('storefront_terms_page', trans('storefront::attributes.storefront_terms_page'), $errors, $pages, $settings) }}
        {{ Form::select('storefront_privacy_page', trans('storefront::attributes.storefront_privacy_page'), $errors, $pages, $settings) }}

        <ul class="nav nav-pills">

            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#addressTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="addressTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_address][' . $locale . ']', trans('storefront::attributes.storefront_address'), $errors, $settings) }}
                </div>
            @endforeach
        </div>

        <ul class="nav nav-pills">

            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#opentimeTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="opentimeTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_opentime][' . $locale . ']', trans('storefront::attributes.storefront_opentime'), $errors, $settings) }}
                </div>
            @endforeach
        </div>

        {{ Form::checkbox('storefront_show_callback_btn', trans('storefront::attributes.storefront_callback_btn'), trans('storefront::storefront.form.enable_callback_btn'), $errors, $settings) }}

        {{ Form::checkbox('storefront_show_repeat_btn', trans('storefront::attributes.storefront_show_repeat_btn'), trans('storefront::storefront.form.enable_show_repeat_btn'), $errors, $settings) }}

        {{ Form::checkbox('storefront_most_searched_keywords_enabled', trans('storefront::attributes.storefront_most_searched_keywords'), trans('storefront::storefront.form.enable_most_searched_keywords'), $errors, $settings) }}
    </div>
</div>
