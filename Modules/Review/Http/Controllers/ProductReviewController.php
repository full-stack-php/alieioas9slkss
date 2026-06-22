<?php

namespace Modules\Review\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Review\Entities\Review;
use Modules\Product\Entities\Product;
use Modules\Review\Http\Requests\StoreReviewRequest;

class ProductReviewController
{
    /**
     * Display a listing of the resource.
     *
     * @param int $productId
     *
     * @return Response
     */
    public function index($productId)
    {
        return Review::where('product_id', $productId)->latest()->paginate(5);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param int $productId
     * @param StoreReviewRequest $request
     *
     * @return Response
     */
    public function store($productId, StoreReviewRequest $request)
    {
        if (!setting('reviews_enabled')) {
            return response()->json([
                'success' => false,
                'error' => trans('storefront::product.reviews_disabled') ?? 'Отзывы отключены.'
            ], 403);
        }

        try {
            Product::findOrFail($productId)
                ->reviews()
                ->create([
                    'reviewer_id' => auth()->id(),
                    'rating' => $request->rating,
                    'reviewer_name' => $request->reviewer_name,
                    'plus' => $request->plus,
                    'minus' => $request->minus,
                    'comment' => $request->comment,
                    'is_approved' => 0,
                ]);
            return response()->json([
                'success' => true,
                'message' => trans('review::messages.submitted_for_approval')
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при сохранении отзыва: ' . $e->getMessage());

            return response()->json([
                'error' => 'Произошла ошибка при сохранении отзыва. Пожалуйста, попробуйте позже.'
            ], 500);
        }
    }
}
