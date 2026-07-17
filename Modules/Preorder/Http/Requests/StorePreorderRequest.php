<?php

namespace Modules\Preorder\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\Request;
use Modules\Option\Entities\ProductOption;
use Modules\Product\Entities\Product;

class StorePreorderRequest extends Request
{
    protected $availableAttributes = 'preorder::attributes';

    private ?Product $product = null;

    public function rules(): array
    {
        $rules = [
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

            'options' => [
                'nullable',
                'array',
            ],

            'm_options' => [
                'nullable',
                'array',
            ],

            'secondary_options' => [
                'nullable',
                'array',
            ],

            'is_mirrored' => [
                'nullable',
                'boolean',
            ],

            'packaging_id' => [
                'nullable',
                'integer',
            ],
        ];

        $product = $this->product();

        if (!$product) {
            return $rules;
        }

        $rules['packaging_id'][] = Rule::in(
            $product->packagings
                ->pluck('id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->all()
        );

        foreach ($product->options as $option) {
            $rules = array_merge(
                $rules,
                $this->getOptionRules(
                    $option,
                    'options'
                )
            );

            if ($this->boolean('is_mirrored')) {
                $rules = array_merge(
                    $rules,
                    $this->getOptionRules(
                        $option,
                        'secondary_options'
                    )
                );
            }
        }

        return $rules;
    }

    public function validationData(): array
    {
        $options = (array) $this->input(
            'options',
            []
        );

        $mirroredOptions = (array) $this->input(
            'm_options',
            []
        );

        return array_merge(
            $this->all(),
            [
                'options' => $options,
                'm_options' => $mirroredOptions,

                'secondary_options' => $this->boolean(
                    'is_mirrored'
                )
                    ? array_replace(
                        $options,
                        $mirroredOptions
                    )
                    : [],
            ]
        );
    }

    public function messages(): array
    {
        return array_merge(
            [
                'options.*.required' => trans(
                    'preorder::messages.option_required'
                ),

                'secondary_options.*.required' => trans(
                    'preorder::messages.option_required'
                ),

                'options.*.in' => trans(
                    'preorder::messages.option_invalid'
                ),

                'secondary_options.*.in' => trans(
                    'preorder::messages.option_invalid'
                ),

                'options.*.*.in' => trans(
                    'preorder::messages.option_invalid'
                ),

                'secondary_options.*.*.in' => trans(
                    'preorder::messages.option_invalid'
                ),

                'packaging_id.in' => trans(
                    'preorder::messages.packaging_invalid'
                ),
            ],
            parent::messages()
        );
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
                    trans(
                        'preorder::messages.product_unavailable'
                    )
                );

                return;
            }

            $this->validateRequiredOptions(
                $validator,
                $product
            );
        });
    }

    private function validateRequiredOptions(
        $validator,
        Product $product
    ): void {
        $primaryOptions = (array) $this->input(
            'options',
            []
        );

        foreach ($product->options as $option) {
            if (!(bool) $option->is_required) {
                continue;
            }

            $this->validateRequiredOption(
                $validator,
                $option,
                $primaryOptions,
                'options'
            );
        }

        if (!$this->boolean('is_mirrored')) {
            return;
        }

        $secondaryOptions = array_replace(
            $primaryOptions,
            (array) $this->input(
                'm_options',
                []
            )
        );

        foreach ($product->options as $option) {
            if (!(bool) $option->is_required) {
                continue;
            }

            $this->validateRequiredOption(
                $validator,
                $option,
                $secondaryOptions,
                'secondary_options'
            );
        }
    }

    private function validateRequiredOption(
        $validator,
        ProductOption $option,
        array $selectedOptions,
        string $prefix
    ): void {
        $field = "{$prefix}.{$option->id}";

        $value = array_key_exists(
            $option->id,
            $selectedOptions
        )
            ? $selectedOptions[$option->id]
            : null;

        if (!$this->optionValueIsEmpty($value)) {
            return;
        }

        if ($validator->errors()->has($field)) {
            return;
        }

        $validator->errors()->add(
            $field,
            trans(
                'preorder::messages.option_required',
                [
                    'option' => $option->name,
                ]
            )
        );
    }

    private function optionValueIsEmpty(
        mixed $value
    ): bool {
        if (is_array($value)) {
            if (empty($value)) {
                return true;
            }

            foreach ($value as $item) {
                if (!$this->optionValueIsEmpty($item)) {
                    return false;
                }
            }

            return true;
        }

        if ($value === null) {
            return true;
        }

        return trim((string) $value) === '';
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

        return $this->product = Product::withoutGlobalScope(
            'active'
        )
            ->with([
                'options.option',
                'options.values.optionValue',
                'packagings',
            ])
            ->find($productId);
    }

    private function getOptionRules(
        ProductOption $option,
        string $prefix
    ): array {
        $key = "{$prefix}.{$option->id}";

        $presenceRule = $option->is_required
            ? 'required'
            : 'nullable';

        $valueIds = $option->values
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();

        if (
            in_array(
                $option->type,
                [
                    'checkbox',
                    'checkbox_custom',
                ],
                true
            )
        ) {
            return [
                $key => [
                    $presenceRule,
                    'array',
                ],

                "{$key}.*" => [
                    'integer',
                    Rule::in($valueIds),
                ],
            ];
        }

        if (
            in_array(
                $option->type,
                [
                    'dropdown',
                    'radio',
                    'radio_custom',
                    'multiple_select',
                ],
                true
            )
        ) {
            return [
                $key => [
                    $presenceRule,
                    'integer',
                    Rule::in($valueIds),
                ],
            ];
        }

        return [
            $key => [
                $presenceRule,
                'string',
                'max:1000',
            ],
        ];
    }
}
