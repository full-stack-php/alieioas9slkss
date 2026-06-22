<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#googleReviewsTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="googleReviewsTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_google_reviews_section_title][' . $locale . ']', trans('storefront::attributes.section_title'), $errors, $settings) }}
                </div>
            @endforeach
        </div>

        {{ Form::text('storefront_google_reviews_code', trans('storefront::attributes.storefront_google_reviews_code'), $errors, $settings) }}

        {{ Form::checkbox('storefront_google_reviews_section_enabled', trans('storefront::attributes.section_status'), trans('storefront::storefront.form.enable_google_reviews_section'), $errors, $settings) }}


    </div>
</div>
