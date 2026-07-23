<?php

namespace Modules\Cart\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Product\Entities\Product;
use Modules\Core\Http\Requests\Request;
use Modules\Option\Entities\ProductOption;

class StoreCartItemRequest extends Request
{
    private ?Product $product = null;

    protected function prepareForValidation()
    {
        $this->merge([
            'qty' => $this->input('qty', 1),
        ]);
    }

    public function rules()
    {
        $product = $this->getProduct();

        $rules = [
            'product_id' => [
                'required',
                'integer',

                Rule::exists('products', 'id')
                    ->where(function ($query) {
                        $query->where(
                            'is_active',
                            true
                        );
                    }),
            ],

            'qty' => [
                'required',
                'integer',
                'min:1',
            ],

            'options' => [
                'nullable',
                'array',
            ],

            'packaging_id' => $this->getPackagingRules(
                $product
            ),
        ];

        if (!$product) {
            return $rules;
        }

        return array_merge(
            $rules,
            $this->getOptionsRules(
                $product->options
            )
        );
    }

    public function messages()
    {
        return array_merge([
            'product_id.required' => trans(
                'cart::validation.product_required'
            ),

            'product_id.integer' => trans(
                'cart::validation.product_invalid'
            ),

            'product_id.exists' => trans(
                'cart::validation.product_invalid'
            ),

            'qty.required' => trans(
                'cart::validation.quantity_required'
            ),

            'qty.integer' => trans(
                'cart::validation.quantity_invalid'
            ),

            'qty.min' => trans(
                'cart::validation.quantity_invalid'
            ),

            'packaging_id.required' => trans(
                'cart::validation.packaging_required'
            ),

            'packaging_id.integer' => trans(
                'cart::validation.packaging_invalid'
            ),

            'packaging_id.exists' => trans(
                'cart::validation.packaging_invalid'
            ),

            'options.array' => trans(
                'cart::validation.options_invalid'
            ),

            'options.*.required' => trans(
                'cart::validation.this_field_is_required'
            ),

            'options.*.array' => trans(
                'cart::validation.the_selected_option_is_invalid'
            ),

            'options.*.min' => trans(
                'cart::validation.this_field_is_required'
            ),

            'options.*.integer' => trans(
                'cart::validation.the_selected_option_is_invalid'
            ),

            'options.*.in' => trans(
                'cart::validation.the_selected_option_is_invalid'
            ),

            'options.*.*.integer' => trans(
                'cart::validation.the_selected_option_is_invalid'
            ),

            'options.*.*.in' => trans(
                'cart::validation.the_selected_option_is_invalid'
            ),

            'options.*.string' => trans(
                'cart::validation.the_selected_option_is_invalid'
            ),

            'options.*.max' => trans(
                'cart::validation.option_text_too_long'
            ),
        ], parent::messages());
    }

    private function getProduct(): ?Product
    {
        if ($this->product) {
            return $this->product;
        }

        $productId = $this->input('product_id');

        if (!$productId) {
            return null;
        }

        $this->product = Product::with([
            'options.values',
        ])
            ->select([
                'id',
                'manage_stock',
                'stock_status',
                'qty',
                'is_active',
            ])
            ->find($productId);

        return $this->product;
    }

    private function getPackagingRules(
        ?Product $product
    ): array {
        if (!$product) {
            return [
                'nullable',
                'integer',
            ];
        }

        $hasPackagings = $product
            ->packagings()
            ->exists();

        return [
            $hasPackagings
                ? 'required'
                : 'nullable',

            'integer',

            Rule::exists(
                'product_packagings',
                'id'
            )->where(function ($query) use ($product) {
                $query
                    ->where(
                        'product_id',
                        $product->id
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->whereNull(
                        'deleted_at'
                    );
            }),
        ];
    }

    private function getOptionsRules(
        $options
    ): array {
        $rules = [];

        foreach ($options as $option) {
            $optionRules = $this->getOptionRules(
                $option
            );

            foreach (
                $optionRules
                as $attribute => $attributeRules
            ) {
                $rules[$attribute] = $attributeRules;
            }
        }

        return $rules;
    }

    private function getOptionRules(
        ProductOption $option
    ): array {
        $attribute = "options.{$option->id}";

        $allowedValues = $option->values
            ->pluck('id')
            ->map(function ($valueId) {
                return (int) $valueId;
            })
            ->all();

        if (
            in_array(
                $option->type,
                [
                    'checkbox',
                    'checkbox_custom',
                    'multiple_select',
                ],
                true
            )
        ) {
            $rules = [
                $attribute => [
                    $option->is_required
                        ? 'required'
                        : 'nullable',

                    'array',
                ],

                "{$attribute}.*" => [
                    'integer',
                    Rule::in($allowedValues),
                ],
            ];

            if ($option->is_required) {
                $rules[$attribute][] = 'min:1';
            }

            return $rules;
        }

        if (
            in_array(
                $option->type,
                [
                    'dropdown',
                    'radio',
                    'radio_custom',
                ],
                true
            )
        ) {
            return [
                $attribute => [
                    $option->is_required
                        ? 'required'
                        : 'nullable',

                    'integer',
                    Rule::in($allowedValues),
                ],
            ];
        }

        return [
            $attribute => [
                $option->is_required
                    ? 'required'
                    : 'nullable',

                'string',
                'max:2000',
            ],
        ];
    }
}
