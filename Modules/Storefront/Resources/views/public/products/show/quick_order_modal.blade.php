<div id="modal-quickorder" class="modal fade quick-order-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div id="popup-quickorder" class="modal-dialog us-modal-lg chm-modal modal-dialog-centered">
        <form class="modal-content" id="fastorder_data" enctype="multipart/form-data" method="post">
            <div class="modal-header">
                <div class="modal-title">
                    {{ trans('storefront::quick_order.title') }}
                </div>

                <button type="button" class="close-modal" data-bs-dismiss="modal" aria-label="{{ trans('storefront::quick_order.close') }}">
                    <svg class="icon icon-11"><use xlink:href="#cross"></use></svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="row-flex flex-column flex-sm-row">
                    <div class="col-12 p-3 d-flex flex-column fo_product">
                        <div class="d-flex flex-column mb-20">
                            <div class="fo_product__content d-flex flex-column w-100 justify-content-center">
                                <div class="fo_product__name">
                                    <h3>{{ $product->name }}</h3>
                                </div>

                                <div class="hidden">
                                    <input type="hidden" id="this_prod_id" value="{{ $product->id }}" name="product_id">
                                </div>
                            </div>
                            <div class="fo_product__image d-flex justify-content-center">
                                @if($product->base_image && $product->base_image->path)
                                    <img
                                        class="img-fluid"
                                        src="{{ $product->base_image->resizeAndCrop(350, 350) }}"
                                        alt="{{ $product->name }}"
                                    >
                                @endif
                            </div>
                        </div>

                        <div class="fo-text-info mt-auto text-left xs-mb-20 md-mb-0">
                            {{ trans('storefront::quick_order.text_before_button') }}
                        </div>

                        <div class="quick-order-alert d-none"></div>

                        @if(config('korf.modules.quickorder.config.fields.phone.enabled', true))
                            <div class="form-group {{ config('korf.modules.quickorder.config.fields.phone.required', true) ? 'field_required' : '' }} mb-0">
                                <div class="input-group-flex">
                                    <div class="input-group-icon">
                                        <svg class="icon icon-22">
                                            <use xlink:href="#phone"></use>
                                        </svg>
                                    </div>

                                    <input
                                        id="contact-phone"
                                        class="form-control contact-phone"
                                        type="text"
                                        placeholder="{{ trans('storefront::quick_order.phone_placeholder') }}"
                                        value=""
                                        name="phone"
                                        autocomplete="tel"
                                    >
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                    <button
                        id="up-btn-fastorder"
                        class="chm-btn chm-btn-primary qo_confirm chm-px-lg chm-lg-rounded w-100"
                        type="submit"
                    >
                        {{ trans('storefront::quick_order.submit') }}
                    </button>

            </div>
        </form>
    </div>
</div>
