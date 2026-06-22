<div class="checkout-comment h-100">
    <div class="checkout-heading">
        <div class="title-customer d-flex">
            {{ trans('checkout::attributes.order_note') }}
        </div>
    </div>
    <div class="row checkout-comment-info">
        <div class="mb-3 col-12 col-sm-12">
            <textarea name="order_note" placeholder="{{ trans('storefront::checkout.special_note_for_delivery') }}" rows="3" class="form-control">{{ old('order_note') }}</textarea>
        </div>
    </div>
</div>
