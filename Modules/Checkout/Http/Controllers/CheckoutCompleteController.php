<?php

namespace Modules\Checkout\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Modules\Order\Entities\Order;
use Modules\Payment\Facades\Gateway;
use Modules\Checkout\Events\OrderPlaced;
use Modules\Checkout\Services\OrderService;
use Modules\Payment\Libraries\Bkash\BkashService;
use Modules\Payment\Libraries\Nagad\NagadPayment;

class CheckoutCompleteController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param int $orderId
     * @param OrderService $orderService
     *
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(int $orderId, OrderService $orderService)
    {

        $order = Order::findOrFail($orderId);

        $gateway = Gateway::get(request('paymentMethod'));

        try {
            $response = $gateway->complete($order);
        } catch (Exception $e) {
            $orderService->delete($order);

            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }

        $order->storeTransaction($response);

        if (! in_array($order->getRawOriginal('payment_method'), ['monobank', 'liqpay'], true)) {
            event(new OrderPlaced($order));
        }

        if (!request()->ajax()) {
            return redirect()->route('checkout.complete.show');
        }
    }


    /**
     * Display the specified resource.
     *
     * @return Application|Factory|object|View|RedirectResponse
     */
    public function show()
    {
        $order = session('placed_order');

        return $order
            ? view('storefront::public.checkout.complete.show', compact('order'))
            : redirect()->route('home');
    }
}
