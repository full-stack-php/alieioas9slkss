<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#copyrightTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="copyrightTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_copyright_text][' . $locale . ']', trans('storefront::attributes.storefront_copyright_text'), $errors, $settings) }}
                </div>
            @endforeach
        </div>
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#footerOpenTimeTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="footerOpenTimeTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_footer_open_time][' . $locale . ']', trans('storefront::attributes.storefront_footer_open_time'), $errors, $settings) }}
                </div>
            @endforeach
        </div>

        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#footerAddressTabs{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="footerAddressTabs{{ $locale }}">
                    {{ Form::text('translatable[storefront_footer_address][' . $locale . ']', trans('storefront::attributes.storefront_footer_address'), $errors, $settings) }}
                </div>
            @endforeach
        </div>


        <div class="box-content clearfix">
        	@include('media::admin.image_picker.single', [
        	    'title' => trans('storefront::storefront.form.accepted_payment_methods_image'),
        	    'inputName' => 'storefront_accepted_payment_methods_image',
        	    'file' => $acceptedPaymentMethodsImage,
        	])
        </div>
    </div>
</div>
