<div
    id="modal-preorder"
    class="modal fade preorder-modal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    data-options-required-message="{{ trans('preorder::messages.options_required') }}"
    data-close-label="{{ trans('storefront::preorder.close') }}"
>
    <div class="modal-dialog us-modal-lg chm-modal modal-dialog-centered">
        <form
            class="modal-content"
            id="preorder-form"
            method="post"
        >
            <div class="modal-header">
                <div class="modal-title">
                    {{ trans('storefront::preorder.title') }}
                </div>

                <button
                    type="button"
                    class="close-modal"
                    data-bs-dismiss="modal"
                    aria-label="{{ trans('storefront::preorder.close') }}"
                >
                    <svg class="icon icon-11">
                        <use xlink:href="#cross"></use>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <input
                    type="hidden"
                    name="product_id"
                    value="{{ $product->id }}"
                >

                <div class="preorder-product-name">
                    {{ $product->name }}
                </div>

                <div class="preorder-text">
                    {{ trans('storefront::preorder.text') }}
                </div>

                <div class="preorder-alert d-none"></div>

                <div class="form-group field_required">
                    <div class="input-group-flex">
                        <div class="input-group-icon">
                            <svg class="icon icon-22">
                                <use xlink:href="#phone"></use>
                            </svg>
                        </div>

                        <input
                            class="form-control"
                            type="text"
                            name="phone"
                            autocomplete="tel"
                            placeholder="{{ trans('storefront::preorder.phone_placeholder') }}"
                        >
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button
                    id="preorder-submit"
                    class="chm-btn chm-btn-primary chm-px-lg chm-lg-rounded w-100"
                    type="submit"
                >
                    {{ trans('storefront::preorder.submit') }}
                </button>
            </div>
        </form>
    </div>
</div>
