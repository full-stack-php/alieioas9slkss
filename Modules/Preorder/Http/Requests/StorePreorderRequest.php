<?php

namespace Modules\Preorder\Http\Requests;

use Modules\Core\Http\Requests\Request;
use Modules\Product\Entities\Product;

class StorePreorderRequest extends Request
{
    protected $availableAttributes = 'preorder::attributes';

    private ?Product $product = null;

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'phone' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $product = $this->product();

            if (
                !$product
                || !$product->is_active
                || !$product->isPreorder()
            ) {
                $validator->errors()->add(
                    'product_id',
                    trans('preorder::messages.product_unavailable')
                );
            }
        });
    }

    public function product(): ?Product
    {
        if ($this->product) {
            return $this->product;
        }

        $productId = $this->input('product_id');

        if (!$productId) {
            return null;
        }

        return $this->product = Product::withoutGlobalScope('active')
            ->find($productId);
    }
}
