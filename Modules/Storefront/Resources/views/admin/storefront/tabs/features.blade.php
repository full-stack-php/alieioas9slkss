<div class="row">
    <div class="col-md-12">
        {{ Form::checkbox('storefront_features_section_enabled', trans('storefront::attributes.section_status'), trans('storefront::storefront.form.enable_features_section'), $errors, $settings) }}

        <div class="clearfix"></div>

        <div class="card border-primary border">
            <div class="row g-0">
                <div class="col-4 p-2">
                    @include('media::admin.image_picker.single', [
                        'title' => trans('storefront::attributes.icon'),
                        'inputName' => 'storefront_feature_1_icon',
                        'file' => $storefront_feature_1_icon,
                    ])
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ trans('storefront::storefront.form.feature_1') }}</h5>

                        <ul class="nav nav-pills">
                            @foreach (supported_locales() as $locale => $language)
                                <li class="nav-item">
                                    <a href="#feature1Tabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content pt-2 text-muted">
                            @foreach (supported_locales() as $locale => $language)
                                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="feature1Tabs{{ $locale }}">
                                    {{ Form::text('translatable[storefront_feature_1_title][' . $locale . ']', trans('storefront::attributes.title'), $errors, $settings) }}
                                    {{ Form::text('translatable[storefront_feature_1_subtitle][' . $locale . ']', trans('storefront::attributes.subtitle'), $errors, $settings) }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card border-primary border">
            <div class="row g-0">
                <div class="col-4 p-2">
                    @include('media::admin.image_picker.single', [
                        'title' => trans('storefront::attributes.icon'),
                        'inputName' => 'storefront_feature_2_icon',
                        'file' => $storefront_feature_2_icon,
                    ])
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ trans('storefront::storefront.form.feature_2') }}</h5>

                        <ul class="nav nav-pills">

                            @foreach (supported_locales() as $locale => $language)
                                <li class="nav-item">
                                    <a href="#feature2Tabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content pt-2 text-muted">
                            @foreach (supported_locales() as $locale => $language)
                                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="feature2Tabs{{ $locale }}">
                                    {{ Form::text('translatable[storefront_feature_2_title][' . $locale . ']', trans('storefront::attributes.title'), $errors, $settings) }}
                                    {{ Form::text('translatable[storefront_feature_2_subtitle][' . $locale . ']', trans('storefront::attributes.subtitle'), $errors, $settings) }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card border-primary border">
            <div class="row g-0">
                <div class="col-4 p-2">
                    @include('media::admin.image_picker.single', [
                        'title' => trans('storefront::attributes.icon'),
                        'inputName' => 'storefront_feature_3_icon',
                        'file' => $storefront_feature_3_icon,
                    ])
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ trans('storefront::storefront.form.feature_3') }}</h5>

                        <ul class="nav nav-pills">

                            @foreach (supported_locales() as $locale => $language)
                                <li class="nav-item">
                                    <a href="#feature3Tabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content pt-2 text-muted">
                            @foreach (supported_locales() as $locale => $language)
                                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="feature3Tabs{{ $locale }}">
                                    {{ Form::text('translatable[storefront_feature_3_title][' . $locale . ']', trans('storefront::attributes.title'), $errors, $settings) }}
                                    {{ Form::text('translatable[storefront_feature_3_subtitle][' . $locale . ']', trans('storefront::attributes.subtitle'), $errors, $settings) }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card border-primary border">
            <div class="row g-0">
                <div class="col-4 p-2">
                    @include('media::admin.image_picker.single', [
                        'title' => trans('storefront::attributes.icon'),
                        'inputName' => 'storefront_feature_4_icon',
                        'file' => $storefront_feature_4_icon,
                    ])
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ trans('storefront::storefront.form.feature_4') }}</h5>

                        <ul class="nav nav-pills">

                            @foreach (supported_locales() as $locale => $language)
                                <li class="nav-item">
                                    <a href="#feature4Tabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content pt-2 text-muted">
                            @foreach (supported_locales() as $locale => $language)
                                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="feature4Tabs{{ $locale }}">
                                    {{ Form::text('translatable[storefront_feature_4_title][' . $locale . ']', trans('storefront::attributes.title'), $errors, $settings) }}
                                    {{ Form::text('translatable[storefront_feature_4_subtitle][' . $locale . ']', trans('storefront::attributes.subtitle'), $errors, $settings) }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card border-primary border">
            <div class="row g-0">
                <div class="col-4 p-2">
                    @include('media::admin.image_picker.single', [
                        'title' => trans('storefront::attributes.icon'),
                        'inputName' => 'storefront_feature_5_icon',
                        'file' => $storefront_feature_5_icon,
                    ])
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ trans('storefront::storefront.form.feature_5') }}</h5>

                        <ul class="nav nav-pills">

                            @foreach (supported_locales() as $locale => $language)
                                <li class="nav-item">
                                    <a href="#feature5Tabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content pt-2 text-muted">
                            @foreach (supported_locales() as $locale => $language)
                                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="feature5Tabs{{ $locale }}">
                                    {{ Form::text('translatable[storefront_feature_5_title][' . $locale . ']', trans('storefront::attributes.title'), $errors, $settings) }}
                                    {{ Form::text('translatable[storefront_feature_5_subtitle][' . $locale . ']', trans('storefront::attributes.subtitle'), $errors, $settings) }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card border-primary border">
            <div class="row g-0">
                <div class="col-4 p-2">
                    @include('media::admin.image_picker.single', [
                        'title' => trans('storefront::attributes.icon'),
                        'inputName' => 'storefront_feature_6_icon',
                        'file' => $storefront_feature_6_icon,
                    ])
                </div>
                <div class="col-8">
                    <div class="card-body">
                        <h5 class="card-title mb-2">{{ trans('storefront::storefront.form.feature_6') }}</h5>

                        <ul class="nav nav-pills">

                            @foreach (supported_locales() as $locale => $language)
                                <li class="nav-item">
                                    <a href="#feature6Tabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content pt-2 text-muted">
                            @foreach (supported_locales() as $locale => $language)
                                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="feature6Tabs{{ $locale }}">
                                    {{ Form::text('translatable[storefront_feature_6_title][' . $locale . ']', trans('storefront::attributes.title'), $errors, $settings) }}
                                    {{ Form::text('translatable[storefront_feature_6_subtitle][' . $locale . ']', trans('storefront::attributes.subtitle'), $errors, $settings) }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
