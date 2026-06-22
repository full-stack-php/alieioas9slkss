@php
    // Массив с ключами (для названий полей в БД/переводов) и читаемыми названиями
    $aiProviders = [
        'chatgpt'   => 'ChatGPT',
        'perplexity'=> 'Perplexity',
        'grok'      => 'Grok',
        'google_ai' => 'Google AI',
    ];
@endphp

@foreach($aiProviders as $providerKey => $providerName)
    <div class="col-md-12 {{ $loop->first ? '' : 'mt-4' }}">
        <h5 class="mb-3">{{ $providerName }} Settings</h5>
        <ul class="nav nav-pills">
            @foreach (supported_locales() as $locale => $language)
                <li class="nav-item">
                    <a href="#ai-{{ $providerKey }}-tabs-{{ $locale }}" data-bs-toggle="tab" aria-expanded="true" class="nav-link {{ $locale === locale() ? 'active' : '' }}">
                        <span class="d-none d-sm-block">{{ $language['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content pt-2 text-muted">
            @foreach (supported_locales() as $locale => $language)
                <div class="tab-pane {{ $locale === locale() ? 'show active' : '' }}" id="ai-{{ $providerKey }}-tabs-{{ $locale }}">
                    {{ Form::text('translatable[storefront_' . $providerKey . '_home_page][' . $locale . ']', trans("storefront::attributes.{$providerKey}_home_page"), $errors, $settings) }}

                    {{ Form::text('translatable[storefront_' . $providerKey . '_product_listing][' . $locale . ']', trans("storefront::attributes.{$providerKey}_product_listing"), $errors, $settings) }}

                    {{ Form::text('translatable[storefront_' . $providerKey . '_product][' . $locale . ']', trans("storefront::attributes.{$providerKey}_product"), $errors, $settings) }}

                    {{ Form::text('translatable[storefront_' . $providerKey . '_post][' . $locale . ']', trans("storefront::attributes.{$providerKey}_post"), $errors, $settings) }}
                </div>
            @endforeach
        </div>

        {{ Form::checkbox('storefront_' . $providerKey . '_btn_enabled', trans("storefront::attributes.status_btn"), trans("storefront::storefront.form.{$providerKey}_btn"), $errors, $settings) }}
    </div>
@endforeach
