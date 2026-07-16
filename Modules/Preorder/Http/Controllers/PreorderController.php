<?php

namespace Modules\Preorder\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Preorder\Entities\Preorder;
use Modules\Preorder\Http\Requests\StorePreorderRequest;
use Modules\Preorder\Services\PreorderSelectionService;

class PreorderController
{
    public function store(
        StorePreorderRequest $request,
        PreorderSelectionService $selectionService
    ): JsonResponse {
        $product = $request->product();

        $options = $selectionService->resolveOptions(
            $product,
            (array) $request->input('options', []),
            (array) $request->input('m_options', []),
            $request->boolean('is_mirrored')
        );

        $packagingId = $request->filled('packaging_id')
            ? $request->integer('packaging_id')
            : null;

        $packaging = $selectionService->resolvePackaging(
            $product,
            $packagingId
        );

        $preorder = Preorder::create([
            'product_id' => $product->id,

            'phone' => trim(
                (string) $request->input('phone')
            ),

            'options' => $options ?: null,
            'packaging' => $packaging,

            'ip_address' => $request->ip(),

            'user_agent' => mb_substr(
                (string) $request->userAgent(),
                0,
                1000
            ),
        ]);

        return response()->json([
            'success' => true,

            'message' => trans(
                'preorder::messages.created'
            ),

            'preorder_id' => $preorder->id,
        ]);
    }
}
