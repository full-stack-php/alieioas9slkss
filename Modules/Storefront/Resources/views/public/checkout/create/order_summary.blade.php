@php
    $freeShipping = app(\Modules\Shipping\Services\FreeShippingService::class);
    $freeShippingSummary = $freeShipping->summary();

    $availableShippingMethods = Cart::availableShippingMethods();
    $hasShippingMethods = $availableShippingMethods && $availableShippingMethods->isNotEmpty();
@endphp

<div class="totals-inner" id="order-summary-block">
    <div class="title-text-next">
        {{ trans('storefront::checkout.order_summary') }}
    </div>

    @include('storefront::public.checkout.create.coupon')

    @if($freeShippingSummary['enabled'])
        <div
            id="free-shipping-left"
            class="free-shipping-left"
            data-free-shipping-enabled="1"
        >
            <div class="free-shipping-inner mb-3" >
                @if(! $freeShippingSummary['available'])
                    <div class="free-ship-progress-bar">
                        <div
                            class="free-ship-bar-fill"
                            style="width: {{ $freeShippingSummary['percentage'] }}%"
                        ></div>
                    </div>
                @endif

                    <div class="free-ship-info {{ $freeShippingSummary['available'] ? 'active-free-ship' : '' }}">
                        @if($freeShippingSummary['available'])
                            <div class="text-free-shipping">
                                {{ $freeShippingSummary['message_text'] }}
                            </div>
                        @else
                            <div class="text-free-shipping">
                                {{ $freeShippingSummary['message_text'] }}
                            </div>

                            <span class="sum-free-shipping-left">
                                {{ $freeShippingSummary['amount_left_formatted'] }}
                            </span>
                        @endif
                    </div>
            </div>
        </div>
    @endif

    <div class="checkout-totals">
        <table class="table table_total table-cart" style="width: 100%;">
            <tr>
                <td class="text-left total-title">
                    {{ trans('storefront::checkout.subtotal') }}:
                </td>
                <td class="text-right total-text">
                    {{ Cart::subTotal()->format() }}
                </td>
            </tr>

            @if(Cart::hasCoupon())
                <tr>
                    <td class="text-left total-title text-success">
                        {{ trans('storefront::checkout.coupon') }} ({{ Cart::coupon()->code() }}):
                    </td>
                    <td class="text-right total-text text-success">
                        -{{ Cart::coupon()->value()->format() }}
                    </td>
                </tr>
            @endif

            @if($hasShippingMethods)
                <tr>
                    <td class="text-left total-title">
                        {{ trans('storefront::checkout.shipping_cost') }}:
                    </td>

                    <td
                        class="text-right total-text shipping-cost-text {{ $freeShippingSummary['available'] ? 'text-success' : '' }}"
                        style="color: #d8300e;"
                    >
                        {{ $freeShippingSummary['shipping_label'] }}
                    </td>
                </tr>
            @endif

            <tr>
                <td class="text-left total-title total-last">
                    {{ trans('storefront::checkout.total') }}:
                </td>
                <td class="text-right total-text">
                    {{ Cart::total()->format() }}
                </td>
            </tr>
        </table>
    </div>

    <div class="order-summary-bottom" style="margin-top: 20px;">
        <div class="form-group checkout-terms-and-conditions">
            <div class="checkbox">
                <label class="chm-checkbox" for="terms-and-conditions">
                    <input
                        type="checkbox"
                        name="terms_and_conditions"
                        id="terms-and-conditions"
                        class="checkbox-input"
                        value="1"
                        {{ old('terms_and_conditions') ? 'checked="checked"' : '' }}
                    >

                    <span class="checkbox-check"></span>

                    {{ trans('storefront::checkout.i_agree_to_the') }}

                    <a href="{{ $termsPageURL ?? '#' }}" target="_blank">
                        {{ trans('storefront::checkout.terms_&_conditions') }}
                    </a>
                </label>
            </div>

            {!! $errors->first('terms_and_conditions', '<span class="error-message text-danger" style="display:block; margin-top:5px;">:message</span>') !!}
        </div>

        <div id="paypal-button-container" style="display: none; margin-top:15px;"></div>

        <button
            type="submit"
            class="btn btn-primary btn-place-order w-100 opc-main-submit"
            id="place-order-btn"
        >
            {{ trans('storefront::checkout.place_order') }}
        </button>
    </div>
</div>
