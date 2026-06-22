<div class="box-language d-none d-md-block">
    @foreach (supported_locales() as $locale => $language)
    <button class="btn-language-top w-100 {{ $locale === locale() ? 'active' : '' }}" data-toggle="dropdown" onclick="location ='{{ localized_url($locale) }}'" >
        {{ $locale === 'uk' ? 'UA' : strtoupper($locale) }}
    </button>
    @endforeach
</div>
