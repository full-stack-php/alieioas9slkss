<?php

namespace Modules\Storefront\Services;

use Modules\Support\Money;
use Modules\Product\Entities\Product;
use Modules\Option\Entities\ProductOption;
use Modules\Product\Entities\ProductPackaging;

class ProductCartPopupData
{
    public function build(Product $product): array
    {
        $product->loadMissing([
            'files',
            'translations',
            'options.option.translations',
            'options.values.optionValue.translations',
            'packagings.translations',
        ]);

        $mirroredOptionIds = collect(
            setting('product_mirrored_options') ?? []
        )
            ->map(function ($optionId) {
                return (int) $optionId;
            })
            ->values()
            ->all();

        $options = $product->options
            ->map(function (ProductOption $option) use ($mirroredOptionIds) {
                return $this->optionData($option, $mirroredOptionIds);
            })
            ->values();

        $packagings = $product->packagings
            ->map(function (ProductPackaging $packaging) use ($product) {
                return $this->packagingData($product, $packaging);
            })
            ->values();

        $selectedPackaging = $packagings->first(function (array $packaging) {
            return $packaging['available'];
        });

        $selectedPackagingId = $selectedPackaging['id'] ?? null;

        $packagings = $packagings
            ->map(function (array $packaging) use ($selectedPackagingId) {
                $packaging['selected'] = (
                    $selectedPackagingId !== null
                    && $packaging['id'] === $selectedPackagingId
                );

                return $packaging;
            })
            ->values();

        $baseImage = $product->base_image;

        $image = (
            $baseImage
            && !empty($baseImage->path)
        )
            ? $baseImage->resizeAndCrop(320, 320)
            : asset('build/assets/image-placeholder.png');

        $maxQuantity = $product->manage_stock
            ? max(0, (int) $product->qty)
            : 999;

        $initialMaxQuantity = $selectedPackaging['max_quantity']
            ?? $maxQuantity;

        return [
            'id' => (int) $product->id,
            'name' => $product->name,
            'url' => $product->url(),
            'image' => $image,

            'currency' => currency(),

            'base_price' => $product
                ->selling_price
                ->convertToCurrentCurrency()
                ->amount(),

            'formatted_base_price' => $product
                ->selling_price
                ->convertToCurrentCurrency()
                ->format(),

            'manage_stock' => (bool) $product->manage_stock,
            'stock_quantity' => (int) $product->qty,
            'max_quantity' => $maxQuantity,
            'initial_max_quantity' => $initialMaxQuantity,

            'options' => $options->all(),

            'mirrored_options' => $options
                ->where('is_mirrored', true)
                ->values()
                ->all(),

            'has_mirrored_options' => $options
                ->contains('is_mirrored', true),

            'packagings' => $packagings->all(),

            'can_submit' => (
                $product->isInStock()
                && (
                    $packagings->isEmpty()
                    || $selectedPackagingId !== null
                )
            ),
        ];
    }

    private function optionData(
        ProductOption $option,
        array $mirroredOptionIds
    ): array {
        $values = $option->values
            ->map(function ($value) {
                return [
                    'id' => (int) $value->id,
                    'label' => $value->label,

                    'price_type' => $value->price_type,
                    'price' => $this->normalizeOptionPrice(
                        $value->price,
                        $value->price_type
                    ),

                    'special_price_type' => $value->special_price_type,
                    'special_price' => $this->normalizeOptionPrice(
                        $value->special_price,
                        $value->special_price_type
                    ),
                ];
            })
            ->values();

        return [
            'id' => (int) $option->id,
            'name' => $option->name,
            'type' => $option->type,
            'control' => $this->optionControl($option->type),
            'input_type' => $this->inputType($option->type),
            'is_required' => (bool) $option->is_required,

            'is_mirrored' => in_array(
                (int) $option->option_id,
                $mirroredOptionIds,
                true
            ),

            'values' => $values->all(),

            'pricing' => $values->first() ?? [
                    'price_type' => null,
                    'price' => 0,
                    'special_price_type' => null,
                    'special_price' => 0,
                ],
        ];
    }

    private function packagingData(
        Product $product,
        ProductPackaging $packaging
    ): array {
        $regularPrice = (
            (float) $packaging->price
            * max(1, (int) $packaging->qty)
        );

        $finalPrice = $this->packagingFinalPrice($packaging);

        $available = (
            !$product->manage_stock
            || (int) $product->qty >= (int) $packaging->qty
        );

        $maxQuantity = $product->manage_stock
            ? intdiv(
                max(0, (int) $product->qty),
                max(1, (int) $packaging->qty)
            )
            : 999;

        return [
            'id' => (int) $packaging->id,
            'name' => sprintf(
                $packaging->name,
                $packaging->qty
            ),
            'quantity' => (int) $packaging->qty,

            'regular_price' => $this->toCurrentCurrency($regularPrice),
            'final_price' => $this->toCurrentCurrency($finalPrice),

            'formatted_regular_price' => $this->formatPrice($regularPrice),
            'formatted_final_price' => $this->formatPrice($finalPrice),

            'has_special_price' => (
                $finalPrice < $regularPrice
            ),

            'available' => $available,
            'max_quantity' => $maxQuantity,
            'selected' => false,
        ];
    }

    private function packagingFinalPrice(
        ProductPackaging $packaging
    ): float {
        $regularUnitPrice = (float) $packaging->price;
        $specialPrice = (float) $packaging->special_price;

        if ($specialPrice <= 0) {
            return (
                $regularUnitPrice
                * max(1, (int) $packaging->qty)
            );
        }

        if ($packaging->special_price_type === 'percent') {
            return (
                $regularUnitPrice
                * (1 - ($specialPrice / 100))
                * max(1, (int) $packaging->qty)
            );
        }

        return (
            $specialPrice
            * max(1, (int) $packaging->qty)
        );
    }

    private function normalizeOptionPrice(
        $price,
        ?string $priceType
    ): float {
        $price = (float) $price;

        if ($priceType === 'fixed') {
            return $this->toCurrentCurrency($price);
        }

        return $price;
    }

    private function toCurrentCurrency(float $amount): float
    {
        return Money::inDefaultCurrency($amount)
            ->convertToCurrentCurrency()
            ->amount();
    }

    private function formatPrice(float $amount): string
    {
        return Money::inDefaultCurrency($amount)
            ->convertToCurrentCurrency()
            ->format();
    }

    private function optionControl(string $type): string
    {
        return match ($type) {
            'dropdown' => 'select',

            'radio',
            'radio_custom' => 'radio',

            'checkbox',
            'checkbox_custom',
            'multiple_select' => 'checkbox',

            'textarea' => 'textarea',

            default => 'input',
        };
    }

    private function inputType(string $type): string
    {
        return match ($type) {
            'date' => 'date',
            'date_time' => 'datetime-local',
            'time' => 'time',
            default => 'text',
        };
    }
}
