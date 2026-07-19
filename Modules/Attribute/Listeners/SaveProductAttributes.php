<?php

namespace Modules\Attribute\Listeners;

use Modules\Product\Entities\Product;
use Modules\Attribute\Entities\ProductAttributeValue;

class SaveProductAttributes
{
    /**
     * Handle the event.
     *
     * @param Product $product
     *
     * @return void
     */
    public function handle(Product $product)
    {
        $this->deleteProductAttributes($product);
        $this->createProductAttributes($product);
    }


    /**
     * Delete all product attributes associated with the given product.
     *
     * @param Product $product
     *
     * @return void
     */
    private function deleteProductAttributes(Product $product)
    {
        $product->attributes()->delete();
    }


    /**
     * Create product attributes for the given product.
     *
     * @param Product $product
     *
     * @return void
     */
    private function createProductAttributes(
        Product $product
    ): void {
        $productAttributeValues = [];

        foreach (
            request('attributes', [])
            as $index => $attribute
        ) {
            if (empty($attribute['attribute_id'])) {
                continue;
            }

            $position = array_key_exists(
                'position',
                $attribute
            )
                ? max(0, (int) $attribute['position'])
                : (int) $index;

            $productAttribute = $product
                ->attributes()
                ->create([
                    'attribute_id' => (int) $attribute[
                    'attribute_id'
                    ],

                    'position' => $position,
                ]);

            foreach (
                (array) ($attribute['values'] ?? [])
                as $valueId
            ) {
                $productAttributeValues[] = [
                    'product_attribute_id' =>
                        $productAttribute->id,

                    'attribute_value_id' => (int) $valueId,
                ];
            }
        }

        $this->createProductAttributeValues(
            $productAttributeValues
        );
    }


    /**
     * Create the given product attribute values.
     *
     * @param array $productAttributeValues
     *
     * @return void
     */
    private function createProductAttributeValues(array $productAttributeValues)
    {
        ProductAttributeValue::insert($productAttributeValues);
    }
}
