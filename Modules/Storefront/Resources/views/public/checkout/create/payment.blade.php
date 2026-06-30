<div class="checkout-payment-method h-100">
    <div class="checkout-heading">{{ trans('storefront::checkout.payment_method') }}</div>

    <div class="payment-method" id="payment-method-container">

        @if(empty($gateways) || count($gateways) === 0)
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-circle"></i>
                {{ trans('storefront::checkout.no_payment_method') }}
            </div>
        @else
            @foreach ($gateways as $name => $gateway)
                @php
                    $isChecked = (old('payment_method') == $name) || (empty(old('payment_method')) && $loop->first);
                @endphp

                <div class="radio chm-radio payment-method-item">
                    <label for="pm_{{ $name }}">
                        <input
                            id="pm_{{ $name }}"
                            type="radio"
                            name="payment_method"
                            value="{{ $name }}"
                            class="payment-method-input"
                            {{ $isChecked ? 'checked="checked"' : '' }}
                        />
                        <span class="checkbox-radio"></span>
                        {{ $gateway->label }}
                    </label>

                    {{-- Описание платежного метода (показываем только для выбранного) --}}
                    @if (!empty($gateway->description) || !empty($gateway->instructions))
                        <div class="text-info-pm payment-description" id="desc_{{ $name }}" style="display: {{ $isChecked ? 'block' : 'none' }};">
                            @if(!empty($gateway->description))
                                <p>{{ $gateway->description }}</p>
                            @endif

                            {{-- Инструкции (например, реквизиты для банковского перевода), которые раньше выводились через x-html --}}
                            @if(!empty($gateway->instructions))
                                <div class="payment-instructions-content">
                                    {!! $gateway->instructions !!}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        @endif

    </div>
</div>

@if (setting('stripe_enabled') && setting('stripe_integration_type') === 'embedded_form')
    @php
        $isStripeSelected = (old('payment_method') === 'stripe') || (empty(old('payment_method')) && isset($gateways['stripe']) && array_key_first($gateways->toArray()) === 'stripe');
    @endphp

    <div id="stripe-element" style="display: {{ $isStripeSelected ? 'block' : 'none' }}; margin-top: 15px; padding: 15px; border: 1px solid #e5e5e5; border-radius: 4px;">

    </div>
@endif
