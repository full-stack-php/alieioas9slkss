<?php

namespace Modules\QuickOrder\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Checkout\Events\OrderPlaced;
use Modules\QuickOrder\Http\Requests\StoreQuickOrderRequest;
use Modules\QuickOrder\Services\QuickOrderService;

class QuickOrderController
{
    public function store(StoreQuickOrderRequest $request, QuickOrderService $quickOrderService): JsonResponse
    {
        $order = $quickOrderService->create($request);

        event(new OrderPlaced($order));

        return response()->json([
            'success' => true,
            'message' => trans('quickorder::messages.created'),
            'order_id' => $order->id,
        ]);
    }
}
