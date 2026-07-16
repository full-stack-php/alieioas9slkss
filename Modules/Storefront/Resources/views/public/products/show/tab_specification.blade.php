@if ($product->hasAnyAttribute())
    <div class="tab-pane" id="tab-specification">
        @php
            $i = 0;
        @endphp
        @foreach ($product->attributeSets as $attributeSet => $attributes)
        <h2 class="ch-h2">{{ $attributeSet }}&nbsp;{{ $product->h1_name?? $product->name }}</h2>
        <div class="short-attributes-groups">
            @if($i == 0 && !empty($manufacturerData))
                <div class="short-attribute">
                    <span class="attr-name">
                        <span>
                            {{ trans('storefront::product.manufacturer') }}
                        </span>
                    </span>

                    <span class="attr-text" lang="{{ locale() }}">
                        <a href="{{ $manufacturerData['url'] }}">
                            {{ $manufacturerData['name'] }}
                        </a>
                    </span>
                </div>
            @endif
            @foreach ($attributes as $attribute)
                <div class="short-attribute">
                    <span class="attr-name"><span>{{ $attribute->name }}</span></span>
                    <span class="attr-text" lang="{{ locale() }}">{{ $attribute->values->implode('value', ', ') }}</span></span>
                </div>
            @endforeach
        </div>
        @endforeach
    </div>
@endif
