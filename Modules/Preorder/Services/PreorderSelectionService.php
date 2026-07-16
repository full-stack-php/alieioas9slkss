<?php

namespace Modules\Preorder\Services;

use Modules\Option\Entities\ProductOption;
use Modules\Product\Entities\Product;

class PreorderSelectionService
{
    public function resolveOptions(
        Product $product,
        array $options,
        array $mirroredOptions = [],
        bool $isMirrored = false
    ): array {
        $groups = [
            [
                'key' => 'primary',
                'values' => $options,
            ],
        ];

        if ($isMirrored) {
            $groups[] = [
                'key' => 'secondary',
                'values' => array_replace(
                    $options,
                    $mirroredOptions
                ),
            ];
        }

        return collect($groups)
            ->flatMap(function (array $group) use ($product) {
                return $product->options
                    ->map(function (ProductOption $option) use ($group) {
                        if (
                            !array_key_exists(
                                $option->id,
                                $group['values']
                            )
                        ) {
                            return null;
                        }

                        return $this->makeOptionSnapshot(
                            $option,
                            $group['values'][$option->id],
                            $group['key']
                        );
                    })
                    ->filter()
                    ->values();
            })
            ->values()
            ->all();
    }

    public function resolvePackaging(
        Product $product,
        ?int $packagingId
    ): ?array {
        if (!$packagingId) {
            return null;
        }

        $packaging = $product->packagings
            ->firstWhere('id', $packagingId);

        if (!$packaging) {
            return null;
        }

        return [
            'id' => (int) $packaging->id,
            'label' => sprintf(
                (string) $packaging->name,
                (int) $packaging->qty
            ),
            'qty' => (int) $packaging->qty,
        ];
    }

    private function makeOptionSnapshot(
        ProductOption $option,
        mixed $selected,
        string $group
    ): ?array {
        $selectedValues = is_array($selected)
            ? $selected
            : [$selected];

        if ($this->isSelectableOption($option)) {
            $values = collect($selectedValues)
                ->map(function ($valueId) use ($option) {
                    $value = $option->values
                        ->firstWhere('id', (int) $valueId);

                    return $value
                        ? trim((string) $value->label)
                        : null;
                });
        } else {
            $values = collect($selectedValues)
                ->map(function ($value) {
                    return trim((string) $value);
                });
        }

        $values = $values
            ->filter(function ($value) {
                return $value !== null && $value !== '';
            })
            ->values();

        if ($values->isEmpty()) {
            return null;
        }

        return [
            'group' => $group,
            'option_id' => (int) $option->id,
            'name' => (string) $option->name,
            'values' => $values->all(),
        ];
    }

    private function isSelectableOption(
        ProductOption $option
    ): bool {
        return in_array(
            $option->type,
            [
                'dropdown',
                'checkbox',
                'checkbox_custom',
                'radio',
                'radio_custom',
                'multiple_select',
            ],
            true
        );
    }
}
