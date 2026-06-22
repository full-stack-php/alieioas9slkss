@if($product->infoStickers->isNotEmpty())
    <div class="basic-attributes">
        @foreach($product->infoStickers as $sticker)
            <div class="basic-attribute-item">
                <div class="basic-attribute-item-inner">
                    <div class="attribute-item"
                        @if(filled($sticker->popup_description))
                            role="button"
                            tabindex="0"
                            data-bs-toggle="modal"
                            data-bs-target="#info-sticker-modal-{{ $sticker->id }}"
                            aria-controls="info-sticker-modal-{{ $sticker->id }}"
                        @endif
                    >
                        @if($sticker->image->exists)
                            <div class="attribute-item__image" style="background: {{ $sticker->image_background_color ?: 'transparent' }};">
                                <img
                                    loading="lazy"
                                    decoding="async"
                                    width="48"
                                    height="48"
                                    src="{{ $sticker->image->path }}"
                                    alt="{{ $sticker->image_alt }}"
                                >
                            </div>
                        @endif

                        <div class="attribute-item__content">
                            <span class="attribute-item__name">
                                {{ $sticker->name }}
                            </span>

                            @if(filled($sticker->description))
                                <div class="attribute-item__description">
                                    {!! $sticker->description !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @foreach($product->infoStickers as $sticker)
        @if(filled($sticker->popup_description))
            <div
                class="modal fade"
                id="info-sticker-modal-{{ $sticker->id }}"
                tabindex="-1"
                aria-labelledby="info-sticker-modal-title-{{ $sticker->id }}"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="info-sticker-modal-title-{{ $sticker->id }}"
                            >
                                {{ $sticker->name }}
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="{{ trans('admin::admin.buttons.close') }}"
                            ></button>
                        </div>

                        <div class="modal-body">
                            {!! $sticker->popup_description !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif
