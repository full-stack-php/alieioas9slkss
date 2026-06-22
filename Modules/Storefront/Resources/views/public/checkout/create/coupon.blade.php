<div class="checkout-cart-accordion" id="accordion">
    <a href="#collapse-dop-module" class="text-checkout-modules accordion-toggle d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-parent="#accordion">
        {{ trans('storefront::cart.use_coupon') ?? 'Применить купон' }}
        <svg class="icon icon-angel">
            <use xlink:href="#angel-down"></use>
        </svg>
    </a>

    <div id="collapse-dop-module" class="panel-collapse collapse pb-10">
        <div class="cart-coupon coupon-wrap">
            <div class="form-group">
                <label class="control-label" for="input-coupon">
                    {{ trans('storefront::checkout.coupon') ?? 'Купон' }}
                </label>

                <div class="input-group">
                    <input type="text" name="coupon" value="{{ old('coupon') }}" placeholder="{{ trans('storefront::checkout.enter_coupon_code') }}" id="input-coupon" class="form-control" />
                    <input type="button" value="ok" id="button-coupon" class="btn btn-primary" />

                </div>

                <!-- Место для вывода ошибки купона из JS -->
                <span id="coupon-error" class="error-message text-danger" style="display: block; margin-top: 5px;"></span>
            </div>
        </div>
    </div>
</div>
