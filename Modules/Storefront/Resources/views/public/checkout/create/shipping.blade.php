{{-- Обертка в стиле OpenCart --}}
<div class="checkout-shipping-method h-100" id="checkout-shipping-method-block">
    <div class="checkout-heading">{{ trans('storefront::checkout.shipping_method') }}</div>

    <div class="shipping-method" id="shipping-methods-container">

        @php
            $availableShippingMethods = Cart::availableShippingMethods();
            $currentShippingMethod = Cart::shippingMethod()->name();

            $freeShipping = app(\Modules\Shipping\Services\FreeShippingService::class);
            $shippingLabel = $freeShipping->shippingLabel();
        @endphp

        @if($availableShippingMethods && $availableShippingMethods->isNotEmpty())
            @foreach ($availableShippingMethods as $name => $shippingMethod)
                @continue($name === 'free_shipping')

                <div class="radio chm-radio shipping-method-item">
                    <label for="sm_{{ $name }}">
                        <input
                            type="radio"
                            name="shipping_method"
                            id="sm_{{ $name }}"
                            value="{{ $name }}"
                            class="shipping-method-input"
                            {{ (old('shipping_method', $currentShippingMethod) === $name) || $loop->first ? 'checked="checked"' : '' }}
                        />

                        <span class="checkbox-radio"></span>

                        <span class="shipping-method-name">
                            {{ $shippingMethod->label }}
                        </span>

                        <span class="shipping-method-cost {{ $freeShipping->isAvailable() ? 'text-success' : '' }}" style="margin-left: 5px; font-weight: bold;">
                            — {{ $shippingLabel }}
                        </span>
                    </label>
                </div>
            @endforeach
        @else
            <div class="alert alert-warning" id="no-shipping-warning">
                <i class="fa fa-exclamation-circle"></i>
                {{ trans('storefront::checkout.no_shipping_method') }}
            </div>
        @endif

    </div>
</div>
