@extends('storefront::public.account.layout')

@section('title', trans('storefront::account.pages.my_reviews'))

@section('account_breadcrumb')
    <li class="active">{{ trans('storefront::account.pages.my_reviews') }}</li>
@endsection

@section('panel')
    <div class="panel account-reviews-page">
        <div class="panel-header">
            <h3>{{ trans('storefront::account.pages.my_reviews') }}</h3>
        </div>

        <div class="panel-body">
            @if($reviews->isEmpty())
                <div class="empty-message">
                    <h3>
                        {{ trans('storefront::account.reviews.no_reviews') }}
                    </h3>
                </div>
            @else
                <div id="review">
                    <div class="user-review-wrap account-reviews-list">
                        @foreach($reviews as $review)
                            @php
                                $product = $review->product;

                                $productName = $product?->name
                                    ?: trans('storefront::account.reviews.deleted_product');

                                $productUrl = $product && $product->slug
                                    ? route('products.show', $product->slug)
                                    : null;

                                $reviewerName = $review->reviewer_name
                                    ?: $review->reviewer?->getFullNameAttribute()
                                    ?: auth()->user()->getFullNameAttribute()
                                    ?: auth()->user()->email;

                                $reviewerLetter = mb_substr($reviewerName, 0, 1);
                            @endphp

                            <div class="review-item account-review-item">
                                <div class="account-review-product d-flex justify-content-between mb-4">
                                    @if($productUrl)
                                        <a href="{{ $productUrl }}" class="account-review-product-link fw-semibold">
                                            {{ $productName }}
                                        </a>
                                    @else
                                        <span class="account-review-product-link text-muted">
                                            {{ $productName }}
                                        </span>
                                    @endif

                                    @if($review->is_approved)
                                        <span class="badge chm-sm-rounded chm-text-success-bg lh-sm">{{ $review->status }}</span>
                                    @else
                                        <span class="badge chm-sm-rounded chm-text-accent-bg lh-sm">{{ $review->status }}</span>
                                    @endif
                                </div>

                                <div class="rc-heading d-flex align-items-center">
                                    <div class="rc-author-letter">
                                        {{ $reviewerLetter }}
                                    </div>

                                    <div class="rc-author-info">
                                        <div class="rc-author">
                                            {{ $reviewerName }}
                                        </div>

                                        @include('storefront::public.partials.product_rating', [
                                            'data' => $review
                                        ])
                                    </div>

                                    <div class="rc-date">
                                        {{ $review->created_at->diffForHumans() }}
                                    </div>
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
                        @endforeach

                        {{ $reviews->links('storefront::public.partials.pagination') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('globals')
    @vite([
        'Modules/Storefront/Resources/assets/public/sass/account/reviews/main.scss',
    ])
@endpush
