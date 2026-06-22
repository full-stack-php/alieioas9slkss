<div class="tab-pane" id="tab-review">
    <h2 class="ch-h2">Отзывы о&nbsp;{{ $product->h1_name?? $product->name }}</h2>
    @php
            $rating = round($review->avg_rating ?? 0);
            $reviewCount = $review->count ?? 0;
    @endphp
    <div class="reviews-product d-flex">
        <div class="reviews-product__average">
            <div class="reviews-product__header">
                <div class="reviews-product__rating">{{ $rating }}</div>
                <div class="reviews-product__details">
                    <svg class="icon icon-18">
                        <use xlink:href="#comment"></use>
                    </svg>

                    <div class="reviews-product__totals">{{ $reviewCount }}</div>
                </div>
            </div>
            <div class="btn-block-rs">
                <button type="button" class="chm-btn chm-btn-black chm-px-lg chm-lg chm-lg-rounded xs-w-100 sm-w-auto" data-bs-toggle="modal"
                        data-bs-target="#ch-modal-review">{{ trans('storefront::product.add_a_review') }}</button>
            </div>
        </div>
        <div class="reviews-product__rating-summary">
            <div class="product-rating-summary__item">
                <div class="product-rating-summary__icon product-rating-icon-start-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14">
                        <path fill="#fff" fill-rule="evenodd" d="M8.331 3.87 7.27 1.766A29.781 29.781 0 0 0 7 1.24c-.071.134-.157.304-.27.526L5.67 3.87l-.023.046a2.569 2.569 0 0 1-.368.583c-.154.172-.34.314-.55.417-.233.116-.487.16-.663.191l-.049.01-2.242.405a20.34 20.34 0 0 0-.64.122c.108.126.258.29.476.524l1.586 1.706.035.037c.125.134.298.319.417.546.107.203.175.423.203.65.03.25-.005.5-.031.685l-.007.052-.324 2.355c-.04.29-.069.5-.086.665.132-.065.298-.15.518-.263l2.042-1.05c.014-.007.03-.014.045-.023a2.45 2.45 0 0 1 .644-.252c.23-.046.466-.046.696 0 .256.05.486.17.644.252l.045.024 2.042 1.05c.22.112.386.197.518.261a23.79 23.79 0 0 0-.086-.664l-.324-2.355a28.482 28.482 0 0 0-.007-.052c-.026-.185-.061-.434-.03-.686.027-.226.095-.446.201-.65.12-.226.293-.41.418-.545l.035-.037 1.586-1.706c.218-.235.368-.398.477-.524-.153-.033-.355-.07-.64-.122l-2.243-.406a11.268 11.268 0 0 0-.05-.009 2.416 2.416 0 0 1-.663-.19 1.829 1.829 0 0 1-.55-.418 2.598 2.598 0 0 1-.367-.583l-.023-.046Zm2.603 9.135h-.002Zm-7.868 0h.002ZM6.644.085c-.247.122-.433.492-.806 1.23L4.776 3.42c-.11.216-.164.325-.24.41a.829.829 0 0 1-.25.19c-.101.05-.217.07-.448.112l-2.242.406c-.787.142-1.18.213-1.37.421a.89.89 0 0 0-.219.707c.036.284.314.584.87 1.182l1.587 1.707c.163.176.245.264.299.366.05.095.082.199.095.306.014.117-.003.238-.036.48l-.324 2.356c-.114.826-.17 1.24-.04 1.491a.83.83 0 0 0 .576.437c.27.053.627-.132 1.344-.5l2.042-1.05c.21-.108.316-.162.426-.184a.793.793 0 0 1 .308 0c.11.022.215.076.426.184l2.042 1.05c.717.368 1.075.553 1.344.5a.83.83 0 0 0 .576-.437c.13-.252.074-.665-.04-1.491l-.324-2.355c-.033-.243-.05-.364-.036-.48a.893.893 0 0 1 .095-.307c.054-.102.136-.19.3-.366l1.585-1.707c.557-.598.835-.898.87-1.182a.889.889 0 0 0-.219-.707c-.188-.208-.582-.279-1.369-.421l-2.242-.406c-.231-.042-.347-.063-.448-.113a.829.829 0 0 1-.25-.189c-.076-.085-.13-.194-.24-.41L8.162 1.315C7.789.577 7.602.207 7.356.085a.794.794 0 0 0-.712 0Z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="product-rating-summary__content">
                    <div class="product-rating-summary__star">1</div>
                    <div class="product-rating-summary__percent">0%</div>
                </div>
            </div>
            <div class="product-rating-summary__item">
                <div class="product-rating-summary__icon product-rating-icon-start-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14">
                        <path fill="#fff" fill-rule="evenodd" d="M8.331 3.87 7.27 1.766A29.781 29.781 0 0 0 7 1.24c-.071.134-.157.304-.27.526L5.67 3.87l-.023.046a2.569 2.569 0 0 1-.368.583c-.154.172-.34.314-.55.417-.233.116-.487.16-.663.191l-.049.01-2.242.405a20.34 20.34 0 0 0-.64.122c.108.126.258.29.476.524l1.586 1.706.035.037c.125.134.298.319.417.546.107.203.175.423.203.65.03.25-.005.5-.031.685l-.007.052-.324 2.355c-.04.29-.069.5-.086.665.132-.065.298-.15.518-.263l2.042-1.05c.014-.007.03-.014.045-.023a2.45 2.45 0 0 1 .644-.252c.23-.046.466-.046.696 0 .256.05.486.17.644.252l.045.024 2.042 1.05c.22.112.386.197.518.261a23.79 23.79 0 0 0-.086-.664l-.324-2.355a28.482 28.482 0 0 0-.007-.052c-.026-.185-.061-.434-.03-.686.027-.226.095-.446.201-.65.12-.226.293-.41.418-.545l.035-.037 1.586-1.706c.218-.235.368-.398.477-.524-.153-.033-.355-.07-.64-.122l-2.243-.406a11.268 11.268 0 0 0-.05-.009 2.416 2.416 0 0 1-.663-.19 1.829 1.829 0 0 1-.55-.418 2.598 2.598 0 0 1-.367-.583l-.023-.046Zm2.603 9.135h-.002Zm-7.868 0h.002ZM6.644.085c-.247.122-.433.492-.806 1.23L4.776 3.42c-.11.216-.164.325-.24.41a.829.829 0 0 1-.25.19c-.101.05-.217.07-.448.112l-2.242.406c-.787.142-1.18.213-1.37.421a.89.89 0 0 0-.219.707c.036.284.314.584.87 1.182l1.587 1.707c.163.176.245.264.299.366.05.095.082.199.095.306.014.117-.003.238-.036.48l-.324 2.356c-.114.826-.17 1.24-.04 1.491a.83.83 0 0 0 .576.437c.27.053.627-.132 1.344-.5l2.042-1.05c.21-.108.316-.162.426-.184a.793.793 0 0 1 .308 0c.11.022.215.076.426.184l2.042 1.05c.717.368 1.075.553 1.344.5a.83.83 0 0 0 .576-.437c.13-.252.074-.665-.04-1.491l-.324-2.355c-.033-.243-.05-.364-.036-.48a.893.893 0 0 1 .095-.307c.054-.102.136-.19.3-.366l1.585-1.707c.557-.598.835-.898.87-1.182a.889.889 0 0 0-.219-.707c-.188-.208-.582-.279-1.369-.421l-2.242-.406c-.231-.042-.347-.063-.448-.113a.829.829 0 0 1-.25-.189c-.076-.085-.13-.194-.24-.41L8.162 1.315C7.789.577 7.602.207 7.356.085a.794.794 0 0 0-.712 0Z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="product-rating-summary__content">
                    <div class="product-rating-summary__star">2</div>
                    <div class="product-rating-summary__percent">0%</div>
                </div>
            </div>
            <div class="product-rating-summary__item">
                <div class="product-rating-summary__icon product-rating-icon-start-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14">
                        <path fill="#fff" fill-rule="evenodd" d="M8.331 3.87 7.27 1.766A29.781 29.781 0 0 0 7 1.24c-.071.134-.157.304-.27.526L5.67 3.87l-.023.046a2.569 2.569 0 0 1-.368.583c-.154.172-.34.314-.55.417-.233.116-.487.16-.663.191l-.049.01-2.242.405a20.34 20.34 0 0 0-.64.122c.108.126.258.29.476.524l1.586 1.706.035.037c.125.134.298.319.417.546.107.203.175.423.203.65.03.25-.005.5-.031.685l-.007.052-.324 2.355c-.04.29-.069.5-.086.665.132-.065.298-.15.518-.263l2.042-1.05c.014-.007.03-.014.045-.023a2.45 2.45 0 0 1 .644-.252c.23-.046.466-.046.696 0 .256.05.486.17.644.252l.045.024 2.042 1.05c.22.112.386.197.518.261a23.79 23.79 0 0 0-.086-.664l-.324-2.355a28.482 28.482 0 0 0-.007-.052c-.026-.185-.061-.434-.03-.686.027-.226.095-.446.201-.65.12-.226.293-.41.418-.545l.035-.037 1.586-1.706c.218-.235.368-.398.477-.524-.153-.033-.355-.07-.64-.122l-2.243-.406a11.268 11.268 0 0 0-.05-.009 2.416 2.416 0 0 1-.663-.19 1.829 1.829 0 0 1-.55-.418 2.598 2.598 0 0 1-.367-.583l-.023-.046Zm2.603 9.135h-.002Zm-7.868 0h.002ZM6.644.085c-.247.122-.433.492-.806 1.23L4.776 3.42c-.11.216-.164.325-.24.41a.829.829 0 0 1-.25.19c-.101.05-.217.07-.448.112l-2.242.406c-.787.142-1.18.213-1.37.421a.89.89 0 0 0-.219.707c.036.284.314.584.87 1.182l1.587 1.707c.163.176.245.264.299.366.05.095.082.199.095.306.014.117-.003.238-.036.48l-.324 2.356c-.114.826-.17 1.24-.04 1.491a.83.83 0 0 0 .576.437c.27.053.627-.132 1.344-.5l2.042-1.05c.21-.108.316-.162.426-.184a.793.793 0 0 1 .308 0c.11.022.215.076.426.184l2.042 1.05c.717.368 1.075.553 1.344.5a.83.83 0 0 0 .576-.437c.13-.252.074-.665-.04-1.491l-.324-2.355c-.033-.243-.05-.364-.036-.48a.893.893 0 0 1 .095-.307c.054-.102.136-.19.3-.366l1.585-1.707c.557-.598.835-.898.87-1.182a.889.889 0 0 0-.219-.707c-.188-.208-.582-.279-1.369-.421l-2.242-.406c-.231-.042-.347-.063-.448-.113a.829.829 0 0 1-.25-.189c-.076-.085-.13-.194-.24-.41L8.162 1.315C7.789.577 7.602.207 7.356.085a.794.794 0 0 0-.712 0Z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="product-rating-summary__content">
                    <div class="product-rating-summary__star">3</div>
                    <div class="product-rating-summary__percent">0%</div>
                </div>
            </div>
            <div class="product-rating-summary__item">
                <div class="product-rating-summary__icon product-rating-icon-start-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14">
                        <path fill="#fff" fill-rule="evenodd" d="M8.331 3.87 7.27 1.766A29.781 29.781 0 0 0 7 1.24c-.071.134-.157.304-.27.526L5.67 3.87l-.023.046a2.569 2.569 0 0 1-.368.583c-.154.172-.34.314-.55.417-.233.116-.487.16-.663.191l-.049.01-2.242.405a20.34 20.34 0 0 0-.64.122c.108.126.258.29.476.524l1.586 1.706.035.037c.125.134.298.319.417.546.107.203.175.423.203.65.03.25-.005.5-.031.685l-.007.052-.324 2.355c-.04.29-.069.5-.086.665.132-.065.298-.15.518-.263l2.042-1.05c.014-.007.03-.014.045-.023a2.45 2.45 0 0 1 .644-.252c.23-.046.466-.046.696 0 .256.05.486.17.644.252l.045.024 2.042 1.05c.22.112.386.197.518.261a23.79 23.79 0 0 0-.086-.664l-.324-2.355a28.482 28.482 0 0 0-.007-.052c-.026-.185-.061-.434-.03-.686.027-.226.095-.446.201-.65.12-.226.293-.41.418-.545l.035-.037 1.586-1.706c.218-.235.368-.398.477-.524-.153-.033-.355-.07-.64-.122l-2.243-.406a11.268 11.268 0 0 0-.05-.009 2.416 2.416 0 0 1-.663-.19 1.829 1.829 0 0 1-.55-.418 2.598 2.598 0 0 1-.367-.583l-.023-.046Zm2.603 9.135h-.002Zm-7.868 0h.002ZM6.644.085c-.247.122-.433.492-.806 1.23L4.776 3.42c-.11.216-.164.325-.24.41a.829.829 0 0 1-.25.19c-.101.05-.217.07-.448.112l-2.242.406c-.787.142-1.18.213-1.37.421a.89.89 0 0 0-.219.707c.036.284.314.584.87 1.182l1.587 1.707c.163.176.245.264.299.366.05.095.082.199.095.306.014.117-.003.238-.036.48l-.324 2.356c-.114.826-.17 1.24-.04 1.491a.83.83 0 0 0 .576.437c.27.053.627-.132 1.344-.5l2.042-1.05c.21-.108.316-.162.426-.184a.793.793 0 0 1 .308 0c.11.022.215.076.426.184l2.042 1.05c.717.368 1.075.553 1.344.5a.83.83 0 0 0 .576-.437c.13-.252.074-.665-.04-1.491l-.324-2.355c-.033-.243-.05-.364-.036-.48a.893.893 0 0 1 .095-.307c.054-.102.136-.19.3-.366l1.585-1.707c.557-.598.835-.898.87-1.182a.889.889 0 0 0-.219-.707c-.188-.208-.582-.279-1.369-.421l-2.242-.406c-.231-.042-.347-.063-.448-.113a.829.829 0 0 1-.25-.189c-.076-.085-.13-.194-.24-.41L8.162 1.315C7.789.577 7.602.207 7.356.085a.794.794 0 0 0-.712 0Z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="product-rating-summary__content">
                    <div class="product-rating-summary__star">4</div>
                    <div class="product-rating-summary__percent">0%</div>
                </div>
            </div>
            <div class="product-rating-summary__item">
                <div class="product-rating-summary__icon product-rating-icon-start-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 14 14">
                        <path fill="#fff" fill-rule="evenodd" d="M8.331 3.87 7.27 1.766A29.781 29.781 0 0 0 7 1.24c-.071.134-.157.304-.27.526L5.67 3.87l-.023.046a2.569 2.569 0 0 1-.368.583c-.154.172-.34.314-.55.417-.233.116-.487.16-.663.191l-.049.01-2.242.405a20.34 20.34 0 0 0-.64.122c.108.126.258.29.476.524l1.586 1.706.035.037c.125.134.298.319.417.546.107.203.175.423.203.65.03.25-.005.5-.031.685l-.007.052-.324 2.355c-.04.29-.069.5-.086.665.132-.065.298-.15.518-.263l2.042-1.05c.014-.007.03-.014.045-.023a2.45 2.45 0 0 1 .644-.252c.23-.046.466-.046.696 0 .256.05.486.17.644.252l.045.024 2.042 1.05c.22.112.386.197.518.261a23.79 23.79 0 0 0-.086-.664l-.324-2.355a28.482 28.482 0 0 0-.007-.052c-.026-.185-.061-.434-.03-.686.027-.226.095-.446.201-.65.12-.226.293-.41.418-.545l.035-.037 1.586-1.706c.218-.235.368-.398.477-.524-.153-.033-.355-.07-.64-.122l-2.243-.406a11.268 11.268 0 0 0-.05-.009 2.416 2.416 0 0 1-.663-.19 1.829 1.829 0 0 1-.55-.418 2.598 2.598 0 0 1-.367-.583l-.023-.046Zm2.603 9.135h-.002Zm-7.868 0h.002ZM6.644.085c-.247.122-.433.492-.806 1.23L4.776 3.42c-.11.216-.164.325-.24.41a.829.829 0 0 1-.25.19c-.101.05-.217.07-.448.112l-2.242.406c-.787.142-1.18.213-1.37.421a.89.89 0 0 0-.219.707c.036.284.314.584.87 1.182l1.587 1.707c.163.176.245.264.299.366.05.095.082.199.095.306.014.117-.003.238-.036.48l-.324 2.356c-.114.826-.17 1.24-.04 1.491a.83.83 0 0 0 .576.437c.27.053.627-.132 1.344-.5l2.042-1.05c.21-.108.316-.162.426-.184a.793.793 0 0 1 .308 0c.11.022.215.076.426.184l2.042 1.05c.717.368 1.075.553 1.344.5a.83.83 0 0 0 .576-.437c.13-.252.074-.665-.04-1.491l-.324-2.355c-.033-.243-.05-.364-.036-.48a.893.893 0 0 1 .095-.307c.054-.102.136-.19.3-.366l1.585-1.707c.557-.598.835-.898.87-1.182a.889.889 0 0 0-.219-.707c-.188-.208-.582-.279-1.369-.421l-2.242-.406c-.231-.042-.347-.063-.448-.113a.829.829 0 0 1-.25-.189c-.076-.085-.13-.194-.24-.41L8.162 1.315C7.789.577 7.602.207 7.356.085a.794.794 0 0 0-.712 0Z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="product-rating-summary__content">
                    <div class="product-rating-summary__star">5</div>
                    <div class="product-rating-summary__percent">0%</div>
                </div>
            </div>
        </div>
    </div>

    <div id="review">
        <div class="user-review-wrap js-reviews-list">
            @forelse($product->reviews()->paginate(5) as $review)
                <div class="review-item">
                    <div class="rc-heading d-flex align-items-center">
                        <div class="rc-author-letter">{{ substr($review->reviewer_name, 0, 1) }}</div>
                        <div class="rc-author-info">
                            <div class="rc-author">
                                {{ $review->reviewer_name }}
                            </div>
                            @include('storefront::public.partials.product_rating', ['data' => $review])
                        </div>
                        <div class="rc-date">{{ $review->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="review-info">
                        <div class="comment">
                            {{ $review->comment }}
                        </div>
                        @if($review->plus)
                        <div class="review_plus">
                            <div>
                                <span>{{ trans('storefront::product.review_plus') }}:</span>
                                {{ $review->plus }}
                            </div>
                        </div>
                        @endif
                        @if($review->minus)
                        <div class="review_minus">
                            <div>
                                <span>{{ trans('storefront::product.review_minus') }}:</span>
                                {{ $review->minus }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-message">
                    <span>{{ trans('storefront::product.be_the_first_one_to_review_this_product') }}</span>
                </div>
            @endforelse

            {{ $product->reviews()->paginate(5)->links('storefront::public.partials.pagination') }}
        </div>
    </div>



</div>




<div class="modal fade" id="ch-modal-review" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog chm-modal modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">{{ trans('storefront::product.add_a_review') }}</div>
                <button type="button" class="close-modal" data-bs-dismiss="modal">
                    <svg class="icon icon-11">
                        <use xlink:href="#cross"></use>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
{{--                @if ($product->purchasedByUser())--}}
                @if(auth()->check())
                <form id="form-review" action="{{ route('products.reviews.store', $product->id ?? 0) }}" enctype="multipart/form-data" method="POST">
                    @csrf

                    <div class="form-group required_field @error('name') has-error @enderror">
                        <input type="text"
                               name="reviewer_name"
                               placeholder="{{ trans('storefront::product.review_form.name') }}"
                               value="{{ old('reviewer_name', auth()->check() ? auth()->user()->getFullNameAttribute() : '') }}"
                               id="input-name"
                               class="form-control"
                            {{ auth()->check() ? 'readonly' : '' }}
                        />
                        @error('reviewer_name')
                        <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group required_field @error('text') has-error @enderror">
                        <textarea name="comment" rows="5" placeholder="{{ trans('storefront::product.review_form.comment') ?? 'Ваш отзыв' }}" id="input-review" class="form-control">{{ old('comment') }}</textarea>
                        @error('comment')
                        <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group {{ !empty($show_review_plus_required) ? 'required_field' : '' }} @error('plus') has-error @enderror">
                        <textarea name="plus" rows="3" placeholder="{{ trans('storefront::product.review_form.plus') ?? 'Достоинства' }}" id="input-review-plus" class="form-control">{{ old('plus') }}</textarea>
                        @error('plus')
                        <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group {{ !empty($show_review_minus_required) ? 'required_field' : '' }} @error('minus') has-error @enderror">
                        <textarea name="minus" rows="3" placeholder="{{ trans('storefront::product.review_form.minus') ?? 'Недостатки' }}" id="input-review-minus" class="form-control">{{ old('minus') }}</textarea>
                        @error('minus')
                        <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group required @error('rating') has-error @enderror">
                        <label class="control-label">{{ trans('storefront::product.review_form.your_rating') ?? 'Оценка' }}</label>
                        <div class="product-rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <input class="d-none" id="rating{{ $i }}" type="radio" autocomplete="off" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} />
                                <label class="up-icon star-rating label-star-prod {{ old('rating') >= $i ? 'checked' : '' }}" for="rating{{ $i }}"></label>
                            @endfor
                        </div>
                        @error('rating')
                        <span class="help-block text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group help-block">
                        {!! trans('storefront::product.review_form.note') ?? 'Внимание: HTML не поддерживается! Используйте обычный текст.' !!}
                    </div>

                    <div class="captcha-block">

                    </div>

                </form>
                @else
                    {!! trans('storefront::product.review_form.please_auth') !!}
                @endif
            </div>
            @if(auth()->check())
            <div class="modal-footer">
                <button class="btn btn-primary w-100" type="button" id="button-review" data-loading-text="Загрузка...">{{ trans('storefront::product.review_form.submit_review') }}</button>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    @if (setting('google_recaptcha_enabled'))
        <script async src="https://www.google.com/recaptcha/api.js"></script>
    @endif
@endpush
