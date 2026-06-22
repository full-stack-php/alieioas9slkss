@php
    $currentUrl = url()->current();

    $aiProviders = [
        'chatgpt' => [
            'name' => 'ChatGPT',
            'background' => '#12a37f',
            'url_prefix' => 'https://chatgpt.com/?q=',
        ],
        'perplexity' => [
            'name' => 'Perplexity',
            'background' => '#6f42c1',
            'url_prefix' => 'https://www.perplexity.ai/search/new?q=',
        ],
        'google_ai' => [
            'name' => 'AI Overviews',
            'background' => '#4286f5',
            'url_prefix' => 'https://www.google.com/search?udm=50&aep=11&q=',
        ],
        'grok' => [
            'name' => 'Grok',
            'background' => '#0a001f',
            'url_prefix' => 'https://grok.com/?q=',
        ],
    ];
@endphp

<div class="row">
    <div class="col-12">
        <div class="ai-sharing">
            @foreach($aiProviders as $key => $provider)
                @if(setting("storefront_{$key}_btn_enabled"))
                    @php
                        $promptText = setting("storefront_{$key}_{$type}");
                    @endphp

                    @if(!empty($promptText))
                        @php
                            $fullQuery = $promptText . ': ' . $currentUrl;

                            $encodedQuery = urlencode($fullQuery);
                        @endphp

                        <a href="{{ $provider['url_prefix'] }}{{ $encodedQuery }}" target="_blank" rel="noopener noreferrer" style="color:#fff;background:{{ $provider['background'] }}" class="btn">
                            {{ $provider['name'] }}
                        </a>
                    @endif
                @endif
            @endforeach
        </div>
    </div>
</div>
