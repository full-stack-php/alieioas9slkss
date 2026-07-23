<div
    class="product-configurator js-product-configurator"
    data-base-price="{{ $configurator['base_price'] }}"
    data-currency="{{ $configurator['currency'] }}"
    data-base-max-quantity="{{ $configurator['max_quantity'] }}"
>
    <div class="product-configurator__product">
        <a
            href="{{ $configurator['url'] }}"
            class="product-configurator__image"
        >
            <img
                src="{{ $configurator['image'] }}"
                alt="{{ $configurator['name'] }}"
                loading="lazy"
            >
        </a>

        <div class="product-configurator__product-info">
            <a
                href="{{ $configurator['url'] }}"
                class="product-configurator__product-name"
            >
                {{ $configurator['name'] }}
            </a>

            <div class="product-configurator__base-price">
                {{ $configurator['formatted_base_price'] }}
            </div>
        </div>
    </div>

    <div
        class="alert alert-danger d-none js-product-configurator-error"
        role="alert"
    ></div>

    <form
        class="product-configurator__form js-product-configurator-form"
        novalidate
    >
        <input
            type="hidden"
            name="product_id"
            value="{{ $configurator['id'] }}"
        >

        <input
            type="hidden"
            name="is_mirrored"
            value="0"
        >

        @if(!empty($configurator['packagings']))
            <div
                class="product-configurator__section"
                data-error-key="packaging_id"
            >
                <div class="product-configurator__section-title">
                    {{ trans('storefront::product_configurator.packaging') }}
                </div>

                <div class="product-configurator__packagings">
                    @foreach($configurator['packagings'] as $packaging)
                        <label
                            class="product-configurator__packaging {{ !$packaging['available'] ? 'is-disabled' : '' }}"
                            for="configurator-packaging-{{ $packaging['id'] }}"
                        >
                            <input
                                type="radio"
                                name="packaging_id"
                                id="configurator-packaging-{{ $packaging['id'] }}"
                                value="{{ $packaging['id'] }}"
                                data-final-price="{{ $packaging['final_price'] }}"
                                data-max-quantity="{{ $packaging['max_quantity'] }}"
                                {{ $packaging['selected'] ? 'checked' : '' }}
                                {{ !$packaging['available'] ? 'disabled' : '' }}
                            >

                            <span class="product-configurator__packaging-info">
                                <span class="product-configurator__packaging-name">
                                    {{ $packaging['name'] }}
                                </span>

                                <span class="product-configurator__packaging-price">
                                    @if($packaging['has_special_price'])
                                        <span class="old-price">
                                            {{ $packaging['formatted_regular_price'] }}
                                        </span>

                                        <span class="new-price">
                                            {{ $packaging['formatted_final_price'] }}
                                        </span>
                                    @else
                                        <span class="new-price">
                                            {{ $packaging['formatted_final_price'] }}
                                        </span>
                                    @endif
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div
                    class="invalid-feedback d-none js-configurator-field-error"
                ></div>
            </div>
        @endif

        @if(!empty($configurator['options']))
            <div class="product-configurator__section">
                <div class="product-configurator__section-title">
                    {{ trans('storefront::product_configurator.options') }}
                </div>

                <div class="product-configurator__options">
                    @foreach($configurator['options'] as $option)
                        @include(
                            'storefront::public.products.cart_popup.option',
                            [
                                'option' => $option,
                                'namePrefix' => 'options',
                                'idPrefix' => 'primary',
                            ]
                        )
                    @endforeach
                </div>
            </div>
        @endif

        @if($configurator['has_mirrored_options'])
            <div class="product-configurator__mirror-control">
                <button
                    type="button"
                    class="btn btn-outline-secondary js-product-configurator-mirror-toggle"
                    data-enable-label="{{ trans('storefront::product_configurator.enable_second_configuration') }}"
                    data-disable-label="{{ trans('storefront::product_configurator.disable_second_configuration') }}"
                >
                    {{ trans('storefront::product_configurator.enable_second_configuration') }}
                </button>
            </div>

            <div
                class="product-configurator__section product-configurator__mirror d-none js-product-configurator-mirror"
            >
                <div class="product-configurator__section-title">
                    {{ trans('storefront::product_configurator.second_configuration') }}
                </div>

                <div class="product-configurator__options">
                    @foreach($configurator['mirrored_options'] as $option)
                        @include(
                            'storefront::public.products.cart_popup.option',
                            [
                                'option' => $option,
                                'namePrefix' => 'm_options',
                                'idPrefix' => 'secondary',
                            ]
                        )
                    @endforeach
                </div>
            </div>
        @endif

        <div
            class="product-configurator__quantity"
            data-error-key="qty"
        >
            <label for="product-configurator-quantity">
                {{ trans('storefront::product_configurator.quantity') }}
            </label>

            <div class="input-group-quantity">
                <input
                    type="number"
                    name="qty"
                    id="product-configurator-quantity"
                    class="form-control js-product-configurator-quantity"
                    value="1"
                    min="1"
                    max="{{ $configurator['initial_max_quantity'] }}"
                >

                <span class="btn-wrapper">
                    <button
                        type="button"
                        class="btn btn-number js-product-configurator-plus"
                        aria-label="{{ trans('storefront::product_configurator.increase_quantity') }}"
                    >
                        +
                    </button>

                    <button
                        type="button"
                        class="btn btn-number js-product-configurator-minus"
                        aria-label="{{ trans('storefront::product_configurator.decrease_quantity') }}"
                    >
                        -
                    </button>
                </span>
            </div>

            <div
                class="invalid-feedback d-none js-configurator-field-error"
            ></div>
        </div>

        <div class="product-configurator__footer">
            <div class="product-configurator__total">
                <span class="product-configurator__total-label">
                    {{ trans('storefront::product_configurator.total') }}
                </span>

                <span class="product-configurator__total-price js-product-configurator-total">
                    {{ $configurator['formatted_base_price'] }}
                </span>
            </div>

            <button
                type="submit"
                class="btn btn-primary product-configurator__submit js-product-configurator-submit"
                {{ !$configurator['can_submit'] ? 'disabled' : '' }}
            >
                {{ trans(
                    $configurator['can_submit']
                        ? 'storefront::product_configurator.add_to_cart'
                        : 'storefront::product_configurator.unavailable'
                ) }}
            </button>
        </div>
    </form>
</div>
