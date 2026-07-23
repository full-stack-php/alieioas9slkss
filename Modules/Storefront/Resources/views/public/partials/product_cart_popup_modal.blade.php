<div
    class="modal fade product-configurator-modal"
    id="product-configurator-modal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    aria-labelledby="product-configurator-modal-title"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5
                    class="modal-title"
                    id="product-configurator-modal-title"
                >
                    {{ trans('storefront::product_configurator.title') }}
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="{{ trans('storefront::product_configurator.close') }}"
                ></button>
            </div>

            <div class="modal-body js-product-configurator-content">
                <div class="product-configurator-loading">
                    {{ trans('storefront::product_configurator.loading') }}
                </div>
            </div>
        </div>
    </div>
</div>
