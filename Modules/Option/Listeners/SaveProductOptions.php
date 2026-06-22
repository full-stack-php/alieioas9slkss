<?php

namespace Modules\Option\Listeners;

class SaveProductOptions
{
    public function handle($product)
    {
        $this->deleteOldOptions($product);
        $this->saveOptions($product);
    }

    private function deleteOldOptions($product)
    {
        $incomingIds = array_filter(array_column($this->options(), 'id'));
        $product->options()->whereNotIn('id', $incomingIds)->get()->each->delete();
    }

    private function options()
    {
        $options = request('options', []);

        return collect($options)
            ->filter(function ($option) {
                return isset($option['type']) && !is_null($option['type']);
            })
            ->unique(function ($option) {
                return $option['option_id'] ?? $option['id'];
            })
            ->toArray();
    }

    private function saveOptions($product)
    {
        $counter = 0;

        foreach (array_reset_index($this->options()) as $attributes) {
            $optionId = $attributes['option_id'] ?? $attributes['id'];

            $productOption = $product->options()->updateOrCreate(
                [
                    'id' => $attributes['id'] ?? null,
                ],
                [
                    'option_id' => $optionId,
                    'is_required' => $attributes['is_required'] ?? false,
                    'position' => ++$counter,
                ]
            );

            $productOption->saveValues($attributes['values'] ?? []);
        }
    }
}
