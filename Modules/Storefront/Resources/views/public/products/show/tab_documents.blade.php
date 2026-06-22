@if($documents->isNotEmpty())
    <div class="tab-pane" id="tab-documents">
        <h2 class="ch-h2">
            {{ trans('storefront::product.documents') }} {{ $product->h1_name ?? $product->name }}
        </h2>

        <div class="product-documents">
            @foreach($documents as $file)
                @if($file->isImage())
                    <a
                        href="{{ $file->path }}"
                        class="product-documents__item js-open-document-image-modal"
                        data-title="{{ $file->filename }}"
                    >
                        <span class="product-documents__icon">
                            <img
                                src="{{ $file->path }}"
                                alt="{{ $file->filename }}"
                                width="42"
                                height="42"
                            >
                        </span>

                        <span class="product-documents__content">
                            <span class="product-documents__name">
                                {{ $file->filename }}
                            </span>

                            <span class="product-documents__download">
                                {{ trans('storefront::product.open_image') }}
                            </span>
                        </span>
                    </a>
                @else
                    <a
                        href="{{ $file->path }}"
                        class="product-documents__item"
                        target="_blank"
                        rel="noopener noreferrer"
                        download
                    >
                        <span class="product-documents__icon">
                            <i class="fa {{ $file->icon() }}"></i>
                        </span>

                        <span class="product-documents__content">
                            <span class="product-documents__name">
                                {{ $file->filename }}
                            </span>

                            <span class="product-documents__download">
                                {{ trans('storefront::product.download_document') }}
                            </span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <div
        class="modal fade"
        id="document-image-modal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog chm-modal modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title" id="document-image-modal-title">
                        {{ trans('storefront::product.documents') }}
                    </div>

                    <button
                        type="button"
                        class="close-modal"
                        data-bs-dismiss="modal"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <svg class="icon icon-11">
                            <use xlink:href="#cross"></use>
                        </svg>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <img
                        id="document-image-modal-img"
                        class="img-fluid document-image-modal-img"
                        src=""
                        alt=""
                    >
                </div>
            </div>
        </div>
    </div>
@endif

@once
    @push('scripts')
        <script>
            document.addEventListener('click', function (event) {
                const link = event.target.closest('.js-open-document-image-modal');

                if (!link) {
                    return;
                }

                event.preventDefault();

                const modalElement = document.getElementById('document-image-modal');
                const modalImage = document.getElementById('document-image-modal-img');
                const modalTitle = document.getElementById('document-image-modal-title');

                if (!modalElement || !modalImage || !modalTitle) {
                    return;
                }

                modalImage.src = link.getAttribute('href');
                modalImage.alt = link.dataset.title || '';
                modalTitle.textContent = link.dataset.title || '{{ trans('storefront::product.documents') }}';

                if (window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                    return;
                }

                if (window.jQuery && window.jQuery.fn.modal) {
                    window.jQuery(modalElement).modal('show');
                }
            });

            document.addEventListener('hidden.bs.modal', function (event) {
                if (event.target.id !== 'document-image-modal') {
                    return;
                }

                const modalImage = document.getElementById('document-image-modal-img');

                if (modalImage) {
                    modalImage.src = '';
                }
            });
        </script>
    @endpush
@endonce
