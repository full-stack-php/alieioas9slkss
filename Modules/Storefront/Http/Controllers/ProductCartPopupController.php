<?php

namespace Modules\Storefront\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;
use Modules\Storefront\Services\ProductCartPopupData;

class ProductCartPopupController extends Controller
{
    public function show(
        Product $product,
        ProductCartPopupData $popupData
    ): View {
        abort_unless($product->is_active, 404);

        return view(
            'storefront::public.products.cart_popup',
            [
                'configurator' => $popupData->build($product),
            ]
        );
    }
}
