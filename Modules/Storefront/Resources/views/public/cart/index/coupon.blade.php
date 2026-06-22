<div class="cart-coupon">
    <form action="{{ route('cart.coupon.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="control-label" for="input-coupon">Промокод</label>
            <div class="input-group">
                <input type="text" name="coupon" value="{{ old('coupon') }}" placeholder="Промокод" id="input-coupon" class="form-control" />

                    <input type="button" value="ok" id="button-coupon" class="btn btn-primary" />
            </div>
        </div>
    </form>
</div>
