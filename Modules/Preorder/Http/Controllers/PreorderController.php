<?php

namespace Modules\Preorder\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Preorder\Entities\Preorder;
use Modules\Preorder\Http\Requests\StorePreorderRequest;

class PreorderController
{
    public function store(StorePreorderRequest $request): JsonResponse
    {
        $preorder = Preorder::create([
            'product_id' => $request->integer('product_id'),
            'phone' => trim((string) $request->input('phone')),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr(
                (string) $request->userAgent(),
                0,
                1000
            ),
        ]);

        return response()->json([
            'success' => true,
            'message' => trans('preorder::messages.created'),
            'preorder_id' => $preorder->id,
        ]);
    }
}
