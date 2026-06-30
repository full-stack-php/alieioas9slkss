<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;
use Modules\Option\Entities\ProductOption;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOneCMapping extends Model
{
    use SoftDeletes;

    protected $table = 'product_one_c_mappings';

    protected $fillable = [
        'product_id',
        'product_packaging_id',
        'product_options',
        'external_id',
        'one_c_id',
    ];

    protected $casts = [
        'product_options' => 'array',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'target_label',
        'options_label',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProductOneCMapping $mapping) {
            $mapping->one_c_id = static::makeOneCId(
                (int) $mapping->product_id,
                (string) $mapping->external_id
            );
        });
    }

    public static function makeOneCId(int $productId, string $externalId): string
    {
        $baseOneCId = Product::withoutGlobalScope('active')
            ->whereKey($productId)
            ->value('1c_id');

        $baseOneCId = trim((string) $baseOneCId);
        $externalId = trim($externalId);

        if ($baseOneCId !== '' && $baseOneCId !== '0') {
            return "{$baseOneCId}#{$externalId}";
        }

        return $externalId;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)
            ->withoutGlobalScope('active')
            ->withDefault();
    }

    public function packaging(): BelongsTo
    {
        return $this->belongsTo(ProductPackaging::class, 'product_packaging_id')
            ->withDefault();
    }

    public function getTargetLabelAttribute(): string
    {
        $parts = [];

        if ($this->product_packaging_id) {
            $parts[] = 'Упаковка: ' . ($this->packaging->name ?: '#' . $this->product_packaging_id);
        }

        if ($this->options_label) {
            $parts[] = 'Опции: ' . $this->options_label;
        }

        return implode(' / ', $parts) ?: '—';
    }

    public function getOptionsLabelAttribute(): string
    {
        $options = $this->product_options ?: [];

        if (empty($options)) {
            return '';
        }

        $productOptions = ProductOption::with(['option', 'values.optionValue'])
            ->whereIn('id', array_keys($options))
            ->get()
            ->keyBy('id');

        $labels = [];

        foreach ($options as $productOptionId => $productOptionValueId) {
            $productOption = $productOptions->get((int) $productOptionId);

            if (!$productOption) {
                continue;
            }

            $value = $productOption->values
                ->firstWhere('id', (int) $productOptionValueId);

            $optionName = $productOption->name ?: '#' . $productOptionId;
            $valueName = $value ? $value->label : '#' . $productOptionValueId;

            $labels[] = "{$optionName}: {$valueName}";
        }

        return implode(', ', $labels);
    }
}
