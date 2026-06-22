<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Contracts\View\View;
use Modules\Review\Entities\Review;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

class AccountReviewController
{
    public function index(): View|Factory|Application
    {
        $reviews = Review::withoutGlobalScope('approved')
            ->with([
                'product' => function ($query) {
                    $query->withoutGlobalScope('active');
                },
            ])
            ->where('reviewer_id', auth()->id())
            ->latest()
            ->paginate(5);

        return view('storefront::public.account.reviews.index', [
            'reviews' => $reviews,
        ]);
    }
}
