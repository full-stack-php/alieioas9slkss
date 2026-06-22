<?php

use Modules\Product\Entities\Product;

if (!function_exists('product_price_formatted')) {
    /**
     * Get the selling price of the given product.
     *
     * @param Product $product
     * @param Closure|null $callback
     *
     * @return string
     */
    function product_price_formatted(Product $product, Closure $callback = null): string
    {
        $price = $product->price->convertToCurrentCurrency()->format();
        $specialPrice = $product->getSpecialPrice()->convertToCurrentCurrency()->format();

        $html = "<div class='price-update-container'>";

        if (!$product->hasSpecialPrice()) {
            $html .= "<span class='autocalc-product-price'>{$price}</span>";
        } else {
            $html .= "<span class='price-old'><span class='price_value'>{$price}</span></span> ";
            $html .= "<span class='price-new'><span class='special_value'>{$specialPrice}</span></span>";
        }

        $html .= "</div>";
        return $html;
    }
}
